/**
 * Background Generator Plugin
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Epron Toolkit
 * @version 1.0.0
 */

;(function($) {

	jQuery.fn.BgGenerator = function( options ) {
			
		return this.each(function(i) {		  
			var opts = $.extend({
				'null' : 0
			}, options);
			   
			/* List variables */
			var 
				container  = $( this ).parents( '.box-row' ),
				edit_box   = $( '#bg-editor-box' ),
				target     = $( '.bg-holder', container ),
				edit_label = container.find( '.bg-holder-wrap' ).attr( 'data-edit-label' ),
				add_label  = container.find( '.bg-holder-wrap' ).attr( 'data-add-label' );

			/* Open dialog box */
			$( '.generate-bg', container ).on( 'click', function(event) {
				_edit_bg_box();
			    event.preventDefault();						 
			});
			$(  '.image-holder', container ).on( 'click', function(event) {
				_edit_bg_box();
			    event.preventDefault();						 
			});

			// Delete bg
			$( '.delete-bg', container ).on( 'click', function(event) {

				container.find( '.image-holder img' ).remove();
				container.find( '.image-holder' ).hide();

				target.text('');
				container.find( '.generate-bg' ).html( '<i class="fa icon fa-magic"></i>' + add_label );
				container.find( '.image-holder .preview-color-holder' ).remove();
				$( '.msg', container ).hide();
				$( this ).hide();

			    event.preventDefault();						 
			});
			

			/* Ajax functions
	        ------------------------------------------------------------------------*/

			/* --- Get BG Editor --- */
			function _bg_editor() {


				var saved_data = target.val();

				var data = {
					data : saved_data,
					action: 'bg_editor'
				};

				$( '#bg-editor-loader' ).show();
				
				$.ajax({
					url: ajaxurl,
					data: data,
					type: 'POST',
					success: function( response ) {
						$( '#bg-editor-content', edit_box ).append( response );
						$( '#bg-editor-content', edit_box ).find( '#color_picker' ).wpColorPicker();
						$( '#bg-editor-content', edit_box ).fadeIn( 600 );
						$( '#bg-editor-loader' ).hide();
					}
				});
			}

			/* --- Save BG --- */
			function _bg_editor_save() {

				var item_fields = {};

				var inputs = $('#bg-editor-content input, #bg-editor-content textarea, #bg-editor-content select').not(':input[type=button], :input[type=submit], :input[type=reset]');
				$( inputs, edit_box ).each( function( i ){
					var name = $( this ).attr( 'name' );
					if ( $( this ).val() !== '' )
				        item_fields[name] = $( this ).val();
				});
				var json = JSON.stringify( item_fields );
				target.text( json );

				// Update preview

				// Image
				container.find( '.image-holder img' ).remove();
				if ( $( '#bg-editor-content .image-holder img' ).length ) {
					var new_img = $( '#bg-editor-content .image-holder img' ).attr( 'src' );
					container.find( '.image-holder' ).append( '<img src="' + new_img + '">' );
				}

				// Color
				var color_val = $( '#bg-editor-content #color_picker' ).val();
				container.find( '.image-holder .preview-color-holder' ).remove();
				if ( color_val !== '' ) {
					container.find( '.image-holder' ).append( '<div class="preview-color-holder" style="background-color:' + color_val + '"></div>' );
				}

				if ( target.text() !== '' ) {
					container.find( '.generate-bg' ).html( '<i class="fa icon fa-magic"></i>' + edit_label );
					$( '.delete-bg', container ).show();
					$( '.msg', container ).hide();
					container.find( '.image-holder' ).show();
				}

			}
			

			/* --- Check JSON OBJ --- */
			function IsJsonString(str) {
			    try {
			        JSON.parse(str);
			    } catch (e) {
			        return false;
			    }
			    	return true;
			}
			
			/* Edit media BOX */
			function _edit_bg_box() {
				$( '#bg-editor-box' ).dialog( {
					title: 'Background',
					modal: false,
					width: 600,
					height: 'auto',
					dialogClass: 'ui-custom ui-custom-dialog',
					buttons: [
						{
							text: 'Save Background',
							'class': 'ui-button-update-item',
							click: function() {
								_bg_editor_save();
								
							}
						},
						{
							text: 'Close',
							'class': 'ui-button-cancel',
							click: function() {
								$( this ).dialog( 'close' );
							}
						}
					],
					open: function( event, ui ) {

						/* Buttons icons */
						$(event.target).parent().find( '.ui-button-cancel span' ).prepend( '<i class="fa icon fa-times"></i>' );
						$(event.target).parent().find( '.ui-button-update-item span' ).prepend( '<i class="fa icon fa-refresh"></i>' );

						/* Add helper class to overlay layer */
						$( '.ui-widget-overlay' ).addClass( 'ui-custom-overlay' );

						/* Resizable */
						/* Mobile Resizable */
						var init_width = $( window ).width(),
							init_height = $( window ).height();

						if ( init_width <= 768 ) {
							$( event.target ).parent().css( 'max-width', '90%' );
							$( event.target ).dialog( 'option', 'position', 'center' );
							$( event.target ).dialog( 'option', 'height', 'auto' );
						} else {
							$( event.target ).dialog( 'option', 'height', init_height-100 );
						}
						$( '.ui-widget-overlay' ).css( 'height', init_height );
						
						$( window ).resize( function() {

							var windowWidth = $( window ).width();

							if ( windowWidth <= 768 ) {
								$( event.target ).parent().css( 'max-width', '90%' );
							} else {
								$( event.target ).parent().css( 'max-width', '600px' );
								$( '.ui-widget-overlay' ).css( 'height', init_height );
							}
	    					$( event.target ).dialog( 'option', 'position', 'center' );
						});
						/* --- */

						/* Add loader to */
						$( event.target ).parent().find( '.ui-dialog-buttonpane' ).append( $( '#bg-editor-loader' ) );
						
						_bg_editor();
					},
					close: function() {
					   $( '#bg-editor-loader' ).appendTo(edit_box);
					   $( '#bg-editor-content', edit_box).children().remove();
					   $( '#bg-editor-content', edit_box).hide();
					  
					}
				});
			}
				
		});
	}

})(jQuery);