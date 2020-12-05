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
 * @var int $count
 * @var array $data
 * @var array $datas
 */
$classies = [];
if (!$this->CuCustomField->allowPublish($data)) {
	$classies = ['unpublish', 'disablerow'];
} else {
	$classies = ['publish'];
}
$class = ' class="' . implode(' ', $classies) . '"';
?>


<tr<?php echo $class ?>>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--no"><?php // No ?>
		<?php echo $data['CuCustomFieldDefinition']['id']; ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--title"><?php // タイトル ?>
		<?php
		$this->BcBaser->link($data['CuCustomFieldDefinition']['name'],
			[
				'controller' => 'cu_custom_field_definitions',
				'action' => 'edit',
				$data['CuCustomFieldDefinition']['config_id'],
				$data['CuCustomFieldDefinition']['id']
			],
			[
				'title' => '編集'
			]);
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--hasCustomField"><?php // フィールド名 ?>
		<?php echo $data['CuCustomFieldDefinition']['field_name'] ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--hasCustomField"><?php // フィールドタイプ ?>
		<?php
		echo $this->CuCustomField->arrayValue($data['CuCustomFieldDefinition']['field_type'], $customFieldConfig['field_type'], '<small>未登録</small>');
		if ($data['CuCustomFieldDefinition']['field_type'] == 'wysiwyg') {
			echo '<br /><small>' . $this->CuCustomField->arrayValue($data['CuCustomFieldDefinition']['editor_tool_type'], $customFieldConfig['editor_tool_type'], '') . '</small>';
		}
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--form_place"><?php // 必須設定 ?>
		<?php
		if ($data['CuCustomFieldDefinition']['required']) {
			echo '<p class="annotation-text"><small>必須入力</small></p>';
		}
		?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
		<?php
		// 非公開
		$this->BcBaser->link('',
			[
				'action' => 'ajax_unpublish',
				$data['CuCustomFieldDefinition']['id']
			],
			[
				'title' => __d('baser', '非公開'),
				'class' => 'btn-unpublish bca-btn-icon',
				'data-bca-btn-type' => 'unpublish', 'data-bca-btn-size' => 'lg'
			]);
		// 公開
		$this->BcBaser->link('',
			[
				'action' => 'ajax_publish',
				$data['CuCustomFieldDefinition']['id']
			],
			[
				'title' => __d('baser', '公開'),
				'class' => 'btn-publish bca-btn-icon',
				'data-bca-btn-type' => 'publish',
				'data-bca-btn-size' => 'lg'
			]);
		// 編集
		$this->BcBaser->link('',
			[
				'controller' => 'cu_custom_field_definitions',
				'action' => 'edit',
				$data['CuCustomFieldConfig']['id'],
				$data['CuCustomFieldDefinition']['id']
			],
			[
				'title' => __d('baser', '編集'),
				'class' => ' bca-btn-icon',
				'data-bca-btn-type' => 'edit',
				'data-bca-btn-size' => 'lg'
			]);
		// 削除
		$this->BcBaser->link('',
			[
				'action' => 'ajax_delete',
				$data['CuCustomFieldConfig']['id'],
				$data['CuCustomFieldDefinition']['id']
			],
			[
				'title' => __d('baser', '削除'),
				'class' => 'btn-delete bca-btn-icon',
				'data-bca-btn-type' => 'delete',
				'data-bca-btn-size' => 'lg'
			]);

		// 並び替えはconfigIdで絞り込んだ画面で有効化する
		if ($this->request->params['pass']) {
			$faArrowUp = '<i class="fa fa-arrow-up fa-2x" style="vertical-align: bottom;margin-left: 2px;"></i>';
			$faArrowDown = '<i class="fa fa-arrow-down fa-2x" style="vertical-align: bottom;margin-left: 2px;"></i>';
			if ($this->CuCustomField->isAvailableDefinitionMoveUp($datas, $count -1)) {
				$this->BcBaser->link($faArrowUp, [
					'controller' => 'cu_custom_field_definitions',
					'action' => 'move_up',
					$data['CuCustomFieldConfig']['id'],
					$data['CuCustomFieldDefinition']['id']
				], [
					'class' => 'btn-up',
					'title' => '上へ移動'
				]);
			} else {
				$this->BcBaser->link($faArrowUp, [
					'controller' => 'cu_custom_field_definitions',
					'action' => 'move_up',
					$data['CuCustomFieldConfig']['id'],
					$data['CuCustomFieldDefinition']['id']
				], [
					'class' => 'btn-up',
					'title' => '上へ移動',
					'style' => 'display:none'
				]);
//				if (count($datas) > 2) {
//					//最下段へ移動
//					$this->BcBaser->link('<i class="fa fa-arrow-circle-down fa-2x" style="vertical-align: bottom;margin-left: 2px;"></i>',
//							[
//								'controller' => 'cu_custom_field_definitions',
//								'action' => 'move_down',
//								$data['CuCustomFieldConfig']['id'],
//								$data['CuCustomFieldDefinition']['id'],
//								'tobottom'
//							],
//							[
//								'class' => 'btn-down',
//								'title' => '最下段へ移動'
//							]);
//				}
			}
		}
		if ($this->CuCustomField->isAvailableDefinitionMoveDown($datas, $count -1)) {
			$this->BcBaser->link($faArrowDown, [
				'controller' => 'cu_custom_field_definitions',
				'action' => 'move_down',
				$data['CuCustomFieldConfig']['id'],
				$data['CuCustomFieldDefinition']['id']
			], [
				'class' => 'btn-down',
				'title' => '下へ移動'
			]);
		} else {
			$this->BcBaser->link($faArrowDown, [
				'controller' => 'cu_custom_field_definitions',
				'action' => 'move_down',
				$data['CuCustomFieldConfig']['id'],
				$data['CuCustomFieldDefinition']['id']
			], [
				'class' => 'btn-down',
				'title' => '下へ移動',
				'style' => 'display:none'
			]);
//			if (count($datas) > 2) {
//				//最上段へ移動
//				$this->BcBaser->link('<i class="fa fa-arrow-circle-up fa-2x" style="vertical-align: bottom;margin-left: 2px;"></i>',
//					[
//						'controller' => 'cu_custom_field_definitions',
//						'action' => 'move_up',
//						$data['CuCustomFieldConfig']['id'],
//						$data['CuCustomFieldDefinition']['id'],
//						'totop'
//					],
//					[
//						'class' => 'btn-up',
//						'title' => '最上段へ移動'
//					]);
//			}
		}
		?>
	</td>
</tr>
