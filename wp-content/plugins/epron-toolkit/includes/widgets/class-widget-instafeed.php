<?php

/**
 * Rascals Instafeed Widget
 *
 * Displays recent posts with images
 *
 * @author Rascals Themes
 * @category Widgets
 * @package EpronToolkit/Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */
 
class RascalsInstafeedWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	function __construct() {

		/* Widget settings */
		$widget_ops = array(
			'classname' => 'widget_rt_instafeed',
			'description' => esc_html__( 'Display recent images from Instagram account.', 'epron-toolkit' )
		);
		
		/* Create the widget */
		parent::__construct( 'rt-instafeed-widget', esc_html__( '[RT] Instagram', 'epron-toolkit' ), $widget_ops );
		
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
		$title                  = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title                  = apply_filters( 'widget_title', $title );
		$username               = ( isset( $instance['username'] ) ) ? sanitize_text_field( $instance['username'] ) : '';
		$access_token           = ( isset( $instance['access_token'] ) ) ? sanitize_text_field( $instance['access_token'] ) : '';
		$display_name           = ( isset( $instance['display_name'] ) ) ? sanitize_text_field( $instance['display_name'] ) : '';
		$display_header         = ( isset( $instance['display_header'] ) && $instance['display_header'] !== '' ) ? 'yes' : '';
		$display_follow_overlay = ( isset( $instance['display_follow_overlay'] ) ) ? sanitize_text_field( $instance['display_follow_overlay'] ) : '';
		$images_per_row         = ( isset( $instance['images_per_row'] ) ) ? sanitize_text_field( $instance['images_per_row'] ) : '3';
		$number_of_rows         = ( isset( $instance['number_of_rows'] ) ) ? sanitize_text_field( $instance['number_of_rows'] ) : '2';
		$image_gap              = ( isset( $instance['image_gap'] ) ) ? sanitize_text_field( $instance['image_gap'] ) : 'small-gap';
		$request_timeout        = ( isset( $instance['request_timeout'] ) ) ? sanitize_text_field( $instance['request_timeout'] ) : '2';
		$cache_time             = ( isset( $instance['cache_time'] ) ) ? sanitize_text_field( $instance['cache_time'] ) : '60';
		$classes                = ( isset( $instance['classes'] ) ) ? sanitize_text_field( $instance['classes'] ) : '';
		
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
		echo do_shortcode( '[kc_instafeed 
			username="' . $username . '"
			access_token="' . $access_token . '" 
			display_name="' . $display_name . '" 
			images_per_row="' . $images_per_row . '" 
			number_of_rows="' . $number_of_rows . '"
			display_header="' . $display_header . '" 
			display_follow_overlay="' . $display_follow_overlay . '" 
			image_gap="' . $image_gap . '" 
			request_timeout="' . $request_timeout . '" 
			cache_time="' . $cache_time . '" 
			color_scheme="' . $color_scheme . '" 
			classes="widget-size ' . $classes . ' 
			"]' );
		
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
		
		$instance                           = $old_instance;
		$instance['title']                  = sanitize_text_field( $new_instance['title'] );
		$instance['username']               = sanitize_text_field( $new_instance['username'] );
		$instance['access_token']           = sanitize_text_field( $new_instance['access_token'] );
		$instance['display_name']           = sanitize_text_field( $new_instance['display_name'] );
		$instance['display_header']         = sanitize_text_field( $new_instance['display_header'] );
		$instance['display_follow_overlay'] = sanitize_text_field( $new_instance['display_follow_overlay'] );
		$instance['images_per_row']         = sanitize_text_field( $new_instance['images_per_row'] );
		$instance['number_of_rows']         = sanitize_text_field( $new_instance['number_of_rows'] );
		$instance['image_gap']              = sanitize_text_field( $new_instance['image_gap'] );
		$instance['request_timeout']        = sanitize_text_field( $new_instance['request_timeout'] );
		$instance['cache_time']             = sanitize_text_field( $new_instance['cache_time'] );
		$instance['classes']                = sanitize_text_field( $new_instance['classes'] );
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
			'title'                  => esc_html__( 'Instagram Feed', 'epron-toolkit' ),  
			'username'               => '',
			'access_token'           => '',
			'display_name'           => '',
			'display_header'         => 'yes',
			'display_follow_overlay' => '',
			'images_per_row'         => '3',
			'number_of_rows'         => '2',
			'image_gap'              => 'small-gap',
			'request_timeout'        => '2',
			'cache_time'             => '60',
			'classes'                => '',
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

			// Username
			$RW->input_e( array(
				'label' => esc_html__( 'Username', 'epron-toolkit' ),
				'value' => $instance['username'],
				'id'    => $this->get_field_id( 'username' ),
				'name'  => $this->get_field_name( 'username' ),
				'desc'  => esc_html__( 'Enter the ID as it appears after the instagram url (ex. http://www.instagram.com/ID)', 'epron-toolkit' )
			));

			// ID
			$RW->input_e( array(
				'label' => esc_html__( 'Access Token', 'epron-toolkit' ),
				'value' => $instance['access_token'],
				'id'    => $this->get_field_id( 'access_token' ),
				'name'  => $this->get_field_name( 'access_token' ),
				'desc'  => esc_html__( 'You can get the Access token at http://instagram.pixelunion.net/', 'epron-toolkit' )
			));

			// Display Name
			$RW->input_e( array(
				'label' => esc_html__( 'Display Name', 'epron-toolkit' ),
				'value' => $instance['display_name'],
				'id'    => $this->get_field_id( 'display_name' ),
				'name'  => $this->get_field_name( 'display_name' ),
				'desc'  => esc_html__( 'Enter custom profile name instead of ID name.', 'epron-toolkit' )
			));

			// Header
			$RW->checkbox_e( array(
				'label' => esc_html__( 'Display header', 'epron-toolkit' ),
				'value' => $instance['display_header'],
				'id'    => $this->get_field_id( 'display_header' ),
				'name'  => $this->get_field_name( 'display_header' ),
				'desc'  => esc_html__( 'Display or hide the Instagram header section', 'epron-toolkit' )
			));

			// Display follow overlay
			$RW->checkbox_e( array(
				'label' => esc_html__( 'Display Follow Overlay', 'epron-toolkit' ),
				'value' => $instance['display_follow_overlay'],
				'id'    => $this->get_field_id( 'display_follow_overlay' ),
				'name'  => $this->get_field_name( 'display_follow_overlay' ),
				'desc'  => esc_html__( 'Display or hide the black overlay with profile link.', 'epron-toolkit' )
			));

			// Images per row
			$RW->number_e( array(
				'label' => esc_html__( 'Images per row', 'epron-toolkit' ),
				'value' => $instance['images_per_row'],
				'id'    => $this->get_field_id( 'images_per_row' ),
				'min'   => '1',
				'max'   => '5',
				'units' => esc_html__( 'images', 'epron-toolkit' ),
				'name'  => $this->get_field_name( 'images_per_row' ),
				'desc'  => esc_html__( 'Set the number of images displayed on each row (default is 3)', 'epron-toolkit' )
			));

			// Number of rows
			$RW->number_e( array(
				'label' => esc_html__( 'Number of rows', 'epron-toolkit' ),
				'value' => $instance['number_of_rows'],
				'id'    => $this->get_field_id( 'number_of_rows' ),
				'min'   => '1',
				'max'   => '4',
				'units' => esc_html__( 'rows', 'epron-toolkit' ),
				'name'  => $this->get_field_name( 'number_of_rows' ),
				'desc'  => esc_html__( 'Set on how many rows to display the images (default is 2)', 'epron-toolkit' )
			));

			// Image Gap
			$RW->select_e( array(
				'label'   => esc_html__( 'Image gap', 'epron-toolkit' ),
				'value'   => $instance['image_gap'],
				'id'      => $this->get_field_id( 'image_gap' ),
				'options' => array(
					array( 'value' => 'no-gap', 'label' => 'No Gap' ),
					array( 'value' => 'small-gap', 'label' => '2 px' ), 
				),
				'name' => $this->get_field_name( 'image_gap' ),
				'desc' => esc_html__( 'Set a gap between images (default: No gap)', 'epron-toolkit' )
			));

		$RW->tabEnd();

		// ----------------------- Tab 02
		$RW->tabStart(2);

			// Request Timeout
			$RW->number_e( array(
				'label' => esc_html__( 'Request timeout', 'epron-toolkit' ),
				'value' => $instance['request_timeout'],
				'id' => $this->get_field_id( 'request_timeout' ),
				'min' => '1',
				'max' => '5',
				'units' => esc_html__( 'minutes', 'epron-toolkit' ),
				'name' => $this->get_field_name( 'request_timeout' ),
				'desc' => esc_html__( 'Timeout for the instagram API request.', 'epron-toolkit' )
			));

			// Cache time
			$RW->number_e( array(
				'label' => esc_html__( 'Cache time', 'epron-toolkit' ),
				'value' => $instance['cache_time'],
				'id' => $this->get_field_id( 'cache_time' ),
				'min' => '5',
				'max' => '1000',
				'units' => esc_html__( 'minutes', 'epron-toolkit' ),
				'name' => $this->get_field_name( 'cache_time' ),
				'desc' => esc_html__( 'Time that data is stored in the database before re-downloading', 'epron-toolkit' )
			));

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