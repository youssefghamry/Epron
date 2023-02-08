<?php

/**
 * Rascals Recent Posts Widget
 *
 * Displays recent posts with images
 *
 * @author Rascals Themes
 * @category Widgets
 * @package EpronToolkit/Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */
 
class RascalsRecentPostsWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	function __construct() {

		/* Widget settings */
		$widget_ops = array(
			'classname' => 'widget_rt_recent_posts',
			'description' => esc_html__( 'Display recent posts containing featured image.', 'epron-toolkit' )
		);
		
		/* Create the widget */
		parent::__construct( 'rt-recent-posts-widget', esc_html__( '[RT] Recent Posts', 'epron-toolkit' ), $widget_ops );
		
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

		global $wp_query, $post;

		extract( $args );
		$title      = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title      = apply_filters( 'widget_title', $title );
		$limit      = ( isset( $instance['limit'] ) ) ? absint( $instance['limit'] ) : 5;
		$excerpt    = ( isset( $instance['excerpt'] ) ) ? sanitize_text_field( $instance['excerpt'] ) : '';
		$thumbnails = ( isset( $instance['thumbnails'] ) && $instance['thumbnails'] !== '' ) ? 'yes' : '';
		$classes    = ( isset( $instance['classes'] ) ) ? sanitize_text_field( $instance['classes'] ) : '';
		

		// Date format
    	$date_format = get_option( 'date_format' );
			
		echo wp_kses_post( $args['before_widget'] );

		// Title
		if ( ! empty( $title ) ) { 
			echo wp_kses_post( $before_title ) . esc_attr( $title) . wp_kses_post( $after_title );
		}
		
		// Color Scheme
		$color_scheme = get_theme_mod( 'color_scheme', 'dark' );
		
		// Render Module
		echo do_shortcode( "[kc_recent_posts limit='{$limit}' excerpt='{$excerpt}' thumbnails='{$thumbnails}' color_scheme='{$color_scheme}' classes='{$classes}']" );


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
		
		$instance               = $old_instance;
		$instance['title']      = sanitize_text_field( $new_instance['title'] );
		$instance['limit']      = absint( $new_instance['limit'] );
		$instance['excerpt']    = sanitize_text_field( $new_instance['excerpt'] );
		$instance['ratings']    = sanitize_text_field( $new_instance['ratings'] );
		$instance['thumbnails'] = sanitize_text_field( $new_instance['thumbnails'] );
		$instance['classes']    = sanitize_text_field( $new_instance['classes'] );
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
		global $wpdb;

		$defaults = array(
			'title'      => esc_html__( 'Recent Posts', 'epron-toolkit' ),  
			'limit'      => '6',
			'excerpt'    => '',
			'ratings'    => '',
			'thumbnails' => '',
			'classes'    => ''
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

			// Limit
			$RW->number_e( array(
				'label' => esc_html__( 'Limit', 'epron-toolkit' ),
				'value' => $instance['limit'],
				'id'    => $this->get_field_id( 'limit' ),
				'min'   => '1',
				'max'   => '100',
				'units' => esc_html__( 'posts', 'epron-toolkit' ),
				'name'  => $this->get_field_name( 'limit' ),
				'desc'  => esc_html__( 'Number of thumbnails to show. Limit of 10 thumbnails.', 'epron-toolkit' )
			));

			// Display thumbnails
			$RW->checkbox_e( array(
				'label' => esc_html__( 'Display thumbnails', 'epron-toolkit' ),
				'value' => $instance['excerpt'],
				'id'    => $this->get_field_id( 'thumbnails' ),
				'name'  => $this->get_field_name( 'thumbnails' ),
				'desc'  => ''
			));

			// Display Excerpt
			$RW->checkbox_e( array(
				'label' => esc_html__( 'Excerpt', 'epron-toolkit' ),
				'value' => $instance['excerpt'],
				'id'    => $this->get_field_id( 'excerpt' ),
				'name'  => $this->get_field_name( 'excerpt' ),
				'desc'  => esc_html__( 'Display short text (excerpt).', 'epron-toolkit' )
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