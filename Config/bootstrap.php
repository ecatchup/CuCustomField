<?php
App::uses('CuCustomFieldUtil', 'CuCustomField.Lib');
// フィールドタイプの分類の並べ順をプラグインより優先するため field_type だけはここで定義
Configure::write(['cuCustomField' => [
	'field_type' => [
		'基本' => [],
		'日付' => [],
		'選択' => [],
		'コンテンツ' => [],
		'その他' => [
			'loop' => 'ループ'
		]
]]]);
CuCustomFieldUtil::loadPlugin();
