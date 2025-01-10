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
class LetterForwardItem extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const VIEW_TYPE_TRUE = 1;
    const VIEW_TYPE_FALSE = 0;


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
        return 'letter_forward_item';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['letter_forward_id', 'user_id'], 'required'],
            [['title'], 'string' , 'max' => 255],
            [['description'], 'safe'],
            [['letter_id', 'letter_forward_id', 'user_id','sent_date', 'order', 'view_type', 'view_date', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
            [['letter_forward_id'], 'exist', 'skipOnError' => true, 'targetClass' => LetterForward::className(), 'targetAttribute' => ['letter_forward_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'letter_id',
            'letter_forward_id',
            'user_id',
//            'title',
//            'description',
            'view_type',
            'view_date',
            'sent_date',
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
            'user',
            'letterForward',
            'letterReply',
            'letterReplyHistory',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }
    public function getLetter()
    {
        return $this->hasOne(Letter::className(), ['id' => 'letter_id']);
    }

    public function getLetterForward()
    {
        return $this->hasOne(LetterForward::className(), ['id' => 'letter_forward_id']);
    }

    public function getLetterReply()
    {
        return $this->hasOne(LetterReply::className(), ['letter_forward_item_id' => 'id'])->orderBy('id desc')->where(['is_deleted' => 0]);
    }

    public function getLetterReplyHistory()
    {
        if ($this->letterReply != null) {
            return $this->hasMany(LetterReply::className(), ['letter_forward_item_id' => 'id'])
                ->where(['is_deleted' => 0])
                ->andWhere(['!=' ,'id' , $this->letterReply->id]);
        }
        return null;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->letter_id = $model->letterForward->letter_id;

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->letter_id = $model->letterForward->letter_id;

        if ($model->status == 1) {
            $model->sent_date = time();
        }
        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
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
