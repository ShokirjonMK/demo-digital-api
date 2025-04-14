<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%room}}".
 *
 * @property int $id
 * @property int|null $type type education building or hostel or something
 * @property int|null $gender room gender male 1 female 0
 * @property int|null $empty_count bosh joylar soni
 * @property float|null $price room price
 * @property int $building_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $capacity
 *
 * @property Building $building
 * @property TimeTable[] $timeTables
 */
class Room extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;


    const TYPE_EDUCATIoN = 1;
    const TYPE_HOSTEL = 2;

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
        return 'room';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['type', 'gender', 'empty_count', 'building_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'capacity'], 'integer'],
            [['price'], 'number'],
            [['building_id'], 'required'],
            [['building_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type', //'type education building or hostel or something',
            'gender' => 'Gender', // 'room gender male 1 female 0',
            'empty_count' => 'Empty Count', // 'bosh joylar soni',
            'price' => 'room price',
            // 'name' => 'Name',
            'building_id' => 'Building ID',
            'capacity' => 'capacity',
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
            'gender',
            'empty_count',
            'price',
            'building_id',
            'capacity',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',


        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'building',
            'timeTables',
            'description',
            'hostelStudentCount',
            'hostelStudens',
            'hostelStudentsCount',
            'busyCount',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Building]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * Gets query for [[TimeTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTables()
    {
        return $this->hasMany(TimeTable::className(), ['room_id' => 'id']);
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

    public function getHostelStudents()
    {
        return $this->hasMany(HostelStudentRoom::class, ['room_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0]);
    }

    public function getHostelStudentsCount()
    {
        return $this->getHostelStudent()->count();
    }

    public function getHostelStudentCount()
    {
        return HostelStudentRoom::find()
            ->where(['room_id'=> $this->id])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['archived' => 0])
            ->count();
    }

    /**
     * Gets the count of busy (occupied) hostel student rooms for the current room ID.
     *
     * @return int The count of busy hostel student rooms.
     */
    public function getBusyCount()
    {
        return HostelStudentRoom::find()
            ->where([
                'room_id' => $this->id,
                'is_deleted' => 0,
                'status' => 1
            ])
            ->count();
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
