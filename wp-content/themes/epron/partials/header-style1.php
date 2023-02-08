<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            header-style1.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get options
$epron_opts = epron_opts();
    
// Defaults values for social icons
$header_social_defaults = array(
    array(
        'social_type' => 'facebook',
        'social_link'  => '#',
    ),
    array(
        'social_type' => 'twitter',
        'social_link'  => '#',
    ),
    array(
        'social_type' => 'soundcloud',
        'social_link'  => '#',
    ),
    array(
        'social_type' => 'mixcloud',
        'social_link'  => '#',
    ),
    array(
        'social_type' => 'spotify',
        'social_link'  => '#',
    )
);


// Defaults values for main navigation
$defaults_main = array(
    'theme_location'  => 'main',
    'menu'            => '',
    'container'       => false,
    'container_class' => '',
    'menu_class'      => 'menu',
    'fallback_cb'     => 'wp_page_menu',
    'depth'           => 3
); 

if ( class_exists( 'EpronToolkitSuperMenu' ) ) {
    $defaults_main['walker'] = new EpronToolkitSuperMenu();
}
?>


<?php if ( get_theme_mod( 'show_top_header', false ) ) : ?>
<!-- top menu -->
<div id="top-header" class="top-header">
    <div class="container">
        <nav class="nav-top-flex nav-horizontal" role="navigation">
            <?php if ( has_nav_menu( 'top_menu' ) ) { ?>
                <?php wp_nav_menu( array( 
                    'theme_location'  => 'top_menu',
                    'menu'            => '',
                    'container'       => false,
                    'container_class' => '',
                    'menu_id'         => 'top-nav', 
                    'menu_class'      => 'top-nav', 
                    'fallback_cb'     => 'wp_page_menu',
                    'depth'           => 2
                ) ); ?>
            <?php } ?>
            <?php
                echo '<ul class="top-nav top-right-nav">';
                if ( class_exists( 'woocommerce' ) && get_theme_mod( 'show_top_header_cart', '1' ) ) {
                    echo '<li class="cart cart-wrap">';
                    epron_cart_details();
                    echo '</li>';
                    echo '<li class="checkout"><a href="' . esc_url( wc_get_checkout_url() ) . '"><i class="icon icon-checkmark"></i>' . esc_html__( 'Checkout','epron' ) . '</a></li>';
                }

                if ( get_theme_mod( 'show_top_header_search', '1' ) ) {
                    echo '<li class="search">';
                      get_search_form();
                    echo '</li>';
                }
              
                echo '</ul>';

            ?>
        </nav>
    </div>
</div>
<!-- /top menu -->
<?php endif; ?>


<div id="header-wrap">
    <div id="header" class="header header-transparent">

        <!-- Nav Block --> 
        <div class="nav-block">
            <div class="nav-container container">

                <!-- Logo -->
                <div id="site-logo" class="header-logo">
                    <?php

                    $logo_path = get_template_directory_uri() . '/images';
                    
                    if ( get_theme_mod( 'color_scheme', 'dark' ) === 'dark' ) {
                        $default_logo_img =  $logo_path . '/logo-light.svg';
                        $default_logo_hero = $logo_path . '/logo-light.svg';
                    } else {
                        $default_logo_img =  $logo_path . '/logo-dark.svg';
                        $default_logo_hero = $logo_path . '/logo-light.svg';
                    }

                    if ( get_theme_mod( 'logo_hero' ) ) {
                        $has_hero_logo = 'has-overlay-logo';
                    } else {
                        $has_hero_logo = '';
                    }

                    echo '<a href="' . esc_url( home_url('/') ) . '" class="theme-logo ' . esc_attr( $has_hero_logo ) . '">';
                    if ( get_theme_mod( 'logo' ) ) {
                        echo '<img src="' . esc_url( get_theme_mod( 'logo' ) ) . '" class="theme-logo-img" alt="' . esc_attr__( 'Logo Image', 'epron' ) . '">';
                    } else {
                        echo '<img src="' . esc_url( $default_logo_img ) . '" class="theme-logo-img" alt="' . esc_attr__( 'Logo Image', 'epron' ) . '">';
                    }
                    if ( get_theme_mod( 'logo_hero' ) ) {
                        echo '<img src="' . esc_url( get_theme_mod( 'logo_hero' ) ) . '" class="theme-logo-overlay" alt="' . esc_attr__( 'Logo Image', 'epron' ) . '">';
                    }

                    echo '</a>';
                    ?>
                </div>

                <div class="responsive-trigger-wrap">
                    <a href="#" class="responsive-trigger">
                        <span class="icon"></span>
                    </a>
                </div>
                
                <!-- Main nav -->
                <?php if ( has_nav_menu( 'main' ) ) : ?>
                    <nav id="nav-main" class="nav-horizontal">
                        <?php wp_nav_menu( $defaults_main ); ?>
                    </nav>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>