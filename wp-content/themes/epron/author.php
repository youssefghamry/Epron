<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            author.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();
    
// Copy query 
$temp_post = $post;
$query_temp = $wp_query;

// Get layout 
$content_layout = 'wide';

// Get loop block 
$block = 'block2';

// Columns
$block_option = '3';


// Pagination 
$pagination = get_theme_mod( 'term_pagination', 'next_prev' );
$author_slug = get_query_var( 'author_name' );
$author_id = $wp_query->query_vars['author'];

$filter_atts = array(
    'author_ids' => $author_id,
    'limit'      => '9',
    'sort_order' => 'post_date',
    'offset'     => '0'
);
$query_args = epron_prepare_wp_query( $filter_atts, $paged );

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




?>


<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>">
    
    <div class="page-header author-header default-image-header">
         <div class="author-desc">
            <?php if ( isset( $paged ) and $paged === 0 ) : ?>
            <div class="author-gravatar"><?php echo get_avatar( get_the_author_meta( 'email', $author_id ), '150' ); ?></div>
            <h1>
            <?php  echo get_the_author_meta( 'display_name', $author_id ); ?>
            </h1>
            <div class="author-text-block">
                <p><?php echo get_the_author_meta( 'description', $author_id ); ?></p>
                <span class="posts-count">
                    <?php $user_post_count = count_user_posts( $author_id, 'post' ); ?>
                    <span><?php echo esc_html( $user_post_count ); ?></span>
                    <?php _e( 'posts', 'epron' ); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">

        <div class="main">
            <?php
                if ( have_posts() ) : ?>

                    <?php

                    /* Show pagination? */
                    if ( ($paged+1) === intval( $wp_query->max_num_pages ) ) {
                        $ajax_paged_visible = false;
                    } else {
                        $ajax_paged_visible = true;
                    }

                    // Render loop block
                    set_query_var( 'block_option', $block_option );
                    get_template_part( 'partials/loop', $block );
                        
                    ?>
                    <div class="clear"></div>
                    <?php 

                    // Pagination 
                    if ( $ajax_paged_visible ) {

                        // Prev/Next 
                        if ( $pagination === 'next_prev' ) {
                            epron_paging_nav();

                        // Ajax 
                        } else if ( $pagination === 'load_more' || 'infinite' ) {
                            $pagination_class = $pagination === 'infinite' ? 'infinite-load' : 'btn load-more'; 
                            $ajax_opts = array(
                                'posts_container' => '#ajax-grid',
                                'action' => 'epron_load_more'
                            );
                            
                            echo "<a class='" . esc_attr( $pagination_class ) . "' data-loading='false' data-paged='2' data-opts='" . json_encode( $ajax_opts ) . "' data-filter='" . json_encode( $filter_atts ) . "'>";

                            if ( $pagination === 'infinite' && function_exists( 'epron_content_loader' ) ) {
                                echo epron_content_loader();
                            } else {
                                _e( 'Load More', 'epron' );
                            }

                            echo "</a>";
                        }
                    }

                    ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'It seems we can not find what you are looking for.', 'epron' ); ?></p>
                <?php endif; // have_posts() ?>
        </div> <!-- .main -->

        <?php if ( $sidebar ) : ?>
            <div class="sidebar sidebar-block">
                <div class="theiaStickySidebar">
                    <?php get_sidebar(); ?>
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