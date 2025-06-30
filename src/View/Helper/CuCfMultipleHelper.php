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
namespace CuCustomField\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;

/**
 * Class CuCfMultipleHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfMultipleHelper extends CuCustomFieldAppHelper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BcAdminForm'
    ];
	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'multiCheckbox',
			'multiple' => 'multiple',
			'options' => (isset($definition['choices'])) ? $this->textToArray($definition['choices']) : [],
		], $options);

		$input = $this->BcAdminForm->control($fieldName, $options);

        return $input;
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
