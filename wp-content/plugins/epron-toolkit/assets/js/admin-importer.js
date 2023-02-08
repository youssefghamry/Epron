/**
 * Importer scripts
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Epron Toolkit
 * @version 1.0.0
 */

jQuery(document).ready(function($) {

	"use strict"; 

	// Show message after imported
	var check_interval = setInterval(function(){ check_result() }, 500);

	var result = $( '#rascals-importer-message' ).text();
 	if ( result.indexOf('_IMPORTFINISH') < 0 ) {
 		$('.rascals-gdpr-confirm').removeClass('hidden');
 	}

	function check_result() {
	 	if ( result.indexOf('_IMPORTFINISH') >= 0 ) {
	 		clearTimer();
	 		$('.rascals-gdpr-confirm').remove();
	 		$('.confirm-layer').remove();
	 		$( '#rascals-importer-success' ).fadeIn();
	 		$( '.rascals-importer-loading-msg' ).fadeOut();
	 	}

	}

	function clearTimer() {
	    clearInterval(check_interval);
	}

	// Select styles
	$( '.demo .rascals-import-start' ).on( 'click', function(){
		$('.rascals-gdpr-confirm').slideUp();
	 	$('.confirm-layer').remove();
		$( '.demos, .rascals-importer-info' ).slideUp();
		$( '.rascals-importer-loading-msg' ).slideDown();

		var id = $( this ).attr('data-id');

		$( '#selected_demo_content' ).val(id);
	});

	// Confirm
	$('#confirm').change(function() {
        if(this.checked) {
           $('.rascals-importer-wrap').addClass('accepted');
        } else {
        	$('.rascals-importer-wrap').removeClass('accepted');
        }
                
    });

});