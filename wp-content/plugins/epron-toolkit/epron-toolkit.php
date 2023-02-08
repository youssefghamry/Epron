<?php
/**
 * Plugin Name:       Epron Toolkit
 * Plugin URI:        http://rascalsthemes.com/
 * Description:       This is a Epron Toolkit plugin for the Epron WordPress theme. This plugin extends theme functionality. Is a required part of the theme.
 * Version:           1.2.1
 * Author:            Rascals Themes
 * Author URI:        http://rascalsthemes.com
 * Text Domain:       epron-toolkit
 * License:           See "Licensing" Folder
 * License URI:       See "Licensing" Folder
 * Domain Path:       /languages
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'EpronToolkit' ) ) {

	/**
	 * Main EpronToolkit Class
	 *
	 * Contains the main functions for EpronToolkit
	 *
	 * @package         EpronToolkit
	 * @author          Rascals Themes
	 * @copyright       Rascals Themes
 	 * @version       	1.0.2
	 */
	class EpronToolkit {

		/* instances
		  -------------------------------- */ 
		public $artists = null;
		public $releases = null;
		public $gallery = null;
		// 
		public $events = null;
		public $scamp_player = null;
		// 
		public $customizer = null;
		public $kc = null;
		public $shortcodes = null;
		public $admin_pages = null;
		public $importer = null;
		public $metaboxes = null;
		public $widgets = null;


		/* Other variables
		 -------------------------------- */
		
		// @var string
		public $theme_slug = 'epron';

		// @var string
		public $theme_panel = 'epron_panel_opts';

		// @var string
		public $version = '1.0.0';

		// @var integer
		public static $id = 0;

		// @var Single instance of the class
		private static $_instance;


		/**
		 * Epron Toolkit Instance
		 *
		 * Ensures only one instance of Epron Toolkit is loaded or can be loaded.
		 *
		 * @static
		 * @return Epron Toolkit - Main instance
		 */
		public static function getInstance() {
			if ( ! ( self::$_instance instanceof self ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Epron Toolkit Constructor.
		 * @return void
		 */
		public function __construct() {
			
			if ( defined( 'RASCALS_TOOLKIT_PATH' ) ) {
				add_action( 'admin_notices', array( $this,  'noticeToolkitActivated' ) );
				return;
			}

			if ( get_option( 'rascals_toolkit' ) === false && get_option( 'rascals_toolkit' ) !== $this->theme_slug ) {
				add_action( 'admin_notices', array( $this,  'noticeOldTheme' ) );
				return;
			}

			// If old plugin is activated
			if ( in_array( 'rt-epron-extensions/rt-epron-extensions.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				add_action( 'admin_notices', array( $this,  'noticeOldPlugin' ) );
				return;
			}

			// Set localisation
			$this->loadPluginTextdomain();

			// Load Frontend scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			// Get theme panel options
			$option_name = $this->theme_panel;
			$this->theme_panel = get_option( $option_name );

			$this->defineConstants();
			$this->initAdmin();
			$this->initTools();
			$this->initHooks();

			do_action( 'epron_toolkit_loaded' );
		}

		/**
		 * Include required core files used in admin and on the backend.
		 * @return void
		 */
		public function initAdmin() {

			if ( ! is_admin() ) {
				return;
			}

			// Admin Pages
			include_once( $this->pluginPath() . '/includes/class-admin.php' );
			$this->admin_pages = new RascalsAdminPages();

			// Importer
			include_once( $this->pluginPath() . '/includes/importer/class-importer.php' );
			include_once( $this->pluginPath() . '/includes/importer/class-importer-data.php' );
			$this->importer = new RascalsImporterData;

			// Metaboxes
			include_once( $this->pluginPath() . '/includes/class-register-metaboxes.php' );
			$this->metaboxes = new RascalsRegisterMetaBoxes();
			
		}


		/**
		 * Include required core files used in admin and on the frontend.
		 * @return void
		 */
		public function initTools() {
			
			// Load toolkit functions
			include_once( $this->pluginPath() . '/includes/functions-toolkit.php' );
			

			// Register custom post types
			include_once( $this->pluginPath() . '/includes/class-cpt.php' );

			include_once( $this->pluginPath() . '/includes/post-types/class-releases.php' );
			$this->releases = new RascalsRegisterReleases();

			include_once( $this->pluginPath() . '/includes/post-types/class-artists.php' );
			$this->artists = new RascalsRegisterArtists();
			
			include_once( $this->pluginPath() . '/includes/post-types/class-gallery.php' );
			$this->gallery = new RascalsRegisterGallery();
			
			include_once( $this->pluginPath() . '/includes/post-types/class-events-manager.php' );
			$this->events = new RascalsEventsManager();
			
			include_once( $this->pluginPath() . '/includes/scamp-player/class-scamp-player.php' );
			$this->scamp_player = new RascalsScampPlayer( array( 
				'theme_name' => 'epron', 
				'theme_panel' => $this->theme_panel,
				'post_type' => 'epron_tracks'
			) );

			// Twitter
			include_once( $this->pluginPath() . '/includes/class-twitter.php' );

			// Resize
			include_once( $this->pluginPath() . '/includes/class-image-resize.php' );

			// King Composer
			include_once( $this->pluginPath() . '/includes/class-kc.php' );
			$this->kc = new RascalsKC( array( 
				'theme_name'    => 'epron', 
				'theme_panel'   => $this->theme_panel,
				'supported_cpt' => array( 'wp_releases', 'wp_events_manager', 'wp_gallery', 'wp_artists' ),
				'default_fonts' => '{"Open%20Sans":["cyrillic%2Cvietnamese%2Ccyrillic-ext%2Cgreek%2Cgreek-ext%2Clatin%2Clatin-ext","300%2C300italic%2Cregular%2Citalic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic","cyrillic%2Clatin-ext","300%2C300italic%2Cregular%2Citalic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic"]}'
			) );
	
			// Widgets
			include_once( $this->pluginPath() . '/includes/class-widgets.php' );
			add_action( 'widgets_init', array( $this, 'registerWidget' ) );
			$this->widgets = new RascalsWidgets();

			// Super menu
			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
			include_once( $this->pluginPath() . '/includes/super-menu-walker.php' );

			// Revolution Slider
			add_action( 'plugins_loaded', array( $this, 'setRevoSlider' ) );			

			// Customizer Installer
			include_once( $this->pluginPath() . '/includes/class-customizer-install.php' );
			include_once( $this->pluginPath() . '/includes/class-customizer.php' );
			$this->customizer = new RascalsCustomizer();
			
		}


		/**
		 * Register widgets function.
		 *
		 * @return void
		 */
		public function registerWidget() {

			// Recent Posts
			include_once( $this->pluginPath() . '/includes/widgets/class-widget-recent-posts.php' );
			register_widget( 'RascalsRecentPostsWidget' );

			// Instagram feed
			include_once( $this->pluginPath() . '/includes/widgets/class-widget-instafeed.php' );
			register_widget( 'RascalsInstafeedWidget' );

			// AD Block
			include_once( $this->pluginPath() . '/includes/widgets/class-widget-ad.php' );
			register_widget( 'RascalsADWidget' );

			// Twitter
			include_once( $this->pluginPath() . '/includes/widgets/class-widget-twitter.php' );
			register_widget( 'RascalsTwitterWidget' );

			// Flickr
			include_once( $this->pluginPath() . '/includes/widgets/class-widget-flickr.php' );
			register_widget( 'RascalsFlickrWidget' );
		}


		/**
		 * Hook into actions and filters
		 * @return void
		 */
		private function initHooks() {

			add_action( 'admin_bar_menu', array( $this, 'showAdminBar' ), 100 );
			add_action( 'init', array( $this, 'init' ), 0 );
		}


 		/**
 		 * Init hooks when WordPress Initialises.
 		 * @return void
 		 */
		public function init() {

			// Before init action
			do_action( 'before_epron_toolkit_init' );

			// Init action
			do_action( 'epron_toolkit_init' );
		}


		/**
		 * Load scripts
		 * @return array
		 */
		public function enqueue() {

	        wp_enqueue_script( 'magnific-popup', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/vendors/magnific-popup/jquery.magnific-popup.min.js', false, false, true  );
	        wp_enqueue_style( 'magnific-popup' ,  esc_url( RASCALS_TOOLKIT_URL ) . '/assets/vendors/magnific-popup/magnific-popup.css' );

	        wp_enqueue_script( 'smooth-scrollbar' , esc_url( RASCALS_TOOLKIT_URL ) . '/assets/vendors/smooth-scrollbar.min.js' , false, false, true );
	        wp_enqueue_script( 'countdown' , esc_url( RASCALS_TOOLKIT_URL ) . '/assets/vendors/jquery.countdown.min.js' , false, false, true );
	        wp_enqueue_script( 'owl-carousel' , esc_url( RASCALS_TOOLKIT_URL ) . '/assets/vendors/owl.carousel.min.js' , false, false, true );

	    	// main toolkit
			wp_enqueue_style( 'epron-toolkit', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/css/frontend-toolkit.css' );
		   	wp_enqueue_script( 'epron-toolkit', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/js/frontend-toolkit.js' , false, false, true );

			$js_variables = array(
				'plugin_uri' => plugins_url('scamp-player' , esc_url( RASCALS_TOOLKIT_URL ) ),
			);
			wp_localize_script( 'epron-toolkit', 'scamp_vars', $js_variables );
		}


		/**
		 * Define constants
		 * @return void
		 */
		public function defineConstants() {

			// Plugin's directory path.
			define( 'RASCALS_TOOLKIT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

			// Plugin's directory URL.
			define( 'RASCALS_TOOLKIT_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );

		}


		/**
		 * Loads the plugin text domain for translation
		 * @return void
		 */
		public function loadPluginTextdomain() {

			$domain = 'epron-toolkit';
			$locale = apply_filters( 'epron-toolkit', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Display admin notice
		 * @return void
		 */
		public function noticeToolkitActivated(){
		    echo '<div class="notice rascals-notice notice-error is-dismissible">
		          <p><strong>' . esc_html__( 'Rascals Toolkit plugin is already activated, it was probably used by another Rascals Theme. Please dectivate old Toolkit plugin and activate new one.', 'epron-toolkit') . '</strong></p>
		         </div>';
		}


		/**
		 * Display admin notice
		 * @return void
		 */
		public function noticeOldTheme(){
		    echo '<div class="notice rascals-notice notice-error is-dismissible">
		          <p><strong>' . esc_html__( 'Epron Toolkit plugin is not compatible with installed theme. Please deactivate and activate your parent theme again or install compatible plugin with your theme version.', 'epron-toolkit') . '</strong></p>
		         </div>';
		}


		/**
		 * Display admin notice
		 * @return void
		 */
		public function noticeOldPlugin(){
		    echo '<div class="notice rascals-notice notice-error is-dismissible">
		          <p><strong>' . esc_html__( 'Epron Toolkit is currently disabled, please deactivate and remove Epron Extensions plug-in to activate it. This is an outdated version and is not compatible with your theme.', 'epron-toolkit') . '</strong></p>
		         </div>';
			
		}


		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function pluginUrl() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}


		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function pluginPath() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Get the theme option
		 * @return string|bool|array
		 */
		public function get_theme_option( $option, $default = null ) {

			if ( $this->theme_panel === false || ! is_array( $this->theme_panel )  || ! isset( $option ) ) {
				return false;
			}
			if ( isset( $this->theme_panel[ $option ] ) ) {
				return $this->theme_panel[ $option ];
			} elseif ( $default !== null ) {
				return $default;
			} else {	
				return false;
			}
		
		}


		/**
		 * Display escaped text.
		 * @param  $text
		 * @return string
		 */
		public function esc( $text ) {
			$text = preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $text );
			return $text;
		}


		/**
		 * Display escaped text through echo function.
		 * @param  $text
		 * @return string
		 */
		public function e_esc( $text ) {
			echo preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $text );
		}


		/**
		 * Show admin bar hook
		 * @return void
		 */
		public function showAdminBar() {

			global $wp_admin_bar;
		
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}

			$wp_admin_bar->add_menu(
				array( 
					'id'    => 'admin-welcome', 
					'title' => '<span class="ab-icon rascals-admin-link"></span> ' . esc_html__( 'Theme Settings', 'epron-toolkit' ), 
					'href'  => get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . 'admin-welcome'
				)
			);
		}


		/**
		 * Set Revo Slider options for Ajax loader
		 * @version 1.0.0
		 * @return void
		 */
		public function setRevoSlider() {

			if ( class_exists( 'RevSlider' ) && function_exists( 'rev_slider_shortcode' ) ) {
				
				// Only for 6 and above versions
    			if ( defined('RS_REVISION') && version_compare( RS_REVISION, '6.0.0' ) >= 0 ) {
					$ajax = $this->get_theme_option( 'ajaxed', 'off' );

					if ( $ajax === 'on' ) {
						$rev_slider = new RevSlider();

						$rev_opts = $rev_slider->get_global_settings();
						if ( is_array( $rev_opts ) && isset( $rev_opts['include'] ) && $rev_opts['include'] === 'false' ) {
							$rev_opts['include'] = 'true';
							$rev_slider->set_global_settings($rev_opts);

						}
	            	}
	            }
        	}
        }


        /**
		 * Reize Image on fly and save in wp-content
		 * @return array|string
		 */
		static function imageResize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
			/* WPML Fix */
	        if ( defined( 'ICL_SITEPRESS_VERSION' ) ){
	            global $sitepress;
	            $url = $sitepress->convert_url( $url, $sitepress->get_default_language() );
	        }
	        /* WPML Fix */
	        $rascalsResize = RascalsResize::getInstance();
	        return $rascalsResize->process( $url, $width, $height, $crop, $single, $upscale );
		}

	}
}


// Returns the main instance of EpronToolkit to prevent the need to use globals.
function epronToolkit() {
	return EpronToolkit::getInstance();
}

epronToolkit(); // Run