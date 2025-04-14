<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%club_time}}".
 *
 * @property int $id
 * @property int|null $club_category_id
 * @property int $club_id
 * @property int|null $room
 * @property int|null $building
 * @property string|null $times
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Club $club
 * @property ClubCategory $clubCategory
 */
class ClubTime extends \yii\db\ActiveRecord
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
        return 'club_time';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['club_category_id', 'duration', 'club_id', 'room_id', 'building_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['club_id', 'times'], 'required'],
            [['times'], 'safe'],
            [['status'], 'default', 'value' => 1],
            [['club_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClubCategory::className(), 'targetAttribute' => ['club_category_id' => 'id']],
            [['club_id'], 'exist', 'skipOnError' => true, 'targetClass' => Club::className(), 'targetAttribute' => ['club_id' => 'id']],
            [
                ['room_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id',
            'building_id',
            'club_category_id',
            'club_id',
            'times',
            'duration',

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
            // 'description' => function ($model) {
            //     return $model->translate->description ?? '';
            // },
            'room_id',
            'building_id',
            'club_category_id',
            'club_id',
            'duration',


            'times',
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

            'clubCategory',
            'club',
            'room',
            'building',
            'studentClub',
            'selected',
            'leader',

            'studentClubsCount',
            'members',


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
     * Gets query for [[Club]].
     *
     * @return \yii\db\ActiveQuery|ClubQuery
     */
    public function getClub()
    {
        return $this->hasOne(Club::className(), ['id' => 'club_id']);
    }

    /**
     * Gets query for [[StudentClubs]].
     *
     * @return \yii\db\ActiveQuery|StudentClubQuery
     */
    public function getStudentClubs()
    {
        if (isRole('student')) {
            return $this->hasOne(StudentClub::className(), ['club_time_id' => 'id'])->onCondition(['student_id' => $this->student()]);
        }
        return $this->hasMany(StudentClub::className(), ['club_time_id' => 'id']);
    }


    public function getLeader()
    {
        return $this->hasOne(StudentClub::className(), ['club_time_id' => 'id'])->onCondition(['is_leader' => 1]);
    }

    public function getStudentClubsCount()
    {
        return count($this->studentClubs);
    }
    public function getMembers()
    {
        return count($this->studentClubs);
    }

    public function getSelected()
    {
        if (isRole('student')) {

            $studentClub = StudentClub::find()
                ->where([
                    'club_time_id' => $this->id,
                    'student_id' => $this->student()
                ])
                ->all();

            if (count($studentClub) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * Gets query for [[ClubCategory]].
     *
     * @return \yii\db\ActiveQuery|ClubCategoryQuery
     */
    public function getClubCategory()
    {
        return $this->hasOne(ClubCategory::className(), ['id' => 'club_category_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['times'])) {

            if (($post['times'][0] == "'") && ($post['times'][strlen($post['times']) - 1] == "'")) {
                $post['times'] =  substr($post['times'], 1, -1);
            }

            if (!isJsonMK($post['times'])) {
                $errors['times'] = [_e('Must be Json')];
            } else {
                $times = ((array)json_decode($post['times']));

                $model->times = $times;
            }
        }

        $model->club_category_id = $model->club->club_category_id;
        if (isset($post['room_id'])) {
            $model->building_id = $model->room->building_id;
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['times'])) {

            if (($post['times'][0] == "'") && ($post['times'][strlen($post['times']) - 1] == "'")) {
                $post['times'] =  substr($post['times'], 1, -1);
            }

            if (!isJsonMK($post['times'])) {
                $errors['times'] = [_e('Must be Json')];
            } else {
                $times = ((array)json_decode($post['times']));

                $model->times = $times;
            }
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
