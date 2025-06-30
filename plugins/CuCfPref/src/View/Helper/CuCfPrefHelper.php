<?php
/**
 * CuCustomField : baserCMS Custom Field Pref Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfPref.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfRadioHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 * @property BcTextHelper $BcText
 */
class CuCfPrefHelper extends CuCustomFieldAppHelper {

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
		$options = array_merge([
			'type' => 'select',
			'options' => $this->BcText->prefList()
		], $options);
		return $this->CuCustomField->BcForm->input($fieldName, $options);
	}

	/**
	 * Get
	 *
	 * @param mixed $fieldValue
	 * @param array $fieldDefinition
	 * @return mixed
	 */
	public function get($fieldValue, $fieldDefinition, $options) {
		$selector = $this->BcText->prefList();
		return $this->arrayValue($fieldValue, $selector, $options['novalue']);
	}

}
