<?php

namespace api\controllers;

use common\models\model\KpiCategory;
use Yii;
use base\ResponseStatus;

class KpiCategoryController extends ApiActiveController
{
    public $modelClass = 'api\resources\KpiCategory';

    public function actions()
    {
        return [];
    }

    public $table_name = 'kpi_category';
    public $controller_name = 'KpiCategory';

    public function actionIndex($lang)
    {
        $model = new KpiCategory();

        $query = $model->find()
            ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("kpi_category_info kcinfo", "kcinfo.kpi_category_id = $this->table_name.id")
            ->groupBy($this->table_name . '.id')
            // ->andWhere(['kcinfo.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['kcinfo.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'kcinfo.name', Yii::$app->request->get('query')]);


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
        $model = new KpiCategory();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = KpiCategory::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = KpiCategory::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        unset($post['tab']);

        $this->load($model, $post);
        $result = KpiCategory::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = KpiCategory::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = KpiCategory::find()
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


    public function actionExtra()
    {
        return $this->response(1, _e('Success.'), KpiCategory::extra(), null, ResponseStatus::OK);
    }
}
