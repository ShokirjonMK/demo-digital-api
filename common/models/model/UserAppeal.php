<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\Profile;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;


/**
 * This is the model class for table "{{%user_appeal}}".
 *
 * @property int $id
 * @property int $user_id ID of the user who created the record
 * @property int|null $type
 * @property int|null $type_mini
 * @property string|null $text
 * @property string|null $answer_text
 * @property string|null $file
 * @property string|null $answer_file
 * @property string|null $date
 * @property int|null $para_id
 * @property string|null $description
 * @property int|null $order Order of the item
 * @property int|null $status Status of the item (1 = active, 0 = inactive)
 * @property int|null $is_deleted Is the item deleted (0 = no, 1 = yes)
 * @property int $created_at Creation timestamp
 * @property int $updated_at Update timestamp
 * @property int $created_by ID of the user who created the record
 * @property int $updated_by ID of the user who last updated the record
 * @property int $archived Is the item archived (0 = no, 1 = yes)
 *
 * @property User $user
 */
class UserAppeal extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const UPLOADS_FOLDER = 'uploads/user_appeal/';
    public $upload_file;
    public $FileMaxSize = 1024 * 1024 * 3; // 3 Mb
    public $upload_answer_file;
    public $answerFileMaxSize = 1024 * 1024 * 3; // 3 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_appeal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'user_id',
                'type',
                'type_mini',
            ], 'required'],
            [['user_id', 'main', 'type', 'type_mini', 'para_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['text', 'answer_text', 'description'], 'string'],
            [['date'], 'safe'],
            [['file', 'answer_file'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['para_id'], 'exist', 'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']],

            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,doc,docx,mp4,avi', 'maxSize' => $this->FileMaxSize],
            [['upload_answer_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,doc,docx,mp4,avi', 'maxSize' => $this->answerFileMaxSize],

            [['type'], 'in', 'range' => array_keys(self::types()), 'message' => 'Invalid type'],
            [['type_mini'], 'in', 'range' => array_keys(self::typesMini()), 'message' => 'Invalid type mini'],

            [['type_mini'], 'in', 'range' => array_keys(self::typesMini($this->type)), 'message' => 'Invalid type mini'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('ID of the user who created the record'),
            'type' => _e('Type'),
            'type_mini' => _e('Type Mini'),
            'text' => _e('Text'),
            'answer_text' => _e('Answer Text'),
            'file' => _e('File'),
            'answer_file' => _e('Answer File'),
            'date' => _e('Date'),
            'para_id' => _e('Para ID'),
            'description' => _e('Description'),
            'order' => _e('Order of the item'),
            'status' => _e('Status of the item (1 = active, 0 = inactive)'),
            'is_deleted' => _e('Is the item deleted (0 = no, 1 = yes)'),
            'created_at' => _e('Creation timestamp'),
            'updated_at' => _e('Update timestamp'),
            'created_by' => _e('ID of the user who created the record'),
            'updated_by' => _e('ID of the user who last updated the record'),
            'archived' => _e('Is the item archived (0 = no, 1 = yes)'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'type',
            'type_mini',
            'text',
            'answer_text',
            'file',
            'answer_file',
            'date',
            'para_id',
            'description',
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

            'user',
            'typeName',
            'typeMiniName',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }



    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    public function getTypeName()
    {
        $types = self::types(); // Avoid redundant calls
        return $types[$this->type] ?? null; // Use null coalescing for cleaner handling
    }

    public static function types()
    {
        // Return the static array directly without recreating it each time
        $types = [
            1 => _e("Registrator ofis"),
            2 => _e("Akademik halollik"),
            3 => _e("Psiholog"),
        ];
        return $types;
    }

    public function getTypeMiniName()
    {
        // Filter the matching mini type and return its name
        $miniType = array_filter(
            self::typesMini(),
            fn($mini) => $mini['id'] == $this->type_mini
        );

        // Get the first matching result, or return null if none exist
        return $miniType ? array_values($miniType)[0]['name'] : null;
    }

    public static function typesMini()
    {
        // Return the static array directly without recreating it each time
        $typesMini = [
            ["id" => 1, 'type' => 1, "name" => _e("GPA")],
            ["id" => 2, 'type' => 1, "name" => _e("O‘qish joyidan ma’lumotnoma")],
            ["id" => 3, 'type' => 2, "name" => _e("Imtihon akt bo'yicha murojaat")],
            ["id" => 4, 'type' => 3, "name" => _e("Murojaat")],
        ];
        return $typesMini;
    }


    /**
     * TechIssue createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!isset($post['_user_id'])) {
            $model->user_id = Current_user_id();
        }

        if (isset($post['date'])) {
            $model->date = date('Y-m-d', strtotime($post['date']));
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            // dd($model->upload_file);
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $FileUrl = $model->uploadFile($model->upload_file, '');
                if ($FileUrl) {
                    $model->file = $FileUrl;
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

        if (isset($post['date'])) {
            $model->date = date('Y-m-d', strtotime($post['date']));
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $FileUrl = $model->uploadFile($model->upload_file, '');
            if ($FileUrl) {
                $model->_file = $FileUrl;
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

    // public static function types()
    // {
    //     return [
    //         1 => _e("Registrator ofis"),
    //         2 => _e("Akademik halollik"),
    //         3 => _e("Psiholog"),
    //     ];
    // }

    // public static function typesMini()
    // {
    //     return [
    //         [
    //             "id" => 1,
    //             'type' => 1,
    //             "name" => _e("GPA")
    //         ],
    //         [
    //             "id" => 2,
    //             'type' => 1,
    //             "name" => _e("O‘qish joyidan ma’lumotnoma")
    //         ],
    //         [
    //             "id" => 3,
    //             'type' => 2,
    //             "name" => _e("Imtihon akt bo'yicha murojaat")
    //         ],

    //         [
    //             "id" => 4,
    //             'type' => 3,
    //             "name" => _e("Murojaat")
    //         ],
    //     ];
    // }

    public function getExtra()
    {
        return self::extra();
    }

    public static function extra()
    {
        return ["types" => self::types(), "typesMini" => self::typesMini()];
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
