<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 * @var array $customFieldConfig
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('auto_convert'); ?>">
	<th class="col-head bca-form-table__label">
		入力テキスト変換処理
	</th>
	<td class="col-input bca-form-table__input">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.auto_convert', '自動変換') ?>
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.auto_convert', ['type' => 'select', 'options' => $customFieldConfig['auto_convert']]) ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div id="helptextCuCustomFieldDefinitionAutoConvert" class="helptext">
			<ul>
				<li>半角変換を指定すると、「全角」英数字を「半角」に変換して保存します。</li>
				<li>フィールドタイプがテキスト、テキストエリアの際に変換処理は実行されます。</li>
			</ul>
		</div>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.auto_convert') ?>
	</td>
</tr>
