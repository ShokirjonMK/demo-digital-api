<?php

namespace api\components;

use common\models\model\Profile;
use common\models\model\VisitorProfile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;

class TurniketMK
{
    public $user_number = '';
    public $numbers_array = [];
    const UPLOADS_FOLDER = 'uploads/faceID/';

    const BASE_URL = 'https://192.168.50.11/artemis/api/resource/v1';
    // const API_VER = 'v1';
    const URL = '/person/single/add';
    private static $_token = 'BF9F9B0C-9273-4072-A815-A51AC905FE9A';
    public static $_AK = '27116438';
    public static $_SIGNATURE = 'vAChsHLLW7ewHv+4U5vZVNX821psGFpEAP7umPjy+u8=';
    public static $_SIGNATURE1 = 'GyJMANKHiFHCytfi6h2HZE5a1tuJmSEr4YIHCJ19v28=';
    public static $_SIGNATURE_privilege_group = 'GyJMANKHiFHCytfi6h2HZE5a1tuJmSEr4YIHCJ19v28=';
    // public static $_SIGNATURE = 'echo(EQAgAIYoraDN3HcMqLAD6yu0++htp5IhEDdcPOHBumB2MfIF94tkrGnJpsfKGjBpW0eSKMhsfqz8ngQ8xWDLsnoWqhQ=';

    // public static function generateSignature($profile)
    // {
    //     $timestamp = time();  // Current timestamp
    //     $stringToSign = self::$_AK . $timestamp . $jsonData;  // Concatenate Access Key, timestamp, and JSON data
    //     $signature = hash_hmac('sha256', $stringToSign, $secretKey);  // Generate the signature

    //     return $signature;
    // }

    public static function addPerson($profile)
    {

        // this is works
        // you see?
        $data = [];
        $data['status'] = false; // Status of the request

        // Convert image to base64 before sending it

        if (isset($profile->image)) {
            $imgPath = MAIN_STORAGE_PATH . ($profile->image ?? '');  // Path to the image file
            if (!file_exists($imgPath) || !$img = self::imageBase64($imgPath)) {
                $data['error'] = file_exists($imgPath) ? 'Failed to convert image to Base64' : 'Image not found';
                return $data;
            }
        } else {
            $data['error'] =  'Image not found';
            return $data;
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => self::$_SIGNATURE,  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            self::BASE_URL . self::URL,  // API URL
            [
                'body' => json_encode([
                    "personCode" => $profile->passport_pin,
                    "personFamilyName" => $profile->last_name,
                    "personGivenName" => $profile->first_name,
                    "gender" => $profile->gender,
                    "orgIndexCode" => "1",
                    "remark" => "description",
                    "phoneNo" => "911355505",
                    "email" => $profile->user->email ?? "test@test.test",
                    "faces" => [
                        [
                            "faceData" => $img  // Sending Base64 encoded image
                        ]
                    ],
                    "beginTime" => "2020-05-26T15:00:00+08:00",
                    "endTime" => "2030-05-26T15:00:00+08:00"
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {
            if ($responseBody['code'] == 131) {
                // 131 - Person already exists
                return self::infoPerson($profile);
            }
            if ($responseBody['code'] == 0) {
                $turniket_id = $responseBody['data'];
                $profile->turniket_id = $turniket_id;
                if (!$profile->save(false)) $error = $profile->errors;
                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function addVisitorPerson($visitorProfile)
    {

        // this is works
        // you see?
        $data = [];
        $data['status'] = false; // Status of the request

        // Convert image to base64 before sending it

        if (isset($visitorProfile->image)) {
            $imgPath = MAIN_STORAGE_PATH . ($visitorProfile->image ?? '');  // Path to the image file
            if (!file_exists($imgPath) || !$img = self::imageBase64($imgPath)) {
                $data['error'] = file_exists($imgPath) ? 'Failed to convert image to Base64' : 'Image not found';
                return $data;
            }
        } else {
            $data['error'] =  'Image not found';
            return $data;
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => self::$_SIGNATURE,  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            self::BASE_URL . self::URL,  // API URL
            [
                'body' => json_encode([
                    "personCode" => $visitorProfile->passport_pin,
                    "personFamilyName" => $visitorProfile->last_name,
                    "personGivenName" => $visitorProfile->first_name,
                    "gender" => $visitorProfile->gender,
                    "orgIndexCode" => "166",
                    "remark" => "description",
                    "phoneNo" => $visitorProfile->phone,
                    "email" => "test@test.test",
                    "faces" => [
                        [
                            "faceData" => $img  // Sending Base64 encoded image
                        ]
                    ],
                    "beginTime" => "2020-05-26T15:00:00+08:00",
                    "endTime" => "2030-05-26T15:00:00+08:00"
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);

        // dd($responseBody);
        if ($response->getStatusCode() == 200) {
            if ($responseBody['code'] == 131) {
                // 131 - Person already exists
                return self::infoVisitorPerson($visitorProfile);
            }
            if ($responseBody['code'] == 0) {
                $turniket_id = $responseBody['data'];
                $visitorProfile->turniket_id = $turniket_id;
                if (!$visitorProfile->save(false)) $error = $visitorProfile->errors;


                // dd([
                //     "responseBody" => $responseBody,
                //     "visitorProfile" => $visitorProfile,
                //     "error" => $error,
                //     "turniket_id" => $turniket_id,
                //     "errors" => $visitorProfile->errors,
                //     "save" => $visitorProfile->save(false)
                // ]);
                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function updateVisitorPic($visitorProfile)
    {

        // this is works
        // you see?
        $data = [];
        $data['status'] = false; // Status of the request

        // Convert image to base64 before sending it

        if (isset($visitorProfile->image)) {
            $imgPath = MAIN_STORAGE_PATH . ($visitorProfile->image ?? '');  // Path to the image file
            if (!file_exists($imgPath) || !$img = self::imageBase64($imgPath)) {
                $data['error'] = file_exists($imgPath) ? 'Failed to convert image to Base64' : 'Image not found';
                return $data;
            }
        } else {
            $data['error'] =  'Image not found';
            return $data;
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => "IpZ2dhDhVKLBGK8wmt0mICdrzx2R6zdvD4d2joez3xs=",  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/resource/v1/person/face/update',  // API URL
            [
                'body' => json_encode([
                    "personId" => (string)$visitorProfile->turniket_id,
                    "faceData" => $img  // Sending Base64 encoded image
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function updatePic($profile)
    {

        // this is works
        // you see?
        $data = [];
        $data['status'] = false; // Status of the request

        // Convert image to base64 before sending it

        if (isset($profile->image)) {
            $imgPath = MAIN_STORAGE_PATH . ($profile->image ?? '');  // Path to the image file
            if (!file_exists($imgPath) || !$img = self::imageBase64($imgPath)) {
                $data['error'] = file_exists($imgPath) ? 'Failed to convert image to Base64' : 'Image not found';
                return $data;
            }
        } else {
            $data['error'] =  'Image not found';
            return $data;
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => "IpZ2dhDhVKLBGK8wmt0mICdrzx2R6zdvD4d2joez3xs=",  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/resource/v1/person/face/update',  // API URL
            [
                'body' => json_encode([
                    "personId" => (string)$profile->turniket_id,
                    "faceData" => $img  // Sending Base64 encoded image
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function addPersonByDepartment($profile, $department_id_on_turniket)
    {
        $data = [];
        $data['status'] = false; // Status of the request

        // Convert image to base64 before sending it
        $imgPath = MAIN_STORAGE_PATH . (isset($profile->image) ? $profile->image : '');  // Path to the image file


        // $img = self::imageBase64(STORAGE_PATH . 'user_images_new\32611902630050.png');  // Convert image to Base64
        if (isset($profile->image)) {
            $imgPath = MAIN_STORAGE_PATH . ($profile->image ?? '');  // Path to the image file
            if (!file_exists($imgPath) || !$img = self::imageBase64($imgPath)) {
                $data['error'] = file_exists($imgPath) ? 'Failed to convert image to Base64' : 'Image not found';
                return $data;
            }
        } else {
            $data['error'] =  'Image not found';
            return $data;
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => self::$_SIGNATURE,  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
            'timeout' => 10,
        ]);

        $response = $client->post(
            self::BASE_URL . self::URL,  // API URL
            [
                'body' => json_encode([
                    "personCode" => $profile->passport_pin,
                    "personFamilyName" => $profile->last_name,
                    "personGivenName" => $profile->first_name,
                    "gender" => $profile->gender,
                    "orgIndexCode" => (string) $department_id_on_turniket,
                    "remark" => "description",
                    "phoneNo" => "911355505",
                    "email" => $profile->user->email ?? "test@test.test",
                    "faces" => [
                        [
                            "faceData" => $img  // Sending Base64 encoded image
                        ]
                    ],
                    "beginTime" => "2020-05-26T15:00:00+08:00",
                    "endTime" => "2030-05-26T15:00:00+08:00"
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 131) {
                // 131 - Person already exists
                return self::infoPerson($profile);
            }
            if ($responseBody['code'] == 0) {
                $turniket_id = $responseBody['data'];
                $profile->turniket_id = $turniket_id;
                if (!$profile->save(false)) $error = $profile->errors;
                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function accessList($pageNo = 1, $pageSize = 10)
    {
        // but this is not works
        $data = [];
        $data['status'] = false; // Status of the request


        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => '4WO1ii9Fs9iZTwHrHGXyki0ja0f/uXifjhCbux1qg60=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/acs/v1/privilege/group/single/addPersons',
            [
                'body' => json_encode([
                    "privilegeGroupId" => 3,
                    "type" => 1,
                    "list" => [
                        [
                            "id" => 8051
                        ]
                    ]
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function addVisitorAccessPerson($visitorProfile)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request
        if (!$visitorProfile instanceof VisitorProfile || !$visitorProfile->turniket_id > 0) {
            $error[] = _e("Turniketga qo'shilmagan");
            $data['error'] = $error;
            return $data;
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => self::$_SIGNATURE1,  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/acs/v1/privilege/group/single/addPersons',  // API URL
            [
                'body' => json_encode([
                    "privilegeGroupId" => "12",
                    "type" => 1,
                    "list" => [
                        [
                            // "personCode" => $profile->passport_pin
                            "id" => (string)$visitorProfile->turniket_id
                            // "id" => "8001"
                        ]
                    ],

                ])
            ]
        );

        // dd($response);

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $visitorProfile->turniket_status = 1;
                if (!$visitorProfile->save(false)) $error = $visitorProfile->errors;

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function addAccessPerson($profile)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request
        if (!$profile instanceof Profile || !$profile->turniket_id > 0) {
            $error[] = _e("Turniketga qo'shilmagan");
            $data['error'] = $error;
            return $data;
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => self::$_SIGNATURE1,  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/acs/v1/privilege/group/single/addPersons',  // API URL
            [
                'body' => json_encode([
                    "privilegeGroupId" => "12",
                    "type" => 1,
                    "list" => [
                        [
                            // "personCode" => $profile->passport_pin
                            "id" => (string)$profile->turniket_id
                            // "id" => "8001"
                        ]
                    ],

                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $profile->turniket_status = 1;
                if (!$profile->save(false)) $error = $profile->errors;

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    //Subscribe the events by event type
    public static function subscribeEvent($profile)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request
        if (!$profile instanceof Profile || !$profile->turniket_id > 0) {
            $error[] = _e("Turniketga qo'shilmagan");
            $data['error'] = $error;
            return $data;
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => 'eV2IUYERctsvgwgGqEsExsbSyprh8hhdYzSZZz1dIUQ=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/eventService/v1/eventSubscriptionByEventTypes',  // API URL
            [
                'body' => json_encode([
                    "eventTypes" => [
                        [
                            196893
                        ]
                    ],
                    "eventDest" => "https://api-digital.tsul.uz/en/opens/turniket",
                    "token" => "qscasd",
                    "passBack" => 0

                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function deletePerson($profile)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request
        if (!$profile instanceof Profile || !$profile->turniket_id > 0) {
            $error[] = _e("Turniketga qo'shilmagan");
            $data['error'] = $error;
            return $data;
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => 'X3lCCuBi4FaV/NzQiXJmFQLIC3m2r1PRExsckRWu4K8=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/resource/v1/person/single/delete',  // API URL
            [
                'body' => json_encode([
                    "personId" => (string)$profile->turniket_id,
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $profile->turniket_status = 0;
                $profile->turniket_id = null;
                if (!$profile->save(false)) $error = $profile->errors;

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function addDep($dep, $name, $level)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => 'eV2IUYERctsvgwgGqEsExsbSyprh8hhdYzSZZz1dIUQ=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/resource/v1/org/single/add',  // API URL
            [
                'body' => json_encode([

                    "orgName" => $name,
                    "parentIndexCode" => $level

                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $dep->turniket_department_id = $responseBody['data']['orgIndexCode'];
                if (!$dep->save(false)) $error = $dep->errors;

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function infoPerson($profile)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request
        if (!$profile instanceof Profile || !$profile->passport_pin > 0) {
            $error[] = _e("Passport Pin not found");
            $data['error'] = $error;
            return $data;
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => 'JP4VB8X9R4VMi/hA2Bbt2lth1FBWv/wZ7xEWrz++6JA=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/resource/v1/person/personCode/personInfo',  // API URL
            [
                'body' => json_encode([
                    "personCode" => (string)$profile->passport_pin,
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $profile->turniket_id = $responseBody['data']['personId'];
                if (!$profile->save(false)) $error = $profile->errors;

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function infoVisitorPerson($visitorProfile)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request
        if (!$visitorProfile instanceof VisitorProfile || !$visitorProfile->passport_pin > 0) {
            $error[] = _e("Passport Pin not found");
            $data['error'] = $error;
            return $data;
        }
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => 'JP4VB8X9R4VMi/hA2Bbt2lth1FBWv/wZ7xEWrz++6JA=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/resource/v1/person/personCode/personInfo',  // API URL
            [
                'body' => json_encode([
                    "personCode" => (string)$visitorProfile->passport_pin,
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $visitorProfile->turniket_id = $responseBody['data']['personId'];
                if (!$visitorProfile->save(false)) $error = $visitorProfile->errors;

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function getPicture($picUrl)
    {
        $data = [];
        $error = [];
        $data['status'] = false; // Status of the request

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => 'i1d15QY1SKfQPHtwQK7jVKXys90JwSRJcQ7Lm+x3H1A=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/acs/v1/event/pictures',  // API URL
            [
                'body' => json_encode([
                    "picUri" => $picUrl,
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            return $responseBody;
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    // Get access level list
    public static function accessLevelList($pageNo = 1, $pageSize = 20)
    {
        $data = [];
        $data['status'] = false; // Status of the request

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => '4WO1ii9Fs9iZTwHrHGXyki0ja0f/uXifjhCbux1qg60=',  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            'https://192.168.50.11/artemis/api/acs/v1/privilege/group',  // API URL
            [
                'body' => json_encode([
                    "pageNo" => $pageNo,
                    "pageSize" => $pageSize,
                    "type" => 1,

                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {
                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }


    public static function deviceList($pageNo = 1, $pageSize = 20)
    {
        $data = [];
        $data['status'] = false; // Status of the request

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Ca-Key' => self::$_AK,  // Access Key
                'X-Ca-Signature' => self::$_SIGNATURE,  // Signature
            ],
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);

        $response = $client->post(
            self::BASE_URL . '/acsDevice/acsDeviceList',  // API URL
            [
                'body' => json_encode([
                    "pageNo" => $pageNo,
                    "pageSize" => $pageSize
                ])
            ]
        );

        // Check for response and handle errors
        $responseBody = json_decode($response->getBody(), true);
        if ($response->getStatusCode() == 200) {

            if ($responseBody['code'] == 0) {

                $data['status'] = true;
                $data['data'] = $responseBody;
                return $data;
            } else {
                $data['error'] = $responseBody['msg'] ?? 'Unknown error';
                $data['data'] = $responseBody;
                return $data;
            }
        } else {
            $data['status'] = false; // Status of the request
            return $data;
        }
    }

    public static function imageBase64($imagePath)
    {
        // Check file existence and directly return Base64 encoding if successful
        if (is_file($imagePath)) {
            return base64_encode(file_get_contents($imagePath));
        }
        return null;
    }


    // public static function imageBase64s($imagePath)
    // {
    //     // Ensure the file exists
    //     if (file_exists($imagePath)) {
    //         // Get the image content and encode it to base64

    //         $imageData = file_get_contents($imagePath);
    //         return base64_encode($imageData);
    //     }
    //     return null;
    // }


    public static function encodeImageToBase64(string $filePath): string
    {
        $imageData = file_get_contents($filePath);
        $base64String = base64_encode($imageData);

        return "data:image/png;base64,{$base64String}";
    }

    public static function saveImage($exam_id)
    {
        if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
            mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
        }

        $imagee = UploadedFile::getInstancesByName('image');
        if ($imagee) {
            $imagee = $imagee[0];

            $fileName = $exam_id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $imagee->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $imagee->saveAs($url, false);
            return "storage/" . $miniUrl;
        }
    }
}
