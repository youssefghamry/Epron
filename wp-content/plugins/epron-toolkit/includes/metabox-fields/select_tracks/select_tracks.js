/*------------------------------------------------------------------------
 Track Select Plugin
 Copyright: Rascals Themes
 www: http://rascals.eu
------------------------------------------------------------------------*/

;(function($) {

	if ( $( '.tracks-select-block' ).length ) {

		$( '.tracks-select-block' ).each( function () {

			var  
				block = $( this ),
				target_input = block.find( '.tracks-ids' ),
				track_selector = block.find( '.track-id' ),
				tracklist_block = block.find( '.box-tp-block' ),
				init_id = track_selector.val(),
				ajax_timeout,
				track_id,
				timeout = 500;
			
			/* Get Track ID */
			track_selector.on( 'change', function() {
  				track_id = $( this ).val();
  				
  				if ( tracklist_block.hasClass( 'new' ) || init_id === 'none' ) {
  					init_id = track_id;
					_display_links();
				} else {
					/* When list is modified */
					if ( confirm( "You will lose the settings of your tracklist. Are you sure you want to delete this?" ) ){
						init_id = track_id;
				       _display_links();
				    }
				    else {

				    	$( this ).val( init_id );
				        return false;
				    }
				}
  		
			});

			/* Remove Track */
			$( block ).on( 'click', '.remove-track', function(e) {
				$( this ).parent().remove();
				tracklist_block.trigger( 'sortupdate' );
				e.preventDefault();
			});

			/* Update IDS */
			_update_ids = function() {
				// selected_ids.push( id );
				var ids = [];
				tracklist_block.find( '.track-item' ).each( function(){
					var id = $( this ).attr( 'data-id' );
					ids.push( id );
				});
				ids = ids.join( '|' );
				target_input.val( ids );
			}


			/* Display links */
			var _display_links = function() {

				if ( track_id === 'none' ) {
					tracklist_block.empty().addClass( 'new' );
					target_input.val( '' )
					return;
				}
				var 
					data = {
						action: 'get_tracks_ajax',
						id: track_id
					};
						
				$( '.ajax-loader', block ).show();
				
				$.ajax( {
					url: ajaxurl,
					data: data,
					type: 'POST',
					success: function( response ) {
						/* Remove edit post */
						var old_edit_post = block.find( '.edit-track-post' ).remove();

						/* Get edit post link */
						var edit_post = $( response ).filter( '.edit-track-post' );

						/* Insert response */
					    tracklist_block.html( response );

					    /* Add new edit post */
					    tracklist_block.find( '.edit-track-post' ).appendTo( tracklist_block );					    
					   
					    tracklist_block.addClass( 'new' );
						$( '.ajax-loader', block ).hide();
						_sortable();
						_update_ids();
					}
				});
			};


			/* Sortable */
			function _sortable() {

				tracklist_block.sortable({
					handle: $( '.track-item', tracklist_block )
				});
				tracklist_block.on('sortupdate',function(){
					tracklist_block.removeClass( 'new' );
					_update_ids();
				});

				
			}

			_sortable();
			
		
		});
	}
	

})(jQuery);