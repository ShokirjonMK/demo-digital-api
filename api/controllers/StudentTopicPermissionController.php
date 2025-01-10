<?php

namespace api\controllers;

use common\models\model\StudentTopicPermission;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;

class StudentTopicPermissionController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduType';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_topic_permission';
    public $controller_name = 'StudentTopicPermission';

    public function actionIndex($lang)
    {
        $model = new StudentTopicPermission();

        $query = $model->find()->andWhere(['is_deleted' => 0]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

//    public function actionCreate($lang)
//    {
//        $model = new StudentTopicPermission();
//        $post = Yii::$app->request->post();
//
//        $result = StudentTopicPermission::createItem($model, $post);
//        if (!is_array($result)) {
//            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
//        } else {
//            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
//        }
//    }

    public function actionUpdate($lang)
    {
        $model = new StudentTopicPermission();

        $post = Yii::$app->request->post();

        $result = StudentTopicPermission::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionPermission($lang) {
        $model = new StudentTopicPermission();

        $post = Yii::$app->request->post();

        $result = StudentTopicPermission::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentTopicPermission::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentTopicPermission::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
