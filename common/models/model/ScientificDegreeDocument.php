<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%scientific_degree_document}}".
 *
 * @property int $id
 * @property int|null $academic_degree_id
 * @property int|null $degree_id
 * @property string|null $name
 * @property int $scientific_specialization_id
 * @property string|null $council_number
 * @property string|null $council_name
 * @property string|null $protection_date
 * @property string|null $performed_organization
 * @property string|null $leader_info
 * @property string|null $independent
 * @property string|null $base
 * @property string|null $autoreferat_file
 * @property string|null $diploma_number
 * @property string|null $diploma_file
 * @property string|null $dissertation_file
 * @property string|null $attestat_raqami
 * @property string|null $organization_recommended
 * @property string|null $oak_order_date
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property AcademicDegree $academicDegree
 * @property Degree $degree
 * @property ScientificSpecialization $scientificSpecialization
 */
class ScientificDegreeDocument extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    // diploma_file
    // dissertation_file

    const UPLOADS_FOLDER = 'uploads/scientific_degree_document/';
    public $upload_diploma_file;
    public $upload_diploma_fileMaxSize = 1024 * 1024 * 5; // 5 Mb

    public $upload_dissertation_file;
    public $upload_dissertation_fileMaxSize = 1024 * 1024 * 5; // 5 Mb

    public $upload_autoreferat_file;
    public $upload_autoreferat_fileMaxSize = 1024 * 1024 * 5; // 5 Mb


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
        return '{{%scientific_degree_document}}';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['academic_degree_id', 'user_id', 'degree_id', 'scientific_specialization_id', 'order', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'archived'], 'integer'],
            [['name', 'council_name', 'leader_info'], 'string'],
            [['scientific_specialization_id'], 'required'],
            [['protection_date', 'oak_order_date'], 'safe'],
            [['protection_date', 'oak_order_date'], 'date', 'format' => 'php:Y-m-d'],
            [['council_number', 'performed_organization', 'independent', 'base', 'autoreferat_file', 'diploma_number', 'diploma_file', 'dissertation_file', 'attestat_raqami', 'organization_recommended'], 'string', 'max' => 255],
            [['academic_degree_id'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicDegree::className(), 'targetAttribute' => ['academic_degree_id' => 'id']],
            [['degree_id'], 'exist', 'skipOnError' => true, 'targetClass' => Degree::className(), 'targetAttribute' => ['degree_id' => 'id']],
            [['academic_degree_id', 'degree_id'], 'required', 'when' => function ($model) {
                return empty($model->academic_degree_id) && empty($model->degree_id);
            }, 'message' => 'One of Academic Degree or Degree must be selected.'],
            ['academic_degree_id', 'validateAcademicDegreeFields'],
            ['degree_id', 'validateDegreeFields'],
            [['upload_diploma_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,jpg,doc,docx', 'maxSize' => $this->upload_diploma_fileMaxSize],
            [['upload_dissertation_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,jpg,doc,docx', 'maxSize' => $this->upload_dissertation_fileMaxSize],
            [['upload_autoreferat_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,jpg,doc,docx', 'maxSize' => $this->upload_autoreferat_fileMaxSize],

        ];
    }

    public function validateAcademicDegreeFields($attribute, $params)
    {
        if (!empty($this->academic_degree_id)) {
            $academicDegree = AcademicDegree::findOne($this->academic_degree_id);
            if ($academicDegree && !empty($academicDegree->fields)) {
                $fields = $academicDegree->fields;
                if (!is_array($fields)) {
                    $fields = json_decode($fields, true);
                }
                foreach ($fields as $field) {
                    if (empty($this->$field)) {
                        $this->addError($field, _e("{field} cannot be blank.", ['field' => ucfirst(str_replace('_', ' ', $field))]));
                    }
                }
            }
        }
    }

    public function validateDegreeFields($attribute, $params)
    {
        if (!empty($this->degree_id)) {
            $degree = Degree::findOne($this->degree_id);
            if ($degree && !empty($degree->fields)) {
                $fields = $degree->fields;
                if (!is_array($fields)) {
                    $fields = json_decode($fields, true);
                }
                foreach ($fields as $field) {
                    if (empty($this->$field)) {
                        $this->addError($field, _e("{field} cannot be blank.", ['field' => ucfirst(str_replace('_', ' ', $field))]));
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'academic_degree_id' => _e('Academic Degree ID'),
            'degree_id' => _e('Degree ID'),
            'name' => _e('Name'),
            'scientific_specialization_id' => _e('Scientific Specialization ID'),
            'council_number' => _e('Council Number'),
            'council_name' => _e('Council Name'),
            'protection_date' => _e('Protection Date'),
            'performed_organization' => _e('Performed Organization'),
            'leader_info' => _e('Leader Info'),
            'independent' => _e('Independent'),
            'base' => _e('Base'),
            'autoreferat_file' => _e('Autoreferat File'),
            'diploma_number' => _e('Diploma Number'),
            'diploma_file' => _e('Diploma File'),
            'dissertation_file' => _e('Dissertation File'),
            'attestat_raqami' => _e('Attestat Raqami'),
            'organization_recommended' => _e('Organization Recommended'),
            'oak_order_date' => _e('Oak Order Date'),
            'user_id' => _e('User ID'),
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
            'academic_degree_id',
            'degree_id',
            'name',
            'scientific_specialization_id',
            'council_number',
            'council_name',
            'protection_date',
            'performed_organization',
            'leader_info',
            'independent',
            'base',
            'autoreferat_file',
            'diploma_number',
            'diploma_file',
            'dissertation_file',
            'attestat_raqami',
            'organization_recommended',
            'oak_order_date',
            'user_id',
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
            'academicDegree',
            'degree',
            'scientificSpecialization',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[AcademicDegree]].
     *
     * @return \yii\db\ActiveQuery|AcademicDegreeQuery
     */
    public function getAcademicDegree()
    {
        return $this->hasOne(AcademicDegree::className(), ['id' => 'academic_degree_id']);
    }

    /**
     * Gets query for [[Degree]].
     *
     * @return \yii\db\ActiveQuery|DegreeQuery
     */
    public function getDegree()
    {
        return $this->hasOne(Degree::className(), ['id' => 'degree_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|DegreeQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
     * ScientificDegreeDocument createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        if ($model->save()) {

            // $model->upload_diploma_file file saqlaymiz
            $model->upload_diploma_file = UploadedFile::getInstancesByName('upload_diploma_file');
            if ($model->upload_diploma_file) {
                $model->upload_diploma_file = $model->upload_diploma_file[0];
                $upload_diploma_fileFileUrl = $model->uploadFile($model->upload_diploma_file);
                if ($upload_diploma_fileFileUrl) {
                    $model->diploma_file = $upload_diploma_fileFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // $model->upload_dissertation_file file saqlaymiz
            $model->upload_dissertation_file = UploadedFile::getInstancesByName('upload_dissertation_file');
            if ($model->upload_dissertation_file) {
                $model->upload_dissertation_file = $model->upload_dissertation_file[0];
                $upload_dissertation_fileFileUrl = $model->uploadFile($model->upload_dissertation_file);
                if ($upload_dissertation_fileFileUrl) {
                    $model->dissertation_file = $upload_dissertation_fileFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // $model->upload_autoreferat_file file saqlaymiz
            $model->upload_autoreferat_file = UploadedFile::getInstancesByName('upload_autoreferat_file');
            if ($model->upload_autoreferat_file) {
                $model->upload_autoreferat_file = $model->upload_autoreferat_file[0];
                $upload_autoreferat_fileFileUrl = $model->uploadFile($model->upload_autoreferat_file);
                if ($upload_autoreferat_fileFileUrl) {
                    $model->autoreferat_file = $upload_autoreferat_fileFileUrl;
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
     * ScientificDegreeDocument updateItem <$model, $post>
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

        if ($model->save()) {

            // $model->upload_diploma_file file saqlaymiz
            $model->upload_diploma_file = UploadedFile::getInstancesByName('upload_diploma_file');
            if ($model->upload_diploma_file) {
                $model->upload_diploma_file = $model->upload_diploma_file[0];
                $model->upload_diploma_fileFileUrl = $model->uploadFile($model->upload_diploma_file);
                if ($model->upload_diploma_fileFileUrl) {
                    $model->dissertation_file = $model->upload_diploma_fileFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // $model->upload_dissertation_file file saqlaymiz
            $model->upload_dissertation_file = UploadedFile::getInstancesByName('upload_dissertation_file');
            if ($model->upload_dissertation_file) {
                $model->upload_dissertation_file = $model->upload_dissertation_file[0];
                $model->upload_dissertation_fileFileUrl = $model->uploadFile($model->upload_dissertation_file);
                if ($model->upload_dissertation_fileFileUrl) {
                    $model->dissertation_file = $model->upload_dissertation_fileFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // $model->upload_autoreferat_file file saqlaymiz
            $model->upload_autoreferat_file = UploadedFile::getInstancesByName('upload_autoreferat_file');
            if ($model->upload_autoreferat_file) {
                $model->upload_autoreferat_file = $model->upload_autoreferat_file[0];
                $model->upload_autoreferat_fileFileUrl = $model->uploadFile($model->upload_autoreferat_file);
                if ($model->upload_autoreferat_fileFileUrl) {
                    $model->autoreferat_file = $model->upload_autoreferat_fileFileUrl;
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
