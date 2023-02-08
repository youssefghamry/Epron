/**
 * Addons
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Epron Toolkit
 * @version 1.0.0
 */


var theme_toolkit = (function($) {

    "use strict";


    /* GO!
     -------------------------------- */

    // If document is ready VJS
    document.addEventListener("DOMContentLoaded", function() {
        theme_toolkit.init($);
    });

    return {


        // King Composer plugins
        reload_kc_video : false,
        loaded : false,


        /* Init
         -------------------------------- */
        init : function(){
            
            this.prototypes();
            this.scamp_player.init();
            this.scamp_player.scroll('body');
            this.album_player.init();

            this.lightbox();
            this.countdown();
            this.stats();
            this.parallax();

            // King Composer plugins
            if ( typeof kc_front !== "undefined" ) {
                if ( typeof kc_video_play !== "undefined") {
                    if ( $('.kc_video_play').length > 0 && typeof kc_video_play.init !== "undefined" ) {
                        theme_toolkit.reload_kc_video = true;
                    } 
                }
            }

            theme_toolkit.loaded = true;

            // Run after posts are loaded via ajax
            $(document).on( "AjaxPostsLoaded" , function(e){
                theme_toolkit.scamp_player.scroll(e.wrapper[0]);
                
            } );

        },

        scamp_player : {

            init : function(container) {
      
                $('.spl-track').ScampPlayerLite({
                    path: 'js/scamp_player_lite',
                    track_vol: 60,
                    sm_options: {},
                    onReady: function() {
                    }
                });

               
            },
            scroll : function( container ) {

                if ( $(container).find('.spl-scroll').length ) {

                    // Tracklist scroll
                    $(container).find('.spl-scroll').each(function() {

                        var id = $(this).attr('id')
                          , Scrollbar = window.Scrollbar;

                        Scrollbar.init(document.querySelector('#' + id), {});

                    });
                }
            }
           
        },

        album_player : {

            init : function(container) {
              
                // Play
                $(document).on('click', '.spl-player .spl-player-list .spl-track-ctrl', theme_toolkit.album_player.clicks);

                 // Open Tracklist
                $(document).on('click', '.spl-player .show-list-btn', function() {
                    $(this).parents('.spl-player').toggleClass('open');
                });

                // Next track
                $(document).on('click', '.spl-player .spl-next', function() {
                    theme_toolkit.album_player.next_track($(this).parents('.spl-track.spl-player-container'));
                });

                // Prev track
                $(document).on('click', '.spl-player .spl-prev', function() {
                    theme_toolkit.album_player.prev_track($(this).parents('.spl-track.spl-player-container'));
                });

                // Events
                $(document).on('spl_onfinish', function(event, sound) {
                    theme_toolkit.album_player.next_track($(sound));
                });

            },

            clicks : function(){

                var $this = $(this).parents('.spl-simple-track');
                var $player = $this.parents('.spl-player').find('.spl-player-container');

                $this.parents('.spl-playlist').find('.spl-simple-track').removeClass('active playing');
                $this.addClass('active playing');

                if ($this.attr('data-url') != $player.attr('data-url')) {

                    // Activated track
                    $this.parents('.spl-playlist').find('.spl-simple-track').removeClass('active');
                    $this.addClass('active');

                     theme_toolkit.album_player.add_track($this);

                } else if ($this.attr('data-url') == $player.attr('data-url')) {
                    $player.find('.spl-play-button').trigger('click');

                }

            },

            add_track : function(that){

                var $player = that.parents('.spl-player').find('.spl-player-container');

                $player.ScampPlayerLite('stop');

                // Activated track
                that.parents('.spl-playlist').find('.spl-simple-track').removeClass('active playing');
                that.addClass('active playing');

                // Cover 
                if (that.find('img.hidden-cover').length) {
                    $player.find('img.hidden-cover').attr('src', that.find('img.hidden-cover').attr('src'));
                    $player.find('img.hidden-cover').show();
                    $player.removeClass('spl-has-number').addClass('spl-has-cover').find('img.hidden-cover').css('display', 'inline-block');

                } else {
                    $player.removeClass('spl-has-cover').addClass('spl-has-number').find('img.hidden-cover').css('display', 'none');
                }

                // Title / desc
                $player.find('.spl-title').html(that.find('.spl-title').html());
                $player.find('.spl-desc').html(that.find('.spl-desc').html());

                // URL
                $player.attr('data-url', that.attr('data-url'));
                $player.data('url', that.attr('data-url'));

                $player.find('.spl-play-button').trigger('click');
                $player.addClass('playing');
            },

            next_track : function(that){

                 var $that = $(this)
                  , url = that.data('url')
                  , $tracklist = that.parents('.spl-player').find('.spl-playlist')
                  , $tracks_length = $tracklist.find('.spl-single-track').length
                  , $track_index = $tracklist.find('.spl-single-track[data-url="' + url + '"]').index();

                if (($track_index + 1) < $tracks_length) {
                    $track_index++;
                } else {
                    $track_index = 0;
                }
                var next_track = $tracklist.find('.spl-single-track').eq($track_index);
                setTimeout(function() {
                     theme_toolkit.album_player.add_track(next_track);
                }, 300);
            },

            prev_track : function(that){

                var $that = $(this)
                  , url = that.data('url')
                  , $tracklist = that.parents('.spl-player').find('.spl-playlist')
                  , $tracks_length = $tracklist.find('.spl-single-track').length
                  , $track_index = $tracklist.find('.spl-single-track[data-url="' + url + '"]').index();

                if (($track_index + 1) <= $tracks_length) {
                    $track_index--;
                } else {
                    $track_index = $tracks_length;
                }
                var prev_track = $tracklist.find('.spl-single-track').eq($track_index);
                setTimeout(function() {
                     theme_toolkit.album_player.add_track(prev_track);
                }, 300);

            },
        },


        lightbox : function(){

            /* Unbind prettyphoto form KC*/
            $("a[rel^='prettyPhoto']").unbind('click.prettyphoto');
            $("a[data-lightbox]").unbind('click.prettyphoto');

            $("a.kc-pretty-photo").each(function(i) {
                if ($(this).attr('data-lightbox')) {
                    $(this).addClass('kc-gallery-item');
                } else {
                    $(this).addClass('imagebox');
                }
            })
            $("a.kc-pretty-photo").removeAttr('rel data-lightbox').removeClass('kc-pretty-photo');

            /* KC Gallery */
            $('.kc_image_gallery').magnificPopup({
                delegate: 'a.kc-gallery-item',
                closeMarkup: '<a href="#" class="mfp-close"></a>',
                type: 'image',
                image: {
                    verticalFit: true,
                },
                gallery: {
                    arrowMarkup: '<a href="#" class="mfp-arrow mfp-arrow-%dir%"></a>',
                    enabled: true
                }
            });

            /* Theme gallery */
            $('.gallery-images-grid').magnificPopup({
                delegate: 'a.g-item',
                closeMarkup: '<a href="#" class="mfp-close"></a>',
                type: 'image',
                zoom: {
                    enabled: true,
                    duration: 300, // don't foget to change the duration also in CSS
                    opener: function(element) {
                        return element.find('img');
                    }
                },
                image: {
                    verticalFit: true,
                },
                callbacks: {
                    elementParse: function( item ) {

                        if ( item.el.hasClass( 'iframe-link' ) ) {
                            item.type = 'iframe';
                        } else {
                            item.type = 'image';
                        }

                    }
                },
                gallery: {
                    arrowMarkup: '<a href="#" class="mfp-arrow mfp-arrow-%dir%"></a>',
                    enabled: true
                }
            });

            /* Image */
            $('.imagebox').magnificPopup({
                type: 'image',
                closeMarkup: '<a href="#" class="mfp-close"></a>',
            });

            /* iframe */
            $('.iframebox').magnificPopup({
                type: 'iframe',
                closeMarkup: '<a href="#" class="mfp-close"></a>',
            });

            /* WP Gallery */
            $('.gallery').each(function() {

                var gallery = $(this)
                  , id = $(this).attr('id')
                  , attachment_id = false;
                if ($('a[href*="attachment_id"]', gallery).length) {
                    return false;
                }
                $('a[href*="uploads"]', gallery).each(function() {
                    $(this).attr('data-group', id);
                    $(this).addClass('thumb');
                    if ($(this).parents('.gallery-item').find('.gallery-caption').length) {
                        var caption = $(this).parents('.gallery-item').find('.gallery-caption').text();
                        $(this).attr('title', caption);
                    }

                });

                $(this).magnificPopup({
                    delegate: 'a',
                    closeMarkup: '<a href="#" class="mfp-close"></a>',
                    type: 'image',
                    fixedBgPos: true,
                    gallery: {
                        arrowMarkup: '<a href="#" class="mfp-arrow mfp-arrow-%dir%"></a>',
                        enabled: true
                    }
                });

            });
        },

        countdown : function() {

            if ( $.fn.countdown ) {
                $( '.kc-countdown' ).each( function(e) {
                    var date = $( this ).data( 'event-date' );

                    $( this ).countdown( date, function( event ) {
                        var $this = $( this );
                        $this.find( '.days' ).html( event.offset.totalDays );
                        $this.find( '.hours' ).html( event.strftime(''+'%H') );
                        $this.find( '.minutes' ).html( event.strftime(''+'%M') );
                        $this.find( '.seconds' ).html( event.strftime(''+'%S') );
                        
                    });
                });
            }
        },

        stats : function() {

            $( 'ul.stats' ).each( function(){

                /* Variables */
                var
                    $max_el       = 6,
                    $stats        = $( this ),
                    $stats_values = [],
                    $stats_names  = [],
                    $timer        = $stats.data( 'timer' ),
                    $stats_length;


                /* Get all stats and convert to array */
                /* Set length variable */
                $( 'li', $stats).each( function(i){
                    $stats_values[i] = $( '.stat-value', this).text();
                    $stats_names[i] = $( '.stat-name', this).text();
                });
                $stats_length = $stats_names.length;

                /* Clear list */
                $stats.html( '' );

                /* Init */
                display_stats();

                /* Set $timer */
                var init = setInterval( function(){
                    display_stats();
                },$timer);

                /* Generate new random array */
                function randsort(c,l,m) {
                    var o = new Array();
                    for (var i = 0; i < m; i++) {
                        var n = Math.floor(Math.random()*l);
                        var index = jQuery.inArray(n, o);
                        if (index >= 0) i--;
                        else o.push(n);
                    }
                    return o;
                }

                /* Display stats */
                function display_stats(){
                    var random_list = randsort( $stats_names, $stats_length, $max_el);
                    var i = 0;

                    /* First run */
                    if ( $( 'li', $stats).length == 0) {
                        for (var e = 0; e < random_list.length; e++) {
                            $( $stats).append( '<li class="stat-col"><span class="stat-value"></span><span class="stat-name"></span></li>' );
                        }
                    }

                    var _display = setInterval( function(){

                        var num = random_list[i];
                            var stat_name = $( 'li', $stats).eq(i).find( '.stat-name' );
                            stat_name.animate({bottom : '-40px', opacity : 0}, 400, function(){
                                $( this ).text( $stats_names[num]);
                                $( this ).css({bottom : '-40px', opacity : 1});
                                $( this ).animate({ bottom : 0}, 400 );
                            });
                            
                            var stat_value = $( 'li', $stats).eq(i).find( '.stat-value' );
                            display_val(stat_value, num);
                        i++;
                        if (i == random_list.length)
                            clearInterval(_display);
                    },600);
                }

                /* Display value */
                function display_val(val, num) {
                    var 
                        val_length = $stats_values[num].length,
                        val_int = parseInt( $stats_values[num], 10 ),
                        counter = 10,
                        delta = 10,
                        new_val;

                    // Delta
                    if (val_int <= 50) delta = 1;
                    else if (val_int > 50 && val_int <= 100) delta = 3;
                    else if (val_int > 100 && val_int <= 1000) delta = 50;
                    else if (val_int > 1000 && val_int <= 2000) delta = 100
                    else if (val_int > 2000 && val_int <= 3000) delta = 150;
                    else if (val_int > 3000 && val_int <= 4000) delta = 200;
                    else delta = 250;

                    var _display = setInterval( function(){
                        
                        counter = counter+delta;
                        new_val = counter;
                        val.text(new_val);
                        if (new_val >= val_int) {
                            clearInterval(_display);
                            val.text( $stats_values[num]);
                        }
                            
                    },40);
                    
                }

            });

        },

        parallax : function() {
            var $window = $(window);
            var windowHeight = $window.height();
            $( '.kc-elm[data-kc-parallax="true"]' ).each( function(){
                var $this = $(this), el_top;
                el_top = $this.offset().top;
                var pos = $window.scrollTop();
                    var $el = $(this), top = $el.offset().top, height = $el.outerHeight(true);
                    $this.css('backgroundPosition', "50% " + Math.round((el_top - pos) * 0.4) + "px");
            });
        },


        /* ==================================================
          Prototypes 
        ================================================== */
        prototypes : function() {

            $.fn.addClassDelay = function( c, d ) {
                var t = $( this );
                setTimeout( function(){ 
                    t.addClass( c ) }, 
                d );
                return this;
            };
            $.fn.removeClassDelay = function( c, d ) {
                var t = $( this );
                setTimeout( function(){ 
                    t.removeClass( c ) }, 
                d );
                return this;
            };

        }
    }

}( jQuery ));