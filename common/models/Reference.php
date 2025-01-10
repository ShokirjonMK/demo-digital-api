<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "reference".
 *
 * @property int $id
 * @property string $type
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class Reference extends \base\libs\RedisDB
{

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'reference';
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
            [['sort', 'status', 'deleted', 'created_by', 'updated_by'], 'integer'],
            [['status', 'sort', 'deleted','type'], 'default', 'value' => 0],
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
        return $this->hasMany(ReferenceInfo::class, ['reference_id' => 'id']);
    }

    /**
     * Get reference info relation
     *
     * @return void
     */
    public function getInfoRelation()
    {
        self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(ReferenceInfo::class, ['reference_id' => 'id'])
                    ->andOnCondition(['language' => self::$selected_language]);
    }

    /**
     * Get reference info
     *
     * @return void
     */
    public function getInfo()
    {
        return $this->infoRelation[0];
    }

    /**
     * Get all items
     *
     * @param int $lang
     * @return array
     */
    public static function listAll($type, $lang = null){
        
        $lang = $lang ?? self::$selected_language;
        $result = self::find()
            ->join('INNER JOIN', 'reference_info info', 'info.reference_id = reference.id')
            ->select('reference.*, info.*')
            ->where([
                'and',
                ['info.language' => $lang],
                ['type' => $type]
            ])
            ->asArray()->all();
        $list = [];
        foreach ($result as $one) {
            $list[$one['id']] = $one['name'];
        }
        return $list;
    }

    /**
     * Get all items
     *
     * @param int $lang
     * @return array
     */
    public static function listAllWithType($type, $lang = null){
        
        $lang = $lang ?? self::$selected_language;
        $list = self::find()
            ->join('INNER JOIN', 'reference_info info', 'info.reference_id = reference.id')
            ->select('reference.id, info.name, reference.type')
            ->where([
                'and',
                ['info.language' => $lang],
                ['type' => $type]
            ])
            ->asArray()->all();
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

}
