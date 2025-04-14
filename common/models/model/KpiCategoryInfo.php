<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "kpi_category_info".
 *
 * @property int $id
 * @property int $kpi_category_id
 * @property string $name
 * @property string $lang
 * @property string $name
 * @property string $description
 * @property string $tab_name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class KpiCategoryInfo extends \yii\db\ActiveRecord
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
        return 'kpi_category_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'kpi_category_id',
                    'lang',
                    'name',
                ], 'required'
            ],
            [
                [
                    'lang',
                ], 'string', 'max' => 3
            ],
            [
                [
                    'tab_name',
                ], 'string', 'max' => 255
            ],
            [
                [
                    'name',
                ], 'string'
            ],
            [
                [
                    'description',
                ], 'string'
            ],
            [['kpi_category_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [
                ['status'], 'default', 'value' => 1
            ],
            [['kpi_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => KpiCategory::className(), 'targetAttribute' => ['kpi_category_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'kpi_category_id',
            'lang',
            'name',
            'description',
            'tab_name',

            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function extraFields()
    {
        $extraFields =  [
            'kpiCategory',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getKpiCategory()
    {
        return $this->hasMany(KpiCategory::className(), ['id' => 'kpi_category_id']);
    }


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
