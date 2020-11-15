<?php
/**
 * CuCustomField : baserCMS Custom Field File Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfFile.View.Helper
 * @license          MIT LICENSE
 */

/**
 * Class CuCfFileHelper
 *
 * @property BcHtmlHelper $BcHtml
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfFileHelper extends AppHelper {

	/**
	 * ファイルの保存URL
	 * @var string
	 */
	public $saveUrl = '/files/cu_custom_field/';

	/**
	 * helper
	 * @var string[]
	 */
	public $helpers = ['BcHtml'];

	/**
	 * Input
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function input ($fieldName, $definition, $options) {
		$options = array_merge([
			'type' => 'file'
		], $options);
		// ファイル
		$output = $this->CuCustomField->BcForm->input($fieldName, $options);
		// 保存値
		$value = $this->CuCustomField->value($fieldName);
		if ($value && is_string($value) && strpos($value, '.') !== false) {
			// 削除
			$delCheckTag = $this->BcHtml->tag('span',
				$this->CuCustomField->BcForm->checkbox($fieldName . '_delete', ['class' => 'bca-file__delete-input']) .
				$this->CuCustomField->BcForm->label($fieldName . '_delete', __d('baser', '削除する'))
			);
			// ファイルリンク
			list($name, $ext) = explode('.', $value);
			$thumb = $name . '_thumb.' . $ext;
			if(in_array($ext, ['png', 'gif', 'jpeg', 'jpg'])) {
				$fileLinkTag = '<figure class="bca-file__figure">' . $this->BcHtml->link(
					$this->BcHtml->image($this->saveUrl . $thumb, ['width' => 300]),
					$this->saveUrl . $value,
					['rel' => 'colorbox', 'escape' => false]
				) . '</figure>';
			} else {
				$fileLinkTag = '<p>' . $this->BcHtml->link(
					'ダウンロード',
					$this->saveUrl . $value,
					['target' => '_blank', 'class' => 'bca-btn']
				) . '</p>';
			}

			$output = $output . $delCheckTag . '<br>' . $fileLinkTag;
		}
		return $output;
	}

	/**
	 * Get
	 *
	 * @param mixed $fieldValue
	 * @param array $fieldDefinition
	 * @param array $options
	 * 	- output : 出力形式
	 * 		- tag : 画像の場合は画像タグ、ファイルの場合はリンク
	 * 		- url : ファイルのURL
	 * @return mixed
	 */
	public function get($fieldValue, $fieldDefinition, $options) {
		$options = array_merge([
			'output' => 'tag'
		], $options);

		if($fieldValue) {
			if($options['output'] === 'tag') {
				if(in_array(pathinfo($fieldValue, PATHINFO_EXTENSION), ['png', 'gif', 'jpeg', 'jpg'])) {
					$data = $this->uploadImage($fieldValue, $options);
				} else {
					$options['label'] = $fieldDefinition['name'];
					$data = $this->fileLink($fieldValue, $options);
				}
			} elseif($options['output'] === 'url') {
				$data = $this->saveUrl . $fieldValue;
			} else {
				$data = $fieldValue;
			}
		} else {
			$data = '';
		}
		return $data;
	}

	/**
	 * アップロード画像
	 * @param $fieldValue
	 * @param $options
	 * @return mixed|string
	 */
	public function uploadImage($fieldValue, $options)
	{
		$options = array_merge([
			'width' => (!empty($options['thumb']))? false : '100%',
			'thumb' => false
		], $options);
		$noValue = $options['novalue'];
		$thumb = $options['thumb'];

		unset($options['format'], $options['model'], $options['separator'], $options['novalue'], $options['thumb']);
		if(!$fieldValue) {
			return $noValue;
		} else {
			if($thumb) {
				$fieldValue = preg_replace('/^(.+\/)([^\/]+)(\.[a-z]+)$/', "$1$2_thumb$3", $fieldValue);
			}
			return $this->BcHtml->image($this->saveUrl . $fieldValue, $options);
		}
	}

	/**
	 * ファイルリンク
	 *
	 * @param string $fieldValue
	 * @param array $options
	 * @return mixed|string
	 */
	public function fileLink($fieldValue, $options) {
		$options = array_merge([
			'target' => '_blank',
			'label' => 'ダウンロード'
		], $options);
		$noValue = $options['novalue'];
		$label = $options['label'];
		unset($options['format'], $options['model'], $options['separator'], $options['novalue']);
		if(!$fieldValue) {
			return $noValue;
		} else {
			return $this->BcHtml->link($label, $this->saveUrl . $fieldValue, $options);
		}
	}

}
