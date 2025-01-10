<?php

namespace common\models;

/**
 * This is the model class for table "rank_info".
 *
 * @property int $info_id
 * @property int|null $rank_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class RankInfo extends \base\libs\RedisDB
{

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'rank_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'rank_id', 'language'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['language', 'description'], 'string'],
            [['description'], 'default', 'value' => ''],
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
            'info_id' => _e('Info ID'),
            'rank_id' => _e('Rank'),
            'language' => _e('Languages'),
            'name' => _e('Name'),
            'description' => _e('Description'),
        ];
    }

    /**
     * Get content model
     *
     * @return void
     */
    public function getModel()
    {
        return $this->hasOne(Rank::class, ['id' => 'rank_id']);
    }
}
