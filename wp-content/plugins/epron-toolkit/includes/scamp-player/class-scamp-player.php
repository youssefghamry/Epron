<?php
/**
 * Rascals Scamp Player
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsScampPlayer {

	/*
	Private variables
	 */
	private $tracks_filter_args = array();
	private $post_type          = null;

	/*
	Public variables
	 */
	public $prefix    = 'wp_';
	public $post_name = 'tracks';
	public $icon      = 'dashicons-format-audio';
	public $supports  = array('title', 'editor');


	/**
	 * Rascals CPT Constructor.
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize class
	 * @return void
	 */
	public function init() {

		// Set post type
		$this->post_type = $this->prefix . $this->post_name;

		// Regiter Post type
		add_action( 'init', array( $this, 'regsiterPostType' ), 0 );

		// Add options to post type columns
		$this->addTracksColumns();

		// Frontend Scripts
		add_action( 'wp_enqueue_scripts' , array( $this, 'scampPlayerEnqueue' ) );

	}


	/**
	 * Register Post Type
	 * @return void
	 */
	public function regsiterPostType() {

		// Class arguments 
	
		// Post arguments
		$post_options = array(
			'labels' => array(
				'name'               => esc_html__( 'Tracks Manager', 'epron-toolkit' ),
				'singular_name'      => esc_html__( 'Track', 'epron-toolkit' ),
				'add_new'            => esc_html__( 'Add New', 'epron-toolkit' ),
				'add_new_item'       => esc_html__( 'Add New Tracks', 'epron-toolkit' ),
				'edit_item'          => esc_html__( 'Edit Tracks', 'epron-toolkit' ),
				'new_item'           => esc_html__( 'New Tracks', 'epron-toolkit' ),
				'view_item'          => esc_html__( 'View Tracks', 'epron-toolkit' ),
				'search_items'       => esc_html__( 'Search', 'epron-toolkit' ),
				'not_found'          => esc_html__( 'No tracks found', 'epron-toolkit' ),
				'not_found_in_trash' => esc_html__( 'No tracks found in Trash', 'epron-toolkit' ), 
				'parent_item_colon'  => ''
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'rewrite'           => array(
				'slug'       => EpronToolkit::getInstance()->get_theme_option( 'tracks_slug', 'tracks' ),
				'with_front' => false
			),
			'supports'          => $this->supports,
			'menu_icon'         => $this->icon
		);

		$post_slug = $post_options['rewrite']['slug'];
		add_rewrite_rule('^'. $post_slug .'/page/([0-9]+)','index.php?pagename=artists&paged=$matches[1]', 'top');

		// Register Post Type 
		register_post_type( $this->post_type, $post_options );
		
	}


	/**
	 * Add extra columns to post table
	 * @param array $args
	 */
	public function addTracksColumns() {

		$this->tracks_filter_args = array(
			'post_name'    => $this->post_type,
			'extra_cols'   => array(
				'cb'            => '<input type="checkbox" />',
				'title'         => esc_html__( 'Title', 'epron-toolkit' ),
				'preview'       => esc_html__( 'Preview', 'epron-toolkit' ),
				'id'			=> esc_html__( 'Tracklist ID', 'epron-toolkit' ),
			)
		);
		
		add_filter( 'manage_edit-' . esc_attr( $this->tracks_filter_args['post_name'] ) . '_columns', array( $this, 'filterTracksAddColumns' ) );
		add_filter( 'manage_posts_custom_column', array( $this, 'filterTracksDisplayColumns' ) );
		
	}


	/**
	 * Defined fields for columns
	 * @param  array $columns
	 * @return array 
	 */
	public function filterTracksAddColumns( $columns ) {

		// Get extra columns options
		$cols    = $this->tracks_filter_args['extra_cols'];
		$filters = $this->tracks_filter_args['filters'];
		$prefix  = $this->tracks_filter_args['post_name'] . '_';
		
		// Add unique ID to table columns
		foreach ( $cols as $i => $k ) {

			// ID
			if ( $i === 'id' ) {
				$cols[$prefix . 'id'] =  $cols['id'];
				unset( $cols['id'] );
			}

			// Preview
			if ( $i === 'preview' ) {
				$cols[$prefix . 'preview'] = $cols['preview'];
				unset( $cols['preview'] );
			}

		}


		// Merge extra columns
		$columns = array_merge( $columns, $cols );

		return $columns;
	}

	/**
	 * Display content in extra columns
	 * @param  array $column
	 * @return array
	 */
	public function filterTracksDisplayColumns( $column ) {

		global $post;

		$prefix  = $this->tracks_filter_args['post_name'] . '_';
		$cols    = $this->tracks_filter_args['extra_cols'];
		
		// Extra Cols
		foreach ( $cols as $i => $k ) {

			/* ID */
			if ( $i === 'id' && $column === $prefix . 'id' ) {
				echo esc_attr( $post->ID );
			}

			/* Preview */
			if ( $i === 'preview' && $column === $prefix . 'preview' ) {
				if ( has_post_thumbnail( $post->ID ) ) {
					the_post_thumbnail( array( 60, 60 ) );
				}
			}

		
		}
	}


	/**
	 * Enqueue all frontend scripts and styles 
	 * @return void
	 */
	public function scampPlayerEnqueue() {

		$plugin_url = RASCALS_TOOLKIT_URL;
		wp_enqueue_script( 'soundmanager2' , esc_url( $plugin_url ) . '/assets/vendors/soundmanager2-nodebug-jsmin.js' , false, false, true );
		wp_enqueue_script( 'scamp-player-lite' , esc_url( $plugin_url ) . '/assets/js/jquery.scamp.player.lite.min.js' , false, false, true );
	}


	/**
	 * Get tracklist in array
	 * @param  integer $audio_post_id 
	 * @return array               
	 */
	public function getTracklist( $audio_post, $custom_ids = '' ) {

		// Get IDS
		if ( $custom_ids !== '' ) {
			$audio_ids = $custom_ids;
		} else {
			$audio_ids = get_post_meta( $audio_post, '_audio_tracks', true );
		}

		if ( ! $audio_ids || $audio_ids === '' ) {
			 return false;
		}

		$count = 0;
		$ids = explode( '|', $audio_ids );
		$defaults = array(
			'id'               => 0,
			'custom'           => false,
			'custom_url'       => false,
			'title'            => '',
			'artists'          => false,
			'buttons'          => false,
			'cover'            => false,
			'cover_full'       => false,
			'release_url'      => '',
			'release_target'   => '', 
			'artists'          => '',
			'artists_url'      => '',
			'artists_target'   => '',
			'cart_url'         => '',
			'cart_target'      => '',
			'free_download'    => 'no',
			'track_length'     => '',
			'lyrics'           => '',
			'disable_playback' => 'no',
			'waveform'         => '',
			'volume'           => '100',
			'desc'             => false,
		);

		$tracklist = array();

		/* Start Loop */
		foreach ( $ids as $id ) {

			// Vars 
			$title = '';
			$subtitle = '';

			// Get image meta 
			$track = get_post_meta( $audio_post, '_audio_tracks_' . $id, true );

			// Add default values 
			if ( isset( $track ) && is_array( $track ) ) {
				$track = array_merge( $defaults, $track );
			} else {
				$track = $defaults;
			}

			// ID 
			$track['id'] = $id;

			// Custom cover 
			if ( $track['cover'] ) {

				// If from Media Libary
				if ( is_numeric( $track['cover'] ) ) {
					$image_cover = wp_get_attachment_image_src( $track['cover'], 'thumbnail' );
					$image_cover = $image_cover[0];
					$image_cover_full = wp_get_attachment_image_src( $track['cover'], 'epron-release-thumb' );
					$image_cover_full = $image_cover_full[0];
					if ( $image_cover ) {
						$track['cover'] =  $image_cover;
						$track['cover_full'] =  $image_cover_full;
					} else {
						$track['cover'] = false;
					}
				} else {
					$track['cover_full'] = $track['cover'];
				}

			}

			/* Waveform */
			if ( $track['waveform'] ) {

				$image_waveform = wp_get_attachment_image_src( $track['waveform'], 'full' );
				$image_waveform = $image_waveform[0];
			
				if ( $image_waveform ) {
					$track['waveform'] = $image_waveform;
				} else {
					$track['waveform'] = false;
				}
			}

			// Check if track is custom 
		   	if ( wp_get_attachment_url( $id ) ) {
		      	$track_att = get_post( $id );
		      	$track['url'] = wp_get_attachment_url( $id );
		      	if ( $track['title'] === '' ) {
		      		$track['title'] = $track_att->post_title;
		      	}
		    } else {
				$track['url'] = $track['custom_url'];
				if ( $track['url'] === '' ) {
					continue;
				}
				if ( $track['title'] === '' ) {
					$track['title'] = esc_html__( 'Custom Title', 'epron-toolkit' );
				}
				$track['custom'] = true;
		    }
    
		    array_push( $tracklist, $track );
		}
		
		return $tracklist;
	
	}


}