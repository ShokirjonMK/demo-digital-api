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
class DocumentDecreeBody extends \yii\db\ActiveRecord
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
        return 'document_decree_body';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['document_decree_id', 'body'], 'required'],
            [['body','qr_code_signature','qr_code_user'], 'safe'],
            [['name_user','name_signature'], 'string' , 'max' => 255],
            [['document_decree_id','user_id','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['document_decree_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentDecree::className(), 'targetAttribute' => ['document_decree_id' => 'id']],
//            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }
    public function fields()
    {
        $fields =  [
            'id',
            'document_decree_id',
            'body',
            'name_user',
            'name_signature',
            'qr_code_signature',
            'qr_code_user',
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
