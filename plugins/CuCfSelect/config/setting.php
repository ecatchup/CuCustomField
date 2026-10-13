<?php
/**
 * CuCustomField : baserCMS Custom Field Select Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfSelect
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'選択' => [
// 			'select' => 'セレクトボックス'
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
             * BcCcSelect
             *
             * セレクトボックスを表示するフィールドタイプ
             */
            'CuCfSelect' => [
                'category' => __d('baser_core', '選択'),
                'label' => __d('baser_core', 'セレクトボックス'),
                'columnType' => 'string',
                'controlType' => 'select',
                'preview' => true,
                'useSource' => true,
                'loop' => true
            ]
        ]
    ]
];
