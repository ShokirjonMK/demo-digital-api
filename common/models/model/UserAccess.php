<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $user_id
 * @property string $table_name
 * @property int $table_id
 * @property string $role_name
 * 
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property User $User
 */
class UserAccess extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;

    // leader status
    const IS_LEADER_TRUE = 1;
    const IS_LEADER_FALSE = 0;


    const WORK_TYPE_MAIN = 1;
    const WORK_TYPE_IN_MAIN = 2;
    const WORK_TYPE_OUT_MAIN = 3;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_access';
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
                    'table_id',
                    'user_access_type_id',
                ],
                'required'
            ],
            [
                [
                    'work_rate_id',
                    'job_title_id',
                    'work_type',
                    'has_kpi',

                    'user_id',
                    'table_id',
                    'is_leader',
                    'user_access_type_id',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'archived'
                ],
                'integer'
            ],
            [['tabel_number'], 'string', 'max' => 22],
            [['role_name', 'table_name'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_access_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccessType::className(), 'targetAttribute' => ['user_access_type_id' => 'id']],
            [['job_title_id'], 'exist', 'skipOnError' => true, 'targetClass' => JobTitle::className(), 'targetAttribute' => ['job_title_id' => 'id']],
            [['work_rate_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkRate::className(), 'targetAttribute' => ['work_rate_id' => 'id']],

            [['user_id', 'user_access_type_id', 'table_id'], 'unique', 'targetAttribute' => ['user_id', 'user_access_type_id', 'table_id'],],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User Id',
            'is_leader' => 'Is Leader',
            'table_name' => 'Table Name',
            'table_id' => 'Table Id',
            'role_name' => 'Role Name',
            'user_access_type_id' => 'user_access_type_id',
            'work_type' => 'work_type',

            'work_rate_id' => _e('work_rate_id'),
            'job_title_id' => _e('job_title_id'),
            'tabel_number' => _e('tabel_number'),

            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /*   public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'is_leader',
            'table_name',
            'table_id',
            'role_name',
            'user_access_type_id',

            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',

        ];
        return $fields;
    } */

    public function extraFields()
    {
        $extraFields =  [
            'attendance',
            'turniket',
            'user',
            'userAccessType',
            'profile',
            'fullName',
            'workRate',
            'jobTitle',
            'department',
            'kpiStaff',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getTurniket()
    {
        $dateFrom = new \DateTime(Yii::$app->request->get('year') . '-' . Yii::$app->request->get('month') . '-' . (Yii::$app->request->get('type') == 1 ? '01' : '16'));
        $dateTo = (new \DateTime(Yii::$app->request->get('year') . '-' . Yii::$app->request->get('month') . '-' . date('t', $dateFrom->getTimestamp())))->modify('+1 day');

        return Turniket::find()
            ->where(['between', 'date', $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
            ->andWhere(['turniket_id' => $this->profile->turniket_id])
            ->andWhere(['is_deleted' => 0])
            ->all();
    }


    public function getAttendance()
    {
        $year = Yii::$app->request->get('year');
        $month = Yii::$app->request->get('month');
        $type = Yii::$app->request->get('type');

        if (!$year || !$month || !$type) {
            return []; // Early return if any essential parameter is missing
        }

        // Define the date range based on $type
        $dateFrom = new \DateTime("$year-$month-" . ($type == 1 ? '01' : '16'));
        $dateTo = new \DateTime("$year-$month-" . ($type == 1 ? '15' : date('t', strtotime("$year-$month-01"))));

        $workingDays = [];
        $workingHours = $this->workRate->hour_day ?? 8;

        // Fetch relevant data before the loop
        $holidays = Holiday::find()
            ->where([
                'and', // Grouping the andWhere conditions
                ['<=', 'start_date', $dateTo->format('Y-m-d')],
                ['>=', 'finish_date', $dateFrom->format('Y-m-d')]
            ])
            ->orWhere(['between', 'moved_date', $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
            ->all();


        $vacations = Vocation::find()
            ->where(['<=', 'start_date', $dateTo->format('Y-m-d')])
            ->andWhere(['>=', 'finish_date', $dateFrom->format('Y-m-d')])
            ->andWhere(['user_id' => $this->user_id])
            ->all();

        $turnikets = Turniket::find()
            ->where(['>=', 'date', $dateFrom->format('Y-m-d')])
            ->andWhere(['<=', 'date', $dateTo->format('Y-m-d')])
            ->andWhere(['turniket_id' => $this->profile->turniket_id])
            ->indexBy('date') // Index by date for easy access
            ->all();

        // dd([isset($turnikets['2024-11-26']), isset($turnikets['2024-11-27'])]);
        // Loop through each day in the range
        while ($dateFrom <= $dateTo) {
            $currentDate = $dateFrom->format('Y-m-d');
            $dayNumber = $dateFrom->format('d');
            // Check for holidays
            $isHoliday = array_filter($holidays, function ($holiday) use ($currentDate) {
                return $holiday->start_date <= $currentDate && $holiday->finish_date >= $currentDate;
            });

            // Check for turniket data
            if (isset($turnikets[$currentDate])) {
                $workingDays[intval($dayNumber)] = $workingHours; // Working day from turniket
            }

            if ($isHoliday) {
                $holiday = reset($isHoliday);
                if ($holiday->type == 2 && $currentDate == $holiday->moved_date) {
                    // $workingDays[$holiday->moved_date] = $workingHours; // Moved holiday date
                    if (isset($turnikets[$currentDate])) {
                        $workingDays[intval($dayNumber)] = $workingHours; // Working day from turniket
                    }
                } else {
                    $workingDays[intval($dayNumber)] = "V"; // Regular holiday
                }
            }
            // Check for vacations
            elseif ($vacations) {
                $vacation = array_filter($vacations, function ($vac) use ($currentDate) {
                    return $vac->start_date <= $currentDate && $vac->finish_date >= $currentDate;
                });

                if ($vacation) {
                    $workingDays[intval($dayNumber)] = reset($vacation)->symbol; // Vacation symbol
                }
            }


            // Check for weekends
            if (in_array($dateFrom->format('N'), [6, 7])) { // 6 = Saturday, 7 = Sunday
                if (in_array($currentDate, array_column($holidays, 'moved_date'), true)) {
                    if (isset($turnikets[$currentDate])) {
                        $workingDays[intval($dayNumber)] = $workingHours; // Working day from turniket
                    } else {
                        $workingDays[intval($dayNumber)] = "A"; // Absent
                    }
                } else {
                    $workingDays[intval($dayNumber)] = "V"; // Weekend
                }
            }

            // Default to absent
            if (empty($workingDays[intval($dayNumber)])) {
                $workingDays[intval($dayNumber)] = "A"; // Absent
            }

            $dateFrom->modify('+1 day');
        }

        // dd($workingDays);
        return $workingDays;
    }

    public function getAttendance1()
    {
        $year = Yii::$app->request->get('year');
        $month = Yii::$app->request->get('month');
        $type = Yii::$app->request->get('type');

        // Validate input parameters
        if (!in_array($type, [1, 2])) {
            return []; // Invalid type
        }

        // Define the date range based on $type
        $dateFrom = new \DateTime($year . '-' . $month . ($type == 1 ? '-01' : '-16'));
        $dateTo = new \DateTime($year . '-' . $month . '-' . ($type == 1 ? '15' : date('t', strtotime("$year-$month-01"))));

        $workingHours = $this->workRate->hour_day ?? 8;
        $turniketId = $this->profile->turniket_id;

        $workingDays = [];

        // Preload holidays and vacations for the date range
        $holidays = Holiday::find()
            ->where(['<=', 'start_date', $dateTo->format('Y-m-d')])
            ->andWhere(['>=', 'finish_date', $dateFrom->format('Y-m-d')])
            ->all();

        $vacations = Vocation::find()
            ->where(['<=', 'start_date', $dateTo->format('Y-m-d')])
            ->andWhere(['>=', 'finish_date', $dateFrom->format('Y-m-d')])
            ->andWhere(['user_id' => $this->user_id])
            ->all();

        $holidayMap = [];
        foreach ($holidays as $holiday) {
            $holidayDates = new \DatePeriod(
                new \DateTime($holiday->start_date),
                new \DateInterval('P1D'),
                (new \DateTime($holiday->finish_date))->modify('+1 day')
            );
            foreach ($holidayDates as $date) {
                $holidayMap[$date->format('Y-m-d')] = $holiday;
            }
        }

        $vacationMap = [];
        foreach ($vacations as $vacation) {
            $vacationDates = new \DatePeriod(
                new \DateTime($vacation->start_date),
                new \DateInterval('P1D'),
                (new \DateTime($vacation->finish_date))->modify('+1 day')
            );
            foreach ($vacationDates as $date) {
                $vacationMap[$date->format('Y-m-d')] = $vacation->symbol;
            }
        }

        // Loop through each day in the range
        while ($dateFrom <= $dateTo) {
            $currentDate = $dateFrom->format('Y-m-d');
            $dayNumber = $dateFrom->format('d');

            // Check turniket by day and user_id
            $turniket = Turniket::find()
                ->where(['date' => $currentDate, 'turniket_id' => $turniketId])
                ->andWhere(['is_deleted' => 0])
                ->exists();

            if ($turniket) {
                $workingDays[intval($dayNumber)] = $workingHours;
            } else {
                $dayOfWeek = $dateFrom->format('N'); // 1 (Monday) to 7 (Sunday)

                if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                    $workingDays[intval($dayNumber)] = "V"; // Weekend
                } elseif (isset($holidayMap[$currentDate])) {
                    $holiday = $holidayMap[$currentDate];
                    $workingDays[intval($dayNumber)] = $holiday->type == 2 && $holiday->moved_date
                        ? $workingHours // Moved date
                        : "V"; // Holiday
                } elseif (isset($vacationMap[$currentDate])) {
                    $workingDays[intval($dayNumber)] = $vacationMap[$currentDate]; // Vacation symbol
                } else {
                    $workingDays[intval($dayNumber)] = $workingHours; // Default working day
                }
            }

            $dateFrom->modify('+1 day');
        }

        return $workingDays;
    }


    public function getKpiStaff()
    {
        $eduYearId = Yii::$app->request->get('edu_year_id');

        $query = $this->hasOne(KpiStaff::class, ['user_access_id' => 'id'])->orderBy(['id' => SORT_DESC]);

        if ($eduYearId !== null) {
            $query->andWhere(['edu_year_id' => $eduYearId]);
        }

        $query->andWhere(['is_deleted' => 0, 'archived' => 0]);

        return $query;
    }

    public function getDepartment()
    {
        return $this->hasOne($this->userAccessType->table_name::className(), ['id' => 'table_id']);
        $data = [];
        $data['user_access_type_id'] = $this->user_access_type_id;
        $data['model'] = $this->userAccessType->table_name::findOne($this->table_id);
        return $data;
    }

    public function getKafedra()
    {
        if ($this->user_access_type_id == 2)
            return $this->hasOne(Kafedra::className(), ['id' => 'table_id']);
        return null;
    }

    public function getFaculty()
    {
        if ($this->user_access_type_id == 2)
            return $this->hasOne(Faculty::className(), ['id' => 'table_id']);
        return null;
    }

    // public function getDepartment()
    // {
    //     if ($this->user_access_type_id == 3)
    //         return $this->hasOne(Department::className(), ['id' => 'table_id']);
    //     return null;
    // }
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }



    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getFullName() {}

    public function getWorkRate()
    {
        return $this->hasOne(WorkRate::className(), ['id' => 'work_rate_id']);
    }

    public function getJobTitle()
    {
        return $this->hasOne(JobTitle::className(), ['id' => 'job_title_id']);
    }



    /**
     * Gets query for [[UserAccessType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserAccessType()
    {
        return $this->hasOne(UserAccessType::className(), ['id' => 'user_access_type_id']);
    }

    /**
     * Gets query for [[Access]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccess()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function createItems($user_access_type_id, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $da = [];
        $table_id = isset($post['table_id']) ? $post['table_id'] : null;

        if ($table_id) {
            if (isset($post['user_access'])) {
                $user_access = json_decode(str_replace("'", "", $post['user_access']));
                foreach ($user_access as $user_id) {
                    $user = User::findOne($user_id);
                    $da['user_id'][] = $user_id;

                    $hasUserAccess = UserAccess::findOne([
                        'user_access_type_id' => $user_access_type_id,
                        'table_id' => $table_id,
                        'user_id' => $user_id
                    ]);

                    if ($user) {
                        $da['user'][] = $user->id;
                        if (!($hasUserAccess)) {
                            $da['hasUserAccess'][] = $hasUserAccess;
                            $newUserAccess = new UserAccess();
                            $newUserAccess->user_id = $user_id;
                            $newUserAccess->user_access_type_id = $user_access_type_id;
                            $newUserAccess->table_id = $table_id;
                            if (!($newUserAccess->validate())) {
                                $errors[] = $newUserAccess->errors;
                            } else {
                                $newUserAccess->save();
                            }
                        } else {
                            $errors[] = ['user_id' => [_e('This user already attached (' . $user_id . ')')]];
                        }
                    } else {
                        $errors[] = ['user_id' => [_e('User Id not found (' . $user_id . ')')]];
                    }
                }
            } else {
                $errors[] = ['user_access' => [_e('User Access is required')]];
            }
        } else {
            $errors[] = ['table_id' => [_e('Table Id is required')]];
        }
        // var_dump($da);
        // die();

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($modelExist = self::findOne(['table_id' => $post['table_id'], 'user_id' => $post['user_id'], 'is_deleted' => 1])) {
            $modelExist->attributes = $post;
            $modelExist->is_deleted = 0;
            if ($modelExist->save()) {
                $transaction->commit();
                return true;
            }
        }

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

    public static function changeLeader(int $tableId, int $userAccessTypeId, int $userId): bool
    {
        return (bool) self::updateAll(
            ['is_leader' => self::IS_LEADER_FALSE],
            [
                'table_id' => $tableId,
                'user_access_type_id' => $userAccessTypeId,
                'is_leader' => self::IS_LEADER_TRUE,
            ]
        ) && (bool) self::updateAll(
            ['is_leader' => self::IS_LEADER_TRUE],
            [
                'user_id' => $userId,
                'table_id' => $tableId,
                'user_access_type_id' => $userAccessTypeId,
            ]
        );
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
