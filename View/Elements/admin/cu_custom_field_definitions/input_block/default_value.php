<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.View
 * @license          MIT LICENSE
 */

/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
// <<<< カスタムフィールドの 初期値 wysiwygやtextareaの場合は、入力欄をtextareaに変更
if(isset($this->request->data['CuCustomFieldDefinition']['field_type'])) {
	$type = $this->request->data['CuCustomFieldDefinition']['field_type'];
} else {
	$type = '';
}
$areaTypeArray = [
	'wysiwyg',
	'textarea'
];
if (in_array($type, $areaTypeArray)) {
	$options = [
		'type' => 'textArea',
		'lows' => 3,
		'class' => 'bca-textarea__textarea',
		'counter' => false,
	];
} else {
	$options = [
		'type' => 'text',
		'size' => 60,
		'maxlength' => 255,
		'counter' => true,
	];
}
$options['placeholder'] = 'カスタムフィールドの入力欄の初期値を指定します';
?>


<tr id="RowCuCfDefaultValue">
	<th class="bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.default_value', '初期値') ?>
	</th>
	<td class="bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.default_value', $options) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div class="helptext">
			<h5 class="weight-bold">ラジオボタン、セレクトボックスの場合</h5>
			<ul>
				<li>選択肢の入力内容のラベル名（キー）を指定してください。</li>
				<li>選択肢でラベル名（キー）と値を指定した場合は、値を指定してください。</li>
			</ul>
			<h5 class="weight-bold">チェックボックスの場合</h5>
			<ul>
				<li>「1」を指定すると、初期表示はチェックが入った状態になります。</li>
			</ul>
			<h5 class="weight-bold">マルチチェックボックスの場合</h5>
			<ul>
				<li>半角パイプ「|」で値を区切ると、初期表示は複数にチェックが入った状態になります。</li>
			</ul>
			<h5 class="weight-bold">都道府県リストの場合</h5>
			<ul>
				<li>選択値対応表の「値」を初期値として指定できます。</li>
			</ul>
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.default_value') ?>
	</td>
</tr>
