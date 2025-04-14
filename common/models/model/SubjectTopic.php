<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 *
 * @property int $name
 * @property int $hours
 * @property string $subject_id
 * @property string $lang_id
 * @property int $description
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan[] $eduPlans
 */
class SubjectTopic extends \yii\db\ActiveRecord
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
        return 'subject_topic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'hours',
                    'subject_id',
                    'lang_id',
                    // 'teacher_access_id',
                ],
                'required'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [
                [
                    'hours',
                    'subject_id',
                    'lang_id',
                    'subject_category_id',
                    'teacher_access_id',
                ],
                'integer'
            ],
            [
                [
                    'name',
                    'description',
                ],
                'string'
            ],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
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
            'name' => 'Name',
            'hours' => 'Hours',
            'subject_id' => 'Subject Id',
            'subject_category_id' => 'Subject Category Id',
            'lang_id' => 'Lang Id',
            'description' => 'Description',
            'teacher_access_id' => 'teacher_access_id',

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
            'name',
            'hours',
            'subject_id',
            'subject_category_id',
            'lang_id',
            'description',
            'teacher_access_id',

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
            'content',
            'subjectContentMark',
            'contentCount',
            'hasContent',
            'mark',
            'subject',
            'teacherAccess',
            'subjectCategory',
            'lang',
            'reference',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getContent()
    {
        if (Yii::$app->request->get('user_id') != null) {
            return $this->hasMany(SubjectContent::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0, 'user_id' => Yii::$app->request->get('user_id')]);
        }
        return $this->hasMany(SubjectContent::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0]);
    }

    public function getSubjectContentMark()
    {
        if (Yii::$app->request->get('user_id') != null) {
            return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0, 'user_id' => Yii::$app->request->get('user_id')]);
        }
        return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0]);
    }

    public function getContentCount()
    {
        return count($this->content);
    }

    public function getHasContent()
    {
        return count($this->content) > 0 ? 1 : 0;
    }

    public function getMark()
    {
        if (Yii::$app->request->get('user_id') != null) {
            return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0, 'user_id' => Yii::$app->request->get('user_id')]);
        }
        return $this->hasMany(SubjectContentMark::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0]);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id'])->select(['id', 'name']);
    }

    public function getReference()
    {
        return $this->hasMany(SubjectTopicReference::className(), ['subject_topic_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0, 'user_id' => Yii::$app->request->get('user_id') ?? current_user_id()]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $teacherAccess = TeacherAccess::findOne(['subject_id' => $model->subject_id, 'user_id' => current_user_id()]);
            $model->teacher_access_id =  $teacherAccess ? $teacherAccess->id : 0;
            $model->user_id = current_user_id();
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
