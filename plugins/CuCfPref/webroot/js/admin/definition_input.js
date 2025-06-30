/**
 * CuCustomField : baserCMS Custom Field Pref Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfPref.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    switchRelated();

    // submit時の処理
    $("#BtnSave").click(function () {
        // 都道府県の選択値対応表は送らないようにする
        $('#CuCustomFieldDefinitionPreviewPrefList').attr('disabled', 'disabled');
    });

    function switchRelated() {
        if(fieldType.val() === 'pref') {
            $("#PreviewPrefList").show();
        } else {
            $("#PreviewPrefList").hide();
        }
    }
});

