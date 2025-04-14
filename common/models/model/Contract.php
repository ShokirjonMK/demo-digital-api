<?php

namespace common\models\model;

use common\models\User;
use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;

use api\resources\ResourceTrait;
/**
 * This is the model class for table "test_98".
 *
 * @property int $id
 * @property string $amount
 * @property int $edu_form_id
 * @property int $edu_year_id
 * @property int $edu_type_id
 * @property int $type
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Contract extends \yii\db\ActiveRecord
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
        return 'contract';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['edu_year_id', 'edu_type_id', 'edu_form_id',], 'required'],
            [['status', 'edu_year_id', 'edu_type_id', 'edu_form_id', 'type',], 'integer'],
            [['amount'], 'string', 'max' => 255],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\EduYear::class, 'targetAttribute' => ['edu_year_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\EduType::class, 'targetAttribute' => ['edu_type_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\EduForm::class, 'targetAttribute' => ['edu_form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_year_id' => _e('Edu Year Id'),
            'edu_type_id' => _e('Finish Date'),
            'edu_form_id' => _e('Edu Form Id'),
            'type' => _e('Type'),
            'amount' => _e('Amount'),
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
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'edu_year_id',
            'edu_type_id',
            'edu_form_id',
            'type',
            'amount',
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

            'description',
            'createdBy',
            'updatedBy',
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

    public function getInfoRelation()
    {
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
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
        }
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


    #region rel
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
    public function getEduYear()
    {
        return $this->hasOne(\common\models\model\EduYear::className(), ['id' => 'edu_year_id']);
    }
    public function getEduType()
    {
        return $this->hasOne(\common\models\model\EduType::className(), ['id' => 'edu_type_id']);
    }
    public function getEduForm()
    {
        return $this->hasOne(\common\models\model\EduForm::className(), ['id' => 'edu_form_id']);
    }
    #endregion

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

