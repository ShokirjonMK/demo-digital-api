<?php

namespace api\controllers;

use common\models\model\LoginHistory;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class LoginHistoryController extends ApiActiveController
{
    public $modelClass = 'api\resources\LoginHistory';

    public function actions()
    {
        return [];
    }

    public $table_name = 'login_history';
    public $controller_name = 'LoginHistory';

    public function actionIndex($lang)
    {
        $model = new LoginHistory();

        $query = $model->find();

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        $query->orderBy(['id' => SORT_DESC]);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionSelf($lang)
    {

        $model = new LoginHistory();

        $query = $model->find()
            ->andWhere([$this->table_name . '.user_id' => current_user_id()]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        $query->orderBy(['id' => SORT_DESC]);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    // public function actionViews($lang, $id)
    // {

    //     $model = new LoginHistory();

    //     $query = $model->find()
    //         ->andWhere([$this->table_name . '.user_id' => $id]);

    //     // filter
    //     $query = $this->filterAll($query, $model);

    //     // sort
    //     $query = $this->sort($query);
    //     // $query->orderBy(['order' => SORT_ASC]);

    //     // data
    //     $data =  $this->getData($query);
    //     return $this->response(1, _e('Success'), $data);
    // }

    public function actionView($lang, $id)
    {
        $model = LoginHistory::findOne($id);


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = LoginHistory::findOne($id);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
