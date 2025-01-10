<?php

namespace common\models\model;

use common\models\Countries;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "regions".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property int|null $country_id
 * @property int|null $parent_id
 * @property int|null $sort
 * @property int|null $postcode
 * @property int|null $lat
 * @property int|null $long
 * @property int $status
 * @property string|null $created_on
 * @property int|null $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 *
 * @property Countries $country
 */
class Regions extends \base\libs\RedisDB
{
    const TYPE_CITY = 10;
    const TYPE_REGION = 20;
    const TYPE_STATE = 30;
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['country_id', 'parent_id', 'sort', 'status'], 'integer'],
            [['status', 'name','name_kirill', 'country_id'], 'required'],
            [['created_by', 'updated_by'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 150],
            [['lat', 'long', 'postcode'], 'string', 'max' => 150],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'Id']],
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
            'id' => 'ID',
            'name' => _e('Name'),
            'slug' => _e('Slug'),
            'country_id' => _e('Country'),
            'postcode' => _e('Postcode'),
            'lat' => _e('Latitude'),
            'long' => _e('Longitude'),
            'parent_id' => $this->getParentType(),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'created_by' => _e('Created by'),
            'updated_by' => _e('Updated by'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name',
            'status',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'area',
        ];
        return $extraFields;
    }


    /**
     * Get parent
     *
     * @return void
     */

    public function getArea()
    {
        return $this->hasMany(Area::className(), ['region_id' => 'id']);
    }


    public function getParent()
    {
        return $this->hasOne(Regions::className(), ['id' => 'parent_id']);
    }

    /**
     * get Country
     *
     * @return void
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['Id' => 'country_id']);
    }

    /**
     * Get parent
     *
     * @return string
     */
    private function getParentType()
    {
        // Ushbu method ni keyinchalik state va mahalla lar qo'shilganda o'zgartirib ishlatish mumkin
        switch ($this->type) {
            case self::TYPE_REGION:
                return _e('Region2');
                break;
        }
    }

    public static function listRegions($country_id = null){
        return ArrayHelper::map(self::find()->where(['type' => 10])->andFilterWhere(['country_id' => $country_id])->all(),'id', 'name');
    }

    public static function listDistricts($parent_id = null){
        return ArrayHelper::map(self::find()->where(['type' => 20])->andFilterWhere(['parent_id' => $parent_id])->all(),'id', 'name');
    }

    public static function listRegionsWithCountry($country_id = null){
        return self::find()
        ->select(['id', 'name', 'country_id'])
        ->where(['type' => 10])
        ->andFilterWhere(['country_id' => $country_id])
        ->asArray()
        ->all();
    }

    public static function listDistrictsWithRegion($parent_id = null){
        return self::find()
        ->select(['id', 'name', 'parent_id'])
        ->where(['type' => 20])
        ->andFilterWhere(['parent_id' => $parent_id])
        ->asArray()
        ->all();
    }



    public static function listAll($type, $parent_id = null){
        return ArrayHelper::map(self::find()->where(['type' => $type])->andFilterWhere(['parent_id' => $parent_id])->all(),'id', 'name');
    }
}
