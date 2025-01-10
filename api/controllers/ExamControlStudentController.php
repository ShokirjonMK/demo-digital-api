<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\ExamControlStudent;
use common\models\model\Faculty;
use common\models\model\Subject;
use Yii;
use yii\rest\ActiveController;

class ExamControlStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControlStudent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_control_student';
    public $controller_name = 'ExamControlStudent';


    public function actionIndex($lang)
    {
        $model = new ExamControlStudent();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('student')) {
            $query->andWhere([
                'is_checked' => 1,
                'student_user_id' => current_user_id()
            ]);
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

    public function actionCreate($lang)
    {
        $model = new ExamControlStudent();
        $post = Yii::$app->request->post();
        $data = [];
        if (isRole('student')) {
            if (isset($post['exam_control_id'])) $data['exam_control_id'] = $post['exam_control_id'];
            if (isset($post['upload2_file'])) $data['upload2_file'] = $post['upload2_file'];
            if (isset($post['upload_file'])) $data['upload_file'] = $post['upload_file'];
            if (isset($post['answer2'])) $data['answer2'] = $post['answer2'];
            if (isset($post['answer'])) $data['answer'] = $post['answer'];

            $this->load($model, $data);
            $result = ExamControlStudent::createItem($model, $data);
        } else {
            // if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
            if (isset($post['upload2_file'])) unset($post['upload2_file']);
            if (isset($post['upload_file'])) unset($post['upload_file']);
            if (isset($post['answer2'])) unset($post['answer2']);
            if (isset($post['answer'])) unset($post['answer']);
            if (isset($post['main_ball'])) unset($post['main_ball']);

            $this->load($model, $post);
            $result = ExamControlStudent::createItem($model, $post);
        }
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamControlStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $data = [];
        $post = Yii::$app->request->post();

        if (isset($post['ball'])) {
            if (!is_null($model->ball)) {
                return $this->response(0, _e('Can not change ball.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        if (isset($post['ball2'])) {
            if (!is_null($model->ball2)) {
                return $this->response(0, _e('Can not change ball.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        if (isRole('student')) {
            if ($model->student_id != $this->student()) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
            if (isset($post['exam_control_id'])) $data['exam_control_id'] = $post['exam_control_id'];
            if (isset($post['upload2_file'])) $data['upload2_file'] = $post['upload2_file'];
            if (isset($post['upload_file'])) $data['upload_file'] = $post['upload_file'];
            if (isset($post['answer2'])) $data['answer2'] = $post['answer2'];
            if (isset($post['answer'])) $data['answer'] = $post['answer'];

            $this->load($model, $data);
            $result = ExamControlStudent::updateItem($model, $data);
        } else {
            if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
            if (isset($post['upload2_file'])) unset($post['upload2_file']);
            if (isset($post['upload_file'])) unset($post['upload_file']);
            if (isset($post['answer2'])) unset($post['answer2']);
            if (isset($post['answer'])) unset($post['answer']);
            if (isset($post['main_ball'])) unset($post['main_ball']);

            $this->load($model, $post);
            $result = ExamControlStudent::updateItem($model, $post);
        }

        // $this->load($model, $post);
        // $result = ExamControlStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionRating($lang , $id) {
        $model = ExamControlStudent::findOne([
            'id' => $id,
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        $result = ExamControlStudent::rating($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionFinish($lang , $id) {
        $model = ExamControlStudent::findOne([
            'id' => $id,
            'student_user_id' => current_user_id(),
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        $result = ExamControlStudent::studentFileUpload($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCheck($lang, $id)
    {
        $model = ExamControlStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamControlStudent::updateCheck($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        if (isRole('student')) {
            $query = ExamControlStudent::find()
                ->where([
                    'id' => $id,
                    'student_user_id' => current_user_id(),
                    'is_deleted' => 0
                ])
                ->one();
        } else {
            $query = ExamControlStudent::find()
                ->where([
                    'id' => $id,
                    'is_deleted' => 0
                ])
                ->one();
        }
        if ($query == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole('student')) {
            if ($query->start_time == null) {
                $query->start_time = time();
                $query->finish_time = strtotime('+'. $query->duration .' minutes' , $query->start_time);
                $query->save(false);
            }
        }
        return $this->response(1, _e('Success.'), $query, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControlStudent::find()
             ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        $model->delete();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
