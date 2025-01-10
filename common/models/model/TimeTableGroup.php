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
class TimeTableGroup extends \yii\db\ActiveRecord
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
        return 'time_table_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time_table_id','group_id', 'student_id'], 'required'],
            [
                [

                    'time_table_id',
                    'time_table_ids',
                    'group_id',

                    'edu_plan_id',
                    'teacher_user_id',
                    'subject_id',
                    'subject_category_id',
                    'edu_semestr_id',
                    'building_id',
                    'room_id',
                    'week_id',
                    'para_id',
                    'edu_year_id',
                    'semestr_id',
                    'course_id',
                    'language_id',
                    'teacher_access_id',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'archived',
                ],
                'integer'
            ],
            [['is_deleted_date'], 'safe'],
            [
                ['time_table_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TimeTable1::className(), 'targetAttribute' => ['time_table_id' => 'id']
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
                ['edu_plan_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']
            ],
            [
                ['teacher_user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']
            ],
            [
                ['subject_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']
            ],
            [
                ['subject_category_id'], 'exist',
                'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']
            ],
            [
                ['edu_semestr_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']
            ],
            [
                ['building_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']
            ],
            [
                ['room_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']
            ],
            [
                ['week_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Week::className(), 'targetAttribute' => ['week_id' => 'id']
            ],
            [
                ['para_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']
            ],
            [
                ['edu_year_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [
                ['semestr_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['semestr_id' => 'id']
            ],
            [
                ['course_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']
            ],
            [
                ['language_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']
            ],
            [
                ['teacher_access_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']
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
            'time_table_id',
            'group_id',
            'student_id',
            'edu_plan_id',
            'teacher_user_id',
            'subject_id',
            'subject_category_id',
            'edu_semestr_id',
            'building_id',
            'room_id',
            'week_id',
            'para_id',
            'edu_year_id',
            'semestr_id',
            'course_id',
            'language_id',
            'teacher_access_id',
            'type',
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
            'timeTables',
            'group',
            'profile',
            'student',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }



    public function getTimeTables()
    {
        return $this->hasOne(TimeTable1::className(), ['id' => 'time_table_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => $this->student->user_id]);
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
