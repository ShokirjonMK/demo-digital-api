<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%exam_student}}".
 *
 * @property int $id
 * @property int|null $archived
 * @property int|null $student_mark_id
 * @property string|null $is_checked_full_c is_checked_full_c
 * @property string|null $has_answer_c
 * @property int $student_id
 * @property int $exam_id
 * @property int|null $teacher_access_id
 * @property float|null $on1 oraliq 1
 * @property float|null $on2 oraliq 2
 * @property float|null $in_ball oraliq bal
 * @property float|null $ball
 * @property int|null $type 1 ielts 2 nogiron masalan
 * @property int|null $main_ball
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int|null $lang_id
 * @property int|null $start
 * @property int|null $duration
 * @property int|null $finish
 * @property string|null $password
 * @property int|null $exam_semeta_id exam_semeta id
 * @property string|null $conclusion umumiy xulosa
 * @property string|null $plagiat_file fayl
 * @property float|null $plagiat_percent foyizi
 * @property int|null $is_plagiat 0-plagiat emas, 1-plagiat
 * @property int|null $act 1 act tuzilgan imtihon qodalarini bizgan
 * @property string|null $act_reason
 * @property string|null $act_file
 * @property int|null $is_checked tekshirilganligi
 * @property int|null $is_checked_full toliq tekshirilhanligi
 * @property int|null $has_answer javob yozilganligi
 * @property int|null $edu_year_id talim yili
 * @property int|null $subject_id
 * @property int|null $edu_semestr_subject_id
 *
 * @property Exam $exam
 * @property ExamAppeal[] $examAppeals
 * @property ExamSemetum $examSemeta
 * @property ExamStudentReaxam[] $examStudentReaxams
 * @property ExamStudentReexam[] $examStudentReexams
 * @property Student $student
 * @property TeacherAccess $teacherAccess
 */
class ExamStudent extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const ACT_TYPE_1 = 1;
    const ACT_TYPE_2 = 2;


    const STATUS_INACTIVE = 0;
    const STATUS_TAKED = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_IN_CHECKING = 3;
    const STATUS_CHECKED = 4;
    const STATUS_SHARED = 5;

    const IS_PLAGIAT_TRUE = 1;
    const IS_PLAGIAT_FALSE = 0;

    const TYPE_IELTS = 1;
    const TYPE_NOGIRON = 2;
    const TYPE_JAPAN = 3;

    const UPLOADS_FOLDER = 'uploads/exam_student/plagiat_files/';
    const UPLOADS_FOLDER_ACT = 'uploads/exam_student/act_files/';
    public $actFile;
    public $plagiatFile;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb

    // conclusion
    // plagiat_file
    // plagiat_percent
    // act_file

    const ACT_FALSE = 0;
    const ACT_TRUE = 1;
    const ACT_NOT_COMFORMED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {

        // Check if user wants archived data (e.g., via a parameter or a flag)
        if (Yii::$app->request->get('archived')) {
            return 'exam_student_23_24';
        }

        return 'exam_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'exam_id'], 'required'],
            [
                [
                    'student_mark_id',
                    'is_checked',
                    'is_checked_full',
                    'has_answer',
                    'student_id',
                    'start',
                    'finish',
                    'exam_id',
                    'subject_id',
                    'edu_semestr_subject_id',
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
                    'act',
                    'act_type',
                    'type',
                    'checking_time',
                    'archived'
                ],
                'integer'
            ],
            [['ball', 'in_ball', 'on1', 'on2'], 'double'],

            [['plagiat_file', 'act_file'], 'string', 'max' => 255],
            [['act_reason'], 'string'],
            [['password'], 'safe'],
            [['plagiat_percent'], 'double'],
            [['conclusion'], 'string'],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['plagiatFile', 'actFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg,jepg,zip,mp4,avi', 'maxSize' => $this->fileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_mark_id' => 'student_mark ID',
            'student_id' => 'Student ID',
            'lang_id' => 'Lang ID',
            'exam_id' => 'Exam ID',
            'teacher_access_id' => 'Teacher Access ID',
            'password' => 'Password',
            'exam_semeta_id' => 'Exam Semeta Id',
            'ball' => 'Ball',
            'duration' => 'Duration',
            'start' => 'Start',
            'type' => 'type',
            'finish' => 'Finish',
            'is_plagiat' => 'Is Plagiat',
            'attempt' => 'Attempt',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'act' => _e('act'),
            'on1' => _e('on1'),
            'on2' => _e('on2'),
            'act_file' => _e('act_file'),
            'act_reason' => _e('act_reason'),


            'in_ball' => _e('in_ball'),
            'is_checked' => _e('is_checked'),
            'is_checked_full' => _e('is_checked_full'),
            'has_answer' => _e('has_answer'),


            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        if (isRole('teacher')) {

            $fields = [
                'id',
                // 'student_id',
                'exam_id',
                'lang_id',
                // 'teacher_access_id',
                'teacher_access_id' => function ($model) {
                    return (isRole('admin')) ? $model->teacher_access_id : null;
                },
                'ball',
                'attempt',
                'password',
                'is_plagiat',
                'duration',
                // 'finish',
                'finish' => function ($model) {
                    return $model->finishedAt;
                },
                // 'start',
                'start' => function ($model) {
                    return $model->startedAt;
                },

                'type',
                'on1',
                'act_type',
                //  => function ($model) {
                //     return $model->oraliq1;
                // },
                'on2',
                //  => function ($model) {
                //     return $model->oraliq2;
                // },
                'correct',
                'checking_time',
                'archived',
                'act_file',
                'act_reason',
                'conclusion',
                'plagiat_file',
                'plagiat_percent',
                'reExam',
                // 'examStudentReexam',

                'in_ball',
                'is_checked',
                'is_checked_full',
                'has_answer',

                'act',
                'order',
                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',

            ];
        } else {
            $fields = [
                'id',
                'student_id',
                'exam_id',
                'lang_id',
                // 'teacher_access_id',
                'teacher_access_id' => function ($model) {
                    return (isRole('admin')) ? $model->teacher_access_id : null;
                },
                'ball',
                // 'ball' => function ($model) {
                //     return $model->allBall;
                // },
                'attempt',
                'password',
                'is_plagiat',
                'duration',
                // 'finish',
                'finish' => function ($model) {
                    return $model->finishedAt;
                },
                // 'start',
                'start' => function ($model) {
                    return $model->startedAt;
                },

                'type',
                'on1',
                //  => function ($model) {
                //     return $model->oraliq1;
                // },
                'on2',
                //  => function ($model) {
                //     return $model->oraliq2;
                // },
                'correct',
                'checking_time',
                'archived',

                // act
                'act',
                'act_type',
                'act_reason',
                'act_reason_created_by',
                'act_reason_created_at',
                'act_confirmed_created_at',
                'act_confirmed_created_by',
                'act_file',

                'conclusion',
                'plagiat_file',
                'plagiat_percent',
                'reExam',
                // 'examStudentReexam',

                'in_ball',
                'is_checked',
                'is_checked_full',
                'has_answer',

                'order',
                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',

            ];
        }
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'exam',
            'student',

            'examStudentAnswers',

            'answers',
            'hasAnswer',
            'isChecked',
            'isCheckedFull',
            'allBall',
            'oldAllBall',


            'controlBallCorrect',

            'statusName',
            // 'teacherAccess',
            'examSemeta',

            'accessKey',
            'decodedKey',

            'examControlStudent',
            'reExam',
            'examStudentReexam',

            'actReasonCreatedBy',
            'actConfirmedCreatedBy',

            'appeal',
            'examAppeal',
            'teacher',

            'finishedAt',
            'startedAt',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getStartedAt()
    {
        return $this->start ? date('Y-m-d H:i:s', $this->start) : '';
    }

    public function getActReasonCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'act_reason_created_by']);
    }

    public function getActConfirmedCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'act_confirmed_created_by']);
    }

    //     public function getStartedAt()
    // act_confirmed_created_by


    public function getConclution12()
    {
        if (!isRole('admin')) {
            if (Yii::$app->request->get('subject_id') != null) {
                return ExamConclution::find()
                    ->where(['subject_id' => Yii::$app->request->get('subject_id')])
                    ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
                    ->andWhere(['created_by' => current_user_id()])
                    ->all();
            }
            return ExamConclution::find()
                ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
                ->andWhere(['created_by' => current_user_id()])
                ->all();
        }
        if (Yii::$app->request->get('subject_id') != null) {
            return ExamConclution::find()
                ->where(['subject_id' => Yii::$app->request->get('subject_id')])
                ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
                ->all();
        }
        return ExamConclution::find()
            ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
            ->all();

        return ExamConclution::find()->all();
    }

    public function getConclution()
    {
        $query = ExamConclution::find()
            ->andWhere(['lang_code' => Yii::$app->request->get('lang')]);

        if (!isRole('admin')) {
            $query->andWhere(['created_by' => current_user_id()]);
        }

        if (Yii::$app->request->get('subject_id') != null) {
            $query->andWhere(['subject_id' => Yii::$app->request->get('subject_id')]);
        }

        return $query->all();
    }


    public function getControlBallCorrect()
    {
        $on1 = ExamControlStudent::find()
            ->where([
                'student_id' => $this->student_id,
                'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
                'subject_id' => $this->exam->eduSemestrSubject->subject_id,
                'category' => $this->exam->category
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $on1 = $on1->ball ?? null;
        if (is_null($this->on1)) {
            $this->on1 = $on1;
            $this->save();
        }
        $on2 = $on1->ball2 ?? null;
        if (is_null($this->on2)) {
            $this->on2 = $on2;
            $this->save();
        }
        return 1;
    }

    public function getCorrect()
    {
        $examControlStudent = ExamControlStudent::find()
            ->where([
                'student_id' => $this->student_id,
                'category' => $this->exam->category,
                // 'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
                'subject_id' => $this->exam->eduSemestrSubject->subject_id,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $on1 = $examControlStudent->ball ?? null;
        if ($this->on1 != $on1) {
            $this->on1 = $on1;
            $this->save();
        }

        $on2 = $examControlStudent->ball2 ?? null;
        if ($this->on2 != $on2) {
            $this->on2 = $on2;
            $this->save();
        }

        return 1;
    }

    public function getOraliq1()
    {
        $on1 = ExamControlStudent::findOne([
            'category' => $this->exam->category,
            'student_id' => $this->student_id,
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ])->ball ?? null;

        // if (is_null($this->on1)) {
        $this->on1 = $on1;
        $this->save();
        // }
        return $this->on1;
    }

    public function getOraliq2()
    {
        $on2 = ExamControlStudent::findOne([
            'category' => $this->exam->category,
            'student_id' => $this->student_id,
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ])->ball2 ?? null;

        // if (is_null($this->on2)) {
        $this->on2 = $on2;
        $this->save();
        // }
        return $this->on2;
    }

    public function getExamControlStudent()
    {
        return ExamControlStudent::findOne([
            'student_id' => $this->student_id,
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semester_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ]);
    }

    public function getExamControl()
    {
        return ExamControl::findOne([
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semester_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ]);
    }



    public function getAllBallChages()
    {
        $this->ball = $this->allBall;
        $this->save(false);
    }


    public function getAllBall()
    {
        if ($this->type == 2) {
            return $this->ball;
        }

        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->andWhere([
            'in',
            $model->tableName() . '.exam_student_answer_id',
            ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
        ])
            ->sum('ball');

        return  $query;
    }


    public function getOldAllBall()
    {
        /* $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query
            ->select(['SUM(COALESCE(old_ball, ball))'])
            ->andWhere([
                'in', $model->tableName() . '.exam_student_answer_id',
                ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
            ])
            ->asArray()
            ->one();

        return  $query; */

        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->select(['SUM(COALESCE(old_ball, ball))'])
            ->andWhere([
                'in',
                $model->tableName() . '.exam_student_answer_id',
                ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
            ]);

        $totalBall = $query->createCommand()->queryScalar();

        return $totalBall;
    }

    public function getFinishedAt()
    {

        // return $this->finish ??
        if ($this->finish > 0) {
            return date("Y-m-d H:i:s", $this->finish);
        } else {
            $exam_finish = $this->start + $this->exam->duration ?? 0 + (int)$this->duration ?? 0;
            if ($exam_finish > strtotime($this->exam->finish)) {
                return date("Y-m-d H:i:s", strtotime($this->exam->finish));
            } else {
                return date("Y-m-d H:i:s", $exam_finish);
            }
        }

        return "Undefined";
    }

    public function getAccessKey()
    {
        return $this->encodemk5MK('MK' . $this->id);

        return $this->encodeMK($this->student_id) . 'MK' . $this->encodeMK($this->id);
    }

    public function getDecodedKey()
    {
        return $this->decodemk5MK('ODEwODMtNzg3MQ');

        return $this->encodeMK($this->student_id) . '-' . $this->encodeMK($this->id);
    }

    public function getIsChecked()
    {

        // return $this->examStudentAnswers->examStudentAnswerSubQuestion;

        $model = new ExamStudentAnswer();
        $query = $model->find()->with('examStudentAnswerSubQuestion');

        $query = $query->andWhere([$model->tableName() . '.exam_student_id' => $this->id])
            ->leftJoin("exam_student_answer_sub_question esasq", "esasq.exam_student_answer_id = " . $model->tableName() . " .id ")
            ->andWhere(['esasq.ball' => null, 'esasq.teacher_conclusion' => null])
            ->andWhere([$model->tableName() . '.teacher_conclusion' => null]);

        if (count($query->all()) > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getIsCheckedFull()
    {
        return ExamStudentAnswerSubQuestion::find()
            ->andWhere([
                'exam_student_answer_id' => ExamStudentAnswer::find()
                    ->select('id')
                    ->where(['exam_student_id' => $this->id])
                    ->column()
            ])
            ->andWhere([
                'or',
                ['is', 'ball', new \yii\db\Expression('null')],
                ['is', 'teacher_conclusion', new \yii\db\Expression('null')]
            ])
            ->exists() ? 0 : 1;
    }

    public function getIsCheckedFullUpdate()
    {
        $this->updateAttributes(['is_checked_full' => $this->getIsCheckedFull()]);
    }

    public function getHasAnswer()
    {
        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->andWhere([
            'in',
            $model->tableName() . '.exam_student_answer_id',
            ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
        ]);

        if (count($query->all()) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getHasAnswernew()
    {
        return (bool)ExamStudentAnswerSubQuestion::find()
            ->andWhere([
                'exam_student_answer_id' => ExamStudentAnswer::find()
                    ->select('id')
                    ->where(['exam_student_id' => $this->id])
            ])
            ->count();
    }

    public function getHasAnswerUpdate()
    {
        $this->updateAttributes(['has_answer' => $this->hasAnswernew]);
    }


    // public function getExamStudentAnswers()
    // {
    //     if(isRole('student')){
    //         if($this->exam->status == 4){
    //             return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);

    //         }
    //     }
    //     return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
    // }

    /**
     * Get the exam student answers relationship.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamStudentAnswers()
    {
        // Check if the current user has the 'student' role
        if (isRole('student')) {
            // Check if the exam status is 4
            if ($this->exam->status == 4) {
                // Return the relationship if conditions are met
                return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
                return $this->hasMany(ExamStudentAnswer::class, ['exam_student_id' => 'id']);
            }
        }

        // Return the default relationship if conditions are not met
        return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
        return $this->hasMany(ExamStudentAnswer::class, ['exam_student_id' => 'id']);
    }


    public function getAnswers()
    {
        // Check if the current user has the 'student' role
        if (isRole('student')) {
            // Check if the exam status is 4
            if ($this->exam->status == 4) {
                // Return the relationship if conditions are met
                return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
                return $this->hasMany(ExamStudentAnswer::class, ['exam_student_id' => 'id']);
            }
        }

        // Return the default relationship if conditions are not met
        return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
        return $this->hasMany(ExamStudentAnswer::class, ['exam_student_id' => 'id']);
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
     * Gets query for [[Exam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamStudentReexam()
    {
        return $this->hasMany(ExamStudentReexam::className(), ['exam_student_id' => 'id']);
    }

    public function getReExam()
    {
        return $this->hasMany(ExamStudentReexam::className(), ['exam_student_id' => 'id']);
    }


    public function getAppeal()
    {
        // Check if the role is student and the appeal status is announced
        if (isRole('student') && $this->exam->status_appeal == Exam::STATUS_APPEAL_ANNOUNCED) {
            return $this->hasOne(ExamAppeal::className(), ['exam_student_id' => 'id']);
        }

        // In other cases, return the relationship without conditions
        return $this->hasOne(ExamAppeal::className(), ['exam_student_id' => 'id']);
    }

    public function getExamAppeal()
    {
        // Check if the role is student and the appeal status is announced
        if (isRole('student') && $this->exam->status_appeal == Exam::STATUS_APPEAL_ANNOUNCED) {
            return $this->hasOne(ExamAppeal::className(), ['exam_student_id' => 'id']);
        }

        // In other cases, return the relationship without conditions
        return $this->hasOne(ExamAppeal::className(), ['exam_student_id' => 'id']);
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
        if (current_user_id() == 1) {
        }

        return null;
    }

    public function getTeacher()
    {
        if (isRole('admin')) {
            return $this->teacherAccess->profile ?? null;
        }

        // if (current_user_id() == 1) {
        // }

        return null;
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


    protected static function actionUpdateExamModel($model)
    {
        if ($model->type > 0) {
            $model->ball = $model->allBall;
            // $model->correct;
            $model->is_checked = $model->isChecked;
            $model->is_checked_full = $model->isCheckedFull;
            $model->has_answer = $model->hasAnswer;

            $model->update();
        }

        return $model;
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

        $model->type = $model->exam->eduSemestrSubject->eduSemestr->type ?? 1;
        $model->edu_year_id = $model->exam->eduSemestrSubject->eduSemestr->edu_year_id;
        // $model->subject_id = $model->exam->eduSemestrSubject->subject_id;

        // $model->exam_id = $examId;
        $model->edu_year_id = $model->exam->eduSemestrSubject->eduSemestr->edu_year_id;
        // $model->student_id = $student_id;
        $model->lang_id = $model->student->edu_lang_id;

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

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // $oldFile = $model->plagiat_file;
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

        if ($model->plagiat_percent > Yii::$app->params['plagiat_percent_max']) {
            $model->is_plagiat = self::IS_PLAGIAT_TRUE;
        }

        // dd(['model' => $model, 'post' => $post, 'errors' => $errors, 'savemodel' => $model->save()]);

        if ($model->save() && count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function actItemWithCreate($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->type = $model->exam->eduSemestrSubject->eduSemestr->type ?? 1;
        $model->edu_year_id = $model->exam->eduSemestrSubject->eduSemestr->edu_year_id;
        // $model->subject_id = $model->exam->eduSemestrSubject->subject_id;

        // $model->exam_id = $examId;
        $model->edu_year_id = $model->exam->eduSemestrSubject->eduSemestr->edu_year_id;
        // $model->student_id = $student_id;
        $model->lang_id = $model->student->edu_lang_id;


        $model->actFile = UploadedFile::getInstancesByName('actFile');
        if ($model->actFile) {
            $model->actFile = $model->actFile[0];
            $actFileUrl = $model->uploadActFile();

            if ($actFileUrl) {
                $model->act_file = $actFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // $model->act = self::ACT_TRUE;
        if (isset($post['act_reason'])) {
            $model->act_reason = $post['act_reason'];
            $model->act_reason_created_by = current_user_id();
            $model->act_reason_created_at = time();
            $model->act = self::ACT_NOT_COMFORMED;
        }
        if (isRole('edu_admin') || isRole('admin')) {
            $model->act = self::ACT_TRUE;
        }
        if (isset($post['act'])) {
            if ($model->act_reason_created_by != current_user_id()) {
                $model->act = self::ACT_TRUE;
                $model->act_confirmed_created_by = current_user_id();
                $model->act_confirmed_created_at = time();
            } else {
                $errors[] = _e('You cannot confirm the Act because you have entered the act');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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

    public static function actItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $oldFile = $model->act_file;

        // act file saqlaymiz
        $model->actFile = UploadedFile::getInstancesByName('actFile');
        if ($model->actFile) {
            $model->actFile = $model->actFile[0];
            $actFileUrl = $model->uploadActFile();

            if ($actFileUrl) {
                $model->act_file = $actFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // $model->act = self::ACT_TRUE;
        if (isset($post['act_reason'])) {
            $model->act_reason = $post['act_reason'];
            $model->act_reason_created_by = current_user_id();
            $model->act_reason_created_at = time();
            $model->act = self::ACT_NOT_COMFORMED;
        }
        if (isRole('edu_admin') || isRole('admin')) {
            $model->act = self::ACT_TRUE;
        }
        if (isset($post['act'])) {
            if ($model->act_reason_created_by != current_user_id()) {
                $model->act = self::ACT_TRUE;
                $model->act_confirmed_created_by = current_user_id();
                $model->act_confirmed_created_at = time();
            } else {
                $errors[] = _e('You cannot confirm the Act because you have entered the act');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        // if()



        if ($model->save() && count($errors) == 0) {
            // $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteMK($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $examStudent = ExamStudent::find()->where(['id' => $model->id])->one();

        $examStudentAnswers = ExamStudentAnswer::find()->where(['exam_student_id' => $model->id])->all();

        foreach ($examStudentAnswers as $examStudentAnswerOne) {
            $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::find()->where(['exam_student_answer_id' => $examStudentAnswerOne->id])->all();
            foreach ($examStudentAnswerSubQuestion as $examStudentAnswerSubQuestionOne) {
                $examStudentAnswerSubQuestionDeteledNew = new ExamStudentAnswerSubQuestionDeleted();
                // $examStudentAnswerSubQuestionDeteledNew->load($examStudentAnswerSubQuestionOne, '');

                $examStudentAnswerSubQuestionDeteledNew->exam_student_answer_sub_question_id = $examStudentAnswerSubQuestionOne->id;

                $examStudentAnswerSubQuestionDeteledNew->file = $examStudentAnswerSubQuestionOne->file;
                $examStudentAnswerSubQuestionDeteledNew->exam_student_answer_id = $examStudentAnswerSubQuestionOne->exam_student_answer_id;

                $examStudentAnswerSubQuestionDeteledNew->sub_question_id = $examStudentAnswerSubQuestionOne->sub_question_id;
                $examStudentAnswerSubQuestionDeteledNew->teacher_conclusion = $examStudentAnswerSubQuestionOne->teacher_conclusion;
                $examStudentAnswerSubQuestionDeteledNew->answer = $examStudentAnswerSubQuestionOne->answer;
                $examStudentAnswerSubQuestionDeteledNew->ball = $examStudentAnswerSubQuestionOne->ball;
                $examStudentAnswerSubQuestionDeteledNew->max_ball = $examStudentAnswerSubQuestionOne->max_ball;

                $examStudentAnswerSubQuestionDeteledNew->order = $examStudentAnswerSubQuestionOne->order;
                $examStudentAnswerSubQuestionDeteledNew->status = $examStudentAnswerSubQuestionOne->status;

                $examStudentAnswerSubQuestionDeteledNew->is_deleted = $examStudentAnswerSubQuestionOne->is_deleted;
                $examStudentAnswerSubQuestionDeteledNew->exam_student_id = $examStudentAnswerSubQuestionOne->exam_student_id;
                $examStudentAnswerSubQuestionDeteledNew->archived = $examStudentAnswerSubQuestionOne->archived;
                $examStudentAnswerSubQuestionDeteledNew->student_id = $examStudentAnswerSubQuestionOne->student_id;
                $examStudentAnswerSubQuestionDeteledNew->old_ball = $examStudentAnswerSubQuestionOne->old_ball;
                $examStudentAnswerSubQuestionDeteledNew->old_ball_calculate = $examStudentAnswerSubQuestionOne->old_ball_calculate;
                $examStudentAnswerSubQuestionDeteledNew->is_cheked = $examStudentAnswerSubQuestionOne->is_cheked;
                $examStudentAnswerSubQuestionDeteledNew->student_created_at = $examStudentAnswerSubQuestionOne->student_created_at;
                $examStudentAnswerSubQuestionDeteledNew->student_updated_at = $examStudentAnswerSubQuestionOne->student_updated_at;



                $examStudentAnswerSubQuestionDeteledNew->created_at_o = $examStudentAnswerSubQuestionOne->created_at;
                $examStudentAnswerSubQuestionDeteledNew->updated_at_o = $examStudentAnswerSubQuestionOne->updated_at;
                $examStudentAnswerSubQuestionDeteledNew->created_by_o = $examStudentAnswerSubQuestionOne->created_by;
                $examStudentAnswerSubQuestionDeteledNew->updated_by_o = $examStudentAnswerSubQuestionOne->updated_by;

                if (!($examStudentAnswerSubQuestionDeteledNew->save() && $examStudentAnswerSubQuestionOne->delete())) {
                    $errors[] = _e("Deleting on ExamStudentAnswerSubQuestion ID(" . $examStudentAnswerSubQuestionOne->id . ")");
                }
                // return $examStudentAnswerSubQuestionDeteledNew;
            }
            $ExamStudentAnswerDeletedNew = new ExamStudentAnswerDeleted();
            // $ExamStudentAnswerDeletedNew->load($examStudentAnswerOne, '');
            $ExamStudentAnswerDeletedNew->exam_student_answer_id = $examStudentAnswerOne->id;


            $ExamStudentAnswerDeletedNew->exam_id = $examStudentAnswerOne->exam_id;
            $ExamStudentAnswerDeletedNew->question_id = $examStudentAnswerOne->question_id;
            $ExamStudentAnswerDeletedNew->parent_id = $examStudentAnswerOne->parent_id;
            $ExamStudentAnswerDeletedNew->student_id = $examStudentAnswerOne->student_id;
            $ExamStudentAnswerDeletedNew->option_id = $examStudentAnswerOne->option_id;
            $ExamStudentAnswerDeletedNew->teacher_access_id = $examStudentAnswerOne->teacher_access_id;
            $ExamStudentAnswerDeletedNew->exam_student_id = $examStudentAnswerOne->exam_student_id;
            $ExamStudentAnswerDeletedNew->attempt = $examStudentAnswerOne->attempt;
            $ExamStudentAnswerDeletedNew->type = $examStudentAnswerOne->type;

            $ExamStudentAnswerDeletedNew->order = $examStudentAnswerOne->order;
            $ExamStudentAnswerDeletedNew->status = $examStudentAnswerOne->status;
            $ExamStudentAnswerDeletedNew->is_deleted = $examStudentAnswerOne->is_deleted;

            $ExamStudentAnswerDeletedNew->archived = $examStudentAnswerOne->archived;
            $ExamStudentAnswerDeletedNew->appeal_teacher_conclusion = $examStudentAnswerOne->appeal_teacher_conclusion;
            $ExamStudentAnswerDeletedNew->student_created_at = $examStudentAnswerOne->student_created_at;
            $ExamStudentAnswerDeletedNew->student_updated_at = $examStudentAnswerOne->student_updated_at;

            $ExamStudentAnswerDeletedNew->created_at_o = $examStudentAnswerOne->created_at;
            $ExamStudentAnswerDeletedNew->updated_at_o = $examStudentAnswerOne->updated_at;
            $ExamStudentAnswerDeletedNew->created_by_o = $examStudentAnswerOne->created_by;
            $ExamStudentAnswerDeletedNew->updated_by_o = $examStudentAnswerOne->updated_by;

            if (!($ExamStudentAnswerDeletedNew->save() && $examStudentAnswerOne->delete())) {
                $errors[] = _e("Deleting on ExamStudentAnswer ID(" . $examStudentAnswerOne->id . ")");
            }
        }

        $examStudentDeletedNew = new ExamStudentDeleted();
        $examStudentDeletedNew->student_id = $model->student_id;
        $examStudentDeletedNew->exam_student_id = $model->id;
        $examStudentDeletedNew->start = $model->start;
        $examStudentDeletedNew->finish = $model->finish;
        $examStudentDeletedNew->exam_id = $model->exam_id;
        $examStudentDeletedNew->teacher_access_id = $model->teacher_access_id;
        $examStudentDeletedNew->attempt = $model->attempt;
        $examStudentDeletedNew->lang_id = $model->lang_id;
        $examStudentDeletedNew->exam_semeta_id = $model->exam_semeta_id;
        $examStudentDeletedNew->is_plagiat = $model->is_plagiat;
        $examStudentDeletedNew->duration = $model->duration;
        $examStudentDeletedNew->ball = $model->ball;
        $examStudentDeletedNew->plagiat_file = $model->plagiat_file;
        $examStudentDeletedNew->password = $model->password;
        $examStudentDeletedNew->plagiat_percent = $model->plagiat_percent;

        $examStudentDeletedNew->conclusion = $model->conclusion;

        $examStudentDeletedNew->order = $model->order;
        $examStudentDeletedNew->status = $model->status;
        $examStudentDeletedNew->is_deleted = $model->is_deleted;

        $examStudentDeletedNew->created_at_o = $model->created_at;
        $examStudentDeletedNew->updated_at_o = $model->updated_at;
        $examStudentDeletedNew->created_by_o = $model->created_by;
        $examStudentDeletedNew->updated_by_o = $model->updated_by;

        $examStudentDeletedNew->save();

        $examStudent->duration = null;
        $examStudent->start = null;
        $examStudent->act = 0;
        $examStudent->act_reason = null;
        $examStudent->status = 0;
        $examStudent->attempt = $examStudent->attempt + 1;

        if (count($errors) != 0) {

            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!($examStudent->validate())) {

            $errors[] = $examStudent->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($examStudent->save()) {
            // dd(['saved' => $examStudent->save(), 'examStudent' => $examStudent]);
            $transaction->commit();
            return true;
        } else {
            // dd(['not saved' => $examStudent->save(), 'examStudent' => $examStudent]);

            $errors = $examStudent->getErrors();
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * Modelni “arxivlash” (yoki o‘chirish) jarayoni.
     * try/catch ishlatilmagan, xatoliklar oldingidek qaytariladi
     *
     * @param ExamStudent $model
     * @return bool|string true yoki xatolik matni
     */
    public static function deleteMK123123($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // 1. ExamStudentAnswer va unga bog‘liq subQuestion-larni arxivlab-o‘chirish
        $examStudentAnswers = $model->getExamStudentAnswers()->all();
        foreach ($examStudentAnswers as $answer) {
            // SubQuestion-lar
            $subQuestions = $answer->getExamStudentAnswerSubQuestions()->all();
            foreach ($subQuestions as $subQuestion) {
                // Arxiv (deleted) model
                $deletedSubQuestion = new ExamStudentAnswerSubQuestionDeleted();
                // Barcha attributelarni bir ko‘tarishda set qilish:
                $deletedSubQuestion->attributes = $subQuestion->attributes;
                // Asosiy ID ni ham saqlab qolish uchun:
                $deletedSubQuestion->exam_student_answer_sub_question_id = $subQuestion->id;

                if (!$deletedSubQuestion->save()) {
                    $errors[] = "SubQuestionDeleted saqlashda xatolik: " . json_encode($deletedSubQuestion->errors);
                }
                if (!$subQuestion->delete()) {
                    $errors[] = "SubQuestion o‘chirishda xatolik: " . json_encode($subQuestion->errors);
                }
            }

            // Answer’ni arxivlab-o‘chirish
            $deletedAnswer = new ExamStudentAnswerDeleted();
            $deletedAnswer->attributes = $answer->attributes;
            $deletedAnswer->exam_student_answer_id = $answer->id;

            if (!$deletedAnswer->save()) {
                $errors[] = "ExamStudentAnswerDeleted saqlashda xatolik: " . json_encode($deletedAnswer->errors);
            }
            if (!$answer->delete()) {
                $errors[] = "ExamStudentAnswer o‘chirishda xatolik: " . json_encode($answer->errors);
            }
        }

        // 2. ExamStudentDeleted jadvaliga yozuv kiritish
        $deletedExamStudent = new ExamStudentDeleted();
        $deletedExamStudent->attributes = $model->attributes;
        $deletedExamStudent->exam_student_id = $model->id;

        if (!$deletedExamStudent->save()) {
            $errors[] = "ExamStudentDeleted saqlashda xatolik: " . json_encode($deletedExamStudent->errors);
        }

        // 3. Asosiy modeldagi ustunlarni o‘zgartirish
        $model->duration = null;
        $model->start    = null;
        $model->act      = 0;
        $model->act_reason = null;
        $model->status   = 0;
        $model->attempt  = $model->attempt + 1;

        // Agar validatsiya xato bo‘lsa, xatolarni yig‘amiz
        if (!$model->validate()) {
            // $model->errors massiv bo‘lgani uchun xatoni to‘g‘ridan-to‘g‘ri qo‘shamiz
            $errors[] = $model->errors;
        }

        // Agar oldingi bosqichlarda xatolik to‘plangan bo‘lsa, rollBack qilamiz
        if (!empty($errors)) {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Moddiy o‘zgartirishlarni saqlaymiz (validatsiyasiz, chunki tepada validate qildik)
        if (!$model->save(false)) {
            $errors[] = "ExamStudent saqlashda xatolik: " . json_encode($model->errors);
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Hammasi yaxshi bo‘lsa, transaction-ni commit qilamiz
        $transaction->commit();
        return true;
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
    public function uploadActFile()
    {
        if ($this->validate()) {
            $filePath = self::UPLOADS_FOLDER_ACT . $this->exam_id . '/';
            if (!file_exists(STORAGE_PATH . $filePath)) {
                mkdir(STORAGE_PATH . $filePath, 0777, true);
            }

            // kim qachonm qilgani yoziladi act fayl nomida
            $fileName = current_user_id() . "_" . time() . "_" . $this->student_id . '.' . $this->actFile->extension;

            $miniUrl = $filePath . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->actFile->saveAs($url, false);
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


    public static function correct($i)
    {
        $soni = $i * 5000;
        // $model = ExamStudent::find()
        //     // ->where(['type' => null])
        //     // ->andWhere(['is_checked_full' => 0])
        //     ->limit(5000)->offset($soni)->all();

        $model = ExamStudent::find()
            // ->where(['type' => null])
            // ->andWhere(['is_checked_full' => 0])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5000)
            ->offset($soni)
            ->all();


        foreach ($model as $modelOne) {
            if (!($modelOne->type > 0)) {

                $modelOne->ball = $modelOne->allBall;

                $modelOne->is_checked = $modelOne->isChecked;
                $modelOne->is_checked_full = $modelOne->isCheckedFull;
                $modelOne->has_answer = $modelOne->hasAnswer;
                $modelOne->update();
            }
        }

        return true;
    }


    // public function beforeSaveas($insert)
    // {
    //     if ($insert) {
    //         $this->created_by = current_user_id();
    //     } else {
    //         $this->updated_by = current_user_id();
    //     }
    //     return parent::beforeSave($insert);
    // }

    // public function beforeSave($insert)
    // {
    //     // Check if it's an insert operation
    //     if ($insert) {
    //         $this->created_by = current_user_id();

    //         // Find the latest student mark for this edu_semestr_subject_id
    //         $latestStudentMark = StudentMark::find()
    //             ->where(['edu_semestr_subject_id' => $this->edu_semestr_subject_id])
    //             ->orderBy(['id' => 'DESC'])
    //             ->one();

    //         // Find the corresponding examControlStudent record
    //         $examStudent = self::find()
    //             ->where(['is not null', 'student_mark_id'])
    //             ->andWhere(['edu_semestr_subject_id' => $this->edu_semestr_subject_id])
    //             ->one();

    //         $studentMarkNew = new StudentMark();

    //         if ($latestStudentMark && $examStudent) {
    //             $studentMarkNew->attempt = $latestStudentMark->attempt + 1;
    //         }

    //         $studentMarkNew->student_id = $this->student_id;
    //         $studentMarkNew->subject_id = $this->subject_id;
    //         $studentMarkNew->edu_semestr_id = $this->edu_semester_id;
    //         $studentMarkNew->edu_semestr_subject_id = $this->edu_semestr_subject_id;
    //         $studentMarkNew->course_id = $this->course_id;
    //         $studentMarkNew->semestr_id = $this->semestr_id;
    //         $studentMarkNew->edu_year_id = $this->edu_year_id;
    //         $studentMarkNew->edu_plan_id = $this->edu_plan_id;
    //         $studentMarkNew->faculty_id = $this->student->faculty_id;
    //         $studentMarkNew->edu_plan_id = $this->student->edu_plan_id;
    //         $studentMarkNew->exam_control_student_ball = $this->ball;
    //         $studentMarkNew->exam_control_student_ball2 = $this->ball2;

    //         if ($studentMarkNew->save()) {
    //             $this->student_mark_id = $studentMarkNew->id;
    //         }
    //     } else {
    //         $this->updated_by = current_user_id();

    //         // Update the corresponding student mark record
    //         $studentMark = StudentMark::findOne(['id' => $this->student_mark_id]);
    //         $studentMark->exam_control_student_ball = $this->ball;
    //         $studentMark->exam_control_student_ball2 = $this->ball2;

    //         $studentMark->save(false);
    //     }

    //     return parent::beforeSave($insert);
    // }

    public function beforeSave($insert)
    {
        if (!isRole('admin')) {

            // $this->correct;
            if ($insert) {
                $this->created_by = current_user_id();
                // $StudentMark = StudentMark::createItemFromExam($insert);
                // if ($StudentMark['status']) {
                //     $this->student_mark_id =  $StudentMark['data']->id;
                // }
            } else {
                $this->updated_by = current_user_id();
                // StudentMark::updateItemFromExam($this->student_mark_id, $this->ball);
            }
            $this->is_checked_full = $this->isCheckedFull;
        }
        return parent::beforeSave($insert);
    }
}
