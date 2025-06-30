<?php
namespace CuCustomField\Model\Behavior;

use AllowDynamicProperties;
use BaserCore\Utility\BcFileUploader;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BaserCore\Vendor\Imageresizer;
use BcBlog\Model\Entity\BlogPost;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Laminas\Diactoros\UploadedFile;

/**
 * Class CuCfFileBehavior
 *
 * # プレビュー処理の仕様
 *
 * ## セッションへの保存
 * 1. Upload用セッションを削除
 * 2. ファイル名からファイルを特定するキーを生成
 * 3. そのキーをもとにUpload用セッションにコンテンツタイプと画像データを保存
 * 4. モデルのフィールドデータの配列に session_key をキーとして、ファイル名を格納
 *
 * ## ヘルパでの表示
 * 5. ファイル名のキーに session_key があれば、一時画像とみなしフラグを立てる
 * 6. 一時画像のフラグがたっていれば、画像のURLを UploadsControllerに切り替える
 * @property CuCustomFieldValue $CuCustomFieldValue
 * @property BlogPost $BlogPost
 * @property BcFileUploader $BcFileUploader
 * @uses CuCfFileBehavior
 */
#[AllowDynamicProperties]
class CuCfFileBehavior extends Behavior
{

    /**
     * oldEntity
     * @var array
     */
    public $oldEntity = [];

    /**
     * initialize
     * @param array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        // parent::initialize($config);
        $this->saveDir = WWW_ROOT . 'files' . DS . 'cu_custom_field' . DS;
        $this->config = $config;
        $this->config['type'] = 'BlogPost';
        $this->BcFileUploader = new BcFileUploader();
        $this->BlogPost = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
    }

    /**
     * Before Validate
     *
     * - id の設定
     * - ループフィールドのセッティング
     *
     * @param Model $model
     * @param array $options
     * @return bool|mixed|void
     */
    public function beforeValidate(Table $model, $options = [])
    {
        $model->data['CuCustomFieldValue']['id'] = (!empty($model->data['BlogPost']['id']))? $model->data['BlogPost']['id'] : null;
        $isDraft = false;
        if(!empty($model->data['CuApproverApplication']['contentsMode']) && $model->data['CuApproverApplication']['contentsMode'] === 'draft') {
            $isDraft = true;
        }
        $this->setupLoopFieldSettings($model->data['CuCustomFieldValue'], $isDraft);
    }

    /**
     * Before Validate
     *
     * - ループを平データに変換
     * - ポストデータをアップローダーにセットアップする
     * - 旧データを取得する
     *
     * @param Model $Model
     * @param array $options
     * @return boolean
     */
    public function afterValidate(Table $model, $options = [])
    {
        if($this->CuCustomFieldValue->validatingLock) return true;
        $data = $this->CuCustomFieldValue->convertToFlatteningData($model->data['CuCustomFieldValue']);
        $model->data['CuCustomFieldValue'] = $this->BcFileUploader->setupRequestData($data);
        $this->oldEntity = $this->CuCustomFieldValue->getOldEntity($model->data['CuCustomFieldValue']['id']);
        $this->CuCustomFieldValue->validatingLock = true;
        return true;
    }


    /**
     * Check Field
     * @param $model
     * @return bool
     */
    public function checkField($model, $data, $tmp = false)
    {
        if(isset($data['CuCustomFieldValue'])) {
            $data = $data['CuCustomFieldValue'];
        }
        if($data['key'] === 'CuCustomFieldValue.relate_id') {
            return false;
        }
        $key = $data['key'];
        $relateId = $data['relate_id'];
        $contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $relateId]);
        $definition = $model->getFieldDefinition($contentId, $key);
        if(!$definition || $definition['field_type'] !== 'loop') {
            $srcKey = $this->isDeleteAction($key);
            if($srcKey) {
                if($tmp) {
                    return false;
                }
                $this->checkAndDeleteFile($model, $relateId, $srcKey, $data['value'], null, $tmp);
                return false;
            } else {
                $model->data['CuCustomFieldValue']['value'] = $this->checkAndSaveFile($model, $relateId, $key, $data['value'], null, $tmp);
            }
        } else {
            $value = [];
            if($data['value']) {
                foreach($data['value'] as $i => $set) {
                    if($i === '__loop-src__') {
                        continue;
                    }
                    if (!$set) {
                        $value[$i] = $set;
                        break;
                    }
                    $deleteTarget = [];
                    foreach($set as $setKey => $setValue) {
                        if (!empty($deleteTarget[$setKey])) {
                            $value[$i][$setKey] = '';
                            continue;
                        }
                        $srcKey = $this->isDeleteAction($setKey);
                        if($srcKey) {
                            if($setValue) {
                                $deleteTarget[$srcKey] = true;
                            }
                            $this->checkAndDeleteFile($model, $relateId, 'CuCustomFieldValue.' . $srcKey, $setValue, $i, $tmp);
                        } else {
                            $result = $this->checkAndSaveFile($model, $relateId, 'CuCustomFieldValue.' . $setKey, $setValue, $i, $tmp);
                            if($result !== false) {
                                $value[$i][$setKey] = $result;
                            }
                        }
                    }
                }
            }
            $model->data['CuCustomFieldValue']['value'] = $value;
        }
        return true;
    }

    /**
     * 削除モードかチェックする
     * @param $key
     * @return false|mixed
     */
    public function isDeleteAction($key) {
        if (preg_match('/(.+)_delete$/', $key, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Check And Delete File
     * @param $model
     * @param $relateId
     * @param $key
     * @param $value
     * @param null $loopRow
     */
    public function checkAndDeleteFile($model, $relateId, $key, $value, $loopRow = null, $tmp = false) {
        if(empty($value)) {
            return;
        }
        $contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $relateId]);
        $definition = $model->getFieldDefinition($contentId, $key);
        if(!$definition || $definition['field_type'] !== 'file') {
            return;
        }
        $beforeValue = $this->getBeforeValue($model, $relateId, $this->getBareFieldName($key), $definition['parent_id'], $loopRow);
        if(is_null($loopRow)) {
            $targetRecord = $model->find('first', ['conditions' => ['relate_id' => $relateId, 'key' => $key], 'recursive' => -1]);
            $targetRecord['CuCustomFieldValue']['value'] = '';
            $model->save($targetRecord, ['callbacks' => false, 'validate' => false]);
        }
        $this->deleteFile($beforeValue, $tmp);
    }

    /**
     * Check And Save File
     * @param $model
     * @param $key
     * @param $value
     * @param $relateId
     * @param null $parentId
     * @param null $loopRow
     * @return false|string
     */
    public function checkAndSaveFile($model, $relateId, $key, $value, $loopRow = null, $tmp = false) {
        $contentId = $this->BlogPost->field('blog_content_id', ['BlogPost.id' => $relateId]);
        $definition = $model->getFieldDefinition($contentId, $key);
        if(!$definition || $definition['field_type'] !== 'file') {
            return $value;
        }
        $beforeValue = $this->getBeforeValue($model, $relateId, $this->getBareFieldName($key), $definition['parent_id'], $loopRow);
        return $this->saveFile($value, $beforeValue, $tmp);
    }

    /**
     * Get Bare Field Name
     * @param $fieldName
     * @return mixed|string
     */
    public function getBareFieldName($fieldName) {
        if(strpos($fieldName, '.') !== false) {
            list(, $fieldName) = explode('.', $fieldName);
        }
        return $fieldName;
    }

    /**
     * Get Before Value
     * @param $model
     * @param $relateId
     * @param $fieldName
     * @param null $parentId
     * @param null $loopRow
     * @return mixed|string
     */
    public function getBeforeValue($model, $relateId, $fieldName, $parentId = null, $loopRow = null) {
        if(!empty($parentId)) {
            // 親のフィールド名を取得
            $definitionModel = ClassRegistry::init('CuCustomField.CuCustomFieldDefinition');
            $parentName = $definitionModel->field('field_name', ['id' => $parentId]);
            $parentValue = $model->getSection($relateId, 'CuCustomFieldValue', $parentName);
            if(!empty($parentValue[$loopRow][$fieldName])) {
                $beforeValue = $parentValue[$loopRow][$fieldName];
            } else {
                $beforeValue = '';
            }
        } else {
            $beforeValue = $model->getSection($relateId, 'CuCustomFieldValue', $fieldName);
        }
        return $beforeValue;
    }

    /**
     * アップロードしたファイルを保存する
     * @param $value
     * @param $beforeValue
     * @param false $tmp
     * @return string|array
     */
    public function saveCuCfFile(int $blogContentId, UploadedFile $value, string $beforeValue = null, $tmp = false)
    {
        if (empty($value)) {
            return '';
        }
        if ($value->getError() !== 0) {
            return $beforeValue;
        }
        if($value->getSize() === 0) {
            return $beforeValue;
        }

        $file = [
            'name' => $value->getClientFilename(),
            'size' => $value->getSize(),
            'type' => $value->getClientMediaType(),
            'error' => $value->getError(),
            'tmp_name' => ($value->getError() === UPLOAD_ERR_OK)? $value->getStream()->getMetadata('uri') : '',
            'ext' => BcUtil::decodeContent($value->getClientMediaType(), $value->getClientFilename())
        ];

        $ext = BcUtil::decodeContent($file['type'], $file['name']);
        $year = date('Y');
        $month = date('m');
        $baseFileName = $this->config['type'] . '/' . $blogContentId . '/' . $year . '/' . $month . '/' . Text::uuid();
        $fileName = $baseFileName . '.' . $ext;

        if($tmp) {
            $_fileName = str_replace(array('.', '/'), array('_', '_'), $fileName);
            $this->Session->write('Upload.' . $_fileName . '.type', $file['type']);
            $this->Session->write('Upload.' . $_fileName . '.data', file_get_contents($file['tmp_name']));
            $file['session_key'] = $fileName;
            return $file;
        } else {
            $Folder = new BcFolder();
            $Folder->create($this->saveDir . $this->config['type'] . DS . $blogContentId . DS . $year . DS . $month . DS, 0777);
            if (in_array($ext, ['png', 'gif', 'jpeg', 'jpg'])) {
                $thumbName = $baseFileName . '_thumb.' . $ext;
                $imageresizer = new Imageresizer();
                $imageresizer->resize($file['tmp_name'], $this->saveDir . $fileName, 1000, 1000, false);
                $imageresizer->resize($this->saveDir . $fileName, $this->saveDir . $thumbName, 300, 300, false);
            } else {
                move_uploaded_file($file['tmp_name'], $this->saveDir . $fileName);
                chmod($this->saveDir . $fileName, 0666);
            }
            if ($beforeValue) {
                $this->deleteCuCfFile($beforeValue);
            }
        }
        return $fileName;
    }

    /**
     * ファイルを削除する
     *
     * @param string $value
     * @return |null
     */
    public function deleteCuCfFile($value, $tmp = false)
    {
        if (!$value || strpos($value, '.') === false) {
            return false;
        }

        if(!$tmp) {
            $filePath = $this->saveDir . $value;
            list($baseFileName, $ext) = explode('.', $value);
            $thumbPath = $this->saveDir . $baseFileName . '_thumb.' . $ext;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
        return null;
    }

    /**
     * セッションに一時ファイルを保存
     * @param Model $Model
     * @param $data
     * @return mixed
     */
    public function saveTmpFile(Model $Model, $data)
    {
        $this->Session->delete('Upload');
        if(isset($data['CuCustomFieldValue']) && $data['CuCustomFieldValue']) {
            foreach ($data['CuCustomFieldValue'] as $field => $value) {
                $newDetail = [];
                $section = 'CuCustomFieldValue';
                $key = $section . '.' . $field;
                $newDetail['relate_id'] = $data['BlogPost']['id'];
                $newDetail['key'] = $key;
                $newDetail['value'] = $value;
                $newDetail['model'] = 'CuCustomFieldValue';
                $this->checkField($Model, $newDetail, true);
                if(isset($Model->data['CuCustomFieldValue']['value'])) {
                    $data['CuCustomFieldValue'][$field] = $Model->data['CuCustomFieldValue']['value'];
                }
            }
        }
        return $data;
    }
}
