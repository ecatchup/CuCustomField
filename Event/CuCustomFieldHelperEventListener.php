<?php

/**
 * [HelperEventListener] CuCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
class CuCustomFieldHelperEventListener extends BcHelperEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = array(
		'BcFormTable.before',
		'BcFormTable.after',
	);

	/**
	 * 処理対象とするコントローラー
	 *
	 * @var array
	 */
	private $targetController = array('blog_posts');

	/**
	 * 処理対象とするアクション
	 *
	 * @var array
	 */
	private $targetAction = array('admin_edit', 'admin_add');

	/**
	 * カスタムフィールドの表示を判定
	 *
	 * @var boolean
	 */
	private $isDisplay = false;

	/**
	 * BcFormTable Before
	 * - ブログ記事追加・編集画面にカスタムフィールド編集欄を追加する
	 * - 記事編集画面の下部に追加する
	 *
	 * @param CakeEvent $event
	 */
	public function bcFormTableBefore(CakeEvent $event) {
		if (!BcUtil::isAdminSystem()) {
			return false;
		}

		$View = $event->subject();

		if (!in_array($View->request->params['controller'], $this->targetController)) {
			return false;
		}

		if (!in_array($View->request->params['action'], $this->targetAction)) {
			return false;
		}

		$targetId = array('BlogPostForm');
		if (!in_array($event->data['id'], $targetId)) {
			return false;
		}

		if (!isset($View->request->data['CuCustomFieldConfig']) || empty($View->request->data['CuCustomFieldConfig'])) {
			return false;
		}

		if (!$View->request->data['CuCustomFieldConfig']['status']) {
			return false;
		}

		if ($View->request->data['CuCustomFieldConfig']['form_place'] === 'top') {
			// ブログ記事追加画面にカスタムフィールド編集欄を追加する
			$this->isDisplay = true;
			return $View->element('CuCustomField.admin/cu_custom_field_values/cu_custom_field_form');
		}
		return false;
	}

	/**
	 * BcFormTable After
	 * - ブログ記事追加・編集画面にカスタムフィールド編集欄を追加する
	 * - 記事編集画面の下部に追加する
	 *
	 * @param CakeEvent $event
	 */
	public function bcFormTableAfter(CakeEvent $event) {
		if (!BcUtil::isAdminSystem()) {
			return false;
		}

		$View = $event->subject();

		if (!in_array($View->request->params['controller'], $this->targetController)) {
			return false;
		}

		if (!in_array($View->request->params['action'], $this->targetAction)) {
			return false;
		}

		$targetId = array('BlogPostForm');
		if (!in_array($event->data['id'], $targetId)) {
			return false;
		}

		if (!isset($View->request->data['CuCustomFieldConfig']) || empty($View->request->data['CuCustomFieldConfig'])) {
			return false;
		}

		if (!$View->request->data['CuCustomFieldConfig']['status']) {
			return false;
		}

		if ($this->isDisplay) {
			return false;
		}

		if ($View->request->data['CuCustomFieldConfig']['form_place'] === 'normal') {
			// ブログ記事追加画面にカスタムフィールド編集欄を追加する
			$this->isDisplay = true;
			return $View->element('CuCustomField.admin/cu_custom_field_values/cu_custom_field_form');
		}
		return false;
	}

}
