<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            single-wp_artists.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();

// Get content layout 
$content_layout = get_post_meta( $wp_query->post->ID, '_content_layout', true );
$content_layout = ( $content_layout !== '' ? $content_layout : 'wide' );

// Hero Layout 
$hero_layout = get_post_meta( $wp_query->post->ID, '_hero_layout', true ); 
$hero_layout = ( ! isset( $hero_layout ) || $hero_layout === '' ) ? 'default' : $hero_layout;

// Background 
$content_bg = get_post_meta( $wp_query->post->ID, '_content_bg', true );

// Set classes and variables 
$sidebar = false;
$content_classes = array(
	'content page-template-simple'
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
	array_push( $content_classes, 'page-layout-' . $content_layout, 'sidebar-on-left' );
} else if ( $content_layout === 'right_sidebar' ) {
	$sidebar = true;
	array_push( $content_classes, 'page-layout-' . $content_layout, 'sidebar-on-right' );
} else if ( $content_layout === 'page_builder' && class_exists( 'KingComposer' ) ) {
	unset( $container_classes, $content_classes );
	$content_classes[] = 'content-full';
	$container_classes[] = 'container-full';
}

?>

<?php 
// Default Hero
if ( $hero_layout === 'default' ) {
	get_template_part( 'partials/hero', 'default' );
}
?>

<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>" 
	<?php if ( function_exists( 'epron_get_background' ) && epron_get_background( $content_bg ) ) {
		echo 'style="' . epron_get_background( $content_bg ) . '"';
	} ?>
>

	<div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">
		<div class="main">

		<?php 
		// Simple Hero
		if ( $hero_layout === 'simple' ) {
			get_template_part( 'partials/hero', 'simple' );
		}
		?>
			
		<?php 
		while ( have_posts() ) { 
			the_post();

	    	// Render content via Page Builder 
	  		epron_get_content();
	
	        wp_link_pages( array(
	            'before'    => '<div class="clear"></div><div class="page-links">' . esc_html__( 'Jump to Page', 'epron' ),
	            'after'     => '</div>',
	        ) );
		
		}

		echo '<div class="clear"></div>';

		// If comments are open or we have at least one comment, load up the comment template.
		if ( get_theme_mod( 'posts_comments', true ) ) {
			if ( in_array( 'content-full' , $content_classes ) ) {
				echo '<div class="container">';
			}
			if ( comments_open() || get_comments_number() ) {
				$disqus = $epron_opts->get_option( 'disqus_comments' );
				$disqus_shortname = $epron_opts->get_option( 'disqus_shortname' );

				if ( ( $disqus && $disqus === 'on' ) && ( $disqus_shortname && $disqus_shortname !== '' ) ) {
					get_template_part( 'inc/disqus' );
				} else {
					comments_template();
				}
			}
			if ( in_array( 'content-full' , $content_classes ) ) {
				echo '</div>';
			}
		}
		?>
		</div>  <!-- .main -->

        <?php if ( $sidebar ) : ?>
            <div class="sidebar sidebar-block">
                <div class="theiaStickySidebar">
                    <?php get_sidebar( 'custom' ); ?>
                </div>
            </div>
        <?php endif; ?>
    </div> <!-- .container -->
</div> <!-- .content -->

<?php

// Get footer
get_footer();