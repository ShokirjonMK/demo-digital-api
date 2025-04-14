<?php

namespace api\controllers;

use common\models\model\HostelDoc;
use Yii;
use base\ResponseStatus;

class HostelDocController extends ApiActiveController
{
    public $modelClass = 'api\resources\HostelDoc';

    public function actions()
    {
        return [];
    }

    public $table_name = 'hostel_doc';
    public $controller_name = 'HostelDoc';

    public function actionIndex($lang)
    {
        $model = new HostelDoc();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0]);


        if (isRole("student")) {
            $query = $query->andWhere([
                'student_id' => $this->student()
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new HostelDoc();

        if (!isRole("student")) {
            return $this->response(0, _e('This action is only for students.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();

        $post['student_id'] = $this->student();
        $post['user_id'] = current_user_id();

        if (isset($post['start'])) {
            $post['start'] = date('Y-m-d', strtotime($post['start']));
        }

        if (isset($post['finish'])) {
            $post['finish'] = date('Y-m-d', strtotime($post['finish']));
        }
        if (isset($post['conclution'])) {
            unset($post['conclution']);
        }
        if (isset($post['is_checked'])) {
            unset($post['is_checked']);
        }

        $this->load($model, $post);
        $result = HostelDoc::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = HostelDoc::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole("student")) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('This is not yours.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = HostelDoc::updateItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = HostelDoc::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole("student")) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('This is not yours.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionCheck($lang, $id)
    {
        $model = HostelDoc::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $result = HostelDoc::checkItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('Conformed.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionNot($lang, $id)
    {
        $model = HostelDoc::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model->is_checked = HostelDoc::IS_CHECKED_FALSE;
        $model->ball = 0;

        if ($model->save()) {
            return $this->response(1, _e('Refused.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $model->errors, ResponseStatus::BAD_REQUEST);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = HostelDoc::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            if (!isRole("student")) {
                if ($model->user_id != current_user_id()) {
                    return $this->response(0, _e('This is not yours.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            }

            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
