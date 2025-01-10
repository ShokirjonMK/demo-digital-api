<?php

namespace common\models;

/**
 * This is the model class for table "university_info".
 *
 * @property int $info_id
 * @property int|null $university_id
 * @property string $language
 * @property string $name
 * @property string $description
 * @property string $address
 * @property string $parent
 */
class UniversityInfo extends \base\libs\RedisDB
{

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'university_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'university_id', 'language'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['language', 'description', 'address', 'parent'], 'string'],
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
            'university_id' => _e('University'),
            'language' => _e('Languages'),
            'name' => _e('Name'),
            'description' => _e('Description'),
            'address' => _e('Address'),
            'parent' => _e('Head organization'),
        ];
    }

    /**
     * Get content model
     *
     * @return void
     */
    public function getModel()
    {
        return $this->hasOne(University::class, ['id' => 'university_id']);
    }
}
