<?php
/**
 * Admin settings page.
 *
 * @package WP_Topbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Topbar_Admin {

	const PAGE_SLUG = 'wp-topbar';

	/**
	 * Settings handler.
	 *
	 * @var WP_Topbar_Settings
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param WP_Topbar_Settings $settings Settings handler.
	 */
	public function __construct( WP_Topbar_Settings $settings ) {
		$this->settings = $settings;

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . WPTB_BASENAME, array( $this, 'add_settings_link' ) );
	}

	/**
	 * Register the top-level admin menu page.
	 */
	public function add_menu() {
		add_menu_page(
			__( 'Top Bar', 'wp-topbar' ),
			__( 'Top Bar', 'wp-topbar' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' ),
			'dashicons-megaphone',
			81
		);
	}

	/**
	 * Add a "Settings" link on the plugins list screen.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_settings_link( $links ) {
		$url  = admin_url( 'admin.php?page=' . self::PAGE_SLUG );
		$link = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Settings', 'wp-topbar' ) );
		array_unshift( $links, $link );

		return $links;
	}

	/**
	 * Register the setting, args and sanitize callback.
	 */
	public function register_setting() {
		register_setting(
			'wptb_settings_group',
			WPTB_OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this->settings, 'sanitize' ),
				'default'           => WP_Topbar_Settings::get_defaults(),
			)
		);
	}

	/**
	 * Enqueue admin assets, scoped to our settings screen only.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'wp-topbar-admin', WPTB_URL . 'assets/css/admin.css', array( 'wp-color-picker' ), WPTB_VERSION );
		wp_enqueue_style( 'wp-topbar', WPTB_URL . 'assets/css/topbar.css', array(), WPTB_VERSION );

		wp_enqueue_script( 'wp-topbar-admin', WPTB_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), WPTB_VERSION, true );

		wp_localize_script(
			'wp-topbar-admin',
			'wptbAdmin',
			array(
				'chooseImageTitle' => __( 'Choose an image', 'wp-topbar' ),
				'useImageText'     => __( 'Use this image', 'wp-topbar' ),
			)
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = $this->settings->get_options();

		include WPTB_PATH . 'includes/views/settings-page.php';
	}
}
