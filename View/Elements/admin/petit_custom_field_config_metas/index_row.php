<?php
/**
 * [ADMIN] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
$classies = array();
if (!$this->PetitCustomField->allowPublish($data)) {
	$classies = array('unpublish', 'disablerow');
} else {
	$classies = array('publish');
}
$class=' class="'.implode(' ', $classies).'"';
?>

<tr<?php echo $class ?>>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--no"><?php // No ?>
		<?php echo $data['PetitCustomFieldConfigMeta']['position']; ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--title"><?php // タイトル ?>
		<?php 
		$this->BcBaser->link($data['PetitCustomFieldConfigField']['name'],
				[
					'controller' => 'petit_custom_field_config_fields', 
					'action' => 'edit', 
					$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
					$data['PetitCustomFieldConfigMeta']['field_foreign_id']
				], 
				[
					'title' => '編集'
				]); 
		?>
		<br />
		<small><?php echo $data['PetitCustomFieldConfigField']['label_name'] ?></small>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--hasCustomField"><?php // フィールド名 ?>
		<?php echo $data['PetitCustomFieldConfigField']['field_name'] ?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--hasCustomField"><?php // フィールドタイプ ?>
		<?php
		echo $this->PetitCustomField->arrayValue($data['PetitCustomFieldConfigField']['field_type'], $customFieldConfig['field_type'], '<small>未登録</small>');
		if ($data['PetitCustomFieldConfigField']['field_type'] == 'wysiwyg') {
			echo '<br /><small>'. $this->PetitCustomField->arrayValue($data['PetitCustomFieldConfigField']['editor_tool_type'], $customFieldConfig['editor_tool_type'], ''). '</small>';
		}
		?>
	</td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--form_place"><?php // 必須設定 ?>
		<?php
		if ($data['PetitCustomFieldConfigField']['required']) {
			echo '<p class="annotation-text"><small>必須入力</small></p>';
		}
		?>
		<small>
			<?php
			echo $this->PetitCustomField->arrayValue($data['PetitCustomFieldConfigField']['auto_convert'], $customFieldConfig['auto_convert'], '未登録');
			?>
		</small>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
		<?php
		// 非公開
		$this->BcBaser->link('', 
			[
				'action' => 'ajax_unpublish', 
				$data['PetitCustomFieldConfigMeta']['id'], 
				$data['PetitCustomFieldConfig']['id']
			], 
			[
				'title' => __d('baser', '非公開'), 
				'class' => 'btn-unpublish bca-btn-icon', 
				'data-bca-btn-type' => 'unpublish','data-bca-btn-size' => 'lg'
			]);
		// 公開
		$this->BcBaser->link('', 
			[
				'action' => 'ajax_publish', 
				$data['PetitCustomFieldConfigMeta']['id'], 
				$data['PetitCustomFieldConfig']['id']
			], 
			[
				'title' => __d('baser', '公開'), 
				'class' => 'btn-publish bca-btn-icon', 
				'data-bca-btn-type' => 'publish',
				'data-bca-btn-size' => 'lg'
			]);
		// 管理
		$this->BcBaser->link('', 
			[
				'controller' => 'petit_custom_field_config_metas',
				'action' => 'edit', 
				$data['PetitCustomFieldConfigMeta']['id'], 
				$data['PetitCustomFieldConfigMeta']['id']
			], 
			[
				'title' => __d('baser', '管理'), 
				'class' => ' bca-btn-icon', 
				'data-bca-btn-type' => 'setting',
				'data-bca-btn-size' => 'lg'
			]);
		// 編集
		$this->BcBaser->link('', 
			[
				'controller' => 'petit_custom_field_config_fields', 
				'action' => 'edit', 
				$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
				$data['PetitCustomFieldConfigMeta']['field_foreign_id']
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
				$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
				$data['PetitCustomFieldConfigMeta']['id']
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
			if ($count != 1 || !isset($datas)) {
				$this->BcBaser->link($faArrowUp,
						[
							'controller' => 'petit_custom_field_config_metas', 
							'action' => 'move_up', 
							$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
							$data['PetitCustomFieldConfigMeta']['id']
						], 
						[
							'class' => 'btn-up', 
							'title' => '上へ移動'
						]);
			} else {
				$this->BcBaser->link($faArrowUp,
						[
							'controller' => 'petit_custom_field_config_metas', 
							'action' => 'move_up', 
							$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
							$data['PetitCustomFieldConfigMeta']['id']
						], 
						[
							'class' => 'btn-up', 
							'title' => '上へ移動', 
							'style' => 'display:none'
						]);
				if (count($datas) > 2) {
					//最下段へ移動
					$this->BcBaser->link('<i class="fa fa-arrow-circle-down fa-2x" style="vertical-align: bottom;margin-left: 2px;"></i>',
							[
								'controller' => 'petit_custom_field_config_metas', 
								'action' => 'move_down', 
								$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
								$data['PetitCustomFieldConfigMeta']['id'], 
								'tobottom'
							], 
							[
								'class' => 'btn-down', 
								'title' => '最下段へ移動'
							]);
				}
			}
		}
		if (!isset($datas) || count($datas) != $count) {
			$this->BcBaser->link($faArrowDown,
					[
						'controller' => 'petit_custom_field_config_metas', 
						'action' => 'move_down', 
						$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
						$data['PetitCustomFieldConfigMeta']['id']
					], 
					[
						'class' => 'btn-down', 
						'title' => '下へ移動'
					]);
		} else {
			$this->BcBaser->link($faArrowDown,
					[
						'controller' => 'petit_custom_field_config_metas', 
						'action' => 'move_down', 
						$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
						$data['PetitCustomFieldConfigMeta']['id']
					], 
					[
						'class' => 'btn-down', 
						'title' => '下へ移動', 
						'style' => 'display:none'
					]);
			if (count($datas) > 2) {
				//最上段へ移動
				$this->BcBaser->link('<i class="fa fa-arrow-circle-up fa-2x" style="vertical-align: bottom;margin-left: 2px;"></i>',
					[
						'controller' => 'petit_custom_field_config_metas', 
						'action' => 'move_up', 
						$data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'], 
						$data['PetitCustomFieldConfigMeta']['id'], 'totop'
					], 
					[
						'class' => 'btn-up', 
						'title' => '最上段へ移動'
					]);
			}
		}
		?>
	</td>
</tr>
