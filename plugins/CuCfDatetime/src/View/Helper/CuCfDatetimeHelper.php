<?php
/**
 * CuCustomField : baserCMS Custom Field Datetime Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfDatetime.View.Helper
 * @license          MIT LICENSE
 */

/**
 * Class CuCfDatetimeHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 * @property BcTimeHelper $BcTime
 */
class CuCfDatetimeHelper extends AppHelper {

	public $helpers = ['BcTime'];

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'dateTimePicker',
			'size' => (isset($definition['size'])) ? $definition['size'] : '12',
			'maxlength' => (isset($definition['max_length'])) ? $definition['max_length'] : '10',
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
			'format' => 'Y/m/d H:i:s',
		], $options);
		return $this->BcTime->format($options['format'], $fieldValue);
	}

}
