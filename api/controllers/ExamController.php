<?php

namespace api\controllers;

use common\models\model\ExamStudent;
use Yii;
use base\ResponseStatus;
use common\models\model\Exam;
use common\models\model\Faculty;
use common\models\model\Student;

class ExamController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControl';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam';
    public $controller_name = 'Exam';

    public function actionIndex($lang)
    {
        $model = new Exam();
        $query = $model->find()->andWhere([$this->table_name . '.is_deleted' => 0]);
        if (isRole('student')) {
            $query->andWhere([
               'in' , 'status' , [
                    Exam::STATUS_STARTED,
                    Exam::STATUS_FINISHED,
                    Exam::STATUS_ALLOTMENT,
                    Exam::STATUS_NOTIFY
                ]
            ]);
        }

        if (isRole('teacher')) {
            $query->andWhere(['in' , 'id' , ExamStudent::find()
                ->select('exam_id')
                ->where([
                    'exam_teacher_user_id' => current_user_id(),
                    'is_deleted' => 0
                ])]);
        }

        // filter
        $query = $this->filterAll($query, $model);
        // sort
        $query = $this->sort($query);
        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Exam();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        if (isset($post['start'])) {
            $model['start_time'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish_time'] = strtotime($post['finish']);
        }
        $result = Exam::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Exam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if ($model == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Exam::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionExamFinish($lang, $id)
    {
        $model = Exam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if ($model == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Exam::examFinish($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAllotment($lang, $id)
    {
        $model = Exam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if ($model == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Exam::examAllotment($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionExamCheck($lang, $id)
    {
        $model = Exam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if ($model == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Exam::examCheck($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionExamNotify($lang, $id)
    {
        $model = Exam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if ($model == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Exam::examNotify($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionExamTeacherAttach($lang , $id) {
        $model = Exam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if ($model == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status < Exam::STATUS_FINISHED) {
            return $this->response(0, _e('Wait for the exam to finish!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();
        $result = Exam::examTeacherAttach($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Exam::find()->where([ 'id' => $id, 'is_deleted' => 0 ])->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Exam::find()->where([ 'id' => $id, 'is_deleted' => 0 ])->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        // remove model
        if ($model) {
            $model->status = Exam::STATUS_INACTIVE;
            $model->is_deleted = 1;
            $model->save(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
