<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "time_table".
 *
 * @property int $id
 * @property string $key
 * @property int $faculty_id
 * @property int $edu_plan_id
 * @property int $edu_year_id
 * @property int $edu_semester_id
 * @property int $lang_id
 * @property int $type
 * @property string $description
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Faculty_id $faculty_id
 * @property Edu_plan_id $edu_plan_id
 * @property Edu_year_id $edu_year_id
 * @property Edu_semester_id $edu_semester_id
 * @property Lang_id $lang_id
 */
class TimeOption extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_NEW = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'key',
                    // 'faculty_id',
                    // 'edu_plan_id',
                    'edu_year_id',

                    'edu_semester_id',
                    'capacity',

                    'language_id',
                ], 'required'
            ],
            [
                [
                    'faculty_id',
                    'edu_plan_id',
                    'edu_year_id',
                    'edu_semester_id',
                    'language_id',
                    'capacity',

                    // 'order',
                    'status',
                    'selected_count',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'archived',
                    'is_deleted'
                ], 'integer'
            ],
            [['description'], 'string'],
            [['key'], 'string', 'max' => 1],
            ['key', 'filter', 'filter' => 'ucfirst'],
            [
                ['edu_semester_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semester_id' => 'id']
            ],
            [
                ['edu_plan_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']
            ],
            [
                ['faculty_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']
            ],
            [
                ['edu_year_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [
                ['language_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']
            ],

            [['key'], 'unique', 'targetAttribute' => ['edu_semester_id', 'key', 'language_id']],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_plan_id' => 'edu_plan_id',
            'edu_year_id' => 'Edu Year ID',
            'edu_semester_id' => 'Edu Semester ID',
            'language_id' => 'Language ID',
            'capacity',

            'key',
            'faculty_id',
            'type',
            'description',
            'archived',
            'selected_count',

            // 'order' => _e('Order'),
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

            'name' => function ($model) {
                return $model->key . '(' . $this->language->lang_code . ')';
            },

            'capacity',
            'key',
            'faculty_id',
            'edu_plan_id',
            'selected_count',
            'edu_year_id',
            'edu_semester_id',
            'language_id',
            'type',
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

            'eduYear',
            'eduPlan',
            'faculty',
            'eduSemester',
            'language',
            'timeTables',
            'studentTimeOption',
            'studentTimeOptions',
            'selected',
            'selectedCount',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getStudentTimeOption()
    {
        return $this->hasMany(StudentTimeOption::className(), ['time_option_id' => 'id']);
    }

    public function getStudentTimeOptions()
    {
        return $this->hasMany(StudentTimeOption::className(), ['time_option_id' => 'id']);
    }

    public function getSelected()
    {
        if (isRole('student')) {

            $studentTimeOption = StudentTimeOption::find()
                ->where([
                    'time_option_id' => $this->id,
                    'student_id' => $this->student(),
                    'archived' => 0,
                ])
                ->all();

            if (count($studentTimeOption) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        // $studentTimeOption = StudentTimeOption::find()->where(['time_option_id' => $this->id])->all();
        return count($this->studentTimeOption);
    }

    public function getSelectedCount()
    {
        $studentTimeOption = StudentTimeOption::find()->where(['time_option_id' => $this->id])->all();
        return count($studentTimeOption);
        return count($this->studentTimeOption);
    }

    public function getTimeTables()
    {
        return $this->hasMany(TimeTable::className(), ['time_option_id' => 'id'])->onCondition(['parent_id' => null, 'is_deleted' => 0]);
    }

    /**
     * Gets query for [[faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getEduSemester()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semester_id']);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id'])->select(['name', 'lang_code']);
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

        $model->edu_plan_id = $model->eduSemester->edu_plan_id;
        $model->faculty_id = $model->eduPlan->faculty_id;

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

        $model->edu_plan_id = $model->eduSemester->edu_plan_id;
        $model->faculty_id = $model->eduPlan->faculty_id;

        if (isset($post['status'])) {
            TimeTable::updateAll(['status' => $post['status']], ['time_option_id' => $model->id]);
            $time_table_parent = TimeTable::findOne(['time_option_id' => $model->id, 'parent_id IS NOT NULL', 'lecture_id IS NOT NULL']);
            if ($time_table_parent) {
                TimeTable::updateAll(['status' => $post['status']], ['or', ['parent_id' => $time_table_parent->id], ['lecture_id' => $time_table_parent->id]]);
                TimeTable::updateAll(['time_option_id' => $model->id], ['or', ['parent_id' => $time_table_parent->id], ['lecture_id' => $time_table_parent->id]]);
            }
        }

        // $subquery = TimeTable::find()
        //     ->select(['parent_id', 'lecture_id'])
        //     ->where(['time_option_id' => $model->id, 'parent_id IS NOT NULL']);

        // TimeTable::updateAll(
        //     ['status' => $post['status']],
        //     ['or', ['time_option_id' => $model->id], ['in', 'parent_id', $subquery], ['in', 'lecture_id', $subquery]]
        // );

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
