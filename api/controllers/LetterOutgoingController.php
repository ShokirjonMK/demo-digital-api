<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\Document;
use common\models\model\DocumentFiles;
use common\models\model\EduSemestr;
use common\models\model\Letter;
use common\models\model\LetterFiles;
use common\models\model\LetterOutgoing;
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
use function GuzzleHttp\Promise\all;

class LetterOutgoingController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'letter_outgoing';
    public $controller_name = 'LetterOutgoing';

    public function actionIndex($lang)
    {
        $model = new LetterOutgoing();

        $letters = Letter::find()->where(['status' => 1, 'is_deleted' => 0])->all();
        $data = [];
        if (count($letters) > 0) {
            foreach ($letters as $letter) {
                $outgoing = LetterOutgoing::find()
                    ->where([
                        'letter_id' => $letter->id,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                    ->orderBy('id desc')
                    ->one();
                if ($outgoing) {
                    $data[] = $outgoing->id;
                }
            }
        }

        $query = $model->find()
            ->andWhere(['in' , 'id' , $data]);

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
        $model = new LetterOutgoing();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = LetterOutgoing::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = LetterOutgoing::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = LetterOutgoing::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionIsOk($lang, $id)
    {
        $model = LetterOutgoing::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
//        if ($model->is_ok == Letter::IS_OK_TRUE) {
//            return $this->response(0, _e('Confirmed information cannot be changed'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
//        }
        $post = Yii::$app->request->post();

        $result = LetterOutgoing::isOk($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = LetterOutgoing::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (current_user_id() == $model->user_id) {
            $model->view_type = 1;
            $model->view_date = time();
            $model->save(false);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = LetterOutgoing::find()
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
