<?php
/**
 * Media Manager Images Field
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

if ( ! function_exists( 'rascals_media_manager_images' ) ):

function rascals_media_manager_images( $type, $id, $item, $options, $custom ) {
   
   /* Display only if the type matches */
  	if ( $type === 'images' ) {

  		/* Output */
  		$output = '';

  		/* Defaults */
	   	$defaults = array(
			'custom' => $custom,
			'title' => '',
			'custom_link' => ''
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
						<input type="text" id="mm-image-title" name="title" value="' . esc_attr( $options['title'] ) . '" />
						<p class="help-box">' . esc_html__( 'Image title.', 'epron-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';
		

		/* Custom Link */
		$output .= '
			<div class="box-row clearfix">
				<div class="box-row-input">
					<div class="box-tc box-tc-label">
						<label for="mm-image-custom-link">' . esc_html__( 'Custom Link', 'epron-toolkit' ) . '</label>
					</div>
					<div class="box-tc box-tc-input">
						<textarea id="mm-custom-link" name="custom_link" style="min-height:40px">'. wp_kses_post( $options['custom_link'] ) .'</textarea>
						<p class="help-box">' . esc_html__( 'Add custom link to popup window.', 'epron-toolkit' ) . '</p>
					</div>
				</div>
				<div class="box-row-line"></div>
			</div>';

		$output .= '</fieldset>';

		return $output;
	}


}

endif;