<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

class Vocation extends \yii\db\ActiveRecord
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
        return 'vocation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'vocation_type_id',
                'start_date',
                'finish_date',
                'user_id',
            ], 'required'],
            [[
                'vocation_type_id',
                'user_id',
                'type',
                'year',
                'month',
            ], 'integer'],

            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            [['symbol'], 'string', 'max' => 5],
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

            'start_date' => _e('start_date'),
            'finish_date' => _e('finish_date'),
            'symbol' => _e('symbol'),
            'user_id' => _e('user_id'),
            'type' => _e('type'),
            'year' => _e('year'),
            'month' => _e('month'),

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

            'start_date',
            'finish_date',
            'symbol',
            'user_id',
            'type',
            'year',
            'month',
            'vocation_type_id',

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
            'vocationType',
            'user',
            'profile',
            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getFullName()
    {
        return $this->profile ? $this->profile->first_name . ' ' . $this->profile->last_name : '';
    }

    public static function filter($year, $month)
    {
        return  self::find()
            ->where(['year' => $year, 'is_deleted' => 0, 'status' => 1])
            ->andWhere("month(start_date) >= " . $month . "")
            ->andWhere("month(finish_date) <= " . $month . "")
            ->all();
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


    /**
     * Gets query for [[VocationType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVocationType()
    {
        return $this->hasOne(VocationType::className(), ['id' => 'vocation_type_id']);
    }

    /**
     * Gets query for [[Rooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRooms()
    {
        return $this->hasMany(Room::className(), ['building_id' => 'id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        // $has_error = Translate::checkingAll($post);

        $model->year = date('Y', strtotime($post['start_date'] ?? time()));
        $model->month = date('m', strtotime($post['start_date'] ?? time()));

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // // if ($has_error['status']) {
        // if ($model->save()) {
        //         if (isset($post['description'])) {
        //             Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
        //         } else {
        //             Translate::createTranslate($post['name'], $model->tableName(), $model->id);
        //         }
        //     $transaction->commit();
        //     return true;
        // } else {
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }
        // } else {
        //     $transaction->rollBack();
        //     return double_errors($errors, $has_error['errors']);
        // }
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

        // $has_error = Translate::checkingUpdate($post);
        // if ($has_error['status']) {
        //     if ($model->save()) {
        //         if (isset($post['name'])) {
        //             if (isset($post['description'])) {
        //                 Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
        //             } else {
        //                 Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
        //             }
        //         }
        //         $transaction->commit();
        //         return true;
        //     } else {
        //         $transaction->rollBack();
        //         return simplify_errors($errors);
        //     }
        // } else {
        //     $transaction->rollBack();
        //     return double_errors($errors, $has_error['errors']);
        // }
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
