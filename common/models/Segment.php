<?php

namespace common\models;

/**
 * This is the model class for table "site_segments".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $settings
 * @property string $template
 * @property string $layout
 * @property string $view
 * @property string $resource_type
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property int $cacheable
 * @property int $searchable
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class Segment extends \base\libs\RedisDB
{
    public $_slug;
    public $default_title;
    public $posts_column;
    public $posts_sorting;
    public $products_sorting;
    public $posts_per_page;
    public $products_per_page;
    public $products_view_type;
    public $subcategories_view_type;

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'site_segments';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['created_on', 'updated_by'], 'safe'],
            [['sort', 'type'], 'required'],
            [['parent_id', 'sort', 'status', 'deleted', 'searchable', 'cacheable', 'created_by', 'updated_by'], 'integer'],
            [['template', 'layout', 'view', 'posts_column', 'posts_sorting', 'posts_per_page', 'subcategories_view_type', 'products_view_type', 'products_sorting', 'products_per_page'], 'string'],
            [['searchable', 'cacheable'], 'default', 'value' => 1],
            [['parent_id', 'status', 'sort', 'deleted'], 'default', 'value' => 0],
            [['template', 'layout', 'view', 'resource_type'], 'default', 'value' => ''],
            [['settings'], 'default', 'value' => []],
            ['created_on', 'default', 'value' => date('Y-m-d H:i:s')],
            ['updated_on', 'default', 'value' => date('Y-m-d H:i:s')],
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
            'type' => _e('Type'),
            'settings' =>  _e('Settings'),
            'parent_id' => _e('Parent ID'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'deleted' => _e('Deleted'),
            'cacheable' => _e('Cacheable'),
            'searchable' => _e('Searchable'),
            'created_on' => _e('Created on'),
            'created_by' => _e('Created by'),
            'updated_on' => _e('Updated on'),
            'updated_by' => _e('Updated by'),
            'template' => _e('Template'),
            'layout' => _e('Layout'),
            'view' => _e('View file'),
            'resource_type' => _e('Resource type'),
            'posts_column' => _e('Posts column'),
            'posts_sorting' => _e('Posts sorting'),
            'posts_per_page' => _e('Posts per page'),
            'childs_view_type' => _e('Childs view type'),
            'products_view_type' => _e('Products view type'),
            'products_sorting' => _e('Posts sorting'),
            'products_per_page' => _e('Posts per page'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new SegmentFields(), new SegmentInfos(), new SegmentRelations());
    }
    
    /**
     * Get content info
     *
     * @return void
     */
    public function getInfo()
    {
        return $this->hasOne(SegmentInfos::className(), ['segment_id' => 'id']);
    }

    /**
     * Get parent
     *
     * @return void
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * Get parent
     *
     * @return void
     */
    public function getParentInfo()
    {
        return $this->hasOne(SegmentInfos::className(), ['segment_id' => 'id'])
            ->via('parent');
    }

    /**
     * Resource types
     *
     * @return array
     */
    public static function resourceTypes()
    {
        return array(
            'default' => _e('Default'),
        );
    }

    /**
     * Segment types
     *
     * @return array
     */
    public static function segmentTypes()
    {
        $default_types = [
            array(
                'key' => 'post_tag',
                'slug' => 'post-tag',
                'slug_generator' => 'self',
                'items_with_parent' => false,
                'image_fields' => array('icon', 'image', 'cover_image'),
                'lexicon' => array(
                    'title' => _e('Tags'),
                    'menu_title' => _e('Tags'),
                    'new_item_title' => _e('New tag'),
                    'edit_item_title' => _e('Edit tag'),
                    'edit_item_title2' => _e('Edit tag: {title}'),
                    'successfully_created' => _e('The tag was created successfully.'),
                    'successfully_updated' => _e('The tag has been successfully updated.'),
                    'back_to_message' => _e('Back to tags'),
                    'not_found_message' => _e('Tags not found!'),
                    'not_found_message_full' => _e('The tag you were looking for does not exist, unavailable for you or deleted.'),
                ),
            ),
            array(
                'key' => 'post_category',
                'slug' => 'post-category',
                'slug_generator' => 'self',
                'items_with_parent' => true,
                'image_fields' => array('icon', 'image', 'cover_image'),
                'lexicon' => array(
                    'title' => _e('Categories'),
                    'menu_title' => _e('Categories'),
                    'new_item_title' => _e('New category'),
                    'edit_item_title' => _e('Edit category'),
                    'edit_item_title2' => _e('Edit category: {title}'),
                    'successfully_created' => _e('The category was created successfully.'),
                    'successfully_updated' => _e('The category has been successfully updated.'),
                    'back_to_message' => _e('Back to categories'),
                    'not_found_message' => _e('Categories not found!'),
                    'not_found_message_full' => _e('The category you were looking for does not exist, unavailable for you or deleted.'),
                ),
            ),
        ];

        $segment_types = theme_configs('segment_types');

        if (is_array($segment_types) && $segment_types) {
            return array_merge($segment_types, $default_types);
        }

        return $default_types;
    }
}
