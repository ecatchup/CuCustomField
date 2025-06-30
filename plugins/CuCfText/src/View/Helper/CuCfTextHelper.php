<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfText.View.Helper
 * @license          MIT LICENSE
 */
namespace CuCfText\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCustomField\Model\Entity\CustomFieldVlue;
use CuCustomField\Model\Entity\CuCustomFieldDefinition;
use Cake\View\Helper;

/**
 * Class CuCfTextHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfTextHelper extends Helper {

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
            'type' => 'text',
            'size' => (isset($definition['size'])) ? $definition['size'] : '',
            'max_length' => (isset($definition['max_length'])) ? $definition['max_length'] : '255',
            'placeholder' => (isset($definition['placeholder'])) ? $definition['placeholder'] : '',
        ], $options);

        if(!empty($definition['counter'])) {
            $options['counter'] = true;
        }

        $input = $this->BcAdminForm->control($fieldName, $options);

        // 説明文がある場合は付与する
        if(!empty($definition['description'])){
            $input = $input . '<br><small>'.$definition['description'].'</small>';
        }

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
        return h($fieldValue);
    }

}
