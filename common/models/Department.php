<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "department".
 *
 * @property int $id
 * @property string $type
 * @property int|null $parent_id
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class Department extends \base\libs\RedisDB
{

    public static $selected_language = 'en';

    const TYPE_DIVISION = 1;
    const TYPE_DEANERY = 2;
    const TYPE_CHAIR = 3;
    const TYPE_CENTER = 4;
    const TYPE_RECTORATE = 5;

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['sort','type'], 'required'],
            [['created_on', 'updated_by'], 'safe'],
            [['parent_id', 'sort', 'status', 'deleted', 'created_by', 'updated_by'], 'integer'],
            [['parent_id', 'status', 'sort', 'deleted'], 'default', 'value' => 0],
            ['created_on', 'default', 'value' => date('Y-m-d H:i:s')],
            ['updated_on', 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'type' => _e('Type'),
            'parent_id' => _e('Parent'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'deleted' => _e('Deleted'),
            'created_on' => _e('Created on'),
            'created_by' => _e('Created by'),
            'updated_on' => _e('Updated on'),
            'updated_by' => _e('Updated by'),
        ];
    }

    /**
     * Get content infos
     *
     * @return void
     */
    public function getInfos()
    {
        return $this->hasMany(DepartmentInfo::class, ['department_id' => 'id']);
    }

    /**
     * Get childs
     *
     * @return void
     */
    public function getChilds()
    {
        return $this->hasMany(Department::class, ['parent_id' => 'id']);
    }

    /**
     * Get content info
     *
     * @return void
     */
    public function getInfoRelation()
    {
        self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(DepartmentInfo::class, ['department_id' => 'id'])
                    ->andOnCondition(['language' => self::$selected_language]);
    }

    /**
     * Get info
     *
     * @return void
     */
    public function getInfo()
    {
        return $this->infoRelation[0];
    }
    
    /**
     * Get parent
     *
     * @return void
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Get parent
     *
     * @return void
     */
    public function getParentInfoRelation()
    {
        return $this->hasOne(DepartmentInfo::class, ['department_id' => 'id'])
            ->via('parent')
            ->andOnCondition(['language' => self::$selected_language]);
    }

    /**
     * Get parent
     *
     * @return void
     */
    public function getParentInfo()
    {
        return $this->parentInfoRelation[0];
    }

    public static function listTypes($lang = null){
        $lang = $lang ?? self::$selected_language;
        return [
            self::TYPE_DIVISION => _t('app', 'Division',[],$lang),
            self::TYPE_DEANERY => _t('app', 'Deanery',[],$lang),
            self::TYPE_CHAIR => _t('app', 'Chair',[],$lang),
            self::TYPE_CENTER => _t('app', 'Center',[],$lang),
            self::TYPE_RECTORATE => _t('app', 'Rectorate',[],$lang)
        ];
    }

    public function getTypeText(){
        self::$selected_language = Yii::$app->request->get('lang') ?? 'en';
        return self::listTypes(self::$selected_language)[$this->type];
    }

    public static function listOtherDepartments($department_id = null, $lang = null){
        
        $lang = $lang ?? self::$selected_language;
        $result = self::find()
            ->join('INNER JOIN', 'department_info info', 'info.department_id = department.id')
            ->select('department.*, info.*')
            ->where(['info.language' => $lang])
            ->andFilterWhere(['<>','id',$department_id])
            ->asArray()->all();
        $list = [];
        foreach ($result as $one) {
            $list[$one['id']] = $one['name'];
        }
        return $list;
    }

    public static function listAll($type = null, $lang = null){
        
        $lang = $lang ?? self::$selected_language;
        $typeCondition = ($type) ? ['type' => $type] : [];
        $result = self::find()
            ->join('INNER JOIN', 'department_info info', 'info.department_id = department.id')
            ->select('department.*, info.*')
            ->where([
                'and',
                ['info.language' => $lang],
                $typeCondition
            ])
            ->asArray()->all();
        $list = [];
        foreach ($result as $one) {
            $list[$one['id']] = $one['name'];
        }
        return $list;
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            1 => _e('Active'),
            0 => _e('Inactive'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new Department(), new  DepartmentInfo());
    }

}
