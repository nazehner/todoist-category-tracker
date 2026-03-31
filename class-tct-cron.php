<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TCT_Cron {

    const CRON_HOOK = 'tct_cron_sync';
    const CRON_HOOK_AUTO_MISS = 'tct_cron_auto_miss';

    const OPTION_USER_OFFSET = 'tct_cron_user_offset';
    const OPTION_AUTO_MISS_LAST_BOUNDARY = 'tct_auto_miss_last_boundary_utc';

    // Auto-miss processing state (for batching / retries).
    const OPTION_AUTO_MISS_PROCESSING_BOUNDARY = 'tct_auto_miss_processing_boundary_utc';
    const OPTION_AUTO_MISS_GOAL_CURSOR         = 'tct_auto_miss_goal_cursor';
    const OPTION_COMPOSITE_LAST_PERIODIC_RECONCILE = 'tct_composite_last_periodic_reconcile_utc';

    // Hard cap to keep the cron job fast; it will resume next run.
    const AUTO_MISS_BATCH_SIZE = 200;

    public function __construct() {
        add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );

        add_action( self::CRON_HOOK, array( $this, 'run' ) );
        add_action( self::CRON_HOOK_AUTO_MISS, array( $this, 'run_auto_miss' ) );

        add_action(
            'update_option_' . TCT_Admin::OPTION_NAME_SYNC_INTERVAL,
            array( __CLASS__, 'reschedule_on_option_change' ),
            10,
            2
        );

        // When the end-of-day time changes, re-evaluate the auto-miss schedule.
        add_action(
            'update_option_' . TCT_Admin::OPTION_NAME_END_OF_DAY_TIME,
            array( __CLASS__, 'reschedule_auto_miss_on_option_change' ),
            10,
            2
        );

        if ( class_exists( 'TCT_Reward' ) ) {
            add_action( TCT_Reward::CRON_HOOK_PROGRESS_GENERATION, array( 'TCT_Reward_Progress_Generator', 'process_queue' ) );
        }

        if ( class_exists( 'TCT_Economy_Normalizer' ) ) {
            add_action( TCT_Economy_Normalizer::CRON_HOOK_NIGHTLY, array( 'TCT_Economy_Normalizer', 'run_nightly_snapshot' ) );
        }

        // Ensure the auto-miss cron is scheduled after plugin updates (activate hook won't re-run).
        self::schedule_auto_miss();
    }

    public function add_cron_schedules( $schedules ) {
        $minutes = TCT_Admin::get_sync_interval_minutes();
        $key     = self::schedule_key_for_minutes( $minutes );

        $schedules[ $key ] = array(
            'interval' => $minutes * 60,
            'display'  => 'Todoist Category Tracker every ' . $minutes . ' minutes',
        );

        // Always provide a 5-minute schedule for auto-miss (even if sync interval is different).
        $key5 = self::schedule_key_for_minutes( 5 );
        if ( ! isset( $schedules[ $key5 ] ) ) {
            $schedules[ $key5 ] = array(
                'interval' => 5 * 60,
                'display'  => 'Todoist Category Tracker every 5 minutes',
            );
        }

        if ( class_exists( 'TCT_Reward' ) && is_callable( array( 'TCT_Reward', 'add_cron_schedule' ) ) ) {
            $schedules = TCT_Reward::add_cron_schedule( $schedules );
        }

        return $schedules;
    }

    public static function schedule_key_for_minutes( $minutes ) {
        $minutes = (int) $minutes;
        if ( ! in_array( $minutes, array( 5, 15, 30 ), true ) ) {
            $minutes = 15;
        }
        return 'tct_every_' . $minutes . '_minutes';
    }

    public static function schedule() {
        $minutes  = TCT_Admin::get_sync_interval_minutes();
        $schedule = self::schedule_key_for_minutes( $minutes );

        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time() + 60, $schedule, self::CRON_HOOK );
        }

        if ( class_exists( 'TCT_Economy_Normalizer' ) ) {
            TCT_Economy_Normalizer::schedule_nightly();
        }

        self::schedule_auto_miss();
    }

    public static function unschedule() {
        $timestamp = wp_next_scheduled( self::CRON_HOOK );
        while ( $timestamp ) {
            wp_unschedule_event( $timestamp, self::CRON_HOOK );
            $timestamp = wp_next_scheduled( self::CRON_HOOK );
        }

        if ( class_exists( 'TCT_Economy_Normalizer' ) ) {
            TCT_Economy_Normalizer::unschedule_nightly();
        }

        self::unschedule_auto_miss();
    }

    public static function schedule_auto_miss() {
        if ( ! class_exists( 'TCT_Admin' ) || ! is_callable( array( 'TCT_Admin', 'get_end_of_day_time' ) ) ) {
            return;
        }

        $eod = TCT_Admin::get_end_of_day_time();
        if ( '' === $eod ) {
            return;
        }

        $schedule = self::schedule_key_for_minutes( 5 );
        if ( ! wp_next_scheduled( self::CRON_HOOK_AUTO_MISS ) ) {
            // Slight delay so activation/update doesn't immediately run it.
            wp_schedule_event( time() + 120, $schedule, self::CRON_HOOK_AUTO_MISS );
        }
    }

    public static function unschedule_auto_miss() {
        $timestamp = wp_next_scheduled( self::CRON_HOOK_AUTO_MISS );
        while ( $timestamp ) {
            wp_unschedule_event( $timestamp, self::CRON_HOOK_AUTO_MISS );
            $timestamp = wp_next_scheduled( self::CRON_HOOK_AUTO_MISS );
        }
    }

    public static function reschedule_on_option_change( $old_value, $new_value ) {
        self::unschedule();
        self::schedule();
    }

    public static function reschedule_auto_miss_on_option_change( $old_value, $new_value ) {
        self::unschedule_auto_miss();
        // Clear the last-boundary marker so the next run re-evaluates from the new setting.
        delete_option( self::OPTION_AUTO_MISS_LAST_BOUNDARY );
        self::schedule_auto_miss();
    }

    public function run() {
        $users = get_users(
            array(
                'meta_key'     => TCT_OAuth::META_TOKEN,
                'meta_compare' => 'EXISTS',
                'fields'       => 'ID',
                'number'       => 0,
            )
        );

        $batch_size = 10;
        $offset = (int) get_option( self::OPTION_USER_OFFSET, 0 );
        $processed_sync_users = 0;
        $enabled_sync_users = is_array( $users ) ? count( $users ) : 0;

        if ( empty( $users ) ) {
            update_option( self::OPTION_USER_OFFSET, 0, false );
        } else {
            sort( $users );
            $slice = array_slice( $users, $offset, $batch_size );

            if ( empty( $slice ) ) {
                $offset = 0;
                $slice  = array_slice( $users, $offset, $batch_size );
            }

            foreach ( $slice as $user_id ) {
                TCT_Sync::sync_user( (int) $user_id, false );
            }

            $processed_sync_users = is_array( $slice ) ? count( $slice ) : 0;

            $new_offset = $offset + $batch_size;
            if ( $new_offset >= count( $users ) ) {
                $new_offset = 0;
            }

            update_option( self::OPTION_USER_OFFSET, $new_offset, false );
        }

        self::maybe_reconcile_composite_periodic(
            array(
                'sync_enabled_user_count' => (int) $enabled_sync_users,
                'sync_users_in_batch'     => (int) $processed_sync_users,
                'sync_offset'             => (int) $offset,
            )
        );
    }


    /**
     * Compute the cutoff boundary for a given local day start (00:00:00 in plugin/site timezone).
     *
     * Handles DST gaps (spring forward) and ambiguous times (fall back):
     * - Gap: if the requested wall-clock time does not exist, PHP typically normalizes it forward; we keep that.
     * - Ambiguous: prefer the later occurrence (after the fallback transition) to avoid prematurely cutting off the day.
     */
    private static function compute_cutoff_boundary_tz( DateTimeImmutable $day_start_tz, $hour, $minute ) {
        $hour   = (int) $hour;
        $minute = (int) $minute;

        if ( $hour < 0 ) {
            $hour = 0;
        } elseif ( $hour > 23 ) {
            $hour = 23;
        }

        if ( $minute < 0 ) {
            $minute = 0;
        } elseif ( $minute > 59 ) {
            $minute = 59;
        }

        $tz = $day_start_tz->getTimezone();

        // Base candidate.
        $candidate       = $day_start_tz->setTime( $hour, $minute, 0 );
        $desired_seconds = ( $hour * 3600 ) + ( $minute * 60 );

        $day_start_ts = (int) $day_start_tz->getTimestamp();
        $day_end_ts   = (int) $day_start_tz->add( new DateInterval( 'P1D' ) )->getTimestamp();

        if ( $day_end_ts <= $day_start_ts ) {
            return $candidate;
        }

        $transitions = array();
        try {
            $transitions = $tz->getTransitions( $day_start_ts, $day_end_ts );
        } catch ( Exception $e ) {
            $transitions = array();
        }

        if ( ! is_array( $transitions ) || count( $transitions ) < 2 ) {
            return $candidate;
        }

        $adjusted = false;

        for ( $i = 1; $i < count( $transitions ); $i++ ) {
            $old_offset = isset( $transitions[ $i - 1 ]['offset'] ) ? (int) $transitions[ $i - 1 ]['offset'] : 0;
            $new_offset = isset( $transitions[ $i ]['offset'] ) ? (int) $transitions[ $i ]['offset'] : 0;
            $delta      = $new_offset - $old_offset;

            if ( 0 == $delta ) {
                continue;
            }

            $ts = isset( $transitions[ $i ]['ts'] ) ? (int) $transitions[ $i ]['ts'] : 0;
            if ( $ts <= 0 ) {
                continue;
            }

            $after_local = ( new DateTimeImmutable( '@' . $ts ) )->setTimezone( $tz );
            $after_sec   = ( (int) $after_local->format( 'H' ) * 3600 ) + ( (int) $after_local->format( 'i' ) * 60 ) + ( (int) $after_local->format( 's' ) );

            if ( $delta > 0 ) {
                // Spring-forward: local times in [after - delta, after) do not exist.
                $gap_start = $after_sec - $delta;
                $gap_end   = $after_sec;

                if ( $desired_seconds >= $gap_start && $desired_seconds < $gap_end ) {
                    // If PHP produced a boundary before the transition, push it forward into the valid range.
                    if ( $candidate->getTimestamp() < $ts ) {
                        $candidate = $candidate->modify( '+' . (int) $delta . ' seconds' );
                        $adjusted  = true;
                    }
                }
            } else {
                // Fall-back: local times in [after, after + (-delta)) are ambiguous (occur twice).
                $amb_start = $after_sec;
                $amb_end   = $after_sec + ( -1 * $delta );

                if ( $desired_seconds >= $amb_start && $desired_seconds < $amb_end ) {
                    // If PHP picked the *earlier* occurrence (before the transition), prefer the later one.
                    if ( $candidate->getTimestamp() < $ts ) {
                        $candidate = $candidate->modify( '+' . ( -1 * (int) $delta ) . ' seconds' );
                        $adjusted  = true;
                    }
                }
            }
        }

        if ( $adjusted && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
            TCT_Utils::debug_log(
                'auto_miss_cutoff_dst_adjusted',
                array(
                    'day_start_local' => $day_start_tz->format( 'Y-m-d H:i:s' ),
                    'requested_time'  => sprintf( '%02d:%02d', $hour, $minute ),
                    'boundary_local'  => $candidate->format( 'Y-m-d H:i:s' ),
                    'tz'              => $tz->getName(),
                ),
                'info'
            );
        }

        return $candidate;
    }

    private static function reconcile_previous_full_day_anki_cards_goals( DateTimeImmutable $boundary_tz, DateTimeZone $tz ) {
        if ( ! class_exists( 'TCT_DB' ) || ! class_exists( 'TCT_Ledger' ) || ! is_callable( array( 'TCT_Ledger', 'reconcile_user' ) ) ) {
            return;
        }
        global $wpdb;
        $goals_table = TCT_DB::table_goals();
        $day_start_tz = $boundary_tz->sub( new DateInterval( 'P1D' ) )->setTime( 0, 0, 0 );
        $day_end_tz = $day_start_tz->add( new DateInterval( 'P1D' ) );
        $start_utc_mysql = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) ? TCT_Utils::dt_to_mysql_utc( $day_start_tz ) : $day_start_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
        $end_utc_mysql = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) ? TCT_Utils::dt_to_mysql_utc( $day_end_tz ) : $day_end_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
        if ( '' === $start_utc_mysql || '' === $end_utc_mysql ) {
            return;
        }
        $user_ids = $wpdb->get_col( "SELECT DISTINCT user_id FROM {$goals_table} WHERE is_tracked = 1 AND goal_type = 'anki_cards' AND target > 0 AND period_unit = 'day' AND period_span = 1 AND points_enabled_at IS NOT NULL AND points_enabled_at <> '' AND points_enabled_at <> '0000-00-00 00:00:00'" );
        if ( ! is_array( $user_ids ) || empty( $user_ids ) ) {
            return;
        }
        foreach ( $user_ids as $user_id_raw ) {
            $user_id = (int) $user_id_raw;
            if ( $user_id <= 0 ) {
                continue;
            }
            TCT_Ledger::reconcile_user( $user_id, $start_utc_mysql, $end_utc_mysql );
        }
    }

    /**
     * End-of-day auto-miss job.
     *
     * For eligible goals (positive + exactly 1/day):
     *  - If the goal has no completion/fail logged between [day start, end-of-day cutoff],
     *    record an auto-miss at the cutoff timestamp.
     *  - Uses Allowed Fails rules to decide whether the miss is 0 points vs normal penalty.
     *  - Idempotent via deterministic (source, source_ref) and ledger event_key.
     */
    public function run_auto_miss() {
        if ( ! class_exists( 'TCT_Admin' ) || ! is_callable( array( 'TCT_Admin', 'get_end_of_day_time' ) ) ) {
            return;
        }

        $end_of_day = TCT_Admin::get_end_of_day_time();
        if ( '' === $end_of_day ) {
            return;
        }

        // Validate HH:MM.
        if ( ! preg_match( '/^\d{2}:\d{2}$/', $end_of_day ) ) {
            return;
        }

        $parts = explode( ':', $end_of_day, 2 );
        $hh    = isset( $parts[0] ) ? (int) $parts[0] : 0;
        $mm    = isset( $parts[1] ) ? (int) $parts[1] : 0;

        if ( $hh < 0 || $hh > 23 || $mm < 0 || $mm > 59 ) {
            return;
        }

        // Use the plugin timezone override if present; otherwise fall back to WP timezone.
        $tz = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ) ? TCT_Utils::wp_timezone() : wp_timezone();

        try {
            $now_tz = new DateTimeImmutable( 'now', $tz );
        } catch ( Exception $e ) {
            return;
        }

        // Determine the most recent cutoff boundary (in plugin/site timezone).
        // Note: some timezones have DST transitions where a wall-clock time may be ambiguous (fall back)
        // or non-existent (spring forward). We normalize to a stable timestamp for the day.
        $today_start_tz     = $now_tz->setTime( 0, 0, 0 );
        $today_boundary     = self::compute_cutoff_boundary_tz( $today_start_tz, $hh, $mm );
        $yesterday_start_tz = $today_start_tz->sub( new DateInterval( 'P1D' ) );
        $yesterday_boundary = self::compute_cutoff_boundary_tz( $yesterday_start_tz, $hh, $mm );

        $boundary_tz = ( $now_tz->getTimestamp() >= $today_boundary->getTimestamp() )
            ? $today_boundary
            : $yesterday_boundary;

        $boundary_utc_mysql = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) )
            ? TCT_Utils::dt_to_mysql_utc( $boundary_tz )
            : $boundary_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

        if ( ! is_string( $boundary_utc_mysql ) || '' === $boundary_utc_mysql ) {
            return;
        }

        self::maybe_run_composite_cron_scaffold(
            'auto_miss',
            array(
                'boundary_utc' => $boundary_utc_mysql,
                'boundary_local' => $boundary_tz->format( 'Y-m-d H:i:s' ),
            )
        );

        $last_boundary = get_option( self::OPTION_AUTO_MISS_LAST_BOUNDARY, '' );
        if ( ! is_string( $last_boundary ) ) {
            $last_boundary = '';
        }

        // Initialize / resume batching state. If a previous boundary is still in-flight, finish it first
        // before moving on to a newer cutoff.
        $processing_boundary = get_option( self::OPTION_AUTO_MISS_PROCESSING_BOUNDARY, '' );
        if ( ! is_string( $processing_boundary ) ) {
            $processing_boundary = '';
        }

        $cursor = (int) get_option( self::OPTION_AUTO_MISS_GOAL_CURSOR, 0 );
        if ( $cursor < 0 ) {
            $cursor = 0;
        }

        $desired_boundary_utc_mysql = $boundary_utc_mysql;
        $desired_boundary_tz        = $boundary_tz;

        if ( '' !== $processing_boundary && $processing_boundary !== $last_boundary ) {
            // Resume the in-flight boundary.
            $boundary_utc_mysql = $processing_boundary;
            try {
                $boundary_utc = new DateTimeImmutable( $processing_boundary, new DateTimeZone( 'UTC' ) );
                $boundary_tz  = $boundary_utc->setTimezone( $tz );
            } catch ( Exception $e ) {
                // If the stored boundary is invalid, reset processing state to the desired boundary.
                $processing_boundary = $desired_boundary_utc_mysql;
                $cursor              = 0;
                $boundary_utc_mysql  = $desired_boundary_utc_mysql;
                $boundary_tz         = $desired_boundary_tz;

                update_option( self::OPTION_AUTO_MISS_PROCESSING_BOUNDARY, $processing_boundary, false );
                update_option( self::OPTION_AUTO_MISS_GOAL_CURSOR, $cursor, false );
            }
        } else {
            // No in-flight boundary. If we've already processed the desired cutoff, no-op.
            if ( $last_boundary === $desired_boundary_utc_mysql ) {
                return;
            }

            $processing_boundary = $desired_boundary_utc_mysql;
            $cursor              = 0;

            update_option( self::OPTION_AUTO_MISS_PROCESSING_BOUNDARY, $processing_boundary, false );
            update_option( self::OPTION_AUTO_MISS_GOAL_CURSOR, $cursor, false );
        }

        // The "day" that just ended is the calendar date immediately before the boundary.
        $miss_tz = $boundary_tz->sub( new DateInterval( 'PT1S' ) );
        $day_ymd = $miss_tz->format( 'Y-m-d' );

        try {
            $day_start_tz = new DateTimeImmutable( $day_ymd . ' 00:00:00', $tz );
        } catch ( Exception $e ) {
            return;
        }

        $start_utc_mysql = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) )
            ? TCT_Utils::dt_to_mysql_utc( $day_start_tz )
            : $day_start_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

        if ( ! is_string( $start_utc_mysql ) || '' === $start_utc_mysql ) {
            return;
        }

        $miss_utc_mysql = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) )
            ? TCT_Utils::dt_to_mysql_utc( $miss_tz )
            : $miss_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

        if ( ! is_string( $miss_utc_mysql ) || '' === $miss_utc_mysql ) {
            return;
        }

        if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) {
            return;
        }

        global $wpdb;

        $goals_table       = TCT_DB::table_goals();
        $completions_table = TCT_DB::table_completions();
        $ledger_table      = TCT_DB::table_ledger();

        $batch_size = (int) self::AUTO_MISS_BATCH_SIZE;
        if ( $batch_size < 10 ) {
            $batch_size = 10;
        }

        // Batch candidate set: tracked daily goals with target=1 and span=1. (Still verify eligibility server-side.)
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                    id,
                    user_id,
                    goal_name,
                    label_name,
                    tracking_mode,
                    is_tracked,
                    target,
                    period_unit,
                    period_span,
                    period_mode,
                    points_per_completion,
                    plant_name,
                    goal_type,
                    threshold,
                    allowed_fails_target,
                    allowed_fails_unit,
                    allowed_fails_span,
                    availability_cycle_json,
                    created_at,
                    updated_at
                 FROM {$goals_table}
                 WHERE is_tracked = 1
                   AND target = 1
                   AND period_unit = 'day'
                   AND period_span = 1
                   AND id > %d
                 ORDER BY id ASC
                 LIMIT %d",
                $cursor,
                $batch_size
            ),
            ARRAY_A
        );

        // When no more rows remain, we have fully processed this boundary.
        if ( empty( $rows ) || ! is_array( $rows ) ) {
            self::reconcile_previous_full_day_anki_cards_goals( $boundary_tz, $tz );
            self::maybe_reconcile_composite_boundary(
                $start_utc_mysql,
                $boundary_utc_mysql,
                array(
                    'day' => $day_ymd,
                    'boundary_local' => $boundary_tz->format( 'Y-m-d H:i:s' ),
                )
            );
            update_option( self::OPTION_AUTO_MISS_LAST_BOUNDARY, $boundary_utc_mysql, false );
            delete_option( self::OPTION_AUTO_MISS_PROCESSING_BOUNDARY );
            delete_option( self::OPTION_AUTO_MISS_GOAL_CURSOR );

            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
                TCT_Utils::debug_log(
                    'auto_miss_run',
                    array(
                        'day'                     => $day_ymd,
                        'boundary_utc'             => $boundary_utc_mysql,
                        'processing_boundary_utc'  => $processing_boundary,
                        'cursor_before'            => $cursor,
                        'cursor_after'             => 0,
                        'batch_size'               => $batch_size,
                        'goals_fetched'            => 0,
                        'goals_checked'            => 0,
                        'misses_created'           => 0,
                        'had_error'                => false,
                        'done'                     => true,
                    ),
                    'info'
                );
            }

            return;
        }

        $had_error      = false;
        $misses_created = 0;
        $goals_checked  = 0;

        $max_id_in_batch     = $cursor;
        $last_processed_id   = $cursor;

        // Group by user_id to reduce repeated queries.
        $by_user = array();
        foreach ( $rows as $goal ) {
            if ( ! is_array( $goal ) ) {
                continue;
            }

            $goal_id = isset( $goal['id'] ) ? (int) $goal['id'] : 0;
            $user_id = isset( $goal['user_id'] ) ? (int) $goal['user_id'] : 0;

            if ( $goal_id > $max_id_in_batch ) {
                $max_id_in_batch = $goal_id;
            }

            if ( $goal_id <= 0 || $user_id <= 0 ) {
                continue;
            }

            if ( ! isset( $by_user[ $user_id ] ) ) {
                $by_user[ $user_id ] = array();
            }

            $by_user[ $user_id ][] = $goal;
        }

        $like_manual = '[manual fail]%';
        $like_auto   = '[auto miss]%';

        foreach ( $by_user as $user_id => $goals ) {
            $user_id = (int) $user_id;
            if ( $user_id <= 0 || empty( $goals ) ) {
                continue;
            }

            // Filter eligible goals for this user (server-side gate) and skip those created after boundary.
            $eligible = array();
            foreach ( $goals as $goal ) {
                if ( ! is_array( $goal ) ) {
                    continue;
                }

                $goal_id = isset( $goal['id'] ) ? (int) $goal['id'] : 0;
                if ( $goal_id <= 0 ) {
                    continue;
                }

                $goals_checked++;

                if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_allowed_fails' ) ) ) {
                    if ( ! TCT_Interval::is_goal_eligible_for_allowed_fails( $goal ) ) {
                        $last_processed_id = max( $last_processed_id, $goal_id );
                        continue;
                    }
                }

                $created_at = isset( $goal['created_at'] ) ? trim( (string) $goal['created_at'] ) : '';
                if ( '' !== $created_at && '0000-00-00 00:00:00' !== $created_at && $created_at > $boundary_utc_mysql ) {
                    $last_processed_id = max( $last_processed_id, $goal_id );
                    continue;
                }

                if ( class_exists( 'TCT_Interval' )
                    && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) )
                    && is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) )
                    && is_callable( array( 'TCT_Interval', 'availability_cycle_state_on_local_date' ) ) ) {
                    if ( TCT_Interval::is_goal_eligible_for_availability_cycle( $goal ) ) {
                        $availability_cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal );
                        if ( is_array( $availability_cfg ) && ! empty( $availability_cfg['enabled'] ) ) {
                            $availability_state = TCT_Interval::availability_cycle_state_on_local_date( $availability_cfg, $day_ymd, $tz );
                            if ( ! empty( $availability_state['enabled'] ) && ! empty( $availability_state['is_paused'] ) ) {
                                $last_processed_id = max( $last_processed_id, $goal_id );
                                continue;
                            }
                        }
                    }
                }

                $eligible[ $goal_id ] = $goal;
            }

            if ( empty( $eligible ) ) {
                continue;
            }

            $goal_ids = array_keys( $eligible );

            // Blockers: any completion/fail before cutoff EXCEPT the deterministic auto_miss record for this day.
            // This allows safe retries when an auto_miss completion exists but ledger insert previously failed.
            $placeholders = implode( ',', array_fill( 0, count( $goal_ids ), '%d' ) );
            $block_sql    = "SELECT goal_id, COUNT(*) AS cnt
                             FROM {$completions_table}
                             WHERE user_id = %d
                               AND goal_id IN ({$placeholders})
                               AND completed_at >= %s
                               AND completed_at <= %s
                               AND NOT (source = 'auto_miss' AND source_ref = CONCAT('auto_miss:', goal_id, ':', %s))
                             GROUP BY goal_id";

            $block_params = array_merge( array( $user_id ), $goal_ids, array( $start_utc_mysql, $boundary_utc_mysql, $day_ymd ) );
            $block_rows   = $wpdb->get_results( $wpdb->prepare( $block_sql, $block_params ), ARRAY_A );

            $blocked = array();
            if ( is_array( $block_rows ) ) {
                foreach ( $block_rows as $r ) {
                    $gid = isset( $r['goal_id'] ) ? (int) $r['goal_id'] : 0;
                    $cnt = isset( $r['cnt'] ) ? (int) $r['cnt'] : 0;
                    if ( $gid > 0 && $cnt > 0 ) {
                        $blocked[ $gid ] = true;
                    }
                }
            }

            // Allowed-fails grouping (by unit+span) so we can count fails for many goals in one query.
            $cfg_by_goal          = array();
            $fails_before_by_goal = array();
            $groups               = array();

            foreach ( $eligible as $gid => $goal ) {
                $gid = (int) $gid;
                if ( $gid <= 0 || isset( $blocked[ $gid ] ) ) {
                    continue;
                }

                $cfg_target = 0;
                $cfg_unit   = 'week';
                $cfg_span   = 1;

                if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_allowed_fails_config_from_row' ) ) ) {
                    $cfg = TCT_Interval::normalize_allowed_fails_config_from_row( $goal );
                    if ( is_array( $cfg ) ) {
                        $cfg_target = isset( $cfg['target'] ) ? (int) $cfg['target'] : 0;
                        $cfg_unit   = isset( $cfg['unit'] ) ? sanitize_key( (string) $cfg['unit'] ) : 'week';
                        $cfg_span   = isset( $cfg['span'] ) ? (int) $cfg['span'] : 1;
                    }
                }

                $cfg_target = max( 0, $cfg_target );
                $cfg_span   = max( 1, $cfg_span );

                $cfg_by_goal[ $gid ] = array(
                    'target' => $cfg_target,
                    'unit'   => $cfg_unit,
                    'span'   => $cfg_span,
                );

                if ( $cfg_target > 0 && in_array( $cfg_unit, array( 'week', 'month', 'year' ), true ) && class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_allowed_fails_bounds' ) ) ) {
                    $gkey = $cfg_unit . '|' . $cfg_span;
                    if ( ! isset( $groups[ $gkey ] ) ) {
                        $groups[ $gkey ] = array(
                            'unit'     => $cfg_unit,
                            'span'     => $cfg_span,
                            'goal_ids' => array(),
                        );
                    }
                    $groups[ $gkey ]['goal_ids'][] = $gid;
                }
            }

            foreach ( $groups as $group ) {
                $gids = isset( $group['goal_ids'] ) ? (array) $group['goal_ids'] : array();
                if ( empty( $gids ) ) {
                    continue;
                }

                $unit = isset( $group['unit'] ) ? (string) $group['unit'] : 'week';
                $span = isset( $group['span'] ) ? (int) $group['span'] : 1;
                $span = max( 1, $span );

                $bounds = TCT_Interval::current_allowed_fails_bounds( $miss_tz, $unit, $span );
                if ( ! is_array( $bounds ) || ! isset( $bounds['start'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) ) {
                    continue;
                }

                $wstart_utc = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) )
                    ? TCT_Utils::dt_to_mysql_utc( $bounds['start'] )
                    : $bounds['start']->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

                if ( ! is_string( $wstart_utc ) || '' === $wstart_utc ) {
                    continue;
                }

                // Count fails strictly BEFORE this miss timestamp to keep results stable across retries.
                $ph = implode( ',', array_fill( 0, count( $gids ), '%d' ) );

                $cnt_sql = "SELECT goal_id, COUNT(*) AS cnt
                            FROM {$ledger_table}
                            WHERE user_id = %d
                              AND goal_id IN ({$ph})
                              AND occurred_at >= %s
                              AND occurred_at < %s
                              AND (details LIKE %s OR details LIKE %s)
                            GROUP BY goal_id";

                $cnt_params = array_merge( array( $user_id ), $gids, array( $wstart_utc, $miss_utc_mysql, $like_manual, $like_auto ) );
                $cnt_rows   = $wpdb->get_results( $wpdb->prepare( $cnt_sql, $cnt_params ), ARRAY_A );

                if ( is_array( $cnt_rows ) ) {
                    foreach ( $cnt_rows as $r ) {
                        $gid = isset( $r['goal_id'] ) ? (int) $r['goal_id'] : 0;
                        $cnt = isset( $r['cnt'] ) ? (int) $r['cnt'] : 0;
                        if ( $gid > 0 ) {
                            $fails_before_by_goal[ $gid ] = $cnt;
                        }
                    }
                }
            }

            // Insert misses for goals that have no blocker completions.
            foreach ( $eligible as $goal_id => $goal ) {
                $goal_id = (int) $goal_id;
                if ( $goal_id <= 0 ) {
                    continue;
                }

                if ( isset( $blocked[ $goal_id ] ) ) {
                    $last_processed_id = max( $last_processed_id, $goal_id );
                    continue;
                }

                $source     = 'auto_miss';
                $source_ref = 'auto_miss:' . $goal_id . ':' . $day_ymd;
                $now_utc    = current_time( 'mysql', true );

                $insert_ok = $wpdb->insert(
                    $completions_table,
                    array(
                        'user_id'              => $user_id,
                        'goal_id'              => $goal_id,
                        'source'               => $source,
                        'source_ref'           => $source_ref,
                        'todoist_completed_id' => '',
                        'todoist_task_id'      => '',
                        'label_name'           => '',
                        'task_content'         => null,
                        'note'                 => 'auto miss ' . $day_ymd,
                        'completed_at'         => $miss_utc_mysql,
                        'created_at'           => $now_utc,
                    ),
                    array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
                );

                if ( false === $insert_ok ) {
                    $errno = isset( $wpdb->last_errno ) ? (int) $wpdb->last_errno : 0;

                    // Duplicate key -> already recorded; treat as success (we still upsert ledger below).
                    if ( 1062 !== $errno ) {
                        $had_error = true;

                        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
                            TCT_Utils::debug_log(
                                'auto_miss_completion_insert_failed',
                                array(
                                    'user_id'      => $user_id,
                                    'goal_id'      => $goal_id,
                                    'errno'        => $errno,
                                    'db_error'     => $wpdb->last_error,
                                    'source_ref'   => $source_ref,
                                    'boundary_utc' => $boundary_utc_mysql,
                                ),
                                'error'
                            );
                        }

                        // Stop here so the job can retry this goal next run.
                        break;
                    }
                } else {
                    $misses_created++;
                }

                // Determine penalty points (or 0 for free fails within allowance).
                $ppc    = isset( $goal['points_per_completion'] ) ? (int) $goal['points_per_completion'] : 0;
                $target = isset( $goal['target'] ) ? (int) $goal['target'] : 0;

                $base_penalty = 0;
                if ( $ppc > 0 && $target > 0 && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_penalty_points' ) ) ) {
                    $base_penalty = (int) TCT_Utils::compute_penalty_points( $ppc, $target, 0 );
                }

                $fail_points          = $base_penalty;
                $allowed_fails_active = false;

                $cfg = isset( $cfg_by_goal[ $goal_id ] ) ? (array) $cfg_by_goal[ $goal_id ] : array( 'target' => 0, 'unit' => 'week', 'span' => 1 );
                $cfg_target = isset( $cfg['target'] ) ? max( 0, (int) $cfg['target'] ) : 0;
                $cfg_unit   = isset( $cfg['unit'] ) ? (string) $cfg['unit'] : 'week';

                if ( $cfg_target > 0 && in_array( $cfg_unit, array( 'week', 'month', 'year' ), true ) ) {
                    $allowed_fails_active = true;
                    $fails_before = isset( $fails_before_by_goal[ $goal_id ] ) ? (int) $fails_before_by_goal[ $goal_id ] : 0;
                    if ( $fails_before < 0 ) {
                        $fails_before = 0;
                    }
                    if ( $fails_before < $cfg_target ) {
                        $fail_points = 0;
                    }
                }

                // Insert ledger entry (0-point entries are still recorded when Allowed Fails is active).
                if ( 0 !== (int) $fail_points || $allowed_fails_active ) {
                    $event_key  = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id );
                    $goal_name  = isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : '';
                    $label_name = isset( $goal['label_name'] ) ? (string) $goal['label_name'] : '';
                    $details    = '[auto miss] ' . $day_ymd;

                    $sql = "INSERT INTO {$ledger_table}
                                (user_id, event_key, event_type, points, occurred_at, goal_id, goal_name, label_name, todoist_completed_id, todoist_task_id, details, created_at, updated_at)
                            VALUES
                                (%d, %s, %s, %d, %s, %d, %s, %s, %s, %s, %s, %s, %s)
                            ON DUPLICATE KEY UPDATE
                                points = VALUES(points),
                                occurred_at = VALUES(occurred_at),
                                goal_id = VALUES(goal_id),
                                goal_name = VALUES(goal_name),
                                label_name = VALUES(label_name),
                                details = VALUES(details),
                                updated_at = VALUES(updated_at)";

                    $wpdb->query(
                        $wpdb->prepare(
                            $sql,
                            $user_id,
                            $event_key,
                            'completion',
                            (int) $fail_points,
                            $miss_utc_mysql,
                            $goal_id,
                            $goal_name,
                            $label_name,
                            '',
                            '',
                            $details,
                            $now_utc,
                            $now_utc
                        )
                    );

                    if ( '' !== $wpdb->last_error ) {
                        $had_error = true;
                        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
                            TCT_Utils::debug_log(
                                'auto_miss_ledger_insert_failed',
                                array(
                                    'user_id'      => $user_id,
                                    'goal_id'      => $goal_id,
                                    'event_key'    => $event_key,
                                    'db_error'     => $wpdb->last_error,
                                    'boundary_utc' => $boundary_utc_mysql,
                                ),
                                'error'
                            );
                        }

                        // Stop here so the job can retry this goal next run.
                        break;
                    }
                }

                $last_processed_id = max( $last_processed_id, $goal_id );
            }

            if ( $had_error ) {
                break;
            }
        }

        // Advance cursor: if no error, skip past the entire fetched batch; otherwise advance to the last
        // successfully processed goal id so the failing goal will be retried next run.
        $new_cursor = $had_error ? $last_processed_id : $max_id_in_batch;
        if ( $new_cursor < 0 ) {
            $new_cursor = 0;
        }
        update_option( self::OPTION_AUTO_MISS_GOAL_CURSOR, (int) $new_cursor, false );

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
            TCT_Utils::debug_log(
                'auto_miss_run',
                array(
                    'day'                     => $day_ymd,
                    'boundary_utc'             => $boundary_utc_mysql,
                    'processing_boundary_utc'  => $processing_boundary,
                    'cursor_before'            => $cursor,
                    'cursor_after'             => $new_cursor,
                    'batch_size'               => $batch_size,
                    'goals_fetched'            => is_array( $rows ) ? count( $rows ) : 0,
                    'goals_checked'            => $goals_checked,
                    'misses_created'           => $misses_created,
                    'had_error'                => $had_error,
                    'done'                     => false,
                ),
                $had_error ? 'error' : 'info'
            );
        }
    }


    private static function composite_goals_feature_enabled() {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'is_composite_goals_enabled' ) ) ) {
            return (bool) TCT_Plugin::is_composite_goals_enabled();
        }

        return false;
    }

    private static function composite_goal_type_slug() {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'composite_goal_type' ) ) ) {
            return (string) TCT_Plugin::composite_goal_type();
        }

        return 'composite_parent';
    }

    private static function is_valid_utc_mysql( $value ) {
        $value = is_string( $value ) ? trim( (string) $value ) : '';
        if ( '' === $value || '0000-00-00 00:00:00' === $value ) {
            return false;
        }

        return ( false !== strtotime( $value . ' UTC' ) );
    }

    private static function composite_periodic_bootstrap_days() {
        $days = 7;
        if ( class_exists( 'TCT_Admin' ) && is_callable( array( 'TCT_Admin', 'get_sync_horizon_days' ) ) ) {
            $days = (int) TCT_Admin::get_sync_horizon_days();
        }

        if ( $days < 7 ) {
            $days = 7;
        }
        if ( $days > 90 ) {
            $days = 90;
        }

        return (int) $days;
    }

    private static function composite_goal_user_ids() {
        if ( ! class_exists( 'TCT_DB' ) || ! is_callable( array( 'TCT_DB', 'table_goals' ) ) ) {
            return array();
        }

        global $wpdb;
        $goals_table = TCT_DB::table_goals();
        $goal_type = self::composite_goal_type_slug();
        $user_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT user_id FROM {$goals_table} WHERE is_tracked = 1 AND goal_type = %s ORDER BY user_id ASC",
                $goal_type
            )
        );

        if ( ! is_array( $user_ids ) ) {
            return array();
        }

        $out = array();
        foreach ( $user_ids as $user_id_raw ) {
            $user_id = (int) $user_id_raw;
            if ( $user_id > 0 ) {
                $out[] = $user_id;
            }
        }

        return $out;
    }

    private static function composite_periodic_reconcile_window() {
        $now_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
        $until_utc_mysql = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) )
            ? TCT_Utils::dt_to_mysql_utc( $now_utc )
            : $now_utc->format( 'Y-m-d H:i:s' );

        if ( ! self::is_valid_utc_mysql( $until_utc_mysql ) ) {
            return array();
        }

        $last_reconcile_utc_mysql = get_option( self::OPTION_COMPOSITE_LAST_PERIODIC_RECONCILE, '' );
        if ( ! is_string( $last_reconcile_utc_mysql ) ) {
            $last_reconcile_utc_mysql = '';
        }

        $bootstrap = false;
        if ( self::is_valid_utc_mysql( $last_reconcile_utc_mysql ) && strcmp( $last_reconcile_utc_mysql, $until_utc_mysql ) < 0 ) {
            $since_utc_mysql = $last_reconcile_utc_mysql;
        } else {
            $bootstrap = true;
            $days = self::composite_periodic_bootstrap_days();
            $since_dt = $now_utc->sub( new DateInterval( 'P' . (int) $days . 'D' ) );
            $since_utc_mysql = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) ) )
                ? TCT_Utils::dt_to_mysql_utc( $since_dt )
                : $since_dt->format( 'Y-m-d H:i:s' );
        }

        if ( ! self::is_valid_utc_mysql( $since_utc_mysql ) ) {
            return array();
        }

        if ( strcmp( $until_utc_mysql, $since_utc_mysql ) <= 0 ) {
            return array();
        }

        return array(
            'since_utc_mysql' => (string) $since_utc_mysql,
            'until_utc_mysql' => (string) $until_utc_mysql,
            'last_reconcile_utc_mysql' => (string) $last_reconcile_utc_mysql,
            'bootstrap' => $bootstrap ? 1 : 0,
        );
    }

    private static function reconcile_composite_users_in_range( $since_utc_mysql, $until_utc_mysql, $context, $args = array() ) {
        if ( ! self::composite_goals_feature_enabled() ) {
            return array();
        }
        if ( ! class_exists( 'TCT_Ledger' ) || ! is_callable( array( 'TCT_Ledger', 'reconcile_user' ) ) ) {
            return array();
        }

        $since_utc_mysql = is_string( $since_utc_mysql ) ? trim( (string) $since_utc_mysql ) : '';
        $until_utc_mysql = is_string( $until_utc_mysql ) ? trim( (string) $until_utc_mysql ) : '';
        if ( ! self::is_valid_utc_mysql( $since_utc_mysql ) || ! self::is_valid_utc_mysql( $until_utc_mysql ) ) {
            return array();
        }
        if ( strcmp( $until_utc_mysql, $since_utc_mysql ) <= 0 ) {
            return array();
        }

        $user_ids = self::composite_goal_user_ids();
        $processed_users = 0;
        foreach ( $user_ids as $user_id ) {
            $user_id = (int) $user_id;
            if ( $user_id <= 0 ) {
                continue;
            }
            TCT_Ledger::reconcile_user( $user_id, $since_utc_mysql, $until_utc_mysql );
            $processed_users++;
        }

        $payload = is_array( $args ) ? $args : array();
        $payload['context'] = sanitize_key( (string) $context );
        $payload['since_utc'] = (string) $since_utc_mysql;
        $payload['until_utc'] = (string) $until_utc_mysql;
        $payload['composite_user_count'] = count( $user_ids );
        $payload['processed_users'] = (int) $processed_users;

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
            TCT_Utils::debug_log( 'composite_goals_cron_reconcile', $payload );
        }

        return $payload;
    }

    private static function maybe_reconcile_composite_periodic( $args = array() ) {
        $window = self::composite_periodic_reconcile_window();
        if ( empty( $window ) ) {
            return;
        }

        $payload = is_array( $args ) ? $args : array();
        $payload['bootstrap'] = ! empty( $window['bootstrap'] ) ? 1 : 0;
        $payload['last_periodic_reconcile_utc'] = isset( $window['last_reconcile_utc_mysql'] ) ? (string) $window['last_reconcile_utc_mysql'] : '';

        self::reconcile_composite_users_in_range(
            isset( $window['since_utc_mysql'] ) ? $window['since_utc_mysql'] : '',
            isset( $window['until_utc_mysql'] ) ? $window['until_utc_mysql'] : '',
            'sync_periodic',
            $payload
        );

        if ( isset( $window['until_utc_mysql'] ) && self::is_valid_utc_mysql( $window['until_utc_mysql'] ) ) {
            update_option( self::OPTION_COMPOSITE_LAST_PERIODIC_RECONCILE, (string) $window['until_utc_mysql'], false );
        }
    }

    private static function maybe_reconcile_composite_boundary( $since_utc_mysql, $until_utc_mysql, $args = array() ) {
        self::reconcile_composite_users_in_range( $since_utc_mysql, $until_utc_mysql, 'auto_miss_boundary', $args );
    }

}
