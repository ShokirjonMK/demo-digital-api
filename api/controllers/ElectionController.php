<?php

namespace api\controllers;

use common\models\model\Election;
use Yii;
use base\ResponseStatus;
use common\models\model\ElectionPass;

class ElectionController extends ApiActiveController
{
    public $modelClass = 'api\resources\Election';

    public function actions()
    {
        return [];
    }

    public $table_name = 'election';
    public $controller_name = 'Election';

    public function actionIndex($lang)
    {
        $model = new Election();

        $query = $model->find()
            ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            // ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['tr.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);
            


        // if (!isRole('admin')) {
        //     $query = $query->andWhere(['in', 'role', current_user_roles_array()]);
        // }

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
        $model = new Election();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Election::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Election::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Election::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Election::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }


        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
        if ($model->password == Yii::$app->request->get('password')) {
            return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('Incorrect password'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionPassword($lang, $id)
    {
        $model = ElectionPass::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model->password = _passwordMK(6);
        if ($model->save()) {
            return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('Error occured.'), null, null, ResponseStatus::NOT_FOUND);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = Election::find()
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
