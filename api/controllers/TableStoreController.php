<?php

namespace api\controllers;

use common\models\model\TableStore;
use Yii;
use base\ResponseStatus;
use common\models\model\Holiday;
use common\models\model\Vocation;

class TableStoreController extends ApiActiveController
{
    public $modelClass = 'api\resources\TableStore';

    public function actions()
    {
        return [];
    }

    public $table_name = 'tabel_store';
    public $controller_name = 'TableStore';

    public function actionIndex($lang)
    {
        $model = new TableStore();

        $month = Yii::$app->request->get('month') ?? (int)date('m');
        $year = Yii::$app->request->get('year') ?? date('Y');
        // $type = Yii::$app->request->get('type');
        // $user_access_type_id = Yii::$app->request->get('user_access_type_id');
        // $table_id = Yii::$app->request->get('table_id');

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')])
        ;

        if (Yii::$app->request->get('month') != null) {
            $query->andFilterWhere(['month' => $month]);
        }

        if (Yii::$app->request->get('year') != null) {
            $query->andFilterWhere(['year' => $year]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data['data'] =  $this->getData($query);

        $data['vocations'] = Vocation::filter($year, $month);
        $data['holidays'] = Holiday::filter($year, $month);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new TableStore();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = TableStore::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = TableStore::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TableStore::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TableStore::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $data['data'] = $model;

        $data['vocations'] = Vocation::filter($model->year, $model->month);
        $data['holidays'] = Holiday::filter($model->year, $model->month);

        return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TableStore::find()
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
