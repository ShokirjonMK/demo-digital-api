<?php

namespace api\controllers;

use common\models\model\MonographBrochure;
use Yii;
use base\ResponseStatus;

class MonographBrochureController extends ApiActiveController
{
    public $modelClass = 'api\resources\MonographBrochure';

    public function actions()
    {
        return [];
    }

    public $table_name = 'monograph_brochure';
    public $controller_name = 'MonographBrochure';

    public function actionIndex($lang)
    {
        $model = new MonographBrochure();
        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0])

            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

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
        $model = new MonographBrochure();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = MonographBrochure::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = MonographBrochure::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = MonographBrochure::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = MonographBrochure::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = MonographBrochure::find()
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


    public function actionTypes()
    {
        return $this->response(1, _e('Success.'), MonographBrochure::types(), null, ResponseStatus::OK);
    }
}
