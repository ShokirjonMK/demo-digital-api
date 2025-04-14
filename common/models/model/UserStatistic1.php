<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Profile;
use common\models\model\TeacherAccess;
use common\models\model\UserAccessType;

class UserStatistic1 extends User
{
    use ResourceTrait;

    public function fields()
    {
        $fields = [
            'id',
            // 'username',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'middle_name' => function ($model) {
                return $model->profile->middle_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'teacherAccess' => function ($model) {
                return $model->teacherAccess ?? [];
            },


            // 'status',
            // 'deleted'

        ];

        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */
    public function extraFields()
    {
        $extraFields = [
            'created_at',
            'updated_at',
            'profile',
            'userAccess',
            'department',
            'here',

            'roles',
            'rolesAll',

            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',


            'teacherAccess',

        ];

        return $extraFields;
    }


    public function getTeacherAccess()
    {
        return $this->hasMany(TeacherAccessStatistic1::className(), ['user_id' => 'id']);
    }
}
