<?php
/**
 * Theme Name: 		Epron
 * Theme Author: 	Mariusz Rek - Rascals Themes
 * Theme URI: 		http://rascalsthemes.com/epron
 * Author URI: 		http://rascalsthemes.com
 * File:			404.php
 * @package epron
 * @since 1.0.0
 */

get_header();

// Get options
$epron_opts = epron_opts();

?>

<div class="content">
    <div class="container text-center">
    	<h6 class="big-text"><span data-parallax='{"y": -60}'>4</span><span data-parallax='{"y": -30}'>0</span><span data-parallax='{"y": 20}'>4</span></h6>
        <h4 data-parallax='{"y": 20}'><?php esc_html_e( 'This is somewhat embarrassing, isn’t it?', 'epron' ); ?></h4>
        <p data-parallax='{"y": 10}'><?php esc_html_e( 'It looks like nothing was found at this location.', 'epron'); ?></p>
	</div><!-- .container -->
</div><!-- .content -->

<?php 

// Get footer
get_footer();