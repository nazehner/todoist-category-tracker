<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } class TCT_Interval { public static function sanitize_span( $span ) { if ( is_string( $span ) ) { $span = trim( $span ); } $span_i = (int) $span; if ( $span_i < 1 ) { $span_i = 1; } if ( $span_i > 1000000 ) { $span_i = 1000000; } return $span_i; } public static function normalize_goal_interval_from_row( $goal_row ) { if ( ! is_array( $goal_row ) ) { return null; } $goal_type = isset( $goal_row['goal_type'] ) ? strtolower( trim( (string) $goal_row['goal_type'] ) ) : ''; if ( 'positive_no_int' === $goal_type ) { return null; } $interval = null; $raw = isset( $goal_row['intervals_json'] ) ? $goal_row['intervals_json'] : ''; if ( is_string( $raw ) && '' !== trim( $raw ) ) { $decoded = json_decode( $raw, true ); if ( is_array( $decoded ) ) { $candidates = array(); if ( isset( $decoded['target'] ) || isset( $decoded['period_unit'] ) || isset( $decoded['unit'] ) ) { $candidates = array( $decoded ); } else { $candidates = $decoded; } $unit_order = array( 'hour' => 0, 'day' => 1, 'week' => 2, 'month' => 3, 'quarter' => 4, 'semiannual' => 5, 'year' => 6, ); $best = null; $best_rank = 999; foreach ( $candidates as $iv ) { if ( ! is_array( $iv ) ) { continue; } $target = isset( $iv['target'] ) ? (int) $iv['target'] : 0; if ( $target <= 0 ) { continue; } if ( $target > 999999 ) { $target = 999999; } $unit = 'week'; if ( isset( $iv['period_unit'] ) ) { $unit = (string) $iv['period_unit']; } elseif ( isset( $iv['unit'] ) ) { $unit = (string) $iv['unit']; } if ( function_exists( 'sanitize_text_field' ) ) { $unit = sanitize_text_field( (string) $unit ); } $unit = self::normalize_unit( $unit ); $span = 1; if ( isset( $iv['period_span'] ) ) { $span = (int) $iv['period_span']; } elseif ( isset( $iv['span'] ) ) { $span = (int) $iv['span']; } $span = self::sanitize_span( $span ); $rank = isset( $unit_order[ $unit ] ) ? (int) $unit_order[ $unit ] : 999; if ( null === $best || $rank < $best_rank ) { $best = array( 'target' => (int) $target, 'period_unit' => (string) $unit, 'period_span' => (int) $span, 'period_mode' => 'calendar', ); $best_rank = $rank; } } if ( is_array( $best ) ) { $interval = $best; } } } if ( null === $interval ) { $target = isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0; if ( $target <= 0 ) { return null; } if ( $target > 999999 ) { $target = 999999; } $unit = isset( $goal_row['period_unit'] ) ? (string) $goal_row['period_unit'] : 'week'; if ( function_exists( 'sanitize_text_field' ) ) { $unit = sanitize_text_field( (string) $unit ); } $unit = self::normalize_unit( $unit ); $span = isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1; $span = self::sanitize_span( $span ); $interval = array( 'target' => (int) $target, 'period_unit' => (string) $unit, 'period_span' => (int) $span, 'period_mode' => 'calendar', ); } return $interval; }


    /**
     * Interval anchor engine.
     *
     * This lets the obligation clock be anchored independently from the active/pause
     * availability cycle. When enabled, interval windows are generated sequentially from
     * the derived anchor start rather than from legacy calendar buckets.
     */
    public static function normalize_interval_anchor_config( $raw, $goal_row = null ) {
        $out = array(
            'enabled'           => false,
            'anchor_date_local' => '',
            'anchor_day'        => 0,
            'anchor_start_local'=> '',
            'period_unit'       => '',
            'period_span'       => 0,
        );

        if ( is_string( $raw ) ) {
            $raw = trim( $raw );
            if ( '' === $raw ) {
                return $out;
            }
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                $raw = $decoded;
            } else {
                return $out;
            }
        } elseif ( is_object( $raw ) ) {
            $raw = (array) $raw;
        }

        if ( ! is_array( $raw ) ) {
            return $out;
        }

        $enabled_val = null;
        foreach ( array( 'enabled', 'is_enabled', 'on', 'active' ) as $key ) {
            if ( array_key_exists( $key, $raw ) ) {
                $enabled_val = $raw[ $key ];
                break;
            }
        }
        if ( ! self::av_boolish( $enabled_val ) ) {
            return $out;
        }

        if ( ! is_array( $goal_row ) || ! self::is_goal_eligible_for_interval_anchor( $goal_row ) ) {
            return $out;
        }

        $interval = self::normalize_goal_interval_from_row( $goal_row );
        if ( ! is_array( $interval ) ) {
            return $out;
        }

        $unit = isset( $interval['period_unit'] ) ? self::normalize_unit( $interval['period_unit'] ) : 'week';
        $span = isset( $interval['period_span'] ) ? self::sanitize_span( $interval['period_span'] ) : 1;
        if ( 'hour' === $unit ) {
            return $out;
        }

        $anchor_date_local = '';
        foreach ( array( 'anchor_date_local', 'anchor_date', 'anchor_local_date', 'anchor_date_ymd', 'date' ) as $key ) {
            if ( isset( $raw[ $key ] ) ) {
                $anchor_date_local = (string) $raw[ $key ];
                break;
            }
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $anchor_date_local = sanitize_text_field( $anchor_date_local );
        }
        $anchor_date_local = trim( $anchor_date_local );
        if ( strlen( $anchor_date_local ) >= 10 ) {
            $anchor_date_local = substr( $anchor_date_local, 0, 10 );
        }
        if ( ! self::ds_is_valid_ymd( $anchor_date_local ) ) {
            return $out;
        }

        $anchor_day = 0;
        foreach ( array( 'anchor_day', 'day', 'day_within_interval', 'interval_day', 'current_day' ) as $key ) {
            if ( isset( $raw[ $key ] ) ) {
                $anchor_day = (int) $raw[ $key ];
                break;
            }
        }
        if ( $anchor_day < 1 ) {
            return $out;
        }
        if ( $anchor_day > 1000000 ) {
            $anchor_day = 1000000;
        }

        $anchor_start_local = self::av_add_days_ymd( $anchor_date_local, -1 * ( $anchor_day - 1 ), self::ds_get_timezone( null ) );
        if ( ! self::ds_is_valid_ymd( $anchor_start_local ) ) {
            return $out;
        }

        $tz = self::ds_get_timezone( null );
        try {
            $anchor_start_dt = new DateTimeImmutable( $anchor_start_local . ' 00:00:00', $tz );
            $anchor_date_dt  = new DateTimeImmutable( $anchor_date_local . ' 00:00:00', $tz );
        } catch ( Exception $e ) {
            return $out;
        }

        $anchor_end_dt = self::interval_anchor_shift_start( $anchor_start_dt, $unit, $span, 1 );
        if ( ! ( $anchor_end_dt instanceof DateTimeImmutable ) || $anchor_end_dt->getTimestamp() <= $anchor_start_dt->getTimestamp() ) {
            return $out;
        }

        if ( $anchor_date_dt->getTimestamp() < $anchor_start_dt->getTimestamp() || $anchor_date_dt->getTimestamp() >= $anchor_end_dt->getTimestamp() ) {
            return $out;
        }

        $out['enabled'] = true;
        $out['anchor_date_local'] = $anchor_date_local;
        $out['anchor_day'] = $anchor_day;
        $out['anchor_start_local'] = $anchor_start_local;
        $out['period_unit'] = $unit;
        $out['period_span'] = $span;
        return $out;
    }

    public static function normalize_interval_anchor_from_row( $goal_row ) {
        $out = array(
            'enabled'           => false,
            'anchor_date_local' => '',
            'anchor_day'        => 0,
            'anchor_start_local'=> '',
            'period_unit'       => '',
            'period_span'       => 0,
        );
        if ( ! is_array( $goal_row ) ) {
            return $out;
        }

        $raw = '';
        if ( array_key_exists( 'interval_anchor_json', $goal_row ) ) {
            $raw = $goal_row['interval_anchor_json'];
        } elseif ( array_key_exists( 'interval_anchor', $goal_row ) ) {
            $raw = $goal_row['interval_anchor'];
        }

        $cfg = self::normalize_interval_anchor_config( $raw, $goal_row );
        return is_array( $cfg ) ? $cfg : $out;
    }

    public static function is_goal_eligible_for_interval_anchor( $goal_row ) {
        if ( ! self::is_goal_eligible_for_availability_cycle( $goal_row ) ) {
            return false;
        }
        $interval = self::normalize_goal_interval_from_row( $goal_row );
        if ( ! is_array( $interval ) ) {
            return false;
        }
        $unit = isset( $interval['period_unit'] ) ? self::normalize_unit( $interval['period_unit'] ) : 'week';
        return ( 'hour' !== $unit );
    }

    public static function goal_interval_current_loop_bounds( $goal_row, DateTimeImmutable $now_tz, $options = array() ) {
        return self::goal_interval_loop_bounds_at_datetime( $goal_row, $now_tz, $options );
    }

    public static function goal_interval_loop_bounds_at_datetime( $goal_row, DateTimeImmutable $now_tz, $options = array() ) {
        $interval = self::normalize_goal_interval_from_row( $goal_row );
        if ( ! is_array( $interval ) ) {
            return null;
        }

        $unit = isset( $interval['period_unit'] ) ? self::normalize_unit( $interval['period_unit'] ) : 'week';
        $span = isset( $interval['period_span'] ) ? self::sanitize_span( $interval['period_span'] ) : 1;
        if ( $span < 1 ) {
            $span = 1;
        }

        $anchor = self::normalize_interval_anchor_from_row( $goal_row );
        if ( empty( $anchor['enabled'] ) || ! self::is_goal_eligible_for_interval_anchor( $goal_row ) ) {
            return self::current_loop_bounds( $now_tz, $unit, $span );
        }

        $tz = $now_tz->getTimezone();
        try {
            $start = new DateTimeImmutable( $anchor['anchor_start_local'] . ' 00:00:00', $tz );
        } catch ( Exception $e ) {
            return self::current_loop_bounds( $now_tz, $unit, $span );
        }

        $end = self::interval_anchor_shift_start( $start, $unit, $span, 1 );
        if ( ! ( $end instanceof DateTimeImmutable ) || $end->getTimestamp() <= $start->getTimestamp() ) {
            return self::current_loop_bounds( $now_tz, $unit, $span );
        }

        $max_loops = isset( $options['max_loops'] ) ? max( 1, (int) $options['max_loops'] ) : 20000;
        $guard = 0;
        if ( $now_tz->getTimestamp() >= $start->getTimestamp() ) {
            while ( $now_tz->getTimestamp() >= $end->getTimestamp() && $guard < $max_loops ) {
                $guard++;
                $start = $end;
                $end = self::interval_anchor_shift_start( $start, $unit, $span, 1 );
                if ( ! ( $end instanceof DateTimeImmutable ) || $end->getTimestamp() <= $start->getTimestamp() ) {
                    return self::current_loop_bounds( $now_tz, $unit, $span );
                }
            }
        } else {
            while ( $now_tz->getTimestamp() < $start->getTimestamp() && $guard < $max_loops ) {
                $guard++;
                $end = $start;
                $start = self::interval_anchor_shift_start( $start, $unit, $span, -1 );
                if ( ! ( $start instanceof DateTimeImmutable ) || $end->getTimestamp() <= $start->getTimestamp() ) {
                    return self::current_loop_bounds( $now_tz, $unit, $span );
                }
            }
        }

        return array(
            'start' => $start,
            'end'   => $end,
            'anchored' => true,
            'interval_anchor' => $anchor,
        );
    }

    /**
     * Availability cycle engine scaffolding (Chunks 1-2).
     *
     * This remains inert by default until later chunks wire it into save/render/runtime flows.
     * Missing or invalid configs normalize to enabled=false so non-opted-in goals preserve
     * existing behavior bit-for-bit.
     */
    public static function normalize_availability_cycle_config( $raw ) {
        $out = array(
            'enabled'           => false,
            'anchor_date_local' => '',
            'anchor_phase'      => '',
            'anchor_day'        => 0,
            'active_duration'   => 0,
            'pause_duration'    => 0,
            'cycle_length'      => 0,
        );

        if ( is_string( $raw ) ) {
            $raw = trim( $raw );
            if ( '' === $raw ) {
                return $out;
            }
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                $raw = $decoded;
            } else {
                return $out;
            }
        } elseif ( is_object( $raw ) ) {
            $raw = (array) $raw;
        }

        if ( ! is_array( $raw ) ) {
            return $out;
        }

        $enabled_val = null;
        if ( array_key_exists( 'enabled', $raw ) ) {
            $enabled_val = $raw['enabled'];
        } elseif ( array_key_exists( 'is_enabled', $raw ) ) {
            $enabled_val = $raw['is_enabled'];
        } elseif ( array_key_exists( 'on', $raw ) ) {
            $enabled_val = $raw['on'];
        } elseif ( array_key_exists( 'active', $raw ) ) {
            $enabled_val = $raw['active'];
        }

        if ( ! self::av_boolish( $enabled_val ) ) {
            return $out;
        }

        $anchor_date_local = '';
        foreach ( array( 'anchor_date_local', 'anchor_date', 'anchor_local_date', 'anchor_date_ymd', 'date' ) as $key ) {
            if ( isset( $raw[ $key ] ) ) {
                $anchor_date_local = (string) $raw[ $key ];
                break;
            }
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $anchor_date_local = sanitize_text_field( $anchor_date_local );
        }
        $anchor_date_local = trim( $anchor_date_local );
        if ( strlen( $anchor_date_local ) >= 10 ) {
            $anchor_date_local = substr( $anchor_date_local, 0, 10 );
        }
        if ( ! self::ds_is_valid_ymd( $anchor_date_local ) ) {
            return $out;
        }

        $anchor_phase = '';
        if ( isset( $raw['anchor_phase'] ) ) {
            $anchor_phase = (string) $raw['anchor_phase'];
        } elseif ( isset( $raw['phase'] ) ) {
            $anchor_phase = (string) $raw['phase'];
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $anchor_phase = sanitize_text_field( $anchor_phase );
        }
        $anchor_phase = strtolower( trim( $anchor_phase ) );
        if ( 'paused' === $anchor_phase ) {
            $anchor_phase = 'pause';
        }
        if ( ! in_array( $anchor_phase, array( 'active', 'pause' ), true ) ) {
            return $out;
        }

        $anchor_day = 0;
        foreach ( array( 'anchor_day', 'anchor_day_within_phase', 'anchor_day_in_phase', 'day', 'day_in_phase' ) as $key ) {
            if ( isset( $raw[ $key ] ) ) {
                $anchor_day = (int) $raw[ $key ];
                break;
            }
        }

        $active_duration = 0;
        foreach ( array( 'active_duration', 'active_days', 'active_length', 'active_span' ) as $key ) {
            if ( isset( $raw[ $key ] ) ) {
                $active_duration = (int) $raw[ $key ];
                break;
            }
        }

        $pause_duration = 0;
        foreach ( array( 'pause_duration', 'pause_days', 'pause_length', 'pause_span' ) as $key ) {
            if ( isset( $raw[ $key ] ) ) {
                $pause_duration = (int) $raw[ $key ];
                break;
            }
        }

        if ( $active_duration <= 0 || $pause_duration <= 0 || $anchor_day <= 0 ) {
            return $out;
        }

        $active_duration = self::sanitize_span( $active_duration );
        $pause_duration  = self::sanitize_span( $pause_duration );
        $phase_length    = ( 'active' === $anchor_phase ) ? $active_duration : $pause_duration;
        if ( $anchor_day > $phase_length ) {
            return $out;
        }

        $out['enabled']           = true;
        $out['anchor_date_local'] = $anchor_date_local;
        $out['anchor_phase']      = $anchor_phase;
        $out['anchor_day']        = (int) $anchor_day;
        $out['active_duration']   = (int) $active_duration;
        $out['pause_duration']    = (int) $pause_duration;
        $out['cycle_length']      = (int) ( $active_duration + $pause_duration );

        return $out;
    }

    /**
     * Convenience wrapper: extract + normalize availability cycle config from a goal DB row.
     */
    public static function normalize_availability_cycle_from_row( $goal_row ) {
        $raw = '';
        if ( is_array( $goal_row ) && array_key_exists( 'availability_cycle_json', $goal_row ) ) {
            $raw = $goal_row['availability_cycle_json'];
        }
        return self::normalize_availability_cycle_config( $raw );
    }

    /**
     * Resolve the availability phase for a local date (or date-like input).
     *
     * The cycle is day-based in the WP/site timezone. The returned state includes:
     *   - active vs paused
     *   - phase day / phase length
     *   - current phase start/end (local)
     *   - first active local midnight on/after the reference date
     *
     * Invalid/missing configs return enabled=false.
     */
    public static function availability_cycle_state_on_local_date( $availability_raw_or_norm, $local_ymd = null, DateTimeZone $tz = null ) {
        $cfg = self::normalize_availability_cycle_config( $availability_raw_or_norm );
        $tz  = self::ds_get_timezone( $tz );
        $ref_dt = self::av_mixed_to_local_datetime( $local_ymd, $tz, true );
        if ( ! ( $ref_dt instanceof DateTimeImmutable ) ) {
            return self::av_disabled_state( $cfg );
        }

        $ref_local_ymd = $ref_dt->format( 'Y-m-d' );
        if ( empty( $cfg['enabled'] ) ) {
            return self::av_disabled_state( $cfg, $ref_dt, $ref_local_ymd );
        }

        $cycle_length = self::av_cycle_length_from_cfg( $cfg );
        if ( $cycle_length <= 0 ) {
            return self::av_disabled_state( $cfg, $ref_dt, $ref_local_ymd );
        }

        $anchor_phase_start_local = self::av_add_days_ymd( $cfg['anchor_date_local'], 1 - (int) $cfg['anchor_day'], $tz );
        if ( ! self::ds_is_valid_ymd( $anchor_phase_start_local ) ) {
            return self::av_disabled_state( $cfg, $ref_dt, $ref_local_ymd );
        }

        if ( 'active' === $cfg['anchor_phase'] ) {
            $cycle_active_start_local = $anchor_phase_start_local;
        } else {
            $cycle_active_start_local = self::av_add_days_ymd( $anchor_phase_start_local, -1 * (int) $cfg['active_duration'], $tz );
        }
        if ( ! self::ds_is_valid_ymd( $cycle_active_start_local ) ) {
            return self::av_disabled_state( $cfg, $ref_dt, $ref_local_ymd );
        }

        $anchor_phase_start_dt  = self::ds_midnight( $anchor_phase_start_local, $tz );
        $cycle_active_start_dt  = self::ds_midnight( $cycle_active_start_local, $tz );
        $reference_midnight_dt  = self::ds_midnight( $ref_local_ymd, $tz );
        $diff_days              = self::ds_diff_days( $cycle_active_start_dt, $reference_midnight_dt );
        $cycle_day_index        = self::av_positive_mod( $diff_days, $cycle_length );
        $active_duration        = (int) $cfg['active_duration'];
        $pause_duration         = (int) $cfg['pause_duration'];

        $phase        = 'active';
        $phase_length = $active_duration;
        $phase_offset = $cycle_day_index;
        if ( $cycle_day_index >= $active_duration ) {
            $phase        = 'pause';
            $phase_length = $pause_duration;
            $phase_offset = $cycle_day_index - $active_duration;
        }

        $phase_day = $phase_offset + 1;
        $current_phase_start_local = self::av_add_days_ymd( $ref_local_ymd, -1 * $phase_offset, $tz );
        $current_phase_end_local_exclusive = self::av_add_days_ymd( $current_phase_start_local, $phase_length, $tz );
        $days_remaining_in_phase = self::av_days_between_local_ymd( $ref_local_ymd, $current_phase_end_local_exclusive, $tz );
        if ( $days_remaining_in_phase < 0 ) {
            $days_remaining_in_phase = 0;
        }

        $next_active_start_local = ( 'active' === $phase ) ? $ref_local_ymd : $current_phase_end_local_exclusive;
        $days_until_resume = ( 'pause' === $phase ) ? self::av_days_between_local_ymd( $ref_local_ymd, $next_active_start_local, $tz ) : 0;
        if ( $days_until_resume < 0 ) {
            $days_until_resume = 0;
        }

        $out = array(
            'enabled'                            => true,
            'reference_local_date'               => $ref_local_ymd,
            'reference_local_datetime'           => $ref_dt,
            'reference_local_midnight'           => $reference_midnight_dt,
            'reference_utc_mysql'                => self::ds_dt_to_mysql_utc( $ref_dt ),
            'anchor_date_local'                  => (string) $cfg['anchor_date_local'],
            'anchor_phase'                       => (string) $cfg['anchor_phase'],
            'anchor_day'                         => (int) $cfg['anchor_day'],
            'anchor_phase_start_local'           => $anchor_phase_start_local,
            'anchor_phase_start_local_dt'        => $anchor_phase_start_dt,
            'cycle_active_start_local'           => $cycle_active_start_local,
            'cycle_active_start_local_dt'        => $cycle_active_start_dt,
            'active_duration'                    => $active_duration,
            'pause_duration'                     => $pause_duration,
            'cycle_length'                       => $cycle_length,
            'phase'                              => $phase,
            'is_active'                          => ( 'active' === $phase ),
            'is_paused'                          => ( 'pause' === $phase ),
            'phase_day'                          => (int) $phase_day,
            'phase_length'                       => (int) $phase_length,
            'cycle_day_index'                    => (int) $cycle_day_index,
            'day_in_cycle'                       => (int) ( $cycle_day_index + 1 ),
            'current_phase_start_local'          => $current_phase_start_local,
            'current_phase_end_local_exclusive'  => $current_phase_end_local_exclusive,
            'current_phase_start_local_dt'       => self::ds_midnight( $current_phase_start_local, $tz ),
            'current_phase_end_local_dt'         => self::ds_midnight( $current_phase_end_local_exclusive, $tz ),
            'days_remaining_in_phase'            => (int) $days_remaining_in_phase,
            'days_until_phase_change'            => (int) $days_remaining_in_phase,
            'days_after_today_until_phase_change'=> (int) max( 0, $days_remaining_in_phase - 1 ),
            'days_until_resume'                  => (int) $days_until_resume,
            'next_active_start_local'            => $next_active_start_local,
            'next_active_start_local_dt'         => self::ds_midnight( $next_active_start_local, $tz ),
            'next_active_start_utc'              => self::ds_dt_to_mysql_utc( self::ds_midnight( $next_active_start_local, $tz ) ),
        );

        return $out;
    }

    /**
     * Resolve availability phase for an arbitrary datetime/timestamp-like input.
     *
     * Since the cycle is local-day based, this converts the input into the WP/site timezone first
     * and then resolves the local-date state.
     */
    public static function availability_cycle_state_at_datetime( $availability_raw_or_norm, $at = null, DateTimeZone $tz = null ) {
        $tz = self::ds_get_timezone( $tz );
        $dt = self::av_mixed_to_local_datetime( $at, $tz, true );
        if ( ! ( $dt instanceof DateTimeImmutable ) ) {
            return self::av_disabled_state( self::normalize_availability_cycle_config( $availability_raw_or_norm ) );
        }
        return self::availability_cycle_state_on_local_date( $availability_raw_or_norm, $dt, $tz );
    }

    /**
     * Convenience wrappers for callers that only need the active/paused boolean.
     */
    public static function availability_cycle_is_active_on_local_date( $availability_raw_or_norm, $local_ymd = null, DateTimeZone $tz = null ) {
        $state = self::availability_cycle_state_on_local_date( $availability_raw_or_norm, $local_ymd, $tz );
        return ! empty( $state['enabled'] ) && ! empty( $state['is_active'] );
    }

    public static function availability_cycle_is_paused_on_local_date( $availability_raw_or_norm, $local_ymd = null, DateTimeZone $tz = null ) {
        $state = self::availability_cycle_state_on_local_date( $availability_raw_or_norm, $local_ymd, $tz );
        return ! empty( $state['enabled'] ) && ! empty( $state['is_paused'] );
    }

    /**
     * Returns the first active local midnight on or after the reference instant.
     */
    public static function availability_cycle_next_active_start( $availability_raw_or_norm, $at = null, DateTimeZone $tz = null ) {
        $state = self::availability_cycle_state_at_datetime( $availability_raw_or_norm, $at, $tz );
        if ( empty( $state['enabled'] ) || ! ( $state['next_active_start_local_dt'] instanceof DateTimeImmutable ) ) {
            return null;
        }
        return $state['next_active_start_local_dt'];
    }

    /**
     * UI-facing state-label context.
     *
     * Callers can either use the numeric args or the default strings.
     */
    public static function availability_cycle_state_label_context( $availability_raw_or_norm, $at = null, DateTimeZone $tz = null ) {
        $state = self::availability_cycle_state_at_datetime( $availability_raw_or_norm, $at, $tz );
        return self::av_state_label_context_from_state( $state );
    }

    /**
     * Contiguous availability segments overlapping [range_start, range_end) in local/site time.
     *
     * Phase boundaries always occur at local midnight, so this is exact for pause-overlap checks.
     */
    public static function availability_cycle_phase_segments_in_range( $availability_raw_or_norm, $range_start, $range_end, DateTimeZone $tz = null ) {
        $cfg = self::normalize_availability_cycle_config( $availability_raw_or_norm );
        $tz  = self::ds_get_timezone( $tz );

        $out = array(
            'enabled'                   => ! empty( $cfg['enabled'] ),
            'range_start_local'         => '',
            'range_end_local_exclusive' => '',
            'range_start_local_dt'      => null,
            'range_end_local_dt'        => null,
            'segments'                  => array(),
            'has_pause_overlap'         => false,
            'paused_day_count'          => 0,
            'active_day_count'          => 0,
        );

        $start_dt = self::av_mixed_to_local_datetime( $range_start, $tz, false );
        $end_dt   = self::av_mixed_to_local_datetime( $range_end, $tz, false );
        if ( ! ( $start_dt instanceof DateTimeImmutable ) || ! ( $end_dt instanceof DateTimeImmutable ) ) {
            return $out;
        }

        $range_start_midnight = $start_dt->setTimezone( $tz )->setTime( 0, 0, 0 );
        $range_end_exclusive_dt = self::av_local_range_end_exclusive_midnight( $end_dt, $tz );
        if ( $range_end_exclusive_dt->getTimestamp() <= $range_start_midnight->getTimestamp() ) {
            return $out;
        }

        $out['range_start_local']         = $range_start_midnight->format( 'Y-m-d' );
        $out['range_end_local_exclusive'] = $range_end_exclusive_dt->format( 'Y-m-d' );
        $out['range_start_local_dt']      = $range_start_midnight;
        $out['range_end_local_dt']        = $range_end_exclusive_dt;

        if ( empty( $cfg['enabled'] ) ) {
            return $out;
        }

        $cursor_local = $out['range_start_local'];
        $range_end_local = $out['range_end_local_exclusive'];

        while ( self::av_compare_ymd( $cursor_local, $range_end_local ) < 0 ) {
            $state = self::availability_cycle_state_on_local_date( $cfg, $cursor_local, $tz );
            if ( empty( $state['enabled'] ) ) {
                break;
            }

            $segment_end_local = isset( $state['current_phase_end_local_exclusive'] ) ? (string) $state['current_phase_end_local_exclusive'] : '';
            if ( ! self::ds_is_valid_ymd( $segment_end_local ) ) {
                break;
            }
            if ( self::av_compare_ymd( $segment_end_local, $range_end_local ) > 0 ) {
                $segment_end_local = $range_end_local;
            }

            $day_count = self::av_days_between_local_ymd( $cursor_local, $segment_end_local, $tz );
            if ( $day_count <= 0 ) {
                break;
            }

            $segment = array(
                'phase'               => isset( $state['phase'] ) ? (string) $state['phase'] : '',
                'is_active'           => ! empty( $state['is_active'] ),
                'is_paused'           => ! empty( $state['is_paused'] ),
                'start_local'         => $cursor_local,
                'end_local_exclusive' => $segment_end_local,
                'start_local_dt'      => self::ds_midnight( $cursor_local, $tz ),
                'end_local_dt'        => self::ds_midnight( $segment_end_local, $tz ),
                'day_count'           => (int) $day_count,
                'phase_day_start'     => isset( $state['phase_day'] ) ? (int) $state['phase_day'] : 0,
                'phase_day_end'       => isset( $state['phase_day'] ) ? (int) $state['phase_day'] + $day_count - 1 : 0,
                'phase_length'        => isset( $state['phase_length'] ) ? (int) $state['phase_length'] : 0,
            );
            $out['segments'][] = $segment;

            if ( ! empty( $segment['is_paused'] ) ) {
                $out['has_pause_overlap'] = true;
                $out['paused_day_count'] += $day_count;
            } else {
                $out['active_day_count'] += $day_count;
            }

            $cursor_local = $segment_end_local;
        }

        return $out;
    }

    public static function availability_cycle_has_pause_overlap_in_range( $availability_raw_or_norm, $range_start, $range_end, DateTimeZone $tz = null ) {
        $segments = self::availability_cycle_phase_segments_in_range( $availability_raw_or_norm, $range_start, $range_end, $tz );
        return ! empty( $segments['enabled'] ) && ! empty( $segments['has_pause_overlap'] );
    }

    public static function availability_cycle_paused_day_count_in_range( $availability_raw_or_norm, $range_start, $range_end, DateTimeZone $tz = null ) {
        $segments = self::availability_cycle_phase_segments_in_range( $availability_raw_or_norm, $range_start, $range_end, $tz );
        return isset( $segments['paused_day_count'] ) ? (int) $segments['paused_day_count'] : 0;
    }

    public static function availability_cycle_active_day_count_in_range( $availability_raw_or_norm, $range_start, $range_end, DateTimeZone $tz = null ) {
        $segments = self::availability_cycle_phase_segments_in_range( $availability_raw_or_norm, $range_start, $range_end, $tz );
        return isset( $segments['active_day_count'] ) ? (int) $segments['active_day_count'] : 0;
    }

    /**
     * Exact future due-schedule conflict detection.
     *
     * Weekly schedules use modular orbit math on the weekly period.
     * Monthly schedules use one 400-year Gregorian cycle plus modular class lifting,
     * which is exact for the repeating monthly recurrence.
     */
    public static function availability_cycle_due_schedule_conflict_details( $availability_raw_or_norm, $due_schedule_raw_or_norm, $from_local_ymd = null, DateTimeZone $tz = null ) {
        $tz       = self::ds_get_timezone( $tz );
        $cfg      = self::normalize_availability_cycle_config( $availability_raw_or_norm );
        $schedule = self::normalize_due_schedule_config( $due_schedule_raw_or_norm );
        $from     = self::av_mixed_to_local_ymd( $from_local_ymd, $tz, true );

        $out = array(
            'availability'             => $cfg,
            'due_schedule'             => $schedule,
            'checked'                  => false,
            'exact'                    => false,
            'has_conflict'             => false,
            'checked_from_local'       => $from,
            'first_due_local'          => '',
            'first_conflict_local'     => '',
            'first_conflict_state'     => null,
            'first_conflict_is_exact'  => false,
            'checked_occurrences'      => 0,
            'schedule_type'            => isset( $schedule['type'] ) ? (string) $schedule['type'] : '',
            'reason'                   => '',
        );

        if ( empty( $cfg['enabled'] ) ) {
            $out['reason'] = 'availability_disabled';
            return $out;
        }
        if ( empty( $schedule['enabled'] ) ) {
            $out['reason'] = 'due_schedule_disabled';
            return $out;
        }

        $cycle_length = self::av_cycle_length_from_cfg( $cfg );
        if ( $cycle_length <= 0 ) {
            $out['reason'] = 'availability_invalid';
            return $out;
        }

        $first_due = self::due_schedule_next_due_local_date( $schedule, $from, $tz );
        if ( ! self::ds_is_valid_ymd( $first_due ) ) {
            $out['reason'] = 'no_future_due';
            return $out;
        }

        $out['checked']         = true;
        $out['first_due_local'] = $first_due;

        if ( 'weekly' === $out['schedule_type'] ) {
            $period_days = 7 * (int) ( isset( $schedule['every'] ) ? $schedule['every'] : 1 );
            if ( $period_days < 1 ) {
                $period_days = 7;
            }
            $g = self::av_gcd( $cycle_length, $period_days );
            $first_state = self::availability_cycle_state_on_local_date( $cfg, $first_due, $tz );
            $out['exact'] = true;
            $out['checked_occurrences'] = 1;

            if ( ! empty( $first_state['is_paused'] ) ) {
                $out['has_conflict']            = true;
                $out['first_conflict_local']    = $first_due;
                $out['first_conflict_state']    = $first_state;
                $out['first_conflict_is_exact'] = true;
                return $out;
            }

            $residue = isset( $first_state['cycle_day_index'] ) ? (int) $first_state['cycle_day_index'] : -1;
            $class   = self::av_positive_mod( $residue, $g );
            $out['has_conflict'] = self::av_pause_range_contains_mod_class( $cfg, $g, $class );

            if ( $out['has_conflict'] ) {
                $orbit_size = (int) ( $cycle_length / max( 1, $g ) );
                $scan_limit = min( 4096, max( 1, $orbit_size ) );
                $cursor = $first_due;
                for ( $i = 0; $i < $scan_limit; $i++ ) {
                    $state = self::availability_cycle_state_on_local_date( $cfg, $cursor, $tz );
                    if ( ! empty( $state['is_paused'] ) ) {
                        $out['first_conflict_local']    = $cursor;
                        $out['first_conflict_state']    = $state;
                        $out['first_conflict_is_exact'] = ( $scan_limit >= $orbit_size );
                        break;
                    }
                    $cursor = self::av_add_days_ymd( $cursor, $period_days, $tz );
                }
                if ( '' === $out['first_conflict_local'] ) {
                    $out['reason'] = 'conflict_exists_future_weekly_orbit';
                }
            } else {
                $out['reason'] = 'no_conflict';
            }

            return $out;
        }

        if ( 'monthly' === $out['schedule_type'] ) {
            $g = self::av_gcd( $cycle_length, 146097 );
            $due_classes = array();
            $cursor = $first_due;
            $scan_limit = 4800; // one exact Gregorian 400-year recurrence cycle
            $out['exact'] = true;
            $out['checked_occurrences'] = $scan_limit;

            for ( $i = 0; $i < $scan_limit; $i++ ) {
                $state = self::availability_cycle_state_on_local_date( $cfg, $cursor, $tz );
                if ( ! empty( $state['is_paused'] ) ) {
                    $out['has_conflict']            = true;
                    $out['first_conflict_local']    = $cursor;
                    $out['first_conflict_state']    = $state;
                    $out['first_conflict_is_exact'] = true;
                    return $out;
                }

                if ( isset( $state['cycle_day_index'] ) ) {
                    $due_classes[ self::av_positive_mod( (int) $state['cycle_day_index'], $g ) ] = true;
                }

                $next_from = self::av_add_days_ymd( $cursor, 1, $tz );
                $next_due  = self::due_schedule_next_due_local_date( $schedule, $next_from, $tz );
                if ( ! self::ds_is_valid_ymd( $next_due ) || $next_due === $cursor ) {
                    break;
                }
                $cursor = $next_due;
            }

            foreach ( $due_classes as $class => $_true ) {
                if ( self::av_pause_range_contains_mod_class( $cfg, $g, (int) $class ) ) {
                    $out['has_conflict'] = true;
                    break;
                }
            }

            if ( $out['has_conflict'] && '' === $out['first_conflict_local'] ) {
                $out['reason'] = 'conflict_exists_future_monthly_orbit';
            } elseif ( ! $out['has_conflict'] ) {
                $out['reason'] = 'no_conflict';
            }

            return $out;
        }

        $out['reason'] = 'unsupported_schedule_type';
        return $out;
    }

    public static function availability_cycle_due_schedule_has_future_conflict( $availability_raw_or_norm, $due_schedule_raw_or_norm, $from_local_ymd = null, DateTimeZone $tz = null ) {
        $details = self::availability_cycle_due_schedule_conflict_details( $availability_raw_or_norm, $due_schedule_raw_or_norm, $from_local_ymd, $tz );
        return ! empty( $details['checked'] ) && ! empty( $details['has_conflict'] );
    }

    /**
     * Allowed Fails eligibility helpers (Chunk 3).
     *
     * "Exactly 1/day" means: target=1, unit=day, span=1 (calendar aligned).
     * Later chunks additionally gate by goal type (positive only) using is_goal_eligible_for_allowed_fails().
     */
    
    /**
     * Server-side eligibility gate for availability cycles.
     *
     * Availability is only meaningful for positive interval goals with a target.
     */
    public static function is_goal_eligible_for_availability_cycle( $goal_row ) {

        if ( ! is_array( $goal_row ) ) {
            return false;
        }

        $goal_type = isset( $goal_row['goal_type'] ) ? strtolower( trim( (string) $goal_row['goal_type'] ) ) : 'positive';
        if ( '' === $goal_type ) {
            $goal_type = 'positive';
        }

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_negative_goal_type' ) ) {
            if ( TCT_Utils::is_negative_goal_type( $goal_type ) ) {
                return false;
            }
        } elseif ( in_array( $goal_type, array( 'never', 'harm_reduction' ), true ) ) {
            return false;
        }

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) {
            if ( TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) {
                return false;
            }
        } elseif ( 'positive_no_int' === $goal_type ) {
            return false;
        }

        $interval = self::normalize_goal_interval_from_row( $goal_row );
        return ( is_array( $interval ) && ! empty( $interval['target'] ) );
    }

    /**
     * Classify a completion for availability-aware interval/vitality counting.
     *
     * Manual completions logged during pause are preserved and counted at the next
     * active instant. Non-manual paused completions are excluded from interval/vitality
     * counting, but their raw history remains unchanged.
     */
    public static function availability_cycle_completion_classification( $availability_raw_or_norm, $completed_at, $source = '', DateTimeZone $tz = null ) {

        $tz = self::ds_get_timezone( $tz );
        $cfg = self::normalize_availability_cycle_config( $availability_raw_or_norm );
        $raw_dt = self::av_mixed_to_local_datetime( $completed_at, $tz, false );

        $out = array(
            'enabled'            => ! empty( $cfg['enabled'] ),
            'counted'            => false,
            'counts_for_interval'=> false,
            'excluded'           => true,
            'excluded_reason'    => '',
            'source'             => is_string( $source ) ? strtolower( trim( (string) $source ) ) : '',
            'is_manual'          => false,
            'was_paused'         => false,
            'mapped_forward'     => false,
            'raw_local_dt'       => $raw_dt,
            'raw_utc_mysql'      => ( $raw_dt instanceof DateTimeImmutable ) ? self::ds_dt_to_mysql_utc( $raw_dt ) : '',
            'effective_local_dt' => null,
            'effective_local_ymd'=> '',
            'effective_utc_mysql'=> '',
            'effective_ts'       => 0,
            'state'              => null,
        );

        $out['is_manual'] = ( 'manual' === $out['source'] );

        if ( ! ( $raw_dt instanceof DateTimeImmutable ) ) {
            $out['excluded_reason'] = 'invalid_completed_at';
            return $out;
        }

        if ( empty( $cfg['enabled'] ) ) {
            $out['counted'] = true;
            $out['counts_for_interval'] = true;
            $out['excluded'] = false;
            $out['effective_local_dt'] = $raw_dt;
            $out['effective_local_ymd'] = $raw_dt->format( 'Y-m-d' );
            $out['effective_utc_mysql'] = self::ds_dt_to_mysql_utc( $raw_dt );
            $out['effective_ts'] = (int) $raw_dt->getTimestamp();
            return $out;
        }

        $state = self::availability_cycle_state_at_datetime( $cfg, $raw_dt, $tz );
        $out['state'] = $state;
        $out['was_paused'] = ! empty( $state['is_paused'] );

        if ( ! empty( $state['is_paused'] ) ) {
            if ( ! $out['is_manual'] ) {
                $out['excluded_reason'] = 'paused_non_manual';
                return $out;
            }

            $next_active_dt = self::availability_cycle_next_active_start( $cfg, $raw_dt, $tz );
            if ( ! ( $next_active_dt instanceof DateTimeImmutable ) ) {
                $out['excluded_reason'] = 'missing_next_active_start';
                return $out;
            }

            $out['counted'] = true;
            $out['counts_for_interval'] = true;
            $out['excluded'] = false;
            $out['mapped_forward'] = true;
            $out['effective_local_dt'] = $next_active_dt;
            $out['effective_local_ymd'] = $next_active_dt->format( 'Y-m-d' );
            $out['effective_utc_mysql'] = self::ds_dt_to_mysql_utc( $next_active_dt );
            $out['effective_ts'] = (int) $next_active_dt->getTimestamp();
            return $out;
        }

        $out['counted'] = true;
        $out['counts_for_interval'] = true;
        $out['excluded'] = false;
        $out['effective_local_dt'] = $raw_dt;
        $out['effective_local_ymd'] = $raw_dt->format( 'Y-m-d' );
        $out['effective_utc_mysql'] = self::ds_dt_to_mysql_utc( $raw_dt );
        $out['effective_ts'] = (int) $raw_dt->getTimestamp();

        return $out;
    }

    /**
     * Count paused seconds inside a real-time range.
     *
     * Availability phases only change at local midnights, so a day-by-day walk keeps
     * the math exact while remaining easy to reuse in vitality/ledger flows.
     */
    public static function availability_cycle_pause_overlap_seconds_in_range( $availability_raw_or_norm, $range_start, $range_end, DateTimeZone $tz = null ) {

        $tz = self::ds_get_timezone( $tz );
        $cfg = self::normalize_availability_cycle_config( $availability_raw_or_norm );
        $start_dt = self::av_mixed_to_local_datetime( $range_start, $tz, false );
        $end_dt   = self::av_mixed_to_local_datetime( $range_end, $tz, false );

        if ( ! ( $start_dt instanceof DateTimeImmutable ) || ! ( $end_dt instanceof DateTimeImmutable ) ) {
            return 0;
        }

        $start_dt = $start_dt->setTimezone( $tz );
        $end_dt   = $end_dt->setTimezone( $tz );
        if ( $end_dt->getTimestamp() <= $start_dt->getTimestamp() ) {
            return 0;
        }

        if ( empty( $cfg['enabled'] ) ) {
            return 0;
        }

        $paused_seconds = 0;
        $cursor = $start_dt;
        $guard = 0;
        while ( $cursor->getTimestamp() < $end_dt->getTimestamp() && $guard < 10000 ) {
            $state = self::availability_cycle_state_on_local_date( $cfg, $cursor, $tz );
            $day_start = $cursor->setTimezone( $tz )->setTime( 0, 0, 0 );
            $next_midnight = $day_start->modify( '+1 day' )->setTime( 0, 0, 0 );
            $slice_end = self::min_dt( $next_midnight, $end_dt );
            if ( ! ( $slice_end instanceof DateTimeImmutable ) || $slice_end->getTimestamp() <= $cursor->getTimestamp() ) {
                break;
            }
            if ( ! empty( $state['is_paused'] ) ) {
                $paused_seconds += max( 0, (int) ( $slice_end->getTimestamp() - $cursor->getTimestamp() ) );
            }
            $cursor = $slice_end;
            $guard++;
        }

        return max( 0, (int) $paused_seconds );
    }

    /**
     * Active seconds inside a real-time range (real elapsed minus paused overlap).
     */
    public static function availability_cycle_active_seconds_in_range( $availability_raw_or_norm, $range_start, $range_end, DateTimeZone $tz = null ) {

        $tz = self::ds_get_timezone( $tz );
        $start_dt = self::av_mixed_to_local_datetime( $range_start, $tz, false );
        $end_dt   = self::av_mixed_to_local_datetime( $range_end, $tz, false );

        if ( ! ( $start_dt instanceof DateTimeImmutable ) || ! ( $end_dt instanceof DateTimeImmutable ) ) {
            return 0;
        }

        $total = (int) ( $end_dt->getTimestamp() - $start_dt->getTimestamp() );
        if ( $total <= 0 ) {
            return 0;
        }

        $paused = self::availability_cycle_pause_overlap_seconds_in_range( $availability_raw_or_norm, $start_dt, $end_dt, $tz );
        $active = $total - (int) $paused;
        if ( $active < 0 ) {
            $active = 0;
        }

        return (int) $active;
    }

    /**
     * Extend an interval end until the window contains the requested amount of active time.
     */
    public static function availability_cycle_effective_end_for_nominal_active_seconds( $availability_raw_or_norm, $start, $nominal_active_seconds, DateTimeZone $tz = null ) {

        $tz = self::ds_get_timezone( $tz );
        $cfg = self::normalize_availability_cycle_config( $availability_raw_or_norm );
        $start_dt = self::av_mixed_to_local_datetime( $start, $tz, false );
        $nominal_active_seconds = (int) $nominal_active_seconds;
        if ( $nominal_active_seconds < 1 ) {
            $nominal_active_seconds = 1;
        }

        if ( ! ( $start_dt instanceof DateTimeImmutable ) ) {
            return null;
        }

        $candidate = $start_dt->modify( '+' . $nominal_active_seconds . ' seconds' );
        if ( empty( $cfg['enabled'] ) ) {
            return $candidate;
        }

        for ( $i = 0; $i < 2048; $i++ ) {
            $paused = self::availability_cycle_pause_overlap_seconds_in_range( $cfg, $start_dt, $candidate, $tz );
            $next = $start_dt->modify( '+' . ( (int) $nominal_active_seconds + (int) $paused ) . ' seconds' );
            if ( $next->getTimestamp() === $candidate->getTimestamp() ) {
                return $candidate;
            }
            $candidate = $next;
        }

        return $candidate;
    }

    /**
     * Resolve the current sequential interval loop after availability-based pause freezing.
     *
     * Each calendar interval contributes its nominal duration, but pauses extend the real
     * window end and shift later loops forward in sequence.
     */
    public static function availability_cycle_current_loop_context( $goal_row, DateTimeImmutable $now_tz, $options = array() ) {

        $tz = $now_tz->getTimezone();
        $cfg = self::normalize_availability_cycle_from_row( $goal_row );
        $interval = self::normalize_goal_interval_from_row( $goal_row );
        $goal_type = is_array( $goal_row ) && isset( $goal_row['goal_type'] ) ? strtolower( trim( (string) $goal_row['goal_type'] ) ) : 'positive';
        $state_now = self::availability_cycle_state_at_datetime( $cfg, $now_tz, $tz );

        $out = array(
            'enabled'         => false,
            'reason'          => '',
            'goal_type'       => $goal_type,
            'availability'    => $cfg,
            'interval'        => $interval,
            'timezone'        => $tz,
            'state_now'       => $state_now,
            'created_anchor'  => null,
            'loops_traversed' => 0,
            'current'         => null,
            'previous'        => null,
        );

        if ( empty( $cfg['enabled'] ) ) {
            $out['reason'] = 'availability_disabled';
            return $out;
        }

        if ( ! self::is_goal_eligible_for_availability_cycle( $goal_row ) || ! is_array( $interval ) ) {
            $out['reason'] = 'goal_ineligible';
            return $out;
        }

        $created_anchor = self::av_goal_created_anchor_local_dt( $goal_row, $tz, $now_tz );
        if ( ! ( $created_anchor instanceof DateTimeImmutable ) ) {
            $created_anchor = $now_tz;
        }
        if ( $created_anchor->getTimestamp() > $now_tz->getTimestamp() ) {
            $created_anchor = $now_tz;
        }
        $out['created_anchor'] = $created_anchor;

        $unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : 'week';
        $span = isset( $interval['period_span'] ) ? (int) $interval['period_span'] : 1;
        if ( $span < 1 ) {
            $span = 1;
        }

        $base_bounds = self::goal_interval_loop_bounds_at_datetime( $goal_row, $created_anchor, $options );
        if ( ! is_array( $base_bounds ) || ! isset( $base_bounds['start'], $base_bounds['end'] ) || ! ( $base_bounds['start'] instanceof DateTimeImmutable ) || ! ( $base_bounds['end'] instanceof DateTimeImmutable ) ) {
            $out['reason'] = 'base_bounds_invalid';
            return $out;
        }

        $actual_start = $base_bounds['start'];
        $base_start   = $base_bounds['start'];
        $base_end     = $base_bounds['end'];
        $previous     = null;
        $max_loops    = isset( $options['max_loops'] ) ? max( 1, (int) $options['max_loops'] ) : 20000;

        for ( $loop_index = 0; $loop_index < $max_loops; $loop_index++ ) {
            $nominal_seconds = max( 1, (int) ( $base_end->getTimestamp() - $base_start->getTimestamp() ) );
            $actual_end = self::availability_cycle_effective_end_for_nominal_active_seconds( $cfg, $actual_start, $nominal_seconds, $tz );
            if ( ! ( $actual_end instanceof DateTimeImmutable ) || $actual_end->getTimestamp() <= $actual_start->getTimestamp() ) {
                $actual_end = $actual_start->modify( '+' . $nominal_seconds . ' seconds' );
            }

            $loop = self::av_goal_loop_struct( $actual_start, $actual_end, $base_start, $base_end, $nominal_seconds, $now_tz, $cfg, $tz, $loop_index );
            if ( $now_tz->getTimestamp() < $actual_end->getTimestamp() || $loop_index === ( $max_loops - 1 ) ) {
                $out['enabled'] = true;
                $out['current'] = $loop;
                $out['previous'] = $previous;
                $out['loops_traversed'] = $loop_index;
                return $out;
            }

            $previous = $loop;
            $out['loops_traversed'] = $loop_index + 1;

            $probe = $base_end->modify( '+1 second' );
            $next_base = self::goal_interval_loop_bounds_at_datetime( $goal_row, $probe, $options );
            if ( ! is_array( $next_base ) || ! isset( $next_base['start'], $next_base['end'] ) || ! ( $next_base['start'] instanceof DateTimeImmutable ) || ! ( $next_base['end'] instanceof DateTimeImmutable ) ) {
                break;
            }

            $actual_start = $actual_end;
            $base_start   = $next_base['start'];
            $base_end     = $next_base['end'];
        }


        $out['reason'] = 'loop_guard_exhausted';
        return $out;
    }

    /**
     * Generate completed availability-aware interval windows whose effective end falls inside a range.
     *
     * These windows preserve the existing calendar unit/span semantics while freezing the clock during
     * paused time by extending the real loop end until the loop contains its full nominal active time.
     */
    public static function availability_cycle_completed_windows_for_range( $goal_row, $since_utc_mysql, $until_utc_mysql, DateTimeZone $tz = null, $options = array() ) {

        $tz = self::ds_get_timezone( $tz );
        if ( ! is_array( $goal_row ) ) {
            return array();
        }

        $cfg = self::normalize_availability_cycle_from_row( $goal_row );
        if ( empty( $cfg['enabled'] ) || ! self::is_goal_eligible_for_availability_cycle( $goal_row ) ) {
            return array();
        }

        $interval = self::normalize_goal_interval_from_row( $goal_row );
        if ( ! is_array( $interval ) ) {
            return array();
        }

        $since_dt = null;
        $until_dt = null;
        try {
            $since_dt = ( new DateTimeImmutable( (string) $since_utc_mysql, new DateTimeZone( 'UTC' ) ) )->setTimezone( $tz );
        } catch ( Exception $e ) {
            $since_dt = null;
        }
        try {
            $until_dt = ( new DateTimeImmutable( (string) $until_utc_mysql, new DateTimeZone( 'UTC' ) ) )->setTimezone( $tz );
        } catch ( Exception $e ) {
            $until_dt = null;
        }
        if ( ! ( $since_dt instanceof DateTimeImmutable ) || ! ( $until_dt instanceof DateTimeImmutable ) ) {
            return array();
        }
        if ( $until_dt->getTimestamp() <= $since_dt->getTimestamp() ) {
            return array();
        }

        $created_anchor = self::av_goal_created_anchor_local_dt( $goal_row, $tz, $until_dt );
        if ( ! ( $created_anchor instanceof DateTimeImmutable ) ) {
            $created_anchor = $since_dt;
        }
        if ( $created_anchor->getTimestamp() > $until_dt->getTimestamp() ) {
            $created_anchor = $until_dt;
        }

        $unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : 'week';
        $span = isset( $interval['period_span'] ) ? (int) $interval['period_span'] : 1;
        if ( $span < 1 ) {
            $span = 1;
        }

        $base_bounds = self::goal_interval_loop_bounds_at_datetime( $goal_row, $created_anchor, $options );
        if ( ! is_array( $base_bounds ) || ! isset( $base_bounds['start'], $base_bounds['end'] ) || ! ( $base_bounds['start'] instanceof DateTimeImmutable ) || ! ( $base_bounds['end'] instanceof DateTimeImmutable ) ) {
            return array();
        }

        $actual_start = $base_bounds['start'];
        $base_start   = $base_bounds['start'];
        $base_end     = $base_bounds['end'];
        $windows      = array();
        $max_loops    = isset( $options['max_loops'] ) ? max( 1, (int) $options['max_loops'] ) : 20000;

        for ( $loop_index = 0; $loop_index < $max_loops; $loop_index++ ) {
            $nominal_seconds = max( 1, (int) ( $base_end->getTimestamp() - $base_start->getTimestamp() ) );
            $actual_end = self::availability_cycle_effective_end_for_nominal_active_seconds( $cfg, $actual_start, $nominal_seconds, $tz );
            if ( ! ( $actual_end instanceof DateTimeImmutable ) || $actual_end->getTimestamp() <= $actual_start->getTimestamp() ) {
                $actual_end = $actual_start->modify( '+' . $nominal_seconds . ' seconds' );
            }

            if ( $actual_end->getTimestamp() > $since_dt->getTimestamp() && $actual_end->getTimestamp() <= $until_dt->getTimestamp() ) {
                $windows[] = self::av_goal_loop_struct( $actual_start, $actual_end, $base_start, $base_end, $nominal_seconds, $until_dt, $cfg, $tz, $loop_index );
            }

            if ( $actual_end->getTimestamp() > $until_dt->getTimestamp() ) {
                break;
            }

            $probe = $base_end->modify( '+1 second' );
            $next_base = self::goal_interval_loop_bounds_at_datetime( $goal_row, $probe, $options );
            if ( ! is_array( $next_base ) || ! isset( $next_base['start'], $next_base['end'] ) || ! ( $next_base['start'] instanceof DateTimeImmutable ) || ! ( $next_base['end'] instanceof DateTimeImmutable ) ) {
                break;
            }

            $actual_start = $actual_end;
            $base_start   = $next_base['start'];
            $base_end     = $next_base['end'];
            if ( $actual_start->getTimestamp() >= $until_dt->getTimestamp() ) {
                break;
            }
        }

        return $windows;
    }

    /**
     * Compute a pause-aware prorated target for a single availability-aware loop.
     */
    public static function availability_cycle_prorated_target_for_loop( $target, $anchor_ts, array $loop, $availability_raw_or_norm, DateTimeZone $tz = null ) {

        $tz = self::ds_get_timezone( $tz );
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

        $anchor_dt = null;
        try {
            $anchor_dt = ( new DateTimeImmutable( '@' . (int) $anchor_ts ) )->setTimezone( $tz );
        } catch ( Exception $e ) {
            $anchor_dt = null;
        }

        $loop_end_dt = ( isset( $loop['end'] ) && ( $loop['end'] instanceof DateTimeImmutable ) ) ? $loop['end'] : null;
        if ( ! ( $loop_end_dt instanceof DateTimeImmutable ) ) {
            try {
                $loop_end_dt = ( new DateTimeImmutable( '@' . (int) $loop_end_ts ) )->setTimezone( $tz );
            } catch ( Exception $e ) {
                $loop_end_dt = null;
            }
        }
        if ( ! ( $anchor_dt instanceof DateTimeImmutable ) || ! ( $loop_end_dt instanceof DateTimeImmutable ) ) {
            return $target;
        }

        $remaining_active_seconds = (int) self::availability_cycle_active_seconds_in_range( $availability_raw_or_norm, $anchor_dt, $loop_end_dt, $tz );
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

public static function is_interval_exactly_one_per_day( $interval ) {
        if ( ! is_array( $interval ) ) {
            return false;
        }
        $target = isset( $interval['target'] ) ? (int) $interval['target'] : 0;
        if ( 1 !== $target ) {
            return false;
        }

        $unit = 'week';
        if ( isset( $interval['period_unit'] ) ) {
            $unit = (string) $interval['period_unit'];
        } elseif ( isset( $interval['unit'] ) ) {
            $unit = (string) $interval['unit'];
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $unit = sanitize_text_field( $unit );
        }
        $unit = self::normalize_unit( $unit );

        $span = 1;
        if ( isset( $interval['period_span'] ) ) {
            $span = (int) $interval['period_span'];
        } elseif ( isset( $interval['span'] ) ) {
            $span = (int) $interval['span'];
        }
        $span = self::sanitize_span( $span );

        return ( 'day' === $unit && 1 === $span );
    }

    public static function is_goal_exactly_one_per_day_interval( $goal_row ) {
        $interval = self::normalize_goal_interval_from_row( $goal_row );
        return self::is_interval_exactly_one_per_day( $interval );
    }

    /**
     * Server-side eligibility gate for Allowed Fails.
     * This is the rule we will enforce in later chunks (upsert/fail/cron):
     *   - goal is positive (not never/harm_reduction, not positive_no_int)
     *   - goal interval is exactly 1/day
     */
    public static function is_goal_eligible_for_allowed_fails( $goal_row ) {
        if ( ! is_array( $goal_row ) ) {
            return false;
        }
        $goal_type = isset( $goal_row['goal_type'] ) ? strtolower( trim( (string) $goal_row['goal_type'] ) ) : '';
        if ( '' === $goal_type ) {
            $goal_type = 'positive';
        }

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_negative_goal_type' ) ) {
            if ( TCT_Utils::is_negative_goal_type( $goal_type ) ) {
                return false;
            }
        } else {
            if ( in_array( $goal_type, array( 'never', 'harm_reduction' ), true ) ) {
                return false;
            }
        }

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) {
            if ( TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) {
                return false;
            }
        } else {
            if ( 'positive_no_int' === $goal_type ) {
                return false;
            }
        }

        return self::is_goal_exactly_one_per_day_interval( $goal_row );
    }

    /**
     * Extract + sanitize allowed-fails config from a goal row (or returns disabled defaults).
     */
    public static function normalize_allowed_fails_config_from_row( $goal_row ) {
        $target = 0;
        $unit   = 'week';
        $span   = 1;

        if ( is_array( $goal_row ) ) {
            if ( isset( $goal_row['allowed_fails_target'] ) ) {
                $target = $goal_row['allowed_fails_target'];
            }
            if ( isset( $goal_row['allowed_fails_unit'] ) ) {
                $unit = $goal_row['allowed_fails_unit'];
            }
            if ( isset( $goal_row['allowed_fails_span'] ) ) {
                $span = $goal_row['allowed_fails_span'];
            }
        }

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'sanitize_allowed_fails_config' ) ) {
            return TCT_Utils::sanitize_allowed_fails_config( $target, $unit, $span );
        }

        $t = (int) $target;
        if ( $t < 0 ) { $t = 0; }
        if ( $t <= 0 ) {
            return array( 'target' => 0, 'unit' => 'week', 'span' => 1 );
        }

        $u = self::normalize_unit( is_string( $unit ) ? $unit : 'week' );
        if ( ! in_array( $u, array( 'week', 'month', 'year' ), true ) ) {
            $u = 'week';
        }

        $s = self::sanitize_span( $span );

        return array(
            'target' => $t,
            'unit'   => $u,
            'span'   => $s,
        );
    }

    /**
     * Current bounds for the "allowed fails window" (week/month/year), calendar aligned,
     * consistent with the Interval Entry Model.
     *
     * Returns [ 'start' => DateTimeImmutable, 'end' => DateTimeImmutable ] in the same timezone as $now_tz.
     */
    public static function current_allowed_fails_bounds( DateTimeImmutable $now_tz, $unit, $span = 1 ) {
        $u = $unit;
        $s = $span;

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'sanitize_allowed_fails_unit' ) ) {
            $u = TCT_Utils::sanitize_allowed_fails_unit( $u );
        } else {
            $u = self::normalize_unit( is_string( $u ) ? $u : 'week' );
            if ( ! in_array( $u, array( 'week', 'month', 'year' ), true ) ) {
                $u = 'week';
            }
        }

        if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'sanitize_allowed_fails_span' ) ) {
            $s = TCT_Utils::sanitize_allowed_fails_span( $s );
        } else {
            $s = self::sanitize_span( $s );
        }

        // Leverage current_loop_bounds() so week alignment respects start_of_week.
        return self::current_loop_bounds( $now_tz, $u, $s );
    }

    /**
     * Due Schedule engine (Chunk 2).
     *
     * A due schedule is stored as JSON in tct_goals.due_schedule_json.
     * This engine normalizes config and performs timezone-correct recurrence math
     * on *local* dates (midnight -> midnight in the WP/site timezone).
     *
     * Normalized shape:
     *   [
     *     'enabled'        => bool,
     *     'type'           => 'weekly'|'monthly',
     *     'start_date'     => 'YYYY-MM-DD' (local),
     *     'every'          => int (weekly: every N weeks),
     *     'day_of_month'   => int (monthly: day-of-month 1..31),
     *     'effective_from' => 'YYYY-MM-DD' (local) optional (forward-only edits),
     *   ]
     *
     * Invalid configs normalize to enabled=false (backwards compatible).
     */
    public static function normalize_due_schedule_config( $raw ) {
        $out = array(
            'enabled'        => false,
            'type'           => '',
            'start_date'     => '',
            'every'          => 1,
            'day_of_month'   => 1,
            'effective_from' => '',
        );

        if ( is_string( $raw ) ) {
            $raw = trim( $raw );
            if ( '' === $raw ) {
                return $out;
            }
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                $raw = $decoded;
            } else {
                return $out;
            }
        } elseif ( is_object( $raw ) ) {
            $raw = (array) $raw;
        }

        if ( ! is_array( $raw ) ) {
            return $out;
        }

        // enabled flag
        $enabled_val = null;
        if ( array_key_exists( 'enabled', $raw ) ) {
            $enabled_val = $raw['enabled'];
        } elseif ( array_key_exists( 'is_enabled', $raw ) ) {
            $enabled_val = $raw['is_enabled'];
        } elseif ( array_key_exists( 'on', $raw ) ) {
            $enabled_val = $raw['on'];
        } elseif ( array_key_exists( 'active', $raw ) ) {
            $enabled_val = $raw['active'];
        }
        $out['enabled'] = (bool) $enabled_val;

        // type
        $type = '';
        if ( isset( $raw['type'] ) ) {
            $type = (string) $raw['type'];
        } elseif ( isset( $raw['schedule_type'] ) ) {
            $type = (string) $raw['schedule_type'];
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $type = sanitize_text_field( $type );
        }
        $type = strtolower( trim( $type ) );
        $type_map = array(
            'week'   => 'weekly',
            'weekly' => 'weekly',
            'w'      => 'weekly',
            'month'  => 'monthly',
            'monthly'=> 'monthly',
            'm'      => 'monthly',
        );
        if ( isset( $type_map[ $type ] ) ) {
            $type = $type_map[ $type ];
        } else {
            $type = '';
        }
        $out['type'] = $type;

        // start_date (local YYYY-MM-DD)
        $start = '';
        if ( isset( $raw['start_date'] ) ) {
            $start = (string) $raw['start_date'];
        } elseif ( isset( $raw['start'] ) ) {
            $start = (string) $raw['start'];
        } elseif ( isset( $raw['dtstart'] ) ) {
            $start = (string) $raw['dtstart'];
        } elseif ( isset( $raw['start_ymd'] ) ) {
            $start = (string) $raw['start_ymd'];
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $start = sanitize_text_field( $start );
        }
        $start = trim( $start );
        if ( strlen( $start ) >= 10 ) {
            $start = substr( $start, 0, 10 );
        }
        if ( self::ds_is_valid_ymd( $start ) ) {
            $out['start_date'] = $start;
        }

        // effective_from (local YYYY-MM-DD)
        $eff = '';
        if ( isset( $raw['effective_from'] ) ) {
            $eff = (string) $raw['effective_from'];
        } elseif ( isset( $raw['effective_from_ymd'] ) ) {
            $eff = (string) $raw['effective_from_ymd'];
        } elseif ( isset( $raw['effective_from_local'] ) ) {
            $eff = (string) $raw['effective_from_local'];
        } elseif ( isset( $raw['effective_from_date'] ) ) {
            $eff = (string) $raw['effective_from_date'];
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $eff = sanitize_text_field( $eff );
        }
        $eff = trim( $eff );
        if ( strlen( $eff ) >= 10 ) {
            $eff = substr( $eff, 0, 10 );
        }
        if ( self::ds_is_valid_ymd( $eff ) ) {
            $out['effective_from'] = $eff;
        }

        if ( 'weekly' === $type ) {
            $every = 1;
            $every_keys = array( 'every', 'every_weeks', 'weekly_every', 'weekly_interval', 'interval_weeks', 'n' );
            foreach ( $every_keys as $k ) {
                if ( isset( $raw[ $k ] ) ) {
                    $every = (int) $raw[ $k ];
                    break;
                }
            }
            $out['every'] = self::sanitize_span( $every );
        } elseif ( 'monthly' === $type ) {
            $dom = 0;
            $dom_keys = array( 'day_of_month', 'dom', 'day', 'monthly_day' );
            foreach ( $dom_keys as $k ) {
                if ( isset( $raw[ $k ] ) ) {
                    $dom = (int) $raw[ $k ];
                    break;
                }
            }
            if ( $dom < 1 || $dom > 31 ) {
                if ( '' !== $out['start_date'] ) {
                    $dom = (int) substr( $out['start_date'], 8, 2 );
                } else {
                    $dom = 1;
                }
            }
            if ( $dom < 1 ) { $dom = 1; }
            if ( $dom > 31 ) { $dom = 31; }
            $out['day_of_month'] = $dom;
        }

        // If enabled but missing required fields, treat as disabled for safety.
        if ( $out['enabled'] ) {
            if ( '' === $out['type'] || '' === $out['start_date'] ) {
                $out['enabled'] = false;
            } else {
                // Default effective_from (forward-only) if missing/invalid.
                if ( ! self::ds_is_valid_ymd( $out['effective_from'] ) ) {
                    $tz = TCT_Utils::wp_timezone();
                    $today_ymd = ( new DateTimeImmutable( 'now', $tz ) )->format( 'Y-m-d' );
                    $out['effective_from'] = ( strcmp( $out['start_date'], $today_ymd ) > 0 ) ? $out['start_date'] : $today_ymd;
                }
                // Ensure effective_from is not earlier than start_date.
                if ( self::ds_is_valid_ymd( $out['effective_from'] ) && self::ds_is_valid_ymd( $out['start_date'] ) && strcmp( $out['effective_from'], $out['start_date'] ) < 0 ) {
                    $out['effective_from'] = $out['start_date'];
                }
            }
        }

        return $out;
    }

    /**
     * Convenience wrapper: extract + normalize due schedule config from a goal DB row.
     */
    public static function normalize_due_schedule_from_row( $goal_row ) {
        $raw = '';
        if ( is_array( $goal_row ) && array_key_exists( 'due_schedule_json', $goal_row ) ) {
            $raw = $goal_row['due_schedule_json'];
        }
        return self::normalize_due_schedule_config( $raw );
    }

    /**
     * Returns true if the schedule is due on the given local date (YYYY-MM-DD).
     *
     * NOTE: All calculations happen in the provided timezone (defaults to WP/site timezone).
     */
    public static function due_schedule_is_due_on_local_date( $schedule_raw_or_norm, $local_ymd, DateTimeZone $tz = null ) {
        $cfg = self::normalize_due_schedule_config( $schedule_raw_or_norm );
        if ( ! is_array( $cfg ) || empty( $cfg['enabled'] ) ) {
            return false;
        }

        $tz = self::ds_get_timezone( $tz );

        $date_dt = null;
        if ( $local_ymd instanceof DateTimeInterface ) {
            try {
                $date_dt = ( new DateTimeImmutable( '@' . (int) $local_ymd->getTimestamp() ) )->setTimezone( $tz )->setTime( 0, 0, 0 );
            } catch ( Exception $e ) {
                $date_dt = null;
            }
        } else {
            $local_ymd = is_string( $local_ymd ) ? trim( $local_ymd ) : '';
            if ( strlen( $local_ymd ) >= 10 ) {
                $local_ymd = substr( $local_ymd, 0, 10 );
            }
            if ( ! self::ds_is_valid_ymd( $local_ymd ) ) {
                return false;
            }
            $date_dt = self::ds_midnight( $local_ymd, $tz );
        }

        if ( ! ( $date_dt instanceof DateTimeImmutable ) ) {
            return false;
        }

        $start_dt = self::ds_midnight( $cfg['start_date'], $tz );
        if ( $date_dt->getTimestamp() < $start_dt->getTimestamp() ) {
            return false;
        }

        if ( ! empty( $cfg['effective_from'] ) && self::ds_is_valid_ymd( $cfg['effective_from'] ) ) {
            $eff_dt = self::ds_midnight( $cfg['effective_from'], $tz );
            if ( $date_dt->getTimestamp() < $eff_dt->getTimestamp() ) {
                return false;
            }
        }

        if ( 'weekly' === $cfg['type'] ) {
            $every = isset( $cfg['every'] ) ? (int) $cfg['every'] : 1;
            if ( $every < 1 ) { $every = 1; }
            $period_days = 7 * $every;
            $diff_days = self::ds_diff_days( $start_dt, $date_dt );
            return ( $diff_days >= 0 && 0 === ( $diff_days % $period_days ) );
        }

        if ( 'monthly' === $cfg['type'] ) {
            $dom = isset( $cfg['day_of_month'] ) ? (int) $cfg['day_of_month'] : 1;
            if ( $dom < 1 ) { $dom = 1; }
            if ( $dom > 31 ) { $dom = 31; }
            $y = (int) $date_dt->format( 'Y' );
            $m = (int) $date_dt->format( 'n' );
            $due_day = self::ds_month_due_day( $y, $m, $dom, $tz );
            $due_dt = $date_dt->setDate( $y, $m, $due_day )->setTime( 0, 0, 0 );
            return ( $due_dt->format( 'Y-m-d' ) === $date_dt->format( 'Y-m-d' ) );
        }

        return false;
    }

    /**
     * Returns the next due local date (YYYY-MM-DD) on/after $from_local_ymd (default: today),
     * or null if the schedule is disabled/invalid.
     *
     * NOTE: All calculations happen in the provided timezone (defaults to WP/site timezone).
     */
    public static function due_schedule_next_due_local_date( $schedule_raw_or_norm, $from_local_ymd = null, DateTimeZone $tz = null ) {
        $cfg = self::normalize_due_schedule_config( $schedule_raw_or_norm );
        if ( ! is_array( $cfg ) || empty( $cfg['enabled'] ) ) {
            return null;
        }

        $tz = self::ds_get_timezone( $tz );

        $from_dt = null;
        if ( $from_local_ymd instanceof DateTimeInterface ) {
            try {
                $from_dt = ( new DateTimeImmutable( '@' . (int) $from_local_ymd->getTimestamp() ) )->setTimezone( $tz )->setTime( 0, 0, 0 );
            } catch ( Exception $e ) {
                $from_dt = null;
            }
        } else {
            $from_s = is_string( $from_local_ymd ) ? trim( (string) $from_local_ymd ) : '';
            if ( strlen( $from_s ) >= 10 ) {
                $from_s = substr( $from_s, 0, 10 );
            }
            if ( '' === $from_s ) {
                $from_s = ( new DateTimeImmutable( 'now', $tz ) )->format( 'Y-m-d' );
            }
            if ( ! self::ds_is_valid_ymd( $from_s ) ) {
                $from_s = ( new DateTimeImmutable( 'now', $tz ) )->format( 'Y-m-d' );
            }
            $from_dt = self::ds_midnight( $from_s, $tz );
        }

        if ( ! ( $from_dt instanceof DateTimeImmutable ) ) {
            return null;
        }

        $start_dt = self::ds_midnight( $cfg['start_date'], $tz );

        // "effective_from" acts like a forward-only activation boundary (in local dates).
        if ( ! empty( $cfg['effective_from'] ) && self::ds_is_valid_ymd( $cfg['effective_from'] ) ) {
            $eff_dt = self::ds_midnight( $cfg['effective_from'], $tz );
            if ( $from_dt->getTimestamp() < $eff_dt->getTimestamp() ) {
                $from_dt = $eff_dt;
            }
        }

        // Never return a due date before the configured start date.
        if ( $from_dt->getTimestamp() < $start_dt->getTimestamp() ) {
            $from_dt = $start_dt;
        }

        if ( 'weekly' === $cfg['type'] ) {
            $every = isset( $cfg['every'] ) ? (int) $cfg['every'] : 1;
            if ( $every < 1 ) { $every = 1; }
            $period_days = 7 * $every;

            $diff_days = self::ds_diff_days( $start_dt, $from_dt );
            if ( $diff_days < 0 ) {
                return $start_dt->format( 'Y-m-d' );
            }
            $remainder = $diff_days % $period_days;
            $candidate = ( 0 === $remainder ) ? $from_dt : $from_dt->modify( '+' . ( $period_days - $remainder ) . ' days' );
            return $candidate->format( 'Y-m-d' );
        }

        if ( 'monthly' === $cfg['type'] ) {
            $dom = isset( $cfg['day_of_month'] ) ? (int) $cfg['day_of_month'] : 1;
            if ( $dom < 1 ) { $dom = 1; }
            if ( $dom > 31 ) { $dom = 31; }

            $y = (int) $from_dt->format( 'Y' );
            $m = (int) $from_dt->format( 'n' );

            $due_day = self::ds_month_due_day( $y, $m, $dom, $tz );
            $candidate = $from_dt->setDate( $y, $m, $due_day )->setTime( 0, 0, 0 );

            if ( $candidate->getTimestamp() < $from_dt->getTimestamp() ) {
                $next_month = $from_dt->modify( 'first day of next month' )->setTime( 0, 0, 0 );
                $y2 = (int) $next_month->format( 'Y' );
                $m2 = (int) $next_month->format( 'n' );
                $due_day2 = self::ds_month_due_day( $y2, $m2, $dom, $tz );
                $candidate = $next_month->setDate( $y2, $m2, $due_day2 )->setTime( 0, 0, 0 );
            }

            return $candidate->format( 'Y-m-d' );
        }

        return null;
    }

    /**
     * Returns UTC MySQL window for a given local date:
     *   [start_utc, end_utc) where local day is midnight -> midnight in WP/site timezone.
     *
     * Example return:
     *   [
     *     'start_utc'   => '2026-02-23 05:00:00',
     *     'end_utc'     => '2026-02-24 05:00:00',
     *     'start_local' => '2026-02-23 00:00:00',
     *     'end_local'   => '2026-02-24 00:00:00',
     *     'tz'          => 'America/New_York'
     *   ]
     */
    public static function due_schedule_local_day_window_utc_mysql( $local_ymd, DateTimeZone $tz = null ) {
        $tz = self::ds_get_timezone( $tz );

        $local_ymd = is_string( $local_ymd ) ? trim( $local_ymd ) : '';
        if ( strlen( $local_ymd ) >= 10 ) {
            $local_ymd = substr( $local_ymd, 0, 10 );
        }
        if ( ! self::ds_is_valid_ymd( $local_ymd ) ) {
            return null;
        }

        $start_local = self::ds_midnight( $local_ymd, $tz );
        $end_local   = $start_local->modify( '+1 day' )->setTime( 0, 0, 0 );

        return array(
            'start_utc'   => self::ds_dt_to_mysql_utc( $start_local ),
            'end_utc'     => self::ds_dt_to_mysql_utc( $end_local ),
            'start_local' => $start_local->format( 'Y-m-d H:i:s' ),
            'end_local'   => $end_local->format( 'Y-m-d H:i:s' ),
            'tz'          => $tz->getName(),
        );
    }

    /**
     * Debug logging helper for the Due Schedule engine.
     *
     * WP_DEBUG only; does not create new endpoints.
     *
     * Visit any page while logged in as an admin with:
     *   ?tct_due_schedule_debug=1
     *
     * Optionally provide a custom JSON payload:
     *   &tct_due_schedule_json={"enabled":true,"type":"weekly","start_date":"2026-02-24","every":2}
     */
    public static function maybe_log_due_schedule_debug() {
        if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
            return;
        }
        if ( ! isset( $_GET['tct_due_schedule_debug'] ) ) {
            return;
        }
        if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tz = self::ds_get_timezone( null );
        $now_tz = new DateTimeImmutable( 'now', $tz );
        $today = $now_tz->format( 'Y-m-d' );

        $raw = null;
        if ( isset( $_GET['tct_due_schedule_json'] ) ) {
            $raw = function_exists( 'wp_unslash' ) ? wp_unslash( $_GET['tct_due_schedule_json'] ) : stripslashes( (string) $_GET['tct_due_schedule_json'] );
        }

        $payloads = array();

        if ( is_string( $raw ) && '' !== trim( $raw ) ) {
            $payloads['custom'] = $raw;
        } else {
            // Sample: next Tuesday in local timezone.
            $next_tue = $now_tz->setTime( 0, 0, 0 );
            for ( $i = 0; $i < 14; $i++ ) {
                if ( 2 === (int) $next_tue->format( 'w' ) ) { // 0=Sun,1=Mon,2=Tue
                    break;
                }
                $next_tue = $next_tue->modify( '+1 day' );
            }
            $tue_ymd = $next_tue->format( 'Y-m-d' );

            // Sample: next 15th.
            $dom15 = 15;
            $cand = $now_tz->setTime( 0, 0, 0 );
            $y = (int) $cand->format( 'Y' );
            $m = (int) $cand->format( 'n' );
            $d_this = self::ds_month_due_day( $y, $m, $dom15, $tz );
            $this15 = $cand->setDate( $y, $m, $d_this )->setTime( 0, 0, 0 );
            if ( $this15->getTimestamp() < $cand->getTimestamp() ) {
                $n = $cand->modify( 'first day of next month' )->setTime( 0, 0, 0 );
                $y2 = (int) $n->format( 'Y' );
                $m2 = (int) $n->format( 'n' );
                $d2 = self::ds_month_due_day( $y2, $m2, $dom15, $tz );
                $this15 = $n->setDate( $y2, $m2, $d2 )->setTime( 0, 0, 0 );
            }

            $payloads['weekly_every1'] = array(
                'enabled'    => true,
                'type'       => 'weekly',
                'start_date' => $tue_ymd,
                'every'      => 1,
            );
            $payloads['weekly_every2'] = array(
                'enabled'    => true,
                'type'       => 'weekly',
                'start_date' => $tue_ymd,
                'every'      => 2,
            );
            $payloads['monthly_15'] = array(
                'enabled'       => true,
                'type'          => 'monthly',
                'start_date'    => $this15->format( 'Y-m-d' ),
                'day_of_month'  => 15,
            );
        }

        $results = array();
        foreach ( $payloads as $k => $p ) {
            $cfg = self::normalize_due_schedule_config( $p );
            $results[ $k ] = array(
                'cfg'      => $cfg,
                'today'    => $today,
                'is_due_today' => self::due_schedule_is_due_on_local_date( $cfg, $today, $tz ),
                'next_due' => self::due_schedule_next_due_local_date( $cfg, $today, $tz ),
                'today_window' => self::due_schedule_local_day_window_utc_mysql( $today, $tz ),
            );
        }

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
            TCT_Utils::debug_log( 'due_schedule_debug', array(
                'tz'   => $tz->getName(),
                'now'  => $now_tz->format( DateTimeInterface::ATOM ),
                'data' => $results,
            ) );
        } else {
            $json = function_exists( 'wp_json_encode' ) ? wp_json_encode( $results ) : json_encode( $results );
            error_log( 'TCT due_schedule_debug tz=' . $tz->getName() . ' now=' . $now_tz->format( DateTimeInterface::ATOM ) . ' ' . $json );
        }
    }

    // ---- Due Schedule private helpers ----

    private static function ds_get_timezone( $tz = null ) {
        if ( $tz instanceof DateTimeZone ) {
            return $tz;
        }
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ) {
            try {
                return TCT_Utils::wp_timezone();
            } catch ( Exception $e ) {
                // fall through
            }
        }
        if ( function_exists( 'wp_timezone' ) ) {
            try {
                return wp_timezone();
            } catch ( Exception $e ) {
                // fall through
            }
        }
        return new DateTimeZone( 'UTC' );
    }

    private static function ds_is_valid_ymd( $ymd ) {
        if ( ! is_string( $ymd ) ) {
            return false;
        }
        $ymd = trim( $ymd );
        if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $ymd ) ) {
            return false;
        }
        $parts = explode( '-', $ymd );
        if ( 3 !== count( $parts ) ) {
            return false;
        }
        $y = (int) $parts[0];
        $m = (int) $parts[1];
        $d = (int) $parts[2];
        return checkdate( $m, $d, $y );
    }

    private static function ds_midnight( $ymd, DateTimeZone $tz ) {
        try {
            return new DateTimeImmutable( $ymd . ' 00:00:00', $tz );
        } catch ( Exception $e ) {
            return new DateTimeImmutable( 'now', $tz );
        }
    }

    private static function ds_diff_days( DateTimeImmutable $start, DateTimeImmutable $end ) {
        $days = (int) $start->diff( $end )->format( '%r%a' );
        return $days;
    }

    private static function ds_month_due_day( $year, $month, $desired_dom, DateTimeZone $tz ) {
        $year = (int) $year;
        $month = (int) $month;
        $desired_dom = (int) $desired_dom;
        if ( $desired_dom < 1 ) { $desired_dom = 1; }
        if ( $desired_dom > 31 ) { $desired_dom = 31; }

        try {
            $first = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', $year, $month ), $tz );
            $days_in_month = (int) $first->format( 't' );
        } catch ( Exception $e ) {
            $days_in_month = 28;
        }

        if ( $days_in_month < 1 ) { $days_in_month = 28; }
        if ( $desired_dom > $days_in_month ) {
            return $days_in_month; // "last day of month" behavior
        }
        return $desired_dom;
    }

    private static function ds_dt_to_mysql_utc( DateTimeImmutable $dt ) {
        try {
            $utc = $dt->setTimezone( new DateTimeZone( 'UTC' ) );
            return $utc->format( 'Y-m-d H:i:s' );
        } catch ( Exception $e ) {
            return gmdate( 'Y-m-d H:i:s' );
        }
    }


    // ---- Availability cycle private helpers ----

    
    private static function av_goal_created_anchor_local_dt( $goal_row, DateTimeZone $tz, DateTimeImmutable $fallback ) {

        $created_raw = is_array( $goal_row ) && isset( $goal_row['created_at'] ) ? trim( (string) $goal_row['created_at'] ) : '';
        if ( '' !== $created_raw && '0000-00-00 00:00:00' !== $created_raw ) {
            try {
                return ( new DateTimeImmutable( $created_raw, new DateTimeZone( 'UTC' ) ) )->setTimezone( $tz );
            } catch ( Exception $e ) {
            }
        }

        return $fallback->setTimezone( $tz );
    }

    private static function av_goal_loop_struct( DateTimeImmutable $actual_start, DateTimeImmutable $actual_end, DateTimeImmutable $base_start, DateTimeImmutable $base_end, $nominal_seconds, DateTimeImmutable $now_tz, $cfg, DateTimeZone $tz, $loop_index ) {

        $nominal_seconds = max( 1, (int) $nominal_seconds );
        $start_ts = (int) $actual_start->getTimestamp();
        $end_ts   = (int) $actual_end->getTimestamp();
        $real_duration = max( 0, $end_ts - $start_ts );

        $active_elapsed = 0;
        if ( $now_tz->getTimestamp() <= $start_ts ) {
            $active_elapsed = 0;
        } elseif ( $now_tz->getTimestamp() >= $end_ts ) {
            $active_elapsed = $nominal_seconds;
        } else {
            $active_elapsed = (int) self::availability_cycle_active_seconds_in_range( $cfg, $actual_start, $now_tz, $tz );
        }

        if ( $active_elapsed < 0 ) {
            $active_elapsed = 0;
        }
        if ( $active_elapsed > $nominal_seconds ) {
            $active_elapsed = $nominal_seconds;
        }

        $active_remaining = $nominal_seconds - $active_elapsed;
        if ( $active_remaining < 0 ) {
            $active_remaining = 0;
        }

        $paused_seconds_total = $real_duration - $nominal_seconds;
        if ( $paused_seconds_total < 0 ) {
            $paused_seconds_total = 0;
        }

        return array(
            'loop_index'              => (int) $loop_index,
            'start'                   => $actual_start,
            'end'                     => $actual_end,
            'start_ts'                => $start_ts,
            'end_ts'                  => $end_ts,
            'start_utc_mysql'         => self::ds_dt_to_mysql_utc( $actual_start ),
            'end_utc_mysql'           => self::ds_dt_to_mysql_utc( $actual_end ),
            'base_start'              => $base_start,
            'base_end'                => $base_end,
            'base_start_ts'           => (int) $base_start->getTimestamp(),
            'base_end_ts'             => (int) $base_end->getTimestamp(),
            'base_start_utc_mysql'    => self::ds_dt_to_mysql_utc( $base_start ),
            'base_end_utc_mysql'      => self::ds_dt_to_mysql_utc( $base_end ),
            'nominal_active_seconds'  => $nominal_seconds,
            'real_duration_seconds'   => $real_duration,
            'paused_seconds_total'    => (int) $paused_seconds_total,
            'active_elapsed_seconds'  => (int) $active_elapsed,
            'active_remaining_seconds'=> (int) $active_remaining,
        );
    }

private static function av_boolish( $value ) {
        if ( is_bool( $value ) ) {
            return $value;
        }
        if ( is_int( $value ) || is_float( $value ) ) {
            return ( 0 !== (int) $value );
        }
        if ( is_string( $value ) ) {
            $v = strtolower( trim( $value ) );
            if ( '' === $v ) {
                return false;
            }
            if ( in_array( $v, array( '1', 'true', 'yes', 'y', 'on', 'enabled', 'enable' ), true ) ) {
                return true;
            }
            if ( in_array( $v, array( '0', 'false', 'no', 'n', 'off', 'disabled', 'disable' ), true ) ) {
                return false;
            }
            return true;
        }
        return ! empty( $value );
    }

    private static function av_cycle_length_from_cfg( $cfg ) {
        if ( is_array( $cfg ) && isset( $cfg['cycle_length'] ) ) {
            $cycle_length = (int) $cfg['cycle_length'];
            if ( $cycle_length > 0 ) {
                return $cycle_length;
            }
        }
        $active = ( is_array( $cfg ) && isset( $cfg['active_duration'] ) ) ? (int) $cfg['active_duration'] : 0;
        $pause  = ( is_array( $cfg ) && isset( $cfg['pause_duration'] ) ) ? (int) $cfg['pause_duration'] : 0;
        $cycle_length = $active + $pause;
        return ( $cycle_length > 0 ) ? (int) $cycle_length : 0;
    }

    private static function av_positive_mod( $value, $mod ) {
        $value = (int) $value;
        $mod   = (int) $mod;
        if ( $mod <= 0 ) {
            return 0;
        }
        $r = $value % $mod;
        if ( $r < 0 ) {
            $r += $mod;
        }
        return $r;
    }

    private static function av_gcd( $a, $b ) {
        $a = abs( (int) $a );
        $b = abs( (int) $b );
        if ( 0 === $a ) {
            return ( $b > 0 ) ? $b : 1;
        }
        if ( 0 === $b ) {
            return $a;
        }
        while ( 0 !== $b ) {
            $tmp = $a % $b;
            $a   = $b;
            $b   = $tmp;
        }
        return ( $a > 0 ) ? $a : 1;
    }

    private static function av_mixed_to_local_datetime( $value, DateTimeZone $tz, $default_to_now = false ) {
        $dt = null;

        if ( $value instanceof DateTimeInterface ) {
            try {
                $dt = ( new DateTimeImmutable( '@' . (int) $value->getTimestamp() ) )->setTimezone( $tz );
            } catch ( Exception $e ) {
                $dt = null;
            }
        } elseif ( is_int( $value ) || is_float( $value ) ) {
            try {
                $dt = ( new DateTimeImmutable( '@' . (int) $value ) )->setTimezone( $tz );
            } catch ( Exception $e ) {
                $dt = null;
            }
        } elseif ( is_string( $value ) ) {
            $value = trim( $value );
            if ( '' !== $value ) {
                if ( ctype_digit( $value ) ) {
                    try {
                        $dt = ( new DateTimeImmutable( '@' . (int) $value ) )->setTimezone( $tz );
                    } catch ( Exception $e ) {
                        $dt = null;
                    }
                }
                if ( ! ( $dt instanceof DateTimeImmutable ) ) {
                    try {
                        $dt = ( new DateTimeImmutable( $value, $tz ) )->setTimezone( $tz );
                    } catch ( Exception $e ) {
                        $dt = null;
                    }
                }
                if ( ! ( $dt instanceof DateTimeImmutable ) && strlen( $value ) >= 10 ) {
                    $ymd = substr( $value, 0, 10 );
                    if ( self::ds_is_valid_ymd( $ymd ) ) {
                        $dt = self::ds_midnight( $ymd, $tz );
                    }
                }
            }
        }

        if ( $dt instanceof DateTimeImmutable ) {
            return $dt->setTimezone( $tz );
        }

        if ( $default_to_now ) {
            try {
                return new DateTimeImmutable( 'now', $tz );
            } catch ( Exception $e ) {
                return null;
            }
        }

        return null;
    }

    private static function av_mixed_to_local_ymd( $value, DateTimeZone $tz, $default_to_today = false ) {
        $dt = self::av_mixed_to_local_datetime( $value, $tz, $default_to_today );
        if ( ! ( $dt instanceof DateTimeImmutable ) ) {
            return '';
        }
        return $dt->format( 'Y-m-d' );
    }

    private static function av_add_days_ymd( $ymd, $days, DateTimeZone $tz ) {
        if ( ! self::ds_is_valid_ymd( $ymd ) ) {
            return '';
        }
        $days = (int) $days;
        $dt   = self::ds_midnight( $ymd, $tz );
        if ( 0 !== $days ) {
            $dt = $dt->modify( ( $days > 0 ? '+' : '' ) . $days . ' days' );
        }
        return $dt->format( 'Y-m-d' );
    }

    private static function av_days_between_local_ymd( $start_ymd, $end_ymd, DateTimeZone $tz ) {
        if ( ! self::ds_is_valid_ymd( $start_ymd ) || ! self::ds_is_valid_ymd( $end_ymd ) ) {
            return 0;
        }
        return self::ds_diff_days( self::ds_midnight( $start_ymd, $tz ), self::ds_midnight( $end_ymd, $tz ) );
    }

    private static function av_compare_ymd( $a, $b ) {
        if ( ! self::ds_is_valid_ymd( $a ) && ! self::ds_is_valid_ymd( $b ) ) {
            return 0;
        }
        if ( ! self::ds_is_valid_ymd( $a ) ) {
            return -1;
        }
        if ( ! self::ds_is_valid_ymd( $b ) ) {
            return 1;
        }
        if ( $a === $b ) {
            return 0;
        }
        return ( strcmp( $a, $b ) < 0 ) ? -1 : 1;
    }

    private static function av_local_range_end_exclusive_midnight( DateTimeImmutable $end_dt, DateTimeZone $tz ) {
        $end_dt   = $end_dt->setTimezone( $tz );
        $midnight = $end_dt->setTime( 0, 0, 0 );
        if ( $end_dt->getTimestamp() === $midnight->getTimestamp() ) {
            return $midnight;
        }
        return $midnight->modify( '+1 day' )->setTime( 0, 0, 0 );
    }

    private static function av_pause_range_contains_mod_class( $cfg, $mod, $class ) {
        $mod = (int) $mod;
        if ( $mod <= 0 ) {
            return false;
        }

        $active = ( is_array( $cfg ) && isset( $cfg['active_duration'] ) ) ? (int) $cfg['active_duration'] : 0;
        $pause  = ( is_array( $cfg ) && isset( $cfg['pause_duration'] ) ) ? (int) $cfg['pause_duration'] : 0;
        if ( $pause <= 0 ) {
            return false;
        }
        if ( $pause >= $mod ) {
            return true;
        }

        $class       = self::av_positive_mod( $class, $mod );
        $active_mod  = self::av_positive_mod( $active, $mod );
        $delta       = self::av_positive_mod( $class - $active_mod, $mod );
        $first_match = $active + $delta;
        $pause_end   = $active + $pause - 1;

        return ( $first_match <= $pause_end );
    }

    private static function av_disabled_state( $cfg = array(), DateTimeImmutable $reference_dt = null, $reference_local_ymd = '' ) {
        if ( '' === $reference_local_ymd && $reference_dt instanceof DateTimeImmutable ) {
            $reference_local_ymd = $reference_dt->format( 'Y-m-d' );
        }
        $reference_midnight = null;
        if ( $reference_dt instanceof DateTimeImmutable ) {
            $reference_midnight = $reference_dt->setTime( 0, 0, 0 );
        }

        return array(
            'enabled'                            => false,
            'reference_local_date'               => self::ds_is_valid_ymd( $reference_local_ymd ) ? $reference_local_ymd : '',
            'reference_local_datetime'           => $reference_dt,
            'reference_local_midnight'           => $reference_midnight,
            'reference_utc_mysql'                => ( $reference_dt instanceof DateTimeImmutable ) ? self::ds_dt_to_mysql_utc( $reference_dt ) : '',
            'anchor_date_local'                  => ( is_array( $cfg ) && isset( $cfg['anchor_date_local'] ) ) ? (string) $cfg['anchor_date_local'] : '',
            'anchor_phase'                       => ( is_array( $cfg ) && isset( $cfg['anchor_phase'] ) ) ? (string) $cfg['anchor_phase'] : '',
            'anchor_day'                         => ( is_array( $cfg ) && isset( $cfg['anchor_day'] ) ) ? (int) $cfg['anchor_day'] : 0,
            'anchor_phase_start_local'           => '',
            'anchor_phase_start_local_dt'        => null,
            'cycle_active_start_local'           => '',
            'cycle_active_start_local_dt'        => null,
            'active_duration'                    => ( is_array( $cfg ) && isset( $cfg['active_duration'] ) ) ? (int) $cfg['active_duration'] : 0,
            'pause_duration'                     => ( is_array( $cfg ) && isset( $cfg['pause_duration'] ) ) ? (int) $cfg['pause_duration'] : 0,
            'cycle_length'                       => self::av_cycle_length_from_cfg( $cfg ),
            'phase'                              => '',
            'is_active'                          => false,
            'is_paused'                          => false,
            'phase_day'                          => 0,
            'phase_length'                       => 0,
            'cycle_day_index'                    => -1,
            'day_in_cycle'                       => 0,
            'current_phase_start_local'          => '',
            'current_phase_end_local_exclusive'  => '',
            'current_phase_start_local_dt'       => null,
            'current_phase_end_local_dt'         => null,
            'days_remaining_in_phase'            => 0,
            'days_until_phase_change'            => 0,
            'days_after_today_until_phase_change'=> 0,
            'days_until_resume'                  => 0,
            'next_active_start_local'            => '',
            'next_active_start_local_dt'         => null,
            'next_active_start_utc'              => '',
        );
    }

    private static function av_day_word( $days ) {
        return ( 1 === (int) $days ) ? 'day' : 'days';
    }

    private static function av_state_label_context_from_state( $state ) {
        $out = array(
            'enabled'             => ! empty( $state['enabled'] ),
            'phase'               => isset( $state['phase'] ) ? (string) $state['phase'] : '',
            'label_key'           => '',
            'label_args'          => array(),
            'default_label'       => '',
            'default_meta_label'  => '',
        );

        if ( empty( $state['enabled'] ) ) {
            return $out;
        }

        $phase_day    = isset( $state['phase_day'] ) ? (int) $state['phase_day'] : 0;
        $phase_length = isset( $state['phase_length'] ) ? (int) $state['phase_length'] : 0;

        if ( ! empty( $state['is_active'] ) ) {
            $days = isset( $state['days_until_phase_change'] ) ? (int) $state['days_until_phase_change'] : 0;
            $out['label_key'] = 'active_day_of_phase';
            $out['label_args'] = array(
                'phase_day'               => $phase_day,
                'phase_length'            => $phase_length,
                'days_until_phase_change' => $days,
                'next_phase_start_local'  => isset( $state['current_phase_end_local_exclusive'] ) ? (string) $state['current_phase_end_local_exclusive'] : '',
            );
            $out['default_label']      = sprintf( 'Active - day %d of %d', $phase_day, $phase_length );
            $out['default_meta_label'] = sprintf( 'Pauses in %d %s', $days, self::av_day_word( $days ) );
            return $out;
        }

        $days = isset( $state['days_until_resume'] ) ? (int) $state['days_until_resume'] : 0;
        $out['label_key'] = 'paused_resume_in_days';
        $out['label_args'] = array(
            'phase_day'          => $phase_day,
            'phase_length'       => $phase_length,
            'days_until_resume'  => $days,
            'next_active_start_local' => isset( $state['next_active_start_local'] ) ? (string) $state['next_active_start_local'] : '',
        );
        $out['default_label']      = sprintf( 'Paused - resumes in %d %s', $days, self::av_day_word( $days ) );
        $out['default_meta_label'] = sprintf( 'Pause day %d of %d', $phase_day, $phase_length );
        return $out;
    }




 private static function interval_anchor_shift_start( DateTimeImmutable $start_dt, $unit, $span, $steps = 1 ) { $unit = self::normalize_unit( $unit ); $span = self::sanitize_span( $span ); $steps = (int) $steps; if ( 0 === $steps ) { return $start_dt; } switch ( $unit ) { case 'day': return $start_dt->modify( ( $steps * $span ) . ' days' ); case 'week': return $start_dt->modify( ( $steps * $span * 7 ) . ' days' ); case 'quarter': return self::ia_add_months_clamped( $start_dt, $steps * $span * 3 ); case 'semiannual': return self::ia_add_months_clamped( $start_dt, $steps * $span * 6 ); case 'month': return self::ia_add_months_clamped( $start_dt, $steps * $span ); case 'year': return self::ia_add_months_clamped( $start_dt, $steps * $span * 12 ); case 'hour': default: return $start_dt->modify( ( $steps * $span ) . ' hours' ); } } private static function ia_add_months_clamped( DateTimeImmutable $dt, $months ) { $months = (int) $months; if ( 0 === $months ) { return $dt; } $year = (int) $dt->format( 'Y' ); $month = (int) $dt->format( 'n' ); $day = (int) $dt->format( 'j' ); $hour = (int) $dt->format( 'H' ); $minute = (int) $dt->format( 'i' ); $second = (int) $dt->format( 's' ); $total = ( $year * 12 ) + ( $month - 1 ) + $months; $new_year = (int) floor( $total / 12 ); $new_month_index = $total - ( $new_year * 12 ); if ( $new_month_index < 0 ) { $new_year--; $new_month_index += 12; } $new_month = $new_month_index + 1; $max_day = self::ia_days_in_month( $new_year, $new_month ); if ( $day > $max_day ) { $day = $max_day; } return $dt->setDate( $new_year, $new_month, $day )->setTime( $hour, $minute, $second ); } private static function ia_days_in_month( $year, $month ) { $year = (int) $year; $month = (int) $month; if ( function_exists( 'cal_days_in_month' ) ) { return (int) cal_days_in_month( CAL_GREGORIAN, $month, $year ); } $tz = new DateTimeZone( 'UTC' ); $probe = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', $year, $month ), $tz ); return (int) $probe->format( 't' ); } public static function normalize_unit( $unit ) { $u = is_string( $unit ) ? strtolower( trim( $unit ) ) : ''; if ( '' === $u ) { return 'week'; } $map = array( 'hours' => 'hour', 'daily' => 'day', 'days' => 'day', 'weekly' => 'week', 'weeks' => 'week', 'monthly' => 'month', 'months' => 'month', 'quarterly' => 'quarter', 'quarters' => 'quarter', 'semi-annual'=> 'semiannual', 'semiannual' => 'semiannual', 'semiannually' => 'semiannual', 'halfyear' => 'semiannual', 'half-year' => 'semiannual', 'annual' => 'year', 'annually' => 'year', 'yearly' => 'year', 'years' => 'year', ); if ( isset( $map[ $u ] ) ) { $u = $map[ $u ]; } if ( ! in_array( $u, array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ), true ) ) { $u = 'week'; } return $u; } private static function min_dt( DateTimeImmutable $a, DateTimeImmutable $b ) { return ( $a->getTimestamp() <= $b->getTimestamp() ) ? $a : $b; } private static function start_of_current_week( DateTimeImmutable $now_tz, $start_of_week ) { $start_of_week = (int) $start_of_week; if ( $start_of_week < 0 || $start_of_week > 6 ) { $start_of_week = 1; } $dow = (int) $now_tz->format( 'w' ); $diff = ( $dow - $start_of_week + 7 ) % 7; return $now_tz->modify( '-' . $diff . ' days' )->setTime( 0, 0, 0 ); } private static function week1_start( $year, DateTimeZone $tz, $start_of_week ) { $year = (int) $year; $start_of_week = (int) $start_of_week; if ( $start_of_week < 0 || $start_of_week > 6 ) { $start_of_week = 1; } $jan1 = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', $year ), $tz ); $dow = (int) $jan1->format( 'w' ); $delta = ( $dow - $start_of_week + 7 ) % 7; return $jan1->modify( '-' . $delta . ' days' )->setTime( 0, 0, 0 ); } public static function current_loop_bounds( DateTimeImmutable $now_tz, $unit, $span = 1 ) { $unit = self::normalize_unit( $unit ); $span = self::sanitize_span( $span ); $tz = $now_tz->getTimezone(); $year = (int) $now_tz->format( 'Y' ); $month = (int) $now_tz->format( 'n' ); $day = (int) $now_tz->format( 'j' ); $start = null; $end = null; switch ( $unit ) { case 'hour': $day_start = $now_tz->setTime( 0, 0, 0 ); $next_day_start = $day_start->modify( '+1 day' )->setTime( 0, 0, 0 ); $seconds_since = (int) ( $now_tz->getTimestamp() - $day_start->getTimestamp() ); if ( $seconds_since < 0 ) { $seconds_since = 0; } $block_seconds = $span * HOUR_IN_SECONDS; $block_index = (int) floor( $seconds_since / $block_seconds ); if ( $block_index < 0 ) { $block_index = 0; } $start = $day_start->modify( '+' . ( $block_index * $span ) . ' hours' ); $end_candidate = $start->modify( '+' . $span . ' hours' ); $end = self::min_dt( $end_candidate, $next_day_start ); break; case 'day': $block_start_day = 1 + (int) floor( ( $day - 1 ) / $span ) * $span; $month_start = $now_tz->modify( 'first day of this month' )->setTime( 0, 0, 0 ); $next_month_start = $month_start->modify( 'first day of next month' )->setTime( 0, 0, 0 ); $start = $now_tz->setDate( $year, $month, $block_start_day )->setTime( 0, 0, 0 ); $end_candidate = $start->modify( '+' . $span . ' days' ); $end = self::min_dt( $end_candidate, $next_month_start ); break; case 'week': $start_of_week = (int) get_option( 'start_of_week', 1 ); if ( $start_of_week < 0 || $start_of_week > 6 ) { $start_of_week = 1; } $W = self::start_of_current_week( $now_tz, $start_of_week ); $Y = (int) $W->format( 'Y' ); $S = self::week1_start( $Y, $tz, $start_of_week ); $S_next = self::week1_start( $Y + 1, $tz, $start_of_week ); if ( $W->getTimestamp() < $S->getTimestamp() ) { $S = self::week1_start( $Y - 1, $tz, $start_of_week ); $S_next = self::week1_start( $Y, $tz, $start_of_week ); } $days_diff = (int) $S->diff( $W )->format( '%r%a' ); if ( $days_diff < 0 ) { $days_diff = 0; } $week_index = 1 + (int) floor( $days_diff / 7 ); if ( $week_index < 1 ) { $week_index = 1; } $block_index = (int) floor( ( $week_index - 1 ) / $span ); if ( $block_index < 0 ) { $block_index = 0; } $weeks_to_add = $block_index * $span; $start = $S->modify( '+' . $weeks_to_add . ' weeks' ); $end_candidate = $start->modify( '+' . $span . ' weeks' ); $end = self::min_dt( $end_candidate, $S_next ); break; case 'quarter': $unit = 'month'; $span = $span * 3; case 'semiannual': if ( 'semiannual' === $unit ) { $unit = 'month'; $span = $span * 6; } case 'month': $block_start_month = 1 + (int) floor( ( $month - 1 ) / $span ) * $span; $start = $now_tz->setDate( $year, $block_start_month, 1 )->setTime( 0, 0, 0 ); $next_year_start = $now_tz->setDate( $year + 1, 1, 1 )->setTime( 0, 0, 0 ); $end_candidate = $start->modify( '+' . $span . ' months' ); $end = self::min_dt( $end_candidate, $next_year_start ); break; case 'year': default: $mod = $year % $span; $block_start_year = $year - $mod; $start = $now_tz->setDate( $block_start_year, 1, 1 )->setTime( 0, 0, 0 ); $end = $start->modify( '+' . $span . ' years' ); break; } if ( ! ( $start instanceof DateTimeImmutable ) ) { $start = new DateTimeImmutable( $now_tz->format( 'Y-m-d 00:00:00' ), $tz ); } if ( ! ( $end instanceof DateTimeImmutable ) ) { $end = $start->modify( '+1 day' ); } $start = $start->setTimezone( $tz ); $end = $end->setTimezone( $tz ); return array( 'start' => $start, 'end' => $end, ); } public static function clamp01( $x ) { $x = (float) $x; if ( $x < 0.0 ) { return 0.0; } if ( $x > 1.0 ) { return 1.0; } return $x; }

    /**
     * Composite goal settlement helpers (Chunk 3).
     *
     * Parents settle against child-native interval windows, but eligibility is
     * decided from the child availability state at the settlement boundary.
     */
    public static function composite_settlement_reference_datetime( $settlement_at = null, DateTimeZone $tz = null, $options = array() ) {
        $tz = self::ds_get_timezone( $tz );
        $dt = self::av_mixed_to_local_datetime( $settlement_at, $tz, true );
        if ( ! ( $dt instanceof DateTimeImmutable ) ) {
            $dt = new DateTimeImmutable( 'now', $tz );
        }

        $shift_seconds = isset( $options['boundary_reference_seconds'] ) ? (int) $options['boundary_reference_seconds'] : 1;
        if ( $shift_seconds < 0 ) {
            $shift_seconds = 0;
        }
        if ( $shift_seconds > DAY_IN_SECONDS ) {
            $shift_seconds = DAY_IN_SECONDS;
        }

        if ( $shift_seconds > 0 ) {
            $dt = $dt->modify( '-' . $shift_seconds . ' seconds' );
        }

        return $dt->setTimezone( $tz );
    }

    public static function composite_child_settlement_context( $goal_row, $settlement_at = null, DateTimeZone $tz = null, $options = array() ) {
        $goal_row = is_array( $goal_row ) ? $goal_row : array();
        $tz = self::ds_get_timezone( $tz );
        $settlement_dt = self::av_mixed_to_local_datetime( $settlement_at, $tz, true );
        if ( ! ( $settlement_dt instanceof DateTimeImmutable ) ) {
            $settlement_dt = new DateTimeImmutable( 'now', $tz );
        }
        $settlement_dt = $settlement_dt->setTimezone( $tz );
        $reference_dt = self::composite_settlement_reference_datetime( $settlement_dt, $tz, $options );

        $interval = self::normalize_goal_interval_from_row( $goal_row );
        $availability = self::normalize_availability_cycle_from_row( $goal_row );
        $availability_state = self::availability_cycle_state_at_datetime( $availability, $reference_dt, $tz );
        $availability_enabled = ! empty( $availability['enabled'] ) && self::is_goal_eligible_for_availability_cycle( $goal_row );
        $active_at_settlement = $availability_enabled ? ! empty( $availability_state['is_active'] ) : true;
        $paused_at_settlement = $availability_enabled ? ! empty( $availability_state['is_paused'] ) : false;

        $out = array(
            'settlement_at' => $settlement_dt,
            'reference_at' => $reference_dt,
            'settlement_at_local' => $settlement_dt->format( 'Y-m-d H:i:s' ),
            'reference_at_local' => $reference_dt->format( 'Y-m-d H:i:s' ),
            'settlement_at_utc_mysql' => self::ds_dt_to_mysql_utc( $settlement_dt ),
            'reference_at_utc_mysql' => self::ds_dt_to_mysql_utc( $reference_dt ),
            'settlement_local_date' => $settlement_dt->format( 'Y-m-d' ),
            'reference_local_date' => $reference_dt->format( 'Y-m-d' ),
            'timezone' => $tz->getName(),
            'interval' => is_array( $interval ) ? $interval : null,
            'interval_target' => is_array( $interval ) && isset( $interval['target'] ) ? (int) $interval['target'] : 0,
            'interval_unit' => is_array( $interval ) && isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : '',
            'interval_span' => is_array( $interval ) && isset( $interval['period_span'] ) ? (int) $interval['period_span'] : 0,
            'has_interval' => is_array( $interval ),
            'availability' => $availability,
            'availability_state' => $availability_state,
            'availability_enabled' => $availability_enabled,
            'active_at_settlement' => $active_at_settlement,
            'paused_at_settlement' => $paused_at_settlement,
            'eligible' => $active_at_settlement,
            'window_source' => '',
            'window' => null,
            'window_start' => null,
            'window_end' => null,
            'window_start_local' => '',
            'window_end_local' => '',
            'window_start_utc_mysql' => '',
            'window_end_utc_mysql' => '',
        );

        if ( ! is_array( $interval ) ) {
            return $out;
        }

        $window = null;
        $window_source = '';

        if ( $availability_enabled ) {
            $loop_context = self::availability_cycle_current_loop_context( $goal_row, $reference_dt, $options );
            if ( ! empty( $loop_context['enabled'] ) && isset( $loop_context['current'] ) && is_array( $loop_context['current'] ) ) {
                $window = $loop_context['current'];
                $window_source = 'availability_cycle';
            }
        }

        if ( ! is_array( $window ) ) {
            $bounds = self::goal_interval_loop_bounds_at_datetime( $goal_row, $reference_dt, $options );
            if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) && ( $bounds['start'] instanceof DateTimeImmutable ) && ( $bounds['end'] instanceof DateTimeImmutable ) ) {
                $window = array(
                    'start' => $bounds['start'],
                    'end' => $bounds['end'],
                    'start_utc_mysql' => self::ds_dt_to_mysql_utc( $bounds['start'] ),
                    'end_utc_mysql' => self::ds_dt_to_mysql_utc( $bounds['end'] ),
                );
                $window_source = 'interval';
            }
        }

        if ( is_array( $window ) && isset( $window['start'], $window['end'] ) && ( $window['start'] instanceof DateTimeImmutable ) && ( $window['end'] instanceof DateTimeImmutable ) ) {
            $out['window_source'] = $window_source;
            $out['window'] = $window;
            $out['window_start'] = $window['start'];
            $out['window_end'] = $window['end'];
            $out['window_start_local'] = $window['start']->setTimezone( $tz )->format( 'Y-m-d H:i:s' );
            $out['window_end_local'] = $window['end']->setTimezone( $tz )->format( 'Y-m-d H:i:s' );
            $out['window_start_utc_mysql'] = isset( $window['start_utc_mysql'] ) && is_string( $window['start_utc_mysql'] ) ? (string) $window['start_utc_mysql'] : self::ds_dt_to_mysql_utc( $window['start'] );
            $out['window_end_utc_mysql'] = isset( $window['end_utc_mysql'] ) && is_string( $window['end_utc_mysql'] ) ? (string) $window['end_utc_mysql'] : self::ds_dt_to_mysql_utc( $window['end'] );
        }

        return $out;
    }

    public static function is_composite_child_eligible_at_settlement( $goal_row, $settlement_at = null, DateTimeZone $tz = null, $options = array() ) {
        $context = self::composite_child_settlement_context( $goal_row, $settlement_at, $tz, $options );
        return ! empty( $context['eligible'] );
    }
 public static function register_debug_rest_route() { if ( ! function_exists( 'register_rest_route' ) ) { return; } register_rest_route( 'tct/v1', '/loop-bounds', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'handle_debug_loop_bounds' ), 'permission_callback' => function () { return current_user_can( 'manage_options' ); }, ) ); register_rest_route( 'tct/v1', '/allowed-fails-debug', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'handle_debug_allowed_fails' ), 'permission_callback' => function () { return current_user_can( 'manage_options' ); }, ) ); } public static function handle_debug_loop_bounds( $request ) { if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ) { $tz = TCT_Utils::wp_timezone(); } elseif ( function_exists( 'wp_timezone' ) ) { $tz = wp_timezone(); } else { $tz = new DateTimeZone( 'UTC' ); } $now_param = is_object( $request ) && is_callable( array( $request, 'get_param' ) ) ? $request->get_param( 'now' ) : null; $unit_param = is_object( $request ) && is_callable( array( $request, 'get_param' ) ) ? $request->get_param( 'unit' ) : null; $span_param = is_object( $request ) && is_callable( array( $request, 'get_param' ) ) ? $request->get_param( 'span' ) : null; try { if ( is_string( $now_param ) && '' !== trim( $now_param ) ) { $tmp = new DateTimeImmutable( $now_param ); $now_tz = $tmp->setTimezone( $tz ); } else { $now_tz = new DateTimeImmutable( 'now', $tz ); } } catch ( Exception $e ) { $now_tz = new DateTimeImmutable( 'now', $tz ); } $start_of_week = (int) get_option( 'start_of_week', 1 ); $primary = self::current_loop_bounds( $now_tz, $unit_param, $span_param ); $samples = array( 'daily_span1' => self::format_bounds_for_debug( self::current_loop_bounds( $now_tz, 'day', 1 ) ), 'biweekly_span2' => self::format_bounds_for_debug( self::current_loop_bounds( $now_tz, 'week', 2 ) ), 'day_span3' => self::format_bounds_for_debug( self::current_loop_bounds( $now_tz, 'day', 3 ) ), 'quarter_span1' => self::format_bounds_for_debug( self::current_loop_bounds( $now_tz, 'quarter', 1 ) ), 'month_span3' => self::format_bounds_for_debug( self::current_loop_bounds( $now_tz, 'month', 3 ) ), ); $dst_example = null; if ( method_exists( $tz, 'getTransitions' ) ) { $year = (int) $now_tz->format( 'Y' ); $start_ts = strtotime( $year . '-01-01 00:00:00' ); $end_ts = strtotime( ( $year + 1 ) . '-01-01 00:00:00' ); if ( false !== $start_ts && false !== $end_ts ) { $transitions = $tz->getTransitions( $start_ts, $end_ts ); if ( is_array( $transitions ) && count( $transitions ) > 1 ) { $prev = $transitions[0]; foreach ( $transitions as $tr ) { if ( isset( $prev['isdst'], $tr['isdst'], $tr['ts'] ) && $prev['isdst'] !== $tr['isdst'] ) { try { $transition_dt = ( new DateTimeImmutable( '@' . (int) $tr['ts'] ) )->setTimezone( $tz ); $dst_example = array( 'transition_at' => $transition_dt->format( DateTimeInterface::ATOM ), 'day_bounds' => self::format_bounds_for_debug( self::current_loop_bounds( $transition_dt, 'day', 1 ) ), ); } catch ( Exception $e ) { } break; } $prev = $tr; } } } } return array( 'timezone' => $tz->getName(), 'start_of_week' => $start_of_week, 'now' => $now_tz->format( DateTimeInterface::ATOM ), 'request' => array( 'unit' => is_string( $unit_param ) ? $unit_param : null, 'span' => $span_param, 'now' => is_string( $now_param ) ? $now_param : null, ), 'bounds' => self::format_bounds_for_debug( $primary ), 'samples' => $samples, 'dst_example' => $dst_example, ); } 
    /**
     * Debug helper for Allowed Fails (Chunk 3).
     * Usage (WP_DEBUG only):
     *   /wp-json/tct/v1/allowed-fails-debug?goal_id=123
     */
    public static function handle_debug_allowed_fails( $request ) {
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ) {
            $tz = TCT_Utils::wp_timezone();
        } elseif ( function_exists( 'wp_timezone' ) ) {
            $tz = wp_timezone();
        } else {
            $tz = new DateTimeZone( 'UTC' );
        }

        $now_param  = is_object( $request ) && is_callable( array( $request, 'get_param' ) ) ? $request->get_param( 'now' ) : null;
        $goal_param = is_object( $request ) && is_callable( array( $request, 'get_param' ) ) ? $request->get_param( 'goal_id' ) : null;

        try {
            if ( is_string( $now_param ) && '' !== trim( $now_param ) ) {
                $tmp = new DateTimeImmutable( $now_param );
                $now_tz = $tmp->setTimezone( $tz );
            } else {
                $now_tz = new DateTimeImmutable( 'now', $tz );
            }
        } catch ( Exception $e ) {
            $now_tz = new DateTimeImmutable( 'now', $tz );
        }

        $goal_id = (int) $goal_param;
        if ( $goal_id <= 0 ) {
            return new WP_Error( 'tct_missing_goal_id', 'Missing or invalid goal_id.', array( 'status' => 400 ) );
        }

        $uid = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
        if ( $uid <= 0 ) {
            return new WP_Error( 'tct_not_logged_in', 'Not logged in.', array( 'status' => 401 ) );
        }

        if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) {
            return new WP_Error( 'tct_db_missing', 'TCT_DB not available.', array( 'status' => 500 ) );
        }

        global $wpdb;
        $table = TCT_DB::table_goals();

        // SELECT * so this debug route doesn't hard-require newer columns.
        $goal = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND id = %d LIMIT 1", $uid, $goal_id ),
            ARRAY_A
        );

        if ( ! is_array( $goal ) ) {
            return new WP_Error( 'tct_goal_not_found', 'Goal not found (or not owned by current user).', array( 'status' => 404 ) );
        }

        $interval_norm = self::normalize_goal_interval_from_row( $goal );
        $allowed_cfg   = self::normalize_allowed_fails_config_from_row( $goal );

        $bounds = self::current_allowed_fails_bounds( $now_tz, $allowed_cfg['unit'], $allowed_cfg['span'] );

        return array(
            'timezone'       => $tz->getName(),
            'start_of_week'  => (int) get_option( 'start_of_week', 1 ),
            'now'            => $now_tz->format( DateTimeInterface::ATOM ),
            'request'        => array(
                'goal_id' => $goal_id,
                'now'     => is_string( $now_param ) ? $now_param : null,
            ),
            'goal'           => array(
                'id'                 => isset( $goal['id'] ) ? (int) $goal['id'] : $goal_id,
                'goal_name'          => isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : '',
                'goal_type'          => isset( $goal['goal_type'] ) ? (string) $goal['goal_type'] : '',
                'target'             => isset( $goal['target'] ) ? (int) $goal['target'] : 0,
                'period_unit'        => isset( $goal['period_unit'] ) ? (string) $goal['period_unit'] : '',
                'period_span'        => isset( $goal['period_span'] ) ? (int) $goal['period_span'] : 0,
                'allowed_fails_target' => isset( $goal['allowed_fails_target'] ) ? (int) $goal['allowed_fails_target'] : 0,
                'allowed_fails_unit'   => isset( $goal['allowed_fails_unit'] ) ? (string) $goal['allowed_fails_unit'] : '',
                'allowed_fails_span'   => isset( $goal['allowed_fails_span'] ) ? (int) $goal['allowed_fails_span'] : 0,
            ),
            'interval_normalized' => $interval_norm,
            'checks'         => array(
                'is_exactly_one_per_day'      => self::is_goal_exactly_one_per_day_interval( $goal ),
                'is_allowed_fails_eligible'   => self::is_goal_eligible_for_allowed_fails( $goal ),
            ),
            'allowed_fails_sanitized' => $allowed_cfg,
            'allowed_fails_bounds'    => self::format_bounds_for_debug( $bounds ),
        );
    }

private static function format_bounds_for_debug( $bounds ) { if ( ! is_array( $bounds ) || ! isset( $bounds['start'], $bounds['end'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) || ! ( $bounds['end'] instanceof DateTimeImmutable ) ) { return array(); } $s = $bounds['start']; $e = $bounds['end']; return array( 'start' => $s->format( DateTimeInterface::ATOM ), 'end' => $e->format( DateTimeInterface::ATOM ), 'start_ts' => (int) $s->getTimestamp(), 'end_ts' => (int) $e->getTimestamp(), ); } } if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    add_action( 'rest_api_init', array( 'TCT_Interval', 'register_debug_rest_route' ) );
    add_action( 'init', array( 'TCT_Interval', 'maybe_log_due_schedule_debug' ) );
} 
