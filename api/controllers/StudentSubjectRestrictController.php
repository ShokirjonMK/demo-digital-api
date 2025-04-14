<?php

namespace api\controllers;

use common\models\model\StudentSubjectRestrict;
use Yii;
use base\ResponseStatus;
use common\models\model\Faculty;

class StudentSubjectRestrictController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentSubjectRestrict';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_subject_restrict';
    public $controller_name = 'StudentSubjectRestrict';

    public function actionIndex($lang)
    {
        $model = new StudentSubjectRestrict();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);


        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']->table_id
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
        }

        // /*  is Role check  */
        // if (isRole('tutor')) {
        //     $query = $query->andWhere([
        //         'tutor_id' => current_user_id()
        //     ]);
        // }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // rawsql($query);
        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndexSort($lang)
    {
        $model = new StudentSubjectRestrict();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);


        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']->table_id
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
        $model = new StudentSubjectRestrict();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = StudentSubjectRestrict::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentSubjectRestrict::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        // $this->load($model, $post);
        $result = StudentSubjectRestrict::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentSubjectRestrict::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentSubjectRestrict::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            if ($model->save(false))
                return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);

            return $this->response(0, _e($this->controller_name . ' failed.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
