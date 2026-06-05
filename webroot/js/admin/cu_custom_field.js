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

    var fieldType = $("#CuCustomFieldDefinitionFieldType, [name='data[CuCustomFieldDefinition][field_type]'], [name='CuCustomFieldDefinition[field_type]']").first();
    var parentId = $("#CuCustomFieldDefinitionParentId, [name='data[CuCustomFieldDefinition][parent_id]'], [name='CuCustomFieldDefinition[parent_id]']").first();
    var name = $("#CuCustomFieldDefinitionName");
    var fieldName = $("#CuCustomFieldDefinitionFieldName");
    var validateRegex = $("#CuCustomFieldDefinitionValidateRegex, [name='data[CuCustomFieldDefinition][validate_regex]'], [name='CuCustomFieldDefinition[validate_regex]']").first();
    var showFieldNameList = $('#show_field_name_list');
    var validateRegexCheck = $("#CuCustomFieldDefinitionValidateREGEXCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='REGEX_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='REGEX_CHECK']").first();
    var btnSave = $("#BtnSave");

    function isFieldType(type) {
        var value = (fieldType.val() || '').toString();
        return value === type;
    }

    function isFieldTypeIn(types) {
        var value = (fieldType.val() || '').toString();
        return $.inArray(value, types) !== -1;
    }

    function toggleValidateOptions(showHalf, showNumeric, showRegex, showNoncheck) {
        $("#CuCustomFieldDefinitionValidateHANKAKUCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='HANKAKU_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='HANKAKU_CHECK']").parent().toggle(!!showHalf);
        $("#CuCustomFieldDefinitionValidateNUMERICCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='NUMERIC_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='NUMERIC_CHECK']").parent().toggle(!!showNumeric);
        $("#CuCustomFieldDefinitionValidateREGEXCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='REGEX_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='REGEX_CHECK']").parent().toggle(!!showRegex);
        $("#CuCustomFieldDefinitionValidateNONCHECKCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='NONCHECK_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='NONCHECK_CHECK']").parent().toggle(!!showNoncheck);
        if (!showRegex) {
            setRegexGroupVisible(false);
        }
    }

    function setRegexGroupVisible(visible) {
        var group = $('#CuCfValidateRegexGroup');
        if (!group.length) return;
        if (visible) {
            group.removeClass('display-none').css('display', 'block');
        } else {
            group.addClass('display-none').css('display', 'none');
        }
    }

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

    fieldType.change(function (e) {
        // 他のJSの切替処理が先に走っても最終状態を上書きする
        setTimeout(function () {
            fieldTypeChangeHandler(e);
        }, 0);
    });

    parentId.change(function () {
        setTimeout(function () {
            parentIdChangeHandler();
            fieldTypeChangeHandler();
        }, 0);
    });
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

    // submit時の処理
    btnSave.click(function () {
        // 正規表現チェックが有効の場合に、正規表現入力欄が空の場合は submit させない
        if (validateRegexCheck.prop('checked')) {
            $validateRegex = validateRegex.val();
            if (!$validateRegex) {
                alert('正規表現入力欄が未入力です。');
                return false;
            }
        }
    });

    // 正規表現チェックのチェック時に、専用の入力欄を表示する
    $(document).on('change', "#CuCustomFieldDefinitionValidateREGEXCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='REGEX_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='REGEX_CHECK']", function () {
        setRegexGroupVisible($(this).prop('checked'));
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
        if(isFieldType('loop')) {
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

        var hasParent = !!parentId.val();

        if (isFieldTypeIn(['text', 'textarea'])) {
            $("#RowCuCfPlaceholder").show();
            $("#RowCuCfSize").show();
            $("#CuCfSize").show();
            $("#CuCfMaxLength").show();
            $("#CuCfCounter").show();
        }

        if (isFieldType('textarea')) {
            $("#RowCuCfRows").show();
            $("#CuCfRows").show().attr('placeholder', '3');
            $("#CuCfCols").show().attr('placeholder', '40');
            $("#CuCfEditorToolType").hide();
            $("#CuCfSize").hide();
            $("#CuCfMaxLength").hide();
        }

        if (isFieldType('wysiwyg')) {
            $("#RowCuCfRows").show();
            $("#RowCuCfParentId").hide();
            $("#CuCfRows").show().attr('placeholder', '200px');
            $("#CuCfCols").show().attr('placeholder', '100%');
            $("#CuCfEditorToolType").show();
        }

        if (isFieldTypeIn(['select', 'radio', 'multiple', 'multiCheckbox', 'pref'])) {
            $("#RowCuCfChoices").show();
        }

        if (isFieldType('radio')) {
            $("#RowCuCfSeparator").show();
        }

        if (isFieldType('checkbox')) {
            $("#RowCuCfLabelName").show();
        }

        if (isFieldType('related')) {
            $("#RowCuCfRelated").show();
        }

        if (isFieldType('googlemaps')) {
            $("#RowCuCfParentId").hide();
            $("#RowCuCfGoogleMaps").show();
        }

        if (isFieldType('file')) {
            $("#RowCuCfDefaultValue").hide();
        }

        if (!hasParent && isFieldTypeIn(['text', 'textarea', 'multiple', 'multiCheckbox'])) {
            $("#RowCuCfValidate").show();
            if (isFieldTypeIn(['multiple', 'multiCheckbox'])) {
                toggleValidateOptions(false, false, false, true);
            } else {
                toggleValidateOptions(true, true, true, false);
                if (validateRegexCheck.prop('checked')) {
                    setRegexGroupVisible(true);
                }
            }
        } else {
            $("#RowCuCfValidate").hide();
            setRegexGroupVisible(false);
        }

        if (!hasParent && isFieldTypeIn(['text', 'textarea'])) {
            $("#RowCuCfAutoConvert").show();
        }

        if (isFieldType('pref')) {
            $("#PreviewPrefList").show();
        } else {
            $("#PreviewPrefList").hide();
        }

        parentIdChangeHandler();
        if(e !== undefined) {
            // バリデーション系は値が残っていると意図しない処理になってしまうので切り替えの度に初期化
            $("#CuCustomFieldDefinitionValidateHANKAKUCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='HANKAKU_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='HANKAKU_CHECK']").prop('checked', false);
            $("#CuCustomFieldDefinitionValidateNUMERICCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='NUMERIC_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='NUMERIC_CHECK']").prop('checked', false);
            $("#CuCustomFieldDefinitionValidateREGEXCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='REGEX_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='REGEX_CHECK']").prop('checked', false);
            $("#CuCustomFieldDefinitionValidateNONCHECKCHECK, input[name='data[CuCustomFieldDefinition][validate][]'][value='NONCHECK_CHECK'], input[name='CuCustomFieldDefinition[validate][]'][value='NONCHECK_CHECK']").prop('checked', false);
            $("#CuCustomFieldDefinitionMaxLength").val('');
        }
    }

});
