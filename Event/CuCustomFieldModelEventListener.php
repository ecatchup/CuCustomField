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
 * Class CuCustomFieldModelEventListener
 */
class CuCustomFieldModelEventListener extends BcModelEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = [
		'Blog.BlogPost.beforeFind',
		'Blog.BlogPost.afterFind',
		'Blog.BlogPost.afterSave',
		'Blog.BlogPost.beforeDelete',
		'Blog.BlogPost.afterCopy',
		'Blog.BlogPost.beforeValidate',
		'Blog.BlogContent.beforeFind',
		'Blog.BlogContent.afterDelete',
	];

	/**
	 * カスタムフィールドモデル
	 *
	 * @var CuCustomFieldValue
	 */
	private $CuCustomFieldValueModel = null;

	/**
	 * カスタムフィールド設定モデル
	 *
	 * @var Object
	 */
	private $CuCustomFieldConfigModel = null;

	/**
	 * ブログ記事多重保存の判定
	 *
	 * @var boolean
	 */
	private $throwBlogPost = false;

	/**
	 * ループを平データで取得するモード
	 *
	 * @var bool
	 */
	public $findFlatteningMode = false;

	/**
	 * モデル初期化：CuCustomFieldValueModel, CuCustomFieldConfig
	 *
	 * @return void
	 */
	private function setUpModel()
	{
		if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldValue')) {
			$this->CuCustomFieldValueModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldValue');
		} else {
			$this->CuCustomFieldValueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		}
		$this->CuCustomFieldValueModel->Behaviors->KeyValue->KeyValue = $this->CuCustomFieldValueModel;

		if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldConfig')) {
			$this->CuCustomFieldConfigModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldConfig');
		} else {
			$this->CuCustomFieldConfigModel = ClassRegistry::init('CuCustomField.CuCustomFieldConfig');
		}
	}

	/**
	 * blogBlogPostBeforeFind
	 * 最近の投稿、ブログ記事前後移動を find する際に実行
	 *
	 * @param CakeEvent $event
	 * @return array
	 */
	public function blogBlogPostBeforeFind(CakeEvent $event)
	{
		if (BcUtil::isAdminSystem()) {
			return $event->data;
		}

		$Model = $event->subject();
		// 最近の投稿、ブログ記事前後移動を find する際に実行
		// TODO get_recent_entries に呼ばれる find 判定に、より良い方法があったら改修する
		if (is_array($event->data[0]['fields']) && count($event->data[0]['fields']) === 2) {
			if (($event->data[0]['fields'][0] == 'no') && ($event->data[0]['fields'][1] == 'name')) {
				$event->data[0]['fields'][] = 'id';
				$event->data[0]['fields'][] = 'posts_date';
				$event->data[0]['fields'][] = 'blog_category_id';
				$event->data[0]['fields'][] = 'blog_content_id';
				$event->data[0]['recursive'] = 2;
			}
		}
		$request = Router::getRequest();
		$customSearch = Configure::read('cuCustomFieldConfig.customSearch');
		if(isset($event->data[0]['customSearch']) && $event->data[0]['customSearch'] === false) {
			$customSearch = false;
		}
		if ($request->query && $customSearch) {
			// keyのリストを取得
			$keyArray = $this->getKeyList();
			$searchQuery = [];

			// クエリの判定
			foreach ($request->query as $key => $query) {
				if($key === 'preview') { // プレビューかどうかの判定
					continue;
				}
				// like検索の場合はkey:likeがついている
				$checkKey = preg_replace('/\:like$/', '', $key);
				// クエリがCuCustomFieldで使用されているkeyに含まれていれば$searchQueryの配列に追加
				if(in_array($checkKey, $keyArray)) {
					$searchQuery[$key] = $query;
				}
			}

			// $searchQueryにクエリが追加されていれば、処理を実行
			if (!empty($searchQuery)) {
				$Model->bindModel(['hasMany' => [
					'CuCustomFieldValue' => [
						'className' => 'CuCustomField.CuCustomFieldValue',
						'order' => 'id',
						'foreignKey' => 'relate_id',
					]
				]], false);
				if (!empty($searchQuery)) {
					$event->data[0] = $this->customSearchQuery($event->data[0], $searchQuery);
				}
			}
		}
		return $event->data;
	}

	public function customSearchQuery($query, $get)
	{
		$conditions = [];
		if (!empty($query['conditions'])) {
			$conditions = $query['conditions'];
		}
		foreach($get as $key => $value) {
			if($value && !is_array($value)) {
				// key:likeがついていればlike検索
				if (preg_match('/^([^\:]+?)\:like$/', $key, $matches)) {
					$conditions['or'][] = [
						'key' => 'CuCustomFieldValue.' . $matches[1],
						'value LIKE' => '%' . $value . '%'
					];
				} else {
					$conditions['or'][] = [
						'key' => 'CuCustomFieldValue.' . $key,
						'value' => $value // 完全一致検索
					];
				}
			}
		}
		$query['conditions'] = $query['conditions'] ? array_merge_recursive($query['conditions'], $conditions) : $conditions;
		$query['joins'][] = [
			'table' => 'cu_custom_field_values',
			'alias' => 'CuCustomFieldValue',
			'type' => 'left',
			'conditions' => [
				'BlogPost.id = CuCustomFieldValue.relate_id'
			]
		];
		if ($query['fields']) {
			if (is_array($query['fields'])) {
				$query['fields'][0] = 'DISTINCT ' . $query['fields'][0];
			} else {
				$query['fields'] = 'DISTINCT ' . $query['fields'];
			}
		} else {
			$query['fields'] = 'DISTINCT BlogPost.*';
		}
		return $query;
	}

	/**
	 * CuCustomFieldで使用されているkeyのリストを取得（クエリの判定等で使用）
	 *
	 * @return array
	 */
	private function getKeyList()
	{
		if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldDefinition')) {
			$CuCustomFieldDefinitionModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldDefinition');
		} else {
			$CuCustomFieldDefinitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
		}
		$list = $CuCustomFieldDefinitionModel->find('list', [
			'fields' => ['field_name'],
			'conditions' => [
				'CuCustomFieldDefinition.status' => 1,
			],
			'recursive' => -1,
		]);
		return $list;
	}

	/**
	 * blogBlogPostAfterFind
	 * ブログ記事取得の際にカスタムフィールド情報も併せて取得する
	 *
	 * @param CakeEvent $event
	 * @return void
	 */
	public function blogBlogPostAfterFind(CakeEvent $event)
	{
		$Model = $event->subject();
		$params = Router::getParams();
		$this->setUpModel();

		if (empty($event->data[0][0]['BlogPost']['id'])) {
			return;
		} else {
			$blogPostId = $event->data[0][0]['BlogPost']['id'];
		}

		if (BcUtil::isAdminSystem()) {
			if ($params['plugin'] !== 'blog') {
				return;
			}
			if ($params['controller'] !== 'blog_posts') {
				return;
			}

			switch($params['action']) {
				case 'admin_index':
					break;

				case 'admin_add':
					break;

				case 'admin_edit':
					$data = $this->CuCustomFieldValueModel->getSection($blogPostId, $this->CuCustomFieldValueModel->name);
					if ($data) {
						$event->data[0][0][$this->CuCustomFieldValueModel->name] = $data;
					}
					break;

				case 'admin_preview':
					$data = $this->CuCustomFieldValueModel->getSection($blogPostId, $this->CuCustomFieldValueModel->name);
					if ($data) {
						$event->data[0][0][$this->CuCustomFieldValueModel->name] = $data;
					}
					break;

				case 'admin_ajax_copy':
					break;

				default:
					break;
			}
			if($this->findFlatteningMode) {
				// findFlatteningMode が true に設定されていれば、一回のみ平データで取得
				// 公開承認の草稿モードの保存で本稿を元データに書き戻すために利用
				// CuApproverApplicationBehavior::getPublish() 内の find() にて利用
				if(!empty($event->data[0][0]['CuCustomFieldValue'])) {
					$event->data[0][0]['CuCustomFieldValue'] = $this->CuCustomFieldValueModel->convertToFlatteningData($event->data[0][0]['CuCustomFieldValue']);
				}
				$this->findFlatteningMode = false;
			}
			return;
		}

		// 公開側の処理
		if (empty($event->data[0])) {
			return;
		}

		foreach($event->data[0] as $key => $value) {
			// 記事のカスタムフィールドデータを取得
			if (empty($value['BlogPost'])) {
				continue;
			}

			// KeyValue 側のモデル情報をリセット
			$this->CuCustomFieldValueModel->Behaviors->KeyValue->KeyValue = $this->CuCustomFieldValueModel;

			// カスタムフィールドの設定情報を取得するため、記事のブログコンテンツIDからカスタムフィールド側のコンテンツIDを取得する
			if (!empty($value['BlogPost']['blog_content_id'])) {
				$contentId = $value['BlogPost']['blog_content_id'];
			} else {
				$contentId = $Model->BlogContent->data['BlogContent']['id'];
			}
			$configData = $this->hasCustomFieldConfigData($contentId);
			if (!$configData) {
				continue;
			}

			if ($configData['CuCustomFieldConfig']['status']) {
				$data = $this->CuCustomFieldValueModel->getSection($value['BlogPost']['id'], $this->CuCustomFieldValueModel->name);
				if ($data) {
					// カスタムフィールドデータを結合
					$event->data[0][$key][$this->CuCustomFieldValueModel->name] = $data;
				}
			}
		}
		if(!empty($contentId)) {
			$this->CuCustomFieldValueModel->setup($contentId);
		}
	}

	/**
	 * ブログコンテンツIDからカスタムフィールド設定情報を取得する
	 *
	 * @param int $contentId
	 * @return array or boolean
	 */
	private function hasCustomFieldConfigData($contentId)
	{
		$data = $this->CuCustomFieldConfigModel->find('first', [
			'conditions' => [
				'CuCustomFieldConfig.content_id' => $contentId,
				'CuCustomFieldConfig.model' => 'BlogContent',
			],
			'recursive' => -1,
		]);
		return $data;
	}

	/**
	 * blogBlogPostBeforeValidate
	 *
	 * @param CakeEvent $event
	 * @return bool
	 */
	public function blogBlogPostBeforeValidate(CakeEvent $event)
	{
		$params = Router::getParams();
		/**
		 * 4系の記事複製動作仕様変更に対応
		 * - これまで複製時のデータに、カスタムフィールドのデータは入って来なかったのが入るようになっているため
		 */
		if (!in_array($params['action'], ['admin_add', 'admin_edit'])) {
			return true;
		}

		$Model = $event->subject();
		// カスタムフィールドの入力データがない場合は、そもそもカスタムフィールドに対する validate 処理を実施しない
		if (!Hash::get($Model->data, 'CuCustomFieldValue')) {
			/**
			 * 4系の記事複製動作仕様変更に対応
			 * - これまで複製時のデータに、カスタムフィールドのデータは入って来なかったのが入るようになっているため
			 * - validateSection 処理まで渡してしまうと、カスタムフィールドに対して、notBlank（入力必須）を設定している場合、
			 *   Cake側の notBlank が走ることで save エラーとなってしまい、記事複製動作が完了できないため
			 */
			return true;
		}

		foreach($Model->data['CuCustomFieldValue'] as $key => $value) {
			if (isset($value['__loop-src__'])) {
				unset($Model->data['CuCustomFieldValue'][$key]['__loop-src__']);
				if(count($value) === 1) {
					$Model->data['CuCustomFieldValue'][$key] = [];
				}
			}
		}

		$this->setUpModel();
		$data = $this->CuCustomFieldConfigModel->find('first', [
			'conditions' => [
				'CuCustomFieldConfig.content_id' => $Model->BlogContent->id,
				'CuCustomFieldConfig.status' => true,
			],
			'recursive' => -1
		]);
		if (!$data) {
			return true;
		}

		$fieldConfigField = $this->CuCustomFieldConfigModel->CuCustomFieldDefinition->find('all', [
			'conditions' => [
				'CuCustomFieldDefinition.config_id' => $data['CuCustomFieldConfig']['id'],
			],
			'order' => 'CuCustomFieldDefinition.lft ASC',
			'recursive' => -1,
		]);
		if (!$fieldConfigField) {
			return true;
		}
		$this->CuCustomFieldValueModel->fieldConfig = $fieldConfigField;
		foreach($fieldConfigField as $key => $fieldConfig) {
			// ステータスが利用しないになっているフィールドは、バリデーション情報として渡さない
			if (!$fieldConfig['CuCustomFieldDefinition']['status']) {
				unset($fieldConfigField[$key]);
			}
		}
		if (!$fieldConfigField) {
			return true;
		}
		$this->CuCustomFieldValueModel->validatingLock = false;
		$this->_setValidate($fieldConfigField);
		if (!$this->CuCustomFieldValueModel->validateValues($Model->data)) {
			$Model->validationErrors += $this->CuCustomFieldValueModel->validationErrors;
			return false;
		}
		$Model->data = $this->CuCustomFieldValueModel->data;
		$this->CuCustomFieldValueModel->validatingLock = false;
		return true;
	}

	/**
	 * バリデーションを設定する
	 *
	 * @param array $data 元データ
	 */
	protected function _setValidate($data = [])
	{
		$validation = [];
		$map = [
			'required' => 'notBlank',
			'max_length' => 'maxLength',
			'validate' => [
				'HANKAKU_CHECK' => 'alphaNumeric',
				'NUMERIC_CHECK' => 'numeric',
				'REGEX_CHECK' => 'regexCheck',
				'NONCHECK_CHECK' => 'multiple'
			]
		];
		foreach($data as $key => $fieldConfig) {
			if(!empty($fieldConfig['CuCustomFieldDefinition']['parent_id'])) {
				continue;
			}
			$fieldName = $fieldConfig['CuCustomFieldDefinition']['field_name'];
			$fieldRule = [];
			foreach($map as $checkType => $rule) {
				if($checkType !== 'validate') {
					if (!empty($fieldConfig['CuCustomFieldDefinition'][$checkType])) {
						$fieldRule = Hash::merge($fieldRule, $this->_getValidationRule($rule, $fieldConfig['CuCustomFieldDefinition']));
					}
				} else {
					foreach($rule as $validateType => $validateRule) {
						if(is_array($fieldConfig['CuCustomFieldDefinition']['validate']) && in_array($validateType, $fieldConfig['CuCustomFieldDefinition']['validate'])) {
							$fieldRule = Hash::merge($fieldRule, $this->_getValidationRule($validateRule, $fieldConfig['CuCustomFieldDefinition']));
						}
					}
				}
			}
			$validation[$fieldName] = $fieldRule;
		}

		// ファイルタイプ制限
		foreach ($data as $key => $fieldConfig) {
			if ($fieldConfig['CuCustomFieldDefinition']['field_type'] !== 'file') {
				continue;
			}
			$fieldName = $fieldConfig['CuCustomFieldDefinition']['field_name'];
			$fieldConfig['CuCustomFieldDefinition']['allow_file_exts']
				= Configure::read('cuCustomField.allow_file_exts');
			if (empty($validation[$fieldName])) {
				$validation[$fieldName] = [];
			}
			$validation[$fieldName] = Hash::merge(
				$validation[$fieldName],
				$this->_getValidationRule('fileExt', $fieldConfig['CuCustomFieldDefinition'])
			);
			$validation[$fieldName] = Hash::merge(
				$validation[$fieldName],
				$this->_getValidationRule('fileCheck', $fieldConfig['CuCustomFieldDefinition'])
			);
		}

		$this->CuCustomFieldValueModel->validate = $validation;
	}

	/**
	 * 設定可能なバリデーションルールを返す
	 *
	 * @param string $rule ルール名
	 * @param array $options
	 * @return array
	 */
	protected function _getValidationRule($rule = '', $definition = [])
	{
		if($rule === 'notBlank' && $definition['field_type'] === 'file') {
			$rule = 'notFileEmpty';
		}
		$validation = [
			'notBlank' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => '必須項目です。',
					'required' => true,
				],
			],
			'notFileEmpty' => [
				'notFileEmpty' => [
					'rule' => ['notFileEmpty'],
					'message' => '必須項目です。',
					'required' => true,
				],
			],
			'multiple' => [
				'notBlank' => [
					'rule' => ['multiple'],
					'message' => '必ず1つ以上選択してください。',
					'required' => true,
				],
			],
			'maxLength' => [
				'maxLength' => [
					'rule' => ['maxLength', $definition['max_length']],
					'message' => $definition['max_length'] . '文字以内で入力してください。',
				],
			],
			'alphaNumeric' => [
				'alphaNumeric' => [
					'rule' => ['alphaNumeric'],
					'message' => '半角英数で入力してください。',
				],
			],
			'numeric' => [
				'numeric' => [
					'rule' => ['numeric'],
					'message' => '数値で入力してください。',
				],
			],
			'regexCheck' => [
				'regexCheck' => [
					'rule' => ['regexCheck'],
					'message' => ($definition['validate_regex_message']) ? $definition['validate_regex_message'] : '入力エラーが発生しました。',
				],
			],
			'fileCheck' => [
				'fileCheck' => [
					'rule' => ['fileCheck', $this->CuCustomFieldValueModel->convertSize(ini_get('upload_max_filesize'))],
					'message' => __d('baser', 'ファイルのアップロードに失敗しました。')
				]
			]
		];
		if (isset($definition['allow_file_exts'])) {
			$validation['fileExt'] = [
				'fileExt' => [
					'rule' => ['fileExt', $definition['allow_file_exts']],
					'message' => '許可されていないファイルです。',
				],
			];
		}
		return $validation[$rule];
	}

	/**
	 * blogBlogPostAfterSave
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogPostAfterSave(CakeEvent $event)
	{
		$Model = $event->subject();

		// カスタムフィールドの入力データがない場合は save 処理を実施しない
		if (!isset($Model->data['CuCustomFieldValue'])) {
			return;
		}

		if (!$this->throwBlogPost) {
			$this->setUpModel();
			$beforeSaveEvent = new CakeEvent('Model.beforeSave', $this->CuCustomFieldValueModel, []);
			list($beforeSaveEvent->break, $beforeSaveEvent->breakOn) = [true, [false, null]];
			$this->CuCustomFieldValueModel->getEventManager()->dispatch($beforeSaveEvent);
			if (!$beforeSaveEvent->result) {
				return false;
			}
			$Model->data['CuCustomFieldValue'] = $this->CuCustomFieldValueModel->data['CuCustomFieldValue'];
			unset($Model->data['CuCustomFieldValue']['relate_id']);
			$this->CuCustomFieldValueModel->savingLock = false;
			if (!$this->CuCustomFieldValueModel->saveSection($Model->id, $Model->data, 'CuCustomFieldValue', null, false)) {
				$this->log(sprintf('ブログ記事ID：%s のカスタムフィールドの保存に失敗', $Model->id));
			} else {
				$this->CuCustomFieldValueModel->set($Model->data);
				$afterSaveEvent = new CakeEvent('Model.afterSave', $this->CuCustomFieldValueModel, [$event->data[0], []]);
				$this->CuCustomFieldValueModel->getEventManager()->dispatch($afterSaveEvent);
			}
			$this->CuCustomFieldValueModel->savingLock = false;
		}
		// ブログ記事コピー保存時、アイキャッチが入っていると処理が2重に行われるため、1周目で処理通過を判定し、
		// 2周目では保存処理に渡らないようにしている
		$this->throwBlogPost = true;
	}

	/**
	 * blogBlogPostAfterDelete
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogPostBeforeDelete(CakeEvent $event)
	{
		$Model = $event->subject();
		// ブログ記事削除時、そのブログ記事が持つカスタムフィールドを削除する
		$this->setUpModel();
		$data = $this->CuCustomFieldValueModel->getSection($Model->id, $this->CuCustomFieldValueModel->name);
		if ($data) {
			$this->CuCustomFieldValueModel->id = $Model->id;
			$this->CuCustomFieldValueModel->getEventManager()->dispatch(new CakeEvent('Model.beforeDelete', $this->CuCustomFieldValueModel, [false]));
			if (!$this->CuCustomFieldValueModel->resetSection($Model->id, $this->CuCustomFieldValueModel->name)) {
				$this->log(sprintf('ブログ記事ID：%s のカスタムフィールドの削除に失敗', $Model->id));
			}
		}
	}

	/**
	 * blogBlogPostAfterCopy
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogPostAfterCopy(CakeEvent $event)
	{
		$data = $this->CuCustomFieldValueModel->getSection($event->data['oldId'], $this->CuCustomFieldValueModel->name);
		if ($data) {

			$config = $this->CuCustomFieldConfigModel->find('first', [
				'conditions' => [
					'CuCustomFieldConfig.content_id' => $event->data['data']['BlogContent']['id'],
					'CuCustomFieldConfig.status' => true,
				],
				'recursive' => -1
			]);

			$fieldConfig = $this->CuCustomFieldConfigModel->CuCustomFieldDefinition->find('all', [
				'conditions' => [
					'CuCustomFieldDefinition.config_id' => $config['CuCustomFieldConfig']['id'],
				],
				'order' => 'CuCustomFieldDefinition.lft ASC',
				'recursive' => -1,
			]);
			if (!$fieldConfig) {
				return true;
			}
			$this->CuCustomFieldValueModel->fieldConfig = $fieldConfig;

			$this->CuCustomFieldValueModel->clear();
			$this->CuCustomFieldValueModel->set($data);
			$beforeSaveEvent = new CakeEvent('Model.beforeSave', $this->CuCustomFieldValueModel, []);
			list($beforeSaveEvent->break, $beforeSaveEvent->breakOn) = [true, [false, null]];
			$this->CuCustomFieldValueModel->getEventManager()->dispatch($beforeSaveEvent);
			if (!$beforeSaveEvent->result) {
				return false;
			}
			$saveData[$this->CuCustomFieldValueModel->name] = $this->CuCustomFieldValueModel->data['CuCustomFieldValue'];
			unset($saveData[$this->CuCustomFieldValueModel->name]['relate_id']);

			$this->CuCustomFieldValueModel->saveSection($event->data['id'], $saveData, 'CuCustomFieldValue', null, false);
		}
	}

	/**
	 * blogBlogContentBeforeFind
	 *
	 * @param CakeEvent $event
	 * @return array
	 */
	public function blogBlogContentBeforeFind(CakeEvent $event)
	{
		$Model = $event->subject();
		// ブログ設定取得の際にカスタム設定情報も併せて取得する
		$association = [
			'CuCustomFieldConfig' => [
				'className' => 'CuCustomField.CuCustomFieldConfig',
				'foreignKey' => 'content_id',
			]
		];
		$Model->bindModel(['hasOne' => $association]);
	}

	/**
	 * blogBlogContentAfterDelete
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogContentAfterDelete(CakeEvent $event)
	{
		$Model = $event->subject();
		// ブログ削除時、そのブログが持つカスタムフィールド設定を削除する
		$this->setUpModel();
		$data = $this->CuCustomFieldConfigModel->find('first', [
			'conditions' => ['CuCustomFieldConfig.content_id' => $Model->id],
			'recursive' => -1
		]);
		if ($data) {
			if (!$this->CuCustomFieldConfigModel->delete($data['CuCustomFieldConfig']['id'])) {
				$this->log('ID:' . $data['CuCustomFieldConfig']['id'] . 'のカスタムフィールド設定の削除に失敗しました。');
			}
		}
	}

	/**
	 * 保存するデータの生成
	 *
	 * @param Object $Model
	 * @param int $contentId
	 * @return array
	 */
	private function generateSaveData($Model, $contentId)
	{
		$params = Router::getParams();
		if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldValue')) {
			$this->CuCustomFieldValueModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldValue');
		} else {
			$this->CuCustomFieldValueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		}

		$data = [];
		$modelId = $oldModelId = null;
		if ($Model->alias == 'BlogPost') {
			$modelId = $contentId;
			if (!empty($params['pass'][1])) {
				$oldModelId = $params['pass'][1];
			}
		}

		if ($contentId) {
			$data = $this->CuCustomFieldValueModel->find('first', ['conditions' => [
				'CuCustomFieldValue.blog_post_id' => $contentId
			]]);
		}

		switch($params['action']) {
			case 'admin_add':
				// 追加時
				if (!empty($Model->data['CuCustomFieldValue'])) {
					$data['CuCustomFieldValue'] = $Model->data['CuCustomFieldValue'];
				}
				$data['CuCustomFieldValue']['blog_post_id'] = $contentId;
				break;

			case 'admin_edit':
				// 編集時
				if (!empty($Model->data['CuCustomFieldValue'])) {
					$data['CuCustomFieldValue'] = $Model->data['CuCustomFieldValue'];
				}
				break;

			case 'admin_ajax_copy':
				// Ajaxコピー処理時に実行
				// ブログコピー保存時にエラーがなければ保存処理を実行
				if (empty($Model->validationErrors)) {
					$_data = [];
					if ($oldModelId) {
						$_data = $this->CuCustomFieldValueModel->find('first', [
							'conditions' => [
								'CuCustomFieldValue.blog_post_id' => $oldModelId
							],
							'recursive' => -1
						]);
					}
					// XXX もしカスタムフィールド設定の初期データ作成を行ってない事を考慮して判定している
					if ($_data) {
						// コピー元データがある時
						$data['CuCustomFieldValue'] = $_data['CuCustomFieldValue'];
						$data['CuCustomFieldValue']['blog_post_id'] = $contentId;
						unset($data['CuCustomFieldValue']['id']);
					} else {
						// コピー元データがない時
						$data['CuCustomFieldValue']['blog_post_id'] = $modelId;
					}
				}
				break;

			default:
				break;
		}

		return $data;
	}

}
