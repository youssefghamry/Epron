/*
 * KingComposer Project
 *
 * (c) Copyright king-theme.com
 *
 * kc.front.js
 *
*/

( function($){
	
	//Make sure the core is ready
	
	if( typeof( kc ) == 'undefined' ){
		console.error('Could not load KingComposer core library');
		return;
	}
	
	/*
	*
	*	REGISTER FRONTEND-EDITOR OBJECT
	*	
	*/
	
	kc.front = {
		
		init : function( frame_doc ){
			
			/*
			*	Load data and run UI(s) init
			*/
			
			kc.widgets = $( kc.template('wp-widgets') );

			kc.model = kc.storage.length;
			kc.detect.init( frame_doc );
			kc.front.ui.init( frame_doc );
			
			window._$ = kc.frame.$;
			
			// Top nav actions
			
			kc.trigger({
				
				el: $('#kc-top-nav'),
				
				events: {
					'.kc-bar-devices:click': 'screen',
					'#kc-enable-inspect:click': 'switch',
					'#kc-bar-tour-view:click': 'tour',
					'#kc-front-exit:click': 'exit',
					'#kc-front-save:click': 'save',
					'#kc-css-inspector:click': 'css_inspector',
					'#kc-bar-undo:click': 'undo',
					'#kc-bar-redo:click': 'redo',
					'#kc-content-settings:click' : 'settings'
				},
				
				screen: function(e){
					
					var screen = $(this).data('screen');
						
					if( screen == 'custom' ){
						screen = prompt( 'Enter custom screen size (unit px)', $(this).find('i').html() );
						if( screen === null )
							return;
					}
					
					if( screen.toString().indexOf('%') > -1 && screen !== '100%' )
						return;
					
					e.data.el.find('.kc-bar-devices').removeClass('active');
					$(this).addClass('active');
					
					$('#kc-live-frame-wrp').width( screen );
					$('body').attr({'data-screen': ((screen=='100%')?'desktop':'responsive')})
							.attr({'data-screen-size': screen});
					
					if( !isNaN( screen ) ){
					    //getting all screensize
                        var sizes = [], ascreen = screen;
                        $('.kc-css-screens-nav li').each( function(index){
                            if( $(this).data("screen") !== 'any')
                                sizes.push( $(this).data("screen"));
                        });

                        for( var i=1; i< sizes.length; i++ )
                            if( sizes[i] < screen ){
                                ascreen = sizes[i-1];
                                break;
                            }

						$('.kc-css-screens-nav li[data-screen="'+ascreen+'"]').trigger('click');
						screen += 'px';
					}else $('.kc-css-screens-nav li[data-screen="any"]').trigger('click');
					
					if (kc.detect.css_inspector.enable === false)	 
						kc.detect.untarget();
						
					$('#kc-curent-screen-view>i').html( screen );
					
					$('.kc-params-popup.kc-live-editor-popup button.cancel').trigger('click');
	
				},
				
				switch : function(e){
					
					if( $(this).find('.toggle').hasClass('disable') ){
						$(this).find('.toggle').removeClass('disable');
						$('body').removeClass('kc-disable-inspect');
						kc.detect.frame.$('body').removeClass('kc-disable-inspect');
						kc.detect.disabled = false;
					}else{
						$(this).find('.toggle').addClass('disable');
						$('body').addClass('kc-disable-inspect');
						kc.detect.frame.$('body').addClass('kc-disable-inspect');
						kc.detect.disabled = true;
						kc.detect.untarget();
						$('#kc-instantor').remove();
						_$('.kc_text_block[data-live-editor="open"]').each(function(){
							kc.ui.instantor.save(this);
							this.removeAttribute('contenteditable');
							this.removeAttribute('data-live-editor');
						});
						kc.detect.instantor = false;
					}
					$('.kc-params-popup .button.cancel').trigger('click');
				},
				
				exit : function(e){
					window.location.href = $('#kc-live-frame').attr('src').replace('&kc_action=live-editor','').replace('?kc_action=live-editor','');
				},
				
				save : function(){
					
					$('.kc-params-popup button.cancel').trigger('click');
					
					kc.msg( kc.__.loading, 'loading' );
					
					var content = '', id = kc_post_ID, _$ = kc.frame.$, dis_row, model,
                        meta = kc.tools.reIndexForm($("input[name^='kc_post_meta']").serializeArray(), []);
					
					_$('.kc_text_block[data-live-editor="open"]').each(function(){
						kc.ui.instantor.save(this);
					});
					
					dis_row = _$('#kc-footers').parent().find('[data-model]').first();
					
					while( dis_row.get(0) ){
						
						model = dis_row.data('model');
						if( model !== null && kc.storage[ model ] !== undefined ){
							content += kc.front.build_shortcode( model );	
						}
						
						dis_row = dis_row.next();
						
					}

					jQuery.post(
					
						kc_ajax_url,
					
						{
							'action': 'kc_instant_save',
							'security': kc_ajax_nonce,
							'task': 'frontend',
							'id': parseInt( id ),
							'content': content,
							'title': '',
							'classes': $('#kc-page-body-classes').val(),
							'css': $('#kc-page-css-code').val(),
							'max_width': $('#kc-page-max-width').val(),
							'thumbnail': $('#kc-page-thumbnail').val(),
							'live_editor': 'yes',
                            'meta': meta.kc_post_meta
						},
					
						function (result) {
							
							if( result == '-3' ){
								kc.front.ui.ask_to_buy();
								return;
							}
									
							if( result == '-1' )
								kc.msg( 'Error: secure session is invalid. Reload and try again', 'error', 'sl-close' );
							else if( result == '-2' )
								kc.msg( 'Error: Post not exist', 'error', 'sl-close' );
							else if( result == '-3' )
								kc.msg( 'Error: You do not have permission to edit this post', 'error', 'sl-close' );
							else kc.msg( 'Successful', 'success', 'sl-check' );
							
							kc.confirm( false );
					
						}
					);
			
				},
				
				tour : function(){
					kc.front.ui.tour();
				},
				
				css_inspector : function(){
					
					$('.kc-params-popup button.cancel').trigger('click');
					
					if( $(this).hasClass('active') ){
						$(this).removeClass('active');
						$('body').removeClass('kc-active-css-inspector');
						_$('body').removeClass('kc-active-css-inspector');
						kc.detect.css_inspector.enable = false;
					}else{
						$(this).addClass('active');
						$('body').addClass('kc-active-css-inspector');
						_$('body').addClass('kc-active-css-inspector');
						kc.detect.css_inspector.enable = true;
						kc.detect.untarget();
						$('#kc-instantor').remove();
						_$('.kc_text_block[data-live-editor="open"]').each(function(){
							kc.ui.instantor.save(this);
							this.removeAttribute('contenteditable');
							this.removeAttribute('data-live-editor');
						});
						kc.detect.instantor = false;
					}
					
				},
				
				undo : function(){
					kc.front.stack.undo();
				},
				
				redo : function(){
					kc.front.stack.redo();
				},
				
				settings : function(e){
					kc.views.builder.post_settings(e);
				}
				
			});
			
			$('body').attr({'data-screen': 'desktop'}).attr({'data-screen-size': '100%'});
			
			// Action before elements adding popup
			
			kc.add_action( 'before_popupAdd', 'unique-26R1', function(){
				
				$('.kc-params-popup button.cancel').trigger('click');
				
			});
			
			// Remove action backend-editor use_preset 
			
			kc.delete_action('use_preset');
			
			// Add action use_preset for live-editor
			
			kc.add_action( 'use_preset', 'uni-R53sq', function( model, name, full, pop ){
								
				if( kc.storage[model] === undefined )
					return;
					
				var oldwidth = kc.storage[model].args.width,
					fid = kc.front.push(full, model, 'replace');
				
				kc.frame.$('[data-model="'+fid+'"]').css({width: oldwidth});
				kc.storage[fid].args.width = oldwidth;
				
				pop.remove();
				
			});
			
			// remove backend action
			kc.delete_action('kc-link-section');
			// Action link section for live editor
			
			kc.add_action('kc-link-section', 'uni-2Fte2', function( e ){
				
				var id = $(e.target).closest('.kc-sections-item').data('id');
					title = $(e.target).closest('.kc-sections-item').data('title'),
					pop = kc.get.popup(e.target),
					current = pop.data('current_item'),
					model = pop.data('model');
				
				if( window.kc_post_ID !== undefined && window.kc_post_ID == id ){
					kc.msg( kc.__.i62, 'error', 'sl-close', 5000 );
					return;
				}
					
				pop.find('button.cancel').trigger('click');
				
				if( current !== undefined && model !== undefined && model !== null ){
					
					kc.storage[model].args.__section_link = id;
					kc.storage[model].args.__section_title = title;
					
					model = kc.front.push('[kc_row __section_link="'+id+'" __section_title="'+title+'"][/kc_row]', model, 'replace');
				
				}else model = kc.front.push('[kc_row __section_link="'+id+'" __section_title="'+title+'"][/kc_row]');
				
				/*
				*	convert to section control
				*/
				kc.front.ui.section.convert( model, id, title );
				
			});
			
			// Onchange action of css fields
			
			kc.add_action( 'kc-css-field-change', 'uni-26Swe2', function( wrp, pop ){
				
				setTimeout(function(wrp, pop){
					
					wrp.find('.kc-css-param').off('change').on('change', pop, function(e){
						
						var wrp = $(this).closest('.kc-param-row.field-css'),
							model = e.data.data('model'),
							name = wrp.find('input.kc-param').attr('name'),
							css = kc.params.fields.css.field_values(wrp);
						
						if (css === '' && kc.storage[model].args[name] === undefined)
							return;
						
						if (kc.storage[model].args[name] !== css)
						{
							
							kc.front.stack.push({
								model: model,
								name: name,
								new_data: css,
								mode: 'css'
							});
							
							kc.storage[model].args[name] = css;
							
							wrp.find('.kc-field-css-value').val( css );
								
							kc.front.css_system.push_to( model );
						
						}
						
					});
					
				}, 250, wrp, pop);
				
			});
			
			kc.delete_action( 'kc-ctrl-s' );
			
			kc.add_action( 'kc-ctrl-s', 'uni-26Die2', function(){
				$('#kc-front-save').trigger('click');
			});
			
			kc.add_action ('kc-ctrl-e', 'uni-2eg3ie2', function(e){
				$('#kc-front-exit').trigger('click');
			});
			
			kc.add_action ('kc-ctrl-z', 'uni-2r6Dfhr', function(e){
				kc.front.stack.undo();
			});
			
			kc.add_action ('kc-shift-ctrl-z', 'uni-2rtHJfhr', function(e){
				kc.front.stack.redo();
			});
			
			kc.add_action( 'kc-exit-right-dialog', 'uni-5dfhR4dR3', function(){
				
				document.getElementById('kc-right-click-helper').style.display = 'none';
				$('#kc-elms-breadcrumn, .kc-right-click-dialog').remove();
				_$('#kc-overlay-placeholder').attr({'style':''});
				document.oncontextmenu = null;
				
			});
			
		},
		
		load : function( frame_doc ){
			
			// onload event
				
		},
		
		params : {
		
			before_save : function( pop ){
				
				pop.data({ active_scroll : pop.find('.m-p-body').scrollTop() });
				
	
			},
			
			save : function( pop ){
				
				var model = typeof(pop) == 'number' ? pop : pop.data('model'),
					name = (kc.storage[model]!==undefined)?kc.storage[model].name:'',
					el = kc.frame.$('[data-model="'+model+'"]')
					code = kc.front.build_shortcode( model );
				
				if( kc.storage[model] === undefined )
					return;
					
				if (kc_maps_view.indexOf( name ) > -1) {
					
					var ob = kc.detect.closest( el.parent().get(0) );
					
					model = ob[1];
					name = kc.storage[model].name;
					code = kc.front.build_shortcode( model );
					setTimeout( function(){ $('.kc-params-popup .button.cancel').trigger('click'); }, 1 );
					
				}
				
				if( code !== '' ){
					
					kc.tools.popup.no_close = true;
					
					var active = kc.frame.$('[data-model="'+model+'"]').data('tab-active'),
						fid = kc.front.push( code, model, 'replace' ),
						el = kc.frame.$('[data-model="'+fid+'"]');
					
					if( kc_maps_views.indexOf( name ) > -1 && active !== undefined ){
						el.find('>div.kc_accordion_section>h3.kc_accordion_header>a,>.kc_wrapper>.ui-tabs-nav>li').
							eq( active-1 ).trigger('click').trigger('mouseover');
					}
					
					if( pop.length === undefined || pop.length === 0 )
						return;
					
					pop.find('.kc-pop-tabs>li').eq( pop.data('tab_active') ).trigger('click');
					
					if( pop.data('active_scroll') !== undefined ){
						pop.find('.m-p-body').scrollTop( pop.data('active_scroll') );
					}
					
					if( pop.find('.sl-check.sl-func').css('visibility') == 'hidden' )
						return;
						
					pop.find('.sl-check.sl-func, button.save').css({visibility: 'hidden'});
					pop.find('.m-p-overlay').stop().
						css({display: 'block', opacity: 0, top: '48px'}).
						animate({opacity: 1}, 250).
						delay(1000).
						animate({opacity:0}, function(){
							$(this).css({display: 'none', top: '-1px'});
							$(this).closest('.kc-params-popup').find('.sl-check.sl-func, button.save').attr({style:''});
						});
					
				}
				
			},
			
			cancel : function( pop ){
				
				kc.detect.locked = false;
				kc.detect.clicked = false;
				kc.detect.css_inspector.active = false;
				
			},
			
			onchanges : {},
			
			change_callback : function( el, pop, e ){
								
				var model = pop.data('model'), 
					elname = kc.storage[model].name, 
					param = el.name,
					map_values = kc.params.get_values( elname ),
					callback = kc.front.params.onchanges[elname];
				
				var redo = {
					fid: model,
					mode: 'push',
					pos: 'replace',
					old_full: kc.front.build_shortcode (model)
				};
				
				if (kc_maps_view.indexOf(elname) > -1)
				{
					var ob = kc.detect.closest(_$('[data-model="'+model+'"]').get(0).parentNode);
					redo.fid = ob[1];
					redo.old_full = kc.front.build_shortcode (ob[1]);
				}
					
				
				/*
				*	If the onchange callback has been defined the parame of this element
				*/
				if (callback !== undefined && typeof callback[param] == 'function')
				{
					
					var atts = kc.tools.getFormData( pop ),
						hidden = [];
							
					/*
					*	Merge atts from & storage
					*/
					if (kc.storage[model] !== undefined && kc.storage[model].args !== undefined)
					{
						for (var n in kc.storage[model].args)
						{
							if (atts[n] === undefined)
								atts[n] = kc.storage[model].args[n];
						}
					}
					/*
					*	Set pin empty for field has default value
					*/
					for (var n in atts)
					{
						if (atts[n] === '' && map_values[n] !== undefined)
							atts[n] = '__empty__';
					}
					/*
					*	delete field in relation hidden
					*/
					pop.find('form.fields-edit-form .kc-param-row.relation-hidden .kc-param').each(function(){
						if (map_values[this.name] === undefined)
							delete atts[this.name];
						$(this).closest('.kc-param-row').find('input,textarea,select').val('');
					});
					
					if (atts._id === undefined || atts._id === '')
						atts._id = Math.round(Math.random()*10000000);
						
					var newel = callback[param](el, pop.data('el'), atts);
						
					if (newel !== undefined)
						pop.data({ 'el' : newel });
					
					if (kc.storage[model] !== undefined)
					{
						
						if (kc.storage[model].args === undefined)
							kc.storage[model].args = {};
							
						kc.storage[model].args = atts;
						
						/*
						* Mark status is unsave	
						*/
						
						kc.confirm (true);
						
					}
					
					// add re-do stack
					if (kc_maps_view.indexOf(elname) > -1)
						redo.full = kc.front.build_shortcode (ob[1]);
					else redo.full = kc.front.build_shortcode (model);
					
					kc.front.stack.push(redo);
					
					return;
					
				}
				/*
				*	Only apply keyup event for js onchange callback
				*/
				if( e.type == 'keyup')
					return;
				/*
				*	Stop auto apply if the input is controling relation	
				*/
				if(el.className.indexOf('m-p-rela') > -1)
					return;	
				/*
				* stop if element ajax
				*/
				//if( document.getElementById('tmpl-kc-'+elname+'-template') === null )
					//return;
				
				clearTimeout( el.change_delay );
				el.change_delay = setTimeout( function(pop){ 
					pop.find('button.button.save').trigger('click');
				}, 350, pop );
				
			},
			
			get_atts : function( input, model ){
		
				var atts = kc.storage[model].args;
				
				if((input.type == 'checkbox' || input.type == 'radio') && input.checked === false)
					atts[input.name] = '';
				else atts[input.name] = kc.tools.esc_attr(input.value);
				
				if( input.getAttribute('data-encode') == 'base64' )
					atts[input.name] = kc.tools.base64.encode(input.value);
				
				return atts;
				
			}
			
		},
		
		build_shortcode : function( model, tag ){
			
			if (kc.storage[model] === undefined)
				return '';
			
			var string = css = '',
				name = (kc.storage[model] !== undefined) ? kc.storage[ model ].name : '';
			
			if (tag !== undefined && tag[name] !== undefined)
					name += tag[name];
					
			if( model !== null && kc.storage[ model ] !== undefined ){
				
				string += '['+name;
				
				for( var n in kc.storage[ model ].args ){
					if( n !== 'content' && n !== 'css_data' && n !== '__name' ){
						
						if( n == 'css' ){
							
							css = kc.storage[ model ].args.css;
							
							if( kc.storage[ model ].args.css_data !== undefined )
								css = ' css="'+kc.tools.esc_attr( kc.storage[ model ].args.css )+'|'+kc.storage[ model ].args.css_data+'"';
							else if( css.indexOf('|') > -1 )
								css = ' css="'+kc.tools.esc_attr( css )+'"';
							else css = '';
							
							string += css;
							
						}else{
							string += ' '+n+'="'+kc.tools.esc_attr( kc.storage[ model ].args[n] )+'"';
						}
							
					}
				}
				string += ']';
				
				kc.front.export (model, tag);
				
				if( 
					kc.maps[kc.storage[model].name] !== undefined && 
					kc.maps[kc.storage[model].name].is_container === true 
				){
					if (kc.storage[model].args.content === undefined)
						kc.storage[model].args.content = '';
							
					kc.storage[model].end = '[/'+name+ ']';
					string += kc.storage[model].args.content+kc.storage[model].end;
				}
			}
			
			return string;
				
		},
		
		do_shortcode : function( input, callback, pos ){
		
			if( input === undefined  )
				return null;
		
			var regx = new RegExp( '\\[(\\[?)(' + kc.tags + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g' ), result, agrs, content = input;
		
			var split_arguments = /([a-zA-Z0-9\-\_\.]+)="([^"]+)+"/gi;
			var output = input;
			
			while ( result = regx.exec( input ) ) {

				var paramesArg 	= [];
				while( agrs = split_arguments.exec( result[3] ) ){
					if(  agrs[1] != '__name' &&  agrs[1] != '__content' && agrs[2] !== '__empty__' )
						paramesArg[ agrs[1] ] = agrs[2];
				}
				
				// do not change id with update action
				if( pos === undefined || pos != 'replace' )
					paramesArg['_id'] = Math.round(Math.random()*10000000);
				
				var args = {
					full		: result[0],
					name 		: result[2],
					/*parames 	: result[3],*/
					/*content 	: result[5],*/
					end		 	: result[6],
					atts	 	: paramesArg,
					/*input		: input,
					result		: result*/
				};
				
				if( undefined !== result[5] && '' !== result[5] ){
					args.content = kc.front.process_alter( result[5], result[2] );
				}
				
				output = output.replace( result[0], kc.front.do_shortcode_tag( args, callback, pos ) );
				
			}
			
			return output;
			
		},
		
		do_shortcode_tag : function( atts, callback, pos ){
			
			var selector = '';
			
			if( atts.atts.__section_link === undefined && atts.content !== undefined && atts.content !== '' ){
				atts._content = atts.content; 
				atts.content = this.do_shortcode( atts.content, callback, pos ); 
			}
			
			var result = kc.template( atts['name'], atts );

			kc.model++;
			var model = kc.model,
				map_values = kc.params.get_values( atts['name'] );
			/*
			*	Set pin empty for field has default value
			*/
			for( var n in map_values ){
				if( atts['atts'][n] === '' || atts['atts'][n] === undefined )
					atts['atts'][n] = '__empty__';
			}
			
			kc.storage[ model ] = {
				name : atts['name'],
				args : atts['atts'],
				full : atts['full'],
			}
			
			if( callback !== undefined && 
				atts.callback !== undefined && 
				typeof( atts.callback ) == 'function' 
			){
				atts.model = model;
				callback.push( atts );
			}
			
			if( atts['end'] !== undefined ){
				kc.storage[ model ].end = atts['end'];
				kc.storage[ model ].args.content = atts._content;
				delete kc.storage[ model ].args._content;
			}
			
			/*
			*	load by ajax if no template for this element
			*/

			if( result !== null && result !== undefined && atts.atts.__section_link === undefined )
				return '<!--kc s '+model+'-->'+result.trim()+'<!--kc e '+model+'-->';
			else return '<div class="kc-element kc-undefined-layout kc-loadElement-via-ajax" data-model="'+model+'"></div>';
				
		},
		
		process_alter : function( input, tag ){
	
			/* remove ### of containers loop */
			var start = input.indexOf('['+tag+'#');
			if( start > -1 ){
				var str = input.substring( start+1, input.indexOf( ']', start ) ).split(' ')[0];
				var exp = new RegExp( str, 'g' );
				input = input.replace( exp, tag);
			}
	
			return input;
	
		},
		
		loop_box : function( items ){
		
			if( typeof( items ) != 'object' )
				return '';
		
			var output = '';
			
			for( var n in items ){
				
				if( items[n]['tag'] == 'image' )
					items[n]['tag'] = 'img';
				if( items[n]['tag'] == 'icon' )
					items[n]['tag'] = 'i';
				if( items[n]['tag'] == 'column' ){
					items[n]['tag'] = 'div';
				}
				
				if( typeof( items[n] == 'object' ) && items[n]['tag'] != 'text' ){
		
					output += '<'+items[n]['tag'];
					
					if( typeof( items[n]['attributes'] ) != 'object' )
						items[n]['attributes'] = {};
		
					if( items[n]['attributes']['class'] === undefined )
						items[n]['attributes']['class'] = '';
		
					if( items[n]['attributes']['cols'] !== undefined )
					{
						items[n]['attributes']['class'] += ' '+items[n]['attributes']['cols'];
						delete items[n]['attributes']['cols'];
					}
					
					if( items[n]['tag'] == 'img' && items[n]['attributes']['src'] === undefined )
						items[n]['attributes']['src'] = kc_plugin_url+'/assets/images/get_start.jpg';
		
					for( var at in items[n]['attributes'] )
					{
						if( items[n]['attributes'][at] !== '' )
							output += ' '+at+'="'+items[n]['attributes'][at]+'"';
					}
		
					if( items[n]['tag'] == 'img' )
						output += '/';
		
					output += '>';
		
					if( typeof( items[n]['children'] ) == 'object' )
						output += kc.front.loop_box( items[n]['children'] );
		
					if( items[n]['tag'] != 'img' )
						output += '</'+items[n]['tag']+'>';
		
				}else output += items[n]['content'];
		
			}
		
			return output;
	
		},
		
		ui : {
			
			bag : {},
						
			delay : [ 100 ],
			
			init : function(){
				
				var el = kc.frame.$('#kc-element-placeholder .move, #kc-sections-placeholder .move').each(function(){
					this.setAttribute('droppable', 'true');
			        this.setAttribute('draggable', 'true');
					this.addEventListener( 'dragstart', kc.front.ui.events.dragstart, false);
				});
				
				/* prevent default right click */
				kc.frame.doc.oncontextmenu = function(e) {
					if( _$(e.target).closest('[data-model]').length == 0 )
						return true;
					return kc.detect.disabled; 
				};
				/*
				*	Register events for drag & mouse
				*/
				kc.detect.frame.doc.addEventListener( 'dragover', this.events.dragover, false);
				kc.detect.frame.doc.addEventListener( 'dragend', this.events.dragend, false);
				document.addEventListener( 'dragend', this.events.dragend, false);
				kc.detect.frame.doc.addEventListener( 'drop', this.events.drop, false);
				kc.detect.frame.doc.addEventListener( 'mousedown', this.events.mousedown, false);
				
				$ (kc.detect.frame.window).on ('keydown', kc.ui.keys_press);
				
				$('#wpbody-content').on( 'click', function(e){
					
					if( e.target.id == 'wpbody-content' )
						kc.detect.untarget();
						
				});
				
				kc.tools.popup.margin_top = -40;

				var row = kc.frame.$('.kc-boxholder .kc-row-placeholder-move').each(function(){
					this.setAttribute('droppable', 'true');
					this.setAttribute('draggable', 'true');
					this.addEventListener( 'dragstart', kc.front.ui.events.row_dragstart, false);
				});
				
				$('#kc-elements-draggable').on('mouseover', function(e){
					clearTimeout(this.time_out);
					this.className = 'kc-ed-show';
				}).on('mouseout', function(e){
					this.time_out = setTimeout(function(el){
						el.className = '';
					}, 100, this);
				});
				
				$('#kc-live-frame').on ('mouseout', function(e)
				{
					kc.detect.untarget();
					setTimeout (kc.detect.untarget, 500);
				});
				
				$('#kc-live-frame-resizer').on('mousedown', function(e){
					
					var wrp = id('kc-live-frame-wrp');
					
					$(document)
						.off('mouseup')
						.on( 'mouseup', function(){
							$('html,body').css({cursor:''}).removeClass('noneuser kc_dragging');
							$('#kc-live-frame,#kc-live-frame-wrp').removeClass('notransition');
							$(document).off('mousemove').off('mouseup');
						})
						.off('mousemove')
						.on( 'mousemove', {
							
							wid: wrp.offsetWidth,
							wrp: wrp,
							unit: $('#kc-curent-screen-view>i').get(0),
							left: e.clientX,
							
						}, function(e){
							
							var offset = (e.data.wid + (e.clientX-e.data.left));
							
							if( offset < 320 )
								offset = '320px';
							else offset += 'px';
							
							e.data.wrp.style.width = offset;
							e.data.unit.innerHTML = offset;
						} 
					);
					
					$('html,body').css({cursor:'col-resize'}).addClass('noneuser kc_dragging');
					$('#kc-live-frame,#kc-live-frame-wrp').addClass('notransition');
					$('#kc-top-toolbar .kc-bar-devices.active').removeClass('active');
					$('#kc-curent-screen-view').addClass('active');
				})
				
			},
			
			events : {

				dragstart : function( e ){
					
					/**
					*	We will get the start element from mousedown of columnsResize
					*/
					
					var u = kc.front.ui, model = kc.get.model( this ),
                        elm = _$('[data-model="'+model+'"]');
					
					if( kc.detect.holder.el === null && kc.detect.holder.sections.el === null ){
						e.preventDefault();
						return false;
					}

					u.bag.e = kc.frame.$('[data-model="'+model+'"]').get(0);				
					u.bag.e.classList.add('kc-ui-placeholder');
					u.bag.model = model;
					u.bag.old_data = {
                        next_el: elm.next(),
                        prev_el: elm.prev(),
                        parent_el: elm.parent()
                    };

					kc.detect.frame.$('body').addClass('kc-ui-dragging');
					
			        e.dataTransfer.effectAllowed = 'move';
			        e.dataTransfer.dropEffect = 'none';

			        if( e.dataTransfer !== undefined && typeof  e.dataTransfer.setData == 'function' )
			        	e.dataTransfer.setData('text/plain', 'KingComposer.Com');

				    if( e.dataTransfer !== undefined && typeof  e.dataTransfer.setDragImage == 'function' ){
						e.dataTransfer.setDragImage(
							kc.frame.$( '#kc-ui-handle-image' ).get(0), 25, 25
						);
					}
					
					setTimeout( kc.detect.untarget, 100 );

				},
				
				add_dragstart : function( e ){
					
					/**
					*	dragstart for drag & drop to add element
					*/
					
					if( e.target === undefined || kc.front.ui.bag.name === undefined ){
						e.preventDefault();
						return false;
					}
						
					var _$ = kc.detect.frame.$, u = kc.front.ui;
									
					u.bag.e.classList.add('kc-ui-placeholder');
					u.bag.model = null;
					
					if( u.bag.footer === undefined  )
						u.bag.footer = kc.frame.$('#kc-footers');
					
					kc.detect.untarget();
					
					_$('body').addClass('kc-ui-dragging');
					
			        e.dataTransfer.effectAllowed = 'move';
			        e.dataTransfer.dropEffect = 'none';
					
			        if( e.dataTransfer !== undefined && typeof  e.dataTransfer.setData == 'function' )
			        	e.dataTransfer.setData('text/plain', 'KingComposer.Com');

				    if( e.dataTransfer !== undefined && typeof  e.dataTransfer.setDragImage == 'function' ){
						e.dataTransfer.setDragImage(
							kc.frame.$( '#kc-ui-handle-image' ).get(0), 25, 25
						);
					}
					
					$('#kc-elements-draggable').removeClass('kc-ed-show');

				},
				
				row_dragstart : function( e ){
					
					
					var model = kc.get.model( this ),
						name = kc.storage[model].name,
						el = kc.frame.$('[data-model="'+model+'"]'),
						ob = [el.get(0), model];
					
					if( kc.storage[model] !== undefined && name !== 'kc_row' && name !== 'kc_row_inner' )
						ob = kc.detect.closest( el.parent().get(0) );
					
					if( ob === null || ob[0] === null || ob[0] === undefined || ob[1] === undefined ){
						e.preventDefault();
						return false;
					}
						
					var u = kc.front.ui,
                        rmodel = kc.get.model( ob[0] )
                        elm = kc.frame.$('[data-model="'+rmodel+'"]');
					
					u.bag.e = ob[0];					
					u.bag.e.classList.add('kc-ui-placeholder');
					u.bag.model = ob[1];
                    u.bag.old_data = {
                        next_el: elm.next(),
                        prev_el: elm.prev(),
                        parent_el: elm.parent()
                    };

					$('body').addClass('kc-ui-dragging');
					
			        e.dataTransfer.effectAllowed = 'move';
			        e.dataTransfer.dropEffect = 'none';

			        if( e.dataTransfer !== undefined && typeof  e.dataTransfer.setData == 'function' )
			        	e.dataTransfer.setData('text/plain', 'KingComposer.Com');

				    if( e.dataTransfer !== undefined && typeof  e.dataTransfer.setDragImage == 'function' ){
						e.dataTransfer.setDragImage(
							kc.frame.$( '#kc-ui-handle-image' ).get(0), 25, 25
						);
					}
					
					setTimeout( kc.detect.untarget, 10 );
					
				},
				
				dragover : function( e ){
					
					var u = kc.front.ui;

					if( u.bag.e === null ){

						e.preventDefault();
						return false;

					}
					
					// Slow down each process when dragging
					
					if( u.delay[1] !== true ){
						if( u.delay[2] !== true ){
							
							u.delay[2] = true;
							
						}else{
							
							u.delay[1] = true;
							setTimeout( function(){
	
								kc.front.ui.delay[1] = false;
								kc.front.ui.delay[2] = false;
	
							}, u.delay[0] );
	
							e.preventDefault();
							return false;
						}
					}else{
						e.preventDefault();
						return false;
					}

					if(!e) e = window.event;
					
					if( u.bag.name == 'kc_row' || 
						( kc.storage[u.bag.model] !== undefined  && 
						  kc.storage[u.bag.model].name == 'kc_row' ) 
					){
							
							return kc.front.ui.events.row_dragover( e, u );
							
						//if( kc.storage[u.bag.model].name == 'kc_row_inner' )
							//return kc.front.ui.events.row_inner_dragover( e, u );
					}
					
					u.bag.t = kc.detect.closest( e.target );
					
					if( u.bag.t !== null && u.bag.t[1] !== undefined && kc.storage[u.bag.t[1]] !== undefined ){
						
						if( kc_maps_view.indexOf( kc.storage[u.bag.t[1]].name ) > -1 )
							u.bag.t = kc.detect.closest( u.bag.t[0].parentNode );
					}
					
					if( u.bag.t === null || 
						( 
							!kc.detect.is_element( u.bag.t[1] ) && 
							u.bag.t[1] != '-1'
						) || 
						( u.bag.e !== undefined && $.contains( u.bag.e, u.bag.t[0] ) ) ){

						// prevent actions when hover it self or hover its children
						e.preventDefault();
						return false;

					}else{

						u.bag.r = u.bag.t[0].getBoundingClientRect();
							
						u.bag.b = (u.bag.r.height/3);
						if( u.bag.b < 100 )
							u.bag.b = u.bag.r.height/2;
							
						if( (u.bag.r.bottom-e.clientY) < u.bag.b ){

							if( u.bag.t[0].nextElementSibling != u.bag.e ){
								$( u.bag.t[0] ).after( u.bag.e );
								kc.ui.preventFlicker( e, u.bag.e );
							}

						}else if( (e.clientY-u.bag.r.top) < u.bag.b ){

							if( u.bag.t[0].previousElementSibling != u.bag.e ){
								$( u.bag.t[0] ).before( u.bag.e );
								kc.ui.preventFlicker( e, u.bag.e );
							}
						}
						
					}

					e.preventDefault();
					return false;
				},

				row_dragover : function( e, u ){
					
					u.bag.t = kc.detect.closest( e.target );
				
					while( u.bag.t !== null && kc.storage[ u.bag.t[1] ] !== undefined ){
						
						if( kc.storage[ u.bag.t[1] ].name == 'kc_row' )
							break;
						
						u.bag.t = kc.detect.closest( u.bag.t[0].parentNode );
					}
					
					if( u.bag.t === null || u.bag.t === undefined || u.bag.t[1] == '-1' ||
						( kc.storage[ u.bag.t[1] ] !== undefined && kc.storage[ u.bag.t[1] ].name != 'kc_row' ) ){

						// prevent actions when hover it self or hover its children
						e.preventDefault();
						return false;

					}else{
						
						u.bag.r = u.bag.t[0].getBoundingClientRect();
							
						u.bag.b = (u.bag.r.height/3);
						if( u.bag.b < 100 )
							u.bag.b = u.bag.r.height/2;
							
						if( (u.bag.r.bottom-e.clientY) < u.bag.b ){

							if( u.bag.t[0].nextElementSibling != u.bag.e ){
								$( u.bag.t[0] ).after( u.bag.e );
								kc.ui.preventFlicker( e, u.bag.e );
							}

						}else if( (e.clientY-u.bag.r.top) < u.bag.b ){

							if( u.bag.t[0].previousElementSibling != u.bag.e ){
								$( u.bag.t[0] ).before( u.bag.e );
								kc.ui.preventFlicker( e, u.bag.e );
							}
						}
						
					}
					
					e.preventDefault();
					return false;
				},		
				
				row_inner_dragover : function( e, u ){
					
					u.bag.t = kc.detect.closest( e.target );
				
					while( u.bag.t !== null && kc.storage[ u.bag.t[1] ] !== undefined ){
						
						if( u.bag.t[0].parentNode === u.bag.e.parentNode )
							break;
						
						u.bag.t = kc.detect.closest( u.bag.t[0].parentNode );
					}
					
					if( u.bag.t === null || 
						( kc.storage[ u.bag.t[1] ] !== undefined && u.bag.t[0].parentNode !== u.bag.e.parentNode ) 
					){

						// prevent actions when hover it self or hover its children
						e.preventDefault();
						return false;

					}else{
						
						u.bag.r = u.bag.t[0].getBoundingClientRect();
							
						u.bag.b = (u.bag.r.height/3);
						if( u.bag.b < 100 )
							u.bag.b = u.bag.r.height/2;
							
						if( (u.bag.r.bottom-e.clientY) < u.bag.b ){

							if( u.bag.t[0].nextElementSibling != u.bag.e ){
								$( u.bag.t[0] ).after( u.bag.e );
								kc.ui.preventFlicker( e, u.bag.e );
							}

						}else if( (e.clientY-u.bag.r.top) < u.bag.b ){

							if( u.bag.t[0].previousElementSibling != u.bag.e ){
								$( u.bag.t[0] ).before( u.bag.e );
								kc.ui.preventFlicker( e, u.bag.e );
							}
						}
						
					}

					e.preventDefault();
					return false;
				},

				_drag : function( e ){

					var atts = $(this).data('atts'),
						h = atts.helperClass,
						p = atts.placeholder,
						el = kc.ui.elm_drag ;

					if( h !== '' && el !== null ){

						if( el.className.indexOf( h ) > -1 ){

							$( el ).removeClass( h );

							if( p !== '' )
								$( el ).addClass( p );
						}
					}

					if( typeof atts.drag == 'function' )
						atts.drag( e, this );

					e.preventDefault();
					return false;

				},

				_dragleave : function( e ){

					var atts = $(this).data('atts');

					if( typeof atts.leave == 'function' )
						atts.leave( e, this );

					e.preventDefault();
					return false;
				},

				dragend : function( e ){
					
					var b = kc.front.ui.bag,
                        elm = _$('[data-model="'+b.model+'"]');

                    kc.front.stack.push({
                        model: b.model,
                        mode: 'move',
                        old_data: b.old_data,
                        new_data: {
                            next_el: elm.next(),
                            prev_el: elm.prev(),
                            parent_el: elm.parent()
                        }
                    });

					if( b.e !== undefined )
						b.e.classList.remove('kc-ui-placeholder');
					
					if( b.footer !== undefined )
						b.footer.removeClass('drag-over');
						
					kc.detect.frame.$('body').removeClass('kc-ui-dragging');
					
					e.preventDefault();
					kc.detect.clicked = false;
					kc.detect.locked = false;
					
					kc.front.ui.bag = {};
					
					return false;

				},

				drop : function( e ){

					e.preventDefault();
					return false;

				},
				
				mousedown : function( e ){
					
					if( e.button == 2 &&
						(	kc.detect.disabled === false || 
							$('#kc-top-toolbar .kc-bar-devices.active').data('screen') != '100%'
						)
					) {
						
						kc.front.ui.right_click(e);
						
						return false; 
					} 
					
					return true; 
					
				},

			},
			
			row : {
				
				stretch_content : function( model ){
					
					var row = _$('[data-model="'+model+'"]');
					
					if( kc.storage[model] === undefined )
						return;
						
					if( kc.storage[model].args === undefined )
						kc.storage[model].args = {};
						
					if (kc.storage[model].args.use_container === undefined || kc.storage[model].args.use_container == 'yes')
					{
						row.find('>.kc-row-container').removeClass('kc-container');
						kc.storage[model].args.use_container = 'no';
					}
					else
					{
						row.find('>.kc-row-container').addClass('kc-container');
						kc.storage[model].args.use_container = 'yes';
					}
					
					kc.detect.untarget();
					
					if( row.get(0).offsetWidth != kc.frame.$('body').width() ){
						alert(kc.__.i57);
					}
					
				}
				
			},
			
			column : {
				
				width_calc : function( wid ){
					
					if( wid === undefined )
						wid = '12/12';
					
					wid = wid.split('/'); 
					var n = 12, m = 12;
					
					if( wid[0] !== undefined && wid[0] !== '' )
						n = wid[0];
					
					if( wid[1] !== undefined && wid[1] !== '' )
						m = wid[1];
					
					if( n == '2.4'){
						return 2.4;
					}else{
						n = parseInt( n );
						if ( n > 0 && m > 0 ){
							var calc = 12/(m/n);
							if( calc > 0 && calc <= 12 )
								return calc;
						}
					}
					
					return 12;
					
				},
				
				width_class : function( width ){
					
					if( width === undefined || width === '' ){
				
						return 'kc_col-sm-12';
				
					}else if( width.indexOf( '%' ) > -1 ){
						
						width = parseFloat( width );
						
						if( width < 12 )
							return 'kc_col-sm-1';
						else if( width < 18 )
							return 'kc_col-sm-2';
						else if( width < 22.5 )
							return 'kc_col-of-5';
						else if( width < 29.5 )
							return 'kc_col-sm-3';
						else if( width < 37 )
							return 'kc_col-sm-4';
						else if( width < 46 )
							return 'kc_col-sm-5';
						else if( width < 54.5 )
							return 'kc_col-sm-6';
						else if( width < 63 )
							return 'kc_col-sm-7';
						else if( width < 71.5 )
							return 'kc_col-sm-8';
						else if( width < 79.5 )
							return 'kc_col-sm-9';
						else if( width < 87.5 )
							return 'kc_col-sm-10';
						else if( width < 95.5 )
							return 'kc_col-sm-11';
						else return 'kc_col-sm-12';
						
					}else if( width.indexOf('/') > -1 ){
					
						var ma = width.split('/'), n = 12, m = 12;
					
						if( ma[0] !== '' )
							n = ma[0];
						if( ma[1] !== undefined && ma[1] !== '' )
							m = ma[1];
						
						if( n == 2.4){
							return 'kc_col-of-5';
						}else{
							if ( n > 0 && m > 0 ) {
								ma = Math.ceil( (n*12) / m );
								if ( ma > 0 && ma <= 12 ) {
									return 'kc_col-sm-'+ma;
								}
							}
						}
						
					}
					
					return '';
					
				},
				
				layout : function( el ){
					
					var model = el.data('e-model');
					if( kc.storage[model] === undefined || ['kc_row', 'kc_row_inner'].indexOf( kc.storage[model].name ) === -1 )
						return;
							
					var columns = kc.frame.$('[data-model="'+model+'"] [data-model]');
					
					if( columns.length === 0 )
						return;
					
					var count = 0, col = columns.get(0), mol;
					columns = [];
					while( col !== undefined && col !== null ){
						mol = col.getAttribute( 'data-model' );
						if( mol !== null &&  kc.storage[ mol ] !== undefined && ( kc.storage[ mol ].name == 'kc_column' ||  kc.storage[ mol ].name == 'kc_column_inner' ) )
							columns.push( col );
						col = col.nextElementSibling;
					}
					
					var pop = kc.tools.popup.render( 
								el.find('span.pointer').get(0), 
								{ 
									title: 'Row Layout', 
									class: 'no-footer',
									width: 341,
									float: true,
									content: kc.template( 'row-columns', {current:columns.length} ),
									help: 'http://docs.kingcomposer.com/documentation/resize-sortable-columns/?source=client_installed' 
								}
							), rect = el.find('span.pointer').get(0).getBoundingClientRect();
					
					pop.css({ left: (rect.left-63+(rect.width/2))+'px', top: '32px', zIndex: 1000000 });
						
					pop.find('.button').off('click').on( 'click', 
						{ 
							model: model,
							columns: columns,
							pop: pop
						}, 
						kc.front.ui.column.set_columns 
					);
					
					pop.find('input[type=checkbox]').on('change',function(){
						
						var name = $(this).data('name');
						if( name == undefined )
							return;
							
						if( this.checked == true )
							kc.cfg[ name ] = 'checked';
						else kc.cfg[ name ] = '';
						
						kc.backbone.stack.set( 'KC_Configs', kc.cfg );
							
					});	
					
				},
				
				set_columns : function( e ){
					
					var newcols = $(this).data('column'),
						uc = kc.front.ui.column,
						_$ = kc.detect.frame.$,
						columns = _$(e.data.columns),
						wrow = columns.eq(0).parent(),
						colWidths = [],
						model;
					
						
					if( newcols == 'custom' ){
						
						newcols = $(this).parent().find('input').val();
						if( newcols === '' || ( newcols.indexOf('%') === -1 && newcols.indexOf('/') === -1 ) ){
							alert('Invalid value, it must be some thing like: 40%+60%');
							return;
						}
						
						newcols = newcols.split('+');
						if( newcols.length > 10 ){
							alert('Maximum 10 columns, you entered '+newcols.length+' columns');
							return;
						}
						var totalcols = 0;
						for( i=0; i<newcols.length; i++ ){
							
							colWidths[i] = newcols[i].trim();
							
							if( colWidths[i].indexOf('/') > -1 ){
								colWidths[i] = colWidths[i].split('/');
								colWidths[i] = kc.tools.nfloat( (parseFloat( colWidths[i][0] )/parseFloat( colWidths[i][1] ))*100 );
							}else if( colWidths[i].indexOf('%') > -1 ){
								colWidths[i] = parseFloat( colWidths[i] );
							}
							
							totalcols += parseFloat( colWidths[i] );
							
						}
						
						if( totalcols > 100 || totalcols < 99 ){
							alert("\nTotal is incorrect: "+totalcols+"%, it must be 100%\n");
							return;
						}
						
						newcols = newcols.length;
						
					}else{
						
						newcols = parseInt( newcols );
						
						for( i=0; i<newcols; i++ ){
							colWidths[i] = kc.tools.nfloat( 100/newcols );
						}
						
					}
					
					if( columns.length < newcols ){
						
						/* Add new columns */
						var id = columns.last().data('model'), el, reInit = false;
						
						for( var i = 0; i < (newcols-columns.length) ; i++ ){
							
							var dobl = kc.front.ui.element.double( columns.last().data('model') );
							
							if( kc.cfg['columnDoubleContent'] != 'checked' ){
								
								dobl.find('[data-model]').each(function(){
									if( this.getAttribute('data-model') != '-1' ){
										delete kc.storage[this.getAttribute('data-model')];
										_$(this).remove();
									}
								});
								
							}
							
						}
							
					}else
					{
						/* Remove columns */
						var remove = [], found_empty, el, last, plast;
						
						for( var i = 0; i < (columns.length-newcols) ; i++ ){
						
							found_empty = false;
							wrow.find(' > [data-model]').each(function(){
								if( _$(this).find('[data-model]').length === 1 )
									found_empty = this;
							});
						
							if( found_empty !== false ){
								_$(found_empty).remove();
						
							}else{
						
								last = wrow.find(' > [data-model]').last();
								plast = last.prev();
								
								plast = plast.find('[data-model]').first().parent();
								
								if( kc.cfg['columnKeepContent'] == 'checked' ){
									
									el = last.find('[data-model]').get(0).parentNode.children;
									[].forEach.call( el, function( elm ){
										
										if( elm.getAttribute('data-model') !== undefined && elm.getAttribute('data-model') != '-1' )
											plast.append( elm );
									});
									
								}
								
								kc.front.clean_storage( last.data('model') );
								last.remove();
								
							}		
						}
						
					}
					
					kc.front.ui.column.reset( columns.eq(0) , colWidths );
					
					e.data.pop.remove();
					kc.detect.untarget();
					
				},
				
				responsive_all : function( el ){
					
				},
				
				insert : function( model, dir ){
					
					var _$ = kc.frame.$,
						col = _$('[data-model="'+model+'"]');
					
					if( kc.storage[model] === undefined || col.length === 0 )
						return;
						
					if( col.parent().find('>[data-model]').length >= 9 ){
						alert( kc.__.i54 );
						return;
					}
					
					var name = kc.storage[model].name,
						code = kc.ui.prepare(name);
						
					kc.front.push(code, model, dir);
						
					this.reset( col );
					
					kc.detect.wrap_node( col.parent().get(0) );
					
				},
				
				reset : function( col ){
					
					var cols = col.parent().find('>[data-model]'),
						cls = kc.tools.nfloat(100/(cols.length)),
                        colWidths = [];

					if( arguments.length > 1)
                        colWidths = arguments[1];

					cols.each(function(i){
						
						var $this = _$(this),
							model = $this.data('model');
						
						$this.removeClass(function (index, css) {
							    return (css.match (/(^|\s)kc_col-sm-\S+/g) || ['kc_col-of-5']).join(' ');
							}).removeClass('kc_col-of-5')
							.addClass( kc.front.ui.column.width_class(cls+'%') )
							.css({width: ''});
						if(colWidths.length > 0)
                            cls = colWidths[i];
						kc.storage[ model ].args.width = cls+'%';
						
						clas = kc.front.el_class( kc.storage[model].args );
						
						kc.front.css_system.push_to( model );
						
					});
					
				}
				
			},
			
			element : {
				
				edit : function( model, e ){
					
					if (kc.storage[model] === undefined)
						return;
					
					if ($('.kc-params-popup').eq(0).data('model') == model)
					{
						$('.kc-params-popup').eq(0).removeClass('kc-popup-collapse');
						return;
					}
						
					if (kc.storage[model] !== undefined 
						&& kc.storage[model].args.css_data !== undefined 
						&& kc.storage[model].args.css !== undefined 
						&& kc.storage[model].args.css.indexOf('|') === -1)
					{
						kc.storage[model].args.css += '|'+kc.storage[model].args.css_data;
					}
					
					$('.kc-params-popup button.cancel').trigger('click');
					
					var el = kc.detect.frame.contents.find('[data-model="'+model+'"]').get(0), 
						name = kc.storage[model].name,
						pop = kc.backbone.settings( el, { 
								scrollTo: false, 
								success_mesage: '<i class="fa-check"></i> '+kc.__.i50,
								noscroll: 'yes',
								save_text: 'Apply',
								cancel_text: 'Close'
							});
					
					if( pop === undefined || pop === null )
						return;
					
					pop.data({ el: kc.frame.$('[data-model="'+model+'"]') });
					pop.addClass('kc-live-editor-popup');
									
					kc.tools.popup.callback( pop, {
						before_callback : kc.front.params.before_save, 
						after_callback : kc.front.params.save, 
						cancel : kc.front.params.cancel
					});
					
					kc.detect.clicked = true;
					kc.detect.locked = true;
					
					setTimeout (function( pop ){
						
						kc.tools.popup.callback( pop, {
							
							change : kc.front.params.change_callback
							
						}, 'Unique-eR0o5w42');
						
					}, 100, pop);
					
					return pop;
						
				},
				
				double : function( model ){
					
					var _$ = kc.frame.$,
						el = kc.detect.frame.contents.find('[data-model="'+model+'"]').get(0),
						code = kc.front.build_shortcode( model );
					
					if( el !== null && el !== undefined && code !== '' ){
						
						kc.model++;
						
						var callback = [],
							elm = $( kc.front.do_shortcode( code, callback ) ),
							wrp = el.parentNode,
							name = kc.storage[model].name;
						
						if( ['kc_column', 'kc_column_inner'].indexOf( name ) > -1 ){
							var columns = kc.frame.$( wrp ).find('>[data-model]');
							if( columns.length >= 10 ){
								alert( kc.__.i54 );
								return;
							}
						}
						
						$( el ).after( elm );
						
						kc.detect.wrap_node( wrp );
						
						if( kc_maps_view.indexOf( name ) > -1 ){
							
							var ob = kc.detect.closest( wrp );
							if( ob[1] !== undefined )
								kc.front.params.save( parseInt( ob[1] ) );
							return;
							
						}
												
						if( callback.length > 0 )
							kc.do_callback( callback, elm.eq(1) );
						
						kc.front.element_vs_ajax();
						kc.detect.untarget();
						
						if( ['kc_column', 'kc_column_inner'].indexOf( name ) > -1 ){
							kc.front.ui.column.reset( _$(el) );	 
						}
						
						// add re-do stack
						kc.front.stack.push({
							model: $(el).next().data('model'),
							mode: 'double'
						});
						
						return elm;
						
					}
					
					return null;
					
				},
				
				copy : function( model ){
					
					
					if( !kc.detect.is_element(model) ){
						// if copy row level 1
						if( kc.storage[model].name == 'kc_row' ){
							
							var expo = kc.front.build_shortcode( model );
							kc.backbone.stack.set( 'KC_RowClipboard', expo );
							kc.tools.toClipboard( expo );
							
							$('body').append('<div id="kc-small-notice"><i class="fa-check"></i> '+kc.__.i66+'!</div>');
							$('#kc-small-notice').
								animate({opacity : 1}).
								delay(1000).
								animate({opacity : 0}, function(){ $(this).remove(); });
						}
						
						kc.detect.untarget();
						return;
						
					}
					
					var content = kc.front.build_shortcode(model),
						admin_view = '<strong>Copy from live-editor</strong>', 
						lm = 0, stack = kc.backbone.stack,
						page = 'live editor', list = stack.get( 'KC_ClipBoard' ), ish;
	
					if( list.length > kc.cfg.limitClipboard - 2 ){
	
						list = list.reverse();
						var new_list = [];
						for( var i = 0; i < kc.cfg.limitClipboard-2; i++ ){
							new_list[i] = list[i];
						}
	
						stack.set( 'KC_ClipBoard', new_list.reverse() );
	
					}
	
					stack.clipboard.add( {
						page	: page,
						content	: kc.tools.base64.encode(content),
						title	: kc.storage[model].name,
						des		: admin_view
					});
					
					// Push to row stack & OS clipboard
					kc.backbone.stack.set('KC_RowClipboard', content);
					kc.tools.toClipboard(content);
					
					kc.detect.untarget();
					$('body').append('<div id="kc-small-notice"><i class="fa-check"></i> Copy successful!</div>');
					$('#kc-small-notice').animate({opacity : 1}).delay(1000).animate({opacity : 0}, function(){ $(this).remove(); });
					
				},
				
				add : function( el ){
					
					kc.do_action( 'before_popupAdd', el );
					
					var pop = kc.backbone.add( el ), 
						_top = ( ($(window).height()-pop.height())/2 ),
						_left = ( ($(window).width()-pop.width())/2 );
						
					pop.find('.wp-pointer-arrow').remove();
					
					pop.css({top: (_top>50?_top:50)+'px', left: (_left>0?_left:0)+'px'});
					
					kc.detect.clicked = true;
					kc.detect.locked = true;
						
					pop.find( 'ul.kc-components-list-main li').off('click').on( 'click', function(){
						
						var model = kc.get.model( this ),
						name = $(this).data('name'),
						full = kc.ui.prepare (name);
						
						var fid = kc.front.push(full, model, kc.get.popup(this).data('pos'));

						$(this).closest('.kc-params-popup').find('.m-p-header .sl-close.sl-func').trigger('click');
						
						kc.detect.untarget();
						kc.front.element_vs_ajax();
						
						var last = kc.detect.frame.doc.querySelectorAll('[data-model="'+fid+'"] [data-model]'), mol;
						
						if( last.length > 0 ){
							for( var i = 0; i < last.length; i++  ){
								mol = last[i].getAttribute('data-model');
								if( mol != '-1' && kc.storage[ mol ] !== undefined &&
									['kc_row','kc_row_inner','kc_column','kc_column_inner'].
										indexOf( kc.storage[ mol ].name ) === -1 
									){ fid = mol; break; }
							}	
						}
						
						if( kc.detect.bone.indexOf( kc.storage[fid].name ) === -1 ){
							kc.detect.on_edit( [ kc.frame.$('[data-model="'+fid+'"]').get(0), fid ] );
						}
							
					});
					
					kc.do_action( 'after_popupAdd', pop );
					
					return pop;
					
				},
				
				delete : function( model ){
					
					var elm = _$('[data-model="'+model+'"]'),
						nelm = elm.next(),
						pelm = elm.prev(),
						ob = kc.detect.closest (elm.parent().get(0)),
						name = kc.storage[ model ].name;
					
					if (kc_maps_view.indexOf (name) > -1)
					{		
						if (ob !== null && confirm (kc.__.sure))
						{
							this.delete_permanently (model);
							var code = kc.front.build_shortcode (ob[1]);
							if (code !== '')
								kc.front.push (code, ob[1], 'replace');
						}
						return true;
					}
						
					// Set relation for undo
					elm.data({
						next_el: nelm,
						prev_el: pelm,
						parent_el: elm.parent(),
					});
					
					var cols_width = [];
					
					if(['kc_column', 'kc_column_inner'].indexOf (name) > -1)
					{
						elm.parent().find('>[data-model]').each(function(){
							cols_width.push(kc.storage[this.getAttribute('data-model')].args.width);
						});
					}
					// move to cached storage
					_$('#kc-delete-elements-cached').append(elm);
					// add re-do stack
					kc.front.stack.push({
						model: model,
						mode: 'delete',
						cols: cols_width
					});
					
					if(['kc_column', 'kc_column_inner'].indexOf (name) > -1)
					{
						if (nelm.length > 0)
							kc.front.ui.column.reset (nelm);
						else if (pelm.length > 0)
							kc.front.ui.column.reset (pelm);
					}
					
					
					kc.detect.untarget();
					$('.kc-params-popup button.cancel').trigger('click');
						
					return true;
					
				},
				
				delete_permanently : function( model ){
					
					var elm = kc.detect.frame.$('[data-model="'+model+'"]');
					
					elm.find('[data-model]').each(function(){
						delete kc.storage[ $(this).data('model') ];
					});
					
					delete kc.storage[ model ];
					elm.remove();
					
				},
				
				from_map : function( name ){
					
					var maps = kc.maps[name],
					map_params = kc.params.merge( name ),
					content = ( typeof( kc.maps[name].content ) != 'undefined' ) ? kc.maps[name].content : '',
					full = '['+name;

					for( var i in map_params ){

						if( map_params[i].type == 'random' ){

							full += ' '+map_params[i].name+'="'+parseInt(Math.random()*1000000)+'"';

						}else if( !_.isUndefined( map_params[i].value ) ){
							if( map_params[i].name == 'content' && maps.is_container === true ){
								content = map_params[i].value;
							}else{
								full += ' '+map_params[i].name+'="'+map_params[i].value+'"';
							}
						}
					}
					
					full += ' _id="'+(Math.round(Math.random()*10000000))+'"';
					
					if( name == 'kc_wp_widget' )
						full += ' data="'+$(this).data('data')+'"';
					
					full += ']';
					
					if( name == 'kc_row_inner' ){
						content += '[kc_column_inner][/kc_column_inner]';
					}
					
					if( maps.is_container === true ){
						full += content+'[/'+name+']';
					}
					
					return full;
	
				},
				
				add_section : function( model ){
					
					var shortcode = '';
					
					if( model !== null && kc.storage[ model ] !== undefined ){
						
						shortcode += '['+kc.storage[ model ].name;
						
						for( var n in kc.storage[ model ].args ){
							if( n !== 'content' )
								shortcode += ' '+n+'="'+kc.tools.esc_attr( kc.storage[ model ].args[n] )+'"';
						}
						shortcode += ']';
						
						kc.front.export( model );
						
						kc.storage[ model ].args.content += 
							kc.front.ui.element.from_map( kc.maps[ kc.storage[ model ].name ]['views']['sections'] );
						
						if( kc.storage[ model ].args.content !== undefined && kc.storage[ model ].end !== undefined ){
							shortcode += kc.storage[ model ].args.content+kc.storage[ model ].end;
						}
					}
					
					var fid = kc.front.push( shortcode, model, 'replace' );
					kc.detect.untarget();
					
				},
				
				order_section : function( section, pos ){
						
					if( section === null )
						return;
						
					var tab = section.parent().find('>.kc_tabs_nav>li.ui-tabs-active');
					
					if( pos == 'up' && section.prev().length > 0 && section.prev().data('model') !== undefined ){
							
							section.prev().before( section );
							if( tab.length > 0 )
								tab.prev().before(tab);
							
					}else if( pos == 'down' && section.next().length > 0 && section.next().data('model') !== undefined ){
								 
							 	section.next().after( section );
							 	
							 if( tab.length > 0 )
								tab.next().after(tab);	
					}
					
				},
				
			},
			
			scrollAssistive : function( ctop, eff ){

				if( kc.cfg.scrollAssistive != 1 )
					return false;

				if( typeof ctop == 'object'  ){
					ctop = kc.detect.frame.$(ctop).get(0);
					if( ctop && typeof ctop.getBoundingClientRect == 'function' ){
						
						if( ctop.tagName === 'KC' && ctop.querySelectorAll('*').length > 0 )
							ctop = ctop.querySelectorAll('*')[0];
						
						var coor = ctop.getBoundingClientRect();
						ctop = (coor.top+kc.detect.frame.$(kc.detect.frame.window).scrollTop()-100);
						
					}
				}
				
				if( undefined !== eff && eff === false )
					kc.detect.frame.$('html,body').scrollTop( ctop );
				else kc.detect.frame.$('html,body').stop().animate({ scrollTop : ctop });

			},
			
			process_tab_titles : function( data ){
				
				var regx = /kc_tab\s([^\]]+)/gi,
					split = /([a-zA-Z0-9\-\_]+)="([^"]+)+"/gi,
					html = '', atts = [], agrs;
				
				while ( result = regx.exec( data._content ) ) {
				
					if( result[0] !== undefined && result[0] !== '' ){
						atts = [];
						
						while( agrs = split.exec( result[0]) ){
							atts[ agrs[1] ] = agrs[2];
						}
						
						html += '<li>'+this.process_tab_title(atts)+'</li>';
				
					}
							
				}

				return html;
					
			},
			
			process_tab_title : function( atts ){
				
				var title = '',
					adv_title = '';
					
				if ( atts['title'] !== undefined && atts['title'] !== '' && atts['title'] !== '__empty__' )
					title = atts['title'];
		
				if( atts['advanced'] !== undefined && atts['advanced'] !== '' ){
						
					if( atts['adv_title'] !== undefined && atts['adv_title'] !== '' )
						adv_title = kc.tools.base64.decode( atts['adv_title'] );
						
					var icon=icon_class=image=image_id=image_url=image_thumbnail=image_medium=image_large=image_full='';
					var svurl = kc_ajax_url+'?action=kc_get_thumbn&id=';
					
					if( atts['adv_icon'] !== undefined && atts['adv_icon'] !== '' ){
						icon_class = atts['adv_icon'];
						icon = '<i class="'+atts['adv_icon']+'"></i>';
					}
					
					if( atts['adv_image'] !== undefined && atts['adv_image'] !== '' ){
						image_id = atts['adv_image'];
						image_url = image_full = svurl+image_id+'&size=full';
						image_medium = svurl+image_id+'&size=medium';
						image_large = svurl+image_id+'&size=large';
						image_thumbnail = svurl+image_id+'&size=thumbnail';
						image = '<img src="'+image_url+'" alt="" />';
					}
					
					adv_title = adv_title.replace( /\{icon\}/g, icon ).
								  replace( /\{icon_class\}/g, icon_class ).
								  replace( /\{title\}/g, title ).
								  replace( /\{image\}/g, image ).
								  replace( /\{image_id\}/g, image_id ).
								  replace( /\{image_url\}/g, image_url ).
								  replace( /\{image_thumbnail\}/g, image_thumbnail ).
								  replace( /\{image_medium\}/g, image_medium ).
								  replace( /\{image_large\}/g, image_large ).
								  replace( /\{image_full\}/g, image_full ).
								  replace( /\{tab_id\}/g, atts['tab_id'] );
					
					return adv_title;
						
				}else{ 
						
					if ( atts['icon_option'] !== undefined && atts['icon_option'] == 'yes' && atts['icon'] !== undefined && atts['icon'] !== '' )
						title = '<i class="'+atts['icon']+'"></i> '+title;	
						
					return '<a href="#'+atts['tab_id']+'">'+title+'</a>';
					
				}
			},
			
			section : {
				
				convert : function( model, link, title ){
					
					var wrp = _$('[data-model='+model+']');
					wrp.find('[data-model]').each(function(){
						kc.front.clean_storage( this.getAttribute('data-model') );
						this.removeAttribute('data-model');
					});
					
					kc.storage[model].args = {
						'__section_link': link,
						'__section_title': title
					}
					
					kc.storage[model].content = '';
						
				}
					
			},
			
			right_click : function( e ){
				
				// disable browser's right-click menu
				// remove exist menus
				$('.kc-right-click-dialog').remove();
					
				var ob = kc.detect.closest(e.target);

				if( ob === null || ob[1] == '-1' )
					ob = kc.detect.closest(e.target.parentNode);
				
				if( ob !== null ){
					
					document.oncontextmenu = function() { return kc.detect.disabled; };
					
					var ww = $(window).width(),
						wh = $(window).height(),
						css = { 
							position: 'fixed',
							zIndex: 99999,
							left: e.clientX+'px',
							top: (e.clientY+32)+'px'
						}
					
					var dialog = kc.detect.elements_tree(ob);

					if (dialog === false)
						return false;
						
					dialog.get(0).oncontextmenu = function(){return false;};
					
					dialog.addClass('kc-right-click-dialog').css( css );
					
					$('#kc-right-click-helper').show().html('').append(dialog);
					
					if( e.clientX > ww/2 )
						dialog.css({left: (e.clientX-dialog.width())+'px'});
					else
						dialog.addClass('kc-rc-left');
						
					if( e.clientY+32+dialog.height() > wh )
						dialog.css({top: (wh-32-dialog.height())+'px'});
					else
						dialog.addClass('kc-rc-top');
					
				}else document.oncontextmenu = null;
					
			},
			
			tour : function(){
				
				var pop = kc.ui.lightbox({
					iframe: true,
					url: 'https://www.youtube.com/embed/kFANGxXh6Fw?autoplay=1'
				}), 
				
				videos_list = $('<ul class="kc-tour-videos"> \
						<li class="active"><a href="https://www.youtube.com/embed/kFANGxXh6Fw?autoplay=1"><img src="http://img.youtube.com/vi/kFANGxXh6Fw/0.jpg" /><span>KC Pro!</span></a></li> \
						<li class="active"><a href="https://www.youtube.com/embed/74FjR6cWNaQ?autoplay=1"><img src="http://img.youtube.com/vi/74FjR6cWNaQ/0.jpg" /><span>Quick Tour</span></a></li> \
						<li><a href="https://www.youtube.com/embed/QQcSldFalnI?autoplay=1"><img src="http://img.youtube.com/vi/QQcSldFalnI/0.jpg" /><span>Responsive</span></a></li> \
						<li><a href="https://www.youtube.com/embed/eFietqQAISY?autoplay=1"><img src="http://img.youtube.com/vi/eFietqQAISY/0.jpg" /><span>CSS Inspector</span></a></li> \
					</ul>');
					
				pop.append( videos_list ).css('top', '-30px');
				
				videos_list.find('a').on('click', function(e){
					$(this).closest('.kc-tour-videos').find('.active').removeClass('active');
					$(this).parent().addClass('active');
					$(this).closest('#kc-preload').find('iframe').attr({src: $(this).attr('href')});
					e.preventDefault();
					return false;
				});
				
			}
			
		},
		
		css_system : {
			
			el : null,
			
			render : function(model) {

				/*
				*	Since ver 2.5
				*	Render all screens, stylesheet, selector of element by model
				*/
				
				if(model === undefined || kc.storage[model] === undefined)
					return;
					
				var atts = kc.storage[model].args, 
					params = kc.params.merge (kc.storage[model].name),
					css_code = '', is_css = [], n, css_render;
				
				if (atts['_id'] === undefined)
				{
					console.warn ('KingComposer: Missing id of the element when trying to render css');
					return '';
				}
				
				// Process column width
				
				if (atts['width'] !== undefined && atts['width'].toString().indexOf('%') > -1)
					css_code += this.render_item ('{`kc-css`:{`1000-5000`:{`group`:{`width|`:`'+atts['width']+'`}}}}', atts['_id']);
					
				for (n in params)
				{
					if (params[n].type == 'css')
						is_css.push( params[n].name );
				}
				
				for (n in atts)
				{	
					if (is_css.indexOf(n) > -1 || n.indexOf('_css_inspector') === 0)
						css_code += this.render_item (atts[n], atts['_id']);
				}
				
				// Add wrapper identify for css code
				if (css_code !== '')
					css_code = '/*s'+atts['_id']+'*/'+css_code+'/*e'+atts['_id']+'*/';
					
				return kc.tools.filter_images(css_code);
				
			},
			
			render_item : function(item, id) {
				
				if (item === undefined || item === '' || item === '__empty__')
					return '';
					
				var n, m, o, p, k, sel, 
					screens = [], 
					css_code = '', 
					css_line,
					css_code_itm = '', 
					selector, 
					css_array = [], 
					keys = [], 
					pro_maps = { 
						'margin': ['margin-top','margin-right','margin-bottom','margin-left'], 
						'padding': ['padding-top','padding-right','padding-bottom','padding-left'], 
						'border-radius': [
							'border-top-left-radius',
							'border-top-right-radius',
							'border-bottom-right-radius',
							'border-bottom-left-radius'
						] 
					};

						
				try{
					
					screens = JSON.parse (item.replace(/\`/g,'"'));
					keys = Object.keys (screens['kc-css']).sort(function(a,b){ 
						return (b=='any') || parseInt(a) < parseInt(b); 
					});
					
					for (m in keys)
					{ // loop screens
						m = keys[m];
						css_code_itm = '';
						css_array = [];
						for (o in screens['kc-css'][m])
						{ // loop groups	
							for (p in screens['kc-css'][m][o])
							{ // loop properties	
								sel = p.split('|');
								
								if (sel[0] == 'gap')
									prefix = '';
								else prefix = 'body.kc-css-system ';

								// available children selector: master class + children class
								if (sel[1] !== undefined &&  sel[1] !== '')
								{
									
									sel[1] = sel[1].split(',')
									selector = [];
									
									for (k in sel[1])
									{
										/*
										*	add spacing for selector which is not :hover
										*/
										sel[1][k] = kc.tools.unesc_attr( sel[1][k].trim() );
										
										if( sel[1][k].indexOf('+') === 0 )
											sel[1][k] = sel[1][k].substr(1);
										else if( sel[1][k].indexOf(':') !== 0 )
											sel[1][k] = ' '+(sel[1][k]);
											
										selector.push (prefix+'.kc-css-'+id+sel[1][k]);
									}
									selector = selector.join(',');
								}
								else
								{
									
									selector = prefix+'.kc-css-'+id;
								}
								
								var gap_selector = prefix+'.kc-css-'+id+'>.kc-wrap-columns';
								// group properties with same selector into one
									 
								if (css_array[ selector ] === undefined)
									css_array[ selector ] = [];
								
								if (css_array[ gap_selector ] === undefined)
									css_array[ gap_selector ] = [];
								
								/*
								*	Process for margin, padding and border-radius
								*	Incase using less than 4 corners
								*/
								var cval = screens['kc-css'][m][o][p];
								
								if (sel[0] == 'gap')
								{
									
									if (parseInt(cval) < 0)
										cval = '0px';
										
									css_line = 'padding-left: '+cval+';'+'padding-right: '+cval;
									
									css_array[ gap_selector ].push( 'margin-left: -'+cval+';'+'margin-right: -'+cval+';width: calc(100% + '+(parseInt( cval )*2)+'px)' );
									
								}
								else if (sel[0] == 'border')
								{
									
									css_line = '';
									
									if( cval.indexOf('|') > -1 ){
										cval = cval.split('|');
										var bmap = ['top', 'right', 'bottom', 'left'];
										for( var cj=0; cj<4; cj++ ){
											if( cval[cj] !== undefined && cval[cj] !== '' )
												css_line += 'border-'+bmap[cj]+': '+cval[cj]+';';
										}
									}else css_line = 'border: '+cval;
								
								}
								else if (sel[0] == 'custom')
								{
									
									css_line = cval.replace(/\"/g,'')
													.replace(/\'/g,'')
													.replace(/\[/g,'')
													.replace(/\]/g,'').trim()+'{{{end}}}';
									
									css_line = css_line.replace(';{{{end}}}', '').replace('{{{end}}}', '');
								
								}
								else if(sel[0] == 'background')
								{
									
									var css_obj = { 
											color: 'transparent', 
											linearGradient: [''], 
											image: 'none', 
											position: '0% 0%', 
											size: 'auto', 
											repeat: 'repeat', 
											attachment: 'scroll', 
											advanced: 0 
										}, val = '';
									
									try{
										
										css_obj = $.extend( css_obj, JSON.parse(kc.tools.base64.decode(cval)) );
										
										if (css_obj.linearGradient[0] !== '')
										{
											if (css_obj.linearGradient[0].indexOf('deg') > -1)
											{
												if (css_obj.linearGradient[1] !== undefined && css_obj.linearGradient[1] !== '')
												{
													if (css_obj.linearGradient[2] === undefined || css_obj.linearGradient[2] === '')
													{
														css_obj.linearGradient[2] = css_obj.linearGradient[1];
													}
												}
											}
											else if (css_obj.linearGradient[1] === undefined || css_obj.linearGradient[1] === '')
												css_obj.linearGradient[1] = css_obj.linearGradient[0];
											
											css_obj.linearGradient = css_obj.linearGradient.join(', ').replace(/\,\ \,/g,', ');
											
											val += 'linear-gradient('+css_obj.linearGradient+')';
											
										}
										
										if (css_obj.color != 'transparent' && css_obj.color !== '')
											val += ( val !== '' ? ', ' : '')+css_obj.color;
										
										if (css_obj.image != 'none' && css_obj.image != '')
										{
											
											if (val === '')
												val += css_obj.color;
											else if(css_obj.color == 'transparent' || css_obj.color === '')
												val += ', transparent';
												
											val += ' url('+css_obj.image+') '+css_obj.position+'/'+css_obj.size+' '+css_obj.repeat+' '+css_obj.attachment;
											
										}
										
										if (val !== '')
											css_line = sel[0]+': '+val;
									
									}catch(ex){
										if (val !== '')
											css_line = sel[0]+': '+cval;
									};
									
								}
								else css_line = sel[0]+': '+cval;
								
								if (Object.keys(pro_maps).indexOf( sel[0] ) > -1 && css_line.indexOf('inherit') > -1)
								{
									
									css_line = cval.split(' ');
									
									for (p = 0; p<4; p++)
									{
										if(css_line[p] !== undefined && css_line[p].trim() != 'inherit')
										{
											css_line[p] = pro_maps[sel[0]][p]+': '+css_line[p];
											if( css_line[4] !== undefined )
												css_line[p] += ' '+css_line[4];
										}else css_line[p] = '';
									}
									
									css_line = css_line.filter(String).join(';');
									
								}
								if (css_line != ';')
									css_array[ selector ].push (css_line);
								
							}
						}
						
						for( sel in css_array ) {
							if (css_array[sel].length > 0)
								css_code_itm += sel+'{'+css_array[sel].join(';').replace(/\{/g,'').replace(/\}/g,'')+';}';
						}
						
						if( m != 'any' ){
							
							if( m.indexOf('-') === -1 ){
								css_code += '@media only screen and (max-width: '+m.trim()+'px){'+css_code_itm+'}';
							}else{
								m = m.split('-');
								css_code += '@media only screen and (min-width: '+m[0].trim()+'px) and (max-width: '+m[1].trim()+'px){'+css_code_itm+'}';
							}
							
						}else{
							css_code += css_code_itm;
						}
						
					}
					
					
				}catch( err ){
					console.error('KingComposer: '+err.message+"\n"+item); 
				};
	
				delete n, m, o, p, k, sel, screens, css_code_itm, selector, css_array;
					
				return css_code;
				
			},
			
			push_to : function(model) {
				
				if( this.el === null )
					this.el = kc.frame.$('#kc-css-render');
					
				this.el.html( this.clean( model ) + this.render( model ) );
				
				kc.confirm( true );
				
			},
			
			scan : function(el) {
				
				var ids = [];
				
				if( el.data('model') !== undefined )
					kc.front.css_system.push_to( el.data('model') );
					
				el.find('[data-model]').each(function(){
					kc.front.css_system.push_to( this.getAttribute('data-model') );
				});
				
			},
			
			clean : function(model) {
				
				if( kc.storage[model] === undefined )
					return;
					
				if( this.el === null )
					this.el = kc.frame.$('#kc-css-render');
					
				var html = this.el.html(), s1, s2, id = kc.storage[model].args._id;
				
				s1 = html.indexOf('/*s'+id+'*/');
				
				while( s1 > -1 ){
					s2 = html.indexOf('/*e'+id+'*/', s1)+(id.toString().length+5);
					html = html.substr(0, s1)+html.substr(s2);
					s1 = html.indexOf('/*s'+id+'*/');
				}
				
				return html;
				
			},
			
			get : function(att) {
				
				var screens = [479, 767, 999, 1024],
					active = $('#kc-top-toolbar .kc-bar-devices.active'),
					screen = active.data('screen'),
					param_name = '_css_inspector_marginer';
				
				if( screen == 'custom' )
					screen = parseInt( active.find('>i').html() );
				
				if( screen == '100%' ){
					screen = 'any';
				}else if( screens.indexOf( screen ) === -1 ){
					for( var i=0; i<screens.length; i++ ){
						if( screen <= screens[i] ){
							screen = screens[i];
							break;
						}
					}
				}
				
				// if selector is defined, then the css inspector mode is enable
				if( att.selector !== undefined && att.selector !== '' && kc.detect.css_inspector.enable === true ){
					param_name = '_css_inspector_'+kc.tools.esc_slug(att.selector);
				}else{
					// test with param name css_custom
					if (kc.storage[att.model] !== undefined)
					{
							
						var merge = kc.params.merge( kc.storage[att.model].name );
						
						for (i in merge)
						{
							if( merge[i]['name'] == 'css_custom' && merge[i]['options'] === undefined )
								param_name = 'css_custom';
						}
						
					}
				
				}
				
				var param_value = kc.storage[att.model]['args'][param_name];
				
				// if this selector has not value before
				if (param_value !== undefined && param_value !== '')
				{
					param_value = JSON.parse (param_value.replace(/\`/g,'"'));
					
					if (param_value['kc-css'][screen] !== undefined && 
						param_value['kc-css'][screen][att.group] !== undefined &&
						param_value['kc-css'][screen][att.group][att.property+'|'+att.selector] !== undefined 
					){	
						return {screen: screen, param: param_name, value: param_value['kc-css'][screen][att.group][att.property+'|'+att.selector]};
					}
	
				}
				
				return { screen: screen, param: param_name, value: '' };
				
			},
			
			set : function(att) {
				
				var param_value = kc.storage[att.model]['args'][att.param],
					set_value = {};
				
				set_value[att.property+'|'+att.selector] = att.value;
				
				// if this selector has not value before
				if (param_value === undefined || param_value === ''){
					
					param_value = { 'kc-css' : {} };
					
					param_value['kc-css'][att.screen] = {}
						
					param_value['kc-css'][att.screen][att.group] = set_value;
							
				}else{ // if this selector has values
					
					param_value = JSON.parse( param_value.replace(/\`/g,'"') );
					
					if( param_value['kc-css'][att.screen] === undefined )
						param_value['kc-css'][att.screen] = {}
						
					if( param_value['kc-css'][att.screen][att.group] === undefined )
						param_value['kc-css'][att.screen][att.group] = set_value;
					else
						param_value['kc-css'][att.screen][att.group][att.property+'|'+att.selector] = att.value;
	
				}
				
				param_value = JSON.stringify( param_value ).toString().replace(/\"/g, '`');
				
				// add redo-stack
				kc.front.stack.push({
					model: att.model,
					name: att.param,
					new_data: param_value,
					mode: 'css'
				});
						
				// update to storage
				kc.storage[att.model]['args'][att.param] = param_value;
	
				//render css again for this element
				kc.front.css_system.push_to( att.model );
				
			},
			
			clear : function(model) {
				
				if( model === undefined || kc.storage[model] === undefined )
					return;
					
				var atts = kc.storage[model].args, 
					params = kc.params.merge (kc.storage[model].name),
					is_css = [], n;
				
				if (atts['_id'] === undefined)
				{
					console.warn('KingComposer: Missing id of the element when trying to clear css');
					return '';
				}
					
				for (n in params) 
				{
					if (params[n].type == 'css')
						is_css.push (params[n].name);
				}
				
				var screen = parseInt($('#kc-curent-screen-view i').html()), json;
				for (n in atts) {
					
					if (is_css.indexOf (n) > -1 || n.indexOf ('_css_inspector') === 0)
					{
						if (kc.detect.is_responsive() === true) {
							
							json = JSON.parse(kc.storage[model].args[n].replace(/\`/g, '"'));
							for(var sc in json['kc-css']) {
								if (sc == screen) {
									delete json['kc-css'][sc];
								}
							}
							
							kc.storage[model].args[n] = JSON.stringify(json).replace(/\"/g, '`');
							
						}else delete kc.storage[model].args[n];
					}
					
				}
				
				this.push_to (model);

			},
				
		},
		
		stack : {
			
			storage : [],
			
			index : 0,
			
			max : 20,
			
			timeout : true,
			
			push : function(args, bulk){
				
				if (kc.storage[args.model] !== undefined && args.mode == 'css')
					args.old_data = kc.storage[args.model].args[args.name];
				
				if (this.timeout === false && bulk === undefined)
				{
					return false;
				}
				
				this.storage[this.index] = args;
				this.index += 1;
				
				for (var i = this.index; i < this.storage.length; i++)
				{
					if(this.storage[i] !== undefined && this.storage[i].mode == 'delete')
						kc.front.ui.element.delete_permanently (this.storage[i].model);
					this.storage[i] = undefined;
				}
				
				if(this.storage.length > this.max)
					this.reset();
				
				$('#kc-bar-undo').addClass('has_items');
				$('#kc-bar-redo').removeClass('has_items');
				
				this.timeout = false;
				setTimeout (function(a){ kc.front.stack.timeout = true; }, 350);
				
			},
			
			undo : function(){
				
				if (this.index === 0)
				{
					$('#kc-bar-undo').removeClass('has_items');
					return 0;
				}
				
				kc.detect.untarget();
					
				this.index -= 1;
				this.st = this.storage[this.index];
				
				if (this.st !== undefined)
				{
					switch (this.st.mode)
					{
						
						case 'css':
						
							kc.storage[this.st.model].args[this.st.name] = this.st.old_data;
							kc.front.css_system.push_to (this.st.model);
							
						break;
						
						case 'delete':
							
							this.undelete_elm(this.st.model);	
							
						break;
						
						case 'push':
							
							if (this.st.pos != 'replace')
								this.delete_elm(this.st.fid);
							else if(kc.storage[this.st.fid] !== undefined) 
								kc.front.push (this.st.old_full, this.st.fid, 'replace', true);
							
						break;

						case 'move':
								this.move_elm(this.st.model, this.st.old_data);
						break;

                        case 'row_order':
                            this.row_order(this.st.model, this.st.old_index);
                        break;

						
						case 'double':
							
							this.delete_elm(this.st.model);
							
						break;
						
					}
					
					$('#kc-bar-redo').addClass('has_items');
					if (this.storage[this.index-1] === undefined)
						$('#kc-bar-undo').removeClass('has_items');
				}
				
			},
			
			redo : function(){
				
				kc.detect.untarget();
					
				this.st = this.storage[this.index];
				
				if (this.st !== undefined)
				{
					switch (this.st.mode)
					{
						
						case 'css':
						
							kc.storage[this.st.model].args[this.st.name] = this.st.new_data;
							kc.front.css_system.push_to (this.st.model);
							
						break;
						
						case 'delete':
						
							this.delete_elm(this.st.model);
							
						break;

                        case 'move':
                            this.move_elm(this.st.model, this.st.new_data);
                        break;

                        case 'row_order':
                            this.row_order(this.st.model, this.st.new_index);
                        break;

						
						case 'push':
							
							if (this.st.pos != 'replace')
								this.undelete_elm(this.st.fid);
							else if(kc.storage[this.st.fid] !== undefined) 
								kc.front.push (this.st.full, this.st.fid, 'replace', true);
							
						break;
						
						case 'double':
							
							this.undelete_elm(this.st.model);
							
						break;
					}
					
					this.index += 1;
					$('#kc-bar-undo').addClass('has_items');
					
					if(this.storage[this.index] === undefined)
						$('#kc-bar-redo').removeClass('has_items');
						
				}
				else
				{
					$('#kc-bar-redo').removeClass('has_items');
				}
				
			},

            row_order : function( model, index ){

                var row = kc.frame.$('[data-model="'+model+'"]'),
                    rows = row.parent().find('>[data-model]');

                if( index === '' || index < 0 || index > rows.length ){

                    $(row).
                    animate({marginLeft:-20}, 150).
                    animate({marginLeft:15}, 150).
                    animate({marginLeft:-10}, 150).
                    animate({marginLeft:5}, 150).
                    animate({marginLeft:0}, 150);

                }else if( index == 0 || index == 1 ){

                    rows.first().before( row );
                    kc.front.ui.scrollAssistive( row.get(0), true );

                }else{

                    if( rows.index(row) < index-1 )
                        rows.eq(index-1).after( row );
                    else rows.eq(index-1).before( row );

                    kc.front.ui.scrollAssistive( row.get(0), true );

                }
                return false;

            },
			
			delete_elm : function(model){
				
				var el = _$('[data-model="'+model+'"]');
				
				if (el.length === 0 || kc.storage[model] === undefined)
					return;	
				
				var nelm = el.next(),
					pelm = el.prev(),
					name = kc.storage[model].name;
			
				// Set relation for undo
				el.data({
					next_el: nelm,
					prev_el: pelm,
					parent_el: el.parent(),
				});
				
				var cols_width = [];
				if(['kc_column', 'kc_column_inner'].indexOf (name) > -1)
				{
					el.parent().find('>[data-model]').each(function(){
						cols_width.push(kc.storage[this.getAttribute('data-model')].args.width);
					});
					this.st.cols = cols_width;
				}
				
				// move to cached storage
				_$('#kc-delete-elements-cached').append(el);
				
				if(['kc_column', 'kc_column_inner'].indexOf (name) > -1)
				{
					if (nelm.length > 0)
						kc.front.ui.column.reset (nelm);
					else if (pelm.length > 0)
						kc.front.ui.column.reset (pelm);
				}
				
			},

            move_elm : function(model, data){

                var el = _$('[data-model="'+model+'"]');

                if (el.length === 0 || kc.storage[model] === undefined)
                    return;

                if (data.next_el.length > 0)
                    data.next_el.before(el);
                else if (data.prev_el.length > 0)
                    data.prev_el.after(el);
                else if (data.parent_el.length > 0)
                    data.parent_el.append(el);

            },

			undelete_elm : function(model){
				
				var el = _$('[data-model="'+model+'"]');
				
				if (el.length === 0 || kc.storage[model] === undefined)
					return;
								
				if (el.data('next_el').length > 0)
					el.data('next_el').before(el);
				else if (el.data('prev_el').length > 0)
					el.data('prev_el').after(el);
				else if (el.data('parent_el').length > 0)
					el.data('parent_el').append(el);
				
				if (this.st.cols !== undefined && this.st.cols.length > 0)
				{
					var cols = el.data('parent_el').find('>[data-model]');
					for (var i=0; i<this.st.cols.length; i++)
					{
						model = cols.eq(i).data('model');
						kc.storage[model].args.width = this.st.cols[i];
						kc.front.css_system.push_to( model );
					}
				}
				
			},
			
			reset : function(){

				var new_st = [], j;
				for (var i = 0; i < this.storage.length; i++)
				{
					j = this.storage.length-i-1;
					
					if (new_st.length < this.max)
					{
						if (this.storage[j] !== undefined)
							new_st.push (this.storage[j]);
					}
					else
					{
						if(this.storage[j] !== undefined && this.storage[j].mode == 'delete')
							kc.front.ui.element.delete_permanently (this.storage[j].model);
					}
				}	
				
				new_st.reverse();
				this.storage = new_st;
				
			}
				
		},
		
		el_class : function( atts ){
			
			var str = ['kc-elm'];
			
			if (atts !== undefined)
			{
				if(atts['css'] !== undefined)
				{
					atts['css'] = atts['css'].split('|');
					str.push( atts['css'][0] );
				}
				
				if (atts['_id'] !== undefined)
					str.push( 'kc-css-'+atts['_id'] );
					
				if (atts['width'] !== undefined && kc.front.ui.column.width_class(atts['width']) !== '')
					str.push (kc.front.ui.column.width_class(atts['width']));
				
				/*if (atts['animate'] !== undefined && atts['animate'] !== '')
				{
					var ani = atts['animate'].split('|')
					
					if (ani[0] !== undefined && ani[0] !== '')
						str.push ('kc-animated kc-animate-eff-'+ani[0]);
					if (ani[1] !== undefined && ani[1] !== '')
						str.push ('kc-animated kc-animate-delay-'+ani[1]);
					if (ani[2] !== undefined && ani[2] !== '')
						str.push ('kc-animated kc-animate-speed-'+ani[2]);	
				}*/
			
			}
				
			return str;
			
		},
		
		push : function(full, model, pos, undo){
			
			var callback = [], elm, wrp,
				redo = {
					model: model,
					mode: 'push',
					pos: pos,
					full: full
				};
				
			if( !kc.ui.check_tmpl() ) return null;
			
			// Push to bottom of builder
			if (model === undefined || kc.storage[ model ] === undefined)
			{
				
				full = full.toString().trim();
				/*
				*	push before kc-footer
				*/
				if (full.indexOf('[kc_row ') !== 0 && full.indexOf('[kc_row]') !== 0)
				{
					if (kc.front.do_shortcode( full ) == full)
						full = '[kc_column_text]' +full+'[/kc_column_text]';
					
					full = '[kc_row'+kc.params.get_atts('kc_row')+']'
						  +'[kc_column width="12/12"'+kc.params.get_atts('kc_column')+']'
						  +full
						  +'[/kc_column][/kc_row]';
						  
				}
				
				elm = $(kc.front.do_shortcode(full, callback));
				
				kc.detect.frame.contents.find('#kc-footers').before( elm );
				kc.detect.wrap_node( kc.detect.frame.body );
				
				elm.each(function(){
					if (typeof this.getAttribute == 'function' && this.getAttribute('data-model') !== null)
					{
						kc.front.stack.push ({
							
							fid: this.getAttribute('data-model'),
							mode: 'push',
							pos: pos,
							full: kc.front.build_shortcode(this.getAttribute('data-model'))
							
						}, true/*Allow bulk pushing*/);
					}
				});
			
			}
			else
			{
				// push into builder
				elm = kc.detect.frame.$(kc.front.do_shortcode (full, callback, pos));
				wrp = kc.detect.frame.$('[data-model="'+model+'"]').eq(0);
				
				if (wrp.length === 1)
				{	
					var pwrp = wrp.parent().get(0), 
						items = wrp.find('[data-model]').first().parent().find('>[data-model]');
					
					switch (pos)
					{
						
						case 'replace':
							
							redo.old_full = kc.front.build_shortcode (model);
							/*
							*	Clean storage & css off elements that will be deleted
							*/
							wrp.find('[data-model]').each(function(){
								kc.front.clean_storage( this.getAttribute('data-model') );
							});
							
							if (!elm.hasClass('kc-loadElement-via-ajax'))
							{
								wrp.after( elm ).remove();
								kc.detect.wrap_node( pwrp );
							}
							else
							{
								wrp.addClass('kc-loadElement-via-ajax');
							}
							
						break;
						
						case 'before': 
							wrp.before( elm );
							kc.detect.wrap_node( pwrp );
						break;
						
						case 'after': 
							wrp.after( elm );
							kc.detect.wrap_node( pwrp );
						break;
						
						case 'top': 
							if (items.length > 0)
							{
								items.first().before( elm );
								kc.detect.wrap_node( wrp.get(0) );
							}
						break;
						
						default: 
							if (items.length > 0)
							{
								items.last().after( elm );
								kc.detect.wrap_node( wrp.get(0) );
							}
						break;
						
					}
					
					if( pos != 'replace' )
						kc.front.ui.scrollAssistive( elm.get(1), true );
					
				}
			}
			
			/*
			*	Do elements's callback
			*/
			
			if( callback.length > 0 )
				kc.do_callback( callback, elm.eq(1) );
			
			/*
			*	Load content via ajax for element which have not live template
			*/
			
			kc.front.element_vs_ajax();
			
			/*
			* Mark status is unsave
			*/

			kc.confirm( true );
			
			var fid = elm.get(0);
			if( fid.nodeType === 8 )
				fid = fid.data.replace( /[^0-9]/g,'' );
			else fid = elm.data('model');
			
			elm = kc.detect.frame.$('[data-model="'+fid+'"]');

			if( pos != 'replace' ){
				
				elm.addClass('kc-bounceIn');
				setTimeout( function( target ){ target.removeClass('kc-bounceIn'); }, 1200, elm );
			
				if (undo === undefined && kc.storage[model] !== undefined)
				{
					// Add re-do stack
					redo.fid = fid;
					kc.front.stack.push (redo);
				}
				
				return fid;
				
			}else{
				
				kc.storage[model] = kc.storage[fid];
				elm.attr({'data-model':model});
				$('.kc-params-popup.kc-live-editor-popup').data({ el: elm });
				
				if (undo === undefined)
				{
					// Add re-do stack
					redo.fid = model;
					kc.front.stack.push(redo);
				}
				
				delete kc.storage[fid];
				return model;
			
			}
			
		},
		
		export : function( model, tag ){
			
			var string = '', 
				_$ = kc.detect.frame.$;
				
			if( model !== null && kc.storage[ model ] !== undefined ){
				
				var name = kc.storage[ model ].name;
				
				if( tag === undefined )
					tag = {};
				
				if( tag[name] === undefined )
					tag[name] = '#';
				else tag[name] += '#';
				
				if( _$('[data-model="'+model+'"]').find('[data-model]').length > 0 ){
						
					var checked = [], fm;
					
					_$('[data-model="'+model+'"]').find('[data-model]').each(function(){
						fm = this.getAttribute('data-model');
						
						if(fm !== null && fm !== '-1' && checked.indexOf(fm) === -1 && kc.front.check_parent(this, model) === true){
							string += kc.front.build_shortcode( fm, $.extend( {}, tag ) );
							checked.push( fm );
						}
					});
					
					kc.storage[ model ].args.content = string;
				
				}else string = kc.storage[ model ].args.content;
				
			}else{
				_$('#kc-footers').parent().find('[data-model]').first().parent().find(' > [data-model]').each(function(){
					string += kc.front.export( _$(this).data('model'), $.extend( {}, tag ) );
				});
			}
			
			return string;
				
		},
		
		check_parent : function( el, model ){
			
			el = el.parentNode;
			
			while( el !== null && el !== undefined ){
				if( el.getAttribute('data-model') !== null &&  el.getAttribute('data-model') !== '-1' ){
					if(  el.getAttribute('data-model') == model )
						return true;
					else return false;
				}
				el = el.parentNode;
			}
			return false;
		},
		
		clean_storage : function( model ){
			
			var el = kc.detect.frame.$('[data-model="'+model+'"]').get(0)
			
			if( el !== undefined ){
				
				var model = el.getAttribute( 'data-model' ), css;
				
				if( kc.storage[ model ] !== undefined ){
					if( kc.storage[ model ].args._id !== undefined )
						kc.front.css_system.clean( kc.storage[ model ].args._id );
					delete kc.storage[ model ];
				}
				
				var els = el.querySelectorAll('[data-model]');
				for( var i = 0; i < els.length; i++ ){
					kc.front.clean_storage( els[i].getAttribute('data-model') );
				}
			}
		},
		
		element_vs_ajax : function(){
			
			var _$ = kc.detect.frame.$;
			_$('.kc-loadElement-via-ajax').each(function(){
				
				if( _$(this).data('is_loaded') === true )
					return;
				else _$(this).data({ 'is_loaded' : true });
				
				_$.post( kc_ajax_url, {
					
					'security': kc_ajax_nonce,
					'action' : 'kc_load_element_via_ajax',
					'kc_action' : 'live-editor',
					'model' : $(this).data('model'),
					'ID' : (kc_post_ID !== undefined) ? kc_post_ID : 0,
					'code' : kc.tools.base64.encode( kc.front.build_shortcode( $(this).data('model') ) )
					
				}, function (result) {
					
					if( result == '-1' ){
						kc.msg( kc.__.security, 'error', 'sl-close' );
						return;
					}
					
					if( typeof( result ) != 'object' || result.model === undefined ){
						kc.msg( 'Error: please reload to try again', 'error', 'sl-close' );
						return;
					}
					
					if( result.stt == '0' ){
						kc.msg( result.message, 'error', 'sl-close' );
						return;
					}
					/*
					*	Element was deleted
					*/
					if( _$('[data-model="'+result.model+'"]').length === 0 ){
						delete kc.storage[result.model]
						kc.front.css_system.clean( result.model );
						return;
					}
						
					/*
					*	return under shortcode by section link
					*/
					if( result.__section_link !== undefined ){
						
						var fid = kc.front.push( result.html, result.model, 'replace' );
						
						kc.front.ui.section.convert( fid, result.__section_link, result.__section_title );
						
						return;
					}
					
					var elm = _$(result.html), wrp = _$('[data-model="'+result.model+'"]').parent();
					_$('[data-model="'+result.model+'"]').after( elm ).remove();
					
					elm.data({ model: result.model });
					
					kc.detect.wrap_node( wrp.get(0) );
					
					$('.kc-params-popup.kc-live-editor-popup').data({ el: kc.frame.$('[data-model="'+result.model+'"]') });
					
					if( result.callback !== undefined && typeof( result.callback ) == 'object' ){
						for( var i in result.callback )
							result.callback[i].model = result.model;
						kc.do_callback( result.callback, _$('[data-model="'+result.model+'"]') );
						
					}
					
					kc.front.css_system.push_to( result.model );
					kc.frame.window.kc_front.owl_slider();
						
				});
			
			});
			
		},
		
		live_changes : function( name, param, callback ){
			
			if( typeof name == 'object' ){
				for( var i in name ){
					kc.front.params.onchanges[i] = name[i];
				}
			}else{
				if( typeof param == 'object' ){
					for( var i in param ){
						kc.front.params.onchanges[name][i] = param[i];
					}
				}else if( typeof callback == 'function' ){
					kc.front.params.onchanges[name][param] = callback;
				}
			}
			
		},
	 
	}

	$( document ).ready( function(){
		
		if( kc.init_front_ready === true )
			kc.front.init();
			
	});
	
	$ (window).on ('load', function(){
		
		
		
	});

} )( jQuery );
