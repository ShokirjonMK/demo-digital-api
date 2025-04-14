<?php

namespace api\controllers;

use common\models\model\KpiStaff;
use Yii;
use base\ResponseStatus;
use common\models\model\Department;

class KpiStaffController extends ApiActiveController
{
    public $modelClass = 'api\resources\KpiStaff';

    public function actions()
    {
        return [];
    }

    public $table_name = 'kpi_staff';
    public $controller_name = 'KpiStaff';

    public function actionIndex($lang)
    {
        $model = new KpiStaff();

        $query = $model->find();


        /** */

        $t = $this->isSelfDep(Department::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query->where([
                'in', $this->table_name . '.id', $t['table_ids']
            ])
                ->andWhere([$this->table_name . '.user_access_type_id' => Department::USER_ACCESS_TYPE_ID]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'kpi_staff.is_deleted' => -1
            ]);
        }


        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionSelf($lang)
    {
        $model = new KpiStaff();

        $query = $model->find();


        $query->andWhere([$this->table_name . '.user_id' => current_user_id()]);


        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionSelfPost($id = null)
    {
        $post = Yii::$app->request->post();
        $model = $id ? KpiStaff::findOne(['id' => $id]) : new KpiStaff();

        // unset($post['upload_work_file']);
        // if (!$model) {
        //     return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        // }

        unset($post['upload_plan_file']);

        $result = $id ? KpiStaff::updateItemSelf($model, $post) : KpiStaff::createItemSelf($model, $post);

        if (is_array($result)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e($this->controller_name . 'successfully updated or created.'), $model, null, ResponseStatus::CREATED);
    }

    public function actionWorkFile($id)
    {
        $post = Yii::$app->request->post();
        $model = KpiStaff::findOne(['id' => $id]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // Check if 'upload_work_file' exists in the post data
        if (!isset($post['upload_work_file'])) {
            return $this->response(0, _e('No upload work file data provided.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $data = $post['upload_work_file'];
        $result = KpiStaff::updateItemSelf($model, $data);

        if (is_array($result)) {
            return $this->response(0, _e('There was an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::CREATED);
    }


    public function actionMonitoring($id = null)
    {
        $post = Yii::$app->request->post();
        $model = $id ? KpiStaff::findOne(['id' => $id]) : new KpiStaff();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = $id ? KpiStaff::updateItemMonitoring($model, $post) : KpiStaff::createItemMonitoring($model, $post);

        if (is_array($result)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e($this->controller_name . 'uccessfully updated or created.'), $model, null, ResponseStatus::CREATED);
    }

    public function actionComission($id = null)
    {
        $post = Yii::$app->request->post();
        $model = $id ? KpiStaff::findOne(['id' => $id]) : new KpiStaff();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = $id ? KpiStaff::updateItemComission($model, $post) : KpiStaff::createItemComission($model, $post);

        if (is_array($result)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e($this->controller_name . 'uccessfully updated or created.'), $model, null, ResponseStatus::CREATED);
    }

    public function actionRector($id = null)
    {
        $post = Yii::$app->request->post();
        $model = $id ? KpiStaff::findOne(['id' => $id]) : new KpiStaff();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = $id ? KpiStaff::updateItemRector($model, $post) : KpiStaff::createItemRector($model, $post);

        if (is_array($result)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e($this->controller_name . 'uccessfully updated or created.'), $model, null, ResponseStatus::CREATED);
    }

    public function actionDepLead($id = null)
    {
        $post = Yii::$app->request->post();
        $model = $id ? KpiStaff::findOne(['id' => $id]) : new KpiStaff();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = $id ? KpiStaff::updateItemDepLead($model, $post) : KpiStaff::createItemDepLead($model, $post);

        if (is_array($result)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e($this->controller_name . 'uccessfully updated or created.'), $model, null, ResponseStatus::CREATED);
    }


    public function actionCreate($lang)
    {
        $model = new KpiStaff();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = KpiStaff::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = KpiStaff::findOne(['id' => $id]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = KpiStaff::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = KpiStaff::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = KpiStaff::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
