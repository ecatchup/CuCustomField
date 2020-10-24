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
 * @var array $customFieldConfig
 */
?>


<tr id="RowCuCfRows">
	<th class="bca-form-table__label">
		その他の設定
	</th>
	<td class="bca-form-table__input">
		<span id="CuCfRows">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.rows', '行数') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.rows', ['type' => 'text', 'size' => 5, 'placeholder' => '3']) ?>
			<i class="bca-icon--question-circle btn help bca-help"></i>
			<div id="helptextCuCustomFieldDefinitionRows" class="helptext">
				<ul>
					<li>テキストエリアの場合は行数指定となります。</li>
					<li>Wysiwyg Editorの場合はpx指定となります。</li>
				</ul>
			</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.rows') ?>
		</span>
		<span id="CuCfCols">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.cols', '横幅サイズ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.cols', ['type' => 'text', 'size' => 5, 'placeholder' => '40']) ?>
			<i class="bca-icon--question-circle btn help bca-help"></i>
			<div id="helptextCuCustomFieldDefinitionCols" class="helptext">
				<ul>
					<li>テキストエリアの場合は数値指定となります。</li>
					<li>Wysiwyg Editorの場合は％指定となります。</li>
				</ul>
			</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.cols') ?>
		</span>
		<span id="CuCfEditorToolType">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.editor_tool_type', 'Ckeditorのタイプ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.editor_tool_type', ['type' => 'select', 'options' => $customFieldConfig['editor_tool_type']]) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.editor_tool_type') ?>
		</span>
	</td>
</tr>
