<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Student;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%exam_control}}".
 *
 * @property int $id
 * @property int $time_table_id
 * @property int|null $start
 * @property int|null $start2
 * @property int|null $finish
 * @property int|null $finish2
 * @property float|null $max_ball
 * @property float|null $max_ball2
 * @property int|null $duration
 * @property int|null $duration2
 * @property string|null $question
 * @property string|null $question2
 * @property string|null $question_file
 * @property string|null $question2_file
 * @property int|null $course_id
 * @property int|null $semester_id
 * @property int $edu_year_id
 * @property int $subject_id
 * @property int $language_id
 * @property int $edu_plan_id
 * @property int $teacher_user_id
 * @property int $edu_semester_id
 * @property int $subject_category_id
 * @property int|null $archived
 * @property int|null $old_exam_control_id
 * @property int|null $faculty_id
 * @property int|null $direction_id
 * @property int|null $type
 * @property int|null $category
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property Direction $direction
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemester
 * @property EduYear $eduYear
 * @property ExamControlStudent[] $examControlStudents
 * @property Faculty $faculty
 * @property Language $language
 * @property Semestr $semester
 * @property Subject $subject
 * @property SubjectCategory $subjectCategory
 * @property TeacherAccess $teacher_access_id
 * @property User $teacherUser
 * @property TimeTable $timeTable
 */
class ExamControl extends \yii\db\ActiveRecord
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
    const STATUS_ANNOUNCED = 2;
    const STATUS_FINISHED = 3;

    const appeal_time = 3 * 24 * 60 * 60; // 3 kun soat

    const UPLOADS_FOLDER = 'uploads/exam_control/question/';
    public $upload_file;
    public $upload2_file;
    public $questionFileMaxSize = 1024 * 1024 * 5; // 10 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        // Check if user wants archived data (e.g., via a parameter or a flag)
        if (Yii::$app->request->get('archived')) {
            return 'exam_control_23_24';
        }
        return 'exam_control';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'time_table_id',
                    // 'edu_year_id',
                    // 'subject_id',
                    // 'language_id',
                    // 'edu_plan_id',
                    // 'teacher_user_id',
                    // 'edu_semester_id',
                    // 'subject_category_id'
                ],
                'required'
            ],
            [
                [
                    'edu_semestr_subject_id',
                    'appeal_at',
                    'appeal2_at',
                    'status2',
                    'time_table_id',
                    'teacher_access_id',
                    'start',
                    'start2',
                    'finish',
                    'finish2',
                    'duration',
                    'duration2',
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
                    'faculty_id',
                    'direction_id',
                    'type',
                    'category',
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
            [['max_ball', 'max_ball2'], 'number'],
            [['question', 'question2'], 'string'],
            [['question_file', 'question2_file'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semester_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semester_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['time_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['time_table_id' => 'id']],
            [
                ['edu_semestr_subject_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EduSemestrSubject::className(),
                'targetAttribute' => ['edu_semestr_subject_id' => 'id']
            ],
            [['upload_file', 'upload2_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->questionFileMaxSize],
            [['time_table_id'], 'unique'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_table_id' => _e('Time Table ID'),
            'edu_semestr_subject_id' => _e('edu_semestr_subject ID'),
            'start' => _e('Start'),
            'start2' => _e('Start2'),
            'finish' => _e('Finish'),
            'finish2' => _e('Finish2'),
            'max_ball' => _e('Max Ball'),
            'max_ball2' => _e('Max Ball2'),
            'duration' => _e('Duration'),
            'duration2' => _e('Duration2'),
            'question' => _e('Question'),
            'question2' => _e('Question2'),
            'question_file' => _e('Question File'),
            'question2_file' => _e('Question2 File'),
            'course_id' => _e('Course ID'),
            'semester_id' => _e('Semester ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'subject_id' => _e('Subject ID'),
            'language_id' => _e('Language ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'teacher_user_id' => _e('Teacher User ID'),
            'teacher_access_id' => _e('Teacher Access ID'),
            'edu_semester_id' => _e('Edu Semester ID'),
            'subject_category_id' => _e('Subject Category ID'),
            'archived' => _e('Archived'),
            'old_exam_control_id' => _e('Old Exam Control ID'),
            'faculty_id' => _e('Faculty ID'),
            'direction_id' => _e('Direction ID'),
            'type' => _e('Type'),
            'category' => _e('Category'),


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
            'name' => function ($model) {
                return $model->translate->name ?? $this->subject->translate->name . ' ' . _e('control work') . ' ' . $this->eduSemester->semestr_id . ' - sm';
            },
            'time_table_id',
            'edu_semestr_subject_id',

            'start',
            'start2',
            'finish',
            'finish2',
            'max_ball',
            'max_ball2',
            'duration',
            'duration2',
            'question',
            'question2',
            'question_file',
            'question2_file',
            'course_id',
            'semester_id',
            'edu_year_id',
            'subject_id',
            'language_id',
            'edu_plan_id',
            'teacher_user_id',
            'teacher_access_id',
            'edu_semester_id',
            'subject_category_id',
            'archived',
            'old_exam_control_id',
            'faculty_id',
            'direction_id',
            // 'type',
            // 'category',

            'order',
            'is_deleted',
            'appeal_at',
            'appeal2_at',
            'status',
            'status2',

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

            'course',
            'direction',
            'eduPlan',
            'eduSemester',
            'eduYear',
            'examControlStudents',
            'faculty',
            'language',
            'semester',
            'subject',
            'subjectCategory',
            'teacherUser',
            'teacherAccess',
            'timeTable',

            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
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
     * Gets query for [[ExamControlStudents]].
     *
     * @return \yii\db\ActiveQuery|ExamControlStudentQuery
     */
    public function getExamControlStudents()
    {
        if (isRole('student')) {
            return $this->hasMany(ExamControlStudent::className(), ['exam_control_id' => 'id'])
                ->onCondition(['student_id' => $this->student()]);
        }

        return $this->hasMany(ExamControlStudent::className(), ['exam_control_id' => 'id']);
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
     * Gets query for [[TeacherAccess]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }
    /**
     * Gets query for [[TeacherUser]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTeacherUser()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_user_id']);
    }

    /**
     * Gets query for [[TimeTable]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTimeTable()
    {
        return $this->hasOne(TimeTable::className(), ['id' => 'time_table_id']);
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

        if ($model->start > $model->finish) {
            $errors[] = _e("Start of exam can not be greater than finish");
        }

        if ($model->timeTable->teacher_user_id != current_user_id() && isRole('teacher')) {
            $errors[] = _e('This is not your timeTable');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->timeTable->subject_category_id == 1) {
            $errors[] = _e('Lecture has no control work');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!isset($post['max_ball'])) {
            $model->max_ball = (int)Yii::$app->params['exam_control_ball'];
        }
        if (!isset($post['max_ball2'])) {
            $model->max_ball2 = 0;
        }

        if ((int)$model->max_ball + (int)$model->max_ball2 != (int) Yii::$app->params['exam_control_ball']) {
            $errors[] = _e('control work') . " max ball " .  Yii::$app->params['exam_control_ball'];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->edu_semestr_subject_id = $model->timeTable->edu_semestr_subject_id;
        $model->course_id = $model->timeTable->course_id;
        $model->semester_id = $model->timeTable->semester_id;
        $model->edu_year_id = $model->timeTable->edu_year_id;
        $model->subject_id = $model->timeTable->subject_id;
        $model->language_id = $model->timeTable->language_id;
        $model->edu_plan_id = $model->timeTable->edu_plan_id;
        $model->edu_semester_id = $model->timeTable->edu_semester_id;
        $model->subject_category_id = $model->timeTable->subject_category_id;
        $model->faculty_id = $model->timeTable->eduPlan->faculty_id;
        $model->direction_id = $model->timeTable->eduPlan->direction_id;
        $model->teacher_access_id = $model->timeTable->teacher_access_id;
        // $model->teacher_user_id = $model->teacherAccess->user_id;
        $model->teacher_user_id = $model->timeTable->teacher_user_id;



        // faqat 4- kurslar uchun
        // if (
        //     !in_array($model->edu_plan_id, [15, 22, 27, 56, 132, 131, 108, 107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 55])
        // ) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {

            // question file saqlaymiz
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_FileUrl = $model->uploadFile($model->upload_file);
                if ($upload_FileUrl) {
                    $model->question_file = $upload_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // question file saqlaymiz
            $model->upload2_file = UploadedFile::getInstancesByName('upload2_file');
            if ($model->upload2_file) {
                $model->upload2_file = $model->upload2_file[0];
                $upload2_FileUrl = $model->uploadFile($model->upload2_file);
                if ($upload2_FileUrl) {
                    $model->question2_file = $upload2_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            if (isset($post['name'])) {
                $has_error = Translate::checkingAll($post);
                if ($has_error['status']) {

                    if (isset($post['description'])) {
                        Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                    }
                } else {
                    $transaction->rollBack();
                    return double_errors($errors, $has_error['errors']);
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

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['start'])) {
            $model['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish'] = strtotime($post['finish']);
        }

        if (isset($post['start2'])) {
            $model['start2'] = strtotime($post['start2']);
        }
        if (isset($post['finish2'])) {
            $model['finish2'] = strtotime($post['finish2']);
        }

        // faqat 4- kurslar uchun
        // if (
        //     !in_array($model->edu_plan_id, [15, 22, 27, 56, 132, 131, 108, 107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 55])
        // ) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        if ($model->start > $model->finish) {
            $errors[] = _e("Start of exam can not be greater than finish");
        }

        if ($model->timeTable->teacher_user_id != current_user_id() && isRole('teacher')) {
            $errors[] = _e('This is not your timeTable');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->timeTable->subject_category_id == 1) {
            $errors[] = _e('Lecture has no control work');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->timeTable->parent_id != null) {
            $errors[] = _e("Choose main time table");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // if (!isset($post['max_ball'])) {
        //     $model->max_ball = (int)Yii::$app->params['exam_control_ball'];
        // }

        // if (!isset($post['max_ball2'])) {
        //     $model->max_ball2 = 0;
        // }

        if ((int)$model->max_ball + (int)$model->max_ball2 != (int) Yii::$app->params['exam_control_ball']) {
            $errors[] = _e('control work') . " max ball " .  Yii::$app->params['exam_control_ball'];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->edu_semestr_subject_id = $model->timeTable->edu_semestr_subject_id;
        $model->course_id = $model->timeTable->course_id;
        $model->semester_id = $model->timeTable->semester_id;
        $model->edu_year_id = $model->timeTable->edu_year_id;
        $model->subject_id = $model->timeTable->subject_id;
        $model->language_id = $model->timeTable->language_id;
        $model->edu_plan_id = $model->timeTable->edu_plan_id;
        $model->teacher_user_id = $model->timeTable->teacher_user_id;
        $model->edu_semester_id = $model->timeTable->edu_semester_id;
        $model->subject_category_id = $model->timeTable->subject_category_id;
        $model->faculty_id = $model->timeTable->eduPlan->faculty_id;
        $model->direction_id = $model->timeTable->eduPlan->direction_id;
        $model->teacher_access_id = $model->timeTable->teacher_access_id;
        // $model->teacher_user_id = $model->teacherAccess->user_id;

        if ($model->status == 2) {
            if (is_null($model->appeal_at))
                $model->appeal_at = time() + self::appeal_time;
        }

        if ($model->status2 == 2) {
            if (is_null($model->appeal2_at))
                $model->appeal2_at = time() + self::appeal_time;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // question file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $upload_FileUrl = $model->uploadFile($model->upload_file);
            if ($upload_FileUrl) {
                $model->question_file = $upload_FileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // question file saqlaymiz
        $model->upload2_file = UploadedFile::getInstancesByName('upload2_file');
        if ($model->upload2_file) {
            $model->upload2_file = $model->upload2_file[0];
            $upload2_FileUrl = $model->uploadFile($model->upload2_file);
            if ($upload2_FileUrl) {
                $model->question2_file = $upload2_FileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->save()) {
            if (isset($post['name'])) {
                $has_error = Translate::checkingUpdate($post);
                if ($has_error['status']) {

                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                } else {
                    $transaction->rollBack();
                    return double_errors($errors, $has_error['errors']);
                }
            }

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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }

        return parent::beforeSave($insert);
    }


    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            TimeTable::updateAll(['has_exam_control' => 1], ['id' => $this->time_table_id]);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public static function statusList()
    {

        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
        ];
    }


    public function uploadFile($question_file)
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(5) . '.' . $question_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $question_file->saveAs($url, false);
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
}
