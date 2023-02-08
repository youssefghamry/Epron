<?php

/**
 * Rascals Importer Data
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsImporterData extends RascalsImporter {

	/**
	 * Set framewok
	 *
	 *
	 * @since 0.0.3
	 *
	 * @var string
	 */
	public $theme_options_framework = 'RascalsPanel';


	public $flag_as_imported;

	/**
	 * Show Console
	 *
	 *
	 * @since 0.0.3
	 *
	 * @var string
	 */
	public $wp_importer_console = 'hidden'; // hidden or empty


	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 0.0.1
	 *
	 * @var object
	 */
	private static $instance;


	/**
	 * Set the key to be used to store theme options
	 *
	 * @since 0.0.2
	 *
	 * @var array
	 */
	public $importer_options;

	/**
	 * Set the key to be used to store theme options
	 *
	 * @since 0.0.2
	 *
	 * @var string
	 */
	public $theme_option_name  = 'epron_panel_opts'; 


	/**
	 * Holds a copy of the widget settings
	 *
	 * @since 0.0.2
	 *
	 * @var string
	 */
	public $widget_import_results;

	/**
	 * Required plugins
	 *
	 * @since 0.0.3
	 *
	 * @var array
	 */
	public $required_plugins;

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		// Set demos path
		$this->demo_files_path = dirname(__FILE__) . '/demo-files/'; 

		
		/* ==================================================
		   Main Demos
		================================================== */ 
		$this->importer_options = array(



			/* DEMO1 - Dark
			 -------------------------------- */
			array(
				'id'		   	 	 => 'demo1',
				'name'   	 		 => 'Dark Version',
				'thumb'				 => 'demo1/thumb.jpg', 
				'content_files'      =>  array(
					array(
						'file_path' => 'demo1/content_media.xml',
					),
					array(
						'file_path' => 'demo1/content.xml',
					),
				),
				'widget_file'        => 'demo1/widgets.wie',
				'customizer_file'    => 'demo1/customizer.dat',
				'panel_options_file' => 'demo1/theme_options.txt',
				'rev_sliders_files'	 => 'rev_sliders',
				'import_notice'      => '',
			),


			/* DEMO2 - Light
			 -------------------------------- */
			array(
				'id'		   	 	 => 'demo2',
				'name'   	 		 => 'Light Version',
				'thumb'				 => 'demo2/thumb.jpg', 
				'content_files'      =>  array(
					array(
						'file_path' => 'demo2/content_media.xml',
					),
					array(
						'file_path' => 'demo2/content.xml',
					),
				),
				'widget_file'        => 'demo2/widgets.wie',
				'customizer_file'    => 'demo2/customizer.dat',
				'panel_options_file' => 'demo2/theme_options.txt',
				'rev_sliders_files'	 => 'rev_sliders',
				'import_notice'      => '',
			),


		);


		$this->required_plugins = array(
			array(
			    	'path' => 'kingcomposer/kingcomposer.php',
			    	'name' => esc_html__( 'KingComposer - The most professional WordPress page builder plugin, it\'s weight and high efficiency to help you build any layout design quickly.', 'epron-toolkit' )
			    ),
		    array(
		    	'path' => 'kirki/kirki.php',
		    	'name' => esc_html__( 'Kirki - Theme customization options.', 'epron-toolkit' )
		    ),
		    array(
		    	'path' => 'epron-toolkit/epron-toolkit.php',
		    	'name' => esc_html__( 'Epron Toolkit - This is a complimentary plugin for the Rascals WordPress themes. You can use it to create, manage and update Custom Posts Types, Widgets, Modules, Customizer Options.', 'epron-toolkit' )
		    )
		);

		self::$instance = $this;	
		parent::__construct();

	}


	/**
	 * Run this function before demo import
	 * @param  string $demo_id - Demo imported ID
	 * @return void
	 */
	public function before_import(){

		
		/* Remove Default Widgets from sidebar
		 -------------------------------- */
		
		/* Sidebar slugname */
		$sidebar = 'primary-sidebar';

		$sidebars_widgets = get_option( 'sidebars_widgets' );
	 	if ( isset( $sidebars_widgets ) && is_array( $sidebars_widgets ) ) {
	 		if ( isset( $sidebars_widgets[$sidebar] ) ) {
	 			$sidebars_widgets[$sidebar] = array();
	 			update_option( 'sidebars_widgets', $sidebars_widgets );
	 		}
	 	} 
	}


	/**
	 * Run this function after demo import
	 * @param  string $demo_id - Demo imported ID
	 * @return void
	 */
	public function after_import($demo_id){


		/* Set demo name
		 -------------------------------- */  

		$demo_name = 'main';

		switch ($demo_id) {
			case 'demo1':
			case 'demo2':
				$demo_name = 'main';
				break;
			
			default:
				$demo_name = 'main';
				break;
		}


		/* ==================================================
		  Main DEMO 
		================================================== */

		if ( $demo_name == 'main' ) {

			var_dump($demo_name);
			/* Set homepage
			 -------------------------------- */

			/* Homepage slugname */

			/* Get home page name depends on demo */
			switch ($demo_id) {
				case 'demo1':
				case 'demo2':
					$homepage_name = 'Home (Revolution Slider)';
					break;
				default:
					
					break;
			}

			$home_id = get_page_by_title( $homepage_name );
			if ( isset( $home_id ) ) {
				update_option( 'page_on_front', $home_id->ID );
				update_option( 'show_on_front', 'page' );
			}

			
			/* Set Menus Locations
			 -------------------------------- */

			/* Get menu name depends on demo */
			$menu_name_1     = 'Main Menu';
			$menu_location_1 = 'main';

			$menu_name_2     = 'Top menu';
			$menu_location_2 = 'top_menu';

			$menu_name_3     = 'Footer Menu';
			$menu_location_3 = 'footer_menu';

			$menu_1 = get_term_by( 'name', $menu_name_1, 'nav_menu' );
			$menu_2 = get_term_by( 'name', $menu_name_2, 'nav_menu' );
			$menu_3 = get_term_by( 'name', $menu_name_3, 'nav_menu' );

			$new_menu = array();
	
			if ( $menu_1 ) {
				$new_menu[$menu_location_1]	= $menu_1->term_id;
			}
			if ( $menu_2 ) {	
				$new_menu[$menu_location_2]	= $menu_2->term_id;
			}
			if ( $menu_3 ) {	
				$new_menu[$menu_location_3]	= $menu_3->term_id;
			}

			set_theme_mod( 'nav_menu_locations', $new_menu );

			$this->flag_as_imported['menus'] = true;


			/* Set Super Menu 
			 -------------------------------- */
			$menu_obj = wp_get_nav_menu_items( $menu_1 );
			foreach ( $menu_obj as $menu_item ) {

				/* Reviews */
				if ( $menu_item->title == 'Recent' ){
					update_post_meta( $menu_item->ID, '_menu_item_super_menu_type', 'posts__slider' );
					update_post_meta( $menu_item->ID, '_menu_item_super_menu_cat_id', 'news' );
				}
				if ( $menu_item->title == 'Artists' ){
					update_post_meta( $menu_item->ID, '_menu_item_super_menu_type', 'super-menu' );
				}
				if ( $menu_item->title == 'Releases' ){
					update_post_meta( $menu_item->ID, '_menu_item_super_menu_type', 'super-menu' );
				}
				if ( $menu_item->title == 'Events' ){
					update_post_meta( $menu_item->ID, '_menu_item_super_menu_type', 'super-menu' );
				}
				if ( $menu_item->title == 'News' ){
					update_post_meta( $menu_item->ID, '_menu_item_super_menu_type', 'super-menu' );
				}

			}

			

		} // end Main demo

	}

}