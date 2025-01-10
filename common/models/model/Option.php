<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $test_id
 * @property string $file
 * @property string $text
 * @property int $level
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction[] $directions
 * @property EduPlan[] $eduPlans
 * @property Kafedra[] $kafedras
 */
class Option extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public $image;

    public $fileMaxSize = 1024 * 1024 * 2; // 2 Mb

    const IS_CORRECT_TRUE = 1;

    const IS_CORRECT_FALSE = 0;

    const UPLOADS_FOLDER = 'uploads/options-images/';

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
        return 'option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['test_id'] , 'required'
            ],
            [['text'] , 'safe'],
            [['file'] , 'string' , 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->fileMaxSize],
            [['test_id','is_correct','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        if (!isRole('student')) {
            $fields =  [
                'id',
                'test_id',
                'text',
                'file',
                'is_correct',

                'order',
                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ];
        } else {
            $fields =  [
                'id',
                'test_id',
                'text',
                'file',
            ];
        }

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'test',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->is_correct == self::IS_CORRECT_TRUE) {
            Option::updateAll(['is_correct' => 0], ['test_id' => $model->test_id]);
        }

        $model->image = UploadedFile::getInstancesByName('file');
        if ($model->image) {
            $model->image = $model->image[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if (count($errors) == 0) {
            $model->save();
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->is_correct == self::IS_CORRECT_TRUE) {
            Option::updateAll(['is_correct' => 0], ['test_id' => $model->test_id]);
        }

        $oldFileUrl = $model->file;
        $model->image = UploadedFile::getInstancesByName('file');
        if ($model->image) {
            $model->image = $model->image[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->save()) {
            if (file_exists($oldFileUrl)) {
                unlink($oldFileUrl);
            }
        } else {
            if (file_exists($fileUrl)) {
                unlink($fileUrl);
            }
            $errors[] = $model->errors;
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(12) . '.' . $this->image->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->image->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
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
