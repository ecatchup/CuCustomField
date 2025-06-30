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
?>


<tr id="RowCuCfRelated">
	<th class="bca-form-table__label">
		その他の設定
	</th>
	<td class="bca-form-table__input">
		<div nowrap>
		<span>
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.table', 'テーブル名') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.table', ['type' => 'text', 'size' => 15, 'placeholder' => 'blog_posts']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.table') ?>
		</span>
		<span>
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.title_field', 'リストに表示するフィールド') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.title_field', ['type' => 'text', 'size' => 15, 'placeholder' => 'name']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.title_field') ?>
		</span>
		</div>
		<div nowrap>
		<span >
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.where_field', '絞り込みフィールド') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.where_field', ['type' => 'text', 'size' => 15, 'placeholder' => 'blog_content_id']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.where_field') ?>
		</span>
		<span>
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.related.where_value', '絞り込み値') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.related.where_value', ['type' => 'text', 'size' => 15, 'placeholder' => '1']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.related.where_value') ?>
		</span>
		</div>
	</td>
</tr>
