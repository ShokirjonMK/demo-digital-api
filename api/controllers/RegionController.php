<?php

namespace api\controllers;

use common\models\model\Region;
use Yii;
use base\ResponseStatus;

class RegionController extends ApiActiveController
{
    public $modelClass = 'api\resources\Region';

    public function actions()
    {
        return [];
    }

    public $table_name = 'region';
    public $controller_name = 'Region';

    public function actionIndex($lang)
    {
        $model = new Region();

        $countryId = Yii::$app->request->get('country_id');
        // return $countryId;

        $query = $model->find()
            ->andFilterWhere(['like','name', Yii::$app->request->get('query')]);

        ;

        // filter
        // $query = $this->filterAll($query, $model);
        if (isset($countryId)) {
            if ((int)$countryId != 229) {
                // $query = $query->andFilterWhere(['!=', 'country_id',  229]);
                $query = $query->andFilterWhere(['in', 'id', [15]]);
            }
        }

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
        $model = Region::findOne($id);

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
