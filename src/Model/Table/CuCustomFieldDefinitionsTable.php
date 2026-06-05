<?php
namespace CuCustomField\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;

/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Model
 * @license          MIT LICENSE
 */


/**
 * Class CuCustomFieldDefinition
 */
class CuCustomFieldDefinitionsTable extends CuCustomFieldAppModelsTable
{

	/**
	 * 4系互換: エイリアス
	 *
	 * @var string
	 */
	public $alias = 'CuCustomFieldDefinition';

	/**
	 * 4系互換: 一時データ
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = [
		'Tree', 'BcCache'
	];

	/**
	 * belongsTo
	 * @var array
	 */
	public $belongsTo = ['CuCustomFieldConfig' => [
        'className' => 'CuCustomField.CuCustomFieldConfig',
		'sort' => ['lft' => 'ASC'],
		'foreignKey' => 'config_id'
    ]];

	public $configId = null;

	/**
	 * constructer
	 *
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);

		$validation = $this->getDefaultValidate();
		$this->validate = $validation['CuCustomFieldDefinition'];
	}
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

		$this->setAlias('CuCustomFieldDefinition');
        $this->setTable('cu_custom_field_definitions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Tree');

        $this->belongsTo('CuCustomFieldConfigs', [
            'foreignKey' => 'config_id',
            'joinType' => 'INNER',
            'className' => 'CuCustomField.CuCustomFieldConfigs',
        ]);
    }
	/**
	 * Setup
	 * @param $configId
	 */
	public function setup($configId)
	{
		$this->configId = $configId;
	}

	/**
	 * バリデーション
	 *
	 * @var array
	 */
	public $validate = [];

	/**
	 * KeyValue で利用するバリデーション内容を取得する
	 * - 通常の validate プロパティにコンストラクタでセットしている
	 *
	 * @return array
	 */
	public function getDefaultValidate()
	{
		$data = $this->keyValueValidate;
		return $data;
	}

	/**
	 * KeyValue で利用するバリデーション
	 * - actAs の validate 指定が空の際に、このプロパティ値が利用される
	 * - モデル名をキーに指定しているのは、KeyValueBehavior の validateSection への対応のため
	 *
	 * @var array
	 */
	public $keyValueValidate = [
		'CuCustomFieldDefinition' => [
			'name' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => '入力欄ラベルを入力してください。',
					'required' => true,
				],
				'maxLength' => [
					'rule' => ['maxLength', 255],
					'message' => '255文字以内で入力してください。',
				],
				'duplicateKeyValue' => [
					'rule' => ['duplicate', 'name'],
					'message' => '入力内容は既に使用されています。変更してください。',
				],
			],
			'label_name' => [
				'maxLength' => [
					'rule' => ['maxLength', 255],
					'message' => '255文字以内で入力してください。',
				],
			],
			'field_name' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => 'フィールド定義名を入力してください。',
					'required' => true,
				],
				'maxLength' => [
					'rule' => ['maxLength', 255],
					'message' => '255文字以内で入力してください。',
				],
				'alphaNumericPlus' => [
					'rule' => ['alphaNumericPlus'],
					'message' => '半角英数で入力してください。',
				],
				'duplicate' => [
					'rule' => ['duplicate', 'field_name'],
					'message' => '入力内容は既に使用されています。変更してください。',
				],
				// フィールドタイプが wysiwyg の場合はチェックするバリデーション
				'alphaNumericUnderscore' => [
					'rule' => ['alphaNumericUnderscore', 'field_type'],
					'message' => '半角英数とアンダースコアで入力してください。',
				],
				[
					'rule' => ['notInList', ['day']],
					'message' => 'フィールド定義名に利用できない文字列です。変更してください。',
				],
			],
			'field_type' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => 'フィールドタイプを選択してください。',
				],
			],
			'validate_regex' => [
				'checkValidateRegex' => [
					'rule' => ['checkValidateRegex'],
					'message' => '正規表現を入力してください。',
				],
			],
		],
	];

	/**
	 * データの重複チェックを行う
	 * @param array $check
	 * @return boolean false 重複あり / true 重複なし
	 */
	public function duplicate($check)
	{
		$conditions = [
			$this->alias . '.' . key($check) => $check[key($check)],
			$this->alias . '.config_id' => $this->data[$this->alias]['config_id'],
		];
		if (!empty($this->id) && $this->exists([$this->getAlias() . '.' . $this->getPrimaryKey() => $this->id])) {
			$conditions['NOT'] = [$this->alias . '.' . $this->primaryKey => $this->id];
		}
		$ret = $this->find()->where($conditions)->enableHydration(false)->first();
		if ($ret) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 英数チェックアンダースコア: アンダースコアを許容する
	 * フィールドタイプが wysiwyg の場合、フィールド名にハイフンがあると正常に表示されなくなるためのチェック
	 *
	 * @param array $check 対象データ
	 * @param string $fieldType フィールドタイプ
	 * @return    boolean
	 */
	public function alphaNumericUnderscore($check, $fieldType)
	{
		if (!$check[key($check)]) {
			return true;
		}
		if ($this->data[$this->alias][$fieldType] == 'wysiwyg') {
			if (preg_match("/^[a-zA-Z0-9\_]+$/", $check[key($check)])) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}

	/**
	 * 入力値チェック: 正規表現
	 * 正規表現選択時に正規表現が入力されているかチェック
	 *
	 * @param array $check 対象データ
	 * @return    boolean
	 */
	public function checkValidateRegex($check)
	{
		if (!empty($this->data[$this->alias]['validate']) && in_array('REGEX_CHECK', $this->data[$this->alias]['validate'])) {
			if (empty($this->data[$this->alias][key($check)])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data = $this->keyValueDefaults;
		return $data;
	}

	/**
	 * KeyValue で利用する初期値の指定
	 * - actAs の defaults 指定が空の際に、このプロパティ値が利用される
	 *
	 * @var array
	 */
	public $keyValueDefaults = [
		'CuCustomFieldDefinition' => [
			'status' => 1,
			'required' => 0,
		],
	];

	/**
	 * beforeSave
	 * マルチチェックボックスへの対応：配列で送られた値はシリアライズ化する
	 *
	 * @param EventInterface $event
	 * @param EntityInterface $entity
	 * @param ArrayObject $options
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		foreach ((array) $entity->toArray() as $key => $value) {
			if (is_array($value)) {
				$entity->set($key, serialize($value));
			}
		}

		if (!empty($this->data[$this->alias]) && is_array($this->data[$this->alias])) {
			foreach ($this->data[$this->alias] as $key => $value) {
				if (is_array($value)) {
					$this->data[$this->alias][$key] = serialize($value);
				}
			}
		}
	}

	/**
	 * afterFind
	 * シリアライズされているデータを復元して返す
	 *
	 * @param array $results
	 * @param boolean $primary
	 */
	public function afterFind($results, $primary = false)
	{
		parent::afterFind($results, $primary);
		$results = $this->unserializeData($results);
		return $results;
	}

	/**
	 * シリアライズされているデータを復元する
	 *
	 * @param array $data
	 * @return array
	 */
	public function unserializeData($data = [])
	{
		foreach($data as $key => $record) {
			foreach($record[$this->alias] as $field => $value) {
				if(!is_string($value) || !preg_match('/}$/', $value)) continue;
				// TODO \BaserCore\Utility\BcUtil::unserialize を利用するとエラーが発生するため通常のシリアライズを利用する
				if ($judge = @unserialize($value)) {
					$data[$key][$this->alias][$field] = $judge;
				}
			}
		}
		return $data;
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param int $configId
	 * @return array|false
	 */
	public function getControlSource($field)
	{
		if (!$this->configId) {
			trigger_error('CuCustomFieldDefinition::setup() を実行して CuCustomFieldDefinition モデルに configId を設定してください。');
		}
		switch($field) {
			case 'field_name':
				$conditions = [
					$this->alias . '.config_id' => $this->configId,
				];
					$controlSources['field_name'] = $this->find('list', [
						'keyField' => 'field_name',
						'valueField' => 'field_name',
					'conditions' => $conditions,
					'order' => ['lft' => 'ASC'],
					])->toArray();
				break;
		}
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

	/**
	 * ツリーリストを取得する（4系互換）
	 *
	 * @param array $conditions
	 * @return array
	 */
	public function generateTreeList($conditions = [])
	{
		$rows = $this->find()
			->select(['id', 'name', 'parent_id', 'lft'])
			->where((array) $conditions)
			->orderBy(['lft' => 'ASC'])
			->enableHydration(false)
			->all()
			->toList();

		$byId = [];
		foreach ($rows as $row) {
			$byId[$row['id']] = $row;
		}

		$list = [];
		foreach ($rows as $row) {
			$depth = 0;
			$parentId = $row['parent_id'] ?? null;
			while ($parentId !== null && isset($byId[$parentId]) && $depth < 50) {
				$depth++;
				$parentId = $byId[$parentId]['parent_id'] ?? null;
			}
			$list[$row['id']] = str_repeat('_', $depth) . (string) ($row['name'] ?? '');
		}

		return $list;
	}

	/**
	 * ループリストを取得する
	 * @return array|null
	 */
	public function getLoopList($configId)
	{
		return $this->find('list', ['conditions' => [
			'CuCustomFieldDefinition.field_type' => 'loop',
			'CuCustomFieldDefinition.config_id' => $configId
		]]);
	}

	/**
	 * 上へ移動
	 * config_id で絞り込むため、TreeBehavior::moveUp() をそのまま使えない
	 * @param $id
	 * @param $configId
	 * @return bool
	 */
	public function up($id, $configId)
	{
		$node = $this->get($id);
		$target = $this->find()
			->select(['id', 'parent_id'])
			->where(['id' => $id])
			->enableHydration(false)
			->first();
		if (!$target) {
			return false;
		}
		$definitions = $this->find()
			->select(['id', 'config_id', 'lft'])
			->where(['parent_id IS' => $target['parent_id']])
			->orderAsc('lft')
			->enableHydration(false)
			->toArray();
		$currentKey = null;
		foreach($definitions as $key => $value) {
			if ((int) $value['id'] === (int) $id) {
				$currentKey = $key;
				break;
			}
		}
		if ($currentKey === null) {
			return false;
		}

		$offset = 0;
		for($i = $currentKey - 1; $i >= 0; $i--) {
			$offset++;
			if (isset($definitions[$i])) {
				if ((int) $definitions[$i]['config_id'] === (int) $configId) {
					break;
				}
			} else {
				return false;
			}
		}
		if ($offset > 0) {
			return $this->moveUp($node, $offset);
		} else {
			return true;
		}
	}

	/**
	 * 下へ移動
	 * config_id で絞り込むため、TreeBehavior::moveDown() をそのまま使えない
	 * @param $id
	 * @param $configId
	 * @return bool
	 */
	public function down($id, $configId)
	{
		$node = $this->get($id);
		$target = $this->find()
			->select(['id', 'parent_id'])
			->where(['id' => $id])
			->enableHydration(false)
			->first();
		if (!$target) {
			return false;
		}
		$definitions = $this->find()
			->select(['id', 'config_id', 'lft'])
			->where(['parent_id IS' => $target['parent_id']])
			->orderAsc('lft')
			->enableHydration(false)
			->toArray();
		$currentKey = null;
		foreach($definitions as $key => $value) {
			if ((int) $value['id'] === (int) $id) {
				$currentKey = $key;
				break;
			}
		}
		if ($currentKey === null) {
			return false;
		}
		$offset = 0;
		for($i = $currentKey + 1; $i <= count($definitions) - 1; $i++) {
			$offset++;
			if (isset($definitions[$i])) {
				if ((int) $definitions[$i]['config_id'] === (int) $configId) {
					break;
				}
			} else {
				return false;
			}
		}
		if ($offset > 0) {
			return $this->moveDown($node, $offset);
		} else {
			return true;
		}
	}
}
