/**
 * CuCustomField : baserCMS Custom Field Textarea Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfTextarea.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType, [name='data[CuCustomFieldDefinition][field_type]'], [name='CuCustomFieldDefinition[field_type]']").first();
    var parentId = $("#CuCustomFieldDefinitionParentId, [name='data[CuCustomFieldDefinition][parent_id]'], [name='CuCustomFieldDefinition[parent_id]']").first();

    fieldType.change(switchRelated);
    parentId.change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'textarea') {
            if(!parentId.val()) {
                $("#RowCuCfValidate").show('slow');
                $("#RowCuCfAutoConvert").show('slow');

                $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").parent().show('slow');
                $("#CuCustomFieldDefinitionValidateNUMERICCHECK").parent().show('slow');
                $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").parent().hide('fast');
                $('#CuCustomFieldDefinitionValidateREGEXCHECK').parent().show('slow');
                if ($('#CuCustomFieldDefinitionValidateREGEXCHECK').prop('checked')) {
                    $('#CuCfValidateRegexGroup').show('fast');
                }
            } else {
                $("#RowCuCfValidate").hide();
                $("#RowCuCfAutoConvert").hide();
            }
            $("#RowCuCfSize").show('slow');
            $("#RowCuCfPlaceholder").show('slow');
            $("#RowCuCfRows").show('slow');
            $("#CuCfSize").hide('fast');
            $("#CuCfMaxLength").hide('fast');
            $("#CuCfCounter").show('slow');
            $("#CuCfRows").show('slow').attr('placeholder', '3');
            $("#CuCfCols").show('slow').attr('placeholder', '40');
            $("#CuCfEditorToolType").hide('fast');
        }
    }
});

