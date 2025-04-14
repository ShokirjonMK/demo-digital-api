<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "{{%student_club}}".
 *
 * @property int $id
 * @property int|null $club_category_id
 * @property int $club_time_id
 * @property int $club_id
 * @property int $student_id
 * @property int|null $faculty_id
 * @property int|null $edu_plan_id
 * @property int|null $gender
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Club $club
 * @property ClubCategory $clubCategory
 * @property ClubTime $clubTime
 * @property EduPlan $eduPlan
 * @property Faculty $faculty
 * @property Student $student
 */
class StudentClub extends \yii\db\ActiveRecord
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
        return 'student_club';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['club_time_id'], 'required'],
            [['club_category_id', 'is_leader', 'club_time_id', 'club_id', 'student_id', 'faculty_id', 'edu_plan_id', 'gender', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['is_leader'], 'default', 'value' => 0],
            [['description'], 'string'],

            [['club_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClubCategory::className(), 'targetAttribute' => ['club_category_id' => 'id']],
            [['club_id'], 'exist', 'skipOnError' => true, 'targetClass' => Club::className(), 'targetAttribute' => ['club_id' => 'id']],
            [['club_time_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClubTime::className(), 'targetAttribute' => ['club_time_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],

            [['student_id'], 'unique', 'targetAttribute' => ['club_id', 'student_id'], 'message' => "You are already selected thi club!"],
            // [['is_leader'], 'unique', 'targetAttribute' => ['club_time_id', 'is_leader'], 'message' => "Only one person can be leader!"],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'club_category_id',
            'club_time_id',
            'club_id',
            'student_id',
            'faculty_id',
            'edu_plan_id',
            'gender',
            'is_leader',
            'description',

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

            'club_category_id',
            'club_time_id',
            'club_id',
            'student_id',
            'is_leader',
            // 'faculty_id',
            // 'edu_plan_id',
            // 'gender',
            'description',

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
            'club',
            'clubCategory',
            'clubTime',
            'eduPlan',
            'faculty',
            'student',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Club]].
     *
     * @return \yii\db\ActiveQuery|ClubQuery
     */
    public function getClub()
    {
        return $this->hasOne(Club::className(), ['id' => 'club_id']);
    }

    /**
     * Gets query for [[ClubCategory]].
     *
     * @return \yii\db\ActiveQuery|ClubCategoryQuery
     */
    public function getClubCategory()
    {
        return $this->hasOne(ClubCategory::className(), ['id' => 'club_category_id']);
    }

    /**
     * Gets query for [[ClubTime]].
     *
     * @return \yii\db\ActiveQuery|ClubTimeQuery
     */
    public function getClubTime()
    {
        return $this->hasOne(ClubTime::className(), ['id' => 'club_time_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['club_time_id'])) {
            $errors[] = ['club_time_id' => _e('Required')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $student = self::student(2);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_id = $student->id;
        $model->faculty_id = $student->faculty_id;
        $model->edu_plan_id = $student->edu_plan_id;
        $model->gender = $student->gender;
        $model->club_id = $model->clubTime->club_id;

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

        $student = self::student(2);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_id = $student->id;
        $model->faculty_id = $student->faculty_id;
        $model->edu_plan_id = $student->edu_plan_id;
        $model->gender = $student->gender;

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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
