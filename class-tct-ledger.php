<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } class TCT_Ledger { const META_POINTS_STARTED_AT = 'tct_points_started_at_utc'; public static function get_points_started_at( $user_id ) { $raw = get_user_meta( $user_id, self::META_POINTS_STARTED_AT, true ); if ( ! $raw || ! is_string( $raw ) ) { return null; } $raw = trim( $raw ); if ( '' === $raw ) { return null; } return $raw; } public static function ensure_points_started( $user_id ) { $existing = self::get_points_started_at( $user_id ); if ( $existing ) { return $existing; } $now = current_time( 'mysql', true ); update_user_meta( $user_id, self::META_POINTS_STARTED_AT, $now ); return $now; } public static function get_balance( $user_id ) { global $wpdb; $ledger = TCT_DB::table_ledger(); return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(points),0) FROM {$ledger} WHERE user_id = %d", $user_id ) ); } public static function get_points_in_range( $user_id, $start_utc_mysql, $end_utc_mysql ) { global $wpdb; $ledger = TCT_DB::table_ledger(); return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(points),0) FROM {$ledger} WHERE user_id = %d AND occurred_at >= %s AND occurred_at <= %s", $user_id, $start_utc_mysql, $end_utc_mysql ) ); } public static function get_earned_lost_in_range( $user_id, $start_utc_mysql, $end_utc_mysql ) { global $wpdb; $ledger = TCT_DB::table_ledger(); $row = $wpdb->get_row( $wpdb->prepare( "SELECT COALESCE(SUM(CASE WHEN points > 0 THEN points ELSE 0 END), 0) AS earned,
                        COALESCE(SUM(CASE WHEN points < 0 THEN points ELSE 0 END), 0) AS lost
                 FROM {$ledger}
                 WHERE user_id = %d
                   AND occurred_at >= %s
                   AND occurred_at <= %s
                   AND (
                        event_type = 'completion'
                        OR (event_type = 'goal_bonus' AND points < 0)
                   )", $user_id, $start_utc_mysql, $end_utc_mysql ), ARRAY_A ); return array( 'earned' => isset( $row['earned'] ) ? (int) $row['earned'] : 0, 'lost' => isset( $row['lost'] ) ? (int) $row['lost'] : 0, ); } public static function get_earned_lost_by_day( $user_id, $start_utc_mysql, $end_utc_mysql ) { global $wpdb; $ledger = TCT_DB::table_ledger(); $offset_str = self::get_wp_utc_offset_string(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT DATE(CONVERT_TZ(occurred_at, '+00:00', %s)) AS day_local,
                        COALESCE(SUM(CASE WHEN points > 0 THEN points ELSE 0 END), 0) AS earned,
                        COALESCE(SUM(CASE WHEN points < 0 THEN points ELSE 0 END), 0) AS lost
                 FROM {$ledger}
                 WHERE user_id = %d
                   AND occurred_at >= %s
                   AND occurred_at <= %s
                   AND (
                        event_type = 'completion'
                        OR (event_type = 'goal_bonus' AND points < 0)
                   )
                 GROUP BY day_local
                 ORDER BY day_local ASC", $offset_str, $user_id, $start_utc_mysql, $end_utc_mysql ), ARRAY_A ); if ( ! is_array( $rows ) ) { return array(); } $out = array(); foreach ( $rows as $r ) { $out[] = array( 'day_local' => isset( $r['day_local'] ) ? (string) $r['day_local'] : '', 'earned' => isset( $r['earned'] ) ? (int) $r['earned'] : 0, 'lost' => isset( $r['lost'] ) ? (int) $r['lost'] : 0, ); } return $out; } private static function get_wp_utc_offset_string() { try { $tz = TCT_Utils::wp_timezone(); $now = new DateTimeImmutable( 'now', $tz ); $offset_seconds = $now->getOffset(); } catch ( Exception $e ) { $offset_seconds = 0; } $sign = $offset_seconds >= 0 ? '+' : '-'; $abs = abs( $offset_seconds ); $hours = (int) floor( $abs / 3600 ); $mins = (int) floor( ( $abs % 3600 ) / 60 ); return sprintf( '%s%02d:%02d', $sign, $hours, $mins ); } public static function get_earned_points_since( $user_id, $since_utc_mysql ) { $user_id = (int) $user_id; if ( $user_id <= 0 || ! is_string( $since_utc_mysql ) ) { return 0; } $since_utc_mysql = trim( $since_utc_mysql ); if ( '' === $since_utc_mysql || '0000-00-00 00:00:00' === $since_utc_mysql ) { return 0; } global $wpdb; $ledger = TCT_DB::table_ledger(); return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(points),0) FROM {$ledger} WHERE user_id = %d AND occurred_at >= %s", $user_id, $since_utc_mysql ) ); } public static function get_transactions( $user_id, $limit = 200, $start_utc_mysql = null, $end_utc_mysql = null ) { global $wpdb; $ledger = TCT_DB::table_ledger(); $completions = TCT_DB::table_completions(); $goals = TCT_DB::table_goals(); $roles = TCT_DB::table_roles(); $domains = TCT_DB::table_domains(); $limit = (int) $limit; if ( $limit < 1 ) { $limit = 1; } if ( $limit > 1000 ) { $limit = 1000; } $where = 'WHERE l.user_id = %d'; $args = array( $user_id ); if ( $start_utc_mysql && $end_utc_mysql ) { $where .= ' AND l.occurred_at >= %s AND l.occurred_at <= %s'; $args[] = $start_utc_mysql; $args[] = $end_utc_mysql; } $sql = "SELECT
                    l.id,
                    l.event_key,
                    l.event_type,
                    l.points,
                    l.occurred_at,
                    l.goal_id,
                    l.goal_name,
                    l.label_name,
                    COALESCE(NULLIF(r.domain_id,0), NULLIF(g.domain_id,0), 0) AS domain_id,
                    d.domain_name AS domain_name,
                    g.role_id AS role_id,
                    r.role_name AS role_name,
                    l.details,
                    l.interval_unit,
                    l.interval_mode,
                    l.interval_target,
                    l.bonus_points,
                    l.met,
                    c.id AS completion_id
                FROM {$ledger} l
                LEFT JOIN {$completions} c
                    ON l.event_type = 'completion'
                    AND c.user_id = l.user_id
                    AND c.goal_id = l.goal_id
                    AND CONCAT('c_', SHA1(CONCAT(c.source, ':', c.source_ref, ':', c.goal_id))) = l.event_key
                LEFT JOIN {$goals} g
                    ON g.user_id = l.user_id
                    AND g.id = l.goal_id
                LEFT JOIN {$roles} r
                    ON r.user_id = l.user_id
                    AND r.id = g.role_id
                LEFT JOIN {$domains} d
                    ON d.user_id = l.user_id
                    AND d.id = COALESCE(NULLIF(r.domain_id,0), NULLIF(g.domain_id,0), 0)
                {$where}
                ORDER BY l.occurred_at DESC, l.id DESC
                LIMIT {$limit}"; return $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ); } public static function delete_user_data( $user_id ) { global $wpdb; $ledger = TCT_DB::table_ledger(); $wpdb->query( $wpdb->prepare( "DELETE FROM {$ledger} WHERE user_id = %d", $user_id ) ); delete_user_meta( $user_id, self::META_POINTS_STARTED_AT ); } public static function reconcile_user( $user_id, $since_utc_mysql, $until_utc_mysql ) {
    $started_at = self::get_points_started_at( $user_id );
    if ( ! $started_at ) {
        return;
    }
    if ( strcmp( $since_utc_mysql, $started_at ) < 0 ) {
        $since_utc_mysql = $started_at;
    }
    if ( strcmp( $until_utc_mysql, $since_utc_mysql ) < 0 ) {
        return;
    }

    global $wpdb;
    $ledger = TCT_DB::table_ledger();
    $completions = TCT_DB::table_completions();
    $goals = TCT_DB::table_goals();
    $roles = TCT_DB::table_roles();
    $domains = TCT_DB::table_domains();
    $goals = TCT_DB::table_goals();

    $wpdb->query(
        $wpdb->prepare(
            "DELETE l FROM {$ledger} l
                 INNER JOIN {$goals} g
                    ON g.user_id = l.user_id
                   AND g.id = l.goal_id
                   AND g.is_tracked = 1
                 WHERE l.user_id = %d
                   AND l.occurred_at >= %s AND l.occurred_at <= %s
                   AND (
                        l.event_type = 'goal_bonus'
                        OR (
                            l.event_type = 'completion'
                            AND NOT (
                                COALESCE(l.details,'') LIKE '[manual fail]%%'
                                OR COALESCE(l.details,'') LIKE '[auto miss]%%'
                            )
                        )
                   )",
            $user_id,
            $since_utc_mysql,
            $until_utc_mysql
        )
    );

    $now = current_time( 'mysql', true );

    $sql_completion = "INSERT IGNORE INTO {$ledger} (
                user_id, event_key, event_type, points, occurred_at,
                goal_id, goal_name, label_name,
                todoist_completed_id, todoist_task_id,
                details, created_at, updated_at
            )
            SELECT
                c.user_id,
                CONCAT('c_', SHA1(CONCAT(c.source, ':', c.source_ref, ':', c.goal_id))),
                'completion',
                g.points_per_completion,
                c.completed_at,
                g.id,
                g.goal_name,
                COALESCE(NULLIF(g.label_name,''), ''),
                COALESCE(NULLIF(c.todoist_completed_id,''), ''),
                COALESCE(NULLIF(c.todoist_task_id,''), ''),
                COALESCE(NULLIF(c.note,''), NULLIF(c.task_content,''), ''),
                %s,
                %s
            FROM {$completions} c
            INNER JOIN {$goals} g
                ON g.user_id = c.user_id AND g.id = c.goal_id
            WHERE c.user_id = %d
              AND c.completed_at >= %s AND c.completed_at <= %s
              AND g.is_tracked = 1
              AND g.points_per_completion > 0
              AND g.points_enabled_at IS NOT NULL
              AND g.points_enabled_at <> ''
              AND g.points_enabled_at <> '0000-00-00 00:00:00'
              AND c.completed_at >= g.points_enabled_at
              AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')";

    $wpdb->query( $wpdb->prepare( $sql_completion, $now, $now, $user_id, $since_utc_mysql, $until_utc_mysql ) );
    self::reconcile_negative_goal_completions( $user_id, $since_utc_mysql, $until_utc_mysql, $now );

    $goal_rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, label_name, goal_name, intervals_json, target, period_unit, period_span, period_mode, points_per_completion, points_enabled_at, goal_type, threshold, created_at, updated_at, availability_cycle_json, interval_anchor_json
                 FROM {$goals}
                 WHERE user_id = %d AND is_tracked = 1",
            $user_id
        ),
        ARRAY_A
    );
    if ( ! is_array( $goal_rows ) || empty( $goal_rows ) ) {
        return;
    }

    $tz = TCT_Utils::wp_timezone();
    $now_tz = new DateTimeImmutable( 'now', $tz );
    $events = array();

    foreach ( $goal_rows as $g ) {
        $goal_id = isset( $g['id'] ) ? (int) $g['id'] : 0;
        $label_name = isset( $g['label_name'] ) ? (string) $g['label_name'] : '';
        $goal_name = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : $label_name;
        if ( $goal_id <= 0 ) {
            continue;
        }

        $goal_type = isset( $g['goal_type'] ) && is_string( $g['goal_type'] ) ? (string) $g['goal_type'] : 'positive';
        $is_negative = TCT_Utils::is_negative_goal_type( $goal_type );
        if ( $is_negative ) {
            $threshold = isset( $g['threshold'] ) && is_numeric( $g['threshold'] ) ? (int) $g['threshold'] : null;
            self::reconcile_negative_goal_bonus( $user_id, $g, $since_utc_mysql, $until_utc_mysql, $now, $goal_type, $threshold, $tz, $now_tz, $events );
            continue;
        }

        $points_enabled_at = isset( $g['points_enabled_at'] ) ? (string) $g['points_enabled_at'] : '';
        $points_enabled_at = trim( $points_enabled_at );
        if ( '' === $points_enabled_at || '0000-00-00 00:00:00' === $points_enabled_at ) {
            continue;
        }

        $ppc = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0;
        if ( $ppc <= 0 ) {
            continue;
        }

        $since_for_goal = $since_utc_mysql;
        if ( strcmp( $since_for_goal, $points_enabled_at ) < 0 ) {
            $since_for_goal = $points_enabled_at;
        }

        $goal_created_at_raw = isset( $g['created_at'] ) ? trim( (string) $g['created_at'] ) : '';
        $goal_updated_at_raw = isset( $g['updated_at'] ) ? trim( (string) $g['updated_at'] ) : '';
        $goal_created_at_ts = 0;
        $goal_updated_at_ts = 0;
        if ( '' !== $goal_created_at_raw && '0000-00-00 00:00:00' !== $goal_created_at_raw ) {
            $tmp = strtotime( $goal_created_at_raw . ' UTC' );
            if ( false !== $tmp ) {
                $goal_created_at_ts = (int) $tmp;
            }
        }
        if ( '' !== $goal_updated_at_raw && '0000-00-00 00:00:00' !== $goal_updated_at_raw ) {
            $tmp = strtotime( $goal_updated_at_raw . ' UTC' );
            if ( false !== $tmp ) {
                $goal_updated_at_ts = (int) $tmp;
            }
        }

        if ( self::is_anki_cards_goal_type( $goal_type ) ) {
            self::reconcile_anki_cards_goal_bonus( $user_id, $g, $since_for_goal, $until_utc_mysql, $now, $tz, $now_tz, $events, $goal_created_at_ts, $goal_updated_at_ts );
            continue;
        }

        $intervals = self::extract_intervals_with_bonus( $g );
        if ( empty( $intervals ) ) {
            continue;
        }

        $availability = self::normalize_goal_availability_for_ledger( $g );
        $availability_enabled = self::goal_uses_availability_cycle_for_ledger( $g, $availability );

        $ts = array();
        $availability_windows = array();
        $weighted_series = array();

        if ( $availability_enabled ) {
            $availability_windows = self::availability_windows_for_goal_bonus( $g, $since_for_goal, $until_utc_mysql, $tz );
            if ( empty( $availability_windows ) ) {
                continue;
            }

            $raw_query_start = self::availability_raw_query_start_utc_mysql( $availability, $availability_windows, $points_enabled_at, $tz );
            $weighted_series = self::build_weighted_completion_series( $user_id, $goal_id, $goal_type, $availability, $raw_query_start, $until_utc_mysql, $tz );
        } else {
            $times = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT completed_at FROM {$completions}
                     WHERE user_id = %d AND goal_id = %d
                       AND completed_at >= %s AND completed_at <= %s
                     ORDER BY completed_at ASC",
                    $user_id,
                    $goal_id,
                    $since_for_goal,
                    $until_utc_mysql
                )
            );
            if ( is_array( $times ) ) {
                foreach ( $times as $t ) {
                    if ( ! $t || ! is_string( $t ) ) {
                        continue;
                    }
                    $tmp_ts = strtotime( $t . ' UTC' );
                    if ( false !== $tmp_ts ) {
                        $ts[] = (int) $tmp_ts;
                    }
                }
            }
        }

        foreach ( $intervals as $interval ) {
            $unit = (string) $interval['period_unit'];
            $mode = (string) $interval['period_mode'];
            $target = (int) $interval['target'];
            $span = isset( $interval['period_span'] ) ? (int) $interval['period_span'] : 1;
            if ( $span < 1 ) {
                $span = 1;
            }
            if ( $target <= 0 ) {
                continue;
            }

            if ( $availability_enabled ) {
                $windows = $availability_windows;
            } else {
                $windows = self::generate_completed_windows( $now_tz, $tz, $unit, $span, $since_for_goal, $until_utc_mysql );
            }
            if ( empty( $windows ) ) {
                continue;
            }

            foreach ( $windows as $w ) {
                $start_utc_mysql = isset( $w['start_utc_mysql'] ) ? (string) $w['start_utc_mysql'] : '';
                $end_utc_mysql = isset( $w['end_utc_mysql'] ) ? (string) $w['end_utc_mysql'] : '';
                if ( '' === $start_utc_mysql || '' === $end_utc_mysql ) {
                    continue;
                }
                if ( strcmp( $end_utc_mysql, $points_enabled_at ) <= 0 ) {
                    continue;
                }

                $eval_start_utc_mysql = $start_utc_mysql;
                if ( strcmp( $eval_start_utc_mysql, $points_enabled_at ) < 0 ) {
                    $eval_start_utc_mysql = $points_enabled_at;
                }

                $count = 0;
                $window_start_ts = isset( $w['start_ts'] ) ? (int) $w['start_ts'] : (int) strtotime( $start_utc_mysql . ' UTC' );
                $window_end_ts = isset( $w['end_ts'] ) ? (int) $w['end_ts'] : (int) strtotime( $end_utc_mysql . ' UTC' );
                $effective_target_l = $target;

                if ( $availability_enabled ) {
                    $count = self::count_weighted_in_range_utc( $weighted_series, $eval_start_utc_mysql, $end_utc_mysql );
                } else {
                    $count = self::count_in_range_utc( $ts, $eval_start_utc_mysql, $end_utc_mysql );
                }

                $prorate_anchor_l = TCT_Utils::compute_prorate_anchor_ts( $goal_created_at_ts, $goal_updated_at_ts, $window_start_ts );
                if ( $prorate_anchor_l > 0 ) {
                    if ( $availability_enabled ) {
                        $effective_target_l = self::availability_prorated_target( $target, $prorate_anchor_l, $w, $availability, $tz );
                    } else {
                        $effective_target_l = TCT_Utils::compute_prorated_target( $target, $prorate_anchor_l, $window_start_ts, $window_end_ts );
                    }
                }
                if ( $effective_target_l <= 0 ) {
                    continue;
                }

                $met = $count >= $effective_target_l;
                if ( $met ) {
                    $pts = (int) TCT_Utils::compute_bonus_points( $ppc, $effective_target_l );
                } else {
                    $pts = (int) TCT_Utils::compute_penalty_points( $ppc, $effective_target_l, $count );
                }

                $applied_mag = abs( (int) $pts );
                $event_key = 'b_' . sha1( implode( '|', array( (string) $user_id, (string) $goal_id, $unit, (string) $span, $mode, $end_utc_mysql ) ) );
                $target_label = (int) $effective_target_l;
                if ( $effective_target_l !== $target ) {
                    $target_label .= ' pro-rated from ' . (int) $target;
                }

                $events[] = array(
                    'user_id' => (int) $user_id,
                    'event_key' => $event_key,
                    'event_type' => 'goal_bonus',
                    'points' => (int) $pts,
                    'occurred_at' => self::interval_end_occurred_at_utc_mysql( $end_utc_mysql ),
                    'goal_id' => (int) $goal_id,
                    'goal_name' => $goal_name,
                    'label_name' => $label_name,
                    'interval_unit' => $unit,
                    'interval_mode' => $mode,
                    'interval_target' => (int) $effective_target_l,
                    'bonus_points' => (int) $applied_mag,
                    'window_start' => $start_utc_mysql,
                    'window_end' => $end_utc_mysql,
                    'met' => $met ? 1 : 0,
                    'details' => $met
                        ? ( 'Goal met (+' . (int) $pts . '; ' . (int) $count . '/' . $target_label . ')' )
                        : ( 'Goal not met (' . (int) $pts . '; ' . (int) $count . '/' . $target_label . ')' ),
                    'created_at' => $now,
                    'updated_at' => $now,
                );
            }
        }
    }

    if ( ! empty( $events ) ) {
        self::bulk_insert_events( $events );
    }

    self::reconcile_composite_goal_events_scaffold( $user_id, $since_utc_mysql, $until_utc_mysql, $goal_rows, $now, $events );
}

private static function composite_goals_feature_enabled() {
    if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'is_composite_goals_enabled' ) ) ) {
        return (bool) TCT_Plugin::is_composite_goals_enabled();
    }

    return false;
}

private static function reconcile_composite_goal_events_scaffold( $user_id, $since_utc_mysql, $until_utc_mysql, $goal_rows, $now, &$events ) {
    if ( ! self::composite_goals_feature_enabled() ) {
        return;
    }

    $user_id = (int) $user_id;
    if ( $user_id <= 0 ) {
        return;
    }

    $since_utc_mysql = is_string( $since_utc_mysql ) ? trim( (string) $since_utc_mysql ) : '';
    $until_utc_mysql = is_string( $until_utc_mysql ) ? trim( (string) $until_utc_mysql ) : '';
    if ( '' === $since_utc_mysql || '' === $until_utc_mysql ) {
        return;
    }
    if ( strcmp( $until_utc_mysql, $since_utc_mysql ) < 0 ) {
        return;
    }

    $parents = array();
    foreach ( (array) $goal_rows as $goal_row ) {
        if ( self::composite_is_parent_row( $goal_row ) ) {
            $parents[] = $goal_row;
        }
    }

    if ( empty( $parents ) ) {
        return;
    }

    $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
    $now_tz = new DateTimeImmutable( 'now', $tz );
    $inserted_initial = 0;
    $inserted_adjustments = 0;
    $window_count = 0;

    foreach ( $parents as $parent_row ) {
        $parent_goal_id = isset( $parent_row['id'] ) ? (int) $parent_row['id'] : 0;
        if ( $parent_goal_id <= 0 ) {
            continue;
        }

        $existing_window_rows = self::composite_existing_window_rows_map( $user_id, $parent_goal_id, $since_utc_mysql, $until_utc_mysql );
        $generated_windows = self::composite_parent_generated_windows( $parent_row, $since_utc_mysql, $until_utc_mysql, $tz, $now_tz );
        $window_map = array();

        foreach ( $generated_windows as $window_key => $window_def ) {
            $window_map[ $window_key ] = array(
                'window_start' => isset( $window_def['window_start'] ) ? (string) $window_def['window_start'] : '',
                'window_end' => isset( $window_def['window_end'] ) ? (string) $window_def['window_end'] : '',
                'existing_rows' => array(),
            );
        }

        foreach ( $existing_window_rows as $window_key => $rows ) {
            if ( ! is_array( $rows ) || empty( $rows ) ) {
                continue;
            }
            $first = reset( $rows );
            $window_map[ $window_key ] = array(
                'window_start' => isset( $first['window_start'] ) ? (string) $first['window_start'] : '',
                'window_end' => isset( $first['window_end'] ) ? (string) $first['window_end'] : '',
                'existing_rows' => $rows,
            );
        }

        if ( empty( $window_map ) ) {
            continue;
        }

        $windows = array_values( $window_map );
        usort(
            $windows,
            static function( $a, $b ) {
                $a_end = isset( $a['window_end'] ) ? (string) $a['window_end'] : '';
                $b_end = isset( $b['window_end'] ) ? (string) $b['window_end'] : '';
                if ( $a_end === $b_end ) {
                    $a_start = isset( $a['window_start'] ) ? (string) $a['window_start'] : '';
                    $b_start = isset( $b['window_start'] ) ? (string) $b['window_start'] : '';
                    return strcmp( $a_start, $b_start );
                }
                return strcmp( $a_end, $b_end );
            }
        );

        $current_child_rows = null;
        foreach ( $windows as $window_def ) {
            $window_start = isset( $window_def['window_start'] ) ? trim( (string) $window_def['window_start'] ) : '';
            $window_end = isset( $window_def['window_end'] ) ? trim( (string) $window_def['window_end'] ) : '';
            if ( '' === $window_start || '' === $window_end ) {
                continue;
            }
            if ( strcmp( $window_end, $window_start ) <= 0 ) {
                continue;
            }

            $window_count++;
            $existing_rows = isset( $window_def['existing_rows'] ) && is_array( $window_def['existing_rows'] ) ? $window_def['existing_rows'] : array();
            $snapshot_children = self::composite_snapshot_children_from_event_rows( $existing_rows );

            if ( ! empty( $snapshot_children ) ) {
                $child_sources = self::composite_child_sources_from_snapshot( $user_id, $snapshot_children );
            } else {
                if ( null === $current_child_rows ) {
                    $current_child_rows = self::composite_current_child_rows_for_parent( $user_id, $parent_goal_id );
                }
                $child_sources = $current_child_rows;
            }

            $payload = self::composite_build_parent_window_payload( $user_id, $parent_row, $window_start, $window_end, $child_sources );
            if ( ! is_array( $payload ) ) {
                continue;
            }

            $desired = self::composite_desired_points_from_payload( $payload );
            if ( empty( $existing_rows ) ) {
                $inserted_initial += self::composite_insert_initial_window_events( $user_id, $parent_row, $window_start, $window_end, $payload, $desired );
            } else {
                $inserted_adjustments += self::composite_insert_adjustment_event( $user_id, $parent_row, $window_start, $window_end, $payload, $desired, $existing_rows, $now );
            }
        }
    }

    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'debug_log' ) ) ) {
        TCT_Utils::debug_log(
            'composite_goals_ledger_reconcile',
            array(
                'user_id' => (int) $user_id,
                'since_utc' => (string) $since_utc_mysql,
                'until_utc' => (string) $until_utc_mysql,
                'parent_count' => count( $parents ),
                'window_count' => (int) $window_count,
                'inserted_initial' => (int) $inserted_initial,
                'inserted_adjustments' => (int) $inserted_adjustments,
            )
        );
    }
}

private static function composite_is_parent_row( $goal_row ) {
    if ( ! is_array( $goal_row ) ) {
        return false;
    }

    $goal_type = isset( $goal_row['goal_type'] ) ? (string) $goal_row['goal_type'] : '';
    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_composite_goal_type' ) ) ) {
        return (bool) TCT_Utils::is_composite_goal_type( $goal_type );
    }

    return ( 'composite_parent' === strtolower( trim( $goal_type ) ) );
}

private static function composite_event_types() {
    return array(
        'composite_bonus',
        'composite_perfection_bonus',
        'composite_penalty',
        'composite_adjustment',
    );
}

private static function composite_window_key( $window_start, $window_end ) {
    return (string) $window_start . '|' . (string) $window_end;
}

private static function composite_existing_window_rows_map( $user_id, $parent_goal_id, $since_utc_mysql, $until_utc_mysql ) {
    global $wpdb;

    $user_id = (int) $user_id;
    $parent_goal_id = (int) $parent_goal_id;
    if ( $user_id <= 0 || $parent_goal_id <= 0 ) {
        return array();
    }

    $ledger = TCT_DB::table_ledger();
    $types = self::composite_event_types();
    $placeholders = implode( ',', array_fill( 0, count( $types ), '%s' ) );
    $sql = "SELECT id, event_key, event_type, points, window_start, window_end, details, created_at, updated_at, occurred_at
              FROM {$ledger}
             WHERE user_id = %d
               AND goal_id = %d
               AND event_type IN ({$placeholders})
               AND window_start IS NOT NULL
               AND window_end IS NOT NULL
               AND window_end > %s
               AND window_end <= %s
             ORDER BY window_end ASC, created_at ASC, id ASC";
    $args = array_merge( array( $user_id, $parent_goal_id ), $types, array( $since_utc_mysql, $until_utc_mysql ) );
    $rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A );
    if ( ! is_array( $rows ) ) {
        return array();
    }

    $map = array();
    foreach ( $rows as $row ) {
        $window_start = isset( $row['window_start'] ) ? (string) $row['window_start'] : '';
        $window_end = isset( $row['window_end'] ) ? (string) $row['window_end'] : '';
        if ( '' === $window_start || '' === $window_end ) {
            continue;
        }
        $key = self::composite_window_key( $window_start, $window_end );
        if ( ! isset( $map[ $key ] ) ) {
            $map[ $key ] = array();
        }
        $map[ $key ][] = $row;
    }

    return $map;
}

private static function composite_parent_interval_from_row( $parent_row ) {
    if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) {
        $interval = TCT_Interval::normalize_goal_interval_from_row( $parent_row );
        if ( is_array( $interval ) ) {
            return $interval;
        }
    }

    if ( ! is_array( $parent_row ) ) {
        return null;
    }

    $unit = isset( $parent_row['period_unit'] ) ? sanitize_text_field( (string) $parent_row['period_unit'] ) : 'day';
    $allowed_units = array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' );
    if ( ! in_array( $unit, $allowed_units, true ) ) {
        $unit = 'day';
    }

    $span = isset( $parent_row['period_span'] ) ? max( 1, (int) $parent_row['period_span'] ) : 1;
    $mode = isset( $parent_row['period_mode'] ) ? sanitize_text_field( (string) $parent_row['period_mode'] ) : 'calendar';
    if ( '' === $mode ) {
        $mode = 'calendar';
    }

    $target = isset( $parent_row['target'] ) ? max( 1, (int) $parent_row['target'] ) : 1;

    return array(
        'target' => (int) $target,
        'period_unit' => (string) $unit,
        'period_span' => (int) $span,
        'period_mode' => (string) $mode,
    );
}

private static function composite_parent_generated_since( $parent_row, $since_utc_mysql ) {
    $since_utc_mysql = is_string( $since_utc_mysql ) ? trim( (string) $since_utc_mysql ) : '';
    $candidate = $since_utc_mysql;

    foreach ( array( 'updated_at', 'created_at', 'points_enabled_at' ) as $key ) {
        $value = isset( $parent_row[ $key ] ) ? trim( (string) $parent_row[ $key ] ) : '';
        if ( '' === $value || '0000-00-00 00:00:00' === $value ) {
            continue;
        }
        if ( false === strtotime( $value . ' UTC' ) ) {
            continue;
        }
        if ( '' === $candidate || strcmp( $value, $candidate ) > 0 ) {
            $candidate = $value;
        }
    }

    return $candidate;
}

private static function composite_parent_generated_windows( $parent_row, $since_utc_mysql, $until_utc_mysql, DateTimeZone $tz, DateTimeImmutable $now_tz ) {
    $interval = self::composite_parent_interval_from_row( $parent_row );
    if ( ! is_array( $interval ) ) {
        return array();
    }

    $unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : '';
    $span = isset( $interval['period_span'] ) ? max( 1, (int) $interval['period_span'] ) : 1;
    if ( '' === $unit ) {
        return array();
    }

    $since_for_parent = self::composite_parent_generated_since( $parent_row, $since_utc_mysql );
    if ( '' === $since_for_parent || strcmp( $until_utc_mysql, $since_for_parent ) <= 0 ) {
        return array();
    }

    $windows = self::generate_completed_windows( $now_tz, $tz, $unit, $span, $since_for_parent, $until_utc_mysql, $parent_row );
    if ( ! is_array( $windows ) ) {
        return array();
    }

    $out = array();
    foreach ( $windows as $window ) {
        $window_start = isset( $window['start_utc_mysql'] ) ? trim( (string) $window['start_utc_mysql'] ) : '';
        $window_end = isset( $window['end_utc_mysql'] ) ? trim( (string) $window['end_utc_mysql'] ) : '';
        if ( '' === $window_start || '' === $window_end ) {
            continue;
        }
        if ( strcmp( $window_end, $window_start ) <= 0 ) {
            continue;
        }
        $out[ self::composite_window_key( $window_start, $window_end ) ] = array(
            'window_start' => $window_start,
            'window_end' => $window_end,
        );
    }

    return $out;
}

private static function composite_decode_event_details( $details ) {
    if ( ! is_string( $details ) ) {
        return null;
    }

    $details = trim( (string) $details );
    if ( '' === $details ) {
        return null;
    }

    $decoded = json_decode( $details, true );
    return is_array( $decoded ) ? $decoded : null;
}

private static function composite_snapshot_children_from_event_rows( $rows ) {
    if ( ! is_array( $rows ) || empty( $rows ) ) {
        return array();
    }

    for ( $i = count( $rows ) - 1; $i >= 0; $i-- ) {
        $row = isset( $rows[ $i ] ) && is_array( $rows[ $i ] ) ? $rows[ $i ] : array();
        $decoded = self::composite_decode_event_details( isset( $row['details'] ) ? $row['details'] : '' );
        if ( is_array( $decoded ) && isset( $decoded['children'] ) && is_array( $decoded['children'] ) ) {
            return array_values( $decoded['children'] );
        }
    }

    return array();
}

private static function composite_fetch_goal_rows_by_ids( $user_id, $goal_ids ) {
    global $wpdb;

    $user_id = (int) $user_id;
    if ( $user_id <= 0 ) {
        return array();
    }

    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_child_id_list' ) ) ) {
        $goal_ids = TCT_Utils::sanitize_composite_child_id_list( $goal_ids );
    } else {
        $goal_ids = array_values( array_unique( array_map( 'intval', (array) $goal_ids ) ) );
        $goal_ids = array_values( array_filter( $goal_ids, static function( $goal_id ) {
            return ( $goal_id > 0 );
        } ) );
    }

    if ( empty( $goal_ids ) ) {
        return array();
    }

    $goals = TCT_DB::table_goals();
    $placeholders = implode( ',', array_fill( 0, count( $goal_ids ), '%d' ) );
    $sql = "SELECT * FROM {$goals} WHERE user_id = %d AND id IN ({$placeholders})";
    $args = array_merge( array( $user_id ), $goal_ids );
    $rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A );
    if ( ! is_array( $rows ) ) {
        return array();
    }

    $map = array();
    foreach ( $rows as $row ) {
        $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0;
        if ( $goal_id > 0 ) {
            $map[ $goal_id ] = $row;
        }
    }

    $ordered = array();
    foreach ( $goal_ids as $goal_id ) {
        if ( isset( $map[ $goal_id ] ) ) {
            $ordered[] = $map[ $goal_id ];
        }
    }

    return $ordered;
}

private static function composite_current_child_rows_for_parent( $user_id, $parent_goal_id ) {
    $rows = array();
    if ( class_exists( 'TCT_DB' ) && is_callable( array( 'TCT_DB', 'get_composite_children_for_parent' ) ) ) {
        $rows = TCT_DB::get_composite_children_for_parent( $parent_goal_id, $user_id );
    }
    return is_array( $rows ) ? array_values( $rows ) : array();
}

private static function composite_child_sources_from_snapshot( $user_id, $snapshot_children ) {
    $snapshot_children = is_array( $snapshot_children ) ? array_values( $snapshot_children ) : array();
    if ( empty( $snapshot_children ) ) {
        return array();
    }

    $ordered_ids = array();
    $snapshot_map = array();
    foreach ( $snapshot_children as $index => $snapshot_child ) {
        if ( ! is_array( $snapshot_child ) ) {
            continue;
        }
        $goal_id = isset( $snapshot_child['goal_id'] ) ? (int) $snapshot_child['goal_id'] : 0;
        if ( $goal_id <= 0 ) {
            continue;
        }
        if ( ! isset( $snapshot_child['order'] ) ) {
            $snapshot_child['order'] = $index + 1;
        }
        $ordered_ids[] = $goal_id;
        $snapshot_map[ $goal_id ] = $snapshot_child;
    }

    if ( empty( $ordered_ids ) ) {
        return array();
    }

    $live_rows = self::composite_fetch_goal_rows_by_ids( $user_id, $ordered_ids );
    $live_map = array();
    foreach ( $live_rows as $live_row ) {
        $goal_id = isset( $live_row['id'] ) ? (int) $live_row['id'] : 0;
        if ( $goal_id > 0 ) {
            $live_map[ $goal_id ] = $live_row;
        }
    }

    $out = array();
    foreach ( $ordered_ids as $goal_id ) {
        if ( isset( $live_map[ $goal_id ] ) ) {
            $source = $live_map[ $goal_id ];
            $source['composite_snapshot_child'] = $snapshot_map[ $goal_id ];
            $source['composite_snapshot_only'] = 0;
            $out[] = $source;
            continue;
        }

        $snapshot_child = $snapshot_map[ $goal_id ];
        $out[] = array(
            'id' => $goal_id,
            'goal_name' => isset( $snapshot_child['goal_name'] ) ? (string) $snapshot_child['goal_name'] : '',
            'goal_type' => isset( $snapshot_child['goal_type'] ) ? (string) $snapshot_child['goal_type'] : '',
            'target' => isset( $snapshot_child['target'] ) ? (int) $snapshot_child['target'] : 0,
            'points_per_completion' => 0,
            'composite_snapshot_child' => $snapshot_child,
            'composite_snapshot_only' => 1,
        );
    }

    return $out;
}

private static function composite_build_parent_window_payload( $user_id, $parent_row, $window_start, $window_end, $child_sources ) {
    $children = array();
    $window_end_ref = $window_end;

    foreach ( (array) $child_sources as $index => $child_source ) {
        $snapshot_child = isset( $child_source['composite_snapshot_child'] ) && is_array( $child_source['composite_snapshot_child'] ) ? $child_source['composite_snapshot_child'] : array();
        $goal_id = isset( $child_source['id'] ) ? (int) $child_source['id'] : ( isset( $snapshot_child['goal_id'] ) ? (int) $snapshot_child['goal_id'] : 0 );
        $goal_type = isset( $child_source['goal_type'] ) ? (string) $child_source['goal_type'] : ( isset( $snapshot_child['goal_type'] ) ? (string) $snapshot_child['goal_type'] : '' );
        $use_snapshot_only = ! empty( $child_source['composite_snapshot_only'] );

        if ( ! $use_snapshot_only && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_composite_child_goal_candidate' ) ) ) {
            if ( ! TCT_Utils::is_composite_child_goal_candidate( $child_source ) && ! empty( $snapshot_child ) ) {
                $use_snapshot_only = true;
            }
        }

        if ( ! $use_snapshot_only ) {
            $achieved = 0;
            $child_penalties_fired = 0;
            $settlement_context = class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'composite_child_settlement_context' ) )
                ? TCT_Interval::composite_child_settlement_context( $child_source, $window_end_ref, null, array() )
                : array();

            $child_window_start = isset( $settlement_context['window_start_utc_mysql'] ) ? (string) $settlement_context['window_start_utc_mysql'] : '';
            $child_window_end = isset( $settlement_context['window_end_utc_mysql'] ) ? (string) $settlement_context['window_end_utc_mysql'] : '';
            if ( '' === $child_window_start && isset( $snapshot_child['window_start_utc_mysql'] ) ) {
                $child_window_start = (string) $snapshot_child['window_start_utc_mysql'];
            }
            if ( '' === $child_window_end && isset( $snapshot_child['window_end_utc_mysql'] ) ) {
                $child_window_end = (string) $snapshot_child['window_end_utc_mysql'];
            }

            if ( $goal_id > 0 && '' !== $child_window_start && '' !== $child_window_end ) {
                $achieved = self::composite_count_child_achieved_for_window( $user_id, $goal_id, $goal_type, $child_window_start, $child_window_end );
                $child_penalties_fired = self::composite_count_child_penalties_fired( $user_id, $goal_id, $child_window_start, $child_window_end );
            }

            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_child_settlement_row_from_goal_row' ) ) ) {
                $children[] = TCT_Utils::composite_goal_child_settlement_row_from_goal_row(
                    $child_source,
                    $window_end_ref,
                    array(
                        'achieved' => $achieved,
                        'child_penalties_fired' => $child_penalties_fired,
                    )
                );
                continue;
            }
        }

        $child_window_start = isset( $snapshot_child['window_start_utc_mysql'] ) ? (string) $snapshot_child['window_start_utc_mysql'] : '';
        $child_window_end = isset( $snapshot_child['window_end_utc_mysql'] ) ? (string) $snapshot_child['window_end_utc_mysql'] : '';
        $achieved = isset( $snapshot_child['achieved'] ) ? (int) $snapshot_child['achieved'] : 0;
        $child_penalties_fired = isset( $snapshot_child['child_penalties_fired'] ) ? max( 0, (int) $snapshot_child['child_penalties_fired'] ) : 0;
        if ( $goal_id > 0 && '' !== $child_window_start && '' !== $child_window_end ) {
            $achieved = self::composite_count_child_achieved_for_window( $user_id, $goal_id, $goal_type, $child_window_start, $child_window_end );
            $child_penalties_live = self::composite_count_child_penalties_fired( $user_id, $goal_id, $child_window_start, $child_window_end );
            if ( $child_penalties_live > 0 ) {
                $child_penalties_fired = $child_penalties_live;
            }
        }

        $seed = array(
            'goal_id' => $goal_id,
            'goal_name' => isset( $child_source['goal_name'] ) ? $child_source['goal_name'] : ( isset( $snapshot_child['goal_name'] ) ? $snapshot_child['goal_name'] : '' ),
            'goal_type' => $goal_type,
            'target' => isset( $snapshot_child['target'] ) ? (int) $snapshot_child['target'] : ( isset( $child_source['target'] ) ? (int) $child_source['target'] : 0 ),
            'achieved' => $achieved,
            'eligible' => ! empty( $snapshot_child['eligible'] ),
            'active_at_settlement' => ! empty( $snapshot_child['active_at_settlement'] ),
            'paused_at_settlement' => ! empty( $snapshot_child['paused_at_settlement'] ),
            'bonus_points' => isset( $snapshot_child['bonus_points'] ) ? (int) $snapshot_child['bonus_points'] : 0,
            'penalty_points' => isset( $snapshot_child['penalty_points'] ) ? (int) $snapshot_child['penalty_points'] : 0,
            'child_penalties_fired' => $child_penalties_fired,
            'settlement_context' => array(
                'window_start_utc_mysql' => $child_window_start,
                'window_end_utc_mysql' => $child_window_end,
                'eligible' => ! empty( $snapshot_child['eligible'] ),
                'active_at_settlement' => ! empty( $snapshot_child['active_at_settlement'] ),
                'paused_at_settlement' => ! empty( $snapshot_child['paused_at_settlement'] ),
            ),
        );

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_normalize_child_settlement_row' ) ) ) {
            $children[] = TCT_Utils::composite_goal_normalize_child_settlement_row( $seed, $index );
        }
    }

    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_build_settlement_payload' ) ) ) {
        return TCT_Utils::composite_goal_build_settlement_payload(
            $children,
            array(
                'parent_goal_id' => isset( $parent_row['id'] ) ? (int) $parent_row['id'] : 0,
                'parent_goal_name' => isset( $parent_row['goal_name'] ) ? (string) $parent_row['goal_name'] : '',
                'settlement_at' => $window_end_ref,
                'window_start' => $window_start,
                'window_end' => $window_end,
            )
        );
    }

    return null;
}

private static function composite_count_child_achieved_for_window( $user_id, $goal_id, $goal_type, $window_start, $window_end ) {
    $user_id = (int) $user_id;
    $goal_id = (int) $goal_id;
    $window_start = is_string( $window_start ) ? trim( (string) $window_start ) : '';
    $window_end = is_string( $window_end ) ? trim( (string) $window_end ) : '';
    if ( $user_id <= 0 || $goal_id <= 0 || '' === $window_start || '' === $window_end ) {
        return 0;
    }

    if ( self::is_anki_cards_goal_type( $goal_type ) ) {
        return self::sum_anki_cards_in_window( $user_id, $goal_id, $window_start, $window_end );
    }

    global $wpdb;
    $completions = TCT_DB::table_completions();
    $ledger = TCT_DB::table_ledger();
    return (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$completions} c
             WHERE c.user_id = %d
               AND c.goal_id = %d
               AND c.completed_at >= %s
               AND c.completed_at < %s
               AND c.source NOT IN ('auto_miss', 'auto_due_miss')
               AND NOT EXISTS (
                    SELECT 1
                    FROM {$ledger} l
                    WHERE l.user_id = c.user_id
                      AND l.goal_id = c.goal_id
                      AND l.event_type = 'completion'
                      AND l.event_key = CONCAT('c_', SHA1(CONCAT(c.source, ':', c.source_ref, ':', c.goal_id)))
                      AND (
                            COALESCE(l.details,'') LIKE '[manual fail]%%'
                            OR COALESCE(l.details,'') LIKE '[auto miss]%%'
                            OR COALESCE(l.details,'') LIKE '[auto due miss]%%'
                      )
               )",
            $user_id,
            $goal_id,
            $window_start,
            $window_end
        )
    );
}

private static function composite_count_child_penalties_fired( $user_id, $goal_id, $window_start, $window_end ) {
    global $wpdb;

    $user_id = (int) $user_id;
    $goal_id = (int) $goal_id;
    $window_start = is_string( $window_start ) ? trim( (string) $window_start ) : '';
    $window_end = is_string( $window_end ) ? trim( (string) $window_end ) : '';
    if ( $user_id <= 0 || $goal_id <= 0 || '' === $window_start || '' === $window_end ) {
        return 0;
    }

    $ledger = TCT_DB::table_ledger();
    $interval_penalties = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ABS(COALESCE(SUM(points), 0)) FROM {$ledger} WHERE user_id = %d AND goal_id = %d AND event_type = 'goal_bonus' AND points < 0 AND window_start = %s AND window_end = %s",
            $user_id,
            $goal_id,
            $window_start,
            $window_end
        )
    );
    $failish_completion_penalties = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ABS(COALESCE(SUM(points), 0)) FROM {$ledger} WHERE user_id = %d AND goal_id = %d AND event_type = 'completion' AND points < 0 AND occurred_at >= %s AND occurred_at < %s AND (COALESCE(details,'') LIKE '[manual fail]%%' OR COALESCE(details,'') LIKE '[auto miss]%%' OR COALESCE(details,'') LIKE '[auto due miss]%%')",
            $user_id,
            $goal_id,
            $window_start,
            $window_end
        )
    );

    $total = abs( (float) $interval_penalties ) + abs( (float) $failish_completion_penalties );
    return max( 0, (int) round( $total ) );
}

private static function composite_desired_points_from_payload( $payload ) {
    $bonus_points = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_finalize_bonus_points' ) )
        ? (int) TCT_Utils::composite_goal_finalize_bonus_points( isset( $payload['bonus_raw'] ) ? $payload['bonus_raw'] : 0 )
        : (int) round( isset( $payload['bonus_raw'] ) ? (float) $payload['bonus_raw'] : 0 );
    $perfection_points = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_finalize_bonus_points' ) )
        ? (int) TCT_Utils::composite_goal_finalize_bonus_points( isset( $payload['perfection_bonus_raw'] ) ? $payload['perfection_bonus_raw'] : 0 )
        : (int) round( isset( $payload['perfection_bonus_raw'] ) ? (float) $payload['perfection_bonus_raw'] : 0 );
    $penalty_points = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_finalize_penalty_points' ) )
        ? (int) TCT_Utils::composite_goal_finalize_penalty_points( isset( $payload['penalty_capped_raw_magnitude'] ) ? $payload['penalty_capped_raw_magnitude'] : 0 )
        : -1 * (int) round( abs( isset( $payload['penalty_capped_raw_magnitude'] ) ? (float) $payload['penalty_capped_raw_magnitude'] : 0 ) );

    return array(
        'bonus_points' => (int) $bonus_points,
        'perfection_points' => (int) $perfection_points,
        'penalty_points' => (int) $penalty_points,
        'total_points' => (int) $bonus_points + (int) $perfection_points + (int) $penalty_points,
    );
}

private static function composite_existing_points_total( $rows ) {
    $total = 0;
    foreach ( (array) $rows as $row ) {
        $total += isset( $row['points'] ) ? (int) $row['points'] : 0;
    }
    return (int) $total;
}

private static function composite_event_key( $user_id, $parent_goal_id, $window_start, $window_end, $subtype ) {
    $prefix = 'cb_';
    if ( 'perfection' === $subtype ) {
        $prefix = 'cpf_';
    } elseif ( 'penalty' === $subtype ) {
        $prefix = 'cpn_';
    }

    return $prefix . sha1( implode( '|', array( (string) (int) $user_id, (string) (int) $parent_goal_id, (string) $window_start, (string) $window_end, (string) $subtype ) ) );
}

private static function composite_adjustment_event_key( $user_id, $parent_goal_id, $window_start, $window_end, $posted_total_before, $desired ) {
    return 'caj_' . sha1(
        implode(
            '|',
            array(
                (string) (int) $user_id,
                (string) (int) $parent_goal_id,
                (string) $window_start,
                (string) $window_end,
                (string) (int) $posted_total_before,
                (string) (int) ( isset( $desired['bonus_points'] ) ? $desired['bonus_points'] : 0 ),
                (string) (int) ( isset( $desired['perfection_points'] ) ? $desired['perfection_points'] : 0 ),
                (string) (int) ( isset( $desired['penalty_points'] ) ? $desired['penalty_points'] : 0 ),
                (string) (int) ( isset( $desired['total_points'] ) ? $desired['total_points'] : 0 )
            )
        )
    );
}

private static function composite_event_key_exists( $user_id, $event_key ) {
    global $wpdb;

    $user_id = (int) $user_id;
    $event_key = is_string( $event_key ) ? trim( (string) $event_key ) : '';
    if ( $user_id <= 0 || '' === $event_key ) {
        return false;
    }

    $ledger = TCT_DB::table_ledger();
    $exists = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$ledger} WHERE user_id = %d AND event_key = %s",
            $user_id,
            $event_key
        )
    );

    return ( $exists > 0 );
}

private static function composite_insert_initial_window_events( $user_id, $parent_row, $window_start, $window_end, $payload, $desired ) {
    $inserted = 0;

    $bonus_key = self::composite_event_key( $user_id, isset( $parent_row['id'] ) ? $parent_row['id'] : 0, $window_start, $window_end, 'bonus' );
    $bonus_details = self::composite_snapshot_details_json(
        $parent_row,
        $payload,
        $desired,
        array(
            'event_mode' => 'initial',
            'event_type' => 'composite_bonus',
            'occurred_at' => self::interval_end_occurred_at_utc_mysql( $window_end ),
        )
    );
    if ( self::composite_insert_composite_event( $user_id, 'composite_bonus', isset( $desired['bonus_points'] ) ? (int) $desired['bonus_points'] : 0, self::interval_end_occurred_at_utc_mysql( $window_end ), $bonus_details, $bonus_key, $parent_row, $window_start, $window_end, $payload ) ) {
        $inserted++;
    }

    if ( ! empty( $desired['perfection_points'] ) ) {
        $perfection_key = self::composite_event_key( $user_id, isset( $parent_row['id'] ) ? $parent_row['id'] : 0, $window_start, $window_end, 'perfection' );
        $perfection_details = self::composite_snapshot_details_json(
            $parent_row,
            $payload,
            $desired,
            array(
                'event_mode' => 'initial',
                'event_type' => 'composite_perfection_bonus',
                'occurred_at' => self::interval_end_occurred_at_utc_mysql( $window_end ),
            )
        );
        if ( self::composite_insert_composite_event( $user_id, 'composite_perfection_bonus', (int) $desired['perfection_points'], self::interval_end_occurred_at_utc_mysql( $window_end ), $perfection_details, $perfection_key, $parent_row, $window_start, $window_end, $payload ) ) {
            $inserted++;
        }
    }

    if ( ! empty( $desired['penalty_points'] ) ) {
        $penalty_key = self::composite_event_key( $user_id, isset( $parent_row['id'] ) ? $parent_row['id'] : 0, $window_start, $window_end, 'penalty' );
        $penalty_details = self::composite_snapshot_details_json(
            $parent_row,
            $payload,
            $desired,
            array(
                'event_mode' => 'initial',
                'event_type' => 'composite_penalty',
                'occurred_at' => self::interval_end_occurred_at_utc_mysql( $window_end ),
            )
        );
        if ( self::composite_insert_composite_event( $user_id, 'composite_penalty', (int) $desired['penalty_points'], self::interval_end_occurred_at_utc_mysql( $window_end ), $penalty_details, $penalty_key, $parent_row, $window_start, $window_end, $payload ) ) {
            $inserted++;
        }
    }

    return (int) $inserted;
}

private static function composite_insert_adjustment_event( $user_id, $parent_row, $window_start, $window_end, $payload, $desired, $existing_rows, $now ) {
    $posted_total_before = self::composite_existing_points_total( $existing_rows );
    $desired_total = isset( $desired['total_points'] ) ? (int) $desired['total_points'] : 0;
    $delta_points = (int) $desired_total - (int) $posted_total_before;
    if ( 0 === $delta_points ) {
        return 0;
    }

    $event_key = self::composite_adjustment_event_key( $user_id, isset( $parent_row['id'] ) ? $parent_row['id'] : 0, $window_start, $window_end, $posted_total_before, $desired );
    if ( self::composite_event_key_exists( $user_id, $event_key ) ) {
        return 0;
    }

    $occurred_at = is_string( $now ) && '' !== trim( $now ) ? trim( (string) $now ) : current_time( 'mysql', true );
    $details = self::composite_snapshot_details_json(
        $parent_row,
        $payload,
        $desired,
        array(
            'event_mode' => 'adjustment',
            'event_type' => 'composite_adjustment',
            'occurred_at' => $occurred_at,
            'posted_total_before' => (int) $posted_total_before,
            'delta_points' => (int) $delta_points,
            'existing_event_count' => is_array( $existing_rows ) ? count( $existing_rows ) : 0,
        )
    );

    return self::composite_insert_composite_event( $user_id, 'composite_adjustment', $delta_points, $occurred_at, $details, $event_key, $parent_row, $window_start, $window_end, $payload ) ? 1 : 0;
}

private static function composite_insert_composite_event( $user_id, $event_type, $points, $occurred_at, $details, $event_key, $parent_row, $window_start, $window_end, $payload ) {
    $user_id = (int) $user_id;
    if ( $user_id <= 0 ) {
        return false;
    }

    $event_key = is_string( $event_key ) ? trim( (string) $event_key ) : '';
    if ( '' === $event_key ) {
        return false;
    }
    if ( self::composite_event_key_exists( $user_id, $event_key ) ) {
        return false;
    }

    $interval = self::composite_parent_interval_from_row( $parent_row );
    $args = array(
        'event_key' => $event_key,
        'goal_id' => isset( $parent_row['id'] ) ? (int) $parent_row['id'] : 0,
        'goal_name' => isset( $parent_row['goal_name'] ) ? (string) $parent_row['goal_name'] : '',
        'label_name' => isset( $parent_row['label_name'] ) ? (string) $parent_row['label_name'] : '',
        'interval_unit' => is_array( $interval ) && isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : '',
        'interval_mode' => is_array( $interval ) && isset( $interval['period_mode'] ) ? (string) $interval['period_mode'] : '',
        'interval_target' => is_array( $interval ) && isset( $interval['target'] ) ? (int) $interval['target'] : 0,
        'bonus_points' => abs( (int) $points ),
        'window_start' => $window_start,
        'window_end' => $window_end,
        'met' => ! empty( $payload['is_complete'] ) ? 1 : 0,
    );

    $result = self::insert_custom_event( $user_id, $event_type, (int) $points, $occurred_at, $details, $args );
    return ! empty( $result );
}

private static function composite_snapshot_details_json( $parent_row, $payload, $desired, $meta = array() ) {
    $event_mode = isset( $meta['event_mode'] ) ? sanitize_text_field( (string) $meta['event_mode'] ) : 'settlement';
    $event_type = isset( $meta['event_type'] ) ? sanitize_text_field( (string) $meta['event_type'] ) : '';
    $occurred_at = isset( $meta['occurred_at'] ) ? (string) $meta['occurred_at'] : '';

    $snapshot = array(
        'version' => 1,
        'event_mode' => $event_mode,
        'event_type' => $event_type,
        'occurred_at' => $occurred_at,
        'parent_goal_id' => isset( $parent_row['id'] ) ? (int) $parent_row['id'] : 0,
        'parent_goal_name' => isset( $parent_row['goal_name'] ) ? (string) $parent_row['goal_name'] : '',
        'parent_goal_type' => isset( $parent_row['goal_type'] ) ? (string) $parent_row['goal_type'] : '',
        'window_start' => isset( $payload['window_start'] ) ? (string) $payload['window_start'] : '',
        'window_end' => isset( $payload['window_end'] ) ? (string) $payload['window_end'] : '',
        'progress_exponent' => round( (float) ( isset( $payload['progress_exponent'] ) ? $payload['progress_exponent'] : 0 ), 6 ),
        'perfect_bonus_rate' => round( (float) ( isset( $payload['perfect_bonus_rate'] ) ? $payload['perfect_bonus_rate'] : 0 ), 6 ),
        'ratio' => round( (float) ( isset( $payload['ratio'] ) ? $payload['ratio'] : 0 ), 6 ),
        'bmax' => round( (float) ( isset( $payload['bmax'] ) ? $payload['bmax'] : 0 ), 6 ),
        'pmax' => round( (float) ( isset( $payload['pmax'] ) ? $payload['pmax'] : 0 ), 6 ),
        'child_penalties_fired' => round( (float) ( isset( $payload['child_penalties_fired'] ) ? $payload['child_penalties_fired'] : 0 ), 6 ),
        'penalty_cap_remaining' => round( (float) ( isset( $payload['penalty_cap_remaining'] ) ? $payload['penalty_cap_remaining'] : 0 ), 6 ),
        'child_count' => isset( $payload['child_count'] ) ? (int) $payload['child_count'] : 0,
        'eligible_child_count' => isset( $payload['eligible_child_count'] ) ? (int) $payload['eligible_child_count'] : 0,
        'ineligible_child_count' => isset( $payload['ineligible_child_count'] ) ? (int) $payload['ineligible_child_count'] : 0,
        'has_eligible_children' => ! empty( $payload['has_eligible_children'] ) ? 1 : 0,
        'has_scoring_exposure' => ! empty( $payload['has_scoring_exposure'] ) ? 1 : 0,
        'treated_complete' => ! empty( $payload['treated_complete'] ) ? 1 : 0,
        'is_complete' => ! empty( $payload['is_complete'] ) ? 1 : 0,
        'is_perfect' => ! empty( $payload['is_perfect'] ) ? 1 : 0,
        'zero_exposure_reason' => isset( $payload['zero_exposure_reason'] ) ? (string) $payload['zero_exposure_reason'] : '',
        'desired_bonus_points' => isset( $desired['bonus_points'] ) ? (int) $desired['bonus_points'] : 0,
        'desired_perfection_points' => isset( $desired['perfection_points'] ) ? (int) $desired['perfection_points'] : 0,
        'desired_penalty_points' => isset( $desired['penalty_points'] ) ? (int) $desired['penalty_points'] : 0,
        'desired_total_points' => isset( $desired['total_points'] ) ? (int) $desired['total_points'] : 0,
        'children' => self::composite_snapshot_children_for_json( isset( $payload['children'] ) ? $payload['children'] : array(), 'full' ),
    );

    foreach ( array( 'posted_total_before', 'delta_points', 'existing_event_count' ) as $meta_key ) {
        if ( isset( $meta[ $meta_key ] ) ) {
            $snapshot[ $meta_key ] = (int) $meta[ $meta_key ];
        }
    }

    $json = function_exists( 'wp_json_encode' ) ? wp_json_encode( $snapshot, JSON_UNESCAPED_SLASHES ) : json_encode( $snapshot );
    if ( is_string( $json ) && strlen( $json ) <= 60000 ) {
        return $json;
    }

    $snapshot['snapshot_mode'] = 'compact';
    $snapshot['children'] = self::composite_snapshot_children_for_json( isset( $payload['children'] ) ? $payload['children'] : array(), 'compact' );
    $json = function_exists( 'wp_json_encode' ) ? wp_json_encode( $snapshot, JSON_UNESCAPED_SLASHES ) : json_encode( $snapshot );
    if ( is_string( $json ) && strlen( $json ) <= 60000 ) {
        return $json;
    }

    $snapshot['snapshot_mode'] = 'minimal';
    $snapshot['children'] = self::composite_snapshot_children_for_json( isset( $payload['children'] ) ? $payload['children'] : array(), 'minimal' );
    $json = function_exists( 'wp_json_encode' ) ? wp_json_encode( $snapshot, JSON_UNESCAPED_SLASHES ) : json_encode( $snapshot );

    return is_string( $json ) ? $json : '';
}

private static function composite_snapshot_children_for_json( $children, $mode = 'full' ) {
    $out = array();
    $mode = is_string( $mode ) ? strtolower( trim( $mode ) ) : 'full';

    foreach ( (array) $children as $index => $child ) {
        if ( ! is_array( $child ) ) {
            continue;
        }
        $base = array(
            'order' => $index + 1,
            'goal_id' => isset( $child['goal_id'] ) ? (int) $child['goal_id'] : 0,
        );

        if ( 'minimal' === $mode ) {
            $out[] = $base;
            continue;
        }

        $base['eligible'] = ! empty( $child['eligible'] ) ? 1 : 0;
        $base['active_at_settlement'] = ! empty( $child['active_at_settlement'] ) ? 1 : 0;
        $base['paused_at_settlement'] = ! empty( $child['paused_at_settlement'] ) ? 1 : 0;
        $base['target'] = isset( $child['target'] ) ? (int) $child['target'] : 0;
        $base['achieved'] = isset( $child['achieved'] ) ? (int) $child['achieved'] : 0;
        $base['completion_ratio'] = round( (float) ( isset( $child['completion_ratio'] ) ? $child['completion_ratio'] : 0 ), 6 );
        $base['bonus_points'] = isset( $child['bonus_points'] ) ? (int) $child['bonus_points'] : 0;
        $base['penalty_points'] = isset( $child['penalty_points'] ) ? (int) $child['penalty_points'] : 0;
        $base['child_penalties_fired'] = isset( $child['child_penalties_fired'] ) ? (int) round( abs( (float) $child['child_penalties_fired'] ) ) : 0;

        if ( 'full' === $mode ) {
            $base['goal_name'] = isset( $child['goal_name'] ) ? (string) $child['goal_name'] : '';
            $base['goal_type'] = isset( $child['goal_type'] ) ? (string) $child['goal_type'] : '';
            $base['window_start_utc_mysql'] = isset( $child['window_start_utc_mysql'] ) ? (string) $child['window_start_utc_mysql'] : '';
            $base['window_end_utc_mysql'] = isset( $child['window_end_utc_mysql'] ) ? (string) $child['window_end_utc_mysql'] : '';
        }

        $out[] = $base;
    }

    return $out;
}

private static function is_anki_cards_goal_type( $goal_type ) { return class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type ) : ( 'anki_cards' === strtolower( trim( (string) $goal_type ) ) ); } private static function sum_anki_cards_in_window( $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; if ( $user_id <= 0 || $goal_id <= 0 ) { return 0; } $completions = TCT_DB::table_completions(); $notes = $wpdb->get_col( $wpdb->prepare( "SELECT note FROM {$completions} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc_mysql, $end_utc_mysql ) ); if ( ! is_array( $notes ) || empty( $notes ) ) { return 0; } $sum = 0; foreach ( $notes as $note ) { $sum += ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'parse_anki_cards_completion_note' ) ) ) ? (int) TCT_Utils::parse_anki_cards_completion_note( $note ) : 0; } return max( 0, (int) $sum ); } private static function reconcile_anki_cards_goal_bonus( $user_id, $g, $since_for_goal, $until_utc_mysql, $now, $tz, $now_tz, &$events, $goal_created_at_ts = 0, $goal_updated_at_ts = 0 ) {
    $goal_id = isset( $g['id'] ) ? (int) $g['id'] : 0;
    if ( $goal_id <= 0 ) {
        return;
    }

    $label_name = isset( $g['label_name'] ) ? (string) $g['label_name'] : '';
    $goal_name = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : $label_name;
    $points_enabled_at = isset( $g['points_enabled_at'] ) ? trim( (string) $g['points_enabled_at'] ) : '';
    if ( '' === $points_enabled_at || '0000-00-00 00:00:00' === $points_enabled_at ) {
        return;
    }

    $ppc = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0;
    if ( $ppc <= 0 ) {
        return;
    }

    $target = isset( $g['target'] ) ? (int) $g['target'] : 0;
    if ( $target <= 0 ) {
        return;
    }

    $unit = isset( $g['period_unit'] ) ? sanitize_text_field( (string) $g['period_unit'] ) : 'day';
    $span = isset( $g['period_span'] ) ? max( 1, (int) $g['period_span'] ) : 1;
    $mode = isset( $g['period_mode'] ) && is_string( $g['period_mode'] ) && '' !== trim( (string) $g['period_mode'] ) ? (string) $g['period_mode'] : 'calendar';

    $availability = self::normalize_goal_availability_for_ledger( $g );
    $availability_enabled = self::goal_uses_availability_cycle_for_ledger( $g, $availability );

    if ( $availability_enabled ) {
        $windows = self::availability_windows_for_goal_bonus( $g, $since_for_goal, $until_utc_mysql, $tz );
        if ( empty( $windows ) ) {
            return;
        }
        $raw_query_start = self::availability_raw_query_start_utc_mysql( $availability, $windows, $points_enabled_at, $tz );
        $weighted_series = self::build_weighted_completion_series( $user_id, $goal_id, 'anki_cards', $availability, $raw_query_start, $until_utc_mysql, $tz );
    } else {
        $windows = self::generate_completed_windows( $now_tz, $tz, $unit, $span, $since_for_goal, $until_utc_mysql );
        if ( empty( $windows ) ) {
            return;
        }
        $weighted_series = array();
    }

    foreach ( $windows as $w ) {
        $start_utc_mysql = isset( $w['start_utc_mysql'] ) ? (string) $w['start_utc_mysql'] : '';
        $end_utc_mysql = isset( $w['end_utc_mysql'] ) ? (string) $w['end_utc_mysql'] : '';
        if ( '' === $start_utc_mysql || '' === $end_utc_mysql ) {
            continue;
        }
        if ( strcmp( $end_utc_mysql, $points_enabled_at ) <= 0 ) {
            continue;
        }

        $eval_start_utc_mysql = $start_utc_mysql;
        if ( strcmp( $eval_start_utc_mysql, $points_enabled_at ) < 0 ) {
            $eval_start_utc_mysql = $points_enabled_at;
        }

        $window_start_ts = isset( $w['start_ts'] ) ? (int) $w['start_ts'] : (int) strtotime( $start_utc_mysql . ' UTC' );
        $window_end_ts = isset( $w['end_ts'] ) ? (int) $w['end_ts'] : (int) strtotime( $end_utc_mysql . ' UTC' );
        $effective_target = $target;
        $prorate_anchor = TCT_Utils::compute_prorate_anchor_ts( $goal_created_at_ts, $goal_updated_at_ts, $window_start_ts );
        if ( $prorate_anchor > 0 ) {
            if ( $availability_enabled ) {
                $effective_target = self::availability_prorated_target( $target, $prorate_anchor, $w, $availability, $tz );
            } else {
                $effective_target = TCT_Utils::compute_prorated_target( $target, $prorate_anchor, $window_start_ts, $window_end_ts );
            }
        }
        if ( $effective_target <= 0 ) {
            continue;
        }

        if ( $availability_enabled ) {
            $actual = self::count_weighted_in_range_utc( $weighted_series, $eval_start_utc_mysql, $end_utc_mysql );
        } else {
            $actual = self::sum_anki_cards_in_window( $user_id, $goal_id, $eval_start_utc_mysql, $end_utc_mysql );
        }

        $pts = ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_anki_cards_adjustment_points' ) ) )
            ? (int) TCT_Utils::compute_anki_cards_adjustment_points( $ppc, $effective_target, $actual )
            : 0;
        $event_key = 'b_' . sha1( implode( '|', array( (string) $user_id, (string) $goal_id, 'anki_cards', $unit, (string) $span, $mode, $end_utc_mysql ) ) );
        $target_label = (int) $effective_target;
        if ( $effective_target !== $target ) {
            $target_label .= ' pro-rated from ' . (int) $target;
        }
        $pts_label = ( $pts > 0 ? '+' : '' ) . (string) (int) $pts;

        $events[] = array(
            'user_id' => (int) $user_id,
            'event_key' => $event_key,
            'event_type' => 'goal_bonus',
            'points' => (int) $pts,
            'occurred_at' => self::interval_end_occurred_at_utc_mysql( $end_utc_mysql ),
            'goal_id' => (int) $goal_id,
            'goal_name' => $goal_name,
            'label_name' => $label_name,
            'interval_unit' => $unit,
            'interval_mode' => $mode,
            'interval_target' => (int) $effective_target,
            'bonus_points' => abs( (int) $pts ),
            'window_start' => $start_utc_mysql,
            'window_end' => $end_utc_mysql,
            'met' => ( $actual >= $effective_target ) ? 1 : 0,
            'details' => 'Anki adjustment (' . $pts_label . '; ' . (int) $actual . '/' . $target_label . ' cards)',
            'created_at' => $now,
            'updated_at' => $now,
        );
    }
} private static function extract_intervals_with_bonus( $goal_row ) { $ppc = isset( $goal_row['points_per_completion'] ) ? (int) $goal_row['points_per_completion'] : 0; if ( $ppc <= 0 ) { return array(); } $interval = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) { $interval = TCT_Interval::normalize_goal_interval_from_row( $goal_row ); } if ( ! is_array( $interval ) ) { $target = isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0; if ( $target <= 0 ) { return array(); } $interval = array( 'target' => (int) $target, 'period_unit' => isset( $goal_row['period_unit'] ) ? sanitize_text_field( (string) $goal_row['period_unit'] ) : 'week', 'period_span' => isset( $goal_row['period_span'] ) ? max( 1, (int) $goal_row['period_span'] ) : 1, 'period_mode' => 'calendar', ); } $target = isset( $interval['target'] ) ? (int) $interval['target'] : 0; if ( $target <= 0 ) { return array(); } $bonus = TCT_Utils::compute_bonus_points( $ppc, $target ); return array( array( 'target' => (int) $target, 'period_unit' => isset( $interval['period_unit'] ) ? sanitize_text_field( (string) $interval['period_unit'] ) : 'week', 'period_span' => isset( $interval['period_span'] ) ? max( 1, (int) $interval['period_span'] ) : 1, 'period_mode' => 'calendar', 'bonus_points' => (int) $bonus, ), ); } private static function generate_completed_windows( DateTimeImmutable $now_tz, DateTimeZone $tz, $unit, $span, $since_utc_mysql, $until_utc_mysql, $goal_row = null ) { $since_ts = strtotime( $since_utc_mysql . ' UTC' ); $until_ts = strtotime( $until_utc_mysql . ' UTC' ); if ( false === $since_ts || false === $until_ts ) { return array(); } $span = is_numeric( $span ) ? (int) $span : 1; if ( $span < 1 ) { $span = 1; } $period_end_tz = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $b_now = ( is_array( $goal_row ) && is_callable( array( 'TCT_Interval', 'goal_interval_loop_bounds_at_datetime' ) ) ) ? TCT_Interval::goal_interval_loop_bounds_at_datetime( $goal_row, $now_tz ) : TCT_Interval::current_loop_bounds( $now_tz, $unit, $span ); if ( is_array( $b_now ) && isset( $b_now['start'] ) && ( $b_now['start'] instanceof DateTimeImmutable ) ) { $period_end_tz = $b_now['start']; } } if ( ! ( $period_end_tz instanceof DateTimeImmutable ) ) { $period_end_tz = self::start_of_current_period( $now_tz, $unit ); } $until_tz = ( new DateTimeImmutable( '@' . (int) $until_ts ) )->setTimezone( $tz ); if ( $period_end_tz > $until_tz ) { if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $b_until = ( is_array( $goal_row ) && is_callable( array( 'TCT_Interval', 'goal_interval_loop_bounds_at_datetime' ) ) ) ? TCT_Interval::goal_interval_loop_bounds_at_datetime( $goal_row, $until_tz ) : TCT_Interval::current_loop_bounds( $until_tz, $unit, $span ); if ( is_array( $b_until ) && isset( $b_until['start'] ) && ( $b_until['start'] instanceof DateTimeImmutable ) ) { $period_end_tz = $b_until['start']; } else { $period_end_tz = self::start_of_current_period( $until_tz, $unit ); } } else { $period_end_tz = self::start_of_current_period( $until_tz, $unit ); } } $windows = array(); $cursor_end = $period_end_tz; $guard = 0; while ( $cursor_end instanceof DateTimeImmutable ) { $guard++; if ( $guard > 5000 ) { break; } $cursor_end_utc_mysql = TCT_Utils::dt_to_mysql_utc( $cursor_end ); $cursor_end_ts = strtotime( $cursor_end_utc_mysql . ' UTC' ); if ( false === $cursor_end_ts || $cursor_end_ts <= $since_ts ) { break; } $probe = $cursor_end->modify( '-1 second' ); $cursor_start = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $b_prev = ( is_array( $goal_row ) && is_callable( array( 'TCT_Interval', 'goal_interval_loop_bounds_at_datetime' ) ) ) ? TCT_Interval::goal_interval_loop_bounds_at_datetime( $goal_row, $probe ) : TCT_Interval::current_loop_bounds( $probe, $unit, $span ); if ( is_array( $b_prev ) && isset( $b_prev['start'] ) && ( $b_prev['start'] instanceof DateTimeImmutable ) ) { $cursor_start = $b_prev['start']; } } if ( ! ( $cursor_start instanceof DateTimeImmutable ) ) { $cursor_start = self::subtract_one_period( $cursor_end, $unit ); } if ( ! ( $cursor_start instanceof DateTimeImmutable ) || $cursor_start >= $cursor_end ) { break; } $windows[] = array( 'start_utc_mysql' => TCT_Utils::dt_to_mysql_utc( $cursor_start ), 'end_utc_mysql' => $cursor_end_utc_mysql, ); $cursor_end = $cursor_start; } return $windows; }

private static function normalize_goal_availability_for_ledger( $goal_row ) {
    if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) ) ) {
        $cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal_row );
        return is_array( $cfg ) ? $cfg : array( 'enabled' => false );
    }
    return array( 'enabled' => false );
}

private static function goal_uses_availability_cycle_for_ledger( $goal_row, $availability = null ) {
    if ( ! is_array( $availability ) ) {
        $availability = self::normalize_goal_availability_for_ledger( $goal_row );
    }
    if ( empty( $availability['enabled'] ) ) {
        return false;
    }
    return class_exists( 'TCT_Interval' )
        && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) )
        && (bool) TCT_Interval::is_goal_eligible_for_availability_cycle( $goal_row );
}

private static function availability_windows_for_goal_bonus( $goal_row, $since_utc_mysql, $until_utc_mysql, DateTimeZone $tz ) {
    if ( ! class_exists( 'TCT_Interval' ) || ! is_callable( array( 'TCT_Interval', 'availability_cycle_completed_windows_for_range' ) ) ) {
        return array();
    }
    $windows = TCT_Interval::availability_cycle_completed_windows_for_range( $goal_row, $since_utc_mysql, $until_utc_mysql, $tz );
    return is_array( $windows ) ? $windows : array();
}

private static function availability_raw_query_start_utc_mysql( $availability, $windows, $points_enabled_at, DateTimeZone $tz ) {
    $points_enabled_at = is_string( $points_enabled_at ) ? trim( (string) $points_enabled_at ) : '';
    $earliest_start = null;

    if ( is_array( $windows ) ) {
        foreach ( $windows as $window ) {
            $start_dt = null;
            if ( isset( $window['start'] ) && ( $window['start'] instanceof DateTimeImmutable ) ) {
                $start_dt = $window['start']->setTimezone( $tz );
            } elseif ( isset( $window['start_utc_mysql'] ) && is_string( $window['start_utc_mysql'] ) && '' !== trim( (string) $window['start_utc_mysql'] ) ) {
                $tmp_ts = strtotime( trim( (string) $window['start_utc_mysql'] ) . ' UTC' );
                if ( false !== $tmp_ts ) {
                    $start_dt = ( new DateTimeImmutable( '@' . (int) $tmp_ts ) )->setTimezone( $tz );
                }
            }
            if ( ! ( $start_dt instanceof DateTimeImmutable ) ) {
                continue;
            }
            if ( ! ( $earliest_start instanceof DateTimeImmutable ) || $start_dt->getTimestamp() < $earliest_start->getTimestamp() ) {
                $earliest_start = $start_dt;
            }
        }
    }

    if ( ! ( $earliest_start instanceof DateTimeImmutable ) ) {
        return $points_enabled_at;
    }

    $pause_days = isset( $availability['pause_duration'] ) ? max( 0, (int) $availability['pause_duration'] ) : 0;
    if ( $pause_days > 0 ) {
        $earliest_start = $earliest_start->modify( '-' . $pause_days . ' days' );
    }

    $raw_query_start = $earliest_start->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
    if ( '' !== $points_enabled_at && '0000-00-00 00:00:00' !== $points_enabled_at && strcmp( $raw_query_start, $points_enabled_at ) < 0 ) {
        return $points_enabled_at;
    }

    return $raw_query_start;
}

private static function availability_completion_weight_for_goal_type( $goal_type, $note ) {
    $goal_type_l = strtolower( trim( (string) $goal_type ) );
    $is_anki_cards_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) )
        ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type_l )
        : ( 'anki_cards' === $goal_type_l );

    if ( ! $is_anki_cards_goal ) {
        return 1;
    }

    if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'parse_anki_cards_completion_note' ) ) ) {
        return max( 0, (int) TCT_Utils::parse_anki_cards_completion_note( $note ) );
    }

    return 0;
}

private static function build_weighted_completion_series( $user_id, $goal_id, $goal_type, $availability, $raw_start_utc_mysql, $raw_end_utc_mysql, DateTimeZone $tz ) {
    global $wpdb;

    $out = array(
        'timestamps' => array(),
        'prefix_weights' => array( 0 ),
        'rows_scanned' => 0,
    );

    $user_id = (int) $user_id;
    $goal_id = (int) $goal_id;
    $raw_start_utc_mysql = is_string( $raw_start_utc_mysql ) ? trim( (string) $raw_start_utc_mysql ) : '';
    $raw_end_utc_mysql = is_string( $raw_end_utc_mysql ) ? trim( (string) $raw_end_utc_mysql ) : '';
    if ( $user_id <= 0 || $goal_id <= 0 || '' === $raw_start_utc_mysql || '' === $raw_end_utc_mysql ) {
        return $out;
    }
    if ( strcmp( $raw_end_utc_mysql, $raw_start_utc_mysql ) <= 0 ) {
        return $out;
    }

    $completions_table = TCT_DB::table_completions();
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT completed_at, source, note FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s ORDER BY completed_at ASC",
            $user_id,
            $goal_id,
            $raw_start_utc_mysql,
            $raw_end_utc_mysql
        ),
        ARRAY_A
    );
    if ( ! is_array( $rows ) || empty( $rows ) ) {
        return $out;
    }

    $running = 0;
    foreach ( $rows as $row ) {
        $out['rows_scanned']++;
        $completed_at = isset( $row['completed_at'] ) ? (string) $row['completed_at'] : '';
        $source = isset( $row['source'] ) ? (string) $row['source'] : '';
        $note = isset( $row['note'] ) ? $row['note'] : '';

        $classification = class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'availability_cycle_completion_classification' ) )
            ? TCT_Interval::availability_cycle_completion_classification( $availability, $completed_at, $source, $tz )
            : array( 'counted' => true, 'excluded' => false, 'effective_ts' => ( false !== strtotime( $completed_at . ' UTC' ) ? (int) strtotime( $completed_at . ' UTC' ) : 0 ) );

        if ( empty( $classification['counted'] ) || ! empty( $classification['excluded'] ) ) {
            continue;
        }

        $effective_ts = isset( $classification['effective_ts'] ) ? (int) $classification['effective_ts'] : 0;
        if ( $effective_ts <= 0 ) {
            continue;
        }

        $weight = self::availability_completion_weight_for_goal_type( $goal_type, $note );
        if ( $weight <= 0 ) {
            continue;
        }

        $out['timestamps'][] = $effective_ts;
        $running += (int) $weight;
        $out['prefix_weights'][] = $running;
    }

    return $out;
}

private static function count_weighted_in_range_utc( $series, $start_utc_mysql, $end_utc_mysql ) {
    $timestamps = ( is_array( $series ) && isset( $series['timestamps'] ) && is_array( $series['timestamps'] ) ) ? $series['timestamps'] : array();
    $prefix_weights = ( is_array( $series ) && isset( $series['prefix_weights'] ) && is_array( $series['prefix_weights'] ) ) ? $series['prefix_weights'] : array( 0 );
    if ( empty( $timestamps ) ) {
        return 0;
    }

    $start_ts = strtotime( $start_utc_mysql . ' UTC' );
    $end_ts = strtotime( $end_utc_mysql . ' UTC' );
    if ( false === $start_ts || false === $end_ts || $end_ts <= $start_ts ) {
        return 0;
    }

    $left = self::lower_bound( $timestamps, $start_ts );
    $right = self::lower_bound( $timestamps, $end_ts );
    if ( ! isset( $prefix_weights[ $left ] ) || ! isset( $prefix_weights[ $right ] ) ) {
        return 0;
    }

    return max( 0, (int) $prefix_weights[ $right ] - (int) $prefix_weights[ $left ] );
}

private static function availability_prorated_target( $target, $anchor_ts, array $loop, $availability, DateTimeZone $tz ) {
    if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'availability_cycle_prorated_target_for_loop' ) ) ) {
        return (int) TCT_Interval::availability_cycle_prorated_target_for_loop( $target, $anchor_ts, $loop, $availability, $tz );
    }

    $target = (int) $target;
    $anchor_ts = (int) $anchor_ts;
    $window_start_ts = isset( $loop['start_ts'] ) ? (int) $loop['start_ts'] : 0;
    $window_end_ts = isset( $loop['end_ts'] ) ? (int) $loop['end_ts'] : 0;
    if ( $target <= 0 || $window_end_ts <= $window_start_ts || $anchor_ts <= 0 || $anchor_ts <= $window_start_ts ) {
        return $target;
    }
    if ( $anchor_ts >= $window_end_ts ) {
        return 0;
    }

    return (int) TCT_Utils::compute_prorated_target( $target, $anchor_ts, $window_start_ts, $window_end_ts );
}
 private static function start_of_current_period( DateTimeImmutable $dt, $unit ) { if ( 'day' === $unit ) { return $dt->setTime( 0, 0, 0 ); } if ( 'week' === $unit ) { $start_of_week = (int) get_option( 'start_of_week', 1 ); $dow = (int) $dt->format( 'w' ); $diff = ( $dow - $start_of_week + 7 ) % 7; return $dt->setTime( 0, 0, 0 )->sub( new DateInterval( 'P' . (int) $diff . 'D' ) ); } $year = (int) $dt->format( 'Y' ); $month = (int) $dt->format( 'n' ); if ( 'year' === $unit ) { return $dt->setDate( $year, 1, 1 )->setTime( 0, 0, 0 ); } if ( 'semiannual' === $unit ) { $start_month = ( $month <= 6 ) ? 1 : 7; return $dt->setDate( $year, $start_month, 1 )->setTime( 0, 0, 0 ); } if ( 'quarter' === $unit ) { $start_month = 1 + ( 3 * (int) floor( ( $month - 1 ) / 3 ) ); return $dt->setDate( $year, $start_month, 1 )->setTime( 0, 0, 0 ); } return $dt->setDate( $year, (int) $dt->format( 'm' ), 1 )->setTime( 0, 0, 0 ); } private static function subtract_one_period( DateTimeImmutable $end, $unit ) { if ( 'day' === $unit ) { return $end->sub( new DateInterval( 'P1D' ) ); } if ( 'week' === $unit ) { return $end->sub( new DateInterval( 'P7D' ) ); } if ( 'quarter' === $unit ) { return $end->modify( '-3 months' ); } if ( 'semiannual' === $unit ) { return $end->modify( '-6 months' ); } if ( 'year' === $unit ) { return $end->modify( '-1 year' ); } return $end->modify( '-1 month' ); } private static function count_in_range_utc( $timestamps, $start_utc_mysql, $end_utc_mysql ) { if ( ! is_array( $timestamps ) || empty( $timestamps ) ) { return 0; } $start_ts = strtotime( $start_utc_mysql . ' UTC' ); $end_ts = strtotime( $end_utc_mysql . ' UTC' ); if ( false === $start_ts || false === $end_ts ) { return 0; } $left = self::lower_bound( $timestamps, $start_ts ); $right = self::lower_bound( $timestamps, $end_ts ); return max( 0, $right - $left ); } private static function lower_bound( $arr, $needle ) { $lo = 0; $hi = count( $arr ); while ( $lo < $hi ) { $mid = (int) floor( ( $lo + $hi ) / 2 ); if ( $arr[ $mid ] < $needle ) { $lo = $mid + 1; } else { $hi = $mid; } } return $lo; } private static function interval_end_occurred_at_utc_mysql( $end_utc_mysql ) { $end_utc_mysql = is_string( $end_utc_mysql ) ? trim( (string) $end_utc_mysql ) : ''; if ( '' === $end_utc_mysql || '0000-00-00 00:00:00' === $end_utc_mysql ) { return $end_utc_mysql; } $end_ts = strtotime( $end_utc_mysql . ' UTC' ); if ( false === $end_ts ) { return $end_utc_mysql; } $adj_ts = (int) $end_ts - 1; if ( $adj_ts < 0 ) { $adj_ts = 0; } return gmdate( 'Y-m-d H:i:s', $adj_ts ); } private static function bulk_insert_events( $events ) { if ( ! is_array( $events ) || empty( $events ) ) { return; } global $wpdb; $ledger = TCT_DB::table_ledger(); $columns = array( 'user_id', 'event_key', 'event_type', 'points', 'occurred_at', 'goal_id', 'goal_name', 'label_name', 'interval_unit', 'interval_mode', 'interval_target', 'bonus_points', 'window_start', 'window_end', 'met', 'details', 'created_at', 'updated_at', ); $chunk_size = 200; $chunks = array_chunk( $events, $chunk_size ); foreach ( $chunks as $chunk ) { $placeholders = array(); $values = array(); foreach ( $chunk as $row ) { $placeholders[] = '(%d,%s,%s,%d,%s,%d,%s,%s,%s,%s,%d,%d,%s,%s,%d,%s,%s,%s)'; $values[] = (int) $row['user_id']; $values[] = (string) $row['event_key']; $values[] = (string) $row['event_type']; $values[] = (int) $row['points']; $values[] = (string) $row['occurred_at']; $values[] = (int) $row['goal_id']; $values[] = (string) $row['goal_name']; $values[] = (string) $row['label_name']; $values[] = (string) $row['interval_unit']; $values[] = (string) $row['interval_mode']; $values[] = (int) $row['interval_target']; $values[] = (int) $row['bonus_points']; $values[] = (string) $row['window_start']; $values[] = (string) $row['window_end']; $values[] = (int) $row['met']; $values[] = (string) $row['details']; $values[] = (string) $row['created_at']; $values[] = (string) $row['updated_at']; } $sql = "INSERT INTO {$ledger} (" . implode( ',', $columns ) . ") VALUES " . implode( ',', $placeholders ); $prepared = $wpdb->prepare( $sql, $values ); $wpdb->query( $prepared ); } } public static function insert_custom_event( $user_id, $event_type, $points, $occurred_at_utc_mysql = null, $details = '', $args = array() ) { global $wpdb; $user_id = (int) $user_id; if ( $user_id <= 0 ) { return false; } $event_type = sanitize_key( (string) $event_type ); if ( '' === $event_type ) { return false; } $points = (int) $points; $occurred_at = is_string( $occurred_at_utc_mysql ) ? trim( (string) $occurred_at_utc_mysql ) : ''; if ( '' === $occurred_at || '0000-00-00 00:00:00' === $occurred_at || false === strtotime( $occurred_at . ' UTC' ) ) { $occurred_at = current_time( 'mysql', true ); } $event_key = ''; if ( is_array( $args ) && isset( $args['event_key'] ) && is_string( $args['event_key'] ) ) { $event_key = trim( (string) $args['event_key'] ); } if ( '' === $event_key ) { $salt = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : uniqid( 'tct', true ); $event_key = 'x_' . sha1( implode( '|', array( (string) $user_id, $event_type, $occurred_at, (string) $points, (string) $salt ) ) ); } $event_key = substr( sanitize_text_field( $event_key ), 0, 80 ); if ( '' === $event_key ) { return false; } $goal_id = 0; $goal_name = ''; $label_name = ''; $interval_unit = ''; $interval_mode = ''; $interval_target = 0; $bonus_points = 0; $window_start = ''; $window_end = ''; $met = 0; if ( is_array( $args ) ) { if ( isset( $args['goal_id'] ) ) { $goal_id = (int) $args['goal_id']; } if ( isset( $args['goal_name'] ) && is_string( $args['goal_name'] ) ) { $goal_name = sanitize_text_field( (string) $args['goal_name'] ); } if ( isset( $args['label_name'] ) && is_string( $args['label_name'] ) ) { $label_name = sanitize_text_field( (string) $args['label_name'] ); } if ( isset( $args['interval_unit'] ) && is_string( $args['interval_unit'] ) ) { $interval_unit = sanitize_text_field( (string) $args['interval_unit'] ); } if ( isset( $args['interval_mode'] ) && is_string( $args['interval_mode'] ) ) { $interval_mode = sanitize_text_field( (string) $args['interval_mode'] ); } if ( isset( $args['interval_target'] ) ) { $interval_target = (int) $args['interval_target']; } if ( isset( $args['bonus_points'] ) ) { $bonus_points = (int) $args['bonus_points']; } if ( isset( $args['window_start'] ) && is_string( $args['window_start'] ) ) { $window_start = trim( (string) $args['window_start'] ); } if ( isset( $args['window_end'] ) && is_string( $args['window_end'] ) ) { $window_end = trim( (string) $args['window_end'] ); } if ( isset( $args['met'] ) ) { $met = ! empty( $args['met'] ) ? 1 : 0; } } $details = is_string( $details ) ? trim( wp_strip_all_tags( (string) $details ) ) : ''; $ledger = TCT_DB::table_ledger(); $now = current_time( 'mysql', true ); $sql = "INSERT INTO {$ledger} (
                    user_id, event_key, event_type, points, occurred_at,
                    goal_id, goal_name, label_name,
                    todoist_completed_id, todoist_task_id,
                    interval_unit, interval_mode, interval_target, bonus_points,
                    window_start, window_end, met,
                    details, created_at, updated_at
                ) VALUES (
                    %d, %s, %s, %d, %s,
                    %d, %s, %s,
                    %s, %s,
                    %s, %s, %d, %d,
                    NULLIF(%s,''), NULLIF(%s,''), %d,
                    %s, %s, %s
                )
                ON DUPLICATE KEY UPDATE
                    event_type = VALUES(event_type),
                    points = VALUES(points),
                    occurred_at = VALUES(occurred_at),
                    goal_id = VALUES(goal_id),
                    goal_name = VALUES(goal_name),
                    label_name = VALUES(label_name),
                    interval_unit = VALUES(interval_unit),
                    interval_mode = VALUES(interval_mode),
                    interval_target = VALUES(interval_target),
                    bonus_points = VALUES(bonus_points),
                    window_start = VALUES(window_start),
                    window_end = VALUES(window_end),
                    met = VALUES(met),
                    details = VALUES(details),
                    updated_at = VALUES(updated_at)"; $prepared = $wpdb->prepare( $sql, $user_id, $event_key, $event_type, $points, $occurred_at, $goal_id, $goal_name, $label_name, '', '', $interval_unit, $interval_mode, $interval_target, $bonus_points, $window_start, $window_end, $met, $details, $now, $now ); $res = $wpdb->query( $prepared ); return false !== $res; } public static function record_completion_from_event( $user_id, $completion_id ) { global $wpdb; $user_id = (int) $user_id; $completion_id = (int) $completion_id; if ( $user_id <= 0 || $completion_id <= 0 ) { return; } $completions = TCT_DB::table_completions(); $goals = TCT_DB::table_goals(); $ledger = TCT_DB::table_ledger(); $c = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, goal_id, source, source_ref, todoist_completed_id, todoist_task_id, task_content, note, completed_at
                 FROM {$completions}
                 WHERE id = %d AND user_id = %d", $completion_id, $user_id ), ARRAY_A ); if ( ! is_array( $c ) || empty( $c['goal_id'] ) ) { return; } $goal_id = (int) $c['goal_id']; if ( $goal_id <= 0 ) { return; } $g = $wpdb->get_row( $wpdb->prepare( "SELECT id, goal_name, label_name, is_tracked, points_per_completion, points_enabled_at
                 FROM {$goals}
                 WHERE id = %d AND user_id = %d", $goal_id, $user_id ), ARRAY_A ); if ( ! is_array( $g ) || empty( $g['id'] ) ) { return; } $is_tracked = isset( $g['is_tracked'] ) ? (int) $g['is_tracked'] : 0; $ppc = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0; $points_enabled_at = isset( $g['points_enabled_at'] ) ? trim( (string) $g['points_enabled_at'] ) : ''; if ( $is_tracked <= 0 || $ppc <= 0 || '' === $points_enabled_at || '0000-00-00 00:00:00' === $points_enabled_at ) { return; } $occurred_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : ''; if ( '' === $occurred_at || '0000-00-00 00:00:00' === $occurred_at ) { return; } if ( strtotime( $occurred_at . ' UTC' ) < strtotime( $points_enabled_at . ' UTC' ) ) { return; } $source = isset( $c['source'] ) ? (string) $c['source'] : 'manual'; $source_ref = isset( $c['source_ref'] ) ? (string) $c['source_ref'] : ''; $event_key = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id ); $details = ''; if ( isset( $c['note'] ) && is_string( $c['note'] ) && '' !== trim( $c['note'] ) ) { $details = (string) $c['note']; } elseif ( isset( $c['task_content'] ) && is_string( $c['task_content'] ) && '' !== trim( $c['task_content'] ) ) { $details = wp_strip_all_tags( (string) $c['task_content'] ); } $now = current_time( 'mysql', true ); $sql = "INSERT INTO {$ledger} (
                    user_id, event_key, event_type, points, occurred_at,
                    goal_id, goal_name, label_name,
                    todoist_completed_id, todoist_task_id,
                    details, created_at, updated_at
                ) VALUES (
                    %d, %s, %s, %d, %s,
                    %d, %s, %s,
                    %s, %s,
                    %s, %s, %s
                )
                ON DUPLICATE KEY UPDATE
                    points = VALUES(points),
                    occurred_at = VALUES(occurred_at),
                    goal_id = VALUES(goal_id),
                    goal_name = VALUES(goal_name),
                    label_name = VALUES(label_name),
                    todoist_completed_id = VALUES(todoist_completed_id),
                    todoist_task_id = VALUES(todoist_task_id),
                    details = VALUES(details),
                    updated_at = VALUES(updated_at)"; $wpdb->query( $wpdb->prepare( $sql, $user_id, $event_key, 'completion', $ppc, $occurred_at, $goal_id, isset( $g['goal_name'] ) ? (string) $g['goal_name'] : '', isset( $g['label_name'] ) && is_string( $g['label_name'] ) ? (string) $g['label_name'] : '', isset( $c['todoist_completed_id'] ) ? (string) $c['todoist_completed_id'] : '', isset( $c['todoist_task_id'] ) ? (string) $c['todoist_task_id'] : '', $details, $now, $now ) ); } private static function reconcile_negative_goal_completions( $user_id, $since_utc_mysql, $until_utc_mysql, $now ) { global $wpdb; $goals = TCT_DB::table_goals(); $completions = TCT_DB::table_completions(); $ledger = TCT_DB::table_ledger(); $negative_goals = $wpdb->get_results( $wpdb->prepare( "SELECT id, goal_name, label_name, goal_type, threshold, points_per_completion, points_enabled_at, period_unit, period_span
                 FROM {$goals}
                 WHERE user_id = %d AND is_tracked = 1
                   AND goal_type IN ('never', 'harm_reduction')
                   AND points_per_completion > 0
                   AND points_enabled_at IS NOT NULL
                   AND points_enabled_at <> ''
                   AND points_enabled_at <> '0000-00-00 00:00:00'", $user_id ), ARRAY_A ); if ( ! is_array( $negative_goals ) || empty( $negative_goals ) ) { return; } $tz = TCT_Utils::wp_timezone(); foreach ( $negative_goals as $g ) { $goal_id = (int) $g['id']; $goal_name = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : ''; $label_name = isset( $g['label_name'] ) ? (string) $g['label_name'] : ''; $goal_type = (string) $g['goal_type']; $threshold = isset( $g['threshold'] ) && is_numeric( $g['threshold'] ) ? (int) $g['threshold'] : null; $ppc = (int) $g['points_per_completion']; $points_enabled_at = (string) $g['points_enabled_at']; $period_unit = isset( $g['period_unit'] ) ? (string) $g['period_unit'] : 'week'; $period_span = isset( $g['period_span'] ) ? max( 1, (int) $g['period_span'] ) : 1; $since_for_goal = $since_utc_mysql; if ( strcmp( $since_for_goal, $points_enabled_at ) < 0 ) { $since_for_goal = $points_enabled_at; } $completion_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, source, source_ref, completed_at, note, task_content
                     FROM {$completions}
                     WHERE user_id = %d AND goal_id = %d
                       AND completed_at >= %s AND completed_at <= %s
                     ORDER BY completed_at ASC", $user_id, $goal_id, $since_for_goal, $until_utc_mysql ), ARRAY_A ); if ( ! is_array( $completion_rows ) || empty( $completion_rows ) ) { continue; } $completions_by_interval = array(); foreach ( $completion_rows as $c ) { $completed_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : ''; if ( '' === $completed_at ) { continue; } $completed_ts = strtotime( $completed_at . ' UTC' ); if ( false === $completed_ts ) { continue; } $completed_dt = new DateTimeImmutable( '@' . $completed_ts ); $completed_dt = $completed_dt->setTimezone( $tz ); $bounds = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $bounds = TCT_Interval::current_loop_bounds( $completed_dt, $period_unit, $period_span ); } if ( ! is_array( $bounds ) || ! isset( $bounds['start'], $bounds['end'] ) ) { continue; } $interval_key = $bounds['start']->format( 'Y-m-d H:i:s' ); if ( ! isset( $completions_by_interval[ $interval_key ] ) ) { $completions_by_interval[ $interval_key ] = array(); } $completions_by_interval[ $interval_key ][] = $c; } foreach ( $completions_by_interval as $interval_key => $interval_completions ) { $count = 0; foreach ( $interval_completions as $c ) { $count++; $source = isset( $c['source'] ) ? (string) $c['source'] : 'manual'; $source_ref = isset( $c['source_ref'] ) ? (string) $c['source_ref'] : ''; $event_key = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id ); $completed_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : $now; $is_violation = TCT_Utils::is_negative_goal_violation( $goal_type, $threshold, $count - 1 ); $points = 0; $details = ''; if ( $is_violation ) { if ( TCT_Utils::is_never_goal( $goal_type, $threshold ) ) { $violation_number = $count; } else { $th = ( null !== $threshold ) ? (int) $threshold : 0; $violation_number = $count - $th; if ( $violation_number < 1 ) { $violation_number = 1; } } $points = TCT_Utils::compute_violation_penalty( $ppc, $violation_number ); $details = '[violation #' . $violation_number . ']'; } else { $points = 0; $details = '[within limit]'; } if ( isset( $c['note'] ) && is_string( $c['note'] ) && '' !== trim( $c['note'] ) ) { $details = trim( $c['note'] ) . ' ' . $details; } elseif ( isset( $c['task_content'] ) && is_string( $c['task_content'] ) && '' !== trim( $c['task_content'] ) ) { $details = wp_strip_all_tags( trim( $c['task_content'] ) ) . ' ' . $details; } $sql = "INSERT INTO {$ledger} (
                                user_id, event_key, event_type, points, occurred_at,
                                goal_id, goal_name, label_name,
                                todoist_completed_id, todoist_task_id,
                                details, created_at, updated_at
                            ) VALUES (
                                %d, %s, %s, %d, %s,
                                %d, %s, %s,
                                %s, %s,
                                %s, %s, %s
                            )
                            ON DUPLICATE KEY UPDATE
                                points = VALUES(points),
                                occurred_at = VALUES(occurred_at),
                                details = VALUES(details),
                                updated_at = VALUES(updated_at)"; $wpdb->query( $wpdb->prepare( $sql, $user_id, $event_key, 'completion', $points, $completed_at, $goal_id, $goal_name, $label_name, '', '', $details, $now, $now ) ); } } } } private static function reconcile_negative_goal_bonus( $user_id, $g, $since_utc_mysql, $until_utc_mysql, $now, $goal_type, $threshold, $tz, $now_tz, &$events ) { global $wpdb; $completions = TCT_DB::table_completions(); $goal_id = isset( $g['id'] ) ? (int) $g['id'] : 0; $label_name = isset( $g['label_name'] ) ? (string) $g['label_name'] : ''; $goal_name = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : $label_name; if ( $goal_id <= 0 ) { return; } $points_enabled_at = isset( $g['points_enabled_at'] ) ? (string) $g['points_enabled_at'] : ''; $points_enabled_at = trim( $points_enabled_at ); if ( '' === $points_enabled_at || '0000-00-00 00:00:00' === $points_enabled_at ) { return; } $ppc = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0; if ( $ppc <= 0 ) { return; } $since_for_goal = $since_utc_mysql; if ( strcmp( $since_for_goal, $points_enabled_at ) < 0 ) { $since_for_goal = $points_enabled_at; } $period_unit = isset( $g['period_unit'] ) ? (string) $g['period_unit'] : 'week'; $period_span = isset( $g['period_span'] ) ? max( 1, (int) $g['period_span'] ) : 1; $bonus = TCT_Utils::compute_negative_goal_bonus( $ppc, $goal_type, $threshold ); $windows = self::generate_completed_windows( $now_tz, $tz, $period_unit, $period_span, $since_for_goal, $until_utc_mysql, $g ); if ( empty( $windows ) ) { return; } foreach ( $windows as $w ) { $start_utc_mysql = $w['start_utc_mysql']; $end_utc_mysql = $w['end_utc_mysql']; if ( strcmp( $end_utc_mysql, $points_enabled_at ) <= 0 ) { continue; } $eval_start_utc_mysql = $start_utc_mysql; if ( strcmp( $eval_start_utc_mysql, $points_enabled_at ) < 0 ) { $eval_start_utc_mysql = $points_enabled_at; } $count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions}
                     WHERE user_id = %d AND goal_id = %d
                       AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $eval_start_utc_mysql, $end_utc_mysql ) ); $met = TCT_Utils::is_negative_goal_met( $goal_type, $threshold, $count ); if ( $met ) { $pts = (int) $bonus; $details = 'Goal kept (+' . (int) $pts . '; ' . (int) $count . ' occurrences)'; } else { $pts = 0; $violations = TCT_Utils::count_negative_goal_violations( $goal_type, $threshold, $count ); $details = 'Goal not kept (0; ' . (int) $violations . ' violations)'; } if ( $pts <= 0 ) { continue; } $event_key = 'b_' . sha1( implode( '|', array( (string) $user_id, (string) $goal_id, $period_unit, (string) $period_span, 'calendar', $end_utc_mysql ) ) ); $events[] = array( 'user_id' => (int) $user_id, 'event_key' => $event_key, 'event_type' => 'goal_bonus', 'points' => (int) $pts, 'occurred_at' => self::interval_end_occurred_at_utc_mysql( $end_utc_mysql ), 'goal_id' => (int) $goal_id, 'goal_name' => $goal_name, 'label_name' => $label_name, 'interval_unit' => $period_unit, 'interval_mode' => 'calendar', 'interval_target'=> 0, 'bonus_points' => (int) $pts, 'window_start' => $start_utc_mysql, 'window_end' => $end_utc_mysql, 'met' => $met ? 1 : 0, 'details' => $details, 'created_at' => $now, 'updated_at' => $now, ); } } } 