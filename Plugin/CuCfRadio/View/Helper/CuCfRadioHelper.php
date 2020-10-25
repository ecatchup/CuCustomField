<?php
/**
 * CuCustomField : baserCMS Custom Field Radio Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRadio.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfRadioHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfRadioHelper extends CuCustomFieldAppHelper {

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'radio',
			'options' => (isset($definition['choices'])) ? $this->textToArray($definition['choices']) : [],
			'separator' => (isset($definition['separator'])) ? $definition['separator'] : ''
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
		$options = array_merge([
			'novalue' => ''
		], $options);
		$selector = $this->textToArray($fieldDefinition['choices']);
		return $this->arrayValue($fieldValue, $selector, $options['novalue']);
	}

}
