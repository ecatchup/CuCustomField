<?php
namespace CuCustomField\Model\Behavior;

use BaserCore\Utility\BcUtil;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use BaserCore\Model\Behavior\BcKeyValueBehavior;
use BaserCore\Utility\BcFileUploader;
use Laminas\Diactoros\UploadedFile;


/**
 * KeyValue Behavior
 *
 * TODO: long text? separate table or not at all?
 * TODO: caching
 *
 * @license MIT
 * @modified Mark Scherer
 */
#[\AllowDynamicProperties]
class KeyValueBehavior extends Behavior {

	/**
	 * Storage model for all key value pairs
	 */
	public $KeyValue = null;

	/**
	 * Default settings
	 *
	 * @var array
	 */
	protected array $_defaultConfig = array(
		'foreignKeyField' => 'relate_id',   // 決め打ち
		'keyField' => 'key',
		'valueField' => 'value',
		'defaults' => null, // looks for `public $keyValueDefaults` property in the model,
		'validate' => null, // looks for `public $keyValueValidate` property in the model
		'defaultOnEmpty' => false, // if nothing is posted, delete (0 is not nothing)
		'deleteIfDefault' => false, // if default value is posted, delete
	);

	/**
	 * Setup
	 *
	 * @param object AppModel
	 * @param array $config
	 */
	public function setup(Table $Model, $config = array()) {
		$config += $this->_defaultConfig;
		$this->settings[$Model->getAlias()] = $config;
		if (!$this->KeyValue) {
			$this->KeyValue = $Model;
			//$this->KeyValue = \Cake\ORM\TableRegistry::getTableLocator()->get('Tools.KeyValue');
		}
		/*
		if ($this->settings[$Model->getAlias()]['validate']) {
			foreach ($this->settings[$Model->getAlias()]['validate'] as $key => $validate) {
				$this->KeyValue->validate[$key] = $validate;
			}
		}
		*/
	}

	/**
	 * Returns details for named section
     * @param string $foreignKey
     * @param Table $Model
     * @param string $section
     * @param string $key
	 *
	 * @var string
	 * @var string
	 * @return mixed Flat array or direct value
	 */
	public function getSection($foreignKey, Table $Model, $section = null, $key = null) {
        // BlogPost新規作成時はデータがないため終了
        if(!$foreignKey){
            return[];
        }

        // $this->>settigsを確認
        $this->settingsCheck($Model);

		extract($this->settings[$Model->getAlias()]);

		$results = $this->KeyValue->find('all', array(
			'conditions' => array($foreignKeyField => $foreignKey),
			'fields' => array('key', 'value')
		))
        ->all();

		$defaultValues = $this->defaultValues($Model);

		$detailArray = array();
		foreach ($results as $value) {
            if(!isset($value['key'])) continue;
			$keyArray = preg_split('/\./', $value['key'], 2);
			$detailArray[$keyArray[0]][$keyArray[1]] = $value['value'];
		}

		foreach ($defaultValues as $model => $values) {
			foreach ($values as $valueKey => $val) {
				if (isset($detailArray[$model][$valueKey])) {
					continue;
				}
				$detailArray[$model][$valueKey] = $val;
			}
		}
		if (empty($detailArray)) {
			return [];
		}
		if ($section === null) {
			return $detailArray[$model];
		}
		if (empty($detailArray[$section])) {
			return array();
		}
		if ($key === null) {
			return $detailArray[$section];
		}
		if (!isset($detailArray[$section][$key])) {
			return null;
		}
		return $detailArray[$section][$key];
	}

	/**
	 * Save details for named section
	 *
	 * TODO: validate
	 *
	 * @var string
	 * @var array
	 * @var string
	 * @return bool Success
	 */
	public function saveSection(Table $Model, $foreignKey, $data, $section = null, $validate = true) {
		if ($validate && !$this->validateSection($Model, $data)) {
			return false;
		}
        // $this->>settigsを確認
        $this->settingsCheck($Model);

		extract($this->settings[$Model->getAlias()]);

        // $dataがオブジェクトではない場合があるため、デフォルトで配列に変更する。
        if (!is_array($data)) $data = $data->toArray();

        //$this->KeyValue->clear();
		foreach ($data as $model => $details) {
			if ($section && $section !== $model) {
				continue;
			}

            //TODO: ループ項目に対応できていない（パートナー企業の声>記事項目など）

            // ファイル関係のフィールドを1つにまとめる(_, _delete等)
            foreach($details as $key => $value){
                if($value instanceof UploadedFile){
                    $replacedValue = ['file' => $value];
                    if(isset($details[$key . '_'])){
                        $replacedValue['oldFileName'] = $details[$key . '_'];
                        unset($details[$key . '_']);
                    }
                    if(isset($details[$key . '_delete'])){
                        $replacedValue['isDelete'] = $details[$key . '_delete'] != '0';
                        unset($details[$key . '_delete']);
                    }
                    $details[$key] = $replacedValue;
                }
            }
            // BlogContentIdを配列・オブジェクトどちらでも取得できるようにする。
            if (is_array($data) && !empty($data['blog_content_id'])) {
                $blog_content_id = $data['blog_content_id'];
            } else {
                if (!empty($data->blog_content_id)) {
                    $blog_content_id = $data->blog_content_id;
                } else {
                    $blog_content_id = $data['data']['blog_content_id'];
                }
            }


			foreach ($details as $field => $value) {
				$newDetail = array();
				$section = $section ? $section : $model;
				$key = $section . '.' . $field;

                // ループフィールドでファイルアップロードフィールドがある場合
                if (is_array($value) && empty($value['file'])) {
                    foreach ($value as $loopkey => $loopOne) {
                        if ($loopkey == '__loop-src__') continue;
                        if (is_array($loopOne)) {
                            foreach ($loopOne as $valKey => $loopValue) {
                                //$newValue[$valKey] = $loopValue;
                                // ループ内のファイルフィールドで既に画像が登録されている場合
                                if (isset($loopOne[$valKey.'_delete']) && isset($loopOne[$valKey.'_'])) {
                                    // 削除にチェックが入っっておらず、新しいファイルが選択されていない場合、既存のファイルを使用する
                                    if ($loopOne[$valKey.'_delete'] == '0' && empty($loopValue->getClientFileName())) {
                                        $loopOne[$valKey] = $loopOne[$valKey.'_'];
                                        $value[$loopkey][$valKey] = $loopOne[$valKey.'_'];
                                        unset($value[$loopkey][$loopOne[$valKey.'_delete']]);
                                        unset($value[$loopkey][$loopOne[$valKey.'_']]);
                                        continue;
                                    }
                                }
                                // ファイルの新規登録
                                if (is_object($loopValue)) {
                                    $newValue['file'] = $loopValue;
                                    $newFileName = $this->saveCuCfFileData($blog_content_id, $newValue);
                                    $value[$loopkey][$valKey] = $newFileName;
                                }
                            }
                        }

                    }
                }

                // ファイルの場合
                // コピーの場合ファイルのパスだけがコピーされてしまうため、nullにする
                if (is_string($value) && strpos($value, 'BlogPost/') !== false) {
                    if (!empty(preg_match('/BlogPost\/[0-9]*\/[0-9]*\/[0-9]*\/.*?\..*[a-zA-Z]/', $value))) $value = null;
                }
                if(isset($value['file'])){
                    $value = $this->saveCuCfFileData($blog_content_id, $value);
                }
				if ($defaultOnEmpty && (string)$value === '' || $deleteIfDefault && (string)$value === (string)$this->defaultValues($Model, $section, $field)) {
					return $this->resetSection($Model, $foreignKey, $section, $field);
				}
				$tmp = $this->KeyValue->find('all')
                    ->where([$foreignKeyField => $foreignKey, $keyField => $key])
                    ->select(['id'])
                    ->first();

                // チェックボックスなど配列の場合
                if (is_array($value) && empty($value['file'])) {
                    // ループフィールドは追加ソースのinput（__loop-src__）が付くので unset しておく
                    if (isset($value['__loop-src__'])) unset($value['__loop-src__']);
                    // 保存前にシリアライズする
                    //debug($value);
                    $value = serialize($value);
                }
                //debug($value);

                $newDetail[$foreignKeyField] = $foreignKey;
				$newDetail[$keyField] = $key;
				$newDetail[$valueField] = $value;
				$newDetail['model'] = $Model->getAlias();
                if ($tmp) {
                    $entity = $this->KeyValue->patchEntity($tmp, $newDetail);
				} else {
                    $entity = $this->KeyValue->newEntity($newDetail);
                    // $entityにmodelカラムが入らない
                    $entity->model = $Model->getAlias();
                    // $entityにcreatedカラムが入らない
                    $entity->created = date('Y-m-d H:i:s');
                }
                // $entityにmodifiedカラムが入らない
                $entity->modified = date('Y-m-d H:i:s');
                $eventManager = $this->KeyValue->getEventManager();
                $beforeSaveListeners = BcUtil::offEvent($eventManager, 'Model.beforeSave');
                $afterSaveListeners = BcUtil::offEvent($eventManager, 'Model.afterSave');

				$this->KeyValue->save($entity);

                BcUtil::onEvent($eventManager, 'Model.beforeSave', $beforeSaveListeners);
                BcUtil::onEvent($eventManager, 'Model.afterSave', $afterSaveListeners);
			}
		}
		return true;
	}

    /**
     * アップロードファイルを保存できるデータ型にする
     *
     * @param int $blog_content_id
     * @param array $value
     * @return string
     */
    public function saveCuCfFileData(int $blog_content_id, array $value) {
        // 削除チェックボックスの確認
        if(isset($value['isDelete']) && $value['isDelete']){
            $this->table()->deleteCuCfFile($value['oldFileName']);
            // ファイルが選択されていない場合は、パスも削除
            if ($value['file']->getError() !== 0) {
                if (empty($value['file']->getClientFileName())) {
                    $newFileName = $this->table()->saveCuCfFile($blog_content_id, $value['file'], '');
                    return $newFileName;
                }
            }
        }
        // $uploadMaxFilesize = ini_get('upload_max_filesize');
        // $uploadMaxFilesize = rtrim($uploadMaxFilesize, 'KMG');
        // debug($uploadMaxFilesize * 1024);
        // debug($value['file']->getSize());
        // debug($value['file']->getClientFileName());
        if (!empty($value['file']->getClientFileName()) && $value['file']->getSize() === 0) {
            $value = 'ファイルアップロードに失敗しました。';
            return false;
        }

        if($value['file']->getError() !== 0){
            // アップロードされていない場合、前回の値、無ければnullとして保存する
            $value = $value['oldFileName'] ?? null;
        } else {
            // ファイル保存して新しいファイル名(uuid)を保存する
            // debug($value['file']->getClientFileName());
            if (!empty($value['file']->getClientFileName())) {
                $newFileName = $this->table()->saveCuCfFile($blog_content_id, $value['file'], $value['file']->getClientFileName());
                $value = $newFileName;
            }
        }
        return $value;
    }

	/**
	 * @return bool Success
	 */
	public function validateSection(Table $Model, $data, $section = null) {
        // $this->>settigsを確認
        $this->settingsCheck($Model);
		$validate = $this->settings[$Model->getAlias()]['validate'];
		if ($validate === null) {
			$validate = 'keyValueValidate';
		}
		if (empty($Model->{$validate})) {
			return true;
		}
		$rules = $Model->{$validate};
		$res = true;
		foreach ($data as $model => $array) {
			if ($section && $section !== $model) {
				continue;
			}
			if (empty($rules[$model])) {
				continue;
			}
			$this->KeyValue->{$model} = \Cake\ORM\TableRegistry::getTableLocator()->get(array('class' => 'AppModel', 'alias' => $model, 'table' => false));
			$this->KeyValue->{$model}->validate = $rules[$model];
			$this->KeyValue->{$model}->set($array);
			$res = $res && $this->KeyValue->{$model}->validates();
		}
		return $res;
	}

	/**
	 * KeyValueBehavior::defaultValues()
	 *
	 * @param Model $Model
	 * @param mixed $section
	 * @param mixed $key
	 * @return array
	 */
	public function defaultValues(Table $Model, $section = null, $key = null) {
        // $this->>settigsを確認
        $this->settingsCheck($Model);
		$defaults = $this->settings[$Model->getAlias()]['defaults'];
		if ($defaults === null) {
			$defaults = 'keyValueDefaults';
		}
		$defaultValues = array();
		if (!empty($Model->{$defaults})) {
			$defaultValues = $Model->{$defaults};
		}
		if ($section !== null) {
			if ($key !== null) {
				return isset($defaultValues[$section][$key]) ? $defaultValues[$section][$key] : null;
			}
			return isset($defaultValues[$section]) ? $defaultValues[$section] : null;
		}
		return $defaultValues;
	}

	/**
	 * Resets the custom data for the specific domains (model, foreign_id)
	 * careful: passing both null values will result in a complete truncate command
	 *
	 * @return bool Success
	 */
	public function resetSection(Table $Model, $foreignKey = null, $section = null, $key = null) {
        $this->settingsCheck($Model);
		extract($this->settings[$Model->getAlias()]);
		$conditions = array();
		if ($foreignKey !== null) {
			$conditions[$foreignKeyField] = $foreignKey;
		}
		if ($section !== null) {
			if ($key !== null) {
				$conditions[$keyField] = $section . '.' . $key;
			} else {
				$conditions[$keyField . ' LIKE'] = $section . '.%';
			}
		}
		if (empty($conditions)) {
			return $this->KeyValue->truncate();
		}
		return (bool)$this->KeyValue->deleteAll($conditions, false);
	}

    /**
	 * settingsが空の場合に再設定する
	 *
	 * @return void
	 */
    public function settingsCheck($Model, $config = [])
    {
        if(!isset($this->settings) || empty($this->settings)){
            $this->setup($Model, $config);
        }
    }
}
