<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property int|null $type
 * @property string|null $code
 * @property int|null $department_id
 * @property int|null $practical_hours
 * @property int|null $theoretical_hours
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class Subject extends \base\libs\RedisDB
{

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['sort'], 'required'],
            [['created_on', 'updated_by'], 'safe'],
            [['code'], 'string'],
            [['department_id', 'practical_hours', 'theoretical_hours', 'sort', 'status', 'deleted', 'created_by', 'updated_by'], 'integer'],
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
            'code' => _e('Code'),
            'department_id' => _e('Department'),
            'practical_hours' => _e('Practical hours'),
            'theoretical_hours' => _e('Theoretical hours'),
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
        return $this->hasMany(SubjectInfo::class, ['subject_id' => 'id']);
    }

    /**
     * Get content info
     *
     * @return void
     */
    public function getInfoRelation()
    {
        self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(SubjectInfo::class, ['subject_id' => 'id'])
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
     * Get department
     *
     * @return void
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    /**
     * Get all hours
     *
     * @return void
     */
    public function getAllHours()
    {
        $practicalHours = ($this->practical_hours) ?? 0;
        $theoreticalHours = ($this->theoretical_hours) ?? 0;
        return $practicalHours + $theoreticalHours;
    }

    /**
     * Get all hours
     *
     * @return void
     */
    public function getHoursText()
    {
        $practicalHours = ($this->practical_hours) ?? 0;
        $theoreticalHours = ($this->theoretical_hours) ?? 0;
        return '<b>' . $this->allHours . '</b> (' .  _e('Practical hours') . ': <b>' . $practicalHours . '</b>, '.  _e('Theoretical hours') . ': <b>' . $theoreticalHours . '</b>)';
    }

    /**
     * Get all items
     *
     * @param int $lang
     * @return array
     */
    public static function listAll($lang = null){
        
        $lang = $lang ?? self::$selected_language;
        $result = self::find()
            ->join('INNER JOIN', 'subject_info info', 'info.subject_id = subject.id')
            ->select('subject.*, info.*')
            ->where([
                'and',
                ['info.language' => $lang]
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

}
