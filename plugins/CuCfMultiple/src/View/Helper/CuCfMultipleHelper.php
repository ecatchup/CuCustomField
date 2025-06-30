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
namespace CuCfMultiple\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCustomField\Model\Entity\CustomFieldVlue;
use CuCustomField\Model\Entity\CuCustomFieldDefinition;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;


/**
 * Class CuCfMultipleHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfMultipleHelper extends Helper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcBaser',
        'CuCustomField.CuCustomFieldApp'
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
            'options' => (isset($definition['choices'])) ? $this->CuCustomFieldApp->textToArray($definition['choices']) : [],
        ], $options);
		return $this->BcAdminForm->control($fieldName, $options);
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
		$selector = $this->CuCustomFieldApp->textToArray($fieldDefinition['choices']);
		$checked = [];
		if (!empty($fieldValue)) {
			if (is_array($fieldValue)) {
				foreach($fieldValue as $check) {
					$checked[] = $this->CuCustomFieldApp->arrayValue($check, $selector);
				}
			} else {
				$checked[] = $fieldValue;
			}
		}
		return implode($options['separator'], $checked);
	}

}
