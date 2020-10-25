<?php
/**
 * CuCustomField : baserCMS Custom Field Related Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfRelated.Model
 * @license          MIT LICENSE
 */

/**
 * Class CuCfRelated
 */
class CuCfRelated extends AppModel {

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
		$db = $this->getDataSource();
		return $db->describe($db->config['prefix'] . $table);
	}

	/**
	 * フィールドの存在チェック
	 * @param string $table
	 * @param string $field
	 * @return bool
	 */
	public function existField($table, $field) {
		$db = $this->getDataSource();
		$schema = $db->describe($db->config['prefix'] . $table);
		foreach($schema as $key => $value) {
			if($key === $field) {
				return true;
			}
		}
		return false;
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
		if(!$this->existTable($table) || !$this->existField($table, $titleField)) {
			return false;
		}
		$db = $this->getDataSource();
		$prefixedTable = $db->config['prefix'] . $table;
		$sql = "SELECT id, {$titleField} FROM {$prefixedTable}";
		$params = [];
		if($whereField && $this->existField($table, $whereField)) {
			$sql .= " WHERE {$whereField} = ?";
			$params[] = $whereValue;
		}
		$record = $db->query($sql, $params);
		return Hash::combine($record, "{n}.{$prefixedTable}.id", "{n}.{$prefixedTable}." . $titleField);
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
		$db = $this->getDataSource();
		$prefixedTable = $db->config['prefix'] . $table;
		$sql = "SELECT * FROM {$prefixedTable}";
		$sql .= " WHERE id = ?";
		$params[] = $id;
		$record = $db->query($sql, $params);
		if($record) {
			return $record[0][$prefixedTable];
		}
		return false;
	}
}
