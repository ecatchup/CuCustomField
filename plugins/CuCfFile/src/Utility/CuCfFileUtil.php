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
namespace CuCfFile\Utility;

use CuCustomField\Model\Entity\CuCustomFieldConfig;
use CuCustomField\Model\Table\CuCustomFieldDefinitionsTable;
use CuCustomField\Model\Table\CuCustomFieldValuesTable;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\NoTodo;

/**
 * Class CuCfFileUtil
 */
class CuCfFileUtil
{

    /**
     * アップローダーの準備を行う
     *
     * @param int $tableId
     * @checked
     * @noTodo
     */
    public static function setupUploader(int $tableId)
    {
        /** @var CustomLinksTable $linksTable */
        $linksTable = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldConfigs');
        $links = $linksTable->find()
            ->contain(['CuCustomFieldDefinitions'])
            ->where([
                'CuCustomFieldConfigs.content_id  ' => $tableId,
                // 'CustomFields.status' => true
            ])->first()->toArray();
        if(!$links) return;

        $fields = [];
        foreach($links['cu_custom_field_definitions'] as $link) {
            /** @var CustomLink $link */
            if($link['field_type'] === 'CuCfFile') {
                $fields[$link->name] = [
                    'type' => 'all',
                    'namefield' => 'id',
                    'nameformat' => '%08d',
                    'imageresize' => ['width' => 1000, 'height' => 1000],
                    'imagecopy' => [
                        'thumb' => ['suffix' => '_thumb', 'width' => 300, 'height' => 300]
                    ]
                ];
            }
        }

        if(!$fields) return;

		$config = [
			'saveDir' => 'cu_custom_field'. DS . 'BlogPost' . DS . $tableId ,
			'subdirDateFormat' => 'Y/m/',
			'fields' => $fields,
			'getUniqueFileName' => 'getUniqueFileName'
		];

        /** @var CustomEntriesTable $entriesTable */
        $entriesTable = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValues');
        $entriesTable->addBehavior('BaserCore.BcUpload', $config);
    }

}
