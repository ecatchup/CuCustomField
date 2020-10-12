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
		'CuCustomFieldDefinition' => [
			'className' => 'CuCustomField.CuCustomFieldDefinition',
			'foreignKey' => 'config_id',
			'order' => ['CuCustomFieldDefinition.lft' => 'ASC'],
			'dependent' => true,
		],
	];

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
