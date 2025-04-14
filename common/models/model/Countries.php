<?php

namespace common\models\model;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property int $id
 * @property string $name
 * @property string $ISO
 * @property string|null $ISO3
 * @property int|null $num_code
 * @property int $phone_code
 *
 * @property Profile[] $profiles
 * @property Profile[] $profiles0
 * @property Region[] $regions
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'ISO', 'phone_code'], 'required'],
            [['num_code', 'phone_code'], 'integer'],
            [['name'], 'string', 'max' => 80],
            [['ISO'], 'string', 'max' => 2],
            [['ISO3'], 'string', 'max' => 3],
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
            'ISO' => 'Iso',
            'ISO3' => 'Iso3',
            'num_code' => 'Num Code',
            'phone_code' => 'Phone Code',
        ];
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['country_id' => 'id']);
    }

    /**
     * Gets query for [[Profiles0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles0()
    {
        return $this->hasMany(Profile::className(), ['permanent_country_id' => 'id']);
    }

    /**
     * Gets query for [[Regions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::className(), ['country_id' => 'id']);
    }
}
