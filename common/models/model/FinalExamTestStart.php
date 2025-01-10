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
class FinalExamTestStart extends \yii\db\ActiveRecord
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
        return 'final_exam_test_start';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'final_exam_test_id',
                ], 'required'
            ],
            [
                [
                    'final_exam_test_id',
                    'student_mark_id',
                    'student_id',
                    'user_id',

                    'exam_type',
                    'exam_form_type',

                    'start_time',
                    'finish_time',
                    'correct',
                    'attends_count',

                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                ], 'integer'
            ],
            [['ball'] , 'number'],
            [['password'] , 'string' , 'max' => 255],
            [['final_exam_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinalExamTest::className(), 'targetAttribute' => ['final_exam_test_id' => 'id']],
            [['student_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentMark::className(), 'targetAttribute' => ['student_mark_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['finish_time' , 'validTime']
        ];
    }

    public function validTime($attribute, $params)
    {
        if ($this->start_time >= $this->finish_time) {
            $this->addError($attribute, _e('Boshlanish vaqti yakunanish vaqtidan kichik bo\'lishi shart!'));
        }
    }

    public function fields()
    {
        $fields =  [
            'id',

            'final_exam_test_id',
            'student_mark_id',
            'student_id',
            'user_id',

            'exam_type',
            'exam_form_type',
            'ball',

            'currect_time' => function () {
                return time();
            },
            'duration' => function () {
                return $this->finalExamTest->finalExam->subject->all_time;
            },
            'question_count' => function () {
                return $this->finalExamTest->finalExam->subject->question_count;
            },

            'edu_semestr_subject_id' => function () {
                return $this->finalExamTest->finalExam->edu_semestr_subject_id;
            },

            'start_time',
            'finish_time',
            'start_date' => function () {
                return date("Y-m-d H:i:s" , $this->start_time);
            },
            'finish_date' => function () {
                return date("Y-m-d H:i:s" , $this->finish_time);
            },
            'correct',
            'attends_count',

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
            'finalExamTest',
            'studentMark',
            'student',
            'user',
            'finalExamTestQuestion',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getFinalExamTest()
    {
        return $this->hasOne(FinalExamTest::className(), ['id' => 'final_exam_test_id']);
    }

    public function getFinalExamTestQuestion()
    {
        $t = false;
        $finalExam = $this->finalExamTest->finalExam;
        if (!isRole('student')) {
            $t = true;
        } elseif ($this->status == 2 && $finalExam->status == 3) {
            $t = true;
        }

        if ($t) {
            return $this->hasMany(FinalExamTestQuestion::className(), ['final_exam_test_start_id' => 'id'])->where(['is_deleted' => 0]);
        }
        return [];
    }


    public function getQuestionsCorrectCount()
    {
        return $this->hasMany(FinalExamTestQuestion::className(), ['final_exam_test_start_id' => 'id'])
            ->where(['is_deleted' => 0 , 'is_correct' => 1 , 'student_id' => $this->student_id])
            ->count();
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


    public static function updateItem($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $finalExam = $model->finalExamTest->finalExam;

        if ($finalExam->status == 3) {
            if (isset($post['start_time'])) {
                $model->start_time = strtotime($post['start_time']);
            }
            if (isset($post['finish_time'])) {
                $model->finish_time = strtotime($post['finish_time']);
            }
            if (isset($post['exam_form_type'])) {
                $model->exam_form_type = $post['exam_form_type'];
            }
            if (isset($post['status'])) {
                $model->status = $post['status'];
                if ($model->status == 3) {
                    $result = self::finish($model);
                    if (!$result['is_ok']) {
                        $transaction->rollBack();
                        return simplify_errors($result['errors']);
                    }
                }
            }

            if (!($model->validate())) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            $model->save(false);
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


    public static function addBall($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();

        $errors = [];
        $finalExam = $model->finalExamTest->finalExam;

        if ($finalExam->status < 7) {
            if (isset($post['ball'])) {
                $model->ball = $post['ball'];
                $model->status = 3;
                $model->save(false);
            } else {
                $errors[] = [_e('Ball yuborilmagan!')];
            }
        } else {
            $errors[] = [_e('Imtixon yakunlangan')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function ipCheck($model)
    {
        $type = $model->exam_form_type;
        if ($type == 2) {
            $finalExam = $model->finalExamTest->finalExam;
            $buildingId = $finalExam->building_id;
            $roomId = $finalExam->room_id;
            $result = FinalExamTestStart::ipBuilding($buildingId , $roomId);
            if (!$result) {
                return ['is_ok' => false];
            }
        }
        return ['is_ok' => true];
    }


    public static function view($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isRole('student')) {
            $student = current_student();
            if ($model->student_id == $student->id) {
                $finalExam = $model->finalExamTest->finalExam;
                $time = time();
                $subject = $finalExam->subject;
                $allTime = $subject->all_time;
                $end_time = strtotime("+".$allTime." minutes", $time);

                if ($finalExam->status == 3) {
                    if ($model->status == 1) {
                        if ($model->start_time <= $time && $model->finish_time >= $time) {
                            if ($model->finish_time <= $end_time) {
                                $end_time = $model->finish_time;
                            }
                            $model->start_time = $time;
                            $model->finish_time = $end_time;
                            $model->status = 2;
                            $model->update(false);
                        } elseif ($model->start_time > $time) {
                            $errors[] = [_e('Imtixon boshlanishiga vaqt bor.')];
                        } else {
                            $errors[] = [_e('Imtixon vaqti yakunlandi.')];
                        }
                    } elseif ($model->status == 2) {
                        if ($model->finish_time < $time) {
                            $result = self::finish($model);
                            if (!$result['is_ok']) {
                                $transaction->rollBack();
                                return simplify_errors($result['errors']);
                            }
                            $model = $result['data'];
                        }
                    } elseif ($model->status == 3) {
                        // $errors[] = [_e('Siz imtixonni yakunlagansiz.')];
                    } else {
                        $errors[] = [_e('Imtixonga ruxsat berilmagan.')];
                    }
                } elseif ($finalExam->status < 3 || ($finalExam->status < 7 && $finalExam->status > 3)) {
                    $errors[] = [_e('Testni ko\'rish imkoni mavjud emas!')];
                }
            } else {
                $errors[] = [_e('Imtixon siz uchun belgilanmagan!')];
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return ['is_ok' => true , 'model' => $model];
        }
        $transaction->rollBack();
        return ['is_ok' => false , 'errors' => simplify_errors($errors)];
    }


    public static function finish($model)
    {
        $errors = [];
        $time = time();

        if (!isset($model->studentMark)) {
            $errors[] = [_e('Student Mark mavjud emas.')];
            return ['is_ok' => false, 'errors' => $errors];
        }
        $mark = $model->studentMark;
        $subject = $mark->subject;
        $correct = $model->questionsCorrectCount;
        $ball = round(($mark->max_ball / $subject->question_count) * $correct);

//        $maxBall = $mark->max_ball;
//        if ($mark->faculty_id == 6) {
//            $minBall30 = $maxBall * 0.45;
//            $minBall60 = $maxBall * 0.6;
//            if ($minBall30 <= $ball && $minBall60 > $ball) {
//                $numbers = [60, 65, 70];
//                $randBall = $numbers[array_rand($numbers)];
//                $randBall = round($maxBall * ($randBall / 100));
//                $ball = $randBall;
//            }
//        }

        if ($model->status == 2) {
            if ($model->start_time <= $time && $model->finish_time > $time) {
                $model->finish_time = $time;
            }
        }
        $model->status = 3;
        $model->correct = $correct;
        $model->ball = $ball;
        $model->update(false);

        if (empty($errors)) {
            return ['is_ok' => true , 'data' => $model];
        }

        return ['is_ok' => false, 'errors' => $errors];
    }


    public static function studentFinish($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $finalExam = $model->finalExamTest->finalExam;

        if ($finalExam->status == 3 && $model->status == 2) {
            $result = self::finish($model);
            if (!$result['is_ok']) {
                $transaction->rollBack();
                return simplify_errors($result['errors']);
            }
        } else {
            $errors[] = [_e('Testni o\'z vaqtida ')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function ipBuilding($buildingId, $roomId)
    {
//        $ipAddresses = [
//            2 => '93.188.82.74',
//            3 => '195.158.16.235',
//            4 => '213.230.108.154',
//            6 => '213.230.108.154',
//            5 => '86.62.3.13',
//            7 => '83.222.7.75',
//        ];

//        $ip = $ipAddresses[$building] ?? '00.00';

        $getIpMK = getIpMK();

//        if (in_array($buildingId, [2, 3])) {
//            return RoomIp::find()
//                ->where(['building_id' => $buildingId, 'ip' => $getIpMK, 'status' => 1, 'is_deleted' => 0])
//                ->exists();
//        }

        $parts = explode('.', $getIpMK);
        if (isset($parts[0], $parts[1]) && $parts[0] . '.' . $parts[1] === '86.62') {
            return true;
        }

        return false;
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
