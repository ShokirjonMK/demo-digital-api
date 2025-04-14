<?php

namespace api\controllers;

use common\models\model\ElectionCandidate;
use Yii;
use base\ResponseStatus;

class ElectionCandidateController extends ApiActiveController
{
    public $modelClass = 'api\resources\ElectionCandidate';

    public function actions()
    {
        return [];
    }

    public $table_name = 'election_candidate';
    public $controller_name = 'ElectionCandidate';

    public function actionIndex($lang)
    {
        $model = new ElectionCandidate();

        $query = $model->find()
            // ->with(['info'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->leftJoin("election_candidate_info eci", "eci.election_candidate_id = $this->table_name.id")
            ->groupBy($this->table_name . '.id')
            // ->andWhere(['eci.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['eci.tabel_name' => 'faculty'])
            // ->andFilterWhere(['like', 'eci.name', Yii::$app->request->get('query')])
            ;


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
        $model = new ElectionCandidate();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ElectionCandidate::createItem($model, $post);
       
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ElectionCandidate::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ElectionCandidate::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ElectionCandidate::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ElectionCandidate::find()
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
