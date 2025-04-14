<?php

namespace api\controllers;

use api\components\MipServiceMK;
use api\components\TurniketMK;
use base\ResponseStatus;
use common\models\model\VisitorProfile;
use Yii;

class VisitorController extends ApiActiveController
{
    public $modelClass = 'api\resources\VisitorProfile';

    public function actions()
    {
        return [];
    }


    public function actionGet($pin, $document_issue_date)
    {

        $mip = MipServiceMK::getDataVisitor($pin, $document_issue_date);

        if ($mip['status']) {
            return $this->response(1, _e('Success'), $mip['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $mip['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIndex($lang)
    {
        $model = new VisitorProfile();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }

    public function actionDeleted($lang)
    {
        $model = new VisitorProfile();

        $query = $model->find()
            ->andWhere(['is_deleted' => 1]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        $model = new VisitorProfile();
        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $result = VisitorProfile::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionTurniket($id)
    {
        $visitorProfile = VisitorProfile::findOne($id);
        if (!$visitorProfile) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // dd(['visitorProfile' => $visitorProfile, 'turniket_id' => $visitorProfile->turniket_id]);
        if (!$visitorProfile->turniket_id > 0) {
            // dd("Sss");
            $data = TurniketMK::addVisitorPerson($visitorProfile);
        } else {
            $data = TurniketMK::updateVisitorPic($visitorProfile);
        }

        if ($data['status']) {
            $responses = TurniketMK::addVisitorAccessPerson($visitorProfile);
        } else {
            $responses = $this->response(0, $data['error'], null, $data, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return  $responses;
    }

    public function actionUpdate($id)
    {
        $model = VisitorProfile::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = VisitorProfile::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Visitor successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionView($id)
    {
        // Eager loading the profile relation using `with()`
        $model = VisitorProfile::find()
            ->one();

        // If the model is not found, return a 'Data not found' response
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // Add to turniket if requested
        if (Yii::$app->request->get('add_turniket') == 1) {
            return TurniketMK::addPerson($model);
        }

        // Add to turniket if requested
        if (Yii::$app->request->get('assigin_turniket') == 1) {
            $model['turniket'] = TurniketMK::addAccessPerson($model);
        }

        // Return the model data with a success response
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


    public function actionDelete($id)
    {
        $result = VisitorProfile::deleteItem($id);
        if (!is_array($result)) {
            return $this->response(1, _e('Visitor successfully deleted.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
