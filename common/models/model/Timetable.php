<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "time_table".
 *
 * @property int $id
 * @property int $teacher_access_id
 * @property int $room_id
 * @property int $para_id
 * @property int $course_id
 * @property int $semester_id
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
class TimeTable extends \yii\db\ActiveRecord
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

    public static $setFirstRecordAsKeys = true;

    public static $getOnlySheet;
    public static $leaveRecordByIndex = [];

    const UPLOADS_FOLDER = 'uploads/import/time_table/';
    public $excel;
    public $excelFileMaxSize = 1024 * 1024 * 10; // 3 Mb
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
                    // 'teacher_access_id',
                    'room_id',
                    'para_id',
                    'subject_id',
                    'language_id',
                    'subject_category_id',
                    'edu_semestr_subject_id',
                ],
                'required'
            ],
            [
                [
                    'teacher_access_id',
                    'room_id',
                    'parent_id',
                    'lecture_id',
                    'para_id',
                    'course_id',
                    'semester_id',
                    'faculty_id',
                    'edu_year_id',
                    'subject_id',
                    'language_id',
                    'teacher_user_id',
                    'edu_plan_id',
                    'building_id',
                    'time_option_id',
                    'edu_semestr_subject_id',
                    'archived',
                    'edu_semester_id',

                    'main',

                    'has_exam_control',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [['time_option_key'], 'string'],
            [
                ['course_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Course::className(),
                'targetAttribute' => ['course_id' => 'id']
            ],
            [
                ['edu_semester_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EduSemestr::className(),
                'targetAttribute' => ['edu_semester_id' => 'id']
            ],
            [
                ['edu_year_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EduYear::className(),
                'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [
                ['language_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Languages::className(),
                'targetAttribute' => ['language_id' => 'id']
            ],
            [
                ['para_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Para::className(),
                'targetAttribute' => ['para_id' => 'id']
            ],
            [
                ['room_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Room::className(),
                'targetAttribute' => ['room_id' => 'id']
            ],
            [
                ['week_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Week::className(),
                'targetAttribute' => ['week_id' => 'id']
            ],
            [
                ['subject_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Subject::className(),
                'targetAttribute' => ['subject_id' => 'id']
            ],
            [
                ['semester_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Semestr::className(),
                'targetAttribute' => ['semester_id' => 'id']
            ],
            [
                ['subject_category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SubjectCategory::className(),
                'targetAttribute' => ['subject_category_id' => 'id']
            ],
            [
                ['teacher_access_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TeacherAccess::className(),
                'targetAttribute' => ['teacher_access_id' => 'id']
            ],
            [
                ['time_option_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => TimeOption::className(),
                'targetAttribute' => ['time_option_id' => 'id']
            ],
            [
                ['faculty_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Faculty::className(),
                'targetAttribute' => ['faculty_id' => 'id']
            ],
            [
                ['edu_semestr_subject_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EduSemestrSubject::className(),
                'targetAttribute' => ['edu_semestr_subject_id' => 'id']
            ],

            [['excel'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx,xls'],
            // [['excel'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx,xls', 'maxSize' => $this->excelFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_semestr_subject_id' => 'edu_semestr_subject ID',
            'faculty_id' => 'faculty ID',
            'teacher_access_id' => 'Teacher Access ID',
            'room_id' => 'Room ID',
            'para_id' => 'Para ID',
            'time_option_id' => 'time_option_id',
            'course_id' => 'Course ID',
            'edu_plan_id' => 'edu_plan_id',
            'building_id' => 'building_id',
            'lecture_id' => 'Lecture ID',
            'semester_id' => 'Semestr ID',
            'parent_id' => 'Parent ID',
            'subject_category_id ' => 'Subject Category ID',
            'edu_year_id' => 'Edu Year ID',
            'edu_semester_id' => 'Edu Semester ID',
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
            'edu_semestr_subject_id',
            'teacher_access_id',
            'room_id',
            'para_id',
            'week_id',
            'course_id',
            'semester_id',
            'parent_id',
            'lecture_id',
            'time_option_id',
            'edu_semester_id',
            'edu_year_id',
            'subject_id',
            'language_id',
            'order',
            'faculty_id',
            'edu_plan_id',
            'building_id',
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
            'studentTimeTable',
            'studentTimeTables',
            'selectedCount',
            'notCheckedControlStudentCount',
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


            'attendanceDates',
            'examControl',

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
        return $this->hasOne(ExamControl::className(), ['time_table_id' => 'id'])->onCondition(['is_deleted' => 0]);
        return $this->hasOne(ExamControl::className(), ['time_table_id' => 'id']);
    }

    public function getAttendanceDates()
    {
        // Get the start and end dates of the education semester
        $dateFromString = $this->eduSemestr->start_date;
        $dateToString = $this->eduSemestr->end_date;

        // Create DateTime objects for comparison
        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        // If the start date is after the end date, return an empty array
        if ($dateFrom > $dateTo) {
            return $dates;
        }

        // Ensure that the starting day matches the desired weekday
        if ($this->week_id != $dateFrom->format('N')) {
            $dateFrom->modify('next ' . $this->dayName()[$this->week_id]);
        }

        // Iterate through the dates within the semester
        while ($dateFrom <= $dateTo) {
            // Check if there's a holiday on the current date
            $holiday = Holiday::find()
                ->where(['status' => 1, 'is_deleted' => 0])
                ->andWhere(['>=', 'start_date', $dateFrom->format('Y-m-d')])
                ->andWhere(['<=', 'finish_date', $dateFrom->format('Y-m-d')])
                ->one();

            // If a holiday is found
            if ($holiday) {
                // Check if it's of type 2
                if ($holiday->type == 2) { // Fixed the comparison operator
                    // Add it to the dates array with its associated attendance data
                    $dates[$holiday->moved_date] = $this->getAttend($holiday->moved_date);
                }
            } else {
                // If no holiday, add the date to the dates array with attendance data
                $dates[$dateFrom->format('Y-m-d')] = $this->getAttend($dateFrom->format('Y-m-d'));
            }

            // Move to the next week
            $dateFrom->modify('+1 week');
        }

        return $dates;
    }



    public function getAttendanceDates01()
    {
        $dateFromString = $this->eduSemestr->start_date;
        $dateToString = $this->eduSemestr->end_date;


        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }

        if ($this->week_id != $dateFrom->format('N')) {
            $dateFrom->modify('next ' . $this->dayName()[$this->week_id]);
        }

        while ($dateFrom <= $dateTo) {
            $holiday = Holiday::find()
                ->where(['status' => 1, 'is_deleted' => 0])
                ->andWhere(['>=', 'start_date', $dateFrom->format('Y-m-d')])
                ->andWhere(['<=', 'end_date', $dateFrom->format('Y-m-d')])
                ->one();
            if ($holiday) {
                if ($holiday->type = 2) {
                    $dates[$holiday->start_date] = $this->getAttend($holiday->start_date);
                }
            } else {
                $dates[$dateFrom->format('Y-m-d')] = $this->getAttend($dateFrom->format('Y-m-d'));
            }

            $dateFrom->modify('+1 week');
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


        if (isset($date) && $date != null) {
            if (!($date >= $this->eduSemestr->start_date && $date <= $this->eduSemestr->end_date)) {
                return 0;
            }

            if ($date > date('Y-m-d')) {
                return 0;
            }
            // if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i', strtotime($date))) && ($this->para->end_time >  date('H:i', strtotime($date)))) {
            /* dd([
                $date,
                date('w', strtotime($date)),
                date('H:i', strtotime($date)),
                $this->para->start_time
            ]); */
            // if ($this->eduSemestr->start_date <= $date && $date <= $this->eduSemestr->end_date)

            // if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i', strtotime($date)))) {

            if ($date == date('Y-m-d')) {
                if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i'))) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                if (($this->week_id == date('w', strtotime($date)))) {
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
                'edu_semestr_id' => $this->edu_semester_id,
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
            $timeTableSameBusy = TimeTable::find()->where([
                'edu_semester_id' => $this->edu_semester_id,
                'edu_year_id' => $this->edu_year_id,
                'semester_id' => $this->semester_id,
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
        $attend = Attend::findOne(['time_table_id' => $this->id, 'date' => $date]);

        if ($attend) {
            // Get the list of student_ids who attended and cast them to integers
            $studentAttendIds = StudentAttend::find()
                ->select('student_id')
                ->where(['attend_id' => $attend->id, 'is_deleted' => 0])
                ->column();

            // Cast each student_id to an integer
            $attend->student_ids = array_map('intval', $studentAttendIds);
        }
        return $attend;
    }




    public function getAttend01($date)
    {
        $date = date("Y-m-d", strtotime($date));
        return Attend::findOne(['time_table_id' => $this->id, 'date' => $date]);
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        if (isRole('student')) {
            return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => $this->student()])->andWhere(['is_deleted' => 0]);
        }

        $filter = json_decode(str_replace("'", "", Yii::$app->request->get('filter')));
        if (isset($filter->student_id)) {
            return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => $filter->student_id])->andWhere(['is_deleted' => 0]);
        }
        return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id'])->andWhere(['is_deleted' => 0]);
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


    public function getStudentTimeTables()
    {
        return $this->hasMany(StudentTimeTable::className(), ['time_table_id' => 'id']);
    }


    public function getSelectedCount()
    {
        $studentTimeTable = StudentTimeTable::find()->where(['time_table_id' => $this->id])->all();
        return count($studentTimeTable);
    }

    public function getNotCheckedControlStudentCount()
    {
        $studentTimeTable = ExamControlStudent::find()
            ->where(['time_table_id' => $this->id])
            ->andWhere([
                'OR',
                ['IS', 'main_ball', null],
                ['main_ball' => 0],
            ])
            ->andWhere(['IS NOT', 'answer_file', null])
            ->andWhere(['IS ', 'ball', null])
            ->all();

        // dd(sqlraw($studentTimeTable));
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
        return $this->hasOne(Semestr::className(), ['id' => 'semester_id']);
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
        return Profile::find()->select(['user_id', 'first_name', 'last_name', 'middle_name'])->where(['user_id' => $this->teacherAccess->user_id ?? null])->one();
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semester_id']);
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

    public static function executeArrayLabel($sheetData)
    {
        $keys = ArrayHelper::remove($sheetData, '1');

        $new_data = [];

        foreach ($sheetData as $values) {
            $new_data[] = array_combine($keys, $values);
        }

        return $new_data;
    }

    public static function import($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (empty($post['type'])) {
            $errors[] = 'Type required';
            return self::returnError($errors, $transaction);
        }

        // Retrieve the uploaded file
        $excelFiles = UploadedFile::getInstancesByName('excel');
        if (empty($excelFiles)) {
            $errors[] = 'Excel file required';
            return self::returnError($errors, $transaction);
        }

        $excelFile = $excelFiles[0];
        $excelUrl = self::uploadExcel($excelFile);
        if (!$excelUrl) {
            return self::returnError('Excel file not uploaded', $transaction);
        }

        try {
            // Identify and read the Excel file
            $inputFileType = IOFactory::identify($excelFile->tempName);
            $reader = IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($excelFile->tempName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Process the sheet data
            if (self::$setFirstRecordAsKeys) {
                $sheetData = self::executeArrayLabel($sheetData);
            }

            if (!empty(self::$getOnlyRecordByIndex)) {
                $sheetData = self::executeGetOnlyRecords($sheetData, self::$getOnlyRecordByIndex);
            }

            if (!empty(self::$leaveRecordByIndex)) {
                $sheetData = self::executeLeaveRecords($sheetData, self::$leaveRecordByIndex);
            }

            // Prepare data for batch insert
            $batchData = [];

            foreach ($sheetData as $row) {


                // if ($row['id'] != null) {
                $model = self::find()->where(['id' => $row['id'] ?? null])->one();
                // }
                if ($model) {
                    unset($row['teacher_access_id']);
                    unset($row['teacher_user_id']);


                    $model->parent_id = $row['parent_id'] ?? $model->parent_id;
                    $model->lecture_id = $row['lecture_id'] ?? $model->lecture_id;
                    $model->week_id = $row['week_id'] ?? $model->week_id;
                    $model->para_id = $row['para_id'] ?? $model->para_id;
                    $model->room_id = $row['room_id'] ?? $model->room_id;
                    $model->building_id = $row['building_id'] ?? $model->building_id;
                    $model->subject_category_id = $row['subject_category_id'] ?? $model->subject_category_id;
                    $model->subject_id = $row['subject_id'] ?? $model->subject_id;
                    $model->edu_semestr_subject_id = $row['edu_semestr_subject_id'] ?? $model->edu_semestr_subject_id;
                    $model->language_id = $row['language_id'] ?? $model->language_id;
                    $model->edu_year_id = $row['edu_year_id'] ?? $model->edu_year_id;
                    $model->time_option_id = $row['time_option_id'] ?? $model->time_option_id;
                    $model->main = $row['main'] ?? $model->main;
                    $model->order = $row['order'] ?? $model->order;
                    $model->course_id = $row['course_id'] ?? $model->course_id;

                    $model->semester_id = $row['semester_id'] ?? $model->semester_id;


                    // $model->setAttributes($row);
                } else {

                    $parent = self::find()
                        ->where(['parent_id' => null])
                        ->andWhere(['edu_semestr_subject_id' => $row['edu_semestr_subject_id'] ?? null])
                        ->andWhere(['language_id' => $row['language_id'] ?? null])
                        ->andWhere(['edu_year_id' => $row['edu_year_id'] ?? null])
                        ->andWhere(['time_option_id' => $row['time_option_id'] ?? null])
                        ->andWhere(['subject_category_id' => $row['subject_category_id'] ?? null])
                        ->andWhere(['order' => $row['order'] ?? null])
                        ->andWhere(['main' => 1])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    if ($parent) {
                        $row['parent_id'] = $parent->id;
                    }

                    $lecture = self::find()
                        ->where(['lecture_id' => null, 'parent_id' => null])
                        ->andWhere(['edu_semestr_subject_id' => $row['edu_semestr_subject_id'] ?? null])
                        ->andWhere(['language_id' => $row['language_id'] ?? null])
                        ->andWhere(['edu_year_id' => $row['edu_year_id'] ?? null])
                        ->andWhere(['time_option_id' => $row['time_option_id'] ?? null])
                        ->andWhere(['subject_category_id' => 1])
                        ->andWhere(['main' => 1])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    if ($lecture) {
                        $row['lecture_id'] = $lecture->id;
                    }

                    $model = new self();
                    $model->setAttributes($row);
                }

                if (!$model->save()) {
                    // dd($row, $model->errors);
                    $errors[] = [$row['id'] => $model->errors];
                }
            }

            if (count($errors) == 0) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return self::returnError($errors, $transaction);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            return self::returnError($e->getMessage(), $transaction);
        }
    }

    /**
     * Helper method to return an error and rollback transaction.
     */
    private static function returnError($message, $transaction)
    {
        $transaction->rollBack();
        return ['error' => $message];
    }

    public static function importtt($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (empty($post['type'])) {
            $errors[] = 'Type required';
            return self::returnError($errors, $transaction);
        }

        // Retrieve the uploaded file
        $excelFiles = UploadedFile::getInstancesByName('excel');
        if (empty($excelFiles)) {
            $errors[] = 'Excel file required';
            return self::returnError($errors, $transaction);
        }

        $excelFile = $excelFiles[0];
        $excelUrl = self::uploadExcel($excelFile);
        if (!$excelUrl) {
            return self::returnError('Excel file not uploaded', $transaction);
        }

        try {
            // Identify and read the Excel file
            $inputFileType = IOFactory::identify($excelFile->tempName);
            $reader = IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($excelFile->tempName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Process the sheet data
            if (self::$setFirstRecordAsKeys) {
                $sheetData = self::executeArrayLabel($sheetData);
            }

            if (!empty(self::$getOnlyRecordByIndex)) {
                $sheetData = self::executeGetOnlyRecords($sheetData, self::$getOnlyRecordByIndex);
            }

            if (!empty(self::$leaveRecordByIndex)) {
                $sheetData = self::executeLeaveRecords($sheetData, self::$leaveRecordByIndex);
            }

            // Prepare data for batch insert
            $batchData = [];

            foreach ($sheetData as $row) {


                $parent = self::find()
                    ->where(['parent_id' => null])
                    ->andWhere(['edu_semestr_subject_id' => $row['edu_semestr_subject_id'] ?? null])
                    ->andWhere(['language_id' => $row['language_id'] ?? null])
                    ->andWhere(['edu_year_id' => $row['edu_year_id'] ?? null])
                    ->andWhere(['time_option_id' => $row['time_option_id'] ?? null])
                    ->andWhere(['subject_category_id' => $row['subject_category_id'] ?? null])
                    ->andWhere(['main' => 1])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if ($parent) {
                    $row['parent_id'] = $parent->id;
                }

                $lecture = self::find()
                    ->where(['lecture_id' => null, 'parent_id' => null])
                    ->andWhere(['edu_semestr_subject_id' => $row['edu_semestr_subject_id'] ?? null])
                    ->andWhere(['language_id' => $row['language_id'] ?? null])
                    ->andWhere(['edu_year_id' => $row['edu_year_id'] ?? null])
                    ->andWhere(['time_option_id' => $row['time_option_id'] ?? null])
                    ->andWhere(['subject_category_id' => 1])
                    ->andWhere(['main' => 1])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if ($lecture) {
                    $row['lecture_id'] = $lecture->id;
                }

                $timeTableNew = new TimeTable();
                $timeTableNew->setAttributes($row);

                $batchData[] = [
                    'parent_id' => $row['parent_id'] ?? null,
                    'lecture_id' => $row['lecture_id'] ?? null,
                    'room_id' => $row['room_id'] ?? null,
                    'para_id' => $row['para_id'] ?? null,
                    'course_id' => $row['course_id'] ?? null,
                    'semester_id' => $row['semester_id'] ?? null,
                    'edu_year_id' => $row['edu_year_id'] ?? null,
                    'subject_id' => $row['subject_id'] ?? null,
                    'language_id' => $row['language_id'] ?? null,
                    'week_id' => $row['week_id'] ?? null,
                    'time_option_id' => $row['time_option_id'] ?? null,
                    'building_id' => $row['building_id'] ?? null,
                    'edu_plan_id' => $row['edu_plan_id'] ?? null,
                    'edu_semester_id' => $row['edu_semester_id'] ?? null,
                    'edu_semestr_subject_id' => $row['edu_semestr_subject_id'] ?? null,
                    'subject_category_id' => $row['subject_category_id'] ?? null,
                    'faculty_id' => $row['faculty_id'] ?? null,
                    'main' => $row['main'] ?? null,
                    'time_option_key' => $row['time_option_key'] ?? null,
                ];
            }

            // Perform batch insert
            $columns = array_keys($batchData[0]); // Extract column names
            $tableName = self::tableName(); // Replace with the actual table name
            Yii::$app->db->createCommand()
                ->batchInsert($tableName, $columns, $batchData)
                ->execute();

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            return self::returnError($e->getMessage(), $transaction);
        }
    }

    public static function importt($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        // $file = UploadedFile::getInstancesByName('excel');
        $excel = UploadedFile::getInstancesByName('excel');
        if (!$excel) {
            $errors[] = _e('Excel file required');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($excel) {
            $excel = $excel[0];
            $excelUrl = self::uploadExcel($excel);
            if (!$excelUrl) {
                $errors[] = _e('Excel file not uploaded');
            }
        }
        try {
            $inputFileType = IOFactory::identify($excel->tempName);
            $objReader = IOFactory::createReader($inputFileType);

            $objectPhpExcel = $objReader->load($excel->tempName);;

            $sheetDatas = [];

            $sheetDatas = $objectPhpExcel->getActiveSheet()->toArray(null, true, true, true);

            // dd($sheetDatas);
            if (self::$setFirstRecordAsKeys) {
                $sheetDatas = self::executeArrayLabel($sheetDatas);
            }

            if (!empty(self::$getOnlyRecordByIndex)) {
                $sheetDatas = $this->executeGetOnlyRecords($sheetDatas, self::$getOnlyRecordByIndex);
            }
            if (!empty(self::$leaveRecordByIndex)) {
                $sheetDatas = $this->executeLeaveRecords($sheetDatas, self::$leaveRecordByIndex);
            }

            dd($sheetDatas);
            foreach ($sheetDatas as $dataOne) {

                $timeTableNew->teacher_access_id = $dataOne['teacher_access_id'];
                $timeTableNew->room_id = $dataOne['room_id'];
                $timeTableNew->para_id = $dataOne['para_id'];
                $timeTableNew->course_id = $dataOne['course_id'];
                $timeTableNew->semester_id = $dataOne['semester_id'];
                $timeTableNew->edu_year_id = $dataOne['edu_year_id'];
                $timeTableNew->subject_id = $dataOne['subject_id'];
                $timeTableNew->language_id = $dataOne['language_id'];
                $timeTableNew->week_id = $dataOne['week_id'];
                $timeTableNew->time_option_id = $dataOne['time_option_id'];
                $timeTableNew->building_id = $dataOne['building_id'];
                $timeTableNew->edu_plan_id = $dataOne['edu_plan_id'];
                $timeTableNew->teacher_user_id = $dataOne['teacher_user_id'];
                $timeTableNew->edu_semester_id = $dataOne['edu_semester_id'];
                $timeTableNew->edu_semestr_subject_id = $dataOne['edu_semestr_subject_id'];
                $timeTableNew->subject_category_id = $dataOne['subject_category_id'];
                $timeTableNew->faculty_id = $dataOne['faculty_id'];
                $timeTableNew->time_option_key = $dataOne['time_option_key'];

                if (!$timeTableNew->save()) {
                    $errors[] = $timeTableNew->errors;
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        dd($errors);
        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $transaction->commit();
            return true;
        }


        return $sheetDatas;
    }



    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $eduSemester = EduSemestr::findOne($model->edu_semester_id);

        if (isset($post['time_option_id'])) {
            $model->edu_year_id = $model->timeOption->edu_year_id;
            $model->edu_plan_id = $model->timeOption->edu_plan_id;
            $model->edu_year_id = $model->timeOption->edu_year_id;
            $model->edu_semester_id = $model->timeOption->edu_semester_id;
            $model->language_id = $model->timeOption->language_id;
        }

        if (isset($model->parent->time_option_id)) {
            $model->time_option_id = $model->parent->time_option_id;
        }
        if (isset($model->lecture->time_option_id)) {
            $model->time_option_id = $model->lecture->time_option_id;
        }
        if (isset($model->parent_id)) {
            $model->lecture_id = $model->parent->lecture_id;
        }


        if (!isset($eduSemester)) {
            $errors[] = _e("Edu Semester not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // $timeTable = TimeTable::findOne([
        //     'room_id' => $model->room_id,
        //     'para_id' => $model->para_id,
        //     'week_id' => $model->week_id,
        //     'edu_year_id' => $eduSemester->edu_year_id,
        //     'archived' => 0,
        //     'status' => 1
        // ]);

        $model->semester_id = $eduSemester->semestr_id;
        $model->course_id = $eduSemester->course_id;
        $model->edu_year_id = $eduSemester->edu_year_id;
        $model->edu_plan_id = $eduSemester->edu_plan_id;
        $model->building_id = $model->room->building_id;

        $model->teacher_user_id = $model->teacherAccess->user_id;
        $model->faculty_id = $model->eduPlan->faculty_id;

        // if (isset($timeTable)) {
        //     if ($model->semester_id % 2 == $timeTable->semester_id % 2) {
        //         $errors[] = _e("This Room and Para is busy for this Edu Year's semestr");
        //         $transaction->rollBack();
        //         return simplify_errors($errors);
        //     }
        // }

        // /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/
        if ($model->faculty_id == 5) {
            $checkTeacherTimeTable = TimeTable::findOne([
                'para_id' => $model->para_id,
                'faculty_id' => $model->faculty_id,
                // 'edu_semester_id' => $model->edu_semester_id,
                'edu_year_id' => $eduSemester->edu_year_id,
                'week_id' => $model->week_id,
                'teacher_access_id' => $model->teacher_access_id,
                'archived' => 0,
                'status' => 1
            ]);
        } else {
            $checkTeacherTimeTable = TimeTable::findOne([
                'para_id' => $model->para_id,
                // 'edu_semester_id' => $model->edu_semester_id,
                'edu_year_id' => $eduSemester->edu_year_id,
                'week_id' => $model->week_id,
                'teacher_access_id' => $model->teacher_access_id,
                'archived' => 0,
                'status' => 1
            ]);
        }

        // // sirtqi kirish uchun 
        // if (isset($checkTeacherTimeTable)) {
        //     if ($model->semester_id % 2 == $checkTeacherTimeTable->semester_id % 2) {
        //         $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
        //         $transaction->rollBack();
        //         return simplify_errors($errors);
        //     }
        // }
        // /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/


        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function addTeacher($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['teacher_access_id'])) {
            $errors[] = _e("Teacher Access not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($model->teacher_access_id)) {
            $errors[] = _e("Teacher Access already set");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->teacher_access_id = $post['teacher_access_id'];

        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->teacher_user_id = $model->teacherAccess->user_id;

        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/
        $checkTeacherTimeTable = TimeTable::findOne([
            'para_id' => $model->para_id,
            'edu_year_id' => $model->edu_year_id,
            'week_id' => $model->week_id,
            'teacher_access_id' => $model->teacher_access_id,
            'status' => 1,
            'archived' => 0,
            'is_deleted' => 0,
            'faculty_id' => '<>5',
        ]);

        if (isset($checkTeacherTimeTable)) {
            if (($model->semester_id % 2 == $checkTeacherTimeTable->semester_id % 2) && ($model->id != $checkTeacherTimeTable->id)) {
                $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateTeacher($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['teacher_access_id'])) {
            $errors[] = _e("Teacher Access not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!isset($model->teacher_access_id)) {
            $errors[] = _e("Teacher Access NOT set");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->teacher_access_id = $post['teacher_access_id'];
        if (!$model->validate()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->teacher_user_id = $model->teacherAccess->user_id;

        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/
        $checkTeacherTimeTable = TimeTable::findOne([
            'para_id' => $model->para_id,
            'edu_year_id' => $model->edu_year_id,
            'week_id' => $model->week_id,
            'teacher_access_id' => $model->teacher_access_id,
            'status' => 1,
            'archived' => 0,
            'is_deleted' => 0,
            'faculty_id' => '<>5',
        ]);

        if (isset($checkTeacherTimeTable)) {
            if (($model->semester_id % 2 == $checkTeacherTimeTable->semester_id % 2) && ($model->id != $checkTeacherTimeTable->id)) {
                $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteTeacher($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        $model->teacher_user_id = null;
        $model->teacher_access_id = null;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $eduSemester = EduSemestr::findOne($model->edu_semester_id);

        if (isset($post['time_option_id'])) {
            $childs = TimeTable::updateAll(['time_option_id' => $post['time_option_id']], ['parent_id' => $model->id]);
            $seminars = TimeTable::updateAll(['time_option_id' => $post['time_option_id']], ['lecture_id' => $model->id]);
        }

        if (!isset($eduSemester)) {
            $errors[] = _e("Edu Semester not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $timeTable = TimeTable::findOne([
            'room_id' => $model->room_id,
            'para_id' => $model->para_id,
            'week_id' => $model->week_id,
            'edu_year_id' => $eduSemester->edu_year_id,
            'status' => 1,
        ]);

        $model->semester_id = $eduSemester->semestr_id;
        $model->course_id = $eduSemester->course_id;
        $model->edu_year_id = $eduSemester->edu_year_id;

        $model->teacher_user_id = $model->teacherAccess->user_id;

        if (isset($timeTable)) {
            if (($model->semester_id % 2 == $timeTable->semester_id % 2) && ($model->id != $timeTable->id)) {
                $errors[] = _e("This Room and Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/
        $checkTeacherTimeTable = TimeTable::findOne([
            'para_id' => $model->para_id,
            // 'edu_semester_id' => $model->edu_semester_id,
            'edu_year_id' => $eduSemester->edu_year_id,
            'week_id' => $model->week_id,
            'teacher_access_id' => $model->teacher_access_id,
            'status' => 1,
            'archived' => 0,
            'is_deleted' => 0,
        ]);

        if (isset($checkTeacherTimeTable)) {
            if (($model->semester_id % 2 == $checkTeacherTimeTable->semester_id % 2) && ($model->id != $checkTeacherTimeTable->id)) {
                $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function uploadExcel($excel)
    {
        // if (self::validate()) {
        if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
            mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
        }

        $fileName = time() . "_" . current_user_id() . '.' . $excel->extension;

        // 84f19fab3d928e7086587ebbae7c4bde0d81fae838edd43ac8d150db251b41ce
        // 84f19fab3d928e7086587ebbae7c4bde0d81fae838edd43ac8d150db251b41ce
        // 793cefe89429e84f8a8419982793edaceef6369de9b9d6788911873b54efb336
        // 793cefe89429e84f8a8419982793edaceef6369de9b9d6788911873b54efb336


        // $fileHash = hash_file('sha256', $excel->tempName);
        // dd($fileHash);

        $miniUrl = self::UPLOADS_FOLDER . $fileName;
        $url = STORAGE_PATH . $miniUrl;
        $excel->saveAs($url, false);
        return "storage/" . $miniUrl;
        // } else {
        //     return false;
        // }
    }

    public static function uploadExcell($excel)
    {
        // if (self::validate()) {
        if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
            mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
        }

        $fileName = time() . "_" . current_user_id() . '.' . $excel->extension;

        $miniUrl = self::UPLOADS_FOLDER . $fileName;
        $url = STORAGE_PATH . $miniUrl;
        $excel->saveAs($url, false);
        return "storage/" . $miniUrl;
        // } else {
        //     return false;
        // }
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
