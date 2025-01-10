<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;


/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 *
 * @property int $name
 * @property int $hours
 * @property string $subject_id
 * @property string $lang_id
 * @property int $description
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan[] $eduPlans
 */
class SubjectTopic extends \yii\db\ActiveRecord
{

    const UPLOADS_FOLDER = 'uploads/topic-import/';

    public $file;

    public $allFileMaxSize = 1024 * 1024 * 10; // 10 Mb

    const IS_CORRECT_TRUE = 1;
    const IS_CORRECT_FALSE = 2;

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
        return 'subject_topic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'hours',
                    'subject_id',
                    'lang_id',
                    'subject_category_id',
                    // 'teacher_access_id',
                ],
                'required'
            ],
            [
                [
                    'allotted_time',
                    'attempts_count',
                    'duration_reading_time',
                    'test_count',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['min_percentage'], 'safe'
            ],
            [
                [
                    'hours',
                    'subject_id',
                    'lang_id',
                    'subject_category_id',
                    'teacher_access_id',
                ],
                'integer'
            ],
            [
                [
                    'name',
                    'description',
                ],
                'string'
            ],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx , xls', 'maxSize' => $this->allFileMaxSize],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'hours' => 'Hours',
            'subject_id' => 'Subject Id',
            'subject_category_id' => 'Subject Category Id',
            'lang_id' => 'Lang Id',
            'description' => 'Description',
            'teacher_access_id' => 'teacher_access_id',

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
            'name',
            'hours',
            'subject_id',
            'subject_category_id',
            'lang_id',
            'description',
            'teacher_access_id',

            'isPermission',

            'allotted_time',
            'attempts_count',
            'duration_reading_time',
            'test_count',
            'min_percentage',

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
            'content',
            'subjectContentMark',
            'contentCount',
            'hasContent',
            'mark',
            'subject',
            'teacherAccess',
            'subjectCategory',
            'lang',
            'reference',
            'contentTest',
            'teachersContentCount',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getContent()
    {
        if (Yii::$app->request->get('user_id') != null) {
            return $this->hasMany(SubjectContent::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => Yii::$app->request->get('user_id')]);
        }
        return $this->hasMany(SubjectContent::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getIsPermission() {

        if (isRole('student')) {
            $studentTopicPermission = new StudentTopicPermission();
            $subjectTopic = new SubjectTopic();
            $topicAsc = $subjectTopic->find()
                ->where([
                    'subject_id' => $this->subject_id,
                    'subject_category_id' => 1,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->orderBy('order asc')
                ->one();
            if ($this->id == $topicAsc->id) {
                $isset = $studentTopicPermission->findOne([
                    'user_id' => current_user_id(),
                    'topic_id' => $this->id,
                    'is_deleted' => 0
                ]);
                if (!isset($isset)) {
                    $newTopicPermission = new StudentTopicPermission();
                    $newTopicPermission->user_id = current_user_id();
                    $student = Student::findOne([
                        'user_id' => current_user_id()
                    ]);
                    $newTopicPermission->student_id = $student->id;
                    $newTopicPermission->topic_id = $this->id;
                    $newTopicPermission->attempts_count = 0; // $this->attempts_count
                    $newTopicPermission->status = 0;
                    $newTopicPermission->is_deleted = 0;
                    $newTopicPermission->save();
                }
            }
            $query = $studentTopicPermission->findOne([
                'user_id' => current_user_id(),
                'topic_id' => $this->id,
                'is_deleted' => 0
            ]);
            if (isset($query)) {
                return $query;
            }
        }
    }

    public function getSubjectContentMark()
    {
        if (Yii::$app->request->get('user_id') != null) {
            return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => Yii::$app->request->get('user_id')]);
        }
        return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getContentCount()
    {
        return count($this->content);
    }

    public function getTeachersContentCount()
    {
        $data = [];
        $teachers = $this->subject->teacherAccesses;
        if (count($teachers) > 0) {
            foreach ($teachers as $teacher) {
                $contentCount = SubjectContent::find()
                    ->where([
                        'teacher_access_id' => $teacher->user_id,
                        'subject_topic_id' => $this->id,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                    ->count();
                $data[] = [
                    'teacher_access_id' => $teacher->id,
                    'count' => $contentCount
                ];
            }
        }
        return $data;
    }

    public function getHasContent()
    {
        return count($this->content) > 0 ? 1 : 0;
    }

    public function getMark()
    {
        if (Yii::$app->request->get('user_id') != null) {
            return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => Yii::$app->request->get('user_id')]);
        }
        return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id'])->select(['id', 'name']);
    }

    public function getContentTest()
    {
        return $this->hasMany(Test::className(), ['topic_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (isset($post['order'])) {
            if ($post['order'] <= 0) {
                $errors[] = _e('The minimum value of the order should not be less than zero');
            } else {
                $order = $post['order'];
                $data = [
                    'subject_id' => $model->subject_id,
                    'subject_category_id' => $model->subject_category_id,
                    'is_deleted' => 0
                ];
                $orderDescOne = SubjectTopic::find()
                    ->where($data)
                    ->orderBy('order desc')
                    ->one();

                if (isset($orderDescOne)) {
                    if ($orderDescOne->order+1 < $order) {
                        $model->order = $orderDescOne->order+1;
                    } elseif ($orderDescOne->order > $order) {
                        $orderUpdate = SubjectTopic::find()
                            ->where(['between', 'order', $order - 1, $orderDescOne->order + 1])
                            ->andWhere($data)
                            ->all();
                        if (count($orderUpdate) > 0) {
                            foreach ($orderUpdate as $orderItem) {
                                $orderItem->order = $orderItem->order + 1;
                                $orderItem->save(false);
                            }
                        }
                    } elseif ($orderDescOne->order == $order) {
                        $orderDescOne->order = $orderDescOne->order + 1;
                        $orderDescOne->save(false);
                    }
                } else {
                    $model->order = 1;
                }
            }
        } else {
            $orderDescOne = SubjectTopic::find()
                ->where([
                    'subject_id' => $model->subject_id,
                    'subject_category_id' => $model->subject_category_id,
                    'is_deleted' => 0
                ])
                ->orderBy('order desc')
                ->one();
            if (isset($orderDescOne)) {
                $model->order = $orderDescOne->order + 1;
            } else {
                $model->order = 1;
            }
        }


//        if (isRole('teacher') && !isRole('mudir')) {
//            $teacherAccess = TeacherAccess::findOne(['subject_id' => $model->subject_id, 'user_id' => current_user_id()]);
//            $model->teacher_access_id =  $teacherAccess ? $teacherAccess->id : 0;
//            $model->user_id = current_user_id();
//        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function createExport($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = new SubjectTopic();

        if (!isset($post['subject_id'])) {
            $errors[] = ['subject_id' => _e('Subject Id not found')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->file = UploadedFile::getInstancesByName('file');
        if ($model->file) {
            $model->file = $model->file[0];
            $fileUrl = $model->upload();
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
                $order = $row[0];
                $name = $row[1];
                $lang = $row[2];
                $hours = $row[3];
                $category = $row[4];

                if ($order == "") {
                    break;
                }

                $new = new SubjectTopic();
                $new->subject_id = $post['subject_id'];
                $new->name = $name;
                $new->lang_id = $lang;
                $new->subject_category_id = $category;
                $new->hours = $hours;
                $new->order = (int)$order;

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
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function upload()
    {
        $folder_name = substr(STORAGE_PATH, 0, -1);
        if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
            mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
        }

        $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->file->extension;
        $miniUrl = self::UPLOADS_FOLDER . $fileName;
        $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
        $this->file->saveAs($url, false);
        return "storage/" . $miniUrl;
    }

    public static function updateItem($model, $post, $modelOrder)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateOrder($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $topics = SubjectTopic::find()
            ->where([
                'subject_id' => $model->subject_id,
                'subject_category_id' => $model->subject_category_id,
                'is_deleted' => 0
            ])
            ->andWhere(['!=' , 'id' , $model->id])
            ->orderBy('order asc')
            ->all();
        $i = 1;
        foreach ($topics as $topic) {
            if ($i == $post['order']) {
                $i++;
            }
            $topic->order = $i;
            $topic->save(false);
            $i++;
        }
        $model->order = $post['order'];


        if ($model->save(false)) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

    }

    public static function validPostTopicTest($post) {
        $errors = [];
        if (isset($post['topic_id'])) {
            $subjectTopic = SubjectTopic::findOne([
                'id' => $post['topic_id'],
                'status' => 1,
                'is_deleted' => 0,
            ]);
            if (!isset($subjectTopic)) {
                $errors[] = ['subject_topic_id' => _e('Subject Topic not found')];
            }
        } else {
            $errors[] = ['subject_topic_id' => _e('Subject Topic Id requierd')];
        }
        if (count($errors) == 0) {
            return ['is_ok' => true , 'data' => $subjectTopic];
        }
        return ['is_ok' => false, 'errors' => simplify_errors($errors)];
    }

    public static function studentTopicPermision($post) {
        $errors = [];
        $studentTopicPermission = StudentTopicPermission::findOne([
            'topic_id' => $post['topic_id'],
            'user_id' => current_user_id(),
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (!isset($studentTopicPermission)) {
            $errors[] = ['permission' => _e('You are not allowed to enter the test')];
        }
        if (isset($studentTopicPermission)) {
            if ($studentTopicPermission->attempts_count == 0) {
                $errors[] = ['availability' => _e('You have taken advantage of all the possibilities')];
            }
        }
        return $errors;
    }

    public static function studentTopicResult($post) {
        $errors = [];
        $isStudentTopicResult = StudentTopicResult::findOne([
            'subject_topic_id' => $post['topic_id'],
            'user_id' => current_user_id(),
            'is_deleted'=> 0,
            'status' => 1
        ]);
        if (isset($isStudentTopicResult)) {
            $endTime = $isStudentTopicResult->start_time + $isStudentTopicResult->subjectTopic->allotted_time;
            if (time() >= $endTime) {
                $finish = self::finishTest($isStudentTopicResult);

                if (!$finish['is_ok']) {
                    return ['is_ok' => false, 'errors' => $finish['errors']];
                }
            }
        }
        if (count($errors) == 0) {
            return ['is_ok' => true , 'data' => $isStudentTopicResult];
        }
        return ['is_ok' => false, 'errors' => simplify_errors($errors)];
    }

    public static function topicTest($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $subjectTopic = self::validPostTopicTest($post);
        if (!$subjectTopic['is_ok']) {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($subjectTopic["errors"])];
        }
        $topic = $subjectTopic['data'];

        $studentTopicPermission = self::studentTopicPermision($post);
        if (count($studentTopicPermission) != 0) {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($studentTopicPermission)];
        }

        $isStudentTopicResult = self::studentTopicResult($post);
        if (!$isStudentTopicResult['is_ok']) {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($isStudentTopicResult['errors'])];
        }

        if (!isset($isStudentTopicResult['data'])) {
            $student = current_student();
            if (!isset($student)) {
                $errors[] = ['student' => _e('Student not found.')];
                return ['is_ok' => false, 'errors' => simplify_errors($errors)];
            }
            $eduSemestr = Student::eduPlan($student);
            $testQuery = new Test();
            $test = $testQuery->find()
                ->where([
                    'topic_id' => $topic->id,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->orderBy(new Expression('rand()'))
                ->limit($topic->test_count)
                ->all();
            if (count($test) == 0) {
                $errors[] = ['questions' => _e('Questions not found.')];
                $transaction->rollBack();
                return ['is_ok' => false, 'errors' => simplify_errors($errors)];
            }


//            if (count($test) != $subjectTopic->test_count) {
//                $errors[] = ['questions' => _e('Not enough questions')];
//                $transaction->rollBack();
//                return simplify_errors($errors);
//            }

            $time = time();
            $newStudentTopicResult = new StudentTopicResult();
            $newStudentTopicResult->subject_id = $topic->subject_id;
            $newStudentTopicResult->subject_topic_id = $topic->id;
            $newStudentTopicResult->user_id = current_user_id();
            $newStudentTopicResult->student_id = $student->id;
            $newStudentTopicResult->question_count = $topic->attempts_count; // tekshiriladi
            $newStudentTopicResult->start_time = $time;
            $newStudentTopicResult->end_time = $time + $topic->allotted_time;
            $newStudentTopicResult->percent = 0;
            $newStudentTopicResult->ball = 0;

            $newStudentTopicResult->group_id = $student->group_id;
            $newStudentTopicResult->edu_semestr_id = $eduSemestr->id;
            $newStudentTopicResult->edu_year_id = $eduSemestr->edu_year_id;
            $newStudentTopicResult->course_id = $eduSemestr->course_id;
            $newStudentTopicResult->status = 1;
            if ($newStudentTopicResult->save()) {
                foreach ($test as $testValue) {
                    $studentTestAnswer = new StudentTopicTestAnswer();
                    $studentTestAnswer->student_topic_result_id = $newStudentTopicResult->id;
                    $studentTestAnswer->subject_topic_id = $topic->id;
                    $studentTestAnswer->student_id = $student->id;
                    $studentTestAnswer->user_id = current_user_id();
                    $studentTestAnswer->test_id = $testValue->id;
                    $studentTestAnswer->answer_option_id = $testQuery->answerOption($testValue->id);
                    $studentTestAnswer->option_id = 0;
                    $studentTestAnswer->is_correct = 0;
                    $studentTestAnswer->options = $testQuery->optionsArray($testValue->id);
                    $studentTestAnswer->status = 1;
                    if (!$studentTestAnswer->save()) {
                        $errors[] = $studentTestAnswer->errors;
                    }
                }
            } else {
                $errors[] = $newStudentTopicResult->errors;
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return ['is_ok' => true];
        } else {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }

    }

    public static function answer($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $validAnswer = self::isAnswer($post);
        if (!$validAnswer['is_ok']) {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($validAnswer['errors'])];
        }

        $answer = $validAnswer['data'];
        $option = $post['option_id'];

        if ($answer->answer_option_id == $option) {
            $answer->is_correct = self::IS_CORRECT_TRUE;
        } else {
            $answer->is_correct = self::IS_CORRECT_FALSE;
        }
        $answer->option_id = $option;

        if (!$answer->save(false)) {
            $errors[] = ['option_id' => _e('Answer not specified')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return ['is_ok' => true , 'data' => $answer->student_topic_result_id];
        } else {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public static function isAnswer($post) {
        $errors = [];
        if (isset($post['answer_id'])) {
            $answer = StudentTopicTestAnswer::findOne([
                'id' => $post['answer_id'],
                'user_id' => current_user_id(),
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!isset($answer)) {
                $errors[] = ['answer_id' => _e('Answer not found')];
            } else {
                $studentTopicResult = StudentTopicResult::findOne([
                    'id' => $answer->student_topic_result_id,
                    'user_id' => current_user_id(),
                    'status' => 1,
                    'is_deleted' => 0,
                ]);
                if (!isset($studentTopicResult)) {
                    $errors[] = ['result_id' => _e('You cannot run the test')];
                } else {
                    // javob belgilashini test vaqti oralig'iga tekshiradi
                    $endTime = $studentTopicResult->start_time + $studentTopicResult->subjectTopic->allotted_time;
                    if (time() >= $endTime) {
                        $errors[] = ['time' => _e('Test time is over')];
                        $studentTopicResult->status = 0;
                        $studentTopicResult->save(false);
                    }
                    // javob belgilashini test vaqti oralig'iga tekshiradi
                }
            }
        } else {
            $errors[] = ['answer_id' => _e('Answer Id required')];
        }

        if (!isset($post['option_id'])) {
            $errors[] = ['option_id' => _e('Option Id required')];
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $answer];
        }
        return ['is_ok' => false, 'errors' => simplify_errors($errors)];
    }



    public static function finish($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        $isFinish = self::isFinish($post);

        if (!$isFinish['is_ok']) {
            return ['is_ok' => false, 'errors' => $isFinish['errors']];
        }

        $studentTopicResult = $isFinish['data'];

        $finishTest = self::finishTest($studentTopicResult);

        if ($finishTest['is_ok']) {
            $transaction->commit();
            return ['is_ok' => true , 'data' => $finishTest['data']];
        }
        $transaction->rollBack();
        return ['is_ok' => false, 'errors' => simplify_errors($finishTest['errors'])];
    }

    public static function finishTest($studentTopicResult) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $isPermission = StudentTopicPermission::findOne([
            'topic_id' => $studentTopicResult->subject_topic_id,
            'user_id' => current_user_id(),
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!isset($isPermission)) {
            $errors[] = ['permission' => _e('You are not allowed to complete the test')];
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
        $answer = StudentTopicTestAnswer::find()
            ->where([
                'student_topic_result_id' => $studentTopicResult->id,
                'user_id' => current_user_id(),
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();

        $allTestCount = count($answer);
        $ball = 0;
        foreach ($answer as $item) {
            if ($item->is_correct == 1) {
                $ball++;
            }
        }
        $studentTopicResult->ball = $ball;

        $min_percentage = $studentTopicResult->subjectTopic->min_percentage;

        $x = (100 * $ball) / $allTestCount;
        $studentTopicResult->percent = $x;

        if ($x >= $min_percentage) {
            SubjectTopic::subjectTopics($studentTopicResult->subject_id , $studentTopicResult->subject_topic_id);
        }

        $studentTopicResult->status = 0;
        $studentTopicResult->end_time = time();
        $isPermission->attempts_count = $isPermission->attempts_count - 1;
        if ($isPermission->attempts_count == 0) {
            $isPermission->status = 2;
        }
        if (!$isPermission->save()) {
            $errors[] = ['data' => _e('Permission not saved')];
        }
        if (!$studentTopicResult->save()) {
            $errors[] = ['data' => _e('Information not saved')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return ['is_ok' => true , 'data' => $studentTopicResult->id];
        }
        return ['is_ok' => false, 'errors' => $errors];
    }

    public static function isFinish($post) {
        $errors = [];

        if (isset($post['result_id'])) {
            $studentTopicResult = StudentTopicResult::find()
                ->where([
                    'id' => $post['result_id'],
                    'user_id' => current_user_id(),
                    'is_deleted' => 0,
                ])
                ->one();
            if (!isset($studentTopicResult)) {
                $errors[] = ['result_id' => _e('You cannot run the test')];
            }
            if ($studentTopicResult->status == 2) {
                $errors[] = ['result_id' => _e('You have completed the test')];
            }
        } else {
            $errors[] = ['result_id' => _e('Result Id required')];
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $studentTopicResult];
        }
        return ['is_ok' => false, 'errors' => simplify_errors($errors)];

    }


    public static function subjectTopics($subjectId , $topicId) {

        $subjectTopic = subjectTopic::find()
            ->where([
                'subject_id' => $subjectId,
                'subject_category_id' => 1,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->orderBy('order asc')
            ->all();

        $count = count($subjectTopic);

        foreach ($subjectTopic as $key => $item) {
            if ($item->id == $topicId) {
                if ($count != $key) {
                    $isPermission = new StudentTopicPermission();
                    $isPermission->user_id = current_user_id();
                    $student = current_student();
                    $isPermission->student_id = $student->id;
                    $isPermission->topic_id = $subjectTopic[$key+1]->id;
                    $isPermission->attempts_count = $subjectTopic[$key+1]->attempts_count;
                    $isPermission->status = 0;
                    $isPermission->is_deleted = 0;
                    $isPermission->save();
                }
            }
        }


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
}
