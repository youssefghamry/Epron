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
$wrap_class[] = 'kc-video-cover';

extract( $atts );

?>
<div class="<?php echo esc_attr( implode(' ', $wrap_class) ); ?> <?php echo esc_attr( $classes ); ?>">
	
   		<?php if ( $video_source === 'youtube' && $video_link !== '' ) : ?>

   			<?php 
			if ( preg_match( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $video_link, $matches ) ) :
			?>
		    	<div class="youtube media" id="<?php echo esc_attr( $matches[1] ) ?>"></div>
		    	<?php kc_js_callback( 'theme.social_players.youtube' ); ?>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ( $video_source === 'vimeo' && $video_link !== '' ) : ?>
			<?php if ( preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $video_link, $matches  ) ) : ?>
        		<div class="vimeo media" id="<?php echo esc_attr( $matches[5] ) ?>" data-parallax='{"y": <?php echo esc_attr( $parallax_y ) ?>}'></div>
        		<?php kc_js_callback( 'theme.social_players.vimeo' ); ?>
			<?php endif; ?>

		<?php endif; ?>

</div>