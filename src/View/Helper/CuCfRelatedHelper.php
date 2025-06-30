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
namespace CuCustomField\View\Helper;

use Cake\ORM\TableRegistry;
use Cake\View\Helper;

/**
 * Class CuCfRelatedHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfRelatedHelper extends Helper {
    /**
	 * ヘルパー
	 *
	 * @var array
	 */
	protected array $helpers = [
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
        $meta = $definition['option_meta'];
		$related = unserialize($meta)['related'];
		$CuCfRelated = TableRegistry::getTableLocator()->get('CuCfRelated.CuCfRelated');
		$list = $CuCfRelated->getRelatedList($related['table'], $related['title_field'], $related['where_field'], $related['where_value']);

		$options = array_merge([
			'type' => 'select',
            'id' => str_replace([' ', '.', '_'], '', ucwords($fieldName, '.')),
			'options' => ['' => '指定なし'] + $list,
		], $options);

		$form = $this->BcAdminForm->control($fieldName, $options);
		return $form;
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
