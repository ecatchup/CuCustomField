<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('append'); ?>">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.append', '入力欄後に表示') ?>
	</th>
	<td class="col-input bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.append', ['type' => 'text', 'size' => 60]) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div id="helptextCuCustomFieldDefinitionAppend" class="helptext">
			入力欄の後に表示される文字を指定できます。
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.append') ?>
	</td>
</tr>
