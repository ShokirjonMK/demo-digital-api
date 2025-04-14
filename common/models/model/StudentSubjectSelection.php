<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "military ".
 *
 * @property int $id
 * @property string $description
 * @property int $edu_semestr_subject_id
 * @property int $student_id
 * @property int $edu_semester_id
 * @property int $subject_id
 * @property int $faculty_id
 * @property int $edu_plan_id
 * @property int $type
 * @property int $user_id
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class StudentSubjectSelection extends \yii\db\ActiveRecord
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
        return 'student_subject_selection';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                [
                    'edu_semestr_subject_id',
                    'student_id',
                ], 'required'
            ],
            [
                ['description'], 'string'
            ],
            [
                [
                    'edu_semestr_subject_id',
                    'student_id',
                    'edu_semester_id',
                    'subject_id',
                    'faculty_id',
                    'edu_plan_id',
                    'type',
                    'user_id',
                    'status',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'archived',
                ], 'integer'
            ],
            [
                ['user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['edu_semestr_subject_id'],
                'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::class, 'targetAttribute' => ['edu_semestr_subject_id' => 'id']
            ],
            [
                ['student_id'],
                'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']
            ],
            [
                ['edu_semester_id'],
                'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::class, 'targetAttribute' => ['edu_semester_id' => 'id']
            ],
            [
                ['subject_id'],
                'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']
            ],
            [
                ['faculty_id'],
                'exist', 'skipOnError' => true, 'targetClass' => Faculty::class, 'targetAttribute' => ['faculty_id' => 'id']
            ],
            [
                ['edu_plan_id'],
                'exist', 'skipOnError' => true, 'targetClass' => EduPlan::class, 'targetAttribute' => ['edu_plan_id' => 'id']
            ],

            ['edu_semestr_subject_id', 'unique', 'targetAttribute' => ['edu_semestr_subject_id', 'user_id', 'archived']],


        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_semestr_subject_id' => _e('edu_semestr_subject_id'),
            'student_id' => _e('student_id'),
            'edu_semester_id' => _e('edu_semester_id'),
            'subject_id' => _e('subject_id'),
            'faculty_id' => _e('faculty_id'),
            'edu_plan_id' => _e('edu_plan_id'),
            'type' => _e('type'),
            'user_id' => _e('user_id'),
            'description' => _e('description'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',

            'edu_semestr_subject_id',
            'student_id',
            'edu_semester_id',
            'subject_id',
            'faculty_id',
            'edu_plan_id',
            'type',
            'user_id',
            'description',

            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'eduSemestrSubject',
            'student',
            'eduSemester',
            'faculty',
            'eduPlan',

            'subject',
            'user',
            'profile',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'user_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }


    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::class, ['id' => 'edu_semestr_subject_id']);
    }
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }
    public function getEduSemester()
    {
        return $this->hasOne(EduSemester::class, ['id' => 'edu_semester_id']);
    }
    public function getFaculty()
    {
        return $this->hasOne(Faculty::class, ['id' => 'faculty_id']);
    }
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::class, ['id' => 'edu_plan_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $student = self::student(2);
        if ($student == null) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->user_id = current_user_id();
        $model->student_id = $student->id;
        $model->faculty_id = $student->faculty_id;
        $model->edu_plan_id = $student->edu_plan_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->edu_semester_id = $model->eduSemestrSubject->edu_semestr_id;

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

        $student = self::student(2);
        if ($student == null) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_id = $student->id;
        $model->faculty_id = $student->faculty_id;
        $model->edu_plan_id = $student->edu_plan_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->edu_semester_id = $model->eduSemestrSubject->edu_semestr_id;

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
