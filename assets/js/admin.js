/* global jQuery, wp */
( function ( $ ) {
	'use strict';

	$( function () {
		var $form = $( '#wptb-settings-form' );

		var preview = {
			bar: document.getElementById( 'wptb-bar' ),
			image: document.getElementById( 'wptb-preview-image' ),
			text: document.getElementById( 'wptb-preview-text' ),
			button: document.getElementById( 'wptb-preview-button' ),
		};

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
				preview.button.style.display = buttonEnabled && buttonText ? 'inline-flex' : 'none';
			}
		}

		function updateImagePreviewNode( url ) {
			if ( ! preview.image ) {
				return;
			}

			if ( url ) {
				preview.image.src = url;
				preview.image.style.display = 'inline-block';
			} else {
				preview.image.style.display = 'none';
				preview.image.removeAttribute( 'src' );
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

		// Schedule toggle visibility.
		function toggleSchedule() {
			var enabled = $( '#wptb-schedule-enabled' ).is( ':checked' );
			$( '#wptb-schedule-fields' ).toggleClass( 'is-disabled', ! enabled );
		}
		$( '#wptb-schedule-enabled' ).on( 'change', toggleSchedule );
		toggleSchedule();

		// Media uploader for the image field.
		var mediaFrame;
		$( '#wptb-image-select' ).on( 'click', function ( e ) {
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
				var url = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.url;

				$( '#wptb-image-id' ).val( attachment.id );
				$( '#wptb-image-preview' ).html( '<img src="' + url + '" alt="" />' );
				$( '#wptb-image-remove' ).show();
				updateImagePreviewNode( url );
			} );

			mediaFrame.open();
		} );

		$( '#wptb-image-remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '#wptb-image-id' ).val( '' );
			$( '#wptb-image-preview' ).html( '<span class="dashicons dashicons-format-image"></span>' );
			$( this ).hide();
			updateImagePreviewNode( '' );
		} );

		var initialImage = $( '#wptb-image-preview img' ).attr( 'src' );
		updateImagePreviewNode( initialImage || '' );
		updatePreview();
	} );
} )( jQuery );
