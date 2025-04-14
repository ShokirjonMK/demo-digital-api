<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "military ".
 *
 * @property int $id
 * @property string $name
 * @property string $lang
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class RelativeInfo extends \yii\db\ActiveRecord
{

    use ResourceTrait;
    public static $selected_language = 'uz';

    use ResourceTrait;

    const TYPE_FATHER = 1;
    const TYPE_MOTHER = 2;
    const TYPE_BROTHER = 3;
    const TYPE_SISTER = 4;

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'relative_info';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['user_id',],'required'],
            [['r_birthday',], 'date', 'format' => 'php:Y-m-d'],
            [['r_type', 'user_type', 'user_id'], 'integer'],
            [['r_last_name',
                'r_first_name',
                'r_middle_name',
                'r_birth_address',
                'r_address',
                'r_work_place',
                'r_work_position',
                'r_phone','r_description'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'r_birthday',
            'r_type',
            'user_type',
            'user_id',
            'r_last_name',
            'r_first_name',
            'r_middle_name',
            'r_birth_address',
            'r_address',
            'r_work_place',
            'r_work_position',
            'r_phone',
            'r_description',
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'r_birthday',
            'r_type',
            'user_type',
            'user_id',
            'r_last_name',
            'r_first_name',
            'r_middle_name',
            'r_birth_address',
            'r_address',
            'r_work_place',
            'r_work_position',
            'r_phone',
            'r_description',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
        ];

        return $fields;
    }

    ///
    public static function createItem($model, $post)
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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
