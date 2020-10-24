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

/**
 * Class CuCustomFieldHelper
 *
 * @property BcFormHelper $BcForm
 * @property BcHtmlHelper $BcHtml
 * @property BcBaserHelper $BcBaser
 */
class CuCustomFieldHelper extends AppHelper
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
	 * カスタムフィールドへの入力データ
	 *
	 * @var array
	 */
	public $publicFieldData = [];

	/**
	 * カスタムフィールドのフィールド別設定データ
	 *
	 * @var array
	 */
	public $publicFieldConfigData = [];

	/**
	 * カスタムフィールド設定データ
	 *
	 * @var array
	 */
	public $publicConfigData = [];

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

		// 記事に設定されているカスタムフィールド情報を取得する
		if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldValue')) {
			$this->CuCustomFieldValueModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldValue');
		} else {
			$this->CuCustomFieldValueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		}
		$this->publicConfigData = $this->CuCustomFieldValueModel->publicConfigData;
		$this->publicFieldConfigData = $this->CuCustomFieldValueModel->publicFieldConfigData;
		$this->publicFieldData = $this->CuCustomFieldValueModel->publicFieldData;
		$this->loadPluginHelper();
	}

	/**
	 * フィールド名を指定して、プチカスタムフィールドのフィールド設定内容を取得する
	 *
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getField($field = '', $options = [])
	{
		$data = '';
		$_options = [
			'field' => 'label_name',
		];
		$options = Hash::merge($_options, $options);
		if (!$field) {
			return '';
		}

		// コンテンツのIDを設定
		$contentId = $this->_View->viewVars['blogContent']['BlogContent']['id'];

		foreach($this->publicFieldConfigData as $key => $fieldConfig) {
			if ($contentId == $key) {
				if (isset($fieldConfig[$field])) {
					$data = $fieldConfig[$field][$options['field']];
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
	 * フィールド名を指定して、プチカスタムフィールドのフィールド設定内容を取得する
	 *
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getPdcfDataField($field = '', $options = [])
	{
		if (Configure::read('debug') > 0) {
			trigger_error(deprecatedMessage('ヘルパーメソッド：CuCustomFieldHelper::getPdcfDataField()', '1.0.0-beta', '1.0.0', '$this->CuCustomField->getField() を利用してください。'), E_USER_DEPRECATED);
		}
		return $this->getField($field, $options);
	}

	/**
	 * フィールド名を指定して、プチカスタムフィールドのデータを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return mixes
	 */
	public function get($post = [], $field = '', $options = [])
	{
		$data = '';
		$_options = [
			'novalue' => '',
			'format' => 'Y-m-d',
			'model' => 'CuCustomFieldValue',
			'separator' => ', ',
		];
		$options = Hash::merge($_options, $options);
		if (!$field) {
			return '';
		}
		if (!isset($post[$options['model']])) {
			return '';
		}
		// カスタムフィールドで取得するモデル名
		$modelName = $options['model'];
		// カスタムフィールドの値。フィールド有無を判定し、ない場合は空文字を返す
		if (!isset($post[$modelName][$field])) {
			return '';
		}
		$fieldValue = $post[$modelName][$field];

		// 記事のコンテンツID
		$contentId = $post['BlogPost']['blog_content_id'];

		// -----------------------------------------------------------------------------
		$fieldConfig = $this->publicFieldConfigData[$contentId];
		$fieldDefinition = $fieldConfig[$field];
		$fieldType = $fieldDefinition['field_type'];
		if(in_array($fieldType, ['related', 'file'])) {
			$pluginName = 'CuCf' . Inflector::camelize($fieldType);
			if(method_exists($this->{$pluginName}, 'get')) {
				return $this->{$pluginName}->get($fieldValue, $fieldDefinition, $options);
			}
		}
		// -----------------------------------------------------------------------------

		foreach($this->publicFieldConfigData as $key => $fieldConfig) {
			if ($contentId == $key) {
				// 記事データには存在するが、記事に設定中のフィールド一覧にないものは利用しないために判定
				if (!empty($fieldConfig[$field])) {
					$fieldType = $fieldConfig[$field]['field_type'];
					switch($fieldType) {
						case 'text':
							$data = $fieldValue;
							break;

						case 'textarea':
							$data = $fieldValue;
							break;

						case 'date':
							$data = $this->BcTime->format($options['format'], $fieldValue, $invalid = false, $userOffset = null);
							break;

						case 'datetime':
							$data = $this->BcTime->format($options['format'], $fieldValue, $invalid = false, $userOffset = null);
							break;

						case 'select':
							$selector = $this->textToArray($fieldConfig[$field]['choices']);
							$data = $this->arrayValue($fieldValue, $selector, $options['novalue']);
							break;

						case 'radio':
							$selector = $this->textToArray($fieldConfig[$field]['choices']);
							$data = $this->arrayValue($fieldValue, $selector, $options['novalue']);
							break;

						case 'checkbox':
							if ($fieldValue) {
								$data = true;
							} else {
								$data = false;
							}
							break;

						case 'multiple':
							$selector = $this->textToArray($fieldConfig[$field]['choices']);
							$checked = [];
							if (!empty($fieldValue)) {
								if (is_array($fieldValue)) {
									foreach($fieldValue as $check) {
										$checked[] = $this->arrayValue($check, $selector);
									}
								} else {
									$checked[] = $fieldValue;
								}
							}
							$data = implode($options['separator'], $checked);
							break;

						case 'pref':
							$selector = $this->BcText->prefList();
							$data = $this->arrayValue($fieldValue, $selector, $options['novalue']);
							break;

						case 'wysiwyg':
							$data = $fieldValue;
							break;

						case 'googlemaps':
							$data = $fieldValue;
							break;

						default:
							$data = $fieldValue;
							break;
					}
				}
			}
		}
		return $data;
	}

	/**
	 * フィールド名を指定して、プチカスタムフィールドのデータを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return mixes
	 */
	public function getPdcfData($post = [], $field = '', $options = [])
	{
		if (Configure::read('debug') > 0) {
			trigger_error(deprecatedMessage('ヘルパーメソッド：CuCustomFieldHelper::getPdcfData()', '1.0.0-beta', '1.0.0', '$this->CuCustomField->get() を利用してください。'), E_USER_DEPRECATED);
		}
		return $this->get($post, $field, $options);
	}

	/**
	 * フィールド名を指定して、Googleマップの表示データを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getGoogleMaps($post = [], $field = '', $options = [])
	{
		$data = $this->get($post, $field, $options);
		if (!$data) {
			return false;
		}

		$elementOptions = [
			'googleMapsPopupText' => true,
			'googleMapsWidth' => '100%',
			'googleMapsHeight' => '400px',
		];

		foreach($elementOptions as $key => $var) {
			if (isset($options[$key])) {
				$data[$key] = $options[$key];
			} else {
				$data[$key] = $var;
			}
		}

		return $this->BcBaser->getElement('CuCustomField.cu_custom_google_maps', $data);
	}

	/**
	 * フィールド名を指定して、Googleマップのテキストデータを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function getGoogleMapsText($post = [], $field = '', $options = [])
	{
		$data = $this->get($post, $field, $options);
		if (isset($data['google_maps_text'])) {
			return $data['google_maps_text'];
		}
	}

	/**
	 * フォームのタイプを判定して、タイプ別の入力フォームを生成する
	 *
	 * @param array $data
	 * @param string $section モデル名を指定: 複数モデルのデータの場合、ここで指定したモデル名のデータを利用する
	 * @param array $options
	 * @return array
	 */
	public function getFormOption($data = [], $section = '', $options = [])
	{
		$formOption = [];

		if ($data) {
			$modelName = key($data);
			// モデル名の指定を優先する
			if ($section) {
				$modelName = $section;
			}
			// フィールドのタイプを判定用に設定する
			$fieldType = $data[$modelName]['field_type'];
			$_formOption = [
				'type' => $fieldType,
			];

			switch($fieldType) {
				case 'text':
					if ($data[$modelName]['size']) {
						$_formOption = array_merge($_formOption, ['size' => $data[$modelName]['size']]);
					}
					if ($data[$modelName]['max_length']) {
						$_formOption = array_merge($_formOption, ['maxlength' => $data[$modelName]['max_length']]);
					} else {
						$_formOption = array_merge($_formOption, ['maxlength' => '255']);
					}
					if ($data[$modelName]['counter']) {
						$_formOption = array_merge($_formOption, ['counter' => $data[$modelName]['counter']]);
					}
					if ($data[$modelName]['placeholder']) {
						$_formOption = array_merge($_formOption, ['placeholder' => $data[$modelName]['placeholder']]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'textarea':
					if ($data[$modelName]['rows']) {
						$_formOption = array_merge($_formOption, ['rows' => $data[$modelName]['rows']]);
					}
					if ($data[$modelName]['cols']) {
						$_formOption = array_merge($_formOption, ['cols' => $data[$modelName]['cols']]);
					}
					if ($data[$modelName]['placeholder']) {
						$_formOption = array_merge($_formOption, ['placeholder' => $data[$modelName]['placeholder']]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'date':
					if ($data[$modelName]['size']) {
						$_formOption = array_merge($_formOption, ['size' => $data[$modelName]['size']]);
					} else {
						$_formOption = array_merge($_formOption, ['size' => 12]);
					}
					if ($data[$modelName]['max_length']) {
						$_formOption = array_merge($_formOption, ['maxlength' => $data[$modelName]['max_length']]);
					} else {
						$_formOption = array_merge($_formOption, ['maxlength' => 10]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'datetime':
					if ($data[$modelName]['size']) {
						$_formOption = array_merge($_formOption, ['size' => $data[$modelName]['size']]);
					} else {
						$_formOption = array_merge($_formOption, ['size' => 12]);
					}
					if ($data[$modelName]['max_length']) {
						$_formOption = array_merge($_formOption, ['maxlength' => $data[$modelName]['max_length']]);
					} else {
						$_formOption = array_merge($_formOption, ['maxlength' => 10]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'select':
					if ($data[$modelName]['choices']) {
						$option = $this->textToArray($data[$modelName]['choices']);
						$_formOption = array_merge($_formOption, ['options' => $option]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'radio':
					if ($data[$modelName]['choices']) {
						$option = $this->textToArray($data[$modelName]['choices']);
						$_formOption = array_merge($_formOption, ['options' => $option]);
					}
					if ($data[$modelName]['separator']) {
						$_formOption = array_merge($_formOption, ['separator' => $data[$modelName]['separator']]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'checkbox':
					if ($data[$modelName]['label_name']) {
						$_formOption = array_merge($_formOption, ['label' => $data[$modelName]['label_name']]);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'multiple':
					$_formOption['type'] = 'select';
					if ($data[$modelName]['choices']) {
						$option = $this->textToArray($data[$modelName]['choices']);
						$_formOption = array_merge($_formOption, ['options' => $option, $fieldType => 'checkbox']);
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'pref':
					$_formOption['type'] = 'select';
					$_formOption = array_merge($_formOption, ['options' => $this->BcText->prefList()]);
					$formOption = Hash::merge($formOption, $_formOption);
					break;

				case 'wysiwyg':
					if ($data[$modelName]['rows']) {
						$_formOption = array_merge($_formOption, ['height' => $data[$modelName]['rows']]);
					} else {
						$_formOption = array_merge($_formOption, ['height' => '200px']);
					}
					if ($data[$modelName]['cols']) {
						$_formOption = array_merge($_formOption, ['width' => $data[$modelName]['cols']]);
					} else {
						$_formOption = array_merge($_formOption, ['width' => '100%']);
					}
					$_formOption = array_merge($_formOption, [
						'editor_tool_type' => $data[$modelName]['editor_tool_type'],
					]);
					$formOption = Hash::merge($formOption, $_formOption);
					break;
				case 'googlemaps':
					$_formOption['definitions'] = $data;
					$formOption = Hash::merge($formOption, $_formOption);
					break;
				case 'related':
					$_formOption['related'] = $data[$modelName]['option_meta']['related'];
					$formOption = Hash::merge($formOption, $_formOption);
					break;
				default:
					$formOption = Hash::merge($formOption, $_formOption);
					break;
			}
		}

		return $formOption;
	}

	/**
	 * タイプに応じたフォームの入力形式を出力する
	 *
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function input($field, $options = [])
	{
		$fieldType = $options['type'];

		// -----------------------------------------------------------------------------
		if(in_array($fieldType, ['related', 'file'])) {
			$pluginName = 'CuCf' . Inflector::camelize($fieldType);
			if(method_exists($this->{$pluginName}, 'input')) {
				return $this->{$pluginName}->input($field, $options);
			}
		}
		// -----------------------------------------------------------------------------

		switch($fieldType) {
			case 'date':
				$options['type'] = 'datepicker';
				$options['class'] = 'bca-textbox__input';
				$formString = $this->BcForm->input($field, $options);
				break;
			case 'datetime':
				$options['type'] = 'dateTimePicker';
				$formString = $this->BcForm->input($field, $options);
				break;
			case 'wysiwyg':
				$editorOptions = [
					'editor' => $this->_View->viewVars['siteConfig']['editor'],
					'editorEnterBr' => $this->_View->viewVars['siteConfig']['editor_enter_br'],
					'editorWidth' => $options['width'],
					'editorHeight' => $options['height'],
					'editorToolType' => $options['editor_tool_type'],
				];
				$options = array_merge($editorOptions, $options);
				$formString = $this->BcForm->ckeditor($field, $options);
				break;
			case 'googlemaps':
				$formString = $this->_View->element('CuCustomField.admin/cu_custom_field_values/input_block/google_maps', ['definitions' => $options['definitions']]);
				break;
			default:
				$formString = $this->BcForm->input($field, $options);
				break;
		}

		return $formString;
	}

	/**
	 * 配列とキーを指定して値を取得する
	 * - グループ指定のある配列に対応
	 *
	 * @param int $key
	 * @param array $array
	 * @param string $noValue
	 * @return mixied
	 */
	public function arrayValue($key, $array, $noValue = '')
	{
		if (is_numeric($key)) {
			$key = (int)$key;
		}
		if (isset($array[$key])) {
			return $array[$key];
		}
		// グループ指定がある場合の判定
		foreach($array as $group => $list) {
			if (isset($list[$key])) {
				return $list[$key];
			}
		}
		return $noValue;
	}

	/**
	 * テキスト情報を配列形式に変換して返す
	 * - 改行で分割する
	 * - 区切り文字で分割する
	 *
	 * @param string $str
	 * @return mixed
	 */
	public function textToArray($str = '')
	{
		// "CR + LF: \r\n｜CR: \r｜LF: \n"
		$code = ['\r\n', '\r'];
		// 文頭文末の空白を削除する
		$str = trim($str);
		// 改行コードを統一する（改行コードを変換する際はダブルクォーテーションで指定する）
		//$str = str_replace($code, '\n', $str);
		$str = preg_replace('/\r\n|\r|\n/', "\n", $str);
		// 分割（結果は配列に入る）
		// 文字によっては文字化けを起こして正しく配列に変換されない
		// preg系は、UTF8文字列を扱う場合はu修飾子が必要
		$str = preg_split('/[\s,]+/u', $str);
		//$str = explode('\n', $str);
		// 区切り文字を利用して、キーと値を指定する場合の処理
		$keyValueArray = [];
		foreach($str as $key => $value) {
			$array = preg_split('/[:]+/', $value);
			if (count($array) > 1) {
				$keyValueArray[$array[1]] = $array[0];
			} else {
				$keyValueArray[$key] = $value;
			}
		}
		if ($keyValueArray) {
			return $keyValueArray;
		}

		return $str;
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
	 * 未使用状態を判定する
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
	public function showCuCustomField($post = [], $options = [])
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
			foreach($plugins as $plugin) {
				$pluginPath = CakePlugin::path('CuCustomField') . 'Plugin' . DS . $plugin . DS;
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
			foreach($plugins as $plugin) {
				$pluginPath = CakePlugin::path('CuCustomField') . 'Plugin' . DS . $plugin . DS;
				if(file_exists($pluginPath . 'View' . DS . 'Helper' . DS . $plugin . 'Helper.php')) {
					$this->{$plugin} = $this->_View->loadHelper($plugin . '.' . $plugin);
					$this->{$plugin}->CuCustomField = $this;
				}
			}
		}
	}

}
