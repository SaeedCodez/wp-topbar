<?php
/**
 * Admin settings page view.
 *
 * @package WP_Topbar
 * @var array $options Current saved options.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wptb-wrap">

	<div class="wptb-header">
		<div class="wptb-header-title">
			<span class="wptb-logo dashicons dashicons-megaphone" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'Top Bar', 'wp-topbar' ); ?></h1>
				<p><?php esc_html_e( 'A fast, lightweight announcement bar for your site.', 'wp-topbar' ); ?></p>
			</div>
		</div>
		<span class="wptb-version"><?php echo esc_html( 'v' . WPTB_VERSION ); ?></span>
	</div>

	<div class="wptb-preview-wrap">
		<p class="wptb-preview-label"><?php esc_html_e( 'Live preview', 'wp-topbar' ); ?></p>
		<div class="wptb-preview-frame">
			<div id="wptb-bar" class="wptb-bar wptb-preview-bar">
				<div id="wptb-preview-content" class="wptb-inner">
					<div class="wptb-content">
						<img id="wptb-preview-image" class="wptb-image" src="" alt="" style="display:none;" />
						<span id="wptb-preview-text" class="wptb-text"></span>
						<a id="wptb-preview-button" class="wptb-button" href="#" onclick="return false;"></a>
					</div>
				</div>
				<a id="wptb-preview-full-image-link" class="wptb-full-image-link" href="#" onclick="return false;" style="display:none;">
					<img id="wptb-preview-full-image" class="wptb-full-image" src="" alt="" />
				</a>
				<span class="wptb-close" aria-hidden="true">
					<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
				</span>
			</div>
		</div>
	</div>

	<form action="options.php" method="post" class="wptb-form" id="wptb-settings-form">
		<?php settings_fields( 'wptb_settings_group' ); ?>

		<div class="wptb-grid">

			<section class="wptb-card">
				<header class="wptb-card-header">
					<h2><?php esc_html_e( 'Bar type', 'wp-topbar' ); ?></h2>
					<label class="wptb-switch">
						<input type="checkbox" name="wptb_options[enabled]" value="1" <?php checked( $options['enabled'] ); ?> />
						<span class="wptb-switch-slider"></span>
					</label>
				</header>
				<p class="wptb-card-desc"><?php esc_html_e( 'Turn the top bar on or off, and choose whether it shows text and a button, or a single full-width image.', 'wp-topbar' ); ?></p>

				<div class="wptb-mode-choice">
					<label class="wptb-mode-option">
						<input type="radio" name="wptb_options[bar_mode]" value="content" <?php checked( $options['bar_mode'], 'content' ); ?> />
						<span class="wptb-mode-option-title"><?php esc_html_e( 'Content bar', 'wp-topbar' ); ?></span>
						<span class="wptb-mode-option-desc"><?php esc_html_e( 'Text, a button and an optional small logo.', 'wp-topbar' ); ?></span>
					</label>
					<label class="wptb-mode-option">
						<input type="radio" name="wptb_options[bar_mode]" value="image" <?php checked( $options['bar_mode'], 'image' ); ?> />
						<span class="wptb-mode-option-title"><?php esc_html_e( 'Full image bar', 'wp-topbar' ); ?></span>
						<span class="wptb-mode-option-desc"><?php esc_html_e( 'A single image fills the entire bar.', 'wp-topbar' ); ?></span>
					</label>
				</div>
			</section>

			<div id="wptb-content-fields" class="wptb-mode-fields">

				<section class="wptb-card">
					<header class="wptb-card-header">
						<h2><?php esc_html_e( 'General', 'wp-topbar' ); ?></h2>
					</header>

					<div class="wptb-field">
						<label for="wptb-text"><?php esc_html_e( 'Message', 'wp-topbar' ); ?></label>
						<textarea id="wptb-text" name="wptb_options[text]" rows="3" class="wptb-input" data-preview="text"><?php echo esc_textarea( $options['text'] ); ?></textarea>
						<p class="wptb-hint"><?php esc_html_e( 'Basic HTML allowed: <a>, <strong>, <em>, <br>, <span>.', 'wp-topbar' ); ?></p>
					</div>
				</section>

				<section class="wptb-card">
					<header class="wptb-card-header">
						<h2><?php esc_html_e( 'Button', 'wp-topbar' ); ?></h2>
						<label class="wptb-switch">
							<input type="checkbox" name="wptb_options[button_enabled]" value="1" <?php checked( $options['button_enabled'] ); ?> />
							<span class="wptb-switch-slider"></span>
						</label>
					</header>
					<p class="wptb-card-desc"><?php esc_html_e( 'Show a call-to-action button next to the message.', 'wp-topbar' ); ?></p>

					<div class="wptb-field-row">
						<div class="wptb-field">
							<label for="wptb-button-text"><?php esc_html_e( 'Button text', 'wp-topbar' ); ?></label>
							<input type="text" id="wptb-button-text" class="wptb-input" data-preview="button-text" name="wptb_options[button_text]" value="<?php echo esc_attr( $options['button_text'] ); ?>" />
						</div>
						<div class="wptb-field">
							<label for="wptb-button-url"><?php esc_html_e( 'Button URL', 'wp-topbar' ); ?></label>
							<input type="url" id="wptb-button-url" class="wptb-input" name="wptb_options[button_url]" value="<?php echo esc_attr( $options['button_url'] ); ?>" placeholder="https://" />
						</div>
					</div>

					<label class="wptb-checkbox">
						<input type="checkbox" name="wptb_options[button_new_tab]" value="1" <?php checked( $options['button_new_tab'] ); ?> />
						<?php esc_html_e( 'Open link in a new tab', 'wp-topbar' ); ?>
					</label>
				</section>

				<section class="wptb-card">
					<header class="wptb-card-header">
						<h2><?php esc_html_e( 'Image', 'wp-topbar' ); ?></h2>
					</header>
					<p class="wptb-card-desc"><?php esc_html_e( 'Optional logo or icon shown before the message.', 'wp-topbar' ); ?></p>

					<div class="wptb-image-picker">
						<div class="wptb-image-preview" id="wptb-image-preview">
							<?php if ( $options['image_id'] ) : ?>
								<?php echo wp_get_attachment_image( $options['image_id'], 'thumbnail' ); ?>
							<?php else : ?>
								<span class="dashicons dashicons-format-image"></span>
							<?php endif; ?>
						</div>
						<div class="wptb-image-actions">
							<input type="hidden" id="wptb-image-id" name="wptb_options[image_id]" value="<?php echo esc_attr( $options['image_id'] ); ?>" />
							<button type="button" class="button" id="wptb-image-select"><?php esc_html_e( 'Choose image', 'wp-topbar' ); ?></button>
							<button type="button" class="button-link-delete" id="wptb-image-remove" <?php echo $options['image_id'] ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'wp-topbar' ); ?></button>
						</div>
					</div>

					<div class="wptb-field">
						<label for="wptb-image-link"><?php esc_html_e( 'Image link (optional)', 'wp-topbar' ); ?></label>
						<input type="url" id="wptb-image-link" class="wptb-input" name="wptb_options[image_link]" value="<?php echo esc_attr( $options['image_link'] ); ?>" placeholder="https://" />
					</div>
				</section>

				<section class="wptb-card">
					<header class="wptb-card-header">
						<h2><?php esc_html_e( 'Colors', 'wp-topbar' ); ?></h2>
					</header>

					<div class="wptb-field-row">
						<div class="wptb-field">
							<label for="wptb-bg-color"><?php esc_html_e( 'Background color', 'wp-topbar' ); ?></label>
							<input type="text" id="wptb-bg-color" class="wptb-color-field" data-preview="bg" name="wptb_options[bg_color]" value="<?php echo esc_attr( $options['bg_color'] ); ?>" />
						</div>
						<div class="wptb-field">
							<label for="wptb-text-color"><?php esc_html_e( 'Text color', 'wp-topbar' ); ?></label>
							<input type="text" id="wptb-text-color" class="wptb-color-field" data-preview="color" name="wptb_options[text_color]" value="<?php echo esc_attr( $options['text_color'] ); ?>" />
						</div>
					</div>

					<div class="wptb-field-row">
						<div class="wptb-field">
							<label for="wptb-button-bg-color"><?php esc_html_e( 'Button background', 'wp-topbar' ); ?></label>
							<input type="text" id="wptb-button-bg-color" class="wptb-color-field" data-preview="btn-bg" name="wptb_options[button_bg_color]" value="<?php echo esc_attr( $options['button_bg_color'] ); ?>" />
						</div>
						<div class="wptb-field">
							<label for="wptb-button-text-color"><?php esc_html_e( 'Button text color', 'wp-topbar' ); ?></label>
							<input type="text" id="wptb-button-text-color" class="wptb-color-field" data-preview="btn-color" name="wptb_options[button_text_color]" value="<?php echo esc_attr( $options['button_text_color'] ); ?>" />
						</div>
					</div>
				</section>

			</div>

			<div id="wptb-image-fields" class="wptb-mode-fields">

				<section class="wptb-card">
					<header class="wptb-card-header">
						<h2><?php esc_html_e( 'Full image', 'wp-topbar' ); ?></h2>
					</header>
					<p class="wptb-card-desc"><?php esc_html_e( 'This image fills the entire bar. It is cropped to the bar height and scaled to the full width of the page.', 'wp-topbar' ); ?></p>

					<div class="wptb-image-picker">
						<div class="wptb-image-preview wptb-image-preview-wide" id="wptb-full-image-preview">
							<?php if ( $options['full_image_id'] ) : ?>
								<?php echo wp_get_attachment_image( $options['full_image_id'], 'full' ); ?>
							<?php else : ?>
								<span class="dashicons dashicons-format-image"></span>
							<?php endif; ?>
						</div>
						<div class="wptb-image-actions">
							<input type="hidden" id="wptb-full-image-id" name="wptb_options[full_image_id]" value="<?php echo esc_attr( $options['full_image_id'] ); ?>" />
							<button type="button" class="button" id="wptb-full-image-select"><?php esc_html_e( 'Choose image', 'wp-topbar' ); ?></button>
							<button type="button" class="button-link-delete" id="wptb-full-image-remove" <?php echo $options['full_image_id'] ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'wp-topbar' ); ?></button>
						</div>
					</div>

					<div class="wptb-field-row">
						<div class="wptb-field">
							<label for="wptb-full-image-link"><?php esc_html_e( 'Link (optional)', 'wp-topbar' ); ?></label>
							<input type="url" id="wptb-full-image-link" class="wptb-input" name="wptb_options[full_image_link]" value="<?php echo esc_attr( $options['full_image_link'] ); ?>" placeholder="https://" />
						</div>
						<div class="wptb-field">
							<label for="wptb-full-image-alt"><?php esc_html_e( 'Alt text', 'wp-topbar' ); ?></label>
							<input type="text" id="wptb-full-image-alt" class="wptb-input" name="wptb_options[full_image_alt]" value="<?php echo esc_attr( $options['full_image_alt'] ); ?>" />
						</div>
					</div>

					<label class="wptb-checkbox">
						<input type="checkbox" name="wptb_options[full_image_new_tab]" value="1" <?php checked( $options['full_image_new_tab'] ); ?> />
						<?php esc_html_e( 'Open link in a new tab', 'wp-topbar' ); ?>
					</label>
				</section>

			</div>

			<section class="wptb-card">
				<header class="wptb-card-header">
					<h2><?php esc_html_e( 'Appearance', 'wp-topbar' ); ?></h2>
				</header>

				<div class="wptb-field-row">
					<div class="wptb-field">
						<label for="wptb-height"><?php esc_html_e( 'Height (px)', 'wp-topbar' ); ?></label>
						<div class="wptb-range-wrap">
							<input type="range" id="wptb-height-range" min="28" max="200" step="1" value="<?php echo esc_attr( $options['height'] ); ?>" />
							<input type="number" id="wptb-height" class="wptb-input wptb-input-number" data-preview="height" min="28" max="200" name="wptb_options[height]" value="<?php echo esc_attr( $options['height'] ); ?>" />
						</div>
					</div>
					<div class="wptb-field wptb-field-inline">
						<label class="wptb-switch">
							<input type="checkbox" name="wptb_options[sticky]" value="1" <?php checked( $options['sticky'] ); ?> />
							<span class="wptb-switch-slider"></span>
						</label>
						<div>
							<label><?php esc_html_e( 'Sticky mode', 'wp-topbar' ); ?></label>
							<p class="wptb-hint"><?php esc_html_e( 'Bar stays visible while visitors scroll.', 'wp-topbar' ); ?></p>
						</div>
					</div>
				</div>
			</section>

			<section class="wptb-card">
				<header class="wptb-card-header">
					<h2><?php esc_html_e( 'Close button', 'wp-topbar' ); ?></h2>
					<label class="wptb-switch">
						<input type="checkbox" name="wptb_options[show_close]" value="1" <?php checked( $options['show_close'] ); ?> />
						<span class="wptb-switch-slider"></span>
					</label>
				</header>
				<p class="wptb-card-desc"><?php esc_html_e( 'Let visitors dismiss the bar. Their choice is remembered per browser.', 'wp-topbar' ); ?></p>

				<div class="wptb-field">
					<label for="wptb-close-duration"><?php esc_html_e( 'Remember closed for', 'wp-topbar' ); ?></label>
					<select id="wptb-close-duration" class="wptb-input" name="wptb_options[close_duration]">
						<?php
						$duration_labels = array(
							'session'   => __( 'Current browser session', 'wp-topbar' ),
							'1'         => __( '1 day', 'wp-topbar' ),
							'7'         => __( '7 days', 'wp-topbar' ),
							'14'        => __( '14 days', 'wp-topbar' ),
							'30'        => __( '30 days', 'wp-topbar' ),
							'permanent' => __( 'Permanently (until reset)', 'wp-topbar' ),
						);
						foreach ( $duration_labels as $value => $label ) :
							?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $options['close_duration'], $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</section>

			<section class="wptb-card">
				<header class="wptb-card-header">
					<h2><?php esc_html_e( 'Display schedule', 'wp-topbar' ); ?></h2>
					<label class="wptb-switch">
						<input type="checkbox" id="wptb-schedule-enabled" name="wptb_options[schedule_enabled]" value="1" <?php checked( $options['schedule_enabled'] ); ?> />
						<span class="wptb-switch-slider"></span>
					</label>
				</header>
				<p class="wptb-card-desc"><?php esc_html_e( 'Only show the bar within a specific date and time range.', 'wp-topbar' ); ?></p>

				<div class="wptb-field-row wptb-schedule-fields" id="wptb-schedule-fields">
					<div class="wptb-field">
						<label for="wptb-start-date"><?php esc_html_e( 'Start', 'wp-topbar' ); ?></label>
						<input type="datetime-local" id="wptb-start-date" class="wptb-input" name="wptb_options[start_date]" value="<?php echo esc_attr( $options['start_date'] ); ?>" />
					</div>
					<div class="wptb-field">
						<label for="wptb-end-date"><?php esc_html_e( 'End', 'wp-topbar' ); ?></label>
						<input type="datetime-local" id="wptb-end-date" class="wptb-input" name="wptb_options[end_date]" value="<?php echo esc_attr( $options['end_date'] ); ?>" />
					</div>
				</div>
				<p class="wptb-hint"><?php esc_html_e( 'Times use your site’s configured timezone.', 'wp-topbar' ); ?></p>
			</section>

		</div>

		<div class="wptb-submit-bar">
			<?php submit_button( __( 'Save changes', 'wp-topbar' ), 'primary', 'submit', false ); ?>
		</div>
	</form>
</div>
