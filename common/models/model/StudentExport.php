<?php

namespace common\models\model;


class StudentExport extends Student
{

    public function fields()
    {
        $fields =  [
            'id',


            'eduPlan' => function ($model) {
                return $model->eduPlan->name ?? '';
            },
            'faculty' => function ($model) {
                return $model->faculty->name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'middle_name' => function ($model) {
                return $model->profile->middle_name ?? '';
            },
            'eduLang' => function ($model) {
                return $model->eduLang->name ?? '';
            },

            'user_id'
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'course',
            'direction',
            'eduType',
            'eduYear',
            'eduForm',
            'eduLang',
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

            'socialCategory',
            'residenceStatus',
            'categoryOfCohabitant',
            'studentCategory',


            'usernamePass',
            'username',
            'password',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
}
