<?php
/**
 * CuCustomField : baserCMS Custom Field Wysiwyg Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfWysiwyg
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'コンテンツ' => [
// 			'wysiwyg' => 'Wysiwyg Editor'
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
             * BcCcWysiwyg
             *
             * Wysiwyg を表示するフィールドタイプ
             */
            'CuCfWysiwyg' => [
                'category' => __d('baser_core', 'コンテンツ'),
                'label' => 'Wysiwyg エディタ',
                'columnType' => 'text',
                'controlType' => 'text',
                'preview' => true
            ]
        ]
    ]
];
