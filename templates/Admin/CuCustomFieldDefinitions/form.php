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
 * @var int $contentId
 * @var array $blogContentDatas
 * @var int $configId
 * @var array $fieldNameList
 */
$id = null;
if(!empty($this->getRequest()->getData('CuCustomFieldDefinition.id'))) {
	$id = $this->getRequest()->getData('CuCustomFieldDefinition.id');
}
$this->BcBaser->css('CuCustomField.admin/cu_custom_field', false);
$this->BcBaser->js('CuCustomField.admin/cu_custom_field', false, ['id' => 'CuCustomFieldDefinitionScript',
	'data-id' => $id,
	'data-config-id' => $configId
]);
$currentModelName = 'CuCustomFieldDefinition';
$customFieldConfig = $customFieldConfig ?? \Cake\Core\Configure::read('cuCustomField');
$contentName = $this->BcText->arrayValue($contentId, $blogContentDatas);

$showFieldNameListValue = $this->getRequest()->getData('show_field_name_list');
if ($showFieldNameListValue === null) {
	$showFieldNameListValue = $this->getRequest()->getData('data.show_field_name_list');
}
$showFieldNameListChecked = !empty($showFieldNameListValue);

$statusValue = $this->getRequest()->getData('CuCustomFieldDefinition.status');
if ($statusValue === null) {
	$statusValue = $this->getRequest()->getData('data.CuCustomFieldDefinition.status');
}
$statusChecked = !empty($statusValue);
?>


<p>
	<?php $this->BcBaser->link($contentName . ' 設定に移動',
		['admin' => true, 'plugin' => 'BcBlog', 'controller' => 'BlogContents', 'action' => 'edit', $contentId],
		['class' => 'bca-btn']
	) ?>
	&nbsp;&nbsp;
	<?php $this->BcBaser->link($contentName . ' 記事一覧に移動',
		['admin' => true, 'plugin' => 'BcBlog', 'controller' => 'BlogPosts', 'action' => 'index', $contentId],
		['class' => 'bca-btn']
	) ?>
	&nbsp;&nbsp;
	<small>
		<span class="bca-checkbox">
			<input type="hidden" name="show_field_name_list" id="show_field_name_list_" value="0">
			<input type="checkbox" name="show_field_name_list" class="bca-checkbox__input" value="1" id="show_field_name_list"<?php if ($showFieldNameListChecked): ?> checked<?php endif; ?>>
			<label for="show_field_name_list" class="bca-checkbox__label">現在利用しているフィールド定義の名称一覧を表示</label>
		</span>
	</small>
</p>


<?php echo $this->BcForm->input('field_name_list', [
	'type' => 'select',
	'multiple' => true,
	'class' => 'bca-select__select',
	'options' => $fieldNameList,
	'id' => 'FieldNameList',
	'style' => 'display:none;background:none'
]) ?>


<?php if ($this->getRequest()->getParam('action') == 'add'): ?>
	<?php echo $this->BcForm->create(null, ['url' => ['action' => 'add', $configId]]) ?>
<?php else: ?>
	<?php echo $this->BcForm->create(null, ['url' => ['action' => 'edit', $configId, $this->getRequest()->getData('CuCustomFieldDefinition.id')]]) ?>
<?php endif ?>
<?php
foreach ([
	'data.CuCustomFieldDefinition.counter',
	'data.CuCustomFieldDefinition.required',
	'data.CuCustomFieldDefinition.status',
	'data.CuCustomFieldDefinition.validate',
	'data.CuCustomFieldDefinition.validate_regex',
	'data.CuCustomFieldDefinition.validate_regex_message',
] as $unlockField) {
	$this->BcForm->unlockField($unlockField);
}
?>
<?php echo $this->BcForm->hidden('CuCustomFieldDefinition.config_id') ?>

<div id="AjaxCheckDuplicateUrl" class="display-none">
	<?php $this->BcBaser->url(['controller' => 'cu_custom_field_definitions', 'action' => 'ajax_check_duplicate']) ?>
</div>

<div id="ForeignId" class="display-none"><?php echo $this->getRequest()->getData('CuCustomFieldDefinition.id') ?></div>

<section id="CuCustomFieldDefinitionTable" class="bca-section" data-bca-section-type='form-group'>
	<table id="CuCustomFieldDefinitionTable1" class="form-table bca-form-table">
<?php if ($this->getRequest()->getParam('action') == 'edit'): ?>
		<tr>
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.id', 'ID') ?>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->value('CuCustomFieldDefinition.id') ?>
				<?php echo $this->BcForm->hidden('CuCustomFieldDefinition.id') ?>
			</td>
		</tr>
<?php endif ?>
		<tr id="Row<?php echo $currentModelName . \Cake\Utility\Inflector::camelize('field_name'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.field_name', 'フィールド定義名') ?>&nbsp;<span
					class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.field_name',
					['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'フィールドを特定する一意の名称を半角英数で入力してください']) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.field_name') ?>
				<?php if ($this->getRequest()->getParam('action') == 'edit'): ?>
				<p>
					<span id="BeforeFieldNameComment">変更前のフィールド定義名：</span>
					<span id="BeforeFieldName"><?php echo $this->BcForm->value('CuCustomFieldDefinition.field_name') ?></span>
				</p>
				<?php endif ?>
				<div id="CheckValueResultFieldName" class="display-none">
					<div class="error-message duplicate-error-message">同じフィールド名が存在します。変更してください。</div>
				</div>
			</td>
		</tr>
		<tr id="Row<?php echo $currentModelName . \Cake\Utility\Inflector::camelize('name'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.name', '入力欄ラベル') ?>&nbsp;<span
					class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.name',
					['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'カスタムフィールドの入力欄に表示されるタイトルを入力してください']) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.name') ?>
				<div id="CheckValueResultName" class="display-none">
					<div class="error-message duplicate-error-message">同じカスタムフィールド名が存在します。変更してください。</div>
				</div>
			</td>
		</tr>
		<tr id="Row<?php echo $currentModelName . \Cake\Utility\Inflector::camelize('field_type'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.field_type', 'フィールドタイプ') ?>&nbsp;<span
					class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.field_type', ['type' => 'select', 'class' => 'bca-select__select', 'options' => $customFieldConfig['field_type']]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.field_type') ?>

				<span id="PreviewPrefList" class="display-none">
				&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->BcForm->label('preview_pref_list', '選択値対応表') ?>
					<?php echo $this->BcForm->input('preview_pref_list', ['type' => 'select', 'options' => $this->CuCustomField->previewPrefList()]) ?>
			</span>
			</td>
		</tr>
		<tr id="Row<?php echo $currentModelName . \Cake\Utility\Inflector::camelize('status'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.status', '利用状態') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<span class="bca-checkbox">
					<input type="hidden" name="data[CuCustomFieldDefinition][status]" id="CuCustomFieldDefinitionStatus_" value="0">
					<input type="checkbox" name="data[CuCustomFieldDefinition][status]" class="bca-checkbox__input" value="1" id="CuCustomFieldDefinitionStatus"<?php if ($statusChecked): ?> checked<?php endif; ?>>
					<label for="CuCustomFieldDefinitionStatus" class="bca-checkbox__label">利用中</label>
				</span>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.status') ?>
			</td>
		</tr>
	</table>
</section>

<section class="bca-section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg">フィールド表示設定</h2>
	<table id="CuCustomFieldDefinitionTable2" class="form-table bca-form-table">
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/parent_id', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/required', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/prepend', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/append', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/description', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/default_value', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/validate', ['currentModelName' => $currentModelName, 'customFieldConfig' => $customFieldConfig]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/placeholder', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/choices', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/auto_convert', ['currentModelName' => $currentModelName, 'customFieldConfig' => $customFieldConfig]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/text_etc', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('cu_custom_field_definitions/input_block/textarea_etc', ['currentModelName' => $currentModelName, 'customFieldConfig' => $customFieldConfig]) ?>
		<?php $this->CuCustomField->loadPluginDefinitionInputs() ?>
	</table>
</section>


<!-- button -->
<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php $this->BcBaser->link('一覧に戻る',
			['controller' => 'cu_custom_field_definitions', 'action' => 'index', $configId],
			['class' => 'bca-btn  bca-actions__item', 'data-bca-btn-type' => 'back-to-list']
		) ?>
		<?php
		echo $this->BcForm->button(__d('baser', '保存'),
			[
				'div' => false,
				'class' => 'button bca-btn bca-actions__item',
				'data-bca-btn-type' => 'save',
				'data-bca-btn-size' => 'lg',
				'data-bca-btn-width' => 'lg',
			]);
		?>
	</div>
	<?php if ($this->getRequest()->getParam('action') == 'edit'): ?>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $configId, $this->getRequest()->getData('CuCustomFieldDefinition.id')], [
				'class' => 'submit-token button bca-btn bca-actions__item',
				'data-bca-btn-type' => 'delete',
				'data-bca-btn-size' => 'sm'
			], sprintf('ID：%s のデータを削除して良いですか？', $this->BcForm->value('CuCustomFieldDefinition.name'))) ?>
		</div>
	<?php endif ?>
</div>


<?php echo $this->BcForm->end() ?>
<?php
if (\Cake\Core\Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>

<script>
(function () {
	function first(selector) {
		return document.querySelector(selector);
	}

	function getFieldTypeEl() {
		return first("#CuCustomFieldDefinitionFieldType")
			|| first("[name='data[CuCustomFieldDefinition][field_type]']")
			|| first("[name='CuCustomFieldDefinition[field_type]']")
			|| first("select[name$='[field_type]']");
	}

	function getParentIdEl() {
		return first("#CuCustomFieldDefinitionParentId")
			|| first("[name='data[CuCustomFieldDefinition][parent_id]']")
			|| first("[name='CuCustomFieldDefinition[parent_id]']")
			|| first("select[name$='[parent_id]']");
	}

	function isChecked(selector) {
		var el = first(selector);
		return !!(el && el.checked);
	}

	function show(id, visible) {
		var el = document.getElementById(id);
		if (!el) return;
		el.style.display = visible ? '' : 'none';
	}

	function hideAllRows() {
		var table = document.getElementById('CuCustomFieldDefinitionTable2');
		if (!table) return;
		var rows = table.querySelectorAll('tr');
		for (var i = 0; i < rows.length; i++) {
			rows[i].style.display = 'none';
		}
	}

	function applyView() {
		var fieldTypeEl = getFieldTypeEl();
		if (!fieldTypeEl) return;
		var parentIdEl = getParentIdEl();
		var fieldType = (fieldTypeEl.value || '').trim();
		var hasParent = !!(parentIdEl && parentIdEl.value);

		hideAllRows();

		show('RowCuCfPrepend', true);
		show('RowCuCfAppend', true);
		show('RowCuCfDescription', true);

		if (fieldType !== 'loop') {
			show('RowCuCfParentId', true);
			show('RowCuCfDefaultValue', true);
			show('RowCuCfRequired', true);
		}

		if (fieldType === 'loop') {
			show('RowCuCfParentId', false);
			show('RowCuCfDefaultValue', false);
			show('RowCuCfRequired', false);
		}

		if (fieldType === 'text' || fieldType === 'textarea') {
			show('RowCuCfPlaceholder', true);
			show('RowCuCfSize', true);
		}

		if (fieldType === 'textarea') {
			show('RowCuCfRows', true);
			show('CuCfSize', false);
			show('CuCfMaxLength', false);
			show('CuCfCounter', true);
			show('CuCfRows', true);
			show('CuCfCols', true);
			show('CuCfEditorToolType', false);
		}

		if (fieldType === 'wysiwyg') {
			show('RowCuCfParentId', false);
			show('RowCuCfRows', true);
			show('CuCfRows', true);
			show('CuCfCols', true);
			show('CuCfEditorToolType', true);
		}

		if (fieldType === 'select' || fieldType === 'radio' || fieldType === 'multiple' || fieldType === 'multiCheckbox' || fieldType === 'pref') {
			show('RowCuCfChoices', true);
		}

		if (fieldType === 'radio') {
			show('RowCuCfSeparator', true);
		}

		if (fieldType === 'checkbox') {
			show('RowCuCfLabelName', true);
		}

		if (fieldType === 'related') {
			show('RowCuCfRelated', true);
		}

		if (fieldType === 'googlemaps') {
			show('RowCuCfParentId', false);
			show('RowCuCfGoogleMaps', true);
		}

		if (fieldType === 'file') {
			show('RowCuCfDefaultValue', false);
		}

		if (!hasParent && (fieldType === 'text' || fieldType === 'textarea' || fieldType === 'multiple' || fieldType === 'multiCheckbox')) {
			show('RowCuCfValidate', true);
		}

		if (!hasParent && (fieldType === 'text' || fieldType === 'textarea')) {
			show('RowCuCfAutoConvert', true);
		}

		show('PreviewPrefList', fieldType === 'pref');
		show('CuCfValidateRegexGroup', isChecked("#CuCustomFieldDefinitionValidateREGEXCHECK") || isChecked("input[name='data[CuCustomFieldDefinition][validate][]'][value='REGEX_CHECK']") || isChecked("input[name='CuCustomFieldDefinition[validate][]'][value='REGEX_CHECK']"));
	}

	var lastType = null;
	var lastParent = null;

	function tick() {
		var typeEl = getFieldTypeEl();
		if (!typeEl) return;
		var parentEl = getParentIdEl();
		var type = typeEl.value || '';
		var parent = parentEl ? (parentEl.value || '') : '';
		if (type !== lastType || parent !== lastParent) {
			lastType = type;
			lastParent = parent;
			applyView();
		}
	}

	document.addEventListener('change', function () {
		setTimeout(applyView, 0);
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			applyView();
			setInterval(tick, 300);
		});
	} else {
		applyView();
		setInterval(tick, 300);
	}
})();
</script>
