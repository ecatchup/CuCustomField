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
 */
class CuCfFileBehavior extends ModelBehavior
{

	/**
	 * Save Directory
	 * @var string|null
	 */
	public $saveDir = null;

	/**
	 * Before Value
	 * @var array
	 */
	public $beforeValue = [];

	/**
	 * Delete Action
	 * @var array
	 */
	public $deleteAction = [];

	/**
	 * Config
	 * @var array
	 */
	public $config = [];

	/**
	 * Session
	 * @var SessionComponent|null
	 */
	public $Session = null;

	/**
	 * BlogPostModel
	 * @var BlogPost|null
	 */
	public $BlogPost = null;

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
		App::uses('SessionComponent', 'Controller/Component');
		$this->Session = new SessionComponent(new ComponentCollection());
		$this->BlogPost = ClassRegistry::init('Blog.BlogPost');
	}

	/**
	 * Before Validate
	 * @param Model $model
	 * @param array $options
	 * @return bool|mixed|void
	 */
	public function beforeValidate(Model $model, $options = [])
	{
		$contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $model->data['CuCustomFieldValue']['relate_id']]);
		foreach ($model->data['CuCustomFieldValue'] as $key => $value) {
			if($key === 'relate_id') {
				continue;
			}
			$definition = $model->getFieldDefinition($contentId, 'CuCustomFieldValue.' . $key);
			if(!$definition || $definition['field_type'] !== 'loop') {
				if (preg_match('/(.+)_saved$/', $key, $matches)) {
					$targetKey = $matches[1];
					if (empty($model->data['CuCustomFieldValue'][$targetKey]['name'])) {
						$model->data['CuCustomFieldValue'][$targetKey] = $value;
						unset($model->data['CuCustomFieldValue'][$key]);
					}
				}
			} else {
				if($value) {
					foreach($value as $i => $set) {
						foreach($set as $setKey => $setValue) {
							if (preg_match('/(.+)_saved$/', $setKey, $matches)) {
								$targetKey = $matches[1];
								if (empty($model->data['CuCustomFieldValue'][$key][$i][$targetKey]['name'])) {
									$model->data['CuCustomFieldValue'][$key][$i][$targetKey] = $setValue;
									unset($model->data['CuCustomFieldValue'][$key][$i][$setKey]);
								}
							}
						}
					}
				}
			}
		}
		return true;
	}

	/**
	 * Before Save
	 * @param Model $model
	 * @param array $options
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function beforeSave(Model $model, $options = [])
	{
		parent::beforeSave($model, $options);
		return $this->checkField($model, $model->data);
	}

	/**
	 * Check Field
	 * @param $model
	 * @return bool
	 */
	public function checkField($model, $data, $tmp = false)
	{
		if(isset($data['CuCustomFieldValue'])) {
			$data = $data['CuCustomFieldValue'];
		}
		if($data['key'] === 'CuCustomFieldValue.relate_id') {
			return false;
		}
		$key = $data['key'];
		$relateId = $data['relate_id'];
		$contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $relateId]);
		$definition = $model->getFieldDefinition($contentId, $key);
		if(!$definition || $definition['field_type'] !== 'loop') {
			$srcKey = $this->isDeleteAction($key);
			if($srcKey) {
				if($tmp) {
					return false;
				}
				$this->checkAndDeleteFile($model, $relateId, $srcKey, $data['value'], null, $tmp);
				return false;
			} else {
				$model->data['CuCustomFieldValue']['value'] = $this->checkAndSaveFile($model, $relateId, $key, $data['value'], null, $tmp);
			}
		} else {
			$value = [];
			if($data['value']) {
				foreach($data['value'] as $i => $set) {
					if($i === '__loop-src__') {
						continue;
					}
					if (!$set) {
						$value[$i] = $set;
						break;
					}
					$deleteTarget = [];
					foreach($set as $setKey => $setValue) {
						if (!empty($deleteTarget[$setKey])) {
							$value[$i][$setKey] = '';
							continue;
						}
						$srcKey = $this->isDeleteAction($setKey);
						if($srcKey) {
							if($setValue) {
								$deleteTarget[$srcKey] = true;
							}
							$this->checkAndDeleteFile($model, $relateId, 'CuCustomFieldValue.' . $srcKey, $setValue, $i, $tmp);
						} else {
							$result = $this->checkAndSaveFile($model, $relateId, 'CuCustomFieldValue.' . $setKey, $setValue, $i, $tmp);
							if($result !== false) {
								$value[$i][$setKey] = $result;
							}
						}
					}
				}
			}
			$model->data['CuCustomFieldValue']['value'] = $value;
		}
		return true;
	}

	/**
	 * 削除モードかチェックする
	 * @param $key
	 * @return false|mixed
	 */
	public function isDeleteAction($key) {
		if (preg_match('/(.+)_delete$/', $key, $matches)) {
			return $matches[1];
		}
		return false;
	}

	/**
	 * Check And Delete File
	 * @param $model
	 * @param $relateId
	 * @param $key
	 * @param $value
	 * @param null $loopRow
	 */
	public function checkAndDeleteFile($model, $relateId, $key, $value, $loopRow = null, $tmp = false) {
		if(empty($value)) {
			return;
		}
		$contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $relateId]);
		$definition = $model->getFieldDefinition($contentId, $key);
		if(!$definition || $definition['field_type'] !== 'file') {
			return;
		}
		$beforeValue = $this->getBeforeValue($model, $relateId, $this->getBareFieldName($key), $definition['parent_id'], $loopRow);
		if(is_null($loopRow)) {
			$targetRecord = $model->find('first', ['conditions' => ['relate_id' => $relateId, 'key' => $key], 'recursive' => -1]);
			$targetRecord['CuCustomFieldValue']['value'] = '';
			$model->save($targetRecord, ['callbacks' => false, 'validate' => false]);
		}
		$this->deleteFile($beforeValue, $tmp);
	}

	/**
	 * Check And Save File
	 * @param $model
	 * @param $key
	 * @param $value
	 * @param $relateId
	 * @param null $parentId
	 * @param null $loopRow
	 * @return false|string
	 */
	public function checkAndSaveFile($model, $relateId, $key, $value, $loopRow = null, $tmp = false) {
		$contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $relateId]);
		$definition = $model->getFieldDefinition($contentId, $key);
		if(!$definition || $definition['field_type'] !== 'file') {
			return $value;
		}
		$beforeValue = $this->getBeforeValue($model, $relateId, $this->getBareFieldName($key), $definition['parent_id'], $loopRow);
		return $this->saveFile($value, $beforeValue, $tmp);
	}

	/**
	 * Get Bare Field Name
	 * @param $fieldName
	 * @return mixed|string
	 */
	public function getBareFieldName($fieldName) {
		if(strpos($fieldName, '.') !== false) {
			list(, $fieldName) = explode('.', $fieldName);
		}
		return $fieldName;
	}

	/**
	 * Get Before Value
	 * @param $model
	 * @param $relateId
	 * @param $fieldName
	 * @param null $parentId
	 * @param null $loopRow
	 * @return mixed|string
	 */
	public function getBeforeValue($model, $relateId, $fieldName, $parentId = null, $loopRow = null) {
		if(!empty($parentId)) {
			// 親のフィールド名を取得
			$definitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
			$parentName = $definitionModel->field('field_name', ['id' => $parentId]);
			$parentValue = $model->getSection($relateId, 'CuCustomFieldValue', $parentName);
			if(!empty($parentValue[$loopRow][$fieldName])) {
				$beforeValue = $parentValue[$loopRow][$fieldName];
			} else {
				$beforeValue = '';
			}
		} else {
			$beforeValue = $model->getSection($relateId, 'CuCustomFieldValue', $fieldName);
		}
		return $beforeValue;
	}

	/**
	 * アップロードしたファイルを保存する
	 * @param $value
	 * @param $beforeValue
	 * @param false $tmp
	 * @return string|array
	 */
	public function saveFile($value, $beforeValue, $tmp = false)
	{
		if (empty($value)) {
			return '';
		}
		if (!is_array($value) || $value['error'] !== 0) {
			return $beforeValue;
		}
		if($value['size'] === 0) {
			return $beforeValue;
		}
		$ext = decodeContent($value['type'], $value['name']);
		$year = date('Y');
		$month = date('m');
		$baseFileName = $this->config['type'] . '/' . $this->config['contentId'] . '/' . $year . '/' . $month . '/' . CakeText::uuid();
		$fileName = $baseFileName . '.' . $ext;

		if($tmp) {
			$_fileName = str_replace(array('.', '/'), array('_', '_'), $fileName);
			$this->Session->write('Upload.' . $_fileName . '.type', $value['type']);
			$this->Session->write('Upload.' . $_fileName . '.data', file_get_contents($value['tmp_name']));
			$value['session_key'] = $fileName;
			return $value;
		} else {
			$Folder = new Folder();
			$Folder->create($this->saveDir . $this->config['type'] . DS . $this->config['contentId'] . DS . $year . DS . $month . DS, 0777);
			if (in_array($ext, ['png', 'gif', 'jpeg', 'jpg'])) {
				$thumbName = $baseFileName . '_thumb.' . $ext;
				$imageresizer = new Imageresizer();
				$imageresizer->resize($value['tmp_name'], $this->saveDir . $fileName, 1000, 1000, false);
				$imageresizer->resize($this->saveDir . $fileName, $this->saveDir . $thumbName, 300, 300, false);
			} else {
				move_uploaded_file($value['tmp_name'], $this->saveDir . $fileName);
				chmod($this->saveDir . $fileName, 0666);
			}
			if ($beforeValue) {
				$this->deleteFile($beforeValue);
			}
		}
		return $fileName;
	}

	/**
	 * ファイルを削除する
	 *
	 * @param string $value
	 * @return |null
	 */
	public function deleteFile($value, $tmp = false)
	{
		if (!$value || strpos($value, '.') === false) {
			return false;
		}

		if(!$tmp) {
			$filePath = $this->saveDir . $value;
			list($baseFileName, $ext) = explode('.', $value);
			$thumbPath = $this->saveDir . $baseFileName . '_thumb.' . $ext;
			if (file_exists($filePath)) {
				unlink($filePath);
			}
			if (file_exists($thumbPath)) {
				unlink($thumbPath);
			}
		}
		return null;
	}

	/**
	 * セッションに一時ファイルを保存
	 * @param Model $Model
	 * @param $data
	 * @return mixed
	 */
	public function saveTmpFile(Model $Model, $data)
	{
		$this->Session->delete('Upload');
		if($data['CuCustomFieldValue']) {
			foreach ($data['CuCustomFieldValue'] as $field => $value) {
				$newDetail = [];
				$section = 'CuCustomFieldValue';
				$key = $section . '.' . $field;
				$newDetail['relate_id'] = $data['BlogPost']['id'];
				$newDetail['key'] = $key;
				$newDetail['value'] = $value;
				$newDetail['model'] = 'CuCustomFieldValue';
				$this->checkField($Model, $newDetail, true);
				if(isset($Model->data['CuCustomFieldValue']['value'])) {
					$data['CuCustomFieldValue'][$field] = $Model->data['CuCustomFieldValue']['value'];
				}
			}
		}
		return $data;
	}
}
