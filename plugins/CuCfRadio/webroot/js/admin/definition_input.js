/**
 * CuCustomField : baserCMS Custom Field Radio Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRadio.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType, [name='data[CuCustomFieldDefinition][field_type]'], [name='CuCustomFieldDefinition[field_type]']").first();

    fieldType.change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'radio') {
            $("#RowCuCfChoices").show('slow');
            $("#RowCuCfSeparator").show('slow');
        }
    }
});

