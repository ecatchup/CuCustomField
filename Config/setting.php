<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Config
 * @license          MIT LICENSE
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavi.cu_custom_field'] = [
	'name' => 'カスタムフィールド',
	'contents' => [
		['name' => '設定一覧',
			'url' => [
				'admin' => true,
				'plugin' => 'cu_custom_field',
				'controller' => 'cu_custom_field_configs',
				'action' => 'index']
		]
	]
];
$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'CuCustomField' => [
				'title' => 'カスタムフィールド',
				'url' => [
					'admin' => true,
					'plugin' => 'cu_custom_field',
					'controller' => 'cu_custom_field_configs',
					'action' => 'index',
				],
				'currentRegex' => '/\/cu_custom_field\/.+?/s'
			],
		]
	]];

/**
 * カスタムフィールド用設定
 *
 */
$config['cuCustomField'] = [
	// フィールドタイプ種別
	'field_type' => [
		'コンテンツ' => [
			'wysiwyg' => 'Wysiwyg Editor',
			'googlemaps' => 'Googleマップ'
		],
		'その他' => [
			'loop' => 'ループ'
		]
	],
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
];
/**
 * カスタムフィールド管理画面表示用設定
 *
 */
$config['cuCustomFieldConfig'] = [
	'submenu' => false
];
