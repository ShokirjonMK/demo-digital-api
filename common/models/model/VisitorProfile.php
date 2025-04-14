<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "{{%visitor_profile}}".
 *
 * @property int $id
 * @property string|null $image
 * @property string|null $birth_place
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $passport_seria
 * @property string|null $passport_number
 * @property string|null $passport_pin
 * @property string|null $passport_given_date
 * @property string|null $passport_issued_date
 * @property string|null $passport_given_by
 * @property string|null $birthday
 * @property string|null $phone
 * @property string|null $phone_secondary
 * @property int|null $citizenship_id citizenship_id fuqarolik turi
 * @property int|null $nationality_id millati id
 * @property int|null $country_id
 * @property int|null $is_foreign
 * @property int|null $region_id
 * @property int|null $area_id
 * @property string|null $address
 * @property int|null $gender
 * @property string|null $description
 * @property int|null $turniket_id turniketdan qaytgan ID
 * @property int|null $turniket_status turniketga biriktirilganligi
 * @property int $status Status: 0-inactive, 1-active
 * @property int|null $order
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class VisitorProfile extends \yii\db\ActiveRecord
{
    use ResourceTrait;
    const UPLOADS_FOLDER = 'uploads/visitors/';
    public $avatar;
    // public $passport_file;
    public $avatarMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $passportFileMaxSize = 1024 * 1024 * 5; // 5 Mb

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
        return 'visitor_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['passport_pin'], 'required'],

            [['passport_given_date', 'passport_issued_date', 'birthday'], 'safe'],
            [['citizenship_id', 'checked_full', 'nationality_id', 'country_id', 'is_foreign', 'region_id', 'area_id', 'gender', 'turniket_id', 'turniket_status', 'status', 'order', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['image', 'birth_place', 'address'], 'string', 'max' => 255],
            [['last_name', 'first_name', 'middle_name'], 'string', 'max' => 64],
            [['passport_seria'], 'string', 'max' => 10],
            [['passport_number', 'passport_pin', 'phone', 'phone_secondary'], 'string', 'max' => 20],
            [['passport_given_by'], 'string', 'max' => 128],

            [['passport_pin'], 'unique'],

            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],

            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['nationality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Nationality::className(), 'targetAttribute' => ['nationality_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'image' => _e('Image'),
            'birth_place' => _e('Birth Place'),
            'last_name' => _e('Last Name'),
            'first_name' => _e('First Name'),
            'middle_name' => _e('Middle Name'),
            'passport_seria' => _e('Passport Seria'),
            'passport_number' => _e('Passport Number'),
            'passport_pin' => _e('Passport Pin'),
            'passport_given_date' => _e('Passport Given Date'),
            'passport_issued_date' => _e('Passport Issued Date'),
            'passport_given_by' => _e('Passport Given By'),
            'birthday' => _e('Birthday'),
            'phone' => _e('Phone'),
            'phone_secondary' => _e('Phone Secondary'),
            'citizenship_id' => _e('citizenship_id fuqarolik turi'),
            'nationality_id' => _e('millati id'),
            'country_id' => _e('Country ID'),
            'is_foreign' => _e('Is Foreign'),
            'region_id' => _e('Region ID'),
            'area_id' => _e('Area ID'),
            'address' => _e('Address'),
            'gender' => _e('Gender'),
            'description' => _e('Description'),
            'turniket_id' => _e('turniketdan qaytgan ID'),
            'turniket_status' => _e('turniketga biriktirilganligi'),
            'status' => _e('Status: 0-inactive, 1-active'),
            'order' => _e('Order'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'checked_full',
            'image',
            'birth_place',
            'last_name',
            'first_name',
            'middle_name',
            'passport_seria',
            'passport_number',
            'passport_pin',
            'passport_given_date',
            'passport_issued_date',
            'passport_given_by',
            'birthday',
            'phone',
            'phone_secondary',
            'citizenship_id',
            'nationality_id',
            'country_id',
            'is_foreign',
            'region_id',
            'area_id',
            'address',
            'gender',
            'description',
            'turniket_id',
            'turniket_status',
            'status',
            'order',
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
            'area',
            'country',

            'region',

            'nationality',
            'citizenship',
            'turniket',
            'turniketData',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getTurniket()
    {
        return $this->hasMany(Turniket::className(), ['turniket_id' => 'turniket_id']);
    }

    public function getTurniketData()
    {
        return $this->hasMany(TurniketData::className(), ['turniket_id' => 'turniket_id']);
    }


    /**
     * Gets query for [[ContractInfo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContractInfo()
    {
        return $this->hasOne(ContractInfo::className(), ['passport_pin' => 'passport_pin']);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }


    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
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
     * Gets query for [[Nationality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNationality()
    {
        return $this->hasOne(Nationality::className(), ['id' => 'nationality_id']);
    }

    /**
     * Gets query for [[Citizenship]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCitizenship()
    {
        return $this->hasOne(Citizenship::className(), ['id' => 'citizenship_id']);
    }

    /**
     * Get user fullname
     *
     * @param object $profile
     * @return mixed
     */
    public static function getFullname($profile)
    {
        $fullname = '';

        if ($profile && $profile->first_name) {
            $fullname = _strtotitle($profile->first_name) . ' ';
        }

        if ($profile && $profile->last_name) {
            $fullname .= _strtotitle($profile->last_name);
        }

        return $fullname ? trim($fullname) : 'Unknown User';
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

    public static function deleteItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = self::findOne(['id' => $id]);

        if (!isset($model)) {
            $errors[] = [_e('Visitor Profile not found')];
        } else {
            $model->is_deleted = 1;
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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
