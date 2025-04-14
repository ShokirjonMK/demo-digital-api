<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "Question".
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_id
 * @property int $teacher_id
 * @property int $ball
 * @property int $attempt
 * @property int $order
 * @property int $status
 * 
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class Question extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    // use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_TEACHER_EDITED = 2;
    const STATUS_MUDIR_REFUSED = 3;
    const STATUS_MUDIR_ACTIVE = 4;
    const STATUS_DEAN_REFUSED = 5;
    const STATUS_DEAN_ACTIVE = 6;
    const STATUS_EDU_ADMIN_REFUSED = 7;
    // const STATUS_EDU_ADMIN_ACTIVE = 1;


    // // 0- hali tekshirilmagan, 1- tasdiqlangan, 2- bekor qilingan 

    // const EDU_CHECK_NOTSEEN = 0;
    // const EDU_CHECK_ACTIVE = 1;
    // const EDU_CHECK_REFUSED = 2;


    const UPLOADS_FOLDER = 'uploads/question_files/';
    public $question_file;
    public $questionFileMaxSize = 1024 * 1024 * 3; // 3 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [
                [
                    // 'course_id',
                    'semestr_id',
                    'subject_id',
                    // 'ball',
                    'question',
                    'lang_id',
                    'question_type_id'
                ],
                'required'
            ],

            [
                [
                    'course_id',
                    'semestr_id',
                    'subject_id',
                    'lang_id',
                    'level',
                    'question_type_id',

                    'archived',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ],
                'integer'
            ],

            [['file'], 'string', 'max' => 255],
            [['ball'], 'double'],
            [['description'], 'string'],

            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['question_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionType::className(), 'targetAttribute' => ['question_type_id' => 'id']],
            [['question_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->questionFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student Id',
            'exam_id' => 'Exam Id',
            'teacher_id' => 'Teacher Id',
            'ball' => 'Ball',
            'description' => 'Description',

            'attempt' => 'Attempt',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
            'archived' => _e('archived'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'question',
            // 'question' => function ($model) {
            //     return strip_tags($model->question);
            //     return substr($model->question, 0, 111) . '...';
            //     // return $model->question ?? [];
            // },

            'course_id',
            'semestr_id',
            'subject_id',
            'subQuestion' => function ($model) {
                return $model->subQuestions ?? [];
            },
            'question_file' => function ($model) {
                return $model->file ?? '';
            },
            'options' => function ($model) {
                return $model->options ?? [];
            },
            'file',
            // 'ball',

            'lang_id',
            'level',
            'question_type_id',
            'description',
            // 'order',
            'status',
            // 'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'course',
            'semestr',
            'options',
            'subject',
            'lang',
            'questionType',
            'subQuestions',

            'statusName',
            'hasAnswer',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [['Subject ']].
     * Subject
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
        // return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getStatusName()
    {
        // return $this->status;
        return   $this->statusList()[$this->status];
    }


    public function getHasAnswer()
    {
        $teacherAccessIds = TeacherAccess::find()
            ->select('id')
            ->where(['user_id' => current_user_id()])
            ->column();

        $query = ExamStudentAnswer::find()
            ->where(['question_id' => $this->id])
            ->andWhere(['in', 'exam_student_id', ExamStudent::find()
                ->select('id')
                ->where(['teacher_access_id' => $teacherAccessIds])
            ]);

        $examId = Yii::$app->request->get('exam_id');
        if ($examId !== null) {
            $query->andWhere(['exam_id' => $examId]);
        }

        return $query->exists() ? 1 : 0;
    }

    /**
     * Gets query for [['Course ']].
     * Course
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [['Semestr ']].
     * Semestr
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }


    public function getOptions()
    {
        return $this->hasMany(QuestionOption::className(), ['question_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [['QuestionType ']].
     * QuestionType
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionType()
    {
        return $this->hasOne(QuestionType::className(), ['id' => 'question_type_id']);
    }

    /**
     * Gets query for [['Languages']].
     * Languages
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id']);
    }

    /**
     * Gets query for [['SubQuestions']].
     * SubQuestions
     * @return \yii\db\ActiveQuery
     */
    public function getSubQuestions()
    {
        return $this->hasMany(SubQuestion::className(), ['question_id' => 'id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $subQuestionPescent = 0;

        $model->status = 0;
        if ($model->save()) {

            // question file saqlaymiz
            $model->question_file = UploadedFile::getInstancesByName('question_file');
            if ($model->question_file) {
                $model->question_file = $model->question_file[0];
                $questionFileUrl = $model->uploadFile();
                if ($questionFileUrl) {
                    $model->file = $questionFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

            // if (!($model->validate())) {
            //     $errors[] = $model->errors;
            //     $transaction->rollBack();
            //     return simplify_errors($errors);
            // }


            if (isset($post['sub_question'])) {
                if ($post['sub_question'] != null && $post['sub_question'] != "") {
                    $post['sub_question'] = str_replace("'", "", $post['sub_question']);
                    $sub_question = json_decode(str_replace("'", "", $post['sub_question']));

                    foreach ($sub_question as $sub_question_one) {
                        if (isset($sub_question_one->question) && isset($sub_question_one->percent)) {
                            $subQuestionNew = new SubQuestion();
                            $subQuestionNew->question = $sub_question_one->question;
                            $subQuestionNew->percent = $sub_question_one->percent;
                            $subQuestionNew->ball = isset($model->ball) ? ($model->ball * $sub_question_one->percent / 100) : 0;
                            $subQuestionNew->question_id = $model->id;
                            if (!$subQuestionNew->save()) {
                                $errors['subQuestion'][] =  $subQuestionNew->errors;
                            }
                            $subQuestionPescent += $sub_question_one->percent;
                        } else {
                            $errors['subQuestion'] = _e('question and percent are required');
                        }
                    }
                    if ($subQuestionPescent != 100) {
                        $errors[] = _e('Sum of percent(' . $subQuestionPescent . ') of SubQuestion\'s is must be 100%');
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
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

        $oldFile = $model->file;
        // question file saqlaymiz
        $model->question_file = UploadedFile::getInstancesByName('question_file');
        if ($model->question_file) {
            $model->question_file = $model->question_file[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->file = $questionFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        // status  changing
        if ($model->status == 1) {
            if (isRole('mudir')) {
                $model->status = self::STATUS_MUDIR_ACTIVE;
            }
            if (isRole('dean')) {
                $model->status = self::STATUS_DEAN_ACTIVE;
            }
            if (isRole('edu_admin')) {
                $model->status = self::STATUS_ACTIVE;
            }
        }

        if ($model->status == 0) {
            if (isRole('mudir')) {
                $model->status = self::STATUS_MUDIR_REFUSED;
            }
            if (isRole('dean')) {
                $model->status = self::STATUS_DEAN_REFUSED;
            }
            if (isRole('edu_admin')) {
                $model->status = self::STATUS_EDU_ADMIN_REFUSED;
            }
        }



        // status 

        if ($model->save()) {
            $subQuestionPescent = 0;
            if (isset($post['sub_question'])) {
                if ($post['sub_question'] != null && $post['sub_question'] != "") {
                    $post['sub_question'] = str_replace("'", "", $post['sub_question']);
                    $sub_question = json_decode(str_replace("'", "", $post['sub_question']));
                    SubQuestion::deleteAll(['question_id' => $model->id]);

                    foreach ($sub_question as $sub_question_one) {
                        if (isset($sub_question_one->question) && isset($sub_question_one->percent)) {
                            $subQuestionNew = new SubQuestion();
                            $subQuestionNew->question = $sub_question_one->question;
                            $subQuestionNew->percent = $sub_question_one->percent;
                            $subQuestionNew->ball = isset($model->ball) ? ($model->ball * $sub_question_one->percent / 100) : 0;
                            $subQuestionNew->question_id = $model->id;
                            if (!$subQuestionNew->save()) {
                                $errors['subQuestion'][] =  $subQuestionNew->errors;
                            }
                            $subQuestionPescent += $sub_question_one->percent;
                        } else {
                            $errors['subQuestion'] = _e('question and percent are required');
                        }
                    }
                    if ($subQuestionPescent != 100) {
                        $errors[] = _e('Sum of percent(' . $subQuestionPescent . ') of SubQuestion\'s is must be 100%');
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }
            }
            if (count($errors) == 0) {

                // $model->deleteFile($oldFile);
                $transaction->commit();
                return true;
            }
        }

        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            1 => _e('Active'),
            0 => _e('Inactive'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }


    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->question_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->question_file->saveAs($url, false);
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

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_TEACHER_EDITED => _e('STATUS_TEACHER_EDITED'),
            self::STATUS_MUDIR_REFUSED => _e('STATUS_MUDIR_REFUSED'),
            self::STATUS_MUDIR_ACTIVE => _e('STATUS_MUDIR_ACTIVE'),
            self::STATUS_DEAN_REFUSED => _e('STATUS_DEAN_REFUSED'),
            self::STATUS_DEAN_ACTIVE => _e('STATUS_DEAN_ACTIVE'),
            self::STATUS_EDU_ADMIN_REFUSED => _e('STATUS_EDU_ADMIN_REFUSED'),
        ];
    }

    public static function statusListRole()
    {
        if (isRole('teacher')) {
            return [
                self::STATUS_TEACHER_EDITED => _e('STATUS_TEACHER_EDITED'),
                self::STATUS_MUDIR_REFUSED => _e('STATUS_MUDIR_REFUSED'),
                self::STATUS_DEAN_REFUSED => _e('STATUS_DEAN_REFUSED'),
                self::STATUS_EDU_ADMIN_REFUSED => _e('STATUS_EDU_ADMIN_REFUSED'),
                self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
                self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            ];
        }
        if (isRole('mudir')) {
            return [
                self::STATUS_TEACHER_EDITED => _e('STATUS_TEACHER_EDITED'),
                self::STATUS_MUDIR_ACTIVE => _e('STATUS_MUDIR_ACTIVE'),
                self::STATUS_MUDIR_REFUSED => _e('STATUS_MUDIR_REFUSED'),
                self::STATUS_DEAN_REFUSED => _e('STATUS_DEAN_REFUSED'),
                self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            ];
        }
        if (isRole('dean')) {
            return [
                self::STATUS_MUDIR_ACTIVE => _e('STATUS_MUDIR_ACTIVE'),
                self::STATUS_DEAN_ACTIVE => _e('STATUS_DEAN_ACTIVE'),
                self::STATUS_DEAN_REFUSED => _e('STATUS_DEAN_REFUSED'),
            ];
        }

        // if (isRole('edu_admin')) {
        //     return [
        //         self::STATUS_DEAN_ACTIVE => _e('STATUS_DEAN_ACTIVE'),
        //         self::STATUS_EDU_ADMIN_REFUSED => _e('STATUS_EDU_ADMIN_REFUSED'),
        //         self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
        //     ];
        // }

        return [
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_TEACHER_EDITED => _e('STATUS_TEACHER_EDITED'),
            self::STATUS_MUDIR_REFUSED => _e('STATUS_MUDIR_REFUSED'),
            self::STATUS_MUDIR_ACTIVE => _e('STATUS_MUDIR_ACTIVE'),
            self::STATUS_DEAN_REFUSED => _e('STATUS_DEAN_REFUSED'),
            self::STATUS_DEAN_ACTIVE => _e('STATUS_DEAN_ACTIVE'),
            self::STATUS_EDU_ADMIN_REFUSED => _e('STATUS_EDU_ADMIN_REFUSED'),

        ];
    }
    /**
     * Get created by
     *
     * @return void
     */
    public function getCreatedBy()
    {
        if (isRole('student')) return [];
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        if (isRole('student')) return [];
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Get created At
     *
     * @return void
     */
    public function getCreatedAt()
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * Get created At
     *
     * @return void
     */
    public function getUpdatedAt()
    {
        return date('Y-m-d H:i:s', $this->updated_at);
    }
}
