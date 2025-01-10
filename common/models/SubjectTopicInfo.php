<?php

namespace common\models;

/**
 * This is the model class for table "subject_topic_info".
 *
 * @property int $info_id
 * @property int|null $subject_topic_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class SubjectTopicInfo extends \base\libs\RedisDB
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'subject_topic_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'subject_topic_id', 'language'], 'required'],
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
            'subject_topic_id' => _e('Subject'),
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
        return $this->hasOne(Subject::class, ['id' => 'subject_topic_id']);
    }
}
