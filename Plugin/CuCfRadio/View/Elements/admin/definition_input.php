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


<tr id="RowCuCfSeparator">
	<th class="bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.separator', '区切り文字') ?>
	</th>
	<td class="bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.separator', ['type' => 'text', 'size' => 60, 'placeholder' => 'ラジオボタン表示の際の区切り文字を指定します']) ?>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.separator') ?>
	</td>
</tr>
