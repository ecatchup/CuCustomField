/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfText.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    $("#CuCustomFieldDefinitionParentId").change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'text') {
            if(!$("#CuCustomFieldDefinitionParentId").val()) {
                $("#RowCuCfValidate").show('slow');
                $("#RowCuCfAutoConvert").show('slow');
                $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").parent().show('slow');
                $("#CuCustomFieldDefinitionValidateNUMERICCHECK").parent().show('slow');
                $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").parent().hide('fast');
                $('#CuCustomFieldDefinitionValidateREGEXCHECK').parent().show('slow');
                // 正規表現チェックが有効に指定されている場合は、専用の入力欄を表示する
                if ($('#CuCustomFieldDefinitionValidateREGEXCHECK').prop('checked')) {
                    $('#CuCfValidateRegexGroup').show('fast');
                }
            } else {
                $("#RowCuCfValidate").hide();
                $("#RowCuCfAutoConvert").hide();
            }
            $("#RowCuCfPlaceholder").show('slow');
            $("#RowCuCfSize").show('slow');

            $("#CuCfSize").show('slow');
            $("#CuCfMaxLength").show('slow');
            $("#CuCfCounter").show('slow');
        }
    }
});

