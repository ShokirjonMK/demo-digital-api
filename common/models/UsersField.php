<?php
namespace common\models;

/**
 * This is the model class for table "users_field".
 *
 * @property int $field_id
 * @property int $user_id
 * @property string $field_key
 * @property string $field_value
 */
class UsersField extends \yii\db\ActiveRecord
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'users_field';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['user_id', 'field_key'], 'required'],
            [['field_key', 'field_value'], 'string'],
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
            'field_id' => 'Field ID',
            'user_id' => 'User ID',
            'field_key' => 'Field Key',
            'field_value' => 'Field Value',
        ];
    }
}
