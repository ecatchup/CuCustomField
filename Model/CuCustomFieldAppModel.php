<?php

/**
 * [Model] CuCustomFieldAppModel
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
class CuCustomFieldAppModel extends AppModel
{

	/**
	 * シリアライズされているデータを復元する
	 *
	 * @param array $data
	 * @return array
	 */
	public function unserializeData($data = array())
	{
		foreach ($data as $key => $value) {
			// TODO BcUtil::unserialize を利用するとエラーが発生するため通常のシリアライズを利用する
			if ($judge = @unserialize($value[$this->alias]['value'])) {
				$data[$key][$this->alias]['value'] = $judge;
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
	public function splitData($data = array())
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
