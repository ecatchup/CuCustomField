<?php

/**
 * [Controller] CuCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
App::uses('CuCustomFieldApp', 'CuCustomField.Controller');

class CuCustomFieldDefinitionsController extends CuCustomFieldAppController
{

	/**
	 * ControllerName
	 *
	 * @var string
	 */
	public $name = 'CuCustomFieldDefinitions';

	/**
	 * Model
	 *
	 * @var array
	 */
	public $uses = array('CuCustomField.CuCustomFieldDefinition');

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'プチ・カスタムフィールド設定管理', 'url' => array('plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'index')),
	);

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = 'フィールド設定';

	/**
	 * beforeFilter
	 *
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		// カスタムフィールド設定からコンテンツIDを取得してセット
		if (!empty($this->request->params['pass'][0])) {
			$configData = $this->CuCustomFieldDefinition->CuCustomFieldConfig->find('first', array(
				'conditions' => array('CuCustomFieldConfig.id' => $this->request->params['pass'][0]),
				'recursive'	 => -1,
			));
			$this->set('contentId', $configData['CuCustomFieldConfig']['content_id']);
		}
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $configId
	 * @param int $foreignId
	 */
	public function admin_edit($configId = null, $foreignId = null)
	{
		$this->pageTitle = $this->adminTitle . '編集';
		$this->help		 = 'cu_custom_field_definitions';
		$deletable		 = true;

		if (!$configId || !$foreignId) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		$this->crumbs[] = array('name' => 'フィールド設定管理', 'url' => array('plugin' => 'cu_custom_field', 'controller' => 'petit_custom_field_config_metas', 'action' => 'index', $configId));

		if (empty($this->request->data)) {
			// $data = $this->CuCustomFieldValueModel->getSection($Model->id, $this->CuCustomFieldValueModel->name);
			$data = $this->{$this->modelClass}->getSection($foreignId, $this->modelClass);
			if ($data) {
				$this->request->data = array($this->modelClass => $data);
			}
		} else {
			// バリデーション重複チェックのため、foreign_id をモデルのプロパティに持たせる
			$this->CuCustomFieldDefinition->foreignId = $foreignId;
			if ($this->CuCustomFieldDefinition->validateSection($this->request->data, 'CuCustomFieldDefinition')) {
				if ($this->CuCustomFieldDefinition->saveSection($foreignId, $this->request->data, 'CuCustomFieldDefinition')) {
					$message = $this->name . '「' . $this->request->data['CuCustomFieldDefinition']['name'] . '」を更新しました。';
					$this->setMessage($message, false, true);
					$this->redirect(array('controller' => 'petit_custom_field_config_metas', 'action' => 'index', $configId));
				} else {
					$this->setMessage('入力エラーです。内容を修正して下さい。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		}

		$fieldNameList = $this->CuCustomFieldDefinition->getControlSource('field_name');
		$this->set(compact('fieldNameList', 'configId', 'foreignId', 'deletable'));
		$this->set('blogContentDatas', array('0' => '指定しない') + $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $configId
	 */
	public function admin_add($configId = null)
	{
		$this->pageTitle = $this->adminTitle . '追加';
		$this->help		 = 'cu_custom_field_definitions';
		$deletable		 = false;

		$this->crumbs[]	 = array('name' => 'カスタムフィールド設定管理', 'url' => array('plugin' => 'cu_custom_field', 'controller' => 'petit_custom_field_config_metas', 'action' => 'index', $configId));
		$foreignId		 = $this->CuCustomFieldDefinition->PetitCustomFieldConfigMeta->getMax('field_foreign_id') + 1;

		if (!$configId) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('controller' => 'cu_custom_field_configs', 'action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->CuCustomFieldDefinition->defaultValues();
		} else {
			if ($this->CuCustomFieldDefinition->validateSection($this->request->data, 'CuCustomFieldDefinition')) {
				if ($this->CuCustomFieldDefinition->saveSection($foreignId, $this->request->data, 'CuCustomFieldDefinition')) {

					// リンクテーブルにデータを追加する
					$saveData = array(
						'PetitCustomFieldConfigMeta' => array(
							'petit_custom_field_config_id'	 => $configId,
							'field_foreign_id'				 => $foreignId,
						),
					);
					// load しないと順番が振られない。スコープが効かない。
					$this->CuCustomFieldDefinition->PetitCustomFieldConfigMeta->Behaviors->load(
							'CuCustomField.List', array('scope' => 'petit_custom_field_config_id')
					);
					$this->CuCustomFieldDefinition->PetitCustomFieldConfigMeta->create($saveData);
					$this->CuCustomFieldDefinition->PetitCustomFieldConfigMeta->save($saveData);

					$message = $this->name . '「' . $this->request->data['CuCustomFieldDefinition']['name'] . '」の追加が完了しました。';
					$this->setMessage($message, false, true);
					$this->redirect(array('controller' => 'petit_custom_field_config_metas', 'action' => 'index', $configId));
				} else {
					$this->setMessage('入力エラーです。内容を修正して下さい。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		}

		$fieldNameList = $this->CuCustomFieldDefinition->getControlSource('field_name');
		$this->set(compact('fieldNameList', 'configId', 'foreignId', 'deletable'));
		$this->set('blogContentDatas', array('0' => '指定しない') + $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $configId
	 * @param int $foreignId
	 */
	public function admin_delete($configId = null, $foreignId = null)
	{
		if (!$configId || !$foreignId) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		// 削除前にメッセージ用にカスタムフィールドを取得する
		$data = $this->CuCustomFieldDefinition->getSection($foreignId, 'CuCustomFieldDefinition');

		if ($this->CuCustomFieldDefinition->resetSection($foreignId)) {
			$message = $this->name . '「' . $data['CuCustomFieldDefinition']['name'] . '」を削除しました。';
			$this->setMessage($message, false, true);
			$this->redirect(array('action' => 'index', $configId));
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(array('action' => 'index', $configId));
	}

	/**
	 * 一覧用の検索条件を生成する
	 *
	 * @param array $data
	 * @return array $conditions
	 */
	protected function _createAdminIndexConditions($data)
	{
		$conditions		 = array();
		$blogContentId	 = '';

		if (isset($data['CuCustomFieldDefinition']['content_id'])) {
			$blogContentId = $data['CuCustomFieldDefinition']['content_id'];
		}

		unset($data['_Token']);
		unset($data['CuCustomFieldDefinition']['content_id']);

		// 条件指定のないフィールドを解除
		if (!empty($data['CuCustomFieldDefinition'])) {
			foreach ($data['CuCustomFieldDefinition'] as $key => $value) {
				if ($value === '') {
					unset($data['CuCustomFieldDefinition'][$key]);
				}
			}
			if ($data['CuCustomFieldDefinition']) {
				$conditions = $this->postConditions($data);
			}
		}

		if ($blogContentId) {
			$conditions = array(
				'CuCustomFieldDefinition.content_id' => $blogContentId
			);
		}

		if ($conditions) {
			return $conditions;
		} else {
			return array();
		}
	}

	/**
	 * [ADMIN][AJAX] 重複値をチェックする
	 *   ・foreign_id が異なるものは重複とみなさない
	 *
	 */
	public function admin_ajax_check_duplicate()
	{
		Configure::write('debug', 0);
		$this->layout	 = null;
		$result			 = true;

		if (!$this->RequestHandler->isAjax()) {
			$message = '許可されていないアクセスです。';
			$this->setMessage($message, true);
			$this->redirect(array('controller' => 'cu_custom_field_configs', 'action' => 'index'));
		}

		if ($this->request->data) {
			$conditions = array();
			if (array_key_exists('name', $this->request->data[$this->modelClass])) {
				$conditions = array(
					$this->modelClass . '.' . 'key'		 => $this->modelClass . '.' . 'name',
					$this->modelClass . '.' . 'value'	 => $this->request->data[$this->modelClass]['name'],
				);
			}
			if (array_key_exists('label_name', $this->request->data[$this->modelClass])) {
				$conditions = array(
					$this->modelClass . '.' . 'key'		 => $this->modelClass . '.' . 'label_name',
					$this->modelClass . '.' . 'value'	 => $this->request->data[$this->modelClass]['label_name'],
				);
			}
			if (array_key_exists('field_name', $this->request->data[$this->modelClass])) {
				$conditions = array(
					$this->modelClass . '.' . 'key'		 => $this->modelClass . '.' . 'field_name',
					$this->modelClass . '.' . 'value'	 => $this->request->data[$this->modelClass]['field_name'],
				);
			}

			if ($this->request->data[$this->modelClass]['foreign_id']) {
				$conditions = Hash::merge($conditions, array(
							'NOT' => array($this->modelClass . '.foreign_id' => $this->request->data[$this->modelClass]['foreign_id']),
				));
			}

			$ret = $this->{$this->modelClass}->find('first', array(
				'conditions' => $conditions,
				'recursive'	 => -1,
			));
			if ($ret) {
				$result = false;
			} else {
				$result = true;
			}
		}

		$this->set('result', $result);
		$this->render('ajax_result');
	}

}
