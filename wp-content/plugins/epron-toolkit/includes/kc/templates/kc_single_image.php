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

$output     = $title_link  = $alt = $image = $target = $image_effect = $image_title = $image_source = $image_external_link = $image_size = $image_size_el = $caption = $on_click_action = $custom_link = $class = $ieclass = $image_full = $html = $css = '';
$size_array = array('full', 'medium', 'large', 'thumbnail');
$image_wrap = 'yes';

extract( $atts );


$css_classes        = apply_filters( 'kc-el-class', $atts );
$default_src        = kc_asset_url('images/get_start.jpg');
$image_source       = $atts['image_source'];
$image_url          = '';
$image_id           = $atts['image'];
$image_size         = $atts['image_size'];
$on_click_action    = $atts['on_click_action'];
$data_lightbox      = '';

$a_classes          = array();
$element_attributes = array();
$image_attributes   = array();
$image_classes      = array();

$css_classes = array_merge( $css_classes, array(
	'kc_shortcode',
	'kc_single_image'
));

if ( !empty( $class ) ) {
	$css_classes[] = $class;
}

if ( !empty( $css ) ) {
	$css_classes[] = $css;
}

if( !empty( $ieclass ) ) {
	$image_classes[] = $ieclass;
}


////////////
// CUSTOM //
////////////

// Tooltip
isset( $tip_text ) ?: $tip_text = '';
isset( $tip_title ) ?: $tip_title = '';
if ( $tip_text !== '' && $tip_title !== '' ) {
	$a_classes[] = 'tip';
}

// 


if( $image_source == 'external_link' ) {

	$image_full = $atts['image_external_link'];
	$image_url  = $image_full;
	$size       = $atts['image_size_el'];

	if( !empty( $image_url ) )
		$image_attributes[] = 'src="'.$image_url.'"';
	else
		$image_attributes[] = 'src="'.$default_src.'"';

	if( empty( $image_full ) )
		$image_full = $default_src;

	if ( preg_match( '/(\d+)x(\d+)/', $size, $matches ) ) {
		$width              = $matches[1];
		$height             = $matches[2];
		$image_attributes[] = 'width="'. $width .'"';
		$image_attributes[] = 'height="'. $height .'"';
	}
} else {

	if( $image_source == 'media_library' ) {
		//$image_id = preg_replace( '/[^\d]/', '', $image_id );
	} else {
		$post_id = get_the_ID();

		if ( $post_id && has_post_thumbnail( $post_id ) ) {
			$image_id = get_post_thumbnail_id( $post_id );
		} else {
			$image_id = 0;
		}
	}

	$image_full_width = wp_get_attachment_image_src( $image_id, 'full' );
	$image_full       = $image_full_width[0];
	
	if( in_array( $image_size, $size_array ) ){
		$image_data       = wp_get_attachment_image_src( $image_id, $image_size );
		$image_url        = $image_data[0];
	}else{
		$image_url 	= kc_tools::createImageSize( $image_full, $image_size );
	}
	

	if( !empty( $image_url ) ) {
		$image_attributes[] = 'src="'.$image_url.'"';
	} else {
		$image_attributes[] = 'src="'.$default_src.'"';
		$image_classes[] = 'kc_image_empty';
	}

	if( empty( $image_full ) )
		$image_full = $default_src;

}

if ( $image_effect === 'thumb_slide' ) {
	$image_classes[] = 'thumb--a';
	$a_classes[] = 'thumb-slide';
}

$image_attributes[] = 'class="'.implode( ' ', $image_classes ).'"';

if( !empty( $alt ) ) {
	$image_attributes[] = 'alt="'. trim(esc_attr($alt)) .'"';
} else {
	$image_attributes[] = 'alt="' . esc_html__( 'Single Image', 'epron-toolkit' ) . '"';
}

$title_link = $alt;

if( $on_click_action == 'lightbox' ) {
	$a_classes[] = 'imagebox';
}
else if( $on_click_action == 'lightbox_iframe' ) {
	$a_classes[] = 'iframebox';
	$image_full = $iframe_link;
}
else if( $on_click_action == 'open_custom_link' ) {

    $custom_link	= ( '||' === $custom_link ) ? '' : $custom_link;
    $custom_link	= kc_parse_link($custom_link);
    
    if ( strlen( $custom_link['url'] ) > 0 ) {
        $image_full 	= $custom_link['url'];
        $title_link 	= $custom_link['title'];
        $target 	= strlen( $custom_link['target'] ) > 0 ? $custom_link['target'] : '_self';
    }
}

// overlay layer
if( $image_effect === 'overlay' ){
    $html = '<div class="kc-image-overlay">';
    if( !empty( $icon ) )
        $html .= '<i class="' . $icon . '"></i>';
    $html .= '</div>';
}


$css_class            = implode(' ', $css_classes);
$element_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

?>
<div <?php echo implode( ' ', $element_attributes ) ;?>>

    <?php
    if( !empty($on_click_action) ) {
    ?>
    <a class="<?php echo implode( ' ', $a_classes ) ;?>" href="<?php echo esc_attr( $image_full );?>" title="<?php echo strip_tags( $title_link ) ;?>" target="<?php echo esc_attr( $target );?>">
    	<span class="thumbs-wrap">
        	<img <?php echo implode( ' ', $image_attributes ) ;?> /><?php echo wp_kses_post( $html );?>

        	<?php 
        	if ( $image_effect === 'thumb_slide' ) {

        		if ( $image_hover !== '' ) {
					$image_full_width = wp_get_attachment_image_src( $image_hover, 'full' );
					$image_full       = $image_full_width[0];

					if( in_array( $image_size, $size_array ) ){
						$image_data       = wp_get_attachment_image_src( $image_hover, $image_size );
						$image_url        = $image_data[0];
					}else{
						$image_url 	= kc_tools::createImageSize( $image_full, $image_size );
					}
				}

				echo '<img class="thumb--b" src=' . esc_url( $image_url ) . ' alt="' . esc_html__( 'Single Image', 'epron-toolkit' ) . '"/>';
			}

        	 ?>

        </span>


        <?php if ( $tip_text !== '' && $tip_title !== '' ) : ?>

			<div class="tip-content hidden"><p><span><?php echo wp_kses_post( $tip_title ) ?></span><?php echo wp_kses_post( $tip_text ) ?></p></div>

		<?php endif; ?>
    </a>
    <?php
    } else {
    ?>
    <img <?php echo implode( ' ', $image_attributes ) ;?> /><?php echo wp_kses_post( $html ); ?>
    <?php
    }
 	if( !empty( $badge ) ){
	    ?>
	    <span class="badge <?php echo esc_attr( $badge ) ?>"><?php echo esc_html( $badge ) ?></span>
	    <?php
    }
    if( !empty( $caption ) ){
	    ?>
	    <p class="scapt"><?php echo html_entity_decode( $caption );?></p>
	    <?php
    }
    ?>
</div>
