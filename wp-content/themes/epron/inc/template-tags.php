<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            template-tags.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ==================================================
  Get Paged
================================================== */
if ( ! function_exists( 'epron_get_paged' ) ) :
function epron_get_paged() {
	global $paged;
	return $paged;
}
endif;


/* ==================================================
  Loop Pagination 
================================================== */
if ( ! function_exists( 'epron_paging_nav' ) ) :
function epron_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => esc_html__( '&larr; Prev', 'epron' ),
		'next_text' => esc_html__( 'Next &rarr;', 'epron' ),
	) );

	if ( $links ) :

	?>
	<nav class="navigation paging-navigation">
		<div class="pagination loop-pagination">
			<?php 
			echo paginate_links( array(
				'base'     => $pagenum_link,
				'format'   => $format,
				'total'    => $GLOBALS['wp_query']->max_num_pages,
				'current'  => $paged,
				'mid_size' => 1,
				'add_args' => array_map( 'urlencode', $query_args ),
				'prev_text' => esc_html__( '&larr;', 'epron' ),
				'next_text' => esc_html__( '&rarr;', 'epron' ),
			) );

			?>
		</div><!-- .pagination -->
	</nav><!-- .navigation -->
	<?php
	endif;
}
endif;


/* ==================================================
  Post Pagination
  Display navigation to next/previous post when applicable. 
================================================== */
if ( ! function_exists( 'epron_post_nav' ) ) :
function epron_post_nav() {
	global $post;

	$epron_opts = epron_opts();
	
	// Don't print empty markup if there's nowhere to navigate. 
	$previous  = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next      = get_adjacent_post( false, '', false );
	$post_type = get_post_type( $post->ID );

	if ( ! $next && ! $previous ) {
		echo '<nav class="navigation post-navigation empty"></nav>';
		return;
	}

	$next_label = esc_html__( 'Next Post', 'epron' );
	$prev_label = esc_html__( 'Prev Post', 'epron' );

	$next_link = get_adjacent_post( false,'',false );          
	$prev_link = get_adjacent_post( false,'',true ); 

	$next_post_thumb = '';
	$prev_post_thumb = '';

	?>
	<?php
	if ( is_attachment() ) :
		echo '<div class="container"><span class="attachment-post-link">';
		previous_post_link( '%link', '<span class="meta-nav">' . esc_html__( 'Published In', 'epron' ) . '</span>' . esc_html__( '%title', 'epron' ) );
		echo '</span></div>';
		echo '<nav class="navigation post-navigation empty"></nav>';
	else : ?>
	<nav class="navigation post-navigation">
		<div class="nav-links">
			<?php
				if ( empty( $prev_link ) && $next_link ) {
					echo '<span class="post-nav-inner link-empty"></span>';
				 	echo '<span class="post-nav-inner link-full"><a href="' . esc_url( get_permalink( $next_link->ID ) ) . '" class="next-link"><span class="nav-desc"><span class="nav-direction">' .  $epron_opts->esc( $next_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $next_link->ID ) ) . '</span></span></a></span>';
				} else if ( $prev_link && empty( $next_link ) ) {
				 	echo '<span class="post-nav-inner link-full"><a href="' . esc_url( get_permalink( $prev_link->ID ) ) . '" class="prev-link"><span class="nav-desc"><span class="nav-direction">' . esc_html( $prev_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $prev_link->ID ) ) . '</span></span></a></span>';
					echo '<span class="post-nav-inner link-empty"></span>';
				} else if ( $prev_link && $next_link  ) {
				 	echo '<span class="post-nav-inner"><a href="' . esc_url( get_permalink( $prev_link->ID ) ) . '" class="prev-link"><span class="nav-desc"><span class="nav-direction">' . esc_html( $prev_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $prev_link->ID ) ) . '</span></span></a></span>';
				 	echo '<span class="post-nav-inner"><a href="' . esc_url( get_permalink( $next_link->ID ) ) . '" class="next-link"><span class="nav-desc"><span class="nav-direction">' . esc_html( $next_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $next_link->ID ) ) . '</span></span></a></span>';
				}
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php endif; ?>
	<?php
}
endif;


/* ----------------------------------------------------------------------
	POST NAVIGATION WITH CUSTOM ORDER
	Display navigation to next/previous post with custom order for special CP.
/* ---------------------------------------------------------------------- */
if ( ! function_exists( 'epron_custom_post_nav' ) ) :
function epron_custom_post_nav() {
	global $post;

	$epron_opts = epron_opts();

	if ( isset( $post ) ) {
		$backup = $post;
	}

	$output = '';
	$post_type = get_post_type($post->ID);
	$id = $post->ID;
	$count = 0;
	$prev_id = '';
	$next_id = '';
	$posts = array();
	$next_label = esc_html__( 'Next Post', 'epron' );
	$prev_label = esc_html__( 'Prev Post', 'epron' );


	if ( $post_type === 'epron_gallery' || $post_type === 'wp_releases' || $post_type === 'epron_artists' || $post_type === 'wp_events_manager' || $post_type = 'epron_videos'  ) {

		// Music
		if ( $post_type === 'wp_releases' ) {
			
			$args = array(
				'post_type' => 'wp_releases',
				'showposts'=> '-1'
			);
		
			$args['orderby'] = 'menu_order';
			$args['order'] = 'ASC';

			$next_label = esc_html__( 'Next Release', 'epron' );
			$prev_label = esc_html__( 'Prev Release', 'epron' );
		}

		// Artist
		if ( $post_type === 'epron_artists' ) {
			
			$args = array(
				'post_type' => 'epron_artists',
				'showposts'=> '-1'
			);
		
			$args['orderby'] = 'menu_order';
			$args['order'] = 'ASC';

			$next_label = esc_html__( 'Next Artist', 'epron' );
			$prev_label = esc_html__( 'Prev Artist', 'epron' );
		}

		// Gallery
		if ( $post_type === 'epron_gallery' ) {
			
			$args = array(
				'post_type' => 'epron_gallery',
				'showposts'=> '-1'
			);

			$next_label = esc_html__( 'Next Album', 'epron' );
			$prev_label = esc_html__( 'Prev Album', 'epron' );
		}

		// Videos
		if ( $post_type === 'epron_videos' ) {
			
			$args = array(
				'post_type' => 'epron_videos',
				'showposts'=> '-1'
			);
		
			$args['orderby'] = 'menu_order';
			$args['order'] = 'ASC';

			$next_label = esc_html__( 'Next Video', 'epron' );
			$prev_label = esc_html__( 'Prev Video', 'epron' );
		}

		// Events
		if ( $post_type === 'wp_events_manager' ) {
			if ( is_object_in_term( $post->ID, 'epron_event_type', 'future-events' ) ) {
				$event_type = 'future-events';
			} else {
				$event_type = 'past-events';
			}
			$order = $event_type === 'future-events' ? $order = 'ASC' : $order = 'DSC';
			$args = array(
				'post_type' => 'wp_events_manager',
				'tax_query' => 
					array(
						array(
						'taxonomy' => 'epron_event_type',
						'field' => 'slug',
						'terms' => $event_type
						)
					),
				'showposts'=> '-1',
				'orderby' => 'meta_value',
				'meta_key' => '_event_date_start',
				'order' => $order
			);

			$next_label = esc_html__( 'Next Event', 'epron' );
			$prev_label = esc_html__( 'Prev Event', 'epron' );
		}

		// Nav loop
		$nav_query = new WP_Query();
		$nav_query->query( $args );
		if ( $nav_query->have_posts() )	{
			while ( $nav_query->have_posts() ) {
				$nav_query->the_post();
				$posts[] = get_the_id();
				if ( $count === 1 ) break;
				if ( $id === get_the_id() ) $count++;
			}
			$current = array_search( $id, $posts );

			$next_post_thumb = '';
			$prev_post_thumb = '';

			// Check IDs
			if ( isset( $posts[$current+1] ) ) {
				$next_id = $posts[$current+1];
				if ( has_post_thumbnail( $next_id ) ) {
					$next_post_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next_id ), 'large' );
				 	if ( $epron_opts->get_option( 'lazyload' ) === 'on' ) {
				 		$next_post_thumb = '<span class="post-nav-preview lazy" data-src="' . esc_attr( $next_post_thumb[0] ) . '"></span>';
				 	} else {
				 		$next_post_thumb = '<span class="post-nav-preview" style="background-image:url(' . esc_attr( $next_post_thumb[0] ) . ')"></span>';
				 	}
				}
			}
			if ( isset( $posts[$current-1] ) ) {
				$prev_id = $posts[$current-1];
				if ( has_post_thumbnail( $prev_id ) ) {
				 	$prev_post_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $prev_id ), 'large' );
				 	if ( $epron_opts->get_option( 'lazyload' ) === 'on' ) {
				 		$prev_post_thumb = '<span class="post-nav-preview lazy" data-src="' . esc_attr( $prev_post_thumb[0] ) . '"></span>';
				 	} else {
				 		$prev_post_thumb = '<span class="post-nav-preview" style="background-image:url(' . esc_attr( $prev_post_thumb[0] ) . ')"></span>';
				 	}
				}

			}

			// Display nav
			$output .= '
			<nav class="navigation post-navigation">
			<div class="nav-links">';
	
				if ( empty( $prev_id ) && $next_id) {
					$output .= '<span class="post-nav-inner link-empty"></span>';
					$output .= '<span class="post-nav-inner link-full"><a href="' . esc_url( get_permalink( $next_id ) ) . '" class="next-link">' . $next_post_thumb . '<span class="nav-desc"><span class="nav-direction">' .  $epron_opts->esc( $next_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $next_id ) ) . '</span></span></a></span>';
				} else if ( $prev_id && empty( $next_id) ) {
					 $output .= '<span class="post-nav-inner link-full"><a href="' . esc_url( get_permalink( $prev_id ) ) . '" class="prev-link"><span class="nav-desc"><span class="nav-direction">' . esc_html( $prev_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $prev_id ) ) . '</span></span>' .  $epron_opts->esc( $prev_post_thumb ) . '</a></span>';
					$output .= '<span class="post-nav-inner link-empty"></span>';
				} else if ( $prev_id && $next_id ) {
					 $output .= '<span class="post-nav-inner"><a href="' . esc_url( get_permalink( $prev_id ) ) . '" class="prev-link"><span class="nav-desc"><span class="nav-direction">' . esc_html( $prev_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $prev_id ) ) . '</span></span>' .  $epron_opts->esc( $prev_post_thumb ) . '</a></span>';
					 $output .= '<span class="post-nav-inner"><a href="' . esc_url( get_permalink( $next_id ) ) . '" class="next-link">' .  $epron_opts->esc( $next_post_thumb ) . '<span class="nav-desc"><span class="nav-direction">' . esc_html( $next_label ) . '</span><span class="nav-title">' . esc_html( get_the_title( $next_id ) ) . '</span></span></a></span>';
				}
		
			$output .= '</div></nav>';
		}

		if ( isset( $post ) ) {
			$post = $backup;
		}
		
		return $output;
	} else {
		return false;
	}
}
endif;