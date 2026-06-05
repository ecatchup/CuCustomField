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

$record = $data;
if (is_object($data) && method_exists($data, 'toArray')) {
	$record = $data->toArray();
} elseif (is_object($data)) {
	$record = get_mangled_object_vars($data);
}

$contentId = $record['content_id'] ?? ($record['blog_content_id'] ?? null);
$definitions = $record['cu_custom_field_definitions']
	?? $record['CuCustomFieldDefinitions']
	?? $record['CuCustomFieldDefinition']
	?? [];
$fieldCount = is_countable($definitions) ? count($definitions) : 0;
$contentTitle = $blogContentDatas[$contentId] ?? '';
?>
<tr<?php echo $class ?>>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--no"><?php // No ?>
		<?php echo $record['id'] ?? ''; ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--title"><?php // タイトル ?>
		<?php
		$this->BcBaser->link($contentTitle,
				[
					'controller' => 'cu_custom_field_definitions',
					'action' => 'index',
					$record['id'] ?? null
				],
				[
					'title' => 'フィールド管理'
				]);
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--hasCustomField"><?php // フィールド数 ?>
		<?php
		if (!$fieldCount) {
			$this->BcBaser->link(__d('baser', 'フィールド作成'),
				[
					'controller' => 'cu_custom_field_definitions',
					'action' => 'add',
					$record['id'] ?? null
				],
				[
					'class' => 'bca-btn',
					'data-bca-btn-type' => 'add',
					'data-bca-btn-size' => 'sm'
				]);
		} else {
			echo $fieldCount;
		}
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--form_place"><?php // form_place ?>
		<?php
		echo $this->BcText->arrayValue($record['form_place'] ?? null, $customFieldConfig['form_place'], '<small>指定なし</small>');
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--date"><?php // 投稿日 ?>
		<?php echo $this->BcTime->format($record['created'] ?? null, 'Y-m-d') ?>
		<br />
		<?php echo $this->BcTime->format($record['modified'] ?? null, 'Y-m-d') ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
		<?php
		//非公開
		$this->BcBaser->link('',
			[
				'action' => 'ajax_unpublish',
				$record['id'] ?? null
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
				$record['id'] ?? null
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
				$record['id'] ?? null
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
				$record['id'] ?? null
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
				$record['id'] ?? null
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
