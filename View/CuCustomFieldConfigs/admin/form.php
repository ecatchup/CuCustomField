<?php
/**
 * [ADMIN] PetitBlogCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitBlogCustomField
 * @license			MIT
 */
$hasAddableBlog = false;
if (count($blogContentDatas) > 0) {
	$hasAddableBlog = true;
}
?>
<script type="text/javascript">
	$(window).load(function() {
		$("#CuCustomFieldConfigFormPlace").focus();
	});
</script>

<?php if($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('CuCustomFieldConfig', array('url' => array('action' => 'add'))) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.model', array('type' => 'hidden')) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('CuCustomFieldConfig', array('url' => array('action' => 'edit'))) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.id', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->input('CuCustomFieldConfig.model', array('type' => 'hidden')) ?>
<?php endif ?>

<?php if($this->request->params['action'] != 'admin_add'): ?>
<h2>
<?php $this->BcBaser->link($blogContentDatas[$this->request->data['CuCustomFieldConfig']['content_id']] .' ブログ設定編集はこちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents',
	'action' => 'edit', $this->request->data['CuCustomFieldConfig']['content_id']
)) ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php $this->BcBaser->link('≫記事一覧こちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts',
	'action' => 'index', $this->request->data['CuCustomFieldConfig']['content_id']
)) ?>
</h2>
<?php endif ?>

<div id="CuCustomFieldConfigTable" class="section">
<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
	<?php if($this->request->params['action'] != 'admin_add'): ?>
	<tr>
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldConfig.id', 'NO') ?>
		</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcForm->value('CuCustomFieldConfig.id') ?>
		</td>
	</tr>
	<?php endif ?>

	<?php if ($hasAddableBlog): ?>
		<?php if($this->request->params['action'] == 'admin_add'): ?>
		<tr>
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldConfig.content_id', 'ブログ') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('CuCustomFieldConfig.content_id', array('type' => 'select', 'options' => $blogContentDatas)) ?>
				<?php echo $this->BcForm->error('CuCustomFieldConfig.content_id') ?>
			</td>
		</tr>
		<?php endif ?>

		<tr>
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldConfig.status', 'カスタムフィールドの利用') ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpCuCustomFieldConfigStatus', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextCuCustomFieldConfigStatus" class="helptext">
					<ul>
						<li>ブログ記事でのカスタムフィールドの利用の有無を指定します。</li>
					</ul>
				</div>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('CuCustomFieldConfig.status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->BcForm->error('CuCustomFieldConfig.status') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('CuCustomFieldConfig.form_place', 'カスタムフィールドの表示位置指定') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('CuCustomFieldConfig.form_place', array('type' => 'select', 'options' => $customFieldConfig['form_place'])) ?>
				<?php echo $this->BcForm->error('CuCustomFieldConfig.form_place') ?>
			</td>
		</tr>
	<?php else: ?>
	<tr>
		<th class="col-head bca-form-table__label">
			<?php echo $this->BcForm->label('CuCustomFieldConfig.content_id', 'ブログ') ?>
		</th>
		<td class="col-input bca-form-table__input">
			追加設定可能なブログがありません。
		</td>
	</tr>
	<?php endif ?>
</table>
</div>

<?php if ($hasAddableBlog): ?>
<!-- button -->
<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',]) ?>
	</div>
</div>
<?php endif ?>
<?php echo $this->BcForm->end() ?>
<?php
if(Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>
