/**
 * RascalsBox scripts
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Epron Toolkit
 * @version 1.0.0
 */

jQuery(document).ready(function($) {

	"use strict"; 

	/* Tabs
	------------------------------------------------------------------------*/
	$( 'body' ).on( 'click', '.rt-tab-nav', function(){

		var $tabs = $( this ).parents( '.rt-tabs-wrap' ),
			id = $( this ).attr( 'data-id' );

		$tabs.find( '.rt-tab-nav' ).removeClass( 'checked' );
		$( this ).addClass( 'checked' );
		$tabs.find( '.rt-tab' ).removeClass( 'checked' );
		$tabs.find( '.rt-tab[data-id=' + id + ']' ).addClass( 'checked' );


	} );


	/* Depedences
	------------------------------------------------------------------------*/

	function depedency_set() {
		$( '.box-row[data-depedency-el]' ).each( function(i) {
			var 
				$this = $( this ),
	            depedency_el = $this.attr( 'data-depedency-el' ),
	            depedency_val = $this.attr( 'data-depedency-val' ),
	            ctrl_val,
	            $ctrl_box,
	            $ctrl;

	        if ( $( '#' + depedency_el ).length ) {
	            // To array
	            depedency_val = depedency_val.split(',');
	            
	            // Controler
	            $ctrl_box = $( '#' + depedency_el ).parents( '.box-row' );

	            if ( $ctrl_box.hasClass( 'dependent-hidden' ) ) {
	            	$this.addClass( 'dependent-hidden' );
	            } else {
		            $ctrl = $( '#' + depedency_el );
		            ctrl_val = $ctrl.val();
		            if ( $.inArray( ctrl_val, depedency_val ) !== -1 ) {
		                $this.removeClass( 'dependent-hidden' );
		            } else {
		                $this.addClass( 'dependent-hidden' );
		            }
	        	}
	        }

		} );
	}

	function depedency_child(element,val){
		let el = $(element);
		let id = el.attr( 'data-id' );
		let child = $( '.box-row[data-id="'+id+'"]' );

		if ( el.length ) {
			let elVal = el.attr( 'data-depedency-val' );
			elVal = elVal.split(',');
			if ( $.inArray( val, elVal ) !== -1 ) {
				el.removeClass( 'dependent-hidden' );
			} else {
				el.addClass( 'dependent-hidden' );
			}	
		}

		// Set depedences
		depedency_set();

	}
	function depedency(id, val) {

        // Find depedency element
       	$( '.box-row[data-depedency-el="'+id+'"]' ).each( function() {
       		depedency_child(this,val);
        });
	}

	// init
	depedency_set();
	

	/* EXTERNAL PLUGINS
	------------------------------------------------------------------------*/


	/* Select Image
	------------------------------------------------------------------------*/
	$( '.select-image img' ).on( 'click', function(event) {
											
		/* Variables */											
		var 
			$box = $( this ).parents( '.box-row' ),
			images = $( 'ul', $box ),
			select_id = $( 'select', $box ).attr( 'id' ),
			id = $( this ).attr( 'data-image_id' );
			
		/* Remove class */
		$( 'img', images ).removeClass( 'selected-image' );
		
		/* Add class */
		$( this ).addClass( 'selected-image' );

		/* Select input option */
		$( 'select option[value="'+id+'"]', $box ).attr( 'selected', true );

		depedency( select_id, id );
			
		event.preventDefault();
	});


	/* Select
	------------------------------------------------------------------------*/
	$( '.box-select' ).each( function() {

        // Show groups
        var 
            $this = $( this ),
            $box = $this.parents( '.box-row' );

        $this.change( function() {		 
			var val = $( this ).val();
            if ( val == undefined ) return;
            depedency( $this.attr('id'), val );
        });

    });


	/* Multiselect
	------------------------------------------------------------------------*/
	$( '.multiselect' ).each( function () {

      var  
         $box = $( this ).parents( '.box-row' );

      if ( $( this ).hasClass( 'save-empty' ) ) {
         $( this ).change(function() {
            var name = $( this ).attr( 'name' );
            if ( ( $( this ).val() || [] ) == '' ) {
               $box.append( '<input type="hidden" name="' + name + '" value="" class="multiselect-empty">' );
            } else {
               $box.find( '.multiselect-empty' ).remove();
            }
         });
      }

   	});


	/* Switch
	------------------------------------------------------------------------*/
	$( '.switch-wrap' ).each( function() {

        // Show groups
        var 
            $this = $( this ),
            $box = $this.parents( '.box-row' ),
            select = $this.find( 'select' ),
            btn = $this.find( '.switch-on-off' ),
            on_val = btn.find( '.onstate' ).attr( 'data-on' ),
            off_val = btn.find( '.offstate' ).attr( 'data-off' );


        btn.on( 'click', function(e) {
            var $t = $( this ),
                v = '',
                depedency_el;

            if ( $t.hasClass( 'on' ) ) {
                v = select.find( 'option' ).eq(1).val();
                select.val( v );
                $t.removeClass( 'on' ).addClass( 'off' );
            } else {
                v = select.find( 'option' ).eq(0).val();
                select.val( v );
                $t.removeClass( 'off' ).addClass( 'on' );
            }
            var val = select.val( );

            if ( val == undefined ) return;

            depedency( select.attr('id'), val )

            e.preventDefault();

        });

    });
	

	/* Add Image
	------------------------------------------------------------------------*/
	(function() {
		var 
			custom_uploader,
			target_input,
			media_container,
			attachment;
 
 
	    $( document ).on( 'click', '.upload-image', function(e) {
	 
	        e.preventDefault();

	        // Media Container
			media_container = $( this ).parent().parent();

			// Target input
			target_input = media_container.find( 'input' );
	 
	        //If the uploader object has already been created, reopen the dialog
	        if ( custom_uploader ) {
	            custom_uploader.open();
	            return;
	        }
	 
	        //Extend the wp.media object
	        custom_uploader = wp.media.frames.file_frame = wp.media({
	            multiple: false,
	            library: { type: 'image' }
	        });
	 
	        // When a file is selected, grab the URL and set it as the text field's value
	        custom_uploader.on('select', function() {
	            attachment = custom_uploader.state().get( 'selection' ).first().toJSON();

	            var url = '';

	            if ( attachment.sizes == undefined ) {
	            	url = attachment.url;
	            }
				else if ( attachment.sizes.thumbnail == undefined ) {
					url = attachment.sizes.full.url;
				} else {
					url = attachment.sizes.thumbnail.url;
				}

	            // Preview
	            media_container.find( '.image-holder img' ).remove();
				media_container.find( '.image-holder' ).append( '<img src="' + url + '" alt="Image Preview">' );

				media_container.find( '.image-holder' ).addClass( 'is_image' );
				
				// Update ID
				target_input.val( attachment.id );
	        });

			custom_uploader.on( 'open', function() {
				var selection = custom_uploader.state().get( 'selection' ),
					id = target_input.val();

				if ( id !== '' ) {
					attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				}
			});

			//Open the uploader dialog
			custom_uploader.open();
	 
	    });

		// Remove image
		 $( document ).on( 'click', '.remove-image', function(e) {

		 	e.preventDefault();

	 		var mc = $( this ).parent().parent();
	 		mc.find( '.image-holder img' ).remove();
	 		mc.find( 'input' ).val('');
	        mc.find( '.image-holder' ).removeClass( 'is_image' );
	    });

		// Select source
		 $( document ).on( 'change', '.image-source-select', function(e) {

			var mc = $( this ).parent(),
				option = $( this ).find( 'option:selected' ).val();

			if ( option == 'media_libary' ) {
				mc.find( '.image-holder' ).removeClass('hidden');
				mc.find( 'input.image-input' ).attr( 'data-external_link', mc.find( 'input.image-input' ).val() );
				mc.find( 'input.image-input' ).val( mc.find( 'input.image-input' ).attr( 'data-media_id' ) );

				mc.find( 'input.image-input' ).attr('type', 'hidden');
			} else if ( option == 'external_link' ) {
				mc.find( 'input.image-input' ).attr( 'data-media_id', mc.find( 'input.image-input' ).val() );

				mc.find( 'input.image-input' ).val( mc.find( 'input.image-input' ).attr( 'data-external_link' ) );
				mc.find( '.image-holder' ).addClass('hidden');
				mc.find( 'input.image-input' ).attr('type', 'text');
			}
			
		});

		
	})();


	/* Datepicker
	------------------------------------------------------------------------*/
	$( '.datepicker-input' ).datepicker( {
		'dateFormat': 'yy-mm-dd',
		beforeShow: function(input, inst) {
		    inst.dpDiv.addClass( '_datepicker' );
		}
	});


	/* Color Picker
	------------------------------------------------------------------------*/
  
	$( '.colorpicker-input' ).each( function( i ) {
		var id = 'color_picker_' + i;
		$( this ).attr( 'id', id );
		$( '#' + id ).wpColorPicker();
	});
  

	/* Easy Link
	------------------------------------------------------------------------*/
	$('.easy-link').on( 'click', function( event ) {
	    $( this ).easyLink();
		event.preventDefault();
	});


	/* Iframe generator
	------------------------------------------------------------------------*/
	if ( $('.generate-iframe').length ) {
		$('.generate-iframe').IframeGenerator();
	}


	/* Background generator
	------------------------------------------------------------------------*/
	if ( $('.generate-bg').length ) {
		$('.generate-bg').BgGenerator();
	}
	

});