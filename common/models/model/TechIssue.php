<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\Profile;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%tech_issue}}".
 *
 * @property int $id
 * @property int|null $tech_issue_type_id
 * @property int|null $building_id
 * @property int|null $room_id
 * @property int|null $issue_user_id
 * @property int|null $answer_user_id
 * @property string|null $issue_text
 * @property string|null $answer_text
 * @property string|null $file
 * @property string|null $issue_file
 * @property string|null $answer_file
 * @property string|null $answer_date
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property User $answerUser
 * @property Building $building
 * @property User $issueUser
 * @property Room $room
 * @property TechIssueType $techIssueType
 */
class TechIssue extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const UPLOADS_FOLDER = 'uploads/tech_issue/';
    public $upload_issue_file;
    public $issueFileMaxSize = 1024 * 1024 * 3; // 3 Mb
    public $upload_answer_file;
    public $answerFileMaxSize = 1024 * 1024 * 3; // 3 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tech_issue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tech_issue_type_id', 'building_id', 'room_id', 'issue_user_id', 'answer_user_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['issue_text', 'answer_text'], 'string'],
            [['answer_date'], 'safe'],
            [['file', 'issue_file', 'answer_file'], 'string', 'max' => 255],
            [['answer_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['answer_user_id' => 'id']],
            [['building_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']],
            [['issue_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['issue_user_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
            [['tech_issue_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TechIssueType::className(), 'targetAttribute' => ['tech_issue_type_id' => 'id']],

            [['upload_issue_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,doc,docx,mp4,avi', 'maxSize' => $this->issueFileMaxSize],
            [['upload_answer_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,doc,docx,mp4,avi', 'maxSize' => $this->answerFileMaxSize],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'tech_issue_type_id' => _e('Tech Issue Type ID'),
            'building_id' => _e('Building ID'),
            'room_id' => _e('Room ID'),
            'issue_text' => _e('Issue Text'),
            'answer_text' => _e('Answer Text'),
            'file' => _e('File'),
            'issue_file' => _e('Issue File'),
            'answer_file' => _e('Answer File'),
            'answer_date' => _e('Answer Date'),
            'order' => _e('Order'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'archived' => _e('Archived'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'tech_issue_type_id',
            'building_id',
            'room_id',
            'issue_user_id',
            'answer_user_id',
            'issue_text',
            'answer_text',
            'file',
            'issue_file',
            'answer_file',
            'answer_date',
            'order',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'archived',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'answerUser',
            'building',
            'issueUser',
            'issueProfile',
            'answerProfile',
            'room',
            'techIssueType',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[AnswerUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getAnswerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'answer_user_id']);
    }

    public function getAnswerProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'answer_user_id']);
    }

    /**
     * Gets query for [[Building]].
     *
     * @return \yii\db\ActiveQuery|BuildingQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * Gets query for [[IssueUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getIssueUser()
    {
        return $this->hasOne(User::className(), ['id' => 'issue_user_id']);
    }
    public function getIssueProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'issue_user_id']);
    }

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery|RoomQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    /**
     * Gets query for [[TechIssueType]].
     *
     * @return \yii\db\ActiveQuery|TechIssueTypeQuery
     */
    public function getTechIssueType()
    {
        return $this->hasOne(TechIssueType::className(), ['id' => 'tech_issue_type_id']);
    }

    /**
     * TechIssue createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!isset($post['issue_user_id'])) {
            $model->issue_user_id = Current_user_id();
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $model->upload_issue_file = UploadedFile::getInstancesByName('upload_issue_file');
            // dd($model->upload_issue_file);
            if ($model->upload_issue_file) {
                $model->upload_issue_file = $model->upload_issue_file[0];
                $issueFileUrl = $model->uploadFile($model->upload_issue_file, 'issue');
                if ($issueFileUrl) {
                    $model->issue_file = $issueFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            $model->upload_answer_file = UploadedFile::getInstancesByName('upload_answer_file');
            if ($model->upload_answer_file) {
                $model->upload_answer_file = $model->upload_answer_file[0];
                $answerFileUrl = $model->uploadFile($model->upload_answer_file, 'answer');
                if ($answerFileUrl) {
                    $model->answer_file = $answerFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            if (count($errors) == 0) {
                if ($model->save()) {
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    /**
     * TechIssue updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload_issue_file = UploadedFile::getInstancesByName('upload_issue_file');
        if ($model->upload_issue_file) {
            $model->upload_issue_file = $model->upload_issue_file[0];
            $issueFileUrl = $model->uploadFile($model->upload_issue_file, 'issue');
            if ($issueFileUrl) {
                $model->issue_file = $issueFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_answer_file = UploadedFile::getInstancesByName('upload_answer_file');
        if ($model->upload_answer_file) {
            $model->upload_answer_file = $model->upload_answer_file[0];
            $answerFileUrl = $model->uploadFile($model->upload_answer_file, 'answer');
            if ($answerFileUrl) {
                $model->answer_file = $answerFileUrl;
            } else {
                $errors[] = $model->errors;
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

    public function uploadFile($file, $type)
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . $type . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
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
