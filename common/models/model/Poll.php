<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%poll}}".
 *
 * @property int $id
 * @property int|null $type
 * @property string|null $roles
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $is_deleted
 * @property PollUser[] $pollUsers
 * @property PollQuestionOption[] $pollQuestionOptions
 * @property PollQuestion[] $pollQuestions
 */
class Poll extends \yii\db\ActiveRecord
{
    use ResourceTrait;
    public static $selected_language = 'uz';

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const FOR_BAKALAVR = 1;
    const FOR_MAGISTR = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%poll}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['roles'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'type' => _e('Type'),
            'roles' => _e('roles'),
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
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'type',
            'roles',
            'order',
            'status',
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
            'pollQuestionOptions',
            'pollQuestions',
            'pollUsers',
            'description',
            'isDone',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    /**
     * Get Tranlate
     *
     * @return void
     */
    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    public function getIsDone()
    {
        $pollQuestionCount = count($this->pollQuestions);
        $pollUserCount = count($this->pollUsersSelf);

        if ($pollQuestionCount == $pollUserCount) {
            return 1;
        }
        return 0;
        // return $this->hasMany(PollQuestionOption::className(), ['poll_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[PollQuestionOptions]].
     *
     * @return \yii\db\ActiveQuery|PollQuestionOptionQuery
     */
    public function getPollQuestionOptions()
    {
        return $this->hasMany(PollQuestionOption::className(), ['poll_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[PollQuestions]].
     *
     * @return \yii\db\ActiveQuery|PollQuestion
     */
    public function getPollQuestions()
    {
        return $this->hasMany(PollQuestion::className(), ['poll_id' => 'id'])->onCondition(['is_deleted' => 0, 'status' => 1]);
    }

    /**
     * Gets query for [[PollUsers]].
     *
     * @return \yii\db\ActiveQuery|PollUser
     */
    public function getPollUsers()
    {
        if (isRole('admin'))
            return $this->hasMany(PollUser::className(), ['poll_id' => 'id'])->onCondition(['is_deleted' => 0]);
        return $this->hasMany(PollUser::className(), ['poll_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => current_user_id()]);
    }

    public function getPollUsersSelf()
    {
        return $this->hasMany(PollUser::className(), ['poll_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => current_user_id()]);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['roles']))
            $model->roles =  json_decode($post['roles']);

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
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

        if (isset($post['roles']))
            $model->roles =  json_decode($post['roles']);

        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function typeList()
    {
        return
            [
                self::FOR_BAKALAVR => _e("FOR_BAKALAVR"),
                self::FOR_MAGISTR => _e("FOR_MAGISTR"),
            ];
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
