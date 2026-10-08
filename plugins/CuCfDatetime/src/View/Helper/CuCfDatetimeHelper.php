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
namespace CuCfDatetime\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use BaserCore\View\Helper\BcTimeHelper;

/**
 * Class CuCfDatetimeHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 * @property BcAdminFormHelper $BcAdminForm
 * @property BcTimeHelper $BcTime
 */
class CuCfDatetimeHelper extends CuCustomFieldAppHelper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcTime'
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
			'type' => 'dateTimePicker',
			'size' => (isset($definition['size'])) ? $definition['size'] : '12',
			'maxlength' => (isset($definition['max_length'])) ? $definition['max_length'] : '10',
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
			'format' => 'Y/m/d H:i:s',
		], $options);
		// 5系の BcTimeHelper::format() は format($date, $format)。
		// 4系は format($format, $date) で引数が逆だった。
		return $this->BcTime->format($fieldValue, $options['format']);
	}

}
