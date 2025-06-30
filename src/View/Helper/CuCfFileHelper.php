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
namespace CuCustomField\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use BaserCore\View\AppView;
use BaserCore\View\Helper\BcAdminFormHelper;
use CuCfFile\Utility\CuCfFileUtil;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\Helper\IdGeneratorTrait;
use CuCustomField\Model\Behavior\CuCfFileBehavior;


/**
 * Class CuCfFileHelper
 *
 * @property BcHtmlHelper $BcHtml
 * @property CuCustomFieldHelper $CuCustomField
 */
class CuCfFileHelper extends Helper
{
    use IdGeneratorTrait;

	/**
	 * ファイルの保存URL
	 * @var string
	 */
	public $saveUrl = '/files/';

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form'],
        'BaserCore.BcForm',
        'BaserCore.BcHtml',
        'CuCustomField.CuCustomField',
    ];

	/**
	 * Constructor
	 * @param View $View
	 * @param array $settings
	 */
	public function __construct(AppView $View, $settings = [])
	{
		parent::__construct($View, $settings);
		/* @var CuCustomFieldValue $valueModel */
		$valueModel = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldValues');
		if(!isset($valueModel->getBehavior('CuCfFile')->BcFileUploader)) {
			return;
		}
		if(empty($valueModel->getBehavior('CuCfFile')->BcFileUploader->settings['saveDir'])) {
			if(BcUtil::isAdminSystem()) {
				$blogContent = $View->get('blogContent');
				$blogContentId = $blogContent['id'];
			} else {
				$post = $View->get('post');
				$blogContentId = !empty($post) ? $post->blog_content_id : null;
			}
			if(!empty($blogContentId)) {
				return;
			}
			$this->setupFileUploader($valueModel, $blogContentId);
		}
		$this->saveUrl .= $this->getUploadSaveDir($valueModel);
	}
    /**
	 * @param $modelName
	 * @return BcFileUploader|false
	 */
	public function getFileUploader()
	{
		return $this->BcFileUploader;
	}

    /**
	 * setupFileUploader
	 * @param Model $model
	 * @param int $blogPostId
	 */
	public function setupFileUploader(Table $model, $contentId)
	{
		$definitions = $model->getFieldDefinition($contentId);
		$fields = [];
		if(!empty($definitions)) {
			foreach($definitions as $definition) {
				if ($definition['field_type'] === 'file' && !$definition['parent_id']) {
					$fields[$definition['field_name']] = [
						'type' => 'all',
						'namefield' => 'no',
						'nameformat' => '%08d',
						'imagecopy' => [
							'large' => ['suffix' => '_large', 'width' => 1000, 'height' => 1000],
							'thumb' => ['suffix' => '_thumb', 'width' => 300, 'height' => 300],
						]
					];
				}
			}
		}
		$config = [
			'saveDir' => 'cu_custom_field' . DS . 'blog' . DS . $contentId . DS . 'blog_posts',
			'subdirDateFormat' => 'Y/m/',
			'fields' => $fields,
			'getUniqueFileName' => 'getUniqueFileName'
		];
        $model->getBehavior('CuCfFile')->BcFileUploader->initialize($config, $model);
	}
	/**
	 * 保存先のURLを取得する
	 * @param $valueModel
	 * @return string
	 */
	public function getUploadSaveDir($valueModel) {

		if(!empty($valueModel->getBehavior('CuCfFile')->BcFileUploader->getSettings()['saveDir'])) {
			$saveDir = $valueModel->getBehavior('CuCfFile')->BcFileUploader->getSettings()['saveDir'] . '/';
			$load = $this->_View->get('approverContentsMode');
			if($this->_View->request->getQuery('cu_approver_load')) {
				$load = $this->_View->request->getQuery('cu_approver_load');
			}
			if(!empty($this->_View->request->getQuery('preview') )&&
				!empty($this->_View->request->getData('CuCustomFieldValue')) &&
				!empty($this->_View->request->getData('CuApproverApplication')) &&
				$this->_View->request->getData('CuApproverApplication.contentsMode') === 'draft') {
				$load = 'draft';
			}
			// 下書き画面にて、下書きデータが存在しなければ、本稿を表示する仕様としている為
			// 下書きデータが存在する場合のみ参照するURLを変更する
			if ($load === 'draft' && !empty($this->_View->request->getData('CuApproverApplication.draft'))) {
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
     * control
     *
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $options = array_merge([
            'type' => 'file',
            'imgsize' => 'thumb'
        ], $options);
        return $this->BcAdminForm->control($link->name, $options);
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
		$output = $this->BcAdminForm->input($fieldName, $options);
        //	保存値
        $value = $this->BcAdminForm->getSourceValue($fieldName);

        if (is_array($value)) {
            $oldValue = $this->value($fieldName . '_');
            if (empty($value['name'] && $oldValue)) {
                $value = $oldValue;
            }
        }

        if ($value && is_string($value) && strpos($value, '.') !== false) {
        // 削除
        $delCheckTag = $this->BcHtml->tag('span',
            $this->BcAdminForm->checkbox($fieldName . '_delete', ['class' => 'bca-file__delete-input', 'id' => $this->_domId($fieldName.'_delete')]) .
            $this->BcAdminForm->label($fieldName . '_delete', __d('baser', '削除する')) . '<br>'
        );
        // ファイルリンク
        list($name, $ext) = explode('.', $value);
        $thumb = 'cu_custom_field/'. $name . '_thumb.' . $ext;
        if(in_array($ext, ['png', 'gif', 'jpeg', 'jpg'])) {
            $fileLinkTag = '<figure class="bca-file__figure">' . $this->BcHtml->link(
                $this->BcHtml->image($this->saveUrl . $thumb, ['width' => 300]),
                $this->saveUrl . 'cu_custom_field/' . $value,
                ['rel' => 'colorbox', 'escape' => false]
            ) . '<br>' . '</figure>'; // カスタムフィールドではファイル名がUUIDに置換されるので表示不要
        } else {
            $fileLinkTag = '<p>' . $this->BcHtml->link(
                'ダウンロード',
                $this->saveUrl . $value,
                ['target' => '_blank', 'class' => 'bca-btn']
            ) . '</p>' . '</figure>'; // カスタムフィールドではファイル名がUUIDに置換されるので表示不要
        }
        $hidden = $this->BcAdminForm->input($fieldName . '_', ['type' => 'hidden', 'value' => $value]);
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
			return $this->BcHtml->link($label, '/files/' . $fieldValue, $options);
		}
	}

}
