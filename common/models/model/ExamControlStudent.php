<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class ExamControlStudent extends ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const USER_STATUS_STUDENT_ACTIVE = 0;

    const USER_STATUS_STUDENT_CONTROL_END = 1;

    const USER_STATUS_STUDENT_TEACHER_END = 2;

    const APPEAL_CHECKED = 2;

    const APPEAL_TYPE_ASOSLI = 1;
    const APPEAL_TYPE_ASOSSIZ = 2;
    const APPEAL_TYPE_TEXNIK = 3;
    const APPEAL_TYPE_ASOSLI_TEXNIK = 4;

    const UPLOADS_FOLDER = 'uploads/exam_control/student_answer/';

    public $upload_file;

    public $answerFileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $answerFileExtension = 'pdf'; // 5 Mb

    public static function tableName()
    {
        return 'exam_control_student';
    }


    public function rules()
    {
        return [
            [
                ['exam_control_id'], 'required'
            ],
            [
                [
                    'exam_control_id',
                    'student_id',
                    'student_user_id',
                    'group_id',
                    'course_id',
                    'semestr_id',
                    'edu_year_id',
                    'subject_id',
                    'language_id',
                    'edu_plan_id',
                    'edu_semestr_id',
                    'subject_category_id',
                    'edu_semestr_exam_type_id',
                    'exam_type_id',
                    'question_count',
                    'finish_time',
                    'start_time',
                    'faculty_id',
                    'direction_id',
                    'edu_year_id',
                    'course_id',
                    'semestr_id',
                    'type',
                    'is_checked',
                    'user_status',
                    'max_ball',
                    'student_ball',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [
                [
                    'answer_file',
                ], 'string',
                'max' => 255
            ],
            ['answer_text' , 'safe'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['exam_control_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamControl::className(), 'targetAttribute' => ['exam_control_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->answerFileExtension, 'maxSize' => $this->answerFileMaxSize],

            ['student_ball' , 'validateBall'],

        ];
    }

    public function validateBall($attribute, $params)
    {
        if ($this->student_ball > $this->max_ball) {
            $this->addError($attribute, _e('The student grade must not be higher than the maximum score!'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'exam_control_id' => _e('Exam Control ID'),
            'student_id' => _e('Student ID'),
            'answer' => _e('Answer'),
            'answer_file' => _e('Answer File'),
            'conclution' => _e('Conclution'),
            'answer2' => _e('Answer2'),
            'answer2_file' => _e('Answer2 File'),
            'conclution2' => _e('Conclution2'),
            'course_id' => _e('Course ID'),
            'semester_id' => _e('Semester ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'subject_id' => _e('Subject ID'),
            'language_id' => _e('Language ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'teacher_user_id' => _e('Teacher User ID'),
            'edu_semester_id' => _e('Edu Semester ID'),
            'subject_category_id' => _e('Subject Category ID'),
            'archived' => _e('Archived'),
            'old_exam_control_id' => _e('Old Exam Control ID'),
            'ball' => _e('Ball'),
            'ball2' => _e('Ball2'),
            'start' => _e('start'),
            'main_ball' => _e('Main Ball'),
            'plagiat_percent' => _e('Plagiat Percent'),
            'plagiat2_percent' => _e('Plagiat Percent2'),
            'plagiat_file' => _e('Plagiat File'),
            'plagiat2_file' => _e('Plagiat File2'),
            'duration' => _e('Duration'),
            'faculty_id' => _e('Faculty ID'),
            'direction_id' => _e('Direction ID'),
            'type' => _e('Type'),
            'category' => _e('Category'),
            'is_checked' => _e('Is Checked'),
            'status' => _e('Status'),
            'order' => _e('Order'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        return [
            'id',
            'exam_control_id',
            'student_id',
            'answer_file',
            'course_id',
            'semestr_id',
            'edu_year_id',
            'subject_id',
            'language_id',
            'max_ball',
            'question_count',
            'is_checked',
            'student_ball',
            'edu_plan_id',
            'user_id',
            'edu_semestr_id',
            'subject_category_id',
            'faculty_id',
            'direction_id',
            'answer_text',
            'answer_file',
            'type',
            'status',
            'user_status',
            'order',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];
    }

    public function extraFields()
    {
        $extraFields =  [
            'course',
            'direction',
            'eduPlan',
            'eduSemester',
            'eduYear',
            'examControl',
            'faculty',
            'language',
            'semester',
            'student',
            'subject',
            'subjectCategory',
            'user',
            'fileInformation',

            'examControlTest',
            'studentTimes',
            'correctCount',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
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

    public function getStudentTimes() {
        return [
            'start' => $this->start_time,
            'finish' => $this->finish_time,
            'current' => time(),
        ];
    }

    public function getFileInformation()
    {
        return [
           'extension' => $this->answerFileExtension,
           'size' => $this->answerFileMaxSize,
        ];
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery|DirectionQuery
     */

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
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
     * Gets query for [[EduSemester]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrQuery
     */
    public function getEduSemester()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semester_id']);
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
     * Gets query for [[ExamControl]].
     *
     * @return \yii\db\ActiveQuery|ExamControlQuery
     */
    public function getExamControl()
    {
        return $this->hasOne(ExamControl::className(), ['id' => 'exam_control_id']);
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
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LanguageQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * Gets query for [[Semester]].
     *
     * @return \yii\db\ActiveQuery|SemestrQuery
     */

    public function getSemester()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semester_id']);
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

    public function getExamControlTest()
    {
        return $this->hasMany(ExamTestStudentAnswer::className(), ['exam_control_student_id' => 'id']);
    }

    public function getCorrectCount() {
        if ($this->examControl->finish_time < time()) {
            $correct = ExamTestStudentAnswer::find()
                ->where([
                    'exam_control_student_id' => $this->id,
                    'status' => 1,
                    'is_deleted' => 0,
                    'is_correct' => 1
                ])
                ->count();
            return $correct;
        }
        return null;
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
     * Gets query for [[TeacherUser]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */

    public function getUser()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        // dd(self::student(2));
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isRole('student')) {
            $model->student_id = self::student();

            if (
                !StudentTimeTable::find()
                    ->where(['time_table_id' => $model->examControl->time_table_id, 'student_id' => $model->student_id])
                    ->count() > 0
            ) {
                $errors[] = _e("You are not in this TimeTable");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $now = time();
        if (isRole('student')) {
            if (isset($post['answer2']) || isset($post['upload2_file'])) {
                if ($model->examControl->start2 > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start2));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish2 < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish2));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            } else {
                if ($model->examControl->start > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            $model->start = $now;
        }

        if (isset($post['ball'])) {
            if ($model->ball > $model->examControl->max_ball) {
                $errors[] = _e('incorrect ball');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        if (isset($post['ball2'])) {
            if ($model->ball2 > $model->examControl->max_ball2) {
                $errors[] = _e('incorrect ball2');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        $model->course_id = $model->examControl->course_id;
        $model->semester_id = $model->examControl->semester_id;
        $model->edu_year_id = $model->examControl->edu_year_id;
        $model->subject_id = $model->examControl->subject_id;
        $model->language_id = $model->examControl->language_id;
        $model->edu_plan_id = $model->examControl->edu_plan_id;
        $model->teacher_user_id = $model->examControl->teacher_user_id;
        $model->edu_semester_id = $model->examControl->edu_semester_id;
        $model->subject_category_id = $model->examControl->subject_category_id;
        $model->old_exam_control_id = $model->examControl->old_exam_control_id;
        $model->faculty_id = $model->examControl->faculty_id;
        $model->direction_id = $model->examControl->direction_id;
        $model->type = $model->examControl->type;
        $model->category = $model->examControl->category;
        $model->main_ball = ($model->ball ?? 0) + ($model->ball2 ?? 0);

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // faqat sirtqi uchun 
        if (
            // !in_array($model->edu_plan_id, [55, 131, 132])
            // &&
            !in_array($model->student->profile->passport_pin, [

                51207025250014,
                50605026600011,
                30207960010019,
                50504056610091

                // 31605986070038,
                // 30306932610102,
                // 62809046590045,
                // 63101047140010,
                // 60302057090017,
                // 51308056350020,
                // 51901035840019,
                // 52101035890040,
                // 52502045860029,
                // 51111037080021,
                // 42111032111032,
                // 50102037010063,
                // 50507035550052,
                // 50109036590017,
                // 51509015310064,
                // 50909016010010,
                // 50107005260013,
                // 63105025780016,
                // 50404035450037,
                // 60101045310016, 61201056350070, 50705035870017, 50603007030033, 51202045970052, 51904046500048, 50707050005041, 61707036350062, 52911045700024, 62104046350036, 51901035820015, 52912035680087, 50803046270047, 53004045680049, 52803055670034, 50908048090010, 52806046490017, 52811046830011, 52504035720050, 50108045950052, 61407047100031, 52702045680030, 52908036080023, 61810046030014, 61810036050059, 52110035890051, 50512046940013, 52706046130059, 50301046130023, 61105055470028, 52808045930044, 52101015040012, 61101045820011, 50512036730048, 61711046600028, 52508045190021, 61609050005008, 62610046780048, 51407046610103, 50709046230053, 51507046260028, 50908046210041, 62305055940034, 63107035840012, 50908040005025, 51401057210055, 60604045830019, 51406027440014, 53006046730020, 60109055140016, 51711046360037, 51703046060033, 51010047210128, 51010045450038, 50804048060030, 50511025790023, 52207046140054, 52308035890029, 62506037320031, 60211057350039, 52402035830037, 61406046900040, 50301045470021, 30803891861663, 50107056910013, 51102046770014, 50907046080021, 51010015440016, 62605046180026, 50206046240018, 52005026320050, 52702046720025, 52301035630031, 50708036820014, 61009055590062, 60708046600029, 51607046780028, 53103046930031, 50307046350010, 52309046950032, 51201056110073, 50802057190047, 53003046590076, 32908946130069, 50301035580039, 52604056610012, 32010912740049, 60403037310069, 52309057360019, 51511045590066, 50803037420019, 50311045820041, 50611055630022, 50701028660018, 50610046930011, 50808046710038, 61909046600010, 50605045840029, 60307047000012, 51407045280035, 52405046180027, 51904065280011, 62608036590012, 52309040005042, 52606046690017, 51407046610095, 32212996950013, 62508045840013, 31207871860076, 51509045590070, 60408046600019, 60402056540015, 50206037020063, 51811046030096, 62511045160054, 53101056590023, 60711035850039, 40503786590018, 50403045780028, 50602037130018, 62108047120014, 50106046720049, 50510046610051, 62402056600014, 51501046390022, 51309057020053, 62009046510010, 50211045200026, 52005047340044, 53004045630037, 52402046900013, 52401046310011, 50307037050037, 51007040005040, 61303046880014, 51304056730011, 52005035780012, 50106056350036, 52112055310074, 62905055730055, 62912046830036, 52706045160033, 52412026020028, 62607036560070, 63006047890030, 51805046810019, 62708040005026, 51601045910068, 52010046530048, 51912046490010, 60103055610106, 61209038300019, 50212046750109, 62001046520013, 53101050005116, 52109045520049, 50111046350024, 61807047350025, 52001036830033, 61702056820027, 53101040251392, 51711046620018, 51402056600021, 62307056540015, 50608056260026, 52303045110028, 50607048660026, 51206055290065, 50805055260033, 51609046590067, 50708050005006, 51609056070010, 53012046180062, 52109046600018, 53009035550014, 51505047190032, 62604046930019, 51002056180011, 53007045820017, 51111047060024, 52107046440018, 52903035460032, 50308047120013, 51108055910054, 51512045140015, 51406055220051, 50207055100016, 51811040005030, 61604055260069, 51609037000027, 60906065910040, 51805055410019, 52504047020049, 50612046520100, 60305056560017, 50808056610010, 61906055220022, 51403035880049, 52503046540042, 53112045540011, 51708056350024, 62309046520010, 52708056600049, 60308057080056, 52508045160013, 51911046440012, 50305046720024, 52311046170017, 52506045700042, 50504050190018, 52906026750040, 50706017020019, 51412046530021, 61811047040055, 50709026920066, 51802046520038, 52307047340036, 52007046500058, 61707056690012, 52212016260036, 51906036980030, 52802036410012, 51708045360045, 51709055740069, 61811036460015, 52310046430026, 63101056180081, 53105046520081, 50404047020011, 52606057060010, 30106996930062, 53009046130023, 51807046490024, 51608047110029, 51003046050015, 50408045960038, 52603046540043, 52404045550024, 52710045210038, 52511046920012, 60106045650012, 51206046590019, 51011057090014, 50602025120025, 60303057040053, 41108922530032, 31705996180065, 50812046230029, 61409046590017, 51808036600043, 50708046570044, 51312037340038, 62511047020051, 50108045650011, 52004046060018, 61209046610041, 50604056070043, 60709046960041, 50805025950056, 63110045260013, 52001055260077, 50810045110056, 52210047040076, 60702046780059, 51112015960106, 60906045400013, 51602026910015, 62503057020033, 52506046530037, 51405045740012, 50604046300019, 52810036450028, 40910860630049, 51604045090026, 53005055310016, 50612047020014, 50803045070011, 31912976950020, 60603047130060, 51407057160026, 52108045590038, 51412046610028, 60903056540020, 50803035630074, 50912045930086, 51810045740025, 62912015320016, 52712035290026, 53103057310017, 52004045790013, 52405046810056, 51802055760018, 51110046530039, 52004056900043, 61006056270021, 50112045540012, 52010046610014, 51008046960023, 60312046530048, 52203055190020, 51105046590053, 52911036730021, 50907046030019, 52010035970064, 31908987150030, 62408046590024, 60505027110012, 51806046840020, 52812055670052, 50405055540021, 51612035630048, 52204016800046, 51306040005064, 53009046820016, 51808036710010, 62307040005012, 61209047040087, 50609048090034, 50505046800016, 52209046600026, 51210040005017, 51811056180044, 60605056610016, 60109056610044, 31108996730045, 60401035760011, 62809046590045, 32702933470026, 52312047150019, 52103035630028, 51307055250016, 53011035080037, 52310047000079, 61509018660037, 51404046210047, 50701036110019, 52907037020083, 60505046470018, 50609045330029, 50812005690017, 52109045710037, 51909056520037, 50408047110055, 51812047190040, 51105006500052, 60403037110012, 52709016070047, 51711035940039, 50502016080014, 51504045910080, 60311040005022, 50404055540011, 52104046020073, 62404046710066, 51811046840077, 51901040005027, 51908046110029, 52210046850023, 50101047060017, 52008045910042, 52606045840011, 51312056150041, 52506045720022, 53107046560023, 52602046320020, 62709045850022, 32807986500073, 51512046950044, 51709048030036, 53008036300031, 62009036080017, 60109048660031, 52103055610150, 52412045470028, 50511036070053, 51307055390015, 50704025050046, 52003046420042, 31107995180093, 53007046180060, 50809046130109, 52708056820076, 50105056920021, 51909037430025, 50108045360053, 50408048300046, 52803046540069, 52205046330022, 52602056960045, 51408036060045, 61807056590012, 51904057060014, 60307026560098, 51604055740049, 50506047340046, 60207055590104, 52603035580027, 60412046030013, 51305047140028, 50410046560047, 52111005630028, 30911830060032, 51611046380023, 62206047140020, 52601057350042, 50702046130013, 60407047020118, 51809035410062, 51601046300044, 62203056450032, 62307056600063, 50211046240058, 61011036820020, 51505015450016, 62702046540017, 51405045730060, 51905036350030, 61512045990015, 51010047040026, 62806046010014, 51804036130047, 51608046260053, 51105045910072, 52911016180103, 51909015700034, 50602045290012, 50201036930023, 50401055830102, 52905035380031, 32903976590023, 52606055740024, 51402026830030, 50406056230096, 62601048660045, 51211025690046, 51510045820012, 52807056570014, 50412046530028, 50108046180142, 52701045590019, 51004035410014, 50405046180069, 61207046350033, 50310046090073, 62006037350013, 60306036500108, 52001056900037, 62801057150049, 51408056600012, 50603046210012, 51802027000045, 50109005310161, 51902035510018, 51404045760013, 52110015830048, 52205055840011, 52212045940068, 51003046950014, 51210046530023, 53101055580016, 50101046230123, 52003046800028, 50807055680114, 62307046430027, 61811036520025, 51912046490010, 52004056230030, 50102046730020, 61601046860031, 52310046920055, 50610046840048, 52702056730012, 50209046210030, 51111040005026, 50112036090049, 50309007110011, 52610046880011, 50606026030040, 50607037000011, 60610055940019, 50811046520039, 50806050005133, 62109046020013, 51706046590014, 52401046090069, 50508046610027, 51703045800036, 51804045450017, 41808890192446, 51009056960078, 51301036400014, 51311055590054, 61707055110025, 41604985700013, 52805037190079, 51112046820048, 52011046800029, 51006035700030, 50705055740026, 52206057120026, 30502934240035, 30204942580061, 52903047150028, 50406047070029, 52212056180013, 50102046180012, 51111047100016, 50208046520044, 40907996560074, 32002985400021, 40806996500030, 62011005060036, 52404005780012, 31605986070038, 51104006180068, 32001977090040, 30905986930022, 31207985150041, 41708976850018, 41410985060028, 32807996860020, 40811943930040, +998977498008, 31501975950100, 50101036060024, 52511045200025, 52610055310059, 62809016570026, 50511045540044, 53010046170052, 62006037350013, 30510966050038, 60404045650033, 53107046520018, 50612025630031, 50904046240033, 52211046730014, 51810035500011, 61809036500059, 50312036330035, 50911046390037, 52404026270024, 62912046240044, 52711045470011, 50204057210042, 50411045450025, 51603035310016, 60311036220079, 62106047190031, 50606046020085, 51312016090033, 33103952410025, 53007026440030, 61208046080065, 41308911070107, 32402995790028, 31412996820010, 41411967090055, 32709830920010, 32105853470015, 51111056950020, 30109930640072, 32005806300018, 51103046520034, 51607026950025, 32308975920024, 61203046300050, 32006986430010, 31805880270033, 32608893160086, 30501932640016, 31907996560025, 32210965840015, 32904966670033, 42001721160011, 30710912640018, 32102976180014, 31304812640035, 32409976270012, 41608863900023, 52204036540021, 30112750580022, 51808056180024, 62106055650022, 52401036590012, 52409045910018, 52806035780013, 31510950470041, 50509045260011, 41112975990062, 32702861780020, 31405976230088, 52709027180021, 41402986560018, 32504911802449, 32808891831098, 51006035710013, 32608893160093, 51803055190030, 60206026520027, 43003862811086, 61807026610023, 51701016930072, 52707057090015, 53004015260026, 52003047340017, 32710986500030, 60901046590053, 51703045800012, 40506956610013, 32909901120062, 30212856590012, 32012956530039, 60702036830027, 50507006100017, 30604986730024, 42211955410037, 50911047190045, 63107036800031, 40103900620038, 32602985860016, 50102015180025, 41403802360034, 51606015510051, 50708055540030, 31911921240030, 32403950520041, 50410037190019, 30810966150049, 52409047140017, 51010046790056, 52905008660021, 32706966560039, 31404952400059, 32404976080012, 32403951120040, 51206045160066, 32201823440029, 50411036500065, 30207960010019, 51210046560086, 32108923990051, 32108880770026, 50609035030012, 63006036590020, 52512046920039, 50909046480038, 40605976560033, 61708050005037, 32401892930032, 32101976500057, 32108995150029, 51202056180107, 51608047210037, 62008045040065, 31603924270029, 31803963120044, 31708850040068, 30406860570014, 50602015720031, 53107036500118, 52005056350014, 52404036940027, 31905940420027, 41603753450020, 42901840500014, 51310047010035, 52205045650057, 31606925670018, 51203015120020, 31710731780019, 30405996840013, 40910871070135, 32111986560015, 50303055730012, 33001841370011, 31604966720019, 50406056230096, 60303046130064, 53103036590056, 30603975600012, 32605954310066, 32701873120015, 52809045960017, 62808036360011, 31205915550019, 52112016540041, 30108872720093, 42105912720072, 32904935290015, 32704961000017, 31401956290022, 50910035820016, 42002890570074, 40904760590013, 31607952390045, 32710850770024, 31507816350018, 31606821930034, 43011927340026, 41008940470012, 50407015680042, 41312914180019, 52707006500019, 31305870430066, 50211026310023, 30812814250051, 32706976400019, 32108976820017, 51505036400032, 52904026280064, 62311047190035, 51601046300044, 42007902640066, 52907045130020, 52109017020032, 31312902380066, 42407975610012, 52207045440047, 31012892740016, 52810006060020, 52104036520040, 61807015450012, 32011976750036, 30809881811171, 50608035740020, 40110966590072, 51706047340034, 50910035820016, 62009026800012, 61507046930038, 61909026590032, 50603046210012, 50907046900017, 52105035550027, 30211997040073, 52910025060046, 60105047190025, 52508035160047, 52505047310017, 52703035510029, 51212035700020, 51509036430011, 62709056610039, 32909966090050, 51812036290096, 51110036320033, 41312914180019, 50206025450027, 52008046950029, 52907045590019, 52104045830044, 32305942390059, 52410045470022, 50601025410042, 51904036450014, 62406015730017, 52511035540074, 51910026330031, 50302046320045, 50806045210034, 52810046520063, 52004036040017, 52903016320025, 52911046900012, 51510047170047, 51807035300013, 52004036080053, 30607927210070, 40908881650023, 52207036980046, 30905956500026, 51507046810036, 50507006130049, 31208995670026, 50507045820013, 50111005500013, 31812977140010, 30503975900014, 50604047150039, 50404038670018, 42707901140072, 60109046010010, 32702952940021, 42608862860017, 50901056810036, 30609915740017, 50510055350012, 50707046390012, 41006810520072, 31404952400059, 31710731780019, 31910822380038, 30406943180013, 62101006610065, 50901036950017, 40807831140060, 52908036590049, 51007026350061, 52610027890031, 61909035880025, 51410015590047, 52304025430024, 52802035890064, 52404045090010, 41901901861123, 50207035900010, 52112025960137, 61110038060055, 30207786690011, 40801862880023, 51309016700017, 31202965550046, 51510015830063, 50406036880017, 50608037040012, 32709996050020, 52507035740046, 52610016050066, 52701045590019, 50401025180060, 52006026170032, 52805025060029, 50508025060012, 32302953080012, 50708045580024, 51502047170015, 50601025900033, 41102966030053, 50205035820031, 62001046170038, 51511026560019, 30407995630062, 52204035370010, 51205025030012, 50911025700024, 53110026590033, 51401047040026, 52904026090045, 40311851170014, 32301987140015, 51609037020021, 50308006090104, 63001045740040, 50304036300064, 52207036300044, 31101892130021, 52702025380026, 60509046030025, 50404047110015, 51412037130023, 51408045170054, 61512005470018, 31503996170022, 51612015560013, 51602016800027, 50906036260012, 50507027140029, 61103045550022, 51201015310029, 53112027890035, 61409015360041, 52101028660025, 30912995700069, 52106016230053, 61405006710013, 32101941790025, 53003036280017, 52509027120027, 52208026390043, 52204015430040, 51802035110020, 51008036730016, 60102036500104, 52204015430040, 32511942750014, 32504985140041, 41405901000033, 31211901771146, 52301006240050, 31712986800036, 60705016080029, 32611996280018, 51612025550013, 60902027040062, 50707006350023, 30409882930019, 51601045650018, 53006035860015, 32501953120142, 31104975300034, 51904026240032, 31301840590035, 61804046350023, 40207902740018, 42905873080024, 31606987140065, 51103046520034, 60207006600016, 50808026350038, 32711932890018, 62610005080033, 42906892860022, 40112891220127

            ])
        ) {
            $errors[] = ["Ruxsat berilmagan"];
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // faqat sirtqi uchun 


        if ($model->save()) {

            // answer file saqlaymiz
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_FileUrl = $model->uploadFile($model->upload_file);
                if ($upload_FileUrl) {
                    $model->answer_file = $upload_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // answer file saqlaymiz
            $model->upload2_file = UploadedFile::getInstancesByName('upload2_file');
            if ($model->upload2_file) {
                $model->upload2_file = $model->upload2_file[0];
                $upload2_FileUrl = $model->uploadFile($model->upload2_file);
                if ($upload2_FileUrl) {
                    $model->answer2_file = $upload2_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // aplagiat file saqlaymiz
            $model->upload_plagiat_file = UploadedFile::getInstancesByName('upload_plagiat_file');
            if ($model->upload_plagiat_file) {
                $model->upload_plagiat_file = $model->upload_plagiat_file[0];
                $upload_plagiat_fileUrl = $model->uploadFile($model->upload_plagiat_file);
                if ($upload_plagiat_fileUrl) {
                    $model->plagiat_file = $upload_plagiat_fileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // aplagiat file saqlaymiz
            $model->upload_plagiat2_file = UploadedFile::getInstancesByName('upload_plagiat2_file');
            if ($model->upload_plagiat2_file) {
                $model->upload_plagiat2_file = $model->upload_plagiat2_file[0];
                $upload_plagiat2_fileUrl = $model->uploadFile($model->upload_plagiat2_file);
                if ($upload_plagiat2_fileUrl) {
                    $model->plagiat2_file = $upload_plagiat2_fileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            if ($model->save()) {
                $transaction->commit();
                return true;
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $now = time();
        if (isRole('student')) {

            if (isset($post['answer2']) || isset($post['upload2_file'])) {
                if ($model->examControl->start2 > $now) {
                    $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start2));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($model->examControl->finish2 < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish2));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            } else {

                if ($model->ball > 0)
                    if ($model->examControl->start > $now) {
                        $errors[] = _e("After " . date('Y-m-d H:m:i', $model->examControl->start));
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                if ($model->examControl->finish < $now) {
                    $errors[] = _e("Before " . date('Y-m-d H:m:i', $model->examControl->finish));
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            $model->start = $now;
        }

        if (isset($post['ball'])) {
            if ($model->ball > $model->examControl->max_ball) {
                $errors[] = _e('incorrect ball');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        if (isset($post['ball2'])) {
            if ($model->ball2 > $model->examControl->max_ball2) {
                $errors[] = _e('incorrect ball2');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // $model->main_ball = $model->ball ?? 0 + $model->ball2 ?? 0;
        $model->main_ball = ($model->ball ?? 0) + ($model->ball2 ?? 0);

        // faqat sirtqi uchun 
        if (
            // !in_array($model->edu_plan_id, [55, 131, 132])
            // &&
            !in_array($model->student->profile->passport_pin, [

                51207025250014,
                50605026600011,
                30207960010019,
                50504056610091

                // 31605986070038,
                // 30306932610102,
                // 62809046590045,
                // 63101047140010,
                // 60302057090017,
                // 51308056350020,
                // 51901035840019,
                // 52101035890040,
                // 52502045860029,
                // 51111037080021,
                // 42111032111032,
                // 50102037010063,
                // 50507035550052,
                // 50109036590017,
                // 51509015310064,
                // 50909016010010,
                // 50107005260013,
                // 63105025780016,
                // 50404035450037,
                // 60101045310016, 61201056350070, 50705035870017, 50603007030033, 51202045970052, 51904046500048, 50707050005041, 61707036350062, 52911045700024, 62104046350036, 51901035820015, 52912035680087, 50803046270047, 53004045680049, 52803055670034, 50908048090010, 52806046490017, 52811046830011, 52504035720050, 50108045950052, 61407047100031, 52702045680030, 52908036080023, 61810046030014, 61810036050059, 52110035890051, 50512046940013, 52706046130059, 50301046130023, 61105055470028, 52808045930044, 52101015040012, 61101045820011, 50512036730048, 61711046600028, 52508045190021, 61609050005008, 62610046780048, 51407046610103, 50709046230053, 51507046260028, 50908046210041, 62305055940034, 63107035840012, 50908040005025, 51401057210055, 60604045830019, 51406027440014, 53006046730020, 60109055140016, 51711046360037, 51703046060033, 51010047210128, 51010045450038, 50804048060030, 50511025790023, 52207046140054, 52308035890029, 62506037320031, 60211057350039, 52402035830037, 61406046900040, 50301045470021, 30803891861663, 50107056910013, 51102046770014, 50907046080021, 51010015440016, 62605046180026, 50206046240018, 52005026320050, 52702046720025, 52301035630031, 50708036820014, 61009055590062, 60708046600029, 51607046780028, 53103046930031, 50307046350010, 52309046950032, 51201056110073, 50802057190047, 53003046590076, 32908946130069, 50301035580039, 52604056610012, 32010912740049, 60403037310069, 52309057360019, 51511045590066, 50803037420019, 50311045820041, 50611055630022, 50701028660018, 50610046930011, 50808046710038, 61909046600010, 50605045840029, 60307047000012, 51407045280035, 52405046180027, 51904065280011, 62608036590012, 52309040005042, 52606046690017, 51407046610095, 32212996950013, 62508045840013, 31207871860076, 51509045590070, 60408046600019, 60402056540015, 50206037020063, 51811046030096, 62511045160054, 53101056590023, 60711035850039, 40503786590018, 50403045780028, 50602037130018, 62108047120014, 50106046720049, 50510046610051, 62402056600014, 51501046390022, 51309057020053, 62009046510010, 50211045200026, 52005047340044, 53004045630037, 52402046900013, 52401046310011, 50307037050037, 51007040005040, 61303046880014, 51304056730011, 52005035780012, 50106056350036, 52112055310074, 62905055730055, 62912046830036, 52706045160033, 52412026020028, 62607036560070, 63006047890030, 51805046810019, 62708040005026, 51601045910068, 52010046530048, 51912046490010, 60103055610106, 61209038300019, 50212046750109, 62001046520013, 53101050005116, 52109045520049, 50111046350024, 61807047350025, 52001036830033, 61702056820027, 53101040251392, 51711046620018, 51402056600021, 62307056540015, 50608056260026, 52303045110028, 50607048660026, 51206055290065, 50805055260033, 51609046590067, 50708050005006, 51609056070010, 53012046180062, 52109046600018, 53009035550014, 51505047190032, 62604046930019, 51002056180011, 53007045820017, 51111047060024, 52107046440018, 52903035460032, 50308047120013, 51108055910054, 51512045140015, 51406055220051, 50207055100016, 51811040005030, 61604055260069, 51609037000027, 60906065910040, 51805055410019, 52504047020049, 50612046520100, 60305056560017, 50808056610010, 61906055220022, 51403035880049, 52503046540042, 53112045540011, 51708056350024, 62309046520010, 52708056600049, 60308057080056, 52508045160013, 51911046440012, 50305046720024, 52311046170017, 52506045700042, 50504050190018, 52906026750040, 50706017020019, 51412046530021, 61811047040055, 50709026920066, 51802046520038, 52307047340036, 52007046500058, 61707056690012, 52212016260036, 51906036980030, 52802036410012, 51708045360045, 51709055740069, 61811036460015, 52310046430026, 63101056180081, 53105046520081, 50404047020011, 52606057060010, 30106996930062, 53009046130023, 51807046490024, 51608047110029, 51003046050015, 50408045960038, 52603046540043, 52404045550024, 52710045210038, 52511046920012, 60106045650012, 51206046590019, 51011057090014, 50602025120025, 60303057040053, 41108922530032, 31705996180065, 50812046230029, 61409046590017, 51808036600043, 50708046570044, 51312037340038, 62511047020051, 50108045650011, 52004046060018, 61209046610041, 50604056070043, 60709046960041, 50805025950056, 63110045260013, 52001055260077, 50810045110056, 52210047040076, 60702046780059, 51112015960106, 60906045400013, 51602026910015, 62503057020033, 52506046530037, 51405045740012, 50604046300019, 52810036450028, 40910860630049, 51604045090026, 53005055310016, 50612047020014, 50803045070011, 31912976950020, 60603047130060, 51407057160026, 52108045590038, 51412046610028, 60903056540020, 50803035630074, 50912045930086, 51810045740025, 62912015320016, 52712035290026, 53103057310017, 52004045790013, 52405046810056, 51802055760018, 51110046530039, 52004056900043, 61006056270021, 50112045540012, 52010046610014, 51008046960023, 60312046530048, 52203055190020, 51105046590053, 52911036730021, 50907046030019, 52010035970064, 31908987150030, 62408046590024, 60505027110012, 51806046840020, 52812055670052, 50405055540021, 51612035630048, 52204016800046, 51306040005064, 53009046820016, 51808036710010, 62307040005012, 61209047040087, 50609048090034, 50505046800016, 52209046600026, 51210040005017, 51811056180044, 60605056610016, 60109056610044, 31108996730045, 60401035760011, 62809046590045, 32702933470026, 52312047150019, 52103035630028, 51307055250016, 53011035080037, 52310047000079, 61509018660037, 51404046210047, 50701036110019, 52907037020083, 60505046470018, 50609045330029, 50812005690017, 52109045710037, 51909056520037, 50408047110055, 51812047190040, 51105006500052, 60403037110012, 52709016070047, 51711035940039, 50502016080014, 51504045910080, 60311040005022, 50404055540011, 52104046020073, 62404046710066, 51811046840077, 51901040005027, 51908046110029, 52210046850023, 50101047060017, 52008045910042, 52606045840011, 51312056150041, 52506045720022, 53107046560023, 52602046320020, 62709045850022, 32807986500073, 51512046950044, 51709048030036, 53008036300031, 62009036080017, 60109048660031, 52103055610150, 52412045470028, 50511036070053, 51307055390015, 50704025050046, 52003046420042, 31107995180093, 53007046180060, 50809046130109, 52708056820076, 50105056920021, 51909037430025, 50108045360053, 50408048300046, 52803046540069, 52205046330022, 52602056960045, 51408036060045, 61807056590012, 51904057060014, 60307026560098, 51604055740049, 50506047340046, 60207055590104, 52603035580027, 60412046030013, 51305047140028, 50410046560047, 52111005630028, 30911830060032, 51611046380023, 62206047140020, 52601057350042, 50702046130013, 60407047020118, 51809035410062, 51601046300044, 62203056450032, 62307056600063, 50211046240058, 61011036820020, 51505015450016, 62702046540017, 51405045730060, 51905036350030, 61512045990015, 51010047040026, 62806046010014, 51804036130047, 51608046260053, 51105045910072, 52911016180103, 51909015700034, 50602045290012, 50201036930023, 50401055830102, 52905035380031, 32903976590023, 52606055740024, 51402026830030, 50406056230096, 62601048660045, 51211025690046, 51510045820012, 52807056570014, 50412046530028, 50108046180142, 52701045590019, 51004035410014, 50405046180069, 61207046350033, 50310046090073, 62006037350013, 60306036500108, 52001056900037, 62801057150049, 51408056600012, 50603046210012, 51802027000045, 50109005310161, 51902035510018, 51404045760013, 52110015830048, 52205055840011, 52212045940068, 51003046950014, 51210046530023, 53101055580016, 50101046230123, 52003046800028, 50807055680114, 62307046430027, 61811036520025, 51912046490010, 52004056230030, 50102046730020, 61601046860031, 52310046920055, 50610046840048, 52702056730012, 50209046210030, 51111040005026, 50112036090049, 50309007110011, 52610046880011, 50606026030040, 50607037000011, 60610055940019, 50811046520039, 50806050005133, 62109046020013, 51706046590014, 52401046090069, 50508046610027, 51703045800036, 51804045450017, 41808890192446, 51009056960078, 51301036400014, 51311055590054, 61707055110025, 41604985700013, 52805037190079, 51112046820048, 52011046800029, 51006035700030, 50705055740026, 52206057120026, 30502934240035, 30204942580061, 52903047150028, 50406047070029, 52212056180013, 50102046180012, 51111047100016, 50208046520044, 40907996560074, 32002985400021, 40806996500030, 62011005060036, 52404005780012, 31605986070038, 51104006180068, 32001977090040, 30905986930022, 31207985150041, 41708976850018, 41410985060028, 32807996860020, 40811943930040, +998977498008, 31501975950100, 50101036060024, 52511045200025, 52610055310059, 62809016570026, 50511045540044, 53010046170052, 62006037350013, 30510966050038, 60404045650033, 53107046520018, 50612025630031, 50904046240033, 52211046730014, 51810035500011, 61809036500059, 50312036330035, 50911046390037, 52404026270024, 62912046240044, 52711045470011, 50204057210042, 50411045450025, 51603035310016, 60311036220079, 62106047190031, 50606046020085, 51312016090033, 33103952410025, 53007026440030, 61208046080065, 41308911070107, 32402995790028, 31412996820010, 41411967090055, 32709830920010, 32105853470015, 51111056950020, 30109930640072, 32005806300018, 51103046520034, 51607026950025, 32308975920024, 61203046300050, 32006986430010, 31805880270033, 32608893160086, 30501932640016, 31907996560025, 32210965840015, 32904966670033, 42001721160011, 30710912640018, 32102976180014, 31304812640035, 32409976270012, 41608863900023, 52204036540021, 30112750580022, 51808056180024, 62106055650022, 52401036590012, 52409045910018, 52806035780013, 31510950470041, 50509045260011, 41112975990062, 32702861780020, 31405976230088, 52709027180021, 41402986560018, 32504911802449, 32808891831098, 51006035710013, 32608893160093, 51803055190030, 60206026520027, 43003862811086, 61807026610023, 51701016930072, 52707057090015, 53004015260026, 52003047340017, 32710986500030, 60901046590053, 51703045800012, 40506956610013, 32909901120062, 30212856590012, 32012956530039, 60702036830027, 50507006100017, 30604986730024, 42211955410037, 50911047190045, 63107036800031, 40103900620038, 32602985860016, 50102015180025, 41403802360034, 51606015510051, 50708055540030, 31911921240030, 32403950520041, 50410037190019, 30810966150049, 52409047140017, 51010046790056, 52905008660021, 32706966560039, 31404952400059, 32404976080012, 32403951120040, 51206045160066, 32201823440029, 50411036500065, 30207960010019, 51210046560086, 32108923990051, 32108880770026, 50609035030012, 63006036590020, 52512046920039, 50909046480038, 40605976560033, 61708050005037, 32401892930032, 32101976500057, 32108995150029, 51202056180107, 51608047210037, 62008045040065, 31603924270029, 31803963120044, 31708850040068, 30406860570014, 50602015720031, 53107036500118, 52005056350014, 52404036940027, 31905940420027, 41603753450020, 42901840500014, 51310047010035, 52205045650057, 31606925670018, 51203015120020, 31710731780019, 30405996840013, 40910871070135, 32111986560015, 50303055730012, 33001841370011, 31604966720019, 50406056230096, 60303046130064, 53103036590056, 30603975600012, 32605954310066, 32701873120015, 52809045960017, 62808036360011, 31205915550019, 52112016540041, 30108872720093, 42105912720072, 32904935290015, 32704961000017, 31401956290022, 50910035820016, 42002890570074, 40904760590013, 31607952390045, 32710850770024, 31507816350018, 31606821930034, 43011927340026, 41008940470012, 50407015680042, 41312914180019, 52707006500019, 31305870430066, 50211026310023, 30812814250051, 32706976400019, 32108976820017, 51505036400032, 52904026280064, 62311047190035, 51601046300044, 42007902640066, 52907045130020, 52109017020032, 31312902380066, 42407975610012, 52207045440047, 31012892740016, 52810006060020, 52104036520040, 61807015450012, 32011976750036, 30809881811171, 50608035740020, 40110966590072, 51706047340034, 50910035820016, 62009026800012, 61507046930038, 61909026590032, 50603046210012, 50907046900017, 52105035550027, 30211997040073, 52910025060046, 60105047190025, 52508035160047, 52505047310017, 52703035510029, 51212035700020, 51509036430011, 62709056610039, 32909966090050, 51812036290096, 51110036320033, 41312914180019, 50206025450027, 52008046950029, 52907045590019, 52104045830044, 32305942390059, 52410045470022, 50601025410042, 51904036450014, 62406015730017, 52511035540074, 51910026330031, 50302046320045, 50806045210034, 52810046520063, 52004036040017, 52903016320025, 52911046900012, 51510047170047, 51807035300013, 52004036080053, 30607927210070, 40908881650023, 52207036980046, 30905956500026, 51507046810036, 50507006130049, 31208995670026, 50507045820013, 50111005500013, 31812977140010, 30503975900014, 50604047150039, 50404038670018, 42707901140072, 60109046010010, 32702952940021, 42608862860017, 50901056810036, 30609915740017, 50510055350012, 50707046390012, 41006810520072, 31404952400059, 31710731780019, 31910822380038, 30406943180013, 62101006610065, 50901036950017, 40807831140060, 52908036590049, 51007026350061, 52610027890031, 61909035880025, 51410015590047, 52304025430024, 52802035890064, 52404045090010, 41901901861123, 50207035900010, 52112025960137, 61110038060055, 30207786690011, 40801862880023, 51309016700017, 31202965550046, 51510015830063, 50406036880017, 50608037040012, 32709996050020, 52507035740046, 52610016050066, 52701045590019, 50401025180060, 52006026170032, 52805025060029, 50508025060012, 32302953080012, 50708045580024, 51502047170015, 50601025900033, 41102966030053, 50205035820031, 62001046170038, 51511026560019, 30407995630062, 52204035370010, 51205025030012, 50911025700024, 53110026590033, 51401047040026, 52904026090045, 40311851170014, 32301987140015, 51609037020021, 50308006090104, 63001045740040, 50304036300064, 52207036300044, 31101892130021, 52702025380026, 60509046030025, 50404047110015, 51412037130023, 51408045170054, 61512005470018, 31503996170022, 51612015560013, 51602016800027, 50906036260012, 50507027140029, 61103045550022, 51201015310029, 53112027890035, 61409015360041, 52101028660025, 30912995700069, 52106016230053, 61405006710013, 32101941790025, 53003036280017, 52509027120027, 52208026390043, 52204015430040, 51802035110020, 51008036730016, 60102036500104, 52204015430040, 32511942750014, 32504985140041, 41405901000033, 31211901771146, 52301006240050, 31712986800036, 60705016080029, 32611996280018, 51612025550013, 60902027040062, 50707006350023, 30409882930019, 51601045650018, 53006035860015, 32501953120142, 31104975300034, 51904026240032, 31301840590035, 61804046350023, 40207902740018, 42905873080024, 31606987140065, 51103046520034, 60207006600016, 50808026350038, 32711932890018, 62610005080033, 42906892860022, 40112891220127

            ])
        ) {
            $errors[] = ["Ruxsat berilmagan"];
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // faqat sirtqi uchun 

        if ($model->save()) {

            // answer file saqlaymiz
            $model->upload_file = UploadedFile::getInstancesByName('upload_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_FileUrl = $model->uploadFile($model->upload_file);
                if ($upload_FileUrl) {
                    $model->answer_file = $upload_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // answer file saqlaymiz
            $model->upload2_file = UploadedFile::getInstancesByName('upload2_file');
            if ($model->upload2_file) {
                $model->upload2_file = $model->upload2_file[0];
                $upload2_FileUrl = $model->uploadFile($model->upload2_file);
                if ($upload2_FileUrl) {
                    $model->answer2_file = $upload2_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // aplagiat file saqlaymiz
            $model->upload_plagiat_file = UploadedFile::getInstancesByName('upload_plagiat_file');
            if ($model->upload_plagiat_file) {
                $model->upload_plagiat_file = $model->upload_plagiat_file[0];
                $upload_plagiat_fileUrl = $model->uploadFile($model->upload_plagiat_file);
                if ($upload_plagiat_fileUrl) {
                    $model->plagiat_file = $upload_plagiat_fileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }

            // aplagiat file saqlaymiz
            $model->upload_plagiat2_file = UploadedFile::getInstancesByName('upload_plagiat2_file');
            if ($model->upload_plagiat2_file) {
                $model->upload_plagiat2_file = $model->upload_plagiat2_file[0];
                $upload_plagiat2_fileUrl = $model->uploadFile($model->upload_plagiat2_file);
                if ($upload_plagiat2_fileUrl) {
                    $model->plagiat2_file = $upload_plagiat2_fileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }


            if ($model->save()) {
                $transaction->commit();
                return true;
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function studentFileUpload($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if ($model->user_status != self::USER_STATUS_STUDENT_ACTIVE) {
            $errors[] = _e("Exam Control time is over.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        if ($model->type == 1) {
            // question file saqlaymiz
            $model->upload_file = UploadedFile::getInstancesByName('answer_file');
            if ($model->upload_file) {
                $model->upload_file = $model->upload_file[0];
                $upload_FileUrl = $model->upload($model->upload_file);
                if ($upload_FileUrl) {
                    $model->answer_file = $upload_FileUrl;
                } else {
                    $errors[] = $model->errors;
                }
            }
            if (isset($post['answer_text'])) {
                $model->answer_text = $post['answer_text'];
            }
            if ($model->answer_text == null && $model->answer_file == null) {
                $errors[] = _e("Enter File or Reply text.");
            }
            $model->user_status = self::USER_STATUS_STUDENT_CONTROL_END;
        } elseif ($model->type == 2) {
            $answer = ExamTestStudentAnswer::find()
                ->where([
                    'exam_control_student_id' => $model->id,
                    'exam_control_id' => $model->exam_control_id,
                    'user_id' => $model->student_user_id,
                    'is_deleted' => 0
                ])
                ->all();
            if (isset($answer)) {
                $correct_answer = 0;
                foreach ($answer as $item) {
                    if ($item->is_correct == 1) {
                        $correct_answer++;
                    }
                }
                $test_percent = ($correct_answer * 100) / $model->question_count;
                $model->test_percent = $test_percent;
                $model->student_ball = number_format((($test_percent * $model->max_ball) / 100) , 1);
                $model->user_status = self::USER_STATUS_STUDENT_TEACHER_END;
                // Student Mark
//                $studentMark = StudentMark::find()
//                    ->where([
//                        'exam_type_id' => $model->exam_type_id,
//                        'student_id' => $model->student_id,
//                        'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
//                        'subject_id' => $model->subject_id,
//                        'semestr_id' => $model->semestr_id,
//                        'course_id' => $model->course_id,
//                        'status' => 1,
//                        'is_deleted' => 0,
//                    ])
//                    ->one();
//                if ($studentMark != null) {
//                    $studentMark->is_deleted = 1;
//                    $studentMark->is_deleted_date = date("Y-m-d H:i:s");
//                    $studentMark->save(false);
//                }
//                $mark = new StudentMark();
//                $mark->edu_semestr_exams_type_id = $model->edu_semestr_exam_type_id;
//                $mark->exam_type_id = $model->exam_type_id;
//                $mark->group_id = $model->group_id;
//                $mark->student_id = $model->student_id;
//                $mark->student_user_id = $model->student->user_id;
//                $mark->max_ball = $model->max_ball;
//                $mark->ball = $model->student_ball;
//                $mark->edu_semestr_subject_id = $model->edu_semestr_subject_id;
//                $mark->subject_id = $model->subject_id;
//                $mark->edu_plan_id = $model->edu_plan_id;
//                $mark->edu_semestr_id = $model->edu_semestr_id;
//                $mark->faculty_id = $model->faculty_id;
//                $mark->direction_id = $model->direction_id;
//                $mark->semestr_id = $model->semestr_id;
//                $mark->course_id = $model->course_id;
//                $mark->exam_control_id = $model->examControl->id;
//                $mark->exam_control_student_id = $model->id;
//                $mark->type = $model->examControl->type;
//                if (!$mark->validate()) {
//                    $errors[] = $mark->errors;
//                    $transaction->rollBack();
//                    return simplify_errors($errors);
//                }
//                $mark->save(false);
            }
        }
        $model->finish_time = time();
        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateCheck($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (isset($post['is_checked'])) {
            $model->is_checked = $post['is_checked'];
            $model->save(false);
        } else {
            $errors[] = ['is_checked' => 'Required!'];
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function rating($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->user_id != current_user_id() && !isRole('admin')) {
            $errors[] = _e("This user cannot grade a student.");
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['ball'])) {
            $model->student_ball = $post['ball'];
            if (!$model->validate()) {
                $errors[] = $model->errors;
            }
        } else {
            $errors[] = ['is_checked' => 'Required!'];
        }
        if (count($errors) == 0) {
            $model->user_status = self::USER_STATUS_STUDENT_TEACHER_END;
            $model->save(false);
//            $studentMark = StudentMark::find()
//                ->where([
//                    'exam_type_id' => $model->exam_type_id,
//                    'student_id' => $model->student_id,
//                    'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
//                    'subject_id' => $model->subject_id,
//                    'semestr_id' => $model->semestr_id,
//                    'course_id' => $model->course_id,
//                    'status' => 1,
//                    'is_deleted' => 0,
//                ])
//                ->one();
//            if ($studentMark != null) {
//                $studentMark->is_deleted = 1;
//                $studentMark->is_deleted_date = date("Y-m-d H:i:s");
//                $studentMark->save(false);
//            }
//            $mark = new StudentMark();
//            $mark->exam_type_id = $model->edu_semestr_exam_type_id;
//            $mark->type = $model->exam_type_id;
//            $mark->group_id = $model->group_id;
//            $mark->student_id = $model->student_id;
//            $mark->max_ball = $model->max_ball;
//            $mark->ball = $model->student_ball;
//            $mark->edu_semestr_subject_id = $model->edu_semestr_subject_id;
//            $mark->subject_id = $model->subject_id;
//            $mark->edu_plan_id = $model->edu_plan_id;
//            $mark->edu_semestr_id = $model->edu_semestr_id;
//            $mark->faculty_id = $model->faculty_id;
//            $mark->direction_id = $model->direction_id;
//            $mark->semestr_id = $model->semestr_id;
//            $mark->course_id = $model->course_id;
//            $mark->exam_control_id = $model->examControl->id;
//            $mark->exam_control_student_id = $model->id;
//            $mark->user_status = 1;
//            if (!$mark->validate()) {
//                $errors[] = $mark->errors;
//                $transaction->rollBack();
//                return simplify_errors($errors);
//            }
//            $mark->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function studentMark($model) {
        $studentMark = StudentMark::find()
            ->where([
                'exam_type_id' => $model->exam_type_id,
                'student_id' => $model->student_id,
                'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                'subject_id' => $model->subject_id,
                'semestr_id' => $model->semestr_id,
                'course_id' => $model->course_id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->one();
        if ($studentMark != null) {
            $studentMark->is_deleted = 1;
            $studentMark->is_deleted_date = date("Y-m-d H:i:s");
            $studentMark->save(false);
        }
        $mark = new StudentMark();
        $mark->exam_type_id = $model->exam_type_id;
        $mark->type = $model->edu_semestr_exam_type_id;
        $mark->group_id = $model->group_id;
        $mark->student_id = $model->student_id;
        $mark->max_ball = $model->max_ball;
        $mark->ball = $model->student_ball;
        $mark->edu_semestr_subject_id = $model->edu_semestr_subject_id;
        $mark->subject_id = $model->subject_id;
        $mark->edu_plan_id = $model->edu_plan_id;
        $mark->edu_semestr_id = $model->edu_semestr_id;
        $mark->faculty_id = $model->faculty_id;
        $mark->direction_id = $model->direction_id;
        $mark->semestr_id = $model->semestr_id;
        $mark->course_id = $model->course_id;
        $mark->save(false);
    }

    public static function appealCheck($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $now = time();

        if (isset($post['appeal_conclution'])) {

            $model->appeal_conclution = $post['appeal_conclution'];
            if (isset($post['ball']))
                if ($model->ball < $post['ball']) {
                    $model->appeal_status = self::APPEAL_TYPE_ASOSLI;
                } else {
                    $model->appeal_status = $post['appeal_status'] ?? self::APPEAL_TYPE_ASOSSIZ;
                }
            if (!$model->old_ball > 0)
                $model->old_ball = $model->ball;

            $model->ball = $post['ball'] ?? $model->ball;
            $model->appeal = self::APPEAL_CHECKED;
        }

        if (isset($post['appeal2_conclution'])) {

            $model->appeal2_conclution = $post['appeal2_conclution'];
            if (isset($post['ball2']))
                if ($model->ball2 < $post['ball2']) {
                    $model->appeal2_status = self::APPEAL_TYPE_ASOSLI;
                } else {
                    $model->appeal2_status = $post['appeal2_status'] ?? self::APPEAL_TYPE_ASOSSIZ;
                }
            if (!$model->old_ball2 > 0)
                $model->old_ball2 = $model->ball2;

            $model->ball2 = $post['ball2'] ?? $model->ball2;
            $model->appeal2 = self::APPEAL_CHECKED;
        }

        $model->main_ball = $model->ball ?? 0 + $model->ball2 ?? 0;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0)
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function appealNew($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $now = time();


        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (count($errors) == 0)
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        $transaction->rollBack();
        return simplify_errors($errors);
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

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->upload_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->upload_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function uploadFile($question_file)
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(5) . '.' . $question_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $question_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
        return true;
    }

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),

        ];
    }
}
