<?php

namespace api\controllers;

use common\models\model\AttendReason;
use Yii;
use base\ResponseStatus;
use common\models\model\Faculty;

class AttendReasonController extends ApiActiveController
{
    public $modelClass = 'api\resources\AttendReason';

    public function actions()
    {
        return [];
    }

    public $table_name = 'attend_reason';
    public $controller_name = 'AttendReason';

    public function actionIndex($lang)
    {
        $model = new AttendReason();

        $query = $model->find()
            // ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
        ;

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
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
        // return strtotime('-1 month');
        $model = new AttendReason();
        $post = Yii::$app->request->post();
        unset($post['is_confirmed']);
        if (isset($post['start'])) {
            $post['start'] = date("Y-m-d H:i:s" , $post['start']);
        }
        if (isset($post['end'])) {
            $post['end'] = date("Y-m-d H:i:s" , $post['end']);
        }
        $this->load($model, $post);

        $result = AttendReason::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

        $model = AttendReason::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        unset($post['is_confirmed']);
        if (isset($post['start'])) {
            $post['start'] = date("Y-m-d H:i:s" , $post['start']);
        }
        if (isset($post['end'])) {
            $post['end'] = date("Y-m-d H:i:s" , $post['end']);
        }
        $this->load($model, $post);
        $result = AttendReason::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConfirm($lang, $id)
    {
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

        $model = AttendReason::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = AttendReason::confirmItem($model);
        if (!is_array($result)) {
            return $this->response(1, _e('Successfully confirmed.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCancellation($lang, $id)
    {
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

        $model = AttendReason::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = AttendReason::cancellationItem($model);
        if (!is_array($result)) {
            return $this->response(1, _e('Successfully confirmed.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = AttendReason::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = AttendReason::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            $result = AttendReason::deleteItem($model);
            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
