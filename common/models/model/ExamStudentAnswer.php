<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use app\components\FaceImageMK;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_student_answer".
 *
 * @property int $id
 * @property string|null $file
 * @property int $exam_id
 * @property int $question_id
 * @property int $student_id
 * @property int|null $option_id
 * @property string|null $answer
 * @property int|null $ball
 * @property int|null $teacher_access_id
 * @property int|null $exam_student_id
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int $type 1-savol, 2-test, 3-another
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $parent_id
 * @property Exam $exam
 * @property ExamQuestion $examQuestion
 * @property ExamQuestionOption $option
 * @property Student $student
 * @property TeacherAccess $teacherAccess
 */
class ExamStudentAnswer extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_NEW = 2;
    const STATUS_COMPLETE = 1;
    const STATUS_IN_CHECKING = 3;
    const STATUS_NON_COMPLATED_CHECKING = 4;
    const STATUS_CHECKED = 5;

    const UPLOADS_FOLDER = 'uploads/answer_files/';
    public $answer_file;
    public $answerFileMaxSize = 1024 * 1024 * 15; // 8 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        // Check if user wants archived data (e.g., via a parameter or a flag)
        if (Yii::$app->request->get('archived')) {
            return 'exam_student_answer_23_24';
        }
        if (current_user_id() == 7656) {
            return 'exam_student_answer_23_24';
        }
        return 'exam_student_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_id', 'question_id', 'student_id', 'type'], 'required'],
            [['student_updated_at', 'student_created_at'], 'integer'],

            [
                [
                    'archived',
                    'exam_id',
                    'question_id',
                    'parent_id',
                    'student_id',
                    'option_id',
                    'teacher_access_id',
                    'exam_student_id',
                    'attempt',
                    'type',
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
            [['answer'], 'string'],
            [['teacher_conclusion', 'appeal_teacher_conclusion'], 'string'],
            [['max_ball', 'ball'], 'double'],
            [['file'], 'string', 'max' => 255],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionOption::className(), 'targetAttribute' => ['option_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['answer_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => $this->answerFileMaxSize],
            // [['answer_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->answerFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'File',
            'parent_id' => 'parent_id',
            'exam_id' => 'Exam ID',
            'question_id' => ' Question ID',
            'student_id' => 'Student ID',
            'option_id' => 'Option ID',
            'teacher_conclusion' => 'Еeacher Сonclusion',
            'appeal_teacher_conclusion' => 'appeal_teacher_conclusion',
            'answer' => 'Answer',
            'ball' => 'Ball',
            'max_ball' => 'Max Ball',
            'teacher_access_id' => 'Teacher Access ID',
            'exam_student_id' => 'Teacher Access ID',
            'attempt' => 'Attempt',
            'type' => 'Type',
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
            'parent_id',
            'file',
            'exam_id',
            // 'question' => function ($model) {
            //     return $model->questionForExamStudentAnswer ?? [];
            // },
            'question_type' => function ($model) {
                return $model->questionType->name ?? '';
            },

            'question_id',
            'exam_student_id',
            'teacher_conclusion',
            'appeal_teacher_conclusion',
            'student_id',
            'option_id',
            'answer',
            'ball',
            'archived',
            'max_ball',
            'teacher_access_id',
            'attempt',
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
        $extraFields = [

            'exam',
            'examStudent',
            'question',
            'questionOnly',
            'option',
            'teacherAccess',
            'questionType',

            //'subQuestionAnswers',
            'examStudentAnswerSubQuestion',
            'examStudentAnswerSubQuestionNoChecked',

            'subQuestions',
            'statusName',
            'student',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    // ExamStudentAnswerSubQuestion
    //    public function getSubQuestionAnswers()
    //    {
    //        return $this->hasMany(ExamStudentAnswerSubQuestion::className(), ['exam_student_answer_id' => 'id']);
    //    }

    // ExamStudentAnswerSubQuestion
    public function getExamStudentAnswerSubQuestion()
    {
        return $this->hasMany(ExamStudentAnswerSubQuestion::className(), ['exam_student_answer_id' => 'id']);
    }

    public function getExamStudentAnswerSubQuestionNoChecked()
    {
        return $this->hasMany(ExamStudentAnswerSubQuestion::className(), ['exam_student_answer_id' => 'id'])->onCondition(['ball' => null, 'teacher_conclusion' => null]);
    }

    //    public function getExamStudentAnswerSubQuestionCount()
    //    {
    //        return ExamStudentAnswerSubQuestion::find()->where(['exam_student_answer_id' => $this->id])->count();
    //    }

    public function getSubQuestions()
    {
        return $this->hasMany(SubQuestion::className(), ['question_id' => 'question_id']);
    }

    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    public function getQuestionOnly()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id'])->select(['question', 'file']);
    }

    public function getQuestionForExamStudentAnswer()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id'])->with(['subQuestions']);
    }

    public function getOption()
    {
        return $this->hasOne(ExamQuestionOption::className(), ['id' => 'option_id']);
    }

    public function getQuestionType()
    {
        return $this->hasOne(QuestionType::className(), ['id' => 'type']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    public function getStatusName()
    {
        return $this->statusList()[$this->status];
    }

    public static function randomQuestions($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $data = [];
        $exam_id = $post["exam_id"] ?? null;
        if (isset($exam_id)) {

            // if (!in_array($exam_id, [
            //     1203, 1202, 1199, 1191, 1185, 1176, 1169, 1602, 1616, 1610, 1594, 1624, 1632
            // ])) {
            //     $errors[] = _e("Sizda bu imtihon uchun ruxsat yo'q");
            //     $transaction->rollBack();
            //     return simplify_errors($errors);
            // }

            $exam = Exam::findOne($exam_id);
            if ($exam) {

                if ($exam->max_ball != 100) {
                    /** Oraliqda borligini tekshirish (bahosi)*/
                    if (!ExamControlStudent::find()
                        ->where(['edu_semestr_subject_id' => $exam->edu_semestr_subject_id])
                        ->exists()) {
                        $errors[] = _e("Oraliq javob topilmadi");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    /** Oraliqda borligini tekshirish (bahosi)*/
                }

                if ($exam->is_protected == Exam::PROTECTED_TURE && !checkAllowedIP()) {
                    $errors[] = _e("Not allowed to this computers");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }

                $student = Student::findOne(['user_id' => current_user_id()]);

                if (!$student) {
                    $errors[] = _e("Student not found");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }

                $student_id = $student->id;
                // $student_id = 15;
                $exam_times = [];

                if ($exam->status == 1) {

                    $ExamStudentHas = ExamStudent::find()->where([
                        'exam_id' => $exam_id,
                        'student_id' => $student_id,
                    ])
                        ->orderBy('id desc')
                        ->one();

                    /*
                    $studentAttendCountAll = StudentAttend::find()
                        ->where([
                            'subject_id' => $exam->eduSemestrSubject->subject_id,
                            'student_id' => $student_id,
                            "edu_semestr_id" => $exam->eduSemestrSubject->edu_semestr_id,
                            "edu_year_id" => $exam->eduSemestrSubject->eduSemestr->edu_year_id,
                            // "reason" => 0
                        ])
                        ->count();
                    $studentAttendCount = StudentAttend::find()
                        ->where([
                            'subject_id' => $exam->eduSemestrSubject->subject_id,
                            'student_id' => $student_id,
                            "edu_semestr_id" => $exam->eduSemestrSubject->edu_semestr_id,
                            "edu_year_id" => $exam->eduSemestrSubject->eduSemestr->edu_year_id,
                            "reason" => 0
                        ])
                        ->count();

                    // if ($studentAttendCount >= $exam->subject->subjectSillabus->all_ball_yuklama) {

                    if (
                        $studentAttendCount <= ((int)$exam->subject->subjectSillabus->all_ball_yuklama * Yii::$app->params['student_attent_access_percent'] / 100)
                    ) {

                        $errors[] = _e("You miss a lot of classes") + " ($studentAttendCount/$studentAttendCountAll";
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    */
                    // if(StudentSubjectRestrict::find()->where(['']))

                    $studentSubjectRestrict = StudentSubjectRestrict::find()
                        ->where([
                            'edu_semestr_subject_id' => $exam->edu_semestr_subject_id,
                            'student_id' => self::student(),
                            'is_deleted' => 0,
                            'archived' => 0,
                            'status' => 1,
                        ])->exists();

                    if (
                        $studentSubjectRestrict && ($exam->category != 2)
                    ) {
                        // if ($exam->studentSubjectRestrict->exists() ) {
                        $errors[] = _e("You are not allowed to this exam");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }

                    // imtihon parolli bo'lsa parol tergandan keyin savol shaklantiriladi
                    $t = true;
                    if ($exam->is_protected == Exam::PROTECTED_TURE) {
                        // if ($ExamStudentHas) {
                        if (isset($post["password"])) {
                            $password = $post["password"];
                            if ($password == $exam->password) {
                                $t = true;
                            } else {
                                $t = false;
                            }
                        } else {
                            $errors[] = _e("Password required!");
                        }
                        // } else {
                        //     $errors[] = _e("Not Generated!");
                        // }

                        // /** Check Face ID */
                        $imageService = new FaceImageMK();
                        $response = $imageService->checkFaceID($exam_id);
                        // Handle the response as needed
                        // $data['faceID'] = $response;
                        // $errors['faceID'] = $response;
                        // $transaction->rollBack();
                        // return simplify_errors($errors);
                        // if ($response->status) {
                        //     $data['faceID'] = $response;
                        //     $errors['faceID'] = $response;
                        //     // dd($response);
                        //     // [status] => 1
                        //     // [result] => 39.77
                        //     // [mask_detection] => 0
                        //     // [glasses_detection] => 0
                        //     // if ($response->mask_detection) {
                        //     //     $errors[] = _e("Face ID error: Mask detection");
                        //     //     $transaction->rollBack();
                        //     //     return simplify_errors($errors);
                        //     // }

                        //     // if ($response->glasses_detection) {
                        //     //     $errors[] = _e("Face ID error: Glasses detection");
                        //     //     $transaction->rollBack();
                        //     //     return simplify_errors($errors);
                        //     // }

                        //     // if ($response->result <= Yii::$app->params['face_id_checking_percentage']) {
                        //     //     $errors[] = _e("Face ID error: compatibility too low");
                        //     //     $transaction->rollBack();
                        //     //     return simplify_errors($errors);
                        //     // }


                        //     // $errors[] = _e("Face ID error: compatibility too low");
                        //     // $transaction->rollBack();
                        //     // return simplify_errors($errors);
                        // }



                        // yangi ishlagani/

                        if ($response->status) {
                            $data['faceID'] = $response->data;
                            $errors['faceID'] = $response->data;
                            if (!$response->data->match) {
                                // $errors['face'] = $response;

                                $errors[] = _e("No fit on the face");
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }
                            // $errors['sasdasdasd'] = $response->data->match;
                            // $transaction->rollBack();
                            // return simplify_errors($errors);
                        } else {
                            $errors['FaceError'] = $response->message;
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                        // dd([$response, "asdasd"]);
                        // /** Check Face ID */
                    }

                    if ($t) {

                        // dd(['exam_id' => $exam_id, 'student_id' => $student_id]);
                        $hasExamStudentAnswer = ExamStudentAnswer::findOne(['exam_id' => $exam_id, 'student_id' => $student_id]);
                        if ($hasExamStudentAnswer) {
                            // dd($hasExamStudentAnswer);
                            $getQuestionModel = new ExamStudentAnswer();
                            $getQuestion = $getQuestionModel->find()
                                ->with(['question'])
                                ->andWhere([
                                    'exam_id' => $exam_id,
                                    'student_id' => $student_id,
                                    'parent_id' => null
                                ])
                                ->all();

                            $data['questions'] = $getQuestion;

                            $exam_times['start'] = date("Y-m-d H:i:s", $ExamStudentHas->start);
                            $exam_times['duration'] = $exam->duration;
                            if ($ExamStudentHas->finish > 0) {
                                $exam_times['finish'] = date("Y-m-d H:i:s", $ExamStudentHas->finish);
                            } else {
                                $exam_finish = $ExamStudentHas->start + $exam->duration + (int)$ExamStudentHas->duration;
                                if ($exam_finish > strtotime($exam->finish)) {
                                    $exam_times['finish'] = date("Y-m-d H:i:s", strtotime($exam->finish));
                                } else {
                                    $exam_times['finish'] = date("Y-m-d H:i:s", $exam_finish);
                                }
                            }
                            $exam_times['now'] = date("Y-m-d H:i:s");
                            // $exam_times['exam_finish'] = $exam_finish;
                            $data['times'] = $exam_times;
                            $data['new'] = false;
                            $data['status'] = true;
                            return $data;
                        }

                        // $now_date = date('Y-m-d H:i:s');
                        $now_second = time();
                        if (
                            strtotime($exam->start) < $now_second
                            && strtotime($exam->finish) >= $now_second
                        ) {

                            $question_count_by_type_with_ball = json_decode($exam->question_count_by_type_with_ball);
                            if (!(isJsonMK($exam->question_count_by_type_with_ball) && $question_count_by_type_with_ball)) {
                                $errors[] = _e("The question is not specified");
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }
                            $subject_id = $exam->eduSemestrSubject->subject_id;
                            $semestr_id = $exam->eduSemestrSubject->eduSemestr->semestr_id;

                            /* Bu yerga bolani imtixonga a`zo qilamiz*/

                            $student_lang_id = $student->edu_lang_id;
                            if ($ExamStudentHas) {
                                $ExamStudent = $ExamStudentHas;
                            } else {
                                $ExamStudent = new ExamStudent();
                            }

                            $ExamStudent->exam_id = $exam_id;
                            $ExamStudent->edu_year_id = $exam->eduSemestrSubject->eduSemestr->edu_year_id;
                            $ExamStudent->edu_semestr_subject_id = $exam->eduSemestrSubject->id;
                            $ExamStudent->subject_id = $exam->eduSemestrSubject->subject_id;

                            $ExamStudent->student_id = $student_id;
                            $ExamStudent->start = time();
                            $ExamStudent->lang_id = $student_lang_id;
                            // $ExamStudent->attempt = isset($ExamStudentHas) ? $ExamStudentHas->attempt + 1 : 1;
                            $ExamStudent->status = ExamStudent::STATUS_TAKED;
                            $ExamStudent->save();

                            /* *****************************/
                            // isJsonMK($question_count_by_type);
                            // isJsonMK($question_count_by_type_with_ball);
                            foreach ($question_count_by_type_with_ball as $type => $question_count_with_ball) {
                                $questionAll = Question::find()
                                    ->where([
                                        'subject_id' => $subject_id,
                                        // 'semestr_id' => $semestr_id, //  $exam->eduSemestrSubject->eduSemestr->semestr_id
                                        'lang_id' => $student_lang_id,
                                        'question_type_id' => $type,
                                        'status' => 1,
                                        'is_deleted' => 0,
                                        'archived' => 0,
                                    ])
                                    ->orderBy(new Expression('rand()'))
                                    ->limit($question_count_with_ball->count)
                                    ->all();

                                if (count($questionAll) == $question_count_with_ball->count) {
                                    // if (count($questionAll) > 0) {
                                    foreach ($questionAll as $question) {
                                        $ExamStudentAnswer = new ExamStudentAnswer();
                                        $ExamStudentAnswer->exam_student_id = $ExamStudent->id;
                                        $ExamStudentAnswer->exam_id = $exam_id;
                                        $ExamStudentAnswer->question_id = $question->id;
                                        $ExamStudentAnswer->student_id = $student_id;
                                        $ExamStudentAnswer->type = $type;
                                        $ExamStudentAnswer->max_ball = $question_count_with_ball->ball;
                                        $ExamStudentAnswer->attempt = 1;
                                        $ExamStudentAnswer->status = ExamStudentAnswer::STATUS_NEW;
                                        $ExamStudentAnswer->save();
                                    }
                                } else {


                                    // ExamStudentAnswer::deleteAll(['exam_id' => $exam_id, 'student_id' => $student_id]);

                                    $errors[] = _e("Questions are not found for this exam");

                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }
                            }

                            $getQuestionModel = new ExamStudentAnswer();
                            $getQuestion = $getQuestionModel->find()
                                ->with(['question'])
                                ->andWhere(['exam_id' => $exam_id, 'student_id' => $student_id, 'parent_id' => null])
                                ->all();

                            $data['questions'] = $getQuestion;

                            // $data['questions'] = ExamStudentAnswer::findAll(['exam_id' => $exam_id, 'student_id' => $student_id, 'parent_id' => null]);

                            $exam_times['start'] = date("Y-m-d H:i:s", $ExamStudent->start);
                            $exam_times['duration'] = $exam->duration;

                            /** */
                            if ($ExamStudent->finish > 0) {
                                $exam_times['finish'] = date("Y-m-d H:i:s", $ExamStudent->finish);
                            } else {
                                $exam_finish = $ExamStudent->start + $exam->duration + (int)$ExamStudent->duration;
                                if ($exam_finish > strtotime($exam->finish)) {
                                    $exam_times['finish'] = date("Y-m-d H:i:s", strtotime($exam->finish));
                                } else {
                                    $exam_times['finish'] = date("Y-m-d H:i:s", $exam_finish);
                                }
                            }
                            /** */

                            if ($ExamStudent->finish > 0) {
                                $exam_times['finish'] = date("Y-m-d H:i:s", $ExamStudent->finish);
                            } else {
                                $exam_finish = $ExamStudent->start + $exam->duration + (int)$ExamStudent->duration;
                                if ($exam_finish > strtotime($exam->finish)) {
                                    $exam_times['finish'] = date("Y-m-d H:i:s", strtotime($exam->finish));
                                } else {
                                    $exam_times['finish'] = date("Y-m-d H:i:s", $exam_finish);
                                }
                            }

                            // $exam_times['finish'] = date("Y-m-d H:i:s", $ExamStudent->start + $exam->duration);
                            $exam_times['now'] = date("Y-m-d H:i:s");
                            // $exam_times['exam_finish'] = $exam_finish;
                            $data['times'] = $exam_times;
                            $data['new'] = true;
                            $data['status'] = true;
                            $transaction->commit();
                            return $data;
                        } else {
                            if (strtotime($exam->start) > $now_second) {
                                $errors[] = _e("This exam`s time is not starts");
                                $errors[] = ["status" => false];
                            } elseif (strtotime($exam->finish) < $now_second) {
                                $errors[] = _e("This exam`s time expired");
                                $errors[] = ["status" => false];
                            }
                            // $errors[] = $exam;
                        }
                    } else {
                        $errors[] = _e("Incorrect password");
                    }
                } else {
                    $errors[] = _e("This exam is " . $exam->getStatusName());
                }
            } else {
                $errors[] = _e("This exam not found");
            }
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $errors[] = _e("Exam id required");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function createItem($model, $post)
    {
        return true;

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->start = time();
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItemTeacher($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        /** subQuestionAnswersChecking */
        if (isset($post['subQuestionAnswersChecking'])) {
            if (($post['subQuestionAnswersChecking'][0] == "'") && ($post['subQuestionAnswersChecking'][strlen($post['subQuestionAnswersChecking']) - 1] == "'")) {
                $post['subQuestionAnswersChecking'] =  substr($post['subQuestionAnswersChecking'], 1, -1);
            }

            if (!isJsonMK($post['subQuestionAnswersChecking'])) {
                $errors['subQuestionAnswersChecking'] = [_e('Must be Json')];
                return simplify_errors($errors);
            }

            $mainBallForOneQuestion = 0;
            $subQuestionOneAnswerCount = 0;
            foreach (((array)json_decode($post['subQuestionAnswersChecking'])) as $subQuestionOneAnswerChecking) {
                //                dd($subQuestionOneAnswerChecking);

                if (isset($subQuestionOneAnswerChecking->exam_student_answer_sub_question_id)) {
                    $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::findOne($subQuestionOneAnswerChecking->exam_student_answer_sub_question_id);

                    if ($examStudentAnswerSubQuestion) {
                        if ($examStudentAnswerSubQuestion->exam_student_answer_id == $model->id) {
                            $examStudentAnswerSubQuestion->teacher_conclusion = $subQuestionOneAnswerChecking->teacher_conclusion;
                            $examStudentAnswerSubQuestion->ball = $subQuestionOneAnswerChecking->ball;
                            if (isset($subQuestionOneAnswerChecking->teacher_conclusion) && isset($subQuestionOneAnswerChecking->ball)) {
                                $examStudentAnswerSubQuestion->is_cheked = 1;
                            }
                            if ($examStudentAnswerSubQuestion->save()) {
                                $mainBallForOneQuestion += $subQuestionOneAnswerChecking->ball;
                                $subQuestionOneAnswerCount++;
                            }
                        } else {
                            $errors[] = [$examStudentAnswerSubQuestion->id => _e("This subQuestion Answer is not for this question's answer")];
                        }
                    } else {
                        $errors[] = _e("This subQuestion Answer is not found");
                    }
                }
            }
            if (ExamStudentAnswerSubQuestion::find()->where(['exam_student_answer_id' => $model->id])->count() == $subQuestionOneAnswerCount) {
                $model->status = self::STATUS_CHECKED;
            } else {
                $model->status = self::STATUS_NON_COMPLATED_CHECKING;
            }
            $model->ball = $mainBallForOneQuestion;
        }
        /** subQuestionAnswersChecking */

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        /** Tekshirayotganda examStudent status aniqlash */
        $examStudent = ExamStudent::findOne($model->exam_student_id);
        // $examStudent->

        if (!($examStudent->type > 0)) {

            $examStudent->ball = $examStudent->allBall;

            $examStudent->is_checked = $examStudent->isChecked;
            $examStudent->is_checked_full = $examStudent->isCheckedFull;
            $examStudent->has_answer = $examStudent->hasAnswer;
            $examStudent->update(false);
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
            // $errors[] = count($errors);
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        //
    }

    public static function appealUpdateItemTeacher($model, $post, $examAppeal)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        /** subQuestionAnswersAppealChecking */
        if (isset($post['subQuestionAnswersAppealChecking'])) {
            if (($post['subQuestionAnswersAppealChecking'][0] == "'") && ($post['subQuestionAnswersAppealChecking'][strlen($post['subQuestionAnswersAppealChecking']) - 1] == "'")) {
                $post['subQuestionAnswersAppealChecking'] =  substr($post['subQuestionAnswersAppealChecking'], 1, -1);
            }

            if (!isJsonMK($post['subQuestionAnswersAppealChecking'])) {
                $errors['subQuestionAnswersAppealChecking'] = [_e('Must be Json')];
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $mainBallForOneQuestion = 0;
            $subQuestionOneAnswerCount = 0;
            foreach (((array)json_decode($post['subQuestionAnswersAppealChecking'])) as $subQuestionOneAnswerAppealChecking) {
                //                dd($subQuestionOneAnswerAppealChecking);

                if (isset($subQuestionOneAnswerAppealChecking->exam_student_answer_sub_question_id)) {
                    $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::findOne($subQuestionOneAnswerAppealChecking->exam_student_answer_sub_question_id);

                    if ($examStudentAnswerSubQuestion) {
                        if ($examStudentAnswerSubQuestion->exam_student_answer_id == $model->id) {
                            $examStudentAnswerSubQuestion->appeal_teacher_conclusion = $subQuestionOneAnswerAppealChecking->appeal_teacher_conclusion;
                            // if ($examStudentAnswerSubQuestion->old_ball == null &&  $examStudentAnswerSubQuestion->ball != $subQuestionOneAnswerAppealChecking->ball) {
                            if ($examStudentAnswerSubQuestion->old_ball == null) {
                                $examStudentAnswerSubQuestion->old_ball = $examStudentAnswerSubQuestion->ball;
                                $examAppeal->is_changed = ExamAppeal::IS_CHANGED_TRUE;
                                $examAppeal->ball = $examAppeal->examStudent->getAllBall();
                                $examAppeal->old_ball = $examAppeal->examStudent->getOldAllBall();

                                $examAppeal->update(false);
                            } else {
                                $errors[] = [$examStudentAnswerSubQuestion->ball => _e("Already checked")];
                            }

                            $examStudentAnswerSubQuestion->ball = $subQuestionOneAnswerAppealChecking->ball;

                            if ($examStudentAnswerSubQuestion->save()) {
                                $mainBallForOneQuestion += $subQuestionOneAnswerAppealChecking->ball;
                                $subQuestionOneAnswerCount++;
                            }
                        } else {
                            $errors[] = [$examStudentAnswerSubQuestion->id => _e("This subQuestion Answer is not for this question's answer")];
                        }
                    } else {
                        $errors[] = _e("This subQuestion Answer is not found");
                    }
                }
            }

            if (ExamStudentAnswerSubQuestion::find()->where(['exam_student_answer_id' => $model->id])->count() == $subQuestionOneAnswerCount) {
                $model->status = self::STATUS_CHECKED;
            } else {
                $model->status = self::STATUS_NON_COMPLATED_CHECKING;
            }
            $model->ball = $mainBallForOneQuestion;
        }
        /** subQuestionAnswersAppealChecking */

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            if ($model->save()) {
                $model->examStudent->ball = $model->examStudent->allBall;
                $model->examStudent->save();

                $transaction->commit(false);
                // dd($examStudent);
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            // $errors[] = count($errors);
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        //
    }


    public static function updateItem($model, $post)
    {
        // attemp esdan chiqmasin
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $exam_times = [];
        // studentni answer file ni saqlaymiz

        $student = Student::findOne(['user_id' => current_user_id()]);
        if (!$student) {
            $errors[] = _e("Student not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $student_id = $student->id;
        // $student_id = 15;

        $exam_id = $model->exam_id;
        $old_file = $model->file;

        if (isset($exam_id)) {

            // if (!in_array($exam_id, [
            //     1203, 1202, 1199, 1191, 1185, 1176, 1169, 1602, 1616, 1610, 1594, 1624, 1632
            // ])) {
            //     $errors[] = _e("Sizda bu imtihon uchun ruxsat yo'q");
            //     $transaction->rollBack();
            //     return simplify_errors($errors);
            // }

            $exam = Exam::findOne($exam_id);
            if ($exam) {
                $examStudent = ExamStudent::find()->where([
                    'exam_id' => $exam_id,
                    'student_id' => $student_id,
                ])
                    ->orderBy('id desc')
                    ->one();
                $now_second = time();

                /*
                $studentAttendCountAll = StudentAttend::find()
                    ->where([
                        'subject_id' => $exam->eduSemestrSubject->subject_id,
                        'student_id' => $student_id,
                        "edu_semestr_id" => $exam->eduSemestrSubject->edu_semestr_id,
                        "edu_year_id" => $exam->eduSemestrSubject->eduSemestr->edu_year_id,
                        // "reason" => 0
                    ])
                    ->count();

                $studentAttendCount = StudentAttend::find()
                    ->where([
                        'subject_id' => $exam->eduSemestrSubject->subject_id,
                        'student_id' => $student_id,
                        "edu_semestr_id" => $exam->eduSemestrSubject->edu_semestr_id,
                        "edu_year_id" => $exam->eduSemestrSubject->eduSemestr->edu_year_id,
                        "reason" => 0
                    ])
                    ->count();

                // if ($studentAttendCount >= Yii::$app->params['student_attent_max_count_for_exam']) {
                // if ($studentAttendCount >= $exam->subject->subjectSillabus->all_ball_yuklama) {
                // if ($studentAttendCount <= ((int)$->subject->subjectSillabus->all_ball_yuklama / Yii::$app->params[''])) {
                if ($studentAttendCount <= ((int)$exam->subject->subjectSillabus->all_ball_yuklama * Yii::$app->params['student_attent_access_percent'] / 100)) {


                    $errors[] = _e("You miss a lot of classes") + " ($studentAttendCount/$studentAttendCountAll";
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                */

                // $studentSubjectRestrict = StudentSubjectRestrict::find()
                //     ->where([
                //         'edu_semestr_subject_id' => $exam->edu_semestr_subject_id,
                //         'student_id' => self::student(),
                //         'is_deleted' => 0
                //     ])
                //     ->one();

                // if ($studentSubjectRestrict) {
                //     // if ($exam->studentSubjectRestrict->exists() ) {
                //     $errors[] = _e("You are not allowed to this exam");
                //     $transaction->rollBack();
                //     return simplify_errors($errors);
                // }


                if ($examStudent) {

                    $finishExamStudent = $examStudent->start + $exam->duration + $examStudent->duration;

                    /*  if (
                        (strtotime($exam->start) <= $now_second)
                        && ((strtotime($exam->finish) >= $now_second) || ($examStudent->finish >= $now_second))
                        && ($now_second <= $finishExamStudent)
                    ) { */


                    /* dd([
                        'now' => $now_second,
                        'exam->start' => strtotime($exam->start),
                        'exam->finish' => strtotime($exam->finish),
                        'examStudent->finish' => $examStudent->finish,
                        'examStudent->start' => $examStudent->start,
                        'exam->duration + $examStudent->duration' => $exam->duration + $examStudent->duration,
                        'finishExamStudent' => $finishExamStudent,
                    ]); */

                    if (strtotime($exam->start) <= $now_second) {
                        if ((strtotime($exam->finish) >= $now_second) || ($examStudent->finish >= $now_second)) {
                            if (($now_second <= $finishExamStudent)) {

                                // dd('if ichi ');
                                $model->attempt = $examStudent->attempt;
                                $model->answer_file = UploadedFile::getInstancesByName('answer_file');
                                if ($model->answer_file) {
                                    $model->answer_file = $model->answer_file[0];
                                    $answer_fileFileUrl = $model->uploadFile($exam->id, $student_id);
                                    if ($answer_fileFileUrl) {
                                        $model->file = $answer_fileFileUrl;
                                    } else {
                                        $errors[] = $model->errors;
                                    }
                                }

                                /** subQuestionAnswers */
                                if (isset($post['subQuestionAnswers'])) {

                                    if (($post['subQuestionAnswers'][0] == "'") && ($post['subQuestionAnswers'][strlen($post['subQuestionAnswers']) - 1] == "'")) {
                                        $post['subQuestionAnswers'] =  substr($post['subQuestionAnswers'], 1, -1);
                                    }

                                    // if ($post['subQuestionAnswers'][0] == "'" && $post['subQuestionAnswers'][strlen($post['subQuestionAnswers']) - 1] == "'") {
                                    //     dd(substr($post['subQuestionAnswers']));
                                    // }
                                    // dd('aa');
                                    // dd(substr($post['subQuestionAnswers'], 1, -1));
                                    // dd(substr($post['subQuestionAnswers'], 1));

                                    // dd(json_encode($post['subQuestionAnswers']));
                                    // dd('ss');
                                    // $post['subQuestionAnswers'] = str_replace("'", "", $post['subQuestionAnswers']);


                                    if (!isJsonMK($post['subQuestionAnswers'])) {
                                        $errors['subQuestionAnswers'] = [_e('Must be Json')];
                                    } else {
                                        $javobiBor = false;
                                        foreach (((array)json_decode($post['subQuestionAnswers'])) as  $subQuestionOneAnswer) {

                                            $subQuestion = SubQuestion::findOne($subQuestionOneAnswer->sub_question_id);
                                            if ($subQuestion) {
                                                if ($model->question->id == $subQuestion->question_id) {
                                                    $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::findOne(['exam_student_answer_id' => $model->id, 'sub_question_id' => $subQuestionOneAnswer->sub_question_id]);

                                                    if (!$examStudentAnswerSubQuestion) {
                                                        $examStudentAnswerSubQuestion = new ExamStudentAnswerSubQuestion();
                                                        $examStudentAnswerSubQuestion->exam_student_answer_id = $model->id;
                                                        $examStudentAnswerSubQuestion->exam_student_id = $model->exam_student_id;
                                                        $examStudentAnswerSubQuestion->student_id = $model->examStudent->student_id;
                                                        $examStudentAnswerSubQuestion->sub_question_id = $subQuestionOneAnswer->sub_question_id;
                                                    }

                                                    $examStudentAnswerSubQuestion->answer = $subQuestionOneAnswer->answer;
                                                    $examStudentAnswerSubQuestion->max_ball = $subQuestion->ball;

                                                    $examStudentAnswerSubQuestion->save();
                                                    $javobiBor = true;
                                                } else {
                                                    $errors[] = [$subQuestion->id => _e("This subQuestion is not for this question")];
                                                }
                                            } else {
                                                $errors[] = _e("This subQuestion is not found");
                                            }
                                        }

                                        if ($javobiBor) {
                                            $examStudent->has_answer = 1;
                                            $examStudent->update(false);
                                        }
                                    }
                                }
                                /** subQuestionAnswers */

                                if (!($model->validate())) {
                                    $errors[] = $model->errors;
                                }
                            } else {

                                // $exam_times['start'] = date("Y-m-d H:i:s", $examStudent->start);
                                // $exam_times['duration'] = $exam->duration;

                                // /** */
                                // if ($examStudent->finish > 0) {
                                //     $exam_times['finish'] = date("Y-m-d H:i:s", $examStudent->finish);
                                // } else {
                                //     $exam_finish = $examStudent->start + $exam->duration + (int)$examStudent->duration;
                                //     if ($exam_finish > strtotime($exam->finish)) {
                                //         $exam_times['finish'] = date("Y-m-d H:i:s", strtotime($exam->finish));
                                //     } else {
                                //         $exam_times['finish'] = date("Y-m-d H:i:s", $exam_finish);
                                //     }
                                // }
                                // /** */
                                // if ($examStudent->finish > 0) {
                                //     $exam_times['finish'] = date("Y-m-d H:i:s", $examStudent->finish);
                                // } else {
                                //     $exam_finish = $examStudent->start + $exam->duration + (int)$examStudent->duration;
                                //     if ($exam_finish > strtotime($exam->finish)) {
                                //         $exam_times['finish'] = date("Y-m-d H:i:s", strtotime($exam->finish));
                                //     } else {
                                //         $exam_times['finish'] = date("Y-m-d H:i:s", $exam_finish);
                                //     }
                                // }
                                // $errors[] = ["times" => $exam_times];

                                $errors[] = _e("This exam already finished for you!");
                                $errors[] = ["status" => false];
                            }
                        } else {
                            $errors[] = _e("This exam`s time expired");
                            $errors[] = ["status" => false];
                        }
                    } else {
                        $errors[] = _e("This exam`s time is not starts");
                        $errors[] = ["status" => false];
                    }
                } else {
                    $errors[] = _e("This exam is not for you!");
                    $errors[] = ["status" => false];
                }
            } else {
                $errors[] = _e("This exam not found!");
                $errors[] = ["status" => false];
            }
        } else {
            $errors[] = _e("This exam not found!");
            $errors[] = ["status" => false];
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
            // $errors[] = count($errors);
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function uploadFile($exam_id, $student_id)
    {
        $folder = self::UPLOADS_FOLDER . "/exam_" . $exam_id . "/";

        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . $folder)) {
                mkdir(STORAGE_PATH . $folder, 0777, true);
            }

            $fileName = $this->id . "_" . $student_id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->answer_file->extension;

            $miniUrl = $folder . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->answer_file->saveAs($url, false);
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

    public function beforeSaves($insert)
    {
        $user = current_user_id();
        $this->created_by = $insert ? $user : $this->created_by;
        $this->updated_by = !$insert ? $user : $this->updated_by;

        if (isRole('student')) {
            $this->student_created_at = $insert ? time() : $this->student_created_at;
            $this->student_updated_at = !$insert ? time() : $this->student_updated_at;
        }

        return parent::beforeSave($insert);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
            if (isRole('student')) {
                $this->student_created_at = time();
            }
        } else {
            $this->updated_by = current_user_id();
            if (isRole('student')) {
                $this->student_updated_at = time();
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->examStudent->hasAnswerUpdate;
    }


    public static function statusList()
    {
        return [
            self::STATUS_NEW => _e('STATUS_NEW'),
            self::STATUS_COMPLETE => _e('STATUS_COMPLETE'),
            self::STATUS_IN_CHECKING => _e('STATUS_IN_CHECKING'),
            self::STATUS_NON_COMPLATED_CHECKING => _e('STATUS_NON_COMPLATED_CHECKING'),
            self::STATUS_CHECKED => _e('STATUS_CHECKED'),
        ];
    }
}
