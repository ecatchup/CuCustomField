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
if(!empty($this->request->data['CuCustomFieldDefinition']['id'])) {
	$id = $this->request->data['CuCustomFieldDefinition']['id'];
}
$this->BcBaser->css('CuCustomField.admin/cu_custom_field', ['inline' => false]);
$this->BcBaser->js('CuCustomField.admin/cu_custom_field', false, ['id' => 'CuCustomFieldDefinitionScript',
	'data-id' => $id,
	'data-config-id' => $configId
]);
$currentModelName = $this->request->params['models']['CuCustomFieldDefinition']['className'];
$contentName = $this->BcText->arrayValue($contentId, $blogContentDatas);
?>


<p>
	<?php $this->BcBaser->link($contentName . ' 設定に移動',
		['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $contentId],
		['class' => 'bca-btn']
	) ?>
	&nbsp;&nbsp;
	<?php $this->BcBaser->link($contentName . ' 記事一覧に移動',
		['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', $contentId],
		['class' => 'bca-btn']
	) ?>
	&nbsp;&nbsp;
	<small><?php echo $this->BcForm->input('show_field_name_list', ['type' => 'checkbox', 'label' => '現在利用しているフィールド定義の名称一覧を表示']) ?></small>
</p>


<?php echo $this->BcForm->input('field_name_list', [
	'type' => 'select',
	'multiple' => true,
	'options' => $fieldNameList,
	'id' => 'FieldNameList',
	'style' => 'display:none;background:none'
]) ?>


<?php if ($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('CuCustomFieldDefinition', ['url' => ['action' => 'add', $configId]]) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('CuCustomFieldDefinition', ['url' => ['action' => 'edit', $configId, $this->request->data['CuCustomFieldDefinition']['id']]]) ?>
<?php endif ?>
<?php echo $this->BcForm->hidden('CuCustomFieldDefinition.config_id') ?>

<div id="AjaxCheckDuplicateUrl" class="display-none">
	<?php $this->BcBaser->url(['controller' => 'cu_custom_field_definitions', 'action' => 'ajax_check_duplicate']) ?>
</div>

<div id="ForeignId" class="display-none"><?php echo $this->request->data['CuCustomFieldDefinition']['id'] ?></div>

<section id="CuCustomFieldDefinitionTable" class="bca-section" data-bca-section-type='form-group'>
	<table id="CuCustomFieldDefinitionTable1" class="form-table bca-form-table">
<?php if ($this->request->action == 'admin_edit'): ?>
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
		<tr id="Row<?php echo $currentModelName . Inflector::camelize('field_name'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.field_name', 'フィールド定義名') ?>&nbsp;<span
					class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.field_name',
					['type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'フィールドを特定する一意の名称を半角英数で入力してください']) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.field_name') ?>
				<?php if ($this->request->action == 'admin_edit'): ?>
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
		<tr id="Row<?php echo $currentModelName . Inflector::camelize('name'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.name', '入力欄ラベル') ?>&nbsp;<span
					class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.name',
					['type' => 'text', 'size' => 60, 'maxlength' => 255, 'counter' => true, 'placeholder' => 'カスタムフィールドの入力欄に表示されるタイトルを入力してください']) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.name') ?>
				<div id="CheckValueResultName" class="display-none">
					<div class="error-message duplicate-error-message">同じカスタムフィールド名が存在します。変更してください。</div>
				</div>
			</td>
		</tr>
		<tr id="Row<?php echo $currentModelName . Inflector::camelize('field_type'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.field_type', 'フィールドタイプ') ?>&nbsp;<span
					class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input" colspan="3">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.field_type', ['type' => 'select', 'options' => $customFieldConfig['field_type']]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.field_type') ?>

				<span id="PreviewPrefList" class="display-none">
				&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->BcForm->label('preview_pref_list', '選択値対応表') ?>
					<?php echo $this->BcForm->input('preview_pref_list', ['type' => 'select', 'options' => $this->CuCustomField->previewPrefList()]) ?>
			</span>
			</td>
		</tr>
		<tr id="Row<?php echo $currentModelName . Inflector::camelize('status'); ?>">
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.status', '利用状態') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.status', ['type' => 'checkbox', 'label' => '利用中']) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.status') ?>
			</td>
		</tr>
	</table>
</section>

<section class="bca-section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg">フィールド表示設定</h2>
	<table id="CuCustomFieldDefinitionTable2" class="form-table bca-form-table">
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/parent_id', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/required', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/prepend', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/append', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/description', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/default_value', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/validate', ['currentModelName' => $currentModelName, 'customFieldConfig' => $customFieldConfig]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/placeholder', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/choices', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/auto_convert', ['currentModelName' => $currentModelName, 'customFieldConfig' => $customFieldConfig]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/text_etc', ['currentModelName' => $currentModelName]) ?>
		<?php $this->BcBaser->element('admin/cu_custom_field_definitions/input_block/textarea_etc', ['currentModelName' => $currentModelName, 'customFieldConfig' => $customFieldConfig]) ?>
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
	<?php if ($this->action == 'admin_edit'): ?>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $configId, $this->request->data['CuCustomFieldDefinition']['id']], [
				'class' => 'submit-token button bca-btn bca-actions__item',
				'data-bca-btn-type' => 'delete',
				'data-bca-btn-size' => 'sm'
			], sprintf('ID：%s のデータを削除して良いですか？', $this->BcForm->value('CuCustomFieldDefinition.name'))) ?>
		</div>
	<?php endif ?>
</div>


<?php echo $this->BcForm->end() ?>
<?php
if (Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>
