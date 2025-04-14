<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%turniket_data}}".
 *
 * @property int $id
 * @property int $user_id User ID
 * @property int $turniket_id Turniket ID
 * @property string|null $date Date 
 * @property string|null $data json data
 * @property int $created_at 
 * @property string|null $key key
 * @property int|null $type type
 */
class TurniketData extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%turniket_data}}';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            // [['user_id', 'turniket_id'], 'required'],
            [['user_id', 'turniket_id', 'in_out', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['date', 'time', 'reader', 'data'], 'safe'],
            [['key', 'passport_pin'], 'string', 'max' => 255],
            [['created_by', 'updated_by'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('User ID'),
            'turniket_id' => _e('Turniket ID'),
            'date' => _e('Date'),
            'data' => _e('statistics json data'),
            'created_at' => _e('Created At'),
            'key' => _e('key'),
            'type' => _e('type'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'turniket_id',
            'date',
            'data',
            'created_at',
            'key',
            'type',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'user',
            'profile',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['turniket_id' => 'turniket_id']);
    }
}
