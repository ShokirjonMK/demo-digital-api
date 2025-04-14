<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_student_answer_sub_question".
 *
 * @property int $id
 * @property string|null $file
 * @property int $exam_student_answer_id
 * @property int $sub_question_id
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $parent_id
 *
 * @property ExamStudentAnswer $examStudentAnswer
 * @property SubQuestion $subQuestion
 */
class ExamStudentAnswerSubQuestionDeleted extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_NEW = 2;
    const STATUS_COMPLETE = 1;
    const STATUS_IN_CHECKING = 3;
    const STATUS_IN_CHECKED = 4;

    const UPLOADS_FOLDER = 'uploads/answer_files/sub_question/';
    public $answer_file;
    public $answerFileMaxSize = 1024 * 1024 * 5; // 3 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_student_answer_sub_question_deleted';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['exam_student_answer_id', 'sub_question_id'], 'required'],
            [
                [
                    'exam_student_answer_id',
                    'exam_student_answer_sub_question_id',
                    'sub_question_id',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',

                    'exam_student_id',
                    'archived',
                    'student_id',
                    'old_ball',
                    'old_ball_calculate',
                    'is_cheked',
                    'student_created_at',
                    'student_updated_at',

                    'created_at_o',
                    'updated_at_o',
                    'created_by_o',
                    'updated_by_o',
                ],
                'integer'
            ],
            [['answer'], 'string'],
            [['teacher_conclusion'], 'string'],
            [['max_ball'], 'double'],
            [['ball'], 'double'],
            [['file'], 'string', 'max' => 255],

            [['exam_student_answer_sub_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudentAnswerSubQuestion::className(), 'targetAttribute' => ['exam_student_answer_sub_question_id' => 'id']],
            [['exam_student_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudentAnswer::className(), 'targetAttribute' => ['exam_student_answer_id' => 'id']],
            [['sub_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubQuestion::className(), 'targetAttribute' => ['sub_question_id' => 'id']],

            [['answer_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->answerFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'File',
            'exam_student_answer_sub_question_id' => 'exam_student_answer_sub_question_id',
            'exam_student_answer_id' => 'Exam Student Answer ID',
            'sub_question_id' => 'Sub Question ID',
            'answer' => 'Answer',
            'ball' => 'Ball',
            'max_ball' => 'Max Ball',
            'teacher_conclusion' => 'Еeacher Сonclusion',
            'created_at_o',
            'updated_at_o',
            'created_by_o',
            'updated_by_o',
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
        $fields = [
            'id',
            // 'file',

            'exam_student_answer_id',
            'sub_question_id',
            'exam_student_answer_sub_question_id',

            /* 'question' => function ($model) {
                return $model->questionForExamStudentAnswer ?? [];
            }, */
            'teacher_conclusion',

            'answer',
            'ball',
            'max_ball',


            'created_at_o',
            'updated_at_o',
            'created_by_o',
            'updated_by_o',
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
        $extraFields = [

            'examStudentAnswer',
            'examStudentAnswerSubQuestion',

            'subQuestion',

            'exam',
            'question',
            'student',
            'teacherAccess',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getExamStudentAnswer()
    {
        return $this->hasOne(ExamStudentAnswer::className(), ['id' => 'exam_student_answer_id']);
    }

    public function getExamStudentAnswerSubQuestion()
    {
        return $this->hasOne(ExamStudentAnswerSubQuestion::className(), ['id' => 'exam_student_answer_sub_question_id']);
    }

    public function getExam()
    {
        return $this->examStudentAnswer->exam ?? [];
    }

    public function getQuestion()
    {
        return $this->examStudentAnswer->question ?? [];
    }

    public function getStudent()
    {
        return $this->examStudentAnswer->student ?? [];
    }

    public function getTeacherAccess()
    {
        return $this->examStudentAnswer->teacherAccess ?? [];
    }


    public function getSubQuestion()
    {
        return $this->hasOne(SubQuestion::className(), ['id' => 'sub_question_id']);
    }


    public static function createItem($model, $post)
    {
        return true;

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->start = time();
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
        // attemp esdan chiqmasin
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        // studentni answer file ni saqlaymiz

        $student = Student::findOne(['user_id' => current_user_id()]);
        if (!$student) {
            $errors[] = _e("Student not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $student_id = $student->id;
        $exam_id = $model->exam_id;
        $old_file = $model->file;

        if (isset($exam_id)) {
            $exam = Exam::findOne($exam_id);
            if ($exam) {
                $examStudent = ExamStudent::find()->where([
                    'exam_id' => $exam_id,
                    'student_id' => $student_id,
                ])
                    ->orderBy('id desc')
                    ->one();
                $now_second = time();
                if ($examStudent) {
                    $finishExamStudent = strtotime($examStudent->start) + $exam->duration + $examStudent->duration;

                    if (
                        (strtotime($exam->start) <= $now_second)
                        && (strtotime($exam->finish) >= $now_second)
                        && ($now_second <= $finishExamStudent)
                    ) {

                        /** SubQuestion */
                        //code here
                        /** SubQuestion */
                        $model->attempt = $examStudent->attempt;
                        $model->answer_file = UploadedFile::getInstancesByName('answer_file');
                        if ($model->answer_file) {
                            $model->answer_file = $model->answer_file[0];
                            $answer_fileFileUrl = $model->uploadFile($exam->id, $student_id);
                            if ($answer_fileFileUrl) {
                                $model->file = $answer_fileFileUrl;
                            } else {
                                $errors[] = $model->errors;
                            }
                        }
                        if (!($model->validate())) {
                            $errors[] = $model->errors;
                        }
                    } else {
                        $errors[] = _e("This exam`s time expired or ");
                    }
                } else {
                    $errors[] = _e("Student not found for this exam");
                }
            } else {
                $errors[] = _e("This exam not found");
            }
        } else {
            $errors[] = _e("This exam not found");
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
            $errors[] = count($errors);
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function uploadFile($exam_id, $student_id)
    {
        $folder = self::UPLOADS_FOLDER . "/exam_" . $exam_id . "/";

        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . $folder)) {
                mkdir(STORAGE_PATH . $folder, 0777, true);
            }

            $fileName = $this->id . "_" . $student_id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->answer_file->extension;

            $miniUrl = $folder . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->answer_file->saveAs($url, false);
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
