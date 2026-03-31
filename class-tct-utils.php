<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } class TCT_Utils { public static function is_debug_enabled() { if ( defined( 'TCT_DEBUG' ) ) { return (bool) TCT_DEBUG; } return ( defined( 'WP_DEBUG' ) && WP_DEBUG ); } public static function request_id() { static $rid = null; if ( null !== $rid ) { return $rid; } if ( function_exists( 'wp_generate_uuid4' ) ) { $rid = (string) wp_generate_uuid4(); return $rid; } $rid = substr( md5( uniqid( 'tct', true ) ), 0, 12 ); return $rid; } public static function debug_log( $message, $context = array(), $level = 'info' ) { if ( ! self::is_debug_enabled() ) { return; } $prefix = 'TCT [' . self::request_id() . '] ' . strtoupper( (string) $level ) . ': '; $line = (string) $message; if ( ! empty( $context ) && function_exists( 'wp_json_encode' ) ) { $safe = array(); foreach ( (array) $context as $k => $v ) { $ks = is_string( $k ) ? strtolower( $k ) : ''; if ( $ks && ( false !== strpos( $ks, 'token' ) || false !== strpos( $ks, 'secret' ) || false !== strpos( $ks, 'password' ) || false !== strpos( $ks, 'authorization' ) || false !== strpos( $ks, 'nonce' ) ) ) { $safe[ $k ] = '[redacted]'; continue; } if ( is_scalar( $v ) || null === $v ) { $safe[ $k ] = $v; } else { $safe[ $k ] = '[' . gettype( $v ) . ']'; } } $line .= ' ' . wp_json_encode( $safe ); } error_log( $prefix . $line ); } public static function send_json_success( $data = null, $status_code = null, $options = 0 ) { $payload = array( 'ok' => true, 'success' => true, 'data' => $data, 'requestId' => self::request_id(), ); if ( null === $status_code ) { $status_code = 200; } wp_send_json( $payload, $status_code, $options ); } public static function send_json_error( $data = null, $status_code = null, $options = 0 ) { if ( null === $status_code ) { $status_code = 400; } $message = 'Request failed.'; $code = ''; if ( is_array( $data ) ) { if ( isset( $data['message'] ) && is_string( $data['message'] ) && '' !== trim( $data['message'] ) ) { $message = (string) $data['message']; } if ( isset( $data['code'] ) && is_string( $data['code'] ) && '' !== trim( $data['code'] ) ) { $code = (string) $data['code']; } } elseif ( is_string( $data ) && '' !== trim( $data ) ) { $message = (string) $data; } if ( '' === $code ) { $code = 'http_' . (int) $status_code; } $payload_data = array(); if ( is_array( $data ) ) { $payload_data = $data; } if ( ! isset( $payload_data['message'] ) ) { $payload_data['message'] = $message; } if ( ! isset( $payload_data['code'] ) ) { $payload_data['code'] = $code; } $payload = array( 'ok' => false, 'success' => false, 'error' => array( 'code' => $code, 'message' => $message, ), 'data' => $payload_data, 'requestId' => self::request_id(), ); $action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; $uid = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0; self::debug_log( 'endpoint_error', array( 'action' => $action, 'user_id' => $uid, 'status' => (int) $status_code, 'code' => $code, 'message' => $message, ), 'error' ); wp_send_json( $payload, $status_code, $options ); } public static function enforce_ajax_nonce( $action = -1, $query_arg = false, $status_code = 403 ) { $ok = check_ajax_referer( $action, $query_arg, false ); if ( ! $ok ) { self::send_json_error( array( 'code' => 'invalid_nonce', 'message' => 'Invalid security token. Please refresh and try again.', ), (int) $status_code ); return false; } return true; } public static function points_importance_weights() { return array( 1 => 1, 2 => 2, 3 => 4, 4 => 7, 5 => 11, ); } public static function points_effort_multipliers() { return array( 1 => 1.0, 2 => 1.1, 3 => 1.25, 4 => 1.45, 5 => 1.7, ); } public static function goal_bonus_rate() { return 0.50; } public static function goal_penalty_rate() { return 1.00; } public static function goal_penalty_gamma() { return 1.5; } public static function points_bonus_k() { return self::goal_bonus_rate(); } public static function compute_points_per_completion( $importance, $effort ) { $importance = (int) $importance; $effort = (int) $effort; $wI = self::points_importance_weights(); $wE = self::points_effort_multipliers(); if ( ! isset( $wI[ $importance ] ) || ! isset( $wE[ $effort ] ) ) { return 0; } $raw = (float) $wI[ $importance ] * (float) $wE[ $effort ]; $pts = (int) round( $raw ); if ( $pts < 0 ) { $pts = 0; } if ( $pts > 999999 ) { $pts = 999999; } return $pts; } public static function compute_prorated_target( $target, $anchor_ts, $loop_start_ts, $loop_end_ts ) { $target = (int) $target; $anchor_ts = (int) $anchor_ts; $loop_start_ts = (int) $loop_start_ts; $loop_end_ts = (int) $loop_end_ts; if ( $target <= 0 ) { return 0; } if ( $anchor_ts <= $loop_start_ts ) { return $target; } $total_duration = $loop_end_ts - $loop_start_ts; if ( $total_duration <= 0 ) { return $target; } if ( $anchor_ts >= $loop_end_ts ) { return 0; } $remaining = $loop_end_ts - $anchor_ts; $fraction = (float) $remaining / (float) $total_duration; $prorated = (int) floor( (float) $target * $fraction ); if ( $prorated < 0 ) { $prorated = 0; } if ( $prorated > $target ) { $prorated = $target; } return $prorated; } public static function compute_prorate_anchor_ts( $created_at_ts, $updated_at_ts, $loop_start_ts ) { $created_at_ts = (int) $created_at_ts; $updated_at_ts = (int) $updated_at_ts; $loop_start_ts = (int) $loop_start_ts; if ( $created_at_ts > 0 && $created_at_ts >= $loop_start_ts ) { return $created_at_ts; } /* Avoid using updated_at as a prorate anchor when created_at exists (edits like Allowed Fails should not reset interval expectations). */ if ( $created_at_ts <= 0 && $updated_at_ts > 0 && $updated_at_ts >= $loop_start_ts ) { return $updated_at_ts; } return 0; } public static function compute_bonus_points( $points_per_completion, $target, $bonus_rate = null ) { $ppc = (int) $points_per_completion; $target = (int) $target; if ( $ppc <= 0 || $target <= 0 ) { return 0; } if ( null === $bonus_rate ) { $bonus_rate = self::goal_bonus_rate(); } $bonus_rate = (float) $bonus_rate; if ( $bonus_rate <= 0 ) { return 0; } $possible = (float) $ppc * (float) $target; $raw = $possible * $bonus_rate; $bonus = (int) round( $raw ); if ( $bonus < 0 ) { $bonus = 0; } if ( $bonus > 999999 ) { $bonus = 999999; } return $bonus; } public static function compute_penalty_points( $points_per_completion, $target, $achieved, $penalty_rate = null, $gamma = null ) { $ppc = (int) $points_per_completion; $target_i = (int) $target; $ach_i = (int) $achieved; if ( $ppc <= 0 || $target_i <= 0 ) { return 0; } if ( $ach_i >= $target_i ) { return 0; } if ( null === $penalty_rate ) { $penalty_rate = self::goal_penalty_rate(); } if ( null === $gamma ) { $gamma = self::goal_penalty_gamma(); } $penalty_rate = (float) $penalty_rate; $gamma = (float) $gamma; if ( $penalty_rate <= 0 ) { return 0; } if ( $gamma <= 0 ) { $gamma = 1.0; } $r = 0.0; if ( $target_i > 0 ) { $r = (float) $ach_i / (float) $target_i; } if ( $r < 0.0 ) { $r = 0.0; } if ( $r > 1.0 ) { $r = 1.0; } $shortfall = 1.0 - $r; if ( $shortfall < 0.0 ) { $shortfall = 0.0; } if ( $shortfall > 1.0 ) { $shortfall = 1.0; } $possible = (float) $ppc * (float) $target_i; $raw = $possible * $penalty_rate * pow( $shortfall, $gamma ); $mag = (int) round( $raw ); if ( $mag < 0 ) { $mag = 0; } if ( $mag > 999999 ) { $mag = 999999; } return -1 * $mag; } public static function compute_goal_interval_points( $points_per_completion, $target, $achieved ) { $ppc = (int) $points_per_completion; $target = (int) $target; $ach = (int) $achieved; if ( $ppc <= 0 || $target <= 0 ) { return 0; } if ( $ach >= $target ) { return self::compute_bonus_points( $ppc, $target ); } return self::compute_penalty_points( $ppc, $target, $ach ); } public static function negative_goal_vitality_drop() { return 15; } public static function negative_goal_penalty_escalation_rate() { return 0.25; } public static function is_negative_goal_type( $goal_type ) { $goal_type = strtolower( trim( (string) $goal_type ) ); return 'never' === $goal_type || 'harm_reduction' === $goal_type; } public static function is_positive_no_interval_goal_type( $goal_type ) { $goal_type = strtolower( trim( (string) $goal_type ) ); return 'positive_no_int' === $goal_type; } public static function is_anki_cards_goal_type( $goal_type ) { $goal_type = strtolower( trim( (string) $goal_type ) ); return 'anki_cards' === $goal_type; } public static function format_anki_cards_completion_note( $count ) { $count = (int) $count; if ( $count < 0 ) { $count = 0; } return 'Anki cards: ' . (string) $count; } public static function parse_anki_cards_completion_note( $note ) { $note = is_string( $note ) ? trim( $note ) : ''; if ( '' === $note ) { return 0; } if ( preg_match( '/anki\s*cards?\s*:\s*([0-9]+)/i', $note, $m ) ) { return isset( $m[1] ) ? max( 0, (int) $m[1] ) : 0; } if ( preg_match( '/^([0-9]+)$/', $note, $m ) ) { return isset( $m[1] ) ? max( 0, (int) $m[1] ) : 0; } return 0; } public static function compute_anki_cards_adjustment_points( $points_per_completion, $target, $actual ) { $ppc = (int) $points_per_completion; $target = (int) $target; $actual = (int) $actual; if ( $ppc <= 0 || $target <= 0 ) { return 0; } if ( $actual < 0 ) { $actual = 0; } $raw = (float) $ppc * ( ( (float) $actual - (float) $target ) / (float) $target ); $points = (int) round( $raw ); if ( $points > 999999 ) { $points = 999999; } if ( $points < -999999 ) { $points = -999999; } return $points; } public static function supported_goal_types() { return array( 'positive', 'never', 'harm_reduction', 'anki_cards' ); } public static function is_supported_goal_type( $goal_type ) { return in_array( $goal_type, self::supported_goal_types(), true ); } public static function is_goal_type_economy_eligible( $goal_type ) { $gt = strtolower( trim( (string) $goal_type ) ); if ( self::is_positive_no_interval_goal_type( $gt ) || self::is_composite_goal_type( $gt ) ) { return false; } return true; } public static function is_never_goal( $goal_type, $threshold = null ) { $goal_type = strtolower( trim( (string) $goal_type ) ); if ( 'never' === $goal_type ) { return true; } if ( 'harm_reduction' === $goal_type ) { $threshold = ( null === $threshold ) ? 0 : (int) $threshold; if ( $threshold <= 0 ) { return true; } } return false; } public static function compute_negative_goal_base_penalty( $points_per_completion ) { $ppc = (int) $points_per_completion; if ( $ppc <= 0 ) { return 0; } $penalty_rate = self::goal_penalty_rate(); $raw = (float) $ppc * $penalty_rate; $mag = (int) round( $raw ); if ( $mag < 0 ) { $mag = 0; } if ( $mag > 999999 ) { $mag = 999999; } return $mag; } public static function compute_violation_penalty( $points_per_completion, $violation_number ) { $ppc = (int) $points_per_completion; $vn = (int) $violation_number; if ( $ppc <= 0 ) { return 0; } if ( $vn < 1 ) { $vn = 1; } $base_penalty = self::compute_negative_goal_base_penalty( $ppc ); $escalation_rate = self::negative_goal_penalty_escalation_rate(); $multiplier = 1.0 + ( $escalation_rate * (float) ( $vn - 1 ) ); $raw = (float) $base_penalty * $multiplier; $mag = (int) round( $raw ); if ( $mag < 0 ) { $mag = 0; } if ( $mag > 999999 ) { $mag = 999999; } return -1 * $mag; } public static function compute_negative_goal_bonus( $points_per_completion, $goal_type, $threshold = null ) { $ppc = (int) $points_per_completion; $goal_type = strtolower( trim( (string) $goal_type ) ); if ( $ppc <= 0 ) { return 0; } if ( ! self::is_negative_goal_type( $goal_type ) ) { return 0; } if ( self::is_never_goal( $goal_type, $threshold ) ) { return self::compute_bonus_points( $ppc, 1 ); } $threshold = (int) $threshold; if ( $threshold <= 0 ) { $threshold = 1; } return self::compute_bonus_points( $ppc, $threshold ); } public static function get_negative_goal_button_label( $goal_type, $threshold, $current_count ) { $goal_type = strtolower( trim( (string) $goal_type ) ); $current_count = (int) $current_count; if ( $current_count < 0 ) { $current_count = 0; } if ( self::is_never_goal( $goal_type, $threshold ) ) { return 'Not who I want to be'; } if ( 'harm_reduction' === $goal_type ) { $threshold = (int) $threshold; if ( $current_count < $threshold ) { return 'Enjoyed'; } else { return 'Not who I want to be'; } } return 'Complete'; } public static function is_negative_goal_violation( $goal_type, $threshold, $current_count ) { $goal_type = strtolower( trim( (string) $goal_type ) ); $current_count = (int) $current_count; if ( $current_count < 0 ) { $current_count = 0; } if ( self::is_never_goal( $goal_type, $threshold ) ) { return true; } if ( 'harm_reduction' === $goal_type ) { $threshold = (int) $threshold; return $current_count >= $threshold; } return false; } public static function has_exceeded_negative_goal_limit( $goal_type, $threshold, $current_count ) { $goal_type = strtolower( trim( (string) $goal_type ) ); $current_count = (int) $current_count; if ( $current_count < 0 ) { $current_count = 0; } if ( self::is_never_goal( $goal_type, $threshold ) ) { return $current_count > 0; } if ( 'harm_reduction' === $goal_type ) { $threshold = (int) $threshold; return $current_count > $threshold; } return false; } public static function count_negative_goal_violations( $goal_type, $threshold, $completion_count ) { $goal_type = strtolower( trim( (string) $goal_type ) ); $completion_count = (int) $completion_count; if ( $completion_count < 0 ) { $completion_count = 0; } if ( self::is_never_goal( $goal_type, $threshold ) ) { return $completion_count; } if ( 'harm_reduction' === $goal_type ) { $threshold = (int) $threshold; $violations = $completion_count - $threshold; return max( 0, $violations ); } return 0; } public static function compute_total_violation_penalty( $points_per_completion, $violation_count ) { $ppc = (int) $points_per_completion; $vc = (int) $violation_count; if ( $ppc <= 0 || $vc <= 0 ) { return 0; } $total = 0; for ( $i = 1; $i <= $vc; $i++ ) { $total += self::compute_violation_penalty( $ppc, $i ); } return $total; } public static function is_negative_goal_met( $goal_type, $threshold, $completion_count ) { $goal_type = strtolower( trim( (string) $goal_type ) ); $completion_count = (int) $completion_count; if ( $completion_count < 0 ) { $completion_count = 0; } if ( self::is_never_goal( $goal_type, $threshold ) ) { return 0 === $completion_count; } if ( 'harm_reduction' === $goal_type ) { $threshold = (int) $threshold; return $completion_count <= $threshold; } return false; } public static function wp_timezone() { if ( class_exists( 'TCT_Admin' ) && is_callable( array( 'TCT_Admin', 'get_timezone' ) ) ) { $plugin_tz = TCT_Admin::get_timezone(); if ( '' !== $plugin_tz ) { try { return new DateTimeZone( $plugin_tz ); } catch ( Exception $e ) { } } } $tz_string = get_option( 'timezone_string' ); if ( $tz_string ) { try { return new DateTimeZone( $tz_string ); } catch ( Exception $e ) { } } $offset = (float) get_option( 'gmt_offset', 0 ); $hours = (int) $offset; $minutes = (int) round( ( $offset - $hours ) * 60 ); $sign = ( $offset >= 0 ) ? '+' : '-'; $hours_abs = abs( $hours ); $minutes_abs = abs( $minutes ); $tz_offset = sprintf( '%s%02d:%02d', $sign, $hours_abs, $minutes_abs ); return new DateTimeZone( $tz_offset ); } public static function iso_to_mysql_utc( $iso ) { try { $dt = new DateTimeImmutable( $iso ); $dt_utc = $dt->setTimezone( new DateTimeZone( 'UTC' ) ); return $dt_utc->format( 'Y-m-d H:i:s' ); } catch ( Exception $e ) { return null; } } public static function format_todoist_since_until( DateTimeImmutable $dt_utc ) { $utc = $dt_utc->setTimezone( new DateTimeZone( 'UTC' ) ); return $utc->format( 'Y-m-d\TH:i:s' ); } public static function dt_to_mysql_utc( DateTimeImmutable $dt ) { $utc = $dt->setTimezone( new DateTimeZone( 'UTC' ) ); return $utc->format( 'Y-m-d H:i:s' ); } public static function mysql_utc_to_tz( $mysql_utc, $tz, $format = 'Y-m-d H:i:s' ) { $mysql_utc = is_string( $mysql_utc ) ? trim( $mysql_utc ) : ''; if ( '' === $mysql_utc || '0000-00-00 00:00:00' === $mysql_utc ) { return ''; } if ( ! ( $tz instanceof DateTimeZone ) ) { $tz = self::wp_timezone(); } $format = ( is_string( $format ) && '' !== trim( $format ) ) ? $format : 'Y-m-d H:i:s'; try { $dt = new DateTimeImmutable( $mysql_utc, new DateTimeZone( 'UTC' ) ); return $dt->setTimezone( $tz )->format( $format ); } catch ( Exception $e ) { if ( function_exists( 'get_date_from_gmt' ) ) { return (string) get_date_from_gmt( $mysql_utc, $format ); } return (string) $mysql_utc; } } public static function current_url() { $scheme = is_ssl() ? 'https' : 'http'; $host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : ''; $uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; return $scheme . '://' . $host . $uri; } public static function add_query_arg_safe( $url, $key, $value ) { return esc_url_raw( add_query_arg( array( $key => $value ), $url ) ); } public static function vitality_bucket_biased( $vitality ) { $v = (int) round( (float) $vitality ); if ( $v < 0 ) { $v = 0; } elseif ( $v > 100 ) { $v = 100; } $bucket = (int) ( 5 * floor( ( $v + 3 ) / 5 ) ); if ( $bucket < 0 ) { $bucket = 0; } elseif ( $bucket > 100 ) { $bucket = 100; } $bucket = (int) ( round( $bucket / 5 ) * 5 ); return $bucket; } public static function vitality_plant_slug( $plant_name ) { $name = trim( (string) $plant_name ); if ( '' === $name ) { return ''; } $name = str_replace( array( "\xE2\x80\x99", "\xE2\x80\x98", "\xC2\xB4", '`' ), "'", $name ); $name = preg_replace( "/'s\b/i", '_s_', $name ); $name = str_replace( array( '(', ')' ), ' ', $name ); $name = str_replace( "'", '_', $name ); $name = preg_replace( '/\s+/', '_', $name ); $name = preg_replace( '/[^\pL\pN_]+/u', '_', $name ); $name = preg_replace( '/_+/', '_', $name ); $name = trim( $name, '_' ); return $name; } public static function vitality_plant_filename( $plant_name, $vitality ) { $slug = self::vitality_plant_slug( $plant_name ); if ( '' === $slug ) { return ''; } $bucket = self::vitality_bucket_biased( $vitality ); return $slug . '_v' . $bucket . '.png'; } public static function find_attachment_url_by_filename( $filename, $size = 'medium' ) { $filename = basename( (string) $filename ); if ( '' === $filename ) { return null; } static $cache = array(); if ( array_key_exists( $filename, $cache ) ) { $cached = $cache[ $filename ]; return $cached ? $cached : null; } $args = array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false, 'meta_query' => array( array( 'key' => '_wp_attached_file', 'value' => $filename, 'compare' => 'LIKE', ), ), ); $q = new WP_Query( $args ); $id = 0; if ( ! empty( $q->posts ) ) { $id = (int) $q->posts[0]; } if ( $id > 0 ) { $url = wp_get_attachment_image_url( $id, $size ); if ( ! $url ) { $url = wp_get_attachment_url( $id ); } if ( $url ) { $cache[ $filename ] = $url; return $url; } } $cache[ $filename ] = ''; return null; } public static function resolve_vitality_plant_image_url( $plant_name, $vitality, $size = 'medium' ) { $plant_name = trim( (string) $plant_name ); if ( '' === $plant_name ) { return null; } $bucket = self::vitality_bucket_biased( $vitality ); $slug = self::vitality_plant_slug( $plant_name ); $filename = ''; if ( '' !== $slug ) { $filename = $slug . '_v' . $bucket . '.png'; } if ( '' === $filename ) { return null; } $url = self::find_attachment_url_by_filename( $filename, $size ); if ( null !== $url ) { return $url; } static $missing_logged = array(); if ( ! isset( $missing_logged[ $filename ] ) ) { $missing_logged[ $filename ] = true; error_log( "TCT vitality plant missing: plant={$plant_name} vitality={$vitality} bucket={$bucket} file={$filename}" ); } return null; } public static function get_vitality_plants() { static $plants = null; if ( null !== $plants ) { return $plants; } $raw = array( 'Rubber plant', 'Monstera deliciosa', 'Snake plant (Sansevieria)', 'Bird of paradise', 'Norfolk Island pine', 'Chinese elm bonsai', 'Olive tree (potted)', 'Bay laurel', 'Juniper bonsai', 'ZZ plant', 'Dracaena marginata', 'Dracaena fragrans', 'Yucca cane', 'Ponytail palm', 'Sago palm', 'Kentia palm', 'Areca palm', 'Parlor palm', 'Majesty palm', 'Bottle palm', 'Cereus cactus', 'Prickly pear cactus', 'Christmas cactus', 'Old man cactus', 'San Pedro cactus', 'Bonsai pine', 'Bonsai juniper', 'Bonsai maple', 'Bonsai ficus', 'Bonsai olive', 'Swiss cheese vine (Monstera adansonii)', 'Philodendron selloum', 'Philodendron birkin', 'Philodendron rojo congo', 'Dieffenbachia', 'Aglaonema', 'Syngonium', 'Pothos', 'Scindapsus', 'Heartleaf philodendron', 'Schefflera arboricola', 'Umbrella tree', 'Money tree (Pachira)', 'Cast iron plant', 'Aspidistra elatior', 'Corn plant', 'Dragon tree', 'Ti plant', 'Fatsia japonica', 'Thyme (potted)', 'Bay tree (topiary)', 'Boxwood (potted)', 'Juniper shrub (potted)', 'Cypress (potted)', 'Hinoki cypress', 'Dwarf spruce', 'Boston fern', 'Staghorn fern', 'Asparagus fern', 'Kimberly queen fern', 'Button fern', 'Bird\'s nest fern', 'Sword fern', 'Tree fern', 'Leatherleaf fern', 'Rabbit\'s foot fern', 'Aralia', 'Rosemary (potted)', 'Lavender (potted)', 'Sage (potted)', ); $seen = array(); $out = array(); foreach ( $raw as $name ) { $name = trim( (string) $name ); if ( '' === $name ) { continue; } if ( isset( $seen[ $name ] ) ) { continue; } $seen[ $name ] = true; $out[] = $name; } $plants = $out; return $plants; } public static function get_vitality_plant_previews( $size = 'thumbnail' ) { $size = $size ? (string) $size : 'thumbnail'; static $cache_by_size = array(); if ( isset( $cache_by_size[ $size ] ) ) { return $cache_by_size[ $size ]; } $plants = self::get_vitality_plants(); $out = array(); if ( empty( $plants ) ) { $cache_by_size[ $size ] = $out; return $out; } $expected_by_plant = array(); $expected_files = array(); foreach ( $plants as $plant_name ) { $slug = self::vitality_plant_slug( $plant_name ); if ( '' === $slug ) { $expected_by_plant[ $plant_name ] = ''; continue; } $fn = $slug . '_v100.png'; $expected_by_plant[ $plant_name ] = $fn; $expected_files[ $fn ] = true; } $ids = array(); $q = new WP_Query( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false, 'meta_query' => array( array( 'key' => '_wp_attached_file', 'value' => 'v100.png', 'compare' => 'LIKE', ), ), ) ); if ( ! empty( $q->posts ) ) { $ids = array_map( 'intval', $q->posts ); } if ( ! empty( $ids ) ) { update_meta_cache( 'post', $ids ); } $filename_to_id = array(); foreach ( $ids as $id ) { $meta = get_post_meta( $id, '_wp_attached_file', true ); if ( ! $meta ) { continue; } $base = basename( (string) $meta ); if ( '' === $base ) { continue; } if ( ! isset( $expected_files[ $base ] ) ) { continue; } if ( isset( $filename_to_id[ $base ] ) ) { continue; } $filename_to_id[ $base ] = (int) $id; } foreach ( $plants as $plant_name ) { $fn = isset( $expected_by_plant[ $plant_name ] ) ? $expected_by_plant[ $plant_name ] : ''; $url = ''; if ( $fn && isset( $filename_to_id[ $fn ] ) ) { $id = (int) $filename_to_id[ $fn ]; $url = wp_get_attachment_image_url( $id, $size ); if ( ! $url ) { $url = wp_get_attachment_url( $id ); } if ( ! $url ) { $url = ''; } } $out[ $plant_name ] = (string) $url; } $cache_by_size[ $size ] = $out; return $out; } 
    /**
     * Allowed Fails helpers (Chunk 3).
     *
     * These functions are intentionally "pure" sanitizers so later chunks (goal upsert / fail scoring / cron)
     * can call a single source of truth. Defaults represent "disabled" behavior.
     */
    public static function sanitize_allowed_fails_target( $value ) {
        if ( null === $value ) {
            return 0;
        }
        if ( is_string( $value ) ) {
            $value = trim( $value );
            if ( '' === $value ) {
                return 0;
            }
        }
        $i = (int) $value;
        if ( $i < 0 ) {
            $i = 0;
        }
        if ( $i > 999999 ) {
            $i = 999999;
        }
        return $i;
    }

    public static function sanitize_allowed_fails_unit( $value ) {
        $u = is_string( $value ) ? strtolower( trim( $value ) ) : '';
        if ( '' === $u ) {
            return 'week';
        }
        $map = array(
            'week'     => 'week',
            'weeks'    => 'week',
            'weekly'   => 'week',
            'month'    => 'month',
            'months'   => 'month',
            'monthly'  => 'month',
            'year'     => 'year',
            'years'    => 'year',
            'yearly'   => 'year',
            'annual'   => 'year',
            'annually' => 'year',
        );
        if ( isset( $map[ $u ] ) ) {
            $u = $map[ $u ];
        }
        if ( ! in_array( $u, array( 'week', 'month', 'year' ), true ) ) {
            $u = 'week';
        }
        return $u;
    }

    public static function sanitize_allowed_fails_span( $value ) {
        if ( null === $value ) {
            return 1;
        }
        if ( is_string( $value ) ) {
            $value = trim( $value );
        }
        $i = (int) $value;
        if ( $i < 1 ) {
            $i = 1;
        }
        if ( $i > 1000000 ) {
            $i = 1000000;
        }
        return $i;
    }

    /**
     * Returns a canonical, sanitized allowed-fails config array:
     *   [ 'target' => int>=0, 'unit' => week|month|year, 'span' => int>=1 ]
     *
     * If target is 0 (disabled), unit/span are returned as schema-safe defaults (week/1).
     */
    public static function sanitize_allowed_fails_config( $target, $unit, $span ) {
        $t = self::sanitize_allowed_fails_target( $target );
        if ( $t <= 0 ) {
            return array(
                'target' => 0,
                'unit'   => 'week',
                'span'   => 1,
            );
        }

        return array(
            'target' => $t,
            'unit'   => self::sanitize_allowed_fails_unit( $unit ),
            'span'   => self::sanitize_allowed_fails_span( $span ),
        );
    }


    /**
     * Composite goal helpers (Chunk 2).
     *
     * These remain pure normalizers so the DB layer, goal upsert, and later
     * settlement/render flows can share one canonical payload contract.
     */
    public static function composite_goal_config_defaults() {
        return array(
            'version' => 1,
            'enabled' => false,
            'summaryOnly' => true,
            'hideChildrenStandalone' => true,
        );
    }

    public static function sanitize_composite_goal_config( $value ) {
        $defaults = self::composite_goal_config_defaults();
        $raw = $value;

        if ( is_string( $raw ) ) {
            $raw = trim( $raw );
            if ( '' === $raw ) {
                return $defaults;
            }

            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                $raw = $decoded;
            } else {
                return $defaults;
            }
        }

        if ( ! is_array( $raw ) ) {
            return $defaults;
        }

        $version = isset( $raw['version'] ) ? (int) $raw['version'] : 1;
        if ( $version < 1 ) {
            $version = 1;
        }
        if ( $version > 9999 ) {
            $version = 9999;
        }

        $enabled = ! empty( $raw['enabled'] );
        $summary_only = array_key_exists( 'summaryOnly', $raw ) ? (bool) $raw['summaryOnly'] : true;
        $hide_children = array_key_exists( 'hideChildrenStandalone', $raw ) ? (bool) $raw['hideChildrenStandalone'] : true;

        return array(
            'version' => $version,
            'enabled' => $enabled,
            'summaryOnly' => $summary_only,
            'hideChildrenStandalone' => $hide_children,
        );
    }

    public static function sanitize_composite_goal_persistence_config( $value ) {
        $config = self::sanitize_composite_goal_config( $value );
        $config['enabled'] = true;
        $config['summaryOnly'] = true;
        $config['hideChildrenStandalone'] = true;
        if ( ! isset( $config['version'] ) || (int) $config['version'] < 1 ) {
            $config['version'] = 1;
        }
        return $config;
    }

    public static function is_composite_goal_type( $goal_type ) {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'is_composite_goal_type' ) ) ) {
            return (bool) TCT_Plugin::is_composite_goal_type( $goal_type );
        }

        return ( 'composite_parent' === strtolower( trim( (string) $goal_type ) ) );
    }

    public static function is_composite_child_goal_candidate( $goal_row ) {
        if ( ! is_array( $goal_row ) ) {
            return false;
        }

        $goal_type = isset( $goal_row['goal_type'] ) ? (string) $goal_row['goal_type'] : '';
        if ( self::is_composite_goal_type( $goal_type ) ) {
            return false;
        }
        if ( self::is_negative_goal_type( $goal_type ) ) {
            return false;
        }
        if ( self::is_positive_no_interval_goal_type( $goal_type ) ) {
            return false;
        }

        $interval = null;
        if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) {
            $interval = TCT_Interval::normalize_goal_interval_from_row( $goal_row );
        }

        if ( is_array( $interval ) ) {
            return ( isset( $interval['target'] ) && (int) $interval['target'] > 0 );
        }

        return ( isset( $goal_row['target'] ) && (int) $goal_row['target'] > 0 );
    }

    public static function sanitize_composite_child_id_list( $value ) {
        $items = array();

        if ( is_string( $value ) ) {
            $value = trim( $value );
            if ( '' === $value ) {
                return array();
            }

            $decoded = json_decode( $value, true );
            if ( is_array( $decoded ) ) {
                $items = $decoded;
            } else {
                $items = preg_split( '/[\s,]+/', $value );
            }
        } elseif ( is_array( $value ) ) {
            $items = $value;
        } else {
            return array();
        }

        $out = array();
        $seen = array();
        foreach ( $items as $item ) {
            if ( is_string( $item ) ) {
                $item = trim( $item );
            }
            $id = (int) $item;
            if ( $id <= 0 ) {
                continue;
            }
            if ( isset( $seen[ $id ] ) ) {
                continue;
            }
            $seen[ $id ] = true;
            $out[] = $id;
            if ( count( $out ) >= 500 ) {
                break;
            }
        }

        return $out;
    }

    public static function sanitize_composite_child_ids( $value ) {
        return self::sanitize_composite_child_id_list( $value );
    }


    /**
     * Composite goal settlement math helpers (Chunk 3).
     *
     * These helpers stay pure and deterministic so preview, ledger, cron,
     * and history adjustment paths can share one canonical implementation.
     */
    public static function composite_goal_scoring_defaults() {
        return array(
            'version' => 1,
            'progress_exponent' => 1.2,
            'perfect_bonus_rate' => 0.10,
            'zero_eligible_ratio' => 1.0,
            'weight_basis' => 'bonus_points',
        );
    }

    public static function composite_goal_progress_exponent() {
        $defaults = self::composite_goal_scoring_defaults();
        return isset( $defaults['progress_exponent'] ) ? (float) $defaults['progress_exponent'] : 1.2;
    }

    public static function composite_goal_perfect_bonus_rate() {
        $defaults = self::composite_goal_scoring_defaults();
        return isset( $defaults['perfect_bonus_rate'] ) ? (float) $defaults['perfect_bonus_rate'] : 0.10;
    }

    public static function composite_goal_zero_eligible_ratio() {
        $defaults = self::composite_goal_scoring_defaults();
        return isset( $defaults['zero_eligible_ratio'] ) ? (float) $defaults['zero_eligible_ratio'] : 1.0;
    }

    public static function composite_goal_epsilon() {
        return 0.0000001;
    }

    public static function composite_goal_clamp_ratio( $ratio ) {
        $ratio = (float) $ratio;
        if ( $ratio < 0.0 ) {
            return 0.0;
        }
        if ( $ratio > 1.0 ) {
            return 1.0;
        }
        return $ratio;
    }

    public static function composite_goal_finalize_point_value( $raw_points ) {
        $points = (int) round( (float) $raw_points );
        if ( $points > 999999 ) {
            $points = 999999;
        }
        if ( $points < -999999 ) {
            $points = -999999;
        }
        return $points;
    }

    public static function composite_goal_finalize_bonus_points( $raw_bonus ) {
        $points = self::composite_goal_finalize_point_value( $raw_bonus );
        if ( $points < 0 ) {
            $points = 0;
        }
        return $points;
    }

    public static function composite_goal_finalize_penalty_points( $raw_penalty_magnitude ) {
        $magnitude = abs( (float) $raw_penalty_magnitude );
        $points = self::composite_goal_finalize_point_value( $magnitude );
        if ( $points < 0 ) {
            $points = abs( $points );
        }
        return -1 * $points;
    }

    public static function composite_goal_child_completion_ratio( $achieved, $target ) {
        $target = (int) $target;
        $achieved = (int) $achieved;
        if ( $achieved < 0 ) {
            $achieved = 0;
        }
        if ( $target <= 0 ) {
            return 1.0;
        }
        return self::composite_goal_clamp_ratio( (float) $achieved / (float) $target );
    }

    public static function composite_goal_child_bonus_exposure( $child, $target = null ) {
        if ( is_array( $child ) ) {
            foreach ( array( 'bonus_points', 'bonus_points_exposure', 'bonusExposure' ) as $key ) {
                if ( isset( $child[ $key ] ) && is_numeric( $child[ $key ] ) ) {
                    $points = abs( (float) $child[ $key ] );
                    return (int) round( $points );
                }
            }
        }

        if ( null === $target ) {
            $target = is_array( $child ) && isset( $child['target'] ) ? (int) $child['target'] : 0;
        }

        $ppc = is_array( $child ) && isset( $child['points_per_completion'] ) ? (int) $child['points_per_completion'] : 0;
        if ( $ppc <= 0 || (int) $target <= 0 ) {
            return 0;
        }

        return (int) self::compute_bonus_points( $ppc, (int) $target );
    }

    public static function composite_goal_child_penalty_exposure( $child, $target = null ) {
        if ( is_array( $child ) ) {
            foreach ( array( 'penalty_points', 'penalty_points_exposure', 'penaltyExposure', 'penalty_magnitude' ) as $key ) {
                if ( isset( $child[ $key ] ) && is_numeric( $child[ $key ] ) ) {
                    $points = abs( (float) $child[ $key ] );
                    return (int) round( $points );
                }
            }
        }

        if ( null === $target ) {
            $target = is_array( $child ) && isset( $child['target'] ) ? (int) $child['target'] : 0;
        }

        $ppc = is_array( $child ) && isset( $child['points_per_completion'] ) ? (int) $child['points_per_completion'] : 0;
        if ( $ppc <= 0 || (int) $target <= 0 ) {
            return 0;
        }

        return abs( (int) self::compute_penalty_points( $ppc, (int) $target, 0 ) );
    }

    public static function composite_goal_child_penalties_fired_magnitude( $child ) {
        if ( ! is_array( $child ) ) {
            return 0;
        }

        foreach ( array( 'child_penalties_fired', 'child_penalty_points_fired', 'childPenaltyPointsFired', 'child_penalties_fired_magnitude' ) as $key ) {
            if ( isset( $child[ $key ] ) && is_numeric( $child[ $key ] ) ) {
                return max( 0, (int) round( abs( (float) $child[ $key ] ) ) );
            }
        }

        return 0;
    }

    public static function composite_goal_normalize_child_settlement_row( $child, $index = 0 ) {
        $row = is_array( $child ) ? $child : array();
        $goal_id = isset( $row['goal_id'] ) ? (int) $row['goal_id'] : ( isset( $row['id'] ) ? (int) $row['id'] : 0 );
        $goal_name = '';
        if ( isset( $row['goal_name'] ) ) {
            $goal_name = (string) $row['goal_name'];
        } elseif ( isset( $row['name'] ) ) {
            $goal_name = (string) $row['name'];
        }
        if ( function_exists( 'sanitize_text_field' ) ) {
            $goal_name = sanitize_text_field( $goal_name );
        } else {
            $goal_name = trim( $goal_name );
        }

        $goal_type = isset( $row['goal_type'] ) ? strtolower( trim( (string) $row['goal_type'] ) ) : '';
        $settlement_context = isset( $row['settlement_context'] ) && is_array( $row['settlement_context'] ) ? $row['settlement_context'] : array();

        $active_at_settlement = false;
        if ( array_key_exists( 'active_at_settlement', $row ) ) {
            $active_at_settlement = ! empty( $row['active_at_settlement'] );
        } elseif ( isset( $settlement_context['active_at_settlement'] ) ) {
            $active_at_settlement = ! empty( $settlement_context['active_at_settlement'] );
        } else {
            $active_at_settlement = true;
        }

        $paused_at_settlement = false;
        if ( array_key_exists( 'paused_at_settlement', $row ) ) {
            $paused_at_settlement = ! empty( $row['paused_at_settlement'] );
        } elseif ( isset( $settlement_context['paused_at_settlement'] ) ) {
            $paused_at_settlement = ! empty( $settlement_context['paused_at_settlement'] );
        }

        if ( array_key_exists( 'eligible', $row ) ) {
            $eligible = ! empty( $row['eligible'] );
        } elseif ( isset( $settlement_context['eligible'] ) ) {
            $eligible = ! empty( $settlement_context['eligible'] );
        } else {
            $eligible = $active_at_settlement;
        }

        $target = isset( $row['target'] ) ? (int) $row['target'] : 0;
        if ( $target <= 0 && isset( $settlement_context['interval_target'] ) ) {
            $target = (int) $settlement_context['interval_target'];
        }
        if ( $target < 0 ) {
            $target = 0;
        }

        $achieved = isset( $row['achieved'] ) ? (int) $row['achieved'] : 0;
        if ( $achieved < 0 ) {
            $achieved = 0;
        }

        $completion_ratio = self::composite_goal_child_completion_ratio( $achieved, $target );
        $bonus_points = self::composite_goal_child_bonus_exposure( $row, $target );
        $penalty_points = self::composite_goal_child_penalty_exposure( $row, $target );

        $progress_weight = 0;
        $progress_weight_basis = 'none';
        if ( $bonus_points > 0 ) {
            $progress_weight = $bonus_points;
            $progress_weight_basis = 'bonus_points';
        } elseif ( $penalty_points > 0 ) {
            $progress_weight = $penalty_points;
            $progress_weight_basis = 'penalty_points';
        }

        $weighted_completion = (float) $progress_weight * (float) $completion_ratio;
        $child_penalties_fired = self::composite_goal_child_penalties_fired_magnitude( $row );

        $window_start_utc_mysql = '';
        if ( isset( $settlement_context['window_start_utc_mysql'] ) && is_string( $settlement_context['window_start_utc_mysql'] ) ) {
            $window_start_utc_mysql = (string) $settlement_context['window_start_utc_mysql'];
        }

        $window_end_utc_mysql = '';
        if ( isset( $settlement_context['window_end_utc_mysql'] ) && is_string( $settlement_context['window_end_utc_mysql'] ) ) {
            $window_end_utc_mysql = (string) $settlement_context['window_end_utc_mysql'];
        }

        return array(
            'index' => max( 0, (int) $index ),
            'goal_id' => $goal_id,
            'goal_name' => $goal_name,
            'goal_type' => $goal_type,
            'eligible' => $eligible,
            'active_at_settlement' => $active_at_settlement,
            'paused_at_settlement' => $paused_at_settlement,
            'target' => $target,
            'achieved' => $achieved,
            'completion_ratio' => $completion_ratio,
            'bonus_points' => $bonus_points,
            'penalty_points' => $penalty_points,
            'progress_weight' => $progress_weight,
            'progress_weight_basis' => $progress_weight_basis,
            'weighted_completion' => $weighted_completion,
            'child_penalties_fired' => $child_penalties_fired,
            'window_start_utc_mysql' => $window_start_utc_mysql,
            'window_end_utc_mysql' => $window_end_utc_mysql,
            'settlement_context' => $settlement_context,
        );
    }

    public static function composite_goal_bonus_raw_from_ratio( $bmax, $ratio, $progress_exponent = null ) {
        $bmax = max( 0.0, (float) $bmax );
        if ( $bmax <= 0.0 ) {
            return 0.0;
        }
        $ratio = self::composite_goal_clamp_ratio( $ratio );
        if ( null === $progress_exponent ) {
            $progress_exponent = self::composite_goal_progress_exponent();
        }
        $progress_exponent = (float) $progress_exponent;
        if ( $progress_exponent <= 0.0 ) {
            $progress_exponent = self::composite_goal_progress_exponent();
        }
        return (float) $bmax * pow( (float) $ratio, (float) $progress_exponent );
    }

    public static function composite_goal_perfection_bonus_raw( $bmax, $is_perfect, $perfect_bonus_rate = null ) {
        if ( ! $is_perfect ) {
            return 0.0;
        }
        $bmax = max( 0.0, (float) $bmax );
        if ( $bmax <= 0.0 ) {
            return 0.0;
        }
        if ( null === $perfect_bonus_rate ) {
            $perfect_bonus_rate = self::composite_goal_perfect_bonus_rate();
        }
        $perfect_bonus_rate = max( 0.0, (float) $perfect_bonus_rate );
        if ( $perfect_bonus_rate <= 0.0 ) {
            return 0.0;
        }
        return (float) $bmax * (float) $perfect_bonus_rate;
    }

    public static function composite_goal_penalty_raw_magnitude_from_ratio( $pmax, $ratio ) {
        $pmax = max( 0.0, (float) $pmax );
        if ( $pmax <= 0.0 ) {
            return 0.0;
        }
        $ratio = self::composite_goal_clamp_ratio( $ratio );
        return (float) $pmax * ( 1.0 - (float) $ratio );
    }

    public static function composite_goal_penalty_cap_remaining( $pmax, $child_penalties_fired ) {
        $pmax = max( 0.0, (float) $pmax );
        $child_penalties_fired = max( 0.0, (float) $child_penalties_fired );
        $remaining = $pmax - $child_penalties_fired;
        if ( $remaining < 0.0 ) {
            $remaining = 0.0;
        }
        return $remaining;
    }

    public static function composite_goal_capped_penalty_raw_magnitude( $raw_penalty_magnitude, $pmax, $child_penalties_fired ) {
        $raw_penalty_magnitude = max( 0.0, (float) $raw_penalty_magnitude );
        $cap_remaining = self::composite_goal_penalty_cap_remaining( $pmax, $child_penalties_fired );
        if ( $cap_remaining <= 0.0 ) {
            return 0.0;
        }
        if ( $raw_penalty_magnitude > $cap_remaining ) {
            return $cap_remaining;
        }
        return $raw_penalty_magnitude;
    }

    public static function composite_goal_child_settlement_row_from_goal_row( $goal_row, $settlement_at = null, $options = array() ) {
        $goal_row = is_array( $goal_row ) ? $goal_row : array();
        $goal_id = isset( $goal_row['id'] ) ? (int) $goal_row['id'] : 0;
        $interval = null;
        $settlement_context = array();

        if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) {
            $interval = TCT_Interval::normalize_goal_interval_from_row( $goal_row );
        }

        if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'composite_child_settlement_context' ) ) ) {
            $settlement_context = TCT_Interval::composite_child_settlement_context( $goal_row, $settlement_at, null, $options );
        }

        $target = is_array( $interval ) && isset( $interval['target'] ) ? (int) $interval['target'] : ( isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0 );
        if ( $target < 0 ) {
            $target = 0;
        }

        $achieved = 0;
        if ( isset( $options['achieved'] ) && is_numeric( $options['achieved'] ) ) {
            $achieved = (int) $options['achieved'];
        } elseif ( $goal_id > 0 && isset( $options['child_achieved_map'] ) && is_array( $options['child_achieved_map'] ) && array_key_exists( $goal_id, $options['child_achieved_map'] ) ) {
            $achieved = (int) $options['child_achieved_map'][ $goal_id ];
        }
        if ( $achieved < 0 ) {
            $achieved = 0;
        }

        $child_penalties_fired = 0;
        if ( isset( $options['child_penalties_fired'] ) && is_numeric( $options['child_penalties_fired'] ) ) {
            $child_penalties_fired = max( 0, (int) round( abs( (float) $options['child_penalties_fired'] ) ) );
        } elseif ( $goal_id > 0 && isset( $options['child_penalties_fired_map'] ) && is_array( $options['child_penalties_fired_map'] ) && array_key_exists( $goal_id, $options['child_penalties_fired_map'] ) ) {
            $child_penalties_fired = max( 0, (int) round( abs( (float) $options['child_penalties_fired_map'][ $goal_id ] ) ) );
        }

        $child_seed = array(
            'goal_id' => $goal_id,
            'goal_name' => isset( $goal_row['goal_name'] ) ? $goal_row['goal_name'] : '',
            'goal_type' => isset( $goal_row['goal_type'] ) ? $goal_row['goal_type'] : '',
            'points_per_completion' => isset( $goal_row['points_per_completion'] ) ? (int) $goal_row['points_per_completion'] : 0,
            'target' => $target,
            'achieved' => $achieved,
            'eligible' => isset( $settlement_context['eligible'] ) ? ! empty( $settlement_context['eligible'] ) : true,
            'active_at_settlement' => isset( $settlement_context['active_at_settlement'] ) ? ! empty( $settlement_context['active_at_settlement'] ) : true,
            'paused_at_settlement' => isset( $settlement_context['paused_at_settlement'] ) ? ! empty( $settlement_context['paused_at_settlement'] ) : false,
            'child_penalties_fired' => $child_penalties_fired,
            'settlement_context' => $settlement_context,
        );

        if ( $goal_id > 0 && isset( $options['child_bonus_points_map'] ) && is_array( $options['child_bonus_points_map'] ) && array_key_exists( $goal_id, $options['child_bonus_points_map'] ) ) {
            $child_seed['bonus_points'] = $options['child_bonus_points_map'][ $goal_id ];
        }
        if ( $goal_id > 0 && isset( $options['child_penalty_points_map'] ) && is_array( $options['child_penalty_points_map'] ) && array_key_exists( $goal_id, $options['child_penalty_points_map'] ) ) {
            $child_seed['penalty_points'] = $options['child_penalty_points_map'][ $goal_id ];
        }

        return self::composite_goal_normalize_child_settlement_row( $child_seed );
    }

    public static function composite_goal_build_settlement_payload( $children, $options = array() ) {
        $defaults = self::composite_goal_scoring_defaults();
        $progress_exponent = isset( $options['progress_exponent'] ) ? (float) $options['progress_exponent'] : (float) $defaults['progress_exponent'];
        if ( $progress_exponent <= 0.0 ) {
            $progress_exponent = (float) $defaults['progress_exponent'];
        }

        $perfect_bonus_rate = isset( $options['perfect_bonus_rate'] ) ? (float) $options['perfect_bonus_rate'] : (float) $defaults['perfect_bonus_rate'];
        if ( $perfect_bonus_rate < 0.0 ) {
            $perfect_bonus_rate = 0.0;
        }

        $normalized_children = array();
        foreach ( (array) $children as $index => $child ) {
            $normalized_children[] = self::composite_goal_normalize_child_settlement_row( $child, $index );
        }

        $eligible_children = array();
        $weighted_completion_sum = 0.0;
        $weighted_possible_sum = 0.0;
        $bmax = 0.0;
        $pmax = 0.0;
        $child_penalties_fired = 0.0;
        $all_eligible_complete = true;

        foreach ( $normalized_children as $child ) {
            if ( empty( $child['eligible'] ) ) {
                continue;
            }
            $eligible_children[] = $child;
            $weighted_completion_sum += (float) $child['weighted_completion'];
            $weighted_possible_sum += (float) $child['progress_weight'];
            $bmax += (float) $child['bonus_points'];
            $pmax += (float) $child['penalty_points'];
            $child_penalties_fired += (float) $child['child_penalties_fired'];
            if ( (float) $child['completion_ratio'] < ( 1.0 - self::composite_goal_epsilon() ) ) {
                $all_eligible_complete = false;
            }
        }

        $eligible_child_count = count( $eligible_children );
        $has_eligible_children = ( $eligible_child_count > 0 );
        $treated_complete = ! $has_eligible_children;
        $has_scoring_exposure = $has_eligible_children && ( $bmax > 0.0 || $pmax > 0.0 );

        if ( $weighted_possible_sum > 0.0 ) {
            $ratio = self::composite_goal_clamp_ratio( $weighted_completion_sum / $weighted_possible_sum );
        } else {
            $ratio = self::composite_goal_zero_eligible_ratio();
        }

        if ( ! $has_eligible_children ) {
            $ratio = self::composite_goal_zero_eligible_ratio();
        }

        $is_complete = $treated_complete ? true : $all_eligible_complete;
        $is_perfect = $has_eligible_children && $all_eligible_complete;

        $bonus_raw = $has_scoring_exposure ? self::composite_goal_bonus_raw_from_ratio( $bmax, $ratio, $progress_exponent ) : 0.0;
        $perfection_bonus_raw = $has_scoring_exposure ? self::composite_goal_perfection_bonus_raw( $bmax, $is_perfect, $perfect_bonus_rate ) : 0.0;
        $penalty_raw_magnitude = $has_scoring_exposure ? self::composite_goal_penalty_raw_magnitude_from_ratio( $pmax, $ratio ) : 0.0;
        $penalty_cap_remaining = $has_scoring_exposure ? self::composite_goal_penalty_cap_remaining( $pmax, $child_penalties_fired ) : 0.0;
        $penalty_capped_raw_magnitude = $has_scoring_exposure ? self::composite_goal_capped_penalty_raw_magnitude( $penalty_raw_magnitude, $pmax, $child_penalties_fired ) : 0.0;

        $zero_exposure_reason = '';
        if ( ! $has_eligible_children ) {
            $zero_exposure_reason = 'no_eligible_children';
        } elseif ( ! $has_scoring_exposure ) {
            $zero_exposure_reason = 'no_scoring_exposure';
        }

        $parent_goal_id = isset( $options['parent_goal_id'] ) ? (int) $options['parent_goal_id'] : 0;
        $parent_goal_name = isset( $options['parent_goal_name'] ) ? (string) $options['parent_goal_name'] : '';
        if ( function_exists( 'sanitize_text_field' ) ) {
            $parent_goal_name = sanitize_text_field( $parent_goal_name );
        } else {
            $parent_goal_name = trim( $parent_goal_name );
        }

        $settlement_at = isset( $options['settlement_at'] ) ? $options['settlement_at'] : null;
        $window_start = isset( $options['window_start'] ) ? $options['window_start'] : '';
        $window_end = isset( $options['window_end'] ) ? $options['window_end'] : '';

        return array(
            'version' => isset( $defaults['version'] ) ? (int) $defaults['version'] : 1,
            'parent_goal_id' => $parent_goal_id,
            'parent_goal_name' => $parent_goal_name,
            'settlement_at' => $settlement_at,
            'window_start' => $window_start,
            'window_end' => $window_end,
            'progress_exponent' => $progress_exponent,
            'perfect_bonus_rate' => $perfect_bonus_rate,
            'children' => $normalized_children,
            'eligible_children' => array_values( $eligible_children ),
            'child_count' => count( $normalized_children ),
            'eligible_child_count' => $eligible_child_count,
            'ineligible_child_count' => count( $normalized_children ) - $eligible_child_count,
            'has_eligible_children' => $has_eligible_children,
            'has_scoring_exposure' => $has_scoring_exposure,
            'treated_complete' => $treated_complete,
            'is_complete' => $is_complete,
            'is_perfect' => $is_perfect,
            'ratio' => $ratio,
            'weighted_completion_sum' => $weighted_completion_sum,
            'weighted_possible_sum' => $weighted_possible_sum,
            'bmax' => $bmax,
            'pmax' => $pmax,
            'child_penalties_fired' => $child_penalties_fired,
            'penalty_cap_remaining' => $penalty_cap_remaining,
            'bonus_raw' => $bonus_raw,
            'perfection_bonus_raw' => $perfection_bonus_raw,
            'penalty_raw_magnitude' => $penalty_raw_magnitude,
            'penalty_raw' => -1.0 * $penalty_raw_magnitude,
            'penalty_capped_raw_magnitude' => $penalty_capped_raw_magnitude,
            'penalty_capped_raw' => -1.0 * $penalty_capped_raw_magnitude,
            'zero_exposure_reason' => $zero_exposure_reason,
        );
    }

    public static function composite_goal_settlement_payload_from_goal_rows( $child_goal_rows, $settlement_at = null, $options = array() ) {
        $rows = is_array( $child_goal_rows ) ? $child_goal_rows : array();
        $children = array();
        foreach ( $rows as $goal_row ) {
            $children[] = self::composite_goal_child_settlement_row_from_goal_row( $goal_row, $settlement_at, $options );
        }

        $payload_options = $options;
        if ( ! isset( $payload_options['settlement_at'] ) ) {
            $payload_options['settlement_at'] = $settlement_at;
        }

        return self::composite_goal_build_settlement_payload( $children, $payload_options );
    }

public static function maybe_cleanup_duplicate_assets() { if ( ! defined( 'TCT_PLUGIN_DIR' ) ) { return; } if ( ! is_admin() ) { return; } if ( ! current_user_can( 'manage_options' ) ) { return; } $state = get_option( 'tct_dup_asset_cleanup_v1', array() ); if ( ! is_array( $state ) ) { $state = array(); } $last_attempt = isset( $state['last_attempt'] ) ? (int) $state['last_attempt'] : 0; if ( $last_attempt > 0 && ( time() - $last_attempt ) < DAY_IN_SECONDS ) { return; } $plugin_dir = trailingslashit( TCT_PLUGIN_DIR ); $candidates = array(); $globs = array( $plugin_dir . '*.js', $plugin_dir . '*.css', ); foreach ( $globs as $pattern ) { $found = glob( $pattern ); if ( is_array( $found ) && ! empty( $found ) ) { $candidates = array_merge( $candidates, $found ); } } $deleted = array(); $failed = array(); $suffixes = array( '.bak', '.old', '.orig', '.backup' ); foreach ( $candidates as $path ) { if ( ! $path || ! is_string( $path ) ) { continue; } if ( ! file_exists( $path ) || ! is_file( $path ) ) { continue; } $base = basename( $path ); if ( '' === $base ) { continue; } $canonical_name = ''; if ( 0 === strpos( $base, '_' ) && strlen( $base ) > 1 ) { $canonical_name = substr( $base, 1 ); } if ( '' === $canonical_name && '~' === substr( $base, -1 ) && strlen( $base ) > 1 ) { $canonical_name = substr( $base, 0, -1 ); } if ( '' === $canonical_name ) { foreach ( $suffixes as $suf ) { $len = strlen( $suf ); if ( $len > 0 && strlen( $base ) > $len && $suf === substr( $base, -$len ) ) { $canonical_name = substr( $base, 0, -$len ); break; } } } if ( '' === $canonical_name ) { continue; } if ( ! preg_match( '/\.(js|css)$/', $canonical_name ) ) { continue; } $canonical_path = $plugin_dir . $canonical_name; if ( ! file_exists( $canonical_path ) || ! is_file( $canonical_path ) ) { continue; } if ( @unlink( $path ) ) { $deleted[] = $base; } else { $failed[] = $base; } } $state = array( 'last_attempt' => time(), 'deleted' => $deleted, 'failed' => $failed, ); update_option( 'tct_dup_asset_cleanup_v1', $state, false ); } } 
