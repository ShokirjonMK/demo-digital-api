<?php

namespace api\controllers;

use common\models\model\ExamTest;
use Yii;
use base\ResponseStatus;
use common\models\model\Test;

class TestController extends ApiController
{
    public function actions()
    {
        return [];
    }

    public $table_name = 'test';
    public $controller_name = 'Test';

    public function actionIndex($lang)
    {
        $model = new Test();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);
        // filter
        $query = $this->filterAll($query, $model);
        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionExcelImport($lang) {
        $post = Yii::$app->request->post();
        $result = Test::createExcelImport($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCreate($lang)
    {
        $model = new Test();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Test::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIsCheck($lang , $id) {
        $model = Test::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Test::ischeck($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Test::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model->topic_id = null;
        $model->subject_id = null;
        $model->exam_type_id = null;
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Test::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Test::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


    public function actionAllDelete($lang, $id)
    {
        $model = new Test();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0 , 'subject_id' => $id]);

        if (isset($post['lang_id'])) {
            $query = $query->andWhere(['lang_id' => $post['lang_id']]);
        }

        $query = $query->all();

        if (count($query) == 0) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = Test::allDelete($query);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully delete.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = Test::findone([
            'id' => $id,
            'is_deleted' => 0
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->save(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
