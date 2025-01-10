<?php

namespace api\controllers;

use common\models\model\Area;
use Yii;
use base\ResponseStatus;

class AreaController extends ApiActiveController
{
    public $modelClass = 'api\resources\Area';

    public function actions()
    {
        return [];
    }

    public $table_name = 'area';
    public $controller_name = 'Area';

    public function actionIndex($lang)
    {
        $model = new Area();

        $query = $model->find()

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
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
    }

    public function actionView($lang, $id)
    {
        $model = Area::findOne($id);
            
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
    }


}
