<?php

namespace api\resources;

use api\components\TurniketMK;
use common\models\AuthAssignment;
use common\models\model\Area;
use common\models\model\Attend;
use common\models\model\Citizenship;
use common\models\model\Countries;
use common\models\model\TeacherAccess;
use common\models\model\PasswordEncrypts;
use Yii;
//use api\resources\Profile;
use common\models\model\Profile;
use common\models\model\EncryptPass;
use common\models\model\ExamStudent;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Keys;
use common\models\model\KpiMark;
use common\models\model\LoginHistory;
use common\models\model\Nationality;
use common\models\model\Oferta;
use common\models\model\Region;
use common\models\model\TimeTable;
use common\models\model\Turniket;
use common\models\model\UserAccess;
use common\models\model\UserAccessType;
use common\models\User as CommonUser;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

class User extends CommonUser
{
    use ResourceTrait;

    const UPLOADS_FOLDER = 'user-images/';
    const PASSWORD_CHANED = 1;
    const PASSWORD_NO_CHANED = 0;
    // const UPLOADS_FOLDER_PASSPORT = 'uploads/user-passport/';

    public $avatar;
    public $passport_file;
    public $avatarMaxSize = 1024 * 200; // 200 Kb
    public $passportFileMaxSize = 1024 * 1024 * 5; // 5 Mb

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
            [['username', 'email', 'status', 'password_hash'], 'required'],
            [['status'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['password_reset_token'], 'unique'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],
            [['passport_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png, jpg', 'maxSize' => $this->passportFileMaxSize],
            [['deleted'], 'default', 'value' => 0],
            [['template', 'layout', 'view'], 'default', 'value' => ''],
            [['is_changed'], 'integer'],
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
            'created_at',
            'updated_at',
            'profile',
            'userAccess',
            'department',
            'departmentName',
            'kafedraName',
            'facultyName',
            'facultyNameViaKafedra',
            'here',

            'roles',
            'rolesAll',

            'kpiBall',
            'kpiBall1',
            'kpiMark',

            'turniket',
            'turniketDate',
            'goInTime',
            'oferta',
            'ofertaAll',
            'ofertaIsComformed',

            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',
            'nationality',
            'citizenship',

            'attendedCount',
            'timeTables',
            'timeTablesCount',


            'loginHistory',
            'lastIn',
            'password',
            'usernamePass',

            'updatedBy',
            'createdBy',

            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getUsernamePass()
    {
        $data = new Password();
        $data = $data->decryptThisUser($this->id);
        return $data;
    }

    public function getNotCheckedCountc()
    {

        // Initialize the model and get the table name
        $model = new ExamStudent();
        $tableName = $model->tableName();

        // Construct the query
        $query = $model->find()
            ->select([
                'SUM(CASE WHEN exam_student.is_checked_full = 0 AND exam_student.act <> 1 AND exam_student.has_answer = 1 THEN 1 ELSE 0 END) AS not_checked',
                'SUM(CASE WHEN exam_student.act = 1 THEN 1 ELSE 0 END) AS akt_count',
                'SUM(CASE WHEN exam_student.has_answer = 0 THEN 1 ELSE 0 END) AS no_answer',
                'COUNT(*) AS all'
            ])
            ->leftJoin('teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
            ->leftJoin('user_access', 'teacher_access.user_id = user_access.user_id AND user_access.user_access_type_id = 2')
            ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
            ->leftJoin('subject', 'subject.id = exam_student.subject_id')
            ->leftJoin('exam', 'exam.id = exam_student.exam_id')
            ->where([
                'teacher_access.user_id' => current_user_id(),
            ])
            ->andWhere([$tableName . '.is_deleted' => 0])
            ->andWhere(['exam.archived' => 0])
            // ->groupBy(['profile.user_id', 'profile.last_name', 'profile.middle_name', 'profile.first_name'])
        ;

        // Execute the query and get the results
        return $query->all();
    }

    public function getNotCheckedCounta()
    {
        $model = new ExamStudent();

        $query = $model->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->leftJoin("exam", "exam.id = $this->table_name.exam_id");

        $query = $query->andWhere([
            'in',
            $this->table_name . '.teacher_access_id',
            $this->teacher_access()
        ]);

        $query = $query->andWhere([$this->table_name . '.exam_student.is_checked_full' => 0]);

        $query = $query->andWhere([$this->table_name . '.exam_student.act' => 1]);
        $query = $query->andWhere([$this->table_name . '.exam_student.has_answer' => 1]);

        $query = $query->andWhere(['exam.archived' => 0]);

        return $query->count();
    }

    public function getNotCheckedCount($user_id)
    {
        $model = new ExamStudent();
        $tableName = $model->tableName();

        // Construct the query
        $query = $model->find()
            ->andWhere([$tableName . '.is_deleted' => 0])
            ->leftJoin("exam", "exam.id = $tableName.exam_id")
            ->andWhere([
                'in',
                $tableName . '.teacher_access_id',
                $this->teacher_access(1, ['id'], $user_id)
            ])
            ->andWhere([$tableName . '.act' => 0])
            ->andWhere([
                $tableName . '.is_checked_full' => 0,

                $tableName . '.has_answer' => 1,
                'exam.archived' => 0
            ]);

        // return $query->createCommand()->getRawSql();
        return $query->count();
    }


    public function getPassword()
    {
        return $this->usernamePass['password'];
    }

    // getLoginHistory
    // public function getLoginHistory()
    // {
    //     return $this->hasMany(LoginHistory::className(), ['user_id' => 'id']);
    // }
    public function getLoginHistory()
    {
        return $this->hasMany(LoginHistory::className(), ['user_id' => 'id'])
            ->orderBy(['id' => SORT_DESC])->limit(10);
    }

    public function getTurniket()
    {
        return $this->hasMany(Turniket::className(), ['user_id' => 'id'])
            ->orderBy(['id' => SORT_DESC])->limit(10);
    }

    public function getTurniketDate()
    {
        $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        return $this->hasOne(Turniket::className(), ['user_id' => 'id'])
            ->andWhere(['date' => $date]);
    }

    public function getGoInTime1()
    {
        $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        return date('Y-m-d H:i:s', strtotime($this->hasOne(Turniket::className(), ['user_id' => 'id'])
            ->andWhere(['date' => $date])->one()->go_in_time));
    }

    public function getGoInTime()
    {
        $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        $timestamp = Turniket::find()
            ->select('go_in_time')
            ->where(['user_id' => $this->id, 'date' => $date])
            ->scalar();

        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }


    public function getGoOutTime()
    {
        $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        return date('Y-m-d H:i:s', strtotime($this->hasOne(Turniket::className(), ['user_id' => 'id'])
            ->andWhere(['date' => $date])->one()->go_out_time));
    }

    public function getLastIn()
    {
        return $this->hasOne(LoginHistory::className(), ['user_id' => 'id'])->onCondition(['log_in_out' => LoginHistory::LOGIN])->orderBy(['id' => SORT_DESC]);
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
                // if ($roleOne->item_name == 'student') {
                $authItem = AuthItem::find()->where(['name' => $roleOne->item_name])->one();
                $perms = $authItem->permissions;
                if ($perms && is_array($perms)) {
                    foreach ($perms as $row) {
                        $result[] = $row['name'];
                    }
                }
                // }
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

    public function getRolesNoStudent()
    {
        if ($this->roleItem) {
            $authItems = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            $result = [];
            foreach ($authItems as $authItem) {
                if ($authItem['item_name'] != 'student') {
                    $result[] = $authItem['item_name'];
                }
            }
            return $result;
        } else {
            return [];
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

    public function getAttendedTEST()
    {
        if (!empty($_GET['date'])) {
            $date = date("Y-m-d", strtotime(Yii::$app->request->get('date')));
            $week_id = date('N', strtotime($date));


            return Attend::find()
                ->where(['in' . 'time_table', TimeTable::find()
                    ->select('id')
                    ->andWhere([
                        'teacher_user_id' => $this->id,
                        'archived' => 0,
                        'is_deleted' => 0,
                        'week_id' => $week_id,
                    ])])
                ->andWhere(['date' => $date])
                ->count();
        }
        return Attend::find()
            ->where(['in' . 'time_table', TimeTable::find()
                ->select('id')
                ->andWhere([
                    'teacher_user_id' => $this->id,
                    'archived' => 0,
                    'is_deleted' => 0,
                ])])
            ->count();
    }

    public function getAttendedCountEski()
    {
        $query = Attend::find()
            ->innerJoinWith('time_table', false)
            ->andWhere([
                'time_table.teacher_user_id' => $this->id,
                'time_table.archived' => 0,
                'time_table.is_deleted' => 0,
            ]);

        if (!empty($_GET['date'])) {
            $date = date("Y-m-d", strtotime(Yii::$app->request->get('date')));
            $week_id = date('N', strtotime($date));

            $query->andWhere([
                'date' => $date,
                'week_id' => $week_id,
            ]);
        }

        return $query->count();
    }
    public function getAttendedCount()
    {
        $query = Attend::find()
            ->joinWith('timeTable') // Use joinWith instead of innerJoinWith
            ->andWhere([
                'time_table.teacher_user_id' => $this->id,
                'time_table.archived' => 0,
                'time_table.is_deleted' => 0,
            ]);

        if (!empty($_GET['date'])) {
            $date = date("Y-m-d", strtotime(Yii::$app->request->get('date')));
            $week_id = date('N', strtotime($date));

            $query->andWhere([
                'date' => $date,
                'time_table.week_id' => $week_id,
            ]);
        }

        return $query->count();
    }



    public function getTimeTables()
    {
        if (!empty($_GET['date'])) {
            $date = date("Y-m-d", strtotime(Yii::$app->request->get('date')));
            $week_id = date('N', strtotime($date));

            return $this->hasMany(TimeTable::className(), ['teacher_user_id' => 'id'])
                ->andWhere([
                    'archived' => 0,
                    'is_deleted' => 0,
                    'week_id' => $week_id,
                ]);
        }
        return $this->hasMany(TimeTable::className(), ['teacher_user_id' => 'id'])
            ->andWhere([
                'archived' => 0,
                'is_deleted' => 0,
            ]);
    }

    public function getTimeTablesCount()
    {
        return count($this->timeTables);
    }


    public function getKpiBall()
    {
        return $this->hasMany(KpiMark::className(), ['user_id' => 'id'])->andWhere(['archived' => 0, 'type' => 1, 'is_deleted' => 0])->sum('ball');
    }
    public function getKpiBall1()
    {
        return $this->hasMany(KpiMark::className(), ['user_id' => 'id'])->andWhere(['archived' => 0, 'type' => 1, 'is_deleted' => 0])->sum('ball_in');
    }


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
        return $this->hasOne(Oferta::className(), ['created_by' => 'id'])->onCondition(['archived' => 0, 'is_deleted' => 0]);
    }

    public function getOfertaAll()
    {
        return $this->hasOne(Oferta::className(), ['created_by' => 'id']);
    }

    // getNationality
    public function getNationality()
    {
        return Nationality::findOne($this->profile->nationality_id) ?? null;
    }

    // Profile Citizenship
    public function getCitizenship()
    {
        return Citizenship::findOne($this->profile->citizenship_id) ?? null;
    }


    public function getKpiMark()
    {
        return $this->hasMany(KpiMark::className(), ['user_id' => 'id'])
            ->onCondition(['archived' => 0, 'type' => 1, 'is_deleted' => 0]);
        // ->onCondition(['>=', 'created_at', 1725152461]);
    }

    // public function getKafedra()
    // {
    //    return getUserAccess
    //     return $this->hasOne(Kafedra::className(), ['user_id' => 'id']);
    // }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
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


    // UserAccess
    public function getUserAccess()
    {
        return $this->hasMany(UserAccess::className(), ['user_id' => 'id']);
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
        $userAccess = UserAccess::find()->where(['user_id' => $this->id, 'user_access_type_id' => 2])->one();
        return $userAccess->kafedra->translate->name ?? null;
    }

    // KafedraId
    public static function getKafedraId($user_id)
    {
        $userAccess = UserAccess::find()->where(['user_id' => $user_id, 'user_access_type_id' => 2])->one();
        return $userAccess->kafedra->id ?? null;
    }

    // FacultyName
    public function getFacultyName()
    {
        $userAccess = UserAccess::find()->where(['user_id' => $this->id, 'user_access_type_id' => 1])->one();

        return $userAccess->faculty->translate->name ?? null;
    }

    public function getFacultyNameViaKafedra()
    {
        return $this->kafedra->faculty->translate->name ?? null;
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
            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);

            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();

            if ($model->save()) {

                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */

                /** UserAccess */
                if (isset($post['user_access'])) {
                    $post['user_access'] = str_replace("'", "", $post['user_access']);
                    $user_access = json_decode(str_replace("'", "", $post['user_access']));

                    foreach ($user_access as $user_access_type_id => $tableIds) {

                        $userAccessType = UserAccessType::findOne($user_access_type_id);
                        if (isset($userAccessType)) {
                            foreach ($tableIds as $tableIdandIsLeader) {

                                $tableIdandIsLeaderExplode = explode('-', $tableIdandIsLeader);  // tableId-isLeader

                                if (isset($tableIdandIsLeaderExplode[0]) && isset($tableIdandIsLeaderExplode[1])) {
                                    $tableId = $userAccessType->table_name::find()->where(['id' => $tableIdandIsLeaderExplode[0]])->one();
                                    $da['tableId'][] = $tableId;
                                    if ($tableId && isset($tableId)) {
                                        $userAccessNew = new UserAccess();
                                        $userAccessNew->table_id = $tableId->id;
                                        $userAccessNew->user_access_type_id = $user_access_type_id;
                                        $userAccessNew->user_id = $model->id;
                                        $userAccessNew->is_leader = $tableIdandIsLeaderExplode[1];
                                        $userAccessNew->save(false);
                                        if ($tableIdandIsLeaderExplode[1]) {
                                            $tableId->user_id = $model->id;
                                            $tableId->save(false);
                                        }
                                    } else {
                                        $errors[] = ['table_id' => [_e('Not found')]];
                                    }
                                } else {
                                    $errors[] = ['user_access_type_id' => [_e('Not found')]];
                                }
                            }
                        } else {
                            $errors[] = ['userAccessType' => [_e('Not found')]];
                        }
                    }
                }
                /** UserAccess */

                $profile->user_id = $model->id;

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

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    // role ni userga assign qilish
                    $auth = Yii::$app->authManager;
                    $roles = json_decode(str_replace("'", "", $post['role']));
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

            if ($model->save()) {
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

                if (!$profile->save()) {
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
        if (isset($post['is_deleted'])) {
            if ($post['is_deleted'] == 0) {
                $model->deleted = 0;
            }
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
                    //**parolni shifrlab saqlaymiz */
                    $model->savePassword($password, $model->id);
                    //**** */
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                }
            }

            if ($model->save()) {

                /** UserAccess */
                // if (isset($post['user_access'])) {
                //     $post['user_access'] = str_replace("'", "", $post['user_access']);
                //     $user_access = json_decode(str_replace("'", "", $post['user_access']));
                //     // dd($user_access);
                //     UserAccess::deleteAll(['user_id' => $model->id]);
                //     foreach ($user_access as $user_access_type_id => $tableIds) {
                //         $userAccessType = UserAccessType::findOne($user_access_type_id);
                //         if (isset($userAccessType)) {
                //             foreach ($tableIds as $tableIdandIsLeader) {

                //                 $tableIdandIsLeaderExplode = explode('-', $tableIdandIsLeader);  // tableId-isLeader

                //                 if (isset($tableIdandIsLeaderExplode[0]) && isset($tableIdandIsLeaderExplode[1])) {
                //                     $tableId = $userAccessType->table_name::find()->where(['id' => $tableIdandIsLeaderExplode[0]])->one();
                //                     $da['tableId'][] = $tableId;
                //                     if ($tableId && isset($tableId)) {
                //                         $userAccessNew = new UserAccess();
                //                         $userAccessNew->table_id = $tableId->id;
                //                         $userAccessNew->user_access_type_id = $user_access_type_id;
                //                         $userAccessNew->user_id = $model->id;
                //                         $userAccessNew->is_leader = $tableIdandIsLeaderExplode[1];
                //                         $userAccessNew->save(false);
                //                         if ($tableIdandIsLeaderExplode[1]) {
                //                             $tableId->user_id = $model->id;
                //                             $tableId->save(false);
                //                         }
                //                     } else {
                //                         $errors[] = ['table_id' => [_e('Not found')]];
                //                     }
                //                 } else {
                //                     $errors[] = ['user_access_type_id' => [_e('Not found')]];
                //                 }
                //             }
                //         } else {
                //             $errors[] = ['userAccessType' => [_e('Not found')]];
                //         }
                //     }
                // }
                /** UserAccess */

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

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    if (isset($post['role'])) {
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
                                            $teacherAccessOne->save();
                                        }

                                        foreach ($teacherAccess as $subjectIds => $subjectIdsValues) {
                                            if (is_array($subjectIdsValues)) {
                                                foreach ($subjectIdsValues as $langId) {
                                                    $teacherAccessHas = TeacherAccess::findOne([
                                                        'user_id' => $model->id,
                                                        'subject_id' => $subjectIds,
                                                        'language_id' => $langId,
                                                    ]);
                                                    if ($teacherAccessHas) {
                                                        $teacherAccessHas->is_deleted = 0;
                                                        $teacherAccessHas->save();
                                                    } else {
                                                        $teacherAccessNew = new TeacherAccess();
                                                        $teacherAccessNew->user_id = $model->id;
                                                        $teacherAccessNew->subject_id = $subjectIds;
                                                        $teacherAccessNew->language_id = $langId;
                                                        $teacherAccessNew->save();
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
            /* $filePath = assets_url($model->profile->image);
            if(file_exists($filePath)){
                unlink($filePath);
            } */
            // remove profile
            $profileDeleted = Profile::findOne(['user_id' => $id]);
            $profileDeleted->is_deleted = 1;

            if (!$profileDeleted->save(false)) {
                $errors[] = [_e('Error in profile deleting process.')];
            }

            // $userAccess = UserAccess::findAll(['user_id' => $model->id]);
            // foreach ($userAccess as $userAccessOne) {
            //     $userAccessOne->is_deleted = 1;
            //     $userAccessOne->update();
            // }
            UserAccess::updateAll(['is_deleted' => 1], ['user_id' => $model->id]);
            try {
                TurniketMK::deletePerson($profileDeleted);
            } catch (\Exception $e) {
                // skip this step if any error
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

            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->avatar->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->avatar->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function uploadPassport()
    {
        if ($this->validate()) {

            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->passport_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->passport_file->saveAs($url, false);
            return "storage/" . $miniUrl;
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
