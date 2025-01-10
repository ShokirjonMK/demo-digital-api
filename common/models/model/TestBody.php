<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Option;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Predis\Configuration\Options;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $test_id
 * @property string $file
 * @property string $text
 * @property int $type
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction[] $directions
 * @property EduPlan[] $eduPlans
 * @property Kafedra[] $kafedras
 */
class TestBody extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

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
        return 'test_body';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                ['test_id'] , 'required'
            ],
            [['text'] , 'safe'],
            [['file'] , 'string' , 'max' => 255],
            [['type', 'test_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
            [['file', 'text'], 'validateFileOrText'],
        ];
    }

    public function validateFileOrText($attribute, $params, $validator)
    {
        if (empty($this->file) && empty($this->text)) {
            $this->addError('file', _e('Either "file" or "text" must be provided.'));
            $this->addError('text', _e('Either "file" or "text" must be provided.'));
        }
    }


    public function fields()
    {
        $fields =  [
            'id',
            'test_id',

            'text',
            'file',
            'type',

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
            'test',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
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
