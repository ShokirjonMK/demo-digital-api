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
            [['edu_plan_id', 'course_id', 'semestr_id', 'edu_year_id'], 'required'],
            [['edu_plan_id', 'course_id', 'optional_subject_count', 'required_subject_count', 'semestr_id', 'edu_year_id', 'is_checked', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['credit'], 'double'],
            [['one_credit_sum'], 'double'],
            [['all_credit_sum'], 'double'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
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
            'one_credit_sum',
            'all_credit_sum',
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
            return $this->eduYear->year . '-' . date("Y", strtotime("+1 year", strtotime($this->eduYear->year . "-01-01")));
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

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
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
        return $this->hasMany(EduSemestrSubject::className(), ['edu_semestr_id' => 'id']);
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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
