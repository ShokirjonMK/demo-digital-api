<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Predis\Configuration\Options;
use common\models\model\Option;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
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
class ExamTestStudentAnswer extends \yii\db\ActiveRecord
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
        return 'exam_test_student_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'exam_control_student_id',
                    'exam_control_id',
                    'subject_id',
                    'user_id',
                    'student_id',

                    'exam_test_id',
                    'answer_option_id',
                    'options',
//                    'exam_test_option_id',
                ] , 'required'
            ],
            [
                [
                    'exam_control_student_id',
                    'subject_id',
                    'user_id',
                    'student_id',
                    'exam_test_option_id',
                    'exam_test_id',
                    'answer_option_id',
                    'is_correct',
                ],'integer'
            ],

            [['options'],'string', 'max'=> 255],

            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['exam_control_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamControlStudent::className(), 'targetAttribute' => ['exam_control_student_id' => 'id']],
            [['exam_control_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamControl::className(), 'targetAttribute' => ['exam_control_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['exam_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['exam_test_id' => 'id']],
            [['answer_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => Option::className(), 'targetAttribute' => ['answer_option_id' => 'id']],
//            [['exam_test_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamTestOption::className(), 'targetAttribute' => ['exam_test_option_id' => 'id']],
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
        if (isRole('admin')) {
            $fields =  [
                'id',
                'exam_control_student_id',
                'exam_test_option_id',
                'subject_id',
                'user_id',
                'exam_test_id',
                'options',
                'status'
            ];
        } else {
            $fields =  [
                'id',
                'exam_control_student_id',
                'exam_test_option_id',
                'user_id',
                'exam_test_id',
                'options',
                'status',
//                'isCorrect',
            ];
        }
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'test',
            'isCorrect',
            'examControl',
            'examControlStudent',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }

    public function getExamControl()
    {
        return $this->hasOne(ExamControl::className(), ['id' => 'exam_control_id']);
    }

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'exam_test_id']);
    }

    public function getIsCorrect()
    {
        if ($this->examControlStudent->user_status > 0 && $this->examControlStudent->examControl->finish_time < time()) {
            return $this->is_correct;
        }
        return 0;
    }

    public function getExamControlStudent()
    {
        return $this->hasOne(ExamControlStudent::className(), ['id' => 'exam_control_student_id']);
    }

    public static function designation($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (isset($post['answer_id'])) {
            $examControlStudentAnswer = ExamTestStudentAnswer::findOne([
                'id' => $post['answer_id'],
                'user_id' => current_user_id(),
            ]);
            if (!isset($examControlStudentAnswer)) {
                $errors[] = _e("You have no test questions.");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            $examControlStudent = ExamControlStudent::findOne([
                'id' => $examControlStudentAnswer->exam_control_student_id
            ]);
            if ($examControlStudent->examControl->finish_time < time()) {
                $errors[] = _e("Exam control time is over.");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            if ($examControlStudent->examControl->start_time > time()) {
                $errors[] = _e("Exam control time has not started.");
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            if (!($examControlStudent->start_time < time() && $examControlStudent->finish_time > time())) {
                $errors[] = _e("The allotted time has expired.");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            if (isset($post['option_id'])) {
                if ($examControlStudentAnswer->answer_option_id == $post['option_id']) {
                    $examControlStudentAnswer->is_correct = 1;
                }
                $examControlStudentAnswer->exam_test_option_id = $post['option_id'];
                $examControlStudentAnswer->save(false);
            } else {
                $errors[] = _e("Option Id not found.");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function answerOption($id) {
        $option = Option::findOne([
            'test_id' => $id,
            'is_correct' => 1,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (isset($option)) {
            return $option->id;
        }
    }

    public static function optionsArray($id) {
        $options = Option::find()
            ->select('id')
            ->where([
                'test_id' => $id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->orderBy(new Expression('rand()'))
            ->asArray()->all();
        return json_encode($options);
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
