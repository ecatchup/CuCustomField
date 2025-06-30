<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Event
 * @license          MIT LICENSE
 */

class CuCfApproverBehavior extends ModelBehavior
{

	/**
	 * After Validate
	 *
	 * 公開承認の草稿モードの保存で本稿を元データに書き戻す際に（CuApproverApplicationBehavior::getPublish() 内の find()）
	 * データを平データ取得するためのフラグを立てる
	 * 平データの取得処理は、CuCustomFieldModelEventListener::blogBlogPostAfterFind() に定義
	 *
	 * @param Model $model
	 * @return bool
	 */
	public function afterValidate(Model $model)
	{
		if(empty($model->data['CuApproverApplication']['contentsMode']) || $model->data['CuApproverApplication']['contentsMode'] !== 'draft') {
			return true;
		}
		foreach($model->getEventManager()->listeners('Model.Blog.BlogPost.afterFind') as $listener) {
			if(get_class($listener['callable'][0]) === 'CuCustomFieldModelEventListener') {
				$listener['callable'][0]->findFlatteningMode = true;
			}
		}
		return true;
	}
}

