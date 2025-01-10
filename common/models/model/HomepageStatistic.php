<?php

namespace common\models\model;

use api\controllers\ApiActionTrait;
use api\resources\ResourceTrait;
use api\resources\User;
use common\models\AuthAssignment;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "building".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Room[] $rooms
 */
class HomepageStatistic extends \yii\db\ActiveRecord
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
        return 'homepage_statistic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','role_name' , 'edu_year_id' , 'date'], 'required'],
            [['user_id','role_name' , 'edu_year_id','is_deleted'], 'integer'],
            [['json'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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
            'edu_year_id',
            'date',
            'json',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'rooms',

            'roomLecture',
            'roomSeminar',
            'roomsCount',
            'roomsLectureCount',
            'roomSeminarCount',

            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function employeeCount()
    {
        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['users.deleted' => 0])
            ->groupBy('profile.user_id')
            ->andWhere(['not in', 'auth_assignment.item_name', ['admin' , currentRole(), 'student' , 'teacher']]);

        if (!isRole('admin')) {

            $userIds = AuthAssignment::find()
                ->select('user_id')
                ->where([
                    'in', 'auth_assignment.item_name',
                    AuthChild::find()->select('child')->where([
                        'in', 'parent', currentRole()
                    ])
                ]);
            $query->andFilterWhere([
                'in', 'users.id', $userIds
            ]);

            // faculty
            if (isRole('mudir')) {
                $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
                // // kafedra
                if ($k['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess'],
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }

            // Dean
            if (isRole('dean') || isRole('dean_deputy')) {
                $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID,2);
                if ($f['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $f['UserAccess'],
                            'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ])->orFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => Kafedra::find()->select('id')->where([
                                'faculty_id' => $f['UserAccess'],
                                'status' => 1,
                                'is_deleted' => 0,
                            ]),
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }

            // department
            if (isRole('dep_lead')) {
                $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID, 2);
                if ($d['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $d['UserAccess'],
                            'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }
        }

        return $query->count();
    }

    public function teacherCount()
    {
        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['users.deleted' => 0])
            ->groupBy('profile.user_id')
            ->andWhere(['in', 'auth_assignment.item_name', ['teacher']]);

        if (currentRole() != 'admin') {
            $userIds = AuthAssignment::find()
                ->select('user_id')
                ->where([
                    'in', 'auth_assignment.item_name',
                    AuthChild::find()->select('child')->where([
                        'in', 'parent', currentRole()
                    ])
                ]);
            $query->andFilterWhere([
                'in', 'users.id', $userIds
            ]);
        }

        if (!(isRole('admin'))) {

            // faculty
            if (isRole('mudir')) {
                $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
                // // kafedra
                if ($k['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess'],
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }

            // Dean
            if (isRole('dean') || isRole('dean_deputy')) {
                $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID,2);
                if ($f['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $f['UserAccess'],
                            'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ])->orFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => Kafedra::find()->select('id')->where([
                                'faculty_id' => $f['UserAccess'],
                                'status' => 1,
                                'is_deleted' => 0,
                            ]),
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }
            // department
            if (isRole('dep_lead')) {
                $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID, 2);
                if ($d['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $d['UserAccess'],
                            'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                            'is_deleted' => 0,
                            'status' => 1,
                        ])
                    ]);
                } else {
                    $query->andFilterWhere([
                        'users.id' => -1
                    ]);
                }
            }
        }

        return $query->count();
    }

    public function studentCount()
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->join('INNER JOIN', 'users', 'users.id = student.user_id')
            ->groupBy('profile.user_id');


        if (isRole('dean')) {
            $userAccess = UserAccess::find()
                ->select('table_id')
                ->where([
                    'user_id' => current_user_id(),
                    'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    'is_leader' => UserAccess::IS_LEADER_TRUE,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->asArray()
                ->one();
            $query = $query->andWhere([
                'student.faculty_id' => $userAccess
            ]);
        }

        if (isRole('tutor')) {
            $query = $query->andWhere([
                'student.tutor_id' => current_user_id()
            ]);
        }

        $count = 0;
        return $query->andWhere(['student.edu_form_id' => 1])->count();
        $man = $query->andWhere(['student.gender' => 1])->count();
        $eduForm = EduForm::find()->where(['status' => 1, 'is_deleted' => 0])->all();
        $eduFormData = [];
        if (count($eduForm) > 0) {
            foreach ($eduForm as $item) {
                $student_count = $query->andWhere(['student.edu_form_id' => $item->id])->count();
                $count = $count + $student_count;
                $eduFormData[] = [
                    'edu_form' => $item->translate->name ?? '',
                    'student_count' => $student_count,
                ];
            }
        }

        return [
            'all' => $count,
            'man' => $man,
            'woman' => $count - $man,
            'edu_form' => $eduFormData,
        ];
    }

    public static function mainRole($user, $date, $year)
    {
        $model = new HomepageStatistic();
        $model->user_id = $user->id;
        $model->role_name = $user->attach_role;
        $model->date = $date;
        $model->edu_year_id = $year->id;
        $json = [
            'employee' => $model->employeeCount(),
            'teacher' => $model->teacherCount(),
            'student' => $model->studentCount(),
        ];
        dd($json);
        $model->save(false);
        return ['is_ok' => true , 'model' => $model];
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
