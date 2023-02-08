<?php
/**
 * Theme Name:      Epron
 * Theme Author:    Mariusz Rek - Rascals Themes
 * Theme URI:       http://rascalsthemes.com/epron
 * Author URI:      http://rascalsthemes.com
 * File:            admin-init.php
 * @package epron
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* Gutenberg FIX
 -------------------------------- */
/**
 * Modify the height of a specific CSS class to fix an issue in Chrome 77 with Gutenberg.
 *
 * @see https://github.com/WordPress/gutenberg/issues/17406
 */
add_action(
	'admin_head',
	function() {
		echo '<style>.block-editor-writing-flow { height: auto; }</style>'; // phpcs:ignore
	}
);


/* Main page
 -------------------------------- */
function epron_register_admin_pages() {

    add_theme_page( esc_html__( 'Epron', 'epron' ), esc_html__( 'Epron', 'epron' ), 'edit_theme_options', 'admin-welcome', 'epron_admin_page_welcome');

}
add_action('admin_menu', 'epron_register_admin_pages', 1 );


/**
 * Render admin pages header
 * @version 1.1.0
 * @return void
 */
function epron_admin_page_header() {

	$pages = array(

		array(
			'title' => esc_html__( 'Welcome', 'epron' ),
			'title_attr' => esc_html__( 'Welcome', 'epron' ),
			'id' => 'admin-welcome'
		),
		array(
			'title' => esc_html__( 'System Status', 'epron' ),
			'title_attr' => esc_html__( 'Please install Epron Toolkit plugin to activate this tab.', 'epron' ),
			'id' => 'inactive'
		),
		array(
			'title' => esc_html__( 'Theme Plugins', 'epron' ),
			'title_attr' => esc_html__( 'Theme Plugins', 'epron' ),
			'id' => 'admin-plugins'
		),
		array(
			'title' => esc_html__( 'Install Demos', 'epron' ),
			'title_attr' => esc_html__( 'Please install Epron Toolkit plugin to activate this tab.', 'epron' ),
			'id' => 'inactive'
		),
		array(
			'title' => esc_html__( 'Customize', 'epron' ),
			'title_attr' => esc_html__( 'Please install Epron Toolkit and Kirki Customizer plugins to activate this tab.', 'epron' ),
			'id' => 'inactive'
		),
		array(
			'title' => esc_html__( 'Theme Panel', 'epron' ),
			'title_attr' => esc_html__( 'Theme Panel', 'epron' ),
			'id' => 'theme-panel'
		)

	);

	if  ( has_filter( 'rascals_admin_menu' ) ) {
		$pages = apply_filters( 'rascals_admin_menu', $pages );
	}
	
	$page_link = 'admin.php?page=';

	$theme = wp_get_theme();
	?>


	<h3 class="rascals-theme-name"><?php echo esc_html( $theme->get( 'Name' ) ); ?><span class="rascals-version"> v <?php echo esc_html( $theme->get( 'Version' ) ); ?></span></h3>

	<div class="wrap about-wrap rascals-admin-header">
	    <h2 class="nav-tab-wrapper"> 
			<?php
			foreach ( $pages as $mp_page ) {
			    $extra_classes = '';

			    $title_attr = $mp_page['title_attr']; 

		        /* Redirect */
		        if ( $mp_page['id'] === 'inactive' ) {
		        	$page_link = '#';
		        	$extra_classes .= ' inactive';
		        } else if ( strpos( $mp_page['id'], '.php' ) !== false ) {
		        	$page_link = $mp_page['id'];
		        } else {
		        	$page_link = 'admin.php?page=' . esc_attr( $mp_page['id'] );
		        }
		        
		        if ( isset( $_GET['page'] ) && ( $_GET['page'] === $mp_page['id'] ) ) { 
		        	$extra_classes .= ' nav-tab-active';
		        }

		        ?>	
		        <a href="<?php echo esc_url( $page_link ) ?>" title="<?php echo esc_attr( $title_attr ) ?>" class="nav-tab rascals-nav-tab <?php echo esc_attr( $extra_classes ); ?> "><?php echo esc_html( $mp_page['title'] ) ?></a>
		        <?php
		    }
		    ?>
		</h2>
	</div>
    <?php
	
}


/* ==================================================
  Admin Notices
================================================== */
if ( ! function_exists( 'epron_admin_notices' ) ) :
function epron_admin_notices() {

	$theme = wp_get_theme();
	$theme->get( 'Version' );
	return; 
	$output = '<div class="rascals-admin-notice warning"><p>';
	$output .= esc_html__( 'Please note! The current version of Epron is created from scratch. Please deactivate old Epron Extensions plugin and remove it from WordPress because it will no longer be required. The new required plug-in is called Epron Toolki and must be installed. Also we changed the Customizator now please install the Kirki plugin which is located in the plugins sections. Open it and save settings again. If your widgets disappear, find them in the "Inactive Widgets" pane and re-add them into the sidebar(s) of the theme. These changes are necessary to meet the latest ThemeForest requirements. We apologize for the inconvenience if you have any problems, please contact us immediately.', 'epron' );
	$output .= '</p></div>';
	$output = '';
	 
	return $output;
	
}
endif;


/* ==================================================
  Admin Scripts 
================================================== */
function epron_admin_scripts() {

	wp_enqueue_style( 'rascals-admin-pages', get_template_directory_uri() . '/admin/assets/css/admin-page.css' );

}
add_action( 'admin_enqueue_scripts', 'epron_admin_scripts' );


/* ==================================================
  Welcome Page 
================================================== */
function epron_admin_page_welcome() {
	require_once( 'admin-welcome.php' );
}


/* ==================================================
  Plugins Page 
================================================== */
require_once( 'admin-plugins.php' );


/* ==================================================
  Theme Panel 
================================================== */
if ( ! class_exists( 'RascalsThemePanel' ) ) {

	/* Load Theme panel class */
	require_once( trailingslashit( get_template_directory() ) . 'admin/classes/class-rascals-panel.php' );

	/* Init Panel */
	require_once( trailingslashit( get_template_directory() ) . 'admin/panel-options.php' );
}
