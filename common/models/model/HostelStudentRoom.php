<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Codeception\Command\Build;
use common\models\enums\Gender;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hostel_student_room}}".
 *
 * @property int $id
 * @property int|null $room_id
 * @property int|null $student_id
 * @property int|null $faculty_id
 * @property int|null $edu_year_id
 * @property int|null $edu_plan_id
 * @property float|null $payed
 * @property int|null $is_contract
 * @property int|null $is_free
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property EduPlan $eduPlan
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Room $room
 * @property Student $student
 */
class HostelStudentRoom  extends \yii\db\ActiveRecord
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
        return '{{%hostel_student_room}}';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['room_id', 'building_id', 'student_id', 'archived', 'faculty_id', 'edu_year_id', 'edu_plan_id', 'is_contract', 'is_free', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['room_id', 'student_id',], 'required'],
            [['payed'], 'double'],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            // [['student_id', 'edu_year_id', 'is_deleted'], 'unique', 'targetAttribute' => ['student_id', 'edu_year_id', 'is_deleted']],
            // [['student_id', 'edu_year_id'], 'unique', 'targetAttribute' => ['student_id', 'edu_year_id'], 'filter' => ['is_deleted' => 0]],
            /**
             * Ensures that for each record with `is_deleted` set to 0 (i.e., not deleted),
             * the combination of `student_id` and `edu_year_id` is unique.
             */
            [
                ['student_id', 'edu_year_id'],
                'unique',
                'targetAttribute' => ['student_id', 'edu_year_id'],
                'filter' => ['is_deleted' => 0]
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'Room ID',
            'building_id' => 'Building ID',
            'student_id' => 'Student ID',
            'faculty_id' => 'Faculty ID',
            'edu_year_id' => 'Edu Year ID',
            'edu_plan_id' => 'Edu Plan ID',
            'payed' => 'Payed',
            'is_contract' => 'Is Contract',
            'is_free' => 'Is Free',
            'order' => _e('Order'),
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
            'room_id',
            'building_id',
            'student_id',
            'faculty_id',
            'edu_year_id',
            'edu_plan_id',
            'payed',
            'is_contract',
            'is_free',

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
            'eduPlan',
            'eduYear',
            'faculty',
            'room',
            'student',
            'building',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
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
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery|RoomQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    /**
     * Gets query for [[building_id]].
     *
     * @return \yii\db\ActiveQuery|RoomQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
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
     * HostelStudentRoom createItem <$model, $post>
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

        $model->faculty_id = $model->student->faculty_id;
        $model->edu_plan_id = $model->student->edu_plan_id;
        $model->building_id = $model->room->building_id;
        $model->is_contract = $model->student->is_contract;

        if (!isset($post['edu_year_id'])) {
            $eduYear = EduYear::findOne(['status' => 1], ['order' => ['id' => SORT_DESC]]);
            if ($eduYear !== null) {
                $model->edu_year_id = $eduYear->id;
            }
        }

        if ($model->room->type != Room::TYPE_HOSTEL) {
            $errors[] = _e('This room is not for hostel');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->room->gender != $model->student->gender) {
            $errors[] = _e('This room is for ') . Gender::list()[$model->room->gender];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $forhisYearCapacity = self::find()->where([
            'is_deleted' => 0,
            'room_id' => $model->room_id,
            'edu_year_id' => $model->edu_year_id,
            'archived' => 0,
        ])->count();

        if (!$model->room->capacity > $forhisYearCapacity) {
            $errors[] = _e('This room capacity is full');
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
     * HostelStudentRoom updateItem <$model, $post>
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

        $model->faculty_id = $model->student->faculty_id;
        $model->edu_plan_id = $model->student->edu_plan_id;
        $model->is_contract = $model->student->is_contract;
        $model->building_id = $model->room->building_id;

        if (!isset($post['edu_year_id'])) {
            $eduYear = EduYear::findOne(['status' => 1], ['order' => ['id' => SORT_DESC]]);
            if ($eduYear !== null) {
                $model->edu_year_id = $eduYear->id;
            }
        }

        if ($model->room->type != Room::TYPE_HOSTEL) {
            $errors[] = _e('This room is not for hostel');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->room->gender != $model->student->gender) {
            $errors[] = _e('This room is for ') . Gender::list()[$model->room->gender];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $forhisYearCapacity = self::find()->where([
            'is_deleted' => 0,
            'room_id' => $model->room_id,
            'edu_year_id' => $model->edu_year_id,
            'archived' => 0,
        ])->count();

        if (!$model->room->capacity > $forhisYearCapacity) {
            $errors[] = _e('This room capacity is full');
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

    // public function beforeSave($insert)
    // {
    //     if ($insert) {
    //         $this->created_by = current_user_id();
    //     } else {
    //         $this->updated_by = current_user_id();
    //     }
    //     return parent::beforeSave($insert);
    // }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
            // Increment the empty_count of the related room
            $room = Room::findOne($this->room_id);
            if ($room !== null) {
                $room->empty_count++;
                $room->save();
            }
        } else {
            $this->updated_by = current_user_id();
            $room = Room::findOne($this->room_id);
            if ($room !== null) {
                --$room->empty_count;
                $room->save();
            }
        }

        return parent::beforeSave($insert);
    }
}
