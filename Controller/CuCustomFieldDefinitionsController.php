<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Controller
 * @license          MIT LICENSE
 */
App::uses('CuCustomFieldApp', 'CuCustomField.Controller');

/**
 * Class CuCustomFieldDefinitionsController
 * @property CuCustomFieldDefinition $CuCustomFieldDefinition
 */
class CuCustomFieldDefinitionsController extends CuCustomFieldAppController
{

	/**
	 * Model
	 *
	 * @var array
	 */
	public $uses = ['CuCustomField.CuCustomFieldDefinition'];

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = [
		['name' => 'プラグイン管理', 'url' => ['plugin' => '', 'controller' => 'plugins', 'action' => 'index']],
		['name' => 'カスタムフィールド定義管理', 'url' => ['plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'index']],
	];

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = 'フィールド定義';

	/**
	 * beforeFilter
	 *
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		// カスタムフィールド定義からコンテンツIDを取得してセット
		if (!empty($this->request->params['pass'][0])) {
			$this->CuCustomFieldDefinition->setup($this->request->params['pass'][0]);
			$configData = $this->CuCustomFieldDefinition->CuCustomFieldConfig->find('first', [
				'conditions' => ['CuCustomFieldConfig.id' => $this->request->params['pass'][0]],
				'recursive' => -1,
			]);
			$this->set('contentId', $configData['CuCustomFieldConfig']['content_id']);
		}
	}


	/**
	 * [ADMIN] フィールド定義一覧
	 *
	 * @param int $configId
	 */
	public function admin_index($configId)
	{

		if (!$configId) {
			$this->BcMessage->setError('無効な処理です。');
			$this->notFound();
		}

		$this->pageTitle = $this->adminTitle . '一覧';
		$this->help = 'cu_custom_field_metas_index';

		$this->setViewConditions('CuCustomFieldDefinition', ['default' => [
			'named' => [
				'num' => $this->siteConfigs['admin_list_num']
			]]]);

		$conditions = $this->_createAdminIndexConditions($configId, $this->request->data);

		$list = $this->CuCustomFieldDefinition->generateTreeList($conditions);
		$definitions = [];
		foreach($list as $key => $value) {
			$definition = $this->CuCustomFieldDefinition->find('first', ['conditions' => ['CuCustomFieldDefinition.id' => $key]]);
			if (preg_match("/^([_]+)/i", $value, $matches)) {
				$prefix = str_replace('_', '   ', $matches[1]);
				$definition['CuCustomFieldDefinition']['name'] = $prefix . '└&nbsp;' . $definition['CuCustomFieldDefinition']['name'];
			}
			$definitions[] = $definition;
		}
		$this->set('datas', $definitions);
		$this->set('configId', $configId);
		$this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_edit($configId = null, $id = null)
	{
		$this->pageTitle = $this->adminTitle . '編集';
		$this->help = 'cu_custom_field_definitions';
		$deletable = true;

		if (!$configId || !$id) {
			$this->BcMessage->setError('無効な処理です。');
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->CuCustomFieldDefinition->find('first', ['conditions' => ['CuCustomFieldDefinition.id' => $id]]);
		} else {
			$this->CuCustomFieldDefinition->set($this->request->data);
			if ($this->CuCustomFieldDefinition->save()) {

				if ($this->request->data['CuCustomFieldDefinition']['field_type'] !== 'loop') {
					$children = $this->CuCustomFieldDefinition->children($this->request->data['CuCustomFieldDefinition']['id']);
					if ($children) {
						foreach($children as $child) {
							$child['CuCustomFieldDefinition']['parent_id'] = null;
							$this->CuCustomFieldDefinition->set($child);
							$this->CuCustomFieldDefinition->save();
						}
					}
				}

				$message = 'フィールド定義「' . $this->request->data['CuCustomFieldDefinition']['name'] . '」を更新しました。';
				$this->BcMessage->setSuccess($message);
				$this->redirect(['action' => 'index', $configId]);
			} else {
				$this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
			}
		}

		$fieldNameList = $this->CuCustomFieldDefinition->getControlSource('field_name');
		$this->set('loops', $this->CuCustomFieldDefinition->getLoopList($configId));
		$this->set(compact('fieldNameList', 'configId', 'deletable'));
		$this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
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
		$this->help = 'cu_custom_field_definitions';
		$deletable = false;

		if (!$configId) {
			$this->BcMessage->setError('無効な処理です。');
			$this->redirect(['controller' => 'cu_custom_field_configs', 'action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->request->data = ['CuCustomFieldDefinition' => ['config_id' => $configId]];
		} else {
			$this->CuCustomFieldDefinition->create($this->request->data);
			if ($this->CuCustomFieldDefinition->save()) {
				$message = 'フィールド定義「' . $this->request->data['CuCustomFieldDefinition']['name'] . '」の追加が完了しました。';
				$this->BcMessage->setSuccess($message);
				$this->redirect(['action' => 'index', $configId]);
			} else {
				$this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
			}
		}

		$fieldNameList = $this->CuCustomFieldDefinition->getControlSource('field_name');
		$this->set('loops', $this->CuCustomFieldDefinition->getLoopList($configId));
		$this->set(compact('fieldNameList', 'configId', 'deletable'));
		$this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $configId
	 * @param int $foreignId
	 */
	public function admin_delete($configId = null, $id = null)
	{
		if (!$configId || !$id) {
			$this->BcMessage->setError('無効な処理です。');
			$this->redirect(['action' => 'index']);
		}

		// 削除前にメッセージ用にカスタムフィールドを取得する
		$data = $this->CuCustomFieldDefinition->read($id);

		if ($this->CuCustomFieldDefinition->delete($id)) {
			$message = $this->name . '「' . $data['CuCustomFieldDefinition']['name'] . '」を削除しました。';
			$this->BcMessage->setSuccess($message);
			$this->redirect(['action' => 'index', $configId]);
		} else {
			$this->BcMessage->setError('データベース処理中にエラーが発生しました。');
		}
		$this->redirect(['action' => 'index', $configId]);
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_ajax_delete($configId = null, $id = null)
	{
		if (!$configId || !$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		// 削除実行
		if ($this->CuCustomFieldDefinition->delete($id)) {
			clearViewCache();
			exit(true);
		}
		exit();
	}


	/**
	 * [ADMIN] 無効状態にする（AJAX）
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_ajax_unpublish($id = null)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->{$this->modelClass}->validationErrors);
		}
		exit();
	}

	/**
	 * [ADMIN] 有効状態にする（AJAX）
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_ajax_publish($id = null)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->{$this->modelClass}->validationErrors);
		}
		exit();
	}


	/**
	 * ステータスを変更する
	 *
	 * @param int $configId
	 * @param int $id
	 * @param boolean $status
	 * @return boolean
	 */
	protected function _changeStatus($id = null, $status = false)
	{
		$data = $this->CuCustomFieldDefinition->find('first', [
			'conditions' => ['id' => $id],
			'recursive' => -1
		]);

		$data['CuCustomFieldDefinition']['status'] = $status;
		$this->CuCustomFieldDefinition->set($data);
		if ($this->CuCustomFieldDefinition->save()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 一覧用の検索条件を生成する
	 *
	 * @param array $data
	 * @return array $conditions
	 */
	protected function _createAdminIndexConditions($configId, $data)
	{
		$conditions = ['config_id' => $configId];
		if ($conditions) {
			return $conditions;
		} else {
			return [];
		}
	}


	/**
	 * [ADMIN] 並び順を上げる
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_move_up($configId, $id)
	{
		if (!$id || !$configId) {
			$this->BcMessage->setError('無効な処理です。');
			$this->redirect(['action' => 'index']);
		}

		if ($this->CuCustomFieldDefinition->up($id, $configId)) {
			$this->BcMessage->setSuccess('フィールド定義の並び順を繰り上げました。');
		} else {
			$this->BcMessage->setError('データベース処理中にエラーが発生しました。');
		}
		$this->redirect(['action' => 'index', $configId]);
	}

	/**
	 * [ADMIN] 並び順を下げる
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_move_down($configId = null, $id = null, $toBottom = '')
	{
		if (!$id || !$configId) {
			$this->BcMessage->setError('無効な処理です。');
			$this->redirect(['action' => 'index']);
		}

		if ($this->CuCustomFieldDefinition->down($id, $configId)) {
			$this->BcMessage->setSuccess('フィールド定義の並び順を繰り下げました。');
		} else {
			$this->BcMessage->setError('データベース処理中にエラーが発生しました。');
		}
		$this->redirect(['action' => 'index', $configId]);
	}

	/**
	 * [ADMIN][AJAX] 重複値をチェックする
	 *   ・foreign_id が異なるものは重複とみなさない
	 *
	 */
	public function admin_ajax_check_duplicate()
	{
		$this->autoRender = false;
		Configure::write('debug', 0);
		$result = true;

		if (!$this->request->is('ajax')) {
			$message = '許可されていないアクセスです。';
			$this->BcMessage->setError($message);
			$this->redirect(['controller' => 'cu_custom_field_configs', 'action' => 'index']);
		}

		if ($this->request->data) {
			$conditions = [];
			if (array_key_exists('name', $this->request->data[$this->modelClass])) {
				$conditions = [
					$this->modelClass . '.' . 'name' => $this->request->data[$this->modelClass]['name']
				];
			}
			if (array_key_exists('label_name', $this->request->data[$this->modelClass])) {
				$conditions = [
					$this->modelClass . '.' . 'label_name' => $this->request->data[$this->modelClass]['label_name']
				];
			}
			if (array_key_exists('field_name', $this->request->data[$this->modelClass])) {
				$conditions = [
					$this->modelClass . '.' . 'field_name' => $this->request->data[$this->modelClass]['field_name']
				];
			}

			$conditions = Hash::merge($conditions, [
				$this->modelClass . '.' . 'config_id' => $this->request->data[$this->modelClass]['config_id'],
				'NOT' => [$this->modelClass . '.id' => $this->request->data[$this->modelClass]['id']],
			]);

			$ret = $this->{$this->modelClass}->find('first', [
				'conditions' => $conditions,
				'recursive' => -1,
			]);
			if ($ret) {
				$result = false;
			} else {
				$result = true;
			}
		}
		echo $result;
	}

}
