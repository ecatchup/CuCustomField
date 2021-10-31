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

    var fieldType = $("#CuCustomFieldDefinitionFieldType");
    var parentId = $("#CuCustomFieldDefinitionParentId");
    var name = $("#CuCustomFieldDefinitionName");
    var fieldName = $("#CuCustomFieldDefinitionFieldName");
    var validateRegex = $('#CuCustomFieldDefinitionValidateRegex');
    var showFieldNameList = $('#show_field_name_list');
    var validateRegexCheck = $('#CuCustomFieldDefinitionValidateREGEXCHECK');
    var btnSave = $("#BtnSave");

    fieldName.focus();
    fieldTypeChangeHandler();
    parentIdChangeHandler();

    // 編集画面のときのみ実行する（削除ボタンの有無で判定）
    if ($('#BtnDelete').html()) {
        $('#BeforeFieldName').hide();
        btnSave.click(function () {
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

    fieldType.change(fieldTypeChangeHandler);

    parentId.change(parentIdChangeHandler);
    // カスタムフィールド名、ラベル名、フィールド名の入力時、リアルタイムで重複チェックを行う
    name.keyup(checkDuplicateValueChangeHandler);

    fieldName.keyup(checkDuplicateValueChangeHandler);

    // 利用中フィールド名一覧を表示する
    showFieldNameList.change(function () {
        if ($(this).prop('checked')) {
            $('#FieldNameList').show('slow');
        } else {
            $('#FieldNameList').hide();
        }
    });

    // 正規表現入力欄が空欄になった際はメッセージを表示して入力促す
    validateRegex.change(function () {
        if (!$(this).val()) {
            $('#CheckValueResultValidateRegex').show('slow');
        } else {
            $('#CheckValueResultValidateRegex').hide();
        }
    });

    // 正規表現チェックのチェック時に、専用の入力欄を表示する
    validateRegexCheck.change(function () {
        $value = $(this).prop('checked');
        if ($value) {
            $('#CuCfValidateRegexGroup').show('slow');
        } else {
            $('#CuCfValidateRegexGroup').hide('high');
        }
    });

    /**
     * ループ行変更時処理
     */
    function parentIdChangeHandler() {
        var rowPrepend = $("#RowCuCfPrepend");
        var rowAppend = $("#RowCuCfAppend");
        var rowDescription = $("#RowCuCfDescription");
        var rowRequired = $("#RowCuCfRequired");
        if(parentId.val()) {
            rowPrepend.hide();
            rowAppend.hide();
            rowDescription.hide();
            rowRequired.hide();
            $("input[name='data[CuCustomFieldDefinition][required]']").prop('checked', false);
        } else {
            rowPrepend.show('slow');
            rowAppend.show('slow');
            rowDescription.show('slow');
            rowRequired.show('slow');
        }
    }

    /**
     * 重複があればメッセージを表示する
     */
    function checkDuplicateValueChangeHandler() {
        var fieldId = this.id;
        var options = {};
        var script = $("#CuCustomFieldDefinitionScript");
        // 本来であれば編集時のみ必要な値だが、actionによる条件分岐でビュー側に値を設定しなかった場合、
        // Controllerでの取得値が文字列での null となってしまうため、常に設定し取得している
        var id = script.attr('data-id');
        var configId = script.attr('data-config-id');

        switch (fieldId) {
            case 'CuCustomFieldDefinitionName':
                options = {
                    "data[CuCustomFieldDefinition][id]": id,
                    "data[CuCustomFieldDefinition][config_id]": configId,
                    "data[CuCustomFieldDefinition][name]": name.val()
                };
                break;
            case 'CuCustomFieldDefinitionFieldName':
                options = {
                    "data[CuCustomFieldDefinition][id]": id,
                    "data[CuCustomFieldDefinition][config_id]": configId,
                    "data[CuCustomFieldDefinition][field_name]": fieldName.val()
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

    /**
     * タイプの値によって入力欄の表示設定を行う
     */
    function fieldTypeChangeHandler(e) {
        $hideTrs = $('#CuCustomFieldDefinitionTable2')
            .find('tr')
            .not('#RowCuCfPrepend, #RowCuCfAppend, #RowCuCfDescription, #RowCuCfDefaultValue, #RowCuCfRequired')
            .hide();
        if(fieldType.val() === 'loop') {
            $("#RowCuCfParentId").hide();
            $("#RowCuCfDefaultValue").hide();
            $("#RowCuCfRequired").hide();
            parentId.val('');
            $("#CuCustomFieldDefinitionRequired").attr('checked', false);
        } else {
            $("#RowCuCfDefaultValue").show();
            $("#RowCuCfRequired").show();
            $("#RowCuCfParentId").show();
        }
        parentIdChangeHandler();
        if(e !== undefined) {
            // バリデーション系は値が残っていると意図しない処理になってしまうので切り替えの度に初期化
            $("#CuCustomFieldDefinitionValidateHANKAKUCHECK").attr('checked', false);
            $("#CuCustomFieldDefinitionValidateNUMERICCHECK").attr('checked', false);
            $("#CuCustomFieldDefinitionValidateREGEXCHECK").attr('checked', false);
            $("#CuCustomFieldDefinitionValidateNONCHECKCHECK").attr('checked', false);
            $("#CuCustomFieldDefinitionMaxLength").val('');
        }
    }

});
