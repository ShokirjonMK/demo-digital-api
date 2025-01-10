<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "analytics_sessions".
 *
 * @property int $id
 * @property string|null $uid
 * @property string|null $session_key
 * @property string|null $created_on
 * @property string|null $updated_on
 */
class AnalyticsSessions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analytics_sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_on', 'updated_on'], 'safe'],
            [['uid', 'session_key'], 'string', 'max' => 255],
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
            'session_key' => 'Session Key',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
        ];
    }
}
