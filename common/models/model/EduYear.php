<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

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
class EduYear extends \yii\db\ActiveRecord
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
        return 'edu_year';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_year', 'end_year', 'type'], 'required',],
//             [['type'], 'max'=> 9],
            [[
                'start_year',
                'end_year',
                'order',
                'status',
                'type',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted'
            ], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //            'name' => 'Name',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'year' => 'Year',
            'type' => 'Type',
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
            'name' => function($model) {
                return $model->start_year. " - ". $model->end_year . " - ". $model->type;
            },
            'type',
            'order',
            'start_year',
            'end_year',
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
            'eduPlans',
            'eduSemestrs',
            'timeTables',
            'description',
            'teacherCheckingType',
            'activeEduYear',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Get Tranlate
     *
     * @return void
     */
    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }


    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }


    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    /**
     * Gets query for [[EduPlans]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduPlans()
    {
        return $this->hasMany(EduPlan::className(), ['edu_year_id' => 'id']);
    }

    public function getActiveEduYear() {
        $eduYear = EduYear::findOne([
            'status' => 1,
            'is_deleted' => 0,
        ]);
        return $eduYear;
    }

    public static function activeEduYear() {
        $eduYear = EduYear::findOne([
            'status' => 1,
            'is_deleted' => 0,
        ]);
        return $eduYear;
    }

    public function getTeacherAttendPercent()
    {
        $eduYearId = Yii::$app->request->get('edu_year_id');
        $kafedraId = Yii::$app->request->get('kafedra_id');



    }

    /**
     * Gets query for [[EduSemestrs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrs()
    {
        return $this->hasMany(EduSemestr::className(), ['edu_year_id' => 'id']);
    }

    /**
     * Gets query for [[TimeTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTables()
    {
        return $this->hasMany(TimeTable1::className(), ['edu_year_id' => 'id']);
    }

    /**
     * Gets query for [[TeacherCheckingType]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getTeacherCheckingType()
    {
        return $this->hasMany(TeacherCheckingType::className(), ['edu_year_id' => 'id']);
    }

    public static function createItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['year'])) {
            $yearPost = $post['year'];
            $year = EduYear::find()
                ->orderBy('id desc')
                ->one();
            if (isset($year)) {
                if (!($yearPost > $year->end_year)) {
                    $errors[] = ['year' => _e('Created before '.$yearPost)];
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($year->type > 2 || $year->type < 1) {
                    $errors[] = ['type' => _e('The type value is invalid')];
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($year->type == 1) {
                    $model = new EduYear();
                    $model->start_year = $year->start_year;
                    $model->end_year = $year->end_year;
                    $model->type = 2;
                    $model->status = 0;
                    if (!$model->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }
                    $start_date = $model->end_year;
                }
                if ($year->type == 2) {
                    $start_date = $year->end_year;
                }

                for ($i=$start_date; $i<=$yearPost; $i++) {
                    $model = new EduYear();
                    $model->start_year = $i;
                    $model->end_year = $i+1;
                    $model->type = 1;
                    $model->status = 0;
                    if (!$model->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }

                    $model2 = new EduYear();
                    $model2->start_year = $i;
                    $model2->end_year = $i+1;
                    $model2->type = 2;
                    $model2->status = 0;
                    if (!$model2->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }
                }

            } else {
                for ($i=2023; $i<=2035; $i++) {
                    $model = new EduYear();
                    $model->start_year = $i;
                    $model->end_year = $i+1;
                    $model->type = 1;
                    $model->status = 0;
                    if (!$model->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }

                    $model2 = new EduYear();
                    $model2->start_year = $i;
                    $model2->end_year = $i+1;
                    $model2->type = 2;
                    $model2->status = 0;
                    if (!$model2->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }
                }
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


    public static function createItem11($model, $post)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        EduYear::updateAll(['status' => 0], ['status' => 1]);

        $model->status = 1;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save(false)) {
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
