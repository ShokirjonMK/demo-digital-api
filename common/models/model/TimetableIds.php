<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "building".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Room[] $rooms
 */
class TimetableIds extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable_ids';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['number'], 'required'],
            [['number'], 'integer'],
            [['number'], 'unique'],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'number',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

        ];

        return $extraFields;
    }


    public static function createItem()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $new = new TimetableIds();
        if ($new->save(false)) {
            $transaction->commit();
            return ['is_ok' => true , 'ids' => $new->id];
        }

        $errors[] = $new->errors;
        $transaction->rollBack();
        return ['is_ok' => false , 'errors' => simplify_errors($errors)];
    }

}
