<?php
/**
 * CuCustomField : baserCMS Custom Field Textarea Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfTextarea
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['cuCustomField'] = [
// 	'field_type' => [
// 		'基本' => [
// 			'textarea' => 'テキストエリア'
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
             * BcCcTextarea
             *
             * テキストエリアを表示するフィールドタイプ
             */
            'CuCfTextarea' => [
                'category' => __d('baser_core', '基本'),
                'label' => __d('baser_core', 'テキストエリア'),
                'columnType' => 'text',
                'controlType' => 'textarea',
                'preview' => true,
                'useSize' => true,
                'useLine' => true,
                'useMaxLength' => true,
                'useAutoConvert' => true,
                'useCounter' => true,
                'usePlaceholder' => true,
                'useCheckNumber' => true,
                'useCheckHankaku' => true,
                'useCheckZenkakuKatakana' => true,
                'useCheckZenkakuHiragana' => true,
                'useCheckRegex' => true,
                'loop' => true
            ]
        ]
    ]
];
