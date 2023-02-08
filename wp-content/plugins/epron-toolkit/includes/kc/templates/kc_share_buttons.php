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

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

// Add custom classes to element 
$wrap_class[] = 'kc-share-buttons';

$queried_object = get_queried_object();

$post_id = null;
if ( $queried_object ) {
    $post_id = $queried_object->ID;
}

$toolkit = epronToolkit();

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $atts['classes'] ); ?>">
	<div class="kc-share-buttons-inner">
		<?php echo epron_toolkit_share( $post_id, true ); ?>
	</div>
</div>