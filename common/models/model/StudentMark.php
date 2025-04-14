<?php

namespace common\models\model;


use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%student_mark}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int $subject_id
 * @property int $edu_semestr_id
 * @property int $edu_semestr_subject_id
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property int|null $edu_year_id
 * @property int|null $faculty_id
 * @property int|null $edu_plan_id
 * @property float|null $exam_control_student_ball
 * @property float|null $exam_control_student_ball2
 * @property float|null $exam_student_ball
 * @property float|null $ball
 * @property string|null $description
 * @property string|null $data
 * @property int|null $attempt
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $edu_lang_id
 * @property string|null $alphabet
 * @property string|null $mark
 * @property int|null $order
 *
 * @property Course $course
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemestr
 * @property EduSemestrSubject $eduSemestrSubject
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Semestr $semestr
 * @property Student $student
 * @property Subject $subject
 */
class StudentMark extends ActiveRecord
{
    public static $selected_language = 'uz';

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
        return '{{%student_mark}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'subject_id', 'edu_semestr_id', 'edu_semestr_subject_id'], 'required'],
            [['student_id', 'subject_id', 'edu_semestr_id', 'edu_semestr_subject_id', 'course_id', 'semestr_id', 'edu_year_id', 'faculty_id', 'edu_plan_id', 'attempt', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'edu_lang_id', 'order'], 'integer'],
            [['exam_control_student_ball', 'exam_control_student_ball2', 'exam_student_ball', 'ball'], 'number'],
            [['description'], 'string'],
            [['data'], 'safe'],
            [['alphabet', 'mark'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'student_id' => _e('Student ID'),
            'subject_id' => _e('Subject ID'),
            'edu_semestr_id' => _e('Edu Semestr ID'),
            'edu_semestr_subject_id' => _e('Edu Semestr Subject ID'),
            'course_id' => _e('Course ID'),
            'semestr_id' => _e('Semestr ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'faculty_id' => _e('Faculty ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'edu_lang_id' => _e('Edu Lang ID'),
            'exam_control_student_ball' => _e('Exam Control Student Ball'),
            'exam_control_student_ball2' => _e('Exam Control Student Ball2'),
            'exam_student_ball' => _e('Exam Student Ball'),
            'ball' => _e('Ball'),
            'alphabet' => _e('Alphabet'),
            'mark' => _e('Mark'),
            'description' => _e('Description'),
            'data' => _e('Data'),
            'attempt' => _e('Attempt'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'order' => _e('Order'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'student_id',
            'subject_id',
            'edu_semestr_id',
            'edu_semestr_subject_id',
            'course_id',
            'semestr_id',
            'edu_year_id',
            'faculty_id',
            'edu_plan_id',
            'exam_control_student_ball',
            'exam_control_student_ball2',
            'exam_student_ball',
            'ball',
            'description',
            'data',
            'attempt',
            'edu_lang_id',

            'alphabet',
            'mark',

            'order',
            'status',
            'is_deleted',
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
            'eduPlan',
            'eduSemestr',
            'eduSemestrSubject',
            'eduYear',
            'faculty',
            'semestr',
            'student',
            'subject',

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
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    /**
     * Gets query for [[EduSemestrSubject]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrSubjectQuery
     */
    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
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
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery|SemestrQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
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
     * StudentMark createItem From ExamControlStudent
     */
    public static function createItemFromControl($examControlStudent)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = new StudentMark();
        // $modelControl = StudentMark::find()
        //     ->where(['edu_semestr_subject_id' => $examControlStudent->edu_semestr_subject_id])
        //     ->andWhere(['is not null', 'exam_control_student_ball'])
        //     ->one();

        $modelControlNotNull = StudentMark::find()
            ->where(['edu_semestr_subject_id' => $examControlStudent->edu_semestr_subject_id])
            ->andWhere(['not', ['exam_control_student_ball' => null]])
            ->orderBy(['created_at' => SORT_DESC])  // assuming 'latest()' is for ordering
            ->one();

        if ($modelControlNotNull) {
            $model->attempt = $modelControlNotNull->attempt + 1;
        }

        $modelControlNull = StudentMark::find()
            ->where(['edu_semestr_subject_id' => $examControlStudent->edu_semestr_subject_id])
            ->andWhere(['exam_control_student_ball' => null])
            ->orderBy(['created_at' => SORT_DESC])  // assuming 'latest()' is for ordering
            ->one();

        if ($modelControlNull) {
            $model = $modelControlNull;

            $model->exam_control_student_ball = $examControlStudent->ball;
            $model->exam_control_student_ball2 = $examControlStudent->ball2;
        } else {
            $model->exam_control_student_ball = $examControlStudent->ball;
            $model->exam_control_student_ball2 = $examControlStudent->ball2;

            $model->edu_semestr_subject_id = $examControlStudent->edu_semestr_subject_id;
            $model->subject_id = $examControlStudent->subject_id;
            $model->student_id = $examControlStudent->student_id;
            $model->edu_semestr_id = $examControlStudent->examControl->edu_semester_id;

            $model->course_id = $examControlStudent->course_id;
            $model->semestr_id = $examControlStudent->semester_id;
            $model->edu_year_id = $examControlStudent->edu_year_id;
            $model->faculty_id = $examControlStudent->faculty_id;
            $model->edu_plan_id = $examControlStudent->edu_plan_id;

            $model->edu_lang_id = $examControlStudent->language_id;
        }

        if ($model->save()) {
            $transaction->commit();
            return [
                'status' => true,
                'data' => $model
            ];
        } else {
            $transaction->rollBack();
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
    }

    public static function updateItemFromControl($id, $ball, $ball2)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model = self::findOne($id);

        $model->exam_control_student_ball = $ball;
        $model->exam_control_student_ball2 = $ball2;


        if ($model->save()) {
            $transaction->commit();
            return [
                'status' => true,
                'data' => $model
            ];
        } else {
            $transaction->rollBack();
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
    }

    /**
     * StudentMark createItem From ExamStudent
     */
    public static function createItemFromExam($examStudent)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = new StudentMark();
        // $modelControl = StudentMark::find()
        //     ->where(['edu_semestr_subject_id' => $examControlStudent->edu_semestr_subject_id])
        //     ->andWhere(['is not null', 'exam_control_student_ball'])
        //     ->one();

        $modelControlNotNull = StudentMark::find()
            ->where('edu_semestr_subject_id', $examStudent->edu_semestr_subject_id)
            ->whereNotNull('exam_control_student_ball')
            ->latest()
            ->first();

        if ($modelControlNotNull) {
            $model->attempt = $modelControlNotNull->attempt + 1;
        }

        $modelControlNull = StudentMark::find()
            ->where('edu_semestr_subject_id', $examStudent->edu_semestr_subject_id)
            ->whereNotNull('exam_control_student_ball')
            ->latest()
            ->first();

        if ($modelControlNull) {
            $model = $modelControlNull;

            $model->exam_student_ball = $examStudent->ball;
        } else {
            $model->exam_student_ball = $examStudent->ball;

            $model->edu_semestr_subject_id = $examStudent->edu_semestr_subject_id;
            $model->subject_id = $examStudent->subject_id;
            $model->student_id = $examStudent->student_id;
            $model->edu_semestr_id = $model->eduSemestrSubject->edu_semestr_id;

            $model->course_id = $model->eduSemestr->course_id;
            $model->semestr_id = $model->eduSemestr->semester_id;
            $model->faculty_id = $examStudent->student->faculty_id;
            $model->edu_plan_id = $model->eduSemestr->edu_plan_id;
            $model->edu_year_id = $examStudent->edu_year_id;

            $model->edu_lang_id = $examStudent->lang_id;
        }

        if ($model->save()) {
            $transaction->commit();
            return [
                'status' => true,
                'data' => $model
            ];
        } else {
            $transaction->rollBack();
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
    }

    public static function updateItemFromExam($id, $ball)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model = self::findOne($id);

        $model->exam_student_ball = $ball;

        if ($model->save()) {
            $transaction->commit();
            return [
                'status' => true,
                'data' => $model
            ];
        } else {
            $transaction->rollBack();
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
    }

    /**
     * StudentMark createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        // $errors[] = $model->errors;
        // $transaction->rollBack();
        // return simplify_errors($errors);


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * StudentMark updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $errors[] = $model->errors;
        $transaction->rollBack();
        return simplify_errors($errors);

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    // /**
    //  * {@inheritdoc}
    //  * @return StudentMarkQuery the active query used by this AR class.
    //  */
    // public static function find()
    // {
    //     return new StudentMarkQuery(get_called_class());
    // }

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
