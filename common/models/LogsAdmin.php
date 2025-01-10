<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logs_admin".
 *
 * @property int $id
 * @property int $user_id
 * @property int $res_id
 * @property string|null $action
 * @property string|null $type
 * @property string|null $data
 * @property int $created_on
 */
class LogsAdmin extends \yii\db\ActiveRecord
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'logs_admin';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['created_on'], 'safe'],
            [['action', 'data', 'type'], 'string'],
            [['user_id', 'res_id'], 'integer'],
            [['user_id', 'action', 'type'], 'required'],
            [['res_id'], 'default', 'value' => 0],
            [['data'], 'default', 'value' => ''],
            [['created_on'], 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'action' => 'Action',
            'type' => 'Type',
            'data' => 'Data',
            'created_on' => 'Created date',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
