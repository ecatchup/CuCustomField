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
 * Class CuCustomFieldValuesController
 */
class CuCustomFieldValuesController extends \BaserCore\Controller\Admin\BcAdminAppController
{
    /**
     * [ADMIN] 一覧
     *
     */
    public function index()
    {
        $this->setTitle($this->adminTitle . '一覧');
        $this->search = 'cu_custom_field_values_index';
        $this->help = 'cu_custom_field_values_index';
        parent::admin_index();
    }
    /**
     * [ADMIN] 編集
     *
     * @param int $id
     */
    public function edit($id = null)
    {
        $this->setTitle($this->adminTitle . '編集');
        if (!$id) {
            $this->BcMessage->setError('無効な処理です。');
            $this->redirect(['action' => 'index']);
        }
        if (empty($this->getRequest()->getData())) {
            $this->{$this->modelClass}->id = $id;
            $this->setRequest($this->getRequest()->withParsedBody($this->{$this->modelClass}->read()));
            $configData = $this->CuCustomFieldConfig->find('first', ['conditions' => ['CuCustomFieldConfig.content_id' => $this->getRequest()->getData()[$this->modelClass]['content_id']]]);
            $this->setRequest($this->getRequest()->withData('CuCustomFieldConfig', $configData['CuCustomFieldConfig']));
        } else {
            $configData = $this->CuCustomFieldConfig->find('first', ['conditions' => ['CuCustomFieldConfig.content_id' => $this->getRequest()->getData()[$this->modelClass]['content_id']]]);
            $this->setRequest($this->getRequest()->withData('CuCustomFieldConfig', $configData['CuCustomFieldConfig']));
            if ($this->{$this->modelClass}->save($this->getRequest()->getData())) {
                $this->BcMessage->setSuccess($this->name . ' ID:' . $id . ' を更新しました。');
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
            }
        }
        $this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
        $this->render('form');
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
}
