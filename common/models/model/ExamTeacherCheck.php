<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "exam_teacher_check".
 *
 * @property int $id
 * @property int|null $teacher_access_id
 * @property int $student_id
 * @property int $exam_id
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class ExamTeacherCheck extends \yii\db\ActiveRecord
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
        return 'exam_teacher_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_access_id', 'student_id', 'exam_id', 'attempt', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['student_id', 'exam_id'], 'required'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_access_id' => 'Teacher Access ID',
            'student_id' => 'Student ID',
            'exam_id' => 'Exam ID',
            'attempt' => 'Attempt',
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
        $fields = [
            'id',
            'teacher_access_id',
            'student_id',
            'exam_id',
            'attempt',

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
        $extraFields = [
            'teacherAccess',
            'student',
            'exam',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[TeacherAccess]].
     * teacher_access_id
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['teacher_access_id' => 'id']);
    }

    /**
     * Gets query for [[Student]].
     * student_id
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[Exam]].
     * exam_id
     * @return \yii\db\ActiveQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['exam_id' => 'id']);
    }

    public static function randomStudent($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post["exam_id"])) {
            $exam_id = $post["exam_id"];

            if (isset($exam_id)) {
                $exam = Exam::findOne($exam_id);
                if (isset($exam)) {

                    $now_second = time();
                    if (strtotime($exam->finish) < $now_second) {

                        $examSemeta = ExamSemeta::findAll(['exam_id' => $exam->id]);

                        if (isset($examSemeta)) {
                            foreach ($examSemeta as $examSemetaOne) {
                                if ($examSemetaOne->status == ExamSemeta::STATUS_NEW) {
                                    $examStudent = ExamStudent::find()
                                        ->where([
                                            'exam_id' => $exam_id,
                                            'lang_id' => $examSemetaOne->lang_id,
                                        ])
                                        ->andWhere(['teacher_access_id' => null])
                                        ->orderBy(new Expression('rand()'))
                                        ->limit($examSemetaOne->count)
                                        ->all();

                                    foreach ($examStudent as $examStudentOne) {
                                        $examStudentOne->teacher_access_id = $examSemetaOne->teacher_access_id;
                                        $examStudentOne->save(false);
                                    }
                                    $examSemetaOne->status = ExamSemeta::STATUS_IN_CHECKING;
                                    $examSemetaOne->save(false);
                                }
                            }
                        }
                        $data = ExamStudent::findAll(['exam_id' => $exam_id]);
                        return $data;
                    } else {
                        $errors[] = _e("This exam`s time not expired");
                    }
                } else {
                    $errors[] = _e("This exam not found");
                }
            } else {
                $errors[] = _e("This exam not found");
            }
        } else {
            $errors[] = _e("This exam not found");
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
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

    public static function getUniqueStudentId($studentIds)
    {

        $ids = [];
        foreach ($studentIds as $studentId) {
            if (!in_array($studentId['student_id'], $ids)) {
                $ids[] = $studentId['student_id'];
            }
        }
        return $ids;
    }
}
