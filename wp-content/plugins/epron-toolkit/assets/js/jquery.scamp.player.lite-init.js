/**
 * Scamp Player INIT scripts
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Pendulum Toolkit
 * @version 1.0.0
 */

jQuery(document).ready(function($) {

    "use strict";

    /* Init Scamp Player Lite
	 ---------------------------------------------------------------------- */
    $('.spl-track').ScampPlayerLite({
        path: 'js/scamp_player_lite',
        track_vol: 60,
        sm_options: {},
        onReady: function() {
        }
    });

    // Helper functions

    // Tracklist scroll
    $('.spl-scroll').each(function() {

        var id = $(this).attr('id')
          , Scrollbar = window.Scrollbar;

        Scrollbar.init(document.querySelector('#' + id), {});

    });

    // Album player
    (function() {

        if ($('.spl-player').length <= 0)
            return;

        $('.spl-player .spl-player-list .spl-track-ctrl').on('click', function() {
            var $this = $(this).parents('.spl-simple-track');
            var $player = $this.parents('.spl-player').find('.spl-player-container');

            $this.parents('.spl-playlist').find('.spl-simple-track').removeClass('active playing');
            $this.addClass('active playing');

            if ($this.attr('data-url') != $player.attr('data-url')) {

                // Activated track
                $this.parents('.spl-playlist').find('.spl-simple-track').removeClass('active');
                $this.addClass('active');

                __add_track($this);

            } else if ($this.attr('data-url') == $player.attr('data-url')) {
                $player.find('.spl-play-button').trigger('click');

            }
        });

        // Events
        $('.spl-player .spl-track.spl-player-container').bind('onfinish', function(event, sound) {
            __next_track($(this));
        });

        // Add track
        function __add_track(that) {
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

        }

        // Next track
        $('.spl-player .spl-next').on('click', function() {
            __next_track($(this).parents('.spl-track.spl-player-container'));
        });

        function __next_track(that) {
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
                __add_track(next_track);
            }, 300);

        }

        // Prev track
        $('.spl-player .spl-prev').on('click', function() {
            __prev_track($(this).parents('.spl-track.spl-player-container'));
        });

        function __prev_track(that) {
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
                __add_track(prev_track);
            }, 300);
        }
    }
    )();

});

(function($) {

    $.fn.sizeChanged = function(handleFunction) {
        var element = this;
        var lastWidth = element.width();
        var lastHeight = element.height();

        setInterval(function() {
            if (lastWidth === element.width() && lastHeight === element.height())
                return;
            if (typeof (handleFunction) == 'function') {
                handleFunction({
                    width: lastWidth,
                    height: lastHeight
                }, {
                    width: element.width(),
                    height: element.height()
                });
                lastWidth = element.width();
                lastHeight = element.height();
            }
        }, 100);

        return element;
    }
    ;

}(jQuery));
