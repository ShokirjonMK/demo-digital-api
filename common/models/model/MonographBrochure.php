<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

use function PHPSTORM_META\type;

/**
 * This is the model class for table "{{%monograph_brochure}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $keys
 * @property string|null $co_author_user_ids
 * @property string|null $co_authors
 * @property int|null $pages
 * @property int|null $basis_for_publication_id
 * @property string|null $dio
 * @property string|null $udk
 * @property string|null $bbk
 * @property string|null $isbn
 * @property string|null $publisher_name
 * @property string|null $file
 * @property string|null $translator
 * @property int $user_id
 * @property int|null $in_library
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property BasisForPublication $basisForPublication
 * @property User $user
 */
class MonographBrochure extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/monograph_brochure/';
    public $upload_file;
    public $upload_fileMaxSize = 1024 * 1024 * 5; // 5 Mb


    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const TYPE_MONOGRAPH = 1;
    const TYPE_BROCHURE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%monograph_brochure}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'co_authors', 'publisher_name', 'translator'], 'string'],
            [['co_author_user_ids'], 'safe'],
            [['pages', 'basis_for_publication_id', 'user_id', 'in_library', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['user_id'], 'required'],
            [['keys', 'dio', 'udk', 'bbk', 'isbn', 'file'], 'string', 'max' => 255],
            [['basis_for_publication_id'], 'exist', 'skipOnError' => true, 'targetClass' => BasisForPublication::className(), 'targetAttribute' => ['basis_for_publication_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,jpg,doc,docx', 'maxSize' => $this->upload_fileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'name' => _e('Name'),
            'keys' => _e('Keys'),
            'co_author_user_ids' => _e('Co Author User Ids'),
            'co_authors' => _e('Co Authors'),
            'pages' => _e('Pages'),
            'basis_for_publication_id' => _e('Basis For Publication ID'),
            'dio' => _e('Dio'),
            'udk' => _e('Udk'),
            'bbk' => _e('Bbk'),
            'isbn' => _e('Isbn'),
            'publisher_name' => _e('Publisher Name'),
            'file' => _e('File'),
            'translator' => _e('Translator'),
            'user_id' => _e('User ID'),
            'in_library' => _e('In Library'),
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
            'name',
            'keys',
            'co_author_user_ids',
            'co_authors',
            'pages',
            'basis_for_publication_id',
            'dio',
            'udk',
            'bbk',
            'isbn',
            'publisher_name',
            'file',
            'translator',
            'user_id',
            'in_library',
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
            'basisForPublication',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
    /**
     * Gets query for [[BasisForPublication]].
     *
     * @return \yii\db\ActiveQuery|BasisForPublicationQuery
     */
    public function getBasisForPublication()
    {
        return $this->hasOne(BasisForPublication::className(), ['id' => 'basis_for_publication_id']);
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


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if user_id isset on $post model user_id 
        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        if (isset($post['co_author_user_ids'])) {
            // Remove single quotes if present at the beginning and end of the string
            if (($post['co_author_user_ids'][0] === "'") && ($post['co_author_user_ids'][strlen($post['co_author_user_ids']) - 1] === "'")) {
                $post['co_author_user_ids'] = substr($post['co_author_user_ids'], 1, -1);
            }

            // Decode the JSON data into an array and handle errors
            try {
                $co_author_user_ids = \yii\helpers\Json::decode($post['co_author_user_ids'], true);
            } catch (\yii\base\InvalidArgumentException $e) {
                // JSON decoding error occurred
                $errors['co_author_user_ids'] = [_e('Invalid JSON format')];
            }

            // Check if each user ID exists in the users table
            $existingUsers = User::find()->select('id')->indexBy('id')->column();
            $nonExistingIds = array_diff($co_author_user_ids, array_keys($existingUsers));

            if (!empty($nonExistingIds)) {
                $errors['co_author_user_ids'] = [_e('Invalid user IDs: ') . implode(', ', $nonExistingIds)];
            } else {
                // Assign the array to the $model->co_author_user_ids attribute
                $model->co_author_user_ids = $co_author_user_ids;
            }
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {

            // $model->upload_file file saqlaymiz
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_fileFileUrl = $model->uploadFile($model->upload_file);
                if ($upload_fileFileUrl) {
                    $model->file = $upload_fileFileUrl;
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

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['co_author_user_ids'])) {
            // Remove single quotes if present at the beginning and end of the string
            if (($post['co_author_user_ids'][0] === "'") && ($post['co_author_user_ids'][strlen($post['co_author_user_ids']) - 1] === "'")) {
                $post['co_author_user_ids'] = substr($post['co_author_user_ids'], 1, -1);
            }

            // Decode the JSON data into an array and handle errors
            try {
                $co_author_user_ids = \yii\helpers\Json::decode($post['co_author_user_ids'], true);
            } catch (\yii\base\InvalidArgumentException $e) {
                // JSON decoding error occurred
                $errors['co_author_user_ids'] = [_e('Invalid JSON format')];
            }

            // Check if each user ID exists in the users table
            $existingUsers = User::find()->select('id')->indexBy('id')->column();
            $nonExistingIds = array_diff($co_author_user_ids, array_keys($existingUsers));

            if (!empty($nonExistingIds)) {
                $errors['co_author_user_ids'] = [_e('Invalid user IDs: ') . implode(', ', $nonExistingIds)];
            } else {
                // Assign the array to the $model->co_author_user_ids attribute
                $model->co_author_user_ids = $co_author_user_ids;
            }
        }

        // $model->upload_file file saqlaymiz
        $model->upload_file = UploadedFile::getInstancesByName('upload_file');
        if ($model->upload_file) {
            $model->upload_file = $model->upload_file[0];
            $upload_fileFileUrl = $model->uploadFile($model->upload_file);
            if ($upload_fileFileUrl) {
                $model->file = $upload_fileFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            if (count($errors) == 0) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $errors[] = $model->getErrorSummary(true);
        }
    }

    // public static function categoryFields()
    // {
    //     return [
    //         "date",
    //         "file",
    //         // "subject_category",
    //         // "count_of_copyright",
    //         "link",
    //         "input",

    //     ];

    //     /*  return
    //         [
    //             "input"=> ,
    //             "link",
    //             "number",
    //             "file",
    //             "date",
    //             "double_date",
    //             "select",
    //             "nimadir"
    //         ]; */
    // }

    // public static function term()
    // {
    //     return
    //         [
    //             1 => _e("1 year"),
    //             2 => _e("6 month"), //1
    //             3 => _e('Bir kalendar yil davomida'), //1
    //             4 => _e('Sertifikat muddati davomida'), // check
    //             5 => _e('Taribdan chiqarilgunga qadar'),
    //             6 => _e('1 month'), //
    //         ];
    // }

    // public static function tab()
    // {
    //     return [
    //         1 => _e("O‘quv va o‘quv-uslubiy ishlar"),
    //         2 => _e("Ilmiy va innovatsiyalarga oid ishlar"),
    //         3 => _e("Xalqaro hamkorlikka oid ishlar"),
    //         4 => _e("Ma'naviy-ma'rifiy ishlarga rioya etish holati")
    //     ];
    // }

    // public function getExtra()
    // {
    //     return self::extra();
    // }



    // public static function extra()
    // {
    //     return ["fields" => self::categoryFields(), "term" => self::term(), "tab" => self::tab()];
    // }


    /**
     * Returns an array of types with their corresponding descriptions.
     *
     * @return array
     */
    public static function types()
    {
        return [
            self::TYPE_MONOGRAPH => _e("TYPE_MONOGRAPH"),
            self::TYPE_BROCHURE => _e("TYPE_BROCHURE"),
        ];
    }


    public function uploadFile($file)
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $file->extension;

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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
