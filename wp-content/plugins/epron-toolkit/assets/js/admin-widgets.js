/**
 * Widgets scripts
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Epron Toolkit
 * @version 1.0.0
 */


jQuery(document).ready(function() {

	"use strict";

	/* Widget Tabs
	 ---------------------------------------------------------------------- */
	(function() {
		
		$( 'body' ).on( 'click', '.rt-tab-nav', function(){

			var $tabs = $( this ).parents( '.rt-tabs-wrap' );

			$tabs.find( '.rt-tab-nav' ).removeClass( 'checked' );
			$( this ).addClass( 'checked' );

		} );

		
	})();

});