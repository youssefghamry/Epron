<?php
/**
 * Theme Name: 		Epron
 * Theme Author: 	Mariusz Rek - Rascals Themes
 * Theme URI: 		http://rascalsthemes.com/epron
 * Author URI: 		http://rascalsthemes.com
 * File:			functions.php
 * @package epron
 * @since 1.0.0
 */


/* ==================================================
   Set up the content width value based on the theme's design.
================================================== */
if ( ! isset( $content_width ) ) {
	$content_width = 680;
}


/* ==================================================
  Theme Translation 
================================================== */
load_theme_textdomain( 'epron', get_template_directory() . '/languages' );


/* ==================================================
  Admin Panel 
================================================== */
require_once( trailingslashit( get_template_directory() ) . '/admin/admin-init.php' );


/* ==================================================
  Theme Setup 
================================================== */
if ( ! function_exists( 'epron_setup' ) ) :

	/**
	 * epron setup.
	 *
	 * Set up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support post thumbnails.
	 *
	 */

function epron_setup() {

	// This theme styles the visual editor to resemble the theme style. 
	add_editor_style( get_template_directory_uri() . '/css/editor-style.css' );

	// Add RSS feed links to <head> for posts and comments. 
	add_theme_support( 'automatic-feed-links' );

	// Gutenberg support 
	add_theme_support(
    	'gutenberg',
    	array( 'wide-images' => true )
	);

	// Add Title tag 
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails, and declare two sizes. 
	add_theme_support( 'post-thumbnails' );

	set_post_thumbnail_size( 790, 500, array( 'center', 'center' ) );

	add_image_size( 'epron-content-thumb', 790, 500, array( 'center', 'center' ) );
	add_image_size( 'epron-large-square-thumb', 660, 660, array( 'center', 'center' ) );
	add_image_size( 'epron-medium-square-thumb', 400, 400, array( 'center', 'center' ) );

	// This theme uses wp_nav_menu() in two locations. 
	register_nav_menus( array(
		'top_menu'    => esc_html__( 'Top Menu', 'epron' ),
		'main'        => esc_html__( 'Main Menu', 'epron' ),
		'footer_menu' => esc_html__( 'Footer Menu', 'epron' )
	) );

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	// This theme uses its own gallery styles. 
	add_filter( 'use_default_gallery_style', '__return_false' );

	// Enable support for Woocommerce 
	add_theme_support( 'woocommerce' );

}

add_action( 'after_setup_theme', 'epron_setup' );

endif; 

// Add a theme init flag after activation 
function epron_active () {

	// Add new compatible option
	add_option( 'rascals_toolkit', 'epron' );
	wp_redirect( admin_url() . 'themes.php?page=admin-welcome' );
}
add_action('after_switch_theme', 'epron_active');

// Delete a theme init flag after deactivation 
function epron_dective () {

	delete_option( 'rascals_toolkit' );

	// Remove outdated compatibility with the extensions plug-in
	delete_option( 'epron_init' );
}
add_action( 'switch_theme', 'epron_dective' );


/* ==================================================
  Theme Toolkit
================================================== */
function epron_toolkit( $instance = null ) {

	if ( class_exists( 'EpronToolkit' ) && $instance === null ) {
		return true;
	} elseif ( class_exists( 'EpronToolkit' ) && $instance !== null ) {
		$toolkit = epronToolkit();
		if ( $instance !== null && property_exists( 'EpronToolkit', $instance ) ) {
			return $toolkit->$instance;
		} else {
			return false;
		}
		
		return $toolkit;
	}

	return false;

} 


/* ==================================================
  Google Fonts 
================================================== */
function epron_fonts_url() {

    	$font_url = '';
	    $epron_opts = epron_opts();
	    
	    if ( $epron_opts->get_option( 'use_google_fonts' ) === 'on' ) {
	        if ( $epron_opts->get_option( 'google_fonts' ) ) {
	             $font_url = add_query_arg( 'family', esc_attr( $epron_opts->get_option( 'google_fonts' ) ), "//fonts.googleapis.com/css" );
	        }
    	}

    return $font_url;
}


// Enqueue scripts and styles.
function epron_fonts_scripts() {
    wp_enqueue_style( 'epron-fonts', epron_fonts_url(), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'epron_fonts_scripts' );


/* ==================================================
   Required styles and scripts
================================================== */

if ( ! function_exists( 'epron_scripts_and_styles' ) ) :
function epron_scripts_and_styles() {
	
	global $post, $wp_query;

	$epron_opts = epron_opts();

	/* Add comment reply script when applicable */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	if ( $epron_opts->get_option( 'ajaxed' ) && $epron_opts->get_option( 'ajaxed' ) === 'on' ) {
		$ajaxed = 1;

	} else {
		$ajaxed = 0;
	}

	// Animation 
	wp_enqueue_script( 'anime', get_template_directory_uri() . '/js/anime.min.js', array( 'jquery' ), false, true );

	// Lazy load 
	wp_enqueue_script( 'lazy-load', get_template_directory_uri() . '/js/jquery.lazy.min.js', array( 'jquery' ), false, true );

	// Enable retina displays 
	if ( $epron_opts->get_option( 'retina' ) && $epron_opts->get_option( 'retina' ) === 'on' ) {
		wp_enqueue_script( 'retina', get_template_directory_uri() . '/js/retina.min.js', array( 'jquery' ), false, true );
	}

	// Enable FB JSSDK 
	if ( $epron_opts->get_option( 'fbsdk' ) && $epron_opts->get_option( 'fbsdk' ) === 'on' ) {
		wp_enqueue_script( 'facebook-jssdk', 'https://connect.facebook.net/' . esc_attr( get_locale() ) . '/sdk.js#xfbml=1&version=v2.7', array( 'jquery' ), false, true );
	}

	// Enable sticky sidebar 
	if ( get_theme_mod( 'sticky_sidebar', true ) === true ) {
		wp_enqueue_script( 'theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js', array( 'jquery' ), false, true );
	}

	$js_vars = array(
		'theme_uri' => get_template_directory_uri(),
	);

	wp_enqueue_script( 'epron-theme-scripts', get_template_directory_uri() . '/js/theme.js' , array('jquery', 'imagesloaded'), false, true );
	wp_localize_script( 'epron-theme-scripts', 'controls_vars', $js_vars );
	wp_localize_script( 'epron-theme-scripts', 'ajax_action', array('ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce( 'ajax-nonce') ) );


	/* Styles
	 -------------------------------- */
	wp_enqueue_style( 'icomoon', get_template_directory_uri() . '/icons/icomoon.css' );
	
	// Main styles
	wp_enqueue_style( 'epron-style', get_stylesheet_uri() );


    /* WooCommerce Style
     -------------------------------- */
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'woocommerce-theme-style', get_stylesheet_directory_uri() . '/css/woocommerce-theme-style.css' );	
	}
	
}

add_action( 'wp_enqueue_scripts', 'epron_scripts_and_styles' );
endif;


/* ==================================================
  Woocommerce 
================================================== */
if ( class_exists( 'WooCommerce' ) ) {

	$epron_opts = epron_opts();
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Add body class if page is excluded 
	if ( $epron_opts->get_option( 'ajaxed' ) && $epron_opts->get_option( 'ajaxed' ) === 'on' ) {

		if ( ! function_exists( 'wc_body_classes' ) ) {
			function wc_body_classes( $classes ) {

				if ( is_woocommerce() || is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() || is_checkout() || is_account_page() ){
					return array_merge( $classes, array( 'wp-ajax-exclude-page' ) );
				} else {
					return $classes;
				}

			}
			add_filter( 'body_class','wc_body_classes' );
		}
	}

}


/* ==================================================
  Sidebars 
================================================== */
function epron_widgets_init() {

	$epron_opts = epron_opts();

	register_sidebar( array(
		'name'          => esc_html__( 'Primary Sidebar', 'epron' ),
		'id'            => 'primary-sidebar',
		'description'   => esc_html__( 'Main sidebar that appears on the left or right.', 'epron' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 1', 'epron' ),
		'id'            => 'footer-col1-sidebar',
		'description'   => esc_html__( 'Footer Column 1 that appear on the botton of the page.', 'epron' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 2', 'epron' ),
		'id'            => 'footer-col2-sidebar',
		'description'   => esc_html__( 'Footer Column 2 that appear on the botton of the page.', 'epron' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 3', 'epron' ),
		'id'            => 'footer-col3-sidebar',
		'description'   => esc_html__( 'Footer Column 3 that appear on the botton of the page.', 'epron' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );


	// Custom sidebars
	if ( false !== get_option( 'custom_sidebars' ) ) {
		if ( $epron_opts->get_option( 'custom_sidebars' ) ) {
				
			foreach ( $epron_opts->get_option( 'custom_sidebars' ) as $sidebar ) {
				
				$id = sanitize_title_with_dashes( $sidebar[ 'name' ] );
				register_sidebar( array(
					'name'          => $sidebar[ 'name' ],
					'id'            => $id,
					'description'   => esc_html__( 'Custom sidebar created in admin options.', 'epron' ),
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				));
			}
		}
	}

}
add_action( 'widgets_init', 'epron_widgets_init' );


/* ==================================================
  Include necessary files
================================================== */
require_once( trailingslashit( get_template_directory() ) . '/inc/modules.php' );
require_once( trailingslashit( get_template_directory() ) . '/inc/blocks.php' );
require_once( trailingslashit( get_template_directory() ) . '/inc/helpers.php' );
require_once( trailingslashit( get_template_directory() ) . '/inc/ajax.php' );
require_once( trailingslashit( get_template_directory() ) . '/inc/template-tags.php' );	