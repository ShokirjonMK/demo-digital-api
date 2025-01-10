<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "university".
 *
 * @property int $id
 * @property int $country_id
 * @property int|null $region_id
 * @property int|null $district_id
 * @property int|null $type
 * @property string|null $code
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class University extends \base\libs\RedisDB
{

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'university';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['sort', 'country_id'], 'required'],
            [['created_on', 'updated_by'], 'safe'],
            [['code'], 'string'],
            [['sort', 'status', 'deleted', 'created_by', 'updated_by', 'region_id', 'district_id'], 'integer'],
            [['status', 'sort', 'deleted','type'], 'default', 'value' => 0],
            ['created_on', 'default', 'value' => date('Y-m-d H:i:s')],
            ['updated_on', 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'country_id' => _e('Country'),
            'region_id' => _e('Region'),
            'district_id' => _e('District'),
            'type' => _e('Type'),
            'code' => _e('Code'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'deleted' => _e('Deleted'),
            'created_on' => _e('Created on'),
            'created_by' => _e('Created by'),
            'updated_on' => _e('Updated on'),
            'updated_by' => _e('Updated by'),
        ];
    }

    /**
     * Get content infos
     *
     * @return void
     */
    public function getInfos()
    {
        return $this->hasMany(UniversityInfo::class, ['university_id' => 'id']);
    }

    /**
     * Get content info
     *
     * @return void
     */
    public function getInfoRelation()
    {
        self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(UniversityInfo::class, ['university_id' => 'id'])
                    ->andOnCondition(['language' => self::$selected_language]);
    }

    /**
     * Get info
     *
     * @return void
     */
    public function getInfo()
    {
        return $this->infoRelation[0];
    }

    /**
     * Get country
     *
     * @return void
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::class, ['id' => 'country_id']);
    }


    /**
     * Get region
     *
     * @return void
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::class, ['id' => 'region_id']);
    }

    /**
     * Get district
     *
     * @return void
     */
    public function getDistricts()
    {
        return $this->hasOne(Regions::class, ['id' => 'district_id']);
    }

    /**
     * Get all items
     *
     * @param int $lang
     * @return array
     */
    public static function listAll($lang = null){
        
        $lang = $lang ?? self::$selected_language;
        $result = self::find()
            ->join('INNER JOIN', 'university_info info', 'info.university_id = university.id')
            ->select('university.*, info.*')
            ->where([
                'and',
                ['info.language' => $lang]
            ])
            ->asArray()->all();
        $list = [];
        foreach ($result as $one) {
            $list[$one['id']] = $one['name'];
        }
        return $list;
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            1 => _e('Active'),
            0 => _e('Inactive'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }

}
