<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            post-related-posts.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

if ( get_theme_mod( 'show_related_posts', '0' ) === true && function_exists( 'epron_block_rp' ) ) {
	// Display Related Posts 
	echo epron_block_rp( array(
		'post_id'         => $wp_query->post->ID,
		'display_by'      => 'categories', //tags
		'limit'           => 3,
		'show_navigation' => 'yes',
		'module'          => 'epron_module3',
		'thumb_size'      => 'epron-medium-square-thumb',
		'excerpt'         => false,
		'classes'         => 'small-gap'
		)
	);
}

?>