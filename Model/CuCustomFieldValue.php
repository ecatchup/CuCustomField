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
 * Class CuCustomFieldValue
 *
 * KeyValueBehavior を利用しているため、beforeSave / afterSave は呼び出されない
 * そのためこのクラスでは実装しないこと
 * 他の Behavior で上記イベントを実装できるが、CuCustomFieldModelEventListener より dispatch している
 */
class CuCustomFieldValue extends CuCustomFieldAppModel
{

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = [
		'CuCustomField.KeyValue' => [
			'foreignKeyField' => 'relate_id'
		]
	];

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
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = [])
	{
		$this->data['CuCustomFieldValue'] = $this->autoConvert($this->data['CuCustomFieldValue']);
		// 新規登録時、このタイミングで $this->>data['BlogPost']['no'] に新しいデータが入っていないため実体より取得
		$blogPostModel = ClassRegistry::init('Blog.BlogPost');
		if(!empty($blogPostModel->data['BlogPost']['id'])) {
			$this->data['CuCustomFieldValue']['id'] = $blogPostModel->data['BlogPost']['id'];
		}
		if(!empty($blogPostModel->data['BlogPost']['no'])) {
			$this->data['CuCustomFieldValue']['no'] = $blogPostModel->data['BlogPost']['no'];
		}
		return parent::beforeSave($options);
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
		$fieldConfig = Hash::extract($this->fieldConfig, '{n}.CuCustomFieldDefinition[field_name=' . $fieldName . ']');
		$validateRegex = Hash::extract($fieldConfig, '{n}.validate_regex');
		if (preg_match($validateRegex[0], $check[key($check)])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * フィールド定義を取得する
	 * @param $relateId
	 * @param $fieldName
	 * @return false|mixed
	 */
	public function getFieldDefinition($contentId, $fieldName = '')
	{
		/* @var CuCustomFieldConfig $$CustomFieldConfig */
		$CustomFieldConfig = ClassRegistry::init('CuCustomField.CuCustomFieldConfig');
		$config = $CustomFieldConfig->find('first', [
				'conditions' => [
					'CuCustomFieldConfig.content_id' => $contentId,
					'CuCustomFieldDefinition.status' => true
				],
				'joins' => [
					[
						'table' => 'cu_custom_field_definitions',
						'alias' => 'CuCustomFieldDefinition',
						'type' => 'inner',
						'conditions' => [
							'CuCustomFieldDefinition.config_id = CuCustomFieldConfig.id'
						]
					]
				],
				'recursive' => 1]
		);
		if (is_array($config) && empty($config['CuCustomFieldDefinition'])) {
			return false;
		}
		if ($fieldName) {
			if(strpos($fieldName, '.') !== false) {
				list(, $fieldName) = explode('.', $fieldName);
			}
			foreach($config['CuCustomFieldDefinition'] as $definition) {
				if ($definition['field_name'] === $fieldName) {
					return $definition;
				}
			}
			return false;
		} else {
			return $config['CuCustomFieldDefinition'];
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
			$this->publicFieldConfigData[$contentId] = Hash::combine($definition, '{n}.field_name', '{n}');
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
