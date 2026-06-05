<?php
namespace CuCustomField\View\Helper;

use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

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
#[\AllowDynamicProperties]
class CuCustomFieldHelper extends CuCustomFieldAppHelper
{
	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	protected array $helpers = [
        'BcForm',
        'Blog.Blog',
        'BcBaser',
        'BcTime',
        'BcText',
        'BcHtml'
    ];

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
	public function __construct(\Cake\View\View $View, $settings = [])
	{
		parent::__construct($View, $settings);
		$this->customFieldConfig = \Cake\Core\Configure::read('cuCustomField');
		$this->CuCustomFieldValueModel = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValues');
		$this->loadPluginHelper();
	}

	/**
	 * setup
	 * @param $contentId
	 */
	public function setup($contentId) {
        if(!$contentId){
            return;
        }

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
		$contentId = $post['blog_content_id'];
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
			if (\Cake\Utility\Hash::get($config, 'choices')) {
				$selector = $this->textToArray(\Cake\Utility\Hash::get($config, 'choices'));
			}
		}
		return $selector;
	}

	/**
	 * フィールド名を指定して、カスタムフィールドのデータを取得する
	 *
	 * @param array $post
	 * @param string $field
	 * @param array $options
	 * @return string
	 */
	public function get($post = [], $field = '', $options = [])
	{
		$options = \Cake\Utility\Hash::merge([
			'novalue' => '',
			'model' => 'CuCustomFieldValues'
		], $options);

		if (!$field) {
			return '';
		}
        // $postにcu_custom_field_valuesがセットされているので、findしないで配列処理
        if (empty($post->CuCustomFieldValues)
            && !empty($post->cu_custom_field_values)) {
            $options['model'] = 'cu_custom_field_values';
            foreach ($post->cu_custom_field_values as $field_value) {
                if (is_string($field_value)) continue;
                if (!empty($field_value->key) && !empty($field_value->value)) {
                    $modelKey = str_replace('CuCustomFieldValue.', '', $field_value->key);
                    $post[$options['model']][$modelKey] = $field_value->value;
                }
            }
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

		if(isset($post['blog_content_id'])) {
			$contentId = $post['blog_content_id'];
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
		$fieldType = (string) ($fieldDefinition['field_type'] ?? '');
		if ($fieldType === '') {
			return '';
		}

		if($fieldType === 'loop') {
			return unserialize($fieldValue);
		} else {
            return $this->customFieldGet($fieldValue, $fieldDefinition, $options);
		}
		return '';
	}

    /**
     * フィールドの値を取得する
     *
     * @param string $fieldValue
     * @param array $fieldDefinition
     * @param array $options
     * @return string
     */
	private function customFieldGet($fieldValue, $fieldDefinition, $options = []) {
        switch($fieldDefinition['field_type']){
            case 'mjPartner':
                $related = unserialize($fieldDefinition['option_meta']);
                return $this->getRelatedCustomValue($related['related']['table'], null, (int)$fieldValue);
            case 'multiple':
                $selector = $this->textToArray($fieldDefinition['choices']);
                $checked = [];
                if (!empty($fieldValue)) {
                    // multipleは複数選択を考慮しデシリアライズする
                    $checkedIds = unserialize($fieldValue);
                    if (is_array($checkedIds)) {
                        foreach($checkedIds as $check) {
                            $checked[] = $this->arrayValue($check, $selector);
                        }
                    } else {
                        $checked[] = $fieldValue;
                    }
                }
                return implode(', ', $checked);
            case 'select':
                $selector = $this->textToArray($fieldDefinition['choices']);
                $selected = '';
                // selectは単一選択を前提とする
                if ($fieldValue !== null) {
                    $selected = $this->arrayValue($fieldValue, $selector);
                }
                return $selected;
            case 'related':
            case 'text':
            case 'textarea':
                return $fieldValue;
            case 'file':
                $options = array_merge([
                    'output' => 'tag'
                ], $options);

                if($fieldValue) {
                    if($options['output'] === 'tag') {
                        $checkValue = $fieldValue;
                        if(isset($options['tmp'])) {
                            $checkValue = $options['tmp'];
                        }
                        if(is_string($checkValue) && in_array(pathinfo($checkValue, PATHINFO_EXTENSION), ['png', 'gif', 'jpeg', 'jpg'])) {
                            $data = $this->uploadImage($fieldValue, $options);
                        } else {
                            $options['label'] = $fieldDefinition['name'];
                            $data = $this->fileLink($fieldValue, $options);
                        }
                    } elseif($options['output'] === 'url') {
                        $data = is_string($fieldValue) ? '/files' . DS . 'cu_custom_field' . DS . $fieldValue : '';
                    } else {
                        $data = $fieldValue;
                    }
                } else {
                    $data = '';
                }
                return $data;
            default:
                return $fieldValue;
        }
	}
    public function getRelatedCustomValue($tableName, $fieldName, $id)
    {
        $tableAlias = Inflector::camelize($tableName);
        $table = TableRegistry::getTableLocator()->get('Mj.'.$tableAlias); // プラグイン名は取るのが面倒
        $entity = $table->findById($id)->first();
        if($fieldName){
            return $entity && isset($entity[$fieldName]) ? $entity[$fieldName] : null;
        }else{
            return $entity;
        }
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

		$fieldType = (string) ($definition['field_type'] ?? '');
		if ($fieldType === '') {
			return '';
		}
		$pluginName = 'CuCf' . \Cake\Utility\Inflector::camelize($fieldType);
        $view = $this->getView();
        // プラグインフォルダからヘルパーを読み込む
        $cuCfHelper = $view->helpers()->load("$pluginName.$pluginName");
		if(method_exists($cuCfHelper, 'input')) {
			return $cuCfHelper->input($field, $definition, $options);
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
                // php 8.3対応 key()を object に対してコールすることは、推奨されなくなりました。
                if (!is_array($data)) $data = get_mangled_object_vars($data);
				$key = key($data);
				if (!empty($data[$key][$options['field']])) {
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
			if (isset($data->CuCustomFieldDefinitions)) {
				if ($data->CuCustomFieldDefinitions['status']) {
					return true;
				}
			} else {
                if (!empty($data['status'])) return true;
				$key = key($data);
				if (!empty($data[$key]['status'])) {
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
			$data = isset($data[$modelName]) ? $data[$modelName] : $data;
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
		$options = \Cake\Utility\Hash::merge($_options, $options);
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
		$fieldTypes = (array) \Cake\Core\Configure::read('CuCustomField.fieldTypes');
		$element = 'definition_input';
		foreach ((array) $fieldTypes as $plugin => $value) {
			if ($plugin === 'group') {
				continue;
			}
			if (!\Cake\Core\Plugin::isLoaded((string) $plugin)) {
				continue;
			}

			$templateElement = \Cake\Core\Plugin::templatePath((string) $plugin) . 'Admin' . DS . 'element' . DS . $element . '.php';
			if (file_exists($templateElement)) {
				$this->BcBaser->element($plugin . '.' . $element);
				continue;
			}

			$pluginPath = \Cake\Core\Plugin::path((string) $plugin);
			$legacyElement = $pluginPath . 'View' . DS . 'Elements' . DS . 'admin' . DS . $element . '.php';
			$legacyTemplateElement = $pluginPath . 'template' . DS . 'Admin' . DS . 'element' . DS . $element . '.php';
			if (file_exists($legacyElement)) {
				$this->BcBaser->element($plugin . '.admin/' . $element);
				continue;
			}
			if (file_exists($legacyTemplateElement)) {
				$this->renderPhpElement($legacyTemplateElement);
			}
		}
	}

	/**
	 * element PHPを直接読み込み表示する
	 *
	 * @param string $filePath
	 * @return void
	 */
	private function renderPhpElement(string $filePath): void
	{
		$view = $this->getView();
		extract($view->getVars());
		include $filePath;
	}

	/**
	 * プラグインのヘルパーを読み込む
	 */
	public function loadPluginHelper() {
		$plugins = \Cake\Core\Configure::read('CuCustomField.fieldTypes');

		if($plugins) {
			foreach($plugins as $plugin => $value) {
				$pluginPath = \Cake\Core\Plugin::path($plugin);
				if(!empty($value['controlType'])) {
                    // プラグインのヘルパーを読み込む
                    // フィールドタイプが配列の場合
                    if (is_array($value['controlType'])) {
                        foreach($value['controlType'] as $controlType) {
                            $this->getControlTypeHelper($controlType, $plugin);
                        }
                    } else {
                        // フィールドタイプが文字列の場合
                        $this->getControlTypeHelper($value['controlType'], $plugin);
                    }
				}
			}
		}
	}

    /**
     * geControlTypeHelper
     * プラグインのヘルパーを読み込む（ヘルパー名を取得）
     * @param $controlType
     * @return void
     */
    private function getControlTypeHelper($controlType, $plugin)
    {
        $pluginPath = \Cake\Core\Plugin::path($plugin);
        $helper = 'CuCf' . \Cake\Utility\Inflector::camelize($controlType);
        if(file_exists($pluginPath . 'src' . DS. 'View' . DS . 'Helper' . DS . $helper . 'Helper.php')) {
            $this->{$helper} = $this->_View->loadHelper(
                $plugin,
                ['className' => "$plugin.$plugin"]
            );
            //$this->{$helper}->CuCustomField = $this;
        }
    }

	public function getSiteByBlogContentId($id)
	{
		$contentModel = \Cake\ORM\TableRegistry::getTableLocator()->get('Content');
		$content = $contentModel->findByType('Blog.BlogContent', $id);
		if(!$content) return '';
		return $content['Site'];
	}

    /**
	 * アップロード画像
	 * @param $fieldValue
	 * @param $options
	 * @return mixed|string
	 */
	public function uploadImage($fieldValue, $options)
	{
        $saveDir = '/files' . DS . 'cu_custom_field' . DS;
		$options = array_merge([
			'width' => (!empty($options['thumb']))? false : '100%',
			'thumb' => false,
            'full' => false,
		], $options);
		$noValue = $options['novalue'];
		$thumb = $options['thumb'];

		unset($options['format'], $options['model'], $options['separator'], $options['novalue'], $options['thumb']);
		if(!$fieldValue) {
			return $noValue;
		} else {
			if($thumb) {
				$fieldValue = preg_replace('/^(.+\/)([^\/]+)(\.[a-z]+)$/', "$1$2_thumb$3", $fieldValue);
			}
			if(!empty($options['tmp'])) {
				$fileUrl = '/uploads/tmp/' . str_replace(['.', '/'], ['_', '_'], $options['tmp']);
			} else {
				$fileUrl = $saveDir . $fieldValue;
			}

			return $this->BcBaser->getImg($fileUrl, $options);
		}
	}

	/**
	 * ファイルリンク
	 *
	 * @param string $fieldValue
	 * @param array $options
	 * @return mixed|string
	 */
	private function fileLink($fieldValue, $options) {
        $saveUrl = '/files' . DS . 'cu_custom_field' . DS;
		$options = array_merge([
			'target' => '_blank',
			'label' => 'ダウンロード'
		], $options);
		$noValue = $options['novalue'];
		$label = $options['label'];
		unset($options['format'], $options['model'], $options['separator'], $options['novalue']);

		if(!$fieldValue || !is_string($fieldValue)) {
			return $noValue;
		} else {
			return $this->BcHtml->link($label, $saveUrl . $fieldValue, $options);
		}
	}

    /**
	 * CustomFieldValueを後付けする
	 *
	 * @param array|EntityInterface $post
	 */
    public function appendCustomFieldValue($post)
    {
        if(!isset($post['id'])){
            return;
        }

        $data = $this->CuCustomFieldValueModel->getSection($post['id'], $this->CuCustomFieldValueModel);
        $post[$this->CuCustomFieldValueModel->getAlias()] = $data;

        return $post;
    }
}
