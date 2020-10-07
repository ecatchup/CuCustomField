<?php

class CuCustomFieldConfigsSchema extends CakeSchema {

	public $file = 'cu_custom_field_configs.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {

	}

	public $cu_custom_field_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'content_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コンテンツID'),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '利用状態'),
		'form_place' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => 'フォームの表示位置'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => 'モデル名'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);

}
