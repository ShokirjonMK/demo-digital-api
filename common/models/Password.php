<?php

namespace common\models;

/**
 * Change password.
 */
class Password extends \yii\db\ActiveRecord
{
    public $old_password;
    public $new_password;
    public $confirm_password;

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password'], 'string', 'min' => 4, 'max' => 50],
            [['old_password', 'new_password', 'confirm_password'], 'required'],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password']
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
            'old_password' => _e('Old password'),
            'new_password' => _e('New password'),
            'confirm_password' => _e('Confirm password'),
        ];
    }

    /**
     * Validate password
     *
     * @return mixed
     */
    public function checkPassword($user_id)
    {
        $output = false;
        $user = self::findOne(['id' => $user_id]);

        if ($user) {
            $password_hash = $user->password_hash;

            if (\Yii::$app->security->validatePassword($this->old_password, $password_hash)) {
                $output = true;
            } else {
                $output = _e('Old password is incorrect.');
            }
        } else {
            $output = _e('User not found.');
        }

        return $output;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($user_id)
    {
        $output['success'] = false;
        $output['message'] = false;
        $user = self::findOne(['id' => $user_id]);

        if ($user) {
            $password_hash = $user->password_hash;

            if (\Yii::$app->security->validatePassword($this->old_password, $password_hash)) {
                $user->password_hash = \Yii::$app->security->generatePasswordHash($this->new_password);
                $user->save(false);

                $output['success'] = true;
                $output['message'] = _e('Password has been updated.');
            } else {
                $output['message'] = _e('Old password is incorrect.');
            }
        } else {
            $output['message'] = _e('User not found.');
        }

        return $output;
    }
}
