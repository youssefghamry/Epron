<?php
/**
 *
 * Contains toolkit functions
 *
 *
 * @package         EpronToolkit
 * @author          Rascals Themes
 * @copyright       Rascals Themes
 * @version       	1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ==================================================
  Share Buttons 
================================================== */
if ( ! function_exists( 'epron_toolkit_share' ) ) :

/**
 * Show share buttons
 * @param  $post_id [Post ID]
 * @return string   [Buttons HTML code]
 */
function epron_toolkit_share( $post_id, $container = false ) {
    $output = '';

    if ( $container === true ) {
        $output .= '<div class="share-buttons"> <span class="share-label">' . esc_html__( 'Share', 'epron-toolkit' ) . '</span>';
    }
    $output .= '
        <a class="share-button fb-share-btn" target="_blank" href="http://www.facebook.com/sharer.php?u=' . esc_url( get_permalink( $post_id ) ) . '"><span class="icon icon-facebook"></span></a>
        <a class="share-button twitter-share-btn" target="_blank" href="http://twitter.com/share?url=' . esc_url( get_permalink( $post_id ) ) . '"><span class="icon icon-twitter"></span></a>
        <a class="share-button linkedin-share-btn" target="_blank" href="https://www.linkedin.com/cws/share?url=' . esc_url( get_permalink( $post_id ) ) . '"><span class="icon icon-linkedin"></span></a>
        ';
    if ( $container === true ) {
        $output .= '</div>';
    }

    return $output;

}
endif;


/* ==================================================
  Social Icons
================================================== */
if ( ! function_exists( 'epron_toolkit_social_buttons' ) ) :

/**
 * Show share buttons
 * @param  $post_id [Post ID]
 * @return string   [Buttons HTML code]
 */
function epron_toolkit_social_buttons( $content = '', $separator = ' ', $classes = '' ) {
    $buttons = preg_replace( '/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n", $content ) ) );
    $buttons = explode( "\n", $buttons );
    $buttons_a = array();
    $html = '';

    if ( is_array( $buttons ) ) {
        foreach ( $buttons as $button ) {
            $button = explode( "|", $button );
            if ( is_array( $button ) ) {
                /* Icon */
                if ( isset( $button[0] ) && isset( $button[1] ) ) {
                    $buttons_a[] = '<a href="' . esc_url( $button[1] ) . '" class="' . esc_attr( $classes ) . '" target="_blank"><span class="icon icon-' . esc_attr( $button[0] ) . '"></span></a>';

                }
            }
        }
    }
    if ( ! empty( $buttons_a ) ) {
        $html = implode( $separator, $buttons_a );
    }

    return $html;

}
endif;


/* ==================================================
  Shortcode - Player button 
================================================== */
function epron_toolkit_player_button( $atts, $content = null ) {
    extract(shortcode_atts(array(
        'link'    => '#',
        'title'   => 'Artist name',
        'target'  => '_self',
        'classes' => ''
    ), $atts));
   return '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">' . wp_kses_post( $title ) . '</a>';
}

add_shortcode( 'player_button', 'epron_toolkit_player_button' );