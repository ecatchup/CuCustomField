<?php
if (isset($googleMapsPopupText)) {
	if ($googleMapsPopupText === true) {
		$text = $google_maps_text;
	} else {
		$text = $googleMapsPopupText;
	}
} else {
	$text = '';
}
?>
<?php if ($this->BcBaser->siteConfig['google_maps_api_key']): ?>
	<?php if ($google_maps_latitude && $google_maps_longtude && $google_maps_zoom): ?>
		<div class="petit-google-maps" style="width: <?php echo h($googleMapsWidth) ?>; height:<?php echo h($googleMapsHeight) ?>;" data-latitude="<?php echo h($google_maps_latitude) ?>" data-longtude="<?php echo h($google_maps_longtude) ?>" data-zoom="<?php echo h($google_maps_zoom) ?>" data-text="<?php echo h($text) ?>"></div>
		<?php echo $this->BcBaser->js('https://maps.google.com/maps/api/js?key=' . $this->BcBaser->siteConfig['google_maps_api_key']) ?>
		<?php echo $this->BcBaser->js('PetitCustomField.google_maps') ?>
	<?php endif; ?>
<?php else: ?>
	※Googleマップを利用するには、Google Maps APIのキーの登録が必要です。キーを取得して、システム管理より設定してください。
<?php endif; ?>
