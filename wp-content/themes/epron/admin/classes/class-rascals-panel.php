<?php
/**
 * Rascals Theme Panel
 *
 * @package         RascalsThemePanel
 * @version 		1.2.0
 * @author          Mariusz Rek
 * @copyright       2019 Rascals Themes
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    die;
}

if ( ! class_exists( 'RascalsThemePanel' ) ) {
	class RascalsThemePanel {
		
		protected $app_environment = 'stage'; // product / stage
		protected $args;
		protected $extensions = array();
		protected $options;
		protected $saved_options;
		protected $used_plugins = array( 'panel' );
		protected $textdomain = 'epron';
		protected $defaults = array(
			'admin_path'      => '',
			'admin_uri'       => '',
			'option_name'     => 'rascals_panel_options',
			'css_option_name' => 'rascals_panel_css',
			'resize_function' => false
		);
		

		/**
         * Panel Constructor.
         *
         * @since       1.1.0
         * @access      public
         * @return      void
        */
		public function __construct( $args, $options ) {

			/* Set options and page variables */
			if ( isset( $args ) && is_array( $args ) ) {
				$this->args = array_merge( $this->defaults, $args );
			} else {
				$this->args = $args;
			}

			// Options
			$this->options = $options;

			
			/* Set paths */

			/* Set path */
			$this->args['admin_path'] = get_template_directory_uri() . '/admin';

			/* Set URI path */
			$this->args['admin_uri'] = get_template_directory() . '/admin';

			/* Get saved options */
			$this->saved_options = get_option( $this->args['option_name'] );

			/* Parse CSS */
			$this->css_parser();
			
			if ( is_admin() ) {
				add_action( 'init', array( $this, 'init' ) );
			}
		}


		/**
         * Admin CSS Parser
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		function css_parser() {
			add_action( 'wp_enqueue_scripts', array( $this, 'css_enqueue' ), 12 );
		}

		/**
         * Enqueue CSS Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		function css_enqueue() {
			
			$css = get_option( $this->args['css_option_name'] );

			if ( $css && is_array($css) ) {
				
				$output = '';

				foreach ($css as $rule) {
					if ( isset( $rule['values'] ) ) {
						$output .= $rule['selector'];
						$output .= '{';
						foreach ($rule['values'] as $name => $value) {
							$output .= $name . ':' . $value . ';';
						}
						$output .= '} ';
					}
				}

				// CSS file
				wp_register_style( 'rascalspanel-custom', false );
				wp_enqueue_style( 'rascalspanel-custom' );

				wp_add_inline_style( 'rascalspanel-custom', $output );

			}

		}


		/**
         * Create sidebar menu.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		function add_admin_menu() {
			$add_submenu_func = 'add_'.'submenu_'.'page';
			add_theme_page( $this->args['menu_name'], $this->args['menu_name'], 'edit_theme_options', 'theme-panel', array( $this, 'display'));
		 }
		

		/**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		public function rascalspanel_enqueue() {

			/* Panel
			---------------------------------------------- */

			$current_screen = get_current_screen();

			if ( strpos( $current_screen->base, 'theme-panel' ) !== false ) {

				if ( $this->app_environment === 'product' ) {
					$min = '.min';
				} else {
					$min = '';
				}
						
				wp_enqueue_script('media-upload');

				/* Media */
				wp_enqueue_media();

				/* Notify */
				wp_enqueue_script( 'notify-js', esc_url( $this->args['admin_path'] ) . '/assets/vendors/jquery.notify/jquery.notify.min.js', false, false, true);

				/* UI */
				wp_enqueue_style( 'rascals-panel-ui', esc_url( $this->args['admin_path'] ) . '/assets/vendors/jquery-ui/jquery-ui.min.css' );

				/* Panel stylesheet */
				wp_enqueue_style( 'rascals-panel', esc_url( $this->args['admin_path'] ) . '/assets/css/panel'.$min.'.css' );

				/* Panel fonts */
				wp_enqueue_style( 'font-awesome', esc_url( $this->args['admin_path'] ) . '/assets/vendors/font-awesome/font-awesome.min.css' );
				
				/* Panel javascripts */
				wp_enqueue_script( 'rascals-panel-core', esc_url( $this->args['admin_path'] ) . '/assets/js/core'.$min.'.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-widget', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-ui-slider', 'jquery-ui-draggable', 'jquery-ui-datepicker'), false, true );

				/* Panel Fields */
				wp_enqueue_script( 'rascals-panel-fields', esc_url( $this->args['admin_path'] ) . '/assets/js/fields'.$min.'.js', false, false, true);
			
			}
	    }


		/**
         * Admin Init
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		function init() {

			/* Call method to create the sidebar menu items */
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 19 );

			/* --- Ajax Actions --- */
			add_action( 'wp_ajax_panel_save', array( $this, 'panel_save' ) );


			/* Import Dummy data */
			if ( ! isset( $this->saved_options[ 'theme_name' ] )  ) {

				// Modify this filename and / or location to meet your needs.
		        $dummy_file = get_template_directory_uri() . '/admin/default-options.txt';
		      
		        $dummy_data = wp_remote_get( $dummy_file );

		        // Error
		        if ( ! $dummy_data || is_wp_error( $dummy_data ) ) {
		            $dummy_data = '';
		        } else {

		        	/* Import items */
					$dummy_data = maybe_unserialize( $dummy_data['body'] );
					if ( $dummy_data ) {
						update_option( $this->args['option_name'], $dummy_data );
					}

		        }

			}

			// Panel Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'rascalspanel_enqueue' ) );
			
			/* --- Extensions --- */
			foreach ( $this->options as $option ) {

				// Extension Class Name
				$class_name = 'RascalsThemePanel_' . esc_attr( $option['type'] );

				if ( ! method_exists( $this, $option['type'] ) ) {

					// Include Extensions
					$file = $option['type'] . '.php';
					if ( file_exists( $this->args['admin_uri'] . '/fields/' . $option['type'] . '/' . $file  ) ) {
						require_once( $this->args['admin_uri'] . '/fields/' . $option['type'] . '/' . $file );
						if ( class_exists( $class_name ) ) {
							$this->extensions[$option['type']] = new $class_name( $option, $this->args, $this->saved_options );
						}
					}

				} 
			}
		}	


		/**
         * Save options
         *
         * @since       1.1.0
         * @access      public
         * @return      void
        */
		function panel_save() {
		    $data = $_POST['data'];
			
			$new_options = array( );
			
			if ( isset( $data['save_options'] ) ) {

				if ( isset( $data['import'] ) && $data['import'] !== '' ) {
					
					/* Import items */
					$data = stripslashes( $data['import'] );
					$data = maybe_unserialize( $data );
					if ( ! $data ) {
						echo 'import_error';
						die();
					} else {
						update_option( $this->args['option_name'], $data );
					}
					update_option( $this->args['option_name'], $data );
					
				} else {
					foreach ( $this->options as $option ) {
						if ( isset( $option['id'] ) && is_array( $option['id'] ) ) {
							$items_count = count( $data[$option['array_name'] . '_hidden'] );
							for ( $items = $items_count; $items >= 0; $items-- ) {
		
								foreach ( $option['id'] as $item => $option_id ) {
									
									if ( isset( $data[$option_id['id']][ $items ] ) && $data[$option_id['id']][ $items ] !='' ) {
										$data_items = $data[ $option_id['id'] ][ $items ];
										$new_options[$option['array_name']][ $items ][$option_id['name']] = stripslashes( $data_items );
									}
								}
							}
		
						} else {
							if ( isset( $option['id'] ) && isset( $data[$option['id']] ) ) {

								// Multiple arrays
								if ( is_array( $data[$option['id']] ) ) {
								  $new_options[ $option['id'] ] = $data[$option['id']];
								} elseif ( $data[$option['id']] !== '' ) {
								   $new_options[ $option['id'] ] = stripslashes( $data[$option['id']] );
								} elseif ( isset($option['std'] ) && $option['std'] !== '' ) {
								   $new_options[ $option['id'] ] = stripslashes( $option['std'] );
								}
							}
						}
					}
				
				   update_option( $this->args['option_name'], $new_options );
				}
				
				$this->saved_options = $new_options;
				$this->saved_options['export'] = serialize( $this->saved_options );
				
				/* Encode */
				echo json_encode( $this->saved_options );

				// CSS
				$elements = array();
				$selectors = array();
				$el_nr = 0;
				foreach ( $this->options as $option ) {	

		    		// CSS
		    		if ( isset( $option['output'] ) && $option['output'] !== '') {

		    			// This selector has saved data
		    			if ( isset( $this->saved_options[$option['id']] ) )	{
		    				$elements[$el_nr]['output'] = $option['output'];

		    				$data = json_decode( $this->saved_options[$option['id']], true );
		    				$val_output = '';
		    				// We have saved multi options
							if ( $data ) {
								$val_output = $data;
							} else {
								$val_output = $this->saved_options[$option['id']];
		    				}
		    				if ( is_array($val_output) ) {
		    					$values = array_map('array_pop', $val_output);
		    					$val_output = implode(' ', $values);
		    				}

		    				$elements[$el_nr]['value'] = $val_output;

		    				// grab all selectors
		    				foreach ($option['output'] as $o_key => $o) {
		    					// Create selectors
		    					foreach ($o['element'] as $el) {
		    						array_push($selectors, $el);
		    					}
		    				}

		    				$el_nr++;
		    			}
		    		}
		    	}

		    	if ( count( $selectors ) > 0 ) {
		    		
		    		$selectors_new = array_unique($selectors);

					// We have some CSS options
		    		$css = array();
		    		foreach( $selectors_new as $k => $sel ) {
		    			$css[$k]['selector'] = $sel;
		    			foreach ($elements as $e_index => $e) {
		    				$val = $e['value'];
		    				foreach ($e['output'] as $o_key => $o) {
		    					$prop = $o['property'];
		    					if ( in_array($sel, $o['element']) ) {
		    						$css[$k]['values'][$prop] = $val;
		    					}
		    					
		    				
		    				}
		    			}
		    		}

		    		update_option( $this->args['css_option_name'], $css );

		    	} else {
		    		delete_option( $this->args['css_option_name'] );
		    	}


			}
			die();
		}
		
		
		/**
         * Display framework fields
         *
         * @since       1.0.1
         * @access      public
         * @return      void
        */
		function display() {
				
			$this->saved_options = get_option( $this->args['option_name'] );

			/* Autosave */
			if ( isset($_REQUEST['autosave']) ) {
				$autosave = 'true';
			} else { 
				$autosave = 'false';
			}

			if ( function_exists( 'epron_admin_page_header' ) ) {
				epron_admin_page_header();
			}

			/* Panel */
			echo '<div id="rascals-panel" data-autosave="' . esc_attr( $autosave ) . '">';

			/* Mobile top */
			echo '<div id="rascals-panel-top">';
			echo '<span id="show-res-nav" class="mobile-button"><i class="icon fa-bars"></i></span>';
			echo '<span id="_save_mobile" class="mobile-button"><i class="icon fa-save"></i></span>';
			echo '</div>';

			/* Sidebar */
			echo '<div id="rascals-panel-sidebar">';
	  		echo '<div id="menu-container">';
			echo '<ul class="rascals-panel-menu">';
			
			/* Display menu */
			$sub = false;
	    	foreach ( $this->options as $option ) {

				if ( $option['type'] === 'open' && isset( $option['tab_name'] ) ) {
					echo '<li>'."\n";
					if ( isset( $option['icon'] ) ) {
						$icon = '<i class="fa icon fa-' . esc_attr( $option['icon'] ) . '"></i>';
					} else {
						$icon = '';
					}
					echo '<a class="rascals-panel-menu-level0" data-tab_id="' . esc_attr( $option['tab_id'] ) . '" href="#nav">' . $this->esc( $icon ) . '<span>' . esc_html( $option['tab_name'] ) . '</span></a>';
				}
				// sub
				if ( $option['type'] === 'sub_open' && isset( $option['sub_tab_name'] ) && $sub === false ) {
					echo '<ul class="rascals-panel-sub-menu">';
					$sub = true;
				}

				if ( $option['type'] === 'sub_open' && isset( $option['sub_tab_name'] ) && $sub === true ) {
					echo '<li><a class="rascals-panel-menu-level1" data-tab_id="' . esc_attr( $option['sub_tab_id'] ) . '" href="#nav">' . esc_attr( $option['sub_tab_name'] ) . '</a></li>';
					$sub = true;
				}
				
				if ( $option['type'] === 'close' && $sub === false ) {
					echo '</li>';
				}
				
				if ( $option['type'] === 'close' && $sub === true) {
					echo '</ul>';
					echo '</li>';
					$sub = false;
				}
						
			}
	        echo '</ul>';
	        echo '</div>';
			echo '<button class="_button" id="_save"><i class="icon fa-save"></i><span class="r-save-text">' . esc_html__( 'Save Settings', 'epron' ) . '</span></button>';
			echo '</div>';
			
			/* Content */
			echo '<div id="rascals-panel-content">';

			/* Notices */
			echo '<div id="rascals-panel-notices" style="display:none"><div id="default"><h1>#{title}</h1><p>#{text}</p></div></div>';

			/* Form */
	    	echo '<form method="post" id="rascals-panel_form" action="#">';

			/* Display */
	      	foreach ( $this->options as $key => $option ) {	
					

				if ( method_exists( $this, $option['type'] ) ) {

					// Display Private Methods
					call_user_func( array( $this, $option['type'] ), $option );

				} else {

					// Extensions
					if ( isset( $this->extensions[ $option['type'] ] ) ) {
						$instance = $this->extensions[ $option['type'] ];
					} else {
						$instance = false;
					}
					
					$class_name = 'RascalsThemePanel_' . esc_attr( $option['type'] );

					if ( is_object( $instance ) ) {
						if ( class_exists( $class_name ) && $instance instanceof $class_name ) {
							$o = new $instance( $option, $this->args, $this->saved_options );
							$o->render();
						}
					}

				}

			}

			unset( $this->extensions );
			
			echo '<input type="hidden" name="save_options" value="1"/>';
			echo '</form>';
			echo '</div>';
			echo '</div>';
		}

		
		/* Public Methods
		---------------------------------------------- */

		/* Add Custom Option
		---------------------------------------------- */
		public function custom_options( $new_options ) {

			if ( is_array( $new_options ) ) {

				array_push( $this->options,  $new_options );
			}
		}


		/* Get Option
		---------------------------------------------- */
		public function get_option( $option, $default = null ) {

			if ( is_array( $this->saved_options ) ) {
				if ( isset( $this->saved_options[ $option ] ) ) {
					return $this->saved_options[ $option ];
				} else if ( $default !== null ) {
					return $default;
				} else {
					return false;
				}
			} else {
				if ( $default !== null ) {
					return $default;
				} 
				return false;
			}
		}


		/* Get Option Echo
		---------------------------------------------- */
		public function e_get_option( $option, $default = null ) {

			if ( is_array( $this->saved_options ) ) {
				if ( isset( $this->saved_options[ $option ] ) ) {
					$this->esc( $this->saved_options[ $option ] );
				} else if ( $default !== null ) {
					return $default;
				} else {
					return false;
				}
			} else {
				if ( $default !== null ) {
					return $default;
				} 
				return false;
			}
		}


		/* ESC
		---------------------------------------------- */
		public function esc( $option ) {
			$option = preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $option );
			return $option;
		}


		/* ESC Echo
		---------------------------------------------- */
		public function e_esc( $option ) {
			echo preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $option );
		}


		/* Image exist
		---------------------------------------------- */
		public function get_image( $option, $size = 'full', $default = null ) {

			// Get image field
			if ( $this->get_option( $option ) ) {
				$option = $this->get_option( $option );
			}

			// Check image src or image ID
			if ( intval( $option ) ) {
		    	$image_att = wp_get_attachment_image_src( $option, $size );

			   	if ( $image_att[0] ) {
			   		return $image_att[0];
			   	} else if ( $default !== null ) {
					return $default;
				} else {
					return false;
				}
			}

			//define upload path & dir
		   	$upload_info = wp_upload_dir();
			$upload_dir = $upload_info['basedir'];
			$upload_url = $upload_info['baseurl'];

			// check if $img_url is local
			if( strpos( $option, $upload_url ) === false ) {

			   	if ( $default !== null ) {
					return $default;
				}
				return false;
			}	


			//define path of image
			$rel_path = str_replace( $upload_url, '',  $option );
			$img_path = $upload_dir . $rel_path;

			$image = @getimagesize( $img_path );
			if ( $image ) {
				return $option;
			} else if ( $default !== null ) {
				return $default;
			} else {
				return false;
			}
		}


		/* Private Methods
		---------------------------------------------- */

		/* Open Tab
		---------------------------------------------- */
		private function open( $value ) {
			echo '<div class="rascals-panel-tab rascals-panel-main-tab" id="' . esc_attr( $value['tab_id'] ) . '">';
			echo '<div class="rascals-panel-breadcrumb"><div>' .  esc_html( $value['tab_name'] ) . '</div><div class="r-separator"><i class="fa fa-angle-right"></i></div><div></div></div>';

			/* Display description */
			if ( isset( $value['desc'] ) ) {
				echo '<div class="sub-desc">' . $this->esc( value['desc'] ) . '</div>';
			}
		}
		

		/* Close Tab
		---------------------------------------------- */
		private function close( $value ) {
			echo '</div>';
		}
		

		/* Sub tab
		---------------------------------------------- */
		private function sub_open( $value ) {
			echo '<div class="rascals-panel-tab" id="' . esc_attr( $value['sub_tab_id'] ) . '">';
			
		}
		

		/* Sub Tab Close
		---------------------------------------------- */
		private function sub_close( $value ) {
			echo '</div>';
		}


		/* Export
		---------------------------------------------- */
		private function export( $value ) {	
			
			if ($this->saved_options !== false && count($this->saved_options) > 0) {
				$export = serialize( $this->saved_options );
			} else { 
				$export = '';
			}

			echo '<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="color">' . esc_html__( 'Export Data', 'epron' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<textarea name="export" style="height:200px;overflow:auto">' . esc_textarea( $export ) . '</textarea>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';
		}
		

		/* Import
		---------------------------------------------- */
		private function import( $value ) {	
			
			echo '<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="color">' . esc_html__( 'Import Data', 'epron' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<div id="data-import-wrap" style="display:none">
								<div class="input-wrap"></div>
							</div>
							<div class="clear"></div>
							<button class="_button data-import"><i class="fa fa-upload icon"></i>' . esc_html__( 'Import data', 'epron' ) . '</button>
							<p class="help-box">' . esc_html__( 'Click on "Import data" button and paste in the box above previously exported data, and press the save button.', 'epron' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';
		}


		/* Hidden Field
		---------------------------------------------- */
		private function hidden_field( $value ) {
			echo '<input type="hidden" name="' . esc_attr( $value['id'] ) . '" id="' . esc_attr( $value['id'] ) . '" value="' . esc_attr( $value['value'] ) . '"/>';
		}
	}
}

?>