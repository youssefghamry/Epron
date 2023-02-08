<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            loop-block.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

// Date Format 
$date_format = get_option( 'date_format' );

// Module classes 
$classes = array(
    'post-list-module',
    'full-size'
);

// Set color scheme 
$color_scheme = get_theme_mod( 'color_scheme', 'dark' );
$classes[] = $color_scheme . '-scheme-el';

// Module Opts 
$module_opts = array(
	'module'     => 'epron_module1',
	'thumb_size' => 'full',
	'excerpt'    => true,
	'readmore'   => 'yes',
	'classes'    => implode( ' ', $classes )
);
?>

<div data-module-opts='<?php echo json_encode( $module_opts ) ?>' class="ajax-grid flex-grid flex-1 flex-tablet-1 flex-mobile-1 flex-mobile-portrait-1 flex-gap-empty posts-container anim-grid" data-anim-effect="amun">
<?php

// Start Loop 
while ( have_posts() ) {

	the_post();

	$index = $wp_query->current_post + 1;

	// Get taxonomies 
	if ( function_exists( 'epron_get_taxonomies' ) ) {
	    $tax_args = array(
	        'id' => $wp_query->post->ID,
	        'tax_name' => 'category',
	        'separator' => ' · ',
	        'link' => true,
	        'limit' => 2,
	        'show_count' => true
	    );
	    $cats_html = epron_get_taxonomies( $tax_args );
	} else {
	    $cats_html = '';
	}

	// Module arguments 
	if ( function_exists( $module_opts['module'] ) ) {
		echo '<div class="flex-item">';
	    $epron_opts->e_esc( $module_opts['module']( array(
			'post_id'       => $wp_query->post->ID,
			'thumb_size'    => $module_opts['thumb_size'],
			'lazy'          => true,
			'title'         => get_the_title(),
			'permalink'     => get_permalink(),
			'author'        => get_the_author_meta( 'display_name', $wp_query->post->post_author ),
			'date'          => get_the_time( $date_format ),
			'posts_cats'    => $cats_html,
			'classes'       => $module_opts['classes'],
			'posts_classes' => implode( ' ', get_post_class( '', $wp_query->post->ID ) ),
			'excerpt'       => true,
			'readmore'      => 'yes',
			'placeholder'   => false
	    ) ) );
	    echo '</div>';
	}
}
?>
</div>