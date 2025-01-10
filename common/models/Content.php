<?php

namespace common\models;

/**
 * This is the model class for table "site_content".
 *
 * @property int $id
 * @property string $type
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
class Content extends \base\libs\RedisDB
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
    public $segment_relations;

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'site_content';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['created_on', 'updated_by', 'segment_relations'], 'safe'],
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
            'id' => _e('ID'),
            'title' => _e('Title'),
            'settings' => _e('Settings'),
            'parent_id' => _e('Parent'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'deleted' => _e('Deleted'),
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
            'subcategories_view_type' => _e('Subcategories view type'),
            'products_view_type' => _e('Products view type'),
            'products_sorting' => _e('Products sorting'),
            'products_per_page' => _e('Products per page'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new ContentFields(), new ContentInfos(), new SegmentRelations());
    }
    
    /**
     * Get content info
     *
     * @return void
     */
    public function getInfo()
    {
        return $this->hasOne(ContentInfos::className(), ['content_id' => 'id']);
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
        return $this->hasOne(ContentInfos::className(), ['content_id' => 'id'])
            ->via('parent');
    }

    /**
     * Resource types
     *
     * @return array
     */
    public static function resourceTypes($content_type = null)
    {
        $output = array(
            'default' => _e('Default'),
        );

        if (is_null($content_type) || $content_type == 'page') {
            $output = array(
                'default' => _e('Default'),
                'home_page' => _e('Home page'),
                'start_page' => _e('Start page'),
                'error_page' => _e('Error page'),
            );

            $content_types = self::contentTypes();

            if ($content_types) {
                foreach ($content_types as $content_type) {
                    $key = array_value($content_type, 'key');
                    $lexicon = array_value($content_type, 'lexicon');
                    $title = array_value($lexicon, 'title');

                    if ($key && $key != 'page' && $title) {
                        $output[$key] = $title;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Content types
     *
     * @return array
     */
    public static function contentTypes()
    {
        $default_types = [
            array(
                'key' => 'page',
                'slug' => 'page',
                'slug_generator' => 'self',
                'items_with_parent' => true,
                'image_fields' => array('image', 'cover_image'),
                'segments' => array(),
                'lexicon' => array(
                    'title' => _e('Pages'),
                    'menu_title' => _e('Pages'),
                    'new_item_title' => _e('New page'),
                    'edit_item_title' => _e('Edit page'),
                    'edit_item_title2' => _e('Edit page: {title}'),
                    'successfully_created' => _e('The page was created successfully.'),
                    'successfully_updated' => _e('The page has been successfully updated.'),
                    'back_to_message' => _e('Back to pages'),
                    'not_found_message' => _e('Page not found.'),
                    'not_found_message_full' => _e('The page you were looking for does not exist, unavailable for you or deleted.'),
                ),
            ),
            array(
                'key' => 'post',
                'slug' => 'post',
                'slug_generator' => 'self',
                'items_with_parent' => false,
                'image_fields' => array('image', 'cover_image'),
                'segments' => array(
                    'post_category' => array(
                        'label' => _e('Categories'),
                        'input' => 'select', // select, select-multiple
                        'required' => 0,
                    ),
                    'post_tag' => array(
                        'label' => _e('Tags'),
                        'input' => 'select-multiple', // select, select-multiple
                        'required' => 0,
                    ),
                ),
                'lexicon' => array(
                    'title' => _e('Posts'),
                    'menu_title' => _e('Posts'),
                    'new_item_title' => _e('New post'),
                    'edit_item_title' => _e('Edit post'),
                    'edit_item_title2' => _e('Edit post: {title}'),
                    'successfully_created' => _e('The post was created successfully.'),
                    'successfully_updated' => _e('The post has been successfully updated.'),
                    'back_to_message' => _e('Back to posts'),
                    'not_found_message' => _e('Post not found.'),
                    'not_found_message_full' => _e('The post you were looking for does not exist, unavailable for you or deleted.'),
                ),
            )
        ];

        $content_types = theme_configs('content_types');

        if (is_array($content_types) && $content_types) {
            return array_merge($content_types, $default_types);
        }

        return $default_types;
    }
}
