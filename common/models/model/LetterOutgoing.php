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
class LetterOutgoing extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 10; // 10 Mb

    public $fileExtension = 'pdf, doc, docx';

    const UPLOADS_FOLDER = 'uploads/document/letter-outgoing';


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
        return 'letter_outgoing';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['letter_id', 'user_id', 'description'], 'required'],
            [['file', 'output_number', 'output_number'], 'string', 'max' => 255],
            [['output_number', 'access_number'], 'unique'],
            [['description','message'], 'safe'],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
            [['letter_id', 'user_id', 'is_ok', 'view_type', 'view_date', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'letter_id',
            'user_id',
            'output_number',
            'access_number',
            'description',
            'is_ok',
            'message',
            'file',
            'view_type',
            'view_date',
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
        $extraFields = [
            'letter',
            'letterOutgoing',
            'body',
            'user',
            'qrCode',
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

    public function getBody()
    {
        return $this->hasOne(LetterOutgoingBody::className(), ['letter_outgoing_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getQrCode()
    {
        return $this->hasMany(QrLetterOutgoing::className(), ['letter_outgoing_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $query = LetterOutgoing::find()
            ->where([
                'letter_id' => $model->letter_id,
                'is_ok' => [Letter::IS_OK_DEFAULT, Letter::IS_OK_TRUE],
                'is_deleted' => 0
            ])
            ->count();
        if ($query > 0) {
            $errors[] = _e("Can't insert new submission!");
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

        if ($model->save(false)) {
            if (isset($post['body'])) {
                $new = new LetterOutgoingBody();
                $new->letter_outgoing_id = $model->id;
                $new->body = $post['body'];
                $new->save(false);
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
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if ($model->save(false)) {
            if (isset($post['body'])) {
                $query = LetterOutgoingBody::findOne([
                    'letter_outgoing_id' => $model->id,
                    'status' => 1,
                    'is_deleted' => 0
                ]);
                if ($query) {
                    $query->body = $post['body'];
                    $query->save(false);
                } else {
                    $new = new LetterOutgoingBody();
                    $new->letter_outgoing_id = $model->id;
                    $new->body = $post['body'];
                    $new->save(false);
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


    public static function isOk($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['is_ok'])) {
            $model->is_ok = $post['is_ok'];
            if (isset($post['message'])) {
                $model->message = $post['message'];
            }
            if ($model->is_ok == Letter::IS_OK_TRUE) {
                $query = QrLetterOutgoing::findOne([
                    'letter_outgoing_id' => $model->id,
                    'status' => 1,
                    'is_deleted' => 0,
                ]);
                if ($query != null) {
                    $query->is_deleted = 1;
                    $query->save(false);
                }
                $qr_info = profileFullName(current_user_profile());
                $newQr = new QrLetterOutgoing();
                $newQr->letter_outgoing_id = $model->id;
                $newQr->letter_id = $model->letter_id;
                $newQr->qr_code = qrCodeMK($qr_info);
                $newQr->save(false);
            }
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
            if (!file_exists(\Yii::getAlias('@api/web' . $folder_name . "/" . self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web' . $folder_name . "/" . self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web' . $folder_name . "/" . self::UPLOADS_FOLDER . $fileName);
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
