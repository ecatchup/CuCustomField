<?php
/**
 * CuCustomField : baserCMS Custom Field Googlemaps Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfGooglemaps.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfGooglemapsHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 * @property BcTextHelper $BcText
 */
class CuCfGooglemapsHelper extends CuCustomFieldAppHelper {

	/**
	 * Helper
	 * @var string[]
	 */
	public $helpers = ['BcText'];

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		return $this->_View->element('CuCfGooglemaps.admin/googlemaps', ['definitions' => $definition]);
	}

	/**
	 * Get
	 *
	 * @param mixed $fieldValue
	 * @param array $fieldDefinition
	 * @return mixed
	 */
	public function get($fieldValue, $fieldDefinition, $options) {
		return $this->getGooglemaps($fieldValue, $options);
	}

	/**
	 * フィールド名を指定して、Googleマップの表示データを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getGooglemaps($data, $options = [])
	{
		if (!$data) {
			return false;
		}

		$elementOptions = [
			'googleMapsPopupText' => true,
			'googleMapsWidth' => '100%',
			'googleMapsHeight' => '400px',
		];

		foreach($elementOptions as $key => $var) {
			if (isset($options[$key])) {
				$data[$key] = $options[$key];
			} else {
				$data[$key] = $var;
			}
		}

		return $this->_View->element('CuCfGooglemaps.googlemaps', $data);
	}


	/**
	 * フィールド名を指定して、Googleマップのテキストデータを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getGooglemapsText($post = [], $field = '', $options = [])
	{
		$data = $this->get($post, $field, $options);
		if (isset($data['google_maps_text'])) {
			return $data['google_maps_text'];
		}
		return '';
	}

}
