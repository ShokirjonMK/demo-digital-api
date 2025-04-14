<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use \yii\db\ActiveRecord;


/**
 * This is the model class for table "instruction".
 *
 * @property int $id
 * @property string $file_url
 * @property string $key
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */

class Instruction extends ActiveRecord
{
    use ResourceTrait;

    public static $selected_language = 'uz';

    const UPLOADS_FOLDER = 'instruction/';
    public $file;
    public $videoMaxSize = 1024 * 1024 * 100; // 100 Mb

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'instruction';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['file_url', 'key'], 'string', 'max' => 255],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'mp4,3gp,avi,mov,m4v,mpeg,mpg,png,jpeg,jpg,pdf', 'maxSize' => $this->videoMaxSize],
            ['key', 'unique', 'message' => 'bu ma`lumot allaqachon saqlangan'],
            [
                [
                    'status',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                ], 'integer'
            ],
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
            'id' => _e('ID'),
            'file_url' => _e('File_url'),
            'key' => _e('Key'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'file_url',
            'key',
            'status',
            'is_deleted',
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
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getInfoRelation()
    {
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }


    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->file = UploadedFile::getInstancesByName('file');
        if ($model->file) {
            $model->file = $model->file[0];
            $videoFile = $model->uploadFile();
            if ($videoFile) {
                $model->file_url = $videoFile;
            } else {
                $errors[] = $model->errors;
            }
        }

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }
        $has_error = Translate::checkingAll($post);
        if ($has_error['status']) {

            if ($model->save()) {

                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }

                $transaction->commit();
                return true;
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
        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
            if ($model->save()) {
                $oldFile = $model->file_url;

                $model->file = UploadedFile::getInstancesByName('file');
                if ($model->file) {
                    $model->file = $model->file[0];
                    $file_url = $model->uploadFile();
                    if ($file_url) {
                        $model->deleteFile($oldFile);
                        $model->file_url = $file_url;
                    } else {
                        $errors[] = $model->errors;
                    }
                }

                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
                if ($model->save()) {
                    $model->deleteFile($oldFile);
                } else {
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public function uploadFile()
    {
        if ($this->validate()) {
            $folder = self::UPLOADS_FOLDER . $this->file->extension . '/';
            if (!file_exists(UPLOADS_PATH  . $folder)) {
                mkdir(UPLOADS_PATH  . $folder, 0777, true);
            }
            $fileName = \Yii::$app->security->generateRandomString(6) . '.' . $this->file->extension;
            $miniUrl = $folder . $fileName;
            $url = UPLOADS_PATH . $miniUrl;
            $this->file->saveAs($url, false);
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
