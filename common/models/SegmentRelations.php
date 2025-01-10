<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_segments_relation".
 *
 * @property int $relation_id
 * @property int|null $segment_id
 * @property string|null $segment_type
 * @property int|null $content_id
 */
class SegmentRelations extends \base\libs\RedisDB
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'site_segments_relation';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['segment_type'], 'string'],
            [['segment_id', 'content_id'], 'integer'],
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
            'relation_id' => 'Relation ID',
            'segment_id' => 'Segment ID',
            'segment_type' => 'Segment type',
            'content_id' => 'Content ID',
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new Segment(), new SegmentFields(), new SegmentInfos());
    }
    
}
