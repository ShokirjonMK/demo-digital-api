<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 * @property string $name in translate with description
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan[] $eduPlans
 */
class NotificationUser extends \yii\db\ActiveRecord
{

    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_APPROVED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['user_id', 'notification_role_id', 'notification_id'], 'integer'],
            //            [['name'], 'string', 'max' => 255],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notification::className(), 'targetAttribute' => ['notification_id' => 'id']],
            [['notification_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotificationRole::className(), 'targetAttribute' => ['notification_role_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            //            'name' => 'Name',
            'notification_role_id' => 'Notification Role Id',
            'notification_id' => 'Notification Id',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'notification' => function ($model) {
                return $model->notification->translate->name ?? '';
            },
            // 'user_id',
            // 'notification_role_id',
            'notification_id',
            'order',
            'status',
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
            'statusName',
            'user',
            // 'notification',
            'notificationRole',
            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Notification]].
     * notification
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::className(), ['id' => 'notification_id']);
    }

    public function getDescription()
    {
        return $this->notification->translate->description ?? "";
    }

    /**
     * Gets query for [[NotificationRole]].
     * notification
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationRole()
    {
        return $this->hasOne(NotificationRole::className(), ['id' => 'notification_role_id']);
    }

    public function getStatusName()
    {
        return   $this->statusList()[$this->status];
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $new = new NotificationUser();
        $new->user_id = current_user_id();
        $new->notification_id = $model->id;
        // $new->notification_role_id = $model->notification_role_id;
        $new->status = self::STATUS_APPROVED;


        if (!($new->validate())) {
            $errors[] = $new->errors;
        }

        if ($new->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => 'STATUS_INACTIVE',
            self::STATUS_ACTIVE => 'STATUS_ACTIVE',
            self::STATUS_APPROVED => 'STATUS_APPROVED',
        ];
    }
}
