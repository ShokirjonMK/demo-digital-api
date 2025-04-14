<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "election".
 *
 * @property int $id
 * @property string $name
 * @property string $roles
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class ElectionCandidate extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const UPLOADS_FOLDER = 'uploads/election/condidate/';
    public $photo_file;
    public $photoFileMaxSize = 1024 * 1024 * 3; // 3 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'election_candidate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['election_id', 'short_info'], 'required'
            ],
            [['election_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['photo'], 'string', 'max' => 255],
            [['short_info', 'full_info'], 'string'],
            [['photo_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg', 'maxSize' => $this->photoFileMaxSize],

            [
                ['election_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Election::className(),
                'targetAttribute' => ['election_id' => 'id']
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
            'election_id' => _e('election_id'),
            'photo' => _e('photo'),
            'short_info' => _e('short_info'),
            'full_info' => _e('full_info'),

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
            'photo',
            'short_info',
            'full_info',

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

            'election',
            'electionVoteCount',
            'electionVote',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    // public function getInfo()
    // {
    //     if (Yii::$app->request->get('self') == 1) {
    //         return $this->infoRelation[0];
    //     }

    //     return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    // }

    // public function getDescription()
    // {
    //     return $this->info->description ?? '';
    // }

    // public function getInfoRelation()
    // {
    //     // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
    //     return $this->hasMany(ElectionCandidateInfo::class, ['election_candidate_id' => 'id'])
    //         ->andOnCondition(['lang' => Yii::$app->request->get('lang')]);
    // }

    // public function getInfoRelationDefaultLanguage()
    // {
    //     // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
    //     return $this->hasMany(ElectionCandidateInfo::class, ['election_candidate_id' => 'id'])
    //         ->andOnCondition(['lang' => self::$selected_language]);
    // }


    public function getElection()
    {
        return $this->hasOne(Election::className(), ['id' => 'election_id']);
    }


    public function getElectionVote()
    {
        return $this->hasMany(ElectionVote::className(), ['election_candidate_id' => 'id']);
    }

    public function getElectionVoteCount()
    {
        return count($this->electionVote);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            // Photo file saqlaymiz
            $model->photo_file = UploadedFile::getInstancesByName('photo_file');
            if ($model->photo_file) {
                $model->photo_file = $model->photo_file[0];
                $photoFileUrl = $model->uploadFile();
                if ($photoFileUrl) {
                    $model->photo = $photoFileUrl;
                } else {
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
            // Photo file saqlaymiz

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

        // Photo file saqlaymiz
        $model->photo_file = UploadedFile::getInstancesByName('photo_file');
        if ($model->photo_file) {
            $model->photo_file = $model->photo_file[0];
            $photoFileUrl = $model->uploadFile();
            if ($photoFileUrl) {
                $model->photo = $photoFileUrl;
            } else {
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
        // Photo file saqlaymiz

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            1 => _e('Active'),
            0 => _e('Inactive'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }


    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->photo_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->photo_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }


    // 
    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
        return true;
    }


    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),

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
