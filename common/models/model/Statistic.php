<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%statistic}}".
 *
 * @property int $id
 * @property string|null $data statistics json data
 * @property string|null $key statistics key
 * @property int|null $type statistics type
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 */
class Statistic extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'statistic';
    }

    public function rules()
    {
        return [
            [['data', 'date'], 'safe'],
            [['type', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['key'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => _('ID'),
            'data' => _('statistics json data'),
            'date' => _('date'),
            'key' => _('statistics key'),
            'type' => _('statistics type'),
            'order' => _('Order'),
            'status' => _('Status'),
            'is_deleted' => _('Is Deleted'),
            'created_at' => _('Created At'),
            'updated_at' => _('Updated At'),
            'created_by' => _('Created By'),
            'updated_by' => _('Updated By'),
            'archived' => _('Archived'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'data',
            'date',
            'key',
            'type',
            'text',
            'order',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'archived',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public static function createItem($model, $post) {}

    public static function updateItem($model, $post) {}



    // public function beforeSave($insert)
    // {
    //     if ($insert) {
    //         $this->created_by = Current_user_id();
    //     } else {
    //         $this->updated_by = Current_user_id();
    //     }
    //     return parent::beforeSave($insert);
    // }
}
