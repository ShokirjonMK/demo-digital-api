<?php

namespace api\controllers;

use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\Citizenship;

class CitizenshipController extends ApiActiveController
{
    public $modelClass = 'api\resources\Citizenship';

    public function actions()
    {
        return [];
    }

    public $table_name = 'citizenship';
    public $controller_name = 'Citizenship';

    public function actionIndex($lang)
    {
        $model = new Citizenship();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            // ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data

        // dd(Yii::$app->request->get('query'));
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Citizenship();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Citizenship::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Citizenship::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Citizenship::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Citizenship::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Citizenship::find()
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
