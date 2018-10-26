<?php
/**
 * Plugin Name: MCC Admin Area
 * Plugin URI:
 * Description: Merivale Community Centre Admin plugin for wordpress.
 * Version: 1.0
 * Author: Saul Boyd
 * Author URI: https://avikar.io
 * Text Domain: mcc-admin-area
 * License: MIT (https://opensource.org/licenses/MIT)
 */
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require_once( plugin_dir_path( __FILE__ ) . './class.mcc-admin-area.php' );
require_once( plugin_dir_path( __FILE__ ) . './includes/shortcodes.php' );

// Activation and Deactivation
register_activation_hook( __FILE__, ['MCCAdminArea', 'activated'] );
register_deactivation_hook( __FILE__, ['MCCAdminArea', 'deactivated'] );

// Init
add_action( 'plugins_loaded', ['MCCAdminArea', 'init'] );
