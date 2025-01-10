<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Predis\Configuration\Options;
use common\models\model\Option;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $subject_id
 * @property int $semestr_id
 * @property string $file
 * @property string $text
 * @property int $level
 * @property int $type
 * @property int $is_check
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
class ExamTest extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public $image;
    public $fileMaxSize = 1024 * 1024 * 2; // 2 Mb
    public $allFileMaxSize = 1024 * 1024 * 10; // 2 Mb

    const UPLOADS_FOLDER = 'uploads/exam-question/';
    const UPLOADS_FOLDER_EXCEL = 'uploads/exam-question_excel/';

    public $upload;

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
        return 'exam_test';
    }

    public function rules()
    {
        return [
            [
                ['subject_id'] , 'required'
            ],
            [['text'] , 'safe'],
            [['file'] , 'string' , 'max' => 255],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx , xls', 'maxSize' => $this->allFileMaxSize],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->fileMaxSize],
            [['semestr_id','teacher_access_id','user_id','type','level','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['is_check','semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
        ];
    }

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
        if (isRole('admin')) {
            $fields =  [
                'id',
                'is_check',
                'subject_id',
                'semestr_id',
                'text',
                'file',
                'level',
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
                'text',
                'file',
                'status',
            ];
        }

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'options',
            'subject',
            'semestr',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['status' => 1 , 'is_deleted' => 0]);
    }

    public function getOptions()
    {
        return $this->hasMany(ExamTestOption::className(), ['question_id' => 'id'])->onCondition(['status' => 1 , 'is_deleted' => 0]);
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->semestr_id = $model->subject->semestr_id;

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

        if (!($model->validate())) {
            $errors[] = $model->errors;
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

    public static function createExcelImport($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = new ExamTest();

        if (!isset($post['subject_id'])) {
            $errors[] = ['subject_id' => _e('Subject Id not found')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('upload');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $fileUrl = $model->uploadFile();
        } else {
            $errors[] = ['file' => _e('File not found')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $inputFileName = $fileUrl;
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        if (file_exists($fileUrl)) {
            unlink($fileUrl);
        }


        foreach ($data as $key => $row) {

            if ($key != 0) {
                $question = $row[1];
                $optionTrue = $row[2];
                $option1 = $row[3];
                $option2 = $row[4];
                $option3 = $row[5];

                if ($question == "") {
                    break;
                }
                if ($optionTrue == "") {
                    $optionTrue = ".";
                }
                if ($option1 == "") {
                    $option1 = ".";
                }
                if ($option2 == "") {
                    $option2 = ".";
                }
                if ($option3 == "") {
                    $option3 = ".";
                }

                $option = [
                    0 => $optionTrue,
                    1 => $option1,
                    2 =>$option2,
                    3 =>$option3,
                ];
                $optionData = custom_shuffle($option);
                $new = new ExamTest();
                $new->subject_id = $post['subject_id'];
                $new->semestr_id = $new->subject->semestr_id;
                $new->text = $question;
                if ($new->save(false)) {
                    foreach ($optionData as $key => $item) {
                        $newOption = new ExamTestOption();
                        $newOption->question_id = $new->id;
                        $newOption->text = $item;
                        if ($key == 0) {
                            $newOption->is_correct = 1;
                        }
                        if (!$newOption->save(false)) {
                            $errors[] = _e("Option not saved.");
                        }
                    }

                }

                if (!$new->validate()) {
                    $errors[] = $new->errors;
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if (!$new->save()) {
                    $errors[] = $new->errors;
                    $transaction->rollBack();
                    return simplify_errors($errors);
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

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $model->semestr_id = $model->subject->semestr_id;

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
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            if (file_exists($oldFileUrl)) {
                unlink($oldFileUrl);
            }
        } else {
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

    public static function ischeck($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['is_check'])) {
            $model->is_check = $post['is_check'];
            if (!$model->save(false)) {
                $errors[] = $model->errors;
            }
        } else {
            $errors[] = ['is_check' => _e('Is Check required!')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function uploadFile()
    {
        $folder_name = substr(STORAGE_PATH, 0, -1);
        if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_EXCEL))) {
            mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_EXCEL), 0777, true);
        }

        $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->upload->extension;
        $miniUrl = self::UPLOADS_FOLDER_EXCEL . $fileName;
        $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_EXCEL. $fileName);
        $this->upload->saveAs($url, false);
        return "storage/" . $miniUrl;
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
