/* global jQuery, wp */
( function ( $ ) {
	'use strict';

	$( function () {
		var $form = $( '#wptb-settings-form' );

		var preview = {
			bar: document.getElementById( 'wptb-bar' ),
			content: document.getElementById( 'wptb-preview-content' ),
			image: document.getElementById( 'wptb-preview-image' ),
			text: document.getElementById( 'wptb-preview-text' ),
			button: document.getElementById( 'wptb-preview-button' ),
			fullImageLink: document.getElementById( 'wptb-preview-full-image-link' ),
			fullImage: document.getElementById( 'wptb-preview-full-image' ),
		};

		function currentMode() {
			return $( 'input[name="wptb_options[bar_mode]"]:checked' ).val() || 'content';
		}

		// topbar.css sets `display` with !important on the button and logo,
		// which beats a plain inline style, so toggle with the same priority.
		function setDisplay( el, value ) {
			el.style.setProperty( 'display', value, 'important' );
		}

		function updatePreview() {
			if ( ! preview.bar ) {
				return;
			}

			var height = parseInt( $( '#wptb-height' ).val(), 10 ) || 44;
			preview.bar.style.setProperty( '--wptb-height', height + 'px' );
			preview.bar.style.setProperty( '--wptb-bg', $( '#wptb-bg-color' ).val() || '#0a0a0a' );
			preview.bar.style.setProperty( '--wptb-color', $( '#wptb-text-color' ).val() || '#ffffff' );
			preview.bar.style.setProperty( '--wptb-btn-bg', $( '#wptb-button-bg-color' ).val() || '#ffffff' );
			preview.bar.style.setProperty( '--wptb-btn-color', $( '#wptb-button-text-color' ).val() || '#0a0a0a' );

			if ( preview.text ) {
				preview.text.innerHTML = $( '#wptb-text' ).val();
			}

			if ( preview.button ) {
				var buttonEnabled = $( 'input[name="wptb_options[button_enabled]"]' ).is( ':checked' );
				var buttonText = $( '#wptb-button-text' ).val();
				preview.button.textContent = buttonText;
				setDisplay( preview.button, buttonEnabled && buttonText ? 'inline-flex' : 'none' );
			}
		}

		function updateImagePreviewNode( url ) {
			if ( ! preview.image ) {
				return;
			}

			if ( url ) {
				preview.image.src = url;
				setDisplay( preview.image, 'inline-block' );
			} else {
				setDisplay( preview.image, 'none' );
				preview.image.removeAttribute( 'src' );
			}
		}

		function updateFullImagePreviewNode( url ) {
			if ( ! preview.fullImage ) {
				return;
			}

			if ( url ) {
				preview.fullImage.src = url;
			} else {
				preview.fullImage.removeAttribute( 'src' );
			}
		}

		function toggleMode() {
			var mode = currentMode();
			var isImageMode = 'image' === mode;

			$( '#wptb-content-fields' ).toggleClass( 'wptb-mode-hidden', isImageMode );
			$( '#wptb-image-fields' ).toggleClass( 'wptb-mode-hidden', ! isImageMode );

			if ( preview.bar ) {
				preview.bar.classList.toggle( 'wptb-image-mode', isImageMode );
			}
			if ( preview.content ) {
				preview.content.style.display = isImageMode ? 'none' : '';
			}
			if ( preview.fullImageLink ) {
				preview.fullImageLink.style.display = isImageMode ? 'block' : 'none';
			}
		}

		// Color pickers.
		$( '.wptb-color-field' ).wpColorPicker( {
			change: function () {
				// wp.color picker fires change before the input value updates; defer a tick.
				setTimeout( updatePreview, 10 );
			},
			clear: updatePreview,
		} );

		// Live-updating fields.
		$form.on( 'input change', '[data-preview], #wptb-text, #wptb-button-text, input[name="wptb_options[button_enabled]"]', updatePreview );

		// Height range <-> number sync.
		$( '#wptb-height-range' ).on( 'input', function () {
			$( '#wptb-height' ).val( this.value );
			updatePreview();
		} );
		$( '#wptb-height' ).on( 'input', function () {
			$( '#wptb-height-range' ).val( this.value );
			updatePreview();
		} );

		// Bar mode (content vs. full image) toggle.
		$( 'input[name="wptb_options[bar_mode]"]' ).on( 'change', toggleMode );
		toggleMode();

		// Schedule toggle visibility.
		function toggleSchedule() {
			var enabled = $( '#wptb-schedule-enabled' ).is( ':checked' );
			$( '#wptb-schedule-fields' ).toggleClass( 'is-disabled', ! enabled );
		}
		$( '#wptb-schedule-enabled' ).on( 'change', toggleSchedule );
		toggleSchedule();

		/**
		 * Wire up a media-library picker for an image field.
		 *
		 * @param {Object} opts Configuration for the picker instance.
		 */
		function initImagePicker( opts ) {
			var mediaFrame;

			$( opts.selectButton ).on( 'click', function ( e ) {
				e.preventDefault();

				if ( mediaFrame ) {
					mediaFrame.open();
					return;
				}

				mediaFrame = wp.media( {
					title: wptbAdmin.chooseImageTitle,
					button: { text: wptbAdmin.useImageText },
					multiple: false,
					library: { type: 'image' },
				} );

				mediaFrame.on( 'select', function () {
					var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
					var thumbSize = ( attachment.sizes && attachment.sizes[ opts.previewSize ] ) ? attachment.sizes[ opts.previewSize ] : null;
					var previewUrl = thumbSize ? thumbSize.url : attachment.url;

					$( opts.idField ).val( attachment.id );
					$( opts.previewBox ).html( '<img src="' + previewUrl + '" alt="" />' );
					$( opts.removeButton ).show();
					opts.onSelect( attachment.url, previewUrl );
				} );

				mediaFrame.open();
			} );

			$( opts.removeButton ).on( 'click', function ( e ) {
				e.preventDefault();
				$( opts.idField ).val( '' );
				$( opts.previewBox ).html( '<span class="dashicons dashicons-format-image"></span>' );
				$( this ).hide();
				opts.onSelect( '', '' );
			} );
		}

		initImagePicker( {
			selectButton: '#wptb-image-select',
			removeButton: '#wptb-image-remove',
			idField: '#wptb-image-id',
			previewBox: '#wptb-image-preview',
			previewSize: 'thumbnail',
			onSelect: function ( url, previewUrl ) {
				updateImagePreviewNode( previewUrl );
			},
		} );

		initImagePicker( {
			selectButton: '#wptb-full-image-select',
			removeButton: '#wptb-full-image-remove',
			idField: '#wptb-full-image-id',
			previewBox: '#wptb-full-image-preview',
			previewSize: 'full',
			onSelect: function ( url ) {
				updateFullImagePreviewNode( url );
			},
		} );

		var initialImage = $( '#wptb-image-preview img' ).attr( 'src' );
		updateImagePreviewNode( initialImage || '' );

		var initialFullImage = $( '#wptb-full-image-preview img' ).attr( 'src' );
		updateFullImagePreviewNode( initialFullImage || '' );

		updatePreview();
	} );
} )( jQuery );
