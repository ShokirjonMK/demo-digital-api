<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%subject_evaluation}}".
 *
 * @property int $id
 * @property int $subject_id
 * @property string|null $control_submission
 * @property string|null $control_assessment
 * @property string|null $final_submission
 * @property string|null $final_assessment
 * @property string|null $control_submission_ru
 * @property string|null $control_assessment_ru
 * @property string|null $final_submission_ru
 * @property string|null $final_assessment_ru
 * @property string|null $control_submission_en
 * @property string|null $control_assessment_en
 * @property string|null $final_submission_en
 * @property string|null $final_assessment_en
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property Subject $subject
 */
class SubjectEvaluation extends \yii\db\ActiveRecord
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
        return 'subject_evaluation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_id'], 'required'],
            [['subject_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['control_submission', 'control_assessment', 'final_submission', 'final_assessment', 'control_submission_ru', 'control_assessment_ru', 'final_submission_ru', 'final_assessment_ru', 'control_submission_en', 'control_assessment_en', 'final_submission_en', 'final_assessment_en'], 'string'],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [
                ['subject_id'],
                'unique',
                'targetAttribute' => ['subject_id', 'is_deleted', 'archived'],
                'message' => _e('Subject ID must be unique'),
                'filter' => function ($query) {
                    $query->andWhere(['is_deleted' => 0, 'archived' => 0]);
                },
            ],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'subject_id' => _e('Subject ID'),
            'control_submission' => _e('Control Submission'),
            'control_assessment' => _e('Control Assessment'),
            'final_submission' => _e('Final Submission'),
            'final_assessment' => _e('Final Assessment'),
            'control_submission_ru' => _e('Control Submission Ru'),
            'control_assessment_ru' => _e('Control Assessment Ru'),
            'final_submission_ru' => _e('Final Submission Ru'),
            'final_assessment_ru' => _e('Final Assessment Ru'),
            'control_submission_en' => _e('Control Submission En'),
            'control_assessment_en' => _e('Control Assessment En'),
            'final_submission_en' => _e('Final Submission En'),
            'final_assessment_en' => _e('Final Assessment En'),
            'order' => _e('Order'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'archived' => _e('Archived'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'subject_id',
            'control_submission',
            'control_assessment',
            'final_submission',
            'final_assessment',
            'control_submission_ru',
            'control_assessment_ru',
            'final_submission_ru',
            'final_assessment_ru',
            'control_submission_en',
            'control_assessment_en',
            'final_submission_en',
            'final_assessment_en',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by'
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'subject',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);;
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
}
