<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property int|null $education_direction_id
 * @property int|null $basis_of_learning
 * @property int|null $education_type
 * @property string|null $diploma_number
 * @property string|null $diploma_date
 * @property int|null $type_of_residence
 * @property string|null $landlord_info
 * @property string|null $student_live_with
 * @property string|null $other_info
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'department_id', 'gender', 'education_direction_id', 'basis_of_learning', 'education_type', 'type_of_residence'], 'integer'],
            [['diploma_date'], 'safe'],
            [['landlord_info', 'student_live_with', 'other_info'], 'string'],
            [['diploma_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {

        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'department_id' => Yii::t('app', 'Department'),
            'education_direction_id' => Yii::t('app', 'Education speciality'),
            'basis_of_learning' => _e('Basis of learning'),
            'education_type' => Yii::t('app', 'Education type'),
            'diploma_number' => _e('Diploma number'),
            'diploma_date' => _e('Date of issue of the diploma'),
            'type_of_residence' => _e('Type of residence'),
            'landlord_info' => Yii::t('app', 'Landlord info'),
            'student_live_with' => Yii::t('app', 'Student live with'),
            'other_info' => Yii::t('app', 'Other informations (student)'),
        ];
    }

    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }
}
