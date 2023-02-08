<?php
/**
 * Rascals King Composer Extensions
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Kingcomposer wrapper class for each element 
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Plugin Toolkit Class 
$toolkit = epronToolkit();

// Variables 
$wrap_class[] = 'kc-tracklist-wrap';

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

$id           = $atts['id'];
$ids          = $atts['ids'];
$fixed_height = $atts['fixed_height'];
$covers       = $atts['covers'];
$limit        = (int)$atts['limit'];
$limit        = ( $limit === 0 ? -1 : $limit ); 
$color_scheme = $atts['color_scheme'];
$classes      = $atts['classes'];
$color_scheme = $atts['color_scheme'];
$big_cover    = $atts['big_cover'];

$desc         = 'yes';
$buttons      = 'yes';
$autoplay     = 'no';

// Get tracklist
$sc = $toolkit->scamp_player;
$track = $sc->getTracklist( $id );

// Exit if ID doesn't exist
if ( $id === 0 || $track === false ) {
    return false;
}

?>

<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $classes ); ?>">

    <?php

    // Autoplay
    $autoplay = ( $autoplay === 'yes' ) ? 'true' : 'false';

    // Set unique ID
    $toolkit::$id++;
    $sid = $toolkit::$id . uniqid();

    if ( $fixed_height !== '0' ) : ?>
        <div id="tracklist-block-<?php echo esc_attr( $sid ); ?>" class="spl-playlist spl-tracklist spl-has-fixed-height spl-scroll" data-autoplay="<?php echo esc_attr( $autoplay ); ?>" style="height:<?php echo esc_attr( $fixed_height ); ?>px" data-playnext="true">
    <?php else : ?>
        <div class="spl-playlist spl-tracklist" data-autoplay="<?php esc_attr( $autoplay ); ?>" data-playnext="true">
    <?php endif; ?>
    <?php
    foreach ( $track as $i => $track ) :

        $list_classes = [];

        // Big covers
        if ( $big_cover === 'yes' ) {
            $list_classes[] = 'spl-big-cover';
        }

        $i ++;

        // Cover
        if ( ( $track['cover'] || $track['cover'] !== '' ) && $covers === 'yes' ) {
            $list_classes[] = 'spl-has-cover';
        } else {
            $list_classes[] = 'spl-has-number';
        }
        if ( $track['buttons'] !== '' && $buttons === 'yes' ) {
            $list_classes[] = 'spl-has-buttons';
        }
        ?>
        <div class="spl-track spl-single-track spl-simple-track <?php echo esc_attr( implode(' ', $list_classes) ); ?>" data-url="<?php echo esc_url( $track['url'] ) ?>" data-volume="60">
            <div class="spl-row">
                <span class="spl-track-ctrl spl-play-button" data-nr="<?php echo esc_attr( $i ) ?>">
                	<?php if ( $covers === 'yes' ) : ?>
                    <?php if ( $track['cover'] !== false && $track['cover'] !== '' ) : ?>
                        <img src="<?php echo esc_url( $track['cover'] ) ?>" alt="<?php esc_attr_e( 'Track cover', 'epron-toolkit' ) ?>">
                    <?php endif; ?>
                    <?php endif; ?>
                </span>
                <div class="spl-track-header">
                    <h5 class="spl-title spl-play-button"><?php echo esc_html( $track['title'] ) ?></h5>
                    <?php if ( $track['desc'] !== '' && $desc === 'yes') : ?>
                        <h6 class="spl-desc"><?php echo do_shortcode ( $track['desc'] ) ?></h6>
                    <?php endif; ?>
                </div>
                <?php if ( $track['buttons'] !== '' && $buttons === 'yes' ) : ?>
                    <div class="spl-buttons"><?php echo do_shortcode( $track['buttons'] ) ?></div>
                <?php endif; ?>
            </div>

            <div class="spl-player-content spl-row">
                <?php if ( $track['waveform'] ) : ?>
                    <img src="<?php echo esc_url( $track['waveform'] ) ?>" class="spl-waveform">
                <?php endif; ?>
                <span class="spl-elapsed"></span>
                <span class="spl-total"></span>
                <span class="ui spl-progress">
                    <span class="spl-loading"></span>
                    <span class="spl-position"></span>
                </span>
            </div>

        </div>

    <?php 
    // Limit
    if ( $i === $limit ) {
        break;
    }

    ?>

    <?php endforeach;

    kc_js_callback( 'theme_toolkit.scamp_player.scroll' );

    ?>

    </div>
</div>
