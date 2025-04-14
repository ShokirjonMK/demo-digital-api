<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "direction".
 * @property int $id
 * @property int $subject_id
 * @property int $survey_question_id
 * @property int $ball
 * @property int $exam_id
 * @property int $edu_semestr_subject_id
 * @property int $student_id
 * @property int $user_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class SurveyAnswer16 extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }



    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'survey_answer_16';
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
                    'survey_question_id',
                    'ball',
                    'exam_id',
                ], 'required'
            ],
            [
                [
                    'subject_id',
                    'survey_question_id',
                    'ball',
                    'exam_id',
                    'edu_semestr_subject_id',
                    'student_id',
                    'user_id',
                ], 'integer'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['survey_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyQuestion::className(), 'targetAttribute' => ['survey_question_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            [['survey_question_id'], 'unique', 'targetAttribute' => ['survey_question_id', 'exam_id', 'user_id']],

            // a1 needs to be unique
            // ['a1', 'unique'],
            // a1 needs to be unique, but column a2 will be used to check the uniqueness of the a1 value
            // ['a1', 'unique', 'targetAttribute' => 'a2'],
            // a1 and a2 need to be unique together, and they both will receive error message
            // a1 and a2 need to be unique together, only a1 will receive error message
            // ['a1', 'unique', 'targetAttribute' => ['a1', 'a2']],
            // a1 needs to be unique by checking the uniqueness of both a2 and a3 (using a1 value)
            // ['a1', 'unique', 'targetAttribute' => ['a2', 'a1' => 'a3']],
            // ['a1', 'unique', 'targetAttribute' => ['a2', 'a1','a3', 'a4']],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'subject_id' => _e('subject_id'),
            'survey_question_id' => _e('survey_question_id'),
            'ball' => _e('ball'),
            'exam_id' => _e('exam_id'),
            'edu_semestr_subject_id' => _e('edu_semestr_subject_id'),
            'student_id' => _e('student_id'),
            'user_id' => _e('user_id'),

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

            'subject_id',
            'survey_question_id',
            'ball',
            'exam_id',
            'student_id',
            // 'edu_semestr_subject_id',
            'user_id',

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
            'surveyQuestion',
            'exam',
            'eduSemestrSubject',
            'student',
            'user',
            'description',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' =>  'subject_id']);
    }

    public function getSurveyQuestion()
    {
        return $this->hasOne(SurveyQuestion::className(), ['id' =>  'survey_question_id']);
    }

    public function getExam()
    {
        return $this->hasOne(exam::className(), ['id' =>  'exam_id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' =>  'edu_semestr_subject_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' =>  'student_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' =>  'user_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->edu_semestr_subject_id = $model->exam->edu_semestr_subject_id;
        $model->subject_id = $model->exam->edu_semestr_subject_id->subject_id;

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItems($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $examId = $post['exam_id'] ?? null;
        $data = ['status' => true,];

        if (!isset($examId)) {
            $errors['exam_id'] = [_e('Exam ID is required')];
        } else {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                if (isset($post['answers'])) {
                    $post['answers'] = str_replace("'", "", $post['answers']);
                    if (!isJsonMK($post['answers'])) {
                        $errors['answers'] = [_e('Must be Json')];
                    } else {
                        foreach (((array)json_decode($post['answers'])) as  $survey_question_id => $ball) {

                            $newOrUpdateModel = self::findOne([
                                'survey_question_id' => $survey_question_id,
                                'exam_id' => $examId,
                            ]);

                            if (!$newOrUpdateModel) {
                                $newOrUpdateModel = new self();
                            }

                            $newOrUpdateModel->user_id = $post['user_id'];
                            $newOrUpdateModel->student_id = $post['student_id'];
                            $newOrUpdateModel->exam_id = $examId;
                            $newOrUpdateModel->edu_semestr_subject_id = $exam->edu_semestr_subject_id;
                            $newOrUpdateModel->subject_id = $exam->eduSemestrSubject->subject_id;

                            $newOrUpdateModel->survey_question_id = $survey_question_id;
                            $newOrUpdateModel->ball = $ball;
                            // dd($newOrUpdateModel);
                            if (!($newOrUpdateModel->validate()) || (!$newOrUpdateModel->save())) {
                                $errors[] = [$survey_question_id => $newOrUpdateModel->errors];
                            }
                        }
                    }
                } else {
                    $errors['answers'] = [_e('Required')];
                }
            } else {
                $errors['exam'] = [_e('Exam not found')];
            }
        }

        if (count($errors) == 0) {

            $transaction->commit();
            return $data;
        } else {
            $data['status'] = false;
            $transaction->rollBack();
            $data['errors'] = $errors;
            return $data;
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->getErrorSummary(true);
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
