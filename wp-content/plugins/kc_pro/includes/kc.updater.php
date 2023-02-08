<?php
/**
*
*	King Composer
*	(c) KingComposer.com
*
*/

if(!defined('ABSPATH')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class kc_pro_updater{
	
	/*
	*	the slug of primary file
	*/
	
	private $source = 'http://bit.ly/kc-pro';
	
	function __construct(){
		
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

		add_filter( 'plugins_api', array( &$this, 'view_detail' ), 10, 3 );

		add_action( 'in_plugin_update_message-' .KCP_BASENAME, array( &$this, 'message_link' ) );
		
	}

	public function check_update ( $transient ){
		
		global $kc;
		
		if( isset( $transient->response[ KCP_BASENAME ] ) )
			unset( $transient->response[ KCP_BASENAME ] );
			
		$response = $this->get_response();
		$plugin_info = get_plugin_data( KCP_PLUGIN );
		
		$pdk = $kc->get_pdk();
		if ($pdk['pack'] != 'trial' && isset($pdk['key']) && strlen($pdk['key']) !== 41)
			update_option ('kc_tkl_pdk', array('pack'=>'trial','date'=>time(),'stt'=>0,'key'=>''));
		
		if(isset($response) && !empty($response) && isset($response['new_version']))
		{
			if(isset( $plugin_info['Version'] ) && !version_compare( $plugin_info['Version'], $response['new_version'], '<' ))
				return $transient;
		}
		else return $transient;
			
		$response = array_merge( array( 'new_version' => '', 'url' => '', 'package' => '', 'name' => '' ), $response );
		
		if( $response === false )
			return $transient;
		
	    $obj = new stdClass();
	    
	    $obj->slug = KCP_SLUG;
	    $obj->new_version = $response['new_version'];
	    $obj->url = $response['url'];
	    $obj->name = $response['name'];
	    $obj->package = $response['package'];

	    $transient->response[ KCP_BASENAME ] = $obj;

	    return $transient;
	    
	}
	
	public function get_response( $detail = false ){

		global $kc, $kc_pro;
		
		$settings = $kc->settings();
		$key = isset( $settings['license'] ) ? esc_attr( $settings['license'] ) : '';
		$theme = sanitize_title( basename( get_template_directory() ) );
		$domain = str_replace( '=', '-d', base64_encode( site_url() ) );
		
		$url = (is_ssl() ? 'https' : 'http').'://kingcomposer.com/updates/?kc_store_action=u&verify='.$key.'&theme='.$theme.'&domain='.$domain;
		
		if( $detail !== false )
			$url .= '&view_details=1';
	
		$request = wp_remote_get($url);
		$response = wp_remote_retrieve_body( $request );
		if (is_wp_error($request) || empty($response)) {
			$response = file_get_contents($url);
		}
		$response = (array)json_decode( $response );

		return $response;
	
	}
		
	public function view_detail( $a, $b, $arg ) {
		
		if ( isset( $arg->slug ) && strtolower( $arg->slug ) === strtolower( KCP_SLUG ) ) {
			
			$update_plugin = get_site_transient( 'update_plugins' );
			
			$response = $this->get_response( true );
			
			$info = array( 
				'name' => 'kingcomposer', 
				'banners' => array( 
					'low' => 'https://kingcomposer.com/updates/banner-1140x500.png', 
					'high' => 'https://kingcomposer.com/updates/banner-1140x500.png' 
				), 
				'sections' => array( 
					'Error' => 'Sorry! Could not get details from server this moment.' 
				) 
			);

			if ( isset( $response ) && is_array( $response ) )
				$info = array_merge( $info, $response );

			$detail = array();
			
			foreach( $info as $key => $value  ){
				if( is_object( $value ) )
					$value = (array)$value;
				
				$detail[ $key ] = $value;
			}
			
			$detail['slug'] = KCP_SLUG;
			$detail['author'] = 'king-theme';
			$detail['homepage'] = 'https://kingcomposer.com';
			
			return (object)$detail;
			
		}

		return false;
	}
	
	public function message_link(){

		if ( 1 !== 1 ) {
		/* Verify to upgrade */
			?><script>
				var viewdetails = document.querySelectorAll("tr#kingcomposer-update .update-message a.thickbox")[0];
				while( viewdetails.nextSibling ){
					viewdetails.parentNode.removeChild( viewdetails.nextSibling );
				}
		   </script><?php
			echo '<br />Sorry! Plugin update has been disabled because you have not submitted License Key.';
			echo '<br />Please submit License Key via <a href="'.admin_url('/?page=kingcomposer#kc_product_license').'">Settings Page</a> to update';
			echo ' OR <a href="'.$this->buy_link.'" target=_blank>' . __( 'Download new version from CodeCanyon.', 'kingcomposer' ) . '</a>';
		} else {
			//echo ' or <a href="' . wp_nonce_url( admin_url( 'update.php?action=upgrade-plugin&plugin=' .KCP_BASENAME ), 'upgrade-plugin_' .KCP_SLUG.'.php' ) . '">' . __( 'Update KingComposer Now.', 'kingcomposer' ) . '</a>';
		}
		
	}
	
}

/**
*	Run
*/

new kc_pro_updater();

