<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "building".
 *
 * @property int $id
 * @property int $user_id
 * @property int $user_access_id
 * @property int $work_load_id
 * @property int $work_rate_id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Room[] $rooms
 */
class LoadRate extends \yii\db\ActiveRecord
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
        return 'load_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_access_id' , 'user_id'], 'required'],
            [['user_access_id' , 'user_id' , 'work_load_id', 'work_rate_id' , 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['user_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccess::className(), 'targetAttribute' => ['user_access_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['work_load_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkLoad::className(), 'targetAttribute' => ['work_load_id' => 'id']],
            [['work_rate_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkRate::className(), 'targetAttribute' => ['work_rate_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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
            'user_id',
            'user_access_id',
            'work_load_id',
            'work_rate_id',
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
            'workLoad',
            'workRate',
            'userAccess',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getWorkRate()
    {
        return $this->hasOne(WorkRate::className(), ['id' => 'work_rate_id']);
    }

    public function getWorkLoad()
    {
        return $this->hasOne(WorkLoad::className(), ['id' => 'work_load_id']);
    }

    public function getUserAccess()
    {
        return $this->hasOne(UserAccess::className(), ['id' => 'user_access_id']);
    }

    public static function deleteItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->is_deleted = 1;
        $model->status = 0;
        $model->save(false);

        $orLoadRate = LoadRate::findOne([
            'user_access_id' => $model->user_access_id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$orLoadRate) {
            $userAccess = $model->userAccess;

            $userAccess->status = 0;
            $userAccess->is_deleted = 1;
            $userAccess->save(false);
            if ($userAccess->is_leader == 1) {
                $userAccessType = UserAccessType::findOne($model->user_access_type_id);
                $tableId = $userAccessType->table_name::find()->where(['id' => $model->table_id])->one();
                $tableId->user_id = null;
                $tableId->save(false);
            }
        }

        if (count($errors) == 0) {
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
}
