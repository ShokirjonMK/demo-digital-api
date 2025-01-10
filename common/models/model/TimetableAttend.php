<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


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
class TimetableAttend extends \yii\db\ActiveRecord
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

    const REASON_TRUE = 1;
    const REASON_FALSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable_attend';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'timetable_id',
                'timetable_date_id',
                'student_id',
            ], 'required'],
            [[
                'timetable_id',
                'ids_id',
                'timetable_date_id',
                'reason',
                'timetable_reason_id',
                'group_id',
                'student_id',
                'student_user_id',
                'edu_year_id',
                'edu_semestr_id',
                'faculty_id',
                'semestr_id',
                'para_id',
                'edu_plan_id',
                'subject_id',
                'subject_category_id',
                'edu_form_id',
                'edu_type_id',
                'group_type',

                'status',
                'order',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted'
            ], 'integer'],
            [['date'], 'safe'],
            [['timetable_id'], 'exist', 'skipOnError' => true, 'targetClass' => Timetable::className(), 'targetAttribute' => ['timetable_id' => 'id']],
            [['timetable_date_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimetableDate::className(), 'targetAttribute' => ['timetable_date_id' => 'id']],
            [['ids_id'], 'exist', 'skipOnError' => true, 'targetClass' => Timetable::className(), 'targetAttribute' => ['ids_id' => 'ids']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['timetable_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimetableReason::className(), 'targetAttribute' => ['timetable_reason_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['para_id'], 'exist', 'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']],

            [['student_id'], 'unique', 'targetAttribute' => ['student_id', 'timetable_date_id'], 'message' => "Student Already recorded"],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'timetable_id',
            'ids_id',
            'timetable_date_id',
            'para_id',
            'student_id',
            'subject_id',
            'subject_category_id',
            'reason',
            'date',
            'updated_at',
            'updated_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'profile',
            'attend',
            'timetableReason',
            'timeTableDate',
            'eduPlan',
            'eduSemestr',
            'eduYear',
            'faculty',
            'para',
            'student',
            'user',
            'subject',
            'subjectCategory',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'student_user_id']);
    }

    public function getTimetableReason()
    {
        return $this->hasOne(TimetableReason::className(), ['id' => 'timetable_reason_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
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

    public function getTimeTableDate()
    {
        return $this->hasOne(TimetableDate::className(), ['id' => 'timetable_date_id']);
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
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery|SubjectCategoryQuery
     */
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    public static function createItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $models = TimetableDate::find()
            ->where([
                'ids_id' => $post['ids'],
                'date' => $post['date'],
                'group_type' => $post['group_type'],
                'para_id' => $post['para_id'],
                'status' => 1,
                'is_deleted' => 0
            ])->all();

        $students = array_unique(json_decode($post['student_ids']));

        $studentCount = count($students);
        if (count($models) != 0) {
            foreach ($models as $model) {
                if ($studentCount > 0) {
                    $timetable = $model->thisTimetable;
                    $timetableStudents = $timetable->timeTableStudent;
                    if (count($timetableStudents) > 0) {
                        foreach ($timetableStudents as $timetableStudent) {
                            for ($i = 0; $i < $studentCount; $i++) {
                                if ($students[$i] == $timetableStudent['id']) {
                                    $new = new TimetableAttend();
                                    $new->timetable_id = $timetable->id;
                                    $new->ids_id = $timetable->ids;
                                    $new->timetable_date_id = $model->id;
                                    $new->para_id = $model->para_id;
                                    $new->date = $model->date;
                                    $new->subject_id = $model->subject_id;
                                    $new->subject_category_id = $model->subject_category_id;
                                    $new->group_id = $model->group_id;
                                    $new->student_id =$students[$i];
                                    $new->student_user_id = $new->student->user_id;
                                    $new->edu_plan_id = $model->edu_plan_id;
                                    $new->edu_semestr_id = $model->edu_semestr_id;
                                    $new->faculty_id = $model->faculty_id;
                                    $new->semestr_id = $model->semestr_id;
                                    $new->edu_type_id = $model->edu_type_id;
                                    $new->edu_year_id = $model->edu_year_id;
                                    $new->group_type = $model->group_type;
                                    $paraTime = strtotime($new->date. " ".$model->para->start_time);
                                    $studentReasons = TimetableReason::find()
                                        ->where(['is_confirmed' => 1, 'edu_year_id' => $model->edu_year_id, 'student_id' => $students[$i], 'status' => 1,'is_deleted' => 0])
                                        ->all();
                                    if (count($studentReasons) > 0) {
                                        foreach ($studentReasons as $studentReason) {
                                            if (strtotime($studentReason->start) <= $paraTime && strtotime($studentReason->end) >= $paraTime) {
                                                $new->timetable_reason_id = $studentReason->id;
                                                $new->reason = 1;
                                                break;
                                            }
                                        }
                                    }

                                    if ($new->validate()) {
                                        $new->save(false);
                                    } else {
                                        $errors[] = $new->errors;
                                        return simplify_errors($errors);
                                    }
                                }
                            }
                        }
                    }
                }
                $model->attend_status = 1;
                $model->update(false);
            }
        } else {
            $errors[] = _e('There is no class today.');
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isRole('teacher')) {
            if ($model->created_by != current_user_id()) {
                $errors[] = [_e('Xatolik!!!')];
            }
        }

        if (isset($post['reason']) && in_array($post['reason'], [0, 1])) {
            $model->reason = $post['reason'];
            $model->update(false);
        }

        if (count($errors) == 0) {
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
