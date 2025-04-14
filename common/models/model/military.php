<?php

namespace common\models\model;

use yii\behaviors\TimestampBehavior;
use api\resources\ResourceTrait;
use common\models\User;
use Yii;


/**
 * This is the model class for table "military ".
 *
 * @property int $id
 * @property string $joy
 * @property string $chas_raqami
 * @property string $year
 * @property string $seria_raqami
 * @property int $student_id
 * @property int $user_id
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Military extends \yii\db\ActiveRecord
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
        return 'military';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['user_id', 'student_id'], 'required'],
            [['student_id', 'user_id',], 'integer'],
            [['joy','chas_raqami','year','seria_raqami'], 'string', 'max' => 255],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => _e('Student Id'),
            'user_id' => _e('User Id'),
            'joy' => _e('Joy'),
            'chas_raqami' => _e('Chas Raqami'),
            'year' => _e('Year'),
            'seria_raqami' => _e('Seria Raqami'),
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
            'student_id',
            'user_id',
            'year',
            'seria_raqami',
            'chas_raqami',
            'joy',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
        ];

        return $fields;
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

    #region rel
    public function getUsers()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    public function getStudents()
    {
        return $this->hasOne(\common\models\model\Student::class, ['id' => 'student_id']);
    }
    #endregion


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

