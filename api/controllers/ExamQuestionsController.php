<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\EduSemestr;
use common\models\model\ExamQuestions;
use common\models\model\Room;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduYear;
use common\models\model\Para;
use common\models\model\Semestr;
use common\models\model\TimeTable1;
use common\models\model\Week;

class ExamQuestionsController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_questions';
    public $controller_name = 'ExamQuestions';

    public function actionIndex($lang)
    {
        $model = new ExamQuestions();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andFilterWhere(['like', 'text', Yii::$app->request->get('query')]);

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
        $model = new ExamQuestions();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = ExamQuestions::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamQuestions::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->is_confirm == ExamQuestions::CONFIRM) {
            return $this->response(0, _e('Confirmed information cannot be changed.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ExamQuestions::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIsConfirm($lang, $id)
    {
        $model = ExamQuestions::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamQuestions::updateConfirm($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamQuestions::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamQuestions::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->save(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
