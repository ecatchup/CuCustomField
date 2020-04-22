<?php
/**
 * [ADMIN] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
?>
<div class="submit bca-actions">
	<div class="bca-actions__main">
	<?php 
	$this->BcBaser->link('カスタムフィールド設定一覧', 
		[
			'admin' => true, 
			'plugin' => 'petit_custom_field', 
			'controller' => 'petit_custom_field_configs', 
			'action'=>'index'
		],
		[
			'class' => 'button btn-red bca-btn',
		]);
	?>	
	</div>
	<?php 
	if ($this->request->params['controller'] == 'petit_custom_field_config_fields') {
		echo '<div class="bca-actions__sub">';
		$this->BcBaser->link('フィールド設定一覧', 
			[
				'admin' => true, 
				'plugin' => 'petit_custom_field', 
				'controller' => 'petit_custom_field_config_metas', 
				'action'=>'index', 
				$configId
			],
			[
				'class' => 'button btn-red bca-btn', 
			]);
		echo '</div>';
	}
	?>
</div>