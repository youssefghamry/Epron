<?php

/**
 *
 * Contains the main functions to add and manage Custom Posts Types
 *
 * @class RascalsCPT
 *
 * @package         EpronToolkit
 * @author          Rascals Themes
 * @copyright       Rascals Themes
 * @version       	1.0.1
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RascalsCPT {
	
	private $args;
	private $options;
	private $columns_filter_args;
	
	/**
	 * Rascals CPT Constructor.
	 * @return void
	 */
	public function __construct( $args, $options ) {

		// Set options and page variables
		$this->args = $args;
		$this->options = $options;

		// Register Post Type
		$this->registerPostType();

		// Admin
		if ( is_admin() ) {
			
			// Init hooks
			$this->initHooks();
		}

	}

	/**
	 * Register Custom Post
	 * @return void
	 */
	public function registerPostType() {

		// Register Post Type 
		register_post_type( $this->args['post_name'], $this->options );
	}

	/**
	 * Hook into actions and filters
	 * @return void
	 */
	public function initHooks() {

		/* Add ajax sortable function */
		if ( $this->args['sortable'] ) {
			
			add_action( 'wp_ajax_' . $this->args['post_name'], array( $this, 'saveOrder') );
			
			/* Call method to create the sidebar menu items */
			add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
			
			/* Set admin order */
			add_filter( 'pre_get_posts', array( $this, 'setAdminOrder' ) );
			
		}
	}

	/**
	 * Create the sidebar menu
	 * @return void
	 */
	public function addAdminMenu() {	
		$panel_sub_page = add_submenu_page( 'edit.php?post_type=' . esc_attr( $this->args['post_name'] ), esc_attr( $this->args['post_name'] ) . '_sort', esc_html__( 'Sort Items', 'epron-toolkit' ), 'moderate_comments', $this->args['post_name'], array( $this, 'displaySortablePage'));

		// Load CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'registerStyles' ) );

		// Load the JS conditionally
        add_action( 'load-' . esc_attr( $panel_sub_page ), array( $this, 'registerScripts' ) );
	}
	
	/**
	 * Register scripts
	 * @return void
	 */
	public function registerScripts() {

		/* Custom Post */
		wp_enqueue_script( 'rascals-cpt', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/js/admin-custom-post.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-ui-draggable'), false, true );
		wp_localize_script( 'rascals-cpt-js', 'ajax_action', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce( 'ajax-nonce') ) );

		/* Touch punch */
		wp_enqueue_script( 'jquery-ui-touch-punch', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/vendors/jquery.ui.touch-punch.min.js', false, false, true );
	
	}

	/**
	 * Register styles only on sortable page
	 * @return void
	 */
	public function registerStyles() {

		wp_enqueue_style( 'sortable_custom_post_css', esc_url( RASCALS_TOOLKIT_URL ) . '/assets/css/admin-custom-post.css' );
	}
	
	
	/**
	 * Save Order by Ajax
	 * @return void
	 */
	public function saveOrder() {
		global $wpdb;
	 
		$order = explode( ',', $_POST['order'] );
		$counter = 1;
	 
		foreach ( $order as $value ) {
			$wpdb->update( $wpdb->posts, array('menu_order' => $counter), array('ID' => $value) );
			$counter++;
		}
		die(1);
		return;
	}
	
	/**
	 * Set admin order
	 * @param $wp_query
	 * @return void
	 */
	public function setAdminOrder( $wp_query ) {
		
		if ( is_admin() ) {
			global $pagenow;
			if ( ( $pagenow === 'edit.php' ) || (get_post_type() === 'edit') ) {

				if ( isset( $wp_query->query['post_type'] ) ) {
					
					/* Get the post type from the query */
					$post_type = $wp_query->query['post_type'];
			
					if ( $post_type === $this->args['post_name'] ) {
			
						/* 'orderby' value can be any column name */
						$wp_query->set( 'orderby', 'menu_order' );

						/* 'order' value can be ASC or DESC */
						$wp_query->set( 'order', 'ASC' );
					  
					}
				}
			}
		}
	}


	/**
	 * Display sortable options page
	 * @return html string
	 */
	public function displaySortablePage() {
		
		$sort_query = new WP_Query( 'post_type=' . esc_attr( $this->args['post_name'] ) . '&posts_per_page=-1&orderby=menu_order&order=ASC' );
		
		echo '<div class="wrap-sortable-post">';
		echo '<h3>' . esc_html__( 'Sort Items', 'epron-toolkit' ) . ' <img src="' . esc_url( site_url() ) . '/wp-admin/images/loading.gif" id="loading-animation" alt="' . esc_attr( $this->args['post_name'] ) . '"/></h3>';

		echo '<ul id="sortable-posts">';
		while ( $sort_query->have_posts() ){ 

			$sort_query->the_post();
			echo '<li id="' . esc_attr( get_the_ID() ) . '">';
			echo '<span class="drag-item"></span>';
			echo '<a href="' . esc_url( home_url() ) . '/wp-admin/post.php?action=edit&post=' . esc_attr( get_the_ID() ) . '" class="edit-item" title="' . esc_html__( 'Edit This Post', 'epron-toolkit' ) . '"></a>';
			echo '<div class="sortable-content">';
			if ( has_post_thumbnail( get_the_ID() ) ) {
				echo '<div class="preview-thumb">';
				the_post_thumbnail( array( 60, 60 ) );
				echo '</div>';
			}
			echo '<h6>' . esc_html( get_the_title() ) . ' <span>[id: ' . esc_attr( get_the_ID() ) . ']</span></h6>';
			echo '</div>';
			echo '</li>';

		}
		
		echo '</ul>';
		echo '</div>';
	}
	
	/**
	 * Add extra columns to post table
	 * @param array $args
	 * @version 1.2
	 */
	public function addAdminColumns( $args = null ) {

		if ( $args !== null && is_admin() ) {
			$this->columns_filter_args = $args;
		}
		
		if ( isset( $this->columns_filter_args ) ) {

			add_filter( 'manage_edit-' . esc_attr( $this->columns_filter_args['post_name'] ) . '_columns', array( $this, 'filterAddColumns' ) );
			add_filter( 'manage_posts_custom_column', array( $this, 'filterDisplayColumns' ) );
			
			if ( isset($this->columns_filter_args['filters'] ) ) {
				$filters = $this->columns_filter_args['filters'];
				foreach ( $filters as $nr => $filter ) {
					$nr++;
					add_action('restrict_manage_posts', function() use ( $filter, $nr ) { 
			           $this->restrictManagePosts( $this->columns_filter_args['post_name'], 'top', $filter, $nr ); 
			       	});
		       	}
	       	}
		}
		
	}


	/**
	 * Defined fields for columns
	 * @param  array $columns
	 * @return array 
	 */
	public function filterAddColumns( $columns ) {

		// Get extra columns options
		$cols    = $this->columns_filter_args['extra_cols'];
		$filters = $this->columns_filter_args['filters'];
		$prefix  = $this->columns_filter_args['post_name'] . '_';
		
		// Add unique ID to table columns
		foreach ( $cols as $i => $k ) {

			// Preview
			if ( $i === 'preview' ) {
				$cols[$prefix . 'preview'] = $cols['preview'];
				unset( $cols['preview'] );
			}

			// ID
			if ( $i === 'id' ) {
				$cols[$prefix . 'id'] =  $cols['id'];
				unset( $cols['id'] );
			}

		}

		// Filters
		if ( isset( $this->columns_filter_args['filters'] ) ) {
			$filters = $this->columns_filter_args['filters'];
			foreach ( $filters as $k => $t) {
				$k++;
				$cols[$t] = $this->columns_filter_args['filter_label'] . ' ' . $k;
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
	public function filterDisplayColumns( $column ) {

		global $post;

		$prefix  = $this->columns_filter_args['post_name'] . '_';
		$cols    = $this->columns_filter_args['extra_cols'];
		
		// Extra Cols
		foreach ( $cols as $i => $k ) {

			/* Preview */
			if ( $i === 'preview' && $column === $prefix . 'preview' ) {
				if ( has_post_thumbnail( $post->ID ) ) {
					the_post_thumbnail( array( 60, 60 ) );
				}
			}

			/* ID */
			if ( $i === 'id' && $column === $prefix . 'id' ) {
				echo esc_html( $post->ID );
			}


		}

		// Filters
		if ( isset( $this->columns_filter_args['filters'] ) ) {
			$filters = $this->columns_filter_args['filters'];
			foreach ( $filters as $k => $t) {
				if ( $column ===  $t ) {

					$cats = get_the_terms( $post->ID, $t );
					if ($cats) {
						$cats_a = array();
						foreach( $cats as $taxonomy ) {
							$cats_a[] = $taxonomy->name; 
						}
						echo implode( ', ', $cats_a );

					}
				}
			}
		}
	}

	/**
	 * Add filter to posts table
	 * @return void
	 */
	public function restrictManagePosts($post_type, $location, $filter, $nr) {

		global $typenow;

		if ( $typenow === $this->columns_filter_args['post_name'] ) {
			$filter_args = array( 'name' => $filter );
			$filters     = get_taxonomies( $filter_args );
			
			foreach ( $filters as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				$tax_name = $tax_obj->labels->name;
				
				echo '<select name="' . esc_attr( $tax_slug ) . '" id="' . esc_attr( $tax_slug ) . '" class="postform">';
				echo '<option value="">' . $this->columns_filter_args['filter_label'] . ' ' . esc_attr( $nr ) . '</option>';
				$this->generateTxonomyOptions( $tax_slug, 0, 0 );
				echo "</select>";
			}
		}
	}
	
	/**
	 * Generate taxonomy options
	 * @param  string  $tax_slug 
	 * @param  string  $parent 
	 * @param  integer $level
	 * @return string      
	 */
	public function generateTxonomyOptions( $tax_slug = null, $parent = '', $level = 0 ) {
	    $args = array( 'show_empty' => 1 );
	    if ( ! is_null( $parent ) ) {
	        $args = array( 'parent' => $parent );
	    } 
	    $terms = get_terms( $tax_slug, $args );
	    $tab = '';
	    for ( $i = 0; $i < $level; $i++ ) {
	        $tab .= '--';
	    }
	    foreach ( $terms as $term ) {
	        echo '<option value='. esc_attr( $term->slug ), isset($_GET[$tax_slug]) && $_GET[$tax_slug] === $term->slug ? ' selected="selected"' : '','>' . esc_html( $tab ) . esc_html( $term->name ) .' (' . esc_html( $term->count ) . ')</option>';
	        $this->generateTxonomyOptions( $tax_slug, $term->term_id, $level+1 );
	    }
	}
}