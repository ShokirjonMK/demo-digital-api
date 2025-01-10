<?php

namespace common\models;

/**
 * This is the model class for table "menu_items".
 *
 * @property int $id
 * @property string $language
 * @property string $data
 * @property string $item_id
 * @property string $type
 * @property string $icon
 * @property string $image
 * @property int $parent_id
 * @property string $group_key
 * @property int $sort
 */
class MenuItems extends \base\libs\RedisDB
{
    public $name;
    public $attrs;
    public $class_name;
    public $snippet;
    public $icon;
    public $image;
    public $link;
    public $link_target;

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'menu_items';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['group_key'], 'required'],
            [['link', 'link_target', 'attrs', 'class_name', 'snippet', 'icon', 'image', 'language'], 'string'],
            [['parent_id', 'sort', 'item_id'], 'integer'],
            [['name', 'group_key'], 'string', 'max' => 255],
            [['parent_id', 'item_id'], 'default', 'value' => 0],
            [['data', 'type', 'icon', 'image'], 'default', 'value' => ''],
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
            'name' => _e('Name'),
            'data' => _e('Data'),
            'item_id' => _e('Item ID'),
            'type' => _e('Menu type'),
            'icon' => _e('Icon'),
            'image' => _e('Image'),
            'parent_id' => _e('Parent ID'),
            'group_key' => _e('Group key'),
            'sort' =>  _e('Sort'),
            'attrs' => _e('Attributes'),
            'class_name' => _e('Class name'),
            'snippet' => _e('Snippet'),
            'link' => _e('Link'),
            'link_target' => _e('Link target'),
        ];
    }
    
    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new MenuGroup());
    }
    
}
