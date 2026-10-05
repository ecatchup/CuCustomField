<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class Initial extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up(): void
    {
        // cu_custom_field_configs
        $this->table('cu_custom_field_configs')
            ->addColumn('content_id', 'integer', [
                'comment' => 'コンテンツID',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'comment' => '利用状態',
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('form_place', 'string', [
                'comment' => 'フォーム表示位置',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'comment' => 'モデル名',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => '作成日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
        //  cu_custom_field_definitions
        $this->table('cu_custom_field_definitions')
            ->addColumn('config_id', 'integer', [
                'comment' => '設定ID',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('parent_id', 'integer', [
                'comment' => '親フィールド定義ID',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('label_name', 'string', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('field_name', 'string', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('field_type', 'string', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('preview_pref_list', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('status', 'boolean', [
                'comment' => '状態',
                'default' => 1,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('required', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('default_value', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('validate', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('validate_regex', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('validate_regex_message', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('size', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('max_length', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('counter', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('placeholder', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('rows', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('cols', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('editor_tool_type', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('choices', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('separator', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('auto_convert', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('google_maps_latitude', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('google_maps_longitude', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('google_maps_zoom', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('google_maps_text', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('prepend', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('append', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('option_meta', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => '作成日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
        //
        $this->table('cu_custom_field_values')
            ->addColumn('relate_id', 'integer', [
                'comment' => '関連コンテンツID',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('key', 'string', [
                'comment' => '保存キー',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('value', 'text', [
                'comment' => '保存値',
                'default' => null,
                'limit' => 4294967295,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'comment' => '保存モデル名',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => '作成日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down(): void
    {
        $this->table('cu_custom_field_configs')->drop()->save();
        $this->table('cu_custom_field_definitions')->drop()->save();
        $this->table('cu_custom_field_values')->drop()->save();
    }
}
