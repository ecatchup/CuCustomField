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

$hasAddableBlog = false;
if (count($blogContentDatas) > 0) {
	$hasAddableBlog = true;
}
/**
 * @var BcAppView $this
 */
?>
<script type="text/javascript">
	$(window).load(function () {
		$("#CuCustomFieldConfigFormPlace").focus();
	});
</script>

<?php if ($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('CuCustomFieldConfig', ['url' => ['action' => 'add']]) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.model', ['type' => 'hidden']) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('CuCustomFieldConfig', ['url' => ['action' => 'edit']]) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.id', ['type' => 'hidden']) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.model', ['type' => 'hidden']) ?>
<?php endif ?>

<?php if ($this->request->params['action'] != 'admin_add'): ?>
<p>
	<?php $this->BcBaser->link($blogContentDatas[$this->request->data['CuCustomFieldConfig']['content_id']] . ' 設定に移動',
		['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $this->request->data['CuCustomFieldConfig']['content_id']],
		['class' => 'bca-btn']
	) ?>
	&nbsp;&nbsp;
	<?php $this->BcBaser->link($blogContentDatas[$this->request->data['CuCustomFieldConfig']['content_id']] . ' 記事一覧に移動',
		['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', $this->request->data['CuCustomFieldConfig']['content_id']],
		['class' => 'bca-btn']
	) ?>
</p>
<?php endif ?>

<div id="CuCustomFieldConfigTable" class="section">

<?php if ($hasAddableBlog): ?>
	<table id="FormTable" class="form-table bca-form-table">
		<?php if ($this->request->params['action'] != 'admin_add'): ?>
			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcForm->label('CuCustomFieldConfig.id', 'NO') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->value('CuCustomFieldConfig.id') ?>
				</td>
			</tr>
		<?php endif ?>
			<?php if ($this->request->params['action'] == 'admin_add'): ?>
				<tr>
					<th class="col-head bca-form-table__label">
						<?php echo $this->BcForm->label('CuCustomFieldConfig.content_id', 'ブログ') ?>
					</th>
					<td class="col-input bca-form-table__input">
						<?php echo $this->BcForm->input('CuCustomFieldConfig.content_id', ['type' => 'select', 'options' => $blogContentDatas]) ?>
						<?php echo $this->BcForm->error('CuCustomFieldConfig.content_id') ?>
					</td>
				</tr>
			<?php endif ?>

			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcForm->label('CuCustomFieldConfig.status', 'カスタムフィールドの利用') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('CuCustomFieldConfig.status', ['type' => 'radio', 'options' => $this->BcText->booleanDoList('利用')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div class="helptext"><?php echo __d('baser', 'ブログ記事でのカスタムフィールドの利用の有無を指定します。') ?></div>
					<?php echo $this->BcForm->error('CuCustomFieldConfig.status') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcForm->label('CuCustomFieldConfig.form_place', 'カスタムフィールドの表示位置指定') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('CuCustomFieldConfig.form_place', ['type' => 'select', 'options' => $customFieldConfig['form_place']]) ?>
					<?php echo $this->BcForm->error('CuCustomFieldConfig.form_place') ?>
				</td>
			</tr>
	</table>
<?php else: ?>
<p>ブログが存在しないか、すでに全てのブログにカスタムフィールドを設定しているため、新しくカスタムフィールドを設定できるブログがありません。</p>
<?php endif ?>
</div>


	<!-- button -->
	<div class="submit bca-actions">
		<div class="bca-actions__main">
			<?php $this->BcBaser->link('一覧に戻る', ['action' => 'index'], [
				'class' => 'button bca-btn',
				'data-bca-btn-type' => 'back-to-list'
			]) ?>
			<?php if ($hasAddableBlog): ?>
			<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item',
				'data-bca-btn-type' => 'save',
				'data-bca-btn-size' => 'lg',
				'data-bca-btn-width' => 'lg',]) ?>
			<?php endif ?>
		</div>
	</div>
<?php echo $this->BcForm->end() ?>
<?php
if (Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>
