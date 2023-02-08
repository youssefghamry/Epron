<?php
/**
 * Template Name: Events
 *
 * @package epron
 * @since 1.0.0
 */

get_header(); 

// Get panel options 
$epron_opts = epron_opts();
	
// Copy query 
$temp_post = $post;
$query_temp = $wp_query;

$more = 0;

// Set color scheme 
$color_scheme = get_theme_mod( 'color_scheme', 'dark' );

// Get layout 
$content_layout = get_post_meta( $wp_query->post->ID, '_content_layout', true );
$content_layout = ( $content_layout !== '' ? $content_layout : 'wide' );

// Hero Layout 
$hero_layout = get_post_meta( $wp_query->post->ID, '_hero_layout', true ); 
$hero_layout = ( ! isset( $hero_layout ) || $hero_layout === '' ) ? 'default' : $hero_layout;

// Background 
$content_bg = get_post_meta( $wp_query->post->ID, '_content_bg', true );

// Pagination 
$pagination = get_post_meta( $wp_query->post->ID, '_pagination', true );
$pagination = ( ! empty( $pagination ) ? $pagination : 'next_prev' );

// Fullwidth
$fullwidth = get_post_meta(  $wp_query->post->ID, '_fullwidth', true );

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
$page_builder = get_post_meta( $wp_query->post->ID, '_page_builder', true );
if ( class_exists( 'KingComposer' ) and $page_builder === 'on' ) {
	if ( $paged === 1 ) {
		$is_page_builder = true;
	}
} 

// Get loop block 
$block = get_post_meta( $wp_query->post->ID, '_block', true );
$block = ( $block !== '' ? $block : 'events-block1' );
$block_option = '';
switch ($block) {
	case 'events-block1': // list
		$block = 'events-block1';
		break;
	case 'events-block2': // grid
		$block = 'events-block2';
		$block_option = '4';
		break;
	case 'events-block3': // grid
		$block = 'events-block2';
		$block_option = '3';
		break;
	case 'events-block4': // grid
		$block = 'events-block2';
		$block_option = '2';
		break;

}
// Filter 
$filter_args = array(
	'filter_atts'       => get_post_meta( $wp_query->post->ID, '_filter_atts_events', false ), 
	'ajax_filter'       => get_post_meta( $wp_query->post->ID, '_ajax_filter', true ),
	'show_filters'      => get_post_meta( $wp_query->post->ID, '_show_filters', true ),
	'filter_sel_method' => get_post_meta( $wp_query->post->ID, '_filter_sel_method', true )
);

// Queries  
$filters_query = epron_prepare_events_filter( $filter_args );
$query_args = epron_prepare_wp_query( $filters_query, $paged );

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
?>

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

<div class="<?php echo esc_attr( implode(' ', $content_classes ) ) ?>">

	<div class="<?php echo esc_attr( implode(' ', $container_classes ) ) ?>">

		<div class="main">
	
			<!-- Filter -->
			<?php 

			$ajax_opts = array(
				'action' => 'epron_load_more'
			);

			?>
			<div class="ajax-grid-block <?php echo esc_attr( $color_scheme ) ?>-scheme-el" data-loading='false' data-paged='2' data-opts='<?php echo json_encode( $ajax_opts ); ?>' data-filter='<?php echo json_encode( $filters_query );?>'>
				<?php 

	    		// Filters 
		       	if ( function_exists( 'epron_get_filters' ) ) {
		       		echo epron_get_filters($filters_query);
		       	}
	  
	            $wp_query = new WP_Query();
				$wp_query->query( $query_args );

				// Show pagination? 
				if ( $paged === intval( $wp_query->max_num_pages ) ) {
        			$ajax_paged_visible = false;
    			} else {
    				$ajax_paged_visible = true;
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

    				// Prev/Next 
	    			if ( $pagination === 'next_prev' ) {
	    				epron_paging_nav();

	    			// Ajax 
	    			} else if ( $pagination === 'load_more' || $pagination === 'infinite' ) {
	    				if ( $ajax_paged_visible ) {
	    					if ( function_exists( 'epron_content_loader' ) ) {
                        		echo epron_content_loader();
                        	}

		    				$pagination_class = $pagination === 'infinite' ? 'infinite-load' : 'btn load-more';
		    				echo "<a class='" . esc_attr( $pagination_class ) . "'>";
		    				if ( $pagination === 'load_more' ) {
		    					_e( 'Load More', 'epron' );
		    				}
		    				echo "</a>";
	    				}
	    			}
	    			
	    			?>
				<?php else : ?>
					<p><?php esc_html_e( 'It seems we can not find what you are looking for.', 'epron' ); ?></p>

				<?php endif; // have_posts() ?>
			</div>
		</div>  <!-- .main -->
		<?php 
		// Restore query 
		$post = $temp_post;
		$wp_query = $query_temp;
		?>
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