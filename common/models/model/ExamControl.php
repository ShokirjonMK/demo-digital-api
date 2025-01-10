<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Student;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%exam_control}}".
 *
 * @property int $id
 * @property int $group_id
 * @property int|null $start_time
 * @property int|null $finish_time
 * @property float|null $max_ball
 * @property int|null $duration
 * @property string|null $question
 * @property string|null $file
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property int $edu_year_id
 * @property int $language_id
 * @property int $edu_plan_id
 * @property int $teacher_user_id
 * @property int $edu_semestr_id
 * @property int $edu_semestr_exam_type_id
 * @property int $edu_semester_subject_id
 * @property int $subject_category_id
 * @property int|null $faculty_id
 * @property int|null $direction_id
 * @property int|null $type
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property Direction $direction
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemester
 * @property EduYear $eduYear
 * @property ExamControlStudent[] $examControlStudents
 * @property Faculty $faculty
 * @property Language $language
 * @property Semestr $semester
 * @property Subject $subject
 * @property SubjectCategory $subjectCategory
 * @property TeacherAccess $teacher_access_id
 * @property User $teacherUser
 * @property TimeTable1 $timeTable
 */
class ExamControl extends \yii\db\ActiveRecord
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
    const STATUS_ANNOUNCED = 2;
    const STATUS_FINISHED = 3;

    const WRITE = 1;
    const TEST = 2;

    const LECTURE = 1;

    const appeal_time = 3 * 24 * 60 * 60; // 3 kun soat

    const UPLOADS_FOLDER = 'uploads/exam_control/question/';

    public $upload_file;

    public $questionFileMaxSize = 1024 * 1024 * 10; // 10 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_control';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'group_id',
                    'edu_semestr_subject_id',
                    'start_time',
                    'finish_time',
                    'type',
                    'duration',
                    'edu_semestr_exam_type_id',
                    'subject_category_id' ,
                    'user_id',
                ], 'required'
            ],
            [
                [
                    'start_time',
                    'finish_time',
                    'duration',
                    'course_id',
                    'semestr_id',
                    'edu_year_id',
                    'subject_id',
                    'language_id',
                    'edu_plan_id',
                    'user_id',
                    'edu_semestr_id',
                    'edu_semestr_subject_id',
                    'edu_semestr_exam_type_id',
                    'exam_type_id',
                    'subject_category_id',
                    'faculty_id',
                    'direction_id',
                    'question_count',
                    'type',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by', 'is_deleted'
                ], 'integer'
            ],
            [['max_ball'], 'number'],
            [['question'], 'string'],
            [['file'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['edu_semestr_exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrExamsType::className(), 'targetAttribute' => ['edu_semestr_exam_type_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
//            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],

            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => $this->questionFileMaxSize],
            ['start_time' , 'validateTime'],
        ];
    }

    public function validateTime($attribute, $params)
    {
        if ($this->start_time >= $this->finish_time) {
            $this->addError($attribute, _e('The finish time must be greater than the start time.'));
        }
    }


    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? $this->subject->translate->name . " | " . $this->eduSemestrExamType->examsType->translate->name . " | " .$this->eduSemestr->semestr_id . ' - sm';
            },
            'group_id',
            'start_time',
            'finish_time',
            'max_ball',
            'duration',
            'question',
            'question_count',
            'file',
            'course_id',
            'semestr_id',
            'edu_year_id',
            'edu_semestr_exam_type_id',
            'edu_semestr_subject_id',
            'exam_type_id',
            'language_id',
            'edu_plan_id',
            'user_id',
            'edu_semestr_id',
            'subject_category_id',
            'faculty_id',
            'direction_id',
            'type',

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
            'group',
            'course',
            'direction',
            'eduPlan',
            'eduSemestr',
            'eduYear',
            'faculty',
            'language',
            'semester',
            'subject',
            'subjectCategory',
            'teacherUser',
            'teacherAccess',
            'eduSemestrExamType',
            'examControlStudents',
            'examTimes',

            'user',

            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getExamTimes() {
        return [
            'start' => $this->start_time,
            'finish' => $this->finish_time,
            'current' => time(),
        ];
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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }


    /**
     * Gets query for [[ExamControlStudents]].
     *
     * @return \yii\db\ActiveQuery|ExamControlStudentQuery
     */


    public function getExamControlStudents()
    {
        if (isRole('student')) {
            return $this->hasMany(ExamControlStudent::className(), ['exam_control_id' => 'id'])
                ->onCondition(['student_id' => $this->student()]);
        }
        return $this->hasMany(ExamControlStudent::className(), ['exam_control_id' => 'id']);
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
    public function getSemester()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
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
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    /**
     * Gets query for [[TeacherAccess]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }
    /**
     * Gets query for [[TeacherUser]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTeacherUser()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_user_id']);
    }

    public function getEduSemestrExamType()
    {
        return $this->hasOne(EduSemestrExamsType::className(), ['id' => 'edu_semestr_exam_type_id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }


    /**
     * Gets query for [[TimeTable]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->edu_plan_id = $model->group->edu_plan_id;
        $model->edu_semestr_id = $model->group->activeEduSemestr->id;
        $model->edu_year_id = $model->eduSemestr->edu_year_id;
        $model->course_id = $model->eduSemestr->course_id;
        $model->semestr_id = $model->eduSemestr->semestr_id;
        $model->language_id = $model->group->language_id;
        $model->faculty_id = $model->eduPlan->faculty_id;
        $model->direction_id = $model->eduPlan->direction_id;
        $model->max_ball = $model->eduSemestrExamType->max_ball;
        $model->exam_type_id = $model->eduSemestrExamType->exams_type_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;

        // question file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $upload_FileUrl = $model->upload($model->upload_file);
            if ($upload_FileUrl) {
                $model->file = $upload_FileUrl;
            } else {
                $errors[] = $model->errors;
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
            $students = $model->group->student;
            if (count($students) > 0) {
                foreach ($students as $student) {
                    $examControlStudent = new ExamControlStudent();
                    $examControlStudent->exam_control_id = $model->id;
                    $examControlStudent->type = $model->type;
                    $examControlStudent->group_id = $model->group_id;
                    $examControlStudent->student_id = $student->id;
                    $examControlStudent->student_user_id = $student->user_id;
                    $examControlStudent->subject_id = $model->subject_id;
                    $examControlStudent->subject_category_id = $model->subject_category_id;
                    $examControlStudent->language_id = $model->language_id;
                    $examControlStudent->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                    $examControlStudent->edu_semestr_exam_type_id = $model->edu_semestr_exam_type_id;
                    $examControlStudent->max_ball = $model->max_ball;
                    $examControlStudent->student_ball = 0;
                    $examControlStudent->duration = $model->duration;
                    $examControlStudent->exam_type_id = $model->exam_type_id;
                    $examControlStudent->question_count = $model->question_count;
                    $examControlStudent->user_id  = $model->user_id;
                    $examControlStudent->faculty_id  = $model->faculty_id;
                    $examControlStudent->direction_id  = $model->direction_id;
                    $examControlStudent->edu_plan_id  = $model->edu_plan_id;
                    $examControlStudent->edu_semestr_id  = $model->edu_semestr_id;
                    $examControlStudent->edu_year_id  = $model->edu_year_id;
                    $examControlStudent->course_id  = $model->course_id;
                    $examControlStudent->semestr_id  = $model->semestr_id;
                    $examControlStudent->user_status  = 0;
                    if (!$examControlStudent->validate()) {
                        $errors[] = $examControlStudent->errors;
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    if (!$examControlStudent->save()) {
                        $errors[] = _e("Exam Control Student not saved.");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    if ($examControlStudent->type == self::TEST) {
                        $test = Test::find()
                            ->where([
                                'subject_id' => $model->subject_id,
                                'exam_type_id' => $model->exam_type_id,
                                'is_checked' => 1,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->orderBy(new Expression('rand()'))
                            ->limit($model->question_count ? $model->question_count : 0)
                            ->all();
                        if (count($test) == 0) {
                            $errors[] = ['questions' => _e('Questions not found.')];
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        } elseif (count($test) < $model->question_count) {
                            $errors[] = ['question_count' => _e('There are not enough test questions.')];
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                        foreach ($test as $value) {
                            $studentTestAnswer = new ExamTestStudentAnswer();
                            $studentTestAnswer->exam_control_student_id = $examControlStudent->id;
                            $studentTestAnswer->exam_control_id = $model->id;
                            $studentTestAnswer->subject_id = $model->subject_id;
                            $studentTestAnswer->student_id = $student->id;
                            $studentTestAnswer->user_id = $student->user_id;
                            $studentTestAnswer->is_correct = 0;
                            $studentTestAnswer->exam_test_id = $value->id;
                            $studentTestAnswer->answer_option_id = $studentTestAnswer->answerOption($value->id);
                            $studentTestAnswer->options = $studentTestAnswer->optionsArray($value->id);
                            if (!$studentTestAnswer->save()) {
                                $errors[] = $studentTestAnswer->errors;
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }

                        }
                    }
                }
            }
        } else {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

    }

    public static function updateItem($model, $type)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->edu_plan_id = $model->group->edu_plan_id;
        $model->edu_semestr_id = $model->group->activeEduSemestr->id;
        $model->edu_year_id = $model->eduSemestr->edu_year_id;
        $model->course_id = $model->eduSemestr->course_id;
        $model->semestr_id = $model->eduSemestr->semestr_id;
        $model->language_id = $model->group->language_id;
        $model->faculty_id = $model->eduPlan->faculty_id;
        $model->direction_id = $model->eduPlan->direction_id;
        $model->max_ball = $model->eduSemestrExamType->max_ball;
        $model->exam_type_id = $model->eduSemestrExamType->exams_type_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // question file saqlaymiz
        $oldFile = $model->file;
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $upload_FileUrl = $model->upload($model->upload_file);
            if ($upload_FileUrl) {
                $model->file = $upload_FileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        if ($model->save()) {
            $examControlStudents = ExamControlStudent::find()
                ->where(['exam_control_id' => $model->id])
                ->all();
            if ($examControlStudents != null) {
                foreach ($examControlStudents as $examControlStudent) {
                    $examControlStudent->type = $model->type;
                    $examControlStudent->subject_id = $model->subject_id;
                    $examControlStudent->subject_category_id = $model->subject_category_id;
                    $examControlStudent->language_id = $model->language_id;
                    $examControlStudent->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                    $examControlStudent->edu_semestr_exam_type_id = $model->edu_semestr_exam_type_id ;
                    $examControlStudent->exam_type_id = $model->exam_type_id;
                    $examControlStudent->question_count = $model->question_count;
                    $examControlStudent->user_id  = $model->user_id ;
                    $examControlStudent->faculty_id  = $model->faculty_id ;
                    $examControlStudent->direction_id  = $model->direction_id ;
                    $examControlStudent->edu_plan_id  = $model->edu_plan_id ;
                    $examControlStudent->edu_semestr_id  = $model->edu_semestr_id ;
                    $examControlStudent->edu_year_id  = $model->edu_year_id ;
                    $examControlStudent->course_id  = $model->course_id ;
                    $examControlStudent->semestr_id  = $model->semestr_id ;

                    if (!$examControlStudent->validate()) {
                        $errors[] = $examControlStudent->errors;
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    if (!$examControlStudent->save()) {
                        $errors[] = _e("Exam Control Student not update.");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    if ($type != $model->type) {
                        if ($model->type == self::TEST) {
                            $test = Test::find()
                                ->where([
                                    'subject_id' => $model->subject_id,
                                    'exam_type_id' => $model->exam_type_id,
                                    'is_checked' => 1,
                                    'status' => 1,
                                    'is_deleted' => 0,
                                ])
                                ->orderBy(new Expression('rand()'))
                                ->limit($model->question_count ? $model->question_count : 0)
                                ->all();
                            if (count($test) == 0) {
                                $errors[] = ['questions' => _e('Questions not found.')];
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }
                            foreach ($test as $value) {
                                $studentTestAnswer = new ExamTestStudentAnswer();
                                $studentTestAnswer->exam_control_student_id = $examControlStudent->id;
                                $studentTestAnswer->exam_control_id = $model->id;
                                $studentTestAnswer->subject_id = $model->subject_id;
                                $studentTestAnswer->student_id = $examControlStudent->student_id;
                                $studentTestAnswer->user_id = $examControlStudent->student_user_id;
                                $studentTestAnswer->is_correct = 0;
                                $studentTestAnswer->exam_test_id = $value->id;
                                $studentTestAnswer->answer_option_id = $studentTestAnswer->answerOption($value->id);
                                $studentTestAnswer->options = $studentTestAnswer->optionsArray($value->id);
                                if (!$studentTestAnswer->save()) {
                                    $errors[] = $studentTestAnswer->errors;
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }

                            }
                        }
                        if ($type == self::TEST) {
                            ExamTestStudentAnswer::deleteAll(['exam_control_student_id' => $examControlStudent->id]);
                        }
                    }
                }
            } else {
                $students = $model->group->student;
                if (count($students) > 0) {
                    foreach ($students as $student) {
                        $examControlStudent = new ExamControlStudent();
                        $examControlStudent->exam_control_id = $model->id;
                        $examControlStudent->type = $model->type;
                        $examControlStudent->group_id = $model->group_id;
                        $examControlStudent->student_id = $student->id;
                        $examControlStudent->student_user_id = $student->user_id;
                        $examControlStudent->subject_id = $model->subject_id;
                        $examControlStudent->subject_category_id = $model->subject_category_id;
                        $examControlStudent->language_id = $model->language_id;
                        $examControlStudent->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                        $examControlStudent->edu_semestr_exam_type_id = $model->edu_semestr_exam_type_id ;
                        $examControlStudent->exam_type_id = $model->exam_type_id;
                        $examControlStudent->question_count = $model->question_count;
                        $examControlStudent->user_id  = $model->user_id ;
                        $examControlStudent->faculty_id  = $model->faculty_id ;
                        $examControlStudent->direction_id  = $model->direction_id ;
                        $examControlStudent->edu_plan_id  = $model->edu_plan_id ;
                        $examControlStudent->edu_semestr_id  = $model->edu_semestr_id ;
                        $examControlStudent->edu_year_id  = $model->edu_year_id ;
                        $examControlStudent->course_id  = $model->course_id ;
                        $examControlStudent->semestr_id  = $model->semestr_id ;
                        if (!$examControlStudent->validate()) {
                            $errors[] = $examControlStudent->errors;
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                        if (!$examControlStudent->save()) {
                            $errors[] = _e("Exam Control Student not saved.");
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                        if ($examControlStudent->type == self::TEST) {
                            $test = Test::find()
                                ->where([
                                    'subject_id' => $model->subject_id,
                                    'exam_type_id' => $model->exam_type_id,
                                    'is_checked' => 1,
                                    'status' => 1,
                                    'is_deleted' => 0,
                                ])
                                ->orderBy(new Expression('rand()'))
                                ->limit($model->question_count ? $model->question_count : 0)
                                ->all();
                            if (count($test) == 0) {
                                $errors[] = ['questions' => _e('Questions not found.')];
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }
                            foreach ($test as $value) {
                                $studentTestAnswer = new ExamTestStudentAnswer();
                                $studentTestAnswer->exam_control_student_id = $examControlStudent->id;
                                $studentTestAnswer->exam_control_id = $model->id;
                                $studentTestAnswer->subject_id = $model->subject_id;
                                $studentTestAnswer->student_id = $student->id;
                                $studentTestAnswer->user_id = $student->user_id;
                                $studentTestAnswer->is_correct = 0;
                                $studentTestAnswer->exam_test_id = $value->id;
                                $studentTestAnswer->answer_option_id = $studentTestAnswer->answerOption($value->id);
                                $studentTestAnswer->options = $studentTestAnswer->optionsArray($value->id);
                                if (!$studentTestAnswer->save()) {
                                    $errors[] = $studentTestAnswer->errors;
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }

                            }

                        }
                    }
                }
            }
        } else {
            $errors[] = _e("Exam Control not update.");
        }

        if (count($errors) == 0) {
            $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
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

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->upload_file->saveAs($url, false);
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
