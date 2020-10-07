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
		'Form.afterCreate',
		'Form.afterForm',
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
	 * formAfterCreate
	 * - ブログ記事追加・編集画面にカスタムフィールド編集欄を追加する
	 * - 記事編集画面の上部に追加する
	 *
	 * @param CakeEvent $event
	 * @return array
	 */
	public function formAfterCreate(CakeEvent $event)
	{
		if (!BcUtil::isAdminSystem()) {
			return;
		}

		$View = $event->subject();

		if (!in_array($View->request->params['controller'], $this->targetController)) {
			return;
		}

		if (!in_array($View->request->params['action'], $this->targetAction)) {
			return;
		}

		$targetId = array('BlogPostForm');
		if (!in_array($event->data['id'], $targetId)) {
			return;
		}

		if (!isset($View->request->data['CuCustomFieldConfig']) || empty($View->request->data['CuCustomFieldConfig'])) {
			return;
		}

		if (!$View->request->data['CuCustomFieldConfig']['status']) {
			return;
		}

		if ($View->request->data['CuCustomFieldConfig']['form_place'] === 'top') {
			// ブログ記事追加画面にカスタムフィールド編集欄を追加する
			$event->data['out']	 = $event->data['out'] . $View->element('CuCustomField.petit_custom_field_form');
			$this->isDisplay	 = true;
		}

		return;
	}

	/**
	 * formAfterForm
	 * - ブログ記事追加・編集画面にカスタムフィールド編集欄を追加する
	 * - 記事編集画面の下部に追加する
	 *
	 * @param CakeEvent $event
	 */
	public function formAfterForm(CakeEvent $event)
	{
		if (!BcUtil::isAdminSystem()) {
			return;
		}

		$View = $event->subject();

		if (!in_array($View->request->params['controller'], $this->targetController)) {
			return;
		}

		if (!in_array($View->request->params['action'], $this->targetAction)) {
			return;
		}

		$targetId = array('BlogPostForm');
		if (!in_array($event->data['id'], $targetId)) {
			return;
		}

		if (!isset($View->request->data['CuCustomFieldConfig']) || empty($View->request->data['CuCustomFieldConfig'])) {
			return;
		}

		if (!$View->request->data['CuCustomFieldConfig']['status']) {
			return;
		}

		if ($this->isDisplay) {
			return;
		}

		if ($View->request->data['CuCustomFieldConfig']['form_place'] === 'normal') {
			// ブログ記事追加画面にカスタムフィールド編集欄を追加する
			echo $View->element('CuCustomField.cu_custom_field_form');
		}
	}

}
