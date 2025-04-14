<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "direction_info".
 *
 * @property int $info_id
 * @property int|null $direction_id
 * @property string $language
 * @property string|null $name
 * @property string|null $description
 */
class DirectionInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'direction_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id'], 'integer'],
            [['language'], 'required'],
            [['description'], 'string'],
            [['language'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'info_id' => 'Info ID',
            'direction_id' => 'Direction ID',
            'language' => 'Languages',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }
}
