<?php
/**
 * Rascals Register Artists
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsRegisterArtists extends RascalsCPT {

	/**
	 * Instance of RascalsCPT 
	 * @var class
	 */
	private $cpt;

	/*
	Public variables
	 */
	public $post_name = 'wp_artists';
	public $icon = 'dashicons-groups';
	public $supports = array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields');

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
		add_action( 'init', array( $this, 'regsiterPostType' ), 0 );
	}

	/**
	 * Register Post Type
	 * @return void
	 */
	public function regsiterPostType() {

		// Init Toolkit
		$toolkit = epronToolkit();
		
		// Class arguments 
		$args = array( 
			'post_name' => $this->post_name, 
			'sortable' => true
		);

		// Post arguments
		$post_options = array(
			'labels' => array(
				'name'               => esc_html__( 'Artists', 'epron-toolkit' ),
				'singular_name'      => esc_html__( 'Artists', 'epron-toolkit' ),
				'add_new'            => esc_html__( 'Add New', 'epron-toolkit' ),
				'add_new_item'       => esc_html__( 'Add New Artist', 'epron-toolkit' ),
				'edit_item'          => esc_html__( 'Edit Artist', 'epron-toolkit' ),
				'new_item'           => esc_html__( 'New Artist', 'epron-toolkit' ),
				'view_item'          => esc_html__( 'View Artist', 'epron-toolkit' ),
				'search_items'       => esc_html__( 'Search', 'epron-toolkit' ),
				'not_found'          => esc_html__( 'No artists found', 'epron-toolkit' ),
				'not_found_in_trash' => esc_html__( 'No artists found in Trash', 'epron-toolkit' ), 
				'parent_item_colon'  => ''
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'rewrite'           => array(
				'slug'       => $toolkit->get_theme_option( 'artists_slug', 'artists' ),
				'with_front' => false
			),
			'supports'          => $this->supports,
			'menu_icon'         => $this->icon
		);

		// Register taxonomy
		register_taxonomy( 'wp_artists_cats', array($this->post_name), array(
			'hierarchical'   => true,
			'label'          => esc_html__( 'Filter 1', 'epron-toolkit' ),
			'singular_label' => esc_html__( 'Filter 1', 'epron-toolkit' ),
			'query_var'      => true,
			'show_in_rest'   => true,
			'rewrite'        => array(
				'slug'       => $toolkit->get_theme_option( 'artists_cat_slug', 'artist-category' ),
				'with_front' => false
			),
		));

		// Register taxonomy 2
		register_taxonomy( 'wp_artists_cats2', array($this->post_name), array(
			'hierarchical'   => true,
			'label'          => esc_html__( 'Filter 2', 'epron-toolkit' ),
			'singular_label' => esc_html__( 'Filter 2', 'epron-toolkit' ),
			'query_var'      => true,
			'show_in_rest'   => true,
			'rewrite'        => array(
				'slug'       => $toolkit->get_theme_option( 'artists_cat_slug2', 'artist-category-2' ),
				'with_front' => false
			),
		));


		// Add Class instance
		$this->cpt = new RascalsCPT( $args, $post_options );

		// Add columns filter
		$columns_filter_args = array(
			'post_name'    => $this->post_name,
			'filter_label' => esc_html__( 'Filter', 'epron-toolkit' ),
			'filters'      => array(
				'wp_artists_cats',
				'wp_artists_cats2',
				
			),
			'extra_cols'   => array(
				'cb'      => '<input type="checkbox" />',
				'title'   => esc_html__( 'Title', 'epron-toolkit' ),
				'date'    => esc_html__( 'Date', 'epron-toolkit' ),
				'preview' => esc_html__( 'Preview', 'epron-toolkit' ),
			)
		);

		$this->cpt->addAdminColumns( $columns_filter_args );
		
	}

}
