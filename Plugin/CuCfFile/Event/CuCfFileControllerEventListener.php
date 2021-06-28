<?php
/**
 * CuCustomField : baserCMS Custom Field File Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfFile.Model.Behavior
 * @license          MIT LICENSE
 */

/**
 * Class CuCfFileControllerEventListener
 */
class CuCfFileControllerEventListener extends BcControllerEventListener {
	/**
	 * Events
	 * @var string[]
	 */
	public $events = [
		'startup',
		'Blog.Blog.beforeRender'
	];

	/**
	 * Startup
	 * @param CakeEvent $event
	 */
	public function startup(CakeEvent $event) {
		if(!$this->isAction(['BlogPosts.AdminAdd', 'BlogPosts.AdminEdit', 'Blog.Archives'])) {
			return;
		}
		/* @var CuCustomFieldValue $CuCustomFieldValue */
		$controller = $event->subject();
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$CuCustomFieldValue->Behaviors->load('CuCfFile.CuCfFile', [
			'type' => 'BlogPost',
			'contentId' => $controller->blogContent['BlogContent']['id']
		]);
	}

	/**
	 * ブログ記事のプレビュー用処理
	 * @param CakeEvent $event
	 */
	public function blogBlogBeforeRender(CakeEvent $event) {
		if(!$this->isAction('Blog.Archives')) {
			return;
		}
		$controller = $event->subject();
		if(!isset($controller->request->params['pass'][0]) || !is_numeric($controller->request->params['pass'][0])) {
			return;
		}
		if(empty($controller->BcContents->preview)) {
			return;
		}
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$controller->set('post', $CuCustomFieldValue->saveTmpFile($controller->viewVars['post']));
	}
}
