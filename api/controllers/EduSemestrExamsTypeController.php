<?php

namespace api\controllers;

use common\models\model\EduSemestrExamsType;
use Yii;
use base\ResponseStatus;

class EduSemestrExamsTypeController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestrExamsType';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new EduSemestrExamsType();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

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
        $model = new EduSemestrExamsType();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestrExamsType::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('EduSemestrExamsType successfully created.'), $model, null, ResponseStatus::CREATED);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduSemestrExamsType::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestrExamsType::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('EduSemestrExamsType successfully updated.'), $model, null, ResponseStatus::OK);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduSemestrExamsType::find()
            ->andWhere(['id' => $id])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = EduSemestrExamsType::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = EduSemestrExamsType::findOne($id);

        if($result){
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('EduSemestrExamsType succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

}
