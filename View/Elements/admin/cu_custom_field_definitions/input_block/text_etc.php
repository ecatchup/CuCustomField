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
 */
?>


<tr id="RowCuCfSize">
	<th class="bca-form-table__label">
		その他の設定
	</th>
	<td class="bca-form-table__input">
		<span id="CuCfSize">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.size', '入力サイズ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.size', ['type' => 'text', 'size' => 4, 'placeholder' => '60']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.size') ?>
		</span>
		<span id="CuCfMaxLength">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.max_length', '最大入力文字数') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.max_length', ['type' => 'text', 'size' => 4, 'placeholder' => '255']) ?>
			<i class="bca-icon--question-circle btn help bca-help"></i>
			<div id="helptextCuCustomFieldDefinitionMaxLength" class="helptext">
				入力すると、指定文字数制限による入力チェックが行われます。
			</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.max_length') ?>
		</span>
		<span id="CuCfCounter">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.counter', ['type' => 'checkbox', 'label' => '文字数カウンターを表示する']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.counter') ?>
		</span>
	</td>
</tr>
