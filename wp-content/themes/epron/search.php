<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            search.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();

// Get layout 
$content_layout = 'wide';

// Get loop block 
$block = 'simple-block';

// Columns
$block_option = '3';

// Set classes and variables
$sidebar = false;
$content_classes = array(
    'content',
    'is-hero'
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

//////////
// HERO //
//////////

get_template_part( 'partials/hero', 'category' );

?>

<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>">
    <div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">

        <div class="main">
            <?php if ( have_posts() ) : ?>
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
                <div class="search-404">
                    <h6 class="big-text"><?php esc_html_e( 'Oops...', 'epron' ) ?></h6>
                    <h4 class="no-margin"><?php esc_html_e( 'It seems we can not find what you are looking for.', 'epron' ); ?></h4>
                    <p><?php esc_html_e( 'How about trying our search', 'epron' ) ?></p>
                    <p id="search-404-form">
                        <?php get_search_form(); ?>
                    </p>
                </div>

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

// Get footer
get_footer();