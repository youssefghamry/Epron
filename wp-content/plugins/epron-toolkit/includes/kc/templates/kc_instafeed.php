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

// Plugin Toolkit Class 
$toolkit = epronToolkit();

// Kingcomposer wrapper class for each element 
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

// Module classes
if ( isset( $atts['module_classes'] ) ) {
    $atts['module_classes'] .= ' ' . $atts['color_scheme'] . '-scheme-el';
}

// Add custom classes to element 
$wrap_class[] = 'rt-instagram';
$wrap_class[] = $atts['size'];

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $atts['classes'] ); ?>">
    <?php
    // Instafeed data 
    $saved_data = false;
    
    // Instagram id is not set 
    if ( empty( $atts['username'] ) ) {
        return esc_html_e( 'Render failed - no data is received, please check the ID', 'epron-toolkit' );
    }

    // Set cache key 
    $cache_key = 'rt_instafeed_' . $atts['username'];


    // Remove @ from user id 
    $check_char = strpos( $atts['username'], '@' );
    if ( $check_char !== false ) {
        $atts['username'] = substr( $atts['username'], $check_char + 1 );
    }

    // Check cache 
    if ( false === ( $cache_task = get_transient( $cache_key ) ) ) {

        // Return the serialized data present in the page script 
        $remote_images = wp_remote_get( "https://api.instagram.com/v1/users/self/media/recent/?access_token=" . $atts['access_token'] );
        $remote_user = wp_remote_get( "https://api.instagram.com/v1/users/self/?access_token=" . $atts['access_token'] );
        
        if ( is_wp_error( $remote_images ) || is_wp_error( $remote_user ) ) {
             // error handling 
            return esc_html_e( "Something went wrong with Instagram server", 'epron-toolkit' );
        }

        // Media OK
        $instagram_images = json_decode( $remote_images['body'] );
        if ( $remote_images['response']['code'] === 200 ) {
           $saved_data['media'] = $instagram_images->data;
        } elseif ( $remote_images['response']['code'] === 400 ) {
            return '<b>' . $remote_images['response']['message'] . ': </b>' . $instagram_images->meta->error_message;

        }

        // User OK
        $instagram_user = json_decode( $remote_user['body'] );
        if ( $remote_user['response']['code'] === 200 ) {
           $saved_data['user'] = $instagram_user->data;
        } elseif ( $remote_user['response']['code'] === 400 ) {
            return '<b>' . $remote_images['response']['message'] . ': </b>' . $instagram_images->meta->error_message;
        }

        /* Exists save data */
        if ( ! isset( $saved_data ) === null and ! isset( $saved_data['media'] ) ) {
            return esc_html_e( 'Error decoding the instagram json', 'epron-toolkit' );
        }

        // Set cache 
        set_transient( $cache_key, $saved_data, 60 * $atts['cache_time'] );

    }

    // Get feed from cache 
    $cache_task = get_transient( $cache_key );
    if ( $cache_task === false && $saved_data !== false  ) {
        $cache_task = $saved_data;
    } 
    if ( ! isset( $cache_task['user'] ) || ! isset( $cache_task['media'] ) ) {
        delete_transient( $cache_key );
        return esc_html_e( 'Error decoding the instagram json', 'epron-toolkit' );
    }
    $user = $cache_task['user'];
    $media = $cache_task['media'];

     // Set number of images 
    $images_per_row = intval( $atts['images_per_row'] );
    $number_of_rows = intval( $atts['number_of_rows'] );
    $images_nr = $images_per_row * $number_of_rows;

    // Profile image 
    $profile_img = '';
    if ( isset( $user->profile_picture ) ) {
        $profile_img = $user->profile_picture;
    }

    // Followers 
    $followers = 0;
    if ( isset( $user->counts->followed_by) ) {
        $followers = $user->counts->followed_by;
    }
    
    $followers = epron_format_number( $followers );

    ?>

    <?php if ( ! empty( $atts['display_header'] ) ) : ?>
        <div class="rt-instagram-header">
            <div class="rt-instagram-profile-img">
                <img src="<?php echo esc_url( $profile_img ); ?>" alt="<?php echo esc_attr__( 'Instagram profile image', 'epron-toolkit' ); ?>">
            </div>
            <div class="rt-instagram-data">
                <div class="rt-instagram-user">
                    <a target="_blank" href="https://www.instagram.com/<?php echo esc_attr( $atts['username'] ); ?>">
                        <?php if ( ! empty( $atts['display_name'] ) ) : ?>
                            @<?php echo esc_attr( $atts['display_name'] ); ?>
                        <?php else : ?>
                            @<?php echo esc_attr( $atts['username'] ); ?>
                        <?php endif ?>
                    </a>
                </div>
                <div class="rt-instagram-followers">
                    <span><?php echo esc_attr( $followers ); ?></span><?php esc_html_e( 'Followers', 'epron-toolkit' ); ?>
                </div>
                <a class="rt-instagram-button" target="_blank" href="https://www.instagram.com/<?php echo esc_attr( $atts['username'] ); ?>"><?php esc_html_e( 'Follow', 'epron-toolkit' ); ?></a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( isset( $media ) ) : ?>
        <?php $image_count = 0; ?>
        <div class="rt-instagram-images rt-instagram-grid-<?php echo esc_attr( $atts['images_per_row'] ); ?> <?php echo esc_attr( $atts['image_gap'] ); ?>">
        <?php foreach ( $media as $image ) : ?>
            <?php if ( isset( $image->link )) : ?>
                <div class="rt-instagram-image">
                     <div class="instafeed-image-square">
                    <a href="<?php echo esc_url( $image->link ) ?>" target="_blank">
                        <img src="<?php echo esc_url( $image->images->standard_resolution->url ) ?>" alt="<?php echo esc_attr__( 'Instagram image', 'epron-toolkit' ); ?>">
                    </a>
                </div>
                    <div class="rt-instagram-meta">
                        <div>
                            <span class="rt-instagram-comments"><i class="icon icon-comment-o"></i><?php echo epron_format_number($image->comments->count ); ?></span>
                            <span class="rt-instagram-likes"><i class="icon icon-heart-o"></i><?php echo epron_format_number( $image->likes->count ); ?></span>
                        </div>
                    </div>

                </div>
            <?php endif ?>

        <?php 
            $image_count ++;
            if ( $image_count === $images_nr ) {
                break;
            }
        ?>

        <?php endforeach; ?>

        <?php if ( ! empty( $atts['display_follow_overlay'] ) ) : ?>
            <div class="follow-overlay">
                <h6>
                    <a target="_blank" href="https://www.instagram.com/<?php echo esc_attr( $atts['username'] ); ?>">
                        <?php if ( ! empty( $atts['display_name'] ) ) : ?>
                            @<?php echo esc_attr( $atts['display_name'] ); ?>
                        <?php else : ?>
                            @<?php echo esc_attr( $atts['username'] ); ?>
                        <?php endif ?>
                    </a>
                </h6>
            </div>
        <?php endif ?>
        </div>

    <?php endif ?>

</div>