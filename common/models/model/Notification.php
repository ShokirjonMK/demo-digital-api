<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 * @property string $name in translate with description
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
class Notification extends \yii\db\ActiveRecord
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
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //            [['name'], 'required'],
            [['order', 'status', 'type', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            //            [['name'], 'string', 'max' => 255],
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
            'roles',

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

    /** notification roles */
    public function getRoles()
    {
        return $this->hasMany(NotificationRole::className(), ['notification_id' => 'id'])->select('role');
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $auth = Yii::$app->authManager;
        $roles = json_decode(str_replace("'", "", $post['roles'] ?? null));
        if (is_array($roles)) {

            foreach ($roles as $role) {
                $authorRole = $auth->getRole($role);
                if (!$authorRole) {
                    $errors[] = ['role' => [_e('Role not found.(' . $role . ')')]];
                }
            }
        } else {
            $errors[] = ['role' => [_e('Role is invalid')]];
        }

        if (count($errors) == 0) {
            $has_error = Translate::checkingAll($post);

            if ($has_error['status']) {
                if ($model->save()) {
                    if (isset($post['name'])) {
                        if (isset($post['description'])) {
                            Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                        } else {
                            Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                        }
                    }
                    foreach ($roles as $role) {
                        $authorRole = $auth->getRole($role);
                        if ($authorRole) {
                            ////code
                            $notificcationRole = new NotificationRole();
                            $notificcationRole->notification_id = $model->id;
                            $notificcationRole->role = $authorRole->name;
                            if (!($notificcationRole->validate())) {
                                $errors[] = $notificcationRole->errors;
                            }
                            $notificcationRole->save();

                            ////code
                        } else {
                            $errors[] = ['role' => [_e('Role not found.(' . $role . ')')]];
                        }
                    }

                    if (count($errors) == 0) {
                        $transaction->commit();
                        return true;
                    }
                } else {
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            } else {
                $transaction->rollBack();
                return double_errors($errors, $has_error['errors']);
            }
        }

        if (count($errors) > 0) {
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
        $has_error = Translate::checkingUpdate($post);

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description']) && isset($post['name'])) {
                    Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } elseif (isset($post['name'])) {
                    Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
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


    /**
     * Deletes a notification and associated data from related tables.
     *
     * @param Notification $model The notification model to delete.
     * @return mixed Returns true if deletion is successful, otherwise returns error messages.
     */
    public static function deleteItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();

        // Remove related records.
        NotificationRole::deleteAll(['notification_id' => $model->id]);
        NotificationUser::deleteAll(['notification_id' => $model->id]);
        Translate::deleteAll(['model_id' => $model->id, 'table_name' => 'notification']);

        // Try deleting the main notification.
        if ($model->delete()) {
            $transaction->commit();
            return true;
        } else {
            // Rollback if an error occurs.
            $transaction->rollBack();
            return [_e('Error occurred on deleting')];
        }
    }


    // public static function deleteItem($model)
    // {
    //     $transaction = Yii::$app->db->beginTransaction();
    //     $errors = [];

    //     NotificationRole::deleteAll(['notification_id' => $model->id]);
    //     NotificationUser::deleteAll(['notification_id' => $model->id]);
    //     Translate::deleteAll(['model_id' => $model->id, 'table_name' => 'notification']);

    //     if ($model->delete()) {
    //         $transaction->commit();
    //         return true;
    //     } else {
    //         $errors[] = _e('Error occurred on deleting');
    //         $transaction->rollBack();
    //         return simplify_errors($errors);
    //     }
    // }

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
