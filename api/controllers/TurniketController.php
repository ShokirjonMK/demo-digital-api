<?php

namespace api\controllers;

// use common\models\model\Test;

use api\components\TurniketMK;
use api\resources\User;
use base\ResponseStatus;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\model\Department;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentTurniket;
use common\models\model\Turniket;
use common\models\model\UserAccess;
use common\models\model\UserAccessType;
use Yii;
use yii\helpers\Console;
use yii\rest\ActiveController;

class TurniketController extends ApiActiveController
{
    public $modelClass = 'api\resources\Test';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Turniket();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.turniket_id = turniket.turniket_id')
            ->join('LEFT JOIN', 'users', 'users.id = profile.user_id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            // ->andWhere(['users.deleted' => 1])
            // ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

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
        // order by id desc
        $query = $query->orderBy(['id' => SORT_DESC]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());
        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionAddDepartment($lang)
    {
        $model = new Faculty();
        $data = [];
        $query = $model->find()
            ->andWhere(['is_deleted' => 0])->all();

        foreach ($query as $one) {
            $data[$one->id] = TurniketMK::addDep($one, $one->translate->name, "16");
        }

        return $data;
    }

    public function actionGet($lang)
    {
        $picUrl = Yii::$app->request->get('picUrl');
        if (isset($picUrl)) {
            return TurniketMK::getPicture($picUrl);
        }
    }

    public function actionEvent($lang)
    {
        $data = [];
        $data['post_data'] = Yii::$app->request->post() ?? null;


        $filepath = MAIN_STORAGE_PATH . 'turniket';

        if (!file_exists(\Yii::getAlias($filepath))) {
            mkdir(\Yii::getAlias($filepath), 0777, true);
        }
        $turniketFilePath = $filepath . "/turniket-" . date("Y-m-d") . ".json";
        file_put_contents(\Yii::getAlias($turniketFilePath), "," . json_encode($data['post_data']), FILE_APPEND | LOCK_EX);
        return $data;
    }
    public function actionEvent1($lang)
    {
        $data = [];
        $data['post_data'] = Yii::$app->request->post() ?? null;


        $filepath = MAIN_STORAGE_PATH . 'turniket';

        if (!file_exists(\Yii::getAlias($filepath))) {
            mkdir(\Yii::getAlias($filepath), 0777, true);
        }
        $turniketFilePath = $filepath . "/turniket-" . date("Y-m-d") . ".json";
        file_put_contents(\Yii::getAlias($turniketFilePath), "," . json_encode($data['post_data']), FILE_APPEND | LOCK_EX);
        return $data;
    }

    public function actionCreate1($lang)
    {
        $user_id = \Yii::$app->request->post('user_id');
        if (!$user_id) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $profile = Profile::findOne(['user_id' => $user_id]);
        $user_access = UserAccess::findOne(['user_id' => $user_id, 'is_deleted' => 0]);

        if ($user_access) {
            $user_access_type = UserAccessType::findOne($user_access->user_access_type_id);
            $turniket_department_id = $user_access_type ? $user_access_type->table_name::findOne($user_access->table_id)->turniket_department_id : null;
        }

        if (isset($turniket_department_id)) {
            $data = TurniketMK::addPersonByDepartment($profile, $turniket_department_id);
            if ($data['status']) {
                return [$data, TurniketMK::addAccessPerson($profile)];
            } else {
                return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
            }

            return [
                TurniketMK::addPersonByDepartment($profile, $turniket_department_id),
                TurniketMK::addAccessPerson($profile)
            ];
        }
        $data = TurniketMK::addPerson($profile);
        if ($data['status']) {
            return [$data, TurniketMK::addAccessPerson($profile)];
        } else {
            return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return [
            TurniketMK::addPerson($profile),
            TurniketMK::addAccessPerson($profile)
        ];
    }

    public function actionCreate($lang)
    {
        $user_id = \Yii::$app->request->post('user_id');
        $user_ids = json_decode(\Yii::$app->request->post('user_ids')); // Get user_ids as an array if provide)

        // Determine if we're working with a single user ID or an array of IDs
        $userIds = $user_ids ?? [$user_id]; // If user_ids is provided, use it; otherwise, use the single user_id in an array format

        // If no user_id or user_ids are provided, return a "Data not found" response
        if (empty($userIds[0])) {
            return $this->response(0, _e('ID Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $responses = [];
        foreach ($userIds as $user_id) {
            $profile = Profile::findOne(['user_id' => $user_id]);

            // Check if the user is a student with valid status and not deleted
            if (isRole('student', $user_id)) {
                $student = Student::findOne(['user_id' => $user_id, 'is_deleted' => 0, 'status' => 10]);
                if ($student) {
                    $stdTurniket = StudentTurniket::findOne([
                        'faculty_id' => $student->faculty_id,
                        'course_id' => $student->course_id,
                        'edu_type' => $student->edu_type_id
                    ]);

                    if ($stdTurniket) {
                        if (isset($profile->turniket_id) && $profile->turniket_id > 0) {
                            $responses[$user_id] = TurniketMK::updatePic($profile);
                            continue;
                        } else {

                            $responses[$user_id] = $this->addPersonByDepartment($profile, $stdTurniket->turniket_department_id);
                            continue; // Move to the next user_id
                        }
                    } else {
                        $responses[$user_id] = TurniketMK::addPerson($profile);
                        continue;
                    }

                    // $responses[$user_id] = $this->response(0, _e(' Student Department on turniket not found.'), null, null, ResponseStatus::NOT_FOUND);
                    // continue;
                }
            }

            // Check if the user has access with department-based user access type
            $user_access = UserAccess::findOne([
                'user_id' => $user_id,
                'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                'is_deleted' => 0
            ]);

            $turniket_department_id = $this->getTurniketDepartmentId($user_access);
            if ($turniket_department_id) {
                if (isset($profile->turniket_id) && $profile->turniket_id > 0) {
                    $responses[$user_id] = TurniketMK::updatePic($profile);
                    continue;
                } else {
                    $responses[$user_id] = $this->addPersonByDepartment($profile, $turniket_department_id);
                    continue;
                }
            }

            // Check if the user has any other access
            $user_access = UserAccess::findOne(['user_id' => $user_id, 'is_deleted' => 0]);
            $turniket_department_id = $this->getTurniketDepartmentId($user_access);

            if ($turniket_department_id) {
                if (isset($profile->turniket_id) && $profile->turniket_id > 0) {
                    $responses[$user_id] = TurniketMK::updatePic($profile);
                    continue;
                } else {
                    $responses[$user_id] = $this->addPersonByDepartment($profile, $turniket_department_id);
                    continue;
                }
            }

            // Default action to add the person without a specific department
            if (isset($profile->turniket_id) && $profile->turniket_id > 0) {
                // $responses[$user_id] = TurniketMK::addAccessPerson($profile);
                // continue;

                $responses[$user_id] = TurniketMK::updatePic($profile);
            } else {
                $data = TurniketMK::addPerson($profile);

                if ($data['status']) {
                    $responses[$user_id] = [$data, TurniketMK::addAccessPerson($profile)];
                } else {
                    $data = TurniketMK::updatePic($profile);
                    $responses[$user_id] = $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            }
        }

        // Return responses for each user_id
        return $responses;
    }

    /**
     * Helper function to get the turniket department ID from user access
     */
    private function getTurniketDepartmentId($user_access)
    {
        if (!$user_access) {
            return null;
        }

        $user_access_type = UserAccessType::findOne($user_access->user_access_type_id);
        return $user_access_type ? $user_access_type->table_name::findOne($user_access->table_id)->turniket_department_id : null;
    }

    /**
     * Helper function to add a person by department and return the response
     */
    private function addPersonByDepartment($profile, $turniket_department_id)
    {
        $data = TurniketMK::addPersonByDepartment($profile, $turniket_department_id);
        if ($data['status']) {
            return [$data, TurniketMK::addAccessPerson($profile)];
        } else {
            return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionCreateOld($lang)
    {
        $user_id = \Yii::$app->request->post('user_id');
        if (!$user_id) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $profile = Profile::findOne(['user_id' => $user_id]);

        if (isRole('student', $user_id) && $student = Student::find()->where(['user_id' => $user_id, 'is_deleted' => 0, 'status' => 10])->one()) {
            $stdTurniket = StudentTurniket::find()->where([
                'faculty_id' => $student->faculty_id,
                'course_id' => $student->course_id,
                'edu_type' => $student->edu_type_id
            ])->one();

            if ($stdTurniket) {
                $turniket_department_id = $stdTurniket->turniket_department_id;
                $data = TurniketMK::addPersonByDepartment($profile, $turniket_department_id);
                if ($data['status']) {
                    return [$data, TurniketMK::addAccessPerson($profile)];
                } else {
                    return $this->response(0, _e('Not in turniket data'), null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        $user_access = UserAccess::findOne(['user_id' => $user_id, 'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,  'is_deleted' => 0]);

        if ($user_access) {
            $user_access_type = UserAccessType::findOne($user_access->user_access_type_id);
            $turniket_department_id = $user_access_type ? $user_access_type->table_name::findOne($user_access->table_id)->turniket_department_id : null;
        }

        if (isset($turniket_department_id)) {
            $data = TurniketMK::addPersonByDepartment($profile, $turniket_department_id);
            if ($data['status']) {
                return [$data, TurniketMK::addAccessPerson($profile)];
            } else {
                return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        $user_access = UserAccess::findOne(['user_id' => $user_id, 'is_deleted' => 0]);

        if ($user_access) {
            $user_access_type = UserAccessType::findOne($user_access->user_access_type_id);
            $turniket_department_id = $user_access_type ? $user_access_type->table_name::findOne($user_access->table_id)->turniket_department_id : null;
        }

        if (isset($turniket_department_id)) {
            $data = TurniketMK::addPersonByDepartment($profile, $turniket_department_id);
            if ($data['status']) {
                return [$data, TurniketMK::addAccessPerson($profile)];
            } else {
                return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        $data = TurniketMK::addPerson($profile);
        if ($data['status']) {
            return [$data, TurniketMK::addAccessPerson($profile)];
        } else {
            return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return [
            TurniketMK::addPerson($profile),
            TurniketMK::addAccessPerson($profile)
        ];
    }

    public function actionStudent($lang)
    {
        $user_id = \Yii::$app->request->post('user_id');
        if (!$user_id) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $profile = Profile::findOne(['user_id' => $user_id]);

        if (isRole('student', $user_id) && $student = Student::find()->where(['user_id' => $user_id, 'is_deleted' => 0, 'status' => 10])->one()) {
            $stdTurniket = StudentTurniket::find()->where([
                'faculty_id' => $student->faculty_id,
                'course_id' => $student->course_id,
                'edu_type' => $student->edu_type_id
            ])->one();

            if ($stdTurniket) {
                $turniket_department_id = $stdTurniket->turniket_department_id;
                $data = TurniketMK::addPersonByDepartment($profile, $turniket_department_id);
                if ($data['status']) {
                    return [$data, TurniketMK::addAccessPerson($profile)];
                } else {
                    return $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
            return $this->response(0, _e('Not in turniket data'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionAdd($lang)
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

        return [TurniketMK::updatePic($profile), TurniketMK::addAccessPerson($profile)];
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
            $profile->turniket_id = null;
            $profile->turniket_status = null;
            $profile->save();
            return TurniketMK::deletePerson($profile);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionCreate4545($lang)
    {

        // Replace with your actual access key and secret key
        $accessKey = "27116438";
        $secretKey = "IdmSsuMbgfd3AtcTdfHF";

        $accessToken = $this->getAccessToken($accessKey, $secretKey);
        if ($accessToken === null) {
            echo "Failed to retrieve access token";
            exit;
        }
        $url = "https://192.168.50.11/artemis/api/resource/v1/person/face/update";

        $data = [
            "pageNo" => 1,
            "pageSize" => 11
        ];

        $jsonData = json_encode($data);

        // Create the signature (HMAC SHA256)
        $timestamp = time();
        $stringToSign = $accessKey . $timestamp . $jsonData;
        $signature = hash_hmac('sha256', $stringToSign, $secretKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Ca-Key: ' . $accessKey,          // Correct header for Access Key
            'Authorization: Bearer ' . $accessToken, // Use the access token here
            'Signature: ' . $signature,         // HMAC signature
            'Timestamp: ' . $timestamp
        ]);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $jsonData
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            $decodedResponse = json_decode($response, true);
            print_r($decodedResponse);
        }

        curl_close($ch);
        dd("asdasd");

        $url = "https://192.168.50.11/artemis/api/resource/v1/person/face/update";

        $accessKey = "27116438";  // Replace with your actual Access Key
        $secretKey = "IdmSsuMbgfd3AtcTdfHF";  // Replace with your actual Secret Key

        $data = [
            "pageNo" => 1,
            "pageSize" => 11
        ];

        $jsonData = json_encode($data);

        // Create the signature (HMAC SHA256)
        $timestamp = time();  // Current timestamp
        $stringToSign = $accessKey . $timestamp . $jsonData;  // Concatenate the access key, timestamp, and request data
        $signature = hash_hmac('sha256', $stringToSign, $secretKey);  // Generate HMAC SHA256 signature
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'AccessKey: ' . $accessKey,  // Send Access Key as a header
            'Signature: ' . $signature,  // Send generated signature as a header
            'Timestamp: ' . $timestamp   // Send the timestamp as a header
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            $decodedResponse = json_decode($response, true);
            dd($decodedResponse);
        }

        // Close the cURL session
        curl_close($ch);

        dd('ssss');
    }
}
