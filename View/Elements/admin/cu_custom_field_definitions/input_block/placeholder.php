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


<tr id="RowCuCfPlaceholder">
	<th class="bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.placeholder', 'プレースホルダー') ?>
	</th>
	<td class="bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.placeholder', ['type' => 'text', 'size' => 60, 'placeholder' => 'プレースホルダーを入力します']) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div class="helptext">
			入力欄内に未入力時に表示される文字を指定できます。
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.placeholder') ?>
	</td>
</tr>
