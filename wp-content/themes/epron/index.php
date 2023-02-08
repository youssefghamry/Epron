<?php
/**
 * Theme Name: 		Epron
 * Theme Author: 	Mariusz Rek - Rascals Themes
 * Theme URI: 		http://rascalsthemes.com/epron
 * Author URI: 		http://rascalsthemes.com
 * File:			index.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();
   	
// Copy query 
$temp_post = $post;
$query_temp = $wp_query;

// Thumb Size 
$thumb_size = 'epron-content-thumb';

// Pagination 
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

// Date format 
$date_format = get_option( 'date_format' );

//////////
// HERO //
//////////

get_template_part( 'partials/hero', 'category' );

?>
<div class="content is-hero layout-style-1 sidebar-on-right blog-list blog-list-index">
	<div class="container">
		<div class="main">
			<?php
			$args = array(
				'paged'               => $paged,
				'ignore_sticky_posts' => false
            );
            $wp_query = new WP_Query();
			$wp_query->query($args);

			if ( have_posts() ) : ?>

				<?php

				/* Block 3
				 -------------------------------- */
				get_template_part( 'partials/loop', 'block' );
					
				?>

			<div class="clear"></div>
    		<?php epron_paging_nav(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'It seems we can not find what you are looking for.', 'epron' ); ?></p>
			<?php endif; // have_posts() ?>
				
		</div>  <!-- .main -->

         <div class="sidebar sidebar-block">
            <div class="theiaStickySidebar">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div> <!-- .container -->
</div> <!-- .content -->

<?php

// Restore query 
$post = $temp_post;
$wp_query = $query_temp;

// Get footer
get_footer();