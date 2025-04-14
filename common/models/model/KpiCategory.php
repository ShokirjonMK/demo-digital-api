<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

use function PHPSTORM_META\type;

/**
 * This is the model class for table "direction".
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
 */
class KpiCategory extends \yii\db\ActiveRecord
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
    const TYPE_KPI = 1;
    const TYPE_1030_1 = 2;
    const TYPE_1030_2 = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'term',
                    'tab',
                    'type'
                ],
                'integer'
            ],
            [
                [
                    'max_ball',
                ],
                'double'
            ],
            // [
            //     [
            //         'fields',
            //     ], 'text', 'max' => 255
            // ],

            // [
            //     [
            //         'fields',
            //     ], 'json'
            // ],
            [[
                'user_ids'
            ], 'safe'],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status'], 'default', 'value' => 1],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name on info table
            // description on info table
            // tab name on info table

            'type',
            'fields',
            'max_ball',
            'term',
            'tab',
            'user_ids',

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
            'name' => function ($model) {
                return $model->info->name ?? '';
            },

            'fields',
            'user_ids',
            'max_ball',
            'termName',
            'tab',
            'type',

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

            'description',
            'term',
            'tabName',
            'extra',

            'kpiData',
            'kpiMark',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getInfo()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->info->description ?? '';
    }

    public function getTabName()
    {
        return $this->info->tab_name ?? '';
    }

    public function getTermName()
    {
        return $this->term()[$this->term] ?? '';
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(KpiCategoryInfo::class, ['kpi_category_id' => 'id'])
            ->andOnCondition(['lang' => Yii::$app->request->get('lang')]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(KpiCategoryInfo::class, ['kpi_category_id' => 'id'])
            ->andOnCondition(['lang' => self::$selected_language]);
    }

    /**
     * Get all of the getKpiData for the KpiCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getKpiData()
    {
        // return 1;

        return $this->hasMany(KpiData::className(), ['kpi_category_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => Yii::$app->request->get('user_id') ?? current_user_id()]);
    }

    public function getKpiMark()
    {
        return $this->hasOne(KpiMark::className(), ['kpi_category_id' => 'id'])->onCondition(['is_deleted' => 0, 'archived' => 0, 'user_id' => Yii::$app->request->get('user_id') ?? current_user_id()]);
        // $edu_year_id = EduYear::findOne(['year' => date("Y")])->id;
        // 
        // return $this->hasOne(KpiMark::className(), ['kpi_category_id' => 'id'])->onCondition(['is_deleted' => 0, 'edu_year_id' => $edu_year_id, 'user_id' => Yii::$app->request->get('user_id') ?? current_user_id()]);
    }


    public function getKpiCategory()
    {
        return $this->hasMany(KpiMark::class, ['kpi_category_id' => 'id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['fields'])) {

            if (($post['fields'][0] == "'") && ($post['fields'][strlen($post['fields']) - 1] == "'")) {
                $post['fields'] =  substr($post['fields'], 1, -1);
            }

            if (!isJsonMK($post['fields'])) {
                $errors['fields'] = [_e('Must be Json')];
            } else {
                $fields = ((array)json_decode($post['fields']));
                // dd($fields);
                if (!empty(array_diff($fields, self::categoryFields()))) {
                    $errors['fields'] = [_e('Incorrect fields')];
                } else {
                    $model->fields = $fields;
                }
            }
        }

        if (isset($post['user_ids'])) {
            // Remove single quotes if present at the beginning and end of the string
            if (($post['user_ids'][0] === "'") && ($post['user_ids'][strlen($post['user_ids']) - 1] === "'")) {
                $post['user_ids'] = substr($post['user_ids'], 1, -1);
            }

            // Decode the JSON data into an array and handle errors
            try {
                $user_ids = \yii\helpers\Json::decode($post['user_ids'], true);
            } catch (\yii\base\InvalidArgumentException $e) {
                // JSON decoding error occurred
                $errors['user_ids'] = [_e('Invalid JSON format')];
            }

            // Check if each user ID exists in the users table
            $existingUsers = User::find()->select('id')->indexBy('id')->column();
            $nonExistingIds = array_diff($user_ids, array_keys($existingUsers));

            if (!empty($nonExistingIds)) {
                $errors['user_ids'] = [_e('Invalid user IDs: ') . implode(', ', $nonExistingIds)];
            } else {
                // Assign the array to the $model->user_ids attribute
                $model->user_ids = $user_ids;
            }
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];
                } else {
                    foreach ($post['name'] as $lang => $name) {
                        $info = new KpiCategoryInfo();
                        $info->kpi_category_id = $model->id;
                        $info->lang = $lang;
                        $info->name = $name;
                        $info->description = $post['description'][$lang] ?? null;
                        $info->tab_name = $post['tab_name'][$lang] ?? null;
                        if (!$info->save()) {
                            $errors[] = $info->getErrorSummary(true);
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one name attribute.')];
            }
        } else {
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

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['fields'])) {
            if (($post['fields'][0] == "'") && ($post['fields'][strlen($post['fields']) - 1] == "'")) {
                $post['fields'] =  substr($post['fields'], 1, -1);
            }

            if (!isJsonMK($post['fields'])) {
                $errors['fields'] = [_e('Must be Json')];
            } else {
                $model->fields = ((array)json_decode($post['fields']));
            }
        }


        if (isset($post['user_ids'])) {

            if (($post['user_ids'][0] == "'") && ($post['user_ids'][strlen($post['user_ids']) - 1] == "'")) {
                $post['user_ids'] =  substr($post['user_ids'], 1, -1);
            }

            if (!isJsonMK($post['user_ids'])) {
                $errors['user_ids'] = [_e('Must be Json')];
            } else {
                $user_ids = ((array)json_decode($post['user_ids']));
                $model->user_ids = $user_ids;
            }
        }


        // if (isset($post['user_ids'])) {
        //     // Remove single quotes if present at the beginning and end of the string
        //     if (($post['user_ids'][0] === "'") && ($post['user_ids'][strlen($post['user_ids']) - 1] === "'")) {
        //         $post['user_ids'] = substr($post['user_ids'], 1, -1);
        //     }

        //     // Decode the JSON data into an array and handle errors
        //     try {
        //         $user_ids = \yii\helpers\Json::decode($post['user_ids'], true);
        //     } catch (\yii\base\InvalidArgumentException $e) {
        //         // JSON decoding error occurred
        //         $errors['user_ids'] = [_e('Invalid JSON format')];
        //     }

        //     // Check if each user ID exists in the users table
        //     $existingUsers = User::find()->select('id')->indexBy('id')->column();
        //     $nonExistingIds = array_diff($user_ids, array_keys($existingUsers));

        //     if (!empty($nonExistingIds)) {
        //         $errors['user_ids'] = [_e('Invalid user IDs: ') . implode(', ', $nonExistingIds)];
        //     } else {
        //         // Assign the array to the $model->user_ids attribute
        //         $model->user_ids = $user_ids;
        //     }
        // }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];
                } else {
                    foreach ($post['name'] as $lang => $name) {
                        $info = KpiCategoryInfo::find()->where(['kpi_category_id' => $model->id, 'lang' => $lang])->one();
                        if ($info) {
                            $info->name = $name;
                            $info->description = $post['description'][$lang] ?? null;
                            $info->tab_name = $post['tab_name'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        } else {
                            $info = new KpiCategoryInfo();
                            $info->kpi_category_id = $model->id;
                            $info->lang = $lang;
                            $info->name = $name;
                            $info->description = $post['description'][$lang] ?? null;
                            $info->tab_name = $post['tab_name'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        }
                    }
                }
            }
        } else {
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

    public static function categoryFields()
    {
        return [
            "date",
            "file",
            "file1",
            "file2",
            "file3",
            // "subject_category",
            // "count_of_copyright",
            "link",
            "link2",
            "input",
            "isbn",
            "name",
            "name1",
            "name2",

        ];

        /*  return
            [
                "input"=> ,
                "link",
                "number",
                "file",
                "date",
                "double_date",
                "select",
                "nimadir"
            ]; */
    }

    public static function term()
    {
        return
            [
                1 => _e("1 year"),
                2 => _e("6 month"), //1
                3 => _e('Bir kalendar yil davomida'), //1
                4 => _e('Sertifikat muddati davomida'), // check
                5 => _e('Taribdan chiqarilgunga qadar'),
                6 => _e('1 month'), //
                7 => _e('2 years'), //
            ];
    }

    public static function type()
    {
        return [
            1 => _e("KPI"),
            2 => _e("1030-1"),
            3 => _e("1030-2"),
        ];
    }

    public static function tab()
    {
        return [
            1 => _e("O‘quv va o‘quv-uslubiy ishlar"),
            2 => _e("Ilmiy va innovatsiyalarga oid ishlar"),
            3 => _e("Xalqaro hamkorlikka oid ishlar"),
            4 => _e("Ma'naviy-ma'rifiy ishlarga rioya etish holati"),
            5 => _e("Ijro intizomni taʼminlashga oid ishlar"),
            10 => _e("1030 1-ilova "),
            11 => _e("1030 2-ilova "),
        ];
    }

    public function getExtra()
    {
        return self::extra();
    }

    public static function extra()
    {
        return [
            "type" => self::type(),
            "fields" => self::categoryFields(),
            "term" => self::term(),
            "tab" => self::tab(),
            "extra" => KpiData::varTypes(),
        ];
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
