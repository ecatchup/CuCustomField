<?php
$config['cuCustomFieldConfig'] = [
    'submenu' => false,
    'customSearch' => true
];
/**
 * カスタムフィールド用設定
 */
$config['cuCustomField'] = [
    // エディターのタイプ
    'editor_tool_type' => [
        'simple' => 'Simple',
        'normal' => 'Normal',
    ],
    // 入力チェック種別
    'validate' => [
        'HANKAKU_CHECK' => '半角英数チェック',
        'NUMERIC_CHECK' => '数字チェック',
        'NONCHECK_CHECK' => 'チェックボックス未入力チェック',
        'REGEX_CHECK' => '正規表現チェック',
    ],
    // 文字変換種別
    'auto_convert' => [
        'NO_CONVERT' => 'しない',
        'CONVERT_HANKAKU' => '半角変換',
    ],
    'form_place' => [
        'normal' => 'コンテンツ編集領域の下部',
        'top' => 'コンテンツ編集領域の上部',
    ],
    // 必須選択
    'required' => [
        0 => '必須としない',
        1 => '必須とする',
    ],
    // ファイルタイプ制限
    'allow_file_exts' => ['jpg', 'png', 'gif', 'pdf'],

    'field_type' => [
        '基本' => [],
        '日付' => [],
        '選択' => [],
        'コンテンツ' => [],
        'その他' => [
            'loop' => 'ループ'
        ]
    ]
];
$config['BcApp']['adminNavigation'] = [
    'Plugins' => [
        'menus' => [
            'CuCustomField' => [
                'title' => 'カスタムフィールド',
                'url' => [
                    'admin' => true,
                    'plugin' => 'CuCustomField',
                    'controller' => 'CuCustomFieldConfigs',
                    'action' => 'index',
                ],
                //'currentRegex' => '/\/cu_custom_field\/.+?/s'
            ],
        ]
    ]];
return $config;
