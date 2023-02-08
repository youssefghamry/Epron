<?php

/**
 * Rascals Importer
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsImporter {

	public $theme_options_file;

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 0.0.2
	 * @var object
	 */
	public $widgets;

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 0.0.2
	 * @var object
	 */
	public $content_demo;


	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 0.0.2
	 * @var object
	 */
	public $import_notice;


	/**
	 * Show WP Importer Console.
	 *
	 * @since 0.0.2
	 * @var string
	 */
	public $importer_console;

	/**
	 * Plugins.
	 *
	 * @since 0.0.2
	 * @var object
	 */
	public $theme_plugins;

	/**
	 * Homepage.
	 *
	 * @since 0.0.2
	 * @var object
	 */
	public $homepage;

	/**
	 * Flag imported to prevent duplicates
	 *
	 * @since 0.0.3
	 * @var array
	 */
	public $imported_flags= array( 'content' => false, 'menus' => false, 'options' => false, 'widgets' =>false, 'customizer' =>false  );

	/**
	 * imported sections to prevent duplicates
	 *
	 * @since 0.0.3
	 * @var array
	 */
	public $imported_demos = array();

	/**
	 * Flag imported to prevent duplicates
	 *
	 * @since 0.0.3
	 * @var bool
	 */
	public $add_admin_menu = true;

    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 0.0.2
     * @var object
     */
    private static $instance;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 0.0.2
     */
    public function __construct() {

    	global $pagenow;
    	
        self::$instance = $this;

        // Get main options
        $this->options = $this->importer_options;

        // Path
        $this->demo_files_path = $this->demo_files_path;
     
        // Get required plugins
        $this->theme_plugins = $this->required_plugins;

        // Show or hide console
        $this->importer_console = $this->wp_importer_console;

        // Check 
		$this->imported_demos = get_option( 'rascals_imported_demos' );

		// Menu
        if( $this->add_admin_menu ) add_action( 'admin_menu', array($this, 'add_admin'),11 );

        // Check previous Meta
        if ( $pagenow === 'themes.php' ) {
            add_filter( 'add_post_metadata', array( $this, 'check_previous_meta' ), 10, 5 );
        }

  		// Importer scripts and styles
		add_action( 'admin_enqueue_scripts', array( &$this, 'importer_enqueue' ) );

    }

	/**
	 * Add Panel Page
	 *
	 * @since 0.0.2
	 */
    public function add_admin() {
        add_theme_page( esc_html__( 'Install Demos', 'epron-toolkit' ), esc_html__( 'Install Demos', 'epron-toolkit' ), 'edit_theme_options', 'admin-demos', array($this, 'demo_installer'));

    }


    /**
	 * Importer enqueue
	 *
	 * @since 0.0.2
	 */
    public function importer_enqueue() {

		$current_screen = get_current_screen();

		if ( strpos( $current_screen->base, 'admin-demos' ) !== false ) {

			/* Style */
			wp_enqueue_style( 'rascals-importer', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/css/admin-importer.css' );

			/* Scripts */
			wp_enqueue_script( 'rascals-importer', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/js/admin-importer.js', false, false, true );
		}
    }


    /**
     * Avoids adding duplicate meta causing arrays in arrays from WP_importer
     *
     * @param null    $continue
     * @param unknown $post_id
     * @param unknown $meta_key
     * @param unknown $meta_value
     * @param unknown $unique
     *
     * @since 0.0.2
     *
     * @return
     */
    public function check_previous_meta( $continue, $post_id, $meta_key, $meta_value, $unique ) {

		$old_value = get_metadata( 'post', $post_id, $meta_key );

		if ( count( $old_value ) === 1 ) {

			if ( $old_value[0] === $meta_value ) {

				return false;

			} elseif ( $old_value[0] !== $meta_value ) {

				update_post_meta( $post_id, $meta_key, $meta_value );
				return false;

			}

		}

	}

	/**
	 * Add Panel Page
	 *
	 * @since 0.0.2
	 */
	public function after_wp_importer( $demo ) {
		$this->imported_flags['id'] = $demo;

		$imported_demos = get_option('rascals_imported_demos');

		if ( ! $imported_demos ) {
			$imported_demos = array();
		}

		$imported_demos[$demo] = true;

		update_option( 'rascals_imported_demos', $imported_demos );

	}

	

	public function req_plugins_html() {

		$plugins_activated = true;
		?>

		<div class="rascals-importer-warning">
		    <p class="tie_message_hint"><?php esc_html_e( 'Before you begin, you need to install and activate the following plugins', 'epron-toolkit') ?> (<a href="admin.php?page=admin-plugins"><?php esc_html_e( 'Install Plugins', 'epron-toolkit' );?></a>):</p>
		<ul>
  		<?php
    	if ( ! empty( $this->theme_plugins ) ) {
	    	foreach ( $this->theme_plugins as $key ) {
	    		if ( ! in_array( $key['path'], apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	    			echo '<li>' . $key['name'] . '</li>';
	    		}
			}
		}
		echo '</ul>';
		
	}

    /**
     * demo_installer Output
     *
     * @since 0.0.2
     * @return null
     */
    public function demo_installer() {

		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		$demo_content = isset($_REQUEST['demo_content']) ? $_REQUEST['demo_content'] : '';
		$admin_pages = EpronToolkit::getInstance()->admin_pages;

		$admin_pages->renderAdminMenu();

		$theme = wp_get_theme(); ?>
		
        <div id="rascals-importer" class="rascals-admin-wrap about-wrap">

    	<h1><?php echo esc_html( $theme->get( 'Name' ) ); ?> <?php _e( 'Demos', 'epron-toolkit' ) ?></h1>
		<div class="about-text">
			<p>
			<?php _e( 'Importing demo data (post, pages, images, theme settings, ...) is the easiest way to setup your theme. It will allow you to quickly edit everything instead of creating content from scratch. Before running the importer, make sure that you have installed and activated the required and recommended plugins. You will see a message at the bottom of this page if you have not yet done so. When you import the data following things will happen:', 'epron-toolkit' ) ?>
			</p>
		</div>
		<ul class="rascals-list">
			<li><strong><?php esc_html_e( 'All widgets located in primary sidebar will be removed, please move them to another sidebar.', 'epron-toolkit') ?></strong></li>
	        <li><?php esc_html_e( 'No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified.', 'epron-toolkit') ?></li>
	        <li><?php esc_html_e( 'No WordPress settings will be modified.', 'epron-toolkit') ?></li>
	        <li><?php esc_html_e( 'Posts, pages, some images, some widgets and menus will get imported.', 'epron-toolkit') ?></li>
	        <li><?php esc_html_e( 'Images will be downloaded from our server.', 'epron-toolkit') ?></li>
	        <li><?php esc_html_e( 'Please click import only once and wait.', 'epron-toolkit') ?></li>
	        <li><strong><?php esc_html_e( 'Be patient and wait for the import process to complete. It can take up to 5-7 minutes.', 'epron-toolkit') ?></li>
	      </ul>

		<div class="rascals-gdpr-confirm hidden">
			<input type="checkbox" id="confirm" name="confirm" value="confirm" />
    		<label for="confirm"><?php esc_html_e( 'I agree that all media elements of demo content like images, videos and mp3 files will be downloaded from the third-party server (rascalsthemes.com).', 'epron-toolkit') ?></label>
		</div>

       	<div class="rascals-importer-wrap" data-nonce="<?php echo wp_create_nonce('rascals-demo-code'); ?>">
			<div class="confirm-layer"></div>
	        <form method="post">
	        	
	        	<?php if ( $this->check_plugins() ) : ?>
	        	<div class="rascals-importer-content">
		        	
		          	<input type="hidden" name="demononce" value="<?php echo wp_create_nonce('rascals-demo-code'); ?>" />
					
					<?php if ( 'demo-data' !== $action ) : ?>
	
			          	<div class="demos">
							<?php

							// Display demos
							foreach ($this->options as $option) : ?>

								<div class="demo-column">
									<?php
										$opt = $option['id'];
										if ( $this->imported_demos && is_array($this->imported_demos) && isset( $this->imported_demos[$opt] ) ) {
											$btn_text = esc_html__( 'Import Again', 'epron-toolkit' );
											$demo_class = 'imported';
										} else {
											$btn_text = esc_html__( 'Import', 'epron-toolkit' );
											$demo_class = '';
										}
									 ?>
									<div class="demo <?php echo esc_attr( $demo_class ) ?>">
										<div class="demo-thumb">
											<img src="<?php echo esc_url( RASCALS_TOOLKIT_URL ) . '/includes/importer/demo-files/' . esc_attr( $option['thumb'] ) ?>" alt="Demo Thumb">
										</div>
										<h3><?php echo esc_html( $option['name'] ) ?></h3>
										<div class="import-btn">
											<input name="reset" data-id="<?php echo esc_attr( $option['id'] ) ?>" class="panel-save button-primary rascals-import-start" type="submit" value="<?php echo esc_attr( $btn_text ) ?>" />
										</div>
									</div>
								</div>


							<?php endforeach; ?>

						</div>
					<?php endif; ?>

		          	<input id="selected_demo_content" type="hidden" name="demo_content" value="light" />
		          	<input type="hidden" name="action" value="demo-data" />
				</div>
				
				<h3 class="rascals-importer-loading-msg hidden"><?php esc_html_e( 'Please be patient and wait for the import process to complete. It can take up to 5-7 minutes.', 'epron-toolkit' ); ?></h3>

	          	<?php if( 'demo-data' === $action && check_admin_referer('rascals-demo-code' , 'demononce')) : ?>
	          		
						<div id="rascals-importer-message" class="<?php echo esc_attr( $this->importer_console ) ?>">
				        
				        <?php	
				         	$this->process_imports( $demo_content );
				        ?>
			 	        
					</div>
				<?php endif; ?>
				<div id="rascals-importer-success">
					<h6><?php esc_html_e( 'Well Done! Demo content has been imported.', 'epron-toolkit' ) ?></h6>
					<p class="rascals-importer-notice"><?php echo wp_kses_post(  $this->import_notice ); ?></p>
	          	</div>
			<?php else : ?>
			<?php $this->req_plugins_html(); ?>
			<?php endif; ?>
 	        </form>

	        </div>

       </div><?php

    }

    public function check_plugins() {

    	$plugins_activated = true;
    	if ( ! empty( $this->theme_plugins ) ) {
	    	foreach ( $this->theme_plugins as $key ) {
	    		if ( ! in_array( $key['path'], apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	    			$plugins_activated = false;
	    			break;
	    		}
			}
		}
		return $plugins_activated;
		
    }

    /**
     * Process all imports
     *
     * @params $content
     * @params $options
     * @params $options
     * @params $widgets
     *
     * @since 0.0.3
     * @return void
     */
    public function process_imports( $demo_content ) {

    	// Content
    	foreach ( $this->options as $option ) {

    		if ( $option['id'] === $demo_content ) {

    			/* Demo path */
    			$demo_path = $this->demo_files_path . $demo_content;

    			/* Run function before import */
				$this->before_import();

    			// Import content files XML
    			if ( isset( $option['content_files'] ) ) {
    				foreach ( $option['content_files'] as $content ) {
    					$content_file = $this->demo_files_path . $content['file_path'];
    					if ( $content_file && !empty( $content_file ) && is_file( $content_file ) ) {
		 					$this->set_demo_data( $content_file );
		 				}
	
    				}
    			}

    			// Theme Options 
    			if ( isset( $option['panel_options_file'] ) ) {
    				$options_file = $this->demo_files_path . $option['panel_options_file'];
    				if ( $options_file && !empty( $options_file ) && is_file( $options_file ) ) {
						$this->set_demo_theme_options( $options_file );
					}
    			}

    			// Customizer
    			if ( isset( $option['customizer_file'] ) ) {
    				$customizer_file = $this->demo_files_path . $option['customizer_file'];
    				if ( $customizer_file && !empty( $customizer_file ) && is_file( $customizer_file ) ) {
						$this->set_customizer( $customizer_file );
					}
    			}

    			//Import widgets
    			if ( isset( $option['widget_file'] ) ) {

	    			// Import
    				$widget_file = $this->demo_files_path . $option['widget_file'];
	    			if ( $widget_file && !empty( $widget_file) && is_file( $widget_file) ) {
			 			$this->process_widget_import_file( $widget_file );
			 		}
		 		}

		 		//Import Revo Sliders
		 		if ( isset( $option['rev_sliders_files'] ) && $option['rev_sliders_files'] !== '' ) {
		 			$rev_sliders_path = $this->demo_files_path . $option['rev_sliders_files'];
		 		} else {
		 			$rev_sliders_path = $demo_path;
		 		}
		 		$this->import_rev_slider( $rev_sliders_path );

				/* Run function after import */
				$this->after_import($demo_content);

				// Display import notice
				if ( isset( $option['import_notice'] ) && $option['import_notice'] !== '' ) {
					$this->import_notice = $option['import_notice'];
				}

				$this->after_wp_importer( $option['id'] );
				echo '_IMPORTFINISH';
    			break;
    		}

    	}

    }


    /* Import Revo SLiders
     -------------------------------- */
    public function import_rev_slider( $demo ) {


    	if ( ! class_exists( 'RevSlider' ) ) {
    		return;
    	}

    	// Only for 6 and above versions
    	if ( defined('RS_REVISION') && version_compare( RS_REVISION, '6.0.0' ) >= 0 ) {

			if ( class_exists('RevSliderSliderImport') ) {

    		   $rev_sliders = glob( $demo . '/*.zip' );

			    if ( ! empty( $rev_sliders ) ) {
			        foreach ( $rev_sliders as $rev_slider ) {
			            $slider = new RevSliderSliderImport();
			            $slider->import_slider(false, $rev_slider, false, false, true, true);
			        }
			    }
    		}
		}
	}


    /* Set customizer options
    */
    public function set_customizer( $file ) {

    	// Setup global vars.
		global $wp_customize, $wpdb;

		// Setup internal vars.
		$template = get_template();

    	if ( ! file_exists( $file ) ) {
    		return;
    	}
		global $wp_filesystem;
        if ( empty( $wp_filesystem ) ) {
            require_once ( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }
		$raw = $wp_filesystem->get_contents( $file );

		if ( ! $raw ) {
			return;
		}

		$data = @unserialize( $raw );


		if ( ! is_array( $data ) && ( ! isset( $data['template'] ) || ! isset( $data['mods'] ) ) ) {
			return;
		}
		if ( $data['template'] !== $template ) {
			return;
		}

		// Loop through the mods and save the mods.
		foreach ( $data['mods'] as $key => $val ) {
			
			// Import images.
			if ( $this->is_image_url( $val ) ) {
				$path_a = explode( '/', $val );
	            $upload_index = array_search( 'uploads', $path_a );
	            if ( $upload_index ) {
	                $path_a = array_slice( $path_a, $upload_index, count( $path_a ) );
	                $path = implode( '/', $path_a );
	                $val = content_url() . '/' . $path;
	            }
			}

			do_action( 'customize_save_' . $key, $wp_customize );
			
			set_theme_mod( $key, $val );
		}

		do_action( 'customize_save_after', $wp_customize );
		$this->flag_as_imported['customizer'] = true;

    }


     /**
	 * Checks to see whether a string is an image url or not.
	 *
	 * @since 1.1.1
	 * @param string $string The string to check.
	 * @return bool Whether the string is an image url or not.
	 */
	private function is_image_url( $string = '' ) {
		if ( is_string( $string ) ) {
			if ( preg_match( '/\.(jpg|jpeg|png|gif)/i', $string ) ) {
				return true;
			}
		}
		return false;
	}


    /**
     * add_widget_to_sidebar Import sidebars
     * @param  string $sidebar_slug    Sidebar slug to add widget
     * @param  string $widget_slug     Widget slug
     * @param  string $count_mod       position in sidebar
     * @param  array  $widget_settings widget settings
     *
     * @since 0.0.2
     *
     * @return null
     */
    public function add_widget_to_sidebar($sidebar_slug, $widget_slug, $count_mod, $widget_settings = array()){

        $sidebars_widgets = get_option('sidebars_widgets');

        if(!isset($sidebars_widgets[$sidebar_slug]))
           $sidebars_widgets[$sidebar_slug] = array('_multiwidget' => 1);

        $newWidget = get_option('widget_'.$widget_slug);

        if(!is_array($newWidget))
            $newWidget = array();

        $count = count($newWidget)+1+$count_mod;
        $sidebars_widgets[$sidebar_slug][] = $widget_slug.'-'.$count;

        $newWidget[$count] = $widget_settings;

        update_option('sidebars_widgets', $sidebars_widgets);
        update_option('widget_'.$widget_slug, $newWidget);

    }

    public function set_demo_data( $file ) {

	    if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

        require_once ABSPATH . 'wp-admin/includes/import.php';

        $importer_error = false;

        if ( !class_exists( 'WP_Importer' ) ) {

            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

            if ( file_exists( $class_wp_importer ) ){

                require_once($class_wp_importer);

            } else {

                $importer_error = true;

            }

        }

        if ( !class_exists( 'WP_Import' ) ) {

            $class_wp_import = dirname(__FILE__) . '/class-wordpress-importer.php';

            if ( file_exists( $class_wp_import ) ) {
                require_once($class_wp_import);
            } else {
                $importer_error = true;
                
            }

        }

        if($importer_error){

            die("Error on import");

        } else {

            if ( ! is_file( $file )){
            	echo '<div class="rascals-importer-error">';
                esc_html_e( "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755. If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually.", 'epron-toolkit' );
                echo '</div>';

            } else {

               	$wp_import = new WP_Import();
               	$wp_import->fetch_attachments = true;
               	$wp_import->import( $file );
               	echo '_IMPORTING';
				$this->flag_as_imported['content'] = true;

         	}

    	}

    }

    public function set_demo_theme_options( $file ) {

    	// Does the File exist?
		if ( file_exists( $file ) ) {

			// Get file contents and decode
			$fgc_func = 'file_'.'get_'.'contents';
			$data = $fgc_func( $file );
			
			$data = $this->options_decode( $data );


			// Only if there is data
			if ( !empty( $data ) || is_array( $data ) ) {

				update_option( $this->theme_option_name, $data );

				$this->flag_as_imported['options'] = true;
			}

  		} else {

      		wp_die(
  				esc_html__( 'Theme options Import file could not be found. Please try again.', 'epron-toolkit' ),
  				'',
  				array( 'back_link' => true )
  			);
   		}

    }

    /**
     * Available widgets
     *
     * Gather site's widgets into array with ID base, name, etc.
     * Used by export and import functions.
     *
     * @since 0.0.2
     *
     * @global array $wp_registered_widget_updates
     * @return array Widget information
     */
    function available_widgets() {

    	global $wp_registered_widget_controls;

    	$widget_controls = $wp_registered_widget_controls;

    	$available_widgets = array();

    	foreach ( $widget_controls as $widget ) {

    		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

    			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
    			$available_widgets[$widget['id_base']]['name'] = $widget['name'];

    		}

    	}

    	return  $available_widgets;

    }


    /**
     * Process import file
     *
     * This parses a file and triggers importation of its widgets.
     *
     * @since 0.0.2
     *
     * @param string $file Path to .wie file uploaded
     * @global string $widget_import_results
     */
    function process_widget_import_file( $file ) {

    	// File exists?
    	if ( ! file_exists( $file ) ) {
    		wp_die(
    			esc_html__( 'Widget Import file could not be found. Please try again.', 'epron-toolkit' ),
    			'',
    			array( 'back_link' => true )
    		);
    	}

    	// Get file contents and decode
    	$fgc_func = 'file_'.'get_'.'contents';
		$data = $fgc_func( $file );
    	$data = json_decode( $data );

    	// Delete import file
    	//unlink( $file );

    	// Import the widget data
    	$this->widget_import_results = $this->import_widgets( $data );

    }


    /**
     * Import widget JSON data
     *
     * @since 0.0.2
     * @global array $wp_registered_sidebars
     * @param object $data JSON widget data from .json file
     * @return array Results array
     */
    public function import_widgets( $data ) {

    	global $wp_registered_sidebars;

    	// Have valid data?
    	// If no data or could not decode
    	if ( empty( $data ) || ! is_object( $data ) ) {
    		return;
    	}

    	// Hook before import
    	$data = apply_filters( 'Muttley_theme_import_widget_data', $data );

    	// Get all available widgets site supports
    	$available_widgets = $this->available_widgets();

    	// Get all existing widget instances
    	$widget_instances = array();
    	foreach ( $available_widgets as $widget_data ) {
    		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
    	}

    	// Begin results
    	$results = array();

    	// Loop import data's sidebars
    	foreach ( $data as $sidebar_id => $widgets ) {

    		// Skip inactive widgets
    		// (should not be in export file)
    		if ( 'wp_inactive_widgets' === $sidebar_id ) {
    			continue;
    		}

    		// Check if sidebar is available on this site
    		// Otherwise add widgets to inactive, and say so
    		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
    			$sidebar_available = true;
    			$use_sidebar_id = $sidebar_id;
    			$sidebar_message_type = 'success';
    			$sidebar_message = '';
    		} else {
    			$sidebar_available = false;
    			$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
    			$sidebar_message_type = 'error';
    			$sidebar_message = esc_html__( 'Sidebar does not exist in theme (using Inactive)', 'epron-toolkit' );
    		}

    		// Result for sidebar
    		$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
    		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
    		$results[$sidebar_id]['message'] = $sidebar_message;
    		$results[$sidebar_id]['widgets'] = array();

    		// Loop widgets
    		foreach ( $widgets as $widget_instance_id => $widget ) {

    			$fail = false;

    			// Get id_base (remove -# from end) and instance ID number
    			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
    			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

    			// Does site support this widget?
    			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
    				$fail = true;
    				$widget_message_type = 'error';
    				$widget_message = esc_html__( 'Site does not support widget', 'epron-toolkit' ); // explain why widget not imported
    			}

    			// Filter to modify settings before import
    			// Do before identical check because changes may make it identical to end result (such as URL replacements)
    			$widget = apply_filters( 'Muttley_theme_import_widget_settings', $widget );

    			// Does widget with identical settings already exist in same sidebar?
    			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

    				// Get existing widgets in this sidebar
    				$sidebars_widgets = get_option( 'sidebars_widgets' );
    				$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

    				// Loop widgets with ID base
    				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
    				foreach ( $single_widget_instances as $check_id => $check_widget ) {

    					// Is widget in same sidebar and has identical settings?
    					if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget === $check_widget ) {

    						$fail = true;
    						$widget_message_type = 'warning';
    						$widget_message = esc_html__( 'Widget already exists', 'epron-toolkit' ); // explain why widget not imported

    						break;

    					}

    				}

    			}

    			// No failure
    			if ( ! $fail ) {

    				// Add widget instance
    				$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
    				$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
    				$single_widget_instances[] = (array) $widget; // add it

					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );

    				// Assign widget instance to sidebar
    				$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
    				$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
    				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
    				update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

    				// Success message
    				if ( $sidebar_available ) {
    					$widget_message_type = 'success';
    					$widget_message = esc_html__( 'Imported', 'epron-toolkit' );
    				} else {
    					$widget_message_type = 'warning';
    					$widget_message = esc_html__( 'Imported to Inactive', 'epron-toolkit' );
    				}

    			}

    			// Result for widget instance
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = $widget->title ? $widget->title : esc_html__( 'No Title', 'epron-toolkit' ); // show "No Title" if widget instance is untitled
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;

    		}

    	}

		$this->flag_as_imported['widgets'] = true;


    	// Return results
    	return $results;

    }

    /**
     * Helper function to return option tree decoded strings
     *
     * @return    string
     *
     * @access    public
     * @since     0.0.3
     * @updated   0.0.3.1
     */
    public function options_decode( $value ) {
		
		$prepared_data = maybe_unserialize( $value );
		
		return $prepared_data;

    }

}//class