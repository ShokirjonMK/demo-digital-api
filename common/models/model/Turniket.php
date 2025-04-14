<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%turniket}}".
 *
 * @property int $id
 * @property int|null $user_id User ID
 * @property int|null $turniket_id User ID
 * @property string|null $date Date
 * @property int|null $go_in_time Go in time
 * @property int|null $go_out_time Go out time
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property User $user
 */
class Turniket extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const TYPE_OQUV = 1;
    const TYPE_TTJ = 2;

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
        return '{{%turniket}}';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['user_id', 'type', 'turniket_id', 'go_in_time', 'go_out_time', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['date'], 'safe'],
            [['passport_pin'], 'string', 'max' => 255],

            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'turniket_id' => _e('User ID'),
            'date' => _e('Date'),
            'go_in_time' => _e('Go in time'),
            'go_out_time' => _e('Go out time'),
            'order' => _e('Order'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'archived' => _e('Archived'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'turniket_id',
            'date',
            'go_in_time',
            'go_out_time',
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
            'user',
            'profile',
            'turniketData',
            'timeOn',
            'goInTime',
            'goOutTime',

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
    public function getTimeOn()
    {
        return date("Y-m-d H:i:s", strtotime($this->go_in_time));
    }
    public function getGoInTime()
    {
        return date("Y-m-d H:i:s", $this->go_in_time);
    }
    public function getGoOutTime()
    {
        if ($this->go_out_time === null) {
            return null;
        }
        return date("Y-m-d H:i:s", $this->go_out_time);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['turniket_id' => 'turniket_id']);
    }

    public function getTurniketData()
    {
        return $this->hasMany(TurniketData::className(), ['turniket_id' => 'turniket_id', 'date' => 'date']);
    }

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
