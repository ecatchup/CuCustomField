<?php
/**
 * CuCustomField : baserCMS Custom Field Checkbox Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfCheckbox.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfCheckboxHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfCheckboxHelper extends CuCustomFieldAppHelper {

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'checkbox',
			'label' => (isset($definition['label_name'])) ? $definition['label_name'] : ''
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
		return (bool) ($fieldValue);
	}

}
