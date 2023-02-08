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

// Module classes 
$classes = array(
    'post-grid-module'
);
if ( $atts['module_classes'] ) {
    $classes[] = $atts['module_classes'];  
}

// Module Opts 
$module_opts = array(
    'module'      => 'epron_release_module1',
    'thumb_size'  => 'epron-large-square-thumb',
    'classes'     => implode( ' ', $classes )
);

?>
<div data-module-opts='<?php echo json_encode( $module_opts ) ?>' class="ajax-grid flex-grid <?php echo esc_attr( $grid_classes ) ?> flex-gap-medium kc-flex-no-gap anim-grid kc-releases-block1" data-anim-effect="amun"> 
<?php while ( $posts_block_q->have_posts() ) : ?>
<?php
    $posts_block_q->the_post();
    $count = $posts_block_q->post_count; 

    $module_args = array(
        'post_id'       => $posts_block_q->post->ID,
        'thumb_size'    => $thumb_size,
        'lazy'          => false,
        'title'         => get_the_title(),
        'permalink'     => get_permalink(),
        'posts_classes' => implode( ' ', get_post_class( '', $posts_block_q->post->ID ) ),
        'classes'       => $atts['module_classes']
    );

?>

    <div class="flex-item">
        <?php
            echo epron_release_module1( $module_args ); 
        ?>
    </div>
 

<?php $i++;  ?>
<?php endwhile ?>
</div>