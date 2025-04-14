<?php

namespace api\controllers;

use common\models\model\IntensiveApp;
use Yii;
use base\ResponseStatus;
use common\models\model\Department;
use common\models\model\Faculty;
use common\models\model\Profile;

class IntensiveController extends ApiActiveController
{
    public $modelClass = 'api\resources\IntensiveApp';

    public function actions()
    {
        return [];
    }

    public $table_name = 'intensive_app';
    public $controller_name = 'IntensiveApp';

    public function actionIndex($lang)
    {
        $model = new IntensiveApp();

        $query = $model->find()
            ->join('INNER JOIN', 'student', 'student.id = ' . $model->tableName() . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');

        /** */
        if (isRole('student')) {
            $query->andFilterWhere([$model->tableName() . '.student_id' => $this->student()]);
        }
        //else {
        //   $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     $query->where([
        //       'in', $this->table_name . '.id', $t['UserAccess']->table_id
        //   ])->all();
        //  } elseif ($t['status'] == 2) {
        //     $query->andFilterWhere([
        //        'intensive_app.is_deleted' => -1
        //    ]);
        // }
        //  }

        //  Filter from Profile 
        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // sqlraw($query);
        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new IntensiveApp();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = IntensiveApp::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = IntensiveApp::findOne(['id' => $id]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = IntensiveApp::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = IntensiveApp::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = IntensiveApp::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }


        // remove model
        if ($model) {
            if (!isRole('admin')) {
                if ($model->paymet_status == 1) {
                    return $this->response(0, _e('Payment approved. Impossible to delete'), null, null, ResponseStatus::BAD_REQUEST);
                }
                $model->is_deleted = 1;
                $model->save();
            } else {
                $model->delete();
            }
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
