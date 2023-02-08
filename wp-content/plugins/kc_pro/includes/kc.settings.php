<?php
/**
*
*	King Composer
*	(c) KingComposer.com
*
*/
if(!defined('KCP_PLUGIN')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$kc = KingComposer::globe();
$pdk = $kc->get_pdk();
$settings = $kc->settings();

$kc_pro_els = array(
	'our_teams' => __('Our Teams', 'kc_pro'),
	'our_works' => __('Our Works', 'kc_pro'),
	'testimonials' => __('Testimonials', 'kc_pro'),
	'faqs' => __('FAQs', 'kc_pro'),
	'pricing_tables' => __('Pricing Tables', 'kc_pro'),
	'image_fadein' => __('Image Fadein', 'kc_pro'),
	'tooltip' => __('Tooltip', 'kc_pro'),
	'divider' => __('Divider', 'kc_pro'),
	'box_alert' => __('Box Alert', 'kc_pro'),
	'blog_posts' => __('Blog Posts', 'kc_pro'),
	'subscribe' => __('Subscribe Form', 'kc_pro'),
	'timeline' => __('Blog Timeline', 'kc_pro'),
	'cf7' => __('Contact Form 7 (by slug)', 'kc_pro'),
	'masterslider' => __('MasterSlider (by slug)', 'kc_pro'),
);

if( isset( $settings['pro'] ) && $settings['pro'] == 'disabled' ){
	$settings['pro'] = array();
	foreach( $kc_pro_els as $k => $v )
		$settings['pro'][$k] = 'disable';
}
if( !isset( $settings['pro'] ) || !is_array( $settings['pro'] ) )
	$settings['pro'] = $kc_pro_els;

?>

<?php if( $pdk['pack'] == 'trial' ){ ?>
<div class="kc-notice">
	<p>
		<?php
		if( $pdk['date'] > time() ){
			_e('Welcome to KC Pro! You are using the trial version', 'kingcomposer');
			echo ' ('.round(($pdk['date']-time())/86400).__(' days left', 'kingcomposer'). ').'; 
		}else{
			_e('Your free 7-day trial has expired. Please submit license key to continue using Front-End Live Editor.', 'kingcomposer');	
		} 
		?> 
		<a href="#" onclick="jQuery('#kc_product_license-tab').trigger('click')">
			<?php _e('Enter your license key for KC Pro!', 'kingcomposer'); ?>
		</a>
	</p>
</div>
<div class="kc-pro-guider"><img src="<?php echo KCP_URI; ?>/assets/images/guide.jpg" alt="" width="765" /></div>
<?php }else{ ?>
<style type="text/css">#kc_pro-tab{display: none;}</style>
<script type="text/javascript">jQuery('#kc_pro-tab').remove();</script>
<?php } ?>
