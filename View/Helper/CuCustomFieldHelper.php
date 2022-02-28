<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.View
 * @license          MIT LICENSE
 */

App::uses('CuCustomFieldAppHelper', 'CuCustomField.View/Helper');
/**
 * Class CuCustomFieldHelper
 *
 * @property BcFormHelper $BcForm
 * @property BcHtmlHelper $BcHtml
 * @property BcBaserHelper $BcBaser
 */
class CuCustomFieldHelper extends CuCustomFieldAppHelper
{

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcForm', 'Blog.Blog', 'BcBaser', 'BcTime', 'BcText', 'BcHtml'];

	/**
	 * カスタムフィールド設定情報
	 *
	 * @var array
	 */
	public $customFieldConfig = [];

	/**
	 * カスタムフィールドデータ・モデル
	 *
	 * @var Object
	 */
	public $CuCustomFieldValueModel = null;

	/**
	 * カスタムフィールドのフィールド別設定データ
	 *
	 * @var array
	 */
	public $publicFieldConfigData = [];

	/**
	 * constructor
	 * - 記事に設定されているカスタムフィールド設定情報を取得する
	 *
	 * @param View $View
	 * @param array $settings
	 */
	public function __construct(View $View, $settings = [])
	{
		parent::__construct($View, $settings);
		$this->customFieldConfig = Configure::read('cuCustomField');
		$this->CuCustomFieldValueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$this->loadPluginHelper();
	}

	/**
	 * setup
	 * @param $contentId
	 */
	public function setup($contentId) {
		if(!isset($this->publicFieldConfigData[$contentId])) {
			$this->CuCustomFieldValueModel->setup($contentId);
			$this->publicFieldConfigData = $this->CuCustomFieldValueModel->publicFieldConfigData;
		}
	}

	/**
	 * フィールド名を指定して、カスタムフィールドのフィールド設定内容を取得する
	 *
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getFieldAttribute($post, $field, $attribute = 'label_name')
	{
		$data = '';
		// コンテンツのIDを設定
		$contentId = $post['BlogPost']['blog_content_id'];
		$this->setup($contentId);
		foreach($this->publicFieldConfigData as $key => $fieldConfig) {
			if ($contentId == $key) {
				if (isset($fieldConfig[$field])) {
					$data = $fieldConfig[$field][$attribute];
				} else {
					$data = '';
				}
			}
		}
		return $data;
	}

	/**
	 * 指定したコンテンツIDのフィールド設定一覧を取得する
	 *
	 * @param int $contentId
	 * @return array
	 */
	public function getFieldConfigList($contentId)
	{
		$this->setup($contentId);
		foreach($this->publicFieldConfigData as $key => $fieldConfigList) {
			if ($contentId == $key) {
				return $fieldConfigList;
			}
		}
		return [];
	}

	/**
	 * 指定したコンテンツIDのフィールド設定内の、指定したフィールド名の設定内容を取得する
	 *
	 * @param int $contentId
	 * @param string $fieldName
	 * @return array
	 */
	public function getFieldConfig($contentId, $fieldName)
	{
		$configList = $this->getFieldConfigList($contentId);
		if ($configList) {
			foreach($configList as $key => $fieldConfig) {
				if ($key === $fieldName) {
					return $fieldConfig;
				}
			}
		}
		return [];
	}

	/**
	 * 指定したコンテンツIDのフィールド設定内の、指定したフィールド名の設定内容の選択リスト一覧を取得する
	 *
	 * @param int $contentId
	 * @param string $fieldName
	 * @return array
	 */
	public function getFieldConfigChoice($contentId, $fieldName)
	{
		$selector = [];
		$config = $this->getFieldConfig($contentId, $fieldName);
		if ($config) {
			if (Hash::get($config, 'choices')) {
				$selector = $this->textToArray(Hash::get($config, 'choices'));
			}
		}
		return $selector;
	}

	/**
	 * フィールド名を指定して、プチカスタムフィールドのデータを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function get($post = [], $field = '', $options = [])
	{
		$options = Hash::merge([
			'novalue' => '',
			'model' => 'CuCustomFieldValue'
		], $options);

		if (!$field) {
			return '';
		}
		if(isset($post[$options['model']][$field])) {
			$fieldValue = $post[$options['model']][$field];
		} elseif(isset($post[$field])) {
			$fieldValue = $post[$field];
		} else {
			return '';
		}

		if(isset($post[$options['model']][$field . '_tmp'])) {
			$options['tmp'] = $post[$options['model']][$field . '_tmp'];
		} elseif(isset($post[$field . '_tmp'])) {
			$options['tmp'] = $post[$field . '_tmp'];
		}

		if(isset($post['BlogPost']['blog_content_id'])) {
			$contentId = $post['BlogPost']['blog_content_id'];
		} elseif(isset($this->publicFieldConfigData)) {
			$contentId = key($this->publicFieldConfigData);
		} else {
			return '';
		}

		$this->setup($contentId);
		$fieldConfig = $this->publicFieldConfigData[$contentId];
		if(empty($fieldConfig[$field])) {
			return '';
		}

		$fieldDefinition = $fieldConfig[$field];
		$fieldType = $fieldDefinition['field_type'];
		if($fieldType === 'loop') {
			return $fieldValue;
		} else {
			$pluginName = 'CuCf' . Inflector::camelize($fieldType);
			if(method_exists($this->{$pluginName}, 'get')) {
				return $this->{$pluginName}->get($fieldValue, $fieldDefinition, $options);
			}
		}
		return '';
	}

	/**
	 * タイプに応じたフォームの入力形式を出力する
	 *
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function input($field, $definition, $options = [])
	{
		if(isset($definition['CuCustomFieldDefinition'])) {
			$definition = $definition['CuCustomFieldDefinition'];
		}
		$fieldType = $definition['field_type'];
		$pluginName = 'CuCf' . Inflector::camelize($fieldType);
		if(method_exists($this->{$pluginName}, 'input')) {
			return $this->{$pluginName}->input($field, $definition, $options);
		}
		return '';
	}

	/**
	 * 各フィールド別の表示判定を行う
	 *
	 * @param array $data
	 * @param array $options
	 * @return boolean
	 */
	public function judgeShowFieldConfig($data = [], $options = [])
	{
		$_options = [
			'field' => '',
		];
		$options = array_merge($_options, $options);

		if ($data) {
			if (isset($data['CuCustomFieldDefinition'])) {
				if ($data['CuCustomFieldDefinition'][$options['field']]) {
					return true;
				}
			} else {
				$key = key($data);
				if ($data[$key][$options['field']]) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * カスタムフィールドが有効になっているか判定する
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function judgeStatus($data = [])
	{
		if ($data) {
			if (isset($data['CuCustomFieldDefinition'])) {
				if ($data['CuCustomFieldDefinition']['status']) {
					return true;
				}
			} else {
				$key = key($data);
				if ($data[$key]['status']) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * カスタムフィールドを持っているか判定する
	 *
	 * @param array $data
	 * @return int
	 */
	public function hasCustomField($data = [])
	{
		$count = 0;
		if ($data['CuCustomFieldDefinition']) {
			$count = count($data['CuCustomFieldDefinition']);
		}
		return $count;
	}

	/**
	 * 利用状態を判定する
	 *
	 * @param array $data
	 * @param string $modelName
	 * @return boolean 未使用状態
	 */
	public function allowPublish($data, $modelName = '')
	{
		if ($modelName) {
			$data = $data[$modelName];
		} else {
			if (isset($data['CuCustomFieldDefinition'])) {
				$data = $data['CuCustomFieldDefinition'];
			} elseif (isset($data['CuCustomFieldConfig'])) {
				$data = $data['CuCustomFieldConfig'];
			}
		}
		$allowPublish = (int)$data['status'];
		return $allowPublish;
	}

	/**
	 * KeyValu形式のデータを、['Model']['key'] = value に変換する
	 *
	 * @param array $data
	 * @return array
	 */
	public function convertKeyValueToModelData($data = [])
	{
		$dataField = [];
		if (isset($data['CuCustomFieldDefinition'])) {
			$dataField[]['CuCustomFieldDefinition'] = $data['CuCustomFieldDefinition'];
		}

		$detailArray = [];
		foreach($dataField as $value) {
			$keyArray = preg_split('/\./', $value['CuCustomFieldDefinition']['key'], 2);
			$detailArray[$keyArray[0]][$keyArray[1]] = $value['CuCustomFieldDefinition']['value'];
		}
		return $detailArray;
	}

	/**
	 * カスタムフィールド一覧を表示する
	 *
	 * @param array $post
	 * @param array $options
	 * @return void
	 */
	public function showCustomField($post = [], $options = [])
	{
		$_options = [
			'template' => 'cu_custom_field_block'
		];
		$options = Hash::merge($_options, $options);
		extract($options);

		$this->BcBaser->element('CuCustomField.' . $template, ['plugin' => 'cu_custom_field', 'post' => $post]);
	}

	/**
	 * 初期値設定用として、キー（値）と名称を表示させた都道府県リストを取得する
	 *
	 * @return array
	 */
	public function previewPrefList()
	{
		$prefList = $this->BcText->prefList();
		foreach($prefList as $key => $value) {
			if (!$key) {
				$prefList[$key] = '値 ＝ ' . $value;
			} else {
				$prefList[$key] = $key . ' ＝ ' . $value;
			}
		}
		return $prefList;
	}

	/**
	 * フィールド定義一覧で上へ移動ボタンが利用可能かどうか
	 * @param $records
	 * @param $currentKey
	 * @return bool
	 */
	public function isAvailableDefinitionMoveUp($records, $currentKey)
	{
		$current = $records[$currentKey];
		$parentId = $current['CuCustomFieldDefinition']['parent_id'];
		for($i = $currentKey - 1; $i >= 0; $i--) {
			if (isset($records[$i])) {
				if ($records[$i]['CuCustomFieldDefinition']['parent_id'] === $parentId) {
					return true;
				}
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * フィールド定義一覧で下へ移動ボタンが利用可能かどうか
	 * @param $records
	 * @param $currentKey
	 * @return bool
	 */
	public function isAvailableDefinitionMoveDown($records, $currentKey)
	{
		$current = $records[$currentKey];
		$parentId = $current['CuCustomFieldDefinition']['parent_id'];
		for($i = $currentKey + 1; $i <= count($records) - 1; $i++) {
			if (isset($records[$i])) {
				if ($records[$i]['CuCustomFieldDefinition']['parent_id'] === $parentId) {
					return true;
				}
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * プラグインのフィールド定義の入力欄を読み込む
	 */
	public function loadPluginDefinitionInputs() {
		$plugins = Configure::read('cuCustomField.plugins');
		if($plugins) {
			foreach($plugins as $plugin => $value) {
				$pluginPath = $value['path'];
				if(file_exists($pluginPath . 'webroot' . DS . 'js' . DS . 'admin' . DS . 'definition_input.js')) {
					$this->BcBaser->js($plugin . '.admin/definition_input', false);
				}
				if(file_exists($pluginPath . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'definition_input.php')) {
					$this->BcBaser->element($plugin . '.admin/definition_input');
				}
			}
		}
	}

	/**
	 * プラグインのヘルパーを読み込む
	 */
	public function loadPluginHelper() {
		$plugins = Configure::read('cuCustomField.plugins');
		if($plugins) {
			foreach($plugins as $plugin => $value) {
				$pluginPath = $value['path'];
				if(!empty($value['fieldType'])) {
					foreach($value['fieldType'] as $fieldType) {
						$helper = 'CuCf' . Inflector::camelize($fieldType);
						if(file_exists($pluginPath . 'View' . DS . 'Helper' . DS . $helper . 'Helper.php')) {
							$this->{$helper} = $this->_View->loadHelper($plugin . '.' . $helper);
							$this->{$helper}->CuCustomField = $this;
						}
					}
				}
			}
		}
	}

}
