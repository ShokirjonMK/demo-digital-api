<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ips_service".
 *
 * @property int $id
 * @property string|null $service_name
 * @property string|null $url
 * @property string|null $function
 * @property string|null $comment
 * @property int|null $is_working
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class IpsService extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function status($index = null)
    {
        $result = [
          self::STATUS_ACTIVE => 'Active',
          self::STATUS_INACTIVE => 'Inactive',
        ];
        return $index ? $result[$index] : $result;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ips_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_working', 'status', 'created_at', 'updated_at'], 'integer'],
            [['service_name', 'url', 'function'], 'string', 'max' => 255],
            [['comment'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_name' => 'Service Name',
            'url' => 'Url',
            'function' => 'Function',
            'comment' => 'Comment',
            'is_working' => 'Is Working',
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
        ];
    }
}
