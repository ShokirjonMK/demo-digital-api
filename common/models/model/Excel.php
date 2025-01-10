<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $name
 * @property int $building_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Building $building
 * @property TimeTable1[] $timeTables
 */
class Excel extends \yii\db\ActiveRecord
{
    public $excel;
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'profile';
    }

    public function rules()
    {
        return [
            [['excel'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public static function createItem($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->excel = UploadedFile::getInstancesByName('excel');
        if ($model->excel) {
            $model->excel = $model->excel;
            $saveFile = $model->uploadExcel();
            if (!$saveFile) {
                $errors[] = ['errors' => _e('Error loading file')];
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function upload()
    {
        $fileName = 'excel' . '.' . $this->excel->extension;
        $url = \Yii::getAlias('@console/controllers/excels/'.$fileName);
        $this->excel->saveAs($url, false);
        return true;
    }

}
