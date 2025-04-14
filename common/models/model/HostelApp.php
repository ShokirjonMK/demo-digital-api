<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\User as ModelsUser;
use Yii;
use yii\behaviors\TimestampBehavior;

class HostelApp extends \yii\db\ActiveRecord
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
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_REVISION = 3;

    const STATUS_IN_CHECKING = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hostel_app';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'student_id',
                    'user_id',
                    'edu_year_id',
                ], 'required'
            ],
            [
                [
                    'student_id',
                    'edu_year_id',
                    'faculty_id',
                    'user_id',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'archived'
                ], 'integer'
            ],

            [['description', 'conclution'], 'string'],

            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModelsUser::className(), 'targetAttribute' => ['user_id' => 'id']],

            // [['exam_student_id'], 'unique', 'targetAttribute' => ['is_deleted']],
            // [['student_id'], 'unique', 'targetAttribute' => ['edu_year_id', 'is_deleted']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'student_id' => _e('student_id'),
            'user_id' => _e('user_id'),
            'faculty_id' => _e('faculty_id'),
            'edu_year_id' => _e('edu_year_id'),
            'ball' => _e('ball'),
            'description' => _e('description'),
            'conclution' => _e('conclution'),

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
            'user_id',
            'faculty_id',
            'edu_year_id',
            'ball',
            'description',
            'conclution',

            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'student',
            'eduYear',
            'profile',
            'hostelDoc',
            'isChecked',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getIsChecked()
    {
        $model = new HostelDoc();

        $query = $model->find();
        $query->andWhere(['not', ['is_checked' => null]]);

        if (count($query->all()) > 0) {
            return 0;
        } else {
            return 1;
        }

        return $this->hasMany(HostelDoc::className(), ['hostel_app_id' => 'id'])->onCondition(['student_id' => $this->student_id]);
    }


    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getHostelDoc()
    {
        return $this->hasMany(HostelDoc::className(), ['hostel_app_id' => 'id'])->onCondition(['student_id' => $this->student_id]);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getStudentHere()
    {
        return  $this->student_now();
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->edu_year_id = EduYear::findOne(['year' => date("Y")])->id;

        $has = self::findOne(['user_id' => $model->user_id, 'edu_year_id' => $model->edu_year_id]);

        if ($has) {
            if ($has->is_deleted == 1) {
                $has->is_deleted = 0;
            }
            $has->description = $model->description;
            if ($has->update()) {
                $transaction->commit();
                return true;
            }
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

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

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

    public static function teacherUpdateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->teacher_conclusion = $post['teacher_conclusion'] ?? null;
        $model->type = $post['type'] ?? null;

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

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACCEPTED => _e('STATUS_ACCEPTED'),
            self::STATUS_REJECTED => _e('STATUS_REJECTED'),
            self::STATUS_REVISION => _e('STATUS_REVISION'),

        ];
    }
}
