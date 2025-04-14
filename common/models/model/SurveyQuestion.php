<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class SurveyQuestion extends \yii\db\ActiveRecord
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
        return 'survey_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'min',
                    'max',
                    'type',
                ], 'integer'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status', 'type'], 'default', 'value' => 1],
            [['min'], 'default', 'value' => 0],
            [['max'], 'default', 'value' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // question on info table
            // description on info table

            'min',
            'max',
            // 'type',

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
            'question' => function ($model) {
                return $model->info->question ?? '';
            },
            'min',
            'max',
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
        return $this->hasMany(SurveyQuestionInfo::class, ['survey_question_id' => 'id'])
            ->andOnCondition(['lang' => Yii::$app->request->get('lang')]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(SurveyQuestionInfo::class, ['survey_question_id' => 'id'])
            ->andOnCondition(['lang' => self::$selected_language]);
    }


    public function getKafedras()
    {
        return $this->hasMany(Kafedra::className(), ['direction_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }

        if ($model->save()) {
            if (isset($post['question'])) {
                if (!is_array($post['question'])) {
                    $errors[] = [_e('Please send Question attribute as array.')];
                } else {
                    foreach ($post['question'] as $lang => $question) {
                        $info = new SurveyQuestionInfo();
                        $info->survey_question_id = $model->id;
                        $info->lang = $lang;
                        $info->question = $question;
                        $info->description = $post['description'][$lang] ?? null;
                        if (!$info->save()) {
                            $errors[] = $info->getErrorSummary(true);
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one Question attribute.')];
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
            if (isset($post['question'])) {
                if (!is_array($post['question'])) {
                    $errors[] = [_e('Please send Question attribute as array.')];
                } else {
                    foreach ($post['question'] as $lang => $question) {
                        $info = SurveyQuestionInfo::find()->where(['survey_question_id' => $model->id, 'lang' => $lang])->one();
                        if ($info) {
                            $info->question = $question;
                            $info->description = $post['description'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        } else {
                            $info = new SurveyQuestionInfo();
                            $info->survey_question_id = $model->id;
                            $info->lang = $lang;
                            $info->question = $question;
                            $info->description = $post['description'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one Question attribute.')];
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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
