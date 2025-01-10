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
 * This is the model class for table "{{%exam}}".
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
 * @property int $edu_semestr_id
 * @property int $edu_semester_subject_id
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
    const STATUS_STARTED = 2;
    const STATUS_FINISHED = 3;
    const STATUS_ALLOTMENT = 4;
    const STATUS_NOTIFY = 5;

    const EXAM = 3;

    const WRITE = 1;
    const TEST = 2;

    const LECTURE = 1;

    const appeal_time = 3 * 24 * 60 * 60; // 3 kun soat

    const UPLOADS_FOLDER = 'uploads/exam/question/';

    public $upload_file;

    public $questionFileMaxSize = 1024 * 1024 * 10; // 10 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'edu_plan_id',
                    'edu_semestr_id',
                    'edu_semestr_subject_id',
                    'exam_type_id',
                    'start_time',
                    'finish_time',
                    'duration',
                    'question_count',
                    'type',
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
                    'edu_plan_id',
                    'edu_semestr_id',
                    'edu_semestr_subject_id',
                    'exam_type_id',
                    'faculty_id',
                    'direction_id',
                    'question_count',
                    'is_confirm',
                    'type',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['max_ball'], 'number'],
            [['question','description'], 'safe'],
            [['file'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],

            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => $this->questionFileMaxSize],
            ['start_time' , 'validateTime'],
            ['exam_type_id' , 'validateExamType'],
            ['question_count' , 'validateCount'],
        ];
    }


    public function validateTime($attribute, $params)
    {
        if ($this->start_time >= $this->finish_time) {
            $this->addError($attribute, _e('The finish time must be greater than the start time.'));
        }
    }

    public function validateExamType($attribute, $params)
    {
        if ($this->exam_type_id != self::EXAM) {
            $this->addError($attribute, _e('You can only add an exam!'));
        }
    }

    public function validateCount($attribute, $params)
    {
        $tests = Test::find()
            ->where([
                'type' => $this->type,
                'subject_id' => $this->eduSemestrSubject->subject_id,
                'exam_type_id' => $this->exam_type_id,
                'is_checked' => 1,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->count();

        if ($tests < $this->question_count) {
            $this->addError($attribute, _e('Not enough questions!'));
        }
    }


    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $this->eduPlan->translate->name . " | " . $this->subject->translate->name . " | " .$this->eduSemestr->semestr_id . ' - sm';
            },
            'edu_plan_id',
            'edu_semestr_id',
            'faculty_id',
            'direction_id',
            'course_id',
            'semestr_id',
            'edu_year_id',

            'edu_semestr_subject_id',
            'exam_type_id',

            'start_time',
            'finish_time',
            'max_ball',
            'duration',
            'question',
            'file',
            'type',
            'question_count',
            'description',
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
            'course',
            'direction',
            'eduPlan',
            'eduSemestr',
            'eduYear',
            'faculty',
            'language',
            'semester',
            'subject',
            'examStudents',
            'examTimes',
            'examsType',
            'studentMark',
            'timeTableGroup',

            'examStudentsCount',
            'examStudentsCheck',
            'examStudentsCheckCount',

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
            'end' => $this->finish_time,
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

    public function getTimeTableGroup()
    {
        return Group::find()
            ->where(['is_deleted' => 0])
            ->andWhere([
                'in' , 'id' , TimeTable1::find()
                    ->select('group_id')
                    ->where([
                        'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
                        'edu_semestr_id' => $this->edu_semestr_id,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
            ])
            ->all();
    }

    public function getStudentGroupPermission()
    {
        $type = 2;
        $user_id = current_user_id();
        $timeTable = TimeTable1::find()
            ->where([
                'group_id' =>  $this->student($type , $user_id)->group_id,
                'edu_semestr_subject_id' => Yii::$app->request->get('edu_semestr_subject_id'),
                'edu_semestr_id' => $this->edu_semestr_id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();
        if (count($timeTable) > 0) {
            return true;
        }
        return false;
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

    public function getExamsType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exam_type_id']);
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


    public function getExamStudents()
    {
        if (isRole('student')) {
            return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])
                ->onCondition(['student_id' => $this->student()]);
        }
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id']);
    }

    public function getExamStudentsCount() {
        $examStudents = $this->examStudents;
        return count($examStudents);
    }

    public function getExamStudentsCheck() {
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])
            ->onCondition(['status' => ExamStudent::STUDENT_EVALUATED]);
    }
    public function getExamStudentsCheckCount() {
        $examStudentsCheck = $this->examStudentsCheck;
        return count($examStudentsCheck);
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

    public function getStudentMark()
    {
        $studentMark = StudentMark::findOne([
            'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
            'exam_type_id' => $this->exam_type_id,
            'student_user_id' => current_user_id(),
            'status' => 1
        ]);
        if ($studentMark != null) {
            return 1;
        }
        return 0;
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
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
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
        $model->edu_year_id = $model->eduSemestr->edu_year_id;
        $model->course_id = $model->eduSemestr->course_id;
        $model->semestr_id = $model->eduSemestr->semestr_id;
        $model->faculty_id = $model->eduPlan->faculty_id;
        $model->direction_id = $model->eduPlan->direction_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->status = 0;

        $ball = EduSemestrExamsType::findOne([
            'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
            'exams_type_id' => $model->exam_type_id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($ball == null) {
            $errors[] = _e("There is no mark for the exam in this subject.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->max_ball = $ball->max_ball;

        $query = Exam::find()->where([
            'edu_plan_id' => $model->edu_plan_id,
            'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
            'exam_type_id' => $model->exam_type_id,
            'is_deleted' => 0,
        ])->count();
        if ($query > 0) {
            $errors[] = _e('This information was previously created.');
        }

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

        if (count($errors) == 0) {
            $model->save();
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

        if ($model->status > self::STATUS_ACTIVE) {
            $errors[] = _e("You cannot change the data!");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->load($post, '');

        if (isset($post['start'])) {
            $model['start_time'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish_time'] = strtotime($post['finish']);
        }

        $model->edu_year_id = $model->eduSemestr->edu_year_id;
        $model->course_id = $model->eduSemestr->course_id;
        $model->semestr_id = $model->eduSemestr->semestr_id;
        $model->faculty_id = $model->eduPlan->faculty_id;
        $model->direction_id = $model->eduPlan->direction_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;

        $ball = EduSemestrExamsType::findOne([
            'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
            'exams_type_id' => $model->exam_type_id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($ball == null) {
            $errors[] = _e("There is no mark for the exam in this subject.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->max_ball = $ball->max_ball;

        $query = Exam::find()->where([
            'edu_plan_id' => $model->edu_plan_id,
            'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
            'exam_type_id' => $model->exam_type_id,
            'is_deleted' => 0,
        ])->andWhere(['!=' , 'id' , $model->id])->count();
        if ($query > 0) {
            $errors[] = _e('This information was previously created.');
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $oldFile = $model->file;
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

        if (count($errors) == 0) {
            $model->save();
            if ($oldFile != $model->file) {
                $model->deleteFile($oldFile);
            }
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function examTeacherAttach($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['teachers'])) {
            $teachers = json_decode(str_replace("'", "", $post['teachers']));
            $datas = [];
            foreach ($teachers as $teacherUserId => $count) {
                $examTeacherCount = ExamStudent::find()
                    ->where([
                        'exam_id' => $model->id,
                        'exam_teacher_user_id' => $teacherUserId,
                        'status' => ExamStudent::STUDENT_FINISHED,
                        'is_deleted' => 0
                    ])
                    ->orderBy(new Expression('rand()'))
                    ->all();
                if (count($examTeacherCount) > $count) {
                    $deleteCount = count($examTeacherCount) - $count;
                    foreach ($examTeacherCount as $delTeacher) {
                        if ($deleteCount != 0) {
                            $delTeacher->exam_teacher_user_id = null;
                            $delTeacher->save(false);
                        } else {
                            break;
                        }
                        $deleteCount--;
                    }
                } elseif (count($examTeacherCount) < $count) {
                    $addCount = $count - count($examTeacherCount);
                    $datas[] = [
                        $teacherUserId => $addCount
                    ];
                }
            }

            if (count($datas) > 0) {
                foreach ($datas as $data) {
                    foreach ($data as $userId => $plusCount) {
                        $examStudents = ExamStudent::find()
                            ->where([
                                'exam_id' => $model->id,
                                'exam_teacher_user_id' => null,
                                'is_deleted' => 0
                            ])
                            ->orderBy(new Expression('rand()'))
                            ->limit($plusCount)
                            ->all();
                        if (count($examStudents) > 0) {
                            foreach ($examStudents as $examStudent) {
                                $examStudent->exam_teacher_user_id = $userId;
                                $examStudent->save(false);
                            }
                        }
                    }
                }
            }

        } else {
            $errors[] = ['teachers' => _e("Teachers required!")];
        }

        $model->save(false);
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function examCheck($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->status == self::STATUS_ACTIVE || $model->status == self::STATUS_STARTED) {
            if (isset($post['status'])) {
                $model->status = $post['status'];
            }
        } else {
            $errors[] = _e("You can't confirm yet!");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function examFinish($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->status == self::STATUS_FINISHED || $model->status == self::STATUS_STARTED)) {
            $errors[] = _e("You cannot change the information!");
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $model->status = $post['status'];
        }

        $examStudents = ExamStudent::find()
            ->where([
                'exam_id' => $model->id,
                'status' => [ExamStudent::STUDENT_STARTED , ExamStudent::STUDENT_FINISHED],
                'is_deleted' => 0
            ])
            ->all();
        if (count($examStudents) > 0) {
            foreach ($examStudents as $examStudent) {
                if ($model->type == self::TEST) {
                    $examStudentQuestions = ExamStudentQuestion::find()->where([
                        'exam_student_id' => $examStudent->id,
                        'type' => $model->type,
                        'is_deleted' => 0
                    ])->all();
                    if (count($examStudentQuestions) > 0) {
                        $correctCount = 0;
                        foreach ($examStudentQuestions as $examStudentQuestion) {
                            if ($examStudentQuestion->is_correct == 1) {
                                $correctCount++;
                            }
                        }
                        $examStudent->student_ball = number_format(($model->max_ball * $correctCount) / $model->question_count, 1, '.', '');
                    }
                    $examStudent->status = ExamStudent::STUDENT_EVALUATED;
                } else {
                    $examStudent->status = ExamStudent::STUDENT_FINISHED;
                }
                $examStudent->save(false);
            }
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function examAllotment($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->status == self::STATUS_FINISHED || $model->status == self::STATUS_ALLOTMENT) {
            if (isset($post['status'])) {
                $model->status = $post['status'];
            }
        } else {
            $errors[] = _e("You cannot change the information!");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function examNotify($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->status == self::STATUS_ALLOTMENT || $model->status == self::STATUS_NOTIFY) {
            if (isset($post['status'])) {
                $model->status = $post['status'];
            }
        } else {
            $errors[] = _e("The exam has not been completed.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0) {
            $model->save(false);
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
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

}
