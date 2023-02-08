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


/* ==================================================
  Metaboxes options
================================================== */
function epron_toolkit_mb_post() {

	$rascals_mb = EpronToolkit::getInstance()->metaboxes;


	/* ==================================================
	  Single Post 
	================================================== */

	/* Meta info */ 
	$meta_info = array(
		'title' => esc_html__( 'Post Options', 'epron-toolkit'), 
		'id'    =>'rascals_mb_post', 
		'page'  => array(
			'post'
		), 
		'context'  => 'normal', 
		'priority' => 'high', 
		'callback' => '', 
		'template' => array( 
			'default'
		),
	);

	/* Box Filter */
	if ( has_filter( 'rascals_mb_post_box' ) ) {
		$meta_info = apply_filters( 'rascals_mb_post_box', $meta_info );
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


		/* TAB: Loop CONTENT
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Loop Content', 'epron-toolkit' ),
			'id'   => 'tab-loop',
			'type' => 'tab_open',
		),

			/* Short Description */ 
			array(
				'name'       => esc_html__( 'Short Description', 'epron-toolkit' ),
				'id'         => '_short_desc',
				'type'       => 'textarea',
				'tinymce'    => 'true',
				'height'     => '100',
				'std'        => '',
				'separator'  => true,
				'desc'       => esc_html__( 'Optional summary or description of a post. This text is displayed on the page with posts list.', 'epron-toolkit' ),
			), 
		array(
			'type' => 'tab_close'
		),


		/* TAB: Featured
		 -------------------------------- */
		array(
			'name' => esc_html__( 'Featured', 'epron-toolkit' ),
			'id'   => 'tab-featured',
			'type' => 'tab_open',
		),

			
			/* Featured Content */
			array(
				'name'   => esc_html__( 'Featured Content', 'epron-toolkit' ),
				'id'     => '_featured_content',
				'type'   => 'select_image',
				'std'    => 'image',
				'size'   => 'medium', // original, medium, small
				'images' => array(
					array( 
						'id'    => 'none', 
						'title' => esc_html__( 'Disabled', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/none.png'
					),
					array( 
						'id'    => 'image', 
						'title' => esc_html__( 'Image', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/image.png'
					),
					array( 
						'id'    => 'youtube', 
						'title' => esc_html__( 'YouTube Video', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/youtube.png'
					),
					array( 
						'id'    => 'vimeo', 
						'title' => esc_html__( 'Vimeo Video', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/vimeo.png'
					),
					array( 
						'id'    => 'soundcloud', 
						'title' => esc_html__( 'Soundcloud', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/soundcloud.png'
					),
					array( 
						'id'    => 'spotify', 
						'title' => esc_html__( 'Spotify', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/spotify.png'
					),
					array( 
						'id'    => 'bandcamp', 
						'title' => esc_html__( 'Bandcamp', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/bandcamp.png'
					),
					array( 
						'id'    => 'tracks', 
						'title' => esc_html__( 'Music Tracks', 'epron-toolkit' ), 
						'image' => esc_url( RASCALS_TOOLKIT_URL ) . '/assets/images/icons/scamp-player.png'
					),
					
				),
				'desc' => esc_html__( 'Choose featured content.', 'epron-toolkit' )
			),

			/* Media Link */
			array(
				'name'       => esc_html__( 'Media Link', 'epron-toolkit' ),
				'id'         => '_media_link',
				'type'       => 'text',
				'std'        => '',
				'desc'       => esc_html__( 'Paste media link.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_featured_content',
					"value"   => array( 'youtube', 'vimeo', 'soundcloud', 'spotify' )
		    	)
			),

			/* Bandcamp Link */
			array(
				'name'   => esc_html__( 'Bandcamp Embed Code', 'epron-toolkit' ),
				'id'     => '_bandcamp_code',
				'type'   => 'textarea',
				'height' => '100',
				'std'    => '',
				'desc'   => esc_html__( 'Paste iframe embed code.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_featured_content',
					"value"   => array( 'bandcamp' )
		    	)
			),

			/* Source name */
			array(
				'name'       => esc_html__( 'Source Name', 'epron-toolkit' ),
				'id'         => '_source_name',
				'type'       => 'textarea',
				'height'     => '50',
				'std'        => '',
				'desc'       => esc_html__( 'Source name, this will appear at the bottom of the featured content.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_featured_content',
					"value"   => array( 'image', 'youtube', 'vimeo', 'soundcloud', 'spotify', 'bandcamp', 'tracks' )
		    	)
			),

			/* Tracks */
			array(
				'name' => esc_html__( 'Select Tracks', 'epron-toolkit' ),
				'id'   => array(
					array( 'id' => '_track_id', 'std' => ''),
					array( 'id' => '_tracks_ids', 'std' => '') 
				),
				'type'       => 'select_tracks',
				'options'    => $rascals_mb->getTracks( 'wp_tracks' ),
				'std'        => '',
				'separator'  => false,
				'desc'       => esc_html__( 'Select tracklist post and drag and drop tracks to set the order. If there are no tracks available, then you can add a audio tracks using Tacks Manager menu on the left. Tip: Modifying this list does not affect on the original tracklist in Tracks Manager', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_featured_content',
					"value"   => array( 'tracks' )
		    	)
			),

			/* Size */
			array(
				'name'    => esc_html__( 'Style', 'epron-toolkit' ),
				'id'      => '_player_style',
				'type'    => 'select',
				'std'     => 'medium',
				'options' => array(
					array( 'name' => esc_html__( 'Tracklist', 'epron-toolkit' ), 'value' => 'tracklist' ),
					array( 'name' => esc_html__( 'Album Player', 'epron-toolkit' ), 'value' => 'album_player' ),
				),
				'separator'  => false,
				'desc'       => esc_html__( 'Select player style.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_featured_content',
					"value"   => array( 'tracks' )
		    	)
			),

			/* Fixed height */
			array(
				'name'       => esc_html__( 'Fixed Height', 'epron-toolkit' ),
				'id'         => '_fixed_height',
				'type'       => 'range',
				'min'        => 0,
				'max'        => 999,
				'std'        => '0',
				'separator'  => false,
				'desc'       => esc_html__( 'Set fixed height (px) of tracklist. If the value is set at "0" then the height of the list is set to automatic and the scroll on right is invisible.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_featured_content',
					"value"   => array( 'tracks' )
		    	)
			),

			/* Covers Images */
			array(
				'name'    => esc_html__( 'Show Cover Images', 'epron-toolkit' ),
				'id'      => '_show_covers',
				'type'    => 'switch_button',
				'std'     => 'yes',
				'options' => array(
					array( 'name' => 'On', 'value' => 'yes' ), // ON
					array( 'name' => 'Off', 'value' => 'no' ) // OFF
				),
				'separator'  => false,
				'desc'       => esc_html__( 'Show or hide tracks cover images in tracklist', 'epron-toolkit' ),
				'dependency' => array(
		        	"element" => '_player_style',
		        	"value" => array( 'tracklist' )
		    	)
			),

			/* Covers Images */
			array(
				'name'    => esc_html__( 'Big Cover Images', 'epron-toolkit' ),
				'id'      => '_big_cover',
				'type'    => 'switch_button',
				'std'     => 'no',
				'options' => array(
					array( 'name' => 'On', 'value' => 'yes' ), // ON
					array( 'name' => 'Off', 'value' => 'no' ) // OFF
				),
				'separator'  => false,
				'desc'       => esc_html__( 'Show obig cover images in tracklist', 'epron-toolkit' ),
				'dependency' => array(
		        	"element" => '_show_covers',
		        	"value" => array( 'yes' )
		    	)
			),

			/* Limit */
			array(
				'name'      => esc_html__( 'Display Limit', 'epron-toolkit' ),
				'id'        => '_limit',
				'type'      => 'range',
				'min'       => 0,
				'max'       => 999,
				'std'       => 0,
				'separator' => false,
				'desc' => esc_html__( 'How many tracks will be visibile. If the value is set at "0" then all tracks will be shown.', 'epron-toolkit' ),
				'dependency' => array(
					"element" => '_player_style',
					"value"   => array( 'tracklist' )
		    	)
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
	if ( has_filter( 'rascals_mb_post_opts' ) ) {
		$meta_options = apply_filters( 'rascals_mb_post_opts', $meta_options );
	}

	/* Add class instance */
	$rascals_mb_post = new RascalsBox( $meta_options, $meta_info );
		
}

return epron_toolkit_mb_post();