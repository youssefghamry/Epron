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

// Date Format 
$date_format = get_option( 'date_format' );

// Module classes 
$classes = array(
    'post-grid-module'
);

if ( $atts['module_classes'] ) {
    $classes[] = $atts['module_classes'];  
}

// Module Opts 
$module_opts = array(
    'module' => 'epron_event_module1',
    'date_divider' => $atts['date_divider'],
    'classes' => implode( ' ', $classes )
);

// Month variables
$next_month = '';
$month_count = 0;

?>
<div data-module-opts='<?php echo json_encode( $module_opts ) ?>' class="ajax-grid flex-grid events-list posts-container anim-grid kc-releases-block1" data-anim-effect="amun">

<?php while ( $posts_block_q->have_posts() ) : ?>
<?php
    $posts_block_q->the_post();
    $count = $posts_block_q->post_count; 

    $module_args = array(
        'post_id'       => $posts_block_q->post->ID,
        'lazy'          => true,
        'title'         => get_the_title(),
        'permalink'     => get_permalink(),
        'posts_classes' => implode( ' ', get_post_class( '', $posts_block_q->post->ID ) ),
        'classes'       => $module_opts['classes']
    );

?>

    <div class="flex-item">
        <?php
            if ( $atts['date_divider'] === 'yes' ) {
                //////////////////////
                // Months Separator //
                //////////////////////
                $event_date_start = strtotime(get_post_meta($posts_block_q->post->ID, '_event_date_start', true));
                $this_month = date_i18n('F', $event_date_start);
                if ( $this_month != $next_month || $month_count == 0 ) {
                    echo '<h4 class="events-heading">' . esc_html( $this_month ) . ' <span class="color">' . date( 'Y', $event_date_start) . '</span></h4>';
                    $month_count++; 
                } 
                $next_month = $this_month;
            }
            
            echo epron_event_module1( $module_args ); 
        ?>
    </div>
 

<?php $i++;  ?>
<?php endwhile ?>
</div>