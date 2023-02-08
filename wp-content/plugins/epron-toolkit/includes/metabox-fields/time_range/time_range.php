<?php
/**
 * Time Range Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}
if ( ! class_exists( 'RascalsBox_time_range' ) ) {

	class RascalsBox_time_range extends RascalsBox {

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
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'][0]['id'] ) . '">';
			} else {
				echo '<div class="box-row clearfix">';
			}
				$format = get_option( 'time_format' );

				if ( strpos( $format, 'H' ) !== false ) {

					$start_time = self::$_option['id'][0]['std'];
					$end_time = self::$_option['id'][1]['std'];

					self::$_option['id'][0]['std'] = date( 'H:i', strtotime( $start_time ) );
					self::$_option['id'][1]['std'] = date( 'H:i', strtotime( $end_time ) );

				    // Military time
					$time_a = array(
						array( 'value' => '24:00', 'name' => '24:00' ),
						array( 'value' => '00:15', 'name' => '00:15' ),
						array( 'value' => '00:30', 'name' => '00:30' ),
						array( 'value' => '00:45', 'name' => '00:45' ),

						array( 'value' => '01:00', 'name' => '01:00' ),
						array( 'value' => '01:15', 'name' => '01:15' ),
						array( 'value' => '01:30', 'name' => '01:30' ),
						array( 'value' => '01:45', 'name' => '01:45' ),

						array( 'value' => '02:00', 'name' => '02:00' ),
						array( 'value' => '02:15', 'name' => '02:15' ),
						array( 'value' => '02:30', 'name' => '02:30' ),
						array( 'value' => '02:45', 'name' => '02:45' ),

						array( 'value' => '03:00', 'name' => '03:00' ),
						array( 'value' => '03:15', 'name' => '03:15' ),
						array( 'value' => '03:30', 'name' => '03:30' ),
						array( 'value' => '03:45', 'name' => '03:45' ),

						array( 'value' => '04:00', 'name' => '04:00' ),
						array( 'value' => '04:15', 'name' => '04:15' ),
						array( 'value' => '04:30', 'name' => '04:30' ),
						array( 'value' => '04:45', 'name' => '04:45' ),

						array( 'value' => '05:00', 'name' => '05:00' ),
						array( 'value' => '05:15', 'name' => '05:15' ),
						array( 'value' => '05:30', 'name' => '05:30' ),
						array( 'value' => '05:45', 'name' => '05:45' ),

						array( 'value' => '06:00', 'name' => '06:00' ),
						array( 'value' => '06:15', 'name' => '06:15' ),
						array( 'value' => '06:30', 'name' => '06:30' ),
						array( 'value' => '06:45', 'name' => '06:45' ),

						array( 'value' => '07:00', 'name' => '07:00' ),
						array( 'value' => '07:15', 'name' => '07:15' ),
						array( 'value' => '07:30', 'name' => '07:30' ),
						array( 'value' => '07:45', 'name' => '07:45' ),

						array( 'value' => '08:00', 'name' => '08:00' ),
						array( 'value' => '08:15', 'name' => '08:15' ),
						array( 'value' => '08:30', 'name' => '08:30' ),
						array( 'value' => '08:45', 'name' => '08:45' ),

						array( 'value' => '09:00', 'name' => '09:00' ),
						array( 'value' => '09:15', 'name' => '09:15' ),
						array( 'value' => '09:30', 'name' => '09:30' ),
						array( 'value' => '09:45', 'name' => '09:45' ),

						array( 'value' => '10:00', 'name' => '10:00' ),
						array( 'value' => '10:15', 'name' => '10:15' ),
						array( 'value' => '10:30', 'name' => '10:30' ),
						array( 'value' => '10:45', 'name' => '10:45' ),

						array( 'value' => '11:00', 'name' => '11:00' ),
						array( 'value' => '11:15', 'name' => '11:15' ),
						array( 'value' => '11:30', 'name' => '11:30' ),
						array( 'value' => '11:45', 'name' => '11:45' ),

						array( 'value' => '12:00', 'name' => '12:00' ),
						array( 'value' => '12:15', 'name' => '12:15' ),
						array( 'value' => '12:30', 'name' => '12:30' ),
						array( 'value' => '12:45', 'name' => '12:45' ),

						array( 'value' => '13:00', 'name' => '13:00' ),
						array( 'value' => '13:15', 'name' => '13:15' ),
						array( 'value' => '13:30', 'name' => '13:30' ),
						array( 'value' => '13:45', 'name' => '13:45' ),

						array( 'value' => '14:00', 'name' => '14:00' ),
						array( 'value' => '14:15', 'name' => '14:15' ),
						array( 'value' => '14:30', 'name' => '14:30' ),
						array( 'value' => '14:45', 'name' => '14:45' ),

						array( 'value' => '15:00', 'name' => '15:00' ),
						array( 'value' => '15:15', 'name' => '15:15' ),
						array( 'value' => '15:30', 'name' => '15:30' ),
						array( 'value' => '15:45', 'name' => '15:45' ),

						array( 'value' => '16:00', 'name' => '16:00' ),
						array( 'value' => '16:15', 'name' => '16:15' ),
						array( 'value' => '16:30', 'name' => '16:30' ),
						array( 'value' => '16:45', 'name' => '16:45' ),

						array( 'value' => '17:00', 'name' => '17:00' ),
						array( 'value' => '17:15', 'name' => '17:15' ),
						array( 'value' => '17:30', 'name' => '17:30' ),
						array( 'value' => '17:45', 'name' => '17:45' ),

						array( 'value' => '18:00', 'name' => '18:00' ),
						array( 'value' => '18:15', 'name' => '18:15' ),
						array( 'value' => '18:30', 'name' => '18:30' ),
						array( 'value' => '18:45', 'name' => '18:45' ),

						array( 'value' => '19:00', 'name' => '19:00' ),
						array( 'value' => '19:15', 'name' => '19:15' ),
						array( 'value' => '19:30', 'name' => '19:30' ),
						array( 'value' => '19:45', 'name' => '19:45' ),

						array( 'value' => '20:00', 'name' => '20:00' ),
						array( 'value' => '20:15', 'name' => '20:15' ),
						array( 'value' => '20:30', 'name' => '20:30' ),
						array( 'value' => '20:45', 'name' => '20:45' ),

						array( 'value' => '21:00', 'name' => '21:00' ),
						array( 'value' => '21:15', 'name' => '21:15' ),
						array( 'value' => '21:30', 'name' => '21:30' ),
						array( 'value' => '21:45', 'name' => '21:45' ),

						array( 'value' => '22:00', 'name' => '22:00' ),
						array( 'value' => '22:15', 'name' => '22:15' ),
						array( 'value' => '22:30', 'name' => '22:30' ),
						array( 'value' => '22:45', 'name' => '22:45' ),

						array( 'value' => '23:00', 'name' => '23:00' ),
						array( 'value' => '23:15', 'name' => '23:15' ),
						array( 'value' => '23:30', 'name' => '23:30' ),
						array( 'value' => '23:45', 'name' => '23:45' ),
					);

				} else {

					$start_time = self::$_option['id'][0]['std'];
					$end_time = self::$_option['id'][1]['std'];

					self::$_option['id'][0]['std'] = date( 'g:i A', strtotime( $start_time ) );
					self::$_option['id'][1]['std'] = date( 'g:i A', strtotime( $end_time ) );

					// Standard time
					$time_a = array(
						array( 'value' => '12:00 AM', 'name' => '12:00 AM' ),
						array( 'value' => '12:15 AM', 'name' => '12:15 AM' ),
						array( 'value' => '12:30 AM', 'name' => '12:30 AM' ),
						array( 'value' => '12:45 AM', 'name' => '12:45 AM' ),

						array( 'value' => '1:00 AM', 'name' => '1:00 AM' ),
						array( 'value' => '1:15 AM', 'name' => '1:15 AM' ),
						array( 'value' => '1:30 AM', 'name' => '1:30 AM' ),
						array( 'value' => '1:45 AM', 'name' => '1:45 AM' ),

						array( 'value' => '2:00 AM', 'name' => '2:00 AM' ),
						array( 'value' => '2:15 AM', 'name' => '2:15 AM' ),
						array( 'value' => '2:30 AM', 'name' => '2:30 AM' ),
						array( 'value' => '2:45 AM', 'name' => '2:45 AM' ),

						array( 'value' => '3:00 AM', 'name' => '3:00 AM' ),
						array( 'value' => '3:15 AM', 'name' => '3:15 AM' ),
						array( 'value' => '3:30 AM', 'name' => '3:30 AM' ),
						array( 'value' => '3:45 AM', 'name' => '3:45 AM' ),

						array( 'value' => '4:00 AM', 'name' => '4:00 AM' ),
						array( 'value' => '4:15 AM', 'name' => '4:15 AM' ),
						array( 'value' => '4:30 AM', 'name' => '4:30 AM' ),
						array( 'value' => '4:45 AM', 'name' => '4:45 AM' ),

						array( 'value' => '5:00 AM', 'name' => '5:00 AM' ),
						array( 'value' => '5:15 AM', 'name' => '5:15 AM' ),
						array( 'value' => '5:30 AM', 'name' => '5:30 AM' ),
						array( 'value' => '5:45 AM', 'name' => '5:45 AM' ),

						array( 'value' => '6:00 AM', 'name' => '6:00 AM' ),
						array( 'value' => '6:15 AM', 'name' => '6:15 AM' ),
						array( 'value' => '6:30 AM', 'name' => '6:30 AM' ),
						array( 'value' => '6:45 AM', 'name' => '6:45 AM' ),

						array( 'value' => '7:00 AM', 'name' => '7:00 AM' ),
						array( 'value' => '7:15 AM', 'name' => '7:15 AM' ),
						array( 'value' => '7:30 AM', 'name' => '7:30 AM' ),
						array( 'value' => '7:45 AM', 'name' => '7:45 AM' ),

						array( 'value' => '8:00 AM', 'name' => '8:00 AM' ),
						array( 'value' => '8:15 AM', 'name' => '8:15 AM' ),
						array( 'value' => '8:30 AM', 'name' => '8:30 AM' ),
						array( 'value' => '8:45 AM', 'name' => '8:45 AM' ),

						array( 'value' => '9:00 AM', 'name' => '9:00 AM' ),
						array( 'value' => '9:15 AM', 'name' => '9:15 AM' ),
						array( 'value' => '9:30 AM', 'name' => '9:30 AM' ),
						array( 'value' => '9:45 AM', 'name' => '9:45 AM' ),

						array( 'value' => '10:00 AM', 'name' => '10:00 AM' ),
						array( 'value' => '10:15 AM', 'name' => '10:15 AM' ),
						array( 'value' => '10:30 AM', 'name' => '10:30 AM' ),
						array( 'value' => '10:45 AM', 'name' => '10:45 AM' ),

						array( 'value' => '11:00 AM', 'name' => '11:00 AM' ),
						array( 'value' => '11:15 AM', 'name' => '11:15 AM' ),
						array( 'value' => '11:30 AM', 'name' => '11:30 AM' ),
						array( 'value' => '11:45 AM', 'name' => '11:45 AM' ),

						array( 'value' => '12:00 PM', 'name' => '12:00 PM' ),
						array( 'value' => '12:15 PM', 'name' => '12:15 PM' ),
						array( 'value' => '12:30 PM', 'name' => '12:30 PM' ),
						array( 'value' => '12:45 PM', 'name' => '12:45 PM' ),

						array( 'value' => '1:00 PM', 'name' => '1:00 PM' ),
						array( 'value' => '1:15 PM', 'name' => '1:15 PM' ),
						array( 'value' => '1:30 PM', 'name' => '1:30 PM' ),
						array( 'value' => '1:45 PM', 'name' => '1:45 PM' ),

						array( 'value' => '2:00 PM', 'name' => '2:00 PM' ),
						array( 'value' => '2:15 PM', 'name' => '2:15 PM' ),
						array( 'value' => '2:30 PM', 'name' => '2:30 PM' ),
						array( 'value' => '2:45 PM', 'name' => '2:45 PM' ),

						array( 'value' => '3:00 PM', 'name' => '3:00 PM' ),
						array( 'value' => '3:15 PM', 'name' => '3:15 PM' ),
						array( 'value' => '3:30 PM', 'name' => '3:30 PM' ),
						array( 'value' => '3:45 PM', 'name' => '3:45 PM' ),

						array( 'value' => '4:00 PM', 'name' => '4:00 PM' ),
						array( 'value' => '4:15 PM', 'name' => '4:15 PM' ),
						array( 'value' => '4:30 PM', 'name' => '4:30 PM' ),
						array( 'value' => '4:45 PM', 'name' => '4:45 PM' ),

						array( 'value' => '5:00 PM', 'name' => '5:00 PM' ),
						array( 'value' => '5:15 PM', 'name' => '5:15 PM' ),
						array( 'value' => '5:30 PM', 'name' => '5:30 PM' ),
						array( 'value' => '5:45 PM', 'name' => '5:45 PM' ),

						array( 'value' => '6:00 PM', 'name' => '6:00 PM' ),
						array( 'value' => '6:15 PM', 'name' => '6:15 PM' ),
						array( 'value' => '6:30 PM', 'name' => '6:30 PM' ),
						array( 'value' => '6:45 PM', 'name' => '6:45 PM' ),

						array( 'value' => '7:00 PM', 'name' => '7:00 PM' ),
						array( 'value' => '7:15 PM', 'name' => '7:15 PM' ),
						array( 'value' => '7:30 PM', 'name' => '7:30 PM' ),
						array( 'value' => '7:45 PM', 'name' => '7:45 PM' ),

						array( 'value' => '8:00 PM', 'name' => '8:00 PM' ),
						array( 'value' => '8:15 PM', 'name' => '8:15 PM' ),
						array( 'value' => '8:30 PM', 'name' => '8:30 PM' ),
						array( 'value' => '8:45 PM', 'name' => '8:45 PM' ),

						array( 'value' => '9:00 PM', 'name' => '9:00 PM' ),
						array( 'value' => '9:15 PM', 'name' => '9:15 PM' ),
						array( 'value' => '9:30 PM', 'name' => '9:30 PM' ),
						array( 'value' => '9:45 PM', 'name' => '9:45 PM' ),

						array( 'value' => '10:00 PM', 'name' => '10:00 PM' ),
						array( 'value' => '10:15 PM', 'name' => '10:15 PM' ),
						array( 'value' => '10:30 PM', 'name' => '10:30 PM' ),
						array( 'value' => '10:45 PM', 'name' => '10:45 PM' ),

						array( 'value' => '11:00 PM', 'name' => '11:00 PM' ),
						array( 'value' => '11:15 PM', 'name' => '11:15 PM' ),
						array( 'value' => '11:30 PM', 'name' => '11:30 PM' ),
						array( 'value' => '11:45 PM', 'name' => '11:45 PM' ),
					);
				}

				// Input Wrap
				echo '<div class="box-row-input">';

					// Label
					echo '<div class="box-tc box-tc-label">';
						if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
							echo '<label for="' . esc_attr( self::$_option['id'][0]['id'] ) . '" >' . esc_attr( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_attr( self::$_option['sub_name'] ) . '</div>';
						}


						echo '<select name="' . esc_attr( self::$_option['id'][0]['id'] ) . '" id="' . esc_attr( self::$_option['id'][0]['id'] ) . '" size="1"  class="timepicker-input time-start">';
							if (isset( $time_a ) ) {
								foreach ( $time_a as $option ) {
									if ( isset( self::$_option['id'][0]['std'] ) && self::$_option['id'][0]['std'] === $option['value'] ) $selected = 'selected';
									else $selected = '';
									echo "<option " . esc_attr( $selected ) ." value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
								}
							}
						echo '</select>';

						echo '<span class="date-separator"></span>';


						echo '<select name="' . esc_attr( self::$_option['id'][1]['id'] ) . '" id="' . esc_attr( self::$_option['id'][1]['id'] ) . '" size="1"  class="timepicker-input time-end">';
							if (isset( $time_a ) ) {
								foreach ( $time_a as $option ) {
									if ( isset( self::$_option['id'][1]['std'] ) && self::$_option['id'][1]['std'] === $option['value'] ) $selected = 'selected';
									else $selected = '';
									echo "<option " . esc_attr( $selected ) ." value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
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