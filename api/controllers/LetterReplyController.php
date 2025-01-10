<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\Document;
use common\models\model\DocumentFiles;
use common\models\model\EduSemestr;
use common\models\model\Letter;
use common\models\model\LetterFiles;
use common\models\model\LetterReply;
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

class LetterReplyController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'letter_reply';
    public $controller_name = 'LetterReply';

    public function actionIndex($lang)
    {
        $model = new LetterReply();

        $query = $model->find()->andWhere(['is_deleted' => 0]);

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
        $model = new LetterReply();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = LetterReply::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = LetterReply::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (!isRole('admin')) {
            if (!(isRole('doc_admin') || $model->letterForwardItem->user_id == current_user_id())) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        if ($model->is_ok > 0) {
            return $this->response(0, _e('The information is verified.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = LetterReply::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIsOk($lang, $id)
    {
        $model = LetterReply::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->status != 1) {
            return $this->response(0, _e('Information not sent.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();

        $result = LetterReply::isOk($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = LetterReply::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (!isRole('admin')) {
            if (!(isRole('doc_admin') || $model->created_by == current_user_id())) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        if (isRole('doc_admin') && $model->view_type == 0) {
            $model->view_type == 1;
            $model->view_date == time();
            $model->save(false);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = LetterReply::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->status == 1) {
            return $this->response(0, _e('Data cannot be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        // remove model
        if ($model->is_ok == 0) {
            $model->is_deleted = 1;
            $model->update(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
