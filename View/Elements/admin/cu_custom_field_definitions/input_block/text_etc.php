<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('size'); ?>Group">
	<th class="col-head bca-form-table__label">
		その他の設定
	</th>
	<td class="col-input bca-form-table__input">
		<span id="Row<?php echo $currentModelName . Inflector::camelize('size'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.size', '入力サイズ') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.size', ['type' => 'text', 'size' => 4, 'placeholder' => '60']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.size') ?>
		</span>
		<span id="Row<?php echo $currentModelName . Inflector::camelize('max_lenght'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.max_length', '最大入力文字数') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.max_length', ['type' => 'text', 'size' => 4, 'placeholder' => '255']) ?>
			<i class="bca-icon--question-circle btn help bca-help"></i>
			<div id="helptextCuCustomFieldDefinitionMaxLength" class="helptext">
				入力すると、指定文字数制限による入力チェックが行われます。
			</div>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.max_length') ?>
		</span>
		<span id="Row<?php echo $currentModelName . Inflector::camelize('counter'); ?>">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.counter', ['type' => 'checkbox', 'label' => '文字数カウンターを表示する']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.counter') ?>
		</span>
	</td>
</tr>
