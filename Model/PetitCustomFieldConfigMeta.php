<?php

/**
 * [Model] PetitCustomFieldConfigMeta
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
App::uses('CuCustomField.CuCustomFieldAppModel', 'Model');

class PetitCustomFieldConfigMeta extends CuCustomFieldAppModel
{

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = array(
		'BcCache',
		'CuCustomField.List' => array(
			'scope' => 'petit_custom_field_config_id',
		),
	);

	/**
	 * belongsTo
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'CuCustomFieldConfig' => array(
			'className'	 => 'CuCustomField.CuCustomFieldConfig',
			'foreignKey' => 'petit_custom_field_config_id'
		),
	);

	/**
	 * カスタムフィールド設定メタ情報取得の際に、カスタムフィールド設定情報も併せて取得する
	 *
	 * @param array $results
	 * @param boolean $primary
	 */
	public function afterFind($results, $primary = false)
	{

		parent::afterFind($results, $primary);
		if ($results) {
			if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldDefinition')) {
				$this->CuCustomFieldDefinitionModel = ClassRegistry::getObject('CuCustomField.CuCustomFieldDefinition');
			} else {
				$this->CuCustomFieldDefinitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
			}

			$this->CuCustomFieldDefinitionModel->Behaviors->KeyValue->KeyValue = $this->CuCustomFieldDefinitionModel;
			foreach ($results as $key => $value) {
				// $data = $this->PetitCustomFieldModel->getSection($Model->id, $this->PetitCustomFieldModel->name);
				// $data = $this->{$this->modelClass}->getSection($foreignId, $this->modelClass);
				// getMax等のfindの際にはモデル名をキーとしたデータが入ってこないため判定
				if (isset($value['PetitCustomFieldConfigMeta'])) {
					$dataField = $this->CuCustomFieldDefinitionModel->getSection($value['PetitCustomFieldConfigMeta']['field_foreign_id'], 'CuCustomFieldDefinition');
					if ($dataField) {
						// マルチチェックの初期値の配列化に対応
						$dataField										 = $this->splitData($dataField);
						$_dataField['CuCustomFieldDefinition']		 = $dataField;
						$results[$key]['CuCustomFieldDefinition']	 = $_dataField['CuCustomFieldDefinition'];
					}
				}
			}
		}
		return $results;
	}

}
