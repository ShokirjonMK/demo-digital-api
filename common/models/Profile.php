<?php

namespace common\models;

use Yii;
/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $middlename
 * @property string|null $image
 * @property string|null $dob
 * @property int|null $gender
 * @property string|null $phone
 * @property string|null $phone_secondary
 * @property int|null $is_stateless
 * @property int|null $is_foreign
 * @property int|null $country_id
 * @property int|null $nationality_id
 * @property int|null $birth_place_id
 * @property int|null $permanent_place_id
 * @property string|null $permanent_address
 * @property int|null $temporary_place_id
 * @property string|null $temporary_address
 * @property string|null $passport_serial
 * @property string|null $passport_number
 * @property string|null $passport_pinip
 * @property string|null $passport_given_place
 * @property string|null $passport_given_date
 * @property string|null $passport_validity_date
 * @property int|null $residence_permit
 * @property string|null $residence_permit_no
 * @property string|null $residence_permit_date
 * @property string|null $residence_permit_expire
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    public $region_id;

    public $birth_country_id;
    public $birth_region_id;

    public $temporary_country_id;
    public $temporary_region_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'gender', 'is_stateless', 'is_foreign', 'country_id', 'nationality_id', 'birth_place_id', 'permanent_place_id', 'temporary_place_id', 'residence_permit'], 'integer'],
            [['region_id', 'birth_country_id', 'birth_region_id', 'temporary_country_id', 'temporary_region_id'], 'integer'],
            [['birthday', 'passport_given_date', 'passport_validity_date', 'residence_permit_date', 'residence_permit_expire'], 'safe'],
            [['first_name', 'last_name', 'middle_name', 'image', 'permanent_address', 'temporary_address', 'passport_serial', 'passport_number', 'passport_given_place', 'residence_permit_no'], 'string', 'max' => 255],
            [['phone', 'phone_secondary'], 'string', 'max' => 50],
            [['passport_pinip'], 'string', 'max' => 14],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('User'),
            'first_name' => _e('First name'),
            'last_name' => _e('Last name'),
            'middle_name' => _e('Middle name'),
            'image' => _e('Photo'),
            'birthday' => _e('Date of birth'),
            'gender' => _e('Gender'),
            'phone' => _e('Phone'),
            'phone_secondary' => _e('Secondary phone'),
            'is_stateless' => _e('Stateless'),
            'is_foreign' => _e('Foreign citizen'),
            'country_id' => _e('Country'),
            'nationality_id' => _e('Nationality'),
            'birth_place_id' => _e('District'),
            'permanent_place_id' => _e('District'),
            'permanent_address' => _e('Постоянный адрес'),
            'temporary_place_id' => _e('District'),
            'temporary_address' => _e('Temporary address'),
            'passport_serial' => _e('Passport serial'),
            'passport_number' => _e('Passport number'),
            'passport_pinip' => _e('PINIP'),
            'passport_given_place' => _e('Passport given place'),
            'passport_given_date' => _e('Passport given date'),
            'passport_validity_date' => _e('Passport validity date'),
            'residence_permit' => _e('Residence permit'),
            'residence_permit_no' => _e('Residence permit number'),
            'residence_permit_date' => _e('Residence permit date'),
            'residence_permit_expire' => _e('Residence permit expire'),

            'birth_place' => _e('Birth place'),
            'region_id' => _e('Region'),
            'birth_country_id' => _e('Country'),
            'birth_region_id' => _e('Region'),
            'temporary_country_id' => _e('Country'),
            'temporary_region_id' => _e('Region'),
        ];
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

        if ($profile && $profile->firstname) {
            $fullname = _strtotitle($profile->firstname) . ' ';
        }

        if ($profile && $profile->lastname) {
            $fullname .= _strtotitle($profile->lastname);
        }

        return $fullname ? trim($fullname) : 'Unknown User';
    }

    /**
     * Get user avatar
     *
     * @param object $profile
     * @return mixed
     */
    public static function getAvatar($profile)
    {
        $image = images_url('user.png');

        if ($profile && $profile->image && is_url($profile->image)) {
            $image = $profile->image;
        }

        return $image;
    }

    public function getPermanentPlace(){
        return $this->hasOne(Regions::class,['id' => 'permanent_place_id']);
    }

    public function getTemporaryPlace(){
        return $this->hasOne(Regions::class,['id' => 'temporary_place_id']);
    }

    public function getBirthPlace(){
        return $this->hasOne(Regions::class,['id' => 'birth_place_id']);
    }

}
