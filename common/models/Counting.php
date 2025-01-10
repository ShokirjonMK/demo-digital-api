<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "counting".
 *
 * @property int $count_id
 * @property int|null $item_id
 * @property string $item_type
 * @property int|null $item_count
 */
class Counting extends \yii\db\ActiveRecord
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'counting';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['item_id', 'item_count'], 'integer'],
            [['item_id', 'item_type'], 'required'],
            [['item_type'], 'string', 'max' => 150],
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
            'count_id' => 'ID',
            'item_id' => 'Item ID',
            'item_type' => 'Item Type',
            'item_count' => 'Item Count',
        ];
    }
}
