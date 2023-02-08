<?php
/**
 * Background Generator Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}
if ( ! class_exists( 'RascalsBox_bg_generator' ) ) {

	class RascalsBox_bg_generator extends RascalsBox {

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

	            /* BG Editor */
				add_action( 'wp_ajax_bg_editor', array( $this, 'bg_editor' ) );        
	            
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
			
			// Admin Footer
			add_action( 'admin_footer', array( $this, 'admin_footer' ) );

			wp_enqueue_script( 'wp-color-picker' );
	    	wp_enqueue_style( 'wp-color-picker' );

	    	$path = self::$_args['admin_url'] . '/includes';

			// Load script
			$handle = self::$_option['type'] . '.js';
			if ( ! wp_script_is( $handle, 'enqueued' ) ) {
				wp_enqueue_script( $handle, $path . '/metabox-fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.js', false, false, true );
			}
			
		}


		/**
         * Render HTML code in admin footer
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function admin_footer() {
			$this->bg_box();
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
			
			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="' . esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'] ) . '">';
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

						// Field
						// ---------------------------------------
				
						echo '<div class="bg-holder-wrap" data-add-label="' . esc_attr__( 'Add Background', 'epron-toolkit' ) . '" data-edit-label="' . esc_attr__( 'Edit Background', 'epron-toolkit' ) . '">';
						echo '<textarea id="' . esc_attr( self::$_option['id'] ) . '" name="' . esc_attr( self::$_option['id'] ) . '" class="bg-holder">' . esc_attr( self::$_option['std'] ) . '</textarea>';
						echo '</div>';

						if ( !isset( self::$_option['std'] ) || self::$_option['std'] === '' ) {

							echo '<div class="image-holder bg-image-holder" style="display:none"></div>';
							echo '<button class="_button generate-bg" ><i class="fa icon fa-magic"></i>' . esc_html__( 'Add Background', 'epron-toolkit' ) . '</button>';
							echo '<button class="_button ui-button-delete delete-bg" style="display:none"><i class="fa icon fa-trash-o"></i>' . esc_html__( 'Remove', 'epron-toolkit' ) . '</button>';
						} else {
							
							$data = stripslashes( self::$_option['std'] );
							$preview = '';
							$messages = '';
							if ( json_decode( $data ) !== null ) {
								
								$data = json_decode( $data, true );
								
								if ( isset( $data['image'] ) ) {
									$image = wp_get_attachment_image_src( $data['image'], 'thumbnail' );
									$image = $image[0];
									// If image exists
									if ( $image ) {
										$preview .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Preview Image', 'epron-toolkit' ) . '">';
									}
								} 
								if ( isset( $data['color'] ) ) {
									$preview .= '<div class="preview-color-holder" style="background-color:' . esc_attr( $data['color'] ) . ';"></div>';

								}
							} else {

								$messages = '<div class="msg msg-warning">' . esc_html__( 'Set your background again, because current is not compatible with latest framework version.', 'epron-toolkit' ) . '</div>';
							}
							echo '<div class="image-holder bg-image-holder">';
							$this->e_esc( $preview );
							echo '</div>';
							$this->e_esc( $messages );
							echo '<button class="_button generate-bg" ><i class="fa icon fa-magic"></i>' . esc_html__( 'Edit Background', 'epron-toolkit' ) . '</button>';
							echo '<button class="_button ui-button-delete delete-bg"><i class="fa icon fa-trash-o"></i>' . esc_html__( 'Remove', 'epron-toolkit' ) . '</button>';
						}

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


		/* Background Box
		---------------------------------------------- */
		private function bg_box() {

			echo '<div id="bg-editor-box" style="display:none">';
		    echo '<input type="hidden" autofocus="autofocus" />';
			echo '<div id="bg-editor-content" class="rascalsbox">';

			echo '</div>';
		    echo '</div>';

		}


		/* Background editor
		---------------------------------------------- */
		public function bg_editor() {

			$data = $_POST['data'];


			$defaults = array(
				'color' => 'transparent',
			);
			if ( isset( $data ) ) {
				$data = stripslashes( $data );
				
				if ( json_decode( $data ) !== null ) {
					
					$data = json_decode( $data, true );

				}
			}

			/* Set default options */
			if ( isset( $data ) && is_array( $data) ) {
				$data = array_merge( $defaults, $data );
			} else { 
				$data = $defaults;
			}

			$output .= '<fieldset>';
			

			/* Color ---------------------------------- */
			$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="color">' . esc_html__( 'Background Color', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<input type="hidden" id="color_picker_hidden" name="color" value="transparent"/>
						<input type="text" id="color_picker" name="color" value="' . esc_attr( $data['color'] ) . '"/>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';


			/* Image ---------------------------------- */
			$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="bg-image">' . esc_html__( 'Background Image', 'epron-toolkit' ) . '</label>
					</div>

					<div class="box-tc box-tc-input">
						<input type="hidden" id="bg-image" name="image" value="' . esc_attr( $data['image'] ) . '" />';

						$image = wp_get_attachment_image_src( $data['image'], 'thumbnail' );
						$image = $image[0];
						// If image exists
						if ( $image ) {
							$image_html = '<img src="' . esc_attr( $image ) . '" alt="' . esc_attr__( 'Preview Image', 'epron-toolkit'  ) . '">';
							$is_image = 'is_image'; 
						} else {
							$image_html = '';
							$is_image = ''; 
						}

						$output .= '<div class="image-holder image-holder-cover ' . esc_attr( $is_image ) . '" data-placeholder="' . esc_attr( self::$_args[ 'admin_path' ] ) . '/assets/images/metabox/audio.png">';

						// Image
						$output .=  $image_html;

						// Button
						$output .= '<button class="upload-image"><i class="fa icon fa-plus"></i></button>';

						/* Remove image */
						$output .= '<a class="remove-image"><i class="fa icon fa-remove"></i></a>';

					$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="box-row-line"></div>';
			$output .= '</div>';


			/* Position ---------------------------------- */
			$output .= '<div class="box-row clearfix">';
				$output .= '<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="position">' . esc_html__( 'Background Position', 'epron-toolkit' ) . '</label>
						</div>

					<div class="box-tc box-tc-input">';
						$output .= '<select name="position" id="position" size="1">';
						$select_options = array(
										 array( 'name' => 'left top', 'value' => 'left top' ),
										 array( 'name' => 'left center', 'value' => 'left center' ),
										 array( 'name' => 'left bottom', 'value' => 'left bottom' ),
										 array( 'name' => 'right top', 'value' => 'right top' ),
										 array( 'name' => 'right center', 'value' => 'right center' ),
										 array( 'name' => 'right bottom', 'value' => 'right bottom' ),
										 array( 'name' => 'center top', 'value' => 'center top' ),
										 array( 'name' => 'center center', 'value' => 'center center' ),
										 array( 'name' => 'center bottom', 'value' => 'center bottom' ),
										 );
						foreach ( $select_options as $option ) {
							
							if ( $data['position'] === $option['value'] ) 
								$selected = 'selected';
							else 
								$selected = '';
							$output .= "<option ".esc_attr( $selected )." value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
						}
						$output .= '</select>';


						$output .= '<p class="help-box">' . esc_html__( 'The background-position property sets the starting position of a background image. The first value is the horizontal position and the second value is the vertical. The top left corner is 0 0.', 'epron-toolkit' ) . '</p>';
						unset( $select_options );
					$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="box-row-line"></div>';
			$output .= '</div>';


			/* Repeat ---------------------------------- */
			$output .= '<div class="box-row clearfix">';
				$output .= '<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="repeat">' . esc_html__( 'Background Repeat', 'epron-toolkit' ) . '</label>
						</div>

					<div class="box-tc box-tc-input">';
						$output .= '<select name="repeat" id="repeat" size="1">';
						$select_options = array(
							array( 'name' => 'no-repeat', 'value' => 'no-repeat' ),
							array( 'name' => 'repeat', 'value' => 'repeat' ),
							array( 'name' => 'repeat-x', 'value' => 'repeat-x' ),
							array( 'name' => 'repeat-y', 'value' => 'repeat-y' )
						);
						foreach ( $select_options as $option ) {
							
							if ( $data['repeat'] === $option['value'] ) 
								$selected = 'selected';
							else 
								$selected = '';
							$output .= "<option ".esc_attr( $selected )." value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
						}
						$output .= '</select>';


						$output .= '<p class="help-box">' . esc_html__( 'The background-repeat property sets if/how a background image will be repeated.', 'epron-toolkit' ) . '</p>';
						unset( $select_options );
					$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="box-row-line"></div>';
			$output .= '</div>';


			/* Attachment ---------------------------------- */

			$output .= '<div class="box-row clearfix">';
				$output .= '<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="attachment">' . esc_html__( 'Background Attachment', 'epron-toolkit' ) . '</label>
						</div>

					<div class="box-tc box-tc-input">';
						$output .= '<select name="attachment" id="attachment" size="1">';
						$select_options = array(
							array( 'name' => 'scroll', 'value' => 'scroll' ),
							array( 'name' => 'fixed', 'value' => 'fixed' )
						);
						foreach ( $select_options as $option ) {
							
							if ( $data['attachment'] === $option['value'] ) 
								$selected = 'selected';
							else 
								$selected = '';
							$output .= "<option ".esc_attr( $selected )." value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
						}
						$output .= '</select>';


						$output .= '<p class="help-box">' . esc_html__( 'The background-attachment property sets whether a background image is fixed or scrolls with the rest of the page.', 'epron-toolkit' ) . '</p>';
						unset( $select_options );
					$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="box-row-line"></div>';
			$output .= '</div>';


			/* Size ---------------------------------- */

			$output .= '<div class="box-row clearfix">';
				$output .= '<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="size">' . esc_html__( 'Background Attachment', 'epron-toolkit' ) . '</label>
						</div>

					<div class="box-tc box-tc-input">';
						$output .= '<select name="size" id="size" size="1">';
						$select_options = array(
							array( 'name' => 'auto', 'value' => 'auto' ),
							array( 'name' => 'cover', 'value' => 'cover' ),
							array( 'name' => 'contain', 'value' => 'contain' )
						);
						foreach ( $select_options as $option ) {
							
							if ( $data['size'] === $option['value'] ) 
								$selected = 'selected';
							else 
								$selected = '';
							$output .= "<option " . esc_attr( $selected ) . " value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
						}
						$output .= '</select>';


						$output .= '<p class="help-box">' . esc_html__( 'The background-size property specifies the size of the background images.', 'epron-toolkit' ) . '</p>';
						unset( $select_options );
					$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="box-row-line"></div>';
			$output .= '</div>';


			$output .= '</fieldset>';

			$this->e_esc( $output );
		    exit;

		}


	}
}