<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%intensive_app}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int $subject_id
 * @property float $amount
 * @property float|null $payed_amount
 * @property int|null $payment_status 0-payment is expected. 1-payment approved. 2-payment did not reach or another error was observed 
 * @property int|null $edu_semestr_subject_id
 * @property int|null $edu_semestr_id
 * @property int|null $faculty_id
 * @property int|null $edu_plan_id
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property string|null $file
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemestr
 * @property EduSemestrSubject $eduSemestrSubject
 * @property Faculty $faculty
 * @property Student $student
 * @property Subject $subject
 */
class IntensiveApp extends \yii\db\ActiveRecord
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
        return '{{%intensive_app}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'edu_semestr_subject_id'], 'required'],
            // [['amount'], 'required', 'message' => 'The intensive price was not determined'],
            [['student_id', 'subject_id', 'payment_status', 'edu_semestr_subject_id', 'edu_semestr_id', 'faculty_id', 'edu_plan_id', 'course_id', 'semestr_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['amount', 'credit', 'one_credit_sum', 'payed_amount'], 'double'],
            [['file'], 'string', 'max' => 255],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],

            // student_id, edu_semestr_subject_id  are unique whren id_deleted is 0. message is "You have already submitted an application"
            // [['student_id', 'edu_semestr_subject_id'], 'unique', 'targetAttribute' => ['student_id', 'edu_semestr_subject_id'], 'message' => 'You have already submitted an application'],

            [['student_id', 'edu_semestr_subject_id'], 'unique', 'targetAttribute' => ['student_id', 'edu_semestr_subject_id'], 'when' => function ($model) {
                return $model->is_deleted == 0;
            }, 'message' => 'You have already submitted an application'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'student_id' => _e('Student ID'),
            'subject_id' => _e('Subject ID'),
            'amount' => _e('Amount'),
            'payed_amount' => _e('Payed Amount'),
            'payment_status' => _e('payment_status'),
            'edu_semestr_subject_id' => _e('Edu Semestr Subject ID'),
            'edu_semestr_id' => _e('Edu Semestr ID'),
            'faculty_id' => _e('Faculty ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'course_id' => _e('Course ID'),
            'semestr_id' => _e('Semestr ID'),
            'file' => _e('File'),

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
            'student_id',
            'subject_id',
            'amount',
            'payed_amount',
            'payment_status',
            'edu_semestr_subject_id',
            'edu_semestr_id',
            'faculty_id',
            'edu_plan_id',
            'course_id',
            'semestr_id',
            'credit',
            'one_credit_sum',
            'file',
            'order',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'archived',
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
            'student',
            'subject',
            'all',
            'allSumma',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getAll()
    {
        return $this->hasMany(self::className(), ['student_id' => 'student_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getAllSumma()
    {
        return $this->hasMany(self::className(), ['student_id' => 'student_id'])
            ->onCondition(['is_deleted' => 0])
            ->sum('amount');
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
     * IntensiveApp createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->student_id = self::student();

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->edu_semestr_id = $model->eduSemestrSubject->edu_semestr_id;
        $model->credit = $model->eduSemestrSubject->credit;
        $model->one_credit_sum = $model->eduSemestr->one_credit_sum;

        $model->edu_plan_id = $model->student->edu_plan_id;
        $model->faculty_id = $model->student->faculty_id;

        $model->course_id = $model->eduSemestr->course_id;
        $model->semestr_id = $model->eduSemestr->semestr_id;


        // Check if one_credit_sum and credit are not null before calculating amount
        if ($model->eduSemestr && $model->eduSemestrSubject && $model->eduSemestr->one_credit_sum !== null && $model->eduSemestrSubject->credit !== null) {
            $model->amount = (float) $model->eduSemestr->one_credit_sum * (int) $model->eduSemestrSubject->credit;

            if ($model->amount <= 0) {
                $errors[] = 'Amount calculation error';
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $errors[] = 'One or more required fields are missing';
            $transaction->rollBack();
            return simplify_errors($errors);
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

    /**
     * IntensiveApp updateItem <$model, $post>
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


        // if ($model->payment_status == 1) {
        //     $errors[] = _e("Payment approved");
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

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
