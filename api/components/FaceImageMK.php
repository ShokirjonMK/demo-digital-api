<?php

namespace app\components;

use Yii;
use yii\web\UploadedFile;
use GuzzleHttp\Client;

class FaceImageMK
{
    const UPLOADS_FOLDER = 'uploads/faceID/';

    public function checkFaceIDTest($exam_id = 99999)
    {

        $uploadedImage = $this->saveImage($exam_id);
        // if $uploadedImage is null then return error "Image not saved"

        $profileImagePath = $this->getUserProfileImagePath();
        // if $uploadedImage is null then return error "Profile image not found"

        // Make the POST request to another URL with the image paths
        $response = $this->sendImagesNew($uploadedImage, $profileImagePath);

        if ($response->status == 1) {
            return $response;
        }


        return null;
    }

    public function checkFaceIDOld($exam_id)
    {
        $uploadedImage = $this->saveImage($exam_id);
        $profileImagePath = $this->getUserProfileImagePath();

        // return $uploadedImage;
        // Make the POST request to another URL with the image paths
        // $response = $this->sendImagesNew($uploadedImage, $profileImagePath);
        $response = $this->sendImagesOther($uploadedImage, $profileImagePath);

        $result = [];

        // dd($response);

        // [status] => 1
        // [result] => 39.77
        // [mask_detection] => 0
        // [glasses_detection] => 0

        // Handle the response as needed
        if ($response->status) {
            // Success
            $result  = $response;
            // $result['status'] = true;
            // $result['result'] = $response->result;
            // // $result = $response->data;
            // $result['mask_detection'] = $response->glasses_detection;
            // $result['glasses_detection'] = $response->glasses_detection;

            // Process $result['status'], $result['message'], $result['result'], etc.
        } else {
            // Handle error
            $result['status'] = false;
            $errors = $response->data;
            $result['message'] = $response->message;


            // Process $errors
        }

        return $result;
    }

    public function checkFaceID($exam_id)
    {
        $uploadedImage = $this->saveImage($exam_id);
        $profileImagePath = $this->getUserProfileImagePath();

        // return $uploadedImage;
        // Make the POST request to another URL with the image paths
        // $response = $this->sendImagesNew($uploadedImage, $profileImagePath);
        $response = $this->sendImagesOther($uploadedImage, $profileImagePath);
        return $response;

        $result = [];


        // Handle the response as needed
        if ($response->status) {
            // Success
            $result  = $response;
            // $result['status'] = true;
            // $result['result'] = $response->result;
            // // $result = $response->data;
            // $result['mask_detection'] = $response->glasses_detection;
            // $result['glasses_detection'] = $response->glasses_detection;

            // Process $result['status'], $result['message'], $result['result'], etc.
        } else {
            // Handle error
            $result['status'] = false;
            $errors = $response->data;
            $result['message'] = $response->message;


            // Process $errors
        }

        return $result;
    }

    protected function uploadImage()
    {
        $imageFile = UploadedFile::getInstanceByName('imageFile'); // Assuming the form input name is 'imageFile'

        if ($imageFile !== null) {
            $uploadPath = 'path/to/save/'; // Specify your upload path
            $filename = $uploadPath . $imageFile->baseName . '.' . $imageFile->extension;

            if ($imageFile->saveAs($filename)) {
                return $filename;
            }
        }

        return null;
    }

    protected function saveImage($exam_id)
    {
        $uploadFolderUrl = self::UPLOADS_FOLDER . $exam_id . '/';
        if (!file_exists(STORAGE_PATH . $uploadFolderUrl)) {
            mkdir(STORAGE_PATH . $uploadFolderUrl, 0777, true);
        }


        // get fom post request base64 image and save it
        $base64Image = Yii::$app->request->post('base64Image');


        if ($base64Image) {

            // Split the base64 image data into parts
            // $parts = explode(";base64,", $imgBase64);

            // // Extract the image type from the parts
            // $imageparts = explode("image/", @$parts[0]);

            // // Decode the base64 image data
            // $imagebase64 = base64_decode($imgBase64);



            // Decode the base64 image data
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

            // Specify the upload path and filename
            $filename = $uploadFolderUrl . current_user_id() . '_' . time() . '.png';

            // Save the image file
            if (file_put_contents(STORAGE_PATH . $filename, $imageData)) {
                return "storage/" . $filename;
            }
        }


        $imageFile = UploadedFile::getInstanceByName('imageFile');

        if ($imageFile !== null) {
            $uploadPath = $uploadFolderUrl; // Specify your upload path

            $filename = $uploadPath . current_user_id() . '_' . time() . '.' . $imageFile->extension;

            if ($imageFile->saveAs(STORAGE_PATH . $filename)) {
                return "storage/" . $filename;
            }
        }

        return null;
    }


    private static function saveImageBase64($imgBase64, $exam_id)
    {
        // $imgBase64 = '';
        $uploadFolredUrl = STORAGE_PATH . self::UPLOADS_FOLDER . $exam_id . '/';
        if (!file_exists($uploadFolredUrl)) {
            mkdir($uploadFolredUrl, 0777, true);
        }

        // Split the base64 image data into parts
        $parts = explode(";base64,", $imgBase64);

        // Extract the image type from the parts
        $imageparts = explode("image/", @$parts[0]);

        // Decode the base64 image data
        $imagebase64 = base64_decode($imgBase64);

        // Generate a unique filename using the provided PIN
        $filename = current_user_id() . '_' . time() . '.png';

        // Construct the full file path
        $file = $uploadFolredUrl . $filename;

        // Save the decoded image data to the file
        file_put_contents($file, $imagebase64);

        // Return the relative URL of the saved image
        return 'storage/user_images_new/' . $filename;
    }

    protected function getUserProfileImagePath()
    {
        // Logic to get the current user's profile image path from the Profile table
        // Replace the following line with your actual logic
        return Yii::$app->user->identity->profile->image;
    }

    protected function sendImagesNew($uploadedImage, $profileImagePath)
    {
        $data = [];
        $error = [];
        $data['status'] = false;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                // 'Api-token' => self::getAuthToken(),
                'Authorization' => 'Bearer ' . self::getAuthToken()
            ]
        ]);

        $real_image = "https://api-digital.tsul.uz/" . $uploadedImage;
        $origin_image = "https://api-digital.tsul.uz/" . $profileImagePath;
        // dd($real_image);
        $response = $client->post(
            'http://192.168.100.77:8005/check-face',
            ['body' => json_encode(
                [
                    // "origin_image" => "https://api-digital.tsul.uz/storage/user_images_new/62608035980016.png",
                    // "real_image" => "https://api-digital.tsul.uz/storage/user_images_new/43110976520017.png"
                    // "real_image" => "https://api-digital.tsul.uz/storage/uploads/faceID/1337/1617_1713252075.png",
                    'real_image' =>  $real_image,
                    'origin_image' =>  $origin_image
                ]
            )]
        );

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            $data['status'] = false;
            return $data;
        }
    }

    protected function sendImagesOther($uploadedImage, $profileImagePath)
    {
        $data = [];
        $error = [];
        $data['status'] = false;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                // 'Api-token' => self::getAuthToken(),
                // 'Authorization' => 'Bearer ' . self::getAuthToken()
            ]
        ]);

        $real_image = "https://api-digital.tsul.uz/" . $uploadedImage;
        $origin_image = "https://api-digital.tsul.uz/" . $profileImagePath;
        // dd($real_image);
        $response = $client->post(
            'http://192.168.100.77:8555/compare-faces',
            // 'http://192.168.100.77:8005/check-face',
            ['body' => json_encode(
                [
                    // "url1" => "https://api-digital.tsul.uz/storage/user_images_new/43110976520017.png",
                    // "url2" => "https://api-digital.tsul.uz/storage/user_images_new/40102941670048.png"

                    // "url1" => "https://api-digital.tsul.uz/storage/uploads/faceID/xushnudbek.jpg",
                    // "url2" => "https://api-digital.tsul.uz/storage/uploads/faceID/xushnudbek2.jpg"


                    // // "real_image" => "https://api-digital.tsul.uz/storage/uploads/faceID/1337/1617_1713252075.png",

                    'url1' =>  $real_image,
                    'url2' =>  $origin_image

                    // 'real_image' =>  $real_image,
                    // 'origin_image' =>  $origin_image
                ]
            )]
        );

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            //   dd($response);      
            $data['status'] = false;
            return $data;
        }
    }


    protected function sendImages($uploadedImage, $profileImagePath)
    {
        $httpClient = new \yii\httpclient\Client();
        $url = "https://api-digital.tsul.uz/";
        $request = $httpClient->createRequest()
            ->setMethod('post')
            // ->setUrl('http://192.168.100.77:8000/api')
            ->setUrl('http://192.168.100.77:8005/check-face')
            ->setData([
                'real_image' => $url . $uploadedImage,
                'origin_image' => $url . $profileImagePath
            ]);

        // Add authorization bearer token to the request
        $request->addHeaders(['Authorization' => 'Bearer ' . self::getAuthToken()]);

        $response = $request->send();

        return $response;
    }

    protected static function getAuthToken()
    {
        // Logic to get the authorization bearer token
        // Replace the following line with your actual logic
        return '768bff963d269bd014be0d160368205a082f0bec';
    }


    protected function sendImagesss($uploadedImage, $profileImagePath)
    {
        // Logic to send images to another URL and get the response
        // You can use Yii's HTTP client or any other preferred method

        // Example using Yii's HTTP client
        $httpClient = new \yii\httpclient\Client();

        $response = $httpClient->createRequest()
            ->setMethod('post')
            ->setUrl('http://192.168.100.77:8000/api')
            ->setData([
                'uploadedImage' => $uploadedImage,
                'profileImagePath' => $profileImagePath
            ])
            ->send();

        return $response;
    }
}
