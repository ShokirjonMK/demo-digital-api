<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;

class SubjectTopicReference extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;


    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_5 = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_topic_reference';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'subject_topic_id',
                    // 'subject_id',
                    // 'user_id',
                    // 'teacher_access_id',
                    // 'link',
                    'name',
                    // 'start_page',
                    // 'end_page',
                ], 'required'
            ],
            [
                [
                    'subject_id',
                    'subject_topic_id',
                    'user_id',
                    'teacher_access_id',
                    'start_page',
                    'end_page',
                    'type',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'archived',
                ], 'integer'
            ],

            [['link'], 'string', 'max' => 255],
            [['name'], 'string'],

            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['subject_topic_id' => 'id']],
            // [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            // [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject_id' => _e('subject_id'),
            'subject_topic_id' => _e('subject_topic_id'),
            'user_id' => _e('user_id'),
            'teacher_access_id' => _e('teacher_access_id'),
            'link' => _e('link'),
            'name' => _e('name'),
            'start_page' => _e('start_page'),
            'end_page' => _e('end_page'),
            'type' => _e('type'),

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
            'subject_id',
            'subject_topic_id',
            'user_id',
            'teacher_access_id',
            'link',
            'name',
            'start_page',
            'end_page',
            'type',

            'order',
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

            'subject',
            'subjectTopic',
            'user',
            'teacherAccess',

            'typeName',
            'startedAt',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
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
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[SubjectTopic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectTopic()
    {
        return $this->hasOne(SubjectTopic::className(), ['id' => 'subject_topic_id']);
    }

    public function getTypeName()
    {
        return $this->typeList()[$this->type];
    }

    /**
     * Gets query for [[TeacherAccess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
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

        if (isRole('teacher')) {
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


        $model->subject_id = $model->subjectTopic->subject_id;
        $model->user_id = current_user_id();

        if (isRole('teacher')) {
            $teacherAccess = TeacherAccess::findOne(['subject_id' => $model->subject_id, 'user_id' => current_user_id()]);
            $model->teacher_access_id =  $teacherAccess ? $teacherAccess->id : 0;
        }

        if ($model->save() && count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function typeList()
    {
        return [
            self::TYPE_1 => _e('Asosiy maqsad va vazifalar, talabalar qanday bilim va ko‘nikmaga ega bo‘lishi kutilmoqda: '),
            self::TYPE_2 => _e('O‘qilishi tavsiya etiladigan asosiy adabiyotlar'),
            self::TYPE_3 => _e('O‘qilishi tavsiya etiladigan qo‘shimcha adabiyotlar'),
            self::TYPE_4 => _e('Muhokama uchun savollar: '),
            self::TYPE_5 => _e('Muhokama uchun kazus yoki muammoli savol: '),
        ];
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
