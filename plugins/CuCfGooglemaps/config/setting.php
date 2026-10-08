<?php
/**
 * CuCustomField : baserCMS Custom Field Googlemaps Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfGooglemaps
 * @license          MIT LICENSE
 */

/**
 * フィールドタイプ
 */
// $config['CuCustomField'] = [
// 	'field_type' => [
// 		'コンテンツ' => [
// 			'googlemaps' => 'Googleマップ'
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
             * BcCcTel
             *
             * Googleマップ用テキストボックスを表示するフィールドタイプ
             */
            'CuCfGooglemaps' => [
                'category' => __d('baser_core', '基本'),
                'label' => __d('baser_core', 'Googleマップ'),
                'columnType' => 'string',
                'controlType' => 'text',
                'preview' => true,
                'useSize' => true,
                'useMaxLength' => true,
                'useAutoConvert' => true,
                'usePlaceholder' => true,
                'useCheckNumber' => true,
                'loop' => true
            ]
        ]
    ]
];
