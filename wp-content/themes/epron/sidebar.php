<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            sidebar.php
 * @package epron
 * @since 1.0.0
 */
?>

<?php if ( is_active_sidebar( 'primary-sidebar' )  ) : ?>
	<aside>
		<?php dynamic_sidebar( 'primary-sidebar' ); ?>
	</aside>
<?php endif; ?>