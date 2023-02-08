<?php

/**
 * Rascals Twitter Widget
 *
 * Displays recent posts from Twitter
 *
 * @author Rascals Themes
 * @category Widgets
 * @package EpronToolkit/Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */
class RascalsTwitterWidget extends WP_Widget {


	/**
	 * Constructor
	 */
	function __construct() {

		/* Widget settings */
		$widget_ops = array(
			'classname' => 'widget_rt_twitter',
			'description' => esc_html__( 'Display latest images from Twitter', 'epron-toolkit' )
		);
		
		/* Create the widget */ 
		parent::__construct( 'rt-twiter-widget', esc_html__( '[RT] Twitter', 'epron-toolkit' ), $widget_ops );
		
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
		$title      = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title      = apply_filters( 'widget_title', $title );
		$username   = sanitize_text_field( $instance['username'] );
		$limit      = ( isset( $instance['limit'] ) ) ? absint(  $instance['limit'] ) : 3;
		$replies    = ( isset( $instance['replies'] ) ) ? sanitize_text_field( $instance['replies'] ) : '';
		$api_key    = ( isset( $instance['api_key'] ) ) ? sanitize_text_field( $instance['api_key'] ) : '';
		$api_secret = ( isset( $instance['api_secret'] ) ) ? sanitize_text_field( $instance['api_secret'] ) : '';
		$classes    = ( isset( $instance['classes'] ) ) ? sanitize_text_field( $instance['classes'] ) : '';
		
		echo wp_kses_post( $before_widget );
		
		// Title
		if ( ! empty( $title ) ) { 
			echo wp_kses_post( $before_title ) . esc_attr( $title) . wp_kses_post( $after_title );
		}
	
		$twitter_args = array(
			'username'   => $username,
			'limit'      => $limit,
			'replies'    => $replies,
			'api_key'    => $api_key,
			'api_secret' => $api_secret
		);
		$tweets = new RascalsTwitter( $twitter_args );

		$tweets_a = $tweets->showTweets();
		 echo '<ul class="tweets-widget ' . esc_attr( $classes ) . '">';
            foreach ( $tweets_a as $key => $tweet ) {
                echo '<li>' . wp_kses_post( $tweet['text'] ) . '<span class="date">' . wp_kses_post( $tweet['date'] ) . '</span></li>';  
                if ( $key === $limit ) {
                    break;
                }
            }

            echo '</ul>';

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
		$instance['username']   = sanitize_text_field( $new_instance['username'] );
		$instance['api_key']    = sanitize_text_field( $new_instance['api_key'] );
		$instance['api_secret'] = sanitize_text_field( $new_instance['api_secret'] );
		$instance['limit']      = absint( $new_instance['limit'] );
		$instance['replies']    = sanitize_text_field( $new_instance['replies'] );
		$instance['classes']    = sanitize_text_field( $new_instance[ 'classes'] );
		
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
			'title'      => esc_html__( 'Twitter', 'epron-toolkit' ), 
			'username'   => '', 
			'api_key'    => '', 
			'api_secret' => '', 
			'limit'      => '3', 
			'replies'    => 'true',
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

			// Username
			$RW->input_e( array(
				'label' => esc_html__( 'Username', 'epron-toolkit' ),
				'value' => $instance['username'],
				'id'    => $this->get_field_id( 'username' ),
				'name'  => $this->get_field_name( 'username' ),
				'desc'  => ''
			));

			// API Key
			$RW->input_e( array(
				'label' => esc_html__( 'API Key', 'epron-toolkit' ),
				'value' => $instance['api_key'],
				'id'    => $this->get_field_id( 'api_key' ),
				'name'  => $this->get_field_name( 'api_key' ),
				'desc'  => ''
			));

			// API Secret
			$RW->input_e( array(
				'label' => esc_html__( 'API Secret', 'epron-toolkit' ),
				'value' => $instance['api_secret'],
				'id'    => $this->get_field_id( 'api_secret' ),
				'name'  => $this->get_field_name( 'api_secret' ),
				'desc'  => ''
			));

			// Number of tweets to show
			$RW->number_e( array(
				'label' => esc_html__( 'Number of tweets to show:', 'epron-toolkit' ),
				'value' => $instance['limit'],
				'id'    => $this->get_field_id( 'limit' ),
				'min'   => '1',
				'max'   => '20',
				'units' => esc_html__( 'tweets', 'epron-toolkit' ),
				'name'  => $this->get_field_name( 'limit' ),
				'desc'  => esc_html__( 'Number of thumbnails to show. Limit of 10 thumbnails.', 'epron-toolkit' )
			));

		$RW->tabEnd();

		// ----------------------- Tab 02
		$RW->tabStart(2);

			// Replies
			$RW->checkbox_e( array(
				'label' => esc_html__( 'Replies', 'epron-toolkit' ),
				'value' => $instance['replies'],
				'id'    => $this->get_field_id( 'replies' ),
				'name'  => $this->get_field_name( 'replies' ),
				'desc'  => esc_html__( 'Display replies.', 'epron-toolkit' ),
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