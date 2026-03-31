<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'TCT_FEATURE_REWARDS' ) ) {
    define( 'TCT_FEATURE_REWARDS', true );
}

if ( ! defined( 'TCT_FEATURE_COMPOSITE_GOALS' ) ) {
    define( 'TCT_FEATURE_COMPOSITE_GOALS', false );
}

require_once TCT_PLUGIN_DIR . 'class-tct-db.php';
require_once TCT_PLUGIN_DIR . 'class-tct-utils.php';
require_once TCT_PLUGIN_DIR . 'class-tct-interval.php';
require_once TCT_PLUGIN_DIR . 'class-tct-vitality.php';
require_once TCT_PLUGIN_DIR . 'class-tct-admin.php';
require_once TCT_PLUGIN_DIR . 'class-tct-todoist-api.php';
require_once TCT_PLUGIN_DIR . 'class-tct-oauth.php';
require_once TCT_PLUGIN_DIR . 'class-tct-ledger.php';
require_once TCT_PLUGIN_DIR . 'class-tct-sync.php';
require_once TCT_PLUGIN_DIR . 'class-tct-cron.php';
require_once TCT_PLUGIN_DIR . 'class-tct-reward.php';
require_once TCT_PLUGIN_DIR . 'class-tct-hex-grid.php';
require_once TCT_PLUGIN_DIR . 'class-tct-reward-progress-generator.php';
require_once TCT_PLUGIN_DIR . 'class-tct-economy-normalizer.php';
require_once TCT_PLUGIN_DIR . 'class-tct-backup.php';
require_once TCT_PLUGIN_DIR . 'class-tct-shortcode.php';
require_once TCT_PLUGIN_DIR . 'class-tct-history-switch.php';

class TCT_Plugin {
    const FEATURE_FLAG_COMPOSITE_GOALS = 'composite_goals';
    const GOAL_TYPE_COMPOSITE_PARENT = 'composite_parent';
    const OPTION_COMPOSITE_GOALS_ENABLED = 'tct_feature_composite_goals_enabled';

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function composite_goal_type() {
        return self::GOAL_TYPE_COMPOSITE_PARENT;
    }

    public static function is_composite_goal_type( $goal_type ) {
        $goal_type = strtolower( trim( (string) $goal_type ) );
        return ( self::GOAL_TYPE_COMPOSITE_PARENT === $goal_type );
    }

    public static function composite_goals_option_name() {
        return self::OPTION_COMPOSITE_GOALS_ENABLED;
    }

    public static function is_composite_goals_enabled() {
        $enabled = (bool) TCT_FEATURE_COMPOSITE_GOALS;
        $option_name = self::composite_goals_option_name();

        if ( function_exists( 'get_option' ) ) {
            $raw = get_option( $option_name, null );

            if ( null !== $raw ) {
                if ( is_bool( $raw ) ) {
                    $enabled = $raw;
                } elseif ( is_numeric( $raw ) ) {
                    $enabled = ( (int) $raw ) !== 0;
                } else {
                    $enabled = in_array( strtolower( trim( (string) $raw ) ), array( '1', 'true', 'yes', 'on' ), true );
                }
            }
        }

        if ( function_exists( 'apply_filters' ) ) {
            $enabled = (bool) apply_filters( 'tct_feature_composite_goals_enabled', $enabled );
        }

        return $enabled;
    }

    public static function composite_feature_state() {
        return array(
            'enabled' => self::is_composite_goals_enabled(),
            'goalType' => self::composite_goal_type(),
            'scaffoldOnly' => true,
        );
    }

    private function __construct() {
        TCT_DB::maybe_upgrade();

        if ( is_admin() ) {
            add_action( 'admin_init', array( 'TCT_Utils', 'maybe_cleanup_duplicate_assets' ) );
        }

        new TCT_Admin();
        new TCT_OAuth();
        new TCT_Cron();
        new TCT_Shortcode();
        new TCT_History_Switch();

        TCT_Backup::init();
        TCT_Backup::schedule_daily();
    }

    public static function activate() {
        TCT_DB::create_tables();
        TCT_Cron::schedule();
        TCT_Backup::schedule_daily();
    }

    public static function deactivate() {
        TCT_Cron::unschedule();
        TCT_Backup::unschedule();
    }
}
