<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Option;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Predis\Configuration\Options;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $topic_id
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
class Test extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public $text;

    public $file;

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 5;
    public $excelFileMaxSize = 1024 * 1024 * 10; // 10 Mb

    const UPLOADS_FOLDER = 'uploads/question-images/';

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
        return 'test';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                ['subject_id' , 'type', 'lang_id'] , 'required',
            ],

            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png', 'maxSize' => $this->fileMaxSize],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx , xls', 'maxSize' => $this->excelFileMaxSize],

            [['is_checked' , 'lang_id' , 'type', 'language_id', 'exam_type_id' , 'subject_id', 'topic_id','level','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['topic_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['lang_id' => 'id']],
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
        $fields =  [
            'id',
            'type',
            'subject_id',
            'topic_id',
            'exam_type_id',
            'is_checked',
            'level',
            'lang_id',

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
            'topic',
            'options',
            'subject',
            'language',
            'examType',
            'testBody',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getOptions()
    {
        return $this->hasMany(Option::className(), ['test_id' => 'id'])->where(['status' => 1 , 'is_deleted' => 0]);
    }

    public function getArrayOption()
    {
        $data = [];
        $options = $this->options;
        if (count($options) > 0) {
            foreach ($options as $option) {
                $data[] = [
                    'id' => $option->id,
                    'text' => $option->text,
                    'file' => $option->file,
                ];
            }
        }
        return $data;
    }

    public function getTestBody()
    {
        return $this->hasOne(TestBody::className(), ['test_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public function getTopic()
    {
        return $this->hasOne(SubjectTopic::className(), ['id' => 'topic_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'lang_id']);
    }

    public function getExamType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exam_type_id']);
    }

    public static function answerOption($id) {
        $option = Option::findOne([
            'test_id' => $id,
            'is_correct' => 1,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (isset($option)) {
            return $option->id;
        }
    }

    public static function optionsArray($id) {
        $options = Option::find()
            ->select('id')
            ->where([
                'test_id' => $id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->orderBy(new Expression('rand()'))
            ->asArray()->all();
        return json_encode($options);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $file = null;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->topic_id != null) {
            $model->subject_id = $model->topic->subject_id;
        }

        $model->file = UploadedFile::getInstancesByName('upload');
        if ($model->file) {
            $model->file = $model->file[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->save(false);

        $testBody = new TestBody();
        $testBody->test_id = $model->id;
        if (isset($post['text'])) {
            $testBody->text = $post['text'];
        }
        $testBody->file = $file;
        if (!$testBody->validate()) {
            $errors[] = $testBody->errors;
        } else {
            $testBody->save(false);
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

        if (isset($post['is_checked'])) {
            $model->is_checked = $post['is_checked'];
            if (!$model->save(false)) {
                $errors[] = $model->errors;
            }
        } else {
            $errors[] = ['is_checked' => _e('Is Checked required!')];
        }

        if (count($errors) == 0) {
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
        $file = null;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->topic_id != null) {
            $model->subject_id = $model->topic->subject_id;
        }

        $model->file = UploadedFile::getInstancesByName('upload');
        if ($model->file) {
            $model->file = $model->file[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->save(false);

        $testBody = TestBody::findOne($model->id);

        if (isset($post['text'])) {
            $testBody->text = $post['text'];
        }

        $testBody->file = $file;
        if (!$testBody->validate()) {
            $errors[] = $testBody->errors;
        } else {
            $testBody->save(false);
        }

        if (count($errors) == 0) {
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

        $model = new Test();

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

        $model->deleteFile($fileUrl);

        foreach ($data as $key => $row) {

            if ($key != 0) {
                $type = 2;
                $question = $row[0];
                $optionTrue = $row[1];
                $option1 = $row[2];
                $option2 = $row[3];
                $option3 = $row[4];

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
                $new = new Test();
                $new->load($post , '');
                if ($new->topic_id != null) {
                    $new->subject_id = $new->topic->subject_id;
                }
                $new->is_checked = 1;
                $new->type = $type;
                if (!$new->validate()) {
                    $errors[] = $new->errors;
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($new->save(false)) {

                    $testBody = new TestBody();
                    $testBody->test_id = $new->id;
                    $testBody->text = $question;
                    $testBody->save(false);

                    if ($type == Exam::TEST) {
                        foreach ($optionData as $key => $item) {
                            $newOption = new Option();
                            $newOption->test_id = $new->id;
                            $newOption->text = $item;
                            if ($key == 0) {
                                $newOption->is_correct = 1;
                            }
                            if (!$newOption->save(false)) {
                                $errors[] = _e("Option not saved.");
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }
                        }
                    }
                } else {
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


    public static function allDelete($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        foreach ($model as $item) {
            $item->is_deleted = 1;
            $item->save(false);
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
        if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
            mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
        }
        $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->upload->extension;
        $miniUrl = self::UPLOADS_FOLDER . $fileName;
        $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
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

            $fileName = $this->id . \Yii::$app->security->generateRandomString(12) . '.' . $this->file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->file->saveAs($url, false);
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
