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
class DocumentNotification extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 10; // 10 Mb
    public $fileExtension = 'pdf , doc, docx';

    const UPLOADS_FOLDER = 'uploads/document/notification/';

    const STATUS_FALSE = 0;
    const STATUS_TRUE = 1;
    const IS_OK_DEFAULT = 0;
    const IS_OK_TRUE = 1;
    const IS_OK_CANCEL = 2;

    const TYPE_DEFAULT = 0;
    const HR_TRUE = 2;
    const HR_FALSE = 1;
    const RECTOR_FALSE = 3;
    const RECTOR_TRUE = 4;

    const COMMAND_TYPE_TRUE = 1;
    const COMMAND_TYPE_FALSE = 0;


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
        return 'document_notification';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['ids'], 'unique'],
            [['user_id', 'description'], 'required'],
            [['file'], 'string' , 'max' => 255],
            [['description','message'], 'safe'],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
            [['signature_user_id','returned_user_id','command_type', 'hr_sent_time','user_id', 'type' ,'sent_time', 'is_ok' , 'is_ok_time' ,'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['signature_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['signature_user_id' => 'id']],
            [['returned_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['returned_user_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'ids',
            'command_type',
            'description',
            'file',
            'user_id',
            'signature_user_id',
            'is_ok',
            'is_ok_time',
            'sent_time',

            'hr_sent_time',
            'returned_user_id',
            'message',

            'type',
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
            'user',
            'signature',
            'body',
            'info',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getSignature()
    {
        return $this->hasOne(User::className(), ['id' => 'signature_user_id']);
    }

    public function getBody()
    {
        return $this->hasOne(DocumentNotificationBody::className(), ['document_notification_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getInfo()
    {
        return $this->hasMany(DocumentNotificationInfo::className(), ['document_notification_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->user_id = current_user_id();
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('file');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $url = $model->upload($model->upload);
            if ($url) {
                $model->file = $url;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->type = self::TYPE_DEFAULT;
        $model->is_ok = self::IS_OK_DEFAULT;
        $model->status = self::STATUS_FALSE;
        $model->returned_user_id = null;
        $model->message = null;
        if ($model->save(false)) {
            $model->ids = 'doc-n-'.$model->id;
            $model->save(false);
            $body = new DocumentNotificationBody();
            $body->document_notification_id = $model->id;
            $qr_info = fullName(current_user_profile($model->user_id));
            $body->name_user = $qr_info;
            if (isset($post['body'])) {
                $body->body = $post['body'];
            }
            $body->save(false);
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

        $body = $model->body;
        if (isset($post['body'])) {
            $body->body = $post['body'];
            $body->save(false);
        }

        $model->upload = UploadedFile::getInstancesByName('file');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $url = $model->upload($model->upload);
            if ($url) {
                $model->file = $url;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->user_id == current_user_id() || isRole('admin')) {
            if ($model->status == self::STATUS_TRUE) {
                $model->sent_time = $time;
                $qr_info = profileFullName(current_user_profile($model->user_id));
                $body->qr_code_user = qrCodeMK($qr_info);
                $body->save(false);
            }
            $model->type = self::TYPE_DEFAULT;
            $model->is_ok = self::IS_OK_DEFAULT;
        }
        $model->save(false);
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function commandType($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['command_type']) && ($post['command_type'] == self::COMMAND_TYPE_TRUE || $post['command_type'] == self::COMMAND_TYPE_FALSE)) {
            $model->command_type = $post['command_type'];
        } else {
            $errors[] = ['command_type' => _e("Type input required!")];
        }

        if (!$model->validate()) {
            $errors[] = $model->errors;
        }
        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function hrUpdateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['type']) && ($post['type'] == self::HR_FALSE || $post['type'] == self::HR_TRUE)) {
            $model->type = $post['type'];
            if (isset($post['message'])) {
                $model->message = $post['message'];
            }
        } else {
            $errors[] = ['type' => _e("Type input required!")];
        }

        if ($model->type == self::HR_FALSE) {
            if ($model->message == null) {
                $errors[] = ['message' => _e("Message input required!")];
            }
            $model->returned_user_id = current_user_id();
            $model->is_ok = self::IS_OK_CANCEL;
            $model->is_ok_time = $time;
        } elseif ($model->type == self::HR_TRUE) {
            if (isset($post['signature_user_id'])) {
                $model->signature_user_id = $post['signature_user_id'];
                $model->hr_sent_time = $time;
            }
        }

        if (!$model->validate()) {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function signatureUpdateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['type']) && ($post['type'] == self::RECTOR_FALSE || $post['type'] == self::RECTOR_TRUE)) {
            $model->type = $post['type'];
            if (isset($post['message'])) {
                $model->message = $post['message'];
            }
        } else {
            $errors[] = ['type' => _e("Type input required!")];
        }

        if ($model->type == self::RECTOR_TRUE) {
            $model->is_ok = self::IS_OK_TRUE;
            $model->is_ok_time = $time;
            $body = $model->body;
            if ($body != null && $body->body != null) {
                $signature_name = fullName(current_user_profile($model->signature_user_id));
                $qr_info = profileFullName(current_user_profile($model->signature_user_id));
                $body->name_signature = $signature_name;
                $body->qr_code_signature = qrCodeMK($qr_info);
                $body->save(false);
            } else {
                $errors[] = _e("There is no signature smoking document.");
            }
        } elseif ($model->type == self::RECTOR_FALSE) {
            if ($model->message == null) {
                $errors[] = ['message' => _e("Message input required!")];
            }
            $model->returned_user_id = $model->signature_user_id;
            $model->is_ok = self::IS_OK_CANCEL;
            $model->is_ok_time = $time;
        }

        if (!$model->validate()) {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->upload->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
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
