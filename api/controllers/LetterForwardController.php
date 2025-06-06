<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\Document;
use common\models\model\DocumentFiles;
use common\models\model\EduSemestr;
use common\models\model\Letter;
use common\models\model\LetterFiles;
use common\models\model\LetterForward;
use common\models\model\LetterView;
use common\models\model\Room;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduYear;
use common\models\model\Para;
use common\models\model\Semestr;
use common\models\model\TimeTable1;
use common\models\model\Week;

class LetterForwardController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'letter_forward';
    public $controller_name = 'LetterForward';

    public function actionIndex($lang)
    {
        $model = new LetterForward();

        $query = $model->find()->andWhere([
            'is_deleted' => 0,
        ]);

        if (!isRole('doc_admin') && !isRole('admin')) {
            $query->andWhere([
                'user_id' => current_user_id(),
                'status' => 1
            ]);
        }

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
        $model = new LetterForward();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $model->start_date = time();
        if (isset($post['end'])) {
            $model->end_date = strtotime($post['end']);
        }

        $result = LetterForward::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = LetterForward::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        if (isset($post['end'])) {
            $model->end_date = strtotime($post['end']);
        }

        $result = LetterForward::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = LetterForward::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->user_id == current_user_id() && $model->view_type == LetterForward::VIEW_TYPE_FALSE) {
            $model->view_type = LetterForward::VIEW_TYPE_TRUE;
            $model->view_date = time();
            $model->update(false);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = LetterForward::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
