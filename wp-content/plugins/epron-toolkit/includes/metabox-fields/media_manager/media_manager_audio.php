<?php
/**
 * Media Manager Audio Field
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed
}

if ( ! function_exists( 'rascals_media_manager_audio' ) ):

function rascals_media_manager_audio( $type, $id, $item, $options, $admin_path, $custom ) {
   
   /* Display only if the type matches */
  	if ( $type === 'audio' ) {

  		/* Output */
  		$output = '';

  		/* Defaults */
	   	$defaults = array(
			'custom'     => $custom,
			'custom_url' => '',
			'title'      => '',
			'buttons'    => '',
			'desc'       => '',
			'cover'      => '',
			'waveform'   => '',
			'volume'     => '100'
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


		/*  FIELDS
		 ------------------------------------------------------------------------------*/

		$output .= '<fieldset class="rascalsbox">';
		/* Loading layer */
		$output .= '<div class="loading-layer"></div>';	
		/* Title */
		if ( $options['title'] === '' && ! $options['custom'] ) {
			$options['title'] = $item->post_title;
		}
		if ( $options['title'] === '' ) {
			$options['title'] = esc_html__( 'Custom title', 'epron-toolkit' );
		}
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label>' . esc_html__( 'Track ID', 'meloo-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<input type="text" id="mm-audio-id" onfocus="this.select();" readonly="readonly" value="' . esc_attr( $id ) . '" />
						<p class="help-box">' . esc_html__( 'Track ID can be used to select tracks.', 'meloo-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>

			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="mm-audio-title">' . esc_html__( 'Track Title', 'meloo-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<input type="text" id="mm-audio-title" name="title" value="' . esc_attr( $options['title'] ) . '" />
						<p class="help-box">' . esc_html__( 'Enter track title.', 'meloo-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';
		

		/* Custom url */
		if ( $options['custom'] ) {
			$output .= '
				<div class="box-row clearfix">
					<div class="box-row-input">
						<div class="box-tc box-tc-label">
							<label for="mm-audio-custom-url">' . esc_html__( 'Release/Track URL', 'epron-toolkit' ) . '</label>
						</div>
						<div class="box-tc box-tc-input">
							<input type="text" id="mm-audio-custom-url" name="custom_url" value="' . esc_attr( $options['custom_url'] ) . '" />
							<p class="help-box">' . esc_html__( 'Paste here link to the MP3 file or link to Soundcloud track, list, favorite tracks, or paste direct link of music track from following services like: hearthis.at and click on appropriate button. Then the fields will be automatically filled in with the data taken from the selected site.', 'epron-toolkit' ) . '</p>
							<div class="sub-name services-label">' . esc_html__( 'Get track data from following services:', 'epron-toolkit' ) . '</div>
							<div class="box-services-buttons">
								<button class="_button add-hearthis"><i class="fa icon fa-plus"></i>'.esc_html__( 'hearthis.at', 'epron-toolkit' ).'</button><button class="_button add-googledrive"><i class="fa icon fa-plus"></i>'.esc_html__( 'Google Drive', 'epron-toolkit' ).'</button>
							</div>
							
							<div class="services-messages">
								<p class="msg msg-warning msg-correct-link">'.esc_html__( 'Please enter a valid link, or select another service..', 'epron-toolkit' ).'</p>
								<p class="msg msg-warning msg-already-exists">'.esc_html__( 'Link is already converted, please enter a new link.', 'epron-toolkit' ).'</p>
								<p class="msg msg-error msg-track-error">'.esc_html__( 'Error! Data could not be retrieved. Please try later, service may now be disabled.', 'epron-toolkit' ).'</p>
								<p class="msg msg-success msg-done">'.esc_html__( 'Done! Data has been downloaded successfully.', 'epron-toolkit' ).'</p>
							</div>
						</div>
					</div>
					<div class="box-row-line"></div>
				</div>';
		}

		/* Description  */
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="mm-track_desc">' . esc_html__( 'Description', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<textarea id="mm-audio-desc" name="desc" style="min-height:120px">'. wp_kses_post( $options['desc'] ) .'</textarea>
						<p class="help-box">' . esc_html__( 'Short description for the audio track e.g.: Artists names etc.', 'epron-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';

		/* Buttons */
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="mm-audio-buttons">' . esc_html__( 'Track Buttons', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<textarea id="mm-audio-buttons" name="buttons" style="min-height:120px">'. wp_kses_post( $options['buttons'] ) .'</textarea>
						<p class="help-box">' . esc_html__( 'Add player buttons, sparated by enter. Button example:
	[player_button title="Download" link="http://link_here" target="_self"]', 'epron-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';

		/* Cover */
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label>' . esc_html__( 'Cover Image', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">';
						
						/* Source */
						$external_link = 'selected="selected"';
						$media_libary = '';
						$input_type = 'text';
						$holder_classes = ' hidden';

						if ( is_numeric( $options['cover'] ) || $options['cover'] === '' ) {
							$media_libary = 'selected="selected"';
							$external_link='';
							$input_type = 'hidden';
							$holder_classes = '';
						}

						$output .= '<select size="1" class="image-source-select cover-source" >';

							$output .= "<option $media_libary value='media_libary'>" . esc_html__( 'Media libary', 'epron-toolkit' ) . "</option>";
							$output .= "<option $external_link value='external_link'>" . esc_html__( 'External link', 'epron-toolkit' ) . "</option>";
						
						$output .= '</select>';

						$output .= '<input type="' . esc_attr( $input_type ) . '" id="r-cover" name="cover" value="' . esc_attr( $options['cover'] ) . '" class="track-cover image-input" />';

						$image = wp_get_attachment_image_src( $options['cover'], 'thumbnail' );
						$image = $image[0];
						// If image exists
						if ( $image ) {
							$image_html = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Preview Image', 'epron-toolkit' ) . '">';
							$is_image = 'is_image'; 
						} else {
							$image_html = '';
							$is_image = ''; 
						}

						$output .= '<div class="image-holder image-holder-cover ' . esc_attr( $is_image ) . ' ' . esc_attr( $holder_classes ) . '" data-placeholder="' . esc_url( $admin_path ) . '/assets/images/metabox/audio.png">';

						// Image
						$output .=  $image_html;

						// Button
						$output .= '<button class="upload-image"><i class="fa icon fa-plus"></i></button>';

						/* Remove image */
						$output .= '<a class="remove-image"><i class="fa icon fa-remove"></i></a>';
						$output .= '</div>';
						
		$output .= '<p class="help-box">' . esc_html__( 'Add image cover.', 'epron-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';


		/* Waveform */
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label>' . esc_html__( 'Waveform Image', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">';
						
						/* Source */
						if ( is_numeric( $options['waveform'] ) || $options['waveform'] == '' ) {
							$media_libary = 'selected="selected"';
							$input_type = 'hidden';
						} else {
							$external_link = 'selected="selected"';
							$input_type = 'text';
							$holder_classes .= ' hidden';
						}

						$output .= '<select size="1" class="image-source-select" >';

							$output .= "<option $media_libary value='media_libary'>" . esc_html__( 'Media libary', 'epron-toolkit' ) . "</option>";
							$output .= "<option $external_link value='external_link'>" . esc_html__( 'External link', 'epron-toolkit' ) . "</option>";
						
						$output .= '</select>';

						$output .= '<input type="' . esc_attr( $input_type ) . '" id="r-waveform" name="waveform" value="' . esc_attr( $options['waveform'] ) . '" class="track-waveform image-input" />';

						$image = wp_get_attachment_image_src( $options['waveform'], 'thumbnail' );
						$image = $image[0];
						// If image exists
						if ( $image ) {
							$image_html = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Preview Image', 'epron-toolkit' ) . '">';
							$is_image = 'is_image'; 
						} else {
							$image_html = '';
							$is_image = ''; 
						}

						$output .= '<div class="image-holder image-holder-waveform ' . esc_attr( $is_image ) . ' ' . esc_attr( $holder_classes ) . '" data-placeholder="' . esc_url( $admin_path ) . '/assets/images/metabox/audio.png">';

						// Image
						$output .=  $image_html;

						// Button
						$output .= '<button class="upload-image"><i class="fa icon fa-plus"></i></button>';

						/* Remove image */
						$output .= '<a class="remove-image"><i class="fa icon fa-remove"></i></a>';
						$output .= '</div>';
						
		$output .= '<p class="help-box">' . esc_html__( 'Add track waveform, best image is white or black PNG (depends on theme skin) with transparent background. Waveform can be generated on following site:', 'epron-toolkit' ) . '<br><a href="http://convert.ing-now.com/mp3-audio-waveform-graphic-generator/" target="_blank">Waveform generator</a></p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';

		/* Volume */
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="mm-audio-volume">' . esc_html__( 'Volume', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<input type="text" id="mm-audio-volume" name="volume" value="' . esc_attr( $options['volume'] ) . '" />
						<p class="help-box">' . esc_html__( 'Set track volume (0-100)', 'epron-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';

		$output .= '</fieldset>';

		return $output;
	}


}

endif;