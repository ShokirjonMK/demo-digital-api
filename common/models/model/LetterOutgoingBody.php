<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $description
 * @property int $letter_id
 * @property int $documant_weight_id
 * @property int $important_level_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Building $building
 * @property TimeTable1[] $timeTables
 */
class LetterOutgoingBody extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;


    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter_outgoing_body';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['letter_outgoing_id', 'body'], 'required'],
            [['body'], 'safe'],
            [['letter_outgoing_id','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['letter_outgoing_id'], 'exist', 'skipOnError' => true, 'targetClass' => LetterOutgoing::className(), 'targetAttribute' => ['letter_outgoing_id' => 'id']],
        ];
    }
    public function fields()
    {
        $fields =  [
            'id',
            'letter_outgoing_id',
            'body',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
        return $fields;
    }


    public function extraFields()
    {
        $extraFields =  [
            'letter',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getLetterOutgoing()
    {
        return $this->hasOne(LetterOutgoing::className(), ['id' => 'letter_outgoing_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
