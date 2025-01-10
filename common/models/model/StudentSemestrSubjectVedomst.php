<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $name
 * @property int $building_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Building $building
 * @property TimeTable1[] $timeTables
 */
class StudentSemestrSubjectVedomst extends \yii\db\ActiveRecord
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
        return 'student_semestr_subject_vedomst';
    }


    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['subject_id','student_id','semestr_id'], 'required'],
            [['student_semestr_subject_id','subject_id','student_id','semestr_id','edu_year_id','student_user_id','group_id','ball','passed','vedomst', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['student_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentSemestrSubject::className(), 'targetAttribute' => ['student_semestr_subject_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'student_semestr_subject_id',
            'subject_id',
            'student_id',
            'semestr_id',
            'edu_year_id',
            'student_user_id',
            'group_id',
            'ball',
            'passed',
            'vedomst',

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
        $extraFields =  [
            'subject',
            'student',
            'studentUser',
            'eduYear',
            'semestr',
            'group',
            'studentMark',
            'studentSemestrSubject',
            'sheet',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Building]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getStudentSemestrSubject()
    {
        return $this->hasOne(StudentSemestrSubject::className(), ['id' => 'student_semestr_subject_id'])->where(['is_deleted' => 0]);
    }

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    public function getStudentMark()
    {
        return $this->hasMany(StudentMark::className(), ['student_semestr_subject_vedomst_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public function getSheet()
    {
        $studentMarks = $this->studentMark;
        $eduSemestrSubject = $this->studentSemestrSubject;
        if (!$eduSemestrSubject) {
            return [
                'ball' => -1,
                'rating' => 0,
                'attend' => 0
            ];
        }
        $eduSemestrSubject = EduSemestrSubject::findOne($eduSemestrSubject->edu_semestr_subject_id);
        $type = $eduSemestrSubject->type;

        $examBall = 0;
        $controlBall = 0;

        $examCategorys = EduSemestrExamsType::find()
            ->where([
                'edu_semestr_subject_id' => $eduSemestrSubject->id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();

        if (count($examCategorys) > 0) {
            foreach ($examCategorys as $examCategory) {
                if ($examCategory->exams_type_id != 3) {
                    $controlBall = $controlBall + $examCategory->max_ball;
                } else {
                    $examBall = $examCategory->max_ball;
                }
            }
        }

        $persentExamBall = (int)(($examBall * 60) / 100);
        $persentControlBall = (int)(($controlBall * 60) / 100);

        if (count($studentMarks) > 0) {
            $ball = 0;
            $yak = 0;
            $attend = 0;
            foreach ($studentMarks as $studentMark) {
                if ($studentMark->exam_type_id == 3) {
                    $yak =  $studentMark->ball;
                    if ($studentMark->attend == 1) {
                        $attend = 1;
                    }
                } else {
                    $ball = $ball + $studentMark->ball;
                }
            }

            if ($ball < $persentControlBall && $type == 0) {
               return [
                   'ball' => 0,
                   'rating' => 0,
                   'attend' => 2,
               ];
            } else {
                if ($yak >= $persentExamBall) {
                    $all_ball = $ball + $yak;
                    if ($all_ball >= 60 && $all_ball < 70) {
                        $rating = 3;
                    } elseif ($all_ball >= 70 && $all_ball < 90) {
                        $rating = 4;
                    } elseif ($all_ball >= 90) {
                        $rating = 5;
                    }
                    return [
                        'ball' => $all_ball,
                        'rating' => $rating,
                        'attend' => 1
                    ];
                } else {
                    if ($attend == 1) {
                        $att = 1;
                        $rating = 2;
                    } else {
                        $att = 2;
                        $rating = 0;
                    }
                    return [
                        'ball' => 0,
                        'rating' => $rating,
                        'attend' => $att
                    ];
                }
            }
        }
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getStudentUser()
    {
        return $this->hasOne(User::className(), ['id' => 'student_user_id']);
    }

    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }



        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $studentMarks = json_decode($post['studentMarks']);

        foreach ($studentMarks as $studentMarkId => $studentMarkBall) {
            $query = StudentMark::findOne([
                'id' => $studentMarkId,
                'student_semestr_subject_vedomst_id' => $model->id,
                'is_deleted' => 0
            ]);
            if (!$query) {
                $errors[] = $studentMarkId. _e(' Student Mark Id not found.');
            } else {
                $query->ball = $studentMarkBall;
                if (!$query->validate()) {
                    $errors[] = $query->errors;
                    return simplify_errors($errors);
                } else {
                    $query->update(false);
                }
            }
        }


        $marks = StudentMark::find()
            ->where([
                'student_semestr_subject_vedomst_id' => $model->id,
                'is_deleted' => 0
            ])->all();
        $allBall = 0;
        $examBall = 0;
        foreach ($marks as $mark) {
            if ($mark->exam_type_id == 3) {
                $examModel = $mark;
                $examBall = $mark->ball;
            }
            $allBall = $allBall + $mark->ball;
        }


        $t = false;
        if ($examBall >= 18 && $allBall >= 60) {
            $examModel->passed = 1;
            $examModel->attend = 1;
            $examModel->status = 2;
            $examModel->update(false);
            $t = true;
        }

        if ($t) {
            $model->ball = $allBall;
            $model->passed = 1;
            $model->update(false);
            $studentSemestrSubject = $model->studentSemestrSubject;
            $studentSemestrSubject->all_ball = $allBall;
            $studentSemestrSubject->closed = 1;
            $studentSemestrSubject->update(false);
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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
