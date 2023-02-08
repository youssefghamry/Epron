<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            blocks.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ==================================================
  Releated Posts 
================================================== */
if ( ! function_exists( 'epron_block_rp' ) ) :

function epron_block_rp( $params = array() ) {

    global $wp_query, $post;

    // The defaults will be overidden if set in $params 
    $defaults = array( 
        'post_id'         => 0,
        'display_by'      => 'tags',
        'show_navigation' => true,
        'limit'           => 3,
        'excerpt'         => false,
        'module'          => 'epron_module1',
        'module_classes'  => 'small-module anim-zoom flex-col-1-3 ajax-item',
        'thumb_size'      => 'epron-medium-square-thumb',
        'classes'         => 'gap nav-style-2'
    );

    // Copy query 
    $temp_post = $post;
    $query_temp = $wp_query;

    // Variables 
    $html             = '';
    $terms            = '';
    $module_html      = '';
    $date_format      = get_option( 'date_format' );
    $ajax_category_in = '';
    $ajax_tag_in      = '';

    // If $params is not array 
    if ( ! is_array( $params ) ) {
        return false;
    }
    $params = array_merge( $defaults, $params );
    extract( $params, EXTR_PREFIX_SAME, 'module' );

    // Display by Tags or Categories 
    if ( $display_by === 'categories' ) {
        $terms = get_the_category( $post_id );
    } else {
        $terms = wp_get_post_tags( $post_id );
    }

    if ( $terms ) {
        $terms_ids = array();
        foreach( $terms as $individual_term ) {
            $terms_ids[] = $individual_term->term_id;
        }
        $args = array(
            'post__not_in'        => array( $post_id ),
            'showposts'           => $limit,
            'ignore_sticky_posts' => 1
        );

        if ( $display_by === 'categories' ) {
            $args['category__in'] = $terms_ids;
            $ajax_category_in = implode( ',', $terms_ids );
        } else {
            $args['tag__in'] = $terms_ids;
            $ajax_tag_in = implode( ',', $terms_ids );
        }

        // Check how many post are available
        $temp_args = $args;
        $temp_args['showposts'] = -1;
        $temp_query_count = new WP_Query();
        $temp_query_count->query( $temp_args );
        $posts_nr = $temp_query_count->post_count;

        // Check how many post are available
        if ( $posts_nr > $limit && $show_navigation === true ) {
            $show_nav = true;
        } else {
            $show_nav = false;
        }

        $related_posts_q = new wp_query( $args );

        if ( $related_posts_q->have_posts() ) {

            $html .= '<div class="block block-rp ' . esc_attr( $classes ) . '">';
            $html .= '<h4 class="block-rp-title">' . esc_html__( 'More Posts', 'epron' ) . '</h4>';

            $html .= '<div class="ajax-posts-slider anim anim-slide-from-right" data-obj=\'{"action": "epron_posts_slider_ajax", "cats": "", "category__in": "' . esc_attr( $ajax_category_in ) . '", "tag__in": "' . esc_attr( $ajax_tag_in ) . '", "post__not_in": "' . esc_attr( $post_id ) . '", "ignore_sticky_posts": "1", "cpt": "post", "tax": "category", "limit": "' . esc_attr( $limit ) . '", "excerpt": "' . esc_attr( $excerpt ) . '", "thumb_size": "' . esc_attr( $thumb_size ) . '", "module" : "' . esc_attr( $module ) . '" }\' data-pagenum="1">';
            $html .= '<div class="ajax-posts-slider-inner">';
            while( $related_posts_q->have_posts() ) {
                $related_posts_q->the_post();

                // Get taxonomies
                if ( function_exists( 'epron_get_taxonomies' ) ) {
                    $tax_args = array(
                        'id'         => $related_posts_q->post->ID,
                        'tax_name'   => 'category',
                        'separator'  => ' / ',
                        'link'       => true,
                        'limit'      => 1,
                        'show_count' => true
                    );
                    $cats_html = epron_get_taxonomies( $tax_args );
                } else {
                    $cats_html = '';
                }

                // Excerpt
                if ( $excerpt ) {
                    if ( has_excerpt() ) {
                        $excerpt = wp_trim_words( get_the_excerpt(), 30, '' );
                    } else {
                        $excerpt = wp_trim_words( strip_shortcodes( get_the_content() ), 30, '' ); 
                    }
                }

                $module_classes = array(
                    'flex-col-1-' . esc_attr( $limit ),
                    'ajax-item',
                    'small-module',
                    'anim-zoom'
                );
                
                if ( function_exists( $module ) ) {

                    $module_html = $module( array( 
                        'post_id'       => $related_posts_q->post->ID,
                        'thumb_size'    => $thumb_size,
                        'lazy'          => true,
                        'title'         => get_the_title(),
                        'permalink'     => get_permalink(),
                        'author'        => get_the_author_meta( 'display_name', $related_posts_q->post->post_author ),
                        'date'          => get_the_time( $date_format ),
                        'posts_cats'    => $cats_html,
                        'excerpt'       => $excerpt,
                        'readmore'      => '',
                        'posts_classes' => implode( ' ', get_post_class( '', $related_posts_q->post->ID ) ),
                        'classes'       => implode(' ', $module_classes )
                    ) );
                }

                $html .= $module_html;

            }
            $html .= '</div>';

            if ( $show_nav ) {
                $html .= '<div class="arrows-nav-block"><div class="arrow-nav left disabled"><span><i class="icon icon-angle-left"></i></span></div><div class="arrow-nav right"><span><i class="icon icon-angle-right"></i></span></div></div>';
                    if ( function_exists( 'epron_content_loader' ) ) {
                        $html .= epron_content_loader();
                    }
            }

            $html .= '</div></div>';

        }
    }
    
    // Restore main query 
    $post = $temp_post;
    $wp_query = $query_temp;

    return $html;
    
}
endif;