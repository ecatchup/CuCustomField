<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Model
 * @license          MIT LICENSE
 */
App::uses('CuCustomField.CuCustomFieldAppModel', 'Model');

/**
 * Class CuCustomFieldConfig
 */
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
