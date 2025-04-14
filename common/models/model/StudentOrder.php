<?php

namespace common\models\model;

use common\models\User;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use Yii;

use api\resources\ResourceTrait;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%student_order}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int $order_type_id
 * @property int|null $user_id
 * @property string|null $date
 * @property string|null $file
 * @property string|null $description
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class StudentOrder extends \yii\db\ActiveRecord
{
    public $uploadFile;
    const UPLOADS_FOLDER = 'student_order/';

    public $imgMaxSize = 1024 * 1024 * 10; // 3 Mb

    public static $selected_language = 'uz';

    use ResourceTrait;

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
        return 'student_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'order_type_id'], 'required'],
            [['student_id', 'order_type_id', 'user_id', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['date'], 'safe'],
            [['description'], 'string'],
            [['file'], 'string', 'max' => 255],
            [['uploadFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->imgMaxSize],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['order_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\OrderType::class, 'targetAttribute' => ['order_type_id' => 'id']],

        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'order_type_id' => _e('Order Type ID'),
            'date' => _e('Date'),
            'file' => _e('File'),
            'student_id' => _e('Student ID'),
            'user_id' => _e('User ID'),
            'description' => _e('Description'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'order_type_id',
            'date',
            'file',
            'student_id',
            'user_id',
            'description',
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

            'users',
            'student',
            'orderType',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }



    #region rel
    public function getUsers()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public function getOrderType()
    {
        return $this->hasOne(OrderType::class, ['id' => 'order_type_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->user_id = self::findByStudentId($model->student_id);

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

        if ($this->validate()) {
            if (!file_exists(UPLOADS_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(UPLOADS_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . '_' . time() . '.' . $this->uploadFile->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
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



    #endregion
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
