<?php

/**
 * [Model] CuCustomFieldConfig
 *
 * @copyright        Copyright, Catchup, Inc.
 * @link            https://catchup.co.jp
 * @package            CuCustomField
 * @license            MIT
 */
App::uses('CuCustomField.CuCustomFieldAppModel', 'Model');

class CuCustomFieldConfig extends CuCustomFieldAppModel
{

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = [
		'PetitCustomFieldConfigMeta' => [
			'className' => 'CuCustomField.PetitCustomFieldConfigMeta',
			'foreignKey' => 'petit_custom_field_config_id',
			'order' => ['PetitCustomFieldConfigMeta.position' => 'ASC'],
			'dependent' => true,
		],
	];

	/**
	 * HABTM
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = [
		'CuCustomFieldDefinition' => [
			'className' => 'CuCustomField.CuCustomFieldDefinition',
			'joinTable' => 'petit_custom_field_config_metas',
			'foreignKey' => 'petit_custom_field_config_id',
			'associationForeignKey' => 'field_foreign_id',
			'conditions' => '',
			'order' => '',
			'limit' => '',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => ''
		]];

	/**
	 * 初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data = [
			'CuCustomFieldConfig' => [
				'status' => true,
				'form_place' => 'normal',
			]
		];
		return $data;
	}

}
