<?php
/**
 * CuCustomField : baserCMS Custom Field Loop Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfLoop.View.Helper
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCfLoopHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfLoopHelper extends CuCustomFieldAppHelper {

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $options) {
//		$options['options'] = ['' => '指定しない'] + $options['options'];
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
