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
 * @property string $file
 * @property int $subject_id
 * @property int $is_confirm
 * @property int $language_id
 * @property int $semestr_id
 * @property int $exam_type_id
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
class ExamQuestions extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const CONFIRM = 1;
    const NOT_CONFIRM = 0;
    public $allFileMaxSize = 1024 * 1024 * 10; // 10 Mb
    public $fileExtension = 'pdf';

    const UPLOADS_FOLDER = 'uploads/exam-write-question/';

    public $upload;

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
        return 'exam_questions';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['subject_id','language_id'], 'required'],
            [['text'], 'safe'],
            [['file'] , 'string' , 'max' => 255],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->allFileMaxSize],
            [['is_confirm','subject_id','language_id','exam_type_id','semestr_id','type', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'subject_id',
            'is_confirm',
            'language_id',
            'text',
            'file',
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
            'subject',
            'language',
            'examsType',
            'semestr',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Building]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    public function getExamsType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exam_type_id']);
    }
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
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
        $model->is_confirm = ExamQuestions::NOT_CONFIRM;
        $model->semestr_id = $model->subject->semestr_id;

        $model->upload = UploadedFile::getInstancesByName('file');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            $model->save();
            $transaction->commit();
            return true;
        }
        $model->deleteFile($model->file);
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
        $model->is_confirm = ExamQuestions::NOT_CONFIRM;
        $model->semestr_id = $model->subject->semestr_id;

        $oldFile = $model->file;
        $model->upload = UploadedFile::getInstancesByName('file');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if (count($errors) == 0) {
            $model->save();
            if ($model->file != $oldFile) {
                $model->deleteFile($oldFile);
            }
            $transaction->commit();
            return true;
        }
        $model->deleteFile($model->file);
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateConfirm($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['is_confirm'])) {
            $model->is_confirm = $post['is_confirm'];
        } else {
            $errors[] = ['is_confirm' => _e("Required!")];
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

            $fileName = $this->id . \Yii::$app->security->generateRandomString(12) . '.' . $this->image->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->image->saveAs($url, false);
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
