<?php
/**
 * Media Manager Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}
if ( ! class_exists( 'RascalsBox_media_manager' ) ) {

	class RascalsBox_media_manager extends RascalsBox {

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

	           	/* Media Manager - Get data of single item */
				add_action( 'wp_ajax_mm_editor', array( $this, 'mm_editor') );

				/* Media Manager - Save data of single item */
				add_action( 'wp_ajax_mm_editor_save', array( $this, 'mm_editor_save') );

				/* Media Manager - Actions */
				add_action( 'wp_ajax_mm_actions', array( $this, 'mm_actions') );       
	            
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
         * Render HTML code in admin footer
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function admin_footer() {
			$this->mm_explorer_box();
			$this->mm_editor_box();
		}

		
		/**
         * Field Render Function.
         * Takes the vars and outputs the HTML
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function render() {

			global $post;


			if ( isset( self::$_saved_options[self::$_option['id']] ) ) {
				self::$_option['std'] = self::$_saved_options[self::$_option['id']];
			}

			$def_img_src = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
			
			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden mm-box-row" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="'.esc_attr( implode(',', self::$_option['dependency']['value'] ) ).'" data-id="' . esc_attr( self::$_option['id'] ) . '">';
			} else {

				echo '<div class="box-row clearfix mm-box-row">';
			}

				// Input Wrap
				// 
				if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {
					echo '<div class="box-row-input">';
				} else {
					echo '<div class="box-row-input fullwidth">';
				}
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
						if ( isset( self::$_option['layout'] ) ) {
							$layout = self::$_option['layout'];
						} else {
							$layout = 'grid';
						}

						/* Muttleybox Sortable list */
						echo '<div class="mb-sortable-list" data-default-img="' . esc_attr( $def_img_src ) . '">';
						echo '<div class="mm-block mm-'. esc_attr( $layout ) .' mm-'. esc_attr( self::$_option['media_type'] ) .'">';
						// Field
						// ---------------------------------------
						
						if ( ! isset( self::$_option['std'] ) || self::$_option['std'] === '') {
							$no_images = 'block';
						} else {
							$no_images = 'none';
						}

						/* Select All Items */
						echo '<a title="' . esc_html__( 'Select All', 'epron-toolkit' ) . '" class="mm-select-all"><i class="fa fa-check-square"></i></a>';
						echo '<div class="clear"></div>';
						
						/* Message */
						echo '<div class="msg-dotted" style="display:' . esc_attr( $no_images ) . '">' . wp_kses_post( self::$_option['msg_text'] ) . '</div>';

						/* Settings */
						echo '<span class="mm-settings mm-hidden" data-post-id="' . esc_attr( $post->ID ) . '" data-mm-id="' . esc_attr( self::$_option['id'] ) . '" data-mm-type="' . esc_attr( self::$_option['media_type'] ) . '" data-mm-layout="' . esc_attr( self::$_option['layout'] ) . '" data-mm-admin-path="' . esc_url( self::$_args[ 'admin_path' ] ) . '"></span>';

						/*  Hidden input */
						echo '<input type="hidden" value="' . esc_attr( self::$_option['std'] ) . '" id="' . esc_attr( self::$_option['id'] ) . '" name="' . esc_attr( self::$_option['id'] ) . '" class="mm-ids"/>';

						if ( self::$_option['layout'] === 'list' ) {

							/* Head list TPL
							 -------------------------------- */
							$default_item = array();
							$default_item['cover'] = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';

							$fields = $this->render_fields( self::$_option['display_fields'], $default_item, true );
							$this->e_esc( $this->list_head_tpl( $fields ) );
							
						}
						/* Container */
						echo '<div class="mm-container">';

						/* Preview Items */
						if ( isset( self::$_option['std'] ) && self::$_option['std'] !== '' ) {

							$items = explode('|', self::$_option['std'] );

							foreach( $items as $index => $id ) {

								/* Grid */
								if ( self::$_option['layout'] === 'grid' ) {

									$image = wp_get_attachment_image_src( $id );

									if ( $image ) {
										$item = get_post( $id );
										$meta = wp_get_attachment_metadata( $id );
										if ( is_array( $meta ) ) {
											$meta_html = esc_html( basename( $item->guid ) ) . ' - ' . $meta['width'] . 'x' . $meta['height'];
										} else {
											$meta_html = '';
										}
										echo '
										<a class="mm-item mm-image" id="' . esc_attr( $id ) . '" title="' . esc_attr( $meta_html ) . '">
											<div class="mm-item-preview">
										    	<div class="mm-item-image">
										    		<div class="mm-centered">
										    			<img src="' . esc_url( $image[0] ) . '" />
										    		</div>
										    	</div>
											</div>
											<span class="mm-edit-button"><i class="fa fa-gear"></i></span>
										</a>';
									} else {
										echo '
										<a class="mm-item mm-image" id="' . esc_attr( $id ) . '">
											<div class="mm-item-preview">
										    	<div class="mm-filename"><div>' . esc_html__( 'Error: Image file doesn\'t exists.', 'epron-toolkit' ) . '</div></div>
											</div>
										</a>';
									}
								}

								/* List */
								if ( self::$_option['layout'] === 'list' ) {
									
									/* If custom id */

									if ( is_numeric($id) ) {
										$list_item = get_post( $id );
									} else {
										$list_item = false;
									}
									$item = false;

									if ( $list_item ) {

										/* This is not custom audio */
										$item = get_post_meta( $post->ID, self::$_option['id'] . '_' . $id, true );
										if ( ! isset( $item['title'] ) ) {
											$item = array();
											$item['title'] = $list_item->post_title;
										}
										$list_item_filename = $list_item->guid;
									} else {

										$item = get_post_meta( $post->ID, self::$_option['id'] . '_' . $id, true );

										/////////
										// MOD //
										/////////
										$list_item = true;

										/* Check custom track */
										if ( isset( $item['custom_url'] ) ) {
											$list_item = true;
											$list_item_filename = $item['custom_url'];
										} else {
											$list_item_filename = '';
										}
									}


									if ( $list_item ) {

										$image = '';
										$src = '';

										// If image exists
										if ( isset( $item[ 'image' ] ) ) {

											if (is_numeric( $item['image'] ) ) {
												$image = wp_get_attachment_image_src( $item['image'], 'thumbnail' );
												$src = $image[0];
											} elseif ( isset( $item[ 'image' ] ) && ! is_numeric( $item['image'] ) && $item['image'] !== '' ) {
												$src = $item['image'];
											}
											if ( $src === false || $src === null || $src === '' ) {
												$src = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
											}
											$item['image'] = $src;
										}
										
										
										if ( self::$_option['media_type'] === 'audio' ) {
											$image = '';
											$src = '';


											// If image exists
											if ( isset( $item[ 'cover' ] )	&& is_numeric( $item['cover'] ) ) {
												$image = wp_get_attachment_image_src( $item['cover'], 'thumbnail' );
												$src = $image[0];
											} elseif ( isset( $item[ 'cover' ] ) && ! is_numeric( $item['cover'] ) && $item['cover'] !== '' ) {
												$src = $item['cover'];
											}
											if ( $src === false || $src === null || $src === '' ) {
												$src = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
											}
											if ( isset( $item['cover'] ) ) {
												$item['cover'] = $src;
											}
											
										}
											
										/* Display list item
										 -------------------------------- */
										$fields = $this->render_fields( self::$_option['display_fields'], $item );
										$this->e_esc( $this->list_tpl( $id, $fields ) );

									} 
								}
						    }	
						}
						echo '</div>';

						/* Hidden TPL
						 -------------------------------- */
						echo '<div class="mm-list-tpl">';
							$default_item = array();
							$default_item['cover'] = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
							$default_item['image'] = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';

							$fields = $this->render_fields( self::$_option['display_fields'], $default_item );
							$this->e_esc( $this->list_tpl( '', $fields ) );
						echo '</div>';

						/* Messages
						 -------------------------------- */
						echo '<p class="msg msg-error" style="display:none;">' . esc_html__( 'Error: AJAX Transport', 'epron-toolkit') . '</p>';

						/* Buttons
						 -------------------------------- */

						if ( isset( self::$_option['buttons'] ) && is_array( self::$_option['buttons'] ) ) {

						 	foreach ( self::$_option['buttons'] as $i => $button ) {
						 		echo '<button class="_button _button-' . esc_attr( $button['color'] ) . ' mm-' . esc_attr( $button['type'] ) . '" title="' . esc_attr( $button['title'] ) . '">' . esc_html( $button['label'] ) . '</button>';
						 		
						 	}
						} 

						/* Delete */
						echo '<button class="_button _button-red mm-delete-button" title="' . esc_html__( 'Remove Selected Items', 'epron-toolkit' ) . '" style="display:none">' . esc_html__( 'Remove Selected', 'epron-toolkit' ) . '</button>';

						/* Ajax loader */
						echo '<img class="mm-ajax" src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" alt="Loading..." />';


						echo '</div>'; //block

						echo '</div>'; //sortable list
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

		/* Fields helpers
		---------------------------------------------- */
		function render_fields( $display_fields, $data_fields, $title = false  ) { 
			
			$field = '';
			if ( isset( $display_fields ) && is_array( $display_fields ) && is_array( $data_fields ) ) {
									
			 	foreach ( $display_fields as $i => $f ) {
			 		if ( isset( $data_fields[ $f['name'] ] ) ) {
			 			$new_field = $data_fields[ $f['name'] ];
			 		} elseif ( isset($f['std']) ) {
			 			$new_field = $f['std'];
			 		}

			 		if ( ! isset($f['classes'] )) {
			 			$f['classes']  = '';
			 		}
			 		if ( $title ) {
			 			$new_field = $f['title'];
			 		}

			 		if ( $f['type'] === 'image' ) {
			 			$field .= '<div class="mm-field mm-field-' . esc_attr( $f['type'] ) . '">';
			 			if ( ! $title ) {
				 			if (  ! isset($new_field )) {
				 				$new_field = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
				 			}
				 			$field .= '<img class="mm-update-field ' . esc_attr( $f['classes'] ) . '" data-field="' . esc_attr( $f['name'] ) . '" data-field-type="image" src="' . esc_url( $new_field ) . '" alt="Track cover image" >';
			 			} else {
			 				$field .= esc_html( $new_field );
			 			}
			 			$field .= '</div>';

			 		} elseif ( $f['type'] === 'text' ) {
			 			$field .= '<div class="mm-field mm-field-' . esc_attr( $f['name'] ) . ' mm-field-' . esc_attr( $f['type'] ) . ' mm-update-field" data-field="' . esc_attr( $f['name'] ) . '" data-field-type="text">' . wp_kses_post( $new_field ) . '</div>';
				 	} elseif ( $f['type'] === 'post_name' ) {
				 		if ( ! $title ) {
				 			$t = get_the_title($new_field);
				 		} else {
				 			$t = $new_field;
				 		}
			 			$field .= '<div class="mm-field mm-field-' . esc_attr( $f['name'] ) . ' mm-field-' . esc_attr( $f['type'] ) . ' mm-update-field" data-field="' . esc_attr( $f['name'] ) . '" data-field-type="post_name">' . wp_kses_post( $t ) . '</div>';
			 		}
				 }	
			} 

			return $field;
		}


		/* List item template */
		function list_tpl( $id, $fields ) {

			return	'<div class="mm-item" id="' . esc_attr( $id ) . '">
						<div class="mm-item-container">
						' . wp_kses_post( $fields ) . '
						<div class="mm-field mm-field-buttons">
				            <a title="' . esc_html__( 'Save', 'epron-toolkit' ) . '" class="mm-save-button"><i class="fa fa-save"></i></a>
				            <a title="' . esc_html__( 'Edit/Close', 'epron-toolkit' ) . '" class="mm-edit-button"><i class="fa fa-gear"></i></a>
				            <a title="' . esc_html__( 'Select', 'epron-toolkit' ) . '" class="mm-select-button"><i class="fa fa-check-square"></i></a>
			            </div>
		            </div>
				</div>';
		}

		/* List head template */
		function list_head_tpl($fields ) {

			return	'<div class="mm-item mm-head">
						<div class="mm-item-container">
						' . wp_kses_post( $fields ) . '
						<div class="mm-field mm-field-buttons"></div>
		            </div>
				</div>';
		}


		/* Ajax Actions
		---------------------------------------------- */

		/* Save item data */
		function mm_editor_save() {
			
			/* Variables */
			$fields = $_POST['fields'];
			$update_fields = $_POST['update_fields'];
			$settings = $_POST['settings'];
			$id = $_POST['item_id'];
			$output = '';
			$response = 'success';

			/* Update fileds  */

			if ( isset( $update_fields ) && $settings['mm_layout'] === 'list' ) {
				foreach ( $update_fields as $i => $field ) {
					$f = $field['name'];

					
					if ( isset($fields[$f]) && $fields[$f] !== '' ) {
						if ( $field['type'] === 'image' ) {

							if ( is_numeric( $fields[$f] ) ) {
								$image_cover = wp_get_attachment_image_src( $fields[$f], 'thumbnail' );
								$image_cover = $image_cover[0];
								if ( $image_cover ) {
									$update_fields[$i]['val'] = $image_cover;
								} else {
									$update_fields[$i]['val'] = self::$_args['admin_url'] . '/assets/images/metabox/audio.png';
								}
							} else {
								$update_fields[$i]['val'] = $fields[$f];
							}
						} else {
							$update_fields[$i]['val'] = $fields[$f];
						}
					} else if ( $field['type'] === 'image' ) {
						$update_fields[$i]['val'] = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
					}
				}

				$response = json_encode( $update_fields );

			}

			$option_name = $settings['mm_id'] . '_' . $id;
			$options = get_post_meta($settings['post_id'], $option_name , true);
			
			if ( ! isset( $fields ) && is_array( $fields ) || ! isset( $settings ) ) {
				die();
			}
			
			update_post_meta( $settings['post_id'], $option_name, $fields );
		    $this->e_esc( $response );
		   exit;
		}

		/* Media Manager - Ajax Actions */
		function mm_actions() {
			
			$action = $_POST['mm_action'];
			$output = '';

			if ( ! isset( $_POST['action'] ) ) {
				exit;
				echo 'Error - Not set action';
			}

			/* --- Media Explorer --- */
			if ( $action === 'media_explorer' ) {

				/* Variables */
				$pagenum = $_POST['page_num'];
			    $args = array();
			    $update_fields = $_POST['update_fields'];
			    $args['pagenum'] = $pagenum;
			    $args['numberposts'] = $_POST['numberposts'];
			    $output = '';

				if ( isset( $_POST['layout'] ) ) 
					$args['layout'] = $_POST['layout'];
				else 
					$args['layout'] = 'grid';

				if ( isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
					$args['ids'] = $_POST['ids'];
				}
				if ( isset( $_POST['s'] ) && $_POST['s'] !== '' ) 
					$args['s'] = stripslashes( $_POST['s'] );
				
				$results = $this->mm_query( $args );

				if ( ! isset( $results ) ) die();
				
			    $output = '';
				if ( ! empty( $results ) ) {

					foreach ( $results as $i => $result ) {

						$item = get_post( $result['ID'] );

						/* Grid */
						if ( $args['layout'] === 'grid' ) {
							$meta = wp_get_attachment_metadata( $result['ID'] );
							if ( is_array( $meta ) ) {
								$meta_html = esc_html( basename( $item->guid ) ) . ' - ' . $meta['width'] . 'x' . $meta['height'];
							} else {
								$meta_html = '';
							}
							$output .= '
							<a class="mm-item mm-image" id="' . esc_attr( $result['ID'] ) . '" title="' . esc_attr( $meta_html ) . '">
								<div class="mm-item-preview">
							    	<div class="mm-item-image">
							    		<div class="mm-centered">
							    			<img src="' . esc_url( $result['image'][0] ) . '" />
							    		</div>
							    	</div>
								</div>
							</a>';

						/* List */
						} else {

							/* Display list item
							 -------------------------------- */
							$result['cover'] = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
							$fields = $this->render_fields( $update_fields, $result );
							$output .= $this->list_tpl( $result['ID'], $fields );

							
						}
					}
				} else {
					$output = 'end pages';
				}

			    $this->e_esc( $output );
			    exit;
			}


			/* --- Add Media --- */
			if ( $action === 'add_media' ) {

				/* Variables */
				$items = $_POST['items'];
				$layout = $_POST['layout'];
				$update_fields = $_POST['update_fields'];

				if ( ! isset( $items ) || empty( $items ) ) 
					die();

				$output = '';
				foreach( $items as $id ) {

					$item = get_post( $id );

					/* grid */
					if ( $layout === 'grid' ) {
						$image = wp_get_attachment_image_src( $id );
						$meta = wp_get_attachment_metadata( $id );
						if ( is_array( $meta ) ) {
							$meta_html = esc_html( basename( $item->guid ) ) . ' - ' . $meta['width'] . 'x' . $meta['height'];
						} else {
							$meta_html = '';
						}
						$output .= '
						<a class="mm-item mm-image" id="' . esc_attr( $id ) . '" title="' . esc_attr( $meta_html ) . '">
		                	<div class="mm-item-preview">
			                	<div class="mm-item-image">
			                		<div class="mm-centered">
			                			<img src="' . esc_url( $image[0] ) . '" />
			                		</div>
			                	</div>
		                	</div>
		                	<span class="mm-edit-button"><i class="fa fa-gear"></i></span>
		                </a>';
					}

					/* List */
					if ( $layout === 'list' ) {
						$audio = get_post( $id );

						/* Display list item
						 -------------------------------- */
						$result['cover'] = esc_url( self::$_args['admin_url'] ) . '/assets/images/metabox/audio.png';
						$result['title'] = $audio->post_title;
						$fields = $this->render_fields( $update_fields, $result );
						$output .= $this->list_tpl( $id, $fields );

					}
				}

				$this->e_esc( $output );
				exit;
			}


			/* --- Remove Media --- */
			if ( $action === 'remove_media' ) {

				/* Variables */
				$settings = $_POST['settings'];
				$selected_ids = $_POST['selected_ids'];
				$output = '';

				if ( ! isset( $selected_ids ) || empty( $selected_ids ) ) 
					die();
				if ( ! isset( $settings ) ) 
					die();

				foreach ( $selected_ids as $id ) {
					$option_name = $settings['mm_id'] . '_' . $id;
					
					if ( get_post_meta( $settings['post_id'], $option_name ) ) {
						delete_post_meta( $settings['post_id'], $option_name );
					}

				}
				echo 'success';
				exit;
			}


			/* --- Update Media --- */
			if ( $action === 'update_media' ) {

				/* Variables */
				$settings = $_POST['settings'];
				$ids = $_POST['ids'];
				$output = '';
				
				if ( ! isset( $settings ) ) 
					die();

				/* Update post string */
				if ( ! isset( $ids ) || $ids === '' )
					delete_post_meta( $settings['post_id'], $settings['mm_id'] );
				else
			    	update_post_meta( $settings['post_id'], $settings['mm_id'], $ids );
			  	
				echo 'success';
			   	exit;
			}

			echo 'Error: Bad action';
			exit;
		}


		/* Widgets
		---------------------------------------------- */

		/* mm Box */
		function mm_explorer_box() {
		  
			echo '<div id="mm-explorer-box" style="display:none">';
			echo '<input type="hidden" autofocus="autofocus" />';
			echo '<div id="explorer-top">';
			echo '<label for="mm-search">';
			echo '<input type="text" id="mm-search" name="mm-search" tabindex="60" autocomplete="off" value="" placeholder="' . esc_html__( 'Search', 'epron-toolkit' ) . '" />';
			echo '</label>';
			echo '<label for="mm-select" class="mm-label-select">';
			echo '<span>' . esc_html__( 'Select All:', 'epron-toolkit' ) . '</span>';
			echo '<input type="checkbox" id="mm-select" name="mm-select"/>';
			echo '</label>';
			echo '<img id="mm-explorer-loader" class="mm-ajax" src="' . esc_url(admin_url('images/wpspin_light.gif')) . '" alt="" />';
			echo '</div>';
			
			/* Results */
			echo '<div class="mm-block">';
			echo '</div>';
			echo '<div class="clear"></div>';
			echo '<span class="mm-load-next">' . esc_html__( 'Load Next 30 Items', 'epron-toolkit' ) . '</span>';

			echo '</div>';

		}


		/* ----- Helper functions ----- */

		/* mm query */
		function mm_query( $args = array() ) {

			/* Media Manager type */
			if ( $args['layout'] === 'grid' ) 
				$args['type'] = 'image';
			else 
				$args['type'] = 'audio';

			$query = array(
				'post_type'      => 'attachment',
				'order'          => 'DESC',
				'orderby'        => 'post_date',
				'post_status'    => null,
				'post_parent'    => null, // any parent
				'post_mime_type' => $args['type'],
				'numberposts'    => $args['numberposts']
			);
		    
			if ( isset( $args['ids'] ) ) 
				$query['exclude'] = $args['ids'];
			
			$args['pagenum'] = isset( $args['pagenum']) ? absint( $args['pagenum'] ) : 1;

			if ( isset( $args['s'] ) ) $query['s'] = $args['s'];

			$query['offset'] = $args['pagenum'] > 1 ? $query['numberposts'] * ($args['pagenum'] - 1) : 0;

			// Do main query.
			$posts = get_posts( $query );

			// Check if any posts were found.
			if ( ! $posts )
				return false;

			// Build results.
			$results = array();
			foreach ( $posts as $post ) {
				setup_postdata( $post ); 
				$results[] = array(
					'ID' => $post->ID,
					'image' => wp_get_attachment_image_src( $post->ID ),
					'title' => trim( esc_html( strip_tags( get_the_title( $post) ) ) ),
					'permalink' => get_permalink( $post->ID )
				);
			}
			return $results;
		}


		/* ------------------------------------------------------------------------------------------- */

		/*											EDITOR 											   */
		
		/* ------------------------------------------------------------------------------------------- */


		/* Box */
		private function mm_editor_box() {
		  
		    echo '<div id="mm-editor-box" style="display:none">';
		    echo '<input type="hidden" autofocus="autofocus" />';
			echo '<img id="mm-editor-loader" src="' . esc_url(admin_url('images/wpspin_light.gif')) . '" alt="" />';
			echo '<div id="mm-editor-content">';

			echo '</div>';
		    echo '</div>';
		}

		/* Editable content */
		public function mm_editor() {
		
			/* Variables */
			$id = $_POST['item_id'];
			$settings = $_POST['settings'];
			$custom = ($_POST['custom'] === 'true');
			if ( ! isset( $id ) || ! isset( $settings ) ) 
				die();
			$type = $settings['mm_type'];
			$item = get_post( $id );
			$output = '';
			$option_name = $settings[ 'mm_id' ] . '_' . $id;
			$options = get_post_meta( $settings[ 'post_id' ], $option_name, true );

			// If post doesn't exists
			if ( ! $item && ! $custom ) {
					echo '<p class="msg msg-error">' . esc_html__( 'Error!', 'epron-toolkit' ) . '</p>';
				exit;
				return die();
			}

		   	
			// Include fields

			// Buttons
			if ( file_exists( plugin_dir_path(__FILE__) .'media_manager_buttons.php' ) ) {
				require_once( 'media_manager_buttons.php' );
				if ( function_exists( 'rascals_media_manager_buttons' ) ) {
					$output .= rascals_media_manager_buttons( $type, $id, $item, $options, $custom );
				}
			}

			// Albums SLider
			if ( file_exists( plugin_dir_path(__FILE__) .'media_manager_albums_slider.php' ) ) {
				require_once( 'media_manager_albums_slider.php' );
				if ( function_exists( 'rascals_media_manager_albums_slider' ) ) {
					$output .= rascals_media_manager_albums_slider( $type, $id, $item, $options, $custom );
				}
			}

			// Images
			if ( file_exists( plugin_dir_path(__FILE__) .'media_manager_images.php' ) ) {
				require_once( 'media_manager_images.php' );
				if ( function_exists( 'rascals_media_manager_images' ) ) {
					$output .= rascals_media_manager_images( $type, $id, $item, $options, $custom );
				}
			}

			// Simple Slider
			if ( file_exists( plugin_dir_path(__FILE__) .'media_manager_simple_slider.php' ) ) {
				require_once( 'media_manager_simple_slider.php' );
				if ( function_exists( 'rascals_media_manager_simple_slider' ) ) {
					$output .= rascals_media_manager_simple_slider( $type, $id, $item, $options, $custom );
				}
			}

			// Slider
			if ( file_exists( plugin_dir_path(__FILE__) .'media_manager_slider.php' ) ) {
				require_once( 'media_manager_slider.php' );
				if ( function_exists( 'rascals_media_manager_slider' ) ) {
					$output .= rascals_media_manager_slider( $type, $id, $item, $options, $custom );
				}
			}

			// Audio
			if ( file_exists( plugin_dir_path(__FILE__) .'media_manager_audio.php' ) ) {
				require_once( 'media_manager_audio.php' );
				if ( function_exists( 'rascals_media_manager_audio' ) ) {
					$output .= rascals_media_manager_audio( $type, $id, $item, $options, self::$_args[ 'admin_path' ], $custom );
				}
			}

		    $this->e_esc( $output );
		    exit;
		}

	}
}