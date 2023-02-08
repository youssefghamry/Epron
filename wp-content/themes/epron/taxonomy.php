<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            taxonomy.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();
    
// Copy query 
$temp_post = $post;
$query_temp = $wp_query;

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

// Get layout 
$content_layout = 'wide';

// Get loop block 
$block = 'simple-block';

// Columns
$block_option = '3';

// Set classes and variables
$sidebar = false;
$content_classes = array(
    'content page-template-simple',
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

// Current TAX
$queried_object = get_queried_object();
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;

// If Gallery 
if ( is_tax( 'epron_gallery_cats' ) )  {
    $block = 'gallery-block1';
    $block_option = '3';
    $content_layout = 'wide';
}
// Releases 
elseif ( is_tax( 'wp_release_genres' ) || is_tax( 'wp_release_artists' ) )  {


    $block = 'releases-block1';
    $block_option = '3';
    $content_layout = 'wide';
    $query_args = array(
        'post_type'      => 'wp_releases',
        'posts_per_page' => '-1',
        'sort_order'     => 'menu_order',
        'order'          => 'ASC',
        'paged'          => $paged,
        'tax_query'      => array(
           array(
                'taxonomy' => $taxonomy,
                'field'    => 'id',
                'terms'    => $term_id
            )
        ),
    );

}
// Events 
elseif ( is_tax( 'epron_events_cats' ) )  {
    $block = 'events-block2';
    $block_option = '3';
    $content_layout = 'wide';
}


//////////
// HERO //
//////////

get_template_part( 'partials/hero', 'category' );

?>

<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>">


    <div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">

        <div class="main">
            <?php

                if ( isset( $query_args) ) {
                    $wp_query = new WP_Query();
                    $wp_query->query( $query_args );
                }
                if ( have_posts() ) : ?>

                    <?php

                        // Render loop block
                        set_query_var( 'block_option', $block_option );
                        get_template_part( 'partials/loop', $block );
                        
                    ?>
                    <div class="clear"></div>
                    <?php 

                    // Pagination  
                    epron_paging_nav();
                   
                    ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'It seems we can not find what you are looking for.', 'epron' ); ?></p>

                <?php endif; // have_posts() ?>
        </div>  <!-- .main -->

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