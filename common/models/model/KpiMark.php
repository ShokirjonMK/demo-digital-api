<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "military ".
 *
 * @property int $id
 * @property double $ball
 * @property int $kpi_category_id
 * @property int $user_id
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class KpiMark extends \yii\db\ActiveRecord
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
        return 'kpi_mark';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['user_id', 'kpi_category_id', 'ball'], 'required'],
            [['ball'], 'double',],
            [['ball_in'], 'double',],
            ['ball', 'validateBallMK'],
            [['user_id', 'archived', 'type', 'edu_year_id', 'kpi_category_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['kpi_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => KpiCategory::class, 'targetAttribute' => ['kpi_category_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::class, 'targetAttribute' => ['edu_year_id' => 'id']],


            [['kpi_category_id', 'edu_year_id', 'user_id', 'kpi_category_id', 'is_deleted', 'archived'], 'unique', 'targetAttribute' => ['edu_year_id', 'user_id', 'kpi_category_id', 'is_deleted', 'archived'],],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User Id',
            'kpi_category_id' => 'Kpi Category Id',
            'type' => 'type',
            'edu_year_id' => 'edu_year_id',
            'ball' => 'Ball',
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'user_id',
            'type',
            'edu_year_id',
            'kpi_category_id',
            'ball',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'user',
            'kpiCategory',
            'subjectCategory',
            'ballIn',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Validates the ball attribute.
     */
    public function validateBallMK($attribute, $params)
    {
        // Assuming you have a relation setup to access the related model
        $relatedModel = $this->kpiCategory; // Replace 'relatedModel' with the correct relation name

        if (!$relatedModel) {
            $this->addError($attribute, 'Related model not found.');
            return;
        }

        if ($this->$attribute < 0 || $this->$attribute > $relatedModel->max_ball) {
            $this->addError($attribute, "Ball must be between 0 and {$relatedModel->max_ball}.");
        }
    }


    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getBallIn()
    {
        return $this->ball_in;
    }

    public function getKpiCategory()
    {
        return $this->hasOne(KpiCategory::class, ['id' => 'kpi_category_id']);
    }

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::class, ['id' => 'edu_year_id']);
    }


    /**
     * Creates an item for the given model.
     * 
     * @param  object  $model The primary model for the record.
     * @param  array|null  $post Post data, if any.
     * @return mixed Returns true on successful creation, otherwise returns an array of error messages.
     */
    public static function createItem($model, $post = null)
    {
        $transaction = Yii::$app->db->beginTransaction(); // Start the transaction
        $errors = [];

        // Setting the education year based on current date's year.
        $eduYear = EduYear::findOne(['year' => date("Y")]);
        if (!$eduYear) {
            $errors[] = _e('Education year not found.');
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->edu_year_id = $eduYear->id;

        // Validate the model attributes.
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Check the ball value against the max_ball of its category.
        if ($model->ball > $model->kpiCategory->max_ball) {
            $errors[] = _e('Ushbu tur uchun max ball ') . $model->kpiCategory->max_ball;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Check user access to the category.
        $userIds = $model->kpiCategory->user_ids;
        if (!(is_array($userIds) && in_array(current_user_id(), $userIds))) {
            $errors[] = _e('You have no access for this category');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Save the model.
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    // public static function createItem($model, $post = null)
    // {
    //     $transaction = Yii::$app->db->beginTransaction();
    //     $errors = [];

    //     $model->edu_year_id = EduYear::findOne(['year' => date("Y")])->id;

    //     if (!($model->validate())) {
    //         $errors[] = $model->errors;

    //         $transaction->rollBack();
    //         return simplify_errors($errors);
    //     }

    //     if ($model->ball > $model->kpiCategory->max_ball) {
    //         $errors[] = _e('Ushbu tur uchun max ball ') . $model->kpiCategory->max_ball;
    //         $transaction->rollBack();
    //         return simplify_errors($errors);
    //     }

    //     $userIds = $model->kpiCategory->user_ids;
    //     if (!(is_array($userIds) && in_array(current_user_id(), $userIds))) {
    //         $errors[] = _e('You have no access for this category');
    //         $transaction->rollBack();
    //         return simplify_errors($errors);
    //     }


    //     // $userIds = json_decode(trim($model->kpiCategory->user_ids, "'"), true);
    //     // if (!(is_array($userIds) && in_array(current_user_id(), $userIds))) {
    //     //     $errors[] = _e('You have no access for this category');
    //     //     $transaction->rollBack();
    //     //     return simplify_errors($errors);
    //     // }

    //     // if($model->kpiCategory->user_ids)

    //     if ($model->save()) {
    //         $transaction->commit();
    //         return true;
    //     } else {
    //         $transaction->rollBack();
    //         return simplify_errors($errors);
    //     }
    // }

    public static function createItemStat($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->edu_year_id = EduYear::findOne(['year' => date("Y")])->id;

        if (!($model->validate())) {
            $errors[] = $model->errors;

            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->ball > $model->kpiCategory->max_ball) {
            $errors[] = _e('Ushbu tur uchun max ball ' . $model->kpiCategory->max_ball);
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
        }

        if ($model->ball > $model->kpiCategory->max_ball) {
            $errors[] = _e('Ushbu tur uchun max ball ' . $model->kpiCategory->max_ball);
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
            if (!isset($this->created_by))
                $this->created_by = current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
