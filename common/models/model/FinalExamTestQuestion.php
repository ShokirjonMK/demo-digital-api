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
class FinalExamTestQuestion extends \yii\db\ActiveRecord
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
        return 'final_exam_test_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'final_exam_test_start_id',
                ], 'required'
            ],
            [
                [
                    'final_exam_test_start_id',
                    'student_mark_id',
                    'student_id',
                    'user_id',

                    'test_id',
                    'option_id',
                    'is_correct',

                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                ], 'integer'
            ],
            [['option'] , 'string' , 'max' => 255],
            [['final_exam_test_start_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinalExamTestStart::className(), 'targetAttribute' => ['final_exam_test_start_id' => 'id']],
            [['student_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentMark::className(), 'targetAttribute' => ['student_mark_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => Option::className(), 'targetAttribute' => ['option_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'student_id',
            'user_id',
            'option',
            'option_id',
            'is_correct' => function () {
                if (isRole('student')) {
                    return 0;
                }
                return $this->is_correct;
            },
            'time' => function () {
                return time();
            },

            'status',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'finalExamTestStart',
            'studentMark',
            'student',
            'user',
            'test',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getFinalExamTestStart()
    {
        return $this->hasOne(FinalExamTestStart::className(), ['id' => 'final_exam_test_start_id']);
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

    public function getTest()
    {
        $test = Test::findOne($this->test_id);
        $textBody = $test->testBody;
        return [
            'id' => $this->id,
            'question' => [
                'id' => $test->id,
                'text' => $textBody->text,
                'file' => $textBody->file,
            ],
            'options' => $test->arrayOption,
        ];
    }

    public static function studentUpdate($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();
        $start = $model->finalExamTestStart;
        $finalExam = $start->finalExamTest->finalExam;
        if ($finalExam->status <= 3 && $start->status == 2 && $start->start_time <= $time && $time <= $start->finish_time) {
            if (isset($post['option_id'])) {
                $option = Option::findOne([
                    'test_id' => $model->test_id,
                    'id' => $post['option_id'],
                ]);
                if (!$option) {
                    $errors[] = [_e('Variant mavjud emas!')];
                } else {
                    $model->option_id = $post['option_id'];
                    $model->is_correct = $option->is_correct;
                    $model->update(false);
                }
            } else {
                $errors[] = [_e('Option Id mavjud emas!')];
            }
        } else {
            $errors[] = [_e('ERRORS!')];
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
