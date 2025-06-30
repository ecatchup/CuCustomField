<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace CuCfFile\Event;

use ArrayObject;
use BaserCore\Error\BcException;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\Utility\BcContainerTrait;
use CuCfFile\Utility\CuCfFileUtil;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CuCfFileControllerEventListener
 */
class CuCfFileControllerEventListener extends BcControllerEventListener
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Event
     *
     * @var string[]
     */
    public $events = [
        // 'BcBlog.BlogPosts.startup',
        //'BcBlog.Blog.beforeRender'
    ];

    /**
     * CuCustomFieldCuCfFileStartup
     *
     * @param EventInterface $event
     * @param ArrayObject $content
     * @param ArrayObject $options
     */
    public function bcBlogBlogPostsStartup(EventInterface $event)
    {

        $controller = $event->getSubject();
        $request = $controller->getRequest();
        if($controller->getPlugin() !== 'BcBlog'){
            return;
        }

        if(!$this->isAction(['BlogPosts.Add', 'BlogPosts.Edit', 'Blog.Archives'])) {
            return;
        }
        $tableId = $request->getParam('pass.0');
        if (!$tableId) $tableId = $request->getQuery('config_id');
        CuCfFileUtil::setupUploader((int) $tableId);



        // debug($request->getParams());
        // /* @var CuCustomFieldValue $CuCustomFieldValue */
        // if(empty($controller->blogContent['BlogContent']['id'])) {
        //     return;
        // }
        /* @var CuCustomFieldValue $CuCustomFieldValue */
        // $CuCustomFieldValue = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValues');
        // debug($CuCustomFieldValue);
        // $CuCustomFieldValue->addBehavior('CuCfFile.CuCfFile', ['foreignKeyField' => 'relate_id']);
        // // $CuCustomFieldValue->Behaviors->load('CuCfFile.CuCfFile', [
        // //     'type' => 'BlogPost',
        // //     'contentId' => 3//$controller->blogContent['BlogContent']['id']
        // // ]);
    }

    /**
     * BcCustomContent CustomEntries Before Render
     *
     * @param Event $event
     */
    public function bcBlogBlogBeforeRender(Event $event)
    {
        /** @var Controller $controller */
        $controller = $event->getSubject();
        if($this->isAction('Index', false)) {
            $table = $controller->viewBuilder()->getVar('customTable');
            if(!$table) throw new BcException(__d('baser_core', 'ビュー変数 $customTable がセットされていません。'));
            CuCfFileUtil::setupUploader($table->id);
        } elseif($this->isAction('View', false)) {
            $entry = $controller->viewBuilder()->getVar('customEntry');
            if(!$entry) throw new BcException(__d('baser_core', 'ビュー変数 $customEntry がセットされていません。'));
            CuCfFileUtil::setupUploader($entry->custom_table_id);
        }
    }

}
