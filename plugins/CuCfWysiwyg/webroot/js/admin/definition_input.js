/**
 * CuCustomField : baserCMS Custom Field Wysiwyg Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfWysiwyg.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'wysiwyg') {
            $("#RowCuCfParentId").hide();
            $("#RowCuCfRows").show('slow');
            $("#CuCfRows").show('slow').attr('placeholder', '200px');
            $("#CuCfCols").show('slow').attr('placeholder', '100%');
            $("#CuCfEditorToolType").show('slow');
        }
    }
});

