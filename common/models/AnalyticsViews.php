<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "analytics_views".
 *
 * @property int $id
 * @property string|null $uid
 * @property string|null $type
 * @property string|null $value
 * @property string|null $referrer
 * @property string|null $status_code
 * @property string|null $created_on
 */
class AnalyticsViews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analytics_views';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_on'], 'safe'],
            [['status_code'], 'integer'],
            [['uid', 'value', 'referrer', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'type' => 'Type',
            'value' => 'Value',
            'referrer' => 'Referrer',
            'status_code' => 'Status code',
            'created_on' => 'Created On',
        ];
    }
}
