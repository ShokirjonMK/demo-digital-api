<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%student_gpa_old}}".
 *
 * @property int $id
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $direction
 * @property string|null $course
 * @property string|null $group
 * @property string|null $semestr
 * @property string|null $edu_lang
 * @property string|null $subject_name
 * @property string|null $username_distant
 * @property string|null $srs_id
 * @property float|null $all_ball
 * @property string|null $alphabet
 * @property string|null $mark
 * @property int|null $student_id
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class StudentGpaOld extends \yii\db\ActiveRecord
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
        return '{{%student_gpa_old}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['all_ball'], 'double'],
            [['student_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['last_name', 'first_name', 'middle_name', 'direction', 'course', 'group', 'semestr', 'edu_lang', 'subject_name', 'username_distant', 'srs_id', 'alphabet', 'mark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('app', 'ID'),
            'last_name' => _e('app', 'Last Name'),
            'first_name' => _e('app', 'First Name'),
            'middle_name' => _e('app', 'Middle Name'),
            'direction' => _e('app', 'Direction'),
            'course' => _e('app', 'Course'),
            'group' => _e('app', 'Group'),
            'semestr' => _e('app', 'Semestr'),
            'edu_lang' => _e('app', 'Edu Lang'),
            'subject_name' => _e('app', 'Subject Name'),
            'username_distant' => _e('app', 'Username Distant'),
            'srs_id' => _e('app', 'Srs ID'),
            'all_ball' => _e('app', 'All Ball'),
            'alphabet' => _e('app', 'Alphabet'),
            'mark' => _e('app', 'Mark'),
            'student_id' => _e('app', 'Student ID'),
            'status' => _e('app', 'Status'),
            'order' => _e('app', 'Order'),
            'created_at' => _e('app', 'Created At'),
            'updated_at' => _e('app', 'Updated At'),
            'created_by' => _e('app', 'Created By'),
            'updated_by' => _e('app', 'Updated By'),
            'is_deleted' => _e('app', 'Is Deleted'),
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'last_name',
            'first_name',
            'middle_name',
            'direction',
            'course',
            'group',
            'semestr',
            'edu_lang',
            'subject_name',
            'username_distant',
            'srs_id',
            'all_ball',
            'alphabet',
            'mark',
            'student_id',

            'status',
            'order',
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
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * StudentGpaOld createItem <$model, $post>
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

    /**
     * StudentGpaOld updateItem <$model, $post>
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
