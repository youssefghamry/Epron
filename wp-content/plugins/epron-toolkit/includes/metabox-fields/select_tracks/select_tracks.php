<?php
/**
 * Select Tracks Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}
if ( ! class_exists( 'RascalsBox_select_tracks' ) ) {

	class RascalsBox_select_tracks extends RascalsBox {

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
	            add_action( 'wp_ajax_get_tracks_ajax', array( $this, 'get_tracks_ajax' ) );            
	            
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
			
			$path = self::$_args['admin_url'] . '/includes';

			// Load script
			$handle = self::$_option['type'] . '.js';
			if ( ! wp_script_is( $handle, 'enqueued' ) ) {
				wp_enqueue_script( $handle, $path . '/metabox-fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.js', false, false, true );
			}

			// Load style
			$handle_css = self::$_option['type'] . '.css';
			if ( ! wp_style_is( $handle, 'enqueued' ) ) {
				wp_enqueue_style( $handle, $path . '/metabox-fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.css' );
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

			if ( isset( self::$_option['id'][0]['id'] ) ) {
				$track_id = self::$_option['id'][0]['id'];
				$track_std = self::$_option['id'][0]['std'];
			} else {
				return esc_html__( 'Error: ID: Is not defined!', 'epron-toolkit' );
			}
			if ( isset( self::$_option['id'][1]['id'] ) ) {
				$tracks_ids = self::$_option['id'][1]['id'];
				$tracks_std = self::$_option['id'][1]['std'];
			} else {
				return esc_html__( 'Error: ID: Is not defined!', 'epron-toolkit' );
			}

			/* Set defaults options */
			if ( isset( self::$_saved_options[$track_id] ) ) {
				$track_std = self::$_saved_options[$track_id];
			}
			if ( isset( self::$_saved_options[$tracks_ids] ) ) {
				$tracks_std = self::$_saved_options[$tracks_ids];
			}
			
			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( $track_id ) . '">';
			} else {
				echo '<div class="box-row clearfix">';
			}

				// Input Wrap
				echo '<div class="box-row-input tracks-select-block">';

					// Label
					echo '<div class="box-tc box-tc-label">';
						if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
							echo '<label for="' . esc_attr( $track_id ) . '" >' . esc_html( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_attr( self::$_option['sub_name'] ) . '</div>';
						}


						echo '<div class="track-id-block">'; 

						// Field
						// ---------------------------------------
						echo '<select name="' . esc_attr( $track_id ) . '" id="' . esc_attr( $track_id ) . '" size="1"  class="box-select track-id">';
							if (isset( self::$_option['options'] ) ) {
								foreach ( self::$_option['options'] as $option ) {
									if ( isset( $track_std ) && $track_std === $option['value'] ) {
										$selected = 'selected';
									}
									else {
										$selected = '';
									}
									echo "<option " . esc_attr( $selected ) ." value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
								}
							}
						echo '</select>';
						/* Ajax loader */
						echo '<img class="ajax-loader" src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" alt="Loading..." />';
						echo '</div>';

						// Hidden Field
						// ---------------------------------------
						echo '<input name="' . esc_attr( $tracks_ids ) . '" id="' . esc_attr( $tracks_ids ) . '" type="hidden" value="' . esc_html( $tracks_std ) . '" class="text-input tracks-ids"/>';

						// Tracklist preview
						// ---------------------------------------
						echo '<div class="box-tp-block">';
							if ( function_exists( 'epronToolkit' ) ) {
								$toolkit = epronToolkit();
								
								if ( $track_std !== 'none' && $toolkit->scamp_player !== null ) {

									$tracklist = $toolkit->scamp_player->getTracklist( $track_std, $tracks_std );
									$this->e_esc( $this->tracks_render( $tracklist ) );
								}

							}
						echo '</div>';

						if ( $track_std !== 'none' ) {
							echo '<a href="' . admin_url( 'post.php?post=' . esc_attr( $track_std ) . '&action=edit' ) . '" class="edit-track-post" target="_blank">' . esc_html__( 'Edit original tracklist here.', 'epron-toolkit' ) . '</a>';
						}

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


		/* Get Tracks Ajax
		---------------------------------------------- */
		public function get_tracks_ajax() {
			
			$id = $_POST['id'];
			
			if ( ! isset( $_POST['id'] ) || ! function_exists( 'epronToolkit' ) ) {
				die();
				return false;
			}

			$toolkit = epronToolkit();
			$tracklist = $toolkit->scamp_player->getTracklist( $id );

			$output = $this->tracks_render( $tracklist );

			$output .= '<a href="' . admin_url( 'post.php?post=' . esc_attr( $id ) . '&action=edit' ) . '" class="edit-track-post" target="_blank">' . esc_html__( 'Edit original tracklist here.', 'epron-toolkit' ) . '</a>';

		    $this->e_esc( $output );
		    exit;
		}


		/* Tracks Render
		---------------------------------------------- */
		public function tracks_render( $tracks = array() ) {
			$output = '';
			if ( is_array( $tracks ) && ! empty( $tracks ) ) {
				foreach ( $tracks as $track ) {
			       	if ( ! $track['cover'] || $track['cover'] === '' ) {
			            $track['cover'] = get_template_directory_uri() . '/images/no-track-image.png';
			        }
			        $output .= '
			        <div class="track-item" data-id="' . esc_attr( $track['id'] ) . '">
			        	<div class="cover-img"><img src="' . esc_url( $track['cover'] ) . '" alt="Track cover image" ></div>
			            <div class="track-details">
			                <span class="track-title">' . $this->esc( $track['title'] ) . '</span>
			                <span class="artists">' . $this->esc( $track['artists'] ) . '</span>
			            </div>
			            <a href="#" class="remove-track" title="' . esc_html__( 'Remove Track', 'epron-toolkit' ) . '"></a>
			        </div>';
			    }

			    return $output;
			}

		}
	}
}