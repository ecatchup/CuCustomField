/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.js
 * @license          MIT LICENSE
 */

$(function(){
	var mapFormElements = $('.petit-google-maps-form');

	var GoogleMapsAdmin = new GoogleMapsAdmin();

	// 保存ボタンのイベントを一時的に無効化する
	GoogleMapsAdmin.offSaveButtonClickEvent();

	mapFormElements.each(function(index, element) {
		GoogleMapsAdmin.set($(element));
	});

	$('#BtnSave').click(function(e){
		var result = true;

		mapFormElements.each(function(index, element) {
			result = GoogleMapsAdmin.checkFieldEmpty($(element));
			if (!result) return false;
		});

		if (result) {
			// 無効化した保存ボタンのイベントを有効化
			GoogleMapsAdmin.onSaveButtonClickEvent();
			GoogleMapsAdmin.clickSaveButton();
		}

		return false;
	});

	function GoogleMapsAdmin() {
		var self = this;

		var saveButton = $('#BtnSave');;
		var saveButtonClickEvents = [];

		var selectors = {
			'map': '.petit-google-maps',
			'latitudeInput': '.petit-google_maps_latitude',
			'longitudeInput': '.petit-google_maps_longitude',
			'zoomInput': '.petit-google_maps_zoom',
			'popupTextInput': '.petit-google_maps_text',
			'addressInput': '.petit-google_maps_address',
			'setMapButton': '.petit-set_google_maps_setting',
		};

		self.offSaveButtonClickEvent = function() {
			if (!saveButton.length) return false;

			var saveEvents = $._data(saveButton.get(0), 'events');

			if (!saveEvents.click) return false;

			saveEvents.click.forEach(function(e) {
				saveButtonClickEvents.push(e);
			});

			saveButton.off('click');
		};

		self.onSaveButtonClickEvent = function() {
			saveButton.off('click');

			saveButtonClickEvents.forEach(function(v) {
				saveButton.on('click', v);
			});
		};

		self.clickSaveButton = function() {
			saveButton.click();
		};

		self.set = function(element) {
			var mapElement = element.find(selectors.map)[0];
			var latitudeInput = element.find(selectors.latitudeInput);
			var longitudeInput = element.find(selectors.longitudeInput);
			var zoomInput = element.find(selectors.zoomInput);
			var popupTextInput = element.find(selectors.popupTextInput);
			var addressInput = element.find(selectors.addressInput);
			var setMapButton = element.find(selectors.setMapButton);

			var latitude = parseFloat(latitudeInput.val());
			var longitude = parseFloat(longitudeInput.val());
			var zoom = parseInt(zoomInput.val());

			// データが入力されていない場合の初期値を設定（東京都）
			if (isNaN(latitude)) latitude = 35.6894875;
			if (isNaN(longitude)) longitude = 139.69170639999993;
			if (isNaN(zoom)) zoom = 7;

			var latlng = new google.maps.LatLng(latitude, longitude);

			var geocoder = new google.maps.Geocoder();

			var options = {
				center: latlng,
				zoom: zoom
			};

			var map = new google.maps.Map(mapElement, options);

			var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				draggable: true
			});

			// マーカーをドラッグした際は、地図ドラッグにマーカーを追従しない
			var markerDragMode = false;

			// 地図操作時に地図情報とマーカーの位置を更新
			google.maps.event.addListener(map, 'dragend', function(){
				if (!markerDragMode) {
					marker.setPosition(map.getCenter());
					latitudeInput.val(map.getCenter().lat());
					longitudeInput.val(map.getCenter().lng());
					zoomInput.val(map.getZoom());
				}
			});
			google.maps.event.addListener(map, 'drag', function(){
				if (!markerDragMode) {
					marker.setPosition(map.getCenter());
				}
			});
			google.maps.event.addListener(map, 'zoom_changed', function(){
				if (!markerDragMode) {
					marker.setPosition(map.getCenter());
				}
				zoomInput.val(map.getZoom());
			});
			// クリックした位置にマーカー移動
			google.maps.event.addListener(map, 'click', function(e){
				markerDragMode = true;
				marker.setPosition(e.latLng);
				latitudeInput.val(e.latLng.lat());
				longitudeInput.val(e.latLng.lng());
			});
			// マーカーダブルクリックでマーカー座標を地図のセンターに
			google.maps.event.addListener(marker, 'dblclick', function(){
				map.setCenter(marker.getPosition());
			});
			// マーカードラッグ
			google.maps.event.addListener(marker, 'dragend', function(){
				markerDragMode = true;
				latitudeInput.val(marker.getPosition().lat());
				longitudeInput.val(marker.getPosition().lng());
				zoomInput.val(map.getZoom());
			});

			// 住所から緯度経度を取得
			setMapButton.click(function() {
				var address = addressInput.val();
				geocoder.geocode({ 'address': address }, function(result, status) {
					if (status != google.maps.GeocoderStatus.OK) {
						alert('地図情報の取得に失敗しました。')
						return;
					}
					map.setCenter(result[0].geometry.location);
					marker.setPosition(result[0].geometry.location);
					latitudeInput.val(map.getCenter().lat());
					longitudeInput.val(map.getCenter().lng());
					zoomInput.val(map.getZoom());
				});
			});

			// マーカークリック時にポップアップテキストを表示
			var infoWindow = new google.maps.InfoWindow();
			marker.addListener('click', function() {
				var text = popupTextInput.val();
				if (text) {
					infoWindow.setContent('<div class="petit-google-maps-popup">' + text + '</div>');
					infoWindow.open(map, marker);
				}
			});
		}

		self.checkFieldEmpty = function(element) {
			var latitudeInput = element.find(selectors.latitudeInput);
			var longitudeInput = element.find(selectors.longitudeInput);
			var zoomInput = element.find(selectors.zoomInput);

			var latitude = parseFloat(latitudeInput.val());
			var longitude = parseFloat(longitudeInput.val());
			var zoom = parseInt(zoomInput.val());

			if (!latitude || !longitude || !zoom) {
				alert('緯度、経度、zoom値を数値で入力してください');
				return false;
			}

			return true;
		}
	}
});
