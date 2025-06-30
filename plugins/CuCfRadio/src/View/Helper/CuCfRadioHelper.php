<?php
/**
 * CuCustomField : baserCMS Custom Field Radio Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRadio.View.Helper
 * @license          MIT LICENSE
 */

namespace CuCfRadio\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCustomField\Model\Entity\CustomFieldVlue;
use CuCustomField\Model\Entity\CuCustomFieldDefinition;
use Cake\View\Helper;

/**
 * Class CuCfRadioHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfRadioHelper extends CuCustomFieldAppHelper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcBaser'
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
			'type' => 'radio',
			'options' => (isset($definition['choices'])) ? $this->textToArray($definition['choices']) : [],
			'separator' => (isset($definition['separator'])) ? $definition['separator'] : ''
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
			'novalue' => ''
		], $options);
		$selector = $this->textToArray($fieldDefinition['choices']);
		return $this->arrayValue($fieldValue, $selector, $options['novalue']);
	}

}
