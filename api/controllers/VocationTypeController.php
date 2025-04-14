<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\VocationType;
use Yii;

/**
 * VocationTypeController implements the CRUD actions for VocationType model.
 */
class VocationTypeController extends ApiActiveController
{

    public function actions()
    {
        return [];
    }

    public $modelClass = 'api\resources\VocationType';
    public $table_name = 'vocation_type';
    public $controller_name = 'Vocation Type';

    /**
     * Lists all VocationType models.
     * @param string $lang
     * @return mixed
     */
    public function actionIndex($lang)
    {
        $model = new VocationType();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    /**
     * Creates a new VocationType model.
     * @param string $lang
     * @return mixed
     */
    public function actionCreate($lang)
    {
        $model = new VocationType();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = VocationType::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    /**
     * Updates an existing VocationType model.
     * @param string $lang
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($lang, $id)
    {
        $model = VocationType::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = VocationType::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    /**
     * Deletes an existing VocationType model.
     * @param string $lang
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($lang, $id)
    {
        $model = VocationType::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model->is_deleted = 1;
        if ($model->save()) {
            return $this->response(1, _e($this->controller_name . ' successfully deleted.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $model->errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    /**
     * Finds the VocationType model based on its primary key value.
     * @param string $lang
     * @param integer $id
     * @return mixed
     */
    public function actionView($lang, $id)
    {
        $model = VocationType::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success'), $model);
    }
}
