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
$wrap_class[] = 'kc-album-player-wrap';

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

$id           = $atts['id'];
$ids          = $atts['ids'];
$fixed_height = $atts['fixed_height'];
$color_scheme = $atts['color_scheme'];
$classes      = $atts['classes'];
$color_scheme = $atts['color_scheme'];

$desc         = 'yes';
$buttons      = 'yes';
$autoplay     = 'no';
$open         = ( $atts['show_tracklist'] === 'yes' ) ? 'open' : '';

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
    $sid = $toolkit::$id . uniqid(); ?>

    <div id="spl-player-ID-<?php echo esc_attr( $sid ); ?>" class="spl-player <?php echo esc_attr( $open ) ?>">

        <?php
        foreach ( $track as $i => $track ) :

            $list_classes = [];
            $i ++;

            ?>
            <?php 
            // Open tracklist container and display player container
            if ( $i === 1 ) : ?>

                <?php 
                // Player container
                if ( $track['cover'] !== '' ) {
                    $player_classes = 'spl-has-cover';
                } else {
                    $player_classes = 'spl-has-number';  
                }
                ?>

                <div class="spl-player-container spl-track spl-single-track spl-simple-track <?php echo esc_attr( $player_classes ) ?>" data-url="<?php echo esc_url( $track['url'] ) ?>" data-volume="60">
                    <div class="spl-row">
                        <span class="spl-track-ctrl spl-play-button">
                            <?php if (  $track['cover'] !== false && $track['cover'] !== '' ) : ?>
                                <img class="hidden-cover" src="<?php echo esc_url( $track['cover'] ) ?>" alt="<?php esc_attr_e( 'Track cover', 'epron-toolkit' ) ?>">
                            <?php else : ?>
                                <img class="hidden-cover" src="" alt="<?php esc_attr_e( 'Track cover', 'epron-toolkit' ); ?>" style="display:none">
                            <?php endif; ?>

                        </span>
                        <div class="spl-track-header">
                            <h5 class="spl-title"><?php echo esc_html( $track['title'] ) ?></h5>
                            <?php if ( $track['desc'] !== '' && $desc === 'yes') : ?>
                                <h6 class="spl-desc"><?php echo do_shortcode ( $track['desc'] ) ?></h6>
                            <?php endif; ?>
                            <div class="spl-player-content">
                                <span class="spl-elapsed"></span>
                                <span class="spl-total"></span>
                                <span class="spl-prev icon icon-previous2"></span>
                                <span class="spl-next icon icon-next2"></span>
                                <span class="ui spl-progress">
                                    <span class="spl-loading"></span>
                                    <span class="spl-position"></span>
                                </span>
                            </div>
                            <span class="show-list-btn icon icon-menu2"></span>
                        </div>
                    </div>
                </div>
       
                <?php 
                // Tracklist container
                if ( $fixed_height !== '0' ) : ?>
                    <div id="tracklist-block-<?php echo esc_attr( $sid ); ?>" class="spl-playlist spl-tracklist spl-player-list spl-has-fixed-height spl-scroll" data-autoplay="<?php echo esc_attr( $autoplay ); ?>" style="height:<?php echo esc_attr( $fixed_height ); ?>px" data-playnext="true">
                <?php else : ?>
                    <div class="spl-playlist spl-tracklist spl-player-list" data-autoplay="<?php esc_attr( $autoplay ); ?>" data-playnext="true">
                <?php endif; ?>

            <?php endif; ?>


            <?php 

            // Cover
            if ( $track['cover'] || $track['cover'] !== '' ) {
                $list_classes[] = 'spl-has-number';
            }

            if ( $track['buttons'] !== '' && $buttons === 'yes' ) {
                $list_classes[] = 'spl-has-buttons';
            }

             ?>

            <div class="spl-single-track spl-simple-track <?php echo esc_attr( implode(' ', $list_classes) ); ?>" data-url="<?php echo esc_url( $track['url'] ) ?>" data-volume="60">
                <div class="spl-row">
                    <span class="spl-track-ctrl" data-nr="<?php echo esc_attr( $i ) ?>">

                        <?php if ( $track['cover'] || $track['cover'] !== '' ) : ?>
                            <img class="hidden-cover" src="<?php echo esc_url( $track['cover'] ) ?>" alt="<?php esc_attr_e( 'Track cover', 'epron-toolkit' ) ?>">
                        <?php endif; ?>

                    </span>
                    <div class="spl-track-header">
                        <h5 class="spl-title"><?php echo esc_html( $track['title'] ) ?></h5>
                        <?php if ( $track['desc'] !== '' && $desc === 'yes') : ?>
                            <h6 class="spl-desc"><?php echo do_shortcode ( $track['desc'] ) ?></h6>
                        <?php endif; ?>
                    </div>
                    <?php if ( $track['buttons'] !== '' && $buttons === 'yes' ) : ?>
                        <div class="spl-buttons"><?php echo do_shortcode( $track['buttons'] ) ?></div>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>

        </div>
    </div>
</div>