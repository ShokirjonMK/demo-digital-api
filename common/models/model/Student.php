<?php

namespace common\models\model;

use api\resources\Password;
use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%student}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $tutor_id tutor
 * @property int|null $faculty_id
 * @property int|null $direction_id
 * @property int|null $course_id
 * @property int|null $edu_year_id
 * @property int|null $edu_type_id
 * @property int|null $edu_form_id talim shakli id 
 * @property int|null $edu_lang_id
 * @property int|null $edu_plan_id
 * @property int|null $is_contract
 * @property string|null $diplom_number
 * @property string|null $diplom_seria
 * @property string|null $diplom_date
 * @property string|null $description
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int|null $gender 1-erkak 0-ayol
 * @property int|null $social_category_id  ijtimoiy toifa 
 * @property int|null $residence_status_id category_of_cohabitant id 
 * @property int|null $category_of_cohabitant_id category_of_cohabitant 
 * @property int|null $student_category_id student_category id 
 * @property int|null $partners_count partners_count  
 * @property string|null $live_location live_location  
 * @property string|null $parent_phone parent_phone  
 * @property string|null $res_person_phone res_person_phone  
 * @property string|null $last_education last_education  
 *
 * @property AttendReason[] $attendReasons
 * @property CategoryOfCohabitant $categoryOfCohabitant
 * @property ContractInfo[] $contractInfos
 * @property Course $course
 * @property Direction $direction
 * @property EduPlan $eduPlan
 * @property EduType $eduType
 * @property EduYear $eduYear
 * @property ExamControlStudentTegilmasin[] $examControlStudentTegilmasins
 * @property ExamControlStudent[] $examControlStudents
 * @property ExamStudent15[] $examStudent15s
 * @property ExamStudent2223[] $examStudent2223s
 * @property ExamStudentAnswer15[] $examStudentAnswer15s
 * @property ExamStudentAnswer2223[] $examStudentAnswer2223s
 * @property ExamStudentAnswer[] $examStudentAnswers
 * @property ExamStudentReaxam2223[] $examStudentReaxam2223s
 * @property ExamStudentReaxam[] $examStudentReaxams
 * @property ExamStudentReexam[] $examStudentReexams
 * @property ExamStudent[] $examStudents
 * @property ExamTeacherCheck[] $examTeacherChecks
 * @property Faculty $faculty
 * @property HostelApp[] $hostelApps
 * @property HostelDoc[] $hostelDocs
 * @property HostelStudentRoom[] $hostelStudentRooms
 * @property Military[] $militaries
 * @property OlympicCertificate[] $olympicCertificates
 * @property PollUser[] $pollUsers
 * @property QuestionStudentAnswer[] $questionStudentAnswers
 * @property ResidenceStatus $residenceStatus
 * @property SocialCategory $socialCategory
 * @property SportCertificate[] $sportCertificates
 * @property StudentAttendCopy1[] $studentAttendCopy1s
 * @property StudentAttend[] $studentAttends
 * @property StudentCategory $studentCategory
 * @property StudentClub[] $studentClubs
 * @property StudentMarkCopy1[] $studentMarkCopy1s
 * @property StudentMarkCopy2[] $studentMarkCopy2s
 * @property StudentMark[] $studentMarks
 * @property StudentOrder[] $studentOrders
 * @property StudentSubjectRestrict[] $studentSubjectRestricts
 * @property StudentTimeOption16[] $studentTimeOption16s
 * @property StudentTimeOption[] $studentTimeOptions
 * @property StudentTimeTable16[] $studentTimeTable16s
 * @property StudentTimeTable[] $studentTimeTables
 * @property User $tutor
 * @property User $user
 */
class Student extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const IS_CONTRACT_FALSE = 0;
    const IS_CONTRACT_TRUE = 1;
    const IS_CONTRACT_SUPER = 2;

    const STUDY_TYPE_DEFAULT = 1;
    const STUDY_TYPE_PEREVOD = 2;
    const STUDY_TYPE_EXCHANGE = 3;
    // const STUDY_TYPE_ = 4;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    // 'faculty_id',
                    // 'direction_id',
                    // 'course_id',
                    // 'edu_year_id',
                    // 'edu_type_id',
                    // 'is_contract',
                    // 'edu_lang_id'
                ],
                'required'
            ],
            [
                [
                    'edu_form_id',
                    'tutor_id',
                    'user_id',
                    'faculty_id',
                    'direction_id',
                    'course_id',
                    'edu_plan_id',
                    'diplom_number',
                    'edu_year_id',
                    'edu_type_id',
                    'social_category_id',
                    'residence_status_id',
                    'category_of_cohabitant_id',
                    'student_category_id',
                    'partners_count',
                    'edu_lang_id',

                    'study_type',

                    'is_contract',
                    'order',
                    'status',
                    'gender',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [['diplom_date'], 'safe'],
            [
                [
                    'description',
                    'live_location',
                    'last_education'
                ],
                'string'
            ],
            [['diplom_seria'], 'string', 'max' => 255],
            [
                [
                    'parent_phone',
                    'res_person_phone'
                ],
                'string',
                'max' => 55
            ],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['tutor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['tutor_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['edu_lang_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],

            [['social_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SocialCategory::className(), 'targetAttribute' => ['social_category_id' => 'id']],
            [['residence_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResidenceStatus::className(), 'targetAttribute' => ['residence_status_id' => 'id']],
            [['category_of_cohabitant_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryOfCohabitant::className(), 'targetAttribute' => ['category_of_cohabitant_id' => 'id']],
            [['student_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudentCategory::className(), 'targetAttribute' => ['student_category_id' => 'id']],
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
            'tutor_id' => _e('Tutor ID'),
            'faculty_id' => _e('Faculty ID'),
            'direction_id' => _e('Direction ID'),
            'course_id' => _e('Course ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'edu_form_id' => _e('Edu Form ID'),
            'edu_type_id' => _e('Edu Type ID'),
            'edu_lang_id' => _e('Edu Lang'),
            'edu_plan_id' => _e('Edu Plan Id'),
            'social_category_id' => _e('Social Category Id'),
            'residence_status_id' => _e('Residence Status Id'),
            'category_of_cohabitant_id' => _e('Category Of Cohabitant Id'),
            'student_category_id' => _e('Student Category Id'),

            'partners_count' => _e('partners_count'),
            'live_location' => _e('live_location'),
            'parent_phone' => _e('parent_phone'),
            'res_person_phone' => _e('res_person_phone'),
            'last_education' => _e('last_education'),

            'is_contract' => _e('Is Contract'),
            'diplom_number' => _e('Diplom Number'),
            'diplom_seria' => _e('Diplom Seria'),
            'diplom_date' => _e('Diplom Date'),
            'description' => _e('Description'),
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

            'user_id',
            'tutor_id',
            'faculty_id',
            'direction_id',
            'course_id',
            'edu_year_id',
            'edu_form_id',
            'edu_type_id',
            'edu_lang_id',
            'edu_plan_id',
            'social_category_id',
            'residence_status_id',
            'category_of_cohabitant_id',
            'student_category_id',
            'partners_count',
            'live_location',
            'parent_phone',
            'res_person_phone',
            'last_education',
            'is_contract',
            'diplom_number',
            'diplom_seria',
            'diplom_date',
            'description',
            'study_type',

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

            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',
            'nationality',

            'socialCategory',
            'residenceStatus',
            'categoryOfCohabitant',
            'studentCategory',

            'usernamePass',
            'username',
            'password',

            'mark',

            // attent
            'studentAttends',
            'studentAttendReason',
            'studentAttendsCount',
            'studentAttendReasonCount',


            /********* */

            'citizenship',
            'profile',
            'profileSelf',
            'profileMe',
            'eduLang',
            'attends',
            'studentSubjectRestrict',
            'eduForm',
            'studentMark',
            'attendReasons',
            'categoryOfCohabitant',
            'contractInfos',
            'course',
            'direction',
            'eduPlan',
            'eduType',
            'eduYear',
            'examControlStudents',
            'examStudentAnswers',
            'examStudentReaxams',
            'examStudentReexams',
            'examStudents',
            'examTeacherChecks',
            'faculty',
            'hostelApps',
            'hostelDocs',
            'hostelStudentRooms',
            'militaries',
            'olympicCertificates',
            'pollUsers',
            'questionStudentAnswers',
            'residenceStatus',
            'socialCategory',
            'sportCertificates',
            'studentAttendCopy1s',
            'studentAttends',
            'studentCategory',
            'studentClubs',
            'studentMarks',
            'studentOrders',
            'studentSubjectRestricts',
            'studentTimeOption16s',
            'studentTimeOptions',
            'studentTimeTable16s',
            'studentTimeTables',
            'tutor',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[mark]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMark()
    {
        return $this->hasMany(StudentMark::className(), ['student_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }
    public function getStudentMark()
    {
        return $this->hasMany(StudentMark::className(), ['student_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[AttendReasons]].
     *
     * @return \yii\db\ActiveQuery|AttendReasonQuery
     */
    public function getAttendReasons()
    {
        return $this->hasMany(AttendReason::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[ContractInfos]].
     *
     * @return \yii\db\ActiveQuery|ContractInfoQuery
     */
    public function getContractInfos()
    {
        return $this->hasMany(ContractInfo::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[ExamControlStudents]].
     *
     * @return \yii\db\ActiveQuery|ExamControlStudentQuery
     */
    public function getExamControlStudents()
    {
        return $this->hasMany(ExamControlStudent::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[ExamStudentAnswers]].
     *
     * @return \yii\db\ActiveQuery|ExamStudentAnswerQuery
     */
    public function getExamStudentAnswers()
    {
        return $this->hasMany(ExamStudentAnswer::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[ExamStudentReexams]].
     *
     * @return \yii\db\ActiveQuery|ExamStudentReexamQuery
     */
    public function getExamStudentReexams()
    {
        return $this->hasMany(ExamStudentReexam::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[ExamStudents]].
     *
     * @return \yii\db\ActiveQuery|ExamStudentQuery
     */
    public function getExamStudents()
    {
        return $this->hasMany(ExamStudent::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[ExamTeacherChecks]].
     *
     * @return \yii\db\ActiveQuery|ExamTeacherCheckQuery
     */
    public function getExamTeacherChecks()
    {
        return $this->hasMany(ExamTeacherCheck::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[HostelApps]].
     *
     * @return \yii\db\ActiveQuery|HostelAppQuery
     */
    public function getHostelApps()
    {
        return $this->hasMany(HostelApp::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[HostelDocs]].
     *
     * @return \yii\db\ActiveQuery|HostelDocQuery
     */
    public function getHostelDocs()
    {
        return $this->hasMany(HostelDoc::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[HostelStudentRooms]].
     *
     * @return \yii\db\ActiveQuery|HostelStudentRoomQuery
     */
    public function getHostelStudentRooms()
    {
        return $this->hasMany(HostelStudentRoom::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[Militaries]].
     *
     * @return \yii\db\ActiveQuery|MilitaryQuery
     */
    public function getMilitaries()
    {
        return $this->hasMany(Military::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[OlympicCertificates]].
     *
     * @return \yii\db\ActiveQuery|OlympicCertificateQuery
     */
    public function getOlympicCertificates()
    {
        return $this->hasMany(OlympicCertificate::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[PollUsers]].
     *
     * @return \yii\db\ActiveQuery|PollUserQuery
     */
    public function getPollUsers()
    {
        return $this->hasMany(PollUser::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[QuestionStudentAnswers]].
     *
     * @return \yii\db\ActiveQuery|QuestionStudentAnswerQuery
     */
    public function getQuestionStudentAnswers()
    {
        return $this->hasMany(QuestionStudentAnswer::className(), ['student_id' => 'id']);
    }


    /**
     * Gets query for [[SportCertificates]].
     *
     * @return \yii\db\ActiveQuery|SportCertificateQuery
     */
    public function getSportCertificates()
    {
        return $this->hasMany(SportCertificate::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[StudentClubs]].
     *
     * @return \yii\db\ActiveQuery|StudentClubQuery
     */
    public function getStudentClubs()
    {
        return $this->hasMany(StudentClub::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[StudentMarks]].
     *
     * @return \yii\db\ActiveQuery|StudentMarkQuery
     */
    public function getStudentMarks()
    {
        return $this->hasMany(StudentMark::className(), ['student_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[StudentOrders]].
     *
     * @return \yii\db\ActiveQuery|StudentOrderQuery
     */
    public function getStudentOrders()
    {
        return $this->hasMany(StudentOrder::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[StudentSubjectRestricts]].
     *
     * @return \yii\db\ActiveQuery|StudentSubjectRestrictQuery
     */
    public function getStudentSubjectRestricts()
    {
        return $this->hasMany(StudentSubjectRestrict::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[StudentTimeOptions]].
     *
     * @return \yii\db\ActiveQuery|StudentTimeOptionQuery
     */
    public function getStudentTimeOptions()
    {
        return $this->hasMany(StudentTimeOption::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[StudentTimeTables]].
     *
     * @return \yii\db\ActiveQuery|StudentTimeTableQuery
     */
    public function getStudentTimeTables()
    {
        return $this->hasMany(StudentTimeTable::className(), ['student_id' => 'id']);
    }


    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getContractInfo()
    {
        return $this->profile->contractInfo;
    }

    public function getStudentSubjectRestrict()
    {
        if (null !==  Yii::$app->request->get('subject_id')) {
            return $this->hasMany(
                StudentSubjectRestrict::className(),
                ['student_id' => 'id']
            )
                ->onCondition([
                    'subject_id' => Yii::$app->request->get('subject_id'),
                    'is_deleted' => 0
                ]);
        }

        if (null !==  Yii::$app->request->get('edu_semestr_subject_id')) {
            return $this->hasMany(
                StudentSubjectRestrict::className(),
                ['student_id' => 'id']
            )
                ->onCondition([
                    'edu_semestr_subject_id' => Yii::$app->request->get('edu_semestr_subject_id'),
                    'is_deleted' => 0
                ]);
        }

        return $this->hasMany(
            StudentSubjectRestrict::className(),
            [
                'student_id' => 'id',
            ]
        )
            ->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        if (null !==  Yii::$app->request->get('subject_id')) {
            return $this->hasMany(StudentAttend::className(), ['student_id' => 'id'])
                ->onCondition([
                    'subject_id' => Yii::$app->request->get('subject_id'),
                    'archived' => 0
                ])
                ->orderBy(['date' => SORT_ASC]);
        }


        return $this->hasMany(StudentAttend::className(), ['student_id' => 'id'])->onCondition(['archived' => 0])->orderBy(['date' => SORT_ASC]);
    }

    public function getStudentAttendsCount()
    {
        return count($this->studentAttends);
    }

    /**
     * Gets query for [[StudentAttendReason]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttendReason()
    {
        if (null !==  Yii::$app->request->get('subject_id')) {
            return $this->hasMany(StudentAttend::className(), ['student_id' => 'id'])
                ->onCondition([
                    'subject_id' => Yii::$app->request->get('subject_id'),
                    'reason' => 1,
                    'archived' => 0
                ]);
        }
        return $this->hasMany(StudentAttend::className(), ['student_id' => 'id'])->onCondition(['reason' => 1]);
    }
    public function getStudentAttendReasonCount()
    {
        return count($this->studentAttends);
    }

    public function getAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['student_id' => 'id'])->onCondition([
            'archived' => 0,
            'edu_plan_id' => $this->edu_plan_id,
        ]);
    }

    public function getUsernamePass()
    {
        $data = new Password();
        $data = $data->decryptThisUser($this->user_id);
        return $data;
    }

    public function getPassword()
    {
        return $this->usernamePass['password'];
    }

    public function getUsername()
    {
        return $this->user->username;
    }

    /**
     * Gets query for [[profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {

        // $query = $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
        $query = $this->hasOne(ProfileSelf::className(), ['user_id' => 'user_id']);
        //  if(!isRole('admin')){

        //     $query->select([
        //     'last_name',
        //     'image',
        //     'first_name',
        //     'middle_name',
        //     'phone',
        //     'phone_secondary',]);
        //  }
        return $query;
    }

    public function getProfileSelf()
    {
        return ProfileSelf::findOne(['user_id' => current_user_id()]);
    }
    public function getProfileMe()
    {
        return ProfileSelf::findOne(['user_id' => current_user_id()]);
    }


    /**
     * Gets query for [[socialCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialCategory()
    {
        return $this->hasOne(SocialCategory::className(), ['id' => 'social_category_id']);
    }

    public function getEduLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'edu_lang_id']);
    }

    /**
     * Gets query for [[residenceStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResidenceStatus()
    {
        return $this->hasOne(ResidenceStatus::className(), ['id' => 'residence_status_id']);
    }

    /**
     * Gets query for [[CategoryOfCohabitant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryOfCohabitant()
    {
        return $this->hasOne(CategoryOfCohabitant::className(), ['id' => 'category_of_cohabitant_id']);
    }

    /**
     * Gets query for [[studentCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentCategory()
    {
        return $this->hasOne(StudentCategory::className(), ['id' => 'student_category_id']);
    }

    // Profile Citizenship
    public function getCitizenship()
    {
        return Citizenship::findOne($this->profile->citizenship_id) ?? null;
    }

    // getCountry
    public function getCountry()
    {
        return Countries::findOne($this->profile->country_id) ?? null;
    }

    // getRegion
    public function getRegion()
    {
        return Region::findOne($this->profile->region_id) ?? null;
    }

    // getArea
    public function getArea()
    {
        return Area::findOne($this->profile->area_id) ?? null;
    }

    // getPermanentCountry
    public function getPermanentCountry()
    {
        return Countries::findOne($this->profile->permanent_country_id) ?? null;
    }

    // getPermanentRegion
    public function getPermanentRegion()
    {
        return Region::findOne($this->profile->permanent_region_id) ?? null;
    }

    // getPermanentArea
    public function getPermanentArea()
    {
        return Area::findOne($this->profile->permanent_area_id) ?? null;
    }

    // getNationality
    public function getNationality()
    {
        return Nationality::findOne($this->profile->nationality_id) ?? null;
    }

    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }
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
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[EduForm]].
     *edu_form_id
     * @return \yii\db\ActiveQuery
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
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

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Tutor]].
     * tutor_id
     * @return \yii\db\ActiveQuery
     */
    public function getTutor()
    {
        return $this->hasOne(User::className(), ['id' => 'tutor_id']);
    }

    // public static function find()
    // {
    //     return parent::find()->where(['is_deleted' => 0]);
    // }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }


    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function typesArray($key = null)
    {
        $array = [
            self::STUDY_TYPE_DEFAULT => 'STUDY_TYPE_DEFAULT',
            self::STUDY_TYPE_PEREVOD => 'STUDY_TYPE_PEREVOD',
            self::STUDY_TYPE_EXCHANGE => 'STUDY_TYPE_EXCHANGE',

        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }
}
