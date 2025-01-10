<?php

namespace api\controllers;

use api\resources\AccessControl;
use api\resources\User;
use base\ResponseStatus;
use common\models\model\Excel;
use Yii;

use yii\web\UploadedFile;
use function PHPSTORM_META\type;

class ExcelController extends ApiController
{

    public function actions()
    {
        return [];
    }

    public function actionIkExcel() {
        $model = new Excel();
        $post = Yii::$app->request->post();
        $result = Excel::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

}
