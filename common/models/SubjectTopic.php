<?php

namespace common\models;

use common\models\enums\TopicType;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_topic".
 *
 * @property int $id
 * @property int $type
 * @property int $subject_id
 * @property int $hours
 * @property int $sort
 * @property int|null $status
 * @property int $deleted
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class SubjectTopic extends \base\libs\RedisDB
{

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'subject_topic';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['type', 'subject_id', 'hours', 'sort'], 'required'],
            [['created_on', 'updated_by'], 'safe'],
            [['subject_id', 'type', 'hours', 'sort', 'status', 'deleted', 'created_by', 'updated_by'], 'integer'],
            [['status', 'sort', 'deleted', 'type'], 'default', 'value' => 0],
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
            'subject_id' => _e('Subject'),
            'hours' => _e('Hours'),
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
        return $this->hasMany(SubjectTopicInfo::class, ['subject_topic_id' => 'id']);
    }

    /**
     * Get content info
     *
     * @return void
     */
    public function getInfoRelation()
    {
        self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(SubjectTopicInfo::class, ['subject_topic_id' => 'id'])
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
     * Get subject
     *
     * @return void
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Get all items
     *
     * @param int $lang
     * @return array
     */
    public static function listAll($subject_id = null, $lang = null)
    {

        $lang = $lang ?? self::$selected_language;
        $result = self::find()
            ->join('INNER JOIN', 'subject_topic_info info', 'info.subject_topic_id = subject_topic.id')
            ->select('subject_topic.*, info.*')
            ->where([
                'and',
                ['info.language' => $lang]
            ])
            ->andFilterWhere(['subject_id' => $subject_id])
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
