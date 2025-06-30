<?php
/**
 * CuCustomField : baserCMS Custom Field Textarea Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfTextarea.View.Helper
 * @license          MIT LICENSE
 */
namespace CuCfTextarea\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCustomField\Model\Entity\CustomFieldVlue;
use CuCustomField\Model\Entity\CuCustomFieldDefinition;
use Cake\View\Helper;

/**
 * Class CuCfTextareaHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfTextareaHelper extends Helper {

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * Input
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     */
    public function input (string $fieldName, array $definition, array $options) {
        $options = array_merge([
            'type' => 'textarea',
            'rows' => (isset($definition['rows'])) ? $definition['rows'] : '',
            'cols' => (isset($definition['cols'])) ? $definition['cols'] : '',
            'placeholder' => (isset($definition['placeholder'])) ? $definition['placeholder'] : ''
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
        return h($fieldValue);
    }

}
