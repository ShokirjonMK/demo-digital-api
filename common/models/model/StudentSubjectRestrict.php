<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%student_subject_restrict}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int $edu_semestr_subject_id
 * @property string|null $description
 * @property int $subject_id
 * @property int|null $semestr_id
 * @property int|null $edu_semestr_id
 * @property int|null $edu_plan_id
 * @property int|null $faculty_id
 * @property int|null $edu_year_id
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemestr
 * @property EduSemestrSubject $eduSemestrSubject
 * @property Faculty $faculty
 * @property Semestr $semestr
 * @property Student $student
 * @property Subject $subject
 */
class StudentSubjectRestrict extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student_subject_restrict}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'edu_semestr_subject_id',], 'required'],
            [['student_id', 'edu_semestr_subject_id', 'subject_id', 'semestr_id', 'edu_semestr_id', 'edu_plan_id', 'faculty_id', 'edu_year_id', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],

            [['student_id'], 'unique', 'targetAttribute' => ['edu_semestr_subject_id', 'student_id', 'is_deleted'], 'message' => "This student already restricted for this eduSemestrSubject!"],

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
            'edu_semestr_subject_id' => _e('Edu Semestr Subject ID'),
            'description' => _e('Description'),
            'subject_id' => _e('Subject ID'),
            'semestr_id' => _e('Semestr ID'),
            'edu_semestr_id' => _e('Edu Semestr ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'faculty_id' => _e('Faculty ID'),
            'edu_year_id' => _e('edu_year_id'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'student_id',
            'edu_semestr_subject_id',
            'description',
            'subject_id',
            'semestr_id',
            'edu_semestr_id',
            'edu_plan_id',
            'faculty_id',
            'edu_year_id',
            'status',
            'is_deleted',
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
            'eduPlan',
            'eduSemestr',
            'eduSemestrSubject',
            'faculty',
            'semestr',
            'student',
            'subject',
            'eduYear',


            'studentAttend',
            'studentAttendCount',

            'studentAttendReason',
            'studentAttendReasonCount',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[StudentAttend]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttend()
    {
        return $this->hasMany(StudentAttend::className(), ['student_id' => 'student_id'])
            ->onCondition([
                'subject_id' => $this->subject_id,
                'edu_semestr_id' => $this->edu_semestr_id,
                // 'reason' => 0
            ]);
    }
    public function getStudentAttendReason()
    {
        return $this->hasMany(StudentAttend::className(), ['student_id' => 'student_id'])
            ->onCondition([
                'subject_id' => $this->subject_id,
                'edu_semestr_id' => $this->edu_semestr_id,
                'reason' => 0,
                'archived' => 0
            ]);
    }

    public function getStudentAttendCount()
    {
        return count($this->studentAttend);
    }
    public function getStudentAttendReasonCount()
    {
        return count($this->studentAttendReason);
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
     * Gets query for [[EduSemestrSubject]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrSubjectQuery
     */
    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
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
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[Semestr]].
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
     * StudentSubjectRestrict createItem <$model, $post>
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

        if ($model->student->edu_plan_id != $model->eduSemestrSubject->eduSemestr->edu_plan_id) {
            $errors[] = _e('This student is not releted to this subject');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->semestr_id = $model->eduSemestrSubject->eduSemestr->semestr_id;
        $model->edu_semestr_id = $model->eduSemestrSubject->edu_semestr_id;
        $model->edu_plan_id = $model->eduSemestrSubject->eduSemestr->edu_plan_id;
        $model->faculty_id = $model->eduSemestrSubject->faculty_id;
        $model->edu_year_id = $model->eduSemestrSubject->eduSemestr->edu_year_id;

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

    /**
     * StudentSubjectRestrict updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->description = $post['description'] ?? $model->description;

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
}
