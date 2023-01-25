<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.Model
 * @license          MIT LICENSE
 */

/**
 * Class CuCustomFieldAppModel
 */
class CuCustomFieldAppModel extends AppModel
{

	/**
	 * シリアライズされているデータを復元する
	 *
	 * @param array $data
	 * @return array
	 */
	public function unserializeData($data = [])
	{
		foreach($data as $key => $value) {
			if(isset($value[$this->alias]['value']) && preg_match('/^[a-zA-Z]:/', $value[$this->alias]['value'], $matches)) {
				if ($judge = unserialize($value[$this->alias]['value'])) {
					$data[$key][$this->alias]['value'] = $judge;
				}
			}
		}
		return $data;
	}

	/**
	 * 半角パイプで区切られたデータを配列に変換する
	 *
	 * @param array $data
	 * @return array
	 */
	public function splitData($data = [])
	{
		if ($data) {
			if (!empty($data['field_type']) && $data['field_type'] == 'multiple') {
				if (!empty($data['default_value'])) {
					$data['default_value'] = explode('|', $data['default_value']);
				}
			}
		}
		return $data;
	}

}
