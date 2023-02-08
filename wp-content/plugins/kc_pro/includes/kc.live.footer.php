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

global $kc_front;

?>
<div id="kc-element-placeholder" class="kc-boxholder">
	<div class="mpb-tooltip move tip cxols">
		<span class="label" title="<?php _e( 'Drag & drop to arrange this element', 'kingcomposer' ); ?>"></span>
		<span class="tips">
			<ol>
				<li class="short">
					<span data-action="edit">
						<i class="fa-edit" title="<?php _e( 'Edit', 'kingcomposer' ); ?>"></i> <?php _e( 'Edit', 'kingcomposer' ); ?>
					</span>
					<span data-action="copy">
						<i class="fa-copy" title="<?php _e( 'Copy', 'kingcomposer' ); ?>"></i> <?php _e( 'Copy', 'kingcomposer' ); ?>
					</span>
					<span data-action="double">
						<i class="fa-clone" title="<?php _e( 'Double', 'kingcomposer' ); ?>"></i> <?php _e( 'Double', 'kingcomposer' ); ?>
					</span>
					<span data-action="delete">
						<i class="fa-recycle" title="<?php _e( 'Delete', 'kingcomposer' ); ?>"></i> <?php _e( 'Delete', 'kingcomposer' ); ?>
					</span>
				</li>
			</ol>
		</span>
	</div>
	<div class="mpb mpb-top" data-dir="top">
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
		<span class="kc-marginer"><?php _e('margin', 'kingcomposer'); ?></span>
	</div>
	<div class="mpb mpb-right" data-dir="right">
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
		<span class="kc-marginer"><?php _e('margin', 'kingcomposer'); ?></span>
	</div>
	<div class="mpb mpb-bottom" data-dir="bottom">
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
		<span class="kc-marginer"><?php _e('margin', 'kingcomposer'); ?></span>
	</div>
	<div class="mpb mpb-left" data-dir="left">
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
		<span class="kc-marginer"><?php _e('margin', 'kingcomposer'); ?></span>
	</div>
</div>
<?php for( $i=0; $i<10; $i++ ){ ?>
<div id="kc-column-<?php echo $i; ?>-placeholder" class="kc-boxholder kc-column-holder" data-col-index="<?php echo $i; ?>">
	<div class="mpb mpb-top" data-dir="top">
		<ul class="cxols cxols_row top">
			<li class="tip col-control row-control kc-row-placeholder-move">
				<label><?php _e( 'ROW', 'kingcomposer' ); ?></label>
				<span class="tips">
					<ol>
						<li class="short">
							<i class="fa-recycle" title="<?php _e( 'Delete', 'kingcomposer' ); ?>" data-action="delete" data-target="row"></i>
							<i class="fa-edit" title="<?php _e( 'Edit', 'kingcomposer' ); ?>" data-action="edit" data-target="row"></i>
							<i class="fa-clone" title="<?php _e( 'Double', 'kingcomposer' ); ?>" data-action="double" data-target="row"></i>
							<i class="fa-copy" title="<?php _e( 'Copy', 'kingcomposer' ); ?>" data-action="copy" data-target="row"></i>
						</li>
						<li data-action="stretch-content" data-target="row">
							<i class="sl-frame"></i> <?php _e( 'Stretch content', 'kingcomposer' ); ?>
						</li>
						<li data-action="save-section" data-target="row">
							<i class="sl-cursor"></i> <?php _e( 'Save to section', 'kingcomposer' ); ?>
						</li>
						<li data-action="row-order">
							<input type="number" data-target="row" />
							<button data-target="row"><?php _e( 'Row order', 'kingcomposer' ); ?></button>
						</li>
					</ol>
				</span>
			</li>
			<li class="col-info">100%</li>
		</ul>
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
	</div>
	<div class="mpb mpb-right" data-dir="right">
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
	</div>
	<div class="mpb mpb-bottom" data-dir="bottom">
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
		<?php if( $i === 0 ){ ?>
		<span class="kc-marginer" id="kc-marginner-bottom-row"><?php _e('margin', 'kingcomposer'); ?></span>
		<?php } ?>
	</div>
	<div class="mpb mpb-left" data-dir="left">
		<?php if( $i > 0 ){ ?>
		<div class="handle-resize">
		</div>
		<?php } ?>
		<span class="kc-paddinger"><?php _e('padding', 'kingcomposer'); ?></span>
	</div>
</div>
<?php } ?>
<div id="kc-section-link-placeholder" class="kc-boxholder" data-label"">
	<div class="kc-row-placeholder-move">
		<button class="edit-row-section" data-link="<?php echo admin_url('/?page=kingcomposer&kc_action=live-editor&id='); ?>"><i class="sl-note"></i> <?php _e('Go to edit this section', 'kingcomposer'); ?></button>
		<button class="select-row-section"><i class="sl-list"></i> <?php _e('Select another section', 'kingcomposer'); ?></button>
		<button class="remove-row-section"><i class="sl-close"></i> <?php _e('Remove', 'kingcomposer'); ?></button>
	</div>
</div>
<div id="kc-overlay-placeholder"><!--This will prevent the mouse out of parent frame when dragding--></div>
<div id="kc-delete-elements-cached"><!--This will storage delete elements, ready for undo immediately--></div>
<img width="50" src="<?php echo KC_URL; ?>/assets/images/drag.png" id="kc-ui-handle-image" />
<img width="50" src="<?php echo KC_URL; ?>/assets/images/drag-copy.png" id="kc-ui-handle-image-copy" />
<script type="text/javascript">

	if( top.kc === undefined )
		top.kc = { front : {}, frame : {} };

	top.kc.storage = <?php echo json_encode( $kc_front->storage ); ?>;

	(function ($){

		top.kc.frame = {
			doc : document,
			window : window,
			html : $('html').get(0),
			body : $('body').get(0),
			$ : jQuery
		}

		if (top.kc.detect !== undefined)
			top.kc.detect.frame = top.kc.frame;

		$ (document).ready(function(){

			if (top.kc.front === undefined || typeof(top.kc.front.init) != 'function')
				top.kc.init_front_ready = true;
			else top.kc.front.init();

		}).
		on ('mouseover', function(e){
			if (top.kc !== undefined && top.kc.detect !== undefined)
				top.kc.detect.hover(e);

		}).
		on ('click', function(e){
			if (top.kc !== undefined && top.kc.detect !== undefined)
				top.kc.detect.click(e);

		}).
		on ('dblclick', function(e){
			if (top.kc !== undefined && top.kc.detect !== undefined)
				top.kc.detect.dblclick(e);

		});

		top.kc.do_callback = function(callback, el){

			for (var i in callback)
				eval ('('+callback[i].callback.toString()+')( jQuery(\'[data-model="'+callback[i].model+'"]\'), jQuery, callback[i] );');
		}


	}) (jQuery);

</script>
