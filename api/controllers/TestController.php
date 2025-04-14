<?php

namespace api\controllers;

// use common\models\model\Test;

use api\components\TurniketMK;
use app\components\FaceImageMK;
use base\ResponseStatus;
use common\models\model\Profile;
use common\models\model\StudentTimeTable;
use RuntimeException;

class TestController extends ApiActiveController
{
    public $modelClass = 'api\resources\Test';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        // student_id', 'time_table_id

        $dd = [];
        $data = [
            1326 => 25479,
            8380 => 25057,
            10490 => 24775,
            10130 => 24775,
            5933 => 24695,
            1959 => 25342,
            1179 => 24160,
            456 => 24209,
            413 => 25725,
            1500 => 24386,
            1806 => 25260,
            1807 => 25260,
            1768 => 25260,
            5934 => 25260,
            378 => 25034,
            2032 => 25338,
            1500 => 24934,
            3630 => 24864,
            5743 => 24864,

        ];

        foreach ($data as $key => $value) {
            $studentTimeTable = new StudentTimeTable();
            $studentTimeTable->student_id = $key;
            $studentTimeTable->time_table_id = $value;

            $dd[$key][$value] = StudentTimeTable::createItem($studentTimeTable);
        }

        return $dd;
        // createItem()
    }


    public function actionCreate001($lang)
    {

        // return 1;
        /** Check Face ID */
        $imageService = new FaceImageMK();
        $response = $imageService->checkFaceIDTest();

        // Handle the response as needed

        return $this->response(1, _e(''), $response, null, ResponseStatus::OK);
        // return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);

        /** Check Face ID */
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


        // return 1;
        // $API_URL = 'https://192.168.50.11/artemis/api/resource/v1/acsDevice/acsDeviceList';

        // $APP_KEY = '28738424';
        // $APP_SECRET = 'gc3zcpH46jBYOsDUJsc';

        // $data = [
        //     'pageNo' => 1,
        //     'pageSize' => 11,
        // ];

        // $jsonData = json_encode($data);

        // $ch = curl_init($API_URL);

        // curl_setopt_array($ch, [
        //     CURLOPT_POST => true,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_HTTPHEADER => [
        //         'Content-Type: application/json',
        //         "APPKey: $APP_KEY",
        //         "APPSecret: $APP_SECRET"
        //     ],
        //     CURLOPT_POSTFIELDS => $jsonData,
        //     CURLOPT_SSL_VERIFYPEER => false,
        //     CURLOPT_SSL_VERIFYHOST => false,
        // ]);

        // $response = curl_exec($ch);

        // if ($response === false) {
        //     throw new RuntimeException(curl_error($ch));
        // }

        // $decodedResponse = json_decode($response, true);

        // curl_close($ch);

        // dd($decodedResponse);
    }
    public function actionCreate($lang)
    {

        // Replace with your actual access key and secret key
        $accessKey = "27116438";
        $secretKey = "IdmSsuMbgfd3AtcTdfHF";

        // Replace with the actual access token after obtaining it
        $accessToken = "yourAccessToken";  // Replace with actual Access Token
        // $accessKey = "yourAccessKey";  // Replace with actual Access Key (AppKey)
        // $secretKey = "yourSecretKey";  // Replace with actual Secret Key

        // API URL for adding a person
        $url = "https://192.168.50.11/artemis/api/resource/v1/person/single/add";

        // Data payload as per the API documentation
        $data = [
            "personCode" => "123245214",
            "personFamilyName" => "LI",
            "personGivenName" => "person0",
            "gender" => 1,
            "orgIndexCode" => "1rwad89d-0ce6-4826-9146-6b71f037d81e",
            "remark" => "description",
            "phoneNo" => "13000110011",
            "email" => "person1@qq.com",
            "faces" => [
                [
                    "faceData" => "/9j/4AAQSkZJRgABAQEAAAAAAAD/4QBCRXhpZgAATU.." // Replace with actual Base64 encoded face data
                ]
            ],
            "cards" => [
                [
                    "cardNo" => "123456"
                ]
            ],
            "beginTime" => "2020-05-26T15:00:00+08:00",
            "endTime" => "2030-05-26T15:00:00+08:00",
            "residentRoomNo" => 9999,
            "residentFloorNo" => 1
        ];

        // Convert data to JSON
        $jsonData = json_encode($data);

        // Generate signature (HMAC-SHA256)
        $timestamp = time();  // Current timestamp
        $stringToSign = $accessKey . $timestamp . $jsonData;  // Concatenate Access Key, timestamp, and JSON data
        $signature = hash_hmac('sha256', $stringToSign, $secretKey);  // Generate the signature

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Ca-Key' => $accessKey,
            'X-Ca-Signature' => $secretKey,

            // 'Content-Type: application/json',
            // 'AccessKey: ' . $accessKey,  // Access Key header
            // 'Signature: ' . $signature,  // Signature header
            // 'Timestamp: ' . $timestamp,  // Timestamp header
            // 'X-Ca-Key: ' . $accessKey,  // AppKey (as mentioned in the error message)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification (for self-signed certificates)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable SSL verification (for self-signed certificates)

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            $error = curl_error($ch);
            echo "cURL Error: $error";
        } else {
            // Decode the JSON response
            $decodedResponse = json_decode($response, true);
            print_r($decodedResponse);
        }

        // Close the cURL session
        curl_close($ch);
    }
}
