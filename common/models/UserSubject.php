<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_subject".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject_id
 * @property int $language_id
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 */
class UserSubject extends \base\libs\RedisDB
{

    public static $selected_language = 'en';

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'user_subject';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'subject_id', 'language_id'], 'required'],
            [['created_on', 'updated_by'], 'safe'],
            [['user_id', 'subject_id', 'language_id', 'created_by', 'updated_by'], 'integer'],
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
            'user_id' => _e('User'),
            'subject_id' => _e('Subject'),
            'language_id' => _e('Languages'),
            'created_on' => _e('Created on'),
            'created_by' => _e('Created by'),
            'updated_on' => _e('Updated on'),
            'updated_by' => _e('Updated by'),
        ];
    }

    /**
     * Get user
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
     * Get language
     *
     * @return void
     */
    public function getLanguage()
    {
        return $this->hasOne(Reference::class, ['id' => 'language_id']);
    }

    public static function listAll($user_id)
    {
        $data = self::find()->where(['user_id' => $user_id])->all();
        $data2 = [];
        $subjects = [];
        $langs = [];
        foreach ($data as $row) {
            $subjects[] = $row->subject_id;
            $langs[] = $row->language_id;
            $data2[] = [
                'subject' => $row->subject_id,
                'lang' => $row->language_id,
            ];
        }
        $subjects = array_unique($subjects);
        $langs = array_unique($langs);

        $data3 = [];
        foreach ($subjects as $subject) {
            $tmp = [];
            $tmp['subject'] = $subject;
            foreach ($data2 as $row) {
                if ($row['subject'] == $subject) {
                    $tmp['langs'][] = $row['lang'];
                }
            }
            $data3[] = $tmp;
        }
        return $data3;
    }
}
