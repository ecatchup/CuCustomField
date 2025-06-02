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
App::uses('CuCustomField.CuCustomFieldAppModel', 'Model');

/**
 * Class CuCustomFieldConfig
 */
class CuCustomFieldConfig extends CuCustomFieldAppModel
{

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = [
		'CuCustomFieldDefinition' => [
			'className' => 'CuCustomField.CuCustomFieldDefinition',
			'foreignKey' => 'config_id',
			'order' => ['CuCustomFieldDefinition.lft' => 'ASC'],
			'dependent' => true,
		],
	];

	/**
	 * 初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data = [
			'CuCustomFieldConfig' => [
				'status' => true,
				'form_place' => 'normal',
			]
		];
		return $data;
	}

	/**
	 * カスタムフィールドの設定を保存する
	 *
	 */
	public function beforeDelete($cascade = true)
	{
		// 削除するフィールドの情報を保存
		$this->deleteData = $this->read();
        return true;
    }

	/**
	 * カスタムフィールドの設定を削除する
	 *
	 */
	public function afterDelete($cascade = true)
	{
		// CuCustomFieldValueモデルのロード
		$CuCustomFieldValue = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		// blogpostsモデルのロード　
		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		// 消したいデータのdefinitionsを取得する
		$deleteData = $this->deleteData;

		// そのコンテンツidに属するblogpostテーブルのidのみ全取得
		$deleteBlogPosts = $BlogPost->find('list',[
			'fields' => ['BlogPost.id'],
			'conditions' => ['blog_content_id' => $deleteData["CuCustomFieldConfig"]["content_id"]],
			'recursive' => -1,
		]);

		// 削除条件を作成
		$conditions = [
			'CuCustomFieldValue.relate_id' => array_values($deleteBlogPosts),
		];

		if ($CuCustomFieldValue->deleteAll($conditions, false)) {
			clearViewCache();
		}
		return true;
	}
}
