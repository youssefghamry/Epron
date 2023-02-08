/**
 * Rascals Panel scripts
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Rascals Panel
 * @version 1.0.0
 */
jQuery(document).ready(function($) {

	"use strict"; 
	
	/* UI
	------------------------------------------------------------------------*/

	// GENERIC JQUERY UI SETUP
	$( '._button' ).button();


	/* Navigation
	 ------------------------------------------------------------------------*/

	// Store variables
    var 
    	menu_head = $( '.rascals-panel-menu > li > a' ),
        menu_body = $( '.rascals-panel-menu li > .rascals-panel-sub-menu' );

    // Create breadcrumbs
    $( '.rascals-panel-menu li a' ).each( function(i) {
    	var
    		id = $( this ).data( 'tab_id' ),
    		sub = $( this ).next(),
    		ul = null;

    	if ( sub.length > 0 ) {

    		ul = $( this ).parent().find( 'ul' ).clone();
    		ul.find( 'a' ).contents().unwrap().attr( 'id', '' );
    		ul = '<ul>' + ul.html() + '</ul>';
    		$( '#' + id ).find( '.rascals-panel-breadcrumb div:last-child' ).append( ul );
    	}

    });

    //  Breadcrumb animation
    function breadcrumb( el, eq ) {

    	var
    		offset = - ( eq * 20 );

    	el.animate( {
    		top: offset
    	});
    }

    // Open the first tab on load
	menu_head.first()
		.addClass( 'active' )
		.next()
		.slideDown( 'normal' )
		.find( 'a:first' )
		.addClass( 'active' );

	/* Display first tab */
	$( '.rascals-panel-tab:first' ).css( 'display', 'block' );
	if ( $( '.rascals-panel-tab:first' ).find( '.rascals-panel-tab' ).length > 0 ) {
		$( '.rascals-panel-tab:first .rascals-panel-tab:first' ).css( 'display', 'block' );
	}

	// Click function
    menu_head.on( 'click', function( event ) {
			
		var 
			sub = $( this ).next(),
			id = $( this ).data( 'tab_id' ),
			tab = $( '#' + id );


        // Disable header links
        event.preventDefault();

        // Show and hide the tabs on click
        if ( !$(this).hasClass( 'active' ) ) {

        	// Hide all tabs
			$( '.rascals-panel-tab' ).css( 'display', 'none' );

			// Show main tab
			tab.fadeIn( 500 );
            menu_body.slideUp( 'normal' );
            $( this ).next().stop( true, true ).slideToggle( 'normal' );
            menu_head.removeClass( 'active' );
            menu_body.find( 'a' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            $( this ).next().find( 'li:first a' ).addClass( 'active' );
            $( '.rascals-panel-tab:visible .rascals-panel-tab:first' ).css( 'display', 'block' );

            // Breadcrumbs
            breadcrumb( $( '.rascals-panel-breadcrumb ul', tab ), 0 );
        }

    });

    // Click function
    menu_body.find( 'li > a' ).on( 'click', function( event ) {

			var
				id = $( this ).data( 'tab_id' ),
				tab = $( '#' + id ),
				main_tab = null,
				eq = null;

			if ( !$( this ).hasClass( 'active' ) ) {
				menu_body.find( 'a' ).removeClass( 'active' );
				$( this ).addClass( 'active' );
				$( '.rascals-panel-tab:visible .rascals-panel-tab' ).css( 'display', 'none' );
				main_tab = $( '.rascals-panel-tab:visible > .rascals-panel-breadcrumb ul' );
				tab.fadeIn( 500 );

				// Breadcrumbs
				eq = $( this ).parent().index();
				breadcrumb( main_tab, eq );
				
			}
        event.preventDefault();
    });

    /* Respnsive menu */
    $( '#show-res-nav' ).on( 'click', function( event ){
    	if ( $( '#rascals-panel-sidebar' ).hasClass( 'mobile-nav' ) ) {
			$( '#rascals-panel-sidebar' ).removeClass( 'mobile-nav' );
    	} else {
    		$( '#rascals-panel-sidebar' ).addClass( 'mobile-nav' );
    	}
    	event.preventDefault();
    });


	/* Save Settings
 	------------------------------------------------------------------------*/

	$( '#_save, #_save_mobile' ).on( 'click', function() {
		 
		/* Update editor content */
        $( '.custom-tiny-editor' ).each( function() {
											   
			/* Only for visual editor */
			if ( $( this ).children().hasClass( 'tmce-active' ) ) {
				var editor_id = $( this ).data( 'id' );
				editor_id = '#' + editor_id;
				var editor_content = $( editor_id + '_ifr', this ).contents().find( 'body' ).html();
				if ( editor_content ) {
				   if ( editor_content == '' || editor_content == '<p><br></p>' || editor_content == '<p><br data-mce-bogus="1"></p>' ) {
				   		editor_content = '';
				   	}
				   $( 'textarea' + editor_id, this ).val( editor_content );
				}
			}
		});

		var 
			data = {
				action: 'panel_save',
				data: $( '#rascals-panel_form' ).find(':input:not(.no-save)').serializePost()
	    	};

        $.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			success: function( response ) {
				
				if ( response == 'import_error' ) {

					/* Show notice */
					$( '#rascals-panel-notices' ).notify( 'create' , {
						title: 'Error!',
						text: 'Import error.'
					});

					return false;
				}

				/* Parse JSON object */
				response = JSON.parse( response );
				var is_import = false;
				
				/* If import */
				if ( $( '#data-import-wrap :input' ).length > 0 && $( '#data-import-wrap :input' ).val() != '' ) is_import = true;
				

				$( '#rascals-panel_form :input' ).each( function ( i ) {
															 
					var input_name = $( this ).attr( 'name' );
					var input_val = $( this ).val();
					var response_val = response[ input_name ];
					
					/* Inputs */
					if ( response_val != undefined & response_val != input_val ) {
						$( this ).val( response[input_name] );
					}
				
				});

				/* Default colorpicker value */
  				$( '.colorpicker-input' ).each( function() {
				    if ( $( this ).val() != '' ) {
						var hex = $( this ).val();
						$( this ).next().css( 'background-color', hex );
					}
				});
				
				/* TinyMCE update content */
				$( '.custom-tiny-editor' ).each( function() {
					var editor_id = $( this ).data( 'id' );
					editor_id = '#' + editor_id;
					var saved_content = $( 'textarea' + editor_id, this ).val();
					$( editor_id + '_ifr', this ).contents().find( 'body' ).html( saved_content );
				});
				
				/* If import */
				if ( is_import ) {

					/* Show notice */
					$( '#rascals-panel-notices' ).notify( 'create' , {
						title: 'Success!',
						text: 'New settings are imported.'
					});

					setTimeout(function(){
						location.reload();
					}, 2000);

					return false;
				} else {
					/* Fire Event */
		            $.event.trigger({
		                type: "SettingsSaved"
		            });
				}
 
				/* Show notice */
				$( '#rascals-panel-notices' ).notify( 'create' , {
					title: 'Success!',
					text: 'Settings are saved.'
				});
	    	}
		});
		return false;
	});
	
	/* Autosave */
	if ( $( '#rascals-panel' ).data('autosave') == true ) {
		$( '#_save' ).trigger('click');
	}


	/* Import Data
	------------------------------------------------------------------------*/	
	$( '.data-import' ).toggle( function () {
		var $textarea = '<textarea name="import" style="height:200px;overflow:auto" cols="" rows=""></textarea>';									        
		$( '#data-import-wrap .input-wrap' ).append($textarea);
		$( '#data-import-wrap' ).slideDown(400);
	    return false;
											  
	}, function () {
		$( '#data-import-wrap' ).slideUp( function() {
			$( '#data-import-wrap .input-wrap :input' ).remove();
		});
	    return false;
		
	});


    /* Notices
	------------------------------------------------------------------------*/
	$( '#rascals-panel-notices' ).notify();

});


/*------------------------------------------------------------------------

 Small Plugins

------------------------------------------------------------------------*/	


/* Serialize Post
------------------------------------------------------------------------*/
;(function($) {

    $.fn.serializePost = function() {  
        var data = {};  
        var formData = this.serializeArray();  
        for ( var i = formData.length; i--; ) {  
            var name = formData[i].name;  
            var value = formData[i].value;  
            var index = name.indexOf( '[]' );  
            if ( index > -1 ) {  
                name = name.substring( 0, index );  
                if ( !( name in data ) ) {  
                    data[name] = [];  
                }  
                data[name].push(value);  
            }  
            else  
                data[name] = value;  
        }  
        return data;  
    };

})(jQuery);