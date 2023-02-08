<?php
/**
 * Rascals MetaBox
 *
 * Register Page Metabox
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/* ==================================================
  Metaboxes options
================================================== */
function epron_toolkit_mb_page() {

	$rascals_mb = EpronToolkit::getInstance()->metaboxes;

	/* ==================================================
	  Page 
	================================================== */

	/* Meta info */ 
	$meta_info = array(
		'title' => esc_html__( 'Page Options', 'epron-toolkit'), 
		'id'    =>'rascals_mb_page', 
		'page'  => array(
			'page'
		), 
		'context'  => 'normal', 
		'priority' => 'high', 
		'callback' => '', 
		'template' => array( 
			'default'
		),
	);

	/* Box Filter */
	if ( has_filter( 'rascals_mb_page_box' ) ) {
		$meta_info = apply_filters( 'rascals_mb_page_box', $meta_info );
	}

	/* Meta options */
	$meta_options = array(


		/* TAB: CONTENT
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Content', 'epron-toolkit' ),
			'id'   => 'tab-content',
			'type' => 'tab_open',
		),

			/* Header Layout */
			array(
				'name'   => esc_html__( 'Hero Layout', 'epron-toolkit' ),
				'id'     => '_hero_layout',
				'type'   => 'select_image',
				'std'    => 'default',
				'images' => array(
					array( 
						'id'    => 'default', 
						'title' => esc_html__( 'Hero section with image background and title.', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/header-type-5.png'
					),
					array( 
						'id'    => 'page_builder', 
						'title' => esc_html__( 'Hero section with custom background created via page builder', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/header-type-3.png'
					),
					array( 
						'id'    => 'simple', 
						'title' => esc_html__( 'Simple header with solid background color', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/header-type-4.png'
					),

				),
				'desc' => esc_html__( 'Choose the page layout. When is selected "Page Builder", it will switch to a full width layout (with no sidebar and title). If you want to use a sidebar with the page builder please use the Widget Sidebar block', 'epron-toolkit' ),
			),

			/* Hero Image */
			array(
				'name'   => esc_html__( 'Hero Image', 'epron-toolkit' ),
				'id'     => '_hero_image',
				'type'   => 'add_image',
				'source' => 'media_libary', // all, media_libary, external_link
				'desc'   => esc_html__('Use image that are at least 1920 x 1080 pixels or higher for the best display.', 'epron-toolkit' ),
				'separator' => false,
				'dependency' => array(
					"element" => '_hero_layout',
					"value"   => array( 'default' )
		    	),
			),

			/* Hero Top Padding */
			array(
				'name'       => esc_html__( 'Hero Top Padding', 'epron-toolkit' ),
				'id'         => '_hero_top_margin',
				'type'       => 'range',
				'max'        => '600',
				'min'        => '0',
				'unit'       => 'px',
				'step'       => '10',
				'std'        => '40',
				'desc'       => esc_html__('Set top padding between title and top of the hero area.', 'epron-toolkit' ),
				'separator' => false,
				'dependency' => array(
					"element" => '_hero_layout',
					"value"   => array( 'default' )
		    	)
			),

			/* Hero Bottom Padding */
			array(
				'name'       => esc_html__( 'Hero Bottom Padding', 'epron-toolkit' ),
				'id'         => '_hero_bottom_margin',
				'type'       => 'range',
				'max'        => '600',
				'min'        => '0',
				'unit'       => 'px',
				'step'       => '10',
				'std'        => '40',
				'desc'       => esc_html__('Set bottom padding between title and bottom of the hero area.', 'epron-toolkit' ),
				'separator' => false,
				'dependency' => array(
					"element" => '_hero_layout',
					"value"   => array( 'default' )
		    	)
			),

			/* Hero Position */
			array(
				'name'    => esc_html__( 'Hero Image Position', 'epron-toolkit' ),
				'id'      => '_hero_bg_position',
				'type'    => 'select',
				'std'     => 'top',
				'options' => array(
					array( 'name' => 'Top', 'value' => 'top' ), 
					array( 'name' => 'Center', 'value' => 'center' ),
					array( 'name' => 'Bottom', 'value' => 'bottom' ),
				),
				'desc'       => esc_html__( 'Set background vertical position.', 'epron-toolkit' ),
				'separator' => false,
				'dependency' => array(
					"element" => '_hero_layout',
					"value"   => array( 'default' )
		    	)
			),

			/* Hero Subtitle */
			array(
				'name'      => esc_html__( 'Hero Subtitle', 'epron-toolkit' ),
				'id'        => '_hero_subtitle',
				'type'      => 'text',
				'std'       => '',
				'separator' => true,
				'desc'      => esc_html__( 'Show extra title under main title.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_hero_layout',
					"value"   => array( 'default', 'simple' )
		    	)
			),

			/* Import: Page layout */
			epron_toolkit_mb_common( 'content_layout' ),

			/* Content Background */
			array(
				'name'      => esc_html__( 'Content Background', 'epron-toolkit' ),
				'id'        => '_content_bg',
				'type'      => 'bg_generator',
				'std'       => '',
				'separator' => true,
				'desc'      => esc_html__( 'Add background image.', 'epron-toolkit' ),
			),

			/* Overlay header */
			array(
				'name'    => esc_html__( 'Show Overlay Header', 'epron-toolkit' ),
				'id'      => '_overlay_header',
				'type'    => 'switch_button',
				'std'     => 'no',
				'options' => array(
					array( 'name' => 'On', 'value' => 'yes' ), // ON
					array( 'name' => 'Off', 'value' => 'no' ) // OFF
				),
				'separator'  => false,
				'desc'       => esc_html__( 'Show overlay header instead of the classic header. When scrolling, the overlay header changes to classic.', 'epron-toolkit' ),
			),

		array(
			'type' => 'tab_close'
		),

		
		/* Import: Footer Section */
		epron_toolkit_mb_common( 'footer_tab' ),

		/* Import: Facebook Sharing */
		epron_toolkit_mb_common( 'facebook_sharing_tab' ),
		
	);

	/* Options Filter */
	if ( has_filter( 'rascals_mb_page_opts' ) ) {
		$meta_options = apply_filters( 'rascals_mb_page_opts', $meta_options );
	}

	/* Add class instance */
	$rascals_mb_page = new RascalsBox( $meta_options, $meta_info );
		
}

return epron_toolkit_mb_page();