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
class LetterForward extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 10; // 10 Mb

    public $fileExtension = 'pdf, doc, docx';

    const UPLOADS_FOLDER = 'uploads/document/letter-forward/';

    const VIEW_TYPE_FALSE = 0;
    const VIEW_TYPE_TRUE = 1;


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
        return 'letter_forward';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['letter_id', 'user_id', 'description', 'start_date' , 'end_date'], 'required'],
            [['description'], 'safe'],
            [['letter_id', 'user_id', 'sent_date', 'start_date' , 'end_date', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['start_date' , 'validateDates']
        ];
    }

    public function validateDates($attribute, $params)
    {
        if ($this->start_date >= $this->end_date) {
            $this->addError($attribute, _e('The start time of the task must be less than the end time!'));
        }
    }

    public function fields()
    {
        $fields =  [
            'id',
            'letter_id',
            'user_id',
            'start_date',
            'description',
            'start_date',
            'end_date',
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
            'letterForwardItem',
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

    public function getLetterForwardItem()
    {
        return $this->hasMany(LetterForwardItem::className(), ['letter_forward_id' => 'id'])->where(['is_deleted' => 0]);
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

        if ($model->save(false)) {
            if (isset($post['users'])) {
                $users = json_decode($post['users']);
                foreach ($users as $userIds) {
                    foreach ($userIds as $userId) {
                        $new = new LetterForwardItem();
                        $new->letter_id = $model->letter_id;
                        $new->letter_forward_id = $model->id;
                        $new->user_id = $userId;
                        if (!$new->validate()) {
                            $errors[] = $new->errors;
                        } else {
                            $new->save(false);
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
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
        $time = time();
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->status == 1) {
            $model->sent_date = $time;
        }

        if ($model->save(false)) {
            if (isset($post['users'])) {
                $users = json_decode($post['users']);
                foreach ($users as $userIds) {
                    foreach ($userIds as $userId) {
                        $new = new LetterForwardItem();
                        $new->letter_id = $model->letter_id;
                        $new->letter_forward_id = $model->id;
                        $new->user_id = $userId;
                        if (!$new->validate()) {
                            $errors[] = $new->errors;
                        } else {
                            $new->save(false);
                        }
                    }
                }
            }
        }

        if ($model->status == 1) {
            $letterForwardItems = $model->letterForwardItem;
            if (count($letterForwardItems) > 0) {
                foreach ($letterForwardItems as $letterForwardItem) {
                    if ($letterForwardItem->status == 0) {
                        $letterForwardItem->status = 1;
                        $letterForwardItem->sent_date = $time;
                        $letterForwardItem->save(false);
                    }
                }
            }
        }

        if (count($errors) == 0) {
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
