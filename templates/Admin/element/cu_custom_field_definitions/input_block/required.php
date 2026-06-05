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

$requiredValue = $this->getRequest()->getData('CuCustomFieldDefinition.required');
if ($requiredValue === null) {
	$requiredValue = $this->getRequest()->getData('data.CuCustomFieldDefinition.required');
}
$requiredChecked = !empty($requiredValue);
?>


<tr id="RowCuCfRequired">
	<th class="bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.required', '必須設定') ?>
	</th>
	<td class="bca-form-table__input">
		<span class="bca-checkbox">
			<input type="hidden" name="data[CuCustomFieldDefinition][required]" id="CuCustomFieldDefinitionRequired_" value="0">
			<input type="checkbox" name="data[CuCustomFieldDefinition][required]" class="bca-checkbox__input" value="1" id="CuCustomFieldDefinitionRequired"<?php if ($requiredChecked): ?> checked<?php endif; ?>>
			<label for="CuCustomFieldDefinitionRequired" class="bca-checkbox__label">必須入力とする</label>
		</span>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.required') ?>
	</td>
</tr>
