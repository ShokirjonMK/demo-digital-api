<?php

namespace api\controllers;

use Yii;

trait ResponseTrait
{
    protected function response($status, $message, $data = null, $errors = null, $responsStatusCode = 200)
    {
        Yii::$app->response->statusCode = $responsStatusCode;
        $response = [
            'status' => $status,
            'message' => $message
        ];
        if($data) $response['data'] = $data; 
        if($errors) $response['errors'] = $errors; 
        return $response;
    }
}
