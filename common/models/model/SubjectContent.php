<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 *
 * @property int $id
 *
 * @property int $name
 * @property int $time
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class SubjectContent extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const TYPE_TEXT = 1;
    const TYPE_FILE = 2;
    const TYPE_IMAGE = 3;
    const TYPE_VIDEO = 4;
    const TYPE_AUDIO = 5;

    public $file_file;
    public $file_image;
    public $file_video;
    public $file_audio;

    const UPLOADS_FOLDER = 'uploads/content_files';
    public $file_textFileMaxSize = "";
    public $file_fileFileMaxSize = 1024 * 1024 * 15; // 5 Mb
    public $file_imageFileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $file_videoFileMaxSize = 1024 * 1024 * 100; // 100 Mb
    public $file_audioFileMaxSize = 1024 * 1024 * 15; // 20 Mb

    public $file_textFileExtentions = 'text';
    public $file_fileFileExtentions = 'pdf,doc,docx,ppt,pptx,zip';
    public $file_imageFileExtentions = 'png,jpg,gimp,bmp,jpeg';
    public $file_videoFileExtentions = 'mp4,avi,mov,mkv,wmv';
    public $file_audioFileExtentions = 'mp3,ogg,m4a,wav';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'subject_topic_id',
//                    'lang_id',
                    'type'
                ],
                'required'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [
                [
                    'user_id',
                    'lang_id',
                    'type',
                    'subject_topic_id',
                    'subject_id',
                    'teacher_access_id',
                ],
                'integer'
            ],
            [
                [
                    'text',
                    'description',
                ],
                'safe'
            ],

            [ 'file', 'string', 'max' => 255 ],
            [ 'file_extension', 'string', 'max' => 50 ],

            [['subject_topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['subject_topic_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['file_file'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->file_fileFileExtentions, 'maxSize' => $this->file_fileFileMaxSize],
            [['file_image'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->file_imageFileExtentions, 'maxSize' => $this->file_imageFileMaxSize],
            [['file_video'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->file_videoFileExtentions, 'maxSize' => $this->file_videoFileMaxSize],
            [['file_audio'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->file_audioFileExtentions, 'maxSize' => $this->file_audioFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Content',
            'type' => 'Type',
            'subject_id' => 'subject_id',
            'user_id' => 'user_id',
            'language_id' => 'language_id',
            'subject_topic_id' => 'subject_topic_id',
            'teacher_access_id' => 'teacher_access_id',
            'description' => 'description',
            'file_url' => "File Url",
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
        $fields = [
            'id',
            'subject_topic_id',
            'type',
            'text',
            'description',
            'file',
            'lang_id',
            'file_extension',

            'user_id',
            'teacher_access_id',

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
            'subject',
            'subjectTopic',
            'subjectCategory',
            'types',
            'teacherAccess',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getSubject()
    {
        return $this->subjectTopic->subject;
    }

    public function getTypes()
    {
        $type = $this->typesArray($this->type);
        return $type;
    }

    public function getSubjectTopic()
    {
        return $this->hasOne(SubjectTopic::className(), ['id' => 'subject_topic_id']);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    public function getSubjectCategory()
    {
        return $this->subjectTopic->subjectCategory;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['order'])) {
            $orderDescOne = SubjectContent::find()
                ->where(['subject_topic_id' => $model->subject_topic_id, 'is_deleted' => 0])
                ->orderBy('order desc')
                ->one();
            if (isset($orderDescOne)) {
                if ($orderDescOne->order+1 < $post['order']) {
                    $model->order = $orderDescOne->order+1;
                } elseif ($orderDescOne->order > $post['order']) {
                    $orderUpdate = SubjectContent::find()->where([
                        'between', 'order', $post['order'], $orderDescOne->order
                    ])
                        ->andWhere([ 'subject_topic_id' => $model->subject_topic_id , 'is_deleted' => 0 ])
                        ->all();
                    if (isset($orderUpdate)) {
                        foreach ($orderUpdate as $orderItem) {
                            $orderItem->order = $orderItem->order + 1;
                            $orderItem->save(false);
                        }
                    }
                } elseif ($orderDescOne->order == $post['order']) {
                    $orderDescOne->order = $orderDescOne->order + 1;
                    $orderDescOne->save(false);
                }
            } else {
                $model->order = 1;
            }
        } else {
            $orderDescOne = SubjectContent::find()
                ->where(['subject_topic_id' => $model->subject_topic_id , 'is_deleted' => 0])
                ->orderBy('order desc')
                ->one();
            if (isset($orderDescOne)) {
                $model->order = $orderDescOne->order + 1;
            } else {
                $model->order = 1;
            }
        }


        $model->type = self::TYPE_TEXT;
        $model->subject_id = $model->subjectTopic->subject_id;
        $model->lang_id = $model->subjectTopic->lang_id;

        /* Fayl Yuklash*/
        $model->file_file = UploadedFile::getInstancesByName('file_file');
        if ($model->file_file) {
            $model->file_file = $model->file_file[0];
            $fileUrl = $model->uploadFile("file_file", $model->subject_topic_id);
            if ($fileUrl) {
                $model->file = $fileUrl;
                $model->type = self::TYPE_FILE;
                $model->file_extension = $model->file_file->extension;
            } else {
                $errors[] = $model->errors;
            }
        }

        /* Fayl Yuklash*/

        /* Image Yuklash*/
        $model->file_image = UploadedFile::getInstancesByName('file_image');
        if ($model->file_image) {
            $model->file_image = $model->file_image[0];
            $fileUrl = $model->uploadFile("file_image", $model->subject_topic_id);
            if ($fileUrl) {
                $model->type = self::TYPE_IMAGE;
                $model->file = $fileUrl;
                $model->file_extension = $model->file_image->extension;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Image Yuklash*/

        /* Video Yuklash*/
        $model->file_video = UploadedFile::getInstancesByName('file_video');
        if ($model->file_video) {
            $model->file_video = $model->file_video[0];
            $fileUrl = $model->uploadFile("file_video", $model->subject_topic_id);
            if ($fileUrl) {
                $model->file = $fileUrl;
                $model->type = self::TYPE_VIDEO;
                $model->file_extension = $model->file_video->extension;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Video Yuklash*/

        /* Audio Yuklash*/
        $model->file_audio = UploadedFile::getInstancesByName('file_audio');
        if ($model->file_audio) {
            $model->file_audio = $model->file_audio[0];
            $fileUrl = $model->uploadFile("file_audio", $model->subject_topic_id);
            if ($fileUrl) {
                $model->file = $fileUrl;
                $model->type = self::TYPE_AUDIO;
                $model->file_extension = $model->file_audio->extension;
            } else {
                $errors[] = $model->errors;
            }
        }

        /* Audio Yuklash*/
        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;

            $transaction->rollBack();
            return simplify_errors($errors);
        }


        if (isRole('teacher')) {

            $teacherAccess = TeacherAccess::findOne(['subject_id' => $model->subject_id, 'user_id' => current_user_id()]);
            $model->teacher_access_id =  $teacherAccess ? $teacherAccess->id : 0;
            $model->user_id = current_user_id();
        }

        if ($model->save()) {

            if (!isset($post['order'])) {
                $lastOrder = SubjectContent::find()
                    ->where(['subject_topic_id' => $model->subject_topic_id])
                    ->orderBy(['order' => SORT_DESC])
                    ->select('order')
                    ->one();

                if ($lastOrder) {
                    $model->order = $lastOrder->order + 1;
                }
                $model->update();
            }
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

        // $model->type = self::TYPE_TEXT;

        /* Fayl Yuklash*/
        $model->file_file = UploadedFile::getInstancesByName('file_file');
        if ($model->file_file) {
            $model->file_file = $model->file_file[0];
            $fileUrl = $model->uploadFile("file_file", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->file);
                $model->file = $fileUrl;
                $model->type = self::TYPE_FILE;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Fayl Yuklash*/

        /* Image Yuklash*/
        $model->file_image = UploadedFile::getInstancesByName('file_image');
        if ($model->file_image) {
            $model->file_image = $model->file_image[0];
            $fileUrl = $model->uploadFile("file_image", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->file);
                $model->type = self::TYPE_IMAGE;
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Image Yuklash*/

        /* Video Yuklash*/
        $model->file_video = UploadedFile::getInstancesByName('file_video');
        if ($model->file_video) {
            $model->file_video = $model->file_video[0];
            $fileUrl = $model->uploadFile("file_video", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->file);
                $model->file = $fileUrl;
                $model->type = self::TYPE_VIDEO;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Video Yuklash*/

        /* Audio Yuklash*/
        $model->file_audio = UploadedFile::getInstancesByName('file_audio');
        if ($model->file_audio) {
            $model->file_audio = $model->file_audio[0];
            $fileUrl = $model->uploadFile("file_audio", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->file);
                $model->file = $fileUrl;
                $model->type = self::TYPE_AUDIO;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Audio Yuklash*/

        if ($model->save()) {
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

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */

    public function typesArray($key = null)
    {
        $array = [
            [
                'id' => self::TYPE_TEXT,
                'type' => _e('TEXT'),
                'size' => $this->file_textFileMaxSize,
                'extension' => $this->file_textFileExtentions,
            ],
            [
                'id' => self::TYPE_FILE,
                'type' => _e('FILE'),
                'size' => $this->file_fileFileMaxSize,
                'extension' => $this->file_fileFileExtentions,
            ],
            [
                'id' => self::TYPE_IMAGE,
                'type' => _e('IMAGE'),
                'size' => $this->file_imageFileMaxSize,
                'extension' => $this->file_imageFileExtentions,
            ],
            [
                'id' => self::TYPE_VIDEO,
                'type' => _e('VIDEO'),
                'size' => $this->file_videoFileMaxSize,
                'extension' => $this->file_videoFileExtentions,
            ],
            [
                'id' => self::TYPE_AUDIO,
                'type' => _e('AUDIO'),
                'size' => $this->file_audioFileMaxSize,
                'extension' => $this->file_audioFileExtentions,
            ],
        ];

        if (isset($array[$key-1])) {
            return $array[$key-1];
        }

        return $array;
    }

    public function typesArrayyyyy($key = null)
    {
        $array = [
            self::TYPE_TEXT => [_e('TEXT'), $this->file_textFileMaxSize, $this->file_textFileExtentions],
            self::TYPE_FILE => [_e('FILE'), $this->file_fileFileMaxSize, $this->file_fileFileExtentions],
            self::TYPE_IMAGE => [_e('IMAGE'), $this->file_imageFileMaxSize, $this->file_imageFileExtentions],
            self::TYPE_VIDEO => [_e('VIDEO'), $this->file_videoFileMaxSize, $this->file_videoFileExtentions],
            self::TYPE_AUDIO => [_e('AUDIO'), $this->file_audioFileMaxSize, $this->file_audioFileExtentions],
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }

    public function uploadFile($type, $subject_topic_id)
    {
        $subject = SubjectTopic::findOne([
            'id' => $subject_topic_id,
            'is_deleted' => 0,
        ]);
        if ($subject) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            $folder = self::UPLOADS_FOLDER . "/subject_" . $subject->subject_id . "/topic_" . $subject_topic_id . "/";
            if ($this->validate()) {
                if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". $folder))) {
                    mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". $folder), 0777, true);
                }

//                $fileName =  \Yii::$app->security->generateRandomString(12) . '.' . $this->file->extension;
//                $miniUrl = $folder . $fileName;
//                $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
//                $url = STORAGE_PATH . $miniUrl;
//                $this->$type->saveAs($url, false);
//                return "storage/" . $miniUrl;

                $fileName = $this->id . \Yii::$app->security->generateRandomString(12) . '.' . $this->$type->extension;
                $miniUrl = $folder . $fileName;
                $url = \Yii::getAlias('@api/web'. $folder_name  ."/". $folder. $fileName);
                $this->$type->saveAs($url, false);
                return "storage/" . $miniUrl;

            }
        }
        return false;
    }

    public static function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

    public static function orderCorrector($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        /** orders */
        if (!isset($post['subject_topic_id'])) {
            $errors[] = "subject_topic_id required";
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['orders'])) {
            if (($post['orders'][0] == "'") && ($post['orders'][strlen($post['orders']) - 1] == "'")) {
                $post['orders'] =  substr($post['orders'], 1, -1);
            }

            if (!isJsonMK($post['orders'])) {
                $errors['orders'] = [_e('Must be Json')];
            } else {
                foreach ((array)json_decode($post['orders']) as $id => $order) {
                    $content = self::findOne(['id' => $id, 'subject_topic_id' => $post['subject_topic_id']]);
                    if ($content) {
                        $content->order = $order;
                        $content->update(false);
                    }
                }
            }
        } else {
            $errors[] = "orders required";
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $transaction->commit();
            return true;
        }
    }
}
