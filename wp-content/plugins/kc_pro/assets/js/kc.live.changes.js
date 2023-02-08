/*
 * King Composer Project
 *
 * (c) Copyright king-theme.com
 *
 * Must obtain permission before using this script in any other purpose
 *
 * kc.live.changes.js
 *
*/

( function($){
	
	if( typeof( kc ) == 'undefined' ){
		console.error('Could not load KingComposer core library');
		return;
	}
	
	var do_same = function( name, func, list ){
			for( var i in list ){
				args[name][list[i]] = args[name][func];
			}
		},
		
		add_class = function( input, el ){
			
			if( kc.storage[el.data('model')] === undefined )
				return;
				
			var args = kc.storage[el.data('model')].args;
			
			if( args[input.name] !== undefined && args[input.name] !== '' )
				el.removeClass( args[input.name] );
			
			el.addClass( kc.tools.esc_attr( input.value ) );
			
		},

		load_template = function( input, el, atts ){

			if( kc.frame.$(input).attr('type') === 'text' && kc.frame.$(input).is(':focus') )
				return;

			if( kc.storage[el.data('model')] === undefined )
				return;

			var _$ = kc.detect.frame.$,
				model = el.data('model');
			kc.storage[ model ].args = atts;
			_$.post( kc_ajax_url, {
				'security': kc_ajax_nonce,
				'action' : 'kc_load_element_via_ajax',
				'model' : model,
				'ID' : (kc_post_ID !== undefined) ? kc_post_ID : 0,
				'code' : kc.tools.base64.encode( kc.front.build_shortcode( model ) )

			}, function (result) {

				if( typeof( result ) != 'object' || result.model === undefined )
					return;

				var elm = _$( result.html ), wrp = _$('[data-model="'+result.model+'"]').parent();
				_$('div[data-model="'+result.model+'"]').after( elm ).remove();

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

		}
		
		add_id = function( inp, el, atts ){
					
			el.attr({ id: atts['row_id'] });
			
		},
		
		args = {
		
			'kc_row' : {
				
				'__row' : function( el ){
					if( el.hasClass('kc_row') )
						return el;
					else return el.find('>.kc_row').first();	
				},
				
				'row_id' : add_id,
				
				'use_container' : function( inp, el, atts ){
					
					var model = el.data('model'), 
						row = _$('[data-model="'+model+'"]');
					
					if( kc.storage[model] === undefined )
						return;

					if( atts['use_container'] == 'yes' ){
						row.find('>.kc-row-container').addClass('kc-container');
					}else{
						row.find('>.kc-row-container').removeClass('kc-container');
					}
                    if( atts['force'] == 'yes')
						this.force( inp, el, atts);
					
					kc.detect.untarget();
					
					if( row.get(0).offsetWidth != kc.frame.$('body').width() ){
						alert(kc.__.i57);
					}

				},

                'force' : function( inp, el, atts ){

                    var model = el.data('model'),
                        row = _$('[data-model="'+model+'"]');

                    if( atts['force'] == 'yes'){

                        if( atts['use_container'] == 'yes' ){
                            row.attr({'data-kc-fullwidth' : 'row'});
                        }else{
                            row.attr({'data-kc-fullwidth' : 'content'});
                        }
                        kc.frame.window.kc_front.row_action(true);
                    }
                    else{
                        row.attr({'data-kc-fullwidth' : '', 'style':''});
                    }

                },
				
				'full_height' : function( inp, el, atts ){
					
					if( atts['full_height'] == 'yes' ){
						if( atts['content_placement'] !== undefined && atts['content_placement'] == 'middle' )
							this.__row(el).attr({ 'data-kc-fullheight': 'middle-content' });
						else this.__row(el).attr({ 'data-kc-fullheight': 'true' });
					}else{
						this.__row(el).removeAttr('data-kc-fullheight');
					}
					
					// clear helper controls
					kc.detect.untarget();
					
				},
				
				'equal_height' : function( inp, el, atts ){
					
					if( atts['equal_height'] == 'yes' ){
                        if( atts['column_align'] === '' )
                            atts['column_align'] = 'top';
                        this.__row(el).attr({ 'data-kc-equalheight': 'true', 'data-kc-equalheight-align': atts['column_align'] });
                    }else{
                        this.__row(el).attr({ 'data-kc-equalheight': '', 'data-kc-equalheight-align': '' });
                    }

				},

                'column_align' : function( inp, el, atts ){

                    this.equal_height( inp, el, atts );

                },
				
				'content_placement' : function( inp, el, atts ){
					
					this.full_height( inp, el, atts );
					
				},
				
				'video_bg' : function( inp, el, atts ){
					
					var video_bg_url = atts['video_bg_url'];
					
					if( video_bg_url === undefined || video_bg_url === '' )	
						video_bg_url = 'https://www.youtube.com/watch?v=dOWFVKb2JqM';
					
					if( this.__row(el).data('kc-video-bg') == video_bg_url && atts['video_bg'] == 'yes' )
						return;
						
					this.__row(el)
							.removeClass('kc-video-bg')
							.css({'position': ''})
							.data({'kc-video-bg':''})
							.find('>.kc_wrap-video-bg')
							.remove();
							
					if( atts['video_bg'] == 'yes' ){
						
						this.__row(el)
							.addClass('kc-video-bg')
							.css({'position': 'relative'})
							.data({ 'kc-video-bg' : video_bg_url });
							
						kc.frame.window.kc_front.youtube_row_background.init();
						
					}
					
				},
				
				'video_bg_url' : function( inp, el, atts ){
					
					this.video_bg( inp, el, atts );
					
				},
				
				'container_class' : function( inp, el, atts ){
					
					return add_class( inp, el.find('>.kc-container').data({model:el.data('model')}) );
					
				},
							
				'row_class' : add_class,
				
				'_parallax' : function( inp, el, atts ){
					
					if( atts['video_bg_url'] === undefined || atts['video_bg'] !== 'yes' ){
						
						if( atts['parallax'] !== undefined && atts['parallax'] !== '' ){
					
							this.__row(el).attr({ 'data-kc-parallax': 'true' }); 
					
							if( atts['parallax_speed'] !== undefined )
								atts['parallax_speed'] = 1;
					
							this.__row(el).attr({ 'data-speed': atts['parallax_speed'] });
					
							if( atts['parallax'] == 'yes-new' ){
								
								var bg_image = ajaxurl+'?action=kc_get_thumbn&size=full&id='+atts['parallax_image'];
								this.__row(el).css({'background-image' : 'url("'+bg_image+'")'});
							}
							
							this.__row(el).kc_parallax();
							
						}else{
							this.__row(el)
								.removeAttr('data-kc-parallax')
								.removeAttr('data-parallax_background_size')
								.removeAttr('data-speed')
								.removeAttr('data-kc-bgfull')
								.css({'background-image' : ''}); 
						}
					}
					
				}
					
			},
			
			'kc_row_inner' : {
				
				'row_id' : add_id,
				
				'equal_height' : function( inp, el, atts){
					
					if( atts['equal_height'] == 'yes' )
						el.attr({ 'data-kc-equalheight': 'true', 'data-kc-row-action': 'true' });
					else el.attr({ 'data-kc-equalheight': '', 'data-kc-row-action': '' });
					
				},
				
				'row_class' : add_class,
				
				'row_class_container' : function( inp, el, atts){
					
					if( el.find('>.kc_column_inner').length > 0 ){
							
						var cont = kc.frame.$('<div class="'+atts['row_class_container']+'"></div>');
						el.append( cont );
						el.find('>.kc_column_inner').each(function(){
							cont.append(this);
						});
						
					}else el.find('>.div').attr({class: atts['row_class_container']});
					
				},

                'video_bg' : function( inp, el, atts ){

                    var video_bg_url = atts['video_bg_url'];

                    if( video_bg_url === undefined || video_bg_url === '' )
                        video_bg_url = 'https://www.youtube.com/watch?v=dOWFVKb2JqM';

                    if( el.data('kc-video-bg') == video_bg_url && atts['video_bg'] == 'yes' )
                        return;

                    el.removeClass('kc-video-bg')
                        .css({'position': ''})
                        .data({'kc-video-bg':''})
                        .find('>.kc_wrap-video-bg')
                        .remove();

                    if( atts['video_bg'] == 'yes' ){

                        el.addClass('kc-video-bg')
                            .css({'position': 'relative'})
                            .data({ 'kc-video-bg' : video_bg_url });

                        kc.frame.window.kc_front.youtube_row_background.init();

                    }

                },

                'video_bg_url' : function( inp, el, atts ){

                    this.video_bg( inp, el, atts );

                },
				
			},
			
			'kc_column' : {
				
				'col_container_class' : function( inp, el, atts ){
						
					add_class( inp, el.find('>.kc-col-container').data({model:el.data('model')}) );
					
				},
				
				'col_class' : add_class,

                'video_bg' : function( inp, el, atts ){

                    var video_bg_url = atts['video_bg_url'];

                    if( video_bg_url === undefined || video_bg_url === '' )
                        video_bg_url = 'https://www.youtube.com/watch?v=dOWFVKb2JqM';

                    if( el.data('kc-video-bg') == video_bg_url && atts['video_bg'] == 'yes' )
                        return;

                    el.removeClass('kc-video-bg')
                    .css({'position': ''})
                    .data({'kc-video-bg':''})
                    .find('>.kc_wrap-video-bg')
                    .remove();

                    if( atts['video_bg'] == 'yes' ){

                        el.addClass('kc-video-bg')
                        .css({'position': 'relative'})
                        .data({ 'kc-video-bg' : video_bg_url });

                        kc.frame.window.kc_front.youtube_row_background.init();

                    }

                },

                'video_bg_url' : function( inp, el, atts ){

                    this.video_bg( inp, el, atts );

                },
					
			},
			
			'kc_column_inner' : {
				
				'col_in_class' : add_class,
				
				'col_in_class_container' : function( inp, el, atts){
					
					return add_class( inp, el.find('>.kc_wrapper').data({model:el.data('model')}) );
					
				}
				
			},
			
			'kc_image_gallery' : {

				'wrap_class' : add_class,

				'columns' : function( input, el, atts ){
					el.find('.item-grid').removeClass().addClass('item-grid grid-'+atts['columns']);

					if( atts['type'] == 'image_masonry')
						kc.frame.window.kc_front.image_gallery.masonry();
				},

				'title' : function( input, el, atts ){
					
					var tit = el.find('>h3.image-gallery-title');
					
					if( input.value !== '' ){
						
						if( tit.length === 0 )
							el.prepend('<h3 class="image-gallery-title">'+atts['title']+'</h3>');
						else tit.html(atts['title']);
						
					}else tit.remove();
					
				},

				'click_action' : function( inp, el, atts ){

					var images = ( atts['images'] !== undefined ) ? atts['images'] : '',
						custom_links = ( atts['custom_links'] !== undefined ) ? atts['custom_links'] : '',
						click_action = ( atts['click_action'] !== undefined ) ? atts['click_action'] : '',
						custom_links_arr = [];

					if( images === '' )
						images = [];
					else
						images = images.split(',');

					if( custom_links !== '' && 'custom_link' === click_action ) {
						custom_links = custom_links.replace('/[\r\n]+/',"\n").replace('/^\n/','').replace('/\n$/','');
						custom_links_arr = custom_links.split('\n');
					}
					
					var ar_imgs = el.find('img');
					var pretty_id = parseInt(Math.random()*100000);

					kc.frame.$(ar_imgs).each( function (i) {
						var image_id = images[i];
						switch( click_action ){
							case 'large_image':

								var link_tag = kc.frame.$('<a target="_blank" href="' + ajaxurl + '?action=kc_get_thumbn&size=full&id=' + image_id  + '"></a>'),
									wrp = kc.frame.$(this).closest('.item-grid'),
									img = kc.frame.$(this).detach();

								link_tag.append(img);

								wrp.find('a').remove();
								wrp.prepend( link_tag );

								break;

							case 'lightbox':

								var link_tag = kc.frame.$('<a class="kc-image-link kc-pretty-photo" data-lightbox="kc-lightbox" rel="kc-pretty-photo['+pretty_id+']" href="' + ajaxurl + '?action=kc_get_thumbn&size=full&id=' + image_id  + '"></a>'),
									wrp = kc.frame.$(this).closest('.item-grid'),
									img = kc.frame.$(this).detach();

								link_tag.append(img);

								wrp.find('a').remove();
								wrp.prepend( link_tag );

								break;

							case 'custom_link':

								var link_tag = kc.frame.$('<a target="_blank" href="' + custom_links_arr[i]  + '"></a>'),
									wrp = kc.frame.$(this).closest('.item-grid'),
									img = kc.frame.$(this).detach();

								link_tag.append(img);

								wrp.find('a').remove();
								wrp.prepend( link_tag );

								break;
							case 'none':
								var wrp = kc.frame.$(this).closest('.item-grid'),
									img = kc.frame.$(this).detach();

								wrp.find('a').remove();
								wrp.prepend( img );

								break;
						}
					});


					kc.frame.window.kc_front.pretty_photo();


					return el;

				},
				_render : function( input, el, atts ){

					var new_el = $( kc.template('kc_image_gallery', {atts: atts } ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					if( atts['type'] == 'image_masonry')
						kc.frame.window.kc_front.image_gallery.masonry();
					
					kc.frame.window.kc_front.pretty_photo();
					
					return new_el;
				}
			},

			'kc_carousel_images' : {

				'wrap_class' : add_class,

				_render : function( input, el, atts ){

					var new_el = $( kc.template('kc_carousel_images', {atts: atts } ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					kc.frame.window.kc_front.carousel_images(new_el);

					return new_el;
				}
			},

			'kc_accordion' : {

				'class' : function( input, el ){

					var model = el.data('model'),
						args = kc.storage[model].args,
						claz = 	kc.tools.esc_attr(input.value);

					if( args.class !== undefined && args.class !== '' )
						el.removeClass(args.class);

					el.addClass(claz);

				},

				'title' : function( input, el, atts ){
					var title = el.find('.kc-accordion-title');

					if( atts['title'] == undefined ){
						title.remove();
						return;
					}

					if( !title.get(0)){
						title = kc.frame.$('<h3 class="kc-accordion-title"></h3>');
						el.prepend(title);
					}
					title.html( atts['title']);

				}

			},

			'kc_accordion_tab' : {
				
				'class' : add_class,
				
				_title : function( input, el, atts ){
					
					var title = atts['title'];
					
					if( atts['icon_option'] == 'yes' && atts['icon'] !== '' )
						title = '<i class="'+atts['icon']+'"></i> '+title;
					
					el.find('>h3.kc_accordion_header>a').html( title );
					
				}
				
			},
			
			'kc_tab' : {
				
				'class' : function( input, el ){
					
					var model = el.data('model'),
						args = kc.storage[model].args,
						claz = 	kc.tools.esc_attr(input.value);
					
					if( args.class !== undefined && args.class !== '' )
						el.removeClass(args.class);
					
					el.addClass(claz);
					
				},
				
				_title : function( input, el, atts ){
						
					el.parent().find('>ul>li.ui-tabs-active').html( kc.front.ui.process_tab_title( atts ) );
					
				}
				
			},
			
			'kc_title' : {
				
				_get : function(el, atts){
					
					var tit = el;
					
					if( el.hasClass('kc_title') && el.get(0).tagName.toLowerCase()  == atts['type'] )
						tit = el;
					else tit = el.find(atts['type']+'.kc_title');
					
					return tit;
				},

				'post_title' : function( inp, el, atts ){
					console.log(atts);
					var tit = this._get(el, atts),
						text = kc.tools.base64.decode( atts['text'] );
					if( atts['post_title'] === 'yes'){
						text = kc_post_title;
					}

					if( tit )
						tit.html( text );


				},
				
				'text' : function( inp, el, atts ){
					
					var tit = this._get(el, atts);
					
					if( tit )
						tit.html( kc.tools.base64.decode( atts['text'] ) );
					
				},
				
				'align' : function( inp, el, atts ){
					
					var tit = this._get(el, atts),
						oatts = kc.storage[el.data('model')].args;
						
					tit.removeClass('align-'+oatts['align']).addClass('align-'+atts['align']);
					
				},
				
				'class' : function( inp, el, atts ){
					
					var tit = this._get(el, atts),
						oatts = kc.storage[el.data('model')].args;
					
					tit.removeClass(oatts['class']).addClass(atts['class']);
					
				},

				_render : function( inp, el, atts ){
					
					var attributes = [], 
						classes = ['kc_title'], 
						wrp_class = [], 
						model = el.data('model'), 
						output,
						text = kc.tools.base64.decode( atts['text'] );
						tit = this._get(el, atts);
					
					if( atts['class'] !== undefined && atts['class'] !== '' )
						classes.push( atts['class'] );
						
					if( atts['align'] !== undefined && atts['align'] !== '' )
						classes.push( 'align-'+atts['align'] );
					
					attributes.push( 'class="'+classes.join(' ')+'"' );
					
					if( atts['title_wrap'] === undefined || atts['title_wrap'] != 'yes' )
						attributes.push( 'data-model="'+model+'"' );


					if( atts['post_title'] === 'yes'){
						text = kc_post_title;
					}

					output = '<'+atts['type']+' '+attributes.join(' ')+'>'+ text +'</'+atts['type']+'>';
					
					if( atts['title_wrap'] !== undefined && atts['title_wrap'] == 'yes' ){
						
						if( atts['before'] !== undefined && atts['before'] !== '' )
							output = kc.tools.base64.decode( atts['before']) + output;
						if( atts['after'] !== undefined && atts['after'] !== '' )
							output += kc.tools.base64.decode( atts['after']);
							
						if( atts['title_wrap_class'] !== undefined && atts['title_wrap_class'] !== '' )
							wrp_class.push( atts['title_wrap_class'] );
						
						if( atts['align'] !== undefined && atts['align'] !== '' )
							wrp_class.push( 'align-'+atts['align'] );
							
						output = '<div data-model="'+model+'" class="'+wrp_class.join(' ')+'">'+output+'</div>';
						
					}
					
					var new_el = kc.frame.$( output );
					
					new_el.addClass('kc-css-'+atts['_id']);
					el.after(new_el).remove();
					
					return new_el;	
					
				}
				
			},
			
			'kc_single_image' : {
				
				_img : function( el ){
					
					if( el.get(0).tagName == 'IMG' )
						return el;
					else return el.find('img');
					
				},

				'image_source' : function( inp, el, atts ){
					var id =  -1,
						url = kc_url + "/assets/images/get_start.jpg";

					if( atts['image_source'] === 'featured_image'){
						url = ajaxurl+'?action=kc_get_thumbn&type=post_featured&size=' + atts['image_size'] + '&id=' + kc_post_ID;
					}else if(atts['image_source'] === 'external_link') {
						if( typeof atts['image_external_link'] !== 'undefined' && atts['image_external_link'] !== '')
							url = atts['image_external_link'];
					}else{
						
						if(typeof atts['image'] !== 'undefined' && atts['image'] !== ''){
							id = atts['image'].replace( /[^\d]/, '' );
							url = ajaxurl+'?action=kc_get_thumbn&size=' + atts['image_size'] + '&id=' + id;
						}
					}
					this._img(el).attr('src', url);
				},
				
				'image' : function( inp, el, atts ){
					
					var id = atts['image'].replace( /[^\d]/, '' ),
						url = ajaxurl+'?action=kc_get_thumbn&size='+atts['image_size']+'&id='+id;
					this._img(el).attr({ src : url });
					
				},
				
				'image_size' : function( inp, el, atts ){
					var sizes 	= ['full', 'thumbnail', 'medium', 'large'],
						id 		= -1,
						url 	= '',
						image_size = atts['image_size'];

					if( atts['image_source'] === 'featured_image'){
						url = ajaxurl+'?action=kc_get_thumbn&type=post_featured&size=' + atts['image_size'] + '&id=' + kc_post_ID;
					}else if( atts['image_source'] === 'media_library'){
						id = atts['image'].replace( /[^\d]/, '' );

						if ( sizes.indexOf( image_size ) > -1  ) {
							url 	= ajaxurl + '?action=kc_get_thumbn&id=' + id + '&size=' + image_size ;
						}else if( image_size.indexOf('x') > 0 ){
							url 	= ajaxurl + '?action=kc_get_thumbn_size&id=' + id + '&size=' + image_size ;
						}else{
							url 	= ajaxurl + '?action=kc_get_thumbn&id=' + id + '&size=full';
						}

					}else{
						url = atts['image_external_link'];
					}
						
					this._img(el).attr({ src : url });
					
				},
				
				'image_external_link' : function( inp, el, atts ){
					if(atts['image_external_link'] !=='')
						this._img(el).attr({ src : atts['image_external_link'] });
				},
				
				'alt' : function( inp, el, atts ){
					this._img(el).attr({ alt : atts['caption'] });
				},

				'caption' : function( inp, el, atts ){

					var cap = el.find('>p.scapt');

					if( inp.value !== '' ){

						if( cap.length === 0 )
							el.append('<p class="scapt">' + atts['caption'] + '</p>');
						else
							cap.html( atts['caption'] );

					}else
						cap.remove();
				},


				'image_size_el' : function( inp, el, atts ){
					
					var size = atts['image_size_el'].split('x');
					
					if( size[0] !== undefined )
						this._img(el).attr({ width : size[0] });
						
					if( size[1] !== undefined )
						this._img(el).attr({ height : size[1] });
					
				},
				
				'image_align' : function( inp, el, atts ){
					
					el.css({ 'text-align' : atts['image_align'] });
					
				},

				'ieclass' : function( inp, el, atts ){
					add_class( inp, this._img(el).data({ model: el.data('model') }), atts );	
				},
				
				'class' : add_class,

				'overlay' : function( inp, el, atts ){

					if( atts['overlay'] == 'yes'){
						var ol = el.find('.kc-image-overlay');

						if( !ol.get(0)){
							ol = kc.frame.$('<div class="kc-image-overlay"><i></i></div>');
							if( atts['on_click_action'] !== '' ){
								el.find('a').append(ol);
							}else{
								el.find('img').after(ol);
							}
						}

						ol.find('i').attr({class: atts['icon']});
					}else{
						el.find('.kc-image-overlay').remove();
					}


				},
				
				'on_click_action' : function( inp, el, atts ){

					var id = atts['image'].replace( /[^\d]/, '' );

					if( atts['on_click_action'] !== '' ){
						
						var a = el.find('a'),
							image_full = ajaxurl+'?action=kc_get_thumbn&size=full&id='+id;

						if ( !a.get(0) ) {
							a = kc.frame.$('<a href="#"></a>');
						}

						a.attr({ rel : '' });
						a.attr({ title : atts['caption'] });

						if( atts['on_click_action'] == 'lightbox') {
							a.attr({ rel : 'prettyPhoto' });
							a.attr({ href : image_full });
							a.attr({ class : 'kc-pretty-photo' });
						}else if( atts['on_click_action'] == 'op_large_image' ){
							a.attr({ rel : '' });
							a.attr({ href : image_full });
							a.attr({ class : '' });
						}else if( atts['on_click_action'] == 'open_custom_link' ){

							var link = atts['custom_link'],
								link_title = atts['caption'],
								target='_self';

							link = link.split('|');
							if( link[0] !== undefined )
								image_full = link[0];

							if( link[1] !== undefined )
								link_title =  link[1];

							if( link[2] !== undefined )
								target= link[2];

							a.attr({ href : image_full, 'target': target, 'title': link_title });
						}

						var img = el.find('img');
						el.find('img').remove();
						a.prepend( img );
						el.prepend(a);

						kc.frame.window.kc_front.pretty_photo();

					}else{

						var new_el = el.find('img');
						el.find('a').remove();
						el.prepend(new_el);


					}
					//check overlay
					this.overlay(inp, el, atts);

					return el;
					
				}
				
				
			},
			
			'kc_icon' : {
				
				_i : function( el ){
					if( el.get(0).tagName == 'I' )
						return el;
					else return el.find('i');
				},
				
				_render : function( inp, el, atts ){
				
					var new_el = $( kc.template('kc_icon', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();
					
					return new_el;
				
				}
				
			},
			
			'kc_spacing' : {
				
				'height' : function( inp, el, atts ){

					el.height( atts['height'] );

				},
				
				'class' : add_class
				
			},

			'kc_progress_bars' : {
				'radius' : function( inp, el, atts ){
	
					kc.front.css_system.push_to( el.data('model') );
					
				},

				'wrap_class' : add_class,

				_render : function( inp, el, atts ){

					var options = atts['options'];

					delete options[0];

					atts['options'] = options;

					var new_el = $( kc.template('kc_progress_bars', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					kc.frame.window.kc_front.progress_bar.update( new_el );

					return new_el;

				}

			},

			'kc_pie_chart' : {
				
				'icon_option' : function( inp, el, atts ){

					if( atts['icon_option'] == 'yes'){
						if( atts['icon'] === undefined )
							atts['icon'] = 'fa-leaf';
						var icon = el.find('.pie_chart_icon');

						if( !icon.get(0)){
							icon = kc.frame.$('<i class="pie_chart_icon"></i>');
							el.find('.pie_chart_percent').prepend( icon );
						}

						icon.attr({ 'class': 'pie_chart_icon ' + atts['icon'] })

					}else{
						el.find('i').remove();
					}

					return el;
				},

				'wrap_class' : add_class,

				_render : function( inp, el, atts ){

					var new_el = $( kc.template('kc_pie_chart', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					kc.frame.window.kc_front.piechar.update( new_el );

					return new_el;

				}
			},

			'kc_flip_box' : {

				'wrap_class' : add_class,

				'direction' : function( inp, el, atts ){

					if( atts['direction'] === 'vertical' )
						el.addClass('flip-vertical');
					else
						el.removeClass('flip-vertical');


				},

				'_front' : function( inp, el, atts ){

					var icon = '', title = '',	button = '', link = '', url ='#', target='_self', link_title = atts['text_on_button'];

					el.find('.front-content').html('');

					if( atts['show_icon']  === 'yes' && atts['icon'] !=='' )
					{
						icon = kc.frame.$('<div class="wrap-icon"><i class="' + atts['icon'] + '"></i></div>');
						el.find('.front-content').append( icon );
					}

					if( atts['title']  !== '' && atts['title']  !== '__empty__' )
					{
						title = kc.frame.$('<h3>' + atts['title'] + '</h3>');
						el.find('.front-content').append( title );
					}


					if( atts['description']  !== undefined && atts['description']  !== '__empty__' )
					{
						el.find('.front-content').append('<p>' + kc.tools.base64.decode( atts['description'] ) + '</p>');
					}

					return el;


				},

				'_back' : function( inp, el, atts ){

					var title = '',	icon = '', button = '', link = atts['b_link'], url ='#', target='_self', link_title = atts['b_text_on_button'];

					el.find('.back .des').html('');

					if( atts['b_show_icon']  === 'yes' && atts['b_icon'] !=='' )
					{
						icon = kc.frame.$('<div class="wrap-icon"><i class="' + atts['b_icon'] + '"></i></div>');
						el.find('.back .des').append( icon );
					}

					if( atts['b_title']  !== '' && atts['b_title']  !== '__empty__' )
					{
						title = kc.frame.$('<h3>' + atts['b_title'] + '</h3>');
						el.find('.back .des').append( title );
					}


					if( atts['b_description']  !== undefined && atts['b_description']  !== '__empty__' )
					{
						el.find('.back .des').append('<p>' + kc.tools.base64.decode( atts['b_description'] ) + '</p>');
					}



					if( atts['b_show_button']  == 'yes' && atts['b_text_on_button'] !=='' )
					{
						button = kc.frame.$('<a class="button">' + atts['b_text_on_button'] + '</a>');
						if( atts['b_link'] !== undefined )
							link = atts['b_link'];
						else
							link = '||';

						link = link.split('|');

						if( link[0] !== undefined )
							url = link[0];

						if( link[1] !== undefined )
							link_title =  link[1];

						if( link[2] !== undefined )
							target= link[2];

						button.attr({ href : url, 'target': target, 'title': link_title });

						el.find('.back .des').append( button );
					}

					return el;

				},


			},

			'kc_counter_box' : {

				'wrap_class' : add_class,

				'number' : function( inp, el, atts ){

					el.find('.counterup').html( inp.value );

					kc.frame.window.kc_front.counterup();
				},

				'label' : function( inp, el, atts ){
					console.log(atts);
					var label = el.find('h4');

					if( !label.get(0) ){

						label = kc.frame.$('<h4></h4>');

						if( atts['label_above'] === 'yes')
							el.find('.counterup').before( label );
						else
							el.append( label );
					}

					if( atts['label'] == '__empty__' || atts['label'] === undefined )
						el.find('h4').remove();
					else
						label.html( inp.value );

				},

				'label_above' : function( inp, el, atts ){

					var text = el.find('h4'),
						num = el.find('.counterup');

					if( atts['label_above'] == 'yes'){

						el.find('.counterup').remove();

						el.append(num);
					}else{

						el.find('h4').remove();

						el.append(text);

					}

				},

				'icon_show' : function ( inp, el, atts ){

					if( atts['icon_show'] == 'yes'){

						var icon = el.find('i'),
							cls_icon = ( atts['icon'] !== undefined ) ? atts['icon'] : 'fa-leaf';;

						if( !icon.get(0) ){
							icon = kc.frame.$('<i></i>');
							el.prepend( icon );
						}

						icon.attr({ class : 'element-icon ' + cls_icon });

					}else{
						el.find('i').remove();
					}

					return el;
				},
			},

			'kc_coundown_timer' : {

				'title' : function( inp, el, atts ){

					var title = el.find('h3');

					if( !title.get(0) ){

						title = kc.frame.$('<h3></h3>');

						el.prepend( title );

					}

					title.html( inp.value );

				},

				'timer_style' : function( inp, el, atts ){

					var new_el = $( kc.template('kc_coundown_timer', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					kc.frame.window.kc_front.countdown_timer();

					return new_el;
				},

				'wrap_class' : add_class,

			},

			'kc_button' : {

				'onclick' : function( inp, el, atts ){

					el.find('.kc_button').attr({onclick: atts['onclick'] });

				},
				'link' : function( inp, el, atts ){

					var link = inp.value;

					link = link.split('|');
					
					el.find('.kc_button').attr({href: link[0], title: link[1], target: link[2] });
					
				},
				
				'wrap_class' : add_class,

				'ex_class' : function ( inp, el, atts ){
					return add_class( inp, el.find('>.kc_button').data({model:el.data('model')}) );
				},

				'show_icon' : function ( inp, el, atts ){

					if( atts['show_icon'] === 'yes'){

						var icon = el.find('i');

						el.find('.kc_button').html( atts['text_title'] );

						if( !icon.get(0) ){
							icon = kc.frame.$('<i class="' + atts['icon'] + '"></i>');
						}

						if( atts['icon_position'] === 'right' )
							el.find('.kc_button').append(' ').append( icon );
						else
							el.find('.kc_button').prepend(' ').prepend( icon );

					}else{
						el.find('.kc_button').html( atts['text_title'] );
					}

					return el;

				},

				'icon' : function ( inp, el, atts ) {

					el.find('.kc_button i').attr({class: atts['icon']} );

				}

			},
			
			'kc_column_text' : {
				
				'content' : function( inp, el, atts ){
					
					el.html( inp.value );

				},
				
				'class' : add_class
					
			},


			/** Pro version**/
            'kc_box_alert' : {

                'class' : add_class,

                'title' : function( inp, el, atts ){
                    //remove all html
                    var wp      = el.find('.message-box-wrap');

                    wp.html('');

                    if( !wp.find('i').get(0) && atts['icon'] !== undefined ){

                        var icon = kc.frame.$('<i></i>');

                        icon.attr({ 'class' : atts['icon'] });

                        wp.append(icon);
                    }

                    if( atts['icon'] !== undefined && atts['show_button'] == 'yes' ){

                        var button = kc.frame.$('<button class="kc-close-but">close</button>');

                        wp.append( button );
                    }

                    wp.append( kc.tools.base64.decode( atts['title'] ) );

                    return el;
                },
            },

			'kc_divider' : {

                'class' : add_class,

				'style' : function( inp, el, atts ){

					var new_el = $( kc.template('kc_divider', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					return new_el;
				},
			},

            'kc_faqs' : {

			    'custom_class' : add_class,

			    _render : load_template,

            },

			'kc_image_fadein' : {

				'wrap_class' : add_class,

				_render : function( inp, el, atts ){

					var new_el = $( kc.template('kc_image_fadein', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					kc.frame.window.kc_front.image_fade();

					return new_el;
				},

				'title' : function( inp, el, atts ){

					var title = el.find('h3');

					if( !title.get(0) ){

						title = kc.frame.$('<h3></h3>');

						el.prepend( title );
					}

					title.html( inp.value );
				},

				'force_size' : function( inp, el, atts ){

					var _images = el.find('.image_fadein').data('images'),
						images = [];

					images = _images.split(',');

					el.find('img').each( function (index){

						if( atts['force_size'] == 'yes'){

							var width 		= ( atts['width'] !== undefined )? atts['width'] :'250',
								height 		= ( atts['height'] !== undefined )? atts['height'] :'250',
								position 	= ( atts['position'] !== undefined )? atts['position'] :'c',
								url = ajaxurl+'?action=kc_get_thumbn_size&size='+ width +'x'+ height +'x'+ position +'&id=' + images[index];

						}else{

							var url = ajaxurl+'?action=kc_get_thumbn&size=full&id=' + images[index];

						}

						$(this).attr({src: url} );

					});

					return el;
				}

			},

			'kc_tooltip' : {
                'custom_class' : add_class,

                _render : function( inp, el, atts ){

                    var new_el = $( kc.template('kc_tooltip', { atts: atts} ) );

                    new_el.attr({ 'data-model': el.data('model') });

					if(atts['layout'] == 2){
						var image = atts['image'],
							img_link = '';

						if( image > 0){

							image = image.replace( /[^\d]/, '' );

							if ( atts['img_size'] !== 'full'  ) {
								img_link = ajaxurl + '?action=kc_get_thumbn_size&id=' + image + '&size=' + atts['img_size'] ;
							} else {
								img_link =  ajaxurl + '?action=kc_get_thumbn&size=full&id=' + image;
							}

						}else{
							img_link = kc_url + "/assets/images/get_start_s.jpg";
						}

						var newImg = new Image();

						newImg.onload = function (){
							kc.frame.window.kc_front.tooltips();
						}

						newImg.src = img_link;

					}else{
						kc.frame.window.kc_front.tooltips();
					}

					el.after( new_el ).remove();

                    return new_el;
                },
			},


			'kc_interactive_banner' : {

				'custom_class' : add_class,

				_render : function( inp, el, atts ){

					var new_el = $( kc.template('kc_interactive_banner', { atts: atts} ) );

					new_el.attr({ 'data-model': el.data('model') });

					el.after( new_el ).remove();

					return new_el;
				},

			},

			'kc_creative_button' : {

				'custom_class' : add_class,

				'title' : function( inp, el, atts ){

					el.find('.creative_title').html( atts['title'] );

				},

				'icon_show' : function ( inp, el, atts ){

					if( atts['icon_show'] == 'yes'){

						var cls_icon = ( atts['icon'] !== undefined ) ? atts['icon'] : 'fa-leaf',
							position = ( atts['icon_float'] !== undefined ) ? atts['icon_float'] : 'before',
							link_tag = el.find('a');

						el.find('.creative_icon').remove();

						var icon_wrp = kc.frame.$('<span class="creative_icon"><i></i></span>');

						if( position === 'before'){
							icon_wrp.addClass('creative_icon_left');
							link_tag.prepend( icon_wrp );
						}
						else
						{
							icon_wrp.addClass('creative_icon_right');
							link_tag.append( icon_wrp );
						}


						icon_wrp.find('i').attr({ class : cls_icon });

					}else{
						el.find('.creative_icon').remove();
					}

					return el;
				},

				'link' : function( inp, el, atts ){

					var link = inp.value;

					link = link.split('|');

					el.find('a').attr({href: link[0], title: link[1], target: link[2] });

				},
			},


			'kc_dropcaps' : {

				'desc' 	: function( inp, el, atts ){

					var text = kc.tools.base64.decode( atts['desc'] );

					if( text !== '<p><br data-mce-bogus="1"></p>'){
						text = text.replace(/^(<[a-zA-Z\s\d=\'\"]+>)(\s*[&nbsp;]*)*([a-zA-Z\d]{1})|^(\s*[&nbsp;]*)*([a-zA-Z\d]{1})|^(<[a-zA-Z\s\d=\'\"]+>)(\s*[&nbsp;]*)*([^\x00-\x7F]{1})|^(\s*[&nbsp;]*)*([^\x00-\x7F]{1})/i, '$1<span class="dropcaps-text">$3$5$8$10</span>');

						el.html(text);

						return el;

					}

				},

				'custom_class' : add_class,
			},

			'kc_call_to_action' : {

                _render : function( inp, el, atts ){
                    var new_el = $( kc.template('kc_call_to_action', { atts: atts} ) );
                    new_el.attr({ 'data-model': el.data('model') });
                    el.after( new_el ).remove();
                    return new_el;
                },

				'custom_class' : add_class,
			},

			'kc_post_type_list' : {

				'title' : function( inp, el, atts ){
					var title = el.find('h3.list-post-title');

					if( atts['title'] === undefined || atts['title'] === '__empty__' ){
						el.find('h3.list-post-title').remove();
						return el;
					}


					if( !title.get(0) ){
						title = kc.frame.$('<h3 class="list-post-title"></h3>');
						el.prepend(title);
					}

					title.html( atts['title'] );

					return el;
				},

				'thumbnail' : load_template,

				'show_button' : load_template,

				'readmore_text' : function( inp, el, atts ){
					el.find('.read-more').html( atts['readmore_text']);
				},

				'wrap_class' : add_class,
			},

			'kc_image_hover_effects' : {

				'custom_class' : add_class,

				_render : function( inp, el, atts ){
					var new_el = $( kc.template('kc_image_hover_effects', { atts: atts} ) );
					new_el.attr({ 'data-model': el.data('model') });
					el.after( new_el ).remove();
					kc.frame.window.kc_front.pretty_photo();
					return new_el;
				}
			},

			'kc_feature_box' : {

				'custom_class' : add_class,

				_render : function( inp, el, atts ){
					var new_el = $( kc.template('kc_feature_box', { atts: atts} ) );
					new_el.attr({ 'data-model': el.data('model') });
					el.after( new_el ).remove();
					return new_el;
				}
			},


			'kc_team' : {

				'custom_class' : add_class,

				_render : function( inp, el, atts ){
					var new_el = $( kc.template('kc_team', { atts: atts} ) );
					new_el.attr({ 'data-model': el.data('model') });
					el.after( new_el ).remove();
					return new_el;
				}
			},

			'kc_pricing' : {

				'custom_class' : add_class,
				_render : function( inp, el, atts ){
					var new_el = $( kc.template('kc_pricing', { atts: atts} ) );
					new_el.attr({ 'data-model': el.data('model') });
					el.after( new_el ).remove();
					return new_el;
				}
			},
            'kc_multi_icons' : {

                'custom_class' : add_class,

                _render : function( inp, el, atts ){

                    var icons = atts['icons'];

                    delete icons[0];

                    atts['icons'] = icons;

                    var new_el = $( kc.template('kc_multi_icons', { atts: atts} ) );
                    new_el.attr({ 'data-model': el.data('model') });
                    el.after( new_el ).remove();
                    return new_el;
                }
            },
			'kc_testimonial' : {

				'custom_class' : add_class,

				_render : function( inp, el, atts ){
					var new_el = $( kc.template('kc_testimonial', { atts: atts} ) );
					new_el.attr({ 'data-model': el.data('model') });
					el.after( new_el ).remove();
					return new_el;
				}
			},

			'kc_blog_posts' : {

				'class' : add_class,

				'layout' : load_template,
			},
			
		};
	
	// Use same callback for params
	do_same( 'kc_tab', '_title', ['title', 'icon', 'icon_option', 'advanced', 'adv_icon', 'adv_title', 'adv_image'] );
	do_same( 'kc_accordion_tab', '_title', ['title', 'icon', 'icon_option'] );
	do_same( 'kc_row', '_parallax', ['parallax', 'parallax_image'] );
	do_same( 'kc_title', '_render', ['type', 'title_wrap', 'before', 'after', 'title_wrap_class'] );
	do_same( 'kc_icon', '_render', ['icon', 'class', 'icon_wrap_class', 'link', 'use_link'] );
	do_same( 'kc_button', 'show_icon', ['text_title', 'icon_position'] );
	do_same( 'kc_flip_box', '_front', ['text_on_button', 'title', 'link', 'description', 'show_button', 'show_icon', 'icon'] );
	do_same( 'kc_flip_box', '_back', ['b_text_on_button', 'b_title', 'b_link', 'b_description', 'b_show_button', 'b_show_icon', 'b_icon'] );
	do_same( 'kc_image_gallery', '_render', ['images', 'type', 'slider_width', 'image_size', 'navigation', 'pagination', 'auto_rotate', 'columns'] );
	do_same( 'kc_carousel_images', '_render', ['images', 'image_size', 'onclick', 'show_thumb', 'items_number', 'speed', 'delay', 'num_thumb', 'auto_play', 'auto_height', 'progress_bar', 'navigation', 'pagination', 'nav_style'] );
	do_same( 'kc_counter_box', 'icon_show', ['icon_show', 'icon'] );
	do_same( 'kc_coundown_timer', 'timer_style', ['datetime', 'custom_template'] );
	do_same( 'kc_box_alert', 'title', ['icon', 'show_button'] );
	do_same( 'kc_divider', 'style', ['icon', 'line_text'] );
	do_same( 'kc_pie_chart', '_render', ['percent', 'linewidth', 'size', 'auto_width', 'rounded_corners_bar', 'barcolor', 'trackcolor'] );
	do_same( 'kc_pie_chart', 'icon_option', ['icon'] );
	do_same( 'kc_progress_bars', '_render', ['style', 'speed', 'options'] );
	do_same( 'kc_faqs', '_render', ['amount', 'category', 'order'] );
	do_same( 'kc_creative_button', 'icon_show', ['icon', 'icon_float'] );
	do_same( 'kc_single_image', 'overlay', ['icon'] );
	do_same( 'kc_single_image', 'on_click_action', ['custom_link'] );
	do_same( 'kc_call_to_action', '_render', ['layout', 'title', 'desc', 'button_show', 'button_link', 'button_text', 'icon_show', 'icon'] );
	do_same( 'kc_image_hover_effects', '_render', ['layout', 'image', 'img_size', 'event_click', 'custom_link', 'title', 'desc', 'button_text', 'button_link', 'icon_picker'] );
	do_same( 'kc_tooltip', '_render', ['layout', 'position', 'icon', 'position', 'text_tooltip', 'image', 'img_size', 'button_text', 'button_link'] );
	do_same( 'kc_feature_box', '_render', ['layout', 'title', 'position', 'image', 'show_icon', 'icon', 'show_button', 'button_text', 'button_link', 'show_box_hover', 'desc'] );
	do_same( 'kc_testimonial', '_render', ['layout', 'title', 'position', 'image', 'img_size', 'desc'] );
	do_same( 'kc_team', '_render', ['layout', 'title', 'subtitle', 'desc', 'show_button', 'button_text', 'button_link', 'image', 'img_size'] );
	do_same( 'kc_pricing', '_render', ['subtitle', 'layout', 'show_icon_header', 'icon_header', 'title', 'price', 'currency', 'show_on_top', 'duration', 'desc', 'show_button', 'button_text', 'button_link', 'show_icon', 'icon'] );
	do_same( 'kc_interactive_banner', '_render', ['layout', 'banner_img', 'img_size', 'text_title', 'desc', 'link'] );
	do_same( 'kc_image_fadein', '_render', ['images', 'transition', 'delay'] );
	do_same( 'kc_image_fadein', 'force_size', ['width', 'height', 'position'] );
	do_same( 'kc_multi_icons', '_render', ['icons'] );

	// Register changes callback
	kc.front.live_changes( args );

} )( jQuery );
