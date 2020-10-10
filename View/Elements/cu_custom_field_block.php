<?php
/**
 * [PUBLISH] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 *
 * このファイルは、カスタムフィールドを利用する際の利用例を記述したサンプルファイルです。
 * 記事詳細用や記事一覧表示用のビュー・ファイルに記述することで、
 * カスタムフィールドに入力した内容を反映できます。
 * 1フィールド毎に表示したい場合は、以下のソースが例となります。
 *
 * フィールドのラベル名を表示する: $this->CuCustomField->get($post, 'example_field_name');
 * フィールドの入力内容を表示する: $this->CuCustomField->get('example_field_name');
 *
 */
$this->BcBaser->css('PetitCustomField.petit_custom_field');
?>
<?php if ($this->CuCustomField->allowPublish($this->CuCustomField->publicConfigData, 'CuCustomFieldConfig')): ?>

<?php if (!empty($post)): ?>
	<?php if (!empty($post['CuCustomFieldValue'])): ?>
<div id="PetitCustomFieldBlock">
	<div class="petit-custom-body">
		<table class="table">
			<thead>
				<tr>
					<th>フィールド名</th><th>ラベル名</th><td>内容</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($post['CuCustomFieldValue'] as $fieldName => $value): ?>
				<tr>
					<td><?php echo $fieldName ?></td>
					<td><?php echo $this->CuCustomField->getField($fieldName) ?></td>
					<td>
						<?php
						$fieldConfig = $this->CuCustomField->getFieldConfig($this->request->params['Content']['entity_id'], $fieldName);
						if ($fieldConfig['field_type'] == 'googlemaps') {
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
<?php endif ?>

<?php endif ?>
