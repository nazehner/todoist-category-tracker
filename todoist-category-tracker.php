<?php
/**
 * Plugin Name: Todoist Category Tracker
 * Description: Tracks completed Todoist tasks by label and displays a per-user KPI dashboard via shortcode.
 * Version: 0.4.0
 * Author: Todoist Category Tracker
 * License: GPLv2 or later
 * Text Domain: todoist-category-tracker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TCT_VERSION', '0.4.0' );
define( 'TCT_PLUGIN_FILE', __FILE__ );
define( 'TCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TCT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once TCT_PLUGIN_DIR . 'class-tct-plugin.php';

register_activation_hook( __FILE__, array( 'TCT_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TCT_Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'TCT_Plugin', 'instance' ) );
