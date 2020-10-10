<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('placeholder'); ?>">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.placeholder', 'プレースホルダー') ?>
	</th>
	<td class="col-input bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.placeholder', ['type' => 'text', 'size' => 60, 'placeholder' => 'プレースホルダーを入力します']) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div id="helptextCuCustomFieldDefinitionPlaceholder" class="helptext">
			入力欄内に未入力時に表示される文字を指定できます。
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.placeholder') ?>
	</td>
</tr>
