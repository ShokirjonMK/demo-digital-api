<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr_subject_category_time".
 *
 * @property int $id
 * @property int $edu_semestr_subject_id
 * @property int $subject_category_id
 * @property int $subject_id
 * @property int $edu_semestr_id
 * @property int $hours
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrSubject $eduSemestrSubject
 * @property SubjectCategory $subjectCategory
 */
class EduSemestrSubjectCategoryTime extends \yii\db\ActiveRecord
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
        return 'edu_semestr_subject_category_time';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_semestr_subject_id', 'subject_category_id', 'hours' , 'edu_semestr_id', 'subject_id'], 'required'],
            [['edu_semestr_id', 'subject_id' ,'edu_semestr_subject_id', 'subject_category_id', 'hours', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_semestr_subject_id' => 'Edu Semestr Subject ID',
            'subject_category_id' => 'Subject Category ID',
            'hours' => 'Hours',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /**
     * Gets query for [[EduSemestrSubject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }


    public function extraFields()
    {
        $extraFields =  [
            'eduSemestrSubject',
            'subjectCategory',
            'subject',
            'freeHour',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getFreeHour()
    {
        $groupId = Yii::$app->request->get('group_id');
        $query = TimetableDate::find()
            ->where([
                'group_id' => $groupId,
                'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
                'subject_category_id' => $this->subject_category_id,
                'group_type' => 1,
                'status' => 1,
                'is_deleted' => 0
            ])->count();
        $freeHour = ($this->hours / 2) - $query;
        if ($freeHour > 0) {
            return $freeHour;
        }
        return 0;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if($model->save()){
            $transaction->commit();
            return true;
        }else{
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
        if($model->save()){
            $transaction->commit();
            return true;
        }else{
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public function beforeSave($insert) {
        if ($insert) {
            $this->created_by = Current_user_id();
        }else{
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }



}
