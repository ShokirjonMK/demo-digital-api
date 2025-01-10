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
class Kafedra extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    const USER_ACCESS_TYPE_ID = 2;

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
        return 'kafedra';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['faculty_id'], 'required'],
            [['direction_id', 'user_id', 'faculty_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            //            [['name'], 'string', 'max' => 255],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //            'name' => 'Name',
            'direction_id' => 'Direction ID',
            'faculty_id' => 'Faculty ID',
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
            'direction_id',
            'faculty_id',
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
            'direction',
            'leader',
            'userAccess',

            'faculty',
            'subjects',
            'description',
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
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Subjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['kafedra_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[Leader]].
     * leader
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[UserAccess]].
     * userAccess
     * @return \yii\db\ActiveQuery
     */

    // public function getuserAccess()
    // {
    //     return $this->hasMany(UserAccess::className(), ['table_id' => 'id'])
    //         ->andOnCondition(['user_access_type_id' => self::USER_ACCESS_TYPE_ID]);
    // }

    public function getUserAccess()
    {
        return $this->hasMany(UserAccess::className(), ['table_id' => 'id'])
            ->andOnCondition(['USER_ACCESS_TYPE_ID' => self::USER_ACCESS_TYPE_ID, 'is_deleted' => 0, 'status' => 1]);
    }

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
                if (isset($post['user_id'])) {
                    $userAccess = new UserAccess();
                    $userAccess->user_id = $post['user_id'];
                    $userAccess->user_access_type_id = self::USER_ACCESS_TYPE_ID;
                    $userAccess->table_id = $model->id;
                    $userAccess->is_leader = 1;
                    if ($userAccess->save()) {
                        if (isset($post['teacher_access'])) {
                            $post['teacher_access'] = str_replace("'", "", $post['teacher_access']);
                            $teacher_access = json_decode(str_replace("'", "", $post['teacher_access']));

                            TeacherAccess::updateAll(['status' => 0 , 'is_deleted' => 1], ['user_id' => $model->user_id]);

                            foreach ($teacher_access as $teacher_access_key => $teacher_access_value) {
                                $subject = Subject::findOne($teacher_access_key);
                                if (isset($subject)) {
                                    foreach ($teacher_access_value as $langKey => $langValue) {
                                        $language = Languages::findOne($langKey);
                                        if (isset($language)) {
                                            foreach ($langValue as $subject_category_value) {
                                                $subject_category = SubjectCategory::findOne($subject_category_value);
                                                if (isset($subject_category)) {
                                                    $userAccessBefore = TeacherAccess::findOne([
                                                        'user_id' => $model->user_id,
                                                        'subject_id' => $teacher_access_key,
                                                        'language_id' => $langKey,
                                                        'is_lecture' => $subject_category_value,
                                                    ]);
                                                    if (!isset($userAccessBefore)) {
                                                        $teacherAccessNew = new TeacherAccess();
                                                        $teacherAccessNew->user_id = $model->user_id;
                                                        $teacherAccessNew->subject_id = $teacher_access_key;
                                                        $teacherAccessNew->language_id = $langKey;
                                                        $teacherAccessNew->is_lecture = $subject_category_value;
                                                        $teacherAccessNew->save();
                                                    } else {
                                                        $userAccessBefore->status = 1;
                                                        $userAccessBefore->is_deleted = 0;
                                                        $userAccessBefore->save(false);
                                                    }
                                                } else {
                                                    $errors[] = ['subject_category_id' => [_e($subject_category_value.' No subject category ID available')]];
                                                }
                                            }
                                        } else {
                                            $errors[] = ['language_id' => [_e($langKey.' No language ID available')]];
                                        }
                                    }
                                } else {
                                    $errors[] = ['subject_id' => [_e($teacher_access_key.' No subject ID available')]];
                                }
                            }
                        }
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

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['direction_id'])) {
            $model->direction_id = null;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $has_error = Translate::checkingUpdate($post);

        if ($has_error['status']) {
            if ($model->save()) {

                /* update User Access and update Teacher Access */
                if (isset($post['user_id'])) {
//                    TeacherAccess::deleteAll(['user_id' => $post['user_id']]);
                    $userAccessUser = User::findOne($post['user_id']);
                    if (isset($userAccessUser)) {
//                        dd(UserAccess::changeLeader($model->id, self::USER_ACCESS_TYPE_ID, $userAccessUser->id));
//                        dd(UserAccess::changeLeader($model->id, self::USER_ACCESS_TYPE_ID, $userAccessUser->id));
                        if (!(UserAccess::changeLeader($model->id, self::USER_ACCESS_TYPE_ID, $userAccessUser->id))) {
                            $errors = ['user_id' => _e('Error occured on updating UserAccess')];
                        }
                    }
                    if (isset($post['teacher_access'])) {
                        TeacherAccess::updateAll(['status' => 0 , 'is_deleted' => 1], ['user_id' => $post['user_id']]);
                        $post['teacher_access'] = str_replace("'", "", $post['teacher_access']);
                        $user_access = json_decode(str_replace("'", "", $post['teacher_access']));
                        foreach ($user_access as $teacher_access_key => $teacher_access_value) {
                            $subject = Subject::findOne($teacher_access_key);
                            if (isset($subject)) {
                                foreach ($teacher_access_value as $langKey => $langValue) {
                                    $language = Languages::findOne([
                                        'id' => $langKey,
                                        'status' => 1,
                                    ]);
                                    if (isset($language)) {
                                        foreach ($langValue as $subject_category_value) {
                                            $subject_category = SubjectCategory::findOne($subject_category_value);
                                            if (isset($subject_category)) {

                                                $userAccessBefore = TeacherAccess::findOne([
                                                    'user_id' => $model->user_id,
                                                    'subject_id' => $teacher_access_key,
                                                    'language_id' => $langKey,
                                                    'is_lecture' => $subject_category_value,
                                                    'status' => 0,
                                                    'is_deleted' => 1
                                                ]);
                                                if (!isset($userAccessBefore)) {
                                                    $teacherAccessNew = new TeacherAccess();
                                                    $teacherAccessNew->user_id = $model->id;
                                                    $teacherAccessNew->subject_id = $teacher_access_key;
                                                    $teacherAccessNew->language_id = $langKey;
                                                    $teacherAccessNew->is_lecture = $subject_category_value;
                                                    $teacherAccessNew->save();
                                                } else {
                                                    $userAccessBefore->status = 1;
                                                    $userAccessBefore->is_deleted = 0;
                                                    $userAccessBefore->save(false);
                                                }
                                            } else {
                                                $errors[] = ['subject_category_id' => [_e($subject_category_value.' No subject category ID available')]];
                                            }
                                        }
                                    } else {
                                        $errors[] = ['language_id' => [_e($langKey.' No language ID available')]];
                                    }
                                }
                            } else {
                                $errors[] = ['subject_id' => [_e($teacher_access_key.' No subject ID available')]];
                            }
                        }
                    }
                }
                /* User Access and update Teacher Access */

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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
