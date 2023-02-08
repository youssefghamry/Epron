<?php
/**
 * Gradient Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

if ( ! class_exists( 'RascalsBox_gradient' ) ) {

	class RascalsBox_gradient extends RascalsBox {

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

	            // Enqueue
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

	            // Ajax
	            add_action( 'wp_ajax_easy_link_ajax', array( $this, 'easy_link_ajax' ) );            
	            
	        }

		}


		/**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		public function enqueue() {

			wp_enqueue_script( 'wp-color-picker' );
	    	wp_enqueue_style( 'wp-color-picker' );
			
		}


		/**
         * Field Render Function.
         * Takes the vars and outputs the HTML
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function render() {

			if ( isset( self::$_saved_options[self::$_option['id'][0]['id']] ) ) {
				self::$_option['std'][0]['std'] = self::$_saved_options[self::$_option['id'][0]['id']];
			}
			if ( isset( self::$_saved_options[self::$_option['id'][1]['id']] ) ) {
				self::$_option['std'][1]['std'] = self::$_saved_options[self::$_option['id'][1]['id']];
			}
			if ( isset( self::$_saved_options[self::$_option['id'][2]['id']] ) ) {
				self::$_option['std'][2]['std'] = self::$_saved_options[self::$_option['id'][2]['id']];
			}
			


			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'][0]['id'] ) . '">';
			} else {
				echo '<div class="box-row clearfix">';
			}

				// Input Wrap
				echo '<div class="box-row-input">';

					// Label
					echo '<div class="box-tc box-tc-label">';
						if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
							echo '<label>' . esc_attr( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_attr( self::$_option['sub_name'] ) . '</div>';
						}

						// Field
						// ---------------------------------------
						echo '<div class="gradient-col">';
						echo '<input name="' . esc_attr( self::$_option['id'][0]['id'] ) . '" id="' . esc_attr( self::$_option['id'][0]['id'] ) . '" type="text" value="' . esc_attr( self::$_option['id'][0]['std'] ) . '" class="colorpicker-input gradient-from" />';
						echo '</div>';

						echo '<div class="gradient-col">';
						echo '<input name="' . esc_attr( self::$_option['id'][1]['id'] ) . '" id="' . esc_attr( self::$_option['id'][1]['id'] ) . '" type="text" value="' . esc_attr( self::$_option['id'][1]['std'] ) . '" class="colorpicker-input gradient-to" />';
						echo '</div>';

						echo '<div class="gradient-col">';
						echo '<input name="' . esc_attr( self::$_option['id'][2]['id'] ) . '" id="' . esc_attr( self::$_option['id'][2]['id'] ) . '" type="text" value="' . esc_attr( self::$_option['id'][2]['std'] ) . '" class="gradient-direction" />';
						echo '</div>';

						// ----------------------------------------

						// Display help
						if ( isset( self::$_option['desc'] ) && self::$_option['desc'] !== '' ) {
							echo '<p class="help-box">';
							$this->e_esc( self::$_option['desc'] );
							echo '</p>';
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