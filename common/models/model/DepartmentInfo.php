<?php

namespace common\models\model;

use common\models\Department;

/**
 * This is the model class for table "department_info".
 *
 * @property int $info_id
 * @property int|null $department_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class DepartmentInfo extends \base\libs\RedisDB
{

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'department_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'department_id', 'language'], 'required'],
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
            'department_id' => _e('Department'),
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
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new Department());
    }
}
