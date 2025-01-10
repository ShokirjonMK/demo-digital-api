<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $name
 * @property int $building_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Building $building
 * @property TimeTable1[] $timeTables
 */
class StudentSemestrSubject extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

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
        return 'student_semestr_subject';
    }


    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['edu_semestr_subject_id','student_id'], 'required'],
            [['all_ball'], 'number'],
            [['edu_plan_id','edu_semestr_id', 'edu_semestr_subject_id','student_id','student_user_id','faculty_id','direction_id','edu_form_id','edu_year_id','course_id','semestr_id','closed', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',

            'edu_plan_id',
            'edu_semestr_id',
            'edu_semestr_subject_id',
            'student_id',
            'student_user_id',
            'faculty_id',
            'direction_id',
            'edu_form_id',
            'edu_year_id',
            'course_id',
            'semestr_id',
            'closed',
            'all_ball',

            'rating' => function () {
                return rating($this->all_ball);
            },

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
            'eduPlan',
            'eduSemestr',
            'eduSemestrSubject',
            'student',
            'studentUser',
            'faculty',
            'direction',
            'eduForm',
            'eduYear',
            'course',
            'semestr',
            'group',
            'studentVedomst',

            'studentAttends',
            'studentAttendsCount',
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
     * Gets query for [[Building]].
     *
     * @return \yii\db\ActiveQuery
     */


    public function getStudentAttends()
    {
        $subject = $this->eduSemestrSubject;
        return $this->hasMany(TimetableAttend::className(), ['student_id' => 'student_id'])
            ->onCondition([
                'subject_id' => $subject->subject_id,
                'edu_year_id' => $this->edu_year_id,
                'is_deleted' => 0,
            ])->orderBy(['date' => SORT_ASC]);
    }

    public function getStudentAttendsCount()
    {
        return count($this->studentAttends);
    }

    public function getStudentAttendReason()
    {
        $subject = $this->eduSemestrSubject;
        return $this->hasMany(TimetableAttend::className(), ['student_id' => 'student_id'])
            ->onCondition([
            'reason' => 1,
            'subject_id' => $subject->subject_id,
            'edu_year_id' => $this->edu_year_id,
        ]);
    }

    public function getStudentAttendReasonCount()
    {
        return count($this->studentAttendReason);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getStudentUser()
    {
        return $this->hasOne(User::className(), ['id' => 'student_user_id']);
    }

    public function getStudentVedomst()
    {
        return $this->hasMany(StudentSemestrSubjectVedomst::className(), ['student_semestr_subject_id' => 'id'])->where(['is_deleted' => 0])->orderBy('vedomst asc');
    }

    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
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

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }



        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
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
