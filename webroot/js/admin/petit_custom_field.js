/**
 * [ADMIN] PetitCustomField
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			PetitCustomField
 * @license			MIT
 */
/**
 * プチカスタムフィールド用のJS処理
 */
$(function(){
	$fieldType = $("#PetitCustomFieldConfigFieldFieldType").val();
	petitCustomFieldConfigFieldFieldTypeChangeHandler($fieldType);
	// タイプを選択すると入力するフィールドが切り替わる
	$("#PetitCustomFieldConfigFieldFieldType").change(function(){
		petitCustomFieldConfigFieldFieldTypeChangeHandler($("#PetitCustomFieldConfigFieldFieldType").val());
	});
	
	// カスタムフィールド名の入力時、ラベル名が空の場合は名称を自動で入力する
	$("#PetitCustomFieldConfigFieldName").change(function(){
		$labelName = $("#PetitCustomFieldConfigFieldLabelName");
		var labelNameValue = $labelName.val();
		if(!labelNameValue){
			$labelName.val($("#PetitCustomFieldConfigFieldName").val());
		}
	});
	
	// 利用中フィールド名一覧を表示する
	$('#show_field_name_list').change(function() {
		if ($(this).prop('checked')) {
			$('#FieldNameList').show('slow');
		} else {
			$('#FieldNameList').hide();
		}
	});
	
	// カスタムフィールド名、ラベル名、フィールド名の入力時、リアルタイムで重複チェックを行う
	$("#PetitCustomFieldConfigFieldName").keyup(checkDuplicateValueChengeHandler);
	$("#PetitCustomFieldConfigFieldLabelName").keyup(checkDuplicateValueChengeHandler);
	$("#PetitCustomFieldConfigFieldFieldName").keyup(checkDuplicateValueChengeHandler);
	// 重複があればメッセージを表示する
	function checkDuplicateValueChengeHandler() {
		var fieldId = this.id;
		var options = {};
		// 本来であれば編集時のみ必要な値だが、actionによる条件分岐でビュー側に値を設定しなかった場合、
		// Controllerでの取得値が文字列での null となってしまうため、常に設定し取得している
		var foreignId = $("#ForeignId").html();
		
		switch (fieldId) {
			case 'PetitCustomFieldConfigFieldName':
				options = {
					"data[PetitCustomFieldConfigField][foreign_id]": foreignId,
					"data[PetitCustomFieldConfigField][name]": $("#PetitCustomFieldConfigFieldName").val()
				};
				break;
			case 'PetitCustomFieldConfigFieldLabelName':
				options = {
					"data[PetitCustomFieldConfigField][foreign_id]": foreignId,
					"data[PetitCustomFieldConfigField][label_name]": $("#PetitCustomFieldConfigFieldLabelName").val()
				};
				break;
			case 'PetitCustomFieldConfigFieldFieldName':
				options = {
					"data[PetitCustomFieldConfigField][foreign_id]": foreignId,
					"data[PetitCustomFieldConfigField][field_name]": $("#PetitCustomFieldConfigFieldFieldName").val()
				};
				break;
		}
		$.ajax({
			type: "POST",
			data: options,
			url: $("#AjaxCheckDuplicateUrl").html(),
			dataType: "html",
			cache: false,
			success: function(result, status, xhr) {
				if(status === 'success') {
					if(!result) {
						if (fieldId === 'PetitCustomFieldConfigFieldName') {
							$('#CheckValueResultName').show('fast');
						}
						if (fieldId === 'PetitCustomFieldConfigFieldLabelName') {
							$('#CheckValueResultLabelName').show('fast');
						}
						if (fieldId === 'PetitCustomFieldConfigFieldFieldName') {
							$('#CheckValueResultFieldName').show('fast');
						}
					} else {
						if (fieldId === 'PetitCustomFieldConfigFieldName') {
							$('#CheckValueResultName').hide('fast');
						}
						if (fieldId === 'PetitCustomFieldConfigFieldLabelName') {
							$('#CheckValueResultLabelName').hide('fast');
						}
						if (fieldId === 'PetitCustomFieldConfigFieldFieldName') {
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
		$("#BtnSave").click(function(){
			$beforeFieldName = $('#BeforeFieldName').html();
			$inputFieldName = $('#PetitCustomFieldConfigFieldFieldName').val();
			if ($beforeFieldName !== $inputFieldName) {
				if(!confirm('フィールド名を変更した場合、これまでの記事でこのフィールドに入力していた内容は引き継がれません。\n本当によろしいですか？')) {
					$('#BeforeFieldNameComment').css('visibility', 'visible');
					$('#BeforeFieldName').show();
					return false;
				}
			}
		});
	}
	
	// 正規表現チェックのチェック時に、専用の入力欄を表示する
	$('#PetitCustomFieldConfigFieldValidateREGEXCHECK').change(function() {
		$value = $(this).prop('checked');
		if ($value) {
			$('#PetitCustomFieldConfigFieldValidateRegexBox').show('slow');
		} else {
			$('#PetitCustomFieldConfigFieldValidateRegexBox').hide('high');
		}
	});
	
	// 正規表現入力欄が空欄になった際はメッセージを表示して入力促す
	$('#PetitCustomFieldConfigFieldValidateRegex').change(function() {
		if (!$(this).val()) {
			$('#CheckValueResultValidateRegex').show('slow');
		} else {
			$('#CheckValueResultValidateRegex').hide();
		}
	});
	
	// submit時の処理
	$("#BtnSave").click(function(){
		// 都道府県の選択値対応表は送らないようにする
		$('#PetitCustomFieldConfigFieldPreviewPrefList').attr('disabled', 'disabled');
		
		// 正規表現チェックが有効の場合に、正規表現入力欄が空の場合は submit させない
		$validateRegexCheck = $('#PetitCustomFieldConfigFieldValidateREGEXCHECK');
		if ($validateRegexCheck.prop('checked')) {
			$validateRegex = $('#PetitCustomFieldConfigFieldValidateRegex').val();
			if (!$validateRegex) {
				alert('正規表現入力欄が未入力です。');
				return false;
			}
		}
	});
	
/**
 * タイプの値によってフィールドの表示設定を行う
 * 
 * @param {string} value フィールドタイプ
 */
	function petitCustomFieldConfigFieldFieldTypeChangeHandler(value){
		$configTable1 = $('#PetitCustomFieldConfigFieldTable1');
		$configTable2 = $('#PetitCustomFieldConfigFieldTable2');

		// 管理システム表示設定の「初期値」、「入力欄前に表示」、「入力欄後に表示」、「このフィールドの説明文」行以外の行
		// この４つの行はほとんどのフィールドタイプで表示されるので、除外した行を取得
		$hideTrs = $configTable2.find('tr').not('#RowPetitCustomFieldConfigFieldPrepend, '
			+ '#RowPetitCustomFieldConfigFieldAppend, #RowPetitCustomFieldConfigFieldDescription, '
			+ '#RowPetitCustomFieldConfigFieldDefaultValue');

		$defaultValue = $("#RowPetitCustomFieldConfigFieldDefaultValue");
			$previewPrefList = $("#PreviewPrefList");
		$validateGroup = $("#RowPetitCustomFieldConfigFieldValidateGroup");
			$validateHankaku = $("#PetitCustomFieldConfigFieldValidateHANKAKUCHECK");
			$validateNumeric = $("#PetitCustomFieldConfigFieldValidateNUMERICCHECK");
			$validateNonCheckCheck = $("#PetitCustomFieldConfigFieldValidateNONCHECKCHECK");
			$validateRegex = $('#PetitCustomFieldConfigFieldValidateREGEXCHECK');
				$validateRegexBox = $('#PetitCustomFieldConfigFieldValidateRegexBox');
		$sizeGroup = $("#RowPetitCustomFieldConfigFieldSizeGroup");
			$size = $("#RowPetitCustomFieldConfigFieldSize");
			$maxLength = $("#RowPetitCustomFieldConfigFieldMaxLenght");
			$counter = $("#RowPetitCustomFieldConfigFieldCounter");
		$placeholder = $("#RowPetitCustomFieldConfigFieldPlaceholder");
		$rowsGroup = $("#RowPetitCustomFieldConfigFieldRowsGroup");
			$rows = $("#PetitCustomFieldConfigFieldRows");
			$cols = $("#PetitCustomFieldConfigFieldCols");
			$editorToolType = $("#RowPetitCustomFieldConfigFieldEditorToolType");
		$choices = $("#RowPetitCustomFieldConfigFieldChoices");
		$separator = $("#RowPetitCustomFieldConfigFieldSeparator");
		$autoConvert = $("#RowPetitCustomFieldConfigFieldAutoConvert");
		$googlemapsGroup = $("#RowPetitCustomFieldConfigFieldGoogleMapsGroup");
		
		switch (value){
			case 'text':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
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
				$hideTrs.hide('fast');
				$previewPrefList.hide();
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
				break;

			case 'date':
			case 'datetime':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
				$defaultValue.show();
				break;

			case 'select':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
				$defaultValue.show();

				$choices.show('slow');
				break;

			case 'radio':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
				$defaultValue.show();

				$choices.show('slow');
				$separator.show('slow');
				break;

			case 'checkbox':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
				$defaultValue.show();

				// バリデーション項目
				$validateGroup.hide('fast');
					$validateHankaku.parent().hide('fast');
					$validateNumeric.parent().hide('fast');
					$validateNonCheckCheck.parent().show('fast');
					$validateRegex.parent().hide('fast');
						$validateRegexBox.hide('fast');

				break;
				
			case 'multiple':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
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
				$hideTrs.hide('fast');
				$previewPrefList.show();
				$defaultValue.show();
				break;
				
			case 'wysiwyg':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
				$defaultValue.hide();
				
				$rowsGroup.show('slow');
					$rows.show('slow');
						$rows.attr('placeholder', '200px');
					$cols.show('slow');
						$cols.attr('placeholder', '100%');
					$editorToolType.show('slow');
				break;

			case 'googlemaps':
				$hideTrs.hide('fast');
				$previewPrefList.hide();
				$defaultValue.hide();

				$googlemapsGroup.show('slow');
				break;
		}
	}
});
