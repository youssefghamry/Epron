<?php
/*
Plugin Name: KC Pro!
Plugin URI: https://kingcomposer.com
Description: KC Pro! is professional front-end builder for KingComposer page builder.
Version: 1.9.5
Author: King-Theme
Author URI: http://king-theme.com
*/

define( 'KCP_VERSION', '1.9.5' );

define( 'KCPS', DIRECTORY_SEPARATOR );

define( 'KCP_OPTNAME', 'kc_pro_options' );

define( 'KCP_PLUGIN', __FILE__ );

define( 'KCP_BASENAME', plugin_basename( KCP_PLUGIN ) );

define( 'KCP_SLUG', basename(dirname(__FILE__)));

define( 'KCP_NAME', trim( dirname( KCP_BASENAME ), '/' ) );

define( 'KCP_DIR', untrailingslashit( dirname( KCP_PLUGIN ) ) );

define( 'KCP_URI', untrailingslashit( plugins_url( '', KCP_PLUGIN ) ) );

define( 'KCP_THEME', get_template_directory().'/kc_pro/' );

define( 'KCP_CTHEME', get_stylesheet_directory().'/kc_pro/' );

define( 'KCP_THEME_URI', get_template_directory_uri().'/kc_pro/' );

define( 'KCP_CTHEME_URI', get_stylesheet_directory_uri().'/kc_pro/' );

define( 'KCP_PPP', get_option( 'posts_per_page' ) );



/********************************************************/
/*                      Class master                     */
/********************************************************/

class kc_pro{
	
	public $action		= null;
	private $pdk		= array();
	private $std_pdk	= array(
		'pack' => '', 
		'key' => '', 
		'theme' => '', 
		'domain' => '', 
		'date' => '', 
		'stt' => ''
	);
	
	public function __construct() {
		
		if (isset($_GET['kc_action'] ) && !empty($_GET['kc_action']))
			$this->action = sanitize_title($_GET['kc_action']);
		/*
		*
		*	@ kc pro - register actions
		*
		*/
		add_action ('init', array(&$this, 'init' ), 1);
		add_action ('kc-front-before-header', array(&$this, 'front_builder_load'));
		add_action ('kc_tmpl_storage', array(&$this, 'tmpl_storage'));
		add_action ('kc_tmpl_nocache', array(&$this, 'tmpl_nocache'));
		// activate action
		register_activation_hook (KCP_PLUGIN, array(&$this, 'plugin_activate'));
		
		add_action ('kc-live-edit-link', array(&$this, 'live_edit_link'));
		/*
		*	Actions when live builder is active
		*/
		if ($this->action == 'live-editor')
		{	
			add_action ('kc-content-before', array(&$this, 'content_before_process'), 1);
			add_action ('wp_footer', array(&$this, 'front_footer' ), 1);
			add_action ('kc_after_admin_footer', array(&$this, 'after_admin_footer'), 0);
			// load css
			add_filter ('kc_enqueue_styles', array(&$this, 'frontend_editor_styles'));
			add_filter ('kc-core-styles', array(&$this, 'backend_editor_styles'));
			add_filter ('kc-core-scripts', array(&$this, 'backend_editor_scripts'));
		}
		
	}
	/*
	* @ wp-init action with index = 1
	*/
	public function init(){
		/*
		*	@ KC Pro!
		*	Initiate action
		*/
		if (!function_exists('is_plugin_active'))
		{
			include_once (ABSPATH . 'wp-admin/includes/plugin.php');
		}
		/*
		*	Show notice & stop if the kc inactive
		*/
		if (!is_plugin_active('kingcomposer/kingcomposer.php') )
		{
			if (is_admin())
				add_action('admin_notices', array( &$this, 'missing_notices'));
	
			return;	
	
		}
		/*
		*	Replace settings tab of kc pro
		*/
		remove_action ('kc-pro-settings-tab', 'kc_pro_settings_tab');
		add_action ('kc-pro-settings-tab', array( &$this, 'settings_tab'));
		// remove promo tab from main plugin
		remove_action ('kc-top-nav', 'kc_ask2try_btn');
		remove_action ('kc-switcher-buttons', 'kc_ask2try_btn');
		// add front-end editor buttons
		add_action ('kc-top-nav', array( &$this, 'switcher_live_editor'));
		add_action ('kc-switcher-buttons', array( &$this, 'switcher_live_editor'));
		
		if (!get_option ('kc_tkl_pdk', false))
		{	
			$this->std_pdk['pack'] 	= 'trial';
			$this->std_pdk['date'] 	= time()+604800;
			$this->std_pdk['stt']	= 0;
			// add default value
			add_option ('kc_tkl_pdk', $this->std_pdk, null, 'no');
		}
		
		$this->pdk = get_option ('kc_tkl_pdk');
		
		if( $this->action == 'revoke' )
			$this->revoke();
		
		// include updater
		require_once KCP_DIR . '/includes/kc.updater.php';
	
	}
	
	public function plugin_activate() {
		add_option('kc_pro_do_activation_redirect', true);
	}

	public function missing_notices(){

		if (is_dir(ABSPATH.KCPS.'wp-content'.KCPS.'plugins'.KCPS.'kingcomposer'))
		{
			$plugin_path = 'kingcomposer/kingcomposer.php';
			$active_url = wp_nonce_url( self_admin_url('plugins.php?action=activate&plugin='.$plugin_path), 'activate-plugin_'.$plugin_path );
			
			$call_action = '<a href="'.$active_url.'">'.__('Active KingComposer now', 'kc_pro').'</a>';
		
		}
		else
		{	
			$call_action = '<a href="'.admin_url('/plugin-install.php?s=kingcomposer+king-theme&tab=search&type=term').'">Click here to install KingComposer</a>';
		
		}
		
		?>
		 <div class="notice notice-warning" style="display:block !important;">
	        <p><?php printf( __( 'Oops, The KC Pro! requires KingComposer plugin. %s', 'kc_pro' ), $call_action ); ?></p>
	    </div>
		<?php
			
	}
	
	public function content_before_process( $content ){
			
		$content = trim($content);
		
		if (empty($content))
			return $content;
		
		if (strpos($content, '[kc_row') === false)
		{
			$content = '[kc_row use_container="yes" _id="'.rand(345464,4675677).'"]
							[kc_column width="100%"
								_id="'.rand(345464,4675677).'"]
								[kc_column_text]'.$content.'[/kc_column_text]
							[/kc_column]
						[/kc_row]';
		}
		
		return $content;
	
	}
	
	public function bottom_builder( $content ){
		
		ob_start();
			include KCP_DIR.KCPS.'includes'.KCPS.'kc.bottom.builder.php';
			$content .= ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function front_footer(){
		
		include KCP_DIR.KCPS.'includes'.KCPS.'kc.live.footer.php';
		
	}
	
	public function after_admin_footer(){
		
		include KCP_DIR.KCPS.'includes'.KCPS.'kc.templates.php';
		
	}
	
	public function frontend_editor_styles( $styles = array() ){
		
		$styles['kc-front-builder'] = array(
			'src'     => str_replace( array( 'http:', 'https:' ), '', trailingslashit( KCP_URI ) ) . 
						 'assets/css/kc.front.builder.css',
			'deps'    => '',
			'version' => KCP_VERSION,
			'media'   => 'all'
		);
		
		return $styles;
		
	}
	
	public function backend_editor_styles( $styles = array() ){
		
		$styles['live'] = trailingslashit( KCP_URI ).'assets/css/kc.live.builder.css';
		
		return $styles;
		
	}
	
	public function backend_editor_scripts( $scripts = array() ){
		
		$scripts['front-builder'] = trailingslashit( KCP_URI ).'assets/js/kc.front.js';
		$scripts['front-detect'] = trailingslashit( KCP_URI ).'assets/js/kc.detect.js';
		$scripts['front-changes'] = trailingslashit( KCP_URI ).'assets/js/kc.live.changes.js';
		
		return $scripts;
		
	}
	
	public function switcher_live_editor(){
		?>
		<a href="#kc_live_editor" onclick="kc.go_live()" class="kc-button green kc-live-editor-go">
			<i class="sl-paper-plane"></i> 
			<?php _e('Live edit with KC Pro!', 'kc_pro'); ?>
		</a>
		<?php
	}
	
	public function live_edit_link( $wp_admin_bar ){
		global $post, $wp_the_query, $wp_admin_bar;
		

		if (is_admin())
		{
			$screen = get_current_screen();
			if( $screen->base == 'post')
				$wp_admin_bar->add_node(
					array(
						'id'    => 'kc-edit',
						'title' => '<i class="fa-star"></i> '.__('Live edit with KC Pro!', 'kc_pro'),
						'href'  => admin_url('?page=kingcomposer&kc_action=live-editor&id=' . $post->ID )
					)
				);
		}else{
			$current_object = $wp_the_query->get_queried_object();

			if ( empty( $current_object ) )
				return;
			
			if ( ! empty( $current_object->post_type )
			     && ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			     && current_user_can( 'edit_post', $current_object->ID )
			     && $post_type_object->show_in_admin_bar
			     && $edit_post_link = get_edit_post_link( $current_object->ID ) )
			{
				$wp_admin_bar->add_menu( array(
					'id' => 'kc-edit',
					'title' => '<i class="fa-star"></i> '.__('Live edit with KC Pro!', 'kc_pro'),
					'href'  => admin_url('?page=kingcomposer&kc_action=live-editor&id=' . $current_object->ID )
				) );
			}
		}
	}

	public static function globe(){

		global $kc_pro;

		if (isset($kc_pro))
			return $kc_pro;
		else wp_die('KingComposer Addon Error: Global varible could not be loaded.');

	}

	public function settings_tab(){
		include KCP_DIR.KCPS.'includes'.KCPS.'kc.settings.php';
	}

	private function revoke(){
		
		$key = $_GET['key'];
		
		if (!isset($key) || empty($key) || strlen($key) != 41)
			return false;
			
		if ($key == $this->pdk['key'])
		{
			$this->std_pdk['pack'] 	= 'trial';
			$this->std_pdk['date'] 	= time();
			$this->std_pdk['stt']	= 0;
			$this->std_pdk['key']	= '';
			
			echo update_option ('kc_tkl_pdk', $this->std_pdk).':revoked';
			
		}
				
	}

	public function tmpl_storage(){
		
		global $kc;
		$kc_maps = $kc->get_maps();
		
		foreach ( $kc_maps as $name => $map )
		{	
			if (
				isset($map['live_editor']) && 
				is_file($map['live_editor']) && 
				strpos($map['live_editor'], KC_PATH.KDS.'shortcodes'.KDS.'live_editor') !== false &&
				$map['flag'] == 'core'
			)
			{
				echo '<script type="text/html" id="tmpl-kc-'.esc_attr( $name ).'-template">';
				@include( $map['live_editor'] );
				echo '</script>';
			} 
		} 
		
	}
	
	public function tmpl_nocache(){
		
		global $kc;
		$kc_maps = $kc->get_maps();
		
		foreach ( $kc_maps as $name => $map )
		{	
			if (
				isset($map['live_editor']) && 
				is_file($map['live_editor']) && 
				strpos($map['live_editor'], KC_PATH.KDS.'shortcodes'.KDS.'live_editor') === false &&
				$map['flag'] == 'core'
			)
			{
				echo '<script type="text/html" id="tmpl-kc-'.esc_attr( $name ).'-template">';
				@include( $map['live_editor'] );
				echo '</script>';
			} 
		}
		
	}

}

add_action( 'admin_init', 'kc_pro_admin_init' );

//register our settings
function kc_pro_admin_init() {
	
	if (get_option('kc_pro_do_activation_redirect', false)) 
	{

	    delete_option('kc_pro_do_activation_redirect');

	    if (function_exists('is_plugin_active') && is_plugin_active('kingcomposer/kingcomposer.php'))
	    	wp_redirect("admin.php?page=kingcomposer#kc_pro");
	}
	
}

/**************************************************/
	global $kc_pro;
	$kc_pro = new kc_pro();
/**************************************************/
