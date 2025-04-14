<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%teacher_work_plan}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject_id
 * @property int $edu_year_id
 * @property int|null $semestr_type 1 kuz 2 bahor
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property int|null $student_count Talaba soni
 * @property int|null $student_count_plan Talaba soni reja
 * @property int|null $lecture ma'ruza mashg'uloti
 * @property int|null $lecture_plan ma'ruza mashg'uloti reja
 * @property int|null $seminar Seminar mashg'uloti
 * @property int|null $seminar_plan Seminar mashg'uloti reja
 * @property int|null $practical Amaliy mashg'ulot
 * @property int|null $practical_plan Amaliy mashg'ulot reja
 * @property int|null $labarothoria Labarotoriya mashg'uloti
 * @property int|null $labarothoria_plan Labarotoriya mashg'uloti reja
 * @property int|null $advice Maslahatlar o'tkazish
 * @property int|null $advice_plan Maslahatlar o'tkazish reja
 * @property int|null $prepare Ma'ruza va seminar (amaliy) mashg'ulotlarga tayyorgarlik ko'rish
 * @property int|null $prepare_plan Ma'ruza va seminar (amaliy) mashg'ulotlarga tayyorgarlik ko'rish reja
 * @property int|null $checking Oraliq va yakuniy nazoratlarni tekshirish
 * @property int|null $checking_plan Oraliq va yakuniy nazoratlarni tekshirish reja
 * @property int|null $checking_appeal Yakuniy nazorat turi bo'yicha qo'yilgan balldan norozi bo'lgan talabaning apellyasiya shikoyati ko'rib chiqish bo'yicha apellyasiya komissiyasi a'zosi sifatida ishtirok etish
 * @property int|null $checking_appeal_plan Yakuniy nazorat turi bo'yicha qo'yilgan balldan norozi bo'lgan talabaning apellyasiya shikoyati ko'rib chiqish bo'yicha apellyasiya komissiyasi a'zosi sifatida ishtirok etish reja
 * @property int|null $lead_practice Bakalavriat talabalari amaliyotiga rahbarlik qilish va b.
 * @property int|null $lead_practice_plan Bakalavriat talabalari amaliyotiga rahbarlik qilish va b. reja
 * @property int|null $lead_graduation_work Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish
 * @property int|null $lead_graduation_work_plan Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish reja
 * @property int|null $dissertation_advicer Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish
 * @property int|null $dissertation_advicer_plan Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish reja
 * @property int|null $doctoral_consultation TDYU doktorantiga ilmiy maslahatchilik qilish
 * @property int|null $doctoral_consultation_plan TDYU doktorantiga ilmiy maslahatchilik qilish reja
 * @property int|null $supervisor_exam Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish
 * @property int|null $supervisor_exam_plan Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish reja
 * @property int|null $kazus_input Talabalar bilimini aniqlash bo'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish
 * @property int|null $kazus_input_plan Talabalar bilimini aniqlash bo'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish reja
 * @property int|null $legal_clinic Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish
 * @property int|null $legal_clinic_plan Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish reja
 * @property int|null $final_attestation Yakuniy davlat attestasiyasini o'tkazish
 * @property int|null $final_attestation_plan Yakuniy davlat attestasiyasini o'tkazish reja
 * @property string|null $description
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Course $course
 * @property EduYear $eduYear
 * @property Semestr $semestr
 * @property Subject $subject
 * @property User $user
 */
class TeacherWorkPlan extends \yii\db\ActiveRecord
{

    public static $selected_language = 'uz';

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
        return '{{%teacher_work_plan}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'subject_id', 'edu_year_id'], 'required'],
            [[
                'user_id',
                'faculty_id',
                'time_option_id',
                'subject_id',
                'edu_year_id',
                'semestr_type',
                'course_id',
                'semestr_id',
                'student_count',
                'student_count_plan',
                'lecture',
                'lecture_plan',
                'seminar',
                'seminar_plan',
                'practical',
                'practical_plan',
                'labarothoria',
                'labarothoria_plan',

                'lead_graduation_work',
                'lead_graduation_work_plan',
                'doctoral_consultation',
                'doctoral_consultation_plan',
                'supervisor_exam',
                'supervisor_exam_plan',

                'legal_clinic',
                'legal_clinic_plan',
                'final_attestation',
                'final_attestation_plan',
                'status',
                'is_deleted',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by'
            ], 'integer'],
            [['description'], 'string'],
            [[
                'faculty_ids'
            ], 'safe'],
            [[
                'checking',
                'checking_plan',
                'checking_appeal',
                'checking_appeal_plan',

                'kazus_input',
                'kazus_input_plan',


                'advice',
                'advice_plan',
                'prepare',
                'prepare_plan',
                'lead_practice',
                'lead_practice_plan',
                'dissertation_advicer',
                'dissertation_advicer_plan',
            ], 'double'],


            [
                ['course_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']
            ],
            [
                ['edu_year_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['time_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeOption::className(), 'targetAttribute' => ['time_option_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            [['is_deleted'], 'default', 'value' => 0],
            // ['user_id', 'subject_id', 'edu_year_id', 'unique', 'targetAttribute' => ['user_id', 'subject_id', 'edu_year_id', 'is_deleted',]],
            // ['user_id', 'subject_id', 'edu_year_id', 'unique', 'targetAttribute' => ['user_id', 'subject_id', 'edu_year_id', 'is_deleted',]],
            // [['user_id', 'subject_id', 'time_option_id'], 'unique', 'targetAttribute' => ['user_id', 'time_option_id', 'subject_id', 'is_deleted']],


            // [
            //     // Validate uniqueness of user_id, subject_id, time_option_id when is_deleted is 0.
            //     ['user_id', 'subject_id', 'time_option_id'],
            //     'unique',
            //     'targetAttribute' => ['user_id', 'time_option_id', 'subject_id'],
            //     'when' => function ($model) {
            //         // The 'when' property takes a closure that defines when the validation should occur.
            //         // Return true to apply the validation when is_deleted is 1.
            //         return $model->is_deleted == 1;
            //     },
            // ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'user_id' => _e('User ID'),
            'subject_id' => _e('Subject ID'),
            'faculty_id' => _e('Faculty ID'),
            'time_option_id' => _e('time_option ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'semestr_type' => _e('1 kuz 2 bahor'),
            'course_id' => _e('Course ID'),
            'faculty_ids' => _e('faculty IDS'),
            'semestr_id' => _e('Semestr ID'),
            'student_count' => _e('Talaba soni'),
            'student_count_plan' => _e('Talaba soni reja'),
            'lecture' => _e('ma\'ruza mashg\'uloti'),
            'lecture_plan' => _e('ma\'ruza mashg\'uloti reja'),
            'seminar' => _e('Seminar mashg\'uloti'),
            'seminar_plan' => _e('Seminar mashg\'uloti reja'),
            'practical' => _e('Amaliy mashg\'ulot'),
            'practical_plan' => _e('Amaliy mashg\'ulot reja'),
            'labarothoria' => _e('Labarotoriya mashg\'uloti'),
            'labarothoria_plan' => _e('Labarotoriya mashg\'uloti reja'),
            'advice' => _e('Maslahatlar o\'tkazish'),
            'advice_plan' => _e('Maslahatlar o\'tkazish reja'),
            'prepare' => _e('Ma\'ruza va seminar (amaliy) mashg\'ulotlarga tayyorgarlik ko\'rish'),
            'prepare_plan' => _e('Ma\'ruza va seminar (amaliy) mashg\'ulotlarga tayyorgarlik ko\'rish reja'),
            'checking' => _e('Oraliq va yakuniy nazoratlarni tekshirish'),
            'checking_plan' => _e('Oraliq va yakuniy nazoratlarni tekshirish reja'),
            'checking_appeal' => _e('Yakuniy nazorat turi bo\'yicha qo\'yilgan balldan norozi bo\'lgan talabaning apellyasiya shikoyati ko\'rib chiqish bo\'yicha apellyasiya komissiyasi a\'zosi sifatida ishtirok etish'),
            'checking_appeal_plan' => _e('Yakuniy nazorat turi bo\'yicha qo\'yilgan balldan norozi bo\'lgan talabaning apellyasiya shikoyati ko\'rib chiqish bo\'yicha apellyasiya komissiyasi a\'zosi sifatida ishtirok etish reja'),
            'lead_practice' => _e('Bakalavriat talabalari amaliyotiga rahbarlik qilish va b.'),
            'lead_practice_plan' => _e('Bakalavriat talabalari amaliyotiga rahbarlik qilish va b. reja'),
            'lead_graduation_work' => _e('Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish'),
            'lead_graduation_work_plan' => _e('Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish reja'),
            'dissertation_advicer' => _e('Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish'),
            'dissertation_advicer_plan' => _e('Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish reja'),
            'doctoral_consultation' => _e('TDYU doktorantiga ilmiy maslahatchilik qilish'),
            'doctoral_consultation_plan' => _e('TDYU doktorantiga ilmiy maslahatchilik qilish reja'),
            'supervisor_exam' => _e('Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish'),
            'supervisor_exam_plan' => _e('Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish reja'),
            'kazus_input' => _e('Talabalar bilimini aniqlash bo\'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish'),
            'kazus_input_plan' => _e('Talabalar bilimini aniqlash bo\'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish reja'),
            'legal_clinic' => _e('Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish'),
            'legal_clinic_plan' => _e('Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish reja'),
            'final_attestation' => _e('Yakuniy davlat attestasiyasini o\'tkazish'),
            'final_attestation_plan' => _e('Yakuniy davlat attestasiyasini o\'tkazish reja'),
            'description' => _e('Description'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'subject_id',
            'time_option_id',
            'edu_year_id',
            'semestr_type',
            'course_id',
            'faculty_ids',
            'semestr_id',
            'student_count',
            'student_count_plan',
            'lecture',
            'lecture_plan',
            'seminar',
            'seminar_plan',
            'practical',
            'practical_plan',
            'labarothoria',
            'labarothoria_plan',
            'advice',
            'advice_plan',
            'prepare',
            'prepare_plan',
            'checking',
            'checking_plan',
            'checking_appeal',
            'checking_appeal_plan',
            'lead_practice',
            'lead_practice_plan',
            'lead_graduation_work',
            'lead_graduation_work_plan',
            'dissertation_advicer',
            'dissertation_advicer_plan',
            'doctoral_consultation',
            'doctoral_consultation_plan',
            'supervisor_exam',
            'supervisor_exam_plan',
            'kazus_input',
            'kazus_input_plan',
            'legal_clinic',
            'legal_clinic_plan',
            'final_attestation',
            'final_attestation_plan',
            'description',
            'status',
            'is_deleted',
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
            'faculty',
            'timeOption',
            'course',
            'eduYear',
            'semestr',
            'subject',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[faculty_id]].
     *
     * @return \yii\db\ActiveQuery|CourseQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[time_option_id]].
     *
     * @return \yii\db\ActiveQuery|CourseQuery
     */
    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery|CourseQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
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
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery|SemestrQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    public function countHours($subjectCategory)
    {
        $timeTables = TimeTable::find()
            ->where([
                'time_option_id' => $this->time_option_id,
                'subject_id' => $this->subject_id,
                'teacher_user_id' => $this->user_id,
                'edu_year_id' => $this->edu_year_id,
                'subject_category_id' => $subjectCategory,
                'is_deleted' => 0,
                'archived' => 0
            ])->all();

        $countAll = 0;

        foreach ($timeTables as $timeTable) {
            // Get the start and end dates for the semester
            $dateFromString = $timeTable->eduSemestr->start_date;
            $dateToString = $timeTable->eduSemestr->end_date;

            // Create DateTime objects from the date strings
            $dateFrom = new \DateTime($dateFromString);
            $dateTo = new \DateTime($dateToString);

            $count = 0;

            if ($dateFrom > $dateTo) {
                // If the start date is after the end date, there are no valid weeks, return 0
                return $count;
            }

            // Calculate the number of weeks based on the specified day of the week
            if ($timeTable->week_id != $dateFrom->format('N')) {
                $dateFrom->modify('next ' . $this->dayName()[$timeTable->week_id]);
            }

            while ($dateFrom <= $dateTo) {
                $count++;
                $dateFrom->modify('+1 week');
            }

            $countAll += $count;
        }
        // dd($countAll);
        // $countAll now contains the total number of weeks for the given timeTables
        return $countAll;
    }
    public function countHourss($subjectCategory)
    {
        $timeTables = TimeTable::findAll([
            'time_option_id' => $this->time_option_id,
            'subject_id' => $this->subject_id,
            'teacher_user_id' => $this->user_id,
            'edu_year_id' => $this->edu_year_id,
            'subject_category_id' => $subjectCategory,
            'is_deleted' => 0
        ]);

        $countAll = 0;

        foreach ($timeTables as $timeTable) {

            // time_option_id
            $dateFromString = $timeTable->eduSemestr->start_date;
            $dateToString = $timeTable->eduSemestr->end_date;

            $dateFrom = new \DateTime($dateFromString);
            $dateTo = new \DateTime($dateToString);
            $count = 0;

            if ($dateFrom > $dateTo) {
                return $count;
            }

            if ($this->week_id != $dateFrom->format('N')) {
                $dateFrom->modify('next ' . $this->dayName()[$timeTable->week_id]);
            }

            while ($dateFrom <= $dateTo) {
                $count++;
                $dateFrom->modify('+1 week');
            }

            $countAll += $count;
        }
    }

    /**
     * TeacherWorkPlan createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['edu_year_id'])) {

            $model->edu_year_id = EduYear::findOne(['year' => date("Y")])->id;
        }

        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        $uniqueFacultyIds = array_values(
            array_unique(
                TimeTable::find()
                    ->where([
                        'teacher_user_id' => $model->user_id,
                        'subject_id' => $model->subject_id,

                        'edu_year_id' => $model->edu_year_id,
                    ])
                    ->select('faculty_id')
                    ->asArray()
                    ->column()
            )
        );
        $model->faculty_ids = $uniqueFacultyIds;
        $faculty_count = count($uniqueFacultyIds);

        // if (isset($post['time_option_id'])) {
        //     $eduSemestrSubjectCategoryTimes = null;

        //     if ($model->subject && $model->subject->subjectSillabus) {
        //         $eduSemestrSubjectCategoryTimes = json_decode($model->subject->subjectSillabus->edu_semestr_subject_category_times);
        //     } else {
        //         $errors[] = _e('SubjectSyllabus not included');
        //         $transaction->rollBack();
        //         return simplify_errors($errors);
        //     }

        //     if ($eduSemestrSubjectCategoryTimes === null) {

        //         $errors[] = _e('SubjectSyllabus not included');
        //         $transaction->rollBack();
        //         return simplify_errors($errors);
        //     }
        //     // $edu_semestr_subject_category_times = json_decode($model->subject->subjectSillabus->edu_semestr_subject_category_times);
        //     // $model->edu_year_id = $model->timeOption->edu_year_id;
        //     $model->faculty_id = $model->timeOption->faculty_id;


        //     if (TimeTable::find()->where([
        //         'time_option_id' => $model->time_option_id,
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 1
        //     ])->count() > 0) {
        //         $model->lecture =  $edu_semestr_subject_category_times->{1} ?? 0;
        //     } else {
        //         $model->lecture = 0;
        //     }

        //     if (TimeTable::find()->where([
        //         'time_option_id' => $model->time_option_id,
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 2
        //     ])->count() > 0) {
        //         $model->seminar =  $edu_semestr_subject_category_times->{2} ?? 0;
        //     } else {
        //         $model->seminar = 0;
        //     }

        //     if (TimeTable::find()->where([
        //         'time_option_id' => $model->time_option_id,
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 3
        //     ])->count() > 0) {
        //         $model->practical =  $edu_semestr_subject_category_times->{3} ?? 0;
        //     } else {
        //         $model->practical = 0;
        //     }

        //     if (TimeTable::find()->where([
        //         'time_option_id' => $model->time_option_id,
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 5
        //     ])->count() > 0) {
        //         $model->labarothoria =  $edu_semestr_subject_category_times->{5} ?? 0;
        //     } else {
        //         $model->labarothoria = 0;
        //     }
        // }


        if (isset($post['time_option_id'])) {
            $eduSemestrSubjectCategoryTimes = null;

            if ($model->subject && $model->subject->subjectSillabus) {
                $eduSemestrSubjectCategoryTimes = json_decode($model->subject->subjectSillabus->edu_semestr_subject_category_times);
            } else {
                $errors[] = _e('SubjectSyllabus not included');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            if ($eduSemestrSubjectCategoryTimes === null) {
                $errors[] = _e('SubjectSyllabus not included');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->faculty_id = $model->timeOption->faculty_id;

            $subjectCategories = [
                1 => 'lecture',
                2 => 'seminar',
                3 => 'practical',
                5 => 'labarothoria'
            ];

            foreach ($subjectCategories as $categoryId => $attribute) {
                $count = TimeTable::find()->where([
                    'time_option_id' => $model->time_option_id,
                    'teacher_user_id' => $model->user_id,
                    'subject_id' => $model->subject_id,
                    'edu_year_id' => $model->edu_year_id,
                    'is_deleted' => 0,
                    'subject_category_id' => $categoryId
                ])->count();

                $model->$attribute = $count > 0 ? ($eduSemestrSubjectCategoryTimes->{$categoryId} ?? 0) : 0;
            }
        }


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        // $model->lecture = TimeTable::find()->where([
        //     'teacher_user_id' => $model->user_id,
        //     'subject_id' => $model->subject_id,
        //     'edu_year_id' => $model->edu_year_id,
        //     'is_deleted' => 0,
        //     'subject_category_id' => 1
        // ])->count();

        // $model->seminar =
        //     TimeTable::find()->where([
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 2
        //     ])->count();

        // $model->practical =
        //     TimeTable::find()->where([
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 3
        //     ])->count();

        // $model->labarothoria =
        //     TimeTable::find()->where([
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 5
        //     ])->count();

        $examStudentCount = ExamStudent::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])
            ->andWhere(['edu_year_id' => $model->edu_year_id])
            ->andWhere(['subject_id' => $model->subject_id])
            ->count();

        $examControlStudentCount = ExamControlStudent::find()
            ->andWhere(['teacher_user_id' => $model->user_id])
            ->andWhere(['edu_year_id' => $model->edu_year_id])
            ->andWhere(['subject_id' => $model->subject_id])
            ->count();

        $model->checking = 0.5 * ($examStudentCount + $examControlStudentCount);

        $examAppealCount = ExamAppeal::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])->andWhere(['edu_year_id' => $model->edu_year_id])->count();

        $model->checking_appeal = 0.2 * $examAppealCount;

        $tpyeKazusCount = Question::find()->where([
            'subject_id' => $model->subject_id,
            'created_by' => $model->user_id,
            'question_type_id' => 1,
            'is_deleted' => 0,
            'status' => 1,
        ])
            // ->andWhere(['>', 'created_at', 1693508400])
            ->count();

        $tpyeLogicCount = Question::find()->where([
            'subject_id' => $model->subject_id,
            'created_by' => $model->user_id,
            'question_type_id' => 2,
            'is_deleted' => 0,
            'status' => 1,
        ])
            // ->andWhere(['>', 'created_at', 1693508400])
            ->count();


        $model->kazus_input = 2 *  $tpyeKazusCount + 0.5 * $tpyeLogicCount;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->semestr_type = $model->eduYear->type;

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /*** TeacherWorkPlan updateItem <$model, $post>*/
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        $uniqueFacultyIds = array_values(
            array_unique(
                TimeTable::find()
                    ->where([
                        'teacher_user_id' => $model->user_id,
                        'subject_id' => $model->subject_id,

                        'edu_year_id' => $model->edu_year_id,
                    ])
                    ->select('faculty_id')
                    ->asArray()
                    ->column()
            )
        );
        $model->faculty_ids = $uniqueFacultyIds;
        $faculty_count = count($uniqueFacultyIds);


        if ($model->time_option_id > 0) {

            $edu_semestr_subject_category_times = json_decode($model->subject->subjectSillabus->edu_semestr_subject_category_times);
            // $model->edu_year_id = $model->timeOption->edu_year_id;
            $model->faculty_id = $model->timeOption->faculty_id;


            if (TimeTable::find()->where([
                'time_option_id' => $model->time_option_id,
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 1
            ])->count() > 0) {
                $model->lecture =  $edu_semestr_subject_category_times->{1} ?? 0;
            } else {
                $model->lecture = 0;
            }

            if (TimeTable::find()->where([
                'time_option_id' => $model->time_option_id,
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 2
            ])->count() > 0) {
                $model->seminar =  $edu_semestr_subject_category_times->{2} ?? 0;
            } else {
                $model->seminar = 0;
            }

            if (TimeTable::find()->where([
                'time_option_id' => $model->time_option_id,
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 3
            ])->count() > 0) {
                $model->practical =  $edu_semestr_subject_category_times->{3} ?? 0;
            } else {
                $model->practical = 0;
            }

            if (TimeTable::find()->where([
                'time_option_id' => $model->time_option_id,
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 5
            ])->count() > 0) {
                $model->labarothoria =  $edu_semestr_subject_category_times->{5} ?? 0;
            } else {
                $model->labarothoria = 0;
            }

            // dd([
            //     $model->lecture,
            //     $model->seminar,
            //     $edu_semestr_subject_category_times,
            //     $edu_semestr_subject_category_times->{1},
            //     $edu_semestr_subject_category_times->{2}
            // ]);
        }




        // $model->lecture = TimeTable::find()->where([
        //     'teacher_user_id' => $model->user_id,
        //     'subject_id' => $model->subject_id,
        //     'edu_year_id' => $model->edu_year_id,
        //     'is_deleted' => 0,
        //     'subject_category_id' => 1
        // ])->count();

        // $model->seminar =
        //     TimeTable::find()->where([
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 2
        //     ])->count();

        // $model->practical =
        //     TimeTable::find()->where([
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 3
        //     ])->count();

        // $model->labarothoria =
        //     TimeTable::find()->where([
        //         'teacher_user_id' => $model->user_id,
        //         'subject_id' => $model->subject_id,
        //         'edu_year_id' => $model->edu_year_id,
        //         'is_deleted' => 0,
        //         'subject_category_id' => 5
        //     ])->count();

        $examStudentCount = ExamStudent::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])->andWhere(['edu_year_id' => $model->edu_year_id])
            ->andWhere(['subject_id' => $model->subject_id])
            ->count();

        $examControlStudentCount = ExamControlStudent::find()
            ->andWhere(['teacher_user_id' => $model->user_id])
            ->andWhere(['edu_year_id' => $model->edu_year_id])
            ->andWhere(['subject_id' => $model->subject_id])
            ->count();

        $model->checking = 0.5 * ($examStudentCount + $examControlStudentCount);


        // dd([
        //     ExamStudent::find()->where([
        //         'in', 'teacher_access_id',
        //         TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        //     ])->andWhere(['edu_year_id' => $model->edu_year_id])
        //         ->andWhere(['subject_id' => $model->subject_id])
        //         ->createCommand()->getRawSql(),
        //     $examStudentCount,


        //     ExamControlStudent::find()
        //         ->andWhere(['teacher_user_id' => $model->user_id])
        //         ->andWhere(['edu_year_id' => $model->edu_year_id])
        //         ->andWhere(['subject_id' => $model->subject_id])
        //         ->createCommand()->getRawSql(),
        //     $examControlStudentCount,
        //     $model->checking
        // ]);


        $examAppealCount = ExamAppeal::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])
            ->andWhere(['edu_year_id' => $model->edu_year_id])
            ->andWhere(['subject_id' => $model->subject_id])
            ->count();

        $model->checking_appeal = 0.2 * $examAppealCount;

        $tpyeKazusCount = Question::find()->where([
            'subject_id' => $model->subject_id,
            'created_by' => $model->user_id,
            'question_type_id' => 1,
            'is_deleted' => 0,
            // 'status' => 1,
        ])
            // ->andWhere(['>', 'created_at', 1693508400])
            ->count();

        $tpyeLogicCount = Question::find()->where([
            'subject_id' => $model->subject_id,
            'created_by' => $model->user_id,
            'question_type_id' => 2,
            'is_deleted' => 0,
            // 'status' => 1,
        ])
            // ->andWhere(['>', 'created_at', 1693508400])
            ->count();


        $model->kazus_input = 2 *  $tpyeKazusCount + 0.5 * $tpyeLogicCount;

        $model->semestr_type = $model->eduYear->type;


        // dd($model);
        if ($model->save()) {
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
