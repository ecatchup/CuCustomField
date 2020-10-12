<?php
/**
 * @var BcAppView $this
 * @var array $definitions
 */
?>


<div class="petit-google-maps-form">
<?php if (!empty($this->BcBaser->siteConfig['google_maps_api_key'])): ?>
	<div class="petit-google-maps" style="width:100%; height:450px;"></div>
	<div style="margin-right: 5px;">
		<?php echo $this->BcForm->input('google_maps_address', ['type' => 'text', 'name' => '', 'class' => 'bca-textbox__input petit-google_maps_address']) ?>
		<?php echo $this->BcForm->button('入力住所から地図を設定', ['type' => 'button', 'class' => 'bca-btn petit-set_google_maps_setting', 'size' => 40]) ?>
	</div>
	<?php echo '緯度' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['CuCustomFieldDefinition']['field_name']}.google_maps_latitude", [
		'type' => 'text',
		'class' => 'bca-textbox__input petit-google_maps_latitude',
		'default' => $definitions['CuCustomFieldDefinition']['google_maps_latitude'],
		'size' => 22
	]) ?>
	<?php echo '経度' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['CuCustomFieldDefinition']['field_name']}.google_maps_longtude", [
		'type' => 'text',
		'class' => 'bca-textbox__input petit-google_maps_longtude',
		'default' => $definitions['CuCustomFieldDefinition']['google_maps_longtude'],
		'size' => 22
	]) ?>
	<?php echo 'ズーム値' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['CuCustomFieldDefinition']['field_name']}.google_maps_zoom", [
		'type' => 'text',
		'class' => 'bca-textbox__input petit-google_maps_zoom',
		'default' => $definitions['CuCustomFieldDefinition']['google_maps_zoom'],
		'size' => 4
	]) ?>
	<br>
	<?php echo 'ポップアップテキスト' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['CuCustomFieldDefinition']['field_name']}.google_maps_text", [
		'type' => 'text',
		'class' => 'bca-textbox__input petit-google_maps_text',
		'default' => $definitions['CuCustomFieldDefinition']['google_maps_text'],
		'size' => 60
	]) ?>
<?php else: ?>
	※Googleマップを利用するには、Google Maps APIのキーの登録が必要です。キーを取得して、システム管理より設定してください。
<?php endif; ?>
</div>
