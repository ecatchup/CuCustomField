<?php
namespace CuCustomField\Model\Table;

use BaserCore\Model\Behavior\BcKeyValueBehavior;
use CuCustomField\Model\Behavior\KeyValueBehavior;
use CuCustomField\Model\Behavior\CuCfFileBehavior;
use Cake\Event\Event;
use Cake\ORM\Table;


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
 * Class CuCustomFieldValue
 *
 * KeyValueBehavior を利用しているため、beforeSave / afterSave は呼び出されない
 * そのためこのクラスでは実装しないこと
 * 他の Behavior で上記イベントを実装できるが、CuCustomFieldModelEventListener より dispatch している
 */
class CuCustomFieldValuesTable extends CuCustomFieldAppModelsTable
{

	/**
	 * actsAs
	 *
	 * @var array
	 */
	// public $actsAs = [
	// 	'CuCustomField.KeyValue' => [
	// 		'foreignKeyField' => 'relate_id'
	// 	]
	// ];

	/**
	 * 保存中のロック
	 * @var bool
	 */
	public $savingLock = false;

	/**
	 * バリデーション中のロック
	 * @var bool
	 */
	public $validatingLock = false;

	/**
	 * definitions
	 * @var array
	 */
	public $definitions;

	/**
	 * バリデーション
	 * - CuCustomFieldModelEventListener::_setValidate にて設定する
	 *
	 * @var array
	 */
	public $validate = [];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('cu_custom_field_values');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('CuCustomField.CuCfFile');
        $this->addBehavior('CuCustomField.KeyValue', [
            'foreignKeyField' => 'relate_id'
        ]);
        $this->belongsTo('BlogPosts', [
            'className' => 'BcBlog.BlogPosts',
            'foreignKey' => 'relate_id',
        ]);
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
	 * - 初期値は CuCustomFieldControllerEventListener でフィールド設定から生成している
	 *
	 * @var array
	 */
	public $keyValueDefaults = [
		'CuCustomFieldValue' => [],
	];

	/**
	 * 保存データに対するカスタムフィールドの設定情報
	 *
	 * @var array
	 */
	public $fieldConfig = [];

	/**
	 * カスタムフィールドのフィールド別設定データ
	 *
	 * @var array
	 */
	public $publicFieldConfigData = [];

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
		// TODO json_decode($results, true) に切替える
		$results = $this->unserializeData($results);
		return $results;
	}

	/**
	 * Before Save
     * - KeyValueBehavior で利用するため、このタイミングでデータを変換する
     * @param Event $event
	 * @return bool
	 */
	public function beforeSave(Event $event)
	{
        $entity = $event->getData('entity');
		//$this->data['CuCustomFieldValue'] = $this->autoConvert($entity->toArray());
		// 新規登録時、このタイミングで $this->>data['BlogPost']['no'] に新しいデータが入っていないため実体より取得
		$blogPostModel = \Cake\ORM\TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
        // debug($entity->toArray());
        // debug($blogPostModel);
        // exit;
		if(!empty($blogPostModel->data['BlogPost']['id'])) {
			$this->data['CuCustomFieldValue']['id'] = $blogPostModel->data['BlogPost']['id'];
		}
		if(!empty($blogPostModel->data['BlogPost']['no'])) {
			$this->data['CuCustomFieldValue']['no'] = $blogPostModel->data['BlogPost']['no'];
		}
        return $entity;
		return parent::beforeSave($entity);
	}

	/**
	 * フィールド設定情報をもとに保存文字列の自動変換処理を行う
	 * - 変換指定が有効の際に変換する
	 *
	 * @param array $data
	 * @return array $data
	 */
	public function autoConvert($data = [])
	{
		if(!$data) {
			return $data;
		}
		foreach($data as $key => $value) {
			foreach($this->fieldConfig as $config) {
				$config = $config['CuCustomFieldDefinition'];
				if ($key == $config['field_name']) {
					if ($config['auto_convert'] == 'CONVERT_HANKAKU') {
						// 全角英数字を半角に変換する処理を行う
						$data[$key] = mb_convert_kana($value, 'a');
					}
					// 配列で送られた値はシリアライズ化する
					// TODO json_encode() に切替える
					if (is_array($value)) {
						$data[$key] = serialize($value);
					}
				}
			}
		}
		return $data;
	}

	/**
	 * 正規表現チェック用関数
	 *
	 * @param array $check 対象データ
	 * @return    boolean
	 */
	public function regexCheck($check)
	{
		$fieldName = key($check);
		//$check[key($check)]
		$fieldConfig = \Cake\Utility\Hash::extract($this->fieldConfig, '{n}.CuCustomFieldDefinition[field_name=' . $fieldName . ']');
		$validateRegex = \Cake\Utility\Hash::extract($fieldConfig, '{n}.validate_regex');
		if (preg_match($validateRegex[0], $check[key($check)])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * フィールド定義を取得する
	 * @param int/$relateId
	 * @param string/$fieldName
	 * @param bool $asObject true の場合 object 配列で返す（admin向け）
	 * @return false|mixed
	 */
	public function getFieldDefinition(int $contentId, string $fieldName = '', bool $asObject = false)
	{
		/* @var CuCustomFieldConfig $$CustomFieldConfig */
		$CustomFieldConfig = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldConfigs');
		$CustomFieldDefinition = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldDefinitions');
		$definitionAlias = $CustomFieldDefinition->getAlias();
		$rows = $CustomFieldConfig->find()
            ->join([
                'table' => $CustomFieldDefinition->getTable(),
                'alias' => $definitionAlias,
                'type' => 'inner',
                'conditions' => [
                    $definitionAlias . '.config_id = CuCustomFieldConfigs.id'
                ]
            ])->where([
                'CuCustomFieldConfigs.content_id' => $contentId,
                $definitionAlias . '.status' => true,
            ])
            ->select($CustomFieldDefinition)
            ->enableHydration(false)
            ->all()
            ->toArray();

		$config = [];
		foreach ($rows as $row) {
			$definition = [];
			if (isset($row[$definitionAlias]) && is_array($row[$definitionAlias])) {
				$definition = $row[$definitionAlias];
			} else {
				$prefix = $definitionAlias . '__';
				foreach ($row as $key => $value) {
					if (is_string($key) && str_starts_with($key, $prefix)) {
						$definition[substr($key, strlen($prefix))] = $value;
					}
				}
			}
			if ($definition) {
				$row['CuCustomFieldDefinitions'] = $definition;
				$config[] = $row;
			}
		}

		if (is_array($config) && empty($config)) {
			return false;
		}
		if ($fieldName) {
			if(strpos($fieldName, '.') !== false) {
				list(, $fieldName) = explode('.', $fieldName);
			}
			foreach($config as $definition) {
				if (!empty($definition['CuCustomFieldDefinitions']['field_name']) && $definition['CuCustomFieldDefinitions']['field_name'] === $fieldName) {
					if ($asObject) {
						return (object) $definition;
					}
					return $definition;
				}
			}
			return false;
		} else {
			if ($asObject) {
				return array_map(static function ($row) {
					return (object) $row;
				}, $config);
			}
			return $config;
		}
	}

	/**
	 * Setup
	 * @param $contentId
	 */
	public function setup($contentId) {
		if(isset($this->publicFieldConfigData[$contentId])) {
			return;
		}
		$definition = $this->getFieldDefinition($contentId);
		if($definition) {
			$this->publicFieldConfigData[$contentId] = \Cake\Utility\Hash::combine($definition, '{n}.CuCustomFieldDefinitions.field_name', '{n}.CuCustomFieldDefinitions');
            // dd($this->publicFieldConfigData);
		}
	}

	/**
	 * バリデーション
	 * @param $data
	 */
	public function validateValues($data) {
		$validateSuccess = true;
		$beforeData = $data;
		// ループブロック以外に対するバリデーション
		$this->set($data);
		if (!$this->validates()) {
			$validateSuccess = false;
		}

		// ループブロックに対するバリデーション

		// - ループブロックを取得
		$loopFieldNames = [];
		foreach ($this->fieldConfig as $fieldConfig) {
			if ($fieldConfig['CuCustomFieldDefinition']['field_type'] === 'loop') {
				$loopFieldNames[] = $fieldConfig['CuCustomFieldDefinition']['field_name'];
			}
		}
		$loopGroups = [];
		foreach ($data['CuCustomFieldValue'] as $fieldKey => $fieldValue) {
			if (in_array($fieldKey, $loopFieldNames)) {
				$loopGroups[$fieldKey] = $fieldValue;
			}
		}

		// - ブロックごとにバリデーションを実行
		$dataTmp = $this->data;
		$modelValidate = $this->validate;
		foreach ($loopGroups as $loopGroupName => $loopGroup) {
			foreach ($loopGroup as $loopBlockKey => $loopBlock) {

				$this->validate = $this->getLoopBlockValidate($loopBlock);
				$this->set($loopBlock);
				if (!$this->validates()) {
					$validateSuccess = false;
				}
				if ($this->validationErrors) {
					foreach ($this->validationErrors as $fieldKey => $fieldError) {
						$this->inValidate("{$loopGroupName}_{$loopBlockKey}_{$fieldKey}", $fieldError[0]);
					}
				}
			}
		}
		$this->data = $dataTmp;
		$this->validate = $modelValidate;
		if(!$validateSuccess) {
			$this->data = $beforeData;
		}
		return $validateSuccess;
	}

	/**
	 * ループブロック中に存在するフィールドのバリデーションを取得
	 * @param $loopBlock
	 */
	private function getLoopBlockValidate($loopBlock) {
		$loopBlockValidate = [];
		foreach ($loopBlock as $loopBlockFieldName => $loopBlockFieldValue) {
			if (!empty($this->validate[$loopBlockFieldName])) {
				$loopBlockValidate[$loopBlockFieldName] = $this->validate[$loopBlockFieldName];
			}
		}
		return $loopBlockValidate;
	}

	/**
	 * getUniqueFileName
	 *
	 * BcFileUploader で利用
	 *
	 * @param array $setting
	 * @param array $file
	 * @return mixed
	 */
	public function getUniqueFileName($setting, $file, $entity)
	{
        $ext = $file['ext'];
        $pathInfo = pathinfo($file['name']);
        $basename = $pathInfo['filename'];
        // 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
        $records = $this->find('all', [
        	'fields' => 'value',
        	'conditions' => [
        		'relate_id <>' => $entity['id'],
        		'key' => 'CuCustomFieldValue.file',
        		'value LIKE' => $basename . '%' . $ext
        	],
        	'recursive' => -1
        ]);
        $numbers = [];
        if ($records) {
            foreach($records as $data) {
                if (!empty($data['CuCustomFieldValue']['value'])) {
                    $_basename = preg_replace("/\." . $ext . "$/is", '', $data['CuCustomFieldValue']['value']);
                    $lastPrefix = preg_replace('/^' . preg_quote($basename, '/') . '/', '', $_basename);
                    if (!$lastPrefix) {
                        $numbers[1] = 1;
                    } elseif (preg_match("/^__([0-9]+)$/s", $lastPrefix, $matches)) {
                        $numbers[$matches[1]] = true;
                    }
                }
            }
            if ($numbers) {
                $prefixNo = 1;
                while(true) {
                    if (!isset($numbers[$prefixNo])) break;
                    $prefixNo++;
                }
                if ($prefixNo == 1) {
                    return $basename . '.' . $ext;
                } else {
                    return $basename . '__' . ($prefixNo) . '.' . $ext;
                }
            } else {
                return $basename . '.' . $ext;
            }
        } else {
            return $basename . '.' . $ext;
        }
	}

	/**
	 * getOldEntity
	 *
	 * BcFileUploader で利用
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function getOldEntity($id)
	{
		$entity = $this->getSection($id);
		if(!$entity) return false;
		return $this->convertToFlatteningData($entity['CuCustomFieldValue'], true);
	}

	/**
	 * convertFlatteningData
	 * @param array $data
	 * @param false $unserialize
	 * @return mixed
	 */
	public function convertToFlatteningData($data, $unserialize = false)
	{
		foreach($data as $fieldName => $value) {
			$definition = $this->getDefinition($fieldName);
			if(!$definition) continue;
			if($fieldName === $definition['field_name'] && $definition['field_type'] === 'loop') {
				if($unserialize && is_string($value)) {
					$value = unserialize($value);
				}
				if($value && is_array($value)) {
					foreach($value as $loopKey => $loop) {
						if($loopKey === '__loop-src__') {
							continue;
						}
						foreach($loop as $loopFieldName => $loopValue) {
							$name = $fieldName . '_' . $loopKey . '_' . $loopFieldName;
							$data[$name] = $loopValue;
						}
					}
					unset($data[$fieldName]);
				}
			}
		}
		return $data;
	}

	/**
	 * convertToArrayData
	 * @param array $data
	 * @return mixed
	 */
	public function convertToArrayData($data, $serialize = false)
	{
		if(empty($this->definitions)) {
			return $data;
		}

		// ループデータで平データでなく、ループフィールドと一致するのキーのデータは、ゴミデータとして消去
		// 公開承認で本稿データを取得後に、草稿データで上書きする処理で、上記の条件のキーが残ってしまう
		// CuApproverControllerEventListener::loadDraft()
		// （例）
		// loop_1_file ▶ 変換対象
		// loop_1_select ▶ 変換対象
		// loop_1_text ▶ 変換対象
		// loop ▶ 消去対象
		foreach($data as $fieldName => $value) {
			foreach($this->definitions as $definition) {
				if($definition['field_type'] === 'loop' && $definition['field_name'] === $fieldName) {
					$data[$fieldName] = [];
				}
			}
		}

		foreach($data as $fieldName => $value) {
			foreach($this->definitions as $definition) {
				if($definition['field_type'] === 'loop') {
					$regex = '/' . $definition['field_name'] . '_([0-9]+)_(.+)$/is';
					if(preg_match($regex, $fieldName, $matches)) {
						$loopKey = $matches[1];
						$loopFieldName = $matches[2];
						$data[$definition['field_name']][$loopKey][$loopFieldName] = $value;
						unset($data[$fieldName]);
					}
				}
			}
		}
		if($serialize) {
			foreach($data as $fieldName => $value) {
				$definition = $this->getDefinition($fieldName);
				if(!$definition) continue;
				if($definition['field_type'] === 'loop') {
					$data[$fieldName] = serialize($value);
				}
			}
		}
		return $data;
	}

	/**
	 * getDefinition
	 * @param string $fieldName
	 * @return false|mixed
	 */
	public function getDefinition($fieldName)
	{
		if(empty($this->definitions)) {
			return false;
		}
		foreach($this->definitions as $definition) {
			if($fieldName === $definition['field_name']) {
				return $definition;
			}
		}
		return false;
	}

}
