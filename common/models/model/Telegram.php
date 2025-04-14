<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/* This is the model class for table "{{%telegram}}".
 *
 * @property int $id
 * @property string|null $phone
 * @property string|null $username
 * @property string|null $password
 * @property int|null $chat_id
 * @property int|null $step
 * @property int|null $user_id User ID
 * @property int|null $order Order of the item
 * @property int|null $status Status of the item (1 = active, 0 = inactive)
 * @property int|null $is_deleted Is the item deleted (0 = no, 1 = yes)
 * @property int $created_at Creation timestamp
 * @property int $updated_at Update timestamp
 * @property int $created_by ID of the user who created the record
 * @property int $updated_by ID of the user who last updated the record
 *
 * @property Users $user
 */

class Telegram  extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

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
        return 'telegram';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_id', 'lang_id', 'step', 'user_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['phone', 'lang', 'telegram_username', 'username', 'password'], 'string', 'max' => 255],
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
            'phone' => _e('Phone'),
            'username' => _e('Username'),
            'password' => _e('Password'),
            'chat_id' => _e('Chat ID'),
            'step' => _e('Step'),
            'user_id' => _e('User ID'),
            'order' => _e('Order of the item'),
            'status' => _e('Status of the item (1 = active, 0 = inactive)'),
            'is_deleted' => _e('Is the item deleted (0 = no, 1 = yes)'),
            'created_at' => _e('Creation timestamp'),
            'updated_at' => _e('Update timestamp'),
            'created_by' => _e('ID of the user who created the record'),
            'updated_by' => _e('ID of the user who last updated the record'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'phone',
            'username',
            'password',
            'chat_id',
            'step',
            'user_id',
            'order',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'user',

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
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Telegram createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * Telegram updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * {@inheritdoc}
     * @return TelegramQuery the active query used by this AR class.
     */
    // public static function find()
    // {
    //     return new TelegramQuery(get_called_class());
    // }
    public static function findByChatId($condition)
    {
        // If the condition is not an array, assume it's a chat_id
        if (!is_array($condition)) {
            $condition = ['chat_id' => $condition];
        }

        return static::findOne($condition);
    }



    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
