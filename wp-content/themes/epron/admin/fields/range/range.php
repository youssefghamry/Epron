<?php
/**
 * Rascals Theme Panel Class
 *
 * @package     RascalsThemePanel
 * @subpackage  range
 * @author      Mariusz Rek
 * @version     2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'RascalsThemePanel_range' ) ) {

	class RascalsThemePanel_range extends RascalsThemePanel {

		private static $_initialized = false;
		private static $_args;
		private static $_saved_options;
		private static $_option;


		/**
         * Field Constructor.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		public function __construct( $option, $args, $saved_options ) {

			// Variables
			self::$_args = $args;
			self::$_saved_options = $saved_options;
			self::$_option = $option;
			
			// Only for first instance
			if ( ! self::$_initialized ) {
	            self::$_initialized = true;
	        }

		}


		/**
         * Field Render Function.
         * Takes the vars and outputs the HTML
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function render() {

			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'] ) . '">';
			} else {
				echo '<div class="box-row clearfix">';
			}

				// Input Wrap
				echo '<div class="box-row-input">';

					// Label
					echo '<div class="box-tc box-tc-label">';
						if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
							echo '<label for="' . esc_attr( self::$_option['id'] ) . '" >' . esc_html( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_html( self::$_option['sub_name'] ) . '</div>';
						}
						echo '<input name="' . esc_attr( self::$_option['id'] ) . '" id="' . esc_attr( self::$_option['id'] ) . '" type="number" value="' . esc_attr( self::$_option['std'] ) . '" class="range-input" min="'.esc_attr( self::$_option['min'] ).'" max="'.esc_attr( self::$_option['max'] ).'"/>';
						if ( isset( self::$_option['unit'] ) && self::$_option['unit']) {
							echo '<span class="range-unit">' . esc_html( self::$_option['unit'] ) . '</span>';
						}

						// Display help
						if ( isset( self::$_option['desc'] ) && self::$_option['desc'] !== '' ) {
							echo '<div class="help-box">';
							$this->e_esc( self::$_option['desc'] );
							echo '</div>';
						}
					echo '</div>';

				echo '</div>';

				if ( ! isset( self::$_option['separator'] ) || ( self::$_option['separator'] === true ) ) {	
					echo '<div class="box-row-line"></div>';
				}

			
			echo '</div>';

		}

	}
}