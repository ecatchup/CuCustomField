/**
 * CuCustomField : baserCMS Custom Field Multiple Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfMultiple.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType, [name='data[CuCustomFieldDefinition][field_type]'], [name='CuCustomFieldDefinition[field_type]']").first();
    var parentId = $("#CuCustomFieldDefinitionParentId, [name='data[CuCustomFieldDefinition][parent_id]'], [name='CuCustomFieldDefinition[parent_id]']").first();

    fieldType.change(switchRelated);
    parentId.change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'multiple' || fieldType.val() === 'multiCheckbox') {
            if(!parentId.val()) {
                $("#RowCuCfValidate").show('slow');
            } else {
                $("#RowCuCfValidate").hide();
            }
            $("#RowCuCfChoices").show('slow');
            $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").parent().hide('fast');
            $("#CuCustomFieldDefinitionValidateNUMERICCHECK").parent().hide('fast');
            $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").parent().show('slow');
            $('#CuCustomFieldDefinitionValidateREGEXCHECK').parent().hide('fast');
            $('#CuCfValidateRegexGroup').hide('fast');
        }
    }
});

