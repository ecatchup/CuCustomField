<?php
/**
 * CuCustomField : baserCMS Custom Field Related Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRelated
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'選択' => [
// 			'related' => '関連データ'
// ]]];
return [
    /**
     * カスタムコンテンツ設定
     *
     * 各フィールドの設定値についての説明は、BcCcText プラグインの setting.php を参考にする
     */
    'CuCustomField' => [
        'fieldTypes' => [
            /**
             * BcCcRelated
             *
             * 関連データを表示するフィールドタイプ
             */
            'CuCfRelated' => [
                'category' => __d('baser_core', '選択'),
                'label' => __d('baser_core', '関連データ'),
                'columnType' => 'string',
                'controlType' => 'select',
                'preview' => true,
                'loop' => true
            ]
        ]
    ]
];
