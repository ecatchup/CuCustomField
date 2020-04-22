<?php
/**
 * [ADMIN] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
$this->BcBaser->css(array(
	'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', 
	'PetitCustomField.admin/petit_custom_field',
));
$this->BcListTable->setColumnNumber(9);
?>
<h3>
<?php $this->BcBaser->link($this->BcText->arrayValue($contentId, $blogContentDatas) .' ブログ設定編集はこちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents',
	'action' => 'edit', $contentId
)) ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php $this->BcBaser->link('≫記事一覧こちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts',
	'action' => 'index', $contentId
)) ?>
</h3>

<div class="bca-data-list__top">
	<!-- 一括処理 -->
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div class="bca-main__header-actions">
			<?php
			$this->BcBaser->link(__d('baser', '新規追加'), 
				[
					'controller' => 'petit_custom_field_config_fields', 
					'action' => 'add', 
					$configId
				], 
				[
				'class' => 'bca-btn',
				'data-bca-btn-type' => 'add',
				'data-bca-btn-size' => 'sm'
			]);
			?>　
		</div>
	<?php endif ?>
</div>

<!-- list -->
<table class="list-table bca-table-listup" id="ListTable">
<thead class="bca-table-listup__thead">
	<tr>
		<th class="bca-table-listup__thead-th"><?php // No ?>
		<?php
		echo $this->Paginator->sort('no',
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
		<th class="bca-table-listup__thead-th"><?php // カスタムフィールド名 ?>
			カスタムフィールド名<br /><small>ラベル名</small>
		</th>
		<th class="bca-table-listup__thead-th"><?php // フィールド名 ?>
			フィールド名
		</th>
		<th class="bca-table-listup__thead-th"><?php // フィールドタイプ ?>
			フィールドタイプ
		</th>
		<th class="bca-table-listup__thead-th"><?php // 必須設定 ?>
			必須設定<br /><small>変換処理</small>
		</th>
		<th class="bca-table-listup__thead-th"><?php // アクション ?>
			<?php echo __d('baser', 'アクション') ?>
		</th>
	</tr>
</thead>
<tbody class="bca-table-listup__tbody">
<?php if(!empty($datas)): ?>
	<?php
	foreach ($datas as $key => $data) {
			$this->BcBaser->element('petit_custom_field_config_metas/index_row', 
				[
					'data' => $data, 
					'count' => ($key + 1)
			]);
	}
	?>
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
<?php
if(Configure::read('petitCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>