<?php
/**
 * [ADMIN] CuCustomField
 *
 * @copyright        Copyright, Catchup, Inc.
 * @link            https://catchup.co.jp
 * @package            CuCustomField
 * @license            MIT
 */

/**
 * @var BcAppView $this
 * @var array $fieldConfigField
 */
$formPlace = $this->request->data('CuCustomFieldConfig.form_place');
$this->BcBaser->js('https://maps.google.com/maps/api/js?key=' . $this->BcBaser->siteConfig['google_maps_api_key']);
$this->BcBaser->js('CuCustomField.admin/google_maps');
?>


<?php if ($fieldConfigField): ?>

	<table class="form-table section bca-form-table" id="CuCustomFieldTable">
	<?php foreach($fieldConfigField as $keyFieldConfig => $valueFieldConfig): ?>
		<?php if ($this->CuCustomField->judgeStatus($valueFieldConfig)): ?>
				<tr>
					<th class="col-head bca-form-table__label">
						<?php echo $this->BcForm->label("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}", $valueFieldConfig['CuCustomFieldDefinition']['name']) ?>
						<?php if ($this->CuCustomField->judgeShowFieldConfig($valueFieldConfig, ['field' => 'required'])): ?>&nbsp;
							<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
						<?php endif ?>
					</th>
					<td class="col-input bca-form-table__input">
						<?php if ($this->CuCustomField->judgeShowFieldConfig($valueFieldConfig, ['field' => 'prepend'])): ?>
							<div><?php echo nl2br($valueFieldConfig['CuCustomFieldDefinition']['prepend']) ?></div>
						<?php endif ?>
<?php if ($valueFieldConfig['CuCustomFieldDefinition']['field_type'] == 'googlemaps'): ?>
						<?php if (!empty($this->BcBaser->siteConfig['google_maps_api_key'])): ?>
							<div class="petit-google-maps" style="width:100%; height:450px;"></div>
							<div style="margin-right: 5px;">
								<?php echo $this->BcForm->input('google_maps_address',
									['type' => 'text', 'name' => '', 'class' => 'petit-google_maps_address']) ?>
								<?php echo $this->BcForm->button('入力住所から地図を設定', ['type' => 'button', 'class' => 'petit-set_google_maps_setting', 'size' => 40]) ?>
							</div>
							<?php echo '緯度' . $this->CuCustomField->input("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}.google_maps_latitude", ['type' => 'text', 'class' => 'petit-google_maps_latitude', 'default' => $valueFieldConfig['CuCustomFieldDefinition']['google_maps_latitude'], 'size' => 22]); ?>
							<?php echo '経度' . $this->CuCustomField->input("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}.google_maps_longtude", ['type' => 'text', 'class' => 'petit-google_maps_longtude', 'default' => $valueFieldConfig['CuCustomFieldDefinition']['google_maps_longtude'], 'size' => 22]); ?>
							<?php echo 'ズーム値' . $this->CuCustomField->input("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}.google_maps_zoom", ['type' => 'text', 'class' => 'petit-google_maps_zoom', 'default' => $valueFieldConfig['CuCustomFieldDefinition']['google_maps_zoom'], 'size' => 4]); ?>
							<br>
							<?php echo 'ポップアップテキスト' . $this->CuCustomField->input("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}.google_maps_text", ['type' => 'text', 'class' => 'petit-google_maps_text', 'default' => $valueFieldConfig['CuCustomFieldDefinition']['google_maps_text'], 'size' => 60]); ?>
						<?php else: ?>
							※Googleマップを利用するには、Google Maps APIのキーの登録が必要です。キーを取得して、システム管理より設定してください。
						<?php endif; ?>
<?php else: ?>
						<?php echo $this->CuCustomField->input("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}", $this->CuCustomField->getFormOption($valueFieldConfig, 'CuCustomFieldDefinition')) ?>
<?php endif ?>
						<?php echo $this->BcForm->error("CuCustomFieldValue.{$valueFieldConfig['CuCustomFieldDefinition']['field_name']}") ?>
						<?php if ($this->CuCustomField->judgeShowFieldConfig($valueFieldConfig, ['field' => 'append'])): ?>
							<div><?php echo nl2br($valueFieldConfig['CuCustomFieldDefinition']['append']) ?></div>
						<?php endif ?>
						<?php if ($this->CuCustomField->judgeShowFieldConfig($valueFieldConfig, ['field' => 'description'])): ?>
							<br/>
							<small><?php echo nl2br($valueFieldConfig['CuCustomFieldDefinition']['description']) ?></small>
						<?php endif ?>
					</td>
				</tr>
		<?php endif ?>
	<?php endforeach ?>
	</table>

<?php else: ?>

	<ul>
		<li>利用可能なフィールドがありません。不要な場合は
			<?php $this->BcBaser->link('カスタムフィールド設定',
				['plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'edit', $this->request->data['CuCustomFieldConfig']['id']],
				[],
				'カスタムフィールド設定画面へ移動して良いですか？編集中の内容は保存されません。'); ?>
			より無効設定ができます。
		</li>
	</ul>

<?php endif ?>
