<?php

namespace api\controllers;

// use common\models\model\Test;

use api\components\MipServiceMK;
use api\components\TurniketMK;
use api\resources\User;
use base\ResponseStatus;
use common\models\model\Department;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentTurniket;
use common\models\model\Turniket;
use common\models\model\TurniketData;
use common\models\model\UserAccess;
use common\models\model\UserAccessType;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT;
use Yii;
use yii\db\Expression;
use yii\helpers\Console;
use yii\rest\ActiveController;

class OpenController extends ActiveController
{
    use ApiOpen;
    public $modelClass = 'api\resources\Test';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $data = [];

        $users = User::find()
            ->select('users.id')
            ->innerJoin('profile', 'users.id = profile.user_id')
            // ->innerJoin('student', 'users.id = student.user_id')
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['users.status' => 10, 'users.deleted' => 0])
            // ->andWhere(['not like', 'users.username', '%std%'])
            // ->andWhere(['!=', 'student.status', 10])
            // ->andWhere(['not in', 'student.course_id', [1, 2, 3, 4, 9]])
            ->andWhere(['=', 'auth_assignment.item_name', 'teacher'])
            ->andWhere(['!=', 'auth_assignment.item_name', 'student'])
            ->andWhere(['profile.checked_full' => 1])
            ->andWhere(['IS NOT', 'profile.passport_pin', null])
            ->andWhere(['IS', 'profile.turniket_id', null])
            // ->orderBy(new Expression('rand()'))
            ->column();
        // dd($users);


        foreach ($users as $user_id) {

            $profile = Profile::findOne(['user_id' => $user_id]);
            $user_access = UserAccess::findOne(['user_id' => $user_id, 'is_deleted' => 0, 'user_access_type_id' => 2]);

            if ($user_access) {
                $user_access_type = UserAccessType::findOne($user_access->user_access_type_id);
                $turniket_department_id = $user_access_type ? $user_access_type->table_name::findOne($user_access->table_id)->turniket_department_id : null;
            }

            if (isset($turniket_department_id)) {
                $data[$profile->passport_pin] = [
                    TurniketMK::addPersonByDepartment($profile, $turniket_department_id),
                    TurniketMK::addAccessPerson($profile)
                ];
            }

            $data[$profile->passport_pin] = [
                TurniketMK::addPerson($profile),
                TurniketMK::addAccessPerson($profile)
            ];
        }

        return $data;
    }

    public function actionIndex1($lang)
    {
        // $studentTurniket = StudentTurniket::find()->all();
        $studentTurniket = StudentTurniket::find()->orderBy(new Expression('rand()'))->all();
        $data = [];
        foreach ($studentTurniket as $studentTurniketone) {
            $students = Student::find()
                ->select('student.user_id')
                ->innerJoin('profile', 'student.user_id = profile.user_id')
                ->where(['student.faculty_id' => $studentTurniketone->faculty_id])
                ->andWhere(['student.edu_type_id' => $studentTurniketone->edu_type])
                ->andWhere(['student.course_id' => $studentTurniketone->course_id])
                ->andWhere(['profile.checked_full' => 1])
                ->andWhere(['profile.turniket_id' => null])
                // ->andWhere(['IS NOT', 'profile.passport_pin', null])
                ->column();

            foreach ($students as $student_id) {
                $profile = Profile::findOne(['user_id' => $student_id]);

                if ($profile && isset($profile->passport_pin) && !$profile->turniket_id > 0) {
                    $data[$student_id] = [
                        TurniketMK::addPersonByDepartment($profile, $studentTurniketone->turniket_department_id),
                        TurniketMK::addAccessPerson($profile)
                    ];
                }
            }
        }

        return $data;
    }


    public function actionIndexDep($lang)
    {

        return 1;
        $model = new Faculty();
        $data = [];
        $query = $model->find()
            ->andWhere(['is_deleted' => 0])->all();

        foreach ($query as $one) {
            $data[$one->id] = TurniketMK::addDep($one, $one->translate->name, "16");
        }

        return $data;
    }

    public function actionTurniket($lang)
    {
        $postData = Yii::$app->request->post() ?? null;

        $main_path = MAIN_STORAGE_PATH . 'turniket_log';

        $year = $main_path . "/year-" . date("Y");
        $month = $year . '/month-' . date("m");
        $day = $month . '/day-' . date('d');
        // $logFilePath = $day . "/log-" . date("Y-m-d") . ".json";
        // $logFilePath = $day . "/log-" . date("Y-m-d") . ".json";

        if (!file_exists(\Yii::getAlias($year))) {
            mkdir(\Yii::getAlias($year), 0777, true);
        }
        if (!file_exists(\Yii::getAlias($month))) {
            mkdir(\Yii::getAlias($month), 0777, true);
        }
        if (!file_exists(\Yii::getAlias($day))) {
            mkdir(\Yii::getAlias($day), 0777, true);
        }

        // if (!file_exists(\Yii::getAlias($logFilePath))) {
        //     mkdir(\Yii::getAlias($logFilePath), 0777, true);
        // }

        $turniketFilePath = $day . "/turniket-" . date("Y-m-d") . ".json";
        file_put_contents(\Yii::getAlias($turniketFilePath), "," . json_encode($postData), FILE_APPEND | LOCK_EX);


        $postData = is_string($postData) ? json_decode($postData) : $postData;
        // return $postData['params']['events'][0]['data']['personId'];

        $turniketData = new TurniketData();
        $turniketData->data = $postData;
        $turniketData->turniket_id = $postData['params']['events'][0]['data']['personId'];
        $turniketData->passport_pin = $postData['params']['events'][0]['data']['personCode'];

        $turniketData->date = date("Y-m-d", strtotime($postData['params']['events'][0]['happenTime']));
        $turniketData->time = strtotime($postData['params']['events'][0]['happenTime']);
        $turniketData->reader = $postData['params']['events'][0]['data']['readerIndexCode'];
        $turniketData->in_out = (stripos($postData['params']['events'][0]['data']['readerName'], 'kirish') !== false) ? 1 : ((stripos($postData['params']['events'][0]['data']['readerName'], 'chiqish') !== false) ? 2 : 0);
        // $turniketData->user_id = $turniketData->profile->user_id;
        // $turniketData->type = (stripos($postData['params']['events'][0]['srcName'], 'ttj') !== false) ? 2 : 1;
        $turniketData->type = (strpos(strtolower($postData['params']['events'][0]['srcName']), 'ttj') !== false) ? 2 : 1;

        $turniketData->save(false);
        $turniket = Turniket::findOne([
            'turniket_id' => $turniketData->turniket_id,
            'date' => $turniketData->date,
            'type' => $turniketData->type
        ]);
        if ($turniket) {
            $turniket->go_out_time = $turniketData->time;
            // $turniket->user_id = $turniketData->profile->user_id;
        } else {
            $turniket = new Turniket();
            $turniket->turniket_id = $turniketData->turniket_id;
            $turniket->passport_pin = $turniketData->profile->passport_pin;
            $turniket->user_id = $turniket->profile->user_id;
            $turniket->date = $turniketData->date;
            $turniket->go_in_time = $turniketData->time;
            $turniket->type = $turniketData->type;
        }

        $turniket->save(false);
        return true;
    }

    public function actionCreate($lang)
    {
        $user_id = \Yii::$app->request->post('user_id');
        if (isset($user_id)) {
            $profile = Profile::findOne(['user_id' => $user_id]);
            // if ($profile->turniket_id > 0) {
            // return TurniketMK::addAccessPerson($profile);
            // } else {

            // return TurniketMK::addAccessPerson($profile);
            return [TurniketMK::addPerson($profile), TurniketMK::addAccessPerson($profile)];
            // }
            // return [TurniketMK::addPerson($profile), TurniketMK::addAccessPerson($profile)];
            // return;
        }
    }

    public function actionUpdate($lang, $id)
    {
        if (!$profile = Profile::findOne(['user_id' => $id])) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return [TurniketMK::addPerson($profile), TurniketMK::addAccessPerson($profile)];
    }


    public function actionView($lang, $id)
    {
        if (!$profile = Profile::findOne(['user_id' => $id])) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return TurniketMK::infoPerson($profile);
    }

    public function actionDelete($lang, $id)
    {
        if (!$profile = Profile::findOne(['user_id' => $id])) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($profile) {
            return TurniketMK::deletePerson($profile);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }


    public function actionGetPersonalData($lang)
    {
        $request = \Yii::$app->request;

        // 🔐 Basic auth tekshiruvi
        $authHeader = $request->headers->get('Authorization');
        $expected = 'Basic bWs6bWtAMTIz'; // base64(mk:mk@123)

        if (!$authHeader || $authHeader !== $expected) {
            \Yii::$app->response->statusCode = 401;
            return [
                'status' => 0,
                'message' => 'Unauthorized',
                'error' => ['auth' => 'Access denied.']
            ];
        }

        // 🔽 Asosiy logika
        $pinfl = $request->post('pinfl');
        if (!$pinfl) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $mip = MipServiceMK::getDataPin($pinfl);

        if ($mip['status']) {
            return $this->response(1, _e('Success'), $mip['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $mip['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
