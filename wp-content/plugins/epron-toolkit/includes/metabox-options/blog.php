<?php
/**
 * Rascals MetaBox
 *
 * Register Blog Metabox
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
function epron_toolkit_mb_blog() {

	$rascals_mb = EpronToolkit::getInstance()->metaboxes;


	/* ==================================================
	  Latest Articles 
	================================================== */

	/* Meta info */ 
	$meta_info = array(
		'title' => esc_html__( 'Articles Options', 'epron-toolkit'), 
		'id'    =>'rt_blog', 
		'page'  => array(
			'page'
		), 
		'context'  => 'normal', 
		'priority' => 'high', 
		'callback' => '', 
		'template' => array( 
			'template-blog.php'
		),
	);

	/* Box Filter */
	if ( has_filter( 'rascals_mb_blog_box' ) ) {
		$meta_info = apply_filters( 'rascals_mb_blog_box', $meta_info );
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
				'name'   => esc_html__( 'Select Block', 'epron-toolkit' ),
				'id'     => '_block',
				'type'   => 'select_image',
				'std'    => 'block',
				'images' => array(
					array( 
						'id'    => 'block', 
						'title' => esc_html__( 'Block 1', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block16.png'
					),
					array( 
						'id'    => 'block2', 
						'title' => esc_html__( 'Block 2', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block4.png'
					),
					array( 
						'id'    => 'block3', 
						'title' => esc_html__( 'Block 3', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block5.png'
					),
					array( 
						'id'    => 'block4', 
						'title' => esc_html__( 'Block 4', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block6.png'
					),
					array( 
						'id'    => 'block5', 
						'title' => esc_html__( 'Block 5', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/blocks/block17.png'
					),


				),
				'desc' => esc_html__( 'Select a block to be used in the loop of this page.', 'epron-toolkit' )
			),

			/* Pagination Method */
			array(
				'name'    => esc_html__( 'Pagination Method', 'epron-toolkit' ),
				'id'      => '_pagination',
				'type'    => 'select',
				'std'     => 'next_prev',
				'options' => array(
					array( 'name' => esc_html__( 'Next/Prev Pagination', 'epron-toolkit' ), 'value' => 'next_prev' ),
					array( 'name' => esc_html__( 'Load More Button', 'epron-toolkit' ), 'value' => 'load_more' ),
					array( 'name' => esc_html__( 'Infinite Load', 'epron-toolkit' ), 'value' => 'infinite' )
				),
				'desc' => esc_html__( 'Select pagination method.', 'epron-toolkit' )
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
				'group' => '_filter_atts',
				'id'    => 'limit',
				'type'  => 'range',
				'max'   => '999',
				'min'   => '1',
				'unit'  => 'posts',
				'step'  => '1',
				'std'   => '8',
				'desc'  => esc_html__('If the field is empty the limit post number will be the number from Wordpress settings -> Reading', 'epron-toolkit' ),
			), 

			/* Sort Order */
			array(
				'name'    => esc_html__( 'Sort Order', 'epron-toolkit' ),
				'group'   => '_filter_atts',
				'id'      => 'sort_order',
				'type'    => 'select',
				'std'     => 'post_date',
				'options' => array(
					array( 'name' => esc_html__( 'Latest (By date)', 'epron-toolkit' ), 'value' => 'post_date' ),
					array( 'name' => esc_html__( 'Alphabetical A -> Z', 'epron-toolkit' ), 'value' => 'title' ),
					array( 'name' => esc_html__( 'Random Posts', 'epron-toolkit' ), 'value' => 'rand' ),
					array( 'name' => esc_html__( 'Random Posts Today', 'epron-toolkit' ), 'value' => 'rand_today' ),
					array( 'name' => esc_html__( 'Random Posts From Last 7 Days', 'epron-toolkit' ), 'value' => 'rand_week' ),
					array( 'name' => esc_html__( 'Most Commented', 'epron-toolkit' ), 'value' => 'comment_count' ),
					array( 'name' => esc_html__( 'Highest rated (reviews)', 'epron-toolkit' ), 'value' => 'highest_rated' ),
				),
				'separator' => false,
				'desc'      => esc_html__( 'How to sort the posts.', 'epron-toolkit' ),
			),
			array(
				'name'      => esc_html__( 'Post ID', 'epron-toolkit' ),
				'group'     => '_filter_atts',
				'id'        => 'post_ids',
				'type'      => 'text',
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' ),
			),
			array(
				'name'      => esc_html__( 'Multiple Category ID', 'epron-toolkit' ),
				'group'     => '_filter_atts',
				'id'        => 'category_ids',
				'type'      => 'taxonomy',
				'taxonomy'  => 'category',
				'multiple'  => true,
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter multiple categories. Hold the CTRL key (PC) or COMMAND key (Mac) and click the items in a list to choose them. Click all the items you want to select. They don’t have to be next to each other.
Click any item again to deselect it, e.g. if you have made a mistake. Remember to keep the CTRL or COMMAND key pressed.', 'epron-toolkit' ),
			),
			array(
				'name'      => esc_html__( 'Category Slug', 'epron-toolkit' ),
				'group'     => '_filter_atts',
				'id'        => 'category_slugs',
				'type'      => 'text',
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' ),
			),
			array(
				'name'      => esc_html__( 'Tag Slug', 'epron-toolkit' ),
				'group'     => '_filter_atts',
				'id'        => 'tag_slugs',
				'type'      => 'text',
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter tags by slugs. Enter the tag slugs separated by commas (ex: tag1,tag2,tag3)', 'epron-toolkit' ),
			),
			array(
				'name'      => esc_html__( 'Author ID', 'epron-toolkit' ),
				'group'     => '_filter_atts',
				'id'        => 'author_ids',
				'type'      => 'text',
				'std'       => '',
				'separator' => false,
				'desc'      => esc_html__( 'Filter multiple authors by ID. Enter the author IDs separated by commas (ex: 32,11,899)', 'epron-toolkit' ),
			),
			array(
				'name'      => esc_html__( 'Offset Posts', 'epron-toolkit' ),
				'group'     => '_filter_atts',
				'id'        => 'offset',
				'type'      => 'range',
				'max'       => '999',
				'min'       => '0',
				'unit'      => 'posts nr',
				'step'      => '1',
				'std'       => '0',
				'separator' => false,
				'desc'      => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' ),
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
	if ( has_filter( 'rascals_mb_blog_opts' ) ) {
		$meta_options = apply_filters( 'rascals_mb_blog_opts', $meta_options );
	}

	/* Add class instance */
	$rascals_mb_blog = new RascalsBox( $meta_options, $meta_info );
		
}

return epron_toolkit_mb_blog();