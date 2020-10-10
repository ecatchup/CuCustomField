<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('choices'); ?>">
	<th class="col-head bca-form-table__label">
		<?php echo $this->BcForm->label('CuCustomFieldDefinition.choices', '選択肢') ?>
	</th>
	<td class="col-input bca-form-table__input">
		<?php echo $this->BcForm->input('CuCustomFieldDefinition.choices', ['type' => 'textarea', 'rows' => '4', 'placeholder' => "選択肢を\n改行毎に\n入力します"]) ?>
		<?php echo $this->BcForm->error('CuCustomFieldDefinition.choices') ?>
		<i class="bca-icon--question-circle btn help bca-help"></i>
		<div id="helptextCuCustomFieldDefinitionChoices" class="helptext">
			<ul>
				<li>より細かく制御する場合は、ラベル名（キー）と値の両方を指定することができます。</li>
				<li>指定したいラベル名（キー）と値を半角「:」で区切って入力してください。</li>
				<li>（例：ラベル名:値）</li>
			</ul>
		</div>
	</td>
</tr>
