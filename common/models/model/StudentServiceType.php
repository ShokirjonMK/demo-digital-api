<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%student_service_type}}".
 *
 * @property int $id
 * @property int|null $type 1- auto 2-manual
 * @property int|null $category 1- murojaat, 2-kategoriya turlari uchun
 * @property int|null $type_id o`zini id si category nomini olish uchun
 * @property string|null $text
 * @property string|null $file
 * @property string|null $link
 * @property int|null $order Order of the item
 * @property int|null $status Status of the item (1 = active, 0 = inactive)
 * @property int|null $is_deleted Is the item deleted (0 = no, 1 = yes)
 * @property int $created_at Creation timestamp
 * @property int $updated_at Update timestamp
 * @property int $created_by ID of the user who created the record
 * @property int $updated_by ID of the user who last updated the record
 * @property int $archived Is the item archived (0 = no, 1 = yes)
 *
 * @property StudentServiceType[] $studentServiceTypes
 * @property StudentServiceType $type0
 */
class StudentServiceType extends ActiveRecord
{
    public static $selected_language = 'uz';

    const TYPE_MANUAL = 1;
    const TYPE_AUTO = 2;

    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/student_service_type/';
    public $upload_file;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb


    public static function tableName()
    {
        return '{{%student_service_type}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['type', 'category', 'type_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['text'], 'string'],
            [['file', 'link'], 'string', 'max' => 255],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentServiceType::className(), 'targetAttribute' => ['type_id' => 'id']],

            [['text'], 'required', 'when' => function ($model) {
                return $model->type == self::TYPE_AUTO;
            }, 'whenClient' => "function (attribute, value) {
                return $('#studentservicetype-type').val() == " . self::TYPE_AUTO . ";
            }"],


            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->fileMaxSize],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => _e('Type'),
            'category' => _e('Category'),
            'type_id' => _e('Parent Type ID'),
            'text' => _e('Text'),
            'file' => _e('File'),
            'link' => _e('Link'),
            'order' => _e('Order'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'archived' => _e('Archived'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'type',
            'category',
            'type_id',
            'text',
            'file',
            'link',
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
            'description',
            'categoryName',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getCategoryType()
    {
        return $this->hasOne(self::className(), ['id' => 'type_id'])
            ->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getCategoryName()
    {
        return $this->categoryType->translate->name ?? '';
    }

    public function getInfoRelation()
    {
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
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

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {
            if ($model->save()) {
                // upload file saqlaymiz
                $model->upload_file = UploadedFile::getInstancesByName('upload_file');
                if ($model->upload_file) {
                    $model->upload_file = $model->upload_file[0];
                    $uploadFileUrl = $model->uploadFile();
                    if ($uploadFileUrl) {
                        $model->file = $uploadFileUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
                if (count($errors) == 0) {
                    if ($model->save()) {
                        $transaction->commit();
                        return true;
                    } else {
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        // question file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $uploadFileUrl = $model->uploadFile();
            if ($uploadFileUrl) {
                $model->file = $uploadFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
                if (count($errors) == 0) {
                    if ($model->save()) {
                        $transaction->commit();
                        return true;
                    } else {
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function typeList()
    {
        return [
            self::TYPE_AUTO => _e('Auto'),
            self::TYPE_MANUAL => _e('Manual'),
        ];
    }

    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->upload_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
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
