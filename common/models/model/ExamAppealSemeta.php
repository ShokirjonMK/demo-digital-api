<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "exam_question".
 *
 * @property int $id
 * @property int $exam_id
 * @property string $lang_id
 * @property float|null $teacher_access_id
 * @property string $count
 *
 * @property int|null $order
 * @property int|null $status
 * @property int|null $start
 * @property int|null $finish
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Exam $Exam
 * @property Languages $lang
 * @property TeacherAccess[] $teacherAccess
 */
class ExamAppealSemeta extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_NEW = 0;
    const STATUS_CONFIRMED = 1; // tasdiqlangan
    const STATUS_IN_CHECKING = 2;
    const STATUS_COMPLETED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_appeal_semeta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_id', 'lang_id', 'teacher_access_id',  'count'], 'required'],
            [
                [
                    'exam_id',
                    'teacher_access_id',
                    'lang_id',
                    'count',
                    'type',

                    'order',
                    'status',

                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
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
            'exam_id',
            'lang_id',
            'type',

            'teacher_access_id',
            'count',
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
            'exam_id',
            'lang_id',
            'teacher_access_id',
            'count',

            // 'type',
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
            'exam',
            'lang',
            'teacherAccess',

            'statusName',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Exam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    /**
     * Gets query for [[Lang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id']);
    }

    /**
     * Gets query for [[exam_question_type_id]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    public function getStatusName()
    {
        return   $this->statusList()[$this->status];
    }

    public static function createItems($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $examId = isset($post['exam_id']) ?  $post['exam_id'] : null;
        $data = [];
        $status = $post['status'] ?? null;
        $data['status'] = true;
        if (isset($examId)) {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                /** smetas */
                if ($exam->status_appeal == Exam::STATUS_APPEAL_DISTRIBUTED) {
                    $errors[] = [_e('Exam already distributed, you can not change!')];
                    $data['status'] = false;
                    $transaction->rollBack();
                    $data['errors'] = $errors;
                    return $data;
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if (isset($post['smetas'])) {
                    $post['smetas'] = str_replace("'", "", $post['smetas']);
                    if (!isJsonMK($post['smetas'])) {
                        $errors['smetas'] = [_e('Must be Json')];
                        $data['status'] = false;
                        $transaction->rollBack();
                        $data['errors'] = $errors;
                        return $data;
                    }

                    // $countOfExamStudent = $exam->examStudentCount;
                    $countOfExamStudent = $exam->getAppealCount();
                    $countOfSmetas = 0;
                    // foreach (ExamAppealSemeta::findAll(['exam_id' => $exam->id]) as $oldExamAppealSemetaOne) {
                    //     $oldExamAppealSemetaOne->is_deleted = 1;
                    //     $oldExamAppealSemetaOne->save();
                    // }

                    ExamAppealSemeta::deleteAll(['exam_id' => $exam->id]);

                    foreach (((array)json_decode($post['smetas'])) as  $teacherAccessId => $smetaAttribute) {
                        // [['exam_id', 'lang_id', 'teacher_access_id',  'count'], 'required'],

                        $subjectId = $exam->eduSemestrSubject->subject_id;
                        // dd($subjectId);
                        $hasTeacherAccess = TeacherAccess::findOne([
                            'subject_id' => $subjectId,
                            'language_id' => $smetaAttribute->lang_id,
                            'id' =>  $teacherAccessId
                        ]);
                        // dd($hasTeacherAccess);
                        if ($hasTeacherAccess) {

                            $oldExamSmeta = ExamAppealSemeta::findOne([
                                'exam_id' => $examId,
                                'lang_id' => $smetaAttribute->lang_id,
                                'teacher_access_id' => $teacherAccessId
                            ]);

                            if ($oldExamSmeta) {
                                $newExamSmeta = $oldExamSmeta;
                            } else {
                                $newExamSmeta = new ExamAppealSemeta();
                            }
                            if (isset($post['start'])) {
                                $newExamSmeta->start = strtotime($post['start']);
                            }
                            if (isset($post['finish'])) {
                                $newExamSmeta->finish = strtotime($post['finish']);
                            }
                            $newExamSmeta->exam_id = (int)$examId;
                            $newExamSmeta->lang_id = $smetaAttribute->lang_id;
                            $newExamSmeta->count = $smetaAttribute->count;
                            $newExamSmeta->teacher_access_id = $teacherAccessId;

                            $newExamSmeta->status = self::STATUS_NEW;


                            if (!($newExamSmeta->validate())) {
                                $errors[] = [$teacherAccessId => $newExamSmeta->errors];
                            }

                            $newExamSmeta->status = $status;
                            $newExamSmeta->save();
                            $data['data'][] = $newExamSmeta;
                        } else {
                            $errors[] = [$teacherAccessId  => _e(' Teacher Access Id is not vailed (' . $teacherAccessId . ')')];
                        }

                        $countOfSmetas += $smetaAttribute->count;
                        // ***
                    }

                    if ($countOfSmetas != $countOfExamStudent) {
                        $transaction->rollBack();
                        $errors['smetas'] = [_e('Incorrectly distributed')];
                        $data['status'] = false;
                        // $transaction->rollBack();
                        $data['errors'] = $errors;
                    }
                } else {
                    $errors['smetas'] = [_e('Required')];
                }
            } else {
                $errors['exam'] = [_e('Exam not found')];
            }
        } else {
            $errors['exam_id'] = [_e('Exam ID is required')];
        }

        if (count($errors) == 0) {

            $data['status'] = true;
            $transaction->commit();
            return $data;
        } else {
            $data['status'] = false;
            $transaction->rollBack();
            $data['errors'] = $errors;
            return $data;
            return simplify_errors($errors);
        }
        /** smetas */
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

    public static function distribution($exam)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $exam->status = Exam::STATUS_DISTRIBUTED;
        if ($exam->save()) {

            $examSmetas = ExamAppealSemeta::findAll(['exam_id' => $exam->id]);

            foreach ($examSmetas as $examSmetaOne) {
                $examStudent = ExamStudent::find()
                    ->where([
                        'exam_id' => $exam->id,
                        'teacher_access_id' => null,
                        'status' => ExamStudent::STATUS_TAKED,
                    ])
                    ->orderBy(new Expression('rand()'))
                    ->limit($examSmetaOne->count)
                    ->all();

                $examSmetaOne->status = self::STATUS_IN_CHECKING;
                if (!$examSmetaOne->save()) {
                    $errors[] = _('There is an error occurred while distributed');
                }

                foreach ($examStudent as $examStudentOne) {
                    $examStudentOne->teacher_access_id = $examSmetaOne->teacher_access_id;
                    $examStudentOne->status = ExamStudent::STATUS_IN_CHECKING;

                    if (!$examStudentOne->save()) {
                        $errors[] = _('There is an error occurred while distributed');
                    }
                }
            }
        } else {
            $errors[] = _('There is an error occurred on exam');
        }
        if (count($errors) == 0) {
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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public static function statusList()
    {
        return [
            self::STATUS_NEW => _e('STATUS_NEW'),
            self::STATUS_CONFIRMED => _e('STATUS_CONFIRMED'),
            self::STATUS_IN_CHECKING => _e('STATUS_IN_CHECKING'),
            self::STATUS_COMPLETED => _e('STATUS_COMPLETED'),
        ];
    }
}
