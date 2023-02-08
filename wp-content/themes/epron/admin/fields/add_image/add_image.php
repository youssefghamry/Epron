<?php

/**
 * Rascals Theme Panel Class
 *
 * @package     RascalsThemePanel
 * @subpackage  add_image
 * @author      Mariusz Rek
 * @version     2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'RascalsThemePanel_add_image' ) ) {

	class RascalsThemePanel_add_image extends RascalsThemePanel {

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

			if ( isset( self::$_saved_options[self::$_option['id']] ) ) {
				self::$_option['std'] = self::$_saved_options[self::$_option['id']];
			}

			if ( ! isset( self::$_option['std'] ) ) {
				self::$_option['std'] = '';
			}
			
			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'] ) . '">';
			} else {
				echo '<div class="box-row clearfix">';
			}

			// Variables
			$media_libary = '';
			$external_link = '';
			$holder_classes = '';


			// Source
			if ( isset( self::$_option['source'] ) && self::$_option['source'] === 'all' ) {
				$source = 'all';
			} elseif ( isset( self::$_option['source'] ) && self::$_option['source'] === 'media_libary' ){
				$source = 'media_libary';
				$input_type = 'hidden';
			} elseif ( isset( self::$_option['source'] ) && self::$_option['source'] === 'external_link' ){
				$source = 'external_link';
				$input_type = 'text';
				$holder_classes .= ' hidden';
			} else {
				$source = 'all';
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

					if ( $source === 'all' ) {

						if ( is_numeric( self::$_option['std'] ) || self::$_option['std'] === '' ) {
							$media_libary = 'selected';
							$input_type = 'hidden';
						} else {
							$external_link = 'selected';
							$input_type = 'text';
							$holder_classes .= ' hidden';
						}

						echo '<select size="1" class="image-source-select" >';

							echo "<option ". esc_attr( $media_libary ) . " value='media_libary'>" . esc_html__( 'Media libary', 'epron' ) . "</option>";
							echo "<option " . esc_attr( $external_link ) . " value='external_link'>" . esc_html__( 'External link', 'epron' ) . "</option>";
						
						echo '</select>';

					}

					// Input
					echo '<input type="' . esc_attr( $input_type ) . '" value="' . esc_attr( self::$_option['std'] ) . '" id="' . esc_attr( self::$_option['id'] ) . '" name="' . esc_attr( self::$_option['id'] ) . '" class="image-input"/>';

					/* Image preview */
					
					// Get image data
					$image = wp_get_attachment_image_src( self::$_option['std'], 'thumbnail' );
					$image = $image[0];
					if ( $image && strpos( $image,  'kingcomposer/assets/images/get_start.jpg' ) === false ) {
						$holder_classes .= ' is_image';
					}

					echo '<div class="image-holder ' . esc_attr(  $holder_classes ) . '">';

						// Image
						// If image exists
						if ( $image && strpos( $image,  'kingcomposer/assets/images/get_start.jpg' ) === false ) {
							echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Preview Image', 'epron' ) . '">';
						} 

						// Button
						echo '<button class="upload-image"><i class="fa icon fa-plus"></i></button>';

						/* Remove image */
						echo '<a class="remove-image"><i class="fa icon fa-remove"></i></a>';
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