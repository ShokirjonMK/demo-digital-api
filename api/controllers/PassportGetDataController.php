<?php

namespace api\controllers;

use api\resources\GetPasportData;
use base\ResponseStatus;
use common\models\model\Building;
use Yii;

class PassportGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public function actionIk($lang)
    {
        $birthday = Yii::$app->request->get('birthday' , null);
        $seria = Yii::$app->request->get('seria' , null);
        $number = Yii::$app->request->get('number' , null);

        $result = GetPasportData::birthdatSeriaNumber($birthday, $seria , $number);

        if ($result['is_ok']) {
            return $this->response(1, _e('Success'), $result['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionPinfl($lang)
    {
        $pinfl = Yii::$app->request->get('pinfl' , null);

        $result = GetPasportData::pinfl($pinfl);

        if ($result['is_ok']) {
            return $this->response(1, _e('Success'), $result['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
