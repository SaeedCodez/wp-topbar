<?php
/**
 * Core bootstrap class.
 *
 * @package WP_Topbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class WP_Topbar {

	/**
	 * Singleton instance.
	 *
	 * @var WP_Topbar|null
	 */
	private static $instance = null;

	/**
	 * Settings handler.
	 *
	 * @var WP_Topbar_Settings
	 */
	public $settings;

	/**
	 * Frontend renderer.
	 *
	 * @var WP_Topbar_Frontend
	 */
	public $frontend;

	/**
	 * Admin settings page.
	 *
	 * @var WP_Topbar_Admin|null
	 */
	public $admin;

	/**
	 * Get the singleton instance.
	 *
	 * @return WP_Topbar
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		$this->settings = new WP_Topbar_Settings();
		$this->frontend = new WP_Topbar_Frontend( $this->settings );

		if ( is_admin() ) {
			$this->admin = new WP_Topbar_Admin( $this->settings );
		}
	}

	/**
	 * Load the plugin translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-topbar', false, dirname( WPTB_BASENAME ) . '/languages' );
	}
}
