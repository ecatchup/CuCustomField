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

class CuCustomFieldValuesController extends CuCustomFieldAppController
{

	/**
	 * Model
	 *
	 * @var array
	 */
	public $uses = array('CuCustomField.CuCustomFieldValue', 'CuCustomField.CuCustomFieldConfig');

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'プチ・カスタムフィールド管理', 'url' => array('plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_values', 'action' => 'index'))
	);

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = 'プチ・カスタムフィールド';

	/**
	 * beforeFilter
	 *
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	/**
	 * [ADMIN] 一覧
	 *
	 */
	public function admin_index()
	{
		$this->pageTitle = $this->adminTitle . '一覧';
		$this->search	 = 'cu_custom_field_values_index';
		$this->help		 = 'cu_custom_field_values_index';

		parent::admin_index();
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 */
	public function admin_edit($id = null)
	{
		$this->pageTitle = $this->adminTitle . '編集';

		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->{$this->modelClass}->id					 = $id;
			$this->request->data							 = $this->{$this->modelClass}->read();
			$configData										 = $this->CuCustomFieldConfig->find('first', array(
				'conditions' => array(
					'CuCustomFieldConfig.content_id' => $this->request->data[$this->modelClass]['content_id']
			)));
			$this->request->data['CuCustomFieldConfig']	 = $configData['CuCustomFieldConfig'];
		} else {
			$configData										 = $this->CuCustomFieldConfig->find('first', array(
				'conditions' => array(
					'CuCustomFieldConfig.content_id' => $this->request->data[$this->modelClass]['content_id']
			)));
			$this->request->data['CuCustomFieldConfig']	 = $configData['CuCustomFieldConfig'];

			if ($this->{$this->modelClass}->save($this->request->data)) {
				$this->setMessage($this->name . ' ID:' . $id . ' を更新しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		}

		$this->set('blogContentDatas', array('0' => '指定しない') + $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $id
	 */
	public function admin_delete($id = null)
	{
		parent::admin_delete($id);
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
		$name			 = '';
		$blogContentId	 = '';

		if (isset($data['CuCustomFieldValue']['name'])) {
			$name = $data['CuCustomFieldValue']['name'];
		}
		if (isset($data['CuCustomFieldValue']['content_id'])) {
			$blogContentId = $data['CuCustomFieldValue']['content_id'];
		}
		if (isset($data['CuCustomFieldValue']['status']) && $data['CuCustomFieldValue']['status'] === '') {
			unset($data['CuCustomFieldValue']['status']);
		}

		unset($data['_Token']);
		unset($data['CuCustomFieldValue']['name']);
		unset($data['CuCustomFieldValue']['content_id']);

		// 条件指定のないフィールドを解除
		foreach ($data['CuCustomFieldValue'] as $key => $value) {
			if ($value === '') {
				unset($data['CuCustomFieldValue'][$key]);
			}
		}

		if ($data['CuCustomFieldValue']) {
			$conditions = $this->postConditions($data);
		}
		/*
		  if($name) {
		  $conditions[] = array(
		  'PetitCustomField.name LIKE' => '%'.$name.'%'
		  );
		  } */
		// １つの入力指定から複数フィールド検索指定
		if ($name) {
			$conditions['or'][] = array(
				'PetitCustomField.name LIKE' => '%' . $name . '%'
			);
		}
		if ($blogContentId) {
			$conditions['and'] = array(
				'PetitCustomField.content_id' => $blogContentId
			);
		}

		if ($conditions) {
			return $conditions;
		} else {
			return array();
		}
	}

}
