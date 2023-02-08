<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            sidebar-footer-col2.php
 * @package epron
 * @since 1.0.0
 */

// Get panel options
$epron_opts = epron_opts();
?>

<?php if ( is_active_sidebar( 'footer-col2-sidebar' )  ) : ?>
	<?php dynamic_sidebar( 'footer-col2-sidebar' ); ?>
<?php endif; ?>