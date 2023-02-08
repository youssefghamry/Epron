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
$wrap_class[] = 'kc-adspot';

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $atts['classes'] ); ?>">
    <?php if ( ! empty( $atts['adspot'] ) ) {
    	if ( function_exists( 'epron_show_ad' ) ) {
        	echo epron_show_ad( $atts['adspot'], $atts['display_ad_title'], 'yes' );
    	}
    } ?>
</div>