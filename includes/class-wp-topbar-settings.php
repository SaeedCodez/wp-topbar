<?php
/**
 * Settings storage: defaults, retrieval and sanitization.
 *
 * @package WP_Topbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Topbar_Settings {

	/**
	 * Allowed values for the "remember closed for" field.
	 *
	 * @var string[]
	 */
	public static $close_durations = array( 'session', '1', '7', '14', '30', 'permanent' );

	/**
	 * Allowed values for the bar content mode.
	 *
	 * @var string[]
	 */
	public static $bar_modes = array( 'content', 'image' );

	/**
	 * Cached options.
	 *
	 * @var array|null
	 */
	private $options = null;

	/**
	 * Default option values.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'enabled'            => false,
			'bar_mode'           => 'content',
			'text'               => __( 'We use cookies to improve your experience. 🎉 Check out our latest update!', 'wp-topbar' ),
			'button_enabled'     => true,
			'button_text'        => __( 'Learn more', 'wp-topbar' ),
			'button_url'         => home_url( '/' ),
			'button_new_tab'     => false,
			'image_id'           => 0,
			'image_link'         => '',
			'full_image_id'      => 0,
			'full_image_alt'     => '',
			'full_image_link'    => '',
			'full_image_new_tab' => false,
			'bg_color'           => '#0a0a0a',
			'text_color'         => '#ffffff',
			'button_bg_color'    => '#ffffff',
			'button_text_color'  => '#0a0a0a',
			'sticky'             => true,
			'height'             => 44,
			'show_close'         => true,
			'close_duration'     => '7',
			'schedule_enabled'   => false,
			'start_date'         => '',
			'end_date'           => '',
		);
	}

	/**
	 * Get all saved options merged with defaults.
	 *
	 * @return array
	 */
	public function get_options() {
		if ( null === $this->options ) {
			$saved         = get_option( WPTB_OPTION_KEY, array() );
			$this->options = wp_parse_args( is_array( $saved ) ? $saved : array(), self::get_defaults() );
		}

		return $this->options;
	}

	/**
	 * Get a single option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Fallback value.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$options = $this->get_options();

		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Sanitize the settings array before saving.
	 *
	 * @param array $input Raw input from the settings form.
	 * @return array
	 */
	public function sanitize( $input ) {
		$input   = is_array( $input ) ? $input : array();
		$current = $this->get_options();
		$output  = array();

		$output['enabled']  = ! empty( $input['enabled'] );
		$output['bar_mode'] = ( isset( $input['bar_mode'] ) && in_array( $input['bar_mode'], self::$bar_modes, true ) )
			? $input['bar_mode']
			: $current['bar_mode'];
		$output['text']     = isset( $input['text'] ) ? wp_kses(
			wp_unslash( $input['text'] ),
			array(
				'a'      => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
				'strong' => array(),
				'em'     => array(),
				'br'     => array(),
				'span'   => array( 'style' => true ),
			)
		) : '';

		$output['button_enabled'] = ! empty( $input['button_enabled'] );
		$output['button_text']    = isset( $input['button_text'] ) ? sanitize_text_field( wp_unslash( $input['button_text'] ) ) : '';
		$output['button_url']     = isset( $input['button_url'] ) ? esc_url_raw( trim( wp_unslash( $input['button_url'] ) ) ) : '';
		$output['button_new_tab'] = ! empty( $input['button_new_tab'] );

		$output['image_id']   = isset( $input['image_id'] ) ? absint( $input['image_id'] ) : 0;
		$output['image_link'] = isset( $input['image_link'] ) ? esc_url_raw( trim( wp_unslash( $input['image_link'] ) ) ) : '';

		$output['full_image_id']      = isset( $input['full_image_id'] ) ? absint( $input['full_image_id'] ) : 0;
		$output['full_image_alt']     = isset( $input['full_image_alt'] ) ? sanitize_text_field( wp_unslash( $input['full_image_alt'] ) ) : '';
		$output['full_image_link']    = isset( $input['full_image_link'] ) ? esc_url_raw( trim( wp_unslash( $input['full_image_link'] ) ) ) : '';
		$output['full_image_new_tab'] = ! empty( $input['full_image_new_tab'] );

		foreach ( array( 'bg_color', 'text_color', 'button_bg_color', 'button_text_color' ) as $color_key ) {
			$color               = isset( $input[ $color_key ] ) ? sanitize_hex_color( wp_unslash( $input[ $color_key ] ) ) : '';
			$output[ $color_key ] = $color ? $color : $current[ $color_key ];
		}

		$output['sticky'] = ! empty( $input['sticky'] );

		$height           = isset( $input['height'] ) ? absint( $input['height'] ) : $current['height'];
		$output['height'] = min( 200, max( 28, $height ) );

		$output['show_close']     = ! empty( $input['show_close'] );
		$output['close_duration'] = ( isset( $input['close_duration'] ) && in_array( $input['close_duration'], self::$close_durations, true ) )
			? $input['close_duration']
			: $current['close_duration'];

		$output['schedule_enabled'] = ! empty( $input['schedule_enabled'] );
		$output['start_date']       = isset( $input['start_date'] ) ? $this->sanitize_datetime( $input['start_date'] ) : '';
		$output['end_date']         = isset( $input['end_date'] ) ? $this->sanitize_datetime( $input['end_date'] ) : '';

		return $output;
	}

	/**
	 * Validate a datetime-local input value (Y-m-d\TH:i).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private function sanitize_datetime( $value ) {
		$value = sanitize_text_field( wp_unslash( $value ) );

		if ( '' === $value ) {
			return '';
		}

		$date = DateTime::createFromFormat( 'Y-m-d\TH:i', $value );

		return ( $date && $date->format( 'Y-m-d\TH:i' ) === $value ) ? $value : '';
	}
}
