<?php

namespace api\controllers;

use aki\telegram\base\Command;
use api\resources\StudentUser;
use common\models\model\StudentGroup;
use Yii;
use base\ResponseStatus;
use api\forms\Login;
use common\models\model\LoginHistory;
use common\models\model\StudentTimeTable;

class OpenInfoController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['permissionCheck']);
        unset($behaviors['authorCheck']);
        return $behaviors;
    }

    public function actionStudentCallSheet()
    {
        $post = Yii::$app->request->post();

        if (isRole('student')) {
            $studentId = current_student()->id;
        } else {
            $studentId = $post['student_id'];
        }
        $model = StudentGroup::findOne([
            'student_id' => $studentId,
            'edu_year_id' => $post['edu_year_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = StudentUser::studentCallSheet($model , $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

}