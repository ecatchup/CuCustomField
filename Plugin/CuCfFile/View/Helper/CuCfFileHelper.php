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
	public $saveUrl = '/files/';

	/**
	 * helper
	 * @var string[]
	 */
	public $helpers = ['BcHtml'];

	/**
	 * Constructor
	 * @param View $View
	 * @param array $settings
	 */
	public function __construct(View $View, $settings = [])
	{
		parent::__construct($View, $settings);
		/* @var CuCustomFieldValue $valueModel */
		$valueModel = ClassRegistry::init('CuCustomField.CuCustomFieldValue');
		if(!isset($valueModel->Behaviors->CuCfFile->BcFileUploader)) {
			return;
		}
		if(empty($valueModel->Behaviors->CuCfFile->BcFileUploader->settings['saveDir'])) {
			if(BcUtil::isAdminSystem()) {
				$blogContent = $View->get('blogContent');
				$blogContentId = $blogContent['BlogContent']['id'];
			} else {
				$post = $View->get('post');
				$blogContentId = $post['BlogPost']['blog_content_id'];
			}
			if(!$blogContentId) {
				return;
			}
			$valueModel->setupFileUploader($blogContentId);
		}
		$this->saveUrl .= $this->getUploadSaveDir($valueModel);
	}

	/**
	 * 保存先のURLを取得する
	 * @param $valueModel
	 * @return string
	 */
	public function getUploadSaveDir($valueModel) {
		if(!empty($valueModel->Behaviors->CuCfFile->BcFileUploader->settings['saveDir'])) {
			$saveDir = $valueModel->Behaviors->CuCfFile->BcFileUploader->settings['saveDir'] . '/';
			$load = $this->_View->get('approverContentsMode');
			if(isset($this->_View->request->query['cu_approver_load'])) {
				$load = $this->_View->request->query['cu_approver_load'];
			}
			if(!empty($this->_View->request->query['preview']) &&
				!empty($this->_View->request->data['CuCustomFieldValue']) &&
				!empty($this->_View->request->data['CuApproverApplication']) &&
				$this->_View->request->data['CuApproverApplication']['contentsMode'] === 'draft') {
				$load = 'draft';
			}
			// 下書き画面にて、下書きデータが存在しなければ、本稿を表示する仕様としている為
			// 下書きデータが存在する場合のみ参照するURLを変更する
			if ($load === 'draft' && !empty($this->_View->request->data['CuApproverApplication']['draft'])) {
				if (preg_match('/^' . 'cu_approver_applications' . '/', $saveDir)) {
					return $saveDir;
				} else {
					// limited をつけると 存在するファイルとしてフレームワークに処理が渡らない
					return 'cu_approver_applications' . DS . $saveDir;
				}
			} else {
				return $saveDir;
			}
		}
		return '';
	}

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

		if (is_array($value)) {
			$oldValue = $this->value($fieldName . '_');
			if (empty($value['name'] && $oldValue)) {
				$value = $oldValue;
			}
		}

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
				) . '<br>' . $this->BcHtml->tag('figcaption', mb_basename($value), ['class' => 'bca-file__figcaption file-name']) . '</figure>';
			} else {
				$fileLinkTag = '<p>' . $this->BcHtml->link(
					'ダウンロード',
					$this->saveUrl . $value,
					['target' => '_blank', 'class' => 'bca-btn']
				) . '</p>' . $this->BcHtml->tag('figcaption', mb_basename($value), ['class' => 'bca-file__figcaption file-name']) . '</figure>';
			}
			$hidden = $this->CuCustomField->BcForm->input($fieldName . '_', ['type' => 'hidden', 'value' => $value]);
			$output .= $hidden . $delCheckTag . '<br>' . $fileLinkTag;
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
				$checkValue = $fieldValue;
				if(isset($options['tmp'])) {
					$checkValue = $options['tmp'];
				}
				if(is_string($checkValue) && in_array(pathinfo($checkValue, PATHINFO_EXTENSION), ['png', 'gif', 'jpeg', 'jpg'])) {
					$data = $this->uploadImage($fieldValue, $options);
				} else {
					$options['label'] = $fieldDefinition['name'];
					$data = $this->fileLink($fieldValue, $options);
				}
			} elseif($options['output'] === 'url') {
				$data = is_string($fieldValue) ? $this->saveUrl . $fieldValue : '';
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
			if(!empty($options['tmp'])) {
				$fileUrl = '/uploads/tmp/' . str_replace(['.', '/'], ['_', '_'], $options['tmp']);
			} else {
				$fileUrl = $this->saveUrl . $fieldValue;
			}
			return $this->BcHtml->image($fileUrl, $options);
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
		if(!$fieldValue || !is_string($fieldValue)) {
			return $noValue;
		} else {
			return $this->BcHtml->link($label, $this->saveUrl . $fieldValue, $options);
		}
	}

}
