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
// debug($blogContentDatas);
// debug($data);
/**
 * @var BcAppView $this
 */
$classies = array();
if (!$this->CuCustomField->allowPublish($data, 'CuCustomFieldConfig')) {
	$classies = array('unpublish', 'disablerow');
} else {
	$classies = array('publish');
}
$class=' class="'.implode(' ', $classies).'"';
?>
<tr<?php echo $class ?>>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--no"><?php // No ?>
		<?php echo $data['id']; ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--title"><?php // タイトル ?>
		<?php
		$this->BcBaser->link($this->BcBaser->arrayValue($data['content_id'], $blogContentDatas, ''),
				[
					'controller' => 'cu_custom_field_definitions',
					'action' => 'index',
					$data['id']
				],
				[
					'title' => 'フィールド管理'
				]);
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--hasCustomField"><?php // フィールド数 ?>
		<?php
		if (!$this->CuCustomField->hasCustomField($data)) {
			$this->BcBaser->link(__d('baser', 'フィールド作成'),
				[
					'controller' => 'cu_custom_field_definitions',
					'action' => 'add',
					$data['id']
				],
				[
					'class' => 'bca-btn',
					'data-bca-btn-type' => 'add',
					'data-bca-btn-size' => 'sm'
				]);
		} else {
			echo count($data['CuCustomFieldDefinition']);
		}
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--form_place"><?php // form_place ?>
		<?php
		echo $this->BcText->arrayValue($data['form_place'], $customFieldConfig['form_place'], '<small>指定なし</small>');
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--date"><?php // 投稿日 ?>
		<?php echo $this->BcTime->format($data['created'], 'Y-m-d') ?>
		<br />
		<?php echo $this->BcTime->format($data['modified'], 'Y-m-d') ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
		<?php
		//非公開
		$this->BcBaser->link('',
			[
				'action' => 'ajax_unpublish',
				$data['id']
			],
			[
				'title' => __d('baser', '非公開'),
				'class' => 'btn-unpublish bca-btn-icon',
				'data-bca-btn-type' => 'unpublish',
				'data-bca-btn-size' => 'lg'
		]);
		//公開
		$this->BcBaser->link('',
			[
				'action' => 'ajax_publish',
				$data['id']
			],
			[
				'title' => __d('baser', '公開'),
				'class' => 'btn-publish bca-btn-icon',
				'data-bca-btn-type' => 'publish',
				'data-bca-btn-size' => 'lg'
			]);
		//フィールド管理
		$this->BcBaser->link('',
			[
				'controller' => 'cu_custom_field_definitions',
				'action' => 'index',
				$data['id']
			],
			[
				'title' => __d('baser', 'フィールド管理'),
				'class' => ' bca-btn-icon',
				'data-bca-btn-type' => 'th-list',
				'data-bca-btn-size' => 'lg'
			]);
		//編集
		$this->BcBaser->link('',
			[
				'action' => 'edit',
				$data['id']
			],
			[
				'title' => __d('baser', '編集'),
				'class' => ' bca-btn-icon',
				'data-bca-btn-type' => 'edit',
				'data-bca-btn-size' => 'lg'
			]);
		//削除
		$this->BcBaser->link('',
			[
				'action' => 'ajax_delete',
				$data['id']
			],
			[
				'title' => __d('baser', '削除'),
				'class' => 'btn-delete bca-btn-icon',
				'data-bca-btn-type' => 'delete',
				'data-bca-btn-size' => 'lg'
			]);
		?>
	</td>
</tr>
