<?php

namespace api\controllers;

use api\resources\User as ResourcesUser;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\model\EduYear;
use common\models\model\HomepageStatistic;
use common\models\model\Student;
use common\models\model\UserStatistic;
use Yii;
use common\models\model\Department;
use common\models\model\Kafedra;
use common\models\model\Profile;
use common\models\model\UserAccess;

use common\models\model\EduPlan;
use common\models\model\EduSemestrSubject;
use common\models\model\ExamStudent;
use common\models\model\ExamStudentAnswer;
use common\models\model\ExamStudentAnswerSubQuestion;
use common\models\model\Faculty;
use common\models\model\FacultyStatistic;
use common\models\model\KafedraStatistic;
use common\models\model\KpiMark;
use common\models\model\StudentTimeTable;
use common\models\model\SubjectContentMark;
use common\models\model\SurveyAnswer;
use common\models\model\TeacherAccess;
use common\models\model\TimeTable1;
use common\models\model\UserStatistic1;
use common\models\User;
use yii\db\Expression;
use yii\db\Query;

class StatisticController extends ApiActiveController
{
    public $modelClass = 'api\resources\BaseGet';

    public function actions()
    {
        return [];
    }


    public function actionHomePage()
    {
        $date = date("Ymd");
        $year = EduYear::activeEduYear();
        $user = current_user();

        $query = HomepageStatistic::findOne([
            'user_id' => $user->id,
            'edu_year_id' => $year->id,
            'date' => $date,
            'is_deleted' => 0
        ]);
        if ($query) {
            return $this->response(1, _e('Success'), $query);
        } else {
            if ($user->attach_role == 'admin' || $user->attach_role == 'rector' || $user->attach_role == 'dep_lead' || $user->attach_role == 'edu_admin') {
                $result = HomepageStatistic::mainRole($user , $date, $year);
            } elseif ($user->attach_role == 'dean' || $user->attach_role == 'dean_deputy') {

            }
        }
    }


    public function actionStatistic() {
        $data = [];
        $student = new Student();
        $teacher = new TeacherAccess();
        $employees = new UserAccess();
        // Umumiy talabalar soni
        $studentsAll = $student
            ->find()
            ->where([
                'is_deleted' => 0,
                'status' => 10
            ])
            ->count();
        $data['all-students'] = $studentsAll;

        // Umumiy o'qituvchilar soni
        $data['teachers'] = $teacher
            ->find()
            ->where([
                'is_deleted' => 0,
                'status' => 1
            ])
            ->groupBy('user_id')
            ->count();

        // Umumiy ishchilar soni
        $data['employees'] = $employees
            ->find()
            ->where([
                'is_deleted' => 0,
                'status' => 1
            ])
            ->groupBy('user_id')
            ->count();

        return $data;

    }


    public function actionStudents() {

        /*********/
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            // ->groupBy('student.id')
        ;

        // return $model->tableName();
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
        }

        /*  is Role check  */
        if (isRole('tutor')) {
            $query = $query->andWhere([
                'tutor_id' => current_user_id()
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  count($query->all());

        return $this->response(1, _e('Success'), $data);

    }

    public function actionEmpolyee() {
        $model = new \api\resources\User();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            // ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        $query = $query->andWhere(['!=', 'auth_assignment.item_name', "admin"]);

        $query = $query->andFilterWhere(['!=', 'auth_assignment.item_name', currentRole()]);

//         $query = $query->andFilterWhere(['not in', 'auth_assignment.item_name', parentRoles()]);


        // dd($query->createCommand()->getRawSql());

        //$query = $query->andWhere(['!=', 'auth_assignment.item_name', Yii::$app->request->get('query')]);


        if (currentRole() != 'admin') {
            $userIds = AuthAssignment::find()
                ->select('user_id')
                ->where([
                    'in', 'auth_assignment.item_name',
                    AuthChild::find()->select('child')->where([
                        'in', 'parent', AuthAssignment::find()->select("item_name")->where(['user_id' => current_user_id()])
                    ])
                ]);
            $query->andWhere([
                'in', 'users.id', $userIds
            ]);
            $query->andWhere([
                'in', 'users.id', current_user_id()
            ]);
        }


//        dd(AuthAssignment::find()->select('user_id')
//            ->where([
//                'in', 'auth_assignment.item_name', AuthChild::find()->select('parent')->where(['child' => currentRole()])
//            ])->orWhere(['auth_assignment.item_name' => currentRole()])->asArray()->all());

//        $userIds = AuthAssignment::find()
//            ->select('user_id')
//            ->andFilterWhere([
//                'in' , 'user_id' ,
//                AuthAssignment::find()->select('user_id')
//                    ->where([
//                        'in', 'auth_assignment.item_name', AuthChild::find()->select('parent')->where(['child' => currentRole()])
//                    ])->orWhere(['auth_assignment.item_name' => currentRole()])
//                ]);


        // /*  is Self  */

//        $kafedraId = Yii::$app->request->get('kafedra_id');
//        if (isset($kafedraId)) {
//            $query->andFilterWhere([
//                'in', 'users.id', UserAccess::find()->select('user_id')->where([
//                    'table_id' => $kafedraId,
//                    'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
//                ])
//            ]);
//        }
//
//        $facultyId = Yii::$app->request->get('faculty_id');
//        if (isset($facultyId)) {
//            $query->andFilterWhere([
//                'in', 'users.id', UserAccess::find()->select('user_id')->where([
//                    'table_id' => $facultyId,
//                    'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
//                ])
//            ]);
//        }

        if (!(isRole('admin')  || isRole('content_assign') || isRole('kpi_check'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
            if (isRole('mudir')) {

                if ($f['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $f['UserAccess'],
                            'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                        ])
                    ]);
                }

                // // kafedra
                if ($k['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess'],
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                        ])
                    ]);
                }
            }

            if (isRole('dean')) {
                if ($f['status'] == 1) {
                    $query->orFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess'],
                            'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                        ])
                    ]);
                }
                // kafedra
//                if ($k['status'] == 1) {
//                    $query->orFilterWhere([
//                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
//                            'table_id' => $k['UserAccess']->table_id,
//                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
//                        ])
//                    ]);
//                }
            }


            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $d['UserAccess'],
                        'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }
            if ($f['status'] == 2 && $k['status'] == 2 && $d['status'] == 2) {
                $query->andFilterWhere([
                    'users.id' => -1
                ]);
            }
        }
        /*  is Self  */

        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        //  Filter from Profile
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);

    }


    public function actionFacultyStatistic($lang) {

        $model = new Faculty();
        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere(['faculty.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = faculty.id and tr.table_name = 'faculty'")
            ->groupBy('faculty.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query->where([
                'in', 'faculty.id', $t['UserAccess']
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty.is_deleted' => -1
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionStudentCountByFaculty($lang)
    {
        $model = new FacultyStatistic();

        $table_name = 'faculty';

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

        return 0;
    }

    public function actionKafedra($lang)
    {
        return "ok";
        $model = new KafedraStatistic();

        $table_name = 'kafedra';

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

        return 0;
    }

    public function actionEduPlan($lang)
    {
        return "ok";
        $model = new EduPlan();
        $table_name = 'edu_plan';
        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            // ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query->andFilterWhere([
                'faculty_id' => $t['UserAccess']->table_id
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
        }
        // dd('ss');

        /*  is Self  */

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionChecking($lang)
    {
        return "ok";
        $model = new UserStatistic();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        // $userIds = AuthAssignment::find()->select('user_id')->where([
        //     'in', 'auth_assignment.item_name',
        //     AuthChild::find()->select('child')->where([
        //         'in', 'parent',
        //         AuthAssignment::find()->select("item_name")->where([
        //             'user_id' => current_user_id()
        //         ])
        //     ])
        // ]);

        // $query->andFilterWhere([
        //     'in', 'users.id', $userIds
        // ]);

        /*  is Self  */
        // if(isRole('dean')){

        // }


        if (!(isRole('admin'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
            if ($f['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $f['UserAccess']->table_id,
                        'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // kafedra
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $k['UserAccess']->table_id,
                        'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $d['UserAccess']->table_id,
                        'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }
            if ($f['status'] == 2 && $k['status'] == 2 && $d['status'] == 2) {
                $query->andFilterWhere([
                    'users.id' => -1
                ]);
            }
        }
        /*  is Self  */

        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }


    public function actionCheckingChala($lang)
    {
        return "ok";
        $model = new UserStatistic1();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        // $userIds = AuthAssignment::find()->select('user_id')->where([
        //     'in', 'auth_assignment.item_name',
        //     AuthChild::find()->select('child')->where([
        //         'in', 'parent',
        //         AuthAssignment::find()->select("item_name")->where([
        //             'user_id' => current_user_id()
        //         ])
        //     ])
        // ]);

        // $query->andFilterWhere([
        //     'in', 'users.id', $userIds
        // ]);

        /*  is Self  */
        // if(isRole('dean')){

        // }


        if (!(isRole('admin'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
            if ($f['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $f['UserAccess']->table_id,
                        'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // kafedra
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $k['UserAccess']->table_id,
                        'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $d['UserAccess']->table_id,
                        'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }
            if ($f['status'] == 2 && $k['status'] == 2 && $d['status'] == 2) {
                $query->andFilterWhere([
                    'users.id' => -1
                ]);
            }
        }
        /*  is Self  */

        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }

    public function actionExamChecking($lang)
    {
        // return "ok";
        $model = new UserStatistic();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        if (!(isRole('admin'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
            if ($f['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $f['UserAccess']->table_id,
                        'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // kafedra
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $k['UserAccess']->table_id,
                        'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $d['UserAccess']->table_id,
                        'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }
            if ($f['status'] == 2 && $k['status'] == 2 && $d['status'] == 2) {
                $query->andFilterWhere([
                    'users.id' => -1
                ]);
            }
        }
        /*  is Self  */

        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);


        $users =  $query->all();

        $data = [];
        foreach ($users as $user) {
            $userDATA = [];
            $t = true;
            $teacherAccess =  TeacherAccess::find()->where(['is_deleted' => 0, 'user_id' => $user->id])->all();

            $teacherAccessDATA = [];

            foreach ($teacherAccess as $teacherAccessOne) {
                $examStudent = ExamStudent::find()->where(['is_deleted' => 0, 'teacher_access_id' => $teacherAccessOne->id])->all();
                $examStudentCount = count($examStudent);

                if ($examStudentCount > 0) {

                    $examStudentCheckedCount = 0;

                    foreach ($examStudent as $examStudentOne) {

                        $isChecked = true;
                        $examStudentAnswer = ExamStudentAnswer::find()->where(['is_deleted' => 0, 'exam_student_id' => $examStudentOne->id])->all();
                        $hasAnswer = true;
                        foreach ($examStudentAnswer as $examStudentAnswerOne) {
                            $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::find()
                                ->where(['is_deleted' => 0, 'exam_student_answer_id' => $examStudentAnswerOne->id])
                                ->andWhere(['IS', 'ball', null])
                                ->andWhere(['IS', 'teacher_conclusion', null])
                                ->all();

                            $examStudentAnswerSubQuestionCount = count($examStudentAnswerSubQuestion);

                            if ($examStudentAnswerSubQuestionCount > 0) {
                                $isChecked = false;
                                // foreach ($examStudentAnswerSubQuestion as $examStudentAnswerSubQuestionOne) {
                                //     if (!isNull($examStudentAnswerSubQuestionOne->ball) && !isNull($examStudentAnswerSubQuestionOne->teacher_conclusion)) {
                                //         $isChecked = true;
                                //     }
                                // }
                            }
                        }

                        if ($isChecked) {
                            $examStudentCheckedCount = $examStudentCheckedCount + 1;
                        }
                    }

                    $teacherAccessDATA[]['checkedCount'] = $examStudentCheckedCount;
                    $teacherAccessDATA[]['mustCheckedCount'] = $teacherAccessOne->examStudentCount;
                }
            }

            $userDATA['user'] = $user;
            $userDATA['teacherAccess'] = $teacherAccessDATA;
            $data[] = $userDATA;
        }

        return $data;
    }


    public function actionKpiContentStore()
    {
        // return "ok";
        $model = new UserStatistic();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id');

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        $data = [];
        $errors = [];
        $created_by = 7457;
        $users = $query->all();
        foreach ($users as $userOne) {

            $summ = SubjectContentMark::find()
                ->where([
                    'user_id' => $userOne->id,
                    'is_deleted' => 0
                ])
                ->sum('ball');

            $count = SubjectContentMark::find()
                ->where([
                    'user_id' => $userOne->id,
                    'is_deleted' => 0
                ])
                ->count();

            $created = SubjectContentMark::findOne([
                'user_id' => $userOne->id,
                'is_deleted' => 0
            ]);

            if ($created) $created_by  = $created->created_by;

            // $data[$userOne->id]['sum'] = $summ;
            // $data[$userOne->id]['count'] = $count;
            if ($count > 0) {

                $hasKpiMark = KpiMark::findOne([
                    'user_id' => $userOne->id,
                    'kpi_category_id' => 8,
                    'is_deleted' => 0
                ]);

                if ($hasKpiMark) {
                    $newKpiMark = $hasKpiMark;
                } else {
                    $newKpiMark = new KpiMark();
                }
                $newKpiMark->type = 1;
                $newKpiMark->created_by = $created_by;
                $newKpiMark->kpi_category_id = 8;
                $newKpiMark->user_id = $userOne->id;
                $newKpiMark->edu_year_id = 16;
                $newKpiMark->ball = round($summ / $count);
                $result = KpiMark::createItemStat($newKpiMark);
                if (is_array($result)) {
                    $errors[] = [$userOne->id => [$newKpiMark, $result]];
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }

    public function actionKpiSurveyStore($i)
    {
        return "ok";

        /*     SELECT
	time_table.teacher_user_id,
	ROUND( AVG( survey_answer.ball ), 0 ) AS average_ball ,
	AVG( survey_answer.ball )
FROM
	time_table
	INNER JOIN student_time_table ON time_table.id = student_time_table.time_table_id
	INNER JOIN survey_answer ON student_time_table.student_id = survey_answer.student_id 
	AND time_table.subject_id = survey_answer.subject_id 
WHERE
	time_table.archived = 1 
-- 	and time_table.teacher_user_id = 8177
GROUP BY
	time_table.teacher_user_id */

        $model = new UserStatistic();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->groupBy('users.id');

        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        $query = $query->orderBy(['users.id' => SORT_DESC]);
        $soni = $i * 50;
        $query = $query->limit(50)->offset($soni);



        $data = [];
        $errors = [];
        $created_by = 7457;

        // dd($query->createCommand()->getRawSql());

        $users = $query->all();
        foreach ($users as $userOne) {

            $surveyAnswerAverage = SurveyAnswer::find()
                ->where(['in', 'created_by', StudentTimeTable::find()
                    ->where(['in', 'time_table_id', TimeTable1::find()
                        ->where([
                            'teacher_user_id' => $userOne->id,
                            'archived' => 1
                        ])
                        ->select('id')])
                    ->select('created_by')])
                ->andWhere([
                    'in',  'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in', 'subject_id',
                        TeacherAccess::find()->select('subject_id')
                            ->where([
                                'user_id' => $userOne->id
                            ])
                    ])
                ]); //->average('ball');


            dd($surveyAnswerAverage->createCommand()->getRawSql());




            $created_by  = 591; // bosit oka

            $hasKpiMark = KpiMark::findOne([
                'user_id' => $userOne->id,
                'kpi_category_id' => 12,
                'is_deleted' => 0
            ]);

            if ($hasKpiMark) {
                $newKpiMark = $hasKpiMark;
            } else {
                $newKpiMark = new KpiMark();
            }

            $newKpiMark->type = 1;
            $newKpiMark->created_by = $created_by;
            $newKpiMark->kpi_category_id = 12;
            $newKpiMark->user_id = $userOne->id;
            $newKpiMark->edu_year_id = 17;
            // $newKpiMark->ball = round($summ / $count);
            $result = KpiMark::createItemStat($newKpiMark);
            if (is_array($result)) {
                $errors[] = [$userOne->id => [$newKpiMark, $result]];
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }

    public function actionKpiSurveyStore00($i)
    {
        // return "ok";
        $model = new UserStatistic();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->groupBy('users.id');

        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        $query = $query->orderBy(['users.id' => SORT_DESC]);
        $soni = $i * 50;
        $query = $query->limit(50)->offset($soni);



        $data = [];
        $errors = [];
        $created_by = 7457;

        // dd($query->createCommand()->getRawSql());

        $users = $query->all();
        foreach ($users as $userOne) {

            $summ = SurveyAnswer::find()
                ->where([
                    'in',  'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in', 'subject_id',
                        TeacherAccess::find()->select('subject_id')
                            ->where([
                                'user_id' => $userOne->id,
                                'is_deleted' => 0
                            ])
                    ])
                ])
                ->sum('ball');

            $count = SurveyAnswer::find()
                ->where([
                    'in',  'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in', 'subject_id',
                        TeacherAccess::find()->select('subject_id')
                            ->where([
                                'user_id' => $userOne->id,
                                'is_deleted' => 0
                            ])
                    ])
                ])
                ->count();

            $created_by  = 591; // bosit oka

            if ($count > 0) {

                $hasKpiMark = KpiMark::findOne([
                    'user_id' => $userOne->id,
                    'kpi_category_id' => 12,
                    'is_deleted' => 0
                ]);

                if ($hasKpiMark) {
                    $newKpiMark = $hasKpiMark;
                } else {
                    $newKpiMark = new KpiMark();
                }

                $newKpiMark->type = 1;
                $newKpiMark->created_by = $created_by;
                $newKpiMark->kpi_category_id = 12;
                $newKpiMark->user_id = $userOne->id;
                $newKpiMark->edu_year_id = 17;
                $newKpiMark->ball = round($summ / $count);
                $result = KpiMark::createItemStat($newKpiMark);
                if (is_array($result)) {
                    $errors[] = [$userOne->id => [$newKpiMark, $result]];
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }
}
