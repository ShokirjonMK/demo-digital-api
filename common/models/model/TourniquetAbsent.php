<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%tourniquet_absent}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $roles
 * @property string $passport_pin
 * @property string|null $date
 * @property string|null $date_time
 * @property string $date_out
 * @property string $date_in
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property User $user
 */
class TourniquetAbsent extends \yii\db\ActiveRecord
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
        return '{{%tourniquet_absent}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['passport_pin'], 'required'],
            [['date'], 'default', 'value' => date('Y-m-d')],
            // [['date'], 'default', 'value' => date('Y-m-d')],
            [['date', 'date_time', 'date_out', 'date_in'], 'safe'],
            [['roles', 'passport_pin'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

            ['passport_pin', 'unique', 'targetAttribute' => ['passport_pin', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'roles' => Yii::t('app', 'Roles'),
            'passport_pin' => Yii::t('app', 'Passport Pin'),
            'date' => Yii::t('app', 'Date'),
            'date_time' => Yii::t('app', 'Date Time'),
            'date_out' => Yii::t('app', 'Date Out'),
            'date_in' => Yii::t('app', 'Date In'),
            'status' => Yii::t('app', 'Status'),
            'order' => Yii::t('app', 'Order'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'roles',
            'passport_pin',
            'date',
            'date_time',
            'date_out',
            'date_in',
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
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * TourniquetAbsent createItem <$model, $post>
     */
    public static function createItemPGT($sheetDatas, $post)
    {
        $errors = [];
        $profiles = Profile::find()
            ->andWhere(['in', 'passport_pin', array_column($sheetDatas, 'ID')])
            ->joinWith(['user'])
            ->andWhere(['users.deleted' => 0])
            ->all();

        foreach ($sheetDatas as $value) {
            if ($value['ID'] !== null) {
                $profile = array_filter($profiles, function ($p) use ($value) {
                    return $p->passport_pin == $value['ID'];
                });
                if (count($profile) > 0) {
                    $newModel = new TourniquetAbsent();
                    $newModel->passport_pin = (int)$value['ID'];
                    $roles = current_user_roles(current($profile)->user_id);
                    $newModel->roles = json_encode(array_keys($roles));
                    $newModel->date = date('Y-m-d');
                    $newModel->user_id = current($profile)->user_id;
                    if (!$newModel->save(false)) {
                        $errors[] = ['ID' => $value['ID'], 'error' => $newModel->errors];
                    }
                } else {
                    $errors[] = ['ID' => $value['ID'], 'error' => "Profile not found"];
                }
            }
        }
        return (count($errors) > 0) ? $errors : true;
    }

    public static function createItem($sheetDatas, $post)
    {
        $errors = [];

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($sheetDatas as $value) {
                if ($value['ID'] !== null) {
                    $profile = Profile::findOne(['passport_pin' => $value['ID']]);
                    if ($profile) {
                        $newModel = new TourniquetAbsent();
                        $newModel->passport_pin = (int)$value['ID'];
                        $roles = current_user_roles($profile->user_id);
                        $newModel->roles = json_encode(array_keys($roles));
                        $newModel->date = date('Y-m-d');
                        $newModel->user_id = $profile->user_id;
                        if (!$newModel->save(false)) {
                            $errors[$value['ID']] = $newModel->errors;
                        }
                    } else {
                        $errors[$value['ID']] = "Profile not found for ID: " . $value['ID'];
                    }
                }
            }
            $transaction->commit();

            if (count($errors) === 0) {

                return true;
            } else {
                throw new \Exception('Failed to save some items');
            }
        } catch (\Exception $e) {
            // $transaction->rollBack();
            return $errors;
        }
    }




    public static function createItem0($sheetDatas, $post)
    {
        $errors = [];

        foreach ($sheetDatas as $value) {
            if ($value['ID'] !== null) { // ID null bo'lmasa
                $profile = Profile::findOne(['passport_pin' => $value['ID']]);
                if ($profile) { // Profil topilganda
                    $newModel = new TourniquetAbsent();
                    $newModel->passport_pin = (int)$value['ID'];
                    $roles = current_user_roles($profile->user_id);
                    $newModel->roles = json_encode(array_keys($roles));
                    $newModel->date = date('Y-m-d');
                    $newModel->user_id = $profile->user_id;

                    if (!$newModel->save(false)) { // Ma'lumot saqlanmagan bo'lsa
                        $errors[] = $newModel->errors; // Xatolarni qaytarib berish
                    }
                } else { // Profil topilmagan bo'lsa
                    $errors[] = "Profile not found for ID: " . $value['ID']; // Xatolarni qaytarib berish
                }
            }
        }
    }

    /**
     * TourniquetAbsent updateItem <$model, $post>
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

    // /**
    //  * {@inheritdoc}
    //  * @return TourniquetAbsentQuery the active query used by this AR class.
    //  */
    // public static function find()
    // {
    //     return new TourniquetAbsentQuery(get_called_class());
    // }


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
