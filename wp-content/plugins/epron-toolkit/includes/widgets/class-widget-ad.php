<?php

/**
 * Rascals Advertisement Widget
 *
 * Displays recent posts with images
 *
 * @author Rascals Themes
 * @category Widgets
 * @package EpronToolkit/Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */
 
class RascalsADWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	function __construct() {

		/* Widget settings */
		$widget_ops = array(
			'classname' => 'widget_rt_a_d',
			'description' => esc_html__( 'Display advertisement in sidebar.', 'epron-toolkit' )
		);
		
		/* Create the widget */
		parent::__construct( 'rt-adspot-widget', esc_html__( '[RT] Advertisement Spot', 'epron-toolkit' ), $widget_ops );
		
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
		$title            = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title            = apply_filters( 'widget_title', $title );
		$adspot           = ( isset( $instance['adspot'] ) ) ? sanitize_text_field( $instance['adspot'] ) : '';
		$display_ad_title = ( isset( $instance['display_ad_title'] ) && $instance['display_ad_title'] !== '' ) ? 'yes' : '';
		$classes          = ( isset( $instance['classes'] ) ) ? sanitize_text_field( $instance['classes'] ) : '';
		
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
		echo do_shortcode( "[kc_adspot adspot='{$adspot}' display_ad_title='{$display_ad_title}' classes='{$classes}']" );
		
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
		
		$instance                     = $old_instance;
		$instance['title']            = sanitize_text_field( $new_instance['title'] );
		$instance['display_ad_title'] = sanitize_text_field( $new_instance['display_ad_title'] );
		$instance['adspot']           = sanitize_text_field( $new_instance['adspot'] );
		$instance['classes']          = sanitize_text_field( $new_instance['classes'] );
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
			'title'            => esc_html__( 'AD', 'epron-toolkit' ),  
			'display_ad_title' => '',
			'adspot'           => '',
			'classes'          => '',
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

			// Display AD Title
			$RW->checkbox_e( array(
				'label' => esc_html__( 'Display AD Title', 'epron-toolkit' ),
				'value' => $instance[ 'display_ad_title' ],
				'id'    => $this->get_field_id( 'display_ad_title' ),
				'name'  => $this->get_field_name( 'display_ad_title' ),
				'desc'  => esc_html__( 'Display small title by default is: - Advertisement -. Note: Text can be replaced by Translate plugin like Loco Translate.', 'epron-toolkit' ),
			));

			// AD Spot
			$RW->select_e( array(
				'label'   => esc_html__( 'Use adspot from:', 'epron-toolkit' ),
				'value'   => $instance[ 'adspot' ],
				'id'      => $this->get_field_id( 'adspot' ),
				'options' => array(
					array( 'value' => '', 'label' => '- Select -' ), 
					array( 'value' => 'sidebar', 'label' => 'Sidebar' ), 
					array( 'value' => 'header', 'label' => 'Header' ),
					array( 'value' => 'footer', 'label' => 'Footer' ),
					array( 'value' => 'article_top', 'label' => 'Article Top' ),
					array( 'value' => 'article_bottom', 'label' => 'Article Bottom' ),
					array( 'value' => 'article_bottom', 'label' => 'Article Bottom' ),
					array( 'value' => 'tracklist', 'label' => 'Tracklist Inline' ),
					array( 'value' => 'custom1', 'label' => 'Custom 1' ),
					array( 'value' => 'custom2', 'label' => 'Custom 2' ),
					array( 'value' => 'custom3', 'label' => 'Custom 3' ),
				),
				'name' => $this->get_field_name( 'adspot' ),
				'desc' => esc_html__( 'Choose the adspot from list', 'epron-toolkit' )
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