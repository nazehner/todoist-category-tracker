<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } class TCT_Shortcode { const SHORTCODE = 'todoist_category_tracker'; const MOBILE_SHORTCODE = 'tct_mobile'; public function __construct() { add_shortcode( self::SHORTCODE, array( $this, 'render_shortcode' ) ); add_shortcode( self::MOBILE_SHORTCODE, array( $this, 'render_mobile_shortcode' ) ); add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) ); add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_mobile_assets' ) ); add_action( 'admin_post_tct_manual_sync', array( $this, 'handle_manual_sync' ) ); add_action( 'admin_post_tct_refresh_labels', array( $this, 'handle_refresh_labels' ) ); add_action( 'admin_post_tct_goal_upsert', array( $this, 'handle_goal_upsert' ) ); add_action( 'admin_post_tct_goal_delete', array( $this, 'handle_goal_delete' ) ); add_action( 'admin_post_tct_goal_archive', array( $this, 'handle_goal_archive' ) ); add_action( 'admin_post_tct_goal_restore', array( $this, 'handle_goal_restore' ) ); add_action( 'admin_post_tct_domain_upsert', array( $this, 'handle_domain_upsert' ) ); add_action( 'admin_post_tct_domain_delete', array( $this, 'handle_domain_delete' ) ); add_action( 'admin_post_tct_role_upsert', array( $this, 'handle_role_upsert' ) ); add_action( 'admin_post_tct_role_delete', array( $this, 'handle_role_delete' ) ); add_action( 'admin_post_tct_reward_save', array( $this, 'handle_reward_save' ) ); add_action( 'admin_post_tct_reward_redeem', array( $this, 'handle_reward_redeem' ) ); add_action( 'admin_post_tct_timezone_save', array( $this, 'handle_timezone_save' ) ); add_action( 'admin_post_tct_openai_save_key', array( $this, 'handle_openai_save_key' ) ); add_action( 'admin_post_tct_composite_feature_save', array( $this, 'handle_composite_feature_save' ) ); add_action( 'admin_post_tct_sleep_export_csv', array( $this, 'handle_sleep_export_csv' ) ); add_action( 'wp_ajax_tct_save_role_order', array( $this, 'handle_role_order_ajax' ) ); add_action( 'wp_ajax_tct_save_goal_order', array( $this, 'handle_goal_order_ajax' ) ); add_action( 'wp_ajax_tct_quick_complete', array( $this, 'handle_quick_complete_ajax' ) ); add_action( 'wp_ajax_tct_complete_composite_parent', array( $this, 'handle_complete_composite_parent_ajax' ) ); add_action( 'wp_ajax_tct_fail_goal', array( $this, 'handle_fail_goal_ajax' ) ); add_action( 'wp_ajax_tct_goal_history', array( $this, 'handle_goal_history_ajax' ) ); add_action( 'wp_ajax_tct_goal_success_stats', array( $this, 'handle_goal_success_stats_ajax' ) ); add_action( 'wp_ajax_tct_undo_completion', array( $this, 'handle_undo_completion_ajax' ) ); add_action( 'wp_ajax_tct_points_poll', array( $this, 'handle_points_poll_ajax' ) ); add_action( 'wp_ajax_tct_ui_snapshot', array( $this, 'handle_ui_snapshot_ajax' ) ); add_action( 'wp_ajax_tct_archived_goals_search', array( $this, 'handle_archived_goals_search_ajax' ) ); add_action( 'wp_ajax_tct_suggest_aliases', array( $this, 'handle_suggest_aliases_ajax' ) ); add_action( 'wp_ajax_tct_goal_heatmap', array( $this, 'handle_goal_heatmap_ajax' ) ); add_action( 'wp_ajax_tct_domain_heatmap', array( $this, 'handle_domain_heatmap_ajax' ) ); add_action( 'wp_ajax_tct_domain_yearbar', array( $this, 'handle_domain_yearbar_ajax' ) ); add_action( 'wp_ajax_tct_domain_monthbar', array( $this, 'handle_domain_monthbar_ajax' ) ); add_action( 'wp_ajax_tct_domain_weekbar', array( $this, 'handle_domain_weekbar_ajax' ) ); add_action( 'wp_ajax_tct_domain_month_heatmap', array( $this, 'handle_domain_month_heatmap_ajax' ) ); add_action( 'wp_ajax_tct_domain_week_heatmap', array( $this, 'handle_domain_week_heatmap_ajax' ) ); add_action( 'wp_ajax_tct_sleep_state', array( $this, 'handle_sleep_state_ajax' ) ); add_action( 'wp_ajax_tct_sleep_save_bedtime', array( $this, 'handle_sleep_save_bedtime_ajax' ) ); add_action( 'wp_ajax_tct_sleep_save_waketime', array( $this, 'handle_sleep_save_waketime_ajax' ) ); add_action( 'wp_ajax_tct_sleep_clear_cycle', array( $this, 'handle_sleep_clear_cycle_ajax' ) ); if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) { add_action( 'wp_ajax_tct_debug_goal_bounds', array( $this, 'handle_debug_goal_bounds_ajax' ) ); } add_action( 'wp_ajax_tct_experimental_settings_schema_status', array( $this, 'handle_experimental_settings_schema_status_ajax' ) ); add_action( 'wp_ajax_tct_experimental_settings_schema_migrate', array( $this, 'handle_experimental_settings_schema_migrate_ajax' ) ); add_action( 'wp_ajax_tct_mobile_search', array( $this, 'handle_mobile_search_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_search', array( $this, 'handle_mobile_search_ajax' ) ); add_action( 'wp_ajax_tct_mobile_chip_counts', array( $this, 'handle_mobile_chip_counts_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_chip_counts', array( $this, 'handle_mobile_chip_counts_ajax' ) ); add_action( 'wp_ajax_tct_mobile_chip_filter', array( $this, 'handle_mobile_chip_filter_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_chip_filter', array( $this, 'handle_mobile_chip_filter_ajax' ) ); add_action( 'wp_ajax_tct_mobile_domain_filter', array( $this, 'handle_mobile_domain_filter_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_domain_filter', array( $this, 'handle_mobile_domain_filter_ajax' ) ); add_action( 'wp_ajax_tct_mobile_domain_counts', array( $this, 'handle_mobile_domain_counts_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_domain_counts', array( $this, 'handle_mobile_domain_counts_ajax' ) ); add_action( 'wp_ajax_tct_mobile_daily_default', array( $this, 'handle_mobile_daily_default_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_daily_default', array( $this, 'handle_mobile_daily_default_ajax' ) ); add_action( 'wp_ajax_tct_mobile_favorites', array( $this, 'handle_mobile_favorites_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_favorites', array( $this, 'handle_mobile_favorites_ajax' ) ); add_action( 'wp_ajax_tct_mobile_ledger', array( $this, 'handle_mobile_ledger_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_ledger', array( $this, 'handle_mobile_ledger_ajax' ) ); add_action( 'wp_ajax_tct_mobile_heartbeat', array( $this, 'handle_mobile_heartbeat_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_heartbeat', array( $this, 'handle_mobile_heartbeat_ajax' ) ); add_action( 'wp_ajax_tct_mobile_reward_refresh', array( $this, 'handle_mobile_reward_refresh_ajax' ) ); add_action( 'wp_ajax_nopriv_tct_mobile_reward_refresh', array( $this, 'handle_mobile_reward_refresh_ajax' ) ); add_action( 'template_redirect', array( $this, 'maybe_hide_admin_bar_on_mobile' ) ); } public function maybe_enqueue_assets() { if ( is_admin() ) { return; } global $post; if ( ! $post || ! isset( $post->post_content ) ) { return; } if ( has_shortcode( $post->post_content, self::SHORTCODE ) ) { wp_enqueue_style( 'dashicons' ); $css_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'dashboard.css' ) : null; $js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'dashboard.js' ) : null; $css_ver = ( $css_path && file_exists( $css_path ) ) ? (string) filemtime( $css_path ) : TCT_VERSION; $js_ver = ( $js_path && file_exists( $js_path ) ) ? (string) filemtime( $js_path ) : TCT_VERSION; $shared_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'tct-shared.js' ) : null; $shared_js_ver = ( $shared_js_path && file_exists( $shared_js_path ) ) ? (string) filemtime( $shared_js_path ) : TCT_VERSION; wp_enqueue_style( 'tct-dashboard', TCT_PLUGIN_URL . 'dashboard.css', array(), $css_ver ); wp_enqueue_script( 'tct-shared-js', TCT_PLUGIN_URL . 'tct-shared.js', array(), $shared_js_ver, true ); wp_enqueue_script( 'tct-dashboard-js', TCT_PLUGIN_URL . 'dashboard.js', array( 'jquery', 'jquery-ui-sortable', 'tct-shared-js' ), $js_ver, true ); $allowed_fails_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'allowed-fails.js' ) : null; $allowed_fails_js_ver = ( $allowed_fails_js_path && file_exists( $allowed_fails_js_path ) ) ? (string) filemtime( $allowed_fails_js_path ) : TCT_VERSION; wp_enqueue_script( 'tct-allowed-fails-js', TCT_PLUGIN_URL . 'allowed-fails.js', array( 'tct-dashboard-js' ), $allowed_fails_js_ver, true ); $goal_order_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'goal-order.js' ) : null; $goal_order_js_ver = ( $goal_order_js_path && file_exists( $goal_order_js_path ) ) ? (string) filemtime( $goal_order_js_path ) : TCT_VERSION; wp_enqueue_script( 'tct-goal-order-js', TCT_PLUGIN_URL . 'goal-order.js', array( 'jquery', 'jquery-ui-sortable', 'tct-dashboard-js' ), $goal_order_js_ver, true ); $goal_aliases_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'goal-aliases.js' ) : null; $goal_aliases_js_ver = ( $goal_aliases_js_path && file_exists( $goal_aliases_js_path ) ) ? (string) filemtime( $goal_aliases_js_path ) : TCT_VERSION; wp_enqueue_script( 'tct-goal-aliases-js', TCT_PLUGIN_URL . 'goal-aliases.js', array( 'jquery', 'tct-dashboard-js' ), $goal_aliases_js_ver, true ); $dashboard_search_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'dashboard-search.js' ) : null; $dashboard_search_js_ver = ( $dashboard_search_js_path && file_exists( $dashboard_search_js_path ) ) ? (string) filemtime( $dashboard_search_js_path ) : TCT_VERSION; wp_enqueue_script( 'tct-dashboard-search-js', TCT_PLUGIN_URL . 'dashboard-search.js', array( 'tct-dashboard-js' ), $dashboard_search_js_ver, true ); $availability_cycle_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'availability-cycle.js' ) : null; $availability_cycle_js_ver = ( $availability_cycle_js_path && file_exists( $availability_cycle_js_path ) ) ? (string) filemtime( $availability_cycle_js_path ) : TCT_VERSION; wp_enqueue_script( 'tct-availability-cycle-js', TCT_PLUGIN_URL . 'availability-cycle.js', array( 'tct-dashboard-js' ), $availability_cycle_js_ver, true ); $interval_anchor_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'interval-anchor.js' ) : null; $interval_anchor_js_ver = ( $interval_anchor_js_path && file_exists( $interval_anchor_js_path ) ) ? (string) filemtime( $interval_anchor_js_path ) : TCT_VERSION; wp_enqueue_script( 'tct-interval-anchor-js', TCT_PLUGIN_URL . 'interval-anchor.js', array( 'tct-dashboard-js' ), $interval_anchor_js_ver, true ); $vitality_plants_js = array(); if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'get_vitality_plants' ) ) { $plants = TCT_Utils::get_vitality_plants(); $previews = method_exists( 'TCT_Utils', 'get_vitality_plant_previews' ) ? TCT_Utils::get_vitality_plant_previews( 'large' ) : array(); if ( is_array( $plants ) ) { foreach ( $plants as $pname ) { $pname = trim( (string) $pname ); if ( '' === $pname ) { continue; } $vitality_plants_js[] = array( 'name' => $pname, 'previewUrl' => ( isset( $previews[ $pname ] ) && is_string( $previews[ $pname ] ) ) ? $previews[ $pname ] : '', ); } } } $taken_plants_js = array(); $current_user_id = get_current_user_id(); if ( $current_user_id > 0 && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_goals' ) ) { global $wpdb; $goals_table = TCT_DB::table_goals(); $taken_rows = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT plant_name FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 AND plant_name IS NOT NULL AND plant_name != ''", $current_user_id ) ); if ( is_array( $taken_rows ) ) { foreach ( $taken_rows as $tname ) { $tname = trim( (string) $tname ); if ( '' !== $tname ) { $taken_plants_js[] = $tname; } } } } $sleep_enabled_goal_id = 0; if ( $current_user_id > 0 && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_goals' ) ) { global $wpdb; $goals_table = TCT_DB::table_goals(); $col = $wpdb->get_var( "SHOW COLUMNS FROM {$goals_table} LIKE 'sleep_tracking_enabled'" ); if ( $col ) { $sleep_enabled_goal_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 AND sleep_tracking_enabled = 1 ORDER BY id ASC LIMIT 1", $current_user_id ) ); } } $experimental_settings_schema = null; if ( current_user_can( 'manage_options' ) && class_exists( 'TCT_Admin' ) && is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) && TCT_Admin::is_experimental_features_enabled() ) { $installed_schema = (int) get_option( TCT_Admin::OPTION_NAME_SETTINGS_SCHEMA_VERSION, 0 ); $target_schema = (int) TCT_Admin::SETTINGS_SCHEMA_VERSION; $experimental_settings_schema = array( 'installedVersion' => (int) $installed_schema, 'targetVersion' => (int) $target_schema, 'needsMigration' => (bool) ( $installed_schema < $target_schema ), ); } wp_localize_script( 'tct-dashboard-js', 'tctDashboard', array( 'ajaxUrl' => admin_url( 'admin-ajax.php', 'relative' ), 'adminPostUrl' => admin_url( 'admin-post.php', 'relative' ), 'roleOrderNonce' => wp_create_nonce( 'tct_role_order' ), 'goalOrderNonce' => wp_create_nonce( 'tct_goal_order' ), 'quickCompleteNonce' => wp_create_nonce( 'tct_quick_complete' ), 'compositeParentCompleteNonce' => wp_create_nonce( 'tct_complete_composite_parent' ), 'compositeParentCompleteConfirm' => 'Complete all child goals for this parent? Blocked children will be skipped.', 'compositeParentCompleteError' => 'Could not complete all child goals. Please try again.', 'failGoalNonce' => wp_create_nonce( 'tct_fail_goal' ), 'goalHistoryNonce' => wp_create_nonce( 'tct_goal_history' ), 'undoCompletionNonce' => wp_create_nonce( 'tct_undo_completion' ), 'archivedGoalsSearchNonce' => wp_create_nonce( 'tct_archived_goals_search' ), 'goalDeleteNonce' => wp_create_nonce( 'tct_goal_delete' ), 'goalArchiveNonce' => wp_create_nonce( 'tct_goal_archive' ), 'goalHeatmapNonce' => wp_create_nonce( 'tct_goal_heatmap' ), 'domainHeatmapNonce' => wp_create_nonce( 'tct_domain_heatmap' ), 'domainYearbarNonce' => wp_create_nonce( 'tct_domain_yearbar' ), 'domainMonthbarNonce' => wp_create_nonce( 'tct_domain_monthbar' ), 'domainWeekbarNonce' => wp_create_nonce( 'tct_domain_weekbar' ), 'domainMonthHeatmapNonce' => wp_create_nonce( 'tct_domain_month_heatmap' ), 'domainWeekHeatmapNonce' => wp_create_nonce( 'tct_domain_week_heatmap' ), 'sleepStateNonce' => wp_create_nonce( 'tct_sleep_state' ), 'sleepBedtimeNonce' => wp_create_nonce( 'tct_sleep_save_bedtime' ), 'sleepWaketimeNonce' => wp_create_nonce( 'tct_sleep_save_waketime' ), 'sleepClearCycleNonce' => wp_create_nonce( 'tct_sleep_clear_cycle' ), 'pointsPollNonce' => wp_create_nonce( 'tct_points_poll' ), 'uiSnapshotNonce' => wp_create_nonce( 'tct_ui_snapshot' ), 'mobileSearchNonce' => wp_create_nonce( 'tct_mobile_search' ), 'debugGoalBoundsNonce' => ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) ? wp_create_nonce( 'tct_debug_goal_bounds' ) : '', 'backupNonce' => current_user_can( 'manage_options' ) ? wp_create_nonce( 'tct_backup' ) : '', 'experimentalSettingsSchemaStatusNonce' => current_user_can( 'manage_options' ) ? wp_create_nonce( 'tct_experimental_settings_schema_status' ) : '', 'experimentalSettingsSchemaMigrateNonce' => current_user_can( 'manage_options' ) ? wp_create_nonce( 'tct_experimental_settings_schema_migrate' ) : '', 'experimentalSettingsSchema' => $experimental_settings_schema, 'startOfWeek' => (int) get_option( 'start_of_week', 1 ), 'vitalityPlants' => $vitality_plants_js, 'takenPlants' => $taken_plants_js, 'sleepEnabledGoalId' => (int) $sleep_enabled_goal_id, 'i18n' => array( 'roleOrderError' => 'Could not save role order. Please refresh and try again.', 'goalOrderError' => 'Could not save goal order. Please refresh and try again.', 'quickCompleteError' => 'Could not complete task. Please try again.', 'goalHistoryError' => 'Could not load history. Please try again.', 'undoCompletionError' => 'Could not undo completion. Please try again.', 'undoCompletionConfirm' => 'Undo this completion?', 'archivedGoalsSearchError' => 'Could not search archived goals. Please try again.', 'goalHeatmapError' => 'Could not load completion map. Please try again.', 'domainHeatmapError' => 'Could not load domain heatmap. Please try again.', 'domainWeekHeatmapError' => 'Could not load week heatmap. Please try again.', ), 'composite' => $this->tct_composite_feature_config(), 'features' => array( 'experimental' => ( is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) ) ? (bool) TCT_Admin::is_experimental_features_enabled() : false, 'compositeGoals' => $this->tct_composite_goals_enabled(), ), ) ); } } private function tct_abbrev_time_ago( $from_ts, $to_ts ) { $from_ts = (int) $from_ts; $to_ts = (int) $to_ts; if ( $from_ts <= 0 || $to_ts <= 0 ) { return '--'; } $diff_s = $to_ts - $from_ts; if ( $diff_s < 0 ) { $diff_s = 0; } if ( $diff_s < 60 ) { return 'just now'; } $minute = 60; $hour = 3600; $day = 86400; $week = 604800; $month = 2592000; $year = 31536000; if ( $diff_s < $hour ) { $n = (int) floor( $diff_s / $minute ); if ( $n < 1 ) { $n = 1; } return (string) $n . 'm'; } if ( $diff_s < $day ) { $n = (int) floor( $diff_s / $hour ); if ( $n < 1 ) { $n = 1; } return (string) $n . 'h'; } if ( $diff_s < $week ) { $n = (int) floor( $diff_s / $day ); if ( $n < 1 ) { $n = 1; } return (string) $n . 'd'; } if ( $diff_s < $month ) { $n = (int) floor( $diff_s / $week ); if ( $n < 1 ) { $n = 1; } return (string) $n . 'w'; } if ( $diff_s < $year ) { $n = (int) floor( $diff_s / $month ); if ( $n < 1 ) { $n = 1; } return (string) $n . 'mo'; } $n = (int) floor( $diff_s / $year ); if ( $n < 1 ) { $n = 1; } return (string) $n . 'y'; } private function get_redirect_target_from_post() { $redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : ''; if ( ! $redirect_to ) { $redirect_to = wp_get_referer(); } if ( ! $redirect_to ) { $redirect_to = home_url( '/' ); } return $redirect_to; } public function handle_manual_sync() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_manual_sync' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $result = TCT_Sync::sync_user( $user_id, true ); if ( is_wp_error( $result ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_sync', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_sync_msg', rawurlencode( $result->get_error_message() ) ); wp_safe_redirect( $redirect_to ); exit; } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_sync', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_refresh_labels() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_refresh_labels' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $result = TCT_Sync::sync_labels( $user_id ); if ( is_wp_error( $result ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_labels', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_labels_msg', rawurlencode( $result->get_error_message() ) ); wp_safe_redirect( $redirect_to ); exit; } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_labels', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_reward_save() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_reward_save' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); if ( ! class_exists( 'TCT_Reward' ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward module is missing.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( method_exists( 'TCT_Reward', 'is_enabled' ) && ! TCT_Reward::is_enabled() ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Rewards are disabled. Enable TCT_FEATURE_REWARDS to use this feature.' ) ); wp_safe_redirect( $redirect_to ); exit; } $title = isset( $_POST['reward_title'] ) ? sanitize_text_field( wp_unslash( $_POST['reward_title'] ) ) : ''; $title = trim( (string) $title ); $cost_raw = isset( $_POST['reward_cost'] ) ? sanitize_text_field( wp_unslash( $_POST['reward_cost'] ) ) : ''; $monthly_raw = isset( $_POST['reward_monthly_savings'] ) ? sanitize_text_field( wp_unslash( $_POST['reward_monthly_savings'] ) ) : ''; $cost = (float) str_replace( ',', '.', (string) $cost_raw ); $monthly = (float) str_replace( ',', '.', (string) $monthly_raw ); if ( '' === $title ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward title is required.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( $cost <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward cost must be greater than 0.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( $monthly <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Monthly savings must be greater than 0.' ) ); wp_safe_redirect( $redirect_to ); exit; } $existing = TCT_Reward::get_active_reward( $user_id ); $reward = is_array( $existing ) ? $existing : array(); $old_attachment_id = isset( $reward['attachment_id'] ) ? (int) $reward['attachment_id'] : 0; $new_image_uploaded = false; $reward['title'] = $title; $reward['cost'] = (float) $cost; $reward['monthly_savings'] = (float) $monthly; $upload_error = ''; if ( isset( $_FILES['reward_image'] ) && is_array( $_FILES['reward_image'] ) ) { $file_err = isset( $_FILES['reward_image']['error'] ) ? (int) $_FILES['reward_image']['error'] : UPLOAD_ERR_NO_FILE; if ( UPLOAD_ERR_NO_FILE !== $file_err ) { if ( ! current_user_can( 'upload_files' ) ) { $upload_error = 'You do not have permission to upload files.'; } elseif ( UPLOAD_ERR_OK !== $file_err ) { switch ( $file_err ) { case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE: $upload_error = 'Upload failed: file is too large.'; break; case UPLOAD_ERR_PARTIAL: $upload_error = 'Upload failed: file was only partially uploaded.'; break; case UPLOAD_ERR_NO_TMP_DIR: $upload_error = 'Upload failed: missing a temporary folder on the server.'; break; case UPLOAD_ERR_CANT_WRITE: $upload_error = 'Upload failed: could not write file to disk.'; break; case UPLOAD_ERR_EXTENSION: $upload_error = 'Upload failed: a PHP extension stopped the file upload.'; break; default: $upload_error = 'Upload failed.'; break; } } else { if ( ! function_exists( 'wp_handle_upload' ) ) { require_once ABSPATH . 'wp-admin/includes/file.php'; } if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) { require_once ABSPATH . 'wp-admin/includes/image.php'; } if ( ! function_exists( 'media_handle_upload' ) ) { require_once ABSPATH . 'wp-admin/includes/media.php'; } $overrides = array( 'test_form' => false, 'mimes' => array( 'jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', ), ); $post_data = array( 'post_title' => 'TCT Reward: ' . $title, 'post_author' => $user_id, ); $attachment_id = media_handle_upload( 'reward_image', 0, $post_data, $overrides ); if ( is_wp_error( $attachment_id ) ) { $upload_error = $attachment_id->get_error_message(); } else { $reward['attachment_id'] = (int) $attachment_id; $new_image_uploaded = true; } } } } if ( empty( $reward['created_at_utc'] ) ) { $reward['created_at_utc'] = gmdate( 'Y-m-d H:i:s' ); } if ( ( ! isset( $reward['economy_hash'] ) || '' === (string) $reward['economy_hash'] ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $reward['economy_hash'] = TCT_Reward::compute_points_economy_hash( $user_id ); } $ok = TCT_Reward::set_active_reward( $user_id, $reward ); if ( ! $ok ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Failed to save reward.' ) ); wp_safe_redirect( $redirect_to ); exit; } $new_attachment_id = isset( $reward['attachment_id'] ) ? (int) $reward['attachment_id'] : 0; $image_changed = $new_image_uploaded && ( $new_attachment_id !== $old_attachment_id ); if ( $image_changed ) { if ( is_callable( array( 'TCT_Reward', 'delete_progress_variant_attachments' ) ) ) { TCT_Reward::delete_progress_variant_attachments( $user_id, true ); } if ( is_callable( array( 'TCT_Reward', 'clear_progress_variants' ) ) ) { TCT_Reward::clear_progress_variants( $user_id ); } if ( is_callable( array( 'TCT_Reward', 'clear_generation_job' ) ) ) { TCT_Reward::clear_generation_job( $user_id ); } $should_generate = class_exists( 'TCT_Admin' ) && TCT_Admin::is_progress_images_enabled(); if ( $should_generate && $new_attachment_id > 0 ) { if ( is_callable( array( 'TCT_Reward', 'queue_generation_job' ) ) ) { TCT_Reward::queue_generation_job( $user_id ); } } } if ( '' !== $upload_error ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $msg = 'Saved reward, but image upload failed: ' . $upload_error . ' (Allowed: JPG, PNG, WebP).'; $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); wp_safe_redirect( $redirect_to ); exit; } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_timezone_save() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_timezone_save' ); $redirect_to = $this->get_redirect_target_from_post(); if ( ! current_user_can( 'manage_options' ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_tz', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_tz_msg', rawurlencode( 'Only administrators can change the timezone.' ) ); wp_safe_redirect( $redirect_to ); exit; } $tz_value = isset( $_POST['tct_timezone'] ) ? sanitize_text_field( wp_unslash( $_POST['tct_timezone'] ) ) : ''; $tz_value = trim( $tz_value ); if ( '' !== $tz_value ) { try { new DateTimeZone( $tz_value ); } catch ( Exception $e ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_tz', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_tz_msg', rawurlencode( 'Invalid timezone selected.' ) ); wp_safe_redirect( $redirect_to ); exit; } } update_option( 'tct_timezone', $tz_value ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_tz', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_openai_save_key() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_openai_save_key' ); $redirect_to = $this->get_redirect_target_from_post(); if ( ! current_user_can( 'manage_options' ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_openai', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_openai_msg', rawurlencode( 'Only administrators can save the OpenAI API key.' ) ); wp_safe_redirect( $redirect_to ); exit; } $key = isset( $_POST['tct_openai_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['tct_openai_api_key'] ) ) : ''; $key = trim( (string) $key ); if ( '' !== $key ) { $key = preg_replace( '/\s+/', '', $key ); if ( strlen( $key ) < 20 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_openai', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_openai_msg', rawurlencode( 'That API key looks too short. Please paste the full OpenAI API key (or leave blank to clear).' ) ); wp_safe_redirect( $redirect_to ); exit; } } update_option( 'tct_openai_api_key', $key, false ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_openai', 'success' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_openai_msg', rawurlencode( '' === $key ? 'OpenAI API key cleared.' : 'OpenAI API key saved.' ) ); wp_safe_redirect( $redirect_to ); exit; } public function handle_composite_feature_save() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_composite_feature_save' ); $redirect_to = $this->get_redirect_target_from_post(); if ( ! current_user_can( 'manage_options' ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_composite_feature', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_composite_feature_msg', rawurlencode( 'Only administrators can change the composite parent goal setting.' ) ); wp_safe_redirect( $redirect_to ); exit; } $enabled = isset( $_POST['tct_composite_goals_enabled'] ) ? sanitize_text_field( wp_unslash( $_POST['tct_composite_goals_enabled'] ) ) : '0'; $enabled = in_array( strtolower( trim( (string) $enabled ) ), array( '1', 'true', 'yes', 'on' ), true ) ? '1' : '0'; $option_name = ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'composite_goals_option_name' ) ) ) ? TCT_Plugin::composite_goals_option_name() : 'tct_feature_composite_goals_enabled'; update_option( $option_name, $enabled, false ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_composite_feature', 'success' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_composite_feature_msg', rawurlencode( ( '1' === $enabled ) ? 'Composite parent goals enabled.' : 'Composite parent goals disabled.' ) ); wp_safe_redirect( $redirect_to ); exit; } public function handle_sleep_export_csv() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_sleep_export_csv' ); $user_id = get_current_user_id(); if ( $user_id <= 0 ) { wp_die( 'Unauthorized', 403 ); } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'list_completed_sleep_cycles' ) ) { wp_die( 'Sleep tracking is not available.', 400 ); } if ( method_exists( 'TCT_DB', 'table_sleep_cycles' ) ) { global $wpdb; $sleep_table = TCT_DB::table_sleep_cycles(); $pattern = $wpdb->esc_like( $sleep_table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { wp_die( 'Sleep tracking is not available.', 400 ); } } $rows = TCT_DB::list_completed_sleep_cycles( $user_id, 0, 0, 0 ); if ( ! is_array( $rows ) ) { $rows = array(); } $local_date = function_exists( 'current_time' ) ? current_time( 'Y-m-d' ) : ''; $filename = 'tct-sleep-' . ( ( is_string( $local_date ) && '' !== $local_date ) ? $local_date : gmdate( 'Y-m-d' ) ) . '.csv'; while ( ob_get_level() ) { @ob_end_clean(); } nocache_headers(); header( 'Content-Type: text/csv; charset=utf-8' ); header( 'Content-Disposition: attachment; filename="' . $filename . '"' ); header( 'Pragma: no-cache' ); header( 'Expires: 0' ); $out = fopen( 'php://output', 'w' ); if ( false === $out ) { wp_die( 'Could not open output stream.', 500 ); } fputcsv( $out, array( 'Night of', 'Time to bed', 'Time to wake up', 'Hours of sleep (HH:MM)' ) ); foreach ( $rows as $r ) { $sleep_date = isset( $r['sleep_date'] ) ? (string) $r['sleep_date'] : ''; $bed_time = isset( $r['bed_time'] ) ? (string) $r['bed_time'] : ''; $wake_time = isset( $r['wake_time'] ) ? (string) $r['wake_time'] : ''; $duration = isset( $r['duration_hhmm'] ) ? (string) $r['duration_hhmm'] : ''; if ( '' === $duration && method_exists( 'TCT_DB', 'calculate_sleep_duration_hhmm' ) ) { $duration = (string) TCT_DB::calculate_sleep_duration_hhmm( $bed_time, $wake_time ); } fputcsv( $out, array( $sleep_date, $bed_time, $wake_time, $duration ) ); } fclose( $out ); exit; } public function handle_reward_redeem() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_reward_redeem' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); if ( ! class_exists( 'TCT_Reward' ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward module is missing.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( method_exists( 'TCT_Reward', 'is_enabled' ) && ! TCT_Reward::is_enabled() ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Rewards are disabled. Enable TCT_FEATURE_REWARDS to use this feature.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( ! class_exists( 'TCT_Ledger' ) || ! method_exists( 'TCT_Ledger', 'insert_custom_event' ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Ledger helper is missing (insert_custom_event).' ) ); wp_safe_redirect( $redirect_to ); exit; } $reward = TCT_Reward::get_active_reward( $user_id ); if ( ! is_array( $reward ) || empty( $reward ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'No active reward to redeem.' ) ); wp_safe_redirect( $redirect_to ); exit; } $progress = TCT_Reward::compute_reward_progress( $user_id, $reward ); if ( ! is_array( $progress ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Could not compute reward progress.' ) ); wp_safe_redirect( $redirect_to ); exit; } $errors = ( isset( $progress['errors'] ) && is_array( $progress['errors'] ) ) ? $progress['errors'] : array(); if ( ! empty( $errors ) ) { $msg = 'Reward progress is unavailable: ' . implode( ' | ', array_map( 'sanitize_text_field', $errors ) ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); wp_safe_redirect( $redirect_to ); exit; } $target = isset( $progress['target_points'] ) ? (int) $progress['target_points'] : 0; $earned = isset( $progress['earned_points'] ) ? (int) $progress['earned_points'] : 0; $pct = isset( $progress['progress_pct'] ) ? (float) $progress['progress_pct'] : 0.0; $is_earned = ( isset( $progress['is_earned'] ) && $progress['is_earned'] ); if ( $target <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward target points are invalid. Check that you have at least one points-enabled goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( ! $is_earned ) { $pct_label = number_format_i18n( $pct, 1 ); $earned_label = number_format_i18n( $earned ); $target_label = number_format_i18n( $target ); $msg = 'Reward is not earned yet (' . $pct_label . '% -- ' . $earned_label . '/' . $target_label . ' pts).'; $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); wp_safe_redirect( $redirect_to ); exit; } $title = isset( $reward['title'] ) ? trim( (string) $reward['title'] ) : ''; if ( '' === $title ) { $title = 'Reward'; } $cost = isset( $reward['cost'] ) ? (float) $reward['cost'] : 0.0; $monthly = isset( $reward['monthly_savings'] ) ? (float) $reward['monthly_savings'] : 0.0; $created_at_utc = ( isset( $reward['created_at_utc'] ) && is_string( $reward['created_at_utc'] ) ) ? trim( (string) $reward['created_at_utc'] ) : ''; if ( '' === $created_at_utc ) { $created_at_utc = gmdate( 'Y-m-d H:i:s' ); } $redeemed_at_utc = current_time( 'mysql', true ); $redeem_id = 'reward_redeem_' . sha1( implode( '|', array( (string) $user_id, $created_at_utc, (string) $target ) ) ); $cost_label = number_format_i18n( $cost, 2 ); $details = 'Redeemed reward: ' . $title . ' (cost ' . $cost_label . ', -' . (int) $target . ' pts)'; $ok = TCT_Ledger::insert_custom_event( $user_id, 'reward_redeem', -1 * abs( (int) $target ), $redeemed_at_utc, $details, array( 'event_key' => $redeem_id, ) ); if ( ! $ok ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Failed to record redemption in the ledger.' ) ); wp_safe_redirect( $redirect_to ); exit; } $attachment_id = ( is_array( $reward ) && isset( $reward['attachment_id'] ) ) ? (int) $reward['attachment_id'] : 0; $image_url = ''; if ( $attachment_id > 0 ) { $image_url = wp_get_attachment_url( $attachment_id ); if ( ! $image_url ) { $image_url = ''; } } $start_ts = strtotime( $created_at_utc . ' UTC' ); $end_ts = strtotime( $redeemed_at_utc . ' UTC' ); $elapsed = 0; if ( false !== $start_ts && false !== $end_ts ) { $elapsed = (int) max( 0, $end_ts - $start_ts ); } $elapsed_days = (int) floor( $elapsed / 86400 ); $elapsed_label = ( false !== $start_ts && false !== $end_ts ) ? $this->tct_abbrev_time_ago( $start_ts, $end_ts ) : ''; $hof_entry = array( 'id' => $redeem_id, 'title' => $title, 'cost' => (float) $cost, 'monthly_savings' => (float) $monthly, 'points_cost' => (int) $target, 'created_at_utc' => $created_at_utc, 'redeemed_at_utc' => $redeemed_at_utc, 'elapsed_seconds' => (int) $elapsed, 'elapsed_days' => (int) $elapsed_days, 'elapsed_label' => $elapsed_label, 'attachment_id' => (int) $attachment_id, 'image_url' => (string) $image_url, ); $hof_ok = TCT_Reward::append_hof( $user_id, $hof_entry ); if ( is_callable( array( 'TCT_Reward', 'delete_progress_variant_attachments' ) ) ) { TCT_Reward::delete_progress_variant_attachments( $user_id, true ); } if ( is_callable( array( 'TCT_Reward', 'clear_progress_variants' ) ) ) { TCT_Reward::clear_progress_variants( $user_id ); } if ( is_callable( array( 'TCT_Reward', 'clear_generation_job' ) ) ) { TCT_Reward::clear_generation_job( $user_id ); } $cleared = TCT_Reward::set_active_reward( $user_id, null ); if ( ! $hof_ok ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward redeemed, but failed to save Hall of Fame entry.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( ! $cleared ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( 'Reward redeemed, but failed to clear active reward. Please refresh.' ) ); wp_safe_redirect( $redirect_to ); exit; } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_goal_upsert() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_goal_upsert' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $tct_reward_rebase = array( 'should_attempt' => false, 'hash_before' => '', 'ratio_before' => null, 'target_before' => 0, 'pct_before' => 0.0, ); if ( class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'is_enabled' ) && TCT_Reward::is_enabled() ) { $active_reward = TCT_Reward::get_active_reward( $user_id ); if ( is_array( $active_reward ) && ! empty( $active_reward ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $tct_reward_rebase['should_attempt'] = true; $tct_reward_rebase['hash_before'] = TCT_Reward::compute_points_economy_hash( $user_id ); if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } $before = TCT_Reward::compute_reward_progress( $user_id, $active_reward ); if ( is_array( $before ) ) { $errs = ( isset( $before['errors'] ) && is_array( $before['errors'] ) ) ? $before['errors'] : array(); $told = isset( $before['target_points'] ) ? (int) $before['target_points'] : 0; $eold = isset( $before['earned_points'] ) ? (int) $before['earned_points'] : 0; $pold = isset( $before['progress_pct'] ) ? (float) $before['progress_pct'] : 0.0; if ( $told > 0 && empty( $errs ) ) { $tct_reward_rebase['ratio_before'] = (float) $eold / (float) $told; $tct_reward_rebase['target_before'] = (int) $told; $tct_reward_rebase['pct_before'] = (float) $pold; } } } } $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id < 0 ) { $goal_id = 0; } $composite_upsert = array( 'is_composite' => false, 'config' => array(), 'config_json' => '', 'child_ids' => array(), 'should_sync' => false, ); $tracking_mode = 'manual'; $goal_type = isset( $_POST['goal_type'] ) ? sanitize_text_field( wp_unslash( $_POST['goal_type'] ) ) : 'positive'; $is_composite_goal = $this->tct_composite_goal_type_requested( $goal_type ); if ( $is_composite_goal && ! $this->tct_composite_goals_enabled() ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Composite goals are currently disabled.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( $is_composite_goal ) { $goal_type = $this->tct_composite_goal_type_slug(); } if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ) { if ( TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) { $goal_type = 'positive_no_int'; } } elseif ( 'positive_no_int' === strtolower( trim( (string) $goal_type ) ) ) { $goal_type = 'positive_no_int'; } if ( ! $is_composite_goal && 'positive_no_int' !== $goal_type && ! TCT_Utils::is_supported_goal_type( $goal_type ) ) { $goal_type = 'positive'; } $threshold = null; if ( 'harm_reduction' === $goal_type ) { $threshold_raw = isset( $_POST['threshold'] ) ? wp_unslash( $_POST['threshold'] ) : ''; if ( is_numeric( $threshold_raw ) ) { $threshold = (int) $threshold_raw; if ( $threshold < 0 ) { $threshold = 0; } } else { $threshold = 0; } } $label_name = ''; $goal_name = isset( $_POST['goal_name'] ) ? sanitize_text_field( wp_unslash( $_POST['goal_name'] ) ) : ''; if ( '' === $goal_name ) { if ( '' !== $label_name ) { $goal_name = $label_name; } else { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Goal name is required.' ) ); wp_safe_redirect( $redirect_to ); exit; } } $aliases_in = isset( $_POST['aliases'] ) ? (array) wp_unslash( $_POST['aliases'] ) : array(); $aliases_clean = array(); $seen_alias = array(); foreach ( $aliases_in as $a_raw ) { if ( is_array( $a_raw ) || is_object( $a_raw ) ) { continue; } $a = sanitize_text_field( (string) $a_raw ); $a = trim( $a ); if ( '' === $a ) { continue; } if ( strlen( $a ) > 120 ) { $a = substr( $a, 0, 120 ); } $k = strtolower( $a ); if ( $k === strtolower( $goal_name ) ) { continue; } if ( isset( $seen_alias[ $k ] ) ) { continue; } $seen_alias[ $k ] = true; $aliases_clean[] = $a; if ( count( $aliases_clean ) >= 20 ) { break; } } $aliases_json = ! empty( $aliases_clean ) ? wp_json_encode( $aliases_clean ) : ''; $link_url_raw = isset( $_POST['link_url'] ) ? wp_unslash( $_POST['link_url'] ) : ''; $link_url_raw = is_string( $link_url_raw ) ? trim( $link_url_raw ) : ''; $link_url = $this->normalize_goal_link_url_input( $link_url_raw ); if ( '' !== $link_url_raw && '' === $link_url ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Please enter a valid link URL. You can use example.com, https://example.com, android-app://com.example.app, an intent: URL, or just an Android package name like com.example.app.' ) ); wp_safe_redirect( $redirect_to ); exit; } $phone_number_raw = isset( $_POST['phone_number'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_number'] ) ) : ''; $phone_number_raw = is_string( $phone_number_raw ) ? trim( $phone_number_raw ) : ''; $phone_number_digits = '' !== $phone_number_raw ? preg_replace( '/\D+/', '', $phone_number_raw ) : ''; $phone_number_digits = is_string( $phone_number_digits ) ? trim( $phone_number_digits ) : ''; if ( '' !== $phone_number_digits && 10 !== strlen( $phone_number_digits ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Phone number must contain exactly 10 digits.' ) ); wp_safe_redirect( $redirect_to ); exit; } $sms_number_raw = isset( $_POST['sms_number'] ) ? sanitize_text_field( wp_unslash( $_POST['sms_number'] ) ) : ''; $sms_number_raw = is_string( $sms_number_raw ) ? trim( $sms_number_raw ) : ''; $sms_number_digits = '' !== $sms_number_raw ? preg_replace( '/\D+/', '', $sms_number_raw ) : ''; $sms_number_digits = is_string( $sms_number_digits ) ? trim( $sms_number_digits ) : ''; if ( '' !== $sms_number_digits && 10 !== strlen( $sms_number_digits ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Text number must contain exactly 10 digits.' ) ); wp_safe_redirect( $redirect_to ); exit; } $link_target_count = 0; if ( '' !== $link_url ) { $link_target_count++; } if ( '' !== $phone_number_digits ) { $link_target_count++; } if ( '' !== $sms_number_digits ) { $link_target_count++; } if ( $link_target_count > 1 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Use only one of link URL, call number, or text number.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( '' !== $phone_number_digits ) { $link_url = 'tel:' . $phone_number_digits; } elseif ( '' !== $sms_number_digits ) { $link_url = 'sms:' . $sms_number_digits; } $goal_notes = isset( $_POST['goal_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['goal_notes'] ) ) : ''; $goal_notes = is_string( $goal_notes ) ? trim( $goal_notes ) : ''; if ( strlen( $goal_notes ) > 8000 ) { $goal_notes = substr( $goal_notes, 0, 8000 ); } $plant_name = isset( $_POST['plant_name'] ) ? sanitize_text_field( wp_unslash( $_POST['plant_name'] ) ) : ''; $plant_name = trim( (string) $plant_name ); if ( '' !== $plant_name && class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'get_vitality_plants' ) ) { $allowed_plants = TCT_Utils::get_vitality_plants(); if ( is_array( $allowed_plants ) && ! in_array( $plant_name, $allowed_plants, true ) ) { $plant_name = ''; } } $timer_duration_seconds = 0; $timer_enabled = isset( $_POST['timer_enabled'] ) && '1' === (string) $_POST['timer_enabled']; if ( $timer_enabled && ( 'positive' === $goal_type || 'positive_no_int' === $goal_type ) ) { $timer_hours = isset( $_POST['timer_hours'] ) ? (int) $_POST['timer_hours'] : 0; $timer_minutes = isset( $_POST['timer_minutes'] ) ? (int) $_POST['timer_minutes'] : 0; $timer_seconds = isset( $_POST['timer_seconds'] ) ? (int) $_POST['timer_seconds'] : 0; $timer_hours = max( 0, min( 23, $timer_hours ) ); $timer_minutes = max( 0, min( 59, $timer_minutes ) ); $timer_seconds = max( 0, min( 59, $timer_seconds ) ); $timer_duration_seconds = ( $timer_hours * 3600 ) + ( $timer_minutes * 60 ) + $timer_seconds; if ( $timer_duration_seconds > 86400 ) { $timer_duration_seconds = 86400; } } $alarm_sound = ''; $alarm_duration = 0; $alarm_vibration = 0; if ( $timer_enabled && ( 'positive' === $goal_type || 'positive_no_int' === $goal_type ) && $timer_duration_seconds > 0 ) { $alarm_sound_raw = isset( $_POST['alarm_sound'] ) ? sanitize_text_field( wp_unslash( $_POST['alarm_sound'] ) ) : ''; $valid_sounds = array( 'soft_chime', 'meditation_bell', 'wind_chimes', 'gentle_pulse', 'standard_alert', 'digital_beep', 'rapid_pulse', 'urgent_alarm', 'alarm_clock', 'vibration_only' ); if ( in_array( $alarm_sound_raw, $valid_sounds, true ) ) { $alarm_sound = $alarm_sound_raw; } $alarm_duration_raw = isset( $_POST['alarm_duration'] ) ? (int) $_POST['alarm_duration'] : 0; $valid_durations = array( 5, 15, 30, 60, 600 ); if ( in_array( $alarm_duration_raw, $valid_durations, true ) ) { $alarm_duration = $alarm_duration_raw; } $alarm_vibration = isset( $_POST['alarm_vibration'] ) && '1' === (string) $_POST['alarm_vibration'] ? 1 : 0; } $visible_after_time = null; $vat_raw = isset( $_POST['visible_after_time'] ) ? sanitize_text_field( wp_unslash( $_POST['visible_after_time'] ) ) : ''; $vat_raw = trim( $vat_raw ); if ( '' !== $vat_raw && preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $vat_raw ) ) { $visible_after_time = $vat_raw; } $sleep_tracking_enabled = null; $sleep_rollover_time = null; if ( array_key_exists( 'sleep_tracking_enabled', $_POST ) ) { $raw = wp_unslash( $_POST['sleep_tracking_enabled'] ); if ( is_array( $raw ) ) { $raw = end( $raw ); } $sleep_tracking_enabled = ( '1' === (string) $raw ) ? 1 : 0; } if ( array_key_exists( 'sleep_rollover_time', $_POST ) ) { $sleep_rollover_time = sanitize_text_field( wp_unslash( $_POST['sleep_rollover_time'] ) ); $sleep_rollover_time = trim( (string) $sleep_rollover_time ); if ( '' === $sleep_rollover_time ) { $sleep_rollover_time = null; } } if ( 1 === (int) $sleep_tracking_enabled ) { if ( null === $sleep_rollover_time ) { $sleep_rollover_time = '18:00'; } if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', (string) $sleep_rollover_time ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid sleep rollover time. Please use HH:MM (00:00-23:59).' ) ); wp_safe_redirect( $redirect_to ); exit; } } elseif ( null !== $sleep_rollover_time ) { if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', (string) $sleep_rollover_time ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid sleep rollover time. Please use HH:MM (00:00-23:59).' ) ); wp_safe_redirect( $redirect_to ); exit; } } $wake_time_enabled = null; $wake_time_target = null; if ( array_key_exists( 'wake_time_enabled', $_POST ) ) { $raw = wp_unslash( $_POST['wake_time_enabled'] ); if ( is_array( $raw ) ) { $raw = end( $raw ); } $wake_time_enabled = ( '1' === (string) $raw ) ? 1 : 0; } if ( array_key_exists( 'wake_time_target', $_POST ) ) { $wake_time_target = sanitize_text_field( wp_unslash( $_POST['wake_time_target'] ) ); $wake_time_target = trim( (string) $wake_time_target ); } if ( null !== $wake_time_target && '' !== (string) $wake_time_target ) { if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', (string) $wake_time_target ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid wake-up target time. Please use HH:MM (00:00-23:59).' ) ); wp_safe_redirect( $redirect_to ); exit; } } $bed_time_enabled = null; $bed_time_target = null; if ( array_key_exists( 'bed_time_enabled', $_POST ) ) { $raw = wp_unslash( $_POST['bed_time_enabled'] ); if ( is_array( $raw ) ) { $raw = end( $raw ); } $bed_time_enabled = ( '1' === (string) $raw ) ? 1 : 0; } if ( array_key_exists( 'bed_time_target', $_POST ) ) { $bed_time_target = sanitize_text_field( wp_unslash( $_POST['bed_time_target'] ) ); $bed_time_target = trim( (string) $bed_time_target ); } if ( null !== $bed_time_target && '' !== (string) $bed_time_target ) { if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', (string) $bed_time_target ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid bed-time target time. Please use HH:MM (00:00-23:59).' ) ); wp_safe_redirect( $redirect_to ); exit; } } if ( 1 === (int) $wake_time_enabled && 1 === (int) $bed_time_enabled ) { $bed_time_enabled = 0; $bed_time_target = null; } $domain_id = 0; $role_id = isset( $_POST['role_id'] ) ? (int) wp_unslash( $_POST['role_id'] ) : 0; if ( $role_id < 0 ) { $role_id = 0; } $importance = isset( $_POST['importance'] ) ? (int) wp_unslash( $_POST['importance'] ) : 0; $effort = isset( $_POST['effort'] ) ? (int) wp_unslash( $_POST['effort'] ) : 0; if ( $importance < 0 ) { $importance = 0; } if ( $importance > 5 ) { $importance = 5; } if ( $effort < 0 ) { $effort = 0; } if ( $effort > 5 ) { $effort = 5; } $points_per_completion = $is_composite_goal ? 0 : TCT_Utils::compute_points_per_completion( $importance, $effort ); $primary_target = 0; $primary_unit = 'week'; $primary_span = 1; $primary_mode = 'calendar'; $intervals_json = wp_json_encode( array() ); if ( 'positive_no_int' !== $goal_type ) { $intervals_raw = isset( $_POST['intervals_json'] ) ? wp_unslash( $_POST['intervals_json'] ) : ''; if ( ! is_string( $intervals_raw ) || '' === trim( $intervals_raw ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Missing goal intervals.' ) ); wp_safe_redirect( $redirect_to ); exit; } $decoded = json_decode( $intervals_raw, true ); if ( ! is_array( $decoded ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid goal intervals JSON.' ) ); wp_safe_redirect( $redirect_to ); exit; } $intervals = $this->sanitize_intervals( $decoded ); if ( empty( $intervals ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Please add at least one interval with a target greater than 0.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) && TCT_Utils::is_anki_cards_goal_type( $goal_type ) ) { $anki_target = isset( $intervals[0]['target'] ) ? (int) $intervals[0]['target'] : 0; if ( $anki_target <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Please enter a daily Anki cards target greater than 0.' ) ); wp_safe_redirect( $redirect_to ); exit; } $intervals = array( array( 'target' => $anki_target, 'period_unit' => 'day', 'period_span' => 1, 'period_mode' => 'calendar' ) ); } $primary_target = (int) $intervals[0]['target']; $primary_unit = (string) $intervals[0]['period_unit']; $primary_span = 1; if ( isset( $intervals[0]['period_span'] ) ) { $primary_span = (int) $intervals[0]['period_span']; } if ( $primary_span < 1 ) { $primary_span = 1; } if ( $primary_span > 1000000 ) { $primary_span = 1000000; } $primary_mode = 'calendar'; $intervals_json = wp_json_encode( $intervals ); } $allowed_fails_target = 0; $allowed_fails_unit = 'week'; $allowed_fails_span = 1; $eligible_for_allowed_fails = false; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_allowed_fails' ) ) ) { $eligible_for_allowed_fails = (bool) TCT_Interval::is_goal_eligible_for_allowed_fails( array( 'goal_type' => $goal_type, 'target' => $primary_target, 'period_unit' => $primary_unit, 'period_span' => $primary_span, 'period_mode' => $primary_mode, 'allowed_fails_target' => (int) $allowed_fails_target, 'allowed_fails_unit' => (string) $allowed_fails_unit, 'allowed_fails_span' => (int) $allowed_fails_span, 'intervals_json' => $intervals_json, ) ); } if ( $eligible_for_allowed_fails ) { $af_target_raw = isset( $_POST['allowed_fails_target'] ) ? wp_unslash( $_POST['allowed_fails_target'] ) : 0; $af_unit_raw = isset( $_POST['allowed_fails_unit'] ) ? wp_unslash( $_POST['allowed_fails_unit'] ) : 'week'; $af_span_raw = isset( $_POST['allowed_fails_span'] ) ? wp_unslash( $_POST['allowed_fails_span'] ) : 1; if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_allowed_fails_config' ) ) ) { $af_cfg = TCT_Utils::sanitize_allowed_fails_config( $af_target_raw, $af_unit_raw, $af_span_raw ); } else { $af_t = (int) $af_target_raw; if ( $af_t < 0 ) { $af_t = 0; } $af_u = is_string( $af_unit_raw ) ? strtolower( trim( $af_unit_raw ) ) : 'week'; if ( ! in_array( $af_u, array( 'week', 'month', 'year' ), true ) ) { $af_u = 'week'; } $af_s = (int) $af_span_raw; if ( $af_s < 1 ) { $af_s = 1; } if ( $af_t <= 0 ) { $af_t = 0; $af_u = 'week'; $af_s = 1; } $af_cfg = array( 'target' => $af_t, 'unit' => $af_u, 'span' => $af_s ); } if ( is_array( $af_cfg ) ) { if ( isset( $af_cfg['target'] ) ) { $allowed_fails_target = (int) $af_cfg['target']; } if ( isset( $af_cfg['unit'] ) ) { $allowed_fails_unit = (string) $af_cfg['unit']; } if ( isset( $af_cfg['span'] ) ) { $allowed_fails_span = (int) $af_cfg['span']; } } } else { $allowed_fails_target = 0; $allowed_fails_unit = 'week'; $allowed_fails_span = 1; } if ( $points_per_completion > 0 ) { TCT_Ledger::ensure_points_started( $user_id ); } global $wpdb; if ( $role_id > 0 ) { $roles_table = TCT_DB::table_roles(); $role_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, domain_id FROM {$roles_table} WHERE id = %d AND user_id = %d", $role_id, $user_id ), ARRAY_A ); if ( ! is_array( $role_row ) || empty( $role_row['id'] ) ) { $role_id = 0; } else { $domain_id = (int) $role_row['domain_id']; } } if ( $domain_id > 0 ) { $domains_table = TCT_DB::table_domains(); $exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$domains_table} WHERE id = %d AND user_id = %d", $domain_id, $user_id ) ); if ( $exists <= 0 ) { $domain_id = 0; $role_id = 0; } } $table = TCT_DB::table_goals(); $now = current_time( 'mysql', true ); $existing_enabled_at = ''; $existing_row = null; $select_due = $this->tct_goal_due_schedule_column_exists() ? ', due_schedule_json' : ''; $select_availability = $this->tct_goal_availability_cycle_column_exists() ? ', availability_cycle_json' : ''; $select_interval_anchor = $this->tct_goal_interval_anchor_column_exists() ? ', interval_anchor_json' : ''; $select_composite = ', ' . $this->tct_goal_composite_config_select_sql(); if ( $goal_id > 0 ) { $existing_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, points_enabled_at, goal_type, role_id, domain_id, is_tracked{$select_due}{$select_availability}{$select_interval_anchor}{$select_composite} FROM {$table} WHERE user_id = %d AND id = %d", $user_id, $goal_id ), ARRAY_A ); if ( is_array( $existing_row ) && isset( $existing_row['id'] ) ) { if ( isset( $existing_row['points_enabled_at'] ) && is_string( $existing_row['points_enabled_at'] ) ) { $existing_enabled_at = trim( (string) $existing_row['points_enabled_at'] ); } } else { $goal_id = 0; } } if ( 0 === $goal_id && '' !== $label_name ) { $existing_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, points_enabled_at, goal_type, role_id, domain_id, is_tracked{$select_due}{$select_availability}{$select_interval_anchor}{$select_composite} FROM {$table} WHERE user_id = %d AND label_name = %s", $user_id, $label_name ), ARRAY_A ); if ( is_array( $existing_row ) && isset( $existing_row['id'] ) ) { $goal_id = (int) $existing_row['id']; if ( isset( $existing_row['points_enabled_at'] ) && is_string( $existing_row['points_enabled_at'] ) ) { $existing_enabled_at = trim( (string) $existing_row['points_enabled_at'] ); } } } $composite_upsert = $this->tct_composite_plan_upsert( $goal_type, $goal_id, $user_id, $role_id, $domain_id, $existing_row ); if ( is_wp_error( $composite_upsert ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( $composite_upsert->get_error_message() ) ); wp_safe_redirect( $redirect_to ); exit; } $is_composite_goal = ! empty( $composite_upsert['is_composite'] ); if ( $is_composite_goal ) { $goal_type = $this->tct_composite_goal_type_slug(); $importance = 0; $effort = 0; $points_per_completion = 0; $timer_duration_seconds = 0; $alarm_sound = ''; $alarm_duration = 0; $alarm_vibration = 0; $sleep_tracking_enabled = 0; $sleep_rollover_time = null; $wake_time_enabled = 0; $wake_time_target = null; $bed_time_enabled = 0; $bed_time_target = null; } if ( 1 === (int) $sleep_tracking_enabled ) { $other_sleep_goal_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table}
                     WHERE user_id = %d
                       AND is_tracked = 1
                       AND sleep_tracking_enabled = 1
                       AND id <> %d
                     LIMIT 1", $user_id, $goal_id ) ); if ( $other_sleep_goal_id > 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Sleep tracking can only be enabled on one active goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } } $points_enabled_at = $existing_enabled_at; if ( '' === $points_enabled_at || '0000-00-00 00:00:00' === $points_enabled_at ) { $points_enabled_at = ''; } if ( $points_per_completion > 0 && '' === $points_enabled_at ) { $points_enabled_at = $now; } $data = array( 'user_id' => (int) $user_id, 'tracking_mode' => $tracking_mode, 'goal_name' => $goal_name, 'goal_type' => $goal_type, 'threshold' => $threshold, 'timer_duration_seconds' => (int) $timer_duration_seconds, 'alarm_sound' => (string) $alarm_sound, 'alarm_duration' => (int) $alarm_duration, 'alarm_vibration' => (int) $alarm_vibration, 'domain_id' => (int) $domain_id, 'role_id' => (int) $role_id, 'is_tracked' => 1, 'target' => $primary_target, 'period_unit' => $primary_unit, 'period_span' => (int) $primary_span, 'period_mode' => $primary_mode, 'allowed_fails_target' => (int) $allowed_fails_target, 'allowed_fails_unit' => (string) $allowed_fails_unit, 'allowed_fails_span' => (int) $allowed_fails_span, 'intervals_json' => $intervals_json, 'points_per_completion'=> (int) $points_per_completion, 'importance' => (int) $importance, 'effort' => (int) $effort, 'points_enabled_at' => $points_enabled_at ? $points_enabled_at : null, 'updated_at' => $now, ); $formats = array( '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%d', '%s', '%d', '%s', '%d', '%d', '%d', '%s', '%s' ); if ( $this->tct_goal_aliases_column_exists() ) { $data['aliases_json'] = $aliases_json; $formats[] = '%s'; } if ( $this->tct_goal_link_url_column_exists() ) { $data['link_url'] = $link_url; $formats[] = '%s'; } if ( $this->tct_goal_notes_column_exists() ) { $data['goal_notes'] = $goal_notes; $formats[] = '%s'; }
        if ( $is_composite_goal && $this->tct_goal_composite_config_column_exists() ) { $data['composite_config_json'] = isset( $composite_upsert['config_json'] ) ? (string) $composite_upsert['config_json'] : ''; $formats[] = '%s'; }
        if ( $this->tct_goal_due_schedule_column_exists() ) {
            $due_enabled = isset( $_POST['due_schedule_enabled'] ) ? intval( $_POST['due_schedule_enabled'] ) : 0;
            $due_type = isset( $_POST['due_schedule_type'] ) ? sanitize_text_field( wp_unslash( $_POST['due_schedule_type'] ) ) : 'weekly';
            $due_start = isset( $_POST['due_schedule_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['due_schedule_start_date'] ) ) : '';
            $due_every_n = isset( $_POST['due_schedule_every_n'] ) ? intval( $_POST['due_schedule_every_n'] ) : 1;
            $due_dom = isset( $_POST['due_schedule_day_of_month'] ) ? intval( $_POST['due_schedule_day_of_month'] ) : 1;
            if ( 'manual' !== $tracking_mode ) { $due_enabled = 0; }
            $due_schedule_json = '';
            if ( $due_enabled ) {
                if ( 'weekly' !== $due_type && 'monthly' !== $due_type ) { $due_type = 'weekly'; }
                $due_every_n = max( 1, min( 52, $due_every_n ) );
                $due_dom = max( 1, min( 31, $due_dom ) );
                $due_cfg = array( 'enabled' => 1, 'type' => $due_type, 'start_date' => $due_start );
                if ( 'weekly' === $due_type ) { $due_cfg['every'] = $due_every_n; } else { $due_cfg['day_of_month'] = $due_dom; }
                $old_cfg_raw = '';
                if ( is_array( $existing_row ) && isset( $existing_row['due_schedule_json'] ) ) { $old_cfg_raw = (string) $existing_row['due_schedule_json']; }
                $old_cfg_norm = TCT_Interval::normalize_due_schedule_config( $old_cfg_raw );
                $new_cfg_norm = TCT_Interval::normalize_due_schedule_config( $due_cfg );
                if ( ! empty( $new_cfg_norm['enabled'] ) ) {
                    $old_pattern = array( 'enabled' => ! empty( $old_cfg_norm['enabled'] ) ? 1 : 0, 'type' => isset( $old_cfg_norm['type'] ) ? (string) $old_cfg_norm['type'] : '', 'start_date' => isset( $old_cfg_norm['start_date'] ) ? (string) $old_cfg_norm['start_date'] : '', 'every' => isset( $old_cfg_norm['every'] ) ? intval( $old_cfg_norm['every'] ) : 0, 'day_of_month' => isset( $old_cfg_norm['day_of_month'] ) ? intval( $old_cfg_norm['day_of_month'] ) : 0 );
                    $new_pattern = array( 'enabled' => 1, 'type' => isset( $new_cfg_norm['type'] ) ? (string) $new_cfg_norm['type'] : '', 'start_date' => isset( $new_cfg_norm['start_date'] ) ? (string) $new_cfg_norm['start_date'] : '', 'every' => isset( $new_cfg_norm['every'] ) ? intval( $new_cfg_norm['every'] ) : 0, 'day_of_month' => isset( $new_cfg_norm['day_of_month'] ) ? intval( $new_cfg_norm['day_of_month'] ) : 0 );
                    $pattern_changed = ( wp_json_encode( $old_pattern ) !== wp_json_encode( $new_pattern ) );
                    $tz = TCT_Utils::wp_timezone();
                    $today_ymd = ( new DateTimeImmutable( 'now', $tz ) )->format( 'Y-m-d' );
                    $effective_from = $today_ymd;
                    if ( isset( $new_cfg_norm['start_date'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $new_cfg_norm['start_date'] ) && strcmp( (string) $new_cfg_norm['start_date'], $today_ymd ) > 0 ) { $effective_from = (string) $new_cfg_norm['start_date']; }
                    if ( ! empty( $old_cfg_norm['enabled'] ) && ! $pattern_changed && isset( $old_cfg_norm['effective_from'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $old_cfg_norm['effective_from'] ) ) { $effective_from = (string) $old_cfg_norm['effective_from']; }
                    $new_cfg_norm['effective_from'] = $effective_from;
                    $due_schedule_json = wp_json_encode( $new_cfg_norm );
                }
            }
            $data['due_schedule_json'] = $due_schedule_json;
            $formats[] = '%s';
        }
        if ( $this->tct_goal_availability_cycle_column_exists() ) {
            $availability_cycle_json = '';
            $existing_availability_raw = '';
            if ( is_array( $existing_row ) && isset( $existing_row['availability_cycle_json'] ) && is_string( $existing_row['availability_cycle_json'] ) ) { $existing_availability_raw = trim( (string) $existing_row['availability_cycle_json'] ); }
            $availability_post_keys = array( 'availability_cycle_json', 'availability_cycle_enabled', 'availability_enabled', 'availability_cycle_anchor_date_local', 'availability_anchor_date_local', 'availability_cycle_anchor_date', 'availability_anchor_date', 'availability_cycle_anchor_phase', 'availability_anchor_phase', 'availability_cycle_anchor_day', 'availability_anchor_day', 'availability_cycle_anchor_day_within_phase', 'availability_anchor_day_within_phase', 'availability_cycle_active_duration', 'availability_active_duration', 'availability_cycle_pause_duration', 'availability_pause_duration' );
            $availability_non_json_keys = array( 'availability_cycle_enabled', 'availability_enabled', 'availability_cycle_anchor_date_local', 'availability_anchor_date_local', 'availability_cycle_anchor_date', 'availability_anchor_date', 'availability_cycle_anchor_phase', 'availability_anchor_phase', 'availability_cycle_anchor_day', 'availability_anchor_day', 'availability_cycle_anchor_day_within_phase', 'availability_anchor_day_within_phase', 'availability_cycle_active_duration', 'availability_active_duration', 'availability_cycle_pause_duration', 'availability_pause_duration' );
            $availability_present = false;
            foreach ( $availability_post_keys as $availability_key ) {
                if ( array_key_exists( $availability_key, $_POST ) ) {
                    $availability_present = true;
                    break;
                }
            }
            $availability_enabled_present = false;
            $availability_enabled_raw = null;
            foreach ( array( 'availability_cycle_enabled', 'availability_enabled' ) as $availability_enabled_key ) {
                if ( array_key_exists( $availability_enabled_key, $_POST ) ) {
                    $availability_enabled_present = true;
                    $availability_enabled_raw = wp_unslash( $_POST[ $availability_enabled_key ] );
                    if ( is_array( $availability_enabled_raw ) ) { $availability_enabled_raw = end( $availability_enabled_raw ); }
                    break;
                }
            }
            $availability_non_json_present = false;
            foreach ( $availability_non_json_keys as $availability_non_json_key ) {
                if ( array_key_exists( $availability_non_json_key, $_POST ) ) {
                    $availability_non_json_present = true;
                    break;
                }
            }
            $availability_enabled_scalar = null;
            if ( $availability_enabled_present ) {
                if ( is_bool( $availability_enabled_raw ) ) {
                    $availability_enabled_scalar = $availability_enabled_raw ? '1' : '0';
                } else {
                    $availability_enabled_scalar = strtolower( trim( (string) $availability_enabled_raw ) );
                }
            }
            $availability_explicit_disable = ( null !== $availability_enabled_scalar && in_array( $availability_enabled_scalar, array( '', '0', 'false', 'off', 'no' ), true ) );
            $availability_raw_json = null;
            if ( array_key_exists( 'availability_cycle_json', $_POST ) ) {
                $availability_raw_json = wp_unslash( $_POST['availability_cycle_json'] );
                if ( is_array( $availability_raw_json ) ) { $availability_raw_json = end( $availability_raw_json ); }
            }
            $availability_cfg_input = null;
            if ( ! $availability_present ) {
                if ( '' !== $existing_availability_raw ) { $availability_cfg_input = $existing_availability_raw; }
            } elseif ( $availability_explicit_disable ) {
                $availability_cycle_json = '';
            } elseif ( is_string( $availability_raw_json ) && '' === trim( $availability_raw_json ) && ! $availability_non_json_present ) {
                $availability_cycle_json = '';
            } elseif ( is_string( $availability_raw_json ) && '' !== trim( $availability_raw_json ) ) {
                $availability_cfg_input = $availability_raw_json;
            } else {
                $availability_anchor_date = '';
                foreach ( array( 'availability_cycle_anchor_date_local', 'availability_anchor_date_local', 'availability_cycle_anchor_date', 'availability_anchor_date' ) as $availability_anchor_date_key ) {
                    if ( array_key_exists( $availability_anchor_date_key, $_POST ) ) {
                        $availability_anchor_date = sanitize_text_field( wp_unslash( $_POST[ $availability_anchor_date_key ] ) );
                        break;
                    }
                }
                $availability_anchor_phase = '';
                foreach ( array( 'availability_cycle_anchor_phase', 'availability_anchor_phase' ) as $availability_anchor_phase_key ) {
                    if ( array_key_exists( $availability_anchor_phase_key, $_POST ) ) {
                        $availability_anchor_phase = sanitize_text_field( wp_unslash( $_POST[ $availability_anchor_phase_key ] ) );
                        break;
                    }
                }
                $availability_anchor_day = 0;
                foreach ( array( 'availability_cycle_anchor_day', 'availability_anchor_day', 'availability_cycle_anchor_day_within_phase', 'availability_anchor_day_within_phase' ) as $availability_anchor_day_key ) {
                    if ( array_key_exists( $availability_anchor_day_key, $_POST ) ) {
                        $availability_anchor_day = (int) wp_unslash( $_POST[ $availability_anchor_day_key ] );
                        break;
                    }
                }
                $availability_active_duration = 0;
                foreach ( array( 'availability_cycle_active_duration', 'availability_active_duration' ) as $availability_active_duration_key ) {
                    if ( array_key_exists( $availability_active_duration_key, $_POST ) ) {
                        $availability_active_duration = (int) wp_unslash( $_POST[ $availability_active_duration_key ] );
                        break;
                    }
                }
                $availability_pause_duration = 0;
                foreach ( array( 'availability_cycle_pause_duration', 'availability_pause_duration' ) as $availability_pause_duration_key ) {
                    if ( array_key_exists( $availability_pause_duration_key, $_POST ) ) {
                        $availability_pause_duration = (int) wp_unslash( $_POST[ $availability_pause_duration_key ] );
                        break;
                    }
                }
                $availability_cfg_input = array(
                    'enabled' => $availability_enabled_present ? $availability_enabled_raw : 1,
                    'anchor_date_local' => $availability_anchor_date,
                    'anchor_phase' => $availability_anchor_phase,
                    'anchor_day' => $availability_anchor_day,
                    'active_duration' => $availability_active_duration,
                    'pause_duration' => $availability_pause_duration,
                );
            }
            if ( null !== $availability_cfg_input ) {
                if ( ! class_exists( 'TCT_Interval' ) || ! is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_config' ) ) ) {
                    $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' );
                    $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Availability cycle helper is unavailable.' ) );
                    wp_safe_redirect( $redirect_to );
                    exit;
                }
                $availability_cfg = TCT_Interval::normalize_availability_cycle_config( $availability_cfg_input );
                if ( empty( $availability_cfg['enabled'] ) ) {
                    $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' );
                    $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid availability cycle. Please provide a valid anchor date, phase, day, active duration, and pause duration.' ) );
                    wp_safe_redirect( $redirect_to );
                    exit;
                }
                $availability_goal_row = array(
                    'goal_type' => $goal_type,
                    'target' => $primary_target,
                    'period_unit' => $primary_unit,
                    'period_span' => $primary_span,
                    'period_mode' => $primary_mode,
                    'intervals_json' => $intervals_json,
                );
                $availability_interval = is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ? TCT_Interval::normalize_goal_interval_from_row( $availability_goal_row ) : null;
                $availability_eligible = ( ! TCT_Utils::is_negative_goal_type( $goal_type ) && ! TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) && is_array( $availability_interval ) && ! empty( $availability_interval['target'] ) );
                if ( ! $availability_eligible ) {
                    $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' );
                    $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Availability cycles are only available for positive interval goals.' ) );
                    wp_safe_redirect( $redirect_to );
                    exit;
                }
                if ( ! empty( $data['due_schedule_json'] ) && is_callable( array( 'TCT_Interval', 'availability_cycle_due_schedule_conflict_details' ) ) ) {
                    $availability_tz = TCT_Utils::wp_timezone();
                    $availability_today_ymd = ( new DateTimeImmutable( 'now', $availability_tz ) )->format( 'Y-m-d' );
                    $availability_conflict = TCT_Interval::availability_cycle_due_schedule_conflict_details( $availability_cfg, $data['due_schedule_json'], $availability_today_ymd, $availability_tz );
                    if ( is_array( $availability_conflict ) && ! empty( $availability_conflict['checked'] ) && ! empty( $availability_conflict['has_conflict'] ) ) {
                        $availability_conflict_date = '';
                        if ( isset( $availability_conflict['first_conflict_local'] ) && is_string( $availability_conflict['first_conflict_local'] ) ) { $availability_conflict_date = trim( (string) $availability_conflict['first_conflict_local'] ); }
                        $availability_conflict_msg = 'Availability cycle conflicts with the due schedule because a future due date would land during a paused block.';
                        if ( '' !== $availability_conflict_date ) { $availability_conflict_msg = 'Availability cycle conflicts with the due schedule on ' . $availability_conflict_date . ' because that due date would land during a paused block.'; }
                        $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' );
                        $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( $availability_conflict_msg ) );
                        wp_safe_redirect( $redirect_to );
                        exit;
                    }
                }
                $availability_cycle_json = wp_json_encode( $availability_cfg );
            }
            $data['availability_cycle_json'] = $availability_cycle_json;
            $formats[] = '%s';
        }
        if ( $this->tct_goal_interval_anchor_column_exists() ) {
            $interval_anchor_json = '';
            $existing_interval_anchor_raw = '';
            if ( is_array( $existing_row ) && isset( $existing_row['interval_anchor_json'] ) && is_string( $existing_row['interval_anchor_json'] ) ) { $existing_interval_anchor_raw = trim( (string) $existing_row['interval_anchor_json'] ); }
            $interval_anchor_post_keys = array( 'interval_anchor_json', 'interval_anchor_enabled', 'interval_anchor_anchor_date_local', 'interval_anchor_anchor_date', 'interval_anchor_anchor_day', 'interval_anchor_day', 'interval_anchor_day_within_interval' );
            $interval_anchor_non_json_keys = array( 'interval_anchor_enabled', 'interval_anchor_anchor_date_local', 'interval_anchor_anchor_date', 'interval_anchor_anchor_day', 'interval_anchor_day', 'interval_anchor_day_within_interval' );
            $interval_anchor_present = false;
            foreach ( $interval_anchor_post_keys as $interval_anchor_key ) { if ( array_key_exists( $interval_anchor_key, $_POST ) ) { $interval_anchor_present = true; break; } }
            $interval_anchor_enabled_present = false;
            $interval_anchor_enabled_raw = null;
            if ( array_key_exists( 'interval_anchor_enabled', $_POST ) ) { $interval_anchor_enabled_present = true; $interval_anchor_enabled_raw = wp_unslash( $_POST['interval_anchor_enabled'] ); if ( is_array( $interval_anchor_enabled_raw ) ) { $interval_anchor_enabled_raw = end( $interval_anchor_enabled_raw ); } }
            $interval_anchor_non_json_present = false;
            foreach ( $interval_anchor_non_json_keys as $interval_anchor_non_json_key ) { if ( array_key_exists( $interval_anchor_non_json_key, $_POST ) ) { $interval_anchor_non_json_present = true; break; } }
            $interval_anchor_enabled_scalar = null;
            if ( $interval_anchor_enabled_present ) { if ( is_bool( $interval_anchor_enabled_raw ) ) { $interval_anchor_enabled_scalar = $interval_anchor_enabled_raw ? '1' : '0'; } else { $interval_anchor_enabled_scalar = strtolower( trim( (string) $interval_anchor_enabled_raw ) ); } }
            $interval_anchor_explicit_disable = ( null !== $interval_anchor_enabled_scalar && in_array( $interval_anchor_enabled_scalar, array( '', '0', 'false', 'off', 'no' ), true ) );
            $interval_anchor_raw_json = null;
            if ( array_key_exists( 'interval_anchor_json', $_POST ) ) { $interval_anchor_raw_json = wp_unslash( $_POST['interval_anchor_json'] ); if ( is_array( $interval_anchor_raw_json ) ) { $interval_anchor_raw_json = end( $interval_anchor_raw_json ); } }
            $interval_anchor_cfg_input = null;
            if ( ! $interval_anchor_present ) { if ( '' !== $existing_interval_anchor_raw ) { $interval_anchor_cfg_input = $existing_interval_anchor_raw; } } elseif ( $interval_anchor_explicit_disable ) { $interval_anchor_json = ''; } elseif ( is_string( $interval_anchor_raw_json ) && '' === trim( $interval_anchor_raw_json ) && ! $interval_anchor_non_json_present ) { $interval_anchor_json = ''; } elseif ( is_string( $interval_anchor_raw_json ) && '' !== trim( $interval_anchor_raw_json ) ) { $interval_anchor_cfg_input = $interval_anchor_raw_json; } else { $interval_anchor_anchor_date = ''; foreach ( array( 'interval_anchor_anchor_date_local', 'interval_anchor_anchor_date' ) as $interval_anchor_anchor_date_key ) { if ( array_key_exists( $interval_anchor_anchor_date_key, $_POST ) ) { $interval_anchor_anchor_date = sanitize_text_field( wp_unslash( $_POST[ $interval_anchor_anchor_date_key ] ) ); break; } } $interval_anchor_anchor_day = 0; foreach ( array( 'interval_anchor_anchor_day', 'interval_anchor_day', 'interval_anchor_day_within_interval' ) as $interval_anchor_anchor_day_key ) { if ( array_key_exists( $interval_anchor_anchor_day_key, $_POST ) ) { $interval_anchor_anchor_day = (int) wp_unslash( $_POST[ $interval_anchor_anchor_day_key ] ); break; } } $interval_anchor_cfg_input = array( 'enabled' => $interval_anchor_enabled_present ? $interval_anchor_enabled_raw : 1, 'anchor_date_local' => $interval_anchor_anchor_date, 'anchor_day' => $interval_anchor_anchor_day, ); }
            if ( null !== $interval_anchor_cfg_input ) {
                if ( ! class_exists( 'TCT_Interval' ) || ! is_callable( array( 'TCT_Interval', 'normalize_interval_anchor_config' ) ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Interval anchor helper is unavailable.' ) ); wp_safe_redirect( $redirect_to ); exit; }
                $interval_anchor_goal_row = array( 'goal_type' => $goal_type, 'target' => $primary_target, 'period_unit' => $primary_unit, 'period_span' => $primary_span, 'period_mode' => $primary_mode, 'intervals_json' => $intervals_json, );
                $interval_anchor_cfg = TCT_Interval::normalize_interval_anchor_config( $interval_anchor_cfg_input, $interval_anchor_goal_row );
                if ( empty( $interval_anchor_cfg['enabled'] ) ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Invalid interval alignment. Please provide a valid anchor date and day within the current interval.' ) ); wp_safe_redirect( $redirect_to ); exit; }
                $interval_anchor_eligible = is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_interval_anchor' ) ) ? TCT_Interval::is_goal_eligible_for_interval_anchor( $interval_anchor_goal_row ) : false;
                if ( ! $interval_anchor_eligible ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Interval alignment is only available for positive interval goals with day-or-larger intervals.' ) ); wp_safe_redirect( $redirect_to ); exit; }
                $interval_anchor_json = wp_json_encode( $interval_anchor_cfg );
            }
            $data['interval_anchor_json'] = $interval_anchor_json;
            $formats[] = '%s';
        }
        $threshold_needs_null_update = ( null === $threshold ); if ( '' !== $plant_name ) { $data['plant_name'] = $plant_name; $formats[] = '%s'; } if ( null !== $visible_after_time ) { $data['visible_after_time'] = $visible_after_time; $formats[] = '%s'; } if ( null !== $sleep_tracking_enabled ) { $data['sleep_tracking_enabled'] = (int) $sleep_tracking_enabled; $formats[] = '%d'; } if ( null !== $sleep_rollover_time ) { $data['sleep_rollover_time'] = (string) $sleep_rollover_time; $formats[] = '%s'; } if ( null !== $wake_time_enabled && $this->tct_goal_wake_time_columns_exist() ) { $data['wake_time_enabled'] = (int) $wake_time_enabled; $formats[] = '%d'; } if ( null !== $wake_time_target && $this->tct_goal_wake_time_columns_exist() ) { $data['wake_time_target'] = (string) $wake_time_target; $formats[] = '%s'; } if ( null !== $bed_time_enabled && $this->tct_goal_bed_time_columns_exist() ) { $data['bed_time_enabled'] = (int) $bed_time_enabled; $formats[] = '%d'; } if ( null !== $bed_time_target && $this->tct_goal_bed_time_columns_exist() ) { $data['bed_time_target'] = (string) $bed_time_target; $formats[] = '%s'; } $fail_button_enabled = 0;
		if ( isset( $_POST['fail_button_enabled'] ) ) {
			$raw_fbe = $_POST['fail_button_enabled'];
			if ( is_array( $raw_fbe ) ) {
				$raw_fbe = end( $raw_fbe );
			}
			$fail_button_enabled = (int) $raw_fbe;
			$fail_button_enabled = ( 1 === $fail_button_enabled ) ? 1 : 0;
		} if ( 1 === (int) $wake_time_enabled || 1 === (int) $bed_time_enabled ) { $fail_button_enabled = 1; } if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) && TCT_Utils::is_anki_cards_goal_type( $goal_type ) ) { $fail_button_enabled = 0; } if ( $is_composite_goal ) { $fail_button_enabled = 0; } $data['fail_button_enabled'] = (int) $fail_button_enabled; $formats[] = '%d'; $is_favorite = isset( $_POST['is_favorite'] ) ? 1 : 0; $data['is_favorite'] = (int) $is_favorite; $formats[] = '%d'; if ( '' !== $label_name ) { $data['label_name'] = $label_name; $formats[] = '%s'; } if ( 0 === $goal_id ) { $data['created_at'] = $now; $formats[] = '%s'; } $tct_composite_tx_started = false; if ( $is_composite_goal ) { $tct_composite_tx_started = ( false !== $wpdb->query( 'START TRANSACTION' ) ); } if ( $goal_id > 0 ) { $result = $wpdb->update( $table, $data, array( 'id' => (int) $goal_id, 'user_id' => (int) $user_id, ), $formats, array( '%d', '%d' ) ); if ( false === $result ) { if ( $tct_composite_tx_started ) { $wpdb->query( 'ROLLBACK' ); } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while saving goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } } else { $result = $wpdb->insert( $table, $data, $formats ); if ( false === $result ) { if ( $tct_composite_tx_started ) { $wpdb->query( 'ROLLBACK' ); } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while saving goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } $goal_id = (int) $wpdb->insert_id; } if ( $is_composite_goal && ! empty( $composite_upsert['should_sync'] ) ) { $composite_sync = $this->tct_sync_composite_parent_children( $goal_id, $user_id, isset( $composite_upsert['child_ids'] ) ? $composite_upsert['child_ids'] : array(), $now ); if ( is_wp_error( $composite_sync ) ) { if ( $tct_composite_tx_started ) { $wpdb->query( 'ROLLBACK' ); } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( $composite_sync->get_error_message() ) ); wp_safe_redirect( $redirect_to ); exit; } } if ( $tct_composite_tx_started ) { $wpdb->query( 'COMMIT' ); } if ( 'manual' === $tracking_mode && $goal_id > 0 ) { $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET label_name = NULL WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); } if ( '' === $plant_name && $goal_id > 0 ) { $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET plant_name = NULL WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); } if ( null === $visible_after_time && $goal_id > 0 ) { $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET visible_after_time = NULL WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); } if ( $threshold_needs_null_update && $goal_id > 0 ) { $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET threshold = NULL WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); } if ( $goal_id > 0 && '' !== $label_name ) { $completions_table = TCT_DB::table_completions(); $wpdb->query( $wpdb->prepare( "UPDATE {$completions_table}
                     SET goal_id = %d,
                         source_ref = CASE
                            WHEN todoist_completed_id IS NOT NULL AND todoist_completed_id <> '' THEN CONCAT(todoist_completed_id, ':', %d)
                            ELSE source_ref
                         END
                     WHERE user_id = %d
                       AND (goal_id IS NULL OR goal_id = 0)
                       AND label_name = %s
                       AND (source = 'todoist' OR source = '' OR source IS NULL)", $goal_id, $goal_id, $user_id, $label_name ) ); } if ( isset( $tct_reward_rebase ) && is_array( $tct_reward_rebase ) && ! empty( $tct_reward_rebase['should_attempt'] ) && class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $hash_before = isset( $tct_reward_rebase['hash_before'] ) ? (string) $tct_reward_rebase['hash_before'] : ''; if ( method_exists( 'TCT_Reward', 'invalidate_reward_caches' ) ) { TCT_Reward::invalidate_reward_caches( $user_id ); } $hash_after = TCT_Reward::compute_points_economy_hash( $user_id ); if ( '' !== $hash_before && '' !== $hash_after && $hash_after !== $hash_before ) { $reward_now = TCT_Reward::get_active_reward( $user_id ); if ( class_exists( 'TCT_Economy_Normalizer' ) && method_exists( 'TCT_Economy_Normalizer', 'record_snapshot_on_economy_change' ) ) { TCT_Economy_Normalizer::record_snapshot_on_economy_change( $user_id ); } $already = false; if ( is_array( $reward_now ) && isset( $reward_now['economy_hash'] ) && is_string( $reward_now['economy_hash'] ) ) { if ( trim( (string) $reward_now['economy_hash'] ) === $hash_after ) { $already = true; } } if ( ! $already ) { if ( class_exists( 'TCT_Admin' ) && method_exists( 'TCT_Admin', 'get_sync_horizon_days' ) && class_exists( 'TCT_Ledger' ) && class_exists( 'TCT_Utils' ) ) { $horizon_days = (int) TCT_Admin::get_sync_horizon_days(); if ( $horizon_days < 1 ) { $horizon_days = 1; } if ( $horizon_days > 3650 ) { $horizon_days = 3650; } $now_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ); $since_utc = $now_utc->sub( new DateInterval( 'P' . (int) $horizon_days . 'D' ) ); $since_mysql = TCT_Utils::dt_to_mysql_utc( $since_utc ); $until_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc ); TCT_Ledger::reconcile_user( $user_id, $since_mysql, $until_mysql ); } $ratio_before = isset( $tct_reward_rebase['ratio_before'] ) ? $tct_reward_rebase['ratio_before'] : null; if ( null !== $ratio_before && method_exists( 'TCT_Reward', 'apply_rebase_adjustment' ) ) { $result = TCT_Reward::apply_rebase_adjustment( $user_id, (float) $ratio_before, (int) $tct_reward_rebase['target_before'], (float) $tct_reward_rebase['pct_before'], (string) $hash_before, (string) $hash_after, (string) $now, 'goal_upsert#' . (string) $goal_id ); if ( ! is_array( $result ) || empty( $result['ok'] ) ) { $msg = is_array( $result ) && isset( $result['error'] ) ? (string) $result['error'] : 'Reward rebase failed.'; $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); } } if ( is_array( $reward_now ) && ! empty( $reward_now ) ) { $reward_now['economy_hash'] = $hash_after; TCT_Reward::set_active_reward( $user_id, $reward_now ); } } } else { if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } } } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_goal_delete() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_goal_delete' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $tct_reward_rebase = array( 'should_attempt' => false, 'hash_before' => '', 'ratio_before' => null, 'target_before' => 0, 'pct_before' => 0.0, ); if ( class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'is_enabled' ) && TCT_Reward::is_enabled() ) { $active_reward = TCT_Reward::get_active_reward( $user_id ); if ( is_array( $active_reward ) && ! empty( $active_reward ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $tct_reward_rebase['should_attempt'] = true; $tct_reward_rebase['hash_before'] = TCT_Reward::compute_points_economy_hash( $user_id ); if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } $before = TCT_Reward::compute_reward_progress( $user_id, $active_reward ); if ( is_array( $before ) ) { $errs = ( isset( $before['errors'] ) && is_array( $before['errors'] ) ) ? $before['errors'] : array(); $told = isset( $before['target_points'] ) ? (int) $before['target_points'] : 0; $eold = isset( $before['earned_points'] ) ? (int) $before['earned_points'] : 0; $pold = isset( $before['progress_pct'] ) ? (float) $before['progress_pct'] : 0.0; if ( $told > 0 && empty( $errs ) ) { $tct_reward_rebase['ratio_before'] = (float) $eold / (float) $told; $tct_reward_rebase['target_before'] = (int) $told; $tct_reward_rebase['pct_before'] = (float) $pold; } } } } $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Missing goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $goals_table = TCT_DB::table_goals(); $completions_table = TCT_DB::table_completions(); $exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$goals_table} WHERE id = %d AND user_id = %d", $goal_id, $user_id ) ); if ( $exists <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Goal not found.' ) ); wp_safe_redirect( $redirect_to ); exit; } $wpdb->query( 'START TRANSACTION' ); if ( method_exists( 'TCT_DB', 'table_sleep_cycles' ) ) { $sleep_table = TCT_DB::table_sleep_cycles(); $pattern = $wpdb->esc_like( $sleep_table ); $exists_sleep = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( $exists_sleep ) { $deleted_sleep = $wpdb->delete( $sleep_table, array( 'user_id' => (int) $user_id, 'goal_id' => (int) $goal_id, ), array( '%d', '%d' ) ); if ( false === $deleted_sleep ) { $wpdb->query( 'ROLLBACK' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while deleting sleep history.' ) ); wp_safe_redirect( $redirect_to ); exit; } } } $deleted_completions = $wpdb->delete( $completions_table, array( 'user_id' => (int) $user_id, 'goal_id' => (int) $goal_id, ), array( '%d', '%d' ) ); if ( false === $deleted_completions ) { $wpdb->query( 'ROLLBACK' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while deleting goal history.' ) ); wp_safe_redirect( $redirect_to ); exit; } $deleted_goal = $wpdb->delete( $goals_table, array( 'id' => (int) $goal_id, 'user_id' => (int) $user_id, ), array( '%d', '%d' ) ); if ( false === $deleted_goal || (int) $deleted_goal <= 0 ) { $wpdb->query( 'ROLLBACK' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while deleting goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } $wpdb->query( 'COMMIT' ); if ( isset( $tct_reward_rebase ) && is_array( $tct_reward_rebase ) && ! empty( $tct_reward_rebase['should_attempt'] ) && class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $hash_before = isset( $tct_reward_rebase['hash_before'] ) ? (string) $tct_reward_rebase['hash_before'] : ''; if ( method_exists( 'TCT_Reward', 'invalidate_reward_caches' ) ) { TCT_Reward::invalidate_reward_caches( $user_id ); } $hash_after = TCT_Reward::compute_points_economy_hash( $user_id ); if ( '' !== $hash_before && '' !== $hash_after && $hash_after !== $hash_before ) { $reward_now = TCT_Reward::get_active_reward( $user_id ); if ( class_exists( 'TCT_Economy_Normalizer' ) && method_exists( 'TCT_Economy_Normalizer', 'record_snapshot_on_economy_change' ) ) { TCT_Economy_Normalizer::record_snapshot_on_economy_change( $user_id ); } $already = false; if ( is_array( $reward_now ) && isset( $reward_now['economy_hash'] ) && is_string( $reward_now['economy_hash'] ) ) { if ( trim( (string) $reward_now['economy_hash'] ) === $hash_after ) { $already = true; } } if ( ! $already ) { $now = current_time( 'mysql', true ); if ( class_exists( 'TCT_Admin' ) && method_exists( 'TCT_Admin', 'get_sync_horizon_days' ) && class_exists( 'TCT_Ledger' ) && class_exists( 'TCT_Utils' ) ) { $horizon_days = (int) TCT_Admin::get_sync_horizon_days(); if ( $horizon_days < 1 ) { $horizon_days = 1; } if ( $horizon_days > 3650 ) { $horizon_days = 3650; } $now_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ); $since_utc = $now_utc->sub( new DateInterval( 'P' . (int) $horizon_days . 'D' ) ); $since_mysql = TCT_Utils::dt_to_mysql_utc( $since_utc ); $until_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc ); TCT_Ledger::reconcile_user( $user_id, $since_mysql, $until_mysql ); } $ratio_before = isset( $tct_reward_rebase['ratio_before'] ) ? $tct_reward_rebase['ratio_before'] : null; if ( null !== $ratio_before && method_exists( 'TCT_Reward', 'apply_rebase_adjustment' ) ) { $result = TCT_Reward::apply_rebase_adjustment( $user_id, (float) $ratio_before, (int) $tct_reward_rebase['target_before'], (float) $tct_reward_rebase['pct_before'], (string) $hash_before, (string) $hash_after, (string) $now, 'goal_delete#' . (string) $goal_id ); if ( ! is_array( $result ) || empty( $result['ok'] ) ) { $msg = is_array( $result ) && isset( $result['error'] ) ? (string) $result['error'] : 'Reward rebase failed.'; $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); } } if ( is_array( $reward_now ) && ! empty( $reward_now ) ) { $reward_now['economy_hash'] = $hash_after; TCT_Reward::set_active_reward( $user_id, $reward_now ); } } } else { if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } } } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_goal_archive() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_goal_archive' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $tct_reward_rebase = array( 'should_attempt' => false, 'hash_before' => '', 'ratio_before' => null, 'target_before' => 0, 'pct_before' => 0.0, ); if ( class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'is_enabled' ) && TCT_Reward::is_enabled() ) { $active_reward = TCT_Reward::get_active_reward( $user_id ); if ( is_array( $active_reward ) && ! empty( $active_reward ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $tct_reward_rebase['should_attempt'] = true; $tct_reward_rebase['hash_before'] = TCT_Reward::compute_points_economy_hash( $user_id ); if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } $before = TCT_Reward::compute_reward_progress( $user_id, $active_reward ); if ( is_array( $before ) ) { $errs = ( isset( $before['errors'] ) && is_array( $before['errors'] ) ) ? $before['errors'] : array(); $told = isset( $before['target_points'] ) ? (int) $before['target_points'] : 0; $eold = isset( $before['earned_points'] ) ? (int) $before['earned_points'] : 0; $pold = isset( $before['progress_pct'] ) ? (float) $before['progress_pct'] : 0.0; if ( $told > 0 && empty( $errs ) ) { $tct_reward_rebase['ratio_before'] = (float) $eold / (float) $told; $tct_reward_rebase['target_before'] = (int) $told; $tct_reward_rebase['pct_before'] = (float) $pold; } } } } $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Missing goal id.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $goals_table = TCT_DB::table_goals(); $now = current_time( 'mysql', true ); $exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$goals_table} WHERE id = %d AND user_id = %d", $goal_id, $user_id ) ); if ( (int) $exists <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Goal not found.' ) ); wp_safe_redirect( $redirect_to ); exit; } $updated = $wpdb->update( $goals_table, array( 'is_tracked' => 0, 'updated_at' => $now, ), array( 'id' => (int) $goal_id, 'user_id' => (int) $user_id, ), array( '%d', '%s' ), array( '%d', '%d' ) ); if ( false === $updated ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while archiving goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( isset( $tct_reward_rebase ) && is_array( $tct_reward_rebase ) && ! empty( $tct_reward_rebase['should_attempt'] ) && class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $hash_before = isset( $tct_reward_rebase['hash_before'] ) ? (string) $tct_reward_rebase['hash_before'] : ''; if ( method_exists( 'TCT_Reward', 'invalidate_reward_caches' ) ) { TCT_Reward::invalidate_reward_caches( $user_id ); } $hash_after = TCT_Reward::compute_points_economy_hash( $user_id ); if ( '' !== $hash_before && '' !== $hash_after && $hash_after !== $hash_before ) { $reward_now = TCT_Reward::get_active_reward( $user_id ); if ( class_exists( 'TCT_Economy_Normalizer' ) && method_exists( 'TCT_Economy_Normalizer', 'record_snapshot_on_economy_change' ) ) { TCT_Economy_Normalizer::record_snapshot_on_economy_change( $user_id ); } $already = false; if ( is_array( $reward_now ) && isset( $reward_now['economy_hash'] ) && is_string( $reward_now['economy_hash'] ) ) { if ( trim( (string) $reward_now['economy_hash'] ) === $hash_after ) { $already = true; } } if ( ! $already ) { if ( class_exists( 'TCT_Admin' ) && method_exists( 'TCT_Admin', 'get_sync_horizon_days' ) && class_exists( 'TCT_Ledger' ) && class_exists( 'TCT_Utils' ) ) { $horizon_days = (int) TCT_Admin::get_sync_horizon_days(); if ( $horizon_days < 1 ) { $horizon_days = 1; } if ( $horizon_days > 3650 ) { $horizon_days = 3650; } $now_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ); $since_utc = $now_utc->sub( new DateInterval( 'P' . (int) $horizon_days . 'D' ) ); $since_mysql = TCT_Utils::dt_to_mysql_utc( $since_utc ); $until_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc ); TCT_Ledger::reconcile_user( $user_id, $since_mysql, $until_mysql ); } $ratio_before = isset( $tct_reward_rebase['ratio_before'] ) ? $tct_reward_rebase['ratio_before'] : null; if ( null !== $ratio_before && method_exists( 'TCT_Reward', 'apply_rebase_adjustment' ) ) { $result = TCT_Reward::apply_rebase_adjustment( $user_id, (float) $ratio_before, (int) $tct_reward_rebase['target_before'], (float) $tct_reward_rebase['pct_before'], (string) $hash_before, (string) $hash_after, (string) $now, 'goal_archive#' . (string) $goal_id ); if ( ! is_array( $result ) || empty( $result['ok'] ) ) { $msg = is_array( $result ) && isset( $result['error'] ) ? (string) $result['error'] : 'Reward rebase failed.'; $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); } } if ( is_array( $reward_now ) && ! empty( $reward_now ) ) { $reward_now['economy_hash'] = $hash_after; TCT_Reward::set_active_reward( $user_id, $reward_now ); } } } else { if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } } } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_goal_restore() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_goal_restore' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $tct_reward_rebase = array( 'should_attempt' => false, 'hash_before' => '', 'ratio_before' => null, 'target_before' => 0, 'pct_before' => 0.0, ); if ( class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'is_enabled' ) && TCT_Reward::is_enabled() ) { $active_reward = TCT_Reward::get_active_reward( $user_id ); if ( is_array( $active_reward ) && ! empty( $active_reward ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $tct_reward_rebase['should_attempt'] = true; $tct_reward_rebase['hash_before'] = TCT_Reward::compute_points_economy_hash( $user_id ); if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } $before = TCT_Reward::compute_reward_progress( $user_id, $active_reward ); if ( is_array( $before ) ) { $errs = ( isset( $before['errors'] ) && is_array( $before['errors'] ) ) ? $before['errors'] : array(); $told = isset( $before['target_points'] ) ? (int) $before['target_points'] : 0; $eold = isset( $before['earned_points'] ) ? (int) $before['earned_points'] : 0; $pold = isset( $before['progress_pct'] ) ? (float) $before['progress_pct'] : 0.0; if ( $told > 0 && empty( $errs ) ) { $tct_reward_rebase['ratio_before'] = (float) $eold / (float) $told; $tct_reward_rebase['target_before'] = (int) $told; $tct_reward_rebase['pct_before'] = (float) $pold; } } } } $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Missing goal id.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $goals_table = TCT_DB::table_goals(); $now = current_time( 'mysql', true ); $exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$goals_table} WHERE id = %d AND user_id = %d", $goal_id, $user_id ) ); if ( (int) $exists <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Goal not found.' ) ); wp_safe_redirect( $redirect_to ); exit; } $updated = $wpdb->update( $goals_table, array( 'is_tracked' => 1, 'updated_at' => $now, ), array( 'id' => (int) $goal_id, 'user_id' => (int) $user_id, ), array( '%d', '%s' ), array( '%d', '%d' ) ); if ( false === $updated ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals_msg', rawurlencode( 'Database error while restoring goal.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( isset( $tct_reward_rebase ) && is_array( $tct_reward_rebase ) && ! empty( $tct_reward_rebase['should_attempt'] ) && class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'compute_points_economy_hash' ) ) { $hash_before = isset( $tct_reward_rebase['hash_before'] ) ? (string) $tct_reward_rebase['hash_before'] : ''; if ( method_exists( 'TCT_Reward', 'invalidate_reward_caches' ) ) { TCT_Reward::invalidate_reward_caches( $user_id ); } $hash_after = TCT_Reward::compute_points_economy_hash( $user_id ); if ( '' !== $hash_before && '' !== $hash_after && $hash_after !== $hash_before ) { $reward_now = TCT_Reward::get_active_reward( $user_id ); if ( class_exists( 'TCT_Economy_Normalizer' ) && method_exists( 'TCT_Economy_Normalizer', 'record_snapshot_on_economy_change' ) ) { TCT_Economy_Normalizer::record_snapshot_on_economy_change( $user_id ); } $already = false; if ( is_array( $reward_now ) && isset( $reward_now['economy_hash'] ) && is_string( $reward_now['economy_hash'] ) ) { if ( trim( (string) $reward_now['economy_hash'] ) === $hash_after ) { $already = true; } } if ( ! $already ) { if ( class_exists( 'TCT_Admin' ) && method_exists( 'TCT_Admin', 'get_sync_horizon_days' ) && class_exists( 'TCT_Ledger' ) && class_exists( 'TCT_Utils' ) ) { $horizon_days = (int) TCT_Admin::get_sync_horizon_days(); if ( $horizon_days < 1 ) { $horizon_days = 1; } if ( $horizon_days > 3650 ) { $horizon_days = 3650; } $now_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ); $since_utc = $now_utc->sub( new DateInterval( 'P' . (int) $horizon_days . 'D' ) ); $since_mysql = TCT_Utils::dt_to_mysql_utc( $since_utc ); $until_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc ); TCT_Ledger::reconcile_user( $user_id, $since_mysql, $until_mysql ); } $ratio_before = isset( $tct_reward_rebase['ratio_before'] ) ? $tct_reward_rebase['ratio_before'] : null; if ( null !== $ratio_before && method_exists( 'TCT_Reward', 'apply_rebase_adjustment' ) ) { $result = TCT_Reward::apply_rebase_adjustment( $user_id, (float) $ratio_before, (int) $tct_reward_rebase['target_before'], (float) $tct_reward_rebase['pct_before'], (string) $hash_before, (string) $hash_after, (string) $now, 'goal_restore#' . (string) $goal_id ); if ( ! is_array( $result ) || empty( $result['ok'] ) ) { $msg = is_array( $result ) && isset( $result['error'] ) ? (string) $result['error'] : 'Reward rebase failed.'; $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_reward_msg', rawurlencode( $msg ) ); } } if ( is_array( $reward_now ) && ! empty( $reward_now ) ) { $reward_now['economy_hash'] = $hash_after; TCT_Reward::set_active_reward( $user_id, $reward_now ); } } } else { if ( method_exists( 'TCT_Reward', 'ensure_active_reward_economy_hash' ) ) { TCT_Reward::ensure_active_reward_economy_hash( $user_id ); } } } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_goals', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_domain_upsert() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_domain_upsert' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; $domain_name = isset( $_POST['domain_name'] ) ? sanitize_text_field( wp_unslash( $_POST['domain_name'] ) ) : ''; $domain_color_raw = isset( $_POST['domain_color'] ) ? sanitize_text_field( wp_unslash( $_POST['domain_color'] ) ) : ''; $domain_color = sanitize_hex_color( $domain_color_raw ); if ( ! $domain_color ) { $domain_color = ''; } if ( '' === $domain_name ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains_msg', rawurlencode( 'Missing domain name.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $table = TCT_DB::table_domains(); $now = current_time( 'mysql', true ); if ( $domain_id > 0 ) { $update_data = array( 'domain_name' => $domain_name, 'updated_at' => $now, ); $update_formats = array( '%s', '%s' ); if ( '' !== $domain_color ) { $update_data['color_hex'] = $domain_color; $update_formats[] = '%s'; } $updated = $wpdb->update( $table, $update_data, array( 'id' => (int) $domain_id, 'user_id' => (int) $user_id, ), $update_formats, array( '%d', '%d' ) ); if ( false === $updated ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains_msg', rawurlencode( 'Database error while updating domain.' ) ); wp_safe_redirect( $redirect_to ); exit; } } else { if ( '' === $domain_color ) { $domain_color = $this->suggest_domain_color_from_domains( $this->get_domains( $user_id ) ); } $max_sort = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(sort_order) FROM {$table} WHERE user_id = %d", $user_id ) ); $sort_order = $max_sort + 1; $inserted = $wpdb->insert( $table, array( 'user_id' => (int) $user_id, 'domain_name' => $domain_name, 'color_hex' => $domain_color, 'sort_order' => (int) $sort_order, 'created_at' => $now, 'updated_at' => $now, ), array( '%d', '%s', '%s', '%d', '%s', '%s' ) ); if ( false === $inserted ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains_msg', rawurlencode( 'Database error while creating domain (name may already exist).' ) ); wp_safe_redirect( $redirect_to ); exit; } } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_domain_delete() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_domain_delete' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains_msg', rawurlencode( 'Missing domain.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $domains_table = TCT_DB::table_domains(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $wpdb->update( $goals_table, array( 'domain_id' => 0, 'role_id' => 0, ), array( 'user_id' => (int) $user_id, 'domain_id' => (int) $domain_id, ), array( '%d', '%d' ), array( '%d', '%d' ) ); $wpdb->delete( $roles_table, array( 'user_id' => (int) $user_id, 'domain_id' => (int) $domain_id, ), array( '%d', '%d' ) ); $deleted = $wpdb->delete( $domains_table, array( 'id' => (int) $domain_id, 'user_id' => (int) $user_id, ), array( '%d', '%d' ) ); if ( false === $deleted ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains_msg', rawurlencode( 'Database error while deleting domain.' ) ); wp_safe_redirect( $redirect_to ); exit; } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_domains', 'deleted' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_role_upsert() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_role_upsert' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $role_id = isset( $_POST['role_id'] ) ? (int) wp_unslash( $_POST['role_id'] ) : 0; $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; $role_name = isset( $_POST['role_name'] ) ? sanitize_text_field( wp_unslash( $_POST['role_name'] ) ) : ''; if ( '' === $role_name ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Missing role name.' ) ); wp_safe_redirect( $redirect_to ); exit; } if ( $domain_id <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Please select a domain for this role.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $domains_table = TCT_DB::table_domains(); $exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$domains_table} WHERE id = %d AND user_id = %d", $domain_id, $user_id ) ); if ( $exists <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Invalid domain.' ) ); wp_safe_redirect( $redirect_to ); exit; } $table = TCT_DB::table_roles(); $now = current_time( 'mysql', true ); if ( $role_id > 0 ) { $updated = $wpdb->update( $table, array( 'role_name' => $role_name, 'domain_id' => (int) $domain_id, 'updated_at' => $now, ), array( 'id' => (int) $role_id, 'user_id' => (int) $user_id, ), array( '%s', '%d', '%s' ), array( '%d', '%d' ) ); if ( false === $updated ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Database error while updating role.' ) ); wp_safe_redirect( $redirect_to ); exit; } } else { $max_sort = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(sort_order) FROM {$table} WHERE user_id = %d AND domain_id = %d", $user_id, $domain_id ) ); $sort_order = $max_sort + 1; $inserted = $wpdb->insert( $table, array( 'user_id' => (int) $user_id, 'domain_id' => (int) $domain_id, 'role_name' => $role_name, 'sort_order' => (int) $sort_order, 'created_at' => $now, 'updated_at' => $now, ), array( '%d', '%d', '%s', '%d', '%s', '%s' ) ); if ( false === $inserted ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Database error while creating role. (It may already exist.)' ) ); wp_safe_redirect( $redirect_to ); exit; } } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'success' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_role_delete() { if ( ! is_user_logged_in() ) { wp_safe_redirect( wp_login_url() ); exit; } check_admin_referer( 'tct_role_delete' ); $redirect_to = $this->get_redirect_target_from_post(); $user_id = get_current_user_id(); $role_id = isset( $_POST['role_id'] ) ? (int) wp_unslash( $_POST['role_id'] ) : 0; if ( $role_id <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Missing role.' ) ); wp_safe_redirect( $redirect_to ); exit; } global $wpdb; $roles_table = TCT_DB::table_roles(); $goals_table = TCT_DB::table_goals(); $exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$roles_table} WHERE id = %d AND user_id = %d", $role_id, $user_id ) ); if ( $exists <= 0 ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Invalid role.' ) ); wp_safe_redirect( $redirect_to ); exit; } $wpdb->update( $goals_table, array( 'role_id' => 0 ), array( 'user_id' => (int) $user_id, 'role_id' => (int) $role_id, ), array( '%d' ), array( '%d', '%d' ) ); $deleted = $wpdb->delete( $roles_table, array( 'id' => (int) $role_id, 'user_id' => (int) $user_id, ), array( '%d', '%d' ) ); if ( false === $deleted ) { $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'error' ); $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles_msg', rawurlencode( 'Database error while deleting role.' ) ); wp_safe_redirect( $redirect_to ); exit; } $redirect_to = TCT_Utils::add_query_arg_safe( $redirect_to, 'tct_roles', 'deleted' ); wp_safe_redirect( $redirect_to ); exit; } public function handle_role_order_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_role_order', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; $order_raw = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : ''; $decoded = json_decode( $order_raw, true ); if ( ! is_array( $decoded ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid order payload.', ), 400 ); } $role_ids = array(); foreach ( $decoded as $id ) { $rid = (int) $id; if ( $rid > 0 ) { $role_ids[] = $rid; } } $role_ids = array_values( array_unique( $role_ids ) ); if ( empty( $role_ids ) ) { TCT_Utils::send_json_success( array( 'ok' => true ) ); } global $wpdb; $roles_table = TCT_DB::table_roles(); $placeholders = implode( ',', array_fill( 0, count( $role_ids ), '%d' ) ); $sql = "SELECT id FROM {$roles_table} WHERE user_id = %d AND domain_id = %d AND id IN ({$placeholders})"; $prepared = $wpdb->prepare( $sql, array_merge( array( $user_id, $domain_id ), $role_ids ) ); $found = $wpdb->get_col( $prepared ); if ( ! is_array( $found ) || count( $found ) !== count( $role_ids ) ) { TCT_Utils::send_json_error( array( 'message' => 'One or more roles were not found for this domain.', ), 403 ); } $order = 1; foreach ( $role_ids as $rid ) { $wpdb->update( $roles_table, array( 'sort_order' => (int) $order, ), array( 'id' => (int) $rid, 'user_id' => (int) $user_id, ), array( '%d' ), array( '%d', '%d' ) ); $order++; } TCT_Utils::send_json_success( array( 'ok' => true ) ); } public function handle_goal_order_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_goal_order', 'nonce' ); $user_id = get_current_user_id(); $type_raw = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : ''; $type = $this->tct_sanitize_goal_order_type( $type_raw ); if ( '' === $type ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid goal order type.', ), 400 ); } $meta_key = $this->tct_goal_order_meta_key( $type ); if ( '' === $meta_key ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid goal order type.', ), 400 ); } $order_raw = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : ''; $goal_ids = $this->tct_normalize_goal_order_ids( $order_raw ); if ( empty( $goal_ids ) ) { delete_user_meta( $user_id, $meta_key ); TCT_Utils::send_json_success( array( 'ok' => true, 'saved' => true, 'count' => 0, ) ); } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Database not available.', ), 500 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $placeholders = implode( ',', array_fill( 0, count( $goal_ids ), '%d' ) ); $sql = "SELECT id, is_favorite, goal_type, target, period_unit, period_span FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 AND id IN ({$placeholders})"; $prepared = $wpdb->prepare( $sql, array_merge( array( $user_id ), $goal_ids ) ); $rows = $wpdb->get_results( $prepared, ARRAY_A ); $valid = array(); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { $gid = isset( $r['id'] ) ? (int) $r['id'] : 0; if ( $gid <= 0 ) { continue; } if ( 'daily' === $type ) { $unit = isset( $r['period_unit'] ) ? strtolower( trim( (string) $r['period_unit'] ) ) : ''; $span = isset( $r['period_span'] ) ? max( 1, (int) $r['period_span'] ) : 1; $target = isset( $r['target'] ) ? (int) $r['target'] : 0; $goal_type = isset( $r['goal_type'] ) && is_string( $r['goal_type'] ) ? (string) $r['goal_type'] : 'positive'; if ( 'day' !== $unit || 1 !== (int) $span ) { continue; } if ( $target <= 0 ) { continue; } if ( class_exists( 'TCT_Utils' ) ) { if ( method_exists( 'TCT_Utils', 'is_negative_goal_type' ) && TCT_Utils::is_negative_goal_type( $goal_type ) ) { continue; } if ( method_exists( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) && TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) { continue; } } $valid[ $gid ] = true; } else { $is_fav = isset( $r['is_favorite'] ) ? (int) $r['is_favorite'] : 0; if ( 1 !== $is_fav ) { continue; } $valid[ $gid ] = true; } } } $filtered = array(); foreach ( $goal_ids as $gid ) { $gid = (int) $gid; if ( $gid > 0 && isset( $valid[ $gid ] ) ) { $filtered[] = $gid; } } $filtered = array_values( array_unique( $filtered ) ); update_user_meta( $user_id, $meta_key, $filtered ); TCT_Utils::send_json_success( array( 'ok' => true, 'saved' => true, 'count' => count( $filtered ), ) ); } private function tct_sanitize_goal_order_type( $type ) { $type = is_string( $type ) ? strtolower( trim( (string) $type ) ) : ''; if ( 'daily' === $type ) { return 'daily'; } if ( 'favorite' === $type || 'favourite' === $type || 'favorites' === $type || 'favourites' === $type ) { return 'favorites'; } return ''; } private function tct_goal_order_meta_key( $type ) { $type = $this->tct_sanitize_goal_order_type( $type ); if ( 'daily' === $type ) { return 'tct_daily_goal_order'; } if ( 'favorites' === $type ) { return 'tct_favorite_goal_order'; } return ''; } private function tct_normalize_goal_order_ids( $raw ) { $decoded = null; if ( is_string( $raw ) ) { $raw = trim( (string) $raw ); if ( '' !== $raw ) { $tmp = json_decode( $raw, true ); if ( is_array( $tmp ) ) { $decoded = $tmp; } } } if ( is_array( $raw ) ) { $decoded = $raw; } if ( ! is_array( $decoded ) ) { return array(); } $out = array(); $seen = array(); foreach ( $decoded as $v ) { $id = (int) $v; if ( $id > 0 && ! isset( $seen[ $id ] ) ) { $out[] = $id; $seen[ $id ] = true; } } return $out; } private function tct_get_goal_order_ids( $user_id, $type ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return array(); } $meta_key = $this->tct_goal_order_meta_key( $type ); if ( '' === $meta_key ) { return array(); } $raw = get_user_meta( $user_id, $meta_key, true ); return $this->tct_normalize_goal_order_ids( $raw ); } private function tct_get_goal_order_map( $user_id, $type ) { $ids = $this->tct_get_goal_order_ids( $user_id, $type ); $map = array(); $pos = 0; foreach ( $ids as $gid ) { $gid = (int) $gid; if ( $gid > 0 && ! isset( $map[ $gid ] ) ) { $map[ $gid ] = (int) $pos; $pos++; } } return $map; } private function render_goal_order_settings_sections( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { return ''; } global $wpdb; $goals_table = TCT_DB::table_goals(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, goal_name, label_name, goal_type, target, period_unit, period_span, is_favorite FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 ORDER BY goal_name ASC", $user_id ), ARRAY_A ); if ( ! is_array( $rows ) ) { $rows = array(); } $daily = array(); $favorites = array(); foreach ( $rows as $r ) { $gid = isset( $r['id'] ) ? (int) $r['id'] : 0; if ( $gid <= 0 ) { continue; } $goal_name = isset( $r['goal_name'] ) ? trim( (string) $r['goal_name'] ) : ''; $label_name = isset( $r['label_name'] ) ? trim( (string) $r['label_name'] ) : ''; $display = ( '' !== $goal_name ) ? $goal_name : $label_name; if ( '' === $display ) { $display = 'Goal #' . (string) $gid; } $r['_display_name'] = $display; $goal_type = isset( $r['goal_type'] ) && is_string( $r['goal_type'] ) ? (string) $r['goal_type'] : 'positive'; $target = isset( $r['target'] ) ? (int) $r['target'] : 0; $unit = isset( $r['period_unit'] ) ? strtolower( trim( (string) $r['period_unit'] ) ) : ''; $span = isset( $r['period_span'] ) ? max( 1, (int) $r['period_span'] ) : 1; $is_fav = isset( $r['is_favorite'] ) ? (int) $r['is_favorite'] : 0; if ( 1 === $is_fav ) { $favorites[] = $r; } $is_daily = ( 'day' === $unit && 1 === (int) $span && $target > 0 ); if ( $is_daily ) { $is_neg = class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_negative_goal_type' ) ? TCT_Utils::is_negative_goal_type( $goal_type ) : false; $is_no_int = class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ? TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) : false; if ( ! $is_neg && ! $is_no_int ) { $daily[] = $r; } } } $daily_order = $this->tct_get_goal_order_map( $user_id, 'daily' ); usort( $daily, function( $a, $b ) use ( $daily_order ) { $aid = isset( $a['id'] ) ? (int) $a['id'] : 0; $bid = isset( $b['id'] ) ? (int) $b['id'] : 0; $ap = isset( $daily_order[ $aid ] ) ? (int) $daily_order[ $aid ] : PHP_INT_MAX; $bp = isset( $daily_order[ $bid ] ) ? (int) $daily_order[ $bid ] : PHP_INT_MAX; if ( $ap !== $bp ) { return $ap <=> $bp; } $an = isset( $a['_display_name'] ) ? (string) $a['_display_name'] : ''; $bn = isset( $b['_display_name'] ) ? (string) $b['_display_name'] : ''; return strcasecmp( $an, $bn ); } ); $fav_order = $this->tct_get_goal_order_map( $user_id, 'favorites' ); usort( $favorites, function( $a, $b ) use ( $fav_order ) { $aid = isset( $a['id'] ) ? (int) $a['id'] : 0; $bid = isset( $b['id'] ) ? (int) $b['id'] : 0; $ap = isset( $fav_order[ $aid ] ) ? (int) $fav_order[ $aid ] : PHP_INT_MAX; $bp = isset( $fav_order[ $bid ] ) ? (int) $fav_order[ $bid ] : PHP_INT_MAX; if ( $ap !== $bp ) { return $ap <=> $bp; } $an = isset( $a['_display_name'] ) ? (string) $a['_display_name'] : ''; $bn = isset( $b['_display_name'] ) ? (string) $b['_display_name'] : ''; return strcasecmp( $an, $bn ); } ); $html = ''; $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Daily goals order</h4>'; $html .= '<p class="tct-muted">Drag the handle to reorder. This controls the order shown under <strong>Daily</strong> on the Dashboard and in the Daily list on mobile.</p>'; if ( empty( $daily ) ) { $html .= '<p class="tct-muted">No daily goals found (day interval, 1 per day).</p>'; } else { $html .= '<div class="tct-table-wrap">'; $html .= '<table class="tct-table" data-tct-goal-order-table="daily">'; $html .= '<thead><tr><th style="width:28px;"></th><th>Goal</th></tr></thead>'; $html .= '<tbody>'; foreach ( $daily as $r ) { $gid = (int) $r['id']; $name = isset( $r['_display_name'] ) ? (string) $r['_display_name'] : ''; $html .= '<tr data-goal-id="' . esc_attr( $gid ) . '">'; $html .= '<td><span class="tct-drag-handle" title="Drag to reorder" aria-hidden="true"></span></td>'; $html .= '<td>' . esc_html( $name ) . '</td>'; $html .= '</tr>'; } $html .= '</tbody>'; $html .= '</table>'; $html .= '</div>'; $html .= '<p class="tct-muted" data-tct-goal-order-status="daily" aria-live="polite" aria-atomic="true" style="margin-top:6px;"></p>'; } $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Favorites order</h4>'; $html .= '<p class="tct-muted">Drag the handle to reorder. This controls the order of items in the mobile <strong>Favorites</strong> view.</p>'; if ( empty( $favorites ) ) { $html .= '<p class="tct-muted">No favorite goals yet.</p>'; } else { $html .= '<div class="tct-table-wrap">'; $html .= '<table class="tct-table" data-tct-goal-order-table="favorites">'; $html .= '<thead><tr><th style="width:28px;"></th><th>Goal</th></tr></thead>'; $html .= '<tbody>'; foreach ( $favorites as $r ) { $gid = (int) $r['id']; $name = isset( $r['_display_name'] ) ? (string) $r['_display_name'] : ''; $html .= '<tr data-goal-id="' . esc_attr( $gid ) . '">'; $html .= '<td><span class="tct-drag-handle" title="Drag to reorder" aria-hidden="true"></span></td>'; $html .= '<td>' . esc_html( $name ) . '</td>'; $html .= '</tr>'; } $html .= '</tbody>'; $html .= '</table>'; $html .= '</div>'; $html .= '<p class="tct-muted" data-tct-goal-order-status="favorites" aria-live="polite" aria-atomic="true" style="margin-top:6px;"></p>'; } return $html; }      private function tct_parent_complete_fetch_goal_row( $user_id, $goal_id ) {
        global $wpdb;

        $user_id = (int) $user_id;
        $goal_id = (int) $goal_id;
        if ( $user_id <= 0 || $goal_id <= 0 ) {
            return array();
        }

        if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) {
            return array();
        }

        $goals_table = TCT_DB::table_goals();
        $goal = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$goals_table} WHERE user_id = %d AND id = %d",
                $user_id,
                $goal_id
            ),
            ARRAY_A
        );

        return is_array( $goal ) ? $goal : array();
    }

    private function tct_parent_complete_goal_label( $goal ) {
        if ( is_array( $goal ) ) {
            if ( isset( $goal['goal_name'] ) && '' !== trim( (string) $goal['goal_name'] ) ) {
                return (string) $goal['goal_name'];
            }
            if ( isset( $goal['label_name'] ) && '' !== trim( (string) $goal['label_name'] ) ) {
                return (string) $goal['label_name'];
            }
            if ( isset( $goal['id'] ) && (int) $goal['id'] > 0 ) {
                return 'Goal #' . (int) $goal['id'];
            }
        }

        return 'Goal';
    }

    private function tct_parent_complete_child_remaining_count( $user_id, $goal, $now_tz = null ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 || ! is_array( $goal ) ) {
            return 0;
        }

        if ( ! ( $now_tz instanceof DateTimeImmutable ) ) {
            $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
            $now_tz = new DateTimeImmutable( 'now', $tz );
        }

        $target = isset( $goal['target'] ) ? max( 0, (int) $goal['target'] ) : 0;
        $achieved = 0;

        if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) {
            try {
                $vitality = TCT_Vitality::compute_for_goal( $user_id, $goal, $now_tz );
                if ( is_array( $vitality ) ) {
                    if ( isset( $vitality['target'] ) ) {
                        $target = max( 0, (int) $vitality['target'] );
                    }
                    if ( isset( $vitality['achieved'] ) ) {
                        $achieved = max( 0, (int) $vitality['achieved'] );
                    }
                }
            } catch ( Exception $e ) {
            }
        }

        if ( $target <= 0 ) {
            return 0;
        }

        $remaining = $target - $achieved;
        if ( $remaining < 0 ) {
            $remaining = 0;
        }

        return (int) $remaining;
    }

    private function tct_parent_complete_child_block_error( $user_id, $goal ) {
        global $wpdb;

        $user_id = (int) $user_id;
        if ( $user_id <= 0 || ! is_array( $goal ) ) {
            return new WP_Error( 'tct_parent_complete_missing_goal', 'Child goal could not be loaded.' );
        }

        $goal_id = isset( $goal['id'] ) ? (int) $goal['id'] : 0;
        $goal_label = $this->tct_parent_complete_goal_label( $goal );
        if ( $goal_id <= 0 ) {
            return new WP_Error( 'tct_parent_complete_missing_goal', 'Child goal "' . $goal_label . '" could not be loaded.' );
        }

        if ( isset( $goal['is_tracked'] ) && (int) $goal['is_tracked'] !== 1 ) {
            return new WP_Error( 'tct_parent_complete_child_archived', 'Goal "' . $goal_label . '" is archived and cannot be completed from the parent.' );
        }

        if ( ! empty( $goal['sleep_tracking_enabled'] ) || ! empty( $goal['wake_time_enabled'] ) || ! empty( $goal['bed_time_enabled'] ) ) {
            return new WP_Error( 'tct_parent_complete_child_auto', 'Goal "' . $goal_label . '" is auto-scored and cannot be completed from the parent.' );
        }

        $goal_type = isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : 'positive';
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_composite_goal_type' ) ) && TCT_Utils::is_composite_goal_type( $goal_type ) ) {
            return new WP_Error( 'tct_parent_complete_child_parent', 'Goal "' . $goal_label . '" is itself a composite parent and cannot be completed from another parent.' );
        }

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_type' ) ) && TCT_Utils::is_negative_goal_type( $goal_type ) ) {
            return new WP_Error( 'tct_parent_complete_child_negative', 'Goal "' . $goal_label . '" is not eligible for parent completion.' );
        }

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) && TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) {
            return new WP_Error( 'tct_parent_complete_child_no_interval', 'Goal "' . $goal_label . '" is not interval-based and cannot be completed from the parent.' );
        }

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) && TCT_Utils::is_anki_cards_goal_type( $goal_type ) ) {
            return new WP_Error( 'tct_parent_complete_child_anki', 'Goal "' . $goal_label . '" requires a manual Anki cards count and cannot be completed from the parent.' );
        }

        $tracking_mode = isset( $goal['tracking_mode'] ) ? (string) $goal['tracking_mode'] : 'todoist';
        if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) {
            $tracking_mode = 'todoist';
        }

        $tz_due = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
        $today_local = ( new DateTimeImmutable( 'now', $tz_due ) )->format( 'Y-m-d' );

        if ( class_exists( 'TCT_Interval' )
            && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) )
            && is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) )
            && is_callable( array( 'TCT_Interval', 'availability_cycle_is_paused_on_local_date' ) )
            && TCT_Interval::is_goal_eligible_for_availability_cycle( $goal )
        ) {
            $availability_cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal );
            if ( is_array( $availability_cfg ) && ! empty( $availability_cfg['enabled'] ) ) {
                $availability_paused_now = (bool) TCT_Interval::availability_cycle_is_paused_on_local_date( $availability_cfg, $today_local, $tz_due );
                if ( $availability_paused_now ) {
                    return new WP_Error( 'tct_parent_complete_child_paused', 'Goal "' . $goal_label . '" is currently paused.' );
                }
            }
        }

        $due_schedule_raw = isset( $goal['due_schedule_json'] ) ? trim( (string) $goal['due_schedule_json'] ) : '';
        if ( '' !== $due_schedule_raw
            && 'manual' === $tracking_mode
            && class_exists( 'TCT_Interval' )
            && is_callable( array( 'TCT_Interval', 'normalize_due_schedule_config' ) )
        ) {
            $due_cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_raw );
            $due_enabled = is_array( $due_cfg ) && ! empty( $due_cfg['enabled'] );
            if ( $due_enabled ) {
                $is_due_today = is_callable( array( 'TCT_Interval', 'due_schedule_is_due_on_local_date' ) )
                    ? (bool) TCT_Interval::due_schedule_is_due_on_local_date( $due_cfg, $today_local, $tz_due )
                    : true;

                if ( ! $is_due_today ) {
                    $weekday = '';
                    if ( is_callable( array( 'TCT_Interval', 'due_schedule_next_due_local_date' ) ) ) {
                        $next_due = TCT_Interval::due_schedule_next_due_local_date( $due_cfg, $today_local, $tz_due );
                        if ( $next_due instanceof DateTimeImmutable ) {
                            $weekday = $next_due->format( 'l' );
                        }
                    }

                    $message = 'Goal "' . $goal_label . '" is not due today.';
                    if ( '' !== $weekday ) {
                        $message .= ' Next due ' . $weekday . '.';
                    }

                    return new WP_Error( 'tct_parent_complete_child_not_due', $message );
                }

                if ( is_callable( array( 'TCT_Interval', 'due_schedule_local_day_window_utc_mysql' ) ) ) {
                    $window = TCT_Interval::due_schedule_local_day_window_utc_mysql( $today_local, $tz_due );
                    if ( is_array( $window ) && ! empty( $window['start_utc'] ) && ! empty( $window['end_utc'] ) ) {
                        $completions_table = TCT_DB::table_completions();
                        $already = (int) $wpdb->get_var(
                            $wpdb->prepare(
                                "SELECT COUNT(1) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s",
                                $user_id,
                                $goal_id,
                                $window['start_utc'],
                                $window['end_utc']
                            )
                        );
                        if ( $already > 0 ) {
                            return new WP_Error( 'tct_parent_complete_child_daily_limit', 'Goal "' . $goal_label . '" is already logged for today.' );
                        }
                    }
                }
            }
        }

        return null;
    }

    private function tct_parent_complete_log_child_once( $user_id, $goal ) {
        global $wpdb;

        $user_id = (int) $user_id;
        if ( $user_id <= 0 || ! is_array( $goal ) ) {
            return new WP_Error( 'tct_parent_complete_log_invalid', 'Child goal could not be completed.' );
        }

        $goal_id = isset( $goal['id'] ) ? (int) $goal['id'] : 0;
        if ( $goal_id <= 0 ) {
            return new WP_Error( 'tct_parent_complete_log_invalid', 'Child goal could not be completed.' );
        }

        $tracking_mode = isset( $goal['tracking_mode'] ) ? (string) $goal['tracking_mode'] : 'todoist';
        if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) {
            $tracking_mode = 'todoist';
        }

        $insert = TCT_DB::insert_manual_completion( $user_id, $goal_id, null, '', '' );
        if ( is_wp_error( $insert ) ) {
            return $insert;
        }

        $completion_row_id = isset( $insert['id'] ) ? (int) $insert['id'] : 0;
        $message = 'Logged.';
        $todoist_closed = false;

        $label_name = isset( $goal['label_name'] ) ? sanitize_text_field( (string) $goal['label_name'] ) : '';
        $token = ( class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'get_token' ) ) ) ? TCT_OAuth::get_token( $user_id ) : '';
        $connected = '' !== $token;

        if ( $connected && ( 'todoist' === $tracking_mode || 'hybrid' === $tracking_mode ) && '' !== $label_name && class_exists( 'TCT_Todoist_API' ) && is_callable( array( 'TCT_Todoist_API', 'close_task' ) ) ) {
            $todoist_task_id = $this->find_active_task_for_label( $token, $label_name );
            if ( '' === $todoist_task_id ) {
                $message = 'Logged; no active Todoist task to close.';
            } else {
                $closed = TCT_Todoist_API::close_task( $token, $todoist_task_id );
                if ( is_wp_error( $closed ) ) {
                    $data = $closed->get_error_data();
                    $msg = $closed->get_error_message();
                    if ( is_array( $data ) && isset( $data['status_code'] ) && 403 === (int) $data['status_code'] ) {
                        $msg = 'Logged; Todoist close failed (no write access). Please disconnect and reconnect.';
                    } else {
                        $msg = 'Logged; Todoist close failed.';
                    }
                    $message = $msg;
                } else {
                    $todoist_closed = true;
                    $message = 'Logged; closed a Todoist task.';
                    if ( $completion_row_id > 0 ) {
                        $completions_table = TCT_DB::table_completions();
                        $wpdb->update(
                            $completions_table,
                            array(
                                'todoist_task_id' => sanitize_text_field( (string) $todoist_task_id ),
                            ),
                            array(
                                'id' => (int) $completion_row_id,
                                'user_id' => (int) $user_id,
                            ),
                            array( '%s' ),
                            array( '%d', '%d' )
                        );
                    }
                }
            }
        }

        if ( $completion_row_id > 0 && class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'record_completion_from_event' ) ) ) {
            TCT_Ledger::record_completion_from_event( $user_id, $completion_row_id );
        }

        return array(
            'completion_id' => $completion_row_id,
            'todoistClosed' => $todoist_closed ? 1 : 0,
            'message' => $message,
        );
    }

    private function tct_parent_complete_child_goal( $user_id, $goal ) {
        $user_id = (int) $user_id;
        $goal_id = is_array( $goal ) && isset( $goal['id'] ) ? (int) $goal['id'] : 0;
        $goal_name = $this->tct_parent_complete_goal_label( $goal );

        $result = array(
            'goal_id' => $goal_id,
            'goal_name' => $goal_name,
            'remaining_before' => 0,
            'remaining_after' => 0,
            'completion_rows_added' => 0,
            'is_already_complete' => false,
            'is_fully_complete' => false,
            'blocked_message' => '',
        );

        if ( $user_id <= 0 || $goal_id <= 0 || ! is_array( $goal ) ) {
            $result['blocked_message'] = 'Child goal could not be loaded.';
            return $result;
        }

        $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
        $now_tz = new DateTimeImmutable( 'now', $tz );

        $remaining = $this->tct_parent_complete_child_remaining_count( $user_id, $goal, $now_tz );
        $result['remaining_before'] = $remaining;
        if ( $remaining <= 0 ) {
            $result['is_already_complete'] = true;
            $result['is_fully_complete'] = true;
            return $result;
        }

        while ( $remaining > 0 ) {
            $block = $this->tct_parent_complete_child_block_error( $user_id, $goal );
            if ( is_wp_error( $block ) ) {
                $result['blocked_message'] = $block->get_error_message();
                break;
            }

            $logged = $this->tct_parent_complete_log_child_once( $user_id, $goal );
            if ( is_wp_error( $logged ) ) {
                $result['blocked_message'] = $logged->get_error_message();
                break;
            }

            $result['completion_rows_added']++;
            $remaining--;
        }

        $remaining_after = $this->tct_parent_complete_child_remaining_count( $user_id, $goal, $now_tz );
        $result['remaining_after'] = $remaining_after;
        $result['is_fully_complete'] = ( $remaining_after <= 0 );

        return $result;
    }

    private function tct_parent_complete_child_potential_rows( $user_id, $goal, $now_tz = null ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 || ! is_array( $goal ) ) {
            return 0;
        }

        if ( ! ( $now_tz instanceof DateTimeImmutable ) ) {
            $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
            $now_tz = new DateTimeImmutable( 'now', $tz );
        }

        $remaining = $this->tct_parent_complete_child_remaining_count( $user_id, $goal, $now_tz );
        if ( $remaining <= 0 ) {
            return 0;
        }

        $block = $this->tct_parent_complete_child_block_error( $user_id, $goal );
        if ( is_wp_error( $block ) ) {
            return 0;
        }

        $tracking_mode = isset( $goal['tracking_mode'] ) ? (string) $goal['tracking_mode'] : 'todoist';
        if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) {
            $tracking_mode = 'todoist';
        }

        $due_schedule_raw = isset( $goal['due_schedule_json'] ) ? trim( (string) $goal['due_schedule_json'] ) : '';
        if ( '' !== $due_schedule_raw
            && 'manual' === $tracking_mode
            && class_exists( 'TCT_Interval' )
            && is_callable( array( 'TCT_Interval', 'normalize_due_schedule_config' ) )
        ) {
            $due_cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_raw );
            if ( is_array( $due_cfg ) && ! empty( $due_cfg['enabled'] ) && $remaining > 1 ) {
                $remaining = 1;
            }
        }

        return max( 0, (int) $remaining );
    }

    private function tct_parent_complete_preview_totals( $user_id, $parent_goal ) {
        static $cache = array();

        $user_id = (int) $user_id;
        $parent_goal_id = $this->tct_goal_id_from_composite_subject( $parent_goal );
        $default = array(
            'child_count' => 0,
            'completable_child_count' => 0,
            'completion_rows_total' => 0,
            'total_points' => 0,
        );

        if ( $user_id <= 0 || $parent_goal_id <= 0 ) {
            return $default;
        }

        $cache_key = $user_id . ':' . $parent_goal_id;
        if ( isset( $cache[ $cache_key ] ) ) {
            return $cache[ $cache_key ];
        }

        $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
        $now_tz = new DateTimeImmutable( 'now', $tz );
        $result = $default;
        $children = $this->tct_goal_composite_children( $parent_goal_id, $user_id );

        foreach ( (array) $children as $child_goal ) {
            if ( ! is_array( $child_goal ) || ! isset( $child_goal['id'] ) ) {
                continue;
            }

            $result['child_count']++;
            $potential_rows = $this->tct_parent_complete_child_potential_rows( $user_id, $child_goal, $now_tz );
            if ( $potential_rows <= 0 ) {
                continue;
            }

            $ppc = isset( $child_goal['points_per_completion'] ) ? max( 0, (int) $child_goal['points_per_completion'] ) : 0;
            $result['completable_child_count']++;
            $result['completion_rows_total'] += $potential_rows;
            $result['total_points'] += ( $potential_rows * $ppc );
        }

        $result['child_count'] = max( 0, (int) $result['child_count'] );
        $result['completable_child_count'] = max( 0, (int) $result['completable_child_count'] );
        $result['completion_rows_total'] = max( 0, (int) $result['completion_rows_total'] );
        $result['total_points'] = max( 0, (int) $result['total_points'] );

        $cache[ $cache_key ] = $result;
        return $result;
    }

    public function handle_complete_composite_parent_ajax() {
        if ( ! is_user_logged_in() ) {
            TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 );
        }

        TCT_Utils::enforce_ajax_nonce( 'tct_complete_composite_parent', 'nonce' );

        $user_id = get_current_user_id();
        $parent_goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0;
        if ( $parent_goal_id <= 0 ) {
            TCT_Utils::send_json_error( array( 'message' => 'Missing parent goal.' ), 400 );
        }

        if ( ! $this->tct_composite_goals_enabled() ) {
            TCT_Utils::send_json_error( array( 'message' => 'Composite goals are currently disabled.' ), 400 );
        }

        $parent_goal = $this->tct_parent_complete_fetch_goal_row( $user_id, $parent_goal_id );
        if ( empty( $parent_goal ) ) {
            TCT_Utils::send_json_error( array( 'message' => 'Parent goal not found.' ), 404 );
        }

        if ( ! $this->tct_goal_is_composite_parent( $parent_goal, $user_id ) ) {
            TCT_Utils::send_json_error( array( 'message' => 'This goal is not a composite parent.' ), 400 );
        }

        $children = $this->tct_goal_composite_children( $parent_goal_id, $user_id );
        if ( empty( $children ) ) {
            TCT_Utils::send_json_error( array( 'message' => 'This parent has no child goals.' ), 400 );
        }

        $total_children = 0;
        $child_goal_ids_changed = array();
        $child_goal_ids_blocked = array();
        $blocked_children = array();
        $completion_rows_added = 0;
        $already_complete_children = 0;
        $fully_complete_children = 0;

        foreach ( $children as $child_goal ) {
            if ( ! is_array( $child_goal ) || ! isset( $child_goal['id'] ) ) {
                continue;
            }

            $total_children++;
            $child_result = $this->tct_parent_complete_child_goal( $user_id, $child_goal );
            $child_goal_id = isset( $child_result['goal_id'] ) ? (int) $child_result['goal_id'] : 0;
            $rows_added = isset( $child_result['completion_rows_added'] ) ? (int) $child_result['completion_rows_added'] : 0;

            if ( ! empty( $child_result['is_already_complete'] ) ) {
                $already_complete_children++;
            }

            if ( ! empty( $child_result['is_fully_complete'] ) ) {
                $fully_complete_children++;
            }

            if ( $rows_added > 0 ) {
                $completion_rows_added += $rows_added;
                if ( $child_goal_id > 0 ) {
                    $child_goal_ids_changed[] = $child_goal_id;
                }
            }

            if ( ! empty( $child_result['blocked_message'] ) && ! empty( $child_result['remaining_after'] ) ) {
                if ( $child_goal_id > 0 ) {
                    $child_goal_ids_blocked[] = $child_goal_id;
                }
                $blocked_children[] = array(
                    'goalId' => $child_goal_id,
                    'goalName' => isset( $child_result['goal_name'] ) ? (string) $child_result['goal_name'] : '',
                    'message' => (string) $child_result['blocked_message'],
                );
            }
        }

        $child_goal_ids_changed = array_values( array_unique( array_filter( array_map( 'intval', $child_goal_ids_changed ) ) ) );
        $child_goal_ids_blocked = array_values( array_unique( array_filter( array_map( 'intval', $child_goal_ids_blocked ) ) ) );
        $changed_child_count = count( $child_goal_ids_changed );
        $blocked_child_count = count( $child_goal_ids_blocked );
        $did_change = $completion_rows_added > 0;

        if ( $did_change ) {
            $message = 'Completed ' . (string) $completion_rows_added . ' child completions across ' . (string) $changed_child_count . ' child goals.';
            if ( $blocked_child_count > 0 ) {
                $message .= ' Skipped ' . (string) $blocked_child_count . ' blocked child goals.';
            }
        } elseif ( $fully_complete_children >= $total_children && $total_children > 0 ) {
            $message = 'All child goals are already complete for the current interval.';
        } elseif ( $blocked_child_count > 0 ) {
            $message = 'No child goals were completed. The remaining child goals are currently blocked.';
        } else {
            $message = 'No child goals needed to be completed.';
        }

        TCT_Utils::send_json_success(
            array(
                'ok' => true,
                'parentGoalId' => $parent_goal_id,
                'didChange' => $did_change ? 1 : 0,
                'completionRowsAdded' => $completion_rows_added,
                'changedChildGoalIds' => $child_goal_ids_changed,
                'blockedChildGoalIds' => $child_goal_ids_blocked,
                'blockedChildren' => $blocked_children,
                'alreadyCompleteChildCount' => $already_complete_children,
                'fullyCompleteChildCount' => $fully_complete_children,
                'message' => $message,
            )
        );
    }

    public function handle_quick_complete_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_quick_complete', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.', ), 400 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, goal_name, label_name, tracking_mode, is_tracked, target, period_unit, period_span, period_mode, points_per_completion, plant_name, goal_type, threshold, due_schedule_json, availability_cycle_json, wake_time_enabled, wake_time_target, bed_time_enabled, bed_time_target FROM {$goals_table} WHERE user_id = %d AND id = %d", $user_id, $goal_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.', ), 404 ); }         if ( isset( $goal['wake_time_enabled'] ) && 1 === (int) $goal['wake_time_enabled'] ) { TCT_Utils::send_json_error( array( 'message' => 'This goal is auto-scored from Sleep Tracker wake-time.', ), 400 ); } if ( isset( $goal['bed_time_enabled'] ) && 1 === (int) $goal['bed_time_enabled'] ) { TCT_Utils::send_json_error( array( 'message' => 'This goal is auto-scored from Sleep Tracker bed-time.', ), 400 ); }
$goal['period_span'] = isset( $goal['period_span'] ) ? max( 1, (int) $goal['period_span'] ) : 1; $tracking_mode = isset( $goal['tracking_mode'] ) ? (string) $goal['tracking_mode'] : 'todoist'; if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) { $tracking_mode = 'todoist'; } $tz_due = TCT_Utils::wp_timezone(); $today_local = ( new DateTimeImmutable( 'now', $tz_due ) )->format( 'Y-m-d' ); $availability_paused_now = false; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) ) && is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) ) && is_callable( array( 'TCT_Interval', 'availability_cycle_is_paused_on_local_date' ) ) ) { if ( TCT_Interval::is_goal_eligible_for_availability_cycle( $goal ) ) { $availability_cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal ); if ( is_array( $availability_cfg ) && ! empty( $availability_cfg['enabled'] ) ) { $availability_paused_now = (bool) TCT_Interval::availability_cycle_is_paused_on_local_date( $availability_cfg, $today_local, $tz_due ); } } }
        // Due schedule enforcement (Chunk 3): schedule-enabled manual goals are only actionable on due days.
        $due_schedule_raw = isset( $goal['due_schedule_json'] ) ? $goal['due_schedule_json'] : '';
        if ( '' !== trim( (string) $due_schedule_raw ) && class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_due_schedule_config' ) ) ) {
            $due_cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_raw );
            $due_enabled = is_array( $due_cfg ) && ! empty( $due_cfg['enabled'] );

            // Not applicable to Todoist-linked goals (todoist/hybrid). Enforce only for manual tracking.
            if ( $due_enabled && 'manual' === $tracking_mode ) {
                $tz_due = TCT_Utils::wp_timezone();
                $today_local = ( new DateTimeImmutable( 'now', $tz_due ) )->format( 'Y-m-d' );

                $is_due_today = is_callable( array( 'TCT_Interval', 'due_schedule_is_due_on_local_date' ) )
                    ? (bool) TCT_Interval::due_schedule_is_due_on_local_date( $due_cfg, $today_local, $tz_due )
                    : true;

                if ( ! $is_due_today && ! $availability_paused_now ) {
                    $next_due_local = is_callable( array( 'TCT_Interval', 'due_schedule_next_due_local_date' ) )
                        ? (string) TCT_Interval::due_schedule_next_due_local_date( $due_cfg, $today_local, $tz_due )
                        : '';

                    $weekday = '';
                    if ( '' !== $next_due_local ) {
                        try {
                            $next_dt = new DateTimeImmutable( $next_due_local . ' 00:00:00', $tz_due );
                            $weekday = $next_dt->format( 'l' );
                        } catch ( Exception $e ) {
                            $weekday = '';
                        }
                    }

                    $msg = 'Not due today';
                    if ( '' !== $weekday ) {
                        $msg .= '  --  next due ' . $weekday;
                    }
                    TCT_Utils::send_json_error( array( 'message' => $msg, 'code' => 'tct_not_due_today' ), 400 );
                }

                // Hard limit: max 1 completion/fail per local day for due-scheduled goals.
                if ( is_callable( array( 'TCT_Interval', 'due_schedule_local_day_window_utc_mysql' ) ) ) {
                    $window = TCT_Interval::due_schedule_local_day_window_utc_mysql( $today_local, $tz_due );
                    if ( is_array( $window ) && ! empty( $window['start_utc'] ) && ! empty( $window['end_utc'] ) ) {
                        $completions_table_ds = TCT_DB::table_completions();
                        $already = (int) $wpdb->get_var( $wpdb->prepare(
                            "SELECT COUNT(1) FROM {$completions_table_ds} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s",
                            $user_id,
                            $goal_id,
                            $window['start_utc'],
                            $window['end_utc']
                        ) );
                        if ( $already > 0 ) {
                            TCT_Utils::send_json_error(
                                array(
                                    'message' => 'Already logged today  --  max 1 per day.',
                                    'code'    => 'tct_due_schedule_one_per_day',
                                ),
                                400
                            );
                        }
                    }
                }
            }
        }
 $goal_type = isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : 'positive'; $threshold = isset( $goal['threshold'] ) && is_numeric( $goal['threshold'] ) ? (int) $goal['threshold'] : null; $is_negative = TCT_Utils::is_negative_goal_type( $goal_type ); $is_anki_cards_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type ) : ( 'anki_cards' === strtolower( trim( (string) $goal_type ) ) ); $ppc = isset( $goal['points_per_completion'] ) ? (int) $goal['points_per_completion'] : 0; $anki_cards_logged = 0; $anki_note = ''; if ( $is_anki_cards_goal ) { $anki_target = isset( $goal['target'] ) ? (int) $goal['target'] : 0; if ( $anki_target <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'This Anki cards goal needs a daily cards target greater than 0.' ), 400 ); } $anki_cards_logged = isset( $_POST['anki_cards'] ) ? (int) wp_unslash( $_POST['anki_cards'] ) : 0; if ( $anki_cards_logged <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Please enter how many Anki cards you studied today.' ), 400 ); } $tz_anki = TCT_Utils::wp_timezone(); $day_start_tz_anki = ( new DateTimeImmutable( 'now', $tz_anki ) )->setTime( 0, 0, 0 ); $day_end_tz_anki = $day_start_tz_anki->add( new DateInterval( 'P1D' ) ); $anki_day_start_utc = $day_start_tz_anki->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ); $anki_day_end_utc = $day_end_tz_anki->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ); $completions_table_anki = TCT_DB::table_completions(); $anki_existing = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table_anki} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $anki_day_start_utc, $anki_day_end_utc ) ); if ( $anki_existing > 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Already logged today  --  max 1 per day.' ), 400 ); } $anki_note = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'format_anki_cards_completion_note' ) ) ? (string) TCT_Utils::format_anki_cards_completion_note( $anki_cards_logged ) : ( 'Anki cards: ' . (string) (int) $anki_cards_logged ); } $tz_early = TCT_Utils::wp_timezone(); $now_tz_early = new DateTimeImmutable( 'now', $tz_early ); $completions_before = 0; $is_violation = false; $violation_number = 0; $negative_points = 0; if ( $is_negative ) { $completions_table_early = TCT_DB::table_completions(); $period_unit = isset( $goal['period_unit'] ) ? (string) $goal['period_unit'] : 'week'; $period_span = isset( $goal['period_span'] ) ? (int) $goal['period_span'] : 1; $loop_bounds = TCT_Interval::current_loop_bounds( $now_tz_early, $period_unit, $period_span ); $loop_start = isset( $loop_bounds['start'] ) ? $loop_bounds['start'] : null; $loop_end = isset( $loop_bounds['end'] ) ? $loop_bounds['end'] : null; if ( $loop_start && $loop_end ) { $loop_start_utc = $loop_start->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ); $loop_end_utc = $loop_end->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ); $completions_before = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table_early}
                         WHERE user_id = %d AND goal_id = %d
                         AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $loop_start_utc, $loop_end_utc ) ); } $is_violation = TCT_Utils::is_negative_goal_violation( $goal_type, $threshold, $completions_before ); if ( $is_violation ) { if ( TCT_Utils::is_never_goal( $goal_type, $threshold ) ) { $violation_number = $completions_before + 1; } else { $th = ( null !== $threshold ) ? (int) $threshold : 0; $violation_number = ( $completions_before - $th ) + 1; if ( $violation_number < 1 ) { $violation_number = 1; } } $negative_points = TCT_Utils::compute_violation_penalty( $ppc, $violation_number ); } } $insert = TCT_DB::insert_manual_completion( $user_id, $goal_id, null, '', $anki_note ); if ( is_wp_error( $insert ) ) { TCT_Utils::send_json_error( array( 'message' => $insert->get_error_message(), ), 500 ); } $completion_row_id = isset( $insert['id'] ) ? (int) $insert['id'] : 0; $message = $is_anki_cards_goal ? ( 'Logged ' . (int) $anki_cards_logged . ' cards.' ) : 'Logged.'; $todoist_closed = false; if ( ! $is_negative ) { $label_name = isset( $goal['label_name'] ) ? sanitize_text_field( (string) $goal['label_name'] ) : ''; $token = TCT_OAuth::get_token( $user_id ); $connected = '' !== $token; if ( $connected && ( 'todoist' === $tracking_mode || 'hybrid' === $tracking_mode ) && '' !== $label_name ) { $todoist_task_id = $this->find_active_task_for_label( $token, $label_name ); if ( '' === $todoist_task_id ) { $message = 'Logged; no active Todoist task to close.'; } else { $closed = TCT_Todoist_API::close_task( $token, $todoist_task_id ); if ( is_wp_error( $closed ) ) { $data = $closed->get_error_data(); $msg = $closed->get_error_message(); if ( is_array( $data ) && isset( $data['status_code'] ) && 403 === (int) $data['status_code'] ) { $msg = 'Logged; Todoist close failed (no write access). Please disconnect and reconnect.'; } else { $msg = 'Logged; Todoist close failed.'; } $message = $msg; } else { $todoist_closed = true; $message = 'Logged; closed a Todoist task.'; if ( $completion_row_id > 0 ) { $completions_table = TCT_DB::table_completions(); $wpdb->update( $completions_table, array( 'todoist_task_id' => sanitize_text_field( (string) $todoist_task_id ), ), array( 'id' => (int) $completion_row_id, 'user_id' => (int) $user_id, ), array( '%s' ), array( '%d', '%d' ) ); } } } } } if ( $completion_row_id > 0 ) { if ( $is_negative ) { $this->record_negative_goal_ledger_entry( $user_id, $goal_id, $goal, $completion_row_id, $negative_points, $is_violation ); if ( $is_violation ) { $message = 'Logged violation. Penalty applied.'; } else { $message = 'Logged (within limit).'; } } else { TCT_Ledger::record_completion_from_event( $user_id, $completion_row_id ); } } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $completions_table = TCT_DB::table_completions(); $vitality_payload = array( 'vitality' => 100, 'target' => isset( $goal['target'] ) ? (int) $goal['target'] : 0, 'achieved' => 0, 'loop_start_utc_mysql' => '', 'loop_end_utc_mysql' => '', 'time_remaining_seconds' => 0, 'time_remaining_label' => '', ); if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) { try { $maybe_vitality = TCT_Vitality::compute_for_goal( $user_id, $goal, $now_tz ); if ( is_array( $maybe_vitality ) ) { $vitality_payload = array_merge( $vitality_payload, $maybe_vitality ); } } catch ( Exception $e ) { } } $goal_count = isset( $vitality_payload['achieved'] ) ? (int) $vitality_payload['achieved'] : 0; $last_completed_raw = (string) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $last_completed_text = '--'; $last_completed_raw = is_string( $last_completed_raw ) ? trim( $last_completed_raw ) : ''; if ( '' === $last_completed_raw || '0000-00-00 00:00:00' === $last_completed_raw ) { $last_completed_text = 'never'; } else { $ts = strtotime( $last_completed_raw . ' UTC' ); if ( false !== $ts ) { $now_ts = (int) $now_tz->getTimestamp(); $diff_s = $now_ts - (int) $ts; if ( $diff_s >= 0 && $diff_s < 60 ) { $last_completed_text = 'just now'; } else { $abbr = $this->tct_abbrev_time_ago( (int) $ts, (int) $now_ts ); if ( 'just now' === $abbr ) { $last_completed_text = 'just now'; } elseif ( '--' === $abbr ) { $last_completed_text = '--'; } else { $last_completed_text = trim( $abbr ) . ' ago'; } } } } $today_tz = $now_tz->setTime( 0, 0, 0 ); $period_unit = isset( $goal['period_unit'] ) ? sanitize_text_field( (string) $goal['period_unit'] ) : 'week'; $tile_state = $this->compute_goal_tile_state( $goal, $goal_count, $now_tz, $today_tz ); $vitality_value = isset( $vitality_payload['vitality'] ) ? (int) $vitality_payload['vitality'] : 100; if ( ! $is_negative && $vitality_value < 0 ) { $vitality_value = 0; } if ( $vitality_value > 100 ) { $vitality_value = 100; } $vitality_target = isset( $vitality_payload['target'] ) ? (int) $vitality_payload['target'] : 0; if ( $vitality_target < 0 ) { $vitality_target = 0; } $vitality_achieved = isset( $vitality_payload['achieved'] ) ? (int) $vitality_payload['achieved'] : $goal_count; if ( $vitality_achieved < 0 ) { $vitality_achieved = 0; } $vitality_remaining_seconds = isset( $vitality_payload['time_remaining_seconds'] ) ? (int) $vitality_payload['time_remaining_seconds'] : 0; if ( $vitality_remaining_seconds < 0 ) { $vitality_remaining_seconds = 0; } $vitality_remaining_label = isset( $vitality_payload['time_remaining_label'] ) ? (string) $vitality_payload['time_remaining_label'] : ''; $vitality_loop_start_utc = isset( $vitality_payload['loop_start_utc_mysql'] ) ? (string) $vitality_payload['loop_start_utc_mysql'] : ''; $vitality_loop_end_utc = isset( $vitality_payload['loop_end_utc_mysql'] ) ? (string) $vitality_payload['loop_end_utc_mysql'] : ''; $plant_name = isset( $goal['plant_name'] ) ? trim( (string) $goal['plant_name'] ) : ''; $plant_bucket = 0; $plant_image_url = ''; if ( '' !== $plant_name && class_exists( 'TCT_Utils' ) ) { if ( method_exists( 'TCT_Utils', 'vitality_bucket_biased' ) ) { $plant_bucket = (int) TCT_Utils::vitality_bucket_biased( (int) $vitality_value ); } else { $v = (int) $vitality_value; if ( $v < 0 ) { $v = 0; } if ( $v > 100 ) { $v = 100; } $plant_bucket = (int) ( 5 * intdiv( ( $v + 3 ), 5 ) ); if ( $plant_bucket < 0 ) { $plant_bucket = 0; } if ( $plant_bucket > 100 ) { $plant_bucket = 100; } } if ( method_exists( 'TCT_Utils', 'resolve_vitality_plant_image_url' ) ) { $resolved = TCT_Utils::resolve_vitality_plant_image_url( $plant_name, (int) $vitality_value, 'medium' ); if ( $resolved ) { $plant_image_url = (string) $resolved; } } } $points_balance = 0; $points_balance_label = '0'; if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'get_balance' ) ) ) { $points_balance = (int) TCT_Ledger::get_balance( $user_id ); $points_balance_label = function_exists( 'number_format_i18n' ) ? number_format_i18n( (int) $points_balance ) : (string) $points_balance; } $reward_stats_html = $this->get_reward_stats_table_html( $user_id, true ); $composite_parent_goal_id = 0; $composite_parent_all_children_complete = 0; if ( $this->tct_composite_goals_enabled() && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'get_composite_parent_goal_id_for_child' ) ) { $composite_parent_goal_id = (int) TCT_DB::get_composite_parent_goal_id_for_child( $goal_id, $user_id ); if ( $composite_parent_goal_id > 0 ) { $composite_parent_preview = $this->tct_composite_dashboard_parent_preview_data( $composite_parent_goal_id, $user_id ); if ( is_array( $composite_parent_preview ) && ! empty( $composite_parent_preview['all_children_complete'] ) ) { $composite_parent_all_children_complete = 1; } } } if ( $availability_paused_now ) { $pause_suffix = ' Goal is currently paused; this completion will count when the goal resumes.'; if ( false === stripos( $message, 'count when the goal resumes' ) ) { $message .= $pause_suffix; } } TCT_Utils::send_json_success( array( 'ok' => true, 'goalId' => (int) $goal_id, 'compositeParentGoalId' => (int) $composite_parent_goal_id, 'compositeParentAllChildrenComplete' => (int) $composite_parent_all_children_complete, 'lastCompletedText' => (string) $last_completed_text, 'vitality' => (int) $vitality_value, 'target' => (int) $vitality_target, 'achieved' => (int) $vitality_achieved, 'time_remaining_label' => (string) $vitality_remaining_label, 'time_remaining_seconds' => (int) $vitality_remaining_seconds, 'loop_start_utc_mysql' => (string) $vitality_loop_start_utc, 'loop_end_utc_mysql' => (string) $vitality_loop_end_utc, 'plant_name' => (string) $plant_name, 'plant_bucket' => (int) $plant_bucket, 'plant_image_url' => (string) $plant_image_url, 'statusKey' => isset( $tile_state['statusKey'] ) ? (string) $tile_state['statusKey'] : 'on-track', 'statusLabel' => isset( $tile_state['statusLabel'] ) ? (string) $tile_state['statusLabel'] : 'On track', 'paceLine1' => isset( $tile_state['paceLine1'] ) ? (string) $tile_state['paceLine1'] : '', 'paceLine2' => isset( $tile_state['paceLine2'] ) ? (string) $tile_state['paceLine2'] : '', 'unit' => isset( $tile_state['unit'] ) ? (string) $tile_state['unit'] : '', 'todoistClosed' => $todoist_closed ? 1 : 0, 'pointsBalance' => (int) $points_balance, 'pointsBalanceLabel' => (string) $points_balance_label, 'rewardStatsHtml' => (string) $this->get_reward_stats_table_html( $user_id, true ), 'rewardStatsHtml' => (string) $this->get_reward_stats_table_html( $user_id, true ), 'message' => (string) $message, ) ); } public function handle_fail_goal_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_fail_goal', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.' ), 400 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, goal_name, label_name, tracking_mode, is_tracked, target, period_unit, period_span, period_mode, points_per_completion, plant_name, goal_type, threshold, due_schedule_json, availability_cycle_json, wake_time_enabled, wake_time_target, bed_time_enabled, bed_time_target FROM {$goals_table} WHERE user_id = %d AND id = %d", $user_id, $goal_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.' ), 404 ); }         if ( isset( $goal['wake_time_enabled'] ) && 1 === (int) $goal['wake_time_enabled'] ) { TCT_Utils::send_json_error( array( 'message' => 'This goal is auto-scored from Sleep Tracker wake-time.', ), 400 ); } if ( isset( $goal['bed_time_enabled'] ) && 1 === (int) $goal['bed_time_enabled'] ) { TCT_Utils::send_json_error( array( 'message' => 'This goal is auto-scored from Sleep Tracker bed-time.', ), 400 ); }
$goal['period_span'] = isset( $goal['period_span'] ) ? max( 1, (int) $goal['period_span'] ) : 1;
        $tracking_mode = isset( $goal['tracking_mode'] ) ? (string) $goal['tracking_mode'] : 'todoist';
        if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) { $tracking_mode = 'todoist'; }

        $tz_due = TCT_Utils::wp_timezone();
        $today_local = ( new DateTimeImmutable( 'now', $tz_due ) )->format( 'Y-m-d' );
        $availability_paused_now = false;
        if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) ) && is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) ) && is_callable( array( 'TCT_Interval', 'availability_cycle_is_paused_on_local_date' ) ) ) {
            if ( TCT_Interval::is_goal_eligible_for_availability_cycle( $goal ) ) {
                $availability_cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal );
                if ( is_array( $availability_cfg ) && ! empty( $availability_cfg['enabled'] ) ) {
                    $availability_paused_now = (bool) TCT_Interval::availability_cycle_is_paused_on_local_date( $availability_cfg, $today_local, $tz_due );
                }
            }
        }
        if ( $availability_paused_now ) {
            TCT_Utils::send_json_error( array( 'message' => 'This goal is currently paused  --  manual fail is unavailable until it resumes.', 'code' => 'tct_goal_paused' ), 400 );
        }

        // Due schedule enforcement (Chunk 3): schedule-enabled manual goals are only actionable on due days.
        $due_schedule_raw = isset( $goal['due_schedule_json'] ) ? $goal['due_schedule_json'] : '';
        if ( '' !== trim( (string) $due_schedule_raw ) && class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_due_schedule_config' ) ) ) {
            $due_cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_raw );
            $due_enabled = is_array( $due_cfg ) && ! empty( $due_cfg['enabled'] );

            // Not applicable to Todoist-linked goals (todoist/hybrid). Enforce only for manual tracking.
            if ( $due_enabled && 'manual' === $tracking_mode ) {
                $tz_due = TCT_Utils::wp_timezone();
                $today_local = ( new DateTimeImmutable( 'now', $tz_due ) )->format( 'Y-m-d' );

                $is_due_today = is_callable( array( 'TCT_Interval', 'due_schedule_is_due_on_local_date' ) )
                    ? (bool) TCT_Interval::due_schedule_is_due_on_local_date( $due_cfg, $today_local, $tz_due )
                    : true;

                if ( ! $is_due_today ) {
                    $next_due_local = is_callable( array( 'TCT_Interval', 'due_schedule_next_due_local_date' ) )
                        ? (string) TCT_Interval::due_schedule_next_due_local_date( $due_cfg, $today_local, $tz_due )
                        : '';

                    $weekday = '';
                    if ( '' !== $next_due_local ) {
                        try {
                            $next_dt = new DateTimeImmutable( $next_due_local . ' 00:00:00', $tz_due );
                            $weekday = $next_dt->format( 'l' );
                        } catch ( Exception $e ) {
                            $weekday = '';
                        }
                    }

                    $msg = 'Not due today';
                    if ( '' !== $weekday ) {
                        $msg .= '  --  next due ' . $weekday;
                    }
                    TCT_Utils::send_json_error( array( 'message' => $msg, 'code' => 'tct_not_due_today' ), 400 );
                }

                // Hard limit: max 1 completion/fail per local day for due-scheduled goals.
                if ( is_callable( array( 'TCT_Interval', 'due_schedule_local_day_window_utc_mysql' ) ) ) {
                    $window = TCT_Interval::due_schedule_local_day_window_utc_mysql( $today_local, $tz_due );
                    if ( is_array( $window ) && ! empty( $window['start_utc'] ) && ! empty( $window['end_utc'] ) ) {
                        $completions_table_ds = TCT_DB::table_completions();
                        $already = (int) $wpdb->get_var( $wpdb->prepare(
                            "SELECT COUNT(1) FROM {$completions_table_ds} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s",
                            $user_id,
                            $goal_id,
                            $window['start_utc'],
                            $window['end_utc']
                        ) );
                        if ( $already > 0 ) {
                            TCT_Utils::send_json_error(
                                array(
                                    'message' => 'Already logged today  --  max 1 per day.',
                                    'code'    => 'tct_due_schedule_one_per_day',
                                ),
                                400
                            );
                        }
                    }
                }
            }
        }
 $ppc = isset( $goal['points_per_completion'] ) ? (int) $goal['points_per_completion'] : 0; $target = isset( $goal['target'] ) ? (int) $goal['target'] : 0; $penalty = 0; if ( $ppc > 0 && $target > 0 && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_penalty_points' ) ) ) { $penalty = (int) TCT_Utils::compute_penalty_points( $ppc, $target, 0 ); } $insert = TCT_DB::insert_manual_completion( $user_id, $goal_id ); if ( is_wp_error( $insert ) ) { TCT_Utils::send_json_error( array( 'message' => $insert->get_error_message() ), 500 ); } $completion_row_id = isset( $insert['id'] ) ? (int) $insert['id'] : 0; if ( $completion_row_id > 0 && $penalty !== 0 ) { $this->record_fail_goal_ledger_entry( $user_id, $goal_id, $goal, $completion_row_id, $penalty ); } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $completions_table = TCT_DB::table_completions(); $vitality_payload = array( 'vitality' => 100, 'target' => $target, 'achieved' => 0, 'loop_start_utc_mysql' => '', 'loop_end_utc_mysql' => '', 'time_remaining_seconds' => 0, 'time_remaining_label' => '', ); if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) { try { $maybe = TCT_Vitality::compute_for_goal( $user_id, $goal, $now_tz ); if ( is_array( $maybe ) ) { $vitality_payload = array_merge( $vitality_payload, $maybe ); } } catch ( Exception $e ) { } } $goal_count = isset( $vitality_payload['achieved'] ) ? (int) $vitality_payload['achieved'] : 0; $last_completed_raw = (string) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $last_completed_text = 'just now'; $today_tz = $now_tz->setTime( 0, 0, 0 ); $tile_state = $this->compute_goal_tile_state( $goal, $goal_count, $now_tz, $today_tz ); $vitality_value = isset( $vitality_payload['vitality'] ) ? (int) $vitality_payload['vitality'] : 0; if ( $vitality_value < 0 ) { $vitality_value = 0; } if ( $vitality_value > 100 ) { $vitality_value = 100; } $points_balance = class_exists( 'TCT_Ledger' ) ? (int) TCT_Ledger::get_balance( $user_id ) : 0; $points_balance_label = number_format_i18n( $points_balance ); $plant_name = isset( $goal['plant_name'] ) ? trim( (string) $goal['plant_name'] ) : ''; $plant_image_url = ''; if ( '' !== $plant_name && class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'resolve_vitality_plant_image_url' ) ) { $url = TCT_Utils::resolve_vitality_plant_image_url( $plant_name, (int) $vitality_value, 'medium' ); if ( $url ) { $plant_image_url = (string) $url; } } TCT_Utils::send_json_success( array( 'completionId' => $completion_row_id, 'goalCount' => $goal_count, 'lastCompletedText' => $last_completed_text, 'tileState' => $tile_state, 'vitalityValue' => $vitality_value, 'vitalityTarget' => isset( $vitality_payload['target'] ) ? (int) $vitality_payload['target'] : 0, 'vitalityAchieved' => isset( $vitality_payload['achieved'] ) ? (int) $vitality_payload['achieved'] : 0, 'vitalityTimeRemainingSeconds' => isset( $vitality_payload['time_remaining_seconds'] ) ? (int) $vitality_payload['time_remaining_seconds'] : 0, 'vitalityTimeRemainingLabel' => isset( $vitality_payload['time_remaining_label'] ) ? (string) $vitality_payload['time_remaining_label'] : '', 'plantImageUrl' => $plant_image_url, 'plantName' => $plant_name, 'pointsBalance' => (int) $points_balance, 'pointsBalanceLabel' => (string) $points_balance_label, 'rewardStatsHtml' => (string) $this->get_reward_stats_table_html( $user_id, true ), 'message' => 'Goal failed. Penalty applied: ' . (string) $penalty, ) ); } private function record_fail_goal_ledger_entry( $user_id, $goal_id, $goal, $completion_id, $points ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $completion_id = (int) $completion_id; $points = (int) $points; if ( $user_id <= 0 || $goal_id <= 0 || $completion_id <= 0 ) { return; } $ledger_table = TCT_DB::table_ledger(); $completions_table = TCT_DB::table_completions(); $c = $wpdb->get_row( $wpdb->prepare( "SELECT source, source_ref, completed_at FROM {$completions_table} WHERE id = %d AND user_id = %d", $completion_id, $user_id ), ARRAY_A ); if ( ! is_array( $c ) ) { return; } $source = isset( $c['source'] ) ? (string) $c['source'] : 'manual'; $source_ref = isset( $c['source_ref'] ) ? (string) $c['source_ref'] : ''; $occurred_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : ''; if ( '' === $occurred_at || '0000-00-00 00:00:00' === $occurred_at ) { $occurred_at = current_time( 'mysql', true ); } $event_key = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id ); $event_type = 'completion'; $details = '[manual fail]'; $goal_name = isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : ''; $label_name = isset( $goal['label_name'] ) && is_string( $goal['label_name'] ) ? (string) $goal['label_name'] : ''; $now = current_time( 'mysql', true ); $sql = "INSERT INTO {$ledger_table} (
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
                    details = VALUES(details),
                    updated_at = VALUES(updated_at)"; $wpdb->query( $wpdb->prepare( $sql, $user_id, $event_key, $event_type, $points, $occurred_at, $goal_id, $goal_name, $label_name, '', '', $details, $now, $now ) ); } private function record_negative_goal_ledger_entry( $user_id, $goal_id, $goal, $completion_id, $points, $is_violation ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $completion_id = (int) $completion_id; $points = (int) $points; if ( $user_id <= 0 || $goal_id <= 0 || $completion_id <= 0 ) { return; } $ledger_table = TCT_DB::table_ledger(); $completions_table = TCT_DB::table_completions(); $c = $wpdb->get_row( $wpdb->prepare( "SELECT source, source_ref, completed_at, note, task_content
                 FROM {$completions_table}
                 WHERE id = %d AND user_id = %d", $completion_id, $user_id ), ARRAY_A ); if ( ! is_array( $c ) ) { return; } $source = isset( $c['source'] ) ? (string) $c['source'] : 'manual'; $source_ref = isset( $c['source_ref'] ) ? (string) $c['source_ref'] : ''; $occurred_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : ''; if ( '' === $occurred_at || '0000-00-00 00:00:00' === $occurred_at ) { $occurred_at = current_time( 'mysql', true ); } $event_key = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id ); $event_type = 'completion'; $details = ''; if ( isset( $c['note'] ) && is_string( $c['note'] ) && '' !== trim( $c['note'] ) ) { $details = (string) $c['note']; } elseif ( isset( $c['task_content'] ) && is_string( $c['task_content'] ) && '' !== trim( $c['task_content'] ) ) { $details = wp_strip_all_tags( (string) $c['task_content'] ); } if ( $is_violation ) { if ( '' !== $details ) { $details .= ' [violation]'; } else { $details = '[violation]'; } } $goal_name = isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : ''; $label_name = isset( $goal['label_name'] ) && is_string( $goal['label_name'] ) ? (string) $goal['label_name'] : ''; $now = current_time( 'mysql', true ); $sql = "INSERT INTO {$ledger_table} (
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
                    details = VALUES(details),
                    updated_at = VALUES(updated_at)"; $wpdb->query( $wpdb->prepare( $sql, $user_id, $event_key, $event_type, $points, $occurred_at, $goal_id, $goal_name, $label_name, '', '', $details, $now, $now ) ); } public function handle_goal_history_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_goal_history', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.', ), 400 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $completions_table = TCT_DB::table_completions(); $ledger_table = TCT_DB::table_ledger(); $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, goal_name, target, period_unit, period_span, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, goal_type, threshold
                 FROM {$goals_table}
                 WHERE user_id = %d AND id = %d", $user_id, $goal_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.', ), 404 ); } $goal_name = isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : ''; $tz = TCT_Utils::wp_timezone(); $tz_label = $tz ? $tz->getName() : 'UTC'; $fmt = 'Y-m-d H:i'; $total_completions = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $completion_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, completed_at, source, source_ref
                 FROM {$completions_table}
                 WHERE user_id = %d AND goal_id = %d
                 ORDER BY completed_at DESC
                 LIMIT 200", $user_id, $goal_id ), ARRAY_A ); $points_by_event_key = array(); $details_by_event_key = array(); if ( is_array( $completion_rows ) && ! empty( $completion_rows ) ) { $event_keys = array(); foreach ( $completion_rows as $r ) { $src = isset( $r['source'] ) ? strtolower( (string) $r['source'] ) : 'manual'; $ref = isset( $r['source_ref'] ) ? (string) $r['source_ref'] : ''; $event_keys[] = 'c_' . sha1( $src . ':' . $ref . ':' . (string) $goal_id ); } $event_keys = array_values( array_unique( array_filter( $event_keys ) ) ); if ( ! empty( $event_keys ) ) { $placeholders = implode( ',', array_fill( 0, count( $event_keys ), '%s' ) ); $sql = "SELECT event_key, points, details FROM {$ledger_table} WHERE user_id = %d AND event_key IN ({$placeholders})"; $prepared = $wpdb->prepare( $sql, array_merge( array( $user_id ), $event_keys ) ); $rows = $wpdb->get_results( $prepared, ARRAY_A ); if ( is_array( $rows ) ) { foreach ( $rows as $lr ) { $ek = isset( $lr['event_key'] ) ? (string) $lr['event_key'] : ''; if ( '' === $ek ) { continue; } $points_by_event_key[ $ek ] = isset( $lr['points'] ) ? (int) $lr['points'] : 0; $details_by_event_key[ $ek ] = isset( $lr['details'] ) ? (string) $lr['details'] : ''; } } } } $completions_out = array();
        if ( is_array( $completion_rows ) ) {
            foreach ( $completion_rows as $r ) {
                $when_utc = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : '';
                $when_utc = is_string( $when_utc ) ? trim( $when_utc ) : '';
                $when_local = '';
                if ( '' !== $when_utc && '0000-00-00 00:00:00' !== $when_utc ) {
                    $when_local = TCT_Utils::mysql_utc_to_tz( $when_utc, $tz, $fmt );
                }

                $src = isset( $r['source'] ) ? (string) $r['source'] : '';
                $src = $src ? strtolower( $src ) : '';
                $ref = isset( $r['source_ref'] ) ? (string) $r['source_ref'] : '';
                $event_key = 'c_' . sha1( $src . ':' . $ref . ':' . (string) $goal_id );

                $points = isset( $points_by_event_key[ $event_key ] ) ? (int) $points_by_event_key[ $event_key ] : 0;

                $details = isset( $details_by_event_key[ $event_key ] ) ? (string) $details_by_event_key[ $event_key ] : '';
                $details_trim = ltrim( (string) $details );

                // Make auto misses/fails understandable in history.
                $src_label = '';
                if ( '' !== $details_trim ) {
                    if ( 0 === stripos( $details_trim, '[manual fail]' ) ) {
                        $src_label = 'Manual fail';
                    } elseif ( 0 === stripos( $details_trim, '[auto miss]' ) ) {
                        $src_label = 'Auto miss';
                    } elseif ( 0 === stripos( $details_trim, '[auto due miss]' ) ) {
                        $src_label = 'Auto due miss';
                    }
                }

                if ( '' === $src_label ) {
                    if ( 'todoist' === $src ) {
                        $src_label = 'Todoist';
                    } elseif ( 'auto_due_miss' === $src ) {
                        $src_label = 'Auto due miss';
                    } elseif ( 'auto_miss' === $src ) {
                        $src_label = 'Auto miss';
                    } elseif ( 'wake_time' === $src ) {
                        $src_label = 'Wake-time';
                    } else {
                        $src_label = 'Manual';
                    }
                }

                // Allowed-fails polish: clarify free fails (0 pts) vs penalized fails.
                if ( 0 === (int) $points ) {
                    $is_failish = false;
                    if ( '' !== $details_trim ) {
                        if ( 0 === stripos( $details_trim, '[manual fail]' )
                            || 0 === stripos( $details_trim, '[auto miss]' )
                            || 0 === stripos( $details_trim, '[auto due miss]' ) ) {
                            $is_failish = true;
                        }
                    }
                    if ( ! $is_failish ) {
                        if ( 'auto_due_miss' === $src || 'auto_miss' === $src ) {
                            $is_failish = true;
                        }
                    }
                    if ( $is_failish && false === stripos( $src_label, 'free' ) ) {
                        $src_label .= ' (free)';
                    }
                }

                $completions_out[] = array(
                    'id'          => isset( $r['id'] ) ? (int) $r['id'] : 0,
                    'completedAt' => $when_local ? $when_local : $when_utc,
                    'source'      => $src,
                    'sourceLabel' => $src_label,
                    'points'      => $points,
                );
            }
        }
$total_points = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(points),0) FROM {$ledger_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $period_target = 0; $period_unit = 'week'; $period_span = 1; $intervals = $this->intervals_from_goal_row( $goal ); if ( is_array( $intervals ) && ! empty( $intervals ) ) { $primary = $intervals[0]; $period_target = isset( $primary['target'] ) ? (int) $primary['target'] : 0; $period_unit = isset( $primary['period_unit'] ) ? (string) $primary['period_unit'] : 'week'; $period_span = isset( $primary['period_span'] ) ? (int) $primary['period_span'] : 1; } else { $period_target = isset( $goal['target'] ) ? (int) $goal['target'] : 0; $period_unit = isset( $goal['period_unit'] ) ? (string) $goal['period_unit'] : 'week'; $period_span = isset( $goal['period_span'] ) ? (int) $goal['period_span'] : 1; } $period_span = max( 1, (int) $period_span ); $period_unit = sanitize_text_field( (string) $period_unit ); $period_unit = strtolower( $period_unit ); $period_label = $this->human_interval_label( $period_target, $period_unit, $period_span ); $goals_met = array(); $periods_to_show = 12; $goal_type = isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : 'positive'; $threshold = isset( $goal['threshold'] ) && is_numeric( $goal['threshold'] ) ? (int) $goal['threshold'] : null; $is_negative = TCT_Utils::is_negative_goal_type( $goal_type ); $is_no_interval_positive = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) : ( 'positive_no_int' === $goal_type ); if ( ! $is_negative && $is_no_interval_positive ) { $period_label = 'No interval target'; } if ( $period_target > 0 || $is_negative ) { $now_tz = new DateTimeImmutable( 'now', $tz ); $b0 = TCT_Interval::current_loop_bounds( $now_tz, $period_unit, $period_span ); $current_start = isset( $b0['start'] ) ? $b0['start'] : $now_tz; $current_end = isset( $b0['end'] ) ? $b0['end'] : $now_tz; $cursor_start = $current_start; for ( $i = 0; $i < $periods_to_show; $i++ ) { if ( 0 === $i ) { $start = $current_start; $end = $current_end; } else { $probe = $cursor_start->modify( '-1 second' ); $bb = TCT_Interval::current_loop_bounds( $probe, $period_unit, $period_span ); $start = isset( $bb['start'] ) ? $bb['start'] : $probe; $end = isset( $bb['end'] ) ? $bb['end'] : $cursor_start; } if ( ! ( $start instanceof DateTimeImmutable ) || ! ( $end instanceof DateTimeImmutable ) ) { break; } if ( $start >= $end ) { break; } $in_progress = 0; $window_end = $end; if ( $end > $now_tz ) { $in_progress = 1; $window_end = $now_tz; } $label = $this->format_period_range_label( $start, $end, $period_unit ); $start_utc = TCT_Utils::dt_to_mysql_utc( $start ); $end_utc = TCT_Utils::dt_to_mysql_utc( $window_end ); $cnt = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id=%d AND goal_id=%d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc, $end_utc ) ); if ( $is_negative ) { $met = TCT_Utils::is_negative_goal_met( $goal_type, $threshold, $cnt ) ? 1 : 0; } else { $met = ( $cnt >= $period_target ) ? 1 : 0; } $status = 'missed'; if ( $met ) { $status = 'met'; } elseif ( $in_progress ) { $status = 'sofar'; } $display_target = $period_target; if ( $is_negative ) { if ( TCT_Utils::is_never_goal( $goal_type, $threshold ) ) { $display_target = 0; } else { $display_target = ( null !== $threshold ) ? (int) $threshold : 0; } } $goals_met[] = array( 'label' => (string) $label, 'periodStart' => $start->format( 'Y-m-d H:i:s' ), 'periodEnd' => $end->format( 'Y-m-d H:i:s' ), 'count' => (int) $cnt, 'target' => (int) $display_target, 'met' => (int) $met, 'inProgress' => (int) $in_progress, 'status' => (string) $status, 'isNegative' => $is_negative ? 1 : 0, ); $cursor_start = $start; } }  $is_composite_parent = $this->tct_goal_is_composite_parent( $goal, $user_id ); $settlement_history = $is_composite_parent ? $this->tct_goal_history_settlement_rows( $user_id, $goal_id, $tz ) : array( 'rows' => array(), 'total' => 0 ); TCT_Utils::send_json_success( array( 'ok' => true, 'goalId' => (int) $goal_id, 'goalName' => sanitize_text_field( $goal_name ), 'goalType' => (string) $goal_type, 'threshold' => $threshold, 'timezoneLabel' => (string) $tz_label, 'totalCompletions' => (int) $total_completions, 'shownCompletions' => (int) count( $completions_out ), 'totalSettlements' => isset( $settlement_history['total'] ) ? (int) $settlement_history['total'] : 0, 'shownSettlements' => isset( $settlement_history['rows'] ) && is_array( $settlement_history['rows'] ) ? (int) count( $settlement_history['rows'] ) : 0, 'totalPoints' => (int) $total_points, 'completions' => $completions_out, 'settlements' => isset( $settlement_history['rows'] ) && is_array( $settlement_history['rows'] ) ? $settlement_history['rows'] : array(), 'isCompositeParent' => $is_composite_parent ? 1 : 0, 'periodTarget' => (int) $period_target, 'periodUnit' => (string) $period_unit, 'periodSpan' => (int) $period_span, 'periodLabel' => (string) $period_label, 'goalsMetPeriods' => (int) $periods_to_show, 'weeklyTarget' => (int) ( ( 'week' === $period_unit && 1 === (int) $period_span ) ? $period_target : 0 ), 'goalsMetWeeks' => (int) $periods_to_show, 'goalsMet' => $goals_met, ) ); }  

    public function handle_points_poll_ajax() {
        if ( ! is_user_logged_in() ) {
            TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 );
        }
        TCT_Utils::enforce_ajax_nonce( 'tct_points_poll', 'nonce' );
        $user_id = get_current_user_id();
        $balance = TCT_Ledger::get_balance( $user_id );
        $balance_label = number_format_i18n( (int) $balance );
        wp_send_json_success(
            array(
                'pointsBalance'      => (int) $balance,
                'pointsBalanceLabel' => $balance_label,
            )
        );
    }

    public function handle_ui_snapshot_ajax() {
        if ( ! is_user_logged_in() ) {
            TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 );
        }
        TCT_Utils::enforce_ajax_nonce( 'tct_ui_snapshot', 'nonce' );
        $user_id = get_current_user_id();

        $redirect_here = isset( $_POST['redirectHere'] ) ? esc_url_raw( wp_unslash( $_POST['redirectHere'] ) ) : '';
        if ( '' === $redirect_here ) {
            $ref = wp_get_referer();
            if ( is_string( $ref ) ) {
                $redirect_here = $ref;
            }
        }

        $nav_html = $this->render_reward_nav_pill( $user_id );
        if ( '' === $nav_html ) {
            $nav_html = $this->render_points_nav_pill( $user_id );
        }

        $dashboard_html = $this->render_kpi_tiles( $user_id );
        $ledger_html = $this->render_ledger_panel( $user_id, $redirect_here );

        $balance = TCT_Ledger::get_balance( $user_id );
        $balance_label = number_format_i18n( (int) $balance );

        wp_send_json_success(
            array(
                'pointsBalance'      => (int) $balance,
                'pointsBalanceLabel' => $balance_label,
                'navPillsHtml'       => $nav_html,
                'dashboardHtml'      => $dashboard_html,
                'ledgerHtml'         => $ledger_html,
            )
        );
    }

public function handle_archived_goals_search_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_archived_goals_search', 'nonce' ); $user_id = get_current_user_id(); $q = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; $q = trim( (string) $q ); global $wpdb; $goals_table = TCT_DB::table_goals(); if ( ! $goals_table ) { TCT_Utils::send_json_success( array( 'results' => array(), ) ); } if ( '' === $q ) { $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, goal_name, label_name, updated_at
                     FROM {$goals_table}
                     WHERE user_id = %d
                       AND is_tracked = 0
                     ORDER BY
                       CASE
                         WHEN goal_name IS NULL OR goal_name = '' THEN label_name
                         ELSE goal_name
                       END ASC,
                       id ASC", $user_id ), ARRAY_A ); } else { $like = '%' . $wpdb->esc_like( $q ) . '%'; $aliases_where = $this->tct_goal_aliases_column_exists() ? 'aliases_json' : "''"; $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, goal_name, label_name, updated_at
                     FROM {$goals_table}
                     WHERE user_id = %d
                       AND is_tracked = 0
                       AND (goal_name LIKE %s OR label_name LIKE %s OR {$aliases_where} LIKE %s)
                     ORDER BY updated_at DESC, id DESC
                     LIMIT 20", $user_id, $like, $like, $like ), ARRAY_A ); } if ( ! is_array( $rows ) ) { $rows = array(); } $results = array(); foreach ( $rows as $r ) { $gid = isset( $r['id'] ) ? (int) $r['id'] : 0; if ( $gid <= 0 ) { continue; } $gn = isset( $r['goal_name'] ) ? (string) $r['goal_name'] : ''; $ln = isset( $r['label_name'] ) ? (string) $r['label_name'] : ''; if ( '' === $gn ) { $gn = '' !== $ln ? $ln : 'Goal'; } $updated = isset( $r['updated_at'] ) ? (string) $r['updated_at'] : ''; $updated_display = '--'; if ( '' !== $updated && '0000-00-00 00:00:00' !== $updated ) { $ts = strtotime( $updated . ' UTC' ); if ( false !== $ts ) { $updated_display = date_i18n( 'Y-m-d H:i', $ts ); } else { $updated_display = $updated; } } $results[] = array( 'id' => (int) $gid, 'goal_name' => sanitize_text_field( $gn ), 'label_name' => sanitize_text_field( $ln ), 'updated_display' => (string) $updated_display, ); } TCT_Utils::send_json_success( array( 'results' => $results, ) ); } public function handle_suggest_aliases_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_suggest_aliases', 'nonce' ); $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : ''; $title = trim( (string) $title ); if ( '' === $title ) { TCT_Utils::send_json_success( array( 'aliases' => array(), ) ); } $api_key = get_option( 'tct_openai_api_key', '' ); $api_key = is_string( $api_key ) ? trim( (string) $api_key ) : ''; if ( '' === $api_key ) { TCT_Utils::send_json_error( array( 'message' => 'OpenAI API key is not set. Add it in Settings first.', ), 400 ); } $model = apply_filters( 'tct_openai_alias_model', 'gpt-4o-mini' ); $system = 'You generate short alternative aliases for a personal task or habit title to improve fuzzy search matching. Return ONLY valid JSON. No markdown.'; $user = 'Task title: "' . $title . '"\n\nReturn 6-10 useful aliases as a JSON array of strings. Include common tense variations (e.g. run/ran/running), minor spelling variants, and short phrasing alternatives. Keep meaning the same. No numbering, no extra keys, no commentary.'; $body = array( 'model' => $model, 'messages' => array( array( 'role' => 'system', 'content' => $system, ), array( 'role' => 'user', 'content' => $user, ), ), 'temperature' => 0.4, 'max_tokens' => 350, ); $args = array( 'headers' => array( 'Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json', ), 'timeout' => 20, 'body' => wp_json_encode( $body ), ); $resp = wp_remote_post( 'https://api.openai.com/v1/chat/completions', $args ); if ( is_wp_error( $resp ) ) { TCT_Utils::send_json_error( array( 'message' => $resp->get_error_message(), ), 500 ); } $status = (int) wp_remote_retrieve_response_code( $resp ); $raw = (string) wp_remote_retrieve_body( $resp ); if ( $status < 200 || $status >= 300 ) { $msg = 'OpenAI API error.'; $decoded_err = json_decode( $raw, true ); if ( is_array( $decoded_err ) && isset( $decoded_err['error'] ) && is_array( $decoded_err['error'] ) && isset( $decoded_err['error']['message'] ) ) { $msg = sanitize_text_field( (string) $decoded_err['error']['message'] ); } TCT_Utils::send_json_error( array( 'message' => $msg, 'status' => $status, ), 500 ); } $decoded = json_decode( $raw, true ); $content = ''; if ( is_array( $decoded ) && isset( $decoded['choices'][0]['message']['content'] ) ) { $content = (string) $decoded['choices'][0]['message']['content']; } $content = trim( (string) $content ); if ( '' === $content ) { TCT_Utils::send_json_success( array( 'aliases' => array(), ) ); } if ( 0 === strpos( $content, '```' ) ) { $content = preg_replace( '/^```[a-zA-Z0-9_-]*\s*/', '', $content ); $content = preg_replace( '/```\s*$/', '', $content ); $content = trim( (string) $content ); } $parsed = json_decode( $content, true ); if ( ! is_array( $parsed ) ) { if ( preg_match( '/\[[\s\S]*\]/', $content, $m ) ) { $parsed = json_decode( $m[0], true ); } } if ( is_array( $parsed ) && isset( $parsed['aliases'] ) && is_array( $parsed['aliases'] ) ) { $parsed = $parsed['aliases']; } $aliases_out = array(); $seen = array(); if ( is_array( $parsed ) ) { foreach ( $parsed as $a ) { if ( ! is_string( $a ) ) { continue; } $a = trim( sanitize_text_field( $a ) ); if ( '' === $a ) { continue; } if ( strlen( $a ) > 120 ) { $a = substr( $a, 0, 120 ); } $k = strtolower( $a ); if ( $k === strtolower( $title ) ) { continue; } if ( isset( $seen[ $k ] ) ) { continue; } $seen[ $k ] = true; $aliases_out[] = $a; if ( count( $aliases_out ) >= 12 ) { break; } } } TCT_Utils::send_json_success( array( 'aliases' => $aliases_out, ) ); } public function handle_goal_heatmap_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_goal_heatmap', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.', ), 400 ); } $view = isset( $_POST['view'] ) ? sanitize_text_field( wp_unslash( $_POST['view'] ) ) : 'year'; $view = is_string( $view ) ? strtolower( trim( $view ) ) : 'year'; if ( ! in_array( $view, array( 'week', 'month', 'year' ), true ) ) { $view = 'year'; } $cursor = isset( $_POST['cursor'] ) ? sanitize_text_field( wp_unslash( $_POST['cursor'] ) ) : ''; $cursor = is_string( $cursor ) ? trim( $cursor ) : ''; if ( '' !== $cursor && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $cursor ) ) { $cursor = ''; } $year_param = isset( $_POST['year'] ) ? (int) wp_unslash( $_POST['year'] ) : 0; $month_param = isset( $_POST['month'] ) ? (int) wp_unslash( $_POST['month'] ) : 0; $week_start = isset( $_POST['week_start'] ) ? sanitize_text_field( wp_unslash( $_POST['week_start'] ) ) : ''; $week_start = is_string( $week_start ) ? trim( $week_start ) : ''; if ( '' !== $week_start && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $week_start ) ) { $week_start = ''; } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $max_year = (int) $now_tz->format( 'Y' ); $current_month = (int) $now_tz->format( 'm' ); global $wpdb; $goals_table = TCT_DB::table_goals(); $exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$goals_table} WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); if ( $exists <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.', ), 404 ); } $cursor_dt = null; if ( '' !== $cursor ) { try { $cursor_dt = new DateTimeImmutable( $cursor . ' 00:00:00', $tz ); } catch ( Exception $e ) { $cursor_dt = null; } } $start_local = null; $end_local = null; $year = 0; $month = 0; if ( 'week' === $view ) { $anchor = $cursor_dt; if ( null === $anchor && '' !== $week_start ) { try { $anchor = new DateTimeImmutable( $week_start . ' 00:00:00', $tz ); } catch ( Exception $e ) { $anchor = null; } } if ( null === $anchor ) { $anchor = $now_tz; } if ( $anchor > $now_tz ) { $anchor = $now_tz; } $anchor = $anchor->setTime( 0, 0, 0 ); $start_of_week = (int) get_option( 'start_of_week', 1 ); if ( $start_of_week < 0 || $start_of_week > 6 ) { $start_of_week = 1; } $dow = (int) $anchor->format( 'w' ); $diff = ( $dow - $start_of_week + 7 ) % 7; if ( $diff > 0 ) { $start_local = $anchor->sub( new DateInterval( 'P' . (int) $diff . 'D' ) ); } else { $start_local = $anchor; } $end_local = $start_local->add( new DateInterval( 'P7D' ) ); $year = (int) $start_local->format( 'Y' ); $month = (int) $start_local->format( 'm' ); } elseif ( 'month' === $view ) { if ( null !== $cursor_dt ) { $year = (int) $cursor_dt->format( 'Y' ); $month = (int) $cursor_dt->format( 'm' ); } else { $year = (int) $year_param; $month = (int) $month_param; } if ( $year < 1970 || $year > $max_year ) { $year = $max_year; } if ( $month < 1 || $month > 12 ) { $month = $current_month; } if ( $year === $max_year && $month > $current_month ) { $month = $current_month; } $start_local = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', (int) $year, (int) $month ), $tz ); $end_local = $start_local->add( new DateInterval( 'P1M' ) ); } else { if ( null !== $cursor_dt ) { $year = (int) $cursor_dt->format( 'Y' ); } else { $year = (int) $year_param; } if ( $year < 1970 || $year > $max_year ) { $year = $max_year; } $start_local = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', (int) $year ), $tz ); $end_local = $start_local->add( new DateInterval( 'P1Y' ) ); $month = 1; } if ( ! ( $start_local instanceof DateTimeImmutable ) || ! ( $end_local instanceof DateTimeImmutable ) ) { TCT_Utils::send_json_error( array( 'message' => 'Could not resolve period.', ), 400 ); } $start_utc = TCT_Utils::dt_to_mysql_utc( $start_local ); $end_utc = TCT_Utils::dt_to_mysql_utc( $end_local ); $completions_table = TCT_DB::table_completions(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT completed_at
                 FROM {$completions_table}
                 WHERE user_id = %d AND goal_id = %d
                   AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc, $end_utc ), ARRAY_A ); $date_counts = array(); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { $when_utc = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; $when_utc = is_string( $when_utc ) ? trim( $when_utc ) : ''; if ( '' === $when_utc || '0000-00-00 00:00:00' === $when_utc ) { continue; } $day = TCT_Utils::mysql_utc_to_tz( $when_utc, $tz, 'Y-m-d' ); if ( ! is_string( $day ) || '' === $day ) { continue; } if ( ! isset( $date_counts[ $day ] ) ) { $date_counts[ $day ] = 0; } $date_counts[ $day ]++; } } $min_raw = (string) $wpdb->get_var( $wpdb->prepare( "SELECT MIN(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $min_raw = is_string( $min_raw ) ? trim( $min_raw ) : ''; $min_year = $year; $min_day = ''; if ( '' !== $min_raw && '0000-00-00 00:00:00' !== $min_raw ) { $min_day = (string) TCT_Utils::mysql_utc_to_tz( $min_raw, $tz, 'Y-m-d' ); $maybe_year = (int) TCT_Utils::mysql_utc_to_tz( $min_raw, $tz, 'Y' ); if ( $maybe_year >= 1970 && $maybe_year <= $max_year ) { $min_year = $maybe_year; } } $min_start = ''; $max_start = ''; if ( 'month' === $view ) { $max_start = $now_tz->modify( 'first day of this month' )->setTime( 0, 0, 0 )->format( 'Y-m-d' ); if ( '' !== $min_day ) { try { $min_dt = new DateTimeImmutable( $min_day . ' 00:00:00', $tz ); $min_start = $min_dt->modify( 'first day of this month' )->setTime( 0, 0, 0 )->format( 'Y-m-d' ); } catch ( Exception $e ) { $min_start = $start_local->format( 'Y-m-d' ); } } else { $min_start = $start_local->format( 'Y-m-d' ); } } elseif ( 'week' === $view ) { $start_of_week = (int) get_option( 'start_of_week', 1 ); if ( $start_of_week < 0 || $start_of_week > 6 ) { $start_of_week = 1; } $anchor_now = $now_tz->setTime( 0, 0, 0 ); $dow_now = (int) $anchor_now->format( 'w' ); $diff_now = ( $dow_now - $start_of_week + 7 ) % 7; if ( $diff_now > 0 ) { $max_start = $anchor_now->sub( new DateInterval( 'P' . (int) $diff_now . 'D' ) )->format( 'Y-m-d' ); } else { $max_start = $anchor_now->format( 'Y-m-d' ); } if ( '' !== $min_day ) { try { $anchor_min = new DateTimeImmutable( $min_day . ' 00:00:00', $tz ); $anchor_min = $anchor_min->setTime( 0, 0, 0 ); $dow_min = (int) $anchor_min->format( 'w' ); $diff_min = ( $dow_min - $start_of_week + 7 ) % 7; if ( $diff_min > 0 ) { $min_start = $anchor_min->sub( new DateInterval( 'P' . (int) $diff_min . 'D' ) )->format( 'Y-m-d' ); } else { $min_start = $anchor_min->format( 'Y-m-d' ); } } catch ( Exception $e ) { $min_start = $start_local->format( 'Y-m-d' ); } } else { $min_start = $start_local->format( 'Y-m-d' ); } } else { $max_start = sprintf( '%04d-01-01', (int) $max_year ); $min_start = sprintf( '%04d-01-01', (int) $min_year ); } TCT_Utils::send_json_success( array( 'ok' => true, 'goalId' => (int) $goal_id, 'view' => (string) $view, 'year' => (int) $year, 'month' => (int) $month, 'periodStart' => $start_local->format( 'Y-m-d' ), 'periodEnd' => $end_local->format( 'Y-m-d' ), 'minStart' => (string) $min_start, 'maxStart' => (string) $max_start, 'minYear' => (int) $min_year, 'maxYear' => (int) $max_year, 'dates' => $date_counts, ) ); } public function handle_domain_heatmap_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_domain_heatmap', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id < 0 ) { $domain_id = 0; } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $max_year = (int) $now_tz->format( 'Y' ); global $wpdb; $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $minmax = $wpdb->get_row( $wpdb->prepare( "SELECT MIN(c.completed_at) AS min_dt, MAX(c.completed_at) AS max_dt
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $domain_id ), ARRAY_A ); $min_year = $max_year; $max_year_found = $max_year; if ( is_array( $minmax ) ) { $min_raw = isset( $minmax['min_dt'] ) ? (string) $minmax['min_dt'] : ''; $max_raw = isset( $minmax['max_dt'] ) ? (string) $minmax['max_dt'] : ''; $min_raw = is_string( $min_raw ) ? trim( $min_raw ) : ''; $max_raw = is_string( $max_raw ) ? trim( $max_raw ) : ''; if ( '' !== $min_raw && '0000-00-00 00:00:00' !== $min_raw ) { $maybe = (int) TCT_Utils::mysql_utc_to_tz( $min_raw, $tz, 'Y' ); if ( $maybe >= 1970 && $maybe <= $max_year ) { $min_year = $maybe; } } if ( '' !== $max_raw && '0000-00-00 00:00:00' !== $max_raw ) { $maybe = (int) TCT_Utils::mysql_utc_to_tz( $max_raw, $tz, 'Y' ); if ( $maybe >= 1970 && $maybe <= $max_year ) { $max_year_found = $maybe; } } } if ( $max_year_found < $min_year ) { $max_year_found = $min_year; } $start_local = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', (int) $min_year ), $tz ); $end_local = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', (int) ( $max_year_found + 1 ) ), $tz ); $start_utc = TCT_Utils::dt_to_mysql_utc( $start_local ); $end_utc = TCT_Utils::dt_to_mysql_utc( $end_local ); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.points_per_completion, g.role_id, COALESCE(NULLIF(r.role_name,''),'') AS role_name
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at < %s
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $start_utc, $end_utc, $domain_id ), ARRAY_A ); $domain_points_by_year = array(); $role_points_by_year = array(); $roles_index = array(); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { $when_utc = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; $when_utc = is_string( $when_utc ) ? trim( $when_utc ) : ''; if ( '' === $when_utc || '0000-00-00 00:00:00' === $when_utc ) { continue; } $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $day = TCT_Utils::mysql_utc_to_tz( $when_utc, $tz, 'Y-m-d' ); if ( ! is_string( $day ) || '' === $day ) { continue; } $yr = (int) substr( $day, 0, 4 ); if ( $yr < 1970 || $yr > $max_year ) { continue; } if ( ! isset( $domain_points_by_year[ $yr ] ) ) { $domain_points_by_year[ $yr ] = array(); } if ( ! isset( $domain_points_by_year[ $yr ][ $day ] ) ) { $domain_points_by_year[ $yr ][ $day ] = 0; } $domain_points_by_year[ $yr ][ $day ] += $ppc; $role_id = isset( $r['role_id'] ) ? (int) $r['role_id'] : 0; if ( $role_id > 0 ) { if ( ! isset( $role_points_by_year[ $role_id ] ) ) { $role_points_by_year[ $role_id ] = array(); } if ( ! isset( $role_points_by_year[ $role_id ][ $yr ] ) ) { $role_points_by_year[ $role_id ][ $yr ] = array(); } if ( ! isset( $role_points_by_year[ $role_id ][ $yr ][ $day ] ) ) { $role_points_by_year[ $role_id ][ $yr ][ $day ] = 0; } $role_points_by_year[ $role_id ][ $yr ][ $day ] += $ppc; if ( ! isset( $roles_index[ $role_id ] ) ) { $rn = isset( $r['role_name'] ) ? (string) $r['role_name'] : ''; $rn = is_string( $rn ) ? trim( $rn ) : ''; $roles_index[ $role_id ] = $rn; } } } } $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT g.id, g.role_id, g.is_tracked, g.points_per_completion, g.target, g.period_unit, g.period_mode, g.intervals_json, g.points_enabled_at,
                        COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) AS tct_effective_domain_id
                 FROM {$goals_table} g
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE g.user_id = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'anki_cards')
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d", $user_id, $domain_id ), ARRAY_A ); if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $g ) { $rid = isset( $g['role_id'] ) ? (int) $g['role_id'] : 0; if ( $rid > 0 && ! isset( $roles_index[ $rid ] ) ) { $maybe_name = $wpdb->get_var( $wpdb->prepare( "SELECT role_name FROM {$roles_table} WHERE user_id = %d AND id = %d", $user_id, $rid ) ); $maybe_name = is_string( $maybe_name ) ? trim( $maybe_name ) : ''; $roles_index[ $rid ] = $maybe_name; } } } $roles_out = array(); foreach ( $roles_index as $rid => $rname ) { $roles_out[] = array( 'id' => (int) $rid, 'name' => (string) $rname, ); } $years_out = array(); for ( $y = (int) $max_year_found; $y >= (int) $min_year; $y-- ) { $possible = $this->tct_compute_domain_and_role_possible_points_for_year( (int) $y, $tz, $goal_rows ); $domain_possible_map = isset( $possible['domain'] ) && is_array( $possible['domain'] ) ? $possible['domain'] : array(); $role_possible_maps = isset( $possible['roles'] ) && is_array( $possible['roles'] ) ? $possible['roles'] : array(); $domain_pts_map = isset( $domain_points_by_year[ $y ] ) && is_array( $domain_points_by_year[ $y ] ) ? $domain_points_by_year[ $y ] : array(); $domain_pcts_out = array(); $domain_points_out = array(); foreach ( $domain_pts_map as $day => $pts ) { $pts = (int) $pts; if ( $pts <= 0 ) { continue; } $possible_pts = isset( $domain_possible_map[ $day ] ) ? (float) $domain_possible_map[ $day ] : 0.0; if ( $possible_pts > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible_pts ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = 100; } if ( $pct <= 0 ) { $pct = 1; } $domain_pcts_out[ $day ] = (int) $pct; $domain_points_out[ $day ] = (int) $pts; } $roles_year_out = array(); foreach ( $roles_out as $ro ) { $rid = isset( $ro['id'] ) ? (int) $ro['id'] : 0; if ( $rid <= 0 ) { continue; } $r_pts_map = ( isset( $role_points_by_year[ $rid ] ) && isset( $role_points_by_year[ $rid ][ $y ] ) && is_array( $role_points_by_year[ $rid ][ $y ] ) ) ? $role_points_by_year[ $rid ][ $y ] : array(); $r_possible_map = isset( $role_possible_maps[ $rid ] ) && is_array( $role_possible_maps[ $rid ] ) ? $role_possible_maps[ $rid ] : array(); $r_pcts_out = array(); $r_points_out = array(); foreach ( $r_pts_map as $day => $pts ) { $pts = (int) $pts; if ( $pts <= 0 ) { continue; } $possible_pts = isset( $r_possible_map[ $day ] ) ? (float) $r_possible_map[ $day ] : 0.0; if ( $possible_pts > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible_pts ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = 100; } if ( $pct <= 0 ) { $pct = 1; } $r_pcts_out[ $day ] = (int) $pct; $r_points_out[ $day ] = (int) $pts; } $roles_year_out[ (string) $rid ] = array( 'pcts' => $r_pcts_out, 'points' => $r_points_out, 'possible' => $r_possible_map, ); } $years_out[] = array( 'year' => (int) $y, 'pcts' => $domain_pcts_out, 'points' => $domain_points_out, 'possible' => $domain_possible_map, 'roles' => $roles_year_out, ); } TCT_Utils::send_json_success( array( 'ok' => true, 'domainId' => (int) $domain_id, 'minYear' => (int) $min_year, 'maxYear' => (int) $max_year_found, 'roles' => $roles_out, 'years' => $years_out, ) ); } public function handle_domain_yearbar_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_domain_yearbar', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id < 0 ) { $domain_id = 0; } $year = isset( $_POST['year'] ) ? (int) wp_unslash( $_POST['year'] ) : 0; $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); if ( $year < 1970 ) { $year = (int) $now_tz->format( 'Y' ); } $year_start_tz = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', $year ), $tz ); $year_end_tz = $year_start_tz->add( new DateInterval( 'P1Y' ) ); $year_start_utc = TCT_Utils::dt_to_mysql_utc( $year_start_tz ); $year_end_utc = TCT_Utils::dt_to_mysql_utc( $year_end_tz ); $year_dates = array(); $year_period = new DatePeriod( $year_start_tz, new DateInterval( 'P1D' ), $year_end_tz ); foreach ( $year_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $year_dates[] = $dt->format( 'Y-m-d' ); } } global $wpdb; $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, role_id, goal_name, is_tracked, points_per_completion, target, period_unit, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, points_enabled_at, domain_id
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1", $user_id ), ARRAY_A ); $role_map = $this->get_role_map( $this->get_roles( $user_id ) ); $points_by_domain = $this->get_domain_points_by_day_for_window( $user_id, $year_start_utc, $year_end_utc, $tz ); $possible_by_domain = $this->compute_domain_possible_points_by_day_for_year( $year, $tz, $goal_rows, $role_map ); $domain_name = ''; $domains = $this->get_domains( $user_id ); foreach ( $domains as $d ) { if ( isset( $d['id'] ) && (int) $d['id'] === $domain_id ) { $domain_name = isset( $d['domain_name'] ) ? (string) $d['domain_name'] : ''; break; } } if ( 0 === $domain_id && '' === $domain_name ) { $domain_name = 'Goals'; } $domain_day_points = isset( $points_by_domain[ $domain_id ] ) && is_array( $points_by_domain[ $domain_id ] ) ? $points_by_domain[ $domain_id ] : array(); $domain_day_possible = isset( $possible_by_domain[ $domain_id ] ) && is_array( $possible_by_domain[ $domain_id ] ) ? $possible_by_domain[ $domain_id ] : array(); $html = $this->render_domain_year_spectrum_bar( $domain_id, $domain_name, $year, $year_dates, $domain_day_points, $domain_day_possible ); TCT_Utils::send_json_success( array( 'ok' => true, 'html' => (string) $html, ) ); } public function handle_domain_monthbar_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_domain_monthbar', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id < 0 ) { $domain_id = 0; } $year = isset( $_POST['year'] ) ? (int) wp_unslash( $_POST['year'] ) : 0; $month = isset( $_POST['month'] ) ? (int) wp_unslash( $_POST['month'] ) : 0; $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); if ( $year < 1970 ) { $year = (int) $now_tz->format( 'Y' ); } if ( $month < 1 || $month > 12 ) { $month = (int) $now_tz->format( 'n' ); } $month_start_tz = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', $year, $month ), $tz ); $month_end_tz = $month_start_tz->add( new DateInterval( 'P1M' ) ); $month_start_utc = TCT_Utils::dt_to_mysql_utc( $month_start_tz ); $month_end_utc = TCT_Utils::dt_to_mysql_utc( $month_end_tz ); $month_dates = array(); $month_period = new DatePeriod( $month_start_tz, new DateInterval( 'P1D' ), $month_end_tz ); foreach ( $month_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $month_dates[] = $dt->format( 'Y-m-d' ); } } global $wpdb; $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, role_id, goal_name, is_tracked, points_per_completion, target, period_unit, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, points_enabled_at, domain_id
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1", $user_id ), ARRAY_A ); $role_map = $this->get_role_map( $this->get_roles( $user_id ) ); $points_by_domain = $this->get_domain_points_by_day_for_window( $user_id, $month_start_utc, $month_end_utc, $tz ); $possible_by_domain_year = $this->compute_domain_possible_points_by_day_for_year( $year, $tz, $goal_rows, $role_map ); $domain_name = ''; $domains = $this->get_domains( $user_id ); foreach ( $domains as $d ) { if ( isset( $d['id'] ) && (int) $d['id'] === $domain_id ) { $domain_name = isset( $d['domain_name'] ) ? (string) $d['domain_name'] : ''; break; } } if ( 0 === $domain_id && '' === $domain_name ) { $domain_name = 'Goals'; } $domain_day_points = isset( $points_by_domain[ $domain_id ] ) && is_array( $points_by_domain[ $domain_id ] ) ? $points_by_domain[ $domain_id ] : array(); $domain_possible_year = isset( $possible_by_domain_year[ $domain_id ] ) && is_array( $possible_by_domain_year[ $domain_id ] ) ? $possible_by_domain_year[ $domain_id ] : array(); $domain_day_possible = array(); foreach ( $month_dates as $dk ) { if ( isset( $domain_possible_year[ $dk ] ) ) { $domain_day_possible[ $dk ] = $domain_possible_year[ $dk ]; } } $html = $this->render_domain_month_spectrum_bar( $domain_id, $domain_name, $year, $month, $month_dates, $domain_day_points, $domain_day_possible ); TCT_Utils::send_json_success( array( 'ok' => true, 'html' => (string) $html, ) ); } public function handle_domain_weekbar_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_domain_weekbar', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id < 0 ) { $domain_id = 0; } $week_starts_on = isset( $_POST['week_starts_on'] ) ? (int) wp_unslash( $_POST['week_starts_on'] ) : -1; if ( 0 !== $week_starts_on && 1 !== $week_starts_on ) { $wp_sow = (int) get_option( 'start_of_week', 1 ); $week_starts_on = ( 0 === $wp_sow ) ? 0 : 1; } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $today_tz = $now_tz->setTime( 0, 0, 0 ); $today_dow = (int) $today_tz->format( 'w' ); $start_dow = ( 0 === $week_starts_on ) ? 0 : 1; $offset_days = ( $today_dow - $start_dow + 7 ) % 7; $week_start_tz = $today_tz->sub( new DateInterval( 'P' . $offset_days . 'D' ) ); $week_end_tz = $week_start_tz->add( new DateInterval( 'P7D' ) ); $week_start_utc = TCT_Utils::dt_to_mysql_utc( $week_start_tz ); $week_end_utc = TCT_Utils::dt_to_mysql_utc( $week_end_tz ); $week_dates = array(); $week_period = new DatePeriod( $week_start_tz, new DateInterval( 'P1D' ), $week_end_tz ); foreach ( $week_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $week_dates[] = $dt->format( 'Y-m-d' ); } } $years_needed = array(); foreach ( $week_dates as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); if ( $y > 0 ) { $years_needed[ $y ] = true; } } if ( empty( $years_needed ) ) { $years_needed[ (int) $now_tz->format( 'Y' ) ] = true; } global $wpdb; $goals_table = TCT_DB::table_goals(); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, role_id, goal_name, is_tracked, points_per_completion, target, period_unit, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, points_enabled_at, domain_id
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1", $user_id ), ARRAY_A ); $role_map = $this->get_role_map( $this->get_roles( $user_id ) ); $points_by_domain = $this->get_domain_points_by_day_for_window( $user_id, $week_start_utc, $week_end_utc, $tz ); $possible_by_year = array(); foreach ( array_keys( $years_needed ) as $y ) { $y = (int) $y; if ( $y < 1970 ) { continue; } $possible_by_year[ $y ] = $this->compute_domain_possible_points_by_day_for_year( $y, $tz, $goal_rows, $role_map ); } $domain_name = ''; $domains = $this->get_domains( $user_id ); foreach ( $domains as $d ) { if ( isset( $d['id'] ) && (int) $d['id'] === $domain_id ) { $domain_name = isset( $d['domain_name'] ) ? (string) $d['domain_name'] : ''; break; } } if ( 0 === $domain_id && '' === $domain_name ) { $domain_name = 'Goals'; } $domain_day_points = isset( $points_by_domain[ $domain_id ] ) && is_array( $points_by_domain[ $domain_id ] ) ? $points_by_domain[ $domain_id ] : array(); $domain_day_possible = array(); foreach ( $week_dates as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); if ( isset( $possible_by_year[ $y ] ) && isset( $possible_by_year[ $y ][ $domain_id ] ) && isset( $possible_by_year[ $y ][ $domain_id ][ $dk ] ) ) { $domain_day_possible[ $dk ] = $possible_by_year[ $y ][ $domain_id ][ $dk ]; } } $cur_year = (int) $now_tz->format( 'Y' ); $cur_month = (int) $now_tz->format( 'n' ); $html = $this->render_domain_week_spectrum_bar( $domain_id, $domain_name, $week_starts_on, $week_dates, $domain_day_points, $domain_day_possible, $cur_year, $cur_month ); TCT_Utils::send_json_success( array( 'ok' => true, 'html' => (string) $html, ) ); } public function handle_domain_month_heatmap_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_domain_month_heatmap', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id < 0 ) { $domain_id = 0; } $year = isset( $_POST['year'] ) ? (int) wp_unslash( $_POST['year'] ) : 0; $month = isset( $_POST['month'] ) ? (int) wp_unslash( $_POST['month'] ) : 0; $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); if ( $year < 1970 ) { $year = (int) $now_tz->format( 'Y' ); } if ( $month < 1 || $month > 12 ) { $month = (int) $now_tz->format( 'n' ); } $month_start_tz = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', $year, $month ), $tz ); $month_end_tz = $month_start_tz->add( new DateInterval( 'P1M' ) ); $month_start_utc = TCT_Utils::dt_to_mysql_utc( $month_start_tz ); $month_end_utc = TCT_Utils::dt_to_mysql_utc( $month_end_tz ); $date_keys = array(); $period = new DatePeriod( $month_start_tz, new DateInterval( 'P1D' ), $month_end_tz ); foreach ( $period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $date_keys[] = $dt->format( 'Y-m-d' ); } } global $wpdb; $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.points_per_completion, g.role_id, COALESCE(NULLIF(r.role_name,''),'') AS role_name
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at < %s
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $month_start_utc, $month_end_utc, $domain_id ), ARRAY_A ); $domain_points = array(); $role_points = array(); $roles_index = array(); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { $when_utc = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; $when_utc = is_string( $when_utc ) ? trim( $when_utc ) : ''; if ( '' === $when_utc || '0000-00-00 00:00:00' === $when_utc ) { continue; } $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $day = TCT_Utils::mysql_utc_to_tz( $when_utc, $tz, 'Y-m-d' ); if ( ! is_string( $day ) || '' === $day ) { continue; } if ( ! isset( $domain_points[ $day ] ) ) { $domain_points[ $day ] = 0; } $domain_points[ $day ] += $ppc; $rid = isset( $r['role_id'] ) ? (int) $r['role_id'] : 0; if ( $rid > 0 ) { if ( ! isset( $role_points[ $rid ] ) ) { $role_points[ $rid ] = array(); } if ( ! isset( $role_points[ $rid ][ $day ] ) ) { $role_points[ $rid ][ $day ] = 0; } $role_points[ $rid ][ $day ] += $ppc; if ( ! isset( $roles_index[ $rid ] ) ) { $rn = isset( $r['role_name'] ) ? (string) $r['role_name'] : ''; $rn = is_string( $rn ) ? trim( $rn ) : ''; $roles_index[ $rid ] = $rn; } } } } $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT g.id, g.role_id, g.is_tracked, g.points_per_completion, g.target, g.period_unit, g.period_mode, g.intervals_json, g.points_enabled_at,
                        COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) AS tct_effective_domain_id
                 FROM {$goals_table} g
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE g.user_id = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'anki_cards')
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d", $user_id, $domain_id ), ARRAY_A ); if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $g ) { $rid = isset( $g['role_id'] ) ? (int) $g['role_id'] : 0; if ( $rid > 0 && ! isset( $roles_index[ $rid ] ) ) { $maybe_name = $wpdb->get_var( $wpdb->prepare( "SELECT role_name FROM {$roles_table} WHERE user_id = %d AND id = %d", $user_id, $rid ) ); $maybe_name = is_string( $maybe_name ) ? trim( $maybe_name ) : ''; $roles_index[ $rid ] = $maybe_name; } } } $roles_out = array(); foreach ( $roles_index as $rid => $rname ) { $roles_out[] = array( 'id' => (int) $rid, 'name' => (string) $rname, ); } $possible_year = $this->tct_compute_domain_and_role_possible_points_for_year( $year, $tz, $goal_rows ); $domain_possible_year = isset( $possible_year['domain'] ) && is_array( $possible_year['domain'] ) ? $possible_year['domain'] : array(); $role_possible_year = isset( $possible_year['roles'] ) && is_array( $possible_year['roles'] ) ? $possible_year['roles'] : array(); $domain_possible = array(); foreach ( $date_keys as $dk ) { $domain_possible[ $dk ] = isset( $domain_possible_year[ $dk ] ) ? (float) $domain_possible_year[ $dk ] : 0.0; } $roles_payload = array(); foreach ( $roles_out as $ro ) { $rid = isset( $ro['id'] ) ? (int) $ro['id'] : 0; if ( $rid <= 0 ) { continue; } $r_possible_year = isset( $role_possible_year[ $rid ] ) && is_array( $role_possible_year[ $rid ] ) ? $role_possible_year[ $rid ] : array(); $r_possible = array(); foreach ( $date_keys as $dk ) { $r_possible[ $dk ] = isset( $r_possible_year[ $dk ] ) ? (float) $r_possible_year[ $dk ] : 0.0; } $r_points = isset( $role_points[ $rid ] ) && is_array( $role_points[ $rid ] ) ? $role_points[ $rid ] : array(); $roles_payload[ (string) $rid ] = array( 'points' => $r_points, 'possible' => $r_possible, ); } TCT_Utils::send_json_success( array( 'ok' => true, 'domainId' => (int) $domain_id, 'year' => (int) $year, 'month' => (int) $month, 'dates' => $date_keys, 'roles' => $roles_out, 'domain' => array( 'points' => $domain_points, 'possible' => $domain_possible, ), 'rolesData' => $roles_payload, ) ); } public function handle_domain_week_heatmap_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_domain_week_heatmap', 'nonce' ); $user_id = get_current_user_id(); $domain_id = isset( $_POST['domain_id'] ) ? (int) wp_unslash( $_POST['domain_id'] ) : 0; if ( $domain_id < 0 ) { $domain_id = 0; } $week_start = isset( $_POST['week_start'] ) ? sanitize_text_field( wp_unslash( $_POST['week_start'] ) ) : ''; $week_starts_on = isset( $_POST['week_starts_on'] ) ? (int) wp_unslash( $_POST['week_starts_on'] ) : (int) get_option( 'start_of_week', 1 ); $week_starts_on = ( 0 === (int) $week_starts_on ) ? 0 : 1; $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $week_start_tz = null; if ( is_string( $week_start ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $week_start ) ) { $maybe = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $week_start . ' 00:00:00', $tz ); if ( $maybe instanceof DateTimeImmutable ) { $week_start_tz = $maybe; } } if ( ! ( $week_start_tz instanceof DateTimeImmutable ) ) { $dow = (int) $now_tz->format( 'w' ); $start_dow = ( 0 === $week_starts_on ) ? 0 : 1; $delta = ( $dow - $start_dow + 7 ) % 7; $week_start_tz = $now_tz->setTime( 0, 0, 0 )->sub( new DateInterval( 'P' . (int) $delta . 'D' ) ); } $week_end_tz = $week_start_tz->add( new DateInterval( 'P7D' ) ); $week_start_utc = TCT_Utils::dt_to_mysql_utc( $week_start_tz ); $week_end_utc = TCT_Utils::dt_to_mysql_utc( $week_end_tz ); $date_keys = array(); $period = new DatePeriod( $week_start_tz, new DateInterval( 'P1D' ), $week_end_tz ); foreach ( $period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $date_keys[] = $dt->format( 'Y-m-d' ); } } global $wpdb; $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.points_per_completion, g.role_id, COALESCE(NULLIF(r.role_name,''),'') AS role_name
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at < %s
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $week_start_utc, $week_end_utc, $domain_id ), ARRAY_A ); $domain_points = array(); $role_points = array(); $roles_index = array(); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { $when_utc = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; $when_utc = is_string( $when_utc ) ? trim( $when_utc ) : ''; if ( '' === $when_utc || '0000-00-00 00:00:00' === $when_utc ) { continue; } $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $day = TCT_Utils::mysql_utc_to_tz( $when_utc, $tz, 'Y-m-d' ); if ( ! is_string( $day ) || '' === $day ) { continue; } if ( ! isset( $domain_points[ $day ] ) ) { $domain_points[ $day ] = 0; } $domain_points[ $day ] += $ppc; $rid = isset( $r['role_id'] ) ? (int) $r['role_id'] : 0; if ( $rid > 0 ) { if ( ! isset( $role_points[ $rid ] ) ) { $role_points[ $rid ] = array(); } if ( ! isset( $role_points[ $rid ][ $day ] ) ) { $role_points[ $rid ][ $day ] = 0; } $role_points[ $rid ][ $day ] += $ppc; if ( ! isset( $roles_index[ $rid ] ) ) { $rn = isset( $r['role_name'] ) ? (string) $r['role_name'] : ''; $rn = is_string( $rn ) ? trim( $rn ) : ''; $roles_index[ $rid ] = $rn; } } } } $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT g.id, g.role_id, g.is_tracked, g.points_per_completion, g.target, g.period_unit, g.period_mode, g.intervals_json, g.points_enabled_at,
                        COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) AS tct_effective_domain_id
                 FROM {$goals_table} g
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE g.user_id = %d
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'anki_cards')
                   AND COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) = %d", $user_id, $domain_id ), ARRAY_A ); if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $g ) { $rid = isset( $g['role_id'] ) ? (int) $g['role_id'] : 0; if ( $rid > 0 && ! isset( $roles_index[ $rid ] ) ) { $maybe_name = $wpdb->get_var( $wpdb->prepare( "SELECT role_name FROM {$roles_table} WHERE user_id = %d AND id = %d", $user_id, $rid ) ); $maybe_name = is_string( $maybe_name ) ? trim( $maybe_name ) : ''; $roles_index[ $rid ] = $maybe_name; } } } $roles_out = array(); foreach ( $roles_index as $rid => $rname ) { $roles_out[] = array( 'id' => (int) $rid, 'name' => (string) $rname, ); } $years_needed = array(); foreach ( $date_keys as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); if ( $y >= 1970 ) { $years_needed[ $y ] = true; } } $possible_by_year = array(); foreach ( array_keys( $years_needed ) as $y ) { $possible_by_year[ $y ] = $this->tct_compute_domain_and_role_possible_points_for_year( (int) $y, $tz, $goal_rows ); } $domain_possible = array(); foreach ( $date_keys as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); $val = 0.0; if ( isset( $possible_by_year[ $y ]['domain'] ) && is_array( $possible_by_year[ $y ]['domain'] ) && isset( $possible_by_year[ $y ]['domain'][ $dk ] ) ) { $val = (float) $possible_by_year[ $y ]['domain'][ $dk ]; } $domain_possible[ $dk ] = $val; } $roles_payload = array(); foreach ( $roles_out as $ro ) { $rid = isset( $ro['id'] ) ? (int) $ro['id'] : 0; if ( $rid <= 0 ) { continue; } $r_possible = array(); foreach ( $date_keys as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); $v = 0.0; if ( isset( $possible_by_year[ $y ]['roles'] ) && is_array( $possible_by_year[ $y ]['roles'] ) && isset( $possible_by_year[ $y ]['roles'][ $rid ] ) && is_array( $possible_by_year[ $y ]['roles'][ $rid ] ) && isset( $possible_by_year[ $y ]['roles'][ $rid ][ $dk ] ) ) { $v = (float) $possible_by_year[ $y ]['roles'][ $rid ][ $dk ]; } $r_possible[ $dk ] = $v; } $r_points = isset( $role_points[ $rid ] ) && is_array( $role_points[ $rid ] ) ? $role_points[ $rid ] : array(); $roles_payload[ (string) $rid ] = array( 'points' => $r_points, 'possible' => $r_possible, ); } TCT_Utils::send_json_success( array( 'ok' => true, 'domainId' => (int) $domain_id, 'weekStart' => $week_start_tz->format( 'Y-m-d' ), 'dates' => $date_keys, 'roles' => $roles_out, 'domain' => array( 'points' => $domain_points, 'possible' => $domain_possible, ), 'rolesData' => $roles_payload, ) ); } public function handle_sleep_state_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_sleep_state', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.' ), 400 ); } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking database is unavailable.' ), 500 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $sleep_col = $wpdb->get_var( "SHOW COLUMNS FROM {$goals_table} LIKE 'sleep_tracking_enabled'" ); if ( ! $sleep_col ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not available.' ), 400 ); } $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, sleep_tracking_enabled, sleep_rollover_time
                 FROM {$goals_table}
                 WHERE id = %d AND user_id = %d
                 LIMIT 1", $goal_id, $user_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.' ), 404 ); } if ( empty( $goal['sleep_tracking_enabled'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not enabled for this goal.' ), 403 ); } $rollover_time = isset( $goal['sleep_rollover_time'] ) ? trim( (string) $goal['sleep_rollover_time'] ) : ''; if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $rollover_time ) ) { $rollover_time = '18:00'; } $tz = null; if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ) { $tz = TCT_Utils::wp_timezone(); } elseif ( function_exists( 'wp_timezone' ) ) { $tz = wp_timezone(); } else { $tz = new DateTimeZone( 'UTC' ); } $now_tz = new DateTimeImmutable( 'now', $tz ); list( $rh, $rm ) = explode( ':', $rollover_time ); $rollover_seconds = ( (int) $rh * 3600 ) + ( (int) $rm * 60 ); $now_seconds = ( (int) $now_tz->format( 'H' ) * 3600 ) + ( (int) $now_tz->format( 'i' ) * 60 ) + ( (int) $now_tz->format( 's' ) ); $default_sleep_date = ( $now_seconds < $rollover_seconds ) ? $now_tz->sub( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $now_tz->format( 'Y-m-d' ); $sleep_date = $default_sleep_date; $is_default = true; $sleep_date_override = isset( $_POST['sleep_date'] ) ? sanitize_text_field( wp_unslash( $_POST['sleep_date'] ) ) : ''; $sleep_date_override = trim( (string) $sleep_date_override ); if ( '' !== $sleep_date_override ) { if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sleep_date_override ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date. Please use YYYY-MM-DD.' ), 400 ); } $maybe = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $sleep_date_override . ' 00:00:00', $tz ); $errs = DateTimeImmutable::getLastErrors(); if ( ! ( $maybe instanceof DateTimeImmutable ) || ( is_array( $errs ) && ( (int) $errs['warning_count'] > 0 || (int) $errs['error_count'] > 0 ) ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date. Please select a real calendar date.' ), 400 ); } $sleep_date = $maybe->format( 'Y-m-d' ); $is_default = ( $sleep_date === $default_sleep_date ); } if ( ! method_exists( 'TCT_DB', 'get_sleep_cycle' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not available.' ), 400 ); } $cycle = TCT_DB::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_wp_error( $cycle ) ) { TCT_Utils::send_json_error( array( 'message' => $cycle->get_error_message() ), 400 ); } $bed = ''; $wake = ''; $duration = ''; if ( is_array( $cycle ) ) { $bed = isset( $cycle['bed_time'] ) ? trim( (string) $cycle['bed_time'] ) : ''; $wake = isset( $cycle['wake_time'] ) ? trim( (string) $cycle['wake_time'] ) : ''; $duration = isset( $cycle['duration_hhmm'] ) ? trim( (string) $cycle['duration_hhmm'] ) : ''; } $state = 'A'; if ( '' !== $bed && '' === $wake ) { $state = 'B'; } elseif ( '' !== $bed && '' !== $wake ) { $state = 'C'; } TCT_Utils::send_json_success( array( 'ok' => true, 'sleepDate' => (string) $sleep_date, 'isDefault' => (bool) $is_default, 'stateKey' => (string) $state, 'bedTime' => (string) $bed, 'wakeTime' => (string) $wake, 'duration' => (string) $duration, ) ); } public function handle_sleep_save_bedtime_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_sleep_save_bedtime', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.' ), 400 ); } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking database is unavailable.' ), 500 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $sleep_col = $wpdb->get_var( "SHOW COLUMNS FROM {$goals_table} LIKE 'sleep_tracking_enabled'" ); if ( ! $sleep_col ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not available.' ), 400 ); } $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, sleep_tracking_enabled, sleep_rollover_time
                 FROM {$goals_table}
                 WHERE id = %d AND user_id = %d
                 LIMIT 1", $goal_id, $user_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.' ), 404 ); } if ( empty( $goal['sleep_tracking_enabled'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not enabled for this goal.' ), 403 ); } $rollover_time = isset( $goal['sleep_rollover_time'] ) ? trim( (string) $goal['sleep_rollover_time'] ) : ''; if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $rollover_time ) ) { $rollover_time = '18:00'; } $tz = null; if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ) { $tz = TCT_Utils::wp_timezone(); } elseif ( function_exists( 'wp_timezone' ) ) { $tz = wp_timezone(); } else { $tz = new DateTimeZone( 'UTC' ); } $now_tz = new DateTimeImmutable( 'now', $tz ); list( $rh, $rm ) = explode( ':', $rollover_time ); $rollover_seconds = ( (int) $rh * 3600 ) + ( (int) $rm * 60 ); $now_seconds = ( (int) $now_tz->format( 'H' ) * 3600 ) + ( (int) $now_tz->format( 'i' ) * 60 ) + ( (int) $now_tz->format( 's' ) ); $default_sleep_date = ( $now_seconds < $rollover_seconds ) ? $now_tz->sub( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $now_tz->format( 'Y-m-d' ); $sleep_date = $default_sleep_date; $is_default = true; $sleep_date_override = isset( $_POST['sleep_date'] ) ? sanitize_text_field( wp_unslash( $_POST['sleep_date'] ) ) : ''; $sleep_date_override = trim( (string) $sleep_date_override ); if ( '' !== $sleep_date_override ) { if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sleep_date_override ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date. Please use YYYY-MM-DD.' ), 400 ); } $maybe = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $sleep_date_override . ' 00:00:00', $tz ); $errs = DateTimeImmutable::getLastErrors(); if ( ! ( $maybe instanceof DateTimeImmutable ) || ( is_array( $errs ) && ( (int) $errs['warning_count'] > 0 || (int) $errs['error_count'] > 0 ) ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date. Please select a real calendar date.' ), 400 ); } $sleep_date = $maybe->format( 'Y-m-d' ); $is_default = ( $sleep_date === $default_sleep_date ); } $bed_time = isset( $_POST['bed_time'] ) ? sanitize_text_field( wp_unslash( $_POST['bed_time'] ) ) : ''; $bed_time = trim( (string) $bed_time ); if ( strlen( $bed_time ) >= 5 ) { $bed_time = substr( $bed_time, 0, 5 ); } if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $bed_time ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid bedtime. Please use HH:MM (00:00-23:59).' ), 400 ); } if ( ! method_exists( 'TCT_DB', 'upsert_sleep_bed_time' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not available.' ), 400 ); } $row = TCT_DB::upsert_sleep_bed_time( $user_id, $goal_id, $sleep_date, $bed_time, ! $is_default ); if ( is_wp_error( $row ) ) { TCT_Utils::send_json_error( array( 'message' => $row->get_error_message() ), 400 ); } $bed = ''; $wake = ''; $duration = ''; if ( is_array( $row ) ) { $bed = isset( $row['bed_time'] ) ? trim( (string) $row['bed_time'] ) : ''; $wake = isset( $row['wake_time'] ) ? trim( (string) $row['wake_time'] ) : ''; $duration = isset( $row['duration_hhmm'] ) ? trim( (string) $row['duration_hhmm'] ) : ''; } $state = 'A'; if ( '' !== $bed && '' === $wake ) { $state = 'B'; } elseif ( '' !== $bed && '' !== $wake ) { $state = 'C'; } if ( class_exists( 'TCT_DB' ) ) { try { $this->auto_score_bed_time_goals_from_sleep_bed_time( $user_id, $sleep_date, $bed, $tz ); } catch ( Exception $e ) { if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) { error_log( 'TCT bed-time auto-score error: ' . $e->getMessage() ); } } } TCT_Utils::send_json_success( array( 'ok' => true, 'sleepDate' => (string) $sleep_date, 'isDefault' => (bool) $is_default, 'stateKey' => (string) $state, 'bedTime' => (string) $bed, 'wakeTime' => (string) $wake, 'duration' => (string) $duration, 'message' => 'Saved bedtime.', ) ); } public function handle_sleep_save_waketime_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_sleep_save_waketime', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.' ), 400 ); } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking database is unavailable.' ), 500 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $sleep_col = $wpdb->get_var( "SHOW COLUMNS FROM {$goals_table} LIKE 'sleep_tracking_enabled'" ); if ( ! $sleep_col ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not available.' ), 400 ); } $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, goal_name, label_name, goal_type, threshold, period_unit, period_span, points_per_completion, is_tracked, points_enabled_at, sleep_tracking_enabled, sleep_rollover_time
                 FROM {$goals_table}
                 WHERE id = %d AND user_id = %d
                 LIMIT 1", $goal_id, $user_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.' ), 404 ); } if ( empty( $goal['sleep_tracking_enabled'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not enabled for this goal.' ), 403 ); } $rollover_time = isset( $goal['sleep_rollover_time'] ) ? trim( (string) $goal['sleep_rollover_time'] ) : ''; if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $rollover_time ) ) { $rollover_time = '18:00'; } $tz = null; if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ) { $tz = TCT_Utils::wp_timezone(); } elseif ( function_exists( 'wp_timezone' ) ) { $tz = wp_timezone(); } else { $tz = new DateTimeZone( 'UTC' ); } $now_tz = new DateTimeImmutable( 'now', $tz ); list( $rh, $rm ) = explode( ':', $rollover_time ); $rollover_seconds = ( (int) $rh * 3600 ) + ( (int) $rm * 60 ); $now_seconds = ( (int) $now_tz->format( 'H' ) * 3600 ) + ( (int) $now_tz->format( 'i' ) * 60 ) + ( (int) $now_tz->format( 's' ) ); $default_sleep_date = ( $now_seconds < $rollover_seconds ) ? $now_tz->sub( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $now_tz->format( 'Y-m-d' ); $sleep_date = $default_sleep_date; $is_default = true; $sleep_date_override = isset( $_POST['sleep_date'] ) ? sanitize_text_field( wp_unslash( $_POST['sleep_date'] ) ) : ''; $sleep_date_override = trim( (string) $sleep_date_override ); if ( '' !== $sleep_date_override ) { if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sleep_date_override ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date. Please use YYYY-MM-DD.' ), 400 ); } $maybe = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $sleep_date_override . ' 00:00:00', $tz ); $errs = DateTimeImmutable::getLastErrors(); if ( ! ( $maybe instanceof DateTimeImmutable ) || ( is_array( $errs ) && ( (int) $errs['warning_count'] > 0 || (int) $errs['error_count'] > 0 ) ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date. Please select a real calendar date.' ), 400 ); } $sleep_date = $maybe->format( 'Y-m-d' ); $is_default = ( $sleep_date === $default_sleep_date ); } $wake_time = isset( $_POST['wake_time'] ) ? sanitize_text_field( wp_unslash( $_POST['wake_time'] ) ) : ''; $wake_time = trim( (string) $wake_time ); if ( strlen( $wake_time ) >= 5 ) { $wake_time = substr( $wake_time, 0, 5 ); } if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $wake_time ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid wake-time. Please use HH:MM (00:00-23:59).' ), 400 ); } if ( ! method_exists( 'TCT_DB', 'upsert_sleep_wake_time' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking is not available.' ), 400 ); } $goal_type = isset( $goal['goal_type'] ) ? strtolower( trim( (string) $goal['goal_type'] ) ) : ''; $threshold = isset( $goal['threshold'] ) ? (int) $goal['threshold'] : 0; $ppc = isset( $goal['points_per_completion'] ) ? (int) $goal['points_per_completion'] : 0; $is_negative = ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_negative_goal_type' ) ) ? TCT_Utils::is_negative_goal_type( $goal_type ) : false; $completions_before = 0; $is_violation = false; $negative_points = 0; if ( $is_negative ) { $period_unit = isset( $goal['period_unit'] ) ? (string) $goal['period_unit'] : 'day'; $period_span = isset( $goal['period_span'] ) ? (int) $goal['period_span'] : 1; if ( $period_span <= 0 ) { $period_span = 1; } $tz_early = $tz; $now_tz_early = new DateTimeImmutable( 'now', $tz_early ); if ( class_exists( 'TCT_Interval' ) && method_exists( 'TCT_Interval', 'current_loop_bounds' ) ) { $bounds = TCT_Interval::current_loop_bounds( $now_tz_early, $period_unit, $period_span ); if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) && $bounds['start'] instanceof DateTimeInterface && $bounds['end'] instanceof DateTimeInterface ) { $loop_start_utc = ( new DateTimeImmutable( $bounds['start']->format( 'Y-m-d H:i:s' ), $tz_early ) )->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ); $loop_end_utc = ( new DateTimeImmutable( $bounds['end']->format( 'Y-m-d H:i:s' ), $tz_early ) )->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ); $completions_table = TCT_DB::table_completions(); $cnt = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $loop_start_utc, $loop_end_utc ) ); if ( $cnt > 0 ) { $completions_before = $cnt; } } } if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'is_negative_goal_violation' ) ) { $is_violation = (bool) TCT_Utils::is_negative_goal_violation( $goal_type, $threshold, $completions_before ); } if ( $is_violation && class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'compute_violation_penalty' ) ) { $violation_number = (int) $completions_before + 1; $negative_points = (int) TCT_Utils::compute_violation_penalty( $ppc, $violation_number ); } else { $negative_points = 0; } } $row = TCT_DB::upsert_sleep_wake_time( $user_id, $goal_id, $sleep_date, $wake_time, ! $is_default ); if ( is_wp_error( $row ) ) { TCT_Utils::send_json_error( array( 'message' => $row->get_error_message() ), 400 ); } $bed = ''; $wake = ''; $duration = ''; if ( is_array( $row ) ) { $bed = isset( $row['bed_time'] ) ? trim( (string) $row['bed_time'] ) : ''; $wake = isset( $row['wake_time'] ) ? trim( (string) $row['wake_time'] ) : ''; $duration = isset( $row['duration_hhmm'] ) ? trim( (string) $row['duration_hhmm'] ) : ''; } $state = 'A'; if ( '' !== $bed && '' === $wake ) { $state = 'B'; } elseif ( '' !== $bed && '' !== $wake ) { $state = 'C'; } $did_award = false; if ( 'C' === $state ) { if ( ! method_exists( 'TCT_DB', 'insert_completion_event_explicit' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep completion tracking is unavailable.' ), 500 ); } $source = 'sleep'; $source_ref = 'sleep:' . (int) $goal_id . ':' . (string) $sleep_date; // Attribute sleep completion to the sleep date (night-of) so it doesn't mark the wake-up day as completed.
            $completed_at_utc_mysql = '';
            try {
                $completed_local = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $sleep_date . ' 12:00:00', $tz );
                $errs = DateTimeImmutable::getLastErrors();
                if ( $completed_local instanceof DateTimeImmutable && ! ( is_array( $errs ) && ( (int) $errs['warning_count'] > 0 || (int) $errs['error_count'] > 0 ) ) ) {
                    $completed_at_utc_mysql = $completed_local->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
                }
            } catch ( Exception $e ) {
                $completed_at_utc_mysql = '';
            }
            if ( '' === $completed_at_utc_mysql ) {
                $completed_at_utc_mysql = current_time( 'mysql', true );
            } $ins = TCT_DB::insert_completion_event_explicit( $user_id, $goal_id, $source, $source_ref, $completed_at_utc_mysql ); if ( is_wp_error( $ins ) ) { TCT_Utils::send_json_error( array( 'message' => $ins->get_error_message() ), 500 ); } $completion_row_id = isset( $ins['id'] ) ? (int) $ins['id'] : 0; $did_award = isset( $ins['inserted'] ) ? (bool) $ins['inserted'] : false;
            // If a completion already existed, older versions stored the wake-up timestamp.
            // That made the *wake day* appear completed. Normalize existing rows to the sleep_date.
            if ( ! $did_award && $completion_row_id > 0 && isset( $ins['completed_at'] ) && (string) $ins['completed_at'] !== (string) $completed_at_utc_mysql ) {
                $completions_table = TCT_DB::table_completions();
                $wpdb->update( $completions_table, array( 'completed_at' => (string) $completed_at_utc_mysql ), array( 'id' => (int) $completion_row_id, 'user_id' => (int) $user_id ), array( '%s' ), array( '%d', '%d' ) );
                // Update ledger timestamp if present (do not recalculate points).
                if ( method_exists( 'TCT_DB', 'table_ledger' ) ) {
                    $ledger_table = TCT_DB::table_ledger();
                    $event_key = 'c_' . sha1( 'sleep:' . $source_ref . ':' . (string) $goal_id );
                    $wpdb->update( $ledger_table, array( 'occurred_at' => (string) $completed_at_utc_mysql, 'updated_at' => current_time( 'mysql', true ) ), array( 'user_id' => (int) $user_id, 'event_key' => (string) $event_key ), array( '%s', '%s' ), array( '%d', '%s' ) );
                }
            }
            if ( $did_award && $completion_row_id > 0 ) { if ( $is_negative ) { $this->record_negative_goal_ledger_entry( $user_id, $goal_id, $goal, $completion_row_id, $negative_points, $is_violation ); } else { if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'record_completion_from_event' ) ) ) { TCT_Ledger::record_completion_from_event( $user_id, $completion_row_id ); } } } } 
        // Wake-time goals: auto-score daily using the Sleep Tracker wake-time.
        if ( class_exists( 'TCT_DB' ) ) {
            try {
                $this->auto_score_wake_time_goals_from_sleep_wake_time( $user_id, $sleep_date, $wake, $tz );
            } catch ( Exception $e ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'TCT wake-time auto-score error: ' . $e->getMessage() );
                }
            }
        }

TCT_Utils::send_json_success( array( 'ok' => true, 'sleepDate' => (string) $sleep_date, 'isDefault' => (bool) $is_default, 'stateKey' => (string) $state, 'bedTime' => (string) $bed, 'wakeTime' => (string) $wake, 'duration' => (string) $duration, 'didAward' => (bool) $did_award, 'message' => $did_award ? 'Saved wake-time. Logged sleep completion.' : 'Saved wake-time.', ) ); }


    private function auto_score_wake_time_goals_from_sleep_wake_time( $user_id, $sleep_date, $wake_time_hhmm, $tz ) {
        global $wpdb;

        $user_id        = (int) $user_id;
        $sleep_date     = is_string( $sleep_date ) ? trim( $sleep_date ) : '';
        $wake_time_hhmm = is_string( $wake_time_hhmm ) ? trim( $wake_time_hhmm ) : '';

        if ( $user_id <= 0 || '' === $sleep_date ) {
            return array();
        }

        // If there's no wake-time, there's nothing to score now (auto-miss handles missing data).
        if ( '' === $wake_time_hhmm ) {
            return array();
        }

        // Validate date.
        if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sleep_date ) ) {
            return array();
        }
        $p = explode( '-', $sleep_date );
        if ( 3 !== count( $p ) || ! checkdate( (int) $p[1], (int) $p[2], (int) $p[0] ) ) {
            return array();
        }

        // Wake-up day is the calendar day after the sleep date (sleep date is "night-of").
        try {
            $sleep_day_tz = new DateTimeImmutable( $sleep_date . ' 00:00:00', $tz );
        } catch ( Exception $e ) {
            return array();
        }
        $wake_day_tz  = $sleep_day_tz->add( new DateInterval( 'P1D' ) );
        $wake_day_ymd = $wake_day_tz->format( 'Y-m-d' );

        // Attribute wake-time completions to the wake-up day at the actual wake time.
        // This ensures the reward stats ("Today") include the points immediately (they are calculated up to "now").
        $completed_at_tz = null;
        $wake_hhmm = is_string( $wake_time_hhmm ) ? trim( $wake_time_hhmm ) : '';

        if ( '' !== $wake_hhmm && $this->wake_time_is_valid_hhmm( $wake_hhmm ) ) {
            $dt_try = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $wake_day_ymd . ' ' . $wake_hhmm . ':00', $tz );
            $errs = DateTimeImmutable::getLastErrors();
            if ( $dt_try instanceof DateTimeImmutable && ( ! is_array( $errs ) || ( 0 === (int) $errs['warning_count'] && 0 === (int) $errs['error_count'] ) ) ) {
                $completed_at_tz = $dt_try;
            }
        }

        if ( ! ( $completed_at_tz instanceof DateTimeImmutable ) ) {
            // Fall back to the start of the day in case of DST parsing edge cases.
            $completed_at_tz = new DateTimeImmutable( $wake_day_ymd . ' 00:00:00', $tz );
        }

        $completed_at_utc_mysql = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) )
            ? TCT_Utils::dt_to_mysql_utc( $completed_at_tz )
            : $completed_at_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

        $goals_table = TCT_DB::table_goals();
        $goals = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, goal_name, label_name, goal_type, target, period_unit, period_span, period_mode, points_per_completion, wake_time_target,
                        allowed_fails_target, allowed_fails_unit, allowed_fails_span
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1 AND wake_time_enabled = 1",
                $user_id
            ),
            ARRAY_A
        );

        if ( ! is_array( $goals ) || empty( $goals ) ) {
            return array();
        }

        $results = array();

        foreach ( $goals as $g ) {
            $goal_id = isset( $g['id'] ) ? (int) $g['id'] : 0;
            if ( $goal_id <= 0 ) {
                continue;
            }

            // Wake-time auto-scoring is for positive goals only.
            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_type' ) ) ) {
                $gt = isset( $g['goal_type'] ) ? (string) $g['goal_type'] : '';
                if ( TCT_Utils::is_negative_goal_type( $gt ) ) {
                    continue;
                }
            }

            // If an auto-miss was recorded for this goal/day (e.g., wake-time entered after EOD),
            // remove it so wake-time scoring is authoritative and we don't double-count.
            $completions_table = TCT_DB::table_completions();
            $ledger_table      = TCT_DB::table_ledger();

            $auto_miss_source     = 'auto_miss';
            $auto_miss_source_ref = 'auto_miss:' . $goal_id . ':' . $wake_day_ymd;

            // Best-effort cleanup; ignore errors and continue scoring.
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = %s AND source_ref = %s",
                    $user_id,
                    $goal_id,
                    $auto_miss_source,
                    $auto_miss_source_ref
                )
            );

            $auto_miss_event_key = 'c_' . sha1( $auto_miss_source . ':' . $auto_miss_source_ref . ':' . (string) $goal_id );
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$ledger_table} WHERE user_id = %d AND event_key = %s",
                    $user_id,
                    $auto_miss_event_key
                )
            );

            $target_time = isset( $g['wake_time_target'] ) && is_string( $g['wake_time_target'] ) ? trim( $g['wake_time_target'] ) : '';
            $diff = $this->wake_time_clock_diff_minutes( $wake_time_hhmm, $target_time );

            $passed = ( null !== $diff && $diff <= 30 );

            $source     = 'wake_time';
            $source_ref = 'wake_time:' . $goal_id . ':' . $wake_day_ymd;

            $ins = TCT_DB::insert_completion_event_explicit(
                $user_id,
                $goal_id,
                $source,
                $source_ref,
                $completed_at_utc_mysql,
                null
            );

            if ( is_wp_error( $ins ) ) {
                continue;
            }

            $completion_id = isset( $ins['id'] ) ? (int) $ins['id'] : 0;
            if ( $completion_id <= 0 ) {
                continue;
            }

            // Force the deterministic completed_at timestamp even if the completion already existed.
            $completions_table = TCT_DB::table_completions();
            $wpdb->update(
                $completions_table,
                array( 'completed_at' => $completed_at_utc_mysql ),
                array( 'id' => $completion_id, 'user_id' => $user_id ),
                array( '%s' ),
                array( '%d', '%d' )
            );

            if ( $passed ) {
                if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'record_completion_from_event' ) ) ) {
                    TCT_Ledger::record_completion_from_event( $user_id, $completion_id );
                }
            } else {
                $ppc    = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0;
                $target = isset( $g['target'] ) ? (int) $g['target'] : 0;

                $fail_points = 0;
                if ( $ppc > 0 && $target > 0 && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_penalty_points' ) ) ) {
                    $fail_points = (int) TCT_Utils::compute_penalty_points( $ppc, $target, 0 );
                }

                $allowed_fails_active = false;

                // Allowed fails support mirrors auto-miss: treat this as an auto-fail and set points to 0 when within the allowance.
                if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_allowed_fails' ) ) && TCT_Interval::is_goal_eligible_for_allowed_fails( $g ) ) {
                    $cfg = is_callable( array( 'TCT_Interval', 'normalize_allowed_fails_config_from_row' ) )
                        ? TCT_Interval::normalize_allowed_fails_config_from_row( $g )
                        : null;

                    $cfg_target = is_array( $cfg ) && isset( $cfg['target'] ) ? max( 0, (int) $cfg['target'] ) : 0;
                    $cfg_unit   = is_array( $cfg ) && isset( $cfg['unit'] ) ? (string) $cfg['unit'] : 'week';
                    $cfg_span   = is_array( $cfg ) && isset( $cfg['span'] ) ? max( 1, (int) $cfg['span'] ) : 1;

                    if ( $cfg_target > 0 && in_array( $cfg_unit, array( 'week', 'month', 'year' ), true ) && is_callable( array( 'TCT_Interval', 'current_allowed_fails_bounds' ) ) ) {
                        $bounds = TCT_Interval::current_allowed_fails_bounds( $completed_at_tz, $cfg_unit, $cfg_span );
                        if ( is_array( $bounds ) && isset( $bounds['start'] ) && $bounds['start'] instanceof DateTimeImmutable ) {
                            $allowed_fails_active = true;

                            $window_start_utc = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) )
                                ? TCT_Utils::dt_to_mysql_utc( $bounds['start'] )
                                : $bounds['start']->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

                            $ledger_table = TCT_DB::table_ledger();
                            $like_manual  = $wpdb->esc_like( '[manual fail]' ) . '%';
                            $like_auto    = $wpdb->esc_like( '[auto miss]' ) . '%';

                            // Count fails strictly before this wake-time fail timestamp (idempotent across retries).
                            $fails_before = (int) $wpdb->get_var(
                                $wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$ledger_table}
                                     WHERE user_id = %d AND goal_id = %d
                                       AND occurred_at >= %s AND occurred_at < %s
                                       AND (details LIKE %s OR details LIKE %s)",
                                    $user_id,
                                    $goal_id,
                                    $window_start_utc,
                                    $completed_at_utc_mysql,
                                    $like_manual,
                                    $like_auto
                                )
                            );

                            if ( $fails_before < $cfg_target ) {
                                $fail_points = 0;
                            }
                        }
                    }
                }

                // Record a ledger entry for wake-time auto-fails, including 0-point allowed fails.
                if ( 0 !== (int) $fail_points || $allowed_fails_active ) {
                    $this->record_wake_time_fail_ledger_entry( $user_id, $goal_id, $g, $completion_id, $fail_points, $wake_day_ymd );
                }
            }

            $results[] = array(
                'goal_id'  => $goal_id,
                'wake_day' => $wake_day_ymd,
                'passed'   => (bool) $passed,
            );
        }

        return $results;
    }

    private function auto_score_bed_time_goals_from_sleep_bed_time( $user_id, $sleep_date, $bed_time_hhmm, $tz ) {
        global $wpdb;

        $user_id       = (int) $user_id;
        $sleep_date    = is_string( $sleep_date ) ? trim( $sleep_date ) : '';
        $bed_time_hhmm = is_string( $bed_time_hhmm ) ? trim( $bed_time_hhmm ) : '';

        if ( $user_id <= 0 || '' === $sleep_date ) {
            return array();
        }

        if ( '' === $bed_time_hhmm ) {
            return array();
        }

        if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sleep_date ) ) {
            return array();
        }
        $p = explode( '-', $sleep_date );
        if ( 3 !== count( $p ) || ! checkdate( (int) $p[1], (int) $p[2], (int) $p[0] ) ) {
            return array();
        }

        $sleep_day_ymd = $sleep_date;

        $completed_at_tz = null;
        $bed_hhmm = is_string( $bed_time_hhmm ) ? trim( $bed_time_hhmm ) : '';
        $rollover_time = '18:00';
        if ( class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_goals' ) ) {
            $goals_table_rollover = TCT_DB::table_goals();
            $maybe_rollover = $wpdb->get_var( $wpdb->prepare( "SELECT sleep_rollover_time FROM {$goals_table_rollover} WHERE user_id = %d AND sleep_tracking_enabled = 1 ORDER BY id ASC LIMIT 1", $user_id ) );
            $maybe_rollover = is_string( $maybe_rollover ) ? trim( (string) $maybe_rollover ) : '';
            if ( preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $maybe_rollover ) ) {
                $rollover_time = $maybe_rollover;
            }
        }
        $rollover_parts = explode( ':', $rollover_time, 2 );
        $rollover_minutes = ( 2 === count( $rollover_parts ) ) ? ( ( (int) $rollover_parts[0] ) * 60 ) + (int) $rollover_parts[1] : ( 18 * 60 );

        if ( '' !== $bed_hhmm && $this->wake_time_is_valid_hhmm( $bed_hhmm ) ) {
            $bed_minutes = $this->wake_time_hhmm_to_minutes( $bed_hhmm );
            $bed_date_ymd = ( null !== $bed_minutes && $bed_minutes < $rollover_minutes ) ? ( new DateTimeImmutable( $sleep_day_ymd . ' 00:00:00', $tz ) )->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $sleep_day_ymd;
            $dt_try = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $bed_date_ymd . ' ' . $bed_hhmm . ':00', $tz );
            $errs = DateTimeImmutable::getLastErrors();
            if ( $dt_try instanceof DateTimeImmutable && ( ! is_array( $errs ) || ( 0 === (int) $errs['warning_count'] && 0 === (int) $errs['error_count'] ) ) ) {
                $completed_at_tz = $dt_try;
            }
        }

        if ( ! ( $completed_at_tz instanceof DateTimeImmutable ) ) {
            $completed_at_tz = new DateTimeImmutable( $sleep_day_ymd . ' 00:00:00', $tz );
        }

        $completed_at_utc_mysql = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) )
            ? TCT_Utils::dt_to_mysql_utc( $completed_at_tz )
            : $completed_at_tz->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

        $goals_table = TCT_DB::table_goals();
        $goals = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, goal_name, label_name, goal_type, target, period_unit, period_span, period_mode, points_per_completion, bed_time_target,
                        allowed_fails_target, allowed_fails_unit, allowed_fails_span
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1 AND bed_time_enabled = 1",
                $user_id
            ),
            ARRAY_A
        );

        if ( ! is_array( $goals ) || empty( $goals ) ) {
            return array();
        }

        $results = array();

        foreach ( $goals as $g ) {
            $goal_id = isset( $g['id'] ) ? (int) $g['id'] : 0;
            if ( $goal_id <= 0 ) {
                continue;
            }

            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_type' ) ) ) {
                $gt = isset( $g['goal_type'] ) ? (string) $g['goal_type'] : '';
                if ( TCT_Utils::is_negative_goal_type( $gt ) ) {
                    continue;
                }
            }

            $completions_table = TCT_DB::table_completions();
            $ledger_table      = TCT_DB::table_ledger();

            $auto_miss_source     = 'auto_miss';
            $auto_miss_source_ref = 'auto_miss:' . $goal_id . ':' . $sleep_day_ymd;

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = %s AND source_ref = %s",
                    $user_id,
                    $goal_id,
                    $auto_miss_source,
                    $auto_miss_source_ref
                )
            );

            $auto_miss_event_key = 'c_' . sha1( $auto_miss_source . ':' . $auto_miss_source_ref . ':' . (string) $goal_id );
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$ledger_table} WHERE user_id = %d AND event_key = %s",
                    $user_id,
                    $auto_miss_event_key
                )
            );

            $target_time = isset( $g['bed_time_target'] ) && is_string( $g['bed_time_target'] ) ? trim( (string) $g['bed_time_target'] ) : '';
            $passed = false;
            if ( $this->wake_time_is_valid_hhmm( $bed_time_hhmm ) && $this->wake_time_is_valid_hhmm( $target_time ) ) {
                $bed_minutes = $this->wake_time_hhmm_to_minutes( $bed_time_hhmm );
                $target_minutes = $this->wake_time_hhmm_to_minutes( $target_time );
                $sleep_day_tz = new DateTimeImmutable( $sleep_day_ymd . ' 00:00:00', $tz );
                $bed_date_ymd = ( null !== $bed_minutes && $bed_minutes < $rollover_minutes ) ? $sleep_day_tz->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $sleep_day_ymd;
                $target_date_ymd = ( null !== $target_minutes && $target_minutes < $rollover_minutes ) ? $sleep_day_tz->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $sleep_day_ymd;
                $bed_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $bed_date_ymd . ' ' . $bed_time_hhmm . ':00', $tz );
                $target_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $target_date_ymd . ' ' . $target_time . ':00', $tz );
                if ( $bed_dt instanceof DateTimeImmutable && $target_dt instanceof DateTimeImmutable ) {
                    $late_minutes = (int) floor( ( (int) $bed_dt->getTimestamp() - (int) $target_dt->getTimestamp() ) / 60 );
                    $passed = ( $late_minutes <= 30 );
                }
            }

            $source     = 'bed_time';
            $source_ref = 'bed_time:' . $goal_id . ':' . $sleep_day_ymd;

            $ins = TCT_DB::insert_completion_event_explicit(
                $user_id,
                $goal_id,
                $source,
                $source_ref,
                $completed_at_utc_mysql,
                null
            );

            if ( is_wp_error( $ins ) ) {
                continue;
            }

            $completion_id = isset( $ins['id'] ) ? (int) $ins['id'] : 0;
            if ( $completion_id <= 0 ) {
                continue;
            }

            $wpdb->update(
                $completions_table,
                array( 'completed_at' => $completed_at_utc_mysql ),
                array( 'id' => $completion_id, 'user_id' => $user_id ),
                array( '%s' ),
                array( '%d', '%d' )
            );

            if ( $passed ) {
                if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'record_completion_from_event' ) ) ) {
                    TCT_Ledger::record_completion_from_event( $user_id, $completion_id );
                }
            } else {
                $ppc    = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0;
                $target = isset( $g['target'] ) ? (int) $g['target'] : 0;

                $fail_points = 0;
                if ( $ppc > 0 && $target > 0 && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_penalty_points' ) ) ) {
                    $fail_points = (int) TCT_Utils::compute_penalty_points( $ppc, $target, 0 );
                }

                $allowed_fails_active = false;

                if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_allowed_fails' ) ) && TCT_Interval::is_goal_eligible_for_allowed_fails( $g ) ) {
                    $cfg = is_callable( array( 'TCT_Interval', 'normalize_allowed_fails_config_from_row' ) )
                        ? TCT_Interval::normalize_allowed_fails_config_from_row( $g )
                        : null;

                    $cfg_target = is_array( $cfg ) && isset( $cfg['target'] ) ? max( 0, (int) $cfg['target'] ) : 0;
                    $cfg_unit   = is_array( $cfg ) && isset( $cfg['unit'] ) ? (string) $cfg['unit'] : 'week';
                    $cfg_span   = is_array( $cfg ) && isset( $cfg['span'] ) ? max( 1, (int) $cfg['span'] ) : 1;

                    if ( $cfg_target > 0 && in_array( $cfg_unit, array( 'week', 'month', 'year' ), true ) && is_callable( array( 'TCT_Interval', 'current_allowed_fails_bounds' ) ) ) {
                        $bounds = TCT_Interval::current_allowed_fails_bounds( $completed_at_tz, $cfg_unit, $cfg_span );
                        if ( is_array( $bounds ) && isset( $bounds['start'] ) && $bounds['start'] instanceof DateTimeImmutable ) {
                            $allowed_fails_active = true;

                            $window_start_utc = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'dt_to_mysql_utc' ) )
                                ? TCT_Utils::dt_to_mysql_utc( $bounds['start'] )
                                : $bounds['start']->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

                            $ledger_table = TCT_DB::table_ledger();
                            $like_manual  = $wpdb->esc_like( '[manual fail]' ) . '%';
                            $like_auto    = $wpdb->esc_like( '[auto miss]' ) . '%';

                            $fails_before = (int) $wpdb->get_var(
                                $wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$ledger_table}
                                     WHERE user_id = %d AND goal_id = %d
                                       AND occurred_at >= %s AND occurred_at < %s
                                       AND (details LIKE %s OR details LIKE %s)",
                                    $user_id,
                                    $goal_id,
                                    $window_start_utc,
                                    $completed_at_utc_mysql,
                                    $like_manual,
                                    $like_auto
                                )
                            );

                            if ( $fails_before < $cfg_target ) {
                                $fail_points = 0;
                            }
                        }
                    }
                }

                if ( 0 !== (int) $fail_points || $allowed_fails_active ) {
                    $this->record_bed_time_fail_ledger_entry( $user_id, $goal_id, $g, $completion_id, $fail_points, $sleep_day_ymd );
                }
            }

            $results[] = array(
                'goal_id'   => $goal_id,
                'sleep_day' => $sleep_day_ymd,
                'passed'    => (bool) $passed,
            );
        }

        return $results;
    }

    private function record_wake_time_fail_ledger_entry( $user_id, $goal_id, $goal, $completion_id, $points, $wake_day_ymd ) {
        global $wpdb;

        $user_id       = (int) $user_id;
        $goal_id       = (int) $goal_id;
        $completion_id = (int) $completion_id;
        $points        = (int) $points;

        if ( $user_id <= 0 || $goal_id <= 0 || $completion_id <= 0 ) {
            return;
        }

        $ledger_table      = TCT_DB::table_ledger();
        $completions_table = TCT_DB::table_completions();

        $c = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT source, source_ref, completed_at FROM {$completions_table} WHERE id = %d AND user_id = %d",
                $completion_id,
                $user_id
            ),
            ARRAY_A
        );

        if ( ! is_array( $c ) ) {
            return;
        }

        $source     = isset( $c['source'] ) ? (string) $c['source'] : 'wake_time';
        $source_ref = isset( $c['source_ref'] ) ? (string) $c['source_ref'] : '';
        $occurred_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : '';
        if ( '' === $occurred_at || '0000-00-00 00:00:00' === $occurred_at ) {
            $occurred_at = current_time( 'mysql', true );
        }

        $event_key  = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id );
        $event_type = 'completion';

        $goal_name  = isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : '';
        $label_name = isset( $goal['label_name'] ) && is_string( $goal['label_name'] ) ? (string) $goal['label_name'] : '';

        // Use the same prefix as auto-miss so existing history/allowed-fails UI can recognize it.
        $details = '[auto miss] wake-time ' . (string) $wake_day_ymd;

        $now = current_time( 'mysql', true );

        $sql = "INSERT INTO {$ledger_table} (
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
                event_type = VALUES(event_type),
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
                $event_type,
                $points,
                $occurred_at,
                $goal_id,
                $goal_name,
                $label_name,
                '',
                '',
                $details,
                $now,
                $now
            )
        );
    }

    private function record_bed_time_fail_ledger_entry( $user_id, $goal_id, $goal, $completion_id, $points, $sleep_day_ymd ) {
        global $wpdb;

        $user_id       = (int) $user_id;
        $goal_id       = (int) $goal_id;
        $completion_id = (int) $completion_id;
        $points        = (int) $points;

        if ( $user_id <= 0 || $goal_id <= 0 || $completion_id <= 0 ) {
            return;
        }

        $ledger_table      = TCT_DB::table_ledger();
        $completions_table = TCT_DB::table_completions();

        $c = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT source, source_ref, completed_at FROM {$completions_table} WHERE id = %d AND user_id = %d",
                $completion_id,
                $user_id
            ),
            ARRAY_A
        );

        if ( ! is_array( $c ) ) {
            return;
        }

        $source      = isset( $c['source'] ) ? (string) $c['source'] : 'bed_time';
        $source_ref  = isset( $c['source_ref'] ) ? (string) $c['source_ref'] : '';
        $occurred_at = isset( $c['completed_at'] ) ? (string) $c['completed_at'] : '';
        if ( '' === $occurred_at || '0000-00-00 00:00:00' === $occurred_at ) {
            $occurred_at = current_time( 'mysql', true );
        }

        $event_key  = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id );
        $event_type = 'completion';

        $goal_name  = isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : '';
        $label_name = isset( $goal['label_name'] ) && is_string( $goal['label_name'] ) ? (string) $goal['label_name'] : '';

        $details = '[auto miss] bed-time ' . (string) $sleep_day_ymd;

        $now = current_time( 'mysql', true );

        $sql = "INSERT INTO {$ledger_table} (
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
                event_type = VALUES(event_type),
                points = VALUES(points),
                occurred_at = VALUES(occurred_at),
                goal_id = VALUES(goal_id),
                goal_name = VALUES(goal_name),
                label_name = VALUES(label_name),
                todoist_completed_id = VALUES(todoist_completed_id),
                todoist_task_id = VALUES(todoist_task_id),
                details = VALUES(details),
                updated_at = VALUES(updated_at)";

        $wpdb->query(
            $wpdb->prepare(
                $sql,
                $user_id,
                $event_key,
                $event_type,
                $points,
                $occurred_at,
                $goal_id,
                $goal_name,
                $label_name,
                '',
                '',
                $details,
                $now,
                $now
            )
        );
    }

    private function wake_time_is_valid_hhmm( $hhmm ) {
        if ( ! is_string( $hhmm ) ) {
            return false;
        }
        $hhmm = trim( $hhmm );
        if ( 1 !== preg_match( '/^\d{2}:\d{2}$/', $hhmm ) ) {
            return false;
        }
        $parts = explode( ':', $hhmm, 2 );
        if ( 2 !== count( $parts ) ) {
            return false;
        }
        $h = (int) $parts[0];
        $m = (int) $parts[1];
        return ( $h >= 0 && $h <= 23 && $m >= 0 && $m <= 59 );
    }

    private function wake_time_hhmm_to_minutes( $hhmm ) {
        if ( ! $this->wake_time_is_valid_hhmm( $hhmm ) ) {
            return null;
        }
        $parts = explode( ':', trim( (string) $hhmm ), 2 );
        if ( 2 !== count( $parts ) ) {
            return null;
        }
        return ( ( (int) $parts[0] ) * 60 ) + ( (int) $parts[1] );
    }

    private function wake_time_clock_diff_minutes( $hhmm_a, $hhmm_b ) {
        $a = $this->wake_time_hhmm_to_minutes( $hhmm_a );
        $b = $this->wake_time_hhmm_to_minutes( $hhmm_b );
        if ( null === $a || null === $b ) {
            return null;
        }
        $diff = abs( (int) $a - (int) $b );
        if ( $diff > 720 ) {
            $diff = 1440 - $diff;
        }
        return (int) $diff;
    }



    public function handle_debug_goal_bounds_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.' ), 401 ); } if ( ! current_user_can( 'manage_options' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Forbidden.' ), 403 ); } TCT_Utils::enforce_ajax_nonce( 'tct_debug_goal_bounds', 'nonce' ); $user_id = get_current_user_id(); $goal_id = 0; if ( isset( $_REQUEST['goal_id'] ) ) { $goal_id = (int) wp_unslash( $_REQUEST['goal_id'] ); } if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal_id.' ), 400 ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, goal_name, target, period_unit, period_span, period_mode, points_per_completion, goal_type, threshold
                 FROM {$goals_table}
                 WHERE id = %d", $goal_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Goal not found.' ), 404 ); } if ( (int) $goal['user_id'] !== (int) $user_id ) { TCT_Utils::send_json_error( array( 'message' => 'Goal does not belong to current user.' ), 403 ); } $unit = isset( $goal['period_unit'] ) ? sanitize_text_field( (string) $goal['period_unit'] ) : 'week'; if ( ! in_array( $unit, array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ), true ) ) { $unit = 'week'; } $span = isset( $goal['period_span'] ) ? max( 1, (int) $goal['period_span'] ) : 1; $period_mode = isset( $goal['period_mode'] ) ? sanitize_text_field( (string) $goal['period_mode'] ) : 'calendar'; if ( 'calendar' !== $period_mode ) { $period_mode = 'calendar'; } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $bounds = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $bounds = TCT_Interval::current_loop_bounds( $now_tz, $unit, $span ); } if ( ! is_array( $bounds ) || ! isset( $bounds['start'], $bounds['end'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) || ! ( $bounds['end'] instanceof DateTimeImmutable ) ) { TCT_Utils::send_json_error( array( 'message' => 'Could not compute loop bounds.' ), 500 ); } $start_tz = $bounds['start']; $end_tz = $bounds['end']; $utc = new DateTimeZone( 'UTC' ); $response = array( 'ok' => true, 'goalId' => (int) $goal_id, 'goalName' => isset( $goal['goal_name'] ) ? (string) $goal['goal_name'] : '', 'target' => isset( $goal['target'] ) ? (int) $goal['target'] : 0, 'unit' => (string) $unit, 'span' => (int) $span, 'periodMode' => (string) $period_mode, 'timezone' => method_exists( $tz, 'getName' ) ? $tz->getName() : 'site', 'nowSite' => $now_tz->format( 'Y-m-d H:i:s' ), 'nowUtc' => $now_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' ), 'loopStartSite' => $start_tz->format( 'Y-m-d H:i:s' ), 'loopEndSite' => $end_tz->format( 'Y-m-d H:i:s' ), 'loopStartUtc' => $start_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' ), 'loopEndUtc' => $end_tz->setTimezone( $utc )->format( 'Y-m-d H:i:s' ), 'loopStartTs' => (int) $start_tz->getTimestamp(), 'loopEndTs' => (int) $end_tz->getTimestamp(), 'nowTs' => (int) $now_tz->getTimestamp(), 'timeRemainingSeconds' => max( 0, (int) ( $end_tz->getTimestamp() - $now_tz->getTimestamp() ) ), ); if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) { $goal_row_for_vitality = array( 'id' => (int) $goal_id, 'points_per_completion' => isset( $goal['points_per_completion'] ) ? (int) $goal['points_per_completion'] : 0, 'target' => isset( $goal['target'] ) ? (int) $goal['target'] : 0, 'period_unit' => (string) $unit, 'period_span' => (int) $span, 'goal_type' => isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : 'positive', 'threshold' => isset( $goal['threshold'] ) && is_numeric( $goal['threshold'] ) ? (int) $goal['threshold'] : null, 'sleep_tracking_enabled' => isset( $goal['sleep_tracking_enabled'] ) ? (int) $goal['sleep_tracking_enabled'] : 0, 'sleep_rollover_time' => isset( $goal['sleep_rollover_time'] ) && is_string( $goal['sleep_rollover_time'] ) ? (string) $goal['sleep_rollover_time'] : '', ); try { $v = TCT_Vitality::compute_for_goal( $user_id, $goal_row_for_vitality, $now_tz ); if ( is_array( $v ) ) { $response['vitalityPayload'] = $v; } } catch ( Exception $e ) { } } TCT_Utils::send_json_success( $response ); } public function handle_experimental_settings_schema_status_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'code' => 'not_logged_in', 'message' => 'Not logged in.', ), 401 ); } if ( ! current_user_can( 'manage_options' ) ) { TCT_Utils::send_json_error( array( 'code' => 'forbidden', 'message' => 'Forbidden.', ), 403 ); } TCT_Utils::enforce_ajax_nonce( 'tct_experimental_settings_schema_status', 'nonce' ); if ( ! class_exists( 'TCT_Admin' ) || ! is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) ) { TCT_Utils::send_json_error( array( 'code' => 'missing_dependency', 'message' => 'Experimental feature gate is unavailable.', ), 500 ); } if ( ! TCT_Admin::is_experimental_features_enabled() ) { TCT_Utils::send_json_error( array( 'code' => 'feature_disabled', 'message' => 'Experimental features are disabled.', ), 403 ); } $installed = (int) get_option( TCT_Admin::OPTION_NAME_SETTINGS_SCHEMA_VERSION, 0 ); $target = (int) TCT_Admin::SETTINGS_SCHEMA_VERSION; TCT_Utils::send_json_success( array( 'ok' => true, 'installedVersion' => (int) $installed, 'targetVersion' => (int) $target, 'needsMigration' => (bool) ( $installed < $target ), ) ); } public function handle_experimental_settings_schema_migrate_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'code' => 'not_logged_in', 'message' => 'Not logged in.', ), 401 ); } if ( ! current_user_can( 'manage_options' ) ) { TCT_Utils::send_json_error( array( 'code' => 'forbidden', 'message' => 'Forbidden.', ), 403 ); } $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : 'GET'; if ( 'POST' !== $method ) { TCT_Utils::send_json_error( array( 'code' => 'method_not_allowed', 'message' => 'Method not allowed.', ), 405 ); } TCT_Utils::enforce_ajax_nonce( 'tct_experimental_settings_schema_migrate', 'nonce' ); if ( ! class_exists( 'TCT_Admin' ) || ! is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) ) { TCT_Utils::send_json_error( array( 'code' => 'missing_dependency', 'message' => 'Experimental feature gate is unavailable.', ), 500 ); } if ( ! TCT_Admin::is_experimental_features_enabled() ) { TCT_Utils::send_json_error( array( 'code' => 'feature_disabled', 'message' => 'Experimental features are disabled.', ), 403 ); } $before = (int) get_option( TCT_Admin::OPTION_NAME_SETTINGS_SCHEMA_VERSION, 0 ); $target = (int) TCT_Admin::SETTINGS_SCHEMA_VERSION; if ( $before >= $target ) { TCT_Utils::send_json_success( array( 'ok' => true, 'beforeVersion' => (int) $before, 'afterVersion' => (int) $before, 'targetVersion' => (int) $target, 'didMigrate' => false, 'backupCreated' => false, 'backupFilename' => '', 'backupError' => '', 'message' => 'Settings schema already up to date.', ) ); } $lock_key = 'tct_settings_schema_migrate_lock_v1'; $lock_ttl = 120; $existing_lock = get_transient( $lock_key ); if ( is_array( $existing_lock ) && isset( $existing_lock['ts'] ) ) { $age = time() - (int) $existing_lock['ts']; if ( $age >= 0 && $age < $lock_ttl ) { TCT_Utils::send_json_error( array( 'code' => 'migration_in_progress', 'message' => 'Settings schema migration is already running. Please wait a moment and try again.', ), 409 ); } } set_transient( $lock_key, array( 'ts' => time(), 'rid' => class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'request_id' ) ) ? TCT_Utils::request_id() : '', ), $lock_ttl ); $details = null; if ( is_callable( array( 'TCT_Admin', 'maybe_upgrade_settings_schema' ) ) ) { $details = TCT_Admin::maybe_upgrade_settings_schema( true ); } else { delete_transient( $lock_key ); TCT_Utils::send_json_error( array( 'code' => 'missing_dependency', 'message' => 'Migration runner is unavailable.', ), 500 ); } $after = (int) get_option( TCT_Admin::OPTION_NAME_SETTINGS_SCHEMA_VERSION, 0 ); $backup_filename = ''; $backup_error = ''; if ( is_array( $details ) ) { if ( isset( $details['backupFilename'] ) && is_string( $details['backupFilename'] ) && '' !== trim( $details['backupFilename'] ) ) { $backup_filename = sanitize_file_name( (string) $details['backupFilename'] ); } if ( isset( $details['backupError'] ) && is_string( $details['backupError'] ) && '' !== trim( $details['backupError'] ) ) { $backup_error = (string) $details['backupError']; } } if ( $after < $before ) { delete_transient( $lock_key ); TCT_Utils::send_json_error( array( 'code' => 'migration_failed', 'message' => 'Settings schema version regressed unexpectedly.', ), 500 ); } if ( $after < $target ) { delete_transient( $lock_key ); TCT_Utils::send_json_error( array( 'code' => 'migration_incomplete', 'message' => 'Settings schema migration did not complete.', ), 500 ); } $did_migrate = ( $after > $before ); delete_transient( $lock_key ); TCT_Utils::send_json_success( array( 'ok' => true, 'beforeVersion' => (int) $before, 'afterVersion' => (int) $after, 'targetVersion' => (int) $target, 'didMigrate' => (bool) $did_migrate, 'backupCreated' => (bool) ( $did_migrate && '' !== $backup_filename ), 'backupFilename' => (string) $backup_filename, 'backupError' => (string) $backup_error, 'message' => $did_migrate ? 'Settings schema migrated.' : 'Settings schema already up to date.', ) ); } public function handle_sleep_clear_cycle_ajax() { TCT_Utils::enforce_ajax_nonce( 'tct_sleep_clear_cycle', 'nonce' ); $user_id = get_current_user_id(); $goal_id = isset( $_POST['goal_id'] ) ? (int) wp_unslash( $_POST['goal_id'] ) : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing goal.' ), 400 ); } $sleep_date = isset( $_POST['sleep_date'] ) ? sanitize_text_field( wp_unslash( $_POST['sleep_date'] ) ) : ''; $sleep_date = trim( (string) $sleep_date ); if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $sleep_date ) ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid sleep date.' ), 400 ); } if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_sleep_cycles' ) ) { TCT_Utils::send_json_error( array( 'message' => 'Sleep tracking database is unavailable.' ), 500 ); } global $wpdb; $sleep_table = TCT_DB::table_sleep_cycles(); $sleep_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $sleep_table ) ); if ( $sleep_table_exists ) { $wpdb->delete( $sleep_table, array( 'user_id' => (int) $user_id, 'goal_id' => (int) $goal_id, 'sleep_date' => (string) $sleep_date, ), array( '%d', '%d', '%s' ) ); } $source_ref = 'sleep:' . (int) $goal_id . ':' . (string) $sleep_date; $completions_table = TCT_DB::table_completions(); $completion_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, source, source_ref FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = 'sleep' AND source_ref = %s LIMIT 1", $user_id, $goal_id, $source_ref ), ARRAY_A ); if ( is_array( $completion_row ) && ! empty( $completion_row['id'] ) ) { $wpdb->delete( $completions_table, array( 'id' => (int) $completion_row['id'], 'user_id' => (int) $user_id, ), array( '%d', '%d' ) ); if ( method_exists( 'TCT_DB', 'table_ledger' ) ) { $ledger_table = TCT_DB::table_ledger(); $event_key = 'c_' . sha1( 'sleep:' . $source_ref . ':' . (string) $goal_id ); $wpdb->delete( $ledger_table, array( 'user_id' => (int) $user_id, 'event_key' => (string) $event_key, ), array( '%d', '%s' ) ); } } 
        // Bed-time goals: clearing sleep data invalidates any derived bed-time score for the sleep day.
        $goals_table = TCT_DB::table_goals();
        $bed_goal_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$goals_table} WHERE user_id = %d AND bed_time_enabled = 1", $user_id ) );
        if ( is_array( $bed_goal_ids ) && ! empty( $bed_goal_ids ) ) {
            $ledger_table = method_exists( 'TCT_DB', 'table_ledger' ) ? TCT_DB::table_ledger() : '';
            foreach ( $bed_goal_ids as $bgid_raw ) {
                $bgid = (int) $bgid_raw;
                if ( $bgid <= 0 ) { continue; }
                $bt_source_ref = 'bed_time:' . (int) $bgid . ':' . (string) $sleep_date;
                $bt_row = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT id FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = 'bed_time' AND source_ref = %s LIMIT 1",
                        $user_id,
                        $bgid,
                        $bt_source_ref
                    ),
                    ARRAY_A
                );
                if ( is_array( $bt_row ) && ! empty( $bt_row['id'] ) ) {
                    $wpdb->delete(
                        $completions_table,
                        array(
                            'id'      => (int) $bt_row['id'],
                            'user_id' => (int) $user_id,
                        ),
                        array( '%d', '%d' )
                    );
                }
                if ( '' !== $ledger_table ) {
                    $event_key = 'c_' . sha1( 'bed_time:' . $bt_source_ref . ':' . (string) $bgid );
                    $wpdb->delete(
                        $ledger_table,
                        array(
                            'user_id'   => (int) $user_id,
                            'event_key' => (string) $event_key,
                        ),
                        array( '%d', '%s' )
                    );
                }
            }
        }

        // Wake-time goals: clearing sleep data invalidates any derived wake-time score for the wake-up day.
        $wake_day_ymd = '';
        try {
            $tz = TCT_Utils::wp_timezone();
            $sleep_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $sleep_date . ' 00:00:00', $tz );
            $errs = DateTimeImmutable::getLastErrors();
            if ( $sleep_dt instanceof DateTimeImmutable && ! ( is_array( $errs ) && ( (int) $errs['warning_count'] > 0 || (int) $errs['error_count'] > 0 ) ) ) {
                $wake_day_ymd = $sleep_dt->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d' );
            }
        } catch ( Exception $e ) {
            $wake_day_ymd = '';
        }

        if ( '' !== $wake_day_ymd ) {
            $goals_table = TCT_DB::table_goals();
            $wake_goal_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$goals_table} WHERE user_id = %d AND wake_time_enabled = 1", $user_id ) );

            if ( is_array( $wake_goal_ids ) && ! empty( $wake_goal_ids ) ) {
                $ledger_table = method_exists( 'TCT_DB', 'table_ledger' ) ? TCT_DB::table_ledger() : '';
                foreach ( $wake_goal_ids as $wgid_raw ) {
                    $wgid = (int) $wgid_raw;
                    if ( $wgid <= 0 ) { continue; }

                    $wt_source_ref = 'wake_time:' . (int) $wgid . ':' . (string) $wake_day_ymd;

                    $wt_row = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT id FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND source = 'wake_time' AND source_ref = %s LIMIT 1",
                            $user_id,
                            $wgid,
                            $wt_source_ref
                        ),
                        ARRAY_A
                    );

                    if ( is_array( $wt_row ) && ! empty( $wt_row['id'] ) ) {
                        $wpdb->delete(
                            $completions_table,
                            array(
                                'id'      => (int) $wt_row['id'],
                                'user_id' => (int) $user_id,
                            ),
                            array( '%d', '%d' )
                        );
                    }

                    if ( '' !== $ledger_table ) {
                        $event_key = 'c_' . sha1( 'wake_time:' . $wt_source_ref . ':' . (string) $wgid );
                        $wpdb->delete(
                            $ledger_table,
                            array(
                                'user_id'   => (int) $user_id,
                                'event_key' => (string) $event_key,
                            ),
                            array( '%d', '%s' )
                        );
                    }
                }
            }
        }

TCT_Utils::send_json_success( array( 'stateKey' => 'A', 'sleepDate' => (string) $sleep_date, 'bedTime' => '', 'wakeTime' => '', 'duration' => '', 'isDefault' => false, 'message' => 'Sleep entry cleared.', ) ); } private function tct_compute_domain_and_role_possible_points_for_year( $year, $tz, $goal_rows ) { $year = (int) $year; if ( $year < 1970 ) { $year = 1970; } $start = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', $year ), $tz ); $end = new DateTimeImmutable( sprintf( '%04d-12-31 00:00:00', $year ), $tz ); $days_in_year = (int) $end->format( 'z' ) + 1; $domain_diff = array_fill( 0, $days_in_year + 1, 0 ); $role_diff = array(); if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $g ) { $ppc = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $goal_type_val = isset( $g['goal_type'] ) ? (string) $g['goal_type'] : ''; if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_goal_type_economy_eligible' ) ) ) { if ( ! TCT_Utils::is_goal_type_economy_eligible( $goal_type_val ) ) { continue; } } elseif ( 'positive_no_int' === strtolower( trim( (string) $goal_type_val ) ) ) { continue; } $rid = isset( $g['role_id'] ) ? (int) $g['role_id'] : 0; if ( $rid < 0 ) { $rid = 0; } $enabled_at = isset( $g['points_enabled_at'] ) ? (string) $g['points_enabled_at'] : ''; $enabled_at = is_string( $enabled_at ) ? trim( $enabled_at ) : ''; $start_idx = 0; if ( '' !== $enabled_at && '0000-00-00 00:00:00' !== $enabled_at ) { $enabled_day = TCT_Utils::mysql_utc_to_tz( $enabled_at, $tz, 'Y-m-d' ); if ( is_string( $enabled_day ) && strlen( $enabled_day ) === 10 ) { $enabled_local = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $enabled_day . ' 00:00:00', $tz ); if ( $enabled_local instanceof DateTimeImmutable ) { if ( (int) $enabled_local->format( 'Y' ) > $year ) { continue; } if ( (int) $enabled_local->format( 'Y' ) === $year ) { $start_idx = (int) $enabled_local->format( 'z' ); if ( $start_idx < 0 ) { $start_idx = 0; } if ( $start_idx > $days_in_year - 1 ) { continue; } } } } } $domain_diff[ $start_idx ] += $ppc; $domain_diff[ $days_in_year ] -= $ppc; if ( $rid > 0 ) { if ( ! isset( $role_diff[ $rid ] ) ) { $role_diff[ $rid ] = array_fill( 0, $days_in_year + 1, 0 ); } $role_diff[ $rid ][ $start_idx ] += $ppc; $role_diff[ $rid ][ $days_in_year ] -= $ppc; } } } $domain_out = array(); $running = 0; for ( $i = 0; $i < $days_in_year; $i++ ) { $running += (int) $domain_diff[ $i ]; if ( $running <= 0 ) { continue; } $d = $start->add( new DateInterval( 'P' . (int) $i . 'D' ) ); $domain_out[ $d->format( 'Y-m-d' ) ] = (int) $running; } $roles_out = array(); foreach ( $role_diff as $rid => $diff ) { $rid = (int) $rid; if ( $rid <= 0 || ! is_array( $diff ) ) { continue; } $rmap = array(); $r = 0; for ( $i = 0; $i < $days_in_year; $i++ ) { $r += (int) $diff[ $i ]; if ( $r <= 0 ) { continue; } $d = $start->add( new DateInterval( 'P' . (int) $i . 'D' ) ); $rmap[ $d->format( 'Y-m-d' ) ] = (int) $r; } $roles_out[ $rid ] = $rmap; } return array( 'domain' => $domain_out, 'roles' => $roles_out, ); } public function handle_undo_completion_ajax() { if ( ! is_user_logged_in() ) { TCT_Utils::send_json_error( array( 'message' => 'Not logged in.', ), 401 ); } TCT_Utils::enforce_ajax_nonce( 'tct_undo_completion', 'nonce' ); $user_id = get_current_user_id(); $completion_id = isset( $_POST['completion_id'] ) ? (int) wp_unslash( $_POST['completion_id'] ) : 0; if ( $completion_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Missing completion.', ), 400 ); } global $wpdb; $completions_table = TCT_DB::table_completions(); $ledger_table = TCT_DB::table_ledger(); $row = $wpdb->get_row( $wpdb->prepare( "SELECT id, goal_id, source, source_ref, todoist_task_id, completed_at
                 FROM {$completions_table}
                 WHERE id = %d AND user_id = %d", $completion_id, $user_id ), ARRAY_A ); if ( ! is_array( $row ) || empty( $row['id'] ) ) { TCT_Utils::send_json_error( array( 'message' => 'Completion not found.', ), 404 ); } $goal_id = isset( $row['goal_id'] ) ? (int) $row['goal_id'] : 0; if ( $goal_id <= 0 ) { TCT_Utils::send_json_error( array( 'message' => 'Invalid completion.', ), 400 ); } $source = isset( $row['source'] ) ? strtolower( (string) $row['source'] ) : 'manual'; $source_ref = isset( $row['source_ref'] ) ? (string) $row['source_ref'] : ''; $todoist_task_id = isset( $row['todoist_task_id'] ) ? sanitize_text_field( (string) $row['todoist_task_id'] ) : ''; $sleep_cleared = false; $sleep_cleared_date = ''; $deleted = $wpdb->delete( $completions_table, array( 'id' => (int) $completion_id, 'user_id' => (int) $user_id, ), array( '%d', '%d' ) ); if ( false === $deleted ) { TCT_Utils::send_json_error( array( 'message' => 'Database error while deleting completion.', ), 500 ); } if ( '' !== $source_ref ) { $event_key = 'c_' . sha1( $source . ':' . $source_ref . ':' . (string) $goal_id ); $wpdb->delete( $ledger_table, array( 'user_id' => (int) $user_id, 'event_key' => (string) $event_key, ), array( '%d', '%s' ) ); } if ( 'sleep' === $source && '' !== $source_ref ) { if ( preg_match( '/^sleep:(\d+):(\d{4}-\d{2}-\d{2})$/', $source_ref, $m ) ) { $sr_goal_id = isset( $m[1] ) ? (int) $m[1] : 0; $sr_date = isset( $m[2] ) ? (string) $m[2] : ''; if ( $sr_goal_id > 0 && $sr_goal_id === (int) $goal_id && '' !== $sr_date && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_sleep_cycles' ) ) { $sleep_table = TCT_DB::table_sleep_cycles(); $sleep_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $sleep_table ) ); if ( $sleep_table_exists ) { $del_sleep = $wpdb->delete( $sleep_table, array( 'user_id' => (int) $user_id, 'goal_id' => (int) $goal_id, 'sleep_date' => (string) $sr_date, ), array( '%d', '%d', '%s' ) ); if ( false !== $del_sleep ) { $sleep_cleared = true; $sleep_cleared_date = (string) $sr_date; } } } } } $message = 'Undone.'; $goals_table_early = TCT_DB::table_goals(); $goal_type_check = $wpdb->get_var( $wpdb->prepare( "SELECT goal_type FROM {$goals_table_early} WHERE user_id = %d AND id = %d", $user_id, $goal_id ) ); $goal_type_early = is_string( $goal_type_check ) ? (string) $goal_type_check : 'positive'; $is_negative_early = TCT_Utils::is_negative_goal_type( $goal_type_early ); if ( '' !== $todoist_task_id && ! $is_negative_early ) { $token = TCT_OAuth::get_token( $user_id ); if ( '' !== $token ) { $reopened = TCT_Todoist_API::reopen_task( $token, $todoist_task_id ); if ( is_wp_error( $reopened ) ) { $data = $reopened->get_error_data(); if ( is_array( $data ) && isset( $data['status_code'] ) && 403 === (int) $data['status_code'] ) { $message = 'Undone locally; could not reopen in Todoist (no write access).'; } else { $message = 'Undone locally; could not reopen in Todoist.'; } } else { $message = 'Undone; reopened in Todoist.'; } } else { $message = 'Undone locally.'; } } $horizon_days = TCT_Admin::get_sync_horizon_days(); $now_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ); $since_utc = $now_utc->sub( new DateInterval( 'P' . (int) $horizon_days . 'D' ) ); TCT_Ledger::reconcile_user( $user_id, TCT_Utils::dt_to_mysql_utc( $since_utc ), TCT_Utils::dt_to_mysql_utc( $now_utc ) ); $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $goals_table = TCT_DB::table_goals(); $goal = $wpdb->get_row( $wpdb->prepare( "SELECT id, goal_name, label_name, tracking_mode, is_tracked, target, period_unit, period_span, period_mode, points_per_completion, plant_name, goal_type, threshold FROM {$goals_table} WHERE user_id = %d AND id = %d", $user_id, $goal_id ), ARRAY_A ); if ( ! is_array( $goal ) || empty( $goal['id'] ) ) { $goal = array( 'id' => (int) $goal_id, 'target' => 0, 'period_unit' => 'week', 'period_span' => 1, 'plant_name' => '', 'points_per_completion' => 0, ); } $goal['period_span'] = isset( $goal['period_span'] ) ? max( 1, (int) $goal['period_span'] ) : 1; $vitality_payload = array( 'vitality' => 100, 'target' => isset( $goal['target'] ) ? (int) $goal['target'] : 0, 'achieved' => 0, 'loop_start_utc_mysql' => '', 'loop_end_utc_mysql' => '', 'time_remaining_seconds' => 0, 'time_remaining_label' => '', ); if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) { try { $maybe_vitality = TCT_Vitality::compute_for_goal( $user_id, $goal, $now_tz ); if ( is_array( $maybe_vitality ) ) { $vitality_payload = array_merge( $vitality_payload, $maybe_vitality ); } } catch ( Exception $e ) { } } $goal_count = isset( $vitality_payload['achieved'] ) ? (int) $vitality_payload['achieved'] : 0; $last_completed_raw = (string) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d", $user_id, $goal_id ) ); $last_completed_text = '--'; $last_completed_raw = is_string( $last_completed_raw ) ? trim( $last_completed_raw ) : ''; if ( '' === $last_completed_raw || '0000-00-00 00:00:00' === $last_completed_raw ) { $last_completed_text = 'never'; } else { $ts = strtotime( $last_completed_raw . ' UTC' ); if ( false !== $ts ) { $now_ts = (int) $now_tz->getTimestamp(); $diff_s = $now_ts - (int) $ts; if ( $diff_s >= 0 && $diff_s < 60 ) { $last_completed_text = 'just now'; } else { $abbr = $this->tct_abbrev_time_ago( (int) $ts, (int) $now_ts ); if ( 'just now' === $abbr ) { $last_completed_text = 'just now'; } elseif ( '--' === $abbr ) { $last_completed_text = '--'; } else { $last_completed_text = trim( $abbr ) . ' ago'; } } } } $today_tz = $now_tz->setTime( 0, 0, 0 ); $today_ymd = $today_tz->format( 'Y-m-d' ); $goal_type = isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : 'positive'; $is_negative = TCT_Utils::is_negative_goal_type( $goal_type ); $period_unit = isset( $goal['period_unit'] ) ? sanitize_text_field( (string) $goal['period_unit'] ) : 'week'; $tile_state = $this->compute_goal_tile_state( $goal, $goal_count, $now_tz, $today_tz ); $vitality_value = isset( $vitality_payload['vitality'] ) ? (int) $vitality_payload['vitality'] : 100; if ( ! $is_negative && $vitality_value < 0 ) { $vitality_value = 0; } if ( $vitality_value > 100 ) { $vitality_value = 100; } $vitality_target = isset( $vitality_payload['target'] ) ? (int) $vitality_payload['target'] : 0; if ( $vitality_target < 0 ) { $vitality_target = 0; } $vitality_achieved = isset( $vitality_payload['achieved'] ) ? (int) $vitality_payload['achieved'] : $goal_count; if ( $vitality_achieved < 0 ) { $vitality_achieved = 0; } $vitality_remaining_seconds = isset( $vitality_payload['time_remaining_seconds'] ) ? (int) $vitality_payload['time_remaining_seconds'] : 0; if ( $vitality_remaining_seconds < 0 ) { $vitality_remaining_seconds = 0; } $vitality_remaining_label = isset( $vitality_payload['time_remaining_label'] ) ? (string) $vitality_payload['time_remaining_label'] : ''; $vitality_loop_start_utc = isset( $vitality_payload['loop_start_utc_mysql'] ) ? (string) $vitality_payload['loop_start_utc_mysql'] : ''; $vitality_loop_end_utc = isset( $vitality_payload['loop_end_utc_mysql'] ) ? (string) $vitality_payload['loop_end_utc_mysql'] : ''; $plant_name = isset( $goal['plant_name'] ) ? trim( (string) $goal['plant_name'] ) : ''; $plant_bucket = 0; $plant_image_url = ''; if ( '' !== $plant_name && class_exists( 'TCT_Utils' ) ) { if ( method_exists( 'TCT_Utils', 'vitality_bucket_biased' ) ) { $plant_bucket = (int) TCT_Utils::vitality_bucket_biased( (int) $vitality_value ); } else { $v = (int) $vitality_value; if ( $v < 0 ) { $v = 0; } if ( $v > 100 ) { $v = 100; } $plant_bucket = (int) ( 5 * intdiv( ( $v + 3 ), 5 ) ); if ( $plant_bucket < 0 ) { $plant_bucket = 0; } if ( $plant_bucket > 100 ) { $plant_bucket = 100; } } if ( method_exists( 'TCT_Utils', 'resolve_vitality_plant_image_url' ) ) { $resolved = TCT_Utils::resolve_vitality_plant_image_url( $plant_name, (int) $vitality_value, 'medium' ); if ( $resolved ) { $plant_image_url = (string) $resolved; } } } $points_balance = 0; $points_balance_label = '0'; if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'get_balance' ) ) ) { $points_balance = (int) TCT_Ledger::get_balance( $user_id ); $points_balance_label = function_exists( 'number_format_i18n' ) ? number_format_i18n( (int) $points_balance ) : (string) $points_balance; } TCT_Utils::send_json_success( array( 'ok' => true, 'goalId' => (int) $goal_id, 'lastCompletedText' => (string) $last_completed_text, 'vitality' => (int) $vitality_value, 'target' => (int) $vitality_target, 'achieved' => (int) $vitality_achieved, 'time_remaining_label' => (string) $vitality_remaining_label, 'time_remaining_seconds' => (int) $vitality_remaining_seconds, 'loop_start_utc_mysql' => (string) $vitality_loop_start_utc, 'loop_end_utc_mysql' => (string) $vitality_loop_end_utc, 'plant_name' => (string) $plant_name, 'plant_bucket' => (int) $plant_bucket, 'plant_image_url' => (string) $plant_image_url, 'statusKey' => isset( $tile_state['statusKey'] ) ? (string) $tile_state['statusKey'] : 'on-track', 'statusLabel' => isset( $tile_state['statusLabel'] ) ? (string) $tile_state['statusLabel'] : 'On track', 'paceLine1' => isset( $tile_state['paceLine1'] ) ? (string) $tile_state['paceLine1'] : '', 'paceLine2' => isset( $tile_state['paceLine2'] ) ? (string) $tile_state['paceLine2'] : '', 'unit' => isset( $tile_state['unit'] ) ? (string) $tile_state['unit'] : '', 'goal_type' => isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : 'positive', 'threshold' => isset( $goal['threshold'] ) && is_numeric( $goal['threshold'] ) ? (int) $goal['threshold'] : null, 'pointsBalance' => (int) $points_balance, 'pointsBalanceLabel' => (string) $points_balance_label, 'rewardStatsHtml' => (string) $this->get_reward_stats_table_html( $user_id, true ), 'sleepCleared' => (bool) $sleep_cleared, 'sleepClearedDate' => (string) $sleep_cleared_date, 'message' => (string) $message, ) ); } private function find_active_task_for_label( $token, $label_name ) { $label_name = sanitize_text_field( (string) $label_name ); if ( '' === $label_name ) { return ''; } $filter_label = $label_name; $filter = '@' . $filter_label; if ( preg_match( '/\s|"/', $filter_label ) ) { $filter_safe = str_replace( '"', '\\"', $filter_label ); $filter = '@"' . $filter_safe . '"'; } $tasks = TCT_Todoist_API::get_tasks( $token, array( 'filter' => $filter, ) ); if ( is_wp_error( $tasks ) ) { $tasks = TCT_Todoist_API::get_tasks( $token, array() ); if ( is_wp_error( $tasks ) ) { return ''; } } if ( ! is_array( $tasks ) ) { $tasks = array(); } $candidates = array(); foreach ( $tasks as $t ) { if ( ! is_array( $t ) || empty( $t['id'] ) ) { continue; } $labels = isset( $t['labels'] ) && is_array( $t['labels'] ) ? $t['labels'] : array(); $has_label = false; foreach ( $labels as $ln ) { if ( 0 === strcasecmp( (string) $ln, (string) $label_name ) ) { $has_label = true; break; } } if ( ! $has_label ) { continue; } $candidates[] = $t; } if ( empty( $candidates ) ) { return ''; } usort( $candidates, function ( $a, $b ) { $ad = ''; $bd = ''; if ( isset( $a['due'] ) && is_array( $a['due'] ) && isset( $a['due']['date'] ) ) { $ad = (string) $a['due']['date']; } if ( isset( $b['due'] ) && is_array( $b['due'] ) && isset( $b['due']['date'] ) ) { $bd = (string) $b['due']['date']; } if ( '' !== $ad && '' !== $bd ) { return strcmp( $ad, $bd ); } if ( '' !== $ad ) { return -1; } if ( '' !== $bd ) { return 1; } return 0; } ); return sanitize_text_field( (string) $candidates[0]['id'] ); } private function sanitize_intervals( $decoded ) { $allowed_units = array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ); $allowed_modes = array( 'calendar' ); $seen = array(); $out = array(); foreach ( $decoded as $interval ) { if ( ! is_array( $interval ) ) { continue; } $target = isset( $interval['target'] ) ? (int) $interval['target'] : 0; if ( $target <= 0 ) { continue; } if ( $target > 999999 ) { $target = 999999; } $unit = isset( $interval['period_unit'] ) ? sanitize_text_field( (string) $interval['period_unit'] ) : 'week'; $unit = strtolower( trim( $unit ) ); if ( '' === $unit ) { $unit = 'week'; } $unit_map = array( 'hours' => 'hour', 'daily' => 'day', 'days' => 'day', 'weekly' => 'week', 'weeks' => 'week', 'monthly' => 'month', 'months' => 'month', 'quarterly' => 'quarter', 'quarters' => 'quarter', 'semi-annual' => 'semiannual', 'halfyear' => 'semiannual', 'half-year' => 'semiannual', 'annual' => 'year', 'annually' => 'year', 'yearly' => 'year', 'years' => 'year', ); if ( isset( $unit_map[ $unit ] ) ) { $unit = $unit_map[ $unit ]; } if ( ! in_array( $unit, $allowed_units, true ) ) { $unit = 'week'; } $span = isset( $interval['period_span'] ) ? (int) $interval['period_span'] : 1; if ( $span < 1 ) { $span = 1; } if ( $span > 1000000 ) { $span = 1000000; } $mode = 'calendar'; if ( isset( $interval['period_mode'] ) ) { $maybe_mode = sanitize_text_field( (string) $interval['period_mode'] ); if ( in_array( $maybe_mode, $allowed_modes, true ) ) { $mode = $maybe_mode; } } $key = $unit . ':' . $mode; $seen[ $key ] = array( 'target' => $target, 'period_unit' => $unit, 'period_span' => $span, 'period_mode' => $mode, ); } if ( empty( $seen ) ) { return array(); } $out = array_values( $seen ); $unit_order = array( 'hour' => 0, 'day' => 1, 'week' => 2, 'month' => 3, 'quarter' => 4, 'semiannual' => 5, 'year' => 6, ); usort( $out, function ( $a, $b ) use ( $unit_order ) { $au = isset( $unit_order[ $a['period_unit'] ] ) ? $unit_order[ $a['period_unit'] ] : 99; $bu = isset( $unit_order[ $b['period_unit'] ] ) ? $unit_order[ $b['period_unit'] ] : 99; if ( $au !== $bu ) { return $au <=> $bu; } return 0; } ); return array( $out[0] ); } private function intervals_from_goal_row( $row ) { $intervals = array(); if ( isset( $row['intervals_json'] ) && is_string( $row['intervals_json'] ) && '' !== trim( $row['intervals_json'] ) ) { $decoded = json_decode( $row['intervals_json'], true ); if ( is_array( $decoded ) ) { $intervals = $this->sanitize_intervals( $decoded ); } } if ( empty( $intervals ) ) { $target = isset( $row['target'] ) ? (int) $row['target'] : 0; if ( $target > 0 ) { $intervals = $this->sanitize_intervals( array( array( 'target' => $target, 'period_unit' => isset( $row['period_unit'] ) ? (string) $row['period_unit'] : 'week', 'period_span' => isset( $row['period_span'] ) ? (int) $row['period_span'] : 1, 'period_mode' => isset( $row['period_mode'] ) ? (string) $row['period_mode'] : 'calendar', ), ) ); } } if ( empty( $intervals ) ) { $goal_type = isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : ''; $is_no_interval = false; if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ) { $is_no_interval = TCT_Utils::is_positive_no_interval_goal_type( $goal_type ); } else { $is_no_interval = ( 'positive_no_int' === $goal_type ); } if ( $is_no_interval ) { $unit = isset( $row['period_unit'] ) ? (string) $row['period_unit'] : 'week'; $span = isset( $row['period_span'] ) ? (int) $row['period_span'] : 1; $mode = isset( $row['period_mode'] ) ? (string) $row['period_mode'] : 'calendar'; $unit = sanitize_text_field( (string) $unit ); $unit = strtolower( trim( $unit ) ); if ( ! in_array( $unit, array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ), true ) ) { $unit = 'week'; } $span = max( 1, (int) $span ); $mode = 'calendar'; $intervals = array( array( 'target' => 0, 'period_unit' => $unit, 'period_span' => $span, 'period_mode' => $mode, ), ); } } return $intervals; } private function tct_goal_aliases_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'aliases_json' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_aliases_select_sql() { return $this->tct_goal_aliases_column_exists() ? 'aliases_json' : "'' AS aliases_json"; } function tct_goal_link_url_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'link_url' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_link_url_select_sql() { return $this->tct_goal_link_url_column_exists() ? 'link_url' : "'' AS link_url"; } function tct_goal_notes_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'goal_notes' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_notes_select_sql() { return $this->tct_goal_notes_column_exists() ? 'goal_notes' : "'' AS goal_notes"; } function tct_goal_due_schedule_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'due_schedule_json' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_due_schedule_select_sql() { return $this->tct_goal_due_schedule_column_exists() ? 'due_schedule_json' : "'' AS due_schedule_json"; } function tct_goal_availability_cycle_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'availability_cycle_json' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_availability_cycle_select_sql() { return $this->tct_goal_availability_cycle_column_exists() ? 'availability_cycle_json' : "'' AS availability_cycle_json"; } function tct_goal_interval_anchor_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'interval_anchor_json' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_interval_anchor_select_sql() { return $this->tct_goal_interval_anchor_column_exists() ? 'interval_anchor_json' : "'' AS interval_anchor_json"; } private function tct_goal_composite_config_column_exists() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'composite_config_json' ) ); $cached = ! empty( $col ); return (bool) $cached; } private function tct_goal_composite_config_select_sql() { return $this->tct_goal_composite_config_column_exists() ? 'composite_config_json' : "'' AS composite_config_json"; } private function tct_goal_wake_time_columns_exist() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col1 = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'wake_time_enabled' ) ); $col2 = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'wake_time_target' ) ); $cached = ( ! empty( $col1 ) && ! empty( $col2 ) ); return (bool) $cached; } private function tct_goal_bed_time_columns_exist() { static $cached = null; if ( null !== $cached ) { return (bool) $cached; } global $wpdb; if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) { $cached = false; return false; } $table = TCT_DB::table_goals(); $pattern = $wpdb->esc_like( $table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $cached = false; return false; } $col1 = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'bed_time_enabled' ) ); $col2 = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'bed_time_target' ) ); $cached = ( ! empty( $col1 ) && ! empty( $col2 ) ); return (bool) $cached; } private function tct_goal_wake_time_select_sql() { return $this->tct_goal_wake_time_columns_exist() ? ( $this->tct_goal_bed_time_columns_exist() ? 'wake_time_enabled, wake_time_target, bed_time_enabled, bed_time_target' : "wake_time_enabled, wake_time_target, 0 AS bed_time_enabled, '' AS bed_time_target" ) : ( $this->tct_goal_bed_time_columns_exist() ? "0 AS wake_time_enabled, '' AS wake_time_target, bed_time_enabled, bed_time_target" : "0 AS wake_time_enabled, '' AS wake_time_target, 0 AS bed_time_enabled, '' AS bed_time_target" ); } function goal_notes_from_goal_row( $row ) { $notes = ''; if ( is_array( $row ) && isset( $row['goal_notes'] ) && is_string( $row['goal_notes'] ) ) { $notes = (string) $row['goal_notes']; } return $notes; } function link_url_from_goal_row( $row ) { $url = ''; if ( is_array( $row ) && isset( $row['link_url'] ) && is_string( $row['link_url'] ) ) { $url = trim( (string) $row['link_url'] ); } return $url; } private function goal_link_allowed_protocols() { return array( 'http', 'https', 'tel', 'sms', 'intent', 'android-app' ); } private function goal_link_output_attr_value( $url ) { $url = is_string( $url ) ? trim( $url ) : ''; if ( '' === $url ) { return ''; } if ( 0 === stripos( $url, 'tel:' ) || 0 === stripos( $url, 'sms:' ) ) { $sanitized = esc_url_raw( $url, $this->goal_link_allowed_protocols() ); return is_string( $sanitized ) ? trim( $sanitized ) : ''; } return $this->normalize_goal_link_url_input( $url ); } private function is_android_package_name( $value ) { if ( ! is_string( $value ) ) { return false; } $value = trim( strtolower( $value ) ); if ( '' === $value ) { return false; } return 1 === preg_match( '/^[a-z][a-z0-9_]*(\.[a-z][a-z0-9_]*){2,}$/', $value ); } private function build_android_package_launch_url( $package_name ) { $package_name = trim( strtolower( (string) $package_name ) ); if ( ! $this->is_android_package_name( $package_name ) ) { return ''; } return 'android-app://' . $package_name; } private function normalize_goal_link_url_input( $raw ) { if ( is_array( $raw ) || is_object( $raw ) ) { return ''; } $url = trim( (string) $raw ); if ( '' === $url ) { return ''; } if ( strlen( $url ) > 1024 ) { $url = substr( $url, 0, 1024 ); } if ( $this->is_android_package_name( $url ) ) { return $this->build_android_package_launch_url( $url ); } if ( 0 === strpos( $url, '//' ) ) { $url = 'https:' . $url; } elseif ( ! preg_match( '#^[a-z][a-z0-9+\-.]*:#i', $url ) ) { if ( 0 !== strpos( $url, '/' ) && 0 !== strpos( $url, '#' ) && 0 !== strpos( $url, '?' ) ) { $url = 'https://' . ltrim( $url, '/' ); } } $sanitized = esc_url_raw( $url, $this->goal_link_allowed_protocols() ); $sanitized = is_string( $sanitized ) ? trim( $sanitized ) : ''; if ( '' === $sanitized ) { return ''; } $parts = wp_parse_url( $sanitized ); if ( ! is_array( $parts ) ) { if ( 0 === stripos( $sanitized, 'intent:' ) && false !== stripos( $sanitized, '#Intent;' ) ) { return $sanitized; } return ''; } $scheme = isset( $parts['scheme'] ) ? strtolower( (string) $parts['scheme'] ) : ''; if ( in_array( $scheme, array( 'http', 'https' ), true ) ) { if ( empty( $parts['host'] ) ) { return ''; } return $sanitized; } if ( 'android-app' === $scheme ) { if ( ! empty( $parts['host'] ) && $this->is_android_package_name( (string) $parts['host'] ) ) { return $sanitized; } return ''; } if ( 'intent' === $scheme ) { if ( false === stripos( $sanitized, '#Intent;' ) ) { return ''; } return $sanitized; } return ''; } function aliases_from_goal_row( $row ) { $aliases = array(); if ( isset( $row['aliases_json'] ) && is_string( $row['aliases_json'] ) && '' !== trim( $row['aliases_json'] ) ) { $decoded = json_decode( $row['aliases_json'], true ); if ( is_array( $decoded ) ) { foreach ( $decoded as $a ) { if ( ! is_string( $a ) ) { continue; } $a = trim( sanitize_text_field( $a ) ); if ( '' === $a ) { continue; } if ( strlen( $a ) > 120 ) { $a = substr( $a, 0, 120 ); } $aliases[] = $a; } } } if ( empty( $aliases ) ) { return array(); } $out = array(); $seen = array(); foreach ( $aliases as $a ) { $k = strtolower( $a ); if ( isset( $seen[ $k ] ) ) { continue; } $seen[ $k ] = true; $out[] = $a; if ( count( $out ) >= 20 ) { break; } } return $out; } public function render_shortcode( $atts ) { if ( ! is_user_logged_in() ) { return '<div class="tct-card tct-alert">Please log in to view your dashboard.</div>'; } $user_id = get_current_user_id(); $redirect_here = TCT_Utils::current_url(); $default_tab = 'dashboard'; if ( isset( $_GET['tct_stats_week_offset'] ) || isset( $_GET['tct_stats_month_offset'] ) || isset( $_GET['tct_stats_year_offset'] ) ) { $default_tab = 'statistics'; } $output = ''; $output .= $this->render_messages(); $today_ymd = ( new DateTimeImmutable( 'now', TCT_Utils::wp_timezone() ) )->format( 'Y-m-d' ); $output .= '<div class="tct-dashboard"' . $this->tct_composite_root_attrs_html( $today_ymd ) . '>'; $output .= '<div class="tct-header">'; $output .= '<h2 class="tct-title">Todoist Category Tracker</h2>'; $output .= '</div>'; $output .= '<div class="tct-tabs tct-main-tabs" data-tct-tabs data-tct-default-tab="' . esc_attr( $default_tab ) . '">'; $output .= '<div class="tct-main-tab-nav-row">'; $output .= '<div class="tct-tab-nav" role="tablist" aria-label="Todoist Category Tracker sections">'; $output .= $this->render_tab_button( 'dashboard', 'Dashboard', $default_tab ); $output .= $this->render_tab_button( 'ledger', 'Ledger', $default_tab ); $output .= $this->render_tab_button( 'goals', 'Goals', $default_tab ); $output .= $this->render_tab_button( 'roles', 'Roles', $default_tab ); $output .= $this->render_tab_button( 'domains', 'Domains', $default_tab ); $output .= $this->render_tab_button( 'statistics', 'Statistics', $default_tab ); $output .= $this->render_tab_button( 'connection', 'Settings', $default_tab ); $output .= '</div>'; $output .= '<div class="tct-nav-pills">'; $reward_pill = $this->render_reward_nav_pill( $user_id ); if ( '' !== $reward_pill ) { $output .= $reward_pill; } else { $output .= $this->render_points_nav_pill( $user_id ); } $output .= '</div>'; $output .= '</div>'; $output .= '<div class="tct-tab-panels">'; $output .= $this->render_tab_panel_start( 'dashboard', $default_tab ); $output .= $this->render_kpi_tiles( $user_id ); $output .= '</div>'; $output .= $this->render_tab_panel_start( 'ledger', $default_tab ); $output .= $this->render_ledger_panel( $user_id, $redirect_here ); $output .= '</div>'; $output .= $this->render_tab_panel_start( 'goals', $default_tab ); $output .= $this->render_settings( $user_id, $redirect_here ); $output .= '</div>'; $output .= $this->render_tab_panel_start( 'roles', $default_tab ); $output .= $this->render_roles_panel( $user_id, $redirect_here ); $output .= '</div>'; $output .= $this->render_tab_panel_start( 'domains', $default_tab ); $output .= $this->render_domains_panel( $user_id, $redirect_here ); $output .= '</div>'; $output .= $this->render_tab_panel_start( 'statistics', $default_tab ); $output .= $this->render_statistics_panel( $user_id, $redirect_here ); $output .= '</div>'; $output .= $this->render_tab_panel_start( 'connection', $default_tab ); $output .= $this->render_connection_card( $user_id, $redirect_here ); $output .= '</div>'; $output .= '</div>'; $output .= '</div>'; if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) && isset( $_GET['tct_vitality_debug'] ) ) { $debug = array(); if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'debug_scenarios' ) ) ) { try { $debug = TCT_Vitality::debug_scenarios(); } catch ( Exception $e ) { $debug = array( 'error' => 'Exception: ' . $e->getMessage(), ); } } else { $debug = array( 'error' => 'TCT_Vitality::debug_scenarios() not available.', ); } $json = wp_json_encode( $debug, JSON_PRETTY_PRINT ); if ( ! is_string( $json ) ) { $json = ''; } $output .= '<div class="tct-card">'; $output .= '<h3>Vitality debug</h3>'; $output .= '<p class="tct-muted">Admin-only debug output. Remove the <code>tct_vitality_debug</code> query flag to hide.</p>'; $output .= '<pre style="white-space:pre-wrap;max-width:100%;overflow:auto;">' . esc_html( $json ) . '</pre>'; $output .= '</div>'; } $output .= '</div>'; return $output; } private function render_tab_button( $key, $label, $active_key ) { $is_active = ( $key === $active_key ); $classes = 'tct-tab' . ( $is_active ? ' tct-tab-active' : '' ); $tab_id = 'tct-tab-btn-' . $key; $panel_id = 'tct-tab-panel-' . $key; return '<button type="button" class="' . esc_attr( $classes ) . '" data-tct-tab="' . esc_attr( $key ) . '" role="tab" id="' . esc_attr( $tab_id ) . '" aria-controls="' . esc_attr( $panel_id ) . '" aria-selected="' . ( $is_active ? 'true' : 'false' ) . '" tabindex="' . ( $is_active ? '0' : '-1' ) . '">' . esc_html( $label ) . '</button>'; } private function render_tab_panel_start( $key, $active_key ) { $is_active = ( $key === $active_key ); $classes = 'tct-tab-panel' . ( $is_active ? ' tct-tab-panel-active' : '' ); $tab_id = 'tct-tab-btn-' . $key; $panel_id = 'tct-tab-panel-' . $key; $hidden = $is_active ? '' : ' hidden="hidden"'; return '<div class="' . esc_attr( $classes ) . '" data-tct-panel="' . esc_attr( $key ) . '" role="tabpanel" id="' . esc_attr( $panel_id ) . '" aria-labelledby="' . esc_attr( $tab_id ) . '"' . $hidden . '>'; } private function render_connection_card( $user_id, $redirect_here ) { $html = ''; $html .= '<div class="tct-card">'; $html .= '<h3>Settings</h3>'; $default_start = (int) get_option( 'start_of_week', 1 ); if ( 0 !== $default_start && 1 !== $default_start ) { $default_start = 1; } $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-week-start-select"><strong>Week starts on</strong></label>'; $html .= '<select id="tct-week-start-select" class="tct-select" data-tct-week-start-select="1" data-default="' . esc_attr( $default_start ) . '">'; $html .= '<option value="0">Sunday</option>'; $html .= '<option value="1">Monday</option>'; $html .= '</select>'; $html .= '<p class="tct-muted tct-settings-help">Used for week tick marks on the month heatmap.</p>'; $html .= '</div>'; $html .= $this->render_goal_order_settings_sections( $user_id ); if ( current_user_can( 'manage_options' ) ) { $current_tz = ''; if ( class_exists( 'TCT_Admin' ) && is_callable( array( 'TCT_Admin', 'get_timezone' ) ) ) { $current_tz = TCT_Admin::get_timezone(); } $composite_enabled = ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'is_composite_goals_enabled' ) ) ) ? (bool) TCT_Plugin::is_composite_goals_enabled() : false; $composite_status = isset( $_GET['tct_composite_feature'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_composite_feature'] ) ) : ''; $composite_msg = isset( $_GET['tct_composite_feature_msg'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['tct_composite_feature_msg'] ) ) ) : ''; $tz_status = isset( $_GET['tct_tz'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_tz'] ) ) : ''; $tz_msg = isset( $_GET['tct_tz_msg'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['tct_tz_msg'] ) ) ) : ''; if ( 'success' === $composite_status ) { $html .= '<div class="tct-notice tct-notice-success" style="margin:8px 0;padding:6px 10px;background:#d1e7dd;border-radius:4px;color:#0f5132;">' . esc_html( $composite_msg ? $composite_msg : 'Composite parent goals setting saved.' ) . '</div>'; } elseif ( 'error' === $composite_status && $composite_msg ) { $html .= '<div class="tct-notice tct-notice-error" style="margin:8px 0;padding:6px 10px;background:#f8d7da;border-radius:4px;color:#842029;">' . esc_html( $composite_msg ) . '</div>'; } if ( 'success' === $tz_status ) { $html .= '<div class="tct-notice tct-notice-success" style="margin:8px 0;padding:6px 10px;background:#d1e7dd;border-radius:4px;color:#0f5132;">Timezone saved.</div>'; } elseif ( 'error' === $tz_status && $tz_msg ) { $html .= '<div class="tct-notice tct-notice-error" style="margin:8px 0;padding:6px 10px;background:#f8d7da;border-radius:4px;color:#842029;">' . esc_html( $tz_msg ) . '</div>'; } $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Composite parent goals</h4>'; $html .= '<div class="tct-muted tct-settings-help" style="margin-top:-6px;">Turn this on to show the <strong>Composite parent</strong> goal type in the Add Goal modal and enable parent goal behavior across desktop, mobile, ledger, and cron.</div>'; $html .= '<div class="tct-muted tct-settings-help" style="margin:6px 0 0 0;">Status: ' . ( $composite_enabled ? '<strong>Enabled</strong>' : '<strong>Disabled</strong>' ) . '</div>'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; $html .= '<input type="hidden" name="action" value="tct_composite_feature_save" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= wp_nonce_field( 'tct_composite_feature_save', '_wpnonce', true, false ); $html .= '<input type="hidden" name="tct_composite_goals_enabled" value="0" />'; $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-composite-goals-enabled"><strong>Enable composite parent goals</strong></label>'; $html .= '<label for="tct-composite-goals-enabled" style="display:inline-flex;align-items:center;gap:8px;flex-wrap:wrap;">'; $html .= '<input id="tct-composite-goals-enabled" type="checkbox" name="tct_composite_goals_enabled" value="1"' . checked( $composite_enabled, true, false ) . ' />'; $html .= '<span>Show the parent goal type and parent controls.</span>'; $html .= '</label>'; $html .= '<p class="tct-muted tct-settings-help">After saving, reload the Add Goal modal. You should then see the composite parent option.</p>'; $html .= '</div>'; $html .= '<p><button type="submit" class="button button-primary">Save composite setting</button></p>'; $html .= '</form>'; $html .= '<hr class="tct-divider" />'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; $html .= '<input type="hidden" name="action" value="tct_timezone_save" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= wp_nonce_field( 'tct_timezone_save', '_wpnonce', true, false ); $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-timezone-select"><strong>Plugin Timezone</strong></label>'; $html .= '<select id="tct-timezone-select" name="tct_timezone" class="tct-select" style="min-width:280px;">'; $wp_tz = get_option( 'timezone_string', '' ); if ( ! $wp_tz ) { $offset = (float) get_option( 'gmt_offset', 0 ); $sign = ( $offset >= 0 ) ? '+' : '-'; $h = abs( (int) $offset ); $m = abs( (int) round( ( $offset - (int) $offset ) * 60 ) ); $wp_tz = sprintf( 'UTC%s%d:%02d', $sign, $h, $m ); } $sel_default = ( '' === $current_tz ) ? ' selected="selected"' : ''; $html .= '<option value=""' . $sel_default . '>-- Use WordPress site timezone (' . esc_html( $wp_tz ) . ') --</option>'; if ( function_exists( 'wp_timezone_choice' ) ) { $choice_val = $current_tz ? $current_tz : '___none___'; $html .= wp_timezone_choice( $choice_val, get_user_locale() ); } else { $zones = timezone_identifiers_list(); foreach ( $zones as $zone ) { $sel_attr = ( $current_tz === $zone ) ? ' selected="selected"' : ''; $html .= '<option value="' . esc_attr( $zone ) . '"' . $sel_attr . '>' . esc_html( str_replace( '_', ' ', $zone ) ) . '</option>'; } } $html .= '</select>'; $html .= '<p class="tct-muted tct-settings-help">Timezone used for goal interval boundaries (day/week/month rollovers).</p>'; $html .= '<p class="tct-muted tct-settings-help" style="color:#b26200;"><strong>Note:</strong> Active goal intervals will immediately recalculate using the new timezone. Past completion timestamps are not modified.</p>'; $html .= '</div>'; $html .= '<p><button type="submit" class="button button-primary">Save timezone</button></p>'; $html .= '</form>'; } $openai_key_raw = get_option( 'tct_openai_api_key', '' ); $openai_key_set = is_string( $openai_key_raw ) && '' !== trim( $openai_key_raw ); $oa_status = isset( $_GET['tct_openai'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_openai'] ) ) : ''; $oa_msg = isset( $_GET['tct_openai_msg'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['tct_openai_msg'] ) ) ) : ''; $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">OpenAI</h4>'; $html .= '<div class="tct-muted tct-settings-help" style="margin-top:-6px;">Provide an OpenAI API key to enable <strong>Suggest aliases</strong> in the Goal editor (helps search match small wording differences like <em>Run</em> vs <em>Ran</em>).</div>'; if ( 'success' === $oa_status ) { $html .= '<div class="tct-notice tct-notice-success" style="margin:8px 0;padding:6px 10px;background:#d1e7dd;border-radius:4px;color:#0f5132;">' . esc_html( $oa_msg ? $oa_msg : 'OpenAI API key saved.' ) . '</div>'; } elseif ( 'error' === $oa_status && $oa_msg ) { $html .= '<div class="tct-notice tct-notice-error" style="margin:8px 0;padding:6px 10px;background:#f8d7da;border-radius:4px;color:#842029;">' . esc_html( $oa_msg ) . '</div>'; } $html .= '<div class="tct-muted tct-settings-help" style="margin:6px 0 0 0;">Status: ' . ( $openai_key_set ? '<strong>Key saved</strong>' : '<strong>No key set</strong>' ) . '</div>'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; $html .= '<input type="hidden" name="action" value="tct_openai_save_key" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= wp_nonce_field( 'tct_openai_save_key', '_wpnonce', true, false ); $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-openai-key"><strong>OpenAI API key</strong></label>'; $html .= '<input id="tct-openai-key" type="password" name="tct_openai_api_key" class="tct-select" style="min-width:320px;" value="" placeholder="' . esc_attr( $openai_key_set ? 'Paste to replace (leave blank to clear)' : 'sk-...' ) . '" autocomplete="off" />'; $html .= '</div>'; $html .= '<p><button type="submit" class="button button-primary">Save OpenAI key</button></p>'; $html .= '</form>'; if ( class_exists( 'TCT_DB' ) ) { $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Archived goals</h4>'; $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-archived-goal-search"><strong>Search</strong></label>'; $html .= '<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">'; $html .= '<input type="search" id="tct-archived-goal-search" class="tct-select" style="min-width:280px;" placeholder="Search archived goals..." autocomplete="off" data-tct-archived-goal-search="1" />'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="tct-inline-form" style="margin:0;">'; $html .= '<input type="hidden" name="action" value="tct_goal_restore" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="goal_id" value="" data-tct-archived-goal-id="1" />'; $html .= wp_nonce_field( 'tct_goal_restore', '_wpnonce', true, false ); $html .= '<button type="submit" class="button" data-tct-archived-goal-restore-btn="1" disabled="disabled">Restore</button>'; $html .= '</form>'; $html .= '</div>'; $html .= '<p class="tct-muted tct-settings-help">Archived goals are hidden from active dashboards. Search here to restore them.</p>'; $html .= '<p class="tct-muted" data-tct-archived-goal-selected-label="1" hidden="hidden"></p>'; $html .= '</div>'; $html .= '<div class="tct-table-wrap" data-tct-archived-goal-results="1" hidden="hidden">'; $html .= '<table class="tct-table">'; $html .= '<thead><tr><th>Goal</th><th>Last updated</th></tr></thead>'; $html .= '<tbody data-tct-archived-goal-results-body="1"></tbody>'; $html .= '</table></div>'; $html .= '<p class="tct-muted" data-tct-archived-goal-empty="1">Type to filter archived goals, or click the field to browse all archived goals.</p>'; } $html .= $this->render_sleep_settings_section( $user_id ); $html .= $this->render_reward_settings_section( $user_id, $redirect_here ); if ( class_exists( 'TCT_Backup' ) && is_callable( array( 'TCT_Backup', 'render_settings_section' ) ) ) { $html .= TCT_Backup::render_settings_section(); } $html .= $this->render_experimental_settings_schema_section(); $html .= '</div>'; return $html; } private function render_experimental_settings_schema_section() { if ( ! current_user_can( 'manage_options' ) ) { return ''; } if ( ! class_exists( 'TCT_Admin' ) || ! is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) ) { return ''; } if ( ! TCT_Admin::is_experimental_features_enabled() ) { return ''; } $html = ''; $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Experimental: Settings Schema</h4>'; $html .= '<div class="tct-backup-section tct-experimental-settings-schema-section" data-tct-exp-settings-schema-section="1" aria-busy="false">'; $html .= '<p class="tct-muted">Admin-only testing tools. These actions are safe to run multiple times.</p><p class="tct-muted">When a migration makes changes, a pre-migration snapshot backup is created (type: migration). You can restore it from the Backup section.</p>'; $html .= '<div class="tct-backup-actions">'; $html .= '<button type="button" class="button" data-tct-exp-schema-status-btn="1">Check status</button>'; $html .= '<button type="button" class="button button-primary" data-tct-exp-schema-migrate-btn="1">Run migration</button>'; $html .= '<span class="tct-muted" data-tct-exp-schema-inline-status="1" aria-live="polite" aria-atomic="true" role="status">No requests yet.</span>'; $html .= '</div>'; $html .= '<div class="tct-muted" data-tct-exp-schema-output="1" aria-live="polite" aria-atomic="true" role="status" style="margin-top:8px;">'; $html .= '<p class="tct-muted" style="margin:0;">Click <strong>Check status</strong> to load the current schema version.</p>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_sleep_settings_section( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } $html = ''; $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Sleep</h4>'; if ( ! class_exists( 'TCT_DB' ) ) { $html .= '<p class="tct-muted">Enable sleep tracking on a goal to start logging.</p>'; return $html; } $sleep_enabled_goal_id = 0; if ( method_exists( 'TCT_DB', 'table_goals' ) ) { global $wpdb; $goals_table = TCT_DB::table_goals(); $col = $wpdb->get_var( "SHOW COLUMNS FROM {$goals_table} LIKE 'sleep_tracking_enabled'" ); if ( $col ) { $sleep_enabled_goal_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 AND sleep_tracking_enabled = 1 ORDER BY id ASC LIMIT 1", $user_id ) ); } } if ( $sleep_enabled_goal_id <= 0 ) { $html .= '<p class="tct-muted">Enable sleep tracking on a goal to start logging.</p>'; return $html; } if ( ! method_exists( 'TCT_DB', 'list_completed_sleep_cycles' ) ) { $html .= '<p class="tct-muted">Enable sleep tracking on a goal to start logging.</p>'; return $html; } if ( method_exists( 'TCT_DB', 'table_sleep_cycles' ) ) { global $wpdb; $sleep_table = TCT_DB::table_sleep_cycles(); $pattern = $wpdb->esc_like( $sleep_table ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { $html .= '<p class="tct-muted">Enable sleep tracking on a goal to start logging.</p>'; return $html; } } $export_url = add_query_arg( array( 'action' => 'tct_sleep_export_csv', ), admin_url( 'admin-post.php' ) ); $export_url = wp_nonce_url( $export_url, 'tct_sleep_export_csv', '_wpnonce' ); $html .= '<p><a class="button" href="' . esc_url( $export_url ) . '">Download CSV</a></p>'; $rows = TCT_DB::list_completed_sleep_cycles( $user_id, 0, 0, 0 ); if ( ! is_array( $rows ) || empty( $rows ) ) { $html .= '<p class="tct-muted">No completed sleep cycles yet. Complete a sleep cycle (bedtime + wake-time) to see it here.</p>'; return $html; } $html .= '<div class="tct-table-wrap">'; $html .= '<table class="tct-table">'; $html .= '<thead><tr><th>Night of</th><th>Bedtime</th><th>Waketime</th><th>Duration</th></tr></thead>'; $html .= '<tbody>'; foreach ( $rows as $r ) { $sleep_date = isset( $r['sleep_date'] ) ? (string) $r['sleep_date'] : ''; $bed_time = isset( $r['bed_time'] ) ? (string) $r['bed_time'] : ''; $wake_time = isset( $r['wake_time'] ) ? (string) $r['wake_time'] : ''; $duration = isset( $r['duration_hhmm'] ) ? (string) $r['duration_hhmm'] : ''; if ( '' === $duration && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'calculate_sleep_duration_hhmm' ) ) { $duration = (string) TCT_DB::calculate_sleep_duration_hhmm( $bed_time, $wake_time ); } $night_display = esc_html( $sleep_date ); if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $sleep_date, $m ) ) { try { $tz_hist = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' ); $dt_hist = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $sleep_date . ' 00:00:00', $tz_hist ); if ( $dt_hist instanceof DateTimeImmutable ) { $dt_end_hist = $dt_hist->add( new DateInterval( 'P1D' ) ); $night_display = (int) $m[2] . '/' . (int) $m[3] . "\xe2\x80\x93" . (int) $dt_end_hist->format( 'm' ) . '/' . (int) $dt_end_hist->format( 'd' ); } } catch ( Exception $e ) { } } $html .= '<tr>'; $html .= '<td>' . esc_html( $night_display ) . '</td>'; $html .= '<td>' . esc_html( $bed_time ) . '</td>'; $html .= '<td>' . esc_html( $wake_time ) . '</td>'; $html .= '<td>' . esc_html( $duration ) . '</td>'; $html .= '</tr>'; } $html .= '</tbody>'; $html .= '</table>'; $html .= '</div>'; return $html; } private function render_reward_settings_section( $user_id, $redirect_here ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } if ( ! class_exists( 'TCT_Reward' ) ) { return ''; } $html = ''; $html .= '<hr class="tct-divider" />'; $html .= '<h4 class="tct-settings-heading">Reward</h4>'; if ( method_exists( 'TCT_Reward', 'is_enabled' ) && ! TCT_Reward::is_enabled() ) { $html .= '<p class="tct-muted">Rewards are currently disabled. To enable: add <code>define(\'TCT_FEATURE_REWARDS\', true);</code> to <code>wp-config.php</code>.</p>'; return $html; } $reward = TCT_Reward::get_active_reward( $user_id ); $title = ( is_array( $reward ) && isset( $reward['title'] ) ) ? (string) $reward['title'] : ''; $cost = ( is_array( $reward ) && isset( $reward['cost'] ) ) ? (string) $reward['cost'] : ''; $monthly = ( is_array( $reward ) && isset( $reward['monthly_savings'] ) ) ? (string) $reward['monthly_savings'] : ''; $attachment_id = ( is_array( $reward ) && isset( $reward['attachment_id'] ) ) ? (int) $reward['attachment_id'] : 0; $image_url = ''; if ( $attachment_id > 0 ) { $image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); if ( ! $image_url ) { $image_url = wp_get_attachment_url( $attachment_id ); } } $can_upload = current_user_can( 'upload_files' ); $created_at_utc = ( is_array( $reward ) && isset( $reward['created_at_utc'] ) ) ? (string) $reward['created_at_utc'] : ''; $html .= '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; $html .= '<input type="hidden" name="action" value="tct_reward_save" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= wp_nonce_field( 'tct_reward_save', '_wpnonce', true, false ); $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-reward-title"><strong>Reward title</strong></label>'; $html .= '<input type="text" id="tct-reward-title" name="reward_title" value="' . esc_attr( $title ) . '" class="tct-select" />'; $html .= '<p class="tct-muted tct-settings-help">A short name for what you are saving points for.</p>'; $html .= '</div>'; $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-reward-cost"><strong>Cost</strong></label>'; $html .= '<input type="number" id="tct-reward-cost" name="reward_cost" value="' . esc_attr( $cost ) . '" class="tct-select" step="0.01" min="0" inputmode="decimal" />'; $html .= '<p class="tct-muted tct-settings-help">Total cost in dollars (or your currency). Example: 1000</p>'; $html .= '</div>'; $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-reward-monthly"><strong>Monthly savings</strong></label>'; $html .= '<input type="number" id="tct-reward-monthly" name="reward_monthly_savings" value="' . esc_attr( $monthly ) . '" class="tct-select" step="0.01" min="0" inputmode="decimal" />'; $html .= '<p class="tct-muted tct-settings-help">How much you intend to save per month toward this reward. Example: 200</p>'; $html .= '</div>'; $html .= '<div class="tct-settings-row">'; $html .= '<label for="tct-reward-image"><strong>Reward photo</strong></label>'; if ( ! $can_upload ) { $html .= '<p class="tct-muted tct-settings-help">Your account cannot upload files. Ask an administrator to grant the <code>upload_files</code> capability.</p>'; $html .= '<input type="file" id="tct-reward-image" name="reward_image" class="tct-select" disabled />'; } else { $html .= '<input type="file" id="tct-reward-image" name="reward_image" class="tct-select" accept="image/jpeg,image/png,image/webp" />'; $html .= '<p class="tct-muted tct-settings-help">Upload a JPG, PNG, or WebP image. This does not affect reward math.</p>'; } if ( $image_url ) { $html .= '<div style="margin-top:8px;"><img src="' . esc_url( $image_url ) . '" alt="Reward photo" style="max-width:160px;height:auto;border-radius:8px;" /></div>'; } elseif ( $attachment_id > 0 ) { $html .= '<p class="tct-muted tct-settings-help">Current image could not be found in the media library.</p>'; } $html .= '</div>'; $html .= '<p><button type="submit" class="button button-primary">Save reward</button></p>'; $html .= '</form>'; if ( is_array( $reward ) && ! empty( $reward ) ) { $progress = TCT_Reward::compute_reward_progress( $user_id ); if ( $created_at_utc ) { $html .= '<p class="tct-muted">Start date: <strong>' . esc_html( $created_at_utc ) . ' UTC</strong></p>'; } $errors = ( isset( $progress['errors'] ) && is_array( $progress['errors'] ) ) ? $progress['errors'] : array(); if ( ! empty( $errors ) ) { $html .= '<p class="tct-muted"><strong>Progress:</strong> unavailable</p>'; $html .= '<ul class="tct-muted">'; foreach ( $errors as $err ) { $html .= '<li>' . esc_html( (string) $err ) . '</li>'; } $html .= '</ul>'; } else { $earned = isset( $progress['earned_points'] ) ? (int) $progress['earned_points'] : 0; $target = isset( $progress['target_points'] ) ? (int) $progress['target_points'] : 0; $pct = isset( $progress['progress_pct'] ) ? (float) $progress['progress_pct'] : 0.0; $pct_label = number_format_i18n( $pct, 1 ); $earned_label = number_format_i18n( $earned ); $target_label = number_format_i18n( $target ); $html .= '<p><strong>Progress:</strong> ' . esc_html( $pct_label ) . '% <span class="tct-muted">(' . esc_html( $earned_label ) . ' / ' . esc_html( $target_label ) . ' pts)</span></p>'; $is_earned = ( isset( $progress['is_earned'] ) && $progress['is_earned'] ); if ( $target > 0 && $is_earned ) { $confirm = 'Redeem this reward? This will deduct ' . $target_label . ' points from your balance.'; $confirm_js = str_replace( "'", "\\'", $confirm ); $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; $html .= '<input type="hidden" name="action" value="tct_reward_redeem" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= wp_nonce_field( 'tct_reward_redeem', '_wpnonce', true, false ); $html .= '<p class="tct-muted" style="margin:8px 0 4px;">Redeeming will deduct <strong>' . esc_html( $target_label ) . '</strong> points and move this reward to your Hall of Fame.</p>'; $html .= '<p><button type="submit" class="button" onclick="return confirm(\'' . esc_attr( $confirm_js ) . '\');">Redeem reward (-' . esc_html( $target_label ) . ' pts)</button></p>'; $html .= '</form>'; } } } else { $html .= '<p class="tct-muted">No active reward configured yet.</p>'; } $html .= $this->render_reward_hall_of_fame_section( $user_id ); return $html; } private function render_reward_hall_of_fame_section( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } if ( ! class_exists( 'TCT_Reward' ) || ! method_exists( 'TCT_Reward', 'get_hof' ) ) { return ''; } $hof = TCT_Reward::get_hof( $user_id ); if ( ! is_array( $hof ) ) { $hof = array(); } $hof = array_values( $hof ); if ( ! empty( $hof ) ) { $hof = array_reverse( $hof ); } $max_items = 50; $truncated = false; if ( count( $hof ) > $max_items ) { $hof = array_slice( $hof, 0, $max_items ); $truncated = true; } $html = ''; $html .= '<div class="tct-reward-hof">'; $html .= '<h5 class="tct-reward-hof-heading">Hall of Fame</h5>'; if ( empty( $hof ) ) { $html .= '<p class="tct-muted">No redeemed rewards yet.</p>'; $html .= '</div>'; return $html; } if ( $truncated ) { $html .= '<p class="tct-muted">Showing the most recent ' . esc_html( (string) $max_items ) . ' rewards.</p>'; } $html .= '<div class="tct-reward-hof-list">'; foreach ( $hof as $entry ) { if ( ! is_array( $entry ) ) { continue; } $title = isset( $entry['title'] ) ? sanitize_text_field( (string) $entry['title'] ) : 'Reward'; if ( '' === $title ) { $title = 'Reward'; } $cost = isset( $entry['cost'] ) ? (float) $entry['cost'] : 0.0; $points_cost = isset( $entry['points_cost'] ) ? (int) $entry['points_cost'] : 0; $created_at = isset( $entry['created_at_utc'] ) ? sanitize_text_field( (string) $entry['created_at_utc'] ) : ''; $redeemed_at = isset( $entry['redeemed_at_utc'] ) ? sanitize_text_field( (string) $entry['redeemed_at_utc'] ) : ''; $elapsed_days = isset( $entry['elapsed_days'] ) ? (int) $entry['elapsed_days'] : 0; $elapsed_label = isset( $entry['elapsed_label'] ) ? sanitize_text_field( (string) $entry['elapsed_label'] ) : ''; if ( ( '' === $elapsed_label || $elapsed_days <= 0 ) && '' !== $created_at && '' !== $redeemed_at ) { $start_ts = strtotime( $created_at . ' UTC' ); $end_ts = strtotime( $redeemed_at . ' UTC' ); if ( false !== $start_ts && false !== $end_ts ) { if ( '' === $elapsed_label && method_exists( $this, 'tct_abbrev_time_ago' ) ) { $elapsed_label = $this->tct_abbrev_time_ago( $start_ts, $end_ts ); } if ( $elapsed_days <= 0 ) { $elapsed_days = (int) floor( max( 0, $end_ts - $start_ts ) / 86400 ); } } } $thumb_url = ''; $attachment_id = isset( $entry['attachment_id'] ) ? (int) $entry['attachment_id'] : 0; if ( $attachment_id > 0 ) { $thumb_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); if ( ! $thumb_url ) { $thumb_url = wp_get_attachment_url( $attachment_id ); } } if ( '' === $thumb_url && isset( $entry['image_url'] ) ) { $thumb_url = (string) $entry['image_url']; } $cost_label = number_format_i18n( $cost, 2 ); $points_label = number_format_i18n( (int) $points_cost ); $meta_line = 'Cost: ' . $cost_label . ' * Points: ' . $points_label; $dates = array(); if ( '' !== $created_at ) { $dates[] = 'Start: ' . $created_at . ' UTC'; } if ( '' !== $redeemed_at ) { $dates[] = 'Redeemed: ' . $redeemed_at . ' UTC'; } $time_to_earn = ''; if ( '' !== $elapsed_label ) { $time_to_earn = $elapsed_label; } elseif ( $elapsed_days > 0 ) { $time_to_earn = $elapsed_days . 'd'; } if ( '' !== $time_to_earn ) { $suffix = ''; if ( $elapsed_days > 0 ) { $suffix = ' (' . $elapsed_days . ' days)'; } $dates[] = 'Earned in: ' . $time_to_earn . $suffix; } $dates_line = ! empty( $dates ) ? implode( ' * ', $dates ) : ''; $html .= '<div class="tct-reward-hof-item">'; if ( '' !== $thumb_url ) { $html .= '<div class="tct-reward-hof-thumb"><img src="' . esc_url( $thumb_url ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" /></div>'; } else { $html .= '<div class="tct-reward-hof-thumb tct-reward-hof-thumb-empty" aria-hidden="true"></div>'; } $html .= '<div class="tct-reward-hof-meta">'; $html .= '<div class="tct-reward-hof-title"><strong>' . esc_html( $title ) . '</strong></div>'; $html .= '<div class="tct-reward-hof-line">' . esc_html( $meta_line ) . '</div>'; if ( '' !== $dates_line ) { $html .= '<div class="tct-reward-hof-line tct-muted">' . esc_html( $dates_line ) . '</div>'; } $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_messages() { $html = ''; $reward_action_error = false; if ( isset( $_GET['tct_sync'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_sync'] ) ); if ( 'error' === $status ) { $msg = isset( $_GET['tct_sync_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_sync_msg'] ) ) : 'Sync failed.'; $html .= '<div class="tct-card tct-error">Sync error: ' . esc_html( $msg ) . '</div>'; } } if ( isset( $_GET['tct_oauth'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_oauth'] ) ); if ( 'success' !== $status ) { $html .= '<div class="tct-card tct-error">Todoist connection error: ' . esc_html( $status ) . '</div>'; } } if ( isset( $_GET['tct_goals'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_goals'] ) ); if ( 'error' === $status ) { $msg = isset( $_GET['tct_goals_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_goals_msg'] ) ) : 'Failed to save goal.'; $html .= '<div class="tct-card tct-error">Goal error: ' . esc_html( $msg ) . '</div>'; } } if ( isset( $_GET['tct_domains'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_domains'] ) ); if ( 'error' === $status ) { $msg = isset( $_GET['tct_domains_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_domains_msg'] ) ) : 'Domain error.'; $html .= '<div class="tct-card tct-error">Domain error: ' . esc_html( $msg ) . '</div>'; } } if ( isset( $_GET['tct_roles'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_roles'] ) ); if ( 'error' === $status ) { $msg = isset( $_GET['tct_roles_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_roles_msg'] ) ) : 'Role error.'; $html .= '<div class="tct-card tct-error">Role error: ' . esc_html( $msg ) . '</div>'; } } if ( isset( $_GET['tct_labels'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_labels'] ) ); if ( 'error' === $status ) { $msg = isset( $_GET['tct_labels_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_labels_msg'] ) ) : 'Failed to refresh labels.'; $html .= '<div class="tct-card tct-error">Label refresh error: ' . esc_html( $msg ) . '</div>'; } } if ( isset( $_GET['tct_reward'] ) ) { $status = sanitize_text_field( wp_unslash( $_GET['tct_reward'] ) ); if ( 'error' === $status ) { $msg = isset( $_GET['tct_reward_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['tct_reward_msg'] ) ) : 'Failed to save reward.'; $html .= '<div class="tct-card tct-error">Reward error: ' . esc_html( $msg ) . '</div>'; $reward_action_error = true; } } if ( ! $reward_action_error && class_exists( 'TCT_Reward' ) && method_exists( 'TCT_Reward', 'is_enabled' ) && TCT_Reward::is_enabled() ) { $user_id = get_current_user_id(); if ( $user_id > 0 ) { $reward = TCT_Reward::get_active_reward( $user_id ); if ( is_array( $reward ) && ! empty( $reward ) ) { $progress = TCT_Reward::compute_reward_progress( $user_id, $reward ); if ( is_array( $progress ) ) { $errors = ( isset( $progress['errors'] ) && is_array( $progress['errors'] ) ) ? $progress['errors'] : array(); $target = isset( $progress['target_points'] ) ? (int) $progress['target_points'] : 0; if ( $target <= 0 || ! empty( $errors ) ) { $msg = ! empty( $errors ) ? implode( ' | ', array_map( 'sanitize_text_field', $errors ) ) : 'Reward target points are invalid. Check that you have at least one points-enabled goal.'; $html .= '<div class="tct-card tct-error">Reward: ' . esc_html( $msg ) . '</div>'; } } } } } if ( isset( $_GET['tct_error'] ) && 'missing_client_credentials' === sanitize_text_field( wp_unslash( $_GET['tct_error'] ) ) ) { $html .= '<div class="tct-card tct-error">Admin configuration required: missing Todoist Client ID/Secret.</div>'; } return $html; } private function get_reward_stats_table_cache_key( $user_id ) { $user_id = (int) $user_id; $blog_id = function_exists( 'get_current_blog_id' ) ? (int) get_current_blog_id() : 1; $key = 'tct_reward_stats_table_' . $blog_id . '_' . $user_id; try { $key .= '_' . ( new DateTimeImmutable( 'now', TCT_Utils::wp_timezone() ) )->format( 'Ymd' ); } catch ( Exception $e ) { $key .= '_' . gmdate( 'Ymd' ); } return $key; } private function compute_reward_stats_table_html( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } $stats_html = ''; $stats_tz = TCT_Utils::wp_timezone(); $stats_utc = new DateTimeZone( 'UTC' ); try { $stats_now = new DateTimeImmutable( 'now', $stats_tz ); $stats_now_utc = $stats_now->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_today_start = $stats_now->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_sow = (int) get_option( 'start_of_week', 1 ); if ( $stats_sow < 0 || $stats_sow > 6 ) { $stats_sow = 1; } $stats_dow = (int) $stats_now->format( 'w' ); $stats_wdiff = ( $stats_dow - $stats_sow + 7 ) % 7; $stats_week_start = $stats_now->modify( '-' . $stats_wdiff . ' days' )->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_month_start = $stats_now->modify( 'first day of this month' )->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_today = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_today_start, $stats_now_utc ); $stats_week = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_week_start, $stats_now_utc ); $stats_month = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_month_start, $stats_now_utc ); $yesterday = $stats_now->modify( '-1 day' ); $stats_yest_start = $yesterday->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_yest_end = $yesterday->setTime( 23, 59, 59 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_yesterday = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_yest_start, $stats_yest_end ); $stats_last_week_start = $stats_now->modify( '-' . ( $stats_wdiff + 7 ) . ' days' )->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_week_end = $stats_now->modify( '-7 days' )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_week = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_last_week_start, $stats_last_week_end ); $stats_dom = (int) $stats_now->format( 'j' ); $last_month_first_obj = $stats_now->modify( 'first day of last month' )->setTime( 0, 0, 0 ); $last_month_days = (int) $last_month_first_obj->format( 't' ); $target_dom = min( $stats_dom, $last_month_days ); $last_month_target_obj = $last_month_first_obj->setDate( (int) $last_month_first_obj->format( 'Y' ), (int) $last_month_first_obj->format( 'm' ), $target_dom )->setTime( (int) $stats_now->format( 'H' ), (int) $stats_now->format( 'i' ), (int) $stats_now->format( 's' ) ); $stats_last_month_start = $last_month_first_obj->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_month_end = $last_month_target_obj->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_month = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_last_month_start, $stats_last_month_end ); $stats_html .= '<div class="tct-reward-widget-stats">'; $stats_html .= '<table class="tct-reward-stats-table">'; $stats_html .= '<tbody>'; $stats_pairs = array( array( 'Today', $stats_today, 'Yesterday', $stats_yesterday ), array( 'This Week', $stats_week, 'Last Week', $stats_last_week ), array( 'This Month', $stats_month, 'Last Month', $stats_last_month ), ); foreach ( $stats_pairs as $pair ) { $cur_lbl = $pair[0]; $cur_data = $pair[1]; $prev_lbl = $pair[2]; $prev_data = $pair[3]; $cur_earned = (int) $cur_data['earned']; $cur_lost = (int) $cur_data['lost']; $prev_earned = (int) $prev_data['earned']; $prev_lost = (int) $prev_data['lost']; $cur_cls = ''; $prev_cls = ''; if ( $cur_earned > $prev_earned ) { $cur_cls = ' class="tct-reward-stats-row-winning"'; } elseif ( $prev_earned > $cur_earned ) { $prev_cls = ' class="tct-reward-stats-row-losing"'; } $stats_html .= '<tr' . $cur_cls . '>'; $stats_html .= '<td class="tct-reward-stats-period">' . esc_html( $cur_lbl ) . '</td>'; $stats_html .= '<td class="tct-reward-stats-earned">+' . esc_html( number_format_i18n( $cur_earned ) ) . '</td>'; $stats_html .= '<td class="tct-reward-stats-lost">' . esc_html( number_format_i18n( $cur_lost ) ) . '</td>'; $stats_html .= '</tr>'; $stats_html .= '<tr' . $prev_cls . '>'; $stats_html .= '<td class="tct-reward-stats-period">' . esc_html( $prev_lbl ) . '</td>'; $stats_html .= '<td class="tct-reward-stats-earned">+' . esc_html( number_format_i18n( $prev_earned ) ) . '</td>'; $stats_html .= '<td class="tct-reward-stats-lost">' . esc_html( number_format_i18n( $prev_lost ) ) . '</td>'; $stats_html .= '</tr>'; } $stats_html .= '</tbody>'; $stats_html .= '</table>'; $stats_html .= '</div>'; } catch ( Exception $e ) { $stats_html = ''; } return $stats_html; } private function get_reward_stats_table_html( $user_id, $force_refresh = false ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } $cache_key = $this->get_reward_stats_table_cache_key( $user_id ); if ( ! $force_refresh ) { $cached = get_transient( $cache_key ); if ( is_string( $cached ) && '' !== trim( $cached ) ) { return $cached; } } $html = $this->compute_reward_stats_table_html( $user_id ); if ( '' !== $html ) { set_transient( $cache_key, $html, 60 ); } else { delete_transient( $cache_key ); } return $html; } private function invalidate_reward_stats_table_cache( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return; } delete_transient( $this->get_reward_stats_table_cache_key( $user_id ) ); } private function render_reward_nav_pill( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } if ( ! class_exists( 'TCT_Reward' ) ) { return ''; } if ( method_exists( 'TCT_Reward', 'is_enabled' ) && ! TCT_Reward::is_enabled() ) { return ''; } $reward = TCT_Reward::get_active_reward( $user_id ); if ( ! is_array( $reward ) || empty( $reward ) ) { return ''; } $progress = TCT_Reward::compute_reward_progress( $user_id, $reward ); if ( ! is_array( $progress ) ) { return ''; } $target = isset( $progress['target_points'] ) ? (int) $progress['target_points'] : 0; $pct_raw = isset( $progress['progress_pct'] ) ? (float) $progress['progress_pct'] : 0.0; $is_earned = ( isset( $progress['is_earned'] ) && $progress['is_earned'] ); $errors = ( isset( $progress['errors'] ) && is_array( $progress['errors'] ) ) ? $progress['errors'] : array(); $title = isset( $reward['title'] ) ? trim( (string) $reward['title'] ) : ''; if ( '' === $title ) { $title = 'Reward'; } $display_title = $title; if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) { if ( mb_strlen( $display_title ) > 32 ) { $display_title = mb_substr( $display_title, 0, 29 ) . '...'; } } else { if ( strlen( $display_title ) > 32 ) { $display_title = substr( $display_title, 0, 29 ) . '...'; } } $balance = TCT_Ledger::get_balance( $user_id ); $balance_label = number_format_i18n( (int) $balance ); $target_label = number_format_i18n( (int) $target ); $pct_raw = ( $target > 0 ) ? ( (float) $balance / (float) $target ) * 100.0 : 0.0; $pct_rounded = round( $pct_raw, 1 ); if ( abs( $pct_rounded - round( $pct_rounded ) ) < 0.05 ) { $pct_label = number_format_i18n( (int) round( $pct_rounded ), 0 ); } else { $pct_label = number_format_i18n( (float) $pct_rounded, 1 ); } $attachment_id = ( isset( $reward['attachment_id'] ) ) ? (int) $reward['attachment_id'] : 0; $image_url = ''; $image_grayscale_fallback = false; $generation_in_progress = false; if ( $attachment_id > 0 ) { $use_progress_variants = class_exists( 'TCT_Admin' ) && TCT_Admin::is_progress_images_enabled(); if ( $use_progress_variants && class_exists( 'TCT_Reward' ) && is_callable( array( 'TCT_Reward', 'get_progress_variant_url' ) ) ) { $generation_in_progress = TCT_Reward::has_active_generation_job( $user_id ); $variant_url = TCT_Reward::get_progress_variant_url( $user_id, $pct_raw, 'medium' ); if ( $variant_url ) { $image_url = $variant_url; } else { $image_url = wp_get_attachment_image_url( $attachment_id, 'medium' ); if ( ! $image_url ) { $image_url = wp_get_attachment_url( $attachment_id ); } if ( $generation_in_progress && $pct_raw < 100 ) { $image_grayscale_fallback = true; } } } else { $image_url = wp_get_attachment_image_url( $attachment_id, 'medium' ); if ( ! $image_url ) { $image_url = wp_get_attachment_url( $attachment_id ); } } } $grayscale_pct = 0; if ( $image_grayscale_fallback ) { $grayscale_pct = max( 0, min( 100, 100 - $pct_raw ) ); } $bar_width = max( 0, min( 100, $pct_raw ) ); $container_classes = 'tct-reward-widget'; if ( $is_earned ) { $container_classes .= ' tct-reward-widget-earned'; } if ( $target <= 0 || ! empty( $errors ) ) { $container_classes .= ' tct-reward-widget-error'; } if ( $generation_in_progress ) { $container_classes .= ' tct-reward-widget-generating'; } $html = '<div class="' . esc_attr( $container_classes ) . '">'; $html .= '<div class="tct-reward-widget-info">'; $html .= '<div class="tct-reward-widget-title">' . esc_html( $display_title ) . '</div>'; if ( $target > 0 && empty( $errors ) ) { $html .= '<div class="tct-reward-widget-ratio">'; $html .= '<span class="tct-reward-widget-balance">' . esc_html( $balance_label ) . '</span>'; $html .= '<span class="tct-reward-widget-sep"> / </span>'; $html .= '<span class="tct-reward-widget-target">' . esc_html( $target_label ) . ' pts</span>'; $html .= '</div>'; } else { $html .= '<div class="tct-reward-widget-ratio tct-reward-widget-ratio-error">Setup needed</div>'; } if ( $target > 0 && empty( $errors ) ) { $html .= '<div class="tct-reward-widget-progress">'; $html .= '<div class="tct-reward-widget-progress-track">'; $html .= '<div class="tct-reward-widget-progress-bar" style="width:' . esc_attr( $bar_width ) . '%;"></div>'; $html .= '</div>'; $html .= '<span class="tct-reward-widget-pct">' . esc_html( $pct_label ) . '%</span>'; $html .= '</div>'; } $html .= '</div>'; $stats_html = $this->get_reward_stats_table_html( $user_id, false ); if ( '' !== $stats_html ) { $html .= $stats_html; } if ( $image_url ) { $image_classes = 'tct-reward-widget-image'; $image_style = ''; if ( $image_grayscale_fallback && $grayscale_pct > 0 ) { $image_style = 'filter: grayscale(' . esc_attr( $grayscale_pct ) . '%);'; } $html .= '<div class="' . esc_attr( $image_classes ) . '">'; if ( $image_style ) { $html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" style="' . esc_attr( $image_style ) . '" />'; } else { $html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" />'; } if ( $generation_in_progress ) { $job_progress = TCT_Reward::get_generation_job_progress( $user_id ); $gen_message = isset( $job_progress['message'] ) ? $job_progress['message'] : 'Generating...'; $html .= '<div class="tct-reward-widget-generating-indicator" title="' . esc_attr( $gen_message ) . '">'; $html .= '<span class="tct-reward-widget-generating-spinner"></span>'; $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; return $html; } private function render_points_nav_pill( $user_id ) { $balance = TCT_Ledger::get_balance( $user_id ); $started_at = TCT_Ledger::get_points_started_at( $user_id ); $balance_label = number_format_i18n( (int) $balance ); $classes = 'tct-points-nav-pill'; if ( ! $started_at ) { $classes .= ' tct-points-nav-pill-muted'; } $title = $started_at ? 'Open Ledger' : 'Open Ledger (points tracking not enabled yet)'; $aria = 'Open Ledger. Points balance: ' . $balance_label; return '<button type="button" class="' . esc_attr( $classes ) . '" data-tct-open-tab="ledger" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $aria ) . '">Points: <strong>' . esc_html( $balance_label ) . '</strong></button>'; } private function render_points_summary_card( $user_id ) { $days = 7; $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $now_utc = $now_tz->setTimezone( new DateTimeZone( 'UTC' ) ); $range_end_utc_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc ); $range_start_utc_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc->sub( new DateInterval( 'P' . (int) $days . 'D' ) ) ); $balance = TCT_Ledger::get_balance( $user_id ); $in_range = TCT_Ledger::get_points_in_range( $user_id, $range_start_utc_mysql, $range_end_utc_mysql ); $started_at = TCT_Ledger::get_points_started_at( $user_id ); $html = ''; $html .= '<div class="tct-card tct-points-card">'; $html .= '<div class="tct-points-header">'; $html .= '<h3>Points</h3>'; $html .= '<button type="button" class="button" data-tct-open-tab="ledger">Ledger</button>'; $html .= '</div>'; $html .= '<div class="tct-points-balance">Balance: <strong>' . esc_html( number_format_i18n( $balance ) ) . '</strong></div>'; $html .= '<div class="tct-muted">In last ' . esc_html( (int) $days ) . ' days: <strong>' . esc_html( number_format_i18n( $in_range ) ) . '</strong></div>'; if ( ! $started_at ) { $html .= '<p class="tct-muted" style="margin-top:8px;">Points are currently disabled. Edit any goal and set its <strong>Importance</strong> and <strong>Effort</strong> to start tracking (not retroactive).</p>'; } $html .= '</div>'; return $html; } 
    private function tct_is_composite_ledger_event_type( $event_type ) {
        $event_type = is_string( $event_type ) ? strtolower( trim( $event_type ) ) : '';

        return in_array(
            $event_type,
            array(
                'composite_bonus',
                'composite_perfection_bonus',
                'composite_penalty',
                'composite_adjustment',
            ),
            true
        );
    }

    private function tct_decode_json_assoc( $value ) {
        if ( ! is_string( $value ) ) {
            return array();
        }

        $value = trim( $value );

        if ( '' === $value || '{' !== substr( $value, 0, 1 ) ) {
            return array();
        }

        $decoded = json_decode( $value, true );

        return is_array( $decoded ) ? $decoded : array();
    }

    private function tct_compact_points_label( $value ) {
        $number = is_numeric( $value ) ? (float) $value : 0.0;

        if ( abs( $number - round( $number ) ) < 0.05 ) {
            return number_format_i18n( (int) round( $number ) );
        }

        return number_format_i18n( $number, 1 );
    }

    private function tct_signed_points_label( $points ) {
        $points = (int) $points;

        return ( $points > 0 ? '+' : '' ) . number_format_i18n( $points );
    }

    private function tct_composite_snapshot_from_details( $details ) {
        $snapshot = $this->tct_decode_json_assoc( $details );

        if ( empty( $snapshot ) ) {
            return array();
        }

        if ( ! isset( $snapshot['parent_goal_id'] ) && ! isset( $snapshot['event_type'] ) ) {
            return array();
        }

        return $snapshot;
    }

    private function tct_composite_snapshot_complete_counts( $snapshot ) {
        $eligible = 0;
        $complete = 0;
        $epsilon  = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_epsilon' ) )
            ? (float) TCT_Utils::composite_goal_epsilon()
            : 0.0000001;

        $children = isset( $snapshot['children'] ) && is_array( $snapshot['children'] ) ? $snapshot['children'] : array();

        foreach ( $children as $child ) {
            if ( ! is_array( $child ) || empty( $child['eligible'] ) ) {
                continue;
            }

            $eligible++;

            $ratio = isset( $child['completion_ratio'] ) && is_numeric( $child['completion_ratio'] )
                ? (float) $child['completion_ratio']
                : 0.0;

            $achieved = isset( $child['achieved'] ) ? (int) $child['achieved'] : 0;
            $target   = isset( $child['target'] ) ? (int) $child['target'] : 0;

            if ( $ratio >= ( 1.0 - $epsilon ) || ( $target > 0 && $achieved >= $target ) ) {
                $complete++;
            }
        }

        return array(
            'eligible' => $eligible,
            'complete' => $complete,
        );
    }

    private function tct_composite_snapshot_window_label( $snapshot, DateTimeZone $tz, $format = 'M j, g:ia' ) {
        $window_start = isset( $snapshot['window_start'] ) ? trim( (string) $snapshot['window_start'] ) : '';
        $window_end   = isset( $snapshot['window_end'] ) ? trim( (string) $snapshot['window_end'] ) : '';

        if ( '' === $window_start || '' === $window_end || '0000-00-00 00:00:00' === $window_start || '0000-00-00 00:00:00' === $window_end ) {
            return '';
        }

        try {
            $start_dt = new DateTimeImmutable( $window_start, new DateTimeZone( 'UTC' ) );
            $end_dt   = new DateTimeImmutable( $window_end, new DateTimeZone( 'UTC' ) );

            $start_local = $start_dt->setTimezone( $tz );
            $end_local   = $end_dt->setTimezone( $tz );

            if ( $start_local->format( 'Y-m-d' ) === $end_local->format( 'Y-m-d' ) ) {
                return $start_local->format( 'M j' ) . ', ' . $start_local->format( 'g:ia' ) . ' - ' . $end_local->format( 'g:ia' );
            }

            return $start_local->format( $format ) . ' - ' . $end_local->format( $format );
        } catch ( Exception $e ) {
            return '';
        }
    }

    private function tct_composite_snapshot_summary_lines( $snapshot, $event_type = '', $points = 0, DateTimeZone $tz = null ) {
        if ( ! is_array( $snapshot ) || empty( $snapshot ) ) {
            return array();
        }

        if ( ! ( $tz instanceof DateTimeZone ) ) {
            $tz = TCT_Utils::wp_timezone();
        }

        $lines = array();

        $window_label = $this->tct_composite_snapshot_window_label( $snapshot, $tz );

        if ( '' !== $window_label ) {
            $lines[] = 'Window: ' . $window_label;
        }

        $counts = $this->tct_composite_snapshot_complete_counts( $snapshot );

        $eligible_children = isset( $snapshot['eligible_child_count'] ) ? (int) $snapshot['eligible_child_count'] : (int) $counts['eligible'];
        $complete_children = (int) $counts['complete'];
        $ratio            = isset( $snapshot['ratio'] ) && is_numeric( $snapshot['ratio'] ) ? (float) $snapshot['ratio'] : 0.0;
        $ratio_pct        = (int) round( max( 0.0, min( 1.0, $ratio ) ) * 100 );
        $bmax             = isset( $snapshot['bmax'] ) && is_numeric( $snapshot['bmax'] ) ? (float) $snapshot['bmax'] : 0.0;
        $pmax             = isset( $snapshot['pmax'] ) && is_numeric( $snapshot['pmax'] ) ? (float) $snapshot['pmax'] : 0.0;
        $child_penalties  = isset( $snapshot['child_penalties_fired'] ) && is_numeric( $snapshot['child_penalties_fired'] ) ? (float) $snapshot['child_penalties_fired'] : 0.0;

        if ( ! empty( $snapshot['has_scoring_exposure'] ) ) {
            $parts = array();

            if ( $eligible_children > 0 ) {
                $parts[] = $complete_children . '/' . $eligible_children . ' eligible children complete';
                $parts[] = $ratio_pct . '% weighted progress';
            }

            if ( $bmax > 0 || $pmax > 0 ) {
                $parts[] = 'Bmax ' . $this->tct_compact_points_label( $bmax ) . ', Pmax ' . $this->tct_compact_points_label( $pmax );
            }

            if ( $child_penalties > 0 ) {
                $parts[] = 'Child penalties already fired: ' . $this->tct_compact_points_label( $child_penalties );
            }

            if ( ! empty( $parts ) ) {
                $lines[] = implode( ' - ', $parts );
            }
        } else {
            $zero_reason = isset( $snapshot['zero_exposure_reason'] ) ? sanitize_key( (string) $snapshot['zero_exposure_reason'] ) : '';

            if ( 'no_eligible_children' === $zero_reason || $eligible_children <= 0 ) {
                $lines[] = 'No eligible children at settlement. Parent was treated complete with no bonus or penalty exposure.';
            } else {
                $lines[] = 'This window had no parent bonus or penalty exposure.';
            }
        }

        if ( 'composite_perfection_bonus' === $event_type && ! empty( $snapshot['is_perfect'] ) ) {
            $lines[] = 'All eligible children were complete for the window.';
        }

        if ( 'composite_adjustment' === $event_type ) {
            $posted_total_before = isset( $snapshot['posted_total_before'] ) ? (int) $snapshot['posted_total_before'] : null;
            $desired_total       = isset( $snapshot['desired_total_points'] ) ? (int) $snapshot['desired_total_points'] : null;

            if ( null !== $posted_total_before && null !== $desired_total ) {
                $lines[] = 'Adjustment moved the settled total from ' . $this->tct_signed_points_label( $posted_total_before ) . ' to ' . $this->tct_signed_points_label( $desired_total ) . '.';
            } elseif ( 0 !== (int) $points ) {
                $lines[] = 'Adjustment posted after a late or backdated child change.';
            }
        }

        return array_values(
            array_filter(
                array_map(
                    static function ( $line ) {
                        return is_string( $line ) ? trim( $line ) : '';
                    },
                    $lines
                )
            )
        );
    }

    private function tct_ledger_event_display_meta( $row, DateTimeZone $tz = null ) {
        if ( ! ( $tz instanceof DateTimeZone ) ) {
            $tz = TCT_Utils::wp_timezone();
        }

        $event_type = isset( $row['event_type'] ) ? strtolower( trim( (string) $row['event_type'] ) ) : '';
        $points     = isset( $row['points'] ) ? (int) $row['points'] : 0;
        $details    = isset( $row['details'] ) ? (string) $row['details'] : '';

        $meta = array(
            'typeLabel'    => 'Event',
            'typeClass'    => 'neutral',
            'windowLabel'  => '',
            'summaryLines' => array(),
        );

        if ( 'completion' === $event_type ) {
            $meta['typeLabel'] = 'Task completion';
            $meta['typeClass'] = 'completion';

            $details_trim = ltrim( $details );

            if ( '' !== $details_trim ) {
                if ( 0 === stripos( $details_trim, '[manual fail]' ) ) {
                    $meta['typeLabel'] = 'Manual fail';
                    $meta['typeClass'] = 'fail';
                } elseif ( 0 === stripos( $details_trim, '[auto miss]' ) ) {
                    $meta['typeLabel'] = 'Auto miss';
                    $meta['typeClass'] = 'fail';
                } elseif ( 0 === stripos( $details_trim, '[auto due miss]' ) ) {
                    $meta['typeLabel'] = 'Auto due miss';
                    $meta['typeClass'] = 'fail';
                }

                if ( 'fail' === $meta['typeClass'] && 0 === $points ) {
                    $meta['typeLabel'] .= ' (free)';
                }
            }

            return $meta;
        }

        if ( 'goal_bonus' === $event_type ) {
            $meta['typeLabel'] = 'Goal bonus / penalty';
            $meta['typeClass'] = $points < 0 ? 'penalty' : 'bonus';

            return $meta;
        }

        if ( $this->tct_is_composite_ledger_event_type( $event_type ) ) {
            $snapshot = $this->tct_composite_snapshot_from_details( $details );

            $meta['windowLabel']  = $this->tct_composite_snapshot_window_label( $snapshot, $tz );
            $meta['summaryLines'] = $this->tct_composite_snapshot_summary_lines( $snapshot, $event_type, $points, $tz );

            if ( 'composite_bonus' === $event_type ) {
                if ( 0 === $points && ! empty( $snapshot ) && empty( $snapshot['has_scoring_exposure'] ) ) {
                    $meta['typeLabel'] = 'Composite settlement snapshot';
                    $meta['typeClass'] = 'snapshot';
                } else {
                    $meta['typeLabel'] = 'Composite bonus';
                    $meta['typeClass'] = 'bonus';
                }
            } elseif ( 'composite_perfection_bonus' === $event_type ) {
                $meta['typeLabel'] = 'Perfection bonus';
                $meta['typeClass'] = 'perfection';
            } elseif ( 'composite_penalty' === $event_type ) {
                $meta['typeLabel'] = 'Composite penalty';
                $meta['typeClass'] = 'penalty';
            } elseif ( 'composite_adjustment' === $event_type ) {
                $meta['typeLabel'] = 'Composite adjustment';
                $meta['typeClass'] = 'adjustment';
            } else {
                $meta['typeLabel'] = 'Composite event';
                $meta['typeClass'] = 'neutral';
            }

            return $meta;
        }

        if ( '' !== $event_type ) {
            $meta['typeLabel'] = ucwords( str_replace( array( '_', '-' ), ' ', sanitize_text_field( $event_type ) ) );
            $meta['typeClass'] = $points < 0 ? 'penalty' : 'bonus';
        }

        return $meta;
    }

    private function tct_goal_history_settlement_rows( $user_id, $goal_id, DateTimeZone $tz, $limit = 100 ) {
        global $wpdb;

        $ledger_table = TCT_DB::table_ledger();
        $limit        = max( 1, min( 200, (int) $limit ) );
        $event_types  = array(
            'composite_bonus',
            'composite_perfection_bonus',
            'composite_penalty',
            'composite_adjustment',
        );

        $count_sql = "SELECT COUNT(*) FROM {$ledger_table} WHERE user_id = %d AND goal_id = %d AND event_type IN ('composite_bonus','composite_perfection_bonus','composite_penalty','composite_adjustment')";
        $total     = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $user_id, $goal_id ) );

        $rows_sql = "SELECT id, event_type, points, occurred_at, details
            FROM {$ledger_table}
            WHERE user_id = %d AND goal_id = %d
              AND event_type IN ('composite_bonus','composite_perfection_bonus','composite_penalty','composite_adjustment')
            ORDER BY occurred_at DESC, id DESC
            LIMIT %d";

        $rows = $wpdb->get_results( $wpdb->prepare( $rows_sql, $user_id, $goal_id, $limit ), ARRAY_A );

        if ( ! is_array( $rows ) ) {
            $rows = array();
        }

        $out = array();

        foreach ( $rows as $row ) {
            $meta = $this->tct_ledger_event_display_meta( $row, $tz );

            $occurred_utc   = isset( $row['occurred_at'] ) ? trim( (string) $row['occurred_at'] ) : '';
            $occurred_local = $occurred_utc;

            if ( '' !== $occurred_utc && '0000-00-00 00:00:00' !== $occurred_utc ) {
                try {
                    $occurred_local = ( new DateTimeImmutable( $occurred_utc, new DateTimeZone( 'UTC' ) ) )->setTimezone( $tz )->format( 'Y-m-d H:i' );
                } catch ( Exception $e ) {
                    $occurred_local = $occurred_utc;
                }
            }

            $out[] = array(
                'id'           => isset( $row['id'] ) ? (int) $row['id'] : 0,
                'occurredAt'   => $occurred_local,
                'type'         => isset( $row['event_type'] ) ? (string) $row['event_type'] : '',
                'typeLabel'    => isset( $meta['typeLabel'] ) ? (string) $meta['typeLabel'] : 'Settlement',
                'typeClass'    => isset( $meta['typeClass'] ) ? (string) $meta['typeClass'] : 'neutral',
                'points'       => isset( $row['points'] ) ? (int) $row['points'] : 0,
                'windowLabel'  => isset( $meta['windowLabel'] ) ? (string) $meta['windowLabel'] : '',
                'summaryLines' => isset( $meta['summaryLines'] ) && is_array( $meta['summaryLines'] ) ? $meta['summaryLines'] : array(),
            );
        }

        return array(
            'rows'  => $out,
            'total' => $total,
        );
    }

    private function render_ledger_panel( $user_id, $redirect_here ) {
        $days = 7;
        $tz = TCT_Utils::wp_timezone();
        $now_tz = new DateTimeImmutable( 'now', $tz );
        $now_utc = $now_tz->setTimezone( new DateTimeZone( 'UTC' ) );
        $range_end_utc_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc );
        $range_start_utc_mysql = TCT_Utils::dt_to_mysql_utc( $now_utc->sub( new DateInterval( 'P' . (int) $days . 'D' ) ) );
        $balance = TCT_Ledger::get_balance( $user_id );
        $in_range = TCT_Ledger::get_points_in_range( $user_id, $range_start_utc_mysql, $range_end_utc_mysql );
        $started_at = TCT_Ledger::get_points_started_at( $user_id );
        $transactions = TCT_Ledger::get_transactions( $user_id, 200 );

        $html = '';
        $html .= '<div class="tct-card">';
        $html .= '<h3>Ledger</h3>';
        $html .= '<div class="tct-ledger-summary">';
        $html .= '<div class="tct-ledger-balance">Balance: <strong>' . esc_html( number_format_i18n( $balance ) ) . '</strong></div>';
        $html .= '<div class="tct-muted">In last ' . esc_html( (int) $days ) . ' days: <strong>' . esc_html( number_format_i18n( $in_range ) ) . '</strong></div>';

        if ( $started_at ) {
            $html .= '<div class="tct-muted">Started: ' . esc_html( $started_at ) . ' UTC</div>';
        } else {
            $html .= '<div class="tct-muted">Points not enabled yet.</div>';
        }

        $html .= '</div>';

        if ( empty( $transactions ) ) {
            $html .= '<p class="tct-muted">No transactions yet. Once you set points on a goal and complete tasks, entries will appear here.</p>';
            $html .= '</div>';

            return $html;
        }

        $html .= '<div class="tct-ledger-table-wrap">';
        $html .= '<table class="widefat striped tct-ledger-table">';
        $html .= '<thead><tr>';
        $html .= '<th class="tct-ledger-date-col">Date</th><th class="tct-ledger-points-col">Points</th><th class="tct-ledger-goal-col" style="text-align:center;">Goal</th><th class="tct-ledger-role-col">Role</th><th class="tct-ledger-domain-col">Domain</th><th class="tct-ledger-type-col">Type</th><th class="tct-ledger-undo-col">Undo</th>';
        $html .= '</tr></thead><tbody>';

        foreach ( $transactions as $row ) {
            $occurred_utc = isset( $row['occurred_at'] ) ? (string) $row['occurred_at'] : '';
            $display_date = $occurred_utc;

            try {
                $dt = new DateTimeImmutable( $occurred_utc, new DateTimeZone( 'UTC' ) );
                $dt = $dt->setTimezone( $tz );
                $display_date = $dt->format( 'Y-m-d H:i' );
            } catch ( Exception $e ) {
            }

            $goal_name = isset( $row['goal_name'] ) && '' !== $row['goal_name'] ? (string) $row['goal_name'] : ( isset( $row['label_name'] ) ? (string) $row['label_name'] : '' );
            $goal_id = isset( $row['goal_id'] ) ? (int) $row['goal_id'] : 0;
            $completion_id = isset( $row['completion_id'] ) ? (int) $row['completion_id'] : 0;
            $points = isset( $row['points'] ) ? (int) $row['points'] : 0;
            $points_class = $points >= 0 ? 'tct-points-positive' : 'tct-points-negative';
            $points_str = ( $points >= 0 ? '+' : '' ) . (string) $points;

            $meta = $this->tct_ledger_event_display_meta( $row, $tz );
            $type_label = isset( $meta['typeLabel'] ) ? (string) $meta['typeLabel'] : 'Event';
            $type_class = isset( $meta['typeClass'] ) ? sanitize_html_class( (string) $meta['typeClass'] ) : 'neutral';
            $summary_lines = isset( $meta['summaryLines'] ) && is_array( $meta['summaryLines'] ) ? array_values( array_filter( $meta['summaryLines'] ) ) : array();

            $type_html = '<span class="tct-history-event-pill tct-history-event-pill-' . esc_attr( $type_class ) . '">' . esc_html( $type_label ) . '</span>';

            if ( ! empty( $summary_lines ) ) {
                $type_html .= '<div class="tct-ledger-type-note">' . esc_html( implode( ' ', $summary_lines ) ) . '</div>';
            }

            $undo_html = '<span class="tct-muted">--</span>';

            if ( isset( $row['event_type'] ) && 'completion' === (string) $row['event_type'] && $completion_id > 0 && $goal_id > 0 ) {
                $undo_html = '<button type="button" class="tct-history-undo-btn" data-tct-ledger-undo="1"';
                $undo_html .= ' data-completion-id="' . esc_attr( $completion_id ) . '" data-goal-id="' . esc_attr( $goal_id ) . '"';
                $undo_html .= ' title="Undo completion">';
                $undo_html .= '<span class="dashicons dashicons-undo" aria-hidden="true"></span>';
                $undo_html .= '<span class="screen-reader-text">Undo</span>';
                $undo_html .= '</button>';
            }

            $domain_name = isset( $row['domain_name'] ) ? (string) $row['domain_name'] : '';
            $role_name = isset( $row['role_name'] ) ? (string) $row['role_name'] : '';
            $domain_disp = '' !== $domain_name ? $domain_name : '--';
            $role_disp = '' !== $role_name ? $role_name : '--';
            $goal_cell_html = esc_html( $goal_name );
            $tr_attrs = '';

            if ( $goal_id > 0 ) {
                $tr_attrs = ' data-tct-open-goal-history="1" data-goal-id="' . esc_attr( $goal_id ) . '"';
            }

            $html .= '<tr' . $tr_attrs . '>';
            $html .= '<td class="tct-ledger-date-col">' . esc_html( $display_date ) . '</td>';
            $html .= '<td class="tct-ledger-points-col"><span class="' . esc_attr( $points_class ) . '">' . esc_html( $points_str ) . '</span></td>';
            $html .= '<td class="tct-ledger-goal-col" style="text-align:center;">' . $goal_cell_html . '</td>';
            $html .= '<td class="tct-ledger-role-col">' . esc_html( $role_disp ) . '</td>';
            $html .= '<td class="tct-ledger-domain-col">' . esc_html( $domain_disp ) . '</td>';
            $html .= '<td class="tct-ledger-type-col">' . $type_html . '</td>';
            $html .= '<td class="tct-ledger-undo-col tct-history-undo-col">' . $undo_html . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
 private function human_interval_label( $target, $unit, $span ) { $target = (int) $target; if ( $target < 0 ) { $target = 0; } $unit = is_string( $unit ) ? strtolower( trim( $unit ) ) : ''; $unit = sanitize_text_field( $unit ); $span = (int) $span; if ( $span < 1 ) { $span = 1; } $labels = array( 'hour' => array( 'singular' => 'hour', 'plural' => 'hours' ), 'day' => array( 'singular' => 'day', 'plural' => 'days' ), 'week' => array( 'singular' => 'week', 'plural' => 'weeks' ), 'month' => array( 'singular' => 'month', 'plural' => 'months' ), 'quarter' => array( 'singular' => 'quarter', 'plural' => 'quarters' ), 'semiannual' => array( 'singular' => 'semiannual', 'plural' => 'semiannual' ), 'year' => array( 'singular' => 'year', 'plural' => 'years' ), ); $unit_s = isset( $labels[ $unit ] ) ? $labels[ $unit ]['singular'] : ( '' !== $unit ? $unit : 'period' ); $unit_p = isset( $labels[ $unit ] ) ? $labels[ $unit ]['plural'] : ( '' !== $unit ? $unit . 's' : 'periods' ); if ( 1 === $span ) { return (string) $target . ' every ' . $unit_s; } return (string) $target . ' every ' . (int) $span . ' ' . $unit_p; } private function format_period_range_label( DateTimeImmutable $start, DateTimeImmutable $end, $unit ) { $unit = is_string( $unit ) ? strtolower( trim( $unit ) ) : ''; $unit = sanitize_text_field( $unit ); if ( 'hour' === $unit ) { return $start->format( 'Y-m-d H:i' ) . ' - ' . $end->format( 'Y-m-d H:i' ); } $end_inclusive = $end->modify( '-1 second' ); return $start->format( 'Y-m-d' ) . ' - ' . $end_inclusive->format( 'Y-m-d' ); } private function current_loop_bounds_for_goal( array $goal_row, DateTimeImmutable $now_tz, $unit, $span = 1 ) { if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'goal_interval_current_loop_bounds' ) ) ) { $bounds = TCT_Interval::goal_interval_current_loop_bounds( $goal_row, $now_tz ); if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) && ( $bounds['start'] instanceof DateTimeImmutable ) && ( $bounds['end'] instanceof DateTimeImmutable ) ) { return $bounds; } } return $this->current_loop_bounds( $now_tz, $unit, $span ); } 
private function current_loop_bounds( DateTimeImmutable $now_tz, $unit, $span = 1 ) { $unit = is_string( $unit ) ? $unit : ''; $unit = sanitize_text_field( $unit ); $span = is_numeric( $span ) ? (int) $span : 1; if ( $span < 1 ) { $span = 1; } $allowed = array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ); if ( ! in_array( $unit, $allowed, true ) ) { $unit = 'week'; } if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) { $bounds = TCT_Interval::current_loop_bounds( $now_tz, $unit, $span ); if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) && ( $bounds['start'] instanceof DateTimeImmutable ) && ( $bounds['end'] instanceof DateTimeImmutable ) ) { return $bounds; } } if ( 'hour' === $unit ) { $midnight = $now_tz->setTime( 0, 0, 0 ); $hour = (int) $now_tz->format( 'G' ); $span_i = max( 1, (int) $span ); $block = (int) floor( $hour / $span_i ); $start_h = $block * $span_i; $start = $midnight->modify( '+' . (int) $start_h . ' hours' ); $end_candidate = $start->modify( '+' . (int) $span_i . ' hours' ); $next_midnight = $midnight->add( new DateInterval( 'P1D' ) ); $end = ( $end_candidate > $next_midnight ) ? $next_midnight : $end_candidate; return array( 'start' => $start, 'end' => $end ); } if ( 'day' === $unit ) { $year = (int) $now_tz->format( 'Y' ); $month = (int) $now_tz->format( 'n' ); $day = (int) $now_tz->format( 'j' ); $span_i = max( 1, (int) $span ); $block_start_day = 1 + (int) floor( ( $day - 1 ) / $span_i ) * $span_i; $start = $now_tz->setDate( $year, $month, $block_start_day )->setTime( 0, 0, 0 ); $end_candidate = $start->add( new DateInterval( 'P' . (int) $span_i . 'D' ) ); $month_end = $now_tz->setDate( $year, $month, 1 )->setTime( 0, 0, 0 )->add( new DateInterval( 'P1M' ) ); $end = ( $end_candidate > $month_end ) ? $month_end : $end_candidate; return array( 'start' => $start, 'end' => $end ); } if ( 'week' === $unit ) { $start_of_week = (int) get_option( 'start_of_week', 1 ); if ( $start_of_week < 0 || $start_of_week > 6 ) { $start_of_week = 1; } $span_i = max( 1, (int) $span ); $year = (int) $now_tz->format( 'Y' ); $jan1 = $now_tz->setDate( $year, 1, 1 )->setTime( 0, 0, 0 ); $jan1_dow = (int) $jan1->format( 'w' ); $jan1_diff = ( $jan1_dow - $start_of_week + 7 ) % 7; $week1_start = $jan1->sub( new DateInterval( 'P' . (int) $jan1_diff . 'D' ) ); $dow = (int) $now_tz->format( 'w' ); $diff2 = ( $dow - $start_of_week + 7 ) % 7; $this_week_start = $now_tz->setTime( 0, 0, 0 )->sub( new DateInterval( 'P' . (int) $diff2 . 'D' ) ); $seconds_since = (int) ( $this_week_start->getTimestamp() - $week1_start->getTimestamp() ); if ( $seconds_since < 0 ) { $seconds_since = 0; } $weeks_since = (int) floor( (float) $seconds_since / (float) ( 7 * 86400 ) ); $block_index = (int) floor( (float) $weeks_since / (float) $span_i ); $start = $week1_start->add( new DateInterval( 'P' . (int) ( $block_index * $span_i ) . 'W' ) ); $end_candidate = $start->add( new DateInterval( 'P' . (int) $span_i . 'W' ) ); $jan1_next = $now_tz->setDate( $year + 1, 1, 1 )->setTime( 0, 0, 0 ); $jan1_next_dow = (int) $jan1_next->format( 'w' ); $jan1_next_diff = ( $jan1_next_dow - $start_of_week + 7 ) % 7; $week1_next = $jan1_next->sub( new DateInterval( 'P' . (int) $jan1_next_diff . 'D' ) ); $end = ( $end_candidate > $week1_next ) ? $week1_next : $end_candidate; return array( 'start' => $start, 'end' => $end ); } if ( 'month' === $unit ) { $year = (int) $now_tz->format( 'Y' ); $month = (int) $now_tz->format( 'n' ); $span_i = max( 1, (int) $span ); $block_start_month = 1 + (int) floor( ( $month - 1 ) / $span_i ) * $span_i; $start = $now_tz->setDate( $year, $block_start_month, 1 )->setTime( 0, 0, 0 ); $end_candidate = $start->add( new DateInterval( 'P' . (int) $span_i . 'M' ) ); $year_end = $now_tz->setDate( $year + 1, 1, 1 )->setTime( 0, 0, 0 ); $end = ( $end_candidate > $year_end ) ? $year_end : $end_candidate; return array( 'start' => $start, 'end' => $end ); } $month = (int) $now_tz->format( 'n' ); $year = (int) $now_tz->format( 'Y' ); if ( 'quarter' === $unit ) { $q_start_month = ( (int) floor( ( $month - 1 ) / 3 ) * 3 ) + 1; $start = $now_tz->setDate( $year, $q_start_month, 1 )->setTime( 0, 0, 0 ); $end = $start->add( new DateInterval( 'P3M' ) ); return array( 'start' => $start, 'end' => $end ); } if ( 'semiannual' === $unit ) { $sa_start_month = ( $month >= 7 ) ? 7 : 1; $start = $now_tz->setDate( $year, $sa_start_month, 1 )->setTime( 0, 0, 0 ); $end = $start->add( new DateInterval( 'P6M' ) ); return array( 'start' => $start, 'end' => $end ); } $start = $now_tz->setDate( $year, 1, 1 )->setTime( 0, 0, 0 ); $end = $start->add( new DateInterval( 'P1Y' ) ); return array( 'start' => $start, 'end' => $end ); } 

    /**
     * Build the "Allowed fails" line shown on tiles (via paceLine2) for eligible goals.
     *
     * @param int               $user_id
     * @param array             $goal_row
     * @param DateTimeImmutable $now_tz
     * @return string
     */
    private function get_allowed_fails_pace_line( $user_id, $goal_row, DateTimeImmutable $now_tz ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 || ! is_array( $goal_row ) ) {
            return '';
        }

        $goal_id = 0;
        if ( isset( $goal_row['goal_id'] ) ) {
            $goal_id = (int) $goal_row['goal_id'];
        } elseif ( isset( $goal_row['id'] ) ) {
            $goal_id = (int) $goal_row['id'];
        }

        if ( $goal_id <= 0 ) {
            return '';
        }

        // Ensure required columns are present; some AJAX handlers may pass a partial row.
        $needs_fetch = false;
        foreach ( array( 'goal_type', 'target', 'period_unit', 'period_span', 'allowed_fails_target', 'allowed_fails_unit', 'allowed_fails_span' ) as $k ) {
            if ( ! array_key_exists( $k, $goal_row ) ) {
                $needs_fetch = true;
                break;
            }
        }

        if ( $needs_fetch && class_exists( 'TCT_DB' ) ) {
            global $wpdb;
            $goals_table = TCT_DB::table_goals();
            $fetched = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT id, user_id, goal_type, target, period_unit, period_span, allowed_fails_target, allowed_fails_unit, allowed_fails_span
                     FROM {$goals_table}
                     WHERE id = %d AND user_id = %d
                     LIMIT 1",
                    $goal_id,
                    $user_id
                ),
                ARRAY_A
            );
            if ( is_array( $fetched ) ) {
                $goal_row = array_merge( $goal_row, $fetched );
            }
        }

        if (
            ! class_exists( 'TCT_Interval' )
            || ! method_exists( 'TCT_Interval', 'is_goal_eligible_for_allowed_fails' )
            || ! method_exists( 'TCT_Interval', 'normalize_allowed_fails_config_from_row' )
            || ! method_exists( 'TCT_Interval', 'current_allowed_fails_bounds' )
        ) {
            return '';
        }

        if ( ! TCT_Interval::is_goal_eligible_for_allowed_fails( $goal_row ) ) {
            return '';
        }

        $cfg = TCT_Interval::normalize_allowed_fails_config_from_row( $goal_row );
        if ( ! is_array( $cfg ) ) {
            return '';
        }

        $cfg_target = isset( $cfg['target'] ) ? (int) $cfg['target'] : 0;
        if ( $cfg_target <= 0 ) {
            return '';
        }

        $cfg_unit = isset( $cfg['unit'] ) ? (string) $cfg['unit'] : 'week';
        $cfg_span = isset( $cfg['span'] ) ? (int) $cfg['span'] : 1;
        if ( $cfg_span < 1 ) {
            $cfg_span = 1;
        }

        $bounds = TCT_Interval::current_allowed_fails_bounds( $now_tz, $cfg_unit, $cfg_span );
        if ( ! is_array( $bounds ) || ! isset( $bounds['start'] ) || ! ( $bounds['start'] instanceof DateTimeImmutable ) ) {
            return '';
        }

        if ( ! class_exists( 'TCT_Utils' ) || ! method_exists( 'TCT_Utils', 'dt_to_mysql_utc' ) ) {
            return '';
        }

        $start_utc = TCT_Utils::dt_to_mysql_utc( $bounds['start'] );
        $end_utc   = TCT_Utils::dt_to_mysql_utc( $now_tz );

        static $cache = array();
        $cache_key = $user_id . '|' . $goal_id . '|' . $start_utc . '|' . $end_utc;
        if ( isset( $cache[ $cache_key ] ) ) {
            $used = (int) $cache[ $cache_key ];
        } else {
            if ( ! class_exists( 'TCT_DB' ) ) {
                return '';
            }
            global $wpdb;
            $ledger_table = TCT_DB::table_ledger();
            $like_manual = $wpdb->esc_like( '[manual fail]' ) . '%';
            $like_auto   = $wpdb->esc_like( '[auto miss]' ) . '%';

            $used = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$ledger_table}
                     WHERE user_id = %d
                       AND goal_id = %d
                       AND occurred_at >= %s
                       AND occurred_at < %s
                       AND (details LIKE %s OR details LIKE %s)",
                    $user_id,
                    $goal_id,
                    $start_utc,
                    $end_utc,
                    $like_manual,
                    $like_auto
                )
            );
            $cache[ $cache_key ] = $used;
        }

        if ( $used < 0 ) {
            $used = 0;
        }

        return 'Allowed fails: ' . (int) $used . '/' . (int) $cfg_target . ' used';
    }


private function get_goal_availability_tile_context( array $goal_row, DateTimeImmutable $now_tz = null ) {
    $out = array(
        'enabled' => false,
        'phase' => '',
        'is_active' => false,
        'is_paused' => false,
        'state_label' => '',
        'state_meta' => '',
        'phase_day' => 0,
        'phase_length' => 0,
        'next_active_start_local' => '',
        'current_phase_end_local_exclusive' => '',
    );

    if ( ! class_exists( 'TCT_Interval' ) || ! is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) ) || ! is_callable( array( 'TCT_Interval', 'availability_cycle_state_at_datetime' ) ) || ! is_callable( array( 'TCT_Interval', 'availability_cycle_state_label_context' ) ) ) {
        return $out;
    }

    if ( ! ( $now_tz instanceof DateTimeImmutable ) ) {
        $tz_local = ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
        $now_tz = new DateTimeImmutable( 'now', $tz_local );
    }

    if ( is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) ) && ! TCT_Interval::is_goal_eligible_for_availability_cycle( $goal_row ) ) {
        return $out;
    }

    $cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal_row );
    if ( ! is_array( $cfg ) || empty( $cfg['enabled'] ) ) {
        return $out;
    }

    $tz = $now_tz->getTimezone();
    $state = TCT_Interval::availability_cycle_state_at_datetime( $cfg, $now_tz, $tz );
    if ( ! is_array( $state ) || empty( $state['enabled'] ) ) {
        return $out;
    }

    $labels = TCT_Interval::availability_cycle_state_label_context( $cfg, $now_tz, $tz );
    $out['enabled'] = true;
    $out['phase'] = isset( $state['phase'] ) ? (string) $state['phase'] : '';
    $out['is_active'] = ! empty( $state['is_active'] );
    $out['is_paused'] = ! empty( $state['is_paused'] );
    $out['state_label'] = is_array( $labels ) && isset( $labels['default_label'] ) ? (string) $labels['default_label'] : '';
    $out['state_meta'] = is_array( $labels ) && isset( $labels['default_meta_label'] ) ? (string) $labels['default_meta_label'] : '';
    $out['phase_day'] = isset( $state['phase_day'] ) ? (int) $state['phase_day'] : 0;
    $out['phase_length'] = isset( $state['phase_length'] ) ? (int) $state['phase_length'] : 0;
    $out['next_active_start_local'] = isset( $state['next_active_start_local'] ) ? (string) $state['next_active_start_local'] : '';
    $out['current_phase_end_local_exclusive'] = isset( $state['current_phase_end_local_exclusive'] ) ? (string) $state['current_phase_end_local_exclusive'] : '';

    return $out;
}

private function compute_goal_tile_state( array $goal_row, $goal_count, DateTimeImmutable $now_tz, DateTimeImmutable $today_tz ) { $goal_count = (int) $goal_count; $target = isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0; if ( $target < 0 ) { $target = 0; } $unit = isset( $goal_row['period_unit'] ) ? sanitize_text_field( (string) $goal_row['period_unit'] ) : 'week'; if ( ! in_array( $unit, array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ), true ) ) { $unit = 'week'; } $span = isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1; if ( $span < 1 ) { $span = 1; } $goal_type = isset( $goal_row['goal_type'] ) && is_string( $goal_row['goal_type'] ) ? (string) $goal_row['goal_type'] : 'positive'; $is_negative = TCT_Utils::is_negative_goal_type( $goal_type ); if ( $is_negative ) { return array( 'unit' => $unit, 'target' => $target, 'statusKey' => 'on-track', 'statusLabel' => 'On track', 'paceLine1' => 'On track', 'paceLine2' => '', 'availabilityEnabled' => false, 'availabilityPhase' => '', 'availabilityLabel' => '', 'availabilityMeta' => '', 'availabilityPaused' => false, ); } $availability_ctx = $this->get_goal_availability_tile_context( $goal_row, $now_tz ); if ( ! empty( $availability_ctx['enabled'] ) && ! empty( $availability_ctx['is_paused'] ) ) { return array( 'unit' => $unit, 'target' => $target, 'statusKey' => 'on-track', 'statusLabel' => 'Paused', 'paceLine1' => isset( $availability_ctx['state_label'] ) ? (string) $availability_ctx['state_label'] : 'Paused', 'paceLine2' => isset( $availability_ctx['state_meta'] ) ? (string) $availability_ctx['state_meta'] : '', 'availabilityEnabled' => true, 'availabilityPhase' => isset( $availability_ctx['phase'] ) ? (string) $availability_ctx['phase'] : 'pause', 'availabilityLabel' => isset( $availability_ctx['state_label'] ) ? (string) $availability_ctx['state_label'] : 'Paused', 'availabilityMeta' => isset( $availability_ctx['state_meta'] ) ? (string) $availability_ctx['state_meta'] : '', 'availabilityPaused' => true, ); } $bounds = $this->current_loop_bounds_for_goal( $goal_row, $now_tz, $unit, $span ); $start = isset( $bounds['start'] ) ? $bounds['start'] : $today_tz; $end = isset( $bounds['end'] ) ? $bounds['end'] : $today_tz->add( new DateInterval( 'P7D' ) ); $start_day_ts = (int) $start->setTime( 0, 0, 0 )->getTimestamp(); $end_day_ts = (int) $end->setTime( 0, 0, 0 )->getTimestamp(); $today_ts = (int) $today_tz->getTimestamp(); $span_s = $end_day_ts - $start_day_ts; $total_days = (int) floor( (float) $span_s / ( defined( 'DAY_IN_SECONDS' ) ? (int) DAY_IN_SECONDS : 86400 ) ); if ( $total_days < 1 ) { $total_days = 1; } $day_index = (int) floor( (float) ( $today_ts - $start_day_ts ) / ( defined( 'DAY_IN_SECONDS' ) ? (int) DAY_IN_SECONDS : 86400 ) ) + 1; if ( $day_index < 1 ) { $day_index = 1; } if ( $day_index > $total_days ) { $day_index = $total_days; } $days_left = $total_days - $day_index + 1; if ( $days_left < 1 ) { $days_left = 1; } $start_ts = (int) $start->getTimestamp(); $end_ts = (int) $end->getTimestamp(); $now_ts = (int) $now_tz->getTimestamp(); $total_seconds = $end_ts - $start_ts; if ( $total_seconds < 1 ) { $total_seconds = 1; } $seconds_left = $end_ts - $now_ts; if ( $seconds_left < 0 ) { $seconds_left = 0; } $effective_target = $target; $created_at_raw = isset( $goal_row['created_at'] ) ? trim( (string) $goal_row['created_at'] ) : ''; $updated_at_raw = isset( $goal_row['updated_at'] ) ? trim( (string) $goal_row['updated_at'] ) : ''; $created_at_ts_pr = 0; $updated_at_ts_pr = 0; if ( '' !== $created_at_raw && '0000-00-00 00:00:00' !== $created_at_raw ) { $tmp = strtotime( $created_at_raw . ' UTC' ); if ( false !== $tmp ) { $created_at_ts_pr = (int) $tmp; } } if ( '' !== $updated_at_raw && '0000-00-00 00:00:00' !== $updated_at_raw ) { $tmp = strtotime( $updated_at_raw . ' UTC' ); if ( false !== $tmp ) { $updated_at_ts_pr = (int) $tmp; } } $prorate_anchor = TCT_Utils::compute_prorate_anchor_ts( $created_at_ts_pr, $updated_at_ts_pr, $start_ts ); if ( $prorate_anchor > 0 ) { $effective_target = TCT_Utils::compute_prorated_target( $target, $prorate_anchor, $start_ts, $end_ts ); } $status = $this->compute_goal_status_meta( $goal_count, $effective_target, $day_index, $total_days, $days_left, $seconds_left, $total_seconds, $unit, $span ); $status_key = isset( $status['key'] ) ? (string) $status['key'] : 'on-track'; $status_label = isset( $status['label'] ) ? (string) $status['label'] : 'On track'; if ( $effective_target <= 0 && $target > 0 && $goal_count >= $target ) { $status_key = 'completed'; $status_label = 'Completed'; $effective_target = $target; } $expected = isset( $status['expected'] ) ? (int) $status['expected'] : 0; $need = isset( $status['need'] ) ? (int) $status['need'] : 0; $behind = $expected - $goal_count; if ( $behind < 0 ) { $behind = 0; } $pace_line1 = ''; $pace_line2 = ''; if ( $effective_target > 0 ) { if ( $goal_count >= $effective_target ) { if ( $span > 1 ) { $pace_line1 = 'Done for this period'; } else { $pace_line1 = 'Done for this ' . (string) $unit; } } else { if ( $days_left <= 1 ) { $pace_line1 = 'Need ' . (int) $need . ' today'; } else { if ( 'critical' === $status_key ) { $req = (int) ceil( (float) $need / (float) max( 1, $days_left ) ); if ( $req > 1 ) { $pace_line1 = 'Need ' . (int) $req . '/day'; } } elseif ( $behind > 0 ) { $pace_line1 = 'Behind by ' . (int) $behind; } if ( '' === $pace_line1 ) { $pace_line1 = 'On track'; } } } } elseif ( $target > 0 && 0 === $effective_target ) { $pace_line1 = 'Starts next ' . (string) $unit; } if ( '' === $pace_line2 ) { $user_id_af = isset( $goal_row['user_id'] ) ? (int) $goal_row['user_id'] : ( function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0 ); if ( $user_id_af > 0 ) { $af_line = $this->get_allowed_fails_pace_line( $user_id_af, $goal_row, $now_tz ); if ( is_string( $af_line ) && '' !== $af_line ) { $pace_line2 = $af_line; } } } return array( 'unit' => $unit, 'target' => $target, 'statusKey' => $status_key, 'statusLabel' => $status_label, 'paceLine1' => $pace_line1, 'paceLine2' => $pace_line2, 'availabilityEnabled' => ! empty( $availability_ctx['enabled'] ), 'availabilityPhase' => isset( $availability_ctx['phase'] ) ? (string) $availability_ctx['phase'] : '', 'availabilityLabel' => isset( $availability_ctx['state_label'] ) ? (string) $availability_ctx['state_label'] : '', 'availabilityMeta' => isset( $availability_ctx['state_meta'] ) ? (string) $availability_ctx['state_meta'] : '', 'availabilityPaused' => ! empty( $availability_ctx['is_paused'] ), ); } private function compute_goal_status_meta( $count, $target, $day_index, $total_days, $days_left, $seconds_left, $total_seconds, $unit = 'week', $span = 1 ) { $count = (int) $count; $target = (int) $target; $day_index = (int) $day_index; $total_days = (int) $total_days; $days_left = (int) $days_left; $seconds_left = (int) $seconds_left; $total_seconds = (int) $total_seconds; $unit = is_string( $unit ) ? (string) $unit : 'week'; $unit = trim( $unit ); if ( function_exists( 'sanitize_text_field' ) ) { $unit = sanitize_text_field( $unit ); } $unit = strtolower( $unit ); if ( ! in_array( $unit, array( 'hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ), true ) ) { $unit = 'week'; } $span = (int) $span; if ( $span < 1 ) { $span = 1; } if ( $total_days < 1 ) { $total_days = 1; } if ( $days_left < 1 ) { $days_left = 1; } if ( $seconds_left < 0 ) { $seconds_left = 0; } if ( $total_seconds < 1 ) { $total_seconds = 1; } if ( $target <= 0 ) { return array( 'key' => 'on-track', 'label' => 'On track', 'rank' => 1, 'expected' => 0, 'need' => 0, ); } if ( $count >= $target ) { return array( 'key' => 'completed', 'label' => 'Completed', 'rank' => 0, 'expected' => $target, 'need' => 0, ); } $expected = 0; if ( $total_days > 0 ) { $expected = (int) floor( ( (float) $day_index * (float) $target ) / (float) $total_days ); } if ( $expected < 0 ) { $expected = 0; } if ( $expected > $target ) { $expected = $target; } $need = $target - $count; if ( $need < 0 ) { $need = 0; } $is_critical = false; $is_risk = false; if ( $need > 0 ) { $hour_s = defined( 'HOUR_IN_SECONDS' ) ? (int) HOUR_IN_SECONDS : 3600; $is_day_span_1 = ( 'day' === $unit && 1 === $span ); $is_day_span_2_target_1 = ( 'day' === $unit && 2 === $span && 1 === $target ); if ( $is_day_span_1 ) { $critical_cutoff_s = 6 * $hour_s; if ( $critical_cutoff_s < 0 ) { $critical_cutoff_s = 0; } if ( $seconds_left <= $critical_cutoff_s ) { $is_critical = true; } } elseif ( $is_day_span_2_target_1 ) { $critical_cutoff_s = 24 * $hour_s; if ( $critical_cutoff_s < 0 ) { $critical_cutoff_s = 0; } if ( $seconds_left <= $critical_cutoff_s ) { $is_critical = true; } } else { $pace_at_risk = ( $need === $days_left ); $pace_critical = ( $need > $days_left ); $critical_window_s = 24 * $hour_s; $risk_window_s = 48 * $hour_s; if ( $seconds_left <= $critical_window_s || $pace_critical ) { $is_critical = true; } elseif ( $seconds_left <= $risk_window_s || $pace_at_risk ) { $is_risk = true; } } } if ( $is_critical ) { return array( 'key' => 'critical', 'label' => 'Critical', 'rank' => 3, 'expected' => $expected, 'need' => $need, ); } if ( $is_risk ) { return array( 'key' => 'risk', 'label' => 'At risk', 'rank' => 2, 'expected' => $expected, 'need' => $need, ); } return array( 'key' => 'on-track', 'label' => 'On track', 'rank' => 1, 'expected' => $expected, 'need' => $need, ); } private function render_cockpit_goal_tile( $g, $is_connected ) { if ( ! is_array( $g ) ) { return ''; } $composite_tile_html = $this->tct_render_composite_goal_tile_scaffold( $g, $is_connected ); if ( '' !== $composite_tile_html ) { return $composite_tile_html; } $html = ''; $context = isset( $g['__tct_context'] ) && is_string( $g['__tct_context'] ) ? trim( (string) $g['__tct_context'] ) : ''; $surface = isset( $g['__tct_surface'] ) && is_string( $g['__tct_surface'] ) ? trim( (string) $g['__tct_surface'] ) : ''; $is_mobile_surface = ( '' !== $surface && 0 === strpos( $surface, 'mobile' ) ); $is_complete_context = ( 'complete' === $context ); $is_paused_context = ( 'paused' === $context ); $availability_enabled = ! empty( $g['availability_enabled'] ); $availability_phase = isset( $g['availability_phase'] ) && is_string( $g['availability_phase'] ) ? (string) $g['availability_phase'] : ''; $availability_is_paused = ! empty( $g['availability_is_paused'] ) || ( 'pause' === $availability_phase ); $availability_is_active = ! empty( $availability_enabled ) && ! $availability_is_paused; $availability_state_label = isset( $g['availability_state_label'] ) && is_string( $g['availability_state_label'] ) ? (string) $g['availability_state_label'] : ''; $availability_state_meta = isset( $g['availability_state_meta'] ) && is_string( $g['availability_state_meta'] ) ? (string) $g['availability_state_meta'] : ''; if ( '' === $availability_phase && $availability_enabled ) { $availability_phase = $availability_is_paused ? 'pause' : 'active'; } $availability_badge_text = $availability_is_paused ? 'Paused' : ( $availability_enabled ? 'Active' : '' ); $goal_type = isset( $g['goal_type'] ) && is_string( $g['goal_type'] ) ? (string) $g['goal_type'] : 'positive'; $threshold = isset( $g['threshold'] ) && is_numeric( $g['threshold'] ) ? (int) $g['threshold'] : null; $is_negative = TCT_Utils::is_negative_goal_type( $goal_type ); $is_no_interval_positive = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) : ( 'positive_no_int' === $goal_type ); $status_key = isset( $g['status_key'] ) ? (string) $g['status_key'] : 'on-track'; $status_label = isset( $g['status_label'] ) ? (string) $g['status_label'] : ''; $goal_count = isset( $g['goal_count'] ) ? (int) $g['goal_count'] : 0; $target = isset( $g['target'] ) ? (int) $g['target'] : 0; $need = isset( $g['need'] ) ? (int) $g['need'] : 0; $expected = isset( $g['expected_by_now'] ) ? (int) $g['expected_by_now'] : 0; $days_left_g = isset( $g['days_left'] ) ? (int) $g['days_left'] : 1; $behind = $expected - $goal_count; if ( $behind < 0 ) { $behind = 0; } if ( $days_left_g < 1 ) { $days_left_g = 1; } if ( $is_negative ) { $status_key = 'on-track'; $status_label = 'On track'; } $ppc = isset( $g['points_per_completion'] ) ? (int) $g['points_per_completion'] : 0; if ( $ppc < 0 ) { $ppc = 0; } $is_anki_cards_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type ) : ( 'anki_cards' === strtolower( trim( (string) $goal_type ) ) ); $bonus_points = 0; $penalty_points = 0; if ( $ppc > 0 ) { if ( $is_negative ) { $bonus_points = (int) TCT_Utils::compute_negative_goal_bonus( $ppc, $goal_type, $threshold ); $penalty_points = (int) TCT_Utils::compute_violation_penalty( $ppc, 1 ); } elseif ( $is_anki_cards_goal ) { $bonus_points = (int) $ppc; $penalty_points = -1 * abs( (int) $ppc ); } elseif ( $target > 0 ) { $bonus_points = (int) TCT_Utils::compute_bonus_points( $ppc, $target ); if ( $bonus_points < 0 ) { $bonus_points = 0; } $penalty_points = (int) TCT_Utils::compute_penalty_points( $ppc, $target, $goal_count ); if ( $penalty_points > 0 ) { $penalty_points = -1 * abs( $penalty_points ); } } } $pace_line1 = ''; $pace_line2 = ''; if ( ! $is_negative && $target > 0 ) { if ( $goal_count >= $target ) { $span_g = isset( $g['period_span'] ) ? (int) $g['period_span'] : 1; if ( $span_g < 1 ) { $span_g = 1; } if ( $span_g > 1 ) { $pace_line1 = 'Done for this period'; } else { $pace_line1 = 'Done for this ' . (string) $g['unit']; } } else { if ( $days_left_g <= 1 ) { $pace_line1 = 'Need ' . (int) $need . ' today'; } else { if ( 'critical' === $status_key ) { $req = (int) ceil( (float) $need / (float) max( 1, $days_left_g ) ); if ( $req > 1 ) { $pace_line1 = 'Need ' . (int) $req . '/day'; } } elseif ( $behind > 0 ) { $pace_line1 = 'Behind by ' . (int) $behind; } if ( '' === $pace_line1 ) { $pace_line1 = 'On track'; } } } } if ( $is_negative && '' === $pace_line1 ) { $pace_line1 = 'On track'; } $due_tile_attr = ''; $due_is_not_due = false; $due_enabled = false; $due_today = true; $due_next_ymd = ''; $due_next_short = ''; $due_next_full = ''; $tracking_mode_for_due = isset( $g['tracking_mode'] ) ? (string) $g['tracking_mode'] : 'todoist'; if ( ! in_array( $tracking_mode_for_due, array( 'todoist', 'manual', 'hybrid' ), true ) ) { $tracking_mode_for_due = 'todoist'; } $due_schedule_raw = isset( $g['due_schedule_json'] ) && is_string( $g['due_schedule_json'] ) ? (string) $g['due_schedule_json'] : ''; if ( 'manual' === $tracking_mode_for_due && '' !== trim( $due_schedule_raw ) && class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_due_schedule_config' ) ) && is_callable( array( 'TCT_Interval', 'due_schedule_is_due_on_local_date' ) ) && is_callable( array( 'TCT_Interval', 'due_schedule_next_due_local_date' ) ) ) { $due_cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_raw ); $due_enabled = is_array( $due_cfg ) && ! empty( $due_cfg['enabled'] ); if ( $due_enabled ) { $tz_due = ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' ); $today_due = ( new DateTimeImmutable( 'now', $tz_due ) )->format( 'Y-m-d' ); $due_today = (bool) TCT_Interval::due_schedule_is_due_on_local_date( $due_cfg, $today_due, $tz_due ); $due_next_ymd = TCT_Interval::due_schedule_next_due_local_date( $due_cfg, $today_due, $tz_due ); if ( is_string( $due_next_ymd ) && '' !== $due_next_ymd ) { try { $next_dt = new DateTimeImmutable( $due_next_ymd . ' 00:00:00', $tz_due ); $due_next_short = $next_dt->format( 'D' ); $due_next_full = $next_dt->format( 'l' ); } catch ( Exception $e ) { $due_next_short = ''; $due_next_full = ''; } } $due_tile_attr = ' data-tct-due-enabled="1" data-tct-due-today="' . ( $due_today ? '1' : '0' ) . '"'; if ( '' !== $due_next_ymd ) { $due_tile_attr .= ' data-tct-next-due-ymd="' . esc_attr( $due_next_ymd ) . '"'; } if ( '' !== $due_next_short ) { $due_tile_attr .= ' data-tct-next-due-label="' . esc_attr( $due_next_short ) . '"'; } if ( '' !== $due_next_full ) { $due_tile_attr .= ' data-tct-next-due-weekday="' . esc_attr( $due_next_full ) . '"'; } if ( ! $due_today ) { $due_is_not_due = true; $label_nd = '' !== $due_next_short ? $due_next_short : $due_next_ymd; if ( '' !== $label_nd ) { $pace_line1 = 'Next due: ' . $label_nd; } else { $pace_line1 = 'Not due today'; } $pace_line2 = ''; } } } $goal_classes = 'tct-domain-goal tct-goal-status-' . $status_key; if ( $is_negative ) { $goal_classes .= ' tct-goal-negative tct-goal-type-' . esc_attr( $goal_type ); } if ( $due_is_not_due ) { $goal_classes .= ' tct-goal-not-due'; } if ( $availability_enabled ) { $goal_classes .= ' tct-goal-availability-enabled'; } if ( $availability_is_paused ) { $goal_classes .= ' tct-goal-paused'; } elseif ( $availability_is_active ) { $goal_classes .= ' tct-goal-active-cycle'; } $goal_id = isset( $g['goal_id'] ) ? (int) $g['goal_id'] : 0; $link_url = $this->link_url_from_goal_row( $g ); $link_url_attr = ''; $link_url_attr_value = $this->goal_link_output_attr_value( $link_url ); if ( '' !== $link_url_attr_value ) { $link_url_attr = ' data-goal-link-url="' . esc_attr( $link_url_attr_value ) . '"'; } $plant_name = isset( $g['plant_name'] ) ? trim( (string) $g['plant_name'] ) : ''; $vitality_data = ( isset( $g['vitality_data'] ) && is_array( $g['vitality_data'] ) ) ? $g['vitality_data'] : array(); if ( ! isset( $vitality_data['vitality'] ) ) { $vitality_data['vitality'] = 100; } if ( ! isset( $vitality_data['target'] ) ) { $vitality_data['target'] = $target; } if ( ! isset( $vitality_data['achieved'] ) ) { $vitality_data['achieved'] = $goal_count; } if ( ! isset( $vitality_data['time_remaining_seconds'] ) ) { $vitality_data['time_remaining_seconds'] = 0; } if ( ! isset( $vitality_data['time_remaining_label'] ) ) { $vitality_data['time_remaining_label'] = ''; } if ( ! isset( $vitality_data['loop_start_utc_mysql'] ) ) { $vitality_data['loop_start_utc_mysql'] = ''; } if ( ! isset( $vitality_data['loop_end_utc_mysql'] ) ) { $vitality_data['loop_end_utc_mysql'] = ''; } $vit_value = isset( $vitality_data['vitality'] ) ? (int) $vitality_data['vitality'] : 100; if ( $vit_value < 0 ) { $vit_value = 0; } if ( $vit_value > 100 ) { $vit_value = 100; } $vit_target = isset( $vitality_data['target'] ) ? (int) $vitality_data['target'] : $target; $vit_achieved = isset( $vitality_data['achieved'] ) ? (int) $vitality_data['achieved'] : $goal_count; $vit_remaining_seconds = isset( $vitality_data['time_remaining_seconds'] ) ? (int) $vitality_data['time_remaining_seconds'] : 0; $vit_remaining_label = isset( $vitality_data['time_remaining_label'] ) ? (string) $vitality_data['time_remaining_label'] : ''; $vit_loop_start_utc = isset( $vitality_data['loop_start_utc_mysql'] ) ? (string) $vitality_data['loop_start_utc_mysql'] : ''; $vit_loop_end_utc = isset( $vitality_data['loop_end_utc_mysql'] ) ? (string) $vitality_data['loop_end_utc_mysql'] : ''; $vit_title = 'Target: ' . (int) $vit_target . "\n" . 'Achieved: ' . (int) $vit_achieved . "\n" . 'Time remaining: ' . (string) $vit_remaining_label . "\n" . 'Vitality: ' . (int) $vit_value . '%'; $tile_key = $goal_id > 0 ? (string) $goal_id : (string) abs( crc32( (string) $g['goal_name'] ) ); $vit_tooltip_id = 'tct-vitality-tooltip-' . $tile_key; $vit_r = 16.0; $vit_circ = 2.0 * pi() * $vit_r; $vit_progress = ( (float) $vit_value ) / 100.0; if ( $vit_progress < 0.0 ) { $vit_progress = 0.0; } elseif ( $vit_progress > 1.0 ) { $vit_progress = 1.0; } $vit_dasharray = number_format( $vit_circ, 2, '.', '' ); $vit_dashoffset = number_format( $vit_circ * ( 1.0 - $vit_progress ), 2, '.', '' ); $vit_onclick = "var t=document.getElementById('{$vit_tooltip_id}');if(!t){return;}if(t.hasAttribute('hidden')){t.removeAttribute('hidden');this.setAttribute('aria-expanded','true');}else{t.setAttribute('hidden','hidden');this.setAttribute('aria-expanded','false');}"; $timer_duration_seconds = isset( $g['timer_duration_seconds'] ) ? (int) $g['timer_duration_seconds'] : 0; $alarm_sound = isset( $g['alarm_sound'] ) && is_string( $g['alarm_sound'] ) ? (string) $g['alarm_sound'] : ''; $alarm_duration = isset( $g['alarm_duration'] ) ? (int) $g['alarm_duration'] : 0; $alarm_vibration = isset( $g['alarm_vibration'] ) ? (int) $g['alarm_vibration'] : 0; $has_timer = ( $timer_duration_seconds > 0 && ! $is_negative ); $context_attr = ''; if ( $is_complete_context ) { $context_attr = ' data-tct-context="complete"'; } elseif ( $is_paused_context ) { $context_attr = ' data-tct-context="paused"'; } $tile_period_unit = isset( $g['unit'] ) && is_string( $g['unit'] ) ? (string) $g['unit'] : ''; $tile_period_span = isset( $g['period_span'] ) ? (int) $g['period_span'] : 1; if ( $tile_period_span < 1 ) { $tile_period_span = 1; } $tile_vat = isset( $g['visible_after_time'] ) && is_string( $g['visible_after_time'] ) ? trim( (string) $g['visible_after_time'] ) : ''; $is_sleep_tracking = isset( $g['sleep_tracking_enabled'] ) && 1 === (int) $g['sleep_tracking_enabled'];
        $is_wake_time_goal = isset( $g['wake_time_enabled'] ) && 1 === (int) $g['wake_time_enabled'];
        $wake_time_target = ( isset( $g['wake_time_target'] ) && is_string( $g['wake_time_target'] ) ) ? trim( (string) $g['wake_time_target'] ) : '';
        $is_bed_time_goal = isset( $g['bed_time_enabled'] ) && 1 === (int) $g['bed_time_enabled'];
        $bed_time_target = ( isset( $g['bed_time_target'] ) && is_string( $g['bed_time_target'] ) ) ? trim( (string) $g['bed_time_target'] ) : '';
 $sleep_tile_attr = ''; $sleep_state_key = ''; $sleep_state_title = ''; $sleep_date = ''; $sleep_bed_time = ''; $sleep_wake_time = ''; $sleep_duration = ''; $sleep_rollover_time = ''; if ( $is_sleep_tracking ) { $goal_classes .= ' tct-goal-sleep'; $sleep_rollover_time = isset( $g['sleep_rollover_time'] ) && is_string( $g['sleep_rollover_time'] ) ? trim( (string) $g['sleep_rollover_time'] ) : ''; if ( ! preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', (string) $sleep_rollover_time ) ) { $sleep_rollover_time = '18:00'; } $tz = TCT_Utils::wp_timezone(); try { $now_tz = new DateTimeImmutable( 'now', $tz ); $today_tz = $now_tz->setTime( 0, 0, 0 ); $parts = explode( ':', $sleep_rollover_time ); $rh = isset( $parts[0] ) ? (int) $parts[0] : 18; $rm = isset( $parts[1] ) ? (int) $parts[1] : 0; $rollover_tz = $today_tz->setTime( $rh, $rm, 0 ); $sleep_date = ( $now_tz->getTimestamp() < $rollover_tz->getTimestamp() ) ? $today_tz->sub( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ) : $today_tz->format( 'Y-m-d' ); } catch ( Exception $e ) { $sleep_date = gmdate( 'Y-m-d' ); } $user_id = (int) get_current_user_id(); if ( $user_id > 0 && $goal_id > 0 && method_exists( 'TCT_DB', 'get_sleep_cycle' ) ) { $cycle = TCT_DB::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_array( $cycle ) ) { $sleep_bed_time = isset( $cycle['bed_time'] ) && is_string( $cycle['bed_time'] ) ? (string) $cycle['bed_time'] : ''; $sleep_wake_time = isset( $cycle['wake_time'] ) && is_string( $cycle['wake_time'] ) ? (string) $cycle['wake_time'] : ''; $sleep_duration = isset( $cycle['duration_hhmm'] ) && is_string( $cycle['duration_hhmm'] ) ? (string) $cycle['duration_hhmm'] : ''; } } if ( '' === $sleep_bed_time ) { $sleep_state_key = 'A'; $sleep_state_title = 'Enter Bedtime'; } elseif ( '' === $sleep_wake_time ) { $sleep_state_key = 'B'; $sleep_state_title = 'Enter Waketime'; } else { $sleep_state_key = 'C'; $sleep_state_title = 'Track Sleep'; if ( '' === $sleep_duration && method_exists( 'TCT_DB', 'calculate_sleep_duration_hhmm' ) ) { $sleep_duration = (string) TCT_DB::calculate_sleep_duration_hhmm( $sleep_bed_time, $sleep_wake_time ); } } $sleep_tile_attr = ' data-tct-sleep-enabled="1"' . ' data-tct-sleep-state="' . esc_attr( (string) $sleep_state_key ) . '"' . ' data-tct-sleep-date="' . esc_attr( (string) $sleep_date ) . '"' . ' data-tct-sleep-is-default="1"' . ' data-tct-sleep-rollover="' . esc_attr( (string) $sleep_rollover_time ) . '"' . ' data-tct-sleep-bed-time="' . esc_attr( (string) $sleep_bed_time ) . '"' . ' data-tct-sleep-wake-time="' . esc_attr( (string) $sleep_wake_time ) . '"' . ' data-tct-sleep-duration="' . esc_attr( (string) $sleep_duration ) . '"'; } $html .= '<div class="' . esc_attr( $goal_classes ) . '" data-tct-goal-tile="1" data-goal-id="' . esc_attr( (int) $goal_id ) . '"' . ' data-goal-type="' . esc_attr( (string) $goal_type ) . '"' . ' data-threshold="' . esc_attr( null !== $threshold ? (string) $threshold : '' ) . '"' . ' data-timer-duration="' . esc_attr( (int) $timer_duration_seconds ) . '"' . ' data-alarm-sound="' . esc_attr( (string) $alarm_sound ) . '"' . ' data-alarm-duration="' . esc_attr( (int) $alarm_duration ) . '"' . ' data-alarm-vibration="' . esc_attr( (int) $alarm_vibration ) . '"' . ' data-plant-name="' . esc_attr( (string) $plant_name ) . '"' . ' data-vitality="' . esc_attr( (int) $vit_value ) . '"' . ' data-vitality-target="' . esc_attr( (int) $vit_target ) . '"' . ' data-vitality-achieved="' . esc_attr( (int) $vit_achieved ) . '"' . ' data-vitality-time-remaining="' . esc_attr( (int) $vit_remaining_seconds ) . '"' . ' data-vitality-time-remaining-label="' . esc_attr( (string) $vit_remaining_label ) . '"' . ' data-vitality-loop-start-utc="' . esc_attr( (string) $vit_loop_start_utc ) . '"' . ' data-vitality-loop-end-utc="' . esc_attr( (string) $vit_loop_end_utc ) . '"' . ' data-period-unit="' . esc_attr( $tile_period_unit ) . '"' . ' data-period-span="' . esc_attr( (int) $tile_period_span ) . '"' . ' data-visible-after-time="' . esc_attr( $tile_vat ) . '"' . ' data-tct-availability-enabled="' . esc_attr( $availability_enabled ? '1' : '0' ) . '"' . ' data-tct-availability-phase="' . esc_attr( (string) $availability_phase ) . '"' . ' data-tct-goal-paused="' . esc_attr( $availability_is_paused ? '1' : '0' ) . '"' . ' data-tct-availability-label="' . esc_attr( (string) $availability_state_label ) . '"' . ' data-tct-availability-meta="' . esc_attr( (string) $availability_state_meta ) . '"' . $due_tile_attr . $context_attr . $sleep_tile_attr . '>'; $html .= '<div class="tct-domain-goal-top">'; $html .= '<div class="tct-domain-goal-main">'; $payload_json = isset( $g['edit_payload_json'] ) ? (string) $g['edit_payload_json'] : ''; $cd_text = '00h 00m 00s'; if ( $vit_remaining_seconds > 0 ) { $cd_total = (int) $vit_remaining_seconds; $cd_days = (int) floor( $cd_total / 86400 ); $cd_rem = (int) ( $cd_total % 86400 ); $cd_hours = (int) floor( $cd_rem / 3600 ); $cd_rem = (int) ( $cd_rem % 3600 ); $cd_mins = (int) floor( $cd_rem / 60 ); $cd_secs = (int) ( $cd_rem % 60 ); $cd_text = sprintf( '%02dh %02dm %02ds', $cd_hours, $cd_mins, $cd_secs ); if ( $cd_days > 0 ) { $cd_text = (int) $cd_days . 'd ' . $cd_text; } } $html .= '<div class="tct-domain-goal-title-row">'; $html .= '<div class="tct-domain-goal-title">' . esc_html( $g['goal_name'] ) . '</div>'; $html .= '</div>'; if ( $availability_enabled && ! $is_mobile_surface ) { $html .= '<div class="tct-domain-goal-sub tct-goal-availability-row">'; $html .= '<span class="tct-goal-availability-badge tct-goal-availability-badge-' . esc_attr( $availability_is_paused ? 'paused' : 'active' ) . '">' . esc_html( (string) $availability_badge_text ) . '</span>'; if ( '' !== $availability_state_label ) { $html .= '<span class="tct-goal-availability-text">' . esc_html( (string) $availability_state_label ) . '</span>'; } $html .= '</div>'; if ( '' !== $availability_state_meta ) { $html .= '<div class="tct-domain-goal-sub tct-goal-availability-meta">' . esc_html( (string) $availability_state_meta ) . '</div>'; } } $html .= '<div class="tct-goal-meta-grid">'; $html .= '<div class="tct-goal-meta-left">'; $tile_unit = isset( $g['unit'] ) ? (string) $g['unit'] : 'week'; $tile_span = isset( $g['period_span'] ) ? (int) $g['period_span'] : 1; if ( $tile_span < 1 ) { $tile_span = 1; } $display_span = (int) $tile_span; $display_unit = (string) $tile_unit; if ( 'semiannual' === $display_unit ) { $display_unit = 'month'; $display_span = (int) $display_span * 6; } $unit_labels = array( 'hour' => array( 'singular' => 'hour', 'plural' => 'hours' ), 'day' => array( 'singular' => 'day', 'plural' => 'days' ), 'week' => array( 'singular' => 'week', 'plural' => 'weeks' ), 'month' => array( 'singular' => 'month', 'plural' => 'months' ), 'quarter' => array( 'singular' => 'quarter', 'plural' => 'quarters' ), 'year' => array( 'singular' => 'year', 'plural' => 'years' ), ); $unit_s = isset( $unit_labels[ $display_unit ] ) ? $unit_labels[ $display_unit ]['singular'] : ( '' !== $display_unit ? $display_unit : 'period' ); $unit_p = isset( $unit_labels[ $display_unit ] ) ? $unit_labels[ $display_unit ]['plural'] : ( '' !== $display_unit ? $display_unit . 's' : 'periods' ); $unit_word = ( 1 === (int) $display_span ) ? $unit_s : $unit_p; $ach_i = (int) $goal_count; if ( $ach_i < 0 ) { $ach_i = 0; } $tgt_i = (int) $target; if ( $tgt_i < 0 ) { $tgt_i = 0; } if ( $is_negative ) { $is_never = TCT_Utils::is_never_goal( $goal_type, $threshold ); if ( $is_never ) { if ( $ach_i <= 0 ) { $completed_label = 'Clean this period'; } else { $completed_label = $ach_i . ' ' . ( 1 === $ach_i ? 'violation' : 'violations' ); } } else { $th_display = ( null !== $threshold && $threshold >= 0 ) ? (int) $threshold : 0; if ( $ach_i <= 0 ) { $completed_label = '0 of ' . $th_display . ' allowed'; } else { $completed_label = $ach_i . ' of ' . $th_display . ' allowed'; } } } else { if ( $is_no_interval_positive ) { if ( $ach_i <= 0 ) { $completed_label = 'None completed'; } else { $completed_label = $ach_i . ' completed'; } } else { if ( $ach_i <= 0 || $tgt_i <= 0 ) { $completed_label = 'None completed'; } else { $completed_label = $ach_i . ' of ' . $tgt_i . ' completed'; } } } if ( $is_negative ) { $is_never = TCT_Utils::is_never_goal( $goal_type, $threshold ); if ( $is_never ) { if ( 1 === (int) $display_span ) { $html .= '<div class="tct-domain-goal-goalline"><strong>Never <span class="tct-muted">(per ' . esc_html( $unit_word ) . ')</span></strong></div>'; } else { $html .= '<div class="tct-domain-goal-goalline"><strong>Never <span class="tct-muted">(per ' . esc_html( (int) $display_span ) . ' ' . esc_html( $unit_word ) . ')</span></strong></div>'; } } else { $th_display = ( null !== $threshold && $threshold >= 0 ) ? (int) $threshold : 0; if ( 1 === (int) $display_span ) { $html .= '<div class="tct-domain-goal-goalline"><strong>Limit <span data-tct-goal-target>' . esc_html( $th_display ) . '</span> per ' . esc_html( $unit_word ) . '</strong></div>'; } else { $html .= '<div class="tct-domain-goal-goalline"><strong>Limit <span data-tct-goal-target>' . esc_html( $th_display ) . '</span> per ' . esc_html( (int) $display_span ) . ' ' . esc_html( $unit_word ) . '</strong></div>'; } } } else { if ( $is_no_interval_positive ) { $html .= '<div class="tct-domain-goal-goalline"><strong>No interval target</strong></div>'; } else { if ( 1 === (int) $display_span ) { $html .= '<div class="tct-domain-goal-goalline"><strong><span data-tct-goal-target>' . esc_html( $target ) . '</span> every ' . esc_html( $unit_word ) . '</strong></div>'; } else { $html .= '<div class="tct-domain-goal-goalline"><strong><span data-tct-goal-target>' . esc_html( $target ) . '</span> every ' . esc_html( (int) $display_span ) . ' ' . esc_html( $unit_word ) . '</strong></div>'; } } } $html .= '<div class="tct-domain-goal-sub tct-muted tct-goal-completed-line"><span data-tct-goal-completed-label>' . esc_html( $completed_label ) . '</span><span data-tct-goal-count hidden>' . esc_html( $goal_count ) . '</span></div>'; $show_status_line = false; $show_pace_line1 = ( '' !== $pace_line1 ); if ( '' === $pace_line2 ) { $user_id_af = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0; if ( $user_id_af > 0 ) { static $now_tz_cache = null; if ( null === $now_tz_cache ) { $tz_cache = ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' ); $now_tz_cache = new DateTimeImmutable( 'now', $tz_cache ); } $af_line = $this->get_allowed_fails_pace_line( $user_id_af, $g, $now_tz_cache ); if ( is_string( $af_line ) && '' !== $af_line ) { $pace_line2 = $af_line; } } } $show_pace_line2 = ( '' !== $pace_line2 ); $goal_is_complete = ( ! $is_negative && $target > 0 && $goal_count >= $target ); $show_countdown = $is_complete_context ? true : ( ! $goal_is_complete ); if ( $is_no_interval_positive && ! $is_negative ) { $show_countdown = false; } if ( $availability_is_paused ) { $show_pace_line1 = false; $show_pace_line2 = false; $show_countdown = false; } $html .= '<div class="tct-domain-goal-sub tct-muted tct-goal-status-line"' . ( $show_status_line ? '' : ' hidden="hidden"' ) . '>'; if ( $show_status_line ) { $html .= '<span class="tct-goal-status-pill tct-goal-status-pill-' . esc_attr( $status_key ) . '">' . esc_html( $status_label ) . '</span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-goal-sub tct-muted tct-goal-pace-line1"' . ( $show_pace_line1 ? '' : ' hidden="hidden"' ) . '>'; if ( '' !== $pace_line1 ) { $html .= '<span class="tct-goal-pace-text">' . esc_html( $pace_line1 ) . '</span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-goal-sub tct-muted tct-goal-pace-line2"' . ( $show_pace_line2 ? '' : ' hidden="hidden"' ) . '>' . esc_html( $pace_line2 ) . '</div>'; $html .= '<div class="tct-domain-goal-sub tct-muted tct-goal-countdown-line"' . ( $show_countdown ? '' : ' hidden="hidden"' ) . '><span class="tct-goal-countdown-time" data-tct-goal-countdown="1">' . esc_html( $cd_text ) . '</span></div>'; $html .= '<div class="tct-domain-goal-sub tct-muted tct-goal-vitality-row">'; $html .= '<button type="button" class="tct-goal-vitality-trigger" data-tct-vitality-trigger="1" aria-expanded="false" aria-controls="' . esc_attr( $vit_tooltip_id ) . '" title="' . esc_attr( $vit_title ) . '" onclick="' . esc_attr( $vit_onclick ) . '" style="background:none;border:0;padding:0;margin:0;display:inline-flex;align-items:center;gap:6px;cursor:pointer;font:inherit;color:inherit;text-align:left;">'; $html .= '<span class="tct-vitality-ring" aria-hidden="true" style="display:inline-block;width:28px;height:28px;flex:0 0 auto;">'; $html .= '<svg viewBox="0 0 40 40" width="28" height="28" aria-hidden="true" focusable="false">'; $html .= '<circle cx="20" cy="20" r="16" fill="none" stroke="#dcdcde" stroke-width="4"></circle>'; $html .= '<circle cx="20" cy="20" r="16" fill="none" stroke="#2271b1" stroke-width="4" stroke-linecap="round" stroke-dasharray="' . esc_attr( $vit_dasharray ) . '" stroke-dashoffset="' . esc_attr( $vit_dashoffset ) . '" transform="rotate(-90 20 20)"></circle>'; $html .= '</svg>'; $html .= '</span>'; $html .= '<span class="tct-vitality-label">Vitality: <span data-tct-vitality-value>' . esc_html( (int) $vit_value ) . '</span></span>'; $html .= '</button>'; $html .= '<div id="' . esc_attr( $vit_tooltip_id ) . '" class="tct-vitality-tooltip" hidden data-tct-vitality-tooltip="1" style="margin-top:6px;border:1px solid #dcdcde;background:#fff;border-radius:6px;padding:8px;max-width:360px;">'; $html .= '<div><span class="tct-muted">Target:</span> <strong><span data-tct-vitality-target>' . esc_html( (int) $vit_target ) . '</span></strong></div>'; $html .= '<div><span class="tct-muted">Achieved:</span> <strong><span data-tct-vitality-achieved>' . esc_html( (int) $vit_achieved ) . '</span></strong></div>'; $html .= '<div><span class="tct-muted">Time remaining:</span> <strong><span data-tct-vitality-time-remaining-label>' . esc_html( (string) $vit_remaining_label ) . '</span></strong></div>'; $html .= '<div><span class="tct-muted">Vitality:</span> <strong><span data-tct-vitality-tooltip-value>' . esc_html( (int) $vit_value ) . '</span>%</strong></div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; if ( $ppc > 0 ) { if ( $is_negative ) { $points_title = 'Penalty per violation: ' . (int) $penalty_points; $points_title .= "\n" . 'Bonus (if kept): +' . (int) $bonus_points; $points_title .= "\n" . 'Penalties escalate with each violation'; } else { $points_title = 'Completion points: +' . (int) $ppc; $points_title .= "\n" . 'Bonus points (if met): +' . (int) $bonus_points; $points_title .= "\n" . 'Penalty points (if missed): ' . (int) $penalty_points; } $html .= '<div class="tct-goal-points-row">'; $html .= '<div class="tct-goal-points-pill" title="' . esc_attr( $points_title ) . '">'; if ( $is_negative ) { $html .= '<span class="tct-goal-points-pill-completion">0</span>'; $html .= '<span class="tct-goal-points-pill-bonus">+' . esc_html( (int) $bonus_points ) . '</span>'; $html .= '<span class="tct-goal-points-pill-penalty">' . esc_html( (int) $penalty_points ) . '</span>'; } else { $html .= '<span class="tct-goal-points-pill-completion">+' . esc_html( (int) $ppc ) . '</span>'; $html .= '<span class="tct-goal-points-pill-bonus">+' . esc_html( (int) $bonus_points ) . '</span>'; $html .= '<span class="tct-goal-points-pill-penalty">' . esc_html( (int) $penalty_points ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; if ( '' !== $plant_name ) { $plant_img_url = null; if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'resolve_vitality_plant_image_url' ) ) { $plant_img_url = TCT_Utils::resolve_vitality_plant_image_url( $plant_name, (int) $vit_value, 'medium' ); } $plant_hidden_attr = $plant_img_url ? '' : ' hidden="hidden"'; $html .= "<div class=\"tct-vitality-plant-wrap\" data-tct-vitality-plant-wrap=\"1\"" . $plant_hidden_attr . ">"; $html .= "<img class=\"tct-vitality-plant-img\" data-tct-vitality-plant-img=\"1\"" . ( $plant_img_url ? ( " src=\"" . esc_url( $plant_img_url ) . "\"" ) : "" ) . " alt=\"" . esc_attr( $plant_name ) . "\" loading=\"lazy\" decoding=\"async\" />"; $html .= "<div class=\"tct-vitality-plant-name\" data-tct-vitality-plant-name=\"1\">" . esc_html( $plant_name ) . "</div>"; $html .= "</div>"; } $html .= '</div>'; $html .= '</div>'; if ( $is_sleep_tracking ) { $bed_id = 'tct-sleep-bedtime-' . $tile_key; $wake_id = 'tct-sleep-waketime-' . $tile_key; $html .= '<div class="tct-sleep-tile" data-tct-sleep-tile-ui="1">'; $date_input_id = 'tct-sleep-date-' . $tile_key; $sleep_end_date = ''; try { $tz_for_sleep = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' ); $dt_sleep = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', (string) $sleep_date . ' 00:00:00', $tz_for_sleep ); if ( $dt_sleep instanceof DateTimeImmutable ) { $sleep_end_date = $dt_sleep->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d' ); } } catch ( Exception $e ) { $sleep_end_date = ''; } if ( '' === $sleep_end_date && is_string( $sleep_date ) && '' !== $sleep_date ) { $sleep_end_date = gmdate( 'Y-m-d', strtotime( (string) $sleep_date . ' +1 day' ) ); } $night_range_label = ''; if ( is_string( $sleep_date ) && '' !== $sleep_date && '' !== $sleep_end_date ) { $sd_m = (int) substr( $sleep_date, 5, 2 ); $sd_d = (int) substr( $sleep_date, 8, 2 ); $ed_m = (int) substr( $sleep_end_date, 5, 2 ); $ed_d = (int) substr( $sleep_end_date, 8, 2 ); $night_range_label = $sd_m . '/' . $sd_d . '-' . $ed_m . '/' . $ed_d; } $html .= '<div class="tct-sleep-date-row">'; $html .= '<span class="tct-sleep-date-label">Night of</span>'; $html .= '<button type="button" class="tct-sleep-date-btn" data-tct-sleep-calendar-btn="1" title="Select night" aria-label="Select night">'; $html .= '<span class="tct-sleep-night-range" data-tct-sleep-night-range="1">' . esc_html( $night_range_label ) . '</span>'; $html .= '<svg class="tct-sleep-date-btn-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'; $html .= '</button>'; $html .= '<input id="' . esc_attr( $date_input_id ) . '" type="hidden" class="tct-sleep-date-input" value="' . esc_attr( (string) $sleep_date ) . '" data-tct-sleep-date-input="1" />'; $html .= '</div>'; if ( 'A' === (string) $sleep_state_key ) { $html .= '<div class="tct-sleep-action-row" data-tct-sleep-action="bedtime">'; $html .= '<span class="tct-muted tct-sleep-action-label">Bedtime</span>'; $html .= '<button type="button" class="tct-goal-action-btn tct-sleep-mini-btn tct-sleep-now-btn" data-tct-sleep-now="1" title="Use the current time (only enabled for the current night).">Now</button>'; $html .= '<button type="button" class="tct-goal-action-btn tct-sleep-mini-btn tct-sleep-manual-btn" data-tct-sleep-manual="1" title="Enter a time manually.">Manual</button>'; $html .= '</div>'; } elseif ( 'B' === (string) $sleep_state_key ) { $bed_b_12h = (string) $sleep_bed_time; if ( '' !== $bed_b_12h && preg_match( '/^(\d{2}):(\d{2})/', $bed_b_12h, $tmb ) ) { $hhb = (int) $tmb[1]; $mmb = (int) $tmb[2]; $ampb = ( $hhb >= 12 ) ? 'PM' : 'AM'; $h12b = $hhb % 12; if ( 0 === $h12b ) { $h12b = 12; } $bed_b_12h = $h12b . ':' . str_pad( (string) $mmb, 2, '0', STR_PAD_LEFT ) . ' ' . $ampb; } $html .= '<div class="tct-sleep-summary-line">'; $html .= '<span class="tct-muted">Bedtime:</span> <strong data-tct-sleep-bedtime-text data-tct-sleep-edit-field="bedtime">' . esc_html( $bed_b_12h ) . '</strong>'; $html .= '</div>'; $html .= '<div class="tct-sleep-action-row" data-tct-sleep-action="waketime">'; $html .= '<span class="tct-muted tct-sleep-action-label">Waketime</span>'; $html .= '<button type="button" class="tct-goal-action-btn tct-sleep-mini-btn tct-sleep-now-btn" data-tct-sleep-now="1" title="Use the current time (only enabled for the current night).">Now</button>'; $html .= '<button type="button" class="tct-goal-action-btn tct-sleep-mini-btn tct-sleep-manual-btn" data-tct-sleep-manual="1" title="Enter a time manually.">Manual</button>'; $html .= '</div>'; } else { $bed_12h = ''; if ( '' !== (string) $sleep_bed_time && preg_match( '/^(\d{2}):(\d{2})/', (string) $sleep_bed_time, $tm ) ) { $hh = (int) $tm[1]; $mm = (int) $tm[2]; $ampm = ( $hh >= 12 ) ? 'PM' : 'AM'; $h12 = $hh % 12; if ( 0 === $h12 ) { $h12 = 12; } $bed_12h = $h12 . ':' . str_pad( (string) $mm, 2, '0', STR_PAD_LEFT ) . ' ' . $ampm; } $wake_12h = ''; if ( '' !== (string) $sleep_wake_time && preg_match( '/^(\d{2}):(\d{2})/', (string) $sleep_wake_time, $tm2 ) ) { $hh2 = (int) $tm2[1]; $mm2 = (int) $tm2[2]; $ampm2 = ( $hh2 >= 12 ) ? 'PM' : 'AM'; $h12b = $hh2 % 12; if ( 0 === $h12b ) { $h12b = 12; } $wake_12h = $h12b . ':' . str_pad( (string) $mm2, 2, '0', STR_PAD_LEFT ) . ' ' . $ampm2; } $html .= '<div class="tct-sleep-summary-list" data-tct-sleep-summary-list>'; $html .= '<div class="tct-sleep-summary-row tct-sleep-editable" data-tct-sleep-edit-field="bedtime"><span class="tct-muted">Bedtime</span><strong data-tct-sleep-bedtime-text>' . esc_html( $bed_12h ) . '</strong></div>'; $html .= '<div class="tct-sleep-summary-row tct-sleep-editable" data-tct-sleep-edit-field="waketime"><span class="tct-muted">Waketime</span><strong data-tct-sleep-waketime-text>' . esc_html( $wake_12h ) . '</strong></div>'; $html .= '<div class="tct-sleep-summary-row"><span class="tct-muted">Duration</span><strong data-tct-sleep-duration-text>' . esc_html( (string) $sleep_duration ) . '</strong></div>'; $html .= '</div>'; } $html .= '</div>'; } else { $last_label_prefix = 'Completed'; if ( $is_negative ) { $is_never = TCT_Utils::is_never_goal( $goal_type, $threshold ); if ( $is_never ) { $last_label_prefix = 'Fell short'; } else { $has_exceeded = TCT_Utils::has_exceeded_negative_goal_limit( $goal_type, $threshold, $goal_count ); if ( $has_exceeded ) { $last_label_prefix = 'Fell short'; } else { $last_label_prefix = 'Enjoyed'; } } } $html .= '<div class="tct-domain-goal-sub tct-muted"><span data-tct-goal-last-prefix>' . esc_html( $last_label_prefix ) . '</span> <span data-tct-goal-last>' . esc_html( (string) $g['last_completed_text'] ) . '</span></div>'; } $complete_title = ''; $button_label = 'Complete'; $tracking_mode = isset( $g['tracking_mode'] ) ? (string) $g['tracking_mode'] : 'todoist'; if ( $is_negative ) { $button_label = TCT_Utils::get_negative_goal_button_label( $goal_type, $threshold, $goal_count ); $is_never = TCT_Utils::is_never_goal( $goal_type, $threshold ); if ( $is_never ) { $complete_title = ' title="Log a slip (penalty applied)."'; } else { $is_violation = TCT_Utils::is_negative_goal_violation( $goal_type, $threshold, $goal_count ); if ( $is_violation ) { $complete_title = ' title="Log exceeding your limit (penalty applied)."'; } else { $complete_title = ' title="Log usage within your limit (no penalty)."'; } } } else { if ( ( 'todoist' === $tracking_mode || 'hybrid' === $tracking_mode ) && ! $is_connected ) { $complete_title = ' title="Logs locally. Connect Todoist to also close a matching active task."'; } elseif ( 'manual' === $tracking_mode ) { $complete_title = ' title="Log a completion (manual goal)."'; } else { $complete_title = ' title="Logs locally and best-effort attempts to close a matching active Todoist task."'; } } $html .= '<div class="tct-goal-actions">'; if ( $is_sleep_tracking ) { $primary_label = 'Completed'; $primary_disabled_attr = ' disabled="disabled"'; $primary_aria_attr = ' aria-disabled="true"'; $primary_title_attr = ' title="Completed for the selected night."'; if ( 'A' === (string) $sleep_state_key ) { $primary_label = 'Enter Bedtime'; $primary_title_attr = ' title="Log bedtime using Now or Manual above."'; } elseif ( 'B' === (string) $sleep_state_key ) { $primary_label = 'Enter Waketime'; $primary_title_attr = ' title="Log wake-time using Now or Manual above."'; } else { $primary_label = 'Completed'; $primary_disabled_attr = ''; $primary_aria_attr = ''; $primary_title_attr = ' title="Click to clear this sleep entry."'; } $html .= '<button type="button" class="tct-goal-action-btn tct-goal-complete-btn tct-sleep-primary-btn"' . $primary_disabled_attr . $primary_aria_attr . $primary_title_attr . '>' . esc_html( $primary_label ) . '</button>'; } else { if ( $is_wake_time_goal || $is_bed_time_goal ) { if ( $is_bed_time_goal ) { $auto_title = 'Auto-scored from Sleep Tracker bed-time.'; if ( '' !== $bed_time_target ) { $auto_title = 'Auto-scored from Sleep Tracker bed-time. Target: ' . $bed_time_target . ' (+/-30m).'; } else { $auto_title = 'Auto-scored from Sleep Tracker bed-time. No target time set; this goal will fail.'; } $auto_class = 'tct-bed-time-primary-btn'; } else { $auto_title = 'Auto-scored from Sleep Tracker wake-time.'; if ( '' !== $wake_time_target ) { $auto_title = 'Auto-scored from Sleep Tracker wake-time. Target: ' . $wake_time_target . ' (+/-30m).'; } else { $auto_title = 'Auto-scored from Sleep Tracker wake-time. No target time set; this goal will fail.'; } $auto_class = 'tct-wake-time-primary-btn'; } $html .= '<button type="button" class="tct-goal-action-btn tct-goal-complete-btn ' . esc_attr( $auto_class ) . '" disabled="disabled" aria-disabled="true" title="' . esc_attr( $auto_title ) . '">' . esc_html( 'Auto' ) . '</button>'; } else { if ( $has_timer ) { $timer_h = (int) floor( $timer_duration_seconds / 3600 ); $timer_m = (int) floor( ( $timer_duration_seconds % 3600 ) / 60 ); $timer_s = (int) ( $timer_duration_seconds % 60 ); if ( $timer_h > 0 ) { $timer_display = sprintf( '%d:%02d:%02d', $timer_h, $timer_m, $timer_s ); } else { $timer_display = sprintf( '%d:%02d', $timer_m, $timer_s ); } $html .= '<button type="button" class="tct-goal-action-btn tct-goal-timer-btn" data-tct-start-timer="1" data-goal-id="' . esc_attr( (int) $goal_id ) . '" title="Start ' . esc_attr( $timer_display ) . ' timer"><span class="dashicons dashicons-clock" aria-hidden="true"></span> ' . esc_html( $timer_display ) . '</button>'; } if ( ! $has_timer ) { $fail_btn_on = isset( $g['fail_button_enabled'] ) ? (int) $g['fail_button_enabled'] : 0; if ( $fail_btn_on && ! $is_negative && ! $availability_is_paused ) { $html .= '<div class="tct-goal-btn-pair">'; $html .= '<button type="button" class="tct-goal-action-btn tct-goal-complete-btn" data-tct-complete-goal="1" data-goal-id="' . esc_attr( (int) $goal_id ) . '"' . $link_url_attr . $complete_title . '>' . esc_html( $button_label ) . '</button>'; $html .= '<button type="button" class="tct-goal-action-btn tct-goal-fail-btn" data-tct-fail-goal="1" data-goal-id="' . esc_attr( (int) $goal_id ) . '" title="Fail this goal for the current interval (penalty applied immediately)">Fail</button>'; $html .= '</div>'; } else { $html .= '<button type="button" class="tct-goal-action-btn tct-goal-complete-btn" data-tct-complete-goal="1" data-goal-id="' . esc_attr( (int) $goal_id ) . '"' . $link_url_attr . $complete_title . '>' . esc_html( $button_label ) . '</button>'; } } } } if ( $goal_id > 0 ) { $html .= '<button type="button" class="tct-goal-action-btn tct-goal-history-btn" data-tct-open-goal-history="1" data-goal-id="' . esc_attr( (int) $goal_id ) . '" aria-label="History" title="History"><span class="dashicons dashicons-backup" aria-hidden="true"></span></button>'; } if ( '' !== $payload_json ) { $html .= '<button type="button" class="tct-goal-action-btn tct-goal-edit-btn" data-tct-open-goal-modal="edit" data-tct-goal="' . esc_attr( $payload_json ) . '" aria-label="Edit goal" title="Edit goal"><span class="dashicons dashicons-edit" aria-hidden="true"></span></button>'; } $html .= '</div>'; if ( $has_timer && ! $is_sleep_tracking ) { $html .= '<div class="tct-goal-timer-overlay" data-tct-timer-overlay hidden="hidden">'; $html .= '<div class="tct-goal-timer-display" data-tct-timer-display>00:00:00</div>'; $html .= '<div class="tct-goal-timer-controls">'; $html .= '<button type="button" class="tct-timer-ctrl-btn tct-timer-pause-btn" data-tct-timer-pause title="Pause"><span class="dashicons dashicons-controls-pause" aria-hidden="true"></span></button>'; $html .= '<button type="button" class="tct-timer-ctrl-btn tct-timer-resume-btn" data-tct-timer-resume title="Resume" hidden="hidden"><span class="dashicons dashicons-controls-play" aria-hidden="true"></span></button>'; $html .= '<button type="button" class="tct-timer-ctrl-btn tct-timer-cancel-btn" data-tct-timer-cancel title="Cancel"><span class="dashicons dashicons-no" aria-hidden="true"></span></button>'; $html .= '<button type="button" class="tct-timer-ctrl-btn tct-timer-complete-btn" data-tct-timer-complete data-goal-id="' . esc_attr( (int) $goal_id ) . '"' . $link_url_attr . ' title="Complete Now">' . esc_html( $button_label ) . '</button>'; $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; return $html; } private function render_kpi_tiles( $user_id ) { global $wpdb; $is_connected = TCT_OAuth::is_connected( $user_id ); $goals_table = TCT_DB::table_goals(); $completions_table = TCT_DB::table_completions(); $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $today_tz = $now_tz->setTime( 0, 0, 0 ); $today_ymd = $today_tz->format( 'Y-m-d' ); $days = 7; $range_start_tz = $today_tz->sub( new DateInterval( 'P' . (int) ( $days - 1 ) . 'D' ) ); $range_end_tz = $now_tz; $range_start_utc = TCT_Utils::dt_to_mysql_utc( $range_start_tz ); $range_end_utc = TCT_Utils::dt_to_mysql_utc( $range_end_tz ); $domains = $this->get_domains( $user_id ); $domain_map = $this->get_domain_map( $domains ); $domain_color_map = $this->get_domain_color_map( $domains ); $roles = $this->get_roles( $user_id ); $roles_by_domain = $this->group_roles_by_domain( $roles ); $role_map = $this->get_role_map( $roles ); $aliases_select = $this->tct_goal_aliases_select_sql(); $link_select = $this->tct_goal_link_url_select_sql(); $notes_select = $this->tct_goal_notes_select_sql(); $due_schedule_select = $this->tct_goal_due_schedule_select_sql(); $availability_cycle_select = $this->tct_goal_availability_cycle_select_sql(); $interval_anchor_select = $this->tct_goal_interval_anchor_select_sql(); $composite_config_select = $this->tct_goal_composite_config_select_sql(); $wake_select = $this->tct_goal_wake_time_select_sql(); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, tracking_mode, label_name, goal_name, {$aliases_select}, {$link_select}, {$notes_select}, {$due_schedule_select}, {$availability_cycle_select}, {$interval_anchor_select}, {$composite_config_select}, plant_name, goal_type, threshold, timer_duration_seconds, alarm_sound, alarm_duration, alarm_vibration, visible_after_time, sleep_tracking_enabled, sleep_rollover_time, {$wake_select}, fail_button_enabled, is_favorite, domain_id, role_id, target, period_unit, period_span, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, points_per_completion, importance, effort, points_enabled_at, created_at, updated_at
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1
                 ORDER BY goal_name ASC", $user_id ), ARRAY_A ); $goal_rows = $this->tct_prepare_goal_rows_for_composite_surface( $goal_rows, 'dashboard', $user_id ); if ( empty( $goal_rows ) ) { return '<div class="tct-card"><p class="tct-muted">No goals yet. Go to the "Goals" tab and click "Add goal".</p></div>'; } $range_counts = $wpdb->get_results( $wpdb->prepare( "SELECT goal_id, COUNT(*) AS cnt
                 FROM {$completions_table}
                 WHERE user_id = %d AND goal_id > 0 AND completed_at >= %s AND completed_at <= %s
                 GROUP BY goal_id", $user_id, $range_start_utc, $range_end_utc ), ARRAY_A ); $range_map = array(); foreach ( $range_counts as $row ) { if ( isset( $row['goal_id'] ) ) { $range_map[ (int) $row['goal_id'] ] = isset( $row['cnt'] ) ? (int) $row['cnt'] : 0; } } $last_rows = $wpdb->get_results( $wpdb->prepare( "SELECT goal_id, MAX(completed_at) AS last_completed_at
                 FROM {$completions_table}
                 WHERE user_id = %d AND goal_id > 0
                 GROUP BY goal_id", $user_id ), ARRAY_A ); $last_map = array(); foreach ( $last_rows as $row ) { if ( isset( $row['goal_id'] ) && isset( $row['last_completed_at'] ) ) { $last_map[ (int) $row['goal_id'] ] = (string) $row['last_completed_at']; } } $format_last_completed = function ( $last_completed_utc_mysql ) use ( $now_tz ) { $last_completed_utc_mysql = is_string( $last_completed_utc_mysql ) ? trim( $last_completed_utc_mysql ) : ''; if ( '' === $last_completed_utc_mysql || '0000-00-00 00:00:00' === $last_completed_utc_mysql ) { return array( 'text' => 'never', 'ts' => 0, ); } $ts = strtotime( $last_completed_utc_mysql . ' UTC' ); if ( false === $ts ) { return array( 'text' => '--', 'ts' => 0, ); } $now_ts = (int) $now_tz->getTimestamp(); $diff_s = $now_ts - (int) $ts; if ( $diff_s >= 0 && $diff_s < 60 ) { return array( 'text' => 'just now', 'ts' => (int) $ts, ); } $abbr = $this->tct_abbrev_time_ago( (int) $ts, (int) $now_ts ); if ( 'just now' === $abbr ) { $text = 'just now'; } elseif ( '--' === $abbr ) { $text = '--'; } else { $text = trim( $abbr ) . ' ago'; } return array( 'text' => $text, 'ts' => (int) $ts, ); }; $compute_status = function ( $count, $target, $day_index, $total_days, $days_left, $seconds_left, $total_seconds, $unit, $span ) { return $this->compute_goal_status_meta( $count, $target, $day_index, $total_days, $days_left, $seconds_left, $total_seconds, $unit, $span ); }; $goals_by_domain_role = array(); foreach ( $goal_rows as $row ) { $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0; if ( $goal_id <= 0 ) { continue; } $label = isset( $row['label_name'] ) && is_string( $row['label_name'] ) ? (string) $row['label_name'] : ''; $tracking_mode = isset( $row['tracking_mode'] ) ? (string) $row['tracking_mode'] : 'todoist'; if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) { $tracking_mode = 'todoist'; } $goal_name = isset( $row['goal_name'] ) ? (string) $row['goal_name'] : ''; if ( '' === $goal_name ) { $goal_name = '' !== $label ? $label : 'Goal'; } $domain_id = isset( $row['domain_id'] ) ? (int) $row['domain_id'] : 0; $role_id = isset( $row['role_id'] ) ? (int) $row['role_id'] : 0; if ( $role_id > 0 && isset( $role_map[ $role_id ] ) ) { $domain_id = (int) $role_map[ $role_id ]['domain_id']; } if ( $domain_id > 0 && ! isset( $domain_map[ $domain_id ] ) ) { $domain_id = 0; } $intervals = $this->intervals_from_goal_row( $row ); if ( empty( $intervals ) ) { continue; } $chosen = $intervals[0]; $target = isset( $chosen['target'] ) ? (int) $chosen['target'] : 0; if ( $target < 0 ) { $target = 0; } $unit = isset( $chosen['period_unit'] ) ? sanitize_text_field( (string) $chosen['period_unit'] ) : 'week'; if ( ! in_array( $unit, array( 'day', 'week', 'month', 'quarter', 'semiannual', 'year' ), true ) ) { $unit = 'week'; } $span = isset( $chosen['period_span'] ) ? (int) $chosen['period_span'] : 1; if ( $span < 1 ) { $span = 1; } $mode = 'calendar'; $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0; if ( $ppc < 0 ) { $ppc = 0; } $vitality_data = array( 'vitality' => 100, 'target' => $target, 'achieved' => 0, 'loop_start_utc_mysql' => '', 'loop_end_utc_mysql' => '', 'time_remaining_seconds' => 0, 'time_remaining_label' => '', ); if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) { $goal_row_for_vitality = array( 'id' => $goal_id, 'points_per_completion' => $ppc, 'target' => $target, 'period_unit' => $unit, 'period_span' => (int) $span, 'goal_type' => isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive', 'threshold' => isset( $row['threshold'] ) && is_numeric( $row['threshold'] ) ? (int) $row['threshold'] : null, 'created_at' => isset( $row['created_at'] ) ? (string) $row['created_at'] : '', 'updated_at' => isset( $row['updated_at'] ) ? (string) $row['updated_at'] : '', 'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0, 'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '', 'interval_anchor_json' => isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ? (string) $row['interval_anchor_json'] : '', 'composite_config_json' => isset( $row['composite_config_json'] ) && is_string( $row['composite_config_json'] ) ? (string) $row['composite_config_json'] : '', ); try { $maybe_vitality = TCT_Vitality::compute_for_goal( $user_id, $goal_row_for_vitality, $now_tz ); if ( is_array( $maybe_vitality ) ) { $vitality_data = array_merge( $vitality_data, $maybe_vitality ); } } catch ( Exception $e ) { } } else { $bounds = $this->current_loop_bounds_for_goal( $row, $now_tz, $unit, $span ); if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) ) { $start_utc = TCT_Utils::dt_to_mysql_utc( $bounds['start'] ); $end_utc = TCT_Utils::dt_to_mysql_utc( $bounds['end'] ); $count_sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id=%d AND goal_id=%d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc, $end_utc ); $vitality_data['achieved'] = (int) $wpdb->get_var( $count_sql ); } } $goal_count = isset( $vitality_data['achieved'] ) ? (int) $vitality_data['achieved'] : 0; $progress_pct = 0; if ( $target > 0 ) { $progress_pct = min( 100, (int) round( ( $goal_count / $target ) * 100 ) ); } $range_total = isset( $range_map[ $goal_id ] ) ? (int) $range_map[ $goal_id ] : 0; $avg_per_day = $days > 0 ? ( $range_total / $days ) : 0; $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0; if ( $ppc < 0 ) { $ppc = 0; } $points_in_range = $ppc > 0 ? ( $ppc * $range_total ) : 0; $last_completed_raw = isset( $last_map[ $goal_id ] ) ? (string) $last_map[ $goal_id ] : ''; $last_info = $format_last_completed( $last_completed_raw ); $bounds = $this->current_loop_bounds_for_goal( $row, $now_tz, $unit, $span ); $start_day_ts = isset( $bounds['start'] ) ? (int) $bounds['start']->setTime( 0, 0, 0 )->getTimestamp() : (int) $today_tz->getTimestamp(); $end_day_ts = isset( $bounds['end'] ) ? (int) $bounds['end']->setTime( 0, 0, 0 )->getTimestamp() : (int) $today_tz->add( new DateInterval( 'P7D' ) )->getTimestamp(); $today_ts = (int) $today_tz->getTimestamp(); $day_seconds = defined( 'DAY_IN_SECONDS' ) ? (int) DAY_IN_SECONDS : 86400; $span_s = $end_day_ts - $start_day_ts; $total_days = (int) floor( (float) $span_s / (float) $day_seconds ); if ( $total_days < 1 ) { $total_days = 1; } $day_index = (int) floor( (float) ( $today_ts - $start_day_ts ) / (float) $day_seconds ) + 1; if ( $day_index < 1 ) { $day_index = 1; } if ( $day_index > $total_days ) { $day_index = $total_days; } $days_left = $total_days - $day_index + 1; if ( $days_left < 1 ) { $days_left = 1; } $loop_start_ts = isset( $bounds['start'] ) ? (int) $bounds['start']->getTimestamp() : (int) $start_day_ts; $loop_end_ts = isset( $bounds['end'] ) ? (int) $bounds['end']->getTimestamp() : (int) $end_day_ts; $now_ts = (int) $now_tz->getTimestamp(); $total_seconds = $loop_end_ts - $loop_start_ts; if ( $total_seconds < 1 ) { $total_seconds = 1; } $seconds_left = $loop_end_ts - $now_ts; if ( $seconds_left < 0 ) { $seconds_left = 0; } $effective_target_dr = $target; $created_at_raw_dr = isset( $row['created_at'] ) ? trim( (string) $row['created_at'] ) : ''; $updated_at_raw_dr = isset( $row['updated_at'] ) ? trim( (string) $row['updated_at'] ) : ''; $created_at_ts_dr = 0; $updated_at_ts_dr = 0; if ( '' !== $created_at_raw_dr && '0000-00-00 00:00:00' !== $created_at_raw_dr ) { $tmp = strtotime( $created_at_raw_dr . ' UTC' ); if ( false !== $tmp ) { $created_at_ts_dr = (int) $tmp; } } if ( '' !== $updated_at_raw_dr && '0000-00-00 00:00:00' !== $updated_at_raw_dr ) { $tmp = strtotime( $updated_at_raw_dr . ' UTC' ); if ( false !== $tmp ) { $updated_at_ts_dr = (int) $tmp; } } $prorate_anchor_dr = TCT_Utils::compute_prorate_anchor_ts( $created_at_ts_dr, $updated_at_ts_dr, $loop_start_ts ); if ( $prorate_anchor_dr > 0 ) { $effective_target_dr = TCT_Utils::compute_prorated_target( $target, $prorate_anchor_dr, $loop_start_ts, $loop_end_ts ); } $status = $compute_status( $goal_count, $effective_target_dr, $day_index, $total_days, $days_left, $seconds_left, $total_seconds, $unit, $span );
		// If prorating has reduced this interval's effective target to 0 (e.g. goal edited mid-interval),
		// but the raw target has still been achieved, treat as completed for display purposes.
		if ( (int) $effective_target_dr <= 0 && (int) $target > 0 && (int) $goal_count >= (int) $target ) {
			$status = array(
				'key'      => 'completed',
				'label'    => 'Completed',
				'rank'     => 0,
				'expected' => (int) $target,
				'need'     => 0,
			);
		}
		$goal_type_for_status = isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive'; if ( TCT_Utils::is_negative_goal_type( $goal_type_for_status ) ) { $status = array( 'key' => 'on-track', 'label' => 'On track', 'rank' => 1, 'expected' => 0, 'need' => 0, ); } $payload_intervals = $intervals; if ( TCT_Utils::is_positive_no_interval_goal_type( isset( $row['goal_type'] ) ? (string) $row['goal_type'] : '' ) ) { $payload_intervals = array(); } $payload = array( 'goal_id' => $goal_id, 'tracking_mode' => $tracking_mode, 'label_name' => $label, 'goal_name' => $goal_name, 'aliases' => $this->aliases_from_goal_row( $row ), 'link_url' => $this->link_url_from_goal_row( $row ), 'goal_notes' => $this->goal_notes_from_goal_row( $row ), 'due_schedule_json' => ( isset( $row['due_schedule_json'] ) && is_string( $row['due_schedule_json'] ) ) ? (string) $row['due_schedule_json'] : '', 'availability_cycle_json' => ( isset( $row['availability_cycle_json'] ) && is_string( $row['availability_cycle_json'] ) ) ? (string) $row['availability_cycle_json'] : '', 'interval_anchor_json' => ( isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ) ? (string) $row['interval_anchor_json'] : '', 'plant_name' => isset( $row['plant_name'] ) && is_string( $row['plant_name'] ) ? (string) $row['plant_name'] : '', 'goal_type' => isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive', 'threshold' => isset( $row['threshold'] ) && is_numeric( $row['threshold'] ) ? (int) $row['threshold'] : null, 'timer_duration_seconds' => isset( $row['timer_duration_seconds'] ) ? (int) $row['timer_duration_seconds'] : 0, 'alarm_sound' => isset( $row['alarm_sound'] ) && is_string( $row['alarm_sound'] ) ? (string) $row['alarm_sound'] : '', 'alarm_duration' => isset( $row['alarm_duration'] ) ? (int) $row['alarm_duration'] : 0, 'alarm_vibration' => isset( $row['alarm_vibration'] ) ? (int) $row['alarm_vibration'] : 0, 'visible_after_time' => isset( $row['visible_after_time'] ) && is_string( $row['visible_after_time'] ) ? (string) $row['visible_after_time'] : '', 'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0, 'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '18:00', 'wake_time_enabled' => isset( $row['wake_time_enabled'] ) ? (int) $row['wake_time_enabled'] : 0, 'wake_time_target' => isset( $row['wake_time_target'] ) && is_string( $row['wake_time_target'] ) ? (string) $row['wake_time_target'] : '', 'bed_time_enabled' => isset( $row['bed_time_enabled'] ) ? (int) $row['bed_time_enabled'] : 0, 'bed_time_target' => isset( $row['bed_time_target'] ) && is_string( $row['bed_time_target'] ) ? (string) $row['bed_time_target'] : '', 'fail_button_enabled' => isset( $row['fail_button_enabled'] ) ? (int) $row['fail_button_enabled'] : 0, 'domain_id' => $domain_id, 'role_id' => $role_id, 'points_per_completion' => isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0, 'importance' => isset( $row['importance'] ) ? (int) $row['importance'] : 0, 'effort' => isset( $row['effort'] ) ? (int) $row['effort'] : 0, 'points_enabled_at' => isset( $row['points_enabled_at'] ) ? (string) $row['points_enabled_at'] : '', 'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0, 'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '', 'wake_time_enabled' => isset( $row['wake_time_enabled'] ) ? (int) $row['wake_time_enabled'] : 0, 'wake_time_target' => isset( $row['wake_time_target'] ) && is_string( $row['wake_time_target'] ) ? (string) $row['wake_time_target'] : '', 'bed_time_enabled' => isset( $row['bed_time_enabled'] ) ? (int) $row['bed_time_enabled'] : 0, 'bed_time_target' => isset( $row['bed_time_target'] ) && is_string( $row['bed_time_target'] ) ? (string) $row['bed_time_target'] : '', 'fail_button_enabled' => isset( $row['fail_button_enabled'] ) ? (int) $row['fail_button_enabled'] : 0, 'is_favorite' => isset( $row['is_favorite'] ) ? (int) $row['is_favorite'] : 0, 'allowed_fails_target' => isset( $row['allowed_fails_target'] ) ? (int) $row['allowed_fails_target'] : 0, 'allowed_fails_unit' => isset( $row['allowed_fails_unit'] ) ? (string) $row['allowed_fails_unit'] : 'week', 'allowed_fails_span' => isset( $row['allowed_fails_span'] ) ? (int) $row['allowed_fails_span'] : 1, 'due_schedule_json' => isset( $row['due_schedule_json'] ) && is_string( $row['due_schedule_json'] ) ? (string) $row['due_schedule_json'] : '', 'availability_cycle_json' => isset( $row['availability_cycle_json'] ) && is_string( $row['availability_cycle_json'] ) ? (string) $row['availability_cycle_json'] : '', 'interval_anchor_json' => isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ? (string) $row['interval_anchor_json'] : '', 'composite_config_json' => isset( $row['composite_config_json'] ) && is_string( $row['composite_config_json'] ) ? (string) $row['composite_config_json'] : '', 'intervals' => $payload_intervals, ); $payload_json = wp_json_encode( $payload ); $availability_tile_ctx = $this->get_goal_availability_tile_context( $row, $now_tz ); $availability_enabled_tile = ! empty( $availability_tile_ctx['enabled'] ); $availability_is_paused_tile = $availability_enabled_tile && ! empty( $availability_tile_ctx['is_paused'] ); $availability_is_active_tile = $availability_enabled_tile && ! $availability_is_paused_tile; if ( $availability_is_paused_tile ) { $status['rank'] = -1; } if ( ! isset( $goals_by_domain_role[ $domain_id ] ) ) { $goals_by_domain_role[ $domain_id ] = array(); } if ( ! isset( $goals_by_domain_role[ $domain_id ][ $role_id ] ) ) { $goals_by_domain_role[ $domain_id ][ $role_id ] = array(); } $unit_singular = (string) $unit; $unit_plural = (string) $unit . 's'; switch ( $unit_singular ) { case 'day': $unit_plural = 'days'; break; case 'week': $unit_plural = 'weeks'; break; case 'month': $unit_plural = 'months'; break; case 'quarter': $unit_plural = 'quarters'; break; case 'year': $unit_plural = 'years'; break; case 'hour': $unit_plural = 'hours'; break; case 'semiannual': $unit_singular = 'half-year'; $unit_plural = 'half-years'; break; } $unit_span_label = $unit_singular; if ( (int) $span > 1 ) { $unit_span_label = (int) $span . ' ' . $unit_plural; } $goals_by_domain_role[ $domain_id ][ $role_id ][] = array( 'goal_id' => $goal_id, 'goal_type' => isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive', 'threshold' => isset( $row['threshold'] ) && is_numeric( $row['threshold'] ) ? (int) $row['threshold'] : null, 'timer_duration_seconds' => isset( $row['timer_duration_seconds'] ) ? (int) $row['timer_duration_seconds'] : 0, 'alarm_sound' => isset( $row['alarm_sound'] ) && is_string( $row['alarm_sound'] ) ? (string) $row['alarm_sound'] : '', 'alarm_duration' => isset( $row['alarm_duration'] ) ? (int) $row['alarm_duration'] : 0, 'alarm_vibration' => isset( $row['alarm_vibration'] ) ? (int) $row['alarm_vibration'] : 0, 'visible_after_time' => isset( $row['visible_after_time'] ) && is_string( $row['visible_after_time'] ) ? (string) $row['visible_after_time'] : '', 'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0, 'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '18:00', 'wake_time_enabled' => isset( $row['wake_time_enabled'] ) ? (int) $row['wake_time_enabled'] : 0, 'wake_time_target' => isset( $row['wake_time_target'] ) && is_string( $row['wake_time_target'] ) ? (string) $row['wake_time_target'] : '', 'bed_time_enabled' => isset( $row['bed_time_enabled'] ) ? (int) $row['bed_time_enabled'] : 0, 'bed_time_target' => isset( $row['bed_time_target'] ) && is_string( $row['bed_time_target'] ) ? (string) $row['bed_time_target'] : '', 'fail_button_enabled' => isset( $row['fail_button_enabled'] ) ? (int) $row['fail_button_enabled'] : 0, 'is_favorite' => isset( $row['is_favorite'] ) ? (int) $row['is_favorite'] : 0, 'tracking_mode' => $tracking_mode, 'label_name' => $label, 'goal_name' => $goal_name, 'aliases' => $this->aliases_from_goal_row( $row ), 'link_url' => $this->link_url_from_goal_row( $row ), 'goal_notes' => $this->goal_notes_from_goal_row( $row ), 'due_schedule_json' => isset( $row['due_schedule_json'] ) && is_string( $row['due_schedule_json'] ) ? (string) $row['due_schedule_json'] : '', 'availability_cycle_json' => isset( $row['availability_cycle_json'] ) && is_string( $row['availability_cycle_json'] ) ? (string) $row['availability_cycle_json'] : '', 'interval_anchor_json' => isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ? (string) $row['interval_anchor_json'] : '', 'composite_config_json' => isset( $row['composite_config_json'] ) && is_string( $row['composite_config_json'] ) ? (string) $row['composite_config_json'] : '', 'availability_enabled' => $availability_enabled_tile ? 1 : 0, 'availability_phase' => isset( $availability_tile_ctx['phase'] ) ? (string) $availability_tile_ctx['phase'] : '', 'availability_is_paused' => $availability_is_paused_tile ? 1 : 0, 'availability_is_active' => $availability_is_active_tile ? 1 : 0, 'availability_state_label' => isset( $availability_tile_ctx['state_label'] ) ? (string) $availability_tile_ctx['state_label'] : '', 'availability_state_meta' => isset( $availability_tile_ctx['state_meta'] ) ? (string) $availability_tile_ctx['state_meta'] : '', 'plant_name' => isset( $row['plant_name'] ) && is_string( $row['plant_name'] ) ? trim( (string) $row['plant_name'] ) : '', 'domain_id' => $domain_id, 'role_id' => $role_id, 'edit_payload_json' => (string) $payload_json, 'range_total' => $range_total, 'avg_per_day' => $avg_per_day, 'points_per_completion' => $ppc, 'points_in_range' => $points_in_range, 'target' => $target, 'unit' => $unit, 'period_span' => (int) $span, 'unit_span_label' => (string) $unit_span_label, 'mode' => $mode, 'goal_count' => $goal_count, 'progress_pct'=> $progress_pct, 'status_key' => (string) $status['key'], 'status_label'=> (string) $status['label'], 'status_rank' => (int) $status['rank'], 'expected_by_now' => (int) $status['expected'], 'need' => (int) $status['need'], 'days_left' => (int) $days_left, 'vitality_data' => $vitality_data, 'last_completed_text' => (string) $last_info['text'], 'last_completed_ts' => (int) $last_info['ts'], '__tct_surface' => 'dashboard', ); } foreach ( $goals_by_domain_role as $did => $roles_bucket ) { if ( ! is_array( $roles_bucket ) ) { continue; } foreach ( $roles_bucket as $rid => $goal_list ) { if ( ! is_array( $goal_list ) ) { continue; } $max_ppc = 0; foreach ( $goal_list as $g_tmp ) { $ppc_tmp = isset( $g_tmp['points_per_completion'] ) ? (int) $g_tmp['points_per_completion'] : 0; if ( $ppc_tmp > $max_ppc ) { $max_ppc = $ppc_tmp; } } $now_ts_sort = isset( $now_tz ) && ( $now_tz instanceof DateTimeImmutable ) ? (int) $now_tz->getTimestamp() : (int) time(); foreach ( $goal_list as $ix => $g_tmp ) { $status_key = isset( $g_tmp['status_key'] ) ? (string) $g_tmp['status_key'] : ''; $g_tmp_is_paused = ! empty( $g_tmp['availability_is_paused'] ); if ( $g_tmp_is_paused ) { $goal_list[ $ix ]['status_rank'] = -1; $goal_list[ $ix ]['urgency_score'] = -1.0; continue; } $vitality = 100; if ( isset( $g_tmp['vitality_data'] ) && is_array( $g_tmp['vitality_data'] ) && isset( $g_tmp['vitality_data']['vitality'] ) ) { $vitality = (int) $g_tmp['vitality_data']['vitality']; } if ( $vitality < 0 ) { $vitality = 0; } if ( $vitality > 100 ) { $vitality = 100; } $vitality_urg = 1.0 - ( (float) $vitality / 100.0 ); if ( $vitality_urg < 0.0 ) { $vitality_urg = 0.0; } if ( $vitality_urg > 1.0 ) { $vitality_urg = 1.0; } $ppc_val = isset( $g_tmp['points_per_completion'] ) ? (int) $g_tmp['points_per_completion'] : 0; if ( $ppc_val < 0 ) { $ppc_val = 0; } $ppc_norm = 0.0; if ( $max_ppc > 0 ) { $ppc_norm = (float) $ppc_val / (float) $max_ppc; } if ( $ppc_norm < 0.0 ) { $ppc_norm = 0.0; } if ( $ppc_norm > 1.0 ) { $ppc_norm = 1.0; } $recency_norm = 0.0; if ( isset( $g_tmp['vitality_data'] ) && is_array( $g_tmp['vitality_data'] ) && isset( $g_tmp['vitality_data']['components'] ) && is_array( $g_tmp['vitality_data']['components'] ) ) { $c = $g_tmp['vitality_data']['components']; $time_since_last_s = isset( $c['time_since_last_s'] ) ? (float) $c['time_since_last_s'] : 0.0; $spacing_days = isset( $c['spacing_days'] ) ? (float) $c['spacing_days'] : 0.0; if ( $time_since_last_s < 0.0 ) { $time_since_last_s = 0.0; } if ( $spacing_days > 0.0001 ) { $spacing_s = $spacing_days * 86400.0; if ( $spacing_s < 1.0 ) { $spacing_s = 1.0; } $t = $time_since_last_s / $spacing_s; $recency_norm = $t / 4.0; } } if ( $recency_norm <= 0.0 ) { $last_ts = isset( $g_tmp['last_completed_ts'] ) ? (int) $g_tmp['last_completed_ts'] : 0; if ( $last_ts > 0 && $now_ts_sort > $last_ts ) { $days_since = ( (float) ( $now_ts_sort - $last_ts ) ) / 86400.0; $recency_norm = $days_since / 14.0; } } if ( $recency_norm < 0.0 ) { $recency_norm = 0.0; } if ( $recency_norm > 1.0 ) { $recency_norm = 1.0; } $w_recency = 0.10; if ( 'critical' === $status_key ) { $w_vitality = 0.45; $w_ppc = 0.45; } else { $w_vitality = 0.585; $w_ppc = 0.315; } $urgency = ( $w_vitality * $vitality_urg ) + ( $w_ppc * $ppc_norm ) + ( $w_recency * $recency_norm ); if ( 'completed' === $status_key ) { $urgency = 0.0; } $goal_list[ $ix ]['urgency_score'] = (float) $urgency; } usort( $goal_list, function ( $a, $b ) { $ar = isset( $a['status_rank'] ) ? (int) $a['status_rank'] : 0; $br = isset( $b['status_rank'] ) ? (int) $b['status_rank'] : 0; if ( $ar !== $br ) { return $br <=> $ar; } $a_gt = isset( $a['goal_type'] ) ? strtolower( trim( (string) $a['goal_type'] ) ) : 'positive'; $b_gt = isset( $b['goal_type'] ) ? strtolower( trim( (string) $b['goal_type'] ) ) : 'positive'; $a_neg = ( 'never' === $a_gt || 'harm_reduction' === $a_gt ) ? 1 : 0; $b_neg = ( 'never' === $b_gt || 'harm_reduction' === $b_gt ) ? 1 : 0; if ( $a_neg !== $b_neg ) { return $a_neg <=> $b_neg; } $as = isset( $a['urgency_score'] ) ? (float) $a['urgency_score'] : 0.0; $bs = isset( $b['urgency_score'] ) ? (float) $b['urgency_score'] : 0.0; if ( $as !== $bs ) { return $bs <=> $as; } $ats = isset( $a['last_completed_ts'] ) ? (int) $a['last_completed_ts'] : 0; $bts = isset( $b['last_completed_ts'] ) ? (int) $b['last_completed_ts'] : 0; if ( $ats !== $bts ) { $a_status_key = isset( $a['status_key'] ) ? (string) $a['status_key'] : ''; $b_status_key = isset( $b['status_key'] ) ? (string) $b['status_key'] : ''; if ( 'completed' === $a_status_key && 'completed' === $b_status_key ) { if ( 0 === $ats ) { return 1; } if ( 0 === $bts ) { return -1; } return $bts <=> $ats; } if ( 0 === $ats ) { return -1; } if ( 0 === $bts ) { return 1; } return $ats <=> $bts; } $an = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : ''; $bn = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : ''; return strcasecmp( $an, $bn ); } ); $goals_by_domain_role[ $did ][ $rid ] = $goal_list; } } $has_unassigned_domain = isset( $goals_by_domain_role[0] ) && ! empty( $goals_by_domain_role[0] ); $domain_order = array(); foreach ( $domains as $d ) { $domain_order[] = (int) $d['id']; } if ( $has_unassigned_domain ) { $domain_order[] = 0; } if ( empty( $domain_order ) ) { $domain_order[] = 0; } $html = '<div class="tct-card">'; $max_role_columns = 0; foreach ( $domain_order as $did ) { $did = (int) $did; $cnt = 0; if ( isset( $roles_by_domain[ $did ] ) && is_array( $roles_by_domain[ $did ] ) ) { $cnt += count( $roles_by_domain[ $did ] ); } $has_unassigned_role = isset( $goals_by_domain_role[ $did ][0] ) && ! empty( $goals_by_domain_role[ $did ][0] ); if ( $has_unassigned_role ) { $cnt += 1; } if ( 0 === $cnt && isset( $goals_by_domain_role[ $did ] ) && ! empty( $goals_by_domain_role[ $did ] ) ) { $cnt = 1; } if ( $cnt > $max_role_columns ) { $max_role_columns = $cnt; } } if ( $max_role_columns < 1 ) { $max_role_columns = 1; } $first_domain_id = ( ! empty( $domain_order ) ) ? (int) $domain_order[0] : 0; $default_domain_tab = 'domain-' . (int) $first_domain_id; $domain_tabs_uid = 'tct-domain-tabs-' . (int) $user_id . '-' . (int) ( function_exists( 'wp_rand' ) ? wp_rand( 1000, 999999999 ) : mt_rand( 1000, 999999999 ) ); $domain_storage_key = 'tct_active_domain_tab_' . (int) $user_id; $html .= '<div class="tct-tabs tct-domain-tabs" data-tct-tabs="1" data-tct-storage-key="' . esc_attr( $domain_storage_key ) . '" data-tct-default-tab="' . esc_attr( $default_domain_tab ) . '">'; $html .= '<div class="tct-tab-nav" role="tablist" aria-label="Domains">'; $urgent_color_css_nav = '#d63638'; $urgent_rgb_css_nav = '214,54,56'; $urgent_tab_key_nav = 'urgent'; $urgent_tab_id_nav = $domain_tabs_uid . '-tab-urgent'; $urgent_panel_id_nav = $domain_tabs_uid . '-panel-urgent'; $urgent_is_active_nav = ( $urgent_tab_key_nav === $default_domain_tab ); $urgent_tab_classes_nav = 'tct-tab tct-tab-urgent' . ( $urgent_is_active_nav ? ' tct-tab-active' : '' ); $html .= '<button type="button" class="' . esc_attr( $urgent_tab_classes_nav ) . '" data-tct-tab="' . esc_attr( $urgent_tab_key_nav ) . '" role="tab" id="' . esc_attr( $urgent_tab_id_nav ) . '" aria-controls="' . esc_attr( $urgent_panel_id_nav ) . '" aria-selected="' . ( $urgent_is_active_nav ? 'true' : 'false' ) . '" tabindex="' . ( $urgent_is_active_nav ? '0' : '-1' ) . '" style="--tct-domain-color:' . esc_attr( $urgent_color_css_nav ) . '; --tct-domain-color-rgb:' . esc_attr( $urgent_rgb_css_nav ) . ';">Urgent</button>'; $complete_color_css_nav = '#22c55e'; $complete_rgb_css_nav = '34,197,94'; $complete_tab_key_nav = 'complete'; $complete_tab_id_nav = $domain_tabs_uid . '-tab-complete'; $complete_panel_id_nav = $domain_tabs_uid . '-panel-complete'; $complete_is_active_nav = ( $complete_tab_key_nav === $default_domain_tab ); $complete_tab_classes_nav = 'tct-tab tct-tab-complete' . ( $complete_is_active_nav ? ' tct-tab-active' : '' ); $html .= '<button type="button" class="' . esc_attr( $complete_tab_classes_nav ) . '" data-tct-tab="' . esc_attr( $complete_tab_key_nav ) . '" role="tab" id="' . esc_attr( $complete_tab_id_nav ) . '" aria-controls="' . esc_attr( $complete_panel_id_nav ) . '" aria-selected="' . ( $complete_is_active_nav ? 'true' : 'false' ) . '" tabindex="' . ( $complete_is_active_nav ? '0' : '-1' ) . '" style="--tct-domain-color:' . esc_attr( $complete_color_css_nav ) . '; --tct-domain-color-rgb:' . esc_attr( $complete_rgb_css_nav ) . ';">Completed</button>'; $paused_color_css_nav = '#64748b'; $paused_rgb_css_nav = '100,116,139'; $paused_tab_key_nav = 'paused'; $paused_tab_id_nav = $domain_tabs_uid . '-tab-paused'; $paused_panel_id_nav = $domain_tabs_uid . '-panel-paused'; $paused_is_active_nav = ( $paused_tab_key_nav === $default_domain_tab ); $paused_tab_classes_nav = 'tct-tab tct-tab-paused' . ( $paused_is_active_nav ? ' tct-tab-active' : '' ); $html .= '<button type="button" class="' . esc_attr( $paused_tab_classes_nav ) . '" data-tct-tab="' . esc_attr( $paused_tab_key_nav ) . '" role="tab" id="' . esc_attr( $paused_tab_id_nav ) . '" aria-controls="' . esc_attr( $paused_panel_id_nav ) . '" aria-selected="' . ( $paused_is_active_nav ? 'true' : 'false' ) . '" tabindex="' . ( $paused_is_active_nav ? '0' : '-1' ) . '" style="--tct-domain-color:' . esc_attr( $paused_color_css_nav ) . '; --tct-domain-color-rgb:' . esc_attr( $paused_rgb_css_nav ) . ';">Paused</button>'; $html .= '<span class="tct-tab-separator" aria-hidden="true"></span>'; foreach ( $domain_order as $domain_id_nav ) { $domain_id_nav = (int) $domain_id_nav; $domain_name_nav = ''; if ( 0 === $domain_id_nav ) { $domain_name_nav = $has_unassigned_domain ? 'Unassigned' : 'Goals'; } else { $domain_name_nav = isset( $domain_map[ $domain_id_nav ] ) ? (string) $domain_map[ $domain_id_nav ] : 'Domain'; } $domain_color_nav = $domain_id_nav > 0 && isset( $domain_color_map[ $domain_id_nav ] ) ? (string) $domain_color_map[ $domain_id_nav ] : ''; $domain_color_css_nav = '' !== $domain_color_nav ? $domain_color_nav : '#e2e8f0'; $domain_rgb_nav = $this->hex_to_rgb_triplet( $domain_color_css_nav ); $domain_rgb_css_nav = is_array( $domain_rgb_nav ) && 3 === count( $domain_rgb_nav ) ? ( (int) $domain_rgb_nav[0] . ',' . (int) $domain_rgb_nav[1] . ',' . (int) $domain_rgb_nav[2] ) : '226,232,240'; $tab_key_nav = 'domain-' . (int) $domain_id_nav; $is_active_domain_tab = ( $tab_key_nav === $default_domain_tab ); $tab_id_nav = $domain_tabs_uid . '-tab-' . (int) $domain_id_nav; $panel_id_nav = $domain_tabs_uid . '-panel-' . (int) $domain_id_nav; $tab_classes = 'tct-tab' . ( $is_active_domain_tab ? ' tct-tab-active' : '' ); $html .= '<button type="button" class="' . esc_attr( $tab_classes ) . '" data-tct-tab="' . esc_attr( $tab_key_nav ) . '" role="tab" id="' . esc_attr( $tab_id_nav ) . '" aria-controls="' . esc_attr( $panel_id_nav ) . '" aria-selected="' . ( $is_active_domain_tab ? 'true' : 'false' ) . '" tabindex="' . ( $is_active_domain_tab ? '0' : '-1' ) . '" style="--tct-domain-color:' . esc_attr( $domain_color_css_nav ) . '; --tct-domain-color-rgb:' . esc_attr( $domain_rgb_css_nav ) . ';">' . esc_html( $domain_name_nav ) . '</button>'; } $html .= '</div>'; $html .= '<div class="tct-tab-panels">'; $urgent_tab_key = 'urgent'; $urgent_tab_id = $domain_tabs_uid . '-tab-urgent'; $urgent_panel_id = $domain_tabs_uid . '-panel-urgent'; $urgent_is_active_panel = ( $urgent_tab_key === $default_domain_tab ); $urgent_panel_classes = 'tct-tab-panel' . ( $urgent_is_active_panel ? ' tct-tab-panel-active' : '' ); $urgent_hidden = $urgent_is_active_panel ? '' : ' hidden="hidden"'; $html .= '<div class="' . esc_attr( $urgent_panel_classes ) . '" data-tct-panel="' . esc_attr( $urgent_tab_key ) . '" role="tabpanel" id="' . esc_attr( $urgent_panel_id ) . '" aria-labelledby="' . esc_attr( $urgent_tab_id ) . '"' . $urgent_hidden . '>'; $urgent_all_goals = array(); $urgent_max_ppc_global = 0; foreach ( $goals_by_domain_role as $u_did => $u_roles ) { if ( ! is_array( $u_roles ) ) { continue; } foreach ( $u_roles as $u_rid => $u_goal_list ) { if ( ! is_array( $u_goal_list ) ) { continue; } foreach ( $u_goal_list as $u_g ) { if ( ! is_array( $u_g ) ) { continue; } $u_status = isset( $u_g['status_key'] ) ? (string) $u_g['status_key'] : ''; if ( 'completed' === $u_status ) { continue; } if ( ! empty( $u_g['availability_is_paused'] ) ) { continue; } $u_ppc = isset( $u_g['points_per_completion'] ) ? (int) $u_g['points_per_completion'] : 0; if ( $u_ppc < 0 ) { $u_ppc = 0; } if ( $u_ppc > $urgent_max_ppc_global ) { $urgent_max_ppc_global = $u_ppc; } $urgent_all_goals[] = $u_g; } } } $urgent_now_ts_sort = (int) $now_tz->getTimestamp(); $urgent_bucket_due_today = array(); $urgent_bucket_critical = array(); $urgent_bucket_risk = array(); $urgent_bucket_vit_low = array(); $urgent_bucket_vit_mid = array(); $urgent_now_hhmm = $now_tz->format( 'H:i' ); foreach ( $urgent_all_goals as $u_goal ) { $u_goal_local = $u_goal; $u_status_key = isset( $u_goal_local['status_key'] ) ? (string) $u_goal_local['status_key'] : 'on-track'; $u_goal_type = isset( $u_goal_local['goal_type'] ) ? (string) $u_goal_local['goal_type'] : 'positive'; $u_is_negative = TCT_Utils::is_negative_goal_type( $u_goal_type ); $u_unit = isset( $u_goal_local['unit'] ) ? (string) $u_goal_local['unit'] : ''; $u_span = isset( $u_goal_local['period_span'] ) ? (int) $u_goal_local['period_span'] : 1; if ( $u_span < 1 ) { $u_span = 1; } $u_is_daily = ( 'day' === $u_unit && 1 === $u_span ); if ( $u_is_daily && ! $u_is_negative ) { $u_target = isset( $u_goal_local['target'] ) ? (int) $u_goal_local['target'] : 0; $u_achieved = isset( $u_goal_local['goal_count'] ) ? (int) $u_goal_local['goal_count'] : 0; if ( $u_achieved < 0 ) { $u_achieved = 0; } if ( $u_target > 0 && $u_achieved < $u_target ) { $u_vat = isset( $u_goal_local['visible_after_time'] ) && is_string( $u_goal_local['visible_after_time'] ) ? trim( $u_goal_local['visible_after_time'] ) : ''; if ( '' === $u_vat || $urgent_now_hhmm >= $u_vat ) { $u_goal_local['urgency_score'] = 0.0; $urgent_bucket_due_today[] = $u_goal_local; continue; } } } if ( $u_is_daily ) { continue; } $u_vd = ( isset( $u_goal_local['vitality_data'] ) && is_array( $u_goal_local['vitality_data'] ) ) ? $u_goal_local['vitality_data'] : array(); $u_vitality = isset( $u_vd['vitality'] ) ? (int) $u_vd['vitality'] : 100; if ( $u_vitality < 0 ) { $u_vitality = 0; } elseif ( $u_vitality > 100 ) { $u_vitality = 100; } $u_ppc = isset( $u_goal_local['points_per_completion'] ) ? (int) $u_goal_local['points_per_completion'] : 0; if ( $u_ppc < 0 ) { $u_ppc = 0; } $u_ppc_norm = 0.0; if ( $urgent_max_ppc_global > 0 ) { $u_ppc_norm = (float) $u_ppc / (float) $urgent_max_ppc_global; if ( $u_ppc_norm < 0.0 ) { $u_ppc_norm = 0.0; } elseif ( $u_ppc_norm > 1.0 ) { $u_ppc_norm = 1.0; } } $u_vitality_urg = 1.0 - ( (float) $u_vitality / 100.0 ); if ( $u_vitality_urg < 0.0 ) { $u_vitality_urg = 0.0; } elseif ( $u_vitality_urg > 1.0 ) { $u_vitality_urg = 1.0; } $u_last_ts = isset( $u_goal_local['last_completed_ts'] ) ? (int) $u_goal_local['last_completed_ts'] : 0; $u_recency_norm = 0.0; $u_window_secs = isset( $u_vd['time_window_seconds'] ) ? (int) $u_vd['time_window_seconds'] : 0; if ( $u_window_secs > 0 && $u_last_ts > 0 ) { $u_elapsed = $urgent_now_ts_sort - $u_last_ts; if ( $u_elapsed < 0 ) { $u_elapsed = 0; } $u_recency_norm = (float) $u_elapsed / (float) $u_window_secs; if ( $u_recency_norm < 0.0 ) { $u_recency_norm = 0.0; } elseif ( $u_recency_norm > 1.0 ) { $u_recency_norm = 1.0; } } elseif ( $u_last_ts > 0 ) { $u_elapsed = $urgent_now_ts_sort - $u_last_ts; if ( $u_elapsed < 0 ) { $u_elapsed = 0; } $u_recency_norm = min( 1.0, (float) $u_elapsed / (float) ( 14 * 86400 ) ); } else { $u_recency_norm = 1.0; } $u_w_recency = 0.10; if ( 'critical' === $u_status_key ) { $u_w_vitality = 0.45; $u_w_ppc = 0.45; } else { $u_w_vitality = 0.585; $u_w_ppc = 0.315; } $u_goal_local['urgency_score'] = ( $u_w_vitality * $u_vitality_urg ) + ( $u_w_ppc * $u_ppc_norm ) + ( $u_w_recency * $u_recency_norm ); if ( ! $u_is_negative && 'critical' === $u_status_key ) { $urgent_bucket_critical[] = $u_goal_local; } elseif ( ! $u_is_negative && 'risk' === $u_status_key ) { $urgent_bucket_risk[] = $u_goal_local; } else { if ( $u_vitality <= 30 ) { $urgent_bucket_vit_low[] = $u_goal_local; } elseif ( $u_vitality >= 31 && $u_vitality <= 60 ) { $urgent_bucket_vit_mid[] = $u_goal_local; } } } $urgent_daily_order_map = $this->tct_get_goal_order_map( $user_id, 'daily' ); usort( $urgent_bucket_due_today, function ( $a, $b ) use ( $urgent_daily_order_map ) { $aid = isset( $a['goal_id'] ) ? (int) $a['goal_id'] : ( isset( $a['id'] ) ? (int) $a['id'] : 0 ); $bid = isset( $b['goal_id'] ) ? (int) $b['goal_id'] : ( isset( $b['id'] ) ? (int) $b['id'] : 0 ); $ap = isset( $urgent_daily_order_map[ $aid ] ) ? (int) $urgent_daily_order_map[ $aid ] : PHP_INT_MAX; $bp = isset( $urgent_daily_order_map[ $bid ] ) ? (int) $urgent_daily_order_map[ $bid ] : PHP_INT_MAX; if ( $ap !== $bp ) { return $ap <=> $bp; } $a_ppc = isset( $a['points_per_completion'] ) ? (int) $a['points_per_completion'] : 0; $b_ppc = isset( $b['points_per_completion'] ) ? (int) $b['points_per_completion'] : 0; if ( $a_ppc !== $b_ppc ) { return $b_ppc <=> $a_ppc; } $a_vd = ( isset( $a['vitality_data'] ) && is_array( $a['vitality_data'] ) ) ? $a['vitality_data'] : array(); $b_vd = ( isset( $b['vitality_data'] ) && is_array( $b['vitality_data'] ) ) ? $b['vitality_data'] : array(); $a_vit = isset( $a_vd['vitality'] ) ? (int) $a_vd['vitality'] : 100; $b_vit = isset( $b_vd['vitality'] ) ? (int) $b_vd['vitality'] : 100; if ( $a_vit !== $b_vit ) { return $a_vit <=> $b_vit; } $a_name = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : ''; $b_name = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : ''; return strcasecmp( $a_name, $b_name ); } ); $urgent_cmp = function ( $a, $b ) { $ar = isset( $a['status_rank'] ) ? (int) $a['status_rank'] : 0; $br = isset( $b['status_rank'] ) ? (int) $b['status_rank'] : 0; if ( $ar !== $br ) { return $br <=> $ar; } $a_gt = isset( $a['goal_type'] ) ? strtolower( trim( (string) $a['goal_type'] ) ) : 'positive'; $b_gt = isset( $b['goal_type'] ) ? strtolower( trim( (string) $b['goal_type'] ) ) : 'positive'; $a_neg = ( 'never' === $a_gt || 'harm_reduction' === $a_gt ) ? 1 : 0; $b_neg = ( 'never' === $b_gt || 'harm_reduction' === $b_gt ) ? 1 : 0; if ( $a_neg !== $b_neg ) { return $a_neg <=> $b_neg; } $au = isset( $a['urgency_score'] ) ? (float) $a['urgency_score'] : 0.0; $bu = isset( $b['urgency_score'] ) ? (float) $b['urgency_score'] : 0.0; if ( abs( $au - $bu ) > 0.000001 ) { return ( $bu <=> $au ); } $ats = isset( $a['last_completed_ts'] ) ? (int) $a['last_completed_ts'] : 0; $bts = isset( $b['last_completed_ts'] ) ? (int) $b['last_completed_ts'] : 0; $a_status = isset( $a['status_key'] ) ? (string) $a['status_key'] : ''; if ( 'completed' === $a_status ) { if ( $ats === $bts ) { $an = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : ''; $bn = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : ''; return strcasecmp( $an, $bn ); } if ( 0 === $ats ) { return 1; } if ( 0 === $bts ) { return -1; } return $bts <=> $ats; } if ( $ats === $bts ) { $an = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : ''; $bn = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : ''; return strcasecmp( $an, $bn ); } if ( 0 === $ats ) { return -1; } if ( 0 === $bts ) { return 1; } return $ats <=> $bts; }; usort( $urgent_bucket_critical, $urgent_cmp ); usort( $urgent_bucket_risk, $urgent_cmp ); usort( $urgent_bucket_vit_low, $urgent_cmp ); usort( $urgent_bucket_vit_mid, $urgent_cmp ); $html .= '<div class="tct-urgent-board" style="--tct-role-columns:5;">'; $html .= '<div class="tct-role-columns tct-urgent-columns" tabindex="0">'; $render_urgent_column = function ( $label, $color_css, $goals, $bucket_key ) use ( &$html, $is_connected ) { $count = is_array( $goals ) ? count( $goals ) : 0; $html .= '<div class="tct-role-column tct-urgent-column" data-tct-urgent-bucket="' . esc_attr( (string) $bucket_key ) . '" style="--tct-domain-color:' . esc_attr( $color_css ) . ';">'; $html .= '<div class="tct-role-header">'; $html .= '<div class="tct-role-title">' . esc_html( $label ) . '</div>'; $html .= '<div class="tct-role-meta">'; $html .= '<span class="tct-role-badge tct-urgent-count-badge" aria-label="' . esc_attr( $label ) . ' count">' . esc_html( (int) $count ) . '</span>'; $html .= '</div>'; $html .= '</div>'; if ( $count <= 0 ) { $html .= '<p class="tct-muted tct-role-empty">No goals in this bucket for the current view.</p>'; $html .= '</div>'; return; } $html .= '<div class="tct-domain-goals">'; foreach ( $goals as $g ) { if ( ! is_array( $g ) ) { continue; } $html .= $this->render_cockpit_goal_tile( $g, $is_connected ); } $html .= '</div>'; $html .= '</div>'; }; $render_urgent_column( 'Due Today', '#1e7e34', $urgent_bucket_due_today, 'due_today' ); $render_urgent_column( 'Critical', '#d63638', $urgent_bucket_critical, 'critical' ); $render_urgent_column( 'At risk', '#dba617', $urgent_bucket_risk, 'risk' ); $render_urgent_column( 'Vitality < 30%', '#7c3aed', $urgent_bucket_vit_low, 'vit_low' ); $render_urgent_column( 'Vitality 31-60%', '#2271b1', $urgent_bucket_vit_mid, 'vit_mid' ); $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $year = (int) $now_tz->format( 'Y' ); $year_start_tz = new DateTimeImmutable( $year . '-01-01 00:00:00', $tz ); $year_end_tz = $year_start_tz->add( new DateInterval( 'P1Y' ) ); $year_start_utc = TCT_Utils::dt_to_mysql_utc( $year_start_tz ); $year_end_utc = TCT_Utils::dt_to_mysql_utc( $year_end_tz ); $year_dates = array(); $year_period = new DatePeriod( $year_start_tz, new DateInterval( 'P1D' ), $year_end_tz ); foreach ( $year_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $year_dates[] = $dt->format( 'Y-m-d' ); } } $month_num = (int) $now_tz->format( 'n' ); if ( $month_num < 1 ) { $month_num = 1; } if ( $month_num > 12 ) { $month_num = 12; } $month_start_tz = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', $year, $month_num ), $tz ); $month_end_tz = $month_start_tz->add( new DateInterval( 'P1M' ) ); $month_dates = array(); $month_period = new DatePeriod( $month_start_tz, new DateInterval( 'P1D' ), $month_end_tz ); foreach ( $month_period as $mdt ) { if ( $mdt instanceof DateTimeInterface ) { $month_dates[] = $mdt->format( 'Y-m-d' ); } } $week_starts_on = (int) get_option( 'start_of_week', 1 ); $week_starts_on = ( 0 === $week_starts_on ) ? 0 : 1; $today_tz = $now_tz->setTime( 0, 0, 0 ); $today_dow = (int) $today_tz->format( 'w' ); $start_dow = ( 0 === $week_starts_on ) ? 0 : 1; $offset_days = ( $today_dow - $start_dow + 7 ) % 7; $week_start_tz = $today_tz->sub( new DateInterval( 'P' . $offset_days . 'D' ) ); $week_end_tz = $week_start_tz->add( new DateInterval( 'P7D' ) ); $week_dates = array(); $week_period = new DatePeriod( $week_start_tz, new DateInterval( 'P1D' ), $week_end_tz ); foreach ( $week_period as $wdt ) { if ( $wdt instanceof DateTimeInterface ) { $week_dates[] = $wdt->format( 'Y-m-d' ); } } $domain_points_by_day_year = $this->get_domain_points_by_day_for_window( $user_id, $year_start_utc, $year_end_utc, $tz ); $domain_possible_by_day_year = $this->compute_domain_possible_points_by_day_for_year( $year, $tz, $goal_rows, $role_map ); foreach ( $domain_order as $domain_id ) { $domain_id = (int) $domain_id; $domain_name = ''; if ( 0 === $domain_id ) { $domain_name = $has_unassigned_domain ? 'Unassigned' : 'Goals'; } else { $domain_name = isset( $domain_map[ $domain_id ] ) ? (string) $domain_map[ $domain_id ] : 'Domain'; } $domain_color = $domain_id > 0 && isset( $domain_color_map[ $domain_id ] ) ? (string) $domain_color_map[ $domain_id ] : ''; $domain_color_css = '' !== $domain_color ? $domain_color : '#e2e8f0'; $domain_rgb = $this->hex_to_rgb_triplet( $domain_color_css ); $domain_rgb_css = is_array( $domain_rgb ) && 3 === count( $domain_rgb ) ? ( (int) $domain_rgb[0] . ',' . (int) $domain_rgb[1] . ',' . (int) $domain_rgb[2] ) : '226,232,240'; $roles_in_domain = isset( $roles_by_domain[ $domain_id ] ) && is_array( $roles_by_domain[ $domain_id ] ) ? $roles_by_domain[ $domain_id ] : array(); $tab_key = 'domain-' . (int) $domain_id; $is_active_domain_panel = ( $tab_key === $default_domain_tab ); $tab_id = $domain_tabs_uid . '-tab-' . (int) $domain_id; $panel_id = $domain_tabs_uid . '-panel-' . (int) $domain_id; $panel_classes = 'tct-tab-panel' . ( $is_active_domain_panel ? ' tct-tab-panel-active' : '' ); $hidden = $is_active_domain_panel ? '' : ' hidden="hidden"'; $html .= '<div class="' . esc_attr( $panel_classes ) . '" data-tct-panel="' . esc_attr( $tab_key ) . '" role="tabpanel" id="' . esc_attr( $panel_id ) . '" aria-labelledby="' . esc_attr( $tab_id ) . '"' . $hidden . '>'; $html .= '<div class="tct-domain-rows" style="--tct-role-columns:' . esc_attr( (int) $max_role_columns ) . ';">'; $html .= '<div class="tct-domain-row" style="--tct-domain-color:' . esc_attr( $domain_color_css ) . '; --tct-domain-color-rgb:' . esc_attr( $domain_rgb_css ) . ';">'; $domain_day_points = isset( $domain_points_by_day_year[ $domain_id ] ) && is_array( $domain_points_by_day_year[ $domain_id ] ) ? $domain_points_by_day_year[ $domain_id ] : array(); $domain_day_possible = isset( $domain_possible_by_day_year[ $domain_id ] ) && is_array( $domain_possible_by_day_year[ $domain_id ] ) ? $domain_possible_by_day_year[ $domain_id ] : array(); $html .= $this->render_domain_year_spectrum_bar( $domain_id, $domain_name, $year, $year_dates, $domain_day_points, $domain_day_possible ); $html .= $this->render_domain_month_spectrum_bar( $domain_id, $domain_name, $year, $month_num, $month_dates, $domain_day_points, $domain_day_possible ); $html .= $this->render_domain_week_spectrum_bar( $domain_id, $domain_name, $week_starts_on, $week_dates, $domain_day_points, $domain_day_possible, $year, $month_num ); $role_order = array(); foreach ( $roles_in_domain as $r ) { $role_order[] = isset( $r['id'] ) ? (int) $r['id'] : 0; } $has_unassigned_role = isset( $goals_by_domain_role[ $domain_id ][0] ) && ! empty( $goals_by_domain_role[ $domain_id ][0] ); if ( $has_unassigned_role ) { $role_order[] = 0; } if ( empty( $role_order ) && isset( $goals_by_domain_role[ $domain_id ] ) && ! empty( $goals_by_domain_role[ $domain_id ] ) ) { $role_order[] = 0; } $html .= '<div class="tct-role-columns" tabindex="0">'; foreach ( $role_order as $rid ) { $rid = (int) $rid; $role_name = 'Unassigned'; if ( $rid > 0 && isset( $role_map[ $rid ] ) ) { $role_name = (string) $role_map[ $rid ]['role_name']; } $role_goals = array(); if ( isset( $goals_by_domain_role[ $domain_id ][ $rid ] ) && is_array( $goals_by_domain_role[ $domain_id ][ $rid ] ) ) { $role_goals = $goals_by_domain_role[ $domain_id ][ $rid ]; } $role_sum_count = 0; $role_sum_target = 0; $role_risk = 0; $role_critical = 0; foreach ( $role_goals as $g ) { $g_goal_type = isset( $g['goal_type'] ) ? (string) $g['goal_type'] : 'positive'; $is_no_interval_positive = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( $g_goal_type ) : ( 'positive_no_int' === $g_goal_type ); if ( ! $is_no_interval_positive ) { $role_sum_count += isset( $g['goal_count'] ) ? (int) $g['goal_count'] : 0; $role_sum_target += isset( $g['target'] ) ? (int) $g['target'] : 0; } $status_key = isset( $g['status_key'] ) ? (string) $g['status_key'] : ''; if ( 'critical' === $status_key ) { $role_critical++; } elseif ( 'risk' === $status_key ) { $role_risk++; } } $role_pct = 0; if ( $role_sum_target > 0 ) { $role_pct = min( 100, (int) round( ( $role_sum_count / $role_sum_target ) * 100 ) ); } $critical_hidden = $role_critical > 0 ? '' : ' hidden="hidden"'; $risk_hidden = $role_risk > 0 ? '' : ' hidden="hidden"'; $html .= '<div class="tct-role-column" data-role-id="' . esc_attr( (int) $rid ) . '">'; $html .= '<div class="tct-role-header">'; $html .= '<div class="tct-role-title">' . esc_html( $role_name ) . '</div>'; $html .= '<div class="tct-role-meta">'; $html .= '<span class="tct-role-badge tct-role-badge-critical" title="Critical goals" data-tct-role-critical-count' . $critical_hidden . '>' . esc_html( (int) $role_critical ) . '</span>'; $html .= '<span class="tct-role-badge tct-role-badge-risk" title="Goals at risk" data-tct-role-risk-count' . $risk_hidden . '>' . esc_html( (int) $role_risk ) . '</span>'; $html .= '<div class="tct-role-metric" hidden="hidden" aria-hidden="true"><span data-tct-role-count>' . esc_html( (int) $role_sum_count ) . '</span><span data-tct-role-target>' . esc_html( (int) $role_sum_target ) . '</span></div>'; $html .= '</div>'; $html .= '</div>'; if ( empty( $role_goals ) ) { $html .= '<p class="tct-muted tct-role-empty">No goals in this role for the current view.</p>'; $html .= '</div>'; continue; } $html .= '<div class="tct-domain-goals">'; foreach ( $role_goals as $g ) { $html .= $this->render_cockpit_goal_tile( $g, $is_connected ); } $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; } $complete_tab_key = 'complete'; $complete_tab_id = $domain_tabs_uid . '-tab-complete'; $complete_panel_id = $domain_tabs_uid . '-panel-complete'; $complete_is_active_panel = ( $complete_tab_key === $default_domain_tab ); $complete_panel_classes = 'tct-tab-panel' . ( $complete_is_active_panel ? ' tct-tab-panel-active' : '' ); $complete_hidden = $complete_is_active_panel ? '' : ' hidden="hidden"'; $html .= '<div class="' . esc_attr( $complete_panel_classes ) . '" data-tct-panel="' . esc_attr( $complete_tab_key ) . '" role="tabpanel" id="' . esc_attr( $complete_panel_id ) . '" aria-labelledby="' . esc_attr( $complete_tab_id ) . '"' . $complete_hidden . '>'; $complete_all_goals = array(); foreach ( $goals_by_domain_role as $c_did => $c_roles ) { if ( ! is_array( $c_roles ) ) { continue; } foreach ( $c_roles as $c_rid => $c_goal_list ) { if ( ! is_array( $c_goal_list ) ) { continue; } foreach ( $c_goal_list as $c_g ) { if ( ! is_array( $c_g ) ) { continue; } $c_status = isset( $c_g['status_key'] ) ? (string) $c_g['status_key'] : ''; if ( 'completed' !== $c_status ) { continue; } if ( ! empty( $c_g['availability_is_paused'] ) ) { continue; } $c_goal_type = isset( $c_g['goal_type'] ) ? (string) $c_g['goal_type'] : 'positive'; if ( TCT_Utils::is_negative_goal_type( $c_goal_type ) ) { continue; } $complete_all_goals[] = $c_g; } } } $calc_interval_days = function ( $unit, $span ) { $span = max( 1, (int) $span ); $unit = is_string( $unit ) ? strtolower( trim( $unit ) ) : 'week'; switch ( $unit ) { case 'hour': return (float) $span / 24.0; case 'day': return (float) $span; case 'week': return (float) $span * 7.0; case 'month': return (float) $span * 30.0; case 'quarter': return (float) $span * 90.0; case 'semiannual': return (float) $span * 180.0; case 'year': return (float) $span * 365.0; default: return (float) $span * 7.0; } }; $complete_bucket_daily = array(); $complete_bucket_weekly = array(); $complete_bucket_monthly = array(); $complete_bucket_longterm = array(); foreach ( $complete_all_goals as $c_goal ) { $c_unit = isset( $c_goal['unit'] ) ? (string) $c_goal['unit'] : 'week'; $c_span = isset( $c_goal['period_span'] ) ? (int) $c_goal['period_span'] : 1; $c_days = $calc_interval_days( $c_unit, $c_span ); if ( $c_days <= 1.0 ) { $complete_bucket_daily[] = $c_goal; } elseif ( $c_days <= 7.0 ) { $complete_bucket_weekly[] = $c_goal; } elseif ( $c_days <= 31.0 ) { $complete_bucket_monthly[] = $c_goal; } else { $complete_bucket_longterm[] = $c_goal; } } $complete_sort_cmp = function ( $a, $b ) { $a_ppc = isset( $a['points_per_completion'] ) ? (int) $a['points_per_completion'] : 0; $b_ppc = isset( $b['points_per_completion'] ) ? (int) $b['points_per_completion'] : 0; if ( $a_ppc !== $b_ppc ) { return $b_ppc <=> $a_ppc; } $a_name = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : ''; $b_name = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : ''; return strcasecmp( $a_name, $b_name ); }; $daily_order_map = $this->tct_get_goal_order_map( $user_id, 'daily' ); $complete_daily_sort_cmp = function ( $a, $b ) use ( $daily_order_map, $complete_sort_cmp ) { $aid = isset( $a['goal_id'] ) ? (int) $a['goal_id'] : 0; $bid = isset( $b['goal_id'] ) ? (int) $b['goal_id'] : 0; $ap = isset( $daily_order_map[ $aid ] ) ? (int) $daily_order_map[ $aid ] : PHP_INT_MAX; $bp = isset( $daily_order_map[ $bid ] ) ? (int) $daily_order_map[ $bid ] : PHP_INT_MAX; if ( $ap !== $bp ) { return $ap <=> $bp; } return $complete_sort_cmp( $a, $b ); }; usort( $complete_bucket_daily, $complete_daily_sort_cmp ); usort( $complete_bucket_weekly, $complete_sort_cmp ); usort( $complete_bucket_monthly, $complete_sort_cmp ); usort( $complete_bucket_longterm, $complete_sort_cmp ); $complete_color_css = '#22c55e'; $html .= '<div class="tct-urgent-board tct-complete-board" style="--tct-role-columns:4;">'; $html .= '<div class="tct-role-columns tct-complete-columns" tabindex="0">'; $render_complete_column = function ( $label, $color_css, $goals, $bucket_key ) use ( &$html, $is_connected ) { $count = is_array( $goals ) ? count( $goals ) : 0; $html .= '<div class="tct-role-column tct-complete-column" data-tct-complete-bucket="' . esc_attr( (string) $bucket_key ) . '" style="--tct-domain-color:' . esc_attr( $color_css ) . ';">'; $html .= '<div class="tct-role-header">'; $html .= '<div class="tct-role-title">' . esc_html( $label ) . '</div>'; $html .= '<div class="tct-role-meta">'; $html .= '<span class="tct-role-badge tct-complete-count-badge" aria-label="' . esc_attr( $label ) . ' count">' . esc_html( (int) $count ) . '</span>'; $html .= '</div>'; $html .= '</div>'; if ( $count <= 0 ) { $html .= '<p class="tct-muted tct-role-empty">No completed goals in this category.</p>'; $html .= '</div>'; return; } $html .= '<div class="tct-domain-goals">'; foreach ( $goals as $g ) { if ( ! is_array( $g ) ) { continue; } $g['__tct_context'] = 'complete'; $html .= $this->render_cockpit_goal_tile( $g, $is_connected ); } $html .= '</div>'; $html .= '</div>'; }; $render_complete_column( 'Daily', $complete_color_css, $complete_bucket_daily, 'daily' ); $render_complete_column( 'Weekly', $complete_color_css, $complete_bucket_weekly, 'weekly' ); $render_complete_column( 'Short-Game', $complete_color_css, $complete_bucket_monthly, 'monthly' ); $render_complete_column( 'Long-Game', $complete_color_css, $complete_bucket_longterm, 'longterm' ); $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $paused_tab_key = 'paused'; $paused_tab_id = $domain_tabs_uid . '-tab-paused'; $paused_panel_id = $domain_tabs_uid . '-panel-paused'; $paused_is_active_panel = ( $paused_tab_key === $default_domain_tab ); $paused_panel_classes = 'tct-tab-panel' . ( $paused_is_active_panel ? ' tct-tab-panel-active' : '' ); $paused_hidden = $paused_is_active_panel ? '' : ' hidden="hidden"'; $html .= '<div class="' . esc_attr( $paused_panel_classes ) . '" data-tct-panel="' . esc_attr( $paused_tab_key ) . '" role="tabpanel" id="' . esc_attr( $paused_panel_id ) . '" aria-labelledby="' . esc_attr( $paused_tab_id ) . '"' . $paused_hidden . '>'; $paused_all_goals = array(); foreach ( $goals_by_domain_role as $p_did => $p_roles ) { if ( ! is_array( $p_roles ) ) { continue; } foreach ( $p_roles as $p_rid => $p_goal_list ) { if ( ! is_array( $p_goal_list ) ) { continue; } foreach ( $p_goal_list as $p_g ) { if ( ! is_array( $p_g ) ) { continue; } if ( empty( $p_g['availability_is_paused'] ) ) { continue; } $p_goal_type = isset( $p_g['goal_type'] ) ? (string) $p_g['goal_type'] : 'positive'; if ( TCT_Utils::is_negative_goal_type( $p_goal_type ) ) { continue; } $paused_all_goals[] = $p_g; } } } $paused_bucket_daily = array(); $paused_bucket_weekly = array(); $paused_bucket_monthly = array(); $paused_bucket_longterm = array(); foreach ( $paused_all_goals as $p_goal ) { $p_unit = isset( $p_goal['unit'] ) ? (string) $p_goal['unit'] : 'week'; $p_span = isset( $p_goal['period_span'] ) ? (int) $p_goal['period_span'] : 1; $p_days = $calc_interval_days( $p_unit, $p_span ); if ( $p_days <= 1.0 ) { $paused_bucket_daily[] = $p_goal; } elseif ( $p_days <= 7.0 ) { $paused_bucket_weekly[] = $p_goal; } elseif ( $p_days <= 31.0 ) { $paused_bucket_monthly[] = $p_goal; } else { $paused_bucket_longterm[] = $p_goal; } } usort( $paused_bucket_daily, $complete_daily_sort_cmp ); usort( $paused_bucket_weekly, $complete_sort_cmp ); usort( $paused_bucket_monthly, $complete_sort_cmp ); usort( $paused_bucket_longterm, $complete_sort_cmp ); $paused_color_css = '#64748b'; $html .= '<div class="tct-urgent-board tct-paused-board" style="--tct-role-columns:4;">'; $html .= '<div class="tct-role-columns tct-paused-columns" tabindex="0">'; $render_paused_column = function ( $label, $color_css, $goals, $bucket_key ) use ( &$html, $is_connected ) { $count = is_array( $goals ) ? count( $goals ) : 0; $html .= '<div class="tct-role-column tct-paused-column" data-tct-paused-bucket="' . esc_attr( (string) $bucket_key ) . '" style="--tct-domain-color:' . esc_attr( $color_css ) . ';">'; $html .= '<div class="tct-role-header">'; $html .= '<div class="tct-role-title">' . esc_html( $label ) . '</div>'; $html .= '<div class="tct-role-meta">'; $html .= '<span class="tct-role-badge tct-paused-count-badge" aria-label="' . esc_attr( $label ) . ' count">' . esc_html( (int) $count ) . '</span>'; $html .= '</div>'; $html .= '</div>'; if ( $count <= 0 ) { $html .= '<p class="tct-muted tct-role-empty">No paused goals in this category.</p>'; $html .= '</div>'; return; } $html .= '<div class="tct-domain-goals">'; foreach ( $goals as $g ) { if ( ! is_array( $g ) ) { continue; } $g['__tct_context'] = 'paused'; $html .= $this->render_cockpit_goal_tile( $g, $is_connected ); } $html .= '</div>'; $html .= '</div>'; }; $render_paused_column( 'Daily', $paused_color_css, $paused_bucket_daily, 'daily' ); $render_paused_column( 'Weekly', $paused_color_css, $paused_bucket_weekly, 'weekly' ); $render_paused_column( 'Short-Game', $paused_color_css, $paused_bucket_monthly, 'monthly' ); $render_paused_column( 'Long-Game', $paused_color_css, $paused_bucket_longterm, 'longterm' ); $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function hex_to_rgb_triplet( $hex ) { $hex = sanitize_hex_color( $hex ); if ( ! $hex ) { return null; } $hex = ltrim( $hex, '#' ); $len = strlen( $hex ); if ( 3 === $len ) { $r = hexdec( str_repeat( $hex[0], 2 ) ); $g = hexdec( str_repeat( $hex[1], 2 ) ); $b = hexdec( str_repeat( $hex[2], 2 ) ); return array( (int) $r, (int) $g, (int) $b ); } if ( 6 !== $len ) { return null; } $r = hexdec( substr( $hex, 0, 2 ) ); $g = hexdec( substr( $hex, 2, 2 ) ); $b = hexdec( substr( $hex, 4, 2 ) ); return array( (int) $r, (int) $g, (int) $b ); } private function get_domain_points_by_day_for_range( $user_id, $range_start_utc_mysql, $range_end_utc_mysql, $tz ) { global $wpdb; $user_id = (int) $user_id; if ( $user_id <= 0 ) { return array(); } $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.points_per_completion,
                        COALESCE(NULLIF(r.domain_id,0), NULLIF(g.domain_id,0), 0) AS domain_id
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g
                    ON g.user_id = c.user_id AND g.id = c.goal_id
                 LEFT JOIN {$roles_table} r
                    ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at <= %s
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND (
                        g.points_enabled_at IS NULL
                        OR g.points_enabled_at = ''
                        OR g.points_enabled_at = '0000-00-00 00:00:00'
                        OR c.completed_at >= g.points_enabled_at
                   )", $user_id, $range_start_utc_mysql, $range_end_utc_mysql ), ARRAY_A ); if ( ! is_array( $rows ) || empty( $rows ) ) { return array(); } $out = array(); $utc = new DateTimeZone( 'UTC' ); foreach ( $rows as $r ) { $domain_id = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; $raw = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; if ( $ppc <= 0 ) { continue; } $raw = trim( $raw ); if ( '' === $raw || '0000-00-00 00:00:00' === $raw ) { continue; } try { $dt_utc = new DateTimeImmutable( $raw, $utc ); } catch ( Exception $e ) { continue; } $dt_local = $dt_utc->setTimezone( $tz ); $day_key = $dt_local->format( 'Y-m-d' ); if ( ! isset( $out[ $domain_id ] ) ) { $out[ $domain_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $day_key ] ) ) { $out[ $domain_id ][ $day_key ] = 0; } $out[ $domain_id ][ $day_key ] += $ppc; } return $out; } private function get_domain_points_by_day_for_window( $user_id, $range_start_utc, $range_end_utc, $tz ) { global $wpdb; $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $range_start_utc = is_string( $range_start_utc ) ? trim( $range_start_utc ) : ''; $range_end_utc = is_string( $range_end_utc ) ? trim( $range_end_utc ) : ''; if ( '' === $range_start_utc || '' === $range_end_utc ) { return array(); } $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.points_per_completion,
                        COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) AS domain_id,
                        g.points_enabled_at
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at < %s
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $range_start_utc, $range_end_utc ), ARRAY_A ); $out = array(); foreach ( (array) $rows as $r ) { $domain_id = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $completed_at = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; if ( '' === $completed_at || '0000-00-00 00:00:00' === $completed_at ) { continue; } $local_day = TCT_Utils::mysql_utc_to_tz( $completed_at, $tz, 'Y-m-d' ); if ( ! is_string( $local_day ) || '' === $local_day ) { continue; } if ( ! isset( $out[ $domain_id ] ) ) { $out[ $domain_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $local_day ] ) ) { $out[ $domain_id ][ $local_day ] = 0; } $out[ $domain_id ][ $local_day ] += $ppc; } return $out; } private function get_domain_role_points_by_day_for_window( $user_id, $range_start_utc, $range_end_utc, $tz ) { global $wpdb; $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $range_start_utc = is_string( $range_start_utc ) ? trim( $range_start_utc ) : ''; $range_end_utc = is_string( $range_end_utc ) ? trim( $range_end_utc ) : ''; if ( '' === $range_start_utc || '' === $range_end_utc ) { return array(); } $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.points_per_completion,
                        COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) AS domain_id,
                        g.role_id,
                        g.points_enabled_at
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at < %s
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND g.role_id > 0
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $range_start_utc, $range_end_utc ), ARRAY_A ); $out = array(); foreach ( (array) $rows as $r ) { $domain_id = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; $role_id = isset( $r['role_id'] ) ? (int) $r['role_id'] : 0; $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; if ( $role_id <= 0 || $ppc <= 0 ) { continue; } $completed_at = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; if ( '' === $completed_at || '0000-00-00 00:00:00' === $completed_at ) { continue; } $local_day = TCT_Utils::mysql_utc_to_tz( $completed_at, $tz, 'Y-m-d' ); if ( ! is_string( $local_day ) || '' === $local_day ) { continue; } if ( ! isset( $out[ $domain_id ] ) ) { $out[ $domain_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $role_id ] ) ) { $out[ $domain_id ][ $role_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $role_id ][ $local_day ] ) ) { $out[ $domain_id ][ $role_id ][ $local_day ] = 0; } $out[ $domain_id ][ $role_id ][ $local_day ] += $ppc; } return $out; } private function get_domain_role_goal_points_by_day_for_window( $user_id, $range_start_utc, $range_end_utc, $tz ) { global $wpdb; $completions_table = TCT_DB::table_completions(); $goals_table = TCT_DB::table_goals(); $roles_table = TCT_DB::table_roles(); $range_start_utc = is_string( $range_start_utc ) ? trim( $range_start_utc ) : ''; $range_end_utc = is_string( $range_end_utc ) ? trim( $range_end_utc ) : ''; if ( '' === $range_start_utc || '' === $range_end_utc ) { return array(); } $rows = $wpdb->get_results( $wpdb->prepare( "SELECT c.completed_at, g.id AS goal_id, g.points_per_completion,
                        COALESCE(NULLIF(r.domain_id, 0), NULLIF(g.domain_id, 0), 0) AS domain_id,
                        g.role_id,
                        g.points_enabled_at
                 FROM {$completions_table} c
                 INNER JOIN {$goals_table} g ON g.id = c.goal_id AND g.user_id = c.user_id
                 LEFT JOIN {$roles_table} r ON r.user_id = g.user_id AND r.id = g.role_id
                 WHERE c.user_id = %d
                   AND c.goal_id > 0
                   AND c.completed_at >= %s AND c.completed_at < %s
                   AND g.is_tracked = 1
                   AND g.points_per_completion > 0
                   AND (g.goal_type IS NULL OR g.goal_type = '' OR g.goal_type = 'positive' OR g.goal_type = 'positive_no_int' OR g.goal_type = 'anki_cards')
                   AND g.role_id > 0
                   AND (g.points_enabled_at IS NULL OR g.points_enabled_at = '0000-00-00 00:00:00' OR c.completed_at >= g.points_enabled_at)", $user_id, $range_start_utc, $range_end_utc ), ARRAY_A ); $out = array(); foreach ( (array) $rows as $r ) { $domain_id = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; $role_id = isset( $r['role_id'] ) ? (int) $r['role_id'] : 0; $goal_id = isset( $r['goal_id'] ) ? (int) $r['goal_id'] : 0; $ppc = isset( $r['points_per_completion'] ) ? (int) $r['points_per_completion'] : 0; if ( $role_id <= 0 || $goal_id <= 0 || $ppc <= 0 ) { continue; } $completed_at = isset( $r['completed_at'] ) ? (string) $r['completed_at'] : ''; if ( '' === $completed_at || '0000-00-00 00:00:00' === $completed_at ) { continue; } $local_day = TCT_Utils::mysql_utc_to_tz( $completed_at, $tz, 'Y-m-d' ); if ( ! is_string( $local_day ) || '' === $local_day ) { continue; } if ( ! isset( $out[ $domain_id ] ) ) { $out[ $domain_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $role_id ] ) ) { $out[ $domain_id ][ $role_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $role_id ][ $goal_id ] ) ) { $out[ $domain_id ][ $role_id ][ $goal_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $role_id ][ $goal_id ][ $local_day ] ) ) { $out[ $domain_id ][ $role_id ][ $goal_id ][ $local_day ] = 0; } $out[ $domain_id ][ $role_id ][ $goal_id ][ $local_day ] += $ppc; } return $out; } private function effective_domain_id_from_goal_row( $row, $role_map ) { if ( isset( $row['tct_effective_domain_id'] ) ) { return (int) $row['tct_effective_domain_id']; } $role_id = isset( $row['role_id'] ) ? (int) $row['role_id'] : 0; if ( $role_id > 0 && is_array( $role_map ) && isset( $role_map[ $role_id ] ) ) { $maybe = isset( $role_map[ $role_id ]['domain_id'] ) ? (int) $role_map[ $role_id ]['domain_id'] : 0; if ( $maybe > 0 ) { return $maybe; } } return isset( $row['domain_id'] ) ? (int) $row['domain_id'] : 0; } private function compute_domain_possible_points_by_day_for_year( $year, $tz, $goal_rows, $role_map ) { $year = (int) $year; if ( $year < 1970 || $year > 3000 ) { return array(); } if ( ! ( $tz instanceof DateTimeZone ) ) { $tz = TCT_Utils::wp_timezone(); } $year_start = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', $year ), $tz ); $year_end = $year_start->add( new DateInterval( 'P1Y' ) ); $out = array(); foreach ( (array) $goal_rows as $row ) { $is_tracked = isset( $row['is_tracked'] ) ? (int) $row['is_tracked'] : 1; if ( 1 !== $is_tracked ) { continue; } $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $goal_type_val = isset( $row['goal_type'] ) ? (string) $row['goal_type'] : ''; if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_goal_type_economy_eligible' ) ) ) { if ( ! TCT_Utils::is_goal_type_economy_eligible( $goal_type_val ) ) { continue; } } elseif ( 'positive_no_int' === strtolower( trim( (string) $goal_type_val ) ) ) { continue; } $intervals = $this->intervals_from_goal_row( $row ); if ( empty( $intervals ) ) { continue; } $interval = $intervals[0]; $unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : ''; $target = isset( $interval['target'] ) ? (int) $interval['target'] : 0; if ( $target <= 0 || '' === $unit ) { continue; } $domain_id = $this->effective_domain_id_from_goal_row( $row, $role_map ); $start_dt = $year_start; $enabled_raw = isset( $row['points_enabled_at'] ) ? (string) $row['points_enabled_at'] : ''; $enabled_raw = is_string( $enabled_raw ) ? trim( $enabled_raw ) : ''; if ( '' !== $enabled_raw && '0000-00-00 00:00:00' !== $enabled_raw ) { $enabled_day = TCT_Utils::mysql_utc_to_tz( $enabled_raw, $tz, 'Y-m-d' ); if ( is_string( $enabled_day ) && '' !== $enabled_day ) { try { $enabled_dt = new DateTimeImmutable( $enabled_day . ' 00:00:00', $tz ); if ( $enabled_dt > $start_dt ) { $start_dt = $enabled_dt; } } catch ( Exception $e ) { } } } if ( $start_dt >= $year_end ) { continue; } $period = new DatePeriod( $start_dt, new DateInterval( 'P1D' ), $year_end ); foreach ( $period as $dt ) { if ( ! ( $dt instanceof DateTimeInterface ) ) { continue; } $date_key = $dt->format( 'Y-m-d' ); if ( ! isset( $out[ $domain_id ] ) ) { $out[ $domain_id ] = array(); } if ( ! isset( $out[ $domain_id ][ $date_key ] ) ) { $out[ $domain_id ][ $date_key ] = 0.0; } $add = 0.0; if ( 'day' === $unit ) { $add = (float) ( $target * $ppc ); } elseif ( 'week' === $unit ) { $add = (float) ( $target * $ppc ) / 7.0; } elseif ( 'month' === $unit ) { $days_in_month = (int) $dt->format( 't' ); if ( $days_in_month < 1 ) { $days_in_month = 1; } $add = (float) ( $target * $ppc ) / (float) $days_in_month; } else { $add = (float) ( $target * $ppc ); } $out[ $domain_id ][ $date_key ] += $add; } } return $out; } private function compute_role_possible_points_by_day_for_year( $year, $tz, $goal_rows ) { $year = (int) $year; if ( $year < 1970 || $year > 3000 ) { return array(); } if ( ! ( $tz instanceof DateTimeZone ) ) { $tz = TCT_Utils::wp_timezone(); } $year_start = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', $year ), $tz ); $year_end = $year_start->add( new DateInterval( 'P1Y' ) ); $out = array(); foreach ( (array) $goal_rows as $row ) { $is_tracked = isset( $row['is_tracked'] ) ? (int) $row['is_tracked'] : 1; if ( 1 !== $is_tracked ) { continue; } $role_id = isset( $row['role_id'] ) ? (int) $row['role_id'] : 0; if ( $role_id <= 0 ) { continue; } $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $goal_type_val = isset( $row['goal_type'] ) ? (string) $row['goal_type'] : ''; if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_goal_type_economy_eligible' ) ) ) { if ( ! TCT_Utils::is_goal_type_economy_eligible( $goal_type_val ) ) { continue; } } elseif ( 'positive_no_int' === strtolower( trim( (string) $goal_type_val ) ) ) { continue; } $intervals = $this->intervals_from_goal_row( $row ); if ( empty( $intervals ) ) { continue; } $interval = $intervals[0]; $unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : ''; $target = isset( $interval['target'] ) ? (int) $interval['target'] : 0; if ( $target <= 0 || '' === $unit ) { continue; } $start_dt = $year_start; $enabled_raw = isset( $row['points_enabled_at'] ) ? (string) $row['points_enabled_at'] : ''; $enabled_raw = is_string( $enabled_raw ) ? trim( $enabled_raw ) : ''; if ( '' !== $enabled_raw && '0000-00-00 00:00:00' !== $enabled_raw ) { $enabled_day = TCT_Utils::mysql_utc_to_tz( $enabled_raw, $tz, 'Y-m-d' ); if ( is_string( $enabled_day ) && '' !== $enabled_day ) { try { $enabled_dt = new DateTimeImmutable( $enabled_day . ' 00:00:00', $tz ); if ( $enabled_dt > $start_dt ) { $start_dt = $enabled_dt; } } catch ( Exception $e ) { } } } if ( $start_dt >= $year_end ) { continue; } $period = new DatePeriod( $start_dt, new DateInterval( 'P1D' ), $year_end ); foreach ( $period as $dt ) { if ( ! ( $dt instanceof DateTimeInterface ) ) { continue; } $date_key = $dt->format( 'Y-m-d' ); if ( ! isset( $out[ $role_id ] ) ) { $out[ $role_id ] = array(); } if ( ! isset( $out[ $role_id ][ $date_key ] ) ) { $out[ $role_id ][ $date_key ] = 0.0; } $add = 0.0; if ( 'day' === $unit ) { $add = (float) ( $target * $ppc ); } elseif ( 'week' === $unit ) { $add = (float) ( $target * $ppc ) / 7.0; } elseif ( 'month' === $unit ) { $days_in_month = (int) $dt->format( 't' ); if ( $days_in_month < 1 ) { $days_in_month = 1; } $add = (float) ( $target * $ppc ) / (float) $days_in_month; } else { $add = (float) ( $target * $ppc ); } $out[ $role_id ][ $date_key ] += $add; } } return $out; } private function compute_goal_possible_points_for_dates( $date_keys, $tz, $goal_rows ) { if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return array(); } if ( ! ( $tz instanceof DateTimeZone ) ) { $tz = TCT_Utils::wp_timezone(); } $out = array(); foreach ( (array) $goal_rows as $row ) { if ( ! is_array( $row ) ) { continue; } $is_tracked = isset( $row['is_tracked'] ) ? (int) $row['is_tracked'] : 1; if ( 1 !== $is_tracked ) { continue; } $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0; if ( $goal_id <= 0 ) { continue; } $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } $goal_type_val = isset( $row['goal_type'] ) ? (string) $row['goal_type'] : ''; if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_goal_type_economy_eligible' ) ) ) { if ( ! TCT_Utils::is_goal_type_economy_eligible( $goal_type_val ) ) { continue; } } elseif ( 'positive_no_int' === strtolower( trim( (string) $goal_type_val ) ) ) { continue; } $intervals = $this->intervals_from_goal_row( $row ); if ( empty( $intervals ) ) { continue; } $interval = $intervals[0]; $unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : ''; $target = isset( $interval['target'] ) ? (int) $interval['target'] : 0; if ( $target <= 0 || '' === $unit ) { continue; } $enabled_day = ''; $enabled_raw = isset( $row['points_enabled_at'] ) ? (string) $row['points_enabled_at'] : ''; $enabled_raw = is_string( $enabled_raw ) ? trim( $enabled_raw ) : ''; if ( '' !== $enabled_raw && '0000-00-00 00:00:00' !== $enabled_raw ) { $maybe_day = TCT_Utils::mysql_utc_to_tz( $enabled_raw, $tz, 'Y-m-d' ); if ( is_string( $maybe_day ) && '' !== $maybe_day ) { $enabled_day = $maybe_day; } } foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } if ( '' !== $enabled_day && strcmp( $dk, $enabled_day ) < 0 ) { continue; } $add = 0.0; if ( 'day' === $unit ) { $add = (float) ( $target * $ppc ); } elseif ( 'week' === $unit ) { $add = (float) ( $target * $ppc ) / 7.0; } elseif ( 'month' === $unit ) { $days_in_month = 1; try { $dt = new DateTimeImmutable( $dk . ' 00:00:00', $tz ); $days_in_month = (int) $dt->format( 't' ); if ( $days_in_month < 1 ) { $days_in_month = 1; } } catch ( Exception $e ) { $days_in_month = 1; } $add = (float) ( $target * $ppc ) / (float) $days_in_month; } else { $add = (float) ( $target * $ppc ); } if ( ! isset( $out[ $goal_id ] ) ) { $out[ $goal_id ] = array(); } if ( ! isset( $out[ $goal_id ][ $dk ] ) ) { $out[ $goal_id ][ $dk ] = 0.0; } $out[ $goal_id ][ $dk ] += $add; } } return $out; } private function render_domain_year_spectrum_bar( $domain_id, $domain_name, $year, $date_keys, $points_by_day, $possible_by_day ) { $domain_id = (int) $domain_id; $domain_name = is_string( $domain_name ) ? $domain_name : ''; $year = (int) $year; $tz = TCT_Utils::wp_timezone(); if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); $months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ); $month_cols = $this->get_month_grid_template_for_year( $year ); $html = '<div class="tct-domain-yearbar" data-tct-domain-yearbar="1" role="button" tabindex="0"'; $html .= ' data-domain-id="' . esc_attr( $domain_id ) . '"'; $html .= ' data-year="' . esc_attr( $year ) . '"'; $html .= ' data-domain-name="' . esc_attr( $domain_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-yearbar-bar">'; $html .= '<div class="tct-domain-yearbar-strip" style="--tct-domain-yearbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-yearbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-yearbar-empty'; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-yearbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-yearbar-monthlines" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; for ( $i = 0; $i < 12; $i++ ) { $html .= '<span></span>'; } $html .= '</div>'; $html .= '</div>'; $now_tz = new DateTimeImmutable( 'now', $tz ); $cur_year = (int) $now_tz->format( 'Y' ); $cur_month = (int) $now_tz->format( 'n' ); $html .= '<div class="tct-domain-yearbar-monthlabels" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; foreach ( $months as $idx => $m ) { $month_num = (int) $idx + 1; $cls = ( (int) $year === $cur_year && $month_num === $cur_month ) ? ' class="tct-heatmap-current-label"' : ''; $html .= '<span' . $cls . '>' . esc_html( $m ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domain_year_spectrum_bar_static( $domain_id, $domain_name, $year, $date_keys, $points_by_day, $possible_by_day ) { $domain_id = (int) $domain_id; $domain_name = is_string( $domain_name ) ? $domain_name : ''; $year = (int) $year; $tz = TCT_Utils::wp_timezone(); if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); $months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ); $month_cols = $this->get_month_grid_template_for_year( $year ); $html = '<div class="tct-domain-yearbar tct-domain-yearbar-static" data-tct-domain-yearbar="0"'; $html .= ' data-domain-id="' . esc_attr( $domain_id ) . '"'; $html .= ' data-year="' . esc_attr( $year ) . '"'; $html .= ' data-domain-name="' . esc_attr( $domain_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-yearbar-bar">'; $html .= '<div class="tct-domain-yearbar-strip" style="--tct-domain-yearbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-yearbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-yearbar-empty'; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-yearbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-yearbar-monthlines" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; for ( $i = 0; $i < 12; $i++ ) { $html .= '<span></span>'; } $html .= '</div>'; $html .= '</div>'; $now_tz = new DateTimeImmutable( 'now', $tz ); $cur_year = (int) $now_tz->format( 'Y' ); $cur_month = (int) $now_tz->format( 'n' ); $html .= '<div class="tct-domain-yearbar-monthlabels" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; foreach ( $months as $idx => $m ) { $month_num = (int) $idx + 1; $cls = ( (int) $year === $cur_year && $month_num === $cur_month ) ? ' class="tct-heatmap-current-label"' : ''; $html .= '<span' . $cls . '>' . esc_html( $m ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_role_year_spectrum_bar( $role_id, $role_name, $year, $date_keys, $points_by_day, $possible_by_day, $alpha_by_day = null, $parent_points_by_day = null, $parent_label = 'Domain' ) { $role_id = (int) $role_id; $role_name = is_string( $role_name ) ? $role_name : ''; $year = (int) $year; $tz = TCT_Utils::wp_timezone(); if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); $months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ); $month_cols = $this->get_month_grid_template_for_year( $year ); $html = '<div class="tct-domain-yearbar tct-role-yearbar"'; $html .= ' data-role-id="' . esc_attr( $role_id ) . '"'; $html .= ' data-year="' . esc_attr( $year ) . '"'; $html .= ' data-role-name="' . esc_attr( $role_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-yearbar-bar">'; $html .= '<div class="tct-domain-yearbar-strip" style="--tct-domain-yearbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-yearbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-yearbar-empty'; } else { $alpha = null; if ( is_array( $alpha_by_day ) && isset( $alpha_by_day[ $dk ] ) ) { $alpha = (float) $alpha_by_day[ $dk ]; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); } if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-yearbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } if ( is_array( $parent_points_by_day ) && isset( $parent_points_by_day[ $dk ] ) ) { $parent_pts = (float) $parent_points_by_day[ $dk ]; if ( $parent_pts > 0.0 ) { $share = (int) round( ( (float) $pts / (float) $parent_pts ) * 100.0 ); if ( $share < 0 ) { $share = 0; } if ( $share > 100 ) { $share = 100; } $title .= ' * ' . (int) $share . '% of ' . $parent_label; } } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-yearbar-monthlines" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; for ( $i = 0; $i < 12; $i++ ) { $html .= '<span></span>'; } $html .= '</div>'; $html .= '</div>'; $now_tz = new DateTimeImmutable( 'now', $tz ); $cur_year = (int) $now_tz->format( 'Y' ); $cur_month = (int) $now_tz->format( 'n' ); $html .= '<div class="tct-domain-yearbar-monthlabels" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; foreach ( $months as $idx => $m ) { $month_num = (int) $idx + 1; $cls = ( (int) $year === $cur_year && $month_num === $cur_month ) ? ' class="tct-heatmap-current-label"' : ''; $html .= '<span' . $cls . '>' . esc_html( $m ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_goal_year_spectrum_bar( $goal_id, $goal_name, $year, $date_keys, $points_by_day, $possible_by_day, $alpha_by_day = null, $parent_points_by_day = null, $parent_label = 'Role' ) { $goal_id = (int) $goal_id; $goal_name = is_string( $goal_name ) ? $goal_name : ''; $year = (int) $year; $tz = TCT_Utils::wp_timezone(); if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); $months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ); $month_cols = $this->get_month_grid_template_for_year( $year ); $html = '<div class="tct-domain-yearbar tct-goal-yearbar"'; $html .= ' data-goal-id="' . esc_attr( $goal_id ) . '"'; $html .= ' data-year="' . esc_attr( $year ) . '"'; $html .= ' data-goal-name="' . esc_attr( $goal_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-yearbar-bar">'; $html .= '<div class="tct-domain-yearbar-strip" style="--tct-domain-yearbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-yearbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-yearbar-empty'; } else { $alpha = null; if ( is_array( $alpha_by_day ) && isset( $alpha_by_day[ $dk ] ) ) { $alpha = (float) $alpha_by_day[ $dk ]; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); } if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-yearbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } if ( is_array( $parent_points_by_day ) && isset( $parent_points_by_day[ $dk ] ) ) { $parent_pts = (float) $parent_points_by_day[ $dk ]; if ( $parent_pts > 0.0 ) { $share = (int) round( ( (float) $pts / (float) $parent_pts ) * 100.0 ); if ( $share < 0 ) { $share = 0; } if ( $share > 100 ) { $share = 100; } $title .= ' * ' . (int) $share . '% of ' . $parent_label; } } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-yearbar-monthlines" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; for ( $i = 0; $i < 12; $i++ ) { $html .= '<span></span>'; } $html .= '</div>'; $html .= '</div>'; $now_tz = new DateTimeImmutable( 'now', $tz ); $cur_year = (int) $now_tz->format( 'Y' ); $cur_month = (int) $now_tz->format( 'n' ); $html .= '<div class="tct-domain-yearbar-monthlabels" style="grid-template-columns:' . esc_attr( $month_cols ) . ';">'; foreach ( $months as $idx => $m ) { $month_num = (int) $idx + 1; $cls = ( (int) $year === $cur_year && $month_num === $cur_month ) ? ' class="tct-heatmap-current-label"' : ''; $html .= '<span' . $cls . '>' . esc_html( $m ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domain_week_spectrum_bar( $domain_id, $domain_name, $week_starts_on, $date_keys, $points_by_day, $possible_by_day, $year = 0, $month = 0 ) { $domain_id = (int) $domain_id; $domain_name = (string) $domain_name; $week_starts_on = ( 0 === (int) $week_starts_on ) ? 0 : 1; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } $days = count( $date_keys ); if ( $days < 1 ) { return ''; } $year = (int) $year; $month = (int) $month; $html = '<div class="tct-domain-weekbar" data-tct-domain-weekbar="1" role="button" tabindex="0"'; $html .= ' data-domain-id="' . esc_attr( $domain_id ) . '"'; $html .= ' data-domain-name="' . esc_attr( $domain_name ) . '"'; $html .= ' data-week-starts-on="' . esc_attr( $week_starts_on ) . '"'; if ( $year >= 1970 ) { $html .= ' data-year="' . esc_attr( $year ) . '"'; } if ( $month >= 1 && $month <= 12 ) { $html .= ' data-month="' . esc_attr( $month ) . '"'; } $html .= '>'; $html .= '<div class="tct-domain-weekbar-bar">'; $html .= '<div class="tct-domain-weekbar-strip" style="--tct-domain-weekbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = (string) $dk; $pts = isset( $points_by_day[ $dk ] ) ? (float) $points_by_day[ $dk ] : 0.0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-weekbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-weekbar-empty'; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-weekbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '" title="' . esc_attr( $title ) . '"' . $style . '></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-dayticks" data-tct-weekbar-dayticks="1"></div>'; $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-daylabels" data-tct-weekbar-daylabels="1"></div>'; $html .= '</div>'; return $html; } private function render_domain_week_spectrum_bar_static( $domain_id, $domain_name, $date_keys, $points_by_day, $possible_by_day, $today_ymd = '' ) { $domain_id = (int) $domain_id; $domain_name = is_string( $domain_name ) ? $domain_name : ''; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); if ( $days < 1 ) { return ''; } $tz = TCT_Utils::wp_timezone(); $html = '<div class="tct-domain-weekbar tct-domain-weekbar-static" data-tct-domain-weekbar-static="1"'; $html .= ' data-domain-id="' . esc_attr( (int) $domain_id ) . '"'; $html .= ' data-domain-name="' . esc_attr( $domain_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-weekbar-bar">'; $html .= '<div class="tct-domain-weekbar-strip" style="--tct-domain-weekbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (float) $points_by_day[ $dk ] : 0.0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-weekbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-weekbar-empty'; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-weekbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '" title="' . esc_attr( $title ) . '"' . $style . '></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-dayticks">'; for ( $i = 0; $i < $days; $i++ ) { $left = ( $days > 0 ) ? ( ( (float) $i / (float) $days ) * 100.0 ) : 0.0; $html .= '<span class="tct-domain-weekbar-day-tick" style="left:' . esc_attr( $left ) . '%"></span>'; } $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-daylabels">'; foreach ( $date_keys as $idx => $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $left = ( $days > 0 ) ? ( ( ( (float) $idx + 0.5 ) / (float) $days ) * 100.0 ) : 0.0; $dow = ''; try { $dt = new DateTimeImmutable( $dk . ' 00:00:00', $tz ); $dow = $dt->format( 'D' ); } catch ( Exception $e ) { $dow = ''; } if ( '' === $dow ) { $dow = 'Day'; } $cls = 'tct-domain-weekbar-day-label'; if ( is_string( $today_ymd ) && '' !== $today_ymd && $dk === $today_ymd ) { $cls .= ' tct-heatmap-current-label'; } $html .= '<span class="' . esc_attr( $cls ) . '" style="left:' . esc_attr( $left ) . '%">' . esc_html( $dow ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_role_week_spectrum_bar( $role_id, $role_name, $date_keys, $points_by_day, $possible_by_day, $today_ymd = '', $alpha_by_day = null, $parent_points_by_day = null, $parent_label = '' ) { $role_id = (int) $role_id; $role_name = is_string( $role_name ) ? $role_name : ''; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $alpha_by_day = is_array( $alpha_by_day ) ? $alpha_by_day : null; $parent_points_by_day = is_array( $parent_points_by_day ) ? $parent_points_by_day : null; $parent_label = is_string( $parent_label ) ? trim( $parent_label ) : ''; if ( '' === $parent_label ) { $parent_label = 'Domain'; } $days = count( $date_keys ); if ( $days < 1 ) { return ''; } $tz = TCT_Utils::wp_timezone(); $html = '<div class="tct-domain-weekbar tct-role-weekbar" data-tct-role-weekbar="1"'; $html .= ' data-role-id="' . esc_attr( (int) $role_id ) . '"'; $html .= ' data-role-name="' . esc_attr( $role_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-weekbar-bar">'; $html .= '<div class="tct-domain-weekbar-strip" style="--tct-domain-weekbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (float) $points_by_day[ $dk ] : 0.0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-weekbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-weekbar-empty'; } else { $alpha = null; if ( is_array( $alpha_by_day ) && isset( $alpha_by_day[ $dk ] ) ) { $alpha = (float) $alpha_by_day[ $dk ]; if ( $alpha < 0.0 ) { $alpha = 0.0; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $class .= ' tct-domain-weekbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } if ( is_array( $parent_points_by_day ) && '' !== $parent_label ) { $parent_pts = isset( $parent_points_by_day[ $dk ] ) ? (float) $parent_points_by_day[ $dk ] : 0.0; $share_pct = 0; if ( $parent_pts > 0.0 ) { $share_pct = (int) round( ( $pts / $parent_pts ) * 100.0 ); if ( $share_pct < 0 ) { $share_pct = 0; } if ( $share_pct > 100 ) { $share_pct = 100; } } else { $share_pct = ( $pts > 0.0 ) ? 100 : 0; } $title .= ' * ' . (int) $share_pct . '% of ' . $parent_label; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '" title="' . esc_attr( $title ) . '"' . $style . '></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-dayticks">'; for ( $i = 0; $i < $days; $i++ ) { $left = ( $days > 0 ) ? ( ( (float) $i / (float) $days ) * 100.0 ) : 0.0; $html .= '<span class="tct-domain-weekbar-day-tick" style="left:' . esc_attr( $left ) . '%"></span>'; } $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-daylabels">'; foreach ( $date_keys as $idx => $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $left = ( $days > 0 ) ? ( ( ( (float) $idx + 0.5 ) / (float) $days ) * 100.0 ) : 0.0; $dow = ''; try { $dt = new DateTimeImmutable( $dk . ' 00:00:00', $tz ); $dow = $dt->format( 'D' ); } catch ( Exception $e ) { $dow = ''; } if ( '' === $dow ) { $dow = 'Day'; } $cls = 'tct-domain-weekbar-day-label'; if ( is_string( $today_ymd ) && '' !== $today_ymd && $dk === $today_ymd ) { $cls .= ' tct-heatmap-current-label'; } $html .= '<span class="' . esc_attr( $cls ) . '" style="left:' . esc_attr( $left ) . '%">' . esc_html( $dow ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_goal_week_spectrum_bar( $goal_id, $goal_name, $date_keys, $points_by_day, $possible_by_day, $today_ymd = '', $alpha_by_day = null, $parent_points_by_day = null, $parent_label = '' ) { $goal_id = (int) $goal_id; $goal_name = is_string( $goal_name ) ? $goal_name : ''; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $alpha_by_day = is_array( $alpha_by_day ) ? $alpha_by_day : null; $parent_points_by_day = is_array( $parent_points_by_day ) ? $parent_points_by_day : null; $parent_label = is_string( $parent_label ) ? trim( $parent_label ) : ''; if ( '' === $parent_label ) { $parent_label = 'Role'; } $days = count( $date_keys ); if ( $days < 1 ) { return ''; } $tz = TCT_Utils::wp_timezone(); $html = '<div class="tct-domain-weekbar tct-goal-weekbar" data-tct-goal-weekbar="1"'; $html .= ' data-goal-id="' . esc_attr( (int) $goal_id ) . '"'; $html .= ' data-goal-name="' . esc_attr( $goal_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-weekbar-bar">'; $html .= '<div class="tct-domain-weekbar-strip" style="--tct-domain-weekbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (float) $points_by_day[ $dk ] : 0.0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-weekbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-weekbar-empty'; } else { $alpha = null; if ( is_array( $alpha_by_day ) && isset( $alpha_by_day[ $dk ] ) ) { $alpha = (float) $alpha_by_day[ $dk ]; if ( $alpha < 0.0 ) { $alpha = 0.0; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $class .= ' tct-domain-weekbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } if ( is_array( $parent_points_by_day ) && '' !== $parent_label ) { $parent_pts = isset( $parent_points_by_day[ $dk ] ) ? (float) $parent_points_by_day[ $dk ] : 0.0; $share_pct = 0; if ( $parent_pts > 0.0 ) { $share_pct = (int) round( ( $pts / $parent_pts ) * 100.0 ); if ( $share_pct < 0 ) { $share_pct = 0; } if ( $share_pct > 100 ) { $share_pct = 100; } } else { $share_pct = ( $pts > 0.0 ) ? 100 : 0; } $title .= ' * ' . (int) $share_pct . '% of ' . $parent_label; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '" title="' . esc_attr( $title ) . '"' . $style . '></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-dayticks">'; for ( $i = 0; $i < $days; $i++ ) { $left = ( $days > 0 ) ? ( ( (float) $i / (float) $days ) * 100.0 ) : 0.0; $html .= '<span class="tct-domain-weekbar-day-tick" style="left:' . esc_attr( $left ) . '%"></span>'; } $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-domain-weekbar-daylabels">'; foreach ( $date_keys as $idx2 => $dk2 ) { $dk2 = is_string( $dk2 ) ? $dk2 : ''; if ( '' === $dk2 ) { continue; } $left = ( $days > 0 ) ? ( ( ( (float) $idx2 + 0.5 ) / (float) $days ) * 100.0 ) : 0.0; $dow = ''; try { $dt = new DateTimeImmutable( $dk2 . ' 00:00:00', $tz ); $dow = $dt->format( 'D' ); } catch ( Exception $e ) { $dow = ''; } if ( '' === $dow ) { $dow = 'Day'; } $cls = 'tct-domain-weekbar-day-label'; if ( is_string( $today_ymd ) && '' !== $today_ymd && $dk2 === $today_ymd ) { $cls .= ' tct-heatmap-current-label'; } $html .= '<span class="' . esc_attr( $cls ) . '" style="left:' . esc_attr( $left ) . '%">' . esc_html( $dow ) . '</span>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domain_month_spectrum_bar( $domain_id, $domain_name, $year, $month, $date_keys, $points_by_day, $possible_by_day ) { $domain_id = (int) $domain_id; $domain_name = is_string( $domain_name ) ? $domain_name : ''; $year = (int) $year; $month = (int) $month; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); $html = '<div class="tct-domain-monthbar" data-tct-domain-monthbar="1" role="button" tabindex="0"'; $html .= ' data-domain-id="' . esc_attr( $domain_id ) . '"'; $html .= ' data-year="' . esc_attr( $year ) . '"'; $html .= ' data-month="' . esc_attr( $month ) . '"'; $html .= ' data-days="' . esc_attr( (int) $days ) . '"'; $html .= ' data-domain-name="' . esc_attr( $domain_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-monthbar-bar">'; $html .= '<div class="tct-domain-monthbar-strip" style="--tct-domain-monthbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-monthbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-monthbar-empty'; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-monthbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-monthbar-weekticks" data-tct-monthbar-weekticks="1"></div>'; $html .= '<div class="tct-domain-monthbar-weeklabels" data-tct-monthbar-weeklabels="1"></div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domain_month_spectrum_bar_static( $domain_id, $domain_name, $year, $month, $date_keys, $points_by_day, $possible_by_day ) { $domain_id = (int) $domain_id; $domain_name = is_string( $domain_name ) ? $domain_name : ''; $year = (int) $year; $month = (int) $month; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $days = count( $date_keys ); $html = '<div class="tct-domain-monthbar tct-domain-monthbar-static" data-tct-domain-monthbar="0"'; $html .= ' data-domain-id="' . esc_attr( $domain_id ) . '"'; $html .= ' data-year="' . esc_attr( $year ) . '"'; $html .= ' data-month="' . esc_attr( $month ) . '"'; $html .= ' data-days="' . esc_attr( (int) $days ) . '"'; $html .= ' data-domain-name="' . esc_attr( $domain_name ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-monthbar-bar">'; $html .= '<div class="tct-domain-monthbar-strip" style="--tct-domain-monthbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( (float) $pts / (float) $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0 ) ? 100 : 0; } $class = 'tct-domain-monthbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0 ) { $class .= ' tct-domain-monthbar-empty'; } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $class .= ' tct-domain-monthbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-monthbar-weekticks" data-tct-monthbar-weekticks="1"></div>'; $html .= '<div class="tct-domain-monthbar-weeklabels" data-tct-monthbar-weeklabels="1"></div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_role_month_spectrum_bar( $role_id, $role_name, $year, $month, $date_keys, $points_by_day, $possible_by_day, $alpha_by_day = null, $parent_points_by_day = null, $parent_label = '' ) { $role_id = (int) $role_id; $role_name = is_string( $role_name ) ? $role_name : ''; $year = (int) $year; $month = (int) $month; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $alpha_by_day = is_array( $alpha_by_day ) ? $alpha_by_day : null; $parent_points_by_day = is_array( $parent_points_by_day ) ? $parent_points_by_day : null; $parent_label = is_string( $parent_label ) ? trim( $parent_label ) : ''; if ( '' === $parent_label ) { $parent_label = 'Domain'; } $days = count( $date_keys ); if ( $days < 1 ) { return ''; } $html = '<div class="tct-domain-monthbar tct-role-monthbar" data-tct-domain-monthbar="0"'; $html .= ' data-role-id="' . esc_attr( (int) $role_id ) . '"'; $html .= ' data-role-name="' . esc_attr( $role_name ) . '"'; $html .= ' data-year="' . esc_attr( (int) $year ) . '"'; $html .= ' data-month="' . esc_attr( (int) $month ) . '"'; $html .= ' data-days="' . esc_attr( (int) $days ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-monthbar-bar">'; $html .= '<div class="tct-domain-monthbar-strip" style="--tct-domain-monthbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (float) $points_by_day[ $dk ] : 0.0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0.0 ) ? 100 : 0; } $class = 'tct-domain-monthbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0.0 ) { $class .= ' tct-domain-monthbar-empty'; } else { $alpha = null; if ( is_array( $alpha_by_day ) && isset( $alpha_by_day[ $dk ] ) ) { $alpha = (float) $alpha_by_day[ $dk ]; if ( $alpha < 0.0 ) { $alpha = 0.0; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $class .= ' tct-domain-monthbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } if ( is_array( $parent_points_by_day ) && '' !== $parent_label ) { $parent_pts = isset( $parent_points_by_day[ $dk ] ) ? (float) $parent_points_by_day[ $dk ] : 0.0; $share_pct = 0; if ( $parent_pts > 0.0 ) { $share_pct = (int) round( ( $pts / $parent_pts ) * 100.0 ); if ( $share_pct < 0 ) { $share_pct = 0; } if ( $share_pct > 100 ) { $share_pct = 100; } } else { $share_pct = ( $pts > 0.0 ) ? 100 : 0; } $title .= ' * ' . (int) $share_pct . '% of ' . $parent_label; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-monthbar-weekticks" data-tct-monthbar-weekticks="1"></div>'; $html .= '<div class="tct-domain-monthbar-weeklabels" data-tct-monthbar-weeklabels="1"></div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_goal_month_spectrum_bar( $goal_id, $goal_name, $year, $month, $date_keys, $points_by_day, $possible_by_day, $alpha_by_day = null, $parent_points_by_day = null, $parent_label = '' ) { $goal_id = (int) $goal_id; $goal_name = is_string( $goal_name ) ? $goal_name : ''; $year = (int) $year; $month = (int) $month; if ( ! is_array( $date_keys ) || empty( $date_keys ) ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } if ( ! is_array( $possible_by_day ) ) { $possible_by_day = array(); } $alpha_by_day = is_array( $alpha_by_day ) ? $alpha_by_day : null; $parent_points_by_day = is_array( $parent_points_by_day ) ? $parent_points_by_day : null; $parent_label = is_string( $parent_label ) ? trim( $parent_label ) : ''; if ( '' === $parent_label ) { $parent_label = 'Role'; } $days = count( $date_keys ); if ( $days < 1 ) { return ''; } $html = '<div class="tct-domain-monthbar tct-goal-monthbar" data-tct-domain-monthbar="0"'; $html .= ' data-goal-id="' . esc_attr( (int) $goal_id ) . '"'; $html .= ' data-goal-name="' . esc_attr( $goal_name ) . '"'; $html .= ' data-year="' . esc_attr( (int) $year ) . '"'; $html .= ' data-month="' . esc_attr( (int) $month ) . '"'; $html .= ' data-days="' . esc_attr( (int) $days ) . '"'; $html .= '>'; $html .= '<div class="tct-domain-monthbar-bar">'; $html .= '<div class="tct-domain-monthbar-strip" style="--tct-domain-monthbar-days:' . esc_attr( (int) $days ) . ';">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; if ( '' === $dk ) { continue; } $pts = isset( $points_by_day[ $dk ] ) ? (float) $points_by_day[ $dk ] : 0.0; $possible = isset( $possible_by_day[ $dk ] ) ? (float) $possible_by_day[ $dk ] : 0.0; $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = ( $pts > 0.0 ) ? 100 : 0; } $class = 'tct-domain-monthbar-seg'; $style = ''; $title = $dk . ' * ' . (int) $pts . ' pts'; if ( $pts <= 0.0 ) { $class .= ' tct-domain-monthbar-empty'; } else { $alpha = null; if ( is_array( $alpha_by_day ) && isset( $alpha_by_day[ $dk ] ) ) { $alpha = (float) $alpha_by_day[ $dk ]; if ( $alpha < 0.0 ) { $alpha = 0.0; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } else { $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $class .= ' tct-domain-monthbar-filled'; $style = ' style="--tct-heat-alpha:' . esc_attr( $alpha ) . ';"'; if ( $possible > 0.0 ) { $title .= ' * ' . (int) $pct . '%'; } if ( is_array( $parent_points_by_day ) && '' !== $parent_label ) { $parent_pts = isset( $parent_points_by_day[ $dk ] ) ? (float) $parent_points_by_day[ $dk ] : 0.0; $share_pct = 0; if ( $parent_pts > 0.0 ) { $share_pct = (int) round( ( $pts / $parent_pts ) * 100.0 ); if ( $share_pct < 0 ) { $share_pct = 0; } if ( $share_pct > 100 ) { $share_pct = 100; } } else { $share_pct = ( $pts > 0.0 ) ? 100 : 0; } $title .= ' * ' . (int) $share_pct . '% of ' . $parent_label; } } $html .= '<span class="' . esc_attr( $class ) . '" data-date="' . esc_attr( $dk ) . '"' . $style . ' title="' . esc_attr( $title ) . '"></span>'; } $html .= '</div>'; $html .= '<div class="tct-domain-monthbar-weekticks" data-tct-monthbar-weekticks="1"></div>'; $html .= '<div class="tct-domain-monthbar-weeklabels" data-tct-monthbar-weeklabels="1"></div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function get_month_grid_template_for_year( $year ) { $year = (int) $year; $tz = TCT_Utils::wp_timezone(); $cols = array(); for ( $m = 1; $m <= 12; $m++ ) { try { $dt = new DateTimeImmutable( sprintf( '%04d-%02d-01 00:00:00', $year, $m ), $tz ); } catch ( Exception $e ) { continue; } $days = (int) $dt->format( 't' ); if ( $days < 1 ) { $days = 30; } $cols[] = $days . 'fr'; } if ( empty( $cols ) ) { return '1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr'; } return implode( ' ', $cols ); } private function render_domain_points_heatmap_strip( $domain_id, $date_keys, $points_by_day, $max_points ) { $domain_id = (int) $domain_id; $max_points = (int) $max_points; if ( ! is_array( $date_keys ) ) { $date_keys = array(); } $days = count( $date_keys ); if ( $days <= 0 ) { return ''; } if ( ! is_array( $points_by_day ) ) { $points_by_day = array(); } $html = '<div class="tct-domain-heatmap" data-tct-domain-heatmap="1" data-domain-id="' . esc_attr( (int) $domain_id ) . '">'; $html .= '<div class="tct-domain-heatmap-grid" style="--tct-domain-heatmap-days:' . esc_attr( (int) $days ) . ';" data-tct-domain-heatmap-grid="1" data-domain-id="' . esc_attr( (int) $domain_id ) . '">'; foreach ( $date_keys as $dk ) { $dk = is_string( $dk ) ? $dk : ''; $pts = isset( $points_by_day[ $dk ] ) ? (int) $points_by_day[ $dk ] : 0; $level = 0; if ( $pts > 0 && $max_points > 0 ) { $ratio = (float) $pts / (float) $max_points; $level = (int) ceil( $ratio * 4 ); if ( $level < 1 ) { $level = 1; } if ( $level > 4 ) { $level = 4; } } $title = $dk . ' * ' . ( $pts > 0 ? ( (string) $pts . ' pts' ) : '0 pts' ); $html .= '<div class="tct-domain-heatmap-cell tct-domain-heatmap-l' . esc_attr( (int) $level ) . '"' . ' title="' . esc_attr( $title ) . '"' . ' data-date="' . esc_attr( $dk ) . '"' . ' data-points="' . esc_attr( (int) $pts ) . '"' . '></div>'; } $html .= '</div>'; $html .= '</div>'; return $html; } private function get_label_names_for_picker( $user_id ) { global $wpdb; $completions_table = TCT_DB::table_completions(); $labels = TCT_Sync::get_cached_labels( $user_id ); $label_names = array(); foreach ( $labels as $l ) { if ( isset( $l['name'] ) ) { $label_names[] = (string) $l['name']; } } $db_labels = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT label_name FROM {$completions_table} WHERE user_id = %d AND label_name IS NOT NULL AND label_name <> '' ORDER BY label_name ASC", $user_id ) ); foreach ( $db_labels as $name ) { $label_names[] = (string) $name; } $label_names = array_values( array_unique( array_filter( array_map( 'sanitize_text_field', $label_names ) ) ) ); sort( $label_names ); return $label_names; } private function get_domains( $user_id ) { global $wpdb; $domains_table = TCT_DB::table_domains(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, domain_name, color_hex, sort_order FROM {$domains_table} WHERE user_id = %d ORDER BY sort_order ASC, domain_name ASC", $user_id ), ARRAY_A ); if ( ! is_array( $rows ) ) { return array(); } return $rows; } private function get_domain_map( $domains ) { $map = array(); foreach ( $domains as $d ) { if ( isset( $d['id'] ) && isset( $d['domain_name'] ) ) { $map[ (int) $d['id'] ] = (string) $d['domain_name']; } } return $map; } private function get_domain_color_map( $domains ) { $map = array(); foreach ( $domains as $d ) { if ( ! isset( $d['id'] ) ) { continue; } $id = (int) $d['id']; if ( $id <= 0 ) { continue; } $color = isset( $d['color_hex'] ) ? sanitize_hex_color( $d['color_hex'] ) : ''; if ( $color ) { $map[ $id ] = $color; } } return $map; } private function suggest_domain_color_from_domains( $domains ) { $palette = TCT_DB::default_domain_palette(); if ( ! is_array( $palette ) || empty( $palette ) ) { return '#2271b1'; } $used = array(); foreach ( $domains as $d ) { $color = isset( $d['color_hex'] ) ? sanitize_hex_color( $d['color_hex'] ) : ''; if ( $color ) { $used[ strtolower( $color ) ] = true; } } foreach ( $palette as $color ) { if ( ! isset( $used[ strtolower( $color ) ] ) ) { return $color; } } return (string) $palette[0]; } private function get_roles( $user_id ) { global $wpdb; $roles_table = TCT_DB::table_roles(); $domains_table = TCT_DB::table_domains(); $rows = $wpdb->get_results( $wpdb->prepare( "SELECT r.id, r.domain_id, r.role_name, r.sort_order, d.domain_name, d.sort_order AS domain_sort
                 FROM {$roles_table} r
                 LEFT JOIN {$domains_table} d ON r.domain_id = d.id AND d.user_id = r.user_id
                 WHERE r.user_id = %d
                 ORDER BY COALESCE(d.sort_order, 999999) ASC, r.sort_order ASC, r.role_name ASC", $user_id ), ARRAY_A ); if ( ! is_array( $rows ) ) { return array(); } return $rows; } private function get_role_map( $roles ) { $map = array(); foreach ( $roles as $r ) { if ( isset( $r['id'] ) && isset( $r['role_name'] ) ) { $map[ (int) $r['id'] ] = array( 'id' => (int) $r['id'], 'domain_id' => isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0, 'role_name' => (string) $r['role_name'], 'sort_order' => isset( $r['sort_order'] ) ? (int) $r['sort_order'] : 0, ); } } return $map; } private function group_roles_by_domain( $roles ) { $out = array(); foreach ( $roles as $r ) { $did = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; if ( ! isset( $out[ $did ] ) ) { $out[ $did ] = array(); } $out[ $did ][] = $r; } return $out; } private function render_settings( $user_id, $redirect_here ) { global $wpdb; $goals_table = TCT_DB::table_goals(); $label_names = $this->get_label_names_for_picker( $user_id ); $domains = $this->get_domains( $user_id ); $domain_map = $this->get_domain_map( $domains ); $domain_color_map = $this->get_domain_color_map( $domains ); $roles = $this->get_roles( $user_id ); $role_map = $this->get_role_map( $roles ); $aliases_select = $this->tct_goal_aliases_select_sql(); $link_select = $this->tct_goal_link_url_select_sql(); $notes_select = $this->tct_goal_notes_select_sql(); $due_schedule_select = $this->tct_goal_due_schedule_select_sql(); $availability_cycle_select = $this->tct_goal_availability_cycle_select_sql(); $interval_anchor_select = $this->tct_goal_interval_anchor_select_sql(); $composite_config_select = $this->tct_goal_composite_config_select_sql(); $wake_select = $this->tct_goal_wake_time_select_sql(); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, tracking_mode, label_name, goal_name, {$aliases_select}, {$link_select}, {$notes_select}, {$due_schedule_select}, {$availability_cycle_select}, {$interval_anchor_select}, {$composite_config_select}, plant_name, goal_type, threshold, timer_duration_seconds, alarm_sound, alarm_duration, alarm_vibration, visible_after_time, sleep_tracking_enabled, sleep_rollover_time, {$wake_select}, fail_button_enabled, is_favorite, domain_id, role_id, target, period_unit, period_span, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, points_per_completion, importance, effort, points_enabled_at, created_at, updated_at
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1
                 ORDER BY goal_name ASC", $user_id ), ARRAY_A ); $role_domain_map = array(); foreach ( $roles as $r ) { if ( ! isset( $r['id'] ) || ! isset( $r['domain_id'] ) ) { continue; } $rid = (int) $r['id']; $did = (int) $r['domain_id']; if ( $rid > 0 ) { $role_domain_map[ $rid ] = $did; } } $stats_domains = array(); foreach ( $domains as $d ) { if ( ! isset( $d['id'] ) || ! isset( $d['domain_name'] ) ) { continue; } $did = (int) $d['id']; if ( $did <= 0 ) { continue; } $stats_domains[ $did ] = array( 'name' => (string) $d['domain_name'], 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } $stats_roles = array(); foreach ( $roles as $r ) { if ( ! isset( $r['id'] ) || ! isset( $r['role_name'] ) ) { continue; } $rid = (int) $r['id']; if ( $rid <= 0 ) { continue; } $did = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; $stats_roles[ $rid ] = array( 'name' => (string) $r['role_name'], 'domain_id'=> $did, 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } foreach ( $goal_rows as $gr ) { $rid = isset( $gr['role_id'] ) ? (int) $gr['role_id'] : 0; $did = isset( $gr['domain_id'] ) ? (int) $gr['domain_id'] : 0; if ( $rid > 0 && isset( $role_map[ $rid ] ) ) { $did = (int) $role_map[ $rid ]['domain_id']; } if ( $did <= 0 || $rid <= 0 ) { continue; } if ( ! isset( $stats_domains[ $did ] ) ) { $stats_domains[ $did ] = array( 'name' => isset( $domain_map[ $did ] ) ? (string) $domain_map[ $did ] : 'Domain', 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } if ( ! isset( $stats_roles[ $rid ] ) ) { $stats_roles[ $rid ] = array( 'name' => 'Role', 'domain_id' => $did, 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } $importance_val = isset( $gr['importance'] ) ? (int) $gr['importance'] : 0; $stats_domains[ $did ]['total']++; $stats_roles[ $rid ]['total']++; if ( 5 === $importance_val ) { $stats_domains[ $did ]['i5']++; $stats_roles[ $rid ]['i5']++; } if ( $importance_val >= 4 ) { $stats_domains[ $did ]['i4plus']++; $stats_roles[ $rid ]['i4plus']++; } } $goal_modal_stats = array( 'roleDomainMap' => $role_domain_map, 'domains' => $stats_domains, 'roles' => $stats_roles, 'thresholds' => array( 'i5Count' => 4, 'i5Pct' => 0.3, 'i4Pct' => 0.6, ), ); $html = '<div class="tct-card">'; $html .= '<div class="tct-goals-header">'; $html .= '<h3>Goals</h3>'; $html .= '<div class="tct-goals-actions"><button type="button" class="button button-primary" data-tct-open-goal-modal="add">Add goal</button></div>'; $html .= '</div>'; $html .= '<p class="tct-muted">Goals can be <strong>Todoist</strong>, <strong>manual</strong>, or <strong>hybrid</strong>. Manual goals don\'t require a label; Todoist/Hybrid goals use a label for import + best-effort task closing.</p>'; if ( empty( $goal_rows ) ) { $html .= '<p class="tct-muted">No goals yet. Click "Add goal".</p>'; } else { $html .= '<div class="tct-table-wrap"><table class="tct-table tct-goals-table" data-tct-goals-table>'; $html .= '<thead><tr><th>Goal</th><th class="tct-sortable" data-tct-goals-sort="domain" aria-sort="none">Domain</th><th class="tct-sortable" data-tct-goals-sort="role" aria-sort="none">Role</th><th>Type</th><th class="tct-col-ppc">Task points</th><th class="tct-col-bonus">Bonus / Penalty</th><th>Target</th><th>Actions</th></tr></thead>'; $html .= '<tbody>'; foreach ( $goal_rows as $row ) { $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0; if ( $goal_id <= 0 ) { continue; } $tracking_mode = isset( $row['tracking_mode'] ) ? (string) $row['tracking_mode'] : 'todoist'; if ( ! in_array( $tracking_mode, array( 'todoist', 'manual', 'hybrid' ), true ) ) { $tracking_mode = 'todoist'; } $label = isset( $row['label_name'] ) && is_string( $row['label_name'] ) ? (string) $row['label_name'] : ''; $goal_name = isset( $row['goal_name'] ) ? (string) $row['goal_name'] : ''; if ( '' === $goal_name ) { $goal_name = '' !== $label ? $label : 'Goal'; } $domain_id = isset( $row['domain_id'] ) ? (int) $row['domain_id'] : 0; $role_id = isset( $row['role_id'] ) ? (int) $row['role_id'] : 0; if ( $role_id > 0 && isset( $role_map[ $role_id ] ) ) { $domain_id = (int) $role_map[ $role_id ]['domain_id']; } $domain_name = $domain_id > 0 && isset( $domain_map[ $domain_id ] ) ? (string) $domain_map[ $domain_id ] : ''; $role_name = $role_id > 0 && isset( $role_map[ $role_id ] ) ? (string) $role_map[ $role_id ]['role_name'] : ''; $intervals = $this->intervals_from_goal_row( $row ); if ( empty( $intervals ) ) { continue; } $payload_intervals = $intervals; if ( TCT_Utils::is_positive_no_interval_goal_type( isset( $row['goal_type'] ) ? (string) $row['goal_type'] : '' ) ) { $payload_intervals = array(); } $payload = array( 'goal_id' => $goal_id, 'tracking_mode' => $tracking_mode, 'label_name' => $label, 'goal_name' => $goal_name, 'aliases' => $this->aliases_from_goal_row( $row ), 'link_url' => $this->link_url_from_goal_row( $row ), 'goal_notes' => $this->goal_notes_from_goal_row( $row ), 'plant_name' => isset( $row['plant_name'] ) && is_string( $row['plant_name'] ) ? (string) $row['plant_name'] : '', 'goal_type' => isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive', 'threshold' => isset( $row['threshold'] ) && is_numeric( $row['threshold'] ) ? (int) $row['threshold'] : null, 'timer_duration_seconds' => isset( $row['timer_duration_seconds'] ) ? (int) $row['timer_duration_seconds'] : 0, 'alarm_sound' => isset( $row['alarm_sound'] ) && is_string( $row['alarm_sound'] ) ? (string) $row['alarm_sound'] : '', 'alarm_duration' => isset( $row['alarm_duration'] ) ? (int) $row['alarm_duration'] : 0, 'alarm_vibration' => isset( $row['alarm_vibration'] ) ? (int) $row['alarm_vibration'] : 0, 'visible_after_time' => isset( $row['visible_after_time'] ) && is_string( $row['visible_after_time'] ) ? (string) $row['visible_after_time'] : '', 'domain_id' => $domain_id, 'role_id' => $role_id, 'points_per_completion' => isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0, 'importance' => isset( $row['importance'] ) ? (int) $row['importance'] : 0, 'effort' => isset( $row['effort'] ) ? (int) $row['effort'] : 0, 'points_enabled_at' => isset( $row['points_enabled_at'] ) ? (string) $row['points_enabled_at'] : '', 'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0, 'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '', 'wake_time_enabled' => isset( $row['wake_time_enabled'] ) ? (int) $row['wake_time_enabled'] : 0, 'wake_time_target' => isset( $row['wake_time_target'] ) && is_string( $row['wake_time_target'] ) ? (string) $row['wake_time_target'] : '', 'bed_time_enabled' => isset( $row['bed_time_enabled'] ) ? (int) $row['bed_time_enabled'] : 0, 'bed_time_target' => isset( $row['bed_time_target'] ) && is_string( $row['bed_time_target'] ) ? (string) $row['bed_time_target'] : '', 'fail_button_enabled' => isset( $row['fail_button_enabled'] ) ? (int) $row['fail_button_enabled'] : 0, 'is_favorite' => isset( $row['is_favorite'] ) ? (int) $row['is_favorite'] : 0, 'allowed_fails_target' => isset( $row['allowed_fails_target'] ) ? (int) $row['allowed_fails_target'] : 0, 'allowed_fails_unit' => isset( $row['allowed_fails_unit'] ) ? (string) $row['allowed_fails_unit'] : 'week', 'allowed_fails_span' => isset( $row['allowed_fails_span'] ) ? (int) $row['allowed_fails_span'] : 1, 'availability_cycle_json' => isset( $row['availability_cycle_json'] ) && is_string( $row['availability_cycle_json'] ) ? (string) $row['availability_cycle_json'] : '', 'interval_anchor_json' => isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ? (string) $row['interval_anchor_json'] : '', 'composite_config_json' => isset( $row['composite_config_json'] ) && is_string( $row['composite_config_json'] ) ? (string) $row['composite_config_json'] : '', 'intervals' => $payload_intervals, ); $payload_json = wp_json_encode( $payload ); $html .= '<tr class="tct-goal-row" data-goal-name="' . esc_attr( $goal_name ) . '" data-domain-name="' . esc_attr( $domain_name ) . '" data-role-name="' . esc_attr( $role_name ) . '">'; $html .= '<td><strong>' . esc_html( $goal_name ) . '</strong>'; $tracking_label = ucfirst( $tracking_mode ); $html .= '<div class="tct-muted">Tracking: ' . esc_html( $tracking_label ) . '</div>'; if ( '' !== $label ) { $html .= '<div class="tct-muted">Label: ' . esc_html( $label ) . '</div>'; } else { $html .= '<div class="tct-muted">Label: <span class="tct-muted">--</span></div>'; } $importance_val = isset( $row['importance'] ) ? (int) $row['importance'] : 0; $effort_val = isset( $row['effort'] ) ? (int) $row['effort'] : 0; if ( $importance_val > 0 && $effort_val > 0 ) { $html .= '<div class="tct-muted">Weight: <strong>I' . esc_html( $importance_val ) . '</strong> / <strong>E' . esc_html( $effort_val ) . '</strong></div>'; } else { $html .= '<div class="tct-muted">Weight: <span class="tct-muted">not set</span></div>'; } $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0; if ( $ppc < 0 ) { $ppc = 0; } $html .= '</td>'; $domain_color = $domain_id > 0 && isset( $domain_color_map[ $domain_id ] ) ? (string) $domain_color_map[ $domain_id ] : ''; $dot_color = '' !== $domain_color ? $domain_color : '#8c8f94'; $domain_badge = '<span class="tct-domain-badge">'; $domain_badge .= '<span class="tct-domain-dot" style="background-color:' . esc_attr( $dot_color ) . '"></span>'; if ( '' !== $domain_name ) { $domain_badge .= '<strong>' . esc_html( $domain_name ) . '</strong>'; } else { $domain_badge .= '<span class="tct-muted">Unassigned</span>'; } $domain_badge .= '</span>'; $html .= '<td>' . $domain_badge . '</td>'; $html .= '<td>'; if ( '' !== $role_name ) { $html .= '<strong>' . esc_html( $role_name ) . '</strong>'; } else { $html .= '<span class="tct-muted">Unassigned</span>'; } $html .= '</td>'; $goal_type_val = isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive'; $threshold_val = isset( $row['threshold'] ) && is_numeric( $row['threshold'] ) ? (int) $row['threshold'] : null; $is_negative_goal = TCT_Utils::is_negative_goal_type( $goal_type_val ); $html .= '<td>'; if ( 'never' === $goal_type_val ) { $html .= '<span class="tct-goal-type-chip tct-goal-type-never">Never</span>'; } elseif ( 'harm_reduction' === $goal_type_val ) { $html .= '<span class="tct-goal-type-chip tct-goal-type-harm-reduction">Limit</span>'; } else { $is_no_interval_positive = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( $goal_type_val ) : ( 'positive_no_int' === $goal_type_val ); if ( $is_no_interval_positive ) { $html .= '<span class="tct-goal-type-chip tct-goal-type-positive">Points</span>'; } else { $html .= '<span class="tct-goal-type-chip tct-goal-type-positive">Build</span>'; } } $html .= '</td>'; $html .= '<td class="tct-col-ppc">'; if ( $ppc > 0 ) { $html .= '<span class="tct-points-chip">' . esc_html( $ppc ) . '</span>'; } else { $html .= '<span class="tct-muted">--</span>'; } $html .= '</td>'; $html .= '<td class="tct-col-bonus">'; if ( $ppc > 0 ) { $bonus_chips = array(); foreach ( $intervals as $interval ) { $interval_target = isset( $interval['target'] ) ? (int) $interval['target'] : 0; $bonus_points = 0; $penalty_points = 0; $is_anki_cards_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type ) : ( 'anki_cards' === strtolower( trim( (string) $goal_type ) ) ); if ( $interval_target > 0 ) { if ( $is_anki_cards_goal ) { $bonus_points = (int) $ppc; $penalty_points = -1 * abs( (int) $ppc ); } else { $bonus_points = (int) TCT_Utils::compute_bonus_points( $ppc, $interval_target ); $penalty_points = (int) TCT_Utils::compute_penalty_points( $ppc, $interval_target, 0 ); } } if ( 0 !== (int) $bonus_points || 0 !== (int) $penalty_points ) { $bonus_chips[] = '<span class="tct-bonus-chip">+' . esc_html( (int) $bonus_points ) . '/' . esc_html( (int) $penalty_points ) . '/' . esc_html( $interval['period_unit'] ) . '</span>'; }} if ( ! empty( $bonus_chips ) ) { $html .= '<div class="tct-bonus-chips">' . implode( '', $bonus_chips ) . '</div>'; } else { $html .= '<span class="tct-muted">--</span>'; } } else { $html .= '<span class="tct-muted">--</span>'; } $html .= '</td>'; $html .= '<td>'; $html .= '<ul class="tct-interval-list">'; $is_no_interval_positive_goal = false; if ( ! $is_negative_goal ) { $is_no_interval_positive_goal = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( $goal_type_val ) : ( 'positive_no_int' === $goal_type_val ); } if ( $is_no_interval_positive_goal ) { $html .= '<li><span class="tct-interval-chip">No interval target</span></li>'; } else { foreach ( $intervals as $interval ) { if ( $is_negative_goal ) { if ( 'never' === $goal_type_val ) { $html .= '<li><span class="tct-interval-chip"><strong>Never</strong> per <strong>' . esc_html( $interval['period_unit'] ) . '</strong>'; } else { $th_display = ( null !== $threshold_val ) ? (int) $threshold_val : 0; $html .= '<li><span class="tct-interval-chip">Max <strong>' . esc_html( $th_display ) . '</strong> per <strong>' . esc_html( $interval['period_unit'] ) . '</strong>'; } } else { $html .= '<li><span class="tct-interval-chip">At least <strong>' . esc_html( (int) $interval['target'] ) . '</strong> per <strong>' . esc_html( $interval['period_unit'] ) . '</strong>'; } $html .= '</span></li>'; } } $html .= '</ul>'; $html .= '</td>'; $html .= '<td>'; $html .= '<div class="tct-goal-actions">'; $html .= '<button type="button" class="button" data-tct-open-goal-modal="edit" data-tct-goal="' . esc_attr( $payload_json ) . '">Edit</button> '; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="tct-inline-form" data-tct-confirm="Delete this goal?">'; $html .= '<input type="hidden" name="action" value="tct_goal_delete" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="goal_id" value="' . esc_attr( (int) $goal_id ) . '" />'; $html .= wp_nonce_field( 'tct_goal_delete', '_wpnonce', true, false ); $html .= '<button type="submit" class="button">Delete</button>'; $html .= '</form>'; $html .= '</div>'; $html .= '</td>'; $html .= '</tr>'; } $html .= '</tbody></table></div>'; } $html .= '</div>'; $html .= $this->render_goal_modal( $label_names, $domains, $roles, $redirect_here, $goal_modal_stats ); $html .= $this->render_goal_history_modal(); $html .= $this->render_domain_heatmap_modal(); return $html; } private function render_goal_modal( $label_names, $domains, $roles, $redirect_here, $goal_modal_stats ) { $roles_by_domain = $this->group_roles_by_domain( $roles ); $domain_map = $this->get_domain_map( $domains ); $role_options = '<option value="0">Unassigned</option>'; foreach ( $domains as $d ) { $did = isset( $d['id'] ) ? (int) $d['id'] : 0; $dname = isset( $domain_map[ $did ] ) ? (string) $domain_map[ $did ] : ''; if ( $did <= 0 || '' === $dname ) { continue; } if ( ! isset( $roles_by_domain[ $did ] ) || empty( $roles_by_domain[ $did ] ) ) { continue; } $role_options .= '<optgroup label="' . esc_attr( $dname ) . '">'; foreach ( $roles_by_domain[ $did ] as $r ) { $rid = isset( $r['id'] ) ? (int) $r['id'] : 0; $rnm = isset( $r['role_name'] ) ? (string) $r['role_name'] : ''; if ( $rid > 0 && '' !== $rnm ) { $role_options .= '<option value="' . esc_attr( $rid ) . '">' . esc_html( $rnm ) . '</option>'; } } $role_options .= '</optgroup>'; } $allowed_units = array( 'hour' => 'Hour', 'day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'quarter' => 'Quarter', 'semiannual' => 'Semiannual', 'year' => 'Year', ); $allowed_modes = array( 'calendar' => 'Calendar', ); $unit_options = ''; foreach ( $allowed_units as $k => $label ) { $unit_options .= '<option value="' . esc_attr( $k ) . '">' . esc_html( $label ) . '</option>'; } $mode_options = ''; foreach ( $allowed_modes as $k => $label ) { $mode_options .= '<option value="' . esc_attr( $k ) . '">' . esc_html( $label ) . '</option>'; } $html = ''; $html .= '<div class="tct-modal-overlay" data-tct-goal-overlay hidden="hidden"></div>'; $html .= '<div class="tct-modal" data-tct-goal-modal hidden="hidden" role="dialog" aria-modal="true" aria-labelledby="tct-goal-modal-title">'; $html .= '<div class="tct-modal-inner">'; $html .= '<div class="tct-modal-header">'; $html .= '<h3 id="tct-goal-modal-title" data-tct-modal-title>Add goal</h3>'; $html .= '<button type="button" class="tct-modal-close" data-tct-modal-close aria-label="Close">&times;</button>'; $html .= '</div>'; $html .= '<div class="tct-modal-body">'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" data-tct-goal-form>'; $html .= '<input type="hidden" name="action" value="tct_goal_upsert" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="intervals_json" value="" data-tct-intervals-json />'; $html .= '<input type="hidden" name="tct_goal_form_mode" value="add" data-tct-goal-form-mode />'; $html .= '<input type="hidden" name="goal_id" value="0" data-tct-goal-id />'; $html .= wp_nonce_field( 'tct_goal_upsert', '_wpnonce', true, false ); $html .= '<input type="hidden" name="threshold" value="" data-tct-threshold-input />'; $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-goal-name">Goal name</label>'; $html .= '<input id="tct-goal-name" type="text" name="goal_name" value="" placeholder="e.g., Call Mom" data-tct-goal-name />'; $html .= '</div>'; $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-goal-notes">Goal purpose / why it matters (optional)</label>'; $html .= '<textarea id="tct-goal-notes" name="goal_notes" rows="4" placeholder="Outline the goal, the purpose, and why it is important to you..." data-tct-goal-notes></textarea>'; $html .= '</div>'; $html .= '<div class="tct-form-row tct-aliases-row" data-tct-aliases-row>'; $html .= '<label class="tct-label">Aliases</label>'; $html .= '<div class="tct-aliases-controls">'; $html .= '<button type="button" class="button" data-tct-suggest-aliases="1" data-tct-suggest-aliases-nonce="' . esc_attr( wp_create_nonce( 'tct_suggest_aliases' ) ) . '">Suggest aliases</button> '; $html .= '<button type="button" class="button" data-tct-add-alias="1">Add alias</button>'; $html .= '</div>'; $html .= '<div class="tct-aliases-list" data-tct-aliases-list></div>'; $html .= '<div class="tct-muted tct-settings-help" style="margin-top:6px;">Aliases help search match small wording differences (e.g. Run vs Ran). Saved when you save the goal.</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-goal-link-row>'; $html .= '<label class="tct-label" for="tct-goal-link-url">Add link</label>'; $html .= '<input id="tct-goal-link-url" type="text" name="link_url" value="" placeholder="https://yourdomain/listen/open" inputmode="url" autocomplete="url" autocapitalize="off" spellcheck="false" data-tct-goal-link-url />'; $html .= '<div class="tct-muted tct-settings-help" style="margin-top:6px;">If set, mobile will immediately navigate to this URL after a successful completion. For Android Chrome, use your verified HTTPS App Link URL here (for example, https://yourdomain/listen/open). Chrome blocks direct Activity/component launches, so use the HTTPS App Link entry URL instead. Use only one: a link, a call number, or a text number.</div>'; $html .= '<details class="tct-goal-link-macrodroid-help" style="margin-top:8px;"><summary style="cursor:pointer;">MacroDroid reminder (for apps without deep links)</summary><div class="tct-muted" style="margin-top:6px;"><ol style="margin:6px 0 0 18px; padding:0;"><li>In MacroDroid, tap <strong>Add Macro</strong>.</li><li>Tap <strong>Add Trigger &rarr; Connectivity &rarr; Webhook (URL)</strong> and choose any identifier.</li><li>Tap <strong>Add Action &rarr; Applications &rarr; Launch Application</strong> and pick the app.</li><li>Copy the full webhook URL and paste it here. After completion, mobile opens the webhook and MacroDroid opens the app.</li></ol></div></details>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-goal-phone-row>'; $html .= '<label class="tct-label" for="tct-goal-phone-number">Call number</label>'; $html .= '<input id="tct-goal-phone-number" type="tel" name="phone_number" value="" placeholder="(555) 123-4567" inputmode="numeric" autocomplete="tel-national" data-tct-goal-phone-number />'; $html .= '<div class="tct-muted tct-settings-help" style="margin-top:6px;">Enter a 10-digit phone number. Spaces, dashes, and parentheses are okay. Mobile will open the phone app with this number after completion.</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-goal-sms-row>'; $html .= '<label class="tct-label" for="tct-goal-sms-number">Text number</label>'; $html .= '<input id="tct-goal-sms-number" type="tel" name="sms_number" value="" placeholder="(555) 123-4567" inputmode="numeric" autocomplete="tel-national" data-tct-goal-sms-number />'; $html .= '<div class="tct-muted tct-settings-help" style="margin-top:6px;">Enter a 10-digit phone number. Spaces, dashes, and parentheses are okay. Mobile will open the messaging app with this number after completion.</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-goal-type-row>'; $html .= '<label class="tct-label" for="tct-goal-type">Goal type</label>'; $html .= '<select id="tct-goal-type" name="goal_type" data-tct-goal-type-select>'; $html .= '<option value="positive" selected>Positive goal with interval</option>'; $html .= '<option value="positive_no_int">Positive goal without interval</option>'; $html .= '<option value="anki_cards">Anki cards goal</option>'; $html .= '<option value="never">Never goal</option>'; $html .= '<option value="harm_reduction">Harm reduction goal</option>'; if ( $this->tct_composite_goals_enabled() ) { $html .= '<option value="' . esc_attr( $this->tct_composite_goal_type_slug() ) . '">Composite parent goal</option>'; } $html .= '</select>'; $html .= '<p class="tct-muted" data-tct-goal-type-hint>Positive goal with interval: earn points for each completion, bonus if you hit your target.</p>'; $html .= '</div>'; $html .= '<input type="hidden" name="tracking_mode" value="manual" />'; $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-goal-role">Role</label>'; $html .= '<select id="tct-goal-role" name="role_id" data-tct-role-select>'; $html .= $role_options; $html .= '</select>'; $html .= '<p class="tct-muted">Pick a role for this goal. The domain is inferred from the role.</p>'; $html .= '</div>'; $html .= $this->tct_render_composite_goal_modal_section(); $goal_modal_stats = $this->tct_compose_goal_modal_stats( $goal_modal_stats, $roles, $domains ); $stats_json_attr = ''; if ( is_array( $goal_modal_stats ) ) { $stats_json_attr = esc_attr( wp_json_encode( $goal_modal_stats ) ); } $html .= '<div hidden="hidden" data-tct-goal-modal-stats="' . $stats_json_attr . '"></div>'; $html .= $this->tct_render_composite_goal_modal_scaffold(); $importance_labels = array( 1 => 'Organize desk drawer', 2 => 'Try a new podcast episode', 3 => 'Read 10 pages of a philosophy book', 4 => 'Deep work block', 5 => 'Take insulin', ); $importance_options = ''; $importance_options .= '<option value="0">Not scored</option>'; for ( $i = 1; $i <= 5; $i++ ) { $opt_label = (string) $i; if ( isset( $importance_labels[ $i ] ) ) { $opt_label .= ' - ' . (string) $importance_labels[ $i ]; } $importance_options .= '<option value="' . esc_attr( (string) $i ) . '">' . esc_html( $opt_label ) . '</option>'; } $effort_labels = array( 1 => 'no resistance, very low energy', 2 => 'low resistance, low energy', 3 => 'moderate resistance, moderate energy', 4 => 'high resistance, high energy', 5 => 'very high resistance, very high energy', ); $effort_options = ''; $effort_options .= '<option value="0">Not scored</option>'; for ( $i = 1; $i <= 5; $i++ ) { $opt_label = (string) $i; if ( isset( $effort_labels[ $i ] ) ) { $opt_label .= ' - ' . (string) $effort_labels[ $i ]; } $effort_options .= '<option value="' . esc_attr( (string) $i ) . '">' . esc_html( $opt_label ) . '</option>'; } $html .= '<div class="tct-form-row" data-tct-goal-scoring-row>'; $html .= '<div class="tct-form-row-head">Scoring</div>'; $html .= '<div class="tct-goal-weight-grid">'; $html .= '<div class="tct-goal-weight-field">'; $html .= '<label class="tct-label" for="tct-goal-importance">Importance (1-5)</label>'; $html .= '<select id="tct-goal-importance" name="importance" data-tct-importance-select>'; $html .= $importance_options; $html .= '</select>'; $html .= '<p class="tct-muted">Examples: 1 = Organize desk drawer; 5 = Take insulin.</p>'; $html .= '</div>'; $html .= '<div class="tct-goal-weight-field">'; $html .= '<label class="tct-label" for="tct-goal-effort">Effort (1-5)</label>'; $html .= '<select id="tct-goal-effort" name="effort" data-tct-effort-select>'; $html .= $effort_options; $html .= '</select>'; $html .= '<p class="tct-muted">Effort = emotional resistance + energy required. 5 = very high; 1 = low.</p>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-muted">Points per completion (auto): <strong><span data-tct-points-preview>0</span></strong></div>'; $html .= '<div class="tct-goal-warning" data-tct-importance-warning hidden="hidden"></div>'; $html .= '<p class="tct-muted">Bonus/penalty is calculated automatically for each loop and applied at the end of the loop: added if met, subtracted if missed. Scoring begins when you save the goal (not retroactive).</p>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-interval-row-container>'; $html .= '<div class="tct-form-row-head" data-tct-interval-heading>Tracking Period</div>'; $html .= '<div class="tct-intervals" data-tct-intervals></div>'; $html .= '<p class="tct-muted" data-tct-interval-hint>Bonus/penalty is calculated automatically per interval and applied at the end of each interval.</p>'; $html .= '</div>';
        $html .= '<div class="tct-form-row" data-tct-due-schedule-row>';
        $html .= '<div class="tct-form-row-head">Due schedule <span class="tct-muted">(optional)</span></div>';
        $html .= '<div class="tct-form-row-body">';
        $html .= '<div class="tct-interval-row tct-due-schedule-grid">';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Enabled</label><select name="due_schedule_enabled" data-tct-due-schedule-enabled><option value="0">Off</option><option value="1">On</option></select></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Start date</label><input type="date" name="due_schedule_start_date" value="' . esc_attr( current_time( 'Y-m-d' ) ) . '" data-tct-due-schedule-start /></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Type</label><select name="due_schedule_type" data-tct-due-schedule-type><option value="weekly">Weekly</option><option value="monthly">Monthly</option></select></div>';
        $html .= '<div class="tct-interval-field">';
        $html .= '<div data-tct-due-schedule-weekly><label class="tct-label">Every</label><input type="number" min="1" max="52" step="1" name="due_schedule_every_n" value="1" data-tct-due-schedule-every /><div class="tct-muted">weeks (weekday is based on start date)</div></div>';
        $html .= '<div data-tct-due-schedule-monthly hidden="hidden"><label class="tct-label">Day of month</label><input type="number" min="1" max="31" step="1" name="due_schedule_day_of_month" value="' . esc_attr( current_time( 'j' ) ) . '" data-tct-due-schedule-dom /><div class="tct-muted">If the day does not exist (e.g. 31st), it uses the last day of the month.</div></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<p class="tct-muted tct-settings-help">When enabled, this goal is only actionable on due days. Not-due days are disabled and do not count as misses. Schedule changes apply forward-only from today.</p>';
        $html .= '<div class="tct-goal-warning" data-tct-due-schedule-warning hidden="hidden"></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="tct-form-row" data-tct-availability-row hidden="hidden" data-tct-availability-today="' . esc_attr( current_time( 'Y-m-d' ) ) . '">';
        $html .= '<div class="tct-form-row-head">Active / Pause cycle <span class="tct-muted">(optional)</span></div>';
        $html .= '<div class="tct-form-row-body">';
        $html .= '<div class="tct-interval-row tct-availability-cycle-toggle-grid">';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Enabled</label><select name="availability_cycle_enabled" data-tct-availability-enabled><option value="0">Off</option><option value="1">On</option></select></div>';
        $html .= '<div class="tct-interval-field tct-availability-summary-field"><label class="tct-label">Availability</label><div class="tct-muted tct-settings-help" data-tct-availability-summary>Only available for positive interval goals.</div></div>';
        $html .= '</div>';
        $html .= '<div class="tct-availability-cycle-fields" data-tct-availability-fields hidden="hidden">';
        $html .= '<div class="tct-interval-row tct-availability-cycle-grid">';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Anchor date</label><input type="date" name="availability_cycle_anchor_date_local" value="' . esc_attr( current_time( 'Y-m-d' ) ) . '" data-tct-availability-anchor-date /></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Anchor phase</label><select name="availability_cycle_anchor_phase" data-tct-availability-anchor-phase><option value="active">Active</option><option value="pause">Pause</option></select></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Anchor day within phase</label><input type="number" min="1" step="1" name="availability_cycle_anchor_day" value="1" data-tct-availability-anchor-day /></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Active duration</label><input type="number" min="1" step="1" name="availability_cycle_active_duration" value="7" data-tct-availability-active-duration /><div class="tct-muted">days</div></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Pause duration</label><input type="number" min="1" step="1" name="availability_cycle_pause_duration" value="7" data-tct-availability-pause-duration /><div class="tct-muted">days</div></div>';
        $html .= '</div>';
        $html .= '<p class="tct-muted tct-settings-help">Anchor date means: this local date is day N of the selected phase. Example: if the anchor date is pause day 6 of 7, two paused days remain and then the goal resumes on the next active day.</p>';
        $html .= '</div>';
        $html .= '<p class="tct-muted tct-settings-help">Use this when a goal should alternate between active and paused blocks. During pause, vitality, urgency, and penalty pressure freeze. Manual completions are still allowed and will count when the goal resumes.</p>';
        $html .= '<div class="tct-goal-warning tct-goal-warning-error" data-tct-availability-warning hidden="hidden"></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="tct-form-row" data-tct-interval-anchor-row hidden="hidden" data-tct-interval-anchor-today="' . esc_attr( current_time( 'Y-m-d' ) ) . '">';
        $html .= '<div class="tct-form-row-head">Interval alignment <span class="tct-muted">(optional)</span></div>';
        $html .= '<div class="tct-form-row-body">';
        $html .= '<div class="tct-interval-row tct-availability-cycle-toggle-grid">';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Enabled</label><select name="interval_anchor_enabled" data-tct-interval-anchor-enabled><option value="0">Off</option><option value="1">On</option></select></div>';
        $html .= '<div class="tct-interval-field tct-availability-summary-field"><label class="tct-label">Alignment</label><div class="tct-muted tct-settings-help" data-tct-interval-anchor-summary>Only available for positive interval goals with day-or-larger intervals.</div></div>';
        $html .= '</div>';
        $html .= '<div class="tct-availability-cycle-fields" data-tct-interval-anchor-fields hidden="hidden">';
        $html .= '<div class="tct-interval-row tct-availability-cycle-grid">';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Anchor date</label><input type="date" name="interval_anchor_anchor_date_local" value="' . esc_attr( current_time( 'Y-m-d' ) ) . '" data-tct-interval-anchor-date /></div>';
        $html .= '<div class="tct-interval-field"><label class="tct-label">Current day within interval</label><input type="number" min="1" step="1" name="interval_anchor_anchor_day" value="1" data-tct-interval-anchor-day /></div>';
        $html .= '</div>';
        $html .= '<p class="tct-muted tct-settings-help">Anchor date means: on this local date, the goal is day N of its current interval. This lets the interval clock be aligned independently from the Active / Pause cycle.</p>';
        $html .= '</div>';
        $html .= '<div class="tct-goal-warning tct-goal-warning-error" data-tct-interval-anchor-warning hidden="hidden"></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="tct-form-row" data-tct-allowed-fails-row hidden="hidden">'; $html .= '<div class="tct-form-row-head">Allowed fails</div>'; $html .= '<div class="tct-form-row-body">'; $html .= '<div class="tct-interval-row tct-allowed-fails-grid">'; $html .= '<div class="tct-interval-field">'; $html .= '<label class="tct-label" for="tct_allowed_fails_target">Free misses per window</label>'; $html .= '<input type="number" id="tct_allowed_fails_target" name="allowed_fails_target" value="0" min="0" step="1" data-tct-allowed-fails-target disabled="disabled" />'; $html .= '</div>'; $html .= '<div class="tct-interval-field">'; $html .= '<label class="tct-label" for="tct_allowed_fails_span">Window span</label>'; $html .= '<input type="number" id="tct_allowed_fails_span" name="allowed_fails_span" value="1" min="1" step="1" data-tct-allowed-fails-span disabled="disabled" />'; $html .= '</div>'; $html .= '<div class="tct-interval-field">'; $html .= '<label class="tct-label" for="tct_allowed_fails_unit">Window unit</label>'; $html .= '<select id="tct_allowed_fails_unit" name="allowed_fails_unit" data-tct-allowed-fails-unit disabled="disabled">'; $html .= '<option value="week">Week(s)</option>'; $html .= '<option value="month">Month(s)</option>'; $html .= '<option value="year">Year(s)</option>'; $html .= '</select>'; $html .= '</div>'; $html .= '</div>'; $html .= '<p class="tct-muted">Only available for positive goals with a tracking period of exactly 1 per day. The first N misses per window are free (0 points), then normal penalties apply.</p>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-plant-row>'; $html .= '<div class="tct-form-row-head">Vitality plant (optional)</div>'; $html .= '<input type="hidden" name="plant_name" value="" data-tct-plant-name />'; $html .= '<div class="tct-plant-picker" data-tct-plant-picker>'; $html .= '<div class="tct-plant-picker-control" data-tct-plant-control>'; $html .= '<button type="button" class="tct-plant-picker-selected" data-tct-plant-selected aria-haspopup="listbox" aria-expanded="false">'; $html .= '<img class="tct-plant-thumb" data-tct-plant-selected-thumb alt="" src="" hidden="hidden" />'; $html .= '<span class="tct-plant-selected-label" data-tct-plant-selected-label>-- No plant selected --</span>'; $html .= '<span class="tct-plant-caret" aria-hidden="true">&#9660;</span>'; $html .= '</button>'; $html .= '<div class="tct-plant-picker-popover" data-tct-plant-popover hidden="hidden">'; $html .= '<input type="text" class="tct-plant-search" placeholder="Search plants..." data-tct-plant-search />'; $html .= '<div class="tct-plant-preview" data-tct-plant-preview>'; $html .= '<img data-tct-plant-preview-img alt="" src="" hidden="hidden" />'; $html .= '<div class="tct-plant-preview-placeholder tct-muted" data-tct-plant-preview-placeholder>Select a plant below</div>'; $html .= '</div>'; $html .= '<div class="tct-plant-options" data-tct-plant-options role="listbox"></div>'; $html .= '<div class="tct-plant-empty tct-muted" data-tct-plant-empty hidden="hidden">No plants found.</div>'; $html .= '<div class="tct-plant-popover-actions">'; $html .= '<button type="button" class="button" data-tct-plant-cancel>Cancel</button>'; $html .= '<button type="button" class="button button-primary" data-tct-plant-select>Select this plant</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-plant-actions">'; $html .= '<button type="button" class="button" data-tct-plant-clear>Clear</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '<p class="tct-muted">Pick a plant to display under the Vitality ring for this goal.</p>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-timer-row>'; $html .= '<div class="tct-form-row-head">Countdown timer (optional)</div>'; $html .= '<div class="tct-timer-enable-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="checkbox" name="timer_enabled" value="1" data-tct-timer-enabled /> Enable countdown timer'; $html .= '</label>'; $html .= '</div>'; $html .= '<div class="tct-timer-fields" data-tct-timer-fields hidden="hidden">'; $html .= '<div class="tct-timer-inputs">'; $html .= '<div class="tct-timer-input-group">'; $html .= '<label for="tct-timer-hours">Hours</label>'; $html .= '<input type="number" id="tct-timer-hours" name="timer_hours" min="0" max="23" value="0" data-tct-timer-hours />'; $html .= '</div>'; $html .= '<span class="tct-timer-separator">:</span>'; $html .= '<div class="tct-timer-input-group">'; $html .= '<label for="tct-timer-minutes">Minutes</label>'; $html .= '<input type="number" id="tct-timer-minutes" name="timer_minutes" min="0" max="59" value="0" data-tct-timer-minutes />'; $html .= '</div>'; $html .= '<span class="tct-timer-separator">:</span>'; $html .= '<div class="tct-timer-input-group">'; $html .= '<label for="tct-timer-seconds">Seconds</label>'; $html .= '<input type="number" id="tct-timer-seconds" name="timer_seconds" min="0" max="59" value="0" data-tct-timer-seconds />'; $html .= '</div>'; $html .= '</div>'; $html .= '<p class="tct-muted">Set the duration for timed sessions (e.g., "Read for 15 minutes").</p>'; $html .= '<div class="tct-alarm-settings" data-tct-alarm-settings>'; $html .= '<div class="tct-alarm-settings-heading">Alarm Settings</div>'; $html .= '<div class="tct-alarm-row">'; $html .= '<label for="tct-alarm-sound">Alarm Sound</label>'; $html .= '<select id="tct-alarm-sound" name="alarm_sound" data-tct-alarm-sound>'; $html .= '<option value="">-- Select --</option>'; $html .= '<option value="soft_chime">Soft Chime</option>'; $html .= '<option value="meditation_bell">Meditation Bell</option>'; $html .= '<option value="wind_chimes">Wind Chimes</option>'; $html .= '<option value="gentle_pulse">Gentle Pulse</option>'; $html .= '<option value="standard_alert">Standard Alert</option>'; $html .= '<option value="digital_beep">Digital Beep</option>'; $html .= '<option value="rapid_pulse">Rapid Pulse</option>'; $html .= '<option value="urgent_alarm">Urgent Alarm</option>'; $html .= '<option value="alarm_clock">Alarm Clock</option>'; $html .= '<option value="vibration_only">Vibration Only</option>'; $html .= '</select>'; $html .= '</div>'; $html .= '<div class="tct-alarm-row">'; $html .= '<label for="tct-alarm-duration">Alarm Duration</label>'; $html .= '<select id="tct-alarm-duration" name="alarm_duration" data-tct-alarm-duration>'; $html .= '<option value="">-- Select --</option>'; $html .= '<option value="5">5 seconds</option>'; $html .= '<option value="15">15 seconds</option>'; $html .= '<option value="30">30 seconds</option>'; $html .= '<option value="60">1 minute</option>'; $html .= '<option value="600">Until dismissed</option>'; $html .= '</select>'; $html .= '</div>'; $html .= '<div class="tct-alarm-row tct-alarm-vibration-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="checkbox" name="alarm_vibration" value="1" data-tct-alarm-vibration /> Enable vibration'; $html .= '</label>'; $html .= '<span class="tct-vibration-notice" data-tct-vibration-notice hidden="hidden">(not supported on this device)</span>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-visible-after-row>'; $html .= '<div class="tct-form-row-head">Show on mobile after (optional)</div>'; $html .= '<div class="tct-visible-after-field">'; $html .= '<input type="time" name="visible_after_time" value="" data-tct-visible-after-time style="max-width:160px;" />'; $html .= '<button type="button" class="button tct-visible-after-clear" data-tct-visible-after-clear style="margin-left:8px;">Clear</button>'; $html .= '</div>'; $html .= '<p class="tct-muted">For daily goals: hide this goal on the mobile page until this time of day. Leave empty to always show.</p>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-sleep-row>'; $html .= '<div class="tct-form-row-head">Sleep tracking (optional)</div>'; $html .= '<input type="hidden" name="sleep_tracking_enabled" value="0" />'; $html .= '<div class="tct-sleep-enable-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="checkbox" name="sleep_tracking_enabled" value="1" data-tct-sleep-enabled /> Enable Sleep Tracking'; $html .= '</label>'; $html .= '</div>'; $html .= '<div class="tct-sleep-rollover-fields" data-tct-sleep-rollover-fields hidden="hidden">'; $html .= '<label class="tct-label" for="tct-sleep-rollover-time">Evening rollover time (HH:MM)</label>'; $html .= '<input id="tct-sleep-rollover-time" type="time" step="60" name="sleep_rollover_time" value="18:00" data-tct-sleep-rollover-time style="max-width:160px;" disabled="disabled" />'; $html .= '<p class="tct-muted">Rollover determines the default sleep date: before this time logs to yesterday; at/after logs to today.</p>'; $html .= '</div>'; $html .= '<p class="tct-muted">Only one goal can have Sleep Tracking enabled at a time.</p>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-wake-time-row>'; $html .= '<div class="tct-form-row-head">Wake-time</div>'; $html .= '<input type="hidden" name="wake_time_enabled" value="0" />'; $html .= '<div class="tct-wake-time-enable-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="checkbox" name="wake_time_enabled" value="1" data-tct-wake-time-enabled /> Enable wake-time'; $html .= '</label>'; $html .= '</div>'; $html .= '<div class="tct-wake-time-fields" data-tct-wake-time-fields hidden="hidden">'; $html .= '<label class="tct-label" for="tct-wake-time-target">Desired wake-up time (HH:MM)</label>'; $html .= '<input id="tct-wake-time-target" type="time" step="60" name="wake_time_target" value="" data-tct-wake-time-target style="max-width:160px;" disabled="disabled" />'; $html .= '<p class="tct-muted">When enabled, this goal can be auto-scored based on your Sleep Tracker wake time (within 30 minutes).</p>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-bed-time-row>'; $html .= '<div class="tct-form-row-head">Bed-time</div>'; $html .= '<input type="hidden" name="bed_time_enabled" value="0" />'; $html .= '<div class="tct-bed-time-enable-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="checkbox" name="bed_time_enabled" value="1" data-tct-bed-time-enabled /> Enable bed-time'; $html .= '</label>'; $html .= '</div>'; $html .= '<div class="tct-bed-time-fields" data-tct-bed-time-fields hidden="hidden">'; $html .= '<label class="tct-label" for="tct-bed-time-target">Desired go-to-bed time (HH:MM)</label>'; $html .= '<input id="tct-bed-time-target" type="time" step="60" name="bed_time_target" value="" data-tct-bed-time-target style="max-width:160px;" disabled="disabled" />'; $html .= '<p class="tct-muted">When enabled, this goal can be auto-scored based on your Sleep Tracker bedtime (on time or early, and up to 30 minutes late).</p>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-favorite-row>'; $html .= '<div class="tct-form-row-head">Favorites</div>'; $html .= '<div class="tct-favorite-enable-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="checkbox" name="is_favorite" value="1" data-tct-favorite-enabled /> Mark as favorite</label>'; $html .= '</div>'; $html .= '<p class="tct-muted">Favorites appear in the mobile Favorites swipe screen.</p>'; $html .= '</div>'; $html .= '<div class="tct-form-row" data-tct-fail-row>'; $html .= '<div class="tct-form-row-head">Fail button (optional)</div>'; $html .= '<div class="tct-fail-enable-row">'; $html .= '<label class="tct-checkbox-label">'; $html .= '<input type="hidden" name="fail_button_enabled" value="0" />';
		$html .= '<input type="checkbox" name="fail_button_enabled" value="1" data-tct-fail-enabled /> Show fail button'; $html .= '</label>'; $html .= '</div>'; $html .= '<p class="tct-muted">When enabled, shows a Fail button alongside Complete. Failing a goal applies the penalty immediately and removes it from the active view for this interval.</p>'; $html .= '</div>'; $html .= '<div class="tct-modal-actions tct-goal-modal-actions">'; $html .= '<button type="button" class="button tct-goal-archive-btn" data-tct-goal-archive hidden="hidden">Archive</button>'; $html .= '<button type="button" class="button tct-goal-delete-btn" data-tct-goal-delete hidden="hidden">Delete</button>'; $html .= '<button type="submit" class="button button-primary">Save</button>'; $html .= '<button type="button" class="button" data-tct-modal-cancel>Cancel</button>'; $html .= '</div>'; $html .= '</form>'; $html .= '</div>'; $html .= '<template data-tct-interval-template>'; $html .= '<div class="tct-interval-row" data-tct-interval-row>'; $html .= '<div class="tct-interval-field" data-tct-interval-target-field>'; $html .= '<label class="tct-label" data-tct-interval-target-label>Completions</label>'; $html .= '<input type="number" min="1" max="999999" value="" data-tct-interval-target />'; $html .= '</div>'; $html .= '<div class="tct-interval-field">'; $html .= '<label class="tct-label">Every</label>'; $html .= '<input type="number" min="1" max="999999" value="1" data-tct-interval-span />'; $html .= '</div>'; $html .= '<div class="tct-interval-field">'; $html .= '<label class="tct-label">Unit</label>'; $html .= '<select data-tct-interval-unit>' . $unit_options . '</select>'; $html .= '</div>'; $html .= '<div class="tct-interval-field" data-tct-interval-bonus-field>'; $html .= '<label class="tct-label" data-tct-interval-bonus-label>Bonus/penalty</label>'; $html .= '<div class="tct-interval-bonus-preview" data-tct-interval-bonus-preview>+0 / 0</div>'; $html .= '<div class="tct-muted" style="font-size:12px;">Auto</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</template>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_goal_history_modal() { $html = ''; $html .= '<div class="tct-modal-overlay" data-tct-history-overlay hidden="hidden"></div>'; $html .= '<div class="tct-modal" data-tct-history-modal hidden="hidden" role="dialog" aria-modal="true" aria-labelledby="tct-history-modal-title">'; $html .= '<div class="tct-modal-inner">'; $html .= '<div class="tct-modal-header">'; $html .= '<h3 id="tct-history-modal-title" data-tct-history-title>History</h3>'; $html .= '<button type="button" class="tct-modal-close" data-tct-history-close aria-label="Close">&times;</button>'; $html .= '</div>'; $html .= '<div class="tct-modal-body">'; $html .= '<div class="tct-domain-heatmap-viewtabs" data-tct-domain-heatmap-viewtabs role="tablist" aria-label="Heatmap views">'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="week" role="tab" aria-selected="false">Week</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="month" role="tab" aria-selected="false">Month</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab tct-domain-heatmap-viewtab-active" data-tct-domain-heatmap-view="year" role="tab" aria-selected="true">Year</button>'; $html .= '</div>'; $html .= '<div class="tct-history-loading" data-tct-history-loading hidden="hidden">Loading...</div>'; $html .= '<div class="tct-history-error" data-tct-history-error hidden="hidden"></div>'; $html .= '<div class="tct-muted" data-tct-history-summary></div>'; $html .= '<div class="tct-history-heatmap" data-tct-history-heatmap>'; $html .= '<div class="tct-history-heatmap-controls" aria-label="Completion map year controls">'; $html .= '<button type="button" class="tct-history-heatmap-btn" data-tct-history-heatmap-prev aria-label="Previous year" title="Previous year"><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>'; $html .= '<button type="button" class="tct-history-heatmap-btn" data-tct-history-heatmap-current aria-label="This year" title="This year"><span class="dashicons dashicons-controls-record" aria-hidden="true"></span></button>'; $html .= '<button type="button" class="tct-history-heatmap-btn" data-tct-history-heatmap-next aria-label="Next year" title="Next year"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>'; $html .= '</div>'; $html .= '<div class="tct-history-heatmap-loading" data-tct-history-heatmap-loading hidden="hidden">Loading...</div>'; $html .= '<div class="tct-history-heatmap-grid-wrap">'; $html .= '<div class="tct-history-heatmap-grid" data-tct-history-heatmap-grid></div>'; $html .= '</div>'; $html .= '<div class="tct-history-heatmap-footer"><span class="tct-history-heatmap-year" data-tct-history-heatmap-year></span><div class="tct-history-heatmap-stats" data-tct-history-heatmap-stats hidden><span class="tct-history-heatmap-stat">Successful intervals: <strong data-tct-history-success-rate>--</strong></span><span class="tct-history-heatmap-stat">Longest streak: <strong data-tct-history-longest-streak>--</strong></span><span class="tct-history-heatmap-stat">Current streak: <strong data-tct-history-current-streak>--</strong></span></div></div>'; $html .= '</div>'; $html .= '<div class="tct-history-tabs" role="tablist" aria-label="Goal history sections">'; $html .= '<button type="button" class="tct-history-tab tct-history-tab-active" data-tct-history-tab="completions" role="tab" aria-selected="true">Completions</button>'; $html .= '<button type="button" class="tct-history-tab" data-tct-history-tab="goals-met" role="tab" aria-selected="false">Goals Met</button>'; $html .= '</div>'; $html .= '<div class="tct-history-panels">'; $html .= '<div class="tct-history-panel tct-history-panel-active" data-tct-history-panel="completions" role="tabpanel">'; $html .= '<div data-tct-history-completions></div>'; $html .= '</div>'; $html .= '<div class="tct-history-panel" data-tct-history-panel="goals-met" role="tabpanel" hidden="hidden">'; $html .= '<div data-tct-history-goals-met></div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domain_heatmap_modal() { $html = '<div class="tct-modal-overlay" data-tct-domain-heatmap-overlay hidden></div>'; $html .= '<div class="tct-modal tct-domain-heatmap-modal" data-tct-domain-heatmap-modal hidden role="dialog" aria-modal="true">'; $html .= '<div class="tct-modal-inner">'; $html .= '<div class="tct-modal-header">'; $html .= '<h3 class="tct-modal-title" data-tct-domain-heatmap-title>Domain activity</h3>'; $html .= '<button type="button" class="tct-modal-close" data-tct-domain-heatmap-close aria-label="Close">&times;</button>'; $html .= '</div>'; $html .= '<div class="tct-modal-body">'; $html .= '<div class="tct-domain-heatmap-viewtabs" data-tct-domain-heatmap-viewtabs role="tablist" aria-label="Heatmap views">'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="week" role="tab" aria-selected="false">Week</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="month" role="tab" aria-selected="false">Month</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab tct-domain-heatmap-viewtab-active" data-tct-domain-heatmap-view="year" role="tab" aria-selected="true">Year</button>'; $html .= '</div>'; $html .= '<div class="tct-domain-heatmap-loading" data-tct-domain-heatmap-loading hidden>Loading...</div>'; $html .= '<div class="tct-domain-heatmap-error" data-tct-domain-heatmap-error hidden></div>'; $html .= '<div class="tct-domain-heatmap-content" data-tct-domain-heatmap-content></div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_roles_panel( $user_id, $redirect_here ) { global $wpdb; $roles_table = TCT_DB::table_roles(); $goals_table = TCT_DB::table_goals(); $domains = $this->get_domains( $user_id ); $domain_map = $this->get_domain_map( $domains ); $domain_color_map = $this->get_domain_color_map( $domains ); $roles = $this->get_roles( $user_id ); $goal_counts_rows = $wpdb->get_results( $wpdb->prepare( "SELECT role_id, COUNT(*) AS cnt FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 GROUP BY role_id", $user_id ), ARRAY_A ); $goal_counts = array(); if ( is_array( $goal_counts_rows ) ) { foreach ( $goal_counts_rows as $r ) { $rid = isset( $r['role_id'] ) ? (int) $r['role_id'] : 0; $cnt = isset( $r['cnt'] ) ? (int) $r['cnt'] : 0; $goal_counts[ $rid ] = $cnt; } } $html = '<div class="tct-card">'; $html .= '<div class="tct-domains-header">'; $html .= '<h3>Roles</h3>'; $add_disabled = empty( $domains ) ? ' disabled="disabled"' : ''; $html .= '<div class="tct-domains-actions"><button type="button" class="button button-primary" data-tct-open-role-modal="add"' . $add_disabled . '>Add role</button></div>'; $html .= '</div>'; $html .= '<p class="tct-muted">Roles belong to a Domain and appear as columns within that Domain on the Dashboard. Assign goals to roles from the Goals tab.</p>'; if ( empty( $domains ) ) { $html .= '<p class="tct-muted">Create at least one Domain first before adding roles.</p>'; } if ( empty( $roles ) ) { $html .= '<p class="tct-muted">No roles yet. Click "Add role".</p>'; } else { $html .= '<div class="tct-table-wrap"><table class="tct-table tct-roles-table" data-tct-roles-table>'; $html .= '<thead><tr><th class="tct-col-order" aria-label="Order"></th><th>Role</th><th>Domain</th><th>Goals</th><th>Actions</th></tr></thead>'; $current_domain_id = null; $opened_tbody = false; foreach ( $roles as $role ) { $rid = isset( $role['id'] ) ? (int) $role['id'] : 0; if ( $rid <= 0 ) { continue; } $domain_id = isset( $role['domain_id'] ) ? (int) $role['domain_id'] : 0; if ( null === $current_domain_id || $domain_id !== $current_domain_id ) { if ( $opened_tbody ) { $html .= '</tbody>'; } $html .= '<tbody class="tct-roles-tbody" data-tct-role-sortable="1" data-domain-id="' . esc_attr( $domain_id ) . '">'; $opened_tbody = true; $current_domain_id = $domain_id; } $domain_name = $domain_id > 0 && isset( $domain_map[ $domain_id ] ) ? (string) $domain_map[ $domain_id ] : ''; $domain_color = $domain_id > 0 && isset( $domain_color_map[ $domain_id ] ) ? (string) $domain_color_map[ $domain_id ] : ''; $role_name = isset( $role['role_name'] ) ? (string) $role['role_name'] : ''; if ( '' === $role_name ) { $role_name = 'Role'; } $payload = array( 'role_id' => $rid, 'id' => $rid, 'role_name' => $role_name, 'domain_id' => $domain_id, ); $payload_json = wp_json_encode( $payload ); $dot_color = '' !== $domain_color ? $domain_color : '#8c8f94'; $domain_badge = '<span class="tct-domain-badge">'; $domain_badge .= '<span class="tct-domain-dot" style="background-color:' . esc_attr( $dot_color ) . '"></span>'; if ( '' !== $domain_name ) { $domain_badge .= '<strong>' . esc_html( $domain_name ) . '</strong>'; } else { $domain_badge .= '<span class="tct-muted">Unassigned</span>'; } $domain_badge .= '</span>'; $html .= '<tr class="tct-role-row" data-role-id="' . esc_attr( $rid ) . '">'; $html .= '<td class="tct-col-order"><span class="tct-drag-handle" title="Drag to reorder" aria-hidden="true"></span></td>'; $html .= '<td><strong>' . esc_html( $role_name ) . '</strong></td>'; $html .= '<td>' . $domain_badge . '</td>'; $html .= '<td>' . esc_html( isset( $goal_counts[ $rid ] ) ? (int) $goal_counts[ $rid ] : 0 ) . '</td>'; $html .= '<td>'; $html .= '<div class="tct-goal-actions">'; $html .= '<button type="button" class="button" data-tct-open-role-modal="edit" data-tct-role="' . esc_attr( $payload_json ) . '">Edit</button> '; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="tct-inline-form" data-tct-confirm="Delete this role? Goals assigned to it will become unassigned.">'; $html .= '<input type="hidden" name="action" value="tct_role_delete" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="role_id" value="' . esc_attr( $rid ) . '" />'; $html .= wp_nonce_field( 'tct_role_delete', '_wpnonce', true, false ); $html .= '<button type="submit" class="button">Delete</button>'; $html .= '</form>'; $html .= '</div>'; $html .= '</td>'; $html .= '</tr>'; } if ( $opened_tbody ) { $html .= '</tbody>'; } $html .= '</table></div>'; } $html .= '</div>'; $html .= $this->render_role_modal( $domains, $redirect_here ); return $html; } private function render_role_modal( $domains, $redirect_here ) { $domain_options = ''; $domain_options .= '<option value="">Select a domain...</option>'; foreach ( $domains as $d ) { $id = isset( $d['id'] ) ? (int) $d['id'] : 0; $name = isset( $d['domain_name'] ) ? (string) $d['domain_name'] : ''; if ( $id > 0 && '' !== $name ) { $domain_options .= '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>'; } } $html = ''; $html .= '<div class="tct-modal-overlay" data-tct-role-overlay hidden="hidden"></div>'; $html .= '<div class="tct-modal" data-tct-role-modal hidden="hidden" role="dialog" aria-modal="true" aria-labelledby="tct-role-modal-title">'; $html .= '<div class="tct-modal-inner">'; $html .= '<div class="tct-modal-header">'; $html .= '<h3 id="tct-role-modal-title" data-tct-role-modal-title>Add role</h3>'; $html .= '<button type="button" class="tct-modal-close" data-tct-role-modal-close aria-label="Close">&times;</button>'; $html .= '</div>'; $html .= '<div class="tct-modal-body">'; $html .= '<div class="tct-domain-heatmap-viewtabs" data-tct-domain-heatmap-viewtabs role="tablist" aria-label="Heatmap views">'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="week" role="tab" aria-selected="false">Week</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="month" role="tab" aria-selected="false">Month</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab tct-domain-heatmap-viewtab-active" data-tct-domain-heatmap-view="year" role="tab" aria-selected="true">Year</button>'; $html .= '</div>'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" data-tct-role-form>'; $html .= '<input type="hidden" name="action" value="tct_role_upsert" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="role_id" value="0" data-tct-role-id />'; $html .= wp_nonce_field( 'tct_role_upsert', '_wpnonce', true, false ); $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-role-domain">Domain</label>'; $html .= '<select id="tct-role-domain" name="domain_id" required="required" data-tct-role-domain-select>'; $html .= $domain_options; $html .= '</select>'; $html .= '</div>'; $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-role-name">Role name</label>'; $html .= '<input id="tct-role-name" type="text" name="role_name" value="" placeholder="e.g., Father" required="required" data-tct-role-name />'; $html .= '</div>'; $html .= '<div class="tct-modal-actions">'; $html .= '<button type="submit" class="button button-primary">Save</button>'; $html .= '<button type="button" class="button" data-tct-role-modal-cancel>Cancel</button>'; $html .= '</div>'; $html .= '</form>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domains_panel( $user_id, $redirect_here ) { global $wpdb; $domains_table = TCT_DB::table_domains(); $goals_table = TCT_DB::table_goals(); $domains = $this->get_domains( $user_id ); $default_color = $this->suggest_domain_color_from_domains( $domains ); $goal_counts = $wpdb->get_results( $wpdb->prepare( "SELECT domain_id, COUNT(*) AS cnt FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1 GROUP BY domain_id", $user_id ), ARRAY_A ); $count_map = array(); foreach ( $goal_counts as $row ) { $count_map[ (int) $row['domain_id'] ] = (int) $row['cnt']; } $html = '<div class="tct-card">'; $html .= '<div class="tct-goals-header">'; $html .= '<h3>Domains</h3>'; $html .= '<div class="tct-goals-actions"><button type="button" class="button button-primary" data-tct-open-domain-modal="add" data-tct-domain-default-color="' . esc_attr( $default_color ) . '">Add domain</button></div>'; $html .= '</div>'; $html .= '<p class="tct-muted">Domains are buckets for grouping goals on your Dashboard (e.g., "Religious Life", "Fitness", "Family & Friends").</p>'; if ( empty( $domains ) ) { $html .= '<p class="tct-muted">No domains yet. Click "Add domain".</p>'; } else { $html .= '<div class="tct-table-wrap"><table class="tct-table">'; $html .= '<thead><tr><th>Domain</th><th>Color</th><th>Goals</th><th>Actions</th></tr></thead>'; $html .= '<tbody>'; foreach ( $domains as $d ) { $id = isset( $d['id'] ) ? (int) $d['id'] : 0; $name = isset( $d['domain_name'] ) ? (string) $d['domain_name'] : ''; if ( $id <= 0 || '' === $name ) { continue; } $color = isset( $d['color_hex'] ) ? sanitize_hex_color( $d['color_hex'] ) : ''; if ( ! $color ) { $color = $default_color; } $payload = array( 'domain_id' => $id, 'domain_name' => $name, 'color_hex' => $color, ); $payload_json = wp_json_encode( $payload ); $goal_cnt = isset( $count_map[ $id ] ) ? (int) $count_map[ $id ] : 0; $html .= '<tr>'; $html .= '<td><strong>' . esc_html( $name ) . '</strong></td>'; $html .= '<td><span class="tct-color-swatch" style="background:' . esc_attr( $color ) . '"></span><span class="tct-color-hex">' . esc_html( $color ) . '</span></td>'; $html .= '<td>' . esc_html( $goal_cnt ) . '</td>'; $html .= '<td>'; $html .= '<div class="tct-goal-actions">'; $html .= '<button type="button" class="button" data-tct-open-domain-modal="edit" data-tct-domain="' . esc_attr( $payload_json ) . '">Edit</button> '; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="tct-inline-form" data-tct-confirm="Delete this domain? Goals will become Unassigned.">'; $html .= '<input type="hidden" name="action" value="tct_domain_delete" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="domain_id" value="' . esc_attr( $id ) . '" />'; $html .= wp_nonce_field( 'tct_domain_delete', '_wpnonce', true, false ); $html .= '<button type="submit" class="button">Delete</button>'; $html .= '</form>'; $html .= '</div>'; $html .= '</td>'; $html .= '</tr>'; } $html .= '</tbody></table></div>'; } $html .= '</div>'; $html .= $this->render_domain_modal( $redirect_here ); return $html; } private function render_statistics_panel( $user_id, $redirect_here ) { $user_id = (int) $user_id; $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $week_offset = isset( $_GET['tct_stats_week_offset'] ) ? (int) wp_unslash( $_GET['tct_stats_week_offset'] ) : 0; $month_offset = isset( $_GET['tct_stats_month_offset'] ) ? (int) wp_unslash( $_GET['tct_stats_month_offset'] ) : 0; $year_offset = isset( $_GET['tct_stats_year_offset'] ) ? (int) wp_unslash( $_GET['tct_stats_year_offset'] ) : 0; if ( $week_offset < -520 ) { $week_offset = -520; } if ( $week_offset > 520 ) { $week_offset = 520; } if ( $month_offset < -240 ) { $month_offset = -240; } if ( $month_offset > 240 ) { $month_offset = 240; } if ( $year_offset < -25 ) { $year_offset = -25; } if ( $year_offset > 25 ) { $year_offset = 25; } $wp_sow = (int) get_option( 'start_of_week', 1 ); $week_starts_on = ( 0 === $wp_sow ) ? 0 : 1; $today_tz = $now_tz->setTime( 0, 0, 0 ); $today_ymd = $today_tz->format( 'Y-m-d' ); $today_dow = (int) $today_tz->format( 'w' ); $start_dow = ( 0 === $week_starts_on ) ? 0 : 1; $offset_days = ( $today_dow - $start_dow + 7 ) % 7; $week_start_tz = $today_tz->sub( new DateInterval( 'P' . $offset_days . 'D' ) ); $week_shift = (int) $week_offset * 7; if ( 0 !== $week_shift ) { $week_start_tz = $week_start_tz->modify( ( $week_shift >= 0 ? '+' : '' ) . $week_shift . ' days' ); } $week_end_tz = $week_start_tz->add( new DateInterval( 'P7D' ) ); $week_start_utc = TCT_Utils::dt_to_mysql_utc( $week_start_tz ); $week_end_utc = TCT_Utils::dt_to_mysql_utc( $week_end_tz ); $week_dates = array(); $week_period = new DatePeriod( $week_start_tz, new DateInterval( 'P1D' ), $week_end_tz ); foreach ( $week_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $week_dates[] = $dt->format( 'Y-m-d' ); } } $week_label = ''; if ( ! empty( $week_dates ) ) { try { $ws = new DateTimeImmutable( $week_dates[0] . ' 00:00:00', $tz ); $we = $ws->add( new DateInterval( 'P6D' ) ); $week_label = $ws->format( 'M j' ) . ' - ' . $we->format( 'M j, Y' ); } catch ( Exception $e ) { $week_label = ''; } } $month_start_tz = new DateTimeImmutable( $now_tz->format( 'Y-m-01 00:00:00' ), $tz ); if ( 0 !== $month_offset ) { $month_start_tz = $month_start_tz->modify( ( $month_offset >= 0 ? '+' : '' ) . $month_offset . ' months' ); } $month_end_tz = $month_start_tz->add( new DateInterval( 'P1M' ) ); $month_start_utc = TCT_Utils::dt_to_mysql_utc( $month_start_tz ); $month_end_utc = TCT_Utils::dt_to_mysql_utc( $month_end_tz ); $month_year = (int) $month_start_tz->format( 'Y' ); $month_num = (int) $month_start_tz->format( 'n' ); $month_label = $month_start_tz->format( 'F Y' ); $month_dates = array(); $month_period = new DatePeriod( $month_start_tz, new DateInterval( 'P1D' ), $month_end_tz ); foreach ( $month_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $month_dates[] = $dt->format( 'Y-m-d' ); } } $cur_year = (int) $now_tz->format( 'Y' ); $year = $cur_year + (int) $year_offset; if ( $year < 1970 ) { $year = 1970; } $year_start_tz = new DateTimeImmutable( sprintf( '%04d-01-01 00:00:00', $year ), $tz ); $year_end_tz = $year_start_tz->add( new DateInterval( 'P1Y' ) ); $year_start_utc = TCT_Utils::dt_to_mysql_utc( $year_start_tz ); $year_end_utc = TCT_Utils::dt_to_mysql_utc( $year_end_tz ); $year_dates = array(); $year_period = new DatePeriod( $year_start_tz, new DateInterval( 'P1D' ), $year_end_tz ); foreach ( $year_period as $dt ) { if ( $dt instanceof DateTimeInterface ) { $year_dates[] = $dt->format( 'Y-m-d' ); } } $year_label = (string) $year; $domains = $this->get_domains( $user_id ); $domain_map = $this->get_domain_map( $domains ); $domain_color_map = $this->get_domain_color_map( $domains ); global $wpdb; $goals_table = TCT_DB::table_goals(); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, role_id, goal_name, is_tracked, points_per_completion, target, period_unit, period_mode, allowed_fails_target, allowed_fails_unit, allowed_fails_span, intervals_json, points_enabled_at, domain_id
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1", $user_id ), ARRAY_A ); $roles = $this->get_roles( $user_id ); $role_map = $this->get_role_map( $roles ); $roles_by_domain = $this->group_roles_by_domain( $roles ); $goals_by_role = array(); if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $gr ) { if ( ! is_array( $gr ) ) { continue; } $rid = isset( $gr['role_id'] ) ? (int) $gr['role_id'] : 0; if ( $rid <= 0 ) { continue; } $ppc = isset( $gr['points_per_completion'] ) ? (int) $gr['points_per_completion'] : 0; if ( $ppc <= 0 ) { continue; } if ( ! isset( $goals_by_role[ $rid ] ) ) { $goals_by_role[ $rid ] = array(); } $goals_by_role[ $rid ][] = $gr; } } $scored_role_ids = array(); if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $gr ) { if ( ! is_array( $gr ) ) { continue; } $rid = isset( $gr['role_id'] ) ? (int) $gr['role_id'] : 0; $ppc = isset( $gr['points_per_completion'] ) ? (int) $gr['points_per_completion'] : 0; if ( $rid > 0 && $ppc > 0 ) { $scored_role_ids[ $rid ] = true; } } } $has_unassigned_domain = false; if ( is_array( $goal_rows ) ) { foreach ( $goal_rows as $gr ) { if ( ! is_array( $gr ) ) { continue; } $did = $this->effective_domain_id_from_goal_row( $gr, $role_map ); if ( 0 === (int) $did ) { $has_unassigned_domain = true; break; } } } $points_week = $this->get_domain_points_by_day_for_window( $user_id, $week_start_utc, $week_end_utc, $tz ); $points_month = $this->get_domain_points_by_day_for_window( $user_id, $month_start_utc, $month_end_utc, $tz ); $points_year = $this->get_domain_points_by_day_for_window( $user_id, $year_start_utc, $year_end_utc, $tz ); $role_points_week = $this->get_domain_role_points_by_day_for_window( $user_id, $week_start_utc, $week_end_utc, $tz ); $goal_points_week = $this->get_domain_role_goal_points_by_day_for_window( $user_id, $week_start_utc, $week_end_utc, $tz ); $goal_possible_week = $this->compute_goal_possible_points_for_dates( $week_dates, $tz, $goal_rows ); $role_points_month = $this->get_domain_role_points_by_day_for_window( $user_id, $month_start_utc, $month_end_utc, $tz ); $goal_points_month = $this->get_domain_role_goal_points_by_day_for_window( $user_id, $month_start_utc, $month_end_utc, $tz ); $goal_possible_month = $this->compute_goal_possible_points_for_dates( $month_dates, $tz, $goal_rows ); $possible_role_month_year_map = $this->compute_role_possible_points_by_day_for_year( $month_year, $tz, $goal_rows ); $role_points_year = $this->get_domain_role_points_by_day_for_window( $user_id, $year_start_utc, $year_end_utc, $tz ); $goal_points_year = $this->get_domain_role_goal_points_by_day_for_window( $user_id, $year_start_utc, $year_end_utc, $tz ); $goal_possible_year = $this->compute_goal_possible_points_for_dates( $year_dates, $tz, $goal_rows ); $possible_role_year_map = $this->compute_role_possible_points_by_day_for_year( $year, $tz, $goal_rows ); $include_unassigned = $has_unassigned_domain; if ( isset( $points_week[0] ) || isset( $points_month[0] ) || isset( $points_year[0] ) ) { $include_unassigned = true; } $domain_ids = array(); if ( is_array( $domains ) ) { foreach ( $domains as $d ) { if ( ! isset( $d['id'] ) ) { continue; } $did = (int) $d['id']; if ( $did > 0 ) { $domain_ids[] = $did; } } } if ( $include_unassigned ) { $domain_ids[] = 0; } if ( empty( $domain_ids ) ) { return '<div class="tct-card"><p class="tct-muted">No domains found. Create a domain first.</p></div>'; } $possible_year_map = $this->compute_domain_possible_points_by_day_for_year( $year, $tz, $goal_rows, $role_map ); $possible_month_year_map = $this->compute_domain_possible_points_by_day_for_year( $month_year, $tz, $goal_rows, $role_map ); $years_needed = array(); foreach ( $week_dates as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); if ( $y > 0 ) { $years_needed[ $y ] = true; } } if ( empty( $years_needed ) ) { $years_needed[ (int) $now_tz->format( 'Y' ) ] = true; } $possible_week_by_year = array(); foreach ( array_keys( $years_needed ) as $y ) { $y = (int) $y; if ( $y < 1970 ) { continue; } $possible_week_by_year[ $y ] = $this->compute_domain_possible_points_by_day_for_year( $y, $tz, $goal_rows, $role_map ); } $possible_role_week_by_year = array(); foreach ( array_keys( $years_needed ) as $y ) { $y = (int) $y; if ( $y < 1970 ) { continue; } $possible_role_week_by_year[ $y ] = $this->compute_role_possible_points_by_day_for_year( $y, $tz, $goal_rows ); } $current_url = TCT_Utils::current_url(); $week_prev_url = add_query_arg( array( 'tct_stats_week_offset' => (int) $week_offset - 1, 'tct_stats_month_offset' => (int) $month_offset, 'tct_stats_year_offset' => (int) $year_offset, ), $current_url ); $week_next_url = add_query_arg( array( 'tct_stats_week_offset' => (int) $week_offset + 1, 'tct_stats_month_offset' => (int) $month_offset, 'tct_stats_year_offset' => (int) $year_offset, ), $current_url ); $month_prev_url = add_query_arg( array( 'tct_stats_week_offset' => (int) $week_offset, 'tct_stats_month_offset' => (int) $month_offset - 1, 'tct_stats_year_offset' => (int) $year_offset, ), $current_url ); $month_next_url = add_query_arg( array( 'tct_stats_week_offset' => (int) $week_offset, 'tct_stats_month_offset' => (int) $month_offset + 1, 'tct_stats_year_offset' => (int) $year_offset, ), $current_url ); $year_prev_url = add_query_arg( array( 'tct_stats_week_offset' => (int) $week_offset, 'tct_stats_month_offset' => (int) $month_offset, 'tct_stats_year_offset' => (int) $year_offset - 1, ), $current_url ); $year_next_url = add_query_arg( array( 'tct_stats_week_offset' => (int) $week_offset, 'tct_stats_month_offset' => (int) $month_offset, 'tct_stats_year_offset' => (int) $year_offset + 1, ), $current_url ); foreach ( array( 'week_prev_url', 'week_next_url', 'month_prev_url', 'month_next_url', 'year_prev_url', 'year_next_url' ) as $k ) { if ( isset( $$k ) && is_string( $$k ) && '' !== $$k && false === strpos( (string) $$k, '#' ) ) { $$k .= '#tct-tab-panel-statistics'; } } $html = '<div class="tct-card tct-statistics-card">'; $html .= '<div class="tct-statistics" data-tct-statistics="1">'; $html .= '<div class="tct-stats-section tct-stats-section-week">'; $html .= '<div class="tct-stats-section-header">'; $html .= '<div class="tct-stats-section-title-wrap">'; $html .= '<div class="tct-stats-section-title">Week</div>'; $html .= '<div class="tct-stats-section-toggles" role="group" aria-label="Week expand">'; $html .= '<button type="button" class="tct-stats-expand-toggle" data-tct-stats-expand-toggle="roles" aria-pressed="false">Roles</button>'; $html .= '<button type="button" class="tct-stats-expand-toggle" data-tct-stats-expand-toggle="goals" aria-pressed="false">Goals</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-stats-nav" aria-label="Week navigation">'; $html .= '<a class="tct-stats-nav-btn" href="' . esc_url( $week_prev_url ) . '" aria-label="Previous week" title="Previous week">&lsaquo;</a>'; $html .= '<div class="tct-stats-nav-label">' . esc_html( $week_label ) . '</div>'; $html .= '<a class="tct-stats-nav-btn" href="' . esc_url( $week_next_url ) . '" aria-label="Next week" title="Next week">&rsaquo;</a>'; $html .= '</div>'; $html .= '</div>'; foreach ( $domain_ids as $domain_id ) { $domain_id = (int) $domain_id; $domain_name = ''; if ( 0 === $domain_id ) { $domain_name = 'Unassigned'; } elseif ( isset( $domain_map[ $domain_id ] ) ) { $domain_name = (string) $domain_map[ $domain_id ]; } $domain_color = ( 0 === $domain_id ) ? '#64748b' : ( isset( $domain_color_map[ $domain_id ] ) ? (string) $domain_color_map[ $domain_id ] : '#94a3b8' ); $domain_rgb = $this->hex_to_rgb_triplet( $domain_color ); if ( ! is_array( $domain_rgb ) || count( $domain_rgb ) !== 3 ) { $domain_rgb = array( 148, 163, 184 ); } $domain_day_points = ( isset( $points_week[ $domain_id ] ) && is_array( $points_week[ $domain_id ] ) ) ? $points_week[ $domain_id ] : array(); $domain_day_possible = array(); foreach ( $week_dates as $dk ) { $y = (int) substr( (string) $dk, 0, 4 ); if ( isset( $possible_week_by_year[ $y ] ) && isset( $possible_week_by_year[ $y ][ $domain_id ] ) && isset( $possible_week_by_year[ $y ][ $domain_id ][ $dk ] ) ) { $domain_day_possible[ $dk ] = $possible_week_by_year[ $y ][ $domain_id ][ $dk ]; } } $domain_alpha_by_day = array(); foreach ( $week_dates as $dk_alpha ) { $pts_a = isset( $domain_day_points[ $dk_alpha ] ) ? (float) $domain_day_points[ $dk_alpha ] : 0.0; $possible_a = isset( $domain_day_possible[ $dk_alpha ] ) ? (float) $domain_day_possible[ $dk_alpha ] : 0.0; $alpha_a = 0.0; if ( $pts_a > 0.0 ) { $pct_a = 0; if ( $possible_a > 0.0 ) { $pct_a = (int) round( ( $pts_a / $possible_a ) * 100.0 ); if ( $pct_a < 0 ) { $pct_a = 0; } if ( $pct_a > 100 ) { $pct_a = 100; } } else { $pct_a = 100; } $ratio_a = (float) $pct_a / 100.0; $alpha_a = 0.15 + ( 0.85 * $ratio_a ); if ( $alpha_a < 0.15 ) { $alpha_a = 0.15; } if ( $alpha_a > 1.0 ) { $alpha_a = 1.0; } } $domain_alpha_by_day[ $dk_alpha ] = $alpha_a; } $week_year = 0; $week_month = 0; try { $ws_dt = new DateTimeImmutable( $week_start_tz->format( 'Y-m-d' ) . ' 00:00:00', $tz ); $week_year = (int) $ws_dt->format( 'Y' ); $week_month = (int) $ws_dt->format( 'n' ); } catch ( Exception $e ) { $week_year = 0; $week_month = 0; } $roles_for_domain = array(); if ( isset( $roles_by_domain[ $domain_id ] ) && is_array( $roles_by_domain[ $domain_id ] ) ) { foreach ( $roles_by_domain[ $domain_id ] as $rr ) { if ( ! is_array( $rr ) ) { continue; } $rid = isset( $rr['id'] ) ? (int) $rr['id'] : 0; if ( $rid <= 0 ) { continue; } if ( ! isset( $scored_role_ids[ $rid ] ) ) { continue; } $roles_for_domain[] = $rr; } } $toggle_id = ''; if ( ! empty( $roles_for_domain ) ) { if ( function_exists( 'wp_unique_id' ) ) { $toggle_id = wp_unique_id( 'tct-stats-domain-roles-' ); } else { $toggle_id = 'tct-stats-domain-roles-' . (int) $domain_id . '-' . (string) wp_rand( 1000, 9999 ); } } $html .= '<div class="tct-stats-domain" style="--tct-domain-color:' . esc_attr( $domain_color ) . '; --tct-domain-color-rgb:' . esc_attr( (int) $domain_rgb[0] . ',' . (int) $domain_rgb[1] . ',' . (int) $domain_rgb[2] ) . ';">'; if ( '' !== $toggle_id ) { $html .= '<input type="checkbox" class="tct-stats-domain-toggle-input" id="' . esc_attr( $toggle_id ) . '" />'; } $html .= '<div class="tct-stats-week-row tct-stats-domain-row">'; if ( '' !== $toggle_id ) { $html .= '<label class="tct-stats-domain-label tct-stats-domain-label-toggle" for="' . esc_attr( $toggle_id ) . '" aria-label="Toggle roles" title="Show roles">'; $html .= '<span class="tct-stats-domain-caret dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>'; } else { $html .= '<div class="tct-stats-domain-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; } $html .= '<span class="tct-stats-domain-dot" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-domain-name">' . esc_html( $domain_name ) . '</span>'; if ( '' !== $toggle_id ) { $html .= '</label>'; } else { $html .= '</div>'; } $domain_week_bar = $this->render_domain_week_spectrum_bar_static( $domain_id, $domain_name, $week_dates, $domain_day_points, $domain_day_possible, $today_ymd ); if ( '' !== $toggle_id ) { $html .= '<label class="tct-stats-domain-bar-toggle" for="' . esc_attr( $toggle_id ) . '" aria-label="Toggle roles" title="Show roles">'; $html .= $domain_week_bar; $html .= '</label>'; } else { $html .= $domain_week_bar; } $html .= '</div>'; if ( '' !== $toggle_id ) { $html .= '<div class="tct-stats-domain-roles">'; foreach ( $roles_for_domain as $rr ) { if ( ! is_array( $rr ) ) { continue; } $rid = isset( $rr['id'] ) ? (int) $rr['id'] : 0; if ( $rid <= 0 ) { continue; } $rname = isset( $rr['role_name'] ) ? (string) $rr['role_name'] : ''; $rname = is_string( $rname ) ? trim( $rname ) : ''; if ( '' === $rname ) { $rname = 'Role'; } $r_points = ( isset( $role_points_week[ $domain_id ] ) && isset( $role_points_week[ $domain_id ][ $rid ] ) && is_array( $role_points_week[ $domain_id ][ $rid ] ) ) ? $role_points_week[ $domain_id ][ $rid ] : array(); $r_possible = array(); foreach ( $week_dates as $dk2 ) { $y2 = (int) substr( (string) $dk2, 0, 4 ); if ( isset( $possible_role_week_by_year[ $y2 ] ) && isset( $possible_role_week_by_year[ $y2 ][ $rid ] ) && isset( $possible_role_week_by_year[ $y2 ][ $rid ][ $dk2 ] ) ) { $r_possible[ $dk2 ] = $possible_role_week_by_year[ $y2 ][ $rid ][ $dk2 ]; } } $role_alpha_by_day = array(); foreach ( $week_dates as $dk_alpha2 ) { $rpts_a = isset( $r_points[ $dk_alpha2 ] ) ? (float) $r_points[ $dk_alpha2 ] : 0.0; $dpts_a = isset( $domain_day_points[ $dk_alpha2 ] ) ? (float) $domain_day_points[ $dk_alpha2 ] : 0.0; $dalpha_a = isset( $domain_alpha_by_day[ $dk_alpha2 ] ) ? (float) $domain_alpha_by_day[ $dk_alpha2 ] : 0.0; $ralpha_a = 0.0; if ( $rpts_a > 0.0 && $dpts_a > 0.0 && $dalpha_a > 0.0 ) { $ralpha_a = $dalpha_a * ( $rpts_a / $dpts_a ); if ( $ralpha_a < 0.0 ) { $ralpha_a = 0.0; } if ( $ralpha_a > 1.0 ) { $ralpha_a = 1.0; } } $role_alpha_by_day[ $dk_alpha2 ] = $ralpha_a; } $goals_for_role = ( isset( $goals_by_role[ $rid ] ) && is_array( $goals_by_role[ $rid ] ) ) ? $goals_by_role[ $rid ] : array(); $goals_render = array(); foreach ( $goals_for_role as $g ) { if ( ! is_array( $g ) ) { continue; } $goal_id = isset( $g['id'] ) ? (int) $g['id'] : 0; if ( $goal_id <= 0 ) { continue; } $gname = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : ''; $gname = is_string( $gname ) ? trim( $gname ) : ''; if ( '' === $gname ) { $gname = 'Goal'; } $g_points = ( isset( $goal_points_week[ $domain_id ] ) && isset( $goal_points_week[ $domain_id ][ $rid ] ) && isset( $goal_points_week[ $domain_id ][ $rid ][ $goal_id ] ) && is_array( $goal_points_week[ $domain_id ][ $rid ][ $goal_id ] ) ) ? $goal_points_week[ $domain_id ][ $rid ][ $goal_id ] : array(); $g_possible = ( isset( $goal_possible_week[ $goal_id ] ) && is_array( $goal_possible_week[ $goal_id ] ) ) ? $goal_possible_week[ $goal_id ] : array(); $possible_total = 0.0; $points_total = 0.0; foreach ( $week_dates as $dk3 ) { $possible_total += isset( $g_possible[ $dk3 ] ) ? (float) $g_possible[ $dk3 ] : 0.0; $points_total += isset( $g_points[ $dk3 ] ) ? (float) $g_points[ $dk3 ] : 0.0; } $goals_render[] = array( 'id' => $goal_id, 'name' => $gname, 'points_by_day' => $g_points, 'possible_by_day' => $g_possible, 'possible_total' => $possible_total, 'points_total' => $points_total, ); } if ( ! empty( $goals_render ) ) { usort( $goals_render, function ( $a, $b ) { $ap = isset( $a['possible_total'] ) ? (float) $a['possible_total'] : 0.0; $bp = isset( $b['possible_total'] ) ? (float) $b['possible_total'] : 0.0; if ( $ap !== $bp ) { return $bp <=> $ap; } $ae = isset( $a['points_total'] ) ? (float) $a['points_total'] : 0.0; $be = isset( $b['points_total'] ) ? (float) $b['points_total'] : 0.0; if ( $ae !== $be ) { return $be <=> $ae; } $an = isset( $a['name'] ) ? (string) $a['name'] : ''; $an = is_string( $an ) ? trim( $an ) : ''; $bn = isset( $b['name'] ) ? (string) $b['name'] : ''; $bn = is_string( $bn ) ? trim( $bn ) : ''; return strcasecmp( $an, $bn ); } ); } $role_toggle_id = ''; if ( ! empty( $goals_render ) ) { if ( function_exists( 'wp_unique_id' ) ) { $role_toggle_id = wp_unique_id( 'tct-stats-role-goals-' ); } else { $role_toggle_id = 'tct-stats-role-goals-' . (int) $rid . '-' . (string) wp_rand( 1000, 9999 ); } } $html .= '<div class="tct-stats-role">'; if ( '' !== $role_toggle_id ) { $html .= '<input type="checkbox" class="tct-stats-role-toggle-input" id="' . esc_attr( $role_toggle_id ) . '" />'; } $html .= '<div class="tct-stats-week-row tct-stats-role-row">'; if ( '' !== $role_toggle_id ) { $html .= '<label class="tct-stats-role-label tct-stats-role-label-toggle" for="' . esc_attr( $role_toggle_id ) . '" aria-label="Toggle goals" title="Toggle goals">'; $html .= '<span class="tct-stats-role-caret dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-role-name">' . esc_html( $rname ) . '</span>'; $html .= '</label>'; } else { $html .= '<div class="tct-stats-role-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-role-name">' . esc_html( $rname ) . '</span>'; $html .= '</div>'; } $role_week_bar = $this->render_role_week_spectrum_bar( $rid, $rname, $week_dates, $r_points, $r_possible, $today_ymd, $role_alpha_by_day, $domain_day_points, 'Domain' ); if ( '' !== $role_toggle_id ) { $html .= '<label class="tct-stats-role-bar-toggle" for="' . esc_attr( $role_toggle_id ) . '" aria-label="Toggle goals" title="Toggle goals">'; $html .= $role_week_bar; $html .= '</label>'; } else { $html .= $role_week_bar; } $html .= '</div>'; if ( '' !== $role_toggle_id ) { $html .= '<div class="tct-stats-role-goals">'; foreach ( $goals_render as $ginfo ) { if ( ! is_array( $ginfo ) ) { continue; } $gid = isset( $ginfo['id'] ) ? (int) $ginfo['id'] : 0; $gname = isset( $ginfo['name'] ) ? (string) $ginfo['name'] : 'Goal'; if ( $gid <= 0 ) { continue; } $gp = ( isset( $ginfo['points_by_day'] ) && is_array( $ginfo['points_by_day'] ) ) ? $ginfo['points_by_day'] : array(); $gpos = ( isset( $ginfo['possible_by_day'] ) && is_array( $ginfo['possible_by_day'] ) ) ? $ginfo['possible_by_day'] : array(); $html .= '<div class="tct-stats-goal">'; $html .= '<div class="tct-stats-week-row tct-stats-goal-row">'; $html .= '<div class="tct-stats-goal-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-goal-name">' . esc_html( $gname ) . '</span>'; $html .= '</div>'; $goal_alpha_by_day = array(); foreach ( $week_dates as $dk_alpha3 ) { $gpts_a = isset( $gp[ $dk_alpha3 ] ) ? (float) $gp[ $dk_alpha3 ] : 0.0; $rpts_a = isset( $r_points[ $dk_alpha3 ] ) ? (float) $r_points[ $dk_alpha3 ] : 0.0; $ralpha_a = isset( $role_alpha_by_day[ $dk_alpha3 ] ) ? (float) $role_alpha_by_day[ $dk_alpha3 ] : 0.0; $galpha_a = 0.0; if ( $gpts_a > 0.0 && $rpts_a > 0.0 && $ralpha_a > 0.0 ) { $galpha_a = $ralpha_a * ( $gpts_a / $rpts_a ); if ( $galpha_a < 0.0 ) { $galpha_a = 0.0; } if ( $galpha_a > 1.0 ) { $galpha_a = 1.0; } } $goal_alpha_by_day[ $dk_alpha3 ] = $galpha_a; } $html .= $this->render_goal_week_spectrum_bar( $gid, $gname, $week_dates, $gp, $gpos, $today_ymd, $goal_alpha_by_day, $r_points, 'Role' ); $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; $html .= '<hr class="tct-divider" />'; $html .= '<div class="tct-stats-section tct-stats-section-month">'; $html .= '<div class="tct-stats-section-header">'; $html .= '<div class="tct-stats-section-title-wrap">'; $html .= '<div class="tct-stats-section-title">Month</div>'; $html .= '<div class="tct-stats-section-toggles" role="group" aria-label="Month expand">'; $html .= '<button type="button" class="tct-stats-expand-toggle" data-tct-stats-expand-toggle="roles" aria-pressed="false">Roles</button>'; $html .= '<button type="button" class="tct-stats-expand-toggle" data-tct-stats-expand-toggle="goals" aria-pressed="false">Goals</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-stats-nav" aria-label="Month navigation">'; $html .= '<a class="tct-stats-nav-btn" href="' . esc_url( $month_prev_url ) . '" aria-label="Previous month">&lsaquo;</a>'; $html .= '<div class="tct-stats-nav-label">' . esc_html( $month_label ) . '</div>'; $html .= '<a class="tct-stats-nav-btn" href="' . esc_url( $month_next_url ) . '" aria-label="Next month">&rsaquo;</a>'; $html .= '</div></div>'; foreach ( $domain_ids as $domain_id ) { $domain_id = (int) $domain_id; $domain_name = isset( $domain_map[ $domain_id ] ) ? (string) $domain_map[ $domain_id ] : ( ( 0 === $domain_id ) ? 'Unassigned' : ( 'Domain ' . $domain_id ) ); $d_color = ( 0 === $domain_id ) ? '#64748b' : ( isset( $domain_color_map[ $domain_id ] ) ? (string) $domain_color_map[ $domain_id ] : '#94a3b8' ); $d_rgb_triplet = $this->hex_to_rgb_triplet( $d_color ); if ( ! is_array( $d_rgb_triplet ) || count( $d_rgb_triplet ) !== 3 ) { $d_rgb_triplet = array( 148, 163, 184 ); } $d_rgb = (int) $d_rgb_triplet[0] . ',' . (int) $d_rgb_triplet[1] . ',' . (int) $d_rgb_triplet[2]; $css_vars = '--tct-domain-color:' . $d_color . '; --tct-domain-color-rgb:' . $d_rgb . ';'; $domain_day_points = isset( $points_month[ $domain_id ] ) && is_array( $points_month[ $domain_id ] ) ? $points_month[ $domain_id ] : array(); $domain_possible_year = isset( $possible_month_year_map[ $domain_id ] ) && is_array( $possible_month_year_map[ $domain_id ] ) ? $possible_month_year_map[ $domain_id ] : array(); $domain_day_possible = array(); foreach ( $month_dates as $dkm ) { if ( isset( $domain_possible_year[ $dkm ] ) ) { $domain_day_possible[ $dkm ] = $domain_possible_year[ $dkm ]; } } $roles_for_domain = array(); if ( isset( $roles_by_domain[ $domain_id ] ) && is_array( $roles_by_domain[ $domain_id ] ) ) { foreach ( $roles_by_domain[ $domain_id ] as $rrow ) { $rid = isset( $rrow['id'] ) ? (int) $rrow['id'] : 0; if ( $rid < 1 ) { continue; } if ( ! isset( $scored_role_ids[ $rid ] ) ) { continue; } $roles_for_domain[] = $rrow; } } $domain_toggle_id = ''; if ( ! empty( $roles_for_domain ) ) { $domain_toggle_id = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'tct-stats-domain-roles-month-' ) : ( 'tct-stats-domain-roles-month-' . mt_rand( 1000, 999999 ) ); } $html .= '<div class="tct-stats-domain" style="' . esc_attr( $css_vars ) . '">'; if ( '' !== $domain_toggle_id ) { $html .= '<input type="checkbox" class="tct-stats-domain-toggle-input" id="' . esc_attr( $domain_toggle_id ) . '" />'; } $html .= '<div class="tct-stats-week-row tct-stats-domain-row">'; if ( '' !== $domain_toggle_id ) { $html .= '<label class="tct-stats-domain-label tct-stats-domain-label-toggle" for="' . esc_attr( $domain_toggle_id ) . '" aria-label="Toggle roles" title="Show roles">'; $html .= '<span class="tct-stats-domain-caret dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-domain-dot" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-domain-name">' . esc_html( $domain_name ) . '</span>'; $html .= '</label>'; } else { $html .= '<div class="tct-stats-domain-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-domain-dot" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-domain-name">' . esc_html( $domain_name ) . '</span>'; $html .= '</div>'; } $domain_month_bar = $this->render_domain_month_spectrum_bar_static( $domain_id, $domain_name, $month_year, $month_num, $month_dates, $domain_day_points, $domain_day_possible ); if ( '' !== $domain_toggle_id ) { $html .= '<label class="tct-stats-domain-bar-toggle" for="' . esc_attr( $domain_toggle_id ) . '" aria-label="Toggle roles" title="Show roles">' . $domain_month_bar . '</label>'; } else { $html .= $domain_month_bar; } $html .= '</div>'; if ( '' !== $domain_toggle_id ) { $domain_alpha_by_day = array(); foreach ( $month_dates as $dkm ) { $pts = isset( $domain_day_points[ $dkm ] ) ? (float) $domain_day_points[ $dkm ] : 0.0; $possible = isset( $domain_day_possible[ $dkm ] ) ? (float) $domain_day_possible[ $dkm ] : 0.0; if ( $pts <= 0.0 ) { $domain_alpha_by_day[ $dkm ] = 0.0; continue; } $pct = 0; if ( $possible > 0.0 ) { $pct = (int) round( ( $pts / $possible ) * 100.0 ); if ( $pct < 0 ) { $pct = 0; } if ( $pct > 100 ) { $pct = 100; } } else { $pct = 100; } $ratio = (float) $pct / 100.0; $alpha = 0.15 + ( 0.85 * $ratio ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } $domain_alpha_by_day[ $dkm ] = $alpha; } $html .= '<div class="tct-stats-domain-roles">'; foreach ( $roles_for_domain as $rrow ) { $rid = isset( $rrow['id'] ) ? (int) $rrow['id'] : 0; if ( $rid < 1 ) { continue; } $rname = isset( $rrow['role_name'] ) ? (string) $rrow['role_name'] : ( 'Role ' . $rid ); $rname = is_string( $rname ) ? trim( $rname ) : ''; if ( '' === $rname ) { $rname = 'Role'; } $r_points = ( isset( $role_points_month[ $domain_id ] ) && isset( $role_points_month[ $domain_id ][ $rid ] ) && is_array( $role_points_month[ $domain_id ][ $rid ] ) ) ? $role_points_month[ $domain_id ][ $rid ] : array(); $r_possible_year = ( isset( $possible_role_month_year_map[ $rid ] ) && is_array( $possible_role_month_year_map[ $rid ] ) ) ? $possible_role_month_year_map[ $rid ] : array(); $r_possible = array(); foreach ( $month_dates as $dkm ) { if ( isset( $r_possible_year[ $dkm ] ) ) { $r_possible[ $dkm ] = $r_possible_year[ $dkm ]; } } $role_alpha_by_day = array(); foreach ( $month_dates as $dkm ) { $dpts = isset( $domain_day_points[ $dkm ] ) ? (float) $domain_day_points[ $dkm ] : 0.0; $dalpha = isset( $domain_alpha_by_day[ $dkm ] ) ? (float) $domain_alpha_by_day[ $dkm ] : 0.0; $rpts = isset( $r_points[ $dkm ] ) ? (float) $r_points[ $dkm ] : 0.0; $ralpha = 0.0; if ( $rpts > 0.0 && $dpts > 0.0 && $dalpha > 0.0 ) { $ralpha = $dalpha * ( $rpts / $dpts ); if ( $ralpha < 0.0 ) { $ralpha = 0.0; } if ( $ralpha > 1.0 ) { $ralpha = 1.0; } } $role_alpha_by_day[ $dkm ] = $ralpha; } $goals_render = array(); if ( isset( $goals_by_role[ $rid ] ) && is_array( $goals_by_role[ $rid ] ) ) { foreach ( $goals_by_role[ $rid ] as $g ) { $gid = isset( $g['id'] ) ? (int) $g['id'] : 0; if ( $gid < 1 ) { continue; } $gname = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : ( 'Goal ' . $gid ); $gname = is_string( $gname ) ? trim( $gname ) : ''; if ( '' === $gname ) { $gname = 'Goal'; } $g_points = ( isset( $goal_points_month[ $domain_id ] ) && isset( $goal_points_month[ $domain_id ][ $rid ] ) && isset( $goal_points_month[ $domain_id ][ $rid ][ $gid ] ) && is_array( $goal_points_month[ $domain_id ][ $rid ][ $gid ] ) ) ? $goal_points_month[ $domain_id ][ $rid ][ $gid ] : array(); $g_possible = ( isset( $goal_possible_month[ $gid ] ) && is_array( $goal_possible_month[ $gid ] ) ) ? $goal_possible_month[ $gid ] : array(); $possible_total = 0.0; $points_total = 0.0; foreach ( $month_dates as $dkm ) { $possible_total += isset( $g_possible[ $dkm ] ) ? (float) $g_possible[ $dkm ] : 0.0; $points_total += isset( $g_points[ $dkm ] ) ? (float) $g_points[ $dkm ] : 0.0; } if ( $possible_total <= 0.0 && $points_total <= 0.0 ) { continue; } $goals_render[] = array( 'id' => $gid, 'name' => $gname, 'points' => $g_points, 'possible' => $g_possible, 'possible_total' => $possible_total, 'points_total' => $points_total, ); } } if ( ! empty( $goals_render ) ) { usort( $goals_render, function( $a, $b ) { $ap = isset( $a['possible_total'] ) ? (float) $a['possible_total'] : 0.0; $bp = isset( $b['possible_total'] ) ? (float) $b['possible_total'] : 0.0; if ( $ap !== $bp ) { return $bp <=> $ap; } $ae = isset( $a['points_total'] ) ? (float) $a['points_total'] : 0.0; $be = isset( $b['points_total'] ) ? (float) $b['points_total'] : 0.0; if ( $ae !== $be ) { return $be <=> $ae; } $an = isset( $a['name'] ) ? (string) $a['name'] : ''; $bn = isset( $b['name'] ) ? (string) $b['name'] : ''; return strcasecmp( $an, $bn ); } ); } $role_toggle_id = ''; if ( ! empty( $goals_render ) ) { $role_toggle_id = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'tct-stats-role-goals-month-' ) : ( 'tct-stats-role-goals-month-' . mt_rand( 1000, 999999 ) ); } $html .= '<div class="tct-stats-role">'; if ( '' !== $role_toggle_id ) { $html .= '<input type="checkbox" class="tct-stats-role-toggle-input" id="' . esc_attr( $role_toggle_id ) . '" />'; } $html .= '<div class="tct-stats-week-row tct-stats-role-row">'; if ( '' !== $role_toggle_id ) { $html .= '<label class="tct-stats-role-label tct-stats-role-label-toggle" for="' . esc_attr( $role_toggle_id ) . '" aria-label="Toggle goals" title="Show goals">'; $html .= '<span class="tct-stats-role-caret dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-role-name">' . esc_html( $rname ) . '</span>'; $html .= '</label>'; } else { $html .= '<div class="tct-stats-role-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-role-name">' . esc_html( $rname ) . '</span>'; $html .= '</div>'; } $role_month_bar = $this->render_role_month_spectrum_bar( $rid, $rname, $month_year, $month_num, $month_dates, $r_points, $r_possible, $role_alpha_by_day, $domain_day_points, 'Domain' ); if ( '' !== $role_toggle_id ) { $html .= '<label class="tct-stats-role-bar-toggle" for="' . esc_attr( $role_toggle_id ) . '" aria-label="Toggle goals" title="Show goals">' . $role_month_bar . '</label>'; } else { $html .= $role_month_bar; } $html .= '</div>'; if ( '' !== $role_toggle_id ) { $html .= '<div class="tct-stats-role-goals">'; foreach ( $goals_render as $ginfo ) { $gid = (int) $ginfo['id']; $gname = (string) $ginfo['name']; $g_points = is_array( $ginfo['points'] ) ? $ginfo['points'] : array(); $g_possible = is_array( $ginfo['possible'] ) ? $ginfo['possible'] : array(); $goal_alpha_by_day = array(); foreach ( $month_dates as $dkm ) { $rpts = isset( $r_points[ $dkm ] ) ? (float) $r_points[ $dkm ] : 0.0; $ralpha = isset( $role_alpha_by_day[ $dkm ] ) ? (float) $role_alpha_by_day[ $dkm ] : 0.0; $gpts = isset( $g_points[ $dkm ] ) ? (float) $g_points[ $dkm ] : 0.0; $galpha = 0.0; if ( $gpts > 0.0 && $rpts > 0.0 && $ralpha > 0.0 ) { $galpha = $ralpha * ( $gpts / $rpts ); if ( $galpha < 0.0 ) { $galpha = 0.0; } if ( $galpha > 1.0 ) { $galpha = 1.0; } } $goal_alpha_by_day[ $dkm ] = $galpha; } $html .= '<div class="tct-stats-goal">'; $html .= '<div class="tct-stats-week-row tct-stats-goal-row">'; $html .= '<div class="tct-stats-goal-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-goal-name">' . esc_html( $gname ) . '</span>'; $html .= '</div>'; $html .= $this->render_goal_month_spectrum_bar( $gid, $gname, $month_year, $month_num, $month_dates, $g_points, $g_possible, $goal_alpha_by_day, $r_points, 'Role' ); $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; $html .= '<hr class="tct-divider" />'; $html .= '<div class="tct-stats-section tct-stats-section-year">'; $html .= '<div class="tct-stats-section-header">'; $html .= '<div class="tct-stats-section-title-wrap">'; $html .= '<div class="tct-stats-section-title">Year</div>'; $html .= '<div class="tct-stats-section-toggles" role="group" aria-label="Year expand">'; $html .= '<button type="button" class="tct-stats-expand-toggle" data-tct-stats-expand-toggle="roles" aria-pressed="false">Roles</button>'; $html .= '<button type="button" class="tct-stats-expand-toggle" data-tct-stats-expand-toggle="goals" aria-pressed="false">Goals</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-stats-nav" aria-label="Year navigation">'; $html .= '<a class="tct-stats-nav-btn" href="' . esc_url( $year_prev_url ) . '" aria-label="Previous year" title="Previous year">&lsaquo;</a>'; $html .= '<div class="tct-stats-nav-label">' . esc_html( $year_label ) . '</div>'; $html .= '<a class="tct-stats-nav-btn" href="' . esc_url( $year_next_url ) . '" aria-label="Next year" title="Next year">&rsaquo;</a>'; $html .= '</div>'; $html .= '</div>'; foreach ( $domain_ids as $domain_id ) { $domain_id = (int) $domain_id; $domain_name = ''; if ( 0 === $domain_id ) { $domain_name = 'Unassigned'; } elseif ( isset( $domain_map[ $domain_id ] ) ) { $domain_name = (string) $domain_map[ $domain_id ]; } else { $domain_name = 'Domain ' . (int) $domain_id; } $domain_color = ( 0 === $domain_id ) ? '#64748b' : ( isset( $domain_color_map[ $domain_id ] ) ? (string) $domain_color_map[ $domain_id ] : '#94a3b8' ); $domain_rgb = $this->hex_to_rgb_triplet( $domain_color ); if ( ! is_array( $domain_rgb ) || count( $domain_rgb ) !== 3 ) { $domain_rgb = array( 148, 163, 184 ); } $domain_day_points = ( isset( $points_year[ $domain_id ] ) && is_array( $points_year[ $domain_id ] ) ) ? $points_year[ $domain_id ] : array(); $domain_day_possible = ( isset( $possible_year_map[ $domain_id ] ) && is_array( $possible_year_map[ $domain_id ] ) ) ? $possible_year_map[ $domain_id ] : array(); $roles_for_domain = array(); if ( isset( $roles_by_domain[ $domain_id ] ) && is_array( $roles_by_domain[ $domain_id ] ) ) { foreach ( $roles_by_domain[ $domain_id ] as $rrow ) { if ( ! is_array( $rrow ) ) { continue; } $rid = isset( $rrow['id'] ) ? (int) $rrow['id'] : 0; if ( $rid <= 0 ) { continue; } if ( ! isset( $scored_role_ids[ $rid ] ) ) { continue; } $roles_for_domain[] = $rrow; } } $domain_toggle_id = ''; if ( ! empty( $roles_for_domain ) ) { $domain_toggle_id = wp_unique_id( 'tct-stats-domain-roles-year-' ); } $html .= '<div class="tct-stats-domain" style="--tct-domain-color:' . esc_attr( $domain_color ) . '; --tct-domain-color-rgb:' . esc_attr( (int) $domain_rgb[0] . ',' . (int) $domain_rgb[1] . ',' . (int) $domain_rgb[2] ) . ';">'; if ( '' !== $domain_toggle_id ) { $html .= '<input type="checkbox" class="tct-stats-domain-toggle-input" id="' . esc_attr( $domain_toggle_id ) . '" />'; } $html .= '<div class="tct-stats-week-row tct-stats-domain-row">'; if ( '' !== $domain_toggle_id ) { $html .= '<label class="tct-stats-domain-label tct-stats-domain-label-toggle" for="' . esc_attr( $domain_toggle_id ) . '">'; } else { $html .= '<div class="tct-stats-domain-label">'; } if ( '' !== $domain_toggle_id ) { $html .= '<span class="tct-stats-domain-caret dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>'; } else { $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; } $html .= '<span class="tct-stats-domain-dot" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-domain-name">' . esc_html( $domain_name ) . '</span>'; if ( '' !== $domain_toggle_id ) { $html .= '</label>'; } else { $html .= '</div>'; } $domain_year_bar = $this->render_domain_year_spectrum_bar_static( $domain_id, $domain_name, $year, $year_dates, $domain_day_points, $domain_day_possible ); if ( '' !== $domain_toggle_id ) { $html .= '<label class="tct-stats-domain-bar-toggle" for="' . esc_attr( $domain_toggle_id ) . '">'; $html .= $domain_year_bar; $html .= '</label>'; } else { $html .= $domain_year_bar; } $html .= '</div>'; if ( '' !== $domain_toggle_id ) { $domain_alpha_by_day = array(); foreach ( $year_dates as $dkm ) { $dkm = is_string( $dkm ) ? $dkm : ''; if ( '' === $dkm ) { continue; } $d_pts = isset( $domain_day_points[ $dkm ] ) ? (float) $domain_day_points[ $dkm ] : 0.0; $d_pos = isset( $domain_day_possible[ $dkm ] ) ? (float) $domain_day_possible[ $dkm ] : 0.0; $pct = 0.0; if ( $d_pos > 0.0 ) { $pct = (float) $d_pts / (float) $d_pos; if ( $pct < 0.0 ) { $pct = 0.0; } if ( $pct > 1.0 ) { $pct = 1.0; } } else { $pct = ( $d_pts > 0.0 ) ? 1.0 : 0.0; } $alpha = 0.0; if ( $d_pts > 0.0 ) { $alpha = 0.15 + ( 0.85 * $pct ); if ( $alpha < 0.15 ) { $alpha = 0.15; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $domain_alpha_by_day[ $dkm ] = $alpha; } $html .= '<div class="tct-stats-domain-roles">'; foreach ( $roles_for_domain as $rrow ) { if ( ! is_array( $rrow ) ) { continue; } $rid = isset( $rrow['id'] ) ? (int) $rrow['id'] : 0; $rname = isset( $rrow['role_name'] ) ? (string) $rrow['role_name'] : ''; if ( $rid <= 0 ) { continue; } $r_points = ( isset( $role_points_year[ $domain_id ][ $rid ] ) && is_array( $role_points_year[ $domain_id ][ $rid ] ) ) ? $role_points_year[ $domain_id ][ $rid ] : array(); $r_possible_year = ( isset( $possible_role_year_map[ $rid ] ) && is_array( $possible_role_year_map[ $rid ] ) ) ? $possible_role_year_map[ $rid ] : array(); $r_possible = array(); foreach ( $year_dates as $dkm ) { if ( isset( $r_possible_year[ $dkm ] ) ) { $r_possible[ $dkm ] = (float) $r_possible_year[ $dkm ]; } } $role_alpha_by_day = array(); foreach ( $year_dates as $dkm ) { $dkm = is_string( $dkm ) ? $dkm : ''; if ( '' === $dkm ) { continue; } $d_pts = isset( $domain_day_points[ $dkm ] ) ? (float) $domain_day_points[ $dkm ] : 0.0; $r_pts = isset( $r_points[ $dkm ] ) ? (float) $r_points[ $dkm ] : 0.0; $alpha = 0.0; if ( $r_pts > 0.0 && $d_pts > 0.0 ) { $d_alpha = isset( $domain_alpha_by_day[ $dkm ] ) ? (float) $domain_alpha_by_day[ $dkm ] : 0.0; $alpha = $d_alpha * ( $r_pts / $d_pts ); if ( $alpha < 0.0 ) { $alpha = 0.0; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $role_alpha_by_day[ $dkm ] = $alpha; } $goals_render = array(); if ( isset( $goals_by_role[ $rid ] ) && is_array( $goals_by_role[ $rid ] ) ) { foreach ( $goals_by_role[ $rid ] as $g ) { if ( ! is_array( $g ) ) { continue; } $gid = isset( $g['id'] ) ? (int) $g['id'] : 0; if ( $gid <= 0 ) { continue; } $gname = isset( $g['goal_name'] ) ? (string) $g['goal_name'] : ''; $g_points = ( isset( $goal_points_year[ $domain_id ][ $rid ][ $gid ] ) && is_array( $goal_points_year[ $domain_id ][ $rid ][ $gid ] ) ) ? $goal_points_year[ $domain_id ][ $rid ][ $gid ] : array(); $g_possible = ( isset( $goal_possible_year[ $gid ] ) && is_array( $goal_possible_year[ $gid ] ) ) ? $goal_possible_year[ $gid ] : array(); $possible_total = is_array( $g_possible ) ? (float) array_sum( $g_possible ) : 0.0; $points_total = is_array( $g_points ) ? (float) array_sum( $g_points ) : 0.0; if ( $possible_total <= 0.0 && $points_total <= 0.0 ) { continue; } $goals_render[] = array( 'id' => $gid, 'name' => $gname, 'points' => $g_points, 'possible' => $g_possible, 'possible_total' => $possible_total, 'points_total' => $points_total, ); } } if ( ! empty( $goals_render ) ) { usort( $goals_render, function( $a, $b ) { $ap = isset( $a['possible_total'] ) ? (float) $a['possible_total'] : 0.0; $bp = isset( $b['possible_total'] ) ? (float) $b['possible_total'] : 0.0; if ( $ap !== $bp ) { return $bp <=> $ap; } $ae = isset( $a['points_total'] ) ? (float) $a['points_total'] : 0.0; $be = isset( $b['points_total'] ) ? (float) $b['points_total'] : 0.0; if ( $ae !== $be ) { return $be <=> $ae; } $an = isset( $a['name'] ) ? (string) $a['name'] : ''; $bn = isset( $b['name'] ) ? (string) $b['name'] : ''; return strcasecmp( $an, $bn ); } ); } $role_toggle_id = ''; if ( ! empty( $goals_render ) ) { $role_toggle_id = wp_unique_id( 'tct-stats-role-goals-year-' ); } $html .= '<div class="tct-stats-role">'; if ( '' !== $role_toggle_id ) { $html .= '<input type="checkbox" class="tct-stats-role-toggle-input" id="' . esc_attr( $role_toggle_id ) . '" />'; } $html .= '<div class="tct-stats-week-row tct-stats-role-row">'; if ( '' !== $role_toggle_id ) { $html .= '<label class="tct-stats-role-label tct-stats-role-label-toggle" for="' . esc_attr( $role_toggle_id ) . '">'; $html .= '<span class="tct-stats-role-caret dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-role-name">' . esc_html( $rname ) . '</span>'; $html .= '</label>'; } else { $html .= '<div class="tct-stats-role-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-role-name">' . esc_html( $rname ) . '</span>'; $html .= '</div>'; } $role_bar = $this->render_role_year_spectrum_bar( $rid, $rname, $year, $year_dates, $r_points, $r_possible, $role_alpha_by_day, $domain_day_points, 'Domain' ); if ( '' !== $role_toggle_id ) { $html .= '<label class="tct-stats-role-bar-toggle" for="' . esc_attr( $role_toggle_id ) . '">'; $html .= $role_bar; $html .= '</label>'; } else { $html .= $role_bar; } $html .= '</div>'; if ( '' !== $role_toggle_id ) { $html .= '<div class="tct-stats-role-goals">'; foreach ( $goals_render as $gitem ) { $gid = isset( $gitem['id'] ) ? (int) $gitem['id'] : 0; $gname = isset( $gitem['name'] ) ? (string) $gitem['name'] : ''; $g_points = isset( $gitem['points'] ) && is_array( $gitem['points'] ) ? $gitem['points'] : array(); $g_possible = isset( $gitem['possible'] ) && is_array( $gitem['possible'] ) ? $gitem['possible'] : array(); if ( $gid <= 0 ) { continue; } $goal_alpha_by_day = array(); foreach ( $year_dates as $dkm ) { $dkm = is_string( $dkm ) ? $dkm : ''; if ( '' === $dkm ) { continue; } $r_pts = isset( $r_points[ $dkm ] ) ? (float) $r_points[ $dkm ] : 0.0; $g_pts = isset( $g_points[ $dkm ] ) ? (float) $g_points[ $dkm ] : 0.0; $alpha = 0.0; if ( $g_pts > 0.0 && $r_pts > 0.0 ) { $r_alpha = isset( $role_alpha_by_day[ $dkm ] ) ? (float) $role_alpha_by_day[ $dkm ] : 0.0; $alpha = $r_alpha * ( $g_pts / $r_pts ); if ( $alpha < 0.0 ) { $alpha = 0.0; } if ( $alpha > 1.0 ) { $alpha = 1.0; } } $goal_alpha_by_day[ $dkm ] = $alpha; } $html .= '<div class="tct-stats-goal">'; $html .= '<div class="tct-stats-week-row tct-stats-goal-row">'; $html .= '<div class="tct-stats-goal-label">'; $html .= '<span class="tct-stats-caret-placeholder" aria-hidden="true"></span>'; $html .= '<span class="tct-stats-goal-name">' . esc_html( $gname ) . '</span>'; $html .= '</div>'; $html .= $this->render_goal_year_spectrum_bar( $gid, $gname, $year, $year_dates, $g_points, $g_possible, $goal_alpha_by_day, $r_points, 'Role' ); $html .= '</div>'; $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; } $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; } private function render_domain_modal( $redirect_here ) { $html = ''; $html .= '<div class="tct-modal-overlay" data-tct-domain-overlay hidden="hidden"></div>'; $html .= '<div class="tct-modal" data-tct-domain-modal hidden="hidden" role="dialog" aria-modal="true" aria-labelledby="tct-domain-modal-title">'; $html .= '<div class="tct-modal-inner">'; $html .= '<div class="tct-modal-header">'; $html .= '<h3 id="tct-domain-modal-title" data-tct-domain-modal-title>Add domain</h3>'; $html .= '<button type="button" class="tct-modal-close" data-tct-domain-modal-close aria-label="Close">&times;</button>'; $html .= '</div>'; $html .= '<div class="tct-modal-body">'; $html .= '<div class="tct-domain-heatmap-viewtabs" data-tct-domain-heatmap-viewtabs role="tablist" aria-label="Heatmap views">'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="week" role="tab" aria-selected="false">Week</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab" data-tct-domain-heatmap-view="month" role="tab" aria-selected="false">Month</button>'; $html .= '<button type="button" class="tct-domain-heatmap-viewtab tct-domain-heatmap-viewtab-active" data-tct-domain-heatmap-view="year" role="tab" aria-selected="true">Year</button>'; $html .= '</div>'; $html .= '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" data-tct-domain-form>'; $html .= '<input type="hidden" name="action" value="tct_domain_upsert" />'; $html .= '<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_here ) . '" />'; $html .= '<input type="hidden" name="domain_id" value="0" data-tct-domain-id />'; $html .= wp_nonce_field( 'tct_domain_upsert', '_wpnonce', true, false ); $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-domain-name">Domain name</label>'; $html .= '<input id="tct-domain-name" type="text" name="domain_name" value="" placeholder="e.g., Religious Life" data-tct-domain-name required="required" />'; $html .= '</div>'; $html .= '<div class="tct-form-row">'; $html .= '<label class="tct-label" for="tct-domain-color">Color</label>'; $html .= '<input id="tct-domain-color" type="color" name="domain_color" value="#2271b1" data-tct-domain-color />'; $html .= '<p class="tct-muted">Used as the accent color on the Dashboard.</p>'; $html .= '</div>'; $html .= '<div class="tct-modal-actions">'; $html .= '<button type="submit" class="button button-primary">Save</button>'; $html .= '<button type="button" class="button" data-tct-domain-modal-cancel>Cancel</button>'; $html .= '</div>'; $html .= '</form>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; } public function maybe_enqueue_mobile_assets() { if ( is_admin() ) { return; } global $post; if ( ! $post || ! isset( $post->post_content ) ) { return; } if ( ! has_shortcode( $post->post_content, self::MOBILE_SHORTCODE ) ) { return; } wp_enqueue_style( 'dashicons' ); $dash_css_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'dashboard.css' ) : null; $dash_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'dashboard.js' ) : null; $dash_css_ver = ( $dash_css_path && file_exists( $dash_css_path ) ) ? (string) filemtime( $dash_css_path ) : TCT_VERSION; $dash_js_ver = ( $dash_js_path && file_exists( $dash_js_path ) ) ? (string) filemtime( $dash_js_path ) : TCT_VERSION; $shared_js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'tct-shared.js' ) : null; $shared_js_ver = ( $shared_js_path && file_exists( $shared_js_path ) ) ? (string) filemtime( $shared_js_path ) : TCT_VERSION; wp_enqueue_style( 'tct-dashboard', TCT_PLUGIN_URL . 'dashboard.css', array(), $dash_css_ver ); wp_enqueue_script( 'tct-shared-js', TCT_PLUGIN_URL . 'tct-shared.js', array(), $shared_js_ver, true ); wp_enqueue_script( 'tct-dashboard-js', TCT_PLUGIN_URL . 'dashboard.js', array( 'jquery', 'jquery-ui-sortable', 'tct-shared-js' ), $dash_js_ver, true ); $vitality_plants_js = array(); if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'get_vitality_plants' ) ) { $plants = TCT_Utils::get_vitality_plants(); $previews = method_exists( 'TCT_Utils', 'get_vitality_plant_previews' ) ? TCT_Utils::get_vitality_plant_previews( 'large' ) : array(); if ( is_array( $plants ) ) { foreach ( $plants as $pname ) { $pname = trim( (string) $pname ); if ( '' === $pname ) { continue; } $vitality_plants_js[] = array( 'name' => $pname, 'previewUrl' => ( isset( $previews[ $pname ] ) && is_string( $previews[ $pname ] ) ) ? $previews[ $pname ] : '', ); } } } $experimental_settings_schema = null; if ( current_user_can( 'manage_options' ) && class_exists( 'TCT_Admin' ) && is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) && TCT_Admin::is_experimental_features_enabled() ) { $installed_schema = (int) get_option( TCT_Admin::OPTION_NAME_SETTINGS_SCHEMA_VERSION, 0 ); $target_schema = (int) TCT_Admin::SETTINGS_SCHEMA_VERSION; $experimental_settings_schema = array( 'installedVersion' => (int) $installed_schema, 'targetVersion' => (int) $target_schema, 'needsMigration' => (bool) ( $installed_schema < $target_schema ), ); } wp_localize_script( 'tct-dashboard-js', 'tctDashboard', array( 'ajaxUrl' => admin_url( 'admin-ajax.php', 'relative' ), 'roleOrderNonce' => wp_create_nonce( 'tct_role_order' ), 'quickCompleteNonce' => wp_create_nonce( 'tct_quick_complete' ), 'failGoalNonce' => wp_create_nonce( 'tct_fail_goal' ), 'goalHistoryNonce' => wp_create_nonce( 'tct_goal_history' ), 'undoCompletionNonce' => wp_create_nonce( 'tct_undo_completion' ), 'goalHeatmapNonce' => wp_create_nonce( 'tct_goal_heatmap' ), 'domainHeatmapNonce' => wp_create_nonce( 'tct_domain_heatmap' ), 'domainYearbarNonce' => wp_create_nonce( 'tct_domain_yearbar' ), 'domainMonthbarNonce' => wp_create_nonce( 'tct_domain_monthbar' ), 'domainWeekbarNonce' => wp_create_nonce( 'tct_domain_weekbar' ), 'domainMonthHeatmapNonce' => wp_create_nonce( 'tct_domain_month_heatmap' ), 'domainWeekHeatmapNonce' => wp_create_nonce( 'tct_domain_week_heatmap' ), 'sleepStateNonce' => wp_create_nonce( 'tct_sleep_state' ), 'sleepBedtimeNonce' => wp_create_nonce( 'tct_sleep_save_bedtime' ), 'sleepWaketimeNonce' => wp_create_nonce( 'tct_sleep_save_waketime' ), 'sleepClearCycleNonce' => wp_create_nonce( 'tct_sleep_clear_cycle' ), 'debugGoalBoundsNonce' => ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) ? wp_create_nonce( 'tct_debug_goal_bounds' ) : '', 'experimentalSettingsSchemaStatusNonce' => current_user_can( 'manage_options' ) ? wp_create_nonce( 'tct_experimental_settings_schema_status' ) : '', 'experimentalSettingsSchemaMigrateNonce' => current_user_can( 'manage_options' ) ? wp_create_nonce( 'tct_experimental_settings_schema_migrate' ) : '', 'experimentalSettingsSchema' => $experimental_settings_schema, 'startOfWeek' => (int) get_option( 'start_of_week', 1 ), 'vitalityPlants' => $vitality_plants_js, 'i18n' => array( 'roleOrderError' => 'Could not save role order. Please refresh and try again.', 'quickCompleteError' => 'Could not complete task. Please try again.', 'goalHistoryError' => 'Could not load history. Please try again.', 'undoCompletionError' => 'Could not undo completion. Please try again.', 'undoCompletionConfirm' => 'Undo this completion?', 'goalHeatmapError' => 'Could not load completion map. Please try again.', 'domainHeatmapError' => 'Could not load domain heatmap. Please try again.', 'domainWeekHeatmapError' => 'Could not load week heatmap. Please try again.', 'archivedGoalsSearchError' => 'Could not search archived goals. Please try again.', 'archivedGoalsSearchNoResults' => 'No archived goals found.', 'archivedGoalsSearchStart' => 'Start typing to search archived goals.', ), 'composite' => $this->tct_composite_feature_config(), 'features' => array( 'experimental' => ( is_callable( array( 'TCT_Admin', 'is_experimental_features_enabled' ) ) ) ? (bool) TCT_Admin::is_experimental_features_enabled() : false, 'compositeGoals' => $this->tct_composite_goals_enabled(), ), ) ); $css_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'mobile.css' ) : null; $js_path = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'mobile.js' ) : null; $css_ver = ( $css_path && file_exists( $css_path ) ) ? (string) filemtime( $css_path ) : TCT_VERSION; $js_ver = ( $js_path && file_exists( $js_path ) ) ? (string) filemtime( $js_path ) : TCT_VERSION; wp_enqueue_style( 'tct-mobile', TCT_PLUGIN_URL . 'mobile.css', array( 'tct-dashboard' ), $css_ver ); wp_enqueue_script( 'tct-mobile-js', TCT_PLUGIN_URL . 'mobile.js', array( 'tct-dashboard-js', 'tct-shared-js' ), $js_ver, true ); $user_id = get_current_user_id(); $domains_js = array(); $roles_js = array(); if ( $user_id > 0 ) { $domains = $this->get_domains( $user_id ); if ( is_array( $domains ) ) { foreach ( $domains as $d ) { if ( ! is_array( $d ) ) { continue; } $did = isset( $d['id'] ) ? (int) $d['id'] : 0; if ( $did <= 0 ) { continue; } $domains_js[] = array( 'id' => $did, 'name' => isset( $d['domain_name'] ) ? (string) $d['domain_name'] : '', 'color' => isset( $d['color_hex'] ) ? (string) sanitize_hex_color( $d['color_hex'] ) : '', 'sort_order' => isset( $d['sort_order'] ) ? (int) $d['sort_order'] : 0, ); } } $roles = $this->get_roles( $user_id ); if ( is_array( $roles ) ) { foreach ( $roles as $r ) { if ( ! is_array( $r ) ) { continue; } $rid = isset( $r['id'] ) ? (int) $r['id'] : 0; if ( $rid <= 0 ) { continue; } $roles_js[] = array( 'id' => $rid, 'domain_id' => isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0, 'name' => isset( $r['role_name'] ) ? (string) $r['role_name'] : '', 'sort_order' => isset( $r['sort_order'] ) ? (int) $r['sort_order'] : 0, ); } } } wp_localize_script( 'tct-mobile-js', 'tctMobile', array( 'ajaxUrl' => admin_url( 'admin-ajax.php', 'relative' ), 'searchNonce' => wp_create_nonce( 'tct_mobile_search' ), 'compositeParentCompleteNonce' => wp_create_nonce( 'tct_complete_composite_parent' ), 'compositeParentCompleteConfirm' => 'Complete all child goals for this parent? Blocked children will be skipped.', 'compositeParentCompleteError' => 'Could not complete all child goals. Please try again.', 'domains' => $domains_js, 'roles' => $roles_js, 'composite' => $this->tct_composite_feature_config(), 'features' => array( 'domainBrowseSwipe' => true, 'favoritesSwipe' => true, 'compositeGoals' => $this->tct_composite_goals_enabled(), ), ) ); $mobile_fast_ui_css = <<<'CSS'
.tct-mobile .tct-mobile-result{transition:box-shadow 0.2s ease,border-color 0.2s ease,opacity 0.16s ease,transform 0.16s ease,height 0.18s ease,max-height 0.18s ease,margin 0.18s ease,border-width 0.18s ease}.tct-mobile .tct-mobile-result.tct-mobile-result-pending-remove{opacity:0;transform:scale(0.98);height:0 !important;max-height:0 !important;margin-top:0 !important;margin-bottom:0 !important;border-width:0 !important;overflow:hidden !important;pointer-events:none}.tct-mobile .tct-mobile-result.tct-mobile-result-pending-remove>.tct-mobile-result-header,.tct-mobile .tct-mobile-result.tct-mobile-result-pending-remove>.tct-mobile-result-body{padding-top:0 !important;padding-bottom:0 !important;margin-top:0 !important;margin-bottom:0 !important;border-width:0 !important}.tct-mobile .tct-mobile-row-complete-btn,.tct-mobile .tct-domain-goal .tct-goal-action-btn{min-height:44px;touch-action:manipulation;-webkit-touch-callout:none}.tct-mobile .tct-mobile-row-complete-btn{min-width:64px;height:44px;padding:0 12px}.tct-mobile .tct-mobile-row-complete-btn>*,.tct-mobile .tct-domain-goal .tct-goal-action-btn>*{pointer-events:none}.tct-mobile .tct-mobile-row-complete-btn[data-tct-fast-pending='1'],.tct-mobile .tct-domain-goal .tct-goal-action-btn[data-tct-fast-pending='1']{cursor:wait}
CSS;
wp_add_inline_style( 'tct-mobile', $mobile_fast_ui_css );
$mobile_fast_ui_js = <<<'JS'
(function(){
    "use strict";
    if (typeof window === "undefined" || typeof document === "undefined") {
        return;
    }
    var root = document.querySelector(".tct-mobile");
    if (!root || typeof window.fetch !== "function") {
        return;
    }
    window.TCT = window.TCT || {};
    if (window.TCT.__tctMobileFastUiBound) {
        return;
    }
    window.TCT.__tctMobileFastUiBound = true;

    var active = Object.create(null);
    var originalFetch = window.fetch.bind(window);

    function parseParams(body) {
        var params = {};
        if (!body) {
            return params;
        }
        if (typeof body === "string") {
            body.split("&").forEach(function (part) {
                if (!part) {
                    return;
                }
                var pieces = part.split("=");
                var key = pieces.shift() || "";
                var value = pieces.join("=");
                key = decodeURIComponent(String(key).replace(/\+/g, " "));
                value = decodeURIComponent(String(value).replace(/\+/g, " "));
                params[key] = value;
            });
            return params;
        }
        if (typeof URLSearchParams !== "undefined" && body instanceof URLSearchParams) {
            body.forEach(function (value, key) {
                params[key] = value;
            });
            return params;
        }
        if (typeof FormData !== "undefined" && body instanceof FormData) {
            body.forEach(function (value, key) {
                if (typeof value === "string") {
                    params[key] = value;
                }
            });
            return params;
        }
        return params;
    }

    function parseRequest(input, init) {
        var body = init && typeof init === "object" && Object.prototype.hasOwnProperty.call(init, "body") ? init.body : null;
        var params = parseParams(body);
        var action = params.action ? String(params.action) : "";
        var goalId = parseInt(params.goal_id || "0", 10);
        if (!isFinite(goalId)) {
            goalId = 0;
        }
        if (!action || goalId <= 0) {
            return null;
        }
        if (action !== "tct_quick_complete" && action !== "tct_fail_goal" && action !== "tct_complete_composite_parent" && action !== "tct_composite_complete_parent") {
            return null;
        }
        return {
            action: action,
            goalId: goalId
        };
    }

    function makeKey(info) {
        return info.action + ":" + String(info.goalId);
    }

    function findRows(goalId) {
        return Array.prototype.slice.call(root.querySelectorAll('.tct-mobile-result[data-goal-id="' + String(goalId) + '"]'));
    }

    function findButtons(goalId) {
        var selector = [
            '[data-tct-mobile-row-complete][data-goal-id="' + String(goalId) + '"]',
            '[data-tct-composite-parent-complete][data-goal-id="' + String(goalId) + '"]',
            '.tct-domain-goal[data-goal-id="' + String(goalId) + '"] .tct-goal-action-btn',
            '.tct-domain-goal[data-goal-id="' + String(goalId) + '"] [data-tct-complete-goal]',
            '.tct-domain-goal[data-goal-id="' + String(goalId) + '"] [data-tct-timer-complete]',
            '.tct-domain-goal[data-goal-id="' + String(goalId) + '"] [data-tct-start-timer]'
        ].join(', ');
        return Array.prototype.slice.call(root.querySelectorAll(selector));
    }

    function startPending(info) {
        var key = makeKey(info);
        if (active[key]) {
            return active[key];
        }
        var token = {
            key: key,
            goalId: info.goalId,
            action: info.action,
            rows: [],
            buttons: []
        };

        findRows(info.goalId).forEach(function (row) {
            var rowHeight = 0;
            if (!row || row.classList.contains("tct-mobile-result-pending-remove")) {
                return;
            }
            rowHeight = Math.max(0, row.offsetHeight || 0);
            row.style.height = String(rowHeight) + "px";
            row.style.maxHeight = String(rowHeight) + "px";
            row.style.overflow = "hidden";
            row.offsetHeight;
            row.classList.add("tct-mobile-result-pending-remove");
            row.setAttribute("data-tct-fast-pending", "1");
            token.rows.push(row);
        });

        findButtons(info.goalId).forEach(function (button) {
            if (!button) {
                return;
            }
            button.__tctFastPendingWasDisabled = !!button.disabled;
            button.setAttribute("data-tct-fast-pending", "1");
            button.setAttribute("aria-busy", "true");
            try {
                button.disabled = true;
            } catch (err) {}
            token.buttons.push(button);
        });

        active[key] = token;
        return token;
    }

    function restorePending(token) {
        if (!token) {
            return;
        }
        token.rows.forEach(function (row) {
            if (!row) {
                return;
            }
            row.classList.remove("tct-mobile-result-pending-remove");
            row.removeAttribute("data-tct-fast-pending");
            row.style.height = "";
            row.style.maxHeight = "";
            row.style.overflow = "";
        });
        token.buttons.forEach(function (button) {
            if (!button) {
                return;
            }
            button.removeAttribute("data-tct-fast-pending");
            button.removeAttribute("aria-busy");
            try {
                button.disabled = !!button.__tctFastPendingWasDisabled;
            } catch (err) {}
            try {
                delete button.__tctFastPendingWasDisabled;
            } catch (err) {
                button.__tctFastPendingWasDisabled = undefined;
            }
        });
        delete active[token.key];
    }

    function finalizePending(token) {
        if (!token) {
            return;
        }
        delete active[token.key];
        window.setTimeout(function () {
            token.rows.forEach(function (row) {
                if (row && row.parentNode && row.classList.contains("tct-mobile-result-pending-remove")) {
                    row.parentNode.removeChild(row);
                }
            });
        }, 180);
    }

    document.addEventListener("click", function (event) {
        var target = event.target && event.target.closest ? event.target.closest('[data-tct-fast-pending="1"]') : null;
        if (!target || !root.contains(target)) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === "function") {
            event.stopImmediatePropagation();
        }
    }, true);

    window.fetch = function (input, init) {
        var info = parseRequest(input, init);
        var token = info ? startPending(info) : null;
        return originalFetch(input, init).then(function (response) {
            if (!token) {
                return response;
            }
            return response.clone().json().then(function (payload) {
                var ok = !!(response && response.ok && payload && payload.success);
                if (ok) {
                    finalizePending(token);
                } else {
                    restorePending(token);
                }
                return response;
            }).catch(function () {
                if (!response || !response.ok) {
                    restorePending(token);
                }
                return response;
            });
        }).catch(function (error) {
            if (token) {
                restorePending(token);
            }
            throw error;
        });
    };
})();
JS;
wp_add_inline_script( 'tct-mobile-js', $mobile_fast_ui_js );
$mobile_composite_expand_persist_js = <<<'JS'
(function(){
    "use strict";
    if (typeof window === "undefined" || typeof document === "undefined") {
        return;
    }
    var root = document.querySelector(".tct-mobile");
    if (!root) {
        return;
    }
    var results = root.querySelector("[data-tct-mobile-results]");
    if (!results) {
        return;
    }
    window.TCT = window.TCT || {};
    if (window.TCT.__tctMobileCompositeExpandPersistBound) {
        return;
    }
    window.TCT.__tctMobileCompositeExpandPersistBound = true;

    var expandedParentIds = Object.create(null);
    var applyQueued = false;

    function parseGoalId(value) {
        var goalId = parseInt(value || "0", 10);
        if (!isFinite(goalId) || goalId <= 0) {
            return 0;
        }
        return goalId;
    }

    function isCompositeParentRow(row) {
        return !!(row && row.getAttribute && row.getAttribute("data-tct-composite-parent") === "1");
    }

    function updateExpandedStateFromRow(row) {
        var goalId;
        if (!isCompositeParentRow(row)) {
            return;
        }
        goalId = parseGoalId(row.getAttribute("data-goal-id"));
        if (goalId <= 0) {
            return;
        }
        if (row.classList.contains("tct-mobile-result-expanded")) {
            expandedParentIds[goalId] = true;
        } else {
            delete expandedParentIds[goalId];
        }
    }

    function applyExpandedParents() {
        applyQueued = false;
        Array.prototype.forEach.call(results.querySelectorAll('.tct-mobile-result[data-tct-composite-parent="1"]'), function (row) {
            var goalId = parseGoalId(row.getAttribute("data-goal-id"));
            if (goalId <= 0 || !expandedParentIds[goalId]) {
                return;
            }
            if (row.classList.contains("tct-mobile-result-pending-remove")) {
                return;
            }
            row.classList.add("tct-mobile-result-expanded");
        });
    }

    function queueApplyExpandedParents() {
        if (applyQueued) {
            return;
        }
        applyQueued = true;
        window.requestAnimationFrame(applyExpandedParents);
    }

    Array.prototype.forEach.call(results.querySelectorAll('.tct-mobile-result[data-tct-composite-parent="1"].tct-mobile-result-expanded'), function (row) {
        updateExpandedStateFromRow(row);
    });

    results.addEventListener("click", function (event) {
        var row = event.target && event.target.closest ? event.target.closest('.tct-mobile-result[data-tct-composite-parent="1"]') : null;
        if (!row || !results.contains(row)) {
            return;
        }
        window.setTimeout(function () {
            updateExpandedStateFromRow(row);
        }, 0);
    });

    results.addEventListener("keydown", function (event) {
        var toggle = event.target && event.target.closest ? event.target.closest("[data-tct-mobile-toggle]") : null;
        var key = event.key || "";
        var row;
        if (!toggle || !results.contains(toggle)) {
            return;
        }
        if (key !== "Enter" && key !== " " && key !== "Spacebar") {
            return;
        }
        row = toggle.closest(".tct-mobile-result");
        if (!isCompositeParentRow(row)) {
            return;
        }
        window.setTimeout(function () {
            updateExpandedStateFromRow(row);
        }, 0);
    });

    var observer = new MutationObserver(function (records) {
        var shouldApply = false;
        records.forEach(function (record) {
            if (record && record.type === "childList") {
                shouldApply = true;
            }
        });
        if (shouldApply) {
            queueApplyExpandedParents();
        }
    });
    observer.observe(results, { childList: true, subtree: true });
})();
JS;
wp_add_inline_script( 'tct-mobile-js', $mobile_composite_expand_persist_js ); } public function render_mobile_shortcode( $atts ) { $user_id = get_current_user_id(); global $wpdb; $goals_table = TCT_DB::table_goals(); $goal_count = 0; if ( $user_id > 0 ) { $goal_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1", $user_id ) ); } $today_ymd = ( new DateTimeImmutable( 'now', TCT_Utils::wp_timezone() ) )->format( 'Y-m-d' ); $html = '<div class="tct-mobile"' . $this->tct_composite_root_attrs_html() . '>'; $html .= '<div class="tct-mobile-header">'; $html .= '<div class="tct-mobile-header-viewport" data-tct-mobile-header-viewport>'; $html .= '<div class="tct-mobile-header-track" data-tct-mobile-header-track>'; $html .= '<div class="tct-mobile-header-pane tct-mobile-header-pane-default" data-tct-mobile-header-pane="default">'; $html .= $this->render_mobile_reward_widget( $user_id ); $html .= '<div class="tct-mobile-chips" data-tct-mobile-chips>'; $html .= '<button type="button" class="tct-mobile-chip tct-mobile-chip-critical" data-tct-mobile-chip="critical" aria-pressed="false"><span class="tct-mobile-chip-text">Critical</span> <span class="tct-mobile-chip-count" data-tct-chip-count="critical">(0)</span></button>'; $html .= '<button type="button" class="tct-mobile-chip tct-mobile-chip-risk" data-tct-mobile-chip="risk" aria-pressed="false"><span class="tct-mobile-chip-text">At Risk</span> <span class="tct-mobile-chip-count" data-tct-chip-count="risk">(0)</span></button>'; $html .= '<button type="button" class="tct-mobile-chip tct-mobile-chip-vitality-low" data-tct-mobile-chip="vit_low" aria-pressed="false"><span class="tct-mobile-chip-text">Vitality &lt;30</span> <span class="tct-mobile-chip-count" data-tct-chip-count="vit_low">(0)</span></button>'; $html .= '<button type="button" class="tct-mobile-chip tct-mobile-chip-vitality-mid" data-tct-mobile-chip="vit_mid" aria-pressed="false"><span class="tct-mobile-chip-text">Vitality 30&#8211;60</span> <span class="tct-mobile-chip-count" data-tct-chip-count="vit_mid">(0)</span></button>'; $html .= '</div>'; $html .= '<div class="tct-mobile-search">'; $html .= '<div class="tct-mobile-search-field">'; $html .= '<input type="text" class="tct-mobile-search-input" data-tct-mobile-search placeholder="Type to search goals..." autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">'; $html .= '<button type="button" class="tct-mobile-clear-btn" data-tct-mobile-clear aria-label="Clear search">&times;</button>'; $html .= '</div>'; $html .= '<button type="button" class="tct-mobile-go-btn" data-tct-mobile-go>Go</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-mobile-header-pane tct-mobile-header-pane-domain" data-tct-mobile-header-pane="domain">'; $html .= '<div class="tct-mobile-domain-grid" data-tct-mobile-domain-grid hidden></div>'; $html .= '<div class="tct-mobile-domain-selected-row" data-tct-mobile-domain-selected-row hidden></div>'; $html .= '<div class="tct-mobile-role-chips" data-tct-mobile-role-chips hidden></div>'; $html .= '<div class="tct-mobile-search">'; $html .= '<div class="tct-mobile-search-field">'; $html .= '<input type="text" class="tct-mobile-search-input" data-tct-mobile-search placeholder="Type to search goals..." autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">'; $html .= '<button type="button" class="tct-mobile-clear-btn" data-tct-mobile-clear aria-label="Clear search">&times;</button>'; $html .= '</div>'; $html .= '<button type="button" class="tct-mobile-go-btn" data-tct-mobile-go>Go</button>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; $html .= '<div class="tct-mobile-favorites-bar" data-tct-mobile-favorites-bar hidden>'; $html .= '<button type="button" class="tct-mobile-favorites-back" data-tct-mobile-favorites-back aria-label="Back">&#x2039;</button>'; $html .= '<div class="tct-mobile-favorites-title">Favorites</div>'; $html .= '</div>'; $html .= '<div class="tct-dashboard tct-mobile-dashboard" data-tct-today="' . esc_attr( $today_ymd ) . '">'; $html .= '<div class="tct-domain-goals" data-tct-mobile-results>'; if ( $goal_count <= 0 ) { $html .= '<div class="tct-mobile-no-results">You currently have no goals.</div>'; } else { $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false; $html .= $this->mobile_render_default_daily_html( $user_id, $connected ); } $html .= '</div>'; $html .= '</div>'; if ( $user_id > 0 ) { $redirect_here = TCT_Utils::current_url(); $label_names = $this->get_label_names_for_picker( $user_id ); $domains = $this->get_domains( $user_id ); $roles = $this->get_roles( $user_id ); $role_map = $this->get_role_map( $roles ); $domain_map = $this->get_domain_map( $domains ); $goal_rows = $wpdb->get_results( $wpdb->prepare( "SELECT domain_id, role_id, importance FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1", $user_id ), ARRAY_A ); $role_domain_map = array(); foreach ( $roles as $r ) { if ( ! isset( $r['id'] ) || ! isset( $r['domain_id'] ) ) { continue; } $rid = (int) $r['id']; $did = (int) $r['domain_id']; if ( $rid > 0 ) { $role_domain_map[ $rid ] = $did; } } $stats_domains = array(); foreach ( $domains as $d ) { if ( ! isset( $d['id'] ) || ! isset( $d['domain_name'] ) ) { continue; } $did = (int) $d['id']; if ( $did <= 0 ) { continue; } $stats_domains[ $did ] = array( 'name' => (string) $d['domain_name'], 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } $stats_roles = array(); foreach ( $roles as $r ) { if ( ! isset( $r['id'] ) || ! isset( $r['role_name'] ) ) { continue; } $rid = (int) $r['id']; if ( $rid <= 0 ) { continue; } $did = isset( $r['domain_id'] ) ? (int) $r['domain_id'] : 0; $stats_roles[ $rid ] = array( 'name' => (string) $r['role_name'], 'domain_id'=> $did, 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } foreach ( $goal_rows as $gr ) { $rid = isset( $gr['role_id'] ) ? (int) $gr['role_id'] : 0; $did = isset( $gr['domain_id'] ) ? (int) $gr['domain_id'] : 0; if ( $rid > 0 && isset( $role_map[ $rid ] ) ) { $did = (int) $role_map[ $rid ]['domain_id']; } if ( $did <= 0 || $rid <= 0 ) { continue; } if ( ! isset( $stats_domains[ $did ] ) ) { $stats_domains[ $did ] = array( 'name' => isset( $domain_map[ $did ] ) ? (string) $domain_map[ $did ] : 'Domain', 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } if ( ! isset( $stats_roles[ $rid ] ) ) { $stats_roles[ $rid ] = array( 'name' => 'Role', 'domain_id' => $did, 'total' => 0, 'i5' => 0, 'i4plus' => 0, ); } $importance_val = isset( $gr['importance'] ) ? (int) $gr['importance'] : 0; $stats_domains[ $did ]['total']++; $stats_roles[ $rid ]['total']++; if ( 5 === $importance_val ) { $stats_domains[ $did ]['i5']++; $stats_roles[ $rid ]['i5']++; } if ( $importance_val >= 4 ) { $stats_domains[ $did ]['i4plus']++; $stats_roles[ $rid ]['i4plus']++; } } $goal_modal_stats = array( 'roleDomainMap' => $role_domain_map, 'domains' => $stats_domains, 'roles' => $stats_roles, 'thresholds' => array( 'i5Count' => 4, 'i5Pct' => 0.3, 'i4Pct' => 0.6, ), ); $html .= $this->render_goal_modal( $label_names, $domains, $roles, $redirect_here, $goal_modal_stats ); $html .= $this->render_goal_history_modal(); $html .= $this->render_domain_heatmap_modal(); $html .= $this->render_role_modal( $domains, $redirect_here ); $html .= $this->render_domain_modal( $redirect_here ); } $html .= '</div>'; return $html; } public function handle_mobile_heartbeat_ajax() { $user_id = get_current_user_id(); if ( $user_id <= 0 ) { TCT_Utils::send_json_error( array( 'logged_out' => true ), 401 ); } TCT_Utils::send_json_success( array( 'nonces' => array( 'searchNonce' => wp_create_nonce( 'tct_mobile_search' ), 'quickCompleteNonce' => wp_create_nonce( 'tct_quick_complete' ), 'failGoalNonce' => wp_create_nonce( 'tct_fail_goal' ), 'goalHistoryNonce' => wp_create_nonce( 'tct_goal_history' ), 'undoCompletionNonce' => wp_create_nonce( 'tct_undo_completion' ), ), ) ); } public function handle_mobile_reward_refresh_ajax() { TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' ); $user_id = get_current_user_id(); if ( $user_id <= 0 ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } $html = $this->render_mobile_reward_widget( $user_id ); TCT_Utils::send_json_success( array( 'html' => $html ) ); } public function maybe_hide_admin_bar_on_mobile() { if ( ! is_singular() ) { return; } global $post; if ( ! $post || ! is_a( $post, 'WP_Post' ) ) { return; } if ( has_shortcode( $post->post_content, self::MOBILE_SHORTCODE ) ) { add_filter( 'show_admin_bar', '__return_false' ); } } public function handle_mobile_daily_default_ajax() { TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' ); $user_id = get_current_user_id(); if ( $user_id <= 0 ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } global $wpdb; $goals_table = TCT_DB::table_goals(); $goal_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$goals_table} WHERE user_id = %d AND is_tracked = 1", $user_id ) ); if ( $goal_count <= 0 ) { TCT_Utils::send_json_success( array( 'html' => '<div class="tct-mobile-no-results">You currently have no goals.</div>', ) ); } $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false; $html = $this->mobile_render_default_daily_html( $user_id, $connected ); TCT_Utils::send_json_success( array( 'html' => (string) $html, ) ); } public function handle_mobile_ledger_ajax() {
        TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' );
        $user_id = get_current_user_id();

        if ( $user_id <= 0 ) {
            TCT_Utils::send_json_success( array( 'html' => '<p>Not logged in.</p>' ) );
        }

        if ( ! class_exists( 'TCT_Ledger' ) ) {
            TCT_Utils::send_json_success( array( 'html' => '<p>Ledger not available.</p>' ) );
        }

        $tz = TCT_Utils::wp_timezone();
        $now_tz = new DateTimeImmutable( 'now', $tz );
        $now_utc = $now_tz->setTimezone( new DateTimeZone( 'UTC' ) );
        $balance = TCT_Ledger::get_balance( $user_id );
        $range_end_utc = TCT_Utils::dt_to_mysql_utc( $now_utc );
        $range_start_utc = TCT_Utils::dt_to_mysql_utc( $now_utc->sub( new DateInterval( 'P7D' ) ) );
        $in_7d = TCT_Ledger::get_points_in_range( $user_id, $range_start_utc, $range_end_utc );
        $transactions = TCT_Ledger::get_transactions( $user_id, 100 );

        $html = '<div class="tct-mobile-ledger-summary">';
        $html .= '<div class="tct-mobile-ledger-balance">Balance: <strong>' . esc_html( number_format_i18n( $balance ) ) . '</strong></div>';
        $html .= '<div class="tct-mobile-ledger-sub">Last 7 days: <strong>' . esc_html( number_format_i18n( $in_7d ) ) . '</strong></div>';
        $html .= '</div>';

        if ( empty( $transactions ) ) {
            $html .= '<p style="text-align:center;color:#8c8f94;">No transactions yet.</p>';
            TCT_Utils::send_json_success( array( 'html' => $html ) );
        }

        $html .= '<div class="tct-mobile-ledger-rows">';

        foreach ( $transactions as $row ) {
            $occurred_utc = isset( $row['occurred_at'] ) ? (string) $row['occurred_at'] : '';
            $display_date = '';

            try {
                $dt = new DateTimeImmutable( $occurred_utc, new DateTimeZone( 'UTC' ) );
                $dt = $dt->setTimezone( $tz );
                $display_date = $dt->format( 'M j, g:ia' );
            } catch ( Exception $e ) {
                $display_date = $occurred_utc;
            }

            $goal_name = isset( $row['goal_name'] ) && '' !== $row['goal_name'] ? (string) $row['goal_name'] : ( isset( $row['label_name'] ) ? (string) $row['label_name'] : '' );
            $goal_id = isset( $row['goal_id'] ) ? (int) $row['goal_id'] : 0;
            $completion_id = isset( $row['completion_id'] ) ? (int) $row['completion_id'] : 0;
            $points = isset( $row['points'] ) ? (int) $row['points'] : 0;
            $points_class = $points >= 0 ? 'tct-mobile-ledger-pts-pos' : 'tct-mobile-ledger-pts-neg';
            $points_str = ( $points >= 0 ? '+' : '' ) . (string) $points;

            $meta = $this->tct_ledger_event_display_meta( $row, $tz );
            $type_label = isset( $meta['typeLabel'] ) ? (string) $meta['typeLabel'] : '';
            $type_class = isset( $meta['typeClass'] ) ? sanitize_html_class( (string) $meta['typeClass'] ) : 'neutral';
            $summary_lines = isset( $meta['summaryLines'] ) && is_array( $meta['summaryLines'] ) ? array_values( array_filter( $meta['summaryLines'] ) ) : array();

            $undo_html = '';
            if ( isset( $row['event_type'] ) && 'completion' === (string) $row['event_type'] && $completion_id > 0 && $goal_id > 0 ) {
                $undo_html = '<button type="button" class="tct-history-undo-btn" data-tct-ledger-undo="1"';
                $undo_html .= ' data-completion-id="' . esc_attr( $completion_id ) . '" data-goal-id="' . esc_attr( $goal_id ) . '"';
                $undo_html .= ' title="Undo completion" aria-label="Undo completion">';
                $undo_html .= '<span class="dashicons dashicons-undo" aria-hidden="true"></span>';
                $undo_html .= '</button>';
            }

            $html .= '<div class="tct-mobile-ledger-row">';
            $html .= '<div class="tct-mobile-ledger-row-left">';
            $html .= '<div class="tct-mobile-ledger-goal">' . esc_html( $goal_name ?: '--' ) . '</div>';
            $html .= '<div class="tct-mobile-ledger-date">' . esc_html( $display_date ) . '</div>';

            if ( '' !== $type_label ) {
                $html .= '<div class="tct-mobile-ledger-meta"><span class="tct-history-event-pill tct-history-event-pill-' . esc_attr( $type_class ) . '">' . esc_html( $type_label ) . '</span></div>';
            }

            if ( ! empty( $summary_lines ) ) {
                $html .= '<div class="tct-mobile-ledger-note">' . esc_html( implode( ' ', $summary_lines ) ) . '</div>';
            }

            $html .= '</div>';
            $html .= '<div class="tct-mobile-ledger-row-right" style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">';
            $html .= '<div class="tct-mobile-ledger-row-pts ' . esc_attr( $points_class ) . '">' . esc_html( $points_str ) . '</div>';

            if ( '' !== $undo_html ) {
                $html .= '<div class="tct-mobile-ledger-row-actions" style="display:flex;justify-content:flex-end;width:100%;">' . $undo_html . '</div>';
            }

            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        TCT_Utils::send_json_success( array( 'html' => $html ) );
    }
 private function render_mobile_reward_widget( $user_id ) { $user_id = (int) $user_id; if ( $user_id <= 0 ) { return ''; } $has_reward = false; $reward = null; $progress = null; if ( class_exists( 'TCT_Reward' ) ) { if ( ! method_exists( 'TCT_Reward', 'is_enabled' ) || TCT_Reward::is_enabled() ) { $reward = TCT_Reward::get_active_reward( $user_id ); if ( is_array( $reward ) && ! empty( $reward ) ) { $progress = TCT_Reward::compute_reward_progress( $user_id, $reward ); if ( is_array( $progress ) ) { $has_reward = true; } } } } $balance = TCT_Ledger::get_balance( $user_id ); $balance_label = number_format_i18n( (int) $balance ); $stats_html = ''; $stats_tz = TCT_Utils::wp_timezone(); $stats_utc = new DateTimeZone( 'UTC' ); try { $stats_now = new DateTimeImmutable( 'now', $stats_tz ); $stats_now_utc = $stats_now->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_today_start = $stats_now->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_sow = (int) get_option( 'start_of_week', 1 ); if ( $stats_sow < 0 || $stats_sow > 6 ) { $stats_sow = 1; } $stats_dow = (int) $stats_now->format( 'w' ); $stats_wdiff = ( $stats_dow - $stats_sow + 7 ) % 7; $stats_week_start = $stats_now->modify( '-' . $stats_wdiff . ' days' )->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_month_start = $stats_now->modify( 'first day of this month' )->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_today = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_today_start, $stats_now_utc ); $stats_week = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_week_start, $stats_now_utc ); $stats_month = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_month_start, $stats_now_utc ); $yesterday = $stats_now->modify( '-1 day' ); $stats_yest_start = $yesterday->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_yest_end = $yesterday->setTime( 23, 59, 59 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_yesterday = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_yest_start, $stats_yest_end ); $stats_last_week_start = $stats_now->modify( '-' . ( $stats_wdiff + 7 ) . ' days' )->setTime( 0, 0, 0 )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_week_end = $stats_now->modify( '-7 days' )->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_week = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_last_week_start, $stats_last_week_end ); $stats_dom = (int) $stats_now->format( 'j' ); $last_month_first_obj = $stats_now->modify( 'first day of last month' )->setTime( 0, 0, 0 ); $last_month_days = (int) $last_month_first_obj->format( 't' ); $target_dom = min( $stats_dom, $last_month_days ); $last_month_target_obj = $last_month_first_obj->setDate( (int) $last_month_first_obj->format( 'Y' ), (int) $last_month_first_obj->format( 'm' ), $target_dom )->setTime( (int) $stats_now->format( 'H' ), (int) $stats_now->format( 'i' ), (int) $stats_now->format( 's' ) ); $stats_last_month_start = $last_month_first_obj->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_month_end = $last_month_target_obj->setTimezone( $stats_utc )->format( 'Y-m-d H:i:s' ); $stats_last_month = TCT_Economy_Normalizer::get_normalized_earned_lost( $user_id, $stats_last_month_start, $stats_last_month_end ); $stats_html .= '<div class="tct-mobile-reward-stats">'; $stats_html .= '<table class="tct-mobile-reward-stats-table">'; $stats_html .= '<tbody>'; $stats_pairs = array( array( 'This Week', $stats_week, 'Last Week', $stats_last_week ), ); foreach ( $stats_pairs as $pair ) { $cur_lbl = $pair[0]; $cur_data = $pair[1]; $prev_lbl = $pair[2]; $prev_data = $pair[3]; $cur_earned = (int) $cur_data['earned']; $cur_lost = (int) $cur_data['lost']; $prev_earned = (int) $prev_data['earned']; $prev_lost = (int) $prev_data['lost']; $cur_cls = ''; $prev_cls = ''; if ( $cur_earned > $prev_earned ) { $cur_cls = ' class="tct-mobile-stats-row-winning"'; } elseif ( $prev_earned > $cur_earned ) { $prev_cls = ' class="tct-mobile-stats-row-losing"'; } $stats_html .= '<tr' . $cur_cls . '>'; $stats_html .= '<td class="tct-mobile-reward-stats-period">' . esc_html( $cur_lbl ) . '</td>'; $stats_html .= '<td class="tct-mobile-reward-stats-earned">+' . esc_html( number_format_i18n( $cur_earned ) ) . '</td>'; $stats_html .= '<td class="tct-mobile-reward-stats-lost">' . esc_html( number_format_i18n( $cur_lost ) ) . '</td>'; $stats_html .= '</tr>'; $stats_html .= '<tr' . $prev_cls . '>'; $stats_html .= '<td class="tct-mobile-reward-stats-period">' . esc_html( $prev_lbl ) . '</td>'; $stats_html .= '<td class="tct-mobile-reward-stats-earned">+' . esc_html( number_format_i18n( $prev_earned ) ) . '</td>'; $stats_html .= '<td class="tct-mobile-reward-stats-lost">' . esc_html( number_format_i18n( $prev_lost ) ) . '</td>'; $stats_html .= '</tr>'; } $stats_html .= '</tbody></table>'; $stats_html .= '</div>'; } catch ( Exception $e ) { } if ( ! $has_reward ) { $html = '<div class="tct-mobile-reward tct-mobile-reward-simple">'; $html .= '<div class="tct-mobile-reward-points-only">'; $html .= 'Points: <strong>' . esc_html( $balance_label ) . '</strong>'; $html .= '</div>'; $html .= $stats_html; $html .= '</div>'; return $html; } $target = isset( $progress['target_points'] ) ? (int) $progress['target_points'] : 0; $is_earned = ( isset( $progress['is_earned'] ) && $progress['is_earned'] ); $errors = ( isset( $progress['errors'] ) && is_array( $progress['errors'] ) ) ? $progress['errors'] : array(); $title = isset( $reward['title'] ) ? trim( (string) $reward['title'] ) : ''; if ( '' === $title ) { $title = 'Reward'; } $display_title = $title; if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) { if ( mb_strlen( $display_title ) > 32 ) { $display_title = mb_substr( $display_title, 0, 29 ) . '...'; } } elseif ( strlen( $display_title ) > 32 ) { $display_title = substr( $display_title, 0, 29 ) . '...'; } $target_label = number_format_i18n( (int) $target ); $pct_raw = ( $target > 0 ) ? ( (float) $balance / (float) $target ) * 100.0 : 0.0; $pct_rounded = round( $pct_raw, 1 ); if ( abs( $pct_rounded - round( $pct_rounded ) ) < 0.05 ) { $pct_label = number_format_i18n( (int) round( $pct_rounded ), 0 ); } else { $pct_label = number_format_i18n( (float) $pct_rounded, 1 ); } $bar_width = max( 0, min( 100, $pct_raw ) ); $attachment_id = isset( $reward['attachment_id'] ) ? (int) $reward['attachment_id'] : 0; $image_url = ''; $image_url_full = ''; if ( $attachment_id > 0 ) { $use_progress_variants = class_exists( 'TCT_Admin' ) && TCT_Admin::is_progress_images_enabled(); if ( $use_progress_variants && is_callable( array( 'TCT_Reward', 'get_progress_variant_url' ) ) ) { $variant_url = TCT_Reward::get_progress_variant_url( $user_id, $pct_raw, 'medium' ); if ( $variant_url ) { $image_url = $variant_url; } $variant_url_full = TCT_Reward::get_progress_variant_url( $user_id, $pct_raw, 'large' ); if ( $variant_url_full ) { $image_url_full = $variant_url_full; } } if ( '' === $image_url ) { $image_url = wp_get_attachment_image_url( $attachment_id, 'medium' ); if ( ! $image_url ) { $image_url = wp_get_attachment_url( $attachment_id ); } } if ( '' === $image_url_full ) { $image_url_full = wp_get_attachment_image_url( $attachment_id, 'full' ); if ( ! $image_url_full ) { $image_url_full = $image_url; } } } $cls = 'tct-mobile-reward'; if ( $is_earned ) { $cls .= ' tct-mobile-reward-earned'; } if ( $image_url ) { $cls .= ' tct-mobile-reward-has-image'; } $html = '<div class="' . esc_attr( $cls ) . '">'; if ( $image_url ) { $html .= '<div class="tct-mobile-reward-image" data-tct-mobile-reward-zoom role="button" tabindex="0">'; $html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" data-full-src="' . esc_url( $image_url_full ) . '" />'; $html .= '</div>'; } $html .= '<div class="tct-mobile-reward-points-area" data-tct-mobile-ledger-trigger role="button" tabindex="0">'; $html .= '<div class="tct-mobile-reward-title">' . esc_html( $display_title ) . '</div>'; $html .= '<div class="tct-mobile-reward-left">'; if ( $target > 0 && empty( $errors ) ) { $html .= '<div class="tct-mobile-reward-ratio">'; $html .= '<span class="tct-mobile-reward-ratio-current">' . esc_html( $balance_label ) . '</span>'; $html .= '<span class="tct-mobile-reward-ratio-sep"> / ' . esc_html( $target_label ) . ' pts</span>'; $html .= '</div>'; $html .= '<div class="tct-mobile-reward-progress">'; $html .= '<div class="tct-mobile-reward-progress-track">'; $html .= '<div class="tct-mobile-reward-progress-bar" style="width:' . esc_attr( $bar_width ) . '%;"></div>'; $html .= '</div>'; $html .= '<span class="tct-mobile-reward-pct">' . esc_html( $pct_label ) . '%</span>'; $html .= '</div>'; } $html .= $stats_html; $html .= '</div>'; $html .= '</div>'; $html .= '</div>'; return $html; }     private function mobile_render_default_daily_html( $user_id, $connected ) {
        $user_id = (int) $user_id;
        $connected = (bool) $connected;
        if ( $user_id <= 0 ) {
            return '';
        }

        $all_items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected );
        if ( empty( $all_items ) ) {
            return '<div class="tct-mobile-no-results">You currently have no goals.</div>';
        }

        $matches = array();
        foreach ( $all_items as $it ) {
            if ( ! is_array( $it ) || ! isset( $it['tile_goal'] ) || ! is_array( $it['tile_goal'] ) ) {
                continue;
            }

            $tile_goal = $it['tile_goal'];
            $goal_type = isset( $tile_goal['goal_type'] ) && is_string( $tile_goal['goal_type'] ) ? (string) $tile_goal['goal_type'] : 'positive';
            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_type' ) ) ) {
                if ( TCT_Utils::is_negative_goal_type( $goal_type ) ) {
                    continue;
                }
            }

            $unit = isset( $tile_goal['unit'] ) && is_string( $tile_goal['unit'] ) ? (string) $tile_goal['unit'] : '';
            $span = isset( $tile_goal['period_span'] ) ? (int) $tile_goal['period_span'] : 1;
            if ( $span < 1 ) {
                $span = 1;
            }
            if ( 'day' !== $unit || 1 !== $span ) {
                continue;
            }

            $target = isset( $tile_goal['target'] ) ? (int) $tile_goal['target'] : 0;
            if ( $target <= 0 ) {
                continue;
            }

            $achieved = isset( $tile_goal['goal_count'] ) ? (int) $tile_goal['goal_count'] : 0;
            if ( $achieved < 0 ) {
                $achieved = 0;
            }

            $is_paused = ! empty( $tile_goal['availability_is_paused'] );
            if ( ! $is_paused && $achieved >= $target ) {
                continue;
            }

            $ppc = isset( $tile_goal['points_per_completion'] ) ? (int) $tile_goal['points_per_completion'] : 0;
            $goal_name = isset( $it['goal_name'] ) ? (string) $it['goal_name'] : '';

            $matches[] = array(
                'ppc' => $ppc,
                'vitality' => isset( $it['vitality'] ) ? (int) $it['vitality'] : 100,
                'time_left' => isset( $it['time_remaining_seconds'] ) ? (int) $it['time_remaining_seconds'] : 0,
                'goal_name' => $goal_name,
                'is_paused' => $is_paused ? 1 : 0,
                'tile_goal' => $tile_goal,
            );
        }

        if ( empty( $matches ) ) {
            return '<div class="tct-mobile-no-results">All daily goals complete.</div>';
        }

        $daily_order_map = $this->tct_get_goal_order_map( $user_id, 'daily' );
        usort( $matches, function ( $a, $b ) use ( $daily_order_map ) {
            $a_paused = isset( $a['is_paused'] ) ? (int) $a['is_paused'] : 0;
            $b_paused = isset( $b['is_paused'] ) ? (int) $b['is_paused'] : 0;
            if ( $a_paused !== $b_paused ) {
                return $a_paused ? 1 : -1;
            }

            $a_gid = isset( $a['tile_goal']['goal_id'] ) ? (int) $a['tile_goal']['goal_id'] : 0;
            $b_gid = isset( $b['tile_goal']['goal_id'] ) ? (int) $b['tile_goal']['goal_id'] : 0;
            $a_pos = isset( $daily_order_map[ $a_gid ] ) ? (int) $daily_order_map[ $a_gid ] : PHP_INT_MAX;
            $b_pos = isset( $daily_order_map[ $b_gid ] ) ? (int) $daily_order_map[ $b_gid ] : PHP_INT_MAX;
            if ( $a_pos !== $b_pos ) {
                return $a_pos <=> $b_pos;
            }

            $a_ppc = isset( $a['ppc'] ) ? (int) $a['ppc'] : 0;
            $b_ppc = isset( $b['ppc'] ) ? (int) $b['ppc'] : 0;
            if ( $a_ppc !== $b_ppc ) {
                return $b_ppc <=> $a_ppc;
            }

            $a_vit = isset( $a['vitality'] ) ? (int) $a['vitality'] : 100;
            $b_vit = isset( $b['vitality'] ) ? (int) $b['vitality'] : 100;
            if ( $a_vit !== $b_vit ) {
                return $a_vit <=> $b_vit;
            }

            $a_time = isset( $a['time_left'] ) ? (int) $a['time_left'] : 0;
            $b_time = isset( $b['time_left'] ) ? (int) $b['time_left'] : 0;
            if ( $a_time !== $b_time ) {
                return $a_time <=> $b_time;
            }

            $a_name = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : '';
            $b_name = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : '';
            if ( '' !== $a_name && '' !== $b_name ) {
                $cmp = strcasecmp( $a_name, $b_name );
                if ( 0 !== $cmp ) {
                    return $cmp;
                }
            }

            return 0;
        } );

        $matches = array_slice( $matches, 0, 200 );
        $html_out = '';
        foreach ( $matches as $item ) {
            if ( ! isset( $item['tile_goal'] ) || ! is_array( $item['tile_goal'] ) ) {
                continue;
            }

            $tile_goal = $item['tile_goal'];
            $goal_name = isset( $tile_goal['goal_name'] ) ? (string) $tile_goal['goal_name'] : '';
            $status_key = isset( $tile_goal['status_key'] ) ? (string) $tile_goal['status_key'] : 'on-track';
            if ( '' === $goal_name ) {
                $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0;
                $goal_name = 'Goal #' . $goal_id;
            }

            $vitality = isset( $item['vitality'] ) ? (int) $item['vitality'] : 100;
            if ( isset( $tile_goal['vitality_data'] ) && is_array( $tile_goal['vitality_data'] ) && isset( $tile_goal['vitality_data']['vitality'] ) ) {
                $vitality = (int) $tile_goal['vitality_data']['vitality'];
            }
            if ( $vitality < 0 ) {
                $vitality = 0;
            } elseif ( $vitality > 100 ) {
                $vitality = 100;
            }

            $html_out .= '<div' . $this->mobile_result_wrapper_attrs_html( $tile_goal, $status_key ) . '>';
            $html_out .= '<div class="tct-mobile-result-header" data-tct-mobile-toggle role="button" tabindex="0">';
            $html_out .= $this->mobile_vitality_badge_html( $vitality );
            $html_out .= $this->mobile_row_info_html( $tile_goal, $goal_name );
            $html_out .= $this->mobile_row_complete_button_html( $tile_goal );
            $html_out .= '</div>';
            $html_out .= '<div class="tct-mobile-result-body">';
            $html_out .= $this->render_cockpit_goal_tile( $tile_goal, $connected );
            $html_out .= '</div>';
            $html_out .= '</div>';
        }

        return (string) $html_out;
    } public function handle_mobile_chip_counts_ajax() { TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' ); $user_id = get_current_user_id(); if ( $user_id <= 0 ) { TCT_Utils::send_json_success( array( 'counts' => array( 'critical' => 0, 'risk' => 0, 'vit_low' => 0, 'vit_mid' => 0, ), ) ); } $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false; $items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected ); $counts = array( 'critical' => 0, 'risk' => 0, 'vit_low' => 0, 'vit_mid' => 0, ); foreach ( $items as $it ) { if ( ! is_array( $it ) ) { continue; } $status_key = isset( $it['status_key'] ) ? (string) $it['status_key'] : 'on-track'; if ( 'completed' === $status_key ) { continue; } $tg = isset( $it['tile_goal'] ) && is_array( $it['tile_goal'] ) ? $it['tile_goal'] : array(); $chip_unit = isset( $tg['unit'] ) && is_string( $tg['unit'] ) ? (string) $tg['unit'] : ''; $chip_span = isset( $tg['period_span'] ) ? (int) $tg['period_span'] : 1; if ( 'day' === $chip_unit && 1 === $chip_span ) { continue; } $vitality = isset( $it['vitality'] ) ? (int) $it['vitality'] : 100; if ( 'critical' === $status_key ) { $counts['critical']++; } elseif ( 'risk' === $status_key ) { $counts['risk']++; } if ( $vitality < 30 ) { $counts['vit_low']++; } elseif ( $vitality >= 30 && $vitality <= 60 ) { $counts['vit_mid']++; } } TCT_Utils::send_json_success( array( 'counts' => $counts, ) ); } public function handle_mobile_chip_filter_ajax() {
        TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' );
        $user_id = get_current_user_id();
        if ( $user_id <= 0 ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $filter = isset( $_POST['filter'] ) ? sanitize_text_field( wp_unslash( $_POST['filter'] ) ) : '';
        $filter = trim( $filter );
        if ( '' === $filter ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $allowed = array( 'critical', 'risk', 'vit_low', 'vit_mid' );
        if ( ! in_array( $filter, $allowed, true ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false;
        $items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected );
        if ( empty( $items ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $matches = array();
        foreach ( $items as $it ) {
            if ( ! is_array( $it ) ) {
                continue;
            }

            $status_key = isset( $it['status_key'] ) ? (string) $it['status_key'] : 'on-track';
            if ( 'completed' === $status_key ) {
                continue;
            }

            $tg = isset( $it['tile_goal'] ) && is_array( $it['tile_goal'] ) ? $it['tile_goal'] : array();
            $filt_unit = isset( $tg['unit'] ) && is_string( $tg['unit'] ) ? (string) $tg['unit'] : '';
            $filt_span = isset( $tg['period_span'] ) ? (int) $tg['period_span'] : 1;
            if ( 'day' === $filt_unit && 1 === $filt_span ) {
                continue;
            }

            $vitality = isset( $it['vitality'] ) ? (int) $it['vitality'] : 100;
            $ok = false;
            if ( 'critical' === $filter ) {
                $ok = ( 'critical' === $status_key );
            } elseif ( 'risk' === $filter ) {
                $ok = ( 'risk' === $status_key );
            } elseif ( 'vit_low' === $filter ) {
                $ok = ( $vitality < 30 );
            } elseif ( 'vit_mid' === $filter ) {
                $ok = ( $vitality >= 30 && $vitality <= 60 );
            }

            if ( $ok ) {
                $matches[] = $it;
            }
        }

        if ( empty( $matches ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        usort(
            $matches,
            function ( $a, $b ) use ( $filter ) {
                $a_vit = isset( $a['vitality'] ) ? (int) $a['vitality'] : 100;
                $b_vit = isset( $b['vitality'] ) ? (int) $b['vitality'] : 100;
                $a_time = isset( $a['time_remaining_seconds'] ) ? (int) $a['time_remaining_seconds'] : 0;
                $b_time = isset( $b['time_remaining_seconds'] ) ? (int) $b['time_remaining_seconds'] : 0;
                $a_name = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : '';
                $b_name = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : '';

                if ( 'critical' === $filter || 'risk' === $filter ) {
                    if ( $a_time !== $b_time ) {
                        return $a_time <=> $b_time;
                    }
                    if ( $a_vit !== $b_vit ) {
                        return $a_vit <=> $b_vit;
                    }
                } else {
                    if ( $a_vit !== $b_vit ) {
                        return $a_vit <=> $b_vit;
                    }
                    if ( $a_time !== $b_time ) {
                        return $a_time <=> $b_time;
                    }
                }

                return strcasecmp( $a_name, $b_name );
            }
        );

        $matches = array_slice( $matches, 0, 150 );
        $html_out = '';
        foreach ( $matches as $it ) {
            if ( ! isset( $it['tile_goal'] ) || ! is_array( $it['tile_goal'] ) ) {
                continue;
            }

            $tile_goal = $it['tile_goal'];
            $html_out .= $this->tct_render_mobile_result_html(
                $tile_goal,
                $connected,
                isset( $it['vitality'] ) ? (int) $it['vitality'] : null,
                isset( $it['goal_name'] ) ? (string) $it['goal_name'] : ''
            );
        }

        TCT_Utils::send_json_success( array( 'html' => (string) $html_out ) );
    } public function handle_mobile_domain_counts_ajax() { TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' ); $user_id = get_current_user_id(); if ( $user_id <= 0 ) { TCT_Utils::send_json_success( array( 'counts' => array() ) ); } $domains = $this->get_domains( $user_id ); $counts = array(); foreach ( (array) $domains as $d ) { if ( ! is_array( $d ) || ! isset( $d['id'] ) ) { continue; } $did = (int) $d['id']; if ( $did <= 0 ) { continue; } $counts[ (string) $did ] = 0; } if ( empty( $counts ) ) { TCT_Utils::send_json_success( array( 'counts' => array() ) ); } $roles = $this->get_roles( $user_id ); $role_map = $this->get_role_map( $roles ); $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false; $items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected ); $role_counts = array(); foreach ( (array) $items as $it ) { if ( ! is_array( $it ) ) { continue; } $status_key = isset( $it['status_key'] ) ? (string) $it['status_key'] : 'on-track'; if ( 'completed' === $status_key ) { continue; } if ( ! isset( $it['tile_goal'] ) || ! is_array( $it['tile_goal'] ) ) { continue; } $tile_goal = $it['tile_goal']; $rid = isset( $tile_goal['role_id'] ) ? (int) $tile_goal['role_id'] : 0; $did = isset( $tile_goal['domain_id'] ) ? (int) $tile_goal['domain_id'] : 0; if ( $rid > 0 && isset( $role_map[ $rid ] ) && isset( $role_map[ $rid ]['domain_id'] ) ) { $did = (int) $role_map[ $rid ]['domain_id']; } if ( $did <= 0 ) { continue; } if ( isset( $counts[ (string) $did ] ) ) { $counts[ (string) $did ]++; } if ( $rid > 0 ) { $rkey = (string) $rid; if ( ! isset( $role_counts[ $rkey ] ) ) { $role_counts[ $rkey ] = 0; } $role_counts[ $rkey ]++; } } TCT_Utils::send_json_success( array( 'counts' => $counts, 'role_counts' => $role_counts, ) ); } public function handle_mobile_domain_filter_ajax() { TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' ); $user_id = get_current_user_id(); if ( $user_id <= 0 ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } $domain_id = isset( $_POST['domain_id'] ) ? (int) $_POST['domain_id'] : 0; if ( $domain_id <= 0 ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } $role_ids_raw = array(); if ( isset( $_POST['role_ids'] ) ) { $role_ids_raw = $_POST['role_ids']; } if ( ! is_array( $role_ids_raw ) ) { $role_ids_raw = array( $role_ids_raw ); } $role_ids = array(); foreach ( (array) $role_ids_raw as $rid_raw ) { $rid = (int) $rid_raw; if ( $rid > 0 ) { $role_ids[] = $rid; } } $role_ids = array_values( array_unique( $role_ids ) ); $domains = $this->get_domains( $user_id ); $domain_map = $this->get_domain_map( $domains ); if ( ! isset( $domain_map[ $domain_id ] ) ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } $roles = $this->get_roles( $user_id ); $role_map = $this->get_role_map( $roles ); if ( ! empty( $role_ids ) ) { $valid_roles = array(); foreach ( $role_ids as $rid ) { if ( isset( $role_map[ $rid ] ) && isset( $role_map[ $rid ]['domain_id'] ) && (int) $role_map[ $rid ]['domain_id'] === $domain_id ) { $valid_roles[] = (int) $rid; } } $role_ids = array_values( array_unique( $valid_roles ) ); } $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false; $items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected ); if ( empty( $items ) ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } $tz = TCT_Utils::wp_timezone(); $now_tz = new DateTimeImmutable( 'now', $tz ); $now_ts_sort = (int) $now_tz->getTimestamp(); $max_ppc_by_role = array(); $display_goals = array(); foreach ( $items as $it ) { if ( ! is_array( $it ) || ! isset( $it['tile_goal'] ) || ! is_array( $it['tile_goal'] ) ) { continue; } $tile_goal = $it['tile_goal']; $rid = isset( $tile_goal['role_id'] ) ? (int) $tile_goal['role_id'] : 0; $did = isset( $tile_goal['domain_id'] ) ? (int) $tile_goal['domain_id'] : 0; if ( $rid > 0 && isset( $role_map[ $rid ] ) && isset( $role_map[ $rid ]['domain_id'] ) ) { $did = (int) $role_map[ $rid ]['domain_id']; } if ( $did !== $domain_id ) { continue; } if ( $rid > 0 && ! isset( $role_map[ $rid ] ) ) { $rid = 0; } $tile_goal['domain_id'] = $did; $tile_goal['role_id'] = $rid; $ppc = isset( $tile_goal['points_per_completion'] ) ? (int) $tile_goal['points_per_completion'] : 0; if ( $ppc < 0 ) { $ppc = 0; } if ( ! isset( $max_ppc_by_role[ $rid ] ) || $ppc > (int) $max_ppc_by_role[ $rid ] ) { $max_ppc_by_role[ $rid ] = $ppc; } $status_key = isset( $tile_goal['status_key'] ) ? (string) $tile_goal['status_key'] : ( isset( $it['status_key'] ) ? (string) $it['status_key'] : 'on-track' ); if ( ! empty( $role_ids ) ) { if ( $rid <= 0 ) { continue; } if ( ! in_array( $rid, $role_ids, true ) ) { continue; } } $display_goals[] = $tile_goal; } if ( empty( $display_goals ) ) { TCT_Utils::send_json_success( array( 'html' => '' ) ); } foreach ( $display_goals as $ix => $g_tmp ) { if ( ! is_array( $g_tmp ) ) { continue; } $status_key = isset( $g_tmp['status_key'] ) ? (string) $g_tmp['status_key'] : ''; $vitality = 100; if ( isset( $g_tmp['vitality_data'] ) && is_array( $g_tmp['vitality_data'] ) && isset( $g_tmp['vitality_data']['vitality'] ) ) { $vitality = (int) $g_tmp['vitality_data']['vitality']; } if ( $vitality < 0 ) { $vitality = 0; } if ( $vitality > 100 ) { $vitality = 100; } $vitality_urg = 1.0 - ( (float) $vitality / 100.0 ); if ( $vitality_urg < 0.0 ) { $vitality_urg = 0.0; } if ( $vitality_urg > 1.0 ) { $vitality_urg = 1.0; } $ppc_val = isset( $g_tmp['points_per_completion'] ) ? (int) $g_tmp['points_per_completion'] : 0; if ( $ppc_val < 0 ) { $ppc_val = 0; } $ppc_norm = 0.0; $rid_for_ppc = isset( $g_tmp['role_id'] ) ? (int) $g_tmp['role_id'] : 0; $max_ppc_role = isset( $max_ppc_by_role[ $rid_for_ppc ] ) ? (int) $max_ppc_by_role[ $rid_for_ppc ] : 0; if ( $max_ppc_role > 0 ) { $ppc_norm = (float) $ppc_val / (float) $max_ppc_role; } if ( $ppc_norm < 0.0 ) { $ppc_norm = 0.0; } if ( $ppc_norm > 1.0 ) { $ppc_norm = 1.0; } $recency_norm = 0.0; if ( isset( $g_tmp['vitality_data'] ) && is_array( $g_tmp['vitality_data'] ) && isset( $g_tmp['vitality_data']['components'] ) && is_array( $g_tmp['vitality_data']['components'] ) ) { $c = $g_tmp['vitality_data']['components']; $time_since_last_s = isset( $c['time_since_last_s'] ) ? (float) $c['time_since_last_s'] : 0.0; $spacing_days = isset( $c['spacing_days'] ) ? (float) $c['spacing_days'] : 0.0; if ( $time_since_last_s < 0.0 ) { $time_since_last_s = 0.0; } if ( $spacing_days > 0.0001 ) { $spacing_s = $spacing_days * 86400.0; if ( $spacing_s < 1.0 ) { $spacing_s = 1.0; } $t = $time_since_last_s / $spacing_s; $recency_norm = $t / 4.0; } } if ( $recency_norm <= 0.0 ) { $last_ts = isset( $g_tmp['last_completed_ts'] ) ? (int) $g_tmp['last_completed_ts'] : 0; if ( $last_ts > 0 && $now_ts_sort > $last_ts ) { $days_since = ( (float) ( $now_ts_sort - $last_ts ) ) / 86400.0; $recency_norm = $days_since / 14.0; } } if ( $recency_norm < 0.0 ) { $recency_norm = 0.0; } if ( $recency_norm > 1.0 ) { $recency_norm = 1.0; } $w_recency = 0.10; if ( 'critical' === $status_key ) { $w_vitality = 0.45; $w_ppc = 0.45; } else { $w_vitality = 0.585; $w_ppc = 0.315; } $urgency = ( $w_vitality * $vitality_urg ) + ( $w_ppc * $ppc_norm ) + ( $w_recency * $recency_norm ); if ( 'completed' === $status_key ) { $urgency = 0.0; } $display_goals[ $ix ]['urgency_score'] = (float) $urgency; } usort( $display_goals, function ( $a, $b ) { $a_paused = ! empty( $a['availability_is_paused'] ); $b_paused = ! empty( $b['availability_is_paused'] ); if ( $a_paused !== $b_paused ) { return $a_paused ? 1 : -1; } $a_status_key = isset( $a['status_key'] ) ? (string) $a['status_key'] : ''; $b_status_key = isset( $b['status_key'] ) ? (string) $b['status_key'] : ''; $a_completed = ( ! $a_paused && 'completed' === $a_status_key ); $b_completed = ( ! $b_paused && 'completed' === $b_status_key ); if ( $a_completed !== $b_completed ) { return $a_completed ? 1 : -1; } $ar = isset( $a['status_rank'] ) ? (int) $a['status_rank'] : 0; $br = isset( $b['status_rank'] ) ? (int) $b['status_rank'] : 0; if ( $ar !== $br ) { return $br <=> $ar; } $a_gt = isset( $a['goal_type'] ) ? strtolower( trim( (string) $a['goal_type'] ) ) : 'positive'; $b_gt = isset( $b['goal_type'] ) ? strtolower( trim( (string) $b['goal_type'] ) ) : 'positive'; $a_neg = ( 'never' === $a_gt || 'harm_reduction' === $a_gt ) ? 1 : 0; $b_neg = ( 'never' === $b_gt || 'harm_reduction' === $b_gt ) ? 1 : 0; if ( $a_neg !== $b_neg ) { return $a_neg <=> $b_neg; } $as = isset( $a['urgency_score'] ) ? (float) $a['urgency_score'] : 0.0; $bs = isset( $b['urgency_score'] ) ? (float) $b['urgency_score'] : 0.0; if ( $as !== $bs ) { return $bs <=> $as; } $ats = isset( $a['last_completed_ts'] ) ? (int) $a['last_completed_ts'] : 0; $bts = isset( $b['last_completed_ts'] ) ? (int) $b['last_completed_ts'] : 0; if ( $ats !== $bts ) { if ( 'completed' === $a_status_key && 'completed' === $b_status_key ) { if ( 0 === $ats ) { return 1; } if ( 0 === $bts ) { return -1; } return $bts <=> $ats; } if ( 0 === $ats ) { return -1; } if ( 0 === $bts ) { return 1; } return $ats <=> $bts; } $an = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : ''; $bn = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : ''; $cmp = strcasecmp( $an, $bn ); if ( 0 !== $cmp ) { return $cmp; } $aid = isset( $a['goal_id'] ) ? (int) $a['goal_id'] : 0; $bid = isset( $b['goal_id'] ) ? (int) $b['goal_id'] : 0; if ( $aid !== $bid ) { return $aid <=> $bid; } return 0; } ); $display_goals = array_slice( $display_goals, 0, 200 ); $html_out = ''; foreach ( $display_goals as $tile_goal ) { if ( ! is_array( $tile_goal ) ) { continue; } $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0; $goal_name = isset( $tile_goal['goal_name'] ) ? (string) $tile_goal['goal_name'] : ''; if ( '' === $goal_name ) { $goal_name = 'Goal #' . $goal_id; } $status_key = isset( $tile_goal['status_key'] ) ? (string) $tile_goal['status_key'] : 'on-track'; $vitality = 100; if ( isset( $tile_goal['vitality_data'] ) && is_array( $tile_goal['vitality_data'] ) && isset( $tile_goal['vitality_data']['vitality'] ) ) { $vitality = (int) $tile_goal['vitality_data']['vitality']; } if ( $vitality < 0 ) { $vitality = 0; } elseif ( $vitality > 100 ) { $vitality = 100; } $html_out .= '<div' . $this->mobile_result_wrapper_attrs_html( $tile_goal, $status_key ) . '>'; $html_out .= '<div class="tct-mobile-result-header" data-tct-mobile-toggle role="button" tabindex="0">'; $html_out .= $this->mobile_vitality_badge_html( $vitality ); $html_out .= $this->mobile_row_info_html( $tile_goal, $goal_name ); $html_out .= $this->mobile_row_complete_button_html( $tile_goal ); $html_out .= '</div>'; $html_out .= '<div class="tct-mobile-result-body">'; $html_out .= $this->render_cockpit_goal_tile( $tile_goal, $connected ); $html_out .= '</div>'; $html_out .= '</div>'; } TCT_Utils::send_json_success( array( 'html' => (string) $html_out, ) ); } private function tct_build_mobile_goal_item_from_row( $row, $user_id, $now_tz, $completions_table, $surface = 'mobile', $extra_tile_goal = array() ) {
        global $wpdb;

        if ( ! is_array( $row ) || ! isset( $row['id'] ) ) {
            return array();
        }

        $user_id = (int) $user_id;
        if ( $user_id <= 0 ) {
            return array();
        }

        if ( ! ( $now_tz instanceof DateTimeImmutable ) ) {
            $tz_fallback = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
            $now_tz = new DateTimeImmutable( 'now', $tz_fallback );
        }

        $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0;
        if ( $goal_id <= 0 ) {
            return array();
        }

        $goal_name = isset( $row['goal_name'] ) ? (string) $row['goal_name'] : '';
        if ( '' === $goal_name && isset( $row['label_name'] ) ) {
            $goal_name = (string) $row['label_name'];
        }
        if ( '' === $goal_name ) {
            $goal_name = 'Goal #' . $goal_id;
        }

        $tracking_mode = isset( $row['tracking_mode'] ) && is_string( $row['tracking_mode'] ) ? (string) $row['tracking_mode'] : 'todoist';
        $label_name = isset( $row['label_name'] ) && is_string( $row['label_name'] ) ? (string) $row['label_name'] : '';
        $plant_name = isset( $row['plant_name'] ) && is_string( $row['plant_name'] ) ? trim( (string) $row['plant_name'] ) : '';
        $target = isset( $row['target'] ) ? (int) $row['target'] : 0;
        $unit = isset( $row['period_unit'] ) ? (string) $row['period_unit'] : 'week';
        $span = isset( $row['period_span'] ) ? (int) $row['period_span'] : 1;
        if ( $span < 1 ) {
            $span = 1;
        }
        $mode = isset( $row['period_mode'] ) ? (string) $row['period_mode'] : 'calendar';
        $goal_type = isset( $row['goal_type'] ) && is_string( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive';
        $threshold = isset( $row['threshold'] ) && is_numeric( $row['threshold'] ) ? (int) $row['threshold'] : null;
        $is_negative = TCT_Utils::is_negative_goal_type( $goal_type );

        $vitality_data = array(
            'vitality' => 100,
            'target' => $target,
            'achieved' => 0,
            'time_remaining_seconds' => 0,
            'time_remaining_label' => '',
        );

        if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) {
            $goal_row_for_vitality = array(
                'id' => $goal_id,
                'points_per_completion' => isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0,
                'target' => $target,
                'period_unit' => $unit,
                'period_span' => $span,
                'goal_type' => $goal_type,
                'threshold' => $threshold,
                'importance' => isset( $row['importance'] ) ? (int) $row['importance'] : 3,
                'effort' => isset( $row['effort'] ) ? (int) $row['effort'] : 3,
                'created_at' => isset( $row['created_at'] ) ? (string) $row['created_at'] : '',
                'updated_at' => isset( $row['updated_at'] ) ? (string) $row['updated_at'] : '',
                'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0,
                'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '',
            );
            try {
                $maybe_vitality = TCT_Vitality::compute_for_goal( $user_id, $goal_row_for_vitality, $now_tz );
                if ( is_array( $maybe_vitality ) ) {
                    $vitality_data = array_merge( $vitality_data, $maybe_vitality );
                }
            } catch ( Exception $e ) {
            }
        }

        $vitality = isset( $vitality_data['vitality'] ) ? (int) $vitality_data['vitality'] : 100;
        if ( $vitality < 0 ) {
            $vitality = 0;
        } elseif ( $vitality > 100 ) {
            $vitality = 100;
        }

        $achieved = isset( $vitality_data['achieved'] ) ? (int) $vitality_data['achieved'] : 0;
        $time_remaining_seconds = isset( $vitality_data['time_remaining_seconds'] ) ? (int) $vitality_data['time_remaining_seconds'] : 0;

        $status = array(
            'key' => 'on-track',
            'label' => 'On track',
            'rank' => 1,
            'expected' => 0,
            'need' => 0,
        );
        $days_left = 1;

        if ( ! $is_negative && $target > 0 ) {
            $bounds = null;
            if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'current_loop_bounds' ) ) ) {
                $bounds = TCT_Interval::current_loop_bounds( $now_tz, $unit, $span );
            }
            if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) ) {
                $today_tz = $now_tz->setTime( 0, 0, 0 );
                $start_day_ts = (int) $bounds['start']->setTime( 0, 0, 0 )->getTimestamp();
                $end_day_ts = (int) $bounds['end']->setTime( 0, 0, 0 )->getTimestamp();
                $today_ts = (int) $today_tz->getTimestamp();
                $day_seconds = 86400;
                $span_s = $end_day_ts - $start_day_ts;
                $total_days = (int) floor( (float) $span_s / (float) $day_seconds );
                if ( $total_days < 1 ) {
                    $total_days = 1;
                }

                $day_index = (int) floor( (float) ( $today_ts - $start_day_ts ) / (float) $day_seconds ) + 1;
                if ( $day_index < 1 ) {
                    $day_index = 1;
                }
                if ( $day_index > $total_days ) {
                    $day_index = $total_days;
                }

                $days_left = $total_days - $day_index + 1;
                if ( $days_left < 1 ) {
                    $days_left = 1;
                }

                $loop_start_ts = (int) $bounds['start']->getTimestamp();
                $loop_end_ts = (int) $bounds['end']->getTimestamp();
                $now_ts_int = (int) $now_tz->getTimestamp();
                $total_seconds = $loop_end_ts - $loop_start_ts;
                if ( $total_seconds < 1 ) {
                    $total_seconds = 1;
                }

                $seconds_left = $loop_end_ts - $now_ts_int;
                if ( $seconds_left < 0 ) {
                    $seconds_left = 0;
                }

                $effective_target = $target;
                $created_at_raw = isset( $row['created_at'] ) ? trim( (string) $row['created_at'] ) : '';
                $updated_at_raw = isset( $row['updated_at'] ) ? trim( (string) $row['updated_at'] ) : '';
                $created_at_ts = 0;
                $updated_at_ts = 0;

                if ( '' !== $created_at_raw && '0000-00-00 00:00:00' !== $created_at_raw ) {
                    $tmp = strtotime( $created_at_raw . ' UTC' );
                    if ( false !== $tmp ) {
                        $created_at_ts = (int) $tmp;
                    }
                }

                if ( '' !== $updated_at_raw && '0000-00-00 00:00:00' !== $updated_at_raw ) {
                    $tmp = strtotime( $updated_at_raw . ' UTC' );
                    if ( false !== $tmp ) {
                        $updated_at_ts = (int) $tmp;
                    }
                }

                $prorate_anchor = TCT_Utils::compute_prorate_anchor_ts( $created_at_ts, $updated_at_ts, $loop_start_ts );
                if ( $prorate_anchor > 0 ) {
                    $effective_target = TCT_Utils::compute_prorated_target( $target, $prorate_anchor, $loop_start_ts, $loop_end_ts );
                }

                $status = $this->compute_goal_status_meta( $achieved, $effective_target, $day_index, $total_days, $days_left, $seconds_left, $total_seconds, $unit, $span );
                if ( $time_remaining_seconds <= 0 ) {
                    $time_remaining_seconds = $seconds_left;
                }
            }
        }

        $last_completed_raw = '';
        if ( is_string( $completions_table ) && '' !== $completions_table ) {
            $last_completed_raw = (string) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT MAX(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d",
                    $user_id,
                    $goal_id
                )
            );
        }

        $last_completed_text = 'never';
        $last_completed_ts = 0;
        if ( '' !== $last_completed_raw && '0000-00-00 00:00:00' !== $last_completed_raw ) {
            $tmp_ts = strtotime( $last_completed_raw . ' UTC' );
            if ( false !== $tmp_ts ) {
                $last_completed_ts = (int) $tmp_ts;
                $now_ts_int = (int) $now_tz->getTimestamp();
                $diff_s = $now_ts_int - $last_completed_ts;
                if ( $diff_s >= 0 && $diff_s < 60 ) {
                    $last_completed_text = 'just now';
                } else {
                    $last_completed_text = $this->tct_abbrev_time_ago( $last_completed_ts, $now_ts_int );
                    if ( 'just now' !== $last_completed_text && '--' !== $last_completed_text ) {
                        $last_completed_text .= ' ago';
                    }
                }
            }
        }

        $ppc = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0;

        $availability_tile_ctx = $this->get_goal_availability_tile_context( $row, $now_tz );
        $availability_enabled_tile = ! empty( $availability_tile_ctx['enabled'] );
        $availability_is_paused_tile = $availability_enabled_tile && ! empty( $availability_tile_ctx['is_paused'] );
        $availability_is_active_tile = $availability_enabled_tile && ! $availability_is_paused_tile;

        $intervals = $this->intervals_from_goal_row( $row );
        $payload_intervals = $intervals;
        if ( TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) {
            $payload_intervals = array();
        }

        $payload = array(
            'goal_id' => $goal_id,
            'tracking_mode' => $tracking_mode,
            'label_name' => $label_name,
            'goal_name' => $goal_name,
            'aliases' => $this->aliases_from_goal_row( $row ),
            'link_url' => $this->link_url_from_goal_row( $row ),
            'goal_notes' => $this->goal_notes_from_goal_row( $row ),
            'due_schedule_json' => isset( $row['due_schedule_json'] ) && is_string( $row['due_schedule_json'] ) ? (string) $row['due_schedule_json'] : '',
            'availability_cycle_json' => isset( $row['availability_cycle_json'] ) && is_string( $row['availability_cycle_json'] ) ? (string) $row['availability_cycle_json'] : '',
            'interval_anchor_json' => isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ? (string) $row['interval_anchor_json'] : '',
            'composite_config_json' => isset( $row['composite_config_json'] ) && is_string( $row['composite_config_json'] ) ? (string) $row['composite_config_json'] : '',
            'availability_enabled' => $availability_enabled_tile ? 1 : 0,
            'availability_phase' => isset( $availability_tile_ctx['phase'] ) ? (string) $availability_tile_ctx['phase'] : '',
            'availability_is_paused' => $availability_is_paused_tile ? 1 : 0,
            'availability_is_active' => $availability_is_active_tile ? 1 : 0,
            'availability_state_label' => isset( $availability_tile_ctx['state_label'] ) ? (string) $availability_tile_ctx['state_label'] : '',
            'availability_state_meta' => isset( $availability_tile_ctx['state_meta'] ) ? (string) $availability_tile_ctx['state_meta'] : '',
            'plant_name' => $plant_name,
            'goal_type' => $goal_type,
            'threshold' => $threshold,
            'timer_duration_seconds' => isset( $row['timer_duration_seconds'] ) ? (int) $row['timer_duration_seconds'] : 0,
            'alarm_sound' => isset( $row['alarm_sound'] ) && is_string( $row['alarm_sound'] ) ? (string) $row['alarm_sound'] : '',
            'alarm_duration' => isset( $row['alarm_duration'] ) ? (int) $row['alarm_duration'] : 0,
            'alarm_vibration' => isset( $row['alarm_vibration'] ) ? (int) $row['alarm_vibration'] : 0,
            'visible_after_time' => isset( $row['visible_after_time'] ) && is_string( $row['visible_after_time'] ) ? (string) $row['visible_after_time'] : '',
            'domain_id' => isset( $row['domain_id'] ) ? (int) $row['domain_id'] : 0,
            'role_id' => isset( $row['role_id'] ) ? (int) $row['role_id'] : 0,
            'points_per_completion' => $ppc,
            'importance' => isset( $row['importance'] ) ? (int) $row['importance'] : 0,
            'effort' => isset( $row['effort'] ) ? (int) $row['effort'] : 0,
            'points_enabled_at' => isset( $row['points_enabled_at'] ) ? (string) $row['points_enabled_at'] : '',
            'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0,
            'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '',
            'wake_time_enabled' => isset( $row['wake_time_enabled'] ) ? (int) $row['wake_time_enabled'] : 0,
            'wake_time_target' => isset( $row['wake_time_target'] ) && is_string( $row['wake_time_target'] ) ? (string) $row['wake_time_target'] : '',
            'bed_time_enabled' => isset( $row['bed_time_enabled'] ) ? (int) $row['bed_time_enabled'] : 0,
            'bed_time_target' => isset( $row['bed_time_target'] ) && is_string( $row['bed_time_target'] ) ? (string) $row['bed_time_target'] : '',
            'fail_button_enabled' => isset( $row['fail_button_enabled'] ) ? (int) $row['fail_button_enabled'] : 0,
            'is_favorite' => isset( $row['is_favorite'] ) ? (int) $row['is_favorite'] : 0,
            'allowed_fails_target' => isset( $row['allowed_fails_target'] ) ? (int) $row['allowed_fails_target'] : 0,
            'allowed_fails_unit' => isset( $row['allowed_fails_unit'] ) ? (string) $row['allowed_fails_unit'] : 'week',
            'allowed_fails_span' => isset( $row['allowed_fails_span'] ) ? (int) $row['allowed_fails_span'] : 1,
            'intervals' => $payload_intervals,
        );

        $payload_json = wp_json_encode( $payload );

        $tile_goal = array(
            'goal_id' => $goal_id,
            'goal_type' => $goal_type,
            'threshold' => $threshold,
            'timer_duration_seconds' => isset( $row['timer_duration_seconds'] ) ? (int) $row['timer_duration_seconds'] : 0,
            'alarm_sound' => isset( $row['alarm_sound'] ) && is_string( $row['alarm_sound'] ) ? (string) $row['alarm_sound'] : '',
            'alarm_duration' => isset( $row['alarm_duration'] ) ? (int) $row['alarm_duration'] : 0,
            'alarm_vibration' => isset( $row['alarm_vibration'] ) ? (int) $row['alarm_vibration'] : 0,
            'visible_after_time' => isset( $row['visible_after_time'] ) && is_string( $row['visible_after_time'] ) ? (string) $row['visible_after_time'] : '',
            'sleep_tracking_enabled' => isset( $row['sleep_tracking_enabled'] ) ? (int) $row['sleep_tracking_enabled'] : 0,
            'sleep_rollover_time' => isset( $row['sleep_rollover_time'] ) && is_string( $row['sleep_rollover_time'] ) ? (string) $row['sleep_rollover_time'] : '',
            'wake_time_enabled' => isset( $row['wake_time_enabled'] ) ? (int) $row['wake_time_enabled'] : 0,
            'wake_time_target' => isset( $row['wake_time_target'] ) && is_string( $row['wake_time_target'] ) ? (string) $row['wake_time_target'] : '',
            'bed_time_enabled' => isset( $row['bed_time_enabled'] ) ? (int) $row['bed_time_enabled'] : 0,
            'bed_time_target' => isset( $row['bed_time_target'] ) && is_string( $row['bed_time_target'] ) ? (string) $row['bed_time_target'] : '',
            'fail_button_enabled' => isset( $row['fail_button_enabled'] ) ? (int) $row['fail_button_enabled'] : 0,
            'is_favorite' => isset( $row['is_favorite'] ) ? (int) $row['is_favorite'] : 0,
            'tracking_mode' => $tracking_mode,
            'label_name' => $label_name,
            'goal_name' => $goal_name,
            'aliases' => $this->aliases_from_goal_row( $row ),
            'link_url' => $this->link_url_from_goal_row( $row ),
            'goal_notes' => $this->goal_notes_from_goal_row( $row ),
            'due_schedule_json' => isset( $row['due_schedule_json'] ) && is_string( $row['due_schedule_json'] ) ? (string) $row['due_schedule_json'] : '',
            'availability_cycle_json' => isset( $row['availability_cycle_json'] ) && is_string( $row['availability_cycle_json'] ) ? (string) $row['availability_cycle_json'] : '',
            'interval_anchor_json' => isset( $row['interval_anchor_json'] ) && is_string( $row['interval_anchor_json'] ) ? (string) $row['interval_anchor_json'] : '',
            'composite_config_json' => isset( $row['composite_config_json'] ) && is_string( $row['composite_config_json'] ) ? (string) $row['composite_config_json'] : '',
            'availability_enabled' => $availability_enabled_tile ? 1 : 0,
            'availability_phase' => isset( $availability_tile_ctx['phase'] ) ? (string) $availability_tile_ctx['phase'] : '',
            'availability_is_paused' => $availability_is_paused_tile ? 1 : 0,
            'availability_is_active' => $availability_is_active_tile ? 1 : 0,
            'availability_state_label' => isset( $availability_tile_ctx['state_label'] ) ? (string) $availability_tile_ctx['state_label'] : '',
            'availability_state_meta' => isset( $availability_tile_ctx['state_meta'] ) ? (string) $availability_tile_ctx['state_meta'] : '',
            'plant_name' => $plant_name,
            'domain_id' => isset( $row['domain_id'] ) ? (int) $row['domain_id'] : 0,
            'role_id' => isset( $row['role_id'] ) ? (int) $row['role_id'] : 0,
            'edit_payload_json' => (string) $payload_json,
            'points_per_completion' => $ppc,
            'target' => $target,
            'unit' => $unit,
            'period_span' => (int) $span,
            'mode' => $mode,
            'goal_count' => $achieved,
            'status_key' => isset( $status['key'] ) ? (string) $status['key'] : 'on-track',
            'status_label' => isset( $status['label'] ) ? (string) $status['label'] : 'On track',
            'status_rank' => isset( $status['rank'] ) ? (int) $status['rank'] : 1,
            'expected_by_now' => isset( $status['expected'] ) ? (int) $status['expected'] : 0,
            'need' => isset( $status['need'] ) ? (int) $status['need'] : 0,
            'days_left' => (int) $days_left,
            'vitality_data' => $vitality_data,
            'last_completed_text' => (string) $last_completed_text,
            'last_completed_ts' => (int) $last_completed_ts,
            'allowed_fails_target' => isset( $row['allowed_fails_target'] ) ? (int) $row['allowed_fails_target'] : 0,
            'allowed_fails_unit' => isset( $row['allowed_fails_unit'] ) ? (string) $row['allowed_fails_unit'] : 'week',
            'allowed_fails_span' => isset( $row['allowed_fails_span'] ) ? (int) $row['allowed_fails_span'] : 1,
        );

        if ( $is_negative ) {
            $tile_goal['status_key'] = 'on-track';
            $tile_goal['status_label'] = 'On track';
            $tile_goal['status_rank'] = 1;
            $tile_goal['expected_by_now'] = 0;
            $tile_goal['need'] = 0;
        } elseif ( $availability_is_paused_tile ) {
            $tile_goal['status_rank'] = -1;
        }

        $surface_name = is_string( $surface ) ? trim( $surface ) : '';
        if ( '' !== $surface_name ) {
            $tile_goal['__tct_surface'] = $surface_name;
        }

        if ( '' !== $surface_name && 0 === strpos( $surface_name, 'mobile' ) && $availability_is_paused_tile ) {
            return array();
        }

        if ( is_array( $extra_tile_goal ) && ! empty( $extra_tile_goal ) ) {
            foreach ( $extra_tile_goal as $extra_key => $extra_value ) {
                if ( ! is_string( $extra_key ) || '' === $extra_key ) {
                    continue;
                }
                $tile_goal[ $extra_key ] = $extra_value;
            }
        }

        if ( $this->tct_composite_goals_enabled() && $this->tct_goal_is_composite_parent( $row, $user_id ) ) {
            $parent_preview = $this->tct_composite_dashboard_parent_preview_data( $row, $user_id );
            $parent_child_count = isset( $parent_preview['child_count'] ) ? (int) $parent_preview['child_count'] : 0;
            $parent_complete_count = isset( $parent_preview['complete_count'] ) ? (int) $parent_preview['complete_count'] : 0;
            $parent_all_children_complete = ( $parent_child_count > 0 && ! empty( $parent_preview['all_children_complete'] ) );
            if ( $parent_all_children_complete ) {
                $parent_last_completed_text = isset( $parent_preview['latest_child_completed_text'] ) ? trim( (string) $parent_preview['latest_child_completed_text'] ) : '';
                if ( '' === $parent_last_completed_text || '--' === $parent_last_completed_text || 'never' === strtolower( $parent_last_completed_text ) ) {
                    $parent_last_completed_text = 'just now';
                }

                $tile_goal['goal_count'] = $parent_complete_count;
                $tile_goal['target'] = $parent_child_count;
                $tile_goal['status_key'] = 'completed';
                $tile_goal['status_label'] = 'Completed';
                $tile_goal['status_rank'] = 0;
                $tile_goal['expected_by_now'] = $parent_child_count;
                $tile_goal['need'] = 0;
                $tile_goal['last_completed_ts'] = isset( $parent_preview['latest_child_completed_ts'] ) ? (int) $parent_preview['latest_child_completed_ts'] : 0;
                $tile_goal['last_completed_text'] = $parent_last_completed_text;

                if ( ! isset( $tile_goal['vitality_data'] ) || ! is_array( $tile_goal['vitality_data'] ) ) {
                    $tile_goal['vitality_data'] = array();
                }
                $tile_goal['vitality_data']['target'] = $parent_child_count;
                $tile_goal['vitality_data']['achieved'] = $parent_complete_count;
                $tile_goal['vitality_data']['vitality'] = 100;
                $vitality = 100;
            }
        }

        $ignore_visible_after_time = ! empty( $tile_goal['__tct_ignore_visible_after_time'] );
        $vat_val = isset( $row['visible_after_time'] ) && is_string( $row['visible_after_time'] ) ? trim( $row['visible_after_time'] ) : '';
        $now_hhmm_mobile = $now_tz->format( 'H:i' );
        if ( ! $ignore_visible_after_time && '' !== $vat_val && 'day' === $unit && 1 === (int) $span && $now_hhmm_mobile < $vat_val ) {
            return array();
        }

        $status_key = isset( $tile_goal['status_key'] ) ? (string) $tile_goal['status_key'] : 'on-track';

        return array(
            'goal_id' => $goal_id,
            'goal_name' => $goal_name,
            'goal_name_lower' => function_exists( 'mb_strtolower' ) ? mb_strtolower( $goal_name, 'UTF-8' ) : strtolower( $goal_name ),
            'status_key' => $status_key,
            'vitality' => $vitality,
            'time_remaining_seconds' => (int) $time_remaining_seconds,
            'is_paused' => $availability_is_paused_tile ? 1 : 0,
            'tile_goal' => $tile_goal,
        );
    } private function tct_render_mobile_result_html( $tile_goal, $connected, $vitality = null, $goal_name = '' ) {
        if ( ! is_array( $tile_goal ) ) {
            return '';
        }

        $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0;
        $goal_name = is_string( $goal_name ) ? trim( $goal_name ) : '';
        if ( '' === $goal_name && isset( $tile_goal['goal_name'] ) ) {
            $goal_name = trim( (string) $tile_goal['goal_name'] );
        }
        if ( '' === $goal_name ) {
            $goal_name = 'Goal #' . $goal_id;
        }

        $status_key = isset( $tile_goal['status_key'] ) && is_string( $tile_goal['status_key'] ) ? (string) $tile_goal['status_key'] : 'on-track';

        if ( ! is_numeric( $vitality ) ) {
            $vitality = 100;
            if ( isset( $tile_goal['vitality_data'] ) && is_array( $tile_goal['vitality_data'] ) && isset( $tile_goal['vitality_data']['vitality'] ) ) {
                $vitality = (int) $tile_goal['vitality_data']['vitality'];
            }
        }

        $vitality = (int) $vitality;
        if ( $vitality < 0 ) {
            $vitality = 0;
        } elseif ( $vitality > 100 ) {
            $vitality = 100;
        }

        $html_out = '';
        $html_out .= '<div' . $this->mobile_result_wrapper_attrs_html( $tile_goal, $status_key ) . '>';
        $html_out .= '<div class="tct-mobile-result-header" data-tct-mobile-toggle role="button" tabindex="0">';
        $html_out .= $this->mobile_vitality_badge_html( $vitality );
        $html_out .= $this->mobile_row_info_html( $tile_goal, $goal_name );
        $html_out .= $this->mobile_row_complete_button_html( $tile_goal );
        $html_out .= '</div>';
        $html_out .= '<div class="tct-mobile-result-body">';
        $html_out .= $this->render_cockpit_goal_tile( $tile_goal, $connected );
        $html_out .= '</div>';
        $html_out .= '</div>';

        return $html_out;
    } private function mobile_collect_all_tile_goals_for_user( $user_id, $connected ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 ) {
            return array();
        }

        global $wpdb;

        $goals_table = TCT_DB::table_goals();
        $completions_table = TCT_DB::table_completions();
        $tz = TCT_Utils::wp_timezone();
        $now_tz = new DateTimeImmutable( 'now', $tz );

        $aliases_select = $this->tct_goal_aliases_select_sql();
        $link_select = $this->tct_goal_link_url_select_sql();
        $notes_select = $this->tct_goal_notes_select_sql();
        $due_schedule_select = $this->tct_goal_due_schedule_select_sql();
        $availability_cycle_select = $this->tct_goal_availability_cycle_select_sql();
        $interval_anchor_select = $this->tct_goal_interval_anchor_select_sql();
        $composite_config_select = $this->tct_goal_composite_config_select_sql();
        $wake_select = $this->tct_goal_wake_time_select_sql();

        $goal_rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, tracking_mode, label_name, goal_name, {$aliases_select}, {$link_select}, {$notes_select}, {$due_schedule_select}, {$availability_cycle_select}, {$interval_anchor_select}, {$composite_config_select}, plant_name, goal_type, threshold,
                        timer_duration_seconds, alarm_sound, alarm_duration, alarm_vibration, visible_after_time, sleep_tracking_enabled, sleep_rollover_time, {$wake_select}, fail_button_enabled,
                        is_favorite,
                        domain_id, role_id, target, period_unit, period_span, period_mode,
                        intervals_json, points_per_completion, importance, effort, points_enabled_at,
                        created_at, updated_at
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1
                 ORDER BY goal_name ASC",
                $user_id
            ),
            ARRAY_A
        );

        $goal_rows = $this->tct_prepare_goal_rows_for_composite_surface( $goal_rows, 'mobile', $user_id );
        if ( empty( $goal_rows ) ) {
            return array();
        }

        $items = array();
        foreach ( $goal_rows as $row ) {
            $item = $this->tct_build_mobile_goal_item_from_row( $row, $user_id, $now_tz, $completions_table, 'mobile' );
            if ( empty( $item ) ) {
                continue;
            }
            $items[] = $item;
        }

        return $items;
    }     public function handle_mobile_favorites_ajax() {
        TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' );
        $user_id = get_current_user_id();
        if ( $user_id <= 0 ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false;
        $items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected );
        if ( empty( $items ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $fav_items = array();
        foreach ( $items as $it ) {
            $tg = isset( $it['tile_goal'] ) && is_array( $it['tile_goal'] ) ? $it['tile_goal'] : array();
            $is_fav = isset( $tg['is_favorite'] ) ? (int) $tg['is_favorite'] : 0;
            if ( 1 === $is_fav ) {
                $fav_items[] = $it;
            }
        }

        if ( empty( $fav_items ) ) {
            TCT_Utils::send_json_success( array( 'html' => '<div class="tct-mobile-no-results">No favorites yet.</div>' ) );
        }

        $fav_order_map = $this->tct_get_goal_order_map( $user_id, 'favorites' );
        usort( $fav_items, function( $a, $b ) use ( $fav_order_map ) {
            $a_gid = isset( $a['goal_id'] ) ? (int) $a['goal_id'] : 0;
            $b_gid = isset( $b['goal_id'] ) ? (int) $b['goal_id'] : 0;
            $a_pos = isset( $fav_order_map[ $a_gid ] ) ? (int) $fav_order_map[ $a_gid ] : PHP_INT_MAX;
            $b_pos = isset( $fav_order_map[ $b_gid ] ) ? (int) $fav_order_map[ $b_gid ] : PHP_INT_MAX;
            if ( $a_pos !== $b_pos ) {
                return $a_pos <=> $b_pos;
            }

            $a_paused = ! empty( $a['tile_goal']['availability_is_paused'] );
            $b_paused = ! empty( $b['tile_goal']['availability_is_paused'] );
            if ( $a_paused !== $b_paused ) {
                return $a_paused ? 1 : -1;
            }

            $ua = isset( $a['urgency'] ) ? (int) $a['urgency'] : 0;
            $ub = isset( $b['urgency'] ) ? (int) $b['urgency'] : 0;
            if ( $ua === $ub ) {
                $an = isset( $a['goal_name_lower'] ) ? (string) $a['goal_name_lower'] : '';
                $bn = isset( $b['goal_name_lower'] ) ? (string) $b['goal_name_lower'] : '';
                return strcmp( $an, $bn );
            }
            return ( $ub <=> $ua );
        } );

        $html_out = '';
        foreach ( $fav_items as $item ) {
            $goal_id = isset( $item['goal_id'] ) ? (int) $item['goal_id'] : 0;
            if ( $goal_id <= 0 ) {
                continue;
            }

            $goal_name = isset( $item['goal_name'] ) ? (string) $item['goal_name'] : '';
            $tile_goal = isset( $item['tile_goal'] ) && is_array( $item['tile_goal'] ) ? $item['tile_goal'] : array();
            $status_key = isset( $tile_goal['status_key'] ) ? (string) $tile_goal['status_key'] : 'on-track';

            $vitality = isset( $item['vitality'] ) ? (int) $item['vitality'] : 100;
            if ( isset( $tile_goal['vitality_data'] ) && is_array( $tile_goal['vitality_data'] ) && isset( $tile_goal['vitality_data']['vitality'] ) ) {
                $vitality = (int) $tile_goal['vitality_data']['vitality'];
            }
            if ( $vitality < 0 ) {
                $vitality = 0;
            } elseif ( $vitality > 100 ) {
                $vitality = 100;
            }

            $html_out .= '<div' . $this->mobile_result_wrapper_attrs_html( $tile_goal, $status_key ) . '>';
            $html_out .= '<div class="tct-mobile-result-header" data-tct-mobile-toggle role="button" tabindex="0">';
            $html_out .= $this->mobile_vitality_badge_html( $vitality );
            $html_out .= $this->mobile_row_info_html( $tile_goal, $goal_name );
            $html_out .= $this->mobile_row_complete_button_html( $tile_goal );
            $html_out .= '</div>';
            $html_out .= '<div class="tct-mobile-result-body">';
            $html_out .= $this->render_cockpit_goal_tile( $tile_goal, $connected );
            $html_out .= '</div>';
            $html_out .= '</div>';
        }

        TCT_Utils::send_json_success( array( 'html' => (string) $html_out, ) );
    } public function handle_mobile_search_ajax() {
        TCT_Utils::enforce_ajax_nonce( 'tct_mobile_search', 'nonce' );
        $user_id = get_current_user_id();
        if ( $user_id <= 0 ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
        $query = trim( $query );
        if ( '' === $query ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $connected = class_exists( 'TCT_OAuth' ) && is_callable( array( 'TCT_OAuth', 'is_connected' ) ) ? (bool) TCT_OAuth::is_connected( $user_id ) : false;
        $top_level_items = $this->mobile_collect_all_tile_goals_for_user( $user_id, $connected );
        if ( empty( $top_level_items ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $items_by_goal_id = array();
        foreach ( $top_level_items as $item ) {
            if ( ! is_array( $item ) || ! isset( $item['tile_goal'] ) || ! is_array( $item['tile_goal'] ) ) {
                continue;
            }
            $goal_id = isset( $item['tile_goal']['goal_id'] ) ? (int) $item['tile_goal']['goal_id'] : 0;
            if ( $goal_id <= 0 ) {
                continue;
            }
            $items_by_goal_id[ $goal_id ] = $item;
        }

        if ( empty( $items_by_goal_id ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        global $wpdb;
        $goals_table = TCT_DB::table_goals();
        $aliases_select = $this->tct_goal_aliases_select_sql();

        $goal_rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, goal_name, label_name, {$aliases_select}
                 FROM {$goals_table}
                 WHERE user_id = %d AND is_tracked = 1
                 ORDER BY goal_name ASC",
                $user_id
            ),
            ARRAY_A
        );

        if ( empty( $goal_rows ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $query_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $query, 'UTF-8' ) : strtolower( $query );
        $query_words = preg_split( '/\s+/', $query_lower, -1, PREG_SPLIT_NO_EMPTY );

        $child_parent_map = array();
        if ( $this->tct_composite_goals_enabled() && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'get_composite_child_parent_map' ) ) {
            $child_parent_map = TCT_DB::get_composite_child_parent_map( $user_id );
            if ( ! is_array( $child_parent_map ) ) {
                $child_parent_map = array();
            }
        }

        $display_hits = array();
        foreach ( $goal_rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['id'] ) ) {
                continue;
            }

            $goal_id = (int) $row['id'];
            if ( $goal_id <= 0 ) {
                continue;
            }

            $goal_name = isset( $row['goal_name'] ) ? (string) $row['goal_name'] : '';
            if ( '' === $goal_name && isset( $row['label_name'] ) ) {
                $goal_name = (string) $row['label_name'];
            }
            if ( '' === $goal_name ) {
                $goal_name = 'Goal #' . $goal_id;
            }

            $name_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $goal_name, 'UTF-8' ) : strtolower( $goal_name );
            $match_score = $this->mobile_search_score( $name_lower, $query_lower, $query_words );

            $aliases = $this->aliases_from_goal_row( $row );
            if ( ! empty( $aliases ) ) {
                foreach ( $aliases as $alias_name ) {
                    $alias_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $alias_name, 'UTF-8' ) : strtolower( $alias_name );
                    $alias_score = $this->mobile_search_score( $alias_lower, $query_lower, $query_words );
                    if ( $alias_score > $match_score ) {
                        $match_score = $alias_score;
                    }
                }
            }

            if ( $match_score <= 0 ) {
                continue;
            }

            $display_goal_id = $goal_id;
            $matched_child_goal_id = 0;
            if ( isset( $child_parent_map[ $goal_id ] ) ) {
                $candidate_parent_id = (int) $child_parent_map[ $goal_id ];
                if ( $candidate_parent_id > 0 && isset( $items_by_goal_id[ $candidate_parent_id ] ) ) {
                    $display_goal_id = $candidate_parent_id;
                    $matched_child_goal_id = $goal_id;
                }
            }

            if ( ! isset( $items_by_goal_id[ $display_goal_id ] ) ) {
                continue;
            }

            if ( ! isset( $display_hits[ $display_goal_id ] ) ) {
                $display_hits[ $display_goal_id ] = array(
                    'match_score' => 0,
                    'matched_child_goal_ids' => array(),
                    'matched_child_goal_names' => array(),
                    'matched_directly' => false,
                );
            }

            if ( $match_score > (int) $display_hits[ $display_goal_id ]['match_score'] ) {
                $display_hits[ $display_goal_id ]['match_score'] = (int) $match_score;
            }

            if ( $matched_child_goal_id > 0 ) {
                $display_hits[ $display_goal_id ]['matched_child_goal_ids'][ $matched_child_goal_id ] = $matched_child_goal_id;
                $display_hits[ $display_goal_id ]['matched_child_goal_names'][ $matched_child_goal_id ] = $goal_name;
            } else {
                $display_hits[ $display_goal_id ]['matched_directly'] = true;
            }
        }

        if ( empty( $display_hits ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        $scored_goals = array();
        foreach ( $display_hits as $display_goal_id => $hit ) {
            if ( ! isset( $items_by_goal_id[ $display_goal_id ] ) ) {
                continue;
            }

            $item = $items_by_goal_id[ $display_goal_id ];
            if ( ! isset( $item['tile_goal'] ) || ! is_array( $item['tile_goal'] ) ) {
                continue;
            }

            $tile_goal = $item['tile_goal'];
            $tile_goal['__tct_surface'] = 'mobile_search';

            $matched_child_goal_ids = array_values( array_map( 'intval', array_keys( isset( $hit['matched_child_goal_ids'] ) ? $hit['matched_child_goal_ids'] : array() ) ) );
            $matched_child_goal_names = array_values( array_map( 'strval', isset( $hit['matched_child_goal_names'] ) ? $hit['matched_child_goal_names'] : array() ) );

            if ( ! empty( $matched_child_goal_ids ) ) {
                $tile_goal['__tct_composite_matched_child_ids'] = $matched_child_goal_ids;
                $tile_goal['__tct_composite_matched_child_names'] = $matched_child_goal_names;
                $tile_goal['__tct_composite_force_expanded'] = 1;
            }

            $scored_goals[] = array(
                'match_score' => isset( $hit['match_score'] ) ? (int) $hit['match_score'] : 0,
                'status_rank' => isset( $tile_goal['status_rank'] ) ? (int) $tile_goal['status_rank'] : 0,
                'is_paused' => isset( $item['is_paused'] ) ? (int) $item['is_paused'] : 0,
                'vitality' => isset( $item['vitality'] ) ? (int) $item['vitality'] : 100,
                'ppc' => isset( $tile_goal['points_per_completion'] ) ? (int) $tile_goal['points_per_completion'] : 0,
                'last_ts' => isset( $tile_goal['last_completed_ts'] ) ? (int) $tile_goal['last_completed_ts'] : 0,
                'goal_name' => isset( $item['goal_name'] ) ? (string) $item['goal_name'] : '',
                'tile_goal' => $tile_goal,
            );
        }

        if ( empty( $scored_goals ) ) {
            TCT_Utils::send_json_success( array( 'html' => '' ) );
        }

        usort(
            $scored_goals,
            function ( $a, $b ) {
                $a_match = isset( $a['match_score'] ) ? (int) $a['match_score'] : 0;
                $b_match = isset( $b['match_score'] ) ? (int) $b['match_score'] : 0;
                if ( $a_match !== $b_match ) {
                    return $b_match <=> $a_match;
                }

                $a_paused = isset( $a['is_paused'] ) ? (int) $a['is_paused'] : 0;
                $b_paused = isset( $b['is_paused'] ) ? (int) $b['is_paused'] : 0;
                if ( $a_paused !== $b_paused ) {
                    return $a_paused ? 1 : -1;
                }

                $a_rank = isset( $a['status_rank'] ) ? (int) $a['status_rank'] : 0;
                $b_rank = isset( $b['status_rank'] ) ? (int) $b['status_rank'] : 0;
                if ( $a_rank !== $b_rank ) {
                    return $b_rank <=> $a_rank;
                }

                $a_gt = ( isset( $a['tile_goal'] ) && isset( $a['tile_goal']['goal_type'] ) ) ? strtolower( trim( (string) $a['tile_goal']['goal_type'] ) ) : 'positive';
                $b_gt = ( isset( $b['tile_goal'] ) && isset( $b['tile_goal']['goal_type'] ) ) ? strtolower( trim( (string) $b['tile_goal']['goal_type'] ) ) : 'positive';
                $a_neg = ( 'never' === $a_gt || 'harm_reduction' === $a_gt ) ? 1 : 0;
                $b_neg = ( 'never' === $b_gt || 'harm_reduction' === $b_gt ) ? 1 : 0;
                if ( $a_neg !== $b_neg ) {
                    return $a_neg <=> $b_neg;
                }

                $a_vit = isset( $a['vitality'] ) ? (int) $a['vitality'] : 100;
                $b_vit = isset( $b['vitality'] ) ? (int) $b['vitality'] : 100;
                if ( $a_vit !== $b_vit ) {
                    return $a_vit <=> $b_vit;
                }

                $a_ppc = isset( $a['ppc'] ) ? (int) $a['ppc'] : 0;
                $b_ppc = isset( $b['ppc'] ) ? (int) $b['ppc'] : 0;
                if ( $a_ppc !== $b_ppc ) {
                    return $b_ppc <=> $a_ppc;
                }

                $a_last = isset( $a['last_ts'] ) ? (int) $a['last_ts'] : 0;
                $b_last = isset( $b['last_ts'] ) ? (int) $b['last_ts'] : 0;
                if ( $a_last !== $b_last ) {
                    return $a_last <=> $b_last;
                }

                $a_name = isset( $a['goal_name'] ) ? (string) $a['goal_name'] : '';
                $b_name = isset( $b['goal_name'] ) ? (string) $b['goal_name'] : '';
                return strcasecmp( $a_name, $b_name );
            }
        );

        $scored_goals = array_slice( $scored_goals, 0, 10 );
        $html_out = '';
        foreach ( $scored_goals as $item ) {
            if ( ! isset( $item['tile_goal'] ) || ! is_array( $item['tile_goal'] ) ) {
                continue;
            }

            $tile_goal = $item['tile_goal'];
            $html_out .= $this->tct_render_mobile_result_html(
                $tile_goal,
                $connected,
                isset( $item['vitality'] ) ? (int) $item['vitality'] : null,
                isset( $item['goal_name'] ) ? (string) $item['goal_name'] : ''
            );
        }

        TCT_Utils::send_json_success( array( 'html' => (string) $html_out ) );
    } private function mobile_search_score( $name_lower, $query_lower, $query_words ) { if ( false !== strpos( $name_lower, $query_lower ) ) { if ( 0 === strpos( $name_lower, $query_lower ) || false !== strpos( $name_lower, ' ' . $query_lower ) ) { return 1000; } return 900; } $all_words_match = true; foreach ( $query_words as $word ) { if ( false === strpos( $name_lower, $word ) ) { $all_words_match = false; break; } } if ( $all_words_match && count( $query_words ) > 0 ) { $word_start_count = 0; foreach ( $query_words as $word ) { if ( 0 === strpos( $name_lower, $word ) || false !== strpos( $name_lower, ' ' . $word ) ) { $word_start_count++; } } return 500 + ( $word_start_count * 50 ); } $any_word_match = false; $match_count = 0; foreach ( $query_words as $word ) { if ( false !== strpos( $name_lower, $word ) ) { $any_word_match = true; $match_count++; } } if ( $any_word_match ) { return 100 + ( $match_count * 20 ); } return 0; } private function mobile_vitality_badge_html( $vitality ) { $color = $this->mobile_vitality_ring_color_hex( $vitality ); return '<span class="tct-mobile-vitality-badge" style="color:' . esc_attr( $color ) . ';">' . esc_html( (string) $vitality ) . '</span>'; }

    private function mobile_result_wrapper_attrs_html( $tile_goal, $status_key = '' ) {
        $composite_attrs = $this->tct_mobile_result_wrapper_attrs_composite_scaffold( $tile_goal, $status_key );
        if ( '' !== $composite_attrs ) {
            return $composite_attrs;
        }

        $tile_goal = is_array( $tile_goal ) ? $tile_goal : array();
        $classes = array( 'tct-mobile-result' );
        $attrs = '';

        $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0;
        if ( $goal_id > 0 ) {
            $attrs .= ' data-goal-id="' . esc_attr( (string) $goal_id ) . '"';
        }

        if ( '' === $status_key && isset( $tile_goal['status_key'] ) && is_string( $tile_goal['status_key'] ) ) {
            $status_key = trim( (string) $tile_goal['status_key'] );
        }
        if ( '' === $status_key ) {
            $status_key = 'on-track';
        }
        $attrs .= ' data-status-key="' . esc_attr( $status_key ) . '"';

        $availability_enabled = ! empty( $tile_goal['availability_enabled'] );
        $availability_is_paused = $availability_enabled && ! empty( $tile_goal['availability_is_paused'] );
        $availability_is_active = $availability_enabled && ! $availability_is_paused;

        if ( $availability_enabled ) {
            $classes[] = 'tct-mobile-result-availability-enabled';
            $attrs .= ' data-tct-availability-enabled="1"';
        }

        if ( $availability_is_paused ) {
            $classes[] = 'tct-mobile-result-paused';
            $attrs .= ' data-tct-availability-paused="1"';
            $attrs .= ' data-tct-availability-phase="pause"';
        } elseif ( $availability_is_active ) {
            $classes[] = 'tct-mobile-result-active-cycle';
            $attrs .= ' data-tct-availability-phase="active"';
        }

        $availability_state_label = isset( $tile_goal['availability_state_label'] ) && is_string( $tile_goal['availability_state_label'] ) ? trim( (string) $tile_goal['availability_state_label'] ) : '';
        if ( '' !== $availability_state_label ) {
            $attrs .= ' data-tct-availability-state-label="' . esc_attr( $availability_state_label ) . '"';
        }

        $class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
        return ' class="' . esc_attr( trim( $class_attr ) ) . '"' . $attrs;
    }     private function mobile_row_info_html( $tile_goal, $goal_name ) {
        $goal_name = is_string( $goal_name ) ? $goal_name : '';

        $html = '<div class="tct-mobile-result-info">';
        $html .= '<div class="tct-mobile-result-name">' . esc_html( $goal_name ) . '</div>';

        $meta_parts = array();

        $availability_enabled = ! empty( $tile_goal['availability_enabled'] );
        $availability_is_paused = $availability_enabled && ! empty( $tile_goal['availability_is_paused'] );

        $allowed_fails_meta = '';
        if ( isset( $tile_goal['allowed_fails_target'] ) && (int) $tile_goal['allowed_fails_target'] > 0 ) {
            $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
            if ( $user_id > 0 ) {
                try {
                    $tz_local = class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'wp_timezone' ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
                    $now_tz = new DateTimeImmutable( 'now', $tz_local );
                    $af_line = $this->get_allowed_fails_pace_line( $user_id, $tile_goal, $now_tz );
                    if ( $af_line ) {
                        $af_line = preg_replace( '/^Allowed\s+fails:\s*/i', 'Fails: ', (string) $af_line );
                        $af_line = preg_replace( '/\s+used\s*$/i', '', (string) $af_line );
                        $af_line = trim( (string) $af_line );
                        if ( '' !== $af_line ) {
                            $allowed_fails_meta = '<span class="tct-mobile-result-allowed-fails tct-muted">' . esc_html( $af_line ) . '</span>';
                        }
                    }
                } catch ( Exception $e ) {
                    $allowed_fails_meta = '';
                }
            }
        }

        $due_enabled = false;
        $due_today = true;
        $next_short = '';
        if ( is_array( $tile_goal ) ) {
            $tracking_mode_for_due = isset( $tile_goal['tracking_mode'] ) ? strtolower( trim( (string) $tile_goal['tracking_mode'] ) ) : '';
            if ( '' === $tracking_mode_for_due ) {
                $tracking_mode_for_due = 'todoist';
            }

            if ( 'manual' === $tracking_mode_for_due ) {
                $due_schedule_json_raw = isset( $tile_goal['due_schedule_json'] ) ? trim( (string) $tile_goal['due_schedule_json'] ) : '';
                if ( '' !== $due_schedule_json_raw && class_exists( 'TCT_Interval' ) && method_exists( 'TCT_Interval', 'normalize_due_schedule_config' ) ) {
                    $cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_json_raw );
                    if ( is_array( $cfg ) && ! empty( $cfg['enabled'] ) && ! empty( $cfg['type'] ) && ! empty( $cfg['start_date'] ) ) {
                        $due_enabled = true;
                        $tz = TCT_Utils::wp_timezone();
                        $today_local_ymd = ( new DateTimeImmutable( 'now', $tz ) )->format( 'Y-m-d' );

                        if ( method_exists( 'TCT_Interval', 'due_schedule_is_due_on_local_date' ) ) {
                            $due_today = (bool) TCT_Interval::due_schedule_is_due_on_local_date( $cfg, $today_local_ymd, $tz );
                        }

                        if ( ! $due_today && method_exists( 'TCT_Interval', 'due_schedule_next_due_local_date' ) ) {
                            $next_due = TCT_Interval::due_schedule_next_due_local_date( $cfg, $today_local_ymd, $tz );
                            if ( $next_due instanceof DateTimeImmutable ) {
                                $next_short = $next_due->format( 'D' );
                            }
                        }
                    }
                }
            }
        }

        if ( $due_enabled && ! $due_today && '' !== $next_short ) {
            $meta_parts[] = '<span class="tct-mobile-result-next-due">Next due: ' . esc_html( $next_short ) . '</span>';
        }

        if ( '' !== $allowed_fails_meta ) {
            $meta_parts[] = $allowed_fails_meta;
        }

        $time_label = '';
        if ( isset( $tile_goal['vitality_data'] ) && is_array( $tile_goal['vitality_data'] ) ) {
            $vd = $tile_goal['vitality_data'];
            if ( isset( $vd['time_remaining_label'] ) && '' !== (string) $vd['time_remaining_label'] ) {
                $time_label = (string) $vd['time_remaining_label'];
            }
        }

        if ( '' !== $time_label && ! $availability_is_paused ) {
            $parts = explode( ' ', $time_label );
            $time_label = strtoupper( $parts[0] );
            if ( isset( $tile_goal['sleep_tracking_enabled'] ) && 1 === (int) $tile_goal['sleep_tracking_enabled'] ) {
                if ( preg_match( '/^(\d+)D$/', $time_label, $match_nights ) ) {
                    $time_label = $match_nights[1] . 'N';
                }
            }
            $meta_parts[] = '<span class="tct-mobile-result-countdown">' . esc_html( $time_label ) . '</span>';
        }

        $last_text = isset( $tile_goal['last_completed_text'] ) ? (string) $tile_goal['last_completed_text'] : '';
        if ( '' !== $last_text && '--' !== $last_text ) {
            if ( 'never' === $last_text ) {
                $meta_parts[] = '<span class="tct-mobile-result-last-done tct-mobile-result-last-never">Never completed</span>';
            } elseif ( 'just now' === $last_text ) {
                $meta_parts[] = '<span class="tct-mobile-result-last-done tct-mobile-result-last-recent">Completed just now</span>';
            } else {
                $meta_parts[] = '<span class="tct-mobile-result-last-done">Completed ' . esc_html( $last_text ) . '</span>';
            }
        }

        if ( ! empty( $meta_parts ) ) {
            $html .= '<div class="tct-mobile-result-meta">';
            $html .= implode( '<span class="tct-mobile-result-meta-sep">&middot;</span>', $meta_parts );
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }     
    private function mobile_row_complete_button_html( $tile_goal ) {
        if ( ! is_array( $tile_goal ) ) {
            return '';
        }

        $goal_type = isset( $tile_goal['goal_type'] ) && is_string( $tile_goal['goal_type'] ) ? (string) $tile_goal['goal_type'] : '';
        if ( $this->tct_composite_goals_enabled() && $this->tct_composite_goal_type_requested( $goal_type ) ) {
            $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0;
            if ( $goal_id <= 0 ) {
                return '';
            }

            $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
            $parent_totals = $this->tct_parent_complete_preview_totals( $user_id, $tile_goal );
            $child_count = isset( $parent_totals['child_count'] ) ? (int) $parent_totals['child_count'] : 0;
            $completable_child_count = isset( $parent_totals['completable_child_count'] ) ? max( 0, (int) $parent_totals['completable_child_count'] ) : 0;
            $parent_total_points = isset( $parent_totals['total_points'] ) ? max( 0, (int) $parent_totals['total_points'] ) : 0;
            $disabled_attr = ( $child_count > 0 ) ? '' : ' disabled="disabled" aria-disabled="true"';
            if ( $child_count > 0 ) {
                if ( $completable_child_count > 0 ) {
                    $title = 'Complete all completable child goals for this parent. Total points: +' . $parent_total_points . '.';
                } else {
                    $title = 'This parent has child goals, but none can be completed right now.';
                }
            } else {
                $title = 'This parent has no child goals.';
            }
            $aria_label = ( $child_count > 0 ) ? ( 'Complete all child goals (+' . $parent_total_points . ' points)' ) : 'This parent has no child goals';
            $button_html = '<span class="tct-mobile-row-complete-text">+' . esc_html( (string) $parent_total_points ) . '</span>';

            return '<button type="button" class="tct-mobile-row-complete-btn tct-mobile-row-composite-parent-complete" data-tct-composite-parent-complete="1" data-goal-id="' . esc_attr( (string) $goal_id ) . '" data-child-count="' . esc_attr( (string) $child_count ) . '" data-completable-child-count="' . esc_attr( (string) $completable_child_count ) . '" data-parent-total-points="' . esc_attr( (string) $parent_total_points ) . '" aria-label="' . esc_attr( $aria_label ) . '" title="' . esc_attr( $title ) . '"' . $disabled_attr . '>' . $button_html . '</button>';
        }

        $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0;
        if ( $goal_id <= 0 ) {
            return '';
        }

        if ( ( isset( $tile_goal['sleep_tracking_enabled'] ) && 1 === (int) $tile_goal['sleep_tracking_enabled'] ) || ( isset( $tile_goal['wake_time_enabled'] ) && 1 === (int) $tile_goal['wake_time_enabled'] ) || ( isset( $tile_goal['bed_time_enabled'] ) && 1 === (int) $tile_goal['bed_time_enabled'] ) ) {
            return '';
        }

        $availability_enabled = ! empty( $tile_goal['availability_enabled'] );
        $availability_is_paused = $availability_enabled && ! empty( $tile_goal['availability_is_paused'] );
        $availability_is_active = $availability_enabled && ! $availability_is_paused;
        $availability_state_label = isset( $tile_goal['availability_state_label'] ) && is_string( $tile_goal['availability_state_label'] ) ? trim( (string) $tile_goal['availability_state_label'] ) : '';

        $link_url = isset( $tile_goal['link_url'] ) ? trim( (string) $tile_goal['link_url'] ) : '';
        $link_url_attr = '';
        $link_url_attr_value = $this->goal_link_output_attr_value( $link_url );
        if ( '' !== $link_url_attr_value ) {
            $link_url_attr = ' data-goal-link-url="' . esc_attr( $link_url_attr_value ) . '"';
        }

        $goal_type = isset( $tile_goal['goal_type'] ) && is_string( $tile_goal['goal_type'] ) ? (string) $tile_goal['goal_type'] : 'positive';
        $threshold = isset( $tile_goal['threshold'] ) && is_numeric( $tile_goal['threshold'] ) ? (int) $tile_goal['threshold'] : null;

        $ppc = isset( $tile_goal['points_per_completion'] ) ? (int) $tile_goal['points_per_completion'] : 0;
        if ( $ppc < 0 ) {
            $ppc = 0;
        }

        $current_count = isset( $tile_goal['goal_count'] ) ? (int) $tile_goal['goal_count'] : 0;
        if ( $current_count < 0 ) {
            $current_count = 0;
        }

        $classes = array( 'tct-mobile-row-complete-btn' );
        if ( $availability_enabled ) {
            $classes[] = 'tct-mobile-row-availability-enabled';
        }
        if ( $availability_is_paused ) {
            $classes[] = 'tct-mobile-row-paused';
        } elseif ( $availability_is_active ) {
            $classes[] = 'tct-mobile-row-active-cycle';
        }

        $availability_data_attrs = '';
        if ( $availability_enabled ) {
            $availability_data_attrs .= ' data-tct-availability-enabled="1"';
        }
        if ( $availability_is_paused ) {
            $availability_data_attrs .= ' data-tct-availability-paused="1"';
        }
        if ( '' !== $availability_state_label ) {
            $availability_data_attrs .= ' data-tct-availability-state-label="' . esc_attr( $availability_state_label ) . '"';
        }

        $due_data_attrs = '';
        $tracking_mode_for_due = isset( $tile_goal['tracking_mode'] ) ? strtolower( trim( (string) $tile_goal['tracking_mode'] ) ) : '';
        if ( '' === $tracking_mode_for_due ) {
            $tracking_mode_for_due = 'todoist';
        }

        if ( 'manual' === $tracking_mode_for_due && ! $availability_is_paused ) {
            $due_schedule_json_raw = isset( $tile_goal['due_schedule_json'] ) ? trim( (string) $tile_goal['due_schedule_json'] ) : '';
            if ( '' !== $due_schedule_json_raw && class_exists( 'TCT_Interval' ) && method_exists( 'TCT_Interval', 'normalize_due_schedule_config' ) ) {
                $cfg = TCT_Interval::normalize_due_schedule_config( $due_schedule_json_raw );
                if ( is_array( $cfg ) && ! empty( $cfg['enabled'] ) && ! empty( $cfg['type'] ) && ! empty( $cfg['start_date'] ) ) {
                    $tz = TCT_Utils::wp_timezone();
                    $today_local_ymd = ( new DateTimeImmutable( 'now', $tz ) )->format( 'Y-m-d' );
                    $due_today = true;
                    if ( method_exists( 'TCT_Interval', 'due_schedule_is_due_on_local_date' ) ) {
                        $due_today = (bool) TCT_Interval::due_schedule_is_due_on_local_date( $cfg, $today_local_ymd, $tz );
                    }
                    $due_data_attrs .= ' data-tct-due-enabled="1" data-tct-due-today="' . ( $due_today ? '1' : '0' ) . '"';

                    if ( ! $due_today ) {
                        $classes[] = 'tct-mobile-row-not-due';
                        $next_label = '';
                        $next_weekday = '';
                        if ( method_exists( 'TCT_Interval', 'due_schedule_next_due_local_date' ) ) {
                            $next_due = TCT_Interval::due_schedule_next_due_local_date( $cfg, $today_local_ymd, $tz );
                            if ( $next_due instanceof DateTimeImmutable ) {
                                $next_label = $next_due->format( 'D' );
                                $next_weekday = $next_due->format( 'l' );
                            }
                        }
                        if ( '' !== $next_label ) {
                            $due_data_attrs .= ' data-tct-next-due-label="' . esc_attr( $next_label ) . '"';
                        }
                        if ( '' !== $next_weekday ) {
                            $due_data_attrs .= ' data-tct-next-due-weekday="' . esc_attr( $next_weekday ) . '"';
                        }
                        $due_data_attrs .= ' aria-disabled="true"';
                    }
                }
            }
        }

        $is_negative = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_type' ) ) ? (bool) TCT_Utils::is_negative_goal_type( $goal_type ) : false;
        $is_violation_if_tap = false;
        if ( $is_negative && class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_violation' ) ) ) {
            $is_violation_if_tap = (bool) TCT_Utils::is_negative_goal_violation( $goal_type, $threshold, $current_count );
        }

        $aria_label = 'Complete';
        $inner_html = '<span class="dashicons dashicons-yes" aria-hidden="true"></span>';

        if ( $is_violation_if_tap ) {
            $classes[] = 'tct-mobile-row-complete-btn-penalty';
            $next_count = $current_count + 1;
            if ( $next_count < 1 ) {
                $next_count = 1;
            }

            $goal_type_norm = strtolower( trim( (string) $goal_type ) );
            $is_never = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_never_goal' ) ) ? (bool) TCT_Utils::is_never_goal( $goal_type_norm, $threshold ) : ( 'never' === $goal_type_norm );

            $violation_n = $next_count;
            if ( ! $is_never && 'harm_reduction' === $goal_type_norm ) {
                $th = ( null === $threshold ) ? 0 : (int) $threshold;
                $violation_n = $next_count - $th;
                if ( $violation_n < 1 ) {
                    $violation_n = 1;
                }
            }

            $penalty_points = 0;
            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_violation_penalty' ) ) ) {
                $penalty_points = (int) TCT_Utils::compute_violation_penalty( $ppc, $violation_n );
            }

            $inner_html = '<span class="tct-mobile-row-complete-text">' . esc_html( (string) $penalty_points ) . '</span>';
            $aria_label = 'Complete (' . (string) $penalty_points . ')';
        } else {
            if ( ! $is_negative && $ppc > 0 ) {
                $classes[] = 'tct-mobile-row-complete-btn-ppc';
                $inner_html = '<span class="tct-mobile-row-complete-text">+' . esc_html( (string) $ppc ) . '</span>';
                $aria_label = 'Complete (+' . (string) $ppc . ')';
            }
        }

        $class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

        $fail_data_attrs = '';
        $fail_button_enabled = isset( $tile_goal['fail_button_enabled'] ) ? (int) $tile_goal['fail_button_enabled'] : 0;
        if ( $fail_button_enabled && ! $is_negative && ! $availability_is_paused ) {
            $penalty_for_fail = 0;
            if ( $ppc > 0 && isset( $tile_goal['vitality_data']['target'] ) && (int) $tile_goal['vitality_data']['target'] > 0 ) {
                $fail_target = (int) $tile_goal['vitality_data']['target'];
                if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_penalty_points' ) ) ) {
                    $penalty_for_fail = (int) TCT_Utils::compute_penalty_points( $ppc, $fail_target, 0 );
                }
            }
            $fail_text = 0 !== $penalty_for_fail ? (string) $penalty_for_fail : 'Fail';
            $fail_data_attrs = ' data-tct-fail-enabled="1" data-tct-fail-text="' . esc_attr( $fail_text ) . '"';
        }

        $complete_btn = '<button type="button" class="' . esc_attr( $class_attr ) . '" data-tct-mobile-row-complete data-goal-id="' . esc_attr( (string) $goal_id ) . '"' . $link_url_attr . ' aria-label="' . esc_attr( $aria_label ) . '"' . $fail_data_attrs . $availability_data_attrs . $due_data_attrs . '>' . $inner_html . '</button>';
        return $complete_btn;
    } private function mobile_vitality_ring_color_hex( $vitality ) { $v = (int) $vitality; if ( $v < 0 ) { $v = 0; } elseif ( $v > 100 ) { $v = 100; } $c_green = array( 0, 200, 83 ); $c_yellow = array( 196, 196, 0 ); $c_dead = array( 43, 43, 43 ); $r = 0; $g = 0; $b = 0; if ( $v >= 30 ) { $t = ( $v - 30 ) / 70.0; if ( $t < 0.0 ) { $t = 0.0; } elseif ( $t > 1.0 ) { $t = 1.0; } $t = pow( $t, 1.5 ); $r = (int) round( $c_yellow[0] + ( $c_green[0] - $c_yellow[0] ) * $t ); $g = (int) round( $c_yellow[1] + ( $c_green[1] - $c_yellow[1] ) * $t ); $b = (int) round( $c_yellow[2] + ( $c_green[2] - $c_yellow[2] ) * $t ); } else { $t = $v / 30.0; if ( $t < 0.0 ) { $t = 0.0; } elseif ( $t > 1.0 ) { $t = 1.0; } $t = $t * $t * ( 3.0 - ( 2.0 * $t ) ); $r = (int) round( $c_dead[0] + ( $c_yellow[0] - $c_dead[0] ) * $t ); $g = (int) round( $c_dead[1] + ( $c_yellow[1] - $c_dead[1] ) * $t ); $b = (int) round( $c_dead[2] + ( $c_yellow[2] - $c_dead[2] ) * $t ); } $r = max( 0, min( 255, $r ) ); $g = max( 0, min( 255, $g ) ); $b = max( 0, min( 255, $b ) ); return sprintf( '#%02x%02x%02x', $r, $g, $b ); } private function build_cadence_label( $target, $unit, $span, $is_negative, $goal_type, $threshold ) { $is_no_interval_positive = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_positive_no_interval_goal_type' ) ) ? (bool) TCT_Utils::is_positive_no_interval_goal_type( (string) $goal_type ) : ( 'positive_no_int' === (string) $goal_type ); if ( ! $is_negative && $is_no_interval_positive ) { return 'No interval target'; } $unit_labels = array( 'hour' => array( 'singular' => 'hour', 'plural' => 'hours' ), 'day' => array( 'singular' => 'day', 'plural' => 'days' ), 'week' => array( 'singular' => 'week', 'plural' => 'weeks' ), 'month' => array( 'singular' => 'month', 'plural' => 'months' ), 'quarter' => array( 'singular' => 'quarter', 'plural' => 'quarters' ), 'semiannual'=> array( 'singular' => 'half-year', 'plural' => 'half-years' ), 'year' => array( 'singular' => 'year', 'plural' => 'years' ), ); $display_span = (int) $span; $display_unit = (string) $unit; if ( 'semiannual' === $display_unit ) { $display_unit = 'month'; $display_span = $display_span * 6; } $unit_s = isset( $unit_labels[ $display_unit ] ) ? $unit_labels[ $display_unit ]['singular'] : $display_unit; $unit_p = isset( $unit_labels[ $display_unit ] ) ? $unit_labels[ $display_unit ]['plural'] : $display_unit . 's'; $unit_word = ( 1 === $display_span ) ? $unit_s : $unit_p; if ( $is_negative ) { $is_never = TCT_Utils::is_never_goal( $goal_type, $threshold ); if ( $is_never ) { if ( 1 === $display_span ) { return 'Never (per ' . $unit_word . ')'; } return 'Never (per ' . $display_span . ' ' . $unit_word . ')'; } else { $th_display = ( null !== $threshold && $threshold >= 0 ) ? (int) $threshold : 0; if ( 1 === $display_span ) { return 'Limit ' . $th_display . ' per ' . $unit_word; } return 'Limit ' . $th_display . ' per ' . $display_span . ' ' . $unit_word; } } if ( 1 === $display_span ) { return $target . ' every ' . $unit_word; } return $target . ' every ' . $display_span . ' ' . $unit_word; } 

	public function handle_goal_success_stats_ajax() {
		// Re-use the same nonce as the main Goal History endpoint.
		// This keeps the JS side simple (it already has goalHistoryNonce).
		if ( class_exists( 'TCT_Utils' ) && method_exists( 'TCT_Utils', 'enforce_ajax_nonce' ) ) {
			if ( ! TCT_Utils::enforce_ajax_nonce( 'tct_goal_history', 'nonce' ) ) {
				return;
			}
		} else {
			check_ajax_referer( 'tct_goal_history', 'nonce' );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => 'Not logged in.' ), 401 );
		}

		$user_id = get_current_user_id();
		$goal_id = isset( $_POST['goal_id'] ) ? (int) $_POST['goal_id'] : 0;

		if ( $goal_id <= 0 ) {
			wp_send_json_error( array( 'message' => 'Invalid goal_id.' ), 400 );
		}

		global $wpdb;
		$goals_table = TCT_DB::table_goals();
		$select_due  = $this->tct_goal_due_schedule_column_exists() ? ', due_schedule_json' : '';

		$goal = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, goal_type, target, period_unit, period_span, threshold, created_at{$select_due} FROM {$goals_table} WHERE user_id = %d AND id = %d",
				$user_id,
				$goal_id
			),
			ARRAY_A
		);

		if ( ! is_array( $goal ) ) {
			wp_send_json_error( array( 'message' => 'Goal not found.' ), 404 );
		}

		$stats = $this->compute_goal_success_interval_stats( $user_id, $goal );
		wp_send_json_success( $stats );
	}

	private function compute_goal_success_interval_stats( $user_id, $goal ) {
		$goal_type = isset( $goal['goal_type'] ) ? (string) $goal['goal_type'] : '';

		if ( TCT_Utils::is_positive_no_interval_goal_type( $goal_type ) ) {
			return array( 'available' => false );
		}

		$unit = isset( $goal['period_unit'] ) ? strtolower( (string) $goal['period_unit'] ) : '';
		$span = isset( $goal['period_span'] ) ? (int) $goal['period_span'] : 1;

		if ( $span < 1 ) {
			$span = 1;
		}

		$is_negative = TCT_Utils::is_negative_goal_type( $goal_type );

		$target = isset( $goal['target'] ) ? (int) $goal['target'] : 0;
		if ( ! $is_negative && $target <= 0 ) {
			return array( 'available' => false );
		}

		if ( '' === $unit ) {
			return array( 'available' => false );
		}

		$threshold = isset( $goal['threshold'] ) ? (float) $goal['threshold'] : 0.0;

		$tz     = TCT_Utils::wp_timezone();
		$now_tz = new DateTimeImmutable( 'now', $tz );

		// Establish a start point (goal created_at, falling back to earliest completion).
		$created_at_utc_mysql = isset( $goal['created_at'] ) ? (string) $goal['created_at'] : '';
		$created_dt_utc       = null;

		if ( '' !== $created_at_utc_mysql ) {
			try {
				$created_dt_utc = new DateTimeImmutable( $created_at_utc_mysql, new DateTimeZone( 'UTC' ) );
			} catch ( Exception $e ) {
				$created_dt_utc = null;
			}
		}

		global $wpdb;
		$completions_table = TCT_DB::table_completions();
		$goal_id           = isset( $goal['id'] ) ? (int) $goal['id'] : 0;

		if ( ! $created_dt_utc && $goal_id > 0 ) {
			$earliest = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT MIN(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d",
					(int) $user_id,
					$goal_id
				)
			);

			if ( $earliest ) {
				try {
					$created_dt_utc = new DateTimeImmutable( (string) $earliest, new DateTimeZone( 'UTC' ) );
				} catch ( Exception $e ) {
					$created_dt_utc = null;
				}
			}
		}

		if ( ! $created_dt_utc ) {
			$created_dt_utc = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		}

		$created_dt_tz  = $created_dt_utc->setTimezone( $tz );
		$first_bounds   = TCT_Interval::current_loop_bounds( $created_dt_tz, $unit, $span );
		$first_start_tz = isset( $first_bounds['start'] ) ? $first_bounds['start'] : $now_tz;

		// Pull all completions for this goal since the first interval start.
		$start_utc_mysql = TCT_Utils::dt_to_mysql_utc( $first_start_tz );
		$now_utc_mysql   = TCT_Utils::dt_to_mysql_utc( $now_tz );

		$completion_rows = array();
		if ( $goal_id > 0 ) {
			$completion_rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT completed_at, source, source_ref FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at <= %s ORDER BY completed_at ASC",
					(int) $user_id,
					$goal_id,
					$start_utc_mysql,
					$now_utc_mysql
				),
				ARRAY_A
			);
		}

		// Preload failish ledger event_keys (manual fail / auto miss), so we can detect them without joining.
		$ledger_table = TCT_DB::table_ledger();
		$failish_keys = array();

		if ( $goal_id > 0 ) {
			$failish_keys = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT event_key FROM {$ledger_table} WHERE user_id = %d AND goal_id = %d AND event_type = 'completion' AND (details LIKE '[manual fail]%%' OR details LIKE '[auto miss]%%' OR details LIKE '[auto due miss]%%')",
					(int) $user_id,
					$goal_id
				)
			);
		}

		$failish_key_map = array();
		if ( is_array( $failish_keys ) ) {
			foreach ( $failish_keys as $k ) {
				$failish_key_map[ (string) $k ] = true;
			}
		}

		// Map interval_start_ts => count
		$interval_counts = array();
		if ( is_array( $completion_rows ) ) {
			foreach ( $completion_rows as $row ) {
				if ( ! isset( $row['completed_at'], $row['source'], $row['source_ref'] ) ) {
					continue;
				}

				$dt_utc = null;
				try {
					$dt_utc = new DateTimeImmutable( (string) $row['completed_at'], new DateTimeZone( 'UTC' ) );
				} catch ( Exception $e ) {
					$dt_utc = null;
				}

				if ( ! $dt_utc ) {
					continue;
				}

				$dt_tz  = $dt_utc->setTimezone( $tz );
				$bounds = TCT_Interval::current_loop_bounds( $dt_tz, $unit, $span );

				if ( ! isset( $bounds['start'] ) ) {
					continue;
				}

				$start_ts = (int) $bounds['start']->getTimestamp();
				if ( ! isset( $interval_counts[ $start_ts ] ) ) {
					$interval_counts[ $start_ts ] = array(
						'success'    => 0,
						'violations' => 0,
					);
				}

				if ( $is_negative ) {
					$interval_counts[ $start_ts ]['violations']++;
					continue;
				}

				$source     = (string) $row['source'];
				$source_ref = (string) $row['source_ref'];
				$event_key  = 'c_' . sha1( $source . ':' . $source_ref . ':' . $goal_id );

				$is_failish = ( 'auto_miss' === $source || 'auto_due_miss' === $source || isset( $failish_key_map[ $event_key ] ) );

				if ( ! $is_failish ) {
					$interval_counts[ $start_ts ]['success']++;
				}
			}
		}

		// Optionally filter by due schedule (only meaningful for 1-day intervals).
		$due_cfg = null;
		if ( isset( $goal['due_schedule_json'] ) && is_string( $goal['due_schedule_json'] ) && '' !== $goal['due_schedule_json'] ) {
			$parsed = TCT_Interval::normalize_due_schedule_config( $goal['due_schedule_json'] );
			if ( is_array( $parsed ) && ! empty( $parsed['enabled'] ) ) {
				$due_cfg = $parsed;
			}
		}

		$intervals = array();
		$cursor    = $now_tz;

		while ( true ) {
			$b = TCT_Interval::current_loop_bounds( $cursor, $unit, $span );
			if ( ! isset( $b['start'], $b['end'] ) ) {
				break;
			}

			$start = $b['start'];
			$end   = $b['end'];

			if ( $start < $first_start_tz ) {
				break;
			}

			// Apply due schedule filter when enabled (only for daily single-span goals).
			if ( $due_cfg && 'day' === $unit && 1 === $span ) {
				$ymd = $start->format( 'Y-m-d' );
				if ( ! TCT_Interval::due_schedule_is_due_on_local_date( $due_cfg, $ymd, $tz ) ) {
					$cursor = $start->sub( new DateInterval( 'PT1S' ) );
					continue;
				}
			}

			$intervals[] = array(
				'start' => $start,
				'end'   => $end,
			);

			$cursor = $start->sub( new DateInterval( 'PT1S' ) );
		}

		$intervals = array_reverse( $intervals );

		$total_complete = 0;
		$total_success  = 0;
		$longest_streak = 0;
		$run            = 0;

		$outcomes = array();

		foreach ( $intervals as $iv ) {
			$start_ts = (int) $iv['start']->getTimestamp();

			$success_cnt   = isset( $interval_counts[ $start_ts ] ) ? (int) $interval_counts[ $start_ts ]['success'] : 0;
			$violation_cnt = isset( $interval_counts[ $start_ts ] ) ? (int) $interval_counts[ $start_ts ]['violations'] : 0;

			if ( $is_negative ) {
				$is_success = TCT_Utils::is_negative_goal_met( $goal_type, $threshold, $violation_cnt );
			} else {
				$is_success = ( $success_cnt >= $target );
			}

			$is_complete = ( $iv['end'] <= $now_tz );

			$outcomes[] = array(
				'success'  => $is_success,
				'complete' => $is_complete,
			);

			if ( $is_complete ) {
				$total_complete++;
				if ( $is_success ) {
					$total_success++;
					$run++;
					if ( $run > $longest_streak ) {
						$longest_streak = $run;
					}
				} else {
					$run = 0;
				}
			}
		}

		// Current streak: count consecutive successful intervals, ignoring an in-progress interval unless it is already successful.
		$current_streak = 0;
		for ( $i = count( $outcomes ) - 1; $i >= 0; $i-- ) {
			$o = $outcomes[ $i ];

			if ( ! $o['complete'] ) {
				if ( $o['success'] ) {
					$current_streak++;
				}
				continue;
			}

			if ( $o['success'] ) {
				$current_streak++;
				continue;
			}

			break;
		}

		if ( $current_streak > $longest_streak ) {
			$longest_streak = $current_streak;
		}

		$pct = 0;
		if ( $total_complete > 0 ) {
			$pct = ( $total_success / $total_complete ) * 100;
		}

		return array(
			'available'       => true,
			'successPct'      => $pct,
			'successPctLabel' => round( $pct ) . '%',
			'successfulCount' => $total_success,
			'totalCount'      => $total_complete,
			'longestStreak'   => $longest_streak,
			'currentStreak'   => $current_streak,
		);
	}


    private function tct_composite_feature_config() {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'composite_feature_state' ) ) ) {
            $cfg = TCT_Plugin::composite_feature_state();
            if ( is_array( $cfg ) ) {
                return $cfg;
            }
        }

        return array(
            'enabled' => false,
            'goalType' => 'composite_parent',
            'scaffoldOnly' => true,
        );
    }

    private function tct_composite_goals_enabled() {
        $cfg = $this->tct_composite_feature_config();
        return ! empty( $cfg['enabled'] );
    }

    private function tct_composite_goal_type_requested( $goal_type ) {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'is_composite_goal_type' ) ) ) {
            return (bool) TCT_Plugin::is_composite_goal_type( $goal_type );
        }

        return ( 'composite_parent' === strtolower( trim( (string) $goal_type ) ) );
    }

    private function tct_composite_goal_upsert_scaffold_message() {
        if ( $this->tct_composite_goals_enabled() ) {
            return 'Composite goals are scaffolded in this build but not yet available.';
        }

        return 'Composite goals are currently disabled.';
    }

    private function tct_composite_root_attrs_html( $today_ymd = '' ) {
        $cfg = $this->tct_composite_feature_config();
        $goal_type = isset( $cfg['goalType'] ) ? (string) $cfg['goalType'] : 'composite_parent';
        $attrs = '';

        if ( '' !== (string) $today_ymd ) {
            $attrs .= ' data-tct-today="' . esc_attr( (string) $today_ymd ) . '"';
        }

        $attrs .= ' data-tct-composite-goals-enabled="' . ( ! empty( $cfg['enabled'] ) ? '1' : '0' ) . '"';
        $attrs .= ' data-tct-composite-goal-type="' . esc_attr( $goal_type ) . '"';

        return $attrs;
    }

    private function tct_compose_goal_modal_stats( $goal_modal_stats, $roles = array(), $domains = array() ) {
        $stats = is_array( $goal_modal_stats ) ? $goal_modal_stats : array();
        $stats['composite'] = $this->tct_composite_feature_config();
        $stats['composite']['progressExponent'] = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_progress_exponent' ) ) ? (float) TCT_Utils::composite_goal_progress_exponent() : 1.2;
        $stats['composite']['perfectBonusRate'] = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_perfect_bonus_rate' ) ) ? (float) TCT_Utils::composite_goal_perfect_bonus_rate() : 0.10;
        $stats['composite']['pickerGoals'] = $this->tct_build_composite_goal_modal_catalog( $roles, $domains );
        return $stats;
    }

    private function tct_render_composite_goal_modal_scaffold() {
        $cfg = $this->tct_composite_feature_config();
        $goal_type = isset( $cfg['goalType'] ) ? (string) $cfg['goalType'] : 'composite_parent';
        $enabled = ! empty( $cfg['enabled'] ) ? '1' : '0';

        return '<div hidden="hidden" data-tct-composite-goal-scaffold="1" data-tct-composite-goals-enabled="' . esc_attr( $enabled ) . '" data-tct-composite-goal-type="' . esc_attr( $goal_type ) . '" aria-hidden="true"></div>';
    }


    private function tct_build_composite_goal_modal_catalog( $roles = array(), $domains = array() ) {
        global $wpdb;

        $cfg = $this->tct_composite_feature_config();
        if ( empty( $cfg['enabled'] ) ) {
            return array();
        }

        $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
        if ( $user_id <= 0 || ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) ) {
            return array();
        }

        $goals_table = TCT_DB::table_goals();
        $aliases_select = $this->tct_goal_aliases_select_sql();
        $availability_cycle_select = $this->tct_goal_availability_cycle_select_sql();
        $interval_anchor_select = $this->tct_goal_interval_anchor_select_sql();
        $composite_config_select = $this->tct_goal_composite_config_select_sql();

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, user_id, goal_name, {$aliases_select}, goal_type, role_id, domain_id, is_tracked, points_per_completion, target, period_unit, period_span, period_mode, intervals_json, threshold, {$availability_cycle_select}, {$interval_anchor_select}, {$composite_config_select} FROM {$goals_table} WHERE user_id = %d ORDER BY goal_name ASC, id ASC",
                $user_id
            ),
            ARRAY_A
        );
        if ( ! is_array( $rows ) ) {
            $rows = array();
        }

        $role_map = $this->get_role_map( $roles );
        $domain_map = $this->get_domain_map( $domains );
        $goal_name_map = array();
        foreach ( $rows as $row ) {
            $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0;
            if ( $goal_id <= 0 ) {
                continue;
            }
            $goal_name_map[ $goal_id ] = $this->tct_composite_goal_label_from_row( $row, $goal_id );
        }

        $parent_map = array();
        $sort_map = array();
        if ( method_exists( 'TCT_DB', 'table_composite_children' ) ) {
            $links_table = TCT_DB::table_composite_children();
            $link_rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT parent_goal_id, child_goal_id, sort_order FROM {$links_table} WHERE user_id = %d ORDER BY sort_order ASC, id ASC",
                    $user_id
                ),
                ARRAY_A
            );
            if ( is_array( $link_rows ) ) {
                foreach ( $link_rows as $link_row ) {
                    $child_goal_id = isset( $link_row['child_goal_id'] ) ? (int) $link_row['child_goal_id'] : 0;
                    $parent_goal_id = isset( $link_row['parent_goal_id'] ) ? (int) $link_row['parent_goal_id'] : 0;
                    if ( $child_goal_id <= 0 || $parent_goal_id <= 0 ) {
                        continue;
                    }
                    $parent_map[ $child_goal_id ] = $parent_goal_id;
                    $sort_map[ $child_goal_id ] = isset( $link_row['sort_order'] ) ? (int) $link_row['sort_order'] : 0;
                }
            }
        }

        $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
        $now_tz = new DateTimeImmutable( 'now', $tz );
        $catalog = array();

        foreach ( $rows as $row ) {
            $goal_id = isset( $row['id'] ) ? (int) $row['id'] : 0;
            if ( $goal_id <= 0 ) {
                continue;
            }

            $goal_type = isset( $row['goal_type'] ) ? (string) $row['goal_type'] : 'positive';
            $goal_name = $this->tct_composite_goal_label_from_row( $row, $goal_id );
            $aliases = $this->aliases_from_goal_row( $row );
            if ( ! is_array( $aliases ) ) {
                $aliases = array();
            }

            $role_id = isset( $row['role_id'] ) ? (int) $row['role_id'] : 0;
            $domain_id = isset( $row['domain_id'] ) ? (int) $row['domain_id'] : 0;
            if ( $role_id > 0 && isset( $role_map[ $role_id ] ) ) {
                $domain_id = isset( $role_map[ $role_id ]['domain_id'] ) ? (int) $role_map[ $role_id ]['domain_id'] : $domain_id;
            }

            $role_name = $role_id > 0 && isset( $role_map[ $role_id ] ) ? (string) $role_map[ $role_id ]['role_name'] : '';
            $domain_name = $domain_id > 0 && isset( $domain_map[ $domain_id ] ) ? (string) $domain_map[ $domain_id ] : '';

            $intervals = $this->intervals_from_goal_row( $row );
            $interval = ! empty( $intervals ) && isset( $intervals[0] ) && is_array( $intervals[0] ) ? $intervals[0] : array();
            $interval_target = isset( $interval['target'] ) ? (int) $interval['target'] : ( isset( $row['target'] ) ? (int) $row['target'] : 0 );
            $period_unit = isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : ( isset( $row['period_unit'] ) ? (string) $row['period_unit'] : 'week' );
            $period_span = isset( $interval['period_span'] ) ? (int) $interval['period_span'] : ( isset( $row['period_span'] ) ? (int) $row['period_span'] : 1 );
            if ( $period_span < 1 ) {
                $period_span = 1;
            }

            $points_per_completion = isset( $row['points_per_completion'] ) ? (int) $row['points_per_completion'] : 0;
            $bonus_points = 0;
            $penalty_points_magnitude = 0;
            if ( $interval_target > 0 && $points_per_completion > 0 ) {
                $is_anki = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_anki_cards_goal_type' ) ) ? (bool) TCT_Utils::is_anki_cards_goal_type( $goal_type ) : ( 'anki_cards' === strtolower( trim( (string) $goal_type ) ) );
                if ( $is_anki ) {
                    $bonus_points = (int) $points_per_completion;
                    $penalty_points_magnitude = abs( (int) $points_per_completion );
                } else {
                    $bonus_points = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_bonus_points' ) ) ? (int) TCT_Utils::compute_bonus_points( $points_per_completion, $interval_target ) : 0;
                    $penalty_points_magnitude = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'compute_penalty_points' ) ) ? abs( (int) TCT_Utils::compute_penalty_points( $points_per_completion, $interval_target, 0 ) ) : 0;
                }
            }

            $availability_ctx = $this->get_goal_availability_tile_context( $row, $now_tz );
            $current_parent_goal_id = isset( $parent_map[ $goal_id ] ) ? (int) $parent_map[ $goal_id ] : 0;
            $current_parent_goal_label = '';
            if ( $current_parent_goal_id > 0 ) {
                if ( isset( $goal_name_map[ $current_parent_goal_id ] ) ) {
                    $current_parent_goal_label = (string) $goal_name_map[ $current_parent_goal_id ];
                } else {
                    $current_parent_goal_label = 'Goal #' . $current_parent_goal_id;
                }
            }

            $is_candidate = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_composite_child_goal_candidate' ) ) ? (bool) TCT_Utils::is_composite_child_goal_candidate( $row ) : true;
            $is_parent = $this->tct_goal_is_composite_parent( $row, $user_id );
            $is_negative = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_negative_goal_type' ) ) ? (bool) TCT_Utils::is_negative_goal_type( $goal_type ) : false;
            $threshold = isset( $row['threshold'] ) ? (int) $row['threshold'] : null;
            $interval_label = $this->build_cadence_label( $interval_target, $period_unit, $period_span, $is_negative, $goal_type, $threshold );

            $catalog[] = array(
                'goalId' => $goal_id,
                'goalName' => $goal_name,
                'goalType' => $goal_type,
                'roleId' => $role_id,
                'roleName' => $role_name,
                'domainId' => $domain_id,
                'domainName' => $domain_name,
                'pointsPerCompletion' => $points_per_completion,
                'intervalTarget' => $interval_target,
                'periodUnit' => $period_unit,
                'periodSpan' => $period_span,
                'intervalLabel' => $interval_label,
                'bonusPoints' => $bonus_points,
                'penaltyPointsMagnitude' => $penalty_points_magnitude,
                'aliases' => $aliases,
                'isTracked' => ! empty( $row['is_tracked'] ) ? 1 : 0,
                'availabilityEnabled' => ! empty( $availability_ctx['enabled'] ) ? 1 : 0,
                'availabilityPaused' => ! empty( $availability_ctx['is_paused'] ) ? 1 : 0,
                'availabilityStateLabel' => isset( $availability_ctx['state_label'] ) ? (string) $availability_ctx['state_label'] : '',
                'availabilityStateMeta' => isset( $availability_ctx['state_meta'] ) ? (string) $availability_ctx['state_meta'] : '',
                'isCandidate' => $is_candidate ? 1 : 0,
                'isParent' => $is_parent ? 1 : 0,
                'currentParentGoalId' => $current_parent_goal_id,
                'currentParentGoalLabel' => $current_parent_goal_label,
                'currentSortOrder' => isset( $sort_map[ $goal_id ] ) ? (int) $sort_map[ $goal_id ] : 0,
            );
        }

        return $catalog;
    }

    private function tct_render_composite_goal_modal_section() {
        $progress_exponent = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_progress_exponent' ) ) ? (float) TCT_Utils::composite_goal_progress_exponent() : 1.2;
        $perfect_bonus_rate = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_perfect_bonus_rate' ) ) ? (float) TCT_Utils::composite_goal_perfect_bonus_rate() : 0.10;
        $exponent_label = rtrim( rtrim( sprintf( '%.2f', $progress_exponent ), '0' ), '.' );
        $perfect_pct_label = (string) round( $perfect_bonus_rate * 100 );

        $html = '';
        $html .= '<div class="tct-form-row tct-composite-config-row" data-tct-composite-config-row hidden="hidden">';
        $html .= '<div class="tct-form-row-head">Composite children</div>';
        $html .= '<input type="hidden" name="composite_child_ids_json" value="" data-tct-composite-child-ids-json />';
        $html .= '<input type="hidden" name="composite_config_json" value="" data-tct-composite-config-json />';
        $html .= '<p class="tct-muted">Pick ordered child goals for this parent. Child goals remain individually completable and keep their own points.</p>';
        $html .= '<div class="tct-composite-toolbar">';
        $html .= '<label class="tct-label tct-composite-search-label" for="tct-composite-search">Find child goals</label>';
        $html .= '<input id="tct-composite-search" type="search" value="" placeholder="Search by goal, alias, role, or domain" data-tct-composite-search />';
        $html .= '</div>';
        $html .= '<div class="tct-composite-picker-grid">';
        $html .= '<div class="tct-composite-picker-pane">';
        $html .= '<div class="tct-composite-pane-head">Search results</div>';
        $html .= '<div class="tct-composite-results" data-tct-composite-results></div>';
        $html .= '<div class="tct-muted" data-tct-composite-results-empty hidden="hidden">No goals match the current filter.</div>';
        $html .= '</div>';
        $html .= '<div class="tct-composite-picker-pane">';
        $html .= '<div class="tct-composite-pane-head">Selected children <span class="tct-muted">(drag to reorder)</span></div>';
        $html .= '<div class="tct-composite-selected-list" data-tct-composite-selected-list></div>';
        $html .= '<div class="tct-muted" data-tct-composite-selected-empty>Choose one or more child goals.</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="tct-composite-preview-grid">';
        $html .= '<div class="tct-composite-preview-card"><span class="tct-muted">Children</span><strong data-tct-composite-preview-count>0</strong></div>';
        $html .= '<div class="tct-composite-preview-card"><span class="tct-muted">Child task points</span><strong data-tct-composite-preview-points>0</strong></div>';
        $html .= '<div class="tct-composite-preview-card"><span class="tct-muted">Bmax</span><strong data-tct-composite-preview-bmax>0</strong></div>';
        $html .= '<div class="tct-composite-preview-card"><span class="tct-muted">Pmax</span><strong data-tct-composite-preview-pmax>0</strong></div>';
        $html .= '<div class="tct-composite-preview-card"><span class="tct-muted">Perfect bonus</span><strong data-tct-composite-preview-perfect>0</strong></div>';
        $html .= '</div>';
        $html .= '<div class="tct-goal-warning" data-tct-composite-validation hidden="hidden"></div>';
        $html .= '<p class="tct-muted tct-settings-help" data-tct-composite-preview-note>Parent bonus uses Bmax x (r ^ ' . esc_html( $exponent_label ) . '). Perfect completion adds ' . esc_html( $perfect_pct_label ) . '% of Bmax. Parent penalty scales with missed eligible child-point fraction and is capped by remaining child penalty exposure.</p>';
        $html .= '</div>';

        return $html;
    }

    private function tct_prepare_goal_rows_for_composite_surface( $goal_rows, $surface = '', $user_id = 0 ) {
        if ( ! is_array( $goal_rows ) ) {
            return array();
        }

        $surface = is_string( $surface ) ? strtolower( trim( $surface ) ) : '';
        if ( ! in_array( $surface, array( 'dashboard', 'mobile' ), true ) || ! $this->tct_composite_goals_enabled() ) {
            return $goal_rows;
        }

        $user_id = (int) $user_id;
        if ( $user_id <= 0 || ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'get_composite_child_parent_map' ) ) {
            return $goal_rows;
        }

        $goal_ids = array();
        $parent_hide_map = array();
        foreach ( $goal_rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['id'] ) ) {
                continue;
            }

            $goal_id = (int) $row['id'];
            if ( $goal_id <= 0 ) {
                continue;
            }

            $goal_ids[] = $goal_id;
            if ( ! $this->tct_goal_is_composite_parent( $row, $user_id ) ) {
                continue;
            }

            $cfg = $this->tct_goal_composite_config_from_goal_row( $row );
            $hide_children = is_array( $cfg ) && ! empty( $cfg['enabled'] ) && ! empty( $cfg['hideChildrenStandalone'] );
            if ( $hide_children ) {
                $parent_hide_map[ $goal_id ] = true;
            }
        }

        if ( empty( $goal_ids ) || empty( $parent_hide_map ) ) {
            return $goal_rows;
        }

        $parent_map = TCT_DB::get_composite_child_parent_map( $user_id, $goal_ids );
        if ( ! is_array( $parent_map ) || empty( $parent_map ) ) {
            return $goal_rows;
        }

        $filtered = array();
        foreach ( $goal_rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['id'] ) ) {
                $filtered[] = $row;
                continue;
            }

            $goal_id = (int) $row['id'];
            $parent_goal_id = isset( $parent_map[ $goal_id ] ) ? (int) $parent_map[ $goal_id ] : 0;
            if ( $parent_goal_id > 0 && isset( $parent_hide_map[ $parent_goal_id ] ) ) {
                continue;
            }

            $filtered[] = $row;
        }

        return $filtered;
    }

    private function tct_goal_id_from_composite_subject( $goal ) {
        if ( is_array( $goal ) && isset( $goal['id'] ) ) {
            return (int) $goal['id'];
        }

        if ( is_array( $goal ) && isset( $goal['goal_id'] ) ) {
            return (int) $goal['goal_id'];
        }

        if ( is_object( $goal ) ) {
            if ( isset( $goal->id ) ) {
                return (int) $goal->id;
            }
            if ( isset( $goal->goal_id ) ) {
                return (int) $goal->goal_id;
            }
        }

        return (int) $goal;
    }

    private function tct_goal_composite_config_from_goal_row( $goal ) {
        if ( class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'get_goal_composite_config' ) ) {
            return TCT_DB::get_goal_composite_config( $goal );
        }

        return array();
    }

    private function tct_goal_is_composite_parent( $goal, $user_id = 0 ) {
        if ( class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'is_composite_parent_goal' ) ) {
            return (bool) TCT_DB::is_composite_parent_goal( $goal, $user_id );
        }

        return false;
    }

    private function tct_goal_is_nested_composite_child( $goal, $user_id = 0 ) {
        $goal_id = $this->tct_goal_id_from_composite_subject( $goal );
        if ( $goal_id <= 0 ) {
            return false;
        }

        if ( class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'is_goal_assigned_as_composite_child' ) ) {
            return (bool) TCT_DB::is_goal_assigned_as_composite_child( $goal_id, $user_id );
        }

        return false;
    }

    private function tct_goal_composite_children( $goal, $user_id = 0 ) {
        $goal_id = $this->tct_goal_id_from_composite_subject( $goal );
        if ( $goal_id <= 0 ) {
            return array();
        }

        if ( class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'get_composite_children_for_parent' ) ) {
            return TCT_DB::get_composite_children_for_parent( $goal_id, $user_id );
        }

        return array();
    }

    private function tct_composite_goal_type_slug() {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'composite_goal_type' ) ) ) {
            return (string) TCT_Plugin::composite_goal_type();
        }

        return 'composite_parent';
    }

    private function tct_composite_goal_label_from_row( $goal_row, $fallback_goal_id = 0 ) {
        if ( is_array( $goal_row ) && isset( $goal_row['goal_name'] ) ) {
            $goal_name = trim( (string) $goal_row['goal_name'] );
            if ( '' !== $goal_name ) {
                return $goal_name;
            }
        }

        $fallback_goal_id = (int) $fallback_goal_id;
        if ( $fallback_goal_id > 0 ) {
            return 'Goal #' . $fallback_goal_id;
        }

        return 'Selected goal';
    }

    private function tct_composite_posted_child_ids() {
        $keys = array(
            'composite_child_ids',
            'composite_child_ids_json',
            'composite_child_goal_ids',
            'composite_children',
            'composite_children_json',
        );

        foreach ( $keys as $key ) {
            if ( ! array_key_exists( $key, $_POST ) ) {
                continue;
            }

            $raw = wp_unslash( $_POST[ $key ] );
            if ( is_array( $raw ) && 1 === count( $raw ) ) {
                $first = reset( $raw );
                if ( is_string( $first ) && '' !== trim( $first ) ) {
                    $raw = $first;
                }
            }

            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_child_id_list' ) ) ) {
                return TCT_Utils::sanitize_composite_child_id_list( $raw );
            }

            if ( is_array( $raw ) ) {
                $out = array();
                foreach ( $raw as $child_goal_id ) {
                    $child_goal_id = (int) $child_goal_id;
                    if ( $child_goal_id > 0 ) {
                        $out[] = $child_goal_id;
                    }
                }
                return array_values( array_unique( $out ) );
            }
        }

        return array();
    }

    private function tct_composite_posted_config_input() {
        foreach ( array( 'composite_config_json', 'composite_config' ) as $key ) {
            if ( array_key_exists( $key, $_POST ) ) {
                return wp_unslash( $_POST[ $key ] );
            }
        }

        return array();
    }

    private function tct_composite_plan_upsert( $goal_type, $goal_id, $user_id, $role_id, $domain_id, $existing_row = null ) {
        $goal_id = (int) $goal_id;
        $user_id = (int) $user_id;
        $role_id = (int) $role_id;
        $domain_id = (int) $domain_id;
        $is_composite = $this->tct_composite_goal_type_requested( $goal_type );
        $existing_is_parent = $this->tct_goal_is_composite_parent( $existing_row, $user_id );

        if ( ! $is_composite ) {
            if ( $existing_is_parent ) {
                return new WP_Error( 'tct_composite_convert_locked', 'Composite parent conversion is not available in this build yet.' );
            }

            return array(
                'is_composite' => false,
                'config' => array(),
                'config_json' => '',
                'child_ids' => array(),
                'should_sync' => false,
            );
        }

        if ( ! $this->tct_composite_goals_enabled() ) {
            return new WP_Error( 'tct_composite_disabled', 'Composite goals are currently disabled.' );
        }

        if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_goals' ) || ! method_exists( 'TCT_DB', 'table_composite_children' ) ) {
            return new WP_Error( 'tct_composite_storage_unavailable', 'Composite goal storage is unavailable.' );
        }

        if ( ! $this->tct_goal_composite_config_column_exists() ) {
            return new WP_Error( 'tct_composite_config_missing', 'Composite goal storage is not ready yet. Please refresh the plugin schema first.' );
        }

        if ( $goal_id > 0 && $this->tct_goal_is_nested_composite_child( $goal_id, $user_id ) ) {
            return new WP_Error( 'tct_composite_parent_is_child', 'A goal that is already nested under another composite parent cannot become a composite parent.' );
        }

        $child_ids = $this->tct_composite_posted_child_ids();
        if ( empty( $child_ids ) ) {
            return new WP_Error( 'tct_composite_children_required', 'Composite parents require at least one child goal.' );
        }

        $validation = $this->tct_validate_composite_child_selection( $goal_id, $user_id, $role_id, $domain_id, $child_ids );
        if ( is_wp_error( $validation ) ) {
            return $validation;
        }

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_goal_persistence_config' ) ) ) {
            $config = TCT_Utils::sanitize_composite_goal_persistence_config( $this->tct_composite_posted_config_input() );
        } elseif ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_goal_config' ) ) ) {
            $config = TCT_Utils::sanitize_composite_goal_config( $this->tct_composite_posted_config_input() );
            $config['enabled'] = true;
            $config['summaryOnly'] = true;
            $config['hideChildrenStandalone'] = true;
        } else {
            $config = array(
                'version' => 1,
                'enabled' => true,
                'summaryOnly' => true,
                'hideChildrenStandalone' => true,
            );
        }

        $config_json = wp_json_encode( $config );
        if ( ! is_string( $config_json ) || '' === $config_json ) {
            return new WP_Error( 'tct_composite_config_encode_failed', 'Composite goal configuration could not be encoded.' );
        }

        return array(
            'is_composite' => true,
            'config' => $config,
            'config_json' => $config_json,
            'child_ids' => $child_ids,
            'should_sync' => true,
        );
    }

    private function tct_validate_composite_child_selection( $parent_goal_id, $user_id, $role_id, $domain_id, $child_ids ) {
        global $wpdb;

        $parent_goal_id = (int) $parent_goal_id;
        $user_id = (int) $user_id;
        $role_id = (int) $role_id;
        $domain_id = (int) $domain_id;
        $child_ids = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_child_id_list' ) ) ? TCT_Utils::sanitize_composite_child_id_list( $child_ids ) : array();

        if ( empty( $child_ids ) ) {
            return new WP_Error( 'tct_composite_children_required', 'Composite parents require at least one child goal.' );
        }

        $goals_table = TCT_DB::table_goals();
        $select_composite = $this->tct_goal_composite_config_select_sql();
        $sql = $wpdb->prepare(
            "SELECT id, user_id, goal_name, goal_type, role_id, domain_id, is_tracked, points_per_completion, target, period_unit, period_span, period_mode, intervals_json, threshold, {$select_composite} FROM {$goals_table} WHERE user_id = %d AND id IN (" . implode( ',', array_map( 'intval', $child_ids ) ) . ")",
            $user_id
        );
        $rows = $wpdb->get_results( $sql, ARRAY_A );
        if ( ! is_array( $rows ) ) {
            $rows = array();
        }

        $rows_by_id = array();
        foreach ( $rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['id'] ) ) {
                continue;
            }
            $rows_by_id[ (int) $row['id'] ] = $row;
        }

        $parent_map = array();
        if ( method_exists( 'TCT_DB', 'get_composite_child_parent_map' ) ) {
            $parent_map = TCT_DB::get_composite_child_parent_map( $user_id, $child_ids );
        }

        foreach ( $child_ids as $child_goal_id ) {
            $child_goal_id = (int) $child_goal_id;
            if ( $child_goal_id <= 0 ) {
                continue;
            }

            if ( $parent_goal_id > 0 && $child_goal_id === $parent_goal_id ) {
                return new WP_Error( 'tct_composite_self_reference', 'A composite parent cannot include itself as a child.' );
            }

            if ( ! isset( $rows_by_id[ $child_goal_id ] ) ) {
                return new WP_Error( 'tct_composite_child_missing', 'One or more selected child goals could not be found.' );
            }

            $child_row = $rows_by_id[ $child_goal_id ];
            $child_label = $this->tct_composite_goal_label_from_row( $child_row, $child_goal_id );
            $assigned_parent_goal_id = isset( $parent_map[ $child_goal_id ] ) ? (int) $parent_map[ $child_goal_id ] : 0;

            if ( empty( $child_row['is_tracked'] ) ) {
                return new WP_Error( 'tct_composite_child_archived', 'Archived goal "' . $child_label . '" cannot be added as a composite child.' );
            }

            if ( $assigned_parent_goal_id > 0 && $assigned_parent_goal_id !== $parent_goal_id ) {
                return new WP_Error( 'tct_composite_child_already_assigned', 'Goal "' . $child_label . '" is already assigned to another composite parent.' );
            }

            if ( $this->tct_goal_is_composite_parent( $child_row, $user_id ) ) {
                return new WP_Error( 'tct_composite_child_is_parent', 'Goal "' . $child_label . '" is already a composite parent and cannot be attached as a child.' );
            }

            if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'is_composite_child_goal_candidate' ) ) ) {
                $is_allowed = (bool) TCT_Utils::is_composite_child_goal_candidate( $child_row );
            } else {
                $is_allowed = ! TCT_Utils::is_negative_goal_type( isset( $child_row['goal_type'] ) ? (string) $child_row['goal_type'] : '' );
            }

            if ( ! $is_allowed ) {
                return new WP_Error( 'tct_composite_child_invalid_type', 'Goal "' . $child_label . '" is not eligible to be used as a composite child.' );
            }

            $child_role_id = isset( $child_row['role_id'] ) ? (int) $child_row['role_id'] : 0;
            $child_domain_id = isset( $child_row['domain_id'] ) ? (int) $child_row['domain_id'] : 0;
            if ( $child_role_id !== $role_id || $child_domain_id !== $domain_id ) {
                return new WP_Error( 'tct_composite_child_role_domain_mismatch', 'Goal "' . $child_label . '" must match the parent role and domain.' );
            }
        }

        return true;
    }

    private function tct_sync_composite_parent_children( $parent_goal_id, $user_id, $child_ids, $now ) {
        global $wpdb;

        $parent_goal_id = (int) $parent_goal_id;
        $user_id = (int) $user_id;
        $child_ids = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_child_id_list' ) ) ? TCT_Utils::sanitize_composite_child_id_list( $child_ids ) : array();

        if ( $parent_goal_id <= 0 || $user_id <= 0 ) {
            return new WP_Error( 'tct_composite_sync_invalid_parent', 'Composite child links could not be saved because the parent goal is invalid.' );
        }

        if ( empty( $child_ids ) ) {
            return new WP_Error( 'tct_composite_sync_missing_children', 'Composite parents require at least one child goal.' );
        }

        if ( ! class_exists( 'TCT_DB' ) || ! method_exists( 'TCT_DB', 'table_composite_children' ) ) {
            return new WP_Error( 'tct_composite_sync_storage_unavailable', 'Composite child link storage is unavailable.' );
        }

        $table = TCT_DB::table_composite_children();
        $existing_links = method_exists( 'TCT_DB', 'get_composite_child_links_for_parent' ) ? TCT_DB::get_composite_child_links_for_parent( $parent_goal_id, $user_id ) : array();
        $existing_by_child = array();
        foreach ( (array) $existing_links as $link ) {
            if ( ! is_array( $link ) ) {
                continue;
            }
            $child_goal_id = isset( $link['child_goal_id'] ) ? (int) $link['child_goal_id'] : 0;
            if ( $child_goal_id > 0 ) {
                $existing_by_child[ $child_goal_id ] = $link;
            }
        }

        $wanted_map = array_fill_keys( $child_ids, true );
        foreach ( $existing_by_child as $child_goal_id => $link ) {
            if ( isset( $wanted_map[ $child_goal_id ] ) ) {
                continue;
            }

            $deleted = $wpdb->delete(
                $table,
                array(
                    'user_id' => $user_id,
                    'parent_goal_id' => $parent_goal_id,
                    'child_goal_id' => (int) $child_goal_id,
                ),
                array( '%d', '%d', '%d' )
            );

            if ( false === $deleted ) {
                return new WP_Error( 'tct_composite_sync_delete_failed', 'Composite child links could not be updated.' );
            }
        }

        foreach ( $child_ids as $index => $child_goal_id ) {
            $child_goal_id = (int) $child_goal_id;
            if ( $child_goal_id <= 0 ) {
                continue;
            }

            $sort_order = max( 1, (int) $index + 1 );
            if ( isset( $existing_by_child[ $child_goal_id ] ) ) {
                $updated = $wpdb->update(
                    $table,
                    array(
                        'sort_order' => $sort_order,
                        'updated_at' => $now,
                    ),
                    array(
                        'id' => isset( $existing_by_child[ $child_goal_id ]['id'] ) ? (int) $existing_by_child[ $child_goal_id ]['id'] : 0,
                        'user_id' => $user_id,
                    ),
                    array( '%d', '%s' ),
                    array( '%d', '%d' )
                );

                if ( false === $updated ) {
                    return new WP_Error( 'tct_composite_sync_update_failed', 'Composite child order could not be saved.' );
                }

                continue;
            }

            $inserted = $wpdb->insert(
                $table,
                array(
                    'user_id' => $user_id,
                    'parent_goal_id' => $parent_goal_id,
                    'child_goal_id' => $child_goal_id,
                    'sort_order' => $sort_order,
                    'created_at' => $now,
                    'updated_at' => $now,
                ),
                array( '%d', '%d', '%d', '%d', '%s', '%s' )
            );

            if ( false === $inserted ) {
                return new WP_Error( 'tct_composite_sync_insert_failed', 'Composite child links could not be saved.' );
            }
        }

        return true;
    }

    private function tct_composite_dashboard_interval_label( $goal_row ) {
        $interval = null;
        if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) {
            $interval = TCT_Interval::normalize_goal_interval_from_row( $goal_row );
        }

        $unit = is_array( $interval ) && isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : ( isset( $goal_row['period_unit'] ) ? (string) $goal_row['period_unit'] : 'week' );
        $span = is_array( $interval ) && isset( $interval['period_span'] ) ? (int) $interval['period_span'] : ( isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1 );
        if ( $span < 1 ) {
            $span = 1;
        }

        if ( 'semiannual' === $unit ) {
            $unit = 'month';
            $span = $span * 6;
        }

        $labels = array(
            'hour' => array( 'singular' => 'hour', 'plural' => 'hours' ),
            'day' => array( 'singular' => 'day', 'plural' => 'days' ),
            'week' => array( 'singular' => 'week', 'plural' => 'weeks' ),
            'month' => array( 'singular' => 'month', 'plural' => 'months' ),
            'quarter' => array( 'singular' => 'quarter', 'plural' => 'quarters' ),
            'year' => array( 'singular' => 'year', 'plural' => 'years' ),
        );

        if ( ! isset( $labels[ $unit ] ) ) {
            return 'this period';
        }

        if ( 1 === $span ) {
            return 'this ' . $labels[ $unit ]['singular'];
        }

        return 'this ' . $span . ' ' . $labels[ $unit ]['plural'];
    }

    private function tct_composite_dashboard_child_achieved_count( $user_id, $goal_row, $now_tz ) {
        static $cache = array();

        $user_id = (int) $user_id;
        $goal_id = is_array( $goal_row ) && isset( $goal_row['id'] ) ? (int) $goal_row['id'] : 0;
        if ( $user_id <= 0 || $goal_id <= 0 ) {
            return 0;
        }

        $cache_key = $user_id . ':' . $goal_id;
        if ( isset( $cache[ $cache_key ] ) ) {
            return (int) $cache[ $cache_key ];
        }

        $interval = null;
        if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) {
            $interval = TCT_Interval::normalize_goal_interval_from_row( $goal_row );
        }

        $unit = is_array( $interval ) && isset( $interval['period_unit'] ) ? (string) $interval['period_unit'] : ( isset( $goal_row['period_unit'] ) ? (string) $goal_row['period_unit'] : 'week' );
        $span = is_array( $interval ) && isset( $interval['period_span'] ) ? (int) $interval['period_span'] : ( isset( $goal_row['period_span'] ) ? (int) $goal_row['period_span'] : 1 );
        if ( $span < 1 ) {
            $span = 1;
        }

        $achieved = 0;
        $resolved = false;
        if ( class_exists( 'TCT_Vitality' ) && is_callable( array( 'TCT_Vitality', 'compute_for_goal' ) ) ) {
            $goal_row_for_vitality = array(
                'id' => $goal_id,
                'points_per_completion' => isset( $goal_row['points_per_completion'] ) ? (int) $goal_row['points_per_completion'] : 0,
                'target' => is_array( $interval ) && isset( $interval['target'] ) ? (int) $interval['target'] : ( isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0 ),
                'period_unit' => $unit,
                'period_span' => $span,
                'goal_type' => isset( $goal_row['goal_type'] ) && is_string( $goal_row['goal_type'] ) ? (string) $goal_row['goal_type'] : 'positive',
                'threshold' => isset( $goal_row['threshold'] ) && is_numeric( $goal_row['threshold'] ) ? (int) $goal_row['threshold'] : null,
                'importance' => isset( $goal_row['importance'] ) ? (int) $goal_row['importance'] : 0,
                'effort' => isset( $goal_row['effort'] ) ? (int) $goal_row['effort'] : 0,
                'created_at' => isset( $goal_row['created_at'] ) ? (string) $goal_row['created_at'] : '',
                'updated_at' => isset( $goal_row['updated_at'] ) ? (string) $goal_row['updated_at'] : '',
                'sleep_tracking_enabled' => isset( $goal_row['sleep_tracking_enabled'] ) ? (int) $goal_row['sleep_tracking_enabled'] : 0,
                'sleep_rollover_time' => isset( $goal_row['sleep_rollover_time'] ) && is_string( $goal_row['sleep_rollover_time'] ) ? (string) $goal_row['sleep_rollover_time'] : '',
                'interval_anchor_json' => isset( $goal_row['interval_anchor_json'] ) && is_string( $goal_row['interval_anchor_json'] ) ? (string) $goal_row['interval_anchor_json'] : '',
                'composite_config_json' => isset( $goal_row['composite_config_json'] ) && is_string( $goal_row['composite_config_json'] ) ? (string) $goal_row['composite_config_json'] : '',
            );

            try {
                $maybe_vitality = TCT_Vitality::compute_for_goal( $user_id, $goal_row_for_vitality, $now_tz );
                if ( is_array( $maybe_vitality ) && isset( $maybe_vitality['achieved'] ) ) {
                    $achieved = max( 0, (int) $maybe_vitality['achieved'] );
                    $resolved = true;
                }
            } catch ( Exception $e ) {
            }
        }

        if ( ! $resolved && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_completions' ) ) {
            global $wpdb;

            $bounds = $this->current_loop_bounds_for_goal( $goal_row, $now_tz, $unit, $span );
            if ( is_array( $bounds ) && isset( $bounds['start'], $bounds['end'] ) ) {
                $start_utc = TCT_Utils::dt_to_mysql_utc( $bounds['start'] );
                $end_utc = TCT_Utils::dt_to_mysql_utc( $bounds['end'] );
                $completions_table = TCT_DB::table_completions();
                $achieved = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s", $user_id, $goal_id, $start_utc, $end_utc ) );
            }
        }

        if ( $achieved < 0 ) {
            $achieved = 0;
        }

        $cache[ $cache_key ] = $achieved;
        return $achieved;
    }

    private function tct_composite_dashboard_child_preview_item( $user_id, $child_row, $now_tz ) {
        if ( ! is_array( $child_row ) || ! isset( $child_row['id'] ) ) {
            return array();
        }

        $goal_id = (int) $child_row['id'];
        if ( $goal_id <= 0 ) {
            return array();
        }

        $goal_name = $this->tct_composite_goal_label_from_row( $child_row, $goal_id );
        $achieved = $this->tct_composite_dashboard_child_achieved_count( $user_id, $child_row, $now_tz );
        $normalized = array(
            'goal_id' => $goal_id,
            'goal_name' => $goal_name,
            'target' => isset( $child_row['target'] ) ? (int) $child_row['target'] : 0,
            'achieved' => $achieved,
            'eligible' => true,
            'active_at_settlement' => true,
            'paused_at_settlement' => false,
            'bonus_points' => 0,
            'penalty_points' => 0,
        );

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_child_settlement_row_from_goal_row' ) ) ) {
            $normalized = TCT_Utils::composite_goal_child_settlement_row_from_goal_row( $child_row, $now_tz, array( 'achieved' => $achieved ) );
        }

        $target = isset( $normalized['target'] ) ? max( 0, (int) $normalized['target'] ) : 0;
        $completion_ratio = isset( $normalized['completion_ratio'] ) ? (float) $normalized['completion_ratio'] : 0.0;
        $epsilon = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_epsilon' ) ) ? (float) TCT_Utils::composite_goal_epsilon() : 0.0000001;
        $is_complete = ( $completion_ratio >= ( 1.0 - $epsilon ) );
        $is_paused = ! empty( $normalized['paused_at_settlement'] );
        $points_per_completion = isset( $child_row['points_per_completion'] ) ? max( 0, (int) $child_row['points_per_completion'] ) : 0;
        $interval_label = $this->tct_composite_dashboard_interval_label( $child_row );

        if ( $target > 0 ) {
            $state_label = $achieved . '/' . $target . ' ' . $interval_label;
        } else {
            $state_label = $interval_label;
        }
        if ( $is_complete ) {
            $state_label = 'Done - ' . $state_label;
        } elseif ( $is_paused ) {
            $state_label = 'Paused - ' . $state_label;
        }

        return array(
            'goal_id' => $goal_id,
            'goal_name' => $goal_name,
            'achieved' => $achieved,
            'target' => $target,
            'is_complete' => $is_complete,
            'is_paused' => $is_paused,
            'points_per_completion' => $points_per_completion,
            'state_label' => $state_label,
            'interval_label' => $interval_label,
            'normalized' => $normalized,
        );
    }

    private function tct_composite_dashboard_parent_preview_data( $parent_goal, $user_id = 0 ) {
        static $cache = array();

        $parent_goal_id = $this->tct_goal_id_from_composite_subject( $parent_goal );
        $user_id = (int) $user_id;
        if ( $user_id <= 0 && function_exists( 'get_current_user_id' ) ) {
            $user_id = (int) get_current_user_id();
        }
        if ( $parent_goal_id <= 0 || $user_id <= 0 ) {
            return array(
                'children' => array(),
                'payload' => array(),
                'child_count' => 0,
                'complete_count' => 0,
                'paused_count' => 0,
                'eligible_child_count' => 0,
                'all_children_complete' => 0,
                'latest_child_completed_ts' => 0,
                'latest_child_completed_text' => 'never',
            );
        }

        $cache_key = $user_id . ':' . $parent_goal_id;
        if ( isset( $cache[ $cache_key ] ) ) {
            return $cache[ $cache_key ];
        }

        $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
        $now_tz = new DateTimeImmutable( 'now', $tz );
        $child_rows = $this->tct_goal_composite_children( $parent_goal_id, $user_id );
        $children = array();
        $normalized_children = array();
        $complete_count = 0;
        $paused_count = 0;
        $latest_child_completed_ts = 0;
        $latest_child_completed_text = 'never';

        foreach ( (array) $child_rows as $child_row ) {
            $child_item = $this->tct_composite_dashboard_child_preview_item( $user_id, $child_row, $now_tz );
            if ( empty( $child_item ) ) {
                continue;
            }

            $children[] = $child_item;
            if ( ! empty( $child_item['normalized'] ) && is_array( $child_item['normalized'] ) ) {
                $normalized_children[] = $child_item['normalized'];
            }
            if ( ! empty( $child_item['is_complete'] ) ) {
                $complete_count++;
            }
            if ( ! empty( $child_item['is_paused'] ) ) {
                $paused_count++;
            }
        }

        $payload = array();
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_build_settlement_payload' ) ) ) {
            $payload = TCT_Utils::composite_goal_build_settlement_payload(
                $normalized_children,
                array(
                    'parent_goal_id' => $parent_goal_id,
                    'parent_goal_name' => is_array( $parent_goal ) ? $this->tct_composite_goal_label_from_row( $parent_goal, $parent_goal_id ) : 'Composite parent',
                    'settlement_at' => $now_tz,
                )
            );
        }

        $child_goal_ids = array();
        foreach ( $children as $preview_child ) {
            if ( ! is_array( $preview_child ) || ! isset( $preview_child['goal_id'] ) ) {
                continue;
            }

            $preview_child_goal_id = (int) $preview_child['goal_id'];
            if ( $preview_child_goal_id > 0 ) {
                $child_goal_ids[] = $preview_child_goal_id;
            }
        }
        $child_goal_ids = array_values( array_unique( array_filter( array_map( 'intval', $child_goal_ids ) ) ) );

        if ( ! empty( $child_goal_ids ) && class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_completions' ) ) {
            global $wpdb;

            $completions_table = TCT_DB::table_completions();
            if ( is_string( $completions_table ) && '' !== $completions_table ) {
                $placeholders = implode( ', ', array_fill( 0, count( $child_goal_ids ), '%d' ) );
                $query_args = array_merge( array( $user_id ), $child_goal_ids );
                $latest_child_completed_raw = (string) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT MAX(completed_at) FROM {$completions_table} WHERE user_id = %d AND goal_id IN ({$placeholders})",
                        $query_args
                    )
                );

                if ( '' !== $latest_child_completed_raw && '0000-00-00 00:00:00' !== $latest_child_completed_raw ) {
                    $latest_child_completed_tmp = strtotime( $latest_child_completed_raw . ' UTC' );
                    if ( false !== $latest_child_completed_tmp ) {
                        $latest_child_completed_ts = (int) $latest_child_completed_tmp;
                        $now_ts_int = (int) $now_tz->getTimestamp();
                        $diff_s = $now_ts_int - $latest_child_completed_ts;
                        if ( $diff_s >= 0 && $diff_s < 60 ) {
                            $latest_child_completed_text = 'just now';
                        } else {
                            $latest_child_completed_text = $this->tct_abbrev_time_ago( $latest_child_completed_ts, $now_ts_int );
                            if ( 'just now' !== $latest_child_completed_text && '--' !== $latest_child_completed_text ) {
                                $latest_child_completed_text .= ' ago';
                            }
                        }
                    }
                }
            }
        }

        $child_count = count( $children );
        $result = array(
            'children' => $children,
            'payload' => $payload,
            'child_count' => $child_count,
            'complete_count' => $complete_count,
            'paused_count' => $paused_count,
            'eligible_child_count' => isset( $payload['eligible_child_count'] ) ? (int) $payload['eligible_child_count'] : max( 0, $child_count - $paused_count ),
            'all_children_complete' => ( $child_count > 0 && $complete_count >= $child_count ) ? 1 : 0,
            'latest_child_completed_ts' => (int) $latest_child_completed_ts,
            'latest_child_completed_text' => (string) $latest_child_completed_text,
        );

        $cache[ $cache_key ] = $result;
        return $result;
    }

    private function tct_render_composite_goal_tile_scaffold( $goal, $is_connected ) {
        if ( ! is_array( $goal ) || ! $this->tct_composite_goals_enabled() ) {
            return '';
        }

        $surface = isset( $goal['__tct_surface'] ) && is_string( $goal['__tct_surface'] ) ? trim( (string) $goal['__tct_surface'] ) : '';
        $goal_type = isset( $goal['goal_type'] ) && is_string( $goal['goal_type'] ) ? (string) $goal['goal_type'] : '';
        if ( ! $this->tct_composite_goal_type_requested( $goal_type ) ) {
            return '';
        }

        $cfg = $this->tct_goal_composite_config_from_goal_row( $goal );
        if ( ! is_array( $cfg ) || empty( $cfg['enabled'] ) ) {
            return '';
        }

        $goal_id = isset( $goal['goal_id'] ) ? (int) $goal['goal_id'] : 0;
        if ( $goal_id <= 0 ) {
            return '';
        }

        $user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
        $preview = $this->tct_composite_dashboard_parent_preview_data( $goal, $user_id );
        $children = isset( $preview['children'] ) && is_array( $preview['children'] ) ? $preview['children'] : array();
        $payload = isset( $preview['payload'] ) && is_array( $preview['payload'] ) ? $preview['payload'] : array();
        $child_count = isset( $preview['child_count'] ) ? (int) $preview['child_count'] : count( $children );
        $complete_count = isset( $preview['complete_count'] ) ? (int) $preview['complete_count'] : 0;
        $paused_count = isset( $preview['paused_count'] ) ? (int) $preview['paused_count'] : 0;
        $eligible_child_count = isset( $preview['eligible_child_count'] ) ? (int) $preview['eligible_child_count'] : max( 0, $child_count - $paused_count );
        $goal_name = isset( $goal['goal_name'] ) && '' !== trim( (string) $goal['goal_name'] ) ? (string) $goal['goal_name'] : ( 'Goal #' . $goal_id );
        $payload_json = isset( $goal['edit_payload_json'] ) ? (string) $goal['edit_payload_json'] : '';

        $bmax_display = isset( $payload['bmax'] ) ? (float) $payload['bmax'] : 0.0;
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_finalize_bonus_points' ) ) ) {
            $bmax_display = (float) TCT_Utils::composite_goal_finalize_bonus_points( $bmax_display );
        } else {
            $bmax_display = (float) round( $bmax_display );
        }

        $pmax_display = isset( $payload['pmax'] ) ? (float) $payload['pmax'] : 0.0;
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_finalize_penalty_points' ) ) ) {
            $pmax_display = abs( (float) TCT_Utils::composite_goal_finalize_penalty_points( $pmax_display ) );
        } else {
            $pmax_display = abs( (float) round( $pmax_display ) );
        }

        $summary_line = 'No child goals attached yet.';
        if ( $child_count > 0 ) {
            $summary_line = $complete_count . ' of ' . $child_count . ' child goals complete';
            if ( $paused_count > 0 ) {
                $summary_line .= ' | ' . $paused_count . ' paused';
            }
            if ( $eligible_child_count <= 0 && $paused_count > 0 ) {
                $summary_line = 'All child goals are paused right now.';
            }
        }

        if ( 'dashboard' === $surface ) {
            $goal_classes = 'tct-domain-goal tct-composite-parent-tile';
            if ( $eligible_child_count <= 0 && $paused_count > 0 ) {
                $goal_classes .= ' tct-goal-paused';
            }

            $html = '';
            $html .= '<div class="' . esc_attr( $goal_classes ) . '" data-goal-id="' . esc_attr( (string) $goal_id ) . '" data-goal-type="' . esc_attr( $this->tct_composite_goal_type_slug() ) . '" data-tct-composite-parent="1">';
            $html .= '<div class="tct-domain-goal-top">';
            $html .= '<div class="tct-domain-goal-main">';
            $html .= '<div class="tct-domain-goal-title-row">';
            $html .= '<div class="tct-domain-goal-title">' . esc_html( $goal_name ) . '</div>';
            $html .= '<span class="tct-composite-parent-badge">Composite</span>';
            $html .= '</div>';
            $html .= '<div class="tct-domain-goal-sub tct-muted tct-composite-parent-summary">' . esc_html( $summary_line ) . '</div>';
            $html .= '<div class="tct-goal-points-row tct-composite-parent-points">';
            $html .= '<span class="tct-goal-points-pill tct-goal-points-pill-completion">Children ' . esc_html( (string) $child_count ) . '</span>';
            $html .= '<span class="tct-goal-points-pill tct-goal-points-pill-bonus">Bmax ' . esc_html( (string) $bmax_display ) . '</span>';
            $html .= '<span class="tct-goal-points-pill tct-goal-points-pill-penalty">Pmax ' . esc_html( (string) $pmax_display ) . '</span>';
            $html .= '</div>';
            if ( ! empty( $children ) ) {
                $html .= '<div class="tct-composite-parent-checklist">';
                foreach ( $children as $child ) {
                    if ( ! is_array( $child ) ) {
                        continue;
                    }

                    $child_classes = 'tct-composite-parent-child';
                    if ( ! empty( $child['is_complete'] ) ) {
                        $child_classes .= ' is-complete';
                    }
                    if ( ! empty( $child['is_paused'] ) ) {
                        $child_classes .= ' is-paused';
                    }

                    $points_value = isset( $child['points_per_completion'] ) ? max( 0, (int) $child['points_per_completion'] ) : 0;
                    $points_label = $points_value > 0 ? ( '+' . $points_value ) : '0';
                    $child_name = isset( $child['goal_name'] ) ? (string) $child['goal_name'] : 'Child goal';
                    $state_label = isset( $child['state_label'] ) ? (string) $child['state_label'] : '';

                    $html .= '<div class="' . esc_attr( $child_classes ) . '">';
                    $html .= '<span class="tct-composite-parent-child-check" aria-hidden="true"></span>';
                    $html .= '<div class="tct-composite-parent-child-main">';
                    $html .= '<div class="tct-composite-parent-child-title">' . esc_html( $child_name ) . '</div>';
                    if ( '' !== $state_label ) {
                        $html .= '<div class="tct-composite-parent-child-meta">' . esc_html( $state_label ) . '</div>';
                    }
                    $html .= '</div>';
                    $html .= '<div class="tct-composite-parent-child-points">' . esc_html( $points_label ) . '</div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            } else {
                $html .= '<div class="tct-domain-goal-sub tct-muted tct-composite-parent-empty">Add child goals in the edit modal to populate this summary.</div>';
            }
            $html .= '</div>';
            $html .= '<div class="tct-goal-actions">';
            if ( $goal_id > 0 ) {
                $parent_complete_disabled_attr = ( $child_count > 0 ) ? '' : ' disabled="disabled" aria-disabled="true"';
                $parent_complete_title = ( $child_count > 0 ) ? 'Complete all completable child goals for this parent.' : 'This parent has no child goals.';
                $html .= '<button type="button" class="tct-goal-action-btn tct-goal-complete-btn tct-composite-parent-complete-btn" data-tct-composite-parent-complete="1" data-goal-id="' . esc_attr( (string) $goal_id ) . '" data-child-count="' . esc_attr( (string) $child_count ) . '" title="' . esc_attr( $parent_complete_title ) . '"' . $parent_complete_disabled_attr . '>Complete</button>';
                $html .= '<button type="button" class="tct-goal-action-btn tct-goal-history-btn" data-tct-open-goal-history="1" data-goal-id="' . esc_attr( (string) $goal_id ) . '" aria-label="History" title="History"><span class="dashicons dashicons-backup" aria-hidden="true"></span></button>';
            }
            if ( '' !== $payload_json ) {
                $html .= '<button type="button" class="tct-goal-action-btn tct-goal-edit-btn" data-tct-open-goal-modal="edit" data-tct-goal="' . esc_attr( $payload_json ) . '" aria-label="Edit goal" title="Edit goal"><span class="dashicons dashicons-edit" aria-hidden="true"></span></button>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        }

        if ( ! in_array( $surface, array( 'mobile', 'mobile_search' ), true ) ) {
            return '';
        }

        $matched_child_ids = array();
        if ( isset( $goal['__tct_composite_matched_child_ids'] ) && is_array( $goal['__tct_composite_matched_child_ids'] ) ) {
            foreach ( $goal['__tct_composite_matched_child_ids'] as $matched_child_id ) {
                $matched_child_id = (int) $matched_child_id;
                if ( $matched_child_id > 0 ) {
                    $matched_child_ids[] = $matched_child_id;
                }
            }
        }
        $matched_child_ids = array_values( array_unique( $matched_child_ids ) );

        $matched_child_names = array();
        if ( ! empty( $matched_child_ids ) ) {
            foreach ( $children as $child ) {
                if ( ! is_array( $child ) ) {
                    continue;
                }
                $child_id = isset( $child['goal_id'] ) ? (int) $child['goal_id'] : 0;
                if ( $child_id > 0 && in_array( $child_id, $matched_child_ids, true ) ) {
                    $child_name = isset( $child['goal_name'] ) ? trim( (string) $child['goal_name'] ) : '';
                    if ( '' !== $child_name ) {
                        $matched_child_names[] = $child_name;
                    }
                }
            }
        }
        if ( empty( $matched_child_names ) && isset( $goal['__tct_composite_matched_child_names'] ) && is_array( $goal['__tct_composite_matched_child_names'] ) ) {
            foreach ( $goal['__tct_composite_matched_child_names'] as $matched_name ) {
                $matched_name = trim( (string) $matched_name );
                if ( '' !== $matched_name ) {
                    $matched_child_names[] = $matched_name;
                }
            }
        }
        $matched_child_names = array_values( array_unique( $matched_child_names ) );

        $html = '';
        $html .= '<div class="tct-mobile-composite-parent-body" data-tct-composite-parent-body="1">';
        $html .= '<div class="tct-mobile-composite-summary">';
        $html .= '<div class="tct-mobile-composite-summary-line">' . esc_html( $summary_line ) . '</div>';
        $html .= '<div class="tct-mobile-composite-summary-pills">';
        $html .= '<span class="tct-mobile-composite-summary-pill">Children ' . esc_html( (string) $child_count ) . '</span>';
        $html .= '<span class="tct-mobile-composite-summary-pill tct-mobile-composite-summary-pill-bonus">Bmax ' . esc_html( (string) $bmax_display ) . '</span>';
        $html .= '<span class="tct-mobile-composite-summary-pill tct-mobile-composite-summary-pill-penalty">Pmax ' . esc_html( (string) $pmax_display ) . '</span>';
        $html .= '</div>';
        if ( ! empty( $matched_child_names ) ) {
            $match_note = 'Matches: ' . implode( ', ', array_slice( $matched_child_names, 0, 3 ) );
            if ( count( $matched_child_names ) > 3 ) {
                $match_note .= ' +' . ( count( $matched_child_names ) - 3 );
            }
            $html .= '<div class="tct-mobile-composite-match-row"><span class="tct-mobile-composite-match-note">' . esc_html( $match_note ) . '</span></div>';
        }
        $html .= '</div>';

        $child_rows = $this->tct_goal_composite_children( $goal_id, $user_id );
        if ( ! empty( $child_rows ) ) {
            $preview_child_map = array();
            foreach ( $children as $child_preview ) {
                if ( ! is_array( $child_preview ) || ! isset( $child_preview['goal_id'] ) ) {
                    continue;
                }

                $preview_child_goal_id = (int) $child_preview['goal_id'];
                if ( $preview_child_goal_id <= 0 ) {
                    continue;
                }

                $preview_child_map[ $preview_child_goal_id ] = $child_preview;
            }

            $tz = class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'wp_timezone' ) ) ? TCT_Utils::wp_timezone() : new DateTimeZone( 'UTC' );
            $now_tz = new DateTimeImmutable( 'now', $tz );
            $completions_table = class_exists( 'TCT_DB' ) && method_exists( 'TCT_DB', 'table_completions' ) ? TCT_DB::table_completions() : '';
            $rendered_child_count = 0;
            $html .= '<div class="tct-mobile-composite-children" data-tct-mobile-composite-children="1">';
            foreach ( $child_rows as $child_row ) {
                if ( ! is_array( $child_row ) || ! isset( $child_row['id'] ) ) {
                    continue;
                }

                $child_goal_id = (int) $child_row['id'];
                if ( $child_goal_id > 0 && isset( $preview_child_map[ $child_goal_id ] ) && ! empty( $preview_child_map[ $child_goal_id ]['is_complete'] ) ) {
                    continue;
                }

                $extra_tile_goal = array(
                    '__tct_surface' => $surface,
                    '__tct_composite_child_nested' => 1,
                    '__tct_composite_parent_goal_id' => $goal_id,
                    '__tct_ignore_visible_after_time' => 1,
                );

                if ( $child_goal_id > 0 && in_array( $child_goal_id, $matched_child_ids, true ) ) {
                    $extra_tile_goal['__tct_composite_child_match'] = 1;
                }

                $child_item = $this->tct_build_mobile_goal_item_from_row( $child_row, $user_id, $now_tz, $completions_table, $surface, $extra_tile_goal );
                if ( empty( $child_item ) || ! isset( $child_item['tile_goal'] ) || ! is_array( $child_item['tile_goal'] ) ) {
                    continue;
                }

                $html .= $this->tct_render_mobile_result_html(
                    $child_item['tile_goal'],
                    $is_connected,
                    isset( $child_item['vitality'] ) ? (int) $child_item['vitality'] : null,
                    isset( $child_item['goal_name'] ) ? (string) $child_item['goal_name'] : ''
                );
                $rendered_child_count++;
            }
            $html .= '</div>';

            if ( $rendered_child_count <= 0 ) {
                $empty_message = 'No child goals are ready right now.';
                if ( $eligible_child_count <= 0 && $paused_count > 0 ) {
                    $empty_message = 'All child goals are paused right now.';
                } elseif ( $child_count > 0 && $complete_count >= $child_count ) {
                    $empty_message = 'All child goals are complete right now.';
                }
                $html .= '<div class="tct-mobile-composite-empty">' . esc_html( $empty_message ) . '</div>';
            }
        } else {
            $html .= '<div class="tct-mobile-composite-empty">Add child goals in the edit modal to populate this summary.</div>';
        }

        $html .= '</div>';

        return $html;
    }

    private function tct_mobile_result_wrapper_attrs_composite_scaffold( $tile_goal, $status_key = '' ) {
        if ( ! is_array( $tile_goal ) ) {
            return '';
        }

        $goal_type = isset( $tile_goal['goal_type'] ) && is_string( $tile_goal['goal_type'] ) ? (string) $tile_goal['goal_type'] : '';
        $is_composite_parent = $this->tct_composite_goals_enabled() && $this->tct_composite_goal_type_requested( $goal_type );
        $is_nested_child = ! empty( $tile_goal['__tct_composite_child_nested'] );
        $is_match_child = ! empty( $tile_goal['__tct_composite_child_match'] );
        $force_expanded = ! empty( $tile_goal['__tct_composite_force_expanded'] );

        if ( ! $is_composite_parent && ! $is_nested_child && ! $is_match_child && ! $force_expanded ) {
            return '';
        }

        $classes = array( 'tct-mobile-result' );
        $attrs = '';

        $goal_id = isset( $tile_goal['goal_id'] ) ? (int) $tile_goal['goal_id'] : 0;
        if ( $goal_id > 0 ) {
            $attrs .= ' data-goal-id="' . esc_attr( (string) $goal_id ) . '"';
        }

        if ( '' === $status_key && isset( $tile_goal['status_key'] ) && is_string( $tile_goal['status_key'] ) ) {
            $status_key = trim( (string) $tile_goal['status_key'] );
        }
        if ( '' === $status_key ) {
            $status_key = 'on-track';
        }
        $attrs .= ' data-status-key="' . esc_attr( $status_key ) . '"';

        $availability_enabled = ! empty( $tile_goal['availability_enabled'] );
        $availability_is_paused = $availability_enabled && ! empty( $tile_goal['availability_is_paused'] );
        $availability_is_active = $availability_enabled && ! $availability_is_paused;

        if ( $availability_enabled ) {
            $classes[] = 'tct-mobile-result-availability-enabled';
            $attrs .= ' data-tct-availability-enabled="1"';
        }

        if ( $availability_is_paused ) {
            $classes[] = 'tct-mobile-result-paused';
            $attrs .= ' data-tct-availability-paused="1"';
            $attrs .= ' data-tct-availability-phase="pause"';
        } elseif ( $availability_is_active ) {
            $classes[] = 'tct-mobile-result-active-cycle';
            $attrs .= ' data-tct-availability-phase="active"';
        }

        $availability_state_label = isset( $tile_goal['availability_state_label'] ) && is_string( $tile_goal['availability_state_label'] ) ? trim( (string) $tile_goal['availability_state_label'] ) : '';
        if ( '' !== $availability_state_label ) {
            $attrs .= ' data-tct-availability-state-label="' . esc_attr( $availability_state_label ) . '"';
        }

        if ( $is_composite_parent ) {
            $classes[] = 'tct-mobile-result-composite-parent';
            $attrs .= ' data-tct-composite-parent="1"';
        }

        if ( $is_nested_child ) {
            $classes[] = 'tct-mobile-result-composite-child';
            $attrs .= ' data-tct-composite-child="1"';
            $parent_goal_id = isset( $tile_goal['__tct_composite_parent_goal_id'] ) ? (int) $tile_goal['__tct_composite_parent_goal_id'] : 0;
            if ( $parent_goal_id > 0 ) {
                $attrs .= ' data-tct-composite-parent-id="' . esc_attr( (string) $parent_goal_id ) . '"';
            }
        }

        if ( $is_match_child ) {
            $classes[] = 'tct-mobile-result-composite-match';
        }

        if ( $force_expanded ) {
            $classes[] = 'tct-mobile-result-expanded';
        }

        $class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
        return ' class="' . esc_attr( trim( $class_attr ) ) . '"' . $attrs;
    }

}
