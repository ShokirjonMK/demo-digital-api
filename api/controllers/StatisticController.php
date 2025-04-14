<?php

namespace api\controllers;

use api\resources\User as ResourcesUser;
use common\models\model\UserStatistic;
use Yii;
use common\models\model\Department;
use common\models\model\Kafedra;
use common\models\model\Profile;
use common\models\model\UserAccess;

use common\models\model\EduPlan;
use common\models\model\EduSemestrSubject;
use common\models\model\ExamControlStudent;
use common\models\model\ExamStudent;
use common\models\model\ExamStudentAnswer;
use common\models\model\ExamStudentAnswerSubQuestion;
use common\models\model\Faculty;
use common\models\model\FacultyStatistic;
use common\models\model\KafedraStatistic;
use common\models\model\KpiMark;
use common\models\model\KpiStaff;
use common\models\model\Statistic;
use common\models\model\SubjectContentMark;
use common\models\model\SurveyAnswer;
use common\models\model\TeacherAccess;
use common\models\model\UserStatistic1;
use yii\db\Expression;
use yii\db\Query;

class StatisticController extends ApiActiveController
{
    public $modelClass = 'api\resources\statistic';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Statistic();

        $query = $model->find()
            ->where(['is_deleted' => 0])
            ->andFilterWhere(['like', 'key', Yii::$app->request->get('query')]);


        if (Yii::$app->request->get('all') != 1) {
            $query->andFilterWhere(['date' => date('Y-m-d')]);
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
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
                        'table_id' => $f['UserAccess']->table_id,
                        'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // kafedra
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
                        'table_id' => $k['UserAccess']->table_id,
                        'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
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
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
                        'table_id' => $f['UserAccess']->table_id,
                        'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // kafedra
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
                        'table_id' => $k['UserAccess']->table_id,
                        'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
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
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
                        'table_id' => $f['UserAccess']->table_id,
                        'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // kafedra
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
                        'table_id' => $k['UserAccess']->table_id,
                        'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }

            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'users.id',
                    UserAccess::find()->select('user_id')->where([
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


    // public function actionKpiContentStore231321321()
    // {
    //     // return "ok";
    //     $model = new UserStatistic();

    //     $query = $model->find()
    //         ->with(['profile'])
    //         ->andWhere(['users.deleted' => 0])
    //         ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
    //         ->groupBy('users.id');

    //     // dd($query->createCommand()->getRawSql());
    //     $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

    //     $errors = [];
    //     $created_by = 7457;
    //     $users = $query->all();
    //     foreach ($users as $userOne) {

    //         $balllllll = SubjectContentMark::find()
    //             ->where([
    //                 'user_id' => $userOne->id,
    //                 'is_deleted' => 0,
    //                 'archived' => 0
    //             ])->andWhere([
    //                 'in', 'subject_id',
    //                 TeacherAccess::find()
    //                     ->where([
    //                         'in', 'subject_id',
    //                         Subject::find()->where(['in', 'semestr_id', [1, 3, 5, 7]])
    //                             ->select('id')
    //                     ])
    //                     ->andWhere([
    //                         'user_id' => $userOne->id,
    //                         'is_deleted' => 0,
    //                         'status' => 1
    //                     ])
    //                     ->select('subject_id')
    //             ])
    //             ->average('ball');



    //         $created = SubjectContentMark::findOne([
    //             'user_id' => $userOne->id,
    //             'is_deleted' => 0,
    //             'archived' => 0
    //         ]);

    //         if ($created) $created_by  = $created->created_by;

    //         $hasKpiMark = KpiMark::findOne([
    //             'user_id' => $userOne->id,
    //             'kpi_category_id' => 8,
    //             'is_deleted' => 0,
    //             'archived' => 0
    //         ]);

    //         if ($hasKpiMark) {
    //             $newKpiMark = $hasKpiMark;
    //         } else {
    //             $newKpiMark = new KpiMark();
    //         }
    //         $newKpiMark->type = 1;
    //         $newKpiMark->created_by = $created_by;
    //         $newKpiMark->kpi_category_id = 8;
    //         $newKpiMark->user_id = $userOne->id;
    //         $newKpiMark->edu_year_id = 17;
    //         $newKpiMark->ball = round($balllllll);
    //         $result = KpiMark::createItemStat($newKpiMark);
    //         if (is_array($result)) {
    //             $errors[] = [$userOne->id => [$newKpiMark, $result]];
    //         }
    //     }

    //     if (count($errors) > 0) {
    //         return $errors;
    //     }
    //     return "ok";
    // }

    public function actionKpiContentStoresssss1231312()
    {
        // Initialize the UserStatistic model
        $model = new UserStatistic();

        // Base query to fetch users who are teachers and not deleted
        $query = $model->find()
            ->with(['profile'])
            ->where(['users.deleted' => 0])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['auth_assignment.item_name' => "teacher"])
            ->groupBy('users.id');

        // Initialize error container
        $errors = [];

        // Default creator ID
        $created_by = 7702;

        // Loop through the user records and process them
        foreach ($query->all() as $userOne) {

            // Sub-query for fetching relevant subject IDs
            $subjectQuery = TeacherAccess::find()
                ->where([
                    'user_id' => $userOne->id,
                    // 'is_deleted' => 0,
                    // 'status' => 1
                ])
                // ->andWhere([
                //     'in', 'subject_id',
                //     Subject::find()->where(['in', 'semestr_id', [1, 3, 5, 7]])->select('id')
                // ])   
                ->select('subject_id');

            // Calculate the average 'ball' value
            $avgBall = SubjectContentMark::find()
                ->where([
                    'user_id' => $userOne->id,
                    'is_deleted' => 0,
                    'archived' => 0
                ])
                ->andWhere(['in', 'subject_id', $subjectQuery])
                ->average('ball');

            // Fetch the creator ID if available
            $created = SubjectContentMark::findOne([
                'user_id' => $userOne->id,
                'is_deleted' => 0,
                'archived' => 0
            ]);
            if ($created) $created_by = $created->created_by;

            // Check for an existing KpiMark entry
            $existingKpiMark = KpiMark::findOne([
                'user_id' => $userOne->id,
                'kpi_category_id' => 8,
                'is_deleted' => 0,
                'archived' => 0
            ]);

            // Initialize a new or existing KpiMark record
            $kpiMark = $existingKpiMark ?? new KpiMark();

            // Update the KpiMark details
            $kpiMark->type = 1;
            $kpiMark->created_by = $created_by;
            $kpiMark->kpi_category_id = 8;
            $kpiMark->user_id = $userOne->id;
            $kpiMark->edu_year_id = 17;
            $kpiMark->ball = round($avgBall);

            // Save or update the KpiMark entry
            $result = KpiMark::createItemStat($kpiMark);
            if (is_array($result)) {
                $errors[] = [$userOne->id => [$kpiMark, $result]];
            }
        }

        // Check if any errors occurred during processing
        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }

    public function actionKpiContentStore()
    {

        // dd("starting");
        // Initialize the UserStatistic model
        $model = new KpiStaff();

        // Base query to fetch users who are teachers and not deleted
        $query = $model->find()
            ->where([
                'user_access_type_id' => 2,
                'edu_year_id' => 69,
            ]);

        // Initialize error container
        $errors = [];

        // Default creator ID
        $created_by = 7648;

        // Loop through the user records and process them
        foreach ($query->all() as $kpiOne) {


            // Calculate the average 'ball' value
            $in_doc_ball = 2 * ($kpiOne->in_doc_percent > 0 ? $kpiOne->in_doc_percent : 0) / 100;
            $ex_doc_ball = 2 * ($kpiOne->ex_doc_percent > 0 ? $kpiOne->ex_doc_percent : 0) / 100;
            $avgBall = $in_doc_ball + $ex_doc_ball;

            // Check for an existing KpiMark entry
            $existingKpiMark = KpiMark::findOne([
                'user_id' => $kpiOne->user_id,
                'kpi_category_id' => 41,
                'is_deleted' => 0,
                'archived' => 0
            ]);

            // Initialize a new or existing KpiMark record
            $kpiMark = $existingKpiMark ?? new KpiMark();

            // Update the KpiMark details
            $kpiMark->type = 1;
            $kpiMark->created_by = $created_by;
            $kpiMark->kpi_category_id = 41;
            $kpiMark->user_id = $kpiOne->user_id;
            $kpiMark->edu_year_id = 69;
            $kpiMark->ball = round($avgBall);

            // Save or update the KpiMark entry
            $result = KpiMark::createItemStat($kpiMark);
            if (is_array($result)) {
                $errors[] = [$kpiOne->user_id => [$kpiMark, $result]];
            }
        }

        // Check if any errors occurred during processing
        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }


    public function actionKpiSurveyStore($i)
    {
        // return "ok";

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
            time_table.teacher_user_id
        */

        $model = new UserStatistic();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->orderBy(['users.id' => SORT_DESC])
            ->groupBy('users.id');

        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        $query = $query->orderBy(['users.id' => SORT_DESC]);
        $soni = $i * 10;
        $query = $query->limit(10)->offset($soni);



        $data = [];
        $errors = [];

        // dd($query->createCommand()->getRawSql());
        // $userIds = [
        //     15121, 15119, 15118, 15116, 15111, 15072, 15058, 15055, 14584, 14096, 14095, 14053, 13933, 13932, 13929, 13925, 13900, 13647, 13646, 13645, 13563, 13560, 13556, 13555, 13518, 13518, 13471, 12394, 12393, 12390, 12378, 12359, 12156, 11977, 11974, 11970, 11944, 11943, 11941, 11939, 11921, 11920, 11916, 11914, 11912, 11911, 11908, 11907, 11906, 11903, 11900, 11899, 11894, 11893, 11884, 11883, 11882, 11880, 11879, 11878, 11870, 11869, 11796, 11754, 11753, 11744, 11739, 11738, 11737, 11724, 11718, 11717, 11715, 11711, 11710, 11708, 11707, 11704, 11612, 11610, 11557, 11551, 11549, 11548, 11547, 11546, 11545, 11542, 11541, 11540, 11539, 11535, 11534, 11533, 11532, 11531, 11530, 11529, 11526, 11525, 11524, 11522, 11490, 11436, 11371, 11165, 11135, 11134, 11132, 10685, 10636, 10532, 10493, 10469, 10467, 10368, 10214, 10180, 10179, 10177, 10033, 10030, 9229, 9228, 9022, 9021, 8991, 8988, 8776, 8353, 8351, 8350, 8348, 8342, 8341, 8338, 8335, 8334, 8259, 8177, 8132, 8123, 8119, 8109, 7826, 7823, 7802, 7801, 7786, 7780, 7775, 7772, 7769, 7747, 7728, 7727, 7726, 7725, 7723, 7719, 7708, 7707, 7706, 7704, 7703, 7702, 7700, 7699, 7698, 7697, 7695, 7691, 7688, 7686, 7683, 7681, 7679, 7678, 7677, 7676, 7675, 7674, 7672, 7671, 7668, 7667, 7666, 7664, 7663, 7662, 7660, 7659, 7656, 7655, 7650, 7648, 7646, 7645, 7635, 7633, 7629, 7625, 7622, 7613, 7609, 7607, 7606, 7605, 7604, 7598, 7596, 7595, 7594, 7593, 7592, 7591, 7590, 7569, 7565, 7494, 7492, 7491, 7490, 7488, 7487, 7486, 7485, 7484, 7482, 7481, 7480, 7479, 7478, 7476, 7475, 7474, 7473, 7472, 7471, 7470, 7469, 7467, 7466, 7462, 7461, 7460, 7458, 7456, 7453, 7452, 7450, 7449, 7448, 7441, 7437, 7436, 7435, 7434, 7429, 7428, 7427, 7426, 7425, 7423, 7422, 7420, 7419, 7418, 7417, 7416, 7415, 6583, 4705, 4704, 4703, 4702, 4700, 4699, 4698, 4697, 4696, 4695, 4694, 4693, 4689, 4688, 4687, 4686, 4685, 4678, 4677, 4676, 4675, 4673, 4669, 4665, 4664, 4663, 4662, 4657, 4656, 4652, 4651, 4650, 4649, 4648, 4647, 4646, 4645, 4644, 4642, 4641, 4640, 4639, 4638, 4637, 4636, 4635, 4634, 4633, 4632, 4631, 4630, 4629, 4628, 4627, 4626, 4625, 4624, 4623, 4622, 4621, 4620, 4618, 4617, 4616, 4615, 4614, 4613, 4611, 4610, 4607, 4605, 4604, 4603, 4602, 4596, 4592, 4591, 4589, 4588, 4586, 4585, 4580, 4576, 4575, 4574, 4573, 4571, 4570, 4569, 4568, 4567, 4566, 4565, 4564, 4563, 4562, 4561, 4560, 4559, 4558, 4557, 4556, 4554, 4553, 4552, 4551, 4546, 4545, 4544, 4543, 4542, 4541, 4539, 4538, 4537, 4536, 4535, 4280, 4279, 4278, 4276, 4275, 4274, 4273, 4272, 4270, 4269, 4268, 4267, 4258, 4256, 4254, 4252, 4249, 4248, 4245, 4242, 4239, 4237, 4235, 4234, 4233, 4231, 4229, 4228, 4218, 4214, 2941, 2940, 2939, 2938, 2937, 2936, 2935, 2934, 2933, 2932, 2931, 2930, 2929, 2927, 2926, 2925, 2924, 2922, 2921, 2918, 2917, 2916, 2915, 2914, 2913, 2912, 2911, 2909, 2908, 2907, 2906, 2905, 2904, 2903, 2902, 2901, 2900, 2899, 2898, 2897, 2896, 2895, 2894, 2893, 2892, 2891, 2890, 2889, 2888, 2887, 2886, 2885, 2884, 2883, 2882, 1616, 1615, 1614, 1125, 906, 609, 605, 601, 600, 598, 597, 596, 595, 594, 593, 592, 591, 312, 310, 209, 208, 207, 206, 205, 203, 202, 201, 200, 197, 195, 193, 189, 185, 184, 50, 48, 10, 6, 5
        // ];

        $users = $query->all();
        foreach ($users as $userOne) {

            // foreach ($userIds as $userOne) {

            // $surveyAnswerAverage = SurveyAnswer::find()
            //     ->where(['in', 'student_id', StudentTimeTable::find()
            //         ->where(['in', 'time_table_id', TimeTable::find()
            //             ->where([
            //                 'teacher_user_id' => $userOne->id,
            //                 'archived' => 0
            //             ])
            //             ->select('id')])
            //         ->select('student_id')])
            //     ->andWhere([
            //         'in',   'subject_id',
            //         TimeTable::find()
            //             ->where([
            //                 'teacher_user_id' => $userOne->id,
            //                 'archived' => 0
            //             ])
            //             ->select('subject_id')
            //     ])
            //     ->andWhere(['archived' => 0])
            //     ->average('ball');

            $query = (new Query())
                ->select(['AVG(sa.ball) AS surveyAnswerAverage'])
                ->from('survey_answer sa')
                ->innerJoin('student_time_table stt', 'sa.student_id = stt.student_id')
                ->innerJoin('time_table tt', 'stt.time_table_id = tt.id')
                ->where([
                    'tt.teacher_user_id' => $userOne->id,  // Teacher's user ID
                    // 'tt.archived' => 1,             // archived entries in the time_table
                    'sa.subject_id' => new \yii\db\Expression('tt.subject_id'), // sa.subject_id must match tt.subject_id
                    // 'sa.archived' => 0,             // Non-archived entries in survey_answer
                    'tt.edu_year_id' => 18,             // Non-archived entries in survey_answer
                ]);

            // Execute the query and get the average
            $surveyAnswerAverage = $query->scalar();



            // dd($surveyAnswerAverage->createCommand()->getRawSql());

            $created_by  = 7702; // ikrom oka

            $hasKpiMark = KpiMark::findOne([
                'user_id' => $userOne->id,
                'kpi_category_id' => 12,
                'is_deleted' => 0,
                'archived' => 0
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
            $newKpiMark->edu_year_id = 70;
            $newKpiMark->ball = 0;
            // $newKpiMark->ball = round($surveyAnswerAverage);
            $newKpiMark->ball_in = round($surveyAnswerAverage);
            // $newKpiMark->ball = round($summ / $count);
            // $result = KpiMark::createItemStat($newKpiMark);
            $data[$userOne->id] = round($surveyAnswerAverage);

            if (!$newKpiMark->save()) {
                $errors[] = [$userOne->id => [$newKpiMark, $newKpiMark->getErrors()]];
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return $data;
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
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in',
                        'subject_id',
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
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in',
                        'subject_id',
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
                    'is_deleted' => 0,
                    'archived' => 0
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


    public function actionControlAppeal()
    {
        $query = ExamControlStudent::find()
            ->select([
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                'exam_control_student.appeal',
                'exam_control_student.appeal_text',
                'exam_control_student.appeal_conclution',
                'exam_control_student.appeal_status',
                'exam_control_student.appeal2',
                'exam_control_student.appeal2_text',
                'exam_control_student.appeal2_conclution',
                'exam_control_student.appeal2_status',
                'sub.NAME AS subject_name',
                'subject.kafedra_id',
                'kaf.NAME AS kafedra_name',
            ])
            ->leftJoin('subject', 'exam_control_student.subject_id = subject.id')
            ->leftJoin(['sub' => 'translate'], 'sub.model_id = exam_control_student.subject_id AND sub.language = :language AND sub.table_name = :subjectTable', [
                ':language' => 'uz',
                ':subjectTable' => 'subject'
            ])
            ->leftJoin(['kaf' => 'translate'], 'kaf.model_id = subject.kafedra_id AND kaf.language = :language AND kaf.table_name = :kafedraTable', [
                ':language' => 'uz',
                ':kafedraTable' => 'kafedra'
            ])
            ->leftJoin('student', 'student.id = exam_control_student.student_id')
            ->leftJoin('profile', 'profile.user_id = student.user_id')
            ->where([
                'or',
                ['and', ['is not', 'exam_control_student.appeal', new Expression('null')], ['is', 'exam_control_student.appeal_status', new Expression('null')]],
                ['and', ['is not', 'exam_control_student.appeal2', new Expression('null')], ['is', 'exam_control_student.appeal2_status', new Expression('null')]],
            ])
            ->andWhere(['exam_control_student.archived' => 0]);

        // Uncomment the following lines if you need to group the results
        // $query->groupBy([
        //     'subject.kafedra_id',
        //     'kaf.NAME'
        // ]);

        $results = $query->all();
    }

    public function actionExamAppeal()
    {
        $subQuery = (new \yii\db\Query())
            ->select([
                'COUNT(0) AS soni',
                'profile.user_id AS user_id',
                'profile.last_name AS last_name',
                'profile.first_name AS first_name',
                'profile.middle_name AS middle_name',
                'exam_appeal.exam_id AS exam_id',
            ])
            ->from('exam_appeal')
            ->leftJoin('teacher_access', 'teacher_access.id = exam_appeal.teacher_access_id')
            ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
            ->leftJoin('exam_student', 'exam_student.id = exam_appeal.exam_student_id')
            ->where([
                'exam_appeal.type' => null,
                'exam_student.act' => 0,
            ])
            ->groupBy([
                'profile.user_id',
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                'exam_appeal.exam_id',
            ]);

        $query = (new \yii\db\Query())
            ->select([
                'exam_appeal_not_checking.*',
                'translate.name',
            ])
            ->from(['exam_appeal_not_checking' => $subQuery])
            ->leftJoin('user_access', 'user_access.user_id = exam_appeal_not_checking.user_id AND user_access.user_access_type_id = 2')
            ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = :language AND translate.table_name = :tableName', [
                ':language' => 'uz',
                ':tableName' => 'kafedra'
            ]);

        $results = $query->all();
    }

    public function actionBmiTekshirish()
    {

        $query = (new Query())
            ->select([
                'translate.NAME AS kafedra_nomi',
                'profile.user_id',
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                'examtr.name AS exam_name',
                'exam_student.exam_id',
                'SUM(CASE WHEN exam_student.ball IS NULL AND exam_student.act != 1 AND exam_student_answer.file IS NOT NULL THEN 1 ELSE 0 END) AS tekshirmaganlar',
                'SUM(CASE WHEN exam_student.act = 1 THEN 1 ELSE 0 END) AS akt_soni',
                // 'SUM(CASE WHEN exam_student.has_answer_new = 0 THEN 1 ELSE 0 END) AS yozilmaganlar',
                'COUNT(*) AS jami'
            ])
            ->from('exam_student')
            ->leftJoin('teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
            ->leftJoin('user_access', 'teacher_access.user_id = user_access.user_id AND user_access.user_access_type_id = 2')
            ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
            ->leftJoin('subject', 'subject.id = exam_student.subject_id')
            ->leftJoin('exam_student_answer', 'exam_student_answer.exam_student_id = exam_student.id')
            ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = "uz" AND translate.table_name = "kafedra"')
            ->leftJoin('translate AS examtr', 'examtr.model_id = exam_student.exam_id AND examtr.language = "uz" AND examtr.table_name = "exam"')
            ->where(['exam_student.subject_id' => 456])
            ->groupBy([
                'translate.NAME',
                'examtr.name',
                'exam_student.exam_id',
                'profile.user_id',
                'profile.last_name',
                'profile.middle_name',
                'profile.first_name'
            ])
            ->orderBy(['tekshirmaganlar' => SORT_DESC]);

        // To execute the query and get the results
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public function actionExamCheckingnew()
    {
        if (Yii::$app->request->get('appeal') == 1) {

            $query = (new Query())
                ->select([
                    'COUNT(0) AS soni',
                    'profile.user_id AS user_id',
                    'profile.last_name AS last_name',
                    'profile.first_name AS first_name',
                    'profile.middle_name AS middle_name',
                    'exam_appeal.exam_id AS exam_id',
                    'tr_exam.NAME AS exam_name',
                    'translate.NAME AS kafedra',
                    new \yii\db\Expression("CONCAT('https://digital.tsul.uz/appelation_cards/', CAST(exam_appeal.exam_id AS CHAR)) AS link"),
                ])
                ->from('exam_appeal')
                ->leftJoin('teacher_access', 'teacher_access.id = exam_appeal.teacher_access_id')
                ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
                ->leftJoin('exam_student', 'exam_student.id = exam_appeal.exam_student_id')
                ->leftJoin('user_access', 'user_access.user_id = teacher_access.user_id AND user_access.user_access_type_id = 2')
                ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = "uz" AND translate.table_name = "kafedra"')
                ->leftJoin('translate AS tr_exam', 'tr_exam.model_id = exam_appeal.exam_id AND tr_exam.language = "uz" AND tr_exam.table_name = "exam"')
                ->where([
                    'exam_appeal.type' => null,
                    'exam_student.act' => 0,
                ])
                ->groupBy([
                    'profile.user_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    'exam_appeal.exam_id',
                ])
                ->orderBy([
                    'soni' => SORT_DESC,
                ]);


            $command = $query->createCommand();
            $data = $command->queryAll();
            return $data;
        }


        if (Yii::$app->request->get('teacher') == 1) {
            $query = (new Query())
                ->select([
                    'translate.NAME AS kafedra_name',
                    'user_access.table_id as kafedra_id',
                    'profile.user_id',
                    // 'exam_student.teacher_access_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    // 'examtr.NAME AS exam_name',
                    // 'exam_student.exam_id',
                    'SUM(CASE WHEN exam_student.is_checked_full = 0 AND exam_student.act <> 1 AND exam_student.has_answer = 1 THEN 1 ELSE 0 END) AS tekshirmagan',
                    'SUM(CASE WHEN exam_student.act = 1 THEN 1 ELSE 0 END) AS akt_soni',
                    'SUM(CASE WHEN exam_student.has_answer = 0 THEN 1 ELSE 0 END) AS yozilmagan',
                    'COUNT(*) AS jami'
                ])
                ->from('exam_student')
                ->leftJoin('teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
                // ->leftJoin('translate AS examtr', 'examtr.model_id = exam_student.exam_id AND examtr.language = "uz" AND examtr.table_name = "exam"')
                ->leftJoin('user_access', 'teacher_access.user_id = user_access.user_id AND user_access.user_access_type_id = 2')
                ->leftJoin('exam', 'exam.id = exam_student.exam_id')
                ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
                ->leftJoin('subject', 'subject.id = exam_student.subject_id')
                ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = "uz" AND translate.table_name = "kafedra"')
                // ->where(['teacher_access.user_id' => 4228])
                ->andWhere(['exam.archived' => 0])
                ->andWhere(['exam.is_deleted' => 0])
                ->groupBy([
                    'translate.NAME',
                    'user_access.table_id',
                    // 'examtr.NAME',
                    // 'exam_student.exam_id',
                    'profile.user_id',
                    // 'exam_student.teacher_access_id',
                    'profile.last_name',
                    'profile.middle_name',
                    'profile.first_name'
                ])
                ->orderBy(['tekshirmagan' => SORT_DESC]);
        } else {
            $query = (new Query())
                ->select([
                    'translate.NAME AS kafedra_name',
                    'user_access.table_id as kafedra_id',
                    'profile.user_id',
                    'exam_student.teacher_access_id',
                    'exam.checking_at',
                    'exam.checking_until',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    'examtr.NAME AS exam_name',
                    'exam_student.exam_id',
                    'SUM(CASE WHEN exam_student.is_checked_full = 0 AND exam_student.act = 0 AND exam_student.has_answer = 1 THEN 1 ELSE 0 END) AS tekshirmagan',
                    'SUM(CASE WHEN exam_student.act != 0 THEN 1 ELSE 0 END) AS akt_soni',
                    'SUM(CASE WHEN exam_student.has_answer = 0 THEN 1 ELSE 0 END) AS yozilmagan',
                    'COUNT(*) AS jami'
                ])
                ->from('exam_student')
                ->leftJoin('teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
                ->leftJoin('translate AS examtr', 'examtr.model_id = exam_student.exam_id AND examtr.language = "uz" AND examtr.table_name = "exam"')
                ->leftJoin('user_access', 'teacher_access.user_id = user_access.user_id AND user_access.user_access_type_id = 2')
                ->leftJoin('exam', 'exam.id = exam_student.exam_id')
                ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
                ->leftJoin('subject', 'subject.id = exam_student.subject_id')
                ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = "uz" AND translate.table_name = "kafedra"')
                // ->where(['teacher_access.user_id' => 4228])
                ->groupBy([
                    'translate.NAME',
                    'examtr.NAME',
                    'exam_student.exam_id',
                    'profile.user_id',
                    'exam_student.teacher_access_id',
                    'profile.last_name',
                    'profile.middle_name',
                    'profile.first_name'
                ])
                ->orderBy(['tekshirmagan' => SORT_DESC]);
        }

        $query->andWhere(['exam.archived' => 0]);


        $query->andWhere(["!=", 'profile.user_id',  11371]);
        $query->andWhere(['exam.is_deleted' => 0]);

        if (isRole('mudir')) {
            $query->andWhere(['user_access.table_id' => ResourcesUser::getKafedraId(current_user_id())]);
        }

        // To execute the query and get the results
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }


    public function actionExamCheckinguntil()
    {
        // Subquery
        $subquery = (new Query())
            ->select([
                'exam_student.teacher_access_id AS teacher_access_id',
                'exam_student.exam_id AS exam_id',
                'exam.checking_at AS checking_at',
                'exam.checking_until AS checking_until',
                'COUNT(0) AS `count(*)`'
            ])
            ->from('exam_student')
            ->leftJoin('exam', 'exam.id = exam_student.exam_id')
            ->where([
                'exam_student.id' => (new Query())
                    ->select('exam_student_answer.exam_student_id')
                    ->from('exam_student_answer')
                    ->where([
                        'exam_student_answer.id' => (new Query())
                            ->select('exam_student_answer_sub_question_chala.exam_student_answer_id')
                            ->from('exam_student_answer_sub_question_chala')
                    ]),
                'exam_student.act' => 0,
                'exam_student.archived' => 0
            ])
            ->andWhere(['exam.archived' => 0])
            ->andWhere(['exam.is_deleted' => 0])
            ->groupBy(['exam_student.teacher_access_id', 'exam_student.exam_id']);

        // Main Query
        $query = (new Query())
            ->select([
                'teacher_profile.user_id',
                'teacher_profile.last_name',
                'teacher_profile.first_name',
                'teacher_profile.middle_name',
                'subq.`count(*)` AS soni',
                'subq.exam_id',
                'subq.checking_at',
                'subq.checking_until',
                'exam_tr.name AS exam_name',
                'user_access.table_id AS kafedra_id',
                'translate.name AS kafedra'
            ])
            ->from('teacher_access')
            ->leftJoin('profile AS teacher_profile', 'teacher_access.user_id = teacher_profile.user_id')
            ->leftJoin(['subq' => $subquery], 'subq.teacher_access_id = teacher_access.id')
            ->leftJoin('user_access', 'user_access.user_id = teacher_access.user_id AND user_access.user_access_type_id = 2')
            ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = "uz" AND translate.table_name = "kafedra"')
            ->leftJoin(['exam_tr' => 'translate'], 'exam_tr.model_id = subq.exam_id AND exam_tr.language = "uz" AND exam_tr.table_name = "exam"')
            ->where(['not', ['subq.teacher_access_id' => null]])
            ->andWhere(['<', 'subq.checking_until', new \yii\db\Expression('CURDATE()')])

            ->andWhere(["!=", 'teacher_profile.user_id',  11371])
            ->groupBy([
                'teacher_profile.user_id',
                'teacher_profile.last_name',
                'teacher_profile.first_name',
                'teacher_profile.middle_name',
                'translate.name',
                'subq.exam_id',
                'exam_tr.name',
                'user_access.table_id',
                'subq.`count(*)`'
            ]);

        if (isRole('mudir')) {
            $query->andWhere(['user_access.table_id' => ResourcesUser::getKafedraId(current_user_id())]);
        }


        // To execute the query and get the results
        $command = $query->createCommand();
        $data = $command->queryAll();

        return $data;
    }

    public function actionExamCheckinguntilTeacher()
    {
        $query = (new Query())
            ->select([
                'translate.name AS kafedra_name',
                'user_access.table_id AS kafedra_id',
                'profile.user_id',
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                "SUM(CASE WHEN exam.checking_until < CURDATE() AND exam_student.is_checked_full = 0 AND exam_student.act <> 1 AND exam_student.has_answer = 1 THEN 1 ELSE 0 END) AS tekshirmagan_vaqt",
                'SUM(CASE WHEN exam_student.is_checked_full = 0 AND exam_student.act <> 1 AND exam_student.has_answer = 1 THEN 1 ELSE 0 END) AS tekshirmagan',
                'SUM(CASE WHEN exam_student.act = 1 THEN 1 ELSE 0 END) AS akt_soni',
                'SUM(CASE WHEN exam_student.has_answer = 0 THEN 1 ELSE 0 END) AS yozilmagan',
                'COUNT(*) AS jami'
            ])
            ->from('exam_student')
            ->leftJoin('teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
            ->leftJoin('user_access', 'teacher_access.user_id = user_access.user_id AND user_access.user_access_type_id = 2')
            ->leftJoin('exam', 'exam.id = exam_student.exam_id')
            ->leftJoin('profile', 'profile.user_id = teacher_access.user_id')
            ->leftJoin('subject', 'subject.id = exam_student.subject_id')
            ->leftJoin('translate', 'translate.model_id = user_access.table_id AND translate.language = "uz" AND translate.table_name = "kafedra"')
            ->where(['exam.archived' => 0])
            ->andWhere(['exam.archived' => 0])
            ->andWhere(['exam.archived' => 0])
            ->andWhere(["!=", 'profile.user_id',  11371])
            ->groupBy([
                'translate.name',
                'user_access.table_id',
                'profile.user_id',
                'profile.last_name',
                'profile.middle_name',
                'profile.first_name'
            ])

            ->orderBy(['tekshirmagan' => SORT_DESC]);

        if (isRole('mudir')) {
            $query->andWhere(['user_access.table_id' => ResourcesUser::getKafedraId(current_user_id())]);
        }

        // Execute the query and get the results
        $data = $query->all();

        return $data;
    }

    public function actionExamNotdis()
    {
        if (Yii::$app->request->get('appeal') == 1) {
            $query = (new Query())
                ->select([
                    'exam_appeal.exam_id',
                    'exam.name AS exam_name',
                    'COUNT(*) AS soni'
                ])
                ->from('exam_appeal')
                ->leftJoin('translate AS exam', 'exam.model_id = exam_appeal.exam_id AND exam.language = "uz" AND exam.table_name = "exam"')
                ->where(['teacher_access_id' => null])

                ->groupBy(['exam_appeal.exam_id', 'exam.name'])
                ->orderBy(['exam_appeal.exam_id' => SORT_DESC]);

            // To execute the query and get the results
            $command = $query->createCommand();
            $data = $command->queryAll();

            return $data;
        } else {
            $query = (new Query())
                ->select([
                    'exam_student.exam_id',
                    'examtr.name AS exam_name',
                    'COUNT(*) AS soni'
                ])
                ->from('exam_student')
                ->leftJoin('translate AS examtr', 'examtr.model_id = exam_student.exam_id AND examtr.language = "uz" AND examtr.table_name = "exam"')
                ->leftJoin('exam', 'exam.id = exam_student.exam_id')
                ->where(['teacher_access_id' => null])
                ->andWhere(['exam.archived' => 0])
                ->andWhere(['exam.is_deleted' => 0])
                ->andWhere(['exam_student.type' => null])
                ->groupBy(['exam_student.exam_id', 'examtr.name'])
                ->orderBy(['exam_student.exam_id' => SORT_DESC]);

            // To execute the query and get the results
            $command = $query->createCommand();
            $data = $command->queryAll();

            return $data;
        }
    }

    public function actionStudentTimeTable()
    {
        if (Yii::$app->request->get('not_select') == 1) {

            $query = (new Query())
                ->select([
                    'student.id',
                    'student.course_id',
                    'student.faculty_id',
                    'student.edu_plan_id',
                    'student.edu_lang_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    'profile.passport_pin',
                    'fac.name AS faculty_name',
                    'eduplan.name AS edu_plan_name'
                ])
                ->from('student')
                // ->leftJoin('student_time_table', 'student.id = student_time_table.student_id')
                ->leftJoin('student_time_option', 'student.id = student_time_option.student_id and student_time_option.archived = 0')
                ->leftJoin('profile', 'student.user_id = profile.user_id')
                ->leftJoin('translate fac', "fac.model_id = student.faculty_id AND fac.table_name = 'faculty' AND fac.language = 'uz'")
                ->leftJoin('translate eduplan', "eduplan.model_id = student.edu_plan_id AND eduplan.table_name = 'edu_plan' AND eduplan.language = 'uz'");

            $query->andWhere(['<>', 'student.faculty_id', 5])
                ->andWhere(['in', 'student.course_id', [1, 2, 3, 4]])
                // ->andWhere(['<>', 'student.course_id', 4])
                ->andWhere(['<>', 'student.is_deleted', 1]);

            if (isRole("dean")) {
                $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
                if ($t['status'] == 1) {
                    $query->andWhere(['student.faculty_id' => $t['UserAccess']->table_id]);
                }
            }

            if (Yii::$app->request->get('faculty_id')) {
                $query->andWhere(['student.faculty_id' => Yii::$app->request->get('faculty_id')]);
            }
            if (Yii::$app->request->get('edu_plan_id')) {
                $query->andWhere(['student.edu_plan_id' => Yii::$app->request->get('edu_plan_id')]);
            }
            if (Yii::$app->request->get('course_id')) {
                $query->andWhere(['student.course_id' => Yii::$app->request->get('course_id')]);
            }


            // To execute the query and get the results
            $command = $query->createCommand();
            $data = $command->queryAll();

            return $data;
        } else {

            $query = (new Query())
                ->select([
                    'student.id',
                    'student.course_id',
                    'student.faculty_id',
                    'student.edu_plan_id',
                    'student.edu_lang_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    'profile.passport_pin',
                    'fac.name AS faculty_name',
                    'eduplan.name AS edu_plan_name',
                    'COUNT(*) AS soni',
                ])
                ->from('student')
                ->leftJoin('student_time_table', 'student.id = student_time_table.student_id')
                ->leftJoin('profile', 'student.user_id = profile.user_id')
                ->leftJoin('translate fac', "fac.model_id = student.faculty_id AND fac.table_name = 'faculty' AND fac.language = 'uz'")
                ->leftJoin('translate eduplan', "eduplan.model_id = student.edu_plan_id AND eduplan.table_name = 'edu_plan' AND eduplan.language = 'uz'")
                ->where([
                    'student_time_table.archived' => 0,
                ]);

            $query->andWhere(['<>', 'student.faculty_id', 5])
                ->andWhere(['in', 'student.course_id', [1, 2, 3, 4]])
                // ->andWhere(['<>', 'student.course_id', 4])

                ->andWhere(['<>', 'student.is_deleted', 1]);

            if (isRole("dean")) {
                $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
                if ($t['status'] == 1) {
                    $query->andWhere(['student.faculty_id' => $t['UserAccess']->table_id]);
                }
            }

            if (Yii::$app->request->get('faculty_id')) {
                $query->andWhere(['student.faculty_id' => Yii::$app->request->get('faculty_id')]);
            }
            if (Yii::$app->request->get('edu_plan_id')) {
                $query->andWhere(['student.edu_plan_id' => Yii::$app->request->get('edu_plan_id')]);
            }
            if (Yii::$app->request->get('course_id')) {
                $query->andWhere(['student.course_id' => Yii::$app->request->get('course_id')]);
            }

            $query->groupBy([
                'student.id',
                'student.course_id',
                'student.faculty_id',
                'student.edu_plan_id',
                'student.edu_lang_id',
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                'profile.passport_pin',
                'fac.name',
                'eduplan.name'
            ]);

            $query->having([
                'NOT',
                [
                    'OR',
                    ['AND', ['student.course_id' => 1], ['soni' => 24]],
                    ['AND', ['student.course_id' => 2], ['soni' => 24]],
                    ['AND', ['student.course_id' => 3], ['soni' => 20]],
                    ['AND', ['student.course_id' => 4], ['soni' => 16]],
                ]
            ]);

            // // To get the raw SQL, you can use:
            // $sql = $query->createCommand()->getRawSql();

            // // To execute the query and get the result, you can use:
            // $students = $query->all();

            // To execute the query and get the results
            $command = $query->createCommand();
            $data = $command->queryAll();

            return $data;
        }
    }
}
