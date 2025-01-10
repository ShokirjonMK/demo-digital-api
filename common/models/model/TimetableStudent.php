<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "week".
 *
 * @property int $id
 * @property int $time_table_id
 * @property int $group_id
 * @property int $student_id
 * @property int $type
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property TimeTable1[] $timeTables
 */
class TimetableStudent extends \yii\db\ActiveRecord
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
        return 'timetable_student';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['ids_id','group_id', 'student_id'], 'required'],
            [
                [

                    'ids_id',
                    'group_id',

                    'student_id',
                    'student_user_id',

                    'group_type',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                ],
                'integer'
            ],
            [
                ['ids_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['ids_id' => 'id']
            ],
            [
                ['group_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']
            ],
            [
                ['student_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']
            ],
            [
                ['student_user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']
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
            //            'name' => 'Name',
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
            'ids_id',
            'group_id',
            'student_id',
            'student_user_id',

            'group_type',
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
            'group',
            'profile',
            'student',
            'user',
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

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'student_user_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => $this->student_user_id]);
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
