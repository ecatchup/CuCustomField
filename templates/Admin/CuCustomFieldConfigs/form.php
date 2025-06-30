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
if (!empty($blogContentDatas) && count($blogContentDatas) > 0) {
	$hasAddableBlog = true;
}
/**
 * @var BcAppView $this
 */
if (empty($customFieldConfig)) {
	$customFieldConfig = \Cake\Core\Configure::read('cuCustomField');
}
?>
<script type="text/javascript">
	$(window).load(function () {
		$("#CuCustomFieldConfigFormPlace").focus();
	});
</script>

<?php if ($this->getRequest()->getParam('action') === 'add'): ?>
    <?php echo $this->BcAdminForm->create($data, ['url' => ['action' => 'add']]); ?>
<?php else: ?>
<?php echo $this->BcAdminForm->create($data, ['url' => ['action' => 'edit', $data->id]]); ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']); ?>
<?php echo $this->BcAdminForm->control('blog_content_id', ['type' => 'hidden']); ?>


<?php /*if ($this->getRequest()->action == 'add'): ?>
	<?php echo $this->BcForm->create('CuCustomFieldConfig', ['url' => ['action' => 'add']]) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.model', ['type' => 'hidden']) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('CuCustomFieldConfig', ['url' => ['action' => 'edit']]) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.id', ['type' => 'hidden']) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.model', ['type' => 'hidden']) */?>
<?php endif ?>

<?php if ($this->getRequest()->getAttribute('params')['action'] != 'add'): ?>
<p>
	<?php
	$contentsId = $data->content_id;
	$this->BcBaser->link($blogContentDatas[$contentsId] . ' 設定に移動',
		['admin' => true, 'plugin' => 'BcBlog', 'controller' => 'blog_contents', 'action' => 'edit', $contentsId],
		['class' => 'bca-btn']
	);
	?>
	&nbsp;&nbsp;
	<?php
	$this->BcBaser->link($blogContentDatas[$contentsId] . ' 記事一覧に移動',
		['admin' => true, 'plugin' => 'BcBlog', 'controller' => 'blog_posts', 'action' => 'index', $contentsId],
		['class' => 'bca-btn']
	);
	?>
</p>
<?php endif ?>

<div id="CuCustomFieldConfigTable" class="section">

<?php if ($hasAddableBlog): ?>
	<table id="FormTable" class="form-table bca-form-table">
        <?php if ($this->getRequest()->getParam('action') != 'add'): ?>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcAdminForm->label('CuCustomFieldConfigs.content_id	', 'NO') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php
					echo !empty($blogContentDatas[$contentsId]) ? h($blogContentDatas[$contentsId]): '';
					echo $this->BcAdminForm->control(
						'content_id',
						[
							'type' => 'hidden',
							'options' => $blogContentDatas,
							'empty' => false,
						]
					); ?>
				</td>
			</tr>
		<?php endif ?>
		<?php if ($this->getRequest()->getParam('action') == 'add'): ?>
			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcAdminForm->label('blog_content_id', __d('cu_custom_field', 'ブログ')); ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcAdminForm->control(
						'content_id',
						[
							'type' => 'select',
							'options' => $blogContentDatas,
							'empty' => false,
						]
					); ?>
					<?php echo $this->BcAdminForm->error('content_id'); ?>
				</td>
			</tr>
			<?php /*
			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcForm->label('CuCustomFieldConfigs.content_id', 'ブログ') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('CuCustomFieldConfigs.content_id', ['type' => 'select', 'options' => $blogContentDatas]) ?>
					<?php echo $this->BcForm->error('CuCustomFieldConfigs.content_id') ?>
				</td>
			</tr>
			*/?>
		<?php endif ?>

			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcAdminForm->label('status', 'カスタムフィールドの利用') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php
					echo $this->BcAdminForm->control(
						'status',
						[
							'type' => 'radio',
							'options' => $this->BcText->booleanDoList('利用')
						]);
					?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div class="bca-helptext"><?php echo __d('baser', 'ブログ記事でのカスタムフィールドの利用の有無を指定します。') ?></div>
					<?php echo $this->BcForm->error('status') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label">
					<?php echo $this->BcForm->label('form_place', 'カスタムフィールドの表示位置指定') ?>
				</th>
				<td class="col-input bca-form-table__input">
					<?php
					echo $this->BcAdminForm->control(
						'form_place',
						['type' => 'select', 'options' => $customFieldConfig['form_place']]);
						?>
					<?php echo $this->BcForm->error('form_place') ?>
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
			<?php echo $this->BcAdminForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item',
				'data-bca-btn-type' => 'save',
				'data-bca-btn-size' => 'lg',
				'data-bca-btn-width' => 'lg',]) ?>
			<?php endif ?>
		</div>
	</div>
<?php echo $this->BcAdminForm->end() ?>
<?php
if (\Cake\Core\Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>
