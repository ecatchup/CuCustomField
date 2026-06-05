/**
 * CuCustomField : baserCMS Custom Field Related Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRelated.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType, [name='data[CuCustomFieldDefinition][field_type]'], [name='CuCustomFieldDefinition[field_type]']").first();

    fieldType.change(switchRelated);
    switchRelated();

    function switchRelated() {
        var relatedGroup = $("#RowCuCfRelated");
        if(fieldType.val() === 'related') {
            relatedGroup.show('slow');
        }
    }
});
