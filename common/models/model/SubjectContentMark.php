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
 * @property string $description
 * @property double $ball
 * @property int $teacher_access_id
 * @property int $subject_topic_id
 * @property int $user_id
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class SubjectContentMark extends \yii\db\ActiveRecord
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
        return 'subject_content_mark';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [[
                'user_id',
                'subject_topic_id',
                'teacher_access_id',
            ], 'required'],
            [['description'], 'string'],
            [
                ['ball'], 'double',
                'max' => 10
            ],
            [[
                'user_id',
                'subject_id',
                'subject_topic_id',
                'teacher_access_id',
                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted',
                'archived',
            ], 'integer'],
            [
                ['user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['subject_topic_id'],
                'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::class, 'targetAttribute' => ['subject_topic_id' => 'id']
            ],
            [
                ['teacher_access_id'],
                'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::class, 'targetAttribute' => ['teacher_access_id' => 'id']
            ],
            [
                ['subject_id'],
                'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']
            ],

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
            'subject_topic_id' => 'Subject Topic Id',
            'teacher_access_id' => 'Teacher Access Id',
            'description' => 'Description',
            'ball' => 'Ball',
            'subject_id' => 'subject_id',
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
            'subject_topic_id',
            'teacher_access_id',
            'description',
            'subject_id',
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
        $extraFields = [
            'subject',
            'subjectType',

            'user',
            'subjectTopic',
            'teacherAccess',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    public function getSubjectTopic()
    {
        return $this->hasOne(SubjectTopic::class, ['id' => 'subject_topic_id']);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::class, ['id' => 'teacher_access_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->subject_id = $model->subjectTopic->subject_id;


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
