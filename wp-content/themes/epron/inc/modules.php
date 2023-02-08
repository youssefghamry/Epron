<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            modules.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ==================================================
  Simple Modle 
================================================== */
if ( ! function_exists( 'epron_simple_module' ) ) :
function epron_simple_module( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'permalink'     => '#',
        'title'         => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );

    // Render module 
    $html .= '
        <div class="module module-simple dark-bg ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <a href="' . esc_url( $permalink ) . '" class="module-link">
                    ' . epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy ) . '
                </a>
                <div class="module-info-container">
                    <div class="module-info">
                        <h3 class="post-title">' . $epron_opts->esc( $title ) . '</h3>
                    </div>
                </div>
            </article>
        </div>
    ';

    return $html;
    
}
endif;


/* ==================================================
  Module 1 
================================================== */
if ( ! function_exists( 'epron_module1' ) ) :
function epron_module1( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'permalink'     => '#',
        'posts_cats'    => '',
        'title'         => '',
        'date'          => '',
        'author'        => '',
        'excerpt'       => true,
        'readmore'      => '',
        'placeholder'   => true,
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );

    // Excerpt 
    $short_desc = get_post_meta( $post_id, '_short_desc', true );

    if ( $short_desc !== '' ) {
        $excerpt = do_shortcode( $short_desc );
    } elseif ( has_excerpt( $post_id ) ) {
        $excerpt = '<div class="short-excerpt">' . get_the_excerpt( $post_id ) . '</div>';
    } else {
        ob_start();
        the_content($post_id);
        $content_output = ob_get_clean();
        $excerpt = $content_output;
    }

    if ( ! empty( $readmore ) ) {
        $readmore = ' <a class="readmore" href="' . esc_url( $permalink ) . '">' . esc_html__( 'Read More', 'epron' ) . '</a>';
    }
    if ( $placeholder ) {
        $img = epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy, $image_id = false );
    } else if ( has_post_thumbnail( $post_id ) ) {
        $img = epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy, $image_id = false );
    } else {
        $img = '';
        $classes .= ' no-thumb';
    }

    if ( $excerpt !== '' ) {
        $excerpt = '<div class="module-excerpt">' . $epron_opts->esc( $excerpt ) . '</div>';
    } else {
        $excerpt = '';
    }

    // Render module 
    $html .= '
        <div class="module module-1 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <h2 class="post-title"><a href="' . esc_url( $permalink ) . '">' . $epron_opts->esc( $title ) . '</a></h2>';
                if (function_exists('epron_featured') ) {
                    $html .= epron_featured( get_the_ID() );
                }
                $html .= '<div class="module-info-container-wrap">
                     ' . $epron_opts->esc( $excerpt ) . '
                    <ul class="module-meta none">
                       <li class="module-date">' . $epron_opts->esc( $date ) . '</li>
                       <li class="module-cats">' . $epron_opts->esc( $posts_cats ) . '</li>
                       <li class="module-more">' . $epron_opts->esc( $readmore ) . '</li>
                    </ul>
                </div>
            </article>
        </div>';

    return $html;
    
}
endif;


/* ==================================================
  Module 2 
================================================== */
if ( ! function_exists( 'epron_module2' ) ) :
function epron_module2( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'ratings'       => true,
        'permalink'     => '#',
        'posts_cats'    => '',
        'title'         => '',
        'date'          => '',
        'author'        => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );

    // Render module 
    $html .= '
        <div class="module module-2 dark-bg ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <a href="' . esc_url( $permalink ) . '" class="module-link">
                    ' . epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy ) . '
                </a>
                <div class="module-info-container">
                    <div class="module-info">
                        <div class="post-meta">
                            <div class="post-cats cats-style">' . $epron_opts->esc( $posts_cats ) . '</div><div class="post-date">' . $epron_opts->esc( $date ) . '</div></div>
                        <h3 class="post-title">' . $epron_opts->esc( $title ) . '</h3>
                    </div>
                </div>
            </article>
        </div>
    ';

    return $html;
    
}
endif;


/* ==================================================
  Module 3 
================================================== */
if ( ! function_exists( 'epron_module3' ) ) :
function epron_module3( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'ratings'       => true,
        'permalink'     => '#',
        'posts_cats'    => '',
        'title'         => '',
        'date'          => '',
        'author'        => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );

    // Render module 
    $html .= '
        <div class="module module-3 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <a href="' . esc_url( $permalink ) . '" class="module-link">
                    ' . epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy ) . '
                </a>
                <div class="module-info-container overlay-gradient-1">
                    <div class="module-info">
                        <div class="post-meta">
                            <div class="post-cats cats-style">' . $epron_opts->esc( $posts_cats ) . '</div><div class="post-date">' . $epron_opts->esc( $date ) . '</div>
                        </div>
                        <h3 class="post-title">' . $epron_opts->esc( $title ) . '</h3>
                    </div>
                </div>
            </article>
        </div>
    ';

    return $html;
    
}
endif;


/* ==================================================
  Release Module 1 
================================================== */
if ( ! function_exists( 'epron_release_module1' ) ) :
function epron_release_module1( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'permalink'     => '#',
        'title'         => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );
    
    if ( ! $post_id ) {
        return;
    }
    $track_id = get_post_meta( $post_id, '_track_id', true );
    $tracks_ids = get_post_meta( $post_id, '_tracks_ids', true );
    $badge = get_post_meta( $post_id, '_badge', true );
    $tip_text = get_post_meta( $post_id, '_tooltip_text', true );
    isset( $tip_text ) ?: $tip_text = '';
    $tip_title = get_post_meta( $post_id, '_tooltip_title', true );
    isset( $tip_title ) ?: $tip_title = '';

    // Render module 
    $html .= '
        <div class="module module-release-1 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <div class="module-thumb-block">
                    <a href="' . esc_url( $permalink ) . '" class="thumb-slide tip">
                        <span class="thumbs-wrap">';

                            // Old version thumb ID
                            $thumb_a = null;
                            $thumb_b = null;
                            $thumb_a_id = get_post_meta( $post_id, '_release_image', true );
                            $thumb_b_id = get_post_meta( $post_id, '_release_image_b', true );

                            if ( isset( $thumb_a_id ) && $thumb_a_id !== '' ) {
                                $thumb_a = epron_get_image( false, $thumb_size, 'thumb--a', false, $thumb_a_id );
                            } else if ( has_post_thumbnail( $post_id ) ) {
                                // Featured image
                                $thumb_a = epron_get_image( $post_id, $thumb_size, 'thumb--a', false, false );
                            }

                            if ( isset( $thumb_b_id ) && $thumb_b_id !== '' ) {
                                $thumb_b = epron_get_image( false, $thumb_size, 'thumb--b', false, $thumb_b_id );
                            } else {
                                // Featured image
                                $thumb_b = $thumb_a;
                            }

                            $html .= $thumb_a . $thumb_b;
                        $html .= '</span>';
                        
                        if ( $tip_text !== '' && $tip_title !== '' ) {

                            $html .= '<div class="tip-content hidden"><p><span>' . wp_kses( $tip_title, array() ) . '</span>' . wp_kses( $tip_text, array() ) . '</p></div>';

                        }


                    $html .= '
                    </a>
                    <div class="module-info-container">
                        <div class="module-info">
                            <h3 class="post-title"><a href="' . esc_url( $permalink ) . '">' . $epron_opts->esc( $title ) . '</a></h3>
                            <div class="post-meta">
                                <div class="post-cats">';

                                // Get taxonomies 
                                if ( function_exists( 'epron_get_taxonomies' ) ) {
                                    $tax_args = array(
                                        'id'         => $post_id,
                                        'tax_name'   => 'wp_release_genres',
                                        'separator'  => ' · ',
                                        'link'       => true,
                                        'limit'      => 2,
                                        'show_count' => false
                                    );
                                    $html .= epron_get_taxonomies( $tax_args );
                                }

                                $html .= '</div>
                            </div>
                        </div>
                    </div>';
                    if ( !empty( $badge ) ) {
                        $html .='<span class="badge ' . esc_attr( $badge ) . '">' . esc_html( $badge ) . '</span>';
                    }
            $html .= '</div>
            </article>
        </div>
    ';

    return $html;
    
}
endif;



/* ==================================================
  Release Module 2 (SINGLE)
================================================== */
if ( ! function_exists( 'epron_release_module2' ) ) :
function epron_release_module2( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'permalink'     => '#',
        'title'         => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );
    
    if ( ! $post_id ) {
        return;
    }
    $track_id = get_post_meta( $post_id, '_track_id', true );
    $tracks_ids = get_post_meta( $post_id, '_tracks_ids', true );
    $badge = get_post_meta( $post_id, '_badge', true );
    $tip_text = get_post_meta( $post_id, '_tooltip_text', true );
    isset( $tip_text ) ?: $tip_text = '';
    $tip_title = get_post_meta( $post_id, '_tooltip_title', true );
    isset( $tip_title ) ?: $tip_title = '';

    // Render module 
    $html .= '
        <div class="module module-release-1 single-module ' . esc_attr( $classes ) . '">
            <div class="module-thumb-block">
                <a href="' . esc_url( $permalink ) . '" class="thumb-slide tip">
                    <span class="thumbs-wrap">';

                        // Old version thumb ID
                        $thumb_a = null;
                        $thumb_b = null;
                        $thumb_a_id = get_post_meta( $post_id, '_release_image', true );
                        $thumb_b_id = get_post_meta( $post_id, '_release_image_b', true );

                        if ( isset( $thumb_a_id ) && $thumb_a_id !== '' ) {
                            $thumb_a = epron_get_image( false, $thumb_size, 'thumb--a', false, $thumb_a_id );
                        } else if ( has_post_thumbnail( $post_id ) ) {
                            // Featured image
                            $thumb_a = epron_get_image( $post_id, $thumb_size, 'thumb--a', false, false );
                        }

                        if ( isset( $thumb_b_id ) && $thumb_b_id !== '' ) {
                            $thumb_b = epron_get_image( false, $thumb_size, 'thumb--b', false, $thumb_b_id );
                        } else {
                            // Featured image
                            $thumb_b = $thumb_a;
                        }

                        $html .= $thumb_a . $thumb_b;
                    $html .= '</span>';
                    
                    if ( $tip_text !== '' && $tip_title !== '' ) {

                        $html .= '<div class="tip-content hidden"><p><span>' . wp_kses( $tip_title, array() ) . '</span>' . wp_kses( $tip_text, array() ) . '</p></div>';

                    }


                $html .= '</a>';
                if ( !empty( $badge ) ) {
                    $html .='<span class="badge ' . esc_attr( $badge ) . '">' . esc_html( $badge ) . '</span>';
                }
        $html .= '</div>
        </div>
    ';

    return $html;
    
}
endif;



/* ==================================================
  Event Module 1 (List)
================================================== */
if ( ! function_exists( 'epron_event_module1' ) ) :
function epron_event_module1( $atts = array() ) {

    // Get Options 
    $epron_opts = epron_opts();

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'permalink'     => '#',
        'date_format'   => $epron_opts->get_option( 'events_date_format_list', 'd/m' ),
        'title'         => '',
        'posts_classes' => '',
        'classes'       => ''
    );


    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );
    
    if ( ! $post_id ) {
        return;
    }
    // Event Date 
    $event_time_start = get_post_meta( $post_id, '_event_time_start', true );
    $event_date_start = get_post_meta( $post_id, '_event_date_start', true );
    $event_date_start = strtotime( $event_date_start );
    $event_date_end = strtotime( get_post_meta( $post_id, '_event_date_end', true ) );

    // Event data 
    $location = get_post_meta( $post_id, '_event_location', true );

    // Render module 
    $html .= '
        <div class="module module-event-1 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <div class="left-event-block">
                    <div class="event-date">
                        <div>' . date_i18n( $date_format, $event_date_start ) . '</div>
                    </div>
                    <div class="event-thumb">
                        <a href="' . esc_url( $permalink ) . '" class="module-link">
                            ' . epron_get_image( $post_id, 'thumbnail', 'module-thumb', false ) . '
                        </a>
                    </div>
                    <div class="event-details">
                        <a href="' . esc_url( $permalink ) . '" class="link-layer"></a>
                        <div class="event-name">' . $epron_opts->esc( $title ) . '</div>
                        <div class="event-location">' . $epron_opts->esc( $location ) . '</div>
                    </div>
                </div>
                <div class="plus-button"></div>
            </article>
        </div>
    ';

    return $html;
}
endif;


/* ==================================================
  Event Module 2
================================================== */
if ( ! function_exists( 'epron_event_module2' ) ) :
function epron_event_module2( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'permalink'     => '#',
        'thumb_size'    => 'large',
        'lazy'          => false,
        'title'         => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );

    // Event Date 
    $event_time_start = get_post_meta( $post_id, '_event_time_start', true );
    $event_date_start = get_post_meta( $post_id, '_event_date_start', true );
    $event_date_start = strtotime( $event_date_start );
    $event_date_end = strtotime( get_post_meta( $post_id, '_event_date_end', true ) );

    // Event data 
    $location = get_post_meta( $post_id, '_event_location', true );

    $img = epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy, $image_id = false );
   
    // Render module 
    $html .= '
        <div class="module module-event-2 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <a href="' . esc_url( $permalink ) . '" class="module-link"></a>
                ' . $epron_opts->esc( $img ) . '
                <div class="module-info-container-wrap">
                    <div class="module-info-container">
                        <div class="event-date">
                            <div class="event-day">' . date_i18n( 'd', $event_date_start ) . '</div>
                            <div class="event-month-year">
                                <div class="event-month">' . date_i18n( 'M', $event_date_start ) . '</div>
                                <div class="event-year">' . date_i18n( 'Y', $event_date_start ) . '</div>
                            </div>
                        </div>
                        <div class="event-details">
                            <h2 class="event-name"><a href="' . esc_url( $permalink ) . '">' . $epron_opts->esc( $title ) . '</a></h2>
                             <div class="event-location">' . $epron_opts->esc( $location ) . '</div>
                        </div>
                    </div>
                </div>
            </article>
        </div>';

    return $html;
    
}
endif;


/* ==================================================
  Gallery Module 1 
================================================== */
if ( ! function_exists( 'epron_gallery_module1' ) ) :
function epron_gallery_module1( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'thumb_size'    => 'full',
        'lazy'          => false,
        'permalink'     => '#',
        'title'         => '',
        'date'          => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );
    
    if ( ! $post_id ) {
        return;
    }
    $tip_text = get_post_meta( $post_id, '_tooltip_text', true );
    isset( $tip_text ) ?: $tip_text = '';
    $tip_title = get_post_meta( $post_id, '_tooltip_title', true );
    isset( $tip_title ) ?: $tip_title = '';

    // Render module 
    $html .= '
        <div class="module module-gallery-1 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <div class="module-thumb-block">
                    <a href="' . esc_url( $permalink ) . '" class="tip">';

                            // Old version thumb ID
                            $thumb_a = null;
                            $thumb_a_id = get_post_meta( $post_id, '_gallery_image', true );

                            if ( isset( $thumb_a_id ) && $thumb_a_id !== '' ) {
                                $thumb_a = epron_get_image( false, $thumb_size, 'thumb--a', false, $thumb_a_id );
                            } else if ( has_post_thumbnail( $post_id ) ) {
                                // Featured image
                                $thumb_a = epron_get_image( $post_id, $thumb_size, 'thumb--a', false, false );
                            }

                            $html .= $thumb_a;
                        
                        if ( $tip_text !== '' && $tip_title !== '' ) {

                            $html .= '<div class="tip-content hidden"><p><span>' . wp_kses( $tip_title, array() ) . '</span>' . wp_kses( $tip_text, array() ) . '</p></div>';

                        }

                    $html .= '
                    </a>
                    <div class="module-info-container">
                        <div class="module-info">
                            <h3 class="post-title"><a href="' . esc_url( $permalink ) . '">' . $epron_opts->esc( $title ) . '</a></h3>
                            <div class="post-meta">
                                ' . esc_html( $date ) . '
                            </div>
                        </div>
                    </div>';
            $html .= '</div>
            </article>
        </div>
    ';

    return $html;
    
}
endif;


/* ==================================================
  Artist Module 1
================================================== */
if ( ! function_exists( 'epron_artist_module1' ) ) :
function epron_artist_module1( $atts = array() ) {

    // The defaults will be overridden if set in $atts 
    $defaults = array( 
        'post_id'       => null,
        'permalink'     => '#',
        'thumb_size'    => 'large',
        'lazy'          => false,
        'title'         => '',
        'posts_classes' => '',
        'classes'       => ''
    );

    // Get Options 
    $epron_opts = epron_opts();

    // HTML variable 
    $html = '';

    // If $atts is not array 
    if ( ! is_array( $atts ) ) {
        return false;
    }
    
    // Set default arguments 
    $atts = array_merge( $defaults, $atts );

    // Import variables into the current symbol table from an array 
    extract( $atts, EXTR_PREFIX_SAME, 'module' );

    $img = epron_get_image( $post_id, $thumb_size, 'module-thumb', $lazy, $image_id = false );
   
    // Render module 
    $html .= '
        <div class="module module-artist-1 ' . esc_attr( $classes ) . '">
            <article class="module-inner ' . esc_attr( $posts_classes ) . '">
                <a href="' . esc_url( $permalink ) . '" class="module-link"></a>
                ' . $epron_opts->esc( $img ) . '
                <div class="module-info-container-wrap">
                    <div class="module-info-container">
                        <h2 class="artist-name"><a href="' . esc_url( $permalink ) . '">' . $epron_opts->esc( $title ) . '</a></h2>
                    </div>
                </div>
            </article>
        </div>';

    return $html;
    
}
endif;