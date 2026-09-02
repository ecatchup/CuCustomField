<?php
namespace CuCustomField\Event;

use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Paging\PaginatedResultSet;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\ResultSet;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Event
 * @license          MIT LICENSE
 */

/**
 * Class CuCustomFieldControllerEventListener
 *
 * @property CuCustomFieldDefinition $CuCustomFieldDefinitionModel
 * @uses CuCustomFieldControllerEventListener
 */
class CuCustomFieldControllerEventListener extends \BaserCore\Event\BcControllerEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = [
		'initialize',
        'beforeRender',
		// 'startup' => ['priority' => 1],	// CuApproverControllerEventListener::start() より早く
	];

	/**
	 * cu_custom_fieldヘルパー
	 *
	 * @var CuCustomFieldHelper
	 */
	public $CuCustomField = null;

	/**
	 * cu_custom_field設定情報
	 *
	 * @var array
	 */
	public $cuCustomFieldConfigs = [];

	/**
	 * cu_custom_fieldモデル
	 *
	 * @var Object
	 */
	public $CuCustomFieldValueModel = null;

	/**
	 * cu_custom_field設定モデル
	 *
	 * @var Object
	 */
	public $CuCustomFieldConfigModel = null;

	/**
	 * cu_custom_fieldフィールド名設定データ
	 *
	 * @var array
	 */
	public $settingsCuCustomField = [];

	/**
	 * initialize
	 *
	 * @param \Cake\Event\Event $event
	 */
	public function initialize(\Cake\Event\Event $event)
	{
		$Controller = $event->getSubject();
		// CuCustomFieldヘルパーの追加
		$Controller->viewBuilder()->addHelpers(['CuCustomField.CuCustomField']);
		$this->settingsCuCustomField = \Cake\Core\Configure::read('cuCustomField');
        $this->setUpModel();
	}

    /**
     * beforeRender
     *
     */
    public function beforeRender(\Cake\Event\Event $event)
    {
        $Controller = $event->getSubject();
        if($Controller->getPlugin() !== 'BcBlog'){
            return;
        }

        if($Controller->getRequest()->getParam('action') === 'index'){
            // 一覧の場合（$posts）
            $posts = $Controller->viewBuilder()->getVar('posts');

            if(!$posts || $posts->isEmpty()){
                return;
            }

            if (BcUtil::isAdminSystem()) {
                $this->setupCustomFieldValueForAdmin($event, $posts->first());
            }else{
                $this->setupCustomFieldValueForFront($event, $posts);
            }

            $Controller->viewBuilder()->setVar('posts', $posts);
        }else{
            // 詳細等の場合（$post）
            $post = $Controller->viewBuilder()->getVar('post');

            if(!$post){
                return;
            }

            if (BcUtil::isAdminSystem()) {
                $this->setupCustomFieldValueForAdmin($event, $post);
            }else{
                $this->setupCustomFieldValueForFront($event, [$post]);
            }

            $Controller->viewBuilder()->setVar('post', $post);
        }

    }

	/**
	 * モデル初期化：CuCustomFieldValueModel, CuCustomFieldConfig
	 *
	 * @return void
	 */
	private function setUpModel()
	{

        $this->CuCustomFieldValueModel = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValues');
		$this->CuCustomFieldValueModel->addBehavior('CuCustomField.KeyValue', ['foreignKeyField' => 'relate_id']);

        $this->CuCustomFieldConfigModel = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldConfigs');
	}

    /**
    *   [PUBLIC]公開側ページのカスタムフィールド紐付け処理
    *
    *   @param array<EntityInterface> $posts ブログ記事
    **/
    public function setupCustomFieldValueForFront(Event $event, $posts)
	{
		foreach($posts as $key => $value) {
			// KeyValue 側のモデル情報をリセット
			//$this->CuCustomFieldValueModel->setupBehavior($this->CuCustomFieldValueModel);

			// カスタムフィールドの設定情報を取得するため、記事のブログコンテンツIDからカスタムフィールド側のコンテンツIDを取得する
			if (isset($value['blog_content_id'])) {
				$contentId = $value['blog_content_id'];
			} else {
				$contentId = $value->blog_content->id;
			}
			$configData = $this->hasCustomFieldConfigData($contentId);
			if (!$configData) {
				continue;
			}

			if ($configData['status']) {
				$data = $this->CuCustomFieldValueModel->getSection($value['id'], $this->CuCustomFieldValueModel);
				if ($data) {
					// カスタムフィールドデータを結合
					$value[$this->CuCustomFieldValueModel->getAlias()] = $data;
				}
			}
		}
		if(!empty($contentId)) {
			$this->CuCustomFieldValueModel->setup($contentId);
		}
	}

    /**
     * 保存データを安全にunserializeする
     * - 保存時（KeyValueBehavior::saveSection）はaddslashesしていないため、まず生データでunserializeを試みる
     * - 過去にstripslashesを前提として保存されたデータのために、失敗時のみstripslashes版でも試みる
     * - どちらも失敗した場合はWarningを出さず空配列を返す
     *
     * @param mixed $data
     * @return mixed
     */
    private function safeUnserialize($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        $result = @unserialize($data);
        if ($result !== false || $data === 'b:0;') {
            return $result;
        }
        $result = @unserialize(stripslashes($data));
        if ($result !== false || stripslashes($data) === 'b:0;') {
            return $result;
        }
        return [];
    }

    // ADMIN
    private function setupCustomFieldValueForAdmin(Event $event, EntityInterface $post)
    {
        $Controller = $event->getSubject();
        $plugin = Router::getRequest()->getParam('plugin');
        $controller = Router::getRequest()->getParam('controller');
        $action = Router::getRequest()->getParam('action');

        // ブログ記事の場合のみ処理を行う
        if ($plugin !== 'BcBlog') return;
        if ($controller !== 'BlogPosts') return;

        if(!empty($contentId)) {
            $this->CuCustomFieldValueModel->setup($contentId);
        }
        // カスタムフィールドの設定情報を取得するため、記事のブログコンテンツIDからカスタムフィールド側のコンテンツIDを取得する
        if (isset($post->blog_content_id)) {
            $contentId = $post->blog_content_id;
        } else {
            $contentId = $post->blog_content->id;
        }
        $configData = $this->hasCustomFieldConfigData($contentId);

        if (!empty($configData->status)) {
            $Controller->viewBuilder()->setVar('cuCustomFieldConfig', $configData);
            // 記事編集のみdataを取得
            if (!empty($post->id)) {
                $data = $this->CuCustomFieldValueModel->getSection($post['id'], $this->CuCustomFieldValueModel);
                // カスタムフィールドデータを結合
                if (!empty($data)) $value[$this->CuCustomFieldValueModel->getAlias()] = $data;
            }
        };
        // カスタムフィールド定義情報を取得
        $definitions = $this->CuCustomFieldValueModel->getFieldDefinition($contentId, '', true);
        if ($definitions) {
            // Joinで所得しているため階層構造に変更
            $definitionArrays = [];
            $loops = [];
            if (!empty($definitions)) {
                foreach ($definitions as $definition) {
                    $definitionData = [];
                    if (is_object($definition) && isset($definition->CuCustomFieldDefinitions) && is_array($definition->CuCustomFieldDefinitions)) {
                        $definitionData = $definition->CuCustomFieldDefinitions;
                    } elseif (is_array($definition) && isset($definition['CuCustomFieldDefinitions']) && is_array($definition['CuCustomFieldDefinitions'])) {
                        $definitionData = $definition['CuCustomFieldDefinitions'];
                    }
                    if (!$definitionData) {
                        continue;
                    }
                    // ループの field_name を配列に格納
                    if (($definitionData['field_type'] ?? null) == 'loop') $loops[] = $definitionData['field_name'] ?? null;
                    // ループの配下のフィールド設定をループ配列に格納
                    if (!empty($definitionData['parent_id'])) {
                        $parentId = $definitionData['parent_id'];
                        if (!isset($definitionArrays[$parentId])) {
                            $definitionArrays[$parentId] = ['CuCustomFieldDefinitions' => ['children' => []]];
                        }
                        if (!isset($definitionArrays[$parentId]['CuCustomFieldDefinitions']['children'])) {
                            $definitionArrays[$parentId]['CuCustomFieldDefinitions']['children'] = [];
                        }
                        $definitionArrays[$parentId]['CuCustomFieldDefinitions']['children'][] = $definition;
                    } else {
                        if (!empty($definitionData['id'])) {
                            $definitionArrays[$definitionData['id']] = is_array($definition) ? $definition : ['CuCustomFieldDefinitions' => $definitionData];
                        }
                    }
                }
                // Config情報に紐づく取得方法のため並び替えができない。
                // そのため、lftをkeyにしてksortする。
                if (!empty($definitionArrays)) {
                    $definitions = [];
                    foreach ($definitionArrays as $definitionArray) {
                        $definitionData = (array) ($definitionArray['CuCustomFieldDefinitions'] ?? []);
                        if (isset($definitionData['lft'])) {
                            $definitions[$definitionData['lft']] = $definitionArray;
                        }
                    }
                    ksort($definitions);
                }
            }
            $Controller->viewBuilder()->setVar('definitions', $definitions);
        }

        switch($action) {
            case 'index':
                break;

            case 'add':
                break;

            case 'edit':
                $datas = $this->CuCustomFieldValueModel->getSection($post['id'], $this->CuCustomFieldValueModel);
                if ($datas) {
                    $fieldData = [];

                    foreach ($datas as $field_name => $data) {
                        // ループフィールド内は unserializeする
                        if (!empty($loops) && in_array($field_name, $loops)) {
                            // シリアライズされているかどうかの判定（未保存・不正データの場合はunserializeしない）
                            if (is_string($data) && strpos($data, 'a:') === 0 && str_ends_with($data, '}') !== false) {
                                $data = $this->safeUnserialize($data);
                            } else {
                                $data = [];
                            }
                            // ループ内のフィールドもシリアライズされていれば unserializeする
                            $children = [];
                            if (!empty($data) && is_array($data)) {
                                foreach ($data as $datakey => $dataValue) {
                                    // シリアライズされているかどうかの判定
                                    if (is_string($dataValue) && strpos($dataValue, 'a:') === 0 && str_ends_with($dataValue, '}') !== false) {
                                        $children[$datakey] = $this->safeUnserialize($dataValue);
                                    } else {
                                        $children[$datakey] = $dataValue;
                                    }
                                }
                            }
                            $fieldData[$field_name] = $children;
                        } else {
                            // シリアライズされているかどうかの判定
                            if (is_string($data) && strpos($data, 'a:') === 0 && str_ends_with($data, '}') !== false) {
                                //debug($data);
                                $fieldData[$field_name] = $this->safeUnserialize($data);
                            } else {
                                $fieldData[$field_name] = $data;
                            }
                        }
                    }

                    // カスタムフィールドデータを結合
                    $value[$this->CuCustomFieldValueModel->getAlias()] = $fieldData;
                    $post['CuCustomFieldValue'] = $fieldData;
                }
                break;

            case 'admin_preview':
                $data = $this->CuCustomFieldValueModel->getSection($post->id, $this->CuCustomFieldValueModel->name);
                if ($data) {
                    $post['CuCustomFieldValue'] = $data;
                    // $event->data[0][0][$this->CuCustomFieldValueModel->name] = $data;
                }
                break;

            case 'admin_ajax_copy':
                break;

            default:
                break;
        }
        return;
    }
    private function hasCustomFieldConfigData($contentId)
	{
		$data = $this->CuCustomFieldConfigModel->find('all', [
			'conditions' => [
				'CuCustomFieldConfigs.content_id' => $contentId,
				'CuCustomFieldConfigs.model' => 'BlogContent',
			],
		])->first();
		return $data;
	}
}
