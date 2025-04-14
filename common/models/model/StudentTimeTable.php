<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\StudentUser;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr".
 *
 * @property int $id
 * @property int $student_id
 * @property int $time_table_id
 * @property int|null $order
 * @property int|null $status
 * @property int|null $teacher_access_id
 * @property int|null $language_id
 * @property int|null $course_id
 * @property int|null $semester_id
 * @property int|null $edu_year_id
 * @property int|null $subject_id
 * @property int|null $room_id
 * @property int|null $para_id
 * @property int|null $week_id
 * @property int|null $edu_semester_id
 * @property int|null $subject_category_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $student
 * @property EduPlan $timeTable
 */
class StudentTimeTable extends \yii\db\ActiveRecord
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

        // if (Yii::$app->request->get('archived')) {
        //     return 'student_time_table_archived';
        // }
        return 'student_time_table'; // original table
    }

    /**
     * {@inheritdoc}
     */

    // const TIME_10 = 1662837860;
    const TIME_10 = 1662872400;
    // const TIME_10 = 1662867000;
    // const TIME_11 = 1662837860;
    const TIME_11 = 1662876000;
    const TIME_12 = 1662879600;
    const TIME_13 = 1662883200;
    const TIME_14 = 1662886800;
    const TIME_15 = 1662890400;
    const TIME_16 = 1662894000;
    const TIME_17 = 1662897600;
    const TIME_18 = 1662901200;
    const TIME_19 = 1662904800;


    public function rules()
    {
        return [
            [['student_id', 'time_table_id'], 'required'],
            [
                [
                    'teacher_access_id',
                    'language_id',
                    'course_id',
                    'semester_id',
                    'edu_year_id',
                    'subject_id',
                    'room_id',
                    'para_id',
                    'week_id',
                    'edu_semester_id',
                    'subject_category_id',

                    'time_table_parent_id',
                    'time_table_lecture_id',
                    'teacher_user_id',
                    'edu_plan_id',
                    'building_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',

                    'archived',

                    'is_deleted'
                ],
                'integer'
            ],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['time_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['time_table_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student Id',
            'time_table_id' => 'Time Table Id',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'edu_plan_id' => 'edu_plan_id',
            'building_id' => 'building_id',

            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),

            'teacher_access_id',
            'language_id',
            'course_id',
            'semester_id',
            'edu_year_id',
            'subject_id',
            'room_id',
            'para_id',
            'week_id',
            'archived',
            'edu_semester_id',
            'subject_category_id',

            'time_table_parent_id',
            'time_table_lecture_id',
            'teacher_user_id',
            'edu_plan_id',
            'order',
            'status',
        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'student_id',
            'time_table_id',
            'course_id',
            'semester_id',
            'edu_year_id',
            'subject_id',
            'room_id',
            'para_id',
            'week_id',
            'edu_semester_id',
            'subject_category_id',

            'time_table_parent_id',
            'time_table_lecture_id',
            'teacher_user_id',
            'edu_plan_id',
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
        $extraFields = [
            'subject',
            'teacher',
            'building',
            'room',
            'week',
            'para',
            'subjectCategory',
            'isBusy',

            'studentAttends',

            'student',
            'timeTable',
        ];

        return $extraFields;
    }


    /* 
    subject,
    teacher,
    building,
    room,
    week,
    para,
    sillabus(seminar | amaliy | ...)
 */
    public static function chekTime()
    {
        return true;
        if (isRole('student')) {
            $student = self::student(2);

            // dd(date('Y-m-d H:m:s', self::TIME_11), time());

            if ($student) {
                $now = time();
                if ($student->course_id == 2) {
                    if ($now >= self::TIME_10 && $now <= self::TIME_11) {
                        return true;
                    }
                    if ($now >= self::TIME_13 && $now <= self::TIME_14) {
                        return true;
                    }
                    if ($now >= self::TIME_16 && $now <= self::TIME_17) {
                        return true;
                    }
                    return false;
                }
                if ($student->course_id == 3) {
                    // dd([$now, self::TIME_11, self::TIME_12]);
                    if ($now >= self::TIME_11 && $now <= self::TIME_12) {
                        return true;
                    }
                    if ($now >= self::TIME_14 && $now <= self::TIME_15) {
                        return true;
                    }
                    if ($now >= self::TIME_17 && $now <= self::TIME_18) {
                        return true;
                    }
                    return false;
                }
                if ($student->course_id == 4) {
                    if ($now >= self::TIME_12 && $now <= self::TIME_13) {
                        return true;
                    }
                    if ($now >= self::TIME_15 && $now <= self::TIME_16) {
                        return true;
                    }
                    if ($now >= self::TIME_18 && $now <= self::TIME_19) {
                        return true;
                    }
                    return false;
                }
            }
            return false;
        }

        return true;
    }

    public function getStudentAttends()
    {
        return $this->timeTable->studentAttends;
    }

    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategory()
    {
        return $this->timeTable->subjectCategory ?? "";
    }

    /**
     * Gets query for [[para]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPara()
    {
        return $this->timeTable->para ?? "";
    }

    /**
     * Gets query for [[week]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWeek()
    {
        return $this->timeTable->week ?? "";
    }

    /**
     * Gets query for [[room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->timeTable->room ?? "";
    }

    /**
     * Gets query for [[building]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->timeTable->building ?? "";
    }

    /**
     * Gets query for [[teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->timeTable->teacher ?? "";
    }

    /**
     * Gets query for [[subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->timeTable->subject ?? "";
    }

    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTable()
    {
        return $this->hasOne(TimeTable::className(), ['id' => 'time_table_id']);
    }

    public function getTimeTableLecture()
    {
        return $this->hasOne(TimeTable::className(), ['id' => 'time_table_lecture_id']);
    }
    public function getTimeTableLectureSelf()
    {
        return $this->hasOne(self::className(), ['time_table_id' => 'time_table_lecture_id']);
    }

    public function getStudentTimeOption()
    {
        return $this->hasOne(StudentTimeOption::className(), ['id' => 'student_time_option_id']);
    }

    public function getFindStudentTimeOptionId1()
    {
        $model = StudentTimeOption::find()
            ->where(['student_id' => $this->student_id, 'time_option_id' => $this->time_table_id, 'archived' => 0, 'is_deleted' => 0])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        return $model ? $model->id : null;
    }
    public function getFindStudentTimeOptionId()
    {
        $studentTimeOption = StudentTimeOption::find()
            ->where([
                'student_id' => $this->student_id,
                'time_option_id' => $this->time_table_id,
                'archived' => 0,
                'is_deleted' => 0,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        return $studentTimeOption ? $studentTimeOption->id : null;
    }




    // public function getStudentTimeOption2paramID()
    // {
    //     return StudentTimeOption::findOne([
    //         'student_id' => $this->student_id,
    //         'time_option_id' => $this->time_table_id,
    //         'archived' => 0,
    //         'is_deleted' => 0,
    //     ])?->id;
    // }

    public static function createItemForOption($model, $post = null)
    {
        $errors = [];

        $student = Student::findOne(['user_id' => Current_user_id()]);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            return $errors;
        }

        $model->student_id = self::student();

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $hasModel = self::findOne([
            'student_id' => $model->student_id,
            'time_table_id' => $model->time_table_id,
        ]);

        $studentCheck = Student::findOne($model->student_id);
        $timeTableCheck = TimeTable::findOne($model->time_table_id);

        // $studentTimeTable = self::find()->where([
        //     'time_table_id' => $model->time_table_id,
        //     'is_deleted' => 0
        // ])->all();

        // if ($model->timeTable->room->capacity <= count($studentTimeTable)) {
        //     $errors[] = _e('This Time Table is Full!');
        //     return $errors;
        // }

        // if ($model->subject_category_id == 1 &&  count($studentTimeTable) > 30) {
        //     $errors[] = _e('This Time Table is Full! (30)');
        //     return $errors;
        // }


        /**
         *  Student Edu Plan bo'yicha tekshirish
         */

        if (isset($timeTableCheck)) {
            if ($timeTableCheck->eduSemestr) {
                if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
                    $errors[] = $timeTableCheck->eduSemestr;
                    $errors[] = $studentCheck->edu_plan_id;

                    $errors[] = _e('This Time Table is not for you');
                    return $errors;
                }
            }
        }

        // if (isset($hasModel)) {
        //     $errors[] = _e('This Student Time Table already exists');
        //     return $errors;
        // }

        //

        /** Shu fanni tanlaganmi */
        // $timeTableSame = TimeTable::find()->where([
        //     'edu_semester_id' => $model->timeTable->edu_semester_id,
        //     'edu_year_id' => $model->timeTable->edu_year_id,
        //     'subject_id' => $model->timeTable->subject_id,
        //     'semester_id' => $model->timeTable->semester_id,
        //     'subject_category_id' => $model->timeTable->subject_category_id,
        //     'parent_id' => null
        // ])->select('id');

        // $timeTableSelected = self::find()
        //     ->where(['in', 'time_table_id', $timeTableSame])
        //     ->andWhere(['student_id' => $model->student_id])
        //     ->all();

        // if (count($timeTableSelected) > 0) {
        //     $errors[] = _e('This subject already selected');
        //     return $errors;
        // }
        /** Shu fanni tanlaganmi */

        /** Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */
        // $timeTableSameBusy = TimeTable::find()->where([
        //     'edu_semester_id' => $model->timeTable->edu_semester_id,
        //     'edu_year_id' => $model->timeTable->edu_year_id,
        //     'semester_id' => $model->timeTable->semester_id,
        //     'para_id' => $model->timeTable->para_id,
        //     'week_id' => $model->timeTable->week_id,
        // ])->select('id');

        // $timeTableSelected = self::find()
        //     ->where(['in', 'time_table_id', $timeTableSameBusy])
        //     ->andWhere(['student_id' => $model->student_id])
        //     ->all();

        // if (count($timeTableSelected) > 0) {
        //     $errors[] = _e('You are busy in this time!');
        //     return $errors;
        // }


        /** Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */

        //

        $model->teacher_access_id = $model->timeTable->teacher_access_id;
        $model->language_id = $model->timeTable->language_id;
        $model->course_id = $model->timeTable->course_id;
        $model->semester_id = $model->timeTable->semester_id;
        $model->edu_year_id = $model->timeTable->edu_year_id;
        $model->subject_id = $model->timeTable->subject_id;
        $model->room_id = $model->timeTable->room_id;
        $model->building_id = $model->timeTable->room->building_id;
        $model->para_id = $model->timeTable->para_id;
        $model->week_id = $model->timeTable->week_id;
        $model->edu_semester_id = $model->edu_semester_id ?? $model->timeTable->edu_semester_id;
        $model->edu_plan_id = $model->timeTable->eduSemestr->edu_plan_id;
        $model->subject_category_id = $model->timeTable->subject_category_id;

        $model->time_table_parent_id = $model->timeTable->parent_id;
        $model->time_table_lecture_id = $model->timeTable->lecture_id;
        $model->teacher_user_id = $model->timeTable->teacher_user_id;

        if ($model->save()) {

            // Student child larini yozish
            $timeTables = TimeTable::findAll(['parent_id' => $model->time_table_id]);
            if (isset($timeTables)) {
                foreach ($timeTables as $timeTableOne) {

                    $newModel = new StudentTimeTable();
                    $newModel->student_id = $model->student_id;
                    $newModel->time_table_id = $timeTableOne->id;
                    $newModel->time_option_id = $model->time_option_id;
                    $newModel->student_time_option_id = $model->student_time_option_id;

                    /** Child Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */
                    // $timeTableSameBusyChild = TimeTable::find()->where([
                    //     'edu_semester_id' => $timeTableOne->edu_semester_id,
                    //     'edu_year_id' => $timeTableOne->edu_year_id,
                    //     'semester_id' => $timeTableOne->semester_id,
                    //     'para_id' => $timeTableOne->para_id,
                    //     'week_id' => $timeTableOne->week_id,
                    // ])->select('id');

                    // $timeTableSelectedChild = self::find()
                    //     ->where(['in', 'time_table_id', $timeTableSameBusyChild])
                    //     ->andWhere(['student_id' => self::student()])
                    //     ->all();

                    // if (count($timeTableSelectedChild) > 0) {
                    //     $errors[] = _e('You are already busy in this time!');
                    //     return $errors;
                    // }

                    /** Child Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */

                    $newModel->teacher_access_id = $timeTableOne->teacher_access_id;
                    $newModel->language_id = $timeTableOne->language_id;
                    $newModel->course_id = $timeTableOne->course_id;
                    $newModel->semester_id = $timeTableOne->semester_id;
                    $newModel->edu_year_id = $timeTableOne->edu_year_id;
                    $newModel->subject_id = $timeTableOne->subject_id;
                    $newModel->room_id = $timeTableOne->room_id;
                    $newModel->para_id = $timeTableOne->para_id;
                    $newModel->week_id = $timeTableOne->week_id;
                    $newModel->edu_semester_id = $timeTableOne->edu_semester_id;
                    $newModel->subject_category_id = $timeTableOne->subject_category_id;

                    $newModel->time_table_parent_id = $timeTableOne->parent_id;
                    $newModel->time_table_lecture_id = $timeTableOne->lecture_id;
                    $newModel->teacher_user_id = $timeTableOne->teacher_user_id;

                    /**** */
                    $newModel->building_id = $timeTableOne->building_id;
                    $newModel->edu_plan_id = $timeTableOne->edu_plan_id;
                    /**** */

                    if (!$newModel->save()) {
                        $errors[] = _e('Child can not added!');
                    }
                }
            }

            if (count($errors) == 0) {
                return true;
            } else {
                return $errors;
            }
        } else {
            return $errors;
        }
    }
    public static function createItem($model, $post = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        /** Faqat  Student user */ ///
        if (!($model->student_id > 0)) {
            $student = Student::findOne(['user_id' => Current_user_id()]);
            if (!isset($student)) {
                $errors[] = _e('Student not found');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->student_id = self::student();
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $hasModel = self::findOne([
            'student_id' => $model->student_id,
            'time_table_id' => $model->time_table_id,
        ]);

        // $studentCheck = Student::findOne($model->student_id);
        // $timeTableCheck = TimeTable::findOne($model->time_table_id);

        $studentTimeTable = self::find()->where([
            'time_table_id' => $model->time_table_id,
            'is_deleted' => 0
        ])->all();

        if ($model->timeTable->room->capacity <= count($studentTimeTable)) {
            $errors[] = _e('This Time Table is Full!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // // tanlash sonini cheklash
        // if ($model->subject_category_id != 1 &&  count($studentTimeTable) > 35) {
        //     $errors[] = _e('This Time Table is Full! (35)');
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        if ($model->subject_category_id == 2 &&  count($studentTimeTable) > 35) {
            $errors[] = _e('This Time Table is Full! (35)');
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        /**
         *  Student Edu Plan bo'yicha tekshirish
         */
        // if (isset($timeTableCheck)) {
        //     if ($timeTableCheck->eduSemestr) {
        //         if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
        //             $errors[] = $timeTableCheck->eduSemestr;
        //             $errors[] = $studentCheck->edu_plan_id;

        //             $errors[] = _e('This Time Table is not for you');
        //             $transaction->rollBack();
        //             return simplify_errors($errors);
        //         }
        //     }
        // }

        if (isset($hasModel)) {
            $errors[] = _e('This Student Time Table already exists');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        /** Shu fanni tanlaganmi */

        $timeTableSame = TimeTable::find()->where([
            'edu_semester_id' => $model->timeTable->edu_semester_id,
            'edu_year_id' => $model->timeTable->edu_year_id,
            'subject_id' => $model->timeTable->subject_id,
            'semester_id' => $model->timeTable->semester_id,
            'subject_category_id' => $model->timeTable->subject_category_id,
            'parent_id' => null
        ])->select('id');

        $timeTableSelected = self::find()
            ->where(['in', 'time_table_id', $timeTableSame])
            ->andWhere(['student_id' => $model->student_id])
            ->andWhere(['archived' => 0])
            ->all();

        if (isRole("student"))
            if (count($timeTableSelected) > 0) {
                $errors[] = _e('This subject already selected');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

        /** Shu fanni tanlaganmi */

        /** Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */

        $timeTableSameBusy = TimeTable::find()->where([
            'edu_semester_id' => $model->timeTable->edu_semester_id,
            'edu_year_id' => $model->timeTable->edu_year_id,
            'semester_id' => $model->timeTable->semester_id,
            'para_id' => $model->timeTable->para_id,
            'week_id' => $model->timeTable->week_id,
            'archived' => 0,
        ])->select('id');

        $timeTableSelected = self::find()
            ->where(['in', 'time_table_id', $timeTableSameBusy])
            ->andWhere(['student_id' => $model->student_id])
            ->all();

        //Bandligini tekshirish
        if (count($timeTableSelected) > 0) {
            $errors[] = _e('You are busy in this time!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        /** Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */

        //

        $model->teacher_access_id = $model->timeTable->teacher_access_id;
        $model->language_id = $model->timeTable->language_id;
        $model->course_id = $model->timeTable->course_id;
        $model->semester_id = $model->timeTable->semester_id;
        $model->edu_year_id = $model->timeTable->edu_year_id;
        $model->subject_id = $model->timeTable->subject_id;
        $model->room_id = $model->timeTable->room_id;
        $model->building_id = $model->timeTable->room->building_id;
        $model->para_id = $model->timeTable->para_id;
        $model->week_id = $model->timeTable->week_id;
        $model->edu_semester_id = $model->edu_semester_id ?? $model->timeTable->edu_semester_id;

        $model->edu_plan_id = $model->timeTable->eduSemestr->edu_plan_id;
        $model->subject_category_id = $model->timeTable->subject_category_id;

        $model->time_table_parent_id = $model->timeTable->parent_id;
        $model->time_table_lecture_id = $model->timeTable->lecture_id;
        $model->teacher_user_id = $model->timeTable->teacher_user_id;
        $model->time_option_id = $model->timeTable->time_option_id;

        // $model->student_time_option_id = $model->studentTimeOption ? $model->studentTimeOption->id : $model->findStudentTimeOptionId;

        $model->student_time_option_id = $model->studentTimeOption
            ? $model->studentTimeOption->id
            : $model->findStudentTimeOptionId;

        if ($model->save()) {

            // Student child larini yozish
            $timeTables = TimeTable::findAll(['parent_id' => $model->time_table_id]);
            if (isset($timeTables)) {
                foreach ($timeTables as $timeTableOne) {

                    $newModel = new StudentTimeTable();
                    $newModel->student_id = $model->student_id;
                    $newModel->time_table_id = $timeTableOne->id;
                    $newModel->time_option_id = $model->time_option_id;
                    $newModel->student_time_option_id = $model->student_time_option_id;

                    /** Child Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */
                    /*
                    $timeTableSameBusyChild = TimeTable::find()->where([
                        'edu_semester_id' => $timeTableOne->edu_semester_id,
                        'edu_year_id' => $timeTableOne->edu_year_id,
                        'semester_id' => $timeTableOne->semester_id,
                        'para_id' => $timeTableOne->para_id,
                        'week_id' => $timeTableOne->week_id,
                    ])->select('id');

                    $timeTableSelectedChild = self::find()
                        ->where(['in', 'time_table_id', $timeTableSameBusyChild])
                        ->andWhere(['student_id' => $model->student_id])
                        ->all();

                    if (count($timeTableSelectedChild) > 0) {
                        $errors[] = _e('You are already busy in this time!');
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    */
                    /** Child Shu tanlagan payt bola o'zi bo'shmi vaqti bormi */

                    $newModel->teacher_access_id = $timeTableOne->teacher_access_id;
                    $newModel->language_id = $timeTableOne->language_id;
                    $newModel->course_id = $timeTableOne->course_id;
                    $newModel->semester_id = $timeTableOne->semester_id;
                    $newModel->edu_year_id = $timeTableOne->edu_year_id;
                    $newModel->subject_id = $timeTableOne->subject_id;
                    $newModel->room_id = $timeTableOne->room_id;
                    $newModel->para_id = $timeTableOne->para_id;
                    $newModel->week_id = $timeTableOne->week_id;
                    // $newModel->edu_semester_id = $timeTableOne->edu_semester_id;
                    $newModel->edu_semester_id = $model->edu_semester_id ?? $timeTableOne->edu_semester_id;

                    $newModel->subject_category_id = $timeTableOne->subject_category_id;

                    $newModel->time_table_parent_id = $timeTableOne->parent_id;
                    $newModel->time_table_lecture_id = $timeTableOne->lecture_id;
                    $newModel->teacher_user_id = $timeTableOne->teacher_user_id;

                    /**** */
                    $newModel->building_id = $timeTableOne->building_id;
                    $newModel->edu_plan_id = $timeTableOne->edu_plan_id;
                    /**** */

                    if (!$newModel->save()) {
                        $errors[] = _e('Child can not added!');
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
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post, $model_old)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        /**
         *  Faqat  Student user
         */

        $student = Student::findOne(['user_id' => Current_user_id()]);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $model->student_id = $student->id;


        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $hasModel = StudentTimeTable::findOne([
            'student_id' => $model->student_id,
            'time_table_id' => $model->time_table_id,
        ]);

        $studentCheck = Student::findOne($model->student_id);
        $timeTableCheck = TimeTable::findOne($model->time_table_id);

        /**
         *    Student Edu Plan bo'chicha tekshirish
         */

        if (isset($timeTableCheck)) {
            if ($timeTableCheck->eduSemestr) {
                if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
                    $errors[] = _e('This Time Table is not for this Student');
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
        }


        if (isset($hasModel)) {
            $errors[] = _e('This Student Time Table already exists ');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $timeTableChilds = TimeTable::find()->select('id')->where(['parent_id' => $model_old->time_table_id]);

        if (isset($timeTableChilds)) {
            StudentTimeTable::deleteAll(['in', 'time_table_id', $timeTableChilds]);
        }

        if ($model->save()) {
            $model_old->delete();
            $timeTables = TimeTable::findAll(['parent_id' => $model->time_table_id]);
            if (isset($timeTables)) {
                foreach ($timeTables as $timeTable) {
                    $newModel = new StudentTimeTable();
                    $newModel->student_id = $model->student_id;
                    $newModel->time_table_id = $timeTable->id;

                    if (!$newModel->save()) {
                        $errors[] = _e('Child can not added!');
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
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        /* self::deleteAll([
            'in', 'time_table_id',
            TimeTable::find()->where([
                'parent_id' => $model->time_table_id
            ])->select('id'),
            'student_id' => $model->student_id
        ]);



        self::deleteAll([
            'in', 'time_table_id',
            TimeTable::find()->where([
                'lecture_id' => $model->time_table_id
            ])->select('id'),
            'student_id' => $model->student_id
        ]);


        // dd("dddd");

        if (self::deleteAll([
            'in', 'time_table_id',
            TimeTable::find()->where(['parent_id' => $model->time_table_id])->select('id')
        ])) {

            $errors[] = _e('Childs not deleted!');
        }

        if (self::deleteAll([
            'in', 'time_table_id',
            TimeTable::find()->where(['lecture_id' => $model->time_table_id])->select('id')
        ])) {
            $errors[] = _e('Seminars not deleted!');
        } */




        $studentTimeTableLecture = StudentTimeTable::find()->where([
            'in',
            'time_table_id',
            TimeTable::find()->where([
                'lecture_id' => $model->time_table_id
            ])->select('id')

        ])
            ->andWhere(['student_id' => $model->student_id])
            ->all();

        if (isset($studentTimeTableLecture)) {

            foreach ($studentTimeTableLecture as $studentTimeTableLectureOne) {
                if (!$studentTimeTableLectureOne->delete()) {
                    $errors[] = _e('Child ' . $studentTimeTableLectureOne->id . ' not deleted!');
                }
            }
        }

        $studentTimeTableParent = StudentTimeTable::find()->where([
            'in',
            'time_table_id',
            TimeTable::find()->where([
                'parent_id' => $model->time_table_id
            ])->select('id')

        ])
            ->andWhere(['student_id' => $model->student_id])
            ->all();


        if (isset($studentTimeTableParent)) {

            foreach ($studentTimeTableParent as $studentTimeTableParentOne) {
                if (!$studentTimeTableParentOne->delete()) {
                    $errors[] = _e('Child ' . $studentTimeTableParentOne->id . ' not deleted!');
                }
            }
        }

        if (count($errors) == 0) {

            if ($model->delete()) {
                $transaction->commit();
                return true;
            } else {
                $errors[] = _e('StudentTimeTable not deleted!');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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
