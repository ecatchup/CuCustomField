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
 */
$this->BcListTable->setColumnNumber(9);
?>


<!-- list -->
<table class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
		<tr>
			<th class="bca-table-listup__thead-th"><?php // No ?>
				<?php
				echo $this->Paginator->sort('id',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'No'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'No')
					],
					[
						'escape' => false,
						'class' => 'btn-direction bca-table-listup__a'
					]);
				?>
			</th>
			<th class="bca-table-listup__thead-th"><?php // content_id ?>
				<?php
				echo $this->Paginator->sort('content_id',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'コンテンツ名'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'コンテンツ名')
					],
					[
						'escape' => false,
						'class' => 'btn-direction bca-table-listup__a'
					]);
					?>
			</th>
			<th class="bca-table-listup__thead-th"><?php // フィールド数 ?>
				フィールド数
			</th>
			<th class="bca-table-listup__thead-th"><?php // 編集画面フォーム表示位置 ?>
				<?php
				echo $this->Paginator->sort('form_place',
					[
						'asc' => '<i class="bca-icon--asc"></i>'. __d('baser', '編集画面フォーム表示位置'),
						'desc' => '<i class="bca-icon--desc"></i>'. __d('baser', '編集画面フォーム表示位置')
					],
					[
						'escape' => false,
						'class' => 'btn-direction bca-table-listup__a'
					]);
				?>
			</th>
			<th class="bca-table-listup__thead-th"><?php // 投稿日 ?>
				<?php echo $this->BcListTable->dispatchShowHead() ?>
				<?php
				echo $this->Paginator->sort('created',
				[
					'asc' => '<i class="bca-icon--asc"></i>'. __d('baser', '登録日'),
					'desc' => '<i class="bca-icon--desc"></i>'. __d('baser', '登録日')
				],
				[
					'escape' => false,
					'class' => 'btn-direction bca-table-listup__a'
				]);
				?>
				<br />
				<?php
				echo $this->Paginator->sort('modified',
				[
					'asc' => '<i class="bca-icon--asc"></i>'. __d('baser', '更新日'),
					'desc' => '<i class="bca-icon--desc"></i>'. __d('baser', '更新日')
				],
					[
						'escape' => false,
						'class' => 'btn-direction bca-table-listup__a'
				]);
				?>
			</th>
			<th class="bca-table-listup__thead-th"><?php // アクション ?>
			<?php echo __d('baser', 'アクション') ?>
			</th>
		</tr>
	</thead>
<tbody class="bca-table-listup__tbody">
	<?php if (!empty($datas)): ?>
		<?php foreach ($datas as $data): ?>
			<?php $this->BcBaser->element('cu_custom_field_configs/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td">
					<p class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
<div class="bca-data-list__bottom">
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
    <!-- list-num -->
    <?php //$this->BcBaser->element('list_num') ?>
  </div>
</div>
