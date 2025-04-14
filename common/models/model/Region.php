<?php

namespace common\models\model;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_kirill
 * @property string|null $slug
 * @property int|null $country_id
 * @property int|null $parent_id
 * @property int|null $type
 * @property string|null $postcode
 * @property string|null $lat
 * @property string|null $long
 * @property int|null $sort
 * @property int|null $status
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 *
 * @property Area[] $areas
 * @property Countries $country
 * @property Region $parent
 * @property Profile[] $profiles
 * @property Profile[] $profiles0
 * @property Region[] $regions
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'parent_id', 'type', 'sort', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'name_kirill', 'slug', 'postcode'], 'string', 'max' => 150],
            [['lat', 'long'], 'string', 'max' => 100],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'name_kirill' => 'Name Kirill',
            'slug' => 'Slug',
            'country_id' => 'Country ID',
            'parent_id' => 'Parent ID',
            'type' => 'Type',
            'postcode' => 'Postcode',
            'lat' => 'Lat',
            'long' => 'Long',
            'sort' => 'Sort',
            'status' => _e('Status'),
            'created_on' => 'Created On',
            'created_by' => _e('Created By'),
            'updated_on' => 'Updated On',
            'updated_by' => _e('Updated By'),
        ];
    }



    public function fields()
    {
        $fields =  [
            'id',
            'name',
            'name_kirill',
            'slug',
            'country_id',
            'parent_id',
            'type',
            'postcode',
            'lat',
            'long',
            'status',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'areas',
            'profilesLive',
            'profiles',
            'country',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }


    /**
     * Gets query for [[Areas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::className(), ['region_id' => 'id']);
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
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Region::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['permanent_region_id' => 'id']);
    }

    /**
     * Gets query for [[Profiles0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfilesLive()
    {
        return $this->hasMany(Profile::className(), ['region_id' => 'id']);
    }

    /**
     * Gets query for [[Regions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::className(), ['parent_id' => 'id']);
    }
}
