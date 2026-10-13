<?php
/**
 * CuCustomField : baserCMS Custom Field Multiple Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfMultiple
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'選択' => [
// 			'multiple' => 'マルチチェックボックス'
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
             * BcCcMultiple
             *
             * マルチチェックボックスを表示するフィールドタイプ
             */
            'CuCfMultiple' => [
                'category' => __d('baser_core', '選択'),
                'label' => __d('baser_core', 'マルチチェックボックス'),
                'columnType' => 'string',
                'controlType' => 'multiCheckbox',
                'preview' => true,
                'useSource' => true,
                'loop' => true
            ]
        ]
    ]
];
