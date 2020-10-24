/**
 * CuCustomField : baserCMS Custom Field Loop Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfLoop.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    $("#CuCustomFieldDefinitionParentId").change(parentIdChangeHandler);

    switchRelated();
    parentIdChangeHandler();

    function parentIdChangeHandler() {
        var rowPrepend = $("#RowCuCfPrepend");
        var rowAppend = $("#RowCuCfAppend");
        var rowDescription = $("#RowCuCfDescription");
        if($("#CuCustomFieldDefinitionParentId").val()) {
            rowPrepend.hide();
            rowAppend.hide();
            rowDescription.hide();
        } else {
            rowPrepend.show('slow');
            rowAppend.show('slow');
            rowDescription.show('slow');
        }
    }

    function switchRelated() {
    console.log(fieldType.val());
        if(fieldType.val() === 'loop') {
            $("#RowCuCfParentId").hide();
            $("#RowCuCfDefaultValue").hide();
            $("#RowCuCfRequired").hide();
            $("#CuCustomFieldDefinitionParentId").val('');
            $("#CuCustomFieldDefinitionRequired").attr('checked', false);
        } else {
            $("#RowCuCfParentId").show();
        }
    }
});

