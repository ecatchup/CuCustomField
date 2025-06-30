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
?>


<?php echo $this->BcForm->create('CuCustomFieldConfig', array('url' => array('action' => 'index'))) ?>
<p>
	<span>
		<?php echo $this->BcForm->label('CuCustomFieldConfig.content_id', 'ブログ') ?>
		&nbsp;<?php echo $this->BcForm->input('CuCustomFieldConfig.content_id', array('type' => 'select', 'options' => $blogContentDatas)) ?>
	</span>
	<span>
		<?php echo $this->BcForm->label('CuCustomFieldConfig.status', '利用状態') ?>
		&nbsp;<?php echo $this->BcForm->input('CuCustomFieldConfig.status', array('type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし')) ?>
	</span>
</p>

<?php if ($this->BcBaser->siteConfig['admin_theme'] == 'admin-third'): ?>
<div class="button bca-search__btns">
	<div class="bca-search__btns-item">
		<?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-btn-lg', 'data-bca-btn-size'=>"lg"]) ?>
		</div>
	<div class="bca-search__btns-item">
		<?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn']) ?>
	</div>
</div>
<?php else: ?>
<div class="button">
	<?php echo $this->BcForm->submit('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn'), array('div' => false, 'id' => 'BtnSearchSubmit')) ?>
</div>
<?php endif; ?>
<?php echo $this->BcForm->end() ?>
