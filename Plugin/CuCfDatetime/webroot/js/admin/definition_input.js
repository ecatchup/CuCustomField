/**
 * CuCustomField : baserCMS Custom Field Datetime Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfDatetime.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    switchRelated();

    function switchRelated() {
        let rowParentId = $("#RowCuCfParentId");
        if(fieldType.val() === 'datetime') {
            rowParentId.hide();
            $("#CuCustomFieldDefinitionParentId").val('');
        } else {
            rowParentId.show();
        }
    }
});

