<?php
namespace CuCustomField\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Error\BcException;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;

/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Controller
 * @license          MIT LICENSE
 */
/**
 * Class CuCustomFieldConfigsController
 */
class CuCustomFieldConfigsController extends \CuCustomField\Controller\Admin\CuCustomFieldAppController
{
    // ブログ名一覧 (blogContentId / blogName)
    public $blogContentDatas;

    /**
     * 管理画面タイトル
     *
     * @var string
     */
    public $adminTitle = 'カスタムフィールド設定';

    /**
     * beforeFilter
     *
     */
    public function beforeFilter(EventInterface $event)
    {
        // ブログ情報を取得
        $ContentModel = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $query = $ContentModel->find()
            ->contain([
                'Sites',
            ]);
        $query = $ContentModel->find('list', [
            'keyField' => 'entity_id',
            'valueField' => 'blog_name',
        ])
            ->select([
                'entity_id',
                'blog_name' => $query->func()->concat([
                    '[ ', 'Sites.title' => 'identifier', ' ] ', 'Contents.title' => 'identifier'
                ]),
            ])
            ->contain([
                'Sites',
            ])
            ->where([
                'plugin' => 'BcBlog',
                'type'	 => 'BlogContent',
                'alias_id IS' => null,
            ]);
        $this->blogContentDatas = $query->toArray();
        $this->set('customFieldConfig', \Cake\Core\Configure::read('cuCustomField'));
    }


    /**
     * [ADMIN] カスタムフィールド設定一覧
     *
     */
    public function index()
    {
        $this->setTitle($this->adminTitle . '一覧');
        $this->search = 'cu_custom_field_configs_index';
        $this->help = 'cu_custom_field_configs_index';
        $this->setViewConditions('CuCustomFieldConfigs', [
            'default' => [
                'query' => [
                    'limit' => BcSiteConfig::get('admin_list_num'),
                    'sort' => 'no',
                    'direction' => 'desc',
                ]
            ]
        ]);
        $conditions = $this->_createAdminIndexConditions($this->getRequest()->getData());
        $query = $this->CuCustomFieldConfigs->find()
            ->where($conditions)
            ->contain(['CuCustomFieldDefinitions']);
        $this->set('datas', $this->paginate($query));
        $this->set('blogContentDatas', $this->blogContentDatas);
    }

    /**
     * [ADMIN] 編集
     *
     * @param int $id
     */
    public function edit($id = null)
    {
        $this->setTitle($this->adminTitle . '編集');
        parent::edit($id);
    }
    /**
     * [ADMIN] 新規登録
     *
     */
    public function add()
    {
        $this->setTitle($this->adminTitle . '追加');
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $entity = $this->CuCustomFieldConfigs->newEmptyEntity();
                $data = $this->CuCustomFieldConfigs->patchEntity($entity, $this->getRequest()->getData());
                $results = $this->CuCustomFieldConfigs->saveOrFail($data);
                $blogName = ($this->blogContentDatas[$results->blog_content_id] ?? '');
                $this->BcMessage->setSuccess(__d('cu_custom_field', '{0} のカスタムフィールド設定を追加しました。', $blogName));
                return $this->redirect(['action' => 'index']);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $this->setRequest($this->getRequest()->withParsedBody([]));
                pr($entity->getErrors());
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (BcException $e) {
                if ($e->getCode() === "23000") {
                    $this->BcMessage->setError(__d('baser_core', '同時更新エラーです。しばらく経ってから保存してください。'));
                } else {
                    $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
                }
            }
        } else {
            $entity = $this->CuCustomFieldConfigs->newEmptyEntity();
            $default = $this->CuCustomFieldConfigs->getDefaultValue();
            $data = $this->CuCustomFieldConfigs->patchEntity($entity, $default);
            $this->setRequest($this->getRequest()->withParsedBody($data));
        }

        $this->_createAdminFormData($data);

        $this->render('form');



//        if ($this->getRequest()->is('post')) {
//            if ($this->{$this->modelClass}->save($this->getRequest()->getData())) {
//                $message = $this->name . 'を追加しました。';
//                $this->BcMessage->setSuccess($message);
//                $this->redirect(['action' => 'index']);
//            } else {
//                $this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
//            }
//        } else {
//            $this->setRequest($this->getRequest()->withParsedBody($this->{$this->modelClass}->getDefaultValue()));
//            $this->getRequest()->getData()[$this->modelClass]['model'] = 'BlogContent';
//        }
//        // 設定データがあるブログは選択リストから除外する
//        $dataList = $this->{$this->modelClass}->find('all');
//        if ($dataList) {
//            foreach ($dataList as $data) {
//                unset($this->blogContentDatas[$data[$this->modelClass]['content_id']]);
//            }
//        }
//        $this->set('blogContentDatas', $this->blogContentDatas);
//        $this->render('form');
    }
    /**
     * [ADMIN] フォームデータ表示
     */
    protected function _createAdminFormData($data)
    {
        if ($this->getRequest()->getParam('action') === 'add') {
            // 設定データがあるブログは選択リストから除外する
            $dataList = $this->CuCustomFieldConfigs->find('all');
            if ($dataList) {
                foreach ($dataList as $data) {
                    unset($this->blogContentDatas[$data->blog_content_id]);
                }
            }
            $this->set('blogContentDatas', $this->blogContentDatas);
        } else {
            $this->set('blogContentDatas', ['0' => __d('baser_core', '指定しない')] + $this->blogContentDatas);
        }

        $this->set('data', $data);
    }


    /**
     * [ADMIN] 削除
     *
     * @param int $id
     */
    public function delete($id = null)
    {
        parent::admin_delete($id);
    }
    /**
     * 各ブログ別のカスタムフィールド設定データを作成する
     * - カスタムフィールド設定データがないブログ用のデータのみ作成する
     *
     */
    public function first()
    {
        $this->setTitle($this->adminTitle . 'データ作成');
        if ($this->getRequest()->getData()) {
            $count = 0;
            if ($this->blogContentDatas) {
                foreach ($this->blogContentDatas as $key => $blog) {
                    $configData = $this->CuCustomFieldConfig->findByContentId($key);
                    if (!$configData) {
                        $this->setRequest($this->getRequest()->withData('CuCustomFieldConfig.content_id', $key));
                        $this->setRequest($this->getRequest()->withData('CuCustomFieldConfig.status', true));
                        $this->setRequest($this->getRequest()->withData('CuCustomFieldConfig.model', 'BlogContent'));
                        $this->setRequest($this->getRequest()->withData('CuCustomFieldConfig.form_place', 'normal'));
                        $this->CuCustomFieldConfig->create($this->getRequest()->getData());
                        if (!$this->CuCustomFieldConfig->save($this->getRequest()->getData(), false)) {
                            $this->log(sprintf('ブログID：%s の登録に失敗しました。', $key));
                        } else {
                            $count++;
                        }
                    }
                }
            }
            $message = sprintf('%s 件のカスタムフィールド設定を登録しました。', $count);
            $this->BcMessage->setSuccess($message);
            $this->redirect(['controller' => 'cu_custom_field_configs', 'action' => 'index']);
        }
    }

    /**
     * 一覧用の検索条件を生成する
     *
     * @param array $data
     * @return array $conditions
     */
    protected function _createAdminIndexConditions($data)
    {
        $conditions = [];

        if (isset($data['CuCustomFieldConfigs']['content_id'])) {
            $conditions['CuCustomFieldConfigs.content_id'] = $data['CuCustomFieldConfigs']['content_id'];
        }

        if (isset($data['CuCustomFieldConfigs']['status']) && $data['CuCustomFieldConfigs']['status'] === '') {
            unset($data['CuCustomFieldConfigs']['status']);
        }
        if (isset($data['CuCustomFieldConfigs']['status'])) {
            $conditions['CuCustomFieldConfigs.status'] = $data['CuCustomFieldConfigs']['status'];
        }

        return $conditions;
    }




}
