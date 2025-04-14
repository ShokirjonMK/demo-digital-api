<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_question_option".
 *
 * @property int $id
 * @property int $exam_question_id
 * @property string|null $file
 * @property int|null $is_correct
 * @property string $option
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property ExamQuestion $examQuestion
 * @property ExamStudentAnswer[] $examStudentAnswers
 */
class ExamQuestionOption extends \yii\db\ActiveRecord
{

    use ResourceTrait;


    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const UPLOADS_FOLDER = 'uploads/option_files/';
    // const UPLOADS_FOLDER_PASSPORT = 'uploads/user-passport/';
    public $option_file;
    public $optionFileMaxSize = 1024 * 1024 * 3; // 3 Mb
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_question_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_question_id', 'option'], 'required'],
            [['exam_question_id', 'is_correct', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['option'], 'string'],
            [['file'], 'string', 'max' => 255],
            [['exam_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamQuestion::className(), 'targetAttribute' => ['exam_question_id' => 'id']],
            [['option_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->optionFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exam_question_id' => 'Exam Question ID',
            'file' => 'File',
            'is_correct' => 'Is Correct',
            'option' => 'Option',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'exam_question_id',
            'option_file' => function ($model) {
                return $model->file ?? '';
            },
            'is_correct',
            'option',

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
            'examQuestion',
            'examStudentAnswers',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[ExamQuestion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamQuestion()
    {
        return $this->hasOne(ExamQuestion::className(), ['id' => 'exam_question_id']);
    }

    /**
     * Gets query for [[ExamStudentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamStudentAnswers()
    {
        return $this->hasMany(ExamStudentAnswer::className(), ['option_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }



        if ($model->save()) {
            // option file saqlaymiz
            $model->option_file = UploadedFile::getInstancesByName('option_file');
            if ($model->option_file) {
                $model->option_file = $model->option_file[0];
                $optionFileUrl = $model->uploadFile();
                if ($optionFileUrl) {
                    $model->file = $optionFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

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
        $oldFile = $model->file;
        // option file saqlaymiz
        $model->option_file = UploadedFile::getInstancesByName('option_file');
        if ($model->option_file) {
            $model->option_file = $model->option_file[0];
            $optionFileUrl = $model->uploadFile();
            if ($optionFileUrl) {
                $model->file = $optionFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        if ($model->save()) {
            $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
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

    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->option_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->option_file->saveAs($url, false);
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
        return true;
    }
}
