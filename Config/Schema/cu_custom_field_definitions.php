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
		'config_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '設定ID'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '親フィールド定義ID'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'label_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'field_name' => array('type' => 'string', 'null' => true, 'default' => null),
		'field_type' => array('type' => 'string', 'null' => true, 'default' => null),
		'preview_pref_list' => array('type' => 'text', 'null' => true, 'default' => null),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'required' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'default_value' => array('type' => 'text', 'null' => true, 'default' => null),
		'validate' => array('type' => 'text', 'null' => true, 'default' => null),
		'validate_regex' => array('type' => 'text', 'null' => true, 'default' => null),
		'validate_regex_message' => array('type' => 'text', 'null' => true, 'default' => null),
		'size' => array('type' => 'text', 'null' => true, 'default' => null),
		'max_length' => array('type' => 'text', 'null' => true, 'default' => null),
		'counter' => array('type' => 'text', 'null' => true, 'default' => null),
		'placeholder' => array('type' => 'text', 'null' => true, 'default' => null),
		'rows' => array('type' => 'text', 'null' => true, 'default' => null),
		'cols' => array('type' => 'text', 'null' => true, 'default' => null),
		'editor_tool_type' => array('type' => 'text', 'null' => true, 'default' => null),
		'choices' => array('type' => 'text', 'null' => true, 'default' => null),
		'separator' => array('type' => 'text', 'null' => true, 'default' => null),
		'auto_convert' => array('type' => 'text', 'null' => true, 'default' => null),
		'google_maps_latitude' => array('type' => 'text', 'null' => true, 'default' => null),
		'google_maps_longitude' => array('type' => 'text', 'null' => true, 'default' => null),
		'google_maps_zoom' => array('type' => 'text', 'null' => true, 'default' => null),
		'google_maps_text' => array('type' => 'text', 'null' => true, 'default' => null),
		'prepend' => array('type' => 'text', 'null' => true, 'default' => null),
		'append' => array('type' => 'text', 'null' => true, 'default' => null),
		'description' => array('type' => 'text', 'null' => true, 'default' => null),
		'option_meta' => array('type' => 'text', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);

}
