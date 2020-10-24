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

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
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
	public $helpers = ['BcText'];

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $options) {
		$editorOptions = [
			'editor' => $this->_View->viewVars['siteConfig']['editor'],
			'editorEnterBr' => $this->_View->viewVars['siteConfig']['editor_enter_br'],
			'editorWidth' => $options['width'],
			'editorHeight' => $options['height'],
			'editorToolType' => $options['editor_tool_type'],
		];
		$options = array_merge($editorOptions, $options);
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
