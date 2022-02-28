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
 * Class CuCfFileModelEventListener
 */
class CuCfFileModelEventListener extends BcModelEventListener {

	/**
	 * Events
	 * @var string[]
	 */
	public $events = [
		'Blog.BlogPost.beforeSave'
	];

	/**
	 * blogBlogPostBeforeSave
	 *
	 * ループブロック削除時のファイル削除
	 * ポストデータが送られてこなかった場合、ループブロックが削除されたと判断し
	 * アップロード済のファイルを削除する
	 *
	 * @param CakeEvent $event
	 */
	public function blogBlogPostBeforeSave(CakeEvent $event) {
		if(!$this->isAction(['BlogPosts.AdminAdd', 'BlogPosts.AdminEdit'])) {
			return true;
		}
		$model = $event->subject();
		if(empty($model->data['CuApproverApplication']['contentsMode']) || $model->data['CuApproverApplication']['contentsMode'] !== 'draft') {
			return true;
		}
		if(empty($model->data['CuApproverApplication']['id'])) {
			return true;
		}
		$applicationModel = ClassRegistry::init('CuApprover.CuApproverApplication');
		$application = $applicationModel->find('first', ['conditions' => ['CuApproverApplication.id' => $model->data['CuApproverApplication']['id']]]);
		if($application) {
			$draft = BcUtil::unserialize($application['CuApproverApplication']['draft']);
		}
		if(empty($draft['CuCustomFieldValue'])) {
			return true;
		}
		$post = BcUtil::unserialize($model->data['CuApproverApplication']['draft'])['CuCustomFieldValue'];
		$fileUploader = $applicationModel->draftFileUploader['CuCustomFieldValue'];
		foreach($draft['CuCustomFieldValue'] as $key => $value) {
			if(!isset($post[$key]) && isset($fileUploader->settings['fields'][$key])) {
				$setting = $fileUploader->settings['fields'][$key];
				$fileUploader->deleteFile($setting, $value);
			}
		}
		return true;
	}

}
