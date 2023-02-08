<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            hero-default.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

// If Shop
if ( class_exists( 'WooCommerce' ) && is_shop() ) {
    $post_id = get_option( 'woocommerce_shop_page_id' );
} else {
    $post_id = $wp_query->post->ID;
}

// Extra classes
$extra_classes = array();

if ( get_theme_mod( 'show_hero_line', true ) === true ) {
	$extra_classes[] = 'has-line';
}

// Post type
$post_type = get_post_type( get_the_ID() );

$img_src = '';
$default_bg = get_theme_mod( 'default_page_bg', get_template_directory_uri() . '/images/default-bg.png' );
$hero_image = get_post_meta( $post_id, '_hero_image', true );
$hero_top_padding = get_post_meta( $post_id, '_hero_top_margin', true );
$hero_top_padding = ( $hero_top_padding === '' ? '50' : $hero_top_padding );
$hero_bottom_padding = get_post_meta( $post_id, '_hero_bottom_margin', true );
$hero_bottom_padding = ( $hero_bottom_padding === '' ? '50' : $hero_bottom_padding );
if ( $hero_image !== '' ) {
	$img_src = wp_get_attachment_image_src( $hero_image, 'full' );
    $img_src = $img_src[0];
} else {
	$img_src = $default_bg;
}
$hero_position = get_post_meta( $post_id, '_hero_bg_position', true );
$hero_position = ( $hero_position === '' ? 'top' : $hero_position );

?>

<div class="hero hero-header <?php echo esc_attr( implode(' ', $extra_classes ) ) ?>" style="padding-top:<?php echo esc_attr( $hero_top_padding ) ?>px;padding-bottom:<?php echo esc_attr( $hero_bottom_padding ) ?>px" >
<?php

// Hero image - Lazy load 
if ( $epron_opts->get_option( 'lazyload' ) === 'on' ) : ?>
	<div class="hero-image lazy" data-bg="url(<?php echo esc_attr( $img_src ); ?>)" style="background-position-y:<?php echo esc_attr( $hero_position )?>;"></div>
<?php else : ?>
	<div class="hero-image" style="background-image:url(<?php echo esc_attr( $img_src ); ?>);"></div>
<?php endif; ?>

	<div class="container">
    	<?php
		// Get Post Title 
		get_template_part( 'partials/single', 'title' );
		?>
	</div>
	<div class="overlay overlay-gradient-2"></div>
</div>