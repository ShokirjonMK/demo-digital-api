<?php

namespace api\controllers;

use api\resources\GetTeacher;
use Yii;
use yii\rest\ActiveController;

class GetTeacherController extends ActiveController
{

    use ApiOpen;

    public $modelClass = 'api\resources\Country';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        // return $_SERVER;
        $model = new GetTeacher();
        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            // ->select(['profile.first_name'])
        ;

        $query = $query->andWhere(['auth_assignment.item_name' => 'teacher']);

        $query->andFilterWhere(['like', 'last_name', Yii::$app->request->get('query')]);
        // $query->orFilterWhere(['like', 'first_name', Yii::$app->request->get('query')]);
        // $query->orFilterWhere(['like', 'middle_name', Yii::$app->request->get('query')]);
        /** */
        // data

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }
}
