<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } class TCT_DB { const DB_VERSION = 32; public static function default_domain_palette() { return array( '#2563eb', '#0ea5e9', '#06b6d4', '#4f46e5', '#7c3aed', '#a855f7', '#d946ef', '#f59e0b', '#f97316', '#64748b', ); } public static function table_completions() { global $wpdb; return $wpdb->prefix . 'tct_completions'; } public static function table_goals() { global $wpdb; return $wpdb->prefix . 'tct_goals'; } public static function table_domains() { global $wpdb; return $wpdb->prefix . 'tct_domains'; } public static function table_roles() { global $wpdb; return $wpdb->prefix . 'tct_roles'; } public static function table_ledger() { global $wpdb; return $wpdb->prefix . 'tct_ledger'; } public static function table_sleep_cycles() { global $wpdb; return $wpdb->prefix . 'tct_sleep_cycles'; } public static function table_composite_children() { global $wpdb; return $wpdb->prefix . 'tct_goal_composite_children'; } public static function maybe_upgrade() { self::ensure_goal_aliases_column(); self::ensure_goal_link_url_column(); self::ensure_goal_notes_column(); self::ensure_allowed_fails_columns(); self::ensure_goal_due_schedule_column(); self::ensure_goal_availability_cycle_column(); self::ensure_goal_interval_anchor_column(); self::ensure_goal_composite_config_column(); self::ensure_wake_time_goal_columns(); self::ensure_bed_time_goal_columns(); self::ensure_goal_composite_children_table(); $installed = (int) get_option( 'tct_db_version', 0 ); if ( $installed >= self::DB_VERSION ) { return; } self::create_tables(); } private static function ensure_goal_aliases_column() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( isset( $cols['aliases_json'] ) ) { return; } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN aliases_json LONGTEXT NULL AFTER goal_name" ); } private static function ensure_goal_link_url_column() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( isset( $cols['link_url'] ) ) { return; } $after = isset( $cols['aliases_json'] ) ? 'aliases_json' : 'goal_name'; $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN link_url VARCHAR(1024) NULL DEFAULT NULL AFTER {$after}" ); } private static function ensure_goal_notes_column() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( isset( $cols['goal_notes'] ) ) { return; } $after = isset( $cols['link_url'] ) ? 'link_url' : ( isset( $cols['aliases_json'] ) ? 'aliases_json' : 'goal_name' ); $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN goal_notes LONGTEXT NULL AFTER {$after}" ); } private static function ensure_allowed_fails_columns() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( ! isset( $cols['allowed_fails_target'] ) ) { $after = isset( $cols['period_mode'] ) ? 'period_mode' : 'period_unit'; $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN allowed_fails_target INT(11) NOT NULL DEFAULT 0 AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY allowed_fails_target INT(11) NOT NULL DEFAULT 0" ); } $cols = self::get_columns( $goals ); if ( ! isset( $cols['allowed_fails_unit'] ) ) { $after = isset( $cols['allowed_fails_target'] ) ? 'allowed_fails_target' : ( isset( $cols['period_mode'] ) ? 'period_mode' : 'period_unit' ); $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN allowed_fails_unit VARCHAR(20) NOT NULL DEFAULT 'week' AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY allowed_fails_unit VARCHAR(20) NOT NULL DEFAULT 'week'" ); } $cols = self::get_columns( $goals ); if ( ! isset( $cols['allowed_fails_span'] ) ) { $after = isset( $cols['allowed_fails_unit'] ) ? 'allowed_fails_unit' : ( isset( $cols['allowed_fails_target'] ) ? 'allowed_fails_target' : ( isset( $cols['period_mode'] ) ? 'period_mode' : 'period_unit' ) ); $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN allowed_fails_span INT(11) NOT NULL DEFAULT 1 AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY allowed_fails_span INT(11) NOT NULL DEFAULT 1" ); } } private static function ensure_goal_due_schedule_column() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( isset( $cols['due_schedule_json'] ) ) { return; } $after = 'intervals_json'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['allowed_fails_span'] ) ) { $after = 'allowed_fails_span'; } elseif ( isset( $cols['allowed_fails_unit'] ) ) { $after = 'allowed_fails_unit'; } elseif ( isset( $cols['allowed_fails_target'] ) ) { $after = 'allowed_fails_target'; } elseif ( isset( $cols['period_mode'] ) ) { $after = 'period_mode'; } elseif ( isset( $cols['period_span'] ) ) { $after = 'period_span'; } elseif ( isset( $cols['period_unit'] ) ) { $after = 'period_unit'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN due_schedule_json LONGTEXT NULL AFTER {$after}" ); } private static function ensure_goal_availability_cycle_column() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( isset( $cols['availability_cycle_json'] ) ) { return; } $after = 'due_schedule_json'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['intervals_json'] ) ) { $after = 'intervals_json'; } elseif ( isset( $cols['allowed_fails_span'] ) ) { $after = 'allowed_fails_span'; } elseif ( isset( $cols['allowed_fails_unit'] ) ) { $after = 'allowed_fails_unit'; } elseif ( isset( $cols['allowed_fails_target'] ) ) { $after = 'allowed_fails_target'; } elseif ( isset( $cols['period_mode'] ) ) { $after = 'period_mode'; } elseif ( isset( $cols['period_span'] ) ) { $after = 'period_span'; } elseif ( isset( $cols['period_unit'] ) ) { $after = 'period_unit'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN availability_cycle_json LONGTEXT NULL AFTER {$after}" ); } private static function ensure_goal_interval_anchor_column() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( isset( $cols['interval_anchor_json'] ) ) { return; } $after = 'availability_cycle_json'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['due_schedule_json'] ) ) { $after = 'due_schedule_json'; } elseif ( isset( $cols['intervals_json'] ) ) { $after = 'intervals_json'; } elseif ( isset( $cols['allowed_fails_span'] ) ) { $after = 'allowed_fails_span'; } elseif ( isset( $cols['allowed_fails_unit'] ) ) { $after = 'allowed_fails_unit'; } elseif ( isset( $cols['allowed_fails_target'] ) ) { $after = 'allowed_fails_target'; } elseif ( isset( $cols['period_mode'] ) ) { $after = 'period_mode'; } elseif ( isset( $cols['period_span'] ) ) { $after = 'period_span'; } elseif ( isset( $cols['period_unit'] ) ) { $after = 'period_unit'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN interval_anchor_json LONGTEXT NULL AFTER {$after}" ); } private static function ensure_wake_time_goal_columns() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( ! isset( $cols['wake_time_enabled'] ) ) { $after = 'sleep_rollover_time'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['sleep_tracking_enabled'] ) ) { $after = 'sleep_tracking_enabled'; } elseif ( isset( $cols['visible_after_time'] ) ) { $after = 'visible_after_time'; } elseif ( isset( $cols['alarm_vibration'] ) ) { $after = 'alarm_vibration'; } elseif ( isset( $cols['alarm_duration'] ) ) { $after = 'alarm_duration'; } elseif ( isset( $cols['alarm_sound'] ) ) { $after = 'alarm_sound'; } elseif ( isset( $cols['timer_duration_seconds'] ) ) { $after = 'timer_duration_seconds'; } elseif ( isset( $cols['threshold'] ) ) { $after = 'threshold'; } elseif ( isset( $cols['goal_type'] ) ) { $after = 'goal_type'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN wake_time_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY wake_time_enabled TINYINT(1) NOT NULL DEFAULT 0" ); } $cols = self::get_columns( $goals ); if ( ! isset( $cols['wake_time_target'] ) ) { $after = 'wake_time_enabled'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['sleep_rollover_time'] ) ) { $after = 'sleep_rollover_time'; } elseif ( isset( $cols['sleep_tracking_enabled'] ) ) { $after = 'sleep_tracking_enabled'; } elseif ( isset( $cols['visible_after_time'] ) ) { $after = 'visible_after_time'; } elseif ( isset( $cols['alarm_vibration'] ) ) { $after = 'alarm_vibration'; } elseif ( isset( $cols['alarm_duration'] ) ) { $after = 'alarm_duration'; } elseif ( isset( $cols['alarm_sound'] ) ) { $after = 'alarm_sound'; } elseif ( isset( $cols['timer_duration_seconds'] ) ) { $after = 'timer_duration_seconds'; } elseif ( isset( $cols['threshold'] ) ) { $after = 'threshold'; } elseif ( isset( $cols['goal_type'] ) ) { $after = 'goal_type'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN wake_time_target VARCHAR(5) NULL DEFAULT NULL AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY wake_time_target VARCHAR(5) NULL DEFAULT NULL" ); } } private static function ensure_bed_time_goal_columns() { global $wpdb; $goals = self::table_goals(); $pattern = $wpdb->esc_like( $goals ); $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) ); if ( ! $exists ) { return; } $cols = self::get_columns( $goals ); if ( ! isset( $cols['bed_time_enabled'] ) ) { $after = 'wake_time_target'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['wake_time_enabled'] ) ) { $after = 'wake_time_enabled'; } elseif ( isset( $cols['sleep_rollover_time'] ) ) { $after = 'sleep_rollover_time'; } elseif ( isset( $cols['sleep_tracking_enabled'] ) ) { $after = 'sleep_tracking_enabled'; } elseif ( isset( $cols['visible_after_time'] ) ) { $after = 'visible_after_time'; } elseif ( isset( $cols['alarm_vibration'] ) ) { $after = 'alarm_vibration'; } elseif ( isset( $cols['alarm_duration'] ) ) { $after = 'alarm_duration'; } elseif ( isset( $cols['alarm_sound'] ) ) { $after = 'alarm_sound'; } elseif ( isset( $cols['timer_duration_seconds'] ) ) { $after = 'timer_duration_seconds'; } elseif ( isset( $cols['threshold'] ) ) { $after = 'threshold'; } elseif ( isset( $cols['goal_type'] ) ) { $after = 'goal_type'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN bed_time_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY bed_time_enabled TINYINT(1) NOT NULL DEFAULT 0" ); } $cols = self::get_columns( $goals ); if ( ! isset( $cols['bed_time_target'] ) ) { $after = 'bed_time_enabled'; if ( ! isset( $cols[ $after ] ) ) { if ( isset( $cols['wake_time_target'] ) ) { $after = 'wake_time_target'; } elseif ( isset( $cols['wake_time_enabled'] ) ) { $after = 'wake_time_enabled'; } elseif ( isset( $cols['sleep_rollover_time'] ) ) { $after = 'sleep_rollover_time'; } elseif ( isset( $cols['sleep_tracking_enabled'] ) ) { $after = 'sleep_tracking_enabled'; } elseif ( isset( $cols['visible_after_time'] ) ) { $after = 'visible_after_time'; } elseif ( isset( $cols['alarm_vibration'] ) ) { $after = 'alarm_vibration'; } elseif ( isset( $cols['alarm_duration'] ) ) { $after = 'alarm_duration'; } elseif ( isset( $cols['alarm_sound'] ) ) { $after = 'alarm_sound'; } elseif ( isset( $cols['timer_duration_seconds'] ) ) { $after = 'timer_duration_seconds'; } elseif ( isset( $cols['threshold'] ) ) { $after = 'threshold'; } elseif ( isset( $cols['goal_type'] ) ) { $after = 'goal_type'; } else { $after = 'goal_name'; } } $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN bed_time_target VARCHAR(5) NULL DEFAULT NULL AFTER {$after}" ); } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY bed_time_target VARCHAR(5) NULL DEFAULT NULL" ); } } private static function get_columns( $table ) { global $wpdb; $cols = array(); $rows = $wpdb->get_results( "SHOW COLUMNS FROM {$table}", ARRAY_A ); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { if ( isset( $r['Field'] ) ) { $cols[ (string) $r['Field'] ] = true; } } } return $cols; } private static function get_indexes( $table ) { global $wpdb; $idx = array(); $rows = $wpdb->get_results( "SHOW INDEX FROM {$table}", ARRAY_A ); if ( is_array( $rows ) ) { foreach ( $rows as $r ) { if ( isset( $r['Key_name'] ) ) { $idx[ (string) $r['Key_name'] ] = true; } } } return $idx; } 

    private static function table_exists( $table ) {
        global $wpdb;
        static $cache = array();

        $table = is_string( $table ) ? trim( $table ) : '';
        if ( '' === $table ) {
            return false;
        }

        if ( array_key_exists( $table, $cache ) ) {
            return (bool) $cache[ $table ];
        }

        $pattern = $wpdb->esc_like( $table );
        $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) );
        $cache[ $table ] = ! empty( $exists );
        return (bool) $cache[ $table ];
    }

    private static function ensure_goal_composite_config_column() {
        global $wpdb;

        $goals = self::table_goals();
        if ( ! self::table_exists( $goals ) ) {
            return;
        }

        $cols = self::get_columns( $goals );
        if ( isset( $cols['composite_config_json'] ) ) {
            return;
        }

        $after = 'interval_anchor_json';
        if ( ! isset( $cols[ $after ] ) ) {
            if ( isset( $cols['availability_cycle_json'] ) ) {
                $after = 'availability_cycle_json';
            } elseif ( isset( $cols['due_schedule_json'] ) ) {
                $after = 'due_schedule_json';
            } elseif ( isset( $cols['intervals_json'] ) ) {
                $after = 'intervals_json';
            } elseif ( isset( $cols['allowed_fails_span'] ) ) {
                $after = 'allowed_fails_span';
            } elseif ( isset( $cols['allowed_fails_unit'] ) ) {
                $after = 'allowed_fails_unit';
            } elseif ( isset( $cols['allowed_fails_target'] ) ) {
                $after = 'allowed_fails_target';
            } elseif ( isset( $cols['period_mode'] ) ) {
                $after = 'period_mode';
            } elseif ( isset( $cols['period_span'] ) ) {
                $after = 'period_span';
            } elseif ( isset( $cols['period_unit'] ) ) {
                $after = 'period_unit';
            } else {
                $after = 'goal_name';
            }
        }

        $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN composite_config_json LONGTEXT NULL AFTER {$after}" );
    }

    private static function composite_children_table_sql( $charset_collate = '' ) {
        $table = self::table_composite_children();
        $charset_collate = is_string( $charset_collate ) ? trim( $charset_collate ) : '';

        return "CREATE TABLE {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            parent_goal_id BIGINT(20) UNSIGNED NOT NULL,
            child_goal_id BIGINT(20) UNSIGNED NOT NULL,
            sort_order INT(11) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_user_child (user_id, child_goal_id),
            UNIQUE KEY uniq_parent_child (parent_goal_id, child_goal_id),
            KEY user_parent_sort (user_id, parent_goal_id, sort_order),
            KEY user_parent (user_id, parent_goal_id),
            KEY user_child (user_id, child_goal_id)
        ) {$charset_collate};";
    }

    private static function ensure_goal_composite_children_table() {
        global $wpdb;

        $table = self::table_composite_children();
        if ( self::table_exists( $table ) ) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( self::composite_children_table_sql( $wpdb->get_charset_collate() ) );
    }
public static function create_tables() { global $wpdb; require_once ABSPATH . 'wp-admin/includes/upgrade.php'; $charset_collate = $wpdb->get_charset_collate(); $installed = (int) get_option( 'tct_db_version', 0 ); $completions = self::table_completions(); $goals = self::table_goals(); $domains = self::table_domains(); $roles = self::table_roles(); $ledger = self::table_ledger(); $sleep_cycles = self::table_sleep_cycles(); $composite_children = self::table_composite_children(); $sql_completions = "CREATE TABLE {$completions} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            goal_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            source VARCHAR(16) NOT NULL DEFAULT 'todoist',
            source_ref VARCHAR(160) NOT NULL DEFAULT '',
            todoist_completed_id VARCHAR(64) NULL DEFAULT '',
            todoist_task_id VARCHAR(64) NULL DEFAULT '',
            label_name VARCHAR(255) NULL DEFAULT '',
            task_content TEXT NULL,
            note TEXT NULL,
            completed_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_user_source_ref (user_id, source, source_ref(150)),
            KEY user_completed_at (user_id, completed_at),
            KEY user_goal_completed_at (user_id, goal_id, completed_at),
            KEY user_label_completed_at (user_id, label_name(191), completed_at)
        ) {$charset_collate};"; $sql_domains = "CREATE TABLE {$domains} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            domain_name VARCHAR(255) NOT NULL,
            color_hex VARCHAR(16) NOT NULL DEFAULT '',
            sort_order INT(11) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_user_domain (user_id, domain_name(191)),
            KEY user_sort (user_id, sort_order)
        ) {$charset_collate};"; $sql_roles = "CREATE TABLE {$roles} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            domain_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            role_name VARCHAR(255) NOT NULL,
            sort_order INT(11) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_user_domain_role (user_id, domain_id, role_name(191)),
            KEY user_domain_sort (user_id, domain_id, sort_order)
        ) {$charset_collate};"; $sql_goals = "CREATE TABLE {$goals} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            label_name VARCHAR(255) NULL,
            tracking_mode VARCHAR(16) NOT NULL DEFAULT 'todoist',
            goal_name VARCHAR(255) NOT NULL DEFAULT '',
            aliases_json LONGTEXT NULL,
            link_url VARCHAR(1024) NULL DEFAULT NULL,
            goal_notes LONGTEXT NULL,
            plant_name VARCHAR(255) NULL DEFAULT NULL,
            goal_type VARCHAR(16) NOT NULL DEFAULT 'positive',
            threshold INT(11) NULL DEFAULT NULL,
            timer_duration_seconds INT(11) NOT NULL DEFAULT 0,
            alarm_sound VARCHAR(32) NOT NULL DEFAULT '',
            alarm_duration INT(11) NOT NULL DEFAULT 0,
            alarm_vibration TINYINT(1) NOT NULL DEFAULT 0,
            visible_after_time VARCHAR(5) NULL DEFAULT NULL,
            sleep_tracking_enabled TINYINT(1) NOT NULL DEFAULT 0,
            sleep_rollover_time VARCHAR(5) NOT NULL DEFAULT '18:00',
            wake_time_enabled TINYINT(1) NOT NULL DEFAULT 0,
            wake_time_target VARCHAR(5) NULL DEFAULT NULL,
            bed_time_enabled TINYINT(1) NOT NULL DEFAULT 0,
            bed_time_target VARCHAR(5) NULL DEFAULT NULL,
            fail_button_enabled TINYINT(1) NOT NULL DEFAULT 0,
            is_favorite TINYINT(1) NOT NULL DEFAULT 0,
            domain_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            role_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            is_tracked TINYINT(1) NOT NULL DEFAULT 0,
            target INT(11) NOT NULL DEFAULT 0,
            period_unit VARCHAR(16) NOT NULL DEFAULT 'week',
            period_span INT(11) NOT NULL DEFAULT 1,
            period_mode VARCHAR(16) NOT NULL DEFAULT 'calendar',
            allowed_fails_target INT(11) NOT NULL DEFAULT 0,
            allowed_fails_unit VARCHAR(20) NOT NULL DEFAULT 'week',
            allowed_fails_span INT(11) NOT NULL DEFAULT 1,
            intervals_json LONGTEXT NULL,
            due_schedule_json LONGTEXT NULL,
            availability_cycle_json LONGTEXT NULL,
            interval_anchor_json LONGTEXT NULL,
            composite_config_json LONGTEXT NULL,
            points_per_completion INT(11) NOT NULL DEFAULT 0,
            importance TINYINT(2) NOT NULL DEFAULT 0,
            effort TINYINT(2) NOT NULL DEFAULT 0,
            points_enabled_at DATETIME NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_user_label (user_id, label_name(191)),
            KEY user_domain (user_id, domain_id),
            KEY user_role (user_id, role_id)
        ) {$charset_collate};"; $sql_ledger = "CREATE TABLE {$ledger} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            event_key VARCHAR(80) NOT NULL,
            event_type VARCHAR(32) NOT NULL,
            points INT(11) NOT NULL DEFAULT 0,
            occurred_at DATETIME NOT NULL,
            goal_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            goal_name VARCHAR(255) NOT NULL DEFAULT '',
            label_name VARCHAR(255) NOT NULL DEFAULT '',
            todoist_completed_id VARCHAR(64) NOT NULL DEFAULT '',
            todoist_task_id VARCHAR(64) NOT NULL DEFAULT '',
            interval_unit VARCHAR(16) NOT NULL DEFAULT '',
            interval_mode VARCHAR(16) NOT NULL DEFAULT '',
            interval_target INT(11) NOT NULL DEFAULT 0,
            bonus_points INT(11) NOT NULL DEFAULT 0,
            window_start DATETIME NULL,
            window_end DATETIME NULL,
            met TINYINT(1) NOT NULL DEFAULT 0,
            details TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_user_event (user_id, event_key),
            KEY user_occurred_at (user_id, occurred_at),
            KEY user_goal (user_id, goal_id)
        ) {$charset_collate};"; $sql_sleep_cycles = "CREATE TABLE {$sleep_cycles} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            goal_id BIGINT(20) UNSIGNED NOT NULL,
            sleep_date DATE NOT NULL,
            bed_time VARCHAR(5) NULL DEFAULT NULL,
            wake_time VARCHAR(5) NULL DEFAULT NULL,
            date_overridden TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_user_goal_sleep_date (user_id, goal_id, sleep_date),
            KEY user_sleep_date (user_id, sleep_date),
            KEY user_goal_sleep_date (user_id, goal_id, sleep_date)
        ) {$charset_collate};"; $sql_composite_children = self::composite_children_table_sql( $charset_collate ); dbDelta( $sql_completions ); dbDelta( $sql_domains ); dbDelta( $sql_roles ); dbDelta( $sql_goals ); dbDelta( $sql_ledger ); dbDelta( $sql_sleep_cycles ); dbDelta( $sql_composite_children ); $now = current_time( 'mysql', true ); $completion_indexes = self::get_indexes( $completions ); if ( isset( $completion_indexes['uniq_user_label_completed'] ) ) { $wpdb->query( "ALTER TABLE {$completions} DROP INDEX uniq_user_label_completed" ); } if ( isset( $completion_indexes['uniq_user_label_completed_at'] ) ) { $wpdb->query( "ALTER TABLE {$completions} DROP INDEX uniq_user_label_completed_at" ); } $completion_cols = self::get_columns( $completions ); if ( ! isset( $completion_cols['goal_id'] ) ) { $wpdb->query( "ALTER TABLE {$completions} ADD COLUMN goal_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER user_id" ); } if ( ! isset( $completion_cols['source'] ) ) { $wpdb->query( "ALTER TABLE {$completions} ADD COLUMN source VARCHAR(16) NOT NULL DEFAULT 'todoist' AFTER goal_id" ); } if ( ! isset( $completion_cols['source_ref'] ) ) { $wpdb->query( "ALTER TABLE {$completions} ADD COLUMN source_ref VARCHAR(160) NOT NULL DEFAULT '' AFTER source" ); } if ( ! isset( $completion_cols['note'] ) ) { $wpdb->query( "ALTER TABLE {$completions} ADD COLUMN note TEXT NULL AFTER task_content" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['tracking_mode'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN tracking_mode VARCHAR(16) NOT NULL DEFAULT 'todoist' AFTER label_name" ); } if ( isset( $goal_cols['label_name'] ) ) { $wpdb->query( "ALTER TABLE {$goals} MODIFY label_name VARCHAR(255) NULL" ); } if ( ! isset( $goal_cols['aliases_json'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN aliases_json LONGTEXT NULL AFTER goal_name" ); } if ( ! isset( $goal_cols['plant_name'] ) ) { if ( isset( $goal_cols['goal_name'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN plant_name VARCHAR(255) NULL DEFAULT NULL AFTER goal_name" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN plant_name VARCHAR(255) NULL DEFAULT NULL" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY plant_name VARCHAR(255) NULL DEFAULT NULL" ); } if ( ! isset( $goal_cols['goal_type'] ) ) { if ( isset( $goal_cols['plant_name'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN goal_type VARCHAR(16) NOT NULL DEFAULT 'positive' AFTER plant_name" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN goal_type VARCHAR(16) NOT NULL DEFAULT 'positive'" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY goal_type VARCHAR(16) NOT NULL DEFAULT 'positive'" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['threshold'] ) ) { if ( isset( $goal_cols['goal_type'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN threshold INT(11) NULL DEFAULT NULL AFTER goal_type" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN threshold INT(11) NULL DEFAULT NULL" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY threshold INT(11) NULL DEFAULT NULL" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['timer_duration_seconds'] ) ) { if ( isset( $goal_cols['threshold'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN timer_duration_seconds INT(11) NOT NULL DEFAULT 0 AFTER threshold" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN timer_duration_seconds INT(11) NOT NULL DEFAULT 0" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY timer_duration_seconds INT(11) NOT NULL DEFAULT 0" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['alarm_sound'] ) ) { if ( isset( $goal_cols['timer_duration_seconds'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN alarm_sound VARCHAR(32) NOT NULL DEFAULT '' AFTER timer_duration_seconds" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN alarm_sound VARCHAR(32) NOT NULL DEFAULT ''" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY alarm_sound VARCHAR(32) NOT NULL DEFAULT ''" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['alarm_duration'] ) ) { if ( isset( $goal_cols['alarm_sound'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN alarm_duration INT(11) NOT NULL DEFAULT 0 AFTER alarm_sound" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN alarm_duration INT(11) NOT NULL DEFAULT 0" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY alarm_duration INT(11) NOT NULL DEFAULT 0" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['alarm_vibration'] ) ) { if ( isset( $goal_cols['alarm_duration'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN alarm_vibration TINYINT(1) NOT NULL DEFAULT 0 AFTER alarm_duration" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN alarm_vibration TINYINT(1) NOT NULL DEFAULT 0" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY alarm_vibration TINYINT(1) NOT NULL DEFAULT 0" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['visible_after_time'] ) ) { if ( isset( $goal_cols['alarm_vibration'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN visible_after_time VARCHAR(5) NULL DEFAULT NULL AFTER alarm_vibration" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN visible_after_time VARCHAR(5) NULL DEFAULT NULL" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY visible_after_time VARCHAR(5) NULL DEFAULT NULL" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['sleep_tracking_enabled'] ) ) { if ( isset( $goal_cols['visible_after_time'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN sleep_tracking_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER visible_after_time" ); } elseif ( isset( $goal_cols['alarm_vibration'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN sleep_tracking_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER alarm_vibration" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN sleep_tracking_enabled TINYINT(1) NOT NULL DEFAULT 0" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY sleep_tracking_enabled TINYINT(1) NOT NULL DEFAULT 0" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['sleep_rollover_time'] ) ) { if ( isset( $goal_cols['sleep_tracking_enabled'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN sleep_rollover_time VARCHAR(5) NOT NULL DEFAULT '18:00' AFTER sleep_tracking_enabled" ); } elseif ( isset( $goal_cols['visible_after_time'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN sleep_rollover_time VARCHAR(5) NOT NULL DEFAULT '18:00' AFTER visible_after_time" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN sleep_rollover_time VARCHAR(5) NOT NULL DEFAULT '18:00'" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY sleep_rollover_time VARCHAR(5) NOT NULL DEFAULT '18:00'" ); } if ( ! isset( $goal_cols['period_span'] ) ) { if ( isset( $goal_cols['period_unit'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN period_span INT(11) NOT NULL DEFAULT 1 AFTER period_unit" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN period_span INT(11) NOT NULL DEFAULT 1" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY period_span INT(11) NOT NULL DEFAULT 1" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['created_at'] ) ) { if ( isset( $goal_cols['points_enabled_at'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN created_at DATETIME NULL AFTER points_enabled_at" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN created_at DATETIME NULL" ); } } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['fail_button_enabled'] ) ) { if ( isset( $goal_cols['sleep_rollover_time'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN fail_button_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER sleep_rollover_time" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN fail_button_enabled TINYINT(1) NOT NULL DEFAULT 0" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY fail_button_enabled TINYINT(1) NOT NULL DEFAULT 0" ); } $goal_cols = self::get_columns( $goals ); if ( ! isset( $goal_cols['is_favorite'] ) ) { if ( isset( $goal_cols['fail_button_enabled'] ) ) { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN is_favorite TINYINT(1) NOT NULL DEFAULT 0 AFTER fail_button_enabled" ); } else { $wpdb->query( "ALTER TABLE {$goals} ADD COLUMN is_favorite TINYINT(1) NOT NULL DEFAULT 0" ); } } else { $wpdb->query( "ALTER TABLE {$goals} MODIFY is_favorite TINYINT(1) NOT NULL DEFAULT 0" ); } $goal_cols = self::get_columns( $goals ); $wpdb->query( "UPDATE {$goals} SET goal_name = label_name WHERE (goal_name = '' OR goal_name IS NULL) AND label_name IS NOT NULL AND label_name <> ''" ); $wpdb->query( "UPDATE {$goals} SET tracking_mode = 'todoist' WHERE tracking_mode = '' OR tracking_mode IS NULL" ); if ( isset( $goal_cols['sleep_tracking_enabled'] ) ) { $wpdb->query( "UPDATE {$goals} SET sleep_tracking_enabled = 0 WHERE sleep_tracking_enabled IS NULL" ); } if ( isset( $goal_cols['sleep_rollover_time'] ) ) { $wpdb->query( "UPDATE {$goals} SET sleep_rollover_time = '18:00' WHERE sleep_rollover_time IS NULL OR sleep_rollover_time = ''" ); } if ( isset( $goal_cols['period_span'] ) ) { $wpdb->query( "UPDATE {$goals} SET period_span = 1 WHERE period_span IS NULL OR period_span < 1" ); } if ( isset( $goal_cols['created_at'] ) ) { $wpdb->query( "UPDATE {$goals} SET created_at = updated_at WHERE created_at IS NULL" ); } if ( isset( $goal_cols['plant_name'] ) ) { $wpdb->query( "UPDATE {$goals} SET plant_name = NULL WHERE plant_name = ''" ); } if ( isset( $goal_cols['goal_type'] ) ) { $wpdb->query( "UPDATE {$goals} SET goal_type = 'positive' WHERE goal_type IS NULL OR goal_type = ''" ); } if ( isset( $goal_cols['threshold'] ) ) { $wpdb->query( "UPDATE {$goals} SET threshold = NULL WHERE goal_type <> 'harm_reduction'" ); } $wpdb->query( "UPDATE {$goals} SET period_mode = 'calendar' WHERE period_mode IS NULL OR period_mode = '' OR period_mode <> 'calendar'" ); if ( isset( $goal_cols['intervals_json'], $goal_cols['target'], $goal_cols['period_unit'], $goal_cols['period_span'], $goal_cols['period_mode'], $goal_cols['goal_type'] ) ) { $legacy = $wpdb->get_results( "SELECT id, user_id, goal_type, target, period_unit, period_span, period_mode, intervals_json
                 FROM {$goals}
                 WHERE intervals_json IS NOT NULL AND intervals_json <> ''", ARRAY_A ); if ( is_array( $legacy ) && ! empty( $legacy ) ) { foreach ( $legacy as $row ) { $gid = isset( $row['id'] ) ? (int) $row['id'] : 0; $uid = isset( $row['user_id'] ) ? (int) $row['user_id'] : 0; if ( $gid <= 0 || $uid <= 0 ) { continue; } $gt = isset( $row['goal_type'] ) ? strtolower( trim( (string) $row['goal_type'] ) ) : ''; if ( 'positive_no_int' === $gt ) { $wpdb->update( $goals, array( 'target' => 0, 'period_mode' => 'calendar', 'intervals_json' => wp_json_encode( array() ), ), array( 'id' => $gid, 'user_id' => $uid, ), array( '%d', '%s', '%s' ), array( '%d', '%d' ) ); continue; } $iv = null; if ( class_exists( 'TCT_Interval' ) && is_callable( array( 'TCT_Interval', 'normalize_goal_interval_from_row' ) ) ) { $iv = TCT_Interval::normalize_goal_interval_from_row( $row ); } if ( ! is_array( $iv ) || empty( $iv ) ) { $wpdb->update( $goals, array( 'intervals_json' => null, 'period_mode' => 'calendar', ), array( 'id' => $gid, 'user_id' => $uid, ), array( '%s', '%s' ), array( '%d', '%d' ) ); continue; } $target = isset( $iv['target'] ) ? (int) $iv['target'] : 0; $unit = isset( $iv['period_unit'] ) ? (string) $iv['period_unit'] : 'week'; $span = isset( $iv['period_span'] ) ? max( 1, (int) $iv['period_span'] ) : 1; $canonical = array( 'target' => (int) $target, 'period_unit' => (string) $unit, 'period_span' => (int) $span, 'period_mode' => 'calendar', ); $wpdb->update( $goals, array( 'target' => (int) $target, 'period_unit' => (string) $unit, 'period_span' => (int) $span, 'period_mode' => 'calendar', 'intervals_json' => wp_json_encode( array( $canonical ) ), ), array( 'id' => $gid, 'user_id' => $uid, ), array( '%d', '%s', '%d', '%s', '%s' ), array( '%d', '%d' ) ); } } } $wpdb->query( "UPDATE {$completions} SET source = 'todoist' WHERE source = '' OR source IS NULL" ); $wpdb->query( "UPDATE {$completions} c
             INNER JOIN {$goals} g
                ON g.user_id = c.user_id AND g.label_name IS NOT NULL AND g.label_name <> '' AND c.label_name IS NOT NULL AND c.label_name <> '' AND g.label_name = c.label_name
             SET c.goal_id = g.id
             WHERE (c.goal_id IS NULL OR c.goal_id = 0)" ); $wpdb->query( "UPDATE {$completions}
             SET source_ref =
                 CASE
                    WHEN todoist_completed_id IS NOT NULL AND todoist_completed_id <> '' AND goal_id > 0 THEN CONCAT(todoist_completed_id, ':', goal_id)
                    WHEN todoist_completed_id IS NOT NULL AND todoist_completed_id <> '' AND (goal_id IS NULL OR goal_id = 0) AND label_name IS NOT NULL AND label_name <> '' THEN CONCAT(todoist_completed_id, ':ln:', SHA1(label_name))
                    WHEN (todoist_completed_id IS NULL OR todoist_completed_id = '') AND todoist_task_id IS NOT NULL AND todoist_task_id <> '' THEN CONCAT('legacy:', SHA1(CONCAT(todoist_task_id, ':', completed_at)))
                    ELSE CONCAT('legacy:', SHA1(CONCAT(IFNULL(label_name,''), ':', completed_at)))
                 END
             WHERE source_ref = '' OR source_ref IS NULL" ); $wpdb->query( "DELETE c1 FROM {$completions} c1
             INNER JOIN {$completions} c2
               ON c1.user_id = c2.user_id
              AND c1.source = c2.source
              AND c1.source_ref = c2.source_ref
              AND c1.id > c2.id
             WHERE c1.source_ref IS NOT NULL AND c1.source_ref <> ''" ); $completion_indexes = self::get_indexes( $completions ); if ( ! isset( $completion_indexes['uniq_user_source_ref'] ) ) { $wpdb->query( "ALTER TABLE {$completions} ADD UNIQUE KEY uniq_user_source_ref (user_id, source, source_ref(150))" ); } $pairs = $wpdb->get_results( "SELECT DISTINCT user_id, domain_id
             FROM {$goals}
             WHERE domain_id > 0 AND (role_id IS NULL OR role_id = 0)", ARRAY_A ); if ( is_array( $pairs ) && ! empty( $pairs ) ) { foreach ( $pairs as $p ) { $uid = isset( $p['user_id'] ) ? (int) $p['user_id'] : 0; $did = isset( $p['domain_id'] ) ? (int) $p['domain_id'] : 0; if ( $uid <= 0 || $did <= 0 ) { continue; } $role_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$roles} WHERE user_id = %d AND domain_id = %d AND role_name = %s", $uid, $did, 'General' ) ); if ( $role_id <= 0 ) { $max_sort = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(sort_order) FROM {$roles} WHERE user_id = %d AND domain_id = %d", $uid, $did ) ); $wpdb->insert( $roles, array( 'user_id' => $uid, 'domain_id' => $did, 'role_name' => 'General', 'sort_order' => $max_sort + 1, 'created_at' => $now, 'updated_at' => $now, ), array( '%d', '%d', '%s', '%d', '%s', '%s' ) ); $role_id = (int) $wpdb->insert_id; } if ( $role_id > 0 ) { $wpdb->query( $wpdb->prepare( "UPDATE {$goals} SET role_id = %d WHERE user_id = %d AND domain_id = %d AND (role_id IS NULL OR role_id = 0)", $role_id, $uid, $did ) ); } } } $palette = self::default_domain_palette(); $palette = is_array( $palette ) ? array_values( array_filter( array_map( 'sanitize_hex_color', $palette ) ) ) : array(); $palette_count = count( $palette ); if ( $palette_count > 0 ) { $legacy_palette = array( '#1d4ed8', '#f97316', '#16a34a', '#dc2626', '#7c3aed', '#0ea5e9', '#d946ef', '#14b8a6', ); $legacy_set = array(); foreach ( $legacy_palette as $c ) { $c = sanitize_hex_color( $c ); if ( $c ) { $legacy_set[ strtolower( $c ) ] = true; } } $palette_set = array(); foreach ( $palette as $c ) { $palette_set[ strtolower( $c ) ] = true; } $user_ids = $wpdb->get_col( "SELECT DISTINCT user_id FROM {$domains}" ); if ( is_array( $user_ids ) && ! empty( $user_ids ) ) { foreach ( $user_ids as $uid_raw ) { $uid = (int) $uid_raw; if ( $uid <= 0 ) { continue; } $rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, color_hex FROM {$domains} WHERE user_id = %d ORDER BY sort_order ASC, domain_name ASC", $uid ), ARRAY_A ); if ( ! is_array( $rows ) || empty( $rows ) ) { continue; } $used = array(); $needs = array(); foreach ( $rows as $r ) { $id = isset( $r['id'] ) ? (int) $r['id'] : 0; $col = isset( $r['color_hex'] ) ? sanitize_hex_color( $r['color_hex'] ) : ''; if ( $id <= 0 ) { continue; } if ( $installed < 18 ) { if ( '' === $col || isset( $legacy_set[ strtolower( $col ) ] ) ) { $needs[] = $id; continue; } if ( '' !== $col && isset( $palette_set[ strtolower( $col ) ] ) ) { $used[ strtolower( $col ) ] = true; } } else { if ( '' === $col ) { $needs[] = $id; continue; } if ( '' !== $col && isset( $palette_set[ strtolower( $col ) ] ) ) { $used[ strtolower( $col ) ] = true; } } } if ( empty( $needs ) ) { continue; } foreach ( $needs as $domain_id ) { $chosen = ''; foreach ( $palette as $color ) { $key = strtolower( $color ); if ( ! isset( $used[ $key ] ) ) { $chosen = $color; $used[ $key ] = true; break; } } if ( '' === $chosen ) { $chosen = (string) $palette[0]; } $wpdb->update( $domains, array( 'color_hex' => $chosen, 'updated_at' => $now, ), array( 'id' => (int) $domain_id, 'user_id' => (int) $uid, ), array( '%s', '%s' ), array( '%d', '%d' ) ); } } } } $ok = true; $completion_cols = self::get_columns( $completions ); $goal_cols = self::get_columns( $goals ); if ( $installed < 25 && isset( $goal_cols['fail_button_enabled'] ) ) { $wpdb->query( "UPDATE {$goals} SET fail_button_enabled = 1 WHERE fail_button_enabled = 0 AND goal_type NOT IN ('never','harm_reduction') AND id IN (SELECT DISTINCT goal_id FROM {$ledger} WHERE event_type = 'manual_fail' AND goal_id > 0)" ); } $sleep_cols = self::get_columns( $sleep_cycles ); $completion_idx = self::get_indexes( $completions ); $sleep_idx = self::get_indexes( $sleep_cycles ); if ( ! isset( $completion_cols['goal_id'], $completion_cols['source'], $completion_cols['source_ref'] ) ) { $ok = false; } if ( ! isset( $goal_cols['tracking_mode'], $goal_cols['period_span'], $goal_cols['plant_name'], $goal_cols['goal_type'], $goal_cols['threshold'], $goal_cols['visible_after_time'], $goal_cols['sleep_tracking_enabled'], $goal_cols['sleep_rollover_time'], $goal_cols['wake_time_enabled'], $goal_cols['wake_time_target'], $goal_cols['aliases_json'], $goal_cols['due_schedule_json'], $goal_cols['allowed_fails_target'], $goal_cols['allowed_fails_unit'], $goal_cols['allowed_fails_span'], $goal_cols['availability_cycle_json'], $goal_cols['interval_anchor_json'] ) ) { $ok = false; } if ( ! isset( $sleep_cols['user_id'], $sleep_cols['goal_id'], $sleep_cols['sleep_date'], $sleep_cols['bed_time'], $sleep_cols['wake_time'] ) ) { $ok = false; } if ( ! isset( $completion_idx['uniq_user_source_ref'] ) ) { $ok = false; } if ( ! isset( $sleep_idx['uniq_user_goal_sleep_date'] ) ) { $ok = false; } if ( $ok ) { update_option( 'tct_db_version', self::DB_VERSION ); } } public static function drop_tables() { global $wpdb; $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_completions() ); $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_goals() ); $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_domains() ); $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_roles() ); $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_ledger() ); $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_sleep_cycles() ); $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_composite_children() ); }

    private static function composite_goal_type_slug() {
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'composite_goal_type' ) ) ) {
            return (string) TCT_Plugin::composite_goal_type();
        }

        return 'composite_parent';
    }

    public static function composite_goal_config_defaults() {
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'composite_goal_config_defaults' ) ) ) {
            return TCT_Utils::composite_goal_config_defaults();
        }

        return array(
            'version' => 1,
            'enabled' => false,
            'summaryOnly' => true,
            'hideChildrenStandalone' => true,
        );
    }

    private static function get_goal_row_for_composite( $goal, $user_id = 0 ) {
        global $wpdb;

        $seed_row = null;
        $goal_id = 0;
        if ( is_array( $goal ) ) {
            $seed_row = $goal;
            if ( isset( $goal['id'] ) ) {
                $goal_id = (int) $goal['id'];
            } elseif ( isset( $goal['goal_id'] ) ) {
                $goal_id = (int) $goal['goal_id'];
            }
            if ( isset( $goal['user_id'] ) ) {
                $user_id = (int) $goal['user_id'];
            }
            if ( array_key_exists( 'goal_type', $goal ) && array_key_exists( 'composite_config_json', $goal ) ) {
                return $goal;
            }
        } elseif ( is_object( $goal ) ) {
            $seed_row = get_object_vars( $goal );
            if ( isset( $goal->id ) ) {
                $goal_id = (int) $goal->id;
            } elseif ( isset( $goal->goal_id ) ) {
                $goal_id = (int) $goal->goal_id;
            }
            if ( isset( $goal->user_id ) ) {
                $user_id = (int) $goal->user_id;
            }
            if ( is_array( $seed_row ) && array_key_exists( 'goal_type', $seed_row ) && array_key_exists( 'composite_config_json', $seed_row ) ) {
                return $seed_row;
            }
        } else {
            $goal_id = (int) $goal;
        }

        $user_id = (int) $user_id;
        if ( $goal_id <= 0 ) {
            return is_array( $seed_row ) ? $seed_row : null;
        }

        $goals = self::table_goals();
        if ( ! self::table_exists( $goals ) ) {
            return is_array( $seed_row ) ? $seed_row : null;
        }

        if ( $user_id > 0 ) {
            $sql = $wpdb->prepare(
                "SELECT id, user_id, goal_type, composite_config_json FROM {$goals} WHERE id = %d AND user_id = %d LIMIT 1",
                $goal_id,
                $user_id
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT id, user_id, goal_type, composite_config_json FROM {$goals} WHERE id = %d LIMIT 1",
                $goal_id
            );
        }

        $row = $wpdb->get_row( $sql, ARRAY_A );
        if ( ! is_array( $row ) ) {
            return is_array( $seed_row ) ? $seed_row : null;
        }

        if ( is_array( $seed_row ) ) {
            return array_merge( $seed_row, $row );
        }

        return $row;
    }

    public static function get_goal_composite_config_value( $goal ) {
        $row = self::get_goal_row_for_composite( $goal );
        if ( ! is_array( $row ) ) {
            return null;
        }

        return array_key_exists( 'composite_config_json', $row ) ? $row['composite_config_json'] : null;
    }

    public static function get_goal_composite_config( $goal ) {
        $raw = self::get_goal_composite_config_value( $goal );

        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_goal_config' ) ) ) {
            return TCT_Utils::sanitize_composite_goal_config( $raw );
        }

        $defaults = self::composite_goal_config_defaults();
        if ( is_string( $raw ) && '' !== trim( $raw ) ) {
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                return array_merge( $defaults, $decoded );
            }
        }

        return $defaults;
    }

    public static function is_composite_parent_goal( $goal, $user_id = 0 ) {
        $row = self::get_goal_row_for_composite( $goal, $user_id );
        if ( ! is_array( $row ) ) {
            return false;
        }

        $goal_type = isset( $row['goal_type'] ) ? (string) $row['goal_type'] : '';
        if ( class_exists( 'TCT_Plugin' ) && is_callable( array( 'TCT_Plugin', 'is_composite_goal_type' ) ) ) {
            return (bool) TCT_Plugin::is_composite_goal_type( $goal_type );
        }

        return ( self::composite_goal_type_slug() === strtolower( trim( $goal_type ) ) );
    }

    public static function get_composite_parent_goal_id_for_child( $child_goal_id, $user_id = 0 ) {
        global $wpdb;

        $child_goal_id = (int) $child_goal_id;
        $user_id = (int) $user_id;
        if ( $child_goal_id <= 0 ) {
            return 0;
        }

        $table = self::table_composite_children();
        if ( ! self::table_exists( $table ) ) {
            return 0;
        }

        if ( $user_id > 0 ) {
            $parent_goal_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT parent_goal_id FROM {$table} WHERE user_id = %d AND child_goal_id = %d LIMIT 1",
                    $user_id,
                    $child_goal_id
                )
            );
        } else {
            $parent_goal_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT parent_goal_id FROM {$table} WHERE child_goal_id = %d LIMIT 1",
                    $child_goal_id
                )
            );
        }

        return (int) $parent_goal_id;
    }

    public static function is_goal_assigned_as_composite_child( $child_goal_id, $user_id = 0 ) {
        return self::get_composite_parent_goal_id_for_child( $child_goal_id, $user_id ) > 0;
    }

    public static function get_composite_child_parent_map( $user_id = 0, $child_goal_ids = array() ) {
        global $wpdb;

        $user_id = (int) $user_id;
        $table = self::table_composite_children();
        if ( ! self::table_exists( $table ) ) {
            return array();
        }

        $ids = array();
        if ( class_exists( 'TCT_Utils' ) && is_callable( array( 'TCT_Utils', 'sanitize_composite_child_id_list' ) ) ) {
            $ids = TCT_Utils::sanitize_composite_child_id_list( $child_goal_ids );
        } elseif ( is_array( $child_goal_ids ) ) {
            foreach ( $child_goal_ids as $child_goal_id ) {
                $child_goal_id = (int) $child_goal_id;
                if ( $child_goal_id > 0 ) {
                    $ids[] = $child_goal_id;
                }
            }
        }

        $where = array();
        if ( $user_id > 0 ) {
            $where[] = $wpdb->prepare( 'user_id = %d', $user_id );
        }
        if ( ! empty( $ids ) ) {
            $where[] = 'child_goal_id IN (' . implode( ',', array_map( 'intval', $ids ) ) . ')';
        }

        $sql = "SELECT child_goal_id, parent_goal_id FROM {$table}";
        if ( ! empty( $where ) ) {
            $sql .= ' WHERE ' . implode( ' AND ', $where );
        }

        $rows = $wpdb->get_results( $sql, ARRAY_A );
        if ( ! is_array( $rows ) ) {
            return array();
        }

        $map = array();
        foreach ( $rows as $row ) {
            $child_goal_id = isset( $row['child_goal_id'] ) ? (int) $row['child_goal_id'] : 0;
            $parent_goal_id = isset( $row['parent_goal_id'] ) ? (int) $row['parent_goal_id'] : 0;
            if ( $child_goal_id <= 0 || $parent_goal_id <= 0 ) {
                continue;
            }
            $map[ $child_goal_id ] = $parent_goal_id;
        }

        return $map;
    }

    public static function get_composite_child_links_for_parent( $parent_goal_id, $user_id = 0 ) {
        global $wpdb;

        $parent_goal_id = (int) $parent_goal_id;
        $user_id = (int) $user_id;
        if ( $parent_goal_id <= 0 ) {
            return array();
        }

        $table = self::table_composite_children();
        if ( ! self::table_exists( $table ) ) {
            return array();
        }

        if ( $user_id > 0 ) {
            $sql = $wpdb->prepare(
                "SELECT id, user_id, parent_goal_id, child_goal_id, sort_order, created_at, updated_at FROM {$table} WHERE user_id = %d AND parent_goal_id = %d ORDER BY sort_order ASC, id ASC",
                $user_id,
                $parent_goal_id
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT id, user_id, parent_goal_id, child_goal_id, sort_order, created_at, updated_at FROM {$table} WHERE parent_goal_id = %d ORDER BY sort_order ASC, id ASC",
                $parent_goal_id
            );
        }

        $rows = $wpdb->get_results( $sql, ARRAY_A );
        if ( ! is_array( $rows ) ) {
            return array();
        }

        foreach ( $rows as &$row ) {
            $row['id'] = isset( $row['id'] ) ? (int) $row['id'] : 0;
            $row['user_id'] = isset( $row['user_id'] ) ? (int) $row['user_id'] : 0;
            $row['parent_goal_id'] = isset( $row['parent_goal_id'] ) ? (int) $row['parent_goal_id'] : 0;
            $row['child_goal_id'] = isset( $row['child_goal_id'] ) ? (int) $row['child_goal_id'] : 0;
            $row['sort_order'] = isset( $row['sort_order'] ) ? (int) $row['sort_order'] : 0;
        }
        unset( $row );

        return $rows;
    }

    public static function get_composite_child_ids_for_parent( $parent_goal_id, $user_id = 0 ) {
        $links = self::get_composite_child_links_for_parent( $parent_goal_id, $user_id );
        if ( empty( $links ) ) {
            return array();
        }

        $child_ids = array();
        foreach ( $links as $link ) {
            $child_goal_id = isset( $link['child_goal_id'] ) ? (int) $link['child_goal_id'] : 0;
            if ( $child_goal_id > 0 ) {
                $child_ids[] = $child_goal_id;
            }
        }

        return $child_ids;
    }

    public static function get_composite_children_for_parent( $parent_goal_id, $user_id = 0 ) {
        global $wpdb;

        $parent_goal_id = (int) $parent_goal_id;
        $user_id = (int) $user_id;
        if ( $parent_goal_id <= 0 ) {
            return array();
        }

        $links_table = self::table_composite_children();
        $goals_table = self::table_goals();
        if ( ! self::table_exists( $links_table ) || ! self::table_exists( $goals_table ) ) {
            return array();
        }

        if ( $user_id > 0 ) {
            $sql = $wpdb->prepare(
                "SELECT g.*, c.parent_goal_id AS composite_parent_goal_id, c.sort_order AS composite_sort_order FROM {$links_table} c INNER JOIN {$goals_table} g ON g.id = c.child_goal_id WHERE c.user_id = %d AND c.parent_goal_id = %d AND g.user_id = %d ORDER BY c.sort_order ASC, c.id ASC, g.goal_name ASC",
                $user_id,
                $parent_goal_id,
                $user_id
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT g.*, c.parent_goal_id AS composite_parent_goal_id, c.sort_order AS composite_sort_order FROM {$links_table} c INNER JOIN {$goals_table} g ON g.id = c.child_goal_id WHERE c.parent_goal_id = %d ORDER BY c.sort_order ASC, c.id ASC, g.goal_name ASC",
                $parent_goal_id
            );
        }

        $rows = $wpdb->get_results( $sql, ARRAY_A );
        if ( ! is_array( $rows ) ) {
            return array();
        }

        foreach ( $rows as &$row ) {
            $row['composite_parent_goal_id'] = isset( $row['composite_parent_goal_id'] ) ? (int) $row['composite_parent_goal_id'] : 0;
            $row['composite_sort_order'] = isset( $row['composite_sort_order'] ) ? (int) $row['composite_sort_order'] : 0;
        }
        unset( $row );

        return $rows;
    }
 public static function insert_manual_completion( $user_id, $goal_id, $completed_at_utc_mysql = null, $todoist_task_id = '', $note = '' ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; if ( $user_id <= 0 || $goal_id <= 0 ) { return new WP_Error( 'tct_invalid_goal', 'Invalid goal.' ); } $table = self::table_completions(); $now = current_time( 'mysql', true ); if ( null === $completed_at_utc_mysql || '' === trim( (string) $completed_at_utc_mysql ) ) { $completed_at_utc_mysql = $now; } $source_ref = 'manual:' . wp_generate_uuid4(); $data = array( 'user_id' => $user_id, 'goal_id' => $goal_id, 'source' => 'manual', 'source_ref' => $source_ref, 'todoist_completed_id' => '', 'todoist_task_id' => sanitize_text_field( (string) $todoist_task_id ), 'label_name' => '', 'task_content' => null, 'note' => is_string( $note ) ? $note : '', 'completed_at' => $completed_at_utc_mysql, 'created_at' => $now, ); $formats = array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ); $ok = $wpdb->insert( $table, $data, $formats ); if ( false === $ok ) { return new WP_Error( 'tct_db_insert_error', 'Database error while inserting completion event.' ); } return array( 'id' => (int) $wpdb->insert_id, 'source_ref' => $source_ref, 'completed_at'=> $completed_at_utc_mysql, ); } public static function get_sleep_cycle( $user_id, $goal_id, $sleep_date ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $sleep_date = sanitize_text_field( (string) $sleep_date ); if ( $user_id <= 0 || $goal_id <= 0 ) { return new WP_Error( 'tct_invalid_goal', 'Invalid goal.' ); } if ( ! self::is_valid_ymd( $sleep_date ) ) { return new WP_Error( 'tct_invalid_sleep_date', 'Invalid sleep date.' ); } $table = self::table_sleep_cycles(); $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND goal_id = %d AND sleep_date = %s LIMIT 1", $user_id, $goal_id, $sleep_date ), ARRAY_A ); if ( ! is_array( $row ) ) { return null; } if ( isset( $row['id'] ) ) { $row['id'] = (int) $row['id']; } if ( isset( $row['user_id'] ) ) { $row['user_id'] = (int) $row['user_id']; } if ( isset( $row['goal_id'] ) ) { $row['goal_id'] = (int) $row['goal_id']; } if ( isset( $row['date_overridden'] ) ) { $row['date_overridden'] = (int) $row['date_overridden']; } if ( isset( $row['bed_time'], $row['wake_time'] ) && is_string( $row['bed_time'] ) && is_string( $row['wake_time'] ) && '' !== $row['bed_time'] && '' !== $row['wake_time'] ) { $row['duration_hhmm'] = self::calculate_sleep_duration_hhmm( $row['bed_time'], $row['wake_time'] ); } else { $row['duration_hhmm'] = ''; } return $row; } public static function upsert_sleep_bed_time( $user_id, $goal_id, $sleep_date, $bed_time, $date_overridden = false ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $sleep_date = sanitize_text_field( (string) $sleep_date ); $bed_time = sanitize_text_field( (string) $bed_time ); if ( $user_id <= 0 || $goal_id <= 0 ) { return new WP_Error( 'tct_invalid_goal', 'Invalid goal.' ); } if ( ! self::is_valid_ymd( $sleep_date ) ) { return new WP_Error( 'tct_invalid_sleep_date', 'Invalid sleep date.' ); } if ( ! self::is_valid_hhmm( $bed_time ) ) { return new WP_Error( 'tct_invalid_bed_time', 'Invalid bedtime.' ); } $table = self::table_sleep_cycles(); $now = current_time( 'mysql', true ); $date_overridden_i = $date_overridden ? 1 : 0; $ok = $wpdb->query( $wpdb->prepare( "UPDATE {$table}
                 SET bed_time = %s,
                     date_overridden = GREATEST(date_overridden, %d),
                     updated_at = %s
                 WHERE user_id = %d AND goal_id = %d AND sleep_date = %s", $bed_time, $date_overridden_i, $now, $user_id, $goal_id, $sleep_date ) ); if ( false === $ok ) { return new WP_Error( 'tct_db_update_error', 'Database error while saving bedtime.' ); } $existing = self::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_wp_error( $existing ) ) { return $existing; } if ( is_array( $existing ) ) { return $existing; } $data = array( 'user_id' => $user_id, 'goal_id' => $goal_id, 'sleep_date' => $sleep_date, 'bed_time' => $bed_time, 'wake_time' => null, 'date_overridden' => $date_overridden_i, 'created_at' => $now, 'updated_at' => $now, ); $formats = array( '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s' ); $ins = $wpdb->insert( $table, $data, $formats ); if ( false === $ins ) { if ( false !== stripos( (string) $wpdb->last_error, 'duplicate' ) ) { $row = self::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_array( $row ) ) { return $row; } } return new WP_Error( 'tct_db_insert_error', 'Database error while creating sleep cycle.' ); } $row = self::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_wp_error( $row ) || null === $row ) { return new WP_Error( 'tct_db_read_error', 'Saved bedtime but failed to read sleep cycle.' ); } return $row; } public static function upsert_sleep_wake_time( $user_id, $goal_id, $sleep_date, $wake_time, $date_overridden = false ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $sleep_date = sanitize_text_field( (string) $sleep_date ); $wake_time = sanitize_text_field( (string) $wake_time ); if ( $user_id <= 0 || $goal_id <= 0 ) { return new WP_Error( 'tct_invalid_goal', 'Invalid goal.' ); } if ( ! self::is_valid_ymd( $sleep_date ) ) { return new WP_Error( 'tct_invalid_sleep_date', 'Invalid sleep date.' ); } if ( ! self::is_valid_hhmm( $wake_time ) ) { return new WP_Error( 'tct_invalid_wake_time', 'Invalid wake-time.' ); } $existing = self::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_wp_error( $existing ) ) { return $existing; } if ( ! is_array( $existing ) ) { return new WP_Error( 'tct_sleep_missing_bedtime', 'Bedtime must be saved before wake-time.' ); } if ( empty( $existing['bed_time'] ) || ! is_string( $existing['bed_time'] ) ) { return new WP_Error( 'tct_sleep_missing_bedtime', 'Bedtime must be saved before wake-time.' ); } $table = self::table_sleep_cycles(); $now = current_time( 'mysql', true ); $date_overridden_i = $date_overridden ? 1 : 0; $ok = $wpdb->query( $wpdb->prepare( "UPDATE {$table}
                 SET wake_time = %s,
                     date_overridden = GREATEST(date_overridden, %d),
                     updated_at = %s
                 WHERE user_id = %d AND goal_id = %d AND sleep_date = %s", $wake_time, $date_overridden_i, $now, $user_id, $goal_id, $sleep_date ) ); if ( false === $ok ) { return new WP_Error( 'tct_db_update_error', 'Database error while saving wake-time.' ); } $row = self::get_sleep_cycle( $user_id, $goal_id, $sleep_date ); if ( is_wp_error( $row ) || null === $row ) { return new WP_Error( 'tct_db_read_error', 'Saved wake-time but failed to read sleep cycle.' ); } return $row; } public static function list_completed_sleep_cycles( $user_id, $goal_id = 0, $limit = 0, $offset = 0 ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $limit = (int) $limit; $offset = (int) $offset; if ( $user_id <= 0 ) { return array(); } $table = self::table_sleep_cycles(); $sql = "SELECT * FROM {$table}
                   WHERE user_id = %d
                     AND bed_time IS NOT NULL AND bed_time <> ''
                     AND wake_time IS NOT NULL AND wake_time <> ''"; $params = array( $user_id ); if ( $goal_id > 0 ) { $sql .= ' AND goal_id = %d'; $params[] = $goal_id; } $sql .= ' ORDER BY sleep_date DESC, id DESC'; if ( $limit > 0 ) { $sql .= ' LIMIT %d'; $params[] = $limit; if ( $offset > 0 ) { $sql .= ' OFFSET %d'; $params[] = $offset; } } $prepared = $wpdb->prepare( $sql, $params ); $rows = $wpdb->get_results( $prepared, ARRAY_A ); if ( ! is_array( $rows ) ) { return array(); } foreach ( $rows as &$r ) { $r['duration_hhmm'] = ''; if ( isset( $r['bed_time'], $r['wake_time'] ) && is_string( $r['bed_time'] ) && is_string( $r['wake_time'] ) ) { $r['duration_hhmm'] = self::calculate_sleep_duration_hhmm( $r['bed_time'], $r['wake_time'] ); } } unset( $r ); return $rows; } public static function calculate_sleep_duration_hhmm( $bed_time, $wake_time ) { $bed_minutes = self::hhmm_to_minutes( (string) $bed_time ); $wake_minutes = self::hhmm_to_minutes( (string) $wake_time ); if ( null === $bed_minutes || null === $wake_minutes ) { return ''; } $diff = $wake_minutes - $bed_minutes; if ( $diff < 0 ) { $diff += 24 * 60; } $hours = (int) floor( $diff / 60 ); $mins = (int) ( $diff % 60 ); return sprintf( '%02d:%02d', $hours, $mins ); } public static function insert_completion_event_explicit( $user_id, $goal_id, $source, $source_ref, $completed_at_utc_mysql = null, $extra = array() ) { global $wpdb; $user_id = (int) $user_id; $goal_id = (int) $goal_id; $source = sanitize_text_field( (string) $source ); $source_ref = sanitize_text_field( (string) $source_ref ); $source = substr( $source, 0, 16 ); $source_ref = substr( $source_ref, 0, 160 ); if ( $user_id <= 0 ) { return new WP_Error( 'tct_invalid_user', 'Invalid user.' ); } if ( '' === $source || '' === $source_ref ) { return new WP_Error( 'tct_invalid_completion_ref', 'Invalid completion reference.' ); } $table = self::table_completions(); $now = current_time( 'mysql', true ); if ( null === $completed_at_utc_mysql || '' === trim( (string) $completed_at_utc_mysql ) ) { $completed_at_utc_mysql = $now; } $todoist_completed_id = ''; $todoist_task_id = ''; $label_name = ''; $task_content = null; $note = ''; if ( is_array( $extra ) ) { if ( isset( $extra['todoist_completed_id'] ) ) { $todoist_completed_id = sanitize_text_field( (string) $extra['todoist_completed_id'] ); } if ( isset( $extra['todoist_task_id'] ) ) { $todoist_task_id = sanitize_text_field( (string) $extra['todoist_task_id'] ); } if ( isset( $extra['label_name'] ) ) { $label_name = sanitize_text_field( (string) $extra['label_name'] ); } if ( array_key_exists( 'task_content', $extra ) ) { $task_content = is_null( $extra['task_content'] ) ? null : (string) $extra['task_content']; } if ( isset( $extra['note'] ) ) { $note = is_string( $extra['note'] ) ? $extra['note'] : ''; } } $data = array( 'user_id' => $user_id, 'goal_id' => $goal_id, 'source' => $source, 'source_ref' => $source_ref, 'todoist_completed_id'=> substr( $todoist_completed_id, 0, 64 ), 'todoist_task_id' => substr( $todoist_task_id, 0, 64 ), 'label_name' => substr( $label_name, 0, 255 ), 'task_content' => $task_content, 'note' => $note, 'completed_at' => $completed_at_utc_mysql, 'created_at' => $now, ); $formats = array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ); $ok = $wpdb->insert( $table, $data, $formats ); if ( false === $ok ) { if ( false !== stripos( (string) $wpdb->last_error, 'duplicate' ) ) { $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id, completed_at FROM {$table} WHERE user_id = %d AND source = %s AND source_ref = %s LIMIT 1", $user_id, $source, $source_ref ), ARRAY_A ); if ( is_array( $existing ) && isset( $existing['id'] ) ) { return array( 'id' => (int) $existing['id'], 'inserted' => false, 'source_ref' => $source_ref, 'completed_at' => isset( $existing['completed_at'] ) ? (string) $existing['completed_at'] : $completed_at_utc_mysql, ); } } return new WP_Error( 'tct_db_insert_error', 'Database error while inserting completion event.' ); } return array( 'id' => (int) $wpdb->insert_id, 'inserted' => true, 'source_ref' => $source_ref, 'completed_at' => $completed_at_utc_mysql, ); } private static function is_valid_ymd( $ymd ) { if ( ! is_string( $ymd ) ) { return false; } $ymd = trim( $ymd ); if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $ymd ) ) { return false; } $parts = explode( '-', $ymd ); if ( 3 !== count( $parts ) ) { return false; } $y = (int) $parts[0]; $m = (int) $parts[1]; $d = (int) $parts[2]; return checkdate( $m, $d, $y ); } private static function is_valid_hhmm( $hhmm ) { if ( ! is_string( $hhmm ) ) { return false; } $hhmm = trim( $hhmm ); if ( 1 !== preg_match( '/^\d{2}:\d{2}$/', $hhmm ) ) { return false; } $parts = explode( ':', $hhmm, 2 ); if ( 2 !== count( $parts ) ) { return false; } $h = (int) $parts[0]; $m = (int) $parts[1]; return ( $h >= 0 && $h <= 23 && $m >= 0 && $m <= 59 ); } private static function hhmm_to_minutes( $hhmm ) { if ( ! self::is_valid_hhmm( $hhmm ) ) { return null; } $parts = explode( ':', trim( $hhmm ), 2 ); $h = (int) $parts[0]; $m = (int) $parts[1]; return ( $h * 60 ) + $m; } } 
