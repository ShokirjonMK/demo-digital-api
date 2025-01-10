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
 * @property string $description
 * @property int $letter_id
 * @property int $documant_weight_id
 * @property int $important_level_id
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
class Letter extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 10; // 10 Mb
    public $fileExtension = 'pdf , doc, docx';

    const UPLOADS_FOLDER = 'uploads/document/letter/';

    const IS_OK_DEFAULT = 0;
    const IS_OK_TRUE = 1;
    const IS_OK_CANCEL = 2;


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
        return 'letter';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['document_weight_id', 'important_level_id', 'description'], 'required'],
            [['file'], 'string' , 'max' => 255],
            [['description','message'], 'safe'],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
            [['document_weight_id','important_level_id','user_id' ,'sent_time', 'is_ok' , 'is_ok_date' , 'view_type' , 'view_date','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['document_weight_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentWeight::className(), 'targetAttribute' => ['document_weight_id' => 'id']],
            [['important_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => ImportantLevel::className(), 'targetAttribute' => ['important_level_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'document_weight_id',
            'important_level_id',
            'description',
            'user_id',
            'is_ok',
            'is_ok_date',
            'view_type',
            'view_date',
            'sent_time',
            'message',
            'file',
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
            'files',
            'user',
            'documentWeight',
            'importantLevel',
            'letterViews',
            'letterOutgoing',
            'letterOutgoingHistory',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getFiles()
    {
        return $this->hasMany(LetterFiles::className(), ['letter_id' => 'id'])->onCondition(['is_deleted' => 0, 'status' => 1]);
    }

    public function getLetterViews()
    {
        return $this->hasMany(LetterView::className(), ['letter_id' => 'id'])->onCondition(['is_deleted' => 0, 'status' => 1]);
    }

    public function getDocumentWeight()
    {
        return $this->hasOne(DocumentWeight::className(), ['id' => 'document_weight_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLetterOutgoing()
    {
        return $this->hasOne(LetterOutgoing::className(), ['letter_id' => 'id'])->where(['is_deleted' => 0])->orderBy('id desc');
    }

    public function getLetterOutgoingHistory()
    {
        return LetterOutgoing::find()
            ->where(['is_deleted' => 0,'letter_id' => $this->id])
            ->andWhere(['!=', 'id' , $this->letterOutgoing ? $this->letterOutgoing->id : 0])
            ->all();
    }

    public function getImportantLevel()
    {
        return $this->hasOne(ImportantLevel::className(), ['id' => 'important_level_id']);
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
                    $new = new LetterFiles();
                    $new->letter_id = $model->id;
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

    public static function isOk($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['is_ok'])) {
            $model->is_ok = $post['is_ok'];
        }
        if (isset($post['message'])) {
            $model->message = $post['message'];
        }
        $model->save(false);

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
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

        // Asosiy fayl yuklash
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

        // Yuborilgandagi vaqt yoziladi
        if ($model->status == 1) {
            $model->sent_time = time();
        }

        if ($model->save(false)) {
            // Qo'shimcha fayllar yuklash
            $model->upload = UploadedFile::getInstancesByName('upload');
            if ($model->upload) {
                foreach ($model->upload as $item) {
                    $new = new LetterFiles();
                    $new->letter_id = $model->id;
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
