<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TCT_Reward_Stats_Window {
    private static $ajax_buffer_started = false;

    public function __construct() {
        add_filter( 'do_shortcode_tag', array( $this, 'filter_shortcode_output' ), 20, 4 );
        add_action( 'init', array( $this, 'maybe_start_ajax_buffer' ), 1 );
    }

    public function maybe_start_ajax_buffer() {
        if ( self::$ajax_buffer_started ) {
            return;
        }

        if ( ! function_exists( 'wp_doing_ajax' ) || ! wp_doing_ajax() ) {
            return;
        }

        $action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
        if ( '' === $action || 0 !== strpos( $action, 'tct_' ) ) {
            return;
        }

        if ( headers_sent() ) {
            return;
        }

        self::$ajax_buffer_started = true;
        ob_start( array( $this, 'filter_ajax_output' ) );
    }

    public function filter_shortcode_output( $output, $tag, $attr, $m ) {
        if ( ! is_string( $output ) || '' === $output ) {
            return $output;
        }

        if ( 'todoist_category_tracker' !== $tag && 'tct_mobile' !== $tag ) {
            return $output;
        }

        $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
        if ( $user_id <= 0 ) {
            return $output;
        }

        return $this->replace_stats_fragments( $output, $user_id );
    }

    public function filter_ajax_output( $buffer ) {
        if ( ! is_string( $buffer ) || '' === trim( $buffer ) ) {
            return $buffer;
        }

        $decoded = json_decode( $buffer, true );
        if ( ! is_array( $decoded ) ) {
            return $buffer;
        }

        $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
        if ( $user_id <= 0 ) {
            return $buffer;
        }

        $mutated = false;

        if ( isset( $decoded['data']['rewardStatsHtml'] ) && is_string( $decoded['data']['rewardStatsHtml'] ) ) {
            $decoded['data']['rewardStatsHtml'] = $this->build_desktop_stats_html( $user_id );
            $mutated = true;
        }

        foreach ( array( 'navPillsHtml', 'dashboardHtml', 'ledgerHtml', 'html' ) as $key ) {
            if ( isset( $decoded['data'][ $key ] ) && is_string( $decoded['data'][ $key ] ) ) {
                $decoded['data'][ $key ] = $this->replace_stats_fragments( $decoded['data'][ $key ], $user_id );
                $mutated = true;
            }
        }

        if ( ! $mutated ) {
            return $buffer;
        }

        $json = function_exists( 'wp_json_encode' ) ? wp_json_encode( $decoded ) : json_encode( $decoded );
        if ( ! is_string( $json ) || '' === $json ) {
            return $buffer;
        }

        return $json;
    }

    private function replace_stats_fragments( $html, $user_id ) {
        if ( ! is_string( $html ) || '' === $html ) {
            return $html;
        }

        $desktop_stats_html = $this->build_desktop_stats_html( $user_id );
        $mobile_stats_html = $this->build_mobile_stats_html( $user_id );
        $desktop_block_summary_html = $this->build_block_summary_lines_html( $user_id, 'desktop' );
        $mobile_block_summary_html = $this->build_block_summary_lines_html( $user_id, 'mobile' );

        if ( '' !== $desktop_stats_html ) {
            $html = preg_replace( '~<div class="tct-reward-widget-stats">.*?</div>~s', $desktop_stats_html, $html );
        }

        if ( '' !== $mobile_stats_html ) {
            $html = preg_replace( '~<div class="tct-mobile-reward-stats">.*?</div>~s', $mobile_stats_html, $html );
        }

        if ( '' !== $desktop_block_summary_html ) {
            $html = preg_replace(
                '~<div class="tct-muted">In last\s+\d+\s+days:\s*<strong>.*?</strong></div>~s',
                $desktop_block_summary_html,
                $html
            );
        }

        if ( '' !== $mobile_block_summary_html ) {
            $html = preg_replace(
                '~<div class="tct-mobile-ledger-sub">Last\s+\d+\s+days:\s*<strong>.*?</strong></div>~s',
                $mobile_block_summary_html,
                $html
            );
        }

        return $html;
    }

    private function build_desktop_stats_html( $user_id ) {
        $data = $this->compute_stats_data( $user_id );
        if ( ! is_array( $data ) ) {
            return '';
        }

        $stats_pairs = array(
            array( 'Today', $data['today'], 'Yesterday', $data['yesterday'] ),
            array( 'Current 14-Day Block', $data['current_block'], 'Prior 14-Day Block', $data['prior_block'] ),
            array( 'This Month', $data['month'], 'Last Month', $data['last_month'] ),
        );

        $html = '<div class="tct-reward-widget-stats">';
        $html .= '<table class="tct-reward-stats-table">';
        $html .= '<tbody>';

        foreach ( $stats_pairs as $pair ) {
            $cur_lbl = $pair[0];
            $cur_data = $this->normalize_earned_lost_payload( $pair[1] );
            $prev_lbl = $pair[2];
            $prev_data = $this->normalize_earned_lost_payload( $pair[3] );

            $cur_earned = (int) $cur_data['earned'];
            $cur_lost = (int) $cur_data['lost'];
            $prev_earned = (int) $prev_data['earned'];
            $prev_lost = (int) $prev_data['lost'];

            $cur_cls = '';
            $prev_cls = '';

            if ( $cur_earned > $prev_earned ) {
                $cur_cls = ' class="tct-stats-row-winning"';
            } elseif ( $prev_earned > $cur_earned ) {
                $prev_cls = ' class="tct-stats-row-losing"';
            }

            $html .= '<tr' . $cur_cls . '>';
            $html .= '<td class="tct-reward-stats-period">' . esc_html( $cur_lbl ) . '</td>';
            $html .= '<td class="tct-reward-stats-earned">+' . esc_html( number_format_i18n( $cur_earned ) ) . '</td>';
            $html .= '<td class="tct-reward-stats-lost">' . esc_html( number_format_i18n( $cur_lost ) ) . '</td>';
            $html .= '</tr>';

            $html .= '<tr' . $prev_cls . '>';
            $html .= '<td class="tct-reward-stats-period">' . esc_html( $prev_lbl ) . '</td>';
            $html .= '<td class="tct-reward-stats-earned">+' . esc_html( number_format_i18n( $prev_earned ) ) . '</td>';
            $html .= '<td class="tct-reward-stats-lost">' . esc_html( number_format_i18n( $prev_lost ) ) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div>';

        return $html;
    }

    private function build_mobile_stats_html( $user_id ) {
        $data = $this->compute_stats_data( $user_id );
        if ( ! is_array( $data ) ) {
            return '';
        }

        $stats_pairs = array(
            array( 'Current 14-Day Block', $data['current_block'], 'Prior 14-Day Block', $data['prior_block'] ),
        );

        $html = '<div class="tct-mobile-reward-stats">';
        $html .= '<table class="tct-mobile-reward-stats-table">';
        $html .= '<tbody>';

        foreach ( $stats_pairs as $pair ) {
            $cur_lbl = $pair[0];
            $cur_data = $this->normalize_earned_lost_payload( $pair[1] );
            $prev_lbl = $pair[2];
            $prev_data = $this->normalize_earned_lost_payload( $pair[3] );

            $cur_earned = (int) $cur_data['earned'];
            $cur_lost = (int) $cur_data['lost'];
            $prev_earned = (int) $prev_data['earned'];
            $prev_lost = (int) $prev_data['lost'];

            $cur_cls = '';
            $prev_cls = '';

            if ( $cur_earned > $prev_earned ) {
                $cur_cls = ' class="tct-mobile-stats-row-winning"';
            } elseif ( $prev_earned > $cur_earned ) {
                $prev_cls = ' class="tct-mobile-stats-row-losing"';
            }

            $html .= '<tr' . $cur_cls . '>';
            $html .= '<td class="tct-mobile-reward-stats-period">' . esc_html( $cur_lbl ) . '</td>';
            $html .= '<td class="tct-mobile-reward-stats-earned">+' . esc_html( number_format_i18n( $cur_earned ) ) . '</td>';
            $html .= '<td class="tct-mobile-reward-stats-lost">' . esc_html( number_format_i18n( $cur_lost ) ) . '</td>';
            $html .= '</tr>';

            $html .= '<tr' . $prev_cls . '>';
            $html .= '<td class="tct-mobile-reward-stats-period">' . esc_html( $prev_lbl ) . '</td>';
            $html .= '<td class="tct-mobile-reward-stats-earned">+' . esc_html( number_format_i18n( $prev_earned ) ) . '</td>';
            $html .= '<td class="tct-mobile-reward-stats-lost">' . esc_html( number_format_i18n( $prev_lost ) ) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div>';

        return $html;
    }

    private function build_block_summary_lines_html( $user_id, $surface = 'desktop' ) {
        $data = $this->compute_stats_data( $user_id );
        if ( ! is_array( $data ) || empty( $data['block_meta'] ) || ! is_array( $data['block_meta'] ) ) {
            return '';
        }

        $meta = $data['block_meta'];
        $day_number = isset( $meta['day_number'] ) ? (int) $meta['day_number'] : 1;
        if ( $day_number < 1 ) {
            $day_number = 1;
        }
        if ( $day_number > 14 ) {
            $day_number = 14;
        }

        $current_total = isset( $data['current_block_points'] ) ? (int) $data['current_block_points'] : 0;
        $prior_total = isset( $data['prior_block_points'] ) ? (int) $data['prior_block_points'] : 0;

        $surface = is_string( $surface ) ? strtolower( trim( $surface ) ) : 'desktop';
        if ( 'mobile' === $surface ) {
            $line_class = 'tct-mobile-ledger-sub';
        } else {
            $line_class = 'tct-muted';
        }

        $html = '';
        $html .= '<div class="' . esc_attr( $line_class ) . '">Current 14-day block (day ' . esc_html( (string) $day_number ) . ' of 14): <strong>' . esc_html( number_format_i18n( $current_total ) ) . '</strong></div>';
        $html .= '<div class="' . esc_attr( $line_class ) . '">Prior 14-day block (through day ' . esc_html( (string) $day_number ) . ' of 14): <strong>' . esc_html( number_format_i18n( $prior_total ) ) . '</strong></div>';

        return $html;
    }

    private function compute_stats_data( $user_id ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 ) {
            return null;
        }

        $tz = $this->get_wp_timezone();
        $utc = new DateTimeZone( 'UTC' );

        try {
            $now_tz = new DateTimeImmutable( 'now', $tz );
        } catch ( Exception $e ) {
            return null;
        }

        $now_utc_mysql = $now_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );

        $today_start_tz = $now_tz->setTime( 0, 0, 0 );
        $today_start_utc_mysql = $today_start_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );

        $yesterday_tz = $now_tz->modify( '-1 day' );
        $yesterday_start_tz = $yesterday_tz->setTime( 0, 0, 0 );
        $yesterday_end_tz = $yesterday_tz->setTime( 23, 59, 59 );
        $yesterday_start_utc_mysql = $yesterday_start_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );
        $yesterday_end_utc_mysql = $yesterday_end_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );

        $month_start_tz = $now_tz->modify( 'first day of this month' )->setTime( 0, 0, 0 );
        $month_start_utc_mysql = $month_start_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );

        $last_month_first_tz = $now_tz->modify( 'first day of last month' )->setTime( 0, 0, 0 );
        $last_month_days = (int) $last_month_first_tz->format( 't' );
        $target_dom = min( (int) $now_tz->format( 'j' ), $last_month_days );
        $last_month_target_tz = $last_month_first_tz->setDate(
            (int) $last_month_first_tz->format( 'Y' ),
            (int) $last_month_first_tz->format( 'm' ),
            $target_dom
        )->setTime(
            (int) $now_tz->format( 'H' ),
            (int) $now_tz->format( 'i' ),
            (int) $now_tz->format( 's' )
        );
        $last_month_start_utc_mysql = $last_month_first_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );
        $last_month_end_utc_mysql = $last_month_target_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' );

        $block_meta = $this->compute_fourteen_day_block_windows( $now_tz );
        if ( ! is_array( $block_meta ) ) {
            return null;
        }

        $current_block_start_utc_mysql = $block_meta['current_start']->setTimezone( $utc )->format( 'Y-m-d H:i:s' );
        $prior_block_start_utc_mysql = $block_meta['prior_start']->setTimezone( $utc )->format( 'Y-m-d H:i:s' );
        $prior_block_end_utc_mysql = $block_meta['prior_end']->setTimezone( $utc )->format( 'Y-m-d H:i:s' );

        return array(
            'today' => $this->get_normalized_earned_lost( $user_id, $today_start_utc_mysql, $now_utc_mysql ),
            'yesterday' => $this->get_normalized_earned_lost( $user_id, $yesterday_start_utc_mysql, $yesterday_end_utc_mysql ),
            'month' => $this->get_normalized_earned_lost( $user_id, $month_start_utc_mysql, $now_utc_mysql ),
            'last_month' => $this->get_normalized_earned_lost( $user_id, $last_month_start_utc_mysql, $last_month_end_utc_mysql ),
            'current_block' => $this->get_normalized_earned_lost( $user_id, $current_block_start_utc_mysql, $now_utc_mysql ),
            'prior_block' => $this->get_normalized_earned_lost( $user_id, $prior_block_start_utc_mysql, $prior_block_end_utc_mysql ),
            'current_block_points' => $this->get_points_in_range( $user_id, $current_block_start_utc_mysql, $now_utc_mysql ),
            'prior_block_points' => $this->get_points_in_range( $user_id, $prior_block_start_utc_mysql, $prior_block_end_utc_mysql ),
            'block_meta' => $block_meta,
        );
    }

    private function compute_fourteen_day_block_windows( DateTimeImmutable $now_tz ) {
        $year = (int) $now_tz->format( 'Y' );

        try {
            $year_start = $now_tz->setDate( $year, 1, 1 )->setTime( 0, 0, 0 );
            $today_start = $now_tz->setTime( 0, 0, 0 );
        } catch ( Exception $e ) {
            return null;
        }

        $day_of_year_zero = (int) $year_start->diff( $today_start )->format( '%a' );
        if ( $day_of_year_zero < 0 ) {
            $day_of_year_zero = 0;
        }

        $block_index = (int) floor( (float) $day_of_year_zero / 14.0 );
        if ( $block_index < 0 ) {
            $block_index = 0;
        }

        try {
            $current_start = $year_start->add( new DateInterval( 'P' . (int) ( $block_index * 14 ) . 'D' ) );
            $prior_start = $current_start->sub( new DateInterval( 'P14D' ) );
        } catch ( Exception $e ) {
            return null;
        }

        $day_number = (int) ( $day_of_year_zero - ( $block_index * 14 ) + 1 );
        if ( $day_number < 1 ) {
            $day_number = 1;
        }
        if ( $day_number > 14 ) {
            $day_number = 14;
        }

        try {
            $prior_end_exclusive = $prior_start->add( new DateInterval( 'P' . (int) $day_number . 'D' ) );
            $prior_end = $prior_end_exclusive->sub( new DateInterval( 'PT1S' ) );
        } catch ( Exception $e ) {
            return null;
        }

        return array(
            'year' => $year,
            'block_index' => $block_index,
            'day_number' => $day_number,
            'current_start' => $current_start,
            'current_end' => $now_tz,
            'prior_start' => $prior_start,
            'prior_end' => $prior_end,
        );
    }

    private function get_wp_timezone() {
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ) {
            try {
                $tz = TCT_Utils::wp_timezone();
                if ( $tz instanceof DateTimeZone ) {
                    return $tz;
                }
            } catch ( Exception $e ) {
            }
        }

        if ( function_exists( 'wp_timezone' ) ) {
            try {
                $tz = wp_timezone();
                if ( $tz instanceof DateTimeZone ) {
                    return $tz;
                }
            } catch ( Exception $e ) {
            }
        }

        return new DateTimeZone( 'UTC' );
    }

    private function get_normalized_earned_lost( $user_id, $start_utc_mysql, $end_utc_mysql ) {
        $user_id = (int) $user_id;
        $start_utc_mysql = is_string( $start_utc_mysql ) ? trim( $start_utc_mysql ) : '';
        $end_utc_mysql = is_string( $end_utc_mysql ) ? trim( $end_utc_mysql ) : '';

        if ( $user_id <= 0 || '' === $start_utc_mysql || '' === $end_utc_mysql ) {
            return array(
                'earned' => 0,
                'lost' => 0,
            );
        }

        if ( class_exists( 'TCT_Economy_Normalizer' ) && is_callable( array( 'TCT_Economy_Normalizer', 'get_normalized_earned_lost' ) ) ) {
            $payload = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $start_utc_mysql, $end_utc_mysql );
            return $this->normalize_earned_lost_payload( $payload );
        }

        if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'get_earned_lost_in_range' ) ) ) {
            $payload = TCT_Ledger::get_earned_lost_in_range( $user_id, $start_utc_mysql, $end_utc_mysql );
            return $this->normalize_earned_lost_payload( $payload );
        }

        return array(
            'earned' => 0,
            'lost' => 0,
        );
    }

    private function get_points_in_range( $user_id, $start_utc_mysql, $end_utc_mysql ) {
        $user_id = (int) $user_id;
        $start_utc_mysql = is_string( $start_utc_mysql ) ? trim( $start_utc_mysql ) : '';
        $end_utc_mysql = is_string( $end_utc_mysql ) ? trim( $end_utc_mysql ) : '';

        if ( $user_id <= 0 || '' === $start_utc_mysql || '' === $end_utc_mysql ) {
            return 0;
        }

        if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'get_points_in_range' ) ) ) {
            return (int) TCT_Ledger::get_points_in_range( $user_id, $start_utc_mysql, $end_utc_mysql );
        }

        $payload = $this->get_normalized_earned_lost( $user_id, $start_utc_mysql, $end_utc_mysql );
        return (int) $payload['earned'] + (int) $payload['lost'];
    }

    private function normalize_earned_lost_payload( $payload ) {
        $earned = 0;
        $lost = 0;

        if ( is_array( $payload ) ) {
            if ( isset( $payload['earned'] ) ) {
                $earned = (int) $payload['earned'];
            }
            if ( isset( $payload['lost'] ) ) {
                $lost = (int) $payload['lost'];
            }
        }

        return array(
            'earned' => $earned,
            'lost' => $lost,
        );
    }
}
