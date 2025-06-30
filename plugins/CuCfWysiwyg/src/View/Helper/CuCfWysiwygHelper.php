<?php
/**
 * CuCustomField : baserCMS Custom Field Wysiwyg Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfWysiwyg.View.Helper
 * @license          MIT LICENSE
 */

namespace CuCfWysiwyg\View\Helper;

use CuCustomField\View\Helper\CuCustomFieldAppHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCustomField\Model\Entity\CustomFieldVlue;
use CuCustomField\Model\Entity\CuCustomFieldDefinition;
use Cake\View\Helper;

/**
 * Class CuCfWysiwygHelper
 *
 * @property CuCustomFieldHelper $CuCustomField
 * @property BcTextHelper $BcText
 */
class CuCfWysiwygHelper extends CuCustomFieldAppHelper {

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
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'editorHeight' => ($definition['rows']) ? $definition['rows'] : '200px',
			'editorWidth' => ($definition['cols']) ? $definition['cols'] : '100%',
			'editor' => $this->_View->viewVars['siteConfig']['editor'],
			'editorEnterBr' => $this->_View->viewVars['siteConfig']['editor_enter_br'],
			'editorToolType' => $definition['editor_tool_type'],
		], $options);
		return $this->CuCustomField->BcForm->ckeditor($fieldName, $options);
	}

	/**
	 * Get
	 *
	 * @param mixed $fieldValue
	 * @param array $fieldDefinition
	 * @return mixed
	 */
	public function get($fieldValue, $fieldDefinition, $options) {
		return $fieldValue;
	}

}
