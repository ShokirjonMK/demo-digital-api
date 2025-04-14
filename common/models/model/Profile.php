<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%profile}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $checked
 * @property int|null $checked_full
 * @property string|null $image
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
 * @property string|null $passport_file
 * @property int|null $country_id
 * @property int|null $is_foreign
 * @property int|null $region_id
 * @property int|null $area_id
 * @property string|null $address
 * @property int|null $gender
 * @property int|null $permanent_country_id
 * @property int|null $permanent_region_id
 * @property int|null $permanent_area_id
 * @property string|null $permanent_address
 * @property int|null $order
 * @property int|null $status
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int|null $citizenship_id citizenship_id fuqarolik turi
 * @property int|null $nationality_id millati id 
 * @property int|null $telegram_chat_id
 * @property int|null $diploma_type_id diploma_type
 * @property int|null $degree_id darajasi id
 * @property int|null $academic_degree_id academic_degree id
 * @property int|null $degree_info_id degree_info id
 * @property int|null $partiya_id partiya id
 * @property int|null $has_disability nogironlik
 *
 * @property Area $area
 * @property Citizenship $citizenship
 * @property Country $country
 * @property Area $permanentArea
 * @property Country $permanentCountry
 * @property Region $permanentRegion
 * @property Region $region
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{

    use ResourceTrait;
    const UPLOADS_FOLDER = 'uploads/user-images/';
    const UPLOADS_FOLDER_STUDENT_IMAGE = 'uploads/student-images/';
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
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            // [[
            //     'user_id', 'image', 'phone', 'phone_secondary',
            //     'is_foreign', 'last_name', 'first_name', 'middle_name',
            //     'passport_seria', 'passport_number', 'passport_pin',
            //     'birthday', 'passport_file', 'country_id', 'region_id',
            //     'area_id', 'address', 'gender', 'passport_given_date',
            //     'passport_issued_date', 'passport_given_by', 'permanent_country_id',
            //     'permanent_region_id', 'permanent_area_id', 'permanent_address'
            // ], 'required'],

            [
                [
                    'diploma_type_id',
                    'degree_id',
                    'academic_degree_id',
                    'degree_info_id',
                    'partiya_id',
                    'checked',
                    'checked_full',
                    'house_of_kindness',
                    'underprivileged',

                    'user_id',
                    'nationality_id',
                    'is_foreign',
                    'country_id',
                    'passport_number',
                    'passport_pin',
                    'region_id',
                    'area_id',
                    'gender',
                    'permanent_country_id',
                    'permanent_region_id',
                    'permanent_area_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'telegram_chat_id',
                    'has_disability',
                    'turniket_id',
                    'turniket_status',
                ],
                'integer'
            ],
            [['passport_given_date', 'birthday',  'passport_issued_date'], 'safe'],
            [
                [
                    'orcid',
                    'image',
                    'last_name',
                    'first_name',
                    'middle_name',
                    'passport_seria',
                    'car_number',


                    'passport_file',
                    'address',
                    'passport_given_by',
                    'permanent_address'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'phone',
                    'phone_secondary'
                ],
                'string',
                'max' => 50
            ],
            [
                [
                    'description'
                ],
                'string'
            ],

            [['last_in'], 'safe'],


            [['passport_pin'], 'unique'],

            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],
            [['passport_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, png, jpg', 'maxSize' => $this->passportFileMaxSize],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['permanent_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['permanent_area_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['permanent_country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['permanent_country_id' => 'id']],
            [['permanent_region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['permanent_region_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['nationality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Nationality::className(), 'targetAttribute' => ['nationality_id' => 'id']],

            [['diploma_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DiplomaType::className(), 'targetAttribute' => ['diploma_type_id' => 'id']],
            [['degree_id'], 'exist', 'skipOnError' => true, 'targetClass' => Degree::className(), 'targetAttribute' => ['degree_id' => 'id']],
            [['academic_degree_id'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicDegree::className(), 'targetAttribute' => ['academic_degree_id' => 'id']],
            [['degree_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => DegreeInfo::className(), 'targetAttribute' => ['degree_info_id' => 'id']],
            [['partiya_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partiya::className(), 'targetAttribute' => ['partiya_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'telegram_chat_id' => 'Telegram Chat ID',
            'nationality_id' => 'Nationality ID',
            'image' => 'Image',
            'phone' => 'Phone',
            'phone_secondary' => 'Phone Secondary',
            'is_foreign' => 'Is Foreign',
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'passport_seria' => 'Passport Seria',
            'passport_number' => 'Passport Number',
            'passport_pin' => 'Passport Pin',
            'birthday' => 'Birthday',
            'passport_file' => 'Passport File',
            'country_id' => 'Country ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'address' => 'Address',
            'gender' => 'Gender',
            'description' => 'Description',

            'diploma_type_id' => _e('diploma_type'),
            'degree_id' => _e('degree'),
            'academic_degree_id' => _e('academic_degree'),
            'degree_info_id' => _e('degree_info'),
            'partiya_id' => _e('partiya'),

            'passport_given_date' => 'Passport Given Date',
            'passport_issued_date' => 'Passport Issued Date',
            'passport_given_by' => 'Passport Given By',
            'permanent_country_id' => 'Permanent Country ID',
            'permanent_region_id' => 'Permanent Region ID',
            'permanent_area_id' => 'Permanent Area ID',
            'permanent_address' => 'Permanent Address',
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
        if (isRole('admin') || isRole('edu_admin') || isRole('hr')) {
            $fields =  [
                'id',
                'user_id',
                // 'checked',
                'checked_full',
                'last_in',
                'image',
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
                'passport_file',
                'country_id',
                'is_foreign',
                'region_id',
                'area_id',
                'address',
                'gender',
                'permanent_country_id',
                'permanent_region_id',
                'permanent_area_id',
                'permanent_address',
                'order',
                'status',
                'description',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted',
                'citizenship_id',
                'nationality_id',
                'telegram_chat_id',
                'diploma_type_id',
                'degree_id',
                'academic_degree_id',
                'degree_info_id',
                'partiya_id',
                'has_disability',
                'orcid',
                'turniket_id',
                'turniket_status',

                // unset($fields['password'], $fields['remember_token']);

            ];
        } else {
            $fields =  [
                'id',
                'user_id',
                'last_in',
                // 'checked',
                'checked_full',
                'image',
                'last_name',
                'first_name',
                'middle_name',
                // 'passport_seria',
                'passport_seria' => function ($model) {
                    return "**";
                },
                // 'passport_number',
                'passport_number' => function ($model) {
                    return "*******";
                },
                // 'passport_pin',
                'passport_pin' => function ($model) {
                    return "**************";
                },
                // 'passport_given_date',
                'passport_given_date' => function ($model) {
                    return "***-**-**";
                },
                // 'passport_issued_date',
                'passport_issued_date' => function ($model) {
                    return "***-**-**";
                },
                // 'passport_given_by',
                'passport_given_by' => function ($model) {
                    return "***";
                },
                'birthday',
                'phone',
                'phone_secondary',
                // 'passport_file',
                'country_id',
                'is_foreign',
                'region_id',
                'area_id',
                'address',
                'gender',
                'permanent_country_id',
                'permanent_region_id',
                'permanent_area_id',
                'permanent_address',
                'order',
                'status',
                'description',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted',
                'citizenship_id',
                'nationality_id',
                'telegram_chat_id',
                'diploma_type_id',
                'degree_id',
                'academic_degree_id',
                'degree_info_id',
                'partiya_id',
                'has_disability',
                'orcid',
                'turniket_id',
                'turniket_status',


            ];
        }


        return $fields;
    }



    public function extraFields()
    {
        $extraFields =  [


            'degreeInfo',
            'academicDegree',
            'degree',
            'contractInfo',
            'area',
            'country',
            'permanentArea',
            'permanentCountry',
            'permanentRegion',
            'region',
            'user',
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

    public function getDegreeInfo()
    {
        return $this->hasOne(DegreeInfo::className(), ['id' => 'degree_info_id']);
    }

    public function getAcademicDegree()
    {
        return $this->hasOne(AcademicDegree::className(), ['id' => 'academic_degree_id']);
    }
    public function getDegree()
    {
        return $this->hasOne(Degree::className(), ['id' => 'degree_id']);
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
     * Gets query for [[PermanentArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermanentArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'permanent_area_id']);
    }

    /**
     * Gets query for [[PermanentCountry]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermanentCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'permanent_country_id']);
    }

    /**
     * Gets query for [[PermanentRegion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermanentRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'permanent_region_id']);
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

        $model = Profile::findOne(['id' => $id]);

        if (!isset($model)) {
            $errors[] = [_e('Profile not found')];
        } else {
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
