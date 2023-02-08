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

// Kingcomposer wrapper class for each element 
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Add custom classes to element 
$wrap_class[] = 'kc-event-countdown-block';
$wrap_class[] = $atts['type'];

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

$post_id = null;
if ( $atts['is_custom_id'] === 'yes' ) {
	$post_id = (int)$atts['custom_id'];
} else {

	$tax = array(
        array(
           'taxonomy' => 'wp_event_type',
           'field' => 'slug',
           'terms' => 'future-events'
          )
    );

	$args = array(
        'post_type'        => 'wp_events_manager',
        'showposts'        => 1,
        'tax_query'        => $tax,
        'orderby'          => 'meta_value',
        'meta_key'         => '_event_date_start',
        'order'            => 'ASC',
        'suppress_filters' => 0 // WPML FIX
    );
    $events = get_posts( $args );
    $events_count = count( $events );

    if ( $events_count !== 0 ) {
        $post_id = $events[0]->ID;
    }
}

// Display Only future event 
if ( isset( $post_id ) && has_term( 'future-events', 'wp_event_type', $post_id ) ) :

	$event_date = get_post_meta( $post_id, '_event_date_start', true );
	$event_time = get_post_meta( $post_id, '_event_time_start', true );
	$location   = get_post_meta( $post_id, '_event_location', true );
	$event_date = strtotime( $event_date );
	$event_time = strtotime( $event_time );
		
	?>
	<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $atts['classes'] ); ?>">

		<div class ="kc-event-countdown-inner">


			<?php if ( $atts['type'] === 'big' || $atts['type'] === 'small' ) : ?>
				<div class="kc-event-countdown-header">
					<h4><?php echo get_the_title( $post_id ); ?></h4>
					<div><span><?php $toolkit->e_esc( $location ); ?></span></div>
				</div>
		   		<div class="kc-countdown kc-event-countdown" data-event-date="<?php echo date_i18n( 'Y/m/d', $event_date ) . ' ' . date_i18n( 'H:i', $event_time );?>:00">
		   			<div class="unit-block"><span><?php esc_html_e( 'Days', 'epron-toolkit' ) ?></span><div class="days unit">000</div></div>
					<div class="unit-block"><span><?php esc_html_e( 'Hours', 'epron-toolkit' ) ?></span><div class="hours unit" >000</div></div>
					<div class="unit-block"><span><?php esc_html_e( 'Minutes', 'epron-toolkit' ) ?></span><div class="minutes unit">00</div></div>
					<div class="unit-block"><span><?php esc_html_e( 'Seconds', 'epron-toolkit' ) ?></span><div class="seconds unit">00</div></div>
		   		</div>
		   	<?php endif; ?>


		   	<?php if ( $atts['type'] === 'small-image' ) : ?>
		   		<?php 

		   	
		   		if ( $atts['custom_image_id'] !== '' ) {
		   			$image_full = wp_get_attachment_image_src( $atts['custom_image_id'], 'full' );
					$image = $toolkit::imageResize( $image_full[0], 660, 330, true, 'c', false );
	                echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'View Post Thumb', 'epron-toolkit' ) . '">';

		   		} else if ( has_post_thumbnail( $post_id ) ) {
	                $image_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
                  	$image = $toolkit::imageResize( $image_url, 660, 330, true, 'c', false );
	                echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'View Post Thumb', 'epron-toolkit' ) . '">';
	            } else {
	                echo '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=" alt="' . esc_attr__( 'View Post Thumb', 'epron-toolkit' ) . '">';
	            }
		   		?>
		   		<div class="kc-countdown kc-image-countdown" data-event-date="<?php echo date_i18n( 'Y/m/d', $event_date ) . ' ' . date_i18n( 'H:i', $event_time );?>:00">
		   			<div class="unit-block"><div class="days unit">000</div><span><?php esc_html_e( 'Days', 'epron-toolkit' ) ?></span></div>
					<div class="unit-block"><div class="hours unit" >000</div><span><?php esc_html_e( 'Hours', 'epron-toolkit' ) ?></span></div>
					<div class="unit-block"><div class="minutes unit">00</div><span><?php esc_html_e( 'Minutes', 'epron-toolkit' ) ?></span></div>
					<div class="unit-block"><div class="seconds unit">00</div><span><?php esc_html_e( 'Seconds', 'epron-toolkit' ) ?></span></div>
					<a href="#" class="plus-button"></a>
		   		</div>

		   	<?php endif; ?>
		</div>
	</div>
	
<?php endif; ?>
<?php kc_js_callback( 'theme_toolkit.countdown' ); ?>
