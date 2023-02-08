<?php
/**
*
*	King Composer
*	(c) KingComposer.com
*
*/
if(!defined('KC_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
	
$kc = KingComposer::globe();
$kc_maps = $kc->get_maps();
$id = !empty($_GET['id']) ? esc_attr($_GET['id']) : '';
$post = get_post($id);
?>
<script type="text/javascript">
	jQuery('#wpadminbar,#wpfooter,#adminmenuwrap,#adminmenuback,#adminmenumain,#screen-meta').remove();
</script>
<div id="kc-preload">
	<h3 class="mesg loading">
		<span class="kc-loader"></span>
		<br /><?php _e('Loading', 'kingcomposer'); ?>
	</h3>
</div>
<div id="wpadminbar">
	<?php ob_start();?>
    <a class="screen-reader-shortcut" href="#wp-toolbar" tabindex="1"><?php _e('Skip to toolbar', 'kingcomposer'); ?></a>
    <div class="quicklinks" id="kc-top-nav">
        <ul class="ab-top-menu">
            <li id="kc-bar-logo" class="menupop">
            	<a class="ab-item" title="<?php _e('Visit the KingComposer\'s home page', 'kingcomposer'); ?>" target=_blank href="http://KingComposer.com">
	            	<img src="<?php echo KC_URL; ?>/assets/images/logo_white.png" height="25" />
	            </a>
            </li>
            <li class="kc-curent-editing">
            	<?php
	            	echo $post->post_type.': '.wp_trim_words($post->post_title, 4);
            	?>
            </li>
            <li id="kc-content-settings" class="mtips">
            	<i class="fa-cog"></i>
            	<span class="mt-mes"><?php _e('Content settings', 'kingcomposer'); ?></span>
            </li>
        </ul>
        <ul id="kc-top-toolbar" class="ab-top-secondary ab-top-menu">
            <li id="wp-admin-bar-exit" class="kc-bar-save mtips">
                <div class="ab-item">
                	<a href="#exit" id="kc-front-exit">
	                	<i class="fa-sign-in"></i>  <?php _e('Exit Editor', 'kingcomposer'); ?>
	                </a>
                </div>
                <span class="mt-mes"><?php _e('(ctrl+e)', 'kingcomposer'); ?></span>
            </li>
            <li id="wp-admin-bar-exit-back" class="kc-bar-save mtips">
                <div class="ab-item">
                	<a href="<?php echo admin_url('/post.php?post='.$id.'&action=edit'); ?>" id="kc-exit-backend">
	                	<i class="fa-paper-plane"></i> <?php _e('Back-End Editor', 'kingcomposer'); ?>
	                </a>
                </div>
                <span class="mt-mes"><?php _e('Edit page with backend editor (ctrl+b)', 'kingcomposer'); ?></span>
            </li>
            <li id="wp-admin-bar-save" class="kc-bar-save mtips">
                <div class="ab-item">
                	<a href="#save" id="kc-front-save">
	                	<i class="fa-check"></i> <?php _e('Save Changes', 'kingcomposer'); ?>
	                </a>
                </div>
                <span class="mt-mes"><?php _e('Press Ctrl+S to save content', 'kingcomposer'); ?></span>
            </li>
             <li id="kc-enable-inspect" class="mtips">
            	<i class="toggle"></i>
            	<span class="mt-mes"><?php _e('Enable / Disable inspect elements to edit', 'kingcomposer'); ?></span>
            </li>
            <li id="kc-bar-desktop-view" data-screen="100%" class="kc-bar-devices active mtips">
				<i class="fa-desktop"></i>
				<span class="mt-mes"><?php _e('Destop Mode', 'kingcomposer'); ?></span>
            </li>
            <li id="kc-bar-tablet-landscape-view" data-screen="1024" class="kc-bar-devices mtips">
				<i class="fa-tablet"></i>
				<span class="mt-mes"><?php _e('Tablet Mode', 'kingcomposer'); ?> (landscape 1024px)</span>
            </li>
            <li id="kc-bar-tablet-view" data-screen="768" class="kc-bar-devices mtips">
				<i class="fa-tablet"></i>
				<span class="mt-mes"><?php _e('Tablet Mode', 'kingcomposer'); ?> (768px)</span>
            </li>
            <li id="kc-bar-mobile-landscape-view" data-screen="767" class="kc-bar-devices mtips">
				<i class="fa-mobile"></i>
				<span class="mt-mes"><?php _e('Mobile Mode', 'kingcomposer'); ?> (landscape 767px)</span>
            </li>
            <li id="kc-bar-mobile-view" data-screen="479" class="kc-bar-devices mtips">
				<i class="fa-mobile"></i>
				<span class="mt-mes"><?php _e('Mobile Mode', 'kingcomposer'); ?> (479px)</span>
            </li>
            <li id="kc-curent-screen-view" data-screen="custom" class="kc-bar-devices mtips">
            	<i>100%</i>
            	<span class="mt-mes"><?php _e('Click to set custom screen', 'kingcomposer'); ?></span>
            </li>
            <li id="kc-bar-redo" class="mtips">
				<i class="fa-share"></i>
				<span class="mt-mes"><?php _e('Redo (ctrl+shift+z)', 'kingcomposer'); ?></span>
            </li>
            <li id="kc-bar-undo" class="mtips">
				<i class="fa-reply"></i>
				<span class="mt-mes"><?php _e('Undo (ctrl+z)', 'kingcomposer'); ?></span>
            </li>
            <li id="kc-bar-tour-view" class="mtips">
				<a href="#tour"><i class="fa-play-circle"></i> <?php _e('Videos', 'kingcomposer'); ?></a>
				<span class="mt-mes"><?php _e('Watch the quick tour video', 'kingcomposer'); ?></span>
            </li>
        </ul>
		<div id="kc-css-inspector" data-label="<?php _e('CSS Inspector', 'kingcomposer'); ?>">
			<i class="fa-paint-brush"></i>
		</div>
    </div>
	<?php
	$admin_bar = ob_get_contents();
	ob_end_clean();
	echo $kc->apply_filters('kc_pro_admin_bar', $admin_bar);
	?>
</div>

<?php 

	kc_after_editor ($post);

	/*
	* Load live template	
	*/
	foreach ($kc_maps as $name => $map)
	{	
		if (isset( $map['live_editor'] ) && is_file( $map['live_editor'] ) && $map['flag'] != 'core')
		{
			echo '<script type="text/html" id="tmpl-kc-'.esc_attr( $name ).'-template">';
			@include( $map['live_editor'] );
			echo '</script>';
		} 
	} 

?>
