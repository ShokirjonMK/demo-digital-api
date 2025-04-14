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
 * @property int $kpi_staff_id
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
class KpiStaffLog extends \yii\db\ActiveRecord
{
    // use ResourceTrait;

    // public function behaviors()
    // {
    //     return [
    //         TimestampBehavior::class,
    //     ];
    // }


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
        return '{{%kpi_staff_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kpi_staff_id'], 'required'],
            [[
                'kpi_staff_id',
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
            'kpi_staff_id' => _e('kpi_staff_id'),
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
            'kpi_staff_id',
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
}
