<?php
/**
 *
 * Contains the main King Composer functions
 *
 *
 * @package         EpronToolkit
 * @author          Rascals Themes
 * @copyright       Rascals Themes
 * @version       	1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


////////////////////
// ADD JS SUPPORT //
////////////////////

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_posts_carousel_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_posts_slider_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_artists_carousel_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_releases_carousel_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_events_carousel_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_gallery_carousel_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}

/**
 * Add Carousel support
 * @param  array  $atts
 * @return array
 */
function kc_gallery_images_carousel_filter( $atts = array() ){

    $atts = kc_remove_empty_code( $atts );
    extract( $atts );

    wp_enqueue_script( 'owl-carousel' );
    wp_enqueue_style( 'owl-theme' );
    wp_enqueue_style( 'owl-carousel' );

    return $atts;
}


/**
 * KC Filters / add supports for embeded scripts
 * @param  $filters
 * @return void
 */
function epron_toolkit_add_scripts_support( $filters ) {
    $filters[] = 'posts_carousel';
    $filters[] = 'posts_slider';
    $filters[] = 'artists_carousel';
    $filters[] = 'releases_carousel';
    $filters[] = 'events_carousel';
    $filters[] = 'gallery_carousel';
    $filters[] = 'gallery_images_carousel';
    return $filters;
}

// Change KC Filters
add_filter( 'kc-core-shortcode-filters', 'epron_toolkit_add_scripts_support', 10 );