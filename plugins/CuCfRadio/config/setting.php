<?php
/**
 * CuCustomField : baserCMS Custom Field Radio Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRadio
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'選択' => [
// 			'radio' => 'ラジオボタン'
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
             * BcCcRadio
             *
             * ラジオボタンを表示するフィールドタイプ
             */
            'CuCfRadio' => [
                'category' => __d('baser_core', '選択'),
                'label' => __d('baser_core', 'ラジオボタン'),
                'columnType' => 'string',
                'controlType' => 'radio',
                'useSource' => true,
                'preview' => true,
                'loop' => true
            ]
        ]
    ]
];
