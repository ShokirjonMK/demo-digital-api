<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\Document;
use common\models\model\DocumentFiles;
use common\models\model\EduSemestr;
use common\models\model\Room;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduYear;
use common\models\model\Para;
use common\models\model\Semestr;
use common\models\model\TimeTable1;
use common\models\model\Week;

class DocumentController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'document';
    public $controller_name = 'Document';

    public function actionIndex($lang)
    {
        $model = new Document();

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
        $model = new Document();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        if (isset($post['start'])) {
            $model->start_date = strtotime($post['start']);
        }
        if (isset($post['end'])) {
            $model->end_date = strtotime($post['end']);
        }

        $result = Document::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Document::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        if (isset($post['start'])) {
            $model->start_date = strtotime($post['start']);
        }
        if (isset($post['end'])) {
            $model->end_date = strtotime($post['end']);
        }
        $result = Document::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Document::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDeleteFile($id) {
        $model = DocumentFiles::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);

        if ($model) {
            $model->is_deleted = 1;
            $model->update();
            return $this->response(1, _e('Document files succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionDelete($lang, $id)
    {
        $model = Document::find()
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
