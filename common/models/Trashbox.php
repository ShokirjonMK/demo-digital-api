<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "trashbox".
 *
 * @property int $id
 * @property int $user_id
 * @property int $res_id
 * @property string|null $type
 * @property string|null $data
 * @property int $created_on
 */
class Trashbox extends \yii\db\ActiveRecord
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'trashbox';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['created_on'], 'safe'],
            [['type', 'data'], 'string'],
            [['user_id', 'res_id'], 'integer'],
            [['user_id', 'data', 'type'], 'required'],
            [['res_id'], 'default', 'value' => 0],
            [['created_on'], 'default', 'value' => date('Y-m-d H:i:s')],
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
            'user_id' => 'User ID',
            'type' => 'Type',
            'data' => 'data',
            'created_on' => 'Created date',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Set item
     *
     * @param array $args
     * @return void
     */
    public static function setItem($args)
    {
        if (is_array($args) && $args) {
            $model = new Trashbox();

            if ($model) {
                $model->res_id = 0;
                $model->type = '';
                $model->data = '';
                $model->created_on = date('Y-m-d H:i:s');
                $model->user_id = Yii::$app->user->getId();

                if (isset($args['user_id']) && is_numeric($args['user_id']) && $args['user_id'] > 0) {
                    $model->user_id = $args['user_id'];
                }

                if (isset($args['res_id']) && is_numeric($args['res_id'])) {
                    $model->res_id = $args['res_id'];
                }

                if (isset($args['type'])) {
                    $model->type = $args['type'];
                }

                if (isset($args['data'])) {
                    $model->data = $args['data'];
                }

                if (isset($args['created_on']) && $args['created_on']) {
                    $model->created_on = $args['created_on'];
                }

                $model->save(false);
            }
        }
    }
}
