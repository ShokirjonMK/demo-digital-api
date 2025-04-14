<?php

namespace api\components;

use common\models\model\Profile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;

class FaceMK
{
    public $user_number = '';
    public $numbers_array = [];
    const UPLOADS_FOLDER = 'uploads/faceID/';

    private static $_token = 'BF9F9B0C-9273-4072-A815-A51AC905FE9A';

    public static function getToken()
    {
        return self::$_token;
    }

    public static function useFace($post)
    {
        $exam_id = $post["exam_id"] ?? null;
        $nimadi = self::saveImage($exam_id);
    }

    public static function faceCheck($pin, $document_issue_date)
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

                $photo = self::saveTo($result->photo, $result->pinpp);
                // dd(json_decode($response->getBody()->getContents()));
                // return  json_decode($response->getBody()->getContents());
                $data['status'] = true;
                $result->avatar = $photo;
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


    private static function saveTo($imgBase64, $pin)
    {
        // Define the upload path for storing images
        $uploadPathMK = STORAGE_PATH . 'user_images_new/';

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
        $filename = $pin . '.png';

        // Construct the full file path
        $file = $uploadPathMK . $filename;

        // Save the decoded image data to the file
        file_put_contents($file, $imagebase64);

        // Return the relative URL of the saved image
        return 'storage/user_images_new/' . $filename;
    }
}
