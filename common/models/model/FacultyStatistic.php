<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\enums\Gender;
use Yii;

class FacultyStatistic extends Faculty
{
    public static $selected_language = 'uz';


    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'studentsCount' => function ($model) {
                return $model->studentsCount ?? 0;
            },

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'studentsCountByGender',
            'studentsAll',
            'genderMale',
            'genderFemale',


            'teachers',
            'teachersCount',


            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getStudentsAll()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id'])->onCondition(['!=', 'course_id', 9]);
    }

    public function getStudentsGraduated()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id'])->onCondition(['course_id' => 9]);;
    }

    public function getStudentsCountByGender()
    {
        // return "asss";
        return [
            Gender::MALE => [
                'count' => count($this->genderMale),
                'name' =>  _e('MALE')
            ],
            Gender::FEMALE => [
                'count' => count($this->genderFemale),
                'name' =>  _e('FEMALE')
            ],

        ];
    }


    public function getGenderMale()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id'])->andOnCondition(['!=', 'course_id', 9])->andWhere(['gender' => Gender::MALE]);
    }

    public function getGenderFemale()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id'])->andOnCondition(['!=', 'course_id', 9])->andWhere(['gender' => Gender::FEMALE]);
    }


    public function getStudentsCount()
    {
        return count($this->studentsAll);
    }

    public function getStudentsCountGraduated()
    {
        return count($this->studentsGraduated);
    }

    public function getTeachers()
    {
        $model = new User();
        $query = $model->find()
            ->join(
                'INNER JOIN',
                'auth_assignment',
                'auth_assignment.user_id = users.id'
            )
            ->join(
                'INNER JOIN',
                'user_access',
                'user_access.user_id = users.id'
            );

        $query = $query->andWhere(['user_access.user_access_type_id' => self::USER_ACCESS_TYPE_ID]);
        $query = $query->andWhere(['user_access.table_id' => $this->id]);
        $query = $query->andWhere(['in', 'auth_assignment.item_name', ['teacher', 'mudir']]);
        return $query->all();
    }

    public function getTeachersCount()
    {
        return count($this->teachers);
    }
}
