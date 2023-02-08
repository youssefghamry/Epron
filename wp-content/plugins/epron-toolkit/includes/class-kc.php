<?php
/**
 * Rascals King Composer Extensions
 *
 *
 * @author Rascals Themes
 * @category Core
 * @package Epron Toolkit
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RascalsKC {

	/**
	 * Private variables
	 */
	private $theme = 'rascals';
	private $theme_panel = null;
	private $supported_cpt =  array( 'post', 'page' );
	private $default_fonts = null;
    private $inline_css = false;


	/**
	 * Rascals CPT Constructor.
	 * @return void
	 */
	public function __construct( $options ) {

		if ( $options !== null ) {
			$this->theme         = $options['theme_name'];
			$this->theme_panel   = $options['theme_panel'];
			$this->supported_cpt = $options['supported_cpt'];
            $this->default_fonts = $options['default_fonts'];
		}

		// Include KC functions
		include_once( RASCALS_TOOLKIT_PATH . '/includes/functions-kc.php' );

        // Remove not supported KC elements
        add_filter( 'kc_add_map', array( $this, 'removeKCElements' ), 1 , 2 );

		// Init
        add_action( 'init', array( $this, 'init' ),99 );

	}


	/**
	 * Initialize class
	 * @return void
	 */
	public function init() {

		// Exit if King Composer plugin is not installed
		if ( ! class_exists( 'KingComposer' ) ) {
			return;
		}

		// Load Frontend scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		// Set default fonts
		$this->setFonts();

		// Add supported custom post types
		$this->addSupportedCPT();

		// Register KC elements
        $this->KC();

	}


	//////////////////////////
	// REGISTER KC ELEMENTS //
	//////////////////////////

	function KC() {

		global $kc;
        
        //get current plugin folder then define the template folder
        $plugin_template = RASCALS_TOOLKIT_PATH . '/includes/kc/templates/';

        //register template folder with KingComposer
        $kc->set_template_path( $plugin_template );

        // Get color scheme
        $color_scheme = get_theme_mod( 'color_scheme', 'dark' );

        //////////////////
        // Single Image //
        //////////////////
        
        // Remove
        $kc->remove_map_param( 'kc_single_image', 'overlay' );

        // Update
        $kc->update_map( 
            'kc_single_image', 
            'params',
            array(
                'general' => array(
                    7 => array(
                        'label'       => esc_html__( 'Click Actions', 'epron-toolkit' ),
                        'name'        => 'on_click_action',
                        'options' => array(
                            ''                 => esc_html__( 'None', 'epron-toolkit'),
                            'op_large_image'   => esc_html__( 'Link to large image', 'epron-toolkit'),
                            'lightbox'         => esc_html__( 'Open Image In Lightbox', 'epron-toolkit'),
                            'lightbox_iframe'  => esc_html__( 'Open Link In Lightbox (Youtube, Vimeo, Soundcloud...)', 'epron-toolkit'),
                            'open_custom_link' => esc_html__( 'Open Custom Link', 'epron-toolkit')
                        )
                    )
                )
            )
        );

        // Add
        $kc->add_map_param( 
            'kc_single_image', 
                array(
                    'label'       => esc_html__( 'Iframe Link', 'epron-toolkit' ),
                    'name'        => 'iframe_link',
                    'type'        => 'text',
                    'description' => esc_html__( 'Enter iframe link.', 'epron-toolkit' ),
                    'relation' => array(
                        'parent'    => 'on_click_action',
                        'show_when' => array( 'lightbox_iframe' )
                    ),
                ), 
        9 );

        // Add
        $kc->add_map_param( 
            'kc_single_image', 
                array(
                    'label'       => esc_html__( 'Tooltip Title', 'epron-toolkit' ),
                    'name'        => 'tip_title',
                    'type'        => 'text',
                    'description' => esc_html__( 'Custom tooltip title.', 'epron-toolkit' ),
                    'relation' => array(
                        'parent'    => 'on_click_action',
                        'show_when' => array( 'lightbox', 'op_large_image', 'open_custom_link', 'lightbox_iframe' )
                    ),
                ), 
        10 );
        $kc->add_map_param( 
            'kc_single_image', 
                array(
                    'label'       => esc_html__( 'Tooltip Text', 'epron-toolkit' ),
                    'name'        => 'tip_text',
                    'type'        => 'textarea',
                    'description' => esc_html__( 'Tooltip text.', 'epron-toolkit' ),
                    'relation' => array(
                        'parent'    => 'on_click_action',
                        'show_when' => array( 'lightbox', 'op_large_image', 'open_custom_link', 'lightbox_iframe' )
                    ),
                ), 
        11 );
        $kc->add_map_param( 
            'kc_single_image', 
                array(
                    'label'       => esc_html__( 'Image Effect', 'epron-toolkit' ),
                    'name'        => 'image_effect',
                    'type'        => 'select',
                    'options' => array(
                        ''            => esc_html__( 'None', 'epron-toolkit'),
                        'overlay'     => esc_html__( 'Overlay', 'epron-toolkit'),
                        'thumb_slide' => esc_html__( 'Thumb Slide', 'epron-toolkit'),
                    ),
                    'description' => esc_html__( 'Tooltip text.', 'epron-toolkit' ),
                    'relation' => array(
                        'parent'    => 'on_click_action',
                        'show_when' => array( 'lightbox', 'op_large_image', 'open_custom_link', 'lightbox_iframe' )
                    ),
                ), 
        14 );
        $kc->add_map_param( 
            'kc_single_image', 
                array(
                    'label'       => esc_html__( 'Hover Image', 'epron-toolkit' ),
                    'name'        => 'image_hover',
                    'type'        => 'attach_image',
                    'description' => esc_html__( 'The picture will be shown when you hover the mouse.', 'epron-toolkit' ),
                    'relation' => array(
                        'parent'    => 'image_effect',
                        'show_when' => array( 'thumb_slide' )
                    ),
                ), 
        15 );
         // Update
        $kc->update_map( 
            'kc_single_image', 
            'params',
            array(
                'general' => array(
                    15 => array(
                        'label'       => esc_html__( 'Icon Overlay', 'epron-toolkit' ),
                        'name'        => 'icon',
                        'description' => esc_html__( 'The icon show on center of overlay laver.', 'epron-toolkit' ),
                        'type'        => 'icon_picker',
                        'value'       => 'fa-plus',
                        'relation'    => array(
                            'parent'    => 'image_effect',
                            'show_when' => 'overlay'
                        ),
                       
                    )
                )
            )
        );
        $kc->add_map_param( 
            'kc_single_image', 
                array(
                    'label'       => esc_html__( 'Badge', 'epron-toolkit' ),
                    'name'        => 'badge',
                    'type'        => 'select',
                    'options' => array(
                        ''     => esc_html__( 'None', 'epron-toolkit'),
                        'new'  => esc_html__( 'New', 'epron-toolkit'),
                        'free' => esc_html__( 'Free', 'epron-toolkit'),
                    ),
                    'description' => esc_html__( 'Select badge. It will be displayed on the image.', 'epron-toolkit' ),
                    
                ), 
        17 );



        ////////////////////
        // ADD NEW BLOCKS //
        ////////////////////
        kc_add_map( array(


            /* Artists Block
                 -------------------------------- */
                'kc_artists_block'    => array(
                    'name'        => esc_html__( 'Artists Block', 'epron-toolkit' ),
                    'description' => esc_html__( 'Display an extremely impressive artists profiles with many beautiful styles.', 'epron-toolkit' ),
                    'icon'        => 'sl-user',
                    'category'    => 'Premium',
                    'params'      => array(
                        'general' => array(
                            array(
                                'label'   => esc_html__( 'Block', 'epron-toolkit' ),
                                'name'    => 'block',
                                'type'    => 'select',
                                'value'   => 'block1',
                                'options' => array(
                                    'block1' => esc_html__( 'Block 1', 'epron-toolkit' ),
                                ),
                                'description'   => esc_html__( 'Select posts block.', 'epron-toolkit' ),
                                'admin_label'   => true
                            ),
                            array(
                                'label'   => esc_html__( 'Artists on Row', 'epron-toolkit' ),
                                'name'    => 'articles_number',
                                'type'    => 'select',
                                'value'   => '2',
                                'options' => array(
                                    '1' => esc_html__( '1 Artist', 'epron-toolkit' ),
                                    '2' => esc_html__( '2 Artists', 'epron-toolkit' ),
                                    '3' => esc_html__( '3 Artists', 'epron-toolkit' ),
                                    '4' => esc_html__( '4 Artists', 'epron-toolkit' ),
                                    '5' => esc_html__( '5 Artists', 'epron-toolkit' )
                                ),
                                'description'   => esc_html__( 'Select number of items per row.', 'epron-toolkit' ),
                                'admin_label'   => false,
                                'relation'    => array(
                                    'parent'    => 'block',
                                    'show_when' => array('block1')
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Ajax Pagination', 'epron-toolkit' ),
                                'name'    => 'pagination',
                                'type'    => 'select',
                                'value'   => '',
                                'options' => array(
                                    ''          => esc_html__( 'None', 'epron-toolkit' ),
                                    'load_more' => esc_html__( 'Load More Button', 'epron-toolkit' ),
                                    'infinite'  => esc_html__( 'Infinite Loading', 'epron-toolkit' ),
                                ),
                                'description'   => esc_html__( 'Select ajax pagination.', 'epron-toolkit' ),
                                'admin_label'   => false,
                                'relation'    => array(
                                    'parent'    => 'block',
                                    'show_when' => array('block1')
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Ajax Filter', 'epron-toolkit' ),
                                'name'    => 'ajax_filter',
                                'type'    => 'select',
                                'value'   => '',
                                'options' => array(
                                    ''                 => esc_html__( 'None', 'epron-toolkit' ),
                                    'on-left'          => esc_html__( 'On Left', 'epron-toolkit' ),
                                    'center'           => esc_html__( 'Center', 'epron-toolkit' ),
                                    'on-right'         => esc_html__( 'On Right', 'epron-toolkit' ),
                                    'multiple-filters' => esc_html__( 'Multiple Filters', 'epron-toolkit' ),
                                ),
                                'description' => esc_html__( 'Display Ajax filter above grid.', 'epron-toolkit' ),
                                'admin_label' => false,
                                'relation'    => array(
                                    'parent'    => 'block',
                                    'show_when' => array('block1' )
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Selection Method', 'epron-toolkit' ),
                                'name'    => 'filter_sel_method',
                                'type'    => 'select',
                                'value'   => 'filter-sel-multiple',
                                'options' => array(
                                    'filter-sel-multiple' => esc_html__( 'Multiple', 'epron-toolkit' ),
                                    'filter-sel-single' => esc_html__( 'Single', 'epron-toolkit' ),
                                ),
                                'description' => esc_html__( 'Select filter selection method.', 'epron-toolkit' ),
                                'admin_label' => false,
                                'relation'    => array(
                                    'parent'    => 'ajax_filter',
                                    'show_when' => array('on-left', 'center', 'on-right', 'multiple-filters')
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Show filters on Start', 'epron-toolkit' ),
                                'name'    => 'show_filters',
                                'type'    => 'select',
                                'value'   => 'no',
                                'options' => array(
                                    'show-filters' => esc_html__( 'Yes', 'epron-toolkit' ),
                                    'hide-filters' => esc_html__( 'No', 'epron-toolkit' ),
                                ),
                                'description' => esc_html__( 'Show filters when page is loaded. Otherwise the filters are shown after clicking the "Filters" button.', 'epron-toolkit' ),
                                'admin_label' => false,
                                'relation'    => array(
                                    'parent'    => 'ajax_filter',
                                    'show_when' => array('multiple-filters')
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                                'name'    => 'color_scheme',
                                'type'    => 'select',
                                'value'   => $color_scheme,
                                'options' => $this->getSchemes(),
                                'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                                'admin_label'   => false
                            ),
                            array(
                                'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                                'name'        => 'classes',
                                'type'        => 'text',
                                'admin_label' => false,
                                'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                            ),
                            array(
                                'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                                'name'        => 'module_classes',
                                'type'        => 'text',
                                'admin_label' => false,
                                'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                            ),
                        ),
                    'filter' => $this->getArtistsFilter(),
                    'styling' => array(
                            array(
                                'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                                'name'    => 'custom_css',
                                'type'    => 'css',
                                'options' => array(
                                    array(
                                        "screens" => "any,1024,999,767,479",
                                        'Box' => array(
                                            array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-videos-block-inner' ),
                                            array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-videos-block-inner' ),
                                        ),
                                        
                                    ),
                                ),
                                'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                            ),
                        ),                  
                       
                    )
                ),  // End of element kc

                
                /* Artists Carousel
                 -------------------------------- */
                'kc_artists_carousel'    => array(
                    'name'        => esc_html__( 'Artists Carousel', 'epron-toolkit' ),
                    'description' => esc_html__( 'Display videos in a nice sliding manner.', 'epron-toolkit' ),
                    'icon'        => 'sl-user',
                    'category'    => 'Premium',
                    'params'      => array(
                        'general' => array(
                            array(
                                'label'   => esc_html__( 'Video Module', 'epron-toolkit' ),
                                'name'    => 'module',
                                'type'    => 'select',
                                'value'   => 'module1',
                                'options' => array(
                                    'module1' => esc_html__( 'Module 1', 'epron-toolkit' ),
                                ),
                                'description'   => esc_html__( 'Select post module.', 'epron-toolkit' ),
                                'admin_label'   => true
                            ),
                            array(
                                'label'   => esc_html__( 'Items per slide', 'epron-toolkit' ),
                                'name'    => 'items_number',
                                'type'    => 'number_slider',
                                'value'   => 3,
                                'options' => array(
                                    'min'        => 1,
                                    'max'        => 10,
                                    'unit'       => '',
                                    'show_input' => false
                                ),
                                'description' => esc_html__( 'The number of items displayed per slide (not apply for auto-height)', 'epron-toolkit' ),
                                'admin_label' => false
                            ),  
                            array(
                                'label'   => esc_html__( 'Items On Tablet?', 'epron-toolkit' ),
                                'name'    => 'tablet',
                                'type'    => 'number_slider',
                                'value'   => 2,
                                'options' => array(
                                    'min'        => 1,
                                    'max'        => 10,
                                    'unit'       => '',
                                    'show_input' => false
                                ),
                                'description' => esc_html__( 'Display number of items per each slide (Tablet Screen)', 'epron-toolkit' ),
                                'admin_label' => false
                            ), 
                            array(
                                'label'   => esc_html__( 'Items On Smartphone?', 'epron-toolkit' ),
                                'name'    => 'mobile',
                                'type'    => 'number_slider',
                                'value'   => 2,
                                'options' => array(
                                    'min'        => 1,
                                    'max'        => 10,
                                    'unit'       => '',
                                    'show_input' => false
                                ),
                                'description' => esc_html__( 'Display number of items per each slide (Mobile Screen)', 'epron-toolkit' ),
                                'admin_label' => false
                            ),
                            array(
                                'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                                'name'    => 'speed',
                                'type'    => 'number_slider',
                                'value'   => 500,
                                'options' => array(
                                    'min'        => 100,
                                    'max'        => 1500,
                                    'unit'       => '',
                                    'show_input' => true
                                ),
                                'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                                'admin_label' => false
                            ),
                            array(
                                'label'       => esc_html__( 'Navigation', 'epron-toolkit' ),
                                'name'        => 'navigation',
                                'type'        => 'toggle',
                                'value'       => '',
                                'description' => esc_html__( 'Display the "Next" and "Prev" buttons.', 'epron-toolkit' ),
                                'admin_label' => true
                            ), 
                            array(
                                'label'         => esc_html__( 'Navigation Style', 'epron-toolkit' ),
                                'name'          => 'nav_style',
                                'type'          => 'select',
                                'value'         => '1',
                                'options'       => array(
                                    'arrows' => esc_html__( 'Arrows', 'epron-toolkit' )
                                ),
                                'description'   => esc_html__( 'Select how navigation buttons display on slide.', 'epron-toolkit' ),
                                'admin_label'   => false,
                                'relation'      => array(
                                    'parent'    => 'navigation',
                                    'show_when' => 'yes'
                                )
                            ), 
                            array(
                                'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                                'name'        => 'pagination',
                                'type'        => 'toggle',
                                'value'       => '',
                                'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                                'admin_label' => true
                            ), 
                            array(
                                'label'       => esc_html__( 'Auto height', 'epron-toolkit' ),
                                'name'        => 'auto_height',
                                'type'        => 'toggle',
                                'value'       => '',
                                'description' => esc_html__( 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.', 'epron-toolkit' ),
                                'admin_label' => true
                            ), 
                            array(
                                'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                                'name'        => 'auto_play',
                                'type'        => 'toggle',
                                'value'       => '',
                                'description' => esc_html__( 'The carousel automatically plays when site loaded', 'epron-toolkit' ),
                                'admin_label' => true
                            ), 
                            array(
                                'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                                'name'    => 'color_scheme',
                                'type'    => 'select',
                                'value'   => $color_scheme,
                                'options' => $this->getSchemes(),
                                'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                                'admin_label'   => false
                            ),
                            array(
                                'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                                'name'        => 'classes',
                                'type'        => 'text',
                                'admin_label' => false,
                                'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                            ),
                            array(
                                'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                                'name'        => 'module_classes',
                                'type'        => 'text',
                                'admin_label' => false,
                                'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                            ),
                        ),
                    'filter' => $this->getArtistsFilter(), 
                       
                    )
                ),  // End of element kc


            /* Stats List
             -------------------------------- */
            'kc_stats'    => array(
                'name'        => esc_html__( 'Stats List', 'epron-toolkit' ),
                'description' => esc_html__( 'Display stats list', 'epron-toolkit' ),
                'icon'        => 'sl-list',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'       => esc_html__('Stats', 'epron-toolkit'),
                            'name'        => 'stats',
                            'type'        => 'textarea',
                            'value'       => "gigs=1200\nhappy peoples=1266\nreleases=2356\ncoffees per year=1076\nred buls per year=2009\nvinyls=3238",
                            'admin_label' => false,
                            'description' => esc_html__( 'Add stats e.g.: coffees per year=1076 (Note: divide stats with linebreaks (Enter)). Minimum 6 stats.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__( 'Timer (s)', 'epron-toolkit' ),
                            'name'        => 'timer',
                            'type'        => 'text',
                            'value'       => '10',
                            'admin_label' => false,
                            'description' => esc_html__( 'Timer in secounds.', 'epron-toolkit' ),
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Button Style' => array(
                                        array( 'property' => 'color', 'label' => 'Text Color', 'selector' => '.kc-stats-inner' ),
                                        array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => '.kc-stats-inner' ),
                                        array( 'property' => 'font-weight', 'label' => 'Font Weight', 'selector' => '.kc-stats-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Margin', 'selector' => '.kc-stats-inner' ),
                                        array( 'property' => 'width', 'label' => 'Column Width', 'selector' => '.kc-stats-inner li' ),
                                    ),
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Gallery Carousel
             -------------------------------- */
            'kc_gallery_carousel'    => array(
                'name'        => esc_html__( 'Gallery Albums Carousel', 'epron-toolkit' ),
                'description' => esc_html__( 'Display gallery albums in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-camera',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Gallery Module', 'epron-toolkit' ),
                            'name'    => 'module',
                            'type'    => 'select',
                            'value'   => 'module1',
                            'options' => array(
                                'module1' => esc_html__( 'Module 1', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select post module.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Image size', 'epron-toolkit' ),
                            'name'    => 'thumb_size',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => $this->getImageSizes( array(
                                    '' => esc_html__( 'Default', 'epron-toolkit' )
                                )
                            ),
                            'description'   => esc_html__( 'Select image size. By default, the image size is set for the selected module. Note: In some modules it is not possible to change image size because it is set permanently.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'   => esc_html__( 'Items per slide', 'epron-toolkit' ),
                            'name'    => 'items_number',
                            'type'    => 'number_slider',
                            'value'   => 3,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'The number of items displayed per slide (not apply for auto-height)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),  
                        array(
                            'label'   => esc_html__( 'Items On Tablet?', 'epron-toolkit' ),
                            'name'    => 'tablet',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Tablet Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Items On Smartphone?', 'epron-toolkit' ),
                            'name'    => 'mobile',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Mobile Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                            'name'    => 'speed',
                            'type'    => 'number_slider',
                            'value'   => 500,
                            'options' => array(
                                'min'        => 100,
                                'max'        => 1500,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Navigation', 'epron-toolkit' ),
                            'name'        => 'navigation',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display the "Next" and "Prev" buttons.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'         => esc_html__( 'Navigation Style', 'epron-toolkit' ),
                            'name'          => 'nav_style',
                            'type'          => 'select',
                            'value'         => '1',
                            'options'       => array(
                                'arrows' => esc_html__( 'Arrows', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select how navigation buttons display on slide.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'      => array(
                                'parent'    => 'navigation',
                                'show_when' => 'yes'
                            )
                        ), 
                        array(
                            'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                            'name'        => 'pagination',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto height', 'epron-toolkit' ),
                            'name'        => 'auto_height',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                            'name'        => 'auto_play',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'The carousel automatically plays when site loaded', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getGalleryFilter(), 
                   
                )
            ),  // End of element kc


            /* Gallery Block
             -------------------------------- */
            'kc_gallery_block'    => array(
                'name'        => esc_html__( 'Gallery Albums', 'epron-toolkit' ),
                'description' => esc_html__( 'Display an extremely impressive gallery with many beautiful styles.', 'epron-toolkit' ),
                'icon'        => 'sl-camera',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Block', 'epron-toolkit' ),
                            'name'    => 'block',
                            'type'    => 'select',
                            'value'   => 'block1',
                            'options' => array(
                                'block1' => esc_html__( 'Block 1', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select posts block.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Items on Row', 'epron-toolkit' ),
                            'name'    => 'articles_number',
                            'type'    => 'select',
                            'value'   => '2',
                            'options' => array(
                                '1' => esc_html__( '1 Item', 'epron-toolkit' ),
                                '2' => esc_html__( '2 Items', 'epron-toolkit' ),
                                '3' => esc_html__( '3 Items', 'epron-toolkit' ),
                                '4' => esc_html__( '4 Items', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select number of items per row.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block1')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Ajax Pagination', 'epron-toolkit' ),
                            'name'    => 'pagination',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''          => esc_html__( 'None', 'epron-toolkit' ),
                                'load_more' => esc_html__( 'Load More Button', 'epron-toolkit' ),
                                'infinite'  => esc_html__( 'Infinite Loading', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select ajax pagination.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block1')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Ajax Filter', 'epron-toolkit' ),
                            'name'    => 'ajax_filter',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''                 => esc_html__( 'None', 'epron-toolkit' ),
                                'on-left'          => esc_html__( 'On Left', 'epron-toolkit' ),
                                'center'           => esc_html__( 'Center', 'epron-toolkit' ),
                                'on-right'         => esc_html__( 'On Right', 'epron-toolkit' ),
                                'multiple-filters' => esc_html__( 'Multiple Filters', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Display Ajax filter above grid.', 'epron-toolkit' ),
                            'admin_label' => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block1' )
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Selection Method', 'epron-toolkit' ),
                            'name'    => 'filter_sel_method',
                            'type'    => 'select',
                            'value'   => 'filter-sel-multiple',
                            'options' => array(
                                'filter-sel-multiple' => esc_html__( 'Multiple', 'epron-toolkit' ),
                                'filter-sel-single'   => esc_html__( 'Single', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Select filter selection method.', 'epron-toolkit' ),
                            'admin_label' => false,
                            'relation'    => array(
                                'parent'    => 'ajax_filter',
                                'show_when' => array('on-left', 'center', 'on-right', 'multiple-filters')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Show filters on Start', 'epron-toolkit' ),
                            'name'    => 'show_filters',
                            'type'    => 'select',
                            'value'   => 'no',
                            'options' => array(
                                'show-filters' => esc_html__( 'Yes', 'epron-toolkit' ),
                                'hide-filters' => esc_html__( 'No', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Show filters when page is loaded. Otherwise the filters are shown after clicking the "Filters" button.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'ajax_filter',
                                'show_when' => array('multiple-filters')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getGalleryFilter(),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-gallery-block-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-gallery-block-inner' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc
            
            
            /* Gallery Images Carousel
             -------------------------------- */
            'kc_gallery_images_carousel'    => array(
                'name'        => esc_html__( 'Gallery Images Carousel', 'epron-toolkit' ),
                'description' => esc_html__( 'Display album images in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-camera',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'       => esc_html__( 'Select Images Album', 'epron-toolkit' ),
                            'name'        => 'album_id',
                            'type'        => 'select',
                            'value'       => 'none',
                            'options'     => $this->getPosts( 'wp_gallery' ),
                            'description' => esc_html__( 'Select album. If there are no albums available, then you can add a album using Gallery menu on the left.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
                            'name'    => 'limit',
                            'type'    => 'number_slider',
                            'value'   => '0',
                            'options' => array(
                                'min'        => 0,
                                'max'        => 40,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'admin_label' => true,
                            'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
                        ),
                        array(
                            'label'   => esc_html__( 'Image size', 'epron-toolkit' ),
                            'name'    => 'thumb_size',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => $this->getImageSizes( array(
                                    '' => esc_html__( 'Default', 'epron-toolkit' )
                                )
                            ),
                            'description'   => esc_html__( 'Select image size. By default, the image size is set for the selected module. Note: In some modules it is not possible to change image size because it is set permanently.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'   => esc_html__( 'Items per slide', 'epron-toolkit' ),
                            'name'    => 'items_number',
                            'type'    => 'number_slider',
                            'value'   => 3,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'The number of items displayed per slide (not apply for auto-height)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),  
                        array(
                            'label'   => esc_html__( 'Items On Tablet?', 'epron-toolkit' ),
                            'name'    => 'tablet',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Tablet Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Items On Smartphone?', 'epron-toolkit' ),
                            'name'    => 'mobile',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Mobile Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                            'name'    => 'speed',
                            'type'    => 'number_slider',
                            'value'   => 500,
                            'options' => array(
                                'min'        => 100,
                                'max'        => 1500,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Navigation', 'epron-toolkit' ),
                            'name'        => 'navigation',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display the "Next" and "Prev" buttons.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'         => esc_html__( 'Navigation Style', 'epron-toolkit' ),
                            'name'          => 'nav_style',
                            'type'          => 'select',
                            'value'         => '1',
                            'options'       => array(
                                'arrows' => esc_html__( 'Arrows', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select how navigation buttons display on slide.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'      => array(
                                'parent'    => 'navigation',
                                'show_when' => 'yes'
                            )
                        ), 
                        array(
                            'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                            'name'        => 'pagination',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto height', 'epron-toolkit' ),
                            'name'        => 'auto_height',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                            'name'        => 'auto_play',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'The carousel automatically plays when site loaded', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                   
                )
            ),  // End of element kc


            /* Gallery Images
             -------------------------------- */
            'kc_gallery_images'    => array(
                'name'        => esc_html__( 'Gallery Images', 'epron-toolkit' ),
                'description' => esc_html__( 'Display images from gallery album.', 'epron-toolkit' ),
                'icon'        => 'sl-camera',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'       => esc_html__( 'Select Images Album', 'epron-toolkit' ),
                            'name'        => 'album_id',
                            'type'        => 'select',
                            'value'       => 'none',
                            'options'     => $this->getPosts( 'wp_gallery' ),
                            'description' => esc_html__( 'Select album. If there are no albums available, then you can add a album using Gallery menu on the left.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Grid Type', 'epron-toolkit' ),
                            'name'    => 'grid_type',
                            'type'    => 'select',
                            'value'   => 'flex',
                            'options' => array(
                                'flex'    => esc_html__( 'Flex Grid', 'epron-toolkit' ),
                                'masonry' => esc_html__( 'Masonry', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select grid type.', 'epron-toolkit' ),
                            'admin_label'   => false,
                        ),
                        array(
                            'label'   => esc_html__( 'Images on Row', 'epron-toolkit' ),
                            'name'    => 'images_number',
                            'type'    => 'select',
                            'value'   => '2',
                            'options' => array(
                                '1' => esc_html__( '1 Image', 'epron-toolkit' ),
                                '2' => esc_html__( '2 Images', 'epron-toolkit' ),
                                '3' => esc_html__( '3 Images', 'epron-toolkit' ),
                                '4' => esc_html__( '4 Images', 'epron-toolkit' ),
                                '5' => esc_html__( '5 Images', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select number of items per row.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'grid_type',
                                'show_when' => array('flex')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
                            'name'    => 'limit',
                            'type'    => 'number_slider',
                            'value'   => '0',
                            'options' => array(
                                'min'        => 0,
                                'max'        => 40,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'admin_label' => true,
                            'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-images-block-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-images-block-inner' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc

            /* Event Countdown
             -------------------------------- */
             'kc_event_countdown'    => array(
                'name'        => esc_html__( 'Event Countdown', 'epron-toolkit' ),
                'description' => esc_html__( 'Display current event countdown.', 'epron-toolkit' ),
                'icon'        => 'sl-clock',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Type', 'epron-toolkit' ),
                            'name'    => 'type',
                            'type'    => 'select',
                            'value'   => 'big',
                            'options' => array(
                                'big'   => esc_html__( 'Big', 'epron-toolkit' ),
                                'small' => esc_html__( 'Small', 'epron-toolkit' ),
                                'small-image' => esc_html__( 'Small with image', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select event countdown type.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                         array(
                            'label'   => esc_html__( 'Select Image', 'epron-toolkit' ),
                            'name'    => 'custom_image_id',
                            'type'    => 'attach_image',
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'type',
                                'show_when' => array( 'small-image' )
                            )
                        ),
                        array(
                            'label'       => esc_html__( 'Select Custom Event?', 'epron-toolkit' ),
                            'name'        => 'is_custom_id',
                            'type'        => 'toggle',
                            'value'       => '',
                            'admin_label' => false,
                        ),
                        array(
                            'label'   => esc_html__( 'Select Event', 'epron-toolkit' ),
                            'name'    => 'custom_id',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => $this->getEvents(),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'is_custom_id',
                                'show_when' => array( 'yes' )
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__( 'Extra Class Name', 'epron-toolkit' ),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Countdown' => array(
                                        array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => '.kc-countdown' ),
                                        array( 'property' => 'color', 'label' => 'Font Color', 'selector' => '.kc-countdown' ),
                                    ),
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-countdown-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-countdown-inner' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Events Block
             -------------------------------- */
            'kc_events_block'    => array(
                'name'        => esc_html__( 'Events Block', 'epron-toolkit' ),
                'description' => esc_html__( 'Display an extremely impressive events blocks with many beautiful styles.', 'epron-toolkit' ),
                'icon'        => 'sl-plane',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Block', 'epron-toolkit' ),
                            'name'    => 'block',
                            'type'    => 'select',
                            'value'   => 'block1',
                            'options' => array(
                                'block1' => esc_html__( 'Block 1 - List', 'epron-toolkit' ),
                                'block2' => esc_html__( 'Block 2 - Grid', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select posts block.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'       => esc_html__( 'Date Divider', 'epron-toolkit' ),
                            'name'        => 'date_divider',
                            'type'        => 'toggle',
                            'value'       => '1',
                            'description' => esc_html__( 'Display date between events.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'   => esc_html__( 'Items on Row', 'epron-toolkit' ),
                            'name'    => 'articles_number',
                            'type'    => 'select',
                            'value'   => '2',
                            'options' => array(
                                '1' => esc_html__( '1 Item', 'epron-toolkit' ),
                                '2' => esc_html__( '2 Items', 'epron-toolkit' ),
                                '3' => esc_html__( '3 Items', 'epron-toolkit' ),
                                '4' => esc_html__( '4 Items', 'epron-toolkit' ),
                                '5' => esc_html__( '5 Items', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select number of items per row.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block2')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Ajax Pagination', 'epron-toolkit' ),
                            'name'    => 'pagination',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''          => esc_html__( 'None', 'epron-toolkit' ),
                                'load_more' => esc_html__( 'Load More Button', 'epron-toolkit' ),
                                'infinite'  => esc_html__( 'Infinite Loading', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select ajax pagination.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block1', 'block2')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Ajax Filter', 'epron-toolkit' ),
                            'name'    => 'ajax_filter',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''                 => esc_html__( 'None', 'epron-toolkit' ),
                                'on-left'          => esc_html__( 'On Left', 'epron-toolkit' ),
                                'center'           => esc_html__( 'Center', 'epron-toolkit' ),
                                'on-right'         => esc_html__( 'On Right', 'epron-toolkit' ),
                                'multiple-filters' => esc_html__( 'Multiple Filters', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Display Ajax filter above grid.', 'epron-toolkit' ),
                            'admin_label' => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block1', 'block2' )
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Selection Method', 'epron-toolkit' ),
                            'name'    => 'filter_sel_method',
                            'type'    => 'select',
                            'value'   => 'filter-sel-multiple',
                            'options' => array(
                                'filter-sel-multiple' => esc_html__( 'Multiple', 'epron-toolkit' ),
                                'filter-sel-single' => esc_html__( 'Single', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Select filter selection method.', 'epron-toolkit' ),
                            'admin_label' => false,
                            'relation'    => array(
                                'parent'    => 'ajax_filter',
                                'show_when' => array('on-left', 'center', 'on-right', 'multiple-filters')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Show filters on Start', 'epron-toolkit' ),
                            'name'    => 'show_filters',
                            'type'    => 'select',
                            'value'   => 'no',
                            'options' => array(
                                'show-filters' => esc_html__( 'Yes', 'epron-toolkit' ),
                                'hide-filters' => esc_html__( 'No', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Show filters when page is loaded. Otherwise the filters are shown after clicking the "Filters" button.', 'epron-toolkit' ),
                            'admin_label' => false,
                            'relation'    => array(
                                'parent'    => 'ajax_filter',
                                'show_when' => array('multiple-filters')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getEventsFilter(),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-events-block-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-events-block-inner' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Events Carousel
             -------------------------------- */
            'kc_events_carousel'    => array(
                'name'        => esc_html__( 'Events Carousel', 'epron-toolkit' ),
                'description' => esc_html__( 'Display events in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-clock',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Post Module', 'epron-toolkit' ),
                            'name'    => 'module',
                            'type'    => 'select',
                            'value'   => 'module2',
                            'options' => array(
                                'module2' => esc_html__( 'Module 1', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select post module.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Image size', 'epron-toolkit' ),
                            'name'    => 'thumb_size',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => $this->getImageSizes( array(
                                    '' => esc_html__( 'Default', 'epron-toolkit' )
                                )
                            ),
                            'description'   => esc_html__( 'Select image size. By default, the image size is set for the selected module. Note: In some modules it is not possible to change image size because it is set permanently.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'   => esc_html__( 'Items per slide', 'epron-toolkit' ),
                            'name'    => 'items_number',
                            'type'    => 'number_slider',
                            'value'   => 3,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'The number of items displayed per slide (not apply for auto-height)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),  
                        array(
                            'label'   => esc_html__( 'Items On Tablet?', 'epron-toolkit' ),
                            'name'    => 'tablet',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Tablet Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Items On Smartphone?', 'epron-toolkit' ),
                            'name'    => 'mobile',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Mobile Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                            'name'    => 'speed',
                            'type'    => 'number_slider',
                            'value'   => 500,
                            'options' => array(
                                'min'        => 100,
                                'max'        => 1500,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Navigation', 'epron-toolkit' ),
                            'name'        => 'navigation',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display the "Next" and "Prev" buttons.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'         => esc_html__( 'Navigation Style', 'epron-toolkit' ),
                            'name'          => 'nav_style',
                            'type'          => 'select',
                            'value'         => '1',
                            'options'       => array(
                                'arrows' => esc_html__( 'Arrows', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select how navigation buttons display on slide.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'      => array(
                                'parent'    => 'navigation',
                                'show_when' => 'yes'
                            )
                        ), 
                        array(
                            'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                            'name'        => 'pagination',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto height', 'epron-toolkit' ),
                            'name'        => 'auto_height',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                            'name'        => 'auto_play',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'The carousel automatically plays when site loaded', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getEventsFilter(), 
                   
                )
            ),  // End of element kc


            /* Releases Carousel
             -------------------------------- */
            'kc_releases_carousel'    => array(
                'name'        => esc_html__( 'Releases Carousel', 'epron-toolkit' ),
                'description' => esc_html__( 'Display music in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-earphones',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Post Module', 'epron-toolkit' ),
                            'name'    => 'module',
                            'type'    => 'select',
                            'value'   => 'module1',
                            'options' => array(
                                'module1' => esc_html__( 'Module 1', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select post module.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Items per slide', 'epron-toolkit' ),
                            'name'    => 'items_number',
                            'type'    => 'number_slider',
                            'value'   => 3,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'The number of items displayed per slide (not apply for auto-height)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),  
                        array(
                            'label'   => esc_html__( 'Items On Tablet?', 'epron-toolkit' ),
                            'name'    => 'tablet',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Tablet Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Items On Smartphone?', 'epron-toolkit' ),
                            'name'    => 'mobile',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Mobile Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                            'name'    => 'speed',
                            'type'    => 'number_slider',
                            'value'   => 500,
                            'options' => array(
                                'min'        => 100,
                                'max'        => 1500,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Navigation', 'epron-toolkit' ),
                            'name'        => 'navigation',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display the "Next" and "Prev" buttons.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'         => esc_html__( 'Navigation Style', 'epron-toolkit' ),
                            'name'          => 'nav_style',
                            'type'          => 'select',
                            'value'         => '1',
                            'options'       => array(
                                'arrows' => esc_html__( 'Arrows', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select how navigation buttons display on slide.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'      => array(
                                'parent'    => 'navigation',
                                'show_when' => 'yes'
                            )
                        ), 
                        array(
                            'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                            'name'        => 'pagination',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto height', 'epron-toolkit' ),
                            'name'        => 'auto_height',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                            'name'        => 'auto_play',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'The carousel automatically plays when site loaded', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getReleasesFilter(), 
                   
                )
            ),  // End of element kc

            
            /* Single Release
             -------------------------------- */
            'kc_release_single'  => array(
                'name'        => esc_html__( 'Single Release', 'epron-toolkit' ),
                'description' => esc_html__( 'Display single release.', 'epron-toolkit' ),
                'icon'        => 'sl-music-tone',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getReleasesFilter(),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-single-release' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-single-release' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Releases Block
             -------------------------------- */
            'kc_releases_block'  => array(
                'name'        => esc_html__( 'Releases Block', 'epron-toolkit' ),
                'description' => esc_html__( 'Display an extremely impressive releases blocks with many beautiful styles.', 'epron-toolkit' ),
                'icon'        => 'sl-music-tone',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Block', 'epron-toolkit' ),
                            'name'    => 'block',
                            'type'    => 'select',
                            'value'   => 'block1',
                            'options' => array(
                                'block1' => esc_html__( 'Block 1', 'epron-toolkit' ),
                               
                            ),
                            'description'   => esc_html__( 'Select posts block.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Items on Row', 'epron-toolkit' ),
                            'name'    => 'articles_number',
                            'type'    => 'select',
                            'value'   => '2',
                            'options' => array(
                                '2' => esc_html__( '2 Items', 'epron-toolkit' ),
                                '3' => esc_html__( '3 Items', 'epron-toolkit' ),
                                '4' => esc_html__( '4 Items', 'epron-toolkit' ),
                                '5' => esc_html__( '5 Items', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select number of items per row.', 'epron-toolkit' ),
                            'admin_label'   => false,
                        ),
                        array(
                            'label'   => esc_html__( 'Ajax Pagination', 'epron-toolkit' ),
                            'name'    => 'pagination',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''          => esc_html__( 'None', 'epron-toolkit' ),
                                'load_more' => esc_html__( 'Load More Button', 'epron-toolkit' ),
                                'infinite'  => esc_html__( 'Infinite Loading', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select ajax pagination.', 'epron-toolkit' ),
                            'admin_label'   => false,
                        ),
                        array(
                            'label'   => esc_html__( 'Ajax Filter', 'epron-toolkit' ),
                            'name'    => 'ajax_filter',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''         => esc_html__( 'None', 'epron-toolkit' ),
                                'on-left'  => esc_html__( 'On Left', 'epron-toolkit' ),
                                'center'   => esc_html__( 'Center', 'epron-toolkit' ),
                                'on-right' => esc_html__( 'On Right', 'epron-toolkit' ),
                                'multiple-filters' => esc_html__( 'Multiple Filters', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Display Ajax filter above grid.', 'epron-toolkit' ),
                            'admin_label' => false,
                        ),
                        array(
                            'label'   => esc_html__( 'Selection Method', 'epron-toolkit' ),
                            'name'    => 'filter_sel_method',
                            'type'    => 'select',
                            'value'   => 'filter-sel-multiple',
                            'options' => array(
                                'filter-sel-multiple' => esc_html__( 'Multiple', 'epron-toolkit' ),
                                'filter-sel-single' => esc_html__( 'Single', 'epron-toolkit' ),
                            ),
                            'description' => esc_html__( 'Select filter selection method.', 'epron-toolkit' ),
                            'admin_label' => false,
                            'relation'    => array(
                                'parent'    => 'ajax_filter',
                                'show_when' => array('on-left', 'center', 'on-right', 'multiple-filters')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Show filters on Start', 'epron-toolkit' ),
                            'name'    => 'show_filters',
                            'type'    => 'select',
                            'value'   => 'no',
                            'options' => array(
                                'show-filters' => esc_html__( 'Yes', 'epron-toolkit' ),
                                'hide-filters' => esc_html__( 'No', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Show filters when page is loaded. Otherwise the filters are shown after clicking the "Filters" button.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'ajax_filter',
                                'show_when' => array('multiple-filters')
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getReleasesFilter(),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-music-block-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-music-block-inner' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Revolution Slider
             -------------------------------- */
            'kc_revolution_slider'  => array(
                'name'        => esc_html__( 'Revolution Slider', 'epron-toolkit' ),
                'description' => esc_html__( 'Display videos in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-star',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Select Slider', 'epron-toolkit' ),
                            'name'    => 'alias',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => $this->getRevoSliders(),
                            'description'   => esc_html__( 'Select Revolution Slider. Note: Revolution Slider doesn\'t refresh on visual mode. Please save and refesh manually your page to see slider.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),

                    ),
                   
                )
            ),  // End of element kc


            /* Share Buttons
             -------------------------------- */
            'kc_share_buttons'    => array(
                'name'        => esc_html__( 'Share Buttons', 'epron-toolkit' ),
                'description' => esc_html__( 'Display share buttons.', 'epron-toolkit' ),
                'icon'        => 'sl-share',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'Custom class for wrapper of the shortcode widget.', 'epron-toolkit' )
                        ),
                    ),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'margin', 'label' => 'Margin', 'selector' => '.kc-share-buttons-inner' ),
                                    ),
                                    'Button Style' => array(
                                        array( 'property' => 'color', 'label' => 'Text Color', 'selector' => '.share-button' ),
                                    ),
                                    'Label' => array(
                                        array( 'property' => 'color', 'label' => 'Text Color', 'selector' => '.share-label' ),
                                        array( 'property' => 'background-color', 'label' => 'Line Color', 'selector' => '.share-label:after' ),
                                        array( 'property' => 'display', 'label' => 'Display', 'selector' => '.share-label' ),
                                    )
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Fullwidth Player
             -------------------------------- */
            'kc_fw_player'    => array(
                'name'        => esc_html__( 'Fullwidth Player', 'epron-toolkit' ),
                'description' => esc_html__( 'Display Fullwidth player.', 'epron-toolkit' ),
                'icon'        => 'sl-playlist',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'       => esc_html__( 'Select Tracklist', 'epron-toolkit' ),
                            'name'        => 'id',
                            'type'        => 'select',
                            'value'       => 'none',
                            'options'     => $this->getTracks('wp_tracks'),
                            'description' => esc_html__( 'Select tracklist post. If there are no tracks available, then you can add a audio tracks using Tacks Manager menu on the left.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Tracks IDS', 'epron-toolkit' ),
                            'name'        => 'ids',
                            'type'        => 'text',
                            'value'       => '',
                            'description' => esc_html__( 'Filter multiple tracks by ID. Enter the track IDs separated by | (ex: 333|18|643).', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Fixed Height', 'epron-toolkit' ),
                            'name'    => 'fixed_height',
                            'type'    => 'number_slider',
                            'value'   => '0',
                            'options' => array(
                                'min'        => 0,
                                'max'        => 999,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set fixed height (px) of tracklist. If the value is set at "0" then the height of the list is set to automatic and the scroll on right is invisible.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Show Tracklist', 'epron-toolkit' ),
                            'name'        => 'show_tracklist',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Show tracklist on startup.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                )
            ),  // End of element kc


            /* Video Cover
             -------------------------------- */
             'kc_video_cover'    => array(
                'name'        => esc_html__( 'Video Cover', 'epron-toolkit' ),
                'description' => esc_html__( 'Display video with image cover.', 'epron-toolkit' ),
                'icon'        => 'sl-camrecorder',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Video Source', 'epron-toolkit' ),
                            'name'    => 'video_source',
                            'type'    => 'select',
                            'value'   => 'youtube',
                            'options' => array(
                                'youtube' => esc_html__( 'YouTube', 'epron-toolkit' ),
                                'vimeo'   => esc_html__( 'Vimeo', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Choose source of video.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'       => esc_html__('Video link', 'epron-toolkit'),
                            'name'        => 'video_link',
                            'type'        => 'text',
                            'value'       => '',
                            'admin_label' => false,
                            'description' => esc_html__( 'Enter the Youtube or Vimeo URL.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'font-size', 'label' => 'Font Size', 'selector' => '.kc-big-event-date' ),
                                        array( 'property' => 'color', 'label' => 'Font Color', 'selector' => '.kc-big-event-date' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc

            /* Single Nav
             -------------------------------- */
            'kc_single_nav'  => array(
                'name'         => esc_html__( 'Single Navigation', 'epron-toolkit' ),
                'description'  => esc_html__( 'Display post navigation.', 'epron-toolkit' ),
                'icon'         => 'fa-list',
                'category'     => 'Premium',
                'params'       => array(
                    'general' => array(

                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__( 'Extra Class Name', 'epron-toolkit' ),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),

                    'styling' => array(
                        array(
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'margin', 'label' => 'Margin', 'selector' => '.single-nav' ),
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.single-nav' ),
                                    ),
                                ),
                            ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Details List
             -------------------------------- */
            'kc_details_list'  => array(
                'name'         => esc_html__( 'Details List', 'epron-toolkit' ),
                'description'  => esc_html__( 'Display details list.', 'epron-toolkit' ),
                'icon'         => 'fa-list',
                'category'     => 'Premium',
                'params'       => array(
                    'general' => array(

                        array(
                            'label'         => esc_html__( 'Group Field', 'epron-toolkit' ),
                            'name'          => 'gdetails',
                            'type'          => 'group',
                            'description'   => '',
                            'options'       => array( 'add_text' => esc_html__( 'Add new detail', 'epron-toolkit' ) ),
                            'value' => base64_encode( json_encode(array(
                                "1" => array(
                                    "label" => esc_html__( 'Date', 'epron-toolkit' ),
                                    'value_type' => 'text',
                                    "value" => "06/03/2013"
                                ),
                                "2" => array(
                                    "label" => esc_html__( 'Catalog', 'epron-toolkit' ),
                                    'value_type' => 'text',
                                    "value" => "EPRN01"
                                ),
                           
                            ) ) ),
                            'params' => array(
                                array(
                                    'label'    => esc_html__( 'Title', 'epron-toolkit' ),
                                    'name'     => 'label',
                                    'type'     => 'text',
                                    'value'    => '',
                                ),
                                array(
                                    'label'   => esc_html__( 'Type', 'epron-toolkit' ),
                                    'name'    => 'value_type',
                                    'type'    => 'select',
                                    'value'   => 'text',
                                    'options' => array(
                                        'text'             => esc_html__( 'Text', 'epron-toolkit' ),
                                        'link'             => esc_html__( 'Link', 'epron-toolkit'),
                                        'social'           => esc_html__( 'Social Links', 'epron-toolkit'),
                                        'filter'           => esc_html__( 'Categories', 'epron-toolkit' ),
                                        'event_date_start' => esc_html__( 'Event Start Date', 'epron-toolkit' ),
                                        'event_date_end'   => esc_html__( 'Event End Date', 'epron-toolkit' ),
                                        'event_time_start' => esc_html__( 'Event Time Start', 'epron-toolkit' ),
                                    )
                                ),
                                array(
                                    'label'       => esc_html__( 'Social Icons', 'epron-toolkit' ),
                                    'name'        => 'social_links',
                                    'type'        => 'textarea',
                                    'admin_label' => false,
                                    'value'       => 'facebook|https://facebook.com',
                                    'description' => esc_html__( 'Add social icons divided with linebreaks (Enter) e.g.: facebook|https://facebook.com', 'epron-toolkit' ),
                                    'relation'    => array(
                                        'parent'    => 'gdetails-value_type',
                                        'show_when' => array( 'social' )
                                    )
                                ),
                                array(
                                    'label'   => esc_html__( 'Text', 'epron-toolkit' ),
                                    'name'    => 'value',
                                    'type'    => 'text',
                                    'relation' => array(
                                       'parent' => 'gdetails-value_type',
                                       'show_when' => array('text', 'link' )
                                    )
                                ),
                                array(
                                    'label'   => esc_html__( 'Link', 'epron-toolkit' ),
                                    'name'    => 'link',
                                    'type'    => 'text',
                                    'relation' => array(
                                       'parent' => 'gdetails-value_type',
                                       'show_when' => array( 'link' )
                                    )
                                ),
                                array(
                                    'label'       => esc_html__( 'Open in New Window?', 'epron-toolkit' ),
                                    'name'        => 'blank',
                                    'type'        => 'toggle',
                                    'value'       => '',
                                    'admin_label' => false,
                                    'relation'    => array(
                                       'parent'    => 'gdetails-value_type',
                                       'show_when' => array( 'link' )
                                    )
                                ),
                                array(
                                    'label'   => esc_html__( 'Display From', 'epron-toolkit' ),
                                    'name'    => 'filter_name',
                                    'type'    => 'select',
                                    'value'   => 'cat',
                                    'options' => array(
                                       'cat'                  => esc_html__( 'Blog [ Categories ]', 'epron-toolkit' ),
                                       'wp_release_genres'    => esc_html__( 'Releases [ Filter 1 ]', 'epron-toolkit' ),
                                       'wp_release_artists'   => esc_html__( 'Releases [ Filter 2 ]', 'epron-toolkit' ),
                                       'wp_event_categories'  => esc_html__( 'Events [ Filter 1 ]', 'epron-toolkit' ),
                                       'wp_event_categories2' => esc_html__( 'Events [ Filter 2 ]', 'epron-toolkit' ),
                                       'wp_artists_cats'      => esc_html__( 'Artists [ Filter 1 ]', 'epron-toolkit' ),
                                       'wp_artists_cats2'     => esc_html__( 'Artists [ Filter 2 ]', 'epron-toolkit' ),
                                    ),
                                    'relation' => array(
                                       'parent' => 'gdetails-value_type',
                                       'show_when' => 'filter'
                                    )
                                ),
                            ),
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__( 'Extra Class Name', 'epron-toolkit' ),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),

                    'styling' => array(
                        array(
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'margin', 'label' => 'Margin', 'selector' => '.details-list' ),
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.details-list' ),
                                    ),
                                ),
                            ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc
                

            /* Recent Posts
             -------------------------------- */
            'kc_recent_posts' => array(
                'name'        => esc_html__( 'Recent Posts', 'epron-toolkit' ),
                'description' => esc_html__( 'Display recent posts', 'epron-toolkit' ),
                'icon'        => 'sl-docs',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Number Posts to Show', 'epron-toolkit' ),
                            'name'    => 'limit',
                            'type'    => 'number_slider',
                            'value'   => '3',
                            'options' => array(
                                'min'        => 1,
                                'max'        => 40,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Number posts to show.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Show Description', 'epron-toolkit' ),
                            'name'        => 'excerpt',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Show post description (excerpt)', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Show Thumbnails', 'epron-toolkit' ),
                            'name'        => 'thumbnails',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Show post thumbnails', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Style', 'epron-toolkit' ),
                            'name'    => 'style',
                            'type'    => 'select',
                            'value'   => 'style1',
                            'options' => array(
                                'style1' => esc_html__( 'Style 1 (titles with thumbnails)', 'epron-toolkit' ),
                                'style2' => esc_html__( 'Style 2 (simple date with title)', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__( 'Extra Class Name', 'epron-toolkit' ),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),

                )
            ),  // End of element kc


            /* Posts Carousel
             -------------------------------- */
            'kc_posts_carousel'    => array(
                'name'        => esc_html__( 'Posts Carousel', 'epron-toolkit' ),
                'description' => esc_html__( 'Display posts in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-docs',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Post Module', 'epron-toolkit' ),
                            'name'    => 'module',
                            'type'    => 'select',
                            'value'   => 'module1',
                            'options' => array(
                                'module2' => esc_html__( 'Module 1', 'epron-toolkit' ),
                                'module3' => esc_html__( 'Module 2', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select post module.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Items per slide', 'epron-toolkit' ),
                            'name'    => 'items_number',
                            'type'    => 'number_slider',
                            'value'   => 3,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'The number of items displayed per slide (not apply for auto-height)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),  
                        array(
                            'label'   => esc_html__( 'Items On Tablet?', 'epron-toolkit' ),
                            'name'    => 'tablet',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Tablet Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Items On Smartphone?', 'epron-toolkit' ),
                            'name'    => 'mobile',
                            'type'    => 'number_slider',
                            'value'   => 2,
                            'options' => array(
                                'min'        => 1,
                                'max'        => 10,
                                'unit'       => '',
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Display number of items per each slide (Mobile Screen)', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                            'name'    => 'speed',
                            'type'    => 'number_slider',
                            'value'   => 500,
                            'options' => array(
                                'min'        => 100,
                                'max'        => 1500,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Navigation', 'epron-toolkit' ),
                            'name'        => 'navigation',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display the "Next" and "Prev" buttons.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'         => esc_html__( 'Navigation Style', 'epron-toolkit' ),
                            'name'          => 'nav_style',
                            'type'          => 'select',
                            'value'         => '1',
                            'options'       => array(
                                'arrows' => esc_html__( 'Arrows', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select how navigation buttons display on slide.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'      => array(
                                'parent'    => 'navigation',
                                'show_when' => 'yes'
                            )
                        ), 
                        array(
                            'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                            'name'        => 'pagination',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto height', 'epron-toolkit' ),
                            'name'        => 'auto_height',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Add height to owl-wrapper-outer so you can use diffrent heights on slides. Use it only for one item per page setting.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                            'name'        => 'auto_play',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'The carousel automatically plays when site loaded', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getBlogFilter(), 
                   
                )
            ),  // End of element kc
            

            /* Posts Slider
             -------------------------------- */
            'kc_posts_slider'    => array(
                'name'        => esc_html__( 'Posts Slider', 'epron-toolkit' ),
                'description' => esc_html__( 'Display posts in a nice sliding manner.', 'epron-toolkit' ),
                'icon'        => 'sl-layers',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Speed', 'epron-toolkit' ),
                            'name'    => 'speed',
                            'type'    => 'number_slider',
                            'value'   => 500,
                            'options' => array(
                                'min'        => 100,
                                'max'        => 1500,
                                'unit'       => '',
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set the speed at which autoplaying sliders will transition in second', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Pagination', 'epron-toolkit' ),
                            'name'        => 'pagination',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Show the pagination.', 'epron-toolkit' ),
                            'admin_label' => true
                        ), 
                        array(
                            'label'       => esc_html__( 'Auto Play', 'epron-toolkit' ),
                            'name'        => 'auto_play',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'The slider automatically plays when site loaded', 'epron-toolkit' ),
                            'admin_label' => true
                        ),

                        array(
                            'label'   => esc_html__( 'Image size', 'epron-toolkit' ),
                            'name'    => 'thumb_size',
                            'type'    => 'text',
                            'value'   => 'epron-content-thumb',
                            'description'   => esc_html__( 'Type image size full/large/medium or custom.', 'epron-toolkit' ),
                            'admin_label'   => false,
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getBlogFilter(),
                'styling' => array(
                        array(
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Slider' => array(
                                        array( 'property' => 'height', 'label' => 'Height', 'selector' => '.post-slide' ),
                                        array( 'property' => 'margin', 'label' => 'Margin', 'selector' => '.kc-posts-slider' ),
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-posts-slider' ),
                                    ),
                                ),
                            ),
                            'description' => esc_html__( 'Slide CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


            /* Posts Block
             -------------------------------- */
            'kc_posts_block'    => array(
                'name'        => esc_html__( 'Posts Block', 'epron-toolkit' ),
                'description' => esc_html__( 'Display an extremely impressive recent posts with many beautiful styles.', 'epron-toolkit' ),
                'icon'        => 'sl-docs',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'   => esc_html__( 'Block', 'epron-toolkit' ),
                            'name'    => 'block',
                            'type'    => 'select',
                            'value'   => 'block1',
                            'options' => array(
                                'block1' => esc_html__( 'Block 1', 'epron-toolkit' ),
                                'block2' => esc_html__( 'Block 2', 'epron-toolkit' ),
                                'block3' => esc_html__( 'Block 3', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select posts block.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'   => esc_html__( 'Articles on Row', 'epron-toolkit' ),
                            'name'    => 'articles_number',
                            'type'    => 'select',
                            'value'   => '2',
                            'options' => array(
                                '1' => esc_html__( '1 Article', 'epron-toolkit' ),
                                '2' => esc_html__( '2 Articles', 'epron-toolkit' ),
                                '3' => esc_html__( '3 Articles', 'epron-toolkit' ),
                                '4' => esc_html__( '4 Articles', 'epron-toolkit' ),
                                '5' => esc_html__( '5 Articles', 'epron-toolkit' )
                            ),
                            'description'   => esc_html__( 'Select number of articles per row.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array('block1', 'block2' )
                            )
                        ),
                       
                        array(
                            'label'   => esc_html__( 'Image size', 'epron-toolkit' ),
                            'name'    => 'thumb_size',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => $this->getImageSizes( array(
                                    '' => esc_html__( 'From Block Settings', 'epron-toolkit' )
                                )
                            ),
                            'description'   => esc_html__( 'Select image size. By default, the image size is set for the selected module. Note: In some blocks it is not possible to change image size because it is set permanently.', 'epron-toolkit' ),
                            'admin_label'   => false,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array( 'blockx' )
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Module Size', 'epron-toolkit' ),
                            'name'    => 'module_size',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                '' => esc_html__( 'From Block Settings', 'epron-toolkit' ),
                                'small-module' => esc_html__( 'Small', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Select module size. Note: In some blocks it is not possible to change the size because it is set permanently.', 'epron-toolkit' ),
                            'admin_label'   => true,
                            'relation'    => array(
                                'parent'    => 'block',
                                'show_when' => array( 'blockx' )
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                        array(
                            'label'       => esc_html__('Extra Module Class Name', 'epron-toolkit'),
                            'name'        => 'module_classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style loop module differently, please add a class name to this field and refer to it in your custom CSS file. Tip: no-cut-corners - disbale cut corners in module.', 'epron-toolkit' )
                        ),
                    ),
                'filter' => $this->getBlogFilter(),
                'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-posts-block-inner' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-posts-block-inner' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                   
                )
            ),  // End of element kc


        	/* Tracklist
             -------------------------------- */
            'kc_tracklist'    => array(
                'name'        => esc_html__( 'Tracklist', 'epron-toolkit' ),
                'description' => esc_html__( 'Display tracklist.', 'epron-toolkit' ),
                'icon'        => 'sl-playlist',
                'category'    => 'Premium',
                'params'      => array(
                    'general' => array(
                        array(
                            'label'       => esc_html__( 'Select Tracklist', 'epron-toolkit' ),
                            'name'        => 'id',
                            'type'        => 'select',
                            'value'       => 'none',
                            'options'     => $this->getTracks('wp_tracks'),
                            'description' => esc_html__( 'Select tracklist post. If there are no tracks available, then you can add a audio tracks using Tacks Manager menu on the left.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Tracks IDS', 'epron-toolkit' ),
                            'name'        => 'ids',
                            'type'        => 'text',
                            'value'       => '',
                            'description' => esc_html__( 'Filter multiple tracks by ID. Enter the track IDs separated by | (ex: 333|18|643).', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Fixed Height', 'epron-toolkit' ),
                            'name'    => 'fixed_height',
                            'type'    => 'number_slider',
                            'value'   => '0',
                            'options' => array(
                                'min'        => 0,
                                'max'        => 999,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set fixed height (px) of tracklist. If the value is set at "0" then the height of the list is set to automatic and the scroll on right is invisible.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Show Cover Images', 'epron-toolkit' ),
                            'name'        => 'covers',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Show or hide tracks cover images in tracklist.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Big Covers', 'epron-toolkit' ),
                            'name'        => 'big_cover',
                            'type'        => 'toggle',
                            'value'       => 'no',
                            'description' => esc_html__( 'Show big covers images in tracklist.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Display Limit', 'epron-toolkit' ),
                            'name'    => 'limit',
                            'type'    => 'number_slider',
                            'value'   => '0',
                            'options' => array(
                                'min'        => 0,
                                'max'        => 999,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'How many tracks will be visibile. If the value is set at "0" then all tracks will be shown.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                )
            ),  // End of element kc


			/* Album Player
             -------------------------------- */
			'kc_album_player'    => array(
			'name'        => esc_html__( 'Album Player', 'epron-toolkit' ),
			'description' => esc_html__( 'Display tracklist.', 'epron-toolkit' ),
			'icon'        => 'sl-playlist',
			'category'    => 'Premium',
			'params'      => array(
                    'general' => array(
                        array(
                            'label'       => esc_html__( 'Select Tracklist', 'epron-toolkit' ),
                            'name'        => 'id',
                            'type'        => 'select',
                            'value'       => 'none',
                            'options'     => $this->getTracks('wp_tracks'),
                            'description' => esc_html__( 'Select tracklist post. If there are no tracks available, then you can add a audio tracks using Tacks Manager menu on the left.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Tracks IDS', 'epron-toolkit' ),
                            'name'        => 'ids',
                            'type'        => 'text',
                            'value'       => '',
                            'description' => esc_html__( 'Filter multiple tracks by ID. Enter the track IDs separated by | (ex: 333|18|643).', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'   => esc_html__( 'Fixed Height', 'epron-toolkit' ),
                            'name'    => 'fixed_height',
                            'type'    => 'number_slider',
                            'value'   => '0',
                            'options' => array(
                                'min'        => 0,
                                'max'        => 999,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Set fixed height (px) of tracklist. If the value is set at "0" then the height of the list is set to automatic and the scroll on right is invisible.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__( 'Show Tracklist', 'epron-toolkit' ),
                            'name'        => 'show_tracklist',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Show tracklist on startup.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__('Extra Class Name', 'epron-toolkit'),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                )
            ),  // End of element kc


        	 /* Instagram Feed
             -------------------------------- */
            'kc_instafeed' => array(
                'name'        => esc_html__( 'Instagram Feed', 'epron-toolkit' ),
                'description' => esc_html__( 'Display recent images from Instagram account.', 'epron-toolkit' ),
                'icon'        => 'kc-icon-instagram',
                'category'    => 'Socials',
                'params'      => array(

                    'general' => array(

                        array(
                            'label'       => esc_html__( 'Username', 'epron-toolkit' ),
                            'name'        => 'username',
                            'type'        => 'text',
                            'value'       => '',
                            'description' => esc_html__( 'Enter the ID as it appears after the instagram url (ex. http://www.instagram.com/ID)', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Display Name', 'epron-toolkit' ),
                            'name'        => 'display_name',
                            'type'        => 'text',
                            'value'       => '',
                            'description' => esc_html__( 'Enter custom profile name instead of ID name.', 'epron-toolkit' ),
                            'admin_label' => true
                        ),
                        array(
                            'label'       => esc_html__( 'Access token', 'epron-toolkit' ),
                            'name'        => 'access_token',
                            'type'        => 'text',
                            'value'       => '',
                            'description' => esc_html__( 'You can get the Access token at http://instagram.pixelunion.net/', 'epron-toolkit' ),
                        ),
                        array(
                            'label'       => esc_html__( 'Display Header', 'epron-toolkit' ),
                            'name'        => 'display_header',
                            'type'        => 'toggle',
                            'value'       => 'yes',
                            'description' => esc_html__( 'Display or hide the Instagram header section.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'       => esc_html__( 'Display Follow Overlay', 'epron-toolkit' ),
                            'name'        => 'display_follow_overlay',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display or hide the black overlay with profile link.', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Images Per Row', 'epron-toolkit' ),
                            'name'    => 'images_per_row',
                            'type'    => 'number_slider',
                            'value'   => '3',
                            'options' => array(
                                'min'        => 1,
                                'max'        => 5,
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Set the number of images displayed on each row (default is 3).', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                         array(
                            'label'   => esc_html__( 'Number of Rows', 'epron-toolkit' ),
                            'name'    => 'number_of_rows',
                            'type'    => 'number_slider',
                            'value'   => '2',
                            'options' => array(
                                'min'        => 1,
                                'max'        => 5,
                                'show_input' => false
                            ),
                            'description' => esc_html__( 'Set the number of images displayed on each row (default is 3).', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'   => esc_html__( 'Image Gap', 'epron-toolkit' ),
                            'name'    => 'image_gap',
                            'type'    => 'select',
                            'value'   => 'no-gap',
                            'options' => array(
                                'no-gap'    => esc_html__( 'No Gap', 'epron-toolkit' ),
                                'small-gap' => esc_html__( '2px', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Set a gap between images (default: No gap)', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'   => esc_html__( 'Size', 'epron-toolkit' ),
                            'name'    => 'size',
                            'type'    => 'select',
                            'value'   => 'no-gap',
                            'options' => array(
                                'widget-size'    => esc_html__( 'Small Widget', 'epron-toolkit' ),
                                'fullwidth-size' => esc_html__( 'Fullwidth', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Set module size.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                    ),

                    'advanced' => array(
                        array(
                            'label'   => esc_html__( 'Request Timeout', 'epron-toolkit' ),
                            'name'    => 'request_timeout',
                            'type'    => 'number_slider',
                            'value'   => '2',
                            'options' => array(
                                'min'        => 1,
                                'max'        => 5,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Timeout in minutes for the instagram API request.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),  
                        array(
                            'label'   => esc_html__( 'Cache Time', 'epron-toolkit' ),
                            'name'    => 'cache_time',
                            'type'    => 'number_slider',
                            'value'   => '60',
                            'options' => array(
                                'min'        => 1,
                                'max'        => 1000,
                                'show_input' => true
                            ),
                            'description' => esc_html__( 'Time in minutes that data is stored in the database before re-downloading.', 'epron-toolkit' ),
                            'admin_label' => false
                        ),
                        array(
                            'label'   => esc_html__( 'Background Color', 'epron-toolkit' ),
                            'name'    => 'color_scheme',
                            'type'    => 'select',
                            'value'   => $color_scheme,
                            'options' => $this->getSchemes(),
                            'description'   => esc_html__( 'Select color scheme.', 'epron-toolkit' ),
                            'admin_label'   => false
                        ),
                        array(
                            'label'       => esc_html__( 'Extra Class Name', 'epron-toolkit' ),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                    'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.kc-instagram' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.kc-instagram' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                )
            ),  // End of element kc
        

            /* AD Spot
             -------------------------------- */
            'kc_adspot' => array(
                'name'        => esc_html__( 'Advertisement', 'epron-toolkit' ),
                'description' => esc_html__( 'Display advertisement.', 'epron-toolkit' ),
                'icon'        => 'sl-target',
                'category'    => 'Socials',
                'params'      => array(

                    'general' => array(
                        array(
                            'label'       => esc_html__( 'Display AD Title', 'epron-toolkit' ),
                            'name'        => 'display_ad_title',
                            'type'        => 'toggle',
                            'value'       => '',
                            'description' => esc_html__( 'Display small title by default is: - Advertisement -. Note: Text can be replaced by Translate plugin like Loco Translate.', 'epron-toolkit' ),
                            'admin_label' => false
                        ), 
                        array(
                            'label'   => esc_html__( 'Use adspot from:', 'epron-toolkit' ),
                            'name'    => 'adspot',
                            'type'    => 'select',
                            'value'   => '',
                            'options' => array(
                                ''               => esc_html__( '- Select -', 'epron-toolkit' ),
                                'sidebar'        => esc_html__( 'Sidebar', 'epron-toolkit' ),
                                'header'         => esc_html__( 'Header', 'epron-toolkit' ),
                                'footer'         => esc_html__( 'Footer', 'epron-toolkit' ),
                                'article_top'    => esc_html__( 'Article Top', 'epron-toolkit' ),
                                'article_bottom' => esc_html__( 'Article Bottom', 'epron-toolkit' ),
                                'tracklist'      => esc_html__( 'Tracklit Inline', 'epron-toolkit' ),
                                'custom1'        => esc_html__( 'Custom 1', 'epron-toolkit' ),
                                'custom2'        => esc_html__( 'Custom 2', 'epron-toolkit' ),
                                'custom3'        => esc_html__( 'Custom 3', 'epron-toolkit' ),
                            ),
                            'description'   => esc_html__( 'Choose the adspot from list.', 'epron-toolkit' ),
                            'admin_label'   => true
                        ),
                        array(
                            'label'       => esc_html__( 'Extra Class Name', 'epron-toolkit' ),
                            'name'        => 'classes',
                            'type'        => 'text',
                            'admin_label' => false,
                            'description' => esc_html__( 'If you wish to style a particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'epron-toolkit' )
                        ),
                    ),
                    'styling' => array(
                        array(
                            'label'   => esc_html__( 'Box CSS', 'epron-toolkit' ),
                            'name'    => 'custom_css',
                            'type'    => 'css',
                            'options' => array(
                                array(
                                    'Box' => array(
                                        array( 'property' => 'padding', 'label' => 'Box Padding', 'selector' => '.adspot' ),
                                        array( 'property' => 'margin', 'label' => 'Box Margin', 'selector' => '.adspot' ),
                                    ),
                                    
                                ),
                            ),
                            'description' => esc_html__( 'Box wrapper CSS', 'epron-toolkit' ),
                        ),
                    ),                  
                )
            ),  // End of element kc
            
        ));
	}



	////////////////////////////////////////
	// FUNCTIONS ONLY FOR SPECIFIED THEME //
	////////////////////////////////////////

	/**
	 * Get blog filter
	 * @return array
	 */
	public function getBlogFilter() {
		return array(              
		    array(
		        'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
		        'name'    => 'limit',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 40,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Sort Order', 'epron-toolkit' ),
		        'name'    => 'sort_order',
		        'type'    => 'select',
		        'value'   => 'post_date',
		        'options' => array(
		            'post_date'     => esc_html__( 'Latest (By date)', 'epron-toolkit' ),
		            'title'         => esc_html__( 'Alphabetical A - Z', 'epron-toolkit' ),
		            'rand'          => esc_html__( 'Random Posts', 'epron-toolkit' ),
		            'rand_today'    => esc_html__( 'Random Posts Today', 'epron-toolkit' ),
		            'rand_week'     => esc_html__( 'Random Posts From Last 7 Days', 'epron-toolkit' ),
		            'comment_count' => esc_html__( 'Most Commented', 'epron-toolkit' ),
		            'highest_rated' => esc_html__( 'Highest rated (reviews)', 'epron-toolkit' )
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'How to sort the posts.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Category', 'epron-toolkit' ),
		        'name'        => 'category_ids',
		        'type'        => 'multiple',
		        'options'     => $this->getTax( 'category' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Category Slug', 'epron-toolkit' ),
		        'name'        => 'category_slugs',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Post ID', 'epron-toolkit' ),
		        'name'        => 'post_ids',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Tag Slug', 'epron-toolkit' ),
		        'name'        => 'tag_slugs',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter tags by slugs. Enter the tag slugs separated by commas (ex: tag1,tag2,tag3)', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Author ID', 'epron-toolkit' ),
		        'name'        => 'author_ids',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple authors by ID. Enter the author IDs separated by commas (ex: 32,11,899)', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Offset Posts', 'epron-toolkit' ),
		        'name'    => 'offset',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 99,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' )
		    )
		);

	}


	/**
	 * Get artists filter
	 * @return array
	 */
	public function getArtistsFilter() {
		return array(              
		    array(
		        'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
		        'name'    => 'limit',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 40,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Sort Order', 'epron-toolkit' ),
		        'name'    => 'sort_order',
		        'type'    => 'select',
		        'value'   => 'post_date',
		        'options' => array(
		            'menu_order'    => esc_html__( 'Drag and Drop', 'epron-toolkit' ),
		            'post_date'     => esc_html__( 'Latest (By date)', 'epron-toolkit' ),
		            'title'         => esc_html__( 'Alphabetical A - Z', 'epron-toolkit' ),
		            'rand'          => esc_html__( 'Random Posts', 'epron-toolkit' ),
		            'rand_today'    => esc_html__( 'Random Posts Today', 'epron-toolkit' ),
		            'rand_week'     => esc_html__( 'Random Posts From Last 7 Days', 'epron-toolkit' ),
		            'comment_count' => esc_html__( 'Most Commented', 'epron-toolkit' ),
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'How to sort the posts.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Post ID', 'epron-toolkit' ),
		        'name'        => 'post_ids',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Offset Posts', 'epron-toolkit' ),
		        'name'    => 'offset',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 99,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' )
		    ),

             /* Filters */ 
            array(
                'label'       => esc_html__( 'Filters Order', 'epron-toolkit' ),
                'name'        => 'filters_order',
                'type'        => 'text',
                'value'       => '1,2',
                'admin_label' => false,
                'description' => esc_html__( 'Enter the filters order number separated by commas e.g.: 2,1 (Display only two filters, the second will be displayed first)', 'epron-toolkit' )
            ),

		    /* Filter 1 */ 
		    array(
		        'label'       => esc_html__( 'Filter 1 - Name', 'epron-toolkit' ),
		        'name'        => 'category_label',
		        'type'        => 'text',
		        'value'       => esc_html__( 'All', 'epron-toolkit' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 1 - IDS', 'epron-toolkit' ),
		        'name'        => 'category_ids',
		        'type'        => 'multiple',
		        'options'     => $this->getTax( 'wp_artists_cats' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 1 - Slug', 'epron-toolkit' ),
		        'name'        => 'category_slugs',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
		    ),


             /* Filter 1 */ 
            array(
                'label'       => esc_html__( 'Filter 2 - Name', 'epron-toolkit' ),
                'name'        => 'category_label2',
                'type'        => 'text',
                'value'       => esc_html__( 'All', 'epron-toolkit' ),
                'admin_label' => true,
                'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
            ),
            array(
                'label'       => esc_html__( 'Filter 2 - IDS', 'epron-toolkit' ),
                'name'        => 'category_ids2',
                'type'        => 'multiple',
                'options'     => $this->getTax( 'wp_artists_cats2' ),
                'admin_label' => true,
                'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
            ),
            array(
                'label'       => esc_html__( 'Filter 2 - Slug', 'epron-toolkit' ),
                'name'        => 'category_slugs2',
                'type'        => 'text',
                'admin_label' => true,
                'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
            ),

		  
		);
	}


	/**
	 * Get releases filter
	 * @return array
	 */
	public function getReleasesFilter() {
		return array(              
		    array(
		        'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
		        'name'    => 'limit',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 40,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Sort Order', 'epron-toolkit' ),
		        'name'    => 'sort_order',
		        'type'    => 'select',
		        'value'   => 'menu_order',
		        'options' => array(
		            'menu_order'    => esc_html__( 'Drag and Drop', 'epron-toolkit' ),
		            'post_date'     => esc_html__( 'Latest (By date)', 'epron-toolkit' ),
		            'title'         => esc_html__( 'Alphabetical A - Z', 'epron-toolkit' ),
		            'rand'          => esc_html__( 'Random Posts', 'epron-toolkit' ),
		            'rand_today'    => esc_html__( 'Random Posts Today', 'epron-toolkit' ),
		            'rand_week'     => esc_html__( 'Random Posts From Last 7 Days', 'epron-toolkit' ),
		            'comment_count' => esc_html__( 'Most Commented', 'epron-toolkit' ),
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'How to sort the posts.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Post ID', 'epron-toolkit' ),
		        'name'        => 'post_ids',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Offset Posts', 'epron-toolkit' ),
		        'name'    => 'offset',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 99,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' )
		    ),

		    /* Filters */ 
		    array(
		        'label'       => esc_html__( 'Filters Order', 'epron-toolkit' ),
		        'name'        => 'filters_order',
		        'type'        => 'text',
		        'value'       => '1,2',
		        'admin_label' => false,
		        'description' => esc_html__( 'Enter the filters order number separated by commas e.g.: 2,1 (Display only two filters, the second will be displayed first)', 'epron-toolkit' )
		    ),

		    /* Filter 1 */ 
		    array(
		        'label'       => esc_html__( 'Filter 1 - Name', 'epron-toolkit' ),
		        'name'        => 'category_label',
		        'type'        => 'text',
		        'value'       => esc_html__( 'All', 'epron-toolkit' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 1 - IDS', 'epron-toolkit' ),
		        'name'        => 'category_ids',
		        'type'        => 'multiple',
		        'options'     => $this->getTax( 'wp_release_genres' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 1 - Slug', 'epron-toolkit' ),
		        'name'        => 'category_slugs',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
		    ),

		    /* Filter 2 */ 
		    array(
		        'label'       => esc_html__( 'Filter 2 - Name', 'epron-toolkit' ),
		        'name'        => 'category_label2',
		        'type'        => 'text',
		        'value'       => esc_html__( 'All', 'epron-toolkit' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 2 - IDS', 'epron-toolkit' ),
		        'name'        => 'category_ids2',
		        'type'        => 'multiple',
		        'options'     => $this->getTax( 'wp_release_artists' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 2 - Slug', 'epron-toolkit' ),
		        'name'        => 'category_slugs2',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
		    ),

		  
		);
	}


	/**
	 * Get events filter
	 * @return array
	 */
	public function getEventsFilter() {
		return array(              
		    array(
		        'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
		        'name'    => 'limit',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 40,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Post ID', 'epron-toolkit' ),
		        'name'        => 'post_ids',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' ),
		        'relation'    => array(
		            'parent'    => 'event_type',
		            'show_when' => array( 'future-events','past-events')
		        )
		    ),
		    array(
		        'label'   => esc_html__( 'Offset Posts', 'epron-toolkit' ),
		        'name'    => 'offset',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 99,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' ),
		        'relation'    => array(
		            'parent'    => 'event_type',
		            'show_when' => array( 'future-events','past-events')
		        )
		    ),

		    /* Filters */ 
		    array(
		        'label'       => esc_html__( 'Filters Order', 'epron-toolkit' ),
		        'name'        => 'filters_order',
		        'type'        => 'text',
		        'value'       => '1,2,3',
		        'admin_label' => false,
		        'description' => esc_html__( 'Enter the filters order number separated by commas e.g.: 2,1 (Display only two filters, the second will be displayed first)', 'epron-toolkit' )
		    ),

		    /* Filter 1 */ 
		    array(
		        'label'       => esc_html__( 'Filter 1 - Name', 'epron-toolkit' ),
		        'name'        => 'event_type_label',
		        'type'        => 'text',
		        'value'       => esc_html__( 'All', 'epron-toolkit' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Filter 1 - Type', 'epron-toolkit' ),
		        'name'    => 'event_type',
		        'type'    => 'select',
		        'value'   => 'future-events',
		        'options' => array(
		            'future-events' => esc_html__( 'Future', 'epron-toolkit' ),
		            'past-events'   => esc_html__( 'Past', 'epron-toolkit' ),
		            'all'           => esc_html__( 'Future + Past', 'epron-toolkit' ),
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Select event type.', 'epron-toolkit' )
		    ),

		    /* Filter 2 */ 
		    array(
		        'label'       => esc_html__( 'Filter 2 - Name', 'epron-toolkit' ),
		        'name'        => 'category_label',
		        'type'        => 'text',
		        'value'       => esc_html__( 'All', 'epron-toolkit' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 2 - IDS', 'epron-toolkit' ),
		        'name'        => 'category_ids',
		        'type'        => 'multiple',
		        'options'     => $this->getTax( 'wp_event_categories' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 2 - Slug', 'epron-toolkit' ),
		        'name'        => 'category_slugs',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
		    ),

            /* Filter 3 */ 
            array(
                'label'       => esc_html__( 'Filter 3 - Name', 'epron-toolkit' ),
                'name'        => 'category_label2',
                'type'        => 'text',
                'value'       => esc_html__( 'All', 'epron-toolkit' ),
                'admin_label' => true,
                'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
            ),
            array(
                'label'       => esc_html__( 'Filter 3 - IDS', 'epron-toolkit' ),
                'name'        => 'category_ids2',
                'type'        => 'multiple',
                'options'     => $this->getTax( 'wp_event_categories2' ),
                'admin_label' => true,
                'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
            ),
            array(
                'label'       => esc_html__( 'Filter 3 - Slug', 'epron-toolkit' ),
                'name'        => 'category_slugs2',
                'type'        => 'text',
                'admin_label' => true,
                'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
            ),

	
		);
	}
	

	/**
	 * Get gallery filter
	 * @return array
	 */
	public function getGalleryFilter() {
		return array(              
		    array(
		        'label'   => esc_html__( 'Limit post number', 'epron-toolkit' ),
		        'name'    => 'limit',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 40,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'If the field is set at "0" the limit post number will be the default number.', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Sort Order', 'epron-toolkit' ),
		        'name'    => 'sort_order',
		        'type'    => 'select',
		        'value'   => 'post_date',
		        'options' => array(
		            'post_date'     => esc_html__( 'Latest (By date)', 'epron-toolkit' ),
		            'title'         => esc_html__( 'Alphabetical A - Z', 'epron-toolkit' ),
		            'rand'          => esc_html__( 'Random Posts', 'epron-toolkit' ),
		            'rand_today'    => esc_html__( 'Random Posts Today', 'epron-toolkit' ),
		            'rand_week'     => esc_html__( 'Random Posts From Last 7 Days', 'epron-toolkit' ),
		            'comment_count' => esc_html__( 'Most Commented', 'epron-toolkit' ),
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'How to sort the posts.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Post ID', 'epron-toolkit' ),
		        'name'        => 'post_ids',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple posts by ID. Enter the post IDs separated by commas (ex: 333,18,643). To exclude posts add them with "-" (ex: -30,-486,-12)', 'epron-toolkit' )
		    ),
		    array(
		        'label'   => esc_html__( 'Offset Posts', 'epron-toolkit' ),
		        'name'    => 'offset',
		        'type'    => 'number_slider',
		        'value'   => '0',
		        'options' => array(
		            'min'        => 0,
		            'max'        => 99,
		            'unit'       => '',
		            'show_input' => true
		        ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Start the count with an offset. If you have a block that shows 10 posts before this one, you can make this one start from the 11\'th post (by using offset 10)', 'epron-toolkit' )
		    ),

		    /* Filters */ 
		    array(
		        'label'       => esc_html__( 'Filters Order', 'epron-toolkit' ),
		        'name'        => 'filters_order',
		        'type'        => 'text',
		        'value'       => '1,2',
		        'admin_label' => false,
		        'description' => esc_html__( 'Enter the filters order number separated by commas e.g.: 2,1 (Display only two filters, the second will be displayed first)', 'epron-toolkit' )
		    ),

		    /* Filter 1 */ 
		    array(
		        'label'       => esc_html__( 'Filter 1 - Name', 'epron-toolkit' ),
		        'name'        => 'category_label',
		        'type'        => 'text',
		        'value'       => esc_html__( 'All', 'epron-toolkit' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 1 - IDS', 'epron-toolkit' ),
		        'name'        => 'category_ids',
		        'type'        => 'multiple',
		        'options'     => $this->getTax( 'wp_gallery_cats' ),
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
		    ),
		    array(
		        'label'       => esc_html__( 'Filter 1 - Slug', 'epron-toolkit' ),
		        'name'        => 'category_slugs',
		        'type'        => 'text',
		        'admin_label' => true,
		        'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
		    ),

             /* Filter 2 */ 
            array(
                'label'       => esc_html__( 'Filter 2 - Name', 'epron-toolkit' ),
                'name'        => 'category_label2',
                'type'        => 'text',
                'value'       => esc_html__( 'All', 'epron-toolkit' ),
                'admin_label' => true,
                'description' => esc_html__( 'Filter name.', 'epron-toolkit' )
            ),
            array(
                'label'       => esc_html__( 'Filter 2 - IDS', 'epron-toolkit' ),
                'name'        => 'category_ids2',
                'type'        => 'multiple',
                'options'     => $this->getTax( 'wp_gallery_cats2' ),
                'admin_label' => true,
                'description' => esc_html__( 'Filter multiple categories.', 'epron-toolkit' )
            ),
            array(
                'label'       => esc_html__( 'Filter 2 - Slug', 'epron-toolkit' ),
                'name'        => 'category_slugs2',
                'type'        => 'text',
                'admin_label' => true,
                'description' => esc_html__( 'Filter multiple categories by category slug name separated by commas (ex: news,interviews,reviews). To exclude posts add them with "-" (ex: -news,-interviews,-reviews)', 'epron-toolkit' )
            ),

		  
		);
	}


	/////////////
	// ASSETS //
	/////////////

	/**
	 * Load scripts
	 * @return array
	 */
	public function enqueue() {

        global $post, $wp_query;

        // If inline CSS styles are enabled
        if ( $this->inline_css === true ) {

            $custom_css = '';

            if ( isset( $wp_query ) && isset( $post ) ) {

                $bg1 = get_post_meta( $wp_query->post->ID, '_bg_1', true );
                $bg2 = get_post_meta( $wp_query->post->ID, '_bg_2', true );

                if ( $bg1 || $bg2 ) {

                    if ( $this->generateBG( $bg1 ) ) {
                        $custom_css .= "
                        html body {
                               " . esc_attr( $this->generateBG( $bg1 ) ) . "
                        }";

                    }
                    if ( $this->generateBG( $bg1 ) ) {
                        $custom_css .= "
                        body #site {
                               " . esc_attr( $this->generateBG( $bg2 ) ) . "
                        }";

                    }
                }

            }

            wp_add_inline_style( 'epron-toolkit', $custom_css );
        }       
	}


	/////////////
	// HELPERS //
	/////////////


	/**
	 * Add supports for posts
	 * @return void
	 */
	public function addSupportedCPT() {
		global $kc;
		$kc->add_content_type( $this->supported_cpt );
	}


	/**
	 * Set default fonts 
     * @version 1.0.1
	 * @return void
	 */
	public function setFonts() {

		$kc_fonts = get_option('kc-fonts');

        // var_dump( json_encode( $kc_fonts) );

		if ( ! is_array( $kc_fonts ) || empty( $kc_fonts ) ) {
		    update_option('kc-fonts', json_decode( $this->default_fonts, true ) );
		} 
		// delete_option('kc-fonts');

	}


	/**
	 * Remove not supported KC elements
	 * @param  array $atts 
	 * @param  array $base 
	 * @return void      
	 */
	function removeKCElements( $atts, $base ){
    
	    if ( in_array( $base, array( 'kc_instagram_feed', 'kc_fb_recent_post', 'kc_post_type_list', 'kc_blog_posts', 'kc_counter_box', 'kc_coundown_timer', 'kc_carousel_post' ) ) ){
	        return null;
	    }
	    return $atts; // required
	}


	/**
	 * Get list of all registered images sizes
	 * @param  array  $extra_opts 
	 * @return array
	 */
	function getImageSizes( $extra_opts = array() ){
	    $image_sizes = get_intermediate_image_sizes();
	    $sizes_a = array();
	    if ( is_array( $extra_opts ) && ! empty( $extra_opts ) ) {
	        $sizes_a = array_merge( $sizes_a, $extra_opts );
	    }
	    foreach ( $image_sizes as $size_name ) {
	        $sizes_a[$size_name] = $size_name;
	    }
	    return $sizes_a;
	}

    /**
     * Get list of all color schemes
     * @param  array  $extra_opts 
     * @return array
     */
    function getSchemes( ){
        $schemes = array();
        $color_scheme = ' ' . get_theme_mod( 'color_scheme', 'dark' );
        $schemes[$color_scheme] = esc_html__( 'Default (Selected in Customizer)', 'epron-toolkit' );
        $schemes['dark']        = esc_html__( 'Dark', 'epron-toolkit' );
        $schemes['light']       = esc_html__( 'Light', 'epron-toolkit' );
        return $schemes;
    }



    /**
     * Get Background Code
     * @param  $bg json
     * @return false|string
     */
    function generateBG( $bg ) {

        if ( json_decode( $bg ) !== null ) {
            
            $css = '';            
            $data = json_decode( $bg, true );
            
            // Image
            if ( isset( $data['image'] ) ) {
                $image = wp_get_attachment_image_src( $data['image'], 'full' );
                $image = $image[0];
                // If image exists
                if ( $image ) {
                    $css .= 'background-image: url( ' . esc_url( $image ) . ');'."\n";
                }
            } 

            // Color
            if ( isset( $data['color'] ) ) {
                $css .= 'background-color:' . esc_attr( $data['color'] ) . ';'."\n";
            }

            // Position
            if ( isset( $data['position'] ) ) {
                $css .= 'background-position:' . esc_attr( $data['position'] ) . ';'."\n";
            }

            // Repeat
            if ( isset( $data['repeat'] ) ) {
                $css .= 'background-repeat:' . esc_attr( $data['repeat'] ) . ';'."\n";
            }

            // Attachment
            if ( isset( $data['attachment'] ) ) {
                $css .= 'background-attachment:' . esc_attr( $data['attachment'] ) . ';'."\n";
            }

            // Size
            if ( isset( $data['size'] ) ) {
                $css .= 'background-size:' . esc_attr( $data['size'] ) . ';'."\n";
            }

            return $css;
        } else {
            return false;
        }
    }


	/**
     * Get Revo Slider list
     * @param  null
     * @version 1.1.0 [compatible with Revo Slider 6+]
     * @return array
     */
    public function getRevoSliders(){
        $intro_revslider = array( '' => esc_html__( 'Select slider...', 'epron-toolkit' ) );
        if ( class_exists( 'RevSlider' ) && function_exists( 'rev_slider_shortcode' ) ) {
            if ( defined('RS_REVISION') && version_compare( RS_REVISION, '6.0.0' ) >= 0 ) {
                $rev_slider = new RevSlider();
                $slides = $rev_slider->get_sliders();

                if ( ! empty( $slides ) ) {
                    $count = 0;
                    foreach ($slides as $slide) {
                        $alias = $slide->alias;
                        $title = $slide->title;
                        $intro_revslider[$alias] = $title;
                        $count++;
                    }
                }
            }
        } 
        return $intro_revslider;
    }


	/**
	 * Get taxonomies 
	 * @param  string $tax_name 
	 * @return array
	 */
	function getTax( $tax_name ){
	   
	    $tax_a = array();
	    $args = array(
	        'hide_empty' => false
	    );

	    if ( taxonomy_exists( $tax_name ) ) {
	        $taxonomies = get_terms( $tax_name, $args );
	        
	        foreach ( $taxonomies as $taxonomy ) {
	            $tax_a[$taxonomy->term_id] = $taxonomy->name;
	        }
	    }
	    return $tax_a;
	}


	/**
	 * Get posts 
	 * @param  string $post_type 
	 * @return array
	 */
	function getPosts( $post_type = 'post' ){
	    global $wpdb;

	    /* Get Audio Tracks  */
	    $posts = array( 'none' => esc_html__( 'Select...', 'epron-toolkit' ) );
	    $posts_query = $wpdb->prepare(
	        "
	        SELECT
	            {$wpdb->posts}.id,
	            {$wpdb->posts}.post_title
	        FROM 
	            {$wpdb->posts}
	        WHERE
	            {$wpdb->posts}.post_type = %s
	        AND 
	            {$wpdb->posts}.post_status = 'publish'
	        ",
	        $post_type
	    );

	    $sql_posts = $wpdb->get_results( $posts_query );
	      
	    if ( $sql_posts ) {
	        $count = 1;
	        foreach( $sql_posts as $track_post ) {
	            $posts[$track_post->id] = $track_post->post_title;
	            $count++;
	        }
	    }

	    return $posts;
	}


	/**
	 * Get Events
	 * @return array
	 */
	public function getEvents() {

		 $future_tax = array(
	        array(
				'taxonomy' => 'wp_event_type',
				'field'    => 'slug',
				'terms'    => 'future-events'
	         )
	    );
	    $future_events = get_posts( array(
	        'post_type' => 'wp_events_manager',
	        'showposts' => -1,
	        'tax_query' => $future_tax,
	        'orderby'   => 'meta_value',
	        'meta_key'  => '_event_date_start',
	        'order'     => 'ASC'
	    ));

	    $events = array();
	    foreach( $future_events as $event ) {
	        $date = get_post_meta( $event->ID, '_event_date_start', true );
	        $events[$event->ID] = $event->post_title . ' ' . $date;
	    }
	    return $events;

	}


	/**
	 * Get all sliders in array
	 * @return array
	 */
	public function getSliders() {

		global $wpdb;

		$slider = array();
	    $slider_post_type = 'epron_slider';
	    $slider_query = $wpdb->prepare(
	        "
	        SELECT
	            {$wpdb->posts}.id,
	            {$wpdb->posts}.post_title
	        FROM 
	            {$wpdb->posts}
	        WHERE
	            {$wpdb->posts}.post_type = %s
	        AND 
	            {$wpdb->posts}.post_status = 'publish'
	        ",
	        $slider_post_type
	    );

	    $sql_slider = $wpdb->get_results( $slider_query );
	    $slider[''] = '';
	    if ( $sql_slider ) {
	        $count = 0;
	        foreach( $sql_slider as $track_post ) {
	            $slider[$track_post->post_title] = $track_post->id;
	            $count++;
	        }
	    }
	    return $slider;
	}


	/**
	 * Get all tracks in array
	 * @return array
	 */
	public function getTracks($tracks_post_type = '') {

		 global $wpdb;

	    /* Get Audio Tracks  */
	    $tracks = array( 'none' => esc_html__( 'Select tracks...', 'epron-toolkit' ) );
	    $tracks_query = $wpdb->prepare(
	        "
	        SELECT
	            {$wpdb->posts}.id,
	            {$wpdb->posts}.post_title
	        FROM 
	            {$wpdb->posts}
	        WHERE
	            {$wpdb->posts}.post_type = %s
	        AND 
	            {$wpdb->posts}.post_status = 'publish'
	        ",
	        $tracks_post_type
	    );

	    $sql_tracks = $wpdb->get_results( $tracks_query );
	      
	    if ( $sql_tracks ) {
	        $count = 1;
	        foreach( $sql_tracks as $track_post ) {
	            $tracks[$track_post->id] = $track_post->post_title;
	            $count++;
	        }
	    }

	    return $tracks;
	}


	/**
	 * Get the theme option
	 * @return string|bool|array
	 */
	public function option( $option, $default = null ) {

		if ( $this->theme_panel === false || ! is_array( $this->theme_panel )  || ! isset( $option ) ) {
			return false;
		}
		if ( isset( $this->theme_panel[ $option ] ) ) {
			return $this->theme_panel[ $option ];
		} elseif ( $default !== null ) {
			return $default;
		} else {	
			return false;
		}
	
	}


}