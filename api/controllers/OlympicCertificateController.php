<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\OlympicCertificate;
use Yii;

class OlympicCertificateController extends ApiActiveController
{
    public $modelClass = 'api\resources\OlympicCertificate';

    public function actions()
    {
        return [];
    }

    public $table_name = 'olympic_certificate';
    public $controller_name = 'OlympicCertificate';

    public function actionIndex($lang)
    {
        $model = new OlympicCertificate();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

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
        $model = new OlympicCertificate();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = OlympicCertificate::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = OlympicCertificate::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = OlympicCertificate::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = OlympicCertificate::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = OlympicCertificate::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->delete()) {
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
