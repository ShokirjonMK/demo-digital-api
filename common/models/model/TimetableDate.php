<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\TimeTableCreate;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "time_table".
 *
 * @property int $id
 * @property int $teacher_access_id
// * @property int $fall_spring
 * @property int $para_id
 * @property int $week_id
 * @property int $course_id
 * @property int $semestr_id
 * @property int $edu_year_id
 * @property int $subject_id
 * @property int $language_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property EduYear $eduYear
 * @property Languages $language
 * @property Para $para
 * @property Room $room
 * @property Subject $subject
 * @property Semestr $semestr
 * @property TeacherAccess $teacherAccess
 */
class TimetableDate extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable_date';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
//                    'group_id',
//                    'edu_plan_id',
//                    'edu_semestr_id',
//                    'edu_semestr_subject_id',
//                    'faculty_id',
//                    'direction_id',
//                    'building_id',
//                    'room_id',
//                    'week_id',
//                    'para_id',
//                    'subject_category_id',
                ], 'required'
            ],
            [
                [
                    'timetable_id',
                    'ids_id',
                    'building_id',
                    'room_id',
                    'week_id',
                    'para_id',
                    'group_id',
                    'edu_semestr_subject_id',
                    'teacher_access_id',
                    'user_id',
                    'subject_id',
                    'subject_category_id',
                    'course_id',
                    'semestr_id',
                    'edu_year_id',
                    'edu_form_id',
                    'edu_type_id',
                    'edu_plan_id',
                    'edu_semestr_id',
                    'type',
                    'attend_status',

                    'two_group',
                    'group_type',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['date'] , 'date' , 'format' => 'yyyy-mm-dd'],
            [
                ['group_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']
            ],
            [
                ['course_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']
            ],
            [
                ['edu_semestr_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']
            ],
            [
                ['timetable_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Timetable::className(), 'targetAttribute' => ['timetable_id' => 'id']
            ],
            [
                ['edu_semestr_subject_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']
            ],
            [
                ['faculty_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']
            ],
            [
                ['direction_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']
            ],
            [
                ['edu_year_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [
                ['edu_plan_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']
            ],
            [
                ['para_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']
            ],
            [
                ['room_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']
            ],
            [
                ['edu_form_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']
            ],
            [
                ['edu_type_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']
            ],
            [
                ['week_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Week::className(), 'targetAttribute' => ['week_id' => 'id']
            ],
            [
                ['subject_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']
            ],
            [
                ['semestr_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']
            ],
            [
                ['subject_category_id'], 'exist',
                'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']
            ],
            ['room_id' , 'validRoom'],
            ['teacher_access_id' , 'validTeacher'],
            ['group_id' , 'validGroup'],
        ];
    }

    public function validRoom($attribute, $params)
    {
//        $model = new TimetableDate();
//        if ($this->two_group == 0) {
//            $query = $model->find()
//                ->where([
//                    'date' => $this->date,
//                    'room_id' => $this->room_id,
//                    'para_id' => $this->para_id,
//                    'status' => 1,
//                    'is_deleted' => 0
//                ])
//                ->andWhere(['<>' , 'ids_id' , $this->ids_id])
//                ->exists();
//            if ($query) {
//                $this->addError($attribute, _e('This room is assigned a lesson.'));
//            }
//        } else {
//            $query = $model->find()
//                ->where([
//                    'date' => $this->date,
//                    'room_id' => $this->room_id,
//                    'para_id' => $this->para_id,
//                    'status' => 1,
//                    'is_deleted' => 0
//                ])
//                ->exists();
//
//            if ($query) {
//                $this->addError($attribute, _e('This room is assigned a lesson.'));
//            }
//        }

    }

    public function validTeacher($attribute, $params)
    {
//        $model = new TimetableDate();
//        if ($this->two_group == 0) {
//            $query = $model->find()
//                ->where([
//                    'date' => $this->date,
//                    'user_id' => $this->user_id,
//                    'para_id' => $this->para_id,
//                    'status' => 1,
//                    'is_deleted' => 0
//                ])
//                ->andWhere(['<>' , 'ids_id' , $this->ids_id])
//                ->exists();
//            if ($query) {
//                $this->addError($attribute, _e('The teacher is busy at this hour.'));
//            }
//        } else {
//            $query = $model->find()
//                ->where([
//                    'date' => $this->date,
//                    'user_id' => $this->user_id,
//                    'para_id' => $this->para_id,
//                    'status' => 1,
//                    'is_deleted' => 0
//                ])
//                ->exists();
//            if ($query) {
//                $this->addError($attribute, _e('The teacher is busy at this hour.'));
//            }
//        }
    }

    public function validGroup($attribute, $params)
    {
        $query = TimetableDate::find()
            ->where([
                'date' => $this->date,
                'group_id' => $this->group_id,
                'para_id' => $this->para_id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['<>' , 'ids_id' , $this->ids_id])
            ->exists();

        if ($query) {
            $this->addError($attribute, _e('This group is busy at this time.'));
        }
    }

    public function fields()
    {
        $fields =  [
            'id',
            'timetable_id',
            'ids_id',
            'date',
            'faculty_id',
            'direction_id',
            'group_id',
            'edu_form_id',
            'edu_type_id',
            'building_id',
            'room_id',
            'week_id',
            'para_id',
            'course_id',
            'semestr_id',
            'group_type',
            'two_group',
            'type',
            'teacher_access_id',
            'user_id',
            'attend_status',

            'edu_semestr_id',
            'edu_year_id',
            'edu_semestr_subject_id',
            'subject_id',
            'order',
            'edu_plan_id',
            'subject_category_id',
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
            'profile',
            'attendance',
            'now',
            'subjectType',
            'isStudentBusy',
            'subjectCategory',
            'course',
            'attends',
            'studentAttends',
            'eduYear',
            'timeOption',
            'eduPlan',
            'child',
            'parent',
            'seminar',
            'selected',
            'selectedCount',
            'para',
            'room',
            'week',
            'subject',
            'semestr',
            'teacherAccess',
            'eduSemestr',
            'teacher',
            'building',
            'lecture',
            'all',
            'secondGroup',
            'timetable',
            'thisTimetable',
            'studentIsGroup',

            'attendStatus',
            'attendStudentStatus',
            'attendStudent',

            'eduSemestrExamsTypes',
            'eduSemestrSubject',
            'studentTimeTables',
            'group',
            'patok',
            'student',
            'twoGroups',
            'attendanceDates',
            'examControl',
            'isLesson',
            'faculty',
            'direction',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getAttendStatus()
    {
        $querys = TimetableDate::find()
            ->where([
                'date' => $this->date,
                'teacher_access_id' => $this->teacher_access_id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
        $queryCount = count($querys);
        $attentCount = 0;
        if ($queryCount > 0) {
            foreach ($querys as $query) {
                if ($query->attend_status == 1) {
                    $attentCount++;
                }
            }
        }

        if ($attentCount == 0) {
            return 0;
        } elseif ($attentCount == $queryCount) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Gets query for [[ExamControls]].
     *
     * @return \yii\db\ActiveQuery|ExamControlQuery
     */

    public function getAttendStudent()
    {
        $studentId = Yii::$app->request->post('student_id');
        if ($studentId == null) {
            return $this->hasOne(TimetableAttend::className(), ['timetable_date_id' => 'id'])
                ->where(['student_user_id' => current_user_id()]);
        }
        return $this->hasOne(TimetableAttend::className(), ['timetable_date_id' => 'id'])
            ->where(['student_id' => $studentId]);
    }

    public function getAttendStudentStatus()
    {
        $query = $this->attendStudent;
        if ($query) {
            return 1;
        }
        return 0;
    }

    public function getExamControl()
    {
        return $this->hasOne(ExamControl::className(), ['time_table_id' => 'id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getTimetable()
    {
        return $this->hasMany(Timetable::className(), ['ids' => 'ids_id'])->where(['is_deleted' => 0]);
    }

    public function getThisTimetable()
    {
        return $this->hasOne(Timetable::className(), ['id' => 'timetable_id'])->where(['is_deleted' => 0]);
    }


    public function getStudentIsGroup()
    {
        if ($this->two_group == 1) {
            $query = TimetableStudent::findOne([
                'ids_id' => $this->ids_id,
                'student_user_id' => Yii::$app->request->get('user_id'),
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($query) {
                if ($query->group_type == $this->group_type) {
                    return 1;
                }
            }
        }
        return 0;
    }

    public function getSecondGroup()
    {
        if ($this->two_group == 1) {
            return $this->hasOne(TimetableDate::className(), ['ids_id' => 'ids_id'])->where([
                'date' => $this->date,
                'para_id' => $this->para_id,
                'group_type' => 2,
                'is_deleted' => 0,
                'status' => 1
            ]);
        }
        return null;
    }

    public function getAll()
    {
        if ($this->two_group == 1) {
            return $this->hasMany(Timetable::className(), ['ids' => 'ids_id'])->where([
                'group_type' => 1,
                'is_deleted' => 0,
            ]);
        }
        return $this->hasMany(Timetable::className(), ['ids' => 'ids_id'])->where(['is_deleted' => 0]);
    }


    /**
     * Gets query for [
     * [SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    // o'quv yili id qo'shish kk
    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    public function getWeek()
    {
        return $this->hasOne(Week::className(), ['id' => 'week_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }




    public static function removeDay($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        TimetableDate::updateAll(['is_deleted' => 1 , 'status' => 0] , [
            'ids_id' => $model->ids_id,
            'date' => $model->date,
            'para_id' => $model->para_id,
            'status' => 1,
            'is_deleted' => 0,
            'attend_status' => 0
        ]);

        $timeTables = $model->timetable;
        if (count($timeTables) > 0) {
            foreach ($timeTables as $timeTable) {
                $timeTable->hour = $timeTable->hour - 1;
                $timeTable->update(false);
            }
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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
