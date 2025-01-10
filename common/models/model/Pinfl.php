<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $user_id
 * @property float|null $checked
 * @property float|null $checked_full
 * @property string|null $image
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $passport_serial
 * @property string|null $passport_number
 * @property string|null $passport_pin
 * @property string|null $passport_issued_date
 * @property string|null $passport_given_date
 * @property string|null $passport_given_by
 * @property string|null $birthday
 * @property int|null $gender
 * @property string|null $phone
 * @property string|null $phone_secondary
 * @property string|null $passport_file
 * @property int|null $countries_id
 * @property int|null $region_id
 * @property int|null $area_id
 * @property int|null $permanent_countries_id
 * @property int|null $permanent_region_id
 * @property int|null $permanent_area_id
 * @property string|null $permanent_address
 * @property string|null $address
 * @property string|null $description
 * @property int|null $is_foreign
 * @property int|null $citizenship_id citizenship_id fuqarolik turi
 * @property int|null $nationality_id millati id
 * @property int|null $telegram_chat_id
 * @property int|null $diploma_type_id diploma_type
 * @property int|null $degree_id darajasi id
 * @property int|null $academic_degree_id academic_degree id
 * @property int|null $degree_info_id degree_info id
 * @property int|null $partiya_id partiya id
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Area $area
 * @property Countries $countries
 * @property Area $permanentArea
 * @property Countries $permanentCountries
 * @property Region $permanentRegion
 * @property Region $region
// * @property Citizenship $citizenship
 * @property User $user

 */
class Pinfl extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    const UPLOADS_FOLDER_STUDENT_IMAGE = 'uploads/student-images/';

    public $avatar;
//     public $passport_file;
    public $avatarMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $passportFileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $allFileMaxSize = 1024 * 1024 * 5; // 5 Mb

    public $file_fileFileExtentions = 'pdf,doc,docx,ppt,pptx,zip';
    public $file_imageFileExtentions = 'png,gimp,bmp,jpeg';

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
        return 'pinfl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'gender',
                    'countries_id',
                    'region_id',
                    'area_id',
                    'permanent_countries_id',
                    'permanent_region_id',
                    'permanent_area_id',
                    'checked',
                    'checked_full',
                    'is_foreign',
                    'citizenship_id',
                    'nationality_id',
                    'telegram_chat_id',
                    'diploma_type_id',
                    'degree_id',
                    'academic_degree_id',
                    'degree_info_id',
                    'partiya_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [[
                'passport_issued_date',
                'passport_given_date',
                'birthday'
            ], 'safe'],
            [
                [
                    'address',
                    'description'
                ], 'string'
            ],
            [
                [
                    'image',
                    'first_name',
                    'last_name',
                    'middle_name',
                    'passport_serial',
                    'passport_number',
                    'passport_given_by',
                    'passport_file',
                    'permanent_address',

                    'father_fio',
                    'father_number',
                    'mather_fio',
                    'mather_number',
                ], 'string', 'max' => 255
            ],

            [
                ['all_file', 'mather_info', 'father_info'], 'safe'
            ],
            [
                ['passport_pin'], 'string', 'max' => 15
            ],
            [
                ['phone', 'phone_secondary'], 'string', 'max' => 50
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('User ID'),
            'checked' => _e('Checked'),
            'checked_full' => _e('Checked Full'),
            'image' => _e('Image'),
            'first_name' => _e('First Name'),
            'last_name' => _e('Last Name'),
            'middle_name' => _e('Middle Name'),
            'passport_serial' => _e('Passport Serial'),
            'passport_number' => _e('Passport Number'),
            'passport_pin' => _e('Passport Pin'),
            'passport_issued_date' => _e('Passport Issued Date'),
            'passport_given_date' => _e('Passport Given Date'),
            'passport_given_by' => _e('Passport Given By'),
            'birthday' => _e('Birthday'),
            'gender' => _e('Gender'),
            'phone' => _e('Phone'),
            'phone_secondary' => _e('Phone Secondary'),
            'passport_file' => _e('Passport File'),
            'countries_id' => _e('Countries ID'),
            'region_id' => _e('Region ID'),
            'area_id' => _e('Area ID'),
            'permanent_countries_id' => _e('Permanent Countries ID'),
            'permanent_region_id' => _e('Permanent Region ID'),
            'permanent_area_id' => _e('Permanent Area ID'),
            'permanent_address' => _e('Permanent Address'),
            'address' => _e('Address'),
            'description' => _e('Description'),
            'is_foreign' => _e('Is Foreign'),
            'citizenship_id' => _e('Citizenship ID'),
            'nationality_id' => _e('Nationality ID'),
            'telegram_chat_id' => _e('Telegram Chat ID'),
            'diploma_type_id' => _e('Diploma Type ID'),
            'degree_id' => _e('Degree ID'),
            'academic_degree_id' => _e('Academic Degree ID'),
            'degree_info_id' => _e('Degree Info ID'),
            'partiya_id' => _e('Partiya ID'),
            'order' => _e('Order'),
            'status' => _e('Status'),
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
        $fields = [
            'id',
            'user_id',
            'checked',
            'checked_full',
            'image',
            'img',
            'first_name',
            'last_name',
            'middle_name',
            'birthday',
            'gender',
            'phone',
            'phone_secondary',
            'passport_file',
            'all_file',
            'countries_id',
            'region_id',
            'area_id',
            'permanent_countries_id',
            'permanent_region_id',
            'permanent_area_id',
            'permanent_address',
            'address',
            'description',
            'is_foreign',
            'citizenship_id',
            'nationality_id',
            'telegram_chat_id',
            'diploma_type_id',
            'degree_id',
            'academic_degree_id',
            'degree_info_id',
            'partiya_id',

            'father_fio',
            'father_number',
            'father_info',
            'mather_fio',
            'mather_number',
            'mather_info',

            'passport_serial' => function () {
                if (isRole('admin') || isRole('edu_admin') || isRole('dean') || isRole('dep_lead') || isRole('hr')) {
                    return $this->passport_serial;
                } elseif ($this->user_id == current_user_id()) {
                    return $this->passport_serial;
                } else {
                    return "**";
                }
            },

            'passport_number' => function () {
                if (isRole('admin') || isRole('edu_admin') || isRole('dean') || isRole('dep_lead') || isRole('hr')) {
                    return $this->passport_number;
                } elseif ($this->user_id == current_user_id()) {
                    return $this->passport_number;
                } else {
                    return "*******";
                }
            },

            'passport_pin' => function () {
                if (isRole('admin') || isRole('edu_admin') || isRole('dean') || isRole('dep_lead') || isRole('hr')) {
                    return $this->passport_pin;
                } elseif ($this->user_id == current_user_id()) {
                    return $this->passport_pin;
                } else {
                    return "**************";
                }
            },

            'passport_issued_date' => function () {
                if (isRole('admin') || isRole('edu_admin') || isRole('dean') || isRole('dep_lead') || isRole('hr')) {
                    return $this->passport_issued_date;
                } elseif ($this->user_id == current_user_id()) {
                    return $this->passport_issued_date;
                } else {
                    return "****-**-**";
                }
            },

            'passport_given_date' => function () {
                if (isRole('admin') || isRole('edu_admin') || isRole('dean') || isRole('dep_lead') || isRole('hr')) {
                    return $this->passport_given_date;
                } elseif ($this->user_id == current_user_id()) {
                    return $this->passport_given_date;
                } else {
                    return "****-**-**";
                }
            },

            'passport_given_by' => function () {
                if (isRole('admin') || isRole('edu_admin') || isRole('dean') || isRole('dep_lead') || isRole('hr')) {
                    return $this->passport_issued_date;
                } elseif ($this->user_id == current_user_id()) {
                    return $this->passport_issued_date;
                } else {
                    return "****-**-**";
                }
            },

            'order',
            'status',
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
        $extraFields = [
            'area',
            'countries',
            'permanentArea',
            'permanentCountries',
            'permanentRegion',
            'region',
            'citizenship',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

}