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

class PetitCustomFieldConfigMetasController extends CuCustomFieldAppController
{

	/**
	 * Model
	 *
	 * @var array
	 */
	public $uses = array('CuCustomField.PetitCustomFieldConfigMeta', 'CuCustomField.CuCustomFieldDefinition');

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'カスタムフィールド定義管理', 'url' => array('plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'index')),
	);

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
	}


	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 */
	public function admin_edit($id = null)
	{
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->{$this->modelClass}->id	 = $id;
			$this->request->data			 = $this->{$this->modelClass}->read();
		} else {
			$configData = $this->PetitCustomFieldConfigMeta->CuCustomFieldConfig->find('first', array(
				'conditions' => array(
					'CuCustomFieldConfig.content_id' => $this->request->data['CuCustomFieldConfig']['content_id'],
				),
				'recursive'	 => -1,
			));

			// 次の位置のデータ（最初と最後以外の場合）
			$nextData = $this->PetitCustomFieldConfigMeta->lowerItem($id);

			// petit_custom_field_config_id
			$newFieldConfigId														 = $configData['CuCustomFieldConfig']['id'];
			$this->request->data[$this->modelClass]['petit_custom_field_config_id']	 = $newFieldConfigId;
			$max																	 = $this->{$this->modelClass}->getMax('position', array(
				'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $newFieldConfigId
			));
			$max																	 = $max + 1;
			$this->request->data[$this->modelClass]['position']						 = $max;

			$this->{$this->modelClass}->set($this->request->data);
			if ($this->{$this->modelClass}->save($this->request->data)) {
				clearViewCache();
				clearDataCache();
				// 最後のデータの場合は何もしなくてOK
				if ($nextData) {
					if ($nextData['PetitCustomFieldConfigMeta']['position'] == 2) {
						$this->PetitCustomFieldConfigMeta->unbindModel(array('belongsTo' => array('CuCustomFieldConfig')));
						$this->PetitCustomFieldConfigMeta->updateAll(
								array('PetitCustomFieldConfigMeta.position' => 'PetitCustomFieldConfigMeta.position - 1'), array('PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $nextData['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'])
						);
						// 以下、どれもダメだった。。。
						// $this->PetitCustomFieldConfigMeta->moveToBottom($nextData['PetitCustomFieldConfigMeta']['id']);
						// $this->PetitCustomFieldConfigMeta->moveToTop($nextData['PetitCustomFieldConfigMeta']['id']);
						// $this->PetitCustomFieldConfigMeta->insertAt(1, $nextData['PetitCustomFieldConfigMeta']['id']);
						// $this->PetitCustomFieldConfigMeta->moveUp($nextData['PetitCustomFieldConfigMeta']['id']);
					} else {
						$newPosition = $nextData['PetitCustomFieldConfigMeta']['position'] - 1;
						$this->PetitCustomFieldConfigMeta->insertAt($newPosition, $nextData['PetitCustomFieldConfigMeta']['id']);
					}
				}

				$this->setMessage($this->name . ' ID:' . $id . '　を更新しました。', false, true);
				$this->redirect(array('action' => 'index', $configData['CuCustomFieldConfig']['id']));
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		}

		$configData['CuCustomFieldConfig'] = $this->request->data['CuCustomFieldConfig'];
		$this->set('configId', $configData['CuCustomFieldConfig']['id']);
		$this->set('blogContentDatas', $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $configId
	 * @param int $id
	 */
	public function admin_delete($configId = null, $id = null)
	{
		if (!$configId || !$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		$data = $this->PetitCustomFieldConfigMeta->find('first', array(
			'conditions' => array('PetitCustomFieldConfigMeta.id' => $id),
			'recursive'	 => -1,
		));
		// $data['PetitCustomFieldConfigMeta']['field_foreign_id']

		if ($this->PetitCustomFieldConfigMeta->delete($id)) {

			// メタ情報削除時、そのメタ情報が持つカスタムフィールド定義を削除する
			$this->CuCustomFieldDefinition->Behaviors->KeyValue->KeyValue = $this->CuCustomFieldDefinition;
			if ($data) {
				//resetSection(Model $Model, $foreignKey = null, $section = null, $key = null)
				if (!$this->CuCustomFieldDefinition->resetSection($data['PetitCustomFieldConfigMeta']['field_foreign_id'], 'CuCustomFieldDefinition')) {
					$this->log(sprintf('field_foreign_id：%s のカスタムフィールドの削除に失敗', $data['PetitCustomFieldConfigMeta']['field_foreign_id']));
				}
			}

			$message = $this->name . ' ID:' . $id . ' を削除しました。';
			$this->setMessage($message, false, true);
			$this->redirect(array('action' => 'index', $configId));
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(array('action' => 'index', $configId));
	}

	/**
	 * [ADMIN] ListBehavior利用中のデータ並び順を割り振る
	 *
	 */
	public function admin_reposition()
	{
		if ($this->PetitCustomFieldConfigMeta->Behaviors->enabled('List')) {
			if ($this->PetitCustomFieldConfigMeta->fixListOrder($this->PetitCustomFieldConfigMeta)) {
				$message = $this->modelClass . 'データに並び順（position）を割り振りました。';
				$this->setMessage($message, false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('データベース処理中にエラーが発生しました。', true);
			}
		} else {
			$this->setMessage('ListBehaviorが無効のモデルです。', true);
		}
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * 一覧用の検索条件を生成する
	 *
	 * @param array $data
	 * @return array $conditions
	 */
	protected function _createAdminIndexConditions($data)
	{
		$conditions	 = array();
		$contentId	 = '';

		if (isset($data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'])) {
			$contentId = $data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id'];
		}

		unset($data['_Token']);
		unset($data['PetitCustomFieldConfigMeta']['petit_custom_field_config_id']);

		// 条件指定のないフィールドを解除
		if (!empty($data['PetitCustomFieldConfigMeta'])) {
			foreach ($data['PetitCustomFieldConfigMeta'] as $key => $value) {
				if ($value === '') {
					unset($data['PetitCustomFieldConfigMeta'][$key]);
				}
			}
			if ($data['PetitCustomFieldConfigMeta']) {
				$conditions = $this->postConditions($data);
			}
		}

		if ($contentId) {
			$conditions = array(
				'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $contentId
			);
		}

		if ($conditions) {
			return $conditions;
		} else {
			return array();
		}
	}

}
