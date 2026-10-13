<?php
/**
 * CuCustomField : baserCMS Custom Field Pref Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfPref
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'選択' => [
// 			'pref' => '都道府県リスト'
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
             * BcCcPref
             *
             * 都道府県リストを表示するフィールドタイプ
             */
            'CuCfPref' => [
                'category' => __d('baser_core', '選択'),
                'label' => __d('baser_core', '都道府県リスト'),
                'columnType' => 'string',
                'controlType' => 'select',
                'preview' => true,
                'loop' => true
            ]
        ]
    ]
];
