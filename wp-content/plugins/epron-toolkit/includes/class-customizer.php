<?php
/**
 * Rascals Customizer
 *
 * Register slider post type
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}


class RascalsCustomizer {

	/**
	 * Social Media Array
	 * @var array
	 */
	public $social_media_a = array(
		'twitter'        => 'Twitter',
		'facebook'       => 'Facebook',
		'youtube'        => 'Youtube',
		'instagram'      => 'Instagram',
		'soundcloud'     => 'Soundcloud',
		'mixcloud'       => 'Mixcloud',
		'bandcamp'       => 'Bandcamp',
		'Beatport'       => 'Beatport',
		'spotify'        => 'Spotify',
		'itunes-filled'  => 'iTunes',
		'lastfm'         => 'lastfm',
		'vimeo'          => 'Vimeo',
		'vk'             => 'VK',
		'flickr'         => 'Flickr',
		'snapchat-ghost' => 'Snapchat',
		'dribbble'       => 'Dribbble',
		'deviantart'     => 'Deviantart',
		'github'         => 'Github',
		'blogger'        => 'Blogger',
		'yahoo'          => 'Yahoo',
		'finder'         => 'Finder',
		'skype'          => 'Skype',
		'reddit'         => 'Reddit',
		'linkedin'       => 'Linkedin',
		'amazon'         => 'Amazon',
		'telegram'       => 'Telegram',
		'qq'             => 'QQ',
		'weibo'          => 'Weibo',
		'wechat'         => 'Wechat',
	);


	/**
	 * Rascals Customizer Constructor.
	 * @return void
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize class
	 * @return void
	 */
	public function init() {
		
		if ( ! class_exists( 'Kirki' ) ) {
			return;
		}

		// Config Kirki
		Kirki::add_config( 'rascals_customizer', array(
		    'capability'    => 'edit_theme_options',
		    'option_type'   => 'theme_mod',
		) );

		// Load customizer style
		add_action( 'customize_controls_print_styles', array( $this, 'registerStyles' ) );

		// Kirki styling
		add_action( 'kirki/config', array( $this, 'configurationSampleStyling' ) );

		// Remove a pre exising customizer setting.
		add_action( 'customize_register', array( $this, 'removeCustomizeRegister' ) );

		// Add Kirki Options
		$this->addOptions();
	}


	/**
	 * Kriki Options
	 * @return void
	 */
	public function addOptions() {

		// Set default logo based on header color scheme 
		$header_color_scheme = get_theme_mod( 'header_color_scheme', 'dark' );
		$images_path = get_template_directory_uri() . '/images';

		if ( $header_color_scheme === 'dark' ) {
			$default_logo_img =  $images_path . '/logo-light.svg';
			$default_logo_hero = $images_path . '/logo-light.svg';
		} else {
			$default_logo_img =  $images_path . '/logo-dark.svg';
			$default_logo_hero = $images_path . '/logo-light.svg';
		}

		$default_page_bg = $images_path . '/default-bg.png';


		/* ==================================================
		  Section: General
		================================================== */
		Kirki::add_section( 'general', array(
		    'title'          => esc_html__( 'General', 'epron-toolkit' ),
		    'description'    => false,
		    'priority'       => 1,
		    'capability'     => 'edit_theme_options',
		    'theme_supports' => '',
		) );


			/** 
			* ------------- Control: Site Size
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Site Size', 'epron-toolkit' ),
				'section'  => 'general',
				'settings' => 'site_size',
				'type'     => 'select',
				'priority' => 1,
				'default'  => 'dark',
				'choices'  => array(
					'small' => esc_html__( 'Small (980px)', 'epron-toolkit' ),
					'wide'  => esc_html__( 'Wide (1170px)', 'epron-toolkit' ),
				)
			) );


			/** 
			* ------------- Control: Show Loader
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Loader', 'epron-toolkit' ),
				'section'  => 'general',
				'settings' => 'loader',
				'type'     => 'toggle',
				'priority' => 2,
				'default'  => '0',
				'description' => esc_html__( 'Please note loader is not visible in customizer mode.', 'epron-toolkit' ),
			) );


			/** 
			* ------------- Control: Show Top Button
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Top Button', 'epron-toolkit' ),
				'section'  => 'general',
				'settings' => 'top_button',
				'type'     => 'toggle',
				'priority' => 3,
				'default'  => '0',
				'description' => esc_html__( 'Show scroll to top button.', 'epron-toolkit' ),
			) );


		/* ==================================================
		  Section: Colors 
		================================================== */
		Kirki::add_section( 'colors', array(
		    'title'          => esc_html__( 'Colors', 'epron-toolkit' ),
		    'description'    => false,
		    'priority'       => 2,
		    'capability'     => 'edit_theme_options',
		    'theme_supports' => '',
		) );


			/** 
			* ------------- Control: Color Scheme
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Main Color Scheme', 'epron-toolkit' ),
				'section'  => 'colors',
				'settings' => 'color_scheme',
				'type'     => 'select',
				'priority' => 1,
				'default'  => 'dark',
				'choices'  => array(
					'dark'  => esc_html__( 'Dark', 'epron-toolkit' ),
					'light' => esc_html__( 'Light', 'epron-toolkit' ),
				),
			) );


			/** 
			* ------------- Control: Accent Color
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Accent Color', 'epron-toolkit' ),
				'section'  => 'colors',
				'settings' => 'accent_color',
				'type'     => 'color',
				'priority' => 8,
				'default'  => '#fa4c29', 
				'choices'  => array(
					'alpha' => false,
				),
				'output' => array(

					///////////
					// COLOR //
					///////////
					array(
						'property' => 'color',
						'element'  => array( 
							'a',
							'.color',
							'article.sticky .post-title:before',
							'.error404 .big-text',	
							'#nav-sidebar ul li a:hover',
							'.hero-title',
							'#comments .comments-title a:hover',
							'.logged-in-as a:hover',
							'.comment .author a:hover',
							'.comment .reply a',
							'#reply-title small:hover',
							'#footer-nav li a:hover',
							'.widget a:hover',
							'.recent-comments li .meta:hover',
							'.recent-entries li a:hover',
							'.widget table#wp-calendar #next a:hover',
				            '.widget table#wp-calendar #prev a:hover',
				            '.rt-newsletter-input-wrap:after',
				            '.module-1 .post-title a:hover',
				            '.module-event-2 .event-date',
				            'body .mfp-arrow:hover',
				            '.mfp-close:hover:after',
				            '.rt-recent-posts.rt-show-thumbs .rp-caption',

							// addons 
							'ul.stats li .stat-value',
							'.spl-player.open .show-list-btn',
							'.spl-player .show-list-btn:hover',
							'.share-button:hover',
							'.kc-testi-layout-1 .content-title',
							'.rp-post-wrap h4 a:hover',
							'div.wpcf7-validation-errors',
							'div.wpcf7-acceptance-missing',
							// ---/ addons 

							// Woocommerce 
							'.woocommerce ul.products li.product .woocommerce-loop-product__title', 
						 	// ---/ woocommerce 
							
						
						)
					),

					//////////////////////
					// BACKGROUND-COLOR //
					//////////////////////
					array(
						'property' => 'background-color',
						'element'  => array(
							'.attachment-post-link a:hover',
							'.social-icons a:hover',
							'.light-scheme .attachment-post-link a:hover',
							'#nav-main li.new > a:after',
							'#comments .comments-title:before',
							'.footer-twitter .twitter-button',
							'.widget button, .widget .button',
							'.widget input[type="button"]',
							'.widget input[type="reset"]', 
							'.widget input[type="submit"]',
							'.recent-entries li .date',
							'.widget_categories a:before', 
				            '.widget_archive a:before', 
				            '.widget_recent_entries a:before', 
				            '.widget_meta a:before',
				            '.widget_nav_menu a:before',
				            '.widget_pages a:before',
				            '.widget_links a:before',
				            '.widget.widget_tag_cloud a:hover',
				            '.module-1 .post-title:before',
				            '.module-event-1 .plus-button',
				            '.module-artist-1 artist-name:before',
				            '.module .post-date:before',
				            '.badge.new',
				            '.badge.free',
				            'input[type="submit"]',
							'button',
							'.btn',
							'.arrow-nav',
							'.ajax-filters li a.is-active',
							'.top-header .top-right-nav li.cart a .contents',
							'.owl-nav-arrows.owl-theme .owl-controls .owl-buttons div',

							// addons 
							'.kc-event-countdown .unit-block',
							'.kc-image-countdown .plus-button',
							'.spl-player-content',
							'.spl-player .spl-single-track .spl-position',
							'.kc-posts-slider.owl-theme .owl-pagination .owl-page:hover',
							'.rp-post-wrap .rp-post-date span',
							//  ---/ addons 

						 	// Woocommerce 
							'.woocommerce #respond input#submit.alt', 
							'.woocommerce a.button.alt',
							'.woocommerce button.button.alt',
							'.woocommerce input.button.alt', 
							'#payment ul li input[type="radio"]:checked:after',
							'.woocommerce #respond input#submit', 
							'.woocommerce a.button', 
							'.woocommerce button.button', 
							'.woocommerce input.button',
							'.woocommerce div.product form.cart .button',
						 	// ---/ woocommerce 


						)	
					),

					///////////////
					// SELECTION //
					///////////////
					array(
						'property' => 'background',
						'element'  => array( 
							'::selection'
						)
					),
					
					//////////////////
					// BORDER-COLOR //
					//////////////////
					array(
						'property' => 'border-color',
						'element'  => array(
							'input:focus, textarea:focus, select:focus',
							'#nav-main > ul > li > a:not(.module-link):before',
							'.module-simple:hover',
							'.module-2:hover',
							'.module-release-1 .module-thumb-block:hover',
							'.module-gallery-1 .module-thumb-block:hover',
							'.cats-style .cat:before',
							'.ajax-grid-block .content-ajax-loader .loader::after',

							// addons 
							'.gallery-images-grid .g-item:hover',
							'div.wpcf7-validation-errors', 
							'div.wpcf7-acceptance-missing',
							 //  ---/ addons 

							// woocommerce 
							'.woocommerce div.product .woocommerce-tabs ul.tabs li.active a',
							// ---/ woocommerce 

						)
					),


					/////////////////////////
					// BORDER-BOTTOM-COLOR //
					/////////////////////////
					array(
						'property' => 'border-bottom-color',
						'element'  => array(
							'.ajax-grid-block .content-ajax-loader .loader::after',
						)
					),
					

				)
			) );

	
		/* ==================================================
		  Panel: Headers
		================================================== */
		Kirki::add_panel( 'headers', array(
			'title'       => esc_html__( 'Headers', 'epron-toolkit' ),
			'priority'    => 3,
			'description' => esc_html__( 'This panel will provide all the options of the header.', 'epron-toolkit' ),
		) );
			

			/* ==================================================
			  Section: Top Header 
			================================================== */
			Kirki::add_section( 'top_header', array(
			    'title'          => esc_html__( 'Top Header', 'epron-toolkit' ),
			    'description'    => false,
			    'panel'          => 'headers',
			    'priority'       => 1,
			    'capability'     => 'edit_theme_options',
			    'theme_supports' => '',
			) );


				/** 
				* ------------- Control: Show Top Header
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Show Top Header', 'epron-toolkit' ),
					'section'  => 'top_header',
					'settings' => 'show_top_header',
					'type'     => 'toggle',
					'priority' => 1,
					'default'  => '0'
				) );

				/** 
				* ------------- Control: Show Search
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Show Search', 'epron-toolkit' ),
					'section'  => 'top_header',
					'settings' => 'show_top_header_search',
					'type'     => 'toggle',
					'priority' => 1,
					'default'  => '1',
					'active_callback'  => [
						[
							'setting'  => 'show_top_header',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Show Cart Details
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'       => esc_html__( 'Show WooCommerce Cart', 'epron-toolkit' ),
					'section'     => 'top_header',
					'settings'    => 'show_top_header_cart',
					'type'        => 'toggle',
					'priority'    => 1,
					'default'     => '1',
					'description' => esc_html__( 'Display cart details in header. Please note WOOCOMMERCE plugin must be installed.', 'epron-toolkit' ),
					'active_callback'  => [
						[
							'setting'  => 'show_top_header',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


			/* ==================================================
			  Section: Main Header 
			================================================== */
			Kirki::add_section( 'header', array(
			    'title'          => esc_html__( 'Main Header', 'epron-toolkit' ),
			    'description'    => false,
			    'panel'          => 'headers',
			    'priority'       => 2,
			    'capability'     => 'edit_theme_options',
			    'theme_supports' => '',
			) );


				/** 
				* ------------- Control: Header Styles
				*/

				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Header Style', 'epron-toolkit' ),
					'section'  => 'header',
					'settings' => 'header_style',
					'type'     => 'select',
					'priority' => 1,
					'default'  => 'header-style1',
					'choices'  => array(
						'header-style1' => esc_html__( 'Left Black', 'epron-toolkit' ),
					),
				) );


				/** 
				* ------------- Control: Logo
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Logo', 'epron-toolkit' ),
					'section'  => 'header',
					'settings' => 'logo',
					'type'     => 'image',
					'priority' => 2,
					'default'  => $default_logo_img,
				) );


				/** 
				* ------------- Control: Logo Hero
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Logo (Transparent Header)', 'epron-toolkit' ),
					'section'  => 'header',
					'settings' => 'logo_hero',
					'type'     => 'image',
					'priority' => 3,
					'default'  => $default_logo_hero,
					'description' => esc_html__( 'This Logo image will be displayed on transparent background on hero header image.', 'epron-toolkit' )
				) );


				/** 
				* ------------- Control: Logo Mobile
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Logo (Mobile Menu)', 'epron-toolkit' ),
					'section'  => 'header',
					'settings' => 'logo_mobile',
					'type'     => 'image',
					'priority' => 4,
					'default'  => $default_logo_img,
					'description' => esc_html__( 'This Logo image will be displayed on mobile (responsive) menu.', 'epron-toolkit' )
				) );


				/** 
				* ------------- Control: Sticky Header
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Sticky Header', 'epron-toolkit' ),
					'section'  => 'header',
					'settings' => 'sticky_header',
					'type'     => 'toggle',
					'priority' => 5,
					'default'  => '0',
				) );


		/* ==================================================
		  Panel: Sidebars 
		================================================== */
		Kirki::add_panel( 'sidebars', array(
		    'priority'    => 4,
		    'title'       => esc_html__( 'Sidebars', 'epron-toolkit' ),
		    'description' => esc_html__( 'This panel will provide all the options of the sidebar and slidebar.', 'epron-toolkit' ),
		) );
			

			/**
			 * ------------- Section: Sidebar
			 *
			 */

			Kirki::add_section( 'sidebar', array(
			    'title'          => esc_html__( 'Sidebar', 'epron-toolkit' ),
			    'description'    => false,
			    'panel'          => 'sidebars',
			    'priority'       => 1,
			    'capability'     => 'edit_theme_options',
			    'theme_supports' => '',
			) );


					/** 
					* ------------- Control: Sticky Sidebar
					*/
				
					Kirki::add_field( 'rascals_customizer', array(
						'label'       => esc_html__( 'Sticky Sidebar', 'epron-toolkit' ),
						'section'     => 'sidebar',
						'settings'    => 'sticky_sidebar',
						'type'        => 'toggle',
						'priority'    => 1,
						'default'     => '0',
						'description' => esc_html__( 'From here you can enable and disable the sticky sidebar on all the templates. The sticky sidebar that has auto resize and it scrolls with the content. The sticky_sidebar reverts back to a normal sidebar on iOS (iPad) and on mobile devices. If you plan to use Google AdSense in the sidebar don\'t enable this feature. Google\'s policy doesn\'t allow placing the ad in a "floating box", you can read more about it ', 'epron-toolkit' ). '<a href="https://support.google.com/adsense/answer/1354742?hl=en">'.esc_html__( 'here', 'epron-toolkit' ).'</a>',
					) );



		/* ==================================================
		  Section: Pages
		================================================== */
		Kirki::add_section( 'pages', array(
		    'title'          => esc_html__( 'Pages/Posts', 'epron-toolkit' ),
		    'description'    => false,
		    'priority'       => 5,
		    'capability'     => 'edit_theme_options',
		    'theme_supports' => '',
		) );

			/** 
			* ------------- Control: Default hero background
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Default hero background', 'epron-toolkit' ),
				'section'  => 'pages',
				'settings' => 'default_page_bg',
				'type'     => 'image',
				'priority' => 1,
				'default'  => $default_page_bg
			) );

			/** 
			* ------------- Control: Show line
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Line?', 'epron-toolkit' ),
				'section'  => 'pages',
				'settings' => 'show_hero_line',
				'type'     => 'toggle',
				'priority' => 2,
				'default'  => '1',
				'description' => esc_html__( 'Show line in hero area (below title)', 'epron-toolkit' )
			) );

			/** 
			* ------------- Control: Show Share
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Share Buttons?', 'epron-toolkit' ),
				'section'  => 'pages',
				'settings' => 'show_share_buttons',
				'type'     => 'toggle',
				'priority' => 3,
				'default'  => '0',
				'description' => esc_html__( 'Show share buttons in hero area (below title)', 'epron-toolkit' )
			) );

			/** 
			* ------------- Control: Show Post Date
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Post Details', 'epron-toolkit' ),
				'section'  => 'pages',
				'settings' => 'show_post_header_details',
				'type'     => 'toggle',
				'priority' => 4,
				'default'  => '0',
				'description' => esc_html__( 'Show date and categories in hero area (above title)', 'epron-toolkit' )
			) );

			/** 
			* ------------- Control: Show Author BOX
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Author Box', 'epron-toolkit' ),
				'section'  => 'pages',
				'settings' => 'show_author_box',
				'type'     => 'toggle',
				'priority' => 5,
				'default'  => '0',
				'description' => esc_html__( 'Show author box below post content.', 'epron-toolkit' )
			) );

			/** 
			* ------------- Control: Show Related Posts
			*/
			Kirki::add_field( 'rascals_customizer', array(
				'label'    => esc_html__( 'Show Related Posts', 'epron-toolkit' ),
				'section'  => 'pages',
				'settings' => 'show_related_posts',
				'type'     => 'toggle',
				'priority' => 6,
				'default'  => '0',
				'description' => esc_html__( 'Show related posts below post content.', 'epron-toolkit' )
			) );

		/* ==================================================
		  Panel: Footer 
		================================================== */
		Kirki::add_panel( 'footer', array(
		    'priority'    => 10,
		    'title'       => esc_html__( 'Footer', 'epron-toolkit' ),
		    'description' => esc_html__( 'This panel will provide all the options of the footer.', 'epron-toolkit' ),
		) );


			/**
			 * ------------- Section: Footer Top
			 */
			Kirki::add_section( 'footer_top', array(
			    'title'          => esc_html__( 'Footer Top', 'epron-toolkit' ),
			    'description'    => false,
			    'panel'          => 'footer',
			    'priority'       => 1,
			    'capability'     => 'edit_theme_options',
			    'theme_supports' => '',
			) );


				/** 
				* ------------- Control: Show Footer Top?
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'       => esc_html__( 'Show Footer Top?', 'epron-toolkit' ),
					'section'     => 'footer_top',
					'settings'    => 'show_footer_top',
					'type'        => 'toggle',
					'default'     => '0',
					'priority'    => 1,
					'description' => esc_html__( 'Show top footer section.', 'epron-toolkit' )
				) );


				/** 
				* ------------- Control: Address
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Address', 'epron-toolkit' ),
					'section'  => 'footer_top',
					'settings' => 'footer_address',
					'type'     => 'text',
					'priority' => 1,
					'default' => '',
					'active_callback'  => [
						[
							'setting'  => 'show_footer_top',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );

				/** 
				* ------------- Control: Phone
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Phone', 'epron-toolkit' ),
					'section'  => 'footer_top',
					'settings' => 'footer_phone',
					'type'     => 'text',
					'priority' => 1,
					'default' => '',
					'active_callback'  => [
						[
							'setting'  => 'show_footer_top',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Email
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Email', 'epron-toolkit' ),
					'section'  => 'footer_top',
					'settings' => 'footer_email',
					'type'     => 'text',
					'priority' => 1,
					'default' => '',
					'active_callback'  => [
						[
							'setting'  => 'show_footer_top',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Social Buttons
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Social Buttons', 'epron-toolkit' ),
					'section'  => 'footer_top',
					'settings' => 'footer_social_buttons',
					'type'     => 'repeater',
					'priority' => 3,
					'row_label' => array(
						'type'  => 'field',
						'value' => esc_html__('Social Button:', 'epron-toolkit' ),
						'field' => 'social_type',
					),
					'default'     => array(
						array(
							'social_type' => 'facebook',
							'social_link'  => '#',
						),
						array(
							'social_type' => 'twitter',
							'social_link'  => '#',
						),
						array(
							'social_type' => 'soundcloud',
							'social_link'  => '#',
						),
						array(
							'social_type' => 'mixcloud',
							'social_link'  => '#',
						),
						array(
							'social_type' => 'spotify',
							'social_link'  => '#',
						)
					),
					'fields' => array(

						'social_type' => array(
							'type'        => 'select',
							'label'       => esc_html__( 'Social Media', 'epron-toolkit' ),
							'description' => esc_html__( 'Select your social media button', 'epron-toolkit' ),
							'default'     => '',
							'choices'  => $this->social_media_a,
						),
						'social_link' => array(
							'type'        => 'text',
							'label'       => esc_html__( 'Link', 'epron-toolkit' ),
							'description' => esc_html__( 'Type your social link', 'epron-toolkit' ),
							'default'     => '',
						),
					),
					'active_callback'  => [
						[
							'setting'  => 'show_footer_top',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Left Column Classes
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Left Column Classes (Advanced)', 'epron-toolkit' ),
					'section'  => 'footer_top',
					'settings' => 'footer_left_col_classes',
					'type'     => 'text',
					'priority' => 2,
					'default'  => 'grid-6 grid-tablet-6 grid-mobile-12',
					'description' => esc_html__( 'Change or add CSS classes to the left column. Theme is designed for 12 columns grid.', 'epron-toolkit' ),
					'active_callback'  => [
						[
							'setting'  => 'show_footer_top',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Left Column Classes
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Right Column Classes (Advanced)', 'epron-toolkit' ),
					'section'  => 'footer_top',
					'settings' => 'footer_right_col_classes',
					'type'     => 'text',
					'priority' => 2,
					'default'  => 'grid-6 grid-tablet-6 grid-mobile-12',
					'description' => esc_html__( 'Change or add CSS classes to the right column. Theme is designed for 12 columns grid.', 'epron-toolkit' ),
					'active_callback'  => [
						[
							'setting'  => 'show_footer_top',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


			/**
			 * ------------- Section: Footer Widgetss
			 */
			Kirki::add_section( 'footer_widgets', array(
			    'title'          => esc_html__( 'Footer Widgets', 'epron-toolkit' ),
			    'description'    => false,
			    'panel'          => 'footer',
			    'priority'       => 2,
			    'capability'     => 'edit_theme_options',
			    'theme_supports' => '',
			) );

			   /** 
				* ------------- Control: Show Widgets
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'       => esc_html__( 'Show Footer Widgets?', 'epron-toolkit' ),
					'section'     => 'footer_widgets',
					'settings'    => 'show_footer_widgets',
					'type'        => 'toggle',
					'default'     => '0',
					'priority'    => 1,
					'description' => esc_html__( 'Show footer widgets.', 'epron-toolkit' )
				) );


			/**
			 * ------------- Section: Footer Bottom
			 */
			Kirki::add_section( 'footer_bottom', array(
			    'title'          => esc_html__( 'Footer Bottom', 'epron-toolkit' ),
			    'description'    => false,
			    'panel'          => 'footer',
			    'priority'       => 3,
			    'capability'     => 'edit_theme_options',
			    'theme_supports' => '',
			) );


				/** 
				* ------------- Control: Note text
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Copyright Text', 'epron-toolkit' ),
					'section'  => 'footer_bottom',
					'settings' => 'copyright_note',
					'type'     => 'textarea',
					'priority' => 1,
					'default' => '&copy; Copyright 2019 Epron. Powered by <a href="#" target="_blank">Rascals Themes</a>. Handcrafted in Europe.',
				) );


				/** 
				* ------------- Control: Show Twitter?
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'       => esc_html__( 'Show Twitter', 'epron-toolkit' ),
					'section'     => 'footer_bottom',
					'settings'    => 'show_footer_twitter',
					'type'        => 'toggle',
					'default'     => '0',
					'priority'    => 1,
					'description' => esc_html__( 'Show recent tweets in footer section.', 'epron-toolkit' )
				) );

				/** 
				* ------------- Control: Username
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'Username', 'epron-toolkit' ),
					'section'  => 'footer_bottom',
					'settings' => 'footer_twitter_username',
					'type'     => 'text',
					'priority' => 2,
					'default'  => '',
					'active_callback'  => [
						[
							'setting'  => 'show_footer_twitter',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );

				/** 
				* ------------- Control: API Key
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'API Key', 'epron-toolkit' ),
					'section'  => 'footer_bottom',
					'settings' => 'footer_twitter_api_key',
					'type'     => 'text',
					'priority' => 3,
					'default'  => '',
					'active_callback'  => [
						[
							'setting'  => 'show_footer_twitter',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );

				/** 
				* ------------- Control: API Secret Key
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'    => esc_html__( 'API Secret Key', 'epron-toolkit' ),
					'section'  => 'footer_bottom',
					'settings' => 'footer_twitter_api_secret',
					'type'     => 'text',
					'priority' => 4,
					'default'  => '',
					'active_callback'  => [
						[
							'setting'  => 'show_footer_twitter',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Show Replies?
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'       => esc_html__( 'Show Replies', 'epron-toolkit' ),
					'section'     => 'footer_bottom',
					'settings'    => 'footer_twitter_replies',
					'type'        => 'toggle',
					'default'     => '0',
					'priority'    => 5,
					'description' => esc_html__( 'Choose whether you want to show replies in your twitter widget or not.', 'epron-toolkit' ),
					'active_callback'  => [
						[
							'setting'  => 'show_footer_twitter',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );


				/** 
				* ------------- Control: Number of Tweets
				*/
				Kirki::add_field( 'rascals_customizer', array(
					'label'       => esc_html__( 'Number of Tweets', 'epron-toolkit' ),
					'section'     => 'footer_bottom',
					'settings'    => 'footer_twitter_limit',
					'type'        => 'number',
					'default'     => '1',
					'priority'    => 6,
					'description' => esc_html__( 'Number of tweets to display.', 'epron-toolkit' ),
					'active_callback'  => [
						[
							'setting'  => 'show_footer_twitter',
							'operator' => '==',
							'value'    => '1',
						],
					]
				) );

			
	}



	/**
	 * Get all registered KC sections
	 * @return array
	 */
	public function getKCSections( ) {
		$kc_sections = array(
			'none' => esc_html__( 'Select Section', 'epron-toolkit' )
		);
		if ( function_exists('kc_add_map') ) {
			$kc_posts = get_posts( array('post_type' => 'kc-section', 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1 ) ); 
			if ( isset( $kc_posts ) && is_array( $kc_posts ) ) {
				foreach ( $kc_posts as $post ) {
					$kc_sections[$post->ID] = $post->post_title;
				}
			}
		}

		return $kc_sections;
	}


	/**
	 * Kriki styling
	 * @return void
	 */
	public function configurationSampleStyling( $config ) {

		return wp_parse_args( array(
			'logo_image'   => false,
			'description'  => false,
			'color_accent' => '#f86239',
			'color_back'   => '#ffffff',
		), $config );
	}


	/**
	 * Register styles only on customizer page
	 * @return void
	 */
	public function registerStyles() {

		wp_enqueue_style( 'rascals-customizer-layout', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/css/admin-customizer.css' );
	}


	/**
	 * Register styles only on customizer page
	 * @return void
	 */
	public function removeCustomizeRegister( $wp_customize ) {;
		$wp_customize->remove_section("active_theme");
	}

}