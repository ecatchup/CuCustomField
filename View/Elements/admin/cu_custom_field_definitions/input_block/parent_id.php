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


<?php if(!empty($loops)): ?>
		<tr id="Row<?php echo $currentModelName . Inflector::camelize('parent_id'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.parent_id', 'ループグループ') ?>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.parent_id', ['type' => 'select', 'options' => $loops]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.parent_id') ?>
			</td>
		</tr>
<?php endif ?>
