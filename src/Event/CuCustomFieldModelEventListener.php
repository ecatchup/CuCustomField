<?php
namespace CuCustomField\Event;

use BaserCore\Event\BcModelEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Exception;
use CuCustomField\Model\Table\CuCustomFieldValuesTable;

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
 * Class CuCustomFieldModelEventListener
 */
#[\AllowDynamicProperties]
class CuCustomFieldModelEventListener extends BcModelEventListener
{

    /**
     * 登録イベント
     *
     * @var array
     */
    public $events = [
        'BcBlog.BlogContents.beforeFind',
        'BcBlog.BlogPosts.beforeFind',
        'BcBlog.BlogPosts.afterFind',
        'BcBlog.BlogPosts.afterSave',
        'BcBlog.BlogPosts.beforeDelete',
        'BcBlog.BlogPosts.beforeCopy',
        'BcBlog.BlogPosts.afterCopy',
        'BcBlog.BlogPosts.beforeValidate',
        //'BcBlog.BlogContents.afterDelete',
    ];


    /**
     * ブログ記事多重保存の判定
     *
     * @var boolean
     */
    private $throwBlogPost = false;

    /**
     * ループを平データで取得するモード
     *
     * @var bool
     */
    public $findFlatteningMode = false;


    /**
     * モデル初期化：CuCustomFieldValues, CuCustomFieldConfig
     *
     * @return void
     */
    private function setUpModel()
    {

         $this->CuCustomFieldValues = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValues');
         $this->CuCustomFieldValues->addBehavior('CuCustomField.KeyValue');

         $this->CuCustomFieldConfigs = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldConfigs');
         $this->CuCustomFieldConfigs->hasMany('CuCustomFieldDefinitions', [
            'className' => 'CuCustomField.CuCustomFieldDefinitions',
            'sort' => ['CuCustomFieldDefinitions.lft' => 'DESC']
        ])
            ->setForeignKey('config_id');
    }
    /**
     * Construct
     *
     */
    public function __construct()
    {
        parent::__construct();

        $cuCustomFieldConfigs = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldConfigs');
        $cuCustomFieldConfigs->hasMany('CuCustomFieldDefinitions', [
            'className' => 'CuCustomField.CuCustomFieldDefinitions',
            'sort' => ['CuCustomFieldDefinitions.lft' => 'DESC']
        ])
            ->setForeignKey('config_id');

        $blogPosts = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
        $blogPosts->hasMany('CuCustomFieldValues', ['className' => 'CuCustomField.CuCustomFieldValues'])
            ->setForeignKey('relate_id');

        $blogContents = TableRegistry::getTableLocator()->get('BcBlog.BlogContents');
        $blogContents->hasOne('CuCustomFieldConfigs', ['className' => 'CuCustomField.CuCustomFieldConfigs'])
            ->setForeignKey('content_id');
    }

    /**
     * bcBlogBlogContentsBeforeFind
     *
     * @param Event $event
     */
    public function bcBlogBlogContentsBeforeFind(Event $event)
    {
        // ブログ設定取得の際にカスタム設定情報も併せて取得する
        if($event->getData(0) instanceof \Cake\ORM\Query\SelectQuery) {
            $event->getData(0)->contain(['CuCustomFieldConfigs']);
        }
    }

    /**
     * blogBlogPostBeforeFind
     * 最近の投稿、ブログ記事前後移動を find する際に実行
     *
     * @param Event $event
     * @return array
     */
     public function bcBlogBlogPostsBeforeFind(Event $event)
     {
        $request = Router::getRequest();
        if ($event->getData(0) == null) return;
         $Model = $event->getSubject();
         $this->setUpModel();
         $data = $event->getData();

         if ($request->getParam('controller') === 'BlogPosts' &&
             in_array($request->getParam('action'), ['add', 'batch'])) {
             return;
         }
         // ブログ記事の際にカスタムフィールドも併せて取得する
         if($event->getData(0) instanceof \Cake\ORM\Query\SelectQuery) {
             $event->getData(0)->contain(['CuCustomFieldValues']);
         }

        if (BcUtil::isAdminSystem()) {
            return $event->getData(0);
        }

         // 最近の投稿、ブログ記事前後移動を find する際に実行
         // TODO get_recent_entries に呼ばれる find 判定に、より良い方法があったら改修する
//         debug($event->getData(0));
//         if (is_array($event->data[0]['fields']) && count($event->data[0]['fields']) === 2) {
//          if (($event->data[0]['fields'][0] == 'no') && ($event->data[0]['fields'][1] == 'name')) {
//              $event->data[0]['fields'][] = 'id';
//              $event->data[0]['fields'][] = 'posts_date';
//              $event->data[0]['fields'][] = 'blog_category_id';
//              $event->data[0]['fields'][] = 'blog_content_id';
//              $event->data[0]['recursive'] = 2;
//          }
//      }
        $customSearch = \Cake\Core\Configure::read('cuCustomFieldConfig.customSearch');
        if(isset($event->data[0]['customSearch']) && $event->data[0]['customSearch'] === false) {
            $customSearch = false;
        }
        if ($request->getQuery() && $customSearch) {
            // keyのリストを取得
            $keyArray = $this->getKeyList();
            $searchQuery = [];

            // クエリの判定
            foreach ($request->query as $key => $query) {
                if($key === 'preview') { // プレビューかどうかの判定
                    continue;
                }
                // like検索の場合はkey:likeがついている
                $checkKey = preg_replace('/\:like$/', '', $key);
                // クエリがCuCustomFieldで使用されているkeyに含まれていれば$searchQueryの配列に追加
                if(in_array($checkKey, $keyArray)) {
                    $searchQuery[$key] = $query;
                }
            }

            // $searchQueryにクエリが追加されていれば、処理を実行
            if (!empty($searchQuery)) {
                $Model->bindModel(['hasMany' => [
                    'CuCustomFieldValue' => [
                        'className' => 'CuCustomField.CuCustomFieldValue',
                        'order' => 'id',
                        'foreignKey' => 'relate_id',
                    ]
                ]], false);
                // if (!empty($searchQuery)) {
                //     $event->data[0] = $this->customSearchQuery($event->data[0], $searchQuery);
                // }
            }
        }
        return $event;
     }

    public function customSearchQuery($query, $get)
    {
        $conditions = [];
        if (!empty($query['conditions'])) {
            $conditions = $query['conditions'];
        }
        foreach($get as $key => $value) {
            if($value && !is_array($value)) {
                // key:likeがついていればlike検索
                if (preg_match('/^([^\:]+?)\:like$/', $key, $matches)) {
                    $conditions['or'][] = [
                        'key' => 'CuCustomFieldValue.' . $matches[1],
                        'value LIKE' => '%' . $value . '%'
                    ];
                } else {
                    $conditions['or'][] = [
                        'key' => 'CuCustomFieldValue.' . $key,
                        'value' => $value // 完全一致検索
                    ];
                }
            }
        }
        $query['conditions'] = $query['conditions'] ? array_merge_recursive($query['conditions'], $conditions) : $conditions;
        $query['joins'][] = [
            'table' => 'cu_custom_field_values',
            'alias' => 'CuCustomFieldValue',
            'type' => 'left',
            'conditions' => [
                'BlogPost.id = CuCustomFieldValue.relate_id'
            ]
        ];
        if ($query['fields']) {
            if (is_array($query['fields'])) {
                $query['fields'][0] = 'DISTINCT ' . $query['fields'][0];
            } else {
                $query['fields'] = 'DISTINCT ' . $query['fields'];
            }
        } else {
            $query['fields'] = 'DISTINCT BlogPost.*';
        }
        return $query;
    }

    /**
     * CuCustomFieldで使用されているkeyのリストを取得（クエリの判定等で使用）
     *
     * @return array
     */
    private function getKeyList()
    {
        $CuCustomFieldDefinitionModel = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldDefinitions');
        $list = $CuCustomFieldDefinitionModel->find('all', [
            'fields' => ['field_name'],
            'conditions' => [
                'CuCustomFieldDefinition.status' => 1,
            ],
        ])
        ->toList();
        return $list;
    }

    /**
     * blogBlogPostAfterFind
     * ブログ記事取得の際にカスタムフィールド情報も併せて取得する
     *
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function bcBlogBlogPostsAfterFind(\Cake\Event\Event $event)
    {
        $Model = $event->getSubject();
        $request = Router::getRequest();
        $data = $event->getData();
        $pass = $request->getParam('pass');
        // BlogContentIdを取得
        $blogContentId = 1;
        if (!empty($request->getParam('pass'))) {
            $blogContentId = $request->getParam('pass')[0];
        }
        $this->setUpModel();

        if (empty($data)) {
            return;
        }

         $data = $data['result']->all();

        // 管理画面側の処理
        if (\BaserCore\Utility\BcUtil::isAdminSystem()) {
            // ブログ記事の場合のみ処理を行う
            if ($request->getParam('plugin') !== 'BcBlog') return;
            if ($request->getParam('controller') !== 'BlogPosts')  return;

            if(!empty($pass[0])) {
                $this->CuCustomFieldValues->setup($pass[0]);
            }

            $configData = $this->hasCustomFieldConfigData($pass[0]);
            if ($configData) $event->getData('cuCustomFieldConfig', $configData);

             // definitions をセット
            $fieldDefinition = $this->CuCustomFieldValues->getFieldDefinition($blogContentId);
            if ($fieldDefinition) $event->getData('definitions', $fieldDefinition);

         switch($request->getParam('action')) {
             case 'index':
                // 一覧でカスタムフィールド項目を表示しないので処理終了
                 break;

             case 'add':
                 break;

             case 'edit':
                    if($event->getData(0) instanceof \Cake\ORM\Query\SelectQuery) {
                        $event->getData(0)->contain(['CuCustomFieldValues']);
                    }
                   $fieldDefinition = ($this->CuCustomFieldValues->getFieldDefinition($blogContentId));
                   $data = $this->CuCustomFieldValues->getSection($pass[1], $this->CuCustomFieldValues);
                   if ($fieldDefinition) $event->data[0][0]['definitions'] = $fieldDefinition;;
                 break;

             case 'preview':
                   $data = ($this->CuCustomFieldValues->getFieldDefinition($blogContentId));
                 //$data = $this->CuCustomFieldValues->getSection($blogPostId, $this->CuCustomFieldValues->name);
                 if ($data) {
                     $event->data[0][0]['definitions'] = $data;
                 }
                 break;

             case 'ajax_copy':
                 break;

             default:
                 break;
         }
         if($this->findFlatteningMode) {
             // findFlatteningMode が true に設定されていれば、一回のみ平データで取得
             // 公開承認の草稿モードの保存で本稿を元データに書き戻すために利用
             // CuApproverApplicationBehavior::getPublish() 内の find() にて利用
             if(!empty($event->data[0][0]['CuCustomFieldValue'])) {
                 $event->data[0][0]['CuCustomFieldValue'] = $this->CuCustomFieldValues->convertToFlatteningData($event->data[0][0]['CuCustomFieldValue']);
             }
             $this->findFlatteningMode = false;
         }
            return $event->getData(0);
        }

        // 公開側の処理
        if (empty($data)) return;
        // ブログ設定取得の際にカスタム設定情報も併せて取得する
        if($event->getData(0) instanceof \Cake\ORM\Query\SelectQuery) {
            $event->getData(0)->contain(['CuCustomFieldValues']);
        }
        return;
    }

    /**
     * ブログコンテンツIDからカスタムフィールド設定情報を取得する
     *
     * @param int $contentId
     * @return array or boolean
     */
    private function hasCustomFieldConfigData($contentId)
    {
        $data = $this->CuCustomFieldConfigs->find('all', [
            'conditions' => [
                'CuCustomFieldConfigs.content_id' => $contentId,
                'CuCustomFieldConfigs.model' => 'BlogContent',
            ],
        ])->first();
        return $data;
    }

    /**
     * blogBlogPostBeforeValidate
     *
     * @param \Cake\Event\Event $event
     * @return bool
     */
    public function bcBlogBlogPostsBeforeValidate(\Cake\Event\Event $event)
    {
        $params = Router::getParams();
        /**
         * 4系の記事複製動作仕様変更に対応
         * - これまで複製時のデータに、カスタムフィールドのデータは入って来なかったのが入るようになっているため
         */
        if (!in_array($params['action'], ['add', 'edit'])) {
            return true;
        }

        $Model = $event->getSubject();
//        debug($Model);
//        exit;
        // カスタムフィールドの入力データがない場合は、そもそもカスタムフィールドに対する validate 処理を実施しない
        if (!\Cake\Utility\Hash::get($Model->data, 'CuCustomFieldValue')) {
            /**
             * 4系の記事複製動作仕様変更に対応
             * - これまで複製時のデータに、カスタムフィールドのデータは入って来なかったのが入るようになっているため
             * - validateSection 処理まで渡してしまうと、カスタムフィールドに対して、notBlank（入力必須）を設定している場合、
             *   Cake側の notBlank が走ることで save エラーとなってしまい、記事複製動作が完了できないため
             */
            return true;
        }

        foreach($Model->data['CuCustomFieldValue'] as $key => $value) {
            if (isset($value['__loop-src__'])) {
                unset($Model->data['CuCustomFieldValue'][$key]['__loop-src__']);
                if(count($value) === 1) {
                    $Model->data['CuCustomFieldValue'][$key] = [];
                }
            }
        }

        $this->setUpModel();
        $data = $this->CuCustomFieldConfigs->find('all', [
            'conditions' => [
                'CuCustomFieldConfig.content_id' => $Model->BlogContent->id,
                'CuCustomFieldConfig.status' => true,
            ],
        ])
        ->first();
        if (!$data) {
            return true;
        }

        $fieldConfigField = $this->CuCustomFieldConfigs->CuCustomFieldDefinition->find('all', [
            'conditions' => [
                'CuCustomFieldDefinitions.config_id' => $data['CuCustomFieldConfig']['id'],
            ],
            'order' => 'CuCustomFieldDefinitions.lft ASC',
        ])
        ->toArray();
        if (!$fieldConfigField) {
            return true;
        }
        $this->CuCustomFieldValues->fieldConfig = $fieldConfigField;
        foreach($fieldConfigField as $key => $fieldConfig) {
            // ステータスが利用しないになっているフィールドは、バリデーション情報として渡さない
            if (!$fieldConfig['CuCustomFieldDefinition']['status']) {
                unset($fieldConfigField[$key]);
            }
        }
        if (!$fieldConfigField) {
            return true;
        }
        $this->CuCustomFieldValues->validatingLock = false;
        $this->_setValidate($fieldConfigField);
        if (!$this->CuCustomFieldValues->validateValues($Model->data)) {
            $Model->validationErrors += $this->CuCustomFieldValues->validationErrors;
            return false;
        }
        $Model->data = $this->CuCustomFieldValues->data;
        $this->CuCustomFieldValues->validatingLock = false;
        return true;
    }

    /**
     * バリデーションを設定する
     *
     * @param array $data 元データ
     */
    protected function _setValidate($data = [])
    {
        $validation = [];
        $map = [
            'required' => 'notBlank',
            'max_length' => 'maxLength',
            'validate' => [
                'HANKAKU_CHECK' => 'alphaNumeric',
                'NUMERIC_CHECK' => 'numeric',
                'REGEX_CHECK' => 'regexCheck',
                'NONCHECK_CHECK' => 'multiple'
            ]
        ];
        foreach($data as $key => $fieldConfig) {
            if(!empty($fieldConfig['CuCustomFieldDefinition']['parent_id'])) {
                continue;
            }
            $fieldName = $fieldConfig['CuCustomFieldDefinition']['field_name'];
            $fieldRule = [];
            foreach($map as $checkType => $rule) {
                if($checkType !== 'validate') {
                    if (!empty($fieldConfig['CuCustomFieldDefinition'][$checkType])) {
                        $fieldRule = \Cake\Utility\Hash::merge($fieldRule, $this->_getValidationRule($rule, $fieldConfig['CuCustomFieldDefinition']));
                    }
                } else {
                    foreach($rule as $validateType => $validateRule) {
                        if(is_array($fieldConfig['CuCustomFieldDefinition']['validate']) && in_array($validateType, $fieldConfig['CuCustomFieldDefinition']['validate'])) {
                            $fieldRule = \Cake\Utility\Hash::merge($fieldRule, $this->_getValidationRule($validateRule, $fieldConfig['CuCustomFieldDefinition']));
                        }
                    }
                }
            }
            $validation[$fieldName] = $fieldRule;
        }

        // ファイルタイプ制限
        foreach ($data as $key => $fieldConfig) {
            if ($fieldConfig['CuCustomFieldDefinition']['field_type'] !== 'file') {
                continue;
            }
            $fieldName = $fieldConfig['CuCustomFieldDefinition']['field_name'];
            $fieldConfig['CuCustomFieldDefinition']['allow_file_exts']
                = \Cake\Core\Configure::read('cuCustomField.allow_file_exts');
            if (empty($validation[$fieldName])) {
                $validation[$fieldName] = [];
            }
            $validation[$fieldName] = \Cake\Utility\Hash::merge(
                $validation[$fieldName],
                $this->_getValidationRule('fileExt', $fieldConfig['CuCustomFieldDefinition'])
            );
            $validation[$fieldName] = \Cake\Utility\Hash::merge(
                $validation[$fieldName],
                $this->_getValidationRule('fileCheck', $fieldConfig['CuCustomFieldDefinition'])
            );
        }

        $this->CuCustomFieldValues->validate = $validation;
    }

    /**
     * 設定可能なバリデーションルールを返す
     *
     * @param string $rule ルール名
     * @param array $options
     * @return array
     */
    protected function _getValidationRule($rule = '', $definition = [])
    {
        if($rule === 'notBlank' && $definition['field_type'] === 'file') {
            $rule = 'notFileEmpty';
        }
        $validation = [
            'notBlank' => [
                'notBlank' => [
                    'rule' => ['notBlank'],
                    'message' => '必須項目です。',
                    'required' => true,
                ],
            ],
            'notFileEmpty' => [
                'notFileEmpty' => [
                    'rule' => ['notFileEmpty'],
                    'message' => '必須項目です。',
                    'required' => true,
                ],
            ],
            'multiple' => [
                'notBlank' => [
                    'rule' => ['multiple'],
                    'message' => '必ず1つ以上選択してください。',
                    'required' => true,
                ],
            ],
            'maxLength' => [
                'maxLength' => [
                    'rule' => ['maxLength', $definition['max_length']],
                    'message' => $definition['max_length'] . '文字以内で入力してください。',
                ],
            ],
            'alphaNumeric' => [
                'alphaNumeric' => [
                    'rule' => ['alphaNumeric'],
                    'message' => '半角英数で入力してください。',
                ],
            ],
            'numeric' => [
                'numeric' => [
                    'rule' => ['numeric'],
                    'message' => '数値で入力してください。',
                ],
            ],
            'regexCheck' => [
                'regexCheck' => [
                    'rule' => ['regexCheck'],
                    'message' => ($definition['validate_regex_message']) ? $definition['validate_regex_message'] : '入力エラーが発生しました。',
                ],
            ],
            'fileCheck' => [
                'fileCheck' => [
                    'rule' => ['fileCheck', $this->CuCustomFieldValues->convertSize(ini_get('upload_max_filesize'))],
                    'message' => __d('baser', 'ファイルのアップロードに失敗しました。')
                ]
            ]
        ];
        if (isset($definition['allow_file_exts'])) {
            $validation['fileExt'] = [
                'fileExt' => [
                    'rule' => ['fileExt', $definition['allow_file_exts']],
                    'message' => '許可されていないファイルです。',
                ],
            ];
        }
        return $validation[$rule];
    }

    /**
     * blogBlogPostAfterSave
     *
     * @param \Cake\Event\Event $event
     */
    public function bcBlogBlogPostsAfterSave(\Cake\Event\Event $event)
    {
        $Model = $event->getSubject();
        $entity = $event->getData('entity');

        // カスタムフィールドの入力データがない場合は save 処理を実施しない
        if (!isset($entity->CuCustomFieldValue)) {
            return;
        }

        if (!$this->throwBlogPost) {
            $this->setUpModel();
            if (!$this->CuCustomFieldValues->saveSection($this->CuCustomFieldValues, $entity->id, $entity, 'CuCustomFieldValue')) {
                \Cake\Log\Log::write(sprintf('ブログ記事ID：%s のカスタムフィールドの保存に失敗', $entity->id));
            }
        }
        // ブログ記事コピー保存時、アイキャッチが入っていると処理が2重に行われるため、1周目で処理通過を判定し、
        // 2周目では保存処理に渡らないようにしている
        $this->throwBlogPost = true;
    }

    /**
     * blogBlogPostBeforeDelete
     *
     * @param \Cake\Event\Event $event
     */
    public function bcBlogBlogPostsBeforeDelete(\Cake\Event\Event $event)
    {
        $entity = $event->getData('entity');
        // ブログ記事削除時、そのブログ記事が持つカスタムフィールドを削除する
        $this->setUpModel();
        $data = $this->CuCustomFieldValues->getSection($entity->id, $this->CuCustomFieldValues);
        if ($data) {
            //resetSection(Model $Model, $foreignKey = null, $section = null, $key = null)
            if (!$this->CuCustomFieldValues->resetSection($this->CuCustomFieldValues, $entity->id)) {
                \Cake\Log\Log::write('error', sprintf('ブログ記事ID：%s のカスタムフィールドの削除に失敗',$entity->id));
                //\Cake\Log\Log::write(sprintf('ブログ記事ID：%s のカスタムフィールドの削除に失敗', $Model->id));
            }
        }
    }

    /**
     * blogBlogPostBeforeCopy
     *
     * @param \Cake\Event\Event $event
     */
    public function bcBlogBlogPostsBeforeCopy(\Cake\Event\Event $event)
    {
        $eventData = $event->getData();
        // ブログ記事コピー時、そのブログ記事が持つカスタムフィールドをコピーする
        $this->setUpModel();
        //debug($event->getData()['data']->cu_custom_field_values);
        $data = $this->CuCustomFieldValues->getSection($eventData['id'], $this->CuCustomFieldValues);
        $event->getData()['data']->cu_custom_field_values = $data;
        return;
    }

    /**
     * bcBlogBlogPostsAfterCopy
     *
     * @param \Cake\Event\Event $event
     */
    public function bcBlogBlogPostsAfterCopy(\Cake\Event\Event $event)
    {
        $Model = $event->getSubject();
        $data = $event->getData();

        // ブログ記事コピー時、そのブログ記事が持つカスタムフィールドをコピーする
        $this->setUpModel();
        $newId = $data['id'];
        // 古い記事IDでカスタムフィールドの値を取得
        $data['CuCustomFieldValue'] = $this->CuCustomFieldValues->getSection($data['oldId'], $this->CuCustomFieldValues);

        if ($data['CuCustomFieldValue']) {
            // 新しい記事IDでレコードを保存
            $this->CuCustomFieldValues->saveSection($this->CuCustomFieldValues, $newId, $data, 'CuCustomFieldValue');
        }
    }

    /**
     * blogBlogContentAfterDelete
     *
     * @param \Cake\Event\Event $event
     */
    public function bcBlogBlogContentsAfterDelete(\Cake\Event\Event $event)
    {
        $Model = $event->getSubject();
        // ブログ削除時、そのブログが持つカスタムフィールド設定を削除する
        $this->setUpModel();
        $data = $this->CuCustomFieldConfigs->find('first', [
            'conditions' => ['CuCustomFieldConfig.content_id' => $Model->id],
            'recursive' => -1
        ]);
        if ($data) {
            if (!$this->CuCustomFieldConfigs->delete($data['CuCustomFieldConfig']['id'])) {
                \Cake\Log\Log::write('ID:' . $data['CuCustomFieldConfig']['id'] . 'のカスタムフィールド設定の削除に失敗しました。');
            }
        }
    }

    /**
     * 保存するデータの生成
     *
     * @param Object $Model
     * @param int $contentId
     * @return array
     */
    private function generateSaveData($Model, $contentId)
    {
        $params = Router::getParams();
        if (ClassRegistry::isKeySet('CuCustomField.CuCustomFieldValue')) {
            $this->CuCustomFieldValues = ClassRegistry::getObject('CuCustomField.CuCustomFieldValue');
        } else {
            $this->CuCustomFieldValues = \Cake\ORM\TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValue');
        }

        $data = [];
        $modelId = $oldModelId = null;
        if ($Model->alias == 'BlogPost') {
            $modelId = $contentId;
            if (!empty($params['pass'][1])) {
                $oldModelId = $params['pass'][1];
            }
        }

        if ($contentId) {
            $data = $this->CuCustomFieldValues->find('first', ['conditions' => [
                'CuCustomFieldValue.blog_post_id' => $contentId
            ]]);
        }

        switch($params['action']) {
            case 'admin_add':
                // 追加時
                if (!empty($Model->data['CuCustomFieldValue'])) {
                    $data['CuCustomFieldValue'] = $Model->data['CuCustomFieldValue'];
                }
                $data['CuCustomFieldValue']['blog_post_id'] = $contentId;
                break;

            case 'admin_edit':
                // 編集時
                if (!empty($Model->data['CuCustomFieldValue'])) {
                    $data['CuCustomFieldValue'] = $Model->data['CuCustomFieldValue'];
                }
                break;

            case 'admin_ajax_copy':
                // Ajaxコピー処理時に実行
                // ブログコピー保存時にエラーがなければ保存処理を実行
                if (empty($Model->validationErrors)) {
                    $_data = [];
                    if ($oldModelId) {
                        $_data = $this->CuCustomFieldValues->find('first', [
                            'conditions' => [
                                'CuCustomFieldValue.blog_post_id' => $oldModelId
                            ],
                            'recursive' => -1
                        ]);
                    }
                    // XXX もしカスタムフィールド設定の初期データ作成を行ってない事を考慮して判定している
                    if ($_data) {
                        // コピー元データがある時
                        $data['CuCustomFieldValue'] = $_data['CuCustomFieldValue'];
                        $data['CuCustomFieldValue']['blog_post_id'] = $contentId;
                        unset($data['CuCustomFieldValue']['id']);
                    } else {
                        // コピー元データがない時
                        $data['CuCustomFieldValue']['blog_post_id'] = $modelId;
                    }
                }
                break;

            default:
                break;
        }

        return $data;
    }

    /**
     * Returns details for named section
     *
     * @var string
     * @var string
     * @return mixed Flat array or direct value
     */
    public function getSection(Table $Model, $foreignKey, $section = null, $key = null) {
        // extract($this->settings[$Model->getAlias()]);
        $results = $this->KeyValue->find('all', array(
            'recursive' => -1,
            'conditions' => array($foreignKeyField => $foreignKey),
            'fields' => array('key', 'value')
        ))->all();

        $defaultValues = $this->defaultValues($Model);

        $detailArray = array();
        foreach ($results as $value) {
            $keyArray = preg_split('/\./', $value[$this->KeyValue->alias]['key'], 2);
            $detailArray[$keyArray[0]][$keyArray[1]] = $value[$this->KeyValue->alias]['value'];
        }

        foreach ($defaultValues as $model => $values) {
            foreach ($values as $valueKey => $val) {
                if (isset($detailArray[$model][$valueKey])) {
                    continue;
                }
                $detailArray[$model][$valueKey] = $val;
            }
        }

        if ($section === null) {
            return $detailArray;
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

}
