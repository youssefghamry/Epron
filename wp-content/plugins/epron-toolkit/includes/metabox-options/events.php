<?php
/**
 * Rascals MetaBox
 *
 * Register Events Metabox
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
function epron_toolkit_mb_events() {

	$rascals_mb = EpronToolkit::getInstance()->metaboxes;

	/* ==================================================
	  Single Event
	================================================== */

	/* Meta info */
	$meta_info = array(
		'title' => esc_html__( 'Event Options', 'epron-toolkit'),
		'id'    =>'rascals_mb_events_box',
		'page'  => array(
			'wp_events_manager'
		),
		'context'  => 'normal',
		'priority' => 'high',
		'callback' => '',
		'template' => array(
			'post'
		),
		'admin_path' => plugin_dir_url( __FILE__ ),
		'admin_uri'  => plugin_dir_path( __FILE__ ),
		'admin_dir'  => '',
		'textdomain' => 'epron-toolkit'
	);

	/* Box Filter */
	if ( has_filter( 'rascals_mb_events_box' ) ) {
		$meta_info = apply_filters( 'rascals_mb_events_box', $meta_info );
	}

	/* Meta options */
	$meta_options = array(


		/* TAB: DATE
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Date/Time', 'epron-toolkit' ),
			'id'   => 'tab-date',
			'type' => 'tab_open',
		),

			array(
				'name' => esc_html__( 'Date', 'epron-toolkit' ),
				'id' => array(
					array('id' => '_event_date_start', 'std' => date('Y-m-d')),
					array('id' => '_event_date_end', 'std' => date('Y-m-d'))
				),
				'type' => 'date_range',
				'desc' => esc_html__( 'Enter the event date; eg 2010-09-11', 'epron-toolkit' )
			),
			array(
				'name' => esc_html__( 'Time', 'epron-toolkit' ),
				'id' => array(
					array('id' => '_event_time_start', 'std' => '21:00'),
					array('id' => '_event_time_end', 'std' => '00:00')
				),
				'type' => 'time_range',
				'desc' => esc_html__( 'Enter the event time; eg 21:00 or 09:00 pm', 'epron-toolkit' ),
			),
			array(
				'name' => esc_html__( 'Repeat', 'epron-toolkit' ),
				'type' => 'select',
				'id' => '_repeat_event',
				'std' => 'default',
				'options' => array(
					array('name' => esc_html__( 'None', 'epron-toolkit' ), 'value' => 'none'),
					array('name' => esc_html__( 'Weekly', 'epron-toolkit' ), 'value' => 'weekly')
				),
				'desc' => esc_html__( 'Repeat event.', 'epron-toolkit' )
			),
			array(
				'name' => esc_html__( 'Every', 'epron-toolkit' ),
				'id' => '_every',
				'type' => 'range',
				'min' => 1,
				'max' => 52,
				'unit' => esc_html__( 'week(s)', 'epron-toolkit' ),
				'std' => '1',
				'desc' => esc_html__( 'Repeat event every week(s).', 'epron-toolkit' ),
				'dependency' => array(
				    "element" => '_repeat_event',
				    "value" => array( 'weekly' )
				)
			),
			array(
				'name' => esc_html__( 'Day(s)', 'epron-toolkit' ),
				'id' => '_weekly_days',
				'type' => 'multiselect',
				'std' => array('friday'),
				'options' => array(
					array('name' => esc_html__( 'Monday', 'epron-toolkit' ), 'value' => 'monday'),
					array('name' => esc_html__( 'Tuesday', 'epron-toolkit' ), 'value' => 'tuesday'),
					array('name' => esc_html__( 'Wednesday', 'epron-toolkit' ), 'value' => 'wednesday'),
					array('name' => esc_html__( 'Thursday', 'epron-toolkit' ), 'value' => 'thursday'),
					array('name' => esc_html__( 'Friday', 'epron-toolkit' ), 'value' => 'friday'),
					array('name' => esc_html__( 'Saturday', 'epron-toolkit' ), 'value' => 'saturday'),
					array('name' => esc_html__( 'Sunday', 'epron-toolkit' ), 'value' => 'sunday'),
				),
				'desc' => esc_html__( 'Please use the CTRL key (PC) or COMMAND key (Mac) to select multiple items.', 'epron-toolkit' ),
				'dependency' => array(
				    'element' => '_repeat_event',
				    'value' => array( 'weekly' )
				)
			),

		array(
			'type' => 'tab_close'
		),


		/* TAB: EVENT DETAIL
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Details', 'epron-toolkit' ),
			'id'   => 'tab-details',
			'type' => 'tab_open',
		),

			/* Event Location */
			array(
				'name'    => esc_html__( 'Event Location', 'epron-toolkit' ),
				'id'      => '_event_location',
				'type'    => 'textarea',
				'tinymce' => 'false',
				'std'     => '',
				'height'  => '40',
				'desc'    => esc_html__( 'Enter the event location; eg.: Amsterdam, Holland', 'epron-toolkit' ),
			),

		array(
			'type' => 'tab_close'
		),


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

			/* Fullwidth */
			array(
				'name'    => esc_html__( 'Full width?', 'epron-toolkit' ),
				'id'      => '_fullwidth',
				'type'    => 'switch_button',
				'std'     => '0',
				'options' => array(
					array( 'name' => 'On', 'value' => 'full-width' ), // ON
					array( 'name' => 'Off', 'value' => '0' ) // OFF
				),
				'separator' => true,
				'desc'      => esc_html__( 'Show full width images grid.', 'epron-toolkit' ),
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
	if ( has_filter( 'rascals_mb_events_opts' ) ) {
		$meta_options = apply_filters( 'rascals_mb_events_opts', $meta_options );
	}

	/* Add class instance */
	$rascals_mb_events = new RascalsBox( $meta_options, $meta_info );



	/* ==================================================
	  Events Template
	================================================== */

	/* Meta info */
	$meta_info = array(
		'title' => esc_html__( 'Events Options', 'epron-toolkit'),
		'id'    =>'rascals_mb_events_template',
		'page'  => array(
			'page'
		),
		'context'  => 'normal',
		'priority' => 'high',
		'callback' => '',
		'template' => array(
			'template-events.php'
		),
		'textdomain' => 'epron-toolkit'
	);

	/* Box Filter */
	if ( has_filter( 'rascals_mb_events_template_box' ) ) {
		$meta_info = apply_filters( 'rascals_mb_events_template_box', $meta_info );
	}

	/* Meta options */
	$meta_options = array(


		/* TAB: GENERAL
		 -------------------------------- */
		array(
			'name' => esc_html__( 'General', 'epron-toolkit' ),
			'id'   => 'tab-general',
			'type' => 'tab_open',
		),

			/* Page Builder */
			array(
				'name'   => esc_html__( 'Page Builder', 'epron-toolkit' ),
				'id'     => '_page_builder',
				'type'   => 'select_image',
				'std'    => 'off',
				'images' => array(
					array(
						'id' => 'off',
						'title' => esc_html__( 'Disabled', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/none.png'
					),
					array(
						'id' => 'on',
						'title' => esc_html__( 'Create page through The Page Builder', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/page-builder.png'
					),
				),
				'desc' => esc_html__( 'Enable or disable page builder above main posts loop. When is enabled "Page Builder", it will switch to a full width layout (with no title).', 'epron-toolkit' )
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
						'id'    => 'simple',
						'title' => esc_html__( 'Simple header with solid background color', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/header-type-4.png'
					),

				),
				'desc' => esc_html__( 'Choose the page layout. When is selected "Page Builder", it will switch to a full width layout (with no sidebar and title). If you want to use a sidebar with the page builder please use the Widget Sidebar block', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_page_builder',
					"value"   => array( 'off' )
		    	),
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
				'desc'       => esc_html__( 'Set bottom padding between title and bottom of the hero area.', 'epron-toolkit' ),
				'separator'  => false,
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
			epron_toolkit_mb_common( 'content_layout_template' ),

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

			/* Fullwidth */
			array(
				'name'    => esc_html__( 'Full width?', 'epron-toolkit' ),
				'id'      => '_fullwidth',
				'type'    => 'switch_button',
				'std'     => '0',
				'options' => array(
					array( 'name' => 'On', 'value' => 'full-width' ), // ON
					array( 'name' => 'Off', 'value' => '0' ) // OFF
				),
				'separator' => true,
				'desc'      => esc_html__( 'Show full width images grid.', 'epron-toolkit' ),
			),



		array(
			'type' => 'tab_close'
		),


		/* TAB: LOOP SETTINGS
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Loop Settings', 'epron-toolkit' ),
			'id'   => 'tab-loop',
			'type' => 'tab_open',
		),


			/* Block */
			array(
				'name' => esc_html__( 'Select Block', 'epron-toolkit' ),
				'id'   => '_block',
				'type' => 'select_image',
				'std'  => 'events-block1',
				'images' => array(
					array(
						'id'    => 'events-block1',
						'title' => esc_html__( 'Block 1 - List', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block15.png'
					),
					array(
						'id'    => 'events-block2',
						'title' => esc_html__( 'Block 2', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block8.png'
					),
					array(
						'id'    => 'events-block3',
						'title' => esc_html__( 'Block 3', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block9.png'
					),
					array(
						'id'    => 'events-block4',
						'title' => esc_html__( 'Block 4', 'epron-toolkit' ),
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block10.png'
					),

				),
				'desc' => esc_html__( 'Select a block to be used in the loop of this page.', 'epron-toolkit' )
			),


			/* Pagination Method */
			array(
				'name' => esc_html__( 'Pagination Method', 'epron-toolkit' ),
				'id'   => '_pagination',
				'type' => 'select',
				'std'  => 'next_prev',
				'options' => array(
					array( 'name' => esc_html__( 'Next/Prev Pagination', 'epron-toolkit' ), 'value' => 'next_prev' ),
					array( 'name' => esc_html__( 'Load More Button', 'epron-toolkit' ), 'value' => 'load_more' ),
					array( 'name' => esc_html__( 'Infinite Load', 'epron-toolkit' ), 'value' => 'infinite' )
				),
				'desc' => esc_html__( 'Select pagination method.', 'epron-toolkit' )
			),

			/* Ajax Filter */
			array(
				'name'  => esc_html__( 'Ajax Filter', 'epron-toolkit' ),
				'id'    => '_ajax_filter',
				'type'  => 'select',
				'std'   => '',
				'options' => array(
					array( 'name' => esc_html__( 'None', 'epron-toolkit' ), 'value' => '' ),
					array( 'name' => esc_html__( 'On Left', 'epron-toolkit' ), 'value' => 'on-left' ),
					array( 'name' => esc_html__( 'Center', 'epron-toolkit' ), 'value' => 'center' ),
					array( 'name' => esc_html__( 'On Right', 'epron-toolkit' ), 'value' => 'on-right' ),
					array( 'name' => esc_html__( 'Multiple Filters', 'epron-toolkit' ), 'value' => 'multiple-filters' ),
				),
				'separator' => false,
				'desc' => esc_html__( 'Show or hide Ajax filter.', 'epron-toolkit' ),
				'dependency' => array(
		        	"element" => '_pagination',
		        	"value" => array( 'load_more', 'infinite' )
		    	)
			),

			/* Filter Selection method */
			array(
				'name'       => esc_html__( 'Selection Method', 'epron-toolkit' ),
				'id'         => '_filter_sel_method',
				'type'       => 'select',
				'std'        => 'filter-sel-multiple',
				'options'    => array(
					array( 'name' => 'Multiple', 'value' => 'filter-sel-multiple' ),
					array( 'name' => 'Single', 'value' => 'filter-sel-single' )
				),
				'separator'  => false,
				'desc'       => esc_html__( 'Select filter selection method.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_ajax_filter',
					"value"   => array( 'on-left', 'center', 'on-right', 'multiple-filters' )
			    )
			),

			/* Visible filter on startup */
			array(
				'name'       => esc_html__( 'Show filters on Start', 'epron-toolkit' ),
				'id'         => '_show_filters',
				'type'       => 'switch_button',
				'std'        => 'hide-filters',
				'options'    => array(
					array( 'name' => 'On', 'value' => 'show-filters' ), // ON
					array( 'name' => 'Off', 'value' => 'hide-filters' ) // OFF
				),
				'separator'  => true,
				'desc'       => esc_html__( 'Show filters when page is loaded. Otherwise the filters are shown after clicking the "Filters" button.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_ajax_filter',
					"value"   => array( 'multiple-filters' )
			    )
			),

		array(
			'type' => 'tab_close'
		),


		/* TAB: LOOP FILTERS
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Filter', 'epron-toolkit' ),
			'id'   => 'tab-filter',
			'type' => 'tab_open',
		),

			/* Limit */
			array(
				'name'  => esc_html__( 'Limit post number', 'epron-toolkit' ),
				'group' => '_filter_atts_events',
				'id'    => 'limit',
				'type'  => 'range',
				'max'   => '999',
				'min'   => '1',
				'unit'  => 'posts',
				'step'  => '1',
				'std'   => '8',
				'desc'  => esc_html__('Number of posts per page.', 'epron-toolkit' ),
			),

			/* Post IDS */
			array(
				'name'      => esc_html__( 'Post ID', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'post_ids',
				'type'      => 'text',
				'std'       => '',
				'separator' => true,
				'desc'      => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' ),
				'dependency' => array(
					"element" => 'event_type',
					"value"   => array( 'future-events','past-events' )
		    	)
			),

			/* Offset */
			array(
				'name'      => esc_html__( 'Offset Posts', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'offset',
				'type'      => 'range',
				'max'       => '999',
				'min'       => '0',
				'unit'      => 'posts nr',
				'step'      => '1',
				'std'       => '0',
				'separator' => true,
				'desc'      => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' ),
				'dependency' => array(
					"element" => 'event_type',
					"value"   => array( 'future-events','past-events' )
		    	)
			),


			/* Filters
			  -------------------------------- */

			/* Order */
			array(
				'name'      => esc_html__( 'Filters Order', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'filters_order',
				'type'      => 'text',
				'std'       => '2,1',
				'separator' => true,
				'desc'      => esc_html__( 'Enter the filters order number separated by commas (ex: 1,2,3).', 'epron-toolkit' ),
			),

			/* Filter 1 */
			array(
				'name'      => esc_html__( 'Filter 1', 'epron-toolkit' ),
				'sub_name'  => esc_html__( 'Name', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'event_type_label',
				'type'      => 'text',
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter name. If this field is empty, the filter will not be shown.', 'epron-toolkit' ),
			),
			array(
				'subname'  => esc_html__( 'Event Type', 'epron-toolkit' ),
				'group' => '_filter_atts_events',
				'id'    => 'event_type',
				'type'  => 'select',
				'std'   => 'future',
				'options' => array(
					array( 'name' => esc_html__( 'Future', 'epron-toolkit' ), 'value' => 'future-events' ),
					array( 'name' => esc_html__( 'Past', 'epron-toolkit' ), 'value' => 'past-events' ),
					array( 'name' => esc_html__( 'Future + Past', 'epron-toolkit' ), 'value' => 'all' ),
				),
				'separator' => true,
				'desc' => esc_html__( 'Select event type.', 'epron-toolkit' ),
			),


			/* Filter 2 */
			array(
				'name'      => esc_html__( 'Filter 2', 'epron-toolkit' ),
				'sub_name'  => esc_html__( 'Name', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'category_label',
				'type'      => 'text',
				'std'       => esc_html__( 'All', 'epron-toolkit' ),
				'separator' => false,
				'desc'      => esc_html__( 'Filter name.', 'epron-toolkit' ),
			),
			array(
				'sub_name'  => esc_html__( 'Categories', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'category_ids',
				'type'      => 'taxonomy',
				'taxonomy'  => 'wp_event_categories',
				'multiple'  => true,
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter multiple categories. Hold the CTRL key (PC) or COMMAND key (Mac) and click the items in a list to choose them. Click all the items you want to select. They don’t have to be next to each other.
				Click any item again to deselect it, e.g. if you have made a mistake. Remember to keep the CTRL or COMMAND key pressed.', 'epron-toolkit' ),
			),
			array(
				'sub_name'  => esc_html__( 'Slugs', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'category_slugs',
				'type'      => 'text',
				'std'       => '',
				'separator' => true,
				'desc' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: dubstep,hip-hop,glitch). Please note: Categories above have a higher priority than slugs names, so if you selected categories names, slugs will not be processed.', 'epron-toolkit' ),
			),

			/* Filter 3 */
			array(
				'name'      => esc_html__( 'Filter 3', 'epron-toolkit' ),
				'sub_name'  => esc_html__( 'Name', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'category_label2',
				'type'      => 'text',
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter name.', 'epron-toolkit' ),
			),
			array(
				'sub_name'  => esc_html__( 'Categories', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'category_ids2',
				'type'      => 'taxonomy',
				'taxonomy'  => 'wp_event_categories2',
				'multiple'  => true,
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter multiple categories. Hold the CTRL key (PC) or COMMAND key (Mac) and click the items in a list to choose them. Click all the items you want to select. They don’t have to be next to each other.
				Click any item again to deselect it, e.g. if you have made a mistake. Remember to keep the CTRL or COMMAND key pressed.', 'epron-toolkit' ),
			),
			array(
				'sub_name'  => esc_html__( 'Slugs', 'epron-toolkit' ),
				'group'     => '_filter_atts_events',
				'id'        => 'category_slugs2',
				'type'      => 'text',
				'std'       => '',
				'separator' => true,
				'desc' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: dubstep,hip-hop,glitch). Please note: Categories above have a higher priority than slugs names, so if you selected categories names, slugs will not be processed.', 'epron-toolkit' ),
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
	if ( has_filter( 'rascals_mb_events_template_opts' ) ) {
		$meta_options = apply_filters( 'rascals_mb_events_template_opts', $meta_options );
	}

	/* Add class instance */
	$rascals_mb_events_template = new RascalsBox( $meta_options, $meta_info );

}

return epron_toolkit_mb_events();