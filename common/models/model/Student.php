<?php

namespace common\models\model;

use api\resources\Password;
use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Da\QrCode\QrCode;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int $user_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $course_id
 * @property int $edu_year_id
 * @property int $edu_type_id
 * @property int $social_category_id
 * @property int $residence_status_id
 * @property int $category_of_cohabitant_id
 * @property int $student_category_id
 * @property int $is_contract
 * @property string $diplom_number
 * @property string $diplom_seria
 * @property string $diplom_date
 * @property string $description
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property Direction $direction
 * @property EduType $eduType
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Users $user
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
//                    'group_id',
                    // 'faculty_id',
                    // 'direction_id',
                    // 'course_id',
                    // 'edu_year_id',
                    // 'edu_type_id',
                    // 'is_contract',
                    // 'edu_lang_id'
                ], 'required'
            ],
            [
                [
                    'edu_form_id',
                    'tutor_id',
                    'user_id',
                    'group_id',
                    'faculty_id',
                    'direction_id',
                    'course_id',
                    'edu_plan_id',
                    'edu_year_id',
                    'edu_type_id',
                    //
                    'social_category_id',
                    'residence_status_id',
                    'category_of_cohabitant_id',
                    'student_category_id',
                    //
                    'partners_count',
                    'edu_lang_id',
                    //

                    'is_contract',
                    'order',
                    'status',
                    'gender',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['diplom_date'], 'safe'],
            [
                [
                    'description',
                    'live_location',
                    'last_education'
                ], 'string'
            ],
            [['diplom_seria', 'diplom_number',], 'string', 'max' => 255],
            [
                [
                    'parent_phone',
                    'res_person_phone'
                ], 'string', 'max' => 55
            ],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['tutor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['tutor_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['edu_lang_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            //

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
            'group_id' => _e('Group ID'),
            'tutor_id' => _e('Tutor ID'),
            'faculty_id' => _e('Faculty ID'),
            'direction_id' => _e('Direction ID'),
            'course_id' => _e('Course ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'edu_form_id' => _e('Edu Form ID'),
            'edu_type_id' => _e('Edu Type ID'),
            'edu_lang_id' => _e('Edu Lang'),
            'edu_plan_id' => _e('Edu Plan Id'),
            //
            'social_category_id' => _e('Social Category Id'),
            'residence_status_id' => _e('Residence Status Id'),
            'category_of_cohabitant_id' => _e('Category Of Cohabitant Id'),
            'student_category_id' => _e('Student Category Id'),
            //

            'partners_count' => _e('partners_count'),
            'live_location' => _e('live_location'),
            'parent_phone' => _e('parent_phone'),
            'res_person_phone' => _e('res_person_phone'),
            'last_education' => _e('last_education'),
            //

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
            'group_id',
            'tutor_id',
            'faculty_id',
            'direction_id',
            'course_id',
            'edu_year_id',
            'edu_form_id',
            'edu_type_id',
            'edu_lang_id',
            'edu_plan_id',
            'gender',

            'type',
            //
            'social_category_id',
            'residence_status_id',
            'category_of_cohabitant_id',
            'student_category_id',
            //
            'partners_count',
            'live_location',
            'parent_phone',
            'res_person_phone',
            'last_education',
            //
            'is_contract',
            'diplom_number',
            'diplom_seria',
            'diplom_date',
            'description',

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
            'course',
            'direction',
            'eduType',
            'eduYear',
            'eduForm',
            'eduLang',
            'faculty',
            'user',
            'group',
            'studentGroup',
            'tutor',
            'profile',
            'eduPlan',
            'citizenship',
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

            // attent
            'studentAttends',
            'studentAttendReason',
            'studentAttendsCount',
            'studentAttendReasonCount',
            'attends',

            'studentSubjectRestrict',
            'studentSemestrSubject',

            'studentMark',
            'activeYear',
            'subjects',
            'activeSemestr',
            'qrAcademikReference',
            'teacherAttends',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getTeacherAttends()
    {
        $timeTableQuery = TimetableAttend::find()
            ->where([
                'is_deleted' => 0,
                'student_id' => $this->id,
                'edu_year_id' => Yii::$app->request->get('edu_year_id')
            ]);

        if (isRole('teacher')) {
            $timeTableQuery->andWhere(['created_by' => current_user_id()]);
        }

        $reasonQuery = clone $timeTableQuery;

        return [
            'all' => $timeTableQuery->count(),
            'reason' => $reasonQuery->andWhere(['reason' => 1])->count()
        ];
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

    public function getActiveSemestr() {
        return EduSemestr::findOne([
            'edu_plan_id' => $this->edu_plan_id,
            'edu_year_id' => Yii::$app->request->get('active_year_id') ? Yii::$app->request->get('active_year_id') : $this->activeYear->id,
            'is_deleted' => 0
        ]);
    }



    public function getQrAcademikReference() {
        $studentGroup = StudentGroup::find()
            ->where(['student_id' => $this->id , 'status' => 1, 'is_deleted' => 0])
            ->orderBy('semestr_id desc')->one();
        if ($studentGroup) {
            $url = 'https://eutas.uz/gpa/'.$studentGroup->semestr_key;
            return (new QrCode($url))
                ->setSize(150)
                ->setMargin(5)->writeDataUri();
        }
        return null;
    }

    public function getActiveYear() {
        return EduYear::findOne([
            'status' => 1,
            'is_deleted' => 0
        ]);
    }

    public function getSubjects() {
        return Subject::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere([
                'in' , 'id' , TimeTable1::find()
                    ->select('subject_id')
                    ->where([
                        'group_id' => $this->group_id ? $this->group_id : null,
                        'edu_semestr_id' => $this->activeSemestr ? $this->activeSemestr->id : null,
                        'status' => 1,
                        'is_deleted' => 0,
                    ])
            ])
            ->all();
    }

    public function getStudentAttends()
    {
        if (null !==  Yii::$app->request->get('subject_id') && null !==  Yii::$app->request->get('edu_semestr_id')) {
            return $this->hasMany(TimetableAttend::className(), ['student_id' => 'id'])
                ->onCondition([
                    'subject_id' => Yii::$app->request->get('subject_id'),
                    'edu_semestr_id' => Yii::$app->request->get('edu_semestr_id'),
                    'is_deleted' => 0,
                ])
                ->orderBy(['date' => SORT_ASC]);
        }
        return $this->hasMany(TimetableAttend::className(), ['student_id' => 'id'])
            ->onCondition([
                'edu_year_id' => Yii::$app->request->get('edu_year_id') ?? activeYearId(),
                'is_deleted' => 0,
            ])
            ->orderBy(['date' => SORT_ASC]);
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
            return $this->hasMany(TimetableAttend::className(), ['student_id' => 'id'])
                ->onCondition([
                    'subject_id' => Yii::$app->request->get('subject_id'),
                    'edu_year_id' => Yii::$app->request->get('active_year_id') ?? activeYearId(),
                    'reason' => 1
                ]);
        }
        return $this->hasMany(TimetableAttend::className(), ['student_id' => 'id'])->onCondition([
            'reason' => 1,
            'edu_year_id' => Yii::$app->request->get('active_year_id') ?? activeYearId(),
        ]);
    }

    public function getStudentAttendReasonCount()
    {
        return count($this->studentAttendReason);
    }

    public function getStudentSemestrSubject() {
        if (null !==  Yii::$app->request->get('semestr_id')) {
            return $this->hasMany(StudentSemestrSubject::className(), ['student_id' => 'id'])
                ->onCondition([
                    'is_deleted' => 0,
                    'semestr_id' => Yii::$app->request->get('semestr_id')
                ]);
        }
    }

    public function getAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['student_id' => 'id'])->onCondition(['archived' => 0]);
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
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
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

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getStudentGroup()
    {
        return $this->hasMany(StudentGroup::className(), ['student_id' => 'id'])->where(['is_deleted' => 0]);
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
        return Countries::findOne($this->profile->countries_id) ?? null;
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
        return Countries::findOne($this->profile->permanent_countries_id) ?? null;
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
    public static function eduPlan($student)
    {
        return EduSemestr::findOne([
            'edu_plan_id' => $student->group->edu_plan_id,
            'status' => 1,
            'is_deleted' => 0,
        ]);
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

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
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
