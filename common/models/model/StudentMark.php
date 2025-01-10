<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "building".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Room[] $rooms
 */
class StudentMark extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STD_NOT_RATED = 1;

    const STD_RATED = 2;

    const STD_VEDEMOST_ONE = 1;
    const STD_VEDEMOST_TWO = 2;
    const STD_VEDEMOST_THREE = 3;

    const EXAM_MIN_BALL = 18;
    const ALL_MIN_BALL = 42;
    const ALL_BALL = 60;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_mark';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'edu_semestr_exams_type_id',
                    'exam_type_id',
                    'group_id',
                    'student_id',
                    'student_user_id',
                    'max_ball',
                    'edu_semestr_subject_id',
                    'subject_id',
                ], 'required'
            ],
            [
                [
                    'exam_type_id',
                    'edu_semestr_exams_type_id',
                    'student_semestr_subject_vedomst_id',
                    'type',
                    'attend',
                    'passed',
                    'vedomst',

                    'group_id',
                    'student_id',
                    'student_user_id',
                    'edu_semestr_subject_id',
                    'edu_plan_id',
                    'subject_id',
                    'edu_semestr_id',
                    'faculty_id',
                    'direction_id',
                    'semestr_id',
                    'course_id',

                    'max_ball',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['ball'], 'safe'],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['edu_semestr_exams_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrExamsType::className(), 'targetAttribute' => ['edu_semestr_exams_type_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['student_semestr_subject_vedomst_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentSemestrSubjectVedomst::className(), 'targetAttribute' => ['student_semestr_subject_vedomst_id' => 'id']],

            ['ball' , 'validateBall'],
            ['student' , 'validateStudentGroup'],

        ];
    }

    public function validateBall($attribute, $params)
    {
        if ($this->ball > $this->max_ball) {
            $this->addError($attribute, _e('The student grade must not be higher than the maximum score!'));
        }
    }

    public function validateStudentGroup($attribute, $params)
    {
        $student = StudentGroup::findOne([
            'student_id' => $this->student_id,
            'edu_semestr_id' => $this->edu_semestr_id,
            'is_deleted' => 0
        ]);
        if ($student->group_id != $this->group_id) {
            $this->addError($attribute, _e('Error in student group information!'));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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
            'edu_semestr_exams_type_id',
            'exam_type_id',
            'type',
            'group_id',
            'student_id',
            'edu_semestr_subject_id',
            'student_semestr_subject_vedomst_id',
            'edu_plan_id',
            'subject_id',
            'edu_semestr_id',
            'faculty_id',
            'direction_id',
            'semestr_id',
            'course_id',
            'ball',
            'vedomst',
            'attend',
            'passed',
            'max_ball',
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
            'attendStatus',
            'subject',
            'examType',
            'examControl',
            'examControlStudent',
            'eduSemestrSubject',
            'vedomsControlBall',
            'group',
            'student',
            'studentUser',
            'studentVedomst',
            'finalExamTest',
            'percent25',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }

    public function getSubject()
    {
        return $this->hasOne(Subject ::className(), ['id' => 'subject_id']);
    }

    public function getFinalExamTest()
    {
        return $this->hasMany(FinalExamTest ::className(), ['student_mark_id' => 'id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student ::className(), ['id' => 'student_id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject ::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group ::className(), ['id' => 'group_id']);
    }

    public function getStudentUser()
    {
        return $this->hasOne(User ::className(), ['id' => 'student_user_id']);
    }

    public function getExamControl()
    {
        return $this->hasOne(ExamControl ::className(), ['id' => 'exam_control_id']);
    }

    public function getExamControlStudent()
    {
        return $this->hasOne(ExamControlStudent ::className(), ['id' => 'exam_control_student_id']);
    }

    public function getAttendStatus()
    {
        $subject = $this->eduSemestrSubject;
        $status = 1;
        $subjectHour = 0;
        $sababliNb = 0;
        $allNb = 0;

        if ($subject->type == 0) {
            $status = 0;
            $subjectHour = $subject->allHour / 2;
            $timeTables = TimetableAttend::find()
                ->where([
                    'student_id' => $this->student_id,
                    'subject_id' => $subject->subject_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();

            $allNb = count($timeTables);

            if ($allNb > 0) {
                foreach ($timeTables as $timeTable) {
                    if ($timeTable->reason == 1) {
                        $sababliNb++;
                    }
                }
            }
        }

        return [
            'status' => $status,
            'subjectAllHour' => $subjectHour,
            'attendAll' => $allNb,
            'reason' => $sababliNb,
        ];
    }


    public function getPercent25()
    {
        $attend = $this->attendStatus;
        $sababsiz = $attend['attendAll'] - $attend['reason'];
        $percent25 = $attend['subjectAllHour'] * 0.25;

        return ($percent25 <= $sababsiz) ? 0 : 1;
    }

    public function getExamType()
    {
        return $this->hasOne(ExamsType ::className(), ['id' => 'exam_type_id']);
    }

    public function getStudentVedomst()
    {
        return $this->hasOne(StudentSemestrSubjectVedomst ::className(), ['id' => 'student_semestr_subject_vedomst_id'])->where(['is_deleted' => 0]);
    }

    public function getVedomsControlBall()
    {
        if ($this->exam_type_id == 3) {
            return StudentMark::find()
                ->where([
                    'student_semestr_subject_vedomst_id' => $this->student_semestr_subject_vedomst_id,
                    'is_deleted' => 0
                ])
                ->andWhere(['not in' , 'exam_type_id' , 3])
                ->all();
        }
    }

    public static function createItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['edu_semestr_exams_type_id'])) {
            $errors[] = ['edu_semestr_exams_type_id' => _e('Edu Semestr Exams Type required!')];
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $examType = EduSemestrExamsType::findOne([
                'id' => $post['edu_semestr_exams_type_id'],
                'status' => 1,
                'is_deleted' => 0,
            ]);
            if (!isset($examType)) {
                $errors[] = ['edu_semestr_exams_type_id' => _e('Edu Semestr Exams Type not found')];
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            $eduSemestr = EduSemestr::findOne($examType->eduSemestrSubject->edu_semestr_id);
            if (!isset($eduSemestr)) {
                $errors[] = ['edu_semestr_id' => _e('Edu Semestr ID not found')];
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $post['student_ids'] = str_replace("'", "", $post['student_ids']);
        $students = json_decode(str_replace("'", "", $post['student_ids']));

        if (isset($students)) {
            foreach ($students as $group => $studentIds) {
                foreach ($studentIds as $studentId => $studentVedomst) {

                    $finalExam = FinalExam::findOne([
                        'edu_semestr_subject_id' => $examType->edu_semestr_subject_id,
                        'edu_semestr_exams_type_id' => $examType->id,
                        'vedomst' => $studentVedomst,
                        'is_deleted' => 0,
                    ]);
                    if ($finalExam) {
                        if ($finalExam->status != 1) {
                            $errors[] = _e('The final control over vedomst is confirmed.');
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                    }

                    if ($studentVedomst != 0) {
                        $validStudentMark = StudentMark::findOne([
                            'group_id' => $group,
                            'edu_semestr_exams_type_id' => $examType->id,
                            'student_id' => $studentId,
                            'passed' => null,
                            'is_deleted' => 0
                        ]);
                        if ($validStudentMark) {
                            $errors[] = _e('XATOLIK!!!');
                        } else {
                            $isStudentMark = StudentMark::find()
                                ->where([
                                    'group_id' => $group,
                                    'edu_semestr_exams_type_id' => $examType->id,
                                    'student_id' => $studentId,
                                    'is_deleted' => 0
                                ])
                                ->orderBy('vedomst desc')
                                ->one();
                            $t = false;
                            if ($isStudentMark) {
                                if ($isStudentMark->vedomst > $studentVedomst) {
                                    $errors[] = _e('This student will not be included in this transcript.');
                                } else {
                                    $t = true;
                                }
                            } else {
                                $t = true;
                            }

                            if ($t) {
                                $studentMark = StudentMark::findOne([
                                    'group_id' => $group,
                                    'edu_semestr_exams_type_id' => $examType->id,
                                    'student_id' => $studentId,
                                    'vedomst' => $studentVedomst,
                                    'is_deleted' => 0
                                ]);
                                if ($studentMark == null) {
                                    $model = new StudentMark();
                                    $model->vedomst = $studentVedomst;
                                    $model->group_id = $group;
                                    $model->student_id = $studentId;
                                    $model->student_user_id = $model->student->user_id;
                                    $model->ball = 0;
                                    $model->edu_semestr_subject_id = $examType->edu_semestr_subject_id;
                                    $model->edu_semestr_exams_type_id = $examType->id;
                                    $model->subject_id = $examType->eduSemestrSubject->subject_id;
                                    $model->max_ball = $examType->max_ball;
                                    $model->exam_type_id = $examType->exams_type_id;
                                    $model->edu_semestr_id = $eduSemestr->id;
                                    $model->edu_plan_id = $eduSemestr->edu_plan_id;
                                    $model->faculty_id = $eduSemestr->faculty_id;
                                    $model->direction_id = $eduSemestr->direction_id;
                                    $model->semestr_id = $eduSemestr->semestr_id;
                                    $model->course_id = $eduSemestr->course_id;
                                    $model->status = self::STD_NOT_RATED;
                                    if (!$model->validate()) {
                                        $errors[] = $model->errors;
                                        $transaction->rollBack();
                                        return simplify_errors($errors);
                                    }
                                    $model->save(false);
                                } else {
                                    $errors[] = ['student id = ' . $studentMark->student_id , _e("You cannot change the information.")];
                                }

                            }
                        }
                    }

                }
            }
        } else {
            $errors[] = ['student_ids' => _e('Students ID not found')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $post['student_ids'] = str_replace("'", "", $post['student_ids']);
        $marks = json_decode(str_replace("'", "", $post['student_ids']));
        if (isset($marks)) {
            foreach ($marks as $markId => $markBall) {
                $studentMark = StudentMark::findOne($markId);
                if (!$studentMark) {
                    $errors[] = $markId. _e(' Mark ID not found.');
                } else {
                    if ($studentMark->exam_type_id != 3 && $studentMark->faculty_id != 6) {
                        $studentMark->ball = $markBall;
                        if (!$studentMark->validate()) {
                            $errors[] = $studentMark->errors;
                        } else {
                            $studentMark->update(false);

                            StudentMark::markHistory($studentMark);

                            $finalExamGroup = FinalExamGroup::findOne([
                                'group_id' => $studentMark->student->group_id,
                                'edu_semestr_subject_id' => $studentMark->edu_semestr_subject_id,
                                'vedomst' => $studentMark->studentVedomst->vedomst,
                                'is_deleted' => 0
                            ]);
                            if ($finalExamGroup) {
                                $finalExam = $finalExamGroup->finalExam;
                                if ($finalExam->status > 2 && $finalExam->is_deleted == 0) {
                                    $errors[] = _e('This form is closed.');
                                }
                            }
                        }
                    } else {
                        $errors[] = _e('Cannot be final graded.');
                    }
                }
            }
        } else {
            $errors[] = ['student_ids' => _e('Students ID not found')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function examItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $post['student_ids'] = str_replace("'", "", $post['student_ids']);
        $marks = json_decode(str_replace("'", "", $post['student_ids']));
        if (isset($marks)) {
            foreach ($marks as $markId => $markBall) {
                $studentMark = StudentMark::findOne($markId);
                if (!$studentMark) {
                    $errors[] = $markId. _e(' Mark ID not found.');
                } else {
                    $studentMark->ball = $markBall;
                    $studentMark->status = 2;
                    $studentMark->attend = 1;
                    if (!$studentMark->validate()) {
                        $errors[] = $studentMark->errors;
                    } else {
                        $studentMark->update(false);
                        StudentMark::markHistory($studentMark);
                        if ($studentMark->exam_type_id == 3) {
                            $marks = StudentMark::find()
                                ->where([
                                    'student_semestr_subject_vedomst_id' => $studentMark->student_semestr_subject_vedomst_id,
                                    'is_deleted' => 0
                                ])->all();
                            $ball = 0;
                            if (count($marks) > 0) {
                                foreach ($marks as $mark) {
                                    $ball = $ball + (int)$mark->ball;
                                }
                            }
                            $studentVedomst = $studentMark->studentVedomst;
                            $studentVedomst->ball = (int)$ball;
                            $studentVedomst->passed = 1;
                            $studentVedomst->update(false);
                            $studentSemestrSubject = $studentVedomst->studentSemestrSubject;
                            $studentSemestrSubject->all_ball = $studentVedomst->ball;
                            $studentSemestrSubject->closed = 1;
                            $studentSemestrSubject->update(false);
                        }
                    }
                }
            }
        } else {
            $errors[] = ['student_ids' => _e('Students ID not found')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function finalExam($post, $model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['students'])) {
            $errors[] = _e('Student not found.');
        }
        $students = json_decode($post['students']);
        if (isset($students)) {
            foreach ($students as $studentMark => $ball) {
                $mark = StudentMark::findOne($studentMark);
                if ($mark != null) {
                    if ($model->vedomst == $mark->studentVedomst->vedomst && $mark->edu_semestr_subject_id == $model->edu_semestr_subject_id) {
                        if ($ball[0] == 1) {
                            $mark->ball = $ball[1];
                            $mark->status = 2;
                            $mark->attend = 1;
                            if ($mark->validate()) {
                                $mark->update(false);
                                StudentMark::markHistory($mark);
                            } else {
                                $errors[] = $mark->errors;
                            }
                        } else {
                            $mark->status = 2;
                            $mark->ball = 0;
                            $mark->attend = 0;
                            $mark->update(false);
                        }
                    } else {
                        $errors[] = $studentMark. _e(' This student is not included in this registr.');
                    }
                } else {
                    $errors[] = $studentMark. _e(' Student Mark Id not found.');
                }
            }
        } else {
            $errors[] = _e(' Students data not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function finalExam12121($post, $model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['students'])) {
            $errors[] = _e('Student not found.');
        }

        $students = json_decode($post['students']);

        if (isset($students)) {
            foreach ($students as $studentMark => $ball) {
                $mark = StudentMark::findOne($studentMark);
                if ($mark != null) {
                    if ($model->vedomst == $mark->vedomst && $mark->edu_semestr_subject_id == $model->edu_semestr_subject_id) {
                        if ($ball[0] == 1) {
                            $mark->ball = $ball[1];
                            $mark->status = 2;
                            if ($mark->validate()) {
                                $mark->update(false);

                                $stdVedomst = StudentMarkVedomst::findOne([
                                    'student_mark_id' => $mark->id,
                                    'vedomst' => $mark->vedomst,
                                    'is_deleted' => 0
                                ]);

                                if ($stdVedomst) {
                                    $stdVedomst->ball = $ball[1];
                                    $stdVedomst->passed = 1;
                                    $stdVedomst->attend = 1;
                                    $query = StudentMark::find()
                                        ->where([
                                            'edu_semestr_subject_id' => $mark->edu_semestr_subject_id,
                                            'student_id' => $mark->student_id,
                                            'is_deleted' => 0
                                        ])
                                        ->andWhere(['<>' , 'exam_type_id' , 3])
                                        ->all();

                                    $bal = 0;
                                    if (count($query) > 0) {
                                        foreach ($query as $item) {
                                            $bal = $bal + $item->ball;
                                        }
                                    }

                                    $all_ball = $bal + $mark->ball;
                                    if ($mark->ball < self::EXAM_MIN_BALL) {
                                        $mark->ball = 0;
                                        $mark->status = 1;
                                        $stdVedomst->passed = 2;
                                    }
                                    if ($bal < self::ALL_MIN_BALL) {
                                        $mark->ball = 0;
                                        $mark->status = 1;
                                        $stdVedomst->passed = 2;
                                    }
                                    if ($all_ball < self::ALL_BALL) {
                                        $mark->ball = 0;
                                        $mark->status = 1;
                                        $stdVedomst->passed = 2;
                                    }
                                    $mark->update(false);
                                    $stdVedomst->update(false);

                                } else {
                                    $errors[] = _e('You cannot grade a student.');
                                }

                            } else {
                                $errors[] = $mark->errors;
                            }
                        } else {
                            $stdVedomst = StudentMarkVedomst::findOne([
                                'student_mark_id' => $mark->id,
                                'vedomst' => $mark->vedomst,
                                'is_deleted' => 0
                            ]);
                            if ($stdVedomst) {
                                $stdVedomst->attend = $ball[0];
                                $stdVedomst->passed = 2;
                                $stdVedomst->ball = 0;
                                $stdVedomst->save(false);
                            } else {
                                $errors[] = _e('You cannot grade a student.');
                            }
                        }
                    } else {
                        $errors[] = $studentMark. _e(' This student is not included in this registr.');
                    }
                } else {
                    $errors[] = $studentMark. _e(' Student Mark Id not found.');
                }
            }
        } else {
            $errors[] = _e(' Students data not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItem12121($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['edu_semestr_exams_type_id'])) {
            $errors[] = ['edu_semestr_exams_type_id' => _e('Edu Semestr Exams Type required!')];
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $examType = EduSemestrExamsType::findOne([
                'id' => $post['edu_semestr_exams_type_id'],
                'status' => 1,
                'is_deleted' => 0,
            ]);
            if (!isset($examType)) {
                $errors[] = ['edu_semestr_exams_type_id' => _e('Edu Semestr Exams Type not found')];
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            $eduSemestr = EduSemestr::findOne($examType->eduSemestrSubject->edu_semestr_id);
            if (!isset($eduSemestr)) {
                $errors[] = ['edu_semestr_id' => _e('Edu Semestr ID not found')];
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $post['student_ids'] = str_replace("'", "", $post['student_ids']);
        $students = json_decode(str_replace("'", "", $post['student_ids']));

        if (isset($students)) {
            foreach ($students as $group => $studentIds) {
                foreach ($studentIds as $studentId => $studentVedomst) {

                    $validStudentMark = StudentMark::findOne([
                        'group_id' => $group,
                        'edu_semestr_exams_type_id' => $examType->id,
                        'student_id' => $studentId,
                        'passed' => null,
                        'is_deleted' => 0
                    ]);
                    if ($validStudentMark) {
                        $errors[] = _e('XATOLIK!!!');
                    } else {
                        $isStudentMark = StudentMark::find()
                            ->where([
                                'group_id' => $group,
                                'edu_semestr_exams_type_id' => $examType->id,
                                'student_id' => $studentId,
                                'is_deleted' => 0
                            ])
                            ->orderBy('vedomst desc')
                            ->one();
                        $t = false;
                        if ($isStudentMark) {
                            if ($isStudentMark->vedomst > $studentVedomst) {
                                $errors[] = _e('This student will not be included in this transcript.');
                            } else {
                                $t = true;
                            }
                        } else {
                            $t = true;
                        }

                        if ($t) {

                            $studentMark = StudentMark::findOne([
                                'group_id' => $group,
                                'edu_semestr_exams_type_id' => $examType->id,
                                'student_id' => $studentId,
                                'vedomst' => $studentVedomst,
                                'is_deleted' => 0
                            ]);

                            if ($studentMark == null) {
                                $model = new StudentMark();
                                $model->vedomst = $studentVedomst;
                                $model->group_id = $group;
                                $model->student_id = $studentId;
                                $model->student_user_id = $model->student->user_id;
                                $model->ball = 0;
                                $model->edu_semestr_subject_id = $examType->edu_semestr_subject_id;
                                $model->edu_semestr_exams_type_id = $examType->id;
                                $model->subject_id = $examType->eduSemestrSubject->subject_id;
                                $model->max_ball = $examType->max_ball;
                                $model->exam_type_id = $examType->exams_type_id;
                                $model->edu_semestr_id = $eduSemestr->id;
                                $model->edu_plan_id = $eduSemestr->edu_plan_id;
                                $model->faculty_id = $eduSemestr->faculty_id;
                                $model->direction_id = $eduSemestr->direction_id;
                                $model->semestr_id = $eduSemestr->semestr_id;
                                $model->course_id = $eduSemestr->course_id;
                                $model->status = self::STD_NOT_RATED;
                                if (!$model->validate()) {
                                    $errors[] = $model->errors;
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }
                                $model->save(false);
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($i == $studentVedomst) {
                                        $type = 0;
                                    } else {
                                        $type = 1;
                                    }
                                    $new = new StudentMarkVedomst();
                                    $new->student_mark_id = $model->id;
                                    $new->edu_semestr_exams_type_id = $model->edu_semestr_exams_type_id;
                                    $new->group_id = $model->group_id;
                                    $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                    $new->student_id = $model->student_id;
                                    $new->student_user_id = $model->student_user_id;
                                    $new->vedomst = $i;
                                    $new->type = $type;
                                    $new->save(false);
                                }
                            } else {
                                if ($studentMark->status == self::STD_RATED) {
                                    $errors[] = ['student id = ' . $studentMark->student_id , _e("This student is graded.")];
                                } else {
                                    $studentMarkHistory = new StudentMarkHistory();
                                    $studentMarkHistory->edu_semestr_exams_type_id = $studentMark->edu_semestr_exams_type_id;
                                    $studentMarkHistory->exam_type_id = $studentMark->exam_type_id;
                                    $studentMarkHistory->group_id = $studentMark->group_id;
                                    $studentMarkHistory->student_id = $studentMark->student_id;
                                    $studentMarkHistory->student_user_id = $studentMark->student_user_id;
                                    $studentMarkHistory->ball = $studentMark->ball;
                                    $studentMarkHistory->max_ball = $studentMark->max_ball;
                                    $studentMarkHistory->edu_semestr_subject_id = $studentMark->edu_semestr_subject_id;
                                    $studentMarkHistory->subject_id = $studentMark->subject_id;
                                    $studentMarkHistory->edu_plan_id = $studentMark->edu_plan_id;
                                    $studentMarkHistory->edu_semestr_id = $studentMark->edu_semestr_id;
                                    $studentMarkHistory->faculty_id = $studentMark->faculty_id;
                                    $studentMarkHistory->direction_id = $studentMark->direction_id;
                                    $studentMarkHistory->semestr_id = $studentMark->semestr_id;
                                    $studentMarkHistory->course_id = $studentMark->course_id;
                                    $studentMarkHistory->type = $studentMark->type;
                                    $studentMarkHistory->exam_id = $studentMark->exam_id;
                                    $studentMarkHistory->exam_student_id = $studentMark->exam_student_id;
                                    $studentMarkHistory->exam_control_id = $studentMark->exam_control_id;
                                    $studentMarkHistory->exam_control_student_id = $studentMark->exam_control_student_id;
                                    $studentMarkHistory->update_date = time();
                                    $studentMarkHistory->vedomst = $studentMark->vedomst;
                                    $studentMarkHistory->status = $studentMark->status;
                                    $studentMarkHistory->save(false);

                                    $oldVedomst = $studentMark->vedomst;
                                    $studentMark->vedomst = $studentVedomst;
                                    $studentMark->update(false);

                                    $finalExam = FinalExam::findOne([
                                        'vedomst' => $studentMark->vedomst,
                                        'edu_semestr_exams_type_id' => $studentMark->edu_semestr_exams_type_id,
                                        'is_deleted' => 0
                                    ]);
                                    if ($finalExam) {
                                        if ($finalExam->status > 1) {
                                            $errors[] = _e("The exam is over.");
                                        }
                                    } else {

                                        $stdVedomst = StudentMarkVedomst::findOne([
                                            'student_mark_id' => $studentMark->id,
                                            'passed' => 1,
                                            'is_deleted' => 0
                                        ]);

                                        if ($stdVedomst) {
                                            $errors[] = _e("You cannot change the information!");
                                        }  else {
                                            $isPassed = StudentMarkVedomst::findOne([
                                                'vedmost' => $oldVedomst,
                                                'student_mark_id' => $studentMark->id,
                                                'is_deleted' => 0
                                            ]);
                                            if ($isPassed->passed = 0) {
                                                $isPassed->type = 0;
                                            }
                                            $query = StudentMarkVedomst::findOne([
                                                'vedmost' => $studentVedomst,
                                                'student_mark_id' => $studentMark->id,
                                                'is_deleted' => 0
                                            ]);
                                            $query->type = 1;
                                            $query->save(false);
                                        }



                                    }


                                }
                            }

                        }
                    }

                }
            }
        } else {
            $errors[] = ['student_ids' => _e('Students ID not found')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function markHistory($mark)
    {
        $new = new MarkHistory();
        $new->student_mark_id = $mark->id;
        $new->user_id = current_user_id();
        $new->ball = $mark->ball;
        $new->update_time = time();
        $new->ip = getIpMK();
        $new->save(false);
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
