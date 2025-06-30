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
    $('.btn-add-loop').click(function(){
        $(this).prop('disabled',true);//ボタンを無効化する
        var srcFieldName = $(this).attr('data-src');
        var count = $(this).attr('data-count');
        var clone = $("#CufcLoopSrc" + srcFieldName).clone();
        clone.find('input,select,textarea').each(function(){
            console.log(count);
            //console.log($(this).attr('name'));
            $(this).attr('name', $(this).attr('name').replace('__loop-src__', count));
            //console.log($(this).attr('name'));
            console.log($(this).attr('id'));
            $(this).attr('id', $(this).attr('id').replace('Loop-src', count));
            console.log($(this).attr('id'));
        });
        // label for属性もループ番号に変更
        clone.find('label').each(function(){
            $(this).attr('for', $(this).attr('for').replace('Loop-src', count));
        });
        clone.attr('id', "CufcLoop" + srcFieldName + '-' + count);
        clone.find('.btn-delete-loop').each(function(){
            $(this).attr('data-delete-target', "CufcLoop" + srcFieldName + '-' + count);
            $(this).click(deleteLoopBlock);
        });
        $("#loop-" + srcFieldName).append(clone);
        clone.slideDown(150);
        $(this).attr('data-count', Number(count) + 1);
        $(this).prop('disabled',false);//ボタンを有効化する
        return false;
    });
    $(".btn-delete-loop").click(deleteLoopBlock);

    function deleteLoopBlock() {
      if(!confirm('ループブロックを削除します。本当によろしいですか？')) {
            return false;
        }
        $("#" + $(this).attr('data-delete-target')).slideUp(150, function(){
            $(this).remove();
        });
        return false;
    }
});
