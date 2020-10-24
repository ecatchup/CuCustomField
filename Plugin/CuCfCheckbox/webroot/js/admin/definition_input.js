/**
 * CuCustomField : baserCMS Custom Field Checkbox Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfCheckbox.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    switchRelated();

    // カスタムフィールド名の入力時、ラベル名が空の場合は名称を自動で入力する
    $("#CuCustomFieldDefinitionName").change(function () {
        $labelName = $("#CuCustomFieldDefinitionLabelName");
        var labelNameValue = $labelName.val();
        if (!labelNameValue) {
            $labelName.val($("#CuCustomFieldDefinitionName").val());
        }
    });

    function switchRelated() {
        if(fieldType.val() === 'checkbox') {
            $("#RowCuCfLabelName").show();
            $("#RowCuCfValidate").hide('fast');

            $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").parent().hide('fast');
            $("#CuCustomFieldDefinitionValidateNUMERICCHECK").parent().hide('fast');
            $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").parent().show('fast');
            $('#CuCustomFieldDefinitionValidateREGEXCHECK').parent().hide('fast');
            $('#CuCfValidateRegexGroup').hide('fast');
        }
    }
});

