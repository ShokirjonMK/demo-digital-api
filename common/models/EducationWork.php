<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_education_work".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property int|null $job_id
 * @property string|null $work_begin_date
 * @property string|null $work_end_date
 * @property string|null $work_begin_doc_no
 * @property string|null $work_begin_doc_date
 * @property string|null $work_end_doc_no
 * @property string|null $work_end_doc_date
 * @property int|null $education
 * @property int|null $education_direction_id
 * @property int|null $education_level
 * @property int|null $specialty_id
 * @property int|null $university_id
 * @property string|null $university_finished_at
 * @property string|null $certification_serial_no
 * @property string|null $diploma_number
 * @property string|null $diploma_date
 * @property string|null $scientific_work
 * @property string|null $languages
 * @property string|null $lang_certs
 * @property float|null $rate
 * @property int|null $rank_id
 * @property int|null $science_degree_id
 * @property int|null $scientific_title_id
 * @property string|null $special_title_id
 * @property string|null $reception_time
 * @property int|null $out_staff
 * @property int|null $basic_job
 * @property int|null $student_department_id
 * @property string|null $basis_of_learning
 * @property string|null $student_other_info
 */
class EducationWork extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_education_work';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'department_id', 'job_id', 'education', 'education_direction_id', 'education_level', 'specialty_id', 'university_id', 'science_degree_id', 'scientific_title_id', 'out_staff', 'basic_job', 'student_department_id', 'special_title_id',  'rank_id'], 'integer'],
            [['work_begin_date', 'work_end_date', 'work_begin_doc_date', 'work_end_doc_date', 'university_finished_at', 'diploma_date', 'rate'], 'safe'],
            [['scientific_work', 'student_other_info'], 'string'],
            [['languages', ], 'safe'],
            [['work_begin_doc_no', 'work_end_doc_no', 'certification_serial_no', 'diploma_number', 'lang_certs', 'reception_time', 'basis_of_learning'], 'string', 'max' => 255],
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
            'work_begin_date' => _e('Start date of work'),
            'work_end_date' => _e('Last date of work'),
            'work_begin_doc_no' => _e('Job appointment document number'),
            'work_begin_doc_date' => _e('Job appointment document date'),
            'work_end_doc_no' => _e('Dismissal document number'),
            'work_end_doc_date' => _e('Dismissal document date'),
            'education' => _e('Education'),
            'education_direction_id' => _e('Education speciality'),
            'education_level' => _e('Education level'),
            'specialty_id' => _e('Specialty'),
            'university_id' => _e('University'),
            'university_finished_at' => _e('Year of graduation from the university'),
            'certification_serial_no' => _e('Certification serial number'),
            'diploma_number' => _e('Diploma number'),
            'diploma_date' => _e('Date of issue of the diploma'),
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
            'student_department_id' => _e('Student department'),
            'basis_of_learning' => _e('Basis of learning'),
            'student_other_info' => _e('Other informations (student)'),
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
}
