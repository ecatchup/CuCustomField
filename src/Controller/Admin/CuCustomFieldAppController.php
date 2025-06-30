<?php

namespace CuCustomField\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BaserCore\Error\BcException;
use Cake\ORM\TableRegistry;
use Cake\ORM\Exception\PersistenceFailedException;

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
 * Class CuCustomFieldAppController
 */
class CuCustomFieldAppController extends \BaserCore\Controller\Admin\BcAdminAppController
{

    /**
     * メッセージ用機能名
     *
     * @var string
     */
    public $controlName;

    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->controlName = __d('banner', 'カスタムフィールド');

        [, $this->modelName] = pluginSplit($this->defaultTable);
        if ($this->modelName === 'Banner') return;
        $this->{$this->defaultTable} = TableRegistry::getTableLocator()->get($this->defaultTable);
    }


    /**
     * [ADMIN] 編集
     *
     * @param int $id
     * @return void
     */
    public function edit($id = null)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
            $this->redirect(['action' => 'index']);
        }
        $entity = $this->{$this->defaultTable}->get($id);
        if ($this->request->is(['post', 'put'])) {
            try {
                $entity = $this->{$this->defaultTable}->patchEntity($entity, $this->request->getData());
                $entity = $this->{$this->defaultTable}->saveOrFail($entity);
                if (!empty($entity->name)) {
                    $name = $entity->name;
                } elseif (!empty($this->blogContentDatas) && !empty($entity->content_id) && !empty($this->blogContentDatas[$entity->content_id])) {
                   $name = $this->blogContentDatas[$entity->content_id];
                } else {
                    $name = '';
                }

                $this->BcMessage->setSuccess(__d('カスタムフィールド設定', '{0}「{1}」を更新しました。', $this->controlName, $name));
                BcUtil::clearAllCache();
                return $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (BcException $e) {
                if ($e->getCode() === "23000") {
                    $this->BcMessage->setError(__d('baser_core', '同時更新エラーです。しばらく経ってから保存してください。'));
                } else {
                    $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
                }
            }
        }
        $this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
        $this->set('data', $entity);
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param int $id
     */
    public function ajax_delete($id = null)
    {
        if (!$id) {
            $this->ajaxError(500, '無効な処理です。');
        }
        // 削除実行
        if ($this->_delete($id)) {
            clearViewCache();
            exit(true);
        }
        exit;
    }
    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param int $id
     */
    public function ajax_unpublish($id)
    {
        if (!$id) {
            $this->ajaxError(500, '無効な処理です。');
        }
        if ($this->_changeStatus($id, false)) {
            clearViewCache();
            exit(true);
        } else {
            $this->ajaxError(500, $this->{$this->defaultTable}->validationErrors);
        }
        exit;
    }
    /**
     * [ADMIN] 有効状態にする（AJAX）
     *
     * @param int $id
     */
    public function ajax_publish($id)
    {
        if (!$id) {
            $this->ajaxError(500, '無効な処理です。');
        }
        if ($this->_changeStatus($id, true)) {
            clearViewCache();
            exit(true);
        } else {
            $this->ajaxError(500, $this->{$this->defaultTable}->validationErrors);
        }
        exit;
    }
}
