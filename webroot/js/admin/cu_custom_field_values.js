/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.js
 * @license          MIT LICENSE
 */

$(function(){
    $(document).on('click', '.btn-add-loop', function(event){
        event.preventDefault();
        var $button = $(this);
        $button.prop('disabled', true);

        var srcFieldName = String($button.attr('data-src') || '');
        var count = parseInt($button.attr('data-count'), 10);
        if (isNaN(count)) {
            count = $('#loop-' + srcFieldName + ' .cucf-loop-block[id^="CucfLoop"]').length;
        }

        var $source = $('#CufcLoopSrc' + srcFieldName);
        var $clone = $source.clone(false, false);
        $clone.removeAttr('hidden').css('display', 'none');

        $clone.find('input,select,textarea').each(function(){
            var $field = $(this);
            var name = $field.attr('name');
            var id = $field.attr('id');
            if (name) {
                $field.attr('name', name.replace(/__loop-src__/g, String(count)));
            }
            if (id) {
                $field.attr('id', id.replace(/Loop-src/g, String(count)));
            }
        });

        $clone.find('label').each(function(){
            var $label = $(this);
            var forId = $label.attr('for');
            if (forId) {
                $label.attr('for', forId.replace(/Loop-src/g, String(count)));
            }
        });

        var blockId = 'CucfLoop' + srcFieldName + '-' + count;
        $clone.attr('id', blockId);
        $clone.find('.btn-delete-loop').attr('data-delete-target', blockId);

        $('#loop-' + srcFieldName).append($clone);
        $clone.slideDown(150);
        $button.attr('data-count', String(count + 1));
        $button.prop('disabled', false);
    });

    $(document).on('click', '.btn-delete-loop', function(event) {
        event.preventDefault();
        if(!confirm('ループブロックを削除します。本当によろしいですか？')) {
            return;
        }
        var target = $(this).attr('data-delete-target');
        if (!target) {
            return;
        }
        $('#' + target).slideUp(150, function(){
            $(this).remove();
        });
    });
});
