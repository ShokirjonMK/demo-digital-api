<?php

namespace common\models\model;

use api\resources\ResourceTrait;
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
class TimeTable1 extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_NEW = 1;
    const STATUS_CHECKED = 2;
    const STATUS_CHANGED = 3;
    const STATUS_INACTIVE = 9;

    const LECTURE = 1;

    const CONSTANT = 0;

    const ODD_WEEKS = 1;

    const EVEN_WEEKS = 2;



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_table';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'type',
                    'teacher_access_id',
                    'edu_plan_id',
                    'edu_semestr_id',
                    'edu_semestr_subject_id',
                    'faculty_id',
                    'direction_id',
                    'building_id',
                    'room_id',
                    'week_id',
                    'para_id',
                    'language_id',
                    'subject_category_id',
                ], 'required'
            ],
            [
                [
                    'ids',
                    'type',
                    'teacher_access_id',
                    'room_id',
                    'para_id',
                    'group_id',
                    'course_id',
                    'semestr_id',
                    'edu_year_id',
                    'edu_form_id',
                    'edu_type_id',
                    'subject_id',
                    'language_id',
                    'user_id',
                    'user_id',
                    'edu_plan_id',
                    'edu_semestr_id',
//                    'fall_spring',
                    'subject_id',
                    'edu_semestr_subject_id',
                    'building_id',
                    'two_groups',
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
            [
                ['start_study','end_study'] , 'safe'
            ],
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
                ['language_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']
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
            [
                ['teacher_access_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']
            ],
            [
                ['user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']
            ],
//            [
//                ['time_option_id'], 'exist',
//                'skipOnError' => true, 'targetClass' => TimeOption::className(), 'targetAttribute' => ['time_option_id' => 'id']
//            ],

            [
                'edu_semestr_id' , 'validateEduSemestrId'
            ],

            [
                'subject_category_id' , 'validateCategoryId'
            ],
            [
                'edu_semestr_subject_id', 'validateSubjectId'
            ],
        ];
    }

    public function validateEduSemestrId($attribute, $params)
    {
        $EduSemestrEduPlanId = EduSemestr::findOne($this->edu_semestr_id)->edu_plan_id;
        if ($EduSemestrEduPlanId != $this->edu_plan_id) {
            $this->addError($attribute, _e('Edu Semestr ID does not match the Edu Plan associated with the edu plan.'));
        }
    }

    public function validateSubjectId($attribute, $params)
    {
        $eduSemestrSubject = EduSemestrSubject::findOne([
            'subject_id' => $this->subject_id,
            'edu_semestr_id' => $this->group->activeEduSemestr->id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($eduSemestrSubject == null) {
            $this->addError($attribute, _e('This subject is not available in the group active semester.'));
        }
    }

    public function validateCategoryId($attribute, $params)
    {
        if ($this->subject_category_id == self::LECTURE) {
            if ($this->two_groups == 1) {
                $this->addError($attribute, _e('It is not possible to divide the group into two groups during the lecture.'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_access_id' => 'Teacher Access ID',
            'room_id' => 'Room ID',
            'para_id' => 'Para ID',
            'time_option_id' => 'time_option_id',
            'course_id' => 'Course ID',
            'edu_plan_id' => 'edu_plan_id',
            'building_id' => 'building_id',
            'lecture_id' => 'Lecture ID',
            'semestr_id' => 'Semestr ID',
            'parent_id' => 'Parent ID',
            'subject_category_id ' => 'Subject Category ID',
            'edu_year_id' => 'Edu Year ID',
            'edu_semestr_id' => 'Edu Semester ID',
            'subject_id' => 'Subject ID',
            'language_id' => 'Languages ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'ids',
            'user_id',
            'teacher_access_id',

            'faculty_id',
            'direction_id',
            'group_id',
            'edu_form_id',
            'edu_type_id',
            'building_id',
            'room_id',
            'week_id',
            'para_id',
            'type',
            'course_id',
            'semestr_id',
            'two_groups',
            'group_type',


            'edu_semestr_id',
//            'fall_spring',
            'edu_year_id',
            'edu_semestr_subject_id',
            'subject_id',
            'language_id',
            'order',
            'edu_plan_id',
            'subject_category_id',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'start_study',
            'end_study',
//            'group'

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            /** */
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
            'language',
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
            /** */

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

    /**
     * Gets query for [[ExamControls]]. 
     * 
     * @return \yii\db\ActiveQuery|ExamControlQuery 
     */
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

    public function getPatok() {
        $model = new TimeTable1();
        if ($this->subject_category_id == self::LECTURE) {
            $query = $model::find()->where([
                'ids' => $this->ids,
                'status' => 1,
                'is_deleted' => 0,
            ])
                ->andWhere(["!=" , "id" , $this->id])
                ->all();
            if (isset($query)) {
                return $query;
            }
        }
        return null;
    }

    public function getTwoGroups()
    {
        if ($this->two_groups == 1) {
            $time_table = TimeTable1::find()
                ->where([
                    'group_id' => $this->group_id,
                    'edu_plan_id' => $this->edu_plan_id,
                    'edu_year_id' => $this->edu_year_id,
                    'edu_semestr_id' => $this->edu_semestr_id,
                    'faculty_id' => $this->faculty_id,
                    'subject_id' => $this->subject_id,
                    'direction_id' => $this->direction_id,
                    'week_id' => $this->week_id,
                    'para_id' => $this->para_id,
                    'two_groups' => $this->two_groups,
                    'subject_category_id' => $this->subject_category_id,
                    'language_id' => $this->language_id,
                    'type' => $this->type,
                ])
                ->andWhere(["!=" , "id" , $this->id])
                ->one();
            if (isset($time_table)) {
                return $time_table;
            }
        }
        return null;
    }

    public function getIsLesson() {
        $thisDay = new \DateTime(date("Y-m-d"));
        $week =  $thisDay->format("W");
        $year =  $thisDay->format("Y");
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $week_start = strtotime($dto->format('Y-m-d'));
        $dto->modify('+6 days');
        $week_end = strtotime($dto->format('Y-m-d'));
        $attendance = self::getAttendanceDates();
        foreach ($attendance as $key => $value) {
            $atDate = strtotime($key);
            if ($atDate >= $week_start && $atDate <= $week_end) {
                // dd(date("Y-m-d" , $week_start) . "-" . date("Y-m-d" , $week_end) . " - " . date("Y-m-d" , $atDate));
                return 1;
            }
        }
        return 0;
    }

    public function getAttendanceDates()
    {
        $dateFromString = $this->start_study;
        $dateToString = $this->end_study;
        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];
        if ($dateFrom > $dateTo) {
            return $dates;
        }
        if ($this->type != self::EVEN_WEEKS) {
            if ($this->week_id != $dateFrom->format('N')) {
                $dateFrom->modify('next ' . $this->dayName()[$this->week_id]);
            }
        }
        $pair = true;
        while ($dateFrom <= $dateTo) {
            if ($this->type == self::EVEN_WEEKS) {
                if ($pair) {
                    $dateFrom->modify('+1 week');
                    $pair = false;
                } else {
                    $dateFrom->modify('+2 week');
                }
            }
            $dates[$dateFrom->format('Y-m-d')] = $this->getAttend($dateFrom->format('Y-m-d'));
            if ($this->type == self::CONSTANT) {
                $dateFrom->modify('+1 week');
            }
            if ($this->type == self::ODD_WEEKS) {
                $dateFrom->modify('+2 week');
            }
        }
        return $dates;
    }

    public function dayName()
    {
        return [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ];
    }

    public function getAttendance($date = null)
    {
        $date = $date ?? Yii::$app->request->get('date');

        $date = date('Y-m-d' , strtotime($date));

        if (isset($date) && $date != null) {
//            dd($date."----".date("Y-m-d", strtotime($this->eduSemestr->start_date)) . "-----" . date("Y-m-d", strtotime($this->eduSemestr->end_date)));
            if (!($date >= date("Y-m-d", strtotime($this->eduSemestr->start_date)) && $date <= date("Y-m-d", strtotime($this->eduSemestr->end_date)))) {
                return 0;
            }
            if ($date > date('Y-m-d')) {
                return 0;
            }
//            // if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i', strtotime($date))) && ($this->para->end_time >  date('H:i', strtotime($date)))) {
//            /* dd([
//                $date,
//                date('w', strtotime($date)),
//                date('H:i', strtotime($date)),
//                $this->para->start_time
//            ]); */
//            // if ($this->eduSemestr->start_date <= $date && $date <= $this->eduSemestr->end_date)
//
//            // if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i', strtotime($date)))) {

            if ($date == date('Y-m-d')) {
                if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i'))) { // bir parani o'zida davomat qilish kerak bolsa yana bitta shart yoziladi.
                    return 1;
                } else {
                    return 0;
                }
            } else {
                if ($this->week_id == date('w', strtotime($date))) {
                    return 1;
                } else {
                    return 0;
                }
            }

            return 0;
        }

        // if (($this->week_id == date('w')) && ($this->para->start_time <  date('H:i')) && ($this->para->end_time >  date('H:i'))) {
        if (($this->week_id == date('w')) && ($this->para->start_time <  date('H:i'))) {
            return 1;
        } else {
            return 0;
        }

        return 0;
    }

    public function getNow()
    {
        return [
            time(),
            date('Y-m-d H:i:s'),
            date('Y-m-d'),
            date('H:i'),
            date('m'),
            date('M'),
            date('w'),
            date('W'),
            date('w', strtotime('2022-10-05')),
        ];

        return [
            $this->para->start_time,
            date('H:i'),
            ($this->para->start_time <  date('H:i')) ? 1 : 0,
            $this->para->end_time,
            ($this->para->end_time >  date('H:i')) ? 1 : 0,

        ];

        if ($this->week_id == date('w')) {
            return 1;
        }

        if ($this->para->start_time <  date('H:i')) {
            return 1;
        }
    }

    public function getSubjectType()
    {
        // return 1;
        $eduSemester = EduSemestrSubject::findOne(
            [
                'subject_id' => $this->subject_id,
                'edu_semestr_id' => $this->edu_semestr_id,
            ]
        );

        if ($eduSemester) {
            return $eduSemester->subject_type_id;
        } else {
            return null;
        }
    }

    public function getIsStudentBusy()
    {
        if (isRole('student')) {
            $timeTableSameBusy = TimeTable1::find()->where([
                'edu_semestr_id' => $this->edu_semestr_id,
                'edu_year_id' => $this->edu_year_id,
                'semestr_id' => $this->semestr_id,
                'para_id' => $this->para_id,
                'week_id' => $this->week_id,
            ])->select('id');

            $timeTableSelected = StudentTimeTable::find()
                ->where(['in', 'time_table_id', $timeTableSameBusy])
                ->andWhere(['student_id' => self::student()])
                ->all();

            if (count($timeTableSelected) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getStudent()
    {
        if ($this->two_groups == 1) {
            return Student::find()->where([
                'type' => $this->group_type,
                'group_id' => $this->group_id,
                'status' => 10,
                'is_deleted' => 0,
            ])->all();
        } else {
            $data = [];
            if ($this->subject_category_id == self::LECTURE) {
                $query = TimeTable1::find()->where([
                    'ids' => $this->ids,
                    'status' => 1,
                    'is_deleted' => 0,
                ])->all();
                if (isset($query)) {
                    foreach ($query as $item) {
                        $data[] = $item->group_id;
                    }
                }
            } else {
                return Student::find()->where([
                    'group_id' => $this->group_id,
                    'status' => 10,
                    'is_deleted' => 0,
                ])->all();
            }
            return Student::find()->where([
                'group_id' => $data,
                'status' => 10,
                'is_deleted' => 0,
            ])->all();
        }
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

    /**
     * Gets query for [[Attends]].
     *
     * @return \yii\db\ActiveQuery|AttendQuery
     */
    public function getAttends()
    {
        $date = Yii::$app->request->get('date');

        if (isset($date)) {
            $date = date("Y-m-d", strtotime($date));
            return $this->hasMany(Attend::className(), ['time_table_id' => 'id'])->onCondition(['date' => $date])->orderBy('date');
        }

        return $this->hasMany(Attend::className(), ['time_table_id' => 'id'])->orderBy('date');
    }

    public function getAttend($date)
    {
        $date = date("Y-m-d", strtotime($date));

        $attend = Attend::find()
            ->where(['date' => $date])
            ->andWhere(['time_table_id' => TimeTable1::find()
                ->select('id')
                ->where([
                    'ids' => $this->ids,
//                    'two_groups' => $this->two_groups,
                    'group_type' => $this->group_type,
                    'status' => 1,
                    'is_deleted' => 0
                ])])
            ->all();
        if (count($attend) > 0) {
            return $attend;
        }
        return null;
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        if (isRole('student')) {
            return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => $this->student()]);
        }

        $filter = json_decode(str_replace("'", "", Yii::$app->request->get('filter')));
        if (isset($filter->student_id)) {
            return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => $filter->student_id]);
        }
        return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getChild()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id']);
    }

    public function getEduSemestrExamsTypes()
    {
        return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'edu_semestr_subject_id']);
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    public function getSeminar()
    {
        return $this->hasMany(self::className(), ['lecture_id' => 'id'])->onCondition(['parent_id' => null]);
    }

    public function getLecture()
    {
        return $this->hasOne(self::className(), ['id' => 'lecture_id'])->onCondition(['parent_id' => null]);
    }

    public function getSelected()
    {
        if (isRole('student')) {

            $studentTimeTable = StudentTimeTable::find()
                ->where([
                    'time_table_id' => $this->id,
                    'student_id' => $this->student()
                ])
                ->all();

            if (count($studentTimeTable) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        $studentTimeTable = StudentTimeTable::find()->where(['time_table_id' => $this->id])->all();
        return count($studentTimeTable);
    }

    public function getStudentTimeTable()
    {
        return $this->hasOne(StudentTimeTable::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => self::student()]);
    }



    public function getSelectedCount()
    {
        $studentTimeTable = StudentTimeTable::find()->where(['time_table_id' => $this->id])->all();
        return count($studentTimeTable);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id'])->select(['name', 'lang_code']);
    }

    /**
     * Gets query for [[Para]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getWeek()
    {
        return $this->hasOne(Week::className(), ['id' => 'week_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[TeacherAccess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    /**
     * Gets query for [[profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return Profile::find()
            ->select(['user_id', 'first_name', 'last_name', 'middle_name'])
            ->where(['user_id' => $this->user_id ?? null])
            ->one();
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

    /**
     * Gets query for [[Building ]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getBuilding()
    {
        return Building::find()->where(['id' => $this->room->building_id])->one();
    }

    public static function createItem($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $ids = 1;
        $idsNumber = TimeTable1::find()->orderBy('id desc')->one();
        if (isset($idsNumber)) {
            $ids = $idsNumber->id;
        }

        $validPost = TimeTable1::validPost($post);
        if (count($validPost) != 0) {
            $transaction->rollBack();
            return simplify_errors($validPost);
        }

        if (isset($post['groups'])) {
            $post['groups'] = str_replace("'", "", $post['groups']);
            $groups = json_decode(str_replace("'", "", $post['groups']));
            $validGroups = TimeTable1::validGroups($post , $groups);
            if (count($validGroups) != 0) {
                $transaction->rollBack();
                return simplify_errors($validGroups);
            }
        } else {
            $errors['groups'] =  "groups is required";
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $validSiklTeacher = true;
        $validSiklTimeTable = true;
        foreach ($groups as $groupItem) {
            foreach ($groupItem as $groupId) {
                $model = new TimeTable1();
                $model->load($post, '');
                $model->group_id = $groupId;
                $model->ids = $ids;

                $model = TimeTable1::loadTimeTable($model);

                if (($model->subject_category_id != self::LECTURE) && $model->two_groups == 1) {
                    $model->group_type = 1;
                }

                // Guruhni aynan shu kunda shu parada boshqa darsi bor yoki yo'qligini tekshirish
                $validGroup = TimeTable1::validGroup($model);
                if ($validGroup) {
                    $errors[] = _e("ID= [". $model->group_id ." ] This group has a lesson on this day at this hour");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                // Guruhni aynan shu kunda shu parada boshqa darsi bor yoki yo'qligini tekshirish

                $validTimeTable = TimeTable1::validTimeTable($model);
                if ($validTimeTable) {
                    if ($model->subject_category_id == self::LECTURE) {
                        if ($validSiklTimeTable) {
                            $errors['room_id'] = _e("This Room and Para is busy for this Edu Year's semestr");
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                        $validSiklTimeTable = false;
                    } else {
                        $errors['room_id'] = _e("This Room and Para is busy for this Edu Year's semestr");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                } else {
                    $validSiklTimeTable = false;
                }

                $validTimeTableTeacher = TimeTable1::validTimeTableTeacher($model);
                if ($validTimeTableTeacher) {
                    if ($model->subject_category_id == self::LECTURE) {
                        if ($validSiklTeacher) {
                            $errors['teacher_access_id'] =  _e("This Teacher in this Para are busy for this Edu Year's semestr");
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                        $validSiklTeacher = false;
                    } else {
                        $errors['teacher_access_id'] =  _e("This Teacher in this Para are busy for this Edu Year's semestr");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                } else {
                    $validSiklTeacher = false;
                }

                if (!($model->validate())) {
                    $errors[] = $model->errors;
                }

                if (!$model->save()) {
                    $errors[] = _e("ID= ".$model->group_id." group data not saved");
                }



                if (($model->subject_category_id != self::LECTURE) && $model->two_groups == 1) {
                    $newModel = new TimeTable1();
                    $newModel->load($post, '');
                    $newModel->group_id = $groupId;
                    $newModel->group_type = 2;
                    $newModel->ids = $ids;

                    $newModel = TimeTable1::loadTimeTable($newModel);

                    if (isset($post['second_room_id'])) {
                        $newModel->room_id = $post['second_room_id'];
                        $newModel->building_id = $newModel->room->building_id;
                    } else {
                        $errors[] = ['second_room_id' => _e("Second Room not found")];
                    }

                    if (isset($post['second_teacher_access_id'])) {
                        $newModel->teacher_access_id = $post['second_teacher_access_id'];
                        $newModel->user_id = $newModel->teacherAccess->user_id;
                    } else {
                        $errors[] = ['second_teacher_access_id' => _e("Second Teacher not found")];
                    }


                    $validTimeTable = TimeTable1::validTimeTable($newModel);
                    if ($validTimeTable) {
                        $errors['second_room_id'] =  _e("For the second group This Room and Para is busy for this Edu Year's semestr");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }

                    $validTimeTableTeacher = TimeTable1::validTimeTableTeacher($newModel , $model);
                    if ($validTimeTableTeacher) {
                        $errors['second_teacher_access_id'] =  _e("For the second group This Teacher in this Para are busy for this Edu Year's semestr");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }

                    if (!($newModel->validate())) {
                        $errors[] = $newModel->errors;
                    }

                    if (!$newModel->save()) {
                        $errors[] = _e("The data of the second group was not saved");
                    }
                }
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

    public static function createAddGroup($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Time Table dan kelgan id bo'yicha malumotlarni olish
        if (isset($post['time_table_id']) && $post['time_table_id'] != null) {
            $timeTable = TimeTable1::findOne($post['time_table_id']);
            if ($timeTable == null) {
                $errors[] = ['time_table_id' => 'Time Table Id not found.'];
            }
        } else {
            $errors[] = ['time_table_id' => 'Time Table Id required.'];
        }

        // Guruhdan kelgan id bo'yicha ma'lumotlarni olish.
        if (isset($post['group_id']) && $post['group_id'] != null) {
            $group = Group::findOne($post['group_id']);
            if ($group == null) {
                $errors[] = ['group_id' => 'Group Id not found.'];
            }
        } else {
            $errors[] = ['group_id' => 'Group Id required.'];
        }

        if (count($errors) != 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($timeTable->subject_category_id != self::LECTURE) {
            $errors[] = ['subject_category_id' => 'You can only add to lecture.'];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $eduSemestrSubject = EduSemestrSubject::findOne([
            'subject_id' => $timeTable->subject_id,
            'edu_semestr_id' => $group->activeEduSemestr->id,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if ($eduSemestrSubject == null) {
            $errors[] = ['edu_semestr_subject_id' => 'This subject is not available in the group active semester.'];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $timeTableGroupTime = TimeTable1::find()
            ->where([
                'group_id' => $group->id,
                'edu_year_id' => $timeTable->edu_year_id,
                'week_id' => $timeTable->week_id,
                'para_id' => $timeTable->para_id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->all();
        if (count($timeTableGroupTime) != 0) {
            $errors[] = ['group_id' => 'This group is currently busy.'];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model = new TimeTable1();
        $model->ids = $timeTable->ids;
        $model->type = $timeTable->type;
        $model->group_id = $group->id;
        $model->subject_id = $timeTable->subject_id;
        $model->subject_category_id = $timeTable->subject_category_id;
        $model->faculty_id = $group->faculty_id;
        $model->direction_id = $group->direction_id;
        $model->edu_plan_id = $group->edu_plan_id;
        $model->edu_semestr_id = $group->activeEduSemestr->id;
        $model->edu_year_id = $timeTable->edu_year_id;
        $model->edu_form_id = $group->eduPlan->edu_form_id;
        $model->edu_type_id = $group->eduPlan->edu_type_id;
        $model->teacher_access_id = $timeTable->teacher_access_id;
        $model->user_id = $timeTable->user_id;
        $model->week_id = $timeTable->week_id;
        $model->para_id = $timeTable->para_id;
        $model->building_id = $timeTable->building_id;
        $model->room_id = $timeTable->room_id;
        $model->course_id = $timeTable->course_id;
        $model->semestr_id = $timeTable->semestr_id;
        $model->language_id = $timeTable->language_id;
        $model->group_type = $timeTable->group_type;
        $model->two_groups = $timeTable->two_groups;
        $model->start_study = $timeTable->start_study;
        $model->end_study = $timeTable->end_study;
        $model->edu_semestr_subject_id = $eduSemestrSubject->id;
        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
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

    public static function validGroups($post, $groups) {
        $errors = [];
        foreach ($groups as $groupItem) {
            if (isset($post['subject_category_id']) != self::LECTURE) {
                if (count($groupItem) != 1) {
                    $errors['groups_count'] = [_e('You cannot add groups ') . count($groupItem)];
                    return $errors;
                }
            }

            $StudentCount = 0;
            foreach ($groupItem as $groupId) {
                // Ushbu guruh rostdan ham shu yo'nalishda bor yoki yo'qligiga tekshiradi
                $group = Group::findOne([
                    'id' => $groupId,
                    'status' => 1,
                    'is_deleted' => 0
                ]);
                if (!isset($group)) {
                    $errors['groups'] = [_e("There is no group with ID") . " = ". $groupId];
                } else {
                    $students = Student::find()->where([
                        'group_id' => $groupId,
                        'is_deleted' => 0,
                    ])->all();
                    $StudentCount = $StudentCount + count($students);
                }
                // Ushbu guruh rostdan ham shu yo'nalishda bor yoki yo'qligiga tekshiradi
            }

            $room = Room::findOne($post['room_id']);
            if ($room->capacity <= $StudentCount) {
                $errors['room'] = [_e("The total number of students is greater than the room capacity")];
            }

        }
        return $errors;
    }

    public static function validPost($post) {
        $errors = [];

//        if (isset($post['subject_category_id']) && $post['subject_category_id'] == self::LECTURE) {
//            if (isset($post['two_groups']) && $post['two_groups'] == 1) {
//                $errors['subject_category_id'] = "It is not possible to divide the group into two groups during the lecture";
//            }
//        }

        if (isset($post['two_groups']) && $post['two_groups'] == 1)
        {
            if (isset($post['second_room_id'])) {
                $secondRoom = Room::findOne($post['second_room_id']);
                if (!isset($secondRoom)) {
                    $errors['second_room_id'] = "second_room_id is invalid";
                }
            } else {
                $errors['second_room_id'] =  "second_room_id is required";
            }
        }

        return $errors;
    }

    public static function validEduSemestrSubject($model) {
        $EduSemestrSubject = EduSemestrSubject::findOne([
            'id' => $model->edu_semestr_subject_id,
            'edu_semestr_id' => $model->edu_semestr_id,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (!$EduSemestrSubject) {
            return false;
        }
        return $EduSemestrSubject;
    }

    public static function validTimeTable($model) {

        $start_date = date("Y-m-d", strtotime($model->eduSemestr->start_date."-1 day"));
        $end_date = date("Y-m-d", strtotime($model->eduSemestr->end_date."+1 day"));

        if ($model->type == self::CONSTANT) {
            $timeTable = TimeTable1::find()
                ->where([
                    'edu_year_id' => $model->eduSemestr->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->all();

            if (count($timeTable) > 0) {
                return true;
            }
            return false;
        }
        if ($model->type == self::EVEN_WEEKS) {
            $timeTable = TimeTable1::find()
                ->where([
                    'type' => self::CONSTANT,
                    'edu_year_id' => $model->eduSemestr->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTable)) {
                return true;
            }
            $timeTableTwo = TimeTable1::find()
                ->where([
                    'type' => self::EVEN_WEEKS,
                    'edu_year_id' => $model->eduSemestr->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTableTwo)) {
                return true;
            }
            return false;
        }
        if ($model->type == self::ODD_WEEKS) {
            $timeTable = TimeTable1::find()
                ->where([
                    'type' => self::CONSTANT,
                    'edu_year_id' => $model->eduSemestr->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTable)) {
                return true;
            }
            $timeTableTwo = TimeTable1::find()
                ->where([
                    'type' => self::ODD_WEEKS,
                    'edu_year_id' => $model->eduSemestr->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTableTwo)) {
                return true;
            }
            return false;
        }

    }

    public static function validTimeTableUpdate($model) {

        $start_date = date("Y-m-d", strtotime($model->eduSemestr->start_date."-1 day"));
        $end_date = date("Y-m-d", strtotime($model->eduSemestr->end_date."+1 day"));

        if ($model->type == self::CONSTANT) {
            $timeTable = TimeTable1::find()
                ->where([
                    'edu_year_id' => $model->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTable)) {
                if ($timeTable->id == $model->id) {
                    return false;
                }
                if ($timeTable->group_id != $model->group_id) {
                    return true;
                }
            }
            return false;
        }
        if ($model->type == self::EVEN_WEEKS) {
            $timeTable = TimeTable1::find()
                ->where([
                    'type' => self::CONSTANT,
                    'edu_year_id' => $model->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTable)) {
                if ($timeTable->id == $model->id) {
                    return false;
                }
                if ($timeTable->group_id != $model->group_id) {
                    return true;
                }
            }
            $timeTableTwo = TimeTable1::find()
                ->where([
                    'type' => self::EVEN_WEEKS,
                    'edu_year_id' => $model->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTableTwo)) {
                if ($timeTable->id == $model->id) {
                    return false;
                }
                if ($timeTableTwo->group_id != $model->group_id) {
                    return true;
                }
            }
            return false;
        }
        if ($model->type == self::ODD_WEEKS) {
            $timeTable = TimeTable1::find()
                ->where([
                    'type' => self::CONSTANT,
                    'edu_year_id' => $model->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTable)) {
                if ($timeTable->id == $model->id) {
                    return false;
                }
                if ($timeTable->group_id != $model->group_id) {
                    return true;
                }
            }
            $timeTableTwo = TimeTable1::find()
                ->where([
                    'type' => self::ODD_WEEKS,
                    'edu_year_id' => $model->edu_year_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                ->one();
            if (isset($timeTableTwo)) {
                if ($timeTable->id == $model->id) {
                    return false;
                }
                if ($timeTableTwo->group_id != $model->group_id) {
                    return true;
                }
            }
            return false;
        }

    }

    public static function validTimeTableTeacher($model , $test= null) {

        $start_date = date("Y-m-d", strtotime($model->eduSemestr->start_date."-1 day"));
        $end_date = date("Y-m-d", strtotime($model->eduSemestr->end_date."+1 day"));
        $checkTeacherTimeTable = TimeTable1::find()
            ->where([
                'edu_year_id' => $model->edu_year_id,
                'building_id' => $model->building_id,
                'week_id' => $model->week_id,
                'para_id' => $model->para_id,
                'teacher_access_id' => $model->teacher_access_id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
            ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
            ->one();

        if (isset($checkTeacherTimeTable)) {
            return true;
        }
        return false;
    }

    // Guruhni parada darsi bor yoki yo'qligini tekshiradi.
    public static function validGroup($model) {

        $TimeTableGroup = TimeTable1::findOne([
            'group_id' => $model->group_id,
            'edu_year_id' => $model->eduSemestr->edu_year_id,
            'week_id' => $model->week_id,
            'para_id' => $model->para_id,
            'status' => 1,
            'is_deleted' => 0,
        ]);

        if (isset($TimeTableGroup)) {
            if ($TimeTableGroup->type == 0) {
                return true;
            }
            if ($model->type != 0) {
                if ($model->type != $TimeTableGroup->type) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public static function loadTimeTable($model) {
        $start_date = date("Y-m-d", strtotime($model->eduSemestr->start_date));
        $end_date = date("Y-m-d", strtotime($model->eduSemestr->end_date));
        $model->edu_plan_id = $model->group->edu_plan_id;
        $model->faculty_id = $model->group->faculty_id;
        $model->direction_id = $model->group->direction_id;
        $model->edu_semestr_id = $model->group->activeEduSemestr->id;
        $model->language_id = $model->group->language_id;
        $model->subject_id = $model->eduSemestrSubject->subject_id;
        $model->semestr_id = $model->eduSemestr->semestr_id;
        $model->course_id = $model->eduSemestr->course_id;
        $model->edu_year_id = $model->eduSemestr->edu_year_id;
        $model->edu_form_id = $model->eduSemestr->edu_form_id;
        $model->edu_type_id = $model->eduSemestr->edu_type_id;
        $model->start_study = $start_date;
        $model->end_study = $end_date;
        $model->building_id = $model->room->building_id;
        $model->user_id = $model->teacherAccess->user_id;

        $eduSemestrSubject = EduSemestrSubject::findOne([
            'subject_id' => $model->subject_id,
            'edu_semestr_id' => $model->edu_semestr_id,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if ($eduSemestrSubject != null) {
            $model->edu_semestr_subject_id = $eduSemestrSubject->id;
        }

        return $model;
    }

    public static function updateItem($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $validPost = TimeTable1::validPost($post);
        if (count($validPost) != 0) {
            $transaction->rollBack();
            return simplify_errors($validPost);
        }
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        $sikl = true;
        $sikl2 = true;
        if ($model->subject_category_id == 1) {
            $allModel = TimeTable1::find()->where([
                'ids' => $model->ids,
                'status' => 1,
                'is_deleted' => 0,
            ])->all();
            foreach ($allModel as $itemModel) {

                $itemModel->load($post, '');

                $itemModel = TimeTable1::loadTimeTable($itemModel);

                $start_date = date("Y-m-d", strtotime($itemModel->eduSemestr->start_date."-1 day"));
                $end_date = date("Y-m-d", strtotime($itemModel->eduSemestr->end_date."+1 day"));
                // Guruhni shu parada dars bor yoki yo'qligini tekshiradi.

                $TimeTableGroup = TimeTable1::findOne([
                    'group_id' => $itemModel->group_id,
                    'edu_year_id' => $model->edu_year_id,
                    'week_id' => $itemModel->week_id,
                    'para_id' => $itemModel->para_id,
                    'status' => 1,
                    'is_deleted' => 0,
                ]);
                if (isset($TimeTableGroup)) {
                    if ($TimeTableGroup->id != $itemModel->id) {
                        $errors[] = _e("ID= [". $model->group_id ." ] This group has a lesson on this day at this hour");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }

                $validTimeTable = TimeTable1::validTimeTableUpdate($itemModel);
                if ($validTimeTable) {
                    if ($sikl) {
                        $errors[] = _e("This Room and Para is busy for this Edu Year's semestr");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }

                $sikl = false;
                if ($sikl2) {
                    $checkTeacherTimeTable = TimeTable1::find()
                        ->where([
                            'edu_year_id' => $model->edu_year_id,
                            'building_id' => $itemModel->building_id,
                            'week_id' => $itemModel->week_id,
                            'para_id' => $itemModel->para_id,
                            'teacher_access_id' => $itemModel->teacher_access_id,
                            'status' => 1,
                            'is_deleted' => 0
                        ])
                        ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                        ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                        ->one();
                    if (isset($checkTeacherTimeTable)) {
                        if ($checkTeacherTimeTable->id != $itemModel->id) {
                            $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                    }
                }
                $sikl2 = false;

                if (!($itemModel->validate())) {
                    $errors[] = $model->errors;
                }

                if (!$itemModel->save()) {
                    $errors[] = _e("ID= ".$model->group_id." group data not saved");
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

        $two_groups = $model->two_groups;

        if ($two_groups == 1) {
            $twoGroups = TimeTable1::find()->where([
                'group_id' => $model->group_id,
                'ids' => $model->ids,
                'type' => $model->type,
                'two_groups' => $model->two_groups,
                'is_deleted' => 0,
                'status' => 1,
            ])
                ->andWhere(['!=' , "id" , $model->id])
                ->one();
            if ($post['two_groups'] != 1 && isset($twoGroups)) {
                $twoGroups->is_deleted = 1;
                $twoGroups->save(false);
            }
        }

        $model->load($post, '');
        $model = TimeTable1::loadTimeTable($model);
        $start_date = date("Y-m-d", strtotime($model->eduSemestr->start_date."-1 day"));
        $end_date = date("Y-m-d", strtotime($model->eduSemestr->end_date."+1 day"));
        // Guruhni shu parada dars bor yoki yo'qligini tekshiradi.
        $TimeTableGroup = TimeTable1::findOne([
            'group_id' => $model->group_id,
            'edu_year_id' => $model->edu_year_id,
            'week_id' => $model->week_id,
            'para_id' => $model->para_id,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (isset($TimeTableGroup)) {
            if ($TimeTableGroup->id != $model->id) {
                $errors[] = _e("ID= [". $model->group_id ." ] This group has a lesson on this day at this hour");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        $validEduSemestrSubject = TimeTable1::validEduSemestrSubject($model);
        if (!$validEduSemestrSubject) {
            $errors[] = _e("This subject will not be taught this semester");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // Faculty & Direction & Edu Plan & Edu Semestr & Edu Semestr Subject => ID verification

        $validTimeTable = TimeTable1::validTimeTableUpdate($model);
        if ($validTimeTable) {
            $errors[] = _e("This Room and Para is busy for this Edu Year's semestr");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $checkTeacherTimeTable = TimeTable1::find()
            ->where([
                'edu_year_id' => $model->edu_year_id,
                'building_id' => $model->building_id,
                'week_id' => $model->week_id,
                'para_id' => $model->para_id,
                'teacher_access_id' => $model->teacher_access_id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
            ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
            ->one();

        if (isset($checkTeacherTimeTable)) {
            if ($checkTeacherTimeTable->id != $model->id) {
                $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (!$model->save()) {
            $errors[] = _e("ID= ".$model->group_id." group data not saved");
        } else {
            if ($two_groups == 1) {

                if ($post['two_groups'] == 1 && isset($twoGroups)) {

                    $twoGroups->load($post, '');
                    $twoGroups = TimeTable1::loadTimeTable($twoGroups);

                    if (isset($post['second_room_id'])) {
                        $twoGroups->room_id = $post['second_room_id'];
                        $twoGroups->building_id = $twoGroups->room->building_id;
                    }

                    if (isset($post['second_teacher_access_id'])) {
                        $twoGroups->teacher_access_id = $post['second_teacher_access_id'];
                        $twoGroups->user_id = $twoGroups->teacherAccess->user_id;
                    }

                    $validTimeTable = TimeTable1::validTimeTableUpdate($twoGroups);
                    if ($validTimeTable) {
                        $errors[] = _e("This Room and Para is busy for this Edu Year's semestr");
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }

                    $checkTeacherTimeTable = TimeTable1::find()
                        ->where([
                            'edu_year_id' => $model->edu_year_id,
                            'building_id' => $twoGroups->building_id,
                            'week_id' => $twoGroups->week_id,
                            'para_id' => $twoGroups->para_id,
                            'teacher_access_id' => $twoGroups->teacher_access_id,
                            'status' => 1,
                            'is_deleted' => 0
                        ])
                        ->andWhere([ 'between', 'start_study', $start_date, $end_date ])
                        ->andWhere([ 'between', 'end_study', $start_date, $end_date ])
                        ->one();

                    if (isset($checkTeacherTimeTable)) {
                        if ($checkTeacherTimeTable->id != $twoGroups->id) {
                            $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                    }

                    if (!($twoGroups->validate())) {
                        $errors[] = $twoGroups->errors;
                    }
                    if (!$twoGroups->save()) {
                        $errors[] = _e("ID= ".$twoGroups->group_id." second group data not saved");
                    }

                }

            }

            if ($two_groups == 0 && isset($post['two_groups']) && $post['two_groups'] == 1) {

                $newModel = new TimeTable1();
                $newModel->load($post, '');
                $newModel->group_id = $model->group_id;
                $newModel->group_type = 2;
                $newModel->room_id = $post['second_room_id'];
                $newModel->teacher_access_id = $post['second_teacher_access_id'];
                // $newModel->building_id = $newModel->room->building_id;
                // $newModel->user_id = $newModel->teacherAccess->user_id;
                $newModel->ids = $model->ids;
                $newModel = TimeTable1::loadTimeTable($newModel);

                $validTimeTable = TimeTable1::validTimeTable($newModel);
                if ($validTimeTable) {
                    $errors[] = _e("For the second group This Room and Para is busy for this Edu Year's semestr");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }

                $validTimeTableTeacher = TimeTable1::validTimeTableTeacher($newModel);
                if ($validTimeTableTeacher) {
                    $errors[] = _e("For the second group This Teacher in this Para are busy for this Edu Year's semestr");
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }

                if (!($newModel->validate())) {
                    $errors[] = $newModel->errors;
                }
                if (!$newModel->save()) {
                    $errors[] = _e("The data of the second group was not saved");
                }
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
