<?php
/**
 * Plugin Name:       WP Top Bar
 * Plugin URI:        https://github.com/saeedcodez/wp-topbar
 * Description:       A lightweight, fast top announcement bar with a custom message, button, image, sticky mode and a scheduling window. Styles are force-applied so themes and other plugins can't override them.
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            SaeedCodez
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-topbar
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'WPTB_VERSION', '1.1.0' );
define( 'WPTB_FILE', __FILE__ );
define( 'WPTB_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPTB_URL', plugin_dir_url( __FILE__ ) );
define( 'WPTB_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPTB_OPTION_KEY', 'wptb_options' );

require_once WPTB_PATH . 'includes/class-wp-topbar.php';
require_once WPTB_PATH . 'includes/class-wp-topbar-settings.php';
require_once WPTB_PATH . 'includes/class-wp-topbar-frontend.php';
require_once WPTB_PATH . 'includes/class-wp-topbar-admin.php';

/**
 * Boot the plugin once all plugins are loaded.
 */
function wptb_run() {
	WP_Topbar::instance();
}
add_action( 'plugins_loaded', 'wptb_run' );

/**
 * Set sensible defaults on activation without overwriting existing settings.
 */
function wptb_activate() {
	if ( false === get_option( WPTB_OPTION_KEY ) ) {
		add_option( WPTB_OPTION_KEY, WP_Topbar_Settings::get_defaults() );
	}
}
register_activation_hook( __FILE__, 'wptb_activate' );
