<?php

namespace common\models\model;

use api\resources\ResourceTrait;

use common\models\model\Student;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $year
 * @property int $month
 * @property int $user_access_type_id
 * @property int $table_id
 * @property json $data
 * @property int $is_checked
 * @property int $type
 * @property string $description
 *
 * @property int|null $type
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class TableStore extends \yii\db\ActiveRecord
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
    const STATUS_FINISHED = 2;

    const ISCHECKED_TRUE = 1;
    const ISCHECKED_FALSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tabel_store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'year',
                    'month',
                    'user_access_type_id',
                    'table_id',
                ], 'required'
            ],
            [
                [
                    'year',
                    'month',
                    'user_access_type_id',
                    'table_id',
                    'is_checked',
                    'type',

                    'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'
                ], 'integer'
            ],
            [
                [
                    'description',
                    // 'data',
                    'mem'
                ], 'string'
            ],
            [['year'], 'datetime', 'format' => 'php:Y'],
            [['month'], 'datetime', 'format' => 'php:m'],
            [['month'], 'default', 'value' => (int)date('m')],
            [['year'], 'default', 'value' => (int)date('Y')],

            [['user_access_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccessType::className(), 'targetAttribute' => ['user_access_type_id' => 'id']],
            [['type'], 'unique', 'targetAttribute' => ['user_access_type_id', 'table_id', 'year', 'month', 'type', 'is_deleted']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'user_access_type_id' => _e('user_access_type_id'),
            'table_id' => _e('table_id'),
            'type' => _e('type'),
            'description' => _e('description'),
            'year' => _e('year'),
            'month' => _e('month'),
            'data' => _e('data'),
            'is_checked' => _e('is_checked'),
            'mem' => _e('mem'),

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

            'year',
            'month',
            'user_access_type_id',
            'table_id',
            'data',
            'is_checked',
            'type',
            'description',
            'mem',

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

            'userAccessType',
            'department',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getDepartment()
    {
        return $this->userAccessType->table_name::findOne(['id' => $this->table_id]);
    }

    public function getUserAccessType()
    {
        return $this->hasOne(UserAccessType::className(), ['id' => 'user_access_type_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['data'])) {
            if (($post['data'][0] == "'") && ($post['data'][strlen($post['data']) - 1] == "'")) {
                $post['data'] =  substr($post['data'], 1, -1);
            }

            if (!isJsonMK($post['data'])) {
                $errors['data'] = [_e('Must be Json')];
            } else {
                $data = ((array)json_decode($post['data']));
                $model->data = $data;
            }
        }

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

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['data'])) {
            if (($post['data'][0] == "'") && ($post['data'][strlen($post['data']) - 1] == "'")) {
                $post['data'] =  substr($post['data'], 1, -1);
            }

            if (!isJsonMK($post['data'])) {
                $errors['data'] = [_e('Must be Json')];
            } else {
                $data = ((array)json_decode($post['data']));
                $model->data = $data;
            }
        }

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
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
            self::STATUS_FINISHED => _e('STATUS_FINISHED'),
        ];
    }
}
