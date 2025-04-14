<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%poll_user}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $poll_id
 * @property int $poll_question_id
 * @property int|null $poll_question_option_id
 * @property string|null $poll_question_option_answer
 * @property string|null $answer
 * @property int|null $student_id
 * @property int|null $faculty_id
 * @property int|null $edu_form_id
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property EduForm $eduForm
 * @property Faculty $faculty
 * @property Poll $poll
 * @property PollQuestion $pollQuestion
 * @property PollQuestionOption $pollQuestionOption
 * @property Student $student
 * @property Users $user
 */
class PollUser extends \yii\db\ActiveRecord
{
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
        return '{{%poll_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['poll_question_id'], 'required'],
            [['user_id', 'poll_id', 'poll_question_id', 'poll_question_option_id', 'student_id', 'faculty_id', 'edu_form_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['poll_question_option_answer', 'answer'], 'string'],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['poll_id'], 'exist', 'skipOnError' => true, 'targetClass' => Poll::className(), 'targetAttribute' => ['poll_id' => 'id']],
            [['poll_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => PollQuestion::className(), 'targetAttribute' => ['poll_question_id' => 'id']],
            [['poll_question_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => PollQuestionOption::className(), 'targetAttribute' => ['poll_question_option_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            // [['poll_question_option_id', 'poll_question_option_answer'], 'requiredAtLeastOne'],
            [['poll_question_option_id', 'poll_question_option_answer'], 'required', 'when' => function ($model) {
                return empty($model->poll_question_option_id) && empty($model->poll_question_option_answer);
            }, 'whenClient' => "function (attribute, value) {
                        return !$('#your-form-id :input[name=\"YourModel[poll_question_option_id]\"]').val() && !$('#your-form-id :input[name=\"YourModel[poll_question_option_answer]\"]').val();
                    }", 'message' => 'Either "Poll Question Option ID" or "Poll Question Option Answer" is required.'],

            ['poll_question_id', 'unique', 'targetAttribute' => ['poll_question_id', 'user_id']]

        ];
    }

    // public function requiredAtLeastOne($attribute, $params)
    // {
    //     if (is_null($this->poll_question_option_id) && is_null($this->poll_question_option_answer)) {
    //         $this->addError($attribute, 'Either "poll_question_option_id" or "poll_question_option_answer" must be filled.');
    //     }
    // }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('User ID'),
            'poll_id' => _e('Poll ID'),
            'poll_question_id' => _e('Poll Question ID'),
            'poll_question_option_id' => _e('Poll Question Option ID'),
            'poll_question_option_answer' => _e('Poll Question Option Answer'),
            'answer' => _e('Answer'),
            'student_id' => _e('Student ID'),
            'faculty_id' => _e('Faculty ID'),
            'edu_form_id' => _e('Edu Form ID'),
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'poll_id',
            'poll_question_id',
            'poll_question_option_id',
            'poll_question_option_answer',
            'answer',
            'student_id',
            'faculty_id',
            'edu_form_id',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'eduForm',
            'faculty',
            'poll',
            'pollQuestion',
            'pollQuestionOption',
            'student',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[EduForm]].
     *
     * @return \yii\db\ActiveQuery|EduFormQuery
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
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
     * Gets query for [[Poll]].
     *
     * @return \yii\db\ActiveQuery|PollQuery
     */
    public function getPoll()
    {
        return $this->hasOne(Poll::className(), ['id' => 'poll_id']);
    }

    /**
     * Gets query for [[PollQuestion]].
     *
     * @return \yii\db\ActiveQuery|PollQuestion
     */
    public function getPollQuestion()
    {
        return $this->hasOne(PollQuestion::className(), ['id' => 'poll_question_id']);
    }

    /**
     * Gets query for [[PollQuestionOption]].
     *
     * @return \yii\db\ActiveQuery|PollQuestionOptionQuery
     */
    public function getPollQuestionOption()
    {
        return $this->hasOne(PollQuestionOption::className(), ['id' => 'poll_question_option_id']);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * PollUser createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->user_id = current_user_id();
        $model->poll_id = $model->pollQuestion->poll_id;

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
     * PollUser updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
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
