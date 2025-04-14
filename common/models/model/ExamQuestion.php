<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_question".
 *
 * @property int $id
 * @property int $exam_id
 * @property string $file
 * @property float|null $ball
 * @property string $question
 * @property int $lang_id
 * @property int $level Qiyinlilik darajasi 1-oson, 2-o\'rta, 3-murakkab
 * @property int $type 1-savol, 2-test, 3-another
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Exam $exam
 * @property Languages $lang
 * @property ExamQuestionOption[] $examQuestionOptions
 * @property ExamStudentAnswer[] $examStudentAnswers
 */
class ExamQuestion extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }




    const UPLOADS_FOLDER = 'uploads/question_files/';
    // const UPLOADS_FOLDER_PASSPORT = 'uploads/user-passport/';
    public $question_file;
    public $questionFileMaxSize = 1024 * 1024 * 3; // 3 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_id', 'question', 'lang_id', 'level', 'exam_question_type_id'], 'required'],
            [['exam_id', 'lang_id', 'level', 'exam_question_type_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['ball'], 'number'],
            [['question'], 'string'],
            [['file'], 'string', 'max' => 255],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['exam_question_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamQuestionType::className(), 'targetAttribute' => ['exam_question_type_id' => 'id']],

            [['question_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->questionFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name translatiion
            'exam_id' => 'Exam ID',
            'file' => 'File',
            'ball' => 'Ball',
            'question' => 'Question',
            'lang_id' => 'Lang ID',
            'level' => 'Level',
            'exam_question_type_id' => 'Type',
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

            'exam_id',
            'question_file' => function ($model) {
                return $model->file ?? '';
            },
            'ball',
            'question',
            'lang_id',
            'level',
            'exam_question_type_id',
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
            'exam',
            'lang',
            'examQuestionOptions',
            'examStudentAnswers',
            'type',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Exam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    /**
     * Gets query for [[Lang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id']);
    }

    /**
     * Gets query for [[exam_question_type_id]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ExamQuestionType::className(), ['id' => 'exam_question_type_id']);
    }

    /**
     * Gets query for [[ExamQuestionOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamQuestionOptions()
    {
        return $this->hasMany(ExamQuestionOption::className(), ['exam_question_id' => 'id']);
    }

    /**
     * Gets query for [[ExamStudentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamStudentAnswers()
    {
        return $this->hasMany(ExamStudentAnswer::className(), ['exam_question_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }



        if ($model->save()) {
            // question file saqlaymiz
            $model->question_file = UploadedFile::getInstancesByName('question_file');
            if ($model->question_file) {
                $model->question_file = $model->question_file[0];
                $questionFileUrl = $model->uploadFile();
                if ($questionFileUrl) {
                    $model->file = $questionFileUrl;
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
        // question file saqlaymiz
        $model->question_file = UploadedFile::getInstancesByName('question_file');
        if ($model->question_file) {
            $model->question_file = $model->question_file[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->file = $questionFileUrl;
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

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->question_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->question_file->saveAs($url, false);
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
