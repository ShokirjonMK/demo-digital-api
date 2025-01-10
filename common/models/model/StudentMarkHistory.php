<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "building".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Room[] $rooms
 */
class StudentMarkHistory extends \yii\db\ActiveRecord
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
        return 'student_mark_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'edu_semestr_exams_type_id',
                    'exam_type_id',
                    'group_id',
                    'student_id',
                    'student_user_id',
                    'max_ball',
                    'edu_semestr_subject_id',
                    'subject_id',
                ], 'required'
            ],
            [
                [
                    'student_mark_id',
                    'exam_control_id',
                    'exam_id',
                    'exam_control_student_id',
                    'exam_student_id',
                    'exam_type_id',
                    'edu_semestr_exams_type_id',
                    'type',

                    'group_id',
                    'student_id',
                    'student_user_id',
                    'edu_semestr_subject_id',
                    'edu_plan_id',
                    'subject_id',
                    'edu_semestr_id',
                    'faculty_id',
                    'direction_id',
                    'semestr_id',
                    'course_id',
                    'update_date',

                    'max_ball',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['ball'], 'safe'],
            [['student_mark_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentMark::className(), 'targetAttribute' => ['student_mark_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['edu_semestr_exams_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrExamsType::className(), 'targetAttribute' => ['edu_semestr_exams_type_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['exam_control_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamControl::className(), 'targetAttribute' => ['exam_control_id' => 'id']],
            [['exam_control_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamControlStudent::className(), 'targetAttribute' => ['exam_control_student_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],

            ['ball' , 'validateBall'],

        ];
    }

    public function validateBall($attribute, $params)
    {
        if ($this->ball > $this->max_ball) {
            $this->addError($attribute, _e('The student grade must not be higher than the maximum score!'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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
            'exam_type_id',
            'type',
            'group_id',
            'student_id',
            'edu_semestr_subject_id',
            'edu_plan_id',
            'subject_id',
            'edu_semestr_id',
            'faculty_id',
            'direction_id',
            'semestr_id',
            'course_id',
            'ball',
            'max_ball',
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
            'examType',
            'examControl',
            'examControlStudent',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getSubject()
    {
        return $this->hasOne(Subject ::className(), ['id' => 'subject_id']);
    }

    public function getExamControl()
    {
        return $this->hasOne(ExamControl ::className(), ['id' => 'exam_control_id']);
    }


    public function getExamControlStudent()
    {
        return $this->hasOne(ExamControlStudent ::className(), ['id' => 'exam_control_student_id']);
    }
    public function getExamType()
    {
        return $this->hasOne(ExamsType ::className(), ['id' => 'type']);
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
