<?php

/**
 * Rascals Flickr Widget
 *
 * Displays recent images from Flickr
 *
 * @author Rascals Themes
 * @category Widgets
 * @package PendulumToolkit/Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */
class RascalsFlickrWidget extends WP_Widget {


	/**
	 * Constructor
	 */
	function __construct() {

		/* Widget settings */
		$widget_ops = array(
			'classname' => 'widget_rt_flickr',
			'description' => esc_html__( 'Display latest images from Flickr', 'epron-toolkit' )
		);
		
		/* Create the widget */ 
		parent::__construct( 'rt-flickr-widget', esc_html__( '[RT] Flickr', 'epron-toolkit' ), $widget_ops );
		
	}


	/**
	 * Widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		extract( $args );
		$title = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title = apply_filters( 'widget_title', $title );

		$source  = ( isset( $instance['flickr_source'] ) ) ? sanitize_text_field( $instance['flickr_source'] ) : 'user';
		$display = ( isset( $instance['flickr_display'] ) ) ? sanitize_text_field( $instance['flickr_display'] ) : 'latest';
		$id      = ( isset( $instance['flickr_id'] ) ) ? sanitize_text_field( $instance['flickr_id'] ) : '';
		$set     = ( isset( $instance['flickr_set'] ) )  ? sanitize_text_field( $instance['flickr_set'] ) : '';
		$nr      = ( isset( $instance['flickr_limit'] ) ) ? absint(  $instance['flickr_limit'] ) : 6;
		$classes = ( isset( $instance['classes'] ) ) ? sanitize_text_field( $instance['classes'] ) : '';
		
		echo wp_kses_post( $before_widget );
		
		// Title
		if ( ! empty( $title ) ) { 
			echo wp_kses_post( $before_title ) . esc_attr( $title) . wp_kses_post( $after_title );
		}
		 if ( $source === 'user' && $id !== '' ) {
		    echo '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=' . esc_attr( $nr ) . '&amp;display=' . esc_attr( $display ). '&amp;size=s&amp;layout=x&amp;source=user&amp;user=' . esc_attr( $id ) . '"></script>';
		} elseif ( $source === 'set' && $set !== '' ) {
			echo '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=' . esc_attr( $nr ) . '&amp;display='. esc_attr( $display ) .'&amp;size=s&amp;layout=x&amp;source=user_set&amp;set=' . esc_attr( $set ) . '"></script>';
		} else {
			echo '<p>' . esc_html__( 'The Flickr ID or user set is invalid or does not exist.',  'epron-toolkit' ) . '</p>';
		}

	    echo wp_kses_post( $after_widget );

	}


	/**
	 * Update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance                   = $old_instance;
		$instance['title']          = sanitize_text_field( $new_instance['title'] );
		$instance['flickr_source']  = sanitize_text_field( $new_instance['flickr_source'] );
		$instance['flickr_display'] = sanitize_text_field( $new_instance['flickr_display'] );
		$instance['flickr_id']      = sanitize_text_field( $new_instance['flickr_id'] );
		$instance['flickr_set']     = sanitize_text_field( $new_instance['flickr_set'] );
		$instance['flickr_limit']   = absint( $new_instance['flickr_limit'] );
		$instance[ 'classes' ]      = sanitize_text_field( $new_instance[ 'classes' ] );
		
		return $instance;
	}


	/**
	 * Form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		
		$defaults = array(
			'title'          => esc_html__( 'Flickr', 'epron-toolkit' ), 
			'flickr_source'  => 'user', 
			'flickr_display' => 'latest', 
			'flickr_id'      => '', 
			'flickr_set'     => '', 
			'flickr_limit'   => '6',
			'classes'        => ''
		);
		$instance = wp_parse_args( (array ) $instance, $defaults );

		// Get acces to RascalsWidgets Class -> Fields
		$RW = new RascalsWidgets();

		// Tabs
		$RW->tabsStart( 
			array( 
				esc_html__( 'General', 'epron-toolkit' ),
				esc_html__( 'Advanced', 'epron-toolkit' ) 
			)
		);

  		// ----------------------- Tab 01
  		$RW->tabStart(1);

			// Title
			$RW->input_e( array(
				'label' => esc_html__( 'Title:', 'epron-toolkit' ),
				'value' => $instance['title'],
				'id'    => $this->get_field_id( 'title' ),
				'name'  => $this->get_field_name( 'title' ),
				'desc'  => ''
			));

			// Source
			$RW->select_e( array(
				'label'   => esc_html__( 'Flickr source', 'epron-toolkit' ),
				'value'   => $instance['flickr_source'],
				'id'      => $this->get_field_id( 'flickr_source' ),
				'options' => array(
					array( 'value' => 'user', 'label' => 'User' ),
					array( 'value' => 'set', 'label' => 'Set' ), 
				),
				'name' => $this->get_field_name( 'flickr_source' ),
				'desc' => esc_html__( 'Select Flickr display source', 'epron-toolkit' )
			));

			// Display
			$RW->select_e( array(
				'label'   => esc_html__( 'Display order', 'epron-toolkit' ),
				'value'   => $instance['flickr_display'],
				'id'      => $this->get_field_id( 'flickr_display' ),
				'options' => array(
					array( 'value' => 'latest', 'label' => 'Latest' ),
					array( 'value' => 'random', 'label' => 'Random' ), 
				),
				'name' => $this->get_field_name( 'flickr_display' ),
				'desc' => esc_html__( 'Image order', 'epron-toolkit' )
			));

			// ID
			$RW->input_e( array(
				'label' => esc_html__( 'Flickr ID', 'epron-toolkit' ),
				'value' => $instance['flickr_id'],
				'id'    => $this->get_field_id( 'flickr_id' ),
				'name'  => $this->get_field_name( 'flickr_id' ),
				'desc'  => esc_html__( 'Displays photos from a user or group ', 'epron-toolkit' ) . '<a href="http://www.idgettr.com">' . esc_html__( 'Find your Flickr user or group id', 'epron-toolkit' ) . '</a>'
			));


			// Set
			$RW->input_e( array(
				'label' => esc_html__( 'Set', 'epron-toolkit' ),
				'value' => $instance['flickr_set'],
				'id'    => $this->get_field_id( 'flickr_set' ),
				'name'  => $this->get_field_name( 'flickr_set' ),
				'desc'  => esc_html__( 'Displays photos from a photo set. The set ID is at the end of the URL.', 'epron-toolkit' )
			));

			// Number o photos
			$RW->number_e( array(
				'label' => esc_html__( 'Limit', 'epron-toolkit' ),
				'value' => $instance['flickr_limit'],
				'id'    => $this->get_field_id( 'flickr_limit' ),
				'min'   => '1',
				'max'   => '100',
				'units' => esc_html__( 'images', 'epron-toolkit' ),
				'name'  => $this->get_field_name( 'flickr_limit' ),
				'desc'  => esc_html__( 'Number of thumbnails to show. Limit of 10 thumbnails.', 'epron-toolkit' )
			));

			

		$RW->tabEnd();

		// ----------------------- Tab 02
		$RW->tabStart(2);

			// Classes
			$RW->input_e( array(
				'label' => esc_html__( 'Classes', 'epron-toolkit' ),
				'value' => $instance['classes'],
				'id'    => $this->get_field_id( 'classes' ),
				'name'  => $this->get_field_name( 'classes' ),
				'desc'  => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'epron-toolkit' )
			));

		$RW->tabEnd();

		$RW->tabsEnd();

	}
	
}