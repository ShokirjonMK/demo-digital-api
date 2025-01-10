<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Department;
use common\models\model\Translate;
use common\models\model\UserAccess;

class DepartmentController extends ApiActiveController
{
    public $modelClass = 'api\resources\Department';

    const ROLE = 'dep_lead';


    public function actions()
    {
        return [];
    }

    public $table_name = 'department';
    public $controller_name = 'Department';

    const USER_ACCESS_TYPE_ID = 3;

    public function actionUserAccess($lang)
    {
        $post = Yii::$app->request->post();
        $result = UserAccess::createItems(self::USER_ACCESS_TYPE_ID, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Users successfully atached to ' . $this->controller_name), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionTypes($lang,$key = null)
    {
        $model = new Department();
        return $model->typesArray($key);
    }

    public function actionIndex($lang)
    {
        $model = new Department();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
//            ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')])
            ->andFilterWhere(['<', $this->table_name.'.type', Yii::$app->request->get('key')])
            ->andFilterWhere(['=', $this->table_name.'.type', Yii::$app->request->get('type')]);

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
        $model = new Department();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Department::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Department::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        
//        if (!$this->checkLead($model, self::ROLE)) {
//            return $this->response(0, _e('You can not change.'), null, null, ResponseStatus::FORBIDDEN);
//        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Department::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Department::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Department::find()
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
