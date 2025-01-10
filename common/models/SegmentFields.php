<?php

namespace common\models;

/**
 * This is the model class for table "site_segments_field".
 *
 * @property int $field_id
 * @property int|null $segment_id
 * @property string $field_key
 * @property string|null $field_value
 */
class SegmentFields extends \base\libs\RedisDB
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'site_segments_field';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['segment_id'], 'integer'],
            [['field_key'], 'required'],
            [['field_value'], 'string'],
            [['field_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * Attribute lables
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'field_id' => 'Field ID',
            'segment_id' => 'Segment ID',
            'field_key' => 'Field Key',
            'field_value' => 'Field Value',
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new Segment(), new SegmentInfos(), new SegmentRelations());
    }
    
}
