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
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('required'); ?>">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.required', '必須設定') ?>
	</th>
	<td class="col-input bca-form-table__input"
		id="Row<?php echo $currentModelName . Inflector::camelize('required'); ?>">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.required', ['type' => 'checkbox', 'label' => '必須入力とする']) ?>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.required') ?>
	</td>
</tr>
