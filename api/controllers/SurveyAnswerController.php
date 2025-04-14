<?php

namespace api\controllers;

use common\models\model\SurveyAnswer;
use Yii;
use base\ResponseStatus;

class SurveyAnswerController extends ApiActiveController
{
    public $modelClass = 'api\resources\SurveyAnswer';

    public function actions()
    {
        return [];
    }

    public $table_name = 'survey_answer';
    public $controller_name = 'SurveyAnswer';

    public function actionIndex($lang)
    {
        $model = new SurveyAnswer();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

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
        $model = new SurveyAnswer();
        $post = Yii::$app->request->post();

        $post['user_id'] = current_user_id();
        $post['student_id'] = $this->student();
        // $post['student_id'] = 2;

        if (isset($post['answers'])) {
            $result = SurveyAnswer::createItems($post);

            if (isset($result['status'])) {
                if ($result['status']) {
                    return $this->response(1, _e($this->controller_name . 's successfully created.'), null, null, ResponseStatus::CREATED);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            $this->load($model, $post);
            $result = SurveyAnswer::createItem($model, $post);
        }


        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = SurveyAnswer::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = SurveyAnswer::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = SurveyAnswer::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = SurveyAnswer::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
