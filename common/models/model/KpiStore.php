<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property string $name
 * @property int $kpi_category_id
 * @property string $date
 * @property string $file
 * @property int $subject_category_id
 * @property string $count_of_copyright
 * @property string $link
 * @property string $ball
 * @property int $user_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class KpiStore extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const UPLOADS_FOLDER = 'uploads/kpi_store/';
    public $kpi_file;
    public $kpiFileMaxSize = 1024 * 1024 * 100; // 24 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'kpi_category_id',
                    'user_id',
                ], 'required'
            ],
            [
                [
                    'kpi_category_id',
                    'subject_category_id',
                    'count_of_copyright',
                    'user_id',
                    'archived',
                ], 'integer'
            ],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [
                [
                    'ball',
                ], 'double'
            ],
            [
                [
                    'file',
                    'link',
                ], 'string', 'max' => 255
            ],

            // [
            //     [
            //         'fields',
            //     ], 'json'
            // ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['kpi_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => KpiCategory::className(), 'targetAttribute' => ['kpi_category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],

            [['kpi_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,mp3,ogg,dsd,aac,alac,wma,flac,mp4,mov,wmv,flv,avi,mkv', 'maxSize' => $this->kpiFileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'kpi_category_id' => _e('kpi_category_id'),
            'date' => _e('date'),
            'file' => _e('file'),
            'subject_category_id' => _e('subject_category_id'),
            'count_of_copyright' => _e('count_of_copyright'),
            'link' => _e('link'),
            'ball' => _e('ball'),
            'user_id' => _e('user_id'),

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
        $fields =  [
            'id',
            'user_id',
            'kpi_category_id',
            'subject_category_id',
            'date',
            'file',
            'count_of_copyright',
            'link',
            'ball',

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
            'kpiCategory',
            'subjectCategory',

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
    public function getKpiCategory()
    {
        return $this->hasOne(KpiCategory::className(), ['id' => 'kpi_category_id']);
    }
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $model->user_id = current_user_id();
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            // kpi file saqlaymiz
            $model->kpi_file = UploadedFile::getInstancesByName('kpi_file');
            if ($model->kpi_file) {
                $model->kpi_file = $model->kpi_file[0];
                $kpiFileUrl = $model->uploadFile();
                if ($kpiFileUrl) {
                    $model->file = $kpiFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

        }
        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // kpi file saqlaymiz
        $model->kpi_file = UploadedFile::getInstancesByName('kpi_file');
        if ($model->kpi_file) {
            $model->kpi_file = $model->kpi_file[0];
            $kpiFileUrl = $model->uploadFile();
            if ($kpiFileUrl) {
                $model->file = $kpiFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***
        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        if (!$model->save()) {
            $errors[] = $model->getErrorSummary(true);
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
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


    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->user_id . "_" . $this->kpi_category_id . "_" . time() . '.' . $this->kpi_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->kpi_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }


    // 
    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
        return true;
    }
}
