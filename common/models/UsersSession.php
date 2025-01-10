<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users_session".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $last_ip
 * @property string|null $last_login
 * @property string|null $last_session
 * @property string|null $history
 *
 * @property User $user
 */
class UsersSession extends \yii\db\ActiveRecord
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'users_session';
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
            [['last_ip', 'last_login', 'last_session', 'history'], 'string'],
            [['last_login'], 'default', 'value' => date('Y-m-d H:i:s')],
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
            'user_id' => _e('User ID'),
            'last_ip' => _e('Last IP'),
            'last_login' => _e('Last login'),
            'last_session' => _e('Last session'),
            'history' => _e('History'),
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

    /**
     * Set log
     *
     * @param integer $userid
     * @return mixed
     */
    public static function getLog($userid = null, $field = null)
    {
        $output = array();

        if (is_numeric($userid) && $userid > 0) {
            $user_id = $userid;
        } else {
            $user_id = Yii::$app->user->getId();
        }

        if (is_numeric($user_id) && $user_id > 0) {
            $row = self::find()
                ->where(['user_id' => $user_id])
                ->one();

            if ($row) {
                $output['last_ip'] = $row->last_ip;
                $output['last_login'] = $row->last_login;
                $output['last_session'] = json_decode($row->last_session);
                $output['history'] = json_decode($row->history);

                if ($field) {
                    $output = $output[$field];
                }
            }
        }

        return $output;
    }

    /**
     * Set log
     *
     * @param integer $userid
     * @return void
     */
    public static function setLog($userid = null)
    {
        if (is_numeric($userid) && $userid > 0) {
            $user_id = $userid;
        } else {
            $user_id = Yii::$app->user->getId();
        }

        if (is_numeric($user_id) && $user_id > 0) {
            $request = Yii::$app->request;
            $ip_address = $request->userIP;
            $referrer = $request->referrer;

            $session_data = getBrowser();
            $session_data['ip_address'] = $ip_address;
            $session_data['referrer'] = $referrer;
            $session_data['date'] = date('Y-m-d H:i:s');

            $row = self::find()
                ->where(['user_id' => $user_id])
                ->one();

            if ($row) {
                $old_history = array();
                $row->last_ip = $ip_address;
                $row->last_login = date('Y-m-d H:i:s');
                $row->last_session = json_encode($session_data);

                if (!is_null($row->history) && $row->history) {
                    $old_history = json_decode($row->history, true);
                }

                if ($old_history) {
                    $history[] = $session_data;
                    $history = array_merge($history, $old_history);
                } else {
                    $history[] = $session_data;
                }

                $row->history = json_encode(array_slice($history, 0, 100));
                $row->save(false);
            } else {
                $history[] = $session_data;

                $model = new UsersSession();
                $model->user_id = $user_id;
                $model->last_ip = $ip_address;
                $model->last_login = date('Y-m-d H:i:s');
                $model->last_session = json_encode($session_data);
                $model->history = json_encode($history);

                $model->save(false);
            }
        }
    }
}
