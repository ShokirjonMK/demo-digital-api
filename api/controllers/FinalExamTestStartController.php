<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\FinalExam;
use common\models\model\FinalExamGroup;
use common\models\model\FinalExamTest;
use common\models\model\FinalExamTestQuestion;
use common\models\model\FinalExamTestStart;
use common\models\Subject;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class FinalExamTestStartController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'final_exam_test_start';
    public $controller_name = 'FinalExamTestStart';

    public function actionUpdate($lang , $id)
    {
        $model = FinalExamTestStart::findOne([
            'id' => $id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTestStart::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully update.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAddBall($lang , $id)
    {
        $model = FinalExamTestStart::findOne([
            'id' => $id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTestStart::addBall($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully update.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionView($lang , $id)
    {
        $model = FinalExamTestStart::findOne([
            'id' => $id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->status == 1 || $model->status == 2) {
            $isIpCheck = FinalExamTestStart::ipCheck($model);
            if (!$isIpCheck['is_ok']) {
                return $this->response(2, _e('Sizning qurilmangizga ruxsat berilmagan.'), null, null, ResponseStatus::METHOD_NOT_ALLOWED);
            }
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTestStart::view($model, $post);
        if ($result['is_ok']) {
            return $this->response(1, _e('Success.'), $result['model'], null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionStudentUpdate($lang , $id)
    {
        $student = current_student();
        $model = FinalExamTestQuestion::findOne([
            'id' => $id,
            'student_id' => $student->id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTestQuestion::studentUpdate($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Question successfully update.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }



    public function actionStudentUpdate2222($lang , $id)
    {
        $student = current_student();
        $model = FinalExamTestQuestion::findOne([
            'id' => $id,
            'student_id' => $student->id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

//        $start = $model->finalExamTestStart;
//        if ($start->status == 2) {
//            $isIpCheck = FinalExamTestStart::ipCheck($start);
//            if (!$isIpCheck['is_ok']) {
//                return $this->response(2, _e('Sizning qurilmangizga ruxsat berilmagan.'), null, null, ResponseStatus::METHOD_NOT_ALLOWED);
//            }
//        }

        $post = Yii::$app->request->post();
        $result = FinalExamTestQuestion::studentUpdate($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Question successfully update.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionGet($lang, $id)
    {
        $model = new FinalExamTestQuestion();

        $query = $model->find()->where(['is_deleted' => 0 , 'final_exam_test_start_id' => $id]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionFinish($lang , $id)
    {
        $student = current_student();
        $model = FinalExamTestStart::findOne([
            'id' => $id,
            'student_id' => $student->id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTestStart::studentFinish($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Exam test successfully finished.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionTime()
    {
        $time = [
            'time' => time()
        ];
        return $this->response(1, _e('Time.'), $time, null, ResponseStatus::OK);
    }
}