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

// Get date format  
$date_format = get_option( 'date_format' );

// Kingcomposer wrapper class for each element 
$wrap_class = apply_filters( 'kc-el-class', $atts );

// Set color scheme 
$atts['color_scheme'] = str_replace(' ', '', $atts['color_scheme'] );
$wrap_class[] =  $atts['color_scheme'] . '-scheme-el';

// Add custom classes to element 
$wrap_class[] = 'kc-recent-posts';
$wrap_class[] = 'rt-recent-posts';
if ( $atts['thumbnails'] === 'yes' ) {
    $wrap_class[] = 'rt-show-thumbs';
}
?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $atts['classes'] ); ?>">
    <?php

    $thumb_size = 'epron-medium-square-thumb';

    // Loop Args. 
    $loop_args = array(
        'showposts' => (int)$atts['limit']
    );

    // Number words to show 
    $words_number = 10;

    $recent_posts_q = new WP_Query( $loop_args );

    ob_start();
    if ( $recent_posts_q->have_posts() ) : ?>

    <ul class="rp-list">
        <?php while ( $recent_posts_q->have_posts() ) : ?>
        <?php
            $recent_posts_q->the_post();

            $tax_args = array(
                'id'         => $recent_posts_q->ID,
                'tax_name'   => 'category',
                'separator'  => ' · ',
                'link'       => false,
                'limit'      => 2,
                'show_count' => true
            );
        ?>
        <li>
            <?php if ( $atts['style'] === 'style1' ) : ?>
                <?php if ( ! empty( $atts['thumbnails'] ) ) : ?>
                <div class="rp-post-thumb">
                    <a class="rp-post-thumb-link" href="<?php echo get_permalink(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo epron_get_image( $recent_posts_q->ID, $thumb_size, 'rp-thumb', true ); ?></a>
                </div>
                <?php endif ?>
                <div class="rp-caption">
                    <div class="rp-cats"><?php echo epron_get_taxonomies( $tax_args ); ?></div>
                    <h4><a href="<?php echo get_permalink(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" ><?php echo get_the_title(); ?></a></h4>
                    <?php if ( has_excerpt() && ! empty( $atts['excerpt'] ) ) : ?>
                        <div class="rp-excerpt">
                            <?php echo wp_trim_words( get_the_excerpt(), $words_number ); ?>
                        </div>
                    <?php endif; ?>        
                </div>
            <?php endif; ?>


            <?php if ( $atts['style'] === 'style2' ) : ?>
                <div class="rp-post-wrap">
                    <div class="rp-post-date">
                        <span><?php echo get_the_time( $date_format ) ?></span>
                    </div>
                    <div class="rp-post-title">
                       <h4><a href="<?php echo get_permalink(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" ><?php echo get_the_title(); ?></a></h4>
                    </div>

                </div>


            <?php endif ?>
        </li>
    <?php endwhile ?>
    </ul>
    <?php endif; ?>
</div>

<?php

    wp_reset_postdata();

    $output = ob_get_clean();

   $toolkit->e_esc( $output ); 
?>