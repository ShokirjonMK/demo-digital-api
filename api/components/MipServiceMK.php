<?php

namespace api\components;

use common\models\model\Profile;
use common\models\model\VisitorProfile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MipServiceMK
{
    public $user_number = '';
    public $numbers_array = [];

    private static $_token = 'BF9F9B0C-9273-4072-A815-A51AC905FE9A';

    public static function getToken()
    {
        return self::$_token;
    }

    public static function socialProtection($pin)
    {
        // $pin = "61801045840029";
        // $document_issue_date =  "2021-01-13";

        $data = [];
        $error = [];
        $data['status'] = false;


        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => self::getToken(),
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "external.minfin_social_protection_registry_by_pin",
                    "params" => [
                        "pin" => $pin,
                    ]
                ]
            )]
        );
        if ($response->getStatusCode() == 200) {
            // dd($response);

            $res = json_decode($response->getBody()->getContents());
            if (isset($res->result)) {
                $result = $res->result;


                $data['status'] = true;
                $profile = Profile::findOne(['passport_pin' => $pin]);

                if ($profile) {
                    $profile->social_protection = 1;
                    if (!$profile->save(false)) $error = $profile->errors;
                }

                $data['data'] = $result;
                $data['error'] = $error;

                return $data;
            } else {
                $error[] = $res->error;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }
    }

    public static function healthHasDisability($pin, $document_serial_number)
    {
        // $pin = "61801045840029";
        // $document_issue_date =  "2021-01-13";

        $data = [];
        $error = [];
        $data['status'] = false;


        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => self::getToken(),
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "external.health_has_disability_by_pin_document_serial_number",
                    "params" => [
                        "pin" => $pin,
                        "document_serial_number" => $document_serial_number
                    ]
                ]
            )]
        );
        if ($response->getStatusCode() == 200) {
            // dd($response);

            $res = json_decode($response->getBody()->getContents());
            if (isset($res->result)) {
                $result = $res->result;


                $data['status'] = true;
                $profile = Profile::findOne(['passport_pin' => $pin]);

                if ($profile) {
                    $profile->has_disability = $result->has_disability;
                    if (!$profile->save(false)) $error = $profile->errors;
                }

                $data['data'] = $result;
                $data['error'] = $error;

                return $data;
            } else {
                $error[] = $res->error;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }
    }

    public static function corrent($profile)
    {
        $pin = $profile->passport_pin;
        $document_issue_date = "2024-01-01";

        $data = [];
        $error = '';
        $data['status'] = false;


        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => self::getToken(),
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "adliya.get_personal_data_by_pin",
                    "params" => [
                        "pin" => $pin,
                        "document_issue_date" => $document_issue_date
                    ]
                ]
            )]
        );


        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());
            if (isset($res->result)) {

                $result = $res->result;

                // $photo = self::saveToTurniket($result->photo, $result->pinpp, $result->namelatin, $result->surnamelatin);
                $photo = self::saveTo($result->photo, $result->pinpp);

                $data['status'] = true;
                $result->avatar = $photo;

                if (isset($result->doc_seria)) {
                    $profile->passport_seria = $result->doc_seria;
                }
                if (isset($result->doc_number)) {
                    $profile->passport_number = $result->doc_number;
                }
                $profile->last_name = $result->surnamelatin ?? null;
                $profile->first_name = $result->namelatin ?? null;
                $profile->middle_name = $result->patronymlatin ?? null;
                $profile->passport_issued_date = $result->docdateend ?? null;
                $profile->birthday = $result->birthdate ?? null;
                $profile->gender = ($result->gender == "M") ? 1 : 0;
                $profile->image = $result->avatar ?? null;
                $profile->checked_full = 1;
                if (!$profile->save(false)) $error = $profile->errors;

                // $data['data'] = $result;
                // $data['error'] = $error;

                return [$pin => true];
                return $data;
            } else {
                $error = $res->error;
                $data['error'] = $error;
                return [$pin => false];
                return $data;
            }
        } else {
            $data['status'] = false;
            return [$pin => false];
            return $data;
        }
    }

    public static function freeMahalladan($profile)
    {
        $pin = $profile->passport_pin;
        $document_issue_date = $profile->passport_given_date;

        $data = [];
        $error = '';
        $data['status'] = false;


        $client = new Client();
        // dd('ssssss');

        try {
            $url = 'https://api.online-mahalla.uz/api/v1/public/tax/passport?series=' . $profile->passport_seria . '&number=' . $profile->passport_number . '&birth_date=' . $profile->birthday;
            // $url = 'https://api.online-mahalla.uz/api/v1/public/tax/passport?series=AC&number=1662283&birth_date=2003-02-02';

            $response = $client->request('GET', $url);

            $data = json_decode($response->getBody(), true);
            $profile->passport_given_by = $data->data->info->data->pinfl;
            $profile->passport_pin = $data->data->info->data->given_date;
            $data['status'] = true;
            return [$profile->passport_pin => true];
        } catch (RequestException $e) {

            // Catch all 4XX errors 

            // To catch exactly error 400 use 
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == '400') {
                    return [$profile->passport_number => false];
                }
            }

            // You can check for whatever error status code you need 

        } catch (\Exception $e) {

            return [$profile->passport_number => 'failed'];
        }



        $pinfl = $data['data']['pinfl'];
        $givenDate = $data['data']['given_date'];

        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());
            if (isset($res->result)) {
                $result = $res->result;

                $photo = self::saveToTurniket($result->photo, $result->pinpp, $result->namelatin, $result->surnamelatin);
                $photo = self::saveTo($result->photo, $result->pinpp);

                $data['status'] = true;
                $result->avatar = $photo;

                $profile->passport_given_by = $result->docgiveplace;

                if (isset($result->doc_seria)) {
                    $profile->passport_seria = $result->doc_seria;
                }
                if (isset($result->doc_number)) {
                    $profile->passport_number = $result->doc_number;
                }

                $profile->last_name = $result->surnamelatin;
                $profile->first_name = $result->namelatin;
                $profile->middle_name = $result->patronymlatin;
                $profile->passport_issued_date = $result->docdateend;
                $profile->birthday = $result->birthdate;
                $profile->passport_issued_date = $result->docdateend;
                $profile->gender = ($result->sex == 1) ? 1 : 0;
                $profile->image = $result->avatar;
                $profile->checked_full = 1;
                if (!$profile->save(false)) $error = $profile->errors;

                // $data['data'] = $result;
                // $data['error'] = $error;

                return [$pin => true];
                return $data;
            } else {
                $error = $res->error;
                $data['error'] = $error;
                return [$pin => false];
                return $data;
            }
        } else {
            $data['status'] = false;
            return [$pin => false];
            return $data;
        }
    }

    public static function getData($pin, $document_issue_date)
    {
        // $pin = "61801045840029";
        // $document_issue_date =  "2021-01-13";

        $data = [];
        $error = [];
        $data['status'] = false;


        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => self::getToken(),
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "adliya.get_personal_data_by_pin",
                    "params" => [
                        "pin" => $pin,
                        "document_issue_date" => $document_issue_date
                    ]
                ]
            )]
        );
        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());
            // dd($res);
            if (isset($res->result)) {
                // dd($res);
                $result = $res->result;

                if (!empty($result->photo)) {
                    $photo = self::saveTo($result->photo, $result->pinpp);
                    $result->avatar = $photo;
                }

                // dd(json_decode($response->getBody()->getContents()));
                // return  json_decode($response->getBody()->getContents());
                $data['status'] = true;

                $profile = Profile::findOne(['passport_pin' => $result->pinpp]);

                if ($profile) {
                    if (isset($result->doc_seria)) {
                        $profile->passport_seria = $result->doc_seria;
                    }
                    if (isset($result->doc_number)) {
                        $profile->passport_number = $result->doc_number;
                    }
                    $profile->last_name = $result->surnamelatin ?? null;
                    $profile->first_name = $result->namelatin ?? null;
                    $profile->middle_name = $result->patronymlatin ?? null;
                    $profile->passport_issued_date = $result->docdateend ?? null;
                    $profile->birthday = $result->birthdate ?? null;
                    $profile->gender = ($result->gender == "M") ? 1 : 0;
                    $profile->image = $result->avatar ?? null;
                    $profile->checked_full = 1;
                    if (!$profile->save(false)) $error = $profile->errors;
                }

                $data['data'] = $result;
                $data['error'] = $error;

                return $data;
            } else {
                $error[] = $res->error ?? null;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }
    }
    public static function getDataVisitor($pin, $document_issue_date)
    {
        $data = [];
        $error = [];
        $data['status'] = false;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => self::getToken(),
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "adliya.get_personal_data_by_pin",
                    "params" => [
                        "pin" => $pin,
                        "document_issue_date" => $document_issue_date
                    ]
                ]
            )]
        );
        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());
            // dd($res);
            if (isset($res->result)) {
                // dd($res);
                $result = $res->result;

                if (!empty($result->photo)) {
                    $photo = self::saveToVisitor($result->photo, $result->pinpp);
                    $result->avatar = $photo;
                }

                // dd(json_decode($response->getBody()->getContents()));
                // return  json_decode($response->getBody()->getContents());
                $data['status'] = true;

                $profile = VisitorProfile::findOne(['passport_pin' => $result->pinpp]);

                if ($profile) {
                    if (isset($result->doc_seria)) {
                        $profile->passport_seria = $result->doc_seria;
                    }
                    if (isset($result->doc_number)) {
                        $profile->passport_number = $result->doc_number;
                    }
                    $profile->last_name = $result->surnamelatin ?? null;
                    $profile->first_name = $result->namelatin ?? null;
                    $profile->middle_name = $result->patronymlatin ?? null;
                    $profile->passport_issued_date = $result->docdateend ?? null;
                    $profile->birthday = $result->birthdate ?? null;
                    $profile->gender = ($result->gender == "M") ? 1 : 0;
                    $profile->image = $result->avatar ?? null;
                    $profile->checked_full = 1;
                    if (!$profile->save(false)) $error = $profile->errors;
                }

                $data['data'] = $result;
                $data['error'] = $error;

                return $data;
            } else {
                $error[] = $res->error ?? null;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }
    }

    public static function getDataPin($pin, $document_issue_date = '2000-01-01')
    {
        $data = [];
        $error = [];
        $data['status'] = false;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => self::getToken(),
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "adliya.get_personal_data_by_pin",
                    "params" => [
                        "pin" => $pin,
                        "document_issue_date" => $document_issue_date
                    ]
                ]
            )]
        );
        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());

            if (isset($res->result)) {

                $result = $res->result;

                $data['status'] = true;

                $data['data'] = $result;
                $data['error'] = $error;

                return $data;
            } else {
                $error[] = $res->error ?? null;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }
    }

    private static function saveTo($imgBase64, $pin)
    {
        // Define the upload path for storing images
        $uploadPathMK = STORAGE_PATH . 'user_visitor/';

        // Ensure that the directory exists, create it if not
        if (!file_exists($uploadPathMK)) {
            mkdir($uploadPathMK, 0777, true);
        }

        // Split the base64 image data into parts
        $parts = explode(";base64,", $imgBase64);

        // Extract the image type from the parts
        $imageparts = explode("image/", @$parts[0]);

        // Decode the base64 image data
        $imagebase64 = base64_decode($imgBase64);

        // Generate a unique filename using the provided PIN
        $filename = $pin . '__ik.jpg';

        // Construct the full file path
        $file = $uploadPathMK . $filename;

        // Save the decoded image data to the file
        file_put_contents($file, $imagebase64);

        // Return the relative URL of the saved image
        return 'storage/user_visitor/' . $filename;
    }

    private static function saveToVisitor($imgBase64, $pin)
    {
        // Define the upload path for storing images
        $uploadPathMK = STORAGE_PATH . 'visitor/';

        // Ensure that the directory exists, create it if not
        if (!file_exists($uploadPathMK)) {
            mkdir($uploadPathMK, 0777, true);
        }

        // Split the base64 image data into parts
        $parts = explode(";base64,", $imgBase64);

        // Extract the image type from the parts
        $imageparts = explode("image/", @$parts[0]);

        // Decode the base64 image data
        $imagebase64 = base64_decode($imgBase64);

        // Generate a unique filename using the provided PIN
        $filename = $pin . '.jpg';

        // Construct the full file path
        $file = $uploadPathMK . $filename;

        // Save the decoded image data to the file
        file_put_contents($file, $imagebase64);

        // Return the relative URL of the saved image
        return 'storage/visitor/' . $filename;
    }


    // private static function saveTo($imgBase64, $pin)
    // {
    //     // $imgBase64 = '';
    //     $uploadPathMK   = STORAGE_PATH  . 'user_images_new/';
    //     if (!file_exists(STORAGE_PATH  . 'user_images_new/')) {
    //         mkdir(STORAGE_PATH . 'user_images_new/', 0777, true);
    //     }

    //     $parts        = explode(
    //         ";base64,",
    //         $imgBase64
    //     );
    //     $imageparts   = explode("image/", @$parts[0]);
    //     $imagebase64  = base64_decode($imgBase64);
    //     $miniurl = $pin . '.png';
    //     $file = $uploadPathMK . $miniurl;

    //     file_put_contents($file, $imagebase64);

    //     return 'storage/user_images_new/' . $miniurl;
    // }

    private static function saveToTurniket($imgBase64, $pin, $first_name, $last_name)
    {
        // $imgBase64 = '';
        $uploadPathMK   = STORAGE_PATH  . 'turniket_100/';
        if (!file_exists(STORAGE_PATH  . 'turniket_100/')) {
            mkdir(STORAGE_PATH . 'turniket_100/', 0777, true);
        }

        $parts        = explode(
            ";base64,",
            $imgBase64
        );
        $imageparts   = explode("image/", @$parts[0]);
        $imagebase64  = base64_decode($imgBase64);
        $miniurl = $last_name . "+" . $first_name . "_" . $pin . '.png';
        $file = $uploadPathMK . $miniurl;

        file_put_contents($file, $imagebase64);

        // return 'storage/turniket_100/' . $miniurl;
        return 'storage/user_images/' . $pin . '.png';
    }
}
