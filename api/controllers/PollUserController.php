<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Translate;
use common\models\model\PollUser;

class PollUserController extends ApiActiveController
{
    public $modelClass = 'api\resources\pollUser';

    public function actions()
    {
        return [];
    }

    public $table_name = 'poll_user';
    public $controller_name = 'PollUser';

    public function actionIndex($lang)
    {
        $model = new PollUser();

        $query = $model->find()
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            // // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
            // ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            // // ->groupBy($model->tableName() . '.id')
            // // ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            // // ->andWhere(['tr.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);


        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        // $query->orderBy(['order' => SORT_ASC]);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new PollUser();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = PollUser::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('No updating.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);

        $model = PollUser::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = PollUser::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = PollUser::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = PollUser::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // // Translate::deleteTranslate($this->table_name, $model->id);
            // $model->is_deleted = 1;
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    // public function actionType()
    // {
    //     return $this->response(1, _e('Success.'), PollUser::typeList(), null, ResponseStatus::OK);
    // }
}
