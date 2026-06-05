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

$validateValues = (array)($this->getRequest()->getData('CuCustomFieldDefinition.validate') ?? []);
$validateRegex = (string)($this->getRequest()->getData('CuCustomFieldDefinition.validate_regex') ?? '');
$validateRegexMessage = (string)($this->getRequest()->getData('CuCustomFieldDefinition.validate_regex_message') ?? '');

$validateOptions = (array)($customFieldConfig['validate'] ?? []);
$optionMap = [
	'HANKAKU_CHECK' => $validateOptions['HANKAKU_CHECK'] ?? '半角英数チェック',
	'NUMERIC_CHECK' => $validateOptions['NUMERIC_CHECK'] ?? '数字チェック',
	'NONCHECK_CHECK' => $validateOptions['NONCHECK_CHECK'] ?? 'チェックボックス未入力チェック',
	'REGEX_CHECK' => $validateOptions['REGEX_CHECK'] ?? '正規表現チェック',
];
?>


<tr id="RowCuCfValidate">
		<th class="bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate', '入力値チェック') ?>
		</th>
		<td class="bca-form-table__input">
			<span class="bca-checkbox-group">
				<input type="hidden" name="data[CuCustomFieldDefinition][validate]" value="" id="CuCustomFieldDefinitionValidate">

				<span class="bca-checkbox">
					<input type="checkbox" name="data[CuCustomFieldDefinition][validate][]" value="HANKAKU_CHECK" id="CuCustomFieldDefinitionValidateHANKAKUCHECK" class="bca-checkbox__input" <?php if (in_array('HANKAKU_CHECK', $validateValues, true)): ?>checked<?php endif; ?>>
					&nbsp;<label for="CuCustomFieldDefinitionValidateHANKAKUCHECK" class="bca-checkbox__label"><?php echo h($optionMap['HANKAKU_CHECK']) ?></label>
				</span>

				<span class="bca-checkbox">
					<input type="checkbox" name="data[CuCustomFieldDefinition][validate][]" value="NUMERIC_CHECK" id="CuCustomFieldDefinitionValidateNUMERICCHECK" class="bca-checkbox__input" <?php if (in_array('NUMERIC_CHECK', $validateValues, true)): ?>checked<?php endif; ?>>
					&nbsp;<label for="CuCustomFieldDefinitionValidateNUMERICCHECK" class="bca-checkbox__label"><?php echo h($optionMap['NUMERIC_CHECK']) ?></label>
				</span>

				<span class="bca-checkbox" style="display: none;">
					<input type="checkbox" name="data[CuCustomFieldDefinition][validate][]" value="NONCHECK_CHECK" id="CuCustomFieldDefinitionValidateNONCHECKCHECK" class="bca-checkbox__input" <?php if (in_array('NONCHECK_CHECK', $validateValues, true)): ?>checked<?php endif; ?>>
					&nbsp;<label for="CuCustomFieldDefinitionValidateNONCHECKCHECK" class="bca-checkbox__label"><?php echo h($optionMap['NONCHECK_CHECK']) ?></label>
				</span>

				<span class="bca-checkbox">
					<input type="checkbox" name="data[CuCustomFieldDefinition][validate][]" value="REGEX_CHECK" id="CuCustomFieldDefinitionValidateREGEXCHECK" class="bca-checkbox__input" <?php if (in_array('REGEX_CHECK', $validateValues, true)): ?>checked<?php endif; ?>>
					&nbsp;<label for="CuCustomFieldDefinitionValidateREGEXCHECK" class="bca-checkbox__label"><?php echo h($optionMap['REGEX_CHECK']) ?></label>
				</span>
			</span>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate') ?>

			<div id="CuCfValidateRegexGroup" class="display-none" style="clear: both;">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate_regex', '正規表現入力') ?>&nbsp;<span
					class="required bca-label"
					data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
				<span class="bca-textbox"><input name="data[CuCustomFieldDefinition][validate_regex]" size="45" maxlength="255" placeholder="例：/^[a-z]+$/i" class="bca-textbox__input" type="text" value="<?php echo h($validateRegex) ?>" id="CuCustomFieldDefinitionValidateRegex"></span>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<div class="helptext">
					<ul>
						<li>正規表現（preg_match）を用いて入力データのチェックができます。/〜/ の形式で入力してください。</li>
						<li>ご入力の正規表現自体の正誤チェックは行いません。</li>
						<li>「エラー用文言」入力欄では、正規表現チェック時のエラーメッセージを指定できます。</li>
						<li>エラーメッセージの指定がない場合は「入力エラーが発生しました。」となります。</li>
					</ul>
				</div>
				<span id="CheckValueResultValidateRegex" class="display-none">
					<div class="error-message duplicate-error-message">正規表現を入力してください。</div>
				</span>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate_regex') ?>
				<br/>
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.validate_regex_message', 'エラー用文言') ?>
				<span class="bca-textbox"><input name="data[CuCustomFieldDefinition][validate_regex_message]" size="49" maxlength="255" placeholder="入力エラーが発生しました。" class="bca-textbox__input" type="text" value="<?php echo h($validateRegexMessage) ?>" id="CuCustomFieldDefinitionValidateRegexMessage"></span>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.validate_regex_message') ?>
			</div>
		</td>
	</tr>
