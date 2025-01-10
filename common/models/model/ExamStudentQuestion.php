<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Option;
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
 * @property int $user_id
 * @property int $subject_topic_id
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class ExamStudentQuestion extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const UPLOADS_FOLDER = 'uploads/exam/student_answer/';

    public $upload_file;

    const ANSWER_FILE_MAX_SIZE = 1024 * 1024 * 5;
    const ANSWER_FILE_EXTENSION = 'pdf'; // 5 Mb

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'exam_student_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'exam_student_id',
                    'student_id',
                ] , 'required'
            ],
            [
                [
                    'exam_student_id',
                    'exam_id',
                    'group_id',
                    'student_user_id',
                    'student_id',
                    'exam_test_id',
                    'type',
                    'is_correct',
                ],'integer'
            ],

            [['options' ,'file'],'string', 'max'=> 255],

            ['student_ball','safe'],
            [['answer_text', 'description'], 'safe'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],

            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['exam_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['exam_test_id' => 'id']],

            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => self::ANSWER_FILE_EXTENSION, 'maxSize' => self::ANSWER_FILE_MAX_SIZE],
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
        $fields = [
            'id',
            'exam_student_id',
            'student_id',
            'student_ball',
            'type',
            'question' => function($model) {
                return $this->question;
            },
            'description'
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'question',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }

    public function getQuestion() {
        $data = null;
        if ($this->type == Exam::TEST) {
            $data = [
                'text' => $this->test->text,
                'file' => $this->test->file,
                'option' => $this->test->options,
                'options_json' => $this->options,
                'student_option' => $this->student_option,
            ];
        } elseif ($this->type == Exam::WRITE) {
            $data = [
                'text' => $this->test->text,
                'file' => $this->test->file,
                'answer_text' => $this->answer_text,
                'answer_file' => $this->file,
            ];
        }
        return $data;
    }

    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'exam_test_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }


    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        if ($model->examStudent->exam->finish_time < $time) {
            $errors[] = _e("Exam time is over.");
        }
        if ($model->examStudent->exam->start_time > $time) {
            $errors[] = _e("Exam time has not started.");
        }

        if (!($model->examStudent->start_time <= $time && $model->examStudent->finish_time >= $time)) {
            $errors[] = _e("The allotted time has expired.");
        }

        if ($model->examStudent->status > ExamStudent::STUDENT_STARTED) {
            $errors[] = _e("You have completed the exam.");
        }

        $oldFile = $model->file;
        if ($model->type == Exam::TEST) {
            if (isset($post['student_option'])) {
                $model->student_option = $post['student_option'];
                if ($model->answerOption($model->exam_test_id) == $model->student_option) {
                    $model->is_correct = 1;
                } else {
                    $model->is_correct = 0;
                }
            } else {
                $errors[] = _e("Option Id not found.");
            }
        } elseif ($model->type == Exam::WRITE) {
            if (isset($post['answer_text'])) {
                $model->answer_text = $post['answer_text'];
            }
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_FileUrl = $model->upload($model->upload_file);
                if ($upload_FileUrl) {
                    $model->file = $upload_FileUrl;
                } else {
                    $errors[] = $model->errors;
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            if ($model->answer_text == null && $model->file == null) {
                $errors[] = ['answer_text' => _e("Required!")];
                $errors[] = ['upload_file' => _e("Required!")];
            }
        }

        if (count($errors) == 0) {
            $model->save(false);
            if ($oldFile != $model->file) {
                $model->deleteFile($oldFile);
            }
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateBall($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->examStudent->exam_teacher_user_id != current_user_id() && !isRole('admin')) {
            $errors[] = _e("You are not allowed to rate.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->examStudent->status == ExamStudent::STUDENT_FINISHED) {
            if (isset($post['student_ball']) && $post['student_ball'] >= 0) {
                $model->student_ball = $post['student_ball'];
                if (isset($post['description'])) {
                    $model->description = $post['description'];
                }
                $model->save(false);
            } else {
                $errors[] = ['student_ball' => _e("Ball not found.")];
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $allBall = ExamStudentQuestion::find()
                ->where([
                    'exam_student_id' => $model->exam_student_id,
                    'is_deleted' => 0
                ])->sum('student_ball');

            if ($allBall > $model->exam->max_ball) {
                $errors[] = _e("Total points should not exceed the maximum score.");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } elseif ($model->examStudent->status == ExamStudent::STUDENT_EVALUATED) {
            $errors[] = _e("The student is evaluated.");
        } else {
            $errors[] = _e("The student did not complete the exam.");
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
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
        return null;
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

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->upload_file->saveAs($url, false);
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
