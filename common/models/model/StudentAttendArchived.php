<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "{{%student_attend}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int|null $reason 0 sababsiz 1 sababli
 * @property int $attend_id
 * @property int|null $attend_reason_id
 * @property string $date
 * @property int $time_table_id
 * @property int $subject_id
 * @property int $subject_category_id
 * @property int $time_option_id
 * @property int $edu_year_id
 * @property int $edu_semestr_id
 * @property int|null $faculty_id
 * @property int|null $course_id
 * @property int|null $edu_plan_id
 * @property int|null $type 1 kuz 2 bohor
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Attend $attend
 * @property AttendReason $attendReason
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemestr
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Student $student
 * @property Subject $subject
 * @property SubjectCategory $subjectCategory
 * @property TimeOption $timeOption
 * @property TimeTable $timeTable
 */
class StudentAttendArchived extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const REASON_TRUE = 1;
    const REASON_FALSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_attend_archived';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'student_id',
                'attend_id',
                'date',
                'time_table_id',
                'subject_id',
                'subject_category_id',
                'time_option_id',
                'edu_year_id',
                'edu_semestr_id'
            ], 'required'],
            [[
                'student_id',
                'reason',
                'attend_id',
                'attend_reason_id',
                'time_table_id',
                'subject_id',
                'subject_category_id',
                'time_option_id',
                'edu_year_id',
                'edu_semestr_id',
                'faculty_id',
                'course_id',
                'edu_plan_id',
                'type',
                'status',
                'order',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted',
                'archived'

            ], 'integer'],
            [['date'], 'safe'],
            [['attend_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attend::className(), 'targetAttribute' => ['attend_id' => 'id']],
            [['attend_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttendReason::className(), 'targetAttribute' => ['attend_reason_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['time_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeOption::className(), 'targetAttribute' => ['time_option_id' => 'id']],
            [['time_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['time_table_id' => 'id']],


            [['student_id'], 'unique', 'targetAttribute' => ['student_id', 'time_table_id', 'date'], 'message' => "Student Already recorded"],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),

            'student_id' => _e('Student ID'),
            'reason' => _e('0 sababsiz 1 sababli'),
            'attend_id' => _e('Attend ID'),
            'attend_reason_id' => _e('Attend Reason ID'),
            'date' => _e('Date'),
            'time_table_id' => _e('Time Table ID'),
            'subject_id' => _e('Subject ID'),
            'subject_category_id' => _e('Subject Category ID'),
            'time_option_id' => _e('Time Option ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'edu_semestr_id' => _e('Edu Semestr ID'),
            'faculty_id' => _e('Faculty ID'),
            'course_id' => _e('Course ID'),
            'edu_plan_id' => _e('Edu Plan ID'),

            'type' => _e('type'),
            'archived' => _e('archived'),
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

            'student_id',
            'reason',
            // 'attend_id',
            // 'attend_reason_id',
            'date',
            // 'time_table_id',
            // 'subject_id',
            // 'subject_category_id',
            // 'time_option_id',
            // 'edu_year_id',
            // 'edu_semestr_id',
            // 'faculty_id',
            // 'course_id',
            // 'edu_plan_id',
            // 'type',


            // 'order',
            // 'status',
            // 'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'attend',
            'attendReason',
            'eduPlan',
            'eduSemestr',
            'eduYear',
            'faculty',
            'student',
            'subject',
            'subjectCategory',
            'timeOption',
            'timeTable',
            'count',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getCount()
    {
        return self::find()
            ->where(['student_id' => $this->student_id])
            ->count();
        return count($this->studentAttends);
    }

    public function getStudentAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['attend_id' => 'attend_id'])->onCondition(['student_id' => $this->student_id]);
    }

    /**
     * Gets query for [[Attend]].
     *
     * @return \yii\db\ActiveQuery|AttendQuery
     */
    public function getAttend()
    {
        return $this->hasOne(Attend::className(), ['id' => 'attend_id']);
    }

    /**
     * Gets query for [[AttendReason]].
     *
     * @return \yii\db\ActiveQuery|AttendReasonQuery
     */
    public function getAttendReason()
    {
        return $this->hasOne(AttendReason::className(), ['id' => 'attend_reason_id']);
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
     * Gets query for [[EduSemestr]].
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
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
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
     * Gets query for [[TimeOption]].
     *
     * @return \yii\db\ActiveQuery|TimeOptionQuery
     */
    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

    /**
     * Gets query for [[TimeTable]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTimeTable()
    {
        return $this->hasOne(TimeTable::className(), ['id' => 'time_table_id']);
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

        /* $student = self::student(2);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        } */



        // time_table_id
        $model->subject_id = $model->timeTable->subject_id;
        $model->subject_category_id = $model->timeTable->subject_category_id;
        $model->time_option_id = $model->timeTable->time_option_id;
        $model->edu_year_id = $model->timeTable->edu_year_id;
        $model->edu_semestr_id = $model->timeTable->edu_semestr_id;
        $model->faculty_id = $model->timeTable->faculty_id;
        $model->edu_plan_id = $model->timeTable->edu_plan_id;

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

        $student = self::student(2);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_id = $student->id;
        $model->faculty_id = $student->faculty_id;
        $model->edu_plan_id = $student->edu_plan_id;
        $model->gender = $student->gender;

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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
