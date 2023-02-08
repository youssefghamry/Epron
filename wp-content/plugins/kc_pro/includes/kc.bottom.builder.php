<?php
/**
*
*	King Composer
*	(c) kingComposer.com
*
*/
if(!defined('KC_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
	
$kc = KingComposer::globe();
	
?>
<div id="kc-footers" class="kc-footers">
	<div class="kc-empty-screen">
		<h3><?php _e('You have a blank page', 'kingcomposer'); ?></h3>
		<p><?php _e('Add new row/column layout', 'kingcomposer'); ?></p>
	</div>
	<ul>
		<li class="basic-add" data-action="browse">
			<i class="et-expand"></i> <?php _e('Elements', 'kingcomposer'); ?><span class="m-a-tips"><?php _e('Browse all elements', 'kingcomposer'); ?></span>
		</li>
		<li class="one-column quickadd" data-content='[kc_row use_container="yes"][kc_column width="12/12"][/kc_column][/kc_row]'>
			<span class="grp-column"></span>
			<span class="m-a-tips"><?php _e('Add an 1-column row', 'kingcomposer'); ?></span>
		</li>
		<li class="two-columns quickadd"  data-content='[kc_row use_container="yes"][kc_column width="50%"][/kc_column][kc_column width="50%"][/kc_column][/kc_row]'>
			<span class="grp-column"></span>
			<span class="grp-column"></span>
			<span class="m-a-tips"><?php _e('Add a 2-column row', 'kingcomposer'); ?></span>
		</li>
		<li class="three-columns quickadd" data-content='[kc_row use_container="yes"][kc_column width="33.33%"][/kc_column][kc_column width="33.33%"][/kc_column][kc_column width="33.33%"][/kc_column][/kc_row]'>
			<span class="grp-column"></span>
			<span class="grp-column"></span>
			<span class="grp-column"></span>
			<span class="m-a-tips"><?php _e('Add a 3-column row', 'kingcomposer'); ?></span>
		</li>
		<li class="four-columns quickadd" data-content='[kc_row use_container="yes"][kc_column width="25%"][/kc_column][kc_column width="25%"][/kc_column][kc_column width="25%"][/kc_column][kc_column width="25%"][/kc_column][/kc_row]'>
			<span class="grp-column"></span>
			<span class="grp-column"></span>
			<span class="grp-column"></span>
			<span class="grp-column"></span>
			<span class="m-a-tips"><?php _e('Add a 4-column row', 'kingcomposer'); ?></span>
		</li>
		<li class="column-text quickadd" data-action="custom-push" data-content="custom">
			<i class="et-document"></i>
			<span class="m-a-tips"><?php _e('Push customized content and shortcodes', 'kingcomposer'); ?></span>
		</li>
		<li class="quickadd" data-action="paste" data-content='paste'>
			<i class="et-clipboard"></i>
			<span class="m-a-tips"><?php _e('Paste copied element', 'kingcomposer'); ?></span>
		</li>
		<li class="kc-online-presets" data-action="online-sections">
			<i class="et-genius"></i>
			<span class="m-a-tips"><?php _e('Sections/Templates KC Hub Online', 'kingcomposer'); ?></span>
		</li>
		<li class="kc-add-sections" data-action="sections">
			<i class="et-layers"></i>
			<span class="m-a-tips"><?php _e('Sections/Templates Library', 'kingcomposer'); ?></span>
		</li>
	</ul>
	<div class="kc-empty-screen">
		<p><?php _e('Right-click everywhere on elements to show control panel menus', 'kingcomposer'); ?></p>
	</div>
</div>
