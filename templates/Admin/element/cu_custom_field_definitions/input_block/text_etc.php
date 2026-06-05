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

$counterValue = $this->getRequest()->getData('CuCustomFieldDefinition.counter');
if ($counterValue === null) {
	$counterValue = $this->getRequest()->getData('data.CuCustomFieldDefinition.counter');
}
$counterChecked = !empty($counterValue);
?>


<tr id="RowCuCfSize">
	<th class="bca-form-table__label">
		その他の設定
	</th>
	<td class="bca-form-table__input">
		<span id="CuCfSize">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.size', '入力サイズ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.size', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 4, 'placeholder' => '60']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.size') ?>
		</span>
		<span id="CuCfMaxLength">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.max_length', '最大入力文字数') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.max_length', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 4, 'placeholder' => '255']) ?>
			<i class="bca-icon--question-circle btn help bca-help"></i>
			<div id="helptextCuCustomFieldDefinitionMaxLength" class="helptext">
				入力すると、指定文字数制限による入力チェックが行われます。
			</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.max_length') ?>
		</span>
		<span id="CuCfCounter">
			<span class="bca-checkbox">
				<input type="hidden" name="data[CuCustomFieldDefinition][counter]" id="CuCustomFieldDefinitionCounter_" value="0">
				<input type="checkbox" name="data[CuCustomFieldDefinition][counter]" class="bca-checkbox__input" value="1" id="CuCustomFieldDefinitionCounter"<?php if ($counterChecked): ?> checked<?php endif; ?>>
				<label for="CuCustomFieldDefinitionCounter" class="bca-checkbox__label">文字数カウンターを表示する</label>
			</span>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.counter') ?>
		</span>
	</td>
</tr>
