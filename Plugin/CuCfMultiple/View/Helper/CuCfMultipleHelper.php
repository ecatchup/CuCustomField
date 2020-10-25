<?php
/**
 * CuCustomField : baserCMS Custom Field Multiple Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfMultiple.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfMultipleHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfMultipleHelper extends CuCustomFieldAppHelper {

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
			'multiple' => 'checkbox',
			'options' => (isset($definition['choices'])) ? $this->textToArray($definition['choices']) : [],
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
			'separator' => ', ',
		], $options);
		$selector = $this->textToArray($fieldDefinition['choices']);
		$checked = [];
		if (!empty($fieldValue)) {
			if (is_array($fieldValue)) {
				foreach($fieldValue as $check) {
					$checked[] = $this->arrayValue($check, $selector);
				}
			} else {
				$checked[] = $fieldValue;
			}
		}
		return implode($options['separator'], $checked);
	}

}
