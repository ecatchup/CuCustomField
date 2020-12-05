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
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    $("#CuCustomFieldDefinitionParentId").change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'textarea') {
            if(!$("#CuCustomFieldDefinitionParentId").val()) {
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

