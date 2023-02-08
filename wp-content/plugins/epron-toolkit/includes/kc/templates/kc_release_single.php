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
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Add custom classes to element 
$wrap_class[] = 'kc-single-release-wrap';

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

// Module classes
if ( isset( $atts['module_classes'] ) ) {
    $atts['module_classes'] .= ' ' . $atts['color_scheme'] . '-scheme-el';
}

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

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?>">
    <div class="kc-single-release">
        <?php

        // Variables 
        $showposts = 1;
        $thumb_size = 'epron-large-square-thumb';
        $date_format = get_option( 'date_format' );

        // Filter 
        $filter_atts = array(
            'post_ids'        => $atts['post_ids'],
            'sort_order'      => $atts['sort_order'],
            'limit'           => 1,
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
            'ajax_filter'       => false,
            'show_filters'      => '',
            'filter_sel_method' => ''
        );
    
        // Filter Queries  
        $filters_query = epron_prepare_releases_filter( $filter_args );
        $query_args = epron_prepare_wp_query( $filters_query, 0 );

        // Begin Loop  
        $posts_block_q = new WP_Query( $query_args );
        
       ?>

            <?php ob_start(); ?>

            <?php 
                // Filters
                if ( function_exists( 'epron_get_filters' ) ) {
                    echo epron_get_filters($filters_query);
                }
            ?>

            <?php
            // Show pagination? 
            if ( $paged === intval( $posts_block_q->max_num_pages ) ) {
                $ajax_paged_visible = false;
            } else {
                $ajax_paged_visible = true;
            }

            if ( $posts_block_q->have_posts() ) : ?>
                <?php while ( $posts_block_q->have_posts() ) : ?>
                <?php
                    $posts_block_q->the_post();
                    $count = $posts_block_q->post_count; 

                    $module_args = array(
                        'post_id'       => $posts_block_q->post->ID,
                        'thumb_size'    => $thumb_size,
                        'lazy'          => false,
                        'title'         => get_the_title(),
                        'permalink'     => get_permalink(),
                        'posts_classes' => implode( ' ', get_post_class( '', $posts_block_q->post->ID ) ),
                        'classes'       => $atts['module_classes']
                    );

                    echo epron_release_module2( $module_args ); 
                ?>
               <?php endwhile ?>
            
            <?php else : ?>
            <div class="warning"><?php esc_html_e( 'Warning: There are no items to display this module.', 'epron-toolkit' ); ?></div>
            <?php endif; ?>
    </div>
</div>

<?php
    
    wp_reset_postdata();

    $output = ob_get_clean();

    $toolkit->e_esc( $output ); 
    // kc_js_callback( 'theme.plugins.ThumbSlider' );
    kc_js_callback( 'theme.plugins.Tooltip' );
?>