<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr".
 *
 * @property int $id
 * @property int $edu_plan_id
 * @property int $course_id
 * @property int $semestr_id
 * @property int $edu_year_id
 * @property int $edu_type_id
 * @property int $edu_form_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $is_checked
 * @property int|null $type
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property EduPlan $eduPlan
 * @property EduYear $eduYear
 * @property Semestr $semestr
 * @property EduSemestrSubject[] $eduSemestrSubjects
 * @property EduSemestrExamsType[] $eduSemestrExamsTypes
 */
class EduSemestr extends \yii\db\ActiveRecord
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
        return 'edu_semestr';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                [
                    'edu_plan_id',
                    'type',
                    'course_id',
//                    'semestr_id',
                    'edu_year_id',
                    'edu_type_id',
                    'edu_form_id',
                    'faculty_id',
                    'direction_id',
                ],
                'required'],
            [
                ['edu_plan_id',
                    'course_id',
                    'optional_subject_count',
                    'required_subject_count',
                    'semestr_id', 'edu_year_id',
                    'is_checked',
                    'edu_type_id',
                    'edu_form_id',
                    'faculty_id',
                    'direction_id',
                    'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted',
                ], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['credit'], 'double'],
            [
                ['start_date'],
                'compare',
                'compareValue' => 'end_date',
                'operator' => '<',
                'message' => _e('The end time must be greater than the start time.')
            ],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_plan_id' => 'Edu Plan ID',
            'course_id' => 'Course ID',
            'semestr_id' => 'Semestr ID',
            'edu_year_id' => 'Edu Year ID',
            'start_date' => 'Start Date',
            'credit' => 'Credit',
            'end_date' => 'End Date',
            'is_checked' => 'Is Checked',
            'type' => 'Type',
            'order' => _e('Order'),
            'optional_subject_count' => 'Optional Subject Count',
            'required_subject_count' => 'Required Subject Count',
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
            'name' => function ($model) {
                return $model->generateName ?? '';
            },
            'edu_plan_id',
            'course_id',
            'optional_subject_count',
            'required_subject_count',
            'semestr_id',
            'credit',
            'edu_year_id',
            'start_date',
            'end_date',
            'type',
            'is_checked',
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
            'weeks',
            'course',
            'eduPlan',
            'eduYear',
            'semestr',
            'eduSemestrSubjects',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getGenerateName()
    {
        if (isset($this->eduYear)) {
            if (isset($this->eduYear->translate)) {
                return $this->eduYear->translate->name . '-' . $this->course->id . '-' . $this->semestr->id;
            }
            return $this->eduYear->start_year . '-' . $this->eduYear->end_year . '-' .$this->eduYear->type;
        }
        return ":) " . $this->course->id . '-' . $this->semestr->id;
    }

    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    // Semestrni hafta kunlarini boshlanish i tugash sanalarini hisoblaydi.
    public function getWeeks()
    {
        $startDate = new \DateTime($this->start_date);
        $start = $startDate->format('Y-m-d');
        $endStartDate = $startDate->modify('next Sunday');
        $end = $endStartDate->format('Y-m-d');
        $endDate = new \DateTime($this->end_date);
        $dates = [];
        $dates[] = [
            'week' => 1,
            'start_date' => $start,
            'end_date' => $end,
        ];
        $i = 2;
        while ($endStartDate <= $endDate) {
            $endStartDate->modify('+1 day');
            $start = $endStartDate->format('Y-m-d');

            $endStartDate->modify('next Sunday');
            $end = $endStartDate->format('Y-m-d');
            if ($end >= $endDate->format('Y-m-d')) {
                $end = $endDate->format('Y-m-d');
            }

            $status = 0;
            if ($start <= date("Y-m-d") && $end >= date("Y-m-d")) {
                $status = 1;
            }

            $dates[] = [
                'week' => $i,
                'start_date' => $start,
                'end_date' => $end,
                'status' => $status
            ];
            $i++;
        }
        return $dates;
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
    }
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[EduSemestrSubjects]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduSemestrSubjects()
    {
        return $this->hasMany(EduSemestrSubject::className(), ['edu_semestr_id' => 'id'])->where(['is_deleted' => 0, 'status' => 1]);
    }

    /**
     *  Model createItem <$model, $post>
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            if ($model->status == 1) {
                $allEduSemesters = EduSemestr::find()->where(['edu_plan_id' => $model->edu_plan_id])->andWhere(['not in', 'id', $model->id])->all();
                if (isset($allEduSemesters)) {
                    foreach ($allEduSemesters as $EduSemester) {
                        $EduSemester->status = 0;
                        $EduSemester->save();
                    }
                }
            }
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
}
