<?php
/**
 * CuCustomField : baserCMS Custom Field Textarea Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfTextarea.View.Helper
 * @license          MIT LICENSE
 */

/**
 * Class CuCfTextareaHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfTextareaHelper extends AppHelper {

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'textarea',
			'rows' => (isset($definition['rows'])) ? $definition['rows'] : '',
			'cols' => (isset($definition['cols'])) ? $definition['cols'] : '',
			'placeholder' => (isset($definition['placeholder'])) ? $definition['placeholder'] : ''
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
		return h($fieldValue);
	}

}
