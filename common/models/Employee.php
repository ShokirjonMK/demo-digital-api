<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property int|null $job_id
 * @property string|null $inps
 * @property string|null $scientific_work
 * @property string|null $languages
 * @property string|null $lang_certs
 * @property float|null $rate
 * @property int|null $rank_id
 * @property int|null $science_degree_id
 * @property int|null $scientific_title_id
 * @property int|null $special_title_id
 * @property string|null $reception_time
 * @property int|null $out_staff
 * @property int|null $basic_job
 * @property int|null $is_convicted
 * @property int|null $party_membership
 * @property string|null $awords
 * @property string|null $depuities
 * @property string|null $military_rank
 * @property int|null $disability_group
 * @property int|null $family_status
 * @property string|null $children
 * @property string|null $other_info
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'department_id', 'job_id', 'rank_id', 'science_degree_id', 'scientific_title_id', 'special_title_id', 'out_staff', 'basic_job', 'is_convicted', 'party_membership', 'disability_group', 'family_status'], 'integer'],
            [['scientific_work', 'other_info','rate'], 'string'],
            [['inps', 'lang_certs', 'reception_time', 'awords', 'depuities', 'military_rank', 'children'], 'string', 'max' => 255],
            [['languages'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('User'),
            'department_id' => _e('Department'),
            'job_id' => _e('Job'),
            'inps' => _e('INPS'),
            'scientific_work' => _e('Scientific Work'),
            'languages' => _e('Languages'),
            'lang_certs' => _e('Lang Certs'),
            'rate' => _e('Rate'),
            'rank_id' => _e('Rank'),
            'science_degree_id' => _e('Science degree'),
            'scientific_title_id' => _e('Scientific title'),
            'special_title_id' => _e('Special itle'),
            'reception_time' => _e('Reception ime'),
            'out_staff' => _e('Out staff'),
            'basic_job' => _e('Basic job'),
            'is_convicted' => _e('Previously convicted'),
            'party_membership' => _e('Party membership'),
            'awords' => _e('Awords'),
            'depuities' => _e('Depuities'),
            'military_rank' => _e('Military rank'),
            'disability_group' => _e('Disability group'),
            'family_status' => _e('Family status'),
            'children' => _e('Children'),
            'other_info' => _e('Other info'),
        ];
    }

    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    public function getJob()
    {
        return $this->hasOne(Job::class, ['id' => 'job_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
