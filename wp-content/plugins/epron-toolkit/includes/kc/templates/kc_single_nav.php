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

global $wp_query;

// Kingcomposer wrapper class for each element 
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

// Add custom classes to element 
$wrap_class[] = 'kc-single-nav';

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ) . ' ' . esc_attr( $atts['classes'] ) ?>">
	<div class="single-nav">

		<?php if ( isset( $wp_query ) && isset( $wp_query->post->ID ) ) : ?>
			<?php 

			$post_type = get_post_type($wp_query->post->ID);
			$id        = $wp_query->post->ID;
			$count     = 0;
			$prev_id   = '';
			$next_id   = '';
			$posts     = array();

			if ( $post_type === 'wp_artists' || $post_type === 'wp_events_manager' || $post_type === 'wp_releases' ) : 

				// Artists
				if ($post_type === 'wp_artists' ) {
					
					$args = array(
						'post_type' => 'wp_artists',
						'showposts' => '-1'
					);
				}
				// Artists
				if ( $post_type === 'wp_releases' ) {
					
					$args = array(
						'post_type' => 'wp_releases',
						'showposts' => '-1'
					);
					
					$args['orderby'] = 'menu_order';
					$args['order']   = 'ASC';
				}
				// Events manager
				if ( $post_type === 'wp_events_manager' ) {
					if ( is_object_in_term( $id, 'wp_event_type', 'Future events' ) ) {
						$event_type = 'Future events';
					} else {
						$event_type = 'Past events';
					}
					$order = $event_type === 'Future events' ? $order = 'ASC' : $order = 'DSC';
					$args = array(
						'post_type' => 'wp_events_manager',
						'tax_query' => 
							array(
								array(
									'taxonomy' => 'wp_event_type',
									'field'    => 'slug',
									'terms'    => $event_type
								)
							),
						'showposts' => '-1',
						'orderby'   => 'meta_value',
						'meta_key'  => '_event_date_start',
						'order'     => $order
					);
				}

				// Nav loop
				$nav_query = new WP_Query();
				$nav_query->query( $args );
				if ( $nav_query->have_posts() ) {
					while ( $nav_query->have_posts() ) {
						$nav_query->the_post();
						$posts[] = get_the_id();
						if ( $count === 1 ) {
							break;
						}
						if ( $id === get_the_id() ) {
							$count++;
						} 
					}
					$current = array_search( $id, $posts );

					// Check IDs
					if ( isset( $posts[$current-1] ) ) {
						$prev_id = $posts[$current-1];
					}
					if ( isset( $posts[$current+1] ) ) {
						$next_id = $posts[$current+1];
					}
				}

				if ( $prev_id ) {
					echo '<a href="' . esc_url( get_permalink( $prev_id )  ) . '" class="nav-prev" title="' . esc_attr( get_the_title( $prev_id ) ). '"></a>';
				}
				else {
					echo '<span class="nav-prev"></span>';
				}
				if ($next_id) { 
					echo '<a href="' . esc_url( get_permalink( $next_id ) ) . '" class="nav-next" title="' . esc_attr( get_the_title( $next_id ) ) . '"></a>';
				}
				else {
					echo '<span class="nav-next"></span>';
				}

			?>

			<?php wp_reset_postdata(); ?>

			<?php else : ?>
				<?php esc_html_e( 'You can not use [nav] in this page.', 'epron-toolkit' ) ?>
			<?php endif; // post_type ?>

		<?php else : // end isset wp query ?>	
				<span class="nav-prev"></span>
				<a href="#" class="nav-next" title="<?php esc_attr_e( 'Demo', 'epron-toolkit') ?>"></a>
		<?php endif; // end isset wp query ?>
	</div>

</div>
