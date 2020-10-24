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


<tr id="RowCuCfDescription">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.description', 'このフィールドの説明文') ?>
	</th>
	<td class="col-input bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.description', ['type' => 'textarea', 'rows' => '2']) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div id="helptextCuCustomFieldDefinitionDescription" class="helptext">
			<ul>
				<li>入力欄に説明文を指定できます。</li>
				<li>入力内容は、編集欄下部に表示されます。</li>
			</ul>
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.description') ?>
	</td>
</tr>
