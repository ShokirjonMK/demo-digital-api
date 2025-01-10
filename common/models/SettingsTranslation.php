<?php

namespace common\models;

/**
 * This is the model class for table "settings_translation".
 *
 * @property int $id
 * @property string|null $language
 * @property string|null $settings_key
 * @property string|null $settings_value
 * @property string|null $updated_on
 */
class SettingsTranslation extends \base\libs\RedisDB
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'settings_translation';
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
            [['language', 'settings_value', 'settings_key'], 'string'],
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
            'language' => _e('Languages'),
            'settings_key' => _e('Settings key'),
            'settings_value' => _e('Settings value'),
            'updated_on' => _e('Updated On'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return new Settings();
    }
    
}
