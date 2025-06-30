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
 * @var array $customFieldConfig
 */
?>


<tr id="RowCuCfValidate">
		<th class="bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate', '入力値チェック') ?>
		</th>
		<td class="bca-form-table__input">
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.validate', ['type' => 'select', 'multiple' => 'checkbox', 'options' => $customFieldConfig['validate']]) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate') ?>

			<div id="CuCfValidateRegexGroup" class="display-none" style="clear: both;">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate_regex', '正規表現入力') ?>&nbsp;<span
					class="required bca-label"
					data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.validate_regex',
					['type' => 'text', 'size' => 45, 'maxlength' => 255, 'placeholder' => '例：/^[a-z]+$/i']) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<div class="helptext">
					<ul>
						<li>正規表現（preg_match）を用いて入力データのチェックができます。/〜/ の形式で入力してください。</li>
						<li>ご入力の正規表現自体の正誤チェックは行いません。</li>
						<li>「エラー用文言」入力欄では、正規表現チェック時のエラーメッセージを指定できます。</li>
						<li>エラーメッセージの指定がない場合は「入力エラーが発生しました。」となります。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate_regex') ?>
				<br/>
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate_regex_message', 'エラー用文言') ?>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.validate_regex_message',
					['type' => 'text', 'size' => 49, 'maxlength' => 255, 'placeholder' => '入力エラーが発生しました。']) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate_regex_message') ?>
			</div>
		</td>
	</tr>
