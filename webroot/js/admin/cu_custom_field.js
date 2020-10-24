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

    $fieldType = $("#CuCustomFieldDefinitionFieldType").val();
    cuCustomFieldDefinitionFieldTypeChangeHandler($fieldType);

    // タイプを選択すると入力するフィールドが切り替わる
    $("#CuCustomFieldDefinitionFieldType").change(function () {
        cuCustomFieldDefinitionFieldTypeChangeHandler($("#CuCustomFieldDefinitionFieldType").val());
    });

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
            $('#CuCustomFieldDefinitionValidateRegexBox').show('slow');
        } else {
            $('#CuCustomFieldDefinitionValidateRegexBox').hide('high');
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
        var prepend = $("#RowCuCustomFieldDefinitionPrepend");
        var append = $("#RowCuCustomFieldDefinitionAppend");
        var description = $("#RowCuCustomFieldDefinitionDescription");
        if($("#CuCustomFieldDefinitionParentId").val()) {
            prepend.hide();
            append.hide();
            description.hide();
        } else {
            prepend.show('slow');
            append.show('slow');
            description.show('slow');
        }
    }
    /**
     * タイプの値によってフィールドの表示設定を行う
     *
     * @param {string} value フィールドタイプ
     */
    function cuCustomFieldDefinitionFieldTypeChangeHandler(value) {
        $configTable1 = $('#CuCustomFieldDefinitionTable1');
        $configTable2 = $('#CuCustomFieldDefinitionTable2');

        // 管理システム表示設定の「初期値」、「入力欄前に表示」、「入力欄後に表示」、「このフィールドの説明文」行以外の行
        // この４つの行はほとんどのフィールドタイプで表示されるので、除外した行を取得
        $hideTrs = $configTable2.find('tr').not('#RowCuCustomFieldDefinitionPrepend, '
            + '#RowCuCustomFieldDefinitionAppend, #RowCuCustomFieldDefinitionDescription, '
            + '#RowCuCustomFieldDefinitionDefaultValue, #RowCuCustomFieldDefinitionLabelName');

        $defaultValue = $("#RowCuCustomFieldDefinitionDefaultValue");
        $previewPrefList = $("#PreviewPrefList");
        $validateGroup = $("#RowCuCustomFieldDefinitionValidateGroup");
        $validateHankaku = $("#CuCustomFieldDefinitionValidateHANKAKUCHECK");
        $validateNumeric = $("#CuCustomFieldDefinitionValidateNUMERICCHECK");
        $validateNonCheckCheck = $("#CuCustomFieldDefinitionValidateNONCHECKCHECK");
        $validateRegex = $('#CuCustomFieldDefinitionValidateREGEXCHECK');
        $validateRegexBox = $('#CuCustomFieldDefinitionValidateRegexBox');
        $sizeGroup = $("#RowCuCustomFieldDefinitionSizeGroup");
        $relatedGroup = $("#RowCuCustomFieldDefinitionRelatedGroup");
        $size = $("#RowCuCustomFieldDefinitionSize");
        $maxLength = $("#RowCuCustomFieldDefinitionMaxLenght");
        $labelName = $("#RowCuCustomFieldDefinitionLabelName");
        $counter = $("#RowCuCustomFieldDefinitionCounter");
        $placeholder = $("#RowCuCustomFieldDefinitionPlaceholder");
        $rowsGroup = $("#RowCuCustomFieldDefinitionRowsGroup");
        $rows = $("#CuCustomFieldDefinitionRows");
        $cols = $("#CuCustomFieldDefinitionCols");
        $editorToolType = $("#RowCuCustomFieldDefinitionEditorToolType");
        $choices = $("#RowCuCustomFieldDefinitionChoices");
        $separator = $("#RowCuCustomFieldDefinitionSeparator");
        $autoConvert = $("#RowCuCustomFieldDefinitionAutoConvert");
        $googlemapsGroup = $("#RowCuCustomFieldDefinitionGoogleMapsGroup");
        $parentId = $("#RowCuCustomFieldDefinitionParentId");
        $required = $("#RowCuCustomFieldDefinitionRequired");

        $hideTrs.hide();
        $previewPrefList.hide();
        $defaultValue.hide();
        $labelName.hide();
        $parentId.show();
        $required.show();

        switch (value) {
            case 'text':
                $defaultValue.show();

                // バリデーション項目
                $validateGroup.show('slow');
                $validateHankaku.parent().show('slow');
                $validateNumeric.parent().show('slow');
                $validateNonCheckCheck.parent().hide('fast');
                $validateRegex.parent().show('slow');
                // 正規表現チェックが有効に指定されている場合は、専用の入力欄を表示する
                if ($validateRegex.prop('checked')) {
                    $validateRegexBox.show('fast');
                }

                $sizeGroup.show('slow');
                $size.show('slow');
                $maxLength.show('slow');
                $counter.show('slow');
                $placeholder.show('slow');
                $autoConvert.show('slow');
                break;

            case 'textarea':
                $defaultValue.show();

                // バリデーション項目
                $validateGroup.show('slow');
                $validateHankaku.parent().show('slow');
                $validateNumeric.parent().show('slow');
                $validateNonCheckCheck.parent().hide('fast');
                $validateRegex.parent().show('slow');
                // 正規表現チェックが有効に指定されている場合は、専用の入力欄を表示する
                if ($validateRegex.prop('checked')) {
                    $validateRegexBox.show('fast');
                }

                $sizeGroup.show('slow');
                $size.hide('fast');
                $maxLength.hide('fast');
                $counter.show('slow');

                $placeholder.show('slow');

                $rowsGroup.show('slow');
                $rows.show('slow');
                $rows.attr('placeholder', '3');
                $cols.show('slow');
                $cols.attr('placeholder', '40');
                $editorToolType.hide('fast');

                $autoConvert.show('slow');
                $labelName.hide();
                break;

            case 'date':
            case 'datetime':
                $parentId.hide();
                $defaultValue.show();
                break;

            case 'select':
                $defaultValue.show();
                $choices.show('slow');
                $labelName.hide();
                break;

            case 'radio':
                $defaultValue.show();

                $choices.show('slow');
                $separator.show('slow');
                $labelName.hide();
                break;

            case 'checkbox':
                $defaultValue.show();
                $labelName.show();

                // バリデーション項目
                $validateGroup.hide('fast');
                $validateHankaku.parent().hide('fast');
                $validateNumeric.parent().hide('fast');
                $validateNonCheckCheck.parent().show('fast');
                $validateRegex.parent().hide('fast');
                $validateRegexBox.hide('fast');

                break;

            case 'multiple':
                $defaultValue.show();

                // バリデーション項目
                $validateGroup.show('slow');
                $validateHankaku.parent().hide('fast');
                $validateNumeric.parent().hide('fast');
                $validateNonCheckCheck.parent().show('slow');
                $validateRegex.parent().hide('fast');
                $validateRegexBox.hide('fast');

                $choices.show('slow');
                break;

            case 'pref':
                $previewPrefList.show();
                $defaultValue.show();
                break;

            case 'wysiwyg':
                $parentId.hide();
                $rowsGroup.show('slow');
                $rows.show('slow');
                $rows.attr('placeholder', '200px');
                $cols.show('slow');
                $cols.attr('placeholder', '100%');
                $editorToolType.show('slow');
                break;

            case 'googlemaps':
                $parentId.hide();
                $googlemapsGroup.show('slow');
                break;

            case 'loop':
                $parentId.hide();
                $required.hide();
                $("#CuCustomFieldDefinitionParentId").val('');
                $("#CuCustomFieldDefinitionRequired").attr('checked', false);
                break;

            case 'related':
                $relatedGroup.show();
                $defaultValue.show();
                break;
        }
    }
});
