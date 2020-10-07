<?php

/**
 * [Model] CuCustomFieldConfig
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
App::uses('CuCustomField.CuCustomFieldAppModel', 'Model');

class CuCustomFieldConfig extends CuCustomFieldAppModel
{

	/**
	 * ModelName
	 *
	 * @var string
	 */
	public $name = 'CuCustomFieldConfig';

	/**
	 * PluginName
	 *
	 * @var string
	 */
	public $plugin = 'CuCustomField';

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = array('BcCache');

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = array(
		'PetitCustomFieldConfigMeta' => array(
			'className'	 => 'CuCustomField.PetitCustomFieldConfigMeta',
			'foreignKey' => 'petit_custom_field_config_id',
			'order'		 => array('PetitCustomFieldConfigMeta.position' => 'ASC'),
			'dependent'	 => true,
		),
	);

	/**
	 * HABTM
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'CuCustomFieldDefinition' => array(
			'className'				 => 'CuCustomField.CuCustomFieldDefinition',
			'joinTable'				 => 'petit_custom_field_config_metas',
			'foreignKey'			 => 'petit_custom_field_config_id',
			'associationForeignKey'	 => 'field_foreign_id',
			'conditions'			 => '',
			'order'					 => '',
			'limit'					 => '',
			'unique'				 => true,
			'finderQuery'			 => '',
			'deleteQuery'			 => ''
	));

	/**
	 * 初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data = array(
			'CuCustomFieldConfig' => array(
				'status'	 => false,
				'form_place' => 'normal',
			)
		);
		return $data;
	}

}
