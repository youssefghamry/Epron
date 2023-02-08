<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            disqus.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$epron_opts = epron_opts();

/* Get DISQUS shortname */
$disqus_shortname = $epron_opts->get_option( 'disqus_shortname' );

?>
<!-- DISQUS Comment section -->
<section id="comments" class="comments-section">
    <!-- container -->
    <div class="comments-container clearfix">
		<h3 id="reply-title"><?php echo '<strong>' . esc_html__('Leave', 'epron') .' </strong>' . esc_html__(' a Reply', 'epron'); ?></h3>
		<div id="disqus_title" class="hidden"><?php echo get_the_title( $wp_query->post->ID )  ?></div>
		<div id="disqus_thread" data-post_id="<?php echo esc_attr( $wp_query->post->ID ) ?>" data-disqus_shortname="<?php echo esc_attr( $disqus_shortname ) ?>"></div>
    </div>
</section>