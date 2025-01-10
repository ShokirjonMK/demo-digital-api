<?php

namespace common\models;

/**
 * This is the model class for table "site_content_info".
 *
 * @property int $info_id
 * @property int|null $content_id
 * @property string $language
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $content
 * @property string $content_blocks
 * @property string $icon
 * @property string $image
 * @property string $cover_image
 * @property string $meta
 */
class ContentInfos extends \base\libs\RedisDB
{
    public $meta_title;
    public $focus_keywords;
    public $meta_description;

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'site_content_info';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'content_id', 'language'], 'required'],
            [['title', 'slug', 'icon', 'image', 'cover_image'], 'string', 'max' => 255],
            [['language', 'description', 'meta_title', 'focus_keywords', 'meta_description'], 'string'],
            [['content', 'description'], 'default', 'value' => ''],
            [['icon', 'image', 'cover_image'], 'default', 'value' => ''],
            [['meta_title', 'meta_description', 'focus_keywords'], 'default', 'value' => ''],
            [['meta', 'content_blocks'], 'default', 'value' => []],
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
            'content_id' => _e('Content ID'),
            'language' => _e('Languages'),
            'title' => _e('Title'),
            'slug' => _e('Slug'),
            'description' => _e('Description'),
            'content' => _e('Content'),
            'content_blocks' => _e('Content blocks'),
            'icon' => _e('Icon'),
            'image' => _e('Image'),
            'cover_image' =>  _e('Cover image'),
            'meta' =>  _e('Meta'),
            'meta_title' => _e('Meta title'),
            'meta_description' => _e('Meta description'),
            'focus_keywords' => _e('Focus keywords'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new Content(), new ContentFields(), new SegmentRelations());
    }
    
    /**
     * Get content
     *
     * @return void
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }

    /**
     * Get content model
     *
     * @return void
     */
    public function getModel()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
}
