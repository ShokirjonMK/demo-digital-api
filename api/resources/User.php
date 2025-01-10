<?php

namespace api\resources;

use common\models\AuthAssignment;
use common\models\model\AcademicDegree;
use common\models\model\Area;
use common\models\model\Citizenship;
use common\models\model\Countries;
use common\models\model\Degree;
use common\models\model\DegreeInfo;
use common\models\model\DiplomaType;
use common\models\model\Group;
use common\models\model\Languages;
use common\models\model\LoadRate;
use common\models\model\Nationality;
use common\models\model\Partiya;
use common\models\model\Student;
use common\models\model\SubjectCategory;
use common\models\model\SubjectSemestr;
use common\models\model\TeacherAccess;
use common\models\model\PasswordEncrypts;
use common\models\model\TimetableDate;
use common\models\Subject;
use Yii;
//use api\resources\Profile;
use common\models\model\Profile;
use common\models\model\EncryptPass;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Keys;
use common\models\model\KpiMark;
use common\models\model\LoginHistory;
use common\models\model\Oferta;
use common\models\model\Region;
use common\models\model\UserAccess;
use common\models\model\UserAccessType;
use common\models\User as CommonUser;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

class User extends CommonUser
{
    use ResourceTrait;

    const FACULTY = 1;
    const KAFEDRA = 2;
    const DEPARTMENT = 3;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    const PASSWORD_CHANED = 1;
    const PASSWORD_NO_CHANED = 0;
     const UPLOADS_FOLDER_PASSPORT = 'uploads/user-passport/';
     const UPLOADS_FOLDER_ALL_FILE = 'uploads/user-all-file/';

    public $avatar;
    public $passport_file;
    public $all_file;

    public $excel;


    public $password;
    public $avatarMaxSize = 1024 * 1024 * 2; // 200 Kb
    public $passportFileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $allFileMaxSize = 1024 * 1024 * 5; // 5 Mb


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            // [['username', 'email', 'status', 'password_hash'], 'required'],
            [['username', 'status', 'password_hash'], 'required'],
            [['status'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            ['password','string', 'min'=>4, 'max'=>50],
            [['attach_role', 'position'],'string', 'max'=>255],
            [['password_reset_token'], 'unique'],

            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],
            [['passport_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png, jpg', 'maxSize' => $this->passportFileMaxSize],
            [['all_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png, jpg', 'maxSize' => $this->allFileMaxSize],

            [['excel'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx'],

            [['deleted'], 'default', 'value' => 0],
            [['template', 'layout', 'view'], 'default', 'value' => ''],
            [['is_changed', 'updated_by','last_seen_time' , 'change_password_type'], 'integer'],
            ['is_changed', 'in', 'range' => [self::PASSWORD_CHANED, self::PASSWORD_NO_CHANED]],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields = [
            'id',
            'username',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'middle_name' => function ($model) {
                return $model->profile->middle_name ?? '';
            },
            'role' => function ($model) {
                return $model->roles ?? '';
            },
            'avatar' => function ($model) {
                return $model->profile->image ?? '';
            },
            // 'passport_file' => function ($model) {
            //     return $model->profile->passport_file ?? '';
            // },
            'last_seen_time',
            'position',
            'email',
            'status',
            // 'deleted'

        ];

        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */

    public function extraFields()
    {
        $extraFields = [
            'profile',
            'userAccess',
            'teacherAccess',
            'allTeacherAccess',
            'isTeacherAccess',
            'department',
            'departmentName',
            'kafedraName',
            'FacultyName',
            'here',

            'academikDegree',
            'degree',
            'diplomaType',
            'degreeInfo',
            'partiya',
            'citizenship',
            'nationality',

            'userAccessKafedra',
            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',

            'roles',
            'rolesAll',

            'kpiBall',
            'kpiMark',


            'oferta',
            'ofertaIsComformed',
            'student',



            'role',
            'loginHistory',
            'lastIn',
            'tutorGroups',

            'decryptUser',
            'updatedBy',
            'createdBy',

            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }





    public function getDecryptUser() {
        if (isRole('admin') || isRole('edu_admin')) {
            $data = new Password();
            $data = $data->decryptThisUser($this->id);
            return $data['password'];
        } else {
            return "***** :( *****";
        }
    }

    public function getTutorGroups()
    {
        $student = Student::find()
            ->select('group_id')
            ->where(['is_deleted' => 0, 'tutor_id' => $this->id])
            ->groupBy('group_id');

        if (isRole('dean')) {
            $dean = get_dean();
            $student = $student->andWhere(['faculty_id' => $dean->id]);
        } elseif (isRole('dean_deputy')) {
            $dean = get_dean_deputy();
            $student = $student->andWhere(['faculty_id' => $dean->id]);
        }

        return Group::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['in' , 'id' , $student])
            ->all();
    }

    public function getPermissionsNoStudent()
    {
        if ($this->rolesAll) {
            $result = [];
            foreach ($this->rolesAll as $roleOne) {
                if ($roleOne->item_name != 'student') {
                    $authItem = AuthItem::find()->where(['name' => $roleOne->item_name])->one();
                    $perms = $authItem->permissions;
                    if ($perms && is_array($perms)) {
                        foreach ($perms as $row) {
                            $result[] = $row['name'];
                        }
                    }
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    public function getPermissionsStudent()
    {
        if ($this->rolesAll) {
            $result = [];
            foreach ($this->rolesAll as $roleOne) {
                if ($roleOne->item_name == 'student') {
                    $authItem = AuthItem::find()->where(['name' => $roleOne->item_name])->one();
                    $perms = $authItem->permissions;
                    if ($perms && is_array($perms)) {
                        foreach ($perms as $row) {
                            $result[] = $row['name'];
                        }
                    }
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    public function getPermissionsAll()
    {
        if ($this->rolesAll) {
            $result = [];
            foreach ($this->rolesAll as $roleOne) {
                $authItem = AuthItem::find()->where(['name' => $roleOne->item_name])->one();
                $perms = $authItem->permissions;
                if ($perms && is_array($perms)) {
                    foreach ($perms as $row) {
                        $result[] = $row['name'];
                    }
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    public function getPermissions()
    {
        if ($this->roleItem) {
            $authItem = AuthItem::find()->where(['name' => $this->roleItem])->one();
            $perms = $authItem->permissions;
            $result = [];
            if ($perms && is_array($perms)) {
                foreach ($perms as $row) {
                    $result[] = $row['name'];
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    public function getRoles()
    {
        if ($this->roleItem) {
            $authItems = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            $result = [];
            foreach ($authItems as $authItem) {
                $result[] = $authItem['item_name'];
            }
            return $result;
        } else {
            return [];
        }
    }

    public static function attachRole($role , $user_id = null) {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }
        if (is_null($role)) {
            $role = null;
        }
        $user = User::findOne($user_id);
        $user->attach_role = $role;
        $user->save(false);
    }

    public function getLoginRolesNoStudent($role = null) {
        if (is_null($role)) {
            $getRole = '';
        } else {
            $getRole = $role;
        }
//            if (isset($role)) {
//                $getRole = $role;
//            } else {
//                $getRole = '';
//            }

//            dd($authItems);

        if (isset($role)) {
            $authItems = AuthAssignment::find()->where([
                'user_id' => $this->id
            ])->andFilterWhere(['item_name' => $getRole])->one();
            if (!isset($authItems)) {
                $authItems = AuthAssignment::find()->where([
                    'user_id' => $this->id
                ])->one();
            }
        } else {
            $authItems = AuthAssignment::find()->where([
                'user_id' => $this->id
            ])->one();
        }

        $result = [];
        if ($authItems['item_name'] != 'student') {
            $result['active_role'] = $authItems['item_name'];
            $authItemOne = AuthItem::find()->where(['name' => $result['active_role']])->one();
            $perms = $authItemOne->permissions;
            if ($perms && is_array($perms)) {
                foreach ($perms as $row) {
                    $result['permission'][] = $row['name'];
                }
            } else {
                $result['permission'] = [];
            }
        }

        $allRoles = AuthAssignment::find()->where(['user_id' => $this->id])->all();
        foreach ($allRoles as $allRole) {
            if ($allRole['item_name'] != 'student') {
                $result['all_roles'][] = $allRole['item_name'];
            }
        }

        return $result;
    }

    public function getRolesNoStudent($role) {
        if (isset($role)) {
            $getRole = $role;
        } else {
            $getRole = '';
        }

        if (isset($role)) {
            $authItems = AuthAssignment::find()->where([
                'user_id' => $this->id
            ])->andFilterWhere(['item_name' => $getRole])->one();
            if (!isset($authItems)) {
                $authItems = AuthAssignment::find()->where([
                    'user_id' => $this->id
                ])->one();
            }
        } else {
            $authItems = AuthAssignment::find()->where([
                'user_id' => $this->id
            ])->one();
        }

        $result = [];
        if ($authItems['item_name'] != 'student') {
            $result['active_role'] = $authItems['item_name'];
            $authItemOne = AuthItem::find()->where(['name' => $result['active_role']])->one();
            $perms = $authItemOne->permissions;
            if ($perms && is_array($perms)) {
                foreach ($perms as $row) {
                    $result['permission'][] = $row['name'];
                }
            } else {
                $result['permission'] = [];
            }
        }

        $allRoles = AuthAssignment::find()->where(['user_id' => $this->id])->all();
        foreach ($allRoles as $allRole) {
            if ($allRole['item_name'] != 'student') {
                $result['all_roles'][] = $allRole['item_name'];
            }
        }

        return $result;
    }

    public function getRolesNoStudent2($role)
    {
        if ($this->roleItem) {

            if (isset($role)) {
                $getRole = $role;
            } else {
                $getRole = '';
            }

            if (isset($role)) {
                $authItems = AuthAssignment::find()->where([
                    'user_id' => $this->id
                ])->andFilterWhere(['item_name' => $getRole])->one();
                if (!isset($authItems)) {
                    $authItems = AuthAssignment::find()->where([
                        'user_id' => $this->id
                    ])->one();
                }
            } else {
                $authItems = AuthAssignment::find()->where([
                    'user_id' => $this->id
                ])->one();
            }

            $result = [];
            if ($authItems['item_name'] != 'student') {
                $result['active_role'] = $authItems['item_name'];
                $authItemOne = AuthItem::find()->where(['name' => $result['active_role']])->one();
                $perms = $authItemOne->permissions;
                if ($perms && is_array($perms)) {
                    foreach ($perms as $row) {
                        $result['permission'][] = $row['name'];
                    }
                } else {
                    $result['permission'] = [];
                }
            }

            $allRoles = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            foreach ($allRoles as $allRole) {
                if ($allRole['item_name'] != 'student') {
                    $result['all_roles'][] = $allRole['item_name'];
                }
            }

            return $result;
        } else {
            return [];
        }
    }

    public function getIsRoleStudent() {
        if ($this->roleItem) {
            $authItems = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            $result = [];
            foreach ($authItems as $authItem) {
                if ($authItem['item_name'] == 'student') {
                    return true;
                }
            }
            return false;
        }
    }

    public function getRolesStudent()
    {
        if ($this->roleItem) {
            $authItems = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            $result = [];
            foreach ($authItems as $authItem) {
                if ($authItem['item_name'] == 'student') {
                    $result[] = $authItem['item_name'];
                }
            }
            return $result;
        } else {
            return [];
        }
    }

    // public function getKpiBall()
    // {
    //     return $this->hasMany(KpiMark::className(), ['user_id' => 'id'])->andWhere(['archived' => 0, 'is_deleted' => 0])->sum('ball');
    // }


    // public function getKpiBall()
    // {
    //     return $this->hasMany(KpiMark::className(), ['user_id' => 'id'])->sum('ball');
    // }

    public function getOfertaIsComformed()
    {
        return $this->oferta ? 1 : 0;
    }

    public function getOferta()
    {
        return 1;
        //    return $this->hasOne(Oferta::className(), ['created_by' => 'id']);
    }

    public function getAcademikDegree()
    {
        return AcademicDegree::findOne($this->profile->academic_degree_id) ?? null;
    }

    public function getDegree()
    {
        return Degree::findOne($this->profile->degree_id) ?? null;
    }
    public function getDegreeInfo()
    {
        return DegreeInfo::findOne($this->profile->degree_id) ?? null;
    }

    public function getDiplomaType()
    {
        return DiplomaType::findOne($this->profile->diploma_type_id) ?? null;
    }

    public function getPartiya()
    {
        return Partiya::findOne($this->profile->partiya_id) ?? null;
    }

    public function getCitizenship()
    {
        return Citizenship::findOne($this->profile->citizenship_id) ?? null;
    }
    public function getNationality()
    {
        return Nationality::findOne($this->profile->nationality_id) ?? null;
    }

    // public function getKpiMark()
    // {
    //     return $this->hasMany(KpiMark::className(), ['user_id' => 'id'])->onCondition(['archived' => 0, 'is_deleted' => 0]);
    // }

    // public function getKafedra()
    // {
    //    return getUserAccess
    //    return $this->hasOne(Kafedra::className(), ['user_id' => 'id']);
    // }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['user_id' => 'id']);
    }

    // getCountry
    public function getCountry()
    {
        return Countries::findOne($this->profile->countries_id) ?? null;
    }


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

    // UserAccess
    public function getUserAccess()
    {
        return $this->hasMany(UserAccess::className(), ['user_id' => 'id'])->onCondition(['status' => 1, 'is_deleted' => 0]);
    }

    public function getTimetable()
    {

    }

    public function getUserAccessKafedra()
    {
        $userAccess = UserAccess::find()
            ->select('table_id')
            ->where([
                'user_id' => $this->id,
                'user_access_type_id' => self::KAFEDRA,
                'status' => 1,
                'is_deleted' => 0,
            ]);
        $query = Kafedra::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0
            ]);
        $query->andFilterWhere(['in' , 'id' , $userAccess]);
        return $query->all();
        return $this->hasMany(UserAccess::className(), ['user_id' => 'id'])->onCondition(['status' => 1, 'is_deleted' => 0]);
    }

    public function getTeacherAccess()
    {
        return $this->hasMany(TeacherAccess ::className(), ['user_id' => 'id'])->onCondition(['status' => 1, 'is_deleted' => 0]);
    }

    public function getAllTeacherAccess()
    {
        return $this->hasMany(TeacherAccess ::className(), ['user_id' => 'id']);
    }

    public function getIsTeacherAccess()
    {
        $data = TeacherAccess::find()
            ->where([
                'user_id' => $this->id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();
//        $data = $this->hasOne(TeacherAccess ::className(), ['id' => 'user_id'])->onCondition(['status' => 1, 'is_deleted' => 0]);
        if ($data != null) {
            return 1;
        }
        return 0;
    }

    // getLoginHistory
    public function getLoginHistory()
    {
        return $this->hasMany(LoginHistory::className(), ['user_id' => 'id']);
    }

    // getLoginHistory
    public function getLastIn()
    {
        return $this->hasOne(LoginHistory::className(), ['user_id' => 'id'])->onCondition(['log_in_out' => LoginHistory::LOGIN])->orderBy(['id' => SORT_DESC]);
    }

    // UserAccess
    public function getDepartmentName()
    {
        $data = [];

        // return $this->userAccess;
        foreach ($this->userAccess as $userAccessOne) {
            $user_access_type = $this->userAccess ? UserAccessType::findOne($userAccessOne->user_access_type_id) : null;
            if ($user_access_type) {
                $sssasaaa = $user_access_type->table_name::findOne(['id' => $userAccessOne->table_id]);

                $data[$userAccessOne->user_access_type_id][] = $sssasaaa->translate->name;
            }
        }

        return $data;
        // return $this->userAccess->user_access_type_id;
        $user_access_type = $this->userAccess ? UserAccessType::findOne($this->userAccess[0]->user_access_type_id) : null;

        return $user_access_type ? $user_access_type->table_name::findOne(['id' => $this->userAccess[0]->table_id]) : [];
    }
    // KafedraName
    public function getKafedraName()
    {
        $userAccess = UserAccess::find()->where(['user_id' => $this->id, 'user_access_type_id' => 2])->with('kafedra')->one();

        return $userAccess->kafedra->translate->name ?? null;
    }
    // FacultyName
    public function getFacultyName()
    {
        $userAccess = UserAccess::find()->where(['user_id' => $this->id, 'user_access_type_id' => 1])->with('faculty')->one();

        return $userAccess->faculty->translate->name ?? null;
    }
    // Kaferda
    public function getKafedra()
    {
        return $this->hasOne(UserAccess::className(), ['user_id' => 'id'])->onCondition(['user_access_type_id' => 2]);
    }
    // Faculty
    public function getFaculty()
    {
        return $this->hasOne(UserAccess::className(), ['user_id' => 'id'])->onCondition(['user_access_type_id' => 2]);
    }

    // UserAccess
    public function getDepartment()
    {
        $data = [];

        // return $this->userAccess;
        foreach ($this->userAccess as $userAccessOne) {
            $user_access_type = $this->userAccess ? UserAccessType::findOne($userAccessOne->user_access_type_id) : null;
            $data[$userAccessOne->user_access_type_id][] =
                $user_access_type ? $user_access_type->table_name::findOne(['id' => $userAccessOne->table_id]) : [];
        }
        return $data;
        // return $this->userAccess->user_access_type_id;
        $user_access_type = $this->userAccess ? UserAccessType::findOne($this->userAccess[0]->user_access_type_id) : null;

        return $user_access_type ? $user_access_type->table_name::findOne(['id' => $this->userAccess[0]->table_id]) : [];
    }

    // Dep Kaf Fac
    public function getHere()
    {
        // return $this->userAccess->user_access_type_id;
        $data = [];

        foreach ($this->userAccess as $userAccessOne) {
            $user_access_type = $this->userAccess ? UserAccessType::findOne($userAccessOne->user_access_type_id) : null;
            $data[] =
                $user_access_type ? $user_access_type->table_name::findOne(['id' => $userAccessOne->table_id]) : [];
        }

        return $data;
        $user_access_type = $this->userAccess ? UserAccessType::findOne($this->userAccess[0]->user_access_type_id) : null;

        return $user_access_type ? $user_access_type->table_name::findOne(['id' => $this->userAccess[0]->table_id]) : [];
    }

    public static function createItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        $roles = $post['role'];
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!(isset($role) && !empty($role) && is_string($role))) {
                    $errors[] = ['role' => [_e('Role is not valid.')]];
                }
            }
        } else {
            if (!(isset($roles) && !empty($roles) && is_string($roles))) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }
        }

        if (count($errors) == 0) {

            if (isset($post['password']) && !empty($post['password'])) {
                if ($post['password'] != 'undefined' && $post['password'] != 'null' && $post['password'] != '') {
                    $password = $post['password'];
                } else {
                    $password = _passwordMK();
                }
            } else {
                $password = _passwordMK();
            }
            if (isset($post['email']) && $post['email'] == "") {
                $model->email = null;
            }
            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();

            if ($model->save()) {

                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */

                if (isRole('dean')) {
                    $faculty = Faculty::findOne([
                        'user_id' => current_user_id()
                    ]);
                    if (isset($faculty)) {
                        $userAccess = new UserAccess();
                        $userAccess->user_id = $model->id;
                        $userAccess->table_id = $faculty->id;
                        $userAccess->user_access_type_id = UserAccess::FACULTY;
                        $userAccess->is_leader = UserAccess::IS_LEADER_FALSE;
                        if (!$userAccess->save()) {
                            $errors[] = ['user_access' => [_e('Error saving data.')]];
                        } else {
                            $loadRate = new LoadRate();
                            $loadRate->work_load_id = 1;
                            $loadRate->work_rate_id = 4;
                            $loadRate->user_access_id = $userAccess->id;
                            $loadRate->user_id = $userAccess->user_id;
                            if (!$loadRate->save()) {
                                $errors[] = ['load_rate' => [_e('Error saving data.')]];
                            }
                        }
                    }
                }

                if (isRole('mudir')) {
                    $kafedra = Kafedra::findOne([
                        'user_id' => current_user_id()
                    ]);
                    if (isset($kafedra)) {
                        $userAccess = new UserAccess();
                        $userAccess->user_id = $model->id;
                        $userAccess->table_id = $kafedra->id;
                        $userAccess->user_access_type_id = UserAccess::KAFEDRA;
                        $userAccess->is_leader = UserAccess::IS_LEADER_FALSE;
                        if (!$userAccess->save()) {
                            $errors[] = ['user_access' => [_e('Error saving data.')]];
                        } else {
                            $loadRate = new LoadRate();
                            $loadRate->work_load_id = 1;
                            $loadRate->work_rate_id = 4;
                            $loadRate->user_access_id = $userAccess->id;
                            $loadRate->user_id = $userAccess->user_id;
                            if (!$loadRate->save()) {
                                $errors[] = ['load_rate' => [_e('Error saving data.')]];
                            }
                        }
                    }
                }

                $profile->user_id = $model->id;

                // file saqlash boshqa joyga olindi

                if (!$profile->save(false)) {
                    $errors[] = $profile->errors;
                } else {

                    // avatarni saqlaymiz
                    $model->avatar = UploadedFile::getInstancesByName('avatar');
                    if ($model->avatar) {
                        if ($model->avatar[0]->size <= $profile->avatarMaxSize) {
                            $model->avatar = $model->avatar[0];
                            $avatarUrl = $model->upload();
                            if ($avatarUrl) {
                                $profile->image = $avatarUrl;
                            } else {
                                $errors[] = _e("An error occurred while inserting the image.");
                            }
                        } else {
                            $errors[] = _e("The avatar size must not exceed the given size.");
                        }
                    }
                    // ***

                    // passport file saqlaymiz
                    $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                    if ($model->passport_file) {
                        if ($model->passport_file[0]->size <= $profile->passportFileMaxSize) {
                            $model->passport_file = $model->passport_file[0];
                            $passportUrl = $model->uploadPassport();
                            if ($passportUrl) {
                                $profile->passport_file = $passportUrl;
                            } else {
                                $errors[] = _e("An error occurred while trying to insert a file");
                            }
                        } else {
                            $errors[] = _e("The passport file size must not exceed the given size.");
                        }

                    }
                    // ***

                    // All file saqlaymiz
                    $model->all_file = UploadedFile::getInstancesByName('all_file');
                    if ($model->all_file) {
                        $json = [];
                        foreach ($model->all_file as $file_key => $files) {
                            $model->all_file = $files[$file_key];
                            if ($model->all_file->size <= $profile->allFileMaxSize) {
                                $res[] = $model->all_file;
                                $AllFileUrl = $model->uploadAllFile();
                                if ($AllFileUrl) {
                                    $json[] = $AllFileUrl;
                                } else {
                                    $errors[] = _e("An error occurred while trying to insert a file");
                                }
                            } else {
                                $errors[] = _e("The file size must not exceed the given size.");
                            }
                        }
                        if (count($json)>0) {
                            $profile->all_file = json_encode($json,true);
                        }
                    }
                    // ***
                    $profile->save(false);

                    // role ni userga assign qilish
                    $auth = Yii::$app->authManager;

                    $roles = json_decode(str_replace("'", "", $post['role']), true);

                    if (is_array($roles)) {
                        foreach ($roles as $role) {
                            $authorRole = $auth->getRole($role);
                            if ($authorRole) {
                                $auth->assign($authorRole, $model->id);
                                if ($role == 'teacher' && isset($post['teacherAccess'])) {
                                    $teacherAccess = json_decode(str_replace("'", "", $post['teacherAccess']));
                                    foreach ($teacherAccess as $subjectIds => $subjectIdsValues) {
                                        if (is_array($subjectIdsValues)) {
                                            foreach ($subjectIdsValues as $langId) {
                                                $teacherAccessNew = new TeacherAccess();
                                                $teacherAccessNew->user_id = $model->id;
                                                $teacherAccessNew->subject_id = (int)$subjectIds;
                                                $teacherAccessNew->language_id = (int)$langId;
                                                $teacherAccessNew->save();
                                            }
                                        }
                                    }
                                }
                            } else {
                                $errors[] = ['role' => [_e('Role not found.')]];
                            }
                        }
                    } else {
                        $errors[] = ['role' => [_e('Role is invalid')]];
                    }
                }
            } else {
                $errors[] = $model->errors;
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

    public static function selfUpdateItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        if (count($errors) == 0) {

            if ($model->save(false)) {
                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                // passport file saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                if (!$profile->save(false)) {
                    $errors[] = $profile->errors;
                }
            } else {
                $errors[] = $model->errors;
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

    public static function updateItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if (isset($post['role'])) {
            $roles = $post['role'];
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    if (!(isset($role) && !empty($role) && is_string($role))) {
                        $errors[] = ['role' => [_e('Role is not valid.')]];
                    }
                }
            } else {
                if (!(isset($roles) && !empty($roles) && is_string($roles))) {
                    $errors[] = ['role' => [_e('Role is not valid.')]];
                }
            }
        }

        if (count($errors) == 0) {
            /* * Password */
            if (isset($post['password']) && !empty($post['password'])) {
                if ($post['password'] != 'undefined' && $post['password'] != 'null' && $post['password'] != '') {
                    if (strlen($post['password']) < 6) {
                        $errors[] = [_e('Password is too short')];
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    $password = $post['password'];
                    //**  */parolni shifrlab saqlaymiz */
                    $model->savePassword($password, $model->id);
                    //**** */
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                }
            }

            if ($model->save(false)) {

                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                // passport file saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }

//                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
//                if ($model->passport_file) {
//                    $model->passport_file = $model->passport_file[0];
//                    $passportUrl = $model->uploadPassport();
//                    if ($passportUrl['is_ok']) {
//                        $profile->passport_file = $passportUrl['data'];
//                    } else {
//                        $errors[] = $model->errors;
//                    }
//                }
                // ***

                // All file saqlaymiz
//                $model->all_file = UploadedFile::getInstancesByName('all_file');
//                if ($model->all_file) {
//                    $json = [];
//                    dd($model->all_file);
//                    foreach ($model->all_file as $file_key => $files) {
//                        $model->all_file = $files[$file_key];
//                        $AllFileUrl = $model->uploadAllFile();
//                        if ($AllFileUrl) {
//                            $json[] = $AllFileUrl;
//                        } else {
//                            $errors[] = $model->errors;
//                        }
//                    }
//                    if (count($json)>0) {
//                        $profile->all_file = json_encode($json,true);
//                    }
//                }
//                if (count($json)>0) {
//                    dd($json);
//                }
                // ***



                if (!$profile->save(false)) {
                    $errors[] = $profile->errors;
                } else {
                    if (isset($post['role']) && $model->id != current_user_id()) {
                        $auth = Yii::$app->authManager;
                        $roles = json_decode(str_replace("'", "", $post['role']));

                        if (is_array($roles)) {
                            $auth->revokeAll($model->id);
                            foreach ($roles as $role) {
                                $authorRole = $auth->getRole($role);
                                if ($authorRole) {
                                    $auth->assign($authorRole, $model->id);
                                    if ($role == 'teacher' && isset($post['teacherAccess'])) {
                                        $teacherAccess = json_decode(str_replace("'", "", $post['teacherAccess']));
                                        foreach (TeacherAccess::findAll(['user_id' => $model->id]) as $teacherAccessOne) {
                                            $teacherAccessOne->is_deleted = 1;
                                            $teacherAccessOne->status = 0;
                                            $teacherAccessOne->save(false);
                                        }
                                        foreach ($teacherAccess as $subjectSemestrId => $subjectIdsValues) {
                                            $subjectSemestr = SubjectSemestr::findOne($subjectSemestrId);
                                            if ($subjectSemestr) {
                                                if (is_array($subjectIdsValues)) {
                                                    foreach ($subjectIdsValues as $langId) {
                                                        $teacherAccessHas = TeacherAccess::findOne([
                                                            'user_id' => $model->id,
                                                            'subject_semestr_id' => $subjectSemestrId,
                                                            'subject_id' => $subjectSemestr->subject_id,
                                                            'language_id' => $langId,
                                                        ]);
                                                        if ($teacherAccessHas) {
                                                            $teacherAccessHas->is_deleted = 0;
                                                            $teacherAccessHas->status = 1;
                                                            $teacherAccessHas->save(false);
                                                        } else {
                                                            $teacherAccessNew = new TeacherAccess();
                                                            $teacherAccessNew->user_id = $model->id;
                                                            $teacherAccessNew->subject_semestr_id = $subjectSemestrId;
                                                            $teacherAccessNew->subject_id = $subjectSemestr->subject_id;
                                                            $teacherAccessNew->language_id = $langId;
                                                            $teacherAccessNew->save(false);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    //                                }
                                } else {
                                    $errors[] = ['role' => [_e('Role not found.')]];
                                }
                            }
                        } else {
                            $errors[] = ['role' => [_e('Role is invalid')]];
                        }
                    }
                }
            } else {
                $errors[] = $model->errors;
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

    public static function allFileSave($model, $data, $profile)
    {
        $json = [];

        $model->all_file = $data[0];
        $allFileUrl = $model->uploadAllFile();
        if ($allFileUrl) {
            if (isset($profile->all_file))
            {
                foreach (json_decode($profile->all_file) as $value) {
                    array_push($json, $value);
                }
            }
            array_push($json , [$allFileUrl]);
            return $json;
        }
        return false;
    }

    public static function deleteAllFile($id , $url) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (!$model) {
            $errors[] = [_e('Data not found.')];
        }
        if (count($errors) == 0) {
            $profileAllFileDeleted = Profile::findOne(['user_id' => $id]);
            if (isset($profileAllFileDeleted->all_file)) {
                $allFile = json_decode($profileAllFileDeleted->all_file);
                foreach ($allFile as $key => $value) {
                    if ($url == $value[0]->url) {
                        if (file_exists($url)){
                            array_splice($allFile , $key ,1);
                            $profileAllFileDeleted->all_file = json_encode($allFile);
                            if ($profileAllFileDeleted->save()) {
                                unlink($url);
                            } else {
                                $errors[] = [_e('Error')];
                            }
                        }
                    }
                }
            } else {
                $errors[] = [_e('File not found.')];
            }

            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }
    public static function deleteItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (!$model) {
            $errors[] = [_e('Data not found.')];
        }
        if (count($errors) == 0) {

            // remove profile image
//            $filePath = assets_url($model->profile->image);
//            if(file_exists($filePath)){
//                unlink($filePath);
//            }
            // remove profile
            $profileDeleted = Profile::findOne(['user_id' => $id]);
            $profileDeleted->is_deleted = 1;

            if (!$profileDeleted->save()) {
                $errors[] = [_e('Error in profile deleting process.')];
            }
            $userAccess = UserAccess::findAll(['user_id' => $model->id]);
            if (count($userAccess) > 0) {
                foreach ($userAccess as $userAccessOne) {
                    $userAccessOne->is_deleted = 1;
                    $userAccessOne->save(false);
                }
            }
            $teacherAccess = TeacherAccess::findAll(['user_id' => $model->id]);
            if (count($teacherAccess) > 0) {
                foreach ($teacherAccess as $teacherAccessOne) {
                    $teacherAccessOne->is_deleted = 1;
                    $teacherAccessOne->save(false);
                }
            }
            $model->deleted = 1;
            $model->status = self::STATUS_BANNED;
            if (!$model->save()) {
                $errors[] = [_e('Error in user deleting process.')];
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

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => _e('Active'),
            self::STATUS_BANNED => _e('Banned'),
            self::STATUS_PENDING => _e('Pending'),
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->avatar->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->avatar->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }


    public function uploadPassport()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_PASSPORT))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_PASSPORT), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->passport_file->extension;
            $miniUrl = self::UPLOADS_FOLDER_PASSPORT . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_PASSPORT. $fileName);
            $this->passport_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            dd($this);
            return false;
        }
    }

    public function uploadAllFile()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_ALL_FILE))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_ALL_FILE), 0777, true);
            }

            $data = [];
            $fileName = $this->id . time(). '_'. \Yii::$app->security->generateRandomString(10) .'.' . $this->all_file->extension;

            $miniUrl =  str_replace("'", "", 'storage/' .self::UPLOADS_FOLDER_ALL_FILE . $fileName);
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER_ALL_FILE. $fileName);
            $this->all_file->saveAs($url, false);
            $data = [
                'url' =>  $miniUrl,
                'name' => $this->all_file->name,
                'size' => $this->all_file->size
            ];
            return $data;
        } else {
            return false;
        }
    }


    //**parolni shifrlab saqlash */

    public function savePassword($password, $user_id)
    {
        // if exist delete and create new one 
        $oldPassword = PasswordEncrypts::find()->where(['user_id' => $user_id])->all();
        if (isset($oldPassword)) {
            foreach ($oldPassword as $pass) {
                $pass->delete();
            }
        }

        $uu = new EncryptPass();
        $max = Keys::find()->count();
        $rand = rand(1, $max);
        $key = Keys::findOne($rand);
        $enc = $uu->encrypt($password, $key->name);
        $save_password = new PasswordEncrypts();
        $save_password->user_id = $user_id;
        $save_password->password = $enc;
        $save_password->key_id = $key->id;
        if ($save_password->save(false)) {
            return true;
        } else {
            return false;
        }
    }
}
