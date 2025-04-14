<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "building".
 *
 * @property int $id
 * @property string $name
 * @property int|1 $type
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Room[] $rooms
 */
class Building extends \yii\db\ActiveRecord
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
        return 'building';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['name'], 'required'],
            [['order', 'type', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
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
            // 'name' => 'Name',
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


            'roomLecture',
            'roomSeminar',
            'roomsCount',
            'capacityCount',
            'roomsLectureCount',
            'roomSeminarCount',
            'hostelStudentCount',
            'roomsCapacity',
            'roomsCapacityFemale',
            'roomsCapacityMale',

            'roomsBusyFemale',
            'roomsBusyMale',
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

    /**
     * Gets query for [[Rooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRooms()
    {
        return $this->hasMany(Room::className(), ['building_id' => 'id'])
            ->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getRoomLecture()
    {
        return $this->hasMany(Room::className(), ['building_id' => 'id'])->onCondition(['>=', 'capacity', 60])
            ->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getRoomSeminar()
    {
        return $this->hasMany(Room::className(), ['building_id' => 'id'])->onCondition(['<', 'capacity', 60])
            ->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getRoomsCount()
    {
        return count($this->rooms);
    }

    public function getCapacityCount()
    {
        return $this->getRooms()->sum('capacity');

        return count($this->rooms);
    }

    public function getHostelStudentCount()
    {
        return HostelStudentRoom::find()
            ->where(['in', 'room_id', $this->getRooms()->select('id')])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['archived' => 0])
            ->count();
    }

    /**
     * Get the total capacity of rooms of type TYPE_HOSTEL in the building.
     *
     * @return integer Total capacity of the rooms.
     */
    public function getRoomsCapacity()
    {
        return Room::find()
            ->where([
                'building_id' => $this->id,     // Fetches rooms from the current building
                'status' => 1,                  // Ensures that the room is active (assuming 1 means active)
                'type' => Room::TYPE_HOSTEL,   // Fetches only rooms of type 'TYPE_HOSTEL'
                'is_deleted' => 0               // Ensures that the room is not marked as deleted
            ])
            ->sum('capacity');  // Sum the capacity of the fetched rooms
    }

    public function getRoomsBusyFemale()
    {
        // Define a subquery to fetch the room_ids from the Room table based on the conditions provided.
        // This will be used to find how many HostelStudentRooms are associated with these room_ids.
        $subQuery = Room::find()
            ->select('id')  // Only the ID is required for the IN condition
            ->where([
                'building_id' => $this->id,          // Matches rooms from the current building
                'status' => 1,                       // Ensures the room is active (assuming 1 means active)
                'gender' => 0,                       // Matches male rooms (assuming 1 represents male)
                'type' => Room::TYPE_HOSTEL,         // Considers only rooms of type 'TYPE_HOSTEL'
                'is_deleted' => 0                    // Ensures the room is not marked as deleted
            ]);

        // Now, let's count how many active and non-deleted HostelStudentRooms are associated with the room_ids from the subquery.
        $count = HostelStudentRoom::find()
            ->where(['in', 'room_id', $subQuery])  // Uses the room IDs from the subquery
            ->andWhere([
                'is_deleted' => 0,                  // Ensures the record is not marked as deleted
                'status' => 1                       // Ensures the record is active (assuming 1 means active)
            ])
            ->count();

        return $count;
    }

    public function getRoomsBusyMale()
    {
        // Define a subquery to fetch the room_ids from the Room table based on the conditions provided.
        // This will be used to find how many HostelStudentRooms are associated with these room_ids.
        $subQuery = Room::find()
            ->select('id')  // Only the ID is required for the IN condition
            ->where([
                'building_id' => $this->id,          // Matches rooms from the current building
                'status' => 1,                       // Ensures the room is active (assuming 1 means active)
                'gender' => 1,                       // Matches male rooms (assuming 1 represents male)
                'type' => Room::TYPE_HOSTEL,         // Considers only rooms of type 'TYPE_HOSTEL'
                'is_deleted' => 0                    // Ensures the room is not marked as deleted
            ]);

        // Now, let's count how many active and non-deleted HostelStudentRooms are associated with the room_ids from the subquery.
        $count = HostelStudentRoom::find()
            ->where(['in', 'room_id', $subQuery])  // Uses the room IDs from the subquery
            ->andWhere([
                'is_deleted' => 0,                  // Ensures the record is not marked as deleted
                'status' => 1                       // Ensures the record is active (assuming 1 means active)
            ])
            ->count();

        return $count;
    }


    public function getRoomsCapacityFemale()
    {
        return Room::find()
            ->where([
                'building_id' => $this->id,
                'gender' => 0,
                'status' => 1,
                'type' => Room::TYPE_HOSTEL,
                'is_deleted' => 0
            ])
            ->sum('capacity');  // Sum the capacity of the fetched rooms
    }

    public function getRoomsCapacityMale()
    {
        return Room::find()
            ->where([
                'building_id' => $this->id,
                'gender' => 1,
                'status' => 1,
                'type' => Room::TYPE_HOSTEL,
                'is_deleted' => 0
            ])
            ->sum('capacity');  // Sum the capacity of the fetched rooms
    }

    public function getRoomsLectureCount()
    {
        return count($this->roomLecture);
    }
    public function getRoomSeminarCount()
    {
        return count($this->roomSeminar);
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

    public static function typeList()
    {
        return
            [
                self::TYPE_EDUCATIoN => _e("O'quv"),
                self::TYPE_HOSTEL => _e("TTJ"),
            ];
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
