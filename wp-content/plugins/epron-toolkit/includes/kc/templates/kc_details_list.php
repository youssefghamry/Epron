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

// Get date format  
$date_format = get_option( 'date_format' );

// Kingcomposer wrapper class for each element 
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Add custom classes to element 
$wrap_class[] = 'kc-details-list';

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';


// Get Post ID
$post_id = null;
// Get Query
$query_obj = get_queried_object();
if ( $query_obj ) {
    $post_id = $query_obj->ID;
}

if ( isset( $post_id ) ) {
	 // Event Date 
    $event_date_start = get_post_meta( $post_id, '_event_date_start', true );
    $event_date_end = get_post_meta( $post_id, '_event_date_start', true );
    $event_time_start = get_post_meta( $post_id, '_event_time_start', true );
} else {
	$event_date_start = '2030-01-01 (example)';
	$event_date_end = '2030-01-01 (example)';
	$event_time_start = '21:00 (example)';
}

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ) . ' ' . esc_attr( $atts['classes'] ) ?>">


	<ul class="details-list">
		<?php foreach ($atts['gdetails'] as $key => $item) : ?>
			<li>
				<div class="label-col">
					<?php echo wp_kses( $item->label , array() ); ?>
				</div>
				<div class="val-col">
					<?php 
					//////////
					// TEXT //
					//////////
					if ( $item->value_type === 'text' ) : ?>
						<?php echo wp_kses( $item->value, array() ); ?>
					<?php 
					//////////
					// LINK //
					//////////
					elseif ( $item->value_type === 'link' ) : ?>
						<a 
						href="<?php echo wp_kses( $item->link, array() ); ?>" 
						<?php if ( $item->blank === 'yes' ) : ?>
							target="_blank"
						<?php endif; ?>
						><?php echo wp_kses( $item->value, array() ); ?></a>
					<?php 
					////////////////////
					// Social Buttons //
					////////////////////
					elseif ( $item->value_type === 'social' ) : 

						echo epron_toolkit_social_buttons( $item->social_links, $separator = '', $classes = 'social-icon' );

					///////////////////
					// EVENT DETAILS //
					///////////////////
					elseif ( $item->value_type === 'event_date_start' ) : ?>
						<?php echo wp_kses( $event_date_start, array() ); ?>
					<?php
					elseif ( $item->value_type === 'event_date_end' ) : ?>
						<?php echo wp_kses( $event_date_end, array() ); ?>
					 
					<?php 
					elseif ( $item->value_type === 'event_time_start' ) : ?>
						<?php echo wp_kses( $event_time_start, array() ); ?>
					 
					<?php 
					////////////
					// FILTER //
					////////////
					elseif ( $item->value_type === 'filter' ) : ?>
						<?php
						if ( isset($post_id )) {
							echo '<div class="val-filter">';
							echo get_the_term_list($post_id , $item->filter_name, '', ' &middot; ', ''); 
							echo '</div>';
						} else {
							echo '<a href="#">Preview 1</a> &middot; <a href="#">Preview 2</a> &middot; <a href="#">Preview 3</a>';
						}
						?>
					<?php endif; ?>
				</div>
		
			</li>
		<?php endforeach; ?>
	</ul>
</div>
