<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "election".
 *
 * @property int $id
 * @property string $election_id
 * @property string $user_id
 * @property string $election_candidate_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class ElectionVote extends \yii\db\ActiveRecord
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
        return 'election_vote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'election_id',
                    'election_candidate_id',
                ], 'required'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [[
                'election_id',
                'user_id',
                'election_candidate_id',
            ], 'integer'],
            [['status'], 'default', 'value' => 1],
            [
                ['election_candidate_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ElectionCandidate::className(),
                'targetAttribute' => ['election_candidate_id' => 'id']
            ],
            [
                ['election_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Election::className(),
                'targetAttribute' => ['election_id' => 'id']
            ],

            [['election_candidate_id'], 'unique', 'targetAttribute' => ['election_id', 'user_id'], 'message' => _e('You have already elected')],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'election_id' => _e('election_id'),
            'user_id' => _e('user_id'),
            'election_candidate_id' => _e('election_candidate_id'),
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

            'election_id',
            'user_id',
            'election_candidate_id',

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

            'electionCondidate',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Get Translate
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

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    public function getElectionCondidate()
    {
        return $this->hasOne(ElectionCandidate::className(), ['id' => 'election_candidate_id']);
    }

    public function getElection()
    {
        return $this->hasOne(Election::className(), ['id' => 'election_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        // dd($model->electionCondidate);
        $model->election_id = $model->electionCondidate->election_id;

        // if (!in_array($model->election->role, current_user_roles_array())) {
        //     $errors[] = "This election not for you";
        //     return simplify_errors($errors);
        // }

        $now_time = time();

        if (!($model->election->start <= $now_time)) {
            $errors[] = "Election not starts yet";
            return simplify_errors($errors);
        }

        if (!($now_time <= $model->election->finish)) {
            $errors[] = "Election time expired";
            return simplify_errors($errors);
        }

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
