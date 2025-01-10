<?php

namespace common\models;

/**
 * This is the model class for table "reference_info".
 *
 * @property int $info_id
 * @property int|null $reference_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class ReferenceInfo extends \base\libs\RedisDB
{

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'reference_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'reference_id', 'language'], 'required'],
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
            'reference_id' => _e('Reference'),
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
        return $this->hasOne(Reference::class, ['id' => 'reference_id']);
    }
}
