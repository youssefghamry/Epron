<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            loop-events-block1.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

// Date Format 
$date_format = get_option( 'date_format' );

// Get Columns Number 
$block_grid = get_query_var( 'block_option' );

// Module classes 
$classes = array(
   	'post-grid-module'
);

// Set color scheme 
$color_scheme = get_theme_mod( 'color_scheme', 'dark' );
$classes[] = $color_scheme . '-scheme-el';

// Module Opts 
$module_opts = array(
	'module'  => 'epron_event_module1',
	'date_divider' => 'yes',
	'classes' => implode( ' ', $classes )
);
?>

<div data-module-opts='<?php echo json_encode( $module_opts ) ?>' class="ajax-grid flex-grid events-list posts-container anim-grid" data-anim-effect="amun">
<?php
// Start Loop
$next_month = '';
$month_count = 0;

while ( have_posts() ) {

	the_post();

	$index = $wp_query->current_post + 1;

	// Module arguments 
	if ( function_exists( $module_opts['module'] ) ) {
		echo '<div class="flex-item">';

	
		//////////////////////
		// Months Separator //
		//////////////////////
		$event_date_start = strtotime(get_post_meta($wp_query->post->ID, '_event_date_start', true));
		$this_month = date_i18n('F', $event_date_start);
		if ( $this_month != $next_month || $month_count == 0 ) {
			echo '<h4 class="events-heading">' . esc_html( $this_month ) . ' <span class="color">' . date( 'Y', $event_date_start) . '</span></h4>';
			$month_count++; 
		} 
		$next_month = $this_month;

	    $epron_opts->e_esc( $module_opts['module']( array(
			'post_id'       => $wp_query->post->ID,
			'lazy'          => true,
			'title'         => get_the_title(),
			'permalink'     => get_permalink(),
			'classes'       => $module_opts['classes'],
			'posts_classes' => implode( ' ', get_post_class( '', $wp_query->post->ID ) ),
	    ) ) );
	    echo '</div>';
	}

}
?>
</div>