<?php
/**
 *
 * Super Menu Walker Classes
 *
 *
 * @package         EpronToolkit
 * @author          Rascals Themes
 * @copyright       Rascals Themes
 * @version         1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

////////////////////////////////
//  Add custom fields to menu //
////////////////////////////////
add_action( 'init', array( 'EpronToolkitMenuCustomFields', 'setup' ) );

class EpronToolkitMenuCustomFields {

    // Display taxonomies children
    static function get_select_taxonomies_child( $taxonomies, $type, $lvl, $std ) {
        $options = '';
        $lvl .= '-';
        foreach ( $taxonomies as $taxonomy ) {
                
            if ( isset( $std ) && $taxonomy->slug === $std ) { 
                $selected = 'selected';
            } else { 
                $selected = '';
            }
            $options .= "<option " . esc_attr( $selected ) . " value=\"" . esc_attr( $taxonomy->slug ) . "\"> " . esc_attr( $lvl ) . " " . esc_attr( $taxonomy->name ) . " - [ slug: " . esc_attr( $taxonomy->slug ) . " ]</option>" . "\n";

            $loterms = get_terms(  array( 'taxonomy' => $type, "orderby" => "slug", "parent" => $taxonomy->term_id ) );
            if ( $loterms ) {
                $options .= self::get_select_taxonomies_child( $loterms, $type, $lvl, $std );
            }     
        }

        return $options;
    }

    // Display taxonomies
    static function get_select_taxonomies( $type, $extra_options = false, $std ) {
       
        $options = '';
        $lvl = '';
        if ( $extra_options && is_array( $extra_options ) ) {
            foreach ( $extra_options as $option ) {
                if ( $std && $std === $option['value'] ) { 
                    $selected = 'selected="selected"'; 
                } else {
                    $selected = '';
                }
                $options .= '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $option['value'] ) . '">' . esc_attr( $option['name'] ) . '</option>';
            }
        }

         $args = array(
            'taxonomy'   => $type,
            'hide_empty' => false,
            "orderby"    => "slug", 
            "parent"     => 0
         );

        if ( taxonomy_exists( $type ) ) {
            $taxonomies = get_terms( $args );
            foreach ( $taxonomies as $taxonomy ) {
                if ( isset( $std ) && $taxonomy->slug === $std ) { 
                    $selected = 'selected';
                } else { 
                    $selected = '';
                }
                $options .= "<option " . esc_attr( $selected ) . " value=\"" . esc_attr( $taxonomy->slug ) . "\">" . esc_attr( $taxonomy->name ) . " - [ slug: " . esc_attr( $taxonomy->slug ) . " ]</option>" . "\n";

                $loterms = get_terms(  array( 'taxonomy' => $type, "orderby" => "slug", "parent" => $taxonomy->term_id ) );

                if ( $loterms ) {

                    $options .= self::get_select_taxonomies_child( $loterms, $type, $lvl, $std );
                }     
            }
        }

        return $options;
    }
      
    static $options = array(
        'text_tpl' => '
            <p class="additional-menu-field-{name} description description-wide">
                <label for="edit-menu-item-{name}-{id}">
                    {label}<br>
                    <input
                        type="text"
                        id="edit-menu-item-{name}-{id}"
                        class="widefat code edit-menu-item-{name}"
                        name="menu-item-{name}[{id}]"
                        value="{value}">
                </label>
                <span class="field-move-visual-label">{desc}</span>
            </p>
        ',
        'select_tpl' => '
            <p class="additional-menu-field-{name} description description-wide">
                <label for="edit-menu-item-{name}-{id}">
                    {label}<br>
                    <select
                        id="edit-menu-item-{name}-{id}"
                        class="widefat code edit-menu-item-{name}"
                        name="menu-item-{name}[{id}]"
                        value="{value}"
                    >
                    {select_options}
                    </select>
                </label>
                <span class="field-move-visual-label">{desc}</span>
            </p>
        ',
    );

    static function setup() {
        // @todo we can do some merging of provided options from WP options for from config
        self::$options['fields'] = array(
            'select' => array(
                'name'            => 'super_menu_type',
                'label'           => esc_html__( 'Select menu type', 'epron-toolkit' ),
                'container_class' => 'link-pages',
                'taxonomy'        => 'extra',
                'extra_options'   => array(
                    array( 'value' => 'none', 'name' => esc_html__( 'Default', 'epron-toolkit' ) ),
                    array( 'value' => 'super-menu', 'name' => esc_html__( 'Big Navigation', 'epron-toolkit' ) ),
                    array( 'value' => 'posts__slider', 'name' => esc_html__( 'Posts Slider', 'epron-toolkit' ) ),
                    
                ),
                'template'        => 'select_tpl',
                'desc'            => esc_html__( 'Select menu type, please add submenus only to "Super Menu". The super menu have to be the top most menu item.', 'epron-toolkit' ),
            ),
            'select2' => array(
                'name'            => 'super_menu_cat_id',
                'label'           => esc_html__( 'Select posts category', 'epron-toolkit' ),
                'container_class' => 'link-pages',
                'taxonomy'        => 'category',
                'extra_options'   => array(
                    array( 'value' => 'none', 'name' => '' ),
                ),
                'template'        => 'select_tpl',
                'desc'            => esc_html__( 'Select posts category to display images slider.', 'epron-toolkit' ),
            ),
            
        );
        
        add_filter( 'wp_edit_nav_menu_walker', function () {
            return 'EpronToolkitMenuWalker';
        });
        add_filter( 'epron_nav_menu_item_additional_fields', array( __CLASS__, '_add_fields' ), 10, 5 );
        add_action( 'save_post', array( __CLASS__, '_save_post' ) );
    }
    static function get_fields_schema() {
        $schema = array();
        foreach( self::$options['fields'] as $name => $field ) {
            
            if ( empty($field['name'] ) ) {
                $field['name'] = $name;
            }

            $schema[] = $field;
        }
        return $schema;
    }
    static function get_menu_item_postmeta_key( $name ) {
        return '_menu_item_' . $name;
    }

    /**
     * Inject the 
     * @hook {action} save_post
     */
    static function _add_fields( $new_fields, $item_output, $item, $depth, $args ) {
        $schema = self::get_fields_schema( $item->ID );
        foreach( $schema as $field ) {
            $field['value'] = get_post_meta( $item->ID, self::get_menu_item_postmeta_key( $field['name'] ), true );
            $field['id'] = $item->ID;
            
            // Taxonomy
            if ( isset( $field['taxonomy'] ) && ! empty( $field['taxonomy'] ) ) {
                $field['select_options'] = self::get_select_taxonomies( $field['taxonomy'], $field['extra_options'], $field['value'] );
            }

            $new_fields .= @str_replace(
                array_map( function( $key ){ return '{' . esc_attr( $key ) . '}'; }, array_keys( $field ) ),
                array_values( $field ),
                self::$options[ $field['template'] ]
            );

        }

        return $new_fields;
    }

    /**
     * Save the newly submitted fields
     * @hook {action} save_post
     */
    static function _save_post( $post_id ) {

        if ( get_post_type($post_id ) !== 'nav_menu_item' ) {
            return;
        }
        $fields_schema = self::get_fields_schema( $post_id );
        foreach( $fields_schema as $field_schema ) {
            $form_field_name = 'menu-item-' . $field_schema['name'];

            if ( isset( $_POST[$form_field_name][$post_id] ) ) {
                $key = self::get_menu_item_postmeta_key( $field_schema['name'] );
                $value = stripslashes( $_POST[$form_field_name][$post_id] );
                update_post_meta( $post_id, $key, $value );
            }
        }
    }
}


//////////////////////////////
// Extends Menu Walker Edit //
//////////////////////////////
class EpronToolkitMenuWalker extends Walker_Nav_Menu_Edit {
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $item_output = '';
        parent::start_el( $item_output, $item, $depth, $args );
        $new_fields = apply_filters( 'epron_nav_menu_item_additional_fields', '', $item_output, $item, $depth, $args );
        if ( $new_fields ) {
            $item_output = preg_replace('/(?=<div[^>]+class="[^"]*submitbox)/', $new_fields, $item_output );
        }
        $output .= $item_output;
    }
}


////////////////////
// Extend WP Menu //
////////////////////
class EpronToolkitSuperMenu extends Walker_Nav_Menu {

    var $number = 1;

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        // SUPER MENU 
        $menu_type    = get_post_meta( $item->ID, '_menu_item_super_menu_type', true );
        $cat_id       = get_post_meta( $item->ID, '_menu_item_super_menu_cat_id', true );

        $custom_menu = array();

        if ( $menu_type === 'super-menu' )  {
            $custom_menu = array(
                'name' => 'super-menu',
                'classes' => 'super-menu'
            );
        } else if ( $menu_type === 'posts__slider' && $cat_id !== 'none' ) {
            $custom_menu = array(
                'name' => 'posts__slider',
                'post_type' => 'post',
                'tax_name' => 'category',
                'slug' => $cat_id,
                'order' => 'date',
                'module' => 'epron_module3',
                'classes' => 'nav-slider'
            );
        } 

        
        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // SUPER MENU - Add parent classes 
        if ( ! empty( $custom_menu )  ) {
            $classes[] = $custom_menu['classes'];
        }
      
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        // WP MENU 
        if ( empty( $custom_menu ) && isset( $args->before) ) { 
            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

        } else if ( isset( $args->before) && $custom_menu['name'] === 'super-menu' ) {
            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

        } else if ( isset( $args->before) && strpos( $custom_menu['name'], '__slider' ) !== false ) {

            // SUPER MENU 
            $item_output   = $args->before;
            $item_cat_link = get_category_link( $cat_id );
            $item_output   .= '<a'. $attributes .' data-super_menu_link="' . esc_url( $item_cat_link ) . '">';
            $item_output   .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output   .= '</a>';
            $item_output   .= $args->after;

            // CUSTOM SUBMENU -START- 
            $output .= '<ul class="sub-menu"><li>';
            
            // From slug to ID 
            $cat_obj = get_term_by('slug',  $custom_menu['slug'],  $custom_menu['tax_name']);
            $cat_id = $cat_obj->term_id;

            // Check if current taxonomy has children 
            $children = get_term_children( $cat_id, $custom_menu['tax_name'] );
            if ( ! empty( $children ) ) {
                $cat_id = $children;
            } else {
                $cat_id = explode( " ", $cat_id );
            }

            // Variables
            $date_format = get_option( 'date_format' );
            $thumb_size = 'epron-medium-square-thumb';
            $limit = 4;
            $excerpt = false;

            // Loop Args 
            $args = array(
                'post_type' => $custom_menu['post_type'],
                'order'     => 'ASC',
                'orderby'   => $custom_menu['order'],
            );

            // Add taxonomies to loop 
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $custom_menu['tax_name'],
                    'field'    => 'term_id',
                    'terms'    => $cat_id,
                )
            );

            // Posts Count
            $temp_args              = $args;
            $temp_args['showposts'] = -1;
            $temp_query_count       = new WP_Query();
            $temp_query_count->query( $temp_args );
            $posts_nr               = $temp_query_count->post_count;
            $module                 = $custom_menu['module'];

            // Check how many post are available
            if ( $posts_nr > $limit ) {
                $show_nav = true;
            } else {
                $show_nav = false;
            }

            // Add limit
            $args['showposts'] = $limit;

            $super_menu_q = new WP_Query();
            $super_menu_q->query( $args );

            $cat_id_e = implode( ',', $cat_id );


            // begin Loop
            if ( $super_menu_q->have_posts() ) {
                $output .= '<div class="ajax-posts-slider super-menu-wrap anim anim-slide-from-right" data-obj=\'{"action": "epron_posts_slider_ajax", "cats": "' . esc_attr( $cat_id_e ) . '", "cpt": "' . esc_attr( $custom_menu['post_type'] ) . '", "tax": "' . esc_attr( $custom_menu['tax_name'] ) . '", "limit": "' . esc_attr( $limit ) . '", "excerpt": "' . esc_attr( $excerpt ) . '", "thumb_size": "' . esc_attr( $thumb_size ) . '", "module" : "' . esc_attr( $module ) . '", "orderby":"' . esc_attr(  $custom_menu['order'] ) . '" }\' data-pagenum="1">';
                $output .= '<div class="ajax-posts-slider-inner">';
                while ( $super_menu_q->have_posts() ) {
                    $super_menu_q->the_post();

                    if  ( function_exists( 'epron_get_taxonomies' ) ) {
                        $tax_args = array(
                            'id'         => $super_menu_q->post->ID,
                            'tax_name'   => 'category',
                            'separator'  => ' · ',
                            'link'       => false,
                            'limit'      => 2,
                            'show_count' => true

                        );
                        $cats_html = epron_get_taxonomies( $tax_args );
                    } else {
                        $cats_html = '';
                    }

                    // Excerpt
                    if ( $excerpt ) {
                        if ( has_excerpt() ) {
                            $excerpt = wp_trim_words( get_the_excerpt(), 30, '' );
                        } else {
                            $excerpt = wp_trim_words( strip_shortcodes( get_the_content() ), 30, '' ); 
                        }
                    }

                    $classes = array(
                        'flex-col-1-' . esc_attr( $limit ),
                        'ajax-item',
                        'small-module',
                    );
                    if ( function_exists( $module ) ) {
                        $output .= $module( array(
                            'post_id'     => $super_menu_q->post->ID,
                            'thumb_size'  => $thumb_size,
                            'lazy'        => false,
                            'title'       => get_the_title(),
                            'permalink'   => get_permalink(),
                            'author'      => esc_html( get_the_author_meta( 'display_name', $super_menu_q->post->post_author ) ),
                            'date'        => get_the_time( $date_format ),
                            'posts_cats'  => $cats_html,
                            'show_tracks' => 'yes',
                            'classes'     => implode(' ', $classes )

                        ) );
                    }
                }
                $output .= '</div>';
                 if ( $show_nav ) {
                    $output .= '<div class="arrows-nav-block"><div class="arrow-nav left disabled"><span><i class="icon icon-angle-left"></i></span></div><div class="arrow-nav right"><span><i class="icon icon-angle-right"></i></span></div></div>';
                    if ( function_exists( 'epron_content_loader' ) ) {
                        $output .= epron_content_loader('#fff');
                    }
                };
                $output .= '</div>';

            } else {
                $output .= esc_html__( 'Category does not contain posts, go to the menu and select another category, or add a few posts to the current category.', 'epron-toolkit' );
            }
           
            $output .= '</li></ul>';

          // CUSTOM SUBMENU -END- 
          $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );


        }
    }

}