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
class StudentTopicTestAnswer extends \yii\db\ActiveRecord
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
        return 'student_topic_test_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'student_topic_result_id',
                    'subject_topic_id',
                    'user_id',
                    'student_id',

                    'test_id',
                    'answer_option_id',
                    'is_correct',
                    'options',

                ] , 'required'
            ],
            [
                [
                    'student_topic_result_id',
                    'subject_topic_id',
                    'user_id',
                    'student_id',
                    'option_id',

                    'test_id',
                    'answer_option_id',
                    'is_correct',
                ],'integer'
            ],

            [['options'],'string', 'max'=> 255],

            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['subject_topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['subject_topic_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['student_topic_result_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentTopicResult::className(), 'targetAttribute' => ['student_topic_result_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
            [['answer_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => Option::className(), 'targetAttribute' => ['answer_option_id' => 'id']],
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
            'student_topic_result_id',
            'subject_topic_id',
            'user_id',
            'test_id',
            'options',
            'option_id',
            'status',
            'questions',
            'answers',
//            'created_at',
//            'updated_at',
//            'created_by',
//            'updated_by',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'questions',
            'options',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getAnswers()
    {

        $options =  Option::find()
            ->select(['id','test_id','text','file'])
            ->where([
                'test_id' => $this->test_id
            ])
            ->asArray()
            ->all();
        return $options;
    }

    public function getQuestions()
    {
        $test = Test::find()
            ->select(['id','topic_id','file','text'])
            ->where([
                'id' => $this->test_id
            ])->asArray();
        return $test;
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
