<?php
namespace CuCustomField\Event;

use BaserCore\Event\BcHelperEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

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
 * Class CuCustomFieldHelperEventListener
 */
class CuCustomFieldHelperEventListener extends BcHelperEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = [
		'BcFormTable.before',
		'BcFormTable.after',
	];

	/**
	 * 処理対象とするコントローラー
	 *
	 * @var array
	 */
	private $targetController = ['BlogPosts'];

	/**
	 * 処理対象とするアクション
	 *
	 * @var array
	 */
	private $targetAction = ['edit', 'add'];

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
	 * @param \Cake\Event\Event $event
	 */
	public function bcFormTableBefore(\Cake\Event\Event $event)
	{
		if (!\BaserCore\Utility\BcUtil::isAdminSystem()) {
			return true;
		}

		$View = $event->getSubject();

		if (!in_array($View->getRequest()->getParam('controller'), $this->targetController)) {
			return true;
		}

		if (!in_array($View->getRequest()->getParam('action'), $this->targetAction)) {
			return true;
		}

		$targetId = ['BlogPostForm'];
		if (!in_array($event->getData()['id'], $targetId)) {
			return true;
		}

        // BlogContentsの取得
        $blogContent = $View->get('blogContent');
        if (!isset($blogContent->cu_custom_field_config) || empty($blogContent->cu_custom_field_config)) {
            return true;
        }

        if (!$blogContent->cu_custom_field_config['status']) {
            return true;
        }

		if ($blogContent->cu_custom_field_config['form_place'] === 'top') {
			// ブログ記事追加画面にカスタムフィールド編集欄を追加する
            $this->isDisplay = true;
            $event->setData('out' , $event->getData('out') . $View->element('CuCustomField.cu_custom_field_form'));
		}
		return true;
	}

	/**
	 * BcFormTable After
	 * - ブログ記事追加・編集画面にカスタムフィールド編集欄を追加する
	 * - 記事編集画面の下部に追加する
	 *
	 * @param \Cake\Event\Event $event
	 */
	public function bcFormTableAfter(\Cake\Event\Event $event)
	{
		if (!\BaserCore\Utility\BcUtil::isAdminSystem()) {
			return true;
		}

		$View = $event->getSubject();

		if (!in_array($View->getRequest()->getParam('controller'), $this->targetController)) {
			return true;
		}

		if (!in_array($View->getRequest()->getParam('action'), $this->targetAction)) {
			return true;
		}

		$targetId = ['BlogPostForm'];
		if (!in_array($event->getData()['id'], $targetId)) {
			return true;
		}
        // BlogContentsの取得
        $blogContent = $View->get('blogContent');
		if (!isset($blogContent->cu_custom_field_config) || empty($blogContent->cu_custom_field_config)) {
			return true;
		}

		if (!$blogContent->cu_custom_field_config['status']) {
			return true;
		}

		if ($this->isDisplay) {
			return true;
		}

		if ($blogContent->cu_custom_field_config['form_place'] === 'normal') {
			// ブログ記事追加画面にカスタムフィールド編集欄を追加する
			$this->isDisplay = true;
            $event->setData('out' , $event->getData('out') . $View->element('CuCustomField.cu_custom_field_form'));
		}
        return true;
	}

}
