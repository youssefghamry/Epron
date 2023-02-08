<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            header.php
 * @package epron
 * @since 1.0.0
 */

// Get options
$epron_opts = epron_opts();

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<?php 
    $page_id = '0';

    if ( isset( $wp_query ) && isset( $post ) ) {
        $page_id = $wp_query->post->ID;
    }
?>

<body <?php body_class() ?> data-page_id="<?php echo esc_attr( $page_id ) ?>" data-wp_title="<?php esc_attr( wp_title( '|', true, 'right' ) ) ?>">

<?php if ( ! is_customize_preview()  ) : ?>
    <?php if ( get_theme_mod( 'loader', '0' ) === true ) {
        echo epron_loader();
    } ?>
<?php endif; ?>

<?php 
    // Get Custom Header
    get_template_part( 'partials/' . esc_html( get_theme_mod( 'header_style', 'header-style1' ) ) );
?>



<div id="slidebar">
    <div class="slidebar-block">
        <div class="slidebar-content">
            <div class="slidebar-close-block">
                <a href="#" id="slidebar-close"></a>
            </div>
            <div class="slidebar-logo-block">
                <div id="slidebar-logo">
                    <?php
                        
                        $logo_path = get_template_directory_uri() . '/images';

                        $default_logo_img =  $logo_path . '/logo-light.svg';
                      
                        echo '<a href="' . esc_url( home_url('/') ) . '">';
                        if ( get_theme_mod( 'logo_mobile' ) ) {
                            echo '<img src="' . esc_url( get_theme_mod( 'logo_mobile' ) ) . '" class="theme-logo-img" alt="' . esc_attr__( 'Logo Image', 'epron' ) . '">';
                        } else {
                            echo '<img src="' . esc_url( $default_logo_img ) . '" alt="' . esc_attr__( 'Logo Image', 'epron' ) . '">';
                        }
                        echo '</a>';
                     ?>
                 </div>
            </div>
            <?php
            // Defaults values for top navigation
            $main_nav = array(
                'theme_location'  => 'main',
                'menu'            => '',
                'container'       => false,
                'container_class' => '',
                'menu_class'      => 'menu',
                'fallback_cb'     => 'wp_page_menu',
                'depth'           => 3
            ); 

            $top_nav = array(
                'theme_location'  => 'top_menu',
                'menu'            => '',
                'container'       => false,
                'container_class' => '',
                'menu_class'      => 'top_nav',
                'fallback_cb'     => 'wp_page_menu',
                'depth'           => 2
            ); 
            ?>
            
            <nav id="nav-sidebar">
                <?php wp_nav_menu( $main_nav ); ?>
                <?php if ( get_theme_mod( 'show_top_header', '0' ) ) : ?>
                    <?php wp_nav_menu( $top_nav ); ?>
                <?php endif; ?>
            </nav>

        </div>
    </div>
     
</div> <!-- #slidebar -->


<div class="site">
    <?php
            
    // Background 
    $bg = '';
    if ( isset( $wp_query->post->ID ) ) {
        if ( is_page() || is_single() || ( class_exists( 'WooCommerce' ) && is_shop() ) ) {
           
            $content_bg = get_post_meta( $wp_query->post->ID, '_content_bg', true );
            if ( class_exists( 'WooCommerce' ) ) {
                 // Shop id
                $shop_id = get_option( 'woocommerce_shop_page_id' );
                if ( is_shop() ) {

                    $content_bg = get_post_meta( $shop_id, '_content_bg', true );
                }
            }
            if ( function_exists( 'epron_get_background' ) && epron_get_background( $content_bg ) ) {
                $bg = epron_get_background( $content_bg );
            } 
        }
    }
    ?>
    <div class="site-container" style="<?php echo esc_attr( $bg ); ?>">