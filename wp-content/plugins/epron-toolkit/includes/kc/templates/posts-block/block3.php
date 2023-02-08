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


// Variables 
$i = 0;

if ( empty( $thumb_size ) ) {
    $thumb_size = 'epron-large-square-thumb';
}

?>
<div class="ajax-grid masonry-grid anim-grid" data-anim-effect="amun"> 
<?php while ( $posts_block_q->have_posts() ) : ?>
<?php
    $posts_block_q->the_post();
    $count = $posts_block_q->post_count; 

    $tax_args = array(
        'id'         => $posts_block_q->ID,
        'tax_name'   => 'category',
        'separator'  => ' · ',
        'link'       => false,
        'limit'      => 2,
        'show_count' => true
    );
    $cats_html = epron_get_taxonomies( $tax_args );

    $module_args = array(
        'post_id'       => $posts_block_q->post->ID,
        'thumb_size'    => $thumb_size,
        'lazy'          => false,
        'title'         => get_the_title(),
        'permalink'     => get_permalink(),
        'author'        => esc_html( get_the_author_meta( 'display_name', $posts_block_q->post->post_author ) ),
        'date'          => get_the_time( $date_format ),
        'posts_cats'    => $cats_html,
        'posts_classes' => implode( ' ', get_post_class( '', $posts_block_q->post->ID ) ),
        'classes'       => $atts['module_classes'] . ' anim-zoom big-module'
    );

?>

    <div class="masonry-brick">
        <div class="masonry-content">
        <?php
            echo epron_module2( $module_args ); 
        ?>
        </div>
    </div>
 

<?php $i++;  ?>
<?php endwhile ?>
</div>