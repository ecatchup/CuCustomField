<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfText.View.Helper
 * @license          MIT LICENSE
 */

/**
 * Class CuCfTextHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfTextHelper extends AppHelper {

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'text',
			'size' => (isset($definition['size'])) ? $definition['size'] : '',
			'max_length' => (isset($definition['max_length'])) ? $definition['max_length'] : '255',
			'placeholder' => (isset($definition['placeholder'])) ? $definition['placeholder'] : ''
		], $options);
		if(!empty($definition['counter'])) {
			$options['counter'] = true;
		}
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
		return h($fieldValue);
	}

}
