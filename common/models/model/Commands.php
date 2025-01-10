<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "edu_year".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan[] $eduPlans
 * @property EduSemestr[] $eduSemestrs
 * @property TimeTable1[] $timeTables
 */
class Commands extends \yii\db\ActiveRecord
{

    public static $selected_language = 'uz';
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const UPLOADS_FOLDER = 'uploads/commands/';

    public $upload;
    public $fileMaxSize = 1024 * 1024 * 10; // 10 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'commands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'faculty_id',
                    'commands_type_id',
                    'commands_date',
                    'commands_number',
                ]
            ],
            [['commands_number','commands_file','commands_name'],'string' , 'max' => 255],
            [['commands_date'] , 'date'],
            [['description','commands_target','commands_summary'] , 'safe'],
            [[
                'faculty_id',
                'commands_type_id',
                'order',
                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted'
            ], 'integer'],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['commands_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommandsType::className(), 'targetAttribute' => ['commands_type_id' => 'id']],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => $this->fileMaxSize],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
            'commands_name',
            'faculty_id',
            'commands_type_id',

            'commands_number',
            'commands_file',
            'commands_date',
            'description',
            'commands_target',
            'commands_summary',

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
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $commands = Commands::findOne([
            'faculty_id' => $model->faculty_id,
            'commands_type_id' => $model->commands_type_id,
            'is_deleted' => 0
        ]);
        if ($commands != null) {
            $errors[] = ['commands_type_id' => _e('This command has been added before for this faculty!')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('upload');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $upload_FileUrl = $model->upload($model->upload);
            if ($upload_FileUrl) {
                $model->commands_file = $upload_FileUrl;
            } else {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $model->save();
        if (count($errors) == 0) {
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $commands = Commands::find()
            ->where([
                'faculty_id' => $model->faculty_id,
                'commands_type_id' => $model->commands_type_id,
                'is_deleted' => 0
            ])
            ->andWhere(['!=' , 'id' , $model->id])
            ->one();
        if ($commands != null) {
            $errors[] = ['commands_type_id' => _e('This command has been added before for this faculty!')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('upload');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $upload_FileUrl = $model->upload($model->upload);
            if ($upload_FileUrl) {
                $model->commands_file = $upload_FileUrl;
            } else {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $model->save();
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
}
