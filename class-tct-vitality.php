<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } class TCT_Vitality { public static function compute( array $args ) { $target = isset( $args['target'] ) ? (int) $args['target'] : 0; $achieved = isset( $args['achieved'] ) ? (int) $args['achieved'] : 0; $loop_start_ts = isset( $args['loop_start_ts'] ) ? (int) $args['loop_start_ts'] : 0; $loop_end_ts = isset( $args['loop_end_ts'] ) ? (int) $args['loop_end_ts'] : 0; $now_ts = isset( $args['now_ts'] ) ? (int) $args['now_ts'] : 0; $importance = isset( $args['importance'] ) ? (int) $args['importance'] : 3; $difficulty = isset( $args['difficulty'] ) ? (int) $args['difficulty'] : 3; $carryover_ratio = isset( $args['carryover_ratio'] ) ? (float) $args['carryover_ratio'] : 1.0; $last_completed_ts = null; if ( isset( $args['last_completed_ts'] ) ) { $tmp = (int) $args['last_completed_ts']; if ( $tmp > 0 ) { $last_completed_ts = $tmp; } } if ( $target <= 0 ) { return array( 'vitality' => 100, 'components' => array( 'reason' => 'no_target', ), ); } if ( $achieved < 0 ) { $achieved = 0; } if ( $achieved >= $target ) { return array( 'vitality' => 100, 'components' => array( 'reason' => 'target_met', ), ); } $duration = $loop_end_ts - $loop_start_ts; if ( $duration <= 0 ) { return array( 'vitality' => 100, 'components' => array( 'reason' => 'invalid_window', ), ); } if ( $now_ts < $loop_start_ts ) { $now_ts = $loop_start_ts; } if ( $now_ts > $loop_end_ts ) { $now_ts = $loop_end_ts; } $elapsed_s = max( 0, $now_ts - $loop_start_ts ); $elapsed_f = self::clamp_01( (float) $elapsed_s / (float) $duration ); $expected_reps = $elapsed_f * (float) $target; $deficit_reps = max( 0.0, $expected_reps - (float) $achieved ); $spacing_seconds = (float) $duration / (float) max( 1, $target ); if ( $spacing_seconds <= 0 ) { $spacing_seconds = 1.0; } if ( null === $last_completed_ts || $last_completed_ts <= 0 ) { $last_completed_ts = $loop_start_ts; } $time_since_last_s = max( 0, $now_ts - (int) $last_completed_ts ); $hydration_cap = self::compute_hydration_cap( $time_since_last_s, $spacing_seconds, $importance, $difficulty ); $spacing_days = (float) $spacing_seconds / 86400.0; if ( $spacing_days <= 0.0001 ) { $spacing_days = 0.0001; } $pace_cap = self::compute_pace_cap( $deficit_reps, $spacing_days, $target ); $carry_cap = 1.0; if ( $achieved <= 0 ) { $carry_cap = self::clamp_01( $carryover_ratio ); } $cap = min( $hydration_cap, $pace_cap, $carry_cap ); $cap = self::clamp_01( $cap ); $vitality = (int) round( 100.0 * $cap ); if ( $vitality < 0 ) { $vitality = 0; } if ( $vitality > 100 ) { $vitality = 100; } return array( 'vitality' => $vitality, 'components' => array( 'elapsed_fraction' => $elapsed_f, 'expected_reps' => $expected_reps, 'deficit_reps' => $deficit_reps, 'spacing_days' => $spacing_days, 'time_since_last_s' => $time_since_last_s, 'hydration_cap' => $hydration_cap, 'pace_cap' => $pace_cap, 'carry_cap' => $carry_cap, 'importance' => $importance, 'difficulty' => $difficulty, ), ); } private static function clamp_01( $x ) { $x = (float) $x; if ( $x < 0.0 ) { return 0.0; } if ( $x > 1.0 ) { return 1.0; } return $x; } private static function clamp_range( $x, $min, $max ) { $x = (float) $x; $min = (float) $min; $max = (float) $max; if ( $x < $min ) { return $min; } if ( $x > $max ) { return $max; } return $x; } private static function normalize_scale_1_5( $v ) { $n = (int) $v; if ( $n < 1 || $n > 5 ) { return 3; } return $n; } private static function compute_hydration_cap( $time_since_last_s, $spacing_seconds, $importance, $difficulty ) { $time_since_last_s = (float) max( 0, (int) $time_since_last_s ); $spacing_seconds = (float) max( 1, (float) $spacing_seconds ); $t = $time_since_last_s / $spacing_seconds; $imp = self::normalize_scale_1_5( $importance ); $dif = self::normalize_scale_1_5( $difficulty ); $thirst = 1.0 + ( 0.04 * ( (float) $imp - 3.0 ) ) + ( 0.05 * ( (float) $dif - 3.0 ) ); $thirst = self::clamp_range( $thirst, 0.75, 1.25 ); $t_scaled = max( 0.0, $t * $thirst ); $half_at = 2.0; $power = 2.0; $ratio = $t_scaled / $half_at; if ( $ratio <= 0 ) { return 1.0; } $cap = 1.0 / ( 1.0 + pow( $ratio, $power ) ); return self::clamp_01( $cap ); } private static function compute_pace_cap( $deficit_reps, $spacing_days, $target ) { $deficit_reps = (float) max( 0.0, (float) $deficit_reps ); $spacing_days = (float) max( 0.0001, (float) $spacing_days ); $target = (int) $target; if ( $target < 1 ) { $target = 1; } $ln_spacing = log( $spacing_days ); $ln_target = log( (float) $target ); $K = 0.30963853623421783 + ( 0.09297314651423387 * $ln_spacing ) + ( 0.6910722661250665 * $ln_target ); $M = -2.2227078007213965 + ( 2.632268622119755 * $ln_spacing ) - ( 0.7726051659072066 * $ln_target ); $K = self::clamp_range( $K, 0.25, 5.0 ); $M = self::clamp_range( $M, 2.0, 12.0 ); if ( $K <= 0 ) { return 0.0; } $ratio = $deficit_reps / $K; if ( $ratio <= 0 ) { return 1.0; } $cap = 1.0 / ( 1.0 + pow( $ratio, $M ) ); return self::clamp_01( $cap ); } public static function debug_scenarios() { $duration = 30 * DAY_IN_SECONDS; $loop_start = 0; $loop_end = $duration; $now = 13 * DAY_IN_SECONDS; $s1 = self::compute( array( 'target' => 4, 'achieved' => 1, 'loop_start_ts' => $loop_start, 'loop_end_ts' => $loop_end, 'now_ts' => $now, 'last_completed_ts' => $now, 'importance' => 3, 'difficulty' => 3, 'carryover_ratio'=> 1.0, ) ); $duration2 = 90 * DAY_IN_SECONDS; $s2 = self::compute( array( 'target' => 1, 'achieved' => 0, 'loop_start_ts' => 0, 'loop_end_ts' => $duration2, 'now_ts' => 60 * DAY_IN_SECONDS, 'last_completed_ts' => 0, 'importance' => 3, 'difficulty' => 5, 'carryover_ratio'=> 1.0, ) ); return array( 'monthly_4_day13_one_done' => $s1, 'quarterly_day60_none' => $s2, ); } 
public static function compute_for_goal( $user_id, array $goal_row, DateTimeImmutable $now_tz ) {
    $user_id = (int) $user_id;
    $goal_id = isset( $goal_row['id'] ) ? (int) $goal_row['id'] : 0;
    if ( $user_id <= 0 || $goal_id <= 0 ) {
        return null;
    }

    $goal_row = self::hydrate_goal_row_for_availability( $user_id, $goal_id, $goal_row );

    if ( class_exists( 'TCT_Interval' )
        && is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) )
        && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) ) ) {
        $availability = TCT_Interval::normalize_availability_cycle_from_row( $goal_row );
        if ( ! empty( $availability['enabled'] ) && TCT_Interval::is_goal_eligible_for_availability_cycle( $goal_row ) ) {
            $payload = self::compute_for_goal_with_availability_cycle( $user_id, $goal_row, $now_tz, $availability );
            if ( is_array( $payload ) ) {
                return $payload;
            }
        }
    }

    return self::compute_for_goal_legacy( $user_id, $goal_row, $now_tz );
}

private static function hydrate_goal_row_for_availability( $user_id, $goal_id, array $goal_row ) {
    global $wpdb;

    $user_id = (int) $user_id;
    $goal_id = (int) $goal_id;
    if ( $user_id <= 0 || $goal_id <= 0 ) {
        return $goal_row;
    }

    $required_keys = array(
        'goal_type',
        'target',
        'period_unit',
        'period_span',
        'period_mode',
        'intervals_json',
        'availability_cycle_json',
        'interval_anchor_json',
        'created_at',
        'updated_at',
        'importance',
        'difficulty',
        'effort',
        'threshold',
        'sleep_tracking_enabled',
        'sleep_rollover_time',
    );

    $needs_hydration = false;
    foreach ( $required_keys as $required_key ) {
        if ( ! array_key_exists( $required_key, $goal_row ) ) {
            $needs_hydration = true;
            break;
        }
    }

    if ( ! $needs_hydration ) {
        return $goal_row;
    }

    static $cache = array();
    if ( isset( $cache[ $goal_id ] ) && is_array( $cache[ $goal_id ] ) ) {
        $db_row = $cache[ $goal_id ];
    } else {
        if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) {
            return $goal_row;
        }
        $goals_table = TCT_DB::table_goals();
        $db_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, goal_type, target, period_unit, period_span, period_mode, intervals_json, availability_cycle_json, interval_anchor_json, created_at, updated_at, importance, difficulty, effort, threshold, sleep_tracking_enabled, sleep_rollover_time FROM {$goals_table} WHERE user_id = %d AND id = %d LIMIT 1",
                $user_id,
                $goal_id
            ),
            ARRAY_A
        );
        if ( ! is_array( $db_row ) ) {
            return $goal_row;
        }
        $cache[ $goal_id ] = $db_row;
    }

    $merged = $db_row;
    foreach ( $goal_row as $key => $value ) {
        if ( in_array( $key, array( 'availability_cycle_json', 'interval_anchor_json', 'created_at', 'updated_at', 'intervals_json' ), true ) ) {
            if ( null === $value ) {
                continue;
            }
            if ( is_string( $value ) && '' === trim( $value ) ) {
                continue;
            }
        }
        $merged[ $key ] = $value;
    }

    return $merged;
}

private static function compute_for_goal_with_availability_cycle( $user_id, array $goal_row, DateTimeImmutable $now_tz, array $availability ) {
    $user_id = (int) $user_id;
    $goal_id = isset( $goal_row['id'] ) ? (int) $goal_row['id'] : 0;
    if ( $user_id <= 0 || $goal_id <= 0 ) {
        return null;
    }

    $target = isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0;
    if ( $target <= 0 ) {
        return null;
    }

    if ( ! class_exists( 'TCT_Interval' ) || ! is_callable( array( 'TCT_Interval', 'availability_cycle_current_loop_context' ) ) ) {
        return null;
    }

    $tz = $now_tz->getTimezone();
    $context = TCT_Interval::availability_cycle_current_loop_context( $goal_row, $now_tz );
    if ( ! is_array( $context ) || empty( $context['enabled'] ) || empty( $context['current'] ) || ! is_array( $context['current'] ) ) {
        return null;
    }

    $current_loop = $context['current'];
    $previous_loop = ( isset( $context['previous'] ) && is_array( $context['previous'] ) ) ? $context['previous'] : null;
    if ( empty( $current_loop['start'] ) || empty( $current_loop['end'] ) || ! ( $current_loop['start'] instanceof DateTimeImmutable ) || ! ( $current_loop['end'] instanceof DateTimeImmutable ) ) {
        return null;
    }

    $stats = self::availability_collect_completion_stats( $user_id, $goal_id, $goal_row, $availability, $current_loop, $previous_loop, $now_tz );
    $achieved = isset( $stats['current_achieved'] ) ? max( 0, (int) $stats['current_achieved'] ) : 0;

    $importance = isset( $goal_row['importance'] ) ? (int) $goal_row['importance'] : 3;
    $difficulty = 3;
    if ( isset( $goal_row['difficulty'] ) ) {
        $difficulty = (int) $goal_row['difficulty'];
    } elseif ( isset( $goal_row['effort'] ) ) {
        $difficulty = (int) $goal_row['effort'];
    }

    $created_ts = self::availability_parse_mysql_utc_ts( isset( $goal_row['created_at'] ) ? $goal_row['created_at'] : '' );
    $updated_ts = self::availability_parse_mysql_utc_ts( isset( $goal_row['updated_at'] ) ? $goal_row['updated_at'] : '' );

    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_prorate_anchor_ts' ) ) ) {
        $current_prorate_anchor_ts = (int) TCT_Utils::compute_prorate_anchor_ts( $created_ts, $updated_ts, isset( $current_loop['start_ts'] ) ? (int) $current_loop['start_ts'] : 0 );
    } else {
        $current_prorate_anchor_ts = 0;
        if ( $created_ts > 0 && $created_ts >= (int) $current_loop['start_ts'] ) {
            $current_prorate_anchor_ts = $created_ts;
        } elseif ( $created_ts <= 0 && $updated_ts > 0 && $updated_ts >= (int) $current_loop['start_ts'] ) {
            $current_prorate_anchor_ts = $updated_ts;
        }
    }

    $effective_target_v = self::availability_prorated_target( $target, $current_prorate_anchor_ts, $current_loop, $availability, $tz );
    if ( $effective_target_v <= 0 ) {
        $payload = array(
            'vitality' => 100,
            'target' => $target,
            'achieved' => $achieved,
            'loop_start_utc_mysql' => isset( $current_loop['start_utc_mysql'] ) ? (string) $current_loop['start_utc_mysql'] : '',
            'loop_end_utc_mysql' => isset( $current_loop['end_utc_mysql'] ) ? (string) $current_loop['end_utc_mysql'] : '',
            'time_remaining_seconds' => isset( $current_loop['active_remaining_seconds'] ) ? max( 0, (int) $current_loop['active_remaining_seconds'] ) : 0,
            'time_remaining_label' => self::format_time_remaining_label( isset( $current_loop['active_remaining_seconds'] ) ? max( 0, (int) $current_loop['active_remaining_seconds'] ) : 0 ),
            'vitality_components' => array(
                'reason' => 'availability_prorated_zero',
                'clock' => 'availability_active_time',
                'effective_target' => 0,
                'availability_phase' => isset( $context['state_now']['phase'] ) ? (string) $context['state_now']['phase'] : '',
                'availability_is_paused' => ! empty( $context['state_now']['is_paused'] ) ? 1 : 0,
            ),
        );
        return $payload;
    }

    $carryover_ratio = 1.0;
    $last_anchor_dt = $current_loop['start'];
    $current_last_effective_ts = isset( $stats['current_last_effective_ts'] ) ? (int) $stats['current_last_effective_ts'] : 0;
    if ( $current_last_effective_ts > 0 ) {
        $last_anchor_dt = self::availability_ts_to_local_dt( $current_last_effective_ts, $tz );
    } else {
        $apply_carryover = true;
        if ( $updated_ts > 0 && $updated_ts >= (int) $current_loop['start_ts'] ) {
            $apply_carryover = false;
            $updated_dt = self::availability_ts_to_local_dt( max( (int) $updated_ts, (int) $current_loop['start_ts'] ), $tz );
            if ( $updated_dt instanceof DateTimeImmutable ) {
                $last_anchor_dt = $updated_dt;
            }
        }

        if ( $apply_carryover && is_array( $previous_loop ) ) {
            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_prorate_anchor_ts' ) ) ) {
                $previous_prorate_anchor_ts = (int) TCT_Utils::compute_prorate_anchor_ts( $created_ts, $updated_ts, isset( $previous_loop['start_ts'] ) ? (int) $previous_loop['start_ts'] : 0 );
            } else {
                $previous_prorate_anchor_ts = 0;
                if ( $created_ts > 0 && $created_ts >= (int) $previous_loop['start_ts'] ) {
                    $previous_prorate_anchor_ts = $created_ts;
                } elseif ( $created_ts <= 0 && $updated_ts > 0 && $updated_ts >= (int) $previous_loop['start_ts'] ) {
                    $previous_prorate_anchor_ts = $updated_ts;
                }
            }

            $previous_effective_target_v = self::availability_prorated_target( $target, $previous_prorate_anchor_ts, $previous_loop, $availability, $tz );
            if ( $previous_effective_target_v > 0 ) {
                $carryover_ratio = self::clamp_01( (float) ( isset( $stats['previous_achieved'] ) ? (int) $stats['previous_achieved'] : 0 ) / (float) $previous_effective_target_v );
            }
        }
    }

    if ( ! ( $last_anchor_dt instanceof DateTimeImmutable ) ) {
        $last_anchor_dt = $current_loop['start'];
    }
    if ( $last_anchor_dt->getTimestamp() < (int) $current_loop['start_ts'] ) {
        $last_anchor_dt = $current_loop['start'];
    }
    if ( $last_anchor_dt->getTimestamp() > $now_tz->getTimestamp() ) {
        $last_anchor_dt = $now_tz;
    }

    $active_since_last_seconds = 0;
    if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'availability_cycle_active_seconds_in_range' ) ) ) {
        $active_since_last_seconds = (int) TCT_Interval::availability_cycle_active_seconds_in_range( $availability, $last_anchor_dt, $now_tz, $tz );
    } else {
        $active_since_last_seconds = max( 0, (int) ( $now_tz->getTimestamp() - $last_anchor_dt->getTimestamp() ) );
    }
    if ( $active_since_last_seconds < 0 ) {
        $active_since_last_seconds = 0;
    }
    if ( isset( $current_loop['nominal_active_seconds'] ) && $active_since_last_seconds > (int) $current_loop['nominal_active_seconds'] ) {
        $active_since_last_seconds = (int) $current_loop['nominal_active_seconds'];
    }

    $computed = self::compute_active_clock( array(
        'target' => $effective_target_v,
        'achieved' => $achieved,
        'nominal_active_seconds' => isset( $current_loop['nominal_active_seconds'] ) ? (int) $current_loop['nominal_active_seconds'] : max( 1, (int) ( $current_loop['end_ts'] - $current_loop['start_ts'] ) ),
        'active_elapsed_seconds' => isset( $current_loop['active_elapsed_seconds'] ) ? (int) $current_loop['active_elapsed_seconds'] : 0,
        'active_since_last_seconds' => $active_since_last_seconds,
        'importance' => $importance,
        'difficulty' => $difficulty,
        'carryover_ratio' => (float) $carryover_ratio,
    ) );

    $vitality = isset( $computed['vitality'] ) ? (int) $computed['vitality'] : 100;
    if ( $vitality < 0 ) {
        $vitality = 0;
    }
    if ( $vitality > 100 ) {
        $vitality = 100;
    }

    $time_remaining_seconds = isset( $current_loop['active_remaining_seconds'] ) ? max( 0, (int) $current_loop['active_remaining_seconds'] ) : 0;
    $payload = array(
        'vitality' => $vitality,
        'target' => $target,
        'achieved' => $achieved,
        'loop_start_utc_mysql' => isset( $current_loop['start_utc_mysql'] ) ? (string) $current_loop['start_utc_mysql'] : '',
        'loop_end_utc_mysql' => isset( $current_loop['end_utc_mysql'] ) ? (string) $current_loop['end_utc_mysql'] : '',
        'time_remaining_seconds' => $time_remaining_seconds,
        'time_remaining_label' => self::format_time_remaining_label( $time_remaining_seconds ),
    );

    $components = isset( $computed['components'] ) && is_array( $computed['components'] ) ? $computed['components'] : array();
    $components['clock'] = 'availability_active_time';
    $components['effective_target'] = (int) $effective_target_v;
    $components['availability_phase'] = isset( $context['state_now']['phase'] ) ? (string) $context['state_now']['phase'] : '';
    $components['availability_phase_day'] = isset( $context['state_now']['phase_day'] ) ? (int) $context['state_now']['phase_day'] : 0;
    $components['availability_phase_length'] = isset( $context['state_now']['phase_length'] ) ? (int) $context['state_now']['phase_length'] : 0;
    $components['availability_is_paused'] = ! empty( $context['state_now']['is_paused'] ) ? 1 : 0;
    $components['nominal_active_seconds'] = isset( $current_loop['nominal_active_seconds'] ) ? (int) $current_loop['nominal_active_seconds'] : 0;
    $components['active_elapsed_seconds'] = isset( $current_loop['active_elapsed_seconds'] ) ? (int) $current_loop['active_elapsed_seconds'] : 0;
    $components['active_remaining_seconds'] = $time_remaining_seconds;
    $components['paused_seconds_total'] = isset( $current_loop['paused_seconds_total'] ) ? (int) $current_loop['paused_seconds_total'] : 0;
    $components['carryover_ratio'] = (float) $carryover_ratio;
    if ( isset( $stats['rows_scanned'] ) ) {
        $components['availability_rows_scanned'] = (int) $stats['rows_scanned'];
    }
    $payload['vitality_components'] = $components;

    return $payload;
}

private static function availability_collect_completion_stats( $user_id, $goal_id, array $goal_row, array $availability, array $current_loop, $previous_loop, DateTimeImmutable $now_tz ) {
    global $wpdb;

    $out = array(
        'current_achieved' => 0,
        'previous_achieved' => 0,
        'current_last_effective_ts' => 0,
        'rows_scanned' => 0,
    );

    if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_completions' ) ) {
        return $out;
    }

    $query_start = isset( $current_loop['start_utc_mysql'] ) ? (string) $current_loop['start_utc_mysql'] : '';
    if ( is_array( $previous_loop ) && ! empty( $previous_loop['start_utc_mysql'] ) ) {
        $query_start = (string) $previous_loop['start_utc_mysql'];
    }
    $query_end = isset( $current_loop['end_utc_mysql'] ) ? (string) $current_loop['end_utc_mysql'] : '';
    if ( '' === $query_start || '' === $query_end ) {
        return $out;
    }

    $completions_table = TCT_DB::table_completions();
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT completed_at, source, note FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s ORDER BY completed_at ASC",
            (int) $user_id,
            (int) $goal_id,
            $query_start,
            $query_end
        ),
        ARRAY_A
    );

    if ( ! is_array( $rows ) || empty( $rows ) ) {
        return $out;
    }

    $goal_type = isset( $goal_row['goal_type'] ) ? (string) $goal_row['goal_type'] : 'positive';
    $current_start_ts = isset( $current_loop['start_ts'] ) ? (int) $current_loop['start_ts'] : 0;
    $current_end_ts = isset( $current_loop['end_ts'] ) ? (int) $current_loop['end_ts'] : 0;
    $previous_start_ts = is_array( $previous_loop ) && isset( $previous_loop['start_ts'] ) ? (int) $previous_loop['start_ts'] : 0;
    $previous_end_ts = is_array( $previous_loop ) && isset( $previous_loop['end_ts'] ) ? (int) $previous_loop['end_ts'] : 0;
    $now_ts = (int) $now_tz->getTimestamp();
    $tz = $now_tz->getTimezone();

    foreach ( $rows as $row ) {
        $out['rows_scanned']++;
        $completed_at = isset( $row['completed_at'] ) ? (string) $row['completed_at'] : '';
        $source = isset( $row['source'] ) ? (string) $row['source'] : '';
        $note = isset( $row['note'] ) ? $row['note'] : '';

        $classification = TCT_Interval::availability_cycle_completion_classification( $availability, $completed_at, $source, $tz );
        if ( empty( $classification['counted'] ) || ! empty( $classification['excluded'] ) ) {
            continue;
        }

        $effective_ts = isset( $classification['effective_ts'] ) ? (int) $classification['effective_ts'] : 0;
        if ( $effective_ts <= 0 ) {
            continue;
        }

        $weight = self::availability_completion_weight_for_row( $goal_type, $note );

        if ( $previous_start_ts > 0 && $previous_end_ts > $previous_start_ts && $effective_ts >= $previous_start_ts && $effective_ts < $previous_end_ts ) {
            $out['previous_achieved'] += $weight;
        }

        if ( $current_end_ts > $current_start_ts && $effective_ts >= $current_start_ts && $effective_ts < $current_end_ts && $effective_ts <= $now_ts ) {
            $out['current_achieved'] += $weight;
            if ( $effective_ts > (int) $out['current_last_effective_ts'] ) {
                $out['current_last_effective_ts'] = $effective_ts;
            }
        }
    }

    if ( $out['current_achieved'] < 0 ) {
        $out['current_achieved'] = 0;
    }
    if ( $out['previous_achieved'] < 0 ) {
        $out['previous_achieved'] = 0;
    }

    return $out;
}

private static function availability_completion_weight_for_row( $goal_type, $note ) {
    $goal_type_l = strtolower( trim( (string) $goal_type ) );
    $is_anki_cards_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) )
        ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type_l )
        : ( 'anki_cards' === $goal_type_l );

    if ( ! $is_anki_cards_goal ) {
        return 1;
    }

    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'parse_anki_cards_completion_note' ) ) ) {
        $weight = (int) TCT_Utils::parse_anki_cards_completion_note( $note );
        return max( 0, $weight );
    }

    return 0;
}

private static function availability_parse_mysql_utc_ts( $value ) {
    $value = is_string( $value ) ? trim( $value ) : '';
    if ( '' === $value || '0000-00-00 00:00:00' === $value ) {
        return 0;
    }

    try {
        return (int) ( new DateTimeImmutable( $value, new DateTimeZone( 'UTC' ) ) )->getTimestamp();
    } catch ( Exception $e ) {
        return 0;
    }
}

private static function availability_ts_to_local_dt( $ts, DateTimeZone $tz ) {
    $ts = (int) $ts;
    if ( $ts <= 0 ) {
        return null;
    }

    try {
        return ( new DateTimeImmutable( '@' . $ts ) )->setTimezone( $tz );
    } catch ( Exception $e ) {
        return null;
    }
}

private static function availability_prorated_target( $target, $anchor_ts, array $loop, $availability, DateTimeZone $tz ) {
    $target = (int) $target;
    $anchor_ts = (int) $anchor_ts;
    $loop_start_ts = isset( $loop['start_ts'] ) ? (int) $loop['start_ts'] : 0;
    $loop_end_ts = isset( $loop['end_ts'] ) ? (int) $loop['end_ts'] : 0;
    $nominal_active_seconds = isset( $loop['nominal_active_seconds'] ) ? (int) $loop['nominal_active_seconds'] : 0;

    if ( $target <= 0 ) {
        return 0;
    }
    if ( $loop_end_ts <= $loop_start_ts ) {
        return $target;
    }
    if ( $nominal_active_seconds <= 0 ) {
        $nominal_active_seconds = max( 1, $loop_end_ts - $loop_start_ts );
    }
    if ( $anchor_ts <= 0 || $anchor_ts <= $loop_start_ts ) {
        return $target;
    }
    if ( $anchor_ts >= $loop_end_ts ) {
        return 0;
    }

    $anchor_dt = self::availability_ts_to_local_dt( $anchor_ts, $tz );
    $loop_end_dt = isset( $loop['end'] ) && ( $loop['end'] instanceof DateTimeImmutable ) ? $loop['end'] : self::availability_ts_to_local_dt( $loop_end_ts, $tz );
    if ( ! ( $anchor_dt instanceof DateTimeImmutable ) || ! ( $loop_end_dt instanceof DateTimeImmutable ) ) {
        return $target;
    }

    if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'availability_cycle_active_seconds_in_range' ) ) ) {
        $remaining_active_seconds = (int) TCT_Interval::availability_cycle_active_seconds_in_range( $availability, $anchor_dt, $loop_end_dt, $tz );
    } else {
        $remaining_active_seconds = max( 0, $loop_end_ts - $anchor_ts );
    }

    if ( $remaining_active_seconds < 0 ) {
        $remaining_active_seconds = 0;
    }
    if ( $remaining_active_seconds > $nominal_active_seconds ) {
        $remaining_active_seconds = $nominal_active_seconds;
    }

    $fraction = (float) $remaining_active_seconds / (float) max( 1, $nominal_active_seconds );
    $prorated = (int) floor( (float) $target * $fraction );
    if ( $prorated < 0 ) {
        $prorated = 0;
    }
    if ( $prorated > $target ) {
        $prorated = $target;
    }

    return $prorated;
}

private static function compute_active_clock( array $args ) {
    $target = isset( $args['target'] ) ? (int) $args['target'] : 0;
    $achieved = isset( $args['achieved'] ) ? (int) $args['achieved'] : 0;
    $duration = isset( $args['nominal_active_seconds'] ) ? (int) $args['nominal_active_seconds'] : 0;
    $elapsed_s = isset( $args['active_elapsed_seconds'] ) ? (int) $args['active_elapsed_seconds'] : 0;
    $time_since_last_s = isset( $args['active_since_last_seconds'] ) ? (int) $args['active_since_last_seconds'] : 0;
    $importance = isset( $args['importance'] ) ? (int) $args['importance'] : 3;
    $difficulty = isset( $args['difficulty'] ) ? (int) $args['difficulty'] : 3;
    $carryover_ratio = isset( $args['carryover_ratio'] ) ? (float) $args['carryover_ratio'] : 1.0;

    if ( $target <= 0 ) {
        return array( 'vitality' => 100, 'components' => array( 'reason' => 'no_target', ) );
    }
    if ( $achieved < 0 ) {
        $achieved = 0;
    }
    if ( $achieved >= $target ) {
        return array( 'vitality' => 100, 'components' => array( 'reason' => 'target_met', ) );
    }
    if ( $duration <= 0 ) {
        return array( 'vitality' => 100, 'components' => array( 'reason' => 'invalid_window', ) );
    }

    if ( $elapsed_s < 0 ) {
        $elapsed_s = 0;
    }
    if ( $elapsed_s > $duration ) {
        $elapsed_s = $duration;
    }
    if ( $time_since_last_s < 0 ) {
        $time_since_last_s = 0;
    }
    if ( $time_since_last_s > $duration ) {
        $time_since_last_s = $duration;
    }

    $elapsed_f = self::clamp_01( (float) $elapsed_s / (float) $duration );
    $expected_reps = $elapsed_f * (float) $target;
    $deficit_reps = max( 0.0, $expected_reps - (float) $achieved );

    $spacing_seconds = (float) $duration / (float) max( 1, $target );
    if ( $spacing_seconds <= 0 ) {
        $spacing_seconds = 1.0;
    }

    $hydration_cap = self::compute_hydration_cap( $time_since_last_s, $spacing_seconds, $importance, $difficulty );
    $spacing_days = (float) $spacing_seconds / 86400.0;
    if ( $spacing_days <= 0.0001 ) {
        $spacing_days = 0.0001;
    }

    $pace_cap = self::compute_pace_cap( $deficit_reps, $spacing_days, $target );
    $carry_cap = 1.0;
    if ( $achieved <= 0 ) {
        $carry_cap = self::clamp_01( $carryover_ratio );
    }

    $cap = min( $hydration_cap, $pace_cap, $carry_cap );
    $cap = self::clamp_01( $cap );
    $vitality = (int) round( 100.0 * $cap );
    if ( $vitality < 0 ) {
        $vitality = 0;
    }
    if ( $vitality > 100 ) {
        $vitality = 100;
    }

    return array(
        'vitality' => $vitality,
        'components' => array(
            'elapsed_fraction' => $elapsed_f,
            'expected_reps' => $expected_reps,
            'deficit_reps' => $deficit_reps,
            'spacing_days' => $spacing_days,
            'time_since_last_s' => $time_since_last_s,
            'hydration_cap' => $hydration_cap,
            'pace_cap' => $pace_cap,
            'carry_cap' => $carry_cap,
            'importance' => $importance,
            'difficulty' => $difficulty,
        ),
    );
}

private static function compute_for_goal_legacy( $user_id, array $goal_row, DateTimeImmutable $now_tz ) { global $wpdb; $user_id = (int) $user_id; if ( $user_id <= 0 ) { return null; } $goal_id = isset( $goal_row['id'] ) ? (int) $goal_row['id'] : 0; if ( $goal_id <= 0 ) { return null; } $target = isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0; if ( $target < 0 ) { $target = 0; } $goal_type = isset( $goal_row['goal_type'] ) && is_string( $goal_row['goal_type'] ) ? (string) $goal_row['goal_type'] : 'positive'; $threshold = isset( $goal_row['threshold'] ) && is_numeric( $goal_row['threshold'] ) ? (int) $goal_row['threshold'] : null; $is_negative = TCT_Utils::is_negative_goal_type( $goal_type ); if ( $is_negative ) { return self::compute_negative_goal_vitality( $user_id, $goal_row, $now_tz, $goal_type, $threshold ); } $is_no_interval_positive = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) : ( 'positive_no_int' === $goal_type ); $is_anki_cards_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type ) : ( 'anki_cards' === strtolower( trim( (string) $goal_type ) ) ); if ( $target <= 0 ) { if ( $is_no_interval_positive ) { $period_unit = isset( $goal_row['period_unit'] ) ? sanitize_text_field( (string) $goal_row['period_unit'] ) : 'week'; $span = isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1; if ( $span < 1 ) { $span = 1; } $bounds = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $bounds = TCT_Interval::goal_interval_current_loop_bounds( $goal_row, $now_tz ); } if ( ! is_array( $bounds ) || ! isset( $bounds['start'], $bounds['end'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) || ! ( $bounds['end'] instanceof DateTimeImmutable ) ) { $bounds = self::compute_loop_bounds_fallback( $now_tz, $period_unit, $span ); } $start_tz = $bounds['start']; $end_tz = $bounds['end']; $utc = new DateTimeZone( 'UTC' ); $start_utc = $start_tz->setTimezone( $utc ); $end_utc = $end_tz->setTimezone( $utc ); $start_utc_mysql = $start_utc->format( 'Y-m-d H:i:s' ); $end_utc_mysql = $end_utc->format( 'Y-m-d H:i:s' ); $completions_table = TCT_DB::table_completions(); $achieved = $is_anki_cards_goal ? (int) self::sum_anki_cards_in_window( $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) : (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) ); if ( $achieved < 0 ) { $achieved = 0; } $time_remaining_seconds = max( 0, (int) ( $end_tz->getTimestamp() - $now_tz->getTimestamp() ) ); return array( 'vitality' => 100, 'target' => $target, 'achieved' => $achieved, 'loop_start_utc_mysql' => $start_utc_mysql, 'loop_end_utc_mysql' => $end_utc_mysql, 'time_remaining_seconds' => $time_remaining_seconds, 'time_remaining_label' => self::format_time_remaining_label( $time_remaining_seconds ), ); } return array( 'vitality' => 100, 'target' => $target, 'achieved' => 0, 'loop_start_utc_mysql' => '', 'loop_end_utc_mysql' => '', 'time_remaining_seconds' => 0, 'time_remaining_label' => '', ); } $period_unit = isset( $goal_row['period_unit'] ) ? sanitize_text_field( (string) $goal_row['period_unit'] ) : 'week'; $span = isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1; if ( $span < 1 ) { $span = 1; } $sleep_tracking_enabled = isset( $goal_row['sleep_tracking_enabled'] ) ? (int) $goal_row['sleep_tracking_enabled'] : 0; $sleep_rollover_time = isset( $goal_row['sleep_rollover_time'] ) && is_string( $goal_row['sleep_rollover_time'] ) ? trim( (string) $goal_row['sleep_rollover_time'] ) : ''; if ( '' === $sleep_rollover_time ) { $sleep_rollover_time = '18:00'; } if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $sleep_rollover_time ) ) { $sleep_rollover_time = '18:00'; } $is_sleep_daily = ( $sleep_tracking_enabled && 'day' === $period_unit && 1 === $span ); $importance = isset( $goal_row['importance'] ) ? (int) $goal_row['importance'] : 3; $difficulty = 3; if ( isset( $goal_row['difficulty'] ) ) { $difficulty = (int) $goal_row['difficulty']; } elseif ( isset( $goal_row['effort'] ) ) { $difficulty = (int) $goal_row['effort']; } $bounds = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $bounds = TCT_Interval::goal_interval_current_loop_bounds( $goal_row, $now_tz ); } if ( ! is_array( $bounds ) || ! isset( $bounds['start'], $bounds['end'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) || ! ( $bounds['end'] instanceof DateTimeImmutable ) ) { $bounds = self::compute_loop_bounds_fallback( $now_tz, $period_unit, $span ); } $start_tz = $bounds['start']; $end_tz = $bounds['end']; if ( $is_sleep_daily ) { $parts = explode( ':', $sleep_rollover_time ); $rh = isset( $parts[0] ) ? (int) $parts[0] : 18; $rm = isset( $parts[1] ) ? (int) $parts[1] : 0; $today_roll = $now_tz->setTime( $rh, $rm, 0 ); if ( $now_tz < $today_roll ) { $start_tz = $today_roll->sub( new DateInterval( 'P1D' ) ); } else { $start_tz = $today_roll; } $end_tz = $start_tz->add( new DateInterval( 'P1D' ) ); } $utc = new DateTimeZone( 'UTC' ); $start_utc = $start_tz->setTimezone( $utc ); $end_utc = $end_tz->setTimezone( $utc ); $start_utc_mysql = $start_utc->format( 'Y-m-d H:i:s' ); $end_utc_mysql = $end_utc->format( 'Y-m-d H:i:s' ); $completions_table = TCT_DB::table_completions(); $achieved = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) ); if ( $achieved < 0 ) { $achieved = 0; } if ( $is_sleep_daily ) { $sleep_date = $start_tz->format( 'Y-m-d' ); $sleep_ref = 'sleep:' . (int) $goal_id . ':' . (string) $sleep_date; $sleep_achieved = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = %s AND source_ref = %s", $user_id, $goal_id, 'sleep', $sleep_ref ) ); if ( $sleep_achieved > 0 ) { $sleep_achieved = 1; } if ( $sleep_achieved > $achieved ) { $achieved = $sleep_achieved; } } $effective_target_v = $target; $created_at_raw_v = isset( $goal_row['created_at'] ) ? trim( (string) $goal_row['created_at'] ) : ''; $updated_at_raw_v = isset( $goal_row['updated_at'] ) ? trim( (string) $goal_row['updated_at'] ) : ''; if ( '' === $created_at_raw_v || '' === $updated_at_raw_v ) { $goals_table_v = TCT_DB::table_goals(); $ts_row_v = $wpdb->get_row( $wpdb->prepare( "SELECT created_at, updated_at FROM {$goals_table_v} WHERE user_id = %d AND id = %d", $user_id, $goal_id ), ARRAY_A ); if ( is_array( $ts_row_v ) ) { if ( '' === $created_at_raw_v && isset( $ts_row_v['created_at'] ) ) { $created_at_raw_v = trim( (string) $ts_row_v['created_at'] ); } if ( '' === $updated_at_raw_v && isset( $ts_row_v['updated_at'] ) ) { $updated_at_raw_v = trim( (string) $ts_row_v['updated_at'] ); } } } $created_at_ts_v = 0; $updated_at_ts_v = 0; if ( '' !== $created_at_raw_v && '0000-00-00 00:00:00' !== $created_at_raw_v ) { $tmp = strtotime( $created_at_raw_v . ' UTC' ); if ( false !== $tmp ) { $created_at_ts_v = (int) $tmp; } } if ( '' !== $updated_at_raw_v && '0000-00-00 00:00:00' !== $updated_at_raw_v ) { $tmp = strtotime( $updated_at_raw_v . ' UTC' ); if ( false !== $tmp ) { $updated_at_ts_v = (int) $tmp; } } $prorate_anchor_v = TCT_Utils::compute_prorate_anchor_ts( $created_at_ts_v, $updated_at_ts_v, (int) $start_tz->getTimestamp() ); if ( $prorate_anchor_v > 0 ) { $effective_target_v = TCT_Utils::compute_prorated_target( $target, $prorate_anchor_v, (int) $start_tz->getTimestamp(), (int) $end_tz->getTimestamp() ); } if ( $effective_target_v <= 0 ) { $time_remaining_seconds = max( 0, (int) ( $end_tz->getTimestamp() - $now_tz->getTimestamp() ) ); return array( 'vitality' => 100, 'target' => $target, 'achieved' => $achieved, 'loop_start_utc_mysql' => $start_utc_mysql, 'loop_end_utc_mysql' => $end_utc_mysql, 'time_remaining_seconds' => $time_remaining_seconds, 'time_remaining_label' => self::format_time_remaining_label( $time_remaining_seconds ), ); } if ( $achieved >= $effective_target_v ) { $time_remaining_seconds = max( 0, (int) ( $end_tz->getTimestamp() - $now_tz->getTimestamp() ) ); return array( 'vitality' => 100, 'target' => $target, 'achieved' => $achieved, 'loop_start_utc_mysql' => $start_utc_mysql, 'loop_end_utc_mysql' => $end_utc_mysql, 'time_remaining_seconds' => $time_remaining_seconds, 'time_remaining_label' => self::format_time_remaining_label( $time_remaining_seconds ), ); } $last_completed_raw = (string) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $last_completed_ts = 0; $last_completed_raw = is_string( $last_completed_raw ) ? trim( $last_completed_raw ) : ''; if ( '' !== $last_completed_raw && '0000-00-00 00:00:00' !== $last_completed_raw ) { $tmp = strtotime( $last_completed_raw . ' UTC' ); if ( false !== $tmp ) { $last_completed_ts = (int) $tmp; } } $last_completed_ts_for_compute = $last_completed_ts; $carryover_ratio = 1.0; if ( 0 === $achieved ) { $apply_carryover = true; if ( $last_completed_ts <= 0 ) { $updated_at_raw = isset( $goal_row['updated_at'] ) ? (string) $goal_row['updated_at'] : ''; $updated_at_raw = is_string( $updated_at_raw ) ? trim( $updated_at_raw ) : ''; if ( '' === $updated_at_raw ) { $goals_table = TCT_DB::table_goals(); $updated_at_raw = (string) $wpdb->get_var( $wpdb->prepare( "SELECT updated_at FROM {$goals_table} WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); $updated_at_raw = is_string( $updated_at_raw ) ? trim( $updated_at_raw ) : ''; } if ( '' !== $updated_at_raw && '0000-00-00 00:00:00' !== $updated_at_raw ) { $updated_ts = strtotime( $updated_at_raw . ' UTC' ); if ( false !== $updated_ts ) { if ( (int) $updated_ts >= (int) $start_utc->getTimestamp() ) { $apply_carryover = false; $carryover_ratio = 1.0; $last_completed_ts_for_compute = (int) max( (int) $updated_ts, (int) $start_tz->getTimestamp() ); } } } } if ( $apply_carryover ) { $prev_moment = $start_tz->modify( '-1 second' ); $prev_bounds = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $prev_bounds = TCT_Interval::current_loop_bounds( $prev_moment, $period_unit, $span ); } if ( ! is_array( $prev_bounds ) || ! isset( $prev_bounds['start'], $prev_bounds['end'] ) || ! ( $prev_bounds['start'] instanceof DateTimeImmutable ) || ! ( $prev_bounds['end'] instanceof DateTimeImmutable ) ) { $prev_bounds = self::compute_loop_bounds_fallback( $prev_moment, $period_unit, $span ); } $prev_start_utc_mysql = $prev_bounds['start']->setTimezone( $utc )->format( 'Y-m-d H:i:s' ); $prev_end_utc_mysql = $prev_bounds['end']->setTimezone( $utc )->format( 'Y-m-d H:i:s' ); if ( $is_sleep_daily ) { $prev_start_tz = $start_tz->sub( new DateInterval( 'P1D' ) ); $prev_end_tz = $start_tz; $prev_start_utc_mysql = $prev_start_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' ); $prev_end_utc_mysql = $prev_end_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' ); } $prev_achieved = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $prev_start_utc_mysql, $prev_end_utc_mysql ) ); if ( $prev_achieved < 0 ) { $prev_achieved = 0; } if ( $is_sleep_daily ) { $prev_sleep_date = $prev_start_tz->format( 'Y-m-d' ); $prev_ref = 'sleep:' . (int) $goal_id . ':' . (string) $prev_sleep_date; $prev_sleep_achieved = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = %s AND source_ref = %s", $user_id, $goal_id, 'sleep', $prev_ref ) ); if ( $prev_sleep_achieved > 0 ) { $prev_sleep_achieved = 1; } if ( $prev_sleep_achieved > $prev_achieved ) { $prev_achieved = $prev_sleep_achieved; } } $carryover_ratio = self::clamp_01( (float) $prev_achieved / (float) $effective_target_v ); } } $time_remaining_seconds = max( 0, (int) ( $end_tz->getTimestamp() - $now_tz->getTimestamp() ) ); $computed = self::compute( array( 'target' => $effective_target_v, 'achieved' => $achieved, 'loop_start_ts' => (int) $start_tz->getTimestamp(), 'loop_end_ts' => (int) $end_tz->getTimestamp(), 'now_ts' => (int) $now_tz->getTimestamp(), 'last_completed_ts'=> (int) $last_completed_ts_for_compute, 'importance' => $importance, 'difficulty' => $difficulty, 'carryover_ratio' => (float) $carryover_ratio, ) ); $vitality = isset( $computed['vitality'] ) ? (int) $computed['vitality'] : 100; if ( $vitality < 0 ) { $vitality = 0; } if ( $vitality > 100 ) { $vitality = 100; } $payload = array( 'vitality' => $vitality, 'target' => $target, 'achieved' => $achieved, 'loop_start_utc_mysql' => $start_utc_mysql, 'loop_end_utc_mysql' => $end_utc_mysql, 'time_remaining_seconds' => $time_remaining_seconds, 'time_remaining_label' => self::format_time_remaining_label( $time_remaining_seconds ), ); if ( isset( $computed['components'] ) && is_array( $computed['components'] ) ) { $payload['vitality_components'] = $computed['components']; } return $payload; } 
private static function compute_negative_goal_vitality( $user_id, array $goal_row, DateTimeImmutable $now_tz, $goal_type, $threshold ) { global $wpdb; $user_id = (int) $user_id; $goal_id = isset( $goal_row['id'] ) ? (int) $goal_row['id'] : 0; $period_unit = isset( $goal_row['period_unit'] ) ? sanitize_text_field( (string) $goal_row['period_unit'] ) : 'week'; $span = isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1; if ( $span < 1 ) { $span = 1; } $bounds = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $bounds = TCT_Interval::goal_interval_current_loop_bounds( $goal_row, $now_tz ); } if ( ! is_array( $bounds ) || ! isset( $bounds['start'], $bounds['end'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) || ! ( $bounds['end'] instanceof DateTimeImmutable ) ) { $bounds = self::compute_loop_bounds_fallback( $now_tz, $period_unit, $span ); } $start_tz = $bounds['start']; $end_tz = $bounds['end']; $utc = new DateTimeZone( 'UTC' ); $start_utc = $start_tz->setTimezone( $utc ); $end_utc = $end_tz->setTimezone( $utc ); $start_utc_mysql = $start_utc->format( 'Y-m-d H:i:s' ); $end_utc_mysql = $end_utc->format( 'Y-m-d H:i:s' ); $completions_table = TCT_DB::table_completions(); $achieved = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) ); if ( $achieved < 0 ) { $achieved = 0; } $violations = TCT_Utils::count_negative_goal_violations( $goal_type, $threshold, $achieved ); $vitality_drop = TCT_Utils::negative_goal_vitality_drop(); $vitality = 100 - ( $vitality_drop * $violations ); if ( $vitality > 100 ) { $vitality = 100; } $time_remaining_seconds = max( 0, (int) ( $end_tz->getTimestamp() - $now_tz->getTimestamp() ) ); $display_target = 0; if ( 'harm_reduction' === strtolower( trim( $goal_type ) ) && null !== $threshold && (int) $threshold > 0 ) { $display_target = (int) $threshold; } return array( 'vitality' => (int) $vitality, 'target' => $display_target, 'achieved' => $achieved, 'loop_start_utc_mysql' => $start_utc_mysql, 'loop_end_utc_mysql' => $end_utc_mysql, 'time_remaining_seconds' => $time_remaining_seconds, 'time_remaining_label' => self::format_time_remaining_label( $time_remaining_seconds ), 'vitality_components' => array( 'reason' => 'negative_goal', 'goal_type' => $goal_type, 'threshold' => $threshold, 'completions' => $achieved, 'violations' => $violations, 'vitality_drop_per' => $vitality_drop, ), ); } private static function compute_loop_bounds_fallback( DateTimeImmutable $now_tz, $unit, $span = 1 ) { $unit = is_string( $unit ) ? strtolower( trim( $unit ) ) : 'week'; $span = (int) $span; if ( $span < 1 ) { $span = 1; } $tz = $now_tz->getTimezone(); $year = (int) $now_tz->format( 'Y' ); $month = (int) $now_tz->format( 'n' ); $day = (int) $now_tz->format( 'j' ); if ( 'hour' === $unit ) { $day_start = $now_tz->setTime( 0, 0, 0 ); $seconds_since = (int) ( $now_tz->getTimestamp() - $day_start->getTimestamp() ); if ( $seconds_since < 0 ) { $seconds_since = 0; } $block_seconds = $span * 3600; $block_index = (int) floor( (float) $seconds_since / (float) $block_seconds ); $start = $day_start->add( new DateInterval( 'PT' . (int) ( $block_index * $span ) . 'H' ) ); $end = $start->add( new DateInterval( 'PT' . (int) $span . 'H' ) ); return array( 'start' => $start, 'end' => $end ); } if ( 'day' === $unit ) { $block_start_day = 1 + intdiv( max( 0, $day - 1 ), $span ) * $span; $start = $now_tz->setDate( $year, $month, $block_start_day )->setTime( 0, 0, 0 ); $end = $start->add( new DateInterval( 'P' . (int) $span . 'D' ) ); return array( 'start' => $start, 'end' => $end ); } if ( 'quarter' === $unit ) { $unit = 'month'; $span = $span * 3; } if ( 'semiannual' === $unit ) { $unit = 'month'; $span = $span * 6; } if ( 'week' === $unit ) { $start_of_week = (int) get_option( 'start_of_week', 1 ); $dow = (int) $now_tz->format( 'w' ); $diff = ( $dow - $start_of_week + 7 ) % 7; $start_of_this_week = $now_tz->setTime( 0, 0, 0 )->sub( new DateInterval( 'P' . (int) $diff . 'D' ) ); $jan1 = $now_tz->setDate( $year, 1, 1 )->setTime( 0, 0, 0 ); $jan1_dow = (int) $jan1->format( 'w' ); $jan1_diff = ( $jan1_dow - $start_of_week + 7 ) % 7; $first_week_start = $jan1->sub( new DateInterval( 'P' . (int) $jan1_diff . 'D' ) ); $seconds_since = (int) ( $start_of_this_week->getTimestamp() - $first_week_start->getTimestamp() ); if ( $seconds_since < 0 ) { $seconds_since = 0; } $weeks_since = (int) floor( (float) $seconds_since / ( 7.0 * 86400.0 ) ); $block_index = intdiv( max( 0, $weeks_since ), $span ); $start = $first_week_start->add( new DateInterval( 'P' . (int) ( $block_index * $span * 7 ) . 'D' ) ); $end = $start->add( new DateInterval( 'P' . (int) ( $span * 7 ) . 'D' ) ); return array( 'start' => $start, 'end' => $end ); } if ( 'month' === $unit ) { $month_index = max( 0, $month - 1 ); $block_index = intdiv( $month_index, $span ); $start_month = 1 + ( $block_index * $span ); $start = $now_tz->setDate( $year, $start_month, 1 )->setTime( 0, 0, 0 ); $end = $start->add( new DateInterval( 'P' . (int) $span . 'M' ) ); return array( 'start' => $start, 'end' => $end ); } $start = $now_tz->setTime( 0, 0, 0 ); $end = $start->add( new DateInterval( 'P1D' ) ); return array( 'start' => $start, 'end' => $end ); } private static function sum_anki_cards_in_window( $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) { global $wpdb; if ( $user_id <= 0 || $goal_id <= 0 ) { return 0; } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_completions' ) ) { return 0; } $completions_table = TCT_DB::table_completions(); $rows = $wpdb->get_col( $wpdb->prepare( "SELECT note FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) ); if ( ! is_array( $rows ) || empty( $rows ) ) { return 0; } $sum = 0; foreach ( $rows as $note ) { $sum += ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'parse_anki_cards_completion_note' ) ) ) ? (int) TCT_Utils::parse_anki_cards_completion_note( $note ) : 0; } return max( 0, (int) $sum ); } private static function format_time_remaining_label( $seconds ) { $seconds = (int) $seconds; if ( $seconds <= 0 ) { return '0m'; } if ( $seconds < 60 ) { return (string) $seconds . 's'; } $minutes = intdiv( $seconds, 60 ); if ( $minutes < 60 ) { return (string) $minutes . 'm'; } $hours = intdiv( $minutes, 60 ); $min_r = $minutes % 60; if ( $hours < 24 ) { if ( $min_r > 0 && $hours < 6 ) { return (string) $hours . 'h ' . (string) $min_r . 'm'; } return (string) $hours . 'h'; } $days = intdiv( $hours, 24 ); $hr_r = $hours % 24; if ( $days < 7 ) { if ( $hr_r > 0 ) { return (string) $days . 'd ' . (string) $hr_r . 'h'; } return (string) $days . 'd'; } $weeks = intdiv( $days, 7 ); $day_r = $days % 7; if ( $day_r > 0 ) { return (string) $weeks . 'w ' . (string) $day_r . 'd'; } return (string) $weeks . 'w'; } } 
