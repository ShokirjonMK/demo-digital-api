<?php

namespace common\models;

/**
 * This is the model class for table "job_info".
 *
 * @property int $info_id
 * @property int|null $job_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class JobInfo extends \base\libs\RedisDB
{

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'job_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'job_id', 'language'], 'required'],
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
            'job_id' => _e('Job'),
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
        return $this->hasOne(Job::class, ['id' => 'job_id']);
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new Job());
    }
}
