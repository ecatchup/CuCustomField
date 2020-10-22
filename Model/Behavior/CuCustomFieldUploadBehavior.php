<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Model.Behavior
 * @license          MIT LICENSE
 */

/**
 * Class CuCustomFieldUploadBehavior
 */
class CuCustomFieldUploadBehavior extends ModelBehavior
{

	public $saveDir = null;
	public $beforeValue = [];
	public $deleteAction = [];

	public function __construct()
	{
		parent::__construct();
		$this->saveDir = WWW_ROOT . 'files' . DS . 'cu_custom_field' . DS;
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
		$definition = $model->getFieldDefinition($data['relate_id'], $key);
		if (!is_array($definition) || $definition['field_type'] === 'file') {
			list(, $fieldName) = explode('.', $key);
			$beforeValue = $model->getSection($data['relate_id'], 'CuCustomFieldValue', $fieldName);
			if ($deleteAction) {
				if ($data['value']) {
					$targetRecord = $model->find('first', ['conditions' => ['relate_id' => $data['relate_id'], 'key' => $key], 'recursive' => -1]);
					$targetRecord['CuCustomFieldValue']['value'] = '';
					$model->save($targetRecord, ['callbacks' => false, 'validate' => false]);
					$this->deleteFile($beforeValue);
				}
				return false;
			} else {
				if ($data['value']['size'] === 0) {
					return false;
				}
				$result = $this->saveFile($data['value'], $beforeValue);
			}
			if ($result !== false) {
				$model->data['CuCustomFieldValue']['value'] = $result;
			}
		}
		return true;
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
			return false;
		}
		if (!is_array($value) || $value['error'] !== 0) {
			return false;
		}
		$ext = decodeContent($value['type'], $value['name']);
		$year = date('Y');
		$month = date('m');
		$Folder = new Folder();
		$Folder->create($this->saveDir . $year . DS . $month . DS, 0777);
		$baseFileName = $year . '/' . $month . '/' . CakeText::uuid();
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
		if (!$value) {
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
