<?php

namespace common\models;

/**
 * This is the model class for table "menu_group".
 *
 * @property int $id
 * @property string $group_key
 * @property string $name
 * @property string $description
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class MenuGroup extends \base\libs\RedisDB
{
    public $title;
    public $language;

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'menu_group';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['created_on', 'updated_on'], 'safe'],
            [['title'], 'required'],
            [['description', 'name', 'language', 'title'], 'string'],
            [['sort', 'status', 'deleted', 'created_by', 'updated_by'], 'integer'],
            [['name', 'group_key'], 'string', 'max' => 255],
            [['sort', 'status'], 'default', 'value' => 0],
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
            'group_key' => _e('Group key'),
            'description' =>  _e('Description'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'deleted' => _e('Deleted'),
            'created_on' => _e('Created on'),
            'created_by' => _e('Created by'),
            'updated_on' => _e('Updated on'),
            'updated_by' => _e('Updated by'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new MenuItems());
    }
    
}
