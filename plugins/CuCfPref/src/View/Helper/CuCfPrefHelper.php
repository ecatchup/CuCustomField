<?php
/**
 * CuCustomField : baserCMS Custom Field Pref Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfPref.View.Helper
 * @license          MIT LICENSE
 */
namespace CuCfPref\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use BaserCore\View\Helper\BcTextHelper;

/**
 * Class CuCfPrefHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 * @property BcAdminFormHelper $BcAdminForm
 * @property BcTextHelper $BcText
 */
class CuCfPrefHelper extends CuCustomFieldAppHelper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcText'
    ];

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $definition
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'select',
			'options' => $this->BcText->prefList()
		], $options);
		return $this->BcAdminForm->control($fieldName, $options);
	}

	/**
	 * Get
	 *
	 * @param mixed $fieldValue
	 * @param array $fieldDefinition
	 * @param array $options
	 * @return mixed
	 */
	public function get($fieldValue, $fieldDefinition, $options) {
		$options = array_merge([
			'novalue' => ''
		], $options);
		$selector = $this->BcText->prefList();
		return $this->arrayValue($fieldValue, $selector, $options['novalue']);
	}

}
