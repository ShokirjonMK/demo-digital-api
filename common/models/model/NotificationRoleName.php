<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 * @property string $name in translate with description
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan[] $eduPlans
 */
class NotificationRoleName extends NotificationRole
{

    public static $selected_language = 'uz';

    use ResourceTrait;



    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->notification->translate->name ?? '';
            },
            'role',
            // 'order',
            // 'status',
            // 'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',

        ];

        return $fields;
    }
}
