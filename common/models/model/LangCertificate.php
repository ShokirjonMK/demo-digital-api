<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "military ".
 *
 * @property int $id
 * @property string $ball
 * @property string $file
 * @property string $lang
 * @property int $certificate_type_id
 * @property int $student_id
 * @property int $user_type
 * @property int $user_id
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class LangCertificate extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public $uploadFile;
    const UPLOADS_FOLDER = 'lang_certificate/';
    public $imgMaxSize = 1024 * 1024 * 10; // 3 Mb

    const USER_TYPE_STUDENT = 1;
    const USER_TYPE_TEACHER = 2;
    const USER_TYPE_STAFF = 3;


    public static function tableName()
    {
        return 'lang_certificate';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['user_id', 'certificate_type_id'], 'required'],
            [['user_id', 'certificate_type_id', 'user_type'], 'integer'],
            [['lang'], 'string', 'max' => 255],
            [['file'], 'string', 'max' => 255],
            [['ball'], 'double'],
            [['uploadFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->imgMaxSize],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['certificate_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LangCertificateType::class, 'targetAttribute' => ['certificate_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'file' => _e('File'),
            'lang' => _e('Lang'),
            'user_type' => _e('user_type'),
            'certificate_type_id' => _e('Certificate Type Id'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'certificate_type' => function ($model) {
                return $model->certificateType->translate->name ?? '';
            },
            'ball',
            'certificate_type_id',
            'file',
            'user_id',
            'user_type',
            'lang',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'certificateType',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getCertificateType()
    {
        return $this->hasOne(LangCertificateType::className(), ['id' => 'certificate_type_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isRole('student', $model->user_id)) {
            $model->user_type = self::USER_TYPE_STUDENT;
        } elseif (isRole('teacher', $model->user_id)) {
            $model->user_type = self::USER_TYPE_TEACHER;
        } else {
            $model->user_type = self::USER_TYPE_STAFF;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $model->uploadFile = UploadedFile::getInstancesByName('uploadFile');
            if ($model->uploadFile) {
                $model->uploadFile = $model->uploadFile[0];
                $imgFile = $model->uploadFile();
                if ($imgFile) {
                    $model->file = $imgFile;
                } else {
                    $errors[] = $model->errors;
                }
            }

            $model->save();
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        $oldFile = $model->file;
        $model->uploadFile = UploadedFile::getInstancesByName('uploadFile');
        if ($model->uploadFile) {
            $model->uploadFile = $model->uploadFile[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->deleteFile($oldFile);
                $model->file = $questionFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->save()) {
            $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function uploadFile()
    {
        $fileUploadUrl = self::UPLOADS_FOLDER;
        if ($this->user_type == self::USER_TYPE_STUDENT) {
            $fileUploadUrl  = self::UPLOADS_FOLDER . "student/";
        }

        if ($this->user_type == self::USER_TYPE_TEACHER) {
            $fileUploadUrl  = self::UPLOADS_FOLDER . "teacher/";
        }
        if ($this->user_type == self::USER_TYPE_STAFF) {
            $fileUploadUrl  = self::UPLOADS_FOLDER . "staff/";
        }

        if ($this->validate()) {
            if (!file_exists(UPLOADS_PATH  . $fileUploadUrl)) {
                mkdir(UPLOADS_PATH  . $fileUploadUrl, 0777, true);
            }
            $fileName =  time() . $this->uploadFile->extension;
            $miniUrl = $fileUploadUrl . $fileName;
            $url = UPLOADS_PATH . $miniUrl;
            $this->uploadFile->saveAs($url, false);
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

    public function getUsers()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getType()
    {
        return $this->hasOne(LangCertificateType::class, ['id' => 'certificate_type_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
