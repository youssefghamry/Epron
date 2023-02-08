<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            footer.php
 * @package epron
 * @since 1.0.0
 */

// Get options
$epron_opts = epron_opts();
?>

    </div><!-- .site-container -->

    <?php 

    // King Composer Footer Section 
    if ( function_exists('kc_add_map') && isset( $wp_query->post->ID ) && is_singular() ) : ?>
        <?php 
        $kc_Section = get_post_meta( $wp_query->post->ID, '_kc_section', true );
        if ( isset( $kc_Section ) && $kc_Section !== 'none' ) : ?> 
            <div class="container-full kc-footer-section">
                <?php echo kc_do_shortcode( get_post_field( 'post_content_filtered', $kc_Section ) ); ?>
            </div>
        <?php endif ?>        
    <?php endif; ?>

    <!-- Footer container -->
    <footer class="footer">

        <?php if ( get_theme_mod( 'show_footer_top', false ) ) : ?>
        <!-- Footer Top -->
        <div class="footer-top">

            <div class="container">
                <!-- Grid 2 cols -->
                <div class="grid-row grid-row-pad grid-no-pb">
                    <div class="<?php echo esc_attr( get_theme_mod( 'footer_left_col_classes', 'grid-6 grid-tablet-6 grid-mobile-12' ) ) ?>">
                        <?php if ( get_theme_mod( 'footer_address' ) || get_theme_mod( 'footer_phone' ) || get_theme_mod( 'footer_email' ) ) : ?>
                        <ul class="contact">
                            <?php if ( get_theme_mod( 'footer_address' ) ) : ?>
                                <li class="address"><span class="icon icon-location"></span> <?php echo wp_kses_post( get_theme_mod( 'footer_address' ) ) ?></li>
                            <?php endif ?>
                            <?php if ( get_theme_mod( 'footer_phone' ) ) : ?>
                                <li class="phone"><span class="icon icon-phone2"></span> <?php echo wp_kses_post( get_theme_mod( 'footer_phone' ) ) ?></li>
                            <?php endif ?>
                            <?php if ( get_theme_mod( 'footer_email' ) ) : ?>
                                <li class="email"><span class="icon icon-grid-32 icon-mail-envelope-closed"></span> <a href="mailto:<?php echo esc_attr( get_theme_mod( 'footer_email' ) ) ?>"><?php echo esc_html( get_theme_mod( 'footer_email' ) ) ?></a></li>
                            <?php endif ?>       
                        </ul>
                        <?php endif ?>
                    </div>

                    <div class="<?php echo esc_attr( get_theme_mod( 'footer_right_col_classes', 'grid-6 grid-tablet-6 grid-mobile-12' ) ) ?>">
                        <div class="footer-social social-icons">
                            <?php 
                            
                            if ( get_theme_mod( 'footer_social_buttons' ) ) {
                                $socials = get_theme_mod( 'footer_social_buttons' );

                                if ( is_array( $socials) ) {
                                    foreach  ($socials as $key => $social ) {
                                        echo '<a href="' . esc_html( $social['social_link'] ) . '" class=""><i class="icon icon-' . esc_attr( $social['social_type'] ) . '"></i><i class="icon icon-' . esc_attr( $social['social_type'] ) . '"></i></a>';
                                    }
                                }

                            }
                                    
                            ?>
                        </div>
                    </div>
                </div>
            </div> <!-- .container -->
        </div> <!-- .footer-top -->
        <?php endif; ?>

        <?php if ( get_theme_mod( 'show_footer_widgets', false ) ) : ?>
        <!-- Footer widgets -->
        <div class="footer-widgets">
            <div class="container">
                <!-- Grid 3 cols -->
                <div class="grid-row grid-row-pad grid-no-pb">
                    <div class="grid-4 grid-tablet-4 grid-mobile-12">
                        <?php get_sidebar( 'footer-col1' ); ?>
                    </div>
                    <div class="grid-4 grid-tablet-4 grid-mobile-12">
                        <?php get_sidebar( 'footer-col2' ); ?>
                    </div>
                    <div class="grid-4 grid-tablet-4 grid-mobile-12">
                        <?php get_sidebar( 'footer-col3' ); ?>
                    </div>

                </div>
                
            </div>
        </div> <!-- .footer-widgets -->
    <?php endif; ?>

        <div class="footer-bottom">
            
            <div class="container">
                <!-- Grid 2 cols -->
                <div class="grid-row grid-row-pad grid-no-pb">

                    <div class="grid-6 grid-tablet-6 grid-mobile-12">

                        <!-- footer-nav -->
                        <?php
                        if ( has_nav_menu( 'footer_menu' ) ) { 
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'footer_menu', 
                                    'container'      => 'nav', 
                                    'container_id'   => 'footer-nav', 
                                    'menu_class'     => 'clearfix', 
                                    'depth'          => 1
                                )
                            );

                        } 
                        ?>
                        <!-- /footer-nav -->
                        <div class="footer-note">
                            <?php 
                            echo wp_kses_post( get_theme_mod( 'copyright_note', '&copy; Copyright 2018 Epron. Powered by <a href="#" target="_blank">Rascals Themes</a>. Handcrafted in Europe.' ) );  
                            ?>
                        </div> <!-- .footer-note -->
                    </div>

                    <!-- Grid 2 cols -->
                    <div class="grid-6 grid-tablet-6 grid-mobile-12">
                        <?php
                        if ( class_exists( 'RascalsTwitter' ) && get_theme_mod( 'show_footer_twitter', false ) ) {
                           
                            $limit = absint( get_theme_mod( 'footer_twitter_limit', '1' ) );
                            $twitter_args = array(
                                'username'   => get_theme_mod( 'footer_twitter_username' ),
                                'replies'    => get_theme_mod( 'footer_twitter_replies' ),
                                'api_key'    => get_theme_mod( 'footer_twitter_api_key' ),
                                'api_secret' => get_theme_mod( 'footer_twitter_api_secret' ),
                            );

                            $tweets = new RascalsTwitter( $twitter_args );
                            $tweets_a = $tweets->showTweets();
                            if ( is_array( $tweets_a ) ) {
                                echo '<div class="footer-twitter">';
                                echo '<ul class="footer-tweets">';
                                foreach ( $tweets_a as $key => $tweet ) {
                                    echo '<li>' . wp_kses_post( $tweet['text'] ) . '<span class="date">' . wp_kses_post( $tweet['date'] ) . '</span></li>';  
                                    if ( $key === $limit ) {
                                        break;
                                    }
                                }
                                echo '</ul>';
                                echo '<a href="http://twitter.com/' . esc_attr( $twitter_args['username'] ) . '" class="twitter-button"><i class="icon icon-twitter"></i><i class="icon icon-twitter"></i></a>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div><!-- .footer-bottom -->

    </footer><!-- .footer -->
</div><!-- .site -->
<?php wp_footer(); ?>
</body>
</html>