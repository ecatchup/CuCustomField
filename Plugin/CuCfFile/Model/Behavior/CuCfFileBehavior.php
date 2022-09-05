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
 * Class CuCfFileBehavior
 *
 * # プレビュー処理の仕様
 *
 * ## セッションへの保存
 * 1. Upload用セッションを削除
 * 2. ファイル名からファイルを特定するキーを生成
 * 3. そのキーをもとにUpload用セッションにコンテンツタイプと画像データを保存
 * 4. モデルのフィールドデータの配列に session_key をキーとして、ファイル名を格納
 *
 * ## ヘルパでの表示
 * 5. ファイル名のキーに session_key があれば、一時画像とみなしフラグを立てる
 * 6. 一時画像のフラグがたっていれば、画像のURLを UploadsControllerに切り替える
 * @property CuCustomFieldValue $CuCustomFieldValue
 * @property BlogPost $BlogPost
 * @property BcFileUploader $BcFileUploader
 * @uses CuCfFileBehavior
 */
class CuCfFileBehavior extends ModelBehavior
{

	/**
	 * oldEntity
	 * @var array
	 */
	public $oldEntity = [];

	/**
	 * CuCfFileBehavior constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->saveDir = WWW_ROOT . 'files' . DS . 'cu_custom_field' . DS;
	}

	/**
	 * Setup
	 * @param Model $model
	 * @param array $config
	 */
	public function setup(Model $model, $config = [])
	{
		$this->config = $config;
		$this->BcFileUploader = new BcFileUploader();
		$this->BlogPost = ClassRegistry::init('Blog.BlogPost');
		$this->CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		$this->setupFileUploader($model, $config['contentId']);
	}

	/**
	 * @param $modelName
	 * @return BcFileUploader|false
	 */
	public function getFileUploader(Model $model)
	{
		return $this->BcFileUploader;
	}

	/**
	 * Before Validate
	 *
	 * - id の設定
	 * - ループフィールドのセッティング
	 *
	 * @param Model $model
	 * @param array $options
	 * @return bool|mixed|void
	 */
	public function beforeValidate(Model $model, $options = [])
	{
		$model->data['CuCustomFieldValue']['id'] = (!empty($model->data['BlogPost']['id']))? $model->data['BlogPost']['id'] : null;
		$isDraft = false;
		if(!empty($model->data['CuApproverApplication']['contentsMode']) && $model->data['CuApproverApplication']['contentsMode'] === 'draft') {
			$isDraft = true;
		}
		$this->setupLoopFieldSettings($model->data['CuCustomFieldValue'], $isDraft);
	}

	/**
	 * Before Validate
	 *
	 * - ループを平データに変換
	 * - ポストデータをアップローダーにセットアップする
	 * - 旧データを取得する
	 *
	 * @param Model $Model
	 * @param array $options
	 * @return boolean
	 */
	public function afterValidate(Model $model, $options = [])
	{
		if($this->CuCustomFieldValue->validatingLock) return true;
		$data = $this->CuCustomFieldValue->convertToFlatteningData($model->data['CuCustomFieldValue']);
		$model->data['CuCustomFieldValue'] = $this->BcFileUploader->setupRequestData($data);
		$this->oldEntity = $this->CuCustomFieldValue->getOldEntity($model->data['CuCustomFieldValue']['id']);
		$this->CuCustomFieldValue->validatingLock = true;
		return true;
	}

	/**
	 * setupLoopFieldSettings
	 * @param array $data
	 * @param bool $isDraft
	 */
	public function setupLoopFieldSettings($data, $isDraft = false)
	{
		// 草稿の場合は草稿からデータを取得
		if($isDraft) {
			$applicationModel = ClassRegistry::init('CuApprover.CuApproverApplication');
			$application = $applicationModel->find('first', ['conditions' => ['CuApproverApplication.entity_id' => $data['id'], 'type' => 'BlogPost']]);
			if($application) {
				$draft = BcUtil::unserialize($application['CuApproverApplication']['draft']);
			}
			if(!empty($draft['CuCustomFieldValue'])) {
				$old = ['CuCustomFieldValue' => $this->CuCustomFieldValue->convertToArrayData($draft['CuCustomFieldValue'])];
			}
		} else {
			if(!empty($data['id'])) {
				$old = $this->CuCustomFieldValue->getSection($data['id']);
			}
		}

		if(!empty($old)) {
			foreach($old['CuCustomFieldValue'] as $fieldName => $field) {
				if ($field === 'a:0:{}') {
					$old['CuCustomFieldValue'][$fieldName] = [];
				} elseif(is_array($field)) {
					foreach($field as $key => $value) {
						if(!isset($data[$fieldName][$key])) {
							$data[$fieldName][$key] = $value;
						}
					}
				}
			}
		}
		$fieldSettings = [];
		foreach($data as $fieldName => $value) {
			$definition = $this->CuCustomFieldValue->getDefinition($fieldName);
			if(!$definition) continue;
			if($fieldName === $definition['field_name'] && $definition['field_type'] === 'loop') {
				foreach($value as $loopKey => $loop) {
					if($loopKey === '__loop-src__') {
						continue;
					}
					foreach($loop as $loopFieldName => $loopValue) {
						$name = $fieldName . '_' . $loopKey . '_' . $loopFieldName;
						$loopDefinition = $this->CuCustomFieldValue->getDefinition($loopFieldName);
						if(!$loopDefinition) continue;
						if($loopDefinition['field_type'] === 'file') {
							$fieldSettings[$name] = [
								'getUniqueFileName' => true,
								'name' => $name,
								'type' => 'all',
								'namefield' => 'no',
								'nameformat' => '%08d',
								'imageresize'  => ['width' => 1000, 'height' => 1000, 'thumb' => false],
								'imagecopy' => [
									'thumb' => ['suffix' => '_thumb', 'width' => 300, 'height' => 300]
								]
							];
						}
					}
				}
			}
		}
		$settings = $this->BcFileUploader->settings;
		$settings['fields'] = $fieldSettings + $settings['fields'];
		$this->BcFileUploader->settings = $settings;
	}

	/**
	 * Before Save
	 *
	 * ループを配列データに変換する
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave(Model $model, $options = [])
	{
		$model->data['CuCustomFieldValue'] = $this->CuCustomFieldValue->convertToArrayData($model->data['CuCustomFieldValue']);
		return true;
	}

	/**
	 * After save
	 *
	 * @param Model $Model
	 * @param bool $created
	 * @param array $options
	 */
	public function afterSave(Model $Model, $created, $options = [])
	{
		if($this->CuCustomFieldValue->savingLock) return;
		$entity = $Model->data['CuCustomFieldValue'];
		$entity['id'] = (!empty($Model->data['BlogPost']['id']))? $Model->data['BlogPost']['id'] : null;
		$entity = $this->CuCustomFieldValue->convertToFlatteningData($entity, true);

		// アップロード時既存データを削除
        if ($this->oldEntity) {
            $this->BcFileUploader->deleteExistingFiles($this->oldEntity);
        }

        // ファイル保存
        $entity = $this->BcFileUploader->saveFiles($entity);

        // 削除チェックボックス処理
        if ($this->CuCustomFieldValue->getOldEntity($entity['id'])) {
            $entity = $this->BcFileUploader->deleteFiles($this->oldEntity, $entity);
            // 公開承認を利用している場合、本稿の場合のみ、ループフィールドのファイルを削除
            if(empty($Model->data['CuApproverApplication']['contentsMode']) || $Model->data['CuApproverApplication']['contentsMode'] !== 'draft') {
            	$this->clearLoopFiles($entity);
            }
        }

        if ($this->BcFileUploader->isUploaded()) {
        	// リネーム処理
            $entity = $this->BcFileUploader->renameToBasenameFields($entity);
            $this->BcFileUploader->resetUploaded();
        }
		$entity = $this->CuCustomFieldValue->convertToArrayData($entity, true);
		$Model->saveSection($Model->data['BlogPost']['id'], ['CuCustomFieldValue' => $entity], 'CuCustomFieldValue', false);

        $this->CuCustomFieldValue->savingLock = true;
	}

	/**
	 * ループブロック削除時のファイル削除
	 * ポストデータが送られてこなかった場合、ループブロックが削除されたと判断し
	 * アップロード済のファイルを削除する
	 * @param $post
	 */
	public function clearLoopFiles($post)
	{
		if(empty($this->oldEntity)) {
			return;
		}
		foreach($this->oldEntity as $key => $value) {
			if(!isset($post[$key]) && isset($this->BcFileUploader->settings['fields'][$key])) {
				$setting = $this->BcFileUploader->settings['fields'][$key];
				$this->BcFileUploader->deleteFile($setting, $value);
			}
		}
	}

	/**
	 * setupFileUploader
	 * @param Model $model
	 * @param int $blogPostId
	 */
	public function setupFileUploader(Model $model, $contentId)
	{
		$this->CuCustomFieldValue->definitions = $model->getFieldDefinition($contentId);
		$fields = [];
		if(!empty($this->CuCustomFieldValue->definitions)) {
			foreach($this->CuCustomFieldValue->definitions as $definition) {
				if ($definition['field_type'] === 'file' && !$definition['parent_id']) {
					$fields[$definition['field_name']] = [
						'type' => 'all',
						'namefield' => 'no',
						'nameformat' => '%08d',
						'imageresize' => ['width' => 1000, 'height' => 1000],
						'imagecopy' => [
							'thumb' => ['suffix' => '_thumb', 'width' => 300, 'height' => 300]
						]
					];
				}
			}
		}
		$config = [
			'saveDir' => 'cu_custom_field' . DS . 'blog' . DS . $contentId . DS . 'blog_posts',
			'subdirDateFormat' => 'Y/m/',
			'fields' => $fields,
			'getUniqueFileName' => 'getUniqueFileName'
		];
        $this->BcFileUploader->initialize($config, $model);
	}

	/**
	 * 一時ファイルとして保存する
	 *
	 * @param Model $Model
	 * @param array $data
	 * @param string $tmpId
	 * @return mixed false|array
	 */
	public function saveTmpFiles(Model $Model, $data, $tmpId)
	{
		if(isset($data[$Model->alias])) {
			$entity = $data[$Model->alias];
		} else {
			$entity = [];
		}
		$this->setupFileUploader($Model, $data['BlogPost']['blog_content_id']);
		$this->setupLoopFieldSettings($entity);
		$entity = $this->CuCustomFieldValue->convertToFlatteningData($entity);
		$entity = $this->BcFileUploader->saveTmpFiles($entity, $tmpId);
		$entity = $this->CuCustomFieldValue->convertToArrayData($entity);
		$data[$Model->alias] = $entity;
		return $data;
	}

	/**
	 * after delete
	 * 画像ファイルの削除を行う
	 *
	 * @param Model $Model
	 * @param bool $cascade
	 * @return bool
	 */
	public function beforeDelete(Model $Model, $cascade = true)
	{
		$blogPostModel = ClassRegistry::init('Blog.BlogPost');
		$blogPost = $blogPostModel->find('first', ['conditions' => ['BlogPost.id' => $Model->id], 'recursive' => -1]);
		$this->setupFileUploader($Model,$blogPost['BlogPost']['blog_content_id']);
		$this->setupLoopFieldSettings(['id' => $Model->id]);
		$oldEntity = $this->CuCustomFieldValue->getOldEntity($Model->id);
		$this->BcFileUploader->deleteFiles($oldEntity, [], true);
		return true;
	}

}
