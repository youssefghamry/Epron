<?php
/**
 * Theme Name:    Epron
 * Theme Author:  Mariusz Rek - Rascals Themes
 * Theme URI:     http://rascalsthemes.com/epron
 * Author URI:    http://rascalsthemes.com
 * File:          woocommerce.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get theme options
$epron_opts = epron_opts();

// Backup Main Query 
$temp_post = $post;
$query_temp = $wp_query;

// Shop id 
$shop_id = get_option( 'woocommerce_shop_page_id' );

// Get content layout 
$content_layout = get_post_meta( $shop_id , '_content_layout', true );
$content_layout = ( $content_layout !== '' ? $content_layout : 'sidebar-on-right' );

// Hero Layout 
$hero_layout = get_post_meta( $shop_id , '_hero_layout', true ); 
$hero_layout = ( ! isset( $hero_layout ) || $hero_layout === '' ) ? 'default' : $hero_layout;

// Background 
$content_bg = get_post_meta( $shop_id , '_content_bg', true );

// Fullwidth
$fullwidth = get_post_meta(  $shop_id , '_fullwidth', true );

if ( function_exists( 'epron_get_paged' ) ) {
    $paged = epron_get_paged();
}
if ( get_query_var( 'paged' ) ) { 
    $paged = get_query_var( 'paged' ); 
} elseif ( get_query_var( 'page' ) ) { 
    $paged = get_query_var( 'page' ); 
} else { 
    $paged = 1; 
}

// Page Builder 
$is_page_builder = false;
$page_builder = get_post_meta( $shop_id , '_page_builder', true );
if ( class_exists( 'KingComposer' ) and $page_builder === 'on' ) {
    if ( $paged === 1 ) {
        $is_page_builder = true;
    }
} 

// Set classes and variables
$sidebar = false;
$content_classes = array(
    'content'
);
$container_classes = array(
    'container'
);
if ( $content_layout === 'narrow' ) {
    array_push( $content_classes, 'page-layout-' . $content_layout );
    $container_classes[] = 'small';
} else if ( $content_layout === 'wide' ) {
    array_push( $content_classes, 'page-layout-' . $content_layout );
    $container_classes[] = 'wide';
} else if ( $content_layout === 'left_sidebar' ) {
    $sidebar = true;
    array_push( $content_classes, 'page-layout-' . $content_layout, 'layout-style-1', 'sidebar-on-left' );
} else if ( $content_layout === 'right_sidebar' ) {
    $sidebar = true;
    array_push( $content_classes, 'page-layout-' . $content_layout, 'layout-style-1', 'sidebar-on-right' );
}

// Fullwidth 
if ( $fullwidth ) {
    $container_classes[] = $fullwidth;
}


// Remove default page title 
add_filter('woocommerce_show_page_title', 'epron_override_page_title');
if ( ! function_exists( 'epron_override_page_title' ) ) {
    function epron_override_page_title() {
        return false;
    }
}

?>

<?php if ( is_shop() ) : ?>

    <?php if ( $is_page_builder === true ) : ?>

        <?php 
        $content_classes[] = 'page-builder-top';
        ?>
        <div class="content-full" 
            <?php if ( function_exists( 'epron_get_background' ) && epron_get_background( $content_bg ) ) {
                echo 'style="' . epron_get_background( $content_bg ) . '"';
            } ?>
        >
            <div class="container-full">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php 
                        // Render content via Page Builder 
                        epron_get_content();
                    ?>
                <?php endwhile; ?>
            </div>
        </div>
        <?php rewind_posts(); ?>
    <?php else : ?>

        <?php 
        // Default Hero
        if ( $hero_layout === 'default' ) {
            get_template_part( 'partials/hero', 'default' );
        }
        ?>

    <?php endif; ?>

<?php endif; ?>

<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>" 
    <?php if ( function_exists( 'epron_get_background' ) && epron_get_background( $content_bg ) ) {
        echo 'style="' . epron_get_background( $content_bg ) . '"';
    } ?>
>
    
    <div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">

        <div class="main">
            <?php if ( is_singular( 'product' ) ) : ?>
                <?php 
                  while ( have_posts() ) :
                    the_post();
                    wc_get_template_part( 'content', 'single-product' );
                  endwhile;
                ?>
            <?php else : ?>
                <?php if ( woocommerce_product_loop() ) : ?>
                    <?php 
                    // Simple Hero
                    if ( $hero_layout === 'simple' ) {
                        get_template_part( 'partials/hero', 'simple' );
                    }
                  
                    ?>
                    <?php do_action( 'woocommerce_before_shop_loop' ); ?>

                    <?php woocommerce_product_loop_start(); ?>

                    <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
                        <?php while ( have_posts() ) : ?>
                            <?php the_post(); ?>
                            <?php wc_get_template_part( 'content', 'product' ); ?>
                        <?php endwhile; ?>
                    <?php endif; ?>

                    <?php woocommerce_product_loop_end(); ?>

                    <?php do_action( 'woocommerce_after_shop_loop' ); ?>

                <?php else : ?>

                    <?php do_action( 'woocommerce_no_products_found' ); ?>

                <?php endif;?>
            <?php endif;?>    
        </div>
        <?php if ( $sidebar ) : ?>
        <div class="sidebar sidebar-block">
            <div class="theiaStickySidebar">
                <?php get_sidebar( 'shop' ); ?>
            </div>
        </div>
        <?php endif; ?>
    </div> <!-- .container -->
</div> <!-- .content -->

<?php

// Restore query 
$post = $temp_post;
$wp_query = $query_temp;

// Get footer
get_footer();