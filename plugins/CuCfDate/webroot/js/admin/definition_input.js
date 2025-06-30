/**
 * CuCustomField : baserCMS Custom Field Date Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfDate.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    switchRelated();

    function switchRelated() {
        let rowParentId = $("#RowCuCfParentId");
        if(fieldType.val() === 'date') {
            rowParentId.hide();
            $("#CuCustomFieldDefinitionParentId").val('');
        } else {
            rowParentId.show();
        }
    }
});

