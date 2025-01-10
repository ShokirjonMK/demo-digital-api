<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $edu_year_id
 * @property int $semestr_id
 * @property int $type
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
// * @property int $edu_year_id
// * @property int $semestr_id
 */
class TeacherCheckingType extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const TYPE_RANDOM = 1;
    const TYPE_OWN = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_checking_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_year_id', 'semestr_id', 'type'], 'required'],
            [
                [
                    'edu_year_id',
                    'semestr_id',
                    'type',

                    'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'
                ], 'integer'
            ],

            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name in translate

            'edu_year_id' => 'Edu Year Id',
            'semestr_id' => 'Semestr Id',
            'type' => 'Type',
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

            'edu_year_id',
            'semestr_id',
            'type',
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

            'eduYear',
            'semestr',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getSemestr()
    {
        return $this->hasMany(Semestr::className(), ['id' => 'semestr_id']);
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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public static function typeList()
    {
        return [
            self::TYPE_RANDOM => _e('TYPE_RANDOM'),
            self::TYPE_OWN => _e('TYPE_OWN'),
        ];
    }
}
