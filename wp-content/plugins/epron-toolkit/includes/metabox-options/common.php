<?php
/**
 * Rascals MetaBox
 *
 * Register Post Metabox
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Return Intro options depend on displayed page or post
 * @return array
 */
function epron_toolkit_mb_intro_opts(){
 	global $post, $title, $action, $current_screen;

	/* Get post/page data */
	$template_name = '';
	$post_type = '';

	if ( isset( $_GET['post'] ) ) { 
		$template_name = get_post_meta( $_GET['post'], '_wp_page_template', true );
		$post_type = get_post_type( $_GET['post'] );
	} elseif ( isset( $_GET['post_type'] ) ) {
		$post_type = $_GET['post_type'];
	} 

	// Intro type
	$intro_type = array(
		array( 'name' => esc_html__( 'Disabled', 'epron-toolkit' ), 'value' => 'disabled' )
	);

	/* Special options for posts and pages templates */
	if ( $template_name == 'page-templates/test.php' ) {
		$intro_type[] = array( 
			array( 'name' => esc_html__( 'Full Screen YouTube Background', 'epron-toolkit' ), 'value' => 'intro_youtube_fullscreen' )

		 );
	};

	if ( $post_type == 'test' ) {
		$intro_type[] = array( 'name' => esc_html__( 'Artist Profile', 'epron-toolkit' ), 'value' => 'artist_profile' );
	};

	
	return $intro_type;
}


/**
 * Display common metaboxes
 * @param  string $tab_name
 * @return array   
 */
function epron_toolkit_mb_common( $tab_name = null ) {

	$rascals_mb = EpronToolkit::getInstance()->metaboxes;
	
	$common_metaboxes = array(

		/* Content layout
		 -------------------------------- */
		'content_layout' => array(

			// Page layout
			array(
				'name'   => esc_html__( 'Content Layout', 'epron-toolkit' ),
				'id'     => '_content_layout',
				'type'   => 'select_image',
				'std'    => 'right_sidebar',
				'images' => array(
					array( 
						'id'    => 'left_sidebar', 
						'title' => esc_html__( 'Sidebar Left', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/left-sidebar.png'
					),
					array( 
						'id'    => 'narrow', 
						'title' => esc_html__( 'Narrow Layout', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/narrow.png'
					),
					array( 
						'id'    => 'wide', 
						'title' => esc_html__( 'Wide Layout', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/wide.png'
					),
					array( 
						'id'    => 'right_sidebar', 
						'title' => esc_html__( 'Sidebar Right', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/right-sidebar.png'
					),
					array( 
						'id'    => 'page_builder', 
						'title' => esc_html__( 'Create page through The Page Builder', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/page-builder.png'
					),

				),
				'desc' => esc_html__( 'Choose the page layout. When is selected "Page Builder", it will switch to a full width layout (with no sidebar and title). If you want to use a sidebar with the page builder please use the Widget Sidebar block', 'epron-toolkit' )
			),

			// Sidebars  
			array(
				'name'       => esc_html__( 'Custom Sidebar', 'epron-toolkit' ),
				'id'         => '_custom_sidebar',
				'type'       => 'select_array',
				'std'        => '',
				'options'	 => array(
					array( 'name' => esc_html__( 'Primary Sidebar', 'epron-toolkit' ), 'value' => '_default' ),
				),
				'array' 	 => $rascals_mb->getSidebars( 'epron_panel_opts' ),
				'key' 		 => 'value',
				'separator'  => true,
				'desc'       => esc_html__( 'Select custom or primary sidebar.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_content_layout',
					"value"   => array( 'left_sidebar', 'right_sidebar' )
			    )
			), 

		),


		/* Content layout for templates
		 -------------------------------- */
		'content_layout_template' => array(

			// Page layout
			array(
				'name'   => esc_html__( 'Content Layout', 'epron-toolkit' ),
				'id'     => '_content_layout',
				'type'   => 'select_image',
				'std'    => 'right_sidebar',
				'images' => array(
					array( 
						'id'    => 'left_sidebar', 
						'title' => esc_html__( 'Sidebar Left', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/left-sidebar.png'
					),
					array( 
						'id'    => 'narrow', 
						'title' => esc_html__( 'Narrow Layout', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/narrow.png'
					),
					array( 
						'id'    => 'wide', 
						'title' => esc_html__( 'Wide Layout', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/wide.png'
					),
					array( 
						'id'    => 'right_sidebar', 
						'title' => esc_html__( 'Sidebar Right', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/right-sidebar.png'
					),

				),
				'desc' => esc_html__( 'Choose the page layout. When is selected "Page Builder", it will switch to a full width layout (with no sidebar and title). If you want to use a sidebar with the page builder please use the Widget Sidebar block', 'epron-toolkit' )
			),

			// Sidebars  
			array(
				'name'       => esc_html__( 'Custom Sidebar', 'epron-toolkit' ),
				'id'         => '_custom_sidebar',
				'type'       => 'select_array',
				'std'        => '',
				'options'	 => array(
					array( 'name' => esc_html__( 'Primary Sidebar', 'epron-toolkit' ), 'value' => '_default' ),
				),
				'array' 	 => $rascals_mb->getSidebars( 'epron_panel_opts' ),
				'key' 		 => 'value',
				'separator'  => true,
				'desc'       => esc_html__( 'Select custom or primary sidebar.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_content_layout',
					"value"   => array( 'left_sidebar', 'right_sidebar' )
			    )
			), 

		),


		/* TAB: FOOTER SECTION
		 -------------------------------- */
		'footer_tab' => array(
			array(
				'name' => esc_html__( 'Footer Section', 'epron-toolkit' ),
				'id'   => 'tab-footer',
				'type' => 'tab_open',
			),

				/* KC Section */ 
				array(
					'name'       => esc_html__( 'King Composer Section', 'epron-toolkit' ),
					'id'         => '_kc_section',
					'type'       => 'select',
					'std'        => '',
					'options'	 => $rascals_mb->getKCSections(),
					'separator'  => true,
					'desc'       => esc_html__( 'You can add your own King Composer Section here. Sections are created in King Composer > Sections Manager.', 'epron-toolkit' ),
				),	

			array(
				'type' => 'tab_close'
			),
		),


		/* TAB: FACEBOOK SHARING
		 -------------------------------- */
		'facebook_sharing_tab' => array(
			array(
				'name' => esc_html__( 'Facebook Sharing', 'epron-toolkit' ),
				'id'   => 'tab-share',
				'type' => 'tab_open',
			),
				/* Is Facebook sharing */
				array(
					'name'    => esc_html__( 'Facebook Sharing', 'epron-toolkit' ),
					'id'      => '_fb_sharing',
					'type'    => 'switch_button',
					'std'     => get_theme_mod( 'fb_sharing', true ),
					'options' => array(
						array( 'name' => 'On', 'value' => true ), // ON
						array( 'name' => 'Off', 'value' => '0' ) // OFF
					),
					'separator' => true,
					'desc'      => esc_html__( 'Show or hide Facebook share options (head tags). Tip: This option can be set as the default in Theme Customizer > Single Post', 'epron-toolkit' ),
				),

				/* Image */
				array(
					'name'       => esc_html__( 'Image', 'epron-toolkit' ),
					'id'         => 'share_image',
					'type'       => 'add_image',
					'source'     => 'media_libary', // all, media_libary, external_link
					'desc'       => esc_html__('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. If share data isn\'t visible on Facebook, please use this link:', 'epron-toolkit' ) . '<br>'.'<a href="https://developers.facebook.com/tools/debug/" target="_blank">Facbook Debuger</a>',
					'dependency' => array(
						"element" => '_fb_sharing',
						"value"   => array( true )
			    	)
				),

				/* Title */
				array(
					'name'       => esc_html__( 'Title', 'epron-toolkit' ),
					'id'         => '_share_title',
					'type'       => 'text',
					'std'        => '',
					'desc'       => esc_html__( 'A clear title without branding or mentioning the domain itself.', 'epron-toolkit' ),
					'dependency' => array(
						"element" => '_fb_sharing',
						"value"   => array( true )
			    	)
				),

				/* Video */
				array(
					'name'       => esc_html__( 'Video', 'epron-toolkit' ),
					'id'         => '_share_video',
					'type'       => 'text',
					'std'        => '',
					'desc'       => esc_html__( 'Video URL.', 'epron-toolkit' ),
					'dependency' => array(
						"element" => '_fb_sharing',
						"value"   => array( true )
			    	)
				),

				/* Short Description */
				array(
					'name'       => esc_html__( 'Short Description', 'epron-toolkit' ),
					'id'         => '_share_description',
					'type'       => 'textarea',
					'tinymce'    => 'false',
					'std'        => '',
					'height'     => '80',
					'desc'       => esc_html__( 'A clear description, at least two sentences long.', 'epron-toolkit' ),
					'dependency' => array(
						"element" => '_fb_sharing',
						"value"   => array( true )
			    	)
				),

			array(
				'type' => 'tab_close'
			),
		)

	);

	if ( isset( $common_metaboxes[$tab_name] ) ) {
		return  $common_metaboxes[$tab_name];
	} 

	return;
		
}