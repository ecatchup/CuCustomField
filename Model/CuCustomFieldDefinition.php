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

class CuCustomFieldDefinition extends CuCustomFieldAppModel
{

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = [
		'BcCache'
	];

/**
 * belongsTo
 * @var array
 */
	public $belongsTo = ['CuCustomFieldConfig' => [ 'className' => 'CuCustomField.CuCustomFieldConfig',
			'foreignKey' => 'config_id']];

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
	 * Setup
	 * @param $configId
	 */
	public function setup($configId) {
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
					'message' => 'カスタムフィールド名を入力してください。',
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
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => 'ラベル名を入力してください。',
					'required' => true,
				],
				'maxLength' => [
					'rule' => ['maxLength', 255],
					'message' => '255文字以内で入力してください。',
				],
			],
			'field_name' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => 'フィールド名を入力してください。',
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
					'message' => 'フィールド名に利用できない文字列です。変更してください。',
				],
			],
			'field_type' => [
				'notBlank' => [
					'rule' => ['notBlank'],
					'message' => 'フィールドタイプを選択してください。',
				],
			],
		],
	];

/**
 * データの重複チェックを行う
 * @param array $check
 * @return boolean false 重複あり / true 重複なし
 */
	public function duplicate($check) {
		$conditions = [
			$this->alias . '.' . key($check) => $check[key($check)],
			$this->alias . '.config_id' => $this->data[$this->alias]['config_id'],
		];
		if ($this->exists()) {
			$conditions['NOT'] = [$this->alias . '.' . $this->primaryKey => $this->id];
		}
		$ret = $this->find('first', ['conditions' => $conditions]);
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
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options = [])
	{
		parent::beforeSave($options);
		foreach($this->data[$this->alias] as $key => $value) {
			if (is_array($value)) {
				$this->data[$this->alias][$key] = serialize($value);
			}
		}
		return true;
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
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param int $configId
	 * @return array|false
	 */
	public function getControlSource($field)
	{
		if(!$this->configId) {
			trigger_error('CuCustomFieldDefinition::setup() を実行して CuCustomFieldDefinition モデルに configId を設定してください。');
		}
		switch($field) {
			case 'field_name':
				$conditions = [
					$this->alias . '.config_id' => $this->configId,
				];
				$controlSources['field_name'] = $this->find('list', [
					'conditions' => $conditions,
					'fields' => ['field_name'],
					'order' => ['sort' => 'ASC'],
				]);
				break;
		}
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

}
