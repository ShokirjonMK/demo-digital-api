<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teacher_access".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject_id
 * @property int $language_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Languages $language
 * @property Subject $subject
 * @property User $user
 * @property TimeTable[] $timeTables
 */
class TeacherAccess extends \yii\db\ActiveRecord
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
        return 'teacher_access';
    }

    /**
     * {@inheritdoc}
     */
    // public function rules()
    // {
    //     return [
    //         [['user_id', 'subject_id', 'language_id'], 'required'],
    //         [['is_lecture', 'user_id', 'subject_id', 'language_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
    //         [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
    //         [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
    //         [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
    //     ];
    // }
    public function rules()
    {
        return [
            [['user_id', 'subject_id', 'language_id'], 'required'],
            [['is_lecture', 'user_id', 'subject_id', 'language_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            
            [['user_id', 'subject_id', 'language_id', 'is_deleted', 'is_lecture'], 'unique', 'targetAttribute' => ['user_id', 'subject_id', 'language_id', 'is_deleted', 'is_lecture'], 'message' => 'The combination of User ID, Subject ID, Language ID, Is Deleted and Is Lecture has already been taken.'],
            
            // [['user_id', 'is_lecture', 'subject_id', 'is_deleted'], 'unique', 'targetAttribute' => ['user_id', 'subject_id','is_lecture', 'is_deleted'], 'message' => 'The combination of User ID, Subject ID and Is Deleted has already been taken.'],
            //         [
            //             ['user_id', 'is_lecture', 'subject_id', 'is_deleted'],
            //             'unique',
            //             'targetAttribute' => ['user_id', 'subject_id', 'is_lecture', 'is_deleted'],
            //             'message' => 'The combination of User ID, is_lecture, Subject ID, and Is Deleted has already been taken.',
            //             'when' => function ($model) {
            //                 return $model->is_deleted == 0;
            //             },
            //             'whenClient' => "function (attribute, value) {
            //     return $('#model-is_deleted').val() == 0;
            // }"
            //         ],

        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'subject_id' => 'Subject ID',
            'language_id' => 'Languages ID',
            'is_lecture' => 'is_lecture',
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
            'teacher' => function ($model) {
                return $model->teacher ?? null;
            },
            'user_id',
            'is_lecture',
            'subject_id',
            'language_id',
            'is_deleted',
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
            'language',
            'subject',
            'subjectAll',
            'teacher',
            'examStudentCount',
            'examStudent',
            'user',

            'hasContent',
            'content',
            'profile',

            'timeTables',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getExamStudent()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }

    public function getExamStudentCount()
    {
        return count($this->examStudent);
    }

    /**
     * Gets query for [[Languages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectAll()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }
    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        // $data = [];
        // $data['first_name'] = $this->profile->first_name;
        // $data['last_name'] = $this->profile->last_name;
        // $data['middle_name'] = $this->profile->middle_name;

        // return $data;

        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']); //->onCondition(['is_deleted' => 0]); //->select(['first_name', 'last_name', 'middle_name']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id'])->select(['first_name', 'last_name', 'middle_name']);
    }

    public function getContent()
    {
        $model = new SubjectContent();

        $query = $model->find()
            ->andWhere(
                ['user_id' => $this->user_id, 'is_deleted' => 0, 'archived' => 0]
            )
            ->andWhere([
                'in', 'subject_topic_id',
                SubjectTopic::find()->select('id')->where(['subject_id' => $this->subject_id, 'lang_id' => $this->language_id])
            ]);

        $data = $query->all();

        return count($data);
    }

    public function getHasContent()
    {
        $model = new SubjectContent();

        $query = $model->find()
            ->andWhere(
                ['user_id' => $this->user_id, 'is_deleted' => 0, 'archived' => 0]
            )
            ->andWhere([
                'in', 'subject_topic_id',
                SubjectTopic::find()->select('id')->where(['subject_id' => $this->subject_id, 'lang_id' => $this->language_id])
            ]);

        $data = $query->all();

        return count($data);
    }


    /**
     * Gets query for [[TimeTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTables()
    {
        return $this->hasMany(TimeTable::className(), ['teacher_access_id' => 'id']);
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
