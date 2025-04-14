<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class StudentTurniket extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_turniket';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [[
                'faculty_id',
                'course_id',
                'edu_type'
            ], 'integer'],
            [
                [
                    'turniket_department_id',
                    'turniket_department_name',
                    'turniket_department_parent_id'
                ], 'string',
                'max' => 255
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'faculty_id',
            'course_id',
            'edu_type',
            'turniket_department_id',
            'turniket_department_name',
            'turniket_department_parent_id'
        ];
    }
}
