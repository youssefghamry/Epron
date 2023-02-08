var theme = (function($) {

    "use strict";

    /* Run scripts
     -------------------------------- */

    // If document is ready VJS
    document.addEventListener("DOMContentLoaded", function() {
        theme.init($);
    });

    // Document Loader
    document.body.onload = function(){
        setTimeout(function() {
            if ( document.getElementById('loader') ) {
                var preloader = document.getElementById('loader');
                if ( ! preloader.classList.contains('done') ) {
                    preloader.classList.add('done');
                }
            }
            // the DOM was loaded
            // Init scripts requiring the entire document to be loaded
        }, 1000)
    }

    return {

        /* Init
         -------------------------------- */
        init: function() {
           
            this.front_editor();
            this.check_mobile();
            this.prototypes();
            this.redirect();
            this.nav.init();
            this.flex_grid.init();
            this.masonry.init();
            this.ajax__posts_slider();
            this.ajax__posts_loader.init();
            this.window_events.init();
            this.social_players.vimeo();
            this.social_players.youtube();
            this.plugins.reset();
            this.plugins.ThumbSlider();
            this.plugins.Tooltip();
            this.plugins.ResIframe();
            this.plugins.Lazy();
            this.plugins.theiaStickySidebar();
            this.plugins.disqus();
        },

        ajax__posts_loaded: function(container) {
            this.redirect();
            this.social_players.vimeo();
            this.social_players.youtube();
            this.plugins.ThumbSlider();
            this.plugins.Tooltip();

            /* Fire Event */
            $.event.trigger({
                type: "AjaxPostsLoaded",
                wrapper: container
            });
        },

        /* ==================================================
          Front Editor Mode
        ================================================== */
        front_editor: function() {
             if (window.location.href.indexOf('kc_action=live-editor') > -1) {
                $('body').addClass('is-front-editor');
            }
        },

        /* ==================================================
          Redirect
        ================================================== */
        redirect: function() {
            if ( document.getElementById('loader') ) {
                var loader = document.getElementById('loader');
                var self_links = document.querySelectorAll('[href]');
                var window_url = window.location;
                

                for ( var link of self_links ) {
                    link.addEventListener('click', function(event) {
                        var show_loader = true;
                        var href = this.getAttribute('href');
                        var target = this.target;
                        var url = '#';
                        var filename = '';
                        var url_test = href.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
                        if ( url_test !== null ) {
                            filename = (href.split('\\').pop().split('/').pop().split('.'))[0];
                            url = new URL( href );
                        }
                        if ( url.host !== window_url.host || target === '_blank' || filename !== '' ) {
                            show_loader = false;
                        } else if ( url.pathname === window_url.pathname ) {
                            show_loader = false;
                        } else if ( url.hash !== window_url.hash ) {
                            show_loader = false;
                        }

                        // console.log(url.hash + ' < - HASH - > ' + window_url.hash );
                        // console.log(url.host + ' < - HOST - > ' + window_url.host );
                        if ( show_loader === true ) {
                            event.preventDefault();
                            // Hide loader
                            loader.querySelector('.content__loader').style.display = 'none';
                            loader.classList.remove('done');
                            setTimeout(function() {
                               window.location.href = href;
                            }, 500)
                        }

                    })
                }
            }
        },

        /* ==================================================
          Check Mobile Browser
        ================================================== */
        check_mobile: function() {

            if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i)) {
                $('body').addClass('is-mobile');
            } 
        },

        /* ==================================================
          Navigation 
        ================================================== */
        nav: {
            init: function() {
                theme.nav.nav__top();
                theme.nav.nav__responsive();
                theme.nav.nav__hash();
                theme.nav.nav__refresh();
            },
            nav__top: function() {

                /*  Create top navigation */
                $(document).on('mouseenter', '.nav-horizontal ul li', function() {
                    var $this = $(this)
                      , $sub = $this.children('ul')
                      , t = this;

                    var timer = $this.data('timer');
                    if (timer)
                        clearTimeout(timer);
                    $this.data('showTimer', setTimeout(function() {

                        $sub.css('display', 'block');
                        anime({
                            targets: t.querySelector('ul'),
                            translateY: 20,
                            opacity: {
                                value: 1,
                                delay: 0,
                                duration: 300,
                                easing: 'easeInQuad',
                            },
                            translateY: {
                                value: 0,
                                delay: 0,
                                duration: 300,
                                easing: 'easeOutQuad',
                            }
                        });
                        if ($sub.length) {
                            $this.addClass('active');
                            var elm = $('ul:first', t)
                              , off = elm.offset()
                              , l = off.left + 20
                              , w = elm.width()
                              , docH = $('body').height()
                              , docW = $('body').width();

                            var isEntirelyVisible = (l + w <= docW);
                            if (!$this.hasClass('super-menu')) {
                                if (!isEntirelyVisible) {
                                    $sub.addClass('edge');
                                } else {
                                    $sub.removeClass('edge');
                                }
                            }
                        }

                    }, 50));

                }).on('mouseleave', '.nav-horizontal ul li', function() {
                    var $t = $(this)
                      , t = this;

                    var showTimer = $t.data('showTimer');
                    if (showTimer)
                        clearTimeout(showTimer);

                    $t.data('timer', setTimeout(function() {
                        anime({
                            targets: t.querySelector('ul'),
                            translateY: 0,
                            opacity: {
                                value: 0,
                                delay: 0,
                                duration: 200,
                                easing: 'easeInQuad',
                            },
                            translateY: {
                                value: 20,
                                delay: 0,
                                duration: 300,
                                easing: 'easeOutQuad',
                            },
                            complete: function() {
                                $t.removeClass('active').children('ul').stop(true, true).removeClass('show-list edge').css('display', 'none');

                            }
                        });

                    }, 500));

                });
            },

            nav__responsive: function() {
                $('#nav-sidebar .menu-item-has-children > a').each(function() {
                    $(this).after('<span class="submenu-trigger"></span>');
                });
                $('#nav-sidebar > ul > li').addClass('first-child');
                $('#nav-sidebar > ul:last-child').append('<li class="nav-end"></li>');
                $('#nav-sidebar .submenu-trigger, #nav-sidebar .menu-item-has-children > a[href="#"]').on('click', function(e) {
                    e.preventDefault();
                    var li = $(this).closest('li')
                      , main_index = $(this).parents('.first-child').index();
                    $('#nav-sidebar > ul > li:not(:eq(' + main_index + ')) ul:visible').slideUp();
                    $('#nav-sidebar > ul > li:not(:eq(' + main_index + ')) li, #nav-sidebar > ul > li:not(:eq(' + main_index + '))').removeClass('opened');
                    li.toggleClass('opened').find(' > ul').slideToggle(400);
                });

                /* Menu Trigger */
                $('.responsive-trigger').on('click', function(e) {
                    e.preventDefault();
                    $('body').addClass('slidebar-visible');
                });
                $('#slidebar-close, #slidebar-layer').on('click', function(e) {
                    e.preventDefault();
                    $('body').removeClass('slidebar-visible');
                });

                /* Close Slidebar after click on hash anchor */
                $(document).on('click', '.slidebar-content a[href*=\\#]', function(e) {
                    if ($(this).attr('href') !== '#') {
                        $('body').removeClass('slidebar-visible');
                    }
                });
            },

            nav__hash: function() {

                var target_hash = location.hash;
                var offset = parseInt($('.header').css('height'), 10);

                if (target_hash !== '' && $(target_hash).length) {
                    var scroll_offset = $(target_hash).offset().top + offset;
                    $('html, body').animate({
                        scrollTop: scroll_offset
                    }, 900);
                }

                $(document).on('click', '#nav-main a[href*=\\#], #slidemenu a[href*=\\#], #slidebar-content a[href*=\\#]', function(e) {
                    var that = $(this);
                    var url = that.attr('href');
                    var target_hash = location.hash;
                    if (that.attr('href') !== '#') {

                        var hash = url.split('#')[1];

                        if (hash) {

                            hash = $(this).attr('href').replace(/^.*?#/, '');
                            hash = '#' + hash;

                            url = url.replace(hash, '');
                            offset = $(this).data('offset');
                            if (offset === undefined || offset === '') {

                                offset = parseInt($('.header').css('height'), 10);
                                offset = -(offset);
                            }
                        } else {
                            hash = '';
                        }

                        if (url === '') {
                            url = ajax_vars.home_url + '/';
                        }

                        if (url !== window.location.href.split('#')[0]) {
                            window.location.href = url + hash;
                        } else {
                            if (hash !== '' && hash !== '#') {
                                var scroll_offset = $(hash).offset().top + offset;
                                $('html, body').animate({
                                    scrollTop: scroll_offset
                                }, 900);
                            }
                        }
                    }
                    e.preventDefault();
                });
            },

            nav__refresh: function() {
                $('#nav-main a').removeClass('active');
            }

        },

         /* ==================================================
          Animations
        ================================================== */

        animfx : {

            fx : function( items, fx ) {

                var effect = theme.animfx.effects[fx],
                    anime_opts = effect.opts,
                    anim_obj = effect.opts;

                anim_obj.targets = items;

                if( effect.perspective != undefined ) {
                    [].slice.call(items).forEach(function(item) { 
                        item.parentNode.style.WebkitPerspective = item.parentNode.style.perspective = effect.perspective + 'px';
                    });
                }
                
                if( effect.origin != undefined ) {
                    [].slice.call(items).forEach(function(item) { 
                        item.style.WebkitTransformOrigin = item.style.transformOrigin = effect.origin;
                    });
                }

                return anim_obj;

            },

            effects : {

                // Hapi
                hapi : {
                    opts : {
                        duration: function(t,i) {
                        return 600 + i*75;
                        },
                        easing: 'easeOutExpo',
                        delay: function(t,i) {
                            return i*50;
                        },
                        opacity: {
                            value: [0,1],
                            easing: 'linear'
                        },
                        scale: [0,1]    
                    }
                },

                // Amun
                amun : {
                    opts : {
                        duration: function(t,i) {
                            return 500 + i*50;
                        },
                        easing: 'easeOutExpo',
                        delay: function(t,i) {
                            return i * 20;
                        },
                        opacity: {
                            value: [0,1],
                            duration: function(t,i) {
                                return 250 + i*50;
                            },
                            easing: 'linear'
                        },
                        translateY: [400,0]
                    }
                   
                },

                // Kek
                kek : {
                    opts : {
                        duration: 800,
                        easing: [0.1,1,0.3,1],
                        delay: function(t,i) {
                            return i * 20;
                        },
                        opacity: {
                            value: [0,1],
                            duration: 600,
                            easing: 'linear'
                        },
                        translateX: [-500,0],
                        rotateZ: [15,0]
                    }
                    
                },
                // Montu
                montu : {
                    perspective: 800,
                    origin: '50% 0%',
                    opts : {
                        duration: 1500,
                        elasticity: 400,
                        delay: function(t,i) {
                            return i*75;
                        },
                        opacity: {
                            value: [0,1],
                            duration: 1000,
                            easing: 'linear'
                        },
                        rotateX: [-90,0]
                    }
                    
                },

                // Elastic
                elastic : {
                    origin: '50% 0%',
                    opts: {
                        duration: 500,
                        easing: 'easeOutBack',
                        delay: function(t,i) {
                            return i * 100;
                        },
                        opacity: {
                            value: [0,1],
                            easing: 'linear'
                        },
                        translateY: [400,0],
                        scaleY: [
                            {value: [3,0.6], delay: function(t,i) {return i * 100 + 120;}, duration: 300, easing: 'easeOutExpo'},
                            {value: [0.6,1], duration: 1400, easing: 'easeOutElastic'}
                        ],
                        scaleX: [
                            {value: [0.9,1.05], delay: function(t,i) {return i * 100 + 120;}, duration: 300, easing: 'easeOutExpo'},
                            {value: [1.05,1], duration: 1400, easing: 'easeOutElastic'}
                        ]
                    }
                },
            }

        },


        /* ==================================================
          Grids
        ================================================== */

        /* Flex Grid
         -------------------------------- */
        flex_grid : {

            init : function() {

                var grids = document.querySelectorAll('.flex-grid');

                if ( ! document.body.classList.contains('is-loader') ) {
                    Array.prototype.forEach.call(grids, function(el, i){
                        imagesLoaded( grids[i], function(instance) {
                            grids[i].classList.add('loaded');
                            theme.flex_grid.show_items(grids);
                        });
                         
                    });

                } else {
                    theme.flex_grid.show_items(grids);
                }

            },

            show_items : function(grids,sel = '.flex-item.new-item') {

                Array.prototype.forEach.call(grids, function(el, i){

                    if ( grids[i].classList.contains('anim-grid') ) {
                        var items = grids[i].querySelectorAll(sel),
                            effect = grids[i].getAttribute('data-anim-effect'),
                            anim_obj;

                        if (effect === null) {
                            effect = 'montu';
                        } 
                        anim_obj = theme.animfx.fx(items, effect);
                        anime(anim_obj);
                    }
                });

            },

        },

        /* Masonry
         -------------------------------- */
        masonry : {

            init : function() {

                var grids = document.querySelectorAll('.masonry-grid');

                window.addEventListener('resize', function() { theme.masonry.resize_grid(grids) } );

                if ( ! document.body.classList.contains('is-loader') ) {
                    Array.prototype.forEach.call(grids, function(el, i){
                        imagesLoaded( grids[i], function(instance) {
                            grids[i].classList.add('loaded');
                            theme.masonry.resize_grid(grids);
                            theme.masonry.show_items(grids);
                        });
                         
                    });

                } else {
                    theme.masonry.resize_grid(grids);
                    theme.masonry.show_items(grids);
                }
               
            },

            resize_item : function(grid, item) {
                var
                    rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap')),
                    rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows')),
                    rowSpan = Math.ceil((item.querySelector('.masonry-content').getBoundingClientRect().height+rowGap)/(rowHeight+rowGap));

                item.style.gridRowEnd = 'span '+rowSpan;

            },

            show_items : function(grids,sel = '.masonry-brick.new-item .masonry-content') {

                Array.prototype.forEach.call(grids, function(el, i){
                    if ( grids[i].classList.contains('anim-grid') ) {
                        var items = grids[i].querySelectorAll(sel),
                            effect = grids[i].getAttribute('data-anim-effect'),
                            anim_obj;

                        if (effect === null) {
                            effect = 'montu';
                        } 
                     
                        anim_obj = theme.animfx.fx(items, effect);
                       
                        anime(anim_obj);
                    }
                });

            },

            resize_grid : function(grids) {
                for (var i = 0; i < grids.length; ++i) { 
                    var items = grids[i].getElementsByClassName('masonry-brick');
                    for (var j = 0; j < items.length; ++j) {
                        theme.masonry.resize_item(grids[i], items[j]);
                    };

                };

            },

        },


        /* ==================================================
          Ajax Posts Slider 
        ================================================== */
        ajax__posts_slider: function() {
            if ($('.ajax-posts-slider').length) {

                $(document).on('click', '.arrow-nav:not(.disabled)', function(event) {

                    event.preventDefault();

                    var $this = $(this), $slider = $(this).parents('.ajax-posts-slider'), $container, direction = 'right', curr_page = parseInt($slider.attr('data-pagenum')), container_height, obj;

                    /* Check loading */
                    if ($slider.hasClass('loading')) {
                        return;
                    }

                    /* Check obj */
                    try {
                        obj = $.parseJSON($slider.attr('data-obj'));
                    } catch (err) {
                        return;
                    }

                    /* Left or right */
                    if ($this.hasClass('left')) {
                        direction = 'left';
                    }

                    /* Set page nr */
                    if (direction === 'left' && curr_page !== 1) {
                        curr_page--;
                        $slider.addClass('anim-slide-from-left').removeClass('anim-slide-from-right');
                    }
                    if (direction === 'right' && !$slider.hasClass('end')) {
                        curr_page++;
                        $slider.addClass('anim-slide-from-right').removeClass('anim-slide-from-left');
                    }

                    /* Grid */
                    $container = $slider.find('.ajax-posts-slider-inner');

                    /* Pagenum */
                    obj['pagenum'] = curr_page;

                    /* Classes */
                    $slider.addClass('loading');

                    /* Set min height */
                    container_height = $container.outerHeight();
                    $container.css('min-height', container_height + 'px');

                    /* Ajax */
                    $.ajax({
                        url: ajax_action.ajaxurl,
                        type: 'post',
                        data: {
                            action: obj['action'],
                            ajax_nonce: ajax_action.ajax_nonce,
                            obj: obj
                        },
                        success: function(result) {

                            if (result === 'Busted!') {
                                location.reload();
                                return false;
                            }
                            var $result = $(result);
                            $result.imagesLoaded({
                                background: false
                            }, function() {

                                $slider.attr('data-pagenum', obj['pagenum']);
                                $slider.removeClass('loading');
                                $container.html($result);
                                $container.css('min-height', '0').find('.ajax-item').addClass('new-item');
                                // Callback function
                                theme.ajax__posts_loaded($container);

                                // Show Posts
                                setTimeout(function() {
                                    $container.find('.ajax-item').addClass('is-active')
                                }, 20);
                                if ($container.find('.ajax-item.finished').length) {
                                    $slider.addClass('end');
                                } else {
                                    $slider.removeClass('end');
                                }
                                if (curr_page !== 1) {
                                    $slider.find('.arrow-nav.left').removeClass('disabled');
                                } else {
                                    $slider.find('.arrow-nav.left').addClass('disabled');
                                }
                                if ($slider.hasClass('end')) {
                                    $slider.find('.arrow-nav.right').addClass('disabled');
                                } else {
                                    $slider.find('.arrow-nav.right').removeClass('disabled');
                                }
                            });
                        },
                        error: function(request, status, error) {
                            $slider.attr('data-pagenum', '2');
                            $slider.removeClass('loading');
                            $container.css('height', '100%');
                        }
                    });

                });
            }
        },


        /* ==================================================
          Ajax Load Posts 
        ================================================== */
        ajax__posts_loader: {

            init: function() {

                $(document).on('scroll', theme.ajax__posts_loader.infinity);
                $(document).on('click', '.load-more', theme.ajax__posts_loader.load_more);
                $(document).on('click', '.ajax-filters ul a', theme.ajax__posts_loader.filter);
                $(document).on('click', '.filter-label', theme.ajax__posts_loader.show_filters);
            },

            show_filters: function(event) {
                var $ajax_filters = $(this).parents('.ajax-filters');
                if ($ajax_filters.hasClass('hide-filters')) {
                    $ajax_filters.removeClass('hide-filters');
                } else {
                    $ajax_filters.addClass('hide-filters');
                }
                event.preventDefault();
            },

            infinity: function(event) {
                if ($('.infinite-load').length && !$('.infinite-load').hasClass('last-page')) {
                    if ($('.infinite-load').visible(true)) {
                        var $ajax_block = $('.infinite-load').parents('.ajax-grid-block');

                        if ($ajax_block.length && !$ajax_block.hasClass('last-page')) {
                            $ajax_block.addClass('loading-infinite');
                            theme.ajax__posts_loader.get($ajax_block);
                        }
                    }
                }

            },

            load_more: function(event) {
                var $ajax_block = $(this).parents('.ajax-grid-block');

                if ($ajax_block.length) {
                    theme.ajax__posts_loader.get($ajax_block);
                }
                event.preventDefault();
            },

            filter: function(event) {

                event.preventDefault();
                var $this = $(this)
                  , $filter = $this.parents('ul')
                  , tax_name = $filter.attr('data-tax-name')
                  , $ajax_block = $this.parents('.ajax-grid-block');


                if (!$ajax_block.length || $ajax_block.data('loading') === true) {
                    return;
                }

                var $filters = $this.parents('.ajax-filters'), filter_data = $.parseJSON($ajax_block.attr('data-filter')), tax_data = null, tax_a, tax_type = 'ids', block_h, saved_taxes = null, tax = [];

                tax[tax_name] = $(this).attr('data-category_ids');

                tax_data = filter_data['taxonomies'][tax_name];

                /* Slugs or ids */
                if (tax_data['ids'] !== '') {
                    tax_type = 'ids';
                } else if (tax_data['slugs'] !== '') {
                    tax_type = 'slugs';
                }

                if (filter_data["0"] !== undefined) {
                    filter_data = filter_data["0"];
                }

                if (tax[tax_name] !== 'all') {

                    $filter.find('li .filter-reset').removeClass('is-active');

                    if ($filters.hasClass('filter-sel-single')) {
                        if (!$this.hasClass('is-active')) {

                            $filter.find('.is-active').removeClass('is-active');
                            $this.addClass('is-active');
                            tax[tax_name] = $('.is-active', $filter).map(function() {
                                return $(this).attr('data-category_' + tax_type);
                            }).get();
                        }

                    } else {
                        if ($this.hasClass('is-active')) {
                            $this.removeClass('is-active');
                            tax[tax_name] = $('.is-active', $filter).map(function() {
                                return $(this).attr('data-category_' + tax_type);
                            }).get();

                            /* Reset */
                            if ($filter.find('.is-active').length <= 0) {
                                $this = $filter.find('li .filter-reset');
                                tax[tax_name] = 'all';
                            }
                        } else {
                            $this.addClass('is-active');
                            tax[tax_name] = $('.is-active', $filter).map(function() {
                                return $(this).attr('data-category_' + tax_type);
                            }).get();
                        }
                    }

                }

                /* Get slugs for event type */
                if (filter_data['event_type'] !== undefined && tax_name.indexOf('_event_type') > -1) {
                    if ($filter.find('.is-active').length > 1 || $(this).attr('data-category_slugs') === 'all') {
                        filter_data['event_type'] = 'all'
                    } else {
                        filter_data['event_type'] = $filter.find('.is-active').attr('data-category_slugs');
                    }
                }

                /* Reset if "all" is clicked */
                if (tax[tax_name] === 'all') {
                    $filter.find('.is-active').removeClass('is-active');
                    $this.addClass('is-active');
                    tax[tax_name] = '';
                }

                tax_data['filter_ids'] = tax[tax_name];

                filter_data = JSON.stringify(filter_data);

                $ajax_block.attr('data-filter', filter_data);

                // Reset current page
                $ajax_block.data('paged', '1');

                // Remove items
                block_h = $ajax_block.find('.ajax-grid').height();
                $ajax_block.find('.ajax-grid').height(block_h);
                $ajax_block.find('.ajax-grid .flex-item').remove();

                // Load posts
                theme.ajax__posts_loader.get($ajax_block);

            },

            get: function(el) {

                if ($(el).data('loading') === true) {
                    return;
                }

                var $this = $(el), opts = $.parseJSON($this.attr('data-opts')), filter = $.parseJSON($this.attr('data-filter')), module_opts, paged = $this.data('paged'), $grid;

                /* Check if posts container exists */
                $grid = $this.find('.ajax-grid');

                // Grid Type
                opts.grid_type = 'flex'; // masonry
                if ( $grid.hasClass('masonry-grid') ) {
                    opts.grid_type = 'masonry'
                }
        

                if ($grid.length <= 0) {
                    return;
                }

                /* Paged */
                opts['paged'] = paged;

                /* Module opts */
                module_opts = $.parseJSON($grid.attr('data-module-opts'));

                /* Classes */
                $this.addClass('loading').removeClass('loaded last-page');

                $(el).data('loading', true);


                /* Ajax */
                $.ajax({
                    url: ajax_action.ajaxurl,
                    type: 'post',
                    data: {
                        action: opts['action'],
                        ajax_nonce: ajax_action.ajax_nonce,
                        opts: opts,
                        filter: filter,
                        module_opts: module_opts
                    },
                    success: function(result) {

                        if (result === 'Busted!') {
                            location.reload();
                            return false;
                        }

                        var $result = $(result);

                        if (result === 'no_results') {
                            $this.removeClass('loading loading-infinite');
                            $this.addClass('loaded');
                            $this.find('.ajax-grid').css('height', 'auto');
                            $this.data('loading', false);
                            return;
                        }

                        $result.imagesLoaded({
                            background: true
                        }, function() {
                            paged++;
                            $this.data('paged', paged);
                            $this.removeClass('loading loading-infinite');
                            $this.data('loading', false);
                            $grid.find('.new-item').removeClass('new-item');
                            $grid.append($($result).addClass('new-item'));
                            $.event.trigger({
                                type: "loadmore",
                                container: $grid,
                            });
                            theme.ajax__posts_loaded($grid);
                            $this.find('.ajax-grid').css('height', 'auto');

                            // Show
                            if ( opts.grid_type === 'masonry' ) {
                                // For VJS
                                var masonry_grid = $.makeArray($grid);
                                theme.masonry.resize_grid(masonry_grid);
                                theme.masonry.show_items(masonry_grid, '.masonry-brick.new-item > .masonry-content' );
                            } else {
                                var flex_grid = $.makeArray($grid);
                                theme.flex_grid.show_items(flex_grid, '.flex-item.new-item' );
                            }
                            if ($grid.find('.last-page').length) {
                                // Hide loader
                                $this.addClass('last-page loaded');
                            } else {
                                $this.removeClass('last-page loaded');
                            }
                        });
                    },
                    error: function(request, status, error) {
                        $this.data('paged', '2');
                        $this.removeClass('loading loading-infinite');
                        $(this).data('loading', false);
                        $this.addClass('loaded');
                        $this.find('.ajax-grid').css('height', 'auto');
                    }
                });
            }

        },

        /* ==================================================
          Social Players 
        ================================================== */
        social_players: {

            youtube: function() {
                if ($('.youtube:not(.ready)').length) {

                    $('.youtube:not(.ready)').each(function() {

                        /* Based on the YouTube ID, we can easily find the thumbnail image */
                        var src = 'https://i.ytimg.com/vi/' + this.id + '/maxresdefault.jpg'
                          , cover = $(this).attr('data-cover')
                          , ca = $(this).attr('data-ca');

                        /* Set default click action */
                        if (typeof ca === 'undefined') {
                            ca = 'open_in_player';
                        }

                        /* If image doesn't exists get image from YouTube */
                        if (cover) {
                            $(this).append('<img src="' + cover + '">');
                        } else {
                            $(this).append('<img src="' + src + '">');
                        }

                        /* Add thumb classes */
                        $(this).addClass('thumb thumb-fade ready');

                        /* Overlay the Play icon to make it look like a video player */
                        var icon_layer_template = '<span class="thumb-icon"><span class="icon icon-play"></span></span>';
                        $(this).append(icon_layer_template);

                        if (ca === 'open_in_player') {
                            $('#' + this.id).on('click', function() {

                                /* Create an iFrame with autoplay set to true */
                                var iframe_url = 'https://www.youtube.com/embed/' + this.id + '?autoplay=1&autohide=1';
                                var $parent = $('#' + this.id).parent();
                                if ($(this).data('params')) {
                                    iframe_url += '&' + $(this).data('params');
                                }

                                /* The height and width of the iFrame should be the same as parent */
                                var iframe = $('<iframe/>', {
                                    'frameborder': '0',
                                    'src': iframe_url,
                                    'width': '1200',
                                    'height': '688'
                                });

                                /* Replace the YouTube thumbnail with YouTube HTML5 Player */
                                $(this).replaceWith(iframe);

                                /* Make movie responsive */
                                if ($.fn.ResIframe) {
                                    $parent.ResIframe();
                                }

                            });
                        }
                    });
                }
            },

            vimeo: function() {
                if ($('.vimeo:not(.ready)').length) {
                    $('.vimeo:not(.ready)').each(function() {

                        var movie = $(this)
                          , id = movie.attr('id')
                          , cover = movie.attr('data-cover')
                          , ca = movie.attr('data-ca');

                        /* Set default click action */
                        if (typeof ca === 'undefined') {
                            ca = 'open_in_player';
                        }

                        /* If image doesn't exists get image from YouTube */
                        if (cover) {
                            movie.append('<img src="' + cover + '">');
                        } else {

                            $.getJSON('https://www.vimeo.com/api/v2/video/' + id + '.json?callback=?', {
                                format: "json"
                            }, function(data) {
                                var src = data[0].thumbnail_large;
                                var src = src.replace("_640.jpg", "_1280x720");
                                movie.append('<img src="' + src + '">');

                            });
                        }

                        /* Add thumb classes */
                        movie.addClass('thumb thumb-fade ready');

                        /* Overlay the Play icon to make it look like a video player */
                        var icon_layer_template = '' + '<span class="thumb-icon"><span class="icon icon-play"></span></span>';

                        movie.append(icon_layer_template);

                        if (ca === 'open_in_player') {

                            $('#' + id).on('click', function() {

                                /* Create an iFrame with autoplay set to true */
                                var iframe_url = 'https://player.vimeo.com/video/' + id + '?autoplay=1';
                                var $parent = $('#' + this.id).parent();
                                if ($(this).data('params')) {
                                    iframe_url += '&' + $(this).data('params');
                                }

                                /* The height and width of the iFrame should be the same as parent */
                                var iframe = $('<iframe/>', {
                                    'frameborder': '0',
                                    'src': iframe_url,
                                    'width': '1280',
                                    'height': '734'
                                });

                                /* Replace the YouTube thumbnail with YouTube HTML5 Player */
                                $(this).replaceWith(iframe);

                                /* Make movie responsive */
                                if ($.fn.ResIframe) {
                                    $parent.ResIframe();
                                }

                            });
                        }

                    });
                }
            }

        },

        /* ==================================================
          Window Events
        ================================================== */
        window_events: {

            // Vars
            offset: 20,
            adminbar_height: 40,
            hidden_nav: false,
            sticky_top: 0,
            sticky_offset: 150,

            // Methods
            init: function() {

                var header = $('#header');

                /* Sticky Block */
                if ($('.sticky-block').length) {
                    theme.window_events.sticky_top = $('.sticky-block').offset().top;
                }

                /* Disable hidden navigation */
                if ($('body').hasClass('sticky-header') ) {
                    theme.window_events.hidden_nav = true;
                }

                // Go to top
                if ($('body').hasClass('is-top-button') && $('#scroll-button').length == 0 ) {
                    document.body.insertAdjacentHTML('beforeend', '<a href="#" id="scroll-button" class="hidden"></a>');
                    var scroll_btn = document.body.querySelector('#scroll-button'),
                    scrollElement = window.document.scrollingElement || window.document.body || window.document.documentElement;
                    scroll_btn.addEventListener('click', function(e){
                        anime({
                            targets: scrollElement,
                            scrollTop: 0,
                            duration: 500,
                            easing: 'easeInOutQuad'
                        });
                        e.preventDefault();
                    }, false);
                }

                /* Add fixed position to WP Admin Bar */
                $('#wpadminbar').css('position', 'fixed');

                /* Onepage actions */
                theme.window_events.onepage();

                /* Scroll / Resize Events */
                theme.window_events.scroll_actions();

                window.addEventListener('scroll', theme.window_events.scroll_actions);
                window.addEventListener('resize', theme.window_events.scroll_actions);

            },


            scroll_actions: function() {
                var st = $(window).scrollTop()
                  , wh = $(window).height()
                  , ww = $(window).width()
                  , header = $('#header')
                  , header_height = header.outerHeight()
                  , top_header_h = 0
                  , offset = 0

                /* WP Header */
                if ($('#top-header').length) {
                    top_header_h = $('#top-header').outerHeight();
                }

                /* WP Header */
                if ($('#wpadminbar').length) {
                    theme.window_events.adminbar_height = $('#wpadminbar').outerHeight();
                }

                offset = top_header_h + theme.window_events.adminbar_height;

                if ( st > 200 ) {
                    $('#scroll-button').removeClass('hidden');
                } else {
                    $('#scroll-button').addClass('hidden');
                }

                /* Show or hide naviagtion background */
                if (theme.window_events.hidden_nav) {
                    if (st+theme.window_events.adminbar_height > offset) {
                        header.addClass('sticky');
                    } else {
                        header.removeClass('sticky');
                    }
                }

            },

            onepage: function() {
                theme.window_events.scroll_onepage();
                window.addEventListener('scroll', theme.window_events.scroll_onepage);
            },

            scroll_onepage: function() {
                var sections = document.querySelectorAll(".one-page-section");
                if (sections.length <= 0) {
                    return;
                }

                var first_section = sections[0], last_section = sections[sections.length - 1], header = document.querySelector('#header'), header_height = header.getBoundingClientRect().height, nav = document.querySelector('#nav-main'), offset = theme.window_events.offset, cur_pos = $(this).scrollTop(), last_pos = last_section.offsetTop + last_section.getBoundingClientRect().height, nav_a, selected_nav, id;

                /* If Main navigation exists */
                if (!nav) {
                    return;
                }

                /* WP Header */
                if ($('#wpadminbar').length) {
                    theme.window_events.adminbar_height = $('#wpadminbar').outerHeight();
                }

                /* Add .active class to navigation if
                is over on .section container */
                if (cur_pos < first_section.offsetTop - header_height - offset) {
                    for (var i = 0; i < sections.length; ++i) {
                        sections[i].classList.remove("active");
                    }
                    nav_a = nav.querySelectorAll('a');
                    for (var i = 0; i < nav_a.length; ++i) {
                        nav_a[i].classList.remove("active");
                    }
                } else if (cur_pos > last_pos - header_height - offset) {
                    for (var i = 0; i < sections.length; ++i) {
                        sections[i].classList.remove("active");
                    }
                    nav_a = nav.querySelectorAll('a');
                    for (var i = 0; i < nav_a.length; ++i) {
                        nav_a[i].classList.remove("active");
                    }
                } else {

                    for (var i = 0; i < sections.length; ++i) {
                        var top = sections[i].offsetTop - header_height - offset
                          , bottom = top + sections[i].getBoundingClientRect().height;

                        if (cur_pos >= top && cur_pos <= bottom) {
                            nav_a = nav.querySelectorAll('a');
                            for (var nav_i = 0; nav_i < nav_a.length; ++nav_i) {
                                nav_a[nav_i].classList.remove("active");
                            }
                            for (var section_i = 0; section_i < sections.length; ++section_i) {
                                sections[section_i].classList.remove("active");
                            }
                            sections[i].classList.add('active');
                            id = sections[i].getAttribute('id');
                            selected_nav = nav.querySelector('a[href*="#' + id + '"]');
                            if (selected_nav !== null) {
                                selected_nav.classList.add('active');
                            }
                        }
                    }
                }

            },

            resize_actions: function() {
                theme.window_events.scroll_actions();
            }

        },

        /* ==================================================
          Vendors Plugins
        ================================================== */
        plugins: {

            reset: function() {

                /* Waypoints */
                if ($.fn.waypoints) {
                    setTimeout(function() {
                        $.waypoints('refresh');
                        $.waypoints('destroy');
                    }, 400)
                }

            },

            // Tooltip
            Tooltip : function() {

                if ( $('body').hasClass('is-front-editor')) {
                    return;
                }

                $('.tip').on('mouseenter', function(e) {
                    // Add tip object
                    var 
                        tip = {},
                        title = '',
                        min_width = 200,
                        mouse_move = false,
                        tip = { 
                        'desc' : $(this).data('tip-desc'),
                        'top' : $(this).offset().top,
                        'content' :  $(this).find('.tip-content').html()
                    };

                    // Check if title is exists
                    if (tip.content == undefined) return;

                    // Append datatip prior to closing body tag
                    $('body').append('<div id="tip"><div class="tip-content"><div class="tip-inner">'+tip.content+'</div></div></div>');

                    // Set max width
                    if ($(this).outerWidth() > min_width) {
                        $('#tip .tip-inner').width($(this).outerWidth());
                    }

                    // Store datatip's height and width for later use
                    tip['h'] = $('#tip div:first').outerHeight()+100;
                    tip['w'] = $('#tip div:first').outerWidth();

                    // Set datatip's mask properties - position, height, width etc  
                    $('#tip').css({position:'absolute', overflow:'hidden', width:'100%', top:tip['top']-tip['h'], height:tip['h'], left:0 });

                    // Mouse Move
                    if (mouse_move) {
                        // Set tip position
                        $('#tip div').css({ left:e.pageX-(tip['w']/2), top:tip['h']+5 }).animate({ top:100 }, 500);

                        // Move datatip according to mouse position, whilst over instigator element
                        $(this).mousemove(function(e){ $('#tip div').css({left: e.pageX-(tip['w']/2)}); }); 
                    } else {
                        // Set tip position
                        var pos =  $(this).offset(),
                        t = document.querySelector('#tip div');
                        t.style.left = pos.left+'px';
                        t.style.top = '200px';

                        anime({
                            targets: t,
                            top: 100,
                            duration: 500,
                            easing: 'easeOutBack',
                        });
                    }


                }).on('mouseleave click', function(e) {
                    if ( $('body').hasClass('is-front-editor')) {
                        return;
                    }
                    // Remove datatip instances
                    $('#tip').remove(); 

                });

            },

            // ThumbSlider
            ThumbSlider: function() {
                if ( ! $('body').hasClass('is-front-editor')) {
                    $('.thumb-slide').thumbSlider();
                }
            },

            // ResIframe
            ResIframe: function() {
                $('body').ResIframe();
            },

            // Lazy Load
            Lazy: function() {

                if ($('body').hasClass('lazyload')) {

                    var lazyLoadInstance = new LazyLoad({
                        elements_selector: ".lazy",
                        scrollDirection: 'vertical',
                        visibleOnly: true,
                        threshold: 0,
                        callback_reveal: function(el){
                            el.classList.add('loaded');
                        }
                    });

                }

            },

            // theiaStickySidebar
            theiaStickySidebar: function() {
                if (typeof $.fn.theiaStickySidebar !== 'undefined' && $('.sticky-sidebars .sidebar').length) {

                    $('.sticky-sidebars .sidebar').each(function(i) {

                        var id = 'sticky-sidebar-' + i
                          , offset = 0
                          , nav_height = $('.header').outerHeight()
                          , additional_margin = theme.window_events.adminbar_height;

                        if ( $( 'body' ).hasClass('sticky-sidebars') ) {
                            additional_margin = additional_margin + nav_height;
                        }

                        $(this).attr('id', id);

                        $('#' + id).theiaStickySidebar({
                            // Settings
                            additionalMarginTop: additional_margin
                        });

                    });

                }
            },

            // Disqus
            disqus: function() {
                if ($('#disqus_thread').length) {

                    var disqus_identifier = $('#disqus_thread').attr('data-post_id')
                      , disqus_shortname = $('#disqus_thread').attr('data-disqus_shortname')
                      , disqus_title = $('#disqus_title').text()
                      , disqus_url = window.location.href
                      , protocol = location.protocol;
                    /* * * Disqus Reset Function * * */
                    if (typeof DISQUS !== 'undefined') {
                        DISQUS.reset({
                            reload: true,
                            config: function() {
                                this.page.identifier = disqus_identifier;
                                this.page.url = disqus_url;
                                this.page.title = disqus_title;
                            }
                        });
                    } else {
                        var dsq = document.createElement('script');
                        dsq.type = 'text/javascript';
                        dsq.async = true;
                        dsq.src = protocol + '//' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    }
                }
            }

        },


        /* ==================================================
          Prototypes 
        ================================================== */
        prototypes : function() {

            // Thumb Slider
            $.fn.thumbSlider = function(options) {

                var 
                    $this = $(this),
                    mouseX = 0,
                    mouseY = 0;
                

                // If "thumb-slide" has two images
                if ( $('img', $this).length > 1) {

                    // Check mouse position
                    $('body').mousemove(function(e){
                        mouseX = e.pageX // gives X position
                        mouseY = e.pageY // gives Y position
                    });

                    $(window).resize(function() {
                            $('.thumbs-wrap img:last-child').css({visibility : 'hidden'})
                    });

                    var 
                        $this,
                        $thumb,
                        $hoverThumb,
                        $height,
                        $width,
                        $wrap,
                        $wrap_a,
                        $enterFrom,
                        $leaveFrom;

                    // Hover event
                    $this.on('mouseenter', function(e) {

                        $this       = $(this),
                        $wrap       = $('.thumbs-wrap', $this),
                        $thumb      = $('img:first-child', $this),
                        $hoverThumb = $('img:last-child', $this),
                        $height     = $thumb.height(),
                        $width      = $thumb.width(),
                        $enterFrom  = enterFrom($this);

                        //console.log("enter from: " + enterFrom($this));

                        // Add fixed width and height to the thumb image 
                        $this.width($width);
                        $this.height($height);
                        $thumb.height($height);
                        $thumb.width($width);
                        $hoverThumb.height($height);
                        $hoverThumb.width($width);
                        $wrap.height($height*2);
                        $wrap.width($width*2);

                        // Add initial styles to thumb image and wrapper
                        $thumb.css({
                            position : 'absolute',
                            top : 0,
                            left : 0
                        });
                        $wrap.css({
                            position : 'absolute',
                            top: 0,
                            left: 0
                        });

                        $wrap_a = $.makeArray($wrap);
                        // Show direct animate

                        // From top
                        if ($enterFrom == 'top') {

                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : 0, left : 0 });
                            $hoverThumb.css({ top : -$height + 'px', left : 0, visibility : 'visible'});
                            anime.remove( $wrap_a);

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                top: $height,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                        }
                        // From bottom
                        else if ($enterFrom == 'bottom') {

                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top :  0, left : 0 });
                            $hoverThumb.css({ top : $height + 'px', left : 0, visibility : 'visible'});

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                top: -$height,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                        }
                        // From left
                        else if ($enterFrom == 'left') {

                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : 0, left : '0' });
                            $hoverThumb.css({ top : 0, left : -$width + 'px', visibility : 'visible'});

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                left: $width,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                        }
                        // From right
                        else if ($enterFrom == 'right') {

                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : 0, left : 0 });
                            $hoverThumb.css({ top : 0, left : $width + 'px', visibility : 'visible'});

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                left: -$width,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                        }
                
                    
                    }).on('mouseleave', function(e) {
                        
                        $leaveFrom  = leaveFrom($this, e);

                        // From top
                        if ($leaveFrom == 'top') {
                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : $height + 'px', left : 0 });
                            $hoverThumb.css({ top : -$height + 'px', left : 0 });

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                top: 0,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                            
                        }
                        // From bottom
                        else if ($leaveFrom == 'bottom') {
                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : -$height + 'px', left : 0 });
                            $hoverThumb.css({ top : $height + 'px', left : 0 });

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                top: 0,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                            
                        }
                        // From left
                        else if ($leaveFrom == 'left') {
                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : 0, left : $width + 'px' });
                            $hoverThumb.css({ top : 0, left : -$width + 'px' });

                            // Animate
                            anime.remove( $wrap_a);
                            anime({
                                targets: $wrap_a,
                                left: 0,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                            
                        }
                        // From right
                        else if ($leaveFrom == 'right') {
                            // Set Hover thumb position and thumbs wrap
                            $wrap.css({ top : 0, left : -$width + 'px' });
                            $hoverThumb.css({ top : 0, left : $width + 'px' });
                            anime.remove( $wrap_a);
                            // Animate
                            anime({
                                targets: $wrap_a,
                                left: 0,
                                duration: 500,
                                easing: 'easeOutExpo',
                            });
                            
                        }
                    
                        // Reset styles
                        $thumb.css({ 
                            position : 'relative',
                            height : 'auto',
                            width : 'auto'

                        });
                        $wrap.css({ 
                            position : 'relative',
                            height : 'auto',
                            width : 'auto'
                        });
                        $this.css({
                            width : 'auto',
                            height : 'auto'
                        });
                    
                    });

                }

                // Detect mouse direction

                // Enter from
                function enterFrom(el) {
                    var 
                        $direction = 'top',
                        $pos       = el.offset();

                    if (mouseX <= $pos.left) return $direction = 'left';
                    if (mouseX >= ($pos.left + el.width())) return $direction = 'right';
                    if ((mouseX >= $pos.left) && (mouseY <= $pos.top)) return $direction = 'top';
                    if ((mouseX >= $pos.left) && (mouseY >= ($pos.top + el.height()))) return $direction = 'bottom';

                    return $direction;
                }

                // Leave from
                function leaveFrom(el, e) {
                    var 
                        $direction = 'top',
                        $pos       = el.offset(),
                        $mouseX    = e.pageX,
                        $mouseY    = e.pageY;

                    if ($mouseX <= $pos.left) return $direction = 'left';
                    if ($mouseX >= ($pos.left + el.width())) return $direction = 'right';
                    if (($mouseX >= $pos.left) && ($mouseY <= $pos.top)) return $direction = 'top';
                    if (($mouseX >= $pos.left) && ($mouseY >= ($pos.top + el.height()))) return $direction = 'bottom';

                    return $direction;
                }

            }


            // Resizable iframes
            $.fn.extend({ 
        
                //pass the options variable to the function
                ResIframe: function(options) {

                    //Set the default values, use comma to separate the settings, example:
                    var defaults = {
                        syntax : ''
                    }
                        
                    var options =  $.extend(defaults, options);

                    return $('iframe', this).each(function(i) {

                        if ( 
                            $(this).parent().hasClass("wpb_video_wrapper") 
                            || $(this).parents().hasClass("kc_video_wrapper") 
                            || $(this).parents().hasClass("kc_wrap-video-bg") ) {

                            return;
                        }
                        var 
                            $o = options,
                            $iframe = $(this),
                            $players = /www.youtube.com|www.youtube-nocookie.com|player.vimeo.com|bandcamp.com/;
                        
                        if ($iframe.attr('src') !== undefined && $iframe.attr('src') !== '' && $iframe.attr('src').search($players) > 0) {

                            // Ratio
                            var $ratio = ($iframe.height() / $iframe.width()) * 100;

                            // Add some CSS to iframe
                            $iframe.css({
                                position : 'absolute',
                                top : '0',
                                left : '0',
                                width : '100%',
                                height : '100%'
                            });

                            // Add wrapper element
                            $iframe.wrap('<div class="iframe-wrap" style="width:100%;position:relative;height:0;padding-bottom:'+$ratio+'%" />');
                        }
                    
                    });
                }
            });


            // Visible
            $.fn.visible = function(partial,hidden,direction,container){

                if (this.length < 1)
                    return;

                var $w=$(window);

                // Set direction default to 'both'.
                direction = direction || 'both';
                    
                var $t          = this.length > 1 ? this.eq(0) : this,
                                isContained = typeof container !== 'undefined' && container !== null,
                                $c                = isContained ? $(container) : $w,
                                wPosition        = isContained ? $c.position() : 0,
                    t           = $t.get(0),
                    vpWidth     = $c.outerWidth(),
                    vpHeight    = $c.outerHeight(),
                    clientSize  = hidden === true ? t.offsetWidth * t.offsetHeight : true;

                if (typeof t.getBoundingClientRect === 'function'){

                    // Use this native browser method, if available.
                    var rec = t.getBoundingClientRect(),
                        tViz = isContained ?
                                                        rec.top - wPosition.top >= 0 && rec.top < vpHeight + wPosition.top :
                                                        rec.top >= 0 && rec.top < vpHeight,
                        bViz = isContained ?
                                                        rec.bottom - wPosition.top > 0 && rec.bottom <= vpHeight + wPosition.top :
                                                        rec.bottom > 0 && rec.bottom <= vpHeight,
                        lViz = isContained ?
                                                        rec.left - wPosition.left >= 0 && rec.left < vpWidth + wPosition.left :
                                                        rec.left >= 0 && rec.left <  vpWidth,
                        rViz = isContained ?
                                                        rec.right - wPosition.left > 0  && rec.right < vpWidth + wPosition.left  :
                                                        rec.right > 0 && rec.right <= vpWidth,
                        vVisible   = partial ? tViz || bViz : tViz && bViz,
                        hVisible   = partial ? lViz || rViz : lViz && rViz,
                vVisible = (rec.top < 0 && rec.bottom > vpHeight) ? true : vVisible,
                        hVisible = (rec.left < 0 && rec.right > vpWidth) ? true : hVisible;

                    if(direction === 'both')
                        return clientSize && vVisible && hVisible;
                    else if(direction === 'vertical')
                        return clientSize && vVisible;
                    else if(direction === 'horizontal')
                        return clientSize && hVisible;
                } else {

                    var viewTop                 = isContained ? 0 : wPosition,
                        viewBottom      = viewTop + vpHeight,
                        viewLeft        = $c.scrollLeft(),
                        viewRight       = viewLeft + vpWidth,
                        position          = $t.position(),
                        _top            = position.top,
                        _bottom         = _top + $t.height(),
                        _left           = position.left,
                        _right          = _left + $t.width(),
                        compareTop      = partial === true ? _bottom : _top,
                        compareBottom   = partial === true ? _top : _bottom,
                        compareLeft     = partial === true ? _right : _left,
                        compareRight    = partial === true ? _left : _right;

                    if(direction === 'both')
                        return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop)) && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
                    else if(direction === 'vertical')
                        return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop));
                    else if(direction === 'horizontal')
                        return !!clientSize && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
                }
            }
        },

    }

}(jQuery));
