<?php
/**
 * Easy Link Field Class
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed diCustomizer
}

if ( ! class_exists( 'RascalsBox_easy_link' ) ) {

	class RascalsBox_easy_link extends RascalsBox {

		private static $_initialized = false;
		private static $_args;
		private static $_saved_options;
		private static $_option;


		/**
         * Field Constructor.
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		public function __construct( $option, $args, $saved_options ) {
			
			// Variables
			self::$_args = $args;
			self::$_saved_options = $saved_options;
			self::$_option = $option;

			// Only for first instance
			if ( ! self::$_initialized ) {
	            self::$_initialized = true;

	            // Enqueue
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

	            // Ajax
	            add_action( 'wp_ajax_easy_link_ajax', array( $this, 'easy_link_ajax' ) );            
	            
	        }

		}


		/**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
        */
		public function enqueue() {
			
			// Admin Footer
			add_action( 'admin_footer', array( $this, 'admin_footer' ) );

			$path = self::$_args['admin_url'] . '/includes';

			// Load script
			$handle = self::$_option['type'] . '.js';
			if ( ! wp_script_is( $handle, 'enqueued' ) ) {
				wp_enqueue_script( $handle, $path . '/metabox-fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.js', false, false, true );
			}

			// Load style
			$handle_css = self::$_option['type'] . '.css';
			if ( ! wp_style_is( $handle, 'enqueued' ) ) {
				wp_enqueue_style( $handle, $path . '/metabox-fields/' . self::$_option['type'] . '/' . self::$_option['type'] . '.css' );
			}
			
		}


		/**
         * Render HTML code in admin footer
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function admin_footer() {
			$this->widget();
		}

		
		/**
         * Field Render Function.
         * Takes the vars and outputs the HTML
         *
         * @since 		1.0.0
         * @access  	public
        */
		public function render() {
		
			if ( isset( self::$_saved_options[self::$_option['id'][0]['id']] ) ) {
				self::$_option['id'][0]['std'] = self::$_saved_options[self::$_option['id'][0]['id']];
			}
			if ( isset( self::$_saved_options[self::$_option['id'][1]['id']] ) ) {
				self::$_option['id'][1]['std'] = self::$_saved_options[self::$_option['id'][1]['id']];
			}
			
			// Depedency
			if ( isset( self::$_option['dependency']) && is_array( self::$_option['dependency'] ) ) {
				echo '<div class="box-row clearfix dependent-hidden" data-depedency-el="' . esc_attr( self::$_option['dependency']['element'] ) .'" data-depedency-val="' . esc_attr( implode(',', self::$_option['dependency']['value'] ) ) . '" data-id="' . esc_attr( self::$_option['id'][0]['id'] ) . '">';
			} else {
				echo '<div class="box-row clearfix">';
			}

				// Input Wrap
				echo '<div class="box-row-input">';

					// Label
					echo '<div class="box-tc box-tc-label">';
						if ( isset( self::$_option['name'] ) && ( self::$_option['name'] !== '' ) ) {	
							echo '<label for="' . esc_attr( self::$_option['id'][0]['id'] ) . '" >' . esc_html( self::$_option['name'] ) . '</label>';
						}
					echo '</div>';

					// Input
					echo '<div class="box-tc box-tc-input">';
						if ( isset( self::$_option['sub_name'] ) && ( self::$_option['sub_name'] !== '' ) ) {	
							echo '<div class="sub-name">' . esc_html( self::$_option['sub_name'] ) . '</div>';
						}

						// Field
						// ---------------------------------------

						echo '<input name="' . esc_attr( self::$_option['id'][0]['id'] ) . '" id="' . esc_attr( self::$_option['id'][0]['id'] ) . '" type="text" value="' . esc_attr( self::$_option['id'][0]['std'] ) . '" class="link-input" data-widget="#_' . esc_attr( self::$_option['type'] ) . '"/>';
						echo '<div class="clear"></div>';
						if ( self::$_option['id'][1]['std'] === 'yes' ) {
							$checked = 'checked';
						} else {
							$checked = '';
						}
						echo '<label class="easylink-label">' . esc_html__( 'Open link in new window/tab: ', 'epron-toolkit' ) . '<input name="' . esc_attr( self::$_option['id'][1]['id'] ) . '" type="hidden" value="no" /><input name="' . esc_attr( self::$_option['id'][1]['id'] ) . '" id="' . esc_attr( self::$_option['id'][1]['id'] ) . '" type="checkbox" class="link-new-window" value="yes" ' . esc_attr( $checked ) . ' /></label>';
						echo '<div class="clear"></div>';
						echo '<button class="_button easy-link special-button"><i class="icon fa fa-external-link"></i>' . esc_html__( 'Insert Link', 'epron-toolkit' ) . '</button>';

						// ----------------------------------------

						// Display help
						if ( isset( self::$_option['desc'] ) && self::$_option['desc'] !== '' ) {
							echo '<p class="help-box">';
							$this->e_esc( self::$_option['desc'] );
							echo '</p>';
						}
					echo '</div>';

				echo '</div>';

				if ( ! isset( self::$_option['separator'] ) || ( self::$_option['separator'] === true ) ) {	
					echo '<div class="box-row-line"></div>';
				}

			
			echo '</div>';

			
		}


		/* Widget
		---------------------------------------------- */
		private function widget() {
		  
			echo '<div id="_' . self::$_option['type'] . '" style="display:none" class="_easy_link">';
			echo '<input type="hidden" autofocus="autofocus" />';
			echo '<div class="_link-search-wrap">';
			echo '<label for="link_search">';
			echo '<span>' . esc_html__( 'Search', 'epron-toolkit' ) . '</span>';
			echo '<input type="text" class="_link_search" name="link_search" tabindex="60" autocomplete="off" value="" />';
			echo '</label>';
			echo '<input type="hidden" id="link_target" name="link_target" value=""/>';
			echo '<img class="ajax-loader" src="' . esc_url(admin_url('images/wpspin_light.gif')) . '" alt="Ajax loader" />';
			echo '</div>';
			echo '<div class="_link-results">';
			echo '<ul>';
			echo '</ul>';
			echo '</div>';
			echo '</div>';

		}


		/* Easy link Ajax
		---------------------------------------------- */
		public function easy_link_ajax() {
			
			$pagenum = $_POST['page_num'];
		    $args = array();
		    $args['pagenum'] = $pagenum;
			
			if ( isset ($_POST['s'] ) && $_POST['s'] !== '') $args['s'] = stripslashes( $_POST['s'] );
			
			$results = $this->easy_link_query( $args );
			if ( ! isset( $results ) ) die();
			
		    $output = '';
			if ( ! empty( $results ) ) {
				foreach ( $results as $i => $result ) {

					if ( $i % 2 === 0 ) 
						$odd = 'class="odd"';
					else 
						$odd ='';

				  	$output .= '<li ' . esc_attr( $odd ) . '><span class="link-title">' . esc_attr( $result['title'] ) . '</span><span class="link-info">' . esc_attr( $result['info'] ) . '</span><span class="permalink r-hidden">' . esc_url( $result['permalink'] ) . '</span><span class="link-id r-hidden">' . esc_attr( $result['ID'] ) . '</span></li>';
				}
			} else {
				$output = 'end pages';
			}

		    $this->e_esc( $output );
		    exit;
		}


		/* Query function
		---------------------------------------------- */
		private function easy_link_query( $args = array() ) {
			$pts = get_post_types( array( 'public' => true ), 'objects' );
			$pt_names = array_keys( $pts );

			$query = array(
				'post_type' => $pt_names,
				'suppress_filters' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'post_status' => 'publish',
				'order' => 'DESC',
				'orderby' => 'post_date',
				'posts_per_page' => 20,
			);

			$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

			if ( isset( $args['s'] ) ) {
				$query['s'] = $args['s'];
			}

			$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

			// Do main query.
			$get_posts = new WP_Query;
			$posts = $get_posts->query( $query );

			// Check if any posts were found.
			if ( ! $get_posts->post_count ) {
				return false;
			}

			// Build results.
			$results = array();
			foreach ( $posts as $post ) {
				if ( 'post' === $post->post_type ){
					$info = mysql2date('Y/m/d', $post->post_date );
				} else {
					$info = $pts[ $post->post_type ]->labels->singular_name;
				}

				$results[] = array(
					'ID'        => $post->ID,
					'title'     => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
					'permalink' => get_permalink( $post->ID ),
					'info'      => $info,
				);
			}

			return $results;
		}

	}
}