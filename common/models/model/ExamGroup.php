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
class ExamGroup extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const ONLINE = 0;
    const OFFLINE = 1;

    const EXAM = 3;

    const QUESTION = 1;
    const TEST = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'exam_id',
                    'group_id',
                    'start_time',
                    'finish_time',
                    'duration',
                    'exam_form',
                ], 'required'
            ],
            [
                [
                    'room_id',
                    'building_id',
                    'exam_id',
                    'group_id',
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
            [['description'], 'safe'],
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
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
            [['building_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']],

            ['start_time' , 'validateTime'],
            ['exam_type_id' , 'validateExamType'],
        ];
    }

    public function validateTime($attribute, $params)
    {
        if ($this->start_time >= $this->finish_time) {
            $this->addError($attribute, _e('The finish time must be greater than the start time.'));
        }
        if (!($this->exam->start_time <= $this->start_time && $this->exam->finish_time >= $this->finish_time)) {
            $this->addError($attribute, _e('Allotment times must be given between exams!'));
        }

        $endTime = strtotime('+'. $this->duration .' minutes' , $this->start_time);

        if ($endTime > $this->finish_time) {
            $this->addError('duration', _e('The student exam time must be less than the exam completion time!'));
        }
    }


    public function fields()
    {
        $fields =  [
            'id',
            'exam_id',
            'group_id',
            'edu_plan_id',
            'edu_semestr_id',
            'faculty_id',
            'direction_id',
            'course_id',
            'semestr_id',
            'edu_year_id',

            'edu_semestr_subject_id',
            'exam_type_id',
            'room_id',
            'building_id',

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
            'room',
            'building',
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
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
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

    public function getStudentsStatusDefault() {
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_status' => ExamStudent::STUDENT_DEFAULT]);
    }
    public function getStudentsStatusStarted() {
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_status' => ExamStudent::STUDENT_STARTED]);
    }
    public function getStudentsStatusFinished() {
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_status' => ExamStudent::STUDENT_FINISHED]);
    }
    public function getStudentsStatusFallen() {
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->onCondition(['student_status' => ExamStudent::STUDENT_FALLEN]);
    }

    public function getStudentStart() {
        return ExamStudent::find()
            ->where(['exam_id' => $this->id ])
            ->andWhere(['>' , 'student_status' , 0])
            ->all();
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
        $exam = $model->exam;
        $model->edu_year_id = $exam->edu_year_id;
        $model->course_id = $exam->course_id;
        $model->semestr_id = $exam->semestr_id;
        $model->faculty_id = $exam->faculty_id;
        $model->direction_id = $exam->direction_id;
        $model->subject_id = $exam->subject_id;
        $model->type = $exam->type;
        $model->edu_plan_id = $exam->edu_plan_id;
        $model->edu_semestr_subject_id = $exam->edu_semestr_subject_id;
        $model->exam_type_id = $exam->exam_type_id;
        $model->max_ball = $exam->max_ball;
        $model->edu_semestr_id = $exam->edu_semestr_id;
        $model->building_id = $exam->room->building_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Guruhni ushbu fandan darsi bor yoki yo'qligini tekshiradi
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

        // Xona bandligini tekshiradi.
        if ($model->exam_form == self::OFFLINE) {
            $validRoom = ExamGroup::validRoom($model);
            if ($validRoom) {
                $errors[] = _e("During this time, the room is reserved for another exam.");
            }
            $validGroup = ExamGroup::validGroup($model);
            if ($validGroup) {
                $errors[] = _e("This group has another exam in this time frame!");
            }
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

    public static function validRoom($model) {
        $isExams = ExamGroup::find()->where([
            'exam_form' => self::OFFLINE,
            'edu_year_id' => $model->edu_year_id,
            'room_id' => $model->room_id,
            'is_deleted' => 0
        ])
            ->andWhere(['!=' , 'id' , $model->id])
            ->all();

        $isRoom = false;
        foreach ($isExams as $isExam) {
            if ($isExam->start_time < $isExam->start_time && $isExam->end_time > $isExam->start_time && $isExam->end_time <= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isExam->start_time >= $isExam->start_time && $isExam->end_time > $isExam->start_time && $isExam->end_time <= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isExam->start_time >= $isExam->start_time && $isExam->start_time < $isExam->finish_time && $isExam->end_time >= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isExam->start_time <= $isExam->start_time && $isExam->end_time >= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isRoom) {
                break;
            }
        }
        return $isRoom;
    }

    public static function validGroup($model) {
        $isExams = ExamGroup::find()->where([
            'exam_form' => self::OFFLINE,
            'edu_year_id' => $model->edu_year_id,
            'group_id' => $model->group_id,
            'is_deleted' => 0
        ])
            ->andWhere(['!=' , 'id' , $model->id])
            ->all();

        $isRoom = false;
        foreach ($isExams as $isExam) {
            if ($isExam->start_time < $isExam->start_time && $isExam->end_time > $isExam->start_time && $isExam->end_time <= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isExam->start_time >= $isExam->start_time && $isExam->end_time > $isExam->start_time && $isExam->end_time <= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isExam->start_time >= $isExam->start_time && $isExam->start_time < $isExam->finish_time && $isExam->end_time >= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isExam->start_time <= $isExam->start_time && $isExam->end_time >= $isExam->finish_time) {
                $isRoom = true;
            }
            if ($isRoom) {
                break;
            }
        }
        return $isRoom;
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
        $exam = $model->exam;
        $model->edu_year_id = $exam->edu_year_id;
        $model->course_id = $exam->course_id;
        $model->semestr_id = $exam->semestr_id;
        $model->faculty_id = $exam->faculty_id;
        $model->direction_id = $exam->direction_id;
        $model->subject_id = $exam->subject_id;
        $model->type = $exam->type;
        $model->edu_plan_id = $exam->edu_plan_id;
        $model->edu_semestr_subject_id = $exam->edu_semestr_subject_id;
        $model->exam_type_id = $exam->exam_type_id;
        $model->max_ball = $exam->max_ball;
        $model->edu_semestr_id = $exam->edu_semestr_id;
        $model->building_id = $exam->room->building_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Guruhni ushbu fandan darsi bor yoki yo'qligini tekshiradi
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

        // Xona bandligini tekshiradi.
        if ($model->exam_form == self::OFFLINE) {
            $validRoom = ExamGroup::validRoom($model);
            if ($validRoom) {
                $errors[] = _e("During this time, the room is reserved for another exam.");
            }
            $validGroup = ExamGroup::validGroup($model);
            if ($validGroup) {
                $errors[] = _e("This group has another exam in this time frame!");
            }
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

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }

        return parent::beforeSave($insert);
    }

}
