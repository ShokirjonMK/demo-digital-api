<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_student".
 * @property int $id
 * @property int $student_id
 * @property int $exam_id
 * @property int|null $teacher_access_id
 * @property float|null $ball
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property Student $exam
 * @property Student $student
 * @property TeacherAccess $teacherAccess
 * @property Student $exam0
 * @property Student $student0
 * @property TeacherAccess $teacherAccess0
 */
class ExamStudentDeleted extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_TAKED = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_IN_CHECKING = 3;
    const STATUS_CHECKED = 4;
    const STATUS_SHARED = 5;

    const IS_PLAGIAT_TRUE = 1;
    const IS_PLAGIAT_FALSE = 0;

    const UPLOADS_FOLDER = 'uploads/plagiat_files/';
    public $plagiatFile;
    public $plagiatFileMaxSize = 1024 * 1024 * 5; // 3 Mb

    // conclusion
    // plagiat_file
    // plagiat_percent


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_student_deleted';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['student_id', 'exam_id'], 'required'],
            [
                [
                    'student_id',
                    'start',
                    'finish',
                    'exam_student_id',
                    'exam_id',
                    'teacher_access_id',
                    'attempt',
                    'lang_id',
                    'exam_semeta_id',
                    'is_plagiat',
                    'duration',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',

                    'created_at_o',
                    'updated_at_o',
                    'created_by_o',
                    'updated_by_o',
                ], 'integer'
            ],
            [['ball'], 'double'],
            [['plagiat_file'], 'string', 'max' => 255],
            [['password'], 'safe'],
            [['plagiat_percent'], 'double'],
            [['conclusion'], 'string'],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['plagiatFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->plagiatFileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'exam_student_id' => 'exam_student',
            'lang_id' => 'Lang ID',
            'exam_id' => 'Exam ID',
            'teacher_access_id' => 'Teacher Access ID',
            'password' => 'Password',
            'exam_semeta_id' => 'Exam Semeta Id',
            'ball' => 'Ball',
            'duration' => 'Duration',
            'start' => 'Start',
            'finish' => 'Finish',
            'is_plagiat' => 'Is Plagiat',
            'attempt' => 'Attempt',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
            'created_at_o',
            'updated_at_o',
            'created_by_o',
            'updated_by_o',

        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'student_id',
            'exam_student_id',
            'exam_id',
            'lang_id',
            'teacher_access_id',
            'ball',
            'start',
            'attempt',
            'password',
            'is_plagiat',
            'duration',
            'finish',

            'conclusion',
            'plagiat_file',
            'plagiat_percent',

            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',

            'created_at_o',
            'updated_at_o',
            'created_by_o',
            'updated_by_o',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'eduSemestrSubject',
            'examType',
            'exam',
            'student',
            'examStudent',
            'examQuestions',
            'examStudentAnswers',

            'answers',
            'statusName',
            'teacherAccess',
            'examSemeta',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    public function getExamStudentAnswers()
    {
        return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
    }

    public function getAnswers()
    {
        return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
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
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[TeacherAccess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    /**
     * Gets query for [[ExamSemeta]].
     *exam_semeta
     * @return \yii\db\ActiveQuery
     */
    public function getExamSemeta()
    {
        return $this->hasOne(ExamSemeta::className(), ['id' => 'exam_semeta_id']);
    }

    public function getStatusName()
    {
        return   $this->statusList()[$this->status];
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
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

        $oldFile = $model->plagiat_file;
        // plagiat file saqlaymiz

        $model->plagiatFile = UploadedFile::getInstancesByName('plagiatFile');
        if ($model->plagiatFile) {
            $model->plagiatFile = $model->plagiatFile[0];
            $plagiatFileUrl = $model->uploadFile();

            if ($plagiatFileUrl) {
                $model->plagiat_file = $plagiatFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***
        // $errors[] = $post['old_file'];

        $model->status = self::STATUS_CHECKED;
        if ($model->plagiat_percent >= Yii::$app->params['plagiat_percent_max']) {
            $model->is_plagiat = self::IS_PLAGIAT_TRUE;
        }
        if ($model->save() && count($errors) == 0) {
            // $model->deleteFile($oldFile);
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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public function uploadFile()
    {
        if ($this->validate()) {
            $filePath = self::UPLOADS_FOLDER . $this->exam_id . '/';
            if (!file_exists(STORAGE_PATH . $filePath)) {
                mkdir(STORAGE_PATH . $filePath, 0777, true);
            }

            $fileName = $this->id . "_" . $this->lang_id . "_" . $this->teacher_access_id . "_" . time() . '.' . $this->plagiatFile->extension;

            $miniUrl = $filePath . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->plagiatFile->saveAs($url, false);
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

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_TAKED => _e('STATUS_TAKED'),
            self::STATUS_COMPLETE => _e('STATUS_COMPLETE'),
            self::STATUS_IN_CHECKING => _e('STATUS_IN_CHECKING'),
            self::STATUS_CHECKED => _e('STATUS_CHECKED'),
            self::STATUS_SHARED => _e('STATUS_SHARED'),
        ];
    }
}
