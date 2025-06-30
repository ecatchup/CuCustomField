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


<?php if(!empty($loops)): ?>
<tr id="RowCuCfParentId">
	<th class="bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.parent_id', 'ループグループ') ?>
	</th>
	<td class="bca-form-table__input" colspan="3">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.parent_id', ['type' => 'select', 'options' => $loops, 'empty' => '指定しない']) ?>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.parent_id') ?>
	</td>
</tr>
<?php endif ?>
