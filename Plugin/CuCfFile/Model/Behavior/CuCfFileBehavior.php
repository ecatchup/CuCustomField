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
 */
class CuCfFileBehavior extends ModelBehavior
{

	public $saveDir = null;
	public $beforeValue = [];
	public $deleteAction = [];
	public $config = [];

	public function __construct()
	{
		parent::__construct();
		$this->saveDir = WWW_ROOT . 'files' . DS . 'cu_custom_field' . DS;
	}

	public function setup(Model $model, $config = [])
	{
		$this->config = $config;
	}

	public function beforeSave(Model $model, $options = [])
	{
		parent::beforeSave($model, $options);
		$data = $model->data['CuCustomFieldValue'];
		$key = $data['key'];
		$deleteAction = false;
		if (preg_match('/(.+)_delete$/', $data['key'], $matches)) {
			$key = $matches[1];
			$deleteAction = true;
		}
		$relateId = $data['relate_id'];
		$definition = $model->getFieldDefinition($relateId, $key);
		if($definition['field_type'] === 'loop') {
			$value = [];
			if($data['value']) {
				foreach($data['value'] as $i => $set) {
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
						if (preg_match('/(.+)_delete$/', $setKey, $matches)) {
							$setKey = $matches[1];
							$deleteAction = true;
						} else {
							$deleteAction = false;
						}
						$definition = $model->getFieldDefinition($relateId, $setKey);
						$beforeValue = $this->getBeforeValue($model, $relateId, $this->getBareFieldName($setKey), $definition['parent_id'], $i);
						if ($deleteAction) {
							if ($setValue) {
								$deleteTarget[$setKey] = true;
								$this->deleteFile($beforeValue);
							}
						} else {
							if($definition && (!is_array($definition) || $definition['field_type'] === 'file')) {
								$value[$i][$setKey] = $this->saveFile($setValue, $beforeValue);
							} else {
								$value[$i][$setKey] = $setValue;
							}
						}
					}
				}
			}
		} else {
			$beforeValue = $this->getBeforeValue($model, $relateId, $this->getBareFieldName($key));
			if($deleteAction) {
				if(!empty($data['value'])) {
					$targetRecord = $model->find('first', ['conditions' => ['relate_id' => $relateId, 'key' => $key], 'recursive' => -1]);
					$targetRecord['CuCustomFieldValue']['value'] = '';
					$model->save($targetRecord, ['callbacks' => false, 'validate' => false]);
					$this->deleteFile($beforeValue);
				}
				return false;
			} else {
				if($definition && (!is_array($definition) || $definition['field_type'] === 'file')){
					$value = $this->saveFile($data['value'], $beforeValue);
				} else {
					$value = $data['value'];
				}
			}
		}
		$model->data['CuCustomFieldValue']['value'] = $value;
		return true;
	}

	public function getBareFieldName($fieldName) {
		if(strpos($fieldName, '.') !== false) {
			list(, $fieldName) = explode('.', $fieldName);
		}
		return $fieldName;
	}

	public function getBeforeValue($model, $relateId, $fieldName, $parentId = null, $loopRow = null) {
		if(!empty($parentId)) {
			// 親のフィールド名を取得
			$definitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
			$parentName = $definitionModel->field('name', ['id' => $parentId]);
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
	 * @param $model
	 * @param $relateId
	 * @param $key
	 * @param $value
	 * @return false|string
	 */
	public function saveFile($value, $beforeValue)
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
		$Folder = new Folder();
		$Folder->create($this->saveDir . $this->config['type'] . DS . $this->config['contentId'] . DS . $year . DS . $month . DS, 0777);
		$baseFileName = $this->config['type'] . '/' . $this->config['contentId'] . '/' . $year . '/' . $month . '/' . CakeText::uuid();
		$fileName = $baseFileName . '.' . $ext;
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
		return $fileName;
	}

	/**
	 * ファイルを削除する
	 *
	 * @param string $value
	 * @return |null
	 */
	public function deleteFile($value)
	{
		if (!$value || strpos($value, '.') === false) {
			return false;
		}
		$filePath = $this->saveDir . $value;
		list($baseFileName, $ext) = explode('.', $value);
		$thumbPath = $this->saveDir . $baseFileName . '_thumb.' . $ext;
		if (file_exists($filePath)) {
			unlink($filePath);
		}
		if (file_exists($thumbPath)) {
			unlink($thumbPath);
		}
		return null;
	}
}
