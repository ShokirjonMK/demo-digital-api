<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "translate".
 *
 * @property int $id
 * @property string $name
 * @property string $table_name
 * @property int $language
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Languages $languages
 */
class Translate11 extends \yii\db\ActiveRecord
{

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * used table names:
     * 
     * building
     * room
     * direction
     * faculty
     * kafedra
     * edu_type
     * subject
     * subject_type
     * 
     */

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'translate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'model_id', 'table_name', 'language'], 'required'],
            [['model_id', 'language', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['name', 'table_name', 'description'], 'string', 'max' => 255],
            [['language'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'model id',
            'name' => 'Name',
            'table_name' => 'Table Name',
            'language' => 'Languages ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /**
     * Gets query for [[Languages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language']);
    }

    public function extraFields()
    {
        $extraFields =  [
            'languages',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
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


    public static function createTranslate($nameArr, $table_name, $model_id, $descArr = null)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        foreach ($nameArr as $key => $value) {
            $new_translate = new Translate();
            $new_translate->name = $value;
            $new_translate->table_name = $table_name;
            $new_translate->model_id = $model_id;
            $new_translate->language = $key;
            $new_translate->description = isset($descArr[$key]) ? $descArr[$key] : null;
            if ($new_translate->save(false)) {
            } else {
                $errors[] = $new_translate->getErrorSummary(true);
                return simplify_errors($errors);
            }
        }
        $transaction->commit();
        return true;
    }

    public static function updateTranslate($nameArr, $table_name, $model_id, $descArr = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        //$deleteAll = Translate::deleteAll(['model_id' => $model_id]);
        foreach ($nameArr as $key => $value) {
            $update_tranlate = Translate::find()->where(['model_id' => $model_id, 'table_name' => $table_name, 'language' => $key])->one();
            if (isset($update_tranlate)) {
                $update_tranlate->name = $value;
                $update_tranlate->description = isset($descArr[$key]) ? $descArr[$key] : null;
                $update_tranlate->update();
            } else {
                $new_translate = new Translate();
                $new_translate->name = $value;
                $new_translate->table_name = $table_name;
                $new_translate->model_id = $model_id;
                $new_translate->language = $key;
                $new_translate->description = isset($descArr[$key]) ? $descArr[$key] : null;
                if ($new_translate->save(false)) {
                } else {
                    $errors[] = $new_translate->getErrorSummary(true);
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
        }
        $transaction->commit();
        return true;
    }

    public function checkingAll($post)
    {

        if (isset($post['name'])) {
            if (!is_array($post['name'])) {
                $errors[] = [_e('Please send Name attribute as array.')];
                return $errors;
            }else{
                foreach($post as $key => $value){
                    
                }
            }
        } else {
            $errors[] = [_e('Please send Name attribute as array.')];
            return $errors;
        }
        if (isset($post['description'])) {
            if (!is_array($post['description'])) {
                $errors[] = [_e('Please send Description attribute as array.')];
                return $errors;
            }else{

            }
        }

    }
}
