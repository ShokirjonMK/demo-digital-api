<?php

namespace common\models;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $settings_key
 * @property string|null $settings_value
 * @property string|null $settings_group
 * @property string|null $settings_type
 * @property int|null $status
 * @property int|null $sort
 * @property int $required
 * @property string|null $updated_on
 */
class Settings extends \base\libs\RedisDB
{
    public $translation;

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['updated_on'], 'safe'],
            [['title', 'settings_key', 'settings_group', 'settings_type', 'settings_value'], 'string'],
            [['status', 'sort', 'required'], 'integer'],
            [['status', 'sort', 'required'], 'default', 'value' => 0],
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
            'title' => _e('Title'),
            'settings_key' => _e('Settings key'),
            'settings_value' => _e('Settings value'),
            'settings_group' => _e('Settings group'),
            'settings_type' => _e('Settings type'),
            'translation' => _e('Translation'),
            'status' => _e('Status'),
            'sort' => _e('Sort'),
            'required' => _e('Required'),
            'updated_on' => _e('Updated on'),
        ];
    }
}
