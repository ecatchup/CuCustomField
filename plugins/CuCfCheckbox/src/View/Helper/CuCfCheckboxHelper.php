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
namespace CuCfCheckbox\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCustomField\Model\Entity\CustomFieldVlue;
use CuCustomField\Model\Entity\CuCustomFieldDefinition;
use Cake\View\Helper;
/**
 * Class CuCfCheckboxHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
#[\AllowDynamicProperties]
class CuCfCheckboxHelper extends Helper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm'
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
			'type' => 'checkbox',
			'label' => (isset($definition['label_name'])) ? $definition['label_name'] : ''
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
		return (bool) ($fieldValue);
	}

}
