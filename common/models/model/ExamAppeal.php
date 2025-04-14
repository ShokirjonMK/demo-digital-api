<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_student_id
 * @property sting $appeal_text
 * @property int $teacher_user_id
 * @property int $subject_id
 * @property int $edu_year_id
 * @property int $semestr_id
 *
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property ExamStudent $examStudent
 */
class ExamAppeal extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_IN_CHECKING = 2;


    const TYPE_NEW = 0;

    const TYPE_ASOSLI = 1;
    const TYPE_ASOSSIZ = 2;
    const TYPE_TEXNIK = 3;
    const TYPE_ASOSLI_TEXNIK = 4;

    const IS_CHANGED_TRUE = 1;
    const IS_CHANGED_FALSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_appeal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'exam_student_id',
                    'appeal_text',
                ],
                'required'
            ],
            [
                [
                    'is_changed',
                    'exam_student_id',
                    'student_id',
                    'teacher_user_id',
                    'subject_id',
                    'edu_year_id',
                    'semestr_id',
                    'faculty_id',
                    'exam_id',
                    'lang_id',
                    'type',
                    'teacher_access_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ],
                'integer'
            ],

            [['appeal_text', 'teacher_conclusion', 'conclusion'], 'string'],

            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['exam_student_id'], 'unique'],
            // [['exam_student_id'], 'unique', 'targetAttribute' => ['is_deleted']],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exam_id' => _e('Exam Id'),
            'faculty_id' => _e('Faculty Id'),
            'student_id' => _e('Student Id'),
            'exam_student_id' => _e('Exam Student Id'),
            'appeal_text' => _e('Appeal Text'),
            'teacher_user_id' => _e('Teacher_user Id'),
            'subject_id' => _e('Subject Id'),
            'edu_year_id' => _e('Edu_year Id'),
            'semestr_id' => _e('Semestr Id'),
            'is_changed' => _e('is_changed'),



            'teacher_access_id' => _e('teacher_access_id'),
            'lang_id' => _e('lang_id'),
            'type' => _e('type'),
            'teacher_conclusion' => _e('teacher_conclusion'),
            'conclusion' => _e('conclusion'),


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


        if (isRole('student')) {

            if ($this->exam->status_appeal == Exam::STATUS_APPEAL_ANNOUNCED) {
                $fields = [
                    'id',
                    'ball',
                    // 'old_ball',
                    'exam_id',
                    'student_id',
                    'exam_student_id',
                    'teacher_access_id',
                    'appeal_text',
                    // 'teacher_user_id',
                    'subject_id',
                    'edu_year_id',
                    'semestr_id',
                    'faculty_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'type',
                    'teacher_conclusion',
                    'conclusion',
                    'lang_id',
                    // 'is_changed',
                ];

                return $fields;
            }
            $fields = [
                'id',
                // 'ball',
                // 'old_ball',
                'exam_id',
                'student_id',
                'exam_student_id',
                'teacher_access_id',
                'appeal_text',
                // 'teacher_user_id',
                'subject_id',
                'edu_year_id',
                'semestr_id',
                'faculty_id',
                'order',
                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted',
                // 'type',
                // 'teacher_conclusion',
                // 'conclusion',
                'lang_id',
                // 'is_changed',
            ];

            return $fields;
        }

        $fields = [
            'id',
            'ball',
            'old_ball',
            'exam_id',
            'student_id',
            'exam_student_id',
            'teacher_access_id',
            'appeal_text',
            'teacher_user_id',
            'subject_id',
            'edu_year_id',
            'semestr_id',
            'faculty_id',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
            'type',
            'teacher_conclusion',
            'conclusion',
            'lang_id',
            'is_changed',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'examStudent',
            'student',
            'teacherUser',
            'subject',
            'exam',
            'eduYear',
            'semestr',

            'teacherAccess',
            'teacher',
            'statusName',

            'accessKey',
            // 'deKey',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    // exam_student_id
    // student_id
    // teacher_user_id
    // subject_id
    // edu_year_id
    // semestr_id

    public function getAccessKey()
    {
        return $this->encodemk5MK($this->student_id . '-' . $this->exam_student_id);

        return $this->encodeMK($this->student_id) . '-' . $this->encodeMK($this->exam_student_id);
    }

    // public function getDeKey()
    // {
    //     return $this->decodeMK($this->accessKey);
    // }

    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getTeacherUser()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_user_id']);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    public function getTeacher()
    {
        return $this->teacherAccess->teacher;
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // dd($model->errors);

        $model->student_id = self::student();

        if ($model->student_id != $model->examStudent->student_id) {
            $errors[] = _e('This is other student\'s exam, you can not appeal');
            return simplify_errors($errors);
        }

        // if (is_null($model->examStudent->teacher_access_id) || !is_null($model->type)) {

        if (is_null($model->examStudent->teacher_access_id)) {

            $errors[] = _e('This exam is not checked!');
            return simplify_errors($errors);
        }

        $currentTimestamp = time();



        /***** */
        // if ($model->examStudent->student->edu_type_id == 2 &&  $model->examStudent->exam->faculty_id == 5 && !in_array(
        // if (!in_array(
        //     $model->student->course_id,
        //     [
        //         4
        //     ]
        // )) {
        //     $errors[] = _e('Ruxsat berilmagan!!!');
        //     return simplify_errors($errors);
        // }

        // exam appeal appelatsiya berish vaqti
        if ($model->examStudent->exam->appeal_start > $currentTimestamp && $model->examStudent->exam->appeal_finish < $currentTimestamp) {
            $errors[] = _e('Appeal time is already finished!');
            return simplify_errors($errors);
        }


        $model->teacher_user_id = self::teacher_access_user_id($model->examStudent->teacher_access_id);

        // dd($model->examStudent->exam->eduSemestrSubject->subject->id);
        $model->subject_id = $model->examStudent->exam->eduSemestrSubject->subject->id;
        $model->exam_id = $model->examStudent->exam->id;
        $model->edu_year_id = $model->examStudent->exam->eduSemestrSubject->eduSemestr->eduYear->id;
        $model->semestr_id =  $model->examStudent->exam->eduSemestrSubject->eduSemestr->semestr->id;
        $model->lang_id =  $model->examStudent->lang_id;
        $model->faculty_id = $model->examStudent->exam->faculty_id;

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

        if (isset($post['appeal_text'])) {
            $model->appeal_text = $post['appeal_text'];
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;

            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function teacherUpdateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if (isset($model->type) && $model->type !== null) {
        //     $errors[] = _e('Appeal is already checked!');
        //     return simplify_errors($errors);
        // }


        $model->teacher_conclusion = $post['teacher_conclusion'] ?? null;

        // if ($model->type == self::TYPE_ASOSLI && $post['type'] == self::TYPE_ASOSSIZ) {
        //     if (ExamStudentAnswerSubQuestion::find()->where([
        //         'exam_student_id' => $model->exam_student_id
        //     ])->andWhere([
        //         'not' => ['old_ball' => null]
        //     ])->exists()) {
        //         $errors[] = _e('Appeal is already checked and you changed ball!');
        //         return simplify_errors($errors);
        //     }
        // } elseif (isset($model->type) && $model->type !== null) {
        //     $errors[] = _e('Appeal is already checked!');
        //     return simplify_errors($errors);
        // }


        if (($model->type == self::TYPE_ASOSLI && $post['type'] == self::TYPE_ASOSSIZ)) {
            $hasCheckedAppeal = ExamStudentAnswerSubQuestion::find()
                ->where(['exam_student_id' => $model->exam_student_id])
                ->andWhere(['not', ['old_ball' => null]])
                ->exists();

            if ($hasCheckedAppeal) {
                $errors[] = _e('Appeal is already checked and you changed ball!');
                return simplify_errors($errors);
            }
        } elseif (isset($model->type) && $model->type !== null) {
            $errors[] = _e('Appeal is already checked!');
            return simplify_errors($errors);
        }


        $model->type = $post['type'] ?? null;

        if (!($model->validate())) {
            $errors[] = $model->errors;

            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
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

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),

        ];
    }
}
