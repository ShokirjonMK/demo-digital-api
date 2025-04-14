<?php

namespace api\controllers;

use common\models\model\KpiData;
use Yii;
use base\ResponseStatus;

class KpiDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\KpiData';

    public function actions()
    {
        return [];
    }

    public $table_name = 'kpi_data';
    public $controller_name = 'KpiData';

    public function actionIndex($lang)
    {
        $model = new KpiData();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0])

            ->andFilterWhere(['like', 'link', Yii::$app->request->get('query')]);

        if (isRole('teacher') && !isRole('mudir')) {
            $query->andWhere([$this->table_name . '.user_id' => current_user_id()]);
        }

        if (Yii::$app->request->get('user_id') != null) {
            $query->andWhere([$this->table_name . '.user_id' => Yii::$app->request->get('user_id')]);
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
        $model = new KpiData();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = KpiData::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = KpiData::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = KpiData::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = KpiData::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = KpiData::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            if (isRole('teacher') && !($model->user_id == current_user_id())) {
                return $this->response(0, _e('This is not yours.'), null, null, ResponseStatus::BAD_REQUEST);
            }

            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
