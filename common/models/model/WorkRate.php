<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class WorkRate extends \yii\db\ActiveRecord
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
        return 'work_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            // [
            //     [
            //         'daily_hours',
            //     ], 'string'
            // ],
            [
                [
                    'rate',
                    'weekly_hours',
                    'hour_day',
                ], 'double'
            ],

            [['type', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            // [['name'], 'string', 'max' => 255],
            // [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rate' => _e('rate'),
            'weekly_hours' => _e('weekly_hours'),
            'hour_day' => _e('hour_day'),
            'daily_hours' => _e('daily_hours'),
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
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },

            'rate',
            'weekly_hours',
            'hour_day',
            'daily_hours',
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
            'rooms',
            'description',
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

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['daily_hours'])) {
            if (($post['daily_hours'][0] == "'") && ($post['daily_hours'][strlen($post['daily_hours']) - 1] == "'")) {
                $post['daily_hours'] =  substr($post['daily_hours'], 1, -1);
            }

            if (!isJsonMK($post['daily_hours'])) {
                $errors['daily_hours'] = [_e('Must be Json')];
            } else {
                $daily_hours = ((array)json_decode($post['daily_hours']));
                $model->daily_hours = $daily_hours;
            }
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

        if (isset($post['daily_hours'])) {
            if (($post['daily_hours'][0] == "'") && ($post['daily_hours'][strlen($post['daily_hours']) - 1] == "'")) {
                $post['daily_hours'] =  substr($post['daily_hours'], 1, -1);
            }

            if (!isJsonMK($post['daily_hours'])) {
                $errors['daily_hours'] = [_e('Must be Json')];
            } else {
                $daily_hours = ((array)json_decode($post['daily_hours']));
                $model->daily_hours = $daily_hours;
            }
        }

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
