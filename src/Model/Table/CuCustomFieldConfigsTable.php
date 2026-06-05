<?php
namespace CuCustomField\Model\Table;
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Model
 * @license          MIT LICENSE
 */
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Utility\BcUtil;
use BaserCore\Error\BcException;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\ORM\Exception\PersistenceFailedException;


/**
 * Class CuCustomFieldConfig
 */
class CuCustomFieldConfigsTable extends CuCustomFieldAppModelsTable
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
			'order' => ['lft' => 'ASC'],
			'dependent' => true,
		],
	];
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cu_custom_field_configs');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->hasMany('CuCustomFieldDefinitions', [
            'className' => 'CuCustomField.CuCustomFieldDefinitions',
            'foreignKey' => 'config_id',
			'order' => ['lft' => 'ASC'],
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }
	/**
	 * 初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data = [
			'CuCustomFieldConfig' => [
				'status' => 1,
				'form_place' => 'normal',
			]
		];
		return $data;
	}

}
