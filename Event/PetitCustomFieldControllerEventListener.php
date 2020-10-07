<?php

/**
 * [ControllerEventListener] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
class PetitCustomFieldControllerEventListener extends BcControllerEventListener
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
	 * @var PetitCustomFieldHelper
	 */
	public $PetitCustomField = null;

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
	public $PetitCustomFieldModel = null;

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
	public $settingsPetitCustomField = array();

	/**
	 * initialize
	 *
	 * @param CakeEvent $event
	 */
	public function initialize(CakeEvent $event)
	{
		$Controller						 = $event->subject();
		// PetitCustomFieldヘルパーの追加
		$Controller->helpers[]			 = 'PetitCustomField.CuCustomFieldValue';
		$this->settingsPetitCustomField	 = Configure::read('petitCustomField');
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
		$Controller->viewVars['customFieldConfig'] = $this->settingsPetitCustomField;

		if ($Controller->BcContents->preview) {
			if (!empty($Controller->request->data['CuCustomFieldValue'])) {
				$Controller->viewVars['post']['CuCustomFieldValue'] = $Controller->request->data['CuCustomFieldValue'];

				$this->PetitCustomFieldModel->publicConfigData = $this->cuCustomFieldConfigs;

				$fieldConfigField																			 = $this->CuCustomFieldConfigModel->PetitCustomFieldConfigMeta->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$defaultFieldValue[$this->cuCustomFieldConfigs['CuCustomFieldConfig']['content_id']]	 = Hash::combine($fieldConfigField, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition');
				$this->PetitCustomFieldModel->publicFieldConfigData											 = $defaultFieldValue;
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
		$Controller->viewVars['customFieldConfig'] = $this->settingsPetitCustomField;

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
				$this->PetitCustomFieldModel->keyValueDefaults	 = array('CuCustomFieldValue' => $defaultFieldValue);
				$defalut										 = $this->PetitCustomFieldModel->defaultValues();
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
					$this->PetitCustomFieldModel->keyValueDefaults	 = array('CuCustomFieldValue' => $defaultFieldValue);
					$defalut										 = $this->PetitCustomFieldModel->defaultValues();
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
		if (ClassRegistry::isKeySet('PetitCustomField.CuCustomFieldConfig')) {
			$this->PetitCustomFieldConfigModel = ClassRegistry::getObject('PetitCustomField.CuCustomFieldConfig');
		} else {
			$this->PetitCustomFieldConfigModel = ClassRegistry::init('PetitCustomField.CuCustomFieldConfig');
		}
		// $this->cuCustomFieldConfigs = $this->PetitCustomFieldConfigModel->read(null, $Controller->BlogContent->id);
		$this->cuCustomFieldConfigs					 = $this->PetitCustomFieldConfigModel->find('first', array(
			'conditions' => array('PetitCustomFieldConfig.content_id' => $Controller->BlogContent->id),
			'recurseve'	 => -1,
		));
		$this->PetitCustomFieldModel					 = ClassRegistry::init('PetitCustomField.CuCustomFieldValue');
		$this->PetitCustomFieldModel->publicConfigData	 = $this->cuCustomFieldConfigs;

		if (ClassRegistry::isKeySet('PetitCustomField.PetitCustomFieldConfigMeta')) {
			$this->PetitCustomFieldConfigMetaModel = ClassRegistry::getObject('PetitCustomField.PetitCustomFieldConfigMeta');
		} else {
			$this->PetitCustomFieldConfigMetaModel = ClassRegistry::init('PetitCustomField.PetitCustomFieldConfigMeta');
		}
	}

}
