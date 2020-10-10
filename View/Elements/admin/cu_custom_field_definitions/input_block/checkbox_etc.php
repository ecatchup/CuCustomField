<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('label_name'); ?>">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.label_name', 'その他の設定') ?>
	</th>
	<td class="col-input bca-form-table__input" colspan="3">
		チェックボックスのラベル<br>
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.label_name',
			['type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'Webサイトで表示するタイトルを入力してください']) ?>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.label_name') ?>
	</td>
</tr>
