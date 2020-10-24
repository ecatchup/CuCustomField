/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.js
 * @license          MIT LICENSE
 */

/**
 * プチカスタムフィールド用のJS処理
 */
$(function () {

    $("#CuCustomFieldDefinitionFieldName").focus();

    cuCustomFieldDefinitionFieldTypeChangeHandler();

    // タイプを選択すると入力するフィールドが切り替わる
    $("#CuCustomFieldDefinitionFieldType").change(cuCustomFieldDefinitionFieldTypeChangeHandler);

    // カスタムフィールド名の入力時、ラベル名が空の場合は名称を自動で入力する
    $("#CuCustomFieldDefinitionName").change(function () {
        $labelName = $("#CuCustomFieldDefinitionLabelName");
        var labelNameValue = $labelName.val();
        if (!labelNameValue) {
            $labelName.val($("#CuCustomFieldDefinitionName").val());
        }
    });

    // 利用中フィールド名一覧を表示する
    $('#show_field_name_list').change(function () {
        if ($(this).prop('checked')) {
            $('#FieldNameList').show('slow');
        } else {
            $('#FieldNameList').hide();
        }
    });

    // カスタムフィールド名、ラベル名、フィールド名の入力時、リアルタイムで重複チェックを行う
    $("#CuCustomFieldDefinitionName").keyup(checkDuplicateValueChengeHandler);
    $("#CuCustomFieldDefinitionFieldName").keyup(checkDuplicateValueChengeHandler);

    // 重複があればメッセージを表示する
    function checkDuplicateValueChengeHandler() {
        var fieldId = this.id;
        var options = {};
        // 本来であれば編集時のみ必要な値だが、actionによる条件分岐でビュー側に値を設定しなかった場合、
        // Controllerでの取得値が文字列での null となってしまうため、常に設定し取得している
        var id = $("#CuCustomFieldDefinitionScript").attr('data-id');
        var configId = $("#CuCustomFieldDefinitionScript").attr('data-config-id');

        switch (fieldId) {
            case 'CuCustomFieldDefinitionName':
                options = {
                    "data[CuCustomFieldDefinition][id]": id,
                    "data[CuCustomFieldDefinition][config_id]": configId,
                    "data[CuCustomFieldDefinition][name]": $("#CuCustomFieldDefinitionName").val()
                };
                break;
            case 'CuCustomFieldDefinitionFieldName':
                options = {
                    "data[CuCustomFieldDefinition][id]": id,
                    "data[CuCustomFieldDefinition][config_id]": configId,
                    "data[CuCustomFieldDefinition][field_name]": $("#CuCustomFieldDefinitionFieldName").val()
                };
                break;
        }
        $.ajax({
            type: "POST",
            data: options,
            url: $("#AjaxCheckDuplicateUrl").html(),
            dataType: "html",
            cache: false,
            success: function (result, status, xhr) {
                if (status === 'success') {
                    if (!result) {
                        if (fieldId === 'CuCustomFieldDefinitionName') {
                            $('#CheckValueResultName').show('fast');
                        }
                        if (fieldId === 'CuCustomFieldDefinitionFieldName') {
                            $('#CheckValueResultFieldName').show('fast');
                        }
                    } else {
                        if (fieldId === 'CuCustomFieldDefinitionName') {
                            $('#CheckValueResultName').hide('fast');
                        }
                        if (fieldId === 'CuCustomFieldDefinitionFieldName') {
                            $('#CheckValueResultFieldName').hide('fast');
                        }
                    }
                }
            }
        });
    }

    // 編集画面のときのみ実行する（削除ボタンの有無で判定）
    if ($('#BtnDelete').html()) {
        $('#BeforeFieldName').hide();
        $("#BtnSave").click(function () {
            $beforeFieldName = $('#BeforeFieldName').html();
            $inputFieldName = $('#CuCustomFieldDefinitionFieldName').val();
            if ($beforeFieldName !== $inputFieldName) {
                if (!confirm('フィールド名を変更した場合、これまでの記事でこのフィールドに入力していた内容は引き継がれません。\n本当によろしいですか？')) {
                    $('#BeforeFieldNameComment').css('visibility', 'visible');
                    $('#BeforeFieldName').show();
                    return false;
                }
            }
        });
    }

    // 正規表現チェックのチェック時に、専用の入力欄を表示する
    $('#CuCustomFieldDefinitionValidateREGEXCHECK').change(function () {
        $value = $(this).prop('checked');
        if ($value) {
            $('#CuCfValidateRegexGroup').show('slow');
        } else {
            $('#CuCfValidateRegexGroup').hide('high');
        }
    });

    // 正規表現入力欄が空欄になった際はメッセージを表示して入力促す
    $('#CuCustomFieldDefinitionValidateRegex').change(function () {
        if (!$(this).val()) {
            $('#CheckValueResultValidateRegex').show('slow');
        } else {
            $('#CheckValueResultValidateRegex').hide();
        }
    });

    // submit時の処理
    $("#BtnSave").click(function () {
        // 都道府県の選択値対応表は送らないようにする
        $('#CuCustomFieldDefinitionPreviewPrefList').attr('disabled', 'disabled');

        // 正規表現チェックが有効の場合に、正規表現入力欄が空の場合は submit させない
        $validateRegexCheck = $('#CuCustomFieldDefinitionValidateREGEXCHECK');
        if ($validateRegexCheck.prop('checked')) {
            $validateRegex = $('#CuCustomFieldDefinitionValidateRegex').val();
            if (!$validateRegex) {
                alert('正規表現入力欄が未入力です。');
                return false;
            }
        }
    });

    $("#CuCustomFieldDefinitionParentId").change(cuCustomFieldDefinitionParentIdChangeHandler);

    cuCustomFieldDefinitionParentIdChangeHandler();

    function cuCustomFieldDefinitionParentIdChangeHandler() {
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
    /**
     * タイプの値によってフィールドの表示設定を行う
     *
     * @param {string} value フィールドタイプ
     */
    function cuCustomFieldDefinitionFieldTypeChangeHandler() {
        var value = $("#CuCustomFieldDefinitionFieldType").val();

        // 管理システム表示設定の「初期値」、「入力欄前に表示」、「入力欄後に表示」、「このフィールドの説明文」行以外の行
        // この４つの行はほとんどのフィールドタイプで表示されるので、除外した行を取得
        $hideTrs = $('#CuCustomFieldDefinitionTable2')
            .find('tr')
            .not('#RowCuCfPrepend, #RowCuCfAppend, #RowCuCfDescription, #RowCuCfDefaultValue, #RowCuCfRequired')
            .hide();

        $("#RowCuCfParentId").show();
        $("#PreviewPrefList").hide();

        switch (value) {

            case 'radio':
                $("#RowCuCfDefaultValue").show();

                $("#RowCuCfChoices").show('slow');
                $("#RowCuCfSeparator").show('slow');
                $("#RowCuCfLabelName").hide();
                break;

            case 'checkbox':
                $("#RowCuCfDefaultValue").show();
                $("#RowCuCfLabelName").show();

                // バリデーション項目
                $("#RowCuCfValidate").hide('fast');
                $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").parent().hide('fast');
                $("#CuCustomFieldDefinitionValidateNUMERICCHECK").parent().hide('fast');
                $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").parent().show('fast');
                $('#CuCustomFieldDefinitionValidateREGEXCHECK').parent().hide('fast');
                $('#CuCfValidateRegexGroup').hide('fast');

                break;

            case 'multiple':
                $("#RowCuCfDefaultValue").show();

                // バリデーション項目
                $("#RowCuCfValidate").show('slow');
                $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").parent().hide('fast');
                $("#CuCustomFieldDefinitionValidateNUMERICCHECK").parent().hide('fast');
                $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").parent().show('slow');
                $('#CuCustomFieldDefinitionValidateREGEXCHECK').parent().hide('fast');
                $('#CuCfValidateRegexGroup').hide('fast');

                $("#RowCuCfChoices").show('slow');
                break;

            case 'pref':
                $("#PreviewPrefList").show();
                $("#RowCuCfDefaultValue").show();
                break;

            case 'wysiwyg':
                $("#RowCuCfParentId").hide();
                $("#RowCuCfRows").show('slow');
                $("#CuCfRows").show('slow');
                $("#CuCfRows").attr('placeholder', '200px');
                $("#CuCfCols").show('slow');
                $("#CuCfCols").attr('placeholder', '100%');
                $("#CuCfEditorToolType").show('slow');
                break;

            case 'googlemaps':
                $("#RowCuCfParentId").hide();
                $("#RowCuCfGoogleMaps").show('slow');
                break;

            case 'loop':
                $("#RowCuCfParentId").hide();
                $("#RowCuCfRequired").hide();
                $("#CuCustomFieldDefinitionParentId").val('');
                $("#CuCustomFieldDefinitionRequired").attr('checked', false);
                break;

        }
    }
});
