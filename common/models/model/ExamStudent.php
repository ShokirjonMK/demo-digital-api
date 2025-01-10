<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;

class ExamStudent extends ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const STUDENT_DEFAULT = 0;
    const STUDENT_STARTED = 1;
    const STUDENT_FINISHED = 2;
    const STUDENT_EVALUATED = 3;

    public static function tableName()
    {
        return 'exam_student';
    }


    public function rules()
    {
        return [
            [
                ['exam_id','student_id'], 'required'
            ],
            [
                [
                    'exam_teacher_user_id',
                    'exam_id',
                    'student_id',
                    'student_user_id',
                    'group_id',
                    'course_id',
                    'semestr_id',
                    'subject_id',
                    'language_id',
                    'edu_plan_id',
                    'edu_semestr_id',
                    'edu_semestr_exams_type_id',
                    'exam_type_id',
                    'finish_time',
                    'start_time',
                    'faculty_id',
                    'direction_id',
                    'course_id',
                    'semestr_id',
                    'type',
                    'max_ball',
                    'student_ball',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            ['description' , 'safe'],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['exam_teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['exam_teacher_user_id' => 'id']],
            [['edu_semestr_exams_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrExamsType::className(), 'targetAttribute' => ['edu_semestr_exams_type_id' => 'id']],

            ['student_ball' , 'validateBall'],
            ['student_id' , 'validatePermission'],
        ];
    }

    public function validateBall($attribute, $params)
    {
        if ($this->student_ball > $this->max_ball) {
            $this->addError($attribute, _e('The student grade must not be higher than the maximum score!'));
        }
    }

    public function validatePermission($attribute, $params)
    {
        $permission = $this->exam->studentMark;
        if (!$permission) {
            $this->addError($attribute, _e('You are not allowed to enter the exam!'));
        }
    }

    public function fields()
    {
        return [
            'id',
            'exam_id',
            'group_id',
            'student_id',
            'course_id',
            'semestr_id',
            'subject_id',
            'language_id',
            'max_ball',
            'student_ball',
            'edu_plan_id',
            'exam_teacher_user_id',
            'edu_semestr_id',
            'faculty_id',
            'direction_id',
            'type',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    public function extraFields()
    {
        $extraFields =  [
            'course',
            'direction',
            'eduPlan',
            'eduSemester',
            'eduYear',
            'exam',
            'faculty',
            'language',
            'semestr',
            'student',
            'subject',
            'user',
            'fileInformation',
            'group',

            'examStudentQuestion',
            'studentTimes',
            'correctCount',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery|CourseQuery
     */

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getStudentTimes() {
        return [
            'start' => $this->start_time,
            'finish' => $this->finish_time,
            'current' => time(),
        ];
    }

    public function getFileInformation()
    {
        return [
            'extension' => ExamStudentQuestion::ANSWER_FILE_EXTENSION,
            'size' => ExamStudentQuestion::ANSWER_FILE_MAX_SIZE,
        ];
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery|DirectionQuery
     */

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[EduSemester]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */


    /**
     * Gets query for [[ExamControl]].
     *
     * @return \yii\db\ActiveQuery|ExamControlQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LanguageQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * Gets query for [[Semester]].
     *
     * @return \yii\db\ActiveQuery|SemestrQuery
     */

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
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

    public function getExamStudentQuestion()
    {
        return $this->hasMany(ExamStudentQuestion::className(), ['exam_student_id' => 'id']);
    }

    public function getCorrectCount() {
        if ($this->exam->finish_time < time()) {
            $correct = ExamStudentQuestion::find()
                ->where([
                    'exam_student_id' => $this->id,
                    'status' => 1,
                    'is_deleted' => 0,
                    'is_correct' => 1
                ])
                ->count();
            return $correct;
        }
        return null;
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

    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery|SubjectCategoryQuery
     */


    /**
     * Gets query for [[TeacherUser]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */

    public function getUser()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        $model->student_id = $model->student();
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $exam = $model->exam;
        $query = ExamStudent::findOne([
            'exam_id' => $exam->id,
            'student_id' => $model->student_id,
            'student_user_id' => $model->student->user_id,
            'is_deleted' => 0
        ]);
        if (isset($query)) {
            $errors[] = _e("The questions have already been created for you.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!($exam->start_time <= $time && $exam->finish_time >= $time)) {
            $errors[] = _e("Wait for the exam to start! Or Exam Completed.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($exam->status != Exam::STATUS_STARTED) {
            $errors[] = _e("Exam not confirmed!");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->group_id = $model->student->group_id;
        $model->type = $exam->type;
        $model->student_id = $model->student->id;
        $model->student_user_id = $model->student->user_id;
        $model->edu_plan_id = $exam->edu_plan_id;
        $model->subject_id = $exam->subject_id;
        $model->language_id = $model->group->language_id;
        $model->edu_semestr_subject_id = $exam->edu_semestr_subject_id;
        $model->exam_type_id = $exam->exam_type_id;
        $model->max_ball = $exam->max_ball;
        $model->faculty_id = $exam->faculty_id;
        $model->direction_id = $exam->direction_id;
        $model->edu_semestr_id = $exam->edu_semestr_id;
        $model->semestr_id = $exam->semestr_id;
        $model->course_id = $exam->course_id;
        $model->start_time = $time;
        $model->status = self::STUDENT_STARTED;
        $model->finish_time = strtotime('+'. $exam->duration .' minutes' , $model->start_time);
        if ($exam->finish_time < $model->finish_time) {
            $model->finish_time = $exam->finish_time;
        }

        $timeTable = TimeTable1::find()
            ->where([
                'group_id' => $model->group_id,
                'subject_id' => $model->subject_id,
                'edu_semestr_id' => $model->edu_semestr_id,
                'is_deleted' => 0
            ])
            ->count();
        if ($timeTable == 0) {
            $errors[] = ['group_id' => _e("This group does not have a lesson in this subject this semester.")];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $tests = Test::find()
                ->where([
                    'type' => $model->type,
                    'subject_id' => $model->subject_id,
                    'exam_type_id' => $model->exam_type_id,
                    'is_checked' => 1,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->orderBy(new Expression('rand()'))
                ->limit($model->exam->question_count)
                ->all();
            if (count($tests) == 0 || count($tests) < $model->exam->question_count) {
                $errors[] = ['question_count' => _e('There are not enough test questions.')];
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            foreach ($tests as $test) {
                $examStudentQuestion = new ExamStudentQuestion();
                $examStudentQuestion->exam_student_id = $model->id;
                $examStudentQuestion->exam_id = $model->exam_id;
                $examStudentQuestion->student_id = $model->student_id;
                $examStudentQuestion->student_user_id = $model->student_user_id;
                $examStudentQuestion->group_id = $model->group_id;
                $examStudentQuestion->type = $model->type;
                $examStudentQuestion->exam_test_id = $test->id;
                if ($model->type == Exam::TEST) {
                    $examStudentQuestion->options = ExamStudentQuestion::optionsArray($test->id);
                }
                if (!$examStudentQuestion->validate()) {
                    $errors[] = $examStudentQuestion->errors;
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                $examStudentQuestion->save(false);
            }
        } else {
            $errors[] = _e("Data not saved.");
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function finish($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->status == self::STUDENT_STARTED) {
            $model->status = self::STUDENT_FINISHED;
            $model->save(false);
        } else {
            $errors[] = _e("You completed first!");
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function studentRating($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->exam_teacher_user_id != current_user_id() && !isRole('admin')) {
            $errors[] = _e("You are not attached to grade this student.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->exam->status == Exam::STATUS_ALLOTMENT) {
            if ($model->status == self::STUDENT_FINISHED) {
                $questions = ExamStudentQuestion::find()
                    ->where([
                        'exam_student_id' => $model->id,
                        'is_deleted' => 0
                    ])
                    ->all();
                $ball = 0;
                if (count($questions) > 0) {
                    foreach ($questions as $question) {
                        $ball = $question->student_ball + $ball;
                    }
                }
                $model->student_ball = $ball;
                $model->status = $post['status'];
            } elseif ($model->status == self::STUDENT_EVALUATED) {
                $model->status = $post['status'];
            }  else {
                $errors[] = _e("The student did not complete the exam.");
            }
        } else {
            $errors[] = _e("You cannot grade a student at this time.");
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function studentMark($model) {
        $studentMark = StudentMark::find()
            ->where([
                'exam_type_id' => $model->exam_type_id,
                'student_id' => $model->student_id,
                'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                'subject_id' => $model->subject_id,
                'semestr_id' => $model->semestr_id,
                'course_id' => $model->course_id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->one();
        if ($studentMark != null) {
            $studentMark->is_deleted = 1;
            $studentMark->is_deleted_date = date("Y-m-d H:i:s");
            $studentMark->save(false);
        }
        $mark = new StudentMark();
        $mark->exam_type_id = $model->exam_type_id;
        $mark->type = $model->edu_semestr_exam_type_id;
        $mark->group_id = $model->group_id;
        $mark->student_id = $model->student_id;
        $mark->max_ball = $model->max_ball;
        $mark->ball = $model->student_ball;
        $mark->edu_semestr_subject_id = $model->edu_semestr_subject_id;
        $mark->subject_id = $model->subject_id;
        $mark->edu_plan_id = $model->edu_plan_id;
        $mark->edu_semestr_id = $model->edu_semestr_id;
        $mark->faculty_id = $model->faculty_id;
        $mark->direction_id = $model->direction_id;
        $mark->semestr_id = $model->semestr_id;
        $mark->course_id = $model->course_id;
        $mark->save(false);
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

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }
}
