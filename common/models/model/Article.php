<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%scientific_article}}".
 *
 * @property int $id
 * @property string $name Name of the article
 * @property string|null $abstract Abstract of the article
 * @property string|null $key_words Key words
 * @property string|null $co_authors Co-authors
 * @property string|null $journal_name Journal name
 * @property string|null $journal_country Journal country
 * @property int $user_id User ID
 * @property int|null $scientific_specialization_id Scientific specialization ID
 * @property string|null $specialization Specialization
 * @property int|null $language_id Language of the article
 * @property string|null $issn ISSN number
 * @property string|null $doi DOI number
 * @property string|null $date Date of publication
 * @property string|null $journal_type Type of journal
 * @property int|null $kpi_data yes in kpi data
 * @property string|null $sdg SDG data
 * @property string|null $kavrtili Kavrtili data
 * @property int|null $type type of the article (1 = milliy oak, 2 = xalqaro jurnali (scopus va wosdan tashqari) chop etilgani, 3 = Scopus va wos-à Ilmiy boshqarmani o’zi kiritadi)
 * @property string|null $file File
 * @property string|null $link link
 * @property int|null $order Order of the item
 * @property int|null $status Status of the item (1 = active, 0 = inactive)
 * @property int|null $is_deleted Is the item deleted (0 = no, 1 = yes)
 * @property int $created_at Creation timestamp
 * @property int $updated_at Update timestamp
 * @property int $created_by ID of the user who created the record
 * @property int $updated_by ID of the user who last updated the record
 * @property int $archived Is the item archived (0 = no, 1 = yes)
 *
 * @property Language $language
 * @property ScientificSpecialization $scientificSpecialization
 * @property User $user
 */
class Article extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/scientific_article/';
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

    const TYPE_NATIONAL = 1;
    const TYPE_INTERNATIONAL = 2;
    const TYPE_SCOPUS_WOS = 3;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'abstract', 'key_words', 'journal_name', 'user_id', 'specialization', 'date', 'journal_type', 'type'], 'required'],
            [['abstract', 'key_words', 'co_authors'], 'string'],
            [['user_id', 'scientific_specialization_id', 'language_id', 'kpi_data', 'type', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['date'], 'safe'],
            [['name', 'journal_name', 'journal_country', 'specialization', 'issn', 'doi', 'journal_type', 'sdg', 'kavrtili', 'file', 'link'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['scientific_specialization_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScientificSpecialization::className(), 'targetAttribute' => ['scientific_specialization_id' => 'id']],
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
            'name' => _e('Name of the article'),
            'abstract' => _e('Abstract of the article'),
            'key_words' => _e('Key words'),
            'co_authors' => _e('Co-authors'),
            'journal_name' => _e('Journal name'),
            'journal_country' => _e('Journal country'),
            'user_id' => _e('User ID'),
            'scientific_specialization_id' => _e('Scientific specialization ID'),
            'specialization' => _e('Specialization'),
            'language_id' => _e('Language of the article'),
            'issn' => _e('ISSN number'),
            'doi' => _e('DOI number'),
            'date' => _e('Date of publication'),
            'journal_type' => _e('Type of journal'),
            'kpi_data' => _e('yes in kpi data'),
            'sdg' => _e('SDG data'),
            'kavrtili' => _e('Kavrtili data'),
            'type' => _e('type of the article (1 = milliy oak, 2 = xalqaro jurnali (scopus va wosdan tashqari) chop etilgani, 3 = Scopus va wos-à Ilmiy boshqarmani o’zi kiritadi)'),
            'file' => _e('File'),
            'link' => _e('link'),
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

    public function fields()
    {
        $fields =  [
            'id',
            'name',
            'abstract',
            'key_words',
            'co_authors',
            'journal_name',
            'journal_country',
            'user_id',
            'scientific_specialization_id',
            'specialization',
            'language_id',
            'issn',
            'doi',
            'date',
            'journal_type',
            'kpi_data',
            'sdg',
            'kavrtili',
            'type',
            'file',
            'link',
            'order',
            'status',
            // 'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            // 'archived',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'scientificSpecialization',
            'user',
            'language',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Language]]. 
     * 
     * @return \yii\db\ActiveQuery|LanguageQuery 
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }


    /**
     * Gets query for [[ScientificSpecialization]].
     *
     * @return \yii\db\ActiveQuery|ScientificSpecializationQuery
     */
    public function getScientificSpecialization()
    {
        return $this->hasOne(ScientificSpecialization::className(), ['id' => 'scientific_specialization_id']);
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


    /**
     * ScientificArticle createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if user_id isset on $post model user_id 
        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
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

    /**
     * ScientificArticle updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

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
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function categoryFields()
    {
        return [
            self::TYPE_SCOPUS_WOS => [
                'name',
                'abstract',
                'key_words',
                'co_authors',
                'journal_name',
                'journal_country',
                'scientific_specialization_id',
                'sdg',
                'kavrtili',
                'type',


            ],

            self::TYPE_NATIONAL => [
                'name',
                'abstract',
                'key_words',
                'co_authors',
                'journal_name',
                'journal_country',
                'scientific_specialization_id',
                'specialization',
                'language',
                'issn',
                'doi',
                'date',
                'journal_type',
                'kpi_data',
                'type'
            ],
            self::TYPE_INTERNATIONAL => [
                'name',
                'abstract',
                'key_words',
                'co_authors',
                'journal_name',
                'journal_country',
                'scientific_specialization_id',
                'specialization',
                'language',
                'issn',
                'doi',
                'date',
                'journal_type',
                'kpi_data',
                'type'
            ],

        ];
    }

    public static function types()
    {
        return
            [
                self::TYPE_NATIONAL => _e("National journal"),
                self::TYPE_INTERNATIONAL => _e("International journal"),
                self::TYPE_SCOPUS_WOS => _e("Scopus and WOS journal"),
            ];
    }

    public function getExtra()
    {
        return self::extra();
    }

    public static function extra()
    {
        return ["fields" => self::categoryFields(), "types" => self::types()];
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
