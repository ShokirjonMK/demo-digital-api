<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $name
 * @property int $building_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Building $building
 * @property TimeTable1[] $timeTables
 */
class RoomIp extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public $upload;

    public $fileMaxSize = 1024 * 1024 * 5;
    public $excelFileMaxSize = 1024 * 1024 * 10; // 10 Mb

    const UPLOADS_FOLDER = 'uploads/export-import/';

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
        return 'room_ip';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['ip','room_id' , 'building_id'], 'required'],
            [['ip'], 'unique'],
            [['ip'], 'ip'],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx , xls', 'maxSize' => $this->excelFileMaxSize],
            [['room_id', 'building_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
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
            //            'name' => 'Name',
            'building_id' => 'Building ID',
            'capacity' => 'capacity',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Upd ated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'ip',
            'room_id',
            'building_id',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];

        return $fields;
    }

    /**
     * Gets query for [[Building]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    public function extraFields()
    {
        $extraFields =  [
            'room',
            'building',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $model->save(false);

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function createExcelImport($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = new RoomIp();

        $model->upload = UploadedFile::getInstancesByName('upload');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $fileUrl = $model->uploadFile();
        } else {
            $errors[] = ['file' => _e('File not found')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $inputFileName = $fileUrl;
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $model->deleteFile($fileUrl);

        $roomId = $post['room_id'];
        $buildingId = $post['building_id'];

        foreach ($data as $key => $row) {
            if ($key != 0) {
                $ip = $row[0];

                if ($ip == "") {
                    break;
                }

                $new = new RoomIp();
                $new->room_id = $roomId;
                $new->building_id = $buildingId;
                $new->ip = $ip;
                if (!$new->validate()) {
                    $errors[] = $new->errors;
                    break;
                }
                $new->save(false);
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function uploadFile()
    {
        $folder_name = substr(STORAGE_PATH, 0, -1);
        if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
            mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
        }
        $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->upload->extension;
        $miniUrl = self::UPLOADS_FOLDER . $fileName;
        $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
        $this->upload->saveAs($url, false);
        return "storage/" . $miniUrl;
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
        return true;
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
