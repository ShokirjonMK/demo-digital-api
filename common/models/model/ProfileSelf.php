<?php

namespace common\models\model;

use api\resources\User;
use Yii;

class ProfileSelf extends Profile
{
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            // 'checked',
            'checked_full',
            'last_in',
            'image',
            'last_name',
            'first_name',
            'middle_name',
            'passport_seria',
            'passport_number',
            'passport_pin',
            'passport_given_date',
            'passport_issued_date',
            'passport_given_by',
            'birthday',
            'phone',
            'phone_secondary',
            'passport_file',
            'country_id',
            'is_foreign',
            'region_id',
            'area_id',
            'address',
            'gender',
            'permanent_country_id',
            'permanent_region_id',
            'permanent_area_id',
            'permanent_address',
            'order',
            'status',
            'description',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
            'citizenship_id',
            'nationality_id',
            'telegram_chat_id',
            'diploma_type_id',
            'degree_id',
            'academic_degree_id',
            'degree_info_id',
            'partiya_id',
            'has_disability',
            'orcid',
            'turniket_id',
            'turniket_status',

        ];

        return $fields;
    }
}
