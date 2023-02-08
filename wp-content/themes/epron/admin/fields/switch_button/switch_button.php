<?php
/**
 * Rascals Theme Panel Class
 *
 * @package     RascalsThemePanel
 * @subpackage  switch_button
 * @author      Mariusz Rek
 * @version     2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'RascalsThemePanel_switch_button' ) ) {
	class RascalsThemePanel_switch_button extends RascalsThemePanel {

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
			
			// Display only on admin page
			$current_screen = get_current_screen();

			if ( strpos( $current_screen->base, 'theme-panel' ) !== false ) {

				$path = get_template_directory_uri() . '/admin';

				// Load script
				$handle = self::$_option['type'] . '.js';
				if ( ! wp_script_is( $handle, 'enqueued' ) ) {
					wp_enqueue_script( $handle, $path . '/fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.js', false, false, true );
				}

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

			if ( isset( self::$_saved_options[self::$_option['id']] ) ) {
				self::$_option['std'] = self::$_saved_options[self::$_option['id']];
			}

			if ( isset( self::$_saved_options[self::$_option['id']] ) ) {
				self::$_option['std'] = self::$_saved_options[self::$_option['id']];
			}

			if ( ! isset( self::$_option['options'] ) ) {
				self::$_option['options'] = array(
					array( 'name' => 'ON', 'value' => 'on' ),
					array( 'name' => 'OFF', 'value' => 'off' )
				);
			}

			// ON Value
			$selected_val = self::$_option['options'][0]['value'];

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

						echo '<div class="switch-wrap">';
							echo '<select name="' . esc_attr( self::$_option['id'] ) . '" id="' . esc_attr( self::$_option['id'] ) . '" size="1" >';
								
								foreach ( self::$_option['options'] as $option ) {
									if ( isset( self::$_option['std'] ) && self::$_option['std'] === $option['value'] ) {
										$selected = 'selected';
										$selected_val = $option['value'];
									}
									else {
										$selected = '';
									}
									echo "<option " . esc_attr( $selected ) ." value='" . esc_html( $option['value'] ) . "'>" . esc_html( $option['name'] ) . "</option>";
								}
								
								echo '</select>';

							// Switch
							$init_class = 'off';
							if ( $selected_val === self::$_option['options'][0]['value'] ) {
								$init_class = 'on';
							}
							echo '<div class="switch-on-off ' . esc_attr( $init_class ).'">
	  								<div class="onstate" data-on="' . esc_attr( self::$_option['options'][0]['value'] ).'">ON</div>
	  								<div class="offstate" data-off="' . esc_attr( self::$_option['options'][1]['value'] ).'">OFF</div>
	  							</div>';

						echo '</div>';

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