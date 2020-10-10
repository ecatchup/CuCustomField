<?php
/**
 * [ADMIN] CuCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */

/**
 * @var BcAppView $this
 * @var int $contentId
 * @var array $blogContentDatas
 */
$this->BcBaser->css(array(
	'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
	'CuCustomField.admin/petit_custom_field',
));
$this->BcListTable->setColumnNumber(9);
$contentName = $this->BcText->arrayValue($contentId, $blogContentDatas);
?>



&nbsp;&nbsp;&nbsp;&nbsp;


<p>
	<?php $this->BcBaser->link($contentName .' 設定に移動',
		['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $contentId],
		['class' => 'bca-btn']
	) ?>
	&nbsp;&nbsp;
	<?php $this->BcBaser->link($contentName . ' 記事一覧に移動',
		['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts',	'action' => 'index', $contentId],
		['class' => 'bca-btn']
	) ?>
</p>

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
			フィールド定義名
		</th>
		<th class="bca-table-listup__thead-th"><?php // フィールド名 ?>
			フィールド名
		</th>
		<th class="bca-table-listup__thead-th"><?php // フィールドタイプ ?>
			フィールドタイプ
		</th>
		<th class="bca-table-listup__thead-th"><?php // 必須設定 ?>
			必須設定
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
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<?php
if(Configure::read('cuCustomFieldConfig.submenu')) {
	$this->BcBaser->element('submenu');
}
?>

<section class="bca-actions">
	<div class="bca-actions__main">
	<?php $this->BcBaser->link('カスタムフィールド設定一覧に戻る',
		['controller' => 'cu_custom_field_configs', 'action' => 'index'],
		['class' => 'bca-btn  bca-actions__item', 'data-bca-btn-type' => 'back-to-list']
	) ?>
	</div>
</section>
