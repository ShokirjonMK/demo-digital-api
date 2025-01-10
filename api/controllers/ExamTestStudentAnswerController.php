<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\ExamControlStudent;
use common\models\model\ExamTestStudentAnswer;
use common\models\model\Faculty;
use common\models\model\Subject;
use Yii;
use yii\rest\ActiveController;

class ExamTestStudentAnswerController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControlStudent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_test_student_answer';
    public $controller_name = 'ExamTestStudentAnswer';


    public function actionIndex($lang)
    {
        $model = new ExamTestStudentAnswer();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('student')) {
            $query->andWhere(['user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);


        // dd($query->createCommand()->getRawSql());

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionDesignation($lang) {
        $post = Yii::$app->request->post();
        $result = ExamTestStudentAnswer::designation($post);
        if ($result) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamControlStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControlStudent::find()
            // ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        $model->delete();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
