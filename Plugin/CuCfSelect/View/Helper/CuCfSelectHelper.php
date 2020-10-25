<?php
/**
 * CuCustomField : baserCMS Custom Field Select Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfSelect.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfSelectHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfSelectHelper extends CuCustomFieldAppHelper {

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
			'options' => (isset($definition['choices'])) ? ['' => '指定しない'] + $this->textToArray($definition['choices']) : []
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
