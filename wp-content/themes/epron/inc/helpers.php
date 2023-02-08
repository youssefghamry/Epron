<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            helpers.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ==================================================
  Show Featured Content
================================================== */
if ( ! function_exists( 'epron_featured' ) ) :
function epron_featured( $id = 0) {
 
    if ( $id === 0 ) {
        return;
    }
    $o = '';

    // Get options
    $epron_opts = epron_opts();

    // Featured Content 
    $featured_content = get_post_meta($id, '_featured_content', true );

    if ( ! isset( $featured_content ) || $featured_content === false || $featured_content === 'none'  ) {
        return;
    }

    // Featured Link 
    $media_link = get_post_meta($id, '_media_link', true );

    // Source name 
    $source_name = get_post_meta($id, '_source_name', true );

    $o .= '<div class="featured-block">';

    // Image
    if ( $featured_content === 'image' && has_post_thumbnail( $id ) ) {

        if ( function_exists( 'epron_get_image' ) ) {
            $o .= '<div class="featured-image">';
            $o .= epron_get_image( $id, 'full', '', $lazy = true, $image_id = false );
            $o .= '</div>';
        }

    // Youtube
    } elseif ( $featured_content === 'youtube' && $media_link !== '' ) {
  
        if ( preg_match( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $media_link, $matches ) ) {

            $o .= '<div class="youtube media" id="' . esc_attr( $matches[1] ) . '"></div>';
        }
 
    // Vimeo
    } elseif ( $featured_content === 'vimeo' && $media_link !== '' ) {

        if ( preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $media_link, $matches  ) ) {
            $o .= '<div class="vimeo media" id="' . esc_attr( $matches[5] ) . '"></div>';
        }

    // Soundcloud
    } elseif ( $featured_content === 'soundcloud' && $media_link !== '' ) {
        $o .= '<div class="soundcloud media">';
            if ( function_exists( 'epron_get_sc_iframe' ) ) {
                $o .= epron_get_sc_iframe( $media_link );
            } 
        $o .= '</div>';
  

    // Spotify
    } elseif ( $featured_content === 'spotify' && $media_link !== '' ) {
        $o .= '<div class="spotify media">';
            if ( function_exists( 'epron_get_spotify_iframe' ) ) {
                $o .= epron_get_spotify_iframe( $media_link );
            } 
        $o .= '</div>';


    // Bandcamp
    } elseif ( $featured_content === 'bandcamp' ) {
        
        $bandcamp_code = get_post_meta($id, '_bandcamp_code', true );
        if ( $bandcamp_code !== '' ) {
            $o .= '<div class="bandcamp media">';
            $o .= get_post_meta($id, '_bandcamp_code', true );
            $o .= '</div>';
        }

    // Custom ID
    } elseif ( $featured_content === 'tracks' ) {
    
        // Tracks 
        $track_id = get_post_meta($id, '_track_id', true );
        $tracks_ids = get_post_meta($id, '_tracks_ids', true );
        
        if ( $track_id !== '' && function_exists( 'epron_toolkit' ) && epron_toolkit( 'scamp_player' ) ) {
   
            $o .= '<div class="scamp-player media">';

            // Get list settings 
            $fixed_height = get_post_meta($id, '_fixed_height', true );
            $fixed_height = ( $fixed_height === '0' ? '0' : $fixed_height );
            $show_covers  = get_post_meta($id, '_show_covers', true );
            $big_cover    = get_post_meta($id, '_big_cover', true );
            $player_style = get_post_meta($id, '_player_style', true );
            $player_style = ( ! isset( $player_style ) || $player_style === '' ) ? 'tracklist' : $player_style;
            $limit        = get_post_meta($id, '_limit', true );

            $o .= do_shortcode( '[kc_' . esc_attr( $player_style ) . ' 
                id="' . esc_attr( $track_id ) . '" 
                ids="' . esc_attr( $tracks_ids ) . '" 
                covers="' . esc_attr( $show_covers ) . '" 
                big_cover="' . esc_attr( $big_cover ) . '" 
                buttons="yes" 
                fixed_height="' . esc_attr( $fixed_height ) . '" 
                limit="' . esc_attr( $limit ) . '" 
                classes="" 
                "]' );

     
            $o .= '</div>';
        }
    }

    // Display source metabox 
    if ( $source_name !== '' ) {
        $o .= '<span class="source-name">' . wp_kses_post( $source_name ) . '</span>';
    }

    $o .= '</div>';

    return $o;
}
endif;



/* ==================================================
  Show Loader 
================================================== */
if ( ! function_exists( 'epron_loader' ) ) :
function epron_loader() {
    
    $loader = '<div class="content__loader"></div>';
    if( has_filter('epron_wpal_change_loader') ) {
        $loader = apply_filters( 'epron_wpal_change_loader', $loader );
    }
    return '<div id="loader">' . $loader . '</div>';
}
endif;


/* ==================================================
  Is WooCommerce page
================================================== */
if ( ! function_exists( 'epron_is_woo_page' ) ) :
function epron_is_wc_page() {

    if ( class_exists( 'WooCommerce' ) ) {
        if ( is_cart() || is_checkout() || is_account_page() ) {
            return true;
        }

    }
    return false;

}
endif;



/* ==================================================
  Woocommerce Cart Details 
================================================== */

// Handle cart in header fragment for ajax add to cart
if ( ! function_exists( 'epron_cart_details' ) ) :
function epron_cart_details() {

    if ( class_exists( 'WooCommerce' ) ) {
        $count = WC()->cart->get_cart_contents_count();
        $wc_link = wc_get_cart_url();

        ?>
        <a href="<?php echo esc_url( $wc_link ) ?>" title="<?php esc_attr_e( 'View your shopping cart', 'epron' ); ?>" class="cart-parent">
            <span>
            <?php echo WC()->cart->get_cart_total(); ?>
            <?php echo '<span class="contents">' . sprintf( _n( '%d item', '%d items', $count, 'epron' ), $count ) . '</span>'; ?>
            </span>
        </a>
    <?php
    }
}
endif;

/* Ajax Fragments */
add_filter( 'add_to_cart_fragments', 'epron_header_fragments' );

function epron_header_fragments( $fragments ) {
   
    ob_start();
    epron_cart_details();
    $fragments['a.cart-parent'] = ob_get_clean();
    return $fragments;
}


/* ==================================================
  Show AD 
================================================== */
if ( ! function_exists( 'epron_show_ad' ) ) :
function epron_show_ad( $place = '', $title = '', $custom = '' ) {
    $epron_opts = epron_opts();
    if ( ! empty( $place ) ) {
        $ad_type = "ad_{$place}_type";
        $ad_type = $epron_opts->get_option( $ad_type );
        $place_class = empty( $custom ) ? 'adspot-' . $place : '';
        $output = '';
        if ( $ad_type and ! empty( $ad_type ) ) {

            switch ( $ad_type ) {
                case 'custom':
                    $ad_code = "ad_{$place}_custom";
                    $ad_code = $epron_opts->get_option( $ad_code );
                    $output .= '<div class="adspot adspot-custom ' . esc_attr( $place_class ) . '" >';
                    if ( ! empty( $title ) ) {
                        $output .=  '<span class="adspot-heading">';
                        $output .= esc_html__( '- Advertisement -', 'epron' );
                        $output .= '</span>';
                    }
                    $output .= $epron_opts->esc( $ad_code );
                    $output .= '</div>';
                    break;
                
                 case 'adsense':
                    $ad_code = "ad_{$place}_adsense";
                    $ad_code = $epron_opts->get_option( $ad_code );
                    $output .= '<div class="adspot adspot-google ' . esc_attr( $place_class ) . '" >';
                    if ( ! empty( $title ) ) {
                        $output .=  '<span class="adspot-heading">';
                        $output .= esc_html__( '- Advertisement -', 'epron' );
                        $output .= '</span>';
                    }
                    $output .= $epron_opts->esc( $ad_code );
                    $output .= '</div>';
                    break;
            }

            return $output;

        }
    
    }

    return false;
   
}
endif;


/* ==================================================
   BG Generator
================================================== */
if ( ! function_exists( 'epron_get_background' ) ) :
function epron_get_background( $bg = null ) {
    
    if ( json_decode( $bg ) !== null ) {
        
        $css = '';            
        $data = json_decode( $bg, true );
        
        // Image
        if ( isset( $data['image'] ) ) {
            $image = wp_get_attachment_image_src( $data['image'], 'full' );
            $image = $image[0];
            // If image exists
            if ( $image ) {
                $css .= 'background-image: url( ' . esc_url( $image ) . ');';
            }
        } 

        // Color
        if ( isset( $data['color'] ) ) {
            $css .= 'background-color:' . esc_attr( $data['color'] ) . ';';
        }

        // Position
        if ( isset( $data['position'] ) ) {
            $css .= 'background-position:' . esc_attr( $data['position'] ) . ';';
        }

        // Repeat
        if ( isset( $data['repeat'] ) ) {
            $css .= 'background-repeat:' . esc_attr( $data['repeat'] ) . ';';
        }

        // Attachment
        if ( isset( $data['attachment'] ) ) {
            $css .= 'background-attachment:' . esc_attr( $data['attachment'] ) . ';';
        }

        // Size
        if ( isset( $data['size'] ) ) {
            $css .= 'background-size:' . esc_attr( $data['size'] ) . ';';
        }

        return $css;
    } else {
        return false;
    }
}
endif;


/* ==================================================
  KC Content
================================================== */
if ( ! function_exists( 'epron_get_content' ) ) :
function epron_get_content($id = null) {

    if ( function_exists( 'kc_do_shortcode' ) ) {
        // Make sure that content has [kc_row]  
        if ( strpos( get_the_content($id), '[kc_row' ) !== false ) {
            echo kc_do_shortcode( get_the_content($id) );
            return;
        } 
    }

    // Display Default WP Content 
    the_content($id); 
}
endif;


/* ==================================================
  Get Filters 
  ver 2.0.0
================================================== */
if ( ! function_exists( 'epron_get_filters' ) ) :
function epron_get_filters( $atts ) {
    if ( ! is_array( $atts ) || ! isset( $atts['taxonomies'] ) || empty( $atts['filter_type'] ) ) {
        return false;
    }

    $output = '';
    $multiple_filters = ( $atts['filter_type'] === 'multiple-filters' ? true : false );

    $filter_classes = array(
        'ajax-filters',
        $atts['filter_type'],
        $atts['show_filters'],
        $atts['filter_sel_method']

    );

    $output .= '<div class="' . esc_attr( implode( ' ', $filter_classes )  ) . '">';

    // Only for multiple filters  
    if ( $atts['filter_type'] === 'multiple-filters' ) {
        $output .= '<div class="filter-header">';
        $color_scheme = get_theme_mod( 'color_scheme', 'dark' );
        if ( function_exists( 'epron_content_loader_small' ) ) {
            $output .= epron_content_loader_small();
        }
        $output .= '<a href="#" class="filter-label">' .  esc_html__('Filters', 'epron' ) . '</a>';
        $output .= '</div>';
    }
    $output .= '<div class="filters-wrap">';

    foreach ( $atts['taxonomies'] as $key => $tax ) {

         // Hidden filter if label is empty 
        if ( $tax['label'] === '' ) {
            $output .= '<ul class="hidden-filter" data-filter-group="" data-tax-name="' . esc_attr( $tax['tax_name'] ) . '">';
        } else {
            $output .= '<ul data-filter-group="" data-tax-name="' . esc_attr( $tax['tax_name'] ) . '">';
        }

        if ( $multiple_filters === false ) {
        
            $output .= '<li>';
            if ( function_exists( 'epron_content_loader' ) ) {
                $output .= epron_content_loader_small();
            }
            $output .= '</li>';
        }
        $output .= '<li><a href="#" data-category_ids="all" data-category_slugs="all" class="is-active filter-reset">' . esc_html( $tax['label'] ) .'</a></li>';

        $term_args = array( 'hide_empty' => '1', 'orderby' => 'name', 'order' => 'ASC' );

        if ( isset($tax['ids']) && $tax['ids'] !== null ) {
            $term_args['include'] = $tax['ids'];
            
        } elseif ( isset($tax['slugs']) && $tax['slugs'] !== null ){
            $slugs_a = explode( ',', $tax['slugs'] );
            $include = get_terms(array(
                'slug'     => $slugs_a, 
                'taxonomy' => $tax['tax_name'],
                'fields'   => 'ids',
            ));
            $term_args['include'] = $include;
        }
        $terms = get_terms( $tax['tax_name'], $term_args );
        if ( $terms ) {
            foreach ( $terms as $term ) {
                $t_query = array(
                    'taxonomy' => $tax['tax_name'],
                    'field' => 'term_id',
                    'terms' => array( $term->term_id )
                );
                if ( epron_tax_is_empty($atts, $t_query) === false ) {  
                    $output .=  '<li><a href="#" data-category_ids="' . esc_attr( $term->term_id ) . '" data-category_slugs="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</a></li>';
                }
            }
        }

        $output .= '</ul>';   
    
    }
    $output .= '</div>';
    $output .= '</div>';

    return $output;
}
endif;


/* ==================================================
  Check if taxonomy is empty
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_tax_is_empty' ) ) :
function epron_tax_is_empty( $atts, $t_query ) {

    $query_args = epron_prepare_wp_query( $atts, 0 );

    $query_args['posts_per_page'] = -1;

    if ( isset( $query_args['tax_query'] ) ) {

        array_push($query_args['tax_query'], $t_query);
        $query_args['tax_query'] = array_unique($query_args['tax_query'], SORT_REGULAR);
        $t_query  = new WP_Query($query_args);
        $count = $t_query->post_count;
        if ( $count === 0 ) {
            return true;
        }
    }
    
    return false;

}
endif;


/* ==================================================
  Prepare Events Filter
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_prepare_events_filter' ) ) :
function epron_prepare_events_filter( $args ) {

    if ( ! isset( $args ) || ! isset( $args['filter_atts'] ) ) {
        return false;
    }

    $filter_atts = $args['filter_atts'];

    if ( isset( $filter_atts[0] ) ) {
        $filter_atts = $filter_atts[0];
    }

    // Extra parameters 
    $filter_atts['filter_type']       = $args['ajax_filter'];
    $filter_atts['show_filters']      = $args['show_filters'];
    $filter_atts['filter_sel_method'] = $args['filter_sel_method'];
    $filter_atts['event_type_tax']    = 'wp_event_type';
    $filter_atts['post_type']         = 'wp_events_manager';
    $filter_atts['sort_order']        = 'events_date';

    // All available filters (taxonomies)  
    $taxes = array(
        'epron_event_type' => array(
            'tax_name' => 'wp_event_type',
            'ids'      => 'event_type_ids', 
            'slugs'    => 'event_type_slugs',
            'label'    => 'event_type_label',
        ),
        'epron_events_cats' => array(
            'tax_name' => 'wp_event_categories',
            'ids'      => 'category_ids', 
            'slugs'    => 'category_slugs',
            'label'    => 'category_label',
        ),
        'epron_events_cats2' => array(
            'tax_name' => 'wp_event_categories2',
            'ids'      => 'category_ids', 
            'slugs'    => 'category_slugs',
            'label'    => 'category_label',
        ),
       
    );

    $filter_atts['taxes'] = $taxes;


    return epron_prepare_filter_taxonomies($filter_atts);

}

endif;


/* ==================================================
  Prepare Artists Filter
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_prepare_artists_filter' ) ) :
function epron_prepare_artists_filter( $args ) {

    if ( ! isset( $args ) || ! isset( $args['filter_atts'] ) ) {
        return false;
    }

    $filter_atts = $args['filter_atts'];

    if ( isset( $filter_atts[0] ) ) {
        $filter_atts = $filter_atts[0];
    }

    // Extra parameters 
    $filter_atts['filter_type']       = $args['ajax_filter'];
    $filter_atts['show_filters']      = $args['show_filters'];
    $filter_atts['filter_sel_method'] = $args['filter_sel_method'];
    $filter_atts['post_type']         = 'wp_artists';

    // All available filters (taxonomies)  
    $taxes = array(
        'epron_artists_cats' => array(
            'tax_name' => 'wp_artists_cats',
            'ids'      => 'category_ids', 
            'slugs'    => 'category_slugs',
            'label'    => 'category_label',
        ),
        'epron_artists_cats2' => array(
            'tax_name' => 'wp_artists_cats2',
            'ids'      => 'category_ids2', 
            'slugs'    => 'category_slugs2',
            'label'    => 'category_label2',
        ),
   
    );

    $filter_atts['taxes'] = $taxes;
    return epron_prepare_filter_taxonomies($filter_atts);

}

endif;


/* ==================================================
  Prepare Releases Filter
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_prepare_releases_filter' ) ) :
function epron_prepare_releases_filter( $args ) {

    if ( ! isset( $args ) || ! isset( $args['filter_atts'] ) ) {
        return false;
    }

    $filter_atts = $args['filter_atts'];

    if ( isset( $filter_atts[0] ) ) {
        $filter_atts = $filter_atts[0];
    }

    // Extra parameters 
    $filter_atts['filter_type']       = $args['ajax_filter'];
    $filter_atts['show_filters']      = $args['show_filters'];
    $filter_atts['filter_sel_method'] = $args['filter_sel_method'];
    $filter_atts['post_type']         = 'wp_releases';

    // All available filters (taxonomies)  
    $taxes = array(
        'epron_releases_cats' => array(
            'tax_name' => 'wp_release_genres',
            'ids'      => 'category_ids', 
            'slugs'    => 'category_slugs',
            'label'    => 'category_label',
        ),
        'epron_releases_cats2' => array(
            'tax_name' => 'wp_release_artists',
            'ids'      => 'category_ids2', 
            'slugs'    => 'category_slugs2',
            'label'    => 'category_label2',
        ),
     
    );

    $filter_atts['taxes'] = $taxes;
    return epron_prepare_filter_taxonomies($filter_atts);

}

endif;


/* ==================================================
  Prepare Gallery Filter
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_prepare_gallery_filter' ) ) :
function epron_prepare_gallery_filter( $args ) {

    if ( ! isset( $args ) || ! isset( $args['filter_atts'] ) ) {
        return false;
    }

    $filter_atts = $args['filter_atts'];

    if ( isset( $filter_atts[0] ) ) {
        $filter_atts = $filter_atts[0];
    }

    // Extra parameters 
    $filter_atts['filter_type']       = $args['ajax_filter'];
    $filter_atts['show_filters']      = $args['show_filters'];
    $filter_atts['filter_sel_method'] = $args['filter_sel_method'];
    $filter_atts['post_type']         = 'wp_gallery';

    // All available filters (taxonomies)  
    $taxes = array(
        'epron_gallery_cats' => array(
            'tax_name' => 'wp_gallery_cats',
            'ids'      => 'category_ids', 
            'slugs'    => 'category_slugs',
            'label'    => 'category_label',
        ),
        'epron_gallery_cats2' => array(
            'tax_name' => 'wp_gallery_cats2',
            'ids'      => 'category_ids2', 
            'slugs'    => 'category_slugs2',
            'label'    => 'category_label2',
        ),
    );

    $filter_atts['taxes'] = $taxes;
    return epron_prepare_filter_taxonomies($filter_atts);

}
endif;


/* ==================================================
  Prepare Posts Filter
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_prepare_posts_filter' ) ) :
function epron_prepare_posts_filter( $args ) {

   if ( ! isset( $args ) || ! isset( $args['filter_atts'] ) ) {
        return false;
    }

    $filter_atts = $args['filter_atts'];

    if ( isset( $filter_atts[0] ) ) {
        $filter_atts = $filter_atts[0];
    }

    // Extra parameters 
    $filter_atts['filter_type']       = $args['ajax_filter'];
    $filter_atts['show_filters']      = $args['show_filters'];
    $filter_atts['filter_sel_method'] = $args['filter_sel_method'];

    // All available filters (taxonomies)  
    $taxes = array(
        'category' => array(
            'tax_name' => 'category',
            'ids'      => 'category_ids', 
            'slugs'    => 'category_slugs',
            'label'    => 'category_label',
        ),
    );

    $filter_atts['taxes'] = $taxes;
    return epron_prepare_filter_taxonomies($filter_atts);

}
endif;


/* ==================================================
  Prepare Filter Taxonomies
  ver 1.0.0
================================================== */
if ( ! function_exists( 'epron_prepare_filter_taxonomies' ) ) :
function epron_prepare_filter_taxonomies( $atts ) {

    if ( isset( $atts[0] ) ) {
        $atts = $atts[0];
    }

    if ( ! isset( $atts['post_type'] ) ) {
        $atts['post_type'] = 'post';
    }

    $temp_taxes = array();
    $count = 1;
    $label = 'category_label';
    $ids_name = 'category_ids';
    $slugs_name = 'category_slugs';
    $multiple_filters  = ( $atts['filter_type'] === 'multiple-filters' ? true : false );

    // Old taxonomies 
    if ( $multiple_filters === false && ! isset( $atts[$label] ) ) {
        $atts[$label] = esc_html__( 'All', 'epron' );
    }

    foreach ($atts['taxes'] as $k => $t) {

        // Tax name  
        $temp_taxes[$count]['tax_name'] = $t['tax_name'];

        // IDS  
        if ( isset( $atts[$t['ids']] ) ) {
            $temp_taxes[$count]['ids'] = $atts[$t['ids']];
            unset( $atts[$t['ids']] );
        } else {
            $temp_taxes[$count]['ids'] = '';
        }

        // Slugs  
        if ( isset( $atts[$t['slugs']] ) ) {
            $temp_taxes[$count]['slugs'] = $atts[$t['slugs']];
            unset( $atts[$t['slugs']] );
        } else {
            $temp_taxes[$count]['slugs'] = '';
        }

        // Label  
        if ( isset( $atts[$t['label']] ) ) {
            $temp_taxes[$count]['label'] = $atts[$t['label']];
            unset( $atts[$t['label']] );
        } else {
            $temp_taxes[$count]['label'] = esc_html__( 'All', 'epron' );
        }

        $count++;

    }
    unset($atts['taxes']);

    // Set order  
    if ( isset( $atts['filters_order'] ) ) {
        $order = array_map( 'intval', explode(',', $atts['filters_order'] ) );
        $sorted_taxes = array();
        if ( is_array( $order ) ) {            
            foreach ( $order as $key => $value ) {
                if ( isset( $temp_taxes[$value] ) ) {
                    $sorted_taxes[] = $temp_taxes[$value];
                }
            }

            $temp_taxes = $sorted_taxes;
        }

        unset( $atts['filters_order'] );
    }

    // Set index  
    foreach ($temp_taxes as $key => $value) {
        $index = $value['tax_name'];
        $atts['taxonomies'][$index] = $value;

        if ( $multiple_filters === false ) {
            break;
        }
    }
 
    return $atts;

}
endif;


/* ==================================================
  Prepare WP Query 
  ver 1.1.0
================================================== */
if ( ! function_exists( 'epron_prepare_wp_query' ) ) :
function epron_prepare_wp_query( $atts = '', $paged = '' ) {

    if ( isset( $atts[0] ) ) {
        $atts = $atts[0];
    }

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_ids'            => '',
        'taxonomies'          => [],
        'tag_slugs'           => '',
        'sort_order'          => 'post_date',
        'limit'               => 8,
        'author_ids'          => '',
        'offset'              => '0',
        'posts_per_page'      => '',
        'post_type'           => 'post',
        'year'                => '',
        'monthnum'            => '',
        'day'                 => '',
        'ignore_sticky_posts' => 1,

    );

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    extract( $atts, EXTR_PREFIX_SAME, 'query_arg' );

    // Make sure that offset is integer 
    $offset = (int)$offset;

    // Init WP query 
    $query_args = array(
        'post_type' => $post_type,
        'ignore_sticky_posts' => $ignore_sticky_posts,
        'post_status' => 'publish',
    );

    // Categories 
    if ( ! empty( $taxonomies ) ) {

        $k = 0;

        foreach ( $taxonomies as $t ) {

            // Skip Event type taxonomy  
            if ( isset( $event_type_tax ) && $t['tax_name'] === $event_type_tax ) {
                continue;
            }

            $tax_terms = [];
            $field_name = 'term_id';

            // IDS or Slugs 

            // Saved 
            if ( ! empty( $t['ids'] ) ) {
                $tax_terms = $t['ids'];
            } else if ( ! empty( $t['slugs'] ) ) {
                $field_name = 'slug';
                $tax_terms = $t['slugs'];
            }

            if ( ! is_array( $tax_terms ) ) {
                $tax_terms = explode( ',', $tax_terms );

            } 

            // From filter 
            if ( isset( $t['filter_ids'] ) && ! empty( $t['filter_ids'] ) ) {
                //$tax_terms = array_unique( array_merge( $tax_terms, $t['filter_ids'] ), SORT_REGULAR );
                $tax_terms = $t['filter_ids'];
            }


            // Simple Category (Blog)  
            if ( $post_type === 'post' && ! empty( $tax_terms ) ) {

                if ( $field_name === 'slug' ) {
                    $cat_slugs_a = $tax_terms;
                    $cat_ids_a = array();
                    foreach ( $cat_slugs_a as $slug ) {
                        if ( $slug[0] === '-'  ) {
                            $str = substr( $slug, 1 );
                            $is_minus = true;
                        } else {
                            $is_minus = false;
                        }
                        if ( get_category_by_slug( $slug ) ) {
                            $cat_obj = get_category_by_slug( $slug );
                            $cat_id = $cat_obj->term_id;
                            if ( $is_minus ) {
                                $cat_id = (-1 * abs( $cat_id ) );
                            }
                            $cat_ids_a[] = $cat_id;
                        }
                    }
                    if ( ! empty( $cat_ids_a ) ) {
                        $tax_terms = implode( ',', $cat_ids_a );
                    }

                 }

                $query_args['cat'] = $tax_terms;
                break;
            }

            if ( ! empty( $tax_terms ) ) {

                // Custom post type category  
                $query_args['tax_query'][$k] = array(
                    'taxonomy' => $t['tax_name'],
                    'field'    => $field_name,
                    'terms'    => $tax_terms,
                );
                
            }      

            $k++;

        }

    }


    if ( ! empty( $tag_slugs ) ) {
        $query_args['tag'] = $tag_slugs;
    }

    if ( ! empty( $author_ids ) ) {
        $query_args['author'] = $author_ids;
    }

    if ( ! empty( $year ) ) {
        $query_args['year'] = $year;
    }

    if ( ! empty( $monthnum ) ) {
        $query_args['monthnum'] = $monthnum;
    }

    if ( ! empty( $day ) ) {
        $query_args['day'] = $day;
    }

    if ( ! empty( $post_ids ) ) {

        $post_ids_a = explode ( ',', $post_ids );

        $post__in = array();
        $post__not_in = array();

        foreach ( $post_ids_a as $post_id ) {
            $post_id = trim( $post_id );

            if ( is_numeric( $post_id ) ) {
                if ( intval( $post_id ) < 0 ) {
                    $post__not_in[] = str_replace('-', '', $post_id);
                } else {
                    $post__in[] = $post_id;
                }
            }
        }

        if ( ! empty( $post__in ) ) {
            $query_args['post__in'] = $post__in;
            $query_args['orderby'] = 'post__in';
        }

        if ( ! empty( $post__not_in ) ) {
            $query_args['post__not_in'] = $post__not_in;
        }
    }

    switch ( $sort_order ) {
        
        case 'menu_order':
            $query_args['orderby'] = 'menu_order';
            $query_args['order'] = 'ASC';
            break;
        case 'oldest_posts':
            $query_args['order'] = 'ASC';
            break;
        case 'highest_rated':
            $query_args['meta_query'] = array(
                'relation' => 'AND',
                'has_review' => array(
                    'key' => '_has_reviews',
                    'value'   => true,
                ),
                'rating' => array(
                    'key' => '_rating',
                    'compare' => 'EXISTS',
                ), 
            );
            $query_args['orderby'] = array( 
                'rating' => 'DESC'
            );
            break;
        case 'rand':
            $query_args['orderby'] = 'rand';
            break;
        case 'title':
            $query_args['orderby'] = 'title';
            $query_args['order'] = 'ASC';
            break;
        case 'comment_count':
            $query_args['orderby'] = 'comment_count';
            $query_args['order'] = 'DESC';
            break;
        case 'rand_today':
            $query_args['orderby'] = 'rand';
            $query_args['year'] = date('Y');
            $query_args['monthnum'] = date('n');
            $query_args['day'] = date('j');
            break;
        case 'rand_week':
            $query_args['orderby'] = 'rand';
            $query_args['date_query'] = array(
                        'column' => 'post_date_gmt',
                        'after' => '1 week ago'
                        );
            break;

    }


    // Limit posts per page 
    if ( empty( $limit ) ) {
        $limit = -1;
    }
    $query_args['posts_per_page'] = $limit;

    // Pagination 
    if ( ! empty( $paged ) ) {
        $query_args['paged'] = $paged;
    } else {
        $query_args['paged'] = 1;
    }


    // Event Type 
    if ( isset( $event_type_tax ) ) {

        if ( !isset( $event_type ) ) {
            $event_type = 'future-events';
        }

        if ( $event_type === 'future-events' || $event_type === 'past-events' ) {

            // Set order 
            $order = $event_type === 'future-events' ? $order = 'ASC' : $order = 'DSC';

            if ( ! isset( $query_args['tax_query'] ) ) {
                $query_args['tax_query'] = array();
            }
            $query_args['tax_query']['relation'] = 'AND';

            array_push( $query_args['tax_query'], 
                array(
                   'taxonomy' => $event_type_tax,
                   'field'    => 'slug',
                   'terms'    => $event_type
                )
            );

            $query_args['orderby'] = 'meta_value';
            $query_args['order'] = $order;
            $query_args['meta_key'] = '_event_date_start';
            
        }

        if ( $event_type === 'all' ) {

            // Reset Query 
            $offset = 0;
            unset($query_args['post__in'],$query_args['post__not_in']);

            $future_tax = array(
                'relation' => 'AND',
                array(
                   'taxonomy' => $event_type_tax,
                   'field'    => 'slug',
                   'terms'    => 'future-events'
                )
            );

            $past_tax = array(
                'relation' => 'AND',
                array(
                   'taxonomy' => $event_type_tax,
                   'field'    => 'slug',
                   'terms'    => 'past-events'
                  )
            );

            if ( isset( $query_args['tax_query']  ) ) {
                array_push( $future_tax, $query_args['tax_query']);
                array_push( $past_tax, $query_args['tax_query']);
            }


            $future_events = get_posts( array(
                'post_type' => $post_type,
                'showposts' => -1,
                'tax_query' => $future_tax,
                'orderby' => 'meta_value',
                'meta_key' => '_event_date_start',
                'order' => 'ASC'
            ));

            // Past Events
            $past_events = get_posts(array(
                'post_type' => $post_type,
                'showposts' => -1,
                'tax_query' => $past_tax,
                'orderby' => 'meta_value',
                'meta_key' => '_event_date_start',
                'order' => 'DSC'
            ));

            $future_nr = count( $future_events );
            $past_nr = count( $past_events );

            // echo "Paged: Future events: $future_nr, Past events: $past_nr";

            $mergedposts = array_merge( $future_events, $past_events ); //combine queries

            $postids = array();
            foreach( $mergedposts as $item ) {
                $postids[] = $item->ID; //create a new query only of the post ids
            }
            $uniqueposts = array_unique( $postids ); //remove duplicate post ids

            $query_args['orderby'] = 'post__in';
            $query_args['post__in'] = $uniqueposts;

            // Remove Tax query
            if ( isset( $query_args['tax_query'] ) ) {
                unset($query_args['tax_query']);

            }
        
        }

    }

    // Offset 
    if ( $offset > 0 ) {

        if ( isset( $post__in ) && is_array( $post__in ) ) {
            $query_args['post__in'] = array_slice( $query_args['post__in'], $offset );
        } else {
            $fake_args = $query_args;
            $fake_args['posts_per_page'] = $offset;
            $fake_query = new WP_Query( $fake_args );
            $not_in_main_query = array();
            if ( $fake_query->have_posts() ) {
                while ( $fake_query->have_posts() ) {
                    $fake_query->the_post();
                    $not_in_main_query[] = get_the_ID();
                }
            } 
            // Restore original Post Data 
            wp_reset_postdata();
            
            if ( isset( $post__not_in ) && is_array( $post__not_in ) ) {
                $not_in_main_query = array_merge( $post__not_in, $not_in_main_query );
            }
            $query_args['post__not_in'] = $not_in_main_query;
        }

    }

    return $query_args;

}
endif;


/* ==================================================
  Get image BG
================================================== */
if ( ! function_exists( 'epron_get_image_bg' ) ) :
function epron_get_image_bg( $post_id, $thumb_size, $lazy = false, $image_id = false ) {

    // Return arguments 
    $args = array(
        'src' => '',
        'lazy_src' => '',
        'class' => ''
    );

    // Lazy effect is disabled in backend 
    if ( is_admin() && $lazy === true ) {
        $lazy = false;
    } 
    
    if ( $image_id === false && has_post_thumbnail( $post_id ) ) {
        $is_image = true;
        $image_id = get_post_thumbnail_id( $post_id );
    } else if ( $image_id !== false && wp_get_attachment_image_src( $image_id ) ) {
        $is_image = true;
    } else {
        $is_image = false;
    }

    // Get theme options 
    $epron_opts = epron_opts();

    if ( $is_image ) {
        $img_src = wp_get_attachment_image_src( $image_id, $thumb_size );

        // Image loading "lazyload" 
        if ( ( $lazy === true ) and ( $epron_opts->get_option( 'lazyload' ) === 'on' ) ) {
            $img_placeholder = epron_get_placeholder_src( $thumb_size );

            $args['lazy_src'] = $img_src[0];
            $args['src'] = 'background-image:url(' . $img_placeholder . ');';
            $args['class'] = 'lazy';
           
        } else {
            $args['src'] = 'background-image:url(' . $img_src[0] . ');';
        }

    } else {
        $img_src = epron_get_placeholder_src( $thumb_size );
       $args['src'] = 'background-image:url(' . $img_src . ');';
    }    
    return $args;
}
endif;


/* ==================================================
  Get image
  ver 1.1.0
================================================== */
if ( ! function_exists( 'epron_get_image' ) ) :
function epron_get_image( $post_id, $thumb_size, $classes, $lazy = false, $image_id = false, $srcset = true ) {

    // Variables 
    $output = '';
    $srcset = '';

    // Get Options 
    $epron_opts = epron_opts();

    // Lazy effect is disabled in backend 
    if ( is_admin() && $lazy === true ) {
        $lazy = false;
    } 
    
    // is image 
    if ( $image_id === false && has_post_thumbnail( $post_id ) ) {
        $is_image = true;
        $image_id = get_post_thumbnail_id( $post_id );
    } else if ( $image_id !== false && wp_get_attachment_image_src( $image_id ) ) {
        $is_image = true;
    } else {
        $is_image = false;
    }

    // Get theme options 
    $epron_opts = epron_opts();

    if ( $is_image ) {
        $img_src = wp_get_attachment_image_src( $image_id, $thumb_size );

        // Get SRC Set 
        if ( $srcset ) {
            $srcset = epron_get_srcset_sizes( $image_id, $thumb_size );
        }

        // Image loading "lazyload" 
        if ( ( $lazy === true ) and ( $epron_opts->get_option( 'lazyload' ) === 'on' ) ) {
            $img_placeholder = epron_get_placeholder_src( $thumb_size );
            $output = '<img class="lazy ' . esc_attr( $classes ) . '" src="' . esc_url( $img_placeholder ) . '" data-src="' . esc_attr( $img_src[0] ) . '" width="' . esc_attr( $img_src[1] ) . '" height="' . esc_attr( $img_src[2] ) . '" ' .  $srcset  . ' alt="' . esc_attr__( 'Post Image', 'epron' ) . '" >';
        } else {
            $output = '<img class="' . esc_attr( $classes ) . '" src="' . esc_url( $img_src[0] ) . '" width="' . esc_attr( $img_src[1] ) . '" height="' . esc_attr( $img_src[2] ) . '" ' .  $epron_opts->esc( $srcset )  . ' alt="' . esc_attr__( 'Post Image', 'epron' ) . '" >';
        }

    } else {
        $img_src = epron_get_placeholder_src( $thumb_size );

        $output = '<img src="' . esc_url( $img_src ) . '" alt="' . esc_attr__( 'Post Image', 'epron' ) . '" class="' . esc_attr( $classes ) . '">';
    }    

    return $output;
}
endif;


/* ==================================================
   Get placeholder src
   ver 1.1.0
================================================== */
if ( ! function_exists( 'epron_get_placeholder_src' ) ) :
function epron_get_placeholder_src( $size ) {
    global $_wp_additional_image_sizes;

    $sizes = array();

    foreach ( get_intermediate_image_sizes() as $_size ) {
        if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
            $sizes[ $_size ]['size'] = $_size;
            $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
            $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
            $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array(
                'size'   => $_size,
                'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
            );
        }
    }

    if ( ! isset( $sizes[ $size ] ) ) {
        $size = 'content';
    } else if ( $sizes[ $size ]['size'] === 'large' ) {
        $size = 'large';
    } else {
        $w = $sizes[ $size ]['width'];
        $h = $sizes[ $size ]['height'];
        $size = "{$w}x{$h}";
    }

    
    return $img = get_template_directory_uri() . '/images/no-thumb/' . esc_attr( $size ) . '.png';
}
endif;


/* ==================================================
   Get Srcset Sizes
   Returns the srcset and sizes parameters or an empty string
================================================== */
if ( ! function_exists( 'epron_get_srcset_sizes' ) ) :
function epron_get_srcset_sizes( $image_id, $thumb_size ) {
    
        $output = '';
        // Check if wp_get_attachment_image_srcset is defined, it was introduced only in WP 4.4 
        if ( function_exists('wp_get_attachment_image_srcset') ) {
            $thumb_srcset = wp_get_attachment_image_srcset( $image_id, $thumb_size );
            $thumb_sizes = wp_get_attachment_image_sizes( $image_id, $thumb_size );
            if ( $thumb_srcset !== false && $thumb_sizes !== false ) {
                $output = ' srcset="' . $thumb_srcset . '" sizes="' . $thumb_sizes . '"';
            }
            
        }

        return $output;
    }
endif;


/* ==================================================
  Get Soundcloud Iframe
  Generate embed Soundcloud iframe based on link
================================================== */
if ( ! function_exists( 'epron_get_sc_iframe' ) ) :
function epron_get_sc_iframe( $url = '', $height = '400' ) {

    if ( $url !== '' ) {

        // Get the JSON data of song details with embed code from SoundCloud oEmbed 
        $response = wp_remote_get( 'http://soundcloud.com/oembed?format=js&url=' . esc_attr( $url ) . '&iframe=true', array(
            'timeout' => 120,
            'sslverify' => false,
            'user-agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0'
        ));

        if ( is_wp_error( $response ) ) {
             // error handling
            $error_message = $response->get_error_message();
            return esc_html__( "Something went wrong:", 'epron' ) . $error_message;
        }

        $request_output = wp_remote_retrieve_body( $response );
        if ( $request_output === '' ) {
            return esc_html__( "Something went wrong", 'epron' );
        }
        
        // Clean the Json to decode 
        $decodeiFrame=substr( $request_output, 1, -2 );
        // json decode to convert it as an array 
        $jsonObj = json_decode( $decodeiFrame );

        return str_replace('height="400"', 'height="' . esc_attr( $height ) . '"', $jsonObj->html);
    }
    return false;
}
endif;


/* ==================================================
  Get bandcamp Iframe
  Generate embed Bandcamp iframe based on Wordpress.com shortcode 
================================================== */
if ( ! function_exists( 'epron_get_bandcamp_iframe' ) ) :
function epron_get_bandcamp_iframe( $code = '', $height = '400' ) {


    if ( $code !== '' and strpos( $code, '[bandcamp ') !== false )  {

        $defaults = array(
             'width'     => '100%',
             'height'    => '470',
             'album'     => '0',
             'size'      => 'large',
             'bgcol'     => 'ffffff',
             'linkcol'   => '0687f5',
             'tracklist' => 'false',
             'minimal'   => 'true',
             'artwork'   => 'small'
        );

        $code = str_replace( array( '[bandcamp ', ']' ),'', $code );

        $code_a = explode( ' ', $code );
        
        $bandcamp_params = array();
        foreach ( $code_a as $key => $value ) {
            $temp_param_a = explode( '=' , $value );
            $bandcamp_params[ $temp_param_a[0] ] = $temp_param_a[1];
        }

        // Set default arguments
        $bandcamp_params = array_merge( $defaults, $bandcamp_params );

        return '<iframe style="border: 0; width: 100%; max-width:700px" src="https://bandcamp.com/EmbeddedPlayer/album=' . esc_attr( $bandcamp_params['album'] ) . '/size=' . esc_attr( $bandcamp_params['size'] ) . '/bgcol=' . esc_attr( $bandcamp_params['bgcol'] ) . '/artwork=' . esc_attr( $bandcamp_params['artwork'] ) . '/linkcol=' . esc_attr( $bandcamp_params['linkcol'] ) . '/tracklist=' . esc_attr( $bandcamp_params['tracklist'] ) . '/transparent=true/" seamless></iframe>';

    }

    return esc_html__( 'Error: Please enter correct Bandcamp code', 'epron' );
}
endif;


/* ==================================================
  get Spotify Iframe
  Generate embed Spotify iframe based on link 
================================================== */
if ( ! function_exists( 'epron_get_spotify_iframe' ) ) :
function epron_get_spotify_iframe( $url = '', $height = '400' ) {

    if ( $url !== '' ) {
        $url = str_replace( 'https://open.spotify.com/', '', $url );
        $url = stristr( $url, '?', true );
  
        return '<iframe src="https://open.spotify.com/embed/' . esc_attr( $url ) . '" width="100%" height="' . esc_attr( $height ) . '" frameborder="0" allowtransparency="true"></iframe>';

    }

    return false;
}
endif;


/* ==================================================
  Format Number 
================================================== */
if ( ! function_exists( 'epron_format_number' ) ) :
 function epron_format_number($num) {

    if ( $num >= 1000000 ) {
        $num = number_format_i18n($num / 1000000, 1) . 'm';
    } elseif ( $num >= 10000 )  {
        $num = number_format_i18n($num / 1000, 1) . 'k';
    } else {
        $num = number_format_i18n($num);
    }
    return $num;
}
endif;


/* ==================================================
  Get taxonomies 
================================================== */
if ( ! function_exists( 'epron_get_taxonomies' ) ) :
function epron_get_taxonomies( $atts = array() ) {

    if ( empty( $atts ) ) {
        return false;
    }

    $categories = get_the_terms( $atts['id'], $atts['tax_name'] );
    $output = '';
    if ( ! empty( $categories ) ) {
        $count = 1;
        foreach( $categories as $category ) {
            if ( $atts['link'] ) {
                $output .= '<a class="cat cat-' . esc_attr( $category->term_id ) . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr__( 'View posts from category', 'epron' ) . '"><span class="cat-inner">' . esc_html( $category->name ) . '</span></a>' . esc_attr ( $atts['separator'] );
            } else {
                $output .= '<span class="cat cat-' . esc_attr( $category->term_id ) . '"><span class="cat-inner">' . esc_html( $category->name ) . '</span></span>' . esc_attr ( $atts['separator'] );
            }
            if ( $atts['limit'] !== -1 && $count === $atts['limit'] ) {
                break;
            }
            $count++;
        } 
        if ( $atts['show_count'] && count( $categories ) > $atts['limit'] ) {

            $counts_nr = '<span class="cats-count">+' . esc_attr( count( $categories ) - $count ) . '</span>';
        } else {
            $counts_nr = '';
        }
        return trim( $output, $atts['separator'] ) . $counts_nr;
    }
    return false;
}
endif;


/* ==================================================
  Content Loader 
================================================== */
if ( ! function_exists( 'epron_content_loader' ) ) :
function epron_content_loader( $color = '' ) {

    $color_scheme = get_theme_mod( 'color_scheme', 'dark' );

    $loader = '<div class="content-ajax-loader"><div class="content__loader"></div></div>';
    if ( has_filter('epron_change_content_loader') ) {
        $loader = apply_filters( 'epron_change_content_loader', $loader );
    }
    return  $loader;
}
endif;


/* ==================================================
  Content Loader Small
================================================== */
if ( ! function_exists( 'epron_content_loader_small' ) ) :
function epron_content_loader_small( $color = '' ) {

    $color_scheme = get_theme_mod( 'color_scheme', 'dark' );

    $loader = '<div class="content-ajax-loader small"><div class="content__loader"></div></div>';
    if ( has_filter('epron_change_content_loader_small') ) {
        $loader = apply_filters( 'epron_change_content_loader_small', $loader );
    }
    return  $loader;
}
endif;


/* ==================================================
  Comments List 
================================================== */
if ( ! function_exists( 'epron_comments' ) ) :
function epron_comments( $comment, $atts, $depth ) {

    $epron_opts = epron_opts();

    $GLOBALS['comment'] = $comment; 

    // Date format
    $date_format = 'd/m/y';

    if ( $epron_opts->get_option( 'custom_comment_date' ) ) {
        $date_format = $epron_opts->get_option( 'custom_comment_date' );
    }
    ?>

    <!-- Comment -->
    <li id="li-comment-<?php comment_ID() ?>" <?php comment_class( 'theme_comment' ); ?>>
        <article id="comment-<?php comment_ID(); ?>">
            <div class="avatar-wrap">
                <?php echo get_avatar( $comment, '100' ); ?>
            </div>
            <div class="comment-meta">
                <h5 class="author"><?php comment_author_link(); ?></h5>
                <p class="date"><?php comment_date( $date_format ); ?> <span class="reply"><?php comment_reply_link( array_merge( $atts, array( 'reply_text' => '<span class="icon icon-reply-all"></span>', 'depth' => $depth, 'max_depth' => $atts['max_depth'] ) ) ); ?></span></p>
            </div>
            <div class="comment-body">
                <?php comment_text(); ?>
                <?php if ( $comment->comment_approved === '0' ) : ?>
                <p class="message info"><?php esc_html_e( 'Your comment is awaiting moderation.', 'epron' ); ?></p>
                <?php endif; ?> 
            </div>
        </article>
<?php 
}
endif;


/* ==================================================
  Tag Cloud Filter 
================================================== */
if ( ! function_exists( 'epron_tag_cloud_filter' ) ) :
function epron_tag_cloud_filter( $atts = array() ) {
   $atts['smallest'] = 13;
   $atts['largest'] = 13;
   $atts['unit'] = 'px';
   return $atts;
}
add_filter( 'widget_tag_cloud_args', 'epron_tag_cloud_filter', 90 );
endif;


/* ==================================================
  Contact Form 7 Custom Loader 
================================================== */
if ( ! function_exists( 'epron_wpcf7_ajax_loader' ) ) :
function epron_wpcf7_ajax_loader () {
    return  get_template_directory_uri() . '/images/loader-dark.svg';
}
add_filter( 'wpcf7_ajax_loader', 'epron_wpcf7_ajax_loader' );
endif;


/* ==================================================
  WP Title
  Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view. 
================================================== */
if ( ! function_exists( 'epron_wp_title' ) ) :
function epron_wp_title( $title, $sep ) {
    global $paged, $page;

    if ( is_feed() ) {
        return $title;
    }

    // Add the site name.
    $title .= get_bloginfo( 'name', 'display' );

    // Add the site description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title = "$title $sep $site_description";
    }

    // Add a page number if necessary.
    if ( $paged >= 2 || $page >= 2 ) {
        $title = esc_html( "$title $sep " . sprintf( esc_html__( 'Page %s', 'epron' ), max( $paged, $page ) ) );
    }

    return $title;
}
add_filter( 'wp_title', 'epron_wp_title', 10, 2 );
endif;


/* ==================================================
  Add Body Classes 
================================================== */
if ( ! function_exists( 'epron_body_class' ) ) :
function epron_body_class( $classes ) {
    
    global $wp_query, $post, $paged;
    
    // Get theme options 
    $epron_opts = epron_opts();

    // Sticky header
    if ( get_theme_mod( 'sticky_header', '0' ) === true ) {
        $classes[] = 'sticky-header';
    }

    // Top button
    if ( get_theme_mod( 'top_button', '0' ) === true ) {
        $classes[] = 'is-top-button';
    }

    // Loader
    if ( get_theme_mod( 'loader', '0' ) === true ) {
        $classes[] = 'is-loader';
    }

    // Add body classes depends on pages options 
    if ( isset( $wp_query->post->ID ) ) {
        $overlay_header = get_post_meta( $wp_query->post->ID, '_overlay_header', true );
        $hero_style = get_post_meta( $wp_query->post->ID, '_hero_layout', true );

        // Hero style
        if ( ! isset( $hero_style ) || $hero_style === '' ) {
            $classes[] = 'hero-default';
        } else {
            $classes[] = 'hero-' . $hero_style;
        }

        // Overlay header
        if ( $overlay_header === 'yes' ) {
            $classes[] = 'overlay-header';
        }
    }
    if ( 
        ( class_exists( 'WooCommerce' ) && is_product() ) or 
        ( is_author() ) or 
        ( is_404() )
    ) {
        $classes[] = 'hero-simple';
    }

    // Small or normal site size
    if ( get_theme_mod( 'site_size', 'small' ) === 'small' ) {
        $classes[] = 'small-site';
    }

    $color_scheme = get_theme_mod( 'color_scheme', 'dark' );
    $color_scheme .= '-scheme';

    $classes[] = $color_scheme;
    
    // Sticky Sidebar
    if ( get_theme_mod( 'sticky_sidebar', '0' ) === true ) {
        $classes[] = 'sticky-sidebars';
    }
    
    // Lazyload
    if ( $epron_opts->get_option( 'lazyload' ) === 'on' ) {
        $classes[] = 'lazyload';
    }

    return $classes;
     
}
add_filter( 'body_class', 'epron_body_class' );
endif;


/* ==================================================
  Facebook Share Options 
================================================== */
if ( ! function_exists( 'epron_share_options' ) ) :
function epron_share_options() {
    global $wp_query; 

    if ( is_single() || is_page() ) { 

        $is_fb_sharing = get_post_meta( $wp_query->post->ID, '_fb_sharing', true );

        if ( isset( $is_fb_sharing  ) && $is_fb_sharing === true ) {
            
            // Video 
            $share_video = get_post_meta( $wp_query->post->ID, '_share_video', true );
            if ( isset( $share_video ) && $share_video !== '' ) {
                echo '<meta property="og:url" content="' . esc_attr( $share_video ) . '"/>' . "\n";     
            } else {
                echo '<meta property="og:url" content="' . esc_url( get_permalink( $wp_query->post->ID ) ) . '"/>' . "\n";
            }
            
            // Title 
            $share_title = get_post_meta( $wp_query->post->ID, '_share_title', true );
            if ( isset( $share_title ) && $share_title !== '' ) {
                 echo "\n" .'<meta property="og:title" content="' . esc_attr( $share_title ) . '"/>' . "\n";     
            } else {
                // Site name 
                echo "\n" .'<meta property="og:title" content="' . esc_attr( get_bloginfo('name') ) . '"/>' . "\n";     
            }

            // Description 
            $share_description = get_post_meta( $wp_query->post->ID, '_share_description', true );
            if ( isset( $share_description ) && $share_description !== '' ) {
                 echo "\n" .'<meta property="og:description" content="' . esc_attr( $share_description ) . '"/>' . "\n";     
            }

            // Image 
            $share_image = get_post_meta( $wp_query->post->ID, 'share_image', true );
            if ( isset( $share_image ) ) {
                $image_attributes = wp_get_attachment_image_src( $share_image, 'full' );
                if ( $image_attributes ) {
                    echo "\n" .'<meta property="og:image" content="' . esc_attr( $image_attributes[0] ) . '"/>' . "\n";
                }
            }
        }

    }
}
add_action( 'wp_head', 'epron_share_options' ); 
endif;


/* ==================================================
  WPML Language Selector 
================================================== */
if ( ! function_exists( 'epron_languages_list' ) ) :
function epron_languages_list( $id = '', $display ){
    if ( function_exists( 'icl_get_languages' ) ) {

        $languages = icl_get_languages( 'skip_missing=0&orderby=code' );
        if ( ! empty( $languages ) ) {
            if ( $id !== '' ) {
                echo '<div id="' . esc_attr($id) . '" class="lang-selector"><ul>';
            } else {
                 echo '<div class="lang-selector"><ul>';
            }
            foreach($languages as $l){
                echo '<li>';

                if ( $display === 'flags' ||  $display === 'language_codes_flags'  ) {
                    if ( $l['country_flag_url'] ) {
                        if ( ! $l[ 'active' ] ) {
                            echo '<a href="'. esc_url( $l['url'] ) . '">';
                        }
                        echo '<img src="'.esc_url($l['country_flag_url']).'" height="12" alt="'.esc_attr($l['language_code']).'" width="18" />';
                        if ( ! $l['active'] ) {
                            echo '</a>';
                         }
                     }

                }

                if ( $display !== 'flags' ) {
                    if ( ! $l[ 'active' ] ) {
                        echo '<a href="'. esc_url( $l['url'] ) . '">';
                    }
                    echo esc_attr( $l['language_code'] );

                    if ( ! $l['active'] ) {
                        echo '</a>';
                    }
                    echo '</li>';
                }
            }
            echo '</ul></div>';
        }
    }
}
endif;