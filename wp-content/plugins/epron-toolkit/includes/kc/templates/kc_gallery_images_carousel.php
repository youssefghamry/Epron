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
$wrp_el_classes = apply_filters( 'kc-el-class', $atts );

// Variables 
$showposts = 3;
$thumb_size = $atts['thumb_size'];
$output = '';
$date_format = get_option( 'date_format' );

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrp_el_classes[] =  $atts['color_scheme'] . '-scheme-el';

// Album ID 
$album_id = $atts['album_id'];

// Images 
$images_ids = get_post_meta( $album_id, '_gallery_images', true ); 

if ( $atts['album_id'] !== 'none' && $images_ids && $images_ids !== '' ) :

    $ids = explode( '|', $images_ids );

    $gallery_loop_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post__in'       => $ids,
        'orderby'        => 'post__in',
        'post_status'    => 'any'
    );
    if ( $atts['limit'] !== 0 ) {
        $gallery_loop_args['showposts'] = $atts['limit'];
    }

    $the_query = new WP_Query( $gallery_loop_args );

    $element_attribute = array();
    $el_classess = array(
        'kc-owl-post-carousel',
        'kc-posts-carousel',
        'kc-gallery-images-carousel',
        'owl-carousel',
        'list-post',
        $atts['classes']
    );
    $module_classess = array();
    if( isset($atts['nav_style']) && $atts['nav_style'] !='' ){
        $el_classess[] = 'owl-nav-' . $atts['nav_style'];
        $el_classess[] = 'gallery-images-grid';
    }

    $owl_option = array(
        'items'         => $atts['items_number'],
        'mobile'        => $atts['mobile'],
        'tablet'        => $atts['tablet'],
        'speed'         => intval( $atts['speed'] ),
        'navigation'    => $atts['navigation'],
        'pagination'    => $atts['pagination'],
        'autoheight'    => $atts['auto_height'],
        'autoplay'      => $atts['auto_play']
    );

    $owl_option = strtolower( json_encode( $owl_option ) );

    $element_attribute[] = 'class="'. esc_attr( implode(' ', $el_classess) ) .' ' . esc_attr( $atts['classes'] ) . '"';
    $element_attribute[] = "data-owl-options='$owl_option'";

    ob_start();

    if ( $the_query->have_posts() ) :

        echo '<div '. implode(' ', $element_attribute) .'>';

        while ( $the_query->have_posts() ) :

            $the_query->the_post();
            ?>
            <div class="item list-item">

                <?php 
                if ( empty( $thumb_size ) ) {
                    // Thumb Size 
                    $thumb_size = 'epron-large-square-thumb';
                }

                $image_att = wp_get_attachment_image_src( get_the_id(), $thumb_size );
                if ( ! $image_att[0] ) { 
                    continue;
                }

                // Get image meta 
                $image = get_post_meta( $album_id, '_gallery_images_' . get_the_id(), true );

                // Add default values 
                $defaults = array(
                    'title' => '',
                    'custom_link'  => '',
                    'thumb_icon' => 'view'
                 );

                if ( isset( $image ) && is_array( $image ) ) {
                    $image = array_merge( $defaults, $image );
                } else {
                    $image = $defaults;
                }

                // Add image src to array 
                $image['src'] = $image_att[0];
                if ( $image[ 'custom_link' ] !== '' ) {
                    $link = $image[ 'custom_link' ];
                } else {
                    $link = wp_get_attachment_image_src( get_the_id(), 'full' );
                    $link = $link[0];
                }

                ?>
                <article <?php post_class( 'article' ); ?>>
                    <a href="<?php echo esc_attr( $link ) ?>" class="<?php if ( $image[ 'custom_link' ] !== '' ) { echo esc_attr( 'iframe-link'); } ?> g-item" title="<?php echo esc_attr( $image['title'] ); ?>">
                        <?php echo epron_get_image( false, $thumb_size, '', true, get_the_id() ) ?>
                    </a>
                </article>
            </div>
        <?php endwhile; ?>

        </div>

    <?php else : ?>

        <?php esc_html_e( 'Carousel Post: No posts found', 'epron-toolkit' ); ?>

    <?php endif; ?>

    <?php 
        wp_reset_postdata();

        $output = ob_get_clean();

        echo '<div class="kc-carousel-post '. esc_attr( implode(' ', $wrp_el_classes) ) .'">'. $output .'</div>';

        kc_js_callback( 'kc_front.owl_slider' );
    ?>
<?php else : ?>
     <div class="warning"><?php esc_html_e( 'Warning: There are no items to display this module. Please select gallery album.', 'epron-toolkit' ); ?></div>
<?php endif; ?>