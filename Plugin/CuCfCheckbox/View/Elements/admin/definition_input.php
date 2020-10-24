<?php
/**
 * CuCustomField : baserCMS Custom Field Checkbox Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfCheckbox.View
 * @license          MIT LICENSE
 */

/**
 * @var BcAppView $this
 */
?>


<tr id="RowCuCfLabelName">
	<th class="bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.label_name', 'その他の設定') ?>
	</th>
	<td class="bca-form-table__input" colspan="3">
		チェックボックスのラベル<br>
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.label_name',
			['type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'Webサイトで表示するタイトルを入力してください']) ?>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.label_name') ?>
	</td>
</tr>
