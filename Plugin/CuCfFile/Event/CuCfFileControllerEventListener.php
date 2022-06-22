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
 * @uses CuCfFileControllerEventListener
 */
class CuCfFileControllerEventListener extends BcControllerEventListener {

	/**
	 * Events
	 * @var string[]
	 */
	public $events = [
		// CuApproverControllerEventListener::start() より早く、CuCustomFieldControllerEventListener::startup() 遅く
		'startup' => ['priority' => 5],
		'Blog.Blog.beforeRender',
		'Blog.BlogPosts.beforeRender'
	];

	/**
	 * Startup
	 * @param CakeEvent $event
	 */
	public function startup(CakeEvent $event) {
		if(!$this->isAction(['BlogPosts.AdminAdd', 'BlogPosts.AdminEdit', 'BlogPosts.AdminDelete', 'Blog.Archives'])) {
			return;
		}
		$controller = $event->subject();
		if(empty($controller->blogContent['BlogContent']['id'])) {
			return;
		}
		/* @var CuCustomFieldValue $CuCustomFieldValue */
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$CuCustomFieldValue->Behaviors->load('CuCfFile.CuCfFile', [
			'type' => 'BlogPost',
			'contentId' => $controller->blogContent['BlogContent']['id']
		]);
		if(!CakePlugin::loaded('CuApprover')) return;
		/* @var BlogPost $blogPostModel */
		$blogPostModel = ClassRegistry::init('Blog.BlogPost');
		$blogPostModel->Behaviors->load('CuCfFile.CuCfApprover');
		$this->setupApprover();
	}

	/**
	 * 公開承認プラグインの設定を行う
	 * @param $data
	 */
	public function setupApprover()
	{
		$fields = Configure::read('CuApprover.targets.BlogPost.draftFields.CuCustomFieldValue.fields');
		if(!$fields) {
			return;
		}
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		foreach($fields as $key => $fieldName) {
			$definition = $CuCustomFieldValue->getDefinition($fieldName);
			if(!$definition) {
				if(preg_match('/^(.+?)_[0-9]+_(.+)$/', $fieldName, $matches)) {
					$definition = $CuCustomFieldValue->getDefinition($matches[1]);
					if(!$definition) continue;
					$definition = $CuCustomFieldValue->getDefinition($matches[2]);
					if(!$definition) continue;
				}
			}
			if($definition['field_type'] === 'file') {
				unset($fields[$key]);
				$fields[$fieldName] = ['isFile' => true];
				$index = array_search($fieldName . '_', $fields);
				if($index !== false) unset($fields[$index]);
				$index = array_search($fieldName . '_delete', $fields);
				if($index !== false) unset($fields[$index]);
			}
		}
		Configure::write('CuApprover.targets.BlogPost.draftFields.CuCustomFieldValue.fields', $fields);
	}

	/**
	 * ブログ記事のプレビュー用処理
	 * @param CakeEvent $event
	 * @uses blogBlogBeforeRender
	 */
	public function blogBlogBeforeRender(CakeEvent $event) {
		$controller = $event->subject();
		/* @var CuCustomFieldValue $CuCustomFieldValue */
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		if(!$this->isAction('Blog.Archives')) {
			return;
		}
		if(empty($controller->BcContents->preview)) {
			return;
		}
		$post = $CuCustomFieldValue->saveTmpFiles($controller->viewVars['post'], mt_rand(0, 99999999));
		$controller->set('post', $post);
		$controller->request->data = $post;
	}

	/**
	 * Blog BlogPosts Before Render
	 *
	 * 公開承認の草稿データは平データで保存されているため
	 * ここで配列データに変換する
	 *
	 * @param CakeEvent $event
	 * @uses blogBlogPostsBeforeRender
	 */
	public function blogBlogPostsBeforeRender(CakeEvent $event) {
		if(!$this->isAction(['BlogPosts.AdminAdd', 'BlogPosts.AdminEdit'])) {
			return;
		}
		$controller = $event->subject();
		$mode = '';
		if(!empty($controller->viewVars['approverContentsMode'])) {
			$mode = $controller->viewVars['approverContentsMode'];
		}
		if(!empty($controller->request->query['cu_approver_load'])) {
			$mode = $controller->request->query['cu_approver_load'];
		}
		// 下書き画面にて、下書きデータが存在しなければ、本稿を表示する仕様としている為
		// 下書きデータが存在する場合のみ下書きデータのコンバート処理を行う
		if($mode !== 'draft' || empty($controller->request->data['CuApproverApplication']['draft'])) {
			return;
		}
		/* @var CuCustomFieldValue $CuCustomFieldValue */
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		if(!$this->isModelError($controller)) {
			$controller->request->data['CuCustomFieldValue'] = $CuCustomFieldValue->convertToArrayData($controller->request->data['CuCustomFieldValue']);
		}


	}

	/**
	 * @param $controller
	 * @return bool
	 */
	public function isModelError($controller)
	{
		$error = false;
		if(CakePlugin::loaded('CuApprover')) {
			$setting = CuApproverSetting::findByControllerName($controller->name);
			foreach($setting->getDraftFields() as $model => $fields) {
				list(, $model) = pluginSplit($model);
				if(isset($controller->{$model}) && $controller->{$model}->validationErrors) {
					$error = true;
				}
			}
		} else {
			if(isset($controller->BlogPost) && $controller->BlogPost->validationErrors) {
				$error = true;
			}
		}
		return $error;
	}

}
