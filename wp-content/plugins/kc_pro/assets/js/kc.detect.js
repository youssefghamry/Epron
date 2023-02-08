/*
 * King Composer Project
 *
 * (c) Copyright king-theme.com
 *
 * Must obtain permission before using this script in any other purpose
 *
 * kc.detect.js
 *
*/

( function($){
	
	if( typeof( kc ) == 'undefined' ){
		console.error('Could not load KingComposer core library');
		return;
	}

	kc.detect = {
		
		frame : kc.frame !== undefined ? kc.frame : {},
		
		holder : null,
		
		ob : null,
		
		locked : false,
		
		clicked : false,
		
		disabled : false,
		
		bone : ['kc_row', 'kc_row_inner', 'kc_column', 'kc_column_inner'],
		
		init : function(){
			
			this.frame.contents = $('#kc-live-frame').contents();
			
			this.wrap_node( this.frame.contents.find('body').get(0), true /* set true to prevent css render */ );
			
			var main = this.frame.contents.find('#kc-element-placeholder');
			
			var get_holder = function( main ){
				return{
					main : main,
					tooltip : main.find('.mpb-tooltip').get(0),
					top : main.find('.mpb-top').get(0),
					right : main.find('.mpb-right').get(0),
					bottom : main.find('.mpb-bottom').get(0),
					left : main.find('.mpb-left').get(0)
				}
			}
			
			this.holder = get_holder( main );
			this.holder.columns = [];
			this.holder.section = this.frame.contents.find('#kc-section-link-placeholder');
			
			this.holder.row_mb = kc.frame.$('#kc-marginner-bottom-row');
			
			for( var i = 0; i < 10; i ++ )
				this.holder.columns.push( get_holder( this.frame.contents.find('#kc-column-'+i+'-placeholder') ) );
			
			this.holder.columns[0].marginer = kc.frame.$('#kc-row-marginer-bottom').get(0);
				
			this.bone = this.bone.concat( kc_maps_views ).concat( kc_maps_view );
			
			kc.frame.$('.kc-boxholder').attr('oncontextmenu', function(){return kc.detect.disabled;});
			
			kc.trigger({
				
				el: kc.frame.$('.kc-boxholder'),
				
				events: {
					'[data-action="edit"]:click': 'edit',
					'span.label:click': 'edit',
					'[data-action="double"]:click': 'double',
					'[data-action="copy"]:click': 'copy',
					'[data-action="add"]:click': 'add',			
					'.handle-resize:mousedown' : 'col_resize',
					'[data-action="col-exchange"]:click' : 'col_exchange',
					'[data-action="delete"]:click' : 'delete',
					
					// Section actions
					'[data-action="edit-section"]:click' : 'edit_section',
					'[data-action="double-section"]:click' : 'double_section',
					'[data-action="insert-section"]:click' : 'insert_section',
					'[data-action="delete-section"]:click' : 'delete_section',
					'[data-action="section-order"]:click' : 'order_section',
					
					'[data-action="insert-column"]:click' : 'insert_column',
					'[data-action="move-column"]:click' : 'move_column',
					
					'[data-action="stretch-content"]:click' : 'stretch_content',
					'[data-action="save-section"]:click' : 'save_to_section',
					'[data-action="row-order"] input:mouseover' : 'get_row_order',
					'[data-action="row-order"] button:click' : 'row_order',
					'[data-action="row-order"] input:keydown' : 'row_order_enter',
					
					'span.kc-marginer:mousedown': 'marginer',
					'span.kc-paddinger:mousedown': 'marginer',
					
					'.column-control:mouseover' : 'col_ctrl_over',
					'.row-control:mouseover' : 'row_ctrl_over',
					
					'.column-control:mouseout' : 'row_ctrl_out',
					'.row-control:mouseout' : 'row_ctrl_out',
					
					'.edit-row-section:click' : 'edit_row_section',
					'.select-row-section:click' : 'select_row_section',
					'.remove-row-section:click' : 'remove_row_section',
					
				},
				
				get_model : function(e){
					
					var model = kc.get.model( e.target ), _$ = kc.frame.$;
					
					if( $(e.target).data('target') == 'row' ){
						var ob = kc.detect.closest( _$('[data-model="'+model+'"]').parent().get(0) );
						if( ob !== null && ob[1] !== undefined )
							return ob[1];
						return null;
					}
					
					return model;
						
				},
				
				edit : function( e ){
					
					if( kc.detect.is_responsive() === true )
						return;	
					
					var model = e.data.get_model(e),
						el = _$('[data-model="'+model+'"]').get(0);
					
					kc.detect.on_edit( [el, model], e );
						
				},
				
				double : function( e ){
					kc.front.ui.element.double( e.data.get_model( e ) );	
				},
				
				delete : function( e ){
					
					var model = e.data.get_model( e );
					kc.front.ui.element.delete( model );
						
				},
				
				copy : function( e ){
					kc.front.ui.element.copy( e.data.get_model( e ) ); 
				},
				
				add : function( e ){
					var pop = kc.front.ui.element.add( e.target );
					if( $(e.target).data('pos') !== null )
						pop.data({'pos':$(e.target).data('pos')});
					else pop.data({'pos':'top'});
				},
				
				col_resize : function( e ){
				
					if( e.which !== undefined && e.which !== 1 )
						return false;
						
					$('html,body').stop();
					
					var index = $( e.target ).closest('div.kc-boxholder').data('col-index'),
						holder = kc.detect.holder.columns[index],
						pholder = kc.detect.holder.columns[index-1],
						el = kc.frame.$('[data-model="'+holder.main.data('model')+'"' ),
						width = kc.storage[holder.main.data('model')].args.width,
						pwidth = kc.storage[pholder.main.data('model')].args.width,
						mouseUp = function(e){
							
							kc.frame.$(kc.frame.doc).off('mousemove').off('mouseup');
							kc.frame.$('html,body').css({cursor:''}).removeClass('noneuser kc-resizing-cols');
							kc.detect.disabled = false;
							kc.detect.untarget();
							kc.detect.columns( e.data.el.get(0) );
							
							kc.front.css_system.push_to( e.data.emodel );
							kc.front.css_system.push_to( e.data.pmodel );
							
							e.data.el.css({width: ''}).removeClass (function (index, css) {
							    return (css.match (/(^|\s)kc_col-sm-\S+/g) || ['kc_col-of-5']).join(' ');
							}).addClass(kc.front.ui.column.width_class( kc.storage[e.data.emodel].args.width ));
							
							e.data.pel.css({width: ''}).removeClass (function (index, css) {
							    return (css.match (/(^|\s)kc_col-sm-\S+/g) || ['kc_col-of-5']).join(' ');
							}).addClass(kc.front.ui.column.width_class( kc.storage[e.data.pmodel].args.width ));
							
						},
						mouseMove = function( e ){
							
							e.preventDefault();
							e.data.offset = e.clientX-e.data.left;
							
							var d = e.data,
								p1 = (d.width-(d.offset*d.ratio)),
								p2 = d.pwidth+(d.offset*d.ratio);

							if( p1 > 9 && p2 > 9 ){
								// update width of cols
								d.el.style.width = p1+'%';
								d.pel.style.width = p2+'%';
								
								//d.col.style.left = e.data.offset+'px';
								
								d.holder.right.style.height = 
								d.holder.left.style.height = 
								d.holder.bottom.style.top = 
								d.pholder.right.style.height = 
								d.pholder.left.style.height = 
								d.pholder.bottom.style.top = d.el.offsetHeight+'px';
								
								d.holder.top.style.width = 
								d.holder.bottom.style.width = 
								d.holder.right.style.left = 
								d.holder.main.get(0).style.width = d.el.offsetWidth+'px';
								
								d.pholder.top.style.width = d.pel.offsetWidth+'px';
								d.pholder.bottom.style.width = d.pel.offsetWidth+'px';
								d.holder.main.get(0).style.left = (d.holder_left+e.data.offset)+'px';
								
								// update info 
								d.einfo.innerHTML = Math.round(p1)+'%';
								d.pinfo.innerHTML = Math.round(p2)+'%';
								
								kc.storage[d.emodel].args.width = kc.tools.nfloat(p1)+'%';
								kc.storage[d.pmodel].args.width = kc.tools.nfloat(p2)+'%';
								
							}
							
						};
					
					$(this).data({ curentWidth: kc.storage[holder.main.data('model')].args.width });
						
					kc.frame.$('html,body').css({cursor:'col-resize'}).addClass('noneuser kc-resizing-cols');
					kc.detect.disabled = true;
					pholder.right.style.display = 'none';
					
					if( width.indexOf('%') > -1 ){
						width = parseFloat( width );
					}else if( width.indexOf('/') > -1 ){
						width = width.split('/');
						width = (parseInt(width[0])/parseInt(width[1]))*100;
					}
					if( pwidth.indexOf('%') > -1 ){
						pwidth = parseFloat( pwidth );
					}else if( pwidth.indexOf('/') > -1 ){
						pwidth = pwidth.split('/');
						pwidth = (parseInt(pwidth[0])/parseInt(pwidth[1]))*100;	
					}
					
					kc.frame.$(kc.frame.doc)
						.on( 'mouseup', {
							
							el:el,
							pel: el.prev(),
							emodel: holder.main.data('model'),
							pmodel: pholder.main.data('model'),
							
						}, mouseUp )
						.on( 'mousemove', {
							
							el: el.get(0),
							pel: el.prev().get(0),
							col: $(e.target).closest('.mpb.mpb-left').get(0),
							holder: holder,
							pholder: pholder,
							
							einfo: holder.main.find('.col-info').get(0),
							pinfo: pholder.main.find('.col-info').get(0),
							emodel: holder.main.data('model'),
							pmodel: pholder.main.data('model'),
							
							holder_left: holder.main.get(0).offsetLeft,
							left: e.clientX,
							width: width,
							pwidth: pwidth,
							
							offset: 1,
							ratio: width/el.get(0).offsetWidth
							
						}, mouseMove );

						
				},
				
				col_exchange : function( e ){
						
					var r_col = parseInt( kc.detect.frame.$( e.target ).closest('.kc-boxholder').data('col-index') ),
						r_model = kc.detect.holder.columns[ r_col ].model,
						l_model = kc.detect.holder.columns[ r_col - 1 ].model,
						l_el = kc.detect.frame.$('[data-model="'+l_model+'"]'),
						r_el = kc.detect.frame.$('[data-model="'+r_model+'"]'),
						cwidth = $(this).closest('.handle-resize').data('curentWidth');;
					
					if( cwidth != kc.storage[r_model].args.width )
						return;
					
					l_el.stop().animate({ marginLeft: r_el.get(0).offsetWidth, marginRight: -r_el.get(0).offsetWidth });
					r_el.stop().animate({ marginLeft: -l_el.get(0).offsetWidth, marginRight: l_el.get(0).offsetWidth }, function(){
						l_el.before( r_el );
						l_el.css({marginLeft:'',marginRight:''});
						r_el.css({marginLeft:'',marginRight:''});
						kc.detect.untarget();
					});
					
					kc.detect.untarget();
										
				},
				
				marginer : function( e ){
					
					var model = kc.get.model( e.target ), 
						el = _$( kc.detect.holder.main.data('el') ),
						elv = _$ ('[data-model="'+model+'"]'),
						value = 0,
						dir = $(this).parent().data('dir'),
						mode = (this.className.indexOf('kc-marginer')>-1)?'margin':'padding';
					
					if (kc.storage[model] === undefined)
						return false;
					
					if (e.which === 3)
					{	// right click
						e.target = elv.get(0);
						kc.front.ui.right_click(e);
						return false;
					}
				
					$('html,body').stop();
					
					_$('.kc-boxholder,.kc-boxholder div').attr({'style':''});
					
					for( var i=0; i<kc.detect.holder.columns.length; i++ ){
						kc.detect.holder.columns[i].main.data({'el':'','model':''});
					}
					
					if (['kc_column', 'kc_column_inner'].indexOf(kc.storage[model].name)>-1)
					{
						el = elv;
						// if margin of column -> transform to margin of row
						if (mode == 'margin' && kc.detect.css_inspector.enable !== true)
						{
							var ob = kc.detect.closest(el.parent().get(0));
							if (ob !== null)
							{
								el = _$(ob[0]);
								model = ob[1];
							}
						}
					}
						
					var css = {
							'margin': { 
								top: parseInt( el.css('margin-top') ), 
								bottom: parseInt( el.css('margin-bottom') ), 
								left: parseInt( el.css('margin-left') ), 
								right: parseInt( el.css('margin-right') )  }, 
							'padding': { 
								top: parseInt( el.css('padding-top') ), 
								bottom: parseInt( el.css('padding-bottom') ), 
								left: parseInt( el.css('padding-left') ), 
								right: parseInt( el.css('padding-right') ) 
							}
						},
						
						mouseUp = function(e){
							
							kc.frame.$(kc.frame.doc).off('mousemove').off('mouseup');
							kc.frame.$('html,body').css({cursor:''}).removeClass('noneuser kc-marginer');
							kc.detect.disabled = false;
							
							$('#kc-marginer-info').remove();
							
							// if this is a click 
							if (e.data.top == e.clientY && e.data.left == e.clientX)
							{
								// in css inspector mode
								if (kc.detect.css_inspector.enable === true)
								{
									el = _$('[data-model="'+e.data.model+'"]').get(0);
									kc.detect.css_inspector.click ({
										target: e.data.el.get(0)}, 
										[_$('[data-model="'+e.data.model+'"]').get(0), e.data.model]
									);
								
									return false;
								
								}
								
								e.data.edit(e);
								return false;
							}
							
							var selector = kc.detect.holder.main.data('selector');
							if (selector === undefined)
								selector = '';
								 
							var get = kc.front.css_system.get({
									model: e.data.model, 
									selector: selector, 
									group: 'box',
									property: e.data.mode
								}),
								index = { top: 0, right: 1, bottom: 2, left: 3 };
								
							if( get.value === '' )
								get.value = ['inherit', 'inherit', 'inherit', 'inherit'];
							else  get.value =  get.value.split(' ');
								
							get.value[index[e.data.dir]] = e.data.el.css(e.data.mode+'-'+e.data.dir);
							
							get.value = get.value.join(' ');
							
							kc.front.css_system.set( $.extend({
								model: e.data.model,
								group: 'box',
								selector: selector,
								property: e.data.mode,
							}, get ));
							
							// remove css inline of object after css has been render
							e.data.el.css(e.data.mode+'-'+e.data.dir, '');
							
							if (kc.detect.css_inspector.active === true)
							{
								kc.detect.css_inspector.hover (
									{ target: e.data.el.get(0) }, 
									[_$('[data-model="'+model+'"]').get(0), model]
								);
								kc.detect.css_inspector.parent_btn();
							}
							
						},
						
						mouseMove = function( e ){
							
							e.preventDefault();
							var d = e.data;
							
							if( d.dir == 'top' || d.dir == 'bottom' )
								d.offset = e.clientY-d.top;
							else if( d.dir == 'left' )
								d.offset = e.clientX-d.left;
							else if( d.dir == 'right' )
								d.offset = -(e.clientX-d.left);
							
							if( d.mode == 'padding' && (d.offset+d.css[d.mode][d.dir]) < 0 )
								d.offset = -d.css[d.mode][d.dir];
							
							d.offset = (d.offset+d.css[d.mode][d.dir])+'px';
							
							d.el.css(d.mode+'-'+d.dir, d.offset);
							
							if (id('kc-marginer-info'))
								id('kc-marginer-info').innerHTML = d.offset;
							
						};
					
					kc.frame.$('html,body').css({cursor:'ns-resize'}).addClass('noneuser kc-marginer');
					
					if (dir == 'left' || dir == 'right')
						kc.frame.$('html,body').css({cursor:'ew-resize'});
					
					$('body').append('<div id="kc-marginer-info" \
										data-dir="'+dir+'" \
										style="left: '+e.clientX+'px; top: '+e.clientY+'px">\
										'+css[mode][dir]+'px\
									</div>');
						
					kc.detect.untarget();
					kc.detect.disabled = true;
										
					value = parseInt( css[mode][dir].toString().replace(/[^0-9\-]/g,'') );

					_$(kc.frame.doc)
						.off('mouseup')
						.on( 'mouseup', { 
							dir: dir, 
							el: el, 
							model: model, 
							mode: mode,
							top: e.clientY,
							left: e.clientX,
							edit: e.data.edit,
							get_model: e.data.get_model
						}, mouseUp )
						.off('mousemove')
						.on( 'mousemove', {
							
							el: el,
							value: value,
							dir: dir,
							css: css,
							
							mode: mode,
							
							model: model,
							top: e.clientY,
							left: e.clientX,
							
							offset: 1,
							
						}, mouseMove );

						
				},
				
				edit_section : function( e ){
					
					var model = kc.get.model( this ),
						section = kc.detect.get_active_section(model);
					
					if(section === null)
						return;
						
					kc.detect.on_edit( [section.get(0), section.data('model')], e );
					
				},
				
				double_section : function( e ){
					var model = kc.get.model( this ),
						section = kc.detect.get_active_section(model);
					kc.front.ui.element.double( section.data('model') );
				},

				insert_section : function( e ){
					var model = kc.get.model( this )
					kc.front.ui.element.add_section( model );
				},
				
				delete_section : function( e ){
					var model = kc.get.model( this ),
						section = kc.detect.get_active_section(model);
					kc.front.ui.element.delete( section.data('model') );
				},
				
				order_section : function( e ){
					
					var model = kc.get.model( this ),
						pos = kc.frame.$(this).data('pos'),
						section = kc.detect.get_active_section(model);
						
					kc.front.ui.element.order_section( section, pos );
					
					e.preventDefault();
					return false;
					
				},
				
				insert_column : function( e ){
					
					kc.front.ui.column.insert( kc.get.model(this) );
					
				},
				
				move_column : function( e ){
					
					var _$ = kc.frame.$,
						pos = _$(this).data('pos'),
						model = kc.get.model( e.target ),
						column = _$('[data-model="'+model+'"]');
					
					if( column.length > 0 ){
						if( pos == 'left' ){
							if( column.prev().length > 0 )
								column.prev().before( column );
						}else if( pos == 'right' ){
							if( column.next().length > 0 )
								column.next().after( column );
						}
					}
					
					kc.detect.untarget();
						
				},
				
				stretch_content : function( e ){
					
					kc.front.ui.row.stretch_content( e.data.get_model(e) );
					
				},
				
				save_to_section : function( e ){
					
					var model = e.data.get_model(e);
					
					kc.views.builder.sections( e, { save_row: model } );
					
				},
				
				get_row_order : function( e ){
					
					var row = kc.frame.$('[data-model="'+e.data.get_model(e)+'"]'), 
						rows = row.parent().find('>[data-model]');
						
					$(this).val( rows.index( row )+1 );
					
				},
				
				row_order : function( e ){



					var  model = e.data.get_model(e),
                        row = kc.frame.$('[data-model="'+model+'"]'),
                        rows = row.parent().find('>[data-model]'),
                        cindex = rows.index( row )+1,
                        index = $(this).parent().find('input').val();
						
					if( index === '' || index < 0 || index > rows.length ){
						
						$(this).prev().
							animate({marginLeft:-20}, 150).
							animate({marginLeft:15}, 150).
							animate({marginLeft:-10}, 150).
							animate({marginLeft:5}, 150).
							animate({marginLeft:0}, 150);
					
					}else if( index == 0 || index == 1 ){
						
						rows.first().before( row );
						kc.front.ui.scrollAssistive( row.get(0), true );
						$(this).parent().find('input').val('');
					
					}else{
						
						if( rows.index(row) < index-1 )
							rows.eq(index-1).after( row );
						else rows.eq(index-1).before( row );
						
						kc.front.ui.scrollAssistive( row.get(0), true );
						$(this).parent().find('input').val('');
					
					}

                    kc.front.stack.push({
                        model: model,
                        mode: 'row_order',
                        old_index: cindex,
                        new_index: index
                    });
					
					kc.detect.untarget();
					
					e.preventDefault();
					return false;
					
				},
				
				row_order_enter : function( e ){
					if( e.keyCode == 13 ){
						$(this).next().trigger('click');
						e.preventDefault();
						return false;
					}
				},
				
				col_ctrl_over : function(e){
					
					var model = kc.get.model(e.target);				
					e.data.viewer( kc.frame.$('[data-model="'+model+'"]').get(0) );
					
				},
				
				row_ctrl_over : function(e){
					
					var model = kc.get.model( e.target ),
						ob = kc.detect.closest(  kc.frame.$('[data-model="'+model+'"]').parent().get(0) );
					
					kc.detect.holder.main.attr({'style': ''}).find('div').attr({'style': ''});
					
					e.data.viewer( ob[0] );
					
				},
				
				row_ctrl_out : function(e){
					kc.frame.$('#kc-overlay-placeholder').attr({'style':''});
				},
				
				viewer : function( el ){
					
					if( el !== undefined && el !== null && typeof el.getBoundingClientRect == 'function' ){
						el = el.getBoundingClientRect();
						kc.frame.$('#kc-overlay-placeholder').css({
							width: el.width+'px', 
							height: el.height+'px', 
							top: (el.top+kc.detect.frame.window.scrollY)+'px', 
							left: el.left+'px' 
						});
					}
					
				},
				
				edit_row_section : function( e ){
					
					var model = kc.get.model(e.target);
					if( kc.storage[model] !== undefined && kc.storage[model].args.__section_link )
						window.open( $(this).data('link')+kc.storage[model].args.__section_link );
					
				},
				
				select_row_section : function( e ){
				
					var model = kc.get.model(e.target);
					if( kc.storage[model] !== undefined && kc.storage[model].args.__section_link )
						kc.views.builder.sections( e, { current: kc.storage[model].args.__section_link, model: model } );
				
				},
				
				remove_row_section : function( e ){
					
					if( confirm(kc.__.sure) ){
					
						var model = kc.get.model(e.target);
						delete kc.storage[model];
						_$('[data-model="'+model+'"]').remove();
					}
					
					kc.detect.untarget();
				
				}

			});
			
			kc.trigger({
				
				el: this.frame.$('#kc-footers'),
				
				events: {
					'[data-action="browse"]:click' : function( e ){ kc.front.ui.element.add( e.target ); },
					'li.quickadd:click' : function( e ){  kc.front.push( this.getAttribute('data-content') ); },
					'[data-action="custom-push"]:click' : 'custom_push',
					'[data-action="paste"]:click' : 'paste',
					'[data-action="sections"]:click' : 'sections',
					'[data-action="online-sections"]:click' : 'online_sections',
					
				},
				
				custom_push : function(e){
					
					var atts = { 
							title: kc.__.i36, 
							width: 750,
							float: true,
							class: 'push-custom-content',
							save_text: 'Push to builder'
						},
						pop = kc.tools.popup.render( e.target, atts );
						
					var copied = kc.backbone.stack.get('KC_RowClipboard');
					if( copied === undefined || copied == '' )
						copied = '';
					pop.find('.m-p-body').html( kc.__.i37+'<p></p><textarea style="width: 100%;height: 300px;">'+copied+'</textarea>');
					
					pop.data({
						callback : function( pop ){
							
							var content = pop.find('textarea').val();
							if( content !== '' ){
								if( content.trim().indexOf('[') !== 0 )
									content = '[kc_column_text]<p>'+content+'</p>[/kc_column_text]';
								kc.front.push( content );
							}
						}
					});
				},
				
				paste : function( e ){
				
					content = kc.backbone.stack.get('KC_RowClipboard');
				
					if( content === undefined || content == '' || content.trim().indexOf('[') !== 0 ){
						content = '[kc_column_text]<p>'+kc.__.i38+'</p>[/kc_column_text]';
					}
				
					if( content != '' )
						kc.front.push( content );
							
				},
				
				sections : function( e ){
					
					kc.views.builder.sections(e);
						
				},
				
				online_sections : function( e ){
					
					kc.views.builder.online_sections(e);
						
				}
				
			});
					
		},
		
		hover : function( e ){
			
			// Disabled inspector or not pure click event or css inspector is active
			if (kc.detect.disabled === true || 
				kc.detect.instantor === true || 
				kc.detect.trust( e ) === false || 
				kc.detect.css_inspector.active === true
			)return;
			
			var u = kc.detect;
			
			// Find closest kc object at target			
			u.ob = u.closest( e.target );
			
			if( u.ob === null ){
				u.untarget();
				return;
			}
			
			if( u.ob[1] == '-1' )
				u.ob = u.closest( u.ob[0].parentNode );
			
			if( u.ob === null ){
				u.untarget();
				return;
			}
			
			clearTimeout(kc.detect.delay);	
			
			// If detect kc object at hover target
			
			var st = kc.storage[u.ob[1]];
			
			if (st === undefined)
				return;
			
			/*
			*	Hove on CSS Inspector mode
			*/
			if (kc.detect.css_inspector.enable === true)
			{
				kc.detect.delay = setTimeout( function(e, ob){ 
					kc.detect.css_inspector.hover( e, ob );
				}, 50, e, u.ob );
				return;
			}
			
			// if hove on column, untarget element
						
			if (st.name == 'kc_column' || st.name == 'kc_column_inner')
			{
				kc.detect.holder.main.attr({'style':''});
				
				if (kc.detect.css_inspector.enable !== true)
				{
					kc.detect.holder.row_mb.css({left: (u.ob[0].parentNode.offsetWidth/2)+'px'});
				}
			}
			
			if (kc.storage[ u.ob[1] ] !== undefined && kc.detect.bone.indexOf( st.name ) === -1)
			{
				kc.detect.delay = setTimeout (
					function(ob){
						kc.detect.target (ob); 
					}, 
					50, 
					kc.detect.ob
				);
				
			}
			else if (kc.storage[ u.ob[1] ] !== undefined && (kc_maps_view.indexOf(st.name) > -1 || kc_maps_views.indexOf(st.name) > -1))
			{
				kc.detect.delay = setTimeout (
					function(ob){
						kc.detect.section (ob); 
					}, 
					50, 
					kc.detect.ob
				);
			}
			
			if( u.ob[1] == '-1' ){
				u.ob[3] = u.ob[0].parentNode;
			}else u.ob[3] = u.ob[0];
			
			if( st.name != 'kc_row' && st.name != 'kc_row_inner' ){
				this.columns( u.ob[3] );
				return;
			}
			
			if( u.ob[0].querySelectorAll('[data-model]')[0] !== undefined ){
				this.columns( u.ob[0].querySelectorAll('[data-model]')[0] );
			}
			
			if( st.name == 'kc_row' && st.args.__section_link !== undefined )
				this.section_link( u.ob, st.args.__section_title );
			
		},
		
		click : function( e ){
			
            var _$ = kc.detect.frame.$;

			if (kc.detect.disabled === true || kc.detect.trust (e) === false)
				return false;
			
			if (e.target === undefined)
			{
				return false;
			}
			else if (_$(e.target).hasClass('kc-add-elements-inner'))
			{
				kc.front.ui.element.add (e.target);
				return;
			}
					
			var ob = kc.detect.closest (e.target), 
				name = (kc.storage[ ob!==null?ob[1]:-1 ] !== undefined) ? kc.storage[ ob[1] ].name : '';

			if (kc.detect.disabled !== true && kc.detect.css_inspector.enable !== true && kc_instantor) {	
				
				// Start testing live tinyMCE
				if (name === 'kc_column_text') {
					var inst = kc.ui.instantor.onclick(e, ob);
					kc.ui.instantor.target(e);
					kc.detect.instantor = true;
					kc.detect.untarget();
					
				}else if (kc.id('kc-instantor') !== null) {
					
					var selection = kc.ui.instantor.selection();
					if (selection.toString() !== '')
						return;
						
					var el = $('#kc-instantor').data('el');
					if (el !== undefined) {
						el.setAttribute('data-live-editor', '');
						el.setAttribute('data-fix-position', '');
					}
					$('#kc-instantor').remove();
					kc.ui.instantor.save(el);
					kc.detect.instantor = false;
					
				}
					
				if (kc.detect.instantor === true)
					return;
			
			}
			
			if (e.target.tagName == 'A' || _$( e.target ).closest('a').length > 0)
			{
				var a;
				
				if(e.target.tagName == 'A')
					a = e.target;
				else a = _$(e.target).closest('a').get(0);
				
				if (location.hostname == a.hostname && a.hash.indexOf('#!') === 0) 
				{
					
					var target = _$(a.hash.replace('!', ''));
					
					if (target.length) 
					{
						_$('html,body').stop().animate({
							scrollTop: target.offset().top-80
						}, 500);
		
					}
				}

				e.preventDefault();
				return false;
				
			}
			else if ([ 'INPUT', 'SELECT', 'TEXTAREA' ].indexOf( e.target.tagName ) > -1)
			{
				return true;
			}
			
			if (kc.detect.locked !== false)
				kc.detect.locked = false;
			
			if ($(e.target).closest('.ui-accordion-header').length > 0 || $(e.target).closest('.ui-tabs-nav').length > 0)
			{
				//return false;
			}
			
			if (ob !== null)
			{
				
				if (ob[1] == '-1')
					var ob = kc.detect.closest (ob[0].parentNode);
				
				kc.detect.clicked = true;
					
				if (name !== '')
				{
					
					var holder;
					
					if (name != 'kc_row' && name != 'kc_row_inner')
						holder = this.holder;
					else if (name == 'kc_column' || name == 'kc_column_inner')
						holder = this.holder.columns[0];
					
					
					if (kc_maps_views.indexOf(name) > -1)
					{	
						section = kc.detect.get_active_section (ob[1]);
						if (section !== null)
							ob = [section.get(0), section.data('model')];
					}
					
					/*
					* Click to element on css inspector mode
					*/
					if (kc.detect.css_inspector.enable === true)
					{
						return kc.detect.css_inspector.click( e, ob );
					}
					/*
					*	Click to element on responsive mode
					*/
					else if (kc.detect.is_responsive() === true)
					{
						kc.front.ui.right_click(e);
					}
					/*
					*	Click to element, if the editing popup is opening -> do edit
					*/
					else if (document.querySelectorAll('.kc-live-editor-popup').length > 0)
					{	
						this.on_edit (ob, e);
					}
					
				}
				
				kc.detect.untarget();
								
			};
			
			return false;
			
		},
		
		dblclick : function( e ){
			
			if (kc.detect.disabled === true || kc.detect.css_inspector === true || kc.id('kc-instantor') !== null)
				return false;
				
			if (!kc.detect.trust(e))
				return false;
			
			var ob = kc.detect.closest (e.target);
			
			if (ob !== null)
			{
				this.on_edit (ob, e);
			
				e.preventDefault();
				e.stopPropagation();
				
				if (kc.frame.window.getSelection)
			        kc.frame.window.getSelection().removeAllRanges();
			    else if (kc.frame.document.selection)
			       kc.frame.document.selection.empty();
		    }
		     
			return false;
				
		},
		
		overlay : function( model ){
			
			var el = _$('[data-model="'+model+'"]').get(0);
						
			if( el !== undefined && el !== null && typeof el.getBoundingClientRect == 'function' ){
				
				el = el.getBoundingClientRect();
				
				_$('#kc-overlay-placeholder').css({
				
					width:	el.width + 'px', 
					height:	el.height + 'px', 
					top:	(el.top + kc.detect.frame.window.scrollY) + 'px', 
					left:	el.left + 'px' 
				
				});
				
			}
				
		},
		
		css_inspector : {
			
			enable : false,
			
			active : false,
			
			selector : '',
			
			hover : function(e, ob){
				
				var d = kc.detect,
					el = e.target,
					st = kc.storage[ob[1]],
					label = [],
					pel = el,
					selc, claz, i,
					wrp = $(el).closest('[data-model]');
					
				if (d.rect( [e.target, ob[1]], d.holder))
				{	
					i = 0;
					while (pel !== ob[0] && i < 3)
					{	
						selc = pel.tagName;
						if (pel.className !== '')
						{	
							claz = pel.className.trim().split(' ');
							
							if (claz[0] !== undefined && claz[0] !== '')
								selc += '.'+claz[0];
							
							if (claz[1] !== undefined && claz[1] !== '')
								selc += '.'+claz[1];
							
						}
						
						label.push (selc);
						
						i++;
						pel = pel.parentNode;
						
					};
					
					label.reverse();
					
					if (label.length > 0)
					{
						this.selector = label.join(' ').toLowerCase();
						label = st.name.replace('kc_', '')+' &#187; '+label.join(' &#187; ');
					}
					else
					{
						this.selector = '';
						label = st.name.replace('kc_', '');
					}
					
					d.holder.tooltip.querySelectorAll('span.label')[0].innerHTML = label;
					
					d.holder.main.data({ el: el, model: ob[1], selector: this.selector });
					
				}
			},
			
			click : function(e, ob){
				
				
				// close exist editing popup
				$('.kc-params-popup button.cancel').trigger('click');
				// target to new target
				this.hover (e, ob);
				// set status to active
				this.active = true;
				
				/*
				*	Add button to access parent
				*/
				this.parent_btn();
				/*
				*	setup maps for inspector css
				*/
				
				var sto = kc.storage[ob[1]],
					keep_params = $.extend( true, {}, kc.maps[ sto.name ].params ),
					css_maps = $.extend( true, [], kc.maps._styling.options ),
					css_maps_hover = $.extend( true, [], kc.maps._styling.options ),
					merge = kc.params.merge (sto.name), group = {'Styling': []},
					i,j,m,n;
					
				for( i in css_maps ){
					for( j in css_maps[i] ){
						for( m in css_maps[i][j] ){
							if( css_maps[i][j][m].property !== undefined ){
								css_maps[i][j][m].selector = this.selector;
								css_maps_hover[i][j][m].selector = this.selector+':hover';
							}
						}
					}
				}
				
				kc.maps[ sto.name ].params = {
					'General' : [{
						'name': '_css_inspector_'+kc.tools.esc_slug(this.selector),
						'type': 'css',
						'options': css_maps
					}],
					'Hover': [{
						'name': '_css_inspector_'+kc.tools.esc_slug(this.selector)+'_hover',
						'type': 'css',
						'options': css_maps_hover
					}]
				};
				
				
				// selector is element
				if (this.selector === '')
				{
					// we'll check this element has custom css
					
					for (var n in merge)
					{
						if (merge[n].type == 'css')
						{
							group['Styling'].push (merge[n]);
						}
					}
					
					if (group['Styling'].length > 0)
						kc.maps[ sto.name ].params = group;
					
				}
				
				
				var pop = kc.front.ui.element.edit (ob[1], e);
				
				pop.find('ul.kc-pop-tabs li').each(
					function(){
						if ($(this).hasClass('kc-tab-general-presets'))
							$(this).remove();
						else $(this).trigger('click');
					}
				).first().trigger('click');
				
				if( this.selector !== '' )
					pop.find('h3.m-p-header span[data-st="label"]').html(this.selector);
				
				kc.maps[ sto.name ].params = keep_params;
				
			},
			
			parent_btn : function(){
				
				if (_$(kc.detect.holder.main.data('el')).closest('[data-model]').length > -1)
				{
					var btn = _$('<i class="fa-object-group select-parent" title="'+kc.__.i68+'"></i>');
					_$(kc.detect.holder.tooltip).find ('span.label').append (btn);
					
					btn.on('click', function(){
						
						var el = kc.detect.holder.main.data('el'),
							pr = el.parentNode,
							model;
						
						if (pr.getAttribute('data-model') !== null)
							model = pr.getAttribute('data-model');
						else model = _$(pr).closest('[data-model]').data('model');
						
						if (model === undefined)
							return;
							
						kc.detect.css_inspector.click({target: pr}, [_$('[data-model="'+model+'"]').get(0), model]);
						
					});
				}
				
			}
			
		},
		
		trust : function( e ){
			
			if( e.originalEvent === undefined )
				return false;
			
			var el = e.target, i, 
				ignored = [
					'kc-boxholder',
					'wp-core-ui',
					'kc-params-popup',
					'sys-colorPicker',
					'kc-footers',
					'mce-container',
				];
				
			while( el !== null && el !== undefined ){
				for( i in el.classList ){
					if( ignored.indexOf( el.classList[i] ) > -1 )
						return false;
				}
				el = el.parentNode;
			}
			
			return true;	
			
		},
		
		closest : function( el, tag ){
			
			if( el === null || el === undefined || typeof( el.getAttribute ) != 'function' )
				return null;
			
			var model = el.getAttribute('data-model');
			
			if( model !== null ){
				
				if( tag === undefined || 
					( tag !== undefined && kc.storage[ model ] !== undefined && kc.storage[ model ].name == tag )
				)return [ el, el.getAttribute('data-model') ];
				
			}
			
			if( el.parentNode !== null )
				return kc.detect.closest( el.parentNode, tag );
				
			return null;
		},
				
		target : function( ob ){
			
			var u = kc.detect;
			
			if( this.holder === null )
				return;

			var name = (kc.storage[ ob[1] ] !== undefined) ? kc.storage[ ob[1] ].name : '';
				
			if (name === '')
				return;
			
			if (this.bone.indexOf( name ) === -1)
			{
					
				if (this.rect (ob, this.holder) === false)
					return;
				
				this.holder.main.removeClass('kc-viewSections-control');
				this.holder.section.attr({'style':''});	
				
				name = kc.storage[ ob[1] ].name.replace('kc_','').replace(/\_/g,' ');
				
				if( kc.storage[ ob[1] ] !== undefined )
					this.holder.tooltip.querySelectorAll('span.label')[0].innerHTML = name;
					
			}
			
		},
		
		untarget : function(){
			
			/*
			* do not untarget when css inpector is editing
			*/
			if (kc.detect.css_inspector.active === true)
				return;
			
			kc.detect.clicked = false;
			
			_$('.kc-boxholder, .kc-boxholder div, #kc-overlay-placeholder, #kc-section-link-placeholder').attr({style:''});
			
			try{
				this.holder.model = null;
				this.holder.el = null;
				this.holder.main.data({'el':'','model':''});
				for( var i=0; i<this.holder.columns.length; i++ ){
					this.holder.columns[i].main.data({'el':'','model':''});
				}
				
				_$('#kc-overlay-placeholder').attr({'style':''});
				
			}catch(ex){}
		},
		
		rect : function( ob, holder, padding ){
			
			if( holder === undefined )
				return false;
				
			if( ob[0] === null || typeof( ob[0].getBoundingClientRect ) != 'function'/* || ob[1] === holder.main.data('model') */)
				return false;
				
			var pr = 0;
			if( padding === undefined ){
				padding = 0;
				pr = 0;
			}
				
			holder.main.data({ el : ob[0], model : ob[1], s : ob[1], selector: '' });
			
			if( ob[0].tagName == 'kc' )
				$(ob[0]).addClass('fix-to-get-rect');

			var coor = ob[0].getBoundingClientRect(),
				top = coor.top+kc.detect.frame.window.scrollY,
				left = coor.left+kc.detect.frame.window.scrollX,
				height = Math.round( ( coor.height >= 27 ) ? coor.height : 27 ),
				width = coor.width;
			
			if( ob[0].tagName == 'kc' )
				$(ob[0]).removeClass('fix-to-get-rect');
			
			holder.width = width;
			holder.height = height;
			holder.el = ob[0];
			holder.model = ob[1];
				
			holder.main.css({ top: (top-padding)+'px', left: left+'px', width: width+'px' });
	
			holder.top.style.width = (width)+'px';
			
			holder.right.style.left = (width-1)+'px';
			holder.right.style.height = (height+padding)+'px';
			
			holder.bottom.style.top = (height+padding)+'px';
			holder.bottom.style.width = (width)+'px';
			
			holder.left.style.height = (height+padding+1)+'px';

			return true;
			
		},
		
		wrap_node : function( node, first_load ){

		    if( node !== null && node !== undefined ){
			    
			    var spc = node.firstChild, spcx;
			    
			    while( spc !== null && spc !== undefined ){
				    spcx = spc.nextSibling;
				 	if( spc.nodeType === 3 && ( spc.data === "\n" || spc.data.trim() === '' )  ){
					 	spc.parentNode.removeChild( spc );
				 	}
				 	spc = spcx;
				}
			    
		        node = node.firstChild;
		        var wrp,discover, nd, ind, model;
		        
		        while( node !== null && node !== undefined ){
			        
			        if( node.nodeType === 8 )
			        	ind = node;
			        else ind = false;
			        
		            if(
		            	node.nodeType === 8 && 
		            	node.data.indexOf('kc s') === 0 && 
		            	node.nextSibling !== null
		            ){

			            if( node.nextSibling.nextSibling !== null ){
				            
				            model = node.data.replace( /[^0-9]/g, '' );
				            
				            if( node.nextSibling.nextSibling.nodeType === 8 && 
				            	node.nextSibling.nextSibling.data.indexOf('kc e') === 0 ){
					            	
					            	if( node.nextSibling.nodeType !== 1 ){
						            	
						            	nd = $('<kc data-model="'+model+'"></kc>');
						            	$( node.nextSibling ).after( nd );
						            	nd.append( node.nextSibling );
						            	
					            	}else node.nextSibling.setAttribute('data-model', model );
					            
				            }else{
			            	
				            	discover = node.nextSibling;
				            	wrp = document.createElement('kc');
				            	
				            	node.parentNode.insertBefore( wrp, discover );
				            	wrp.setAttribute( 'data-model', model );
				            	
				            	while( discover !== null ){
					            	
				            		wrp.appendChild( discover );
				            		
				            		if( wrp.nextSibling !== null && 
				            			wrp.nextSibling.nodeType === 8  && 
										wrp.nextSibling.data.indexOf('kc e') === 0 
									)break;
				            		
				            		if( discover.nodeType === 1 )
				            			kc.detect.wrap_node( discover, first_load );
				            		
				            		discover = wrp.nextSibling;
				            		
				            	}
				            	node = wrp;
				            }
				            
				            if( first_load === undefined || first_load !== true )
				           		kc.front.css_system.push_to( model );
				            
				        }
		            
		            }else if( node.nodeType === 1 )
		            	kc.detect.wrap_node( node, first_load );
		            
		            node = node.nextSibling;
		            
			        if( ind !== false && ind != null && ind.parentNode !== null )
			        	ind.parentNode.removeChild( ind );
		        }
		    }
    
		},
		
		is_element : function( model ){
				
			if( kc.storage[ model ] === undefined )
				return false;
				
			var ignored = [ 'kc_row', 'kc_column', 'kc_column_inner' ]/*.concat( kc_maps_views )*/.concat( kc_maps_view );
			
			if( ignored.indexOf( kc.storage[ model ].name ) > -1 )
				return false;
				
			return true;
			
		},
		
		on_edit : function( ob, e ){
			
			if (ob === null || ob === undefined || ob[0] === undefined)
				return;
						
			kc.front.ui.element.edit (ob[1], e);
			
		},
		
		elements_tree : function( ob ){
				
			var kib = $('<div id="kc-elms-breadcrumn"><ul></ul></div>'),
				acts = {
					
					edit: ['fa-edit', kc.__.edit],
					
					copy: ['fa-copy', kc.__.copy+
							'<ul class="sub">\
								<li data-act="copy-style">\
									<i class="fa-paint-brush"></i> \
									'+kc.__.i35+'\
								</li>\
							</ul>'],
					'copy-style': ['fa-paint-brush', kc.__.i35],
					double: ['fa-clone', kc.__.double],
					
					paste: ['fa-paste', kc.__.paste+
							'<ul class="sub">\
								<li data-act="paste-style">\
									<i class="fa-paint-brush"></i> \
										'+kc.__.i65+'\
									</li>\
								</ul>'],
					layout: ['fa-paper-plane', kc.__.i21],
					
					insert: ['fa-sticky-note', kc.__.insert+
							' column\
							<span data-act="insert" data-dir="before">\
								'+kc.__.before+'\
							</span>\
							<span data-act="insert" data-dir="after">\
								'+kc.__.after+'\
							</span>'],
							
					addin: ['fa-list-alt', kc.__.add+
							' element\
							<span data-act="add" data-dir="before">\
								'+kc.__.before+'\
							</span>\
							<span data-act="add" data-dir="after">\
								'+kc.__.after+'\
							</span>'],
							
					addto: ['fa-list-alt', kc.__.add+
							' element\
							<span data-act="add" data-dir="top">\
								'+kc.__.top+'\
							</span>\
							<span data-act="add" data-dir="bottom">\
								'+kc.__.bottom+'\
							</span>'],
							
					section: ['fa-cubes', kc.__.insert+' section'],
					
					'insert-section': ['fa-cubes', kc.__.insert],
					
					delete: ['fa-trash', kc.__.delete],
					
					'clear-style': ['fa-eraser', kc.__.i70],
					
					'new-row': ['fa-plus-square', kc.__.insert+
								' row<span data-act="insert-row" data-dir="before">\
									'+kc.__.before+'\
								</span>\
								<span data-act="insert-row" data-dir="after">\
									'+kc.__.after+'\
								</span>'], 
								
					'move-section': ['fa-reorder', kc.__.order+
								'<span data-act="move-section" data-dir="up">\
									<i class="fa-angle-up"></i>\
								</span>\
								<span data-act="move-section" data-dir="down">\
									<i class="fa-angle-down"></i>\
								</span>'], 
							
					'move-row': ['fa-reorder', kc.__.order+
								'<span data-act="move-row" data-dir="up">\
									<i class="fa-angle-up"></i>\
								</span>\
								<span data-act="move-row" data-dir="down">\
									<i class="fa-angle-down"></i>\
								</span>'], 
						
					'move-column': ['fa-exchange-alt', kc.__.move+
								' columns<span data-act="move-column" data-dir="left">\
									<i class="fa-angle-left"></i>\
								</span>\
								<span data-act="move-column" data-dir="right">\
									<i class="fa-angle-right"></i>\
								</span>'],
						
				}, holder, li = '', set, n;
			
			/*
			*	Get all of parent elements
			*/
			if (ob[1] == '-1')
				ob = this.closest( ob[0].parentNode );
				
			while (ob !== null)
			{
				
				ob[2] = kc.storage[ob[1]].name;
				ob[3] = ob[2].replace(/kc\_/g,'').replace(/\_/g,' ');	
				
				if (kc.detect.is_responsive() === false)
				{	
					/*
					*	return false if css inspector mode enable
					*/
					if (kc.detect.css_inspector.enable === true )
						return false;	
					/*
					*	Set actions for each element
					*/
							  
					if( ['kc_row', 'kc_row_inner'].indexOf( ob[2] ) > -1 ){
						
						holder = 'row';
						set = ['edit', 'layout', 'new-row', 'copy', 'paste', 'double', 'move-row', 'clear-style', 'delete'];
						
					}else if( ['kc_column', 'kc_column_inner'].indexOf( ob[2] ) > -1 ){
						
						holder = 'column';
						set = ['edit', 'insert', 'move-column', 'addto', 'double', 'copy-style', 'paste', 'clear-style', 'delete'];
						
					}else if( kc_maps_views.indexOf( ob[2] ) > -1 ){
						
						holder = 'sections';
						set = ['edit', 'section', 'copy', 'double', 'paste', 'clear-style', 'delete'];
						
					}else if( kc_maps_view.indexOf( ob[2] ) > -1 ){
						
						acts['insert-section'][1] += ' tab';
						holder = 'section';
						set = ['edit', 'insert-section', 'addto', 'double', 'copy-style', 'paste', 'move-section', 'clear-style', 'delete'];
						
					}else{
						holder = 'element';
						set = ['edit', 'copy', 'double', 'paste', 'addin', 'clear-style', 'delete'];
					}
						
					li = '<li class="item" data-holder="'+holder+'" data-e-model="'+ob[1]+'" data-e-name="'+ob[2]+'">';
					li += '<span class="pointer" data-act="edit">\
								<i class="fa-angle-right" aria-hidden="true"></i> '
								+ob[3]+'</span><ul>';
					
					for( n in set )
					{
						li += '<li data-act="'+set[n]+'" data-name="'+ob[2]+'">';
						li += '<i class="'+acts[set[n]][0]+'" aria-hidden="true"></i> ';
						li += acts[set[n]][1]+'</li>';
					}

                    if( typeof kc.maps[ ob[2] ].preview_menu !== 'undefined' ){

                        var preview_menu = kc.maps[ ob[2] ].preview_menu,
                            elm = _$('[data-model="'+ ob[1] +'"]'),
                            icon_cls = kid_cls = '';

                        if( Object.keys(preview_menu).length > 0){

                            li += '<li data-act="preview_menu" data-name="'+ob[2]+'">';
                            li += '<i class="fa-eye" aria-hidden="true"></i> ';
                            li += ' Preview';

                            li += '<ul class="sub extra">';

                            for( pm in preview_menu )
                            {
                                icon_cls = 'fa-eye-slash';
                                kid_cls = '';

                                if( elm.hasClass( preview_menu[ pm ] ) ){
                                    icon_cls = 'fa-eye';
                                    kid_cls = 'kc-active';
                                }


                                li += '<li class="' + kid_cls + '" data-act="preview" data-name="'+ob[2]+'" data-class="' + preview_menu[ pm ] + '">';
                                li += '<i class="' +icon_cls+'" aria-hidden="true"></i> ';
                                li += pm  + '</li>';
                            }
                            li += '</ul></li>';
                        }


                    }
					li += '</ul></li>';
							
					kib.find('>ul').append(li);
					
				}
				else {
					
					if (['kc_column', 'kc_column_inner'].indexOf (ob[2]) === -1 && li === ''){
						li = '<li class="item for-responsive active" data-holder="column" data-e-model="'+ob[1]+'" data-e-name="'+ob[2]+'"> \
								<span class="pointer"><i class="fa-angle-right"></i> '+ob[3]+'</span> \
								<ul> \
									<li data-act="edit" data-name="'+ob[2]+'"> \
										<i class="fa-pencil"></i> Edit \
									</li> \
									<li data-act="clear-style" data-name="'+ob[2]+'"> \
										<i class="fa-eraser"></i> Clear screen style \
									</li> \
								</ul> \
							</li>';
					}
					else if (['kc_column', 'kc_column_inner'].indexOf (ob[2]) > -1)
					{
						
						li += '<li class="item for-responsive" data-holder="column" data-e-model="'+ob[1]+'" data-e-name="'+ob[2]+'"> \
								<span class="pointer" data-act="edit" data-name="'+ob[2]+'">\
									<i class="fa-angle-right"></i> '+ob[3]+
								'</span> \
								<ul> \
									<li data-act="edit" data-name="'+ob[2]+'"> \
										<i class="fa-pencil"></i> Edit \
									</li> \
									<li data-act="responsive" data-width="100%" data-offset="0%"> \
										<i class="fa-star"></i> 100% offset 0% \
									</li> \
									<li data-act="responsive" data-width="90%" data-offset="5%"> \
										<i class="fa-leaf"></i> 90% offset 5% \
									</li> \
									<li data-act="responsive" data-width="80%" data-offset="10%"> \
										<i class="fa-paper-plane"></i> 80% offset 10% \
									</li> \
									<li data-act="responsive" data-width="50%" data-offset="0%"> \
										<i class="fa-star-half-empty"></i> 50% offset 0% \
									</li> \
									<li> \
										<i class="fa-paw"></i> Custom:<br /> \
										<input data-act="custom-width" placeholder="Width" /> \
										<input data-act="custom-offset" placeholder="Offset" /> \
									</li> \
									<li data-act="clear-style" data-name="'+ob[2]+'"> \
										<i class="fa-eraser"></i> Clear screen style \
									</li> \
								</ul> \
							</li>';
						
						kib.find('>ul').append(li);
						li = ' ';
						
						var get_margin = kc.front.css_system.get({
								model: ob[1], 
								selector: '', 
								group: 'box',
								property: 'margin'
							}),
							get_width = kc.front.css_system.get({
								model: ob[1], 
								selector: '', 
								group: 'box',
								property: 'width'
							});
							
						if( get_margin.value !== '' )
							get_margin.value = get_margin.value.split(' ')[3];
						
						if( get_width.value !== '' ){
							kib.find('>ul li[data-width="'+get_width.value+'"][data-offset="'+get_margin.value+'"]').addClass('active');
							kib.find('>ul li input[data-act="custom-width"]').val(get_width.value);
							kib.find('>ul li input[data-act="custom-offset"]').val(get_margin.value);
						}
							
						
						kib.find('>ul li[data-act="responsive"]').on('click', function(e){
							
							var model = $(this).closest('li.item').data('e-model'),
								width = this.getAttribute('data-width'),
								offset = this.getAttribute('data-offset'),
								wrp = $(this).closest('li.for-responsive');
								
							do_responsive( width, offset, model );
							
							wrp.find('input[data-act="custom-width"]').val(width);
							wrp.find('input[data-act="custom-offset"]').val(offset);
							
							wrp.find('li.active').removeClass('active');
							$(this).addClass('active');
							
							kc.detect.overlay( model );
							
						});
						
						kib.find('>ul>li input').on('keyup', function(e){
							
							if( this.getAttribute('data-act') == 'custom-width' && this.value.length === 1 ){
								this.value += '0';
								if( !isNaN(this.value) )
									this.value += '%';
							}
							
							var p = this.parentNode;
								width = $(p).find('input[data-act="custom-width"]').val(),
								offset = $(p).find('input[data-act="custom-offset"]').val();
							
							$(this).closest('li.for-responsive').find('li.active').removeClass('active');
							
							if( !isNaN( width ) )
								width += '%';
								
							if( !isNaN( offset ) )
								offset += '%';
								
							var model = $(this).closest('li.item').data('e-model');
							
							do_responsive( width, offset, model );
							
							kc.detect.overlay( model );
							
						});
						
						var do_responsive = function( width, offset, model ){
							
							var get = kc.front.css_system.get({
								model: model, 
								selector: '', 
								group: 'box',
								property: 'margin'
							});
						
							if( get.value === '' )
								get.value = ['inherit', 'inherit', 'inherit', 'inherit'];
							else  get.value =  get.value.split(' ');
								
							get.value[3] = offset;
							
							get.value = get.value.join(' ');
							
							kc.front.css_system.set( $.extend({ 
								model: model, 
								property: 'margin',
								group: 'box',
								selector: '',
							}, get ));
							
							get.value = width;
							
							kc.front.css_system.set( $.extend({ 
								model: model, 
								property: 'width',
								group: 'box',
								selector: '',
							}, get ));
						}
						
					}
					
					if (ob[2] == 'kc_row') {
						kib.find('>ul').append('<li class="item for-responsive" data-holder="column" data-e-model="'+ob[1]+'" data-e-name="'+ob[2]+'"> \
							<span class="pointer" data-act="edit" data-name="'+ob[2]+'">\
								<i class="fa-angle-right"></i> '+ob[3]+
							'</span> \
							<ul> \
								<li data-act="edit" data-name="'+ob[2]+'"> \
									<i class="fa-pencil"></i> Edit \
								</li> \
								<li data-act="clear-style" data-name="'+ob[2]+'"> \
									<i class="fa-eraser"></i> Clear screen style \
								</li> \
							</ul> \
						</li>');
					}
				
				}
				/*
				*	return parent for loop
				*/
				ob = this.closest( ob[0].parentNode );
				
			}
			
			kib.find('>ul>li.item').first().addClass('active');
			/*
			*	Add events
			*/
			kib.on('mouseover',function(e){
						
					if( $(e.target).closest('li.item').length > 0 ){
						var model = $(e.target).closest('li.item').data('e-model');
						kc.detect.overlay( model );
					}
					
				})
				.on('mouseout',function(e){
					_$('#kc-overlay-placeholder').attr({'style':''});
				})
				.on('click', function(e){
					
					var act = e.target.getAttribute('data-act'),
						dir = e.target.getAttribute('data-dir');
					
					if( e.target.tagName == 'I' ){
						act = e.target.parentNode.getAttribute('data-act');
						dir = e.target.parentNode.getAttribute('data-dir');
					}
						
					if( act !== null ){
						
						var el = $(e.target).closest('li.item'),
							model = el.data('e-model'),
							elm = _$('[data-model="'+model+'"]'),
							holder = el.data('holder');
						
						switch( act ){
							
							case 'preview':
                                var act_cls = e.target.getAttribute('data-class'),
                                    other_cls = '';

                                if( act_cls === '' || act_cls === null )
                                    return;

                                _$( elm ).toggleClass( act_cls );

                                $(e.target).find('i').toggleClass( 'fa-eye-slash fa-eye' );
                                $(e.target).toggleClass( 'kc-active' );

                                //get other preview and disable
                                _$( e.target ).closest('.extra').find('li').each( function (){
                                    other_cls = this.getAttribute('data-class');
                                    if( act_cls !== other_cls ){
                                        _$( elm ).removeClass( other_cls );
                                        _$( this ).removeClass( 'kc-active' ).find('i').removeClass( 'fa-eye' ).addClass('fa-eye-slash');
                                    }

                                });

                                //kc.do_action('kc-exit-right-dialog');

							break;

							case 'layout':
								kc.front.ui.column.layout( el );
								kc.do_action('kc-exit-right-dialog');
							break;

							case 'add': 
							
								var pop = kc.front.ui.element.add( elm.get(0) );
							
								if( dir === undefined || dir === '' || dir === null )
									dir = 'bottom';
									
								pop.data({ 'pos': dir });
								kc.do_action('kc-exit-right-dialog');
							
							break;
							
							case 'insert': 
								
								kc.front.ui.column.insert( model, dir );
								kc.do_action('kc-exit-right-dialog');
								
							break;
							
							case 'edit': 
								
								kc.do_action('kc-exit-right-dialog');
								
								kc.front.ui.element.edit( model, true );
							
								kib.find('>ul>li.item.active').removeClass('active');
								el.addClass('active');
									
							break;
							
							case 'section': 
								kc.front.ui.element.add_section( model );
								kc.do_action('kc-exit-right-dialog');
							break;
							
							case 'insert-section':
								var op = kc.detect.closest( elm.get(0).parentNode );
								kc.front.ui.element.add_section( op[1] );
								kc.do_action('kc-exit-right-dialog');
							break;
							
							case 'copy': 
								kc.front.ui.element.copy( model );
								kib.find('>ul>li.item.active').removeClass('active');
								kc.do_action('kc-exit-right-dialog');
							break;
							
							case 'paste': 
								
								var copied = kc.backbone.stack.get('KC_RowClipboard'),
									bone = kc_maps_view.concat(['kc_column', 'kc_column_inner']);
								
								if( copied === undefined || copied == '' ){
									kc.msg(kc.__.i63, 'error', 'et-caution', 3000);
								}else if( copied.trim().indexOf('[kc_row ') === 0 ){
									var row = kc.detect.closest( elm.get(0), 'kc_row' );
									if( row !== null )
										kc.front.push( copied, row[1], 'after' );
									else kc.msg(kc.__.i64, 'error', 'et-caution', 3000);
									
								}else{
									if( bone.indexOf( kc.storage[model].name ) > -1 )
										kc.front.push( copied, model );
									else{ 
										if( kc.storage[model].name == 'kc_row' ){
											
											model = _$('[data-model="'+model+'"] [data-model]').first().data('model');
											kc.front.push( copied, model );
											
										}else kc.front.push( copied, model, 'after' );
									}
								}
								
								kc.do_action('kc-exit-right-dialog');
								
							break;
							
							case 'double':
								kc.front.ui.element.double( model );
								kib.find('>ul>li.item.active').removeClass('active');
								kc.do_action('kc-exit-right-dialog');
							break;
							
							case 'clear-style':
								
								var redo = {
										fid: model,
										mode: 'push',
										pos: 'replace',
										old_full: kc.front.build_shortcode (model)
									};
								
								kc.front.css_system.clear (model);
								
								redo.full = kc.front.build_shortcode (model);
								kc.front.stack.push (redo);
								
								kc.do_action('kc-exit-right-dialog');
								
							break;
							
							case 'delete': 
								if( kc.front.ui.element.delete( model ) )
									kc.do_action('kc-exit-right-dialog');
							break;
							
							case 'insert-row': 
							
								var raw = '[kc_row use_container="yes"][kc_column width="100%"][/kc_column][/kc_row]';
								kc.front.push( raw, model, dir );
								
								kc.do_action('kc-exit-right-dialog');
								
							break;
							
							case 'move-section':
									
								kc.front.ui.element.order_section( elm, dir );
								
							break;
							
							case 'move-row':
									
								if( dir == 'up' && elm.prev().data('model') !== undefined )
									elm.prev().before(elm);
								else if( dir == 'down' && elm.next().data('model') !== undefined )
									elm.next().after(elm);
								
								_$('#kc-overlay-placeholder').attr({'style':''});
								
								_$('html, body').animate({ scrollTop: elm.get(0).offsetTop - 150 });	
								
								kc.detect.untarget();
								
							break;
							
							case 'move-column':
									
								if( dir == 'left' && elm.prev().data('model') !== undefined )
									elm.prev().before(elm);
								else if( dir == 'right' && elm.next().data('model') !== undefined )
									elm.next().after(elm);
								
								_$('#kc-overlay-placeholder').attr({'style':''});
								
								kc.detect.untarget();
								
							break;
							
							case 'copy-style':
								
								if( kc.cfg.copied_style === undefined )
									kc.cfg.copied_style = {};
								
								var name = kc.storage[model].name,
									atts = kc.storage[model].args, 
									params = kc.params.merge( name ),
									is_css = [], values = {};
								
								if( atts['_id'] === undefined ){
									console.warn('KingComposer: Missing id of the element when trying to render css');
									return '';
								}
									
								for( n in params ){
									if( params[n].type == 'css' )
										is_css.push( params[n].name );
								}
								
								for( n in atts ){
									
									if( is_css.indexOf( n ) > -1 || n.indexOf( '_css_inspector' ) === 0 )
										values[n] = atts[n];
								
								}
								
								kc.cfg.copied_style[ name ] = values;
								// update to storage
								kc.backbone.stack.set( 'KC_Configs', kc.cfg );
								
								$('body').append('<div id="kc-small-notice"><i class="fa-check"></i> '+kc.__.i67+'!</div>');
								$('#kc-small-notice').
									animate({opacity : 1}).
									delay(1000).
									animate({opacity : 0}, function(){ $(this).remove(); });
								
								kc.do_action('kc-exit-right-dialog');
								
							break;
							
							case 'paste-style':
							
								if( kc.cfg.copied_style === undefined )
									return;
									
								var name = kc.storage[model].name,
									atts = kc.storage[model].args;
								
								if( kc.cfg.copied_style[name] === undefined )
									return;
									
								for( n in kc.cfg.copied_style[name] )
								{	
									kc.front.stack.push({
										model: model,
										mode: 'css',
										name: n,
										old_data: kc.storage[model].args[n],
										new_data: kc.cfg.copied_style[name][n]
									});
									
									kc.storage[model].args[n] = kc.cfg.copied_style[name][n];
								}
								
								kc.front.css_system.push_to( model );
								
								kc.do_action('kc-exit-right-dialog');
								
							break;
						}
					}
					
				});
				
			return kib;
			
		},
		
		columns : function( el ){
				
			this.ob = kc.detect.closest (el);
			var i = 0, el;
			
			while (this.ob !== null && kc.storage[ this.ob[1] ] !== undefined)
			{
				
				if(kc.storage[ this.ob[1] ].name == 'kc_column' ||  kc.storage[ this.ob[1] ].name == 'kc_column_inner')
				{
					
					el = this.ob[0].parentNode.firstChild;
					i = 0;
					while (el !== null && el !== undefined)
					{
						if(el.getAttribute( 'data-model' ) == this.ob[1])
							this.column( el, i++, true );
						else this.column( el, i++, false );
						el = el.nextElementSibling;
					}
					while(i < 10)
					{
						this.holder.columns[i++].main.data({'el':'', 'model':''}).attr({style:''}).find('div').attr({style:''});
					}
					break;
				}
				
				this.ob = kc.detect.closest (this.ob[0].parentNode);
			}
			
		},
		
		column : function( el, index, hit ){
			
			
			if( hit === true )
				this.holder.columns[ index ].main.addClass('mpb-column-focus');
			else this.holder.columns[ index ].main.removeClass('mpb-column-focus');
			
			if( this.rect( [ el, el.getAttribute( 'data-model' ) ], this.holder.columns[ index ], 0 ) !== false ){
				// if target column success
				var st = kc.storage[el.getAttribute( 'data-model' )], _w;
				if( st !== undefined && st.args !== undefined && st.args.width !== undefined ){
					_w = st.args.width;
					if( _w.indexOf('%') > -1 ){
						_w = Math.round( parseFloat( _w ) )+'%';
					}
				}else _w = '100%';
				
				if( st !== undefined && st.name == 'kc_column_inner' ){
					this.holder.columns[ index ].main.addClass('mpb-column-inner');
					this.holder.columns[ index ].main.find('li.row-control>label').html('ROW INNER');
				}else{
					this.holder.columns[ index ].main.removeClass('mpb-column-inner');
					this.holder.columns[ index ].main.find('li.row-control>label').html('ROW');
				}
				
				this.holder.columns[ index ].main.find('.col-info').html( _w );
				
			}
			
		},
		
		section : function( ob ){
			
			if(ob === null)
				return false;
				
			if( kc_maps_view.indexOf( kc.storage[ ob[1] ].name ) > -1 )
				ob = kc.detect.closest( ob[0].parentNode );
				
			if( kc.storage[ ob[1] ] === undefined )
				return false;
			
			// target sections when found a section
			if( this.rect( ob, this.holder ) !== false ){
				// if target sections success
				this.holder.main.addClass('kc-viewSections-control');
				$(this.holder.tooltip).find('.label').html( 
					kc.storage[ ob[1] ].name.replace('kc_','' ) 
				);
				
			}
			
			return true;
			
		},
		
		section_link : function( ob, title ){
			
			this.untarget();
			
			if( ob[0].tagName == 'kc' )
				$(ob[0]).addClass('fix-to-get-rect');
			
			this.holder.section.data({model: ob[1]});
				
			var coor = ob[0].getBoundingClientRect(),
				top = coor.top+kc.detect.frame.window.scrollY,
				left = coor.left+kc.detect.frame.window.scrollX,
				height = Math.round( ( coor.height >= 27 ) ? coor.height : 27 ),
				width = coor.width;
			
			this.holder.section.attr({ 'data-label': title });
				
			this.holder.section.css({
				top: top+'px',
				left: left+'px',
				height: height+'px',
				width: width+'px',
			});
			
		},
		
		get_active_section : function( model ){
					
			var section = null;
			kc.frame.$('[data-model="'+model+'"] [data-model]').first().parent().find('>[data-model]').each(function(){
				if( kc.frame.$( this ).hasClass('kc-section-active') )
					section = kc.frame.$( this );
			});
				
			return section;
			
		},
		
		is_responsive : function(){
			return (document.querySelectorAll('body')[0].getAttribute('data-screen-size') != '100%') ? true : false;
		}

	};
	
} )( jQuery );
