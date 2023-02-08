<?php

/**
 *
 * Contains the main functions to display widgets
 *
 * @class RascalsWidgets
 *
 * @package         EpronToolkit
 * @author          Rascals Themes
 * @copyright       Rascals Themes
 * @version       	1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RascalsWidgets {
	
	/**
	 * Rascals CPT Constructor.
	 * @return void
	 */
	public function __construct( ) {


		// Admin
		if ( is_admin() ) {
			
			// Register scripts and styles on widgets.php page
			add_action( 'admin_enqueue_scripts', array( $this, 'widgetsScripts' ) );
		}

	}

	/**
	 * Register scripts and styles
	 * @return void
	 */
	public function widgetsScripts( $hook ) {

		if ( 'widgets.php' === $hook ) {

	        wp_enqueue_script( 'rascals-widgets-admin' , esc_url( RASCALS_TOOLKIT_URL ) . '/assets/js/admin-widgets.js' , array( 'jquery' ) ,false, true );

	     	wp_enqueue_style( 'rascals-widgets-admin', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/css/admin-widgets.css', array() , false , 'all' );
	    }
		
	}


	/* ==================================================
	  Display Widget Content 
	================================================== */


	/**
	 * Display tabs container
	 * @param  array $tabs
	 * @return string
	 */
	
	public function tabsStart( $tabs = array() ){

		$tabs_a = array_combine( range( 1, count( $tabs ) ), $tabs );
		echo '<div class="rt-tabs-wrap">';
		foreach ($tabs_a as $k => $tab ) {

			$checked = ( $k === 1 ) ? 'checked' : '';

			echo '<div data-id="rt-tab-' . esc_attr( $k ) . '" class="rt-tab-nav ' . esc_attr( $checked ) . '">' . $tab . '</div>';
		}

	}

	/**
	 * Display tabs end container
	 * @return string
	 */
	public function tabsEnd(){

		echo '</div>';
	}

	/**
	 * Display tab
	 * @param  integer $nr
	 * @return string
	 */
	public function tabStart( $nr = 1 ){

		echo '<section data-id="rt-content-' . esc_attr( $nr ) . '" class="rt-tab">';
	}

	/**
	 * Display tab end container
	 * @return string
	 */
	public function tabEnd(){

		echo '</section>';
	}




	/* ==================================================
	  Display Widgets Fields 
	================================================== */

	// Select
	public static function select( $args ) {

		$output = '';

		$output .= '<p class="rt-widget-input">';

		// Label
		$output .= '<label for="' . esc_attr( $args['id'] ). '"> ' . esc_html( $args['label'] ) . '</label>';
		$output .= '<select id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" class="widefat">';
		foreach( $args['options'] as $option ) {	
			if ( $args['value'] === $option['value'] ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
     		$output .= '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['label'] ) . '</option>';
		}
		$output .= '</select>';

		// Desc
		if ( $args['desc'] !== '' ) {
			$output .= '<span class="rt-widget-desc">' . wp_kses_post( $args['desc'] ) . '</span>';
		}
		$output .= '</p>';

		return $output;
	}

	public function select_e( $args ) {
		echo self::select( $args );
	}


	// Input
	public static function input( $args = array() ) {
		$output = '';
		$output .= '<p class="rt-widget-input">';

		// Label
		$output .= '<label for="' . esc_attr( $args['id'] ) . '"> ' . esc_html( $args['label'] ) . '</label>';

		// Field
		$output .= '<input type="text" value="' . esc_attr( $args['value'] ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" class="widefat" />';

		// Desc
		if ( $args['desc'] !== '' ) {
			$output .= '<span class="rt-widget-desc">' . wp_kses_post( $args['desc'] ) . '</span>';
		}
		$output .= '</p>';

		return $output;
	}

	public function input_e( $args ) {
		echo self::input( $args );
	}


	// Number
	public static function number( $args = array() ) {
		$output = '';
		$output .= '<p class="rt-widget-input rt-widget-input-number">';

		// Label
		$output .= '<label for="' . esc_attr( $args['id'] ) . '"> ' . esc_html( $args['label'] ) . '</label>';

		// Field
		$output .= '<input type="number" min="' . esc_attr( $args['min'] ) . '" max="' . esc_attr( $args['max'] ) . '" value="' . esc_attr( $args['value'] ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" class="widefat" />';

		if ( $args['units'] !== '' ) {
			$output .= '<span class="rt-widget-units">' . esc_attr( $args['units'] ) . '</span>';
		}

		// Desc
		if ( $args['desc'] !== '' ) {
			$output .= '<span class="rt-widget-desc">' . wp_kses_post( $args['desc'] ) . '</span>';
		}
		$output .= '</p>';

		return $output;
	}

	public function number_e( $args ) {
		echo self::number( $args );
	}


	// Checkbox
	public static function checkbox( $args = array() ) {
		$output = '';

		// Check saved value
		if ( $args['value'] ) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
		
		$output .= '<p class="rt-widget-input">';

		// Field
		$output .= '<input class="checkbox" type="checkbox" value="yes" id="' . esc_attr( $args['id'] ) . '" ' . esc_attr( $checked ) . ' name="' . esc_attr( $args['name'] ) . '" />';

		// Label
		$output .= '<label for="' . esc_attr( $args['id'] ) . '"> ' . esc_html( $args['label'] ) . '</label>';

		// Desc
		if ( $args['desc'] !== '' ) {
			$output .= '<span class="rt-widget-desc">' . wp_kses_post( $args['desc'] ) . '</span>';
		}
		$output .= '</p>';

		return $output;
	}

	public function checkbox_e( $args ) {
		echo self::checkbox( $args );
	}
}