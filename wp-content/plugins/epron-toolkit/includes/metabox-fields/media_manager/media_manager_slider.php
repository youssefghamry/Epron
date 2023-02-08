<?php
/**
 * Media Manager Slider Field
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

if ( ! function_exists( 'rascals_media_manager_slider' ) ):

function rascals_media_manager_slider( $type, $id, $item, $options, $custom ) {
   
   /* Display only if the type matches */
  	if ( $type === 'slider' ) {

  		$toolkit = epronToolkit();

  		/* Output */
  		$output = '';

  		/* Defaults */
	   	$defaults = array(
			'custom'        => $custom,
			'image_type'    => 'image', // image, lightbox, media, link, link_blank,
			'lightbox_link' => '',
			'link'          => '',
			'media_code'    => '',
			'media_link'    => '',
			'music'         => 'no', // no, yes
			'track_id'      => '', // track id
			'track_nr'      => '1',
			'title'         => '',
			'subtitle'      => '',
		);

		/* Set default options */
		if ( isset( $options ) && is_array( $options ) ) {
			$options = array_merge( $defaults, $options );
		} else {
			$options = $defaults;
		}

		/* Helpers */

		/* Target options */
		$target_options = array(
			array('name' => esc_html__( 'Same Window/Tab', 'epron-toolkit' ), 'value' => '_self'),
			array('name' => esc_html__( 'New Window/Tab', 'epron-toolkit' ), 'value' => '_blank')
		);

		/* Yes/No */
		$yes_no_options = array(
			array('name' => esc_html__( 'No', 'epron-toolkit' ), 'value' => 'no'),
			array('name' => esc_html__( 'Yes', 'epron-toolkit' ), 'value' => 'yes')
		);

		$image_type = array(
			array('name' => 'Image', 'value' => 'image'),
			array('name' => 'Image lightbox', 'value' => 'lightbox'),
			array('name' => 'Image with custom media', 'value' => 'media'),
			array('name' => 'Custom link', 'value' => 'link'),
			array('name' => 'Custom link (new window)', 'value' => 'link_blank'),
		);


		/*  IMAGE META 
		 ------------------------------------------------------------------------------*/
		/* Get Image Data */
		$meta = wp_get_attachment_metadata( $id );
		$image_data = wp_get_attachment_image_src( $id );

		$output .= '
			<div class="mm-item mm-item-editor" id="' . esc_attr( $id ) . '">
				<div class="mm-item-preview">
			    	<div class="mm-item-image">
			    		<div class="mm-centered">
			    			<a href="' . esc_url( $item->guid ) . '" target="_blank"><img src="' . esc_url( $image_data[0] ) . '" /></a>
			    		</div>
			    	</div>
				</div>
			</div>';
		
		/* Meta */
		$output .= '<div id="mm-editor-meta">';
			$output .= '<span><strong>' . esc_html__( 'File name:', 'epron-toolkit' ) . '</strong> ' . esc_html( basename( $item->guid ) ) . '</span>';
			$output .= '<span><strong>' . esc_html__( 'File type:', 'epron-toolkit' ) . '</strong> ' . esc_html( $item->post_mime_type ) . '</span>';
			$output .= '<span><strong>' . esc_html__( 'Upload date:', 'epron-toolkit' ) . '</strong> ' . mysql2date( get_option( 'date_format' ), $item->post_date ) . '</span>';

			if ( is_array( $meta ) && array_key_exists( 'width', $meta ) && array_key_exists('height', $meta ) ) {
				$output .= '<span><strong>' . esc_html__( 'Dimensions:', 'epron-toolkit' ) . '</strong> ' . esc_html( $meta['width'] ) . ' x ' . esc_html( $meta['height'] ) . '</span>';
			}

			$output .= '<span><strong>' . esc_html__( 'Image URL:', 'epron-toolkit' ) . '</strong> <br>
			<a href="' . esc_url( $item->guid ) . '" target="_blank">' . esc_html__( '[IMAGE LINK]', 'epron-toolkit' ) . '</a>
			</span>';

		$output .= '</div>';


		/*  FIELDS
		 ------------------------------------------------------------------------------*/

		$output .= '<fieldset class="rascalsbox">';
			
			/* Title */
			$output .= '
				<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-image-title">' . esc_html__( 'Title', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<textarea id="mm-image-title" name="title" style="min-height:40px">'. wp_kses_post( $options['title'] ) .'</textarea>
							<p class="help-box">' . esc_html__( 'Image title.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';
			

			/* Subtitle */
			$output .= '
				<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-image-subtitle">' . esc_html__( 'Subtitle', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<textarea id="mm-image-subtitle" name="subtitle" style="min-height:40px">'. wp_kses_post( $options['subtitle'] ) .'</textarea>
							<p class="help-box">' . esc_html__( 'Image subtitle.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';


			/* Video */
			$output .= '
				<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-image-video">' . esc_html__( 'Video', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<textarea id="mm-image-video" name="video" style="min-height:40px">'. wp_kses_post( $options['video'] ) .'</textarea>
							<p class="help-box">' . esc_html__( 'Paste the full URL (include http://) of your Vimeo or Youtube movie. Video will be shown instead of the image.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';


			/* Image Type */
			$output .= '
				<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-image-type">' . esc_html__( 'Image Type', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<select id="mm-image-type" name="image_type" size="1" data-main-group="mm-main-group-image-type" class="box-select mm-group">';

				foreach ( $image_type as $option ) {
						
					if ( $options['image_type'] == $option['value'] ) 
						$selected = 'selected';
					else 
						$selected = '';
					$output .= "<option " . esc_attr( $selected ) . " value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
				}

			$output .= '</select>';
			$output .= '<p class="help-box">' . esc_html__( 'Select image type.', 'epron-toolkit' ) . '<br>' . esc_html__( 'NOTE: Displayed only on Intro slider section.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';


			/* Lightbox Link */
			$output .= '
				<div class="box-row clearfix mm-group-lightbox mm-main-group-image-type" style="display:none">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-lightbox_link">' . esc_html__( 'Lightbox Link', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<input type="text" id="mm-lightbox_link" name="lightbox_link" value="' . esc_attr( $options['lightbox_link'] ) . '" />
							<p class="help-box">' . esc_html__( 'Paste the full URL (include http://) of your image you would like to use for jQuery lightbox pop-up effect.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';


			/* Link */
			$output .= '
				<div class="box-row clearfix mm-group-link mm-group-link_blank mm-main-group-image-type" style="display:none">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-link">' . esc_html__( 'Link', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<input type="text" id="mm-link" name="link" value="' . esc_attr( $options['link'] ) . '" />
							<p class="help-box">' . esc_html__( 'Paste the full URL (include http://).', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';

			/* Media */
			$output .= '
				<div class="box-row clearfix media-embed mm-group-media mm-main-group-image-type" style="display:none">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-media-code">' . esc_html__( 'Media Code', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<textarea id="mm-media-code" name="media_code" style="min-height:40px">'. wp_kses_post( $options['media_code'] ) .'</textarea>
							<p class="help-box">' . esc_html__( 'Paste media embed code (iframe) of Soundcloud, Mixcloud or links to Youtube, Vimeo.', 'epron-toolkit' ) . '</p>
							<input type="hidden" id="media_link" name="media_link" value="' . esc_attr( $options['media_link'] ) . '"/>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';


			/* Show Music Player? */
			$output .= '
				<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-music">' . esc_html__( 'Show Music Player?', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<select id="mm-music" data-main-group="mm-main-group-music" name="music" size="1" class="box-select mm-group">';

				foreach ( $yes_no_options as $option ) {
						
					if ( $options['music'] == $option['value'] ) 
						$selected = 'selected';
					else 
						$selected = '';
					$output .= "<option " . esc_attr( $selected ) . " value='" . esc_attr( $option['value'] ) . "'>" . esc_attr( $option['name'] ) . "</option>";
				}

			$output .= '</select>';
			$output .= '<p class="help-box">' . esc_html__( 'Show music player.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';




			/* Track ID */
			$output .= '
				<div class="box-row clearfix mm-group-yes mm-main-group-music" style="display:none">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-track_id">' . esc_html__( 'Select Track(s)', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<select name="track_id" id="mm-track_id" data-main-group="mm-main-group-track_id" name="music" size="1" class="box-select mm-group">';

					/* Get Audio Tracks  */
					$type = 'epron_tracks';
					$args = array(
						'post_type'      => $type,
						'post_status'    => 'publish',
						'posts_per_page' => -1
					);

					$tracks = "<option value='none'>" . esc_html__( 'Select tracks...', 'epron-toolkit' ) . "</option>";
					$my_query = null;
					$my_query = new WP_Query($args);
					if ( $my_query->have_posts() ) {
	  					while ( $my_query->have_posts() ) {
	  						$my_query->the_post();

	  						if ( get_the_id() == $options['track_id'] ) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							$tracklist = $toolkit->scamp_player->getList( $id );
							if ( $toolkit->scamp_player->getList(  get_the_id() ) ) {

								$tracks .= "<option $selected value='" . esc_attr( get_the_id() ) . "'>" . get_the_title() . " (" . count( $toolkit->scamp_player->getList(  get_the_id() ) ) . ")</option>";
							} 
	  					}
	  				}

	  				$output .= $tracks;
					wp_reset_query();  // Restore global post data stomped by the_post().
						
					
			$output .= '</select>';
			$output .= '<p class="help-box">' . esc_html__( 'Select tracklist or single track.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';


			/* Track Number */
			$output .= '
				<div class="box-row clearfix mm-group-yes mm-main-group-music" style="display:none">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-track_nr">' . esc_html__( 'Track Number', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<input type="number" id="mm-track_nr" name="track_nr" value="' . esc_attr( $options['track_nr'] ) . '" />
							<p class="help-box">' . esc_html__( 'Select track.', 'epron-toolkit' ) . '</p>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';

		$output .= '</fieldset>';

		return $output;
	}


}

endif;