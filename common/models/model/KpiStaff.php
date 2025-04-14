<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%kpi_staff}}".
 *
 * @property int $id
 * @property int $user_access_id
 * @property int|null $user_id
 * @property int|null $job_title_id
 * @property int|null $user_access_type_id
 * @property int|null $table_id
 * @property int|null $work_rate_id
 * @property int|null $work_type
 * @property int|null $edu_year_id
 * @property int|null $in_doc_all
 * @property int|null $in_doc_on_time
 * @property int|null $in_doc_after_time
 * @property int|null $in_doc_not_done
 * @property float|null $in_doc_ball
 * @property float|null $in_doc_percent
 * @property float|null $in_doc_collected_ball
 * @property int|null $ex_doc_all
 * @property int|null $ex_doc_on_time
 * @property int|null $ex_doc_after_time
 * @property int|null $ex_doc_not_done
 * @property float|null $ex_doc_ball
 * @property float|null $ex_doc_percent
 * @property float|null $ex_doc_collected_ball
 * @property float|null $ball_dep_lead
 * @property string|null $file_dep_lead
 * @property float|null $ball_rector
 * @property float|null $ball_commission
 * @property float|null $ball_all
 * @property int|null $kpi
 * @property int|null $order
 * @property int|null $status
 * @property int|null $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $archived
 *
 * @property EduYear $eduYear
 * @property UserAccess $userAccess
 */
class KpiStaff extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const UPLOADS_FOLDER = 'uploads/kpi_staff/';
    public $file;
    public $upload_plan_file;
    public $upload_work_file;
    public $fileMaxSize = 1024 * 1024 * 300; // 3 Mb
    public $max_ball_dep_lead = 30;
    public $max_ball_rector = 10;
    public $max_ball_commission = 100;
    public $max_ball_all = 100;
    public $max_kpi = 300;

    public $in_max_ball = 35;
    public $ex_max_ball = 25;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%kpi_staff}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_access_id'], 'required'],
            [[
                'user_access_id',
                'user_id',
                'job_title_id',
                'user_access_type_id',
                'table_id',
                'work_rate_id',
                'work_type',
                'edu_year_id',
                'in_doc_all',
                'in_doc_on_time',
                'in_doc_after_time',
                'in_doc_not_done',
                'ex_doc_all',
                'ex_doc_on_time',
                'ex_doc_after_time',
                'ex_doc_not_done',
                'order',
                'status',
                'is_deleted',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
                'archived'
            ], 'integer'],
            [
                [
                    'in_doc_ball',
                    'in_doc_percent',
                    'in_doc_collected_ball',
                    'ex_doc_ball',
                    'ex_doc_percent',
                    'ex_doc_collected_ball',
                ],
                'number'
            ],
            [['file_dep_lead'], 'string', 'max' => 255],
            [['plan_file', 'work_file'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_access_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccessType::className(), 'targetAttribute' => ['user_access_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['user_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccess::className(), 'targetAttribute' => ['user_access_id' => 'id']],

            [['ball_dep_lead', 'ball_rector', 'ball_commission', 'ball_all'], 'double', 'min' => 0],
            [['ball_dep_lead'], 'double', 'max' => $this->max_ball_dep_lead],
            [['ball_rector'], 'double', 'max' => $this->max_ball_rector],
            [['ball_commission'], 'double', 'max' => $this->max_ball_commission],
            [['ball_all'], 'double', 'max' => $this->max_ball_all],
            ['kpi', 'double', 'min' => 0, 'max' => $this->max_kpi],
            // [['file_dep_lead'], 'string'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'rar,zip,pdf,png,jpg', 'maxSize' => $this->fileMaxSize],
            [['upload_plan_file', 'upload_work_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'rar,zip,pdf,png,jpg', 'maxSize' => $this->fileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_access_id' => _e('User Access ID'),
            'user_id' => _e('User ID'),
            'job_title_id' => _e('Job Title ID'),
            'user_access_type_id' => _e('User Access Type ID'),
            'table_id' => _e('Table ID'),
            'work_rate_id' => _e('Work Rate ID'),
            'work_type' => _e('Work Type'),
            'edu_year_id' => _e('Edu Year ID'),
            'in_doc_all' => _e('In Doc All'),
            'in_doc_on_time' => _e('In Doc On Time'),
            'in_doc_after_time' => _e('In Doc After Time'),
            'in_doc_not_done' => _e('In Doc Not Done'),
            'in_doc_ball' => _e('In Doc Ball'),
            'in_doc_percent' => _e('In Doc Percent'),
            'in_doc_collected_ball' => _e('In Doc Collected Ball'),
            'ex_doc_all' => _e('Ex Doc All'),
            'ex_doc_on_time' => _e('Ex Doc On Time'),
            'ex_doc_after_time' => _e('Ex Doc After Time'),
            'ex_doc_not_done' => _e('Ex Doc Not Done'),
            'ex_doc_ball' => _e('Ex Doc Ball'),
            'ex_doc_percent' => _e('Ex Doc Percent'),
            'ex_doc_collected_ball' => _e('Ex Doc Collected Ball'),
            'ball_dep_lead' => _e('Ball Dep Lead'),
            'file_dep_lead' => _e('File Dep Lead'),
            'ball_rector' => _e('Ball Rector'),
            'ball_commission' => _e('Ball Commission'),
            'ball_all' => _e('Ball All'),
            'kpi' => _e('Kpi'),
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
        $fields = [
            'id',
            'user_access_id',
            'user_id',
            'job_title_id',
            'user_access_type_id',
            'table_id',
            'work_rate_id',
            'work_type',
            'edu_year_id',
            'in_doc_all',
            'in_doc_on_time',
            'in_doc_after_time',
            'in_doc_not_done',
            'in_doc_ball',
            'in_doc_percent',
            'in_doc_collected_ball',
            'ex_doc_all',
            'ex_doc_on_time',
            'ex_doc_after_time',
            'ex_doc_not_done',
            'ex_doc_ball',
            'ex_doc_percent',
            'ex_doc_collected_ball',
            'ball_dep_lead',
            'file_dep_lead',
            'plan_file',
            'work_file',
            'ball_rector',
            'ball_commission',
            'ball_all',
            'kpi',
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
        $extraFields = [
            'eduYear',
            'userAccess',
            'profile',
            'logs',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[UserAccess]].
     *
     * @return \yii\db\ActiveQuery|UserAccessQuery
     */
    public function getUserAccess()
    {
        return $this->hasOne(UserAccess::className(), ['id' => 'user_access_id']);
    }


    public function getLogs()
    {
        return $this->hasMany(KpiStaffLog::className(), ['kpi_staff_id' => 'id']);
    }


    public static function createItemMonitoring($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Check if the 'user_access_id' key is set in the $post array
        if (!isset($post['user_access_id'])) {
            // If it's not set, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        } else {
            $model->user_access_id = $post['user_access_id'];
        }

        if (!isset($post['edu_year_id'])) {
            // Retrieve the latest active EduYear model
            $eduYear = EduYear::findOne(['status' => 1, 'is_deleted' => 0]);

            // Set the 'edu_year_id' property of the $model to the 'id' of the $eduYear model, if it exists. If $eduYear is null, set 'edu_year_id' to null.
            $model->edu_year_id = $eduYear ? $eduYear->id : null;
        } else {
            $model->edu_year_id = $post['edu_year_id'];
        }

        // Set the 'user_id', 'work_rate_id', 'job_title_id', 'user_access_type_id', 'table_id', and 'work_type' properties of the $model based on the corresponding properties of the $model->userAccess model
        $model->user_id = $model->userAccess->user_id;
        $model->work_rate_id = $model->userAccess->work_rate_id;
        $model->job_title_id = $model->userAccess->job_title_id;
        $model->user_access_type_id = $model->userAccess->user_access_type_id;
        $model->table_id = $model->userAccess->table_id;
        $model->work_type = $model->userAccess->work_type;

        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['in_doc_on_time'])) {
            $model->in_doc_on_time = $post['in_doc_on_time'];
        }
        if (isset($post['in_doc_after_time'])) {
            $model->in_doc_after_time = $post['in_doc_after_time'];
        }
        if (isset($post['in_doc_not_done'])) {
            $model->in_doc_not_done = $post['in_doc_not_done'];
        }

        // Set the 'ex_doc_on_time', 'ex_doc_after_time', and 'ex_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ex_doc_on_time'])) {
            $model->ex_doc_on_time = $post['ex_doc_on_time'];
        }
        if (isset($post['ex_doc_after_time'])) {
            $model->ex_doc_after_time = $post['ex_doc_after_time'];
        }
        if (isset($post['ex_doc_not_done'])) {
            $model->ex_doc_not_done = $post['ex_doc_not_done'];
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItemMonitoring($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // dd("asdasdaaa11");
        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['in_doc_on_time'])) {
            $model->in_doc_on_time = $post['in_doc_on_time'];
        }
        if (isset($post['in_doc_after_time'])) {
            $model->in_doc_after_time = $post['in_doc_after_time'];
        }
        if (isset($post['in_doc_not_done'])) {
            $model->in_doc_not_done = $post['in_doc_not_done'];
        }

        // Set the 'ex_doc_on_time', 'ex_doc_after_time', and 'ex_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ex_doc_on_time'])) {
            $model->ex_doc_on_time = $post['ex_doc_on_time'];
        }
        if (isset($post['ex_doc_after_time'])) {
            $model->ex_doc_after_time = $post['ex_doc_after_time'];
        }
        if (isset($post['ex_doc_not_done'])) {
            $model->ex_doc_not_done = $post['ex_doc_not_done'];
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function createItemComission($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Check if the 'user_access_id' key is set in the $post array
        if (!isset($post['user_access_id'])) {
            // If it's not set, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        } else {
            $model->user_access_id = $post['user_access_id'];
        }

        if (!isset($post['edu_year_id'])) {
            // Retrieve the latest active EduYear model
            $eduYear = EduYear::findOne(['status' => 1, 'is_deleted' => 0]);

            // Set the 'edu_year_id' property of the $model to the 'id' of the $eduYear model, if it exists. If $eduYear is null, set 'edu_year_id' to null.
            $model->edu_year_id = $eduYear ? $eduYear->id : null;
        } else {
            $model->edu_year_id = $post['edu_year_id'];
        }

        // Set the 'user_id', 'work_rate_id', 'job_title_id', 'user_access_type_id', 'table_id', and 'work_type' properties of the $model based on the corresponding properties of the $model->userAccess model
        $model->user_id = $model->userAccess->user_id;
        $model->work_rate_id = $model->userAccess->work_rate_id;
        $model->job_title_id = $model->userAccess->job_title_id;
        $model->user_access_type_id = $model->userAccess->user_access_type_id;
        $model->table_id = $model->userAccess->table_id;
        $model->work_type = $model->userAccess->work_type;

        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ball_commission'])) {
            $model->ball_commission = $post['ball_commission'];
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItemComission($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ball_commission'])) {
            $model->ball_commission = $post['ball_commission'];
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function createItemRector($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Check if the 'user_access_id' key is set in the $post array
        if (!isset($post['user_access_id'])) {
            // If it's not set, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        } else {
            $model->user_access_id = $post['user_access_id'];
        }

        if (!isset($post['edu_year_id'])) {
            // Retrieve the latest active EduYear model
            $eduYear = EduYear::findOne(['status' => 1, 'is_deleted' => 0]);

            // Set the 'edu_year_id' property of the $model to the 'id' of the $eduYear model, if it exists. If $eduYear is null, set 'edu_year_id' to null.
            $model->edu_year_id = $eduYear ? $eduYear->id : null;
        } else {
            $model->edu_year_id = $post['edu_year_id'];
        }

        // Set the 'user_id', 'work_rate_id', 'job_title_id', 'user_access_type_id', 'table_id', and 'work_type' properties of the $model based on the corresponding properties of the $model->userAccess model
        $model->user_id = $model->userAccess->user_id;
        $model->work_rate_id = $model->userAccess->work_rate_id;
        $model->job_title_id = $model->userAccess->job_title_id;
        $model->user_access_type_id = $model->userAccess->user_access_type_id;
        $model->table_id = $model->userAccess->table_id;
        $model->work_type = $model->userAccess->work_type;

        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ball_rector'])) {
            $model->ball_rector = $post['ball_rector'];
        }

        $model->kpi = $model->ball_all * 2 + $model->ball_rector;
        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItemRector($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ball_rector'])) {
            $model->ball_rector = $post['ball_rector'];
        }

        $model->kpi = $model->ball_all * 2 + $model->ball_rector;

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItemDepLead($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Check if the 'user_access_id' key is set in the $post array
        if (!isset($post['user_access_id'])) {
            // If it's not set, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        } else {
            $model->user_access_id = $post['user_access_id'];
        }

        if (!isset($post['edu_year_id'])) {
            // Retrieve the latest active EduYear model
            $eduYear = EduYear::findOne(['status' => 1, 'is_deleted' => 0]);

            // Set the 'edu_year_id' property of the $model to the 'id' of the $eduYear model, if it exists. If $eduYear is null, set 'edu_year_id' to null.
            $model->edu_year_id = $eduYear ? $eduYear->id : null;
        } else {
            $model->edu_year_id = $post['edu_year_id'];
        }


        // Set the 'user_id', 'work_rate_id', 'job_title_id', 'user_access_type_id', 'table_id', and 'work_type' properties of the $model based on the corresponding properties of the $model->userAccess model
        $model->user_id = $model->userAccess->user_id;
        $model->work_rate_id = $model->userAccess->work_rate_id;
        $model->job_title_id = $model->userAccess->job_title_id;
        $model->user_access_type_id = $model->userAccess->user_access_type_id;
        $model->table_id = $model->userAccess->table_id;
        $model->work_type = $model->userAccess->work_type;

        //        ball_dep_lead
        //        file_dep_lead


        // user_id
        if ($model->user_id == current_user_id()) {
            $errors[] = _e("You can not evaluate your own KPI");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ball_dep_lead'])) {
            $model->ball_dep_lead = $post['ball_dep_lead'];
        }

        $model->file = UploadedFile::getInstancesByName('file');
        if ($model->file) {
            $model->file = $model->file[0];
            $depFileUrl = $model->uploadFile();
            if ($depFileUrl) {
                $model->file_dep_lead = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_plan_file = UploadedFile::getInstancesByName('upload_plan_file');
        if ($model->upload_plan_file) {
            $model->upload_plan_file = $model->upload_plan_file[0];
            $depFileUrl = $model->uploadFileupload_plan_file($model->upload_plan_file);
            if ($depFileUrl) {
                $model->plan_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_work_file = UploadedFile::getInstancesByName('upload_work_file');
        if ($model->upload_work_file) {
            $model->upload_work_file = $model->upload_work_file[0];
            $depFileUrl = $model->uploadFileupload_work_file($model->upload_work_file);
            if ($depFileUrl) {
                $model->work_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItemDepLead($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];
        //        ball_dep_lead
        //        file_dep_lead
        // Set the 'in_doc_on_time', 'in_doc_after_time', and 'in_doc_not_done' properties of the $model based on the corresponding keys in the $post array
        if (isset($post['ball_dep_lead'])) {
            $model->ball_dep_lead = $post['ball_dep_lead'];
        }

        $model->file = UploadedFile::getInstancesByName('file');
        if ($model->file) {
            $model->file = $model->file[0];
            $depFileUrl = $model->uploadFile();
            if ($depFileUrl) {
                $model->file_dep_lead = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_plan_file = UploadedFile::getInstancesByName('upload_plan_file');
        if ($model->upload_plan_file) {
            $model->upload_plan_file = $model->upload_plan_file[0];
            $depFileUrl = $model->uploadFileupload_plan_file($model->upload_plan_file);
            if ($depFileUrl) {
                $model->plan_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_work_file = UploadedFile::getInstancesByName('upload_work_file');
        if ($model->upload_work_file) {
            $model->upload_work_file = $model->upload_work_file[0];
            $depFileUrl = $model->uploadFileupload_work_file($model->upload_work_file);
            if ($depFileUrl) {
                $model->work_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->user_id == current_user_id()) {
            $errors[] = _e("You can not evaluate your own KPI");
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItemSelf($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        // Check if the 'user_access_id' key is set in the $post array
        if (!isset($post['user_access_id'])) {
            // If it's not set, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        } else {
            $model->user_access_id = $post['user_access_id'];
        }

        if (!isset($post['edu_year_id'])) {
            // Retrieve the latest active EduYear model
            $eduYear = EduYear::findOne(['status' => 1, 'is_deleted' => 0]);

            // Set the 'edu_year_id' property of the $model to the 'id' of the $eduYear model, if it exists. If $eduYear is null, set 'edu_year_id' to null.
            $model->edu_year_id = $eduYear ? $eduYear->id : null;
        } else {
            if ($post['edu_year_id'] != 70) {
                $errors[] = _e('Permission only 2024 - 2025 - 2 for the period');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            $model->edu_year_id = $post['edu_year_id'];
        }

        if ($model->userAccess->user_id != current_user_id()) {
            $errors[] = _e('This is not yourself');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Set the 'user_id', 'work_rate_id', 'job_title_id', 'user_access_type_id', 'table_id', and 'work_type' properties of the $model based on the corresponding properties of the $model->userAccess model
        $model->user_id = $model->userAccess->user_id;
        $model->work_rate_id = $model->userAccess->work_rate_id;
        $model->job_title_id = $model->userAccess->job_title_id;
        $model->user_access_type_id = $model->userAccess->user_access_type_id;
        $model->table_id = $model->userAccess->table_id;
        $model->work_type = $model->userAccess->work_type;


        $model->upload_plan_file = UploadedFile::getInstancesByName('upload_plan_file');
        if ($model->upload_plan_file) {
            $model->upload_plan_file = $model->upload_plan_file[0];
            $depFileUrl = $model->uploadFileupload_plan_file($model->upload_plan_file);
            if ($depFileUrl) {
                $model->plan_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_work_file = UploadedFile::getInstancesByName('upload_work_file');
        if ($model->upload_work_file) {
            $model->upload_work_file = $model->upload_work_file[0];
            $depFileUrl = $model->uploadFileupload_work_file($model->upload_work_file);
            if ($depFileUrl) {
                $model->work_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItemSelf($model, $post)
    {
        // Start a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Initialize an empty array to store any errors
        $errors = [];

        if ($model->userAccess->user_id != current_user_id()) {
            $errors[] = _e('This is not yourself');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload_plan_file = UploadedFile::getInstancesByName('upload_plan_file');
        if ($model->upload_plan_file) {
            $model->upload_plan_file = $model->upload_plan_file[0];
            $depFileUrl = $model->uploadFileupload_plan_file($model->upload_plan_file);
            if ($depFileUrl) {
                $model->plan_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->upload_work_file = UploadedFile::getInstancesByName('upload_work_file');
        if ($model->upload_work_file) {
            $model->upload_work_file = $model->upload_work_file[0];
            $depFileUrl = $model->uploadFileupload_work_file($model->upload_work_file);
            if ($depFileUrl) {
                $model->work_file = $depFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        // Validate the $model
        if (!($model->validate())) {
            // If the $model fails validation, add the validation errors to the $errors array and rollback the transaction
            $errors[] = $model->errors;
            $transaction->rollBack();
            // Return the simplified errors array
            return simplify_errors($errors);
        }

        // Save the $model
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
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

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * KpiStaff updateItem <$model, $post>
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
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function find()
    {
        return parent::find()->where(['is_deleted' => 0, 'archived' => 0]);
    }


    /**
     * After save item, create log
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Create log
        $kpiStaffLog = new KpiStaffLog();
        $kpiStaffLog->setAttributes($this->getAttributes());
        $kpiStaffLog->kpi_staff_id = $this->id;
        $kpiStaffLog->save(false);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }

        // Calculate in_doc_percent and in_doc_collected_ball

        $this->in_doc_all = $this->in_doc_on_time + $this->in_doc_after_time + $this->in_doc_not_done;
        $this->in_doc_ball = $this->in_doc_on_time * 1 + $this->in_doc_after_time * 0.5 + $this->in_doc_not_done * (-1);
        $this->in_doc_percent = round($this->in_doc_all != 0 ? $this->in_doc_ball / $this->in_doc_all * 100 : 0, 2);
        $this->in_doc_collected_ball = $this->in_doc_percent * $this->in_max_ball / 100;

        // Calculate ex_doc_percent and ex_doc_collected_ball
        $this->ex_doc_all = $this->ex_doc_on_time + $this->ex_doc_after_time + $this->ex_doc_not_done;
        $this->ex_doc_ball = $this->ex_doc_on_time * 1 + $this->ex_doc_after_time * 0.5 + $this->ex_doc_not_done * (-1);
        $this->ex_doc_percent = round($this->ex_doc_all != 0 ? $this->ex_doc_ball / $this->ex_doc_all * 100 : 0, 2);
        $this->ex_doc_collected_ball = $this->ex_doc_percent * $this->ex_max_ball / 100;
        // $this->ex_doc_percent  = 30;

        $this->ball_all = round(($this->in_doc_collected_ball + $this->ex_doc_collected_ball
            + $this->ball_dep_lead + $this->ball_rector), 2);

        $this->ball_all < 0 ? $this->ball_all = 0 : $this->ball_all = $this->ball_all;
        $this->ball_all =  round($this->ball_all, 2);
        $this->kpi = round(($this->ball_all * 2 + $this->ball_commission), 0);

        return parent::beforeSave($insert);
    }

    // public function beforeSaveAs($insert)
    // {
    //     if ($insert) {
    //         $this->created_by = Current_user_id();
    //     } else {
    //         $this->updated_by = Current_user_id();
    //     }
    //     $this->in_doc_all = $this->in_doc_on_time + $this->in_doc_after_time + $this->in_doc_not_done;
    //     $this->in_doc_ball = $this->in_doc_on_time * 1 + $this->in_doc_after_time * 0.5 + $this->in_doc_not_done * (-1);
    //     $this->in_doc_percent = $this->in_doc_ball / $this->in_doc_all;
    //     $this->in_doc_collected_ball = $this->in_doc_percent * $this->in_max_ball;

    //     $this->ex_doc_all = $this->ex_doc_on_time + $this->ex_doc_after_time + $this->ex_doc_not_done;
    //     $this->ex_doc_ball = $this->ex_doc_on_time * 1 + $this->ex_doc_after_time * 0.5 + $this->ex_doc_not_done * (-1);
    //     $this->ex_doc_percent = $this->ex_doc_ball / $this->ex_doc_all * 100;
    //     $this->ex_doc_collected_ball = $this->ex_doc_percent * $this->ex_max_ball / 100;


    //     return parent::beforeSave($insert);
    // }

    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->user_id . "_" . time() . '.' . $this->file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }
    public function uploadFileupload_work_file()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->user_id . "_" . time() . '.' . $this->upload_work_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->upload_work_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }


    public function uploadFileupload_plan_file()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->user_id . "_" . time() . '.' . $this->upload_plan_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->upload_plan_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }
}
