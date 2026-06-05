<?php
/**
 * CuCustomField : baserCMS Custom Field Related Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRelated.View
 * @license          MIT LICENSE
 */

/**
 * @var BcAppView $this
 */

$tableList = [];
try {
	$tableModel = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCfRelated.CuCfRelated');
	if (method_exists($tableModel, 'getTableList')) {
		$tableList = (array) $tableModel->getTableList();
	}
} catch (\Throwable $e) {
	$tableList = [];
}
?>


<tr id="RowCuCfRelated">
	<th class="bca-form-table__label">
		その他の設定
	</th>
	<td class="bca-form-table__input">
		<div nowrap>
		<span>
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.table', 'テーブル名') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.table', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 15, 'placeholder' => 'blog_posts', 'list' => 'CuCfRelatedTableList']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.table') ?>
			<datalist id="CuCfRelatedTableList">
				<?php foreach ($tableList as $table): ?>
					<option value="<?php echo h($table) ?>"></option>
				<?php endforeach; ?>
			</datalist>
		</span>
		<span>
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.title_field', 'リストに表示するフィールド') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.title_field', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 15, 'placeholder' => 'name']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.title_field') ?>
		</span>
		</div>
		<div nowrap>
		<span >
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.where_field', '絞り込みフィールド') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.where_field', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 15, 'placeholder' => 'blog_content_id']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.where_field') ?>
		</span>
		<span>
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.where_value', '絞り込み値') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.where_value', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => 15, 'placeholder' => '1']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.where_value') ?>
		</span>
		</div>
	</td>
</tr>
