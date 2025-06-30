<?php

namespace CuCustomField\Controller\Admin;

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
        $this->setViewConditions('CuCustomFieldDefinition', ['default' => ['named' => ['num' => $this->siteConfigs['admin_list_num']]]]);
        $conditions = $this->_createAdminIndexConditions($configId, $this->getRequest()->getData());
        $list = $this->CuCustomFieldDefinition->generateTreeList($conditions);
        $definitions = [];
        foreach ($list as $key => $value) {
            $definition = $this->CuCustomFieldDefinition->find('first', ['conditions' => ['CuCustomFieldDefinition.id' => $key]]);
            if (preg_match("/^([_]+)/i", $value, $matches)) {
                $prefix = str_replace('_', '   ', $matches[1]);
                $definition['CuCustomFieldDefinition']['name'] = $prefix . '└&nbsp;' . $definition['CuCustomFieldDefinition']['name'];
            }
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
            $this->setRequest($this->getRequest()->withParsedBody($this->CuCustomFieldDefinition->find('first', ['conditions' => ['CuCustomFieldDefinition.id' => $id]])));
        } else {
            $this->CuCustomFieldDefinition->set($this->getRequest()->getData());
            if ($this->CuCustomFieldDefinition->save()) {
                if ($this->getRequest()->getData('CuCustomFieldDefinition.field_type') !== 'loop') {
                    $children = $this->CuCustomFieldDefinition->children($this->getRequest()->getData('CuCustomFieldDefinition.id'));
                    if ($children) {
                        foreach ($children as $child) {
                            $child['CuCustomFieldDefinition']['parent_id'] = null;
                            $this->CuCustomFieldDefinition->set($child);
                            $this->CuCustomFieldDefinition->save();
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
            $this->setRequest($this->getRequest()->withParsedBody(['CuCustomFieldDefinition' => ['config_id' => $configId]]));
        } else {
            $this->CuCustomFieldDefinition->create($this->getRequest()->getData());
            if ($this->CuCustomFieldDefinition->save()) {
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
        if ($this->getRequest()->getData()) {
            $conditions = [];
            if (array_key_exists('name', $this->getRequest()->getData()[$this->modelClass])) {
                $conditions = [$this->modelClass . '.' . 'name' => $this->getRequest()->getData()[$this->modelClass]['name']];
            }
            if (array_key_exists('label_name', $this->getRequest()->getData()[$this->modelClass])) {
                $conditions = [$this->modelClass . '.' . 'label_name' => $this->getRequest()->getData()[$this->modelClass]['label_name']];
            }
            if (array_key_exists('field_name', $this->getRequest()->getData()[$this->modelClass])) {
                $conditions = [$this->modelClass . '.' . 'field_name' => $this->getRequest()->getData()[$this->modelClass]['field_name']];
            }
            $conditions = \Cake\Utility\Hash::merge($conditions, [$this->modelClass . '.' . 'config_id' => $this->getRequest()->getData()[$this->modelClass]['config_id'], 'NOT' => [$this->modelClass . '.id' => $this->getRequest()->getData()[$this->modelClass]['id']]]);
            $ret = $this->{$this->modelClass}->find('first', ['conditions' => $conditions, 'recursive' => -1]);
            if ($ret) {
                $result = false;
            } else {
                $result = true;
            }
        }
        echo $result;
    }
}
