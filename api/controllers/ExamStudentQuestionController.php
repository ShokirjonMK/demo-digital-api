<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\ExamControlStudent;
use common\models\model\ExamStudentQuestion;
use common\models\model\ExamTestStudentAnswer;
use common\models\model\Faculty;
use common\models\model\Subject;
use Yii;
use yii\rest\ActiveController;

class ExamStudentQuestionController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControlStudent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student_question';
    public $controller_name = 'ExamStudentQuestion';


    public function actionIndex($lang)
    {
        $model = new ExamStudentQuestion();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        $examStudent = Yii::$app->request->get('exam_student_id');
        if (isset($examStudent)) {
            $query->andWhere(['exam_student_id' => Yii::$app->request->get('exam_student_id')]);
        } else {
            $query->andWhere(['is_deleted' => -2]);
        }
        if (isRole('student')) {
            $query->andWhere(['user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionUpdate($lang,$id) {
        $model = ExamStudentQuestion::findOne([
            'id' => $id,
            'student_user_id' => current_user_id(),
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamStudentQuestion::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdateBall($lang , $id) {
        $model = ExamStudentQuestion::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamStudentQuestion::updateBall($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamStudentQuestion::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamStudentQuestion::find()
             ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        $model->is_deleted = 1;
        $model->save(false);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
