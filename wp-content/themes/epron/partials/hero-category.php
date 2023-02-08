<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            hero-category.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

// Extra classes
$extra_classes = array();

if ( get_theme_mod( 'show_hero_line', true ) === true ) {
	$extra_classes[] = 'has-line';
}

// Post type
$post_type = get_post_type( get_the_ID() );

$img_src = '';
$default_bg = get_theme_mod( 'default_page_bg', get_template_directory_uri() . '/images/default-bg.png' );

// Categories
if ( is_category() ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Category.', 'epron' );
}
// Author
elseif ( is_author() ) {
	$author_id = $wp_query->post->post_author;
	$title = get_the_author_meta( 'display_name', $author_id );
	$subtitle = esc_html__( 'Author Posts.', 'epron' );
}
// Tags
elseif ( is_tag() ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Tag.', 'epron' );
}
// Releases
elseif ( is_tax( 'wp_release_genres' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Releases.', 'epron' );
}
elseif ( is_tax( 'wp_release_artists' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Releases.', 'epron' );
}
// Artists
elseif ( is_tax( 'wp_artist_cats' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Artists.', 'epron' );
}
elseif ( is_tax( 'wp_artist_cats2' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Artists.', 'epron' );
}
// Gallery
elseif ( is_tax( 'wp_gallery_cats' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Gallery.', 'epron' );
}
elseif ( is_tax( 'wp_gallery_cats2' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Gallery.', 'epron' );
}
// Events
elseif ( is_tax( 'wp_event_categories' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Events.', 'epron' );
}
elseif ( is_tax( 'wp_event_cats2' ) ) {
	$title = single_cat_title('', false);
	$subtitle = esc_html__( 'Events.', 'epron' );
}

// Archive
elseif (is_archive()) {
	if ( is_year() ) {
		$title = get_the_time( 'Y' );
	}
	if ( is_month() ) { 
		$title = get_the_time( 'F, Y' );
	}
	if ( is_day() || is_time() ) {
		$title  = get_the_time( 'l - ' . get_option( 'date_format' ) );
	}
	$subtitle = esc_html__( 'Archives', 'epron' );
}
// Search
elseif ( is_search() ) {
	$title = get_search_query();
	$subtitle = esc_html__( 'Search Results', 'epron' );
}

// Index
else {
	$title = esc_html__( 'Blog', 'epron' );
	$subtitle = esc_html__( 'Latest Eprom news.', 'epron' );
}
?>

<div class="hero hero-category hero-header <?php echo esc_attr( implode(' ', $extra_classes ) ) ?>" >
<?php

// Hero image - Lazy load 
if ( $epron_opts->get_option( 'lazyload' ) === 'on' ) : ?>
	<div class="hero-image lazy" data-src="<?php echo esc_attr( $default_bg ); ?>" ></div>
<?php else : ?>
	<div class="hero-image" style="background-image:url(<?php echo esc_attr( $default_bg ); ?>);"></div>
<?php endif; ?>

	<div class="container">
    	<div class="hero-headings">
		    <h1 class="hero-heading hero-title"><?php echo wp_kses_post( $title ) ?></h1>
		    <h5 class="hero-heading hero-subtitle"><?php echo wp_kses_post( $subtitle ) ?></h5>
		</div>
		<?php 
		///////////////////
		// SHARE BUTTONS //
		///////////////////
		if ( get_theme_mod( 'show_share_buttons', '0' ) === true && function_exists( 'wp_toolkit_share' ) ) : ?>
		    <?php echo wp_toolkit_share( get_the_ID(), true ); ?>
		<?php endif; ?>
	</div>
	<div class="overlay overlay-gradient-2"></div>
</div>