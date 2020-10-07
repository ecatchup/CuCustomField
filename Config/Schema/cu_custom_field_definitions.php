<?php

class CuCustomFieldDefinitionsSchema extends CakeSchema {

	public $file = 'cu_custom_field_definitions.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {

	}

	public $cu_custom_field_definitions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'foreign_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メタテーブル用外部キー'),
		'key' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '保存キー'),
		'value' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '保存値'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '保存モデル名'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);

}
