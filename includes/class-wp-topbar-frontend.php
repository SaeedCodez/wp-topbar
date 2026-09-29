<?php
/**
 * Renders the top bar on the public-facing site.
 *
 * @package WP_Topbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Topbar_Frontend {

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

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_body_open', array( $this, 'render' ) );
	}

	/**
	 * Whether the bar should currently be displayed.
	 *
	 * @return bool
	 */
	private function should_display() {
		if ( ! $this->settings->get( 'enabled' ) ) {
			return false;
		}

		if ( is_admin() ) {
			return false;
		}

		if ( ! $this->settings->get( 'schedule_enabled' ) ) {
			return true;
		}

		$now   = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$start = $this->settings->get( 'start_date' );
		$end   = $this->settings->get( 'end_date' );

		if ( $start ) {
			$start_ts = strtotime( $start );
			if ( $start_ts && $now < $start_ts ) {
				return false;
			}
		}

		if ( $end ) {
			$end_ts = strtotime( $end );
			if ( $end_ts && $now > $end_ts ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Enqueue the frontend stylesheet.
	 */
	public function enqueue_assets() {
		if ( ! $this->should_display() ) {
			return;
		}

		wp_enqueue_style( 'wp-topbar', WPTB_URL . 'assets/css/topbar.css', array(), WPTB_VERSION );
		wp_enqueue_script( 'wp-topbar', WPTB_URL . 'assets/js/topbar.js', array(), WPTB_VERSION, true );
	}

	/**
	 * Output the bar markup right after the opening <body> tag.
	 */
	public function render() {
		if ( ! $this->should_display() ) {
			return;
		}

		$bar_mode          = $this->settings->get( 'bar_mode' );
		$is_image_mode     = 'image' === $bar_mode;
		$text              = $this->settings->get( 'text' );
		$button_enabled    = $this->settings->get( 'button_enabled' ) && $this->settings->get( 'button_text' ) && $this->settings->get( 'button_url' );
		$image_id          = absint( $this->settings->get( 'image_id' ) );
		$full_image_id     = absint( $this->settings->get( 'full_image_id' ) );
		$show_close        = (bool) $this->settings->get( 'show_close' );
		$sticky            = (bool) $this->settings->get( 'sticky' );
		$height            = absint( $this->settings->get( 'height' ) );
		$close_duration    = $this->settings->get( 'close_duration' );

		if ( $is_image_mode ) {
			if ( ! $full_image_id ) {
				return;
			}
		} elseif ( ! $text && ! $button_enabled && ! $image_id ) {
			return;
		}

		$store_key = 'wptb_closed_' . substr( md5( wp_json_encode( $this->settings->get_options() ) ), 0, 10 );

		$style_vars = sprintf(
			'--wptb-height:%1$dpx;--wptb-bg:%2$s;--wptb-color:%3$s;--wptb-btn-bg:%4$s;--wptb-btn-color:%5$s;',
			$height,
			esc_attr( $this->settings->get( 'bg_color' ) ),
			esc_attr( $this->settings->get( 'text_color' ) ),
			esc_attr( $this->settings->get( 'button_bg_color' ) ),
			esc_attr( $this->settings->get( 'button_text_color' ) )
		);

		$classes = array( 'wptb-bar' );
		if ( $sticky ) {
			$classes[] = 'wptb-sticky';
		}
		if ( $is_image_mode ) {
			$classes[] = 'wptb-image-mode';
		}
		?>
		<div
			id="wptb-bar"
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			style="<?php echo esc_attr( $style_vars ); ?>"
			data-store-key="<?php echo esc_attr( $store_key ); ?>"
			data-close-duration="<?php echo esc_attr( $close_duration ); ?>"
			role="region"
			aria-label="<?php esc_attr_e( 'Site announcement', 'wp-topbar' ); ?>"
		>
			<?php if ( $is_image_mode ) : ?>
				<?php $this->render_full_image( $full_image_id ); ?>
				<?php if ( $show_close ) : ?>
					<button type="button" class="wptb-close" aria-label="<?php esc_attr_e( 'Close', 'wp-topbar' ); ?>">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
							<path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						</svg>
					</button>
				<?php endif; ?>
			<?php else : ?>
				<div class="wptb-inner">
					<div class="wptb-content">
						<?php $this->render_image( $image_id ); ?>
						<?php if ( $text ) : ?>
							<span class="wptb-text"><?php echo wp_kses_post( $text ); ?></span>
						<?php endif; ?>
						<?php if ( $button_enabled ) : ?>
							<a
								class="wptb-button"
								href="<?php echo esc_url( $this->settings->get( 'button_url' ) ); ?>"
								<?php echo $this->settings->get( 'button_new_tab' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
							>
								<?php echo esc_html( $this->settings->get( 'button_text' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
					<?php if ( $show_close ) : ?>
						<button type="button" class="wptb-close" aria-label="<?php esc_attr_e( 'Close', 'wp-topbar' ); ?>">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
								<path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
							</svg>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $show_close ) : ?>
		<script>
		(function(){
			var bar=document.getElementById('wptb-bar');
			if(!bar)return;
			var key=bar.getAttribute('data-store-key');
			var duration=bar.getAttribute('data-close-duration');
			var store=(duration==='session')?window.sessionStorage:window.localStorage;
			function isClosed(){
				try{
					var raw=store.getItem(key);
					if(!raw)return false;
					if(duration==='session'||duration==='permanent')return true;
					var expires=parseInt(raw,10);
					return !isNaN(expires)&&Date.now()<expires;
				}catch(e){return false;}
			}
			function setClosed(){
				try{
					var value='1';
					if(duration!=='session'&&duration!=='permanent'){
						var days=parseInt(duration,10)||0;
						value=String(Date.now()+days*86400000);
					}
					store.setItem(key,value);
				}catch(e){}
			}
			if(isClosed()){
				bar.style.display='none';
			}else{
				var btn=bar.querySelector('.wptb-close');
				if(btn){
					btn.addEventListener('click',function(){
						setClosed();
						bar.style.display='none';
						window.dispatchEvent(new Event('wptb:change'));
					});
				}
			}
		})();
		</script>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the optional image, wrapped in a link when provided.
	 *
	 * @param int $image_id Attachment ID.
	 */
	private function render_image( $image_id ) {
		if ( ! $image_id ) {
			return;
		}

		$image_html = wp_get_attachment_image(
			$image_id,
			'thumbnail',
			false,
			array(
				'class' => 'wptb-image',
			)
		);

		if ( ! $image_html ) {
			return;
		}

		$image_link = $this->settings->get( 'image_link' );

		if ( $image_link ) {
			printf(
				'<a class="wptb-image-link" href="%s">%s</a>',
				esc_url( $image_link ),
				$image_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		} else {
			echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Render a full-bleed image that fills the entire bar (image-based mode).
	 *
	 * @param int $image_id Attachment ID.
	 */
	private function render_full_image( $image_id ) {
		$alt = $this->settings->get( 'full_image_alt' );

		// Build the <img> manually (instead of wp_get_attachment_image()) so no
		// srcset/sizes attributes are added: those let the browser pick one of
		// WordPress' smaller auto-generated sizes on narrower viewports, when
		// the original, full-size image should always be shown on every device.
		$src = wp_get_attachment_image_url( $image_id, 'full' );

		if ( ! $src ) {
			return;
		}

		$image_html = sprintf(
			'<img src="%1$s" class="wptb-full-image" alt="%2$s" />',
			esc_url( $src ),
			esc_attr( $alt ? $alt : '' )
		);

		$link = $this->settings->get( 'full_image_link' );

		if ( $link ) {
			printf(
				'<a class="wptb-full-image-link" href="%1$s"%2$s aria-label="%3$s">%4$s</a>',
				esc_url( $link ),
				$this->settings->get( 'full_image_new_tab' ) ? ' target="_blank" rel="noopener noreferrer"' : '',
				esc_attr( $alt ? $alt : __( 'Site announcement', 'wp-topbar' ) ),
				$image_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		} else {
			echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
