<?php

namespace common\models\model;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int $user_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $course_id
 * @property int $edu_year_id
 * @property int $edu_type_id
 * @property int $social_category_id
 * @property int $residence_status_id
 * @property int $category_of_cohabitant_id
 * @property int $student_category_id
 * @property int $is_contract
 * @property string $diplom_number
 * @property string $diplom_seria
 * @property string $diplom_date
 * @property string $description
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property Direction $direction
 * @property EduType $eduType
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Users $user
 */
class StudentPinn extends Student
{
    public function fields()
    {
        $fields =  [
            'id',

            'user_id',
            // 'type ',
            'status',
            'user_status' => function ($model) {
                return $this->user->status;
            },
            'photo' => function ($model) {
                return $model->profile->image ?? '';
            },

        ];
        return $fields;
    }
}
