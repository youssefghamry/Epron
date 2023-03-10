<?php
/**
 * Posts Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

if ( ! class_exists( 'RascalsBox_posts' ) ) {

	class RascalsBox_posts extends RascalsBox {

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

	            // Multiple
	        	if ( isset( self::$_option['multiple'] ) && self::$_option['multiple'] ) {

	        		// Enqueue
					add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) ); 
	        	}
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

			$path = self::$_args['admin_path'];

			// Load script
			$handle = self::$_option['type'] . '.js';
			if ( ! wp_script_is( $handle, 'enqueued' ) ) {
				wp_enqueue_script( $handle, $path . '/fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.js', false, false, true );
			}

			// Load style
			$handle_css = self::$_option['type'] . '.css';
			if ( ! wp_style_is( $handle, 'enqueued' ) ) {
				wp_enqueue_style( $handle, $path . '/fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.css' );
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
							echo '<label for="' . esc_attr( self::$_option['id'] ) . '" >' . esc_attr( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_attr( self::$_option['sub_name'] ) . '</div>';
						}


						// Multiple or single
						if ( isset( self::$_option['multiple'] ) && self::$_option['multiple'] ) {

							if ( isset( self::$_option['save_empty'] ) && self::$_option['save_empty'] === true ) {
								$save_empty = 'save-empty';
							} else {
								$save_empty = '';
							}
			
							echo '<select name="' . esc_attr( self::$_option['id'] ) . '[]" id="' . esc_attr( self::$_option['id'] ) . '" multiple="multiple" size="6"  class="multiselect ' . esc_attr( $save_empty ) .'">';
						} else {
							echo '<select name="' . esc_attr( self::$_option['id'] ) . '" id="' . esc_attr( self::$_option['id'] ) . '" size="1">';
						}

						if (isset( self::$_option['options'] ) ) {
							foreach ( self::$_option['options'] as $option ) {

								// Multiple or single
								if ( isset( self::$_option['multiple'] ) && self::$_option['multiple'] ) {
									if ( isset( self::$_option['std'] ) && in_array( $option['value'], self::$_option['std'] )) $selected = 'selected';
									else $selected = '';
								} else {
									if ( isset( self::$_option['std'] ) && self::$_option['std'] === $option['name'] ) $selected = 'selected';
									else $selected = '';
								}

								echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['name'] ) . '</option>';
							}
						}
						
						if ( post_type_exists( self::$_option['post_type'] ) ) {

							$posts = get_posts( array('post_type' => self::$_option['post_type'], 'showposts' => -1 ) ); 
							if ( isset( $posts ) && is_array( $posts ) ) {
								foreach ( $posts as $post ) {

									// Multiple or single
									if ( isset( self::$_option['multiple'] ) && self::$_option['multiple'] ) {
										if ( in_array( $post->ID, self::$_option['std'] )) $selected = 'selected';
										else $selected = '';
									} else {
										if ( $post->ID === self::$_option['std'] ) $selected = 'selected';
										else $selected = '';
									}

									$option = '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $post->ID ). '">';
									$option .= esc_html( $post->post_title );
									$option .= '</option>';
									$this->e_esc( $option );
								}
							}
						}
						
						echo '</select>';

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