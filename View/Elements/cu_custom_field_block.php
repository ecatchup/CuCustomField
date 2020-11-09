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
 * このファイルは、カスタムフィールドを利用する際の利用例を記述したサンプルファイルです。
 * 記事詳細用や記事一覧表示用のビュー・ファイルに記述することで、
 * カスタムフィールドに入力した内容を反映できます。
 * 1フィールド毎に表示したい場合は、以下のソースが例となります。
 *
 * ## フィールドのラベル名を表示する
 * $this->CuCustomField->getFieldAttribute($post, 'example_field_name');
 *
 * ## フィールドの入力内容を表示する
 * $this->CuCustomField->get($post, 'example_field_name');
 *
 * ## ループの入力内容を表示する
 *  $loopItems = $this->CuCustomField->get($post, $fieldName);
 *  if($loopItems) {
 * 		foreach($loopItems as $loopItem) {
 *	 		echo $this->CuCustomField->get($loopItem, 'example_field_name-1');
 * 			echo $this->CuCustomField->get($loopItem, 'example_field_name-2');
 * 		}
 *  }
 */
$this->BcBaser->css('PetitCustomField.cu_custom_field');
?>


<?php if (!empty($post)): ?>
<div id="PetitCustomFieldBlock">
	<div class="petit-custom-body">
		<table class="table">
			<thead>
				<tr>
					<th>フィールド名</th><th>ラベル名</th><th>内容</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($post['CuCustomFieldValue'] as $fieldName => $value): ?>
				<tr>
					<td><?php echo $fieldName ?></td>
					<td><?php echo $this->CuCustomField->getFieldAttribute($post, $fieldName) ?></td>
					<td>
						<?php
						$fieldConfig = $this->CuCustomField->getFieldConfig($post['BlogPost']['blog_content_id'], $fieldName);
						if ($fieldConfig['field_type'] === 'loop') {
							$loopItems = $this->CuCustomField->get($post, $fieldName);
							if($loopItems) {
								foreach($loopItems as $loopItem) {
									foreach($loopItem as $loopField => $value) {
										echo $this->CuCustomField->get($loopItem, $loopField);
									}
								}
							}
						} elseif ($fieldConfig['field_type'] === 'googlemaps') {
							echo $this->CuCustomField->getGoogleMaps($post, $fieldName, ['googleMapsPopupText' => true]);
						} else {
							echo $this->CuCustomField->get($post, $fieldName);
						}
						?>
					</td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif ?>
