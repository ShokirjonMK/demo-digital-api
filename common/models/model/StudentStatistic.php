<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;


class StudentStatistic extends Student
{
    use ResourceTrait;


    // public function fields()
    // {
    //     $fields =  [
    //         'id',
    //         'gender',
    //         // 'gender' => function ($model) {
    //         //     return $model->profile->gender ?? '';
    //         // },


    //     ];
    //     return $fields;
    // }

    public function extraFields()
    {
        $extraFields =  [
            'course',
            'direction',
            'eduType',
            'eduYear',
            'eduForm',
            'faculty',
            'user',
            'tutor',
            'profile',
            'eduPlan',
            'citizenship',
            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',
            'nationality',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    // Profile Citizenship
    public function getCitizenship()
    {
        return Citizenship::findOne($this->profile->citizenship_id) ?? null;
    }

    // getCountry
    public function getCountry()
    {
        return Countries::findOne($this->profile->country_id) ?? null;
    }

    // getRegion
    public function getRegion()
    {
        return Region::findOne($this->profile->region_id) ?? null;
    }

    // getArea
    public function getArea()
    {
        return Area::findOne($this->profile->area_id) ?? null;
    }

    // getPermanentCountry
    public function getPermanentCountry()
    {
        return Countries::findOne($this->profile->permanent_country_id) ?? null;
    }

    // getPermanentRegion
    public function getPermanentRegion()
    {
        return Region::findOne($this->profile->permanent_region_id) ?? null;
    }

    // getPermanentArea
    public function getPermanentArea()
    {
        return Area::findOne($this->profile->permanent_area_id) ?? null;
    }

    // getNationality
    public function getNationality()
    {
        return Nationality::findOne($this->profile->nationality_id) ?? null;
    }


    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }
    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[EduForm]].
     *edu_form_id
     * @return \yii\db\ActiveQuery
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
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

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Tutor]].
     * tutor_id
     * @return \yii\db\ActiveQuery
     */
    public function getTutor()
    {
        return $this->hasOne(User::className(), ['id' => 'tutor_id']);
    }
}
