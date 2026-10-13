<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class FixV4SchemaCompatibility extends BcMigration
{
    public function up(): void
    {
        if ($this->hasTable('cu_custom_field_configs')) {
            $configs = $this->table('cu_custom_field_configs');
            if ($configs->hasColumn('status')) {
                $configs->changeColumn('status', 'boolean', [
                    'comment' => '利用状態',
                    'default' => true,
                    'null' => true,
                ])->update();
            }
        }

        if ($this->hasTable('cu_custom_field_definitions')) {
            $definitions = $this->table('cu_custom_field_definitions');

            if (!$definitions->hasColumn('field_type') && $definitions->hasColumn('type')) {
                $definitions->addColumn('field_type', 'string', [
                    'default' => null,
                    'null' => true,
                ])->update();
                $this->execute('UPDATE cu_custom_field_definitions SET field_type = type WHERE field_type IS NULL');
                $definitions = $this->table('cu_custom_field_definitions');
                $definitions->removeColumn('type')->update();
            }

            $definitions = $this->table('cu_custom_field_definitions');
            if (!$definitions->hasColumn('counter')) {
                $definitions->addColumn('counter', 'text', [
                    'default' => null,
                    'null' => true,
                ])->update();
            }

            $definitions = $this->table('cu_custom_field_definitions');
            if ($definitions->hasColumn('status')) {
                $definitions->changeColumn('status', 'boolean', [
                    'comment' => '状態',
                    'default' => true,
                    'null' => true,
                ])->update();
            }
        }
    }

    public function down(): void
    {
        // 4系完全互換を維持するため、down では戻さない
    }
}
