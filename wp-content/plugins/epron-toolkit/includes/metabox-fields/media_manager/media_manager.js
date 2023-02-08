/**
 * Media Manager Plugin
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Epron Toolkit
 * @version 1.0.0
 */


jQuery(document).ready(function($) {

	$( '.mb-sortable-list' ).each( function( i ) {		  
		var 
			container = $( this ),	  
			mm = $( '.mm-container', container ),
			settings = {},
			message_error = $( '.msg.msg-error', container ),
			target_input = $( '.mm-ids', container  ),
			explorer = $( '#mm-explorer-box' ),
			edit_box = $( '#mm-editor-box' ),
			main_edit_box = container,
			results = $( '.mm-block', explorer ),
			ids = [],
			item_id = null,
			selected_ids = [],
			saved_ids = [],
			ajax_timeout,
			timeout = 500,
			numberposts = 30,
			list_tpl,
			update_fields = {},
			pagenum,
			s,
			default_cover_src = $(this).attr('data-default-img');
				
		/* Init
        ------------------------------------------------------------------------*/

		/* Get media manager settings */
		settings['post_id'] = $( '.mm-settings', container ).data( 'post-id' );
		settings['mm_id'] = $( '.mm-settings', container ).data( 'mm-id' );
		settings['mm_layout'] = $( '.mm-settings', container ).data( 'mm-layout' );
		settings['mm_type'] = $( '.mm-settings', container ).data( 'mm-type' ); // images,audio,slider...
		settings['mm_admin_path'] = $( '.mm-settings', container ).data( 'mm-admin-path' );

		/* Convert saved array */
		var 
			saved_metadata = target_input.val();

		if ( saved_metadata !== '' ) {
			_sortable();
			saved_ids = saved_metadata.split( '|' );
		}
		
		/* Get List tpl and update fields */
		if ( settings['mm_layout'] === 'list' ) {
			list_tpl = container.find( '.mm-list-tpl' );

			/* get update fields */
			list_tpl.find( '.mm-update-field' ).each( function( i ){
				var 
					name = $( this ).attr( 'data-field' ),
					type = $( this ).attr( 'data-field-type' );
				update_fields[i] = new Object();
		        update_fields[i]['name'] = name;
		        update_fields[i]['type'] = type;
		    });
		}	

		
		/* Buttons
        ------------------------------------------------------------------------*/
		
		/* Select Media */
		var _select_media = function( event ) {
			if ( event.shiftKey || event.ctrlKey || event.metaKey || $( this ).hasClass( 'mm-select-button' ) ) {
				
				if ( $( this ).hasClass( 'mm-select-button' ) ) {
					$this = $( this ).parents( '.mm-item' );
				} else {
					$this = $( this );
				}
				
				var 
					id = $this.attr( 'id' );

				if ( $this.hasClass('mm-selected') ) {
					$this.removeClass( 'mm-selected' );
					selected_ids.splice( $.inArray( id, selected_ids ), 1 );
				} else {
					$this.addClass( 'mm-selected' );
					selected_ids.push( id );
				}

				/* Show or hide Delete button */
				if ( $( '.mm-item.mm-selected', container ).length > 0 )
					$( '.mm-delete-button', container ).css( 'display', 'inline-block' );
				else
					$( '.mm-delete-button', container ).hide();

			}

			event.preventDefault();
		}

		$( '.mm-item.mm-image, .mm-block.mm-list .mm-item .mm-select-button', container ).on( 'click', _select_media );

		/* Select all */
		$( '.mm-select-all', container ).on( 'click', function() {
			$( this ).toggleClass( 'selected' );
			selected_ids = [];

			if ( $( '.mm-item.mm-selected', container ).length > 0 ) {
				$( '.mm-item', container ).removeClass( 'mm-selected' );
				$( '.mm-delete-button', container ).hide();
			} else {
				$( '.mm-item', container ).addClass( 'mm-selected' );
				if ( $( '.mm-item.mm-selected', container ).length > 0 )
					$( '.mm-delete-button', container ).css( 'display', 'inline-block' );
				
				$( '.mm-item', container ).each( function() {
					id = $( this ).attr( 'id' );
					selected_ids.push( id );
				});
			}

			event.preventDefault();
		});

		/* Edit Media */
		var _edit_item = function( event ) {
			if ( ! $( this ).hasClass( 'disabled' ) ) {
				item_id = $( this ).parents( '.mm-item' ).attr( 'id' );
		    	_edit_media_box();
			}
			event.preventDefault();
		}
		$( '.mm-edit-button', mm ).on( 'click', _edit_item );

		/* Load Next */
		var _load_next = function( event ) {
			_media_explorer();
			event.preventDefault();
		}

		/* Save inline content */
		container.on( 'click', '.mm-open-editor .mm-save-button', function( event ){
			if ( ! $( this ).hasClass( 'disabled' ) ) {
				item_id = $( this ).parents( '.mm-item' ).attr( 'id' );
		    	_mm_editor_save();
			}
			event.preventDefault();
			
		} );

		/* Remove inline content */
		container.on( 'click', '.mm-open-editor .mm-edit-button', function(){
			var edit_button = $( this ),
				edit_button_parent = edit_button.parents( '.mm-item' );
			edit_button_parent.find( '.mm-inline-editor' ).slideUp( 400, function(){
				$( this ).remove();
				edit_button.removeClass( 'disabled' );
				edit_button_parent.removeClass( 'mm-open-editor' );
				edit_button_parent.removeClass( 'mm-error' );
			});
		} );

		/* Remove Media */
		$( '.mm-delete-button', container ).on( 'click', _remove_media );
		
		/* Add Media */
		$( '.mm-explorer', container ).on( 'click', function( event ) {
			_explorer_box();
			event.preventDefault();
		});

		// Add Custom Item
		$( '.mm-custom', container ).on( 'click', function( event ) {	

			var 
				metadata_string,
				admin_path = settings['mm_admin_path'];
				new_item = list_tpl.find( '.mm-item' ).clone(),
				id = null;

			
			id = _unique_id();

			new_item.attr( 'id', id );

			mm.append( new_item );

			/* Add new ID to array */
			ids.push( id );

			/* Join Arrays */		
			saved_ids = saved_ids.concat( ids );
			metadata_string = saved_ids.join( '|' );
		
			target_input.val( metadata_string );
			_sortable();

			_update_media();

			$( '.mm-new-item', mm ).fadeIn(800).removeClass( 'mm-new-item' );
			$( '.mm-ajax', container ).hide();
			$( '.mm-edit-button', mm ).on( 'click', _edit_item );
			$( '.mm-item.mm-image, .mm-block.mm-list .mm-item .mm-select-button', container ).off( 'click', _select_media );
			$( '.mm-item.mm-image, .mm-block.mm-list .mm-item .mm-select-button', container ).on( 'click', _select_media );
			// Open Dialog
			$( '.mm-item:last-child .mm-edit-button', mm ).trigger( 'click' );
			event.preventDefault();
		});
		
		/* Select All Media */
		$( '#mm-select' ).on( 'click', function( event ) {
			var $checkbox = $( this );
			ids = [];
			$( '.mm-item', results ).each( function( e ) {
				var id = $( this ).attr( 'id' );
				if ( $checkbox.is( ':checked' ) ) {
					$( this ).addClass( 'mm-selected' );
					ids.push( id );
				} else {
					$( this ).removeClass( 'mm-selected' );
					ids.splice( $.inArray( id, ids ), 1 );
				}
			});
		
		});
		
		
		/* Ajax Actions
		------------------------------------------------------------------------*/
		

		/* --- Media Explorer --- */
		function _media_explorer() {

 			$( '.mm-load-next', explorer ).off( 'click', _load_next );
			var data = {
				action: 'mm_actions',
				mm_action: 'media_explorer',
				page_num: pagenum,
				numberposts: numberposts,
				ids: saved_ids,
				update_fields : update_fields,
				s: s,
				layout : settings['mm_layout']
			};

					
			$( '#mm-explorer-loader' ).show();
			
			$.ajax( {
				url: ajaxurl,
				data: data,
				type: 'POST',
				timeout: 10000,
				success: function( response ) {

					if ( response === 'end pages' ) {
						$( '#mm-explorer-loader' ).hide();
						return;
					}
					/* Remove layout classes */
					results.removeClass( 'mm-grid mm-list' );
					/* Add explorer classes */
					var block_classes = $( '.mm-block', container ).attr( 'class' );
					results.addClass( block_classes );

					results.append( response );
					$( '#mm-explorer-loader' ).hide();
					pagenum ++;
					_get_images_ids( response );
					$( '.mm-load-next', explorer ).on( 'click', _load_next );
					response = '';
					return;

				}
			});
		}


		/* --- Add Media --- */
		function _add_media() {

			var data = {
				action: 'mm_actions',
				mm_action: 'add_media',
				update_fields : update_fields,
				items: ids,
				layout : settings['mm_layout']
			};
					
			$( '.mm-ajax', container ).css( 'display', 'inline-block' );
			
			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				timeout: 10000,
				success: function( response ) {
					mm.append( response );

					/* Menage Arrays */
					var metadata_string;

					/* Join Arrays */
					if ( saved_ids.length > 0 ) {
						saved_ids = saved_ids.concat( ids );
						metadata_string = saved_ids.join( '|' );
					} else {
						/* If saved array is empty */
						metadata_string = ids.join( '|' );
					}
					target_input.val( metadata_string );
					_sortable();
					_update_media();
					$( '.mm-new-item', mm ).fadeIn( 800 ).removeClass( 'mm-new-item' );
					$( '.mm-ajax', container ).hide();
					$( '.mm-edit-button', mm ).on( 'click', _edit_item );
					$( '.mm-item.mm-image, .mm-block.mm-list .mm-item .mm-select-button', container ).off( 'click', _select_media );
					$( '.mm-item.mm-image, .mm-block.mm-list .mm-item .mm-select-button', container ).on( 'click', _select_media );
				}
			});
		}


		/* --- Remove Media --- */
		function _remove_media( event ) {
			
			var 
				selected_items = $( '.mm-item.mm-selected', container );

			$( '.mm-select-all.selected' ).removeClass( 'selected' ); 

            message_error.hide();
			
			var data = {
				action: 'mm_actions',
				mm_action: 'remove_media',
				selected_ids: selected_ids,
				settings: settings
			};
			$( '.mm-ajax', container ).css( 'display', 'inline-block' );
			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				timeout: 10000,
				success: function( response ) {
					if (response === 'success' ) {
						selected_items.removeAttr( 'id' );
						_sortable();
						_update_media();

						selected_items.fadeOut( 400, function(){
							$(this).remove();
							$( '.mm-delete-button', container ).hide();
						});
					} else {
						$( '.mm-ajax', container).hide();
						message_error.show();
					}

					return false;
				}
			});
			event.preventDefault();
		}


		/* --- Update Media --- */
		function _update_media() {
            message_error.hide();
			
			var data = {
				action: 'mm_actions',
				mm_action: 'update_media',
				ids: target_input.val(),
				settings: settings
			};
			
			$( '.mm-ajax', container ).css( 'display', 'inline-block' );
			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				timeout: 10000,
				success: function( response ) {
					if (response !== 'success' ) {
						message_error.show();
					}
					$( '.mm-ajax', container ).hide();
					if ( mm.children().length === 0 ) 
						$( '.msg-dotted', container ).slideDown( 400 );
					else 
						$( '.msg-dotted', container ).slideUp( 400 );
					return false;
				}
			});
			return false;
		}


		/* Editor */

		/* --- Media Manger Editor --- */
		function _mm_editor() {

			if ( item_id.indexOf( 'C' ) !== -1 || item_id.indexOf( 'custom_id' ) !== -1 ) 
				custom = true;
			else 
				custom = false;

			var data = {
					action: 'mm_editor',
					item_id: item_id,
					settings: settings,
					custom: custom
				},
				edit_item_box;

			/* Show loader */
			if ( settings['mm_layout'] === 'list' ) {
				$( '#'+item_id, mm ).addClass( 'mm-loading' );
			} else {
				$( '#mm-editor-loader' ).show();
			}
			
			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				timeout: 10000,
				success: function( response ) {

					if ( settings['mm_layout'] === 'list' ) {
						var this_item = $( '#'+item_id, mm );
						this_item.append('<div class="mm-inline-editor">' +response+ '</div>' );
						edit_item_box = this_item.find( '.mm-inline-editor' );
						edit_item_box.hide();
						if ( default_cover_src === null ) {
							default_cover_src = this_item.find('.mm-field-image img').attr('src');
						}

						/* Init Groups */
						_groups( edit_item_box );

						/* Services */
						_services( edit_item_box );

						/* Slide Down Content */
						edit_item_box.slideDown( 400 );

						/* Add classes */
						this_item.addClass( 'mm-open-editor' );

						/* Hide loader */
						this_item.removeClass( 'mm-loading' );
					} else {
						 edit_item_box = edit_box;
						$( '#mm-editor-content', edit_item_box ).html( response );

						/* Init Groups */
						_groups( edit_item_box );

						/* Services */
						_services( edit_item_box );

						/* Show content */
						$( '#mm-editor-content', edit_item_box ).show();

						/* Hide loader */
						$( '#mm-editor-loader' ).hide();
					}

				}
			});
		}


		/* Save Editor data
		 -------------------------------- */
		function _mm_editor_save() {

			var item_fields = {},
				edit_item_box,
				inputs;

			/* List or Grid layout */
			if ( settings['mm_layout'] === 'list' ) {
				var this_item = $( '#'+item_id, mm );
				edit_item_box = this_item.find( '.mm-inline-editor' );
				this_item.find( '.mm-save-button' ).addClass( 'disabled' );

				/* Show loader */
				this_item.removeClass( 'mm-error' );
				this_item.addClass( 'mm-loading' );

				
				/* Set inputs */
				inputs = $( 'input, textarea, select', edit_item_box );
			} else {

				edit_item_box = edit_box;
				/* Show loader */
				$( '#mm-editor-loader, .ui-custom-dialog .loading-layer' ).show();

				/* Set inputs */
				inputs = $( '#mm-editor-content input, #mm-editor-content textarea, #mm-editor-content select', edit_box );
			}	

			inputs.each( function( i ){
				var name = $( this ).attr( 'name' );
				if ( $( this ).val() !== '' ) {
			        item_fields[name] = $( this ).val();
			    }
			});
			
			/* --- Helpers --- */
			
			/* Iframe */
			if ( item_fields.image_type === 'lightbox_soundcloud' || item_fields.image_type === 'lightbox_video' ) {
				var iframe_content = '';

				if ( item_fields.image_type === 'lightbox_soundcloud' )
					iframe_content = item_fields.lightbox_soundcloud;
				if ( item_fields.image_type === 'lightbox_video' ) 
					iframe_content = item_fields.lightbox_video;
				
				/* Get iframe attributes */
				var 
					iframe_content = $( iframe_content ),
					iframe = $( iframe_content ).filter( 'iframe' ),
					src = iframe.attr( 'src' ),
					width = iframe.attr( 'width' ),
					height = iframe.attr( 'height' );
					iframe_code = src + '|' + width + "|" + height;
					item_fields[ 'iframe_code' ] = iframe_code;
			} 

			var data = {
				action : 'mm_editor_save',
				update_fields : update_fields,
				fields : item_fields,
				item_id : item_id,
				settings : settings
			};

			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				success: function( response ) {

					/* Update fields */
					if ( settings['mm_layout'] === 'list' && response !== 'success' && response !== 'error' ) {

						var json = JSON.parse( response ),
							list_img,
							update_item = $( '#'+item_id, mm ),
							placeholder;
						$.each( json, function( index, obj ){
							if ( update_item.find( '[data-field="' + obj.name + '"]' ).length ) {
								var $item = update_item.find( '[data-field="' + obj.name + '"]' );
								var src = default_cover_src;
								
								if ( obj.type === 'text' ) {
									$item.html( obj.val );
								} else if ( obj.type === 'post_name' || obj.type === 'select' ) {
									var $input = $( '#'+item_id, mm ).find( "[name='"+obj.name+"']" );
									var opt = $input.find('option:selected').text();
									$item.html( opt );
								} else if ( obj.type === 'image' ) {

									if ( obj.val !== null ) {
										src = obj.val
									}
									
									$item.attr( 'src', src );
								}
					
							}

						});

					}
					if ( response === 'error' ) {
						$( '#'+item_id, mm ).addClass( 'mm-error' );
					}
					$( '#'+item_id, mm ).removeClass( 'mm-loading' );
					$( '#'+item_id, mm ).find( '.mm-save-button' ).removeClass( 'disabled' );
					$( '#mm-editor-loader, .ui-custom-dialog .loading-layer' ).hide();
				}
			});


		}
		
		
		/* Boxes
        ------------------------------------------------------------------------*/
		
		/* Edit media BOX */
		function _edit_media_box() {

			/* Don't show editor window on list layout. 
			Don't load content when editor is open */
			if ( settings['mm_layout'] === 'list' ) {
				var this_item = $( '#'+item_id, mm );
				if ( ! this_item.hasClass( 'mm-open-editor' ) ) {
					this_item.find( '.mm-edit-button' ).addClass( 'disabled' );
					_mm_editor();
				}
				return false;
			}

			$( '#mm-editor-box' ).dialog( {
				title: 'Edit Media',
				modal: false,
				width: 700,
				maxWidth:700,
				height: 700,
				dialogClass: 'ui-custom ui-custom-dialog',
				buttons: [
					{
						text: 'Update Item',
						'class': 'ui-button-update-item',
						click: function() {
							_mm_editor_save();
							
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


					/* --- */
					/* Resizable */
					/* Mobile Resizable */

				
					$( event.target ).parent().position({
					   my: "center",
					   at: "center",
					   of: window
					});

					$( window ).resize( function() {

						var 
							w = 700,
							windowWidth = $( window ).width(),
							windowHeight = $( window ).height();

						if ( windowWidth <= 768 ) {
							$( event.target ).parent().css( 'max-width', '90%' );
							$( event.target ).css( 'max-width', '90%' );
						} else {
							$( event.target ).parent().css( 'max-width', w );
							$( event.target ).css( 'max-width', w );
						} 

    					$( event.target ).parent().position({
						   my: "center",
						   at: "center",
						   of: window
						});

					});

					$( event.target ).parent().parent().css( 'top', '50px' );


					/* Add loader to */
					$( event.target ).parent().find( '.ui-dialog-buttonpane' ).append( $( '#mm-editor-loader' ) );
					
					_mm_editor();
				},
				close: function() {
				   $( '#mm-editor-loader' ).appendTo(edit_box);
				   $( '#mm-editor-content', edit_box).children().remove();
				   $( '#mm-editor-content', edit_box).hide();
				  
				}
			});
		}
		

	    /* Explorer BOX */
		function _explorer_box() {
			explorer.dialog( {
				title: 'Media Manager',
				modal: false,
				width: 600,
				height: 'auto',
				dialogClass :'ui-custom ui-custom-dialog',
				buttons: [
					{
						text: 'Add Selected Items',
						'class': 'ui-button-add-items',
						click: function() {
							if ( ids.length > 0 ) {
							    _add_media();
							    $(this).dialog( 'close' );
							}
							
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
				open: function ( event, ui ) {

	        		/* Buttons icons */
					$(event.target).parent().find( '.ui-button-cancel span' ).prepend( '<i class="fa icon fa-times"></i>' );
					$(event.target).parent().find( '.ui-button-add-items span' ).prepend( '<i class="fa icon fa-plus"></i>' );

					/* Add helper class to overlay layer */
					$( '.ui-widget-overlay' ).addClass( 'ui-custom-overlay' );

					/* Resizable */
					/* Mobile Resizable */
					var init_width = $( window ).width(),
						init_height = $( window ).height();

					if ( init_width <= 768 ) {
						$( event.target ).parent().css( 'max-width', '90%' );
					} else {
						$( event.target ).dialog( 'option', 'width', init_width-20 );
						$( event.target ).dialog( 'option', 'height', init_height-20 );
						$( event.target ).parent().css( 'position', 'fixed' );
					}
					$( event.target ).dialog( 'option', 'position', 'center' );

					$( window ).resize( function() {

						var windowWidth = $( window ).width(),
							windowHeight = $( window ).height();

						if ( windowWidth <= 768 ) {
							$( event.target ).parent().css( 'max-width', '90%' );
							$( event.target ).parent().css( 'position', 'absolute' );
						} else {
							$( event.target ).dialog( 'option', 'width', windowWidth-20 );
							$( event.target ).dialog( 'option', 'height', windowHeight-20 );
							$( event.target ).parent().css( 'position', 'fixed' );
							$( event.target ).parent().css( 'max-width', '100%' );
						}
    					$( event.target ).dialog( 'option', 'position', 'center' );

					});

					$( event.target ).parent().css( 'top', '10px' );
					/* -------- */

					/* Add loader to */
					$( event.target ).parent().find( '.ui-dialog-buttonpane' ).append( $( '#mm-explorer-loader' ) );

					s = '';
					ids.length = 0;
					pagenum = 1;
					_search();
					_media_explorer();
	    		},
	    		close: function () {
	    			results.html( '' );
					$( '#mm-search' ).val( '' );
					$( '#mm-select' ).attr( 'checked', false);
					$( '.mm-load-next', explorer ).off( 'click', _load_next );
	    		}

			});
		}


		/* ==================================================
		  HELPER FUNCTIONS 
		================================================== */

       	/* Services
       	 -------------------------------- */
		function _services( edit_item_box ) {

			/* -----------------  Add hearthis track */
			$( '.add-hearthis', edit_item_box ).on( 'click', function(event) {

				var 
					url = $( '#mm-audio-custom-url', edit_item_box ).val(),
					api_url = 'https://api-v2.hearthis.at',
					fragment,
					fragment_two,
					finall_url;

				$( '.services-messages .msg', edit_item_box ).hide();

				// Check
				if ( url.match(/hearthis.at/) && ! url.match(/listen/) ) {

					fragment = url.split('/').reverse()[1];
					fragment_two = url.split('/').reverse()[2];
					finall_url = api_url +'/'+fragment_two+'/'+fragment+'/';

					// Loading
					$( '#mm-editor-loader, .ui-custom-dialog .loading-layer' ).show();

					// Get track
					$.ajax({
						url: finall_url,
						type: 'GET',
						timeout: 10000
					}).done(function(response){

						if ( typeof response =='object' ){

							if ( response.stream_url ) {

								// Fill data
								// URL
								$( '#mm-audio-custom-url', edit_item_box ).val( response.stream_url );
								// Title
								$( '#mm-audio-title', edit_item_box ).val( response.title );
								// Artists
								$( '#mm-track_artists', edit_item_box ).val( response.user.username );
								// Artists URI
								$( '#mm-artists_url', edit_item_box ).val( response.user.permalink_url );
								$( '#mm-artists_target', edit_item_box ).val( '_blank' ).trigger('change');
								// Cover
								$( '.cover-source', edit_item_box ).val( 'external_link' ).trigger('change');
				
								$( '#r-cover', edit_item_box ).val( response.artwork_url );
								// Release
								$( '#mm-release_url', edit_item_box ).val( response.permalink_url );
								$( '#mm-release_target', edit_item_box ).val( '_blank' ).trigger('change');
								// Downlable
								if ( response.downloadable === 1 ) {
									$( '#mm-free_download', edit_item_box ).val( 'yes' ).trigger('change');
									$( '#mm-cart_url', edit_item_box ).val( response.download_url );
								}
								$( '.services-messages .msg-done', edit_item_box ).show();

							} else {
								// Stream does'n esists
								$( '.services-messages .msg-track-error', edit_item_box ).show();
							}
							
						} else {
							// OBJ doesn't exists
							$( '.services-messages .msg-track-error', edit_item_box ).show();
						}
						// Loading
						$( '#mm-editor-loader, .ui-custom-dialog .loading-layer' ).hide();
						
						    
					}).fail(function(jqXHR, textStatus){
					    if( textStatus === 'timeout') {     
					      	$( '.services-messages .msg-track-error', edit_item_box ).show();
					    }
					    $( '.services-messages .msg-track-error', edit_item_box ).show();
					    $( '#mm-editor-loader, .ui-custom-dialog .loading-layer' ).hide();
					});

				} else {
					if ( url.indexOf( "/listen/" ) !== 0 ) {
						$( '.services-messages .msg-already-exists', edit_item_box ).show();
					} else {	
						$( '.services-messages .msg-correct-link', edit_item_box ).show();
					}
				}

				event.preventDefault();

			});

			/* -----------------  Add Google Drive track */
			$( '.add-googledrive', edit_item_box ).on( 'click', function(event) {

				var 
					url = $( '#mm-audio-custom-url', edit_item_box ).val(),
					download_url = 'https://drive.google.com/uc?export=download&id=',
					finall_url;

					$( '.services-messages .msg', edit_item_box ).hide();
					
					// Check
					if ( url.indexOf('drive.google.com') >= 0 && url.indexOf('open?id=') >= 0 && url.indexOf('uc?export=download') < 0 ) {

						// Get ID
						var file_id = url.split('=')[1];
						
						if ( file_id !== '' ) {
							finall_url = download_url + file_id;
							$( '#mm-audio-custom-url', edit_item_box ).val( finall_url  );

							$( '.services-messages .msg-done', edit_item_box ).show();
						}
							
					} else {
						if ( url.indexOf('uc?export=download') >= 0 ) {
							$( '.services-messages .msg-already-exists', edit_item_box ).show();
						} else {	
							$( '.services-messages .msg-correct-link', edit_item_box ).show();
						}
						
					}

				event.preventDefault();

			});

		}

		/* Groups
		 -------------------------------- */
       	function _groups( edit_item_box ) {
       		$( '.mm-group', edit_item_box ).each( function() {
				var 
					group = $( this ).val(),
					group = 'mm-group-'+group;
				$( '.' + group, edit_item_box ).show();
			});
											 
			$( '.mm-group', edit_item_box ).change( function() {						 
				var group = $( this ).val(),
				main_group = $( this ).data( 'main-group' ),
				group = 'mm-group-'+group;
				$( '.' + main_group, edit_item_box ).hide();
				$( '.' + group, edit_item_box ).fadeIn( 600 );

			});
       	}
		 
		/* Get unique ID
		 -------------------------------- */
		function _unique_id() {
			var 
				$nr = Math.floor( Math.random()*400 ) + Math.round( Math.random()*400 ),
			id = 'C';

			$nr = $nr.toString();
			id = $nr+id; 

			// Check if ID already exists
			if ( $( '#'+id).length > 0 ) 
				return _unique_id();
			else 
				return id;
		}

		/* Sortable */
		function _sortable() {

			/* Clear temp array */
			ids = [];
			if ( target_input.val() === '' ) saved_ids = [];

			mm.sortable({
				cancel: '.mm-open-editor, .mm-head',
				handle: $( '.mm-item', mm ),
				update: function( event, ui ) {
					_update_ids();
					_update_media();
				}
			});
			_update_ids();
		}

		/* Update ids */
		function _update_ids() {
			saved_ids = mm.sortable( 'toArray' );
			saved_ids = $.grep(saved_ids, function(n, i){
  				return (n !== "" && n !== null);
			});
			var metadata_string = saved_ids.join( '|' );
			target_input.val(metadata_string);
		}
		 
		/* Scroll Box */
		function _scroll_box(e){
			var elem = $( e.currentTarget );
			if ( elem[0].scrollHeight - elem.scrollTop() === elem.outerHeight()-3 ) {
				if (ajax_timeout !== undefined) 
					clearTimeout( ajax_timeout );
				//ul_wrap.unbind( 'scroll', _scroll_box );
				ajax_timeout = setTimeout( _media_explorer, timeout );

				return false;
			}
		}
		
	    /* Search */
		function _search() {
		    $( '#mm-search' ).keyup( function() {													
				if ( ajax_timeout !== undefined ) 
               		clearTimeout(ajax_timeout);									
				s = $(this).val();
				if (s === undefined) 
					return;
				results.html( '' );
				pagenum = 1;
                ajax_timeout = setTimeout( _media_explorer, timeout );
	        });
		}
		
		/* Get images ID */
		function _get_images_ids( response ) {
			
			if ( $( '.mm-item', results ).length > 0 ) {
				$( '.mm-item', results ).off( 'click' );
				$( '.mm-item', results ).on( 'click', function() {
					var id = $( this ).attr( 'id' );
					if ( $( this ).is( '.mm-selected' ) ) {
						$( this ).removeClass( 'mm-selected' );
						ids.splice($.inArray( id, ids ), 1);
					} else {
						$( this ).addClass( 'mm-selected' );
						ids.push( id );
					}
					return false;
				});
			}	
		}
		
		
	});

});