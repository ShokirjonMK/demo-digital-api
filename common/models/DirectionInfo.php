<?php

namespace common\models;

/**
 * This is the model class for table "direction_info".
 *
 * @property int $info_id
 * @property int|null $direction_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class DirectionInfo extends \base\libs\RedisDB
{

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'direction_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'direction_id', 'language'], 'required'],
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
            'direction_id' => _e('Direction'),
            'language' => _e('Language'),
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
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }
}
