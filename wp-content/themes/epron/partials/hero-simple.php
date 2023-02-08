<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            hero-simple.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();

// Extra classes
$extra_classes = array();

if ( get_theme_mod( 'show_hero_line', true ) === true ) {
    $extra_classes[] = 'has-line';
}

// Post type
$post_type = get_post_type( get_the_ID() );

?>

<!-- Simple Hero -->
<div class="hero simple-hero <?php echo esc_attr( implode(' ', $extra_classes ) ) ?>">
    <?php
    // Get Post Title 
    get_template_part( 'partials/single', 'title' );
    ?>
</div>