<?php
/**
 * CuCustomField : baserCMS Custom Field Related Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRelated.View.Helper
 * @license          MIT LICENSE
 */

/**
 * Class CuCfRelatedHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfRelatedHelper extends AppHelper {

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$related = $definition['option_meta']['related'];
		$CuCfRelated = ClassRegistry::init('CuCfRelated.CuCfRelated');
		$list = $CuCfRelated->getRelatedList($related['table'], $related['title_field'], $related['where_field'], $related['where_value']);
		$options = array_merge([
			'type' => 'select',
			'options' => ['' => '指定なし'] + $list,
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
	public function get($fieldValue, $fieldDefinition) {
		$related = $fieldDefinition['option_meta']['related'];
		$CuCfRelated = ClassRegistry::init('CuCfRelated.CuCfRelated');
		return $CuCfRelated->getRelatedRecord($related['table'], $fieldValue);
	}

}
