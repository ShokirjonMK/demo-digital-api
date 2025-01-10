<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\Document;
use common\models\model\DocumentFiles;
use common\models\model\EduSemestr;
use common\models\model\Letter;
use common\models\model\LetterFiles;
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

class LetterController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'letter';
    public $controller_name = 'Letter';

    public function actionIndex($lang)
    {
        $model = new Letter();

        $query = $model->find()->andWhere(['is_deleted' => 0]);

        if (isRole('rector')) {
            $query->andWhere(['status' => 1]);
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
        $model = new Letter();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Letter::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Letter::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status == 1) {
            return $this->response(0, _e('The data cannot be changed.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Letter::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIsOk($lang, $id)
    {
        $model = Letter::find()
            ->andWhere([
                'id' => $id, 'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status != 1) {
            return $this->response(0, _e('An email that has not been sent for confirmation cannot be confirmed.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();

        $result = Letter::isOk($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Letter::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->user_id == current_user_id()) {
            $model->view_type = 1;
            $model->view_date = time();
            $model->save(false);
        }

        if (!(current_user_id() == $model->created_by || isRole('admin') || $model->user_id == current_user_id())) {
            $result = LetterView::createItem($model);
            if (is_array($result)) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDeleteFile($id) {
        $model = LetterFiles::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update(false);
            return $this->response(1, _e($this->controller_name . ' files succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionDelete($lang, $id)
    {
        $model = Letter::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status == 1) {
            return $this->response(0, _e('A sent message cannot be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
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
