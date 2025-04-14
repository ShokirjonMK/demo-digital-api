<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%student_time_option}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int $user_id
 * @property int $time_option_id
 * @property int $edu_year_id
 * @property int $faculty_id
 * @property int $edu_plan_id
 * @property int $edu_semester_id
 * @property int $language_id
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int|null $order
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class StudentTimeOption extends \yii\db\ActiveRecord
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
        return 'student_time_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                // 'student_id',
                // 'user_id',
                'time_option_id',
                // 'edu_year_id'
            ], 'required'],

            [[
                'student_id',
                'user_id',
                'time_option_id',
                'edu_year_id',
                'faculty_id',
                'edu_plan_id',
                'edu_semester_id',
                'language_id',
                'status',
                'is_deleted',
                'order',
                'created_at',
                'updated_at',
                'created_by',
                'archived',
                'updated_by'
            ], 'integer'],

            [
                ['student_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']
            ],
            [
                ['user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['time_option_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TimeOption::className(), 'targetAttribute' => ['time_option_id' => 'id']
            ],
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

            [['student_id'], 'unique', 'targetAttribute' => ['student_id', 'time_option_id'], 'message' => 'You have already selected Time Option'],
            // [['student_id'], 'unique', 'targetAttribute' => ['student_id', 'edu_year_id'], 'message' => 'You have already selected Time Option for this Edu Year'],
            [['student_id', 'edu_year_id'], 'validateUniqueWithArchived', 'on' => 'insert'],

        ];
    }
    public function validateUniqueWithArchived($attribute, $params)
    {
        // Check if a record with the same student_id and edu_year_id and archived = 0 exists
        $exists = self::find()
            ->where(['student_id' => $this->student_id, 'edu_year_id' => $this->edu_year_id, 'archived' => 0])
            ->exists();

        // If such a record exists, add an error
        if ($exists) {
            $this->addError($attribute, 'You have already selected Time Option for this Edu Year.');
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id',
            'user_id',
            'time_option_id',
            'edu_year_id',
            'faculty_id',
            'edu_plan_id',
            'edu_semester_id',
            'language_id',
            'archived',

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
        $fields = [
            'id',
            'student_id',
            'user_id',
            'time_option_id',
            'edu_year_id',

            'faculty_id',
            'edu_plan_id',
            'edu_semester_id',
            'language_id',

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
        $extraFields = [

            'student',
            'user',
            'timeOption',
            'eduYear',
            'faculty',
            'eduPlan',
            'eduSemester',
            'language',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

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

        if (
            $model->timeOption->capacity <= self::find()
            ->where(['time_option_id' => $model->time_option_id, 'is_deleted' => 0])
            ->count()
        ) {
            $errors[] = _e('This Time Option is Full!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // // Increment the selected count and save the time option
        // $model->timeOption->selected += 1;
        // $model->timeOption->save();

        // if ($model->timeOption->capacity <= $model->timeOption->selected) {
        //     $errors[] = _e('This Time Option is Full!');
        //     return simplify_errors($errors);
        // }





        // if ($model->timeOption->capacity < $model->timeOption->selected_count) {
        //     $errors[] = _e('This Time Option is Full!');
        //     return simplify_errors($errors);
        // } else {
        //     $model->timeOption->selected_count += 1;
        //     $model->timeOption->save(false);
        // }
        // $model->timeOption->selected_count += 1;
        // if ($model->timeOption->save()) {
        //     if ($model->timeOption->capacity <= $model->timeOption->selected_count) {
        //         $errors[] = _e('This Time Option is Full!');
        //         $model->timeOption->selected_count -= 1; // Rollback the increment
        //         $model->timeOption->save(); // Save the updated selected
        //         return simplify_errors($errors);
        //     }
        // } else {
        //     $errors[] = _e('Failed to update selected count.');
        //     return simplify_errors($errors);
        // }

        $model->edu_plan_id = $model->timeOption->edu_plan_id;
        $model->faculty_id = $model->timeOption->faculty_id;
        $model->language_id = $model->timeOption->language_id;
        $model->edu_semester_id = $model->timeOption->edu_semester_id;
        $model->edu_year_id = $model->timeOption->edu_year_id;
        $model->user_id = current_user_id();
        $model->student_id = self::student();

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        /** faqat 2,3,4 kurslar uchun */
        // if (!in_array($model->student->course_id, [1])) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }
        // if (!in_array($model->student->faculty_id, [5])) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }


        // if ($model->timeOption->capacity < $model->timeOption->selected_count) {
        //     $errors[] = _e('This Time Option is Full!');
        //     $transaction->rollBack();

        //     return simplify_errors($errors);
        // } else {
        //     $model->timeOption->selected_count += 1;
        //     $model->timeOption->save(false);
        // }

        if ($model->save()) {

            $timeTableParentNull = TimeTable::find()
                ->where([
                    'is_deleted' => 0,
                    'time_option_id' => $model->time_option_id,
                    'subject_category_id' => 1,
                    'parent_id' => null
                ])
                ->andWhere(['!=', 'subject_category_id', 2])
                ->all();

            foreach ($timeTableParentNull as $timeTableParentNullOne) {
                $studentTimeTableNew = new StudentTimeTable();
                $studentTimeTableNew->time_table_id = $timeTableParentNullOne->id;
                $studentTimeTableNew->time_option_id = $model->time_option_id;
                $studentTimeTableNew->student_time_option_id = $model->id;
                $result = StudentTimeTable::createItemForOption($studentTimeTableNew);
                if (is_array($result)) {
                    $errors[] = $result;
                }
            }

            $model->timeOption->selected_count += 1;
            $model->timeOption->save(false);

            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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

    public static function deleteItem(StudentTimeOption $model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $studentTimeTables = StudentTimeTable::find()
            ->where(['time_option_id' => $model->id])
            ->andWhere(['is', 'time_table_parent_id', null])
            ->all();

        foreach ($studentTimeTables as $studentTimeTable) {
            if (!$studentTimeTable->deleteItem()) {
                $errors[] = $studentTimeTable->errors;
            }
        }

        if (StudentTimeTable::find()->where(['student_time_option_id' => $model->id])->exists()) {
            if (!StudentTimeTable::deleteAll(['student_time_option_id' => $model->id])) {
                $errors[] = 'StudentTimeTable not deleted';
            }
        }

        if (!$model->delete()) {
            $errors[] = 'StudentTimeOption not deleted';
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $timeOption = $model->timeOption;
            if ($timeOption !== null) {
                $timeOption->updateCounters(['selected_count' => -1]);
            }
            $transaction->commit();
            return true;
        }
    }


    public static function deleteItemWithRels($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        foreach (StudentTimeTable::findAll(['student_time_option_id' => $model->id]) as $row) {
            if ($row->delete()) {
                $errors[] = $row->errors;
            }
        }

        if (count($errors) > 0) {
            $errors[] = "Nothing is deleted";
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            if ($model->delete()) {
                $transaction->commit();
                return true;
            } else {
                $errors[] = "Nothing is deleted";
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        // Decrement the selected_count of the related time option
        $timeOption = $this->timeOption;
        if ($timeOption !== null) {
            $timeOption->updateCounters(['selected_count' => -1]);
        }

        // // Decrement the selected_count of the related time option
        // $timeOption = $this->timeOption;
        // if ($timeOption !== null) {
        //     $timeOption->selected_count -= 1;
        //     $timeOption->save();
        // }
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
