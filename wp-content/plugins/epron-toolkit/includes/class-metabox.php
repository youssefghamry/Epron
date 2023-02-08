<?php
/**
 * Rascals MetaBox
 *
 * Register Metaboxes
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

class RascalsBox {
	
	protected $minified = false;
	protected $options;
	protected $args = array();
	protected $extensions = array();
	protected $box;
	protected $admin_url;
	protected $admin_path;
	

	/**
     * Panel Constructor.
     *
     * @since       1.0.0
     * @access      public
     * @return      void
    */
	public function __construct( $options, $box ) {

 		/* Set options */

 		/* if array is Imported */
 		foreach ($options as $key => $opt) {

			if ( ! isset( $opt['type'] ) && is_array( $opt ) ) {
				$extra_a = $opt;
				
				foreach ( $extra_a as $extra_opt ) {
					$sorted_options[] = $extra_opt;
				}
			} else {
				$sorted_options[] = $opt;
			}
		}

		$this->options = $sorted_options;
		$this->box = $box;

		/* Minified scripts */
		if ( $this->minified ) {
			$this->minified = '.min';
		} else {
			$this->minified = '';
		}

		// Set path
		$this->args[ 'admin_url' ] = RASCALS_TOOLKIT_URL;

		/* Set URI path */
		$this->args[ 'admin_path' ] = RASCALS_TOOLKIT_PATH;

		/* --- Scripts --- */
		add_action( 'load-post.php', array( $this, 'load_post' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post' ) );

		/* --- Class Actions --- */

		/* init */
		add_action( 'admin_menu', array( $this, 'init' ) );

		/* Save post */
		add_action( 'save_post', array( $this, 'save_postdata' ) );


		/* --- Extensions --- */
		foreach ( $this->options as $option ) {

			// Extension Class Name
			$class_name = 'RascalsBox_' . $option['type'];

			if ( ! method_exists( $this, $option['type'] ) ) {

				// Include Extensions
				$file = $option['type'] . '.php';

				if ( file_exists( $this->args['admin_path'] . '/includes/metabox-fields/' . $option['type'] . '/' . $file  ) ) {

					require_once( $this->args['admin_path'] . '/includes/metabox-fields/' . $option['type'] . '/' . $file );
					if ( class_exists( $class_name ) ) {
						$this->extensions[$option['type']] = new $class_name( $option, $this->args, $this->options );
					}
				}

			} 
		}

	}


	/**
     * Init Function.
     *
     * @since       1.0.0
     * @access      public
     * @return      void
    */
	function init() {	
		$this->create();
	}


	/**
     * Load Post
     *
     * @since       1.0.0
     * @access      public
     * @return      void
    */
	function load_post() {
		global $post;

		/* Get screen object */
		$screen = get_current_screen();
		$page = false;

		/* Checking if a box is also to be displayed on the page */
		if ( in_array( 'page', $this->box['page'] ) ) {
			$page = true;
		}

		if ( in_array( $screen->post_type, $this->box['page'] ) ) {

			/* Add scripts only on page */
			if ( $page ) {

				// If page exist
				if ( isset( $_GET['post'] ) ) {
					$template_name = get_post_meta( $_GET['post'], '_wp_page_template', true );
				} else {
		        	$template_name = '';
		        }

		        if ( $template_name === 'default' || $template_name === '' ) {
		        	$template_name = 'default';
		        }

		        // Display a box on the page with selected template
		        if ( in_array( $template_name, $this->box['template'] ) ) {
		        	$this->enqueue();
		        }
		        
			} else {

				$this->enqueue();
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
	private function enqueue() {

		/* UI */
		wp_enqueue_style( 'rascalsbox-ui', $this->args[ 'admin_url' ] . '/assets/css/admin-jquery-ui'. esc_attr( $this->minified  ) .'.css' );

		/* Metabox stylesheet */
		wp_enqueue_style( 'rascalsbox', $this->args[ 'admin_url' ] . '/assets/css/admin-metabox'. esc_attr( $this->minified  ) .'.css' );

		/* Metabox fonts */
		wp_enqueue_style( 'font-awesome', $this->args[ 'admin_url' ] . '/assets/vendors/font-awesome/font-awesome'. esc_attr( $this->minified  ) .'.css' );

		/* Metabox javascripts */
		wp_enqueue_script( 'rascalsbox', $this->args[ 'admin_url' ] . '/assets/js/admin-metabox'. esc_attr( $this->minified  ) .'.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-widget', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-ui-slider', 'jquery-ui-draggable', 'jquery-ui-datepicker'), false, true );

	}


	/**
     * Create Function.
     * Create metabox
     *
     * @since       1.1.0
     * @access      public
     * @return      void
    */
	private function create() {
		if ( function_exists( 'add_meta_box' ) && is_array( $this->box['template'] ) ) {

			foreach ( $this->box['template'] as $template ) {
				if ( isset( $_GET['post'] ) ) {
					// If post type
					if ( get_post_type( $_GET['post'] ) === 'page' ) {
						$template_name = get_post_meta( $_GET['post'], '_wp_page_template', true );
					} else {
						$template_name = '';
					}
				} else {
		        	$template_name = '';
		        }
		
				if ( $template === 'default' && $template_name === '' ) {
					$template_name = 'default';
				} elseif ($template === 'post') {
					$template = '';
				}
				// var_dump($template);

				if ( $template === $template_name ) {
					if ( is_array( $this->box['page'] ) ) {
						foreach ( $this->box['page'] as $area ) {	
							if ( $this->box['callback'] === '' ) {
								$this->box['callback'] = 'display';
							}
							
							add_meta_box ( 	
								$this->box['id'], 
								$this->box['title'],
								array( $this, $this->box['callback'] ),
								$area, $this->box['context'], 
								$this->box['priority'],
								array( '__block_editor_compatible_meta_box' => true )
							);  
						}
					}  
				}
			}
		}
	}


	/**
     * Save Post Data Function.
     *
     * @since       1.0.0
     * @access      public
     * @return      void
    */
	function save_postdata()  {

		if ( isset( $_POST['post_ID'] ) ) {
			
			if ( ! isset( $_POST['post_type'] ) ) {
				return;
			}

			$post_id = $_POST['post_ID'];

			$groups = array();

			if ( 'page' === $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}
			}


			/* Verify */
			if ( isset( $_POST[$this->box['id'] . '_noncename'] ) && wp_verify_nonce( $_POST[$this->box['id'] . '_noncename'], plugin_basename(__FILE__) ) ) {	
				

				/* Generate temp groups for array metabox */
				foreach ( $this->options as $key => $option ) {

					if ( isset( $option['id'] ) && isset( $option['group'] ) ) {
						if ( is_array( $option['id'] ) ) {
							foreach ( $option['id'] as $option_id ) {
								if ( isset( $_POST[ $option_id['id'] ] ) ) {
							    	$data = $_POST[ $option_id['id'] ];
							    	$groups[$option['group']][$option_id['id']] = $data;
							    }
							}
						} else {
							if ( isset($_POST[ $option['id'] ]) ) {
								$data = $_POST[ $option['id'] ];
								$groups[$option['group']][$option['id']] = $data;
							}
						}
					}
				}

				foreach ( $this->options as $option ) {
					
					if ( isset( $option['id'] ) && isset( $option['group'] ) ) {
						if ( isset( $_POST[ $option['id'] ] ) ) {

							$data = $groups[$option['group']];

							if ( get_post_meta( $post_id, $option['group'] ) === '' ) {
								add_post_meta( $post_id, $option['group'], $data );
							
							} elseif ( $data !== get_post_meta( $post_id, $option['group'] ) ) {
								update_post_meta( $post_id, $option['group'], $data );
							}
							
						}

					// For Array IDs
					} elseif ( isset( $option['id'] ) && is_array( $option['id'] ) ) {
						foreach ( $option['id'] as $option_id ) {
							if ( isset( $_POST[ $option_id['id'] ] ) ) {
							    $data = $_POST[ $option_id['id'] ];

								if ( get_post_meta( $post_id , $option_id['id'] ) === '' ) {
									add_post_meta( $post_id, $option_id['id'], $data, true );
								} elseif ( $data !== get_post_meta( $post_id , $option_id['id'], true ) ) {
									update_post_meta( $post_id, $option_id['id'], $data );
								} elseif ( $data === '' ) {
									delete_post_meta( $post_id , $option_id['id'], get_post_meta( $post_id , $option_id['id'], true ) );
								}
						    }
						}
					} else {
						if ( isset( $option['id'] ) && isset( $_POST[ $option['id'] ] ) ) {
							$data = $_POST[ $option['id'] ];

							if ( get_post_meta( $post_id, $option['id']) === '' ) {
								add_post_meta( $post_id, $option['id'], $data, true );
							} elseif ( $data !== get_post_meta( $post_id, $option['id'], true ) ) {
								update_post_meta( $post_id, $option['id'], $data );
							} elseif ( $data === '' ) {
								delete_post_meta( $post_id, $option['id'], true );
							}
						}
					}
				}

			}

		}
	}


	/**
     * Display Fields.
     *
     * @since       1.0.1
     * @access      public
     * @return      void
    */
	function display() {	
	
		global $post;
        $count = 1;
		$css_class = '';
		$array_size = count( $this->options );

		/* Tabs */
		echo '<div class="rt-tabs-wrap">';

		/* Tabs menu */
		echo '<div class="rt-tabs-nav-wrap">';
		foreach ( $this->options as $option ) {
			if ( $option['type'] === 'tab_open' ) {
				$checked = '';
				if ( $count === 1 ) {
					$checked = 'checked';
				} 
				echo '<div data-id="' . esc_attr( $option['id'] ) . '" class="rt-tab-nav ' . esc_attr( $checked ) . '">' . esc_attr( $option['name'] ) . '</div>';
				$count++;
			}
		}
		echo '</div>';

		/* Reset count var */
		$count = 1;
		foreach ( $this->options as $option ) {
			
			if ( isset( $option['id'] ) ) {
				if ( is_array( $option['id'] ) ) {
					foreach ( $option['id'] as $i => $option_id ) {
						if ( isset( $option['group'] ) ) {
			    			$meta_box_arr = get_post_meta( $post->ID, $option['group'], false );
			    			$meta_box_value = ( isset( $meta_box_arr[0][$option_id['id']] ) ? $meta_box_arr[0][$option_id['id']] : '' );
			    		} else {
							$meta_box_value = get_post_meta( $post->ID, $option_id['id'], true );
						}
						if ( isset( $meta_box_value ) && $meta_box_value !== '' ) {
							$option['id'][$i]['std'] = $meta_box_value;
						}
						if ( ! isset( $option_id['std'] ) ) {
							$option['id'][$i]['std'] = '';
						}
						
					}
					
			    } else {
			    	if ( isset( $option['group'] ) ) {
			    		$meta_box_arr = get_post_meta( $post->ID, $option['group'], false );
			    		$meta_box_value = ( isset( $meta_box_arr[0][$option['id']] ) ? $meta_box_arr[0][$option['id']] : '' );

			    	} else {
						$meta_box_value = get_post_meta( $post->ID, $option['id'], true );
							
					}
					
					if ( isset( $meta_box_value ) && $meta_box_value !== '' ) {
						$option['std'] = $meta_box_value;
					}
					if ( !isset( $option['std'] ) ) {
						$option['std'] = '';
					}
					
			    }
			}
			
			if ( $option['type'] === 'tab_open' ) {
				$checked = '';
				if ( $count === 1 ) {
					$checked = 'checked';
				}
				echo '<div data-id="' . esc_attr( $option['id'] ) . '" class="rt-tab ' . esc_attr( $checked ) . '">';
				$count++;
			}
			if ( $option['type'] !== 'tab_open' && $option['type'] !== 'tab_close' ) {

				echo '<div class="rascalsbox">';
			}

			if ( method_exists( $this, $option['type'] ) ) {
				call_user_func( array( $this, $option['type'] ), $option );
			} elseif ( $option['type'] !== 'tab_open' && $option['type'] !== 'tab_close' ) {

				// Extensions
				$instance = $this->extensions[ $option['type'] ];
				$class_name = 'RascalsBox_' . $option['type'];

				if ( is_object( $instance ) ) {
					if ( class_exists( $class_name ) && $instance instanceof $class_name ) {
						$o = new $instance( $option, $this->args, $this->options );
						$o->render();
					}
				}

			}

			/* End rascalsbox */
			if ( $option['type'] !== 'tab_open' && $option['type'] !== 'tab_close' ) {
				echo '</div>';
		
			}

			/* End tab */
			if ( $option['type'] === 'tab_close' ) {
				echo '</div>';
			}
			
			$count++;
			
		}
		
		/* Security field */
		echo'<input type="hidden" name="' . $this->box['id'] . '_noncename" id="' . $this->box['id'] . '_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';  

		/* Tabs */
		echo '</div>';

	}
	

	/* Helper Functions
	---------------------------------------------- */

	/* ESC
	---------------------------------------------- */
	public function esc( $option ) {

		if ( is_string( $option ) ) {
			$option = preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $option );
		}

		return $option;
	}


	/* ESC Echo
	---------------------------------------------- */
	public function e_esc( $option ) {

		if ( is_string( $option ) ) {
			$option = preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $option );
		}

		print $option;
	}


	/* Image exist
	---------------------------------------------- */
	public function get_image( $img ) {

		// Check image src or image ID
		if ( is_numeric( $img ) ) {
	    	$image_att = wp_get_attachment_image_src( $img, 'full' );
		   	if ( $image_att[0] ) {
		   		return $image_att[0];
		   	} else { 
		   		return false;
		   	}
		}

		//define upload path & dir
	   	$upload_info = wp_upload_dir();
		$upload_dir = $upload_info['basedir'];
		$upload_url = $upload_info['baseurl'];

		// check if $img_url is local
		if ( strpos( $img, $upload_url ) === false ) {
			return false;
		}

		// define path of image
		$rel_path = str_replace( $upload_url, '', $img);
		$img_path = $upload_dir . $rel_path;

		$image = getimagesize( $img_path );
		if ( $image ) {
			return $img;
		} else { 
			return false;
		}

	}


	/* Image resize
	---------------------------------------------- */
	public function img_resize( $width, $height, $src, $crop = 'c', $retina = false ) {

		$image = $this->get_image( $src );

		// If icon
	   	if ( strpos( $src, ".ico" ) !== false ) {
	   		return $src;
	   	}

	   	// If image src exists
		if ( $image ) {
			if ( function_exists( 'mr_image_resize' ) ) {
				return mr_image_resize( $image, $width, $height, true, $crop, $retina );
			} else { 
				return $image;
			}
		}
		return false;
	}


} // end class