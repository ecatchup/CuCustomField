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
	<?php echo '緯度' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['field_name']}.google_maps_latitude", ['field_type' => 'text'], [
		'class' => 'bca-textbox__input petit-google_maps_latitude',
		'default' => $definitions['google_maps_latitude'],
		'size' => 22
	]) ?>
	<?php echo '経度' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['field_name']}.google_maps_longitude", ['field_type' => 'text'], [
		'class' => 'bca-textbox__input petit-google_maps_longitude',
		'default' => $definitions['google_maps_longitude'],
		'size' => 22
	]) ?>
	<?php echo 'ズーム値' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['field_name']}.google_maps_zoom", ['field_type' => 'text'], [
		'class' => 'bca-textbox__input petit-google_maps_zoom',
		'default' => $definitions['google_maps_zoom'],
		'size' => 4
	]) ?>
	<br>
	<?php echo 'ポップアップテキスト' . $this->CuCustomField->input("CuCustomFieldValue.{$definitions['field_name']}.google_maps_text", ['field_type' => 'text'], [
		'class' => 'bca-textbox__input petit-google_maps_text',
		'default' => $definitions['google_maps_text'],
		'size' => 60
	]) ?>
<?php else: ?>
	※Googleマップを利用するには、Google Maps APIのキーの登録が必要です。キーを取得して、システム管理より設定してください。
<?php endif; ?>
</div>
