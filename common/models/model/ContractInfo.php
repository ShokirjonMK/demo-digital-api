<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%contract_info}}".
 *
 * @property int $id
 * @property int|null $student_id
 * @property int|null $uzasbo_id
 * @property string|null $passport_pin
 * @property string|null $contract_number
 * @property string|null $scholarship_type
 * @property float|null $contract_price
 * @property float|null $contract_price_half
 * @property string|null $reception_type
 * @property string|null $order_class
 * @property string|null $order_enter
 * @property string|null $order_no_class
 * @property string|null $order_fire
 * @property string|null $order_edu_holiday
 * @property string|null $order_change_edu_form
 * @property float|null $debt_begin
 * @property float|null $overpayment
 * @property float|null $must_pay_this_year
 * @property float|null $paid_this_year
 * @property float|null $payment_percent
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Student $student
 */
class ContractInfo extends \yii\db\ActiveRecord
{
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
        return '{{%contract_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'uzasbo_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['contract_price', 'contract_price_half', 'debt_begin', 'overpayment', 'must_pay_this_year', 'paid_this_year', 'payment_percent'], 'number'],
            [['passport_pin', 'contract_number', 'scholarship_type', 'reception_type', 'order_class', 'order_enter', 'order_no_class', 'order_fire', 'order_edu_holiday', 'order_change_edu_form'], 'string', 'max' => 255],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'student_id' => _e('Student ID'),
            'uzasbo_id' => _e('Uzasbo ID'),
            'passport_pin' => _e('Passport Pin'),
            'contract_number' => _e('Contract Number'),
            'scholarship_type' => _e('Scholarship Type'),
            'contract_price' => _e('Contract Price'),
            'contract_price_half' => _e('Contract Price Half'),
            'reception_type' => _e('Reception Type'),
            'order_class' => _e('Order Class'),
            'order_enter' => _e('Order Enter'),
            'order_no_class' => _e('Order No Class'),
            'order_fire' => _e('Order Fire'),
            'order_edu_holiday' => _e('Order Edu Holiday'),
            'order_change_edu_form' => _e('Order Change Edu Form'),
            'debt_begin' => _e('Debt Begin'),
            'overpayment' => _e('Overpayment'),
            'must_pay_this_year' => _e('Must Pay This Year'),
            'paid_this_year' => _e('Paid This Year'),
            'payment_percent' => _e('Payment Percent'),
            'status' => _e('Status'),
            'order' => _e('Order'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'student_id',
            'uzasbo_id',
            'passport_pin',
            'contract_number',
            'scholarship_type',
            'contract_price',
            'contract_price_half',
            'reception_type',
            'order_class',
            'order_enter',
            'order_no_class',
            'order_fire',
            'order_edu_holiday',
            'order_change_edu_form',
            'debt_begin',
            'overpayment',
            'must_pay_this_year',
            'paid_this_year',
            'payment_percent',
            'status',
            'order',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'student',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['passport_pin' => 'passport_pin']);
    }

    /**
     * ContractInfo createItem <$model, $post>
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
     * ContractInfo updateItem <$model, $post>
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
