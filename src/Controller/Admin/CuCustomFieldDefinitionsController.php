<?php

namespace CuCustomField\Controller\Admin;

use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcSiteConfig;
use Cake\Utility\Hash;

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
 * Class CuCustomFieldDefinitionsController
 * @property CuCustomFieldDefinition $CuCustomFieldDefinition
 */
class CuCustomFieldDefinitionsController extends \BaserCore\Controller\Admin\BcAdminAppController
{
    /**
     * 管理画面タイトル
     *
     * @var string
     */
    public $adminTitle = 'フィールド定義';

    /**
     * ブログ名一覧
     *
     * @var array
     */
    public $blogContentDatas = [];

    /**
     * 定義テーブル
     *
     * @var object
     */
    public $CuCustomFieldDefinition;

    public function initialize(): void
    {
        parent::initialize();
        $this->CuCustomFieldDefinition = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldDefinitions');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // ブログ情報を取得
        $contentModel = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $query = $contentModel->find()->contain(['Sites']);
        $query = $contentModel->find('list', [
            'keyField' => 'entity_id',
            'valueField' => 'blog_name',
        ])
            ->select([
                'entity_id',
                'blog_name' => $query->func()->concat([
                    '[ ', 'Sites.title' => 'identifier', ' ] ', 'Contents.title' => 'identifier'
                ]),
            ])
            ->contain(['Sites'])
            ->where([
                'plugin' => 'BcBlog',
                'type' => 'BlogContent',
                'alias_id IS' => null,
            ]);
        $this->blogContentDatas = $query->toArray();

        // カスタムフィールド定義からコンテンツIDを取得
        $configId = $this->getRequest()->getParam('pass.0');
        if ($configId) {
            $this->CuCustomFieldDefinition->setup((int) $configId);
            $configTable = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldConfigs');
            $configData = $configTable->find()->where(['id' => $configId])->first();
            if ($configData && isset($configData->content_id)) {
                $this->set('contentId', $configData->content_id);
            }
        }

        $this->set('customFieldConfig', $this->buildCustomFieldConfig());
    }

    /**
     * フィールドタイプ選択肢を再構築する
     *
     * @return array
     */
    private function buildCustomFieldConfig(): array
    {
        $customFieldConfig = (array) Configure::read('cuCustomField');
        $fieldTypes = (array) Configure::read('CuCustomField.fieldTypes');

        if (!$fieldTypes) {
            $pluginPath = Plugin::path('CuCustomField') . 'plugins' . DS;
            foreach (glob($pluginPath . '*', GLOB_ONLYDIR) ?: [] as $childDir) {
                $settingFiles = [
                    $childDir . DS . 'config' . DS . 'setting.php',
                    $childDir . DS . 'Config' . DS . 'setting.php',
                ];
                foreach ($settingFiles as $settingFile) {
                    if (!is_file($settingFile)) {
                        continue;
                    }
                    $setting = include $settingFile;
                    $pluginFieldTypes = (array) ($setting['CuCustomField']['fieldTypes'] ?? []);
                    if ($pluginFieldTypes) {
                        $fieldTypes = array_merge($fieldTypes, $pluginFieldTypes);
                    }
                    break;
                }
            }
            if ($fieldTypes) {
                Configure::write('CuCustomField.fieldTypes', $fieldTypes);
            }
        }

        $groups = [
            '基本' => [],
            '日付' => [],
            '選択' => [],
            'コンテンツ' => [],
            'その他' => ['loop' => 'ループ'],
        ];

        foreach ($fieldTypes as $pluginKey => $fieldType) {
            $category = (string) ($fieldType['category'] ?? 'その他');
            $label = (string) ($fieldType['label'] ?? '');
            if (!$label) {
                continue;
            }
            if (!isset($groups[$category])) {
                $groups[$category] = [];
            }
            $typeKey = preg_replace('/^CuCf/', '', (string) $pluginKey);
            $typeKey = lcfirst((string) $typeKey);
            if ($typeKey === '') {
                continue;
            }
            $groups[$category][$typeKey] = $label;
        }

        $customFieldConfig['field_type'] = $groups;
        return $customFieldConfig;
    }

    /**
     * [ADMIN] フィールド定義一覧
     *
     * @param int $configId
     */
    public function index($configId)
    {
        if (!$configId) {
            $this->BcMessage->setError('無効な処理です。');
            $this->notFound();
        }
        $this->setTitle($this->adminTitle . '一覧');
        $this->help = 'cu_custom_field_metas_index';
        $this->setViewConditions('CuCustomFieldDefinition', [
            'default' => [
                'query' => [
                    'limit' => BcSiteConfig::get('admin_list_num'),
                ]
            ]
        ]);
        $conditions = $this->_createAdminIndexConditions($configId, $this->getRequest()->getData());

        $allRows = $this->CuCustomFieldDefinition->find()
            ->select(['id', 'parent_id'])
            ->where($conditions)
            ->orderBy(['lft' => 'ASC'])
            ->enableHydration(false)
            ->all()
            ->toList();
        $parentMap = [];
        foreach ($allRows as $row) {
            $parentMap[$row['id']] = $row['parent_id'] ?? null;
        }

        $query = $this->CuCustomFieldDefinition->find()
            ->where($conditions)
            ->orderBy(['lft' => 'ASC'])
            ->enableHydration(false);
        $rows = $this->paginate($query);

        $definitions = [];
        foreach ($rows as $row) {
            $depth = 0;
            $parentId = $row['parent_id'] ?? null;
            while ($parentId !== null && isset($parentMap[$parentId]) && $depth < 50) {
                $depth++;
                $parentId = $parentMap[$parentId];
            }

            $row['name'] = $this->normalizeDefinitionName((string) ($row['name'] ?? ''));

            $definition = [
                'CuCustomFieldDefinition' => $row,
                'CuCustomFieldConfig' => ['id' => $configId],
            ];
            $definition['CuCustomFieldDefinition']['_depth'] = $depth;
            $definitions[] = $definition;
        }
        $this->set('datas', $definitions);
        $this->set('configId', $configId);
        $this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
    }
    /**
     * [ADMIN] 編集
     *
     * @param int $configId
     * @param int $id
     */
    public function edit($configId = null, $id = null)
    {
        $this->setTitle($this->adminTitle . '編集');
        $this->help = 'cu_custom_field_definitions';
        $deletable = true;
        if (!$configId || !$id) {
            $this->BcMessage->setError('無効な処理です。');
            $this->redirect(['action' => 'index']);
        }
        if (empty($this->getRequest()->getData())) {
            $row = $this->CuCustomFieldDefinition->find()
                ->where(['CuCustomFieldDefinition.id' => $id])
                ->enableHydration(false)
                ->first();
            if ($row) {
                if (!empty($row['option_meta']) && is_string($row['option_meta'])) {
                    $optionMeta = @unserialize($row['option_meta']);
                    if (is_array($optionMeta)) {
                        $row['option_meta'] = $optionMeta;
                    }
                }
                $row['name'] = $this->normalizeDefinitionName((string) ($row['name'] ?? ''));
                $this->setRequest($this->getRequest()->withParsedBody(['CuCustomFieldDefinition' => $row]));
            }
        } else {
            $requestData = (array) $this->getRequest()->getData();
            $modelData = $this->buildDefinitionSaveData($requestData);
            $entity = $this->CuCustomFieldDefinition->get($id);
            $entity = $this->CuCustomFieldDefinition->patchEntity($entity, $modelData);
            $this->CuCustomFieldDefinition->data = ['CuCustomFieldDefinition' => $modelData];
            if ($this->CuCustomFieldDefinition->save($entity)) {
                if (($modelData['field_type'] ?? null) !== 'loop') {
                    $children = $this->CuCustomFieldDefinition->find()
                        ->select(['id'])
                        ->where(['parent_id' => $id])
                        ->enableHydration(false)
                        ->all()
                        ->toList();
                    if ($children) {
                        foreach ($children as $child) {
                            $childId = $child['id'] ?? null;
                            if ($childId !== null) {
                                $this->CuCustomFieldDefinition->updateAll(['parent_id' => null], ['id' => $childId]);
                            }
                        }
                    }
                }
                $message = 'フィールド定義「' . $this->getRequest()->getData('CuCustomFieldDefinition.name') . '」を更新しました。';
                $this->BcMessage->setSuccess($message);
                $this->redirect(['action' => 'index', $configId]);
            } else {
                $this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
            }
        }
        $fieldNameList = $this->CuCustomFieldDefinition->getControlSource('field_name');
        $this->set('loops', $this->CuCustomFieldDefinition->getLoopList($configId));
        $this->set(compact('fieldNameList', 'configId', 'deletable'));
        $this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
        $this->render('form');
    }
    /**
     * [ADMIN] 編集
     *
     * @param int $configId
     */
    public function add($configId = null)
    {
        $this->setTitle($this->adminTitle . '追加');
        $this->help = 'cu_custom_field_definitions';
        $deletable = false;
        if (!$configId) {
            $this->BcMessage->setError('無効な処理です。');
            $this->redirect(['controller' => 'cu_custom_field_configs', 'action' => 'index']);
        }
        if (empty($this->getRequest()->getData())) {
            $defaultFieldType = 'text';
            $fieldTypeOptions = (array) ($this->buildCustomFieldConfig()['field_type'] ?? []);
            $flattenedFieldTypes = [];
            foreach ($fieldTypeOptions as $options) {
                if (is_array($options)) {
                    $flattenedFieldTypes += $options;
                }
            }
            if (!isset($flattenedFieldTypes[$defaultFieldType])) {
                $defaultFieldType = (string) array_key_first($flattenedFieldTypes);
            }

            $this->setRequest($this->getRequest()->withParsedBody([
                'CuCustomFieldDefinition' => [
                    'config_id' => $configId,
                    'field_type' => $defaultFieldType,
                ],
            ]));
        } else {
            $requestData = (array) $this->getRequest()->getData();
            $modelData = $this->buildDefinitionSaveData($requestData);
            $entity = $this->CuCustomFieldDefinition->newEntity($modelData);
            $this->CuCustomFieldDefinition->data = ['CuCustomFieldDefinition' => $modelData];
            if ($this->CuCustomFieldDefinition->save($entity)) {
                $message = 'フィールド定義「' . $this->getRequest()->getData('CuCustomFieldDefinition.name') . '」の追加が完了しました。';
                $this->BcMessage->setSuccess($message);
                $this->redirect(['action' => 'index', $configId]);
            } else {
                $this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
            }
        }
        $fieldNameList = $this->CuCustomFieldDefinition->getControlSource('field_name');
        $this->set('loops', $this->CuCustomFieldDefinition->getLoopList($configId));
        $this->set(compact('fieldNameList', 'configId', 'deletable'));
        $this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
        $this->render('form');
    }
    /**
     * [ADMIN] 削除
     *
     * @param int $configId
     * @param int $foreignId
     */
    public function delete($configId = null, $id = null)
    {
        if (!$configId || !$id) {
            $this->BcMessage->setError('無効な処理です。');
            $this->redirect(['action' => 'index']);
        }
        // 削除前にメッセージ用にカスタムフィールドを取得する
        $data = $this->CuCustomFieldDefinition->read($id);
        if ($this->CuCustomFieldDefinition->delete($id)) {
            $message = $this->name . '「' . $data['CuCustomFieldDefinition']['name'] . '」を削除しました。';
            $this->BcMessage->setSuccess($message);
            $this->redirect(['action' => 'index', $configId]);
        } else {
            $this->BcMessage->setError('データベース処理中にエラーが発生しました。');
        }
        $this->redirect(['action' => 'index', $configId]);
    }
    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param int $configId
     * @param int $id
     */
    public function ajax_delete($configId = null, $id = null)
    {
        if (!$configId || !$id) {
            $this->ajaxError(500, '無効な処理です。');
        }
        // 削除実行
        if ($this->CuCustomFieldDefinition->delete($id)) {
            clearViewCache();
            exit(true);
        }
        exit;
    }
    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param int $configId
     * @param int $id
     */
    public function ajax_unpublish($id = null)
    {
        if (!$id) {
            $this->ajaxError(500, '無効な処理です。');
        }
        if ($this->_changeStatus($id, false)) {
            clearViewCache();
            exit(true);
        } else {
            $this->ajaxError(500, $this->{$this->modelClass}->validationErrors);
        }
        exit;
    }
    /**
     * [ADMIN] 有効状態にする（AJAX）
     *
     * @param int $configId
     * @param int $id
     */
    public function ajax_publish($id = null)
    {
        if (!$id) {
            $this->ajaxError(500, '無効な処理です。');
        }
        if ($this->_changeStatus($id, true)) {
            clearViewCache();
            exit(true);
        } else {
            $this->ajaxError(500, $this->{$this->modelClass}->validationErrors);
        }
        exit;
    }
    /**
     * [ADMIN] 並び順を上げる
     *
     * @param int $configId
     * @param int $id
     */
    public function move_up($configId, $id)
    {
        if (!$id || !$configId) {
            $this->BcMessage->setError('無効な処理です。');
            $this->redirect(['action' => 'index']);
        }
        if ($this->CuCustomFieldDefinition->up($id, $configId)) {
            $this->BcMessage->setSuccess('フィールド定義の並び順を繰り上げました。');
        } else {
            $this->BcMessage->setError('データベース処理中にエラーが発生しました。');
        }
        $this->redirect(['action' => 'index', $configId]);
    }
    /**
     * [ADMIN] 並び順を下げる
     *
     * @param int $configId
     * @param int $id
     */
    public function move_down($configId = null, $id = null, $toBottom = '')
    {
        if (!$id || !$configId) {
            $this->BcMessage->setError('無効な処理です。');
            $this->redirect(['action' => 'index']);
        }
        if ($this->CuCustomFieldDefinition->down($id, $configId)) {
            $this->BcMessage->setSuccess('フィールド定義の並び順を繰り下げました。');
        } else {
            $this->BcMessage->setError('データベース処理中にエラーが発生しました。');
        }
        $this->redirect(['action' => 'index', $configId]);
    }
    /**
     * [ADMIN][AJAX] 重複値をチェックする
     *   ・foreign_id が異なるものは重複とみなさない
     *
     */
    public function ajax_check_duplicate()
    {
        $this->autoRender = false;
        \Cake\Core\Configure::write('debug', 0);
        $result = true;
        if (!$this->getRequest()->is('ajax')) {
            $message = '許可されていないアクセスです。';
            $this->BcMessage->setError($message);
            $this->redirect(['controller' => 'cu_custom_field_configs', 'action' => 'index']);
        }
        $requestData = (array) $this->getRequest()->getData();
        $modelKey = isset($requestData['CuCustomFieldDefinition']) ? 'CuCustomFieldDefinition' : $this->modelClass;
        $modelData = (array) ($requestData[$modelKey] ?? []);
        if ($modelData) {
            $conditions = [];
            if (array_key_exists('name', $modelData)) {
                $conditions = ['CuCustomFieldDefinition.name' => $modelData['name']];
            }
            if (array_key_exists('label_name', $modelData)) {
                $conditions = ['CuCustomFieldDefinition.label_name' => $modelData['label_name']];
            }
            if (array_key_exists('field_name', $modelData)) {
                $conditions = ['CuCustomFieldDefinition.field_name' => $modelData['field_name']];
            }
            $conditions = \Cake\Utility\Hash::merge($conditions, [
                'CuCustomFieldDefinition.config_id' => $modelData['config_id'] ?? null,
                'NOT' => ['CuCustomFieldDefinition.id' => $modelData['id'] ?? null]
            ]);
            $ret = $this->CuCustomFieldDefinition->find()
                ->where($conditions)
                ->enableHydration(false)
                ->first();
            if ($ret) {
                $result = false;
            } else {
                $result = true;
            }
        }
        echo $result;
    }

    /**
     * 一覧用の検索条件を生成する
     *
     * @param int $configId
     * @param array $data
     * @return array
     */
    protected function _createAdminIndexConditions($configId, $data)
    {
        $conditions = ['CuCustomFieldDefinition.config_id' => $configId];
        if (!empty($data['CuCustomFieldDefinition']['status'])) {
            $conditions['CuCustomFieldDefinition.status'] = $data['CuCustomFieldDefinition']['status'];
        }
        return $conditions;
    }

    /**
     * 保存用データを組み立てる
     * - 4系互換の data.* と 5系キーをマージ
     * - related の option_meta を明示構築して文字列化
     *
     * @param array $requestData
     * @return array
     */
    private function buildDefinitionSaveData(array $requestData): array
    {
        $modelData = array_replace_recursive(
            (array) ($requestData['CuCustomFieldDefinition'] ?? []),
            (array) ($requestData['data']['CuCustomFieldDefinition'] ?? [])
        );

        if (isset($modelData['name'])) {
            $modelData['name'] = $this->normalizeDefinitionName((string) $modelData['name']);
        }

        $related = [
            'table' => (string) (Hash::get($modelData, 'option_meta.related.table') ?? ''),
            'title_field' => (string) (Hash::get($modelData, 'option_meta.related.title_field') ?? ''),
            'where_field' => (string) (Hash::get($modelData, 'option_meta.related.where_field') ?? ''),
            'where_value' => (string) (Hash::get($modelData, 'option_meta.related.where_value') ?? ''),
        ];
        if (implode('', $related) !== '') {
            $modelData['option_meta']['related'] = $related;
        }

        if (isset($modelData['option_meta']) && is_array($modelData['option_meta'])) {
            $modelData['option_meta'] = serialize($modelData['option_meta']);
        }

        return $modelData;
    }

    /**
     * 一覧用インデント記号が name に混入していた過去データを正規化する
     */
    private function normalizeDefinitionName(string $name): string
    {
        $name = str_replace('&nbsp;', ' ', $name);
        return trim((string) preg_replace('/^[\s　]*(?:└|├|┗|┣|│|｜|_)+[\s　]*/u', '', $name));
    }
}
