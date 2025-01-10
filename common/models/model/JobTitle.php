<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class JobTitle extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'job_title';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_access_type_id',
                    'table_id',
                    'is_leader',
                    'type',
                ], 'integer'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['is_leader'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name on info table
            // description on info table

            'user_access_type_id' => _e('user_access_type_id'),
            'table_id' => _e('table_id'),
            'is_leader' => _e('is_leader'),
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
                return $model->info->name ?? '';
            },

            'user_access_type_id',
            'table_id',
            'is_leader',
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

            'description',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getInfo()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->info->description ?? '';
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(JobTitleInfo::class, ['job_title_id' => 'id'])
            ->andOnCondition(['lang' => Yii::$app->request->get('lang')]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(JobTitleInfo::class, ['job_title_id' => 'id'])
            ->andOnCondition(['lang' => self::$selected_language]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }

        if ($model->save()) {
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];
                } else {
                    foreach ($post['name'] as $lang => $name) {
                        $info = new JobTitleInfo();
                        $info->job_title_id = $model->id;
                        $info->lang = $lang;
                        $info->name = $name;
                        $info->status = 1;
                        $info->description = $post['description'][$lang] ?? null;
                        if (!$info->save()) {
                            $errors[] = $info->getErrorSummary(true);
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one Name attribute.')];
            }
        } else {
            $errors[] = $model->getErrorSummary(true);
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

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }


        if ($model->save()) {
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];
                } else {
                    foreach ($post['name'] as $lang => $name) {
                        $info = JobTitleInfo::find()->where(['job_title_id' => $model->id, 'lang' => $lang])->one();
                        if ($info) {
                            $info->name = $name;
                            $info->description = $post['description'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        } else {
                            $info = new JobTitleInfo();
                            $info->job_title_id = $model->id;
                            $info->lang = $lang;
                            $info->name = $name;
                            $info->description = $post['description'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one Name attribute.')];
            }
        } else {
            $errors[] = $model->getErrorSummary(true);
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
