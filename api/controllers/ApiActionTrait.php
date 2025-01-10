<?php

namespace api\controllers;

use api\components\HttpBearerAuth;
use app\components\AuthorCheck;
use app\components\PermissonCheck;
use base\ResponseStatus;
use common\models\model\ActionLog;
use common\models\model\AuthChild;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use common\models\model\TeacherAccess;
use common\models\model\UserAccess;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;

trait ApiActionTrait
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        // User role ining joriy action uchun ruxsati bor yoki yo'qligini tekshiradi.
        $behaviors['permissionCheck'] = [
            'class' => PermissonCheck::class,
            'permission' => $this->getPermission(),
            'allowedRoles' => $this->getAllowedRoles(),
            'referenceControllers' => $this->getReferenceControllers(),
        ];

        // Userning joriy actionga ruxsati bor, lekin mualliflik huquqi bor yoki yo'qligini tekshiradi
        $behaviors['authorCheck'] = [
            'class' => AuthorCheck::class,
        ];


        return $behaviors;
    }

    public function getPermission()
    {
        return Yii::$app->controller->id . '_' . Yii::$app->controller->action->id;
    }

    public function getAllowedRoles()
    {
        return [];
    }

    public function getReferenceControllers()
    {
        return [
            'nationality',
            'language',
            'science-degree',
            'scientific-title',
            'special-title',
            'basis-of-learning',
            'residence-type',
        ];
    }
    /**
     * After action
     *
     * @param $action
     * @return void
     */

    public function afterAction($action, $result)
    {
        if (Yii::$app->params['mkStatusLogging']) {
            $data = [];

            $data['data'] = null;
            $data['errors'] = null;
            $data['status'] = isset($result['status']) ? $result['status'] : 'Failed';
            $data['message'] = isset($result['message']) ? $result['message'] : 'Failed';
            $data['browser'] = json_encode(getBrowser());
            $data['host'] = get_host();

            $data['controller'] = Yii::$app->controller->id;
            $data['action'] = Yii::$app->controller->action->id;
            $data['method'] = $_SERVER['REQUEST_METHOD'];
            $data['get_data'] = json_encode(Yii::$app->request->get() ?? null);
            $data['post_data'] = json_encode(Yii::$app->request->post() ?? null);

            if (isset($result['errors'])) {
                $data['errors'] = json_encode($result['errors']);
            }

            if (isset($result['data'])) {
                $data['data'] = json_encode($result['data']);
            }
            $data['created_at'] = time();
            $data['user_id'] = current_user_id();

            $year = '@api/web/'. 'logs'  ."/year-" . date("Y");
            $month = $year . '/month-'.date("m");
            $day = $month. '/day-'.date('d');
            $logFilePath = $day."/log-" . date("Y-m-d") . ".json";

            if (!file_exists(\Yii::getAlias($year))) {
                mkdir(\Yii::getAlias($year), 0777, true);
            }
            if (!file_exists(\Yii::getAlias($month))) {
                mkdir(\Yii::getAlias($month), 0777, true);
            }
            if (!file_exists(\Yii::getAlias($day))) {
                mkdir(\Yii::getAlias($day), 0777, true);
            }

//             $myfile = fopen(\Yii::getAlias($logFilePath), "a+");
//             fwrite($myfile, "," . json_encode($data));
//             fclose($myfile);

            $logData = json_encode($data);
            file_put_contents(\Yii::getAlias($logFilePath), "," . $logData, FILE_APPEND | LOCK_EX);
        }

        $user = current_user();
        if ($user) {
            $user->last_seen_time = time();
            $user->save(false);
        }


        $result = parent::afterAction($action, $result);
        return $result;
    }

//    public function afterAction($action, $result)
//    {
//        $data = [];
//        $data['user_id'] = current_user_id();
//        $data['data'] = null;
//        $data['errors'] = null;
//        $data['status'] = isset($result['status']) ? $result['status'] : 'Failed';
//        $data['message'] = isset($result['message']) ? $result['message'] : 'Failed';
//        $data['browser'] = json_encode(getBrowser());
//        $data['host'] = get_host();
//
//        $data['controller'] = Yii::$app->controller->id;
//        $data['action'] = Yii::$app->controller->action->id;
//        $data['method'] = $_SERVER['REQUEST_METHOD'];
//        $data['get_data'] = json_encode(Yii::$app->request->get() ?? null);
//        $data['post_data'] = json_encode(Yii::$app->request->post() ?? null);
//
//        if (isset($result['errors'])) {
//            $data['errors'] = json_encode($result['errors']);
//        }
//        $result = parent::afterAction($action, $result);
//        if (isset($result['data'])) {
//            $data['data'] = json_encode($result['data']);
//        }
//        $data['created_on'] = date("Y-m-d H:i:s");
//        $myfile = fopen("Log-" . date("Y-m-d") . ".json", "a+");
//        fwrite($myfile, "," . json_encode($data));
//        fclose($myfile);
//        return $result;
//    }

//     public function afterAction($action, $result)
//     {
//
//         // vdd(Yii::$app->request);
//         // vdd(get_host());
//         // vdd(getIpAddressData());
//
//         $action_log = Yii::$app->session->get('action_log');
//         $action_log->user_id = current_user_id();
//         $action_log->status = isset($result['status']) ? $result['status'] : 'Failed';
//         $action_log->message = isset($result['message']) ? $result['message'] : 'Failed';
//         $action_log->browser = json_encode(getBrowser());
//         // $action_log->ip_address = getIpMK();
//         $action_log->host = get_host();
//         // $action_log->ip_address_data = json_encode(getIpAddressData());
//
//         if (isset($result['errors'])) {
//             $action_log->errors = json_encode($result['errors']);
//         }
//         $result = parent::afterAction($action, $result);
//
//         /*  if (isset($result['data'])) {
//             $action_log->data = json_encode($result['data']);
//         } */
//
//         $action_log->created_on = date("Y-m-d H:i:s");
//         $action_log->save(false);
//         // dd(json_encode($result));
//         return $result;
//     }

    /**
     * Before action
     *
     * @param $action
     * @return void     */
    public function beforeAction($action)
    {
        $this->generate_access_key();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->check_access_key()) {
            $data = json_output();
            $data['message'] = 'Incorrect token key!';
            $this->asJson($this->response(0, _e('Incorrect token key! MK'), null, null, ResponseStatus::UNAUTHORIZED));
            return false;
        }

        //   echo "Please wait!!"; die(); return 0;

        $lang = Yii::$app->request->get('lang');

        $languages = get_languages();
        $langCodes = [];
        foreach ($languages as $itemLang) {
            $langCodes[] = $itemLang['lang_code'];
        }
        if (!in_array($lang, $langCodes)) {
            $this->asJson($this->response(0, _e('Wrong language code selected (' . $lang . ').'), null, null, ResponseStatus::UPROCESSABLE_ENTITY));
        } else {


            // dd("asdasd");
            // vdd(Yii::$app->request->get());
            // vdd(Yii::$app->request->post());

            // $action_log = new ActionLog();
            // $action_log->user_id = current_user_id();
            // $action_log->controller = Yii::$app->controller->id;
            // $action_log->action = Yii::$app->controller->action->id;
            // $action_log->method = $_SERVER['REQUEST_METHOD'];
            // $action_log->get_data = json_encode(Yii::$app->request->get());
            // $action_log->post_data = json_encode(Yii::$app->request->post());
            // $action_log->save(false);
            // Yii::$app->session->set('action_log', $action_log);

            // dd(current_user_id());
            Yii::$app->language = $lang;
            return parent::beforeAction($action);
        }
    }


    /**
     * Generate api access key
     *
     * @return void
     */
    public function generate_access_key()
    {
        $api_salt_key = API_SALT_KEY;
        $api_secret_key = API_SECRET_KEY;
        $api_token = $api_salt_key . '-' . $api_secret_key;

        $date1 = gmdate('Y-m-d H:i', strtotime('+1 min'));
        $date2 = gmdate('Y-m-d H:i', strtotime('+2 min'));

        $generated_key_1 = md5($api_token . $date1);
        $generated_key_2 = md5($api_token . $date2);

        $this->token_key = $generated_key_1;
        $this->token_keys = array($generated_key_1, $generated_key_2);
    }

    /**
     * Check api access key
     *
     * @return void
     */

    private function check_access_key()
    {
        return true;
        $token = '';
        $headers = Yii::$app->request->headers;
        $header_token = $headers->get('api-token');
        $param_token = Yii::$app->request->get('token');

        if ($header_token && is_string($header_token)) {
            $token = $header_token;
        }

        if ($param_token && is_string($param_token)) {
            $token = $param_token;
        }

        if (YII_DEBUG && $token == API_MASTER_KEY) {
            return true;
        } elseif ($token && in_array($token, $this->token_keys)) {
            return true;
        }
        return false;
    }

    public function mainUsers()
    {

    }

    public function filterAll($query, $model)
    {
        $filter = Yii::$app->request->get('filter');
        $queryfilter = Yii::$app->request->get('filter-like');

        if (isset($filter)) {
            $filter = json_decode(str_replace("'", "", $filter));
            if (isset($filter)) {
                foreach ($filter as $attribute => $id) {
                    if (in_array($attribute, $model->attributes())) {
                        if ($attribute == "second_building_id") {
                            $query = $query->andFilterWhere([$model->tableName() . '.building_id' => $id]);
                        } elseif (!($attribute == "status" && $id == "all")) {
                            $query = $query->andFilterWhere([$model->tableName() . '.' . $attribute => $id]);
                        }
                    }
                }
            }
        }

        if (isset($queryfilter)) {
            $queryfilter = json_decode(str_replace("'", "", $queryfilter));
            if (isset($queryfilter)) {
                foreach ($queryfilter as $attributeq => $word) {
                    if (in_array($attributeq, $model->attributes())) {
                        $query = $query->andFilterWhere(['like', $model->tableName() . '.' . $attributeq, '%' . $word . '%', false]);
                    }
                }
            }
        }
        return $query;
    }


    public function filterAnotherTable($query, $model)
    {
        $filter = Yii::$app->request->get('group-filter');
        if (isset($filter)) {
            $filter = json_decode(str_replace("'", "", $filter));
            if (isset($filter)) {
                foreach ($filter as $attribute => $id) {
                    if (in_array($attribute, $model->attributes())) {
                        $query = $query->andFilterWhere([$model->tableName() . '.' . $attribute => $id]);
                    }
                }
            }
        }
        return $query;
    }


    public function filter($query, $model)
    {
        $filterEduYear = Yii::$app->request->get('edu_year_id');
        $filterType = Yii::$app->request->get('type');
//        $filterCourse = Yii::$app->request->get('course');

        if (isset($filterEduYear)) {
            $query = $query->andFilterWhere([$model->tableName().'.edu_year_id' => $filterEduYear]);

            if (isset($filterType)) {
                $query = $query->andWhere(['fall_spring' => $filterType]);
            }

        }

        return $query;

    }


    public function sort($query)
    {
        if (Yii::$app->request->get('sort')) {

            $sortVal = Yii::$app->request->get('sort');
            if (substr($sortVal, 0, 1) == '-') {
                $sortKey = SORT_DESC;
                $sortField = substr($sortVal, 1);
            } else {
                $sortKey = SORT_ASC;
                $sortField = $sortVal;
            }

            $query->orderBy([$sortField => $sortKey]);
        };
        return $query;
    }


    public function getData($query, $perPage = 20, $validatePage = true)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'totalCount' => $query->count(),
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);
    }


    public function getDataNoPage($query, $perPage = 0, $validatePage = true)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);
    }


    public function response($status, $message, $data = null, $errors = null, $responsStatusCode = 200)
    {
        Yii::$app->response->statusCode = $responsStatusCode;
        $response = [
            'status' => $status,
            'message' => $message
        ];
        if ($data) $response['data'] = $data;
        if ($errors) $response['errors'] = $errors;
        return $response;
    }


    public function viewIsSelf($role , $model)
    {
        $t = false;
        $data = ['admin' , 'edu_admin' , 'rector'];
        foreach ($data as $mainRole) {
            if ($mainRole == $role) {
                $t = true;
                break;
            }
        }

        if (isRole('admin')) {
            $childs =
                AuthChild::find()
                    ->andWhere(['parent' => $role])
                    ->where(['in', 'child', current_user_roles_array($model->id)])
                    ->count();

            $parents =
                AuthChild::find()
                    ->where(['child' => $role])
                    ->andWhere(['in' , 'parent' , current_user_roles_array($model->id)])
                    ->count();

            if ($childs > 0 && $parents == 0) {
                $t = true;
            }

            if (isRole('dean')) {
                $dean = get_dean();
            }


        }


    }

    public function load($model, $data)
    {
        return $model->load($data, '');
    }


    public function checkLead($model, $role)
    {
        $user_id = current_user_id();
        $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

        if (property_exists($roles, $role)) {
            if ($model->user_id != $user_id) {
                return false;
            }
        }
        return true;
    }


    public function teacher_access($type = null, $select = [], $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }

        if (is_null($type)) {
            $type = 1;
        }

        if (empty($select)) {
            $select = ['id'];
        }

        if ($type == 1) {
            return TeacherAccess::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andFilterWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')
                ])
                ->select($select);
        } elseif ($type == 2) {
            return TeacherAccess::find()
                ->asArray()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')
                ])
                ->select($select)
                ->all();
        }
    }


    public function subject_ids($type = null, $select = [], $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }

        if (is_null($type)) {
            $type = 1;
        }

        if (empty($select)) {
            $select = ['id'];
        }

        if (isRole("mudir", $user_id)) {
            return Subject::find()
                ->where(['is_deleted' => 0])
                ->where(['in', 'kafedra_id', Kafedra::find()
                    ->where(['is_deleted' => 0, 'user_id' => $user_id])
                    ->select('id')])
                ->select($select);
        }

        if (isRole("teacher", $user_id)) {
            return Subject::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['in', 'subject_id', TeacherAccess::find()
                    ->where(['user_id' => $user_id, 'is_deleted ' => 0])
                    ->andWhere(['in', 'subject_id', Subject::find()
                        ->where(['is_deleted' => 0])
                        ->select('id')])
                    ->select(['subject_id'])])
                ->select($select);
        }


        return Subject::find()
            ->where(['is_deleted' => 0])
            ->select($select);


        if ($type == 1) {
            return TeacherAccess::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')])
                ->select($select);
        } elseif ($type == 2) {
            return TeacherAccess::find()
                ->asArray()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')])
                ->select($select)

                ->all();
        }

        return null;
    }

    public function isSelf($userAccessTypeId, $type = null)
    {
        if (is_null($type)) {
            $type = 1;
        }

        $user_id = current_user_id();
        $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

        $userAccess = [];
        if ($type == 2) {
            $userAccessQuery = UserAccess::find()
                ->where([
                    'user_id' => $user_id,
                    'user_access_type_id' => $userAccessTypeId,
                    'is_leader' => UserAccess::IS_LEADER_TRUE,
                    'status' => 1,
                    'is_deleted' => 0
                ])->one();
                if ($userAccessQuery) {
                    $userAccess[] = $userAccessQuery->table_id;
                } else {
                    $userAccess[] = 0;
                }
        } else {
            $userAccessQuery = UserAccess::find()
                ->select('table_id')
                ->where([
                'user_id' => $user_id,
                'user_access_type_id' => $userAccessTypeId,
                'status' => 1,
                'is_deleted' => 0
            ])
                ->groupBy('table_id')
                ->asArray()->all();

            foreach ($userAccessQuery as $value) {
                $userAccess[] = $value['table_id'];
            }
        }

        $t['status'] = 3;

        foreach (_eduRoles() as $eduRole) {
            if (property_exists($roles, $eduRole)) {
                return $t;
            }
        }

        if (property_exists($roles, 'hr')) {
            return $t;
        }

        if (property_exists($roles, 'kpi_check')) {
            return $t;
        }

        if (property_exists($roles, 'hostel')) {
            return $t;
        }

        if (property_exists($roles, 'rector')) {
            return $t;
        }

        if (property_exists($roles, 'edu_quality')) {
            return $t;
        }

        if (property_exists($roles, 'test_department')) {
            return $t;
        }

        if (property_exists($roles, 'student_internship_department')) {
            return $t;
        }


        if (property_exists($roles, 'corruption')) {
            return $t;
        }

        if (property_exists($roles, 'justice')) {
            return $t;
        }

        if (property_exists($roles, 'prorector')) {
            return $t;
        }

        if (count($userAccess) > 0 && !(property_exists($roles, 'admin'))) {
            $t['status'] = 1;
            $t['UserAccess'] = $userAccess;
            return $t;
        } elseif (!property_exists($roles, 'admin')) {
            $t['status'] = 2;
            return $t;
        }

        return $t;
    }

    public function isMine($userAccessTypeId, $tableId, $userId)
    {
        $userAccess = UserAccess::find()
            ->where([
                'user_id' => $userId,
                'user_access_type_id' => $userAccessTypeId,
                'table_id' => $tableId,
                'is_deleted' => 0
            ])
            ->exists();
        if ($userAccess) {
            return true;
        }
        return false;
    }

    public static function student($type = null, $user_id = null)
    {
        if ($user_id == null) {
            $user_id = current_user_id();
        }
        if ($type == null) {
            $type = 1;
        }
        $student = Student::findOne(['user_id' => $user_id]);
        if ($type == 1) {
            return  $student->id ?? null;
        } elseif ($type == 2) {
            return  $student ?? null;
        }
    }
}
