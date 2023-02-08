<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            single-title.php
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

// Date Format 
$date_format = get_option( 'date_format' );

// Post type
$post_type = get_post_type( get_the_ID() );

// Hero Subtitle
$hero_subtitle = get_post_meta( $post_id, '_hero_subtitle', true ); 

?>

<?php if ( get_theme_mod( 'show_post_header_details', '0' ) === true && $post_type === 'post' ) : ?>
<div class="post-meta-top">
    <div class="post-header-cats cats-style">
    	<?php if ( function_exists( 'epron_get_taxonomies' ) ) {
            $tax_args = array(
                'id'         => $post_id,
                'tax_name'   => 'category',
                'separator'  => ' / ',
                'link'       => true,
                'limit'      => 200,
                'show_count' => true
            );
            echo epron_get_taxonomies( $tax_args );
        } ?>
    </div><span class="date"><?php echo get_the_time( $date_format, $post_id ); ?></span>
</div>
<?php endif; ?>

<div class="hero-headings">
    <h1 class="hero-heading hero-title"><?php echo get_the_title( $post_id ) ?></h1>
    <?php if ( isset( $hero_subtitle ) && $hero_subtitle !== '' ) : ?>
        <h5 class="hero-heading hero-subtitle"><?php echo wp_kses_post( $hero_subtitle ) ?></h5>
    <?php endif; ?>
</div>

<?php 

///////////////////
// SHARE BUTTONS //
///////////////////

$is_share_buttons = get_theme_mod( 'show_share_buttons', '0' );

// Disable buttons on WC pages
if ( epron_is_wc_page() === true ) {
    $is_share_buttons = false;
}

if ( $is_share_buttons === true && function_exists( 'epron_toolkit_share' ) ) : ?>
    <?php echo epron_toolkit_share( get_the_ID(), true ); ?>
<?php endif; ?>