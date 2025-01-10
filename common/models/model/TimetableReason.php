<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;


/**
 * This is the model class for table "{{%student_attend}}".
 *
 * @property int $id
 * @property int $student_id
 * @property int|null $reason 0 sababsiz 1 sababli
 * @property int $attend_id
 * @property int|null $attend_reason_id
 * @property string $date
 * @property int $time_table_id
 * @property int $subject_id
 * @property int $subject_category_id
 * @property int $time_option_id
 * @property int $edu_year_id
 * @property int $edu_semestr_id
 * @property int|null $faculty_id
 * @property int|null $course_id
 * @property int|null $edu_plan_id
 * @property int|null $type 1 kuz 2 bohor
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Attend $attend
 * @property AttendReason $attendReason
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemestr
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Student $student
 * @property Subject $subject
 * @property SubjectCategory $subjectCategory
 * @property TimeTable1 $timeTable
 */
class TimetableReason extends \yii\db\ActiveRecord
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

    const CONFIRMED = 1;
    const NOT_CONFIRMED = 0;
    const CANCELLED = 2;

    const UPLOADS_FOLDER = 'uploads/timetable_attend/';
    public $attend_file;
    public $attendFileMaxSize = 1024 * 1024 * 5; // 5 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable_reason';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'start',
                'end',
                'student_id',
                'edu_semestr_id',
            ], 'required'],
            [[
                'start',
                'end'
            ], 'safe'],
            [[
                'is_confirmed',
                'student_id',
                'student_user_id',
                'edu_plan_id',
                'faculty_id',
                'edu_semestr_id',
                'semestr_id',
                'edu_form_id',
                'edu_type_id',
                'edu_year_id',
                'status',
                'order',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted'
            ], 'integer'],
            [
                ['file'], 'string',
                'max' => 255
            ],
            [
                ['description'], 'string'
            ],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['attend_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->attendFileMaxSize],

            ['start', 'validStart'],
        ];
    }

    public function validStart($attribute, $params)
    {
        if ($this->start >= $this->end) {
            $this->addError($attribute, _e('The start time must be less than the end time.'));
        }
    }

    public function fields()
    {
        $fields =  [
            'id',
            'is_confirmed',
            'start' => function($model) {
                return strtotime($this->start);
            },
            'end' => function($model) {
                return strtotime($this->end);
            },
            'student_id',
            'faculty_id',
            'edu_plan_id',
            'edu_year_id',
            'file',
            'description',

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
            'eduPlan',
            'eduSemestr',
            'eduYear',
            'faculty',
            'student',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }

    public function getStudentAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['attend_id' => 'attend_id'])->onCondition(['student_id' => $this->student_id]);
    }

    /**
     * Gets query for [[Attend]].
     *
     * @return \yii\db\ActiveQuery|AttendQuery
     */
    public function getAttend()
    {
        return $this->hasOne(Attend::className(), ['id' => 'attend_id']);
    }

    /**
     * Gets query for [[AttendReason]].
     *
     * @return \yii\db\ActiveQuery|AttendReasonQuery
     */
    public function getAttendReason()
    {
        return $this->hasOne(AttendReason::className(), ['id' => 'attend_reason_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery|SubjectCategoryQuery
     */
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    /**
     * Gets query for [[TimeOption]].
     *
     * @return \yii\db\ActiveQuery|TimeOptionQuery
     */
    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

    /**
     * Gets query for [[TimeTable]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTimeTable()
    {
        return $this->hasOne(TimeTable1::className(), ['id' => 'time_table_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $eduSemestr = $model->eduSemestr;
        if ($eduSemestr->edu_form_id == 2 && (isRole('dean') || isRole('dean_deputy'))) {
            $errors[] = [_e('Sirtqi ta\'lim uchun bu funksiya qo\'shilmagan.')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_user_id = $model->student->user_id;
        $model->faculty_id = $model->student->faculty_id;
        $model->edu_plan_id = $model->student->edu_plan_id;
        $model->edu_year_id = $eduSemestr->edu_year_id;

        $model->semestr_id = $eduSemestr->semestr_id;
        $model->edu_form_id = $eduSemestr->edu_form_id;
        $model->edu_type_id = $eduSemestr->edu_type_id;

        $model->attend_file = UploadedFile::getInstancesByName('file');
        if ($model->attend_file) {
            $model->attend_file = $model->attend_file[0];
            $attendFileUrl = $model->upload();
            if ($attendFileUrl) {
                $model->file = $attendFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->save(false);
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function confirmItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->is_confirmed == self::CONFIRMED) {
            $errors[] = _e('This has been cancelled!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['is_confirmed']) && $post['is_confirmed'] == 1) {
            $reasonStart = date("Y-m-d", strtotime($model->start));
            $reasonEnd = date("Y-m-d", strtotime($model->end));

            $timetableAttends = TimetableAttend::find()
                ->where(['status' => 1, 'is_deleted' => 0, 'student_id' => $model->student_id, 'timetable_reason_id' => null])
                ->andWhere(['between', 'date', new Expression('DATE(:start)'), new Expression('DATE(:end)')])
                ->params([':start' => $reasonStart, ':end' => $reasonEnd])
                ->all();

            if (count($timetableAttends) > 0) {
                foreach ($timetableAttends as $timetableAttend) {
                    $paraDate = strtotime(date($timetableAttend->date. " ".$timetableAttend->timeTableDate->para->start_time));
                    if ($paraDate >= strtotime($model->start) && $paraDate <= strtotime($model->end)) {
                        $timetableAttend->timetable_reason_id = $model->id;
                        $timetableAttend->reason = 1;
                        $timetableAttend->save(false);
                    }
                }
            }

            $model->is_confirmed = self::CONFIRMED;
        } elseif (isset($post['is_confirmed']) && $post['is_confirmed'] == 2) {
            $model->is_confirmed = self::CANCELLED;
        } else {

            if (isset($post['start'])) {
                $model->start = date("Y-m-d H:i" , $post['start']);
            }
            if (isset($post['end'])) {
                $model->end = date("Y-m-d H:i" , $post['end']);
            }

            $model->attend_file = UploadedFile::getInstancesByName('file');
            if ($model->attend_file) {
                $model->attend_file = $model->attend_file[0];
                $attendFileUrl = $model->upload();
                if ($attendFileUrl) {
                    $model->file = $attendFileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            if (isset($post['description'])) {
                $model->description = $post['description'];
            }
            $model->is_confirmed = self::NOT_CONFIRMED;
        }

        $model->save(false);
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
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

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }
            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->attend_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->attend_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }
}
