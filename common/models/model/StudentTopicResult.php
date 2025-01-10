<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Predis\Configuration\Options;
use common\models\model\Option;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject_topic_id
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class StudentTopicResult extends \yii\db\ActiveRecord
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
        return 'student_topic_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'subject_id',
                    'subject_topic_id',
                    'user_id',
                    'student_id',

                    'start_time',
                    'end_time',

                ] , 'required'
            ],
            [
                [
                    'subject_id',
                    'subject_topic_id',
                    'user_id',
                    'student_id',

                    'question_count',
                    'ball',
                    'start_time',
                    'end_time',

                    'group_id',
                    'edu_semestr_id',
                    'edu_year_id',
                    'course_id',
                ], 'integer'
            ],

            [['percent'],'safe'],

            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['subject_topic_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
            'user_id',
            'subject_id',
            'subject_topic_id',
            'question_count',
            'start_time',
            'end_time',
            'ball',
            'percent',
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
            'now',
            'studentTopicTestAnswer',
            'subjectTopic',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getStudentTopicTestAnswer()
    {
        return $this->hasOne(StudentTopicTestAnswer::className(), ['id' => 'student_topic_result_id']);
    }

    public function getSubjectTopic()
    {
        return $this->hasOne(SubjectTopic::className(), ['id' => 'subject_topic_id']);
    }

    public function getNow() {
        return time();
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
