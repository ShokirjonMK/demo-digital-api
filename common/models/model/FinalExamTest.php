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
 * This is the model class for table "{{%exam}}".
 *
 * @property int $id
 * @property int $group_id
 * @property int|null $start_time
 * @property int|null $finish_time
 * @property float|null $max_ball
 * @property int|null $duration
 * @property string|null $question
 * @property string|null $file
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property int $edu_year_id
 * @property int $language_id
 * @property int $edu_plan_id
 * @property int $edu_semestr_id
 * @property int $edu_semester_subject_id
 * @property int|null $faculty_id
 * @property int|null $direction_id
 * @property int|null $type
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
 * @property TimeTable1 $timeTable
 */
class FinalExamTest extends \yii\db\ActiveRecord
{
    const EXAM_TEST_COUNT = 20;
    const EXAM_TEST_DURATION = 40;

    const INACTIVE = 0;
    const ACTIVE = 1;
    const FINISHED = 2;

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
        return 'final_exam_test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'final_exam_id',
                ], 'required'
            ],
            [
                [
                    'final_exam_id',
                    'student_mark_id',
                    'student_id',
                    'user_id',
                    'attends_count',
                    'correct',
                    'ball',

                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                ], 'integer'
            ],
            [['final_exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinalExam::className(), 'targetAttribute' => ['final_exam_id' => 'id']],
            [['student_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentMark::className(), 'targetAttribute' => ['student_mark_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',

            'final_exam_id',
            'student_mark_id',
            'student_id',
            'user_id',
            'attends_count',
            'correct',
            'ball',

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
            'profile',
            'finalExam',
            'studentMark',
            'student',
            'user',
            'finalExamTestStart',
            'addStatus',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getFinalExam()
    {
        return $this->hasOne(FinalExam::className(), ['id' => 'final_exam_id']);
    }

    public function getStudentMark()
    {
        return $this->hasOne(StudentMark::className(), ['id' => 'student_mark_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getFinalExamTestStart()
    {
        return $this->hasMany(FinalExamTestStart::className(), ['final_exam_test_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public function getAddStatus()
    {
        foreach ($this->finalExamTestStart as $start) {
            if ($start->status != 3) {
                return 0;
            }
        }
        return 1;
    }


    public static function add($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $finalExam = $model->finalExam;

        if ($finalExam->status == 3) {
            $query = FinalExamTestStart::find()
                ->where(['final_exam_test_id' => $model->id, 'student_mark_id' => $model->student_mark_id, 'is_deleted' => 0])
                ->orderBy('attends_count desc')
                ->one();

            $subject = $finalExam->subject;
            $subjectId = $subject->id;

            if ($query) {
                if ($query->status == 3) {
                    $test = new FinalExamTestStart();
                    $test->final_exam_test_id = $model->id;
                    $test->student_mark_id = $model->student_mark_id;
                    $test->student_id = $model->student_id;
                    $test->user_id = $model->user_id;
                    $test->exam_type = $finalExam->exam_type;
                    $test->exam_form_type = $finalExam->exam_form_type;
                    $test->password = 'ik';
                    $test->start_time = $finalExam->start_time;
                    $test->finish_time = $finalExam->finish_time;
                    $test->attends_count = ($query->attends_count + 1);
                    $test->save(false);

                    $questions = Test::find()
                        ->where([
                            'subject_id' => $subjectId,
                            'exam_type_id' => $finalExam->exams_type_id,
                            'status' => 1,
                            'is_deleted' => 0,
                            'is_checked' => 1,
                            'type' => 2,
                            'lang_id' => $finalExam->lang_id,
                        ])
                        ->orderBy(new \yii\db\Expression('RAND()'))
                        ->limit($subject->question_count)
                        ->all();

                    if (count($questions) != $subject->question_count) {
                        $errors[] = [_e('Not enough science questions!')];
                    } else {
                        foreach ($questions as $question) {
                            $studentQuestion = new FinalExamTestQuestion();
                            $studentQuestion->final_exam_test_start_id = $test->id;
                            $studentQuestion->student_mark_id = $model->student_mark_id;
                            $studentQuestion->student_id = $model->student_id;
                            $studentQuestion->user_id = $model->user_id;
                            $studentQuestion->test_id = $question->id;
                            $studentQuestion->option = Test::optionsArray($question->id);
                            $studentQuestion->save(false);
                        }
                    }
                } else {
                    $errors[] = [_e('Oxirgi imtixon yakunlangan bolishi shart!')];
                }
            } else {
                $errors[] = [_e('XATOLIK!!!')];
            }
        } else {
            $errors[] = [_e('Imtixon jarayonda emas!')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function updateItem($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $finalExam = $model->finalExam;

        if ($finalExam->status == 3) {
            if (isset($post['status'])) {
                $model->status = $post['status'];
                if ($finalExam->exams_type_id == 3 && !isRole('admin')) {
                    $model->status = $model->studentMark->percent25;
                }
            }
            $model->update(false);
        } else {
            $errors[] = [_e('Imtixon jarayonda emas!')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
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
}
