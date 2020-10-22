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
	var mapElements = $('.petit-google-maps');
	mapElements.each(function(index, element) {
		setGoogleMaps($(element));
	});

	function setGoogleMaps(element) {
		var mapElement = element[0];
		var latitude = parseFloat(mapElement.getAttribute('data-latitude'));
		var longitude = parseFloat(mapElement.getAttribute('data-longitude'));
		var zoom = parseInt(mapElement.getAttribute('data-zoom'));
		var text = mapElement.getAttribute('data-text');

		var latlng = new google.maps.LatLng(latitude, longitude);

		var options = {
			center: latlng,
			zoom: zoom
		};

		var map = new google.maps.Map(mapElement, options);

		var markerOptions = {
			position: latlng,
			map: map,
		};

		var marker = new google.maps.Marker(markerOptions);

		if (text) {
			text = escapeHtml(text);

			var infoWindow = new google.maps.InfoWindow({
				content: '<div class="petit-google-maps-popup">' + text + '</div>'
			});
			marker.addListener('click', function() {
				infoWindow.open(map, marker);
			});
		}
	}

	function escapeHtml(string) {
		var entityMap = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#39;',
			'/': '&#x2F;',
			'`': '&#x60;',
			'=': '&#x3D;'
		};
		return String(string).replace(/[&<>"'`=\/]/g, function (s) {
			return entityMap[s];
		});
	}
});
