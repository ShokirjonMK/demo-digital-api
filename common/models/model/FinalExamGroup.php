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
class FinalExamGroup extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const EXAM = 3;


    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'final_exam_group';
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
                    'group_id',
//                    'lang_id',
                ], 'required'
            ],
            [
                [
                    'final_exam_id',
                    'group_id',
                    'vedomst',
                    'edu_semestr_subject_id',
                    'subject_id',
                    'edu_semestr_id',
                    'edu_plan_id',
                    'para_id',
                    'user_id',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'start_time',
                    'finish_time',
//                    'language_id',
//                    'lang_id',
                ], 'integer'
            ],
            [['date'], 'safe'],
            [['final_exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinalExam::className(), 'targetAttribute' => ['final_exam_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['para_id'], 'exist', 'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']],
//            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['lang_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'final_exam_id',
            'group_id',
            'edu_semestr_subject_id',
            'edu_semestr_id',
            'edu_plan_id',
            'vedomst',
            'date',
            'para_id',
            'user_id',
            'start_time',
            'finish_time',

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
            'eduSemestrSubject',
            'eduPlan',
            'eduSemestr',
            'finalExam',
            'group',
            'user',
            'para',
            'subject',

            'course',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getFinalExam()
    {
        return $this->hasOne(FinalExam::className(), ['id' => 'final_exam_id']);
    }

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
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


    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

}
