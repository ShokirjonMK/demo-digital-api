<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\StudentServiceType;
use yii\caching\DbDependency;

class StudentServiceTypeController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentServiceType';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_service_type';
    public $controller_name = 'StudentServiceType';

    public function actionIndex($lang)
    {
        $model = new StudentServiceType();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        if (Yii::$app->request->get('category')) {
            if (Yii::$app->request->get('category') == 2) {
                $query->andWhere([$model->tableName() . '.category' => 2]);
            }
        } else {
            $query->andWhere([$model->tableName() . '.category' => 1]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM student_service_type']));

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new StudentServiceType();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentServiceType::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentServiceType::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = StudentServiceType::updateItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentServiceType::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentServiceType::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model->is_deleted = 1;
        $model->update();

        return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::OK);
    }

    public function actionType()
    {
        return $this->response(1, _e('Success.'), StudentServiceType::typeList(), null, ResponseStatus::OK);
    }
}
