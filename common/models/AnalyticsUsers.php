<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "analytics_users".
 *
 * @property int $id
 * @property string|null $uid
 * @property string|null $ip_address
 * @property string|null $country_code
 * @property string|null $uagent
 * @property string|null $ua_device
 * @property string|null $ua_os
 * @property string|null $ua_browser
 * @property string|null $created_on
 * @property string|null $updated_on
 */
class AnalyticsUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analytics_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_on', 'updated_on'], 'safe'],
            [['uid', 'ip_address', 'country_code', 'uagent', 'ua_device', 'ua_os', 'ua_browser'], 'string', 'max' => 255],
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
            'ip_address' => 'Ip Address',
            'country_code' => 'Country Code',
            'uagent' => 'Uagent',
            'ua_device' => 'Device',
            'ua_os' => 'Ua Os',
            'ua_browser' => 'Ua Browser',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
        ];
    }
}
