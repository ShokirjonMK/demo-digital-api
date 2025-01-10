<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $name
 * @property int $building_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Building $building
 * @property TimeTable1[] $timeTables
 */
class Document extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 10; // 10 Mb
    public $fileExtension = 'pdf';

    const UPLOADS_FOLDER = 'uploads/document/';


    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['document_type_id'], 'required'],
            [['title','file','doc_number','access_doc_number','registr_number'], 'string' , 'max' => 255],
            [['description'], 'safe'],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
            [['qr_type','coming_type','document_weight_id','document_type_id','user_id','start_date','end_date', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['document_weight_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentWeight::className(), 'targetAttribute' => ['document_weight_id' => 'id']],
            [['document_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentType::className(), 'targetAttribute' => ['document_type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'document_weight_id',
            'document_type_id',
            'qr_type',
            'coming_type',
            'user_id',
            'doc_number',
            'access_doc_number',
            'registr_number',
            'title',
            'file',
            'start_date',
            'end_date',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];

        return $fields;
    }


    public function extraFields()
    {
        $extraFields =  [
            'user',
            'files',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getFiles()
    {
        return $this->hasMany(DocumentFiles::className(), ['document_id' => 'id'])->onCondition(['is_deleted' => 0, 'status' => 1]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $data = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('file');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $url = $model->upload($model->upload);
            if ($url) {
                $model->file = $url;
                $data[] = $url;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->save(false)) {
            $model->upload = UploadedFile::getInstancesByName('upload');
            if ($model->upload) {
                foreach ($model->upload as $item) {
                    $new = new DocumentFiles();
                    $new->document_id = $model->id;
                    $new->upload = $item;
                    $url = $new->upload();
                    if ($url) {
                        $new->file = $url;
                        $data[] = $url;
                    }
                    if (!($new->validate())) {
                        $errors[] = $new->errors;
                    } else {
                        $new->save(false);
                    }
                }
            }
        } else {
            $errors[] = _e("Data not saved.");
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        if (count($data) > 0) {
            foreach ($data as $file) {
                $model->deleteFile($file);
            }
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('file');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $url = $model->upload($model->upload);
            if ($url) {
                $model->file = $url;
            } else {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if ($model->save(false)) {
            $model->upload = UploadedFile::getInstancesByName('upload');
            if ($model->upload) {
                foreach ($model->upload as $item) {
                    $new = new DocumentFiles();
                    $new->document_id = $model->id;
                    $new->upload = $item;
                    $url = $new->upload();
                    if ($url) {
                        $new->file = $url;
                    }
                    if (!($new->validate())) {
                        $errors[] = $new->errors;
                    } else {
                        $new->save(false);
                    }
                }
            }
        } else {
            $errors[] = _e("Data not saved.");
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->upload->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
