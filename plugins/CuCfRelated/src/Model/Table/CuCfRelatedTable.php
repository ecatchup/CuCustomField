<?php
namespace CuCfRelated\Model\Table;
/**
 * CuCustomField : baserCMS Custom Field Related Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRelated.Model
 * @license          MIT LICENSE
 */

use BaserCore\Model\Table\AppTable;
use BaserCore\Utility\BcUtil;
use Cake\Utility\Hash;

/**
 * Class CuCfRelated
 */
class CuCfRelatedTable extends AppTable {

	/**
	 * テーブルを利用するかどうか
	 * @var bool
	 */
	public $useTable = false;

	/**
	 * テーブルの存在チェック
	 *
	 * @param string $table
	 * @return array|null
	 */
	public function existTable($table) {
        if(!$table){
            return false;
        }

        $db = $this->getConnection();
        $collection = $db->getSchemaCollection();
		return $collection->describe($db->config()['prefix'] . $table);
	}

	/**
	 * フィールドの存在チェック
	 * @param string $table
	 * @param string $field
	 * @return bool
	 */
	public function existField($table, $field) {
        $db = $this->getConnection();
        $collection = $db->getSchemaCollection($table);
		$schema = $collection->describe($db->config()['prefix'] . $table);

		return $schema->getColumn($field);
	}

	/**
	 * 関連データのリストを取得
	 *
	 * @param string $table
	 * @param string $titleField
	 * @param string $whereField
	 * @param string $whereValue
	 * @return array|false
	 */
	public function getRelatedList($table, $titleField, $whereField, $whereValue) {
        // 5系からはnameはtitleへ変更されている
        if(BcUtil::getVersion() >= '5.0.0'){
            if($titleField === 'name'){
                $titleField = 'title';
            }
        }

        if(!$this->existTable($table) || !$this->existField($table, $titleField)) {
			return [];
		}

		$db = $this->getConnection();
		$prefixedTable = $db->config()['prefix'] . $table;
		$sql = "SELECT id, {$titleField} FROM {$prefixedTable}";
		$params = [];
		if($whereField && $this->existField($table, $whereField)) {
			$sql .= " WHERE {$whereField} = ?";
			$params[] = empty($whereValue) ? '0' : $whereValue;
		}

        // 取得結果をid => titleの形式へ加工
		$statement = $db->execute($sql, $params);
		return Hash::combine($statement->fetchAll(), "{n}.0", "{n}.1");
	}

	/**
	 * 関連データを取得
	 *
	 * @param string $table
	 * @param int $id
	 * @return array|false
	 */
	public function getRelatedRecord($table, $id) {
		if(!$this->existTable($table)) {
			return false;
		}
		$db = $this->getConnection();
        $prefixedTable = $db->config()['prefix'] . $table;
		$sql = "SELECT * FROM {$prefixedTable}";
		$sql .= " WHERE id = ?";
		$params[] = $id;
		$record = $db->execute($sql, $params);
		if($record) {
			return $record[0][$prefixedTable];
		}
		return false;
	}
}
