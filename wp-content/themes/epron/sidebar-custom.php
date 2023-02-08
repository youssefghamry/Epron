<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            sidebar-custom.php
 * @package epron
 * @since 1.0.0
 */

// Get options
$epron_opts = epron_opts();

// Get custom sidebar
$custom_sidebar = get_post_meta( $wp_query->post->ID, '_custom_sidebar', true );
?>
	
<aside>
    <?php if ( $custom_sidebar === '' || $custom_sidebar === '_default' ) : ?>
        <?php if ( is_active_sidebar( 'primary-sidebar' )  ) : ?>
            <?php dynamic_sidebar( 'primary-sidebar' ); ?>
        <?php endif; ?>
    <?php else : ?>
        <?php if ( is_active_sidebar( $custom_sidebar )  ) : ?>
            <?php dynamic_sidebar( $custom_sidebar ) ?> 
        <?php endif; ?>
    <?php endif; ?>
</aside>