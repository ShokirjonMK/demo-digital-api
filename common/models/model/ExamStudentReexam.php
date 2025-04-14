<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_student".
 *
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
 *
 * @property Exam $exam
 * @property Student $student
 * @property TeacherAccess $teacherAccess
 * @property Student $exam0
 * @property Student $student0
 * @property TeacherAccess $teacherAccess0
 */
class ExamStudentReexam extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const UPLOADS_FOLDER = 'uploads/exam_student/re_exam/';
    public $uploadFile;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_student_reaxam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            // [['student_id', 'exam_student_id', 'subject_id', 'exam_id'], 'required'],
            [['description', 'uploadFile'], 'required'],
            [['student_id', 'exam_student_id', 'subject_id', 'exam_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['file'], 'string', 'max' => 255],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
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
            'description' => _e('Description'),
            'student_id' => _e('Student ID'),
            'exam_student_id' => _e('Exam Student ID'),
            'subject_id' => _e('Subject ID'),
            'exam_id' => _e('Exam ID'),
            'status' => _e('Status'),
            'order' => _e('Order'),
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
            'file',
            'description',
            'student_id',
            'exam_student_id',
            'subject_id',
            'exam_id',
            'status',
            'order',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'exam',
            'examStudent',
            'student',
            'subject',

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
     * @return \yii\db\ActiveQuery|ExamQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    /**
     * Gets query for [[ExamStudent]].
     *
     * @return \yii\db\ActiveQuery|ExamStudentQuery
     */
    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public static function createItem($post, $exam_student_id)
    {
        $newModel = new self();
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // dd($newModel->attributeLabels());

        $newModel->exam_student_id = $exam_student_id;

        $newModel->exam_id = $newModel->examStudent->exam_id;
        $newModel->student_id = $newModel->examStudent->student_id;
        $newModel->subject_id = $newModel->examStudent->exam->eduSemestrSubject->subject_id;
        $newModel->description = $post['description'] ?? null;

        $newModel->uploadFile = UploadedFile::getInstancesByName('uploadFile');
        if ($newModel->uploadFile) {
            $newModel->uploadFile = $newModel->uploadFile[0];
            $uploadFileUrl = $newModel->uploadFile();
            if ($uploadFileUrl) {
                $newModel->file = $uploadFileUrl;
            } else {
                $errors[] = $newModel->errors;
            }
        }

        if (!($newModel->validate())) {
            $errors[] = $newModel->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($newModel->save()) {
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
            $filePath = self::UPLOADS_FOLDER;
            if (!file_exists(STORAGE_PATH . $filePath)) {
                mkdir(STORAGE_PATH . $filePath, 0777, true);
            }

            $fileName = $this->exam_student_id . "_" . time() . '.' . $this->uploadFile->extension;

            $miniUrl = $filePath . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->uploadFile->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }
}
