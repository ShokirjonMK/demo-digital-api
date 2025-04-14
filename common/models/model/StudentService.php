<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%student_service}}".
 *
 * @property int $id
 * @property int|null $student_service_type ID of the student service type
 * @property int|null $student_id ID of the student
 * @property string|null $name
 * @property string|null $text
 * @property string|null $file
 * @property string|null $answer_text
 * @property string|null $answer_file
 * @property string|null $term
 * @property string|null $date
 * @property int|null $para_id
 * @property string|null $description
 * @property int|null $user_id javob berishi kerak bo'lgan user
 * @property int|null $order Order of the item
 * @property int|null $status Status of the item (1 = active, 0 = inactive)
 * @property int|null $is_deleted Is the item deleted (0 = no, 1 = yes)
 * @property int $created_at Creation timestamp
 * @property int $updated_at Update timestamp
 * @property int $created_by ID of the user who created the record
 * @property int $updated_by ID of the user who last updated the record
 * @property int $archived Is the item archived (0 = no, 1 = yes)
 */
class StudentService extends ActiveRecord
{
    public static $selected_language = 'uz';

    const TYPE_AUTO = 1;
    const TYPE_MANUAL = 2;

    use ResourceTrait;

    const STATUS_ANSWERED = 10;
    const STATUS_DISTRIBUTED = 2;

    const UPLOADS_FOLDER = 'uploads/student_service/';
    public $upload_file;
    public $upload_answer_file;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb


    public static function tableName()
    {
        return '{{%student_service}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['student_service_type', 'student_id', 'para_id', 'user_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['name', 'text', 'answer_text', 'description'], 'string'],
            [['term', 'date'], 'safe'],
            [['file', 'answer_file'], 'string', 'max' => 255],


            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->fileMaxSize],
            [['upload_answer_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->fileMaxSize],

        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'student_service_type',
            'student_id',
            'name',
            'text',
            'file',
            'answer_text',
            'answer_file',
            'term',
            'date',
            'para_id',
            'description',
            'user_id',
            'order',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'archived',
        ];

        return $fields;
    }
    public function extraFields()
    {
        $extraFields =  [
            'description',
            'categoryName',
            'student',
            'user',
            'studentServiceType',
            'studentServiceTypeName',
            'para',
            'paraName',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getCategoryName()
    {
        return $this->studentServiceType->categoryName ?? '';
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getStudentServiceType()
    {
        return $this->hasOne(StudentServiceType::class, ['id' => 'student_service_type']);
    }

    public function getStudentServiceTypeName()
    {
        return $this->studentServiceType->translate->name ?? '';
    }

    public function getPara()
    {
        return $this->hasOne(Para::class, ['id' => 'para_id']);
    }

    public function getParaName()
    {
        return $this->para->translate->name ?? '';
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_id = self::student_now();

        // upload file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $uploadFileUrl = $model->uploadFile();
            if ($uploadFileUrl) {
                $model->file = $uploadFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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

        // question file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $uploadFileUrl = $model->uploadFile();
            if ($uploadFileUrl) {
                $model->file = $uploadFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // answer file saqlaymiz
        $model->upload_answer_file = UploadedFile::getInstancesByName('upload_answer_file');
        if ($model->upload_answer_file) {
            $model->upload_answer_file = $model->upload_answer_file[0];
            $uploadFileUrl = $model->uploadAnswerFile();
            if ($uploadFileUrl) {
                $model->answer_file = $uploadFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function respondItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        // answer file saqlaymiz
        $model->upload_answer_file = UploadedFile::getInstancesByName('upload_answer_file');
        if ($model->upload_answer_file) {
            $model->upload_answer_file = $model->upload_answer_file[0];
            $uploadFileUrl = $model->uploadAnswerFile();
            if ($uploadFileUrl) {
                $model->answer_file = $uploadFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->answer_text = $post['answer_text'] ?? $model->answer_text;
        $model->user_id = current_user_id();

        $model->status = self::STATUS_ANSWERED;

        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->upload_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->upload_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function uploadAnswerFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName =  \Yii::$app->security->generateRandomString(10) . '.' . $this->upload_answer_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->upload_answer_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
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
