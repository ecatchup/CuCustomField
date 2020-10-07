<?php

/**
 * [ControllerEventListener] CuCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
class CuCustomFieldControllerEventListener extends BcControllerEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = array(
		'initialize',
		'Blog.Blog.beforeRender',
		'Blog.BlogPosts.beforeRender',
	);

	/**
	 * cu_custom_fieldヘルパー
	 *
	 * @var CuCustomFieldHelper
	 */
	public $CuCustomField = null;

	/**
	 * cu_custom_field設定情報
	 *
	 * @var array
	 */
	public $cuCustomFieldConfigs = array();

	/**
	 * cu_custom_fieldモデル
	 *
	 * @var Object
	 */
	public $CuCustomFieldValueModel = null;

	/**
	 * cu_custom_field設定モデル
	 *
	 * @var Object
	 */
	public $CuCustomFieldConfigModel = null;

	/**
	 * cu_custom_fieldフィールド名設定データ
	 *
	 * @var array
	 */
	public $settingsCuCustomField = array();

	/**
	 * initialize
	 *
	 * @param CakeEvent $event
	 */
	public function initialize(CakeEvent $event)
	{
		$Controller						 = $event->subject();
		// CuCustomFieldヘルパーの追加
		$Controller->helpers[]			 = 'CuCustomField.CuCustomField';
		$this->settingsCuCustomField	 = Configure::read('cuCustomField');
	}

	/**
	 * blogBeforeRender
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogBeforeRender(CakeEvent $event)
	{
		$Controller = $event->subject();
		$this->setUpModel($Controller);

		// プレビューの際は編集欄の内容を送る
		// 設定値を送る
		$Controller->viewVars['customFieldConfig'] = $this->settingsCuCustomField;

		if ($Controller->BcContents->preview) {
			if (!empty($Controller->request->data['CuCustomFieldValue'])) {
				$Controller->viewVars['post']['CuCustomFieldValue'] = $Controller->request->data['CuCustomFieldValue'];

				$this->CuCustomFieldValueModel->publicConfigData = $this->cuCustomFieldConfigs;

				$fieldConfigField																			 = $this->CuCustomFieldConfigModel->PetitCustomFieldConfigMeta->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$defaultFieldValue[$this->cuCustomFieldConfigs['CuCustomFieldConfig']['content_id']]	 = Hash::combine($fieldConfigField, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition');
				$this->CuCustomFieldValueModel->publicFieldConfigData											 = $defaultFieldValue;
			}
		}
	}

	/**
	 * blogPostsBeforeRender
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogPostsBeforeRender(CakeEvent $event)
	{
		$Controller = $event->subject();
		$this->setUpModel($Controller);

		// 設定値を送る
		$Controller->viewVars['customFieldConfig'] = $this->settingsCuCustomField;

		if (!$this->cuCustomFieldConfigs) {
			return;
		}

		// ブログ記事編集画面で実行
		// - startup で処理したかったが $Controller->request->data に入れるとそれを全て上書きしてしまうのでダメだった
		if ($Controller->request->params['action'] == 'admin_edit') {
			$Controller->request->data['CuCustomFieldConfig'] = $this->cuCustomFieldConfigs['CuCustomFieldConfig'];

			if ($this->cuCustomFieldConfigs['CuCustomFieldConfig']['status']) {
				$fieldConfigField = $this->PetitCustomFieldConfigMetaModel->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$Controller->set('fieldConfigField', $fieldConfigField);

				// フィールド設定から初期値を生成
				$defaultFieldValue								 = Hash::combine($fieldConfigField, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition.default_value');
				$this->CuCustomFieldValueModel->keyValueDefaults	 = array('CuCustomFieldValue' => $defaultFieldValue);
				$defalut										 = $this->CuCustomFieldValueModel->defaultValues();
				// 初期値と存在値をマージする
				if (!empty($Controller->request->data['CuCustomFieldValue'])) {
					$Controller->request->data['CuCustomFieldValue'] = Hash::merge($defalut['CuCustomFieldValue'], $Controller->request->data['CuCustomFieldValue']);
				} else {
					$Controller->request->data['CuCustomFieldValue'] = $defalut['CuCustomFieldValue'];
				}
			}
		}

		// ブログ記事追加画面で実行
		if ($Controller->request->params['action'] == 'admin_add') {
			$Controller->request->data['CuCustomFieldConfig'] = $this->cuCustomFieldConfigs['CuCustomFieldConfig'];

			if ($this->cuCustomFieldConfigs['CuCustomFieldConfig']['status']) {
				$fieldConfigField = $this->PetitCustomFieldConfigMetaModel->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$Controller->set('fieldConfigField', $fieldConfigField);

				// フィールド設定から初期値を生成
				if (empty($Controller->request->data['CuCustomFieldValue'])) {
					$defaultFieldValue								 = Hash::combine($fieldConfigField, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition.default_value');
					$this->CuCustomFieldValueModel->keyValueDefaults	 = array('CuCustomFieldValue' => $defaultFieldValue);
					$defalut										 = $this->CuCustomFieldValueModel->defaultValues();
					$Controller->request->data['CuCustomFieldValue']	 = $defalut['CuCustomFieldValue'];
				}
			}
		}
	}

	/**
	 * モデル登録用メソッド
	 *
	 * @param Controller $Controller
	 */
	private function setUpModel($Controller)
	{
		if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldConfig')) {
			$this->CuCustomFieldConfigModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldConfig');
		} else {
			$this->CuCustomFieldConfigModel = ClassRegistry::init('CuCustomField.CuCustomFieldConfig');
		}
		// $this->cuCustomFieldConfigs = $this->CuCustomFieldConfigModel->read(null, $Controller->BlogContent->id);
		$this->cuCustomFieldConfigs					 = $this->CuCustomFieldConfigModel->find('first', array(
			'conditions' => array('CuCustomFieldConfig.content_id' => $Controller->BlogContent->id),
			'recurseve'	 => -1,
		));
		$this->CuCustomFieldValueModel					 = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$this->CuCustomFieldValueModel->publicConfigData	 = $this->cuCustomFieldConfigs;

		if (ClassRegistry::isKeySet('CuCustomField.PetitCustomFieldConfigMeta')) {
			$this->PetitCustomFieldConfigMetaModel = ClassRegistry::getObject('CuCustomField.PetitCustomFieldConfigMeta');
		} else {
			$this->PetitCustomFieldConfigMetaModel = ClassRegistry::init('CuCustomField.PetitCustomFieldConfigMeta');
		}
	}

}
