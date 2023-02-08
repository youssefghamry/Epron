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

// Add custom classes to element 
$wrap_class[] = 'kc-images-block';

// Module classes
if ( isset( $atts['module_classes'] ) ) {
    $atts['module_classes'] .= ' ' . $atts['color_scheme'] . '-scheme-el';
}

// Add grid classes depends on block 
$grid_classes = '';

switch ( $atts['images_number'] ) {
    case '1':
        $grid_classes = 'flex-1 flex-tablet-1 flex-mobile-1 flex-mobile-portrait-1';
        break;
    case '2':
        $grid_classes = 'flex-2 flex-tablet-2 flex-mobile-2 flex-mobile-portrait-1';
        break;
    case '3':
        $grid_classes = 'flex-3 flex-tablet-3 flex-mobile-2 flex-mobile-portrait-1';
        break;
     case '4':
        $grid_classes = 'flex-4 flex-tablet-4 flex-mobile-2 flex-mobile-portrait-1';
        break;
    case '5':
        $grid_classes = 'flex-5 flex-tablet-4 flex-mobile-2 flex-mobile-portrait-1';
        break;   
}

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $atts['classes'] ); ?>">
    <div class="kc-images-block-inner">

        <?php if ( $atts['album_id'] !== 'none' ) : ?>

            <?php

            // Thumb Size 
            $thumb_size = 'epron-large-square-thumb';

            // Lazy loading 
            $lazy = true;

            // Album ID 
            $album_id = $atts['album_id'];

            // Images 
            $images_ids = get_post_meta( $album_id, '_gallery_images', true ); 

            ?>

            <?php ob_start(); ?>

            <?php if ( $atts['grid_type'] === 'masonry' ) : ?>
                <?php 
                // Thumb size 
                $thumb_size = 'large';

                ?>
                <div class="gallery-images-grid masonry-grid anim-grid" data-anim-effect="amun">
            <?php else : ?>
                <div class="gallery-images-grid ajax-grid flex-grid <?php echo esc_attr( $grid_classes ) ?> flex-gap-medium flex-anim flex-anim-fadeup posts-container">
            <?php endif; ?>
            
                <?php if ( $images_ids || $images_ids !== '' ) :

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

                    $posts_block_q = new WP_Query();
                    $posts_block_q->query( $gallery_loop_args );
                    ?>
        
                    <?php while ( $posts_block_q->have_posts() ) : ?>

                        <?php
                        $posts_block_q->the_post();
                  
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

                        <?php if ( $atts['grid_type'] === 'masonry' ) : ?>
                            <?php $lazy = false; ?>
                            <div class="masonry-brick">
                                <div class="masonry-content">
                        <?php else : ?>
                            <div class="flex-item">
                                <div class="flex-content">
                        <?php endif; ?>
               
                                <div <?php post_class(); ?>>
                                    <a href="<?php echo esc_attr( $link ) ?>" class="<?php if ( $image[ 'custom_link' ] !== '' ) { echo esc_attr( 'iframe-link'); } ?> g-item" title="<?php echo esc_attr( $image['title'] ); ?>">
                                        <?php echo epron_get_image( false, $thumb_size, '', $lazy, get_the_id() ) ?>
                                    </a>
                                </div>

                            </div>
                        </div>
            
                    <?php endwhile ?>
                
                 <?php else : ?>
                   <div class="warning"><?php esc_html_e( 'Warning: There are no items to display this module. Please select gallery album.', 'epron-toolkit' ); ?></div>
                <?php endif; ?>

            </div>

        <?php else : ?>
           <div class="warning"><?php esc_html_e( 'Warning: There are no items to display this module. Please select gallery album.', 'epron-toolkit' ); ?></div>
        <?php endif; ?>
       
    </div>
</div>

<?php
    
    wp_reset_postdata();

    $output = ob_get_clean();

    $toolkit->e_esc( $output ); 

    kc_js_callback( 'theme.masonry.init' );

?>