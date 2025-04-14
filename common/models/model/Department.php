<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "kafedra".
 *
 * @property int $id
 * @property string $name
 * @property int $direction_id
 * @property int $faculty_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction $direction
 * @property Faculty $faculty
 * @property Subject[] $subjects
 */
class Department extends \yii\db\ActiveRecord
{
    public static $selected_language = 'en';

    const USER_ACCESS_TYPE_ID = 3;


    const TYPE_DEPARTMENT = 1;
    const TYPE_CENTER = 2;
    const TYPE_CHAIR = 3;
    const TYPE_DEANERY = 4;
    const TYPE_RECTORATE = 5;
    const TYPE_REGISTRATOR_OFFICE = 6;

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
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['parent_id', 'order', 'turniket_department_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['parent_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => _e('Type'),
            'parent_id' => _e('Parent'),

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
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },

            'type',
            'turniket_department_id',
            'parent_id',

            'user_id',
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
            'leader',
            'userAccess',
            'userAccessCount',
            'depLead',

            'children',
            'parent',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

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
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Department::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Department::className(), ['parent_id' => 'id']);
    }


    /**
     * Gets query for [[Leader]].
     * leader
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id'])->select(['first_name', 'last_name', 'middle_name']);
    }
    public function getDepLead()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id'])->select(['first_name', 'last_name', 'middle_name']);
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[UserAccess]].
     * userAccess
     * @return \yii\db\ActiveQuery
     */
    public function getUserAccess()
    {
        return $this->hasMany(UserAccess::className(), ['table_id' => 'id'])
            ->andOnCondition(['USER_ACCESS_TYPE_ID' => self::USER_ACCESS_TYPE_ID, 'is_deleted' => 0])
            ->orderBy('order');
    }


    public function getUserAccessCount()
    {
        return count($this->userAccess);
    }

    /**
     * Gets query for [[UserAccess]].
     * userAccess
     * @return \yii\db\ActiveQuery
     */
    // public function getUserAccess()
    // {
    //     return $this->hasMany(UserAccess::className(), ['table_id' => 'id'])
    //         ->andOnCondition(['user_access_type_id' => self::USER_ACCESS_TYPE_ID]);
    // }
    // public function getUserAccess()
    // {
    //     return $this->hasMany(UserAccess::className(), ['table_id' => 'id'])
    //         ->andOnCondition(['USER_ACCESS_TYPE_ID' => self::USER_ACCESS_TYPE_ID, 'is_deleted' => 0]);
    // }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
            }
        } else {
            $errors = double_errors($errors, $has_error['errors']);
        }

        if (count($errors) == 0) {
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


        /* update User Access */
        if (isset($post['user_id'])) {
            $userAccessUser = User::findOne($post['user_id']);
            if (isset($userAccessUser)) {
                if (!(UserAccess::changeLeader($model->id, self::USER_ACCESS_TYPE_ID, $userAccessUser->id))) {
                    $errors = ['user_id' => _e('Error occured on updating UserAccess')];
                } else {
                    $model->user_id = $post['user_id'];
                }
            }
        }
        /* User Access */



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
            }
        } else {
            $errors = double_errors($errors, $has_error['errors']);
        }

        if (count($errors) == 0) {
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


    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function typesArray($key = null)
    {
        $array = [
            self::TYPE_DEPARTMENT => 'TYPE_DEPARTMENT',
            self::TYPE_CENTER => 'TYPE_CENTER',
            self::TYPE_CHAIR => 'TYPE_CHAIR',
            self::TYPE_DEANERY => 'TYPE_DEANERY',
            self::TYPE_RECTORATE => 'TYPE_RECTORATE',
            self::TYPE_REGISTRATOR_OFFICE => 'TYPE_REGISTRATOR_OFFICE',
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }
}
