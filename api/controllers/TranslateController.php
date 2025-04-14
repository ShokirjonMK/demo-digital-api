<?php

namespace api\controllers;

use common\models\model\Translate;
use Yii;
use base\ResponseStatus;


class TranslateController extends ApiActiveController
{
    public $modelClass = 'api\resources\Translate';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Translate();

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
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        // $model = new Translate();
        // $post = Yii::$app->request->post();
        // $this->load($model, $post);
        // $result = Translate::createItem($model, $post);
        // if(!is_array($result)){
        //     return $this->response(1, _e('Translate successfully created.'), $model, null, ResponseStatus::CREATED);
        // }else{
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        // }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);

        // $model = Translate::findOne($id);
        // if(!$model){
        //     return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        // }
        // $post = Yii::$app->request->post();
        // $this->load($model, $post);
        // $result = Translate::updateItem($model, $post);
        // if(!is_array($result)){
        //     return $this->response(1, _e('Translate successfully updated.'), $model, null, ResponseStatus::OK);
        // }else{
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        // }
    }

    public function actionView($lang, $id)
    {
        $model = Translate::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Translate::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = Translate::findOne($id);


        if($result){
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('Translate succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

}
