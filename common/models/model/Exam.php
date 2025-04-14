<?php

namespace common\models\model;

use api\resources\ResourceTrait;

use common\models\model\Student;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $exam_type_id
 * @property int $faculty_id
 * @property int $edu_semestr_subject_id
 * @property int $edu_plan_id
 * @property string $start
 * @property string $finish
 * @property float|null $max_ball
 * @property float|null $min_ball
 * @property int|null $type
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrSubject $eduSemestrSubject
 * @property ExamsType $examType
 * @property ExamQuestion[] $examQuestions
 * @property ExamStudentAnswer[] $examStudentAnswers
 */
class Exam extends \yii\db\ActiveRecord
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
    const STATUS_FINISHED = 2;
    const STATUS_DISTRIBUTED = 3;
    const STATUS_ANNOUNCED = 4;
    const STATUS_CHEKLANGAN = 5;

    const STATUS_APPEAL_INACTIVE = 0;
    const STATUS_APPEAL_ACTIVE = 1;
    const STATUS_APPEAL_FINISHED = 2;
    const STATUS_APPEAL_DISTRIBUTED = 3;
    const STATUS_APPEAL_ANNOUNCED = 4;

    const PROTECTED_TURE = 1;
    const PROTECTED_FALSE = 0;

    // 1-oddiy 2-intensiv
    const CATEGORY_MAIN = 1;
    const CATEGORY_INTENSIV = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        // Check if user wants archived data (e.g., via a parameter or a flag)
        if (Yii::$app->request->get('archived')) {
            return 'exam_23_24';
        }
        if (current_user_id() == 7656) {
            return 'exam_23_24';
        }
        return 'exam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_type_id', 'edu_semestr_subject_id', 'start', 'finish'], 'required'],
            [
                [
                    'old_exam_id',
                    'category',
                    'status_appeal',
                    'edu_year_id',
                    'exam_type_id',
                    'faculty_id',
                    'is_protected',
                    'duration',
                    'subject_id',
                    'edu_semestr_subject_id',
                    'edu_plan_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'appeal_start',
                    'appeal_finish',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'archived',
                    'password',
                ],
                'integer'
            ],
            // [['name', 'table_name', 'description'], 'string', 'max' => 255],

            // [['password'], 'string', 'max' => 255],
            [['start', 'checking_at', 'checking_until', 'finish'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['max_ball', 'min_ball'], 'double'],
            [['question_count_by_type'], 'safe'],
            [['question_count_by_type_with_ball'], 'safe'],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],

            // edu_semestr_subject_id, edu_year_id, max_ball
            // [['edu_semestr_subject_id', 'edu_year_id', 'category', 'max_ball'], 'unique', 'targetAttribute' => ['category', 'edu_semestr_subject_id', 'edu_year_id', 'max_ball']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name in translate

            'faculty_id' => 'Faculty Id',
            'direction_id' => 'Direction Id',

            'question_count_by_type' => 'Question Count By Type',
            'exam_type_id' => 'Exam Type ID',
            'edu_semestr_subject_id' => 'Edu Semestr Subject ID',
            'edu_plan_id' => 'Edu Plan ID',
            'start' => 'Start',
            'finish' => 'Finish',
            'is_protected' => 'Is Protected',
            'duration' => 'Duration',
            'max_ball' => 'Max Ball',
            'min_ball' => 'Min Ball',
            'type' => 'Type',
            'subject_id' => 'subject_id',
            'edu_year_id' => 'Edu Year',

            'appeal_start' => 'appeal_start',
            'appeal_finish' => 'appeal_finish',
            'category' => 'category',
            'password' => 'password',

            'order' => _e('Order'),
            'status' => _e('Status'),
            'status_appeal' => _e('Status appeal'),
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
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'question_count_by_type',
            'question_count_by_type_with_ball',
            'exam_type_id',
            'edu_semestr_subject_id',
            'start',
            'finish',
            'faculty_id',
            'direction_id',
            'duration',
            'is_protected',
            'max_ball',
            'min_ball',
            'checking_at',
            'checking_until',
            'archived',

            'subject_id',


            'category',
            'appeal_start',
            'appeal_finish',
            'old_exam_id',

            'order',
            'status',
            'status_appeal',
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
            'eduSemestrSubject',
            'examType',
            'faculty',
            'direction',
            'subject',
            'subjectName',

            'statusName',
            'oldExam',
            'relExam',

            'examQuestions',
            'examStudentAnswers',

            'examStudent',
            'examStudentMain',
            'examStudentCount',
            'examStudentByLang',

            'examAppealByLang',
            'appealCount',
            'appeal',
            'appealCheckedCount',
            'appealCheked',
            'examAppealSemeta',

            'checkCount',

            'teacherAccess',
            'examSmeta',
            'typeName',

            'isConfirmed',
            'isConfirmedAppeal',

            'eduPlan',
            'questionCount',
            'question',

            'key',
            'hasAccess',
            'surveyStatus',
            'surveyAnswer',

            'studentSubjectRestrict',

            // 'allow',


            'examStudentNo',
            'examStudentNoAnswer',
            'examStudentNoAnswerCount',
            'examStudentAct',
            'examStudentActCount',
            'examStudentOrigin',
            'examStudentOriginCount',

            //
            'supervisor',
            'examSupervisor',

            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getHasAccess()
    {
        if (isRole('student')) {

            if (StudentSubjectRestrict::find()->where([
                'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
                'student_id' => self::student(),
                'is_deleted' => 0,
                'status' => 1,
                'archived' => 0,
            ])->exists()) {
                return 1;
            }
            return 0;
        }
        return 1;
    }

    public function getIsConfirmed()
    {
        if (ExamSemeta::findOne(['exam_id' => $this->id])) {
            if (ExamSemeta::findOne(['exam_id' => $this->id, 'status' => ExamSemeta::STATUS_NEW])) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    public function getIsConfirmedAppeal()
    {
        if (ExamAppealSemeta::findOne(['exam_id' => $this->id])) {
            if (ExamAppealSemeta::findOne(['exam_id' => $this->id, 'status' => ExamAppealSemeta::STATUS_NEW])) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    public function getSupervisor()
    {
        return $this->hasMany(ExamSupervisor::className(), ['exam_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }
    public function getSupervisorNames()
    {
        // Retrieve the exam supervisors associated with this exam
        $supervisors = $this->examSupervisor;

        // Initialize an empty array to store the names
        $names = [];
        $namesStr = '';

        // Loop through each supervisor and retrieve their profile
        foreach ($supervisors as $supervisor) {
            $profile = $supervisor->profile;
            if ($profile) {
                // If the profile exists, add the last name and first name to the names array
                $names[] = [
                    'last_name' => $profile->last_name,
                    'first_name' => $profile->first_name,
                ];

                $namesStr  .= $profile->last_name . " " . $profile->first_name . "\n";
            }
        }

        return $namesStr;
        return $names;
    }

    public function getExamSupervisor()
    {
        return $this->hasMany(ExamSupervisor::className(), ['exam_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getTypeName()
    {
        return TeacherCheckingType::typeList()[$this->status];
    }

    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => 'exam']);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => 'exam']);
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getStudentSubjectRestrict()
    {
        if (isRole('student')) {
            return $this->hasOne(
                StudentSubjectRestrict::className(),
                [
                    'edu_semestr_subject_id' => 'edu_semestr_subject_id'
                ]
            )->onCondition([
                'student_id' => self::student(),
                'is_deleted' => 0
            ]);
        }
        return $this->hasMany(
            StudentSubjectRestrict::className(),
            ['edu_semestr_subject_id' => 'edu_semestr_subject_id']
        )
            ->onCondition(['is_deleted' => 0]);
    }

    public function getAppeal()
    {
        if (isRole('teacher') && (!isRole('mudir'))) {
            return  ExamAppeal::find()
                ->where(['exam_id' => $this->id])
                ->andWhere(['in', 'teacher_access_id', self::teacher_access()])
                ->all();
        }

        return $this->hasMany(ExamAppeal::className(), ['exam_id' => 'id']);
    }
    public function getAppealCount()
    {
        return count($this->appeal);
    }

    public function getAppealCheked()
    {
        if (isRole('teacher') && (!isRole('mudir'))) {
            return ExamAppeal::find()
                ->where(['exam_id' => $this->id])
                ->andWhere(['IS NOT', 'type', new \yii\db\Expression('NULL')])
                ->andWhere(['in', 'teacher_access_id', self::teacher_access()])
                ->all();
        }

        return $this->hasMany(ExamAppeal::className(), ['exam_id' => 'id']);
    }

    // public function getAppealCheked()
    // {
    //     if (isRole('teacher') && (!isRole('mudir'))) {
    //         return  ExamAppeal::find()
    //             ->where(['exam_id' => $this->id])
    //             ->andWhere(['not null', 'type'])
    //             ->andWhere(['in', 'teacher_access_id', self::teacher_access()])
    //             ->all();
    //     }

    //     return $this->hasMany(ExamAppeal::className(), ['exam_id' => 'id']);
    // }


    public function getAppealCheckedCount()
    {
        return count($this->appealCheked);
    }

    public function getEduPlan()
    {
        return $this->eduSemestrSubject->eduSemestr->eduPlan ?? [];
    }

    public function getQuestion()
    {
        return $this->eduSemestrSubject->subject->questionActive ?? [];
    }

    public function getQuestionCount()
    {
        return count($this->question);
    }

    public function getSubjectt()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getSubject()
    {
        return $this->eduSemestrSubject->subject ?? [];
    }

    public function getSubjectName()
    {
        return $this->eduSemestrSubject->subject->name ?? '';
    }

    public function getExamStudentMain()
    {
        if (isRole('student')) {
            return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_id' => $this->student()]);
        }

        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->orderBy(new \yii\db\Expression('RAND()'));
    }

    public function getSurveyAnswer()
    {
        if (isRole('student')) {
            return $this->hasMany(SurveyAnswer::className(), ['exam_id' => 'id'])->onCondition(['user_id' => current_user_id()]);
        }
        return $this->hasMany(SurveyAnswer::className(), ['exam_id' => 'id']);
    }

    // public function getSurveyStatus()
    // {
    //     if (isRole('student')) {
    //         if (
    //             count(SurveyAnswer::find()
    //                 ->where(['exam_id' => $this->id, 'user_id' => current_user_id()])
    //                 ->all())
    //             ==
    //             count(SurveyQuestion::find()
    //                 ->where(['status' => SurveyQuestion::STATUS_ACTIVE, 'is_deleted' => 0])
    //                 ->all())

    //         ) {
    //             return 1;
    //         } else {
    //             return 0;
    //         }
    //     }
    //     return 1;
    // }
    public function getSurveyStatus()
    {
        if (isRole('student')) {
            if (
                count(SurveyAnswer::find()
                    ->where(['exam_id' => $this->id, 'user_id' => current_user_id()])
                    ->all()) > 0
            ) {
                return 1;
            } else {
                return 0;
            }
        }
        return 1;
    }

    public function getKey()
    {
        return $this->password ?? '';
    }

    public function getExamStudentNo()
    {
        return Student::find()
            ->where(['edu_plan_id' => $this->eduSemestrSubject->eduSemestr->edu_plan_id])
            ->andWhere(['is_deleted' => 0])
            // ->andWhere(['is_deleted' => 1])
            ->andWhere([
                'not in',
                'id',
                ExamStudent::find()
                    ->select('student_id')
                    ->where(['exam_id' => $this->id])
            ])
            // ->select(['id', 'last_name', 'first_name', 'middle_name', 'pasport_pin', 'faculty_id'])
            ->all();
    }

    public function getExamStudentAct()
    {
        return $this->hasMany(ExamStudent::class, ['exam_id' => 'id'])
            ->andWhere(['act' => 1]);
    }
    public function getExamStudentActCount()
    {
        return count($this->examStudentAct);
    }


    public function getExamStudentOriginQuery()
    {
        $subQuery = ExamStudentAnswerSubQuestion::find()
            ->select('exam_student_id')
            ->where('exam_student_id = exam_student.id');

        $query = $this->hasMany(ExamStudent::class, ['exam_id' => 'id'])
            ->andWhere(['EXISTS', $subQuery])
            ->andWhere(['act' => 0]);

        if (isRole('teacher') && (!isRole('mudir'))) {
            $query->andWhere(['in', 'teacher_access_id', self::teacher_access()]);
        }

        return $query;
    }


    public function getExamStudentOriginCount()
    {
        // Count records in SQL, not PHP
        return $this->getExamStudentOriginQuery()->count();
    }

    // public function getExamStudentOrigin()
    // {
    //     return $this->hasMany(ExamStudent::class, ['exam_id' => 'id'])
    //         ->andWhere([
    //             'EXISTS', ExamStudentAnswerSubQuestion::find()
    //                 ->select('exam_student_id')
    //                 ->where('exam_student_id = exam_student.id')
    //         ])
    //         ->andWhere(['act' => 0]);
    // }
    // public function getExamStudentOriginCount()
    // {
    //     return count($this->examStudentOrigin);
    // }

    public function getExamStudentNoAnswer()
    {
        return $this->hasMany(ExamStudent::class, ['exam_id' => 'id'])
            ->andWhere([
                'NOT EXISTS',
                ExamStudentAnswerSubQuestion::find()
                    ->select('exam_student_id')
                    ->where('exam_student_id = exam_student.id')
            ]);
    }
    public function getExamStudentNoAnswerCount()
    {
        return count($this->examStudentNoAnswer);
    }

    public function getExamStudent()
    {
        $supervisor = Yii::$app->request->get('supervisor');
        if (isset($supervisor)) {
            return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id']);
        }

        if (isRole('student')) {
            return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_id' => $this->student()]);
        }

        if (isRole('teacher') && (!isRole('mudir'))) {
            return  ExamStudent::find()
                ->where(['exam_id' => $this->id])
                ->andWhere(['in', 'teacher_access_id', self::teacher_access()])
                ->all();
        }

        $student_id = Yii::$app->request->get('student_id');
        if (isset($student_id)) {
            return $this->hasOne(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_id' => $student_id]);
        }
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id']);
    }

    public function getExamStudentCountMain()
    {
        return count($this->examStudent);
    }

    public function getExamStudentCount()
    {
        return count($this->examStudent);
    }

    public function getCheckCount()
    {
        $model = new ExamStudent();
        $query = $model->find();
        $query->andWhere([$model->tableName() . '.exam_id' => $this->id]);

        // if (isRole('teacher')) {
        if (isRole('teacher') && (!isRole('mudir'))) {
            $query->andWhere(['in', $model->tableName() . '.teacher_access_id', self::teacher_access()]);
        }

        $query->leftJoin("exam_student_answer", "exam_student_answer.exam_student_id = " . $model->tableName() . ".id ")
            ->leftJoin("exam_student_answer_sub_question", "exam_student_answer_sub_question.exam_student_answer_id = exam_student_answer.id")
            // ->andWhere(['not', ['esasq.ball' => null, 'esasq.teacher_conclusion' => null]])

            ->andWhere(['IS NOT', 'exam_student_answer_sub_question.ball', null])
            ->andWhere(['IS NOT', 'exam_student_answer_sub_question.teacher_conclusion', null])
            ->groupBy('exam_student.id');

        return count($query->all());
    }


    public function getTeacherAccess()
    {
        return $this->subject->teacherAccesses;
        $model = new TeacherAccess();
        $query = $model->find();
        $table_name = 'teacher_access';
        $query = $query->andWhere([$table_name . '.is_deleted' => 0]);

        $query = $query
            ->leftJoin("users us", "us.id = $table_name.user_id")
            ->andWhere(['us.deleted' => 0])
            ->andWhere([$table_name . '.subject_id' => $this->eduSemestrSubject->subject->id, $table_name . '.status' => 1])
            ->groupBy($table_name . '.id');
        // dd($query->createCommand()->getRawSql());

        return $query->all();
    }

    public function getExamSmeta()
    {
        return $this->hasMany(ExamSemeta::className(), ['exam_id' => 'id']);
    }

    public function getExamAppealByLang()
    {
        return (new yii\db\Query())
            ->from('exam_appeal')
            ->select(['COUNT(*) AS count', 'lang_id'])
            ->where(['exam_id' => $this->id])
            // ->andWhere(['!=', 'status', ExamStudent::STATUS_INACTIVE])
            ->groupBy(['lang_id'])
            ->all();
    }

    public function getExamStudentByLang()
    {
        return (new yii\db\Query())
            ->from('exam_student')
            ->select(['COUNT(*) AS count', 'lang_id'])
            ->where(['exam_id' => $this->id])
            // ->andWhere(['!=', 'status', ExamStudent::STATUS_INACTIVE])
            ->groupBy(['lang_id'])
            ->all();
    }

    public function getExamType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exam_type_id']);
    }

    public function getExamQuestions()
    {
        return $this->hasMany(ExamQuestion::className(), ['exam_id' => 'id']);
    }

    public function getExamStudentAnswers()
    {
        return $this->hasMany(ExamStudentAnswer::className(), ['exam_id' => 'id']);
    }

    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['faculty_id' => 'id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['direction_id' => 'id']);
    }

    public function getStatusName()
    {
        return $this->statusList()[$this->status];
    }

    public function getSemeta()
    {
        // if (isRole('teacher')) {

        // }
        return $this->hasMany(ExamSemeta::className(), ['exam_id' => 'id']);
    }

    public function getExamAppealSemeta()
    {
        // if (isRole('teacher')) {

        // }
        return $this->hasMany(ExamAppealSemeta::className(), ['exam_id' => 'id']);
    }

    public function getOldExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'old_exam_id']);
    }

    public function getRelExam()
    {
        return $this->hasOne(Exam::className(), ['old_exam_id' => 'id']);
    }

    public static function generatePasswords1($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $examId = isset($post['exam_id']) ?  $post['exam_id'] : null;

        if (isset($examId)) {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                $eduSemestrSubject = EduSemestrSubject::findOne($exam->edu_semestr_subject_id);
                if (isset($eduSemestrSubject)) {

                    // $studentTimeTable = StudentTimeTable::find()
                    //     // ->select(['student_time_table.id as id', 'student_time_table.student_id as student_id', 'tt.language_id as lang_id'])
                    //     ->leftJoin("time_table tt", "tt.id = student_time_table.time_table_id")
                    //     ->where([
                    //         'tt.edu_semester_id' => $eduSemestrSubject->edu_semestr_id,
                    //         'tt.subject_id' => $eduSemestrSubject->subject_id,
                    //     ])
                    //     ->all();

                    // foreach ($studentTimeTable as $studentTimeTableOne) {
                    //     $student_id = $studentTimeTableOne->student_id;
                    //     $langId = $studentTimeTableOne->timeTable->language_id;

                    //     $ExamStudentHas = ExamStudent::find()->where([
                    //         'exam_id' => $examId,
                    //         'student_id' => $student_id,
                    //     ])
                    //         ->orderBy('id desc')
                    //         ->one();

                    //     if (isset($ExamStudentHas)) {
                    //         $ExamStudent = $ExamStudentHas;
                    //     } else {
                    //         $ExamStudent = new ExamStudent();
                    //     }

                    //     $ExamStudent->exam_id = $examId;
                    //     $ExamStudent->edu_year_id = $exam->eduSemestrSubject->eduSemestr->edu_year_id;
                    //     $ExamStudent->student_id = $student_id;
                    //     $ExamStudent->lang_id = $langId;
                    //     $ExamStudent->password = _random_string('numeric', 4);
                    //     // $ExamStudent->attempt = isset($ExamStudentHas) ? $ExamStudentHas->attempt + 1 : 1;
                    //     $ExamStudent->status = ExamStudent::STATUS_INACTIVE;
                    //     $ExamStudent->save(false);
                    // }

                    /** Student generate Password and create ExamStudent begin */

                    $eduPlan_id = $exam->eduSemestrSubject->eduSemestr->edu_plan_id;

                    $studentsonThisEduPlan = Student::find()
                        ->where(['edu_plan_id' => $eduPlan_id])
                        ->all();

                    $examPassword = _random_string('numeric', 6);

                    foreach ($studentsonThisEduPlan as $studentsonThisEduPlanOne) {
                        $student_id = $studentsonThisEduPlanOne->id;
                        $langId = $studentsonThisEduPlanOne->edu_lang_id;

                        $ExamStudentHas = ExamStudent::find()->where([
                            'exam_id' => $examId,
                            'student_id' => $student_id,
                        ])
                            ->orderBy('id desc')
                            ->one();

                        if ($exam->category == self::CATEGORY_INTENSIV) {
                            $oldExamStudentHas = ExamStudent::find()->where([
                                'exam_id' => $exam->old_exam_id,
                                'student_id' => $student_id,
                            ])
                                ->orderBy('id desc')
                                ->one();
                            if ($oldExamStudentHas) {

                                // dd($oldExamStudentHas->allBall);
                                if (($oldExamStudentHas->allBall + $oldExamStudentHas->in_ball)  < 56) {
                                    if (isset($ExamStudentHas)) {
                                        $ExamStudent = $ExamStudentHas;
                                    } else {
                                        $ExamStudent = new ExamStudent();
                                    }
                                    $ExamStudent->exam_id = $examId;
                                    $ExamStudent->edu_year_id = $exam->eduSemestrSubject->eduSemestr->edu_year_id;
                                    // $ExamStudent->edu_year_id = $exam->eduSemestrSubject->eduSemestr->edu_year_id;
                                    $ExamStudent->student_id = $student_id;
                                    $ExamStudent->lang_id = $langId;
                                    // $ExamStudent->password = _random_string('numeric', 4);
                                    $ExamStudent->password = $examPassword;
                                    // $ExamStudent->attempt = isset($ExamStudentHas) ? $ExamStudentHas->attempt + 1 : 1;
                                    // $ExamStudent->status = ExamStudent::STATUS_INACTIVE;
                                    $ExamStudent->save(false);
                                }
                            }
                        } else {
                            if (isset($ExamStudentHas)) {
                                $ExamStudent = $ExamStudentHas;
                            } else {
                                $ExamStudent = new ExamStudent();
                            }

                            // dd("sdsd");
                            $ExamStudent->exam_id = $examId;
                            $ExamStudent->edu_year_id = $exam->eduSemestrSubject->eduSemestr->edu_year_id;
                            // $ExamStudent->edu_year_id = $exam->eduSemestrSubject->eduSemestr->edu_year_id;
                            $ExamStudent->student_id = $student_id;
                            $ExamStudent->lang_id = $langId;
                            // $ExamStudent->password = _random_string('numeric', 4);
                            $ExamStudent->password = $examPassword;
                            // $ExamStudent->attempt = isset($ExamStudentHas) ? $ExamStudentHas->attempt + 1 : 1;
                            // $ExamStudent->status = ExamStudent::STATUS_INACTIVE;
                            $ExamStudent->save(false);
                        }
                    }
                    /** Student generate Password and create ExamStudent end */

                    ////
                } else {
                    $errors[] = _e("This subject does not belongs to this smester");
                }
            } else {
                $errors[] = _e("Exam not found");
            }
        } else {
            $errors[] = _e("Exam Id is required");
        }
        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $transaction->commit();
            return true;
        }
    }

    public static function generatePasswords($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $examId = isset($post['exam_id']) ?  $post['exam_id'] : null;

        if (isset($examId)) {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                $eduSemestrSubject = EduSemestrSubject::findOne($exam->edu_semestr_subject_id);
                if (isset($eduSemestrSubject)) {

                    /** Student generate Password and create ExamStudent begin */

                    $examPassword = _random_string('numeric', 6);

                    $exam->password = $examPassword;
                    /** Student generate Password and create ExamStudent end */

                    if ($exam->save(false)) {

                        $timeedtostr = strtotime($exam->start);
                        $tgmessage = date('l', $timeedtostr) . "(" . date('d-m-Y', $timeedtostr) . " )" . "\n"
                            . $exam->translate->name . ": " . "<b>" . $examPassword . "</b>" . "\n" . $exam->supervisorNames;

                        Yii::$app->telegram->sendMessage([
                            'chat_id' => -615678452, //test
                            'chat_id' => -1001834226507,
                            'text'  => $tgmessage,
                            'parse_mode' => 'html'
                        ]);

                        $transaction->commit();
                        return true;
                    } else {
                        $errors[] = _e("Error on saving password");
                    }
                    ////
                } else {
                    $errors[] = _e("This subject does not belongs to this smester");
                }
            } else {
                $errors[] = _e("Exam not found");
            }
        } else {
            $errors[] = _e("Exam Id is required");
        }



        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $transaction->commit();
            return true;
        }
    }

    public static function getPasswords($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $data = [];
        $data['is_ok'] = false;
        $examId = $post['exam_id'];

        if (isset($examId)) {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                $examStudents = ExamStudent::find()
                    ->where(['exam_id' => $examId])
                    ->leftJoin("student std", "std.id = exam_student.student_id")
                    ->orderBy('std.direction_id')
                    ->all();

                foreach ($examStudents as $examStudentOne) {
                    $oneStd = [];
                    $oneStd['full_name'] = Profile::getFullname($examStudentOne->student->profile);
                    $oneStd['direction'] = $examStudentOne->student->direction->translate->name ?? null;
                    // $oneStd['password'] = $examStudentOne->password;
                    $data['students'][] = $oneStd;
                }
                $eduSemestrSubject = EduSemestrSubject::findOne($exam->edu_semestr_subject_id);
                if (isset($eduSemestrSubject)) {
                    $info = [];
                    $info['subject'] = $eduSemestrSubject->subject->translate->name;
                    $info['start'] = $exam->start;
                    $info['finish'] = $exam->finish;
                    $info['password'] = $exam->password;
                    $info['exam_type'] = $exam->examType->translate->name;

                    $data['info'] = $info;
                } else {
                    $errors[] = _e("This subject does not belongs to this smester");
                }
                $data['is_ok'] = true;

                return $data;
            } else {
                $errors[] = _e("Exam not found");
            }
        } else {
            $errors[] = _e("Exam Id is required");
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $transaction->commit();
            return $data;
        }
    }

    public static function createItem($model, $post)
    {

        // dd($model->oldExam->question_count_by_type_with_ball);
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->start > $model->finish) {
            $errors[] = _e("Start of exam can not be greater than finish");
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }
        $model->type = $model->eduSemestr->type ?? 1;
        $model->edu_year_id = $model->eduSemestrSubject->eduSemestr->edu_year_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->edu_plan_id = $model->eduSemestrSubject->eduSemestr->edu_plan_id;

        /** question_count_by_type_with_ball */
        if (isset($post['question_count_by_type_with_ball'])) {
            $post['question_count_by_type_with_ball'] = str_replace("'", "", $post['question_count_by_type_with_ball']);
            if (!isJsonMK($post['question_count_by_type_with_ball'])) {
                $errors['question_count_by_type_with_ball'] = [_e('Must be Json')];
            }

            $all_max_ball = 0;
            foreach (((array)json_decode($post['question_count_by_type_with_ball'])) as $questionTypeId => $questionTypeCountWithBall) {

                $all_max_ball += ($questionTypeCountWithBall->count ?? 0) * ($questionTypeCountWithBall->ball ?? 0);

                $q = QuestionType::findOne($questionTypeId);
                if (!($q)) {
                    $errors[] = _e("Question Type Id (" . $questionTypeId . ") not found");
                }
            }

            if ($all_max_ball != $model->max_ball) {
                $errors[] = _e("Max ball(" . $model->max_ball . ") can not be smaller than sum(" . $all_max_ball . ") of each question");
            }
            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->question_count_by_type_with_ball = json_encode(((array)json_decode($post['question_count_by_type_with_ball'])));
        }

        if ($model->old_exam_id && $model->category == self::CATEGORY_INTENSIV) {
            $model->question_count_by_type_with_ball = $model->oldExam->question_count_by_type_with_ball;
        }


        /** question_count_by_type_with_ball */

        /*  if (isset($post['question_count_by_type'])) {
            $post['question_count_by_type'] = str_replace("'", "", $post['question_count_by_type']);
            if (!isJsonMK($post['question_count_by_type'])) {
                $errors['question_count_by_type'] = [_e('Must be Json')];
            }

            foreach (array_unique((array)json_decode($post['question_count_by_type'])) as $questionTypeId => $questionTypeCount) {

                $q = QuestionType::findOne($questionTypeId);
                if (!(isset($q))) {
                    $errors[] = _e("Question Type Id (" . $questionTypeId . ") not found");
                }
            }

            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->question_count_by_type = json_encode(array_unique((array)json_decode($post['question_count_by_type'])));
        } */

        $has_error = Translate::checkingAll($post);
        // dd($model->errors);

        if ($has_error['status']) {

            if (!($model->validate())) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }

                $model->faculty_id = $model->eduSemestrSubject->eduSemestr->eduPlan->faculty_id;
                $model->direction_id = $model->eduSemestrSubject->eduSemestr->eduPlan->direction_id;
                // $model->type = $model->eduSemestrSubject->eduSemestr->type ?? 1;
                $model->type = 1;

                $model->update();
                $transaction->commit();
                return true;
            } else {

                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->type = $model->eduSemestr->type ?? 1;
        $model->edu_year_id = $model->eduSemestrSubject->eduSemestr->edu_year_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        // if ($model->status == Exam::STATUS_ANNOUNCED) {
        //     $errors[] = _e('Exam already Announced');
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        /** question_count_by_type_with_ball */
        if (isset($post['question_count_by_type_with_ball'])) {
            $post['question_count_by_type_with_ball'] = str_replace("'", "", $post['question_count_by_type_with_ball']);
            if (!isJsonMK($post['question_count_by_type_with_ball'])) {
                $errors['question_count_by_type_with_ball'] = [_e('Must be Json')];
            }

            $all_max_ball = 0;
            foreach (((array)json_decode($post['question_count_by_type_with_ball'])) as $questionTypeId => $questionTypeCountWithBall) {

                $all_max_ball += ($questionTypeCountWithBall->count ?? 0) * ($questionTypeCountWithBall->ball ?? 0);

                $q = QuestionType::findOne($questionTypeId);
                if (!($q)) {
                    $errors[] = _e("Question Type Id (" . $questionTypeId . ") not found");
                }
            }

            if ($all_max_ball != $model->max_ball) {
                $errors[] = _e("Max ball(" . $model->max_ball . ") can not be smaller than sum(" . $all_max_ball . ") of each question");
            }
            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->question_count_by_type_with_ball = json_encode(((array)json_decode($post['question_count_by_type_with_ball'])));
        }
        /** question_count_by_type_with_ball */


        /* if (isset($post['question_count_by_type'])) {
            $post['question_count_by_type'] = str_replace("'", "", $post['question_count_by_type']);
            if (!isJsonMK($post['question_count_by_type'])) {
                $errors['question_count_by_type'] = [_e('Must be Json')];
            }

            foreach (array_unique((array)json_decode($post['question_count_by_type'])) as $questionTypeId => $questionTypeCount) {

                $q = QuestionType::findOne($questionTypeId);
                if (!($q)) {
                    $errors[] = _e("Question Type Id (" . $questionTypeId . ") not found");
                }
            }

            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->question_count_by_type = json_encode(array_unique((array)json_decode($post['question_count_by_type'])));
        } */

        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {

            $model->type = $model->eduSemestrSubject->eduSemestr->type ?? 1;

            if ($model->save()) {
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function distribution($exam)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $exam->status = Exam::STATUS_DISTRIBUTED;
        if (is_null($exam->checking_at)) {
            $exam->checking_at = date('Y-m-d H:i:s');
            $exam->checking_until = date('Y-m-d H:i:s', strtotime('+19 days'));
        }

        if ($exam->save()) {

            $examSmetas = ExamSemeta::findAll(['exam_id' => $exam->id]);

            foreach ($examSmetas as $examSmetaOne) {

                $examStudentCount = ExamStudent::find()
                    ->where([
                        'teacher_access_id' => $examSmetaOne->teacher_access_id,
                        // 'exam_semeta_id' => $examSmetaOne->id,
                        'exam_id' => $exam->id,
                        'lang_id' => $examSmetaOne->lang_id,
                    ])->count();


                // dd($examStudentCount);

                $examStudentMustDist = $examSmetaOne->count - $examStudentCount;
                if ($examStudentMustDist > 0) {

                    $examStudent = ExamStudent::find()
                        ->where([
                            'exam_id' => $exam->id,
                            'teacher_access_id' => null,
                            'lang_id' => $examSmetaOne->lang_id,
                            // 'status' => ExamStudent::STATUS_TAKED,
                        ])
                        ->orderBy(new Expression('rand()'))
                        ->limit($examStudentMustDist)
                        ->all();

                    $examSmetaOne->status = ExamSemeta::STATUS_IN_CHECKING;
                    if (!$examSmetaOne->save()) {
                        $errors[] = _('There is an error occurred while distributed');
                    }

                    foreach ($examStudent as $examStudentOne) {

                        $examStudentOne->teacher_access_id = $examSmetaOne->teacher_access_id;
                        $examStudentOne->exam_semeta_id = $examSmetaOne->id;
                        $examStudentOne->status = ExamStudent::STATUS_IN_CHECKING;

                        if (!$examStudentOne->save()) {
                            $errors[] = _('There is an error occurred while distributed');
                        }
                    }
                }
            }
        } else {
            $errors[] = _('There is an error occurred on exam');
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function appealDistribution($exam)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $exam->status_appeal = Exam::STATUS_APPEAL_DISTRIBUTED;
        if ($exam->save()) {

            $examAppealSmetas = ExamAppealSemeta::findAll(['exam_id' => $exam->id]);

            foreach ($examAppealSmetas as $examAppealSmetaOne) {

                /* $mk = 0;
                while ($mk < $examAppealSmetaOne->count) {
                    $examAppeal = ExamAppeal::find()
                        ->where([
                            'exam_id' => $exam->id,
                            'teacher_access_id' => null,
                            'lang_id' => $examAppealSmetaOne->lang_id,
                            // 'status' => ExamStudent::STATUS_TAKED,
                        ])
                        ->orderBy(new Expression('rand()'))
                        ->one();

                    if ($examAppeal) {
                        if ($examAppeal->examStudent->teacher_access_id != $examAppealSmetaOne->teacher_access_id) {
                            $examAppeal->teacher_access_id = $examAppealSmetaOne->teacher_access_id;
                            if ($examAppeal->update()) {
                                $mk++;
                            }
                        }
                    }
                } */

                $examAppealCount = ExamAppeal::find()
                    ->where([
                        'exam_id' => $exam->id,
                        'teacher_access_id' => null,
                        'lang_id' => $examAppealSmetaOne->lang_id,
                        'teacher_access_id' => $examAppealSmetaOne->teacher_access_id,

                    ])->count();

                $examAppealStudentMustDist = $examAppealSmetaOne->count - $examAppealCount;
                if ($examAppealStudentMustDist > 0) {

                    $examAppeal = ExamAppeal::find()
                        ->where([
                            'exam_id' => $exam->id,
                            'teacher_access_id' => null,
                            'lang_id' => $examAppealSmetaOne->lang_id,
                            // 'status' => ExamStudent::STATUS_TAKED,
                        ])
                        ->orderBy(new Expression('rand()'))
                        ->limit($examAppealStudentMustDist)
                        ->all();

                    $examAppealSmetaOne->status = ExamAppealSemeta::STATUS_IN_CHECKING;
                    if (!$examAppealSmetaOne->save()) {
                        $errors[] = $examAppealSmetaOne->getErrorSummary(true);
                        // $errors[] = _('There is an error occurred while saving examAppealSmetaOne!');
                    }

                    foreach ($examAppeal as $examAppealOne) {
                        $examAppealOne->teacher_access_id = $examAppealSmetaOne->teacher_access_id;
                        // $examAppealOne->exam_semeta_id = $examAppealSmetaOne->id;
                        $examAppealOne->status = ExamAppeal::STATUS_IN_CHECKING;

                        if (!$examAppealOne->save()) {
                            // $errors[] = _('There is an error occurred while saving examAppealOne!');
                            $errors[] = $examAppealOne->getErrorSummary(true);
                        }
                    }
                }
            }
        } else {
            $errors[] = _('There is an error occurred on exam');
        }
        if (count($errors) == 0) {
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Create a new instance of ExamLog model
        $examLog = new ExamLog();
        $examLog->attributes = $this->attributes;
        $examLog->exam_id = $this->id;
        // dd($examLog);
        $examLog->save(false);


        // Check if the 'password' attribute has been changed
        if ($this->isAttributeChanged('password')) {
            // Send a notification to the Telegram bot

            Yii::$app->telegram->sendPhoto([
                'chat_id' => -615678452,
                'photo' => 'https://api-digital.tsul.uz/storage/user_images/62507025840016.png',
                'caption' => $this->password
            ]);

            // $telegram = Yii::$app->telegram; // Assuming you have configured the Telegram component

            // $message = "User ID {$this->id} has changed their password.";
            // $telegram->sendMessage([
            //     'chat_id' => 'YOUR_TELEGRAM_CHAT_ID', // Replace with the actual chat ID
            //     'text' => $message,
            // ]);
        }
    }

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
            self::STATUS_FINISHED => _e('STATUS_FINISHED'),
            self::STATUS_DISTRIBUTED => _e('STATUS_DISTRIBUTED'),
            self::STATUS_ANNOUNCED => _e('STATUS_ANNOUNCED'),
            self::STATUS_CHEKLANGAN => _e('STATUS_CHEKLANGAN'),

        ];
    }
}
