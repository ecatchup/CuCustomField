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


<tr id="Row<?php echo $currentModelName . Inflector::camelize('prepend'); ?>">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.prepend', '入力欄前に表示') ?>
	</th>
	<td class="col-input bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.prepend', ['type' => 'text', 'size' => 60]) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div id="helptextCuCustomFieldDefinitionPrepend" class="helptext">
			入力欄の前に表示される文字を指定できます。
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.prepend') ?>
	</td>
</tr>
