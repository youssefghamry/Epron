<?php
/**
 * Rascals Events Manager
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsEventsManager {

	/*
	Private variables
	 */
	private $current_date       = array();
	private $events_filter_args = array();
	private $post_type          = null;

	/*
	Public variables
	 */
	public $prefix          = 'wp_';
	public $post_name       = 'events_manager';
	public $icon            = 'dashicons-calendar';
	public $supports        = array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields');
	public $show_event_type = false;
	public $shelude_time    = 60*60;
	public $clear_timer     = false;
	public $time_zone       = 'local_time';

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
		$this->addEventsColumns();

		// Set event date format
		$this->setDate();

		// Insert event type taxonomy
		$this->insertEventTypeTaxonomy();

		// Shelude Events
		add_action( 'init', array( $this, 'sheludeEvents' ) );

		// Save actions
		add_action( 'wp_insert_post', array( $this, 'saveEvents' ), 0 );

		// Events order
		add_filter( 'pre_get_posts', array( $this, 'setEventsOrder' ), 0 );
	}


	/**
	 * Register Post Type
	 * @return void
	 */
	public function regsiterPostType() {

		// Init Toolkit
		$toolkit = epronToolkit();
		
		// Class arguments 
	
		// Post arguments
		$post_options = array(
			'labels' => array(
				'name'               => esc_html__( 'Events', 'epron-toolkit' ),
				'singular_name'      => esc_html__( 'Events', 'epron-toolkit' ),
				'add_new'            => esc_html__( 'Add New', 'epron-toolkit' ),
				'add_new_item'       => esc_html__( 'Add New Events Item', 'epron-toolkit' ),
				'edit_item'          => esc_html__( 'Edit Events Item', 'epron-toolkit' ),
				'new_item'           => esc_html__( 'New Events Item', 'epron-toolkit' ),
				'view_item'          => esc_html__( 'View Events Item', 'epron-toolkit' ),
				'search_items'       => esc_html__( 'Search Items', 'epron-toolkit' ),
				'not_found'          => esc_html__( 'No events found', 'epron-toolkit' ),
				'not_found_in_trash' => esc_html__( 'No events found in Trash', 'epron-toolkit' ), 
				'parent_item_colon'  => ''
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'capability_type'   => 'post',
			'hierarchical'      => false,
			'rewrite'           => array(
				'slug'       => $toolkit->get_theme_option( 'events_slug', 'event' ),
				'with_front' => false
			),
			'supports'          => $this->supports,
			'menu_icon'         => $this->icon
		);

		// Register hidden taxonomy
		$event_type_options = array(
			'hierarchical'   => true,
			'label'          => esc_html__( 'Event Type', 'epron-toolkit' ),
			'singular_label' => esc_html__( 'Event Type', 'epron-toolkit' ),
			'query_var'      => true,
			'rewrite'        => 'event-type'
		);

		$event_type_hidden_options = array(
			'capabilities'   => array(
				'manage_terms' => 'manage_divisions',
				'edit_terms'   => 'edit_divisions',
				'delete_terms' => 'delete_divisions',
				'assign_terms' => 'edit_posts'
			),
			'show_ui'           => false,
			'show_in_nav_menus' => false
		);

		// Merge extra options
		if ( $this->show_event_type === false ) {
			$event_type_options = array_merge( $event_type_options, $event_type_hidden_options);
		}

		register_taxonomy( $this->prefix . 'event_type', array( $this->post_type ), $event_type_options );

		// Register taxonomy
		register_taxonomy( $this->prefix . 'event_categories', array( $this->post_type ), array(
			'hierarchical'   => true,
			'label'          => esc_html__( 'Filter 1', 'epron-toolkit' ),
			'singular_label' => esc_html__( 'Filter 1', 'epron-toolkit' ),
			'query_var'      => true,
			'rewrite'        => array(
				'slug'       => $toolkit->get_theme_option( 'events_cat_slug', 'event-category' ),
				'with_front' => false
			),
		));

		// Register taxonomy
		register_taxonomy( $this->prefix . 'event_categories2', array( $this->post_type ), array(
			'hierarchical'   => true,
			'label'          => esc_html__( 'Filter 2', 'epron-toolkit' ),
			'singular_label' => esc_html__( 'Filter 2', 'epron-toolkit' ),
			'query_var'      => true,
			'rewrite'        => array(
				'slug'       => $toolkit->get_theme_option( 'events_cat_slug2', 'event-category-2' ),
				'with_front' => false
			),
		));

		// Register Post Type 
		register_post_type( $this->post_type, $post_options );
		
	}


	/**
	 * Add extra columns to post table
	 * @param array $args
	 */
	public function addEventsColumns() {

		$this->events_filter_args = array(
			'post_name'    => $this->post_type,
			'filter_label' => esc_html__( 'Filter', 'epron-toolkit' ),
			'filters'      => array(
				$this->prefix . 'event_type',
				$this->prefix . 'event_categories',
				$this->prefix . 'event_categories2',
			),
			'extra_cols'   => array(
				'cb'            => '<input type="checkbox" />',
				'title'         => esc_html__( 'Title', 'epron-toolkit' ),
				'event_date'    => esc_html__( 'Date', 'epron-toolkit' ),
				'event_details' => esc_html__( 'Details', 'epron-toolkit' ),
				'preview'       => esc_html__( 'Preview', 'epron-toolkit' ),
			)
		);

		
		add_filter( 'manage_edit-' . esc_attr( $this->events_filter_args['post_name'] ) . '_columns', array( $this, 'filterEventsAddColumns' ) );
		add_filter( 'manage_posts_custom_column', array( $this, 'filterEventsDisplayColumns' ) );

		if ( isset( $this->events_filter_args['filters'] ) ) {
		$filters = $this->events_filter_args['filters'];

			foreach ( $filters as $nr => $filter ) {
				
				add_action('restrict_manage_posts', function() use ( $filter, $nr ) { 
		           $this->restrictEventsManagePosts( $this->events_filter_args['post_name'], 'top', $filter, $nr ); 
		       	});
		       	$nr++;
	       	}
	    }
		
	}


	/**
	 * Defined fields for columns
	 * @param  array $columns
	 * @return array 
	 */
	public function filterEventsAddColumns( $columns ) {

		// Get extra columns options
		$cols    = $this->events_filter_args['extra_cols'];
		$filters = $this->events_filter_args['filters'];
		$prefix  = $this->events_filter_args['post_name'] . '_';
		
		// Add unique ID to table columns
		foreach ( $cols as $i => $k ) {

			// Preview
			if ( $i === 'preview' ) {
				$cols[$prefix . 'preview'] = $cols['preview'];
				unset( $cols['preview'] );
			}

			// Event date
			if ( $i === 'event_date' ) {
				$cols[$prefix . 'event_date'] = $cols['event_date'];
				unset( $cols['event_date'], $columns['date'] );
			}

			// Event details
			if ( $i === 'event_details' ) {
				$cols[$prefix . 'event_details'] = $cols['event_details'];
				unset( $cols['event_details'] );
			}
		}

		// Filters
		if ( isset( $this->events_filter_args['filters'] ) ) {
			$filters = $this->events_filter_args['filters'];
			$count = 0;
			foreach ( $filters as $k => $t) {
				if ( $k == 0 ) {
					continue;
				}
				$count++;
				$cols[$t] = $this->events_filter_args['filter_label'] . ' ' . $count;
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
	public function filterEventsDisplayColumns( $column ) {

		global $post;

		$prefix  = $this->events_filter_args['post_name'] . '_';
		$cols    = $this->events_filter_args['extra_cols'];
		$today = strtotime( $this->current_date );
		
		// Extra Cols
		foreach ( $cols as $i => $k ) {

			/* Preview */
			if ( $i === 'preview' && $column === $prefix . 'preview' ) {
				if ( has_post_thumbnail( $post->ID ) ) {
					the_post_thumbnail( array( 60, 60 ) );
				}
			}

			/* Event Date */
			if ( $i === 'event_date' && $column === $prefix . 'event_date' ) {
				$event_date_start = get_post_custom();
				$event_date_end = get_post_custom();
				echo '<p class="mb-event-date">' . $event_date_start['_event_date_start'][0] . ' - ' . $event_date_end['_event_date_end'][0] . '</p>';
			}

			/* Event Details */
			if ( $i === 'event_details' && $column === $prefix . 'event_details' ) {

				/* Type */ 
				$taxonomies = get_the_terms( $post->ID,  $this->prefix . 'event_type' );
				$event_date_end = get_post_custom();
				if ( $taxonomies ) {
					foreach( $taxonomies as $taxonomy ) {
						if ( strtotime( $event_date_end['_event_date_end'][0] ) >= $today && $taxonomy->name === 'Future events' ) 
						    echo '<p class="mb-event-type-future" title="' . esc_html__( 'Future Event', 'epron-toolkit' ) . '">' . esc_html__( 'Future', 'epron-toolkit' ) . '</p>';
						else 
						    echo '<p class="mb-event-type-past" title="' . esc_html__( 'Past Event', 'epron-toolkit' ) . '">' . esc_html__( 'Past', 'epron-toolkit' ) . '</p>';
					}
				}

				/* Repeat */ 
				$custom = get_post_custom();
				if ( isset( $custom['_repeat_event'][0] ) && $custom['_repeat_event'][0] !== 'none' ) {
					echo '<p class="mb-event-repeat" title="' . esc_html__( 'Event repeat', 'epron-toolkit' ) . '">' . ucfirst( $custom['_repeat_event'][0] ) . '</p>';
				}

				/* Days left */ 
				$event_date_start = get_post_custom();
				$event_date_end = get_post_custom();
				if ( $this->daysLeft( $event_date_start['_event_date_start'][0], $event_date_end['_event_date_end'][0], 'days_left' ) ) {
					echo '<p class="mb-event-days-left" title="' . esc_html__( 'Days left', 'epron-toolkit' ) . '">' . $this->daysLeft( $event_date_start['_event_date_start'][0], $event_date_end['_event_date_end'][0], 'days_left' ) . '</p>';
				}
			}

		}

		// Filters
		if ( isset( $this->events_filter_args['filters'] ) ) {
			$filters = $this->events_filter_args['filters'];
			foreach ( $filters as $k => $t) {
				if ( $k == 0 ) {
					continue;
				}
				if ( $column ===  $t ) {
					$cats = get_the_terms( $post->ID, $t );
					if ($cats) {
						foreach( $cats as $taxonomy ) {
							echo esc_html( $taxonomy->name ) . ' ';
						}
					}
				}
			}
		}
	}


	/**
	 * Add filter to posts table
	 * @return void
	 */
	public function restrictEventsManagePosts($post_type, $location, $filter, $nr) {

		global $typenow;

		if ( $typenow === $this->events_filter_args['post_name'] ) {
			$filter_args = array( 'name' => $filter );
			$filters     = get_taxonomies( $filter_args );
			
			foreach ( $filters as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				$tax_name = $tax_obj->labels->name;
				
				echo '<select name="' . esc_attr( $tax_slug ) . '" id="' . esc_attr( $tax_slug ) . '" class="postform">';

				if ( $nr == 0 ) {
					echo '<option value="">' . esc_html__( 'Event Type', 'epron-toolkit' ) . '</option>';
				} else {
					echo '<option value="">' . $this->events_filter_args['filter_label'] . ' ' . esc_attr( $nr ) . '</option>';
				}

				$this->generateEventsTxonomyOptions( $tax_slug, 0, 0 );
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
	public function generateEventsTxonomyOptions( $tax_slug = null, $parent = '', $level = 0 ) {
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
	        $this->generateEventsTxonomyOptions( $tax_slug, $term->term_id, $level+1 );
	    }
	}


	/**
	 * Set date format
	 * @return void
	 */
	private function setDate() {

		/* Timezone */
		$this->current_date['local_time'] = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
		$this->current_date['server_time'] = date( 'Y-m-d', current_time( 'timestamp', 1 ) );
		$this->current_date['UTC'] = date( 'Y-m-d' );
		$this->current_date = $this->current_date[ $this->time_zone ];
	}


	/**
	 * Insert events type taxonomy
	 * @return void
	 */
	private function insertEventTypeTaxonomy() {
		if ( is_admin() ) {
			if ( ! term_exists( 'Future events', $this->prefix . 'event_type' ) ) {
		    	$this->InsertTaxonomy( 'Future events', 0, '', $this->prefix . 'event_type' );
			}
			if ( ! term_exists( 'Past events', $this->prefix . 'event_type' ) ) {
		    	$this->InsertTaxonomy( 'Past events', 0, '', $this->prefix . 'event_type' );
		    }
		}
	}


	/**
	 * Insert taxonomy
	 * @param  string $cat_name    
	 * @param  integer $parent      
	 * @param  string $description 
	 * @param  string $taxonomy    
	 * @return void              
	 */
	private function insertTaxonomy( $cat_name, $parent, $description, $taxonomy ) {
		global $wpdb;

		if ( ! term_exists( $cat_name, $taxonomy ) ) {
			$cat_name = esc_sql( $cat_name );
			$args = array(
				'description' => $description,
		        'slug'        => sanitize_title( $cat_name ),
		        'parent'      => $parent,
			);
			wp_insert_term( $cat_name, $taxonomy, $args );
			return;
		} else {
			return false;
		}
		
	}


	/**
	 * Get Taxonomy ID
	 * @param  string $cat_name 
	 * @param  string $taxonomy 
	 * @return void           
	 */
	public function getTaxonomyID( $cat_name, $taxonomy ) {
		
		$args = array(
			'hide_empty' => false
		);

		$taxonomies = get_terms( $taxonomy, $args );

		if ( $taxonomies ) {
			foreach( $taxonomies as $taxonomy ) {
				
				if ( $taxonomy->name === $cat_name ) {
					return $taxonomy->term_id;
				}
				
			}
		}
		
		return false;
	}


	/**
	 * Displays the number of days until the event starts
	 * @param  string $start_date 
	 * @param  string $end_date   
	 * @param  [type] string       
	 * @return string             
	 */
	private function daysLeft( $start_date, $end_date, $type ) {
		
		$current_date = $this->current_date;

		$now = strtotime( $current_date );
		$start_date = strtotime( $start_date );
		$end_date = strtotime( $end_date );
		
		/* Days left to start date */
		$hours_left_start = ( mktime(0, 0, 0, date( 'm', $start_date ), date( 'd', $start_date ), date( 'Y', $start_date ) ) - $now ) / 3600;
		$days_left_start = ceil( $hours_left_start / 24 );
		
		/* Days left to end date */
		$hours_left_end = ( mktime( 0, 0, 0, date( 'm', $end_date ), date( 'd', $end_date ), date( 'Y', $end_date ) ) - $now ) / 3600;
		$days_left_end = ceil( $hours_left_end / 24 );
		$days_number = ( $days_left_end - $days_left_start ) + 1;
		
		if ( $type === 'days' ) {
			return $days_number;
		}
		
		if ( $type === 'days_left' ) {
			
			/* If future events */
			if ( $days_left_end >= 0 ) {
			
				if ( $days_left_start == 0 ) {
					return '<span style="color:red;font-weight:bold">'. esc_html__( 'Start Today', 'epron-toolkit' ) .'</span>';
				}
				elseif ( $days_left_start < 0 ) {
					return '<span style="color:red;font-weight:bold">' . esc_html__( 'Continued', 'epron-toolkit' ) . '</span>';
				}
				elseif ( $days_left_start > 0 ) {
					return $days_left_start;
				}
			
			} 
			
		}

		return false;
		
	}

	/**
	 * Check event date and add post type taxonomy (Future on Past).
	 * Function it runs every intervals.
	 * @return void
	 */
	private function manageEvents() {
		global $post;
		
		$backup = $post;
		$today = strtotime( $this->current_date );

		$event_type_name = $this->prefix . 'event_type';

		$args = array(
			'post_type'        =>  $this->post_type,
			'post_status'      => 'publish, pending, draft, future, private, trash',
			'numberposts'      => '-1',
			'orderby'          => 'meta_value',  
			'meta_key'         => '_event_date_end',
			'order'            => 'ASC',
		  	'meta_query' 	   => array(array('key' => '_event_date_end', 'value' => date('Y-m-d'), 'compare' => '<', 'type' => 'DATE')),
		);

		$args[$event_type_name ] = 'Future events';


		$events = get_posts( $args );
		
	 	foreach( $events as $event ) {
				
		$event_date_start = get_post_meta( $event->ID, '_event_date_start', true );
		$event_date_end   = get_post_meta( $event->ID, '_event_date_end', true );
		$repeat           = get_post_meta( $event->ID, '_repeat_event', true );
		$start_date   = strtotime( $event_date_start );
		$end_date     = strtotime( $event_date_end );
			
			/* Move Events */

			// FUTURE EVENT If is set repeat event
			if ( isset( $repeat ) && $repeat !== 'none' ) {

				// Weekly
				if ( $repeat === 'weekly' ) {
					$every       = get_post_meta( $event->ID, '_every', true );
					$weekly_days = get_post_meta( $event->ID, '_weekly_days', true );

					// Event length
					$date_diff    = $end_date - $start_date;
					$event_length = floor( $date_diff / (60*60*24) );

					unset( $start_date, $end_date, $date_diff );

					// Make dates array
					$weekly_dates  = array();
					$weekly_days_a = array();
					foreach ( $weekly_days as $key => $day ) {
						$start_date                       = strtotime( "+$every week $day $event_date_start" );
						$date_diff                        = $start_date - $today;
						$days                             = floor( $date_diff / (60*60*24) );
						$start_date                       = date( 'Y-m-d', $start_date );
						$end_date                         = strtotime( "+$event_length day $start_date" );
						$end_date                         = date( 'Y-m-d', $end_date );
						$weekly_dates[$key]['day']        = $day;
						$weekly_dates[$key]['days']       = $days;
						$weekly_dates[$key]['start_date'] = $start_date;
						$weekly_dates[$key]['end_date']   = $end_date;
						$weekly_days_a[]                  = $days;
					}
					// Next event date
					$ne = array_search( min( $weekly_days_a ), $weekly_days_a );

					// Update event date
					update_post_meta( $event->ID, '_event_date_start', $weekly_dates[$ne]['start_date'] );
					update_post_meta( $event->ID, '_event_date_end', $weekly_dates[$ne]['end_date'] );

		        	wp_set_post_terms( $event->ID, $this->getTaxonomyID( 'Future events', $event_type_name ), $event_type_name, false ); 

				}
			} elseif ( $end_date >= $today ) {
		        wp_set_post_terms( $event->ID, $this->getTaxonomyID( 'Future events', $event_type_name ), $event_type_name, false ); 

		    } else {
			    wp_set_post_terms( $event->ID, $this->getTaxonomyID( 'Past events', $event_type_name ), $event_type_name, false );
		    }
		}
		$post = $backup; 
		wp_reset_query();
	}


	/**
	 * Ensures that dates are well set after saving the post
	 * @return void
	 */
	public function saveEvents() {
		
		if ( isset( $_POST['post_ID'] ) ) {
			$post_id = $_POST['post_ID'];
		} else {
			return; 
		}

		// Inline editor
	 	if ( $_POST['action'] == 'inline-save' ) {
	 		return;
	 	}

	    if ( isset( $_POST['post_type'] ) && $_POST['post_type'] === $this->post_type ) {
			
			$event_type_name = $this->prefix . 'event_type';
	        $today = strtotime( $this->current_date );
		    $event_date_start = strtotime( get_post_meta( $post_id, '_event_date_start', true ) );
		   
		    $event_date_end = strtotime( get_post_meta( $post_id, '_event_date_end', true ) );
			
	        /* Add Default Date */
		    if ( ! $event_date_start ) {
		  	    add_post_meta( $post_id, '_event_date_start', date( 'Y-m-d', $today) );
		    }
		    if ( ! $event_date_end ) {
			    add_post_meta( $post_id, '_event_date_end', get_post_meta( $post_id, '_event_date_start', true ) );
		    }
		    if ( $event_date_end < $event_date_start ) {
			    update_post_meta( $post_id, '_event_date_end', get_post_meta( $post_id, '_event_date_start', true ) );
		    }
			
			$event_date_start = strtotime( get_post_meta($post_id, '_event_date_start', true ) );
		    $event_date_end = strtotime( get_post_meta($post_id, '_event_date_end', true ) );
			
			/* Add Default Term */
			$taxonomies = get_the_terms( $post_id, $event_type_name );
			if ( ! $taxonomies ) {
				wp_set_post_terms( $post_id, $this->getTaxonomyID( 'Future events', $event_type_name ), $event_type_name, false );	
			}
		    if ( $event_date_end >= $today ) {
		  	    if ( is_object_in_term( $post_id, $event_type_name, 'Past events' ) )
		        wp_set_post_terms( $post_id, $this->getTaxonomyID( 'Future events', $event_type_name ), $event_type_name, false );	
		    } else {	
		        if ( is_object_in_term( $post_id, $event_type_name, 'Future events' ) )
			    wp_set_post_terms( $post_id, $this->getTaxonomyID( 'Past events', $event_type_name ), $event_type_name, false );
		    }
			
	    }
		
	}


	/**
	 * Set events order on admin page
	 * @param object $query
	 * @return void
	 */
	public function setEventsOrder( $query ) {
		global $pagenow;
		if ( is_admin() && $pagenow == 'edit.php' && isset( $query->query['post_type'] ) ) {
		    $post_type = $query->query['post_type'];
	    	if ( $post_type === $this->post_type ) {
			   	$events_order                    = '_event_date_start';
				$query->query_vars['meta_key']   = $events_order;
				$query->query_vars['orderby']    = 'meta_value';
				$query->query_vars['order']      = 'ASC';
				$query->query_vars['meta_query'] = array( array( 'key' => $events_order, 'value' => '1900-01-01', 'compare' => '>', 'type' => 'NUMERIC') );
	    	}
	  	}
	}


	/**
	 * Shelude events 
	 * @return void
	 */
	public function sheludeEvents() {
		if ( $this->clear_timer === true ) {
			delete_transient('event_task');
		}
		if ( false === ( $event_task = get_transient( 'event_task' ) ) ) {
		    $current_time = time();
			$this->manageEvents();
			set_transient( 'event_task', $current_time, $this->shelude_time );
		}
	}

}