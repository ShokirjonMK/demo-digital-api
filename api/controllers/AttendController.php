<?php

namespace api\controllers;

use common\models\model\Attend;
use Yii;
use base\ResponseStatus;

class AttendController extends ApiActiveController
{
    public $modelClass = 'api\resources\Attend';

    public function actions()
    {
        return [];
    }

    public $table_name = 'attend';
    public $controller_name = 'Attend';

    public function actionIndex($lang)
    {
        $model = new Attend();

        $query = $model->find()
            // ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
        ;

        // if (isRole('student')) {
        //     $query->andWhere([$this->table_name . '.student_id' => $this->student()]);
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
        $post = Yii::$app->request->post();
        if (!isset($post['date'])) {
            $post['date'] = date('Y-m-d H:i:s');
        } else{
            $post['date'] = date('Y-m-d H:i:s', strtotime($post['date']));
        }

        $result = Attend::createItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Attend::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $student_ids = $model->student_ids;
        unset($post['date']);

        unset($post['subject_id']);
        unset($post['subject_category_id']);
        unset($post['time_option_id']);
        unset($post['edu_year_id']);
        unset($post['edu_semestr_id']);
        unset($post['faculty_id']);
        unset($post['edu_plan_id']);
        unset($post['type']);
        unset($post['semestr_id']);
        
        $this->load($model, $post);
        $result = Attend::updateItem($model, $post, $student_ids);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Attend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Attend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            // $model->delete();
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
