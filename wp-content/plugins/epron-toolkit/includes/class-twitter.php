<?php
/**
 * Rascals Twitter
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsTwitter {


	/**
	 * Options
	 */
	private $options = null;

	/**
	 * Default options
	 */
	private $defaults = array(
        'time'       => 30,
        'username'   => '',
        'replies'    => 'no',
        'api_key'    => '',
        'api_secret' => ''
    );

	/**
	 * Rascals Twitter Constructor.
	 * $options array
	 * @return void
	 */
	public function __construct( $options ) {

		// Make options if are doesn't exists
		if ( isset( $options ) && is_array( $options ) ) {
	        $this->options = array_merge( $this->defaults, $options );
	    } else { 
	        $this->options = $this->defaults;
	    }

	}


	/**
	 * Show Tweets
	 * @return string|array
	 */
	public function showTweets() {

		$options = $this->options;

		// Extract $options
	    extract( $options, EXTR_PREFIX_SAME, "twitter" );
		
		// Errors
	    $errors = '';

	    if ( empty( $api_key ) ) $errors = esc_html__( 'ERROR: Missing API Key.', 'epron-toolkit' );
	    if ( empty( $api_secret ) ) $errors = esc_html__( 'ERROR: Missing API Secret.', 'epron-toolkit' );
	    if ( empty( $username ) ) $errors = esc_html__( 'ERROR: Missing Twitter Feed User Name.', 'epron-toolkit' );
	    if ( $errors ) {
	        return '<p class="error">ERROR: ' . esc_html( $errors ) . '</p>';
	    }

	    // Replies
	    if ( $replies === 'yes' ) {
	        $replies = '0';
	    } else {
	        $replies = '1';
	    }

		// Vars
		$trans_name = 'rascals_tweets_' . esc_attr( $username );
		$token      = '';
		$count      = 1;
		$output     = '';

	    // delete_transient( $trans_name );

	    /* Shelude feed */
	    if ( false === ( $tweet_task = get_transient( $trans_name ) ) ) {

	        $bearer_token_credential = $api_key . ':' . $api_secret;
	        $credentials = base64_encode( $bearer_token_credential );
	        
	        $args = array(
				'method'      => 'POST',
				'httpversion' => '1.1',
				'blocking'    => true,
				'headers'     => array( 
					'Authorization' => 'Basic ' . $credentials,
					'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8'
	            ),
	            'body' => array( 'grant_type' => 'client_credentials' )
	        );

	        add_filter( 'https_ssl_verify', '__return_false' );

	        $response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args );
	        if ( is_wp_error( $response ) ) {
	            $keys = false;
	        } else {
	            $keys = json_decode( $response['body'] );
	        }
	        
	        if ( isset( $keys ) && ! isset( $keys->errors ) ) {
	          
	            $token = $keys->{'access_token'};

	            $args = array(
	                'httpversion' => '1.1',
	                'blocking' => true,
	                'headers' => array( 
	                    'Authorization' => "Bearer $token"
	                )
	            );
	            add_filter('https_ssl_verify', '__return_false');
	            $api_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username&count=20&exclude_replies=$replies&include_rts=0";

	            $response = wp_remote_get( $api_url, $args );

	            set_transient( $trans_name, $response['body'], 60 * $time );

	        } else {
	            delete_transient( $trans_name );
	             return '<p class="error">' . esc_html__( 'ERROR: Username not exists or Twitter API error.', 'epron-toolkit' ) . '</p>';     
	        }
	        
	    } 

	    $json = json_decode( get_transient( $trans_name ) );

	    if ( ! empty( $json ) ){

	        /* If feed has error */
	        if ( isset( $json->errors ) ) {
	            $errors = '';

	            foreach ( $json->errors as $error ) {
	                $errors .= '<p class="error">ERROR: ' . esc_html( $error->code ) . ': ' . esc_html( $error->message ) . '</p>';
	            }

	            // Delete transient
	            delete_transient( $trans_name );
	            return $errors;
	        }

	        $tweets_a = array();
	        foreach ( $json as $tweet ) {
	            $datetime = $tweet->created_at;
	            $date = date('F j, Y, g:i a', strtotime( $datetime));
	            $time = date('g:ia', strtotime( $datetime ) );
	            $date = human_time_diff( strtotime( $date ), current_time( 'timestamp', 1 ) );
	            $tweet_text = $tweet->text;
	            
	            // check if any entites exist and if so, replace then with hyperlinked versions
	            $tweet_text = preg_replace('/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="http://$1" target="_blank">http://$1</a>&nbsp;', $tweet_text);
	            $tweet_text = preg_replace('/https:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="https://$1" target="_blank">https://$1</a>&nbsp;', $tweet_text);

	            // convert @ to follow
	            $tweet_text = preg_replace("/(@([_a-z0-9\-]+))/i","<a href=\"http://twitter.com/$2\" title=\"Follow $2\" >$1</a>",$tweet_text);

	            // convert # to search
	            $tweet_text = preg_replace("/(#([_a-z0-9\-]+))/i","<a href=\"https://twitter.com/search?q=%23$2&amp;src=hash\" title=\"Search $1\" >$1</a>",$tweet_text);

	            $tweets_a[$count]['text'] = $tweet_text;
	            $tweets_a[$count]['date'] = '<a href="https://twitter.com/' . esc_attr( $username ) . '/statuses/' . esc_attr( $tweet->id_str ) . '">' . esc_html( $date ) . ' ' . esc_html__( 'ago', 'epron-toolkit') . '</a>';
	            
	            $count++;
	                
	        }
	          
	        return $tweets_a;
	    } else {
	        return '<p class="error">' . esc_html__( 'ERROR: Username not exists or Twitter API error.', 'epron-toolkit' ) . '</p>';
	        delete_transient( $trans_name );
	    }
	}


	/**
	 * Display escaped text.
	 * @param  $text
	 * @return string
	 */
	public function esc( $text ) {
		$text = preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $text );
		return $text;
	}


	/**
	 * Display escaped text through echo function.
	 * @param  $text
	 * @return string
	 */
	public function e_esc( $text ) {
		echo preg_replace( array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('',''), $text );
	}
}