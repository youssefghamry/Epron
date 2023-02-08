/**
 * Rascals Panel extra scripts for fields
 *
 * @author Rascals Themes
 * @category JavaScripts
 * @package Rascals Panel
 * @version 1.0.0
 */


jQuery(document).ready(function($) {

    "use strict";

    /* Sortable List
	------------------------------------------------------------------------*/
    $('.sortable').sortable({
        handle: $('.sortable-list .drag-item'),
        axis: 'y'
    });

    /* Add new static item */
    $('.box-row .add-new-item').on('click', function() {

        var new_item = $(this).parents('.box-row').find('.new-item ul').html();
        $(this).parents('.box-row').find('.sortable-list').append(new_item);
        $(this).parents('.box-row').find('.sortable-list').find('.no-save').removeClass('no-save');
        $(this).parents('.box-row').find('.sortable').sortable({
            handle: $('.sortable-list .drag-item'),
            axis: 'y'
        });
        return false;
    });

    /* Delete item */
    var delete_item = function() {
        var current_item = $(this);

        /* Show Dialog */
        $('<div/>').text('These item will be permanently deleted and cannot be recovered. Are you sure?.').appendTo('body').dialog({
            title: 'Delete Item',
            modal: false,
            width: 400,
            hide: 'fade',
            show: 'fade',
            dialogClass: 'ui-custom ui-custom-dialog',
            buttons: [{
                text: 'Delete item',
                'class': 'ui-button-delete',
                click: function() {
                    current_item.parents('li:eq(0)').fadeOut(400, function() {
                        $(this).remove();
                    });

                    /* Show notice */
                    $('#rascals-panel-notices').notify('create', {
                        title: 'Success!',
                        text: 'Item are removed.'
                    });

                    $(this).dialog('close');
                }
            }, {
                text: 'Cancel',
                'class': 'ui-button-cancel',
                click: function() {
                    $(this).dialog('close');
                }
            }],
            open: function(event, ui) {

                /* Buttons icons */
                $(event.target).parent().find('.ui-button-cancel span').prepend('<i class="fa icon fa-times"></i>');
                $(event.target).parent().find('.ui-button-delete span').prepend('<i class="fa icon fa-trash-o"></i>');

                /* Add helper class to overlay layer */
                $('.ui-widget-overlay').addClass('ui-custom-overlay');

                /* Center dialog */
                $(window).resize(function() {
                    $(event.target).dialog('option', 'position', 'center');
                });
            }

        });

    };

    /* Bind click function (delete row) */
    $('body').on('click', '.sortable-list .delete-item', delete_item);

    /* Depedences
	------------------------------------------------------------------------*/
    $('.box-row[data-depedency-el]').each(function(i) {
        var $this = $(this), depedency_el = $this.attr('data-depedency-el'), depedency_val = $this.attr('data-depedency-val'), ctrl_val, $ctrl_box, $ctrl;

        if ($('#' + depedency_el).length) {
            // To array
            depedency_val = depedency_val.split(',');

            // Controler
            $ctrl_box = $('#' + depedency_el).parents('.box-row');

            if ($ctrl_box.hasClass('dependent-hidden')) {
                $this.addClass('dependent-hidden');
            } else {
                $ctrl = $('#' + depedency_el);
                ctrl_val = $ctrl.val();
                if ($.inArray(ctrl_val, depedency_val) !== -1) {
                    $this.removeClass('dependent-hidden');
                } else {
                    $this.addClass('dependent-hidden');
                }
            }
        }

    });


     /* Multiple values
	------------------------------------------------------------------------*/
	(function() {


        let getMultiObj = function(fields) {
            let obj = {};
            fields.each(function(i){
                let val = $(this).val();
                if ( val === '' ) {
                    val = $(this).data('multi-default');
                    $(this).val(val);
                }
                obj[i] = {
                    value : val
                };
               
            });
            if ( $.isEmptyObject(obj) ) {
                return false;
            }
            return obj;
        }

        let saveMulti = function(options, save_here) {
            let data = getMultiObj(options);
            if ( data ) {
                data = JSON.stringify(data);
                save_here.val(data)
            } else {
                save_here.val('');
            }

        }

        let initMulti = function() {
            $('.box-row.multi-options').each(function(i) {
                if ( $(this).find('.multi-data') ) {
                    let multi_wrap = $(this);
                    let save_here = multi_wrap.find('.multi-data');
                    let options = multi_wrap.find('.multi-option');
                    let data = getMultiObj(options);

                    $('.multi-option').on('blur, change', function() {
                        saveMulti(options, save_here);
                    });
                }
            });

        };

        $( document ).on('SettingsSaved', function() {});

        // init
        initMulti();

    })();


    function depedency(id, val) {

        // Find depedency element
        $('.box-row[data-depedency-el="' + id + '"]').each(function() {
            var $depedency_el = $(this), child_id = $depedency_el.attr('data-id'), $depedency_child = $('.box-row[data-id="' + child_id + '"]'), $depedency_child_field = null, child_filed_val, child_val, depedency_child_field_id;
            if ($depedency_child.length) {
                depedency_child_field_id = $depedency_child.attr('data-id');
                $depedency_child_field = $('.box-row[data-depedency-el="' + depedency_child_field_id + '"]');
            }

            if ($depedency_el.length) {

                var depedency_val = $depedency_el.attr('data-depedency-val');

                // To array
                depedency_val = depedency_val.split(',');

                if ($.inArray(val, depedency_val) !== -1) {

                    // Children
                    if ($depedency_child_field.length) {
                        child_val = $depedency_child.find('#' + child_id).val();
                        child_filed_val = $depedency_child_field.attr('data-depedency-val');
                        child_filed_val = child_filed_val.split(',');

                        if ($.inArray(child_val, child_filed_val) !== -1) {
                            $depedency_child_field.removeClass('dependent-hidden');
                        }

                    }

                    $depedency_el.removeClass('dependent-hidden');
                } else {

                    // Children
                    if ($depedency_child_field.length) {
                        $depedency_child_field.addClass('dependent-hidden');
                    }

                    $depedency_el.addClass('dependent-hidden');
                }

            }

        });
    }

    /* EXTERNAL PLUGINS
	------------------------------------------------------------------------*/

    /* Select Image
	------------------------------------------------------------------------*/
    $('.select-image img').on('click', function(event) {

        /* Variables */
        var $box = $(this).parents('.box-row')
          , images = $('ul', $box)
          , select_id = $('select', $box).attr('id')
          , id = $(this).attr('data-image_id');

        /* Remove class */
        $('img', images).removeClass('selected-image');

        /* Add class */
        $(this).addClass('selected-image');

        /* Select input option */
        $('select option[value="' + id + '"]', $box).attr('selected', true);

        depedency(select_id, id);

        event.preventDefault();
    });

    /* Select
	------------------------------------------------------------------------*/
    $('.box-select').each(function() {

        // Show groups
        var $this = $(this)
          , $box = $this.parents('.box-row');

        $this.change(function() {
            var val = $(this).val();
            if (val == undefined)
                return;
            depedency($this.attr('id'), val);
        });

    });

    /* Multiselect
	------------------------------------------------------------------------*/
    $('.multiselect').each(function() {

        var $box = $(this).parents('.box-row');

        if ($(this).hasClass('save-empty')) {
            $(this).change(function() {
                var name = $(this).attr('name');
                if (($(this).val() || []) == '') {
                    $box.append('<input type="hidden" name="' + name + '" value="" class="multiselect-empty">');
                } else {
                    $box.find('.multiselect-empty').remove();
                }
            });
        }

    });

    /* Switch
	------------------------------------------------------------------------*/
    $('.switch-wrap').each(function() {

        // Show groups
        var $this = $(this)
          , $box = $this.parents('.box-row')
          , select = $this.find('select')
          , btn = $this.find('.switch-on-off')
          , on_val = btn.find('.onstate').attr('data-on')
          , off_val = btn.find('.offstate').attr('data-off');

        btn.on('click', function(e) {
            var $t = $(this), v = '', depedency_el;

            if ($t.hasClass('on')) {
                v = select.find('option').eq(1).val();
                select.val(v);
                $t.removeClass('on').addClass('off');
            } else {
                v = select.find('option').eq(0).val();
                select.val(v);
                $t.removeClass('off').addClass('on');
            }
            var val = select.val();

            if (val == undefined)
                return;

            depedency(select.attr('id'), val)

            e.preventDefault();

        });

    });

    /* Add Image
	------------------------------------------------------------------------*/
    (function() {
        var custom_uploader, target_input, media_container, attachment;

        $(document).on('click', '.upload-image', function(e) {

            e.preventDefault();

            // Media Container
            media_container = $(this).parent().parent();

            // Target input
            target_input = media_container.find('input');

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {

                attachment = custom_uploader.state().get('selection').first().toJSON();

                var url = '';

                if (attachment.sizes == undefined) {
                    url = attachment.url;
                } else if (attachment.sizes.thumbnail == undefined) {
                    url = attachment.sizes.full.url;
                } else {
                    url = attachment.sizes.thumbnail.url;
                }

                // Preview
                media_container.find('.image-holder img').remove();
                media_container.find('.image-holder').append('<img src="' + url + '" alt="Image Preview">');

                media_container.find('.image-holder').addClass('is_image');

                // Update ID
                target_input.val(attachment.id);
            });

            custom_uploader.on('open', function() {
                var selection = custom_uploader.state().get('selection')
                  , id = target_input.val();

                if (id !== '') {
                    attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add(attachment ? [attachment] : []);
                }
            });

            //Open the uploader dialog
            custom_uploader.open();

        });

        // Remove image
        $(document).on('click', '.remove-image', function(e) {

            e.preventDefault();

            var mc = $(this).parent().parent();
            mc.find('.image-holder img').remove();
            mc.find('input').val('');
            mc.find('.image-holder').removeClass('is_image');
        });

        // Select source
        $(document).on('change', '.image-source-select', function(e) {

            var mc = $(this).parent()
              , option = $(this).find('option:selected').val();

            if (option == 'media_libary') {
                mc.find('.image-holder').removeClass('hidden');
                mc.find('input.image-input').attr('data-external_link', mc.find('input.image-input').val());
                mc.find('input.image-input').val(mc.find('input.image-input').attr('data-media_id'));

                mc.find('input.image-input').attr('type', 'hidden');
            } else if (option == 'external_link') {
                mc.find('input.image-input').attr('data-media_id', mc.find('input.image-input').val());

                mc.find('input.image-input').val(mc.find('input.image-input').attr('data-external_link'));
                mc.find('.image-holder').addClass('hidden');
                mc.find('input.image-input').attr('type', 'text');
            }

        });

    }
    )();

    /* Datepicker
	------------------------------------------------------------------------*/
    $('.datepicker-input').datepicker({
        'dateFormat': 'yy-mm-dd',
        beforeShow: function(input, inst) {
            inst.dpDiv.addClass('_datepicker');
        }
    });

    /* Color Picker
	------------------------------------------------------------------------*/

    $('.colorpicker-input').each(function(i) {
        var id = 'color_picker_' + i;
        $(this).attr('id', id);
        $('#' + id).wpColorPicker();
    });

    /* Easy Link
	------------------------------------------------------------------------*/
    $('.easy-link').on('click', function(event) {
        $(this).easyLink();
        event.preventDefault();
    });

    /* Media Manager
	------------------------------------------------------------------------*/
    if ($('.mm-ids').length) {
        $('.mm-ids').MediaManager();
    }

    /* Iframe generator
	------------------------------------------------------------------------*/
    if ($('.generate-iframe').length) {
        $('.generate-iframe').IframeGenerator();
    }

    /* Background generator
	------------------------------------------------------------------------*/
    if ($('.generate-bg').length) {
        $('.generate-bg').BgGenerator();
    }

});
