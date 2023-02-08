<?php
/**
 * Rascals King Composer Extensions
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Plugin Toolkit Class 
$toolkit = epronToolkit();

// Kingcomposer wrapper class for each element 
$wrp_el_classes = apply_filters( 'kc-el-class', $atts );

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrp_el_classes[] =  $atts['color_scheme'] . '-scheme-el';

// Module classes
if ( isset( $atts['module_classes'] ) ) {
    $atts['module_classes'] .= ' ' . $atts['color_scheme'] . '-scheme-el';
}

// Variables 
$showposts = 3;
$thumb_size = $atts['thumb_size'];
$output = '';
$date_format = get_option( 'date_format' );
$separator = ' / ';

// Filter 
$filter_atts = array(
    'post_ids'        => $atts['post_ids'],
    'sort_order'      => $atts['sort_order'],
    'limit'           => (int)$atts['limit'],
    'offset'          => $atts['offset'],
    // Filters  
    'filters_order'   => $atts['filters_order'],
    // 1  
    'category_label'  => $atts['category_label'],
    'category_ids'    => $atts['category_ids'],
    'category_slugs'  => $atts['category_slugs'],
    // 2  
    'category_label2' => $atts['category_label2'],
    'category_ids2'   => $atts['category_ids2'],
    'category_slugs2' => $atts['category_slugs2'],

);
$filter_args = array(
    'filter_atts'       =>  $filter_atts, 
    'ajax_filter'       => '',
    'show_filters'      => '',
    'filter_sel_method' => ''
);

// Filter Queries  
$filters_query = epron_prepare_gallery_filter( $filter_args );
$query_args = epron_prepare_wp_query( $filters_query, 0 );

// Begin Loop  
$the_query = new WP_Query( $query_args );

$element_attribute = array();
$el_classess = array(
    'kc-owl-post-carousel',
    'kc-posts-carousel',
    'kc-gallery-carousel',
    'owl-carousel',
    'list-post',
    $atts['classes']
);

if( isset($atts['nav_style']) && $atts['nav_style'] !='' ){
    $el_classess[] = 'owl-nav-' . $atts['nav_style'];
}

$owl_option = array(
    'items'         => $atts['items_number'],
    'mobile'        => $atts['mobile'],
    'tablet'        => $atts['tablet'],
    'speed'         => intval( $atts['speed'] ),
    'navigation'    => $atts['navigation'],
    'pagination'    => $atts['pagination'],
    'autoheight'    => $atts['auto_height'],
    'autoplay'      => $atts['auto_play']
);

$owl_option = strtolower( json_encode( $owl_option ) );

$element_attribute[] = 'class="'. esc_attr( implode(' ', $el_classess) ) .' ' . esc_attr( $atts['classes'] ) . '"';
$element_attribute[] = "data-owl-options='$owl_option'";

ob_start();

if ( $the_query->have_posts() ) :

    echo '<div '. implode(' ', $element_attribute) .'>';

    while ( $the_query->have_posts() ) :

        $the_query->the_post();
        ?>
        <div class="item list-item">

            <?php 
            if ( empty( $thumb_size ) ) {
                $thumb_size = 'epron-large-square-thumb';
            }

            $module_args = array(
                'post_id'       => $the_query->post->ID,
                'thumb_size'    => $thumb_size,
                'lazy'          => true,
                'date'          => get_the_time( $date_format ),
                'title'         => get_the_title(),
                'permalink'     => get_permalink(),
                'posts_classes' => implode( ' ', get_post_class( '', $the_query->post->ID ) ),
                'classes'       => $atts['module_classes'],
            );
            $module = 'epron_gallery_' . $atts['module'];
            $toolkit->e_esc( $module( $module_args ) ); 
            
            ?>

        </div>
    <?php endwhile; ?>

    </div>

<?php else : ?>

    <?php esc_html_e( 'Carousel Post: No posts found', 'epron-toolkit' ); ?>

<?php endif; ?>

<?php 
    wp_reset_postdata();

    $output = ob_get_clean();

    echo '<div class="kc-carousel-post '. esc_attr( implode(' ', $wrp_el_classes) ) .'">'. $output .'</div>';

    kc_js_callback( 'kc_front.owl_slider' );
?>