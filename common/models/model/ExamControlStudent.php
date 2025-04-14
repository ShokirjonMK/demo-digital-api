<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class ExamControlStudent extends ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const APPEAL_CHECKED = 2;

    const APPEAL_TYPE_ASOSLI = 1;
    const APPEAL_TYPE_ASOSSIZ = 2;
    const APPEAL_TYPE_TEXNIK = 3;
    const APPEAL_TYPE_ASOSLI_TEXNIK = 4;


    const UPLOADS_FOLDER = 'uploads/exam_control/student_answer/';
    const UPLOADS_FOLDER_EDU_ADMIN = 'uploads/exam_control/edu_admin_change_ball/';
    public $upload_plagiat_file;
    public $upload_file;
    public $upload_plagiat2_file;
    public $upload2_file;
    public $edu_admin_change_ball_upload_file;
    public $answerFileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $plagiatFileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $edu_admin_change_ball_fileMaxSize = 1024 * 1024 * 5; // 5 Mb


    public static function tableName()
    {
        // Check if user wants archived data (e.g., via a parameter or a flag)
        if (Yii::$app->request->get('archived')) {
            return 'exam_control_student_23_24';
        }
        return 'exam_control_student';
    }

    public function rules()
    {
        return [
            [
                ['exam_control_id'],
                'required'
            ],
            [
                [
                    'time_option_id',
                    'time_table_id',
                    'student_mark_id',
                    'edu_semestr_subject_id',
                    'appeal',
                    'appeal2',
                    'appeal_status',
                    'appeal2_status',
                    'exam_control_id',
                    'student_id',
                    'course_id',
                    'start',
                    'semester_id',
                    'edu_year_id',
                    'subject_id',
                    'language_id',
                    'edu_plan_id',
                    'teacher_user_id',
                    'edu_semester_id',
                    'subject_category_id',
                    'archived',
                    'old_exam_control_id',
                    'duration',
                    'faculty_id',
                    'direction_id',
                    'type',
                    'category',
                    'is_checked',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [
                [
                    'appeal_text',
                    'appeal2_text',

                    'appeal_conclution',
                    'appeal2_conclution',
                    'answer',
                    'conclution',
                    'answer2',
                    'conclution2'
                ],
                'string'
            ],
            [
                [
                    "old_ball",
                    "old_ball2",
                    'ball',
                    'ball2',
                    'main_ball',
                    'plagiat_percent',
                    'plagiat2_percent'
                ],
                'number'
            ],
            [
                [
                    'time_option_key',
                    'answer_file',
                    'answer2_file',
                    'plagiat_file',
                    'plagiat2_file',
                    'edu_admin_change_ball_file'
                ],
                'string',
                'max' => 255
            ],
            // [['ball', 'ball2', 'main_ball',], 'default', 'value' => 0],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semester_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['exam_control_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamControl::className(), 'targetAttribute' => ['exam_control_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semester_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],

            [['upload_file', 'upload2_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->answerFileMaxSize],
            [['upload_plagiat_file', 'upload_plagiat2_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->plagiatFileMaxSize],
            [['edu_admin_change_ball_upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->edu_admin_change_ball_fileMaxSize],

            [
                ['edu_semestr_subject_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EduSemestrSubject::className(),
                'targetAttribute' => ['edu_semestr_subject_id' => 'id']
            ],
            [
                'exam_control_id',
                'unique',
                'targetAttribute' =>
                ['exam_control_id', 'student_id'],
                'message' => 'This student has alreadey recorded'
            ],
            // ['a1', 'unique', 'targetAttribute' => ['a1', 'a2']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'student_mark_id' => _e('student_mark ID'),
            'exam_control_id' => _e('Exam Control ID'),
            'edu_semestr_subject_id' => _e('edu_semestr_subject ID'),
            'student_id' => _e('Student ID'),
            'answer' => _e('Answer'),
            'answer_file' => _e('Answer File'),
            'conclution' => _e('Conclution'),
            'answer2' => _e('Answer2'),
            'answer2_file' => _e('Answer2 File'),
            'conclution2' => _e('Conclution2'),
            'course_id' => _e('Course ID'),
            'semester_id' => _e('Semester ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'subject_id' => _e('Subject ID'),
            'language_id' => _e('Language ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'teacher_user_id' => _e('Teacher User ID'),
            'edu_semester_id' => _e('Edu Semester ID'),
            'subject_category_id' => _e('Subject Category ID'),
            'archived' => _e('Archived'),
            'old_exam_control_id' => _e('Old Exam Control ID'),
            'ball' => _e('Ball'),
            'ball2' => _e('Ball2'),
            'start' => _e('start'),
            'main_ball' => _e('Main Ball'),
            'plagiat_percent' => _e('Plagiat Percent'),
            'plagiat2_percent' => _e('Plagiat Percent2'),
            'plagiat_file' => _e('Plagiat File'),
            'plagiat2_file' => _e('Plagiat File2'),
            'duration' => _e('Duration'),
            'faculty_id' => _e('Faculty ID'),
            'direction_id' => _e('Direction ID'),
            'type' => _e('Type'),
            'category' => _e('Category'),
            'is_checked' => _e('Is Checked'),
            'status' => _e('Status'),
            'order' => _e('Order'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        return [
            'id',
            'edu_semestr_subject_id',
            'exam_control_id',
            'student_id',
            'answer',
            'answer_file',
            'conclution',
            'answer2',
            'answer2_file',
            'conclution2',
            'appeal_text',
            'appeal2_text',
            'appeal',
            'appeal2',
            'appeal_status',
            'appeal2_status',
            'appeal_conclution',
            'appeal2_conclution',
            'edu_admin_change_ball_file',
            'course_id',
            'semester_id',
            'edu_year_id',
            'subject_id',
            'language_id',
            'edu_plan_id',
            'teacher_user_id',
            'edu_semester_id',
            'subject_category_id',
            'archived',
            'old_exam_control_id',
            'old_ball',
            'ball' => function () {
                if (isRole('student') && $this->examControl->status != 2) {
                    return null;
                }
                return $this->ball;
            },
            'ball2' => function () {
                if (isRole('student') && $this->examControl->status2 != 2) {
                    return null;
                }
                return $this->ball2;
            },
            'old_ball2',
            'main_ball',
            'plagiat_percent',
            'plagiat2_percent',
            'plagiat_file',
            'plagiat2_file',
            'duration',
            'time_table_id',
            'start',
            'faculty_id',
            'direction_id',
            'type',
            'category',
            'is_checked',
            'status',
            'order',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];
    }

    public function extraFields()
    {
        $extraFields =  [

            'course',
            'direction',
            'eduPlan',
            'eduSemester',
            'eduYear',
            'examControl',
            'faculty',
            'language',
            'semester',
            'student',
            'subject',
            'subjectCategory',
            'teacherUser',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery|CourseQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery|DirectionQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[EduSemester]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrQuery
     */
    public function getEduSemester()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semester_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[ExamControl]].
     *
     * @return \yii\db\ActiveQuery|ExamControlQuery
     */
    public function getExamControl()
    {
        return $this->hasOne(ExamControl::className(), ['id' => 'exam_control_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LanguageQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * Gets query for [[Semester]].
     *
     * @return \yii\db\ActiveQuery|SemestrQuery
     */
    public function getSemester()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semester_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery|SubjectCategoryQuery
     */
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    /**
     * Gets query for [[TeacherUser]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTeacherUser()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'teacher_user_id']);
    }

    public static function createItem($model, $post, $changebal = 0)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        // dd(self::student(2));
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isRole('student')) {
            $model->student_id = self::student();

            if (
                !StudentTimeTable::find()
                    ->where(['time_table_id' => $model->examControl->time_table_id, 'student_id' => $model->student_id])
                    ->count() > 0
            ) {
                $errors[] = _e("You are not in this TimeTable");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $now = time();
        if (isRole('student')) {
            if (isset($post['answer2']) || isset($post['upload2_file'])) {
                if ($model->examControl->start2 > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start2) . " (2)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish2 < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish2) . " (2)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            if (isset($post['answer']) || isset($post['upload_file'])) {
                if ($model->examControl->start > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start) . " (1)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish) . " (1)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            $model->start = $now;
        }

        if (isset($post['ball'])) {
            if ($model->ball > $model->examControl->max_ball) {
                $errors[] = _e('incorrect ball');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        if (isset($post['ball2'])) {
            if ($model->ball2 > $model->examControl->max_ball2) {
                $errors[] = _e('incorrect ball2');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        $model->edu_semestr_subject_id = $model->examControl->edu_semestr_subject_id;
        $model->course_id = $model->examControl->course_id;
        $model->semester_id = $model->examControl->semester_id;
        $model->edu_year_id = $model->examControl->edu_year_id;
        $model->subject_id = $model->examControl->subject_id;
        $model->language_id = $model->examControl->language_id;
        $model->edu_plan_id = $model->examControl->edu_plan_id;
        $model->time_table_id = $model->examControl->time_table_id;
        $model->teacher_user_id = $model->examControl->teacher_user_id;
        $model->edu_semester_id = $model->examControl->edu_semester_id;
        $model->subject_category_id = $model->examControl->subject_category_id;
        $model->old_exam_control_id = $model->examControl->old_exam_control_id;
        $model->faculty_id = $model->examControl->faculty_id;
        $model->direction_id = $model->examControl->direction_id;
        $model->type = $model->examControl->type;
        $model->category = $model->examControl->category;
        $model->main_ball = ($model->ball ?? 0) + ($model->ball2 ?? 0);

        $model->time_table_id = $model->examControl->time_table_id;

        $model->time_option_id = $model->examControl->timeTable->time_option_id;
        $model->time_option_key = $model->examControl->timeTable->timeOption->key;

        if ($model->time_option_key == 'I') {
            $model->category = 2;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        // $intensiv =  IntensiveApp::find()->where([
        //     'student_id' => $model->student->id,
        //     'payment_status' => 1,
        //     'subject_id' => $model->subject_id,
        // ])->exists();

        // createItem  // ruxsatni chegaralash
        // if (!$changebal) {
        //     if (
        //         !in_array($model->time_table_id, [])
        //         && !in_array(current_user_id(), [])
        //         && !in_array($model->edu_plan_id, [])
        //         && !in_array($model->student->edu_form_id, [])
        //         && !in_array(
        //             $model->student->profile->passport_pin,
        //             []
        //         )
        //         && !in_array(
        //             $model->student->id,
        //             []
        //         )
        //     ) {
        //         // dd([$intensiv, IntensiveApp::findOne()->where([
        //         //     'student_id' => $model->student->id,
        //         //     'payment_status' => 1,
        //         //     // 'subject_id' => $model->subject_id,
        //         // ])]);
        //         $errors[] = ["Ruxsat berilmagan"];
        //         $transaction->rollBack();
        //         return simplify_errors($errors);
        //     }
        // }


        if ($model->save()) {
            if (isRole('edu_admin') || isRole('admin')) {
                // edu_admin_change_ball_file
                $model->edu_admin_change_ball_upload_file = UploadedFile::getInstancesByName('edu_admin_change_ball_upload_file');
                if ($model->edu_admin_change_ball_upload_file) {
                    $model->edu_admin_change_ball_upload_file = $model->edu_admin_change_ball_upload_file[0];
                    $edu_admin_change_ball_upload_fileUrl = $model->eduAdminUploadFile($model->edu_admin_change_ball_upload_file);
                    if ($edu_admin_change_ball_upload_fileUrl) {
                        $model->edu_admin_change_ball_file = $edu_admin_change_ball_upload_fileUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
            }

            // answer file saqlaymiz
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_FileUrl = $model->uploadFile($model->upload_file);
                if ($upload_FileUrl) {
                    $model->answer_file = $upload_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }


            // answer file saqlaymiz
            $model->upload2_file = UploadedFile::getInstancesByName('upload2_file');
            if ($model->upload2_file) {
                $model->upload2_file = $model->upload2_file[0];
                $upload2_FileUrl = $model->uploadFile($model->upload2_file);
                if ($upload2_FileUrl) {
                    $model->answer2_file = $upload2_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // aplagiat file saqlaymiz
            $model->upload_plagiat_file = UploadedFile::getInstancesByName('upload_plagiat_file');
            if ($model->upload_plagiat_file) {
                $model->upload_plagiat_file = $model->upload_plagiat_file[0];
                $upload_plagiat_fileUrl = $model->uploadFile($model->upload_plagiat_file);
                if ($upload_plagiat_fileUrl) {
                    $model->plagiat_file = $upload_plagiat_fileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // aplagiat file saqlaymiz
            $model->upload_plagiat2_file = UploadedFile::getInstancesByName('upload_plagiat2_file');
            if ($model->upload_plagiat2_file) {
                $model->upload_plagiat2_file = $model->upload_plagiat2_file[0];
                $upload_plagiat2_fileUrl = $model->uploadFile($model->upload_plagiat2_file);
                if ($upload_plagiat2_fileUrl) {
                    $model->plagiat2_file = $upload_plagiat2_fileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            if ($model->save()) {
                $transaction->commit();
                return true;
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $now = time();
        if (isRole('student')) {

            if (isset($post['answer2']) || isset($post['upload2_file'])) {
                if ($model->examControl->start2 > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start2) . " (2)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish2 < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish2) . " (2)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            if (isset($post['answer']) || isset($post['upload_file'])) {
                if ($model->examControl->start > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start) . " (1)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish) . " (1)");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            $model->start = $now;
        }

        if (isset($post['ball'])) {
            if ($model->ball > $model->examControl->max_ball) {
                $errors[] = _e('incorrect ball');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        if (isset($post['ball2'])) {
            if ($model->ball2 > $model->examControl->max_ball2) {
                $errors[] = _e('incorrect ball2');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // $model->main_ball = $model->ball ?? 0 + $model->ball2 ?? 0;
        $model->main_ball = ($model->ball ?? 0) + ($model->ball2 ?? 0);

        // faqat 4- kurslar uchun
        // if (
        //     !in_array(current_user_id(), []) &&
        //     !in_array($model->edu_plan_id, [])
        //     && !in_array(
        //         $model->student->profile->passport_pin,
        //         []
        //     )
        // ) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }


        // $intensiv =  IntensiveApp::find()->where([
        //     'student_id' => $model->student->id,
        //     'payment_status' => 1,
        //     'subject_id' => $model->subject_id,
        // ])->exists();


        // ruxsatni chegaralash
        // if (
        //     !in_array($model->time_table_id, [])
        //     && !in_array(current_user_id(), [])
        //     && !in_array($model->edu_plan_id, [])
        //     && !in_array($model->student->edu_form_id, [])
        //     && !in_array(
        //         $model->student->profile->passport_pin,
        //         []
        //     )
        //     && !in_array(
        //         $model->student->id,
        //         [

        //         ]
        //     )
        // ) {
        //     // dd([$intensiv, IntensiveApp::findOne()->where([
        //     //     'student_id' => $model->student->id,
        //     //     'payment_status' => 1,
        //     //     // 'subject_id' => $model->subject_id,
        //     // ])]);
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }


        // edu_admin_change_ball_file
        // $model->upload_file = UploadedFile::getInstancesByName('edu_admin_change_ball_upload_file');
        // if ($model->edu_admin_change_ball_upload_file && !isRole('edu_admin')) {
        //     $model->edu_admin_change_ball_upload_file = $model->edu_admin_change_ball_upload_file[0];
        //     $edu_admin_change_ball_upload_fileUrl = $model->eduAdminUploadFile($model->edu_admin_change_ball_upload_file);
        //     if ($edu_admin_change_ball_upload_fileUrl) {
        //         $model->edu_admin_change_ball_file = $edu_admin_change_ball_upload_fileUrl;
        //     } else {
        //         $errors[] = $model->errors;
        //     }
        // } else {
        //     $errors[] = [_e('File required')];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }


        // answer file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $upload_FileUrl = $model->uploadFile($model->upload_file);
            if ($upload_FileUrl) {
                $model->answer_file = $upload_FileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // answer file saqlaymiz
        $model->upload2_file = UploadedFile::getInstancesByName('upload2_file');
        if ($model->upload2_file) {
            $model->upload2_file = $model->upload2_file[0];
            $upload2_FileUrl = $model->uploadFile($model->upload2_file);
            if ($upload2_FileUrl) {
                $model->answer2_file = $upload2_FileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // aplagiat file saqlaymiz
        $model->upload_plagiat_file = UploadedFile::getInstancesByName('upload_plagiat_file');
        if ($model->upload_plagiat_file) {
            $model->upload_plagiat_file = $model->upload_plagiat_file[0];
            $upload_plagiat_fileUrl = $model->uploadFile($model->upload_plagiat_file);
            if ($upload_plagiat_fileUrl) {
                $model->plagiat_file = $upload_plagiat_fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // aplagiat file saqlaymiz
        $model->upload_plagiat2_file = UploadedFile::getInstancesByName('upload_plagiat2_file');
        if ($model->upload_plagiat2_file) {
            $model->upload_plagiat2_file = $model->upload_plagiat2_file[0];
            $upload_plagiat2_fileUrl = $model->uploadFile($model->upload_plagiat2_file);
            if ($upload_plagiat2_fileUrl) {
                $model->plagiat2_file = $upload_plagiat2_fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        $model->time_table_id = $model->examControl->time_table_id;

        if ($model->save()) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function changeBallWithFile($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['ball'])) {
            if ($model->ball > $model->examControl->max_ball) {
                $errors[] = _e('incorrect ball');
                $transaction->rollBack();
                return simplify_errors($errors);
            } else {
                $model->ball = $post['ball'];
            }
        }

        if (isset($post['ball2'])) {
            if ($model->ball2 > $model->examControl->max_ball2) {
                $errors[] = _e('incorrect ball2');
                $transaction->rollBack();
                return simplify_errors($errors);
            } else {
                $model->ball2 = $post['ball2'];
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // $model->main_ball = $model->ball ?? 0 + $model->ball2 ?? 0;
        $model->main_ball = (int)$model->ball + (int)$model->ball2;

        // edu_admin_change_ball_file
        $model->edu_admin_change_ball_upload_file = UploadedFile::getInstancesByName('edu_admin_change_ball_upload_file');
        if ($model->edu_admin_change_ball_upload_file) {
            $model->edu_admin_change_ball_upload_file = $model->edu_admin_change_ball_upload_file[0];
            $edu_admin_change_ball_upload_fileUrl = $model->eduAdminUploadFile($model->edu_admin_change_ball_upload_file);
            if ($edu_admin_change_ball_upload_fileUrl) {
                $model->edu_admin_change_ball_file = $edu_admin_change_ball_upload_fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        } else {
            $errors[] = [_e('File required')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }



        if ($model->save()) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function appealCheck($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $now = time();

        if (isset($post['appeal_conclution'])) {

            $model->appeal_conclution = $post['appeal_conclution'];
            if (isset($post['ball']))

                if ($model->ball < $post['ball']) {
                    $model->appeal_status = self::APPEAL_TYPE_ASOSLI;
                    $model->old_ball = $model->ball;
                    $model->ball = $post['ball'];
                } else {
                    $model->appeal_status = $post['appeal_status'] ?? self::APPEAL_TYPE_ASOSSIZ;
                }
            if (!$model->old_ball > 0) {
                $model->old_ball = $model->ball;
            }

            $model->appeal = self::APPEAL_CHECKED;
        }

        if (isset($post['appeal2_conclution'])) {

            $model->appeal2_conclution = $post['appeal2_conclution'];
            if (isset($post['ball2']))
                if ($model->ball2 < $post['ball2']) {
                    $model->appeal2_status = self::APPEAL_TYPE_ASOSLI;
                } else {
                    $model->appeal2_status = $post['appeal2_status'] ?? self::APPEAL_TYPE_ASOSSIZ;
                }
            if (!$model->old_ball2 > 0)
                $model->old_ball2 = $model->ball2;

            $model->ball2 = $post['ball2'] ?? $model->ball2;
            $model->appeal2 = self::APPEAL_CHECKED;
        }

        $model->main_ball = $model->ball ?? 0 + $model->ball2 ?? 0;
        if (isset($post['appeal_status'])) $model->appeal_status = $post['appeal_status'];
        if (isset($post['appeal2_status'])) $model->appeal2_status = $post['appeal2_status'];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0)
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function appealNew($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $now = time();

        // dd($post['appeal_text']);

        // if (isset($post['appeal_text'])) {
        //     if ($model->examControl->status == ExamControl::STATUS_ANNOUNCED) {
        //         // dd('sdfsdsdfesdfwefwervwerv werv sdfdf');

        //         if ($model->examControl->appeal_at >= $now) {

        //             $model->appeal_text = $post['appeal_text'];
        //             $model->appeal = 1;
        //         } else {
        //             $errors[] = _e('Time is up for first control');
        //         }
        //     } else {
        //         $errors[] = _e('First Control is not be ennounced');
        //     }
        // }
        // if (isset($post['appeal2_text'])) {
        //     if ($model->examControl->status2 == ExamControl::STATUS_ANNOUNCED) {

        //         if ($model->examControl->appeal2_at >= $now) {

        //             $model->appeal2_text = $post['appeal2_text'];
        //             $model->appeal2 = 1;
        //         } else {
        //             $errors[] = _e('Time is up for second control');
        //         }
        //     } else {
        //         $errors[] = _e('Second Control is not be ennounced');
        //     }
        // }

        // ruxsatni chegaralash


        if (isset($post['appeal_text'])) {
            $model->appeal_text = $post['appeal_text'];
            $model->appeal = 1;
        }

        if (isset($post['appeal2_text'])) {
            $model->appeal2_text = $post['appeal2_text'];
            $model->appeal2 = 1;
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0)
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function uploadFile($question_file)
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = current_user_id() . "_" . $this->id . "_" . \Yii::$app->security->generateRandomString(5) . '.' . $question_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $question_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function eduAdminUploadFile($edu_admin_change_ball_upload_file)
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER_EDU_ADMIN)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER_EDU_ADMIN, 0777, true);
            }

            $fileName = current_user_id() . "_" . $this->student_id . "_" . $this->subject_id . "_" . $this->semester_id . "_"  . \Yii::$app->security->generateRandomString(5) . '.' . $edu_admin_change_ball_upload_file->extension;

            $miniUrl = self::UPLOADS_FOLDER_EDU_ADMIN . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $edu_admin_change_ball_upload_file->saveAs($url, false);
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

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),

        ];
    }


    // public function beforeSaveas($insert)
    // {
    //     if ($insert) {
    //         $this->created_by = current_user_id();
    //         $studentMark = StudentMark::find()
    //             ->where(['edu_semestr_subject_id' => $this->edu_semestr_subject_id])
    //             ->orderBy(['id' => 'DESC']);

    //         $examControlStudent = self::find()
    //             ->where(['is not null', 'student_mark_id'])->andWhere(['edu_semestr_subject_id' => $this->edu_semestr_subject_id])->one();
    //         $studentMarkNew = new StudentMark();
    //         if ($studentMark && $examControlStudent) {
    //             $studentMarkNew->attempt = $studentMark->attempt + 1;
    //         }
    //         $studentMarkNew->student_id = $this->student_id;
    //         $studentMarkNew->subject_id = $this->subject_id;
    //         $studentMarkNew->edu_semestr_id = $this->edu_semester_id;
    //         $studentMarkNew->edu_semestr_subject_id = $this->edu_semestr_subject_id;

    //         $studentMarkNew->course_id = $this->course_id;
    //         $studentMarkNew->semestr_id = $this->semestr_id;
    //         $studentMarkNew->edu_year_id = $this->edu_year_id;

    //         $studentMarkNew->edu_plan_id = $this->edu_plan_id;
    //         $studentMarkNew->faculty_id = $this->student->faculty_id;
    //         $studentMarkNew->edu_plan_id = $this->student->edu_plan_id;
    //         $studentMarkNew->exam_control_student_ball = $this->ball;
    //         $studentMarkNew->exam_control_student_ball2 = $this->ball2;
    //         if ($studentMarkNew->save()) {
    //             $this->student_mark_id = $studentMarkNew->id;
    //         }
    //     } else {
    //         $this->updated_by = current_user_id();
    //         $studentMark = StudentMark::findOne(['id' => $this->student_mark_id]);
    //         $studentMark->exam_control_student_ball = $this->ball;
    //         $studentMark->exam_control_student_ball2 = $this->ball2;

    //         $studentMark->save(false);
    //     }
    //     return parent::beforeSave($insert);
    // }

    // public function beforeSave($insert)
    // {
    //     // Check if it's an insert operation
    //     if ($insert) {
    //         $this->created_by = current_user_id();

    //         // Find the latest student mark for this edu_semestr_subject_id
    //         $latestStudentMark = StudentMark::find()
    //             ->where(['edu_semestr_subject_id' => $this->edu_semestr_subject_id])
    //             ->orderBy(['id' => 'DESC'])
    //             ->one();

    //         // Find the corresponding examControlStudent record
    //         $examControlStudent = self::find()
    //             ->where(['is not null', 'student_mark_id'])
    //             ->andWhere(['edu_semestr_subject_id' => $this->edu_semestr_subject_id])
    //             ->one();

    //         $studentMarkNew = new StudentMark();

    //         if ($latestStudentMark && $examControlStudent) {
    //             $studentMarkNew->attempt = $latestStudentMark->attempt + 1;
    //         }

    //         $studentMarkNew->student_id = $this->student_id;
    //         $studentMarkNew->subject_id = $this->subject_id;
    //         $studentMarkNew->edu_semestr_id = $this->edu_semester_id;
    //         $studentMarkNew->edu_semestr_subject_id = $this->edu_semestr_subject_id;
    //         $studentMarkNew->course_id = $this->course_id;
    //         $studentMarkNew->semestr_id = $this->semestr_id;
    //         $studentMarkNew->edu_year_id = $this->edu_year_id;
    //         $studentMarkNew->edu_plan_id = $this->edu_plan_id;
    //         $studentMarkNew->faculty_id = $this->student->faculty_id;
    //         $studentMarkNew->edu_plan_id = $this->student->edu_plan_id;
    //         $studentMarkNew->exam_control_student_ball = $this->ball;
    //         $studentMarkNew->exam_control_student_ball2 = $this->ball2;

    //         if ($studentMarkNew->save()) {
    //             $this->student_mark_id = $studentMarkNew->id;
    //         }
    //     } else {
    //         $this->updated_by = current_user_id();

    //         // Update the corresponding student mark record
    //         $studentMark = StudentMark::findOne(['id' => $this->student_mark_id]);
    //         $studentMark->exam_control_student_ball = $this->ball;
    //         $studentMark->exam_control_student_ball2 = $this->ball2;

    //         $studentMark->save(false);
    //     }

    //     return parent::beforeSave($insert);
    // }

    public function beforeSave($insert)
    {
        $currentUserId = current_user_id();

        $this->main_ball = (int)$this->ball + (int)$this->ball2;

        if ($insert) {
            $this->created_by = $currentUserId;
        } else {
            $this->updated_by = $currentUserId;
        }

        return parent::beforeSave($insert);
    }
}
