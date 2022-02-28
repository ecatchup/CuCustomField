<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Event
 * @license          MIT LICENSE
 */

/**
 * Class CuCustomFieldControllerEventListener
 *
 * @property CuCustomFieldDefinition $CuCustomFieldDefinitionModel
 * @uses CuCustomFieldControllerEventListener
 */
class CuCustomFieldControllerEventListener extends BcControllerEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = [
		'initialize',
		'Blog.Blog.beforeRender',
		'Blog.BlogPosts.beforeRender',
		'startup' => ['priority' => 1],	// CuApproverControllerEventListener::start() より早く
	];

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
	public $cuCustomFieldConfigs = [];

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
	public $settingsCuCustomField = [];

	/**
	 * initialize
	 *
	 * @param CakeEvent $event
	 */
	public function initialize(CakeEvent $event)
	{
		$Controller = $event->subject();
		// CuCustomFieldヘルパーの追加
		$Controller->helpers[] = 'CuCustomField.CuCustomField';
		$this->settingsCuCustomField = Configure::read('cuCustomField');
	}

	/**
	 * startup
	 *
	 * @param CakeEvent $event
	 */
	public function startup(CakeEvent $event)
	{
		if(!BcUtil::isAdminSystem()) return;
		if(!$this->isAction(['BlogPosts.AdminAdd', 'BlogPosts.AdminEdit'])) return;
		if(!CakePlugin::loaded('CuApprover')) return;
		$controller = $event->subject();
		if($controller->name === 'CakeError') return;
		$postId = null;
		if(!empty($controller->request->params['pass'][1])) $postId = $controller->request->params['pass'][1];
		$this->setupApprover($controller, $postId);
	}

	/**
	 * 公開承認プラグインの設定を行う
	 *
	 * @param int $blogContentId
	 * @param array $post
	 */
	public function setupApprover($controller, $postId)
	{
		$blogContentId = $controller->blogContent['BlogContent']['id'];
		$configModel = ClassRegistry::init('CuCustomField.CuCustomFieldConfig');
		$definitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
		$valueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$applicationModel = ClassRegistry::init('CuApprover.CuApproverApplication');

		$valueModel->definitions = $valueModel->getFieldDefinition($blogContentId);
		if(!empty($controller->request->data['CuCustomFieldValue'])) {
			$post = $controller->request->data['CuCustomFieldValue'];
		} else {
			$post = [];
			if($postId) {
				$application = $applicationModel->find('first', ['conditions' => ['CuApproverApplication.entity_id' => $postId, 'type' => 'BlogPost']]);
				if($application) {
					// 承認者が直接本稿を作成した場合草稿が存在しない
					if($application['CuApproverApplication']['draft']) {
						$draft = BcUtil::unserialize($application['CuApproverApplication']['draft']);
						if(!empty($draft['CuCustomFieldValue'])) {
							$post = $valueModel->convertToArrayData($draft['CuCustomFieldValue']);
						}
					}
				}
			}
		}

		$config = $configModel->find('first', ['conditions' => ['content_id' => $blogContentId]]);
		$definitions = $definitionModel->find('all', [
			'conditions' => [
				'CuCustomFieldDefinition.config_id' => $config['CuCustomFieldConfig']['id'],
				'CuCustomFieldDefinition.parent_id' => null,
				'status' => true
			],
			'order' => 'CuCustomFieldDefinition.lft ASC',
			'recursive' => -1,
		]);
		$flattening = $valueModel->convertToFlatteningData($post);
		$fields = [];
		foreach($definitions as $definition) {
			$definition = $definition['CuCustomFieldDefinition'];
			if($definition['field_type'] !== 'loop') {
				$fields[] = $definition['field_name'];
			} elseif(!empty($post[$definition['field_name']])) {
				if(!$flattening) continue;
				foreach($flattening as $fieldName => $value) {
					if(preg_match('/' . $definition['field_name'] . '_[0-9]+_/', $fieldName)) {
						$fields[] = $fieldName;
					}
				}
			}
		}
		Configure::write('CuApprover.targets.BlogPost.draftFields.CuCustomFieldValue.fields', $fields);
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
				$fieldConfigField = $this->CuCustomFieldConfigModel->CuCustomFieldDefinition->find('all', [
					'conditions' => [
						'CuCustomFieldDefinition.config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id']
					],
					'order' => 'CuCustomFieldDefinition.lft ASC',
					'recursive' => -1,
				]);
				$defaultFieldValue[$this->cuCustomFieldConfigs['CuCustomFieldConfig']['content_id']] = Hash::combine($fieldConfigField, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition');
				$this->CuCustomFieldValueModel->publicFieldConfigData = $defaultFieldValue;
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
				$definitions = $this->CuCustomFieldDefinitionModel->find('all', [
					'conditions' => [
						'CuCustomFieldDefinition.config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id'],
						'CuCustomFieldDefinition.parent_id' => null
					],
					'order' => 'CuCustomFieldDefinition.lft ASC',
					'recursive' => -1,
				]);
				if($definitions) {
					foreach($definitions as $key => $definition) {
						if($definition['CuCustomFieldDefinition']['field_type'] === 'loop') {
							$children = $this->CuCustomFieldDefinitionModel->children($definition['CuCustomFieldDefinition']['id']);
							foreach($children as $i => $child) {
								if(!$child['CuCustomFieldDefinition']['status']) {
									unset($children[$i]);
								}
							}
							$definitions[$key]['CuCustomFieldDefinition']['children'] = $children;
						}
					}
				}
				$Controller->set('definitions', $definitions);

				// フィールド設定から初期値を生成
				$defaultFieldValue = Hash::combine($definitions, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition.default_value');
				$this->CuCustomFieldValueModel->keyValueDefaults = ['CuCustomFieldValue' => $defaultFieldValue];
				$defalut = $this->CuCustomFieldValueModel->defaultValues();
				// 初期値と存在値をマージする
				if (!empty($Controller->request->data['CuCustomFieldValue'])) {
					$Controller->request->data['CuCustomFieldValue'] = Hash::merge($defalut['CuCustomFieldValue'], $Controller->request->data['CuCustomFieldValue']);
				} else {
					$Controller->request->data['CuCustomFieldValue'] = $defalut['CuCustomFieldValue'];
				}

				// バリデーションエラーが発生した際、ファイルフィールドが配列状態になってしまうため
				// 変換されたデータを格納しなおしファイルを表示する。
				if($this->CuCustomFieldValueModel->validationErrors) {
					$Controller->request->data['CuCustomFieldValue'] = $this->CuCustomFieldValueModel->data['CuCustomFieldValue'];
				}

			}
		}

		// ブログ記事追加画面で実行
		if ($Controller->request->params['action'] == 'admin_add') {
			$Controller->request->data['CuCustomFieldConfig'] = $this->cuCustomFieldConfigs['CuCustomFieldConfig'];

			if ($this->cuCustomFieldConfigs['CuCustomFieldConfig']['status']) {
				$definitions = $this->CuCustomFieldDefinitionModel->find('all', [
					'conditions' => [
						'CuCustomFieldDefinition.config_id' => $this->cuCustomFieldConfigs['CuCustomFieldConfig']['id'],
						'CuCustomFieldDefinition.parent_id' => null
					],
					'order' => 'CuCustomFieldDefinition.lft ASC',
					'recursive' => -1,
				]);
				if($definitions) {
					foreach($definitions as $key => $definition) {
						if($definition['CuCustomFieldDefinition']['field_type'] === 'loop') {
							$children = $this->CuCustomFieldDefinitionModel->children($definition['CuCustomFieldDefinition']['id']);
							foreach($children as $i => $child) {
								if(!$child['CuCustomFieldDefinition']['status']) {
									unset($children[$i]);
								}
							}
							$definitions[$key]['CuCustomFieldDefinition']['children'] = $children;
						}
					}
				}
				$Controller->set('definitions', $definitions);

				// フィールド設定から初期値を生成
				if (empty($Controller->request->data['CuCustomFieldValue'])) {
					$defaultFieldValue = Hash::combine($definitions, '{n}.CuCustomFieldDefinition.field_name', '{n}.CuCustomFieldDefinition.default_value');
					$this->CuCustomFieldValueModel->keyValueDefaults = ['CuCustomFieldValue' => $defaultFieldValue];
					$defalut = $this->CuCustomFieldValueModel->defaultValues();
					$Controller->request->data['CuCustomFieldValue'] = $defalut['CuCustomFieldValue'];
				}
			}
		}

		if(!empty($Controller->request->data['CuCustomFieldValue'])) {
			foreach($Controller->request->data['CuCustomFieldValue'] as $key => $value) {
				if (isset($value['__loop-src__'])) {
					unset($Controller->request->data['CuCustomFieldValue'][$key]['__loop-src__']);
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
		$this->cuCustomFieldConfigs = $this->CuCustomFieldConfigModel->find('first', [
			'conditions' => ['CuCustomFieldConfig.content_id' => $Controller->BlogContent->id],
			'recurseve' => -1,
		]);
		$this->CuCustomFieldValueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$this->CuCustomFieldDefinitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
	}

}
