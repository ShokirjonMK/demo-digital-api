<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class KpiData extends \yii\db\ActiveRecord
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

    const UPLOADS_FOLDER = 'uploads/kpi_data/';
    public $kpi_file1;
    public $kpi_file2;
    public $kpi_file3;
    public $kpi_file4;
    public $kpiFileMaxSize = 1024 * 1024 * 100; // 100 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        // Check if user wants archived data (e.g., via a parameter or a flag)
        if (Yii::$app->request->get('archived')) {
            return 'kpi_data_archived';
        }

        // if (current_user_id() == 8123 || current_user_id() == 2896) {
        //     return 'kpi_data_archived';
        // }

        return 'kpi_data'; // original table
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'kpi_category_id',
                    'user_id',
                ],
                'required'
            ],
            [
                [
                    'kpi_category_id',
                    'count',
                    'subject_category_id',
                    'event_type',
                    'event_form',
                    'count_of_copyright',
                    'archived',
                    'user_id',
                    'quarter',
                    'type',

                ],
                'integer'
            ],
            [
                [
                    'date',
                    'start_date',
                    'end_date',
                ],
                'date',
                'format' => 'php:Y-m-d'
            ],
            [
                [
                    'link',
                    'link2',
                    'text',
                ],
                'string'
            ],
            [
                [
                    'ball',
                ],
                'double'
            ],
            [
                [
                    'file1',
                    'file2',
                    'file3',
                    'file4',

                    'number',
                    'level',
                    'name',
                    'name1',
                    'name2',
                    'name3',
                    'authors',
                    'a1',
                    'a2',
                    'a3',
                    'a4',
                    'isbn',
                ],
                'string',
                'max' => 255
            ],

            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status'], 'default', 'value' => 1],

            [['kpi_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => KpiCategory::className(), 'targetAttribute' => ['kpi_category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],

            [['file1'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,mp3,ogg,dsd,aac,alac,wma,flac,mp4,mov,wmv,flv,avi,mkv', 'maxSize' => $this->kpiFileMaxSize],
            [['file2'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,mp3,ogg,dsd,aac,alac,wma,flac,mp4,mov,wmv,flv,avi,mkv', 'maxSize' => $this->kpiFileMaxSize],
            [['file3'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,mp3,ogg,dsd,aac,alac,wma,flac,mp4,mov,wmv,flv,avi,mkv', 'maxSize' => $this->kpiFileMaxSize],
            [['file4'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg,mp3,ogg,dsd,aac,alac,wma,flac,mp4,mov,wmv,flv,avi,mkv', 'maxSize' => $this->kpiFileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'type',

            'kpi_category_id' => _e('kpi_category_id'),
            'date' => _e('date'),
            'file1' => _e('file'),
            'file2' => _e('file2'),
            'file3' => _e('file3'),
            'file4' => _e('file4'),

            'start_date' => _e('start_date'),
            'end_date' => _e('end_date'),
            'link' => _e('link'),
            'link2' => _e('link2'),
            'ball' => _e('ball'),
            'count' => _e('count'),
            'subject_category_id' => _e('subject_category_id'),
            'event_type' => _e('event_type'),
            'event_form' => _e('event_form'),
            'number' => _e('number'),
            'level' => _e('level'),
            'name' => _e('name'),
            'name1' => _e('name1'),
            'name2' => _e('name2'),
            'name3' => _e('name3'),
            'authors' => _e('authors'),
            'count_of_copyright' => _e('count_of_copyright'),

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
            'type',
            'kpi_category_id',
            'date',
            'file1',
            'file2',
            'file3',
            'file4',
            'start_date',
            'end_date',
            'link',
            'link2',
            'ball',
            'count',
            'subject_category_id',
            'event_type',
            'event_form',
            'number',
            'level',
            'name',
            'name1',
            'name2',
            'name3',
            'text',
            'authors',
            'count_of_copyright',

            'user_id',
            'isbn',


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

            'user',
            'kpiCategory',
            'subjectCategory',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getKpiCategory()
    {
        return $this->hasOne(KpiCategory::className(), ['id' => 'kpi_category_id']);
    }
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $model->user_id = current_user_id();
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->type = $model->kpiCategory->type ?? 1;

        if ($model->save()) {
            // kpi file saqlaymiz
            $model->kpi_file1 = UploadedFile::getInstancesByName('kpi_file1');
            if ($model->kpi_file1) {
                $model->kpi_file1 = $model->kpi_file1[0];
                $kpiFileUrl1 = $model->uploadFile($model->kpi_file1);
                if ($kpiFileUrl1) {
                    $model->file1 = $kpiFileUrl1;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

            // kpi file saqlaymiz
            $model->kpi_file2 = UploadedFile::getInstancesByName('kpi_file2');
            if ($model->kpi_file2) {
                $model->kpi_file2 = $model->kpi_file2[0];
                $kpiFileUrl2 = $model->uploadFile($model->kpi_file2);
                if ($kpiFileUrl2) {
                    $model->file2 = $kpiFileUrl2;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

            // kpi file saqlaymiz
            $model->kpi_file3 = UploadedFile::getInstancesByName('kpi_file3');
            if ($model->kpi_file3) {
                $model->kpi_file3 = $model->kpi_file3[0];
                $kpiFileUrl3 = $model->uploadFile($model->kpi_file3);
                if ($kpiFileUrl3) {
                    $model->file3 = $kpiFileUrl3;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

            // kpi file saqlaymiz
            $model->kpi_file4 = UploadedFile::getInstancesByName('kpi_file4');
            if ($model->kpi_file4) {
                $model->kpi_file4 = $model->kpi_file4[0];
                $kpiFileUrl4 = $model->uploadFile($model->kpi_file4);
                if ($kpiFileUrl4) {
                    $model->file4 = $kpiFileUrl4;
                } else {
                    $errors[] = $model->errors;
                }
            }
            // ***

        }
        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        // kpi file saqlaymiz
        $model->kpi_file1 = UploadedFile::getInstancesByName('kpi_file1');
        if ($model->kpi_file1) {
            $model->kpi_file1 = $model->kpi_file1[0];
            $kpiFileUrl = $model->uploadFile($model->kpi_file1);
            if ($kpiFileUrl) {
                $model->file1 = $kpiFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        // kpi file saqlaymiz
        $model->kpi_file2 = UploadedFile::getInstancesByName('kpi_file2');
        if ($model->kpi_file2) {
            $model->kpi_file2 = $model->kpi_file2[0];
            $kpiFileUrl = $model->uploadFile($model->kpi_file2);
            if ($kpiFileUrl) {
                $model->file2 = $kpiFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        // kpi file saqlaymiz
        $model->kpi_file3 = UploadedFile::getInstancesByName('kpi_file3');
        if ($model->kpi_file3) {
            $model->kpi_file3 = $model->kpi_file3[0];
            $kpiFileUrl = $model->uploadFile($model->kpi_file3);
            if ($kpiFileUrl) {
                $model->file3 = $kpiFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        // kpi file saqlaymiz
        $model->kpi_file4 = UploadedFile::getInstancesByName('kpi_file4');
        if ($model->kpi_file4) {
            $model->kpi_file4 = $model->kpi_file4[0];
            $kpiFileUrl = $model->uploadFile($model->kpi_file4);
            if ($kpiFileUrl) {
                $model->file4 = $kpiFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        if (!isset($post['user_id'])) {
            if (!isRole('admin')) {
                $model->user_id = current_user_id();
            }
        }

        if (!$model->save()) {
            $errors[] = $model->getErrorSummary(true);
        }
        if (count($errors) == 0) {
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


    public static function varTypes()
    {
        $hujjat_turi = [
            1 => _e('Sertifikat'),
            2 => _e('Diplom'),
            3 => _e('Guvohnoma'),
            4 => _e('Dastur'),
            5 => _e('Boshqa'),
        ];

        $tadbir_turi = [
            1 => _e('Konferensiya'),
            2 => _e('Seminar'),
            3 => _e('Taʼlim loyihalari'),
        ];

        $tadbirda_ishtirok_etish_shakli = [
            1 => _e('Moderator'),
            2 => _e('Maʼruzachi'),
        ];

        $sport_tadbir_shakli = [
            1 => _e('Olimpiada'),
            2 => _e('Musobaqa'),
            3 => _e('Boshqa'),
        ];

        $ilmiy_tadbir_shakli = [
            1 => _e('Oʻquv kontentlarni tayyorlashda metodik yordam'),
            2 => _e('master-klass darslari yoki seminar-treninglar oʻtkazish'),
        ];

        $ishtirok_etgan_loyiha_yoki_tadbir_turi = [
            1 => _e('Toʻgarak'),
            2 => _e('Ilmiy maktab'),
            3 => _e('Klublar'),
            4 => _e('Yuridik klinika'),
            5 => _e('Boshqa'),
        ];

        $darslik_qollanma = [
            1 => _e('Darslik'),
            2 => _e('Oʻquv qoʻllanma')
        ];

        $jurnal_turi = [
            1 => _e('Mahalliy jurnal'),
            2 => _e('Xorijiy jurnal'),
        ];

        $imtihon_turi = [
            1 => _e('Makalaviy imtihon 2 yillik'),
            2 => _e('Kirsh imtihoni 1 yilik'),
            3 => _e('Monitoring 1 yillik'),
        ];

        $til_turi = [
            1 => _e('O‘zbek tili'),
            2 => _e('Rus tili'),
            3 => _e('Xorijiy til'),
        ];


        return [
            $hujjat_turi,
            $tadbir_turi,
            $tadbirda_ishtirok_etish_shakli,
            $sport_tadbir_shakli,
            $ilmiy_tadbir_shakli,
            $ishtirok_etgan_loyiha_yoki_tadbir_turi,
            $darslik_qollanma,
            $jurnal_turi,
            $imtihon_turi,
            $til_turi
        ];
    }

    public function uploadFile($thisIsFile)
    {
        if ($this->validate()) {
            $fileDir = self::UPLOADS_FOLDER . $this->user_id  . '/';
            if (!file_exists(STORAGE_PATH  . $fileDir)) {
                mkdir(STORAGE_PATH  . $fileDir, 0777, true);
            }

            $fileName = $this->kpi_category_id . "_" . time() . "_" . \Yii::$app->security->generateRandomString(8) . '.' . $thisIsFile->extension;

            $miniUrl = $fileDir . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $thisIsFile->saveAs($url, false);
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
}
